
// ============================================
// FILTER POPOVER SPECIFIC LOGIC
// ============================================
window.FilterPopoverModule = (function () {
    function init() {
        bindPriceRangeEvents();
        bindPopoverEvents();
    }

    function bindPriceRangeEvents() {
        // Price range slider functionality
        $(document).on('input', '#priceRangeMin, #priceRangeMax', function () {
            var minVal = parseInt($('#priceRangeMin').val());
            var maxVal = parseInt($('#priceRangeMax').val());

            if (minVal > maxVal) {
                if (this.id === 'priceRangeMin') {
                    $('#priceRangeMax').val(minVal);
                    maxVal = minVal;
                } else {
                    $('#priceRangeMin').val(maxVal);
                    minVal = maxVal;
                }
            }

            $('#priceMinValue').text(formatPrice(minVal));
            $('#priceMaxValue').text(formatPrice(maxVal));
        });
    }

    function bindPopoverEvents() {
        // Initialize price values when popover shows
        $(document).on('popover:shown', '[data-popover-id="filterPopover"]', function () {
            updatePriceDisplays();
        });
        $(document).on('click', '#applyAmenityBtn', function () {
            // Force close the filter popover
            PopoverModule.forceHide('#filterPopover');
        });
    }

    function updatePriceDisplays() {
        const minVal = parseInt($('#priceRangeMin').val() || 0);
        const maxVal = parseInt($('#priceRangeMax').val() || 30000000);
        $('#priceMinValue').text(formatPrice(minVal));
        $('#priceMaxValue').text(formatPrice(maxVal));
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
            minimumFractionDigits: 0
        }).format(price);
    }

    // Public API
    return {
        init: init,
        formatPrice: formatPrice,
        updatePriceDisplays: updatePriceDisplays
    };
})();

// ============================================
// SORT POPOVER SPECIFIC LOGIC
// ============================================
window.SortPopoverModule = (function () {
    let selectedSortOption = 'relevant';

    function init() {
        bindSortEvents();
        bindPopoverEvents();
    }

    function bindSortEvents() {
        // Handle sort option selection
        $(document).on('change', 'input[name="sort_by"]', function () {
            selectedSortOption = $(this).val();
            updateSortButtonText();
        });

        // Handle apply sort button
        $(document).on('click', '#applySortBtn', function () {
            applySortAndClose();
        });
    }

    function bindPopoverEvents() {
        // Initialize when popover shows
        $(document).on('popover:shown', '[data-popover-id="sortPopover"]', function () {
            // Ensure correct option is selected
            $(`input[name="sort_by"][value="${selectedSortOption}"]`).prop('checked', true);
        });
    }

    function updateSortButtonText() {
        const sortTexts = {
            'relevant': 'Phù hợp nhất',
            'price_asc': 'Giá: Thấp → Cao',
            'price_desc': 'Giá: Cao → Thấp',
            'level_star_asc': 'Hạng sao Thấp → Cao',
            'level_star_desc': 'Hạng sao Cao → Thấp',
            'rating_asc': 'Đánh giá Thấp → Cao',
            'rating_desc': 'Đánh giá Cao → Thấp'
        };

        const buttonText = sortTexts[selectedSortOption] || 'Sắp xếp';
        const $sortBtn = $('[data-popover-target="#sortPopover"]');
        $sortBtn.html(`<i class="fa fa-sort"></i> ${buttonText}`);
    }

    function applySortAndClose() {
        // Apply the sort logic here
        console.log('Applying sort:', selectedSortOption);

        // Update button text
        updateSortButtonText();

        // Close popover
        PopoverModule.forceHide('#sortPopover');

        // Trigger custom event for other modules to listen
        $(document).trigger('sort:applied', {
            sortOption: selectedSortOption
        });
    }

    function getCurrentSort() {
        return selectedSortOption;
    }

    function setSort(sortOption) {
        selectedSortOption = sortOption;
        $(`input[name="sort_by"][value="${sortOption}"]`).prop('checked', true);
        updateSortButtonText();
    }

    // Public API
    return {
        init: init,
        getCurrentSort: getCurrentSort,
        setSort: setSort
    };
})();

