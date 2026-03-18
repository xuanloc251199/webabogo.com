$(() => {
    // === Multi Variation Price Edit ===
    $(document).on('click', '.btn-trigger-edit-multi-product-variation', function (event) {
        event.preventDefault();

        // Lấy danh sách ID các biến thể đã chọn
        let ids = [];
        $('.table-hover-variants .checkboxes:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            Botble.showError('Vui lòng chọn ít nhất một biến thể để chỉnh sửa!');
            return;
        }

        let url = $(this).data('url');
        // Tạo HTML form trực tiếp
        let idsInputs = ids.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('');
        let formHtml = `
            <form id="form-edit-multi-variation" action="${url}" method="POST">
                ${idsInputs}
                <div class="row">
                    <div class="col-md-6 mb-3 position-relative">
                        <label class="form-label" for="price">Đơn giá</label>
                        <div class="input-group input-group-flat flex">
                            <span class="input-group-text">₫</span>
                            <input class="form-control input-mask-number" type="text" name="price" id="price" value="" data-thousands-separator="," data-decimal-separator="." step="any" im-insert="true">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3 position-relative">
                        <label class="form-label" for="sale_price">Giá giảm</label>
                        <div class="input-group input-group-flat flex">
                            <span class="input-group-text">₫</span>
                            <input class="form-control input-mask-number" type="text" name="sale_price" id="sale_price" value="" data-thousands-separator="," data-decimal-separator="." data-sale-percent-text="Discount :percent from original price." im-insert="true">
                        </div>
                        <small class="form-hint" id="discount-percent-hint"></small>
                    </div>
                </div>
            </form>
        `;
        $('#edit-multi-product-variation-modal .modal-body').html(formHtml);
        $('#edit-multi-product-variation-modal').modal('show');

        // Gọi lại hàm khởi tạo input mask cho các input số tiền
        if (typeof Botble !== 'undefined' && typeof Botble.initResources === 'function') {
            Botble.initResources();
        }
    });


    // Xử lý lưu thay đổi giá
    $(document).on('click', '#store-multi-product-variation-button', function (event) {
        event.preventDefault();

        let $modal = $('#edit-multi-product-variation-modal');
        let $form = $modal.find('form');

        if ($form.length === 0) {
            Botble.showError('Không tìm thấy form!');
            return;
        }

        let formData = $form.serialize();
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            beforeSend: function () {
                $('#store-multi-product-variation-button').addClass('button-loading');
            },
            success: function (res) {
                if (res.error) {
                    Botble.showError(res.message);
                } else {
                    Botble.showSuccess(res.message);
                    $modal.modal('hide');
                    // Reload lại bảng biến thể nếu cần
                    let $table = $('.table-hover-variants');
                    if ($table.length && window.LaravelDataTables && LaravelDataTables[$table.attr('id')]) {
                        LaravelDataTables[$table.attr('id')].draw();
                    }
                }
            },
            error: function (err) {
                Botble.handleError(err);
            },
            complete: function () {
                $('#store-multi-product-variation-button').removeClass('button-loading');
            }
        });
    });
})
