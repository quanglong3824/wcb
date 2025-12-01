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
 */

(function() {
    'use strict';
    
    // Configuration
    var CONFIG = {
        SLIDE_INTERVAL: 8000,        // 8 seconds per slide
        CONTENT_REFRESH: 60000,      // Check for new content every 60 seconds
        HEARTBEAT_INTERVAL: 30000,   // Send heartbeat every 30 seconds
        RELOAD_CHECK_INTERVAL: 10000, // Check for reload signal every 10 seconds
        FADE_DURATION: 800,          // Fade transition duration
        MAX_CONTENTS: 3              // Maximum contents to display
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
        lastContentHash: '',
        isTransitioning: false
    };
    
    // Initialize
    function init() {
        // Get TV info from page
        state.tvId = window.TV_ID || 1;
        state.tvFolder = window.TV_FOLDER || 'basement';
        
        console.log('[TV Player] Initializing for TV:', state.tvFolder, 'ID:', state.tvId);
        
        // Load content
        loadContent();
        
        // Start heartbeat
        startHeartbeat();
        
        // Start reload checker
        startReloadChecker();
        
        // Setup content refresh
        state.contentRefreshTimer = setInterval(loadContent, CONFIG.CONTENT_REFRESH);
        
        // Add meta refresh as fallback (reload page every 10 minutes)
        addMetaRefresh(600);
        
        // Handle visibility change
        if (typeof document.hidden !== 'undefined') {
            document.addEventListener('visibilitychange', handleVisibilityChange);
        }
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
                        handleContentResponse(data);
                    } catch (e) {
                        console.error('[TV Player] Parse error:', e);
                    }
                } else {
                    console.error('[TV Player] Load error:', xhr.status);
                }
            }
        };
        
        xhr.onerror = function() {
            console.error('[TV Player] Network error');
        };
        
        xhr.ontimeout = function() {
            console.error('[TV Player] Request timeout');
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
            console.log('[TV Player] No content available');
            showNoContent(data.message || 'Chưa có nội dung hiển thị');
        }
    }
    
    // Start slideshow
    function startSlideshow() {
        if (state.contentList.length === 0) return;
        
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
        if (!display) return;
        
        console.log('[TV Player] Displaying:', content.name, '- Type:', content.type);
        
        var html = '';
        var basePath = getBasePath();
        
        if (content.type === 'image') {
            html = '<img src="' + basePath + content.file_path + '" alt="' + escapeHtml(content.name) + '" ' +
                   'style="max-width:100%;max-height:100%;object-fit:contain;" ' +
                   'onerror="this.src=\'' + basePath + 'assets/img/no-image.png\'">';
        } else if (content.type === 'video') {
            html = '<video src="' + basePath + content.file_path + '" autoplay loop muted playsinline ' +
                   'style="max-width:100%;max-height:100%;object-fit:contain;"></video>';
        }
        
        display.innerHTML = html;
        
        // For video, ensure it plays
        var video = display.querySelector('video');
        if (video) {
            video.play().catch(function(e) {
                console.log('[TV Player] Video autoplay blocked:', e);
            });
        }
    }
    
    // Show no content message
    function showNoContent(message) {
        var display = document.getElementById('content-display');
        if (!display) return;
        
        display.innerHTML = 
            '<div style="text-align:center;padding:40px;">' +
            '<i class="fas fa-tv" style="font-size:5em;color:#666;display:block;margin-bottom:20px;"></i>' +
            '<p style="font-size:1.5em;color:#999;">' + escapeHtml(message) + '</p>' +
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
    
    // Reload page
    function reloadPage() {
        // Clear all timers
        stopSlideshow();
        if (state.contentRefreshTimer) clearInterval(state.contentRefreshTimer);
        if (state.heartbeatTimer) clearInterval(state.heartbeatTimer);
        if (state.reloadCheckTimer) clearInterval(state.reloadCheckTimer);
        
        // Reload
        window.location.reload(true);
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
            // Page became visible, refresh content
            loadContent();
        }
    }
    
    // Set opacity with fallback for older browsers
    function setOpacity(element, value) {
        element.style.opacity = value;
        element.style.filter = 'alpha(opacity=' + (value * 100) + ')'; // IE fallback
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
