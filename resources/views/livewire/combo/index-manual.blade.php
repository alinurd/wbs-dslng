<!-- Di file index.blade.php Anda -->
<div>
    <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? '' }}</h2>
    <div class="container-fluid py-4">

        <!-- Header Section -->
        <div class="w-full">
            <div class="mb-4 flex flex-col sm:flex-row items-center gap-3">
                <!-- Action Buttons -->
                @include('livewire.components.action-bar', [
                    'permissions' => $permissions,
                    'selectedItems' => $selectedItems,
                    'onCreate' => 'create',
                    'onExportExcel' => "export('excel')",
                    'onExportPdf' => "export('pdf')",
                    'onDeleteBulk' => 'deleteBulk',
                ])

                <!-- Search & Filter -->
                <div class="flex items-center gap-2 sm:flex-1">
                    @include('livewire.components.search-filter', [
                        'perPage' => $perPage,
                        'search' => $search,
                        'filterMode' => $filterMode,
                        'onPerPageChange' => 'perPage',
                        'onSearch' => 'search',
                        'onOpenFilter' => 'openFilter',
                        'onResetFilter' => 'resetFilter',
                    ])
                </div>
            </div>

            <!-- Table -->
            @include('livewire.components.table', [
                'records' => $_records,
                'selectedItems' => $selectedItems,
                'permissions' => $permissions,
                'columns' => [
                    'kelompok' => 'Kelompok',
                    'data' => 'Data',
                    'param_int' => 'Param Int',
                    'param_str' => 'Param Str',
                    'is_active' => 'Status',
                    'created_at' => 'Dibuat Pada',
                ],
                // 'sortBy' => $sortBy,
                // 'getSortIcon' => $getSortIcon,
                'onSort' => 'sortBy',
                'onView' => 'view',
                'onEdit' => 'edit',
                'onDelete' => 'delete',
                'onSelectItem' => 'selectedItems',
                'firstItem' => $_records->firstItem(),
            ])

            <!-- Pagination -->
            <!-- ... your pagination code -->
        </div>

        <!-- Form Modal -->


        <!-- Filter Modal -->
        @include('livewire.components.form-filtering', [
            'showFilterModal' => $showFilterModal,
            'filters' => [
                [
                    'type' => 'text',
                    'label' => 'Filter Kelompok',
                    'model' => 'filterKelompok',
                    'placeholder' => 'Cari kelompok...',
                ],
                [
                    'type' => 'select',
                    'label' => 'Filter Status',
                    'model' => 'filterStatus',
                    'options' => [
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
                    ],
                    'placeholder' => 'Semua Status',
                ],
            ],
            'onClose' => 'closeFilterModal',
            'onReset' => 'resetFilter',
            'onApply' => 'applyFilter',
        ])



        @include('livewire.components.form', [
            'showModal' => $showModal,
            'updateMode' => $updateMode,
            'form' => $form,
            'onClose' => 'closeModal',
            'onSave' => 'save',
            'size' => 'md',
            'title' => 'Combo',
            'fields' => [
                // TEXT
                [
                    'type' => 'text',
                    'label' => 'Nama Lengkap',
                    'model' => 'form.nama',
                    'error' => 'nama',
                    'required' => true,
                    'placeholder' => 'Masukkan nama lengkap',
                    'helper' => 'Nama lengkap sesuai KTP',
                ],
        
                // NUMBER
                [
                    'type' => 'number',
                    'label' => 'Umur',
                    'model' => 'form.umur',
                    'error' => 'umur',
                    'min' => 0,
                    'max' => 100,
                    'placeholder' => 'Masukkan umur',
                ],
        
                // EMAIL
                [
                    'type' => 'email',
                    'label' => 'Email',
                    'model' => 'form.email',
                    'error' => 'email',
                    'required' => true,
                    'placeholder' => 'email@contoh.com',
                ],
        
                // PASSWORD
                [
                    'type' => 'password',
                    'label' => 'Password',
                    'model' => 'form.password',
                    'error' => 'password',
                    'placeholder' => 'Masukkan password',
                ],
        
                // SELECT
                [
                    'type' => 'select',
                    'label' => 'Jenis Kelamin',
                    'model' => 'form.jenis_kelamin',
                    'error' => 'jenis_kelamin',
                    'options' => [
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ],
                    'placeholder' => 'Pilih jenis kelamin',
                ],
        
                // TEXTAREA
                [
                    'type' => 'textarea',
                    'label' => 'Alamat',
                    'model' => 'form.alamat',
                    'error' => 'alamat',
                    'rows' => 4,
                    'placeholder' => 'Masukkan alamat lengkap',
                    'colspan' => 2,
                ],
        
                // CHECKBOX
                [
                    'type' => 'checkbox',
                    'label' => 'Status Aktif',
                    'model' => 'form.is_active',
                    'checkbox_label' => 'Aktifkan user',
                ],
        
                // RADIO
                [
                    'type' => 'radio',
                    'label' => 'Status Pernikahan',
                    'model' => 'form.status_nikah',
                    'error' => 'status_nikah',
                    'options' => [
                        'belum' => 'Belum Menikah',
                        'menikah' => 'Sudah Menikah',
                        'cerai' => 'Cerai',
                    ],
                ],
                // SWITCH MULTI OPTION
        [
            'type' => 'switch',
            'label' => 'Status Pernikahan',
            'model' => 'form.status_nikah',
            'error' => 'status_nikah',
            'helper' => 'Pilih status pernikahan Anda',
            'options' => [
                'belum' => 'Belum Menikah',
                'menikah' => 'Sudah Menikah', 
                'cerai' => 'Cerai'
            ],
        ],

        // SWITCH SINGLE (ON/OFF)
        [
            'type' => 'switch-single',
            'label' => 'Status Aktif',
            'model' => 'form.is_active',
            'error' => 'is_active',
            'helper' => 'Aktifkan atau nonaktifkan data ini',
            'on_label' => 'Aktif',
            'off_label' => 'Nonaktif'
        ],

        // SWITCH DENGAN OPTION BOOLEAN
        [
            'type' => 'switch',
            'label' => 'Status User',
            'model' => 'form.status_user',
            'error' => 'status_user',
            'helper' => 'Tentukan status user',
            'options' => [
                1 => 'Aktif',
                0 => 'Tidak Aktif'
            ],
        ],
        
                // DATE
                [
                    'type' => 'date',
                    'label' => 'Tanggal Lahir',
                    'model' => 'form.tanggal_lahir',
                    'error' => 'tanggal_lahir',
                ],
        
                // TIME
                [
                    'type' => 'time',
                    'label' => 'Waktu Meeting',
                    'model' => 'form.waktu_meeting',
                    'error' => 'waktu_meeting',
                ],
        
                // DATETIME
                [
                    'type' => 'datetime',
                    'label' => 'Tanggal & Waktu',
                    'model' => 'form.tanggal_waktu',
                    'error' => 'tanggal_waktu',
                ],
        
                // FILE
                [
                    'type' => 'file',
                    'label' => 'Upload Foto',
                    'model' => 'form.foto',
                    'error' => 'foto',
                    'accept' => 'image/*',
                ],
        
                // COLOR
                [
                    'type' => 'color',
                    'label' => 'Warna Favorit',
                    'model' => 'form.warna',
                    'error' => 'warna',
                ],
        
                // RANGE
                [
                    'type' => 'range',
                    'label' => 'Tingkat Kepuasan',
                    'model' => 'form.kepuasan',
                    'error' => 'kepuasan',
                    'min' => 0,
                    'max' => 10,
                ],
        
                // URL
                [
                    'type' => 'url',
                    'label' => 'Website',
                    'model' => 'form.website',
                    'error' => 'website',
                    'placeholder' => 'https://example.com',
                ],
        
                // TEL
                [
                    'type' => 'tel',
                    'label' => 'Nomor Telepon',
                    'model' => 'form.telepon',
                    'error' => 'telepon',
                    'placeholder' => '08123456789',
                ],
        
                // READONLY
                [
                    'type' => 'readonly',
                    'label' => 'ID User',
                    'value' => 'USR-001',
                ],
        
                // CUSTOM HTML
                [
                    'type' => 'custom',
                    'label' => 'Informasi Tambahan',
                    'html' =>
                        '<div class="bg-blue-50 p-3 rounded-lg"><p class="text-blue-700">Ini adalah informasi custom</p></div>',
                ],
            ],
        ])


    </div>
</div>
