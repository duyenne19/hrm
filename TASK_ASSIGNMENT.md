# PHÂN CÔNG CHI TIẾT PHÁT TRIỂN HỆ THỐNG QUẢN LÝ NHÂN SỰ - 4 NGƯỜI
**Dự án:** HRM System (Quản Lý Nhân Sự)  
**Công nghệ:** PHP, MySQL, Bootstrap, JavaScript  
**Thời gian:** 10 tuần (2.5 tháng)

---

# 👨‍💻 DEVELOPER 1: QUẢN LÝ CƠ SỞ DỮ LIỆU VÀ BACKEND NỀN TẢNG
**Vai trò:** Người trưởng nhóm backend, chuyên gia database

## GIAI ĐOẠN 1: TUẦN 1-2 (CHUẨN BỊ CƠ SỞ HẠ TẦNG)

### ✅ Nhiệm vụ 1.1: Tối ưu hóa cấu trúc Database
- Phân tích schema database hiện tại
- Thêm indexes cho các cột tìm kiếm thường xuyên (tên nhân viên, mã nhân viên, phòng ban)
- Tạo relationship constraints (khóa ngoại)
- Tạo stored procedures cho tính lương tự động
- Tạo trigger tự động cập nhật ngày sửa đổi
- **File liên quan:** Tạo file `/database/migrations/001_optimize_schema.sql`

### ✅ Nhiệm vụ 1.2: Tạo backup & recovery procedures
- Tạo script backup hàng ngày
- Tạo script restore dữ liệu
- Tạo script dump database cho development
- **File liên quan:** Tạo folder `/database/backup/` với các script shell/batch

### ✅ Nhiệm vụ 1.3: Cấu hình connection pooling
- Sửa file `/connection/config.php` - thêm connection pooling
- Tạo class `DatabaseConnection` quản lý kết nối
- Implement singleton pattern
- Setup retry logic khi kết nối thất bại
- **File cần sửa:** `/connection/config.php`

### ✅ Nhiệm vụ 1.4: Tạo logging hệ thống
- Tạo class `Logger` để ghi log errors
- Tạo folder `/logs/` để lưu file log
- Ghi log tất cả database queries (slow query log)
- Ghi log tất cả errors exceptions
- **File cần tạo:** `/classes/Logger.php`, `/logs/.gitkeep`

---

## GIAI ĐOẠN 2: TUẦN 3-4 (XÂY DỰNG API NỀN TẢNG)

### ✅ Nhiệm vụ 2.1: Tạo REST API Base Framework
- Tạo file `/api/ApiResponse.php` - class xử lý response chuẩn
- Tạo file `/api/ApiRequest.php` - class xử lý request validation
- Tạo file `/api/Router.php` - router cho các API endpoints
- Tạo file `/api/middleware/Authentication.php` - kiểm tra token
- Tạo file `/api/middleware/Validation.php` - validate input data
- **Mục đích:** API responses luôn theo format: `{success, message, data, errors}`

### ✅ Nhiệm vụ 2.2: Xây dựng API endpoints cơ bản
- **GET /api/nhan-vien** - lấy danh sách nhân viên (có phân trang, lọc, tìm kiếm)
- **GET /api/nhan-vien/{id}** - lấy chi tiết nhân viên
- **POST /api/nhan-vien** - thêm nhân viên mới
- **PUT /api/nhan-vien/{id}** - cập nhật nhân viên
- **DELETE /api/nhan-vien/{id}** - xóa nhân viên
- **GET /api/nhan-vien/{id}/luong** - lấy lịch sử lương của nhân viên
- **File cần tạo:** `/api/routes/nhan-vien.php`

### ✅ Nhiệm vụ 2.3: Tạo API endpoints cho các danh mục
- **GET /api/phong-ban** - danh sách phòng ban
- **GET /api/chuc-vu** - danh sách chức vụ
- **GET /api/trinh-do** - danh sách trình độ
- **GET /api/chuyên-môn** - danh sách chuyên môn
- **GET /api/bang-cap** - danh sách bằng cấp
- **POST, PUT, DELETE** cho từng danh mục
- **File cần tạo:** `/api/routes/danh-muc.php`

### ✅ Nhiệm vụ 2.4: Tạo API documentation
- Tạo file `/API_DOCUMENTATION.md` mô tả chi tiết tất cả endpoints
- Cho mỗi endpoint: method, URL, parameters, request body, response examples
- Tạo Postman collection `/postman/HRM-API.postman_collection.json`
- **Ví dụ:** 
  ```
  GET /api/nhan-vien?page=1&limit=10&search=Nguyễn&phong_ban_id=2
  Response: {
    success: true,
    data: [
      {id: 1, ma_nv: "NV001", hoten: "Nguyễn Văn A", ...}
    ],
    pagination: {total: 100, page: 1, limit: 10}
  }
  ```

---

## GIAI ĐOẠN 3: TUẦN 5-6 (REFACTOR MODELS & TỐI ƯU HÓA)

### ✅ Nhiệm vụ 3.1: Refactor model Nhân Viên
- Sửa file `/models/NhanVien.php`
- Thêm input validation cho tất cả methods
- Tách riêng logic business từ database queries
- Implement caching cho danh sách nhân viên
- Thêm method `search($keyword)` - tìm kiếm nâng cao
- Thêm method `getByPhongBan($id)` - lấy nhân viên theo phòng ban
- Thêm method `getByChucVu($id)` - lấy nhân viên theo chức vụ
- **Mục đích:** Code clean, dễ bảo trì, tái sử dụng

### ✅ Nhiệm vụ 3.2: Refactor model Lương
- Sửa file `/models/Luong.php`
- Tách hàm tính lương riêng: `calculateBaseSalary(), calculateBonus(), calculateDeduction()`
- Thêm method `getByNhanVien($nvan_id, $year, $month)` - lấy lương theo nhân viên tháng/năm
- Thêm method `getTotalPayroll($year, $month)` - tổng lương công ty tháng/năm
- Implement caching kết quả tính lương
- Thêm transaction cho insert/update lương (atomicity)
- **Mục đích:** Dễ maintain, có thể tái sử dụng cho reports

### ✅ Nhiệm vụ 3.3: Refactor model Chỉnh Lương
- Sửa file `/models/ChinhLuong.php`
- Thêm method `getHistory($nvan_id)` - lịch sử thay đổi lương
- Thêm method `getHistoryByDateRange($start, $end)` - lịch sử theo ngày
- Thêm method `compareOldNew($old_id, $new_id)` - so sánh lương cũ và mới
- Implement audit log cho tất cả thay đổi lương
- **Mục đích:** Tracking thay đổi lương một cách rõ ràng

### ✅ Nhiệm vụ 3.4: Tạo Base Model & Utilities
- Tạo file `/models/BaseModel.php` - class cha chung cho tất cả models
- Implement CRUD methods cơ bản: `create(), read(), update(), delete()`
- Tạo file `/classes/Validator.php` - validate input data
- Tạo file `/classes/Helper.php` - helper functions dùng chung
- **Mục đích:** DRY principle, tránh code lặp lại

---

## GIAI ĐOẠN 4: TUẦN 7-8 (PERFORMANCE & SECURITY)

