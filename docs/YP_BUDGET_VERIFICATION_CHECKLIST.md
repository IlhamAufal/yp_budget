# YP Budget System - Functional Verification Checklist

> Dokumen ini digunakan untuk **verifikasi fungsional** apps CI4 hasil migrasi.
> Setiap item harus dicek apakah behavior-nya sudah match dengan apps CI3 yang asli.
> Gunakan status: `[ ]` (belum), `[x]` (sudah sesuai), `[!]` (ada perbedaan/issue)

---

## Cara Pakai

1. Jalankan apps CI4 migration
2. Test setiap section di bawah sesuai urutan
3. Beri tanda checklist sesuai hasil
4. Jika ada perbedaan, catat di kolom "Notes"

---

## 1. Authentication & Session

### 1.1 Login

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 1.1.1 | Akses halaman tanpa login | Redirect ke `/login` atau `/portal` | [ ] | |
| 1.1.2 | Login dengan username + password valid | Masuk ke dashboard, session terbentuk | [ ] | |
| 1.1.3 | Login dengan password salah | Error message ditampilkan, tetap di halaman login | [ ] | |
| 1.1.4 | Login dengan username tidak terdaftar | Error message "user not found" atau sejenisnya | [ ] | |
| 1.1.5 | Session expired (idle 2 jam) | Auto redirect ke login | [ ] | |
| 1.1.6 | Akses halaman setelah login | Sidebar menu tampil sesuai role user | [ ] | |

### 1.2 Logout

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 1.2.1 | Klik logout | Session destroyed, redirect ke login/portal | [ ] | |
| 1.2.2 | Akses URL protected setelah logout | Redirect ke login | [ ] | |

### 1.3 Password Management

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 1.3.1 | Ganti password (valid) | Password berubah, redirect ke home dengan param=true | [ ] | |
| 1.3.2 | Ganti password (kosong) | Redirect dengan param=false, password tidak berubah | [ ] | |
| 1.3.3 | Forgot password - kirim email reset | Email terkirim dengan link token | [ ] | |
| 1.3.4 | Reset password - token valid | Form reset tampil, bisa set password baru | [ ] | |
| 1.3.5 | Reset password - token expired | Error "Expired Token" | [ ] | |
| 1.3.6 | Reset password - token sudah dipakai | Error "Token already use" | [ ] | |
| 1.3.7 | Reset password - token invalid | Error "Token not found" / "Invalid Token" | [ ] | |

### 1.4 Registration

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 1.4.1 | Register user baru dengan NIK valid | User terdaftar, email konfirmasi terkirim | [ ] | |
| 1.4.2 | Register dengan NIK tidak ditemukan | Error/rejected | [ ] | |

---

## 2. Role-Based Access Control

### 2.1 Menu Access

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 2.1.1 | Login sebagai admin (role 1/2) | Melihat semua menu | [ ] | |
| 2.1.2 | Login sebagai user biasa | Hanya melihat menu sesuai role | [ ] | |
| 2.1.3 | Akses URL menu yang tidak di-assign | Redirect ke home (unauthorized) | [ ] | |
| 2.1.4 | Sidebar menu multi-level | Parent > Child menu ter-render dengan benar | [ ] | |
| 2.1.5 | Active menu highlight | Menu yang sedang dibuka ter-highlight | [ ] | |

### 2.2 Data Authorization

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 2.2.1 | User dengan dept tertentu | Hanya lihat data department yang di-assign | [ ] | |
| 2.2.2 | User dengan auth_obj `*` (all) | Bisa lihat semua department | [ ] | |
| 2.2.3 | User tanpa role profile | Kick out / redirect ke login | [ ] | |

### 2.3 Concurrent Access Lock

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 2.3.1 | User A buka halaman entry | validasi = "OK", bisa edit | [ ] | |
| 2.3.2 | User B buka halaman yang sama (tahun sama) | validasi = "NOPE", warning ditampilkan | [ ] | |
| 2.3.3 | User A logout/pindah halaman | Lock terelease, User B bisa masuk | [ ] | |

---

## 3. Master Data Management

