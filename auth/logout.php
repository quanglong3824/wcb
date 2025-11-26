<?php
session_start();
require_once '../config/php/config.php';

// Ghi log trước khi logout (nếu có database và user tồn tại)
if (isset($_SESSION['user_id'])) {
    $conn = getDBConnection();
    if ($conn) {
        try {
            // Kiểm tra user có tồn tại không
            $checkStmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
            $checkStmt->bind_param("i", $_SESSION['user_id']);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                // User tồn tại, ghi log
                $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, 'logout', 'User logged out', ?)");
                $ip = $_SERVER['REMOTE_ADDR'];
                $stmt->bind_param("is", $_SESSION['user_id'], $ip);
                $stmt->execute();
                $stmt->close();
            }
            
            $checkStmt->close();
        } catch (Exception $e) {
            // Bỏ qua lỗi logging, vẫn cho phép logout
            error_log("Logout logging error: " . $e->getMessage());
        }
        
        $conn->close();
    }
}

// Xóa tất cả session
$_SESSION = array();

// Xóa session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Hủy session
session_destroy();

// Redirect về trang login
header('Location: login.php?logout=success');
exit;
