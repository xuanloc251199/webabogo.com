let mainProductSwiper = new Swiper(".product-swiper", {
    slidesPerView: 3.5,
    spaceBetween: 10,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    breakpoints: {
        320: {
            slidesPerView: 1,
        },
        768: {
            slidesPerView: 3.5,
        },
    },
});

let productDetailSwiper = new Swiper(".product-detail-swiper", {
    slidesPerView: 3,
    spaceBetween: 10,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    breakpoints: {
        320: {
            slidesPerView: 2,
        },
        768: {
            slidesPerView: 3,
        },
    },
});

let blogSwiper = new Swiper(".blog-swiper", {
    slidesPerView: 3.5,
    spaceBetween: 10,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    breakpoints: {
        320: {
            slidesPerView: 1,
            spaceBetween: 8,
        },
        768: {
            slidesPerView: 3.5,
            spaceBetween: 10,
        },
    },
});
let categoryNewSwiper = new Swiper(".category-new-swiper", {
    slidesPerView: 'auto',
    spaceBetween: 10,
    freeMode: true,
    grabCursor: true,
    breakpoints: {
        320: {
            slidesPerView: 'auto',
            spaceBetween: 8,
        },
        768: {
            slidesPerView: 3.5,
            slidesPerGroup: 3.5,
            spaceBetween: 20,
            freeMode: false,
        },
    },
});

let placeSwiper = new Swiper(".place-swiper", {
    slidesPerView: 3.5,
    spaceBetween: 20,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
});

let blogCategorySwiper = new Swiper(".blog-category-swiper", {
    slidesPerView: 8,
    spaceBetween: 10,
    breakpoints: {
        320: {
            grid: {
                rows: 2,
            },
            slidesPerView: 4,
        },
        768: {
            slidesPerView: 8,
        },
    },
});

let blogLocationSwiper = new Swiper(".blog-location-swiper", {
    slidesPerView: 2,
    slidesPerColumn: 1,
    spaceBetween: 20,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    breakpoints: {
        320: {
            slidesPerView: 2,
        },
        768: {
            slidesPerView: 6,
        },
    },
});

let badgeSwiper = new Swiper(".badge-swiper", {
    slidesPerView: 3.5,
    slidesPerColumn: 1,
    spaceBetween: 16,
    breakpoints: {
        320: {
            slidesPerView: 4.5,
        },
        768: {
            slidesPerView: 8.5,
        },
    },
});
let productBlogSwiper = new Swiper(".product-blog-swiper", {
    slidesPerView: 'auto',
    slidesPerColumn: 1,
    spaceBetween: 16,
    freeMode: true,
    breakpoints: {
        320: {},
        768: {},
    },
});

let productCategorySwiper = new Swiper(".category-swiper", {
    slidesPerView: 4,
    slidesPerGroup: 4,
    spaceBetween: 10,
});

