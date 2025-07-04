<?php
// Enhanced Car CRUD Modals with Robust Error Handling
// Get car brands for dropdown with proper error handling
if (!isset($db)) {
    try {
        require_once 'partial-front/db_con.php';
        $db = new DB_con();
    } catch (Exception $e) {
        error_log("Database connection error in modals: " . $e->getMessage());
        $db = null;
    }
}

$brands_result = null;
$brands_array = [];

if ($db) {
    try {
        $brands_query = "SELECT * FROM car_brands ORDER BY brand_name ASC";
        $brands_result = $db->query($brands_query);
        
        if ($brands_result) {
            while ($brand = $brands_result->fetch_assoc()) {
                $brands_array[] = $brand;
            }
        }
    } catch (Exception $e) {
        error_log("Error fetching car brands: " . $e->getMessage());
        // Fallback brands if database fails
        $brands_array = [
            ['brand_name' => 'Toyota'],
            ['brand_name' => 'Honda'],
            ['brand_name' => 'Ford'],
            ['brand_name' => 'Chevrolet'],
            ['brand_name' => 'Nissan'],
            ['brand_name' => 'BMW'],
            ['brand_name' => 'Mercedes-Benz'],
            ['brand_name' => 'Audi'],
            ['brand_name' => 'Volkswagen'],
            ['brand_name' => 'Hyundai']
        ];
    }
}

// Current year for year dropdown
$current_year = date('Y');
?>

<!-- Enhanced Add Car Modal -->
<div class="modal fade" id="addCarModal" tabindex="-1" aria-labelledby="addCarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; border-bottom: none;">
                <h5 class="modal-title" id="addCarModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Add a New Vehicle
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCarForm" enctype="multipart/form-data" novalidate>
                <div class="modal-body" style="background-color: #f8f9fa; padding: 2rem;">
                    <!-- Alert Container -->
                    <div id="addCarAlert" class="alert d-none" role="alert"></div>
                    
                    <div class="row g-4">
                        <!-- Left Column -->
                        <div class="col-lg-7">
                            <div class="card shadow-sm" style="border: none; border-radius: 10px;">
                                <div class="card-body p-4">
                                    <h6 class="text-muted mb-3"><i class="fas fa-car me-2"></i>Vehicle Details</h6>
                                    <div class="row">
                                        <!-- Car Brand -->
                                        <div class="col-md-6 mb-3">
                                            <label for="car_brand" class="form-label required">Car Brand *</label>
                                            <select class="form-select" name="car_brand" id="car_brand" required>
                                                <option value="" disabled selected>Select Brand</option>
                                                <?php foreach ($brands_array as $brand): ?>
                                                    <option value="<?php echo htmlspecialchars($brand['brand_name']); ?>">
                                                        <?php echo htmlspecialchars($brand['brand_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback">Please select a car brand.</div>
                                        </div>

                                        <!-- Car Model -->
                                        <div class="col-md-6 mb-3">
                                            <label for="car_model" class="form-label required">Car Model *</label>
                                            <input type="text" class="form-control" name="car_model" id="car_model" 
                                                   placeholder="e.g., Camry, Civic" required>
                                            <div class="invalid-feedback">Please enter the car model.</div>
                                        </div>

                                        <!-- Car Year -->
                                        <div class="col-md-6 mb-3">
                                            <label for="car_year" class="form-label required">Year *</label>
                                            <select class="form-select" name="car_year" id="car_year" required>
                                                <option value="" disabled selected>Select Year</option>
                                                <?php for ($year = $current_year; $year >= 1980; $year--): ?>
                                                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                            <div class="invalid-feedback">Please select the car year.</div>
                                        </div>

                                        <!-- Plate Number -->
                                        <div class="col-md-6 mb-3">
                                            <label for="plate_number" class="form-label required">Plate Number *</label>
                                            <input type="text" class="form-control" name="plate_number" id="plate_number" 
                                                   placeholder="e.g., ABC-123" style="text-transform: uppercase;" required>
                                            <div class="invalid-feedback">Please enter the plate number.</div>
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    <h6 class="text-muted mb-3"><i class="fas fa-tools me-2"></i>Service & Maintenance</h6>
                                    <div class="row">
                                        <!-- Current Mileage -->
                                        <div class="col-md-6 mb-3">
                                            <label for="mile_age" class="form-label">Current Mileage (KM)</label>
                                            <input type="number" class="form-control" name="mile_age" id="mile_age" 
                                                   min="0" placeholder="e.g., 50000">
                                        </div>

                                        <!-- Last Service Date -->
                                        <div class="col-md-6 mb-3">
                                            <label for="service_date" class="form-label">Last Service Date</label>
                                            <input type="date" class="form-control" name="service_date" id="service_date" 
                                                   max="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-lg-5">
                            <div class="card shadow-sm" style="border: none; border-radius: 10px;">
                                <div class="card-body p-4">
                                    <h6 class="text-muted mb-3"><i class="fas fa-camera me-2"></i>Vehicle Images</h6>
                                    <div class="mb-3">
                                        <label for="img1" class="form-label">Main Image (Front View)</label>
                                        <input type="file" class="form-control" name="img1" id="img1" accept="image/*" onchange="previewImage(this, 'preview1')">
                                        <div class="image-preview-box mt-2" id="preview1" style="display: none;"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="img2" class="form-label">Side View Image</label>
                                        <input type="file" class="form-control" name="img2" id="img2" accept="image/*" onchange="previewImage(this, 'preview2')">
                                        <div class="image-preview-box mt-2" id="preview2" style="display: none;"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="img3" class="form-label">Interior/Back Image</label>
                                        <input type="file" class="form-control" name="img3" id="img3" accept="image/*" onchange="previewImage(this, 'preview3')">
                                        <div class="image-preview-box mt-2" id="preview3" style="display: none;"></div>
                                    </div>
                                    <div class="form-text">Max size: 5MB. JPG, PNG, GIF accepted.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #e9ecef; border-top: none;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="addCarSubmitBtn">
                        <i class="fas fa-save me-2"></i>Save Vehicle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.image-preview-box {
    width: 100%;
    height: 150px;
    border: 2px dashed #ddd;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    overflow: hidden;
}
.image-preview-box img {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
}
</style>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Image preview"/>`;
            preview.style.display = 'flex';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        preview.innerHTML = '';
    }
}
</script>

