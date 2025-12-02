/**
 * TV Player - Universal Player for all TV displays
 * Compatible with older Smart TV browsers (Samsung, Sony, LG)
 * 
 * Features:
 * - Slideshow with smooth transitions
 * - Auto-refresh content from server
 * - Heartbeat system
 * - Fallback for older browsers
 * - Meta refresh as backup reload mechanism
 * - Multiple reload detection mechanisms for old TVs
 */

(function() {
    'use strict';
    
    // Configuration - Optimized for old Smart TVs
    var CONFIG = {
        SLIDE_INTERVAL: 8000,        // 8 seconds per slide
        CONTENT_REFRESH: 30000,      // Check for new content every 30 seconds
        HEARTBEAT_INTERVAL: 15000,   // Send heartbeat every 15 seconds
        RELOAD_CHECK_INTERVAL: 5000, // Check for reload signal every 5 seconds
        RELOAD_SIGNAL_CHECK: 3000,   // Check system_settings reload signal every 3 seconds
        FADE_DURATION: 800,          // Fade transition duration
        MAX_CONTENTS: 10,            // Maximum contents to display
        META_REFRESH_SECONDS: 300    // Meta refresh every 5 minutes (backup)
    };
    
    // State
    var state = {
        tvId: null,
        tvFolder: null,
        contentList: [],
        currentIndex: 0,
        slideTimer: null,
        contentRefreshTimer: null,
        heartbeatTimer: null,
        reloadCheckTimer: null,
        reloadSignalTimer: null,
        fullscreenCheckTimer: null,
        lastContentHash: '',
        lastReloadTimestamp: 0,
        lastFullscreenTimestamp: 0,
        isTransitioning: false,
        initTime: 0
    };
    
    // Initialize
    function init() {
        // Get TV info from page
        state.tvId = window.TV_ID || 1;
        state.tvFolder = window.TV_FOLDER || 'basement';
        state.initTime = Date.now();
        
        console.log('[TV Player] Initializing for TV:', state.tvFolder, 'ID:', state.tvId);
        
        // Load initial reload timestamp from server
        loadInitialReloadTimestamp();
        
        // Load content
        loadContent();
        
        // Start heartbeat
        startHeartbeat();
        
        // Start reload checker
        startReloadChecker();
        
        // Start reload signal checker (backup for old TVs)
        startReloadSignalChecker();
        
        // Start fullscreen signal checker
        startFullscreenChecker();
        
        // Setup content refresh
        state.contentRefreshTimer = setInterval(loadContent, CONFIG.CONTENT_REFRESH);
        
        // Add meta refresh as fallback
        addMetaRefresh(CONFIG.META_REFRESH_SECONDS);
        
        // Handle visibility change
        if (typeof document.hidden !== 'undefined') {
            document.addEventListener('visibilitychange', handleVisibilityChange);
        }
        
        // Auto fullscreen on load (for TV displays)
        setTimeout(function() {
            tryAutoFullscreen();
        }, 2000);
    }
    
    // Load initial reload timestamp from server
    function loadInitialReloadTimestamp() {
        var xhr = new XMLHttpRequest();
        var basePath = getBasePath();
        var url = basePath + 'api/check-reload-signal.php?tv_id=' + state.tvId;
        
        xhr.open('GET', url, true);
        xhr.timeout = 5000;
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success && data.timestamp) {
                        state.lastReloadTimestamp = parseInt(data.timestamp, 10) || 0;
                        console.log('[TV Player] Initial reload timestamp:', state.lastReloadTimestamp);
                    }
                } catch (e) {
                    console.error('[TV Player] Error parsing initial timestamp:', e);
                }
            }
        };
        
        xhr.send();
    }
    
    // Determine base path based on folder depth
    function getBasePath() {
        var folder = state.tvFolder || '';
        var depth = (folder.match(/\//g) || []).length;
        return depth > 0 ? '../../' : '../';
    }
    
    // Load content from API
    function loadContent() {
        console.log('[TV Player] Loading content...');
        
        var xhr = new XMLHttpRequest();
        var basePath = getBasePath();
        var url = basePath + 'api/get-tv-content.php?tv_id=' + state.tvId + '&get_all=1&folder=' + encodeURIComponent(state.tvFolder);
        
        xhr.open('GET', url, true);
        xhr.timeout = 10000;
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        console.log('[TV Player] Content response:', data);
                        handleContentResponse(data);
                    } catch (e) {
                        console.error('[TV Player] Parse error:', e);
                        showNoContent('Lỗi tải dữ liệu');
                    }
                } else {
                    console.error('[TV Player] Load error:', xhr.status);
                    showNoContent('Lỗi kết nối server');
                }
            }
        };
        
        xhr.onerror = function() {
            console.error('[TV Player] Network error');
            showNoContent('Lỗi mạng');
        };
        
        xhr.ontimeout = function() {
            console.error('[TV Player] Request timeout');
            showNoContent('Hết thời gian chờ');
        };
        
        xhr.send();
    }
    
    // Handle content response
    function handleContentResponse(data) {
        if (data.success && data.contents && data.contents.length > 0) {
            var contents = data.contents.slice(0, CONFIG.MAX_CONTENTS);
            
            // Check if content changed
            var newHash = JSON.stringify(contents.map(function(c) { return c.id; }));
            
            if (newHash !== state.lastContentHash) {
                console.log('[TV Player] Content updated, reloading slideshow');
                state.lastContentHash = newHash;
                state.contentList = contents;
                state.currentIndex = 0;
                
                // Restart slideshow
                stopSlideshow();
                startSlideshow();
            }
        } else {
            console.log('[TV Player] No content available:', data.message);
            showNoContent(data.message || 'Chưa có nội dung hiển thị');
        }
    }
    
    // Start slideshow
    function startSlideshow() {
        if (state.contentList.length === 0) return;
        
        console.log('[TV Player] Starting slideshow with', state.contentList.length, 'items');
        
        // Display first content
        displayContent(state.contentList[state.currentIndex]);
        
        // If multiple contents, start rotation
        if (state.contentList.length > 1) {
            state.slideTimer = setInterval(nextSlide, CONFIG.SLIDE_INTERVAL);
        }
    }
    
    // Stop slideshow
    function stopSlideshow() {
        if (state.slideTimer) {
            clearInterval(state.slideTimer);
            state.slideTimer = null;
        }
    }
    
    // Next slide with fade transition
    function nextSlide() {
        if (state.isTransitioning) return;
        state.isTransitioning = true;
        
        var display = document.getElementById('content-display');
        if (!display) return;
        
        // Fade out
        setOpacity(display, 0);
        
        // Wait for fade, then change content
        setTimeout(function() {
            state.currentIndex = (state.currentIndex + 1) % state.contentList.length;
            displayContent(state.contentList[state.currentIndex]);
            
            // Fade in
            setTimeout(function() {
                setOpacity(display, 1);
                state.isTransitioning = false;
            }, 50);
        }, CONFIG.FADE_DURATION);
    }
    
    // Display content
    function displayContent(content) {
        var display = document.getElementById('content-display');
        if (!display) {
            console.error('[TV Player] content-display element not found!');
            return;
        }
        
        console.log('[TV Player] Displaying:', content.name, '- Type:', content.type);
        
        var html = '';
        var basePath = getBasePath();
        var filePath = content.file_path;
        
        // Handle file path - add basePath if not absolute
        if (filePath && filePath.indexOf('http') !== 0 && filePath.indexOf('/') !== 0) {
            filePath = basePath + filePath;
        }
        
        if (content.type === 'image') {
            html = '<img src="' + filePath + '" alt="' + escapeHtml(content.name) + '" ' +
                   'style="width:100%;height:100%;object-fit:cover;" ' +
                   'onerror="this.src=\'' + basePath + 'assets/img/no-image.png\'">';
        } else if (content.type === 'video') {
            html = '<video src="' + filePath + '" autoplay loop muted playsinline ' +
                   'style="width:100%;height:100%;object-fit:cover;"></video>';
        }
        
        // Remove mode-contain class to show full screen
        display.classList.remove('mode-contain');
        display.innerHTML = html;
        
        // For video, ensure it plays
        var video = display.querySelector('video');
        if (video) {
            video.play().catch(function(e) {
                console.log('[TV Player] Video autoplay blocked:', e);
            });
        }
    }
    
    // Show no content message - Display logo instead of error
    function showNoContent(message) {
        var display = document.getElementById('content-display');
        if (!display) return;
        
        var basePath = getBasePath();
        
        // Hiển thị logo thay vì thông báo lỗi
        display.innerHTML = 
            '<div style="text-align:center;padding:40px;display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;">' +
            '<img src="' + basePath + 'assets/img/logo-dark-ui.png" alt="Logo" ' +
            'style="max-width:400px;max-height:300px;object-fit:contain;margin-bottom:30px;" ' +
            'onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'block\';">' +
            '<div style="display:none;text-align:center;">' +
            '<i class="fas fa-tv" style="font-size:5em;color:#d4af37;display:block;margin-bottom:20px;"></i>' +
            '</div>' +
            '<p style="font-size:1.2em;color:#666;margin-top:20px;">Chế độ chờ</p>' +
            '</div>';
    }
    
    // Heartbeat
    function startHeartbeat() {
        sendHeartbeat();
        state.heartbeatTimer = setInterval(sendHeartbeat, CONFIG.HEARTBEAT_INTERVAL);
    }
    
    function sendHeartbeat() {
        var xhr = new XMLHttpRequest();
        var basePath = getBasePath();
        var url = basePath + 'api/heartbeat.php?tv_id=' + state.tvId + '&folder=' + encodeURIComponent(state.tvFolder);
        
        xhr.open('GET', url, true);
        xhr.timeout = 5000;
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.reload) {
                        console.log('[TV Player] Reload signal received');
                        reloadPage();
                    }
                } catch (e) {}
            }
        };
        
        xhr.send();
    }
    
    // Reload checker (backup for heartbeat)
    function startReloadChecker() {
        state.reloadCheckTimer = setInterval(checkReloadSignal, CONFIG.RELOAD_CHECK_INTERVAL);
    }
    
    function checkReloadSignal() {
        var xhr = new XMLHttpRequest();
        var basePath = getBasePath();
        var url = basePath + 'api/heartbeat.php?tv_id=' + state.tvId + '&folder=' + encodeURIComponent(state.tvFolder) + '&check_only=1';
        
        xhr.open('GET', url, true);
        xhr.timeout = 5000;
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.reload) {
                        console.log('[TV Player] Reload signal from checker');
                        reloadPage();
                    }
                } catch (e) {}
            }
        };
        
        xhr.send();
    }
    
    // Reload signal checker - Check system_settings for reload timestamp
    function startReloadSignalChecker() {
        checkReloadTimestamp();
        state.reloadSignalTimer = setInterval(checkReloadTimestamp, CONFIG.RELOAD_SIGNAL_CHECK);
    }
    
    function checkReloadTimestamp() {
        var xhr = new XMLHttpRequest();
        var basePath = getBasePath();
        var url = basePath + 'api/check-reload-signal.php?tv_id=' + state.tvId + '&t=' + Date.now();
        
        xhr.open('GET', url, true);
        xhr.timeout = 5000;
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success && data.timestamp) {
                        var serverTimestamp = parseInt(data.timestamp, 10) || 0;
                        
                        if (serverTimestamp > state.lastReloadTimestamp && 
                            serverTimestamp * 1000 > state.initTime) {
                            console.log('[TV Player] New reload signal detected!');
                            reloadPage();
                        }
                    }
                } catch (e) {
                    console.error('[TV Player] Error checking reload signal:', e);
                }
            }
        };
        
        xhr.send();
    }
    
    // Fullscreen signal checker
    function startFullscreenChecker() {
        checkFullscreenSignal();
        state.fullscreenCheckTimer = setInterval(checkFullscreenSignal, 5000);
    }
    
    function checkFullscreenSignal() {
        var xhr = new XMLHttpRequest();
        var basePath = getBasePath();
        var url = basePath + 'api/check-fullscreen-signal.php?tv_id=' + state.tvId + '&t=' + Date.now();
        
        xhr.open('GET', url, true);
        xhr.timeout = 5000;
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success && data.timestamp) {
                        var serverTimestamp = parseInt(data.timestamp, 10) || 0;
                        
                        if (serverTimestamp > state.lastFullscreenTimestamp && 
                            serverTimestamp * 1000 > state.initTime) {
                            console.log('[TV Player] Fullscreen signal detected!');
                            state.lastFullscreenTimestamp = serverTimestamp;
                            tryAutoFullscreen();
                        }
                    }
                } catch (e) {
                    console.error('[TV Player] Error checking fullscreen signal:', e);
                }
            }
        };
        
        xhr.send();
    }
    
    // Try to enter fullscreen mode
    function tryAutoFullscreen() {
        var elem = document.documentElement;
        
        // Check if already fullscreen
        if (document.fullscreenElement || document.webkitFullscreenElement || 
            document.mozFullScreenElement || document.msFullscreenElement) {
            console.log('[TV Player] Already in fullscreen');
            return;
        }
        
        console.log('[TV Player] Attempting fullscreen...');
        
        try {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            } else if (elem.mozRequestFullScreen) {
                elem.mozRequestFullScreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            }
        } catch (e) {
            console.log('[TV Player] Fullscreen request failed:', e);
        }
    }
    
    // Reload page
    function reloadPage() {
        console.log('[TV Player] Reloading page...');
        
        // Clear all timers
        stopSlideshow();
        if (state.contentRefreshTimer) clearInterval(state.contentRefreshTimer);
        if (state.heartbeatTimer) clearInterval(state.heartbeatTimer);
        if (state.reloadCheckTimer) clearInterval(state.reloadCheckTimer);
        if (state.reloadSignalTimer) clearInterval(state.reloadSignalTimer);
        
        // Show reload indicator
        try {
            var indicator = document.createElement('div');
            indicator.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.9);z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;color:white;';
            indicator.innerHTML = '<div style="font-size:3em;margin-bottom:20px;">⟳</div><div style="font-size:1.5em;">Đang tải lại...</div>';
            document.body.appendChild(indicator);
        } catch (e) {}
        
        // Reload with cache bust
        setTimeout(function() {
            window.location.href = window.location.href.split('?')[0] + '?reload=' + Date.now();
        }, 500);
    }
    
    // Add meta refresh tag as fallback
    function addMetaRefresh(seconds) {
        var meta = document.createElement('meta');
        meta.httpEquiv = 'refresh';
        meta.content = seconds.toString();
        document.head.appendChild(meta);
    }
    
    // Handle visibility change
    function handleVisibilityChange() {
        if (!document.hidden) {
            loadContent();
        }
    }
    
    // Set opacity with fallback for older browsers
    function setOpacity(element, value) {
        element.style.opacity = value;
        element.style.filter = 'alpha(opacity=' + (value * 100) + ')';
    }
    
    // Escape HTML
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text || ''));
        return div.innerHTML;
    }
    
    // Fullscreen toggle
    window.toggleFullscreen = function() {
        var elem = document.documentElement;
        
        if (!document.fullscreenElement && !document.webkitFullscreenElement && 
            !document.mozFullScreenElement && !document.msFullscreenElement) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            } else if (elem.mozRequestFullScreen) {
                elem.mozRequestFullScreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    };
    
    // Start when DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Expose for debugging
    window.TVPlayer = {
        getState: function() { return state; },
        reload: reloadPage,
        loadContent: loadContent
    };
    
})();
