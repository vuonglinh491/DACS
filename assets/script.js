document.addEventListener('DOMContentLoaded', function() {
    // 1. THANH TÌM KIẾM
    const searchToggle = document.getElementById('searchToggle');
    const searchInput = document.getElementById('navSearchInput');

    if (searchToggle && searchInput) {
        searchToggle.onclick = function(e) {
            e.stopPropagation();
            searchInput.classList.toggle('active');
            if (searchInput.classList.contains('active')) searchInput.focus();
        };
        document.onclick = function(e) {
            if (!e.target.closest('.search-box-dynamic')) {
                searchInput.classList.remove('active');
            }
        };
    }

    // 2. ĐỊNH DẠNG TIỀN (CHỈ NHẬP SỐ + TỰ PHẨY)
    const moneyInputs = document.querySelectorAll('.money-input');
    moneyInputs.forEach(input => {
        input.oninput = function() {
            let value = this.value.replace(/\D/g, ""); // Xóa mọi ký tự không phải số
            if (value !== "") {
                this.value = Number(value).toLocaleString('en-US'); // Thêm dấu phẩy
            } else {
                this.value = "";
            }
        };
    });
});

// 3. DANH MỤC TRÊN ĐẦU
function selectTopCategory(el) {
    document.querySelectorAll('.cat-card-small').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
}

// 4. ƯU TIÊN HIỂN THỊ (CHỌN NHIỀU MỤC)
function selectPriority(el) {
    el.classList.toggle('active'); // Bật/tắt trạng thái chọn
    const icon = el.querySelector('i');
    
    if (el.classList.contains('active')) {
        icon.className = 'fas fa-check-circle'; // Hiện tích xanh
        icon.style.color = '#a855f7';
    } else {
        icon.className = 'far fa-circle'; // Về vòng tròn trống
        icon.style.color = '#ccc';
    }
}