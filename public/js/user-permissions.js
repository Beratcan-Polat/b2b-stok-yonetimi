(() => {
    const section = document.querySelector('[data-user-permissions]');
    const roleSelect = document.getElementById('role_id');
    const roleMode = document.getElementById('role_mode');

    if (!section || !roleSelect) return;

    const fields = section.querySelector('[data-override-fields]');
    const boxes = [...fields.querySelectorAll('input[type="checkbox"]')];
    const overrides = JSON.parse(section.dataset.overrides || '{}');
    const rolePermissions = () => JSON.parse(roleSelect.selectedOptions[0]?.dataset.permissions || '[]').map(String);
    const remember = box => {
        if (box.checked === rolePermissions().includes(box.value)) delete overrides[box.value];
        else overrides[box.value] = box.checked ? 'allow' : 'deny';
    };

    const update = (reset = false) => {
        const creatingRole = roleMode?.value === 'new';
        const option = roleSelect.selectedOptions[0];
        const isSuperAdmin = option?.dataset.superAdmin === '1';
        const permissions = JSON.parse(option?.dataset.permissions || '[]').map(String);

        fields.disabled = isSuperAdmin || creatingRole;
        section.hidden = creatingRole;
        fields.hidden = isSuperAdmin;
        section.querySelector('[data-super-admin-note]').hidden = !isSuperAdmin;

        const existingRole = document.querySelector('[data-existing-role]');
        const newRoleFields = document.querySelector('[data-new-role-fields]');
        if (existingRole && newRoleFields) {
            existingRole.hidden = creatingRole;
            roleSelect.disabled = creatingRole;
            roleSelect.required = !creatingRole;
            newRoleFields.hidden = !creatingRole;
            newRoleFields.disabled = !creatingRole;
            document.getElementById('new_role_name').required = creatingRole;
        }

        if (reset) boxes.forEach(box => {
            box.checked = overrides[box.value] === 'allow'
                || (overrides[box.value] !== 'deny' && permissions.includes(box.value));
        });
    };

    boxes.forEach(box => box.addEventListener('change', () => {
        remember(box);
    }));

    roleSelect.addEventListener('change', () => update(true));
    roleMode?.addEventListener('change', () => update(true));
    update();
    boxes.forEach(remember);
})();
