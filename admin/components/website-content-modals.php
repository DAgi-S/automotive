<!-- Add Hero Slide Modal -->
<div class="modal fade" id="addHeroModal" tabindex="-1" aria-labelledby="addHeroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addHeroModalLabel">Add Hero Slide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_hero_slide">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="hero_title" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="hero_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="hero_subtitle" class="form-label">Subtitle</label>
                        <textarea class="form-control" id="hero_subtitle" name="subtitle" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="hero_button_text" class="form-label">Button Text</label>
                        <input type="text" class="form-control" id="hero_button_text" name="button_text">
                    </div>
                    <div class="mb-3">
                        <label for="hero_button_link" class="form-label">Button Link</label>
                        <input type="text" class="form-control" id="hero_button_link" name="button_link">
                    </div>
                    <div class="mb-3">
                        <label for="hero_background_image" class="form-label">Background Image</label>
                        
                        <!-- File Upload Area -->
                        <div class="image-upload-area" id="heroImageUploadArea">
                            <div class="upload-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Drop image here or click to browse</h5>
                                <p class="text-muted small">Supports: JPEG, PNG, GIF, WebP (Max: 5MB)</p>
                                <button type="button" class="btn btn-outline-primary" id="browseHeroImage">
                                    <i class="fas fa-folder-open"></i> Browse Files
                                </button>
                            </div>
                            
                            <!-- Image Preview -->
                            <div class="image-preview" id="heroImagePreview" style="display: none;">
                                <img id="heroPreviewImg" src="" alt="Preview" class="preview-image">
                                <div class="preview-overlay">
                                    <button type="button" class="btn btn-danger btn-sm" id="removeHeroImage">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="upload-progress" id="heroUploadProgress" style="display: none;">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted">Uploading...</small>
                            </div>
                        </div>
                        
                        <!-- Hidden file input -->
                        <input type="file" id="hero_image_file" name="hero_image_file" accept="image/*" style="display: none;">
                        
                        <!-- Hidden path input for form submission -->
                        <input type="hidden" id="hero_background_image" name="background_image" value="">
                        
                        <!-- Manual path input (fallback) -->
                        <div class="mt-3">
                            <label class="form-label small text-muted">Or enter path manually:</label>
                            <input type="text" class="form-control form-control-sm" id="hero_manual_path" 
                                   placeholder="assets/images/homescreen/auto1.jpg">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="hero_display_order" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="hero_display_order" name="display_order" value="1" min="1">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="hero_is_active" name="is_active" checked>
                        <label class="form-check-label" for="hero_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Slide</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Hero Slide Modal -->
<div class="modal fade" id="editHeroModal" tabindex="-1" aria-labelledby="editHeroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editHeroModalLabel">Edit Hero Slide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_hero_slide">
                <input type="hidden" id="edit_slide_id" name="slide_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_hero_title" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="edit_hero_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_hero_subtitle" class="form-label">Subtitle</label>
                        <textarea class="form-control" id="edit_hero_subtitle" name="subtitle" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_hero_button_text" class="form-label">Button Text</label>
                        <input type="text" class="form-control" id="edit_hero_button_text" name="button_text">
                    </div>
                    <div class="mb-3">
                        <label for="edit_hero_button_link" class="form-label">Button Link</label>
                        <input type="text" class="form-control" id="edit_hero_button_link" name="button_link">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="edit_hero_is_active" name="is_active">
                        <label class="form-check-label" for="edit_hero_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Slide</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Testimonial Modal -->
<div class="modal fade" id="addTestimonialModal" tabindex="-1" aria-labelledby="addTestimonialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTestimonialModalLabel">Add Testimonial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_testimonial">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="testimonial_name" class="form-label">Customer Name *</label>
                        <input type="text" class="form-control" id="testimonial_name" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="testimonial_title" class="form-label">Customer Title</label>
                        <input type="text" class="form-control" id="testimonial_title" name="customer_title" placeholder="e.g., Customer, Business Owner">
                    </div>
                    <div class="mb-3">
                        <label for="testimonial_text" class="form-label">Testimonial Text *</label>
                        <textarea class="form-control" id="testimonial_text" name="testimonial_text" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="testimonial_rating" class="form-label">Rating</label>
                        <select class="form-control" id="testimonial_rating" name="rating">
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="testimonial_display_order" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="testimonial_display_order" name="display_order" value="1" min="1">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="testimonial_is_active" name="is_active" checked>
                        <label class="form-check-label" for="testimonial_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Testimonial</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Testimonial Modal -->