### 3.1 Master Product

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 3.1.1 | View list master product | Tampil semua produk aktif sesuai year | [ ] | |
| 3.1.2 | Filter by year | Data berubah sesuai tahun yang dipilih | [ ] | |
| 3.1.3 | Tambah product baru | Data tersimpan ke master product | [ ] | |
| 3.1.4 | Edit product | Data terupdate | [ ] | |
| 3.1.5 | Upload master product via Excel | Data ter-import dengan benar | [ ] | |

### 3.2 Master Cost Center

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 3.2.1 | View list cost center | Tampil semua cost center | [ ] | |
| 3.2.2 | Mapping cost_center & cost_center_sap | Dual identifier berfungsi | [ ] | |
| 3.2.3 | Upload master cost center via Excel | Data ter-import | [ ] | |

### 3.3 Master COA (Chart of Accounts)

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 3.3.1 | View COA list | Tampil grouped by type (GA/SELLING/FOH) | [ ] | |
| 3.3.2 | COA mapping ke cost_center_header | Header grouping benar | [ ] | |

### 3.4 Assumptions Setup

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 3.4.1 | Set USD exchange rate | Tersimpan per year | [ ] | |
| 3.4.2 | Set inflation rate | Tersimpan per year | [ ] | |
| 3.4.3 | Set UMK/UMP wages | Tersimpan per year | [ ] | |
| 3.4.4 | Set material cost assumptions | Tersimpan (gelatine, sugar, raw, etc.) | [ ] | |
| 3.4.5 | Upload assumptions via template | Data ter-import per category | [ ] | |

### 3.5 Configure Period

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 3.5.1 | Open budget period | Period status = OPEN, users bisa entry | [ ] | |
| 3.5.2 | Close budget period | Period status = CLOSED, entry ter-block | [ ] | |
| 3.5.3 | Entry saat period closed | User tidak bisa save (reject/warning) | [ ] | |

### 3.6 Formula & Mapping

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 3.6.1 | View/edit formula | Formula tersimpan dan terapply | [ ] | |
| 3.6.2 | Product mapping ke category | Mapping tersimpan | [ ] | |
| 3.6.3 | Ratio target setup | Target per product tersimpan | [ ] | |
| 3.6.4 | Production capacity setup | Capacity per SKU tersimpan | [ ] | |

---

## 4. OPEX G&A Module

### 4.1 Budget Entry

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 4.1.1 | Load entry page | Dropdown department tampil sesuai auth | [ ] | |
| 4.1.2 | Select department → load table | AJAX load tabel COA dengan data existing | [ ] | |
| 4.1.3 | Input nilai bulanan (Jan-Dec) | Total auto-calculate | [ ] | |
| 4.1.4 | Save budget entry | Data tersimpan per row (12 bulan + total) | [ ] | |
| 4.1.5 | Entry newlines (sub-items) | Sub-items tersimpan terpisah | [ ] | |
| 4.1.6 | Detail items CRUD | Add/edit/delete detail line items | [ ] | |
| 4.1.7 | Validasi: only assigned dept | User tidak bisa entry dept lain | [ ] | |

### 4.2 Actual Budget Upload

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 4.2.1 | Upload Excel actual data | Data ter-parse dan tersimpan | [ ] | |
| 4.2.2 | Data actual muncul di entry page | Kolom actual/historical terisi | [ ] | |

### 4.3 Report

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 4.3.1 | Report per department | Data sesuai dept yang dipilih | [ ] | |
| 4.3.2 | Report combine (grand total) | Aggregasi semua dept benar | [ ] | |
| 4.3.3 | Total per OPEX category | AJAX load summary per category | [ ] | |
| 4.3.4 | Export to Excel | File terdownload dengan format benar | [ ] | |

---

## 5. OPEX Selling Module

### 5.1 Budget Entry

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 5.1.1 | Load entry page | Tampil COA type SELLING | [ ] | |
| 5.1.2 | Select department → load table | Data per COA selling muncul | [ ] | |
| 5.1.3 | Input & save budget | 12 bulan tersimpan | [ ] | |
| 5.1.4 | Entry newlines | Sub-items selling tersimpan | [ ] | |

