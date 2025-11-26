// TV Management JavaScript

let tvData = [];
let currentView = 'grid'; // 'grid' or 'table'

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadTVList();
});

// Load TV list
function loadTVList() {
    fetch('api/get-tvs.php')
        .then(response => response.json())
        .then(data => {
            tvData = data.tvs || [];
            displayTVs(tvData);
        })
        .catch(error => {
            console.error('Error loading TVs:', error);
            displayTVs([]);
        });
}

// Display TVs
function displayTVs(tvs) {
    if (currentView === 'grid') {
        displayGridView(tvs);
    } else {
        displayTableView(tvs);
    }
}

// Display grid view
function displayGridView(tvs) {
    const container = document.getElementById('tvContainer');
    if (!container) return;
    
    if (tvs.length === 0) {
        container.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: #999;">
                <i class="fas fa-tv" style="font-size: 4em; display: block; margin-bottom: 20px;"></i>
                <p style="font-size: 1.2em;">Chưa có TV nào được cấu hình</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = tvs.map(tv => `
        <div class="tv-card">
            <div class="tv-card-screen ${tv.currentContent ? '' : 'no-signal'}">
                ${tv.currentContent ? `<img src="${tv.contentUrl}" alt="${tv.name}">` : '<i class="fas fa-tv"></i>'}
                <div class="tv-card-badge ${tv.status}">
                    ${tv.status === 'online' ? 'Online' : 'Offline'}
                </div>
            </div>
            <div class="tv-card-body">
                <div class="tv-card-title">${tv.name}</div>
                <div class="tv-card-info">
                    <div class="tv-card-info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${tv.location}</span>
                    </div>
                    <div class="tv-card-info-item">
                        <i class="fas fa-network-wired"></i>
                        <span>${tv.ipAddress || 'N/A'}</span>
                    </div>
                    <div class="tv-card-info-item">
                        <i class="fas fa-image"></i>
                        <span>${tv.currentContent || 'Không có nội dung'}</span>
                    </div>
                </div>
                <div class="tv-card-actions">
                    <button class="btn-control-tv" onclick="controlTV('${tv.id}')">
                        <i class="fas fa-cog"></i> Điều khiển
                    </button>
                    <button class="btn-edit-tv" onclick="editTV('${tv.id}')">
                        <i class="fas fa-edit"></i> Sửa
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Display table view
function displayTableView(tvs) {
    const container = document.getElementById('tvContainer');
    if (!container) return;
    
    if (tvs.length === 0) {
        container.innerHTML = `
            <div class="tv-table-container">
                <table class="tv-table">
                    <tbody>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 60px; color: #999;">
                                <i class="fas fa-tv" style="font-size: 4em; display: block; margin-bottom: 20px;"></i>
                                <p style="font-size: 1.2em;">Chưa có TV nào được cấu hình</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;
        return;
    }
    
    container.innerHTML = `
        <div class="tv-table-container">
            <table class="tv-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên TV</th>
                        <th>Vị trí</th>
                        <th>IP Address</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    ${tvs.map(tv => `
                        <tr>
                            <td>${tv.id}</td>
                            <td>${tv.name}</td>
                            <td>${tv.location}</td>
                            <td>${tv.ipAddress || 'N/A'}</td>
                            <td>
                                <div class="tv-status-indicator">
                                    <span class="status-dot ${tv.status}"></span>
                                    <span>${tv.status === 'online' ? 'Online' : 'Offline'}</span>
                                </div>
                            </td>
                            <td>
                                <div class="tv-actions">
                                    <button class="btn-view-tv" onclick="controlTV('${tv.id}')">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <button class="btn-edit-tv" onclick="editTV('${tv.id}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-delete-tv" onclick="deleteTV('${tv.id}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

// Toggle view
function toggleView(view) {
    currentView = view;
    
    // Update button states
    document.querySelectorAll('.view-toggle button').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    displayTVs(tvData);
}

// Open add TV modal
function openAddTVModal() {
    const modal = document.getElementById('tvModal');
    if (modal) {
        modal.classList.add('active');
        document.getElementById('tvForm').reset();
        document.getElementById('tvId').value = '';
        document.getElementById('modalTitle').textContent = 'Thêm TV mới';
    }
}

// Close modal
function closeTVModal() {
    const modal = document.getElementById('tvModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Edit TV
function editTV(id) {
    const tv = tvData.find(t => t.id == id);
    if (!tv) return;
    
    const modal = document.getElementById('tvModal');
    if (modal) {
        modal.classList.add('active');
        document.getElementById('modalTitle').textContent = 'Chỉnh sửa TV';
        document.getElementById('tvId').value = tv.id;
        document.getElementById('tvName').value = tv.name;
        document.getElementById('tvLocation').value = tv.location;
        document.getElementById('tvIpAddress').value = tv.ipAddress || '';
        document.getElementById('tvDescription').value = tv.description || '';
    }
}

// Save TV
function saveTV(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const id = formData.get('tvId');
    const url = id ? 'api/update-tv.php' : 'api/create-tv.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeTVModal();
            loadTVList();
            alert('Lưu TV thành công!');
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi lưu TV');
    });
}

// Delete TV
function deleteTV(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa TV này?')) return;
    
    fetch('api/delete-tv.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadTVList();
            alert('Xóa TV thành công!');
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi xóa TV');
    });
}

// Control TV
function controlTV(id) {
    window.location.href = `tv-control.php?id=${id}`;
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('tvModal');
    if (event.target === modal) {
        closeTVModal();
    }
}
