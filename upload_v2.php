<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

$event_date = $_POST['event_date'] ?? '';
$event_title = $_POST['event_title'] ?? '';
$tv_ids = $_POST['tv_ids'] ?? [];
$department_ids = $_POST['department_ids'] ?? [];

// Validate
if (empty($event_date) || empty($event_title)) {
    header('Location: admin.php?error=missing_fields');
    exit;
}

// Phải chọn ít nhất một TV hoặc một department
if (empty($tv_ids) && empty($department_ids)) {
    header('Location: admin.php?error=no_selection');
    exit;
}

// Xử lý upload file
if (!isset($_FILES['welcome_image']) || $_FILES['welcome_image']['error'] !== UPLOAD_ERR_OK) {
    header('Location: admin.php?error=upload_failed');
    exit;
}

$file = $_FILES['welcome_image'];
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];

if (!in_array($file['type'], $allowed_types)) {
    header('Location: admin.php?error=invalid_type');
    exit;
}

// Tạo ID và filename
$board_id = 'WCB_' . date('Ymd_His') . '_' . substr(md5(uniqid()), 0, 6);
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = $board_id . '.' . $extension;
$upload_dir = 'uploads/';
$filepath = $upload_dir . $filename;

// Tạo thư mục nếu chưa có
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Upload file
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    header('Location: admin.php?error=move_failed');
    exit;
}

// Lấy kích thước ảnh
list($width, $height) = getimagesize($filepath);

// Lưu vào database
try {
    $conn = getDBConnection();
    
    // Insert board
    $stmt = $conn->prepare("INSERT INTO welcome_boards (id, event_date, event_title, filename, filepath, upload_time, width, height) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("sssssii", $board_id, $event_date, $event_title, $filename, $filepath, $width, $height);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert board");
    }
    
    // Collect all TV IDs (from individual selections + departments)
    $all_tv_ids = $tv_ids;
    
    // Add TVs from selected departments
    foreach ($department_ids as $dept_id) {
        $dept_tvs = getDepartmentTVs($dept_id);
        foreach ($dept_tvs as $tv) {
            if (!in_array($tv['id'], $all_tv_ids)) {
                $all_tv_ids[] = $tv['id'];
            }
        }
    }
    
    // Insert assignments cho các TV được chọn
    $stmt = $conn->prepare("INSERT INTO board_assignments (board_id, tv_id, status) VALUES (?, ?, 'active')");
    
    foreach ($all_tv_ids as $tv_id) {
        $stmt->bind_param("si", $board_id, $tv_id);
        $stmt->execute();
    }
    
    // Update trigger file
    file_put_contents('uploads/.trigger', time());
    
    header('Location: admin.php?success=1');
    exit;
    
} catch (Exception $e) {
    // Xóa file nếu lỗi
    if (file_exists($filepath)) {
        unlink($filepath);
    }
    header('Location: admin.php?error=db_error');
    exit;
}
?>
