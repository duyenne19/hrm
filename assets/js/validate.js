/**
 * validate.js - Hàm validate form dùng chung
 * -------------------------------------------
 * validateForm('#formPhongban', rules, labels)
 */

function validateForm(formSelector, rules, labels = {}) {
    const form = document.querySelector(formSelector);
    if (!form) return;

    form.addEventListener('submit', function (e) {
        let isValid = true;

        // Xóa thông báo lỗi cũ
        form.querySelectorAll('.error-msg').forEach(el => el.remove());

        Object.keys(rules).forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (!field) return;

            const value = field.value.trim();
            const fieldRules = rules[fieldName].split('|');

            for (let rule of fieldRules) {
                const label = getLabel(field, fieldName);

                if (rule === 'required' && value === '') {
                    showError(field, `${label} không được để trống`);
                    isValid = false;
                    break;
                }

                if (rule.startsWith('min:')) {
                    const min = parseInt(rule.split(':')[1]);
                    if (value.length < min) {
                        showError(field, `${label} phải có ít nhất ${min} ký tự`);
                        isValid = false;
                        break;
                    }
                }

                if (rule.startsWith('max:')) {
                    const max = parseInt(rule.split(':')[1]);
                    if (value.length > max) {
                        showError(field, `${label} không được vượt quá ${max} ký tự`);
                        isValid = false;
                        break;
                    }
                }

                if (rule === 'email' && value !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    showError(field, `${label} không hợp lệ`);
                    isValid = false;
                    break;
                }

                if (rule === 'number' && value !== '' && isNaN(value)) {
                    showError(field, `${label} phải là số`);
                    isValid = false;
                    break;
                }
            }
        });

        if (!isValid) e.preventDefault();
    });

    function showError(input, message) {
        const span = document.createElement('span');
        span.classList.add('error-msg');
        span.style.color = 'red';
        span.style.fontSize = '13px';
        span.textContent = message;
        input.insertAdjacentElement('afterend', span);
    }

    // Ưu tiên lấy label từ tham số truyền vào, sau đó mới đến <label for="">
    function getLabel(input, fieldName) {
        if (labels[fieldName]) return labels[fieldName];
        const labelEl = form.querySelector(`label[for="${input.id}"]`);
        return labelEl ? labelEl.textContent : fieldName;
    }
}
