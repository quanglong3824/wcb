<?php
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Get TV ID from query string
$tvId = isset($_GET['tv_id']) ? intval($_GET['tv_id']) : 0;

if ($tvId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid TV ID']);
    exit;
}

// Connect to database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Cannot connect to database']);
    exit;
}

// Get TV info
$tvStmt = $conn->prepare("SELECT id, name, location, default_content_id FROM tvs WHERE id = ?");
$tvStmt->bind_param("i", $tvId);
$tvStmt->execute();
$tvResult = $tvStmt->get_result();

if ($tvResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'TV not found']);
    exit;
}

$tv = $tvResult->fetch_assoc();

// Get default content
$contentId = $tv['default_content_id'];

if ($contentId) {
    $contentStmt = $conn->prepare("SELECT id, name, type, file_path FROM media WHERE id = ? AND status = 'active'");
    $contentStmt->bind_param("i", $contentId);
    $contentStmt->execute();
    $contentResult = $contentStmt->get_result();
    
    if ($contentResult->num_rows > 0) {
        $content = $contentResult->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'tv' => $tv,
            'content' => $content
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No content found',
            'tv' => $tv
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No default content set',
        'tv' => $tv
    ]);
}

$conn->close();
