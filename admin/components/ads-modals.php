<!-- Add Ad Modal -->
<div class="modal fade" id="addAdModal" tabindex="-1" aria-labelledby="addAdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAdModalLabel">Create New Advertisement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_ad">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Basic Information -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Advertisement Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title *</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" 
                                                  placeholder="Brief description of the advertisement"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="position" class="form-label">Position *</label>
                                            <select class="form-control" id="position" name="position" required>
                                                <option value="">Select Position</option>
                                                <option value="home_top">Home Page - Top Banner</option>
                                                <option value="home_middle">Home Page - Middle Section</option>
                                                <option value="home_bottom">Home Page - Bottom Banner</option>
                                                <option value="sidebar">Sidebar Advertisement</option>
                                                <option value="service_page">Service Page Banner</option>
                                                <option value="products_top">Products Page - Top</option>
                                                <option value="products_grid">Products Page - Grid</option>
                                                <option value="products_bottom">Products Page - Bottom</option>
                                                <option value="location_top">Location Page - Top</option>
                                                <option value="location_bottom">Location Page - Bottom</option>
                                                <option value="small_banner">Small Banner</option>
                                                <option value="mini_square">Mini Square Ad</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="target_url" class="form-label">Target URL</label>
                                            <input type="url" class="form-control" id="target_url" name="target_url" 
                                                   placeholder="https://example.com">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule & Settings -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Schedule & Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="start_date" class="form-label">Start Date *</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_date" class="form-label">End Date *</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="priority" class="form-label">Priority</label>
                                            <select class="form-control" id="priority" name="priority">
                                                <option value="1">Low (1)</option>
                                                <option value="2">Normal (2)</option>
                                                <option value="3">High (3)</option>
                                                <option value="4">Critical (4)</option>
                                                <option value="5">Urgent (5)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="budget" class="form-label">Budget (Br)</label>
                                            <input type="number" class="form-control" id="budget" name="budget" 
                                                   step="0.01" min="0" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label for="max_impressions" class="form-label">Max Impressions (0 = unlimited)</label>
                                        <input type="number" class="form-control" id="max_impressions" name="max_impressions" 
                                               min="0" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Image Upload -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Advertisement Image</h6>
                                </div>
                                <div class="card-body">
                                    <div class="ad-upload-area" id="adUploadArea">
                                        <div class="upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <h6 class="text-muted">Drop image here or click to browse</h6>
                                            <p class="text-muted small">Supports: JPEG, PNG, GIF, WebP<br>Max size: 5MB</p>
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="browseAdImage">
                                                <i class="fas fa-folder-open"></i> Browse Files
                                            </button>
                                        </div>
                                        
                                        <!-- Image Preview -->
                                        <div class="image-preview" id="adImagePreview" style="display: none;">
                                            <img id="adPreviewImg" src="" alt="Preview" class="preview-image">
                                            <div class="preview-overlay">
                                                <button type="button" class="btn btn-danger btn-sm" id="removeAdImage">
                                                    <i class="fas fa-trash"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Progress Bar -->
                                        <div class="upload-progress" id="adUploadProgress" style="display: none;">
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <small class="text-muted">Uploading...</small>
                                        </div>
                                    </div>
                                    
                                    <input type="file" id="ad_image_file" name="ad_image" accept="image/*" style="display: none;">
                                    
                                    <div class="mt-3">
                                        <h6 class="small text-muted">Recommended Sizes:</h6>
                                        <ul class="small text-muted mb-0">
                                            <li>Home Banner: 1200x300px</li>
                                            <li>Sidebar: 300x600px</li>
                                            <li>Small Banner: 728x90px</li>
                                            <li>Square: 300x300px</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Advertisement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Ad Modal -->
