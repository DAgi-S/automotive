<?php 
session_start(); // Start the session
define('INCLUDED', true);

// Include DB_con class
require_once 'partial-front/db_con.php';

// Create database connection instance
$db = new DB_con();

// Helper functions
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function redirect_with_message($location, $message = '', $type = 'error') {
    $params = [];
    if (!empty($message)) {
        $params[$type] = urlencode($message);
    }
    $url = $location . (!empty($params) ? '?' . http_build_query($params) : '');
    header("Location: $url");
    exit();
}

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    try {
        // Validate and sanitize inputs
        $name = sanitize_input($_POST['name'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $phonenum = sanitize_input($_POST['phonenum'] ?? '');
        $password = $_POST['password'] ?? '';
        $cpassword = $_POST['cpassword'] ?? '';
        $carbrand = sanitize_input($_POST['carbrand'] ?? '');

        // Validation
        $errors = [];
        
        if (empty($name) || strlen($name) < 2) {
            $errors[] = "Name must be at least 2 characters long";
        }
        
        if (empty($email) || !validate_email($email)) {
            $errors[] = "Please enter a valid email address";
        }
        
        if (empty($phonenum) || strlen($phonenum) < 10) {
            $errors[] = "Please enter a valid phone number";
        }
        
        if (empty($password) || strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters long";
        }
        
        if ($password !== $cpassword) {
            $errors[] = "Passwords do not match";
        }
        
        if (empty($carbrand)) {
            $errors[] = "Please select a car brand";
        }

        // Check if email already exists
        $email_check = "SELECT id FROM tbl_user WHERE email = '$email' LIMIT 1";
        $result = $db->query($email_check);
        if ($result && $result->num_rows > 0) {
            $errors[] = "Email address is already registered";
        }

        if (!empty($errors)) {
            redirect_with_message("signup.php", implode(", ", $errors));
        }

        // Handle file upload
        $new_img_name = 'default-profile.jpg'; // Default profile image
        
        if (isset($_FILES['my_image']) && $_FILES['my_image']['error'] === 0) {
            $img_name = $_FILES['my_image']['name'];
            $img_size = $_FILES['my_image']['size'];
            $tmp_name = $_FILES['my_image']['tmp_name'];
            
            // Validate file size (max 2MB)
            if ($img_size > 2000000) {
                redirect_with_message("signup.php", "Profile image must be less than 2MB");
            }
            
            // Validate file type
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);
            $allowed_exs = array("jpg", "jpeg", "png", "gif");
            
            if (in_array($img_ex_lc, $allowed_exs)) {
                $new_img_name = uniqid("USER-", true) . '.' . $img_ex_lc;
                $img_upload_path = 'assets/img/' . $new_img_name;
                
                if (!move_uploaded_file($tmp_name, $img_upload_path)) {
                    redirect_with_message("signup.php", "Failed to upload profile image");
                }
            } else {
                redirect_with_message("signup.php", "Only JPG, JPEG, PNG, and GIF files are allowed");
            }
        }

        // Hash password using modern method
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user into database
        if ($db->insert_users($name, $phonenum, $hashed_password, $email, $carbrand, $new_img_name)) {
            redirect_with_message("login.php", "Account created successfully! Please login with your credentials.", "success");
        } else {
            redirect_with_message("signup.php", "Failed to create account. Please try again.");
        }
        
    } catch (Exception $e) {
        error_log("Signup error: " . $e->getMessage());
        redirect_with_message("signup.php", "An error occurred. Please try again.");
    }
}

// Get car brands for dropdown
function getCarBrands($db) {
    $brands = [];
    $result = $db->query("SELECT id, brand_name FROM car_brands ORDER BY brand_name ASC");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $brands[] = $row;
        }
    }
    return $brands;
}

$car_brands = getCarBrands($db);

