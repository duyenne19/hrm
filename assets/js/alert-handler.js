/**
 * ============================================
 *  alert-handler.js
 *  Dùng chung cho toàn bộ hệ thống
 *  --------------------------------------------
 *  Hỗ trợ:
 *   ✅ showSuccess(message) – Thông báo thành công
 *   ✅ showError(message)   – Thông báo thất bại
 *   ✅ showConfirmDelete(name, actionUrl) – Hộp thoại xác nhận xóa
 *   ✅ Tự động đọc ?status=&msg= để hiển thị thông báo
 * ============================================
 */

// ✅ Thông báo thành công
function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Thành công!',
        text: message,
        confirmButtonText: 'Đóng',
        timer: 2000,
        timerProgressBar: true
    });
}

// ✅ Thông báo thất bại
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Thất bại!',
        text: message,
        confirmButtonText: 'Đóng',
        confirmButtonColor: '#d33'
    });
}

// ✅ Hộp thoại xác nhận xóa (chuẩn chung toàn web)
function showConfirmDelete(name, actionUrl) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: `Bạn có chắc chắn muốn xóa "${name}" không?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Có xóa',
        cancelButtonText: 'Hủy',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = actionUrl;
        }
    });
}

// ✅ Tự động hiển thị thông báo dựa theo URL (?status=success|fail&msg=...)
document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const status = params.get('status');
    const msg = params.get('msg');

    if (status && msg) {
        const decodedMsg = decodeURIComponent(msg);
        if (status === 'success') {
            showSuccess(decodedMsg);
        } else if (status === 'fail' || status === 'error') {
            showError(decodedMsg);
        }

        // --- PHẦN ĐÃ SỬA ĐỔI ---

        // 1. Xóa các tham số status và msg khỏi đối tượng params
        params.delete('status');
        params.delete('msg');

        // 2. Lấy chuỗi truy vấn mới (chỉ còn lại ck_khenthuong và các tham số khác)
        const newQueryString = params.toString();

        // 3. Xây dựng URL mới
        let newURL = window.location.pathname;

        if (newQueryString) {
            newURL += '?' + newQueryString; // Nối chuỗi truy vấn đã lọc
        }

        // 4. Thay thế URL mới (chỉ có các tham số cần giữ lại)
        window.history.replaceState({}, document.title, newURL);

        // --- KẾT THÚC PHẦN SỬA ĐỔI ---
    }
});