<div class="modal fade" id="editAdModal" tabindex="-1" aria-labelledby="editAdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAdModalLabel">Edit Advertisement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_ad">
                <input type="hidden" id="edit_ad_id" name="ad_id">
                <input type="hidden" id="edit_current_image" name="current_image">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Basic Information -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Advertisement Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="edit_title" class="form-label">Title *</label>
                                        <input type="text" class="form-control" id="edit_title" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_description" class="form-label">Description</label>
                                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="edit_position" class="form-label">Position *</label>
                                            <select class="form-control" id="edit_position" name="position" required>
                                                <option value="home_top">Home Page - Top Banner</option>
                                                <option value="home_middle">Home Page - Middle Section</option>
                                                <option value="home_bottom">Home Page - Bottom Banner</option>
                                                <option value="sidebar">Sidebar Advertisement</option>
                                                <option value="service_page">Service Page Banner</option>
                                                <option value="products_top">Products Page - Top</option>
                                                <option value="products_grid">Products Page - Grid</option>
                                                <option value="products_bottom">Products Page - Bottom</option>
                                                <option value="location_top">Location Page - Top</option>
                                                <option value="location_bottom">Location Page - Bottom</option>
                                                <option value="small_banner">Small Banner</option>
                                                <option value="mini_square">Mini Square Ad</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="edit_target_url" class="form-label">Target URL</label>
                                            <input type="url" class="form-control" id="edit_target_url" name="target_url">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule & Settings -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Schedule & Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="edit_start_date" class="form-label">Start Date *</label>
                                            <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="edit_end_date" class="form-label">End Date *</label>
                                            <input type="date" class="form-control" id="edit_end_date" name="end_date" required>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <label for="edit_status" class="form-label">Status</label>
                                            <select class="form-control" id="edit_status" name="status">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="edit_priority" class="form-label">Priority</label>
                                            <select class="form-control" id="edit_priority" name="priority">
                                                <option value="1">Low (1)</option>
                                                <option value="2">Normal (2)</option>
                                                <option value="3">High (3)</option>
                                                <option value="4">Critical (4)</option>
                                                <option value="5">Urgent (5)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="edit_budget" class="form-label">Budget (Br)</label>
                                            <input type="number" class="form-control" id="edit_budget" name="budget" 
                                                   step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label for="edit_max_impressions" class="form-label">Max Impressions (0 = unlimited)</label>
                                        <input type="number" class="form-control" id="edit_max_impressions" name="max_impressions" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Image Upload -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Advertisement Image</h6>
                                </div>
                                <div class="card-body">
                                    <div id="edit_image_preview" class="mb-3"></div>
                                    
                                    <div class="ad-upload-area" id="editAdUploadArea">
                                        <div class="upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                            <h6 class="text-muted">Upload new image</h6>
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="browseEditAdImage">
                                                <i class="fas fa-folder-open"></i> Browse Files
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <input type="file" id="edit_ad_image_file" name="ad_image" accept="image/*" style="display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Advertisement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Upload Area Styles */
.ad-upload-area {
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
    cursor: pointer;
}

.ad-upload-area:hover {
    border-color: #667eea;
    background: #f0f2ff;
}

.ad-upload-area.dragover {
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

.card-header h6 {
    color: #5a5c69;
    font-weight: 600;
}

.modal-xl .modal-dialog {
    max-width: 1200px;
}

/* Edit modal specific styles */
#edit_image_preview img {
    max-width: 100%;
    max-height: 150px;
    object-fit: cover;
    border-radius: 0.25rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize image upload for add modal
    initializeImageUpload('add');
    
    // Initialize image upload for edit modal
    initializeImageUpload('edit');
    
    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    const nextMonth = new Date();
    nextMonth.setMonth(nextMonth.getMonth() + 1);
    const nextMonthStr = nextMonth.toISOString().split('T')[0];
    
    document.getElementById('start_date').value = today;
    document.getElementById('end_date').value = nextMonthStr;
});

function initializeImageUpload(type) {
    const prefix = type === 'add' ? '' : 'edit_';
    const uploadArea = document.getElementById(type === 'add' ? 'adUploadArea' : 'editAdUploadArea');
    const fileInput = document.getElementById(prefix + 'ad_image_file');
    const browseBtn = document.getElementById(type === 'add' ? 'browseAdImage' : 'browseEditAdImage');
    
    if (!uploadArea || !fileInput || !browseBtn) return;
    
    // Browse button click
    browseBtn.addEventListener('click', (e) => {
        e.preventDefault();
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
            handleFilePreview(e.target.files[0], type);
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
            fileInput.files = files;
            handleFilePreview(files[0], type);
        }
    });
    
    // Remove image functionality for add modal
    if (type === 'add') {
        const removeBtn = document.getElementById('removeAdImage');
        const previewDiv = document.getElementById('adImagePreview');
        
        if (removeBtn && previewDiv) {
            removeBtn.addEventListener('click', () => {
                resetUploadArea(type);
            });
        }
    }
}

function handleFilePreview(file, type) {
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
    
    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        if (type === 'add') {
            const previewImg = document.getElementById('adPreviewImg');
            const previewDiv = document.getElementById('adImagePreview');
            const uploadContent = document.querySelector('#adUploadArea .upload-content');
            
            if (previewImg && previewDiv && uploadContent) {
                previewImg.src = e.target.result;
                previewDiv.style.display = 'block';
                uploadContent.style.display = 'none';
            }
        } else {
            // For edit modal, just show a simple preview
            const previewDiv = document.getElementById('edit_image_preview');
            if (previewDiv) {
                previewDiv.innerHTML = `<img src="${e.target.result}" class="ad-image-preview mb-2" style="max-width: 100%; max-height: 150px; object-fit: cover; border-radius: 0.25rem;">`;
            }
        }
    };
    reader.readAsDataURL(file);
}

function resetUploadArea(type) {
    if (type === 'add') {
        const previewDiv = document.getElementById('adImagePreview');
        const uploadContent = document.querySelector('#adUploadArea .upload-content');
        const fileInput = document.getElementById('ad_image_file');
        
        if (previewDiv && uploadContent && fileInput) {
            previewDiv.style.display = 'none';
            uploadContent.style.display = 'flex';
            fileInput.value = '';
        }
    }
}
</script> 