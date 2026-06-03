// Disable error output to prevent breaking JSON
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering
ob_start();

require_once '../config/php/config.php';
require_once '../includes/auth-check-api.php';

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

// Cấu hình upload
$upload_dir = '../uploads/';
$allowed_audio_types = ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/m4a', 'audio/aac'];

set_time_limit(300);
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!isset($_FILES['audio'])) {
        throw new Exception('Không có file audio được upload');
    }

    $file = $_FILES['audio'];
    $audio_name = $_POST['name'] ?? pathinfo($file['name'], PATHINFO_FILENAME);
    $description = $_POST['description'] ?? '';

    // Kiểm tra lỗi upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Lỗi khi upload file: ' . $file['error']);
    }



    // Kiểm tra loại file
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_audio_types)) {
        throw new Exception('Chỉ chấp nhận file audio: MP3, WAV, OGG, M4A, AAC');
    }

    // Tạo tên file unique
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $unique_filename = uniqid('audio_' . time() . '_') . '.' . $file_extension;
    $file_path = $upload_dir . $unique_filename;

    // Di chuyển file
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception('Không thể lưu file');
    }

    // Lấy thông tin thời lượng audio (nếu có extension getID3)
    $duration = null;
    if (class_exists('getID3')) {
        $getID3 = new getID3;
        $file_info = $getID3->analyze($file_path);
        if (isset($file_info['playtime_seconds'])) {
            $duration = intval($file_info['playtime_seconds']);
        }
    }

    // Lưu thông tin vào database
    $stmt = $conn->prepare("
        INSERT INTO media (name, type, file_name, file_path, file_size, mime_type, duration, description, uploaded_by) 
        VALUES (?, 'audio', ?, ?, ?, ?, ?, ?, ?)
    ");

    $relative_path = 'uploads/' . $unique_filename;
    $user_id = $_SESSION['user_id'] ?? null;

    $stmt->bind_param(
        'sssisisi',
        $audio_name,
        $unique_filename,
        $relative_path,
        $file['size'],
        $mime_type,
        $duration,
        $description,
        $user_id
    );

    $stmt->execute();
    $media_id = $conn->insert_id();
    $stmt->close();

    // Log activity
    $stmt = $conn->prepare("
        INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) 
        VALUES (?, 'upload', 'media', ?, ?, ?)
    ");

    $log_desc = "Upload audio: $audio_name";
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;

    $stmt->bind_param('iiss', $user_id, $media_id, $log_desc, $ip_address);
    $stmt->execute();
    $stmt->close();

    sendJSONResponse([
        'success' => true,
        'message' => 'Upload audio thành công',
        'media' => [
            'id' => $media_id,
            'name' => $audio_name,
            'file_path' => $relative_path,
            'file_size' => $file['size'],
            'mime_type' => $mime_type,
            'duration' => $duration
        ]
    ]);

} catch (Exception $e) {
    // Xóa file nếu đã upload nhưng có lỗi
    if (isset($file_path) && file_exists($file_path)) {
        @unlink($file_path);
    }

    http_response_code(400);
    sendJSONResponse([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
