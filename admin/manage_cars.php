<?php
session_start();
include("db_conn.php");

// Check if admin is logged in
if (!isset($_SESSION['id']) || !isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_brand'])) {
        $brand_name = validate($_POST['brand_name']);
        $stmt = $conn->prepare("INSERT INTO car_brands (brand_name) VALUES (?)");
        $stmt->bind_param("s", $brand_name);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['add_model'])) {
        $brand_id = (int)$_POST['brand_id'];
        $model_name = validate($_POST['model_name']);
        $year_from = (int)$_POST['year_from'];
        $year_to = (int)$_POST['year_to'];
        
        $stmt = $conn->prepare("INSERT INTO car_models (brand_id, model_name, year_from, year_to) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isii", $brand_id, $model_name, $year_from, $year_to);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch all brands
$brands = $conn->query("SELECT * FROM car_brands ORDER BY brand_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cars - Admin Panel</title>
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <?php include "partial-fronts/header-sidebar.php"; ?>

    <div class="container mt-5">
        <h2>Manage Car Brands and Models</h2>
        
        <!-- Add Brand Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Add New Brand</h4>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="brand_name" placeholder="Brand Name" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="add_brand" class="btn btn-primary">Add Brand</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Model Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Add New Model</h4>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-4">
                        <select name="brand_id" class="form-control select2" required>
                            <option value="">Select Brand</option>
                            <?php while ($brand = $brands->fetch_assoc()): ?>
                                <option value="<?php echo $brand['id']; ?>"><?php echo htmlspecialchars($brand['brand_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="model_name" placeholder="Model Name" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="year_from" placeholder="Year From" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="year_to" placeholder="Year To" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_model" class="btn btn-primary">Add Model</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Display Existing Brands and Models -->
        <div class="card">
            <div class="card-header">
                <h4>Existing Brands and Models</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Brand</th>
                                <th>Models</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $brands->data_seek(0);
                            while ($brand = $brands->fetch_assoc()):
                                $models = $conn->query("SELECT * FROM car_models WHERE brand_id = {$brand['id']} ORDER BY model_name");
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($brand['brand_name']); ?></td>
                                <td>
                                    <ul class="list-unstyled">
                                        <?php while ($model = $models->fetch_assoc()): ?>
                                            <li>
                                                <?php echo htmlspecialchars($model['model_name']); ?> 
                                                (<?php echo $model['year_from']; ?> - <?php echo $model['year_to']; ?>)
                                            </li>
                                        <?php endwhile; ?>
                                    </ul>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning">Edit</button>
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
</body>
</html> 