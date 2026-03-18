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
    slidesPerView: 2.5,
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
            slidesPerView: 1.5,
        },
        768: {
            slidesPerView: 2.5,
        },
    },
});

let blogSwiper = new Swiper(".blog-swiper", {
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
        },
        768: {
            slidesPerView: 3.5,
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

$(document).ready(function () {
    $(".select2-location, .select-category, .select-location").select2({
        placeholder: "Chọn địa điểm",
        allowClear: false,
        templateResult: formatLocation,
        templateSelection: formatLocation
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
    $(".single-date-picker").each(function () {
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
        });

        $picker.find("span").text(moment().format("DD/MM/YYYY"));
    });

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

        // Cập nhật text hiển thị
        let rooms = $(".guest-item:eq(0) .count").text();
        let adults = $(".guest-item:eq(1) .count").text();
        let children = $(".guest-item:eq(2) .count").text();

        $(".guest-picker span").text(`${rooms} Phòng - ${adults} Người lớn - ${children} Trẻ em`);
    });

    $(".hotel-gallery").lightGallery({
        selector: ".gallery-item",
        thumbnail: true,
        animateThumb: false,
        showThumbByDefault: false
    });

    let calendarEl = document.getElementById('product-service-calendar');
    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: [
            {
                title: '2tr',
                start: '2025-04-16'
            },
            {
                title: '3tr',
                start: '2025-04-17'
            },
            {
                title: '5tr',
                start: '2025-04-18'
            }
        ],
        eventDisplay: 'block',
    });
    calendar.render();
});
