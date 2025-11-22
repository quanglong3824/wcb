<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý WCB - 7 TV System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f0f0;
            padding: 0;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1800px;
            margin: 0 auto;
        }
        
        header {
            background: #2c3e50;
            color: white;
            padding: 24px 32px;
            margin-bottom: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        header h1 {
            font-size: 28px;
            margin-bottom: 16px;
            font-weight: 600;
            letter-spacing: -0.5px;
        }
        
        .header-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        button, .btn {
            background: #34495e;
            border: none;
            padding: 12px 24px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            color: white;
            display: inline-block;
            font-family: inherit;
            border-radius: 4px;
        }
        
        button:hover, .btn:hover {
            background: #1abc9c;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        button:active, .btn:active {
            transform: translateY(0);
        }
        
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-danger {
            background: #e74c3c;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-primary {
            background: #3498db;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-success {
            background: #27ae60;
        }
        
        .btn-success:hover {
            background: #229954;
        }
        
        .section {
            background: white;
            padding: 32px;
            margin: 24px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .section h2 {
            font-size: 22px;
            margin-bottom: 24px;
            color: #2c3e50;
            font-weight: 600;
            letter-spacing: -0.3px;
        }
        
        .upload-form {
            display: grid;
            gap: 20px;
            max-width: 800px;
        }
        
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #34495e;
            font-size: 14px;
        }
        
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            font-size: 14px;
            font-family: inherit;
            border-radius: 4px;
            transition: all 0.2s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .tv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 24px;
            margin-top: 24px;
        }
        
        .tv-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 24px;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .tv-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }
        
        .tv-card h3 {
            font-size: 18px;
            margin-bottom: 12px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .tv-status {
            font-size: 13px;
            margin: 12px 0;
            padding: 8px 12px;
            background: #ecf0f1;
            border-radius: 4px;
            color: #34495e;
            font-weight: 500;
        }
        
        .tv-status.has-wcb {
            background: #d5f4e6;
            color: #27ae60;
        }
        
        .wcb-preview {
            margin: 16px 0;
            min-height: 120px;
        }
        
        .wcb-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
            margin-bottom: 10px;
            transition: all 0.2s;
        }
        
        .wcb-item:hover {
            background: #e9ecef;
        }
        
        .wcb-item img {
            width: 90px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .wcb-info {
            flex: 1;
        }
        
        .wcb-info strong {
            display: block;
            font-size: 14px;
            color: #2c3e50;
            margin-bottom: 4px;
        }
        
        .wcb-info small {
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .wcb-item button {
            padding: 8px 16px;
            font-size: 12px;
        }
        
        .tv-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 16px;
        }
        
        .tv-actions button {
            width: 100%;
        }
        
        .wcb-list {
            display: grid;
            gap: 20px;
            margin-top: 24px;
        }
        
        .wcb-card {
            display: flex;
            gap: 24px;
            padding: 24px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.2s;
        }
        
        .wcb-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .wcb-card img {
            width: 240px;
            height: 150px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .wcb-details {
            flex: 1;
        }
        
        .wcb-details h4 {
            font-size: 18px;
            margin-bottom: 12px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .wcb-details p {
            margin: 6px 0;
            font-size: 14px;
            color: #7f8c8d;
        }
        
        .wcb-details p strong {
            color: #34495e;
        }
        
        .tv-selector {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 16px;
        }
        
        .tv-checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: #ecf0f1;
            cursor: pointer;
            font-size: 13px;
            border-radius: 4px;
            transition: all 0.2s;
            border: 2px solid transparent;
            font-weight: 500;
        }
        
        .tv-checkbox-label:hover {
            background: #3498db;
            color: white;
        }
        
        .tv-checkbox-label.checked {
            background: #27ae60;
            color: white;
            border-color: #229954;
        }
        
        .tv-checkbox-label input {
            margin: 0;
            cursor: pointer;
        }
        
        .tv-checkbox-label.disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        
        .tv-checkbox-label.disabled:hover {
            background: #ecf0f1;
            color: inherit;
        }
        
        .alert {
            padding: 16px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d5f4e6;
            color: #27ae60;
            border-left: 4px solid #27ae60;
        }
        
        .alert-error {
            background: #fadbd8;
            color: #e74c3c;
            border-left: 4px solid #e74c3c;
        }
        
        .no-wcb {
            text-align: center;
            padding: 48px;
            color: #95a5a6;
            font-size: 15px;
        }
        
        @media (max-width: 768px) {
            .tv-grid {
                grid-template-columns: 1fr;
            }
            
            .wcb-card {
                flex-direction: column;
            }
            
            .wcb-card img {
                width: 100%;
            }
            
            .tv-actions {
                grid-template-columns: 1fr;
            }
            
            .section {
                margin: 16px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Hệ thống quản lý Welcome Board - 7 TV</h1>
            <div class="header-actions">
                <button class="btn-danger" onclick="turnOffAllWCBGlobal()">Tắt toàn bộ WCB</button>
                <button class="btn-primary" onclick="openAllTVs()">Mở tất cả TV</button>
                <button class="btn-success" onclick="location.reload()">Làm mới</button>
            </div>
        </header>

        <!-- Upload WCB -->
        <section class="section">
            <h2>📤 Upload Welcome Board</h2>
            <div id="uploadMessage"></div>
            <form id="uploadForm" class="upload-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Ngày sự kiện</label>
                    <input type="date" name="event_date" required>
                </div>
                <div class="form-group">
                    <label>Tiêu đề sự kiện</label>
                    <input type="text" name="event_title" placeholder="VD: Welcome Mr. John" required>
                </div>
                <div class="form-group">
                    <label>Hình ảnh WCB</label>
                    <input type="file" name="welcome_image" accept="image/*" required>
                </div>
                <button type="submit" class="btn-success">Upload WCB</button>
            </form>
        </section>

        <!-- 7 TV Cards -->
        <section class="section">
            <h2>📺 Quản lý 7 TV đang hoạt động</h2>
            <div id="tvGrid" class="tv-grid">
                <div class="no-wcb">Đang tải...</div>
            </div>
        </section>

        <!-- Chọn WCB và TV -->
        <section class="section">
            <h2>🎯 Chọn WCB và phân bổ cho TV</h2>
            <div id="wcbList" class="wcb-list">
                <div class="no-wcb">Đang tải danh sách WCB...</div>
            </div>
        </section>
    </div>

    <script>
        let tvs = [];
        let boards = [];
        let activeAssignments = [];

        // Load data
        async function loadData() {
            try {
                const [tvRes, boardRes, assignRes] = await Promise.all([
                    fetch('api.php?action=get_tvs'),
                    fetch('api.php?action=get_all_boards'),
                    fetch('api.php?action=get_all_active_assignments')
                ]);

                const tvData = await tvRes.json();
                const boardData = await boardRes.json();
                const assignData = await assignRes.json();

                if (tvData.success) tvs = tvData.tvs;
                if (boardData.success) boards = boardData.boards;
                if (assignData.success) activeAssignments = assignData.assignments;

                renderTVGrid();
                renderWCBList();
            } catch (error) {
                console.error('Load error:', error);
            }
        }

        // Render 7 TV cards
        function renderTVGrid() {
            const container = document.getElementById('tvGrid');
            if (tvs.length === 0) {
                container.innerHTML = '<div class="no-wcb">Không có TV nào</div>';
                return;
            }

            let html = '';
            tvs.forEach(tv => {
                const tvAssignments = activeAssignments.filter(a => a.tv_id == tv.id);
                const maxWCB = tv.max_wcb || (tv.code === 'BASEMENT_TV1' ? 3 : 1);
                const tvPath = tv.code.toLowerCase().replace('_', '/').replace('tv', '/tv');
                const hasWCB = tvAssignments.length > 0;

                html += `
                    <div class="tv-card">
                        <h3>${tv.name}</h3>
                        <div class="tv-status ${hasWCB ? 'has-wcb' : ''}">
                            ${hasWCB ? '🟢' : '⚪'} ${tvAssignments.length}/${maxWCB} WCB đang phát
                        </div>
                        
                        <div class="wcb-preview">
                            ${tvAssignments.length === 0 ? 
                                '<div class="no-wcb">Chưa có WCB nào</div>' :
                                tvAssignments.map(a => `
                                    <div class="wcb-item">
                                        <img src="${a.filepath}" alt="${a.event_title}">
                                        <div class="wcb-info">
                                            <strong>${a.event_title}</strong>
                                            <small>${a.event_date}</small>
                                        </div>
                                        <button onclick="closeWCB(${tv.id}, '${a.board_id}')" class="btn-danger">Đóng</button>
                                    </div>
                                `).join('')
                            }
                        </div>
                        
                        <div class="tv-actions">
                            <button class="btn-primary" onclick="window.open('${tvPath}', '_blank')">Mở TV</button>
                            ${tvAssignments.length > 0 ? 
                                `<button class="btn-danger" onclick="closeAllWCBOnTV(${tv.id})">Đóng WCB</button>` : 
                                '<button disabled>Đóng WCB</button>'}
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        // Render WCB list with TV selector
        function renderWCBList() {
            const container = document.getElementById('wcbList');
            if (boards.length === 0) {
                container.innerHTML = '<div class="no-wcb">Chưa có WCB nào. Vui lòng upload WCB trước.</div>';
                return;
            }

            let html = '';
            boards.forEach(board => {
                const boardAssignments = activeAssignments.filter(a => a.board_id == board.id);
                
                html += `
                    <div class="wcb-card">
                        <img src="${board.filepath}" alt="${board.event_title}">
                        <div class="wcb-details">
                            <h4>${board.event_title}</h4>
                            <p><strong>Ngày:</strong> ${board.event_date}</p>
                            <p><strong>ID:</strong> ${board.id}</p>
                            <p><strong>Đang phát trên:</strong> ${boardAssignments.length > 0 ? 
                                boardAssignments.map(a => a.tv_name).join(', ') : 
                                '<span style="color: #95a5a6;">Chưa phát trên TV nào</span>'}</p>
                            
                            <div class="tv-selector">
                                ${tvs.map(tv => {
                                    const maxWCB = tv.max_wcb || (tv.code === 'BASEMENT_TV1' ? 3 : 1);
                                    const currentCount = activeAssignments.filter(a => a.tv_id == tv.id).length;
                                    const isAssigned = boardAssignments.some(a => a.tv_id == tv.id);
                                    const isFull = currentCount >= maxWCB && !isAssigned;
                                    
                                    return `
                                        <label class="tv-checkbox-label ${isFull ? 'disabled' : ''} ${isAssigned ? 'checked' : ''}">
                                            <input type="checkbox" 
                                                   value="${tv.id}" 
                                                   ${isAssigned ? 'checked' : ''}
                                                   ${isFull ? 'disabled' : ''}
                                                   onchange="toggleAssignment('${board.id}', ${tv.id}, this.checked)">
                                            ${tv.name} (${currentCount}/${maxWCB})
                                        </label>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        // Toggle assignment
        async function toggleAssignment(boardId, tvId, checked) {
            const action = checked ? 'assign_to_tv' : 'unassign_from_tv';
            const formData = new FormData();
            formData.append('board_id', boardId);
            formData.append('tv_id', tvId);

            try {
                const response = await fetch(`api.php?action=${action}`, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    loadData();
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể thực hiện'));
                    loadData();
                }
            } catch (error) {
                alert('Lỗi kết nối');
                loadData();
            }
        }

        // Close specific WCB on TV
        async function closeWCB(tvId, boardId) {
            if (!confirm('Đóng WCB này?')) return;
            
            const formData = new FormData();
            formData.append('board_id', boardId);
            formData.append('tv_id', tvId);
            
            try {
                const response = await fetch('api.php?action=unassign_from_tv', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    loadData();
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể đóng'));
                }
            } catch (error) {
                alert('Lỗi kết nối');
            }
        }

        // Close all WCB on specific TV
        async function closeAllWCBOnTV(tvId) {
            if (!confirm('Đóng toàn bộ WCB trên TV này?')) return;
            
            const tvAssignments = activeAssignments.filter(a => a.tv_id == tvId);
            let successCount = 0;
            
            for (const a of tvAssignments) {
                const formData = new FormData();
                formData.append('board_id', a.board_id);
                formData.append('tv_id', tvId);
                
                try {
                    const response = await fetch('api.php?action=unassign_from_tv', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) successCount++;
                } catch (error) {
                    console.error('Error:', error);
                }
            }
            
            if (successCount > 0) {
                loadData();
            } else {
                alert('Có lỗi xảy ra');
            }
        }

        // Turn off all WCB globally
        async function turnOffAllWCBGlobal() {
            if (!confirm('Tắt TOÀN BỘ WCB trên TẤT CẢ TV?')) return;
            
            let successCount = 0;
            
            for (const a of activeAssignments) {
                const formData = new FormData();
                formData.append('board_id', a.board_id);
                formData.append('tv_id', a.tv_id);
                
                try {
                    const response = await fetch('api.php?action=unassign_from_tv', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) successCount++;
                } catch (error) {
                    console.error('Error:', error);
                }
            }
            
            alert(`Đã tắt ${successCount}/${activeAssignments.length} WCB`);
            loadData();
        }

        // Open all TVs
        function openAllTVs() {
            const paths = [
                'basement/tv1',
                'fo/tv1',
                'fo/tv2',
                'restaurant/tv1',
                'chrysan/tv1',
                'lotus/tv1',
                'jasmin/tv1'
            ];
            paths.forEach(path => {
                window.open(path, '_blank');
            });
        }

        // Upload form
        document.getElementById('uploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const messageDiv = document.getElementById('uploadMessage');
            
            messageDiv.innerHTML = '<div class="alert alert-success">Đang upload...</div>';
            
            try {
                const response = await fetch('upload.php', {
                    method: 'POST',
                    body: formData
                });
                
                const text = await response.text();
                
                if (text.includes('success') || text.includes('thành công')) {
                    messageDiv.innerHTML = '<div class="alert alert-success">Upload thành công!</div>';
                    e.target.reset();
                    setTimeout(() => {
                        messageDiv.innerHTML = '';
                        loadData();
                    }, 2000);
                } else {
                    messageDiv.innerHTML = '<div class="alert alert-error">Lỗi upload</div>';
                }
            } catch (error) {
                messageDiv.innerHTML = '<div class="alert alert-error">Lỗi kết nối</div>';
            }
        });

        // Load on start
        loadData();
        
        // Auto refresh every 10 seconds
        setInterval(loadData, 10000);
    </script>
</body>
</html>
