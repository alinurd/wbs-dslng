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


    // Di bagian Alpine.js
validateAndUploadFiles(files) {
    const validFiles = [];
    const invalidFiles = [];
    
    files.forEach(file => {
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const fileSize = file.size;
        
        if (this.allowedTypes.includes(fileExtension) && fileSize <= this.maxSize) {
            validFiles.push(file);
        } else {
            invalidFiles.push({
                name: file.name,
                reason: !this.allowedTypes.includes(fileExtension) ? 'Tipe file tidak diizinkan' : 'File terlalu besar'
            });
        }
    });
    
    // Upload valid files - pastikan ini memanggil Livewire method dengan benar
    if (validFiles.length > 0) {
        @this.call('uploadMultipleFiles', validFiles);
    }
    
    // Show error for invalid files
    if (invalidFiles.length > 0) {
        this.showInvalidFilesError(invalidFiles);
    }
},
