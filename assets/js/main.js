/* ============================================================
   LKSECURE — MAIN.JS
   File JavaScript duy nhất cho toàn bộ project.
   Mỗi block kiểm tra sự tồn tại của element trước khi chạy
   => an toàn khi include trên mọi trang.
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    /* ==========================================================
       1. NAVBAR — THANH TÌM KIẾM ĐỘNG (dùng chung mọi trang)
    ========================================================== */
    const searchToggle = document.getElementById('searchToggle');
    const searchInput  = document.getElementById('navSearchInput');

    if (searchToggle && searchInput) {
        searchToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            searchInput.classList.toggle('active');
            if (searchInput.classList.contains('active')) searchInput.focus();
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.search-box-dynamic')) {
                searchInput.classList.remove('active');
            }
        });
    }

    /* ==========================================================
       2. PRODUCTS — CHỌN DANH MỤC (chỉ 1 ô active, click lại bỏ)
    ========================================================== */
    document.querySelectorAll('.cat-item').forEach(function (item) {
        item.addEventListener('click', function () {
            const isActive = this.classList.contains('active');
            document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
            if (!isActive) this.classList.add('active');
        });
    });

    /* ==========================================================
       3. PRODUCTS — UU TIEN HIEN THI (toggle, chon nhieu)
    ========================================================== */
    document.querySelectorAll('.selectable-list li').forEach(function (li) {
        li.addEventListener('click', function () {
            this.classList.toggle('active');
            const icon = this.querySelector('i');
            if (!icon) return;
            if (this.classList.contains('active')) {
                icon.className = 'fas fa-check-circle';
                icon.style.color = '#a855f7';
            } else {
                icon.className = 'far fa-circle';
                icon.style.color = '';
            }
        });
    });

    /* ==========================================================
       4. PRODUCTS — INPUT GIA: TU THEM DAU PHAY SAU MOI 3 SO
       Dung class="money-input" tren <input type="text">
    ========================================================== */
    document.querySelectorAll('.money-input').forEach(function (input) {
        // Format khi dang nhap
        input.addEventListener('input', function () {
            const raw = this.value.replace(/[^\d]/g, '');
            this.dataset.rawValue = raw;
            this.value = raw === '' ? '' : Number(raw).toLocaleString('vi-VN');
        });
        // Bo format khi focus de chinh sua thoai mai
        input.addEventListener('focus', function () {
            const raw = (this.dataset.rawValue || this.value.replace(/[^\d]/g, ''));
            this.value = raw;
        });
        // Format lai khi blur
        input.addEventListener('blur', function () {
            const raw = this.value.replace(/[^\d]/g, '');
            this.dataset.rawValue = raw;
            this.value = raw === '' ? '' : Number(raw).toLocaleString('vi-VN');
        });
    });

    /* ==========================================================
       5. HOME — DANH MUC NHANH TREN TRANG CHU
    ========================================================== */
    document.querySelectorAll('.cat-card-small').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.cat-card-small').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
        });
    });

    /* ==========================================================
       6. LOGIN — TOGGLE HIEN/AN MAT KHAU + VALIDATE FORM
    ========================================================== */
    const togglePassLogin = document.getElementById('toggle-pass');
    if (togglePassLogin) {
        togglePassLogin.addEventListener('click', function () {
            const input = document.getElementById('pass-input');
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        const validateEmailLogin = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
        loginForm.querySelectorAll('.form-control').forEach(function (input) {
            input.addEventListener('input', function () {
                const wrapper = this.parentElement;
                if (this.value === '') { wrapper.classList.remove('success', 'error'); return; }
                const isValid = (this.type === 'email') ? validateEmailLogin(this.value) : this.value.length >= 6;
                wrapper.classList.toggle('success', isValid);
                wrapper.classList.toggle('error', !isValid);
            });
        });
    }

    /* ==========================================================
       7. REGISTER — TOGGLE PASS + VALIDATE DAY DU
    ========================================================== */
    document.querySelectorAll('.toggle-pass').forEach(function (icon) {
        icon.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });

    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        const validateEmailReg  = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
        const validatePhone     = (v) => /^(0[35789])([0-9]{8})$/.test(v);

        const updateReq = (regex, val, sel) => {
            const el = registerForm.querySelector(sel);
            if (!el) return false;
            const ok = regex.test(val);
            el.style.color = ok ? '#10b981' : 'rgba(255,255,255,0.5)';
            const icon = el.querySelector('i');
            if (icon) icon.className = ok ? 'fa-solid fa-check' : 'fa-solid fa-circle-dot';
            return ok;
        };

        registerForm.querySelectorAll('.form-control').forEach(function (input) {
            input.addEventListener('input', function () {
                const wrapper = this.parentElement;
                const val = this.value.trim();
                if (val === '') { wrapper.classList.remove('success', 'error'); return; }

                let isValid = false;
                if      (this.name === 'email')            isValid = validateEmailReg(val);
                else if (this.name === 'full_name')        isValid = val.length >= 2;
                else if (this.name === 'phone')            isValid = val === '' || validatePhone(val);
                else if (this.name === 'password') {
                    const r1 = updateReq(/.{8,}/,  val, '.req-8');
                    const r2 = updateReq(/[A-Z]/,  val, '.req-upper');
                    const r3 = updateReq(/[a-z]/,  val, '.req-lower');
                    const r4 = updateReq(/[0-9]/,  val, '.req-number');
                    isValid = r1 && r2 && r3 && r4;
                } else if (this.name === 'confirm_password') {
                    const pass = registerForm.querySelector('input[name="password"]');
                    isValid = pass && val === pass.value && val !== '';
                }

                wrapper.classList.toggle('success', isValid);
                wrapper.classList.toggle('error', !isValid);
            });
        });
    }

    /* ==========================================================
       8. CONTACT — CHON CHU DE + SUBMIT TOAST
    ========================================================== */
    /* Global selectTopic for inline onclick usage */
    window.selectTopic = function (el) {
        document.querySelectorAll('.topic-btn').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
    };

    document.querySelectorAll('.topic-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.topic-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const toast = document.getElementById('contact-toast');
            if (toast) {
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 3500);
            }
            this.reset();
            const topics = document.querySelectorAll('.topic-btn');
            topics.forEach((b, i) => b.classList.toggle('active', i === 0));
        });
    }

    /* ==========================================================
       9. ABOUT — FALLBACK ANH + COUNTER ANIMATION + SCROLL CARDS
    ========================================================== */
    document.querySelectorAll('.team-photo').forEach(function (img) {
        img.addEventListener('error', function () { this.style.display = 'none'; });
        if (!img.src || img.src === window.location.href) img.style.display = 'none';
    });

    function animateCounters() {
        document.querySelectorAll('.stat-number').forEach(function (el) {
            const target = +el.getAttribute('data-target');
            const step   = target / (1800 / 16);
            let current  = 0;
            const timer  = setInterval(function () {
                current += step;
                if (current >= target) { current = target; clearInterval(timer); }
                el.textContent = Math.floor(current).toLocaleString('vi-VN');
            }, 16);
        });
    }

    const heroStats = document.querySelector('.about-hero-stats');
    if (heroStats) {
        new IntersectionObserver(function (entries, obs) {
            if (entries[0].isIntersecting) { animateCounters(); obs.disconnect(); }
        }, { threshold: 0.3 }).observe(heroStats);
    }

    const scrollCards = document.querySelectorAll('.value-card, .team-card, .timeline-content');
    if (scrollCards.length) {
        const cardObs = new IntersectionObserver(function (entries) {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
        }, { threshold: 0.15 });
        scrollCards.forEach(c => cardObs.observe(c));
    }

    /* ==========================================================
       10. WARRANTY & POLICY — ACCORDION + TAB SWITCHER
    ========================================================== */
    document.querySelectorAll('.accordion-header').forEach(function (header) {
        header.addEventListener('click', function () {
            const item  = this.closest('.accordion-item');
            const group = item.closest('.accordion-group');
            const isOpen = item.classList.contains('open');
            if (group) group.querySelectorAll('.accordion-item').forEach(i => i.classList.remove('open'));
            if (!isOpen) item.classList.add('open');
        });
    });

    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = this.dataset.tab;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            const panel = document.getElementById(target);
            if (panel) panel.classList.add('active');
        });
    });

    /* ==========================================================
       11. GIỎ HÀNG — KIỂM TRA ĐĂNG NHẬP TRƯỚC KHI THÊM
       Nếu chưa đăng nhập → chuyển sang trang login, kèm redirect_to
       Sau khi đăng nhập → tự quay về trang products
    ========================================================== */
    document.querySelectorAll('.add-cart-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            // USER_LOGGED_IN được inject từ PHP qua <script> trong products.php
            var isLoggedIn = (typeof USER_LOGGED_IN !== 'undefined') ? USER_LOGGED_IN : false;

            if (!isLoggedIn) {
                // Lấy URL trang hiện tại để redirect về sau khi đăng nhập
                var currentPage = window.location.pathname.split('/').pop() || 'products.php';
                var loginUrl = (typeof LOGIN_URL !== 'undefined' ? LOGIN_URL : 'login.php');
                window.location.href = loginUrl + '?redirect_to=' + encodeURIComponent(currentPage);
                return;
            }

            // Đã đăng nhập — xử lý thêm giỏ hàng
            var productId   = this.dataset.productId;
            var productName = this.dataset.productName || 'Sản phẩm';
            var originalText = this.textContent;

            this.textContent = '✓ Đã thêm!';
            this.disabled = true;
            this.style.background = '#16a34a';

            setTimeout(function () {
                btn.textContent = originalText;
                btn.disabled = false;
                btn.style.background = '';
            }, 1800);

            console.log('Thêm vào giỏ — productId:', productId, '| name:', productName);
            // TODO: Gọi API/action thêm sản phẩm vào giỏ hàng thực tế ở đây
        });
    });

}); // end DOMContentLoaded
