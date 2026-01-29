$(document).ready(function () {
    $('.info-item').each(function (index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });

    $('.item-row').each(function (index) {
        $(this).css('animation-delay', (index * 0.05) + 's');
    });

    $('.payment-card').each(function (index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });

    $('.action-buttons button').each(function (index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });

    $('.product-image').on('error', function () {
        $(this).attr('src', '/assets/images/product-placeholder.png');
    });

    $('.info-item').hover(
        function () {
            $(this).addClass('hover-active');
        },
        function () {
            $(this).removeClass('hover-active');
        }
    );

    $('.item-row').hover(
        function () {
            $(this).addClass('row-hover');
        },
        function () {
            $(this).removeClass('row-hover');
        }
    );

    $('.payment-card').hover(
        function () {
            $(this).addClass('payment-hover');
        },
        function () {
            $(this).removeClass('payment-hover');
        }
    );

    const statusColors = {
        'pending': '#F59E0B',
        'confirmed': '#3B82F6',
        'paid': '#10B981',
        'ready_for_pickup': '#8B5CF6',
        'completed': '#059669',
        'cancelled': '#EF4444'
    };

    $('.status-badge').each(function () {
        const status = $(this).data('status') || $(this).attr('class').match(/status-(\w+)/)?.[1];
        if (status && statusColors[status]) {
            $(this).css('border-left-color', statusColors[status]);
        }
    });

    initPrintFunctionality();
    initResponsiveBehavior();
    initSweetAlertDefaults();
});

function initPrintFunctionality() {
    $('.btn-print').click(function (e) {
        e.preventDefault();
        window.print();
    });
}

function initResponsiveBehavior() {
    $(window).resize(function () {
        const width = $(window).width();

        if (width <= 768) {
            $('.order-details-grid').addClass('mobile-view');
            $('.items-table-container').addClass('mobile-scroll');
            $('.action-buttons').addClass('mobile-stacked');
        } else {
            $('.order-details-grid').removeClass('mobile-view');
            $('.items-table-container').removeClass('mobile-scroll');
            $('.action-buttons').removeClass('mobile-stacked');
        }

        if (width <= 480) {
            $('.page-header').addClass('mobile-header');
            $('.status-badge').addClass('mobile-status');
        } else {
            $('.page-header').removeClass('mobile-header');
            $('.status-badge').removeClass('mobile-status');
        }
    }).trigger('resize');
}

function initSweetAlertDefaults() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    window.SwalToast = Toast;
}

window.markPaymentReceived = function (orderId) {
    Swal.fire({
        title: 'Mark Payment Received',
        html: `
			<div class="payment-modal">
				<div style="text-align: left; margin-bottom: 12px;">
					<label style="display: block; margin-bottom: 4px; font-weight: 500; color: var(--text-color); font-size: 11px;">Payment Method *</label>
					<select id="paymentMethod" class="swal2-input" style="width: 100%; padding: 6px 8px; border-radius: 4px; border: 1px solid var(--gray-border); font-size: 11px;">
						<option value="cash">Cash</option>
						<option value="bank">Bank Transfer</option>
						<option value="mobile_wallet">Mobile Wallet</option>
					</select>
				</div>
				<div style="text-align: left; margin-bottom: 12px;">
					<label style="display: block; margin-bottom: 4px; font-weight: 500; color: var(--text-color); font-size: 11px;">Transaction Number (Optional)</label>
					<input type="text" id="transactionNumber" class="swal2-input" placeholder="Enter transaction number" style="width: 100%; padding: 6px 8px; border-radius: 4px; border: 1px solid var(--gray-border); font-size: 11px;">
				</div>
			</div>
		`,
        showCancelButton: true,
        confirmButtonText: 'Mark as Paid',
        confirmButtonColor: '#10B981',
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        width: '400px',
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        },
        preConfirm: () => {
            const method = $('#paymentMethod').val();
            const transaction = $('#transactionNumber').val();

            if (!method) {
                Swal.showValidationMessage('Please select payment method');
                return false;
            }

            return { method, transaction };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: window.location.origin + '/lead-farmer/orders/mark-payment',
                method: 'POST',
                data: {
                    order_id: orderId,
                    payment_method: result.value.method,
                    transaction_number: result.value.transaction,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonColor: '#10B981',
                            timer: 2000,
                            showConfirmButton: false,
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown animate__faster'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutUp animate__faster'
                            }
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonColor: '#EF4444',
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown animate__faster'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutUp animate__faster'
                            }
                        });
                    }
                },
                error: function (xhr) {
                    Swal.close();
                    const error = xhr.responseJSON;
                    Swal.fire({
                        title: 'Error!',
                        text: error?.message || 'Something went wrong. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#EF4444',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown animate__faster'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp animate__faster'
                        }
                    });
                }
            });
        }
    });
}

window.updateOrderStatus = function (orderId, status) {
    const statusText = status.replace('_', ' ').toUpperCase();
    const statusColors = {
        'confirmed': '#3B82F6',
        'ready_for_pickup': '#8B5CF6',
        'completed': '#10B981',
        'cancelled': '#EF4444'
    };

    Swal.fire({
        title: 'Update Order Status',
        text: `Are you sure you want to mark this order as ${statusText}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Yes, mark as ${statusText}`,
        confirmButtonColor: statusColors[status] || '#10B981',
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: window.location.origin + '/lead-farmer/orders/update-status',
                method: 'POST',
                data: {
                    order_id: orderId,
                    status: status,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    Swal.fire({
                        title: 'Updating...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown animate__faster'
                        }
                    });
                },
                success: function (response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonColor: '#10B981',
                            timer: 1500,
                            showConfirmButton: false,
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown animate__faster'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutUp animate__faster'
                            }
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonColor: '#EF4444',
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown animate__faster'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutUp animate__faster'
                            }
                        });
                    }
                },
                error: function (xhr) {
                    Swal.close();
                    const error = xhr.responseJSON;
                    Swal.fire({
                        title: 'Error!',
                        text: error?.message || 'Something went wrong. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#EF4444',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown animate__faster'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp animate__faster'
                        }
                    });
                }
            });
        }
    });
}

window.cancelOrder = function (orderId) {
    Swal.fire({
        title: 'Cancel Order',
        text: 'Are you sure you want to cancel this order? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, cancel order',
        confirmButtonColor: '#EF4444',
        cancelButtonText: 'No, keep order',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        showCloseButton: true,
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.updateOrderStatus(orderId, 'cancelled');
        }
    });
}

$(document).on('click', '.btn-mark-paid', function () {
    const orderId = $(this).data('order-id') || $('.order-id-text').text().match(/\d+/)?.[0];
    if (orderId) {
        markPaymentReceived(orderId);
    }
});

$(document).on('click', '.btn-ready-pickup', function () {
    const orderId = $(this).data('order-id') || $('.order-id-text').text().match(/\d+/)?.[0];
    if (orderId) {
        updateOrderStatus(orderId, 'ready_for_pickup');
    }
});

$(document).on('click', '.btn-complete', function () {
    const orderId = $(this).data('order-id') || $('.order-id-text').text().match(/\d+/)?.[0];
    if (orderId) {
        updateOrderStatus(orderId, 'completed');
    }
});

$(document).on('click', '.btn-cancel', function () {
    const orderId = $(this).data('order-id') || $('.order-id-text').text().match(/\d+/)?.[0];
    if (orderId) {
        cancelOrder(orderId);
    }
});