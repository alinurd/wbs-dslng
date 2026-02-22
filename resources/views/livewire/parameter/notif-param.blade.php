<div>
    {{-- <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? '' }}</h2> --}}
    <div class="container-fluid py-4">
        <div class="container mx-auto">
{{-- {{dd($_records)}} --}}
            @include('livewire.components.table-wrapper', [
                'records' => $_records,
                'columns' => [
                    // 'kelompok' => 'Kelompok',
                    'data_id' => 'Data Indonesia',
                    // 'param_int' => 'Jam',
                    // 'param_int_1' => 'Hari',
                    'data_en' => 'Data English',
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
                    'type' => 'text',
                    'model' => 'form.data_id',
                    'label' => 'Data Indonesia',
                    'required' => true,
                    'error' => 'form.data_id',
                    'placeholder' => __('table.input_placeh'),
                    'messages' => [
                        'placeholder' => __('table.input_placeh'),
                    ]
                ],
                [
                    'type' => 'text',
                    'label' => 'Data English',
                    'model' => 'form.data_en',
                    'error' => 'form.data_en',
                    'required' => true,
                    'placeholder' => __('table.input_placeh'),
                    'messages' => [
                        'placeholder' => __('table.input_placeh')
                    ]
                ],
                [
                    'type' => 'number',
                    'label' => __('table.data.date'),
                    'model' => 'form.param_int',
                    'error' => 'form.param_int',
                    'required' => true,
                    'placeholder' => __('table.input_placeh'),
                ],
                [
                    'type' => 'number',
                    'label' =>  __('table.data.day'),
                    'model' => 'form.param_int_1',
                    'error' => 'form.param_int_1',
                    'required' => true,
                    'placeholder' => __('table.input_placeh'),
                ],
                [
                    'type' => 'switch-single',
                    'label' => 'Status Aktif',
                    'model' => 'form.is_active',
                    'error' => 'form.is_active',
                    'on_label' => __('table.data.on'),
                    'off_label' => __('table.data.off'),
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
                    'placeholder' =>__('table.search'). ' data ID...',
                ],
                [
                    'type' => 'text',
                    'label' => 'Filter Data EN',
                    'model' => 'filters.data_en', 
                    'placeholder' =>__('table.search'). ' data En...',
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