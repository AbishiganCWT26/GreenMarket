/** Admin Master JS
 * Handles sidebar drawer, notifications, logout, facilitator alerts
 */

document.addEventListener("DOMContentLoaded", function () {

    // ----------------------------------------------------------------------
    // CSRF TOKEN
    // ----------------------------------------------------------------------
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : "";


    // ----------------------------------------------------------------------
    // 1. MOBILE SIDEBAR TOGGLE
    // ----------------------------------------------------------------------
    const mobileMenuBtn = document.getElementById("mobile-menu-btn");
    const sidebarClose = document.getElementById("sidebar-close");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener("click", function () {
            sidebar.classList.add("open");
            overlay.classList.add("active");
            document.body.style.overflow = "hidden";
        });
    }

    if (sidebarClose) {
        sidebarClose.addEventListener("click", function () {
            sidebar.classList.remove("open");
            overlay.classList.remove("active");
            document.body.style.overflow = "";
        });
    }

    if (overlay) {
        overlay.addEventListener("click", function () {
            sidebar.classList.remove("open");
            this.classList.remove("active");
            document.body.style.overflow = "";
        });
    }


    // ----------------------------------------------------------------------
    // 2. NOTIFICATION TOGGLER
    // ----------------------------------------------------------------------
    const notifBtn = document.getElementById("notifBtn");
    const notifDropdown = document.getElementById("notifDropdown");

    if (notifBtn && notifDropdown) {

        notifBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            e.preventDefault();
            const isHidden = notifDropdown.getAttribute("aria-hidden") === "true";
            notifDropdown.classList.toggle("show");
            notifDropdown.setAttribute("aria-hidden", isHidden ? "false" : "true");
        });

        // Close dropdown when clicking outside
        document.addEventListener("click", function (e) {
            if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.remove("show");
                notifDropdown.setAttribute("aria-hidden", "true");
            }
        });

        // Close dropdown on Esc key
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                notifDropdown.classList.remove("show");
                notifDropdown.setAttribute("aria-hidden", "true");
            }
        });
    }


    // ----------------------------------------------------------------------
    // 3. MARK ALL NOTIFICATIONS READ
    // ----------------------------------------------------------------------
    const markAllBtn = document.getElementById("markAllRead");
    const markAllUrl = document.body.dataset.urlMarkAllRead;

    if (markAllBtn && markAllUrl) {
        markAllBtn.addEventListener("click", function (e) {
            e.preventDefault();

            // Show loading state
            const originalText = markAllBtn.textContent;
            markAllBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
            markAllBtn.disabled = true;

            fetch(markAllUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({})
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.success) {
                        // Update UI
                        document.querySelectorAll('.notif-item.unread').forEach(item => {
                            item.classList.remove('unread');
                            item.classList.add('read');
                            const markBtn = item.querySelector('.mark-single');
                            if (markBtn) markBtn.remove();
                        });

                        // Remove notification dot
                        const notifDot = document.querySelector('.notif-dot');
                        if (notifDot) notifDot.remove();

                        // Update mark all button
                        markAllBtn.remove();

                        // Show success message
                        toastr.success('All notifications marked as read');
                    } else {
                        throw new Error(data.message || 'Failed to mark all as read');
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    Swal.fire("Error", "Unable to mark all as read", "error");
                })
                .finally(() => {
                    markAllBtn.innerHTML = originalText;
                    markAllBtn.disabled = false;
                });
        });
    }


    // ----------------------------------------------------------------------
    // 4. MARK SINGLE NOTIFICATION READ
    // ----------------------------------------------------------------------
    const markSingleUrl = document.body.dataset.urlMarkSingleRead;

    document.querySelectorAll(".mark-single").forEach((btn) => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            const id = this.dataset.id;
            const notifItem = this.closest('.notif-item');

            if (!markSingleUrl) return;

            // Show loading on button
            const originalHTML = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            this.disabled = true;

            fetch(markSingleUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ id: id })
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.success) {
                        // Update UI
                        notifItem.classList.remove('unread');
                        notifItem.classList.add('read');
                        this.remove();

                        // Update unread count
                        updateUnreadCount();
                    } else {
                        throw new Error(data.message || 'Failed to update notification');
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    this.innerHTML = originalHTML;
                    this.disabled = false;
                    Swal.fire("Error", "Unable to update notification", "error");
                });
        });
    });


    // ----------------------------------------------------------------------
    // 5. LOGOUT BUTTONS
    // ----------------------------------------------------------------------
    const sidebarLogoutBtn = document.getElementById("logout-button");
    const topLogoutBtn = document.getElementById("logoutTop");

    const logoutForm = document.getElementById("logout-form");
    const logoutFormTop = document.getElementById("logout-form-top");

    if (sidebarLogoutBtn && logoutForm) {
        sidebarLogoutBtn.addEventListener("click", (e) => {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out from the admin panel.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, logout!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    logoutForm.submit();
                }
            });
        });
    }

    if (topLogoutBtn && logoutFormTop) {
        topLogoutBtn.addEventListener("click", () => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out from the admin panel.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, logout!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    logoutFormTop.submit();
                }
            });
        });
    }


    // ----------------------------------------------------------------------
    // 6. ALERT FACILITATOR (optional)
    // ----------------------------------------------------------------------
    const alertUrl = document.body.dataset.urlAlertFacilitator;

    document.querySelectorAll(".alert-facilitator").forEach((btn) => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;

            if (!alertUrl) return;

            Swal.fire({
                title: 'Alert Facilitator?',
                text: "Are you sure you want to send an alert to the facilitator?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, send alert!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(alertUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                        },
                        body: JSON.stringify({ id: id })
                    })
                        .then((r) => r.json())
                        .then((data) => {
                            if (data.success) {
                                Swal.fire("Alert Sent", "Facilitator notified successfully", "success");
                            } else {
                                Swal.fire("Error", data.message || "Unable to send alert", "error");
                            }
                        })
                        .catch(() => Swal.fire("Error", "Unable to send alert", "error"));
                }
            });
        });
    });


    // ----------------------------------------------------------------------
    // 7. HELPER FUNCTIONS
    // ----------------------------------------------------------------------

    function updateUnreadCount() {
        const unreadItems = document.querySelectorAll('.notif-item.unread').length;
        const notifDot = document.querySelector('.notif-dot');
        const markAllBtn = document.getElementById('markAllRead');

        // Update notification dot
        if (unreadItems === 0 && notifDot) {
            notifDot.remove();
        } else if (unreadItems > 0 && !notifDot) {
            const notifBtn = document.getElementById('notifBtn');
            if (notifBtn) {
                const dot = document.createElement('span');
                dot.className = 'notif-dot';
                notifBtn.appendChild(dot);
            }
        }

        // Update mark all button
        if (unreadItems === 0 && markAllBtn) {
            markAllBtn.remove();
        }
    }

    // Initialize notification click handlers for entire items
    document.querySelectorAll('.notif-item').forEach(item => {
        item.addEventListener('click', function(e) {
            // Don't trigger if clicking on mark button
            if (e.target.closest('.mark-single')) return;

            const id = this.dataset.id;
            // You can add navigation to notification details here
            console.log('Notification clicked:', id);
        });
    });

});


// Initialize toastr
if (typeof toastr !== 'undefined') {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
}
