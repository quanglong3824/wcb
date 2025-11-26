// Schedule Management JavaScript

let scheduleData = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadScheduleList();
});

// Load schedule list
function loadScheduleList() {
    fetch('api/get-schedules.php')
        .then(response => response.json())
        .then(data => {
            scheduleData = data.schedules || [];
            displayScheduleList(scheduleData);
        })
        .catch(error => {
            console.error('Error loading schedules:', error);
            displayScheduleList([]);
        });
}

// Display schedule list
function displayScheduleList(schedules) {
    const tbody = document.getElementById('scheduleTableBody');
    if (!tbody) return;
    
    if (schedules.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                    <i class="fas fa-calendar-times" style="font-size: 3em; display: block; margin-bottom: 15px;"></i>
                    Chưa có lịch chiếu nào
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = schedules.map(schedule => `
        <tr>
            <td>${schedule.id}</td>
            <td>
                <div class="schedule-time">
                    <span class="date">${schedule.date}</span>
                    <span class="time">${schedule.startTime} - ${schedule.endTime}</span>
                </div>
            </td>
            <td>${schedule.tvName}</td>
            <td>${schedule.contentName}</td>
            <td>
                <span class="schedule-status ${schedule.status}">
                    ${getStatusText(schedule.status)}
                </span>
            </td>
            <td>
                <div class="schedule-actions">
                    <button class="btn-edit-schedule" onclick="editSchedule('${schedule.id}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-delete-schedule" onclick="deleteSchedule('${schedule.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Get status text
function getStatusText(status) {
    const statusMap = {
        'active': 'Đang chạy',
        'pending': 'Chờ chạy',
        'completed': 'Đã hoàn thành'
    };
    return statusMap[status] || status;
}

// Open add schedule modal
function openAddScheduleModal() {
    const modal = document.getElementById('scheduleModal');
    if (modal) {
        modal.classList.add('active');
        document.getElementById('scheduleForm').reset();
        document.getElementById('scheduleId').value = '';
        document.getElementById('modalTitle').textContent = 'Thêm lịch chiếu mới';
    }
}

// Close modal
function closeScheduleModal() {
    const modal = document.getElementById('scheduleModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Edit schedule
function editSchedule(id) {
    const schedule = scheduleData.find(s => s.id == id);
    if (!schedule) return;
    
    const modal = document.getElementById('scheduleModal');
    if (modal) {
        modal.classList.add('active');
        document.getElementById('modalTitle').textContent = 'Chỉnh sửa lịch chiếu';
        document.getElementById('scheduleId').value = schedule.id;
        document.getElementById('scheduleTv').value = schedule.tvId;
        document.getElementById('scheduleContent').value = schedule.contentId;
        document.getElementById('scheduleDate').value = schedule.date;
        document.getElementById('scheduleStartTime').value = schedule.startTime;
        document.getElementById('scheduleEndTime').value = schedule.endTime;
        document.getElementById('scheduleRepeat').value = schedule.repeat || 'none';
    }
}

// Save schedule
function saveSchedule(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const id = formData.get('scheduleId');
    const url = id ? 'api/update-schedule.php' : 'api/create-schedule.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeScheduleModal();
            loadScheduleList();
            alert('Lưu lịch chiếu thành công!');
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi lưu lịch chiếu');
    });
}

// Delete schedule
function deleteSchedule(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa lịch chiếu này?')) return;
    
    fetch('api/delete-schedule.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadScheduleList();
            alert('Xóa lịch chiếu thành công!');
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi xóa lịch chiếu');
    });
}

// Filter by TV
function filterByTV(tvId) {
    if (tvId === 'all') {
        displayScheduleList(scheduleData);
    } else {
        const filtered = scheduleData.filter(s => s.tvId == tvId);
        displayScheduleList(filtered);
    }
}

// Filter by status
function filterByScheduleStatus(status) {
    if (status === 'all') {
        displayScheduleList(scheduleData);
    } else {
        const filtered = scheduleData.filter(s => s.status === status);
        displayScheduleList(filtered);
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('scheduleModal');
    if (event.target === modal) {
        closeScheduleModal();
    }
}
