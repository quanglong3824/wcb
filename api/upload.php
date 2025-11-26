<?php
session_start();
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

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
            'height' => $height
        ]
    ]);
} else {
    // Delete uploaded file if database insert fails
    unlink($uploadPath);
    echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu thông tin vào database: ' . $conn->error]);
}

$stmt->close();
$conn->close();
