<?php
define('INCLUDED', true);
session_start(); // Start the session

// Check if the session 'password' is set
if (!isset($_SESSION['password'])) {
    header("location: login.php?message=login first"); // Redirect to the login page if the session is not set
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<?php
include 'header.php';
include("partial-front/db_con.php");
include("db_conn.php");

?>

<body>
  <div class="site-content">
    <!-- Header start -->
    <?php
    include 'top_nav.php'
    ?>
    <!-- Header end -->
    <!--Payment screen start -->
    <section id="payment-method-screen">
    <?php
                        $userid = $_SESSION['id'];
                         $sql = "SELECT * FROM tbl_info WHERE userid='$userid' LIMIT 1 ";
                         $result = mysqli_query($conn,$sql);
                         if(mysqli_num_rows($result)==1){
                             $row  = mysqli_fetch_assoc($result);
                             ?>
    <form action="" method="POST" enctype="multipart/form-data">
    <h1 class="d-none">Payment</h1>
    <h2 class="d-none">Hidden</h2>
    <div class="container">
        <div class="payment-method-screen-wrap">
            <div class="mt-32" style="margin-bottom: 10px">
                <h3 class="pay-txt1">Fill the form</h3>
            </div>
        </div>
    </div>
    <div class="navbar-boder"></div>

    <div class="container mt-32">
        <div class="date-picker-sec mt-16">
            <div>
                <h6 style="font-size: 15px; margin-bottom: 10px">CAR BRAND</h6>
                <div class="custom-payment-input">
                    <select name="car_brand" required class="profile-edit-option" style="margin-right: 40px">
                        <option value="<?php echo $row['car_brand'];?>"  selected><?php echo $row['car_brand'];?></option>
                        <option>Suzuki</option>
                        <option>Vitz</option>
                        <option>Corolla</option>
                    </select>
                </div>
            </div>
            <div>
                <h6 style="font-size: 15px; margin-bottom: 10px">YEAR</h6>
                <div class="custom-payment-input">
                    <select name="car_year" required class="profile-edit-option" style="margin-right: 30px">
                        <option value="<?php echo $row['year'];?>"  selected><?php echo $row['year'];?></option>
                        <option>2020</option>
                        <option>2021</option>
                        <option>2022</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-32">
        <div class="date-picker-sec mt-16">
            <div>
                <h6 style="font-size: 15px; margin-bottom: 10px">CAR MODEL</h6>
                <div class="custom-payment-input">
                    <select name="car_model" required class="profile-edit-option" style="margin-right: 60px">
                        <option value="<?php echo $row['car_model'];?>"  selected><?php echo $row['car_model'];?></option>
                        <option>Dizare</option>
                        <option>Platz</option>
                    </select>
                </div>
            </div>
            <div>
                <h6 style="font-size: 15px; margin-bottom: 10px">SERVICE DATE</h6>
                <div class="input-daterange input-group" id="service-datepicker">
                    <div class="form-item">
                        <ul class="input date-picker-input">
                            <li class="input__list">
                                <input required type="text" value="<?php echo $row['service_date'];?>"class="input__item custom-payment-input" name="service_date" placeholder="DD/MM/YYYY" id="service_date" />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-32">
        <div class="date-picker-sec mt-16">
            <div>
                <h6 style="font-size: 15px; margin-bottom: 10px">MILE AGE</h6>
                <div class="input-daterange input-group" id="mileage-datepicker">
                    <div class="form-item">
                        <ul class="input date-picker-input">
                            <li class="input__list">
                                <input required type="number" value="<?php echo $row['mile_age'];?>" name="mile_age" id="mile_age" class="custom-payment-input" placeholder="Mile Age" />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div>
                <h6 style="font-size: 15px; margin-bottom: 10px">OIL CHANGE</h6>
                <div class="input-daterange input-group" id="oilchange-datepicker">
                    <div class="form-item">
                        <ul class="input date-picker-input">
                            <li class="input__list">
                                <input required type="number"  value="<?php echo $row['oil_change'];?>" name="oil_change" class="custom-payment-input" placeholder="Oil Change" id="oil_change" />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-32">
        <div class="date-picker-sec mt-16">
            <div>
                <h6 style="font-size: 15px; margin-bottom: 10px">INSURANCE</h6>
                <div class="input-daterange input-group" id="insurance-datepicker">
                    <div class="form-item">
                        <ul class="input date-picker-input">
                            <li class="input__list">
                                <input required type="text" class="input__item custom-payment-input" value="<?php echo $row['insurance'];?>" name="insurance" placeholder="DD/MM/YYYY" id="insurance_date" />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div>
                <h6 style="font-size: 15px; margin-bottom: 10px">BOLO</h6>
                <div class="input-daterange input-group" id="bolo-datepicker">
                    <div class="form-item">
                        <ul class="input date-picker-input">
                            <li class="input__list">
                                <input required type="text"  value="<?php echo $row['bolo'];?>" class="input__item custom-payment-input" name="bolo" placeholder="DD/MM/YYYY" id="bolo_date" />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>



    
    <div class="container mt-32">
        <div class="date-picker-sec mt-16">
            <div>
                <h6 style="font-size: 15px; margin-bottom: 10px">3rd wegen</h6>
                <div class="input-daterange input-group" id="thirdwegen-datepicker">
                    <div class="form-item">
                        <ul class="input date-picker-input">
                            <li class="input__list">
                                <input required type="text" class="input__item custom-payment-input" value="<?php echo $row['rd_wegen'];?>" name="rd_wegen" placeholder="DD/MM/YYYY" id="rd_wegen_date" />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div>
                <h6 style="font-size: 15px; margin-bottom: 10px">Yemenged fend</h6>
                <div class="input-daterange input-group" id="yemenged-datepicker">
                    <div class="form-item">
                        <ul class="input date-picker-input">
                            <li class="input__list">
                                <input required type="text" value="<?php echo $row['yemenged_fend'];?>" name="yemenged_fend" class="input__item custom-payment-input" placeholder="Exp. Date" id="yemenged_fend_date" />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mt-32">
        <div class="date-picker-sec mt-16">
            <div>
                <h6 style="font-size: 15px; margin-bottom: 10px">car image 1</h6>
                <div class="input-daterange input-group">
                    <div class="form-item">
                        <ul class="input date-picker-input">
                            <li class="input__list">
                            <input type="file" id="car-image3" name="my_image1" accept="image/*" required>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div>
                <h6 style="font-size: 15px; margin-bottom: 10px">Car image 2</h6>
                <div class="input-daterange input-group">
                    <div class="form-item">
                        <ul class="input date-picker-input">
                            <li class="input__list">
                            <input type="file" id="car-image3" name="my_image2" accept="image/*" required>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div>
                <h6 style="font-size: 15px; margin-bottom: 10px">Car image 3</h6>
                <div class="input-daterange input-group">
                    <div class="form-item">
                        <ul class="input date-picker-input">
                            <li class="input__list">
                            <input type="file" id="car-image3" name="my_image3" accept="image/*" required>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        
    </div>

    </div>

    <div class="container mt-32">
        <div class="container">
            <div class="switch-sec payment-switch">
                <div>
                    <h4 class="pay-txt2">Save the information</h4>
                </div>
                <div class="notification-option-switch">
                    <label class="switch">
                        <input type="checkbox" />
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="save-btn-payment mt-8">
                <input style="background-color:black; color:white;" type="submit" name="complete" value="complete">
            </div>
        </div>
    </div>
</form>
<?php
} 
else{
    ?>
    <script>
    window.location.href = 'info2.php';

    </script>
    <?php
}
 ?>
 
<?php

if (isset($_POST['complete']) && isset($_FILES['my_image1']) && isset($_FILES['my_image2']) && isset($_FILES['my_image3'])) {
    $img_name1 = $_FILES['my_image1']['name'];
    $img_size1 = $_FILES['my_image1']['size'];
    $tmp_name1 = $_FILES['my_image1']['tmp_name'];
    $error1 = $_FILES['my_image1']['error'];

    $img_name2 = $_FILES['my_image2']['name'];
    $img_size2 = $_FILES['my_image2']['size'];
    $tmp_name2 = $_FILES['my_image2']['tmp_name'];
    $error2 = $_FILES['my_image2']['error'];

    $img_name3 = $_FILES['my_image3']['name'];
    $img_size3 = $_FILES['my_image3']['size'];
    $tmp_name3 = $_FILES['my_image3']['tmp_name'];
    $error3 = $_FILES['my_image3']['error'];

    if ($error1 === 0 && $error2 === 0 && $error3 === 0) {
        if (check_file($img_name1, $img_size1) && check_file($img_name2, $img_size2) && check_file($img_name3, $img_size3)) {
            $new_img_name1 = generate_name($img_name1);
            $img_upload_path1 = 'assets/img/' . $new_img_name1;
            move_uploaded_file($tmp_name1, $img_upload_path1);

            $new_img_name2 = generate_name($img_name2);
            $img_upload_path2 = 'assets/img/' . $new_img_name2;
            move_uploaded_file($tmp_name2, $img_upload_path2);

            $new_img_name3 = generate_name($img_name3);
            $img_upload_path3 = 'assets/img/' . $new_img_name3;
            move_uploaded_file($tmp_name3, $img_upload_path3);

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
            $userid = $_SESSION['id'];

        
                $db = new db_con;
                $db->insert_info($userid, $car_brand, $year, $car_model, $service_date, $mile_age, $oil_change, $insurance, $bolo, $rd_wegen, $yemenged_fend, $new_img_name1, $new_img_name2, $new_img_name3);
                redirect('home.php', 'Thanks for working with us.');
           
        } else {
            redirect('info.php', 'You can\'t upload files of this type!');
        }
    } else {
        redirect('info.php', 'An unknown error occurred during file upload!');
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

function redirect($url, $message) {
    echo "<script>window.location.href = '$url?message=$message';</script>";
    exit;
}
?>
    </section>
    <?php include 'partial-front/bottom_nav.php'; ?>
    <!-- Tabbar end -->
    <!--SideBar setting menu start-->
    <?php
    include 'option.php'
    ?>
  </div>
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/slick.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/date-time-min.js"></script>
  <script src="assets/js/date-time.js"></script>
  <script src="assets/js/custom.js"></script>
</body>

</html>