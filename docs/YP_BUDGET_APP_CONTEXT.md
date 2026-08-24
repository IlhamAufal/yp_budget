# YP Budget System - Application Context Document

> Dokumen ini berisi konteks lengkap aplikasi **YP Budget System** yang saat ini berjalan di CodeIgniter 3 + HMVC.
> Tujuan: menjadi referensi untuk agent yang akan melakukan rebuild di environment **CodeIgniter 4**.

---

## 1. Overview

**Nama Aplikasi:** YP Budget System  
**Company:** PT Yupi Indo Jelly Gum  
**Versi Saat Ini:** 1.1.0  
**Framework:** CodeIgniter 3.x + HMVC (Modular Extensions - MX)  
**Database:** MySQL (database `yp_budget_system`)  
**PHP Version:** 7.x+  
**Session Driver:** Database (`ci_sessions` table)  
**Timezone:** Asia/Jakarta

### Apa yang dilakukan apps ini?

Ini adalah **Corporate Budget Planning System** untuk mengelola proses perencanaan anggaran tahunan perusahaan manufaktur (food & confectionery). Fitur utamanya mencakup:

- Entry budget bulanan (Jan-Dec) untuk berbagai kategori biaya
- Sales forecasting (domestic & export) dengan multi-currency
- COGS calculation (Raw Material, Packaging Material, Direct Labor)
- OPEX management (G&A dan Selling)
- Factory Overhead (FOH) budgeting
- Capital Expenditure (CAPEX) dengan depreciation calculation
- Manpower Planning (MPP)
- Profit & Loss reporting
- Approval workflow
- Multi-year comparison & monitoring

---

## 2. Architecture & Stack

### 2.1 Framework Structure

```
CI3 + HMVC (MX - Modular Extensions)
├── apps/                        # Application folder
│   ├── config/                  # Configuration files
│   ├── core/                    # Extended core classes
│   │   ├── MY_Controller.php   # Base controller (extends MX_Controller)
│   │   ├── MY_Router.php       # Extends MX_Router
│   │   └── MY_Loader.php       # Extends MX_Loader
│   ├── modules/                 # HMVC Modules (23 modules)
│   ├── models/                  # Shared models
│   ├── logics/                  # Business logic classes (namespaced)
│   ├── helpers/                 # Custom helpers
│   ├── libraries/               # PhpSpreadsheet, custom libs
│   ├── third_party/             # MX (HMVC), CodeIgniter Debugbar
│   └── views/                   # Shared views (sidebar, header, footer)
├── assets/                      # CSS, JS, images, fonts
├── vendor/                      # Composer packages
└── parameters.php               # Database connection config (external)
```

### 2.2 Key Dependencies

| Library | Fungsi |
|---------|--------|
| PhpSpreadsheet | Export/import Excel (upload budget, download reports) |
| Modular Extensions (MX) | HMVC pattern support |
| CodeIgniter Debugbar | Development debugging |
| AdminLTE | UI framework (CSS/JS) |
| jQuery + DataTables | Frontend interaktifitas & tabel |
| Bootstrap 3 | CSS framework |
| Select2, DatePicker | Form components |

### 2.3 Autoloaded Resources

- **Libraries:** database, email, session, form_validation, upload
- **Helpers:** url, file, general, cookie, form
- **Config:** yupiland.php (company-specific parameters)
- **Packages:** codeigniter-debugbar

---

## 3. Authentication & Authorization

### 3.1 Login Flow

- Login via email/username + password (MD5 + salt)
- Session disimpan di database table `ci_sessions`
- Multi-connection support: bisa connect ke database berbeda berdasarkan XML config (`Access_Config.xml`, `DB_config.xml`)
- Cookie `connection` menentukan koneksi database yang digunakan
- Session expiration: 7200 detik (2 jam)

### 3.2 Role-Based Access Control

- Tabel: `gw_sm__user`, `gw_sm__role`, `gw_sm__profile`, `gw_sm__rolemenu`
- Role types: `menu` (akses menu) dan `objek` (akses data/department)
- Setiap user di-assign ke role, role di-mapping ke menu yang boleh diakses
- Authorization object: menentukan department/cost center mana yang boleh dilihat user
- Admin role IDs: `1, 2` (configurable via `admin_authorization`)

