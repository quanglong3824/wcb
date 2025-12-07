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

(function () {
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
        META_REFRESH_SECONDS: 300,   // Meta refresh every 5 minutes (backup)

        // Keep-alive settings to prevent Samsung TV screensaver
        KEEPALIVE_INTERVAL: 30000,   // Keep-alive AJAX ping every 30 seconds
        PIXEL_FLICKER_INTERVAL: 45000, // Pixel color change every 45 seconds
        DOM_MOVE_INTERVAL: 60000     // Move invisible DOM element every 60 seconds
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
        testModeCheckTimer: null,
        lastContentHash: '',
        lastReloadTimestamp: 0,
        lastFullscreenTimestamp: 0,
        lastTestModeTimestamp: 0,
        testModeActive: false,
        isTransitioning: false,
        initTime: 0,

        // Keep-alive state
        keepAliveTimer: null,
        pixelFlickerTimer: null,
        domMoveTimer: null,
        keepAliveElement: null,
        pixelFlickerElement: null,
        keepAliveCount: 0
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

        // Start test mode checker
        startTestModeChecker();

        // Start keep-alive system (prevent Samsung TV screensaver)
        startKeepAliveSystem();

        // Setup content refresh
        state.contentRefreshTimer = setInterval(loadContent, CONFIG.CONTENT_REFRESH);

        // Add meta refresh as fallback
        addMetaRefresh(CONFIG.META_REFRESH_SECONDS);

        // Handle visibility change
        if (typeof document.hidden !== 'undefined') {
            document.addEventListener('visibilitychange', handleVisibilityChange);
        }

        // Auto fullscreen on load (for TV displays)
        setTimeout(function () {
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

        xhr.onreadystatechange = function () {
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

        xhr.onreadystatechange = function () {
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

        xhr.onerror = function () {
            console.error('[TV Player] Network error');
            showNoContent('Lỗi mạng');
        };

        xhr.ontimeout = function () {
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
            var newHash = JSON.stringify(contents.map(function (c) { return c.id; }));

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
        setTimeout(function () {
            state.currentIndex = (state.currentIndex + 1) % state.contentList.length;
            displayContent(state.contentList[state.currentIndex]);

            // Fade in
            setTimeout(function () {
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
            video.play().catch(function (e) {
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

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.reload) {
                        console.log('[TV Player] Reload signal received');
                        reloadPage();
                    }
                } catch (e) { }
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

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.reload) {
                        console.log('[TV Player] Reload signal from checker');
                        reloadPage();
                    }
                } catch (e) { }
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

        xhr.onreadystatechange = function () {
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

        xhr.onreadystatechange = function () {
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

    // Test mode checker
    function startTestModeChecker() {
        checkTestMode();
        state.testModeCheckTimer = setInterval(checkTestMode, 3000);
    }

    function checkTestMode() {
        var xhr = new XMLHttpRequest();
        var basePath = getBasePath();
        var url = basePath + 'api/check-test-mode.php?t=' + Date.now();

        xhr.open('GET', url, true);
        xhr.timeout = 5000;

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        if (data.test_mode && !state.testModeActive) {
                            showTestOverlay();
                            state.testModeActive = true;
                        } else if (!data.test_mode && state.testModeActive) {
                            hideTestOverlay();
                            state.testModeActive = false;
                        }
                    }
                } catch (e) {
                    console.error('[TV Player] Error checking test mode:', e);
                }
            }
        };

        xhr.send();
    }

    function showTestOverlay() {
        // Remove existing overlay if any
        hideTestOverlay();

        var overlay = document.createElement('div');
        overlay.id = 'test-mode-overlay';
        // STATIC MODE: No animation, completely static to prevent TV screensaver detection
        // Keep-alive AJAX still runs in background to trick TV
        overlay.style.cssText =
            'position:fixed;' +
            'top:0;left:0;right:0;bottom:0;' +
            'z-index:9998;' +
            'pointer-events:none;' +
            'border:4px solid #f5af19;';  // Static border, no animation

        // No CSS animations - completely static display
        var style = document.createElement('style');
        style.id = 'test-mode-styles';
        style.innerHTML = '/* Static mode - no animations */';
        document.head.appendChild(style);

        // Corner badge - Top right with friendly design (STATIC)
        var badge = document.createElement('div');
        badge.style.cssText =
            'position:absolute;' +
            'top:20px;right:20px;' +
            'background:linear-gradient(135deg, #ff9500 0%, #ff5e3a 100%);' +
            'color:#fff;' +
            'padding:12px 24px;' +
            'border-radius:12px;' +
            'box-shadow:0 6px 20px rgba(255,94,58,0.4);' +
            'display:flex;' +
            'flex-direction:column;' +
            'align-items:center;' +
            'gap:6px;';  // No animation

        badge.innerHTML =
            '<div style="display:flex;align-items:center;gap:8px;font-size:15px;font-weight:700;">' +
            '<span style="display:inline-block;width:10px;height:10px;background:#4ade80;border-radius:50%;box-shadow:0 0 8px rgba(74,222,128,0.6);"></span>' +  // Static dot, no blink
            'CHẾ ĐỘ KIỂM TRA' +
            '</div>' +
            '<div style="font-size:11px;opacity:0.9;font-weight:500;">Hệ thống đang chạy bình thường</div>';
        overlay.appendChild(badge);

        // Bottom notice - Friendly reminder for staff (STATIC)
        var notice = document.createElement('div');
        notice.style.cssText =
            'position:absolute;' +
            'bottom:25px;left:50%;' +
            'transform:translateX(-50%);' +
            'background:rgba(0,0,0,0.75);' +
            'backdrop-filter:blur(10px);' +
            '-webkit-backdrop-filter:blur(10px);' +
            'color:#fff;' +
            'padding:14px 28px;' +
            'border-radius:50px;' +
            'font-size:14px;' +
            'font-weight:500;' +
            'display:flex;' +
            'align-items:center;' +
            'gap:12px;' +
            'border:1px solid rgba(255,255,255,0.1);' +
            'box-shadow:0 4px 20px rgba(0,0,0,0.3);';

        notice.innerHTML =
            '<span style="font-size:20px;">🔧</span>' +
            '<span>Đang kiểm tra hệ thống • <strong style="color:#4ade80;">Vui lòng không tắt TV</strong></span>' +
            '<span style="font-size:20px;">✨</span>';
        overlay.appendChild(notice);

        // Center watermark - Very subtle, non-intrusive (STATIC)
        var watermark = document.createElement('div');
        watermark.style.cssText =
            'position:absolute;' +
            'top:50%;left:50%;' +
            'transform:translate(-50%,-50%) rotate(-8deg);' +
            'font-size:12vw;' +
            'font-weight:900;' +
            'color:rgba(255,149,0,0.06);' +
            'text-transform:uppercase;' +
            'letter-spacing:20px;' +
            'white-space:nowrap;' +
            'user-select:none;' +
            '-webkit-user-select:none;';
        watermark.innerHTML = 'TEST';
        overlay.appendChild(watermark);

        // Corner indicators - Static dots at corners (NO ANIMATION)
        var corners = ['top:12px;left:12px;', 'top:12px;right:12px;', 'bottom:12px;left:12px;', 'bottom:12px;right:12px;'];
        for (var i = 0; i < corners.length; i++) {
            var dot = document.createElement('div');
            dot.style.cssText =
                'position:absolute;' + corners[i] +
                'width:8px;height:8px;' +
                'background:linear-gradient(135deg, #ff9500, #ff5e3a);' +
                'border-radius:50%;' +
                'box-shadow:0 0 10px rgba(255,149,0,0.5);';  // No animation
            overlay.appendChild(dot);
        }

        document.body.appendChild(overlay);
        console.log('[TV Player] Test mode overlay shown (STATIC MODE)');
    }

    function hideTestOverlay() {
        var overlay = document.getElementById('test-mode-overlay');
        if (overlay) {
            overlay.remove();
        }
        // Also remove the style element
        var style = document.getElementById('test-mode-styles');
        if (style) {
            style.remove();
        }
        console.log('[TV Player] Test mode overlay hidden');
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
        stopKeepAliveSystem();
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
        } catch (e) { }

        // Reload with cache bust
        setTimeout(function () {
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

    // ============================================
    // KEEP-ALIVE SYSTEM - Prevent Samsung TV Screensaver
    // ============================================

    /**
     * Start Keep-Alive System
     * Uses multiple techniques to prevent old Samsung TVs from entering screensaver:
     * 1. AJAX ping - Simulates network activity
     * 2. DOM manipulation - Moves invisible element to simulate DOM changes
     * 3. Pixel flicker - Changes a pixel color to simulate screen activity
     */
    function startKeepAliveSystem() {
        console.log('[TV Player] Starting keep-alive system to prevent screensaver');

        // Create invisible elements for keep-alive tricks
        createKeepAliveElements();

        // Start AJAX keep-alive ping
        keepAlivePing();
        state.keepAliveTimer = setInterval(keepAlivePing, CONFIG.KEEPALIVE_INTERVAL);

        // Start pixel flicker (changes an invisible pixel color)
        pixelFlicker();
        state.pixelFlickerTimer = setInterval(pixelFlicker, CONFIG.PIXEL_FLICKER_INTERVAL);

        // Start DOM movement (moves invisible element)
        moveDOMElement();
        state.domMoveTimer = setInterval(moveDOMElement, CONFIG.DOM_MOVE_INTERVAL);

        console.log('[TV Player] Keep-alive system started with intervals:',
            'AJAX:', CONFIG.KEEPALIVE_INTERVAL + 'ms,',
            'Pixel:', CONFIG.PIXEL_FLICKER_INTERVAL + 'ms,',
            'DOM:', CONFIG.DOM_MOVE_INTERVAL + 'ms');
    }

    /**
     * Create invisible elements used for keep-alive tricks
     * Compatible with older browsers
     */
    function createKeepAliveElements() {
        // Create invisible element for DOM manipulation
        var keepAliveEl = document.createElement('div');
        keepAliveEl.id = 'keep-alive-element';
        keepAliveEl.setAttribute('aria-hidden', 'true');
        keepAliveEl.style.cssText =
            'position:fixed;' +
            'top:-9999px;' +
            'left:-9999px;' +
            'width:1px;' +
            'height:1px;' +
            'opacity:0.01;' +
            'pointer-events:none;' +
            'z-index:-9999;' +
            'visibility:hidden;';
        document.body.appendChild(keepAliveEl);
        state.keepAliveElement = keepAliveEl;

        // Create pixel flicker element (1x1 pixel in corner)
        var pixelEl = document.createElement('div');
        pixelEl.id = 'pixel-flicker-element';
        pixelEl.setAttribute('aria-hidden', 'true');
        pixelEl.style.cssText =
            'position:fixed;' +
            'bottom:0;' +
            'right:0;' +
            'width:1px;' +
            'height:1px;' +
            'opacity:0.02;' +
            'pointer-events:none;' +
            'z-index:1;' +
            'background:#000;';
        document.body.appendChild(pixelEl);
        state.pixelFlickerElement = pixelEl;

        console.log('[TV Player] Keep-alive elements created');
    }

    /**
     * AJAX Keep-Alive Ping
     * Sends lightweight request to server to simulate network activity
     * Compatible with XMLHttpRequest for older browsers
     */
    function keepAlivePing() {
        state.keepAliveCount++;

        var xhr = new XMLHttpRequest();
        var basePath = getBasePath();
        // Use existing heartbeat endpoint with keep-alive flag
        var url = basePath + 'api/heartbeat.php?tv_id=' + state.tvId +
            '&folder=' + encodeURIComponent(state.tvFolder) +
            '&keepalive=1' +
            '&t=' + Date.now();

        xhr.open('GET', url, true);
        xhr.timeout = 10000;

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    // Success - log occasionally
                    if (state.keepAliveCount % 10 === 0) {
                        console.log('[TV Player] Keep-alive ping #' + state.keepAliveCount + ' OK');
                    }
                }
            }
        };

        xhr.onerror = function () {
            // Silent fail - just for keep-alive
        };

        try {
            xhr.send();
        } catch (e) {
            // Silent fail for older browsers
        }
    }

    /**
     * Pixel Flicker
     * Changes the color of an invisible 1x1 pixel to simulate screen activity
     * Some old TVs detect color changes to determine activity
     */
    function pixelFlicker() {
        if (!state.pixelFlickerElement) return;

        // Generate slightly different colors (almost black but not quite)
        var r = Math.floor(Math.random() * 3);  // 0-2
        var g = Math.floor(Math.random() * 3);  // 0-2
        var b = Math.floor(Math.random() * 3);  // 0-2

        var color = 'rgb(' + r + ',' + g + ',' + b + ')';
        state.pixelFlickerElement.style.background = color;

        // Also toggle a tiny bit of opacity for extra activity
        var currentOpacity = parseFloat(state.pixelFlickerElement.style.opacity) || 0.02;
        var newOpacity = currentOpacity === 0.02 ? 0.03 : 0.02;
        state.pixelFlickerElement.style.opacity = newOpacity;
    }

    /**
     * Move DOM Element
     * Moves an invisible element to simulate DOM activity
     * Some old TVs monitor DOM changes to detect activity
     */
    function moveDOMElement() {
        if (!state.keepAliveElement) return;

        // Move element to different position (still off-screen)
        var posX = -9999 + Math.floor(Math.random() * 100);
        var posY = -9999 + Math.floor(Math.random() * 100);

        state.keepAliveElement.style.top = posY + 'px';
        state.keepAliveElement.style.left = posX + 'px';

        // Also update content (timestamp) for extra DOM change
        state.keepAliveElement.innerHTML = Date.now().toString();

        // Force reflow/repaint (helps trigger activity on some TVs)
        void state.keepAliveElement.offsetHeight;
    }

    /**
     * Stop Keep-Alive System
     * Called when page is reloading
     */
    function stopKeepAliveSystem() {
        if (state.keepAliveTimer) {
            clearInterval(state.keepAliveTimer);
            state.keepAliveTimer = null;
        }
        if (state.pixelFlickerTimer) {
            clearInterval(state.pixelFlickerTimer);
            state.pixelFlickerTimer = null;
        }
        if (state.domMoveTimer) {
            clearInterval(state.domMoveTimer);
            state.domMoveTimer = null;
        }
        console.log('[TV Player] Keep-alive system stopped');
    }

    // Fullscreen toggle
    window.toggleFullscreen = function () {
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
        getState: function () { return state; },
        reload: reloadPage,
        loadContent: loadContent,
        // Keep-alive debugging
        keepAlivePing: keepAlivePing,
        stopKeepAlive: stopKeepAliveSystem,
        startKeepAlive: startKeepAliveSystem
    };

})();
