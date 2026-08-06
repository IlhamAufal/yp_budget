<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\AuditLog;

class LoginController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Halaman Login
     */
    public function index()
    {
        // Jika sudah login, langsung lempar ke dashboard
        if (session()->get('user_logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    /**
     * Proses Login
     */
     public function process()
     {
         // 1. Ambil input username/email & password (dari form input field 'username' atau 'email')
         $login    = trim($this->request->getPost('username') ?? $this->request->getPost('email') ?? '');
         $password = $this->request->getPost('password') ?? '';
     
         // 2. Validasi Kelengkapan Input
         if (empty($login) || empty($password)) {
             return redirect()->back()
                 ->withInput()
                 ->with('error', 'Username / Email dan password wajib diisi.');
         }
     
         // 3. Cari User Berdasarkan Username atau Email (Tabel gw_sm__user)
         $user = $this->userModel->getByUsernameOrEmail($login);
     
         if (! $user) {
             return redirect()->back()
                 ->withInput()
                 ->with('error', 'Username / Email atau password salah.');
         }
     
         // 4. Cek Status Akun (Aktif / Tidak Aktif / Diblokir)
         if (! $this->userModel->isActive($user)) {
             return redirect()->back()
                 ->withInput()
                 ->with('error', 'Akun tidak aktif. Silakan hubungi Administrator.');
         }

         if ($this->userModel->isBlocked($user)) {
             return redirect()->back()
                 ->withInput()
                 ->with('error', 'Akun Anda diblokir. Silakan hubungi Administrator.');
         }
     
         // 5. Verifikasi Password (Password Hash modern & Fallback Legacy MD5/SHA1 + Salt)
         $storedPassword = $user['user_password'] ?? $user['password'] ?? '';
         $userSalt       = $user['user_salt'] ?? '';

         $isPasswordValid = false;

         if (! empty($storedPassword) && password_verify($password, $storedPassword)) {
             $isPasswordValid = true;
         } elseif (! empty($storedPassword)) {
             $lowerStored = strtolower($storedPassword);
             $md5Plain    = md5($password);
             $md5Salt1    = md5($password . $userSalt);
             $md5Salt2    = md5($userSalt . $password);
             $md5Salt3    = md5($userSalt . md5($password));
             $md5Salt4    = md5(md5($password) . $userSalt);

             $sha1Plain   = sha1($password);
             $sha1Salt1   = sha1($password . $userSalt);
             $sha1Salt2   = sha1($userSalt . $password);

             $validHashes = [
                 $md5Plain, $md5Salt1, $md5Salt2, $md5Salt3, $md5Salt4,
                 $sha1Plain, $sha1Salt1, $sha1Salt2
             ];

             if (in_array($lowerStored, $validHashes, true)) {
                 $isPasswordValid = true;
             }
         }

         if (! $isPasswordValid) {
             return redirect()->back()
                 ->withInput()
                 ->with('error', 'Username / Email atau password salah.');
         }
     
         // 6. Regenerate Session ID (Keamanan dari Session Fixation Attack)
         session()->regenerate();
     
         // 7. Simpan Session Data User
         $sessionData = [
             'user_logged_in' => true,
             'user_id'        => $user['user_id'] ?? $user['id'] ?? null,
             'user_username'  => $user['user_username'] ?? $user['username'] ?? '',
             'user_email'     => $user['user_email'] ?? $user['email'] ?? '',
             'user_name'      => $user['user_name'] ?? $user['name'] ?? $user['user_username'] ?? $user['user_email'] ?? '',
             'role_id'        => $user['role_id'] ?? null,
             'is_admin'       => ($user['user_admin'] ?? 'N') === 'Y',
         ];
     
         session()->set($sessionData);

         // 7b. Muat role dari tabel relasi gw_sm__profile (RBAC legacy — keputusan
         //     user 6 Agt 2026: skema legacy dipakai, bukan gw_sm__user_role).
         //     RoleFilter (Phase 1.3) akan memvalidasi hak akses URL dari sini.
         $userRoleModel = new \App\Models\UserRoleModel();
         $roleIds = $userRoleModel->getRoleIdsByUser((int) ($user['user_id'] ?? 0));
         session()->set('role_ids', $roleIds);
         if (! empty($roleIds)) {
             session()->set('role_id', $roleIds[0]);
         }

         // 7c. Muat RBAC object-level dari gw_sm__role_object (via profile) ke
         //     sesi 'auth_obj' — format sama dengan sesi legacy sehingga
         //     controller existing (mis. CapexController::getUserDeptList) bekerja.
         $authObj = $userRoleModel->getRoleObjectsByUser((int) ($user['user_id'] ?? 0));
         session()->set('auth_obj', $authObj);

         // 8. Audit trail + Redirect ke Dashboard
         AuditLog::log('LOGIN', 'login/process', "User '{$login}' berhasil login", (string) ($user['user_id'] ?? ''));

         return redirect()->to('/dashboard');
     }

    /**
     * Proses Logout
     */
    public function logout()
    {
        // Audit trail sebelum session dihapus
        AuditLog::log('LOGOUT', 'login/logout', "User '" . session()->get('user_username') . "' logout");

        // Opsional: Release lock akses concurrent jika ada di Library AccessRestrict
        // if (session()->has('user_id')) {
        //     service('accessRestrict')->release(session()->get('user_id'));
        // }

        // Hapus seluruh session
        session()->destroy();

        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar.');
    }
}