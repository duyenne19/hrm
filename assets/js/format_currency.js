function formatCurrency(input) {
        var cursorPosition = input.selectionStart;
        var originalLength = input.value.length;

        // Loại bỏ TẤT CẢ các ký tự không phải là số (Non-digit), kể cả dấu chấm và dấu phẩy cũ
        var num = input.value.replace(/\D/g, ''); 
        
        if (num === '') {
            input.value = '';
            return;
        }

        // Định dạng lại số với dấu phẩy phân cách hàng nghìn
        var formattedNum = num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        input.value = formattedNum;
        
        // Điều chỉnh lại vị trí con trỏ
        var newLength = input.value.length;
        var diff = newLength - originalLength;
        input.selectionStart = cursorPosition + diff;
        input.selectionEnd = cursorPosition + diff;
    }

    /**
     * Hàm định dạng cho HỆ SỐ (Cho phép số thập phân dùng dấu chấm '.', dùng dấu phẩy ',' làm phân cách hàng nghìn)
     */
    function formatDecimal(input) {
        var cursorPosition = input.selectionStart;
        var originalLength = input.value.length;
        
        // 1. Loại bỏ dấu phẩy (phân cách hàng nghìn) cũ để có được số thô
        let rawValue = input.value.replace(/,/g, ''); 

        // 2. Chỉ cho phép số và dấu chấm (dấu thập phân)
        // Lưu ý: Chúng ta cho phép dấu chấm, nhưng loại bỏ các ký tự khác
        rawValue = rawValue.replace(/[^\d.]/g, '');

        // 3. Xử lý phần thập phân: Tách phần nguyên và phần thập phân bằng dấu chấm đầu tiên
        let parts = rawValue.split('.');
        
        // Đảm bảo phần nguyên luôn là phần đầu tiên
        let integerPart = parts[0];
        // Phần thập phân là dấu chấm + các phần còn lại nếu có
        let decimalPart = parts.length > 1 ? '.' + parts.slice(1).join('') : '';

        // 4. Định dạng phần nguyên: Thêm dấu phẩy phân cách hàng nghìn
        let formattedIntegerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        // 5. Gán giá trị đã định dạng (Phần nguyên được định dạng + Phần thập phân nguyên gốc)
        input.value = formattedIntegerPart + decimalPart;
        
        // 6. Điều chỉnh lại vị trí con trỏ
        var newLength = input.value.length;
        var diff = newLength - originalLength;
        input.selectionStart = cursorPosition + diff;
        input.selectionEnd = cursorPosition + diff;
    }

    /**
     * Tự động định dạng giá trị ban đầu khi trang tải
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Lương cơ bản (Currency)
        const luongCobanInput = document.getElementById('nhap_tien');
        if (luongCobanInput && luongCobanInput.value) {
            formatCurrency(luongCobanInput);
        }
        
        // Hệ số lương & Phụ cấp (Decimal)
        const decimalInputs = document.querySelectorAll('#nhap_he_so');
        decimalInputs.forEach(input => {
            if (input.value) {
                formatDecimal(input);
            }
        });
    });