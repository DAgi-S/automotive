<?php 
session_start(); // Start the session at the beginning

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

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    try {
        // Validate input
        $email = sanitize_input($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Basic validation
        if (empty($email)) {
            redirect_with_message("login.php", "Email is required");
        }
        
        if (empty($password)) {
            redirect_with_message("login.php", "Password is required");
        }
        
        if (!validate_email($email)) {
            redirect_with_message("login.php", "Please enter a valid email address");
        }

        // Query user from database using DB_con class
        $user_query = "SELECT * FROM tbl_user WHERE email = '$email' LIMIT 1";
        $result = $db->query($user_query);
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Check password - support both new hashed and old MD5 passwords
            $password_valid = false;
            
            if (password_verify($password, $user['password'])) {
                // Modern password hash verified
                $password_valid = true;
            } elseif (md5($password) === $user['password']) {
                // Legacy MD5 password - upgrade to secure hash
                $new_password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Update password to secure hash
                $update_query = "UPDATE tbl_user SET password = '$new_password_hash' WHERE id = " . $user['id'];
                $db->query($update_query);
                
                $password_valid = true;
            }
            
            if ($password_valid) {
                // Login successful - set session variables
                $_SESSION['password'] = $user['password'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['phonenum'] = $user['phonenum'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['id'] = $user['id'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'] ?? 'user';
                $_SESSION['created_at'] = $user['created_at'] ?? '';
                $_SESSION['new_img_name'] = $user['new_img_name'] ?? '';
                $_SESSION['car_brand'] = $user['car_brand'] ?? '';
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                // Redirect to home page
                redirect_with_message("home.php", "Welcome back, " . $user['name'] . "!", "success");
            } else {
                // Invalid password
                redirect_with_message("login.php", "Invalid email or password");
            }
        } else {
            // User not found
            redirect_with_message("login.php", "Invalid email or password");
        }
        
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        redirect_with_message("login.php", "An error occurred. Please try again.");
    }
}

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
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Nati Automotive</title>
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
    <link rel="stylesheet" href="assets/css/slick.css" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/media-query.css" />
    <link rel="stylesheet" href="assets/css/service_order.css" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Enhanced Login Page Styling */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'League Spartan', sans-serif;
        }
        
        .site-content {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        #top-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .back-btn-page a {
            color: white !important;
            font-size: 1.5rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .back-btn-page a:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateX(-5px);
        }
        
        #sign-in-screen-content {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 40px 0;
        }
        
        .container {
            max-width: 400px;
            margin: 0 auto;
        }
        
        .sign-in-login {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-txt {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .sign-in-login-form {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .form-details-sign-in {
            position: relative;
            margin-bottom: 20px;
        }
        
        .form-details-sign-in span {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
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
        }
        
        .sign-in-custom-input:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        #eye {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            transition: color 0.3s ease;
        }
        
        #eye:hover {
            color: #667eea;
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
        
        .remember-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
            font-size: 14px;
        }
        
        .footer-checkbox-sec {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .footer-checkbox-input {
            width: 18px;
            height: 18px;
            accent-color: #667eea;
        }
        
        .forget-btn a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .forget-btn a:hover {
            text-decoration: underline;
        }
        
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
            background: #e1e8ed;
            z-index: 1;
        }
        
        .or-section p {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 20px;
            margin: 0;
            color: #6c757d;
            position: relative;
            z-index: 2;
            display: inline-block;
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
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: 2px solid #e1e8ed;
        }
        
        .sign-in-social-media-deatails:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }
        
        #let-you-footer {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px 0;
            text-align: center;
        }
        
        .block-footer p {
            margin: 0;
            color: white;
            font-weight: 500;
        }
        
        .block-footer a {
            color: #ffd700;
            text-decoration: none;
            font-weight: 600;
        }
        
        .block-footer a:hover {
            text-decoration: underline;
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
        
        .loading {
            display: none;
        }
        
        .btn-loading .btn-text {
            display: none;
        }
        
        .btn-loading .loading {
            display: inline;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 0 20px;
            }
            
            .sign-in-login-form {
                padding: 30px 20px;
                margin: 0 10px;
            }
            
            .login-txt {
                font-size: 1.8rem;
            }
        }
        
        /* Animation for form elements */
        .form-details-sign-in {
            animation: slideUp 0.6s ease-out;
        }
        
        .form-details-sign-in:nth-child(2) {
            animation-delay: 0.1s;
        }
        
        .form-details-sign-in:nth-child(3) {
            animation-delay: 0.2s;
        }
        
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
        
        /* Loading spinner */
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
    </style>
