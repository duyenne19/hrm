# 📋 PHÂN CÔNG QUẢN LÝ & BẢO TRÌ HỆ THỐNG HRM - 4 DEVELOPERS

```
┌─────────────────────────────────────────────────────────────────┐
│ Mục đích : Quản lý, maintain, fix bugs, tối ưu hệ thống hiện tại │
│ Thời gian : Ongoing (không thời hạn)                             │
│ Phương pháp: Chia công việc theo chức năng hiện tại              │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🚀 QUICK ASSIGNMENT OVERVIEW

| # | Developer | Vai trò | Chức năng chính |
|---|-----------|---------|-----------------|
| **1** | 👨‍💼 Backend | Database Admin | Quản lý DB, optimization, API |
| **2** | 👨‍💼 Security | Security Manager | Login, users, permissions, audit |
| **3** | 👨‍💼 Employee | Data Manager | Employee CRUD, forms, catalogs |
| **4** | 👨‍💼 Payroll | Reports Specialist | Salary, calculations, reports |

---

# 👨‍💻 DEV 1: BACKEND & DATABASE ADMINISTRATOR

### 📌 Mô tả công việc
Quản lý backend logic, database optimization, API performance, queries

### ✅ Công việc hàng ngày (Daily Checklist)
```
☐ Kiểm tra database health (size, performance, slow queries)
☐ Monitor error logs & exceptions
☐ Backup database định kỳ
☐ Check API response times & bottlenecks
☐ Monitor disk space & resources
```

### 📂 Quản lý Files & Codebase

#### Models (Maintain & Optimize):
```
✓ /models/NhanVien.php         → Tối ưu queries, fix bugs
✓ /models/Luong.php            → Logic tính lương, calculations
✓ /models/ChinhLuong.php       → Lịch sử chỉnh lương
✓ /models/TongQuan.php         → Dashboard data
✓ /models/PhongBan.php         → Department data
✓ /models/ChucVu.php           → Position data  
✓ /models/TrinhDo.php & khác   → Catalog models
```

#### Action Files (Backend Logic):
```
✓ /action/nhan-vien-action.php         → CRUD nhân viên
✓ /action/luong-action.php             → Tính lương operations
✓ /action/chinh-luong-action.php       → Chỉnh lương operations
✓ /action/export_excel_nhanvien.php    → Export employee data
✓ /action/export_excel_luong.php       → Export payroll data
✓ /action/print_luong.php              → Print salary
✓ /action/fetch-*.php                  → AJAX endpoints
```

#### Configuration:
```
✓ /connection/config.php    → Database connection, performance tuning
```

### 🔧 Tasks thường xuyên

#### 1️⃣ Kiểm tra & Fix SQL Errors
- Xem error messages & debug logs
- Fix broken queries
- Optimize slow running queries (EXPLAIN ANALYZE)
- Monitor query execution time

#### 2️⃣ Database Maintenance
- Backup hàng ngày (automated)
- Optimize tables & indexes
- Check primary/foreign keys
- Fix data inconsistencies
- Archive old records

#### 3️⃣ Performance Tuning
- Check response times (target: < 2 seconds)
- Identify bottlenecks (slow queries, N+1)
- Add indexes for frequent searches
- Implement caching if needed
- Monitor memory & CPU usage

#### 4️⃣ Code Review Backend
- Review pull requests từ dev khác
- Test database operations
- Ensure data integrity
- Check error handling

---

# 👨‍💻 DEV 2: SECURITY & AUTHENTICATION MANAGER

### 📌 Mô tả công việc
Bảo mật hệ thống, xác thực, phân quyền, audit logs

### ✅ Công việc hàng ngày (Daily Checklist)
```
☐ Monitor login attempts & failed logins
☐ Check for suspicious user activities
☐ Review user access logs
☐ Check security alerts
☐ Review user permission changes
```

### 📂 Quản lý Files

#### Authentication & Login:
```
✓ /login.php                  → Trang login UI
✓ /action/login-action.php    → Logic đăng nhập & xác thực
✓ /login2.php                 → Backup login page (nếu có)
```

#### User Management:
```
✓ /models/TaiKhoan.php        → User model & data
✓ /ds-tai-khoan.php           → Danh sách tài khoản admin
✓ /tai-khoan-ca-nhan.php      → User profile page
✓ /action/tai-khoan-action.php → CRUD operations cho users
```

#### Permissions & Access:
```
✓ /layouts/phan-quyen.php     → Permission checking layout
✓ Permission matrix           → Verify role & permissions
```

### 🔧 Tasks thường xuyên

#### 1️⃣ Security Monitoring
- Check login failures (block after 5 attempts)
- Identify suspicious accounts (unusual access)
- Monitor access patterns (unusual times/locations)
- Check unauthorized access attempts
- Review permission changes

#### 2️⃣ User Account Management
- Create new accounts khi có request
- Delete/disable inactive accounts
- Reset passwords for locked users
- Unlock accounts
- Update user roles & permissions
- Archive old user records

#### 3️⃣ Audit & Compliance
- Generate user activity reports
- Review who accessed what & when
- Check for policy violations
- Maintain audit trail
- Ensure data privacy compliance

#### 4️⃣ Security Hardening
- Test for SQL injection vulnerabilities
- Check password policies
- Verify session timeout
- Review CSRF token usage
- Patch security bugs

---

# 👨‍💻 DEV 3: EMPLOYEE DATA & UI MANAGER

### 📌 Mô tả công việc
Dữ liệu nhân viên, giao diện forms, danh mục, data quality

### ✅ Công việc hàng ngày (Daily Checklist)
```
☐ Check employee data consistency
☐ Verify forms working correctly (add/edit/delete)
☐ Monitor data imports/exports
☐ Check for duplicate entries
☐ Verify required fields filled
```

### 📂 Quản lý Files

#### Employee Management Pages:
```
✓ /ds-nhan-vien.php        → Danh sách nhân viên (DataTable)
✓ /them-nhan-vien.php      → Form thêm nhân viên mới
✓ /xem-nhan-vien.php       → Chi tiết nhân viên (view/edit)
✓ /tra-cuu-nhan-vien.php   → Tìm kiếm nâng cao (nếu có)
```

#### Employee Data Models:
```
✓ /models/NhanVien.php             → Employee data (maintain)
✓ /models/NhomNV.php               → Employee groups
✓ /models/ChiTietNhom.php          → Group details
✓ /models/CongTac.php              → Work assignments/travels
✓ /models/KhenThuongKyLuat.php     → Awards & discipline
```

#### Catalog Management (Danh mục):
```
✓ /models/PhongBan.php      → Departments
✓ /models/ChucVu.php        → Positions/Job titles
✓ /models/BangCap.php       → Degrees/Qualifications
✓ /models/ChuyenMon.php     → Specializations
✓ /models/TrinhDo.php       → Education levels
✓ /models/QuocTich.php      → Nationalities
✓ /models/DanToc.php        → Ethnic groups
✓ /models/TonGiao.php       → Religions
✓ /models/HonNhan.php       → Marriage status
✓ /models/LoaiNhanVien.php  → Employee types
```

#### Employee Actions:
```
✓ /action/nhan-vien-action.php         → CRUD operations
✓ /action/export_excel_nhanvien.php    → Export to Excel
```

### 🔧 Tasks thường xuyên

#### 1️⃣ Employee Data Maintenance
- Check for duplicate entries
- Verify all required fields filled
- Fix incomplete/corrupted records
- Validate data accuracy
- Check data types & formats

#### 2️⃣ Form Management & Testing
- Test add/edit/delete employee forms
- Verify form validation works
- Fix form submission errors
- Ensure data saves correctly
- Check file uploads (photos)

#### 3️⃣ Catalog/Master Data Management
- Keep departments list updated
- Maintain positions/job titles
- Update degrees & qualifications
- Keep specializations current
- Archive old catalog entries

#### 4️⃣ Data Quality & Integrity
- Check for missing employee photos
- Verify contact information (email, phone)
- Validate address data
- Check document numbers (ID, passport)
- Ensure no orphaned records

---

# 👨‍💻 DEV 4: PAYROLL & REPORTS SPECIALIST

### 📌 Mô tả công việc
Lương, tính toán, báo cáo, thống kê, phân tích

### ✅ Công việc hàng ngày (Daily Checklist)
```
☐ Monitor monthly payroll status & progress
☐ Check salary calculations accuracy
☐ Verify deductions (tax, insurance, etc)
☐ Monitor payroll processing
☐ Generate daily/weekly reports
```

### 📂 Quản lý Files

#### Payroll Pages:
```
✓ /luong.php                → Salary list & management
✓ /chinh-luong.php          → Salary adjustments & history
✓ /add-luong.php            → Add new salary (nếu có)
```

#### Payroll Models:
```
✓ /models/Luong.php         → Salary data & calculations (chính)
✓ /models/ChinhLuong.php    → Salary adjustments & history
```

#### Payroll Actions:
```
✓ /action/luong-action.php              → Salary CRUD
✓ /action/chinh-luong-action.php        → Adjustment operations
✓ /action/fetch-luong-details.php       → AJAX salary details
✓ /action/fetch-chinh-luong-history.php → AJAX history
✓ /action/export_excel_luong.php        → Export to Excel
✓ /action/print_luong.php               → Print salary slips
```

#### Reports & Analytics:
```
✓ /tong-quan.php           → Main dashboard (payroll metrics)
✓ /thong-ke-luong.php      → Salary statistics
✓ /thong-ke-top.php        → Top earners & rankings
```

### 🔧 Tasks thường xuyên

#### 1️⃣ Monthly Payroll Processing
- Calculate monthly salaries (25-26 each month)
- Verify all salary amounts
- Check deductions correctness:
  - Insurance (BHXH, BHYT, BHTN)
  - Personal income tax (PIT)
  - Other deductions
- Process salary adjustments
- Generate payroll reports

#### 2️⃣ Salary Calculations Verification
- Verify base salary accuracy
- Check allowances applied correctly:
  - Department allowance
  - Position allowance
  - Skill allowance
- Confirm bonuses calculated
- Validate insurance deductions
- Check tax calculations
- Verify net salary = Gross - All Deductions

#### 3️⃣ Salary Adjustments Management
- Process salary increases/decreases
- Update salary when position changes
- Handle promotions & job transfers
- Track salary change history
- Maintain salary audit trail

#### 4️⃣ Reports & Analysis Generation
- Generate monthly payroll report
- Create salary statistics
- Analyze salary trends (compare months/quarters)
- Identify salary anomalies & issues
- Create top earners list
- Generate department salary breakdowns
- Export data for accounting team

#### 5️⃣ Payroll Operations
- Handle mid-month adjustments
- Process bonus payments
- Handle salary corrections
- Validate banking info for deposits
- Archive historical payroll

---

---

# 📊 COMMON TASKS DISTRIBUTION

| Task | Dev 1 | Dev 2 | Dev 3 | Dev 4 |
|------|:-----:|:-----:|:-----:|:-----:|
| **Database Backups** | ✓ | | | |
| **Monitor Performance** | ✓ | | | |
| **Security Audit** | | ✓ | | |
| **User Access Review** | | ✓ | | |
| **Employee Data Cleanup** | | | ✓ | |
| **Add New Employee** | | | ✓ | |
| **Employee Data Export** | | | ✓ | |
| **Monthly Payroll** | Setup | Approve | Verify | Calculate |
| **Salary Analysis** | | | | ✓ |
| **Bug Fixes** | Backend | Auth | UI/Forms | Calculations |
| **Testing** | Backend | Auth | UI/Forms | Calculations |

---

# 🔄 WORKFLOW QUY TRÌNH

## Thêm Nhân Viên Mới

```
DEV 3: Tạo form /them-nhan-vien.php
             ↓
