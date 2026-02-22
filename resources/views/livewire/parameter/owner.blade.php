<div>
    {{-- <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? '' }}</h2> --}}
    <div class="container-fluid py-4">
        <div class="container mx-auto">

            @include('livewire.components.table-wrapper', [
                'records' => $_records,
                'columns' => [
                    // 'kelompok' => 'Kelompok',
                    'owner_name_1' => 'Data Indonesia',
                    'owner_name' => 'Data English',
                    'is_active' => 'Status',
                    'created_at' => __('table.data.created_at'),
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
        'type' => 'select',
        'model' => 'form.parent_id',
        'label' => 'parent_id',
        'required' => true,
        'placeholder' => 'Pilih parent_id...',
        'error' => 'form.parent_id',
        'options' => [
            1 => 'Manager',
            2 => 'Supervisor',
            3 => 'Staff',
            4=> 'Administrator',
        ],
        // 'helper' => 'Pilih sesuai dengan posisi di perusahaan',
        'messages' => [
            'required' => 'parent_id wajib dipilih',
        ]
    ],
    
                [
                    'type' => 'text',
                    'model' => 'form.owner_name_1',
                    'label' => 'Data Indonesia',
                    'required' => true,
                    'placeholder' => __('table.input_placeh'),
                    'error' => 'form.owner_name_1', 
                ],
                [
                    'type' => 'text',
                    'label' => 'Data English',
                    'model' => 'form.owner_name',
                    'error' => 'form.owner_name',
                    'required' => true,
                    'placeholder' => __('table.input_placeh'),
                    
                ],
                [
                    'type' => 'switch-single',
                    'label' => 'Status Aktif',
                    'model' => 'form.is_active',
                    'error' => 'form.is_active',
                    'on_label' =>  __('table.data.on'),
                    'off_label' =>  __('table.data.off'),
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
                    'model' => 'filters.owner_name_1',
                     'placeholder' =>__('table.search'). ' data ',
                ],
                [
                    'type' => 'text',
                    'label' => 'Filter Data EN',
                    'model' => 'filters.owner_name', 
                    
                    'placeholder' =>__('table.search'). ' data ',
                ],
                 [
                    'type' => 'select',
                    'label' => 'Filter Status',
                    'model' => 'filters.is_active',
                     'options' => [
                        '1' => __('table.data.on'),
                        '0' => __('table.data.off'),
                    ],
                    'placeholder' => __('table.all') .' Status', 
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