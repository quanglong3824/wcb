<?php
/**
 * TV Display Template
 * Universal template for all TV displays
 * Compatible with older Smart TV browsers
 * 
 * Usage: Include this file and set $tvConfig before including
 * 
 * Required $tvConfig:
 * - id: TV ID from database
 * - folder: Folder name (basement, lotus, etc.)
 * - name: Display name
 * - location: Location description
 */

// Default config
$tvConfig = isset($tvConfig) ? $tvConfig : [
    'id' => 1,
    'folder' => 'basement',
    'name' => 'TV Display',
    'location' => 'Aurora Hotel Plaza'
];

// Get TV info from database if possible
if (file_exists(dirname(__DIR__) . '/config/php/config.php')) {
    require_once dirname(__DIR__) . '/config/php/config.php';
    
    $conn = getDBConnection();
    if ($conn) {
        // Try to get TV by folder name
        $stmt = $conn->prepare("SELECT id, name, location FROM tvs WHERE folder = ? LIMIT 1");
        $stmt->bind_param("s", $tvConfig['folder']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $tv = $result->fetch_assoc();
            $tvConfig['id'] = $tv['id'];
            $tvConfig['name'] = $tv['name'];
            $tvConfig['location'] = $tv['location'];
        }
        
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Auto refresh fallback for older browsers - every 10 minutes -->
    <meta http-equiv="refresh" content="600">
    <title><?php echo htmlspecialchars($tvConfig['name']); ?> - Welcome Board</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset - Compatible with older browsers */
        * {
            margin: 0;
            padding: 0;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        
        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #000;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #fff;
        }
        
        /* TV Display Container */
        #tv-display {
            width: 100%;
            height: 100%;
            display: -webkit-box;
            display: -moz-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-box-align: center;
            -moz-box-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            align-items: center;
            -webkit-box-pack: center;
            -moz-box-pack: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            position: relative;
        }
        
        /* Content Display */
        #content-display {
            width: 100%;
            height: 100%;
            display: -webkit-box;
            display: -moz-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-box-align: center;
            -moz-box-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            align-items: center;
            -webkit-box-pack: center;
            -moz-box-pack: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-transition: opacity 0.8s ease;
            -moz-transition: opacity 0.8s ease;
            -o-transition: opacity 0.8s ease;
            transition: opacity 0.8s ease;
        }
        
        #content-display img,
        #content-display video {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            -o-object-fit: contain;
            object-fit: contain;
            display: block;
        }
        
        /* TV Info Overlay */
        .tv-info {
            position: fixed;
            top: 15px;
            left: 15px;
            background: rgba(0, 0, 0, 0.6);
            padding: 8px 15px;
            border-radius: 8px;
            z-index: 999;
            opacity: 0.8;
        }
        
        .tv-info:hover {
            opacity: 1;
        }
        
        .tv-info h2 {
            margin: 0 0 3px 0;
            font-size: 0.9em;
            color: #d4af37;
            font-weight: 600;
        }
        
        .tv-info p {
            margin: 0;
            opacity: 0.7;
            font-size: 0.75em;
        }
        
        /* Fullscreen Button */
        .fullscreen-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(212, 175, 55, 0.9);
            color: #fff;
            border: none;
            padding: 15px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1.2em;
            z-index: 1000;
            -webkit-transition: all 0.3s;
            -moz-transition: all 0.3s;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        
        .fullscreen-btn:hover {
            background: rgba(212, 175, 55, 1);
            -webkit-transform: scale(1.1);
            -moz-transform: scale(1.1);
            -ms-transform: scale(1.1);
            transform: scale(1.1);
        }
        
        /* No Content State */
        .no-content {
            text-align: center;
            padding: 40px;
        }
        
        .no-content i {
            font-size: 5em;
            color: #666;
            margin-bottom: 20px;
            display: block;
        }
        
        .no-content p {
            font-size: 1.5em;
            color: #999;
        }
        
        /* Status Indicator */
        .status-indicator {
            position: fixed;
            bottom: 15px;
            left: 15px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #2ecc71;
            z-index: 1000;
            -webkit-animation: pulse 2s infinite;
            animation: pulse 2s infinite;
        }
        
        .status-indicator.offline {
            background: #e74c3c;
        }
        
        @-webkit-keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body>
    <!-- TV Info -->
    <div class="tv-info">
        <h2><i class="fas fa-tv"></i> <?php echo htmlspecialchars($tvConfig['name']); ?></h2>
        <p><?php echo htmlspecialchars($tvConfig['location']); ?></p>
    </div>
    
    <!-- Status Indicator -->
    <div class="status-indicator" id="statusIndicator"></div>
    
    <!-- Main Display -->
    <div id="tv-display">
        <div id="content-display">
            <div class="no-content">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Đang tải nội dung...</p>
            </div>
        </div>
    </div>
    
    <!-- Fullscreen Button -->
    <button class="fullscreen-btn" onclick="toggleFullscreen()" title="Toàn màn hình">
        <i class="fas fa-expand"></i>
    </button>
    
    <!-- TV Configuration -->
    <script>
        // TV Configuration - passed to player
        var TV_ID = <?php echo intval($tvConfig['id']); ?>;
        var TV_FOLDER = '<?php echo addslashes($tvConfig['folder']); ?>';
        var TV_NAME = '<?php echo addslashes($tvConfig['name']); ?>';
    </script>
    
    <!-- TV Player Script -->
    <script src="../assets/js/tv-player.js"></script>
</body>
</html>
