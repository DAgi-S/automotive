/**
 * Enhanced My Cars Page JavaScript
 * Robust CRUD operations with comprehensive error handling and validation
 */

// Global variables
let isInitialized = false;
let isSubmitting = false;

// Enhanced CRUD Modal Functions
function openAddModal() {
    // Reset form and clear any previous alerts
    $('#addCarForm')[0].reset();
    $('#addCarForm').removeClass('was-validated');
    $('#addCarAlert').addClass('d-none');
    
    // Clear image previews
    for (let i = 1; i <= 3; i++) {
        $(`#preview${i}`).hide().find('img').attr('src', '');
    }
    
    $('#addCarModal').modal('show');
}

function openEditModal(carId) {
    if (!carId) {
        showAlert('editCarAlert', 'danger', 'Invalid car ID provided.');
        return;
    }

    // Show loading state
    $('#editCarLoading').show();
    $('#editCarContent').hide();
    $('#editCarSubmitBtn').hide();
    $('#editCarAlert').addClass('d-none');
    
    // Reset form
    $('#editCarForm')[0].reset();
    $('#editCarForm').removeClass('was-validated');
    
    // Clear current images
    for (let i = 1; i <= 3; i++) {
        $(`#current_img${i}`).empty();
    }
    
    $('#editCarModal').modal('show');
    
    // Fetch car details with timeout and retry logic
    const fetchCarDetails = (retryCount = 0) => {
        $.ajax({
            url: 'ajax/get_car_details.php',
            method: 'GET',
            data: { car_id: carId },
            timeout: 10000, // 10 second timeout
            dataType: 'json'
        })
        .done(function(response) {
            if (response && response.success && response.data) {
                populateEditForm(response.data);
                $('#editCarLoading').hide();
                $('#editCarContent').show();
                $('#editCarSubmitBtn').show();
            } else {
                throw new Error(response.message || 'Invalid response format');
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Error fetching car details:', { xhr, status, error });
            
            if (retryCount < 2) {
                // Retry up to 2 times
                setTimeout(() => fetchCarDetails(retryCount + 1), 1000);
                return;
            }
            
            $('#editCarLoading').hide();
            let errorMessage = 'Unable to load car details. ';
            
            if (status === 'timeout') {
                errorMessage += 'Request timed out. Please check your connection.';
            } else if (xhr.status === 404) {
                errorMessage += 'Car not found.';
            } else if (xhr.status === 403) {
                errorMessage += 'Access denied.';
            } else {
                errorMessage += 'Please try again later.';
            }
            
            showAlert('editCarAlert', 'danger', errorMessage);
        });
    };
    
    fetchCarDetails();
}

function populateEditForm(car) {
    try {
        // Populate basic information
        $('#edit_car_id').val(car.id);
        $('#edit_car_brand').val(car.brand_name || '');
        $('#edit_car_model').val(car.model_name || '');
        $('#edit_car_year').val(car.car_year || '');
        $('#edit_plate_number').val(car.plate_number || '');
        
        // Populate service information
        $('#edit_mile_age').val(car.mile_age || '');
        $('#edit_service_date').val(car.service_date || '');
        $('#edit_oil_change').val(car.oil_change || '');
        
        // Populate legal documents
        $('#edit_insurance').val(car.insurance || '');
        $('#edit_bolo').val(car.bolo || '');
        $('#edit_rd_wegen').val(car.rd_wegen || '');
        $('#edit_yemenged_fend').val(car.yemenged_fend || '');
        
        // Show current images with error handling
        const imageFields = [
            { field: 'img_name1', container: '#current_img1', label: 'Front View' },
            { field: 'img_name2', container: '#current_img2', label: 'Side View' },
            { field: 'img_name3', container: '#current_img3', label: 'Interior/Back View' }
        ];
        
        imageFields.forEach(({ field, container, label }) => {
            if (car[field]) {
                const imgSrc = `assets/img/${car[field]}`;
                $(container).html(`
                    <div class="position-relative">
                        <img src="${imgSrc}" alt="Current ${label}" class="img-thumbnail" 
                             style="max-height: 80px;" 
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div style="display: none;" class="text-muted small">Image not found</div>
                    </div>
                `);
            } else {
                $(container).html('<div class="text-muted small">No image uploaded</div>');
            }
        });
        
    } catch (error) {
        console.error('Error populating edit form:', error);
        showAlert('editCarAlert', 'danger', 'Error loading car information. Please try again.');
    }
}

function openDeleteModal(carId, carInfo = null) {
    if (!carId) {
        showAlert('deleteCarAlert', 'danger', 'Invalid car ID provided.');
        return;
    }

    $('#delete_car_id').val(carId);
    $('#deleteCarAlert').addClass('d-none');
    
    // If car info is provided, use it; otherwise fetch it
    if (carInfo) {
        $('#deleteCarInfo').html(`
            <strong>${carInfo.brand} ${carInfo.model}</strong><br>
            <small class="text-muted">Year: ${carInfo.year} | Plate: ${carInfo.plate}</small>
        `);
    } else {
        $('#deleteCarInfo').html('<div class="text-muted">Loading car information...</div>');
        
        // Fetch car details for confirmation
        $.get('ajax/get_car_details.php', { car_id: carId })
            .done(function(response) {
                if (response && response.success && response.data) {
                    const car = response.data;
                    $('#deleteCarInfo').html(`
                        <strong>${car.brand_name} ${car.model_name}</strong><br>
                        <small class="text-muted">Year: ${car.car_year} | Plate: ${car.plate_number}</small>
                    `);
                } else {
                    $('#deleteCarInfo').html('<div class="text-muted">Car information unavailable</div>');
                }
            })
            .fail(function() {
                $('#deleteCarInfo').html('<div class="text-muted text-danger">Unable to load car information</div>');
            });
    }
    
    $('#deleteCarModal').modal('show');
}

// Make functions globally available
window.openAddModal = openAddModal;
window.openEditModal = openEditModal;
window.openDeleteModal = openDeleteModal;

// Initialize when DOM is ready
$(document).ready(function() {
    console.log('Enhanced My Cars page JavaScript loaded successfully');
    
    // Check if jQuery and Bootstrap are loaded
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }
    
    if (typeof bootstrap === 'undefined') {
        console.warn('Bootstrap is not loaded - some features may not work');
    }
    
    initializePage();
});