DEV 2: Verify quyền & tạo login account (nếu cần)
             ↓
DEV 1: Backup database
             ↓
DEV 4: Thêm salary record (nếu là employee có lương)
             ↓
✅ DONE
```

## Tính Lương Hàng Tháng (25-26/tháng)

```
DEV 3: Verify employee data đầy đủ & chính xác
             ↓
DEV 4: Tính lương dùng /luong.php
             ↓
DEV 4: Verify calculations & generate report
             ↓
DEV 2: Lock payroll (prevent changes) - nếu cần
             ↓
DEV 1: Backup payroll data
             ↓
✅ DONE - READY FOR TRANSFER
```

## Sửa/Cập Nhật Thông Tin Nhân Viên

```
DEV 3: Edit employee via form
             ↓
DEV 1: Verify data integrity
             ↓
DEV 4: Update salary (nếu position/department thay đổi)
             ↓
DEV 2: Log thay đổi & permissions (nếu cần)
             ↓
✅ DONE
```

## Xóa/Archive Nhân Viên

```
DEV 3: Soft delete (mark inactive)
             ↓
DEV 4: Archive salary records
             ↓
DEV 1: Verify no orphaned records
             ↓
DEV 2: Revoke access & permissions
             ↓
✅ DONE
```

---

# 📅 DAILY/WEEKLY CHECKLIST

## 🔔 Daily Tasks (Hàng ngày)

### Dev 1:
```
☐ Check database health (Size, Performance)
☐ Review error logs
☐ Check slow queries
☐ Monitor disk space
```

### Dev 2:
```
☐ Review failed login attempts
☐ Check suspicious activities
☐ Review access logs
☐ Monitor for security issues
```

### Dev 3:
```
☐ Verify employee data consistency
☐ Test add/edit/delete forms
☐ Check for data errors
☐ Monitor file uploads
```

### Dev 4:
```
☐ Check payroll status
☐ Verify salary calculations
☐ Monitor recent changes
☐ Check for anomalies in data
```

---

## 📋 Weekly Tasks (Hàng tuần)

### Dev 1:
```
☐ Full database backup verification
☐ Performance report & analysis
☐ Query optimization review
☐ Security patch check
```

### Dev 2:
```
☐ Security audit report
☐ Permission review & audit
☐ User activity report
☐ Access control review
```

### Dev 3:
```
☐ Employee data quality report
☐ Form functionality testing
☐ Data completeness check
☐ Photo & document upload verify
```

### Dev 4:
```
☐ Payroll preparation for month-end
☐ Salary report generation
☐ Calculation accuracy verification
☐ Historical data archiving
```

---

# 🚨 EMERGENCY HANDLING - XỨNG PHÓ TÌNH HUỐNG KHẨN CẤP

## Database Down / Dữ Liệu Bị Mất

```
⚠️ ALERT LEVEL: CRITICAL
😱 WHO: DEV 1 (Lead), All Devs (Backup)

