# TÓM TẮT PHÂN CÔNG 4 DEVELOPERS - HRM SYSTEM

## 🎯 QUICK REFERENCE

### 👨‍💻 DEV 1: BACKEND & DATABASE
**8 tuần, ~80 files**
```
Tuần 1-2: Database optimization, API framework, logging
Tuần 3-4: API endpoints (employee, catalog)
Tuần 5-6: Query optimization, caching
Tuần 7-8: Security hardening, monitoring

📦 Deliverables:
  - API endpoints cho tất cả modules
  - Database optimized (indexes, triggers)
  - Caching system (Redis/Memcached)
  - /api/ folder (routes, middleware)
  - /classes/ (Logger, Cache, Helper...)
```

---

### 👨‍💻 DEV 2: SECURITY & AUTHENTICATION
**8 tuần, ~60 files**
```
Tuần 1-2: Login refactor, session management, password reset
Tuần 3-4: RBAC (role-permission system), authorization
Tuần 5-6: User management, audit logging
Tuần 7-8: 2FA, JWT tokens, security hardening

📦 Deliverables:
  - RBAC system (roles, permissions)
  - User account management
  - Login/logout/password reset
  - 2FA setup
  - Audit logging
  - /middleware/ folder (CSRF, Auth...)
```

---

### 👨‍💻 DEV 3: EMPLOYEE MANAGEMENT
**8 tuần, ~70 files**
```
Tuần 1-2: Refactor models (phòng ban, chức vụ, danh mục)
Tuần 3-4: Employee CRUD (thêm, sửa, xóa, danh sách)
Tuần 5-6: Employee groups, work assignments, awards/discipline
Tuần 7-8: Advanced search, import/export

📦 Deliverables:
  - Danh sách nhân viên + search/filter
  - Form thêm/sửa nhân viên (form phức tạp)
  - Nhóm nhân viên management
  - Công tác tracking
  - Khen thưởng/kỷ luật management
  - Import/Export nhân viên
```

---

### 👨‍💻 DEV 4: PAYROLL & REPORTS
**8 tuần, ~50 files**
```
Tuần 1-2: Payroll calculation functions, salary scheduler
Tuần 3-4: Lương management (thêm, sửa, lịch sử)
Tuần 5-6: Reports (thống kê lương, top, chi tiết báo cáo)
Tuần 7-8: Dashboard, charts, export/print

📦 Deliverables:
  - Payroll calculation system (accurate + automated)
  - Lương management UI + APIs
  - Báo cáo tổng hợp (Excel, PDF, print)
  - Dashboard + KPIs + Charts
  - Payroll scheduler (auto monthly)
```

---

## 📋 GIAO VIỆC TUẦN ĐẦU TIÊN

### DEV 1 TODO (Tuần 1-2)
- [ ] Read `/TASK_ASSIGNMENT.md` section "Developer 1"
- [ ] Create `/database/migrations/001_optimize_schema.sql`
- [ ] Create `/classes/Logger.php` & `/logs/` folder
- [ ] Refactor `/connection/config.php` (connection pooling)
- [ ] Create `/api/` folder structure
- [ ] Create `/api/ApiResponse.php` & `/api/ApiRequest.php`
- [ ] Start API documentation `/API_DOCUMENTATION.md`

### DEV 2 TODO (Tuần 1-2)
- [ ] Read `/TASK_ASSIGNMENT.md` section "Developer 2"
- [ ] Analyze current `/login.php` & `/action/login-action.php`
- [ ] Create `/classes/AuthManager.php` 
- [ ] Create `/classes/SessionManager.php`
- [ ] Refactor `/models/TaiKhoan.php`
- [ ] Update `/login.php` - new authentication system
- [ ] Create password reset flow

### DEV 3 TODO (Tuần 1-2)
- [ ] Read `/TASK_ASSIGNMENT.md` section "Developer 3"
- [ ] Review all models in `/models/` folder
- [ ] Create refactored versions:
  - `/models/PhongBan.php`
  - `/models/ChucVu.php`
  - `/models/NhanVien.php`
- [ ] Create `/danh-muc/` folder
- [ ] Start `/danh-muc/phong-ban.php` template

### DEV 4 TODO (Tuần 1-2)
- [ ] Read `/TASK_ASSIGNMENT.md` section "Developer 4"
- [ ] Create `/LUONG_RULES.md` - document payroll formulas
- [ ] Analyze current `/models/Luong.php` & `/models/ChinhLuong.php`
- [ ] Create refactored Luong with separated functions:
  - `calculateBaseSalary()`
  - `calculateBonus()`
  - `calculateInsurance()`
  - `calculateTax()`
- [ ] Create `/classes/PayrollScheduler.php`

---

## 📊 DEPENDENCIES MATRIX

```
DEV 1 ←→ DEV 2: API authentication endpoints, token validation
DEV 1 ←→ DEV 3: API endpoints untuk employees & catalogs
DEV 1 ←→ DEV 4: Payroll API endpoints, salary calculations
DEV 2 ←→ DEV 3: Permission checking khi CRUD employees
DEV 2 ←→ DEV 4: Permission checking khi CRUD lương
DEV 3 ←→ DEV 4: Employee data → Payroll calculations
```

**Weekly sync:** DEV 1 & DEV 4 every Wednesday (discuss API/payroll integration)

---

## 🚀 DEPLOYMENT TIMELINE

```
Week 1-2:   Infrastructure setup
Week 3-4:   Core modules development
Week 5-6:   Supporting modules + reporting
Week 7-8:   Dashboard + hardening
Week 9:     Testing & bug fixes (ALL DEVS)
Week 10:    UAT & Production Go-Live
```

---

## 📁 FOLDER STRUCTURE TO CREATE

```
/api/
  /routes/
    nhan-vien.php
    danh-muc.php
  /middleware/
    Authentication.php
    Validation.php
    AuthToken.php
/classes/
  Logger.php
  AuthManager.php
  SessionManager.php
  Validator.php
  Helper.php
  Cache.php
  PayrollScheduler.php
  AuditLogger.php
  BaseModel.php
/middleware/
  Authorization.php
/danh-muc/
  phong-ban.php
  chuc-vu.php
  trinh-do.php
  ... (9 danh mục khác)
/reports/
  tong-hop.php
/database/
  /migrations/
    001_optimize_schema.sql
    002_create_rbac_tables.sql
    003_create_audit_logs.sql
```

---

## 📞 DAILY STANDUP CHECKLIST

**Every 9:00 AM - 15 minutes**

Each dev reports:
1. ✅ Yesterday: What did you complete?
2. 🔨 Today: What are you working on?
3. 🚧 Blockers: What's stopping you?
4. ⚠️ Risks: What might go wrong?

---

## ✅ END-OF-WEEK CHECKLIST

**Every Friday 5 PM:**
- [ ] All code pushed to GitHub
- [ ] PR reviews completed
- [ ] No blockers for next week
- [ ] Time tracking updated
- [ ] Brief status report to PM

---

## 🆘 ESCALATION HOTLINE

**Critical Issues:**
- Slack: @dev-team
- Call: PM immediately
- Standby: Team on-call

**Non-Critical:**
- Slack: #hrm-system channel
- Next standup: Discuss

---

**Version:** 1.0 | **Last Updated:** 25/02/2024 | **Status:** ACTIVE
