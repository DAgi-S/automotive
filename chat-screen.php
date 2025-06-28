<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id']) && !isset($_SESSION['worker_id'])) {
    header('Location: login.php');
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
                        <a href="home.php">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="top-navbar-title">
                        <p>Chat</p>
                    </div>
                    <div class="skip-btn-goal">
                        <a href="notification.php">
                            <i class="fas fa-bell"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="navbar-boder"></div>
        </header>
        <!-- Header end -->
        <!-- Chat screen start -->
        <section id="chatscree2">
            <div class="container">
                <h1 class="d-none">Hidden</h1>
                <button class="btn btn-primary mb-3" id="start-chat-btn">Start New Chat</button>
                <div id="chat-list-container">
                    <!-- Chat list will be loaded here by JS -->
                </div>
            </div>
        </section>
        <!-- Chat screen end -->
        <!-- Tabbar start -->
        <?php include 'partial-front/bottom_nav.php'; ?>
        <!-- Tabbar end -->


    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/chat.js"></script>
    <script>
    // Load chat list for the user
    function loadChatList() {
        $.get('api/chat/list.php', { user_id: window.SENDER_ID }, function(res) {
            let html = '';
            if (res.success && Array.isArray(res.chats) && res.chats.length > 0) {
                res.chats.forEach(function(chat) {
                    html += `<div class="chatscree2-content chat-screen-redirect mb-2">
                        <div class="chatscree2-wrap">
                            <div class="chat-img">
                                <div class="chat-img-sec">
                                    <img src="assets/images/chat/chat1.png" alt="client-img" class="w-100">
                                    <div class="online-btn"><div class="green-btn"></div></div>
                                </div>
                            </div>
                            <div class="chat-content">
                                <div class="chat-content-wrap">
                                    <div class="chat-content-top">
                                        <span class="chat-txt1">Chat #${chat.chat_id}</span>
                                        <span class="chat-txt2">${chat.updated_at || ''}</span>
                                    </div>
                                    <div class="chat-content-wrap-time mt-8">
                                        <span class="chat-txt3">${chat.status}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="single-chat-screen.php?chat_id=${chat.chat_id}" class="stretched-link"></a>
                        <div class="chat-border"></div>
                    </div>`;
                });
            } else {
                html = '<div class="alert alert-info">No chats found. Start a new chat!</div>';
            }
            $('#chat-list-container').html(html);
        }, 'json');
    }
    $(document).ready(function() {
        loadChatList();
        $('#start-chat-btn').click(function() {
            $.post('api/chat/start', { user_id: window.SENDER_ID }, function(res) {
                if (res.success && res.chat_id) {
                    window.location.href = 'single-chat-screen.php?chat_id=' + res.chat_id;
                } else {
                    alert('Failed to start chat.');
                }
            }, 'json');
        });
    });
    </script>
</body>
</html> 