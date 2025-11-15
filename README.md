'fields' => [
    // ==================== TEXT INPUT ====================
    [
        'type' => 'text',
        'model' => 'form.nama_lengkap',
        'label' => 'Nama Lengkap',
        'required' => true,
        'placeholder' => 'Masukkan nama lengkap...',
        'error' => 'form.nama_lengkap',
        'colspan' => 2, // Opsional: melebar 2 kolom
        'helper' => 'Masukkan nama lengkap sesuai KTP',
        'helper_bottom' => 'Maksimal 100 karakter',
        'messages' => [
            'required' => 'Nama lengkap wajib diisi',
            'max' => 'Nama tidak boleh lebih dari 100 karakter',
        ]
    ],

    // ==================== EMAIL INPUT ====================
    [
        'type' => 'email',
        'model' => 'form.email',
        'label' => 'Alamat Email',
        'required' => true,
        'placeholder' => 'contoh@email.com',
        'error' => 'form.email',
        'helper' => 'Email akan digunakan untuk verifikasi',
        'messages' => [
            'required' => 'Email wajib diisi',
            'email' => 'Format email tidak valid',
        ]
    ],

    // ==================== PASSWORD INPUT ====================
    [
        'type' => 'password',
        'model' => 'form.password',
        'label' => 'Password',
        'required' => true,
        'placeholder' => 'Masukkan password minimal 8 karakter',
        'error' => 'form.password',
        'helper' => 'Password harus mengandung huruf dan angka',
        'messages' => [
            'required' => 'Password wajib diisi',
            'min' => 'Password minimal 8 karakter',
        ]
    ],

    // ==================== NUMBER INPUT ====================
    [
        'type' => 'number',
        'model' => 'form.umur',
        'label' => 'Umur',
        'required' => true,
        'placeholder' => 'Masukkan umur',
        'error' => 'form.umur',
        'helper' => 'Hanya angka yang diperbolehkan',
        'messages' => [
            'required' => 'Umur wajib diisi',
            'numeric' => 'Umur harus berupa angka',
            'min' => 'Umur minimal 17 tahun',
        ]
    ],

    // ==================== MONEY INPUT ====================
    [
        'type' => 'money',
        'model' => 'form.gaji',
        'label' => 'Gaji Bulanan',
        'required' => true,
        'placeholder' => 'Rp 0',
        'error' => 'form.gaji',
        'helper' => 'Masukkan jumlah gaji tanpa titik',
        'messages' => [
            'required' => 'Gaji wajib diisi',
            'numeric' => 'Gaji harus berupa angka',
        ]
    ],

    // ==================== TEXTAREA ====================
    [
        'type' => 'textarea',
        'model' => 'form.alamat',
        'label' => 'Alamat Lengkap',
        'required' => true,
        'placeholder' => 'Masukkan alamat lengkap...',
        'error' => 'form.alamat',
        'rows' => 4,
        'colspan' => 2, // Textarea biasanya lebar 2 kolom
        'helper' => 'Sertakan kecamatan, kota, dan kode pos',
        'messages' => [
            'required' => 'Alamat wajib diisi',
        ]
    ],

    // ==================== SELECT DROPDOWN ====================
    [
        'type' => 'select',
        'model' => 'form.jabatan',
        'label' => 'Jabatan',
        'required' => true,
        'placeholder' => 'Pilih jabatan...',
        'error' => 'form.jabatan',
        'options' => [
            'manager' => 'Manager',
            'supervisor' => 'Supervisor',
            'staff' => 'Staff',
            'admin' => 'Administrator',
        ],
        'helper' => 'Pilih sesuai dengan posisi di perusahaan',
        'messages' => [
            'required' => 'Jabatan wajib dipilih',
        ]
    ],

    // ==================== CHECKBOX ====================
    [
        'type' => 'checkbox',
        'model' => 'form.setuju_syarat',
        'label' => 'Persetujuan',
        'checkbox_label' => 'Saya menyetujui syarat dan ketentuan',
        'required' => true,
        'error' => 'form.setuju_syarat',
        'helper' => 'Centang untuk melanjutkan',
        'messages' => [
            'required' => 'Anda harus menyetujui syarat dan ketentuan',
        ]
    ],

    // ==================== RADIO GROUP ====================
    [
        'type' => 'radio',
        'model' => 'form.jenis_kelamin',
        'label' => 'Jenis Kelamin',
        'required' => true,
        'error' => 'form.jenis_kelamin',
        'options' => [
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
        ],
        'helper' => 'Pilih jenis kelamin',
        'messages' => [
            'required' => 'Jenis kelamin wajib dipilih',
        ]
    ],

    // ==================== DATE INPUT ====================
    [
        'type' => 'date',
        'model' => 'form.tanggal_lahir',
        'label' => 'Tanggal Lahir',
        'required' => true,
        'error' => 'form.tanggal_lahir',
        'helper' => 'Format: DD/MM/YYYY',
        'messages' => [
            'required' => 'Tanggal lahir wajib diisi',
            'date' => 'Format tanggal tidak valid',
        ]
    ],

    // ==================== SWITCH SINGLE ====================
    [
        'type' => 'switch-single',
        'model' => 'form.status_aktif',
        'label' => 'Status Aktif',
        'error' => 'form.status_aktif',
        'on_label' => 'AKTIF',
        'off_label' => 'NONAKTIF',
        'helper' => 'Aktifkan atau nonaktifkan pengguna',
    ],
],