### ✅ Nhiệm vụ 4.1: Caching Strategy
- Implement Redis/Memcached caching (nếu available)
- Cache danh sách nhân viên: 1 giờ
- Cache danh mục: 24 giờ
- Cache lương: 24 giờ
- Tạo script clear cache manual
- **File cần tạo:** `/classes/Cache.php`

### ✅ Nhiệm vụ 4.2: Query Optimization
- Analyze tất cả queries dùng EXPLAIN
- Thêm indexes cho LIKE searches
- Optimize JOIN queries
- Implement query result caching
- Giảm N+1 queries
- **Test:** Kiểm tra response time < 2 giây

### ✅ Nhiệm vụ 4.3: Security Hardening
- Prevent SQL injection: sử dụng prepared statements everywhere
- Add input sanitization
- Validate tất cả user inputs
- Escape output data
- Implement rate limiting cho API
- Add CORS headers validate
- **File cần xem:** `/action/*.php` - update tất cả

### ✅ Nhiệm vụ 4.4: Error Handling & Monitoring
- Centralize error handling
- Tạo custom exceptions
- Setup error page graceful
- Monitor database performance
- Setup alerts cho database errors
- **File cần tạo:** `/classes/ExceptionHandler.php`

---

## 📂 Tổng hợp Files cần làm việc (DEV 1):

**Tạo mới:**
- `/api/ApiResponse.php`
- `/api/ApiRequest.php`
- `/api/Router.php`
- `/api/middleware/Authentication.php`
- `/api/middleware/Validation.php`
- `/api/routes/nhan-vien.php`
- `/api/routes/danh-muc.php`
- `/classes/Logger.php`
- `/classes/Validator.php`
- `/classes/Helper.php`
- `/classes/Cache.php`
- `/models/BaseModel.php`
- `/database/migrations/001_optimize_schema.sql`
- `/API_DOCUMENTATION.md`
- `/postman/HRM-API.postman_collection.json`

**Sửa hiện tại:**
- `/connection/config.php`
- `/models/NhanVien.php`
- `/models/Luong.php`
- `/models/ChinhLuong.php`

### 🔗 Phải phối hợp với:
- **Developer 2:** API authentication endpoints
- **Developer 4:** Payroll calculation logic

---

# 👨‍💻 DEVELOPER 2: QUẢN LÝ BẢO MẬT VÀ KIỂM SOÁT TRUY CẬP
**Vai trò:** Chuyên gia bảo mật, quản lý quyền hạn

## GIAI ĐOẠN 1: TUẦN 1-2 (HỆ THỐNG USER & LOGIN)

### ✅ Nhiệm vụ 1.1: Refactor hệ thống Login
- Sửa file `/login.php` - giao diện login
- Sửa file `/action/login-action.php` - logic login
- Xóa file `/login2.php` (duplicate)
- Implement password hashing (use `password_hash()` PHP)
- Thêm remember me functionality (cookie secure)
- Thêm login attempts tracking (block sau 5 lần sai)
- Tạo session management
- **File cần tạo:** `/classes/AuthManager.php`

### ✅ Nhiệm vụ 1.2: Tạo Password Reset Module
- Tạo form quên mật khẩu `/password-reset.php`
- Tạo form change password `/change-password.php`
- Implement email verification (sendmail)
- Tạo reset token valid 24 giờ
- Hash password mới khi reset
- Ghi log lịch sử reset password
- **File cần tạo:** `/action/password-reset-action.php`

### ✅ Nhiệm vụ 1.3: Refactor model TaiKhoan
- Sửa file `/models/TaiKhoan.php`
- Thêm method `login($username, $password)` - đăng nhập
- Thêm method `register($data)` - tạo tài khoản mới
- Thêm method `updatePassword($id, $old_pwd, $new_pwd)` - đổi password
- Thêm method `resetPassword($email)` - reset password
- Thêm method `getByUsername($username)`
- Thêm method `getByEmail($email)`
- Validate password strength (ít nhất 8 ký tự, số, chữ)
- **Mục đích:** Quản lý tài khoản tập trung

### ✅ Nhiệm vụ 1.4: Tạo Session & Cookie Management
- Tạo file `/classes/SessionManager.php`
- Quản lý session timeout (30 phút)
- Quản lý secure cookies
- Tạo CSRF token cho form submissions
- Tạo regenerate session ID (prevent fixation)
- **Mục đích:** An toàn phiên làm việc

---

## GIAI ĐOẠN 2: TUẦN 3-4 (ROLE-BASED ACCESS CONTROL - RBAC)

### ✅ Nhiệm vụ 2.1: Thiết kế RBAC System
- Tạo database tables: `roles`, `permissions`, `role_permissions`, `user_roles`
- Tạo migration script `/database/migrations/002_create_rbac_tables.sql`
- Define roles: Admin, Quản lý nhân sự, Kế toán, Nhân viên, Viewer
- Define permissions cho từng role
- **File cần tạo:** `/database/migrations/002_create_rbac_tables.sql`

### ✅ Nhiệm vụ 2.2: Tạo Role & Permission Models
- Tạo file `/models/Role.php` - quản lý roles
- Tạo file `/models/Permission.php` - quản lý permissions
- Implement methods:
  - `assignRoleToUser($user_id, $role_id)`
  - `removeRoleFromUser($user_id, $role_id)`
  - `assignPermissionToRole($role_id, $perm_id)`
  - `getUserRoles($user_id)`
  - `getUserPermissions($user_id)`
  - `hasPermission($user_id, $permission)`

### ✅ Nhiệm vụ 2.3: Tạo Authorization Middleware
- Tạo file `/middleware/Authorization.php`
- Middleware check `hasPermission()` trước mỗi action
- Middleware check `hasRole()` trước mỗi page
- Tạo global functions: `can($permission)`, `hasRole($role)`
- Redirect to access denied page nếu không có quyền
- **Sử dụng ở:** Tất cả files action

### ✅ Nhiệm vụ 2.4: Tạo quản lý Roles & Permissions UI
- Tạo file `/quan-ly-phan-quyen.php` - giao diện quản lý
- Build DataTable hiển thị tất cả roles
- Build form thêm/sửa/xóa role
- Build form gán permissions cho role
- Build form gán roles cho user
- **File cần tạo:** `/action/phan-quyen-action.php`

---

## GIAI ĐOẠN 3: TUẦN 5-6 (QUẢN LÝ TÀI KHOẢN & AUDIT)

### ✅ Nhiệm vụ 3.1: Tạo User Management Module
- Sửa file `/ds-tai-khoan.php` - danh sách tài khoản
- Tạo file `/them-tai-khoan.php` - thêm tài khoản mới
- Tạo file `/sua-tai-khoan.php` - sửa tài khoản
- Sửa file `/action/tai-khoan-action.php` - logic CRUD
- Implement:
  - DataTable search/filter/sort
  - Bulk actions (xóa multiple)
  - Status inactive/active
  - Change password for user

### ✅ Nhiệm vụ 3.2: Tạo User Profile Module
- Sửa file `/tai-khoan-ca-nhan.php` - profile user đang login
- User có thể sửa info cá nhân (họ tên, email, phone)
- User có thể đổi password ở đây
- Hiển thị roles của user
- Hiển thị last login time
- Allow upload avatar
- **File cần tạo:** `/action/tai-khoan-ca-nhan-action.php`

