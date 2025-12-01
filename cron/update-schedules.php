<?php
/**
 * Schedule Engine Cron Job
 * Run this every minute via cron:
 * * * * * * php /path/to/wcb/cron/update-schedules.php
 */

// Prevent web access
if (php_sapi_name() !== 'cli' && !defined('CRON_ALLOWED')) {
    die('This script can only be run from command line');
}

require_once dirname(__DIR__) . '/config/php/config.php';

$conn = getDBConnection();

if (!$conn) {
    error_log("Schedule Cron: Cannot connect to database");
    exit(1);
}

$now = date('Y-m-d H:i:s');
$today = date('Y-m-d');
$currentTime = date('H:i:s');

echo "[{$now}] Running schedule update...\n";

// 1. Activate pending schedules that should start now
$activateQuery = "UPDATE schedules 
                  SET status = 'active', updated_at = NOW() 
                  WHERE status = 'pending' 
                  AND schedule_date = ? 
                  AND start_time <= ? 
                  AND end_time > ?";
$stmt = $conn->prepare($activateQuery);
$stmt->bind_param("sss", $today, $currentTime, $currentTime);
$stmt->execute();
$activatedCount = $stmt->affected_rows;
echo "Activated {$activatedCount} schedules\n";

// 2. Complete schedules that have ended
$completeQuery = "UPDATE schedules 
                  SET status = 'completed', updated_at = NOW() 
                  WHERE status = 'active' 
                  AND (schedule_date < ? OR (schedule_date = ? AND end_time <= ?))";
$stmt = $conn->prepare($completeQuery);
$stmt->bind_param("sss", $today, $today, $currentTime);
$stmt->execute();
$completedCount = $stmt->affected_rows;
echo "Completed {$completedCount} schedules\n";

// 3. Handle repeat schedules - create next occurrence
$repeatQuery = "SELECT * FROM schedules 
                WHERE status = 'completed' 
                AND repeat_type != 'none' 
                AND schedule_date = ?";
$stmt = $conn->prepare($repeatQuery);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

$createdCount = 0;
while ($schedule = $result->fetch_assoc()) {
    $nextDate = null;
    
    switch ($schedule['repeat_type']) {
        case 'daily':
            $nextDate = date('Y-m-d', strtotime($schedule['schedule_date'] . ' +1 day'));
            break;
        case 'weekly':
            $nextDate = date('Y-m-d', strtotime($schedule['schedule_date'] . ' +1 week'));
            break;
        case 'monthly':
            $nextDate = date('Y-m-d', strtotime($schedule['schedule_date'] . ' +1 month'));
            break;
    }
    
    if ($nextDate) {
        // Check if next schedule already exists
        $checkStmt = $conn->prepare("SELECT id FROM schedules WHERE tv_id = ? AND media_id = ? AND schedule_date = ? AND start_time = ?");
        $checkStmt->bind_param("iiss", $schedule['tv_id'], $schedule['media_id'], $nextDate, $schedule['start_time']);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows === 0) {
            // Create next occurrence
            $insertStmt = $conn->prepare("INSERT INTO schedules (tv_id, media_id, schedule_date, start_time, end_time, repeat_type, priority, status, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW())");
            $insertStmt->bind_param("iissssii", 
                $schedule['tv_id'], 
                $schedule['media_id'], 
                $nextDate, 
                $schedule['start_time'], 
                $schedule['end_time'], 
                $schedule['repeat_type'], 
                $schedule['priority'],
                $schedule['created_by']
            );
            $insertStmt->execute();
            $createdCount++;
        }
    }
}
echo "Created {$createdCount} repeat schedules\n";

// 4. Update TV current content based on active schedules
$updateTVQuery = "UPDATE tvs t
                  SET t.current_content_id = (
                      SELECT s.media_id 
                      FROM schedules s 
                      WHERE s.tv_id = t.id 
                      AND s.status = 'active' 
                      AND s.schedule_date = ? 
                      AND ? BETWEEN s.start_time AND s.end_time
                      ORDER BY s.priority DESC 
                      LIMIT 1
                  )
                  WHERE EXISTS (
                      SELECT 1 FROM schedules s 
                      WHERE s.tv_id = t.id 
                      AND s.status = 'active' 
                      AND s.schedule_date = ? 
                      AND ? BETWEEN s.start_time AND s.end_time
                  )";
$stmt = $conn->prepare($updateTVQuery);
$stmt->bind_param("ssss", $today, $currentTime, $today, $currentTime);
$stmt->execute();
$updatedTVCount = $stmt->affected_rows;
echo "Updated {$updatedTVCount} TV content\n";

// 5. Mark offline TVs (no heartbeat for 5 minutes)
$offlineQuery = "UPDATE tvs 
                 SET status = 'offline' 
                 WHERE status = 'online' 
                 AND (last_heartbeat IS NULL OR TIMESTAMPDIFF(SECOND, last_heartbeat, NOW()) > 300)";
$conn->query($offlineQuery);
$offlineCount = $conn->affected_rows;
if ($offlineCount > 0) {
    echo "Marked {$offlineCount} TVs as offline\n";
    
    // Create notification for offline TVs
    $offlineTVs = $conn->query("SELECT id, name FROM tvs WHERE status = 'offline' AND TIMESTAMPDIFF(SECOND, last_heartbeat, NOW()) BETWEEN 300 AND 360");
    while ($tv = $offlineTVs->fetch_assoc()) {
        $notifyStmt = $conn->prepare("INSERT INTO notifications (user_id, type, title, message, link, created_at) VALUES (NULL, 'tv_offline', 'TV Offline', ?, '/view.php', NOW())");
        $message = "TV {$tv['name']} đã mất kết nối";
        $notifyStmt->bind_param("s", $message);
        $notifyStmt->execute();
    }
}

// 6. Clean up old completed schedules (older than 30 days)
$cleanupQuery = "DELETE FROM schedules 
                 WHERE status = 'completed' 
                 AND repeat_type = 'none' 
                 AND schedule_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
$conn->query($cleanupQuery);
$cleanedCount = $conn->affected_rows;
if ($cleanedCount > 0) {
    echo "Cleaned up {$cleanedCount} old schedules\n";
}

// 7. Clean up old activity logs (older than 90 days)
$logCleanupQuery = "DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)";
$conn->query($logCleanupQuery);
$logCleanedCount = $conn->affected_rows;
if ($logCleanedCount > 0) {
    echo "Cleaned up {$logCleanedCount} old activity logs\n";
}

$conn->close();

echo "[" . date('Y-m-d H:i:s') . "] Schedule update completed.\n";