IMMEDIATE ACTIONS:
  1. DEV 1: Check database server status
  2. DEV 1: Check connection @ /connection/config.php
  3. DEV 1: Review error logs
  4. DEV 1: Attempt recovery from latest backup
  5. DEV 2: Notify all users
  6. DEV 1: Restore from backup (if needed)

RECOVERY TIME: 30-60 minutes (typical)
PREVENTION: Daily automated backups, weekly verification
```

## Security Breach / Hành Vi Đáng Ngờ

```
⚠️ ALERT LEVEL: CRITICAL
😱 WHO: DEV 2 (Lead), DEV 1 (Backup)

IMMEDIATE ACTIONS:
  1. DEV 2: Disable suspicious account immediately
  2. DEV 2: Check /action/login-action.php logs
  3. DEV 2: Review access logs & /layouts/phan-quyen.php rules
  4. DEV 1: Check for unauthorized data access
  5. DEV 2: Change all administrative passwords
  6. DEV 1: Backup current system state
  7. DEV 2: Investigate & document incident

ESCALATION: Notify IT Security immediately
```

## Payroll Error / Sai Sót Tính Lương

```
⚠️ ALERT LEVEL: HIGH
😱 WHO: DEV 4 (Lead), DEV 1 (Backup)

IMMEDIATE ACTIONS:
  1. DEV 4: Check /models/ChinhLuong.php calculation logic
  2. DEV 4: Verify employee salary data via /xem-nhan-vien.php
  3. DEV 4: Review /action/fetch-luong-details.php for errors
  4. DEV 4: Recalculate affected salaries
  5. DEV 1: Verify database integrity (no corrupted data)
  6. DEV 4: Generate audit report for HR department
  7. DEV 1: Restore from pre-error backup (if needed)

