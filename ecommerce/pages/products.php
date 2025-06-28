<?php
session_start();
define('INCLUDED', true);
require_once('../../includes/config.php');
require_once('../../includes/ad_functions.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Auto Parts & Accessories - Nati Automotive</title>
    <?php require_once('../includes/head.php'); ?>
</head>
<body class="ecommerce-wrapper">
    <?php require_once('../includes/header.php'); ?>
    
    <div class="site-content">
        <?php 
        // Get database connection for ads
        $conn = getDBConnection();
        echo displayAd($conn, 'products_top'); 
        ?>
        
        <!-- Promotional Banners -->
        <div class="promo-banners">
            <div class="promo-banner">
                <span>Free Shipping on orders over Br 1000</span>
                <i class="fas fa-chevron-right"></i>
            </div>
            <div class="promo-banner">
                <span>Special Discount 25% OFF</span>
                <span>Collect</span>
            </div>
        </div>

        <!-- Category Circles -->
        <div class="category-circles">
            <a href="#" class="category-circle">
                <div class="category-circle-img">
                    <i class="fas fa-cog"></i>
                </div>
                <span>Engine</span>
            </a>
            <a href="#" class="category-circle">
                <div class="category-circle-img">
                    <i class="fas fa-wrench"></i>
                </div>
                <span>Brake</span>
            </a>
            <a href="#" class="category-circle">
                <div class="category-circle-img">
                    <i class="fas fa-car"></i>
                </div>
                <span>Suspension</span>
            </a>
            <a href="#" class="category-circle">
                <div class="category-circle-img">
                    <i class="fas fa-bolt"></i>
                </div>
                <span>Electrical</span>
            </a>
            <a href="#" class="category-circle">
                <div class="category-circle-img">
                    <i class="fas fa-tools"></i>
                </div>
                <span>Tools</span>
            </a>
        </div>

        <div class="section-title">
            <h2>Super Deals</h2>
            <a href="#">View more <i class="fas fa-chevron-right"></i></a>
        </div>

        <?php echo displayAd($conn, 'small_banner'); ?>

        <div class="ecommerce-container">
            <!-- Filters Bar -->
            <div class="filters-bar">
                <button class="filter-button active">All Products</button>
                <button class="filter-button">Engine Parts</button>
                <button class="filter-button">Brake System</button>
                <button class="filter-button">Suspension</button>
                <button class="filter-button">Electrical</button>
            </div>

            <!-- Product Grid -->
            <div class="product-grid">
                <?php
                $products = [
                    [
                        'id' => 1,
                        'name' => 'Premium Brake Pads - High Performance',
                        'price' => 89.99,
                        'original_price' => 129.99,
                        'image' => 'https://partsouq.com/assets/tesseract/assets/global/TOYOTA00/source/11/110642.gif',
                        'discount' => 30,
                        'category' => 'Brake System'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Engine Oil Filter - Long Life Protection',
                        'price' => 12.99,
                        'original_price' => 19.99,
                        'image' => 'https://www.ebaymotorsblog.com/motors/blog/wp-content/uploads/2023/08/evo_crankshaft-592x400.jpg',
                        'discount' => 35,
                        'category' => 'Engine Parts'
                    ],
                    [
                        'id' => 3,
                        'name' => 'Performance Boost Kit',
                        'price' => 199.99,
                        'original_price' => 249.99,
                        'image' => 'https://sp-ao.shortpixel.ai/client/to_auto,q_glossy,ret_img,w_495,h_450/https://dizz.com/wp-content/uploads/2023/04/car-turbocharger-isolated-on-white-background-tur-2021-08-27-08-38-29-utc-PhotoRoom.png-PhotoRoom.webp',
                        'discount' => 20,
                        'category' => 'Engine Parts'
                    ],
                    [
                        'id' => 4,
                        'name' => 'Premium Suspension System',
                        'price' => 299.99,
                        'original_price' => 399.99,
                        'image' => 'https://w7.pngwing.com/pngs/260/445/png-transparent-automotive-engine-parts-automotive-engine-parts-car-parts-auto-parts.png',
                        'discount' => 25,
                        'category' => 'Suspension'
                    ]
                ];

                $counter = 0;
                foreach ($products as $product): 
                    if ($counter > 0 && $counter % 4 == 0) {
                        echo displayAd($conn, 'products_grid');
                    }
                ?>
                <div class="product-card" data-category="<?php echo htmlspecialchars($product['category']); ?>" 
                     data-price="<?php echo $product['price']; ?>">
                    <?php if ($product['discount']): ?>
                    <div class="discount-badge">-<?php echo $product['discount']; ?>%</div>
                    <?php endif; ?>
                    
                    <div class="product-image">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             loading="lazy"
                             onerror="this.onerror=null; this.src='../assets/img/placeholder.jpg';">
                        <div class="quick-view">Quick View</div>
                    </div>
                    
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="price-section">
                            <p class="product-price">
                                Br <?php echo number_format($product['price'], 2); ?>
                                <?php if (isset($product['original_price'])): ?>
                                <span class="original-price">Br <?php echo number_format($product['original_price'], 2); ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <button class="add-to-cart-btn" onclick="addToCart(<?php echo htmlspecialchars(json_encode([
                            'id' => $product['id'],
                            'name' => $product['name'],
                            'price' => $product['price'],
                            'image' => $product['image']
                        ])); ?>)">
                            Add to Cart
                        </button>
                    </div>
                </div>
                <?php 
                    $counter++;
                endforeach; 
                ?>
            </div>
        </div>

        <?php echo displayAd($conn, 'products_bottom'); ?>

        <!-- Floating Cart Badge -->
        <a href="cart.php" class="cart-badge">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count">0</span>
        </a>
    </div>

    <?php require_once('../includes/bottom_nav.php'); ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter functionality
        const filterButtons = document.querySelectorAll('.filter-button');
        const products = document.querySelectorAll('.product-card');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                const selectedCategory = this.textContent.trim();
                
                // Filter products
                products.forEach(product => {
                    const productCategory = product.dataset.category;
                    if (selectedCategory === 'All Products' || productCategory === selectedCategory) {
                        product.style.display = '';
                    } else {
                        product.style.display = 'none';
                    }
                });
            });
        });

        // Handle image errors
        document.querySelectorAll('.product-image img').forEach(img => {
            img.addEventListener('error', function() {
                this.src = '../assets/img/placeholder.jpg';
            });
        });
    });

    // Add to cart functionality
    function addToCart(product) {
        fetch('../ajax/add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.count;
                }
                // Show success message
                alert('Product added to cart!');
            } else {
                alert(data.message || 'Error adding product to cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding product to cart');
        });
    }
    </script>
</body>
</html> 