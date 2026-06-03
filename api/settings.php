<?php
/**
 * Settings API
 * Get and update system settings
 */
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';
require_once '../includes/logger.php';

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
function getSettings($conn)
{
    // Auto-initialize missing settings (self-healing)
    $defaultSettings = [
        'simple_reload_enabled' => ['value' => '1', 'group' => 'auto_reload', 'description' => 'Bật tự động reload trang đơn giản (TV cũ)'],
        'simple_reload_interval' => ['value' => '102', 'group' => 'auto_reload', 'description' => 'Thời gian reload trang đơn giản (giây)']
    ];

    foreach ($defaultSettings as $key => $data) {
        $checkStmt = $conn->prepare("SELECT id FROM system_settings WHERE setting_key = ?");
        $checkStmt->bind_param("s", $key);
        $checkStmt->execute();
        $res = $checkStmt->get_result();
        if ($res->num_rows === 0) {
            $insertStmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_group, description, setting_type) VALUES (?, ?, ?, ?, 'string')");
            $insertStmt->bind_param("ssss", $key, $data['value'], $data['group'], $data['description']);
            $insertStmt->execute();
            $insertStmt->close();
        }
        $checkStmt->close();
    }

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
function updateSettings($conn)
{
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
                // Update existing - only update setting_value
                $updateStmt = $conn->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
                $updateStmt->bind_param("ss", $value, $key);
                $updateStmt->execute();
            } else {
                // Insert new - only required columns
                $insertStmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?)");
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
