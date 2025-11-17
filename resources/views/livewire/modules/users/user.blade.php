<div>
    <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? '' }}</h2>
    <div class="container-fluid py-4">
        <div class="container mx-auto">

            @include('livewire.components.table-wrapper', [
                'records' => $_records,
                'columns' => [
                    'name' => 'Name', 
                    'email' => 'Email', 
                    'role' => 'Role', 
                    'status' => 'Status', 
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
                    'model' => 'form.name',
                    'label' => 'Name',
                    'required' => true,
                    'placeholder' => 'Masukan Data....',
                    'error' => 'form.name',
                    'messages' => [
                        'required' => 'Name wajib diisi',
                    ]
                ],
                [
                    'type' => 'text',
                    'model' => 'form.email',
                    'label' => 'email',
                    'required' => true,
                    'placeholder' => 'Masukan Data....',
                    'error' => 'form.email',
                    'messages' => [
                        'required' => 'email wajib diisi',
                    ]
                ],
                [
                    'type' => 'password',
                    'model' => 'form.password',
                    'label' => 'password',
                    'required' => true,
                    'placeholder' => 'Masukan Data....',
                    'error' => 'form.password',
                    'messages' => [
                        'required' => 'password wajib diisi',
                    ]
                ],
                [
                    'type' => 'switch-single',
                    'label' => 'Status Aktif',
                    'model' => 'form.status',
                    'error' => 'form.status',
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
                    'label' => 'Filter Name Role',
                    'model' => 'filters.name',
                    'placeholder' => 'Cari Name Role...',
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