### ✅ Nhiệm vụ 3.3: Tạo Audit Log System
- Tạo database table `audit_logs`
- Log tất cả actions: create, update, delete user
- Log tất cả login/logout
- Log tất cả failed login attempts
- Log tất cả permission changes
- Tạo file `/audit-logs.php` - view audit logs
- **File cần tạo:** `/classes/AuditLogger.php`, `/database/migrations/003_create_audit_logs.sql`

### ✅ Nhiệm vụ 3.4: Tạo quản lý phiên làm việc
- Tạo file `/quan-ly-phien-dang-nhap.php` - view active sessions
- Hiển thị danh sách users đang online
- Cho phép admin logout user từ xa
- Hiển thị last activity time của user
- **Mục đích:** Monitor & control user sessions

---

## GIAI ĐOẠN 4: TUẦN 7-8 (SECURITY HARDENING & 2FA)

### ✅ Nhiệm vụ 4.1: Tăng cường bảo mật
- CSRF token: add vào tất cả forms
- Input validation: sanitize tất cả user inputs
- Output escaping: escape tất cả output data
- SQL injection prevention: dùng prepared statements (done bởi Dev 1)
- XSS prevention: escape HTML, validate HTML inputs
- File: Kiểm tra tất cả `/action/*.php` files
- **Tools:** Use functions: `htmlspecialchars()`, `filter_var()`, `preg_match()`

### ✅ Nhiệm vụ 4.2: Implement Two-Factor Authentication (2FA)
- Tạo file `/2fa-setup.php` - setup 2FA bằng Google Authenticator
- Tạo file `/2fa-verify.php` - verify code 2FA lúc login
- Sử dụng library `spomky-labs/otphp` (qua Composer)
- User có thể enable/disable 2FA
- Generate backup codes nếu mất quyền truy cập
- **Tuỳ chọn:** Có thể là SMS 2FA hoặc Email 2FA

### ✅ Nhiệm vụ 4.3: API Authentication & JWT Tokens
- Implement JWT (JSON Web Tokens) cho API
- Tạo endpoint `/api/auth/login` - trả về JWT token
- Tạo endpoint `/api/auth/refresh` - refresh token
- Tạo endpoint `/api/auth/logout` - logout (blacklist token)
- Add JWT middleware cho tất cả API endpoints
- Token expires trong 1 giờ
- **Library:** `firebase/php-jwt`
- **File:** `/api/middleware/AuthToken.php`

### ✅ Nhiệm vụ 4.4: Security Testing & Compliance
- Test SQL Injection trên tất cả inputs
- Test XSS attacks
- Test CSRF attacks
- Test brute force protection
- Test 2FA functionality
- Tạo SECURITY.md document
- **Mục đích:** Đảm bảo không có lỗ hổng bảo mật

---

## 📂 Tổng hợp Files cần làm việc (DEV 2):

**Tạo mới:**
- `/classes/AuthManager.php`
- `/classes/SessionManager.php`
- `/classes/AuditLogger.php`
- `/models/Role.php`
- `/models/Permission.php`
- `/middleware/Authorization.php`
- `/api/middleware/AuthToken.php`
- `/password-reset.php`
- `/change-password.php`
- `/quan-ly-phan-quyen.php`
- `/them-tai-khoan.php`
- `/sua-tai-khoan.php`
- `/2fa-setup.php`
- `/2fa-verify.php`
- `/quan-ly-phien-dang-nhap.php`
- `/audit-logs.php`
- `/action/password-reset-action.php`
- `/action/phan-quyen-action.php`
- `/database/migrations/002_create_rbac_tables.sql`
- `/database/migrations/003_create_audit_logs.sql`

**Sửa hiện tại:**
- `/login.php`
- `/action/login-action.php`
- `/models/TaiKhoan.php`
- `/ds-tai-khoan.php`
- `/tai-khoan-ca-nhan.php`
- `/action/tai-khoan-action.php`
- `/layouts/phan-quyen.php`
- Tất cả `/action/*.php` - add CSRF token & input validation

### 🔗 Phải phối hợp với:
- **Developer 1:** API authentication endpoint, token validation
- **Developer 3:** Check permission khi xem danh sách nhân viên

---

# 👨‍💻 DEVELOPER 3: QUẢN LÝ NHÂN SỰ VÀ DANH MỤC
**Vai trò:** Chuyên gia quản lý dữ liệu nhân sự

## GIAI ĐOẠN 1: TUẦN 1-2 (REFACTOR MODELS DANH MỤC)

### ✅ Nhiệm vụ 1.1: Refactor model Phòng Ban
- Sửa file `/models/PhongBan.php`
- Implement methods:
  - `getAll()` - danh sách tất cả phòng ban
  - `getById($id)` - chi tiết phòng ban
  - `create($data)` - thêm phòng ban
  - `update($id, $data)` - sửa phòng ban
  - `delete($id)` - xóa phòng ban
  - `getCountEmployees($id)` - số lượng nhân viên
  - `search($keyword)` - tìm kiếm
- Add validation

### ✅ Nhiệm vụ 1.2: Refactor model Chức Vụ
- Sửa file `/models/ChucVu.php`
- Implement tương tự PhongBan
- Thêm method `getByPhongBan($phongban_id)` - lấy chức vụ theo phòng ban
- Thêm method `getCountEmployees($id)`

### ✅ Nhiệm vụ 1.3: Refactor models khác
- Sửa `/models/TrinhDo.php`
- Sửa `/models/BangCap.php`
- Sửa `/models/ChuyenMon.php`
- Sửa `/models/QuocTich.php`
- Sửa `/models/DanToc.php`
- Sửa `/models/TonGiao.php`
- Sửa `/models/HonNhan.php`
- Sửa `/models/LoaiNhanVien.php`
- Tất cả follow cùng pattern như PhongBan

### ✅ Nhiệm vụ 1.4: Tạo giao diện quản lý danh mục
- Tạo file `/quan-ly-danh-muc.php` - menu chọn loại danh mục
- Tạo file `/danh-muc/phong-ban.php` - quản lý phòng ban
- Tạo file `/danh-muc/chuc-vu.php` - quản lý chức vụ
- Tạo file `/danh-muc/trinh-do.php` - quản lý trình độ
- Tạo file `/danh-muc/bang-cap.php` - quản lý bằng cấp
- Tạo file `/danh-muc/chuyên-môn.php` - quản lý chuyên môn
- Tạo file `/danh-muc/quoc-tich.php` - quản lý quốc tịch
- Tạo file `/danh-muc/dan-toc.php` - quản lý dân tộc
- Tạo file `/danh-muc/ton-giao.php` - quản lý tôn giáo
- Tạo file `/danh-muc/hon-nhan.php` - quản lý hôn nhân
- Tạo file `/danh-muc/loai-nhanvien.php` - quản lý loại nhân viên
- Mỗi file: DataTable + Form thêm/sửa/xóa

---

## GIAI ĐOẠN 2: TUẦN 3-4 (MODULE NHÂN VIÊN CHÍNH)

