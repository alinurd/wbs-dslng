<div>
    <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? '' }}</h2>
    <div class="container-fluid py-4">
        <div class="w-full">
            <div class="mb-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                @include('livewire.components.action-bar', [
                    'permissions' => $permissions,
                    'selectedItems' => $selectedItems,
                    'onCreate' => 'create',
                    'onExportExcel' => "export('excel')",
                    'onExportPdf' => "export('pdf')",
                    'onDeleteBulk' => 'deleteBulk',
                ])
                <div class="flex items-center gap-2 justify-end sm:flex-1">
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
                    'off_label' => 'NONAKTIF',
                ],
            ],
        ])

        <!-- Include Filter Modal -->
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
                    'model' => 'filters.filterStatus',
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
