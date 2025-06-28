<?php
/**
 * Notification Configuration API Endpoint
 * GET/POST /api/notifications/config.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get notification configuration
        $config_type = $_GET['type'] ?? 'all';
        
        $sql = "SELECT config_key, config_value, config_type, description FROM notification_config WHERE is_active = 1";
        $params = [];
        
        if ($config_type !== 'all') {
            $sql .= " AND config_type = ?";
            $params[] = $config_type;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Group by type
        $grouped_config = [];
        foreach ($configs as $config) {
            $grouped_config[$config['config_type']][$config['config_key']] = [
                'value' => $config['config_value'],
                'description' => $config['description']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'config' => $grouped_config
        ]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update notification configuration
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['config'])) {
            throw new Exception('Invalid configuration data');
        }
        
        $pdo->beginTransaction();
        
        try {
            foreach ($input['config'] as $key => $value) {
                $stmt = $pdo->prepare("
                    INSERT INTO notification_config (config_key, config_value, updated_at) 
                    VALUES (?, ?, NOW()) 
                    ON DUPLICATE KEY UPDATE 
                    config_value = VALUES(config_value), 
                    updated_at = NOW()
                ");
                $stmt->execute([$key, $value]);
            }
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Configuration updated successfully'
            ]);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 