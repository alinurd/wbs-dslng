<div>
    <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? '' }}</h2>
    <div class="container-fluid py-4">
        <div class="container mx-auto">

            @include('livewire.components.table-wrapper', [
                'records' => $_records,
                'columns' => [
                    // 'kelompok' => 'Kelompok',
                    'title_id' => 'title Indonesia',
                    'title_en' => 'title English',
                    'is_active' => 'Status',
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
            'size' => 'md',
            'cols' => 2,
            'fields' => [
                [
                    'type' => 'text',
                    'model' => 'form.title_id',
                    'label' => 'title Indonesia',
                    'required' => true,
                    'placeholder' => 'Masukan title....',
                    'error' => 'form.title_id',
                    'messages' => [
                        'required' => 'title Indonesia wajib diisi',
                    ]
                ],
                [
                    'type' => 'text',
                    'label' => 'title English',
                    'model' => 'form.title_en',
                    'error' => 'form.title_en',
                    'required' => true,
                    'placeholder' => 'Masukkan nama dalam bahasa Inggris',
                    'messages' => [
                        'required' => 'title English wajib diisi',
                    ]
                ],

                [
                    'type' => 'text-editor',
                    'model' => 'form.content_id',
                    'label' => 'content Indonesia',
                    'required' => true,
                    'placeholder' => 'Masukan content....',
                    'error' => 'form.content_id',
                    'messages' => [
                        'required' => 'content Indonesia wajib diisi',
                    ]
                ],
                [
                    'type' => 'text-editor',
                    'label' => 'content English',
                    'model' => 'form.content_en',
                    'error' => 'form.content_en',
                    'required' => true,
                    'placeholder' => 'Masukkan nama dalam bahasa Inggris',
                    'messages' => [
                        'required' => 'content English wajib diisi',
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