<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id']) && !isset($_SESSION['worker_id'])) {
    header('Location: login.php');
    exit;
}
$chatId = isset($_GET['chat_id']) ? intval($_GET['chat_id']) : null;
if (!$chatId) {
    header('Location: chat-screen.php');
    exit;
}
$userType = isset($_SESSION['user_id']) ? 'user' : (isset($_SESSION['admin_id']) ? 'admin' : (isset($_SESSION['worker_id']) ? 'worker' : 'guest'));
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : (isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : (isset($_SESSION['worker_id']) ? intval($_SESSION['worker_id']) : 'null'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nati Automotive - Chat</title>
    <meta name="description" content="Quality automotive services, GPS tracking, and professional car care at Nati Automotive">
    <meta name="keywords" content="automotive, car service, GPS tracking, vehicle maintenance">
    <link rel="icon" href="assets/images/favicon/Nati-logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bottom-nav-universal.css">
    <link rel="stylesheet" href="assets/css/new-home-layout.css">
    <link rel="stylesheet" href="assets/css/mobile-header.css">
    <link rel="stylesheet" href="assets/css/chat.css">
    <script>
    window.CHAT_ID = <?php echo $chatId; ?>;
    window.SENDER_TYPE = '<?php echo $userType; ?>';
    window.SENDER_ID = <?php echo $userId; ?>;
    </script>
</head>
<body>
    <div class="site-content">
        <!-- Header start -->
        <header id="top-navbar" class="top-navbar"> 
            <div class="container">
                <div class="top-navbar_full">
                    <div class="back-btn">
                        <a href="chat-screen.php">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="top-navbar-title">
                        <p>Chat</p>
                    </div>
                    <div class="skip-btn-goal">
                        <a href="chat-screen.php">
                            <i class="fas fa-comment-dots"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="navbar-boder"></div>
        </header>
        <!-- Header end -->
        <!-- Single chat content start-->
        <section id="single-chat-screen">
            <div class="container">
                <div class="amigo-chat-AI-main">
                    <h1 class="d-none">Chat</h1>
                    <h2 class="d-none">Hidden</h2>
                    <div class="chat">
                        <div class="messages">
                            <div class="messages-content"></div>
                        </div>
                        <div class="chat-input">
                            <button type="submit" class="message-submit messages-plus-icon">
                                <i class="fas fa-plus"></i>
                            </button>
                            <div class="message-box">
                                <textarea class="message-input" placeholder="Write here......"></textarea>
                                <i class="fas fa-microphone specker"></i>
                            </div>
                            <button type="submit" class="message-submit">
                                <i class="fas fa-paper-plane send-icons"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Single chat content end-->
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/chat_scrollbar.js"></script>
    <script src="assets/js/chat.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html> 