### 5.2 Actual & Report

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 5.2.1 | Upload actual selling data | Data ter-import | [ ] | |
| 5.2.2 | Report per department | Data filtered per dept | [ ] | |
| 5.2.3 | COA header grouping benar | Group sesuai cost_center_header | [ ] | |

---

## 6. FOH (Factory Overhead) Module

### 6.1 Budget Entry

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 6.1.1 | Load FOH entry | COA type FOH tampil | [ ] | |
| 6.1.2 | Select cost center → load table | Data per COA 6605xxx muncul | [ ] | |
| 6.1.3 | Input & save budget | Data tersimpan per bulan | [ ] | |
| 6.1.4 | Newlines entry | Sub cost center entries tersimpan | [ ] | |

### 6.2 Summary & Report

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 6.2.1 | FOH summary view | Total per cost center header benar | [ ] | |
| 6.2.2 | Summary by account | Aggregasi per account OK | [ ] | |
| 6.2.3 | Summary by cost center | Aggregasi per cost center OK | [ ] | |
| 6.2.4 | Report combine | Grand total semua dept benar | [ ] | |
| 6.2.5 | Export Excel | File download dengan data benar | [ ] | |

---

## 7. CAPEX Module

### 7.1 Entry

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 7.1.1 | Entry CAPEX item baru | Header + detail tersimpan | [ ] | |
| 7.1.2 | Input monthly investment values | 12 bulan terisi | [ ] | |
| 7.1.3 | Edit existing CAPEX | Data terupdate | [ ] | |
| 7.1.4 | Delete CAPEX item | Item terhapus | [ ] | |

### 7.2 Depreciation & Sync

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 7.2.1 | Depreciation calculation | Monthly accumulated depreciation benar | [ ] | |
| 7.2.2 | Sync to OPEX | Depreciation values masuk ke OPEX table | [ ] | |
| 7.2.3 | Existing actual + new CAPEX | Combined calculation (actual base + new additions) | [ ] | |

### 7.3 Report

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 7.3.1 | CAPEX report per department | Filtered per dept, total benar | [ ] | |
| 7.3.2 | CAPEX total report | Aggregasi semua dept | [ ] | |
| 7.3.3 | CAPEX summary | Overview per category | [ ] | |

---

## 8. MPP (Manpower Planning) Module

### 8.1 Entry

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 8.1.1 | Load MPP entry | Tampil sesuai dept yang di-assign | [ ] | |
| 8.1.2 | Entry headcount per position | Data tersimpan per bulan | [ ] | |
| 8.1.3 | Entry salary breakdown | Salary data tersimpan | [ ] | |
| 8.1.4 | Dual identifier (cost_center / cost_center_sap) | Kedua ID resolve ke data yang sama | [ ] | |
| 8.1.5 | Save MPP | Semua data tersimpan | [ ] | |

### 8.2 Summary

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 8.2.1 | MPP summary per dept | Headcount + salary total benar | [ ] | |
| 8.2.2 | Grand total MPP | Aggregasi all dept benar | [ ] | |

---

## 9. Sales Module

### 9.1 Sales Domestic

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 9.1.1 | Entry sales domestic | Form per region/product tampil | [ ] | |
| 9.1.2 | Input qty & revenue per bulan | Data tersimpan (jan_qty, jan_rev, ... dec_qty, dec_rev) | [ ] | |
| 9.1.3 | Filter by region | Data filtered per region | [ ] | |
| 9.1.4 | Upload domestic data via Excel | Data ter-import ke table | [ ] | |
| 9.1.5 | Download Excel template | Template terdownload | [ ] | |

### 9.2 Sales Export

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 9.2.1 | Entry sales export | Form per country/product tampil | [ ] | |
| 9.2.2 | Multi-currency support | Currency field tersimpan per record | [ ] | |
| 9.2.3 | Filter by country | AJAX return data per country | [ ] | |
| 9.2.4 | Filter by region | AJAX return summary per region | [ ] | |
| 9.2.5 | Upload export data via Excel | Data ter-import | [ ] | |

