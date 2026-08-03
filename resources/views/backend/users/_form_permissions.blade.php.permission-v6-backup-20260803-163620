@php
    $permissionGroups = $permissionGroups ?? config('user_permissions.groups', []);
    $permissionActionLabels = config('user_permissions.actions', []);
    $storedPermissions = isset($user)
        ? $user->formPermissions->keyBy('permission_key')
        : collect();
    $hasOldPermissionInput = session()->hasOldInput('permissions');
    $oldPermissionValues = old('permissions', []);
    $permissionsEnabled = (bool) old(
        'form_permissions_enabled',
        isset($user) ? (bool) $user->form_permissions_enabled : false
    );
@endphp

<div class="col-12">
    <section class="ufp-section" id="userFormPermissionSection">
        <div class="ufp-section-head">
            <div class="ufp-heading-wrap">
                <div class="ufp-heading-icon">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div>
                    <h5 class="ufp-title mb-1">กำหนดสิทธิ์รายหมวดและรายฟอร์ม</h5>
                    <div class="ufp-subtitle">
                        บ้านและโครงการใช้จำกัดกลุ่มผู้รับบริการ ส่วนสิทธิ์ด้านล่างใช้จำกัดเมนูและการกระทำในแต่ละฟอร์ม
                    </div>
                </div>
            </div>

            <div class="form-check form-switch ufp-enable-switch">
                <input type="hidden" name="form_permissions_enabled" value="0">
                <input
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    id="formPermissionsEnabled"
                    name="form_permissions_enabled"
                    value="1"
                    {{ $permissionsEnabled ? 'checked' : '' }}
                >
                <label class="form-check-label fw-bold" for="formPermissionsEnabled">
                    เปิดใช้สิทธิ์รายฟอร์ม
                </label>
            </div>
        </div>

        <div class="ufp-legacy-note" id="permissionLegacyNote">
            <i class="bi bi-info-circle-fill"></i>
            <span>
                เมื่อปิดไว้ ผู้ใช้นี้จะยังทำงานตามบทบาทและ Route เดิม จึงไม่กระทบผู้ใช้งานเดิมระหว่างทยอยเชื่อมระบบ
            </span>
        </div>

        <div class="ufp-admin-note d-none" id="permissionAdminNote">
            <i class="bi bi-shield-check"></i>
            <span>ผู้ดูแลระบบได้รับสิทธิ์เต็มระบบเสมอ แม้กำหนดช่องด้านล่างไว้ก็ตาม</span>
        </div>

        @error('form_permissions_enabled')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror
        @error('permissions')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror

        <div class="ufp-toolbar" id="permissionToolbar">
            <button type="button" class="btn ufp-tool-btn" data-permission-global="view">
                <i class="bi bi-eye-fill"></i>
                ดูทั้งหมด
            </button>
            <button type="button" class="btn ufp-tool-btn ufp-tool-btn-primary" data-permission-global="full">
                <i class="bi bi-check2-square"></i>
                เลือกทุกสิทธิ์
            </button>
            <button type="button" class="btn ufp-tool-btn ufp-tool-btn-danger" data-permission-global="clear">
                <i class="bi bi-eraser-fill"></i>
                ล้างทั้งหมด
            </button>
        </div>

        <div class="ufp-groups" id="permissionGroups">
            @foreach($permissionGroups as $groupKey => $group)
                <article class="ufp-group" data-permission-group="{{ $groupKey }}">
                    <div class="ufp-group-head">
                        <div class="ufp-group-title-wrap">
                            <div class="ufp-group-icon">
                                <i class="bi {{ $group['icon'] ?? 'bi-folder-fill' }}"></i>
                            </div>
                            <div>
                                <div class="ufp-group-title">{{ $group['label'] ?? $groupKey }}</div>
                                @if(!empty($group['description']))
                                    <div class="ufp-group-description">{{ $group['description'] }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="ufp-group-actions">
                            <button type="button" class="btn" data-permission-group-action="view">
                                ดูทั้งหมด
                            </button>
                            <button type="button" class="btn" data-permission-group-action="full">
                                เลือกครบ
                            </button>
                            <button type="button" class="btn" data-permission-group-action="clear">
                                ล้าง
                            </button>
                        </div>
                    </div>

                    <div class="ufp-table-wrap">
                        <table class="table ufp-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ufp-module-column">รายการฟอร์ม</th>
                                    @foreach($permissionActionLabels as $actionKey => $actionLabel)
                                        <th class="text-center">{{ $actionLabel }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(($group['items'] ?? []) as $permissionKey => $item)
                                    @php
                                        $record = $storedPermissions->get($permissionKey);
                                        $availableActions = $item['actions'] ?? [];
                                    @endphp
                                    <tr data-permission-row="{{ $permissionKey }}">
                                        <td class="ufp-module-cell">
                                            <div class="ufp-module-name">{{ $item['label'] ?? $permissionKey }}</div>
                                            @if(!empty($item['description']))
                                                <div class="ufp-module-description">{{ $item['description'] }}</div>
                                            @endif
                                        </td>

                                        @foreach($permissionActionLabels as $actionKey => $actionLabel)
                                            @php
                                                $isAvailable = in_array($actionKey, $availableActions, true);
                                                $column = 'can_' . $actionKey;
                                                $isChecked = $hasOldPermissionInput
                                                    ? (bool) data_get($oldPermissionValues, $permissionKey . '.' . $actionKey, false)
                                                    : (bool) ($record?->{$column} ?? false);
                                                $checkboxId = 'permission_' . $permissionKey . '_' . $actionKey;
                                            @endphp
                                            <td class="text-center ufp-action-cell">
                                                @if($isAvailable)
                                                    <input
                                                        class="form-check-input ufp-permission-checkbox"
                                                        type="checkbox"
                                                        name="permissions[{{ $permissionKey }}][{{ $actionKey }}]"
                                                        value="1"
                                                        id="{{ $checkboxId }}"
                                                        data-permission-action="{{ $actionKey }}"
                                                        aria-label="{{ $item['label'] ?? $permissionKey }}: {{ $actionLabel }}"
                                                        {{ $isChecked ? 'checked' : '' }}
                                                    >
                                                @else
                                                    <span class="ufp-not-applicable" title="รายการนี้ไม่มีการกระทำดังกล่าว">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</div>

<style>
.user-form-page .ufp-section{
    border:1px solid #dbe7f4;
    border-radius:24px;
    background:#f8fbff;
    overflow:hidden;
}
.user-form-page .ufp-section-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:1rem;
    flex-wrap:wrap;
    padding:1.25rem;
    background:#fff;
    border-bottom:1px solid #e8eef6;
}
.user-form-page .ufp-heading-wrap,
.user-form-page .ufp-group-title-wrap{
    display:flex;
    align-items:center;
    gap:.85rem;
}
.user-form-page .ufp-heading-icon{
    width:48px;
    height:48px;
    border-radius:16px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
    color:#fff;
    background:linear-gradient(135deg,#1d4ed8,#3b82f6);
    box-shadow:0 10px 24px rgba(37,99,235,.20);
}
.user-form-page .ufp-title{
    font-weight:800;
    color:#0f172a;
}
.user-form-page .ufp-subtitle,
.user-form-page .ufp-group-description,
.user-form-page .ufp-module-description{
    color:#64748b;
    font-size:.88rem;
}
.user-form-page .ufp-enable-switch{
    display:flex;
    align-items:center;
    gap:.6rem;
    margin:0;
    padding:.75rem 1rem .75rem 3.2rem;
    border:1px solid #bfdbfe;
    border-radius:999px;
    background:#eff6ff;
    color:#1e40af;
}
.user-form-page .ufp-enable-switch .form-check-input{
    width:2.55rem;
    height:1.3rem;
    margin-left:-2.55rem;
    cursor:pointer;
}
.user-form-page .ufp-legacy-note,
.user-form-page .ufp-admin-note{
    display:flex;
    align-items:flex-start;
    gap:.65rem;
    margin:1rem 1.25rem 0;
    padding:.85rem 1rem;
    border-radius:16px;
    font-size:.9rem;
}
.user-form-page .ufp-legacy-note{
    color:#7c2d12;
    background:#fff7ed;
    border:1px solid #fed7aa;
}
.user-form-page .ufp-admin-note{
    color:#166534;
    background:#f0fdf4;
    border:1px solid #bbf7d0;
}
.user-form-page .ufp-toolbar{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:.55rem;
    flex-wrap:wrap;
    padding:1rem 1.25rem;
}
.user-form-page .ufp-tool-btn,
.user-form-page .ufp-group-actions .btn{
    display:inline-flex;
    align-items:center;
    gap:.4rem;
    border-radius:999px;
    border:1px solid #dbe3ee;
    background:#fff;
    color:#334155;
    font-weight:700;
    font-size:.85rem;
    padding:.55rem .9rem;
}
.user-form-page .ufp-tool-btn:hover,
.user-form-page .ufp-group-actions .btn:hover{
    border-color:#93c5fd;
    color:#1d4ed8;
    background:#eff6ff;
}
.user-form-page .ufp-tool-btn-primary{
    color:#fff;
    border-color:transparent;
    background:linear-gradient(135deg,#1d4ed8,#3b82f6);
}
.user-form-page .ufp-tool-btn-primary:hover{
    color:#fff;
    background:linear-gradient(135deg,#1e40af,#2563eb);
}
.user-form-page .ufp-tool-btn-danger{
    color:#b91c1c;
    border-color:#fecaca;
    background:#fff;
}
.user-form-page .ufp-groups{
    padding:0 1.25rem 1.25rem;
    transition:opacity .2s ease;
}
.user-form-page .ufp-groups.is-disabled,
.user-form-page .ufp-toolbar.is-disabled{
    opacity:.52;
}
.user-form-page .ufp-group{
    background:#fff;
    border:1px solid #e5edf6;
    border-radius:20px;
    overflow:hidden;
    margin-top:1rem;
}
.user-form-page .ufp-group:first-child{
    margin-top:0;
}
.user-form-page .ufp-group-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:1rem;
    flex-wrap:wrap;
    padding:1rem 1.1rem;
    border-bottom:1px solid #edf2f7;
    background:#fcfdff;
}
.user-form-page .ufp-group-icon{
    width:42px;
    height:42px;
    border-radius:14px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    color:#1d4ed8;
    background:#dbeafe;
    flex:0 0 auto;
}
.user-form-page .ufp-group-title{
    color:#0f172a;
    font-weight:800;
}
.user-form-page .ufp-group-actions{
    display:flex;
    gap:.4rem;
    flex-wrap:wrap;
}
.user-form-page .ufp-table-wrap{
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
}
.user-form-page .ufp-table{
    min-width:880px;
}
.user-form-page .ufp-table thead th{
    background:#f8fafc;
    color:#475569;
    font-size:.84rem;
    font-weight:800;
    white-space:nowrap;
    padding:.85rem .75rem;
    border-bottom:1px solid #e8eef5;
}
.user-form-page .ufp-table tbody td{
    padding:.85rem .75rem;
    border-color:#eef2f7;
}
.user-form-page .ufp-module-column{
    min-width:330px;
}
.user-form-page .ufp-module-name{
    color:#172033;
    font-weight:700;
}
.user-form-page .ufp-action-cell{
    min-width:98px;
}
.user-form-page .ufp-permission-checkbox{
    width:1.18rem;
    height:1.18rem;
    cursor:pointer;
    border-color:#94a3b8;
}
.user-form-page .ufp-permission-checkbox:checked{
    background-color:#2563eb;
    border-color:#2563eb;
}
.user-form-page .ufp-not-applicable{
    color:#cbd5e1;
    font-weight:700;
}
@media (max-width:767.98px){
    .user-form-page .ufp-section-head,
    .user-form-page .ufp-group-head{
        align-items:flex-start;
    }
    .user-form-page .ufp-enable-switch{
        width:100%;
    }
    .user-form-page .ufp-toolbar{
        justify-content:flex-start;
    }
    .user-form-page .ufp-tool-btn{
        flex:1 1 auto;
        justify-content:center;
    }
    .user-form-page .ufp-group-actions{
        width:100%;
    }
    .user-form-page .ufp-group-actions .btn{
        flex:1 1 0;
        justify-content:center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const section = document.getElementById('userFormPermissionSection');
    if (!section) return;

    const enabledSwitch = document.getElementById('formPermissionsEnabled');
    const groups = document.getElementById('permissionGroups');
    const toolbar = document.getElementById('permissionToolbar');
    const legacyNote = document.getElementById('permissionLegacyNote');
    const adminNote = document.getElementById('permissionAdminNote');
    const roleSelect = document.querySelector('select[name="role"]');
    const permissionCheckboxes = [...section.querySelectorAll('.ufp-permission-checkbox')];

    function setPermissionEnabledState() {
        const isEnabled = Boolean(enabledSwitch && enabledSwitch.checked);

        permissionCheckboxes.forEach(function (checkbox) {
            checkbox.disabled = !isEnabled;
        });

        section.querySelectorAll('[data-permission-global], [data-permission-group-action]').forEach(function (button) {
            button.disabled = !isEnabled;
        });

        groups?.classList.toggle('is-disabled', !isEnabled);
        toolbar?.classList.toggle('is-disabled', !isEnabled);
        legacyNote?.classList.toggle('d-none', isEnabled);
    }

    function ensureEnabled() {
        if (enabledSwitch && !enabledSwitch.checked) {
            enabledSwitch.checked = true;
            setPermissionEnabledState();
        }
    }

    function applySelection(container, mode) {
        ensureEnabled();

        const checkboxes = [...container.querySelectorAll('.ufp-permission-checkbox')];

        checkboxes.forEach(function (checkbox) {
            const action = checkbox.dataset.permissionAction;

            if (mode === 'full') {
                checkbox.checked = true;
            } else if (mode === 'view') {
                checkbox.checked = action === 'view';
            } else if (mode === 'clear') {
                checkbox.checked = false;
            }
        });
    }

    section.querySelectorAll('[data-permission-global]').forEach(function (button) {
        button.addEventListener('click', function () {
            applySelection(groups, button.dataset.permissionGlobal);
        });
    });

    section.querySelectorAll('[data-permission-group]').forEach(function (group) {
        group.querySelectorAll('[data-permission-group-action]').forEach(function (button) {
            button.addEventListener('click', function () {
                applySelection(group, button.dataset.permissionGroupAction);
            });
        });
    });

    permissionCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const row = checkbox.closest('[data-permission-row]');
            if (!row) return;

            const viewCheckbox = row.querySelector('[data-permission-action="view"]');

            if (checkbox.dataset.permissionAction !== 'view' && checkbox.checked && viewCheckbox) {
                viewCheckbox.checked = true;
            }

            if (checkbox.dataset.permissionAction === 'view' && !checkbox.checked) {
                row.querySelectorAll('.ufp-permission-checkbox').forEach(function (rowCheckbox) {
                    rowCheckbox.checked = false;
                });
            }
        });
    });

    function syncAdminNotice() {
        const isAdmin = roleSelect && roleSelect.value === 'admin';
        adminNote?.classList.toggle('d-none', !isAdmin);
    }

    enabledSwitch?.addEventListener('change', setPermissionEnabledState);
    roleSelect?.addEventListener('change', syncAdminNotice);

    setPermissionEnabledState();
    syncAdminNotice();

    const form = section.closest('form');
    form?.addEventListener('submit', function () {
        const submitButton = form.querySelector('[data-user-submit]');
        if (!submitButton || submitButton.disabled) return;

        submitButton.disabled = true;
        const label = submitButton.querySelector('[data-submit-label]');
        if (label) label.textContent = 'กำลังบันทึก...';
    });
});
</script>
