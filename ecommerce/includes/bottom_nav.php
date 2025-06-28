<?php
if (!defined('INCLUDED')) {
    header("HTTP/1.0 403 Forbidden");
    exit('Direct access forbidden.');
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Bottom Navigation -->
<nav class="fixed-bottom bg-white shadow-lg d-md-none">
    <div class="row text-center nav-row m-0">
        <div class="col-3 p-0">
            <a href="../../home.php" class="nav-link <?php echo $current_page === 'index.php' ? 'text-primary' : 'text-dark'; ?>">
                <i class="fas fa-home"></i>
                <span class="small d-block">Home</span>
            </a>
        </div>
        <div class="col-3 p-0">
            <a href="products.php" class="nav-link <?php echo $current_page === 'products.php' ? 'text-primary' : 'text-dark'; ?>">
                <i class="fas fa-shopping-bag"></i>
                <span class="small d-block">Shop</span>
            </a>
        </div>
        <div class="col-3 p-0">
            <?php
            // Get cart count if user is logged in
            $cart_count = 0;
            if (isset($_SESSION['id'])) {
                include_once("db_con.php");
                $db = new DB_Ecom();
                $cart_items = $db->get_user_cart($_SESSION['id']);
                $cart_count = count($cart_items);
            }
            ?>
            <a href="cart.php" class="nav-link position-relative <?php echo $current_page === 'cart.php' ? 'text-primary' : 'text-dark'; ?>">
                <i class="fas fa-shopping-cart"></i>
                <?php if ($cart_count > 0): ?>
                <span class="position-absolute top-0 start-75 translate-middle badge rounded-pill bg-danger">
                    <?php echo $cart_count; ?>
                </span>
                <?php endif; ?>
                <span class="small d-block">Cart</span>
            </a>
        </div>
        <div class="col-3 p-0">
            <a href="../../profile.php" class="nav-link <?php echo $current_page === 'profile.php' ? 'text-primary' : 'text-dark'; ?>">
                <i class="fas fa-user"></i>
                <span class="small d-block">Account</span>
            </a>
        </div>
    </div>
</nav>

<style>
.nav-row {
    padding: 10px 0;
}
.nav-link {
    padding: 8px 0;
    font-size: 0.9rem;
    text-decoration: none;
    color: inherit;
}
.nav-link i {
    font-size: 1.2rem;
    display: block;
    margin-bottom: 2px;
}
.badge {
    font-size: 0.6rem;
    padding: 0.25em 0.4em;
}
@media (min-width: 768px) {
    .fixed-bottom.d-md-none {
        display: none !important;
    }
}
</style> 