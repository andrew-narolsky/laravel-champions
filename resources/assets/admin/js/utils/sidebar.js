document.querySelector('[data-toggle="minimize"]')?.addEventListener('click', () => {
    document.body.classList.toggle('sidebar-icon-only');
    const collapsed = document.body.classList.contains('sidebar-icon-only');
    document.cookie = `sidebar-status=${collapsed ? 'true' : 'false'};path=/;max-age=31536000`;
});