### 3.3 Data Authorization (Dynamic Otorisasi)

- User memiliki `auth_obj` di session yang berisi department/cost center yang boleh diakses
- Setting disimpan di tabel authorization object (`gw_sm__role_object_setting`)
- Field-based filtering: bisa membatasi berdasarkan field tertentu (misal cost_center, department)
- Wildcard `*` berarti akses ke semua data

### 3.4 Access Restrict Pattern

Setiap halaman melakukan check concurrent access:
- Insert record ke `gw_sm__access_restrict` saat user membuka halaman
- Jika sudah ada user lain yang membuka, `validasi` = "NOPE"
- Ini digunakan untuk single-user editing lock (hanya 1 user boleh edit di satu halaman per tahun)

---

## 4. Multi-Language Support

Mendukung 3 bahasa:
- **English** (default)
- **Indonesia**
- **Japan** (日本語)

Language files di `apps/language/{english|indonesia|japan}/`. Setiap text UI di-reference via `$this->lang->line('key')`.

---

## 5. Database Schema (Key Tables)

### 5.1 System/Auth Tables (prefix: `gw_sm__`)

| Table | Fungsi |
|-------|--------|
| `gw_sm__user` | Master user (user_id, user_name, user_email, user_password, user_salt) |
| `gw_sm__role` | Master role (role_id, role_name, role_type, role_active) |
| `gw_sm__profile` | User-Role mapping (profile_user_id, profile_role_id) |
| `gw_sm__rolemenu` | Role-Menu access (rolemenu_role_id, rolemenu_menu_id) |
| `gw_sm__menu` | Master menu (menu_id, menu_name_eng/idn/jpn, menu_link, menu_icon, menu_parrent, menu_order) |
| `gw_sm__menu_structure` | Menu hierarchy (structure_menu_id, structure_child_menu_id) |
| `gw_sm__module` | Module registry |
| `gw_sm__access_restrict` | Concurrent access lock |
| `gw_sm__recovery_password` | Password reset tokens |
| `gw_sm__mail_template` | Email templates |
| `gw_sm__role_object_setting` | Authorization object settings per role |

### 5.2 Master Data Tables (prefix: `gw_plan__master_`)

| Table | Fungsi |
|-------|--------|
| `gw_plan__master_product` | Master produk (SKU, nama, capacity, price) |
| `gw_plan__master_product_new` | Produk baru (new lines) |
| `gw_plan__master_coa` | Chart of Accounts (main_account, cost_center_header, cost_center_desc, type: GA/SELLING/FOH) |
| `gw_plan__master_cost_center` | Master cost center (cost_center, cost_center_sap, cost_desc) |
| `gw_plan__master_department` | Master department |
| `gw_plan__master_amount_depreciation` | Depreciation amounts per account |
| `gw_plan__master_packaging_material` | PM cost parameters (blister, box, display, etc.) |

### 5.3 Transaction Tables (prefix: `yp_plan__trans_`)

| Table | Fungsi |
|-------|--------|
| `yp_plan__trans_budget_entry_data` | Entry budget utama (id_coa, id_dept, year_code, `1`-`12`, total) |
| `yp_plan__trans_budget_entry_data_newlines` | Budget entry untuk sub-items/newlines |
| `yp_plan__trans_budget_total_entry_data` | Aggregated budget total per cost header |
| `yp_plan__trans_budget_actual` | Actual/realisasi data (columns: `1`-`12` + `6`,`7`,`8` for historical) |
| `yp_plan__trans_capex_entry_header` | CAPEX entry header (main_account, cost_center, year_code) |
| `yp_plan__trans_capex_entry_detail` | CAPEX entry detail (monthly values `1`-`12`) |
| `yp_plan__trans_sales_domestic_region` | Sales domestic per region/product |
| `yp_plan__trans_sales_export_country` | Sales export per country (region, country, currency, qty & rev per month) |
| `yp_plan__trans_version` | Budget version tracking |
| `yp_plan__trans_cogs_adjusment` | COGS adjustment values per type |
| `yp_plan__master_reclass_monthly` | Balance reclassification monthly |

