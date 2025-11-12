<livewire:components.table-index 
    :columns="['kelompok' => 'Kelompok', 'data' => 'Data', 'is_active' => 'Status']"
    :filters="[
        'kelompok' => ['type' => 'select', 'data' => ['HR', 'IT', 'Finance']],
        'data' => ['type' => 'text'],
        'is_active' => ['type' => 'radio', 'data' => ['Aktif', 'Nonaktif']]
    ]"
     :dataList="$dataList"
    :permissions="$permissions"
    :title="$title"
/>