let flashSaleSwiper = new Swiper(".flash-sale-swiper", {
    slidesPerView: 4,
    slidesPerGroup: 4,
    spaceBetween: 20,
});
$(document).ready(function () {

    $(".read-more").click(function () {
        let txt = $(this).text();
        if (txt === 'Xem thêm') {
            txt = "Thu gọn";
        } else {
            txt = 'Xem thêm';
        }
        $(this).text(txt);
        $(this).parents(".policies").find(".content").toggleClass("active_policies")
    })
    $(".services-toggle-history").click(function () {
        $(this).find("i").toggleClass("fa-chevron-down fa-chevron-up");
        $(this).next(".services-info-history").toggle();
    })
    $('.select2-search-location').select2({
        placeholder: "Địa điểm",
        allowClear: true
    });

    $(".select2-location, .select-category, .select-location").select2({
        placeholder: "Chọn địa điểm",
        allowClear: false,
        templateResult: formatLocation,
        templateSelection: formatLocation
    });
    $(".select2-product").select2({
        placeholder: "Chọn sản phẩm",
        allowClear: false
    });

    $(".select2-room-type").select2({
        placeholder: "Chọn Hạng phòng",
        allowClear: false
    });

    function formatLocation(state) {
        if (!state.id) {
            return state.text;
        }
        let $state = $(
            '<span><i class="fas fa-map-marker-alt me-1 me-md-2"></i> ' + state.text + '</span>'
        );
        return $state;
    }

    // Single Date Picker
    $(".single-date-picker").each(function (index) {

        let $picker = $(this);
        $picker.daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoApply: true,
            locale: {
                format: "DD/MM/YYYY"
            }
        }, function (start) {
            $picker.find("span").text(start.format("DD/MM/YYYY"));
            $picker.find("input").val(start.format("DD/MM/YYYY"));
        });
        if (index === 0) {
            $picker.find("span").text(moment().format("DD/MM/YYYY"));
            $picker.find("input").val(moment().format("DD/MM/YYYY"));
        } else {
            $picker.find("span").text(moment().add(1, 'days').format("DD/MM/YYYY"));
            $picker.find("input").val(moment().add(1, 'days').format("DD/MM/YYYY"));
        }

    });
    // Product Date Range Picker
    $("#dateRangePickerProduct").daterangepicker({
        autoApply: true,
        showDropdowns: true,
        minDate: moment(),
        locale: {
            format: "DD/MM/YYYY",
            separator: " - ",
            applyLabel: "Áp dụng",
            cancelLabel: "Hủy",
            fromLabel: "Từ",
            toLabel: "Đến",
            customRangeLabel: "Tùy chọn",
            weekLabel: "T",
            daysOfWeek: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
            monthNames: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6",
                "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
            firstDay: 1
        }
    }, function (start, end) {
        $("#start_date_room").val(start.format("DD/MM/YYYY"));
        $("#end_date_room").val(end.format("DD/MM/YYYY"));
    });

    // Set default dates for product search
    const defaultStart = moment();
    const defaultEnd = moment().add(1, 'days');
    $("#dateRangePickerProduct").val(defaultStart.format("DD/MM/YYYY") + " - " + defaultEnd.format("DD/MM/YYYY"));
    $("#start_date_room").val(defaultStart.format("DD/MM/YYYY"));
    $("#end_date_room").val(defaultEnd.format("DD/MM/YYYY"));

    // Date Range Picker
    $(".date-range-picker").each(function () {
        let $picker = $(this);
        $picker.daterangepicker({
            autoApply: true,
            showDropdowns: true,
            locale: {
                format: "DD/MM/YYYY",
                separator: " - "
            }
        }, function (start, end) {
            $picker.find("span").text(start.format("DD/MM/YYYY") + " - " + end.format("DD/MM/YYYY"));
        });

        // Set giá trị mặc định là hôm nay - hôm nay
        $picker.find("span").text(moment().format("DD/MM/YYYY") + " - " + moment().format("DD/MM/YYYY"));
    });

    $(".guest-dropdown .counter .btn").click(function () {
        let $btn = $(this);
        let $counter = $btn.siblings(".count");
        let value = parseInt($counter.text());

        if ($btn.hasClass("plus")) value++;
        if ($btn.hasClass("minus") && value > 0) value--;

        $counter.text(value);

        let adults = $(".guest-item:eq(0) .count").text();
        let children = $(".guest-item:eq(1) .count").text();

        $(".guest-picker span.detail").text(`${adults} Người lớn - ${children} Trẻ em`);
        $(".guest-picker input[name='adults']").val(adults);
        $(".guest-picker input[name='children']").val(children);
    });

    // Product Guest Counter Logic
    $(document).on('click', '.counter-btn', function () {
        const $btn = $(this);
        const target = $btn.data('target');
        const $countSpan = $btn.siblings('.count');
        let currentValue = parseInt($countSpan.text()) || 0;

        if ($btn.hasClass('plus')) {
            currentValue++;
        } else if ($btn.hasClass('minus') && currentValue > 0) {
            if (target === 'adults' && currentValue <= 1) {
                return; // Không cho phép giảm số người lớn xuống dưới 1
            }
            currentValue--;
        }

        $countSpan.text(currentValue);

        // Update hidden input
        $(`#${target}`).val(currentValue);

        // Update display text
        updateGuestDisplayProduct();
    });

    function updateGuestDisplayProduct() {
        const adults = parseInt($('#adults').val()) || 1;
        const children = parseInt($('#children').val()) || 0;
        $('.guest-picker .detail').text(`${adults} Người lớn - ${children} Trẻ em`);
    }

    // Apply Guest Selection
    $(document).on('click', '#applyGuestBtnProduct', function () {
        updateGuestDisplayProduct();
        // Close popover
        if (typeof PopoverModule !== 'undefined') {
            PopoverModule.forceHide('#applyGuestBtnProduct');
        }
    });

    function checkedRoomProduct() {
        const selectedValue = $('input[name="room_type_option"]:checked').val();
        const selectedText = $('input[name="room_type_option"]:checked').siblings('.room-type-label').text();

        $('#choose_room_group').val(selectedValue);
        $('.room-type-detail').text(selectedText);
    }

    checkedRoomProduct();
    // Apply Room Type Selection
    $(document).on('click', '#applyRoomTypeBtnProduct', function () {
        checkedRoomProduct();

        // Close popover
        if (typeof PopoverModule !== 'undefined') {
            PopoverModule.forceHide('#applyRoomTypeBtnProduct');
        }
    });
    $(".counter-tour .counter .btn").click(function () {
        let $btn = $(this);
        let $counter = $btn.siblings(".count");
        let value = parseInt($counter.text());

        if ($btn.hasClass("plus")) value++;
        if ($btn.hasClass("minus") && value > 0) value--;

        $counter.text(value);

        let adults = $(".counter-tour:eq(0) .count").text();
        let children = $(".counter-tour:eq(1) .count").text();

        $("input[name=adults_number]").val(adults);
        $("input[name=children_number]").val(children);
        let priceAdults = $("input[name=adults_number]").data("price");
        let priceChildren = $("input[name=children_number]").data("price");
        let total = adults * priceAdults + children * priceChildren;

        $(".total_price_product_tour").text(formatPrice(total));
    });
    $(document).on("click", ".product-option-item-values .counter .btn", function () {
        let $btn = $(this);
        let $counter = $btn.siblings(".count");
        let $qty = $btn.siblings("input");
        let value = parseInt($counter.text());

        if ($btn.hasClass("plus")) value++;
        if ($btn.hasClass("minus") && value > 0) value--;

        $counter.text(value);
        $qty.val(value);
    });

    $(".hotel-gallery").lightGallery({
        selector: ".gallery-item",
        thumbnail: true,
        animateThumb: false,
        showThumbByDefault: false
    });

    function formatDateToDMY(date, split = '-') {
        // Đảm bảo sử dụng giờ UTC+7
        const local = new Date(date.getTime() + 7 * 60 * 60 * 1000); // +7 giờ
        const yyyy = local.getUTCFullYear();
        const mm = String(local.getUTCMonth() + 1).padStart(2, '0');
        const dd = String(local.getUTCDate()).padStart(2, '0');
        return `${dd}` + split + `${mm}` + split + `${yyyy}`;
    }

    let calendarEl = document.getElementById('product-service-calendar');
    if (calendarEl) {
        let startDate = moment().format("DD/MM/YYYY");
        let endDate = moment().add(1, 'days').format("DD/MM/YYYY");
        let hoverDate = null;

        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            eventDisplay: 'block',

            events: function (fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: ajaxCalendarPriceRoute,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        successCallback(data.data);
                    },
                    error: function () {
                        failureCallback();
                    }
                });
            },
            dateClick: function (info) {
                $(".button-re-buy-now").show();
                if (!startDate || (startDate && endDate)) {
                    // Chọn mới hoặc reset
                    startDate = info.date;
                    $(".date-start").text(formatDateToDMY(startDate, "/"));
                    let endBook = new Date(startDate); // clone object để tránh thay đổi gốc
                    endBook.setDate(startDate.getDate() + 1);
                    $(".date-end").text(formatDateToDMY(endBook, "/"))

                    endDate = null;
                    clearDateSelection();
                    highlightDate(info.dateStr);

                    $("#start_date").val(formatDateToYMD(startDate, "/"));
                    $("#end_date").val(formatDateToYMD(endBook, "/"));
                } else if (startDate && !endDate) {
                    endDate = info.date;
                    if (startDate === endDate) {
                        clearDateSelection();
                        return null;
                    }

                    // Đảo ngược nếu chọn lùi
                    if (endDate < startDate) {
                        [startDate, endDate] = [endDate, startDate];
                    }
                    $(".date-start").text(formatDateToDMY(startDate, "/"))
                    $(".date-end").text(formatDateToDMY(endDate, "/"))
                    highlightRange(startDate, endDate);

                }
                const url = $('#product-service-calendar').data('url');
                sendSelectedDateRange(startDate, endDate, url);
            }
        });

        calendar.render();
