<?php
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra file upload
if (!isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'message' => 'Không có file được upload']);
    exit;
}

$file = $_FILES['file'];
$fileName = isset($_POST['fileName']) ? trim($_POST['fileName']) : '';
$fileDescription = isset($_POST['fileDescription']) ? trim($_POST['fileDescription']) : '';

// Validate file error
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Lỗi upload file: ' . $file['error']]);
    exit;
}

// Validate file size (50MB)
$maxSize = 50 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File quá lớn (tối đa 50MB)']);
    exit;
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
    echo json_encode(['success' => false, 'message' => 'Định dạng file không được hỗ trợ']);
    exit;
}

// Validate file type
if (!in_array($mimeType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Định dạng file không được hỗ trợ: ' . $mimeType]);
    exit;
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
    echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu file']);
    exit;
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
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO media (name, type, file_name, file_path, file_size, mime_type, width, height, description, status, uploaded_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, NOW())");

$stmt->bind_param("ssssissssi", 
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
        }
    }
    
    // Ghi log
    try {
        $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'upload', 'media', ?, ?, ?)");
        $logDesc = "Upload file: " . $fileName;
        $ip = $_SERVER['REMOTE_ADDR'];
        $logStmt->bind_param("iiss", $_SESSION['user_id'], $mediaId, $logDesc, $ip);
        $logStmt->execute();
    } catch (Exception $e) {
        error_log("Upload logging error: " . $e->getMessage());
    }
    
    echo json_encode([
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
    echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu thông tin vào database: ' . $conn->error]);
}

$stmt->close();
$conn->close();

/**
 * Generate video thumbnail using FFmpeg
 * @param string $videoPath Full path to video file
 * @param string $videoFileName Video filename
 * @return string|null Relative path to thumbnail or null if failed
 */
function generateVideoThumbnail($videoPath, $videoFileName) {
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
            error_log("FFmpeg thumbnail generation failed: " . implode("\n", array_merge($output, $output2 ?? [])));
            return null;
        }
    }
    
    return $thumbnailRelativePath;
}
