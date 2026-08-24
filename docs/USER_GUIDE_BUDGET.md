# YP Budget System — System & Maintenance Guide

> **Versi Dokumen:** 1.0  
> **Terakhir Diperbarui:** 19 Agustus 2026  
> **Framework:** CodeIgniter 4 (PHP 8.x)  
> **Database:** MySQL / MariaDB — `yp_budget_system`  
> **Environment:** XAMPP (Windows) / Apache + PHP + MySQL

---

## Daftar Isi

1. [Arsitektur Umum Sistem](#1-arsitektur-umum-sistem)
2. [Struktur Folder & Konvensi](#2-struktur-folder--konvensi)
3. [Konfigurasi & Environment](#3-konfigurasi--environment)
4. [Filter Pipeline (Middleware)](#4-filter-pipeline-middleware)
5. [Modul: Autentikasi & Login](#5-modul-autentikasi--login)
6. [Modul: RBAC (Role-Based Access Control)](#6-modul-rbac-role-based-access-control)
7. [Modul: System Administration](#7-modul-system-administration)
8. [Modul: Master Data](#8-modul-master-data)
9. [Modul: Period & Working Year](#9-modul-period--working-year)
10. [Modul: Sales (Domestic & Export)](#10-modul-sales-domestic--export)
11. [Modul: OPEX GA](#11-modul-opex-ga)
12. [Modul: OPEX Selling](#12-modul-opex-selling)
13. [Modul: FOH (Factory Overhead)](#13-modul-foh-factory-overhead)
14. [Modul: CAPEX](#14-modul-capex)
15. [Modul: MPP (Man Power Planning)](#15-modul-mpp-man-power-planning)
16. [Modul: Profit & Loss (P/L) Report](#16-modul-profit--loss-pl-report)
17. [Modul: Dashboard & Grand Report](#17-modul-dashboard--grand-report)
18. [Shared Libraries & Utilities](#18-shared-libraries--utilities)
19. [Database Schema Overview](#19-database-schema-overview)
20. [Panduan Deployment & Maintenance](#20-panduan-deployment--maintenance)

---

## 1. Arsitektur Umum Sistem

### 1.1 Tujuan Bisnis

YP Budget adalah sistem perencanaan anggaran tahunan (yearly plan budget) yang mencakup:

- **Sales Planning** — forecast revenue domestic & export
- **OPEX GA** — operational expenditure General & Administrative
- **OPEX Selling** — operational expenditure bagian penjualan
- **FOH** — Factory Overhead (biaya produksi tidak langsung)
- **CAPEX** — Capital Expenditure (investasi aset)
- **MPP** — Man Power Planning (perencanaan tenaga kerja & biaya gaji)
- **P/L Report** — konsolidasi Profit & Loss

### 1.2 Stack Teknologi

| Layer | Teknologi |
|-------|-----------|
| Backend | CodeIgniter 4, PHP 8.x |
| Database | MySQL 8 / MariaDB 10.x |
| Frontend | Blade-style PHP views, Tailwind CSS, Alpine.js |
| Excel Engine | PhpSpreadsheet (import/export) |
| Auth | Session-based, RBAC custom |
| Server | Apache (XAMPP / Linux) |

### 1.3 Diagram Arsitektur High-Level

```
┌─────────────────────────────────────────────────────────────────┐
│                        BROWSER (Client)                          │
└───────────────────────────────┬─────────────────────────────────┘
                                │ HTTP Request
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                    FILTER PIPELINE (Before)                       │
│  EnsureContext → AuthorizationContext → RoleFilter → PeriodLock  │
└───────────────────────────────┬─────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                         CONTROLLER LAYER                          │
│  LoginController │ SalesController │ OpexGaController │ ...      │
└───────────────────────────────┬─────────────────────────────────┘
                                │
                ┌───────────────┼───────────────┐
                ▼               ▼               ▼
┌───────────────────┐ ┌─────────────────┐ ┌──────────────────┐
│   MODEL LAYER     │ │   LIBRARIES     │ │    SERVICES      │
│ OpexGaModel       │ │ ExcelExporter   │ │ OpexReportService│
│ SalesModel        │ │ ExcelImporter   │ └──────────────────┘
│ FohModel  ...     │ │ AccessRestrict  │
└─────────┬─────────┘ │ AuditLog        │
          │            │ AuthService     │
          ▼            │ MenuBuilder     │
┌─────────────────┐   └─────────────────┘
│    DATABASE      │
│ yp_budget_system │
│   (MySQL)        │
└─────────────────┘
```

---

## 2. Struktur Folder & Konvensi

```
yp_budget_new/
├── app/
│   ├── Config/          # Konfigurasi (Routes, Filters, Database, Authorization)
│   ├── Controllers/     # 16 controller (satu per modul/fitur)
│   ├── Database/
│   │   ├── Migrations/  # 10 migration file (schema + data)
│   │   └── Seeds/       # Seeder untuk user & RBAC awal
│   ├── Exceptions/      # Custom exception (AuthorizationException)
│   ├── Filters/         # 5 HTTP filter (middleware)
│   ├── Helpers/         # Helper functions (menu_helper.php)
│   ├── Libraries/       # 9 shared library (core business logic)
│   ├── Models/          # 22 model file (data access layer)
│   ├── Services/        # Service layer (OpexReportService)
│   └── Views/           # Blade-style PHP views per modul
├── public/              # Document root (index.php, assets)
├── writable/            # Cache, logs, sessions
├── .env                 # Environment variables
├── composer.json        # PHP dependencies
└── yp_budget_system.sql # Legacy database dump (sumber schema)
```

### Konvensi Penamaan

| Elemen | Konvensi | Contoh |
|--------|----------|--------|
| Controller | PascalCase + `Controller` suffix | `OpexGaController` |
| Model | PascalCase + `Model` suffix | `OpexGaModel` |
| Library | PascalCase | `ExcelExporter`, `AccessRestrict` |
| Route | kebab-case (primary) + underscore alias (legacy) | `opex-ga/entry`, `opex_ga/entry` |
| Tabel DB | snake_case dengan prefix namespace | `yp_plan__trans_*`, `gw_sm__*`, `gw_plan__*` |
| View | snake_case, grouped per folder modul | `opex_ga/entry_budget.php` |

---

## 3. Konfigurasi & Environment

### 3.1 Database (`app/Config/Database.php`)

```php
'hostname' => 'localhost',
'username' => 'root',
'password' => '',
'database' => 'yp_budget_system',
'DBDriver' => 'MySQLi',
'port'     => 3306,
```

### 3.2 Environment Variables (`.env`)

| Key | Deskripsi |
|-----|-----------|
| `CI_ENVIRONMENT` | `development` / `production` |
| `database.default.hostname` | Host database |
| `database.default.database` | Nama database |
| `database.default.username` | Username DB |
| `database.default.password` | Password DB |
| `MASTER_LOGIN_HASH` | Hash bcrypt untuk master password (fallback login) |

### 3.3 Routing (`app/Config/Routes.php`)

- **AutoRoute dimatikan** (`setAutoRoute(false)`) — semua route didefinisikan eksplisit.
- Setiap route dilindungi filter `auth` kecuali halaman login dan `api/active-years`.
- Legacy URL (underscore) di-alias ke kebab-case controller method yang sama.

---

## 4. Filter Pipeline (Middleware)

Filter dieksekusi secara global (before) untuk setiap request dalam urutan berikut:

```
Request → EnsureContextFilter → AuthorizationContextFilter → RoleFilter → PeriodLockFilter → Controller
```

### 4.1 EnsureContextFilter

| Aspek | Detail |
|-------|--------|
| **File** | `app/Filters/EnsureContextFilter.php` |
| **Fungsi** | Concurrent access locking (PRD standar 1.7) — mencegah dua user mengedit cost center yang sama |
| **Scope** | Hanya form entry: `foh/entry`, `mpp/entry`, `capex/entry`, `opex-ga/entry`, `opex-selling/entry` |
| **Mekanisme** | Tabel `gw_sm__access_restrict`, lock timeout 15 menit |
| **Bypass** | Halaman report/summary, dashboard, login, set-year |

### 4.2 AuthorizationContextFilter

| Aspek | Detail |
|-------|--------|
| **File** | `app/Filters/AuthorizationContextFilter.php` |
| **Fungsi** | Refresh konteks RBAC dari database sebelum RoleFilter mengevaluasi request |
| **Perilaku gagal** | Session di-destroy, redirect ke login |

### 4.3 RoleFilter

| Aspek | Detail |
|-------|--------|
| **File** | `app/Filters/RoleFilter.php` |
| **Fungsi** | Enforcement RBAC — memvalidasi hak akses URL berdasarkan menu permission |
| **Lookup** | Match `gw_sm__menu.menu_link` dengan URI aktif |
| **Admin bypass** | User dengan `user_admin = 'Y'` selalu diizinkan |
| **Public routes** | `GET login`, `POST login/process`, `GET logout`, `GET api/active-years` |

### 4.4 PeriodLockFilter

| Aspek | Detail |
|-------|--------|
| **File** | `app/Filters/PeriodLockFilter.php` |
| **Fungsi** | Mengunci form entry di luar periode aktif yang dikonfigurasi admin |
| **Tabel** | `yp_plan__master_period` (kolom: `tipe`, `begda`, `endda`, `status`) |
| **Modul terdaftar** | OPEX_GA, OPEX_SELLING, FOH, CAPEX, MPP |
| **Fail-open** | Jika modul belum dikonfigurasi periode (0 baris) → izinkan akses |
| **Halaman report** | Tidak dikunci (regex `/report|summary/`) |

### 4.5 AuthFilter

| Aspek | Detail |
|-------|--------|
| **File** | `app/Filters/AuthFilter.php` |
| **Fungsi** | Cek session login (`user_logged_in`) |
| **AJAX** | Return 401 JSON |
| **Non-AJAX** | Redirect ke `/login` |

---

## 5. Modul: Autentikasi & Login

### Nama & Tujuan

Mengelola proses login, logout, dan change password. Mendukung password modern (bcrypt) dan legacy (MD5/SHA1 + salt).

### Data Source & Skema

| Tabel | Peran |
|-------|-------|
| `gw_sm__user` | Master user (user_id, user_username, user_email, user_password, user_salt, user_admin, user_active) |
| `gw_sm__profile` | Relasi user → role (profile_user_id, profile_role_id) |
| `gw_sm__logs` | Audit trail login/logout |

### Business Logic & Workflow

```
┌──────────┐     ┌──────────────────┐     ┌─────────────────────┐
│  Form    │────▶│ LoginController  │────▶│ Verifikasi Password │
│  Login   │     │  ::process()     │     │ (bcrypt > master >  │
└──────────┘     └──────────────────┘     │  MD5/SHA1 legacy)   │
                                          └──────────┬──────────┘
                                                     │ Valid
                                                     ▼
                                          ┌─────────────────────┐
                                          │ session()->regenerate│
                                          │ Set session data     │
                                          │ AuthorizationService │
                                          │   ::refresh()        │
                                          └──────────┬──────────┘
                                                     │
                                                     ▼
                                          ┌─────────────────────┐
                                          │ Redirect /dashboard  │
                                          └─────────────────────┘
```

**Urutan verifikasi password:**
1. `password_verify()` (bcrypt modern)
2. `password_verify()` terhadap `MASTER_LOGIN_HASH` dari `.env`
3. MD5 variasi: plain, `password+salt`, `salt+password`, `salt+md5(password)`, `md5(password)+salt`
4. SHA1 variasi: plain, `password+salt`, `salt+password`

### Integrasi & Ketergantungan

- **AuthorizationService** — dimuat setelah login sukses untuk membangun konteks RBAC
- **AuditLog** — mencatat event LOGIN/LOGOUT
- **Session** — CI4 native session (file/database driver)

### Maintenance & Watchouts

- **MASTER_LOGIN_HASH** di `.env` adalah backdoor — **jangan** dipakai di production tanpa monitoring.
- Legacy password (MD5/SHA1) tidak di-upgrade otomatis ke bcrypt setelah login berhasil. Pertimbangkan menambahkan auto-rehash.
- `session()->regenerate()` dipanggil saat login untuk mencegah session fixation.
- Jika `AuthorizationService::refresh()` gagal, user tidak bisa login (session di-destroy).

---

## 6. Modul: RBAC (Role-Based Access Control)

### Nama & Tujuan

Sistem otorisasi berbasis role dan menu. Mengontrol halaman apa saja yang bisa diakses user berdasarkan role yang di-assign. Mendukung override per-user (sparse delta model).

### Data Source & Skema

```
┌─────────────┐     ┌──────────────────┐     ┌───────────────┐
│ gw_sm__user │────▶│ gw_sm__profile   │────▶│ gw_sm__role   │
│ (user_id)   │ 1:N │ (user→role)      │ N:1 │ (role_id)     │
└─────────────┘     └──────────────────┘     └───────┬───────┘
                                                     │ 1:N
                                                     ▼
                                             ┌───────────────┐
                                             │gw_sm__rolemenu │
                                             │(role→menu)     │
                                             └───────┬───────┘
                                                     │ N:1
                                                     ▼
                                             ┌───────────────┐
                                             │ gw_sm__menu   │
                                             │ (menu_id,     │
                                             │  menu_link)   │
                                             └───────────────┘
```

**Override per-user:**

| Tabel | Fungsi |
|-------|--------|
| `gw_sm__usermenu` | Sparse delta: `Y` = tambah menu di luar baseline role, `N` = cabut menu dari baseline |

### Business Logic & Workflow

1. User login → `AuthorizationService::refresh()` dipanggil
2. Ambil role utama (tipe `menu`) dari `gw_sm__profile`
3. Bangun **baseline** menu_ids dari `gw_sm__rolemenu`
4. Terapkan **override** dari `gw_sm__usermenu` (Y = add, N = remove)
5. Hasil = **effective_menu_ids** disimpan di session
6. Setiap request, `RoleFilter` mencocokkan URI dengan `gw_sm__menu.menu_link`
7. Jika menu_id ditemukan dan ada di effective_menu_ids → diizinkan

**Tipe Role:**
- `menu` — mengontrol akses menu/halaman
- `object` — mengontrol scope data (cost center mana yang bisa dilihat)

### Integrasi & Ketergantungan

- **AuthorizationService** (`app/Libraries/AuthorizationService.php`) — core logic
- **Config/Authorization.php** — policy mapping (URL aliases → canonical menu_link)
- **MenuBuilder** — render sidebar berdasarkan effective_menu_ids

### Maintenance & Watchouts

- **Fail-open mode**: User tanpa role tetap bisa akses semua modul (lihat warning di Dashboard). Segera assign role.
- Override sparse model: Jika role baseline berubah, user tanpa override otomatis mewarisi perubahan. User dengan override tetap mempertahankan delta-nya.
- Tabel `gw_sm__profile` menggunakan kolom `profile_role_id` (bukan `role_id`).
- Admin (`user_admin = 'Y'`) melewati semua RBAC check — tidak perlu assign menu.

---

## 7. Modul: System Administration

### Nama & Tujuan

CRUD manajemen Menu, Role, dan User. Diakses melalui route `/sys-admin/*`.

### 7.1 Menu Configuration

| Aspek | Detail |
|-------|--------|
| **Controller** | `MenuController` |
| **Model** | `MenuModel` |
| **Tabel** | `gw_sm__menu`, `gw_sm__menu_structure` |
| **Route** | `GET sys-admin/menu`, `POST sys-admin/api/menu/save\|toggle\|delete` |

**Kolom penting `gw_sm__menu`:**

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `menu_id` | INT PK | ID unik |
| `menu_name_idn` | VARCHAR(400) | Nama menu Bahasa Indonesia |
| `menu_link` | VARCHAR(100) | URL route (digunakan RoleFilter untuk matching) |
| `menu_group` | VARCHAR(50) | Grup sidebar (mis. "BUDGET PLANNING") |
| `menu_parrent` | ENUM Y/N | Apakah menu ini parent node |
| `menu_active` | ENUM Y/N | Status aktif |
| `menu_order` | VARCHAR(4) | Urutan tampil di sidebar |
| `menu_module_code` | VARCHAR(4) | Kode modul (untuk filter per module) |

### 7.2 Role Management

| Aspek | Detail |
|-------|--------|
| **Controller** | `RoleController` |
| **Model** | `RoleModel` |
| **Tabel** | `gw_sm__role`, `gw_sm__rolemenu` |
| **Route** | `GET sys-admin/role`, `POST sys-admin/api/role/save\|toggle\|delete\|menus\|menus/save` |

**Workflow permission assignment:**
1. Admin membuat/edit role → `RoleController::save()`
2. Admin klik "Set Permission" → `RoleController::menus()` (ambil daftar menu_id)
3. Admin centang menu → `RoleController::saveMenus()` (replace mode: hapus semua → insert baru)

### 7.3 User Management

| Aspek | Detail |
|-------|--------|
| **Controller** | `UserController` |
| **Model** | `UserModel`, `UserRoleModel`, `UserMenuModel` |
| **Tabel** | `gw_sm__user`, `gw_sm__profile`, `gw_sm__usermenu` |
| **Route** | `GET sys-admin/user`, `POST sys-admin/api/user/save\|toggle\|delete\|reset-password\|roles` |

**Alur simpan user + role + custom menu:**
```
UserController::save()
  ├─ UserModel::saveUser()           → INSERT/UPDATE gw_sm__user
  ├─ UserRoleModel::saveUserRoles()  → REPLACE gw_sm__profile
  └─ UserMenuModel::saveConfiguration() → Sparse delta ke gw_sm__usermenu
     (Seluruh operasi dibungkus DB Transaction)
```

### Maintenance & Watchouts

- `gw_sm__menu.menu_link` HARUS cocok dengan route yang terdaftar di `Routes.php`. Jika tidak, RoleFilter akan menolak akses.
- Saat menambah route baru, **wajib** tambahkan entry di `gw_sm__menu` + assign ke role yang relevan.
- Password reset menggunakan `password_hash(PASSWORD_DEFAULT)` — tidak ada constraint minimum.

---

## 8. Modul: Master Data

### Nama & Tujuan

Mengelola data referensi yang digunakan oleh seluruh modul transaksi: Chart of Account (COA), Cost Center, Departemen, Produk, Salary MPP, dan Configure Period.

### Data Source & Skema

| Sub-Modul | Tabel | Kolom Kunci |
|-----------|-------|-------------|
| COA | `gw_plan__master_coa` | id_cost_center, main_account, id_acct_ext, cost_center_header, cost_center_sub, cost_center_desc, year, type, category, status |
| Cost Center | `gw_plan__master_cost_center` | id_cost_center, cost_center, cost_center_sap, cost_desc, location, year, type, status |
| Departemen | `gw_plan__master_department` | id_dept, dept_code, dept_desc, status |
| Produk | `gw_plan__master_product` | id_product, product_name, id_channel, key_product, mid_product, db, pcs, gr, year, status |
| Salary MPP | (tabel salary custom) | id, desc, type, dept_id, salary, year_code, status |
| Period | `yp_plan__master_year` | year_code (PK), period_start, period_end, status, locked |

### Controller & Route

| Route | Method | Fungsi |
|-------|--------|--------|
| `GET master/coa` | `MasterController::coa()` | Halaman list COA + filter |
| `GET master/coa/export` | `::coaExport()` | CSV export |
| `POST master/api/coa/save` | `::coaSave()` | AJAX create/update |
| `POST master/api/coa/toggle` | `::coaToggle()` | AJAX aktif/nonaktif |
| `POST master/api/coa/copy-year` | `::coaCopyYear()` | Salin COA ke tahun baru |
| `GET master/cost-center` | `::costCenter()` | List cost center |
| `GET master/department` | `::department()` | List departemen |
| `GET master/product` | `::product()` | List produk |
| `GET master/salary-mpp` | `::salaryMpp()` | List salary MPP |
| `GET master/configure-period` | `::configurePeriod()` | Konfigurasi tahun anggaran |

### Business Logic

- **Pagination server-side** — semua halaman master menggunakan `limit`/`offset`.
- **Copy Year COA** — menyalin seluruh record COA dari tahun sumber ke tahun tujuan (berguna saat memulai budget tahun baru).
- **Toggle Status** — soft-delete (ubah status A→D, D→A) bukan hard delete.
- **CSV/Excel Export** — menggunakan `CsvExporter` (bukan ExcelExporter) untuk ringan.

### Relasi Antar Tabel

```
gw_plan__master_coa ──────────┐
  (year, cost_center, type)   │
                              ├──▶ Dipakai oleh OPEX/FOH/CAPEX sebagai
gw_plan__master_cost_center ──┤     referensi akun & cost center
  (year, cost_center_sap)     │
                              │
gw_plan__master_department ───┘──▶ Dipakai MPP, OPEX sebagai referensi dept
                              
gw_plan__master_product ─────────▶ Dipakai Sales sebagai referensi produk
```

### Maintenance & Watchouts

- **`cost_center_sap`** adalah identifier yang digunakan di UI dropdown. Jika NULL, fallback ke `cost_center`.
- COA memiliki field `type` (OPEX/FOH/SELLING/CAPEX) — salah assign tipe menyebabkan data tidak muncul di modul yang sesuai.
- Perubahan master data di tengah tahun berjalan **tidak retroaktif** — transaksi yang sudah tersimpan tetap merujuk data lama.
- Period `yp_plan__master_year` dengan `locked = 1` akan mencegah modifikasi data budget (diimplementasikan di controller/model level, bukan filter).

---

## 9. Modul: Period & Working Year

### Nama & Tujuan

Mengelola tahun anggaran aktif dan periode entry per modul. Setiap user memiliki "working year" yang tersimpan di session dan menentukan data tahun berapa yang sedang diedit.

### Data Source & Skema

| Tabel | Kolom | Keterangan |
|-------|-------|------------|
| `yp_plan__master_year` | year_code (PK), period_start, period_end, status (A/D), locked (0/1) | Daftar tahun anggaran |
| `yp_plan__master_period` | id, tipe, begda, endda, status | Periode entry per modul (OPEX_GA, OPEX_SELLING, FOH, MPP, CAPEX) |

### Business Logic & Workflow

**Set Working Year:**
```
POST /set-year (year_code) → PeriodController::setYear()
  └─ session()->set('year_code', $yearCode)
     └─ Seluruh controller membaca session('year_code') untuk query data
```

**Period Lock (otomatis via PeriodLockFilter):**
```
Request masuk → PeriodLockFilter::before()
  ├─ Cek URI termasuk modul transaksi? (opex-ga, opex-selling, foh, capex, mpp)
  ├─ Apakah halaman report/summary? → Skip (tidak dikunci)
  ├─ Query yp_plan__master_period WHERE tipe = X AND status = 'A'
  │   ├─ 0 baris konfigurasi → fail-open (izinkan)
  │   └─ Ada baris → cek begda <= NOW <= endda
  │       ├─ Dalam range → izinkan
  │       └─ Di luar range → blokir (redirect + flash error)
  └─ AJAX → HTTP 423 JSON
```

### Maintenance & Watchouts

- Jika admin lupa mengonfigurasi periode di `yp_plan__master_period`, modul **fail-open** (selalu bisa diakses).
- Session `year_code` hilang saat logout — user harus pilih ulang.
- Perubahan working year **langsung** mengubah data yang ditampilkan di semua halaman.

---

## 10. Modul: Sales (Domestic & Export)

### Nama & Tujuan

Modul perencanaan penjualan yang mencakup: forecast revenue domestic per channel/region, forecast export per country, discount reclass allocation, dan simulasi volume/revenue.

### Data Source & Skema

| Tabel | Fungsi |
|-------|--------|
| `yp_plan__trans_sales_domestic` | Transaksi sales domestic (per produk × bulan: qty & rev) |
| `yp_plan__trans_sales_domestic_region` | Breakdown domestic per region |
| `yp_plan__trans_sales_export` | Transaksi sales export |
| `yp_plan__trans_sales_export_country` | Breakdown export per country |
| `yp_plan__assump_sales` | Assumption sales (type_sales, value_text) |
| `yp_plan__assump_rate` | Kurs valuta asing (USD, Baht, Ringgit) |
| `yp_plan__master_reclass_monthly` | Alokasi discount reclass per bulan |
| `yp_plan__trans_delivery_customer` | Delivery expense & customer claim |
| `gw_plan__master_product` | Referensi produk |

**Struktur kolom transaksi sales (12 bulan):**
```
jan_qty, jan_rev, feb_qty, feb_rev, ..., dec_qty, dec_rev
```

### Controller & Route

| Route | Fungsi |
|-------|--------|
| `GET sales/` | Summary & Discount Reclass |
| `GET sales/domestic/entry` | Entry Sales Domestic |
| `GET sales/export/entry` | Entry Sales Export |
| `GET sales/simulation` | Simulasi Revenue |
| `POST sales/saveDiscountReclass` | Simpan alokasi diskon |
| `POST sales/processUpload` | Upload Excel domestic/export |
| `GET sales/exportExcel` | Export assumption ke Excel |
| `POST sales/proses_summary_domestic` | Re-aggregate regional → summary |
| `POST sales/cari_domestic_sales` | AJAX search budget domestic |

### Business Logic & Workflow

```
┌─────────────────────────────────────────────────────────────┐
│                   SALES ENTRY FLOW                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  1. Upload Excel (domestic/export)                           │
│     └─ ExcelImporter::import() → INSERT yp_plan__assump_*   │
│                                                              │
│  2. Entry Manual per Channel/Produk                          │
│     └─ AJAX POST → INSERT/UPDATE trans_sales_*              │
│                                                              │
│  3. Proses Summary Domestic                                  │
│     └─ Aggregate trans_sales_domestic_region                 │
│        GROUP BY id_inv → INSERT trans_sales_domestic         │
│                                                              │
│  4. Discount Reclass Allocation                              │
│     └─ Simpan alokasi bulanan (Jan-Dec) ke                  │
│        yp_plan__master_reclass_monthly                       │
│                                                              │
│  5. Output: Summary (Domestic + Export + Total)              │
│     └─ Aggregasi SUM per bulan dari kedua tabel trans       │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Integrasi & Ketergantungan

- **ExcelImporter** — parsing upload file `.xlsx`/`.xls`
- **ExcelExporter** — export template & data
- **ChannelModel** — referensi channel domestic (GT, MT, OEM, dll)
- **AssumptionModel** — asumsi kurs dan volume
- **AuditLog** — pencatatan aktivitas upload/save/export

### Maintenance & Watchouts

- `prosesSummaryDomestic()` melakukan **DELETE + INSERT** — jika gagal di tengah, data summary bisa kosong. Gunakan transaction.
- Kolom `dec` di MySQL adalah reserved word — di-escape sebagai `` `dec` `` dalam query.
- Upload sales tidak melakukan validasi mapping kolom — jika format Excel berubah, data bisa salah masuk.
- Kurs dari `yp_plan__assump_rate` harus diisi terlebih dahulu sebelum sales export bisa dihitung dalam IDR.

---

## 11. Modul: OPEX GA

### Nama & Tujuan

Modul entry dan monitoring budget Operational Expenditure bagian General & Administrative. Mencakup budget per cost center × COA, breakdown detail item, actual data upload, dan laporan departemen.

### Data Source & Skema

| Tabel | Fungsi |
|-------|--------|
| `yp_plan__trans_budget_entry_data` | Data budget utama (id_coa, id_dept, bulan 1-12, total, source, submit_status) |
| `yp_plan__trans_budget_entry_detail` | Breakdown item per entry_data (nama_barang, bulan 1-12) |
| `gw_plan__master_coa` | Referensi COA (WHERE type berkaitan dengan OPEX) |
| `gw_plan__master_cost_center` | Referensi Cost Center |

### Controller & Route

| Route | Method | Fungsi |
|-------|--------|--------|
| `GET opex-ga/` | `index()` | Summary OPEX GA |
| `GET opex-ga/entry` | `entryBudget()` | Form entry budget |
| `GET opex-ga/entry-budget-detail` | `entryBudgetDetail()` | Detail breakdown |
| `GET opex-ga/getEntryData` | AJAX | Data budget per CC |
| `GET opex-ga/getHeaderAccounts` | AJAX | Header COA accounts |
| `GET opex-ga/getDetailMatrix` | AJAX | Matrix budget + actual |
| `POST opex-ga/saveBudget` | AJAX | Simpan budget |
| `POST opex-ga/saveDetailItems` | AJAX | Simpan breakdown items |
| `POST opex-ga/submitBudget` | AJAX | Submit workflow |
| `GET opex-ga/report-department` | `reportDepartment()` | Laporan per CC |
| `GET opex-ga/actual-budget` | `actualBudget()` | Halaman actual data |
| `POST opex-ga/uploadActual` | Upload Excel | Upload actual |
| `GET opex-ga/download-template` | Download | Template Excel |

### Business Logic & Workflow

```
┌────────────────────────────────────────────────────────────────────┐
│                    OPEX GA ENTRY FLOW                                │
├────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  1. User pilih Cost Center dari dropdown                            │
│     └─ AJAX getHeaderAccounts → list COA headers                   │
│                                                                     │
│  2. User klik header → AJAX getDetailMatrix                        │
│     └─ Tampilkan matrix: sub-account × 12 bulan (budget + actual)  │
│                                                                     │
│  3. User isi angka budget per bulan per sub-account                 │
│     └─ POST saveBudget (rows: [{id_coa, jan..dec}])                │
│         ├─ AccessRestrict::checkLock() — concurrent protection     │
│         └─ Model::saveBudget() → UPSERT trans_budget_entry_data    │
│                                                                     │
│  4. User bisa tambah breakdown detail per row                       │
│     └─ POST saveDetailItems → INSERT trans_budget_entry_detail     │
│                                                                     │
│  5. Submit Budget                                                   │
│     └─ Model::submitBudget() → UPDATE submit_status = 'SUBMITTED' │
│                                                                     │
│  6. Upload Actual                                                   │
│     └─ ExcelImporter → Model::saveActualFromImport()               │
│                                                                     │
└────────────────────────────────────────────────────────────────────┘
```

**Submit Workflow States:**
```
DRAFT → SUBMITTED → APPROVED
```

### Integrasi & Ketergantungan

- **AccessRestrict** — concurrent lock sebelum save (PRD 1.7)
- **ExcelImporter / ExcelExporter** — upload actual & download template
- **OpexReportService** — normalisasi data untuk laporan
- **AuditLog** — trace setiap save/submit/upload
- **Kolom `source`** — menandai baris dari MPP sync (`'MPP'`) atau CAPEX sync (`'CAPEX'`) agar tidak ditimpa entry manual

### Maintenance & Watchouts

- **Object-level access**: `resolveEligibleDept()` memeriksa apakah user boleh akses cost center tersebut berdasarkan `auth_obj` (role tipe `object`).
- Kolom `source` NULL/empty = entry manual. Jangan query tanpa filter source jika ingin data manual saja.
- Route alias legacy: `opexga/*`, `opex_ga/*` → semua mengarah ke OpexGaController yang sama.
- Template download menggunakan data actual existing sebagai basis — jika belum ada data, generate dari master COA.

---

## 12. Modul: OPEX Selling

### Nama & Tujuan

Modul entry budget biaya penjualan (selling expense). Strukturnya mirip OPEX GA tetapi dengan cost center khusus selling (kode 600 = Domestic, 700 = Export).

### Data Source & Skema

| Tabel | Fungsi |
|-------|--------|
| `yp_plan__trans_budget_entry_data` | Budget utama (source = 'SELLING') |
| `yp_plan__trans_budget_entry_detail` | Breakdown detail item |
| `gw_plan__master_coa` | COA tipe SELLING |

### Controller & Route

| Route | Fungsi |
|-------|--------|
| `GET opex-selling/` | Entry Budget |
| `GET opex-selling/entry-budget-detail` | Detail breakdown |
| `GET opex-selling/actual` | Actual data manager |
| `GET opex-selling/report-department` | Report per CC |
| `POST opex-selling/saveBudget` | Simpan budget (per main_account[]) |
| `POST opex-selling/saveEntryDetail` | Simpan detail via form POST |
| `POST opex-selling/getActualData` | AJAX actual (Domestic/Export) |
| `POST opex-selling/cariActualTable` | Flat view data (legacy compat) |

### Business Logic

**Perbedaan dengan OPEX GA:**
- Simpan budget menggunakan array `main_account[]` + `toti_aa[]` (bukan JSON rows)
- Cost center Selling hanya 2 pilihan: `600` (Domestic) dan `700` (Export)
- `getActualData()` load-all tanpa pagination (search/filter di client-side)
- Kolom `source = 'SELLING'` di tabel `yp_plan__trans_budget_entry_data`

### Maintenance & Watchouts

- `DbCompat::hasEntrySource()` mengecek apakah kolom `source` ada di tabel. Jika migrasi belum jalan, fallback tanpa filter source.
- Budget OPEX Selling dan OPEX GA **share tabel yang sama** (`trans_budget_entry_data`). Dibedakan oleh kolom `source`.
- `saveEntryDetail()` menggunakan form POST biasa (redirect back) — bukan AJAX. Ini berbeda dari `saveDetailItems()` yang AJAX.

---

## 13. Modul: FOH (Factory Overhead)

### Nama & Tujuan

Modul entry budget biaya pabrik tidak langsung (Factory Overhead). Memiliki fitur workflow approval (submit → approve/reject) yang tidak ada di OPEX GA/Selling.

### Data Source & Skema

| Tabel | Fungsi |
|-------|--------|
| `yp_plan__trans_budget_entry_data` | Budget FOH (submit_status, submit_by, submit_date) |
| `yp_plan__trans_budget_entry_detail` | Breakdown detail |
| `yp_plan__master_period` | Periode submit FOH |

### Controller & Route

| Route | Fungsi |
|-------|--------|
| `GET foh/entry` | Form entry budget |
| `GET foh/entry-budget-detail` | Detail item |
| `POST foh/saveBudget` | Simpan budget |
| `POST foh/saveDetailItems` | Simpan breakdown |
| `POST foh/submit` | Submit untuk approval |
| `POST foh/approve` | Approve budget |
| `POST foh/reject` | Reject budget (+ catatan) |
| `GET foh/getStatus` | Status workflow per dept |
| `GET foh/actual` | Halaman actual |
| `POST foh/uploadActual` | Upload Excel actual |
| `GET foh/summary` | Summary FOH |
| `POST foh/summaryCostCenter` | Summary per CC |

### Business Logic — Workflow Approval

```
┌────────┐    submit()    ┌────────────┐    approve()    ┌──────────┐
│ DRAFT  │───────────────▶│ SUBMITTED  │────────────────▶│ APPROVED │
└────────┘                └─────┬──────┘                 └──────────┘
                                │
                          reject(note)
                                │
                                ▼
                          ┌────────┐
                          │ DRAFT  │ (dikembalikan untuk revisi)
                          └────────┘
```

### Integrasi & Ketergantungan

- **AccessRestrict** — concurrent lock
- **ExcelImporter** — upload actual dari Excel
- **ExcelExporter** — download template & export
- **AuditLog** — trace submit/approve/reject

### Maintenance & Watchouts

- `getConfigPeriod('FOH')` membaca periode submit dari `yp_plan__master_period` — jika tidak dikonfigurasi, submit tetap bisa dilakukan.
- Summary FOH memiliki 2 mode: per cost center dan per account — dipanggil via AJAX POST.
- Approve/Reject membutuhkan validasi user level (tidak semua user bisa approve) — diimplementasikan di FohModel.

---

## 14. Modul: CAPEX

### Nama & Tujuan

Modul Capital Expenditure — entry proposal belanja aset (per kategori), summary acquisition & depreciation, dan sinkronisasi depresiasi ke OPEX Engine.

### Data Source & Skema

| Tabel | Fungsi |
|-------|--------|
| `yp_plan__trans_capex` (atau tabel legacy serupa) | Transaksi CAPEX (description, account, cost_center, qty, unit_price, bulan 1-12) |
| `gw_plan__master_cost_center` | Referensi CC |

### Controller & Route

| Route | Fungsi |
|-------|--------|
| `GET capex/entry` | Form entry CAPEX |
| `GET capex/summary` | Summary page |
| `POST capex/saveFormCapex` | Simpan proposal per kategori aset |
| `POST capex/save_capex` | Legacy save |
| `POST capex/sync_to_opex` | Push depresiasi ke OPEX |
| `GET capex/getSummaryViewAll` | AJAX: semua data CAPEX |
| `GET capex/getSummaryAcquisition` | AJAX: ringkasan per kategori (akuisisi) |
| `GET capex/getSummaryDepreciation` | AJAX: ringkasan per kategori (depresiasi) |
| `GET capex/exportSummaryExcel` | Export summary ke Excel |

### Business Logic & Workflow

```
┌───────────────┐     ┌───────────────┐     ┌─────────────────────┐
│ Entry Form    │────▶│ saveFormCapex  │────▶│ trans_capex INSERT   │
│ (per kategori │     │ (per CC/acct) │     │ → 12 bulan data     │
│  aset)        │     └───────────────┘     └─────────────────────┘
└───────────────┘                                      │
                                                       ▼
                                            ┌─────────────────────┐
                                            │ sync_to_opex        │
                                            │ → Hitung depresiasi │
                                            │ → INSERT ke         │
                                            │   trans_budget_entry │
                                            │   (source='CAPEX')  │
                                            └─────────────────────┘
```

**Sync to OPEX:**
- Menghitung nilai depresiasi bulanan dari proposal CAPEX
- Memasukkan baris ke `yp_plan__trans_budget_entry_data` dengan `source = 'CAPEX'`
- Baris CAPEX hanya menimpa baris dengan source='CAPEX' sebelumnya (bukan manual entry)

### Maintenance & Watchouts

- **AccessRestrict** diterapkan di `saveCapex()` dan `saveFormCapex()` — concurrent lock.
- Summary memiliki 3 tab: View All, Acquisition, Depreciation — masing-masing endpoint AJAX berbeda.
- Pagination di entry page (server-side, 10 per page).
- Route alias `capex-summary/*` mengarah ke endpoint summary yang sama.

---

## 15. Modul: MPP (Man Power Planning)

### Nama & Tujuan

Modul perencanaan tenaga kerja — entry headcount per posisi per cost center, breakdown per kategori (tipe MPP), dan sinkronisasi biaya gaji ke OPEX Engine.

### Data Source & Skema

| Tabel | Fungsi |
|-------|--------|
| `yp_plan__trans_mpp` (atau serupa) | Transaksi MPP (id_dept, tipe_id, posisi, m1-m12) |
| `gw_plan__master_department` | Referensi departemen |
| `gw_plan__master_cost_center` | Referensi CC |
| Salary master | Rate gaji per posisi/tipe |

### Controller & Route

| Route | Fungsi |
|-------|--------|
| `GET mpp/` atau `GET mpp/entry` | Form entry MPP |
| `GET mpp/summary` | Summary headcount |
| `GET mpp/getCostCenters` | AJAX: CC aktif |
| `GET mpp/getMppMatrix` | AJAX: matrix per kategori |
| `GET mpp/getMppBreakdown` | AJAX: detail per posisi |
| `POST mpp/saveMppBreakdown` | Simpan multi-posisi |
| `POST mpp/deleteMppPosition` | Hapus satu posisi |
| `GET mpp/getViewData` | Data ringkasan view |
| `POST mpp/syncToOpex` | Alokasi gaji ke OPEX |

### Business Logic & Workflow

```
┌───────────────────────────────────────────────────────────────┐
│                      MPP ENTRY FLOW                            │
├───────────────────────────────────────────────────────────────┤
│                                                                │
│  1. Pilih Cost Center / Department                             │
│     └─ AJAX getMppMatrix → tampilkan kategori (tipe_mpp)      │
│                                                                │
│  2. Klik kategori → AJAX getMppBreakdown                       │
│     └─ Tampilkan daftar posisi + headcount per bulan           │
│                                                                │
│  3. Isi/ubah headcount per posisi per bulan                    │
│     └─ POST saveMppBreakdown (JSON: rows[])                    │
│         ├─ AccessRestrict::checkLock()                         │
│         └─ Model::saveMppPositions()                           │
│                                                                │
│  4. Sync to OPEX                                               │
│     └─ Kalkulasi: headcount × salary rate × 12 bulan           │
│     └─ INSERT ke trans_budget_entry_data (source='MPP')        │
│                                                                │
└───────────────────────────────────────────────────────────────┘
```

**Periode Info:**
- `getSubmitPeriod()` membaca `yp_plan__master_period` WHERE tipe='MPP' untuk menampilkan informasi kapan periode submit dibuka.

### Integrasi & Ketergantungan

- **AccessRestrict** — concurrent lock per form
- **AuditLog** — trace save/delete/sync
- **SalaryMppModel** — rate gaji per posisi
- **OPEX trans_budget_entry_data** — target sync (source='MPP')

### Maintenance & Watchouts

- `deleteMppPosition()` menghapus **transaksi** posisi, bukan master posisi.
- `syncToOpex()` hanya menimpa baris dengan `source='MPP'` — entry OPEX manual aman.
- Grand total per bulan dihitung server-side dan dikembalikan sebagai `totals[]` array (indeks 0-11 = Jan-Dec, 12 = total).
- Validasi input: `position_id` dan `tipe_id` harus integer > 0, `id_dept` harus numeric string.

---

## 16. Modul: Profit & Loss (P/L) Report

### Nama & Tujuan

Modul monitoring progress entry budget per cost center dan laporan konsolidasi Profit & Loss. Bersifat **read-only** (plus adjustment manual & notes) — tidak ada entry budget baru.

### Data Source & Skema

| Tabel/Source | Fungsi |
|-------|--------|
| Aggregasi dari `trans_budget_entry_data` | Sumber data budget OPEX/FOH |
| Aggregasi dari trans sales | Sumber data revenue |
| `yp_plan__pl_notes` (atau serupa) | Catatan per bagian P/L |
| `yp_plan__pl_adjs` (atau serupa) | Adjustment manual per bagian |

### Controller & Route

| Route | Fungsi |
|-------|--------|
| `GET monitoring/` atau `GET pl/` | Monitoring progress entry |
| `GET pl/summary` atau `GET report-profit-loss` | Laporan P/L |
| `GET monitoring/cari_view_data` | AJAX: detail per CC |
| `GET pl/get_detail_account` | AJAX: detail per akun |
| `GET pl/get_section_detail` | AJAX: detail per bagian |
| `POST pl/save_notes` | AJAX: simpan catatan |
| `POST pl/save_adjs` | AJAX: simpan adjustment manual |
| `GET pl/export_excel` | Export P/L ke Excel |

### Business Logic

**P/L Sections (definisi di ModelPl::PL_SECTIONS):**
```
Revenue (Sales)
  - Domestic Revenue
  - Export Revenue
COGS (Cost of Goods Sold)
  - FOH
  - Material
Gross Profit
OPEX
  - GA Expense
  - Selling Expense
Operating Profit
Adjustment
Net Profit
```

**Monitoring Progress:**
- Menampilkan ringkasan per cost center: berapa yang sudah entry, status submit, dsb.
- Data OPEX + FOH + MPP + CAPEX di-aggregate per dept.

**Adjustment Manual:**
- Admin bisa menambahkan adjustment per bagian P/L (misalnya koreksi manual)
- Disimpan terpisah dari data transaksi (tabel adjustment)
- `Total Adjusted = Total + Adjustment`

### Integrasi & Ketergantungan

- **ModelPl** — semua query aggregasi P/L ada di model ini
- **ExcelExporter** — export laporan P/L
- **AuditLog** — trace notes & adjustment

### Maintenance & Watchouts

- P/L adalah **report agregasi** — tidak punya tabel transaksi sendiri. Bug di modul sumber (Sales/OPEX/FOH) langsung mempengaruhi P/L.
- Export Excel memiliki kolom: SEKSI P/L, KATEGORI, JAN-DEC, TOTAL, ADJUSTMENT, TOTAL ADJUSTED.
- `rowState` untuk Alpine.js disiapkan server-side (JSON) agar interaktivitas per-baris tanpa AJAX tambahan.

---

## 17. Modul: Dashboard & Grand Report

### 17.1 Dashboard

| Aspek | Detail |
|-------|--------|
| **Controller** | `DashboardController` |
| **Model** | `DashboardModel` |
| **Route** | `GET /`, `GET /dashboard` |

**Data yang ditampilkan:**
- `stats` — ringkasan angka kunci (total budget, actual, variance)
- `monthly` — time series bulanan (untuk chart)
- `composition` — komposisi budget per kategori (pie chart)
- `statusRows` — status submit per modul/dept
- `hasBudget` — flag apakah ada data budget untuk tahun ini
- `rolelessUsers` — (admin only) jumlah user aktif tanpa role

### 17.2 Grand OPEX Report

| Aspek | Detail |
|-------|--------|
| **Controller** | `OpexReportController` |
| **Route** | `GET report-grand-opex`, `POST report-grand-opex/data` |

**Logic:**
- Mengambil data dari **semua** cost center OPEX GA
- Mengambil data OPEX Selling
- Menggabungkan via `OpexReportService::combine()`
- Output: tabel konsolidasi OPEX GA + OPEX SELLING

---

## 18. Shared Libraries & Utilities

### 18.1 ExcelExporter (`app/Libraries/ExcelExporter.php`)

| Aspek | Detail |
|-------|--------|
| **Dependency** | PhpSpreadsheet |
| **Fungsi** | Export array data ke .xlsx response download |
| **Interface** | `ExcelExporter::export($headers, $rows, $filename, $sheetTitle, $options)` |
| **Styling** | Header biru (brand), font bold putih, auto-width, freeze row |
| **Dipakai oleh** | SalesController, OpexGaController, OpexSellingController, FohController, CapexController, PlController |

### 18.2 ExcelImporter (`app/Libraries/ExcelImporter.php`)

| Aspek | Detail |
|-------|--------|
| **Dependency** | PhpSpreadsheet |
| **Fungsi** | Baca file .xlsx/.xls → array PHP |
| **Interface** | `ExcelImporter::import($file, $assoc, $headerRow, $sheetName)` |
| **Mode** | `$assoc=true` → header row jadi key; `false` → array numerik |
| **Helper** | `::toFloat()`, `::column()` — normalisasi nilai & lookup kolom |
| **Dipakai oleh** | SalesController, OpexGaController, OpexSellingController, FohController |

### 18.3 AccessRestrict (`app/Libraries/AccessRestrict.php`)

| Aspek | Detail |
|-------|--------|
| **Tabel** | `gw_sm__access_restrict` |
| **Timeout** | 15 menit (auto-purge) |
| **Interface** | `checkLock($userId, $url, $year): ['allowed', 'accessed_by', 'message']` |
| **Fail-safe** | Jika tabel error → selalu izinkan (try/catch) |
| **Dipakai oleh** | OpexGaController, OpexSellingController, FohController, MppController, CapexController |

### 18.4 AuditLog (`app/Libraries/AuditLog.php`)

| Aspek | Detail |
|-------|--------|
| **Tabel** | `gw_sm__logs` (id, type, user, function, message, create_date) |
| **Tipe** | LOGIN, LOGOUT, SAVE, SUBMIT, DELETE, UPLOAD, EXPORT, RBAC, ADJUSTMENT, NOTES, APPROVE, REJECT, SYNC |
| **Interface** | `AuditLog::log($type, $function, $message, $userId)` |
| **Helper** | `::saved()`, `::submitted()` |
| **Fail-safe** | Semua error ditelan — logging tidak pernah memblokir proses utama |

### 18.5 AuthorizationService (`app/Libraries/AuthorizationService.php`)

| Aspek | Detail |
|-------|--------|
| **Fungsi** | Core RBAC logic — build context, resolve permissions, authorize requests |
| **Interface** | `refresh($userId)`, `authorizeRequest($method, $uri, $context)`, `getAllowedMenuIds()`, `getMenuAccessState($userId)` |
| **Session keys** | `auth_obj`, `is_admin`, `role_ids`, `effective_menu_ids` |
| **Dipakai oleh** | LoginController, AuthorizationContextFilter, RoleFilter, MenuBuilder, UserController |

### 18.6 MenuBuilder (`app/Libraries/MenuBuilder.php`)

| Aspek | Detail |
|-------|--------|
| **Fungsi** | Render HTML sidebar menu berdasarkan effective_menu_ids + menu_structure |
| **Output** | HTML `<nav>` dengan Alpine.js interaktivity |
| **Logic** | Tree traversal parent-child, visibility filtering, active path detection |
| **Helper** | `render_menu_sidebar()` (di `menu_helper.php`) |

### 18.7 DbCompat (`app/Libraries/DbCompat.php`)

| Fungsi | Mengecek keberadaan kolom/tabel yang mungkin belum ada (kompatibilitas legacy ↔ migrasi baru) |
| **Contoh** | `DbCompat::hasEntrySource()` — cek kolom `source` di trans_budget_entry_data |

### 18.8 CsvExporter (`app/Libraries/CsvExporter.php`)

| Fungsi | Export data ke CSV response (lebih ringan dari Excel, dipakai Master Data export) |

---

## 19. Database Schema Overview

### 19.1 Prefix Naming Convention

| Prefix | Namespace | Modul |
|--------|-----------|-------|
| `gw_sm__` | System Management | User, Role, Menu, Profile, Logs, Access |
| `gw_plan__` | Planning Master | COA, Cost Center, Department, Product |
| `yp_plan__` | Yearly Plan | Transaksi budget, Actual, Assumption, Period |

### 19.2 Tabel Inti — System

| Tabel | PK | Fungsi |
|-------|----|----|
| `gw_sm__user` | user_id | Master user |
| `gw_sm__role` | role_id | Master role (tipe: menu/object) |
| `gw_sm__profile` | profile_id | Relasi user → role (FK: profile_user_id, profile_role_id) |
| `gw_sm__menu` | menu_id | Master menu |
| `gw_sm__menu_structure` | structure_id | Parent-child menu (structure_menu_id → structure_child_menu_id) |
| `gw_sm__rolemenu` | rolemenu_id | Permission role → menu |
| `gw_sm__usermenu` | usermenu_id | Override per-user (sparse delta) |
| `gw_sm__role_object` | — | Object-level scope (cost center access per role) |
| `gw_sm__access_restrict` | user_id | Concurrent lock |
| `gw_sm__logs` | id | Audit trail |

### 19.3 Tabel Inti — Master Data

| Tabel | PK | Fungsi |
|-------|----|----|
| `gw_plan__master_coa` | id_cost_center | Chart of Account |
| `gw_plan__master_cost_center` | id_cost_center | Cost Center |
| `gw_plan__master_department` | id_dept | Departemen |
| `gw_plan__master_product` | id_product | Produk |
| `yp_plan__master_year` | year_code | Tahun anggaran |
| `yp_plan__master_period` | id | Periode entry per modul |
| `yp_plan__master_assumption_type` | id | Kamus tipe asumsi |
| `yp_plan__master_assumption` | id | Nilai asumsi ekonomi per tahun |
| `yp_plan__master_reclass_monthly` | — | Alokasi discount reclass |

### 19.4 Tabel Inti — Transaksi

| Tabel | PK | Fungsi |
|-------|----|----|
| `yp_plan__trans_budget_entry_data` | id | Budget OPEX/FOH/Selling (per COA × Dept × Year) |
| `yp_plan__trans_budget_entry_detail` | id | Breakdown item budget |
| `yp_plan__trans_sales_domestic` | — | Sales domestic (per produk × channel × bulan) |
| `yp_plan__trans_sales_domestic_region` | — | Sales domestic per region |
| `yp_plan__trans_sales_export` | — | Sales export |
| `yp_plan__trans_sales_export_country` | — | Sales export per country |
| `yp_plan__trans_delivery_customer` | — | Delivery expense & claim |
| `yp_plan__assump_sales` | id | Assumption sales |
| `yp_plan__assump_rate` | — | Kurs valuta asing |
| CAPEX transaction table | — | Transaksi CAPEX |
| MPP transaction table | — | Transaksi headcount MPP |

### 19.5 Kolom Standar pada Tabel Transaksi Budget

```sql
-- yp_plan__trans_budget_entry_data
id              INT AUTO_INCREMENT PK
id_coa          INT/VARCHAR         -- Referensi ke COA
id_dept         INT/VARCHAR         -- Referensi ke Cost Center / Dept
year_code       INT                 -- Tahun anggaran
1..12           DECIMAL             -- Nilai per bulan (Januari = 1, ..., Desember = 12)
total           DECIMAL             -- Total tahunan
notes           TEXT                -- Catatan
source          VARCHAR(20) NULL    -- NULL/manual, 'MPP', 'CAPEX', 'SELLING'
submit_status   ENUM('DRAFT','SUBMITTED','APPROVED')
submit_by       INT NULL
submit_date     DATETIME NULL
created_by      INT
created_date    TIMESTAMP
```

### 19.6 ERD Simplified (RBAC)

```
                    ┌────────────────┐
                    │  gw_sm__user   │
                    │  (user_id PK)  │
                    └────────┬───────┘
                             │ 1:N
                             ▼
                    ┌────────────────┐          ┌────────────────┐
                    │ gw_sm__profile │─────────▶│  gw_sm__role   │
                    │ (user → role)  │   N:1    │  (role_id PK)  │
                    └────────────────┘          └────────┬───────┘
                                                         │ 1:N
                             ┌────────────────┐          ▼
                             │gw_sm__usermenu │  ┌────────────────┐
                             │(user → menu    │  │gw_sm__rolemenu │
                             │ override Y/N)  │  │(role → menu)   │
                             └───────┬────────┘  └────────┬───────┘
                                     │                    │
                                     └────────┬───────────┘
                                              │ N:1
                                              ▼
                                     ┌────────────────┐
                                     │  gw_sm__menu   │
                                     │  (menu_id PK)  │
                                     └────────────────┘
```

---

## 20. Panduan Deployment & Maintenance

### 20.1 Setup Awal

```bash
# 1. Clone repository
git clone <repo-url> yp_budget_new

# 2. Install dependencies
composer install

# 3. Copy environment file
cp env .env
# Edit .env: set CI_ENVIRONMENT, database credentials, MASTER_LOGIN_HASH

# 4. Import database schema
mysql -u root < yp_budget_system.sql

# 5. Jalankan migrasi CI4
php spark migrate

# 6. Jalankan seeder (user & role awal)
php spark db:seed RbacSeeder
php spark db:seed UserSeeder

# 7. Set permission writable/
chmod -R 775 writable/
```

### 20.2 Menambah Modul Baru

1. **Buat Controller** — extend `BaseController`
2. **Buat Model** — tabel referensi dan/atau transaksi
3. **Tambahkan Route** — di `app/Config/Routes.php` dengan filter `auth`
4. **Daftarkan di Authorization** — tambahkan policy mapping di `Config/Authorization.php`
5. **Tambahkan Menu** — INSERT ke `gw_sm__menu` (atau via System Admin UI)
6. **Assign ke Role** — INSERT ke `gw_sm__rolemenu` (atau via Role Management UI)
7. **Jika modul transaksi** — tambahkan entry di `PeriodLockFilter::MODULE_TYPES`
8. **Jika butuh concurrent lock** — tambahkan di `EnsureContextFilter::ENTRY_FORM_SECONDS`

### 20.3 Menambah Route Baru ke Modul Existing

1. Definisikan route di `Routes.php`
2. Tambahkan mapping di `Config/Authorization.php` → `policies()` method
3. Pastikan `gw_sm__menu.menu_link` sesuai (canonical link)
4. Test: login sebagai user non-admin dan pastikan akses sesuai role

### 20.4 Migration Best Practices

- Selalu gunakan `IF NOT EXISTS` / `ensureColumn()` untuk tabel/kolom — agar aman di DB yang sudah ada data legacy.
- Nama file: `YYYY-MM-DD-NNNNNN_DescriptiveName.php`
- Satu migrasi per perubahan logis (jangan campur DDL dan DML kecuali seeding).

### 20.5 Monitoring & Troubleshooting

| Problem | Diagnosa | Solusi |
|---------|----------|--------|
| User tidak bisa akses halaman | Cek `gw_sm__menu.menu_link` vs URI | Pastikan menu_link exact match, assign ke role |
| Form dikunci "sedang diakses user lain" | Cek `gw_sm__access_restrict` | Hapus record lama (>15 menit) atau tunggu auto-purge |
| Form entry diblokir "periode ditutup" | Cek `yp_plan__master_period` | Pastikan ada record aktif yang mencakup hari ini |
| P/L report kosong | Cek data transaksi modul sumber | Pastikan ada data di trans_budget_entry_data untuk tahun ini |
| Upload Excel gagal | Cek format kolom header | Mapping kolom case-insensitive, tapi nama kolom harus sesuai |
| Login gagal | Cek `user_active`, password hash, `MASTER_LOGIN_HASH` | Reset password via admin panel |

### 20.6 Performance Considerations

- **N+1 Query**: Grand OPEX Report loop per cost center — bisa lambat jika banyak CC. Pertimbangkan batch query.
- **Load-all pada OPEX Selling Actual**: `getActualData()` load semua row tanpa pagination — OK untuk <1000 baris, bermasalah jika data tumbuh.
- **Session-based year**: Setiap perpindahan tahun memicu refresh context dari DB. Cache context di session sudah diterapkan.

### 20.7 Security Notes

- **SQL Injection**: Query parameter sudah menggunakan binding (`?`) atau Query Builder CI4.
- **XSS**: Views menggunakan `esc()` untuk output. Pastikan semua input user di-escape.
- **CSRF**: Filter CSRF terdaftar di aliases tapi **belum** diaktifkan di globals. Pertimbangkan mengaktifkan.
- **Session Fixation**: Sudah dicegah via `session()->regenerate()` saat login.
- **Whitelist Column**: `getGroupedSummary()` di Sales menggunakan whitelist validasi untuk mencegah SQL injection di kolom dinamis.

### 20.8 Backup & Recovery

```bash
# Backup database
mysqldump -u root yp_budget_system > backup_$(date +%Y%m%d).sql

# Restore
mysql -u root yp_budget_system < backup_YYYYMMDD.sql

# Rollback migrasi (hati-hati: beberapa migrasi irreversible)
php spark migrate:rollback
```

---

## Lampiran A: Daftar Model

| Model | Tabel Utama | Fungsi |
|-------|-------------|--------|
| `UserModel` | gw_sm__user | CRUD user, auth lookup |
| `RoleModel` | gw_sm__role, gw_sm__rolemenu | CRUD role & permission |
| `UserRoleModel` | gw_sm__profile | Assign role ke user |
| `UserMenuModel` | gw_sm__usermenu | Override menu per user |
| `MenuModel` | gw_sm__menu, gw_sm__menu_structure | CRUD menu |
| `CoaModel` | gw_plan__master_coa | CRUD Chart of Account |
| `CostCenterModel` | gw_plan__master_cost_center | CRUD Cost Center |
| `DepartmentModel` | gw_plan__master_department | CRUD Departemen |
| `ProductModel` | gw_plan__master_product | CRUD Produk |
| `SalaryMppModel` | salary table | CRUD Salary MPP |
| `PeriodModel` | yp_plan__master_year, yp_plan__master_period | CRUD Period/Year |
| `OpexGaModel` | trans_budget_entry_data/detail | Logic OPEX GA |
| `OpexSellingModel` | trans_budget_entry_data/detail | Logic OPEX Selling |
| `FohModel` | trans_budget_entry_data/detail | Logic FOH |
| `CapexModel` | trans_capex | Logic CAPEX |
| `MppModel` | trans_mpp | Logic MPP |
| `ModelPl` | aggregasi multi-tabel | Logic P/L Report |
| `DashboardModel` | aggregasi multi-tabel | Dashboard stats |
| `AssumptionModel` | master_assumption_* | Asumsi ekonomi & sales |
| `ChannelModel` | master channel | Referensi channel |
| `NewHeadAccountModel` | — | Helper header account |
| `MppDetailModel` | — | Detail breakdown MPP |

---

## Lampiran B: Daftar Route Lengkap (Grouped)

### Public Routes
```
GET  /login
POST /login/process
GET  /logout
GET  /api/active-years
```

### Dashboard & Utility
```
GET  /                      → DashboardController::index
GET  /dashboard             → DashboardController::index
POST /set-year              → PeriodController::setYear
GET  /auth/change-password-form
POST /auth/changePassword
```

### Master Data (`/master/*`)
```
GET  /master/coa | cost-center | department | product | salary-mpp | configure-period
GET  /master/*/export
POST /master/api/*/save | toggle | copy-year | set-active | set-locked | delete
```

### System Admin (`/sys-admin/*`)
```
GET  /sys-admin/menu | role | user
POST /sys-admin/api/menu/save | toggle | delete
POST /sys-admin/api/role/save | toggle | delete | menus | menus/save
POST /sys-admin/api/user/save | toggle | delete | reset-password | roles
```

### Sales (`/sales/*`)
```
GET  /sales/ | domestic/entry | export/entry | simulation | setup-target
POST /sales/saveDiscountReclass | processUpload | proses_summary_domestic | cari_domestic_sales
```

### OPEX GA (`/opex-ga/*`)
```
GET  /opex-ga/ | entry | entry-budget | entry-budget-detail | actual-budget | report-department
GET  /opex-ga/getEntryData | getHeaderAccounts | getDetailMatrix | download-template | exportExcel
POST /opex-ga/saveBudget | saveDetailItems | submitBudget | getActualData | uploadActual
```

### OPEX Selling (`/opex-selling/*`)
```
GET  /opex-selling/ | entry | entry-budget-detail | actual | report-department
POST /opex-selling/saveBudget | saveEntryDetail | saveDetailItems | getActualData | uploadActual
```

### FOH (`/foh/*`)
```
GET  /foh/ | entry | entry-budget-detail | actual | summary
POST /foh/saveBudget | saveDetailItems | submit | approve | reject | uploadActual
```

### CAPEX (`/capex/*`)
```
GET  /capex/entry | summary | getEntryData | getSummary* | exportSummaryExcel
POST /capex/saveFormCapex | save_capex | sync_to_opex
```

### MPP (`/mpp/*`)
```
GET  /mpp/ | entry | summary | getCostCenters | getMppMatrix | getMppBreakdown | getViewData
POST /mpp/syncToOpex | saveMppBreakdown | deleteMppPosition
```

### Reports
```
GET  /monitoring | pl | report-profit-loss | report-grand-opex
POST /pl/save_notes | save_adjs
GET  /pl/export_excel
```

---

## Lampiran C: Checklist Maintenance Rutin

- [ ] Pastikan `yp_plan__master_period` dikonfigurasi untuk tahun baru sebelum periode dimulai
- [ ] Jalankan `php spark migrate` setelah deploy update
- [ ] Periksa `gw_sm__logs` untuk anomali (failed login berulang, error pattern)
- [ ] Monitor `gw_sm__access_restrict` — hapus lock stuck jika ada
- [ ] Backup database minimal mingguan
- [ ] Review user tanpa role (Dashboard admin warning)
- [ ] Pastikan kurs (`yp_plan__assump_rate`) diisi sebelum entry sales export
- [ ] Test upload Excel dengan format terbaru sebelum periode entry dibuka
- [ ] Clear `writable/cache/` jika ada perubahan konfigurasi

---

*Dokumen ini disusun berdasarkan analisis source code aktif per 19 Agustus 2026. Untuk pertanyaan lebih lanjut, rujuk langsung ke file controller/model terkait.*