### 5.4 Pola Data Budget

Mayoritas tabel transaksi menggunakan pola kolom bulanan:
```
id | id_coa | id_dept | year_code | `1` | `2` | `3` | ... | `12` | total | notes | created_by | created_date
```

Kolom `1` sampai `12` merepresentasikan **Januari sampai Desember**.

---

## 6. Modules Detail

### 6.1 Home Module (`/home`)

**Fungsi:** Landing page, login, logout, password management, portal

| Method | Fungsi |
|--------|--------|
| `index` | Dashboard utama setelah login, load version data |
| `portal` | Portal/splash page |
| `login_new` | Form login dengan multi-connection select |
| `logout` | Destroy session + clear access restrict |
| `changepassword` | Ganti password user (MD5+salt) |
| `reset_password` | Reset password via token (email flow) |
| `recoveryPassword` | Kirim email reset password |
| `registration_process` | Registrasi user baru |
| `find_nik` | AJAX: cari data karyawan by NIK |

---

### 6.2 Master Module (`/master`)

**Fungsi:** Manage semua master data dan assumptions

| Method | Fungsi |
|--------|--------|
| `product` | CRUD master produk |
| `cost_center` | CRUD master cost center |
| `department` | CRUD master department |
| `coa` | CRUD Chart of Accounts |
| `period` | Configure budget period (open/close) |
| `assumption` | Setup assumptions (USD rate, inflasi, UMK, etc.) |
| `mpp` | Master MPP salary setup |
| `formula` | Formula budget calculation |
| `mapping_product` | Mapping product ke category |
| `setup_product_capacity` | Setup production capacity per SKU |
| `save_budget_sync` | Save budget data (monthly values Jan-Dec) |

**Views:** assumption, configure_period, master_coa, master_department, master_product, formula, mapping_product, ratio_target, shift, template uploads (capacity, gelatine, sugar, raw material, dll.)

**Upload Templates:** Raw material, gelatine, glucose, sugar, capacity, collagen, delivery, customer claim, UMP, ratio target, master CC, master product, base SKU

---

### 6.3 Sales Module (`/sales`)

**Fungsi:** Sales budget planning - domestic & export

| Method | Fungsi |
|--------|--------|
| `entry_domestic` | Entry sales domestic per region/product (qty & revenue per month) |
| `entry_export` | Entry sales export per country (multi-currency) |
| `summary` / `summary_all_new` | Summary all sales data |
| `summary_domestic` | Summary domestic only |
| `summary_export` / `summary_export_idr` | Summary export (in original currency & IDR) |
| `summary_by_country` | AJAX: summary grouped by country |
| `summary_regional` | AJAX: summary grouped by region |
| `cari_sales_export_country` | AJAX: search export data by country |
| `cari_sales_domestic_region` | AJAX: search domestic data by region |
| `upload_domestic_data_2` | Upload domestic data via Excel |
| `upload_export_data_2` | Upload export data via Excel |
| `export_template_sales` | Download Excel template |
| `simulation` | Sales simulation (volume/ASP adjustments) |

**Key Fields:** region, country, product_name, div, key_product, currency, monthly qty & rev (jan_qty, jan_rev, ..., dec_qty, dec_rev)

---

### 6.4 OPEX G&A Module (`/opex_ga`)

**Fungsi:** Operational Expenditure - General & Administrative

| Method | Fungsi |
|--------|--------|
| `entry` | Entry budget OPEX GA per cost center |
| `save_budget` | Save budget entries (monthly Jan-Dec per COA) |
| `actual` | Upload actual/realisasi budget |
| `entry_budget_table` | AJAX: load entry table untuk specific dept/COA |
| `entry_budget_detail` | AJAX: load newlines/sub-items |
| `report_department` | Report per department |
| `report_combine` | Combined report (grand total all departments) |
| `cari_total_opex_category` | AJAX: total per OPEX category |
| `get_detail_items` / `save_detail_item` / `delete_detail_item` | CRUD detail line items |
| `pdf_reader` | View manual book (PDF) |

