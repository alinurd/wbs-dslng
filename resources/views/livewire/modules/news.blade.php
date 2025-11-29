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
                    'label' => 'Title Indonesia',
                    'required' => true,
                    'placeholder' => 'Masukan title....',
                    'error' => 'form.title_id',
                    'messages' => [
                        'required' => 'Title Indonesia wajib diisi',
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => 'Title English',
                    'model' => 'form.title_en',
                    'error' => 'form.title_en',
                    'required' => true,
                    'placeholder' => 'Masukkan nama dalam bahasa Inggris',
                    'messages' => [
                        'required' => 'Title English wajib diisi',
                    ],
                ],
        
                [
        'type' => 'text-editor',
        'model' => 'content_id',
        'label' => 'Content Indonesia',
        'error' => 'content_id',
        'required' => true,
        'placeholder' => 'Masukan content....',
        'messages' => [
            'required' => 'Content Indonesia wajib diisi',
        ],
    ],
    [
        'type' => 'text-editor', 
        'label' => 'Content English',
        'model' => 'content_en',
        'error' => 'content_en',
        'required' => true,
        'placeholder' => 'Masukkan content dalam bahasa Inggris',
        'messages' => [
            'required' => 'Content English wajib diisi',
        ],
    ],
        
                [
                    'type' => 'file',
                    'label' => 'Files',
                    'model' => 'form.files',
                    'error' => 'form.files',
                    'required' => true,
                    'multiple' => true,
                    'size' => '100', // MB
                    'format' =>
                        'ZIP, RAR, DOC, DOCX, XLS, XLSX, PPT, PPTX, PDF, JPG, JPEG, PNG, AVI, MP4, 3GP, MP3',
                    'placeholder' => 'Pilih file...',
                    'messages' => [
                        'required' => 'File wajib diisi',
                    ],
                ],
                [
                    'type' => 'file',
                    'label' => 'Gambar',
                    'model' => 'form.image',
                    'error' => 'form.image',
                    'format' => 'JPG, JPEG, PNG, GIF, WEBP',
                    'size' => '10',
                    'required' => false,
                    'multiple' => false,
                    'placeholder' => 'Pilih gambar...',
                    'messages' => [
                        'required' => 'Gambar wajib diisi',
                    ],
                ],
        
                [
                    'type' => 'select',
                    'label' => 'Category',
                    'model' => 'form.category', // Fixed typo: 'categry' to 'category'
                    'required' => true,
                    'options' => collect($newCategory)->mapWithKeys(function ($p) {
                            return [
                                $p->id => $p->data ?? ($p->data_id ?? ($p->data_en ?? 'No Data')),
                            ];
                        })->toArray(),
                    'error' => 'form.category',
                    'messages' => [
                        'required' => 'Category wajib dipilih',
                    ],
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
        @include('livewire.components.detail-modal-news', [
            'show' => $showDetailModal,
            'title' => $detailTitle,
            'data' => $detailData,
            'onClose' => 'closeDetailModal',
        ])
    </div>
</div>