### ✅ Nhiệm vụ 2.1: Refactor model NhanVien
- Sửa file `/models/NhanVien.php` (Dev 1 sẽ make base methods, Dev 3 thêm business logic)
- Implement methods:
  - `getAll($filters)` - danh sách với filter
  - `getById($id)`
  - `getByMaNV($ma_nv)`
  - `create($data)` - thêm
  - `update($id, $data)` - sửa
  - `delete($id)` - xóa (soft delete)
  - `search($keyword, $filters)` - tìm kiếm
  - `getByPhongBan($pb_id)` - nhân viên theo phòng
  - `getByChucVu($cv_id)` - nhân viên theo chức vụ
  - `getByStatus($status)` - nhân viên active/inactive
  - `getTotalCount()` - tổng số nhân viên
- Add photo upload handling

### ✅ Nhiệm vụ 2.2: Build danh sách nhân viên
- Sửa file `/ds-nhan-vien.php`
- Implement:
  - DataTable với columns: Mã NV, Họ tên, Email, Phone, Phòng ban, Chức vụ, Trạng thái
  - Search bar tìm theo tên/mã/email/phone
  - Filter theo: Phòng ban, Chức vụ, Trạng thái, Trình độ
  - Sorting by columns
  - Pagination (10, 25, 50 records)
  - Buttons: View, Edit, Delete, Export
  - Bulk actions: Xóa nhiều, Thay đổi trạng thái
- **File action:** Sửa `/action/ds-nhan-vien-view-action.php`

### ✅ Nhiệm vụ 2.3: Build form thêm/sửa nhân viên
- Sửa file `/them-nhan-vien.php` - giao diện form
- Sửa file `/sua-nhan-vien.php` - giao diện form sửa
- Form fields:
  - Nhóm thông tin cơ bản: Họ tên, Giới tính, Ngày sinh, Nơi sinh
  - Nhóm liên hệ: Email, Phone, Địa chỉ, Tỉnh/Thành phố
  - Nhóm quốc gia: Quốc tịch, Dân tộc, Tôn giáo, Hộ khẩu
  - Nhóm công việc: Phòng ban, Chức vụ, Loại nhân viên, Trạng thái
  - Nhóm học vấn: Trình độ, Bằng cấp, Chuyên môn
  - Nhóm giấy tờ: CCCD, Nơi cấp, Ngày cấp
  - Upload ảnh đại diện
- Validation tất cả fields
- **File action:** Sửa `/action/nhan-vien-action.php`

### ✅ Nhiệm vụ 2.4: Build view chi tiết nhân viên
- Sửa file `/xem-nhan-vien.php`
- Hiển thị tất cả thông tin nhân viên
- Tabs:
  - Tab "Thông tin cơ bản"
  - Tab "Thông tin công việc"
  - Tab "Lương" - hiển thị lương hiện tại & lịch sử
  - Tab "Công tác" - hiển thị công tác
  - Tab "Khen thưởng/Kỷ luật"
  - Tab "Tài liệu" - file upload ứ tương ứng
- Buttons: Edit, Delete, Print, Back

---

## GIAI ĐOẠN 3: TUẦN 5-6 (NHÓM NHÂN VIÊN & CÔN CÔNG NƯỚC NGOÀI)

### ✅ Nhiệm vụ 3.1: Refactor model NhomNV
- Sửa file `/models/NhomNV.php`
- Refactor `/models/ChiTietNhom.php`
- Implement methods:
  - `getAll()` - danh sách nhóm
  - `getById($id)`
  - `create($data)` - thêm nhóm
  - `update($id, $data)`
  - `delete($id)` - xóa
  - `addEmployee($group_id, $nv_id)` - thêm nhân viên vào nhóm
  - `removeEmployee($group_id, $nv_id)` - lấy nhân viên ra
  - `getEmployees($group_id)` - lấy nhân viên trong nhóm

### ✅ Nhiệm vụ 3.2: Build giao diện Nhóm Nhân Viên
- Sửa file `/ds-nhom-nhan-vien.php` - danh sách nhóm
- Sửa file `/nhom-nhan-vien.php` - chi tiết nhóm
- Tạo file `/them-nhom-nhan-vien.php` - thêm nhóm
- Tạo file `/sua-nhom-nhan-vien.php` - sửa nhóm
- Implement:
  - Danh sách nhóm với DataTable
  - Form thêm/sửa: Tên nhóm, Mô tả, Người quản lý
  - Chi tiết nhóm: danh sách nhân viên trong nhóm
  - Thêm/lấy nhân viên ra khỏi nhóm (drag-drop hoặc select)
  - **File action:** Sửa `/action/nhom-nhan-vien-action.php`, `/action/chi-tiet-nhom-action.php`

### ✅ Nhiệm vụ 3.3: Refactor model CongTac
- Sửa file `/models/CongTac.php`
- Implement methods:
  - `getAll($filters)` - danh sách
  - `getById($id)`
  - `create($data)` - thêm công tác
  - `update($id, $data)`
  - `delete($id)`
  - `getByNhanVien($nv_id)` - công tác của nhân viên
  - `getByPeriod($start, $end)` - công tác trong khoảng thời gian

### ✅ Nhiệm vụ 3.4: Build giao diện Công Tác
- Sửa file `/cong-tac.php` - danh sách công tác
- Tạo file `/them-cong-tac.php` - thêm công tác mới (exists)
- Implement:
  - DataTable hiển thị: Nhân viên, từ-đến, nơi đi, lý do
  - Search/filter
  - Form thêm: Chọn nhân viên, ngày đi, ngày về, địa điểm, mục đích
  - **File action:** Sửa `/action/cong-tac-action.php`, `/action/them-cong-tac-action.php`

---

## GIAI ĐOẠN 4: TUẦN 7-8 (KHEN THƯỞNG/KỶ LUẬT & TÌM KIẾM NÂNG CAO)

### ✅ Nhiệm vụ 4.1: Refactor model KhenThuongKyLuat
- Sửa file `/models/KhenThuongKyLuat.php`
- Implement methods:
  - `getAll($filters)` - danh sách
  - `getById($id)`
  - `create($data)` - thêm
  - `update($id, $data)`
  - `delete($id)`
  - `getByNhanVien($nv_id)` - khen thưởng/kỷ luật của nhân viên
  - `getByType($type)` - khen thưởng hoặc kỷ luật
  - `getCountByNhanVien($nv_id)` - tổng số

### ✅ Nhiệm vụ 4.2: Build giao diện Khen Thưởng/Kỷ Luật
- Sửa file `/khen-thuong-ky-luat.php`
- Implement:
  - DataTable: Nhân viên, Loại (Khen/Kỷ luật), Nội dung, Ngày, Mô tả
  - Filter by type, by nhân viên, by date
  - Form thêm: Chọn nhân viên, Loại, Nội dung, Ngày, Mô tả, File đính kèm
  - Buttons: View, Edit, Delete
  - **File action:** Sửa `/action/khen-thuong-ky-luat-action.php`

### ✅ Nhiệm vụ 4.3: Build Tìm Kiếm Nâng Cao
- Sửa/tạo file `/tra-cuu-nhan-vien.php`
- Implement:
  - Advanced search form với nhiều criteria:
    - Tên nhân viên, Mã NV
    - Phòng ban (multi-select)
    - Chức vụ (multi-select)
    - Trạng thái (Active/Inactive)
    - Ngày sinh (từ - đến)
    - Trình độ, Bằng cấp
    - Khoảng lương (if có permission)
  - Kết quả search hiển thị DataTable
  - Buttons: View details, Export results, Print
  - Save search queries (favorites)
  - **File action:** Sửa `/action/tra-cuu-nhan-vien-action.php`

