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
            var submitBtn = contactForm.querySelector('button[type="submit"], .btn-submit, .contact-submit-btn');
            var originalBtnText = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) { submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...'; submitBtn.disabled = true; }

            var activeTopic = contactForm.querySelector('.topic-btn.active');
            var subject = activeTopic ? activeTopic.textContent.trim() : '';

            var body = new URLSearchParams({
                full_name: (contactForm.querySelector('[name=full_name]') || {value:''}).value,
                email:     (contactForm.querySelector('[name=email]')     || {value:''}).value,
                phone:     (contactForm.querySelector('[name=phone]')     || {value:''}).value,
                subject:   subject,
                message:   (contactForm.querySelector('[name=message], textarea') || {value:''}).value
            });

            var base = window.location.pathname.indexOf('/pages/') !== -1 ? '../' : '';
            fetch(base + 'actions/contact_action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: body.toString()
            })
            .then(r => r.json())
            .then(function(data) {
                var toast = document.getElementById('contact-toast');
                if (data.success) {
                    if (toast) { toast.classList.add('show'); setTimeout(() => toast.classList.remove('show'), 3500); }
                    else { showCartToast('Tin nhắn đã được gửi thành công!', 'success'); }
                    contactForm.reset();
                    var topics = document.querySelectorAll('.topic-btn');
                    topics.forEach((b, i) => b.classList.toggle('active', i === 0));
                } else {
                    showCartToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại.', 'error');
                }
            })
            .catch(function() { showCartToast('Lỗi kết nối.', 'error'); })
            .finally(function() {
                if (submitBtn) { submitBtn.innerHTML = originalBtnText; submitBtn.disabled = false; }
            });
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
       11. GIỎ HÀNG — THÊM VÀO GIỎ + HIỂN THỊ TOAST
    ========================================================== */

    // Helper: show toast
    function showCartToast(msg, type) {
        var t = document.getElementById('lk-toast');
        if (!t) {
            t = document.createElement('div');
            t.id = 'lk-toast';
            t.style.cssText = 'position:fixed;bottom:28px;right:28px;background:#1f2937;color:#fff;padding:12px 18px;border-radius:12px;font-size:14px;font-weight:500;display:flex;align-items:center;gap:10px;z-index:9999;transform:translateY(80px);opacity:0;transition:all .3s;max-width:300px;box-shadow:0 8px 24px rgba(0,0,0,.3);';
            document.body.appendChild(t);
        }
        var icon = type === 'success' ? '✅' : (type === 'error' ? '❌' : 'ℹ️');
        t.innerHTML = icon + ' <span>' + msg + '</span>';
        t.style.transform = 'translateY(0)';
        t.style.opacity = '1';
        clearTimeout(t._timer);
        t._timer = setTimeout(function() {
            t.style.transform = 'translateY(80px)';
            t.style.opacity = '0';
        }, 3000);
    }

    // Helper: update cart badge
    function updateCartBadge(count) {
        document.querySelectorAll('.cart-badge').forEach(function(badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        });
    }

    // Load cart count on page load
    if (typeof USER_LOGGED_IN !== 'undefined' && USER_LOGGED_IN) {
        fetch('../actions/cart_action.php?action=count')
            .then(r => r.json())
            .then(function(data) {
                if (data.success && data.cart_count > 0) updateCartBadge(data.cart_count);
            })
            .catch(function(){});
    }

    // Add to cart buttons
    document.querySelectorAll('.add-cart-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var isLoggedIn = (typeof USER_LOGGED_IN !== 'undefined') ? USER_LOGGED_IN : false;

            if (!isLoggedIn) {
                var currentPage = window.location.pathname.split('/').pop() || 'products.php';
                var loginUrl = (typeof LOGIN_URL !== 'undefined' ? LOGIN_URL : 'login.php');
                window.location.href = loginUrl + '?redirect_to=' + encodeURIComponent(currentPage);
                return;
            }

            var productId   = this.dataset.productId;
            var productName = this.dataset.productName || 'Sản phẩm';
            var originalHTML = this.innerHTML;
            var self = this;

            self.innerHTML = '⏳ Đang thêm...';
            self.disabled = true;

            var base = window.location.pathname.indexOf('/pages/') !== -1 ? '../' : '';

            fetch(base + 'actions/cart_action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=add&product_id=' + productId + '&quantity=1'
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    self.innerHTML = '✓ Đã thêm!';
                    self.style.background = '#16a34a';
                    showCartToast(data.message || 'Đã thêm vào giỏ hàng!', 'success');
                    if (data.cart_count !== undefined) updateCartBadge(data.cart_count);
                } else if (data.require_login) {
                    var loginUrl2 = (typeof LOGIN_URL !== 'undefined' ? LOGIN_URL : 'login.php');
                    window.location.href = loginUrl2;
                } else {
                    showCartToast(data.message || 'Không thể thêm sản phẩm.', 'error');
                }
            })
            .catch(function() { showCartToast('Lỗi kết nối.', 'error'); })
            .finally(function() {
                setTimeout(function() {
                    self.innerHTML = originalHTML;
                    self.disabled = false;
                    self.style.background = '';
                }, 2000);
            });
        });
    });

    /* ==========================================================
       12. GIỎ HÀNG — PANEL SLIDE (Cart Drawer)
    ========================================================== */
    document.querySelectorAll('.cart-icon-btn').forEach(function(cartBtn) {
        cartBtn.addEventListener('click', function() {
            var isLoggedIn = (typeof USER_LOGGED_IN !== 'undefined') ? USER_LOGGED_IN : false;
            if (!isLoggedIn) {
                var loginUrl = (typeof LOGIN_URL !== 'undefined' ? LOGIN_URL : 'login.php');
                window.location.href = loginUrl;
                return;
            }
            openCartDrawer();
        });
    });

    function openCartDrawer() {
        var drawer = document.getElementById('cartDrawer');
        var overlay = document.getElementById('cartOverlay');
        if (!drawer) { buildCartDrawer(); return; }
        loadCartDrawer();
        drawer.classList.add('open');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeCartDrawer() {
        var drawer = document.getElementById('cartDrawer');
        var overlay = document.getElementById('cartOverlay');
        if (drawer) drawer.classList.remove('open');
        if (overlay) overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    function buildCartDrawer() {
        // CSS toàn bộ drawer — inject 1 lần
        var style = document.createElement('style');
        style.textContent = `
            #cartOverlay{position:fixed;inset:0;background:rgba(0,0,0,.6);backdrop-filter:blur(3px);z-index:8000;opacity:0;pointer-events:none;transition:opacity .3s;}
            #cartOverlay.show{opacity:1!important;pointer-events:auto!important;}
            #cartDrawer{position:fixed;top:0;right:0;width:420px;max-width:100vw;height:100vh;z-index:8001;transform:translateX(100%);transition:transform .35s cubic-bezier(.4,0,.2,1);display:flex;flex-direction:column;background:#13111c;box-shadow:-12px 0 60px rgba(0,0,0,.5);}
            #cartDrawer.open{transform:translateX(0)!important;}

            /* Header */
            #cartDrawer .cd-header{padding:20px 24px 16px;border-bottom:1px solid rgba(255,255,255,.07);display:flex;justify-content:space-between;align-items:center;flex-shrink:0;}
            #cartDrawer .cd-title{margin:0;font-size:17px;font-weight:800;color:#fff;display:flex;align-items:center;gap:10px;}
            #cartDrawer .cd-title-icon{width:36px;height:36px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:15px;color:#fff;}
            #cartDrawer .cd-count-pill{background:rgba(139,92,246,.25);color:#a78bfa;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;border:1px solid rgba(139,92,246,.3);}
            #cartDrawer .cd-close{width:32px;height:32px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);border-radius:8px;cursor:pointer;color:#9ca3af;font-size:14px;display:flex;align-items:center;justify-content:center;transition:all .2s;}
            #cartDrawer .cd-close:hover{background:rgba(255,255,255,.12);color:#fff;}

            /* Body scroll */
            #cartBody{flex:1;overflow-y:auto;padding:8px 0;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.1) transparent;}
            #cartBody::-webkit-scrollbar{width:4px;}
            #cartBody::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:4px;}

            /* Item card */
            .cart-item-row{display:flex;align-items:center;gap:14px;padding:14px 24px;border-bottom:1px solid rgba(255,255,255,.05);transition:background .15s;}
            .cart-item-row:hover{background:rgba(255,255,255,.03);}
            .cart-item-row:last-child{border-bottom:none;}

            .ci-img{width:64px;height:64px;border-radius:12px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;}
            .ci-img img{width:100%;height:100%;object-fit:contain;padding:6px;}
            .ci-img .ci-noimg{font-size:24px;color:rgba(255,255,255,.2);}

            .ci-info{flex:1;min-width:0;}
            .ci-name{font-size:13px;font-weight:600;color:#e2e8f0;line-height:1.45;margin-bottom:10px;white-space:normal;word-break:break-word;}
            .ci-controls{display:flex;align-items:center;gap:0;}
            .ci-qty-wrap{display:flex;align-items:center;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:8px;overflow:hidden;}
            .ci-qty-btn{width:30px;height:30px;border:none;background:transparent;color:#c4b5fd;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s;line-height:1;}
            .ci-qty-btn:hover{background:rgba(139,92,246,.3);color:#fff;}
            .ci-qty-num{width:32px;text-align:center;font-size:14px;font-weight:700;color:#fff;border-left:1px solid rgba(255,255,255,.1);border-right:1px solid rgba(255,255,255,.1);height:30px;display:flex;align-items:center;justify-content:center;}
            .ci-del{margin-left:10px;width:30px;height:30px;border:1px solid rgba(239,68,68,.25);background:rgba(239,68,68,.08);border-radius:8px;cursor:pointer;color:#f87171;font-size:12px;display:flex;align-items:center;justify-content:center;transition:all .2s;flex-shrink:0;}
            .ci-del:hover{background:rgba(239,68,68,.25);color:#fff;border-color:rgba(239,68,68,.5);}

            .ci-right{display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0;}
            .ci-price{font-size:14px;font-weight:800;color:#a78bfa;}
            .ci-unit{font-size:11px;color:rgba(255,255,255,.3);}

            /* Footer */
            #cartFooter{padding:20px 24px;border-top:1px solid rgba(255,255,255,.07);background:#0f0d1a;flex-shrink:0;}
            .cf-row{display:flex;justify-content:space-between;align-items:center;font-size:13px;color:rgba(255,255,255,.45);margin-bottom:8px;}
            .cf-row.free{color:#34d399;}
            .cf-row.free .cf-val{color:#34d399;font-weight:600;}
            .cf-divider{height:1px;background:rgba(255,255,255,.07);margin:12px 0;}
            .cf-total-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;}
            .cf-total-label{font-size:15px;font-weight:700;color:#e2e8f0;}
            .cf-total-val{font-size:22px;font-weight:900;background:linear-gradient(135deg,#a78bfa,#6366f1);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
            .cf-ship-note{font-size:11px;color:#34d399;text-align:center;margin-bottom:12px;display:flex;align-items:center;justify-content:center;gap:4px;}
            .cf-checkout-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:15px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:12px;text-decoration:none;font-size:15px;font-weight:800;box-sizing:border-box;transition:opacity .2s,transform .15s;border:none;cursor:pointer;}
            .cf-checkout-btn:hover{opacity:.92;transform:translateY(-1px);}
            .cf-continue{display:block;text-align:center;margin-top:10px;font-size:13px;color:rgba(255,255,255,.35);text-decoration:none;cursor:pointer;transition:color .2s;}
            .cf-continue:hover{color:rgba(255,255,255,.65);}

            /* Empty state */
            .cd-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;padding:40px 24px;text-align:center;}
            .cd-empty-icon{width:80px;height:80px;background:rgba(255,255,255,.05);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:32px;color:rgba(255,255,255,.15);margin-bottom:20px;}
            .cd-empty h4{color:#e2e8f0;font-size:17px;margin:0 0 8px;}
            .cd-empty p{color:rgba(255,255,255,.35);font-size:14px;margin:0 0 24px;}
            .cd-empty-btn{padding:11px 28px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:10px;text-decoration:none;font-size:14px;font-weight:700;}

            /* Loading */
            .cd-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:14px;color:rgba(255,255,255,.3);font-size:14px;}
            .cd-spinner{width:36px;height:36px;border:3px solid rgba(139,92,246,.2);border-top-color:#8b5cf6;border-radius:50%;animation:cdSpin .7s linear infinite;}
            @keyframes cdSpin{to{transform:rotate(360deg)}}
        `;
        document.head.appendChild(style);

        // Overlay
        var overlay = document.createElement('div');
        overlay.id = 'cartOverlay';
        overlay.addEventListener('click', closeCartDrawer);
        document.body.appendChild(overlay);

        // Drawer
        var drawer = document.createElement('div');
        drawer.id = 'cartDrawer';
        drawer.innerHTML = `
            <div class="cd-header">
                <h3 class="cd-title">
                    <span class="cd-title-icon"><i class="fas fa-shopping-cart"></i></span>
                    Giỏ hàng của tôi
                    <span class="cd-count-pill" id="cdCountPill">0 sản phẩm</span>
                </h3>
                <button class="cd-close" onclick="closeCartDrawer()" title="Đóng">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="cartBody"></div>
            <div id="cartFooter"></div>
        `;
        document.body.appendChild(drawer);

        window.closeCartDrawer = closeCartDrawer;
        loadCartDrawer();
        setTimeout(function() {
            drawer.classList.add('open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }, 10);
    }

    function loadCartDrawer() {
        var body   = document.getElementById('cartBody');
        var footer = document.getElementById('cartFooter');
        if (!body) return;

        body.innerHTML = '<div class="cd-loading"><div class="cd-spinner"></div><span>Đang tải giỏ hàng...</span></div>';
        if (footer) footer.innerHTML = '';

        var base = window.location.pathname.indexOf('/pages/') !== -1 ? '../' : '';
        fetch(base + 'actions/cart_action.php?action=get', { credentials: 'same-origin' })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                // ✅ Fix: ép kiểu số rõ ràng để tránh string concatenation "041"
                var totalItems = 0;
                if (data.items && data.items.length > 0) {
                    data.items.forEach(function(item) {
                        totalItems += parseInt(item.quantity, 10) || 0;
                    });
                }

                // Cập nhật pill đếm
                var pill = document.getElementById('cdCountPill');
                if (pill) pill.textContent = totalItems + ' sản phẩm';
                updateCartBadge(totalItems);

                if (!data.success || !data.items || data.items.length === 0) {
                    body.innerHTML = `
                        <div class="cd-empty">
                            <div class="cd-empty-icon"><i class="fas fa-shopping-basket"></i></div>
                            <h4>Giỏ hàng trống</h4>
                            <p>Bạn chưa thêm sản phẩm nào.<br>Khám phá ngay nhé!</p>
                            <a href="${base}pages/products.php" class="cd-empty-btn" onclick="closeCartDrawer()">
                                <i class="fas fa-store"></i> Mua sắm ngay
                            </a>
                        </div>`;
                    if (footer) footer.innerHTML = '';
                    return;
                }

                // ✅ Build HTML trước, gán vào DOM sau — tránh render rỗng
                var grandTotal = 0;
                var html = '';
                data.items.forEach(function(item) {
                    var qty      = parseInt(item.quantity, 10) || 1;
                    var price    = parseFloat(item.price)      || 0;
                    var lineTotal = price * qty;
                    grandTotal   += lineTotal;

                    var imgHTML = item.image
                        ? '<img src="' + base + 'assets/imgs/' + item.image + '" alt="' + item.name + '" onerror="this.parentElement.innerHTML=\'<i class=\\\'fas fa-box ci-noimg\\\'></i>\'">'
                        : '<i class="fas fa-box ci-noimg"></i>';

                    html += '<div class="cart-item-row" id="ci-row-' + item.product_id + '">'
                          +   '<div class="ci-img">' + imgHTML + '</div>'
                          +   '<div class="ci-info">'
                          +     '<div class="ci-name">' + item.name + '</div>'
                          +     '<div class="ci-controls">'
                          +       '<div class="ci-qty-wrap">'
                          +         '<button class="ci-qty-btn" onclick="cartQtyDirect(' + item.product_id + ',' + qty + ',-1)" title="Giảm">−</button>'
                          +         '<div class="ci-qty-num" id="ci-qty-' + item.product_id + '">' + qty + '</div>'
                          +         '<button class="ci-qty-btn" onclick="cartQtyDirect(' + item.product_id + ',' + qty + ',1)" title="Tăng">+</button>'
                          +       '</div>'
                          +       '<button class="ci-del" onclick="cartRemove(' + item.product_id + ')" title="Xóa"><i class="fas fa-trash"></i></button>'
                          +     '</div>'
                          +   '</div>'
                          +   '<div class="ci-right">'
                          +     '<div class="ci-price" id="ci-price-' + item.product_id + '">' + Number(lineTotal).toLocaleString('vi-VN') + 'đ</div>'
                          +     '<div class="ci-unit">' + Number(price).toLocaleString('vi-VN') + 'đ / cái</div>'
                          +   '</div>'
                          + '</div>';
                });

                // ✅ Gán body trước, footer sau — đảm bảo items luôn hiện
                body.innerHTML = html;

                var shipping = grandTotal >= 5000000 ? 0 : 30000;
                var freeShipRemain = 5000000 - grandTotal;
                var footerHTML = '<div class="cf-row"><span>Tạm tính (' + totalItems + ' sp)</span><span>' + Number(grandTotal).toLocaleString('vi-VN') + 'đ</span></div>'
                    + '<div class="cf-row' + (shipping === 0 ? ' free' : '') + '"><span>Phí vận chuyển</span><span class="cf-val">' + (shipping === 0 ? 'Miễn phí 🎉' : Number(shipping).toLocaleString('vi-VN') + 'đ') + '</span></div>'
                    + (shipping > 0 ? '<div class="cf-ship-note"><i class="fas fa-truck"></i> Mua thêm <strong>' + Number(freeShipRemain).toLocaleString('vi-VN') + 'đ</strong> để miễn phí ship</div>' : '')
                    + '<div class="cf-divider"></div>'
                    + '<div class="cf-total-row"><span class="cf-total-label">Tổng cộng</span><span class="cf-total-val">' + Number(grandTotal + shipping).toLocaleString('vi-VN') + 'đ</span></div>'
                    + '<a href="' + base + 'pages/checkout.php" class="cf-checkout-btn"><i class="fas fa-lock"></i> Thanh toán ngay</a>'
                    + '<span class="cf-continue" onclick="closeCartDrawer()"><i class="fas fa-arrow-left"></i> Tiếp tục mua sắm</span>';

                if (footer) footer.innerHTML = footerHTML;
            })
            .catch(function(err) {
                console.error('Cart load error:', err);
                body.innerHTML = '<div class="cd-loading" style="color:#f87171;"><i class="fas fa-exclamation-triangle" style="font-size:32px;"></i><span>Không thể tải giỏ hàng.<br>Vui lòng thử lại.</span></div>';
            });
    }

    // cartQtyDirect: cập nhật UI ngay, sau đó gọi API (optimistic update)
    window.cartQtyDirect = function(productId, currentQty, delta) {
        var newQty = currentQty + delta;
        var base = window.location.pathname.indexOf('/pages/') !== -1 ? '../' : '';

        if (newQty <= 0) {
            // Xóa hẳn nếu về 0
            cartRemove(productId);
            return;
        }

        // Cập nhật UI ngay lập tức (optimistic)
        var qtyEl   = document.getElementById('ci-qty-' + productId);
        var priceEl = document.getElementById('ci-price-' + productId);
        if (qtyEl) qtyEl.textContent = newQty;

        // Cập nhật nút onclick với qty mới
        var row = document.getElementById('ci-row-' + productId);
        if (row) {
            var btns = row.querySelectorAll('.ci-qty-btn');
            if (btns[0]) btns[0].setAttribute('onclick', 'cartQtyDirect(' + productId + ',' + newQty + ',-1)');
            if (btns[1]) btns[1].setAttribute('onclick', 'cartQtyDirect(' + productId + ',' + newQty + ',1)');
        }

        fetch(base + 'actions/cart_action.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=update&product_id=' + productId + '&quantity=' + newQty,
            credentials: 'same-origin'
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            // Tải lại drawer để cập nhật tổng tiền chính xác
            if (data.cart_count !== undefined) updateCartBadge(data.cart_count);
            loadCartDrawer();
        })
        .catch(function() {
            // Rollback nếu lỗi
            if (qtyEl) qtyEl.textContent = currentQty;
            loadCartDrawer();
        });
    };

    window.cartQty = function(productId, delta) {
        // Legacy wrapper — lấy qty hiện tại từ DOM rồi gọi cartQtyDirect
        var qtyEl = document.getElementById('ci-qty-' + productId);
        var currentQty = qtyEl ? parseInt(qtyEl.textContent) || 1 : 1;
        cartQtyDirect(productId, currentQty, delta);
    };

    window.cartRemove = function(productId) {
        var base = window.location.pathname.indexOf('/pages/') !== -1 ? '../' : '';
        // Animation xóa item
        var row = document.getElementById('ci-row-' + productId);
        if (row) {
            row.style.transition = 'opacity .2s, transform .2s';
            row.style.opacity = '0';
            row.style.transform = 'translateX(20px)';
        }
        fetch(base + 'actions/cart_action.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=remove&product_id=' + productId,
            credentials: 'same-origin'
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.cart_count !== undefined) updateCartBadge(data.cart_count);
            setTimeout(function() { loadCartDrawer(); }, 200);
        })
        .catch(function() { loadCartDrawer(); });
    };

}); // end DOMContentLoaded

