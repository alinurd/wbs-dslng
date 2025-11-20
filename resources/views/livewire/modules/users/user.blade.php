<div>
    <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? '' }}</h2>
    <div class="container-fluid py-4">
        <div class="container mx-auto">

            @include('livewire.components.table-wrapper', [
                'records' => $_records,
                'columns' => [
                    'name' => 'Name', 
                    'email' => 'Email',
                    'roles' => 'Roles',
                    'is_active' => 'Status', 
                ],
                'selectedItems' => $selectedItems,
                'permissions' => $permissions,
                'extraActions' => [
                    [
                        'label' => 'Reset Password',
                        'method' => 'resetPassword',
                        'icon' => 'fas fa-key',
                        'permission' => 'users.edit',
                        'class' => 'text-orange-600 hover:text-orange-900'
                    ],
                    [
                        'label' => 'Toggle Status',
                        'method' => 'toggleStatus',
                        'icon' => 'fas fa-power-off',
                        'permission' => 'users.edit',
                        'class' => 'text-purple-600 hover:text-purple-900'
                    ]
                ],
            
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
                    'model' => 'form.name',
                    'label' => 'Name',
                    'required' => true,
                    'placeholder' => 'Masukan nama user...',
                    'error' => 'form.name',
                    'messages' => [
                        'required' => 'Name wajib diisi',
                    ]
                ],
                [
                    'type' => 'email',
                    'model' => 'form.email',
                    'label' => 'Email',
                    'required' => true,
                    'placeholder' => 'Masukan email...',
                    'error' => 'form.email',
                    'messages' => [
                        'required' => 'Email wajib diisi',
                    ]
                ],
                [
                    'type' => 'password',
                    'model' => 'form.password',
                    'label' => 'Password',
                    'required' => !$updateMode, // Required hanya untuk create
                    'placeholder' => $updateMode ? 'Kosongkan jika tidak ingin mengubah password' : 'Masukan password...',
                    'error' => 'form.password',
                    'messages' => [
                        'required' => 'Password wajib diisi',
                    ],
                    'helper_bottom' => $updateMode ? 'Biarkan kosong jika tidak ingin mengubah password' : 'Password minimal 8 karakter'
                ],
                [
                    'type' => 'checkbox-roles',
                    'label' => 'Roles',
                    'model' => 'selectedRoles',
                    'error' => 'selectedRoles',
                    'options' => collect($RolesList)->mapWithKeys(function ($role) {
                        return [
                            $role->id => $role->name
                        ];
                    })->toArray(),
                    'required' => true,
                    'messages' => [
                        'required' => 'Pilih minimal satu role',
                    ]
                ],
                [
                    'type' => 'switch-single',
                    'label' => 'Status Aktif',
                    'model' => 'form.is_active',
                    'error' => 'form.is_active',
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
                    'label' => 'Cari User',
                    'model' => 'filters.search',
                    'placeholder' => 'Cari nama atau email...',
                ],
                [
                    'type' => 'select',
                    'label' => 'Filter Role',
                    'model' => 'filters.role_id',
                    'options' => collect($RolesList)->mapWithKeys(function ($role) {
                        return [
                            $role->id => $role->name
                        ];
                    })->toArray(),
                    'placeholder' => 'Semua Roles', 
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