### ✅ Nhiệm vụ 4.4: Import/Export Nhân Viên
- Sửa file `/action/export_excel_nhanvien.php`
- Implement:
  - Export to Excel: Tất cả nhân viên hoặc search results
  - Columns: Ma NV, Hoten, Email, Phone, Phong ban, Chuc vu, Trinh do, Trang thai
  - Format: Professional template, header, footer
- Tạo hàm import:
  - Upload file Excel
  - Validate data
  - Insert vào database
  - Report errors (duplicate, invalid data)
  - **File action:** Tạo `/action/import-nhan-vien-action.php`

---

## 📂 Tổng hợp Files cần làm việc (DEV 3):

**Tạo mới:**
- `/danh-muc/` folder với các files: `phong-ban.php`, `chuc-vu.php`, `trinh-do.php`, `bang-cap.php`, `chuyên-môn.php`, `quoc-tich.php`, `dan-toc.php`, `ton-giao.php`, `hon-nhan.php`, `loai-nhanvien.php`
- `/quan-ly-danh-muc.php`
- `/sua-nhan-vien.php`
- `/them-nhom-nhan-vien.php`
- `/sua-nhom-nhan-vien.php`
- `/action/import-nhan-vien-action.php`

**Sửa hiện tại:**
- `/models/PhongBan.php`, `ChucVu.php`, `TrinhDo.php`, `BangCap.php`, `ChuyenMon.php`, `QuocTich.php`, `DanToc.php`, `TonGiao.php`, `HonNhan.php`, `LoaiNhanVien.php`
- `/models/NhanVien.php`, `NhomNV.php`, `ChiTietNhom.php`, `CongTac.php`, `KhenThuongKyLuat.php`
- `/ds-nhan-vien.php`, `/them-nhan-vien.php`, `/xem-nhan-vien.php`
- `/ds-nhom-nhan-vien.php`, `/nhom-nhan-vien.php`
- `/cong-tac.php`, `/them-cong-tac.php`
- `/khen-thuong-ky-luat.php`
- `/tra-cuu-nhan-vien.php`
- `/action/nhan-vien-action.php`, `nhom-nhan-vien-action.php`, `chi-tiet-nhom-action.php`, `cong-tac-action.php`, `them-cong-tac-action.php`, `khen-thuong-ky-luat-action.php`, `tra-cuu-nhan-vien-action.php`, `export_excel_nhanvien.php`

### 🔗 Phải phối hợp với:
- **Developer 1:** API endpoints cho danh mục & nhân viên
- **Developer 2:** Check permission khi CRUD
- **Developer 4:** Lương liên quan nhân viên

---

# 👨‍💻 DEVELOPER 4: TÍNH LƯƠNG, BÁOCÁO & DASHBOARD
**Vai trò:** Chuyên gia tính lương, báo cáo & phân tích

## GIAI ĐOẠN 1: TUẦN 1-2 (CÓ SỞ HẠ TẦNG TÍNH LƯƠNG)

### ✅ Nhiệm vụ 1.1: Phân tích công thức tính lương
- Hiểu logic tính lương hiện tại
- Tạo tài liệu công thức:
  - Lương cơ bản
  - Phụ cấp (phòng ban, chức vụ, kỹ năng...)
  - Thưởng (thực hiện, KPI...)
  - Bảo hiểm xã hội, bảo hiểm y tế
  - Thuế thu nhập cá nhân
  - Các khoản khác (vay vốn, công đoàn...)
- **File tạo:** `/LUONG_RULES.md` - tài liệu công thức

### ✅ Nhiệm vụ 1.2: Refactor model Luong
- Sửa file `/models/Luong.php`
- Tách riêng các hàm tính:
  - `calculateBaseSalary($nv_id, $year, $month)` - tính lương cơ bản
  - `calculateAllowances($nv_id, $year, $month)` - tính phụ cấp
  - `calculateBonus($nv_id, $year, $month)` - tính thưởng
  - `calculateInsurance($nv_id, $year, $month)` - tính bảo hiểm
  - `calculateTax($nv_id, $year, $month)` - tính thuế TNCN
  - `calculateDeductions($nv_id, $year, $month)` - tính các khoản khấu trừ
  - `calculateNetSalary($nv_id, $year, $month)` - tính lương thực lĩnh
  - `getPayroll($year, $month)` - tính sheet lương tháng
- Mỗi hàm return array chi tiết: `{amount, details}`

### ✅ Nhiệm vụ 1.3: Refactor model ChinhLuong
- Sửa file `/models/ChinhLuong.php`
- Implement methods:
  - `getHistory($nv_id, $year, $month)` - lịch sử chỉnh lương
  - `create($data)` - tạo chỉnh lương
  - `update($id, $data)` - sửa chỉnh lương
  - `delete($id)` - xóa chỉnh lương
  - `getEffective($nv_id, $year, $month)` - lương hiệu lực
  - `compare($old_id, $new_id)` - so sánh lương cũ mới
- Ghi log khi chỉnh lương

### ✅ Nhiệm vụ 1.4: Tạo Payroll Scheduler
- Tạo file `/classes/PayrollScheduler.php`
- Hàm `generateMonthlyPayroll($year, $month)` - tính lương tháng
- Hàm `recalculatePayroll($year, $month)` - tính lại lương
- Setup cron job tính lương tự động hàng tháng (ngày 25-26/tháng)
- Validation nước deposit (account, amount)
- **File:** `/cronjobs/monthly-payroll.php`
- **Mục đích:** Tính lương tự động, đảm bảo chính xác

---

## GIAI ĐOẠN 2: TUẦN 3-4 (QUẢN LÝ & CHỈNH LƯƠNG)

### ✅ Nhiệm vụ 2.1: Build giao diện Lương
- Sửa file `/luong.php`
- Implement:
  - DataTable hiển thị lương tháng/năm
  - Columns: Ma NV, Hoten, Luong co ban, Phu cap, Thuong, BHXH/BHYT, Thue, Tong lan
  - Filter: Khóa lương, Tháng, Năm, Phòng ban
  - Sort & Search
  - Buttons: View details, Edit, Export, Print
  - Button "Tính lương tháng" nếu chưa tính
  - Button "Khóa lương" để lock entry (prevent changes)
  - **File action:** Sửa `/action/luong-action.php`

### ✅ Nhiệm vụ 2.2: Build form Thêm/Cập Nhập Lương
- Tạo file `/add-luong.php` - thêm lương cho nhân viên mới
- Tạo file `/sua-luong.php` - sửa lương
- Form fields:
  - Chọn nhân viên (dropdown + search)
  - Tháng/Năm áp dụng
  - Lương cơ bản
  - Phụ cấp chức vụ
  - Phụ cấp khu vực
  - Phụ cấp khác
  - Hiển thị tự động: Tổng lương, Bảo hiểm, Thuế, Lương thực lĩnh
  - Button "Tính toán lại"
  - Ghi chú
  - Submit: Lưu
- **File action:** Tạo `/action/add-luong-action.php`

