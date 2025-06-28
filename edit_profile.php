<?php
define('INCLUDED', true);
session_start(); // Start the session

// Check if the session 'password' is set
if (!isset($_SESSION['password'])) {
    header("location: login.php?message=login first"); // Redirect to the login page if the session is not set
    exit;
}
include 'header.php';

include("partial-front/db_con.php");

$db = new DB_con();
$conn = $db->getConn();

if (isset($_POST['update']) && isset($_FILES['my_image'])) {
    $img_name = $_FILES['my_image']['name'];
    $img_size = $_FILES['my_image']['size'];
    $tmp_name = $_FILES['my_image']['tmp_name'];
    $error = $_FILES['my_image']['error'];

    // Get form data
    $name = mysqli_real_escape_string($conn, validate($_POST['name']));
    $phonenum = mysqli_real_escape_string($conn, validate($_POST['phonenum']));
    $email = mysqli_real_escape_string($conn, validate($_POST['email']));
    $car_brand = mysqli_real_escape_string($conn, validate($_POST['car_brand']));
    $id = $_SESSION['id'];

    // Handle image upload
    if ($error === 0) {
        if ($img_size > 1250000) {
            echo "<script>alert('Sorry, your file is too large.');</script>";
        } else {
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);
            $allowed_exs = array("jpg", "jpeg", "png");

            if (in_array($img_ex_lc, $allowed_exs)) {
                $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
                $img_upload_path = 'assets/img/' . $new_img_name;
                
                if (move_uploaded_file($tmp_name, $img_upload_path)) {
                    $db = new DB_con();
                    if ($db->udpate_user($name, $phonenum, $email, $car_brand, $new_img_name, $id)) {
                        echo "<script>window.location.href = 'profile.php?message=Profile updated successfully';</script>";
                        exit;
                    } else {
                        echo "<script>alert('Error updating profile. Please try again.');</script>";
                    }
                } else {
                    echo "<script>alert('Error uploading image. Please try again.');</script>";
                }
            } else {
                echo "<script>alert('Invalid file type. Only JPG, JPEG & PNG files are allowed.');</script>";
            }
        }
    } else {
        // If no new image is uploaded, update without changing the image
        $db = new DB_con();
        if ($db->udpate_user($name, $phonenum, $email, $car_brand, $_SESSION['new_img_name'], $id)) {
            echo "<script>window.location.href = 'profile.php?message=Profile updated successfully';</script>";
            exit;
        } else {
            echo "<script>alert('Error updating profile. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile - Nati Automotive</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="description" content="Edit your profile at Nati Automotive">
    <link rel="icon" href="assets/images/favicon/Nati-logo.png" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bottom-nav-universal.css">
    <link rel="stylesheet" href="assets/css/new-home-layout.css">
    <link rel="stylesheet" href="assets/css/mobile-header.css">
    <link rel="stylesheet" href="assets/css/profile-compact.css">

</head>
<body>
  <div class="site-content" style="min-height:100vh; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding-bottom: 90px; display: flex; align-items: center; justify-content: center;">
    <section id="sign-up-screen-content" style="width:100%; max-width: 420px; margin: 0 auto;">
      <div class="container" style="padding:0;">
        <div class="card shadow-lg p-4" style="border-radius: 18px; margin-top: 40px; margin-bottom: 40px; background: #fff;">
          <div class="sign-in-login mb-3 d-flex align-items-center gap-2">
            <a href="profile.php" style="color:#007bff; font-size:1rem;"><i class="fas fa-arrow-left"></i></a>
            <h1 class="login-txt mb-0" style="color: #2c3e50; font-size: 1.5rem; font-weight: 700;">Edit Profile</h1>
          </div>
          <div class="block-footer mb-3"></div>
          <div class="sign-up-login-form">
            <?php
            $id = $_SESSION['id'];
            $sql = "SELECT * FROM tbl_user WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($result && mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
            ?>
            <form method="POST" enctype="multipart/form-data">
              <div class="mb-3 text-center">
                <?php if (!empty($row['new_img_name'])): ?>
                  <img src="assets/img/<?php echo htmlspecialchars($row['new_img_name']); ?>" alt="Profile Image" class="rounded-circle shadow" style="width: 90px; height: 90px; object-fit: cover; border: 3px solid #e9ecef;">
                <?php else: ?>
                  <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 90px; height: 90px; font-size: 2.5rem; color: #aaa;"><i class="fas fa-user"></i></div>
                <?php endif; ?>
              </div>
              <div class="form-group mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Your Name" value="<?php echo htmlspecialchars($row['name']); ?>" class="form-control" required />
              </div>
              <div class="form-group mb-3">
                <label for="Email" class="form-label">Email</label>
                <input type="email" id="Email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" placeholder="Email" class="form-control" required />
              </div>
              <div class="form-group mb-3">
                <label for="phonenum" class="form-label">Mobile Number</label>
                <input type="text" id="phonenum" name="phonenum" value="<?php echo htmlspecialchars($row['phonenum']); ?>" class="form-control" placeholder="Enter Mobile Number" required />
              </div>
              <div class="form-group mb-3">
                <label for="car_brand" class="form-label">Car Brand</label>
                <select name="car_brand" id="car_brand" class="form-control" required>
                  <option value="<?php echo htmlspecialchars($row['car_brand']); ?>" selected><?php echo htmlspecialchars($row['car_brand']); ?></option>
                  <option value="Toyota">Toyota</option>
                  <option value="Honda">Honda</option>
                  <option value="Ford">Ford</option>
                  <option value="BMW">BMW</option>
                  <option value="Mercedes">Mercedes</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div class="form-group mb-3">
                <label for="my_image" class="form-label">Profile Image</label>
                <input type="file" name="my_image" id="my_image" accept="image/*" class="form-control" />
                <?php if (!empty($row['new_img_name'])): ?>
                  <small class="form-text text-muted">Current image: <?php echo htmlspecialchars($row['new_img_name']); ?></small>
                <?php endif; ?>
              </div>
              <div class="d-grid mt-4">
                <input style="background: linear-gradient(135deg, #007bff, #0056b3); color:white; font-size: 1.1rem; padding: 10px 0; border:none; border-radius:8px; font-weight:600; letter-spacing:0.5px;" name="update" type="submit" value="Update Profile">
              </div>
            </form>
            <?php
            } else {
                echo '<div class="alert alert-danger">User not found.</div>';
            }
            mysqli_stmt_close($stmt);
            ?>
          </div>
        </div>
      </div>
    </section>
    <?php include 'partial-front/bottom_nav.php'; ?>
  </div>
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/slick.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/intlTelInput-jquery.min.js"></script>
  <script src="assets/js/country-flag.js"></script>
  <script src="assets/js/custom.js"></script>
</body>
</html>