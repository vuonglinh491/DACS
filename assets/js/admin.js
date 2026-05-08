/* ============================================================
   LKSECURE — ADMIN.JS
   Script cho toàn bộ trang quản trị admin
   Kiểm tra element tồn tại trước khi chạy — an toàn mọi nơi
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    /* ==========================================================
       1. SIDEBAR NAVIGATION — CHUYỂN PANEL
    ========================================================== */
    const navItems = document.querySelectorAll('.admin-nav-item[data-panel]');
    const panels   = document.querySelectorAll('.admin-panel');

    function switchPanel(panelId) {
        // Deactivate all
        navItems.forEach(n => n.classList.remove('active'));
        panels.forEach(p => p.classList.remove('active'));

        // Activate target
        const targetNav   = document.querySelector(`.admin-nav-item[data-panel="${panelId}"]`);
        const targetPanel = document.getElementById(panelId);

        if (targetNav)   targetNav.classList.add('active');
        if (targetPanel) targetPanel.classList.add('active');

        // Update topbar title
        const titleEl = document.getElementById('topbarTitle');
        const breadEl = document.getElementById('topbarBread');
        if (titleEl && targetNav) {
            titleEl.textContent = targetNav.querySelector('.nav-label')?.textContent || '';
        }
        if (breadEl && targetNav) {
            breadEl.textContent = targetNav.querySelector('.nav-label')?.textContent || '';
        }

        // Close sidebar on mobile
        if (window.innerWidth <= 900) {
            document.getElementById('adminSidebar')?.classList.remove('open');
        }
    }

    navItems.forEach(function (item) {
        item.addEventListener('click', function () {
            switchPanel(this.dataset.panel);
        });
    });

    /* ==========================================================
       2. MOBILE — TOGGLE SIDEBAR
    ========================================================== */
    const menuToggle = document.getElementById('menuToggle');
    const sidebar    = document.getElementById('adminSidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });

        // Close when clicking outside
        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 900 &&
                !sidebar.contains(e.target) &&
                !menuToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }

    /* ==========================================================
       3. TOAST NOTIFICATION
    ========================================================== */
    function showToast(message, type = 'success') {
        const toast = document.getElementById('adminToast');
        if (!toast) return;

        const iconMap = {
            success: 'fa-check-circle',
            error:   'fa-times-circle',
            warning: 'fa-exclamation-triangle'
        };

        toast.className = `admin-toast ${type}`;
        toast.innerHTML = `
            <i class="fas ${iconMap[type] || iconMap.success} toast-icon"></i>
            <span>${message}</span>
        `;
        toast.classList.add('show');

        clearTimeout(toast._timer);
        toast._timer = setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // Expose globally
    window.showAdminToast = showToast;

    /* ==========================================================
       4. MODAL HELPERS
    ========================================================== */
    function openModal(id) {
        const overlay = document.getElementById(id);
        if (overlay) overlay.classList.add('open');
    }

    function closeModal(id) {
        const overlay = document.getElementById(id);
        if (overlay) overlay.classList.remove('open');
    }

    window.openAdminModal  = openModal;
    window.closeAdminModal = closeModal;

    // Close on overlay click
    document.querySelectorAll('.admin-modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('open');
        });
    });

    // Close buttons
    document.querySelectorAll('.modal-close, [data-modal-close]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const modal = this.closest('.admin-modal-overlay');
            if (modal) modal.classList.remove('open');
        });
    });

    /* ==========================================================
       5. QUẢN LÝ NGƯỜI DÙNG
    ========================================================== */

    // Tìm kiếm người dùng
    const userSearch = document.getElementById('userSearch');
    if (userSearch) {
        userSearch.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();
            document.querySelectorAll('#userTableBody tr').forEach(function (row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(keyword) ? '' : 'none';
            });
        });
    }

    // Lọc theo vai trò
    const roleFilter = document.getElementById('roleFilter');
    if (roleFilter) {
        roleFilter.addEventListener('change', function () {
            const val = this.value;
            document.querySelectorAll('#userTableBody tr').forEach(function (row) {
                if (!val) { row.style.display = ''; return; }
                const roleCell = row.querySelector('.user-role-badge');
                row.style.display = (roleCell && roleCell.dataset.role === val) ? '' : 'none';
            });
        });
    }

    // Nút xem chi tiết user
    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-view-user')) {
            const btn  = e.target.closest('.btn-view-user');
            const name  = btn.dataset.name  || '—';
            const email = btn.dataset.email || '—';
            const phone = btn.dataset.phone || '—';
            const role  = btn.dataset.role  || '—';
            const date  = btn.dataset.date  || '—';

            document.getElementById('modalUserName').textContent  = name;
            document.getElementById('modalUserEmail').textContent = email;
            document.getElementById('modalUserPhone').textContent = phone;
            document.getElementById('modalUserRole').textContent  = role;
            document.getElementById('modalUserDate').textContent  = date;

            openModal('userDetailModal');
        }

        // Nút khóa/mở user
        if (e.target.closest('.btn-toggle-user')) {
            const btn = e.target.closest('.btn-toggle-user');
            const row = btn.closest('tr');
            const statusBadge = row?.querySelector('.status-badge');

            if (statusBadge) {
                const isActive = statusBadge.classList.contains('active');
                if (isActive) {
                    statusBadge.className = 'status-badge inactive';
                    statusBadge.textContent = 'Đã khóa';
                    btn.innerHTML = '<i class="fas fa-unlock"></i> Mở';
                    btn.className = 'action-btn edit btn-toggle-user';
                    showToast('Đã khóa tài khoản người dùng', 'warning');
                } else {
                    statusBadge.className = 'status-badge active';
                    statusBadge.textContent = 'Hoạt động';
                    btn.innerHTML = '<i class="fas fa-lock"></i> Khóa';
                    btn.className = 'action-btn lock btn-toggle-user';
                    showToast('Đã mở khóa tài khoản', 'success');
                }
            }
        }

        // Nút xóa user
        if (e.target.closest('.btn-delete-user')) {
            if (confirm('Bạn có chắc muốn xóa người dùng này không?')) {
                const row = e.target.closest('tr');
                row?.remove();
                showToast('Đã xóa người dùng', 'error');
                updateUserCount();
            }
        }
    });

    function updateUserCount() {
        const count = document.querySelectorAll('#userTableBody tr').length;
        const el = document.getElementById('userTotalCount');
        if (el) el.textContent = count + ' người dùng';
    }

    /* ==========================================================
       6. THÊM SẢN PHẨM
    ========================================================== */
    const addProductForm = document.getElementById('addProductForm');

    // Image upload preview
    const imgInput = document.getElementById('productImages');
    const previewGrid = document.getElementById('imgPreviewGrid');

    if (imgInput && previewGrid) {
        imgInput.addEventListener('change', function () {
            const files = Array.from(this.files);
            previewGrid.innerHTML = '';

            files.forEach(function (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const div = document.createElement('div');
                    div.className = 'img-preview-item';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="">
                        <span class="img-preview-remove" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </span>
                    `;
                    previewGrid.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    // Drag & drop on upload zone
    const uploadZone = document.getElementById('uploadZone');
    if (uploadZone) {
        uploadZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.style.borderColor = 'var(--primary)';
            this.style.background  = 'rgba(139,92,246,0.1)';
        });
        uploadZone.addEventListener('dragleave', function () {
            this.style.borderColor = '';
            this.style.background  = '';
        });
        uploadZone.addEventListener('drop', function (e) {
            e.preventDefault();
            this.style.borderColor = '';
            this.style.background  = '';
            if (imgInput) {
                imgInput.files = e.dataTransfer.files;
                imgInput.dispatchEvent(new Event('change'));
            }
        });
        uploadZone.addEventListener('click', function () {
            if (imgInput) imgInput.click();
        });
    }

    // Thêm dòng thông số kỹ thuật
    const addSpecBtn = document.getElementById('addSpecRow');
    const specsBody  = document.getElementById('specsBody');

    if (addSpecBtn && specsBody) {
        addSpecBtn.addEventListener('click', function () {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input class="admin-input" placeholder="Tên thông số" style="margin:0"></td>
                <td><input class="admin-input" placeholder="Giá trị" style="margin:0"></td>
                <td>
                    <button type="button" class="action-btn delete" onclick="this.closest('tr').remove()">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            specsBody.appendChild(row);
        });
    }

    // Submit form thêm sản phẩm
    if (addProductForm) {
        addProductForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validate
            const name  = document.getElementById('productName')?.value.trim();
            const price = document.getElementById('productPrice')?.value.trim();
            const stock = document.getElementById('productStock')?.value.trim();

            if (!name || !price || !stock) {
                showToast('Vui lòng điền đủ thông tin bắt buộc!', 'error');
                return;
            }

            // Simulate save
            const btn = this.querySelector('[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
            }

            setTimeout(function () {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check"></i> Lưu sản phẩm';
                }
                showToast('Đã thêm sản phẩm thành công!', 'success');
                addProductForm.reset();
                if (previewGrid) previewGrid.innerHTML = '';
            }, 1200);
        });
    }

    /* ==========================================================
       7. KIỂM TRA ĐƠN HÀNG
    ========================================================== */

    // Tìm kiếm đơn hàng
    const orderSearch = document.getElementById('orderSearch');
    if (orderSearch) {
        orderSearch.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();
            document.querySelectorAll('#orderTableBody tr').forEach(function (row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(keyword) ? '' : 'none';
            });
        });
    }

    // Lọc theo trạng thái
    const statusFilter = document.getElementById('orderStatusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function () {
            const val = this.value;
            document.querySelectorAll('#orderTableBody tr').forEach(function (row) {
                if (!val) { row.style.display = ''; return; }
                const badge = row.querySelector('.status-badge');
                row.style.display = (badge && badge.dataset.status === val) ? '' : 'none';
            });
        });
    }

    // Xem chi tiết đơn hàng
    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-view-order')) {
            const btn = e.target.closest('.btn-view-order');
            const orderId = btn.dataset.id;

            document.getElementById('modalOrderId').textContent = '#' + orderId;
            openModal('orderDetailModal');
        }

        // Cập nhật trạng thái đơn hàng
        if (e.target.closest('.btn-update-status')) {
            const btn    = e.target.closest('.btn-update-status');
            const select = btn.previousElementSibling;
            if (!select) return;

            const newStatus = select.value;
            const row = btn.closest('tr');
            const badge = row?.querySelector('.status-badge');

            const statusMap = {
                pending:    { label: 'Chờ xử lý',    cls: 'pending' },
                processing: { label: 'Đang xử lý',   cls: 'processing' },
                shipping:   { label: 'Đang giao',     cls: 'shipping' },
                delivered:  { label: 'Đã giao',       cls: 'delivered' },
                cancelled:  { label: 'Đã hủy',        cls: 'cancelled' }
            };

            if (badge && statusMap[newStatus]) {
                badge.className = `status-badge ${statusMap[newStatus].cls}`;
                badge.dataset.status = newStatus;
                badge.textContent = statusMap[newStatus].label;
                showToast(`Đã cập nhật đơn hàng sang: ${statusMap[newStatus].label}`, 'success');
            }
        }
    });

    /* ==========================================================
       8. THỐNG KÊ — DATE FILTER TABS
    ========================================================== */
    document.querySelectorAll('.date-filter-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            const group = this.closest('.date-filter-tabs');
            if (group) group.querySelectorAll('.date-filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Animate bars
            animateBars();
        });
    });

    function animateBars() {
        document.querySelectorAll('.bar').forEach(function (bar) {
            const origHeight = bar.style.height;
            bar.style.height = '0';
            setTimeout(function () {
                bar.style.height = origHeight;
            }, 50);
        });
    }

    // Progress bars — animate on visible
    const progressFills = document.querySelectorAll('.progress-bar-fill');
    if (progressFills.length) {
        const progObs = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const width = el.dataset.width || '0%';
                    el.style.width = '0';
                    setTimeout(function () { el.style.width = width; }, 100);
                    progObs.unobserve(el);
                }
            });
        }, { threshold: 0.3 });

        progressFills.forEach(el => progObs.observe(el));
    }

    /* ==========================================================
       9. STATS — COUNTER ANIMATION
    ========================================================== */
    function animateValue(el, start, end, duration) {
        let startTime = null;
        const step = function (timestamp) {
            if (!startTime) startTime = timestamp;
            const progress = Math.min((timestamp - startTime) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            el.textContent = value.toLocaleString('vi-VN');
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }

    const statValues = document.querySelectorAll('.stat-value[data-count]');
    if (statValues.length) {
        const countObs = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    const el  = entry.target;
                    const end = parseInt(el.dataset.count);
                    if (!isNaN(end)) animateValue(el, 0, end, 1200);
                    countObs.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        statValues.forEach(el => countObs.observe(el));
    }

    /* ==========================================================
       10. LOGOUT
    ========================================================== */
    const logoutBtn = document.getElementById('adminLogout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function () {
            if (confirm('Bạn có chắc muốn đăng xuất không?')) {
                window.location.href = '../actions/logout.php';
            }
        });
    }

    /* ==========================================================
       11. REAL-TIME CLOCK trên topbar
    ========================================================== */
    const clockEl = document.getElementById('adminClock');
    if (clockEl) {
        function updateClock() {
            const now = new Date();
            const hours   = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${hours}:${minutes}:${seconds}`;
        }
        updateClock();
        setInterval(updateClock, 1000);
    }

}); // end DOMContentLoaded