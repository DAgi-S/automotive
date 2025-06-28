<?php
define('INCLUDED', true);
session_start();
if (!isset($_SESSION['password'])) {
    header("location: login.php?message=login first");
    exit;
}

include 'header.php';
include("partial-front/db_con.php");

$db = new db_con;
$car_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$car = $db->get_car_by_id($car_id, $_SESSION['id']);

if (!$car) {
    header("Location: my_cars.php?message=Car not found");
    exit;
}

// Safe assignments for all fields
$mile_age = $car['mile_age'] ?? '';
$oil_change = $car['oil_change'] ?? '';
$insurance = $car['insurance'] ?? '';
$bolo = $car['bolo'] ?? '';
$rd_wegen = $car['rd_wegen'] ?? '';
$yemenged_fend = $car['yemenged_fend'] ?? '';
$car_brand = $car['car_brand'] ?? '';
$car_year = $car['car_year'] ?? '';
$car_model = $car['car_model'] ?? '';
$service_date = $car['service_date'] ?? '';
$plate_number = $car['plate_number'] ?? '';
$trailer_number = $car['trailer_number'] ?? '';
$image1 = $car['image1'] ?? '';
$image2 = $car['image2'] ?? '';
$image3 = $car['image3'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $car_brand = $_POST['car_brand'];
    $year = $_POST['car_year'];
    $car_model = $_POST['car_model'];
    $service_date = $_POST['service_date'];
    $mile_age = $_POST['mile_age'];
    $oil_change = $_POST['oil_change'];
    $insurance = $_POST['insurance'];
    $bolo = $_POST['bolo'];
    $rd_wegen = $_POST['rd_wegen'];
    $yemenged_fend = $_POST['yemenged_fend'];
    $plate_number = $_POST['plate_number'];
    $trailer_number = $_POST['trailer_number'];
    
    $new_images = [null, null, null];
    
    // Handle image uploads
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_FILES["my_image$i"]) && $_FILES["my_image$i"]['error'] === 0) {
            $img = $_FILES["my_image$i"];
            if (check_file($img['name'], $img['size'])) {
                $new_img_name = generate_name($img['name']);
                $img_upload_path = 'assets/img/' . $new_img_name;
                if (move_uploaded_file($img['tmp_name'], $img_upload_path)) {
                    $new_images[$i-1] = $new_img_name;
                    // Delete old image
                    $old_image = $car["image$i"];
                    if ($old_image) {
                        $old_path = 'assets/img/' . $old_image;
                        if (file_exists($old_path)) {
                            unlink($old_path);
                        }
                    }
                }
            }
        }
    }
    
    if ($db->update_car(
        $car_id,
        $_SESSION['id'],
        $car_brand,
        $year,
        $car_model,
        $service_date,
        $mile_age,
        $oil_change,
        $insurance,
        $bolo,
        $rd_wegen,
        $yemenged_fend,
        $new_images[0],
        $new_images[1],
        $new_images[2],
        $plate_number,
        $trailer_number
    )) {
        header("Location: my_cars.php?message=Car updated successfully");
        exit;
    }
}

function check_file($name, $size) {
    $allowed_types = array('jpg', 'jpeg', 'png');
    $file_type = pathinfo($name, PATHINFO_EXTENSION);
    return in_array(strtolower($file_type), $allowed_types) && $size <= 125000000;
}

