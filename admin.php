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
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1600px;
            margin: 0 auto;
        }
        
        header {
            background: white;
            padding: 20px 30px;
            margin-bottom: 20px;
            border: 2px solid #000;
        }
        
        header h1 {
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        button, .btn {
            background: white;
            border: 2px solid #000;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            color: #000;
            display: inline-block;
            font-family: inherit;
        }
        
        button:hover, .btn:hover {
            background: #000;
            color: white;
        }
        
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .section {
            background: white;
            border: 2px solid #000;
            padding: 30px;
            margin-bottom: 20px;
        }
        
        .section h2 {
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }
        
        .upload-form {
            display: grid;
            gap: 15px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #000;
            font-size: 14px;
            font-family: inherit;
        }
        
        .form-group input:focus {
            outline: none;
            background: #f0f0f0;
        }
        
        .tv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .tv-card {
            background: white;
            border: 2px solid #000;
            padding: 20px;
        }
        
        .tv-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .tv-status {
            font-size: 14px;
            margin: 10px 0;
            padding: 5px 0;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }
        
        .wcb-preview {
            margin: 15px 0;
            min-height: 100px;
        }
        
        .wcb-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 8px;
        }
        
        .wcb-item img {
            width: 80px;
            height: 50px;
            object-fit: cover;
            border: 1px solid #000;
        }
        
        .wcb-info {
            flex: 1;
        }
        
        .wcb-info strong {
            display: block;
            font-size: 14px;
        }
        
        .wcb-info small {
            font-size: 12px;
            color: #666;
        }
        
        .tv-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .tv-actions button {
            flex: 1;
        }
        
        .btn-danger {
            background: white;
            border: 2px solid #000;
        }
        
        .btn-danger:hover {
            background: #000;
            color: white;
        }
        
        .wcb-list {
            display: grid;
            gap: 15px;
            margin-top: 20px;
        }
        
        .wcb-card {
            display: flex;
            gap: 20px;
            padding: 20px;
            border: 2px solid #000;
            background: white;
        }
        
        .wcb-card img {
            width: 200px;
            height: 120px;
            object-fit: cover;
            border: 1px solid #000;
        }
        
        .wcb-details {
            flex: 1;
        }
        
        .wcb-details h4 {
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .wcb-details p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .tv-selector {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        
        .tv-checkbox-label {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            border: 2px solid #000;
            background: white;
            cursor: pointer;
            font-size: 14px;
        }
        
        .tv-checkbox-label:hover {
            background: #000;
            color: white;
        }
        
        .tv-checkbox-label input {
            margin: 0;
        }
        
        .tv-checkbox-label.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .tv-checkbox-label.disabled:hover {
            background: white;
            color: #000;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 2px solid #000;
        }
        
        .alert-success {
            background: #d4edda;
        }
        
        .alert-error {
            background: #f8d7da;
        }
        
        .no-wcb {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Hệ thống quản lý WCB - 7 TV</h1>
            <div class="header-actions">
                <button onclick="turnOffAllWCBGlobal()">Tắt toàn bộ WCB</button>
                <button onclick="openAllTVs()">Mở tất cả TV</button>
                <button onclick="location.reload()">Làm mới</button>
            </div>
        </header>

        <!-- Upload WCB -->
        <section class="section">
            <h2>Upload Welcome Board</h2>
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
                <button type="submit">Upload WCB</button>
            </form>
        </section>

        <!-- 7 TV Cards -->
        <section class="section">
            <h2>Quản lý 7 TV đang hoạt động</h2>
            <div id="tvGrid" class="tv-grid">
                <div class="no-wcb">Đang tải...</div>
            </div>
        </section>

        <!-- Chọn WCB và TV -->
        <section class="section">
            <h2>Chọn WCB và phân bổ cho TV</h2>
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

                html += `
                    <div class="tv-card">
                        <h3>${tv.name}</h3>
                        <div class="tv-status">
                            Trạng thái: ${tvAssignments.length}/${maxWCB} WCB
                        </div>
                        
                        <div class="wcb-preview">
                            ${tvAssignments.length === 0 ? 
                                '<div class="no-wcb">Không có WCB</div>' :
                                tvAssignments.map(a => `
                                    <div class="wcb-item">
                                        <img src="${a.filepath}" alt="${a.event_title}">
                                        <div class="wcb-info">
                                            <strong>${a.event_title}</strong>
                                            <small>${a.event_date}</small>
                                        </div>
                                        <button onclick="closeWCB(${tv.id}, ${a.board_id})" class="btn-danger">Đóng WCB</button>
                                    </div>
                                `).join('')
                            }
                        </div>
                        
                        <div class="tv-actions">
                            <button onclick="window.open('${tvPath}', '_blank')">Mở TV</button>
                            ${tvAssignments.length > 0 ? 
                                `<button onclick="closeAllWCBOnTV(${tv.id})" class="btn-danger">Đóng toàn bộ WCB</button>` : 
                                ''}
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
                container.innerHTML = '<div class="no-wcb">Chưa có WCB nào. Vui lòng upload WCB.</div>';
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
                            <p>Ngày: ${board.event_date}</p>
                            <p>ID: ${board.id}</p>
                            <p>Đang chiếu trên: ${boardAssignments.length > 0 ? 
                                boardAssignments.map(a => a.tv_name).join(', ') : 
                                'Chưa có TV nào'}</p>
                            
                            <div class="tv-selector">
                                ${tvs.map(tv => {
                                    const maxWCB = tv.max_wcb || (tv.code === 'BASEMENT_TV1' ? 3 : 1);
                                    const currentCount = activeAssignments.filter(a => a.tv_id == tv.id).length;
                                    const isAssigned = boardAssignments.some(a => a.tv_id == tv.id);
                                    const isFull = currentCount >= maxWCB && !isAssigned;
                                    
                                    return `
                                        <label class="tv-checkbox-label ${isFull ? 'disabled' : ''}" style="${isFull ? 'opacity: 0.5; cursor: not-allowed;' : ''}">
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
