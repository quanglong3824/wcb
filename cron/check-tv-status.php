<?php
/**
 * TV Status Check Cron Job
 * Run this every 5 minutes via cron:
 * */5 * * * * php /path/to/wcb/cron/check-tv-status.php
 */

// Prevent web access
if (php_sapi_name() !== 'cli' && !defined('CRON_ALLOWED')) {
    die('This script can only be run from command line');
}

require_once dirname(__DIR__) . '/config/php/config.php';

$conn = getDBConnection();

if (!$conn) {
    error_log("TV Status Cron: Cannot connect to database");
    exit(1);
}

$now = date('Y-m-d H:i:s');
echo "[{$now}] Checking TV status...\n";

// Get all TVs
$result = $conn->query("SELECT id, name, location, status, last_heartbeat, ip_address FROM tvs");

$onlineCount = 0;
$offlineCount = 0;
$newlyOffline = [];

while ($tv = $result->fetch_assoc()) {
    $isOnline = false;
    
    // Check heartbeat (within 5 minutes)
    if ($tv['last_heartbeat']) {
        $lastHeartbeat = strtotime($tv['last_heartbeat']);
        $isOnline = (time() - $lastHeartbeat) <= 300;
    }
    
    // Optional: Ping IP address if available
    if (!$isOnline && !empty($tv['ip_address'])) {
        $isOnline = pingHost($tv['ip_address']);
    }
    
    // Update status
    $newStatus = $isOnline ? 'online' : 'offline';
    
    if ($tv['status'] !== $newStatus) {
        $updateStmt = $conn->prepare("UPDATE tvs SET status = ? WHERE id = ?");
        $updateStmt->bind_param("si", $newStatus, $tv['id']);
        $updateStmt->execute();
        
        echo "TV {$tv['name']}: {$tv['status']} -> {$newStatus}\n";
        
        // Track newly offline TVs
        if ($newStatus === 'offline') {
            $newlyOffline[] = $tv;
        }
    }
    
    if ($isOnline) {
        $onlineCount++;
    } else {
        $offlineCount++;
    }
}

echo "Online: {$onlineCount}, Offline: {$offlineCount}\n";

// Create notifications for newly offline TVs
if (!empty($newlyOffline)) {
    foreach ($newlyOffline as $tv) {
        $notifyStmt = $conn->prepare("INSERT INTO notifications (user_id, type, title, message, link, created_at) VALUES (NULL, 'tv_offline', 'TV Offline Alert', ?, '/view.php', NOW())");
        $message = "TV '{$tv['name']}' tại {$tv['location']} đã mất kết nối";
        $notifyStmt->bind_param("s", $message);
        $notifyStmt->execute();
    }
    echo "Created " . count($newlyOffline) . " offline notifications\n";
}

$conn->close();

echo "[" . date('Y-m-d H:i:s') . "] TV status check completed.\n";

/**
 * Ping a host to check if it's reachable
 */
function pingHost($host, $timeout = 1) {
    // Try to ping (works on Linux/Mac)
    $command = sprintf('ping -c 1 -W %d %s 2>&1', $timeout, escapeshellarg($host));
    exec($command, $output, $returnCode);
    
    return $returnCode === 0;
}
