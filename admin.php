<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Multi WCB Management</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Modern Clean Design */
        :root {
            --primary: #4a90e2;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --dark: #2c3e50;
            --light: #f8f9fb;
            --border: #e8eef5;
            --shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        header {
            background: white;
            border-radius: 15px;
            padding: 25px 30px;
            margin-bottom: 25px;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        header h1 {
            margin: 0;
            color: var(--dark);
            font-size: 1.8rem;
        }
        
        nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .nav-btn {
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.4);
        }
        
        .panel {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: var(--shadow);
        }
        
        .panel h2 {
            margin: 0 0 20px 0;
            color: var(--dark);
            font-size: 1.5rem;
            border-bottom: 3px solid var(--primary);
            padding-bottom: 10px;
        }
        
        /* Multi Upload Form */
        .upload-section {
            background: var(--light);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .wcb-upload-item {
            background: white;
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.2s;
        }
        
        .wcb-upload-item:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }
        
        .wcb-upload-item h4 {
            margin: 0 0 15px 0;
            color: var(--primary);
            font-size: 1.1rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: var(--shadow);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }
        
        .btn-add-wcb {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .btn-remove-wcb {
            background: var(--danger);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        /* TV Grid */
        .tv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .tv-card {
            background: white;
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .tv-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), #764ba2);
        }
        
        .tv-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .tv-card.full {
            opacity: 0.6;
            border-color: var(--warning);
        }
        
        .tv-card.full::before {
            background: var(--warning);
        }
        
        .tv-card h3 {
            margin: 0 0 10px 0;
            color: var(--dark);
            font-size: 1.1rem;
        }
        
        .tv-code {
            background: var(--primary);
            color: white;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .tv-status {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 10px 0;
            font-size: 0.95rem;
        }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-badge.active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.full {
            background: #fff3cd;
            color: #856404;
        }
        
        .tv-link {
            display: block;
            background: var(--success);
            color: white;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 12px;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .tv-link:hover {
            background: #218838;
            transform: scale(1.02);
        }
        
        /* Department Section */
        .department-section {
            margin-bottom: 30px;
        }
        
        .department-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        
        .department-header h3 {
            margin: 0 0 5px 0;
            font-size: 1.3rem;
        }
        
        .department-header p {
            margin: 0;
            opacity: 0.9;
        }
        
        /* Boards List */
        .board-item {
            background: var(--light);
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.2s;
        }
        
        .board-item:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }
        
        .board-info {
            display: flex;
            gap: 25px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .board-image img {
            width: 200px;
            height: auto;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }
        
        .board-details h4 {
            margin: 0 0 12px 0;
            color: var(--dark);
            font-size: 1.2rem;
        }
        
        .board-details p {
            margin: 8px 0;
            color: #666;
        }
        
        .assignment-tag {
            display: inline-block;
            background: #e7f3ff;
            color: #0066cc;
            padding: 6px 12px;
            border-radius: 6px;
            margin: 4px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .assignment-controls {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .btn-assign, .btn-unassign {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-assign {
            background: var(--success);
            color: white;
        }
        
        .btn-assign:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .btn-unassign {
            background: var(--danger);
            color: white;
        }
        
        .btn-unassign:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        /* Loading & Messages */
        .loading {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .tv-grid {
                grid-template-columns: 1fr;
            }
            
            .board-info {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🎯 Multi WCB Management</h1>
            <nav>
                <a href="index.php" class="nav-btn">🏠 Trang chủ</a>
                <a href="#" onclick="openAllTVs()" class="nav-btn">📺 Mở tất cả TV</a>
            </nav>
        </header>

        <!-- Upload Multi WCB -->
        <section class="panel">
            <h2>📤 Upload Welcome Boards (Tối đa 5 WCB)</h2>
            <div id="uploadMessage"></div>
            <div class="upload-section">
                <button type="button" class="btn-add-wcb" onclick="addWCBUploadItem()">➕ Thêm WCB</button>
                <form id="multiUploadForm" enctype="multipart/form-data">
                    <div id="wcbUploadContainer">
                        <!-- WCB upload items will be added here -->
                    </div>
                    <button type="submit" class="btn-primary">🚀 Upload tất cả WCB</button>
                </form>
            </div>
        </section>

        <!-- Quick Assignment: Chọn WCB và TV -->
        <section class="panel">
            <h2>🎯 Quick Assignment: Chọn WCB → Chọn TV → Assign</h2>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 20px;">
                <!-- WCB Selection -->
                <div style="background: var(--light); padding: 20px; border-radius: 10px;">
                    <h3 style="margin: 0 0 15px 0; color: var(--dark);">📋 Chọn WCB (<span id="wcbSelectedCount">0</span>/∞)</h3>
                    <div id="wcbChecklistContainer" style="max-height: 400px; overflow-y: auto;">
                        <p style="color: #999;">Đang tải danh sách WCB...</p>
                    </div>
                </div>

                <!-- TV Selection -->
                <div style="background: var(--light); padding: 20px; border-radius: 10px;">
                    <h3 style="margin: 0 0 15px 0; color: var(--dark);">📺 Chọn TV (<span id="tvSelectedCount">0</span>/6)</h3>
                    <div id="tvChecklistContainer" style="max-height: 400px; overflow-y: auto;">
                        <p style="color: #999;">Đang tải danh sách TV...</p>
                    </div>
                </div>
            </div>

            <!-- Assignment Summary & Action -->
            <div style="background: #e7f3ff; padding: 20px; border-radius: 10px; border-left: 4px solid var(--primary);">
                <h4 style="margin: 0 0 10px 0;">📊 Tóm tắt Assignment</h4>
                <div id="assignmentSummary" style="margin-bottom: 15px; color: #666;">
                    Chọn WCB và TV để xem tóm tắt
                </div>
                <button onclick="performQuickAssignment()" class="btn-primary" style="width: 100%;">
                    ✓ Assign các WCB đã chọn cho các TV đã chọn
                </button>
            </div>
        </section>

        <!-- TV List by Department -->
        <section class="panel">
            <h2>📺 Danh sách TV (Chỉ hiển thị TV đang hoạt động)</h2>
            <div id="tvList" class="loading">Đang tải...</div>
        </section>

        <!-- Boards Management -->
    </div>

    <script>
        let departments = [];
        let tvs = [];
        let boards = [];
        let wcbCount = 0;
        const MAX_WCB = 5;

        // Load data
        async function loadData() {
            try {
                // Load departments
                const deptResponse = await fetch('api.php?action=get_departments');
                const deptData = await deptResponse.json();
                if (deptData.success) departments = deptData.departments;

                // Load TVs (chỉ active)
                const tvResponse = await fetch('api.php?action=get_tvs');
                const tvData = await tvResponse.json();
                if (tvData.success) tvs = tvData.tvs;

                // Load boards
                const boardResponse = await fetch('api.php?action=get_all_boards');
                const boardData = await boardResponse.json();
                if (boardData.success) boards = boardData.boards;

                renderTVList();
                renderBoardsList();
                loadBoardAssignments();
                
                // Render Quick Assignment checklists
                renderWCBChecklist();
                renderTVChecklist();
                
                // Add first WCB upload item
                if (wcbCount === 0) addWCBUploadItem();
            } catch (error) {
                console.error('Load data error:', error);
            }
        }

        // Add WCB upload item
        function addWCBUploadItem() {
            if (wcbCount >= MAX_WCB) {
                alert('Chỉ được upload tối đa 5 WCB cùng lúc!');
                return;
            }
            
            wcbCount++;
            const container = document.getElementById('wcbUploadContainer');
            const item = document.createElement('div');
            item.className = 'wcb-upload-item';
            item.id = `wcb-item-${wcbCount}`;
            item.innerHTML = `
                <h4>WCB #${wcbCount}</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày sự kiện</label>
                        <input type="date" name="event_dates[]" required>
                    </div>
                    <div class="form-group">
                        <label>Tiêu đề sự kiện</label>
                        <input type="text" name="event_titles[]" placeholder="VD: Welcome Mr. John" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Hình ảnh</label>
                    <input type="file" name="wcb_files[]" accept="image/*" required>
                </div>
                ${wcbCount > 1 ? `<button type="button" class="btn-remove-wcb" onclick="removeWCBItem(${wcbCount})">🗑️ Xóa WCB này</button>` : ''}
            `;
            container.appendChild(item);
        }

        // Remove WCB upload item
        function removeWCBItem(id) {
            const item = document.getElementById(`wcb-item-${id}`);
            if (item) {
                item.remove();
                wcbCount--;
                // Renumber remaining items
                const items = document.querySelectorAll('.wcb-upload-item');
                items.forEach((item, index) => {
                    item.querySelector('h4').textContent = `WCB #${index + 1}`;
                });
            }
        }

        // Handle multi upload
        document.getElementById('multiUploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const messageDiv = document.getElementById('uploadMessage');
            
            messageDiv.innerHTML = '<div class="alert alert-success">⏳ Đang upload...</div>';
            
            try {
                const response = await fetch('upload_multi.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    messageDiv.innerHTML = `<div class="alert alert-success">✅ ${data.message}</div>`;
                    e.target.reset();
                    wcbCount = 0;
                    document.getElementById('wcbUploadContainer').innerHTML = '';
                    addWCBUploadItem();
                    loadData();
                } else {
                    messageDiv.innerHTML = `<div class="alert alert-error">❌ ${data.message}</div>`;
                }
            } catch (error) {
                messageDiv.innerHTML = '<div class="alert alert-error">❌ Lỗi kết nối</div>';
            }
        });

        // Render TV list
        function renderTVList() {
            const container = document.getElementById('tvList');
            let html = '';

            departments.forEach(dept => {
                const deptTVs = tvs.filter(tv => tv.department_id == dept.id);
                if (deptTVs.length === 0) return;

                html += `
                    <div class="department-section">
                        <div class="department-header">
                            <h3>${dept.name} (${dept.code})</h3>
                            <p>${dept.description || ''}</p>
                        </div>
                        <div class="tv-grid">
                `;

                deptTVs.forEach(tv => {
                    const tvPath = tv.code.toLowerCase().replace('_', '/').replace('tv', '/tv');
                    const isFull = tv.board_count >= 3;
                    html += `
                        <div class="tv-card ${isFull ? 'full' : ''}">
                            <span class="tv-code">${tv.code}</span>
                            <h3>${tv.name}</h3>
                            <p>📍 ${tv.location || 'N/A'}</p>
                            <div class="tv-status">
                                <span class="status-badge ${isFull ? 'full' : 'active'}">
                                    ${tv.board_count}/3 WCB
                                </span>
                                ${isFull ? '<span style="color: #856404;">⚠️ Đã đủ</span>' : '<span style="color: #28a745;">✓ Có thể assign</span>'}
                            </div>
                            <a href="${tvPath}" target="_blank" class="tv-link">
                                🖥️ Mở màn hình TV
                            </a>
                        </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html || '<p>Không có TV nào đang hoạt động</p>';
        }

        // Render boards list
        function renderBoardsList() {
            const container = document.getElementById('boardsList');
            if (boards.length === 0) {
                container.innerHTML = '<p>Chưa có board nào</p>';
                return;
            }

            let html = '<div class="boards-list">';
            boards.forEach(board => {
                html += `
                    <div class="board-item">
                        <div class="board-info">
                            <div class="board-image">
                                <img src="${board.filepath}" alt="${board.event_title}">
                            </div>
                            <div class="board-details">
                                <h4>${board.event_title}</h4>
                                <p>📅 ${board.event_date}</p>
                                <p>🆔 ${board.id}</p>
                            </div>
                        </div>
                        <div class="board-assignment" id="assignment-${board.id}">
                            <strong>Đang hiển thị trên:</strong>
                            <div>Đang tải...</div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        }

        // Load board assignments
        async function loadBoardAssignments() {
            for (const board of boards) {
                try {
                    const response = await fetch(`api.php?action=get_board_assignments&board_id=${board.id}`);
                    const data = await response.json();
                    if (data.success) {
                        renderBoardAssignment(board.id, data.assignments);
                    }
                } catch (error) {
                    console.error('Load assignment error:', error);
                }
            }
        }

        // Render board assignment (simplified - chỉ hiển thị TV nào đang hoạt động)
        function renderBoardAssignment(boardId, assignments) {
            const container = document.getElementById(`assignment-${boardId}`);
            if (!container) return;

            let html = '<div style="padding: 15px; background: var(--light); border-radius: 8px;">';
            html += '<strong style="color: var(--dark); font-size: 1rem;">📺 TV đang chiếu WCB này:</strong>';
            html += '<div style="margin-top: 12px;">';
            
            if (assignments.length === 0) {
                html += '<p style="color: #999; margin: 0;">Chưa có TV nào đang chiếu WCB này</p>';
            } else {
                // Group by department
                const byDept = {};
                assignments.forEach(a => {
                    if (!byDept[a.department_name]) {
                        byDept[a.department_name] = [];
                    }
                    byDept[a.department_name].push(a);
                });

                Object.keys(byDept).forEach(deptName => {
                    html += `<div style="margin-bottom: 12px;">`;
                    html += `<div style="font-weight: 600; color: #667eea; margin-bottom: 6px;">${deptName}</div>`;
                    byDept[deptName].forEach(a => {
                        html += `
                            <div style="display: inline-block; background: #d4edda; color: #155724; padding: 6px 12px; border-radius: 6px; margin: 4px; font-size: 0.9rem;">
                                ✓ ${a.tv_name}
                            </div>
                        `;
                    });
                    html += `</div>`;
                });
            }
            
            html += '</div>';
            html += '</div>';

            container.innerHTML = html;
        }


        // Assign to department
        async function assignToDepartment(boardId, deptId, deptName) {
            if (!confirm(`Assign board này cho tất cả TV trong ${deptName}?`)) return;
            
            const formData = new FormData();
            formData.append('board_id', boardId);
            formData.append('department_id', deptId);
            
            try {
                const response = await fetch('api.php?action=assign_to_department', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    alert(`✅ Đã assign cho ${data.assigned_count} TV trong ${deptName}`);
                    loadData();
                } else {
                    alert('❌ Lỗi: ' + (data.message || 'Không thể assign'));
                }
            } catch (error) {
                alert('❌ Lỗi kết nối');
            }
        }

        // Unassign from department
        async function unassignFromDepartment(boardId, deptId, deptName) {
            if (!confirm(`Gỡ board này khỏi tất cả TV trong ${deptName}?`)) return;
            
            const formData = new FormData();
            formData.append('board_id', boardId);
            formData.append('department_id', deptId);
            
            try {
                const response = await fetch('api.php?action=unassign_from_department', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    alert(`✅ Đã gỡ khỏi ${data.unassigned_count} TV trong ${deptName}`);
                    loadData();
                } else {
                    alert('❌ Lỗi: ' + (data.message || 'Không thể unassign'));
                }
            } catch (error) {
                alert('❌ Lỗi kết nối');
            }
        }

        // Assign selected TVs
        async function assignSelectedTVs(boardId) {
            const checkboxes = document.querySelectorAll(`.tv-checkbox-${boardId}:checked:not(:disabled)`);
            if (checkboxes.length === 0) {
                alert('⚠️ Vui lòng chọn ít nhất một TV');
                return;
            }

            const tvIds = Array.from(checkboxes).map(cb => cb.value);
            if (!confirm(`Assign board này cho ${tvIds.length} TV đã chọn?`)) return;

            const formData = new FormData();
            formData.append('board_ids', JSON.stringify([boardId]));
            formData.append('tv_ids', JSON.stringify(tvIds));

            try {
                const response = await fetch('api.php?action=batch_assign', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    alert(`✅ Đã assign cho ${data.success_count} TV`);
                    loadData();
                } else {
                    alert('❌ Lỗi: ' + (data.message || 'Không thể assign'));
                }
            } catch (error) {
                alert('❌ Lỗi kết nối');
            }
        }

        // Unassign selected TVs
        async function unassignSelectedTVs(boardId) {
            const checkboxes = document.querySelectorAll(`.tv-checkbox-${boardId}:checked`);
            if (checkboxes.length === 0) {
                alert('⚠️ Vui lòng chọn ít nhất một TV');
                return;
            }

            const tvIds = Array.from(checkboxes).map(cb => cb.value);
            if (!confirm(`Gỡ board này khỏi ${tvIds.length} TV đã chọn?`)) return;

            let successCount = 0;
            let errorCount = 0;

            for (const tvId of tvIds) {
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
                        successCount++;
                    } else {
                        errorCount++;
                    }
                } catch (error) {
                    errorCount++;
                }
            }

            if (successCount > 0) {
                alert(`✅ Đã gỡ khỏi ${successCount} TV`);
                loadData();
            } else {
                alert('❌ Không thể gỡ TV nào');
            }
        }

        // ===== QUICK ASSIGNMENT FUNCTIONS =====
        
        // Render WCB Checklist
        function renderWCBChecklist() {
            const container = document.getElementById('wcbChecklistContainer');
            if (boards.length === 0) {
                container.innerHTML = '<p style="color: #999;">Chưa có WCB nào. Vui lòng upload WCB trước.</p>';
                return;
            }

            let html = '';
            boards.forEach(board => {
                html += `
                    <label style="display: flex; align-items: center; padding: 12px; background: white; border-radius: 8px; margin-bottom: 10px; cursor: pointer; border: 2px solid #e8eef5; transition: all 0.2s;">
                        <input type="checkbox" 
                               class="wcb-checkbox" 
                               value="${board.id}" 
                               onchange="updateQuickAssignmentSummary()"
                               style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--dark); margin-bottom: 4px;">${board.event_title}</div>
                            <div style="font-size: 0.85rem; color: #666;">📅 ${board.event_date} | 🆔 ${board.id}</div>
                        </div>
                        <img src="${board.filepath}" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px; margin-left: 10px;">
                    </label>
                `;
            });

            container.innerHTML = html;
        }

        // Render TV Checklist
        function renderTVChecklist() {
            const container = document.getElementById('tvChecklistContainer');
            if (tvs.length === 0) {
                container.innerHTML = '<p style="color: #999;">Không có TV nào đang hoạt động.</p>';
                return;
            }

            let html = '';
            departments.forEach(dept => {
                const deptTVs = tvs.filter(tv => tv.department_id == dept.id);
                if (deptTVs.length > 0) {
                    html += `<div style="font-weight: 600; color: #667eea; margin: 15px 0 10px 0; font-size: 0.95rem;">${dept.name}</div>`;
                    deptTVs.forEach(tv => {
                        const isFull = tv.board_count >= 3;
                        html += `
                            <label style="display: flex; align-items: center; padding: 12px; background: ${isFull ? '#fff3cd' : 'white'}; border-radius: 8px; margin-bottom: 8px; cursor: ${isFull ? 'not-allowed' : 'pointer'}; border: 2px solid ${isFull ? '#ffc107' : '#e8eef5'}; opacity: ${isFull ? '0.7' : '1'}; transition: all 0.2s;">
                                <input type="checkbox" 
                                       class="tv-checkbox" 
                                       value="${tv.id}" 
                                       ${isFull ? 'disabled' : ''}
                                       onchange="updateQuickAssignmentSummary()"
                                       style="margin-right: 12px; width: 18px; height: 18px; cursor: ${isFull ? 'not-allowed' : 'pointer'};">
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: var(--dark);">${tv.name}</div>
                                    <div style="font-size: 0.85rem; color: #666;">
                                        ${tv.board_count}/3 WCB ${isFull ? '⚠️ Đã đủ' : '✓ Có thể assign'}
                                    </div>
                                </div>
                            </label>
                        `;
                    });
                }
            });

            container.innerHTML = html;
        }

        // Update Quick Assignment Summary
        function updateQuickAssignmentSummary() {
            const selectedWCBs = document.querySelectorAll('.wcb-checkbox:checked');
            const selectedTVs = document.querySelectorAll('.tv-checkbox:checked');
            
            document.getElementById('wcbSelectedCount').textContent = selectedWCBs.length;
            document.getElementById('tvSelectedCount').textContent = selectedTVs.length;

            const summaryDiv = document.getElementById('assignmentSummary');
            
            if (selectedWCBs.length === 0 || selectedTVs.length === 0) {
                summaryDiv.innerHTML = 'Chọn ít nhất 1 WCB và 1 TV để thực hiện assignment';
                return;
            }

            const wcbNames = Array.from(selectedWCBs).map(cb => {
                const board = boards.find(b => b.id == cb.value);
                return board ? board.event_title : '';
            });

            const tvNames = Array.from(selectedTVs).map(cb => {
                const tv = tvs.find(t => t.id == cb.value);
                return tv ? tv.name : '';
            });

            summaryDiv.innerHTML = `
                <div style="margin-bottom: 10px;">
                    <strong>📋 WCB đã chọn (${selectedWCBs.length}):</strong><br>
                    <span style="color: #667eea;">${wcbNames.join(', ')}</span>
                </div>
                <div>
                    <strong>📺 TV đã chọn (${selectedTVs.length}):</strong><br>
                    <span style="color: #28a745;">${tvNames.join(', ')}</span>
                </div>
                <div style="margin-top: 15px; padding: 10px; background: #fff3cd; border-radius: 6px; font-size: 0.9rem;">
                    ⚠️ Mỗi WCB sẽ được assign cho TẤT CẢ ${selectedTVs.length} TV đã chọn
                </div>
            `;
        }

        // Perform Quick Assignment
        async function performQuickAssignment() {
            const selectedWCBs = Array.from(document.querySelectorAll('.wcb-checkbox:checked')).map(cb => cb.value);
            const selectedTVs = Array.from(document.querySelectorAll('.tv-checkbox:checked')).map(cb => cb.value);

            if (selectedWCBs.length === 0) {
                alert('⚠️ Vui lòng chọn ít nhất 1 WCB');
                return;
            }

            if (selectedTVs.length === 0) {
                alert('⚠️ Vui lòng chọn ít nhất 1 TV');
                return;
            }

            if (!confirm(`Bạn có chắc muốn assign ${selectedWCBs.length} WCB cho ${selectedTVs.length} TV?\n\nTổng cộng: ${selectedWCBs.length * selectedTVs.length} assignments`)) {
                return;
            }

            const formData = new FormData();
            formData.append('board_ids', JSON.stringify(selectedWCBs));
            formData.append('tv_ids', JSON.stringify(selectedTVs));

            try {
                const response = await fetch('api.php?action=batch_assign', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    alert(`✅ Thành công!\n\n- Assigned: ${data.success_count}\n- Lỗi: ${data.error_count}\n\nTổng: ${data.total_assignments} assignments`);
                    
                    // Uncheck all
                    document.querySelectorAll('.wcb-checkbox, .tv-checkbox').forEach(cb => cb.checked = false);
                    updateQuickAssignmentSummary();
                    
                    // Reload data
                    loadData();
                } else {
                    alert('❌ Lỗi: ' + (data.message || 'Không thể assign'));
                }
            } catch (error) {
                alert('❌ Lỗi kết nối: ' + error.message);
            }
        }

        // Open all TVs
        function openAllTVs() {
            const paths = [
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

        // Load on page load
        loadData();
    </script>
</body>
</html>
