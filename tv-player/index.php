<?php
/**
 * Universal TV Player - Compatible with older Smart TVs
 * Supports: Samsung Tizen (older), Sony Bravia, LG WebOS (older)
 * 
 * Features:
 * - No ES6+ syntax (var instead of let/const)
 * - No arrow functions
 * - No fetch API (uses XMLHttpRequest)
 * - No CSS transitions that may cause issues
 * - Simple polling for content updates
 * - Fallback for fullscreen API
 */

// Get TV folder from URL or default
$tvFolder = isset($_GET['tv']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['tv']) : 'basement';
$refreshInterval = isset($_GET['refresh']) ? intval($_GET['refresh']) : 30; // seconds
$slideInterval = isset($_GET['slide']) ? intval($_GET['slide']) : 8; // seconds

// Get base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['REQUEST_URI']));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>WCB Player - <?php echo htmlspecialchars(ucfirst($tvFolder)); ?></title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #000;
            font-family: Arial, sans-serif;
        }
        
        #player-container {
            width: 100%;
            height: 100%;
            position: relative;
            background: #000;
        }
        
        #content-layer {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #content-layer img,
        #content-layer video {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        /* Preload layer for smooth transitions */
        #preload-layer {
            position: absolute;
            top: -9999px;
            left: -9999px;
            visibility: hidden;
        }
        
        /* Status indicator */
        #status-indicator {
            position: fixed;
            top: 10px;
            right: 10px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #2ecc71;
            z-index: 100;
        }
        
        #status-indicator.offline {
            background: #e74c3c;
        }
        
        #status-indicator.loading {
            background: #f39c12;
        }
        
        /* TV Info overlay */
        #tv-info {
            position: fixed;
            top: 10px;
            left: 10px;
            background: rgba(0,0,0,0.7);
            color: #fff;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 12px;
            z-index: 100;
            display: none;
        }
        
        #tv-info.visible {
            display: block;
        }
        
        /* No content message */
        #no-content {
            text-align: center;
            color: #666;
        }
        
        #no-content .icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        
        #no-content .message {
            font-size: 24px;
        }
        
        /* Fullscreen button */
        #fullscreen-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: rgba(212, 175, 55, 0.9);
            border: none;
            border-radius: 50%;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            z-index: 100;
        }
        
        #fullscreen-btn:hover {
            background: rgba(212, 175, 55, 1);
        }
    </style>
