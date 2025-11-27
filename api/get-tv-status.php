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

// Get TV status
$stmt = $conn->prepare("SELECT id, name, status FROM tvs WHERE id = ?");
$stmt->bind_param("i", $tvId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $tv = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'tv_id' => $tv['id'],
        'tv_name' => $tv['name'],
        'status' => $tv['status']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'TV not found'
    ]);
}

$conn->close();