<div class="modal fade" id="editTestimonialModal" tabindex="-1" aria-labelledby="editTestimonialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTestimonialModalLabel">Edit Testimonial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_testimonial">
                <input type="hidden" id="edit_testimonial_id" name="testimonial_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_testimonial_name" class="form-label">Customer Name *</label>
                        <input type="text" class="form-control" id="edit_testimonial_name" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_testimonial_title" class="form-label">Customer Title</label>
                        <input type="text" class="form-control" id="edit_testimonial_title" name="customer_title">
                    </div>
                    <div class="mb-3">
                        <label for="edit_testimonial_text" class="form-label">Testimonial Text *</label>
                        <textarea class="form-control" id="edit_testimonial_text" name="testimonial_text" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_testimonial_rating" class="form-label">Rating</label>
                        <select class="form-control" id="edit_testimonial_rating" name="rating">
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="edit_testimonial_is_active" name="is_active">
                        <label class="form-check-label" for="edit_testimonial_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Testimonial</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add FAQ Modal -->
<div class="modal fade" id="addFaqModal" tabindex="-1" aria-labelledby="addFaqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFaqModalLabel">Add FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_faq">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="faq_question" class="form-label">Question *</label>
                        <input type="text" class="form-control" id="faq_question" name="question" required>
                    </div>
                    <div class="mb-3">
                        <label for="faq_answer" class="form-label">Answer *</label>
                        <textarea class="form-control" id="faq_answer" name="answer" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="faq_category" class="form-label">Category</label>
                        <select class="form-control" id="faq_category" name="category">
                            <option value="general">General</option>
                            <option value="services">Services</option>
                            <option value="products">Products</option>
                            <option value="appointments">Appointments</option>
                            <option value="billing">Billing</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="faq_display_order" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="faq_display_order" name="display_order" value="1" min="1">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="faq_is_active" name="is_active" checked>
                        <label class="form-check-label" for="faq_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit FAQ Modal -->
<div class="modal fade" id="editFaqModal" tabindex="-1" aria-labelledby="editFaqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFaqModalLabel">Edit FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_faq">
                <input type="hidden" id="edit_faq_id" name="faq_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_faq_question" class="form-label">Question *</label>
                        <input type="text" class="form-control" id="edit_faq_question" name="question" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_faq_answer" class="form-label">Answer *</label>
                        <textarea class="form-control" id="edit_faq_answer" name="answer" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_faq_category" class="form-label">Category</label>
                        <select class="form-control" id="edit_faq_category" name="category">
                            <option value="general">General</option>
                            <option value="services">Services</option>
                            <option value="products">Products</option>
                            <option value="appointments">Appointments</option>
                            <option value="billing">Billing</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="edit_faq_is_active" name="is_active">
                        <label class="form-check-label" for="edit_faq_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Social Media Modal -->
<div class="modal fade" id="addSocialModal" tabindex="-1" aria-labelledby="addSocialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSocialModalLabel">Add Social Media Platform</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_social_link">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="social_name" class="form-label">Platform Name *</label>
                        <input type="text" class="form-control" id="social_name" name="platform_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="social_icon" class="form-label">Icon Class *</label>
                        <input type="text" class="form-control" id="social_icon" name="platform_icon" 
                               placeholder="fab fa-facebook" required>
                        <small class="form-text text-muted">Use Font Awesome icon classes (e.g., fab fa-facebook, fab fa-twitter)</small>
                    </div>
                    <div class="mb-3">
                        <label for="social_url" class="form-label">Platform URL *</label>
                        <input type="url" class="form-control" id="social_url" name="platform_url" required>
                    </div>
                    <div class="mb-3">
                        <label for="social_color" class="form-label">Brand Color</label>
                        <input type="color" class="form-control" id="social_color" name="platform_color" value="#3b5998">
                    </div>
                    <div class="mb-3">
                        <label for="social_description" class="form-label">Description</label>
                        <textarea class="form-control" id="social_description" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="social_display_order" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="social_display_order" name="display_order" value="1" min="1">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="social_is_active" name="is_active" checked>
                        <label class="form-check-label" for="social_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Platform</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Social Media Modal -->
<div class="modal fade" id="editSocialModal" tabindex="-1" aria-labelledby="editSocialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSocialModalLabel">Edit Social Media Platform</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_social_link">
                <input type="hidden" id="edit_social_id" name="social_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_social_name" class="form-label">Platform Name *</label>
                        <input type="text" class="form-control" id="edit_social_name" name="platform_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_social_icon" class="form-label">Icon Class *</label>
                        <input type="text" class="form-control" id="edit_social_icon" name="platform_icon" required>
                        <small class="form-text text-muted">Use Font Awesome icon classes (e.g., fab fa-facebook, fab fa-twitter)</small>
                    </div>
                    <div class="mb-3">
                        <label for="edit_social_url" class="form-label">Platform URL *</label>
                        <input type="url" class="form-control" id="edit_social_url" name="platform_url" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_social_color" class="form-label">Brand Color</label>
                        <input type="color" class="form-control" id="edit_social_color" name="platform_color">
                    </div>
                    <div class="mb-3">
                        <label for="edit_social_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_social_description" name="description" rows="2"></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="edit_social_is_active" name="is_active">
                        <label class="form-check-label" for="edit_social_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Platform</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Image Upload Styles */
