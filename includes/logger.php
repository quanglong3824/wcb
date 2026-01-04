<?php
/**
 * System Logger
 * Centralized logging functionality
 */

/**
 * Log an activity
 * 
 * @param mysqli $conn Database connection
 * @param string $action Action code (e.g. 'login', 'create', 'delete')
 * @param string $entityType Type of entity (e.g. 'user', 'tv', 'media')
 * @param int|string $entityId ID of the entity
 * @param string $description Human readable description
 * @param int|null $userId User performing the action (defaults to session user)
 * @return bool Success status
 */
function logActivity($conn, $action, $entityType, $entityId, $description, $userId = null)
{
    // If no user specified, try to get from session
    if ($userId === null) {
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        } else {
            // System action or unauthenticated
            $userId = 0; // Assuming 0 or NULL for system
        }
    }

    // Normalize entity_id -> ensure it's not null if DB requires it
    // Check DB schema from backup: `entity_id` is varchar? or int?
    // Backup says: VALUES("858","14","login",NULL,NULL,"User logged in"...)
    // So entity_type and entity_id can be NULL.

    try {
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // Handling NULLs for bind_param is tricky, better to use variables
        // If entityType is null, we pass null.

        $stmt->bind_param("ississ", $userId, $action, $entityType, $entityId, $description, $ip);

        return $stmt->execute();
    } catch (Exception $e) {
        // Silently fail or log to file
        error_log("Activity log error: " . $e->getMessage());
        return false;
    }
}
