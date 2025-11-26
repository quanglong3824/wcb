<?php
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../profile.php');
    exit;
}

// Lấy dữ liệu từ form
$current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

// Validate
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    header('Location: ../profile.php?error=empty_fields');
    exit;
}

// Kiểm tra mật khẩu mới khớp
if ($new_password !== $confirm_password) {
    header('Location: ../profile.php?error=password_mismatch');
    exit;
}

// Kiểm tra độ dài mật khẩu
if (strlen($new_password) < 6) {
    header('Location: ../profile.php?error=password_too_short');
    exit;
}

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    header('Location: ../profile.php?error=connection');
    exit;
}

// Lấy mật khẩu hiện tại từ database
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header('Location: ../profile.php?error=user_not_found');
    exit;
}

// Verify mật khẩu hiện tại
if (!password_verify($current_password, $user['password'])) {
    header('Location: ../profile.php?error=wrong_password');
    exit;
}

// Hash mật khẩu mới
$new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

// Cập nhật mật khẩu
$updateStmt = $conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
$updateStmt->bind_param("si", $new_password_hash, $_SESSION['user_id']);

if ($updateStmt->execute()) {
    // Ghi log (bỏ qua lỗi nếu có)
    try {
        $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, 'change_password', 'Changed password', ?)");
        $ip = $_SERVER['REMOTE_ADDR'];
        $logStmt->bind_param("is", $_SESSION['user_id'], $ip);
        $logStmt->execute();
    } catch (Exception $e) {
        error_log("Password change logging error: " . $e->getMessage());
    }
    
    header('Location: ../profile.php?success=password_changed');
} else {
    header('Location: ../profile.php?error=update_failed');
}

$updateStmt->close();
$conn->close();
exit;
