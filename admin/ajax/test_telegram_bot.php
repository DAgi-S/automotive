<?php
session_start();
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['bot_token']) || !isset($data['chat_id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing bot token or chat ID']);
    exit();
}

$botToken = trim($data['bot_token']);
$chatId = trim($data['chat_id']);

if (empty($botToken) || empty($chatId)) {
    echo json_encode(['success' => false, 'error' => 'Bot token and chat ID cannot be empty']);
    exit();
}

// Test message
$message = "🤖 *Telegram Bot Test*\n\n";
$message .= "✅ Your Nati Automotive bot is working correctly!\n";
$message .= "📅 Test performed: " . date('Y-m-d H:i:s') . "\n";
$message .= "👤 Tested by: Admin Panel\n\n";
$message .= "You will now receive notifications from your automotive service system.";

// Telegram API URL
$telegramUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";

// Prepare data for Telegram API
$postData = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'Markdown',
    'disable_web_page_preview' => true
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $telegramUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Check for cURL errors
if ($curlError) {
    echo json_encode([
        'success' => false, 
        'error' => 'cURL Error: ' . $curlError
    ]);
    exit();
}

// Decode Telegram response
$telegramResponse = json_decode($response, true);

// Check if the request was successful
if ($httpCode === 200 && $telegramResponse && $telegramResponse['ok'] === true) {
    echo json_encode([
        'success' => true,
        'message' => 'Test message sent successfully!',
        'telegram_response' => $telegramResponse
    ]);
} else {
    // Extract error message from Telegram response
    $errorMsg = 'Unknown error';
    if ($telegramResponse && isset($telegramResponse['description'])) {
        $errorMsg = $telegramResponse['description'];
    } elseif ($httpCode !== 200) {
        $errorMsg = "HTTP Error: {$httpCode}";
    }
    
    echo json_encode([
        'success' => false,
        'error' => $errorMsg,
        'http_code' => $httpCode,
        'response' => $response
    ]);
}
?> 