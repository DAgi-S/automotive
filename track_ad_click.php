<?php
define('INCLUDED', true);
require_once 'includes/config.php';
require_once 'includes/ad_functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$adId = (int)$_GET['id'];

try {
    // Get ad details
    $stmt = $conn->prepare("SELECT target_url FROM tbl_ads WHERE id = ?");
    $stmt->execute([$adId]);
    $ad = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ad || !$ad['target_url']) {
        header('Location: index.php');
        exit();
    }

    // Increment click count
    incrementAdClicks($adId);

    // Redirect to target URL
    header('Location: ' . $ad['target_url']);
    exit();

} catch(PDOException $e) {
    error_log("Error tracking ad click: " . $e->getMessage());
    header('Location: index.php');
    exit();
} 