<!-- Enhanced Edit Car Modal -->
<div class="modal fade" id="editCarModal" tabindex="-1" aria-labelledby="editCarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
            <form id="editCarForm" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="car_id" id="edit_car_id">
                <div class="modal-header" style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; border-bottom: none;">
                    <h5 class="modal-title" id="editCarModalLabel"><i class="fas fa-edit me-2"></i>Edit Vehicle Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: #f8f9fa; padding: 2rem;">
                    <!-- Alert and Loading Container -->
                    <div id="editCarAlert" class="alert d-none" role="alert"></div>
                    <div id="editCarLoading" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading vehicle data...</p>
                    </div>

                    <div id="editCarContent" class="row g-4" style="display: none;">
                        <!-- Left Column -->
                        <div class="col-lg-7">
                            <div class="card shadow-sm" style="border: none; border-radius: 10px;">
                                <div class="card-body p-4">
                                    <h6 class="text-muted mb-3"><i class="fas fa-car me-2"></i>Vehicle Details</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_car_brand" class="form-label required">Car Brand *</label>
                                            <select class="form-select" name="car_brand" id="edit_car_brand" required>
                                                <option value="" disabled>Select Brand</option>
                                                <?php foreach ($brands_array as $brand): ?>
                                                    <option value="<?php echo htmlspecialchars($brand['brand_name']); ?>">
                                                        <?php echo htmlspecialchars($brand['brand_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback">Please select a car brand.</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_car_model" class="form-label required">Car Model *</label>
                                            <input type="text" class="form-control" name="car_model" id="edit_car_model" required>
                                            <div class="invalid-feedback">Please enter the car model.</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_car_year" class="form-label required">Year *</label>
                                            <select class="form-select" name="car_year" id="edit_car_year" required>
                                                <option value="" disabled>Select Year</option>
                                                <?php for ($year = $current_year; $year >= 1980; $year--): ?>
                                                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                            <div class="invalid-feedback">Please select the car year.</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_plate_number" class="form-label required">Plate Number *</label>
                                            <input type="text" class="form-control" name="plate_number" id="edit_plate_number" required>
                                            <div class="invalid-feedback">Please enter the plate number.</div>
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <h6 class="text-muted mb-3"><i class="fas fa-tools me-2"></i>Service & Maintenance</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_mile_age" class="form-label">Current Mileage (KM)</label>
                                            <input type="number" class="form-control" name="mile_age" id="edit_mile_age" min="0">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_service_date" class="form-label">Last Service Date</label>
                                            <input type="date" class="form-control" name="service_date" id="edit_service_date" max="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-lg-5">
                            <div class="card shadow-sm" style="border: none; border-radius: 10px;">
                                <div class="card-body p-4">
                                    <h6 class="text-muted mb-3"><i class="fas fa-camera me-2"></i>Update Images (Optional)</h6>
                                    <div class="mb-3">
                                        <label for="edit_img1" class="form-label">Main Image</label>
                                        <input type="file" class="form-control" name="img1" id="edit_img1" accept="image/*" onchange="previewImage(this, 'edit_preview1')">
                                        <div class="image-preview-box mt-2" id="edit_preview1">
                                            <span class="text-muted small">Current image will be shown here</span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_img2" class="form-label">Side View</label>
                                        <input type="file" class="form-control" name="img2" id="edit_img2" accept="image/*" onchange="previewImage(this, 'edit_preview2')">
                                        <div class="image-preview-box mt-2" id="edit_preview2">
                                            <span class="text-muted small">Current image will be shown here</span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_img3" class="form-label">Interior/Back View</label>
                                        <input type="file" class="form-control" name="img3" id="edit_img3" accept="image/*" onchange="previewImage(this, 'edit_preview3')">
                                        <div class="image-preview-box mt-2" id="edit_preview3">
                                            <span class="text-muted small">Current image will be shown here</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #e9ecef; border-top: none;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cancel</button>
                    <button type="submit" class="btn btn-primary" id="editCarSubmitBtn"><i class="fas fa-save me-2"></i>Update Vehicle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enhanced Delete Car Modal -->
<div class="modal fade" id="deleteCarModal" tabindex="-1" aria-labelledby="deleteCarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #dc3545, #c82333); border-bottom: none;">
                <h5 class="modal-title" id="deleteCarModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteCarForm">
                <input type="hidden" name="car_id" id="delete_car_id">
                <div class="modal-body text-center p-4">
                    <div id="deleteCarAlert" class="alert d-none" role="alert"></div>
                    <p class="lead">Are you sure you want to permanently delete this vehicle?</p>
                    <div id="deleteCarInfo" class="text-muted mb-3"></div>
                    <p class="small text-danger">This action cannot be undone. All associated information and images will be permanently deleted.</p>
                </div>
                <div class="modal-footer" style="background-color: #f8f9fa; border-top: none;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cancel</button>
                    <button type="submit" class="btn btn-danger" id="deleteCarSubmitBtn"><i class="fas fa-trash me-2"></i>Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>