function generate_name($name) {
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    return uniqid('IMG-', true) . '.' . strtolower($ext);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Car - Nati Automotive</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="description" content="Edit car information at Nati Automotive">
    <link rel="icon" href="assets/images/favicon/Nati-logo.png" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bottom-nav-universal.css">
    <link rel="stylesheet" href="assets/css/new-home-layout.css">
    <link rel="stylesheet" href="assets/css/mobile-header.css">
    <link rel="stylesheet" href="assets/css/profile-compact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
    .select2-container--default .select2-selection--single {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 4px;
        height: 38px;
        line-height: 38px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
        color: #495057;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        padding: 8px;
        font-size: 11px;
    }
    .select2-results__option {
        font-size: 11px;
        padding: 8px 12px;
    }
    .current-images {
        display: flex;
        gap: 10px;
        margin-top: 5px;
    }
    .current-images img {
        width: 60px;
        height: 45px;
        object-fit: cover;
        border-radius: 4px;
    }
    </style>
</head>

<body>
    <div class="site-content">
        
        <section id="payment-method-screen">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="car_brand">Car Brand</label>
                                <select name="car_brand" id="car_brand" required class="form-control select2">
                                    <option value="">Select Car Brand</option>
                                    <?php 
                                    $brands_query = "SELECT * FROM car_brands ORDER BY brand_name";
                                    $brands_result = $db->getConn()->query($brands_query);
                                    while ($brand = $brands_result->fetch_assoc()): 
                                        $selected = ($brand['id'] == $car_brand) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $brand['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($brand['brand_name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="car_year">Year</label>
                                <input type="number" name="car_year" id="car_year" class="form-control" value="<?php echo htmlspecialchars($car_year); ?>" min="1980" max="<?php echo date('Y'); ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="car_model">Car Model</label>
                                <input type="text" name="car_model" id="car_model" class="form-control" value="<?php echo htmlspecialchars($car_model); ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="service_date">Service Date</label>
                                <input type="date" name="service_date" id="service_date" class="form-control" value="<?php echo htmlspecialchars($service_date); ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="mile_age">Current Mileage (KM)</label>
                                <input type="number" name="mile_age" id="mile_age" class="form-control" value="<?php echo htmlspecialchars($mile_age); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label for="oil_change">Last Oil Change</label>
                                <input type="date" name="oil_change" id="oil_change" class="form-control" value="<?php echo htmlspecialchars($oil_change); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="insurance">Insurance Expiry</label>
                                <input type="date" name="insurance" id="insurance" class="form-control" value="<?php echo htmlspecialchars($insurance); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label for="bolo">Bolo Expiry</label>
                                <input type="date" name="bolo" id="bolo" class="form-control" value="<?php echo htmlspecialchars($bolo); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label for="rd_wegen">RD Wegen Expiry</label>
                                <input type="date" name="rd_wegen" id="rd_wegen" class="form-control" value="<?php echo htmlspecialchars($rd_wegen); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label for="yemenged_fend">Yemenged Fend Expiry</label>
                                <input type="date" name="yemenged_fend" id="yemenged_fend" class="form-control" value="<?php echo htmlspecialchars($yemenged_fend); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label for="plate_number">Plate Number</label>
                                <input type="text" class="form-control" id="plate_number" name="plate_number" value="<?php echo htmlspecialchars($plate_number); ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="trailer_number">Trailer Number (Optional)</label>
                                <input type="text" class="form-control" id="trailer_number" name="trailer_number" value="<?php echo htmlspecialchars($trailer_number); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <label>Car Image 1</label>
                            <?php if ($image1): ?>
                                <div class="mb-2"><img src="assets/img/<?php echo htmlspecialchars($image1); ?>" alt="Current Image 1" class="img-thumbnail" style="max-width: 100px;"></div>
                            <?php endif; ?>
                            <input type="file" name="my_image1" accept="image/*" class="form-control" />
                        </div>
                        <div class="col-md-4">
                            <label>Car Image 2</label>
                            <?php if ($image2): ?>
                                <div class="mb-2"><img src="assets/img/<?php echo htmlspecialchars($image2); ?>" alt="Current Image 2" class="img-thumbnail" style="max-width: 100px;"></div>
                            <?php endif; ?>
                            <input type="file" name="my_image2" accept="image/*" class="form-control" />
                        </div>
                        <div class="col-md-4">
                            <label>Car Image 3</label>
                            <?php if ($image3): ?>
                                <div class="mb-2"><img src="assets/img/<?php echo htmlspecialchars($image3); ?>" alt="Current Image 3" class="img-thumbnail" style="max-width: 100px;"></div>
                            <?php endif; ?>
                            <input type="file" name="my_image3" accept="image/*" class="form-control" />
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-between">
                            <input style="background-color:#007bff; color:white; font-size: 14px; padding: 8px 24px; border:none; border-radius:4px;" type="submit" name="update" value="Update Car">
                            <a href="my_cars.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        
        <?php include 'partial-front/bottom_nav.php'; ?>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            width: '100%',
            theme: 'classic',
            placeholder: 'Select an option',
            allowClear: true
        });

        // Handle car brand change
        $('#car_brand').on('change', function() {
            var brandId = $(this).val();
            if (brandId) {
                $.ajax({
                    url: 'get_models.php',
                    type: 'GET',
                    data: { brand_id: brandId },
                    dataType: 'json',
                    success: function(data) {
                        var modelSelect = $('#car_model');
                        var yearSelect = $('#car_year');
                        
                        modelSelect.empty().append('<option value="">Select Car Model</option>');
                        yearSelect.empty().append('<option value="">Select Year</option>');
                        
                        if (data && data.length > 0) {
                            data.forEach(function(model) {
                                modelSelect.append(
                                    $('<option></option>')
                                        .attr('value', model.id)
                                        .text(model.model_name)
                                        .data('year_from', model.year_from)
                                        .data('year_to', model.year_to)
                                );
                            });
                        }
                        
                        modelSelect.trigger('change.select2');
                        yearSelect.trigger('change.select2');
                    }
                });
            }
        });

        // Handle car model change
        $('#car_model').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var yearFrom = selectedOption.data('year_from');
            var yearTo = selectedOption.data('year_to');
            var yearSelect = $('#car_year');
            var currentYear = new Date().getFullYear();
            
            yearSelect.empty().append('<option value="">Select Year</option>');
            
            if (yearFrom && yearTo) {
                var maxYear = Math.min(yearTo, currentYear);
                for (var year = yearFrom; year <= maxYear; year++) {
                    yearSelect.append(
                        $('<option></option>')
                            .attr('value', year)
                            .text(year)
                    );
                }
            }
            
            yearSelect.trigger('change.select2');
        });

        // Trigger initial model load for selected brand
    });
    </script>
</body>
</html> 