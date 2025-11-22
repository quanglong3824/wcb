<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin V2 - Multi TV Management</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .tv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .tv-card {
            background: #f8f9fb;
            border: 2px solid #e8eef5;
            border-radius: 10px;
            padding: 20px;
        }
        .tv-card h3 {
            color: #1a1a1a;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        .tv-card .tv-code {
            background: #4a90e2;
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 10px;
        }
        .tv-card .tv-link {
            display: block;
            background: #28a745;
            color: white;
            text-align: center;
            padding: 10px;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 10px;
            font-weight: 600;
        }
        .tv-card .tv-link:hover {
            background: #218838;
        }
        .department-section {
            margin-bottom: 40px;
        }
        .department-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .board-assignment {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border: 1px solid #e1e8ed;
        }
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .checkbox-item {
            background: #f0f0f0;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .checkbox-item:hover {
            background: #e0e0e0;
        }
        .checkbox-item input {
            margin-right: 5px;
        }
        .checkbox-item.checked {
            background: #d4edda;
            border: 2px solid #28a745;
        }
        .department-checkboxes {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        .department-checkbox {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
        }
        .department-checkbox:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .department-checkbox input {
            margin-right: 8px;
        }
        .department-checkbox.checked {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .assignment-controls {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .btn-assign {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .btn-assign:hover {
            background: #218838;
        }
        .btn-unassign {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .btn-unassign:hover {
            background: #c82333;
        }
        .assignment-tag {
            display: inline-block;
            background: #e7f3ff;
            color: #0066cc;
            padding: 4px 10px;
            border-radius: 4px;
            margin: 4px;
            font-size: 0.85rem;
        }
        .board-item {
            background: white;
            border: 2px solid #e8eef5;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .board-info {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .board-image img {
            width: 200px;
            height: auto;
            border-radius: 8px;
        }
        .board-details h4 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🎯 Admin - Quản lý Multi TV</h1>
            <nav>
                <a href="index.php" class="nav-btn">Trang chủ</a>
                <a href="admin.php" class="nav-btn">Admin</a>
                <a href="#" onclick="openAllTVs()" class="nav-btn display-btn">Mở tất cả TV</a>
            </nav>
        </header>

        <!-- Danh sách TV theo bộ phận -->
        <section class="panel">
            <h2>📺 Danh sách TV theo bộ phận</h2>
            <div id="tvList">Đang tải...</div>
        </section>

        <!-- Upload Board mới -->
        <section class="panel">
            <h2>📤 Upload Welcome Board mới</h2>
            <form action="upload.php" method="POST" enctype="multipart/form-data" class="upload-form">
                <div class="form-group">
                    <label>Ngày sự kiện</label>
                    <input type="date" name="event_date" required>
                </div>
                <div class="form-group">
                    <label>Tiêu đề sự kiện</label>
                    <input type="text" name="event_title" required>
                </div>
                <div class="form-group">
                    <label>Hình ảnh</label>
                    <input type="file" name="welcome_image" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label>Chọn theo bộ phận (tất cả TV trong bộ phận):</label>
                    <div class="department-checkboxes" id="departmentCheckboxes">Đang tải...</div>
                </div>
                <div class="form-group">
                    <label>Hoặc chọn từng TV riêng lẻ:</label>
                    <div class="checkbox-group" id="tvCheckboxes">Đang tải...</div>
                </div>
                <button type="submit" class="btn-primary">Upload & Assign</button>
            </form>
        </section>

        <!-- Quản lý Boards -->
        <section class="panel">
            <h2>📋 Quản lý Welcome Boards</h2>
            <div id="boardsList">Đang tải...</div>
        </section>
    </div>

    <script>
        let departments = [];
        let tvs = [];
        let boards = [];

        // Load dữ liệu
        async function loadData() {
            try {
                // Load departments
                const deptResponse = await fetch('api.php?action=get_departments');
                const deptData = await deptResponse.json();
                if (deptData.success) departments = deptData.departments;

                // Load TVs
                const tvResponse = await fetch('api.php?action=get_tvs');
                const tvData = await tvResponse.json();
                if (tvData.success) tvs = tvData.tvs;

                // Load boards
                const boardResponse = await fetch('api.php?action=get_all_boards');
                const boardData = await boardResponse.json();
                if (boardData.success) boards = boardData.boards;

                renderTVList();
                renderDepartmentCheckboxes();
                renderTVCheckboxes();
                renderBoardsList();
                loadBoardAssignments();
            } catch (error) {
                console.error('Load data error:', error);
            }
        }

        // Render danh sách TV
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
                    html += `
                        <div class="tv-card">
                            <span class="tv-code">${tv.code}</span>
                            <h3>${tv.name}</h3>
                            <p>📍 ${tv.location || 'N/A'}</p>
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

            container.innerHTML = html;
        }

        // Render department checkboxes cho upload form
        function renderDepartmentCheckboxes() {
            const container = document.getElementById('departmentCheckboxes');
            let html = '';

            departments.forEach(dept => {
                html += `
                    <label class="department-checkbox">
                        <input type="checkbox" name="department_ids[]" value="${dept.id}" 
                               onchange="this.parentElement.classList.toggle('checked', this.checked)">
                        ${dept.name}
                    </label>
                `;
            });

            container.innerHTML = html;
        }

        // Render checkboxes cho upload form
        function renderTVCheckboxes() {
            const container = document.getElementById('tvCheckboxes');
            let html = '';

            tvs.forEach(tv => {
                html += `
                    <label class="checkbox-item">
                        <input type="checkbox" name="tv_ids[]" value="${tv.id}" 
                               onchange="this.parentElement.classList.toggle('checked', this.checked)">
                        ${tv.name}
                    </label>
                `;
            });

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

        // Render assignment cho một board
        function renderBoardAssignment(boardId, assignments) {
            const container = document.getElementById(`assignment-${boardId}`);
            if (!container) return;

            let html = '<div>';
            if (assignments.length === 0) {
                html += '<p style="color: #999;">Chưa assign cho TV nào</p>';
            } else {
                assignments.forEach(a => {
                    html += `<span class="assignment-tag">${a.department_name} - ${a.tv_name}</span>`;
                });
            }
            html += '</div>';

            // Add assignment controls
            html += '<div class="assignment-controls">';
            html += '<strong style="width: 100%; margin-bottom: 10px; display: block;">Quản lý hiển thị:</strong>';
            
            // Department assignment buttons
            departments.forEach(dept => {
                html += `
                    <button class="btn-assign" onclick="assignToDepartment('${boardId}', ${dept.id}, '${dept.name}')">
                        ➕ ${dept.name}
                    </button>
                    <button class="btn-unassign" onclick="unassignFromDepartment('${boardId}', ${dept.id}, '${dept.name}')">
                        ➖ ${dept.name}
                    </button>
                `;
            });
            html += '</div>';

            container.innerHTML = html;
        }

        // Render danh sách boards
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

        // Mở tất cả TV
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

        // Load khi trang load
        loadData();
    </script>
</body>
</html>
