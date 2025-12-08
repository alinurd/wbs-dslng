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
                    'user_id' => 'Username',
                    'table_name' => 'Table/Modules',
                    'action' => 'Action',
                    'ip_address' => 'Ip Address',
                    'created_at' => 'Access Time',
                    //  'new_values' => 'New values',
                    //  'old_values' => 'Old Values',
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
                    'label' => 'Filter Username',
                    'model' => 'filters.user_id',
                    'placeholder' => 'Cari Username ID...',
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