**COA Prefix:** `7710xxx` (GA accounts)
**Type filter:** `type = 'GA'` di `gw_plan__master_coa`

---

### 6.5 OPEX Selling Module (`/opex_selling`)

**Fungsi:** Operational Expenditure - Sales & Marketing

Struktur sama dengan OPEX GA, tapi:
- **COA Prefix:** `7700xxx` (Selling accounts)
- **Type filter:** `type = 'SELLING'` di `gw_plan__master_coa`

| Method | Fungsi |
|--------|--------|
| `entry` | Entry budget OPEX Selling per cost center |
| `save_budget` | Save entries |
| `actual` | Upload actual data |
| `report_department` | Report per department |
| `entry_budget_table` | AJAX: load table per dept |

---

### 6.6 FOH Module (`/foh`)

**Fungsi:** Factory Overhead budgeting

| Method | Fungsi |
|--------|--------|
| `entry` | Entry FOH budget per cost center |
| `save_budget` | Save budget entries |
| `actual` | Upload actual FOH data |
| `summary` | FOH summary overview |
| `report_department` | Report per department |
| `report_combine` | Grand total report |
| `summary_account` | Summary by account |
| `summary_costcenter` | Summary by cost center |
| `entry_budget_table` | AJAX: load entry table |
| `entry_budget_detail` | AJAX: load newlines |

**COA Prefix:** `6605xxx` (FOH accounts)
**Type filter:** `type = 'FOH'`
**Features:** Uses PhpSpreadsheet for Excel export

---

### 6.7 CAPEX Module (`/capex`)

**Fungsi:** Capital Expenditure planning dengan depreciation

| Method | Fungsi |
|--------|--------|
| `entry` | Entry CAPEX items (asset name, amount, useful life) |
| `save_capex` | Save CAPEX entries |
| `sync_to_opex` / `proses_to_opex` | Calculate depreciation & sync ke OPEX tables |
| `report` | CAPEX report per department |
| `report_total` | Total CAPEX report |
| `summary` | CAPEX summary |
| `pdf_reader` | Manual book viewer |
| `cari_entry_data` | AJAX: search entry data |
| `cari_view_data` / `cari_view_data_all` | AJAX: view data |

**Tables:** `yp_plan__trans_capex_entry_header` + `yp_plan__trans_capex_entry_detail`
**Depreciation:** Calculated based on `gw_plan__master_amount_depreciation`, accumulated monthly

---

### 6.8 MPP Module (`/mpp`)

**Fungsi:** Manpower Planning

| Method | Fungsi |
|--------|--------|
| `entry` | Entry MPP per department (headcount, salary, benefits) |
| `summary` | MPP summary per department |
| `save_mpp` | Save MPP data |
| `cari_mpp_table` | AJAX: load MPP entry table |

**Features:**
- Dual identifier: `cost_center` (internal) & `cost_center_sap` (SAP code)
- Helper method `_resolve_cost_center_identifiers()` to handle both
- Data: headcount per position, salary breakdown monthly

---

### 6.9 COGS Module (`/cogs`)

**Fungsi:** Cost of Goods Sold calculation

| Method | Fungsi |
|--------|--------|
| `rm` | Raw Material cost calculation |
| `pm` | Packaging Material cost (blister, box, display, PP, VBS) |
| `dlp` | Direct Labor Process |
| `total_dlp` | Total DLP aggregation |
| `summary` | COGS summary |
| `yti` | Year-to-date Indicator |
| `other` | Other COGS items |

**Key Concepts:**
- Raw Material: based on product volume x material rate
- Packaging Material: volume x packaging percentages (blister, box, display, others, PP, VBS)
- DLP: direct labor hours x rate
- Adjustments: `yp_plan__trans_cogs_adjusment` table with type_cogs (RM/PM/DLP) and type_channel (DOMES/INTR)
- Upload support via Excel for all sub-categories

---

