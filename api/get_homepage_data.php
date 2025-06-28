<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

try {
    $homepage_data = [];
    
    // Fetch featured services (3 latest active services)
    $stmt = $conn->prepare("SELECT service_name, icon_class, description FROM tbl_services WHERE status = 'active' ORDER BY service_id ASC LIMIT 3");
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $homepage_data['featured_services'] = [];
    foreach ($services as $service) {
        $homepage_data['featured_services'][] = [
            'icon' => $service['icon_class'] ?: 'fa-wrench',
            'title' => $service['service_name'],
            'description' => $service['description']
        ];
    }
    
    // Fetch latest products (3 newest active products)
    $stmt = $conn->prepare("SELECT id, name, price, image_url FROM tbl_products WHERE status = 'active' ORDER BY created_at DESC LIMIT 3");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $homepage_data['latest_products'] = [];
    foreach ($products as $product) {
        $homepage_data['latest_products'][] = [
            'id' => $product['id'],
            'title' => $product['name'],
            'price' => number_format($product['price'], 2),
            'image' => $product['image_url'] ? 'uploads/products/' . $product['image_url'] : 'assets/img/products/brake-pads.jpg'
        ];
    }
    
    // Fetch clients (4 users with profile images)
    $stmt = $conn->prepare("SELECT name, new_img_name FROM tbl_user WHERE new_img_name IS NOT NULL ORDER BY created_at DESC LIMIT 4");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $homepage_data['clients'] = [];
    foreach ($clients as $client) {
        $homepage_data['clients'][] = [
            'name' => $client['name'],
            'image' => $client['new_img_name'] ? 'uploads/users/' . $client['new_img_name'] : 'assets/images/single-courses/client1.png'
        ];
    }
    
    // Fetch latest blogs (3 newest)
    $stmt = $conn->prepare("SELECT id, title, writer, date, s_article, image_url FROM tbl_blog ORDER BY date DESC LIMIT 3");
    $stmt->execute();
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $homepage_data['recent_blogs'] = [];
    foreach ($blogs as $blog) {
        $homepage_data['recent_blogs'][] = [
            'id' => $blog['id'],
            'title' => $blog['title'],
            'writer' => $blog['writer'],
            'date' => date('M d, Y', strtotime($blog['date'])),
            'excerpt' => $blog['s_article'],
            'image' => $blog['image_url'] ? 'uploads/blogs/' . $blog['image_url'] : 'assets/images/logo/logo.png',
            'url' => 'blog.php?blogid=' . $blog['id']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $homepage_data
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} 