### ✅ Nhiệm vụ 2.3: Build giao diện Chỉnh Lương
- Sửa file `/chinh-luong.php`
- Implement:
  - DataTable hiển thị lịch sử thay đổi lương
  - Columns: Nhân viên, Từ lương, Đến lương, Ngày áp dụng, Người sửa, Ghi chú
  - Search & filter
  - Buttons: View details, Revert changes, Delete
  - "Xem chi tiết thay đổi" - so sánh lương cũ/mới
- Tạo file `/them-chinh-luong.php` - form thêm chỉnh lương
- Form:
  - Chọn nhân viên
  - Chọn loại chỉnh: Tăng/Giảm lương CB, Thay đổi phụ cấp, Thay đổi khác
  - Giá trị cũ (auto-fill)
  - Giá trị mới
  - Ngày áp dụng
  - Lý do (textbox)
  - Preview lương mới (tính năng)
  - Submit: Lưu chỉnh lương
- **File action:** Sửa `/action/chinh-luong-action.php`

### ✅ Nhiệm vụ 2.4: Lịch sử & Chi tiết Lương
- Sửa/tạo `/fetch-chinh-luong-history.php` - AJAX endpoint lấy lịch sử
- Sửa/tạo `/fetch-luong-details.php` - AJAX endpoint lấy chi tiết lương
- Methods:
  - `GET /fetch-chinh-luong-history.php?nv_id=1` - return JSON array
  - `GET /fetch-luong-details.php?nv_id=1&year=2024&month=02` - return JSON chi tiết

---

## GIAI ĐOẠN 3: TUẦN 5-6 (BÁOCÁO VÀ THỐNG KÊ)

### ✅ Nhiệm vụ 3.1: Build Thống Kê Lương
- Sửa file `/thong-ke-luong.php`
- Implement:
  - Filter: Tháng, Năm, Phòng ban (tuỳ chọn)
  - Hiển thị:
    - Tổng nhân viên: X người
    - Tổng lương để trả: X đồng
    - Trung bình lương: X đồng
    - Tổng bảo hiểm: X đồng
    - Tổng thuế: X đồng
  - Chart 1: Biểu đồ lương theo phòng ban (bar chart)
  - Chart 2: Phân bố lương (distribution - histogram)
  - Chart 3: Top 10 lương cao nhất (top salaries)
  - DataTable: Danh sách nhân viên + lương chi tiết
  - Button Export to Excel, Print

### ✅ Nhiệm vụ 3.2: Build Thống Kê Top
- Sửa file `/thong-ke-top.php`
- Implement:
  - Top 10 lương cao nhất
  - Top 10 lương thấp nhất
  - Top 10 phụ cấp cao nhất
  - Top 10 bảo hiểm cao nhất
  - Lương trung bình theo phòng ban
  - Lương trung bình theo chức vụ
  - Mỗi danh sách: DataTable sortable
  - Buttons: View details, Export

### ✅ Nhiệm vụ 3.3: Báo Cáo Chi Tiết Lương
- Tạo file `/bao-cao-luong.php` - báo cáo tổng hợp
- Implement:
  - Filter: Tháng, Năm, Phòng ban
  - Report format:
    - Header: Logo công ty, Tên báo cáo, Tháng/Năm
    - Summary: Tổng số nhân viên, Tổng lương, Trung bình
    - Detail table: Tất cả nhân viên + lương chi tiết
    - Footer: Người làm báo cáo, Ngày, Ký
  - Button: Export PDF, Export Excel, Print
  - CSS: Print-friendly layout

### ✅ Nhiệm vụ 3.4: Export & In Ấn
- Sửa file `/action/export_excel_luong.php`
- Implement:
  - Export lương tháng (tất cả hoặc filter)
  - Columns: Ma NV, Hoten, Luong CB, Phu cap, Thuong, Tong lan, BHXH, Thue, Luong TT
  - Format: Header, merged cells, colors
  - Multiple sheets: Sheet 1 = details, Sheet 2 = summary
- Sửa file `/action/print_luong.php` - in lương
- Implement:
  - Print preview
  - Khổ giấy: A4 landscape
  - Header, footer, page breaks
  - Format: Professional

---

## GIAI ĐOẠN 4: TUẦN 7-8 (DASHBOARD & TÔNG QUAN)

### ✅ Nhiệm vụ 4.1: Build Dashboard chính
- Sửa/tạo file `/tong-quan.php` (hoặc `/dashboard.php`)
- Implement:
  - **KPI Cards (top):**
    - Tổng nhân viên (số)
    - Tổng lương tháng (VND)
    - Trung bình lương (VND)
    - Lương cần thanh toán tháng này (VND)
  - **Charts:**
    - Chart 1 (line): Xu hướng lương 6 tháng gần nhất
    - Chart 2 (pie): Phân bố lương theo phòng ban
    - Chart 3 (bar): Lương top 5 phòng ban
  - **Recent activities:**
    - Danh sách 10 chỉnh lương gần đây
    - Danh sách 10 nhân viên mới
  - **Quick actions:**
    - Button "Tính lương tháng này"
    - Button "Xem báo cáo tháng trước"
    - Button "Cấp lương"

### ✅ Nhiệm vụ 4.2: Tạo Report Pages
- Tạo file `/reports/tong-hop.php` - tổng hợp all reports
- Implement:
  - Menu: Chọn loại báo cáo
  - Báo cáo lương theo tháng
  - Báo cáo lương theo phòng ban
  - Báo cáo thống kê nhân sự
  - Báo cáo biến động lương
  - Báo cáo chi phí nhân sự
  - Export all reports to Excel/PDF

### ✅ Nhiệm vụ 4.3: Tích hợp Charts Library
- Add Chart.js hoặc ApexCharts (từ npm/CDN)
- Charts sử dụng:
  - Line Chart: Xu hướng (trend)
  - Bar Chart: So sánh (comparison)
  - Pie/Doughnut: Phân bố (distribution)
  - Area Chart: Tổng hợp (cumulative)
  - Table: Data details
- Responsive design (mobile-friendly)

### ✅ Nhiệm vụ 4.4: Performance & Data Integrity
- Optimize queries cho reports (có developer 1)
- Cache report data (1 giờ)
- Validate data before display
- Handle edge cases (no data, divide by zero...)
- Test report dengan large dataset
- Performance < 3 giây load report

---

## 📂 Tổng hợp Files cần làm việc (DEV 4):

**Tạo mới:**
- `/classes/PayrollScheduler.php`
- `/cronjobs/monthly-payroll.php`
- `/add-luong.php`
- `/sua-luong.php`
- `/them-chinh-luong.php`
- `/bao-cao-luong.php`
- `/reports/` folder với các files báo cáo
- `/reports/tong-hop.php`
- `/LUONG_RULES.md` - tài liệu công thức

**Sửa hiện tại:**
- `/models/Luong.php`
- `/models/ChinhLuong.php`
- `/luong.php`
- `/chinh-luong.php`
- `/thong-ke-luong.php`
- `/thong-ke-top.php`
- `/tong-quan.php`
- `/fetch-chinh-luong-history.php`
- `/fetch-luong-details.php`
- `/action/luong-action.php`
- `/action/chinh-luong-action.php`
- `/action/export_excel_luong.php`
- `/action/print_luong.php`
- `/action/print_luong1.php`
- `/action/print_nhanvien.php` (cộc lại cho salary section)