</head>
	
<body>
	<div class="site-content">
		<header id="top-header">
			<div class="container">
				<div class="let-yoy-page-section-full">
					<div class="back-btn-page">
						<a href="index.php" style="color:black"><h1><</h1></a>
					</div>
				</div>
			</div>
		</header>
		<!-- Header end -->
		<!-- Sign in screen start -->
		<section id="sign-in-screen-content">
			<div class="container">
				<div class="sign-in-login">
					<h1 class="login-txt">Login To Your Account</h1>
				</div>
				
				<!-- Display error/success messages -->
				<?php if (!empty($error_message)): ?>
					<div class="alert alert-danger" role="alert">
						<i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
					</div>
				<?php endif; ?>
				
				<?php if (!empty($success_message)): ?>
					<div class="alert alert-success" role="alert">
						<i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
					</div>
				<?php endif; ?>
				
				<div class="sign-in-login-form mt-24">
					<form method="POST" id="loginForm">
						<!-- Hidden input to ensure signin is always sent -->
						<input type="hidden" name="signin" value="1">
						<div class="form-details-sign-in">
							<span>
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<mask id="mask0_330_7186" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
										<rect width="24" height="24" fill="white"/>
									</mask>
									<g mask="url(#mask0_330_7186)">
										<path d="M19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M3 7L12 13L21 7" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</g>
								</svg>
							</span>
							<input type="email" id="Email" name="email" placeholder="Email" class="sign-in-custom-input" required 
								   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
						</div>
						<div class="form-details-sign-in mt-12">
							<span>
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<mask id="mask0_330_7136" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
										<rect width="24" height="24" fill="white"/>
									</mask>
									<g mask="url(#mask0_330_7136)">
										<path d="M17 11H7C5.89543 11 5 11.8954 5 13V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V13C19 11.8954 18.1046 11 17 11Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M12 17C12.5523 17 13 16.5523 13 16C13 15.4477 12.5523 15 12 15C11.4477 15 11 15.4477 11 16C11 16.5523 11.4477 17 12 17Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M8 11V7C8 5.93913 8.42143 4.92172 9.17157 4.17157C9.92172 3.42143 10.9391 3 12 3C13.0609 3 14.0783 3.42143 14.8284 4.17157C15.5786 4.92172 16 5.93913 16 7V11" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</g>
								</svg>
							</span>
							<input type="password" id="password" name="password" placeholder="Password" class="sign-in-custom-input" required>
							<i class="fas fa-eye-slash" id="eye"></i>
						</div>
						<div class="sign-in-btn mt-32">
						      <button name="signin" type="submit" id="submitBtn">
						          <span class="btn-text">
						              <i class="fas fa-sign-in-alt me-2"></i>Sign In
						          </span>
						          <span class="loading">
						              <div class="spinner"></div> Signing in...
						          </span>
						      </button>
				        </div>
					</form>
				</div>
				<div class="remember-section">
					<div class="footer-checkbox-sec">
						<input class="footer-checkbox-input" id="footer-checkbox" type="checkbox">
						<label for="footer-checkbox" class="footer-chec-txt">Remember Me</label>
					</div>
					<div class="forget-btn">
						<a href="forget_pass.php">Forget password?</a>
					</div>
				</div>
				
				<div class="or-section mt-32">
					<p>or continue with</p>
				</div>
				<div class="sign-in-social-media">
					<div class="sign-in-social-media-full">
						<a href="https://www.facebook.com/" target="_blank" class="sign-in-social-media-deatails">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<mask id="mask0_330_7255" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
									<rect width="24" height="24" fill="white"/>
								</mask>
								<g mask="url(#mask0_330_7255)">
									<path d="M12 24C18.6274 24 24 18.6274 24 12C24 5.37258 18.6274 0 12 0C5.37258 0 0 5.37258 0 12C0 18.6274 5.37258 24 12 24Z" fill="#1977F3"/>
									<path d="M16.6711 15.4696L17.2027 12H13.8749V9.74884C13.8749 8.80045 14.3389 7.874 15.8307 7.874H17.3444V4.92083C17.3444 4.92083 15.9708 4.68626 14.6579 4.68626C11.9173 4.68626 10.1252 6.34679 10.1252 9.35565V12H7.07751V15.4696H10.1252V23.8549C10.7361 23.9511 11.3621 24 12 24C12.6379 24 13.264 23.9494 13.8749 23.8549V15.4696H16.6711Z" fill="white"/>
								</g>
							</svg>
						</a>
						<a href="https://www.google.com/" target="_blank" class="sign-in-social-media-deatails">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<mask id="mask0_330_7246" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
									<rect width="24" height="24" fill="white"/>
								</mask>
								<g mask="url(#mask0_330_7246)">
									<path d="M23.49 12.27C23.49 11.48 23.42 10.73 23.3 10H12V14.51H18.47C18.18 15.99 17.33 17.24 16.07 18.09V21.09H19.93C22.19 19 23.49 15.92 23.49 12.27Z" fill="#4285F4"/>
									<path d="M12 24C15.24 24 17.95 22.92 19.93 21.09L16.07 18.09C14.99 18.81 13.62 19.25 12 19.25C8.87004 19.25 6.22004 17.14 5.27004 14.29H1.29004V17.38C3.26004 21.3 7.31004 24 12 24Z" fill="#34A853"/>
									<path d="M5.27 14.29C5.02 13.57 4.89 12.8 4.89 12C4.89 11.2 5.03 10.43 5.27 9.71V6.62H1.29C0.469999 8.24 0 10.06 0 12C0 13.94 0.469999 15.76 1.29 17.38L5.27 14.29Z" fill="#FBBC05"/>
									<path d="M12 4.75C13.77 4.75 15.35 5.36 16.6 6.55L20.02 3.13C17.95 1.19 15.24 0 12 0C7.31004 0 3.26004 2.7 1.29004 6.62L5.27004 9.71C6.22004 6.86 8.87004 4.75 12 4.75Z" fill="#EA4335"/>
								</g>
							</svg>
						</a>
						<a href="https://www.icloud.com/" target="_blank" class="sign-in-social-media-deatails">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<mask id="mask0_330_7241" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
									<rect width="24" height="24" fill="white"/>
								</mask>
								<g mask="url(#mask0_330_7241)">
									<path d="M20.9144 8.1816C20.7752 8.2896 18.3176 9.6744 18.3176 12.7536C18.3176 16.3152 21.4448 17.5752 21.5384 17.6064C21.524 17.6832 21.0416 19.332 19.8896 21.012C18.8624 22.4904 17.7896 23.9664 16.1576 23.9664C14.5256 23.9664 14.1056 23.0184 12.2216 23.0184C10.3856 23.0184 9.7328 23.9976 8.24 23.9976C6.7472 23.9976 5.7056 22.6296 4.508 20.9496C3.1208 18.9768 2 15.912 2 13.0032C2 8.3376 5.0336 5.8632 8.0192 5.8632C9.6056 5.8632 10.928 6.9048 11.924 6.9048C12.872 6.9048 14.3504 5.8008 16.1552 5.8008C16.8392 5.8008 19.2968 5.8632 20.9144 8.1816ZM15.2984 3.8256C16.0448 2.94 16.5728 1.7112 16.5728 0.4824C16.5728 0.312 16.5584 0.1392 16.5272 0C15.3128 0.0456 13.868 0.8088 12.9968 1.8192C12.3128 2.5968 11.6744 3.8256 11.6744 5.0712C11.6744 5.2584 11.7056 5.4456 11.72 5.5056C11.7968 5.52 11.9216 5.5368 12.0464 5.5368C13.136 5.5368 14.5064 4.8072 15.2984 3.8256Z" fill="black"/>
								</g>
							</svg>
						</a>
					</div>
				</div>
			</div>
		</section>
		<!-- Sign in screen end -->
		<!-- Footer start -->
		<footer id="let-you-footer">
			<div class="block-footer">
				<p>Don't have an account? <a href="signup.php">Sign Up</a></p>
			</div>
		</footer>
		<!-- Footer end -->
	</div>
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/slick.min.js"></script>
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<script src="assets/js/custom.js"></script>
	
	<!-- Enhanced login functionality -->
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		const passwordInput = document.getElementById('password');
		const emailInput = document.getElementById('Email');
		const eyeIcon = document.getElementById('eye');
		const loginForm = document.getElementById('loginForm');
		const submitBtn = document.getElementById('submitBtn');
		const btnText = submitBtn.querySelector('.btn-text');
		const loadingText = submitBtn.querySelector('.loading');
		
		// Add floating label effect
		function addFloatingLabelEffect(input) {
			input.addEventListener('focus', function() {
				this.parentElement.classList.add('focused');
			});
			
			input.addEventListener('blur', function() {
				if (this.value === '') {
					this.parentElement.classList.remove('focused');
				}
			});
			
			// Check if already has value on page load
			if (input.value !== '') {
				input.parentElement.classList.add('focused');
			}
		}
		
		addFloatingLabelEffect(emailInput);
		addFloatingLabelEffect(passwordInput);
		
		// Password visibility toggle with smooth transition
		if (eyeIcon && passwordInput) {
			eyeIcon.addEventListener('click', function() {
				const isPassword = passwordInput.type === 'password';
				
				// Add a small animation
				eyeIcon.style.transform = 'scale(0.8)';
				setTimeout(() => {
					passwordInput.type = isPassword ? 'text' : 'password';
					eyeIcon.classList.toggle('fa-eye-slash', !isPassword);
					eyeIcon.classList.toggle('fa-eye', isPassword);
					eyeIcon.style.transform = 'scale(1)';
				}, 100);
			});
		}
		
		// Real-time validation
		emailInput.addEventListener('input', function() {
			const isValid = isValidEmail(this.value);
			this.style.borderColor = this.value === '' ? '#e1e8ed' : (isValid ? '#28a745' : '#dc3545');
		});
		
		passwordInput.addEventListener('input', function() {
			const isValid = this.value.length >= 6;
			this.style.borderColor = this.value === '' ? '#e1e8ed' : (isValid ? '#28a745' : '#dc3545');
		});
		
		// Form submission handling with enhanced validation
		loginForm.addEventListener('submit', function(e) {
			const email = emailInput.value.trim();
			const password = passwordInput.value.trim();
			
			// Reset any previous error styling
			emailInput.style.borderColor = '';
			passwordInput.style.borderColor = '';
			
			let hasError = false;
			
			// Enhanced client-side validation
			if (!email) {
				showFieldError(emailInput, 'Email is required');
				hasError = true;
			} else if (!isValidEmail(email)) {
				showFieldError(emailInput, 'Please enter a valid email address');
				hasError = true;
			}
			
			if (!password) {
				showFieldError(passwordInput, 'Password is required');
				hasError = true;
			} else if (password.length < 6) {
				showFieldError(passwordInput, 'Password must be at least 6 characters');
				hasError = true;
			}
			
			if (hasError) {
				e.preventDefault();
				return;
			}
			
			// Show loading state with animation
			submitBtn.disabled = true;
			submitBtn.classList.add('btn-loading');
			btnText.style.display = 'none';
			loadingText.style.display = 'inline';
			
			// Add a subtle loading animation to the form
			loginForm.style.opacity = '0.8';
		});
		
		// Show field error function
		function showFieldError(field, message) {
			field.style.borderColor = '#dc3545';
			field.focus();
			
			// Remove any existing error message
			const existingError = field.parentElement.querySelector('.field-error');
			if (existingError) {
				existingError.remove();
			}
			
			// Add error message
			const errorDiv = document.createElement('div');
			errorDiv.className = 'field-error';
			errorDiv.style.cssText = `
				color: #dc3545;
				font-size: 12px;
				margin-top: 5px;
				animation: slideDown 0.3s ease-out;
			`;
			errorDiv.textContent = message;
			field.parentElement.appendChild(errorDiv);
			
			// Auto-remove error after field is corrected
			field.addEventListener('input', function removeError() {
				field.style.borderColor = '';
				if (errorDiv.parentElement) {
					errorDiv.remove();
				}
				field.removeEventListener('input', removeError);
			});
		}
		
		// Email validation function
		function isValidEmail(email) {
			const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			return emailRegex.test(email);
		}
		
		// Enhanced alert handling with slide animation
		const alerts = document.querySelectorAll('.alert');
		alerts.forEach(function(alert) {
			// Add entrance animation
			alert.style.opacity = '0';
			alert.style.transform = 'translateY(-20px)';
			setTimeout(() => {
				alert.style.transition = 'all 0.3s ease-out';
				alert.style.opacity = '1';
				alert.style.transform = 'translateY(0)';
			}, 100);
			
			// Auto-hide after 6 seconds
			setTimeout(function() {
				alert.style.transform = 'translateY(-20px)';
				alert.style.opacity = '0';
				setTimeout(function() {
					if (alert.parentElement) {
						alert.remove();
					}
				}, 300);
			}, 6000);
			
			// Add close button to alerts
			const closeBtn = document.createElement('button');
			closeBtn.innerHTML = '&times;';
			closeBtn.style.cssText = `
				background: none;
				border: none;
				font-size: 20px;
				cursor: pointer;
				margin-left: auto;
				opacity: 0.7;
				transition: opacity 0.3s ease;
			`;
			closeBtn.onclick = function() {
				alert.style.transform = 'translateY(-20px)';
				alert.style.opacity = '0';
				setTimeout(() => alert.remove(), 300);
			};
			closeBtn.onmouseenter = function() { this.style.opacity = '1'; };
			closeBtn.onmouseleave = function() { this.style.opacity = '0.7'; };
			
			alert.appendChild(closeBtn);
		});
		
		// Add keyboard shortcuts
		document.addEventListener('keydown', function(e) {
			// Ctrl/Cmd + Enter to submit form
			if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
				e.preventDefault();
				if (!submitBtn.disabled) {
					loginForm.requestSubmit();
				}
			}
		});
		
		// Add focus trap for accessibility
		const focusableElements = loginForm.querySelectorAll(
			'input[type="email"], input[type="password"], button[type="submit"], a[href]'
		);
		const firstElement = focusableElements[0];
		const lastElement = focusableElements[focusableElements.length - 1];
		
		loginForm.addEventListener('keydown', function(e) {
			if (e.key === 'Tab') {
				if (e.shiftKey && document.activeElement === firstElement) {
					e.preventDefault();
					lastElement.focus();
				} else if (!e.shiftKey && document.activeElement === lastElement) {
					e.preventDefault();
					firstElement.focus();
				}
			}
		});
		
		// Add CSS for slideDown animation
		if (!document.querySelector('#loginAnimations')) {
			const style = document.createElement('style');
			style.id = 'loginAnimations';
			style.textContent = `
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
			`;
			document.head.appendChild(style);
		}
	});
	</script>
</body>
</html>