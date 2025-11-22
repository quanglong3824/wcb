<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

// Xử lý multi-upload (tối đa 5 WCB)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = getDBConnection();
        $uploaded_boards = [];
        $errors = [];
        
        // Kiểm tra có files được upload không
        if (!isset($_FILES['wcb_files']) || empty($_FILES['wcb_files']['name'][0])) {
            echo json_encode(['success' => false, 'message' => 'Không có file nào được upload'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $files = $_FILES['wcb_files'];
        $event_dates = $_POST['event_dates'] ?? [];
        $event_titles = $_POST['event_titles'] ?? [];
        
        // Validate số lượng files (max 5)
        $file_count = count($files['name']);
        if ($file_count > 5) {
            echo json_encode(['success' => false, 'message' => 'Chỉ được upload tối đa 5 WCB cùng lúc'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Upload từng file
        for ($i = 0; $i < $file_count; $i++) {
            // Skip nếu không có file
            if (empty($files['name'][$i])) continue;
            
            $file_name = $files['name'][$i];
            $file_tmp = $files['tmp_name'][$i];
            $file_error = $files['error'][$i];
            $file_size = $files['size'][$i];
            
            // Validate file
            if ($file_error !== UPLOAD_ERR_OK) {
                $errors[] = "File $file_name: Lỗi upload (code: $file_error)";
                continue;
            }
            
            // Kiểm tra file size (max 10MB)
            if ($file_size > 10 * 1024 * 1024) {
                $errors[] = "File $file_name: Kích thước quá lớn (max 10MB)";
                continue;
            }
            
            // Kiểm tra file type
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file_tmp);
            finfo_close($finfo);
            
            if (!in_array($mime_type, $allowed_types)) {
                $errors[] = "File $file_name: Định dạng không hợp lệ (chỉ chấp nhận JPG, PNG, GIF)";
                continue;
            }
            
            // Generate unique filename
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $unique_name = 'wcb_' . time() . '_' . $i . '_' . uniqid() . '.' . $extension;
            $upload_dir = __DIR__ . '/uploads/';
            
            // Tạo thư mục nếu chưa có
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $upload_path = $upload_dir . $unique_name;
            
            // Move file
            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Lưu vào database
                $event_date = $event_dates[$i] ?? date('Y-m-d');
                $event_title = $event_titles[$i] ?? "Welcome Board " . ($i + 1);
                $filepath = 'uploads/' . $unique_name;
                
                $stmt = $conn->prepare("INSERT INTO welcome_boards (event_date, event_title, filepath, created_at) 
                                       VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("sss", $event_date, $event_title, $filepath);
                
                if ($stmt->execute()) {
                    $board_id = $conn->insert_id;
                    $uploaded_boards[] = [
                        'id' => $board_id,
                        'event_date' => $event_date,
                        'event_title' => $event_title,
                        'filepath' => $filepath,
                        'filename' => $file_name
                    ];
                } else {
                    $errors[] = "File $file_name: Lỗi lưu database";
                    // Xóa file đã upload
                    unlink($upload_path);
                }
            } else {
                $errors[] = "File $file_name: Không thể di chuyển file";
            }
        }
        
        // Response
        if (count($uploaded_boards) > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Upload thành công ' . count($uploaded_boards) . ' WCB',
                'boards' => $uploaded_boards,
                'errors' => $errors
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không có file nào được upload thành công',
                'errors' => $errors
            ], JSON_UNESCAPED_UNICODE);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi server: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
}

ob_end_flush();
?>
