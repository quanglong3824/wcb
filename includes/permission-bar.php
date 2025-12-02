<?php
/**
 * Permission Bar Component
 * Hiển thị thanh quyền trên mỗi trang
 * 
 * Sử dụng: include 'includes/permission-bar.php';
 * Yêu cầu: $currentModule phải được định nghĩa trước
 */

if (!isset($currentModule)) {
    return;
}

$_canView = hasPermission($currentModule, PERM_VIEW);
$_canCreate = hasPermission($currentModule, PERM_CREATE);
$_canEdit = hasPermission($currentModule, PERM_EDIT);
$_canDelete = hasPermission($currentModule, PERM_DELETE);
$_isReadonly = isReadOnly($currentModule);

global $ALL_MODULES;
$_moduleName = isset($ALL_MODULES[$currentModule]) ? $ALL_MODULES[$currentModule]['name'] : $currentModule;
?>

<div class="permission-bar <?php echo $_isReadonly ? 'readonly' : ''; ?>">
    <div class="permission-bar-left">
        <span class="permission-label">
            <i class="fas fa-shield-alt"></i> Quyền của bạn:
        </span>
        <div class="permission-badges">
            <span class="perm-badge <?php echo $_canView ? 'active' : 'inactive'; ?>" title="Xem">
                <i class="fas fa-eye"></i> Xem
            </span>
            <span class="perm-badge <?php echo $_canCreate ? 'active' : 'inactive'; ?>" title="Tạo mới">
                <i class="fas fa-plus"></i> Tạo
            </span>
            <span class="perm-badge <?php echo $_canEdit ? 'active' : 'inactive'; ?>" title="Chỉnh sửa">
                <i class="fas fa-edit"></i> Sửa
            </span>
            <span class="perm-badge <?php echo $_canDelete ? 'active' : 'inactive'; ?>" title="Xóa">
                <i class="fas fa-trash"></i> Xóa
            </span>
        </div>
    </div>
    <?php if ($_isReadonly): ?>
    <div class="permission-bar-right">
        <span class="readonly-notice">
            <i class="fas fa-lock"></i> Chế độ chỉ xem
        </span>
    </div>
    <?php endif; ?>
</div>

<style>
.permission-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    margin-bottom: 20px;
    font-size: 0.85em;
}

.permission-bar.readonly {
    background: #fef3c7;
    border-color: #f59e0b;
}

.permission-bar-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.permission-label {
    color: #64748b;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
}

.permission-label i {
    color: #d4af37;
}

.permission-badges {
    display: flex;
    gap: 8px;
}

.perm-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    font-size: 0.85em;
    font-weight: 500;
    border: 1px solid;
}

.perm-badge.active {
    background: #ecfdf5;
    border-color: #10b981;
    color: #059669;
}

.perm-badge.inactive {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #94a3b8;
    text-decoration: line-through;
}

.permission-bar-right {
    display: flex;
    align-items: center;
}

.readonly-notice {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #92400e;
    font-weight: 600;
}

.readonly-notice i {
    color: #f59e0b;
}

/* Disable buttons when no permission */
.no-create-permission .btn-create,
.no-create-permission [data-action="create"],
.no-create-permission .btn-primary:not(.btn-save):not(.btn-submit) {
    opacity: 0.4 !important;
    pointer-events: none !important;
    cursor: not-allowed !important;
}

.no-edit-permission .btn-edit,
.no-edit-permission [data-action="edit"],
.no-edit-permission .btn-action.edit {
    opacity: 0.4 !important;
    pointer-events: none !important;
    cursor: not-allowed !important;
}

.no-delete-permission .btn-delete,
.no-delete-permission [data-action="delete"],
.no-delete-permission .btn-action.delete,
.no-delete-permission .btn-danger {
    opacity: 0.4 !important;
    pointer-events: none !important;
    cursor: not-allowed !important;
}
</style>

<script>
// Add permission classes to body for CSS targeting
document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;
    <?php if (!$_canCreate): ?>
    body.classList.add('no-create-permission');
    <?php endif; ?>
    <?php if (!$_canEdit): ?>
    body.classList.add('no-edit-permission');
    <?php endif; ?>
    <?php if (!$_canDelete): ?>
    body.classList.add('no-delete-permission');
    <?php endif; ?>
    <?php if ($_isReadonly): ?>
    body.classList.add('readonly-mode');
    <?php endif; ?>
});

// Override window.userPermissions for JS
window.userPermissions = {
    canView: <?php echo $_canView ? 'true' : 'false'; ?>,
    canCreate: <?php echo $_canCreate ? 'true' : 'false'; ?>,
    canEdit: <?php echo $_canEdit ? 'true' : 'false'; ?>,
    canDelete: <?php echo $_canDelete ? 'true' : 'false'; ?>,
    isReadonly: <?php echo $_isReadonly ? 'true' : 'false'; ?>
};
</script>
