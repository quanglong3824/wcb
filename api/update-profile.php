<?php
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../profile.php');
    exit;
}

// Lấy dữ liệu từ form
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// Validate
if (empty($full_name)) {
    header('Location: ../profile.php?error=empty_name');
    exit;
}

// Validate email nếu có
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../profile.php?error=invalid_email');
    exit;
}

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    header('Location: ../profile.php?error=connection');
    exit;
}

// Cập nhật thông tin
$stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("ssi", $full_name, $email, $_SESSION['user_id']);

if ($stmt->execute()) {
    // Cập nhật session
    $_SESSION['full_name'] = $full_name;
    $_SESSION['user_email'] = $email;
    
    // Ghi log (bỏ qua lỗi nếu có)
    try {
        $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, 'update_profile', 'Updated profile information', ?)");
        $ip = $_SERVER['REMOTE_ADDR'];
        $logStmt->bind_param("is", $_SESSION['user_id'], $ip);
        $logStmt->execute();
    } catch (Exception $e) {
        error_log("Profile update logging error: " . $e->getMessage());
    }
    
    header('Location: ../profile.php?success=profile_updated');
} else {
    header('Location: ../profile.php?error=update_failed');
}

$stmt->close();
$conn->close();
exit;