### 6.10 Mogul Module (`/mogul`)

**Fungsi:** Direct Labor Mogul (specific manufacturing line budgeting)

| Method | Fungsi |
|--------|--------|
| `actual` | View/upload actual DL Mogul data |
| `entry` | Entry budget DL Mogul |
| `report_department` | Report per department |
| `save_budget` | Save entries |

**Features:** Similar structure to OPEX, uses PhpSpreadsheet for Excel

---

### 6.11 P/L Module (`/pl`)

**Fungsi:** Profit & Loss statement report

| Method | Fungsi |
|--------|--------|
| `report` | Generate P&L report |
| `monthly_pl_selling` | AJAX: monthly P&L for selling (domestic vs export breakdown) |

**Key Logic:**
- Aggregates data from Sales, COGS, OPEX, FOH modules
- Uses CTE (Common Table Expressions) in MySQL queries
- Separates domestic (dept 600) and export (dept 700) selling expenses
- Includes newlines data (`yp_plan__trans_budget_entry_data_newlines`)

---

### 6.12 Balance Module (`/balance`)

**Fungsi:** Balance sheet / reclassification

| Method | Fungsi |
|--------|--------|
| `view` | Balance menu view |
| `forecast` | Balance forecast |
| `save_reclass_discount` | Save monthly reclassification data (dept 600/700) |

**Features:** Transaction-based save with insert/update logic per department pair

---

### 6.13 Monitoring Module (`/monitoring`)

**Fungsi:** Budget monitoring & P&L summary overview

| Method | Fungsi |
|--------|--------|
| `index` | Monitoring dashboard - shows all dept budget summary (GA type) |

**Features:**
- Shows current year budget vs previous year
- Grand total row
- Grouped by cost_desc (cost center description)
- View: `pl_summary.php`

---

### 6.14 Approval Module (`/approval`)

**Fungsi:** Budget approval workflow

| Method | Fungsi |
|--------|--------|
| `inbox` | User's approval inbox |

**Note:** Approval flow appears to be basic (submit → approve) tanpa multi-level complex workflow. Detailnya belum fully implemented.

---

### 6.15 User Management Module (`/user`)

**Fungsi:** CRUD users

| Method | Fungsi |
|--------|--------|
| `index` | List semua users |
| `add_user` | Form tambah user |
| `edit_user` | Form edit user |
| `reset_password` | Admin reset password user |
| `sync` | Sync session (set year_code, module_menu) |
| `sync_broadc` | Broadcast session sync |

---

### 6.16 Role Module (`/role`)

**Fungsi:** CRUD roles & menu access mapping

| Method | Fungsi |
|--------|--------|
| `index` | List semua roles |
| `add_new_role` | Tambah role baru + assign menu access |
| `edit_role` | Edit role + update menu mapping |

---

### 6.17 Menu Module (`/menu`)

**Fungsi:** CRUD menu items (sidebar navigation)

| Method | Fungsi |
|--------|--------|
| `index` | List semua menu |
| `add_new_menu` | Tambah menu baru |
| `edit_menu` | Edit menu |

---

### 6.18 Summary Module (`/summary`)

**Fungsi:** Combined budget summary views

---

### 6.19 Dashboard Module (`/dashboard`)

**Fungsi:** Dashboard visualizations

---

### 6.20 COGM Module (`/cogm`)

**Fungsi:** Cost of Goods Manufactured

---

### 6.21 Manpower Module (`/manpower`)

**Fungsi:** Extended manpower analysis (terpisah dari MPP)

---

## 7. Data Flow & Business Logic

### 7.1 Annual Budget Cycle

```
1. Admin opens budget period (Master → Configure Period)
2. Admin sets assumptions (USD rate, inflation, UMK, material costs)
3. Dept heads enter their budgets:
   - Sales team → Sales domestic/export (volume × price)
   - Production → COGS (RM, PM, DLP), FOH
   - All departments → OPEX (GA & Selling)
   - Dept heads → MPP (headcount & salary)
   - Asset owners → CAPEX (with depreciation calc)
4. System auto-calculates:
   - CAPEX depreciation → synced to OPEX
   - COGS totals from sub-components
   - P&L aggregation from all modules
5. Review & Approval
6. Monitoring (budget vs actual)
```