### 9.3 Sales Summary & Reports

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 9.3.1 | Summary all sales | Domestic + Export aggregated | [ ] | |
| 9.3.2 | Summary by country | Grouped data benar | [ ] | |
| 9.3.3 | Summary regional | Regional aggregation benar | [ ] | |
| 9.3.4 | Summary export in IDR | Currency conversion applied | [ ] | |
| 9.3.5 | Export Excel report | File tergenerate dengan benar | [ ] | |

### 9.4 Sales Simulation

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 9.4.1 | Simulation page load | Form/interface tampil | [ ] | |
| 9.4.2 | Adjust volume | Recalculation sesuai volume baru | [ ] | |
| 9.4.3 | Adjust ASP (Average Selling Price) | Revenue recalculated | [ ] | |

---

## 10. COGS Module

### 10.1 Raw Material (RM)

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 10.1.1 | View RM data | Data per product tampil | [ ] | |
| 10.1.2 | Calculation: volume x material rate | Hasil perhitungan benar | [ ] | |
| 10.1.3 | Upload RM data via Excel | Data ter-import | [ ] | |
| 10.1.4 | RM adjustments | Adjustment domestic/international applied | [ ] | |

### 10.2 Packaging Material (PM)

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 10.2.1 | View PM data | Data per product tampil | [ ] | |
| 10.2.2 | PM components: blister, box, display, PP, VBS, others | Semua komponen terhitung | [ ] | |
| 10.2.3 | PM percentage parameters | Master percentages ter-apply ke calculation | [ ] | |
| 10.2.4 | Upload PM data | Data ter-import | [ ] | |
| 10.2.5 | PM adjustments | Adjustment values applied | [ ] | |

### 10.3 Direct Labor Process (DLP)

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 10.3.1 | View DLP data | Data tampil per dept | [ ] | |
| 10.3.2 | DLP calculation | Hours x rate = cost | [ ] | |
| 10.3.3 | Total DLP aggregation | Sum all DLP benar | [ ] | |
| 10.3.4 | Upload DLP data | Import via Excel berfungsi | [ ] | |

### 10.4 COGS Summary & Others

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 10.4.1 | COGS summary | RM + PM + DLP + Others = Total COGS | [ ] | |
| 10.4.2 | COGS YTI | Year-to-date indicator tampil | [ ] | |
| 10.4.3 | Other COGS items | Entry & view berfungsi | [ ] | |
| 10.4.4 | COGS adjustments per channel | DOMES & INTR adjustment values applied | [ ] | |
| 10.4.5 | Export COGS to Excel | File terdownload per category | [ ] | |

---

## 11. DL Mogul Module

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 11.1 | Entry budget DL Mogul | Data per dept tersimpan (12 bulan) | [ ] | |
| 11.2 | Upload actual Mogul | Data actual ter-import | [ ] | |
| 11.3 | Report per department | Filter & aggregasi benar | [ ] | |
| 11.4 | Export Excel | File terdownload | [ ] | |

---

## 12. P&L (Profit & Loss) Module

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 12.1 | P&L report generation | Data ter-aggregate dari semua modul | [ ] | |
| 12.2 | Revenue line = Sales data | Nilai cocok dengan total sales module | [ ] | |
| 12.3 | COGS line = COGS summary | Nilai cocok dengan COGS module | [ ] | |
| 12.4 | OPEX lines = OPEX GA + Selling | Nilai cocok | [ ] | |
| 12.5 | FOH line = FOH summary | Nilai cocok | [ ] | |
| 12.6 | Depreciation line = CAPEX calc | Nilai cocok | [ ] | |
| 12.7 | Monthly P&L selling (domestic vs export) | Breakdown per dept 600/700 benar | [ ] | |
| 12.8 | Newlines data included | yp_plan__trans_budget_entry_data_newlines data masuk | [ ] | |
| 12.9 | Net profit calculation | Revenue - COGS - OPEX - FOH - Depreciation = Net | [ ] | |

---

