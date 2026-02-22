<div>
    {{-- <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? '' }}</h2> --}}
    <div class="container-fluid py-4">
        <div class="container mx-auto">

            
@php 
    $processedRecords = $_records->map(function ($r) {
        return (object) array_merge(
            $r->toArray(),
            [ 
                'param_int' => $this->getComboById($r->param_int),
                 
            ]
        );
    });
 
    $finalRecords = new \Illuminate\Pagination\LengthAwarePaginator(
        $processedRecords,
        $_records->total(),
        $_records->perPage(),
        $_records->currentPage(),
        ['path' => request()->url(), 'pageName' => 'page']
    );
@endphp


            @include('livewire.components.table-wrapper', [
                'records' => $finalRecords,
                'columns' => [
                    // 'kelompok' => 'Kelompok',
                    'data_id' => 'Data Indonesia',
                    'data_en' => 'Data English',
                    'param_int' => 'Pertanyaan',
                    'is_active' => 'Status',
                    'created_at' => 'Dibuat Pada',
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
                    'type' => 'select',
                    'label' => 'Pertanyaan',
                    'model' => 'form.param_int',
                    'required' => true,
                     'options' => collect($faqDropdown)->mapWithKeys(function ($p) {
                                 return [
                                     $p->id => $p->data ?? $p->data_id ?? $p->data_en ?? 'No Data'
                                    ];
                                })->toArray(), 
                ],

                [
                    'type' => 'textarea',
                    'model' => 'form.data_id',
                    'label' => 'Data Indonesia',
                    'required' => true,
                    'placeholder' => 'Masukan Data....',
                    'error' => 'form.data_id',
                    'helper_bottom'=>'berikan penjelasan/jawab dalam bahasa indonesia'
                ],
                [
                    'type' => 'textarea',
                    'label' => 'Data English',
                    'model' => 'form.data_en',
                    'error' => 'form.data_en',
                    'required' => true,
                    'placeholder' => 'Masukkan nama dalam bahasa Inggris',
                    'helper_bottom'=>'berikan penjelasan/jawab dalam bahasa ingriss'

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