### 7.2 Budget Entry Pattern (Common)

Hampir semua modul budget entry mengikuti pola yang sama:
1. User pilih department/cost center
2. AJAX load table berdasarkan COA + dept + year
3. User mengisi nilai bulanan (Jan-Dec) di spreadsheet-like table
4. Save via POST: loop semua rows, update/insert per row
5. Data disimpan ke tabel transaksi dengan kolom `1`-`12`

### 7.3 Actual Data Upload

- Upload Excel file → parse dengan PhpSpreadsheet
- Match ke master COA + cost center
- Insert/update ke `yp_plan__trans_budget_actual`
- Columns `6`, `7`, `8` digunakan untuk historical data (H-3, H-2, H-1)

### 7.4 Version Control

- Budget versions tracked di `yp_plan__trans_version`
- Linked ke user yang created
- Year-based versioning

---

## 8. Frontend Architecture

### 8.1 Layout Structure

```
main.php (master template)
├── main_header/     → Top navigation bar
├── side_bar/        → Sidebar menu (dynamic based on role)
├── [module view]    → Content area
├── footer/          → Footer
└── sockets/         → JavaScript includes
```

### 8.2 UI Components

- **AdminLTE** template (sidebar layout)
- **DataTables** untuk list/grid views
- **jQuery AJAX** untuk dynamic content loading
- **Select2** untuk searchable dropdowns
- **Bootstrap DatePicker** untuk date inputs
- **Number formatting** (comma-separated thousands di input budget)
- **Spreadsheet-like tables** untuk budget entry (editable cells)

### 8.3 AJAX Pattern

Mayoritas interaksi menggunakan AJAX POST:
```javascript
$.ajax({
    url: base_url + 'module/method',
    type: 'POST',
    data: { param1: value1, dept: dept_id },
    success: function(response) {
        $('#container').html(response);  // atau JSON parse
    }
});
```

Response bisa berupa HTML partial (views) atau JSON data.

---

## 9. Key Patterns to Preserve in CI4

### 9.1 Must-Have Behaviors

1. **Role-based menu access** - dynamic sidebar berdasarkan role user
2. **Department-based data filtering** - user hanya lihat data dept yang di-assign
3. **Monthly budget grid** - entry format 12 kolom (Jan-Dec)
4. **Multi-year comparison** - current year vs previous year(s)
5. **Excel upload/download** - import data dari Excel, export reports
6. **Concurrent access lock** - prevent multiple editors di halaman yang sama
7. **Budget period open/close** - admin control kapan entry dibuka/ditutup
8. **CAPEX → OPEX sync** - depreciation dari CAPEX otomatis masuk ke OPEX

### 9.2 Things to Improve in CI4

| Area | Masalah di CI3 | Rekomendasi CI4 |
|------|----------------|-----------------|
| SQL | Raw SQL queries everywhere (SQL injection risk) | Use Query Builder / Prepared statements |
| Auth | MD5+salt password hashing | Use `password_hash()` / bcrypt |
| Validation | Minimal server-side validation | CI4 Validation library + Request class |
| Architecture | Fat controllers (1000+ lines) | Thin controllers + Service layer + Repository pattern |
| Error Handling | Suppressed errors (`@`, `error_reporting(0)`) | Proper exception handling |
| API | Mixed HTML/JSON responses | Proper REST API with ResponseInterface |
| Session | Global session for everything | Proper DTO/ValueObjects |
| Config | Hardcoded values | Environment-based config (.env) |
| Testing | None | PHPUnit + feature tests |

### 9.3 Suggested CI4 Module Mapping