## 13. Balance & Forecast Module

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 13.1 | Balance menu view | Page tampil | [ ] | |
| 13.2 | Forecast data entry | Data tersimpan | [ ] | |
| 13.3 | Reclassification (dept 600 & 700) | Monthly reclass tersimpan per dept | [ ] | |
| 13.4 | Insert/update logic | Existing: update, New: insert | [ ] | |
| 13.5 | Pair constraint (600 & 700) | Jika keduanya ada, hanya update (no new insert) | [ ] | |

---

## 14. Monitoring Module

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 14.1 | Monitoring dashboard load | All dept budget summary tampil | [ ] | |
| 14.2 | Current year data | Data sesuai year_code di session | [ ] | |
| 14.3 | Previous year comparison | Data tahun sebelumnya tampil | [ ] | |
| 14.4 | Grand total row | Sum semua dept benar | [ ] | |
| 14.5 | Tags (created_by user) | Nama user yang entry tampil | [ ] | |

---

## 15. Approval Module

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 15.1 | Inbox page load | List pending approvals tampil | [ ] | |
| 15.2 | Approve budget | Status berubah, data di-lock | [ ] | |
| 15.3 | Reject budget | Status berubah, bisa re-entry | [ ] | |

---

## 16. User Management (Admin)

### 16.1 User CRUD

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 16.1.1 | List all users | Semua user tampil | [ ] | |
| 16.1.2 | Add user baru | User tersimpan dengan salt + hashed password | [ ] | |
| 16.1.3 | Edit user | Data terupdate | [ ] | |
| 16.1.4 | Admin reset password | Password user ter-reset | [ ] | |
| 16.1.5 | Sync year/module | Session year_code berubah | [ ] | |

### 16.2 Role CRUD

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 16.2.1 | List all roles | Roles tampil | [ ] | |
| 16.2.2 | Add role + assign menu | Role + rolemenu mapping tersimpan | [ ] | |
| 16.2.3 | Edit role + update menu | Mapping terupdate | [ ] | |
| 16.2.4 | Role type: menu vs objek | Tipe berbeda, fungsi berbeda | [ ] | |

### 16.3 Menu CRUD

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 16.3.1 | List all menus | Semua menu tampil | [ ] | |
| 16.3.2 | Add menu (parent) | Menu parent tersimpan | [ ] | |
| 16.3.3 | Add menu (child) | Child + structure mapping tersimpan | [ ] | |
| 16.3.4 | Edit menu | Data terupdate | [ ] | |
| 16.3.5 | Menu order | Urutan sesuai `menu_order` | [ ] | |

---

## 17. Cross-Module Calculations (Integration Tests)

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 17.1 | CAPEX depreciation → appears in OPEX | After sync_to_opex, depreciation muncul di OPEX entry | [ ] | |
| 17.2 | Sales volume → feeds into COGS RM | Volume sales jadi basis perhitungan RM | [ ] | |
| 17.3 | Sales volume → feeds into COGS PM | Volume sales jadi basis perhitungan PM | [ ] | |
| 17.4 | Assumption USD rate → applied in Sales Export | Conversion rate terpakai di summary IDR | [ ] | |
| 17.5 | MPP salary → reflected in P&L | Manpower cost muncul di P&L | [ ] | |
| 17.6 | All modules → aggregate to P&L | Total P&L = sum of all module outputs | [ ] | |
| 17.7 | Period close → blocks all entry modules | Semua save function reject saat period closed | [ ] | |
| 17.8 | Year change (sync year) → all modules switch | Semua data load sesuai year baru | [ ] | |

---

## 18. Excel Import/Export

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 18.1 | Download template → upload → data match | Round-trip Excel tanpa data loss | [ ] | |
| 18.2 | Upload with invalid format | Error message, no data corrupted | [ ] | |
| 18.3 | Upload with empty rows | Skipped gracefully | [ ] | |
| 18.4 | Export report → values match UI | Data di Excel = data di screen | [ ] | |
| 18.5 | Large dataset upload (500+ rows) | Performance acceptable (<30s) | [ ] | |

---

