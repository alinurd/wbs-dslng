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
                    'data_id' => 'Data Id',
                    'data_en' => 'Data En',
                    'is_active' => 'Status',
                    'created_at' => 'Dibuat Pada',
                ],
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
            'placeholder' => 'Masukan Data....',
            'error' => 'form.data_id',
        ],
        [
            'type' => 'text',
            'label' => 'Data English', 
            'model' => 'form.data_en',
            'error' => 'form.data_en',
            'required' => true,
            'placeholder' => 'Masukkan nama dalam bahasa Inggris',
        ],
        [
            'type' => 'switch-single',
            'label' => 'Status Aktif',
            'model' => 'form.is_active',
            'error' => 'form.is_active',
            'on_label' => 'AKTIF',
            'off_label' => 'NONAKTIF'
        ],
    ]
])
@include('livewire.components.form-filtering', [
            'showFilterModal' => $showFilterModal,
            'filters' => [
                [
                    'type' => 'text',
                    'label' => 'Filter Kelompok',
                    'model' => 'kelompok',
                    'placeholder' => 'Cari kelompok...',
                ],
                [
                    'type' => 'text',
                    'label' => 'Filter Data ID',
                    'model' => 'data_id',
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

    </div>
</div>