```
app/
├── Config/
│   ├── Routes.php
│   ├── Database.php (multi-DB support via .env)
│   └── Budget.php (custom config: assumptions, parameters)
├── Controllers/
│   ├── Home.php
│   ├── Auth/
│   │   ├── LoginController.php
│   │   └── PasswordController.php
│   ├── Admin/
│   │   ├── UserController.php
│   │   ├── RoleController.php
│   │   └── MenuController.php
│   ├── Master/
│   │   ├── ProductController.php
│   │   ├── CoaController.php
│   │   ├── CostCenterController.php
│   │   ├── AssumptionController.php
│   │   └── PeriodController.php
│   ├── Budget/
│   │   ├── OpexGaController.php
│   │   ├── OpexSellingController.php
│   │   ├── FohController.php
│   │   ├── CapexController.php
│   │   └── MppController.php
│   ├── Sales/
│   │   ├── DomesticController.php
│   │   └── ExportController.php
│   ├── Cogs/
│   │   ├── RawMaterialController.php
│   │   ├── PackagingController.php
│   │   └── DirectLaborController.php
│   ├── Report/
│   │   ├── PlController.php
│   │   ├── MonitoringController.php
│   │   └── BalanceController.php
│   └── Approval/
│       └── InboxController.php
├── Models/
│   ├── UserModel.php
│   ├── RoleModel.php
│   ├── MenuModel.php
│   ├── BudgetEntryModel.php
│   ├── BudgetActualModel.php
│   ├── CapexModel.php
│   ├── SalesDomesticModel.php
│   ├── SalesExportModel.php
│   ├── MppModel.php
│   ├── CogsAdjustmentModel.php
│   └── ...
├── Services/
│   ├── AuthService.php
│   ├── BudgetCalculationService.php
│   ├── DepreciationService.php
│   ├── ExcelImportService.php
│   ├── ExcelExportService.php
│   ├── MenuService.php
│   └── AccessControlService.php
├── Filters/
│   ├── AuthFilter.php (replace isLogin check)
│   ├── RoleFilter.php (menu access check)
│   └── AccessLockFilter.php (concurrent access)
├── Libraries/
│   └── SpreadsheetHelper.php
└── Views/
    ├── layouts/ (master template)
    ├── auth/
    ├── admin/
    ├── master/
    ├── budget/
    ├── sales/
    ├── cogs/
    ├── reports/
    └── components/ (reusable partials)
```

---

## 10. Database Connections

### 10.1 Connection Groups

| Group | Database | Fungsi |
|-------|----------|--------|
| `default` | `yp_budget_system` | Database utama (budget data + system) |
| `yp_budget_system` | `yp_budget_system` | Alias untuk default |
| `gw_personalia_nonstaff` | `yp_budget_system` | Personalia data (employee info) |

### 10.2 Multi-Database via XML

Apps mendukung koneksi ke database berbeda via XML config:
- `Access_Config.xml` → mapping connection name ke database names
- `DB_config.xml` → actual database credentials (encrypted password)

Di CI4: bisa dihandle via `.env` file atau database config groups tanpa XML.

---

## 11. Email Configuration

- Protocol: SMTP
- Port: 25
- Host: 192.168.92.99 (internal mail server)
- From: no-reply@glicowings.com
- Used for: Password recovery emails

---

## 12. Important Notes untuk Migration

### 12.1 Data yang Critical

- Semua tabel `gw_sm__*` (system/auth)
- Semua tabel `gw_plan__master_*` (master data)
- Semua tabel `yp_plan__trans_*` (transaction/budget data)
- Table `ci_sessions` (bisa dihapus, CI4 handles differently)

### 12.2 Business Rules yang Harus Dipertahankan

1. Budget entry hanya bisa dilakukan saat period **open**
2. User hanya bisa entry untuk department yang di-assign
3. CAPEX depreciation calculation logic (accumulated monthly)
4. Sales summary harus bisa breakdown per region/country/product
5. COGS calculation chain: RM + PM + DLP + adjustments
6. P&L harus aggregate dari semua module
7. Concurrent access lock per page per year
8. Multi-year data comparison

### 12.3 Yang Bisa Di-skip/Simplify

- XML-based database config → gunakan .env di CI4
- Manual MD5 password hashing → `password_hash()`
- Custom encrypt/decrypt logic → CI4 Encryption service
- Access_Config.xml / DB_config.xml → tidak perlu jika single environment
- `sales_21072025` dan `sales_28072025` modules → ini backup/versioning lama, skip

