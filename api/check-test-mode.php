<?php
/**
 * Check Test Mode API
 * Kiểm tra trạng thái test mode cho TV
 */
require_once '../config/php/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'test_mode' => false]);
    exit;
}

// Get test mode status
$stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'tv_test_mode'");
$stmt->execute();
$result = $stmt->get_result();

$testMode = false;
$timestamp = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $testMode = ($row['setting_value'] === '1');
}

// Get timestamp
$stmt2 = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'tv_test_mode_timestamp'");
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2->num_rows > 0) {
    $row2 = $result2->fetch_assoc();
    $timestamp = intval($row2['setting_value']);
}

echo json_encode([
    'success' => true,
    'test_mode' => $testMode,
    'timestamp' => $timestamp
]);

$conn->close();
