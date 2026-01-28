<?php
/**
 * API Quản lý Slideshow
 * Tạo, chỉnh sửa, xóa và lấy thông tin slideshow
 */

require_once '../config/php/config.php';
require_once '../includes/auth-check-api.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'create':
            if ($method !== 'POST') {
                throw new Exception('Method not allowed');
            }
            createSlideshow();
            break;

        case 'update':
            if ($method !== 'POST') {
                throw new Exception('Method not allowed');
            }
            updateSlideshow();
            break;

        case 'delete':
            if ($method !== 'POST') {
                throw new Exception('Method not allowed');
            }
            deleteSlideshow();
            break;

        case 'get':
            if ($method !== 'GET') {
                throw new Exception('Method not allowed');
            }
            getSlideshow();
            break;

        case 'list':
            if ($method !== 'GET') {
                throw new Exception('Method not allowed');
            }
            listSlideshows();
            break;

        case 'add-images':
            if ($method !== 'POST') {
                throw new Exception('Method not allowed');
            }
            addImagesToSlideshow();
            break;

        case 'remove-image':
            if ($method !== 'POST') {
                throw new Exception('Method not allowed');
            }
            removeImageFromSlideshow();
            break;

        case 'reorder-images':
            if ($method !== 'POST') {
                throw new Exception('Method not allowed');
            }
            reorderImages();
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Tạo slideshow mới
function createSlideshow()
{
    global $conn;

    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $transition_duration = intval($_POST['transition_duration'] ?? 5);
    $audio_id = !empty($_POST['audio_id']) ? intval($_POST['audio_id']) : null;
    $fade_out_duration = intval($_POST['fade_out_duration'] ?? 3);
    $image_ids = json_decode($_POST['image_ids'] ?? '[]', true);

    if (empty($name)) {
        throw new Exception('Tên slideshow không được để trống');
    }

    if (empty($image_ids) || !is_array($image_ids)) {
        throw new Exception('Vui lòng chọn ít nhất 1 ảnh');
    }

    // Tính tổng thời gian
    $total_duration = count($image_ids) * $transition_duration;

    // Bắt đầu transaction
    $conn->begin_transaction();

    try {
        // Tạo slideshow
        $stmt = $conn->prepare("
            INSERT INTO slideshows (name, description, transition_duration, total_duration, audio_id, fade_out_duration, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $user_id = $_SESSION['user_id'] ?? null;
        $stmt->bind_param('ssiiiis', $name, $description, $transition_duration, $total_duration, $audio_id, $fade_out_duration, $user_id);
        $stmt->execute();
        $slideshow_id = $conn->insert_id;
        $stmt->close();

        // Thêm images vào slideshow
        $stmt = $conn->prepare("
            INSERT INTO slideshow_images (slideshow_id, media_id, display_order) 
            VALUES (?, ?, ?)
        ");

        foreach ($image_ids as $index => $media_id) {
            $display_order = $index + 1;
            $stmt->bind_param('iii', $slideshow_id, $media_id, $display_order);
            $stmt->execute();
        }
        $stmt->close();

        // Commit transaction
        $conn->commit();

        // Log activity
        logActivity('create', 'slideshow', $slideshow_id, "Tạo slideshow: $name");

        echo json_encode([
            'success' => true,
            'message' => 'Tạo slideshow thành công',
            'slideshow_id' => $slideshow_id
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

// Cập nhật slideshow
function updateSlideshow()
{
    global $conn;

    $slideshow_id = intval($_POST['slideshow_id'] ?? 0);
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $transition_duration = intval($_POST['transition_duration'] ?? 5);
    $audio_id = !empty($_POST['audio_id']) ? intval($_POST['audio_id']) : null;
    $fade_out_duration = intval($_POST['fade_out_duration'] ?? 3);

    if ($slideshow_id <= 0) {
        throw new Exception('ID slideshow không hợp lệ');
    }

    if (empty($name)) {
        throw new Exception('Tên slideshow không được để trống');
    }

    // Tính lại tổng thời gian dựa trên số lượng ảnh hiện tại
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM slideshow_images WHERE slideshow_id = ?");
    $stmt->bind_param('i', $slideshow_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_duration = $row['count'] * $transition_duration;
    $stmt->close();

    // Cập nhật slideshow
    $stmt = $conn->prepare("
        UPDATE slideshows 
        SET name = ?, description = ?, transition_duration = ?, total_duration = ?, audio_id = ?, fade_out_duration = ?
        WHERE id = ?
    ");

    $stmt->bind_param('ssiiiii', $name, $description, $transition_duration, $total_duration, $audio_id, $fade_out_duration, $slideshow_id);
    $stmt->execute();
    $stmt->close();

    // Log activity
    logActivity('update', 'slideshow', $slideshow_id, "Cập nhật slideshow: $name");

    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật slideshow thành công'
    ]);
}

// Xóa slideshow
function deleteSlideshow()
{
    global $conn;

    $slideshow_id = intval($_POST['slideshow_id'] ?? 0);

    if ($slideshow_id <= 0) {
        throw new Exception('ID slideshow không hợp lệ');
    }

    // Lấy tên slideshow trước khi xóa
    $stmt = $conn->prepare("SELECT name FROM slideshows WHERE id = ?");
    $stmt->bind_param('i', $slideshow_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $name = $row['name'] ?? 'Unknown';
    $stmt->close();

    // Xóa slideshow (cascade sẽ tự động xóa slideshow_images)
    $stmt = $conn->prepare("DELETE FROM slideshows WHERE id = ?");
    $stmt->bind_param('i', $slideshow_id);
    $stmt->execute();
    $stmt->close();

    // Log activity
    logActivity('delete', 'slideshow', $slideshow_id, "Xóa slideshow: $name");

    echo json_encode([
        'success' => true,
        'message' => 'Xóa slideshow thành công'
    ]);
}

// Lấy thông tin slideshow
function getSlideshow()
{
    global $conn;

    $slideshow_id = intval($_GET['id'] ?? 0);

    if ($slideshow_id <= 0) {
        throw new Exception('ID slideshow không hợp lệ');
    }

    // Lấy thông tin slideshow
    $stmt = $conn->prepare("
        SELECT s.*, m.name as audio_name, m.file_path as audio_path, m.duration as audio_duration
        FROM slideshows s
        LEFT JOIN media m ON s.audio_id = m.id
        WHERE s.id = ?
    ");

    $stmt->bind_param('i', $slideshow_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $slideshow = $result->fetch_assoc();
    $stmt->close();

    if (!$slideshow) {
        throw new Exception('Slideshow không tồn tại');
    }

    // Lấy danh sách images
    $stmt = $conn->prepare("
        SELECT si.*, m.name, m.file_path, m.thumbnail_path, m.width, m.height
        FROM slideshow_images si
        JOIN media m ON si.media_id = m.id
        WHERE si.slideshow_id = ?
        ORDER BY si.display_order ASC
    ");

    $stmt->bind_param('i', $slideshow_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    $stmt->close();

    $slideshow['images'] = $images;

    echo json_encode([
        'success' => true,
        'slideshow' => $slideshow
    ]);
}

// Liệt kê tất cả slideshow
function listSlideshows()
{
    global $conn;

    $status = $_GET['status'] ?? 'all';

    $query = "
        SELECT s.*, 
               m.name as audio_name,
               (SELECT COUNT(*) FROM slideshow_images WHERE slideshow_id = s.id) as image_count,
               u.fullname as creator_name
        FROM slideshows s
        LEFT JOIN media m ON s.audio_id = m.id
        LEFT JOIN users u ON s.created_by = u.id
    ";

    if ($status !== 'all') {
        $query .= " WHERE s.status = ?";
    }

    $query .= " ORDER BY s.created_at DESC";

    $stmt = $conn->prepare($query);

    if ($status !== 'all') {
        $stmt->bind_param('s', $status);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $slideshows = [];
    while ($row = $result->fetch_assoc()) {
        $slideshows[] = $row;
    }
    $stmt->close();

    echo json_encode([
        'success' => true,
        'slideshows' => $slideshows
    ]);
}

// Thêm ảnh vào slideshow
function addImagesToSlideshow()
{
    global $conn;

    $slideshow_id = intval($_POST['slideshow_id'] ?? 0);
    $image_ids = json_decode($_POST['image_ids'] ?? '[]', true);

    if ($slideshow_id <= 0) {
        throw new Exception('ID slideshow không hợp lệ');
    }

    if (empty($image_ids) || !is_array($image_ids)) {
        throw new Exception('Vui lòng chọn ít nhất 1 ảnh');
    }

    // Lấy display_order hiện tại lớn nhất
    $stmt = $conn->prepare("SELECT MAX(display_order) as max_order FROM slideshow_images WHERE slideshow_id = ?");
    $stmt->bind_param('i', $slideshow_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $max_order = $row['max_order'] ?? 0;
    $stmt->close();

    // Thêm images
    $stmt = $conn->prepare("INSERT INTO slideshow_images (slideshow_id, media_id, display_order) VALUES (?, ?, ?)");

    foreach ($image_ids as $media_id) {
        $max_order++;
        $stmt->bind_param('iii', $slideshow_id, $media_id, $max_order);
        $stmt->execute();
    }
    $stmt->close();

    // Cập nhật lại total_duration
    updateSlideshowDuration($slideshow_id);

    echo json_encode([
        'success' => true,
        'message' => 'Thêm ảnh vào slideshow thành công'
    ]);
}

// Xóa ảnh khỏi slideshow
function removeImageFromSlideshow()
{
    global $conn;

    $slideshow_id = intval($_POST['slideshow_id'] ?? 0);
    $slideshow_image_id = intval($_POST['slideshow_image_id'] ?? 0);

    if ($slideshow_id <= 0 || $slideshow_image_id <= 0) {
        throw new Exception('Tham số không hợp lệ');
    }

    // Xóa ảnh
    $stmt = $conn->prepare("DELETE FROM slideshow_images WHERE id = ? AND slideshow_id = ?");
    $stmt->bind_param('ii', $slideshow_image_id, $slideshow_id);
    $stmt->execute();
    $stmt->close();

    // Cập nhật lại display_order
    reorderImagesInternal($slideshow_id);

    // Cập nhật lại total_duration
    updateSlideshowDuration($slideshow_id);

    echo json_encode([
        'success' => true,
        'message' => 'Xóa ảnh khỏi slideshow thành công'
    ]);
}

// Sắp xếp lại thứ tự ảnh
function reorderImages()
{
    global $conn;

    $slideshow_id = intval($_POST['slideshow_id'] ?? 0);
    $image_orders = json_decode($_POST['image_orders'] ?? '[]', true);

    if ($slideshow_id <= 0) {
        throw new Exception('ID slideshow không hợp lệ');
    }

    if (empty($image_orders) || !is_array($image_orders)) {
        throw new Exception('Dữ liệu sắp xếp không hợp lệ');
    }

    // Cập nhật display_order
    $stmt = $conn->prepare("UPDATE slideshow_images SET display_order = ? WHERE id = ? AND slideshow_id = ?");

    foreach ($image_orders as $item) {
        $id = intval($item['id']);
        $order = intval($item['order']);
        $stmt->bind_param('iii', $order, $id, $slideshow_id);
        $stmt->execute();
    }
    $stmt->close();

    echo json_encode([
        'success' => true,
        'message' => 'Sắp xếp lại thứ tự ảnh thành công'
    ]);
}

// Helper function: Cập nhật lại display_order sau khi xóa
function reorderImagesInternal($slideshow_id)
{
    global $conn;

    $stmt = $conn->prepare("
        SELECT id FROM slideshow_images 
        WHERE slideshow_id = ? 
        ORDER BY display_order ASC
    ");
    $stmt->bind_param('i', $slideshow_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $ids = [];
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row['id'];
    }
    $stmt->close();

    // Cập nhật lại display_order
    $stmt = $conn->prepare("UPDATE slideshow_images SET display_order = ? WHERE id = ?");
    foreach ($ids as $index => $id) {
        $order = $index + 1;
        $stmt->bind_param('ii', $order, $id);
        $stmt->execute();
    }
    $stmt->close();
}

// Helper function: Cập nhật total_duration của slideshow
function updateSlideshowDuration($slideshow_id)
{
    global $conn;

    $stmt = $conn->prepare("
        SELECT s.transition_duration, COUNT(si.id) as image_count
        FROM slideshows s
        LEFT JOIN slideshow_images si ON s.id = si.slideshow_id
        WHERE s.id = ?
        GROUP BY s.id
    ");

    $stmt->bind_param('i', $slideshow_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row) {
        $total_duration = $row['transition_duration'] * $row['image_count'];

        $stmt = $conn->prepare("UPDATE slideshows SET total_duration = ? WHERE id = ?");
        $stmt->bind_param('ii', $total_duration, $slideshow_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Helper function: Log activity
function logActivity($action, $entity_type, $entity_id, $description)
{
    global $conn;

    $user_id = $_SESSION['user_id'] ?? null;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;

    $stmt = $conn->prepare("
        INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param('ississ', $user_id, $action, $entity_type, $entity_id, $description, $ip_address);
    $stmt->execute();
    $stmt->close();
}
