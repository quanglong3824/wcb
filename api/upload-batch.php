<?php
// Disable error output to prevent breaking JSON
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering
ob_start();

try {
    require_once '../includes/auth-check.php';
    require_once '../config/php/config.php';
    
    // Clear any output
    ob_clean();
    
    header('Content-Type: application/json');
    
    // Kiểm tra có files không
    if (!isset($_FILES['files']) || empty($_FILES['files']['name'])) {
        throw new Exception('Không có file được upload');
    }
    
    $files = $_FILES['files'];
    
    // Lấy thông tin từ form (phân tách bằng ;)
    $fileNames = isset($_POST['fileNames']) ? explode(';', $_POST['fileNames']) : [];
    $fileDescriptions = isset($_POST['fileDescriptions']) ? explode(';', $_POST['fileDescriptions']) : [];
    
    // Kết nối database
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Không thể kết nối database');
    }
    
    // Define upload path
    $uploadPath = dirname(__DIR__) . '/uploads/';
    
    // Create uploads directory if not exists
    if (!file_exists($uploadPath)) {
        if (!mkdir($uploadPath, 0755, true)) {
            throw new Exception('Không thể tạo thư mục uploads');
        }
    }
    
    $uploadedFiles = [];
    $errors = [];
    $totalFiles = count($files['name']);
    
    // Process each file
    for ($i = 0; $i < $totalFiles; $i++) {
        try {
            // Skip if error
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                $errors[] = [
                    'file' => $files['name'][$i],
                    'error' => 'Lỗi upload code: ' . $files['error'][$i]
                ];
                continue;
            }
            
            $fileName = $files['name'][$i];
            $fileType = $files['type'][$i];
            $fileTmpName = $files['tmp_name'][$i];
            $fileSize = $files['size'][$i];
            
            // Validate file size (50MB)
            $maxSize = 50 * 1024 * 1024;
            if ($fileSize > $maxSize) {
                $errors[] = [
                    'file' => $fileName,
                    'error' => 'File quá lớn (tối đa 50MB)'
                ];
                continue;
            }
            
            // Determine media type
            $mediaType = '';
            if (strpos($fileType, 'image/') === 0) {
                $mediaType = 'image';
            } elseif (strpos($fileType, 'video/') === 0) {
                $mediaType = 'video';
            } else {
                $errors[] = [
                    'file' => $fileName,
                    'error' => 'Định dạng không được hỗ trợ'
                ];
                continue;
            }
            
            // Generate unique filename
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $uniqueFileName = uniqid() . '_' . time() . '_' . $i . '.' . $extension;
            $fullUploadPath = $uploadPath . $uniqueFileName;
            $relativePath = 'uploads/' . $uniqueFileName;
            
            // Move uploaded file
            if (!move_uploaded_file($fileTmpName, $fullUploadPath)) {
                $errors[] = [
                    'file' => $fileName,
                    'error' => 'Lỗi khi lưu file'
                ];
                continue;
            }
            
            // Get image dimensions if image
            $width = null;
            $height = null;
            if ($mediaType === 'image') {
                $imageInfo = @getimagesize($fullUploadPath);
                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                }
            }
            
            // Get custom name and description
            $customName = isset($fileNames[$i]) && !empty(trim($fileNames[$i])) 
                ? trim($fileNames[$i]) 
                : pathinfo($fileName, PATHINFO_FILENAME);
            
            $customDescription = isset($fileDescriptions[$i]) && !empty(trim($fileDescriptions[$i])) 
                ? trim($fileDescriptions[$i]) 
                : null;
            
            // Check if user exists in database
            $userId = null;
            if (isset($_SESSION['user_id'])) {
                $checkUser = $conn->query("SELECT id FROM users WHERE id = " . intval($_SESSION['user_id']));
                if ($checkUser && $checkUser->num_rows > 0) {
                    $userId = $_SESSION['user_id'];
                }
            }
            
            // Save to database
            $stmt = $conn->prepare("INSERT INTO media (name, type, file_name, file_path, file_size, mime_type, width, height, description, status, uploaded_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, NOW())");
            
            $stmt->bind_param("ssssissssi", 
                $customName,
                $mediaType,
                $uniqueFileName,
                $relativePath,
                $fileSize,
                $fileType,
                $width,
                $height,
                $customDescription,
                $userId
            );
            
            if ($stmt->execute()) {
                $mediaId = $stmt->insert_id;
                
                $uploadedFiles[] = [
                    'id' => $mediaId,
                    'name' => $customName,
                    'original_name' => $fileName,
                    'type' => $mediaType,
                    'file_path' => $relativePath
                ];
            } else {
                @unlink($fullUploadPath);
                $errors[] = [
                    'file' => $fileName,
                    'error' => 'Lỗi database: ' . $stmt->error
                ];
            }
            
        } catch (Exception $e) {
            $errors[] = [
                'file' => isset($fileName) ? $fileName : 'Unknown',
                'error' => $e->getMessage()
            ];
        }
    }
    
    $conn->close();
    
    // Prepare response
    $response = [
        'success' => count($uploadedFiles) > 0,
        'total' => $totalFiles,
        'uploaded' => count($uploadedFiles),
        'failed' => count($errors),
        'files' => $uploadedFiles,
        'errors' => $errors
    ];
    
    if (count($uploadedFiles) > 0 && count($errors) === 0) {
        $response['message'] = "Upload thành công {$totalFiles} file";
    } elseif (count($uploadedFiles) > 0 && count($errors) > 0) {
        $response['message'] = "Upload thành công {$response['uploaded']}/{$totalFiles} file";
    } else {
        $response['message'] = "Upload thất bại";
    }
    
    ob_clean();
    echo json_encode($response);
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => $e->getMessage()
    ]);
}

ob_end_flush();
