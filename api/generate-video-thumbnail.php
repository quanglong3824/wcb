<?php
/**
 * Generate Video Thumbnail API
 * Tạo thumbnail cho video sử dụng FFmpeg
 */
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Lấy dữ liệu
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$mediaId = isset($data['media_id']) ? intval($data['media_id']) : 0;

if ($mediaId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID media không hợp lệ']);
    exit;
}

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

// Lấy thông tin media
$stmt = $conn->prepare("SELECT id, name, type, file_path, thumbnail_path FROM media WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $mediaId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Media không tồn tại']);
    exit;
}

$media = $result->fetch_assoc();

// Chỉ xử lý video
if ($media['type'] !== 'video') {
    echo json_encode(['success' => false, 'message' => 'Chỉ hỗ trợ tạo thumbnail cho video']);
    exit;
}

// Đường dẫn video
$videoPath = ROOT_PATH . $media['file_path'];

if (!file_exists($videoPath)) {
    echo json_encode(['success' => false, 'message' => 'File video không tồn tại']);
    exit;
}

// Tạo thư mục thumbnails nếu chưa có
$thumbnailDir = ROOT_PATH . 'uploads/thumbnails/';
if (!file_exists($thumbnailDir)) {
    mkdir($thumbnailDir, 0755, true);
}

// Tên file thumbnail
$thumbnailFileName = 'thumb_' . pathinfo($media['file_path'], PATHINFO_FILENAME) . '.jpg';
$thumbnailPath = $thumbnailDir . $thumbnailFileName;
$thumbnailRelativePath = 'uploads/thumbnails/' . $thumbnailFileName;

// Kiểm tra FFmpeg có sẵn không
$ffmpegPath = 'ffmpeg'; // Hoặc đường dẫn đầy đủ như /usr/bin/ffmpeg

// Tạo thumbnail tại giây thứ 1 của video
$command = sprintf(
    '%s -i %s -ss 00:00:01 -vframes 1 -vf "scale=640:-1" -q:v 2 %s 2>&1',
    escapeshellcmd($ffmpegPath),
    escapeshellarg($videoPath),
    escapeshellarg($thumbnailPath)
);

exec($command, $output, $returnCode);

if ($returnCode !== 0 || !file_exists($thumbnailPath)) {
    // Thử phương pháp khác - lấy frame đầu tiên
    $command2 = sprintf(
        '%s -i %s -vframes 1 -vf "scale=640:-1" -q:v 2 %s 2>&1',
        escapeshellcmd($ffmpegPath),
        escapeshellarg($videoPath),
        escapeshellarg($thumbnailPath)
    );
    
    exec($command2, $output2, $returnCode2);
    
    if ($returnCode2 !== 0 || !file_exists($thumbnailPath)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Không thể tạo thumbnail. Vui lòng kiểm tra FFmpeg đã được cài đặt.',
            'debug' => implode("\n", array_merge($output, $output2 ?? []))
        ]);
        exit;
    }
}

// Cập nhật database
$updateStmt = $conn->prepare("UPDATE media SET thumbnail_path = ? WHERE id = ?");
$updateStmt->bind_param("si", $thumbnailRelativePath, $mediaId);

if ($updateStmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Đã tạo thumbnail thành công!',
        'thumbnail_path' => $thumbnailRelativePath
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi cập nhật database: ' . $conn->error
    ]);
}

$conn->close();