### 12.4 Session Data yang Dipakai

| Key | Isi |
|-----|-----|
| `user_id` | ID user yang login |
| `user_name` | Nama user |
| `user_username` | NIK/username |
| `user_connection` | Active connection name |
| `year_code` | Tahun budget active |
| `module_menu` | Active module code |
| `module_alias` | Module display name |
| `admin_users` | Flag admin (kosong = bukan admin) |
| `auth_obj` | Array authorization objects (dept access) |
| `set_glicowings_db` | Dynamic DB config (glicowings) |
| `set_personalia_db` | Dynamic DB config (personalia) |
| `landing` | Landing page flag |

---

## 13. URL Routes Summary

| URL | Module/Method | Keterangan |
|-----|--------------|------------|
| `/` | home/index | Dashboard |
| `/portal` | home/portal | Portal page |
| `/login` | home/login_new | Login |
| `/master/product` | master/product | Master Product |
| `/master/cost-center` | master/cost_center | Master Cost Center |
| `/master/coa` | master/coa | Chart of Accounts |
| `/master/assumption` | master/assumption | Budget Assumptions |
| `/master/mpp` | master/mpp | Master MPP |
| `/configure-period` | master/period | Period Config |
| `/opex-ga/entry-budget` | opex_ga/entry | OPEX GA Entry |
| `/opex-ga/actual-budget` | opex_ga/actual | OPEX GA Actual |
| `/opex-ga/report-department` | opex_ga/report_department | OPEX GA Report |
| `/report-grand-opex` | opex_ga/report_combine | Grand OPEX Report |
| `/opex-selling/entry-budget` | opex_selling/entry | OPEX Selling Entry |
| `/opex-selling/actual-budget` | opex_selling/actual | OPEX Selling Actual |
| `/opex-selling/report-department` | opex_selling/report_department | OPEX Selling Report |
| `/foh/entry-budget` | foh/entry | FOH Entry |
| `/foh/actual-budget` | foh/actual | FOH Actual |
| `/foh-summary` | foh/summary | FOH Summary |
| `/capex/entry` | capex/entry | CAPEX Entry |
| `/capex-summary` | capex/summary | CAPEX Summary |
| `/capex-report` | capex/report | CAPEX Report |
| `/mpp-entry` | mpp/entry | MPP Entry |
| `/mpp-summary` | mpp/summary | MPP Summary |
| `/sales-domestic/entry` | sales/entry_domestic | Sales Domestic |
| `/sales-export/entry` | sales/entry_export | Sales Export |
| `/sales-data` | sales/summary | Sales Summary |
| `/cogs-rm` | cogs/rm | COGS Raw Material |
| `/cogs-pm` | cogs/pm | COGS Packaging |
| `/cogs-dlp` | cogs/dlp | COGS Direct Labor |
| `/cogs-total-dlp` | cogs/total_dlp | COGS Total DLP |
| `/cogs-summary` | cogs/summary | COGS Summary |
| `/cogs-yti` | cogs/yti | COGS YTI |
| `/others` | cogs/other | Other COGS |
| `/mogul-actual` | mogul/actual | DL Mogul Actual |
| `/mogul-budget` | mogul/entry | DL Mogul Budget |
| `/report-profit-loss` | pl/report | P&L Report |
| `/balance-menu` | balance/view | Balance Menu |
| `/forecast` | balance/forecast | Forecast |
| `/monitoring` | monitoring | Monitoring |
| `/inbox` | approval/inbox | Approval Inbox |

---

## 14. Quick Reference: COA Types & Account Ranges

| Type | Prefix/Range | Modul |
|------|-------------|-------|
| GA | `7710xxx` | OPEX G&A |
| SELLING | `7700xxx` | OPEX Selling |
| FOH | `6605xxx` | Factory Overhead |
| CAPEX | (via master_amount_depreciation) | CAPEX |

---

*Document generated: August 2026*
*Source: YP Budget System CI3 codebase analysis*
