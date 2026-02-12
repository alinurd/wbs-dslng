<div>
    <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? '' }}</h2>
    <div class="container-fluid py-4">
        <div class="container mx-auto">

            @php
    // Process records untuk menambahkan kolom custom
    $processedRecords = $_records->map(function ($record) {
        return (object) array_merge(
            $record->toArray(),
            [
                'user_id' => $this->getNamaUser($record),
            ]
        );
    });

    // Buat paginator baru dengan data yang sudah diproses
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
                    'user_id' => __('table.username'),
                    'table_name' => __('table.table_name'),
                    'action' => __('table.action'),
                    'ip_address' => __('table.ip_address'),
                    'created_at' => __('table.access_time'),
                    // 'new_values' => __('table.new_values'),
                    // 'old_values' => __('table.old_values'),
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
 
        <!-- Filter Modal -->
        @include('livewire.components.form-filtering', [
            'showFilterModal' => $showFilterModal,
            'filters' => [
                [
                    'type' => 'text',
                    'label' => 'Filter '. __('table.username'),
                    'model' => 'filters.user_id',
                    'placeholder' => __('table.search') .' '. __('table.username'),
                ],
                
            ],
            'onClose' => 'closeFilterModal',
            'onReset' => 'resetFilter',
            'onApply' => 'applyFilter',
        ])

        <!-- Detail Modal -->
        @include('livewire.components.detail-modal-trail', [
            'show' => $showDetailModal,
            'title' => $detailTitle,
            'data' => $detailData,
            'onClose' => 'closeDetailModal',
        ])
    </div>
</div>