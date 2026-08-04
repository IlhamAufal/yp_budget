<?php

namespace App\Controllers;

use App\Models\UserModel;

class LoginController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Halaman login
     */
    public function index()
    {
        // Jika sudah login, redirect ke dashboard
        if (session()->get('user_logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    /**
     * Proses login
     */
    public function process()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Validasi input
        if (empty($username) || empty($password)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username dan password harus diisi.');
        }

        // Cari user berdasarkan username
        $user = $this->userModel->getByUsername($username);

        if (! $user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username atau password salah.');
        }

        // Cek apakah user aktif
        if (! $this->userModel->isActive($user)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Akun tidak aktif. Hubungi administrator.');
        }

        // Cek apakah user diblokir
        if ($this->userModel->isBlocked($user)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Akun diblokir. Hubungi administrator.');
        }

        // Verifikasi password
        if (! $this->userModel->verifyPassword($password, $user['user_password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username atau password salah.');
        }

        // Set session data
        $sessionData = [
            'user_logged_in' => true,
            'user_id'        => $user['user_id'],
            'username'       => $user['user_username'],
            'user_name'      => $user['user_name'],
            'user_admin'     => $user['user_admin'],
            'user_email'     => $user['user_email'] ?? '',
        ];

        session()->set($sessionData);
        session()->regenerate();

        return redirect()->to('/dashboard');
    }

    /**
     * Logout
     */
    public function logout()
    {
        // Hapus semua session data
        session()->destroy();

        // Redirect ke halaman login
        return redirect()->to('/login');
    }
}
