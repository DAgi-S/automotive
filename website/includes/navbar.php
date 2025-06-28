<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="main-nav">
    <div class="container">
        <div class="logo">
            <a href="index.php">
                <img src="assets/images/logo.png" alt="Nati Automotive">
            </a>
        </div>
        <button class="mobile-menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <ul class="nav-links">
            <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
            <li><a href="about.php" class="<?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About</a></li>
            <li><a href="services.php" class="<?php echo $current_page == 'services.php' ? 'active' : ''; ?>">Services</a></li>
            <li><a href="blogs.php" class="<?php echo $current_page == 'blogs.php' ? 'active' : ''; ?>">Blog</a></li>
            <li><a href="contact.php" class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
        </ul>
    </div>
</nav> 