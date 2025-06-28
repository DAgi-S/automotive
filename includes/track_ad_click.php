<?php
define('INCLUDED', true);
/**
 * Ad Click Tracking Handler
 * Handles AJAX requests to track advertisement clicks
 */

session_start();
require_once('../config/database.php');
require_once('ad_functions.php');

// Set content type to JSON
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate ad ID
if (!isset($input['ad_id']) || !is_numeric($input['ad_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid ad ID']);
    exit();
}

$adId = (int) $input['ad_id'];

// Track the click
trackAdClick($conn, $adId);
?> 