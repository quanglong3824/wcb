/**
 * TV Standby Mode
 * Hiển thị logo nhấp nháy khi TV offline
 */

(function() {
    const TV_ID = window.TV_ID || getTVIdFromPath();
    
    if (!TV_ID) {
        console.warn('TV ID not found, standby mode disabled');
        return;
    }
    
    console.log('TV Standby Mode initialized for TV ID:', TV_ID);
    
    // Check TV status every 5 seconds
    setInterval(checkTVStatus, 5000);
    
    // Check immediately on load
    checkTVStatus();
    
    function getTVIdFromPath() {
        const path = window.location.pathname;
        
        if (path.includes('basement')) return 1;
        if (path.includes('chrysan')) return 2;
        if (path.includes('jasmine')) return 3;
        if (path.includes('lotus')) return 4;
        if (path.includes('restaurant')) return 5;
        if (path.includes('fo/tv1')) return 6;
        if (path.includes('fo/tv2')) return 7;
        
        return null;
    }
    
    function checkTVStatus() {
        const apiPath = TV_ID <= 5 ? '../api/get-tv-status.php' : '../../api/get-tv-status.php';
        
        fetch(apiPath + '?tv_id=' + TV_ID + '&t=' + Date.now())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.status === 'offline') {
                        showStandbyMode();
                    } else {
                        hideStandbyMode();
                    }
                }
            })
            .catch(error => {
                console.error('Error checking TV status:', error);
            });
    }
    
    function showStandbyMode() {
        // Check if standby overlay already exists
        if (document.getElementById('standbyOverlay')) {
            return;
        }
        
        // Create standby overlay
        const overlay = document.createElement('div');
        overlay.id = 'standbyOverlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #000000;
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 1s ease;
        `;
        
        // Create logo container
        const logoContainer = document.createElement('div');
        logoContainer.style.cssText = `
            text-align: center;
        `;
        
        // Determine logo path based on TV location
        const logoPath = TV_ID <= 5 ? '../assets/img/logo-dark-ui.png' : '../../assets/img/logo-dark-ui.png';
        
        // Logo only - clean and minimal, no animation
        logoContainer.innerHTML = `
            <img src="${logoPath}" 
                 alt="Quang Long Hotel Logo" 
                 style="
                     max-width: 500px;
                     height: auto;
                     filter: drop-shadow(0 0 50px rgba(212, 175, 55, 0.7));
                 "
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <div style="
                display: none;
                font-size: 8em;
                color: #d4af37;
                filter: drop-shadow(0 0 40px rgba(212, 175, 55, 0.6));
            ">
                <i class="fas fa-tv"></i>
            </div>
        `;
        
        overlay.appendChild(logoContainer);
        
        // Add fullscreen button
        const fullscreenBtn = document.createElement('button');
        fullscreenBtn.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(212, 175, 55, 0.9);
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1.2em;
            z-index: 1000000;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        `;
        fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
        fullscreenBtn.title = 'Toàn màn hình';
        fullscreenBtn.onclick = function() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log('Fullscreen error:', err);
                });
                this.innerHTML = '<i class="fas fa-compress"></i>';
            } else {
                document.exitFullscreen();
                this.innerHTML = '<i class="fas fa-expand"></i>';
            }
        };
        
        fullscreenBtn.onmouseover = function() {
            this.style.background = 'rgba(212, 175, 55, 1)';
            this.style.transform = 'scale(1.1)';
        };
        
        fullscreenBtn.onmouseout = function() {
            this.style.background = 'rgba(212, 175, 55, 0.9)';
            this.style.transform = 'scale(1)';
        };
        
        overlay.appendChild(fullscreenBtn);
        document.body.appendChild(overlay);
        
        // Add CSS animations (only fadeIn, no pulse)
        if (!document.getElementById('standbyStyles')) {
            const style = document.createElement('style');
            style.id = 'standbyStyles';
            style.textContent = `
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                    }
                    to {
                        opacity: 1;
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        console.log('Standby mode activated');
    }
    
    function hideStandbyMode() {
        const overlay = document.getElementById('standbyOverlay');
        if (overlay) {
            overlay.style.animation = 'fadeOut 1s ease';
            overlay.style.opacity = '0';
            
            setTimeout(() => {
                overlay.remove();
                console.log('Standby mode deactivated');
            }, 1000);
        }
    }
})();