/**
 * Initialize all page functionality
 */
function initializePage() {
    if (isInitialized) return;
    
    setupFormHandlers();
    setupImagePreviews();
    setupFormValidation();
    setupModalEnhancements();
    setupMobileInteractions();
    setupCardInteractions();
    setupScrollAnimations();
    
    isInitialized = true;
}

/**
 * Enhanced form submission handlers with robust error handling
 */
function setupFormHandlers() {
    // Add Car Form Handler
    $('#addCarForm').on('submit', function(e) {
        e.preventDefault();
        
        if (isSubmitting) return;
        
        const form = this;
        if (!validateForm(form)) {
            $(form).addClass('was-validated');
            return;
        }
        
        submitCarForm(form, 'ajax/add_car.php', 'addCarAlert', 'addCarSubmitBtn', 'Car added successfully!');
    });
    
    // Edit Car Form Handler
    $('#editCarForm').on('submit', function(e) {
        e.preventDefault();
        
        if (isSubmitting) return;
        
        const form = this;
        if (!validateForm(form)) {
            $(form).addClass('was-validated');
            return;
        }
        
        submitCarForm(form, 'ajax/edit_car.php', 'editCarAlert', 'editCarSubmitBtn', 'Car updated successfully!');
    });
    
    // Delete Car Form Handler
    $('#deleteCarForm').on('submit', function(e) {
        e.preventDefault();
        
        if (isSubmitting) return;
        
        const formData = new FormData(this);
        submitDeleteForm(formData);
    });
}

/**
 * Enhanced form submission with proper error handling
 */
function submitCarForm(form, url, alertId, btnId, successMessage) {
    isSubmitting = true;
    
    const $submitBtn = $(`#${btnId}`);
    const originalBtnText = $submitBtn.html();
    
    // Show loading state
    $submitBtn.prop('disabled', true).html(`
        <div class="spinner-border spinner-border-sm me-2" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        Processing...
    `);
    
    // Hide any previous alerts
    $(`#${alertId}`).addClass('d-none');
    
    const formData = new FormData(form);
    
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        timeout: 30000, // 30 second timeout
        dataType: 'json'
    })
    .done(function(response) {
        if (response && response.success) {
            showAlert(alertId, 'success', successMessage);
            
            // Close modal after 2 seconds and reload page
            setTimeout(() => {
                $(form).closest('.modal').modal('hide');
                location.reload();
            }, 2000);
        } else {
            const errorMessage = response.message || 'An error occurred while processing your request.';
            showAlert(alertId, 'danger', errorMessage);
        }
    })
    .fail(function(xhr, status, error) {
        console.error('Form submission error:', { xhr, status, error });
        
        let errorMessage = 'Unable to process your request. ';
        
        if (status === 'timeout') {
            errorMessage += 'Request timed out. Please try again.';
        } else if (xhr.status === 413) {
            errorMessage += 'File size too large. Please use smaller images.';
        } else if (xhr.status === 422) {
            errorMessage += 'Invalid data provided. Please check your inputs.';
        } else if (xhr.status >= 500) {
            errorMessage += 'Server error. Please try again later.';
        } else {
            errorMessage += 'Please check your connection and try again.';
        }
        
        showAlert(alertId, 'danger', errorMessage);
    })
    .always(function() {
        // Reset button state
        $submitBtn.prop('disabled', false).html(originalBtnText);
        isSubmitting = false;
    });
}