// ============================================
// GUEST POPOVER SPECIFIC LOGIC
// ============================================
window.GuestPopoverModule = (function () {
    let guestData = {
        rooms: 1,
        adults: 1,
        children: 0
    };

    const minValues = {
        rooms: 1,
        adults: 1,
        children: 0
    };

    const maxValues = {
        rooms: 10,
        adults: 20,
        children: 10
    };

    function init() {
        bindCounterEvents();
        bindPopoverEvents();
    }

    function bindCounterEvents() {
        // Handle counter buttons
        $(document).on('click', '.counter-btn', function () {
            const action = $(this).hasClass('plus') ? 'increment' : 'decrement';
            const target = $(this).data('target');
            updateCounter(target, action);
        });

        // Handle apply button
        $(document).on('click', '#applyGuestBtn', function () {
            applyGuestSelectionAndClose();
        });
    }

    function bindPopoverEvents() {
        // Initialize when popover shows
        $(document).on('popover:shown', '[data-popover-id="guestPopover"]', function () {
            updateCounterDisplays();
            updateCounterButtons();
        });

        // Handle reset/clear button
        $(document).on('click', '.guest-reset-btn', function (e) {
            e.preventDefault();
            e.stopPropagation();
            resetToDefaults();
            // Keep popover open to show the reset values
        });
    }

    function updateCounter(target, action) {
        const currentValue = guestData[target];
        let newValue = currentValue;

        if (action === 'increment' && currentValue < maxValues[target]) {
            newValue = currentValue + 1;
        } else if (action === 'decrement' && currentValue > minValues[target]) {
            newValue = currentValue - 1;
        }

        if (newValue !== currentValue) {
            guestData[target] = newValue;
            updateCounterDisplays();
            updateCounterButtons();
            updateHiddenInputs();
            updateGuestPickerText();
        }
    }

    function updateCounterDisplays() {
        $('#roomsCount').text(guestData.rooms);
        $('#adultsCount').text(guestData.adults);
        $('#childrenCount').text(guestData.children);
    }

    function updateCounterButtons() {
        // Update button states based on min/max values
        Object.keys(guestData).forEach(target => {
            const value = guestData[target];
            const $minusBtn = $(`.counter-btn.minus[data-target="${target}"]`);
            const $plusBtn = $(`.counter-btn.plus[data-target="${target}"]`);

            // Disable minus button if at minimum
            if (value <= minValues[target]) {
                $minusBtn.prop('disabled', true);
            } else {
                $minusBtn.prop('disabled', false);
            }

            // Disable plus button if at maximum
            if (value >= maxValues[target]) {
                $plusBtn.prop('disabled', true);
            } else {
                $plusBtn.prop('disabled', false);
            }
        });
    }

    function updateHiddenInputs() {
        $('input[name="rooms"]').val(guestData.rooms);
        $('input[name="adults"]').val(guestData.adults);
        $('input[name="children"]').val(guestData.children);
    }

    function updateGuestPickerText() {
        const { rooms, adults, children } = guestData;
        let text = `${rooms} Phòng - ${adults} Người lớn`;

        if (children > 0) {
            text += ` - ${children} Trẻ em`;
        } else {
            text += ` - 0 Trẻ em`;
        }

        $('.guest-picker .detail').text(text);
    }

    function resetToDefaults() {
        guestData = {
            rooms: 1,
            adults: 1,
            children: 0
        };
        updateCounterDisplays();
        updateCounterButtons();
        updateHiddenInputs();
        updateGuestPickerText();

        // Visual feedback for reset action
        const $resetBtn = $('.guest-reset-btn');
        const originalText = $resetBtn.text();
        $resetBtn.text('Đã xóa').css('color', '#28a745');

        setTimeout(() => {
            $resetBtn.text(originalText).css('color', '');
        }, 1000);
    }

    function applyGuestSelectionAndClose() {
        // Update everything one final time
        updateHiddenInputs();
        updateGuestPickerText();

        // Close popover
        PopoverModule.forceHide('#guestPopover');

        // Trigger custom event for other modules to listen
        $(document).trigger('guest:applied', {
            guestData: { ...guestData }
        });
    }

    function setGuestData(newData) {
        guestData = { ...guestData, ...newData };
        updateCounterDisplays();
        updateCounterButtons();
        updateHiddenInputs();
        updateGuestPickerText();
    }

    function getGuestData() {
        return { ...guestData };
    }

    // Public API
    return {
        init: init,
        setGuestData: setGuestData,
        getGuestData: getGuestData,
        reset: resetToDefaults
    };
})();