.image-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 0.5rem;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    background: #f8f9fa;
    position: relative;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-upload-area:hover {
    border-color: #667eea;
    background: #f0f2ff;
}

.image-upload-area.dragover {
    border-color: #667eea;
    background: #e3f2fd;
    transform: scale(1.02);
}

.upload-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.image-preview {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 0.5rem;
    overflow: hidden;
}

.preview-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 0.5rem;
}

.preview-overlay {
    position: absolute;
    top: 10px;
    right: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-preview:hover .preview-overlay {
    opacity: 1;
}

.upload-progress {
    position: absolute;
    bottom: 10px;
    left: 10px;
    right: 10px;
}

.upload-progress .progress {
    height: 8px;
    border-radius: 4px;
    background: rgba(255,255,255,0.3);
}

.upload-progress .progress-bar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.btn-outline-primary:hover {
    transform: translateY(-1px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize image upload functionality
    initializeImageUpload();
});

function initializeImageUpload() {
    const uploadArea = document.getElementById('heroImageUploadArea');
    const fileInput = document.getElementById('hero_image_file');
    const browseBtn = document.getElementById('browseHeroImage');
    const removeBtn = document.getElementById('removeHeroImage');
    const previewDiv = document.getElementById('heroImagePreview');
    const previewImg = document.getElementById('heroPreviewImg');
    const progressDiv = document.getElementById('heroUploadProgress');
    const progressBar = progressDiv.querySelector('.progress-bar');
    const hiddenInput = document.getElementById('hero_background_image');
    const manualInput = document.getElementById('hero_manual_path');
    
    // Browse button click
    browseBtn.addEventListener('click', () => {
        fileInput.click();
    });
    
    // Upload area click
    uploadArea.addEventListener('click', (e) => {
        if (e.target === uploadArea || e.target.closest('.upload-content')) {
            fileInput.click();
        }
    });
    
    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileUpload(e.target.files[0]);
        }
    });
    
    // Drag and drop events
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileUpload(files[0]);
        }
    });
    
    // Remove image button
    removeBtn.addEventListener('click', () => {
        resetUploadArea();
    });
    
    // Manual path input
    manualInput.addEventListener('input', (e) => {
        if (e.target.value.trim()) {
            hiddenInput.value = e.target.value.trim();
            // Show preview if it's a valid image path
            if (e.target.value.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
                showImagePreview(e.target.value);
            }
        } else {
            resetUploadArea();
        }
    });
    
    function handleFileUpload(file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.');
            return;
        }
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size too large. Maximum size is 5MB.');
            return;
        }
        
        // Show progress
        showProgress();
        
        // Create FormData
        const formData = new FormData();
        formData.append('hero_image', file);
        
        // Upload file
        fetch('ajax/upload_hero_image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideProgress();
            
            if (data.success) {
                hiddenInput.value = data.file_path;
                showImagePreview(data.file_path);
                manualInput.value = ''; // Clear manual input
            } else {
                alert('Upload failed: ' + data.message);
                resetUploadArea();
            }
        })
        .catch(error => {
            hideProgress();
            console.error('Upload error:', error);
            alert('Upload failed. Please try again.');
            resetUploadArea();
        });
    }
    
    function showImagePreview(imagePath) {
        previewImg.src = imagePath.startsWith('http') ? imagePath : '../' + imagePath;
        previewDiv.style.display = 'block';
        uploadArea.querySelector('.upload-content').style.display = 'none';
    }
    
    function resetUploadArea() {
        previewDiv.style.display = 'none';
        uploadArea.querySelector('.upload-content').style.display = 'flex';
        hiddenInput.value = '';
        manualInput.value = '';
        fileInput.value = '';
    }
    
    function showProgress() {
        progressDiv.style.display = 'block';
        progressBar.style.width = '0%';
        
        // Simulate progress
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 30;
            if (progress > 90) progress = 90;
            progressBar.style.width = progress + '%';
        }, 200);
        
        // Store interval for cleanup
        progressDiv.dataset.interval = interval;
    }
    
    function hideProgress() {
        const interval = progressDiv.dataset.interval;
        if (interval) {
            clearInterval(interval);
        }
        
        progressBar.style.width = '100%';
        setTimeout(() => {
            progressDiv.style.display = 'none';
            progressBar.style.width = '0%';
        }, 500);
    }
}
</script> 