### 🔗 Phải phối hợp với:
- **Developer 1:** API payroll endpoints, database optimization
- **Developer 3:** Dữ liệu nhân viên (lương liên quan đến nhân viên)
- **Developer 2:** Check permission khi CRUD lương

---

# 📅 TIMELINE CHI TIẾT

## GIAI ĐOẠN 1: TUẦN 1-2 - CHUẨN BỊ CƠ SỞ HẠ TẦNG

| Công việc | Dev | Thời gian | Deliverable |
|-----------|-----|----------|-------------|
| Database optimization | Dev 1 | 3 ngày | Optimized schema, indexes, triggers |
| Connection pooling setup | Dev 1 | 2 ngày | DatabaseConnection class |
| Logging system | Dev 1 | 1 ngày | Logger class, /logs folder |
| API framework setup | Dev 1 | 2 ngày | ApiResponse, ApiRequest, Router |
| Login refactor | Dev 2 | 3 ngày | New login.php, authentication logic |
| Session management | Dev 2 | 2 ngày | SessionManager class |
| Model refactor (danh mục) | Dev 3 | 3 ngày | PhongBan, ChucVu, TrinhDo, ... models |
| Danh mục UI | Dev 3 | 1 ngày | Basic danh mục pages structure |
| Payroll rules document | Dev 4 | 2 ngày | LUONG_RULES.md file |
| Model Luong refactor | Dev 4 | 2 ngày | Split salary calculation functions |

**Milestone:** Setup hoàn tất, sẵn sàng để dev module

---

## GIAI ĐOẠN 2: TUẦN 3-4 - PHÁT TRIỂN MODULE CHÍNH

| Công việc | Dev | Thời gian | Deliverable |
|-----------|-----|----------|-------------|
| API endpoints (nhân viên, danh mục) | Dev 1 | 4 ngày | All API routes, documentation |
| Postman collection | Dev 1 | 1 ngày | HRM-API.postman_collection.json |
| RBAC database setup | Dev 2 | 1 ngày | RBAC tables, migrations |
| Role/Permission models | Dev 2 | 2 ngày | Role.php, Permission.php models |
| Authorization middleware | Dev 2 | 2 ngày | Authorization.php middleware |
| Phan quyen UI | Dev 2 | 1 ngày | quan-ly-phan-quyen.php |
| Password reset | Dev 2 | 2 ngày | password-reset.php + action |
| Nhân viên models refactor | Dev 3 | 2 ngày | Improved NhanVien.php |
| Danh sách nhân viên | Dev 3 | 2 ngày | ds-nhan-vien.php with DataTable |
| Form thêm/sửa nhân viên | Dev 3 | 2 ngày | them-nhan-vien.php, sua-nhan-vien.php |
| Payroll scheduler | Dev 4 | 2 ngày | PayrollScheduler class, cron job |
| Lương UI (danh sách) | Dev 4 | 1 ngày | luong.php with DataTable |
| Add lương UI | Dev 4 | 2 ngày | add-luong.php, form logic |

**Milestone:** Core modules hoạt động, API sẵn sàng

---

## GIAI ĐOẠN 3: TUẦN 5-6 - MODULES PHỤ TRỢ VÀ BÁOCÁO

| Công việc | Dev | Thời gian | Deliverable |
|-----------|-----|----------|-------------|
| Query optimization | Dev 1 | 2 ngày | Optimized queries, caching strategy |
| Caching implementation | Dev 1 | 2 ngày | Cache.php, Redis/Memcached setup |
| Models refactor (complete) | Dev 3 | 2 ngày | All remaining models perfected |
| Nhóm nhân viên UI | Dev 3 | 2 ngày | ds-nhom-nhan-vien.php, nhom details |
| Công tác UI | Dev 3 | 1 ngày | cong-tac.php with employee movements |
| Khen thưởng/Kỷ luật UI | Dev 3 | 1 ngày | khen-thuong-ky-luat.php |
| Tìm kiếm nâng cao | Dev 3 | 1 ngày | tra-cuu-nhan-vien.php |
| Import nhân viên | Dev 3 | 1 ngày | import-nhan-vien-action.php |
| Chỉnh lương UI | Dev 4 | 2 ngày | chinh-luong.php, them-chinh-luong.php |
| Thống kê lương | Dev 4 | 2 ngày | thong-ke-luong.php with charts |
| Thống kê TOP | Dev 4 | 1 ngày | thong-ke-top.php |
| Báo cáo lương | Dev 4 | 1 ngày | bao-cao-luong.php print-friendly |

**Milestone:** Tất cả modules hoạt động, báo cáo cơ bản

---

## GIAI ĐOẠN 4: TUẦN 7-8 - HOÀN THIỆN & DASHBOARD

| Công việc | Dev | Thời gian | Deliverable |
|-----------|-----|----------|-------------|
| Security hardening | Dev 2 | 2 ngày | CSRF, input validation, sanitization |
| 2FA implementation | Dev 2 | 2 ngày | 2FA setup, Google Authenticator |
| JWT for APIs | Dev 2 | 1 ngày | JWT middleware, token endpoints |
| Audit logging | Dev 2 | 1 ngày | AuditLogger class, audit-logs.php |
| Export/Import nhân viên | Dev 3 | 1 ngày | export_excel_nhanvien.php |
| Export lương | Dev 4 | 1 ngày | export_excel_luong.php improved |
| Dashboard main | Dev 4 | 2 ngày | tong-quan.php with KPIs & charts |
| Reports integration | Dev 4 | 2 ngày | reports/ folder, all report pages |
| Charts integration | Dev 4 | 1 ngày | Chart.js/ApexCharts setup |

**Milestone:** Dashboard hoàn thiện, ready for testing

---

## TUẦN 9: INTEGRATION TESTING & BUG FIXES

| Công việc | Dev | Mô tả |
|-----------|-----|--------|
| End-to-end testing | Tất cả | Test workflow từ đầu đến cuối |
| Cross-module testing | Tất cả | Test interactions giữa modules |
| Performance testing | Dev 1 | Load testing, stress testing |
| Security testing | Dev 2 | SQL injection, XSS, CSRF tests |
| API testing | Dev 1 | Test all endpoints với Postman |
| Data integrity | Dev 1, 3, 4 | Test transactions, data consistency |
| Bug fixes | Tất cả | Fix issues từ testing |
| Documentation | Tất cả | Update README, inline comments |

---

## TUẦN 10: UAT & PRODUCTION DEPLOYMENT

| Công việc | Dev | Mô tả |
|-----------|-----|--------|
| UAT support | Tất cả | Hỗ trợ user testing |
| Final fixes | Tất cả | Fix issues từ UAT |
| Production setup | Dev 1 | Setup database, server config |
| Data migration | Dev 1, 3 | Migrate from old system if applicable |
| Go-live | Tất cả | Deploy to production |
| Post-launch support | Tất cả | Monitor & fix issues after launch |

---

# 👨‍💼 COLLABORATION & COMMUNICATION RULES

## Daily Standup (9:00 AM - 15 phút)

