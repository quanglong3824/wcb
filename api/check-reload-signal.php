<?php
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Get TV ID from query string
$tvId = isset($_GET['tv_id']) ? intval($_GET['tv_id']) : 0;

if ($tvId <= 0) {
    echo json_encode(['success' => false, 'timestamp' => null]);
    exit;
}

// Connect to database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'timestamp' => null]);
    exit;
}

// Get reload signal from system_settings
$settingKey = 'tv_reload_signal_' . $tvId;
$stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
$stmt->bind_param("s", $settingKey);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'timestamp' => $row['setting_value']
    ]);
} else {
    echo json_encode([
        'success' => true,
        'timestamp' => null
    ]);
}

$conn->close();
