<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

try {
    // Fetch featured/latest products for home page (limit 4 for the slider)
    $stmt = $conn->prepare("
        SELECT id, name, description, price, image_url, category_id, stock, status, created_at
        FROM tbl_products 
        WHERE status = 'active' 
            AND stock > 0
        ORDER BY created_at DESC
        LIMIT 4
    ");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format products for frontend
    $formatted_products = [];
    foreach ($products as $product) {
        $formatted_products[] = [
            'id' => $product['id'],
            'name' => $product['name'] ?: 'Premium Product',
            'description' => $product['description'] ?: 'High-quality automotive product',
            'image' => $product['image_url'] ? $product['image_url'] : 'assets/images/default-product.jpg',
            'price' => number_format($product['price'], 0),
            'original_price' => null,
            'current_price' => number_format($product['price'], 0),
            'rating' => 4,
            'reviews' => 0,
            'badge' => ['text' => 'Available', 'color' => 'info'],
            'stock' => intval($product['stock'])
        ];
    }
    
    // If no products found, provide fallback static content
    if (empty($formatted_products)) {
        $formatted_products = [
            [
                'id' => 'fallback-1',
                'name' => 'Premium Car Battery',
                'description' => 'Long-lasting automotive battery with 3-year warranty',
                'image' => 'assets/images/default-product.jpg',
                'price' => '2,500',
                'original_price' => '3,000',
                'current_price' => '2,500',
                'rating' => 4,
                'reviews' => 24,
                'badge' => ['text' => 'New', 'color' => 'success'],
                'stock' => 10
            ],
            [
                'id' => 'fallback-2',
                'name' => 'Synthetic Engine Oil',
                'description' => 'High-performance 5W-30 synthetic motor oil',
                'image' => 'assets/images/default-product.jpg',
                'price' => '850',
                'original_price' => null,
                'current_price' => '850',
                'rating' => 5,
                'reviews' => 18,
                'badge' => ['text' => 'Popular', 'color' => 'primary'],
                'stock' => 25
            ],
            [
                'id' => 'fallback-3',
                'name' => 'Ceramic Brake Pads',
                'description' => 'Premium ceramic brake pads for superior stopping power',
                'image' => 'assets/images/default-product.jpg',
                'price' => '1,200',
                'original_price' => '1,500',
                'current_price' => '1,200',
                'rating' => 4,
                'reviews' => 15,
                'badge' => ['text' => 'Sale', 'color' => 'warning'],
                'stock' => 8
            ],
            [
                'id' => 'fallback-4',
                'name' => 'GPS Vehicle Tracker',
                'description' => 'Real-time GPS tracking device with mobile app',
                'image' => 'assets/images/default-product.jpg',
                'price' => '3,500',
                'original_price' => null,
                'current_price' => '3,500',
                'rating' => 5,
                'reviews' => 32,
                'badge' => ['text' => 'Featured', 'color' => 'info'],
                'stock' => 5
            ]
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $formatted_products,
        'count' => count($formatted_products)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'fallback' => [
            [
                'id' => 'error-fallback-1',
                'name' => 'Quality Products Available',
                'description' => 'Browse our premium automotive products.',
                'image' => 'assets/images/default-product.jpg',
                'price' => '0',
                'rating' => 0,
                'reviews' => 0,
                'badge' => ['text' => 'Available', 'color' => 'primary'],
                'stock' => 0
            ]
        ]
    ]);
}

function getBadgeInfo($product) {
    // Determine badge based on product data
    if (strtotime($product['created_at']) > strtotime('-7 days')) {
        return ['text' => 'New', 'color' => 'success'];
    }
    
    if ($product['sale_price'] && $product['sale_price'] < $product['price']) {
        return ['text' => 'Sale', 'color' => 'warning'];
    }
    
    if ($product['rating'] >= 4.5) {
        return ['text' => 'Popular', 'color' => 'primary'];
    }
    
    if ($product['stock_quantity'] <= 5) {
        return ['text' => 'Limited', 'color' => 'danger'];
    }
    
    return ['text' => 'Available', 'color' => 'info'];
} 