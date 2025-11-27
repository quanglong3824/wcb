/**
 * TV Reload Signal Checker - Database Polling
 * Kiểm tra reload signal từ database mỗi 3 giây
 * Tương thích với mọi trình duyệt cũ
 */

(function() {
    // Lấy TV ID từ URL hoặc từ biến global
    const TV_ID = window.TV_ID || getTVIdFromPath();
    
    if (!TV_ID) {
        console.warn('TV ID not found, reload checker disabled');
        return;
    }
    
    console.log('TV Reload Checker started for TV ID:', TV_ID);
    
    // Check for reload signal every 3 seconds
    setInterval(checkReloadSignal, 3000);
    
    function getTVIdFromPath() {
        // Lấy TV ID từ đường dẫn
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
    
    function checkReloadSignal() {
        // Tính đường dẫn API tương đối
        const apiPath = TV_ID <= 5 ? '../api/check-reload-signal.php' : '../../api/check-reload-signal.php';
        
        fetch(apiPath + '?tv_id=' + TV_ID + '&t=' + Date.now())
            .then(response => response.json())
            .then(data => {
                if (data.success && data.timestamp) {
                    const lastCheck = localStorage.getItem('lastReloadCheck_' + TV_ID) || '0';
                    
                    if (data.timestamp > lastCheck) {
                        console.log('Reload signal detected! Timestamp:', data.timestamp);
                        localStorage.setItem('lastReloadCheck_' + TV_ID, data.timestamp);
                        
                        // Show reload notification
                        showReloadNotification();
                        
                        // Reload after 1.5 seconds
                        setTimeout(() => {
                            location.reload(true);
                        }, 1500);
                    }
                }
            })
            .catch(error => {
                console.error('Error checking reload signal:', error);
            });
    }
    
    function showReloadNotification() {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 25px 50px;
            border-radius: 16px;
            font-size: 1.5em;
            font-weight: 700;
            z-index: 99999;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4);
            text-align: center;
        `;
        notification.innerHTML = `
            <i class="fas fa-sync-alt fa-spin" style="margin-right: 15px; font-size: 1.3em;"></i>
            <div>Đang cập nhật nội dung mới...</div>
        `;
        document.body.appendChild(notification);
    }
})();
