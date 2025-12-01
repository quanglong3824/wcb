<?php
/**
 * TV Heartbeat API
 * Nhận tín hiệu sống từ TV và cập nhật trạng thái
 */
require_once '../config/php/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get TV ID from request
$tvId = 0;
$tvFolder = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $tvId = isset($data['tv_id']) ? intval($data['tv_id']) : 0;
    $tvFolder = isset($data['folder']) ? trim($data['folder']) : '';
} else {
    $tvId = isset($_GET['tv_id']) ? intval($_GET['tv_id']) : 0;
    $tvFolder = isset($_GET['folder']) ? trim($_GET['folder']) : '';
}

// If folder is provided, find TV by folder
if (empty($tvId) && !empty($tvFolder)) {
    $stmt = $conn->prepare("SELECT id FROM tvs WHERE folder = ?");
    $stmt->bind_param("s", $tvFolder);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $tvId = $result->fetch_assoc()['id'];
    }
}

if ($tvId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid TV ID']);
    exit;
}

// Update heartbeat
$stmt = $conn->prepare("UPDATE tvs SET last_heartbeat = NOW(), status = 'online' WHERE id = ?");
$stmt->bind_param("i", $tvId);

if ($stmt->execute()) {
    // Get TV info
    $infoStmt = $conn->prepare("SELECT id, name, folder, status, current_content_id FROM tvs WHERE id = ?");
    $infoStmt->bind_param("i", $tvId);
    $infoStmt->execute();
    $tv = $infoStmt->get_result()->fetch_assoc();
    
    // Check if there's a reload signal
    $reloadSignal = false;
    $signalStmt = $conn->prepare("SELECT id FROM tv_reload_signals WHERE tv_id = ? AND processed = 0 ORDER BY created_at DESC LIMIT 1");
    $signalStmt->bind_param("i", $tvId);
    $signalStmt->execute();
    $signalResult = $signalStmt->get_result();
    
    if ($signalResult->num_rows > 0) {
        $signal = $signalResult->fetch_assoc();
        $reloadSignal = true;
        
        // Mark signal as processed
        $updateSignal = $conn->prepare("UPDATE tv_reload_signals SET processed = 1, processed_at = NOW() WHERE id = ?");
        $updateSignal->bind_param("i", $signal['id']);
        $updateSignal->execute();
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Heartbeat received',
        'tv' => $tv,
        'reload' => $reloadSignal,
        'server_time' => date('Y-m-d H:i:s')
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update heartbeat']);
}

$conn->close();