// Gắn sự kiện hover cho từng cell sau khi render
        calendarEl.addEventListener('mouseover', function (e) {
            const cell = e.target.closest('td.fc-daygrid-day');

            if (!cell || !startDate || endDate) return;

            const dateStr = cell.getAttribute('data-date');
            if (!dateStr) return;

            hoverDate = new Date(dateStr);
            clearHoverHighlight();
            highlightHoverRange(startDate, hoverDate);
        });

        calendarEl.addEventListener('mouseout', function () {
            if (!startDate || endDate) return;
            clearHoverHighlight();
        });
        // 🔵 Thêm CSS để highlight ngày được chọn
        const style = document.createElement('style');
        style.innerHTML = `
        .hover-range {
            background-color: #bce0fb !important;
        }
        .selected-range {
            background-color: #82c6f9 !important;
            color: white !important;
        }`;
        document.head.appendChild(style);

        // 🔁 Hàm hỗ trợ
        function highlightDate(dateStr) {
            const cell = calendarEl.querySelector(`[data-date="${dateStr}"]`);
            if (cell) cell.classList.add('selected-range');
        }

        function highlightRange(start, end) {

            const startCopy = new Date(start);
            const endCopy = new Date(end);
            startCopy.setHours(0, 0, 0, 0);
            endCopy.setHours(0, 0, 0, 0);
            $("#start_date").val(formatDateToYMD(startCopy, "/"));
            $("#end_date").val(formatDateToYMD(endCopy, "/"));
            let current = new Date(startCopy);
            while (current.getTime() <= endCopy.getTime()) {
                const dateStr = formatDateToYMD(current);
                highlightDate(dateStr);
                current.setDate(current.getDate() + 1);
            }

        }

        function highlightHoverRange(start, hover) {
            const startCopy = new Date(start);
            const hoverCopy = new Date(hover);

            startCopy.setHours(0, 0, 0, 0);
            hoverCopy.setHours(0, 0, 0, 0);

            if (hoverCopy < startCopy) {
                [startCopy, hoverCopy] = [hoverCopy, startCopy];
            }

            let current = new Date(startCopy);
            while (current.getTime() <= hoverCopy.getTime()) {
                const dateStr = formatDateToYMD(current);
                const cell = calendarEl.querySelector(`[data-date="${dateStr}"]`);
                if (cell && !cell.classList.contains('selected-range')) {
                    cell.classList.add('hover-range');
                }
                current.setDate(current.getDate() + 1);
            }
        }

        function clearHoverHighlight() {
            calendarEl.querySelectorAll('.hover-range').forEach(cell => {
                cell.classList.remove('hover-range');
            });
        }

        function clearDateSelection() {
            calendarEl.querySelectorAll('.selected-range').forEach(cell => {
                cell.classList.remove('selected-range');
            });
        }

        function formatDateToYMD(date, split = '-') {
            // Đảm bảo sử dụng giờ UTC+7
            const local = new Date(date.getTime() + 7 * 60 * 60 * 1000); // +7 giờ
            const yyyy = local.getUTCFullYear();
            const mm = String(local.getUTCMonth() + 1).padStart(2, '0');
            const dd = String(local.getUTCDate()).padStart(2, '0');
            return `${yyyy}` + split + `${mm}` + split + `${dd}`;
        }


        function sendSelectedDateRange(start, end, url) {
            const startStr = formatDateToYMD(start);
            let endStr;
            if (!end) {
                end = new Date(start); // clone object để tránh thay đổi gốc
                end.setDate(start.getDate() + 1);
                endStr = formatDateToYMD(end);
            } else {
                endStr = formatDateToYMD(end);
            }


            // Ajax
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    $(".button-re-buy-now").html('<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...');
                },
                data: {
                    start: startStr,
                    end: endStr
                },
                success: function (data) {
                    if (data.error) {
                        Swal.fire({
                            title: '',
                            text: data.message,
                            icon: 'warning',
                            confirmButtonText: 'Đóng',
                            confirmButtonColor: '#dc3545'
                        });
                        $(".button-re-buy-now").text('Đặt ngay').hide();
                    } else {
                        $(".button-re-buy-now").text('Đặt ngay');
                        $(".total_price_product").text(formatPrice(data.data.totalPrice));
                    }
                },
                error: function () {
                    $(".button-re-buy-now").hide();
                    const errorMessage = 'Có lỗi xảy ra khi lấy thông tin giá phòng. Vui lòng thử lại.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Lỗi!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'Đóng',
                            confirmButtonColor: '#dc3545'
                        });
                    } else if (typeof Theme !== 'undefined' && Theme.showError) {
                        Theme.showError(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                }
            })
        }
    }


    let calendarTourEl = document.getElementById('product-tour-calendar');
    if (calendarTourEl) {
        let selectedTourDate = null;
        let calendarTour = new FullCalendar.Calendar(calendarTourEl, {
            initialView: 'dayGridMonth',
            eventDisplay: 'block',
            selectable: false,
            events: dataPriceTour,

            eventClick: function (info) {
                // Tìm thông tin tour date từ data
                const clickedDate = info.event.startStr;
                const tourData = dataPriceTour.find(tour => tour.start === clickedDate);

                if (tourData) {
                    selectedTourDate = tourData;

                    // Cập nhật thông tin trong khu vực bên phải
                    updateTourSelection(tourData);

                    // Highlight ngày được chọn
                    highlightSelectedTourDate(clickedDate);
                }
            },

            dateClick: function (info) {
                // Chỉ cho phép click vào ngày có events (có giá)
                const clickedDate = info.dateStr;
                const tourData = dataPriceTour.find(tour => tour.start === clickedDate);

                if (!tourData) {
                    // Không cho phép click vào ngày không có giá
                    return false;
                }

                // Cập nhật tour được chọn (giống như eventClick)
                selectedTourDate = tourData;

                // Cập nhật thông tin trong khu vực bên phải
                updateTourSelection(tourData);

                // Highlight ngày được chọn
                highlightSelectedTourDate(clickedDate);
            }
        });

        calendarTour.render();

        // Thêm CSS để style cho calendar
        const tourCalendarStyle = document.createElement('style');
        tourCalendarStyle.innerHTML = `
            #product-tour-calendar .fc-event {
                cursor: pointer;
                border-radius: 4px;
                font-size: 12px;
                padding: 2px;
            }
            #product-tour-calendar .fc-event:hover {
                opacity: 0.8;
            }
            #product-tour-calendar .fc-daygrid-day.has-event {
                cursor: pointer;
                background-color: #f8f9fa;
                transition: background-color 0.2s ease;
                position: relative;
            }
            #product-tour-calendar .fc-daygrid-day.has-event:hover {
                background-color: #e9ecef;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            #product-tour-calendar .fc-daygrid-day.has-event .fc-daygrid-day-frame {
                height: 100%;
                min-height: 60px;
            }
            #product-tour-calendar .fc-daygrid-day.selected-tour-date {
                background-color: #007bff !important;
                color: white !important;
                border: 2px solid #0056b3 !important;
            }
            #product-tour-calendar .fc-daygrid-day.selected-tour-date .fc-daygrid-day-number {
                color: white !important;
                font-weight: bold;
            }
            #product-tour-calendar .fc-daygrid-day.selected-tour-date .fc-event {
                background-color: rgba(255,255,255,0.2) !important;
                color: white !important;
            }
            #product-tour-calendar .fc-daygrid-day:not(.has-event) {
                pointer-events: none;
                opacity: 0.6;
            }
            .tour-selection-info {
                background-color: #f8f9fa;
                padding: 10px;
                border-radius: 6px;
                border: 1px solid #e9ecef;
            }

            /* Đảm bảo toàn bộ ô ngày có thể click được */
            #product-tour-calendar .fc-daygrid-day.has-event .fc-daygrid-day-top,
            #product-tour-calendar .fc-daygrid-day.has-event .fc-daygrid-day-events,
            #product-tour-calendar .fc-daygrid-day.has-event .fc-daygrid-day-bg {
                pointer-events: none;
            }

            #product-tour-calendar .fc-daygrid-day.has-event {
                pointer-events: auto;
            }
        `;
        document.head.appendChild(tourCalendarStyle);

        // Hàm cập nhật thông tin tour được chọn
        function updateTourSelection(tourData) {
            // Cập nhật ngày khởi hành
            const selectedDateEl = $("#selected-tour-date");
            if (selectedDateEl.length && tourData.start) {
                const formattedDate = formatDateToDMY(new Date(tourData.start), "/");
                selectedDateEl.text(formattedDate);
            }

            // Cập nhật giá tour
            const selectedPriceEl = $("#selected-tour-price");
            if (selectedPriceEl.length && tourData.title) {
                selectedPriceEl.text(tourData.title);
            }

            // Cập nhật ngày đi (legacy)
            $(".date-start").text(tourData.startDate || formatDateToDMY(new Date(tourData.start), "/"));


            // Cập nhật tổng tiền trẻ em
            $(".adults-price").text(tourData.format_price_adults);
            $(".children-price").text(tourData.format_price_children);

            // Cập nhật hidden inputs nếu có
            $("#start_date").val(tourData.start);
            $("#adult_variation_id").val(tourData.adult_variation_id);
            $("#children_variation_id").val(tourData.children_variation_id);

            $("#children_number").data("price", tourData.price_children);
            $("#adults_number").data("price", tourData.price_adults);

            let price_children = tourData.price_children * $("#children_number").val();
            let price_adults = tourData.price_adults * $("#adults_number").val();


            // Cập nhật tổng tiền (legacy)
            $(".total_price_product_tour").text(formatPrice(price_children + price_adults));
            // Hiển thị button nếu đã ẩn
            $(".button-re-buy-now").show();
        }

        // Hàm highlight ngày được chọn
        function highlightSelectedTourDate(dateStr) {
            // Xóa highlight cũ
            calendarTourEl.querySelectorAll('.selected-tour-date').forEach(el => {
                el.classList.remove('selected-tour-date');
            });

            // Thêm highlight mới cho ô ngày
            const dayElements = calendarTourEl.querySelectorAll('.fc-daygrid-day');
            dayElements.forEach(dayEl => {
                const dayDate = dayEl.getAttribute('data-date');
                if (dayDate === dateStr) {
                    dayEl.classList.add('selected-tour-date');
                }
            });

            // Fallback: tìm theo data-date attribute
            const selectedCell = calendarTourEl.querySelector(`[data-date="${dateStr}"]`);
            if (selectedCell) {
                selectedCell.classList.add('selected-tour-date');
            }
        }

        // Đánh dấu các ngày có events và tự động chọn ngày đầu tiên
        setTimeout(() => {
            dataPriceTour.forEach(tour => {
                // Tìm ô ngày theo data-date attribute
                const dayCell = calendarTourEl.querySelector(`[data-date="${tour.start}"]`);
                if (dayCell) {
                    dayCell.classList.add('has-event');
                }

                // Cũng tìm theo fc-daygrid-day class với ngày tương ứng
                const dayElements = calendarTourEl.querySelectorAll('.fc-daygrid-day');
                dayElements.forEach(dayEl => {
                    const dayDate = dayEl.getAttribute('data-date');
                    if (dayDate === tour.start) {
                        dayEl.classList.add('has-event');

                        // Thêm click event cho toàn bộ ô ngày
                        dayEl.addEventListener('click', function (e) {
                            // Ngăn event bubbling nếu click vào event
                            if (e.target.classList.contains('fc-event') || e.target.closest('.fc-event')) {
                                return;
                            }

                            const tourData = dataPriceTour.find(t => t.start === dayDate);
                            if (tourData) {
                                selectedTourDate = tourData;
                                updateTourSelection(tourData);
                                highlightSelectedTourDate(dayDate);
                            }
                        });
                    }
                });
            });

            // Tự động chọn ngày đầu tiên nếu có
            if (dataPriceTour.length > 0) {
                const firstTour = dataPriceTour[0];
                selectedTourDate = firstTour;
                updateTourSelection(firstTour);
                highlightSelectedTourDate(firstTour.start);
            }
        }, 100);
    }
    $('.category-filter-item').on('click', function () {
        let $this = $(this);
        let url = $this.data('url');

        $('.category-filter-item').removeClass('active');
        $this.addClass('active');

        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'html',
            beforeSend: function () {
                $('.category-filter-content').html('<div class="p-3 text-center w-100">Loading...</div>');
            },
            success: function (data) {
                $('.category-filter-content').html(data);

                // Update Swiper after content is loaded
                setTimeout(function () {
                    // Wait for DOM to be fully updated
                    var attempts = 0;
                    var maxAttempts = 5;

                    function tryReinitialize() {
                        attempts++;
                        var slideCount = $('.blog-swiper .swiper-slide').length;

                        if (slideCount > 0) {
                            var success = reinitializeBlogSwiper();
                            if (success) {
                                console.log('Swiper reinitialized successfully with', slideCount, 'slides');
                            } else {
                                console.log('Failed to reinitialize swiper, using fallback CSS');
                                $('.blog-swiper').addClass('swiper-fallback');
                            }
                        } else if (attempts < maxAttempts) {
                            // Try again after a short delay
                            setTimeout(tryReinitialize, 50);
                        } else {
                            $('.blog-swiper').addClass('swiper-fallback');
                        }
                    }

                    tryReinitialize();
                }, 100);
            },
            error: function () {
                $('.category-filter-content').html('<div class="p-3 text-danger text-center w-100">Failed to load content</div>');
            }
        });
    });

    $(document).on('change', '[data-bb-toggle="change-customer-avatar"]', (e) => {
        const currentTarget = $(e.currentTarget)

        const url = currentTarget.data('url')
        const file = currentTarget[0].files[0]
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        if (typeof url === 'undefined' || typeof file === 'undefined') {
            return
        }

        const formData = new FormData()
        formData.append('avatar_file', file)

        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            data: formData,
            contentType: false,
            processData: false,
            success: ({data, message, error}) => {
                if (typeof Theme !== 'undefined' && error) {
                    Theme.showError(message)

                    return
                }

                if (typeof Theme !== 'undefined') {
                    Theme.showSuccess(message)
                }

                $('[data-bb-value="customer-avatar"]').prop('src', data.url)
            },
        })
    })

    const $navbar = $('.navbar');
    let navOffset = $navbar.outerHeight();

    // $(window).on('scroll', function () {
    //     if ($(window).scrollTop() >= navOffset) {
    //         $navbar.addClass('fixed-top');
    //     } else {
    //         $navbar.removeClass('fixed-top');
    //     }
    // });

    $('.product-blog-item').on('click', function () {
        let $this = $(this);
        let url = $this.data('url');

        $('.product-blog-item').removeClass('active');
        $this.addClass('active');

        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'html',
            beforeSend: function () {
                $('.product-blog-content').html('<div class="p-3 text-center w-100">Loading...</div>');
            },
            success: function (data) {
                $('.product-blog-content').html(data);
            },
            error: function () {
                $('.product-blog-content').html('<div class="p-3 text-danger text-center w-100">Failed to load content</div>');
            }
        });
    });
    $('#find_rooms').on('click', function () {
        let $this = $(this);
        let url = $this.data('url');
        let startDate = $("input[name=start_date]").val();
        let endDate = $("input[name=end_date]").val();
        let adults = $("input[name=adults]").val();
        let children = $("input[name=children]").val();
        let room_group = $("input[name=room_group]").val(); // Changed from select to input
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        let services = {};
        $('.service_quantity').val(0);
        $('.count_service').text(0);
        $('.service_quantity').each(function () {
            services[$(this).data('service-id')] = $(this).val();


        });
        // Add loading state
        $this.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang tìm...');

        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            data: {
                startDate: startDate,
                endDate: endDate,
                adults: adults,
                children: children,
                room_group: room_group,
                services: services
            },
            success: ({data, message, error}) => {
                if (!error) {
                    $(".product-prices").html(data.detail_price)
                    $(".total-result").html(data.detail_total_price)
                    $("#list-products-grouped").html(data.products_grouped)
                } else {
                    // Use SweetAlert for error notification
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Không tìm thấy phòng!',
                            text: message || 'Không tìm thấy kết quả phù hợp với yêu cầu của bạn. Vui lòng thử lại với các tiêu chí khác.',
                            icon: 'warning',
                            confirmButtonText: 'Đóng',
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        // Fallback to Theme notification if SweetAlert not available
                        if (typeof Theme !== 'undefined' && Theme.showError) {
                            Theme.showError(message || 'Không tìm thấy kết quả phù hợp với yêu cầu của bạn.');
                        } else {
                            alert(message || 'Không tìm thấy kết quả phù hợp với yêu cầu của bạn.');
                        }
                    }
                    $(".product-prices").text(`Không tìm thấy kết quả!`)
                }
            },
            error: function (xhr) {
                console.error('Error finding rooms:', xhr);

                // Use SweetAlert for AJAX error
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Lỗi kết nối!',
                        text: 'Có lỗi xảy ra khi tìm kiếm phòng. Vui lòng thử lại sau.',
                        icon: 'error',
                        confirmButtonText: 'Đóng',
                        confirmButtonColor: '#dc3545'
                    });
                } else {
                    // Fallback to Theme notification if SweetAlert not available
                    if (typeof Theme !== 'undefined' && Theme.showError) {
                        Theme.showError('Có lỗi xảy ra khi tìm kiếm phòng. Vui lòng thử lại sau.');
                    } else {
                        alert('Có lỗi xảy ra khi tìm kiếm phòng. Vui lòng thử lại sau.');
                    }
                }
            },
            complete: function () {
                // Reset button state
                $this.prop('disabled', false).html('Chọn phòng');
            }
        })
    });
    $(document).on("click", "#add_product_group", function () {
        $(".total-result").show();
    })

    function formatPrice(price) {
        if (typeof price !== 'number') {
            price = parseFloat(price);
        }
        if (isNaN(price)) {
            price = 0;
        }
        return price.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,') + 'đ';
    }

    // ===== START: CART FUNCTIONALITY - PRODUCT PRICE CALCULATION WITH SERVICES =====
    function updatePrices() {
        let serviceFee = 0;

        // Calculate service fees based on quantity
        $('.service_quantity').each(function () {
            const quantity = parseInt($(this).val()) || 0;
            const price = parseFloat($(this).data('extra-price')) || 0;
            serviceFee += quantity * price;
        });

        // Calculate product option fees (checkboxes)
        $('.product-option input[type="checkbox"]:checked').each(function () {
            const price = parseFloat($(this).data('extra-price'));
            if (!isNaN(price)) {
                serviceFee += price;
            }
        });

        $('.price-service-fee').text(formatPrice(serviceFee));

        $('.total-price').each(function () {
            const basePrice = parseFloat($(this).data('base-price'));
            if (!isNaN(basePrice)) {
                const newTotalPrice = basePrice + serviceFee;
                $(this).text(formatPrice(newTotalPrice));
            }
        });
    }

    // Handle service quantity changes
    $(document).on('click', '.service-quantity .btn.plus', function () {
        const serviceId = $(this).data('service-id');
        const $input = $(`.service_quantity[data-service-id="${serviceId}"]`);
        const $countService = $(this).siblings(".count_service");

        const currentValue = parseInt($input.val()) || 0;
        const maxValue = parseInt($input.attr('max')) || 99;

        if (currentValue < maxValue) {
            $input.val(currentValue + 1);
            $countService.text(currentValue + 1);
            updatePrices();
        }
    });

    $(document).on('click', '.service-quantity .btn.minus', function () {
        const serviceId = $(this).data('service-id');
        const $input = $(`.service_quantity[data-service-id="${serviceId}"]`);
        const $countService = $(this).siblings(".count_service");
        const currentValue = parseInt($input.val()) || 0;
        const minValue = parseInt($input.attr('min')) || 0;

        if (currentValue > minValue) {
            $input.val(currentValue - 1);
            $countService.text(currentValue - 1);
            updatePrices();
        }
    });

    $(document).on('change input', '.service_quantity', function () {
        const value = parseInt($(this).val()) || 0;
        const minValue = parseInt($(this).attr('min')) || 0;
        const maxValue = parseInt($(this).attr('max')) || 99;

        if (value < minValue) {
            $(this).val(minValue);
        } else if (value > maxValue) {
            $(this).val(maxValue);
        }

        updatePrices();
    });

    $(document).on('change', '.product-option input[type="checkbox"]', function () {
        updatePrices();
    });
    // ===== END: CART FUNCTIONALITY - PRODUCT PRICE CALCULATION WITH SERVICES =====

    // ===== START: CART FUNCTIONALITY - HANDLE BUY NOW AND ADD TO CART =====
    function handleBuyNow(checkout, e) {
        e.preventDefault();
        const $this = $(e.currentTarget);
        if ($this.hasClass('btn-disabled')) {
            return;
        }

        // Add loading state
        const originalContent = $this.html();
        const isAddToCart = $this.hasClass('button-add-to-cart');
        const isBuyNow = $this.hasClass('button-buy-now');

        let loadingText = '<i class="fas fa-spinner fa-spin"></i>';
        if (isBuyNow) {
            loadingText = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
        }

        $this.prop('disabled', true)
            .addClass('btn-disabled')
            .html(loadingText);

        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const url = $this.data("url");

        const productId = $this.data('id');
        const originProductId = $this.data('origin_product_id');

        const startDate = $("input[name=start_date]").val();
        const endDate = $("input[name=end_date]").val();
        const adults = $("input[name=adults]").val();
        const children = $("input[name=children]").val();
        let selectedServices = {};

        $('.service_quantity').each(function () {
            const serviceId = $(this).data('service-id');
            const quantity = parseInt($(this).val()) || 0;
            if (quantity > 0) {
                selectedServices[serviceId] = quantity;
            }
        });
        let optionsData = {};
        $('.product-option').each(function () {
            const $option = $(this);
            const optionIdMatch = $option.attr('class').match(/product-option-(\d+)/);
            if (!optionIdMatch) return;

            const optionId = optionIdMatch[1];
            const optionType = $option.find('input[name="options[' + optionId + '][option_type]"]').val();

            if (optionType === 'checkbox') {
                const checkedValues = [];
                $option.find('input[type="checkbox"]:checked').each(function () {
                    checkedValues.push($(this).val());
                });

                if (checkedValues.length > 0) {
                    optionsData[optionId] = {
                        'option_type': optionType,
                        'values': checkedValues,
                    };
                }
            }
        });

        let data = {
            'id': productId,
            'origin_product_id': originProductId,
            'start_date': startDate,
            'end_date': endDate,
            'adults': adults,
            'children': children,
            'options': optionsData,
            'services': selectedServices
        };

        // Thêm variation_id cho tour nếu có
        const variationId = $this.data('variation-id');
        const tourDate = $this.data('tour-date');

        // Kiểm tra nếu đây là tour product (có tour quantity selectors)
        const tourAdultsQty = $('#adults-quantity').val();
        const tourChildrenQty = $('#children-quantity').val();

        if (tourAdultsQty !== undefined && tourChildrenQty !== undefined) {
            // Đây là tour product, sử dụng tour quantities
            data.adults_number = parseInt(tourAdultsQty) || 1;
            data.children_number = parseInt(tourChildrenQty) || 0;

            // Lấy thông tin từ selected tour date
            const selectedTourDate = $('#selected-tour-date').text();
            if (selectedTourDate && selectedTourDate !== 'Chưa chọn') {
                data.tour_date = selectedTourDate;

                // Tìm variation IDs từ tourPricesByDate
                if (typeof tourPricesByDate !== 'undefined') {
                    for (const [dateKey, tourInfo] of Object.entries(tourPricesByDate)) {
                        const tourDateFormatted = new Date(dateKey).toLocaleDateString('vi-VN');
                        if (tourDateFormatted === selectedTourDate) {
                            data.adult_variation_id = tourInfo.adult_variation_id;
                            data.child_variation_id = tourInfo.child_variation_id;
                            data.variation_id = tourInfo.adult_variation_id; // Default to adult variation
                            break;
                        }
                    }
                }
            }
        } else {
            // Hotel/villa product, sử dụng logic cũ
            if (variationId) {
                data.variation_id = variationId;
            }
            if (tourDate) {
                data.tour_date = tourDate;
            }
        }
        if (checkout) {
            data.checkout = true;
        }
        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            data: data,
            success: ({data, message, error}) => {
                if (!error) {
                    console.log(error, data)
                    if (data && data.next_url) {
                        window.location.href = data.next_url;
                    } else {
                        $(".total-product-cart").text(data.total_product_cart)
                        // Show success notification with SweetAlert or Theme
                        let successMessage = message;
                        if (!successMessage) {
                            successMessage = isBuyNow ? 'Đặt Đơn Thành Công!' : 'Đã thêm sản phẩm vào giỏ hàng thành công!';
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Thành công!',
                                text: successMessage,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else if (typeof Theme !== 'undefined' && Theme.showSuccess) {
                            Theme.showSuccess(successMessage);
                        } else {
                            alert(successMessage);
                        }

                        // Update cart count in header
                        if (data && data.count) {
                            let $cart = $(".header-actions li").find("a[href$='/cart']");
                            if ($cart.length) {
                                $cart.find("span").text(data.count)
                            }
                        }

                        // Reset button state on success
                        $this.prop('disabled', false)
                            .removeClass('btn-disabled')
                            .html(originalContent);
                    }
                } else {

                    if (data && data.next_url) {
                        window.location.href = data.next_url;
                    } else {
                        // Show error notification with SweetAlert or Theme
                        let errorMessage = message;
                        if (!errorMessage) {
                            errorMessage = isBuyNow ? 'Có lỗi xảy ra khi đặt phòng.' : 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng.';
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'Đóng',
                                confirmButtonColor: '#dc3545'
                            });
                        } else if (typeof Theme !== 'undefined' && Theme.showError) {
                            Theme.showError(errorMessage);
                        } else {
                            alert(errorMessage);
                        }

                        // Reset button state on error
                        $this.prop('disabled', false)
                            .removeClass('btn-disabled')
                            .html(originalContent);
                    }

                }
            },
            error: (xhr) => {
                console.error('Error adding to cart:', xhr);

                // Show AJAX error notification with SweetAlert or Theme
                const ajaxErrorMessage = isBuyNow ?
                    'Có lỗi xảy ra khi đặt phòng. Vui lòng thử lại.' :
                    'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng. Vui lòng thử lại.';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Lỗi kết nối!',
                        text: ajaxErrorMessage,
                        icon: 'error',
                        confirmButtonText: 'Đóng',
                        confirmButtonColor: '#dc3545'
                    });
                } else if (typeof Theme !== 'undefined' && Theme.showError) {
                    Theme.showError(ajaxErrorMessage);
                } else {
                    alert(ajaxErrorMessage);
                }

                // Reset button state on AJAX error
                $this.prop('disabled', false)
                    .removeClass('btn-disabled')
                    .html(originalContent);
            }
        });
    }

    // ===== END: CART FUNCTIONALITY - HANDLE BUY NOW AND ADD TO CART =====

    // ===== START: CART FUNCTIONALITY - BUY NOW BUTTON EVENT =====
    $(document).on('click', '.button-buy-now', function (e) {
        handleBuyNow(true, e);
    });
    // ===== END: CART FUNCTIONALITY - BUY NOW BUTTON EVENT =====


    $(document).on('click', '.button-add-tour-to-cart', function (e) {
        const start_date = $("input[name=start_date]").val();
        const adult_variation_id = $("input[name=adult_variation_id]").val();
        const children_variation_id = $("input[name=children_variation_id]").val();
        const adults_number = $("input[name=adults_number]").val();
        const children_number = $("input[name=children_number]").val();
        if (!start_date) {
            e.preventDefault();
            // Use SweetAlert for user guidance
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Vui lòng chọn ngày đi',
                    text: 'Vui lòng chọn ngày đi',
                    icon: 'info',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                // Fallback to Theme notification if SweetAlert not available
                if (typeof Theme !== 'undefined' && Theme.showNotice) {
                    Theme.showNotice('info', 'Vui lòng chọn ngày đi');
                } else {
                    alert('Vui lòng chọn ngày đi');
                }
            }
            return false;
        }
        if (adults_number <= 0) {
            e.preventDefault();
            // Use SweetAlert for user guidance
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Vui lòng chọn số lượng người lớn',
                    text: 'Vui lòng chọn số lượng người lớn',
                    icon: 'info',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                // Fallback to Theme notification if SweetAlert not available
                if (typeof Theme !== 'undefined' && Theme.showNotice) {
                    Theme.showNotice('info', 'Vui lòng chọn số lượng người lớn');
                } else {
                    alert('Vui lòng chọn số lượng người lớn');
                }
            }
            return false;
        }
        $.ajax({
            url: $(this).data('url'),
            type: 'POST',
            data: {
                id: $(this).data('id'),
                start_date: start_date,
                adults_number: adults_number,
                children_number: children_number,
                adult_variation_id: adult_variation_id,
                children_variation_id: children_variation_id
            },
            success: function (response) {
                if (!response.error) {
                    $(".total-product-cart").text(response.data.total_product_cart)
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        // Fallback to Theme notification if SweetAlert not available
                        if (typeof Theme !== 'undefined' && Theme.showNotice) {
                            Theme.showNotice('success', response.message);
                        } else {
                            alert(response.message);
                        }
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: response.message || 'Có lỗi xảy ra khi thêm tour vào giỏ hàng.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        // Fallback to Theme notification if SweetAlert not available
                        if (typeof Theme !== 'undefined' && Theme.showNotice) {
                            Theme.showNotice('error', response.message || 'Có lỗi xảy ra khi thêm tour vào giỏ hàng.');
                        } else {
                            alert(response.message || 'Có lỗi xảy ra khi thêm tour vào giỏ hàng.');
                        }
                    }
                }
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
        });
    });
    // ===== START: CART FUNCTIONALITY - ADD TO CART BUTTON EVENT =====
    $(document).on('click', '.button-add-to-cart', function (e) {
        // Check if this is from the room selection section (has room search data)
        const startDate = $("input[name=start_date]").val();
        const endDate = $("input[name=end_date]").val();
        const adults = $("input[name=adults]").val();
        // const roomGroup = $("select[name=room_group]").val();

        // If no search data is available, show message to search rooms first
        if (!startDate || !endDate) {
            e.preventDefault();

            // Use SweetAlert for user guidance
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Vui lòng tìm phòng trước!',
                    text: 'Bạn cần điền thông tin ngày, số khách và chọn hạng phòng, sau đó nhấn "Chọn phòng" để xem giá và thêm vào giỏ hàng.',
                    icon: 'info',
                    confirmButtonText: 'Đã hiểu',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                // Fallback to Theme notification if SweetAlert not available
                if (typeof Theme !== 'undefined' && Theme.showNotice) {
                    Theme.showNotice('info', 'Vui lòng điền thông tin tìm kiếm và nhấn "Chọn phòng" trước khi thêm vào giỏ hàng.');
                } else {
                    alert('Vui lòng điền thông tin tìm kiếm và nhấn "Chọn phòng" trước khi thêm vào giỏ hàng.');
                }
            }
            return;
        }

        handleBuyNow(false, e);
    });
    $(document).on('click', '#view-all-product-grouped', function () {
        $(".products-grouped-hide").toggleClass("show");

        // Kiểm tra xem element đang hiện hay ẩn
        if ($(".products-grouped-hide").hasClass("show")) {
            $(this).text("Thu gọn"); // Đang hiển thị => Cho phép thu gọn
        } else {
            $(this).text("Xem tất cả"); // Đang ẩn => Cho phép xem tất cả
        }
    });
    $(document).on('click', '#view-all-service', function () {
        $(".services-hide").toggleClass("show");

        // Kiểm tra xem element đang hiện hay ẩn
        if ($(".services-hide").hasClass("show")) {
            $(this).text("Thu gọn"); // Đang hiển thị => Cho phép thu gọn
        } else {
            $(this).text("Xem tất cả"); // Đang ẩn => Cho phép xem tất cả
        }
    });
    // ===== END: CART FUNCTIONALITY - ADD TO CART BUTTON EVENT =====
    $(document).on('click', '.write-reviews', function () {
        $(".form-reviews").toggleClass("d-none");
    });

    $("#place_search").change(function () {
        window.location.href = $(this).find("option:selected").data("url");
    })

    // Function to reinitialize blog swiper
    function reinitializeBlogSwiper() {
        try {
            // Destroy existing swiper instance if it exists
            if (typeof blogSwiper !== 'undefined' && blogSwiper && blogSwiper.destroy) {
                blogSwiper.destroy(true, true);
            }

            // Check if the swiper container still exists and has slides
            if ($('.blog-swiper').length > 0 && $('.blog-swiper .swiper-slide').length > 0) {
                blogSwiper = new Swiper(".blog-swiper", {
                    slidesPerView: 3.5,
                    spaceBetween: 20,
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    breakpoints: {
                        320: {
                            slidesPerView: 1,
                            spaceBetween: 10,
                        },
                        768: {
                            slidesPerView: 3.5,
                            spaceBetween: 20,
                        },
                    },
                    touchRatio: 1,
                    touchAngle: 45,
                    grabCursor: true,
                    centeredSlides: false,
                    loop: false,
                    watchOverflow: true,
                    observer: true,
                    observeParents: true,
                });

                // Force update
                setTimeout(function () {
                    if (blogSwiper && blogSwiper.update) {
                        blogSwiper.update();
                        blogSwiper.updateSize();
                        blogSwiper.updateSlides();
                        blogSwiper.updateProgress();
                        blogSwiper.updateSlidesClasses();
                    }
                }, 50);

                return true;
            }
        } catch (e) {
            console.log('Error reinitializing swiper:', e);
        }
        return false;
    }

    // Handle window resize for swiper updates
    $(window).on('resize', function () {
        if (typeof blogSwiper !== 'undefined' && blogSwiper) {
            setTimeout(function () {
                blogSwiper.update();
            }, 100);
        }
        if (typeof categoryNewSwiper !== 'undefined' && categoryNewSwiper) {
            setTimeout(function () {
                categoryNewSwiper.update();
            }, 100);
        }
    });
});