</head>
<body>
    <div id="player-container">
        <div id="content-layer">
            <div id="no-content">
                <div class="icon">📺</div>
                <div class="message">Đang tải nội dung...</div>
            </div>
        </div>
        <div id="preload-layer"></div>
    </div>
    
    <div id="status-indicator" class="loading"></div>
    <div id="tv-info">TV: <?php echo htmlspecialchars(ucfirst($tvFolder)); ?></div>
    <button id="fullscreen-btn" onclick="toggleFullscreen()">⛶</button>
    
    <script type="text/javascript">
        // Configuration
        var CONFIG = {
            tvFolder: '<?php echo $tvFolder; ?>',
            apiUrl: '<?php echo $baseUrl; ?>/api/get-tv-content.php',
            heartbeatUrl: '<?php echo $baseUrl; ?>/api/heartbeat.php',
            refreshInterval: <?php echo $refreshInterval * 1000; ?>,
            slideInterval: <?php echo $slideInterval * 1000; ?>,
            retryDelay: 5000,
            maxRetries: 3
        };
        
        // State
        var state = {
            contents: [],
            currentIndex: 0,
            slideTimer: null,
            refreshTimer: null,
            heartbeatTimer: null,
            lastContentHash: '',
            retryCount: 0,
            isOnline: true
        };
        
        // DOM Elements
        var contentLayer = document.getElementById('content-layer');
        var preloadLayer = document.getElementById('preload-layer');
        var statusIndicator = document.getElementById('status-indicator');
        var tvInfo = document.getElementById('tv-info');
        
        // XMLHttpRequest helper (compatible with old browsers)
        function ajax(url, callback, errorCallback) {
            var xhr;
            
            // Try different XMLHttpRequest implementations
            if (window.XMLHttpRequest) {
                xhr = new XMLHttpRequest();
            } else if (window.ActiveXObject) {
                try {
                    xhr = new ActiveXObject('Msxml2.XMLHTTP');
                } catch (e) {
                    try {
                        xhr = new ActiveXObject('Microsoft.XMLHTTP');
                    } catch (e2) {
                        if (errorCallback) errorCallback('XMLHttpRequest not supported');
                        return;
                    }
                }
            }
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            var data = JSON.parse(xhr.responseText);
                            callback(data);
                        } catch (e) {
                            if (errorCallback) errorCallback('JSON parse error');
                        }
                    } else {
                        if (errorCallback) errorCallback('HTTP error: ' + xhr.status);
                    }
                }
            };
            
            xhr.open('GET', url, true);
            xhr.send(null);
        }
        
        // Load content from API
        function loadContent() {
            setStatus('loading');
            
            var url = CONFIG.apiUrl + '?folder=' + encodeURIComponent(CONFIG.tvFolder) + '&get_all=1&t=' + Date.now();
            
            ajax(url, function(data) {
                state.retryCount = 0;
                setStatus('online');
                
                if (data.success && data.contents && data.contents.length > 0) {
                    // Check if content changed
                    var newHash = getContentHash(data.contents);
                    
                    if (newHash !== state.lastContentHash) {
                        state.lastContentHash = newHash;
                        state.contents = data.contents.slice(0, 3);
                        state.currentIndex = 0;
                        
                        // Preload all images
                        preloadContents(function() {
                            startSlideshow();
                        });
                    }
                } else {
                    showNoContent(data.message || 'Chưa có nội dung');
                }
            }, function(error) {
                console.log('Load error:', error);
                setStatus('offline');
                
                state.retryCount++;
                if (state.retryCount < CONFIG.maxRetries) {
                    setTimeout(loadContent, CONFIG.retryDelay);
                }
            });
        }
        
        // Generate hash for content comparison
        function getContentHash(contents) {
            var hash = '';
            for (var i = 0; i < contents.length; i++) {
                hash += contents[i].id + ':' + contents[i].file_path + ';';
            }
            return hash;
        }
        
        // Preload images for smooth transitions
        function preloadContents(callback) {
            var loaded = 0;
            var total = state.contents.length;
            
            if (total === 0) {
                callback();
                return;
            }
            
            for (var i = 0; i < state.contents.length; i++) {
                var content = state.contents[i];
                
                if (content.type === 'image') {
                    var img = new Image();
                    img.onload = img.onerror = function() {
                        loaded++;
                        if (loaded >= total) callback();
                    };
                    img.src = '../' + content.file_path;
                } else {
                    loaded++;
                    if (loaded >= total) callback();
                }
            }
        }
        
        // Start slideshow
        function startSlideshow() {
            // Clear existing timer
            if (state.slideTimer) {
                clearInterval(state.slideTimer);
            }
            
            // Display first content
            displayContent(state.currentIndex);
            
            // Start timer if multiple contents
            if (state.contents.length > 1) {
                state.slideTimer = setInterval(function() {
                    nextSlide();
                }, CONFIG.slideInterval);
            }
        }
        
        // Next slide
        function nextSlide() {
            state.currentIndex = (state.currentIndex + 1) % state.contents.length;
            displayContent(state.currentIndex);
        }
        
        // Display content
        function displayContent(index) {
            var content = state.contents[index];
            if (!content) return;
            
            var html = '';
            
            if (content.type === 'image') {
                html = '<img src="../' + content.file_path + '" alt="' + (content.name || '') + '">';
            } else if (content.type === 'video') {
                html = '<video src="../' + content.file_path + '" autoplay loop muted playsinline></video>';
            }
            
            contentLayer.innerHTML = html;
        }
        
        // Show no content message
        function showNoContent(message) {
            contentLayer.innerHTML = '<div id="no-content"><div class="icon">📺</div><div class="message">' + message + '</div></div>';
        }
        
        // Set status indicator
        function setStatus(status) {
            statusIndicator.className = status;
            state.isOnline = (status === 'online');
        }
        
        // Send heartbeat
        function sendHeartbeat() {
            var url = CONFIG.heartbeatUrl + '?folder=' + encodeURIComponent(CONFIG.tvFolder) + '&t=' + Date.now();
            
            ajax(url, function(data) {
                if (data.reload) {
                    // Server requested reload
                    window.location.reload();
                }
            }, function() {
                // Heartbeat failed, ignore
            });
        }
        
        // Toggle fullscreen
        function toggleFullscreen() {
            var elem = document.documentElement;
            
            if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) {
                // Exit fullscreen
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            } else {
                // Enter fullscreen
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                } else if (elem.mozRequestFullScreen) {
                    elem.mozRequestFullScreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }
            }
        }
        
        // Toggle TV info on click
        document.body.onclick = function(e) {
            if (e.target.id !== 'fullscreen-btn') {
                if (tvInfo.className === 'visible') {
                    tvInfo.className = '';
                } else {
                    tvInfo.className = 'visible';
                    setTimeout(function() {
                        tvInfo.className = '';
                    }, 3000);
                }
            }
        };
        
        // Initialize
        function init() {
            // Load content immediately
            loadContent();
            
            // Set up refresh timer
            state.refreshTimer = setInterval(loadContent, CONFIG.refreshInterval);
            
            // Set up heartbeat timer (every 60 seconds)
            sendHeartbeat();
            state.heartbeatTimer = setInterval(sendHeartbeat, 60000);
            
            // Auto-enter fullscreen after 3 seconds (if supported)
            setTimeout(function() {
                try {
                    toggleFullscreen();
                } catch (e) {
                    // Fullscreen not supported or blocked
                }
            }, 3000);
        }
        
        // Start when page loads
        if (document.readyState === 'complete') {
            init();
        } else if (window.addEventListener) {
            window.addEventListener('load', init, false);
        } else if (window.attachEvent) {
            window.attachEvent('onload', init);
        } else {
            window.onload = init;
        }
        
        // Handle visibility change (pause when hidden)
        if (document.addEventListener) {
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    if (state.slideTimer) clearInterval(state.slideTimer);
                } else {
                    if (state.contents.length > 1) {
                        startSlideshow();
                    }
                }
            }, false);
        }
    </script>
</body>
</html>