/**
 * Handle delete form submission
 */
function submitDeleteForm(formData) {
    isSubmitting = true;
    
    const $submitBtn = $('#deleteCarSubmitBtn');
    const originalBtnText = $submitBtn.html();
    
    // Show loading state
    $submitBtn.prop('disabled', true).html(`
        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
        Deleting...
    `);
    
    $.ajax({
        url: 'ajax/delete_car.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        timeout: 15000,
        dataType: 'json'
    })
    .done(function(response) {
        if (response && response.success) {
            showAlert('deleteCarAlert', 'success', 'Car deleted successfully!');
            
            setTimeout(() => {
                $('#deleteCarModal').modal('hide');
                location.reload();
            }, 1500);
        } else {
            showAlert('deleteCarAlert', 'danger', response.message || 'Failed to delete car.');
        }
    })
    .fail(function(xhr, status, error) {
        console.error('Delete error:', { xhr, status, error });
        
        let errorMessage = 'Unable to delete car. ';
        if (status === 'timeout') {
            errorMessage += 'Request timed out.';
        } else {
            errorMessage += 'Please try again.';
        }
        
        showAlert('deleteCarAlert', 'danger', errorMessage);
    })
    .always(function() {
        $submitBtn.prop('disabled', false).html(originalBtnText);
        isSubmitting = false;
    });
}

/**
 * Enhanced form validation
 */
function validateForm(form) {
    const $form = $(form);
    let isValid = true;
    
    // Check required fields
    $form.find('[required]').each(function() {
        const $field = $(this);
        const value = $field.val().trim();
        
        if (!value) {
            $field.addClass('is-invalid');
            isValid = false;
        } else {
            $field.removeClass('is-invalid').addClass('is-valid');
        }
    });
    
    // Validate file sizes
    $form.find('input[type="file"]').each(function() {
        const files = this.files;
        if (files.length > 0) {
            for (let file of files) {
                if (file.size > 5 * 1024 * 1024) { // 5MB limit
                    $(this).addClass('is-invalid');
                    showAlert($form.find('.alert').attr('id'), 'danger', 
                        `File "${file.name}" is too large. Maximum size is 5MB.`);
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            }
        }
    });
    
    // Validate plate number format (basic validation)
    const plateField = $form.find('input[name="plate_number"]');
    if (plateField.length && plateField.val()) {
        const plateValue = plateField.val().trim();
        if (plateValue.length < 3) {
            plateField.addClass('is-invalid');
            isValid = false;
        } else {
            plateField.removeClass('is-invalid').addClass('is-valid');
        }
    }
    
    return isValid;
}

/**
 * Setup image preview functionality
 */
function setupImagePreviews() {
    // Add car form image previews
    for (let i = 1; i <= 3; i++) {
        $(`#img${i}`).on('change', function() {
            handleImagePreview(this, `#preview${i}`);
        });
        
        $(`#edit_img${i}`).on('change', function() {
            handleImagePreview(this, `#current_img${i}`);
        });
    }
}

/**
 * Handle image preview display
 */
function handleImagePreview(input, previewContainer) {
    const file = input.files[0];
    const $container = $(previewContainer);
    
    if (file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            $(input).addClass('is-invalid');
            $container.html('<div class="text-danger small">Invalid file type. Please use JPG, PNG, or GIF.</div>');
            return;
        }
        
        // Validate file size
        if (file.size > 5 * 1024 * 1024) { // 5MB
            $(input).addClass('is-invalid');
            $container.html('<div class="text-danger small">File too large. Maximum size is 5MB.</div>');
            return;
        }
        
        $(input).removeClass('is-invalid');
        
        const reader = new FileReader();
        reader.onload = function(e) {
            $container.html(`
                <img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-height: 100px;">
            `).show();
        };
        reader.readAsDataURL(file);
    } else {
        $container.hide().empty();
    }
}

/**
 * Setup enhanced modal interactions
 */
function setupModalEnhancements() {
    // Modal show/hide events
    $('.modal').on('show.bs.modal', function() {
        $('body').addClass('modal-backdrop-blur');
    });
    
    $('.modal').on('hidden.bs.modal', function() {
        $('body').removeClass('modal-backdrop-blur');
        
        // Reset forms when modal is closed
        const $form = $(this).find('form');
        if ($form.length) {
            $form[0].reset();
            $form.removeClass('was-validated');
            $form.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $form.find('.alert').addClass('d-none');
        }
    });
    
    // Keyboard navigation
    $('.modal').on('keydown', function(e) {
        if (e.key === 'Escape') {
            $(this).modal('hide');
        }
    });
}