## 19. UI/UX Verification

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 19.1 | Sidebar menu render | Multi-level menu correct | [ ] | |
| 19.2 | Budget entry table | Editable cells, number format (comma separator) | [ ] | |
| 19.3 | Auto-calculate totals | Row total & column total real-time | [ ] | |
| 19.4 | Dropdown filters (dept, year, etc.) | Options sesuai auth & data | [ ] | |
| 19.5 | AJAX loading indicators | User tahu proses sedang berjalan | [ ] | |
| 19.6 | Error messages | Informatif, tidak blank page | [ ] | |
| 19.7 | Number input format | Accept comma-separated, save as plain number | [ ] | |
| 19.8 | Responsive / mobile (optional) | Minimal desktop berfungsi penuh | [ ] | |

---

## 20. Data Integrity Checks

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|----------------|--------|-------|
| 20.1 | Sum kolom 1-12 = total | Setiap row: Jan+Feb+...+Dec = Total field | [ ] | |
| 20.2 | Budget vs Actual alignment | ID COA & dept sama antara budget & actual | [ ] | |
| 20.3 | No orphan records | Semua FK reference valid (dept, COA, user) | [ ] | |
| 20.4 | Year isolation | Data tahun X tidak tercampur tahun Y | [ ] | |
| 20.5 | Concurrent save | Dua user save bersamaan: no data corruption | [ ] | |
| 20.6 | Decimal precision | Angka desimal tidak kehilangan precision | [ ] | |

---

## 21. Performance Benchmarks

| # | Scenario | CI3 Baseline | CI4 Result | Status | Notes |
|---|----------|-------------|------------|--------|-------|
| 21.1 | Login response time | < 2s | | [ ] | |
| 21.2 | Dashboard load | < 3s | | [ ] | |
| 21.3 | Budget entry table load (AJAX) | < 2s | | [ ] | |
| 21.4 | Save budget (20 rows) | < 3s | | [ ] | |
| 21.5 | P&L report generation | < 5s | | [ ] | |
| 21.6 | Excel export (large) | < 10s | | [ ] | |
| 21.7 | Sales summary all | < 5s | | [ ] | |

---

## Summary Scoring

| Section | Total Items | Passed | Failed | Skipped |
|---------|-------------|--------|--------|---------|
| 1. Auth & Session | 15 | | | |
| 2. RBAC | 8 | | | |
| 3. Master Data | 17 | | | |
| 4. OPEX GA | 10 | | | |
| 5. OPEX Selling | 7 | | | |
| 6. FOH | 9 | | | |
| 7. CAPEX | 10 | | | |
| 8. MPP | 7 | | | |
| 9. Sales | 17 | | | |
| 10. COGS | 14 | | | |
| 11. DL Mogul | 4 | | | |
| 12. P&L | 9 | | | |
| 13. Balance | 5 | | | |
| 14. Monitoring | 5 | | | |
| 15. Approval | 3 | | | |
| 16. Admin (User/Role/Menu) | 14 | | | |
| 17. Cross-Module | 8 | | | |
| 18. Excel Import/Export | 5 | | | |
| 19. UI/UX | 8 | | | |
| 20. Data Integrity | 6 | | | |
| 21. Performance | 7 | | | |
| **TOTAL** | **~188** | | | |

---

## Notes & Known Differences

Gunakan section ini untuk mencatat perbedaan yang disengaja antara CI3 dan CI4:

| # | Perbedaan | Alasan | Impact |
|---|-----------|--------|--------|
| 1 | Password hashing: MD5+salt → bcrypt | Security improvement | Password lama perlu migration script |
| 2 | XML DB config → .env based | CI4 standard practice | Tidak perlu Access_Config.xml |
| 3 | Session driver: CI3 table → CI4 table | Different schema | Perlu create tabel session CI4 |
| 4 | HMVC modules → CI4 namespace/folders | Framework difference | URL routing mungkin sedikit berbeda |
| 5 | Raw SQL → Query Builder/Model | Code quality improvement | Behavior harus sama, syntax berbeda |

---

*Verification Checklist v1.0 - August 2026*
*Based on: YP Budget System CI3 analysis*
