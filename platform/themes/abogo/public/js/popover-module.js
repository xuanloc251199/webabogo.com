// ============================================
// UNIVERSAL POPOVER MODULE
// ============================================
window.PopoverModule = (function () {
    let hideTimeout = null;
    let currentActivePopover = null;
    let triggerType = 'hover'; // 'hover' hoặc 'click'

    function init(options = {}) {
        // Cấu hình trigger type: 'hover' hoặc 'click'
        triggerType = options.trigger || 'hover';
        bindEvents();
    }

    function bindEvents() {
        if (triggerType === 'hover') {
            bindHoverEvents();
        } else if (triggerType === 'click') {
            bindClickEvents();
        }

        // Common events for both trigger types
        bindCommonEvents();
    }

    function bindHoverEvents() {
        // Handle popover buttons - HOVER
        $(document).on('mouseenter.popover', '[data-popover="button"]', function () {
            const target = $(this).attr('data-popover-target');
            showPopover(this, target);
        });

        $(document).on('mouseleave.popover', '[data-popover="button"]', function () {
            const target = $(this).attr('data-popover-target');
            hidePopover(target);
        });

        // Keep popover open when hovering over content
        $(document).on('mouseenter.popover', '[data-popover-id]', function () {
            clearTimeout(hideTimeout);
        });

        $(document).on('mouseleave.popover', '[data-popover-id]', function () {
            const popoverId = '#' + $(this).attr('data-popover-id');
            hidePopover(popoverId);
        });
    }

    function bindClickEvents() {
        // Handle popover buttons - CLICK
        $(document).on('click.popover', '[data-popover="button"]', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const target = $(this).attr('data-popover-target');
            const $popover = $(`[data-popover-id="${target.substring(1)}"]`);

            if ($popover.hasClass('show') && currentActivePopover === target) {
                forceHidePopover(target);
            } else {
                showPopover(this, target);
            }
        });

        // Click outside to close popover
        $(document).on('click.popover', function (e) {
            if (!$(e.target).closest('[data-popover="button"], [data-popover-id]').length) {
                hideAllPopovers();
            }
        });
    }

    function bindCommonEvents() {
        // Handle close buttons in any popover
        $(document).on('click.popover', '[data-popover-close]', function () {
            const popover = $(this).closest('[data-popover-id]');
            const popoverId = '#' + popover.attr('data-popover-id');
            forceHidePopover(popoverId);
        });

        // Handle filter apply buttons in any popover
        $(document).on('click.popover', '.btn-filter-apply', function () {
            const popover = $(this).closest('[data-popover-id]');
            if (popover.length) {
                const popoverId = '#' + popover.attr('data-popover-id');
                forceHidePopover(popoverId);
            }
        });

        // Mobile touch support for hover mode
        if (triggerType === 'hover' && 'ontouchstart' in window) {
            $(document).on('touchstart.popover', '[data-popover="button"]', function (e) {
                e.preventDefault();
                const target = $(this).attr('data-popover-target');
                const $popover = $(`[data-popover-id="${target.substring(1)}"]`);

                if ($popover.hasClass('show') && currentActivePopover === target) {
                    forceHidePopover(target);
                } else {
                    showPopover(this, target);
                }
            });
        }
    }

    function showPopover(button, target) {
        if (triggerType === 'hover') {
            clearTimeout(hideTimeout);
        }

        const $button = $(button);
        const $popover = $(`[data-popover-id="${target.substring(1)}"]`);

        if ($popover.length) {
            // Close any currently active popover first
            if (currentActivePopover && currentActivePopover !== target) {
                hideCurrentPopover();
            }

            positionPopover($button, $popover);
            $popover.addClass('show');
            $button.attr('data-popover-active', 'true');

            // Update current active popover
            currentActivePopover = target;

            // Trigger custom events
            $popover.trigger('popover:shown');
            $(document).trigger('popover:activeChanged', { activePopover: target });
        }
    }

    function hidePopover(target) {
        if (triggerType === 'hover') {
            hideTimeout = setTimeout(function () {
                hidePopoverImmediately(target);
            }, 100);
        } else {
            hidePopoverImmediately(target);
        }
    }

    function hidePopoverImmediately(target) {
        const $popover = $(`[data-popover-id="${target.substring(1)}"]`);
        $popover.removeClass('show');
        $(`[data-popover-target="${target}"]`).removeAttr('data-popover-active');

        // Reset current active popover if this is the one being hidden
        if (currentActivePopover === target) {
            currentActivePopover = null;
            $(document).trigger('popover:activeChanged', { activePopover: null });
        }

        // Trigger custom event
        $popover.trigger('popover:hidden');
    }

    function hideAllPopovers() {
        $('[data-popover-id]').removeClass('show');
        $('[data-popover="button"]').removeAttr('data-popover-active');
        if (currentActivePopover) {
            currentActivePopover = null;
            $(document).trigger('popover:activeChanged', { activePopover: null });
        }
    }

    function forceHidePopover(target) {
        if (triggerType === 'hover') {
            clearTimeout(hideTimeout);
        }
        hidePopoverImmediately(target);
    }

    function hideCurrentPopover() {
        if (currentActivePopover) {
            clearTimeout(hideTimeout);
            const $popover = $(`[data-popover-id="${currentActivePopover.substring(1)}"]`);
            $popover.removeClass('show');
            $(`[data-popover-target="${currentActivePopover}"]`).removeAttr('data-popover-active');

            // Trigger custom event
            $popover.trigger('popover:hidden');

            currentActivePopover = null;
            $(document).trigger('popover:activeChanged', { activePopover: null });
        }
    }

    function positionPopover($button, $popover) {
        const buttonOffset = $button.offset();
        const buttonWidth = $button.outerWidth();
        const buttonHeight = $button.outerHeight();
        const windowWidth = $(window).width();

        // Set initial position to measure dimensions
        $popover.css({
            position: 'absolute',
            visibility: 'hidden',
            display: 'block'
        });

        const popoverWidth = $popover.outerWidth();
        const popoverHeight = $popover.outerHeight();

        // Calculate position
        let left = buttonOffset.left + (buttonWidth / 2) - (popoverWidth / 2);
        const top = buttonOffset.top + buttonHeight + 10;

        // Ensure popover doesn't go off screen
        const padding = 15;
        if (left < padding) {
            left = padding;
        } else if (left + popoverWidth > windowWidth - padding) {
            left = windowWidth - popoverWidth - padding;
        }

        $popover.css({
            position: 'absolute',
            left: left + 'px',
            top: top + 'px',
            visibility: 'visible',
            zIndex: 1060
        });
    }

    function setTriggerType(newTriggerType) {
        if (newTriggerType === 'hover' || newTriggerType === 'click') {
            triggerType = newTriggerType;
            console.log('PopoverModule trigger changed to:', triggerType);
            // Rebind events with new trigger type
            $(document).off('.popover'); // Remove all popover namespaced events
            bindEvents();
        } else {
            console.warn('Invalid trigger type. Use "hover" or "click"');
        }
    }

    function getTriggerType() {
        return triggerType;
    }

    // Public API
    return {
        init: init,
        show: showPopover,
        hide: hidePopover,
        forceHide: forceHidePopover,
        hideAll: hideAllPopovers,
        hideCurrentPopover: hideCurrentPopover,
        setTriggerType: setTriggerType,
        getTriggerType: getTriggerType,
        getCurrentActive: function () { return currentActivePopover; }
    };
})();



$(document).ready(function () {
    // Ensure single initialization
    if (window.modulesInitialized) {
        console.warn('Modules already initialized');
        return;
    }

    // Initialize universal popover functionality
    // Có thể chọn 'hover' hoặc 'click' trigger
    PopoverModule.init({ trigger: 'click' }); 
}); 