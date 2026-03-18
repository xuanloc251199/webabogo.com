$(document).ready(function () {


    function formatPrice(price) {
        if (typeof price !== 'number') {
            price = parseFloat(price);
        }
        if (isNaN(price)) {
            price = 0;
        }
        return price.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,') + 'đ';
    }


    // ===== START: CART FUNCTIONALITY - REMOVE ITEM FROM CART =====
    $(document).on('click', '.remove-cart-item', function (e) {
        e.preventDefault();

        const $btn = $(this);
        const url = $btn.data('url');
        const rowId = $btn.data('row-id');

        if (!url) return;

        // Show SweetAlert2 confirmation
        Swal.fire({
            title: 'Xác nhận xóa',
            text: 'Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Add loading state
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang xóa...');

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (response) {
                        if (response.error) {
                            // Use Botble's error notification
                            if (typeof Theme !== 'undefined' && Theme.showError) {
                                Theme.showError(response.message || 'Có lỗi xảy ra khi xóa sản phẩm');
                            } else {
                                Swal.fire({
                                    title: 'Lỗi!',
                                    text: response.message || 'Có lỗi xảy ra khi xóa sản phẩm',
                                    icon: 'error'
                                });
                            }
                            $btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Xóa');
                        } else {
                            // Use Botble's success notification (Toast)
                            if (typeof Theme !== 'undefined' && Theme.showSuccess) {
                                Theme.showSuccess(response.message || 'Đã xóa sản phẩm khỏi giỏ hàng thành công!');
                            } else {
                                Swal.fire({
                                    title: 'Thành công!',
                                    text: response.message || 'Đã xóa sản phẩm khỏi giỏ hàng',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }

                            // Remove the cart item with smooth animation
                            $btn.closest('.cart-item').fadeOut(300, function () {
                                $(this).remove();

                                // Check if cart is empty
                                if ($('.cart-item').length === 0) {
                                    // Show info message before reload
                                    if (typeof Theme !== 'undefined' && Theme.showNotice) {
                                        Theme.showNotice('info', 'Giỏ hàng trống. Đang tải lại trang...');
                                        setTimeout(() => window.location.reload(), 1500);
                                    } else {
                                        Swal.fire({
                                            title: 'Thông tin',
                                            text: 'Giỏ hàng trống. Đang tải lại trang...',
                                            icon: 'info',
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            window.location.reload();
                                        });
                                    }
                                }
                            });

                            // Update cart count if available
                            if (response.data && response.data.count !== undefined) {
                                $('[data-bb-value="cart-count"]').text(response.data.count);
                                $('.cart-count').text(response.data.count);

                                // Update header cart icon badge
                                const $headerCartBadge = $('.header-actions li').find("a[href$='/cart'] span");
                                if ($headerCartBadge.length) {
                                    $headerCartBadge.text(response.data.count);
                                }
                            }

                            // Update cart totals with formatting
                            if (response.data) {
                                if (response.data.subtotal) {
                                    $('[data-bb-value="cart-subtotal"]').text(response.data.subtotal);
                                }
                                if (response.data.total) {
                                    $('[data-bb-value="cart-total"]').text(response.data.total);
                                }
                                if (response.data.tax) {
                                    $('[data-bb-value="cart-tax"]').text(response.data.tax);
                                }
                            }
                        }
                    },
                    error: function (xhr) {
                        console.error('Error removing item:', xhr);

                        // Use Botble's error handling
                        if (typeof Theme !== 'undefined' && Theme.handleError) {
                            Theme.handleError(xhr);
                        } else if (typeof Theme !== 'undefined' && Theme.showError) {
                            Theme.showError('Có lỗi xảy ra khi xóa sản phẩm. Vui lòng thử lại.');
                        } else {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: 'Có lỗi xảy ra khi xóa sản phẩm. Vui lòng thử lại.',
                                icon: 'error'
                            });
                        }

                        $btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Xóa');
                    }
                });
            }
        });
    });
    // ===== END: CART FUNCTIONALITY - REMOVE ITEM FROM CART =====

    // ===== START: CART FUNCTIONALITY - UPDATE TOTAL PRICE CALCULATION =====
    function updateTotalPriceCart() {
        let amount_cart = 0;

        $(".total-price-item").each(function () {
            let qty = parseInt($(this).data("qty"), 0) || 0;
            let price = parseInt($(this).data("price"), 0) || 0;
            let addon = parseInt($(this).data("addon"), 0) || 0;
            let children_surplus = parseInt($(this).data("children-surplus"), 0) || 0;

            let total_price_cart_item = qty * price + addon + children_surplus;

            let total_price_service = 0;
            $(this).parents(".cart-item-details").find(".service-quantity-input").each(function () {
                let qty_service = parseInt($(this).val(), 10) || 0;
                let price_service = parseInt($(this).data("price"), 10) || 0;
                let service_total = qty_service * price_service;

                total_price_service += service_total;

                $(this).parents(".service-item").find(".service-total").text(formatPrice(service_total));
            });

            total_price_cart_item += total_price_service;
            amount_cart += total_price_cart_item;

            $(this).text(formatPrice(total_price_cart_item));
        });

        $(".amount_cart").text(formatPrice(amount_cart));

        let coupon_discount_amount = parseInt($(".coupon-discount-amount").data("price"), 10) || 0;
        let promotion_discount_amount = parseInt($(".promotion-discount-amount").data("price"), 10) || 0;

        let final_total = amount_cart - coupon_discount_amount - promotion_discount_amount;
        $(".total_amount_carts").text(formatPrice(final_total));
    }

    // ===== END: CART FUNCTIONALITY - UPDATE TOTAL PRICE CALCULATION =====


    // ===== START: CART FUNCTIONALITY - SERVICE QUANTITY CONTROLS =====
    // Handle plus button click for services in cart
    $(document).on('click', '.plus-service', function () {
        let $inputQty = $(this).parent().find(".service-quantity-input");
        let qty = $inputQty.val();
        qty++;
        if (qty > 99) {
            qty = 99;
        }
        $inputQty.val(qty);
        $(this).parents(".cart-item").find(".update-cart-item").removeClass("d-none");
        updateTotalPriceCart();

    });

    // Handle minus button click for services in cart
    $(document).on('click', '.minus-service', function () {
        let $inputQty = $(this).parent().find(".service-quantity-input");
        let qty = $inputQty.val();
        qty--;
        if (qty < 0) {
            qty = 0;
        }
        $inputQty.val(qty);

        $(this).parents(".cart-item").find(".update-cart-item").removeClass("d-none");
        updateTotalPriceCart();

    });

    // Handle direct input change for services in cart
    $(document).on('change', '.service-quantity-input', function () {
        let qty = $(this).val();
        if (qty < 0) {
            qty = 0;
        }
        if (qty > 99) {
            qty = 99;
        }
        $(this).val(qty);
        $(this).parents(".cart-item").find(".update-cart-item").removeClass("d-none");
        updateTotalPriceCart();
    });
    // ===== END: CART FUNCTIONALITY - SERVICE QUANTITY CONTROLS =====

    // ===== START: CART FUNCTIONALITY - UPDATE CART ITEM SERVICES =====
    $(document).on('click', '.update-cart-item', function () {
        const $btn = $(this);
        const $cartItem = $btn.closest('.cart-item');
        const cartId = $btn.data('cart-id');

        // Collect all service quantities in this cart item
        let services = {};
        $cartItem.find('.service-quantity-input').each(function () {
            const $input = $(this);
            const serviceId = $input.data('service-id');
            const qty = parseInt($input.val()) || 0;
            services[serviceId] = qty;
        });

        // Send AJAX request to update
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/ajax/cart/update-service',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            data: {
                cart_id: cartId,
                services: services
            },
            beforeSend: function () {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            },
            success: function (response) {
                if (!response.error) {
                    // Hide update button
                    $btn.addClass('d-none');

                    // Update totals
                    if (response.data) {
                        if (response.data.subtotal) {
                            $('[data-bb-value="cart-subtotal"]').text(response.data.subtotal);
                        }
                        if (response.data.total) {
                            $('[data-bb-value="cart-total"]').text(response.data.total);
                        }
                    }

                    // Show success message
                    if (typeof Theme !== 'undefined' && Theme.showSuccess) {
                        Theme.showSuccess(response.message || 'Cập nhật số lượng dịch vụ thành công!');
                    }
                } else {
                    // Show error message
                    if (typeof Theme !== 'undefined' && Theme.showError) {
                        Theme.showError(response.message || 'Có lỗi xảy ra khi cập nhật số lượng dịch vụ.');
                    }
                }
            },
            error: function () {
                if (typeof Theme !== 'undefined' && Theme.showError) {
                    Theme.showError('Có lỗi xảy ra khi cập nhật số lượng dịch vụ.');
                }
            },
            complete: function () {
                $btn.prop('disabled', false).html('Cập nhật');
            }
        });
    });
    // ===== END: CART FUNCTIONALITY - UPDATE CART ITEM SERVICES =====
    //kiểm tra sự tồn tại của biến successMessage
    if (typeof successMessage !== 'undefined' && successMessage) {
        Swal.fire({
            title: 'Đặt Đơn Thành Công!',
            text: successMessage,
            icon: 'success',
            timer: 5000,
            showConfirmButton: true,
            iconColor: '#000',
            confirmButtonColor: '#000',
            confirmButtonText: 'Xác nhận'
        });
    }
    if (typeof successMessage !== 'undefined' && errorMessage) {
        Swal.fire({
            title: 'Đặt Đơn Thất bại!',
            text: errorMessage,
            icon: 'error',
            timer: 5000,
            showConfirmButton: true,
        });
    }
});
