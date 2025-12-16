<div>
    <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? '' }}</h2>
    <div class="container-fluid py-4">
        <div class="container mx-auto">

            @include('livewire.components.table-wrapper', [
                'records' => $_records,
                'columns' => [
                    // 'kelompok' => 'Kelompok',
                    'mailer' => 'Mailer',
                    'host' => 'Host',
                    'port' => 'Port',
                    'username' => 'Username',
                    'created_at' => 'Dibuat Pada',
                ],
                'selectedItems' => $selectedItems,
                'permissions' => $permissions,
            
                // State
                'perPage' => $perPage,
                'search' => $search,
                'filterMode' => $filterMode,
                'firstItem' => $_records->firstItem(),
            
                // Actions
                'onCreate' => 'create',
                'onExportExcel' => "export('excel')",
                'onExportPdf' => "export('pdf')",
                'onDeleteBulk' => 'deleteBulk',
                'onPerPageChange' => 'perPage',
                'onSearch' => 'search',
                'onOpenFilter' => 'openFilter',
                'onResetFilter' => 'resetFilter',
                'onSort' => 'sortBy',
                'onView' => 'view',
                'onEdit' => 'edit',
                'onDelete' => 'delete',
                'onSelectItem' => 'selectedItems',
            ])
        </div>

        <!-- Form Modal -->
        @include('livewire.components.form', [
            'showModal' => $showModal,
            'updateMode' => $updateMode,
            'form' => $form,
            'onClose' => 'closeModal',
            'onSave' => 'save',
            'title' => $title,
            'size' => 'xl',
            'cols' => 1,
            'fields' => [
     
    [
        'type' => 'text',
        'model' => 'form.mailer',
        'label' => 'Mailer',
        'required' => true,
        'placeholder' => 'Masukan Mailer....',
        'error' => 'form.mailer',
        'messages' => [
            'required' => 'mailer wajib diisi',
        ],
    ],
    [
        'type' => 'text',
        'model' => 'form.host',
        'label' => 'SMTP Host',
        'required' => true,
        'placeholder' => 'Masukan SMTP host....',
        'error' => 'form.host',
        'messages' => [
            'required' => 'Host wajib diisi',
        ],
    ],
    [
        'type' => 'number',
        'model' => 'form.port',
        'label' => 'Port',
        'required' => true,
        'placeholder' => 'Masukan port....',
        'error' => 'form.port',
        'messages' => [
            'required' => 'Port wajib diisi',
        ],
    ],
     [
        'type' => 'text',
        'model' => 'form.encryption',
        'label' => 'Encryption',
        'required' => false,
        'placeholder' => 'Masukan Encryption....',
        'error' => 'form.encryption',
        'messages' => [
            'required' => 'Encryption wajib diisi',
        ],
    ],

    
    [
        'type' => 'email',
        'model' => 'form.username',
        'label' => 'Username/Email',
        'required' => true,
        'placeholder' => 'Masukan username/email....',
        'error' => 'form.username',
        'messages' => [
            'required' => 'Username wajib diisi',
        ],
    ],
    [
        'type' => 'password',
        'model' => 'form.password',
        'label' => 'Password',
        'required' => true,
        'placeholder' => 'Masukan password....',
        'error' => 'form.password',
        'messages' => [
            'required' => 'Password wajib diisi',
        ],
    ],
    [
        'type' => 'email',
        'model' => 'form.from_address',
        'label' => 'From Address',
        'required' => true,
        'placeholder' => 'Masukan from address....',
        'error' => 'form.from_address',
        'messages' => [
            'required' => 'From address wajib diisi',
        ],
    ],
    [
        'type' => 'text',
        'model' => 'form.from_name',
        'label' => 'From Name',
        'required' => true,
        'placeholder' => 'Masukan from name....',
        'error' => 'form.from_name',
        'messages' => [
            'required' => 'From name wajib diisi',
        ],
    ],
    [
        'type' => 'switch-single',
        'label' => 'Status Aktif',
        'model' => 'form.active',
        'error' => 'form.active',
        'on_label' => 'AKTIF',
        'off_label' => 'NONAKTIF',
    ],
], 
        ])

        <!-- Filter Modal -->
        @include('livewire.components.form-filtering', [
            'showFilterModal' => $showFilterModal,
            'filters' => [
                [
                    'type' => 'text',
                    'label' => 'Filter Data ID',
                    'model' => 'filters.data_id',
                    'placeholder' => 'Cari data ID...',
                ],
                [
                    'type' => 'text',
                    'label' => 'Filter Data EN',
                    'model' => 'filters.data_en',
                    'placeholder' => 'Cari data EN...',
                ],
                [
                    'type' => 'select',
                    'label' => 'Filter Status',
                    'model' => 'filters.is_active',
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

        <!-- Detail Modal -->
        @include('livewire.components.detail-modal', [
            'show' => $showDetailModal,
            'title' => $detailTitle,
            'data' => $detailData,
            'onClose' => 'closeDetailModal',
        ])
    </div>
</div>