// Handle tab content expand/collapse
$('.btn-expand-content').on('click', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $button = $(this);
    const $wrapper = $button.parent();
    const $preview = $wrapper.find('.tab-content-preview');
    const $full = $wrapper.find('.tab-content-full');

    // Add fade transition classes
    $preview.addClass('fade-transition');
    $full.addClass('fade-transition');

    if ($button.hasClass('expanded')) {
        // Collapse: show preview, hide full content
        $full.fadeOut(300, function () {
            $preview.fadeIn(300);
        });
        $button.removeClass('expanded');
        $button.find('i').removeClass('fa-circle-minus').addClass('fa-circle-plus');

        // Update tooltip
        const originalTitle = $button.attr('title');
        if (originalTitle && originalTitle.includes('Thu gọn')) {
            $button.attr('title', originalTitle.replace('Thu gọn', 'Xem toàn bộ'));
        }
    } else {
        // Expand: show full content, hide preview
        $preview.fadeOut(300, function () {
            $full.fadeIn(300);
        });
        $button.addClass('expanded');
        $button.find('i').removeClass('fa-circle-plus').addClass('fa-circle-minus');

        // Update tooltip
        const originalTitle = $button.attr('title');
        if (originalTitle && originalTitle.includes('Xem toàn bộ')) {
            $button.attr('title', originalTitle.replace('Xem toàn bộ', 'Thu gọn'));
        }
    }
});

// Reset all tabs to collapsed state when switching tabs
$('button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
    const targetTab = $(e.target).attr('data-bs-target');
    const $tabPane = $(targetTab);

    // Reset all expand buttons in the current tab
    $tabPane.find('.btn-expand-content').each(function () {
        const $button = $(this);
        const $wrapper = $button.closest('.tab-content-wrapper');
        const $preview = $wrapper.find('.tab-content-preview');
        const $full = $wrapper.find('.tab-content-full');

        // Reset to collapsed state only if full content exists
        if ($full.length > 0) {
            $full.hide();
            $preview.show();
            $button.removeClass('expanded');
            $button.find('i').removeClass('fa-circle-minus').addClass('fa-circle-plus');
        }
    });
});