// ============================================
// DATE RANGE PICKER MODULE (SIMPLIFIED)
// ============================================
window.DateRangeModule = (function () {
    let selectedStartDate = null;
    let selectedEndDate = null;
    let initialized = false;

    function init() {
        if (initialized) {
            console.warn('DateRangeModule already initialized');
            return;
        }

        initializeDateRangePicker();
        setDefaultDateRange();
        bindDatePickerEvents();
        initialized = true;
    }

    function bindDatePickerEvents() {
        // Before showing daterangepicker, cleanup any existing instances
        $('#dateRangePicker').on('click focus', function () {
            // Small delay to allow cleanup before new instance shows
            setTimeout(function () {
                // Remove duplicate daterangepicker elements
                const $daterangeElements = $('.daterangepicker');
                if ($daterangeElements.length > 1) {
                    console.log('Found duplicate daterangepickers, cleaning up...');
                    $daterangeElements.slice(1).remove(); // Keep only the first one
                }
            }, 50);
        });
    }

    function setDefaultDateRange() {
        // Set default to 2 consecutive days starting from tomorrow
        const start = moment().add(1, 'day').startOf('day'); // Ngày mai
        const end = moment().add(2, 'days').startOf('day');   // Ngày kia

        selectedStartDate = start;
        selectedEndDate = end;

        const displayText = `${start.format('DD/MM/YYYY')} - ${end.format('DD/MM/YYYY')}`;
        $('#dateRangePicker').val(displayText);

        updateHiddenInputs();
    }

    function initializeDateRangePicker() {
        const $picker = $('#dateRangePicker');

        // Remove any existing daterangepicker instances from DOM
        $('.daterangepicker').remove();

        // Destroy existing daterangepicker if any
        if ($picker.data('daterangepicker')) {
            $picker.data('daterangepicker').remove();
        }

        $picker.daterangepicker({
            opens: 'center',
            startDate: moment().add(1, 'day').startOf('day'), // Ngày mai
            endDate: moment().add(2, 'days').startOf('day'),   // Ngày kia
            showDropdowns: true,
            locale: {
                format: 'DD/MM/YYYY',
                separator: ' - ',
                applyLabel: 'Áp dụng',
                cancelLabel: 'Hủy',
                fromLabel: 'Từ',
                toLabel: 'Đến',
                customRangeLabel: 'Tùy chọn',
                weekLabel: 'T',
                daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                monthNames: [
                    'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                    'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
                ],
                firstDay: 1
            }
        }, function (start, end, label) {
            selectedStartDate = start;
            selectedEndDate = end;
            updateHiddenInputs();

            console.log("Date range selected: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));

            // Trigger custom event
            $(document).trigger('daterange:applied', {
                startDate: selectedStartDate.format('YYYY-MM-DD'),
                endDate: selectedEndDate.format('YYYY-MM-DD'),
                displayText: $('#dateRangePicker').val()
            });
        });
    }

    function updateHiddenInputs() {
        if (selectedStartDate && selectedEndDate) {
            $('#startDate').val(selectedStartDate.format('YYYY-MM-DD'));
            $('#endDate').val(selectedEndDate.format('YYYY-MM-DD'));
        } else {
            $('#startDate').val('');
            $('#endDate').val('');
        }
    }

    function setDateRange(startDate, endDate) {
        if (startDate && endDate) {
            const start = moment(startDate);
            const end = moment(endDate);

            selectedStartDate = start;
            selectedEndDate = end;

            const displayText = `${start.format('DD/MM/YYYY')} - ${end.format('DD/MM/YYYY')}`;
            $('#dateRangePicker').val(displayText);

            updateHiddenInputs();

            // Update the daterangepicker widget
            const $picker = $('#dateRangePicker');
            const daterangepicker = $picker.data('daterangepicker');
            if (daterangepicker) {
                try {
                    daterangepicker.setStartDate(start);
                    daterangepicker.setEndDate(end);
                } catch (e) {
                    console.warn('Error updating daterangepicker:', e);
                    // Reinitialize if there's an error
                    initializeDateRangePicker();
                }
            }
        }
    }

    function getDateRange() {
        return {
            startDate: selectedStartDate ? selectedStartDate.format('YYYY-MM-DD') : null,
            endDate: selectedEndDate ? selectedEndDate.format('YYYY-MM-DD') : null,
            startDateObj: selectedStartDate,
            endDateObj: selectedEndDate
        };
    }

    function clearDateRange() {
        selectedStartDate = null;
        selectedEndDate = null;
        $('#dateRangePicker').val('');
        updateHiddenInputs();
    }

    function destroy() {
        const $picker = $('#dateRangePicker');

        // Remove all daterangepicker instances from DOM
        $('.daterangepicker').remove();

        // Destroy data and events
        if ($picker.data('daterangepicker')) {
            $picker.data('daterangepicker').remove();
        }

        // Unbind events
        $picker.off('click focus');

        initialized = false;
    }

    // Public API
    return {
        init: init,
        destroy: destroy,
        setDateRange: setDateRange,
        getDateRange: getDateRange,
        clear: clearDateRange
    };
})();

// ============================================
// LOCATION POPOVER SPECIFIC LOGIC
// ============================================
window.LocationPopoverModule = (function () {
    let selectedLocation = '';
    let selectedLocationName = 'Tất cả địa điểm';

    // Build location names dynamically from HTML
    function getLocationNames() {
        const locationNames = {};
        $('input[name="location_option"]').each(function () {
            const value = $(this).val();
            const label = $(this).siblings('.location-label').text();
            locationNames[value] = label;
        });
        return locationNames;
    }

    function init() {
        bindLocationEvents();
        bindPopoverEvents();
    }

    function bindLocationEvents() {
        // Handle location option selection
        $(document).on('change', 'input[name="location_option"]', function () {
            selectedLocation = $(this).val();
            const locationNames = getLocationNames();
            selectedLocationName = locationNames[selectedLocation] || 'Tất cả địa điểm';

            // Apply location immediately when radio is selected
            applyLocationAndClose(false);
        });

        // Handle apply location button
        $(document).on('click', '#applyLocationBtn', function () {
            applyLocationAndClose(true);
        });
    }

    function bindPopoverEvents() {
        // Initialize when popover shows
        $(document).on('popover:shown', '[data-popover-id="locationPopover"]', function () {
            // Ensure correct option is selected
            $(`input[name="location_option"][value="${selectedLocation}"]`).prop('checked', true);
        });

        // Handle reset/clear button
        $(document).on('click', '.location-reset-btn', function (e) {
            e.preventDefault();
            e.stopPropagation();
            resetToDefaults();
            // Keep popover open to show the reset values
        });
    }

    function updateLocationPickerText() {
        $('.location-picker .location-detail').text(selectedLocationName);
    }

    function updateHiddenInput() {
        $('input[name="places[]"]').val(selectedLocation);
    }

    function resetToDefaults() {
        selectedLocation = '';
        selectedLocationName = 'Tất cả địa điểm';
        $('input[name="location_option"][value=""]').prop('checked', true);
        updateLocationPickerText();
        updateHiddenInput();

        // Visual feedback for reset action
        const $resetBtn = $('.location-reset-btn');
        const originalText = $resetBtn.text();
        $resetBtn.text('Đã xóa').css('color', '#28a745');

        setTimeout(() => {
            $resetBtn.text(originalText).css('color', '');
        }, 1000);
    }

    function applyLocationAndClose(close = true) {
        // Update everything one final time
        updateHiddenInput();
        updateLocationPickerText();

        // Close popover
        if (close) {
            PopoverModule.forceHide('#locationPopover');
        }

        // Trigger custom event for other modules to listen
        $(document).trigger('location:applied', {
            locationId: selectedLocation,
            locationName: selectedLocationName
        });
    }

    function setLocation(locationId) {
        selectedLocation = locationId;
        const locationNames = getLocationNames();
        selectedLocationName = locationNames[locationId] || 'Tất cả địa điểm';
        $(`input[name="location_option"][value="${locationId}"]`).prop('checked', true);
        updateLocationPickerText();
        updateHiddenInput();
    }

    function getLocation() {
        return {
            id: selectedLocation,
            name: selectedLocationName
        };
    }

    // Public API
    return {
        init: init,
        setLocation: setLocation,
        getLocation: getLocation,
        reset: resetToDefaults
    };
})();

// ============================================
// INITIALIZE ALL MODULES
// ============================================
// ============================================
// URL PARAMS UTILITIES
// ============================================
window.URLParamsModule = (function () {

    function buildURLFromFilterData(filterData) {
        const url = new URL(window.location.href);
        const params = new URLSearchParams();

        // Basic search and categories
        if (filterData.search_query) {
            params.set('q', filterData.search_query);
        }

        // Categories and availability (từ hidden fields)
        if (filterData.categories) {
            params.set('categories[]', filterData.categories);
        }
        if (filterData.filter_by_availability) {
            params.set('filter_by_availability', filterData.filter_by_availability);
        }

        // Date range
        if (filterData.date_range.start_date) {
            params.set('start_date', filterData.date_range.start_date);
        }
        if (filterData.date_range.end_date) {
            params.set('end_date', filterData.date_range.end_date);
        }
        if (filterData.date_range.display_text) {
            params.set('date_range', filterData.date_range.display_text);
        }

        // Guest data
        if (filterData.guest_data.rooms > 1) {
            params.set('rooms', filterData.guest_data.rooms);
        }
        if (filterData.guest_data.adults > 1) {
            params.set('adults', filterData.guest_data.adults);
        }
        if (filterData.guest_data.children > 0) {
            params.set('children', filterData.guest_data.children);
        }

        // Location
        if (filterData.location.id) {
            params.set('places[]', filterData.location.id);
        }

        // Filter options
        if (filterData.filter_options.price_range.min > 0) {
            params.set('min_price', filterData.filter_options.price_range.min);
        }
        if (filterData.filter_options.price_range.max < 30000000) {
            params.set('max_price', filterData.filter_options.price_range.max);
        }

        if (filterData.filter_options.rooms_filter) {
            params.set('rooms_filter', filterData.filter_options.rooms_filter);
        }

        if (filterData.filter_options.level_star) {
            params.set('level_star', filterData.filter_options.level_star);
        }

        // Amenities arrays
        if (filterData.filter_options.room_amenities.length > 0) {
            params.set('room_amenities', filterData.filter_options.room_amenities.join(','));
        }

        if (filterData.filter_options.hotel_amenities.length > 0) {
            params.set('hotel_amenities', filterData.filter_options.hotel_amenities.join(','));
        }

        // Sort option (chỉ set nếu khác default)
        if (filterData.sort_by && filterData.sort_by !== 'relevant') {
            params.set('sort_by', filterData.sort_by);
        }

        // Build final URL
        url.search = params.toString();
        return url.toString();
    }

    function parseURLParams() {
        const urlParams = new URLSearchParams(window.location.search);
        const filterData = {
            search_query: urlParams.get('q') || '',
            date_range: {
                start_date: urlParams.get('start_date') || '',
                end_date: urlParams.get('end_date') || '',
                display_text: urlParams.get('date_range') || ''
            },
            guest_data: {
                rooms: parseInt(urlParams.get('rooms')) || 1,
                adults: parseInt(urlParams.get('adults')) || 1,
                children: parseInt(urlParams.get('children')) || 0
            },
            location: {
                id: urlParams.get('places[]') || '',
                name: '' // Sẽ được set từ LocationPopoverModule
            },
            filter_options: {
                price_range: {
                    min: parseInt(urlParams.get('min_price')) || 0,
                    max: parseInt(urlParams.get('max_price')) || 30000000
                },
                rooms_filter: urlParams.get('rooms_filter') || '',
                room_amenities: urlParams.get('room_amenities') ? urlParams.get('room_amenities').split(',') : [],
                level_star: urlParams.get('level_star') || '',
                hotel_amenities: urlParams.get('hotel_amenities') ? urlParams.get('hotel_amenities').split(',') : []
            },
            sort_by: urlParams.get('sort_by') || 'relevant',
            categories: urlParams.get('categories[]') || '',
            filter_by_availability: urlParams.get('filter_by_availability') || ''
        };

        return filterData;
    }

    function applyFilterDataToForm(filterData) {
        // Set search input
        if (filterData.search_query) {
            $('input[name="q"]').val(filterData.search_query);
        }

        // Set hidden fields
        if (filterData.categories) {
            $('input[name="categories[]"]').val(filterData.categories);
        }
        if (filterData.filter_by_availability) {
            $('input[name="filter_by_availability"]').val(filterData.filter_by_availability);
        }

        // Set date range inputs
        if (filterData.date_range.start_date) {
            $('#startDate').val(filterData.date_range.start_date);
        }
        if (filterData.date_range.end_date) {
            $('#endDate').val(filterData.date_range.end_date);
        }
        if (filterData.date_range.display_text) {
            $('#dateRangePicker').val(filterData.date_range.display_text);
        }

        // Set guest inputs
        $('input[name="rooms"]').val(filterData.guest_data.rooms);
        $('input[name="adults"]').val(filterData.guest_data.adults);
        $('input[name="children"]').val(filterData.guest_data.children);

        // Set location input
        if (filterData.location.id) {
            $('input[name="places[]"]').val(filterData.location.id);
        }

        // Set filter inputs
        $('#priceRangeMin').val(filterData.filter_options.price_range.min);
        $('#priceRangeMax').val(filterData.filter_options.price_range.max);

        // Trigger price range update
        $('#priceRangeMin, #priceRangeMax').trigger('input');

        if (filterData.filter_options.rooms_filter) {
            $(`input[name="rooms_radio"][value="${filterData.filter_options.rooms_filter}"]`).prop('checked', true);
        }

        if (filterData.filter_options.level_star) {
            $(`input[name="level_star"][value="${filterData.filter_options.level_star}"]`).prop('checked', true);
        }

        // Set amenities checkboxes
        filterData.filter_options.room_amenities.forEach(amenity => {
            $(`input[name="room_amenities"][value="${amenity}"]`).prop('checked', true);
        });

        // filterData.filter_options.hotel_amenities.forEach(amenity => {
        //     $(`input[name="hotel_amenities"][value="${amenity}"]`).prop('checked', true);
        // });

        // Set sort option
        if (filterData.sort_by) {
            $(`input[name="sort_by"][value="${filterData.sort_by}"]`).prop('checked', true);
        }
    }

    return {
        buildURL: buildURLFromFilterData,
        parseParams: parseURLParams,
        applyToForm: applyFilterDataToForm
    };
})();

// ============================================
// FORM SUBMISSION HANDLER
// ============================================
function handleFormSubmission() {
    $('.product-search-section').on('submit', function (e) {
        e.preventDefault(); // Ngăn chặn form submit mặc định

        // Thu thập tất cả dữ liệu từ form
        const filterData = {
            // Search query
            search_query: $('input[name="q"]').val() || '',

            // Date range
            date_range: {
                start_date: $('input[name="start_date"]').val() || '',
                end_date: $('input[name="end_date"]').val() || '',
                display_text: $('#dateRangePicker').val() || ''
            },

            // Guest data
            guest_data: {
                rooms: parseInt($('input[name="rooms"]').val()) || 1,
                adults: parseInt($('input[name="adults"]').val()) || 1,
                children: parseInt($('input[name="children"]').val()) || 0
            },

            // Location
            location: {
                id: $('input[name="places[]"]').val() || '',
                name: $('.location-picker .location-detail').text() || 'Tất cả địa điểm'
            },

            // Filter options
            filter_options: {
                // Price range
                price_range: {
                    min: parseInt($('#priceRangeMin').val()) || 0,
                    max: parseInt($('#priceRangeMax').val()) || 30000000
                },

                // Room count
                rooms_filter: $('input[name="rooms_radio"]:checked').val() || '',

                // Room amenities
                room_amenities: [],

                // Star rating
                level_star: $('input[name="level_star"]:checked').val() || '',

                // Hotel amenities
                hotel_amenities: []
            },

            // Sort option
            sort_by: $('input[name="sort_by"]:checked').val() || 'relevant',

            // Hidden fields
            categories: $('input[name="categories[]"]').val() || '',
            filter_by_availability: $('input[name="filter_by_availability"]').val() || ''
        };

        // Thu thập room amenities
        $('input[name="room_amenities"]:checked').each(function () {
            filterData.filter_options.room_amenities.push($(this).val());
        });

        // Thu thập hotel amenities
        $('input[name="hotel_amenities"]:checked').each(function () {
            filterData.filter_options.hotel_amenities.push($(this).val());
        });

        console.log('filterData', filterData);

        // Build URL với filter data và redirect
        const newURL = URLParamsModule.buildURL(filterData);
        console.log('🔗 Redirecting to:', newURL);

        // Redirect đến URL mới với params
        window.location.href = newURL;
    });
}

// ============================================
// INITIALIZE FROM URL PARAMS ON PAGE LOAD
// ============================================
function initializeFromURLParams() {
    // Chỉ chạy nếu có params trong URL
    if (window.location.search) {
        console.log('📄 Loading filters from URL params...');

        const filterData = URLParamsModule.parseParams();
        console.log('📊 Parsed filter data:', filterData);

        // Apply data to form elements
        URLParamsModule.applyToForm(filterData);

        // Apply data to modules (with delay để đảm bảo modules đã init)
        setTimeout(function () {
            // Set guest data
            if (window.GuestPopoverModule) {
                GuestPopoverModule.setGuestData(filterData.guest_data);
            }

            // Set location data
            if (window.LocationPopoverModule && filterData.location.id) {
                LocationPopoverModule.setLocation(filterData.location.id);
            }

            // Set sort data
            if (window.SortPopoverModule && filterData.sort_by) {
                SortPopoverModule.setSort(filterData.sort_by);
            }

            // Set date range data
            if (window.DateRangeModule && filterData.date_range.start_date && filterData.date_range.end_date) {
                DateRangeModule.setDateRange(filterData.date_range.start_date, filterData.date_range.end_date);
            }

            // Update filter displays
            if (window.FilterPopoverModule) {
                FilterPopoverModule.updatePriceDisplays();
            }

        }, 200);
    } else {
        // Nếu không có params, đảm bảo date range vẫn có default value
        setTimeout(function () {
            if (window.DateRangeModule && !$('#dateRangePicker').val()) {
                // Set default date range nếu chưa có (2 ngày liên tiếp từ ngày mai)
                const start = moment().add(1, 'day').startOf('day'); // Ngày mai
                const end = moment().add(2, 'days').startOf('day');   // Ngày kia
                const displayText = `${start.format('DD/MM/YYYY')} - ${end.format('DD/MM/YYYY')}`;
                $('#dateRangePicker').val(displayText);
                $('#startDate').val(start.format('YYYY-MM-DD'));
                $('#endDate').val(end.format('YYYY-MM-DD'));
            }
        }, 300);
    }
}

$(document).ready(function () {
    // Ensure single initialization
    if (window.modulesInitialized) {
        console.warn('Modules already initialized');
        return;
    }

    // Initialize filter-specific functionality
    FilterPopoverModule.init();

    // Initialize sort-specific functionality
    SortPopoverModule.init();

    // Initialize guest-specific functionality
    GuestPopoverModule.init();

    // Initialize location-specific functionality
    LocationPopoverModule.init();

    // Initialize date range picker
    DateRangeModule.init();

    // Initialize form submission handler
    handleFormSubmission();

    // Initialize filters from URL params (if any)
    initializeFromURLParams();

    // Mark as initialized
    window.modulesInitialized = true;

    // Debug functions cho console testing
    window.switchToHover = function () {
        PopoverModule.setTriggerType('hover');
    };

    window.switchToClick = function () {
        PopoverModule.setTriggerType('click');
    };

    window.getCurrentTrigger = function () {
        const trigger = PopoverModule.getTriggerType();
        return trigger;
    };
});
