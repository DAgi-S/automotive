<?php
session_start();
require_once('../../config/database.php');
header('Content-Type: application/json');
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
$action = $_POST['action'] ?? $_GET['action'] ?? '';
try {
    if ($action === 'get') {
        // Fetch all branding, info, settings
        $branding = $conn->query('SELECT * FROM company_branding LIMIT 1')->fetch(PDO::FETCH_ASSOC);
        $info = $conn->query('SELECT * FROM company_information LIMIT 1')->fetch(PDO::FETCH_ASSOC);
        $settings = $conn->query('SELECT * FROM company_settings')->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'branding' => $branding, 'info' => $info, 'settings' => $settings]);
        exit;
    }
    if ($action === 'update_branding') {
        $id = intval($_POST['id'] ?? 1);
        $company_name = $_POST['company_name'] ?? '';
        $tagline = $_POST['tagline'] ?? '';
        $primary_color = $_POST['primary_color'] ?? '#4e73df';
        $secondary_color = $_POST['secondary_color'] ?? '#858796';
        $logo_url = $_POST['logo_url'] ?? '';
        $stmt = $conn->prepare('UPDATE company_branding SET company_name=?, tagline=?, primary_color=?, secondary_color=?, logo_url=?, updated_at=NOW() WHERE id=?');
        $stmt->execute([$company_name, $tagline, $primary_color, $secondary_color, $logo_url, $id]);
        echo json_encode(['success' => true]);
        exit;
    }
    if ($action === 'update_info') {
        $id = intval($_POST['id'] ?? 1);
        $address = $_POST['address'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $website = $_POST['website'] ?? '';
        $about = $_POST['about'] ?? '';
        $stmt = $conn->prepare('UPDATE company_information SET address=?, phone=?, email=?, website=?, about=?, updated_at=NOW() WHERE id=?');
        $stmt->execute([$address, $phone, $email, $website, $about, $id]);
        echo json_encode(['success' => true]);
        exit;
    }
    if ($action === 'update_setting') {
        $id = intval($_POST['id']);
        $value = $_POST['setting_value'] ?? '';
        $stmt = $conn->prepare('UPDATE company_settings SET setting_value=?, updated_at=NOW() WHERE id=?');
        $stmt->execute([$value, $id]);
        echo json_encode(['success' => true]);
        exit;
    }
    if ($action === 'add_setting') {
        $key = $_POST['setting_key'] ?? '';
        $value = $_POST['setting_value'] ?? '';
        $stmt = $conn->prepare('INSERT INTO company_settings (setting_key, setting_value, created_at, updated_at) VALUES (?, ?, NOW(), NOW())');
        $stmt->execute([$key, $value]);
        echo json_encode(['success' => true]);
        exit;
    }
    if ($action === 'delete_setting') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare('DELETE FROM company_settings WHERE id=?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    }
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 