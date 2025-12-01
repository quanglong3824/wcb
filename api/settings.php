<?php
/**
 * Settings API
 * Get and update system settings
 */
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

header('Content-Type: application/json');

$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getSettings($conn);
        break;
    case 'POST':
    case 'PUT':
        updateSettings($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

$conn->close();

/**
 * Get all settings
 */
function getSettings($conn) {
    $group = isset($_GET['group']) ? trim($_GET['group']) : '';
    
    $query = "SELECT setting_key, setting_value, setting_group, description FROM system_settings";
    if (!empty($group)) {
        $query .= " WHERE setting_group = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $group);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($query);
    }
    
    $settings = [];
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = [
            'value' => $row['setting_value'],
            'group' => $row['setting_group'],
            'description' => $row['description']
        ];
    }
    
    // Get grouped settings
    $groupedSettings = [];
    foreach ($settings as $key => $data) {
        $group = $data['group'] ?: 'general';
        if (!isset($groupedSettings[$group])) {
            $groupedSettings[$group] = [];
        }
        $groupedSettings[$group][$key] = $data['value'];
    }
    
    echo json_encode([
        'success' => true,
        'settings' => $settings,
        'grouped' => $groupedSettings
    ]);
}

/**
 * Update settings
 */
function updateSettings($conn) {
    // Chỉ super_admin mới được cập nhật settings
    if ($_SESSION['user_role'] !== 'super_admin') {
        echo json_encode(['success' => false, 'message' => 'Không có quyền cập nhật settings']);
        return;
    }
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (empty($data) || !is_array($data)) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        return;
    }
    
    $conn->begin_transaction();
    
    try {
        $updatedCount = 0;
        
        foreach ($data as $key => $value) {
            // Check if setting exists
            $checkStmt = $conn->prepare("SELECT setting_key FROM system_settings WHERE setting_key = ?");
            $checkStmt->bind_param("s", $key);
            $checkStmt->execute();
            
            if ($checkStmt->get_result()->num_rows > 0) {
                // Update existing
                $updateStmt = $conn->prepare("UPDATE system_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
                $updateStmt->bind_param("ss", $value, $key);
                $updateStmt->execute();
            } else {
                // Insert new
                $insertStmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
                $insertStmt->bind_param("ss", $key, $value);
                $insertStmt->execute();
            }
            
            $updatedCount++;
        }
        
        $conn->commit();
        
        // Log activity
        logActivity($conn, 'update_settings', 'setting', 0, "Cập nhật {$updatedCount} settings");
        
        echo json_encode([
            'success' => true,
            'message' => "Cập nhật {$updatedCount} settings thành công"
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
}

/**
 * Log activity
 */
function logActivity($conn, $action, $entityType, $entityId, $description) {
    try {
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt->bind_param("ississ", $_SESSION['user_id'], $action, $entityType, $entityId, $description, $ip);
        $stmt->execute();
    } catch (Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
}