/* ============================================================
   PRODUCTS PAGE — BỘ LỌC & TÌM KIẾM CLIENT-SIDE
   ============================================================ */
(function() {
    var grid = document.getElementById('productGrid');
    if (!grid) return;

    function getItems() {
        return Array.from(grid.querySelectorAll('.product-item-box'));
    }

    var activeCategory = '';
    var activeSort     = '';
    var minPrice = 0, maxPrice = Infinity;

    function applyFilters() {
        var items = getItems();
        var searchVal = (document.getElementById('navSearchInput') || {value: ''}).value.toLowerCase().trim();

        items.forEach(function(item) {
            var cat   = (item.dataset.category || '').toLowerCase();
            var price = parseFloat(item.dataset.price) || 0;
            var name  = (item.querySelector('h5') || {textContent: ''}).textContent.toLowerCase();

            var catOk    = !activeCategory || cat.includes(activeCategory.toLowerCase()) || activeCategory === 'tất cả';
            var priceOk  = price >= minPrice && price <= maxPrice;
            var searchOk = !searchVal || name.includes(searchVal);

            item.style.display = (catOk && priceOk && searchOk) ? '' : 'none';
        });

        // Sort
        if (activeSort) {
            var visible = items.filter(function(i) { return i.style.display !== 'none'; });
            visible.sort(function(a, b) {
                var pa = parseFloat(a.dataset.price) || 0;
                var pb = parseFloat(b.dataset.price) || 0;
                var sa = parseFloat(a.dataset.sold)  || 0;
                var sb = parseFloat(b.dataset.sold)  || 0;
                if (activeSort === 'price-asc')  return pa - pb;
                if (activeSort === 'price-desc') return pb - pa;
                if (activeSort === 'best-sell')  return sb - sa;
                return 0;
            });
            visible.forEach(function(item) { grid.appendChild(item); });
        }
    }

    // Category click
    document.querySelectorAll('.cat-item').forEach(function(cat) {
        cat.addEventListener('click', function() {
            activeCategory = this.textContent.trim();
            applyFilters();
        });
    });

    // Sort click
    document.querySelectorAll('.selectable-list li').forEach(function(li) {
        li.addEventListener('click', function() {
            var txt = this.textContent.trim();
            if (txt.includes('Bán chạy'))   activeSort = 'best-sell';
            else if (txt.includes('thấp đến')) activeSort = 'price-asc';
            else if (txt.includes('cao đến')) activeSort = 'price-desc';
            else activeSort = '';
            applyFilters();
        });
    });

    // Price filter button
    var applyBtn = document.querySelector('.btn-purple-wide');
    if (applyBtn) {
        applyBtn.addEventListener('click', function() {
            var inputs = document.querySelectorAll('.money-input');
            minPrice = parseFloat((inputs[0] && inputs[0].dataset.rawValue) || 0) || 0;
            maxPrice = parseFloat((inputs[1] && inputs[1].dataset.rawValue) || 0) || Infinity;
            if (!maxPrice || maxPrice <= 0) maxPrice = Infinity;
            applyFilters();
        });
    }

    // Search input
    var searchInput = document.getElementById('navSearchInput');
    if (searchInput) {
        var debounce;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounce);
            debounce = setTimeout(applyFilters, 250);
        });
        // Enter key
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') applyFilters();
        });
    }
})();