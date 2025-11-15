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




function notificationHandler() {
        return {
            show: false,
            type: '',
            message: '',
            timer: null,
            
            showNotification(detail) {
                console.log('Detail received:', detail); // Debug log
                
                // Handle both object and array formats
                let notificationData;
                if (Array.isArray(detail)) {
                    // Jika detail adalah array, ambil element pertama
                    notificationData = detail[0];
                } else {
                    // Jika detail adalah object langsung
                    notificationData = detail;
                }
                
                console.log('Processed data:', notificationData); // Debug log
                
                this.type = notificationData.type;
                this.message = notificationData.message;
                this.show = true;
                
                // Clear existing timer
                if (this.timer) {
                    clearTimeout(this.timer);
                }
                
                // Auto hide after 5 seconds
                this.timer = setTimeout(() => {
                    this.show = false;
                }, 5000);
            }
        }
    }

    // Handle session flash messages
    document.addEventListener('DOMContentLoaded', function() {
        // Check for existing flash messages in session
        @if (session()->has('success'))
            Livewire.dispatch('notify', {
                type: 'success',
                message: '{{ session('success') }}'
            });
        @endif

        @if (session()->has('error'))
            Livewire.dispatch('notify', {
                type: 'error', 
                message: '{{ session('error') }}'
            });
        @endif

        @if (session()->has('warning'))
            Livewire.dispatch('notify', {
                type: 'warning',
                message: '{{ session('warning') }}'
            });
        @endif

        @if (session()->has('info'))
            Livewire.dispatch('notify', {
                type: 'info',
                message: '{{ session('info') }}'
            });
        @endif
    });
    