// Get error/success messages from URL
$error_message = '';
$success_message = '';
if (isset($_GET['error'])) {
    $error_message = urldecode($_GET['error']);
}
if (isset($_GET['success'])) {
    $success_message = urldecode($_GET['success']);
}
if (isset($_GET['message'])) {
    $error_message = urldecode($_GET['message']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sign Up - Nati Automotive</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="description" content="Create your account at Nati Automotive for quality automotive services">
    <meta name="keywords" content="signup, register, automotive, car service">
    <link rel="icon" href="assets/images/favicon/Nati-logo.png" />
    <link
      href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100;200;300;400;500;600;700;800;900&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="assets/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* Enhanced Signup Page Styling */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'League Spartan', sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .site-content {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
        }
        
        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .sign-in-login {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .login-txt {
            color: white;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .block-footer {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .block-footer p {
            color: white;
            font-weight: 500;
            margin: 0;
        }
        
        .block-footer a {
            color: #ffd700;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .block-footer a:hover {
            color: #fff;
            text-decoration: underline;
        }
        
        .sign-up-login-form {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            margin-bottom: 30px;
        }
        
        .form-details-sign-in, .mobile-form {
            position: relative;
            margin-bottom: 20px;
        }
        
        .form-details-sign-in span {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
            color: #6c757d;
        }
        
        .sign-in-custom-input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 16px;
            background: #f8f9fa;
            transition: all 0.3s ease;
            outline: none;
            box-sizing: border-box;
        }
        
        .mobile-form .sign-in-custom-input {
            padding-left: 15px;
        }
        
        .sign-in-custom-input:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .sign-in-custom-input.valid {
            border-color: #28a745;
        }
        
        .sign-in-custom-input.invalid {
            border-color: #dc3545;
        }
        
        #eye, #eye1 {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            transition: color 0.3s ease;
            z-index: 3;
        }
        
        #eye:hover, #eye1:hover {
            color: #667eea;
        }
        
        /* File upload styling */
        .file-upload-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-upload-input {
            position: absolute;
            left: -9999px;
        }
        
        .file-upload-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            border: 2px dashed #e1e8ed;
            border-radius: 12px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            justify-content: center;
        }
        
        .file-upload-label:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }
        
        .file-upload-label.has-file {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.05);
        }
        
        .file-upload-text {
            color: #6c757d;
            font-weight: 500;
        }
        
        /* Select dropdown styling */
        select.sign-in-custom-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 40px;
        }
        
        .sign-in-btn button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .sign-in-btn button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .sign-in-btn button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        /* Alert Styles */
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border: none;
            border-radius: 12px;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-left-color: #dc3545;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-left-color: #28a745;
        }
        
        /* Social media section */
        .or-section {
            text-align: center;
            margin: 30px 0 20px;
            position: relative;
        }
        
        .or-section::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.3);
            z-index: 1;
        }
        
        .or-section p {
            background: transparent;
            padding: 0 20px;
            margin: 0;
            color: white;
            position: relative;
            z-index: 2;
            display: inline-block;
            font-weight: 500;
        }
        
        .sign-in-social-media-full {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .sign-in-social-media-deatails {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .sign-in-social-media-deatails:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.3);
        }
        
        /* Field error styling */
        .field-error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            animation: slideDown 0.3s ease-out;
        }
        
        /* Loading states */
        .loading {
            display: none;
        }
        
        .btn-loading .btn-text {
            display: none;
        }
        
        .btn-loading .loading {
            display: inline;
        }
        
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Form animations */
        .form-details-sign-in, .mobile-form {
            animation: slideUp 0.6s ease-out;
        }
        
        .form-details-sign-in:nth-child(2) { animation-delay: 0.1s; }
        .form-details-sign-in:nth-child(3) { animation-delay: 0.2s; }
        .form-details-sign-in:nth-child(4) { animation-delay: 0.3s; }
        .form-details-sign-in:nth-child(5) { animation-delay: 0.4s; }
        .form-details-sign-in:nth-child(6) { animation-delay: 0.5s; }
        .form-details-sign-in:nth-child(7) { animation-delay: 0.6s; }
        .form-details-sign-in:nth-child(8) { animation-delay: 0.7s; }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }
            
            .sign-up-login-form {
                padding: 30px 20px;
                margin: 0 10px 30px;
            }
            
            .login-txt {
                font-size: 1.8rem;
            }
        }
        
        /* Password strength indicator */
        .password-strength {
            height: 4px;
            background: #e1e8ed;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .password-strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-weak { width: 25%; background: #dc3545; }
        .strength-fair { width: 50%; background: #ffc107; }
        .strength-good { width: 75%; background: #17a2b8; }
        .strength-strong { width: 100%; background: #28a745; }
    </style>
</head>

<body>
  <div class="site-content">
    <!-- Sign up screen start -->
    <section id="sign-up-screen-content">
      <div class="container">
        <div class="sign-in-login">
          <h1 class="login-txt">Create Your Account</h1>
        </div>
        <div class="block-footer">
          <p>
            Already have an account? <a href="login.php">Log in</a>
          </p>
        </div>
        
        <!-- Display error/success messages -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="sign-up-login-form">
          <form method="POST" enctype="multipart/form-data" id="signupForm">
            <!-- Hidden input to ensure signup is always sent -->
            <input type="hidden" name="signup" value="1">
            
            <!-- Full Name Field -->
            <div class="form-details-sign-in">
              <span>
                <i class="fas fa-user"></i>
              </span>
              <input type="text" id="name" name="name" placeholder="Full Name" class="sign-in-custom-input" required />
            </div>
            
            <!-- Email Field -->
            <div class="form-details-sign-in">
              <span>
                <i class="fas fa-envelope"></i>
              </span>
              <input type="email" id="email" name="email" placeholder="Email Address" class="sign-in-custom-input" required />
            </div>
            
            <!-- Phone Number Field -->
            <div class="mobile-form">
              <input type="tel" id="phonenum" name="phonenum" class="sign-in-custom-input" placeholder="Phone Number (+251...)" required />
            </div>
            <!-- Password Field -->
            <div class="form-details-sign-in">
              <span>
                <i class="fas fa-lock"></i>
              </span>
              <input type="password" id="password" name="password" placeholder="Password (min 6 characters)" class="sign-in-custom-input" required />
              <i class="fas fa-eye-slash" id="eye"></i>
              <div class="password-strength">
                <div class="password-strength-fill" id="passwordStrength"></div>
              </div>
            </div>
            
            <!-- Confirm Password Field -->
            <div class="form-details-sign-in">
              <span>
                <i class="fas fa-lock"></i>
              </span>
              <input type="password" id="confirm-password" name="cpassword" placeholder="Confirm Password" class="sign-in-custom-input" required />
              <i class="fas fa-eye-slash" id="eye1"></i>
            </div>

            <!-- Profile Image Upload -->
            <div class="form-details-sign-in">
              <div class="file-upload-wrapper">
                <input type="file" id="my_image" name="my_image" class="file-upload-input" accept="image/*">
                <label for="my_image" class="file-upload-label" id="fileLabel">
                  <i class="fas fa-camera"></i>
                  <span class="file-upload-text">Choose Profile Picture (Optional)</span>
                </label>
              </div>
            </div>

            <!-- Car Brand Selection -->
            <div class="form-details-sign-in">
              <span>
                <i class="fas fa-car"></i>
              </span>
              <select name="carbrand" class="sign-in-custom-input" required>
                <option value="" disabled selected>Select Your Car Brand</option>
                <?php foreach ($car_brands as $brand): ?>
                    <option value="<?php echo htmlspecialchars($brand['brand_name']); ?>">
                        <?php echo htmlspecialchars($brand['brand_name']); ?>
                    </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <!-- Submit Button -->
            <div class="sign-in-btn">
              <button type="submit" id="submitBtn">
                <span class="btn-text">
                  <i class="fas fa-user-plus me-2"></i>Create Account
                </span>
                <span class="loading">
                  <div class="spinner"></div> Creating Account...
                </span>
              </button>
				        </div>
          </form>

        </div>
        
        <div class="or-section">
          <p>or continue with</p>
        </div>
        <div class="sign-in-social-media">
          <div class="sign-in-social-media-full">
            <a href="https://www.facebook.com/" target="_blank" class="sign-in-social-media-deatails" title="Continue with Facebook">
              <i class="fab fa-facebook-f" style="color: #1877f2; font-size: 18px;"></i>
            </a>
            <a href="https://www.google.com/" target="_blank" class="sign-in-social-media-deatails" title="Continue with Google">
              <i class="fab fa-google" style="color: #db4437; font-size: 18px;"></i>
            </a>
            <a href="https://www.apple.com/" target="_blank" class="sign-in-social-media-deatails" title="Continue with Apple">
              <i class="fab fa-apple" style="color: #000; font-size: 18px;"></i>
            </a>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Scripts -->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  
  <!-- Enhanced signup functionality -->
  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('signupForm');
      const passwordInput = document.getElementById('password');
      const confirmPasswordInput = document.getElementById('confirm-password');
      const emailInput = document.getElementById('email');
      const nameInput = document.getElementById('name');
      const phoneInput = document.getElementById('phonenum');
      const carbrandSelect = document.querySelector('select[name="carbrand"]');
      const fileInput = document.getElementById('my_image');
      const fileLabel = document.getElementById('fileLabel');
      const submitBtn = document.getElementById('submitBtn');
      const btnText = submitBtn.querySelector('.btn-text');
      const loadingText = submitBtn.querySelector('.loading');
      const eyeIcon = document.getElementById('eye');
      const eyeIcon1 = document.getElementById('eye1');
      const passwordStrength = document.getElementById('passwordStrength');
      
      // Password visibility toggles
      if (eyeIcon && passwordInput) {
          eyeIcon.addEventListener('click', function() {
              togglePasswordVisibility(passwordInput, eyeIcon);
          });
      }
      
      if (eyeIcon1 && confirmPasswordInput) {
          eyeIcon1.addEventListener('click', function() {
              togglePasswordVisibility(confirmPasswordInput, eyeIcon1);
          });
      }
      
      function togglePasswordVisibility(input, icon) {
          if (input.type === 'password') {
              input.type = 'text';
              icon.classList.remove('fa-eye-slash');
              icon.classList.add('fa-eye');
          } else {
              input.type = 'password';
              icon.classList.remove('fa-eye');
              icon.classList.add('fa-eye-slash');
          }
      }
      
      // Password strength checker
      passwordInput.addEventListener('input', function() {
          const password = this.value;
          const strength = checkPasswordStrength(password);
          updatePasswordStrength(strength);
          validateField(this);
      });
      
      function checkPasswordStrength(password) {
          let score = 0;
          if (password.length >= 6) score++;
          if (password.length >= 8) score++;
          if (/[a-z]/.test(password)) score++;
          if (/[A-Z]/.test(password)) score++;
          if (/[0-9]/.test(password)) score++;
          if (/[^A-Za-z0-9]/.test(password)) score++;
          
          if (score <= 2) return 'weak';
          if (score <= 3) return 'fair';
          if (score <= 4) return 'good';
          return 'strong';
      }
      
      function updatePasswordStrength(strength) {
          passwordStrength.className = 'password-strength-fill strength-' + strength;
      }
      
      // Real-time validation
      [nameInput, emailInput, phoneInput, passwordInput, confirmPasswordInput].forEach(input => {
          if (input) {
              input.addEventListener('input', function() {
                  validateField(this);
              });
              input.addEventListener('blur', function() {
                  validateField(this);
              });
          }
      });
      
      carbrandSelect.addEventListener('change', function() {
          validateField(this);
      });
      
      function validateField(field) {
          const value = field.value.trim();
          let isValid = true;
          let errorMessage = '';
          
          switch (field.id) {
              case 'name':
                  isValid = value.length >= 2;
                  errorMessage = 'Name must be at least 2 characters long';
                  break;
              case 'email':
                  isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                  errorMessage = 'Please enter a valid email address';
                  break;
              case 'phonenum':
                  isValid = value.length >= 10;
                  errorMessage = 'Please enter a valid phone number';
                  break;
              case 'password':
                  isValid = value.length >= 6;
                  errorMessage = 'Password must be at least 6 characters long';
                  break;
              case 'confirm-password':
                  isValid = value === passwordInput.value && value.length >= 6;
                  errorMessage = 'Passwords do not match';
                  break;
              default:
                  if (field.name === 'carbrand') {
                      isValid = value !== '';
                      errorMessage = 'Please select a car brand';
                  }
          }
          
          // Remove existing error
          const existingError = field.parentNode.querySelector('.field-error');
          if (existingError) {
              existingError.remove();
          }
          
          // Update field styling and show error if needed
          if (value.length > 0) {
              if (isValid) {
                  field.classList.remove('invalid');
                  field.classList.add('valid');
              } else {
                  field.classList.remove('valid');
                  field.classList.add('invalid');
                  
                  const errorDiv = document.createElement('div');
                  errorDiv.className = 'field-error';
                  errorDiv.textContent = errorMessage;
                  field.parentNode.appendChild(errorDiv);
              }
          } else {
              field.classList.remove('valid', 'invalid');
          }
          
          return isValid;
      }
      
      // File upload handling
      fileInput.addEventListener('change', function() {
          const file = this.files[0];
          const fileText = fileLabel.querySelector('.file-upload-text');
          
          if (file) {
              // Validate file size (2MB)
              if (file.size > 2000000) {
                  alert('File size must be less than 2MB');
                  this.value = '';
                  return;
              }
              
              // Validate file type
              const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
              if (!allowedTypes.includes(file.type)) {
                  alert('Only JPG, JPEG, PNG, and GIF files are allowed');
                  this.value = '';
                  return;
              }
              
              fileText.textContent = file.name;
              fileLabel.classList.add('has-file');
          } else {
              fileText.textContent = 'Choose Profile Picture (Optional)';
              fileLabel.classList.remove('has-file');
          }
      });
      
      // Form submission handling
      form.addEventListener('submit', function(e) {
          // Validate all fields
          let isFormValid = true;
          const fields = [nameInput, emailInput, phoneInput, passwordInput, confirmPasswordInput, carbrandSelect];
          
          fields.forEach(field => {
              if (!validateField(field)) {
                  isFormValid = false;
              }
          });
          
          if (!isFormValid) {
              e.preventDefault();
              alert('Please fix all errors before submitting');
              return;
          }
          
          // Check if passwords match
          if (passwordInput.value !== confirmPasswordInput.value) {
              e.preventDefault();
              alert('Passwords do not match');
              return;
          }
          
          // Show loading state
          submitBtn.classList.add('btn-loading');
          submitBtn.disabled = true;
          
          // Let the form submit naturally for server processing
      });
      
      // Auto-hide alerts after 8 seconds
      setTimeout(function() {
          const alerts = document.querySelectorAll('.alert');
          alerts.forEach(alert => {
              alert.style.transition = 'opacity 0.5s ease-out';
              alert.style.opacity = '0';
              setTimeout(() => {
                  if (alert.parentNode) {
                      alert.remove();
                  }
              }, 500);
          });
      }, 8000);
      
      // Enhanced keyboard shortcuts
      document.addEventListener('keydown', function(e) {
          // Enter key on form fields (except submit button)
          if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
              e.preventDefault();
              const fields = [nameInput, emailInput, phoneInput, passwordInput, confirmPasswordInput, carbrandSelect];
              const currentIndex = fields.indexOf(e.target);
              if (currentIndex !== -1 && currentIndex < fields.length - 1) {
                  fields[currentIndex + 1].focus();
              } else if (currentIndex === fields.length - 1) {
                  submitBtn.focus();
              }
          }
      });
      
      // Focus first field on page load
      if (nameInput) {
          nameInput.focus();
      }
  });
  </script>
</body>

</html>