CRITICAL: This could affect employee payments!
NOTIFY: Finance/HR director immediately
```

## Data Corruption / Upload Error

```
⚠️ ALERT LEVEL: HIGH
😱 WHO: DEV 3 (Lead), DEV 1 (Backup)

IMMEDIATE ACTIONS:
  1. DEV 3: Check /uploads/ directory
  2. DEV 3: Verify employee photo uploads
  3. DEV 3: Check /nhan-vien-action.php for errors
  4. DEV 1: Verify database integrity
  5. DEV 3: Restore corrupted files from backup
  6. DEV 3: Validate data consistency
  7. DEV 1: Re-sync database if needed

TIMELINE: Complete within 2 hours
```

## Forgotten Password / Locked Account

```
⚠️ ALERT LEVEL: LOW
😱 WHO: DEV 2

ACTIONS:
  1. Verify user identity (ID number, email)
  2. Reset password in /models/TaiKhoan.php
  3. Log password reset attempt
  4. Unlock account in /tai-khoan-action.php
  5. Provide temporary password
  6. Ask user to change password on first login

TIMELINE: 15-30 minutes
```

---

# 📂 FILE OWNERSHIP MATRIX

## Dev 1 - Backend & Database

```
FILES OWNED:
├── /connection/config.php                          [Database Connection]
├── /models/
│   ├── Luong.php                                  [Salary Model]
│   ├── ChinhLuong.php                             [Salary Adjustment Model]
│   └── TongQuan.php                               [Dashboard/Overview Model]
└── /action/
    ├── export_excel_luong.php                     [Payroll Export]
    ├── export_excel_nhanvien.php                  [Employee Export]
    ├── fetch-luong-details.php                    [Salary Details API]
    ├── fetch-chinh-luong-history.php              [Salary History API]
    ├── print_luong.php                            [Payroll Print]
    └── print_luong1.php                           [Payroll Print v2]

PRIMARY RESPONSIBILITY:
✓ Database performance & optimization
✓ Backup & recovery procedures
✓ Data integrity & consistency
✓ Query optimization
✓ API response times
```

## Dev 2 - Security & Authentication

```
FILES OWNED:
├── /login.php                                      [Login Page]
├── /login2.php                                     [Login v2]
├── /ds-tai-khoan.php                             [Account List]
├── /tai-khoan-ca-nhan.php                        [Personal Account]
├── /models/TaiKhoan.php                          [Account Model]
├── /layouts/phan-quyen.php                       [Permission Control]
└── /action/
    ├── login-action.php                          [Login Processing]
    ├── login-action-xoa-account.php              [Account Deletion]
    └── tai-khoan-action.php                      [Account Management]

PRIMARY RESPONSIBILITY:
✓ User authentication & login flow
✓ Permission & role management
✓ Account creation/deletion
✓ Security audit & logging
✓ Access control enforcement
```

## Dev 3 - Employee Data Management

```
FILES OWNED:
├── /ds-nhan-vien.php                             [Employee List]
├── /them-nhan-vien.php                           [Add Employee]
├── /xem-nhan-vien.php                            [View Employee]
├── /tra-cuu-nhan-vien.php                        [Search Employee]
├── /nhom-nhan-vien.php                           [Employee Groups]
├── /cong-tac.php                                 [Job Assignments]
├── /khen-thuong-ky-luat.php                      [Rewards/Discipline]
├── /models/
│   ├── NhanVien.php                              [Employee Model]
│   ├── PhongBan.php                              [Department Catalog]
│   ├── ChucVu.php                                [Position Catalog]
│   ├── BangCap.php                               [Qualification Catalog]
│   ├── ChuyenMon.php                             [Specialization Catalog]
│   ├── QuocTich.php                              [Nationality Catalog]
│   ├── DanToc.php                                [Ethnicity Catalog]
│   ├── TonGiao.php                               [Religion Catalog]
│   ├── HonNhan.php                               [Marital Status Catalog]
│   ├── TrinhDo.php                               [Education Level Catalog]
│   ├── LoaiNhanVien.php                          [Employee Type Catalog]
│   ├── NhomNV.php                                [Employee Group Model]
│   ├── ChiTietNhom.php                           [Group Details Model]
│   └── CongTac.php                               [Job Assignment Model]
└── /action/
    ├── nhan-vien-action.php                      [Employee CRUD]
    ├── phong-ban-action.php                      [Department CRUD]
    ├── chuc-vu-action.php                        [Position CRUD]
    ├── bang-cap-action.php                       [Qualification CRUD]
    ├── chuyen-mon-action.php                     [Specialization CRUD]
    ├── quoc-tich-action.php                      [Nationality CRUD]
    ├── dan-toc-action.php                        [Ethnicity CRUD]
    ├── ton-giao-action.php                       [Religion CRUD]
    ├── hon-nhan-action.php                       [Marital Status CRUD]
    ├── trinh-do-action.php                       [Education CRUD]
    ├── loai-nhanvien-action.php                  [Employee Type CRUD]
    ├── nhom-nhan-vien-action.php                 [Group Management]
    ├── chi-tiet-nhom-action.php                  [Group Details]
    ├── cong-tac-action.php                       [Job Assignment CRUD]
    ├── khen-thuong-ky-luat-action.php            [Reward/Discipline CRUD]
    ├── tra-cuu-nhan-vien-action.php              [Employee Search]
    ├── export_excel_nhanvien.php                 [Employee Export]
    ├── print_nhanvien.php                        [Employee Print]
    └── them-nhan-vien-view-action.php            [Add Employee View]

PRIMARY RESPONSIBILITY:
✓ Employee data CRUD operations
✓ Data quality & consistency
✓ Form validation & processing
✓ Master data maintenance (catalogs)
✓ Employee search & reporting
```

## Dev 4 - Payroll & Reports

```
FILES OWNED:
├── /luong.php                                     [Salary Management]
├── /chinh-luong.php                              [Salary Adjustments]
├── /add-luong.php                                [Add Salary]
├── /thong-ke-luong.php                           [Payroll Statistics]
├── /thong-ke-top.php                             [Top Salaries Report]
├── /tong-quan.php                                [Dashboard/Overview]
├── /models/
│   ├── Luong.php                                 [Salary Model]
│   └── ChinhLuong.php                            [Salary Adjustment Model]
└── /action/
    ├── luong-action.php                          [Salary CRUD]
    ├── chinh-luong-action.php                    [Adjustment CRUD]
    ├── export_excel_luong.php                    [Payroll Export]
    ├── fetch-luong-details.php                   [Salary Details API]
    ├── fetch-chinh-luong-history.php             [Adjustment History API]
    ├── print_luong.php                           [Payroll Print]
    └── print_luong1.php                          [Payroll Print v2]

PRIMARY RESPONSIBILITY:
✓ Salary calculation & processing
✓ Payroll administration (25-26/month)
✓ Salary adjustments & modifications
✓ Payroll reports & analytics
✓ Dashboard management
```

---

# 📞 CONTACT & ESCALATION MATRIX

| Issue Category | Lead | Backup | Response Time | Contact Method |
|:---|:---:|:---:|:---:|:---|
| 🗄️ **Database & Performance** | Dev 1 | Dev 3 | IMMEDIATE | Slack/Phone |
| 🔐 **Security & Authentication** | Dev 2 | Dev 1 | IMMEDIATE | Slack/Phone |
| 👥 **Employee Data & Forms** | Dev 3 | Dev 2 | Same Day | Email/Slack |
| 💰 **Payroll & Calculations** | Dev 4 | Dev 1 | URGENT (same day) | Slack/Phone |
| 🐛 **Cross-Module Bug** | PM | All Devs | Same Day | Meeting |

---

# 📝 DOCUMENTATION STANDARDS

## Code Quality Requirements

### All Developers must maintain:
```
✓ Clear, descriptive code comments (English)
✓ Consistent naming conventions (camelCase/snake_case)
✓ Function/method documentation blocks
✓ Error handling with meaningful messages
✓ SQL queries with performance optimization
```

### Individual Repositories:
```
Dev 1: Maintains /connection/ & /models/ documentations
Dev 2: Maintains /auth/ & /permission/ documentations  
Dev 3: Maintains /employee/ & /catalog/ documentations
Dev 4: Maintains /payroll/ & /report/ documentations
```

## System-wide Documentation

### Shared files (all devs contribute):
```
📄 README.md                    - Setup & installation guide
📄 MAINTENANCE_LOG.md           - Weekly maintenance activities
📄 KNOWN_ISSUES.md              - Current bugs & workarounds
📄 DATABASE_SCHEMA.md           - Database structure & relationships
📄 API_ENDPOINTS.md             - API documentation
📄 DEPLOYMENT.md                - Deployment procedures
```

### Update Schedule:
- **README.md** - When setup changes
- **MAINTENANCE_LOG.md** - Every Friday (end of week)
- **KNOWN_ISSUES.md** - When bugs found/fixed
- **DATABASE_SCHEMA.md** - After schema changes
- **API_ENDPOINTS.md** - When APIs change
- **DEPLOYMENT.md** - After deployment procedure changes

---

# 🔄 CODE REVIEW & MERGE PROCESS

## Before Merging to `main` branch:

```
1️⃣ SELF TEST
   ↓
   Dev: Test your changes thoroughly
   Dev: Verify no syntax errors
   Dev: Check database integrity (if applicable)

2️⃣ CODE REVIEW
   ↓
   Request review from another developer
   Provide clear description of changes
   Wait for 2-3 hours for review feedback

3️⃣ PEER FEEDBACK
   ↓
   Reviewer: Check code logic
   Reviewer: Verify no security issues
   Reviewer: Suggest improvements (if any)

4️⃣ REVISIONS
   ↓
   Dev: Make requested changes (if any)
   Dev: Re-test thoroughly

5️⃣ APPROVAL
   ↓
   Reviewer: Approve changes
   Dev: Merge to main
   Dev 1: Create database backup immediately

6️⃣ BACKUP
   ↓
   ✅ DONE - Production Ready
```

---

# 📊 PERFORMANCE MONITORING

## Database Health Checks (Daily)

```
Dev 1 should monitor:
  ✓ Database size (growth rate)
  ✓ Slow query log (> 1 second)
  ✓ Connection pool usage
  ✓ Query execution times
  ✓ Index efficiency
  ✓ Backup completion status
```

## System Monitoring (Weekly Report)

```
Report should include:
  📈 Database performance metrics
  🔐 Security incidents/attempts
  👥 Employee data changes (count)
  💰 Payroll processing status
  🐛 Bugs found & fixed
  📦 Backup verification result
```

---

# 🎯 DEVELOPMENT CONVENTIONS

## File Naming:
```
✓ Use lowercase with hyphens: employee-list.php (not employeeList.php)
✓ Use descriptive names: them-nhan-vien.php (not add-employees.php)
✓ Match Vietnamese terminology where used
✓ Action files: *-action.php (consistent)
✓ Model files: PascalCase (ClassName.php)
```

## Database Naming:
```
✓ Tables: plural, lowercase (nhan_vien, tai_khoan)
✓ Columns: lowercase, descriptive (first_name, email)
✓ Foreign keys: table_id format (nhan_vien_id)
✓ Timestamps: created_at, updated_at (consistent)
```

## Commit Messages:
```
Format: [DEV#] Category: Brief description

Examples:
  [DEV1] fix: Optimize slow query in Luong.php
  [DEV2] feat: Add email verification for login
  [DEV3] fix: Validate employee photo upload
  [DEV4] chore: Update salary calculation formula

Categories:
  feat   - New feature
  fix    - Bug fix
  chore  - Maintenance/utilities
  docs   - Documentation
  refactor - Code refactoring
```

---

# ✅ SIGN-OFF & ACCOUNTABILITY

## Each Developer Must:

- ✅ Review this document weekly
- ✅ Understand their assigned modules
- ✅ Follow code quality standards
- ✅ Test changes before deployment
- ✅ Maintain backups (Dev 1 responsibility)
- ✅ Document changes they make
- ✅ Respond to urgent issues immediately
- ✅ Attend weekly sync meetings
- ✅ Update shared documentation

## Weekly Check-in Meeting:

```
📅 WHEN: Every Friday 14:00-15:00
👥 ATTENDEES: All 4 developers + PM

AGENDA:
  • Review weekly maintenance activities
  • Discuss current issues & blockers
  • Plan next week's priorities
  • Cross-check performance metrics
  • Escalate urgent items
```

---

**📋 Version:** 1.1  
**📅 Last Updated:** 25/02/2026  
**👥 Created by:** Development PM  
**📌 Status:** ACTIVE - Maintenance Mode  
**🔄 Review Schedule:** Quarterly (End of each fiscal quarter)

---

### 📞 Quick Contacts:
- **Dev 1 (Database):** 0XXX-XXX-XXXX  
- **Dev 2 (Security):** 0XXX-XXX-XXXX  
- **Dev 3 (Employee):** 0XXX-XXX-XXXX  
- **Dev 4 (Payroll):** 0XXX-XXX-XXXX  
- **PM (Project Manager):** 0XXX-XXX-XXXX  

### 🆘 For Emergencies:
- Call PM immediately
- Escalate to IT Manager
- Do NOT attempt fixes without consultation
