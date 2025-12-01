<?php
/**
 * TV Display Template for Nested Folders (e.g., fo/tv1, fo/tv2)
 * Universal template for TV displays in subfolders
 * Compatible with older Smart TV browsers
 */

// Default config
$tvConfig = isset($tvConfig) ? $tvConfig : [
    'id' => 1,
    'folder' => 'fo/tv1',
    'name' => 'TV Display',
    'location' => 'Aurora Hotel Plaza'
];

// Get TV info from database if possible
if (file_exists(dirname(dirname(__DIR__)) . '/config/php/config.php')) {
    require_once dirname(dirname(__DIR__)) . '/config/php/config.php';
    
    $conn = getDBConnection();
    if ($conn) {
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
    <meta http-equiv="refresh" content="600">
    <title><?php echo htmlspecialchars($tvConfig['name']); ?> - Welcome Board</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; overflow: hidden; background: #000; }
        body { font-family: Arial, Helvetica, sans-serif; color: #fff; }
        #tv-display { width: 100%; height: 100%; display: -webkit-flex; display: flex; -webkit-align-items: center; align-items: center; -webkit-justify-content: center; justify-content: center; position: relative; }
        #content-display { width: 100%; height: 100%; display: -webkit-flex; display: flex; -webkit-align-items: center; align-items: center; -webkit-justify-content: center; justify-content: center; -webkit-transition: opacity 0.8s ease; transition: opacity 0.8s ease; }
        #content-display img, #content-display video { max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain; display: block; }
        .tv-info { position: fixed; top: 15px; left: 15px; background: rgba(0,0,0,0.6); padding: 8px 15px; border-radius: 8px; z-index: 999; opacity: 0.8; }
        .tv-info:hover { opacity: 1; }
        .tv-info h2 { margin: 0 0 3px 0; font-size: 0.9em; color: #d4af37; font-weight: 600; }
        .tv-info p { margin: 0; opacity: 0.7; font-size: 0.75em; }
        .fullscreen-btn { position: fixed; bottom: 20px; right: 20px; background: rgba(212,175,55,0.9); color: #fff; border: none; padding: 15px 20px; border-radius: 50px; cursor: pointer; font-size: 1.2em; z-index: 1000; -webkit-transition: all 0.3s; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        .fullscreen-btn:hover { background: rgba(212,175,55,1); -webkit-transform: scale(1.1); transform: scale(1.1); }
        .no-content { text-align: center; padding: 40px; }
        .no-content i { font-size: 5em; color: #666; margin-bottom: 20px; display: block; }
        .no-content p { font-size: 1.5em; color: #999; }
        .status-indicator { position: fixed; bottom: 15px; left: 15px; width: 10px; height: 10px; border-radius: 50%; background: #2ecc71; z-index: 1000; -webkit-animation: pulse 2s infinite; animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
    </style>
</head>
<body>
    <div class="tv-info">
        <h2><i class="fas fa-tv"></i> <?php echo htmlspecialchars($tvConfig['name']); ?></h2>
        <p><?php echo htmlspecialchars($tvConfig['location']); ?></p>
    </div>
    <div class="status-indicator" id="statusIndicator"></div>
    <div id="tv-display">
        <div id="content-display">
            <div class="no-content">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Đang tải nội dung...</p>
            </div>
        </div>
    </div>
    <button class="fullscreen-btn" onclick="toggleFullscreen()" title="Toàn màn hình">
        <i class="fas fa-expand"></i>
    </button>
    
    <script>
        var TV_ID = <?php echo intval($tvConfig['id']); ?>;
        var TV_FOLDER = '<?php echo addslashes($tvConfig['folder']); ?>';
        var TV_NAME = '<?php echo addslashes($tvConfig['name']); ?>';
    </script>
    <script src="../../assets/js/tv-player.js"></script>
</body>
</html>