/**
 * Setup mobile touch interactions
 */
function setupMobileInteractions() {
    if ('ontouchstart' in window) {
        $('.car-card').on('touchstart', function() {
            $(this).addClass('touch-active');
        });
        
        $('.car-card').on('touchend', function() {
            setTimeout(() => {
                $(this).removeClass('touch-active');
            }, 150);
        });
        
        // Setup swipe gestures for modals
        setupSwipeGestures();
    }
}

/**
 * Setup swipe gestures for mobile modals
 */
function setupSwipeGestures() {
    let startY = 0;
    
    $('.modal-content').on('touchstart', function(e) {
        startY = e.touches[0].clientY;
    });
    
    $('.modal-content').on('touchmove', function(e) {
        if (!startY) return;
        
        const currentY = e.touches[0].clientY;
        const diffY = startY - currentY;
        
        // Swipe down to close
        if (diffY < -100) {
            $(this).closest('.modal').modal('hide');
        }
        
        startY = 0;
    });
}

/**
 * Setup enhanced card interactions
 */
function setupCardInteractions() {
    $('.car-card').on('click', function(e) {
        // Prevent modal opening when clicking on buttons
        if ($(e.target).closest('.btn, button, a').length) {
            return;
        }
        
        // Add click animation
        $(this).addClass('clicked');
        setTimeout(() => {
            $(this).removeClass('clicked');
        }, 200);
    });
}

/**
 * Setup scroll animations
 */
function setupScrollAnimations() {
    function animateOnScroll() {
        $('.car-card').each(function() {
            const elementTop = $(this).offset().top;
            const elementBottom = elementTop + $(this).outerHeight();
            const viewportTop = $(window).scrollTop();
            const viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('visible');
            }
        });
    }
    
    $(window).on('scroll', animateOnScroll);
    animateOnScroll(); // Run once on load
}

/**
 * Show alert message in specified container
 */
function showAlert(containerId, type, message) {
    const $container = $(`#${containerId}`);
    
    $container.removeClass('d-none alert-success alert-danger alert-warning alert-info')
              .addClass(`alert-${type}`)
              .html(`
                  <div class="d-flex align-items-center">
                      <i class="fas fa-${getAlertIcon(type)} me-2"></i>
                      <div>${message}</div>
                  </div>
              `);
    
    // Auto-hide success messages
    if (type === 'success') {
        setTimeout(() => {
            $container.addClass('d-none');
        }, 5000);
    }
}

/**
 * Get appropriate icon for alert type
 */
function getAlertIcon(type) {
    const icons = {
        'success': 'check-circle',
        'danger': 'exclamation-triangle',
        'warning': 'exclamation-circle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

// Additional utility functions for enhanced functionality

/**
 * Quick service booking
 */
function quickServiceBooking(carId) {
    if (carId) {
        window.location.href = `order_service.php?car_id=${carId}`;
    } else {
        window.location.href = 'order_service.php';
    }
}

/**
 * Quick insurance renewal
 */
function quickInsuranceRenewal(carId) {
    if (carId) {
        window.location.href = `renew_insurance.php?car_id=${carId}`;
    } else {
        alert('Please select a car first.');
    }
}

/**
 * Handle mobile card clicks
 */
function handleMobileCardClick(carId) {
    if (window.innerWidth <= 768 && carId) {
        $(`#carModal${carId}`).modal('show');
    }
}

// Make utility functions globally available
window.quickServiceBooking = quickServiceBooking;
window.quickInsuranceRenewal = quickInsuranceRenewal;
window.handleMobileCardClick = handleMobileCardClick;

// Add enhanced CSS for interactions
const enhancedStyles = `
<style>
.modal-backdrop-blur .site-content {
    filter: blur(2px);
    transition: filter 0.3s ease;
}

.car-card.clicked {
    transform: scale(0.98);
    transition: transform 0.1s ease;
}

.car-card.touch-active {
    background: #f8f9fa;
    transform: translateY(-1px);
}

.car-card.visible {
    animation: slideInUp 0.6s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-control.is-valid, .form-select.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.94-.94 1.88 1.88 3.68-3.68.94.94-4.62 4.62z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-invalid, .form-select.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4M7.2 7.4 5.8 6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

@media (max-width: 768px) {
    .modal-xl {
        margin: 0.5rem;
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    .btn {
        font-size: 0.875rem;
    }
}
</style>
`;

// Inject enhanced styles
$('head').append(enhancedStyles); 