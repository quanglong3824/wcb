<?php
// Disable error output to prevent breaking JSON
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering
ob_start();

require_once '../includes/auth-check.php';
require_once '../config/php/config.php';
require_once '../includes/logger.php';

// Clear any output
ob_clean();

header('Content-Type: application/json');

// Helper function to clean buffer and send JSON
function sendJSONResponse($data) {
    if (ob_get_level() > 0) {
        ob_clean();
    }
    echo json_encode($data);
    if (ob_get_level() > 0) {
        ob_end_flush();
    }
    exit;
}

// Kiểm tra file upload
if (!isset($_FILES['file'])) {
    sendJSONResponse(['success' => false, 'message' => 'Không có file được upload']);
}

$file = $_FILES['file'];
$fileName = isset($_POST['fileName']) ? trim($_POST['fileName']) : '';
$fileDescription = isset($_POST['fileDescription']) ? trim($_POST['fileDescription']) : '';

// Validate file error
if ($file['error'] !== UPLOAD_ERR_OK) {
    sendJSONResponse(['success' => false, 'message' => 'Lỗi upload file: ' . $file['error']]);
}


// Determine file type
$mimeType = $file['type'];
$fileType = '';

if (strpos($mimeType, 'image/') === 0) {
    $fileType = 'image';
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
} elseif (strpos($mimeType, 'video/') === 0) {
    $fileType = 'video';
    $allowedTypes = ['video/mp4', 'video/webm', 'video/avi', 'video/mov', 'video/quicktime'];
} else {
    sendJSONResponse(['success' => false, 'message' => 'Định dạng file không được hỗ trợ']);
}

// Validate file type
if (!in_array($mimeType, $allowedTypes)) {
    sendJSONResponse(['success' => false, 'message' => 'Định dạng file không được hỗ trợ: ' . $mimeType]);
}

// Generate unique filename
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$uniqueFileName = uniqid() . '_' . time() . '.' . $extension;
$uploadPath = UPLOAD_PATH . $uniqueFileName;
$relativePath = 'uploads/' . $uniqueFileName;

// Create uploads directory if not exists
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    sendJSONResponse(['success' => false, 'message' => 'Lỗi khi lưu file']);
}

// Get image dimensions if image
$width = null;
$height = null;
if ($fileType === 'image') {
    $imageInfo = getimagesize($uploadPath);
    if ($imageInfo) {
        $width = $imageInfo[0];
        $height = $imageInfo[1];
    }
}

// Use original filename if no custom name provided
if (empty($fileName)) {
    $fileName = pathinfo($file['name'], PATHINFO_FILENAME);
}

// Save to database
$conn = getDBConnection();

if (!$conn) {
    // Delete uploaded file if database connection fails
    unlink($uploadPath);
    sendJSONResponse(['success' => false, 'message' => 'Không thể kết nối database']);
}

$stmt = $conn->prepare("INSERT INTO media (name, type, file_name, file_path, file_size, mime_type, width, height, description, status, uploaded_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, NOW())");

$stmt->bind_param(
    "ssssissssi",
    $fileName,
    $fileType,
    $uniqueFileName,
    $relativePath,
    $file['size'],
    $mimeType,
    $width,
    $height,
    $fileDescription,
    $_SESSION['user_id']
);

if ($stmt->execute()) {
    $mediaId = $stmt->insert_id;

    // Tự động tạo thumbnail cho video nếu FFmpeg có sẵn
    $thumbnailPath = null;
    if ($fileType === 'video') {
        $thumbnailPath = generateVideoThumbnail($uploadPath, $uniqueFileName);
        if ($thumbnailPath) {
            // Cập nhật thumbnail_path trong database
            $updateThumb = $conn->prepare("UPDATE media SET thumbnail_path = ? WHERE id = ?");
            $updateThumb->bind_param("si", $thumbnailPath, $mediaId);
            $updateThumb->execute();
            $updateThumb->close();
        }
    }

    // Ghi log
    logActivity($conn, 'upload', 'media', $mediaId, "Upload file: " . $fileName);

    $stmt->close();
    $conn->close();

    sendJSONResponse([
        'success' => true,
        'message' => 'Upload thành công',
        'media' => [
            'id' => $mediaId,
            'name' => $fileName,
            'type' => $fileType,
            'file_name' => $uniqueFileName,
            'file_path' => $relativePath,
            'file_size' => $file['size'],
            'mime_type' => $mimeType,
            'width' => $width,
            'height' => $height,
            'thumbnail_path' => $thumbnailPath
        ]
    ]);
} else {
    // Delete uploaded file if database insert fails
    unlink($uploadPath);
    $stmt->close();
    $conn->close();
    sendJSONResponse(['success' => false, 'message' => 'Lỗi khi lưu thông tin vào database: ' . $conn->error]);
}

/**
 * Generate video thumbnail using FFmpeg
 */
function generateVideoThumbnail($videoPath, $videoFileName)
{
    // Tạo thư mục thumbnails nếu chưa có
    $thumbnailDir = UPLOAD_PATH . 'thumbnails/';
    if (!file_exists($thumbnailDir)) {
        mkdir($thumbnailDir, 0755, true);
    }

    // Tên file thumbnail
    $thumbnailFileName = 'thumb_' . pathinfo($videoFileName, PATHINFO_FILENAME) . '.jpg';
    $thumbnailFullPath = $thumbnailDir . $thumbnailFileName;
    $thumbnailRelativePath = 'uploads/thumbnails/' . $thumbnailFileName;

    // Kiểm tra FFmpeg
    $ffmpegPath = 'ffmpeg';

    // Tạo thumbnail tại giây thứ 1
    $command = sprintf(
        '%s -i %s -ss 00:00:01 -vframes 1 -vf "scale=640:-1" -q:v 2 %s 2>&1',
        escapeshellcmd($ffmpegPath),
        escapeshellarg($videoPath),
        escapeshellarg($thumbnailFullPath)
    );

    exec($command, $output, $returnCode);

    if ($returnCode !== 0 || !file_exists($thumbnailFullPath)) {
        // Thử lấy frame đầu tiên
        $command2 = sprintf(
            '%s -i %s -vframes 1 -vf "scale=640:-1" -q:v 2 %s 2>&1',
            escapeshellcmd($ffmpegPath),
            escapeshellarg($videoPath),
            escapeshellarg($thumbnailFullPath)
        );

        exec($command2, $output2, $returnCode2);

        if ($returnCode2 !== 0 || !file_exists($thumbnailFullPath)) {
            error_log("FFmpeg thumbnail generation failed");
            return null;
        }
    }

    return $thumbnailRelativePath;
}
