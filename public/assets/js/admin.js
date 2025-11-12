lucide.createIcons();

    const sidebar = document.getElementById('sidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const openBtn = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('closeSidebar');

    openBtn?.addEventListener('click', () => {
        sidebar.classList.add('open');
        sidebarBackdrop.classList.remove('hidden');
    });
    
    closeBtn?.addEventListener('click', () => {
        sidebar.classList.remove('open');
        sidebarBackdrop.classList.add('hidden');
    });
    
    sidebarBackdrop?.addEventListener('click', () => {
        sidebar.classList.remove('open');
        sidebarBackdrop.classList.add('hidden');
    });