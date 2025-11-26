// Manage WCB JavaScript

let wcbData = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadWCBList();
});

// Load WCB list
function loadWCBList() {
    fetch('api/get-wcb.php')
        .then(response => response.json())
        .then(data => {
            wcbData = data.wcbs || [];
            displayWCBList(wcbData);
        })
        .catch(error => {
            console.error('Error loading WCB:', error);
            displayWCBList([]);
        });
}

// Display WCB list
function displayWCBList(wcbs) {
    const tbody = document.getElementById('wcbTableBody');
    if (!tbody) return;
    
    if (wcbs.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
                    <i class="fas fa-inbox" style="font-size: 3em; display: block; margin-bottom: 15px;"></i>
                    Chưa có nội dung WCB nào
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = wcbs.map(wcb => `
        <tr>
            <td>${wcb.id}</td>
            <td>
                <img src="${wcb.thumbnail}" alt="${wcb.name}" class="wcb-preview" 
                     onclick="previewWCB('${wcb.id}')">
            </td>
            <td>${wcb.name}</td>
            <td>${wcb.type === 'image' ? 'Hình ảnh' : 'Video'}</td>
            <td>${wcb.assignedTo || 'Chưa gán'}</td>
            <td>
                <span class="wcb-status ${wcb.status}">
                    ${wcb.status === 'active' ? 'Đang sử dụng' : 'Không sử dụng'}
                </span>
            </td>
            <td>
                <div class="wcb-actions">
                    <button class="btn-edit" onclick="editWCB('${wcb.id}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-assign" onclick="assignWCB('${wcb.id}')">
                        <i class="fas fa-tv"></i>
                    </button>
                    <button class="btn-delete-wcb" onclick="deleteWCB('${wcb.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Open add WCB modal
function openAddWCBModal() {
    const modal = document.getElementById('wcbModal');
    if (modal) {
        modal.classList.add('active');
        document.getElementById('wcbForm').reset();
        document.getElementById('wcbId').value = '';
        document.getElementById('modalTitle').textContent = 'Thêm WCB mới';
    }
}

// Close modal
function closeModal() {
    const modal = document.getElementById('wcbModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Edit WCB
function editWCB(id) {
    const wcb = wcbData.find(w => w.id == id);
    if (!wcb) return;
    
    const modal = document.getElementById('wcbModal');
    if (modal) {
        modal.classList.add('active');
        document.getElementById('modalTitle').textContent = 'Chỉnh sửa WCB';
        document.getElementById('wcbId').value = wcb.id;
        document.getElementById('wcbName').value = wcb.name;
        document.getElementById('wcbType').value = wcb.type;
        document.getElementById('wcbDescription').value = wcb.description || '';
    }
}

// Save WCB
function saveWCB(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const id = formData.get('wcbId');
    const url = id ? 'api/update-wcb.php' : 'api/create-wcb.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            loadWCBList();
            alert('Lưu thành công!');
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi lưu');
    });
}

// Delete WCB
function deleteWCB(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa WCB này?')) return;
    
    fetch('api/delete-wcb.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadWCBList();
            alert('Xóa thành công!');
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi xóa');
    });
}

// Assign WCB to TV
function assignWCB(id) {
    // Open assign modal or redirect to assignment page
    window.location.href = `assign-wcb.php?wcb=${id}`;
}

// Preview WCB
function previewWCB(id) {
    const wcb = wcbData.find(w => w.id == id);
    if (!wcb) return;
    
    // Open preview in modal or new window
    window.open(wcb.url, '_blank');
}

// Search WCB
function searchWCB(query) {
    const filtered = wcbData.filter(wcb => 
        wcb.name.toLowerCase().includes(query.toLowerCase()) ||
        wcb.description.toLowerCase().includes(query.toLowerCase())
    );
    displayWCBList(filtered);
}

// Filter by type
function filterByType(type) {
    if (type === 'all') {
        displayWCBList(wcbData);
    } else {
        const filtered = wcbData.filter(wcb => wcb.type === type);
        displayWCBList(filtered);
    }
}

// Filter by status
function filterByStatus(status) {
    if (status === 'all') {
        displayWCBList(wcbData);
    } else {
        const filtered = wcbData.filter(wcb => wcb.status === status);
        displayWCBList(filtered);
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('wcbModal');
    if (event.target === modal) {
        closeModal();
    }
}
