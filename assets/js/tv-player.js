    
                    console.error('[TV Player] Error checking reload signal:', e);
                }
            }
        };
        
        xhr.onerror = function() {
            console.error('[TV Player] Network error checking reload signal');
        };
        
        xhr.send();
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
        if (state.forceReloadTimer) clearInterval(state.forceReloadTimer);
        
        // Show reload indicator for old TVs
        try {
            var indicator = document.createElement('div');
            indicator.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.9);z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;color:white;';
            indicator.innerHTML = '<div style="font-size:3em;margin-bottom:20px;">⟳</div><div style="font-size:1.5em;">Đang tải lại...</div>';
            document.body.appendChild(indicator);
        } catch (e) {}
        
        // Small delay to show indicator, then reload
        setTimeout(function() {
            // Force reload with cache bust
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
};
