$(document).ready(function() {
    // Validation cho các URL mạng xã hội
    const socialMediaInputs = $('input[name*="_url"]');
    
    socialMediaInputs.each(function() {
        const input = $(this);
        const fieldName = input.attr('name');
        
        // Thêm icon vào label nếu chưa có
        const label = $('label[for="' + fieldName + '"]');
        if (label.length && !label.find('i').length) {
            let iconClass = '';
            switch(fieldName) {
                case 'facebook_url':
                    iconClass = 'fab fa-facebook-f';
                    break;
                case 'instagram_url':
                    iconClass = 'fab fa-instagram';
                    break;
                case 'youtube_url':
                    iconClass = 'fab fa-youtube';
                    break;
                case 'tiktok_url':
                    iconClass = 'fab fa-tiktok';
                    break;
                case 'twitter_url':
                    iconClass = 'fab fa-twitter';
                    break;
                case 'linkedin_url':
                    iconClass = 'fab fa-linkedin-in';
                    break;
            }
            
            if (iconClass) {
                label.prepend('<i class="' + iconClass + ' me-2"></i>');
            }
        }
        
        // Validation khi blur
        input.on('blur', function() {
            const value = $(this).val().trim();
            const wrapper = $(this).closest('.form-group');
            
            // Xóa message cũ
            wrapper.find('.invalid-feedback').remove();
            $(this).removeClass('is-invalid is-valid');
            
            if (value) {
                if (isValidUrl(value)) {
                    $(this).addClass('is-valid');
                    showSuccessMessage(wrapper, 'URL hợp lệ');
                } else {
                    $(this).addClass('is-invalid');
                    showErrorMessage(wrapper, 'Vui lòng nhập URL hợp lệ (bao gồm https://)');
                }
            }
        });
        
        // Validation khi input
        input.on('input', function() {
            const wrapper = $(this).closest('.form-group');
            wrapper.find('.invalid-feedback, .valid-feedback').remove();
            $(this).removeClass('is-invalid is-valid');
        });
    });
    
    // Hàm kiểm tra URL hợp lệ
    function isValidUrl(string) {
        try {
            new URL(string);
            return string.startsWith('http://') || string.startsWith('https://');
        } catch (_) {
            return false;
        }
    }
    
    // Hiển thị thông báo lỗi
    function showErrorMessage(wrapper, message) {
        const errorDiv = $('<div class="invalid-feedback d-block">' + message + '</div>');
        wrapper.append(errorDiv);
    }
    
    // Hiển thị thông báo thành công
    function showSuccessMessage(wrapper, message) {
        const successDiv = $('<div class="valid-feedback d-block">' + message + '</div>');
        wrapper.append(successDiv);
    }
    
    // Thêm tooltip cho các input
    socialMediaInputs.each(function() {
        const fieldName = $(this).attr('name');
        let tooltipText = '';
        
        switch(fieldName) {
            case 'facebook_url':
                tooltipText = 'Ví dụ: https://facebook.com/username';
                break;
            case 'instagram_url':
                tooltipText = 'Ví dụ: https://instagram.com/username';
                break;
            case 'youtube_url':
                tooltipText = 'Ví dụ: https://youtube.com/channel/...';
                break;
            case 'tiktok_url':
                tooltipText = 'Ví dụ: https://tiktok.com/@username';
                break;
            case 'twitter_url':
                tooltipText = 'Ví dụ: https://twitter.com/username';
                break;
            case 'linkedin_url':
                tooltipText = 'Ví dụ: https://linkedin.com/in/username';
                break;
        }
        
        if (tooltipText) {
            $(this).attr('title', tooltipText);
            $(this).tooltip({
                placement: 'top',
                trigger: 'focus'
            });
        }
    });
    
    // Thêm button để test URL
    socialMediaInputs.each(function() {
        const input = $(this);
        const wrapper = input.closest('.form-group');
        
        // Tạo button test
        const testBtn = $('<button type="button" class="btn btn-sm btn-outline-info ms-2 test-url-btn" title="Kiểm tra URL"><i class="fas fa-external-link-alt"></i></button>');
        
        // Thêm button vào sau input
        input.after(testBtn);
        
        // Xử lý click test button
        testBtn.on('click', function() {
            const url = input.val().trim();
            if (url && isValidUrl(url)) {
                window.open(url, '_blank');
            } else {
                alert('Vui lòng nhập URL hợp lệ trước khi test!');
            }
        });
    });
});