**Format:**
```
[Dev Name]
✅ Done yesterday: [what completed]
🔨 Doing today: [what working on now]
🚧 Blockers: [issues preventing work]
⚠️ Risks: [potential problems ahead]
```

**Ví dụ:**
```
[Dev 1 - Backend]
✅ Done: Completed database optimization & indexes
🔨 Doing: Building API endpoints for employees
🚧 Blockers: None
⚠️ Risks: May need Dev 3 feedback on employee fields

[Dev 2 - Security]
✅ Done: Login refactor & SessionManager
🔨 Doing: Testing RBAC system with Dev 3
🚧 Blockers: Waiting for API authentication from Dev 1
⚠️ Risks: 2FA implementation may take longer than expected
```

---

## Code Review Process

**Tất cả code changes phải:**
1. Create feature branch: `git checkout -b feature/dev{x}-{feature-name}`
2. Push to GitHub
3. Create Pull Request với description
4. Minimum 2 approvals từ team members
5. All CI checks pass
6. Merge vào develop branch

**PR Template:**
```
## Description
[Mô tả công việc]

## Files Changed
- /file1.php - Thay đổi gì
- /file2.php - Thay đổi gì

## Testing
- [x] Tested locally
- [x] No SQL errors
- [x] No XSS vulnerabilities
- [x] Responsive design

## Screenshots/Videos
[Attach if UI changes]

## Notes
[Any special considerations]
```

---

## Naming Conventions

### Branch Names
```
feature/dev1-database-optimization
feature/dev2-rbac-system
bugfix/dev3-employee-import
hotfix/login-session-bug
```

### Commit Messages
```
feat: implement password reset functionality
fix: correct salary calculation for bonus
docs: add API documentation for payroll
refactor: extract common validation logic
test: add unit tests for salary calculations
```

### PHP Code
```php
// Functions: camelCase (English)
public function calculateNetSalary() {}
public function getEmployeeByDepartment() {}

// Class names: PascalCase (English)
class AuthManager {}
class PayrollScheduler {}

// Constants: UPPER_SNAKE_CASE
const MAX_LOGIN_ATTEMPTS = 5;
const SESSION_TIMEOUT = 1800;

// Variables: snake_case (Vietnamese ok)
$tong_luong = 50000000;
$nhan_vien_id = 1;
$moi_nam = 2024;
```

### File Names
```
Models: {ModelName}.php             (e.g., NhanVien.php, Luong.php)
Classes: {ClassName}.php            (e.g., Logger.php, PayrollScheduler.php)
Pages: {feature-name}.php           (kebab-case, e.g., them-nhan-vien.php)
Actions: {feature-name}-action.php  (e.g., nhan-vien-action.php)
API: /api/routes/{resource}.php     (e.g., /api/routes/nhan-vien.php)
```

---

## Database & Schema Changes

**Tất cả schema changes phải:**
1. Tạo migration file `/database/migrations/{number}_{description}.sql`
2. Viết migration UP (create) và DOWN (revert)
3. Communicate với team trước khi apply
4. Test trên development environment trước
5. Backup before production deploy

**Ví dụ migration:**
```sql
-- /database/migrations/001_create_rbac_tables.sql
-- UP
CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- DOWN
DROP TABLE roles;
```

---

## API Interface Standards

**Tất cả API responses phải:**
```json
{
  "success": true/false,
  "message": "Cấp nhật lương thành công",
  "data": {...},
  "errors": {...},
  "timestamp": "2024-02-25T10:30:00Z"
}
```

**Error responses:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": "Email invalid",
    "phone": "Phone not valid"
  }
}
```

**Pagination:**
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "total": 150,
    "page": 1,
    "limit": 10,
    "pages": 15
  }
}
```

---

## Deployment Checklist

**Trước mỗi merge vào main:**
- [ ] Code review passed
- [ ] All tests passing
- [ ] No console errors/warnings
- [ ] Database migrations tested
- [ ] Performance acceptable
- [ ] Security review done
- [ ] Documentation updated
- [ ] Change log updated

---

# 📊 SUCCESS METRICS & KPIs

## Code Quality
- [x] PHẢI: Zero critical bugs trên production
- [x] PHẢI: All CRUD operations working
- [x] PHẢI: No SQL injection vulnerabilities
- [x] NÊN: Code coverage > 80%
- [x] NÊN: Cyclomatic complexity < 10

## Performance
- [x] PHẢI: Page load time < 2 seconds
- [x] PHẢI: API response time < 1 second
- [x] PHẢI: Database queries optimized (no N+1)
- [x] NÊN: 95% uptime

## Functionality
- [x] PHẢI: All modules working end-to-end
- [x] PHẢI: All exports (Excel/PDF) working
- [x] PHẢI: All calculations accurate
- [x] PHẢI: All reports generating correctly
- [x] NÊN: Mobile responsive

## Security
- [x] PHẢI: No SQL injection
- [x] PHẢI: No XSS vulnerabilities
- [x] PHẢI: CSRF protection enabled
- [x] PHẢI: Passwords hashed
- [x] PHẢI: Session management secure
- [x] NÊN: 2FA implemented

## User Experience
- [x] PHẢI: Intuitive UI/UX
- [x] PHẢI: Search & filter working
- [x] PHẢI: Forms validation clear
- [x] PHẢI: Error messages helpful
- [x] NÊN: Vietnamese localization

---

# 📞 CONTACT & ESCALATION

| Vai trò | Tên | Email | Phone | Role |
|---------|-----|-------|-------|------|
| Project Manager | [Your Name] | email@company.com | 0xxx-xxx-xxx | Lead & koordinasi |
| Backend Lead (Dev 1) | [Dev 1 Name] | dev1@company.com | 0xxx-xxx-xxx | Database & API |
| Security Lead (Dev 2) | [Dev 2 Name] | dev2@company.com | 0xxx-xxx-xxx | Auth & RBAC |
| Data Specialist (Dev 3) | [Dev 3 Name] | dev3@company.com | 0xxx-xxx-xxx | Employee data |
| Payroll Specialist (Dev 4) | [Dev 4 Name] | dev4@company.com | 0xxx-xxx-xxx | Salary & Reports |

### Escalation Path
1. **Blockers:** Consult với Lead developer hoặc PM
2. **Cross-module issues:** Schedule sync meeting
3. **Production issues:** Immediate Slack/Call
4. **Change requests:** Discuss trong standup

---

# 📚 RESOURCES & DOCUMENTATION

## Internal Documentation
- `/TASK_ASSIGNMENT.md` - File này (task details)
- `/README.md` - Setup & run instructions
- `/LUONG_RULES.md` - Payroll calculation rules
- `/API_DOCUMENTATION.md` - API endpoints spec
- `/SECURITY.md` - Security guidelines
- `/DATABASE.md` - Database schema documentation

## External Resources
- [PHP PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.0/)
- [DataTables Documentation](https://datatables.net/manual/)
- [Chart.js Documentation](https://www.chartjs.org/docs/latest/)

## Tools
- Git/GitHub - Version control
- Postman - API testing
- mysql-workbench - Database design
- VS Code - Code editor
- XAMPP - Local development server

---

**Document Version:** 1.0  
**Last Updated:** February 25, 2026  
**Maintained By:** [PM Name]  
**Status:** ACTIVE - In Development
