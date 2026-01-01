<?php
/**
 * Update Video Progress API
 * TV gửi thông tin video đang phát và thời gian hiện tại
 */
require_once '../config/php/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get parameters
$tvId = isset($_GET['tv_id']) ? intval($_GET['tv_id']) : 0;
$currentTime = isset($_GET['current_time']) ? floatval($_GET['current_time']) : 0;
$duration = isset($_GET['duration']) ? floatval($_GET['duration']) : 0;
$contentId = isset($_GET['content_id']) ? intval($_GET['content_id']) : 0;

if ($tvId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid TV ID']);
    exit;
}

$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Cannot connect to database']);
    exit;
}

// Update or insert video progress
$stmt = $conn->prepare("
    INSERT INTO tv_video_progress (tv_id, content_id, current_time, duration, updated_at)
    VALUES (?, ?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE
        content_id = VALUES(content_id),
        current_time = VALUES(current_time),
        duration = VALUES(duration),
        updated_at = NOW()
");

$stmt->bind_param("iidd", $tvId, $contentId, $currentTime, $duration);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Video progress updated'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
