// ✅ validator-tooltip.js (bản hoàn thiện)
document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll(".validate-tooltip");

    forms.forEach(form => {
        form.addEventListener("submit", function (e) {
            let valid = true;
            document.querySelectorAll(".tooltip-error").forEach(t => t.remove());

            form.querySelectorAll("input, select, textarea").forEach(field => {
                const value = field.value.trim();
                const msg = field.dataset.msg || "Vui lòng nhập thông tin hợp lệ";
                field.classList.remove("is-invalid");

                // 1️⃣ Required
                if (field.hasAttribute("required") && !value) {
                    showTooltip(field, "Không được để trống");
                    valid = false;
                }

                // 2️⃣ Số điện thoại 10 số
                if (field.name === "sodt" && value && !/^0\d{9}$/.test(value)) {
                    showTooltip(field, "Số điện thoại phải gồm 10 số và bắt đầu bằng 0");
                    valid = false;
                }

                // 3️⃣ minlength
                if (field.hasAttribute("minlength") && value.length < parseInt(field.getAttribute("minlength"))) {
                    showTooltip(field, msg);
                    valid = false;
                }

                // 4️⃣ So khớp (mật khẩu nhập lại)
                if (field.dataset.match) {
                    const matchField = form.querySelector(field.dataset.match);
                    if (matchField && value !== matchField.value) {
                        showTooltip(field, field.dataset.msg || "Giá trị không khớp");
                        valid = false;
                    }
                }

                // 5️⃣ Select bắt buộc chọn
                if (field.tagName === "SELECT" && field.hasAttribute("required") && !value) {
                    showTooltip(field, "Vui lòng chọn một giá trị");
                    valid = false;
                }
            });

            if (!valid) e.preventDefault();
        });
    });

    // ✅ Tooltip hiển thị thông minh (không tràn khỏi màn hình)
    function showTooltip(field, message) {
        field.classList.add("is-invalid");

        const tooltip = document.createElement("div");
        tooltip.className = "tooltip-error";
        tooltip.innerText = message;

        tooltip.style.position = "absolute";
        tooltip.style.background = "#dc3545";
        tooltip.style.color = "#fff";
        tooltip.style.padding = "4px 8px";
        tooltip.style.borderRadius = "4px";
        tooltip.style.fontSize = "13px";
        tooltip.style.zIndex = "9999";
        tooltip.style.whiteSpace = "nowrap";
        tooltip.style.boxShadow = "0 2px 6px rgba(0,0,0,0.2)";

        document.body.appendChild(tooltip);

        const rect = field.getBoundingClientRect();
        const scrollY = window.scrollY || document.documentElement.scrollTop;
        const scrollX = window.scrollX || document.documentElement.scrollLeft;
        const tooltipRect = tooltip.getBoundingClientRect();

        // Mặc định hiển thị bên phải
        let left = rect.left + scrollX + rect.width + 6;
        let top = rect.top + scrollY + rect.height / 2 - tooltipRect.height / 2;

        // Nếu bị tràn bên phải => chuyển sang trái
        if (left + tooltipRect.width > window.innerWidth - 10) {
            left = rect.left + scrollX - tooltipRect.width - 8;
            tooltip.classList.add("tooltip-left");
        }

        tooltip.style.left = `${left}px`;
        tooltip.style.top = `${top}px`;

        // Hiệu ứng fade-in
        tooltip.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 200, fill: "forwards" });

        // Xóa sau 3s
        setTimeout(() => tooltip.remove(), 3000);
    }
});
