<?php

namespace App\Livewire\Modules;

use App\Livewire\Root;
use App\Models\Audit as AuditModel;

class AuditTrail extends Root
{
    public $title = "Audit";
    public $views = 'modules.audit';
    public $model = AuditModel::class;
    public $modul = 'audit';
    public $kel = 'combo';
    
    public $form = [
      
    ];

    public $rules = [
        
    ];

    protected $messages = [
      
    ];
 
     public $filters = [
        'username' => '',
        'user_id' => '',
        'table_name' => '',
        'action' => '',
    ];
   
public function columns(): array
{
    return ['table_name', 'user_id', 'action'];
}

public function query()
{
    $query = ($this->model)::with([ 'user']);
    
    $this->applySearch($query);
    $this->applyFilters($query);
    
    return $query;
}

protected function applySearch($query): void
{
    if (!$this->search || !method_exists($this, 'columns')) {
        return;
    }
    
    $columns = $this->columns();
    
    if (!is_array($columns) || empty($columns)) {
        return;
    }
    
    $query->where(function ($subQuery) use ($columns) {
        foreach ($columns as $column) {
            if ($column === 'user_id') {
                $this->applyUserSearch($subQuery);
            } else {
                $subQuery->orWhere($column, 'like', "%{$this->search}%");
            }
        }
    });
}

protected function applyUserSearch($query): void
{
    $query->orWhereHas('user', function ($userQuery) {
        $userQuery->where('name', 'like', "%{$this->search}%")
                 ->orWhere('username', 'like', "%{$this->search}%");
    });
}

protected function applyFilters($query): void
{
    if (!is_array($this->filters)) {
        return;
    }
    
    foreach ($this->filters as $key => $value) {
        if ($this->isValidFilterValue($value)) {
            $query->where($key, 'like', "%{$value}%");
        }
    }
}

protected function isValidFilterValue($value): bool
{
    return $value !== '' && $value !== null;
}


    public function getNamaUser($record)
    {
        return $record->user->name ?? $record->user->name ?? 'N/A';
    }
    
   public function view($id)
{
    can_any([strtolower($this->modul) . '.view']);
    
    $record = $this->model::findOrFail($id);

    // Format action labels
    $actionMap = [
        'created' => ['label' => 'Dibuat', 'color' => 'success', 'icon' => 'plus'],
        'updated' => ['label' => 'Diperbarui', 'color' => 'primary', 'icon' => 'edit'],
        'deleted' => ['label' => 'Dihapus', 'color' => 'danger', 'icon' => 'trash'],
    ];
    
    $actionInfo = $actionMap[$record->action] ?? 
                 ['label' => ucfirst($record->action), 'color' => 'secondary', 'icon' => 'history'];

    $this->detailData = [
        'common' => [
            'Action' => [
                'value' => $record->action,
                'label' => $actionInfo['label'],
                'color' => $actionInfo['color'],
                'icon' => $actionInfo['icon'],
            ],
            'Tabel/Module' => $record->table_name,
            'User' => [
                'value' => $record->user ? $record->user->name : 'System',
                'email' => $record->user ? $record->user->email : null,
            ],
            'Dibuat Pada' => $record->created_at->format('d/m/Y H:i:s'),
            'IP Address' => $record->ip_address,
            'User Agent' => $this->formatUserAgent($record->user_agent),
        ],
        'Data Baru' => $this->processJsonForDisplay($record->new_values),
        'Data Lama' => $this->processJsonForDisplay($record->old_values),
    ];
    
    $this->detailTitle = "Detail Audit Log #{$record->id}";
    $this->showDetailModal = true;
}

protected function processJsonForDisplay($data)
{
    if (empty($data)) {
        return ['_empty' => true, 'message' => 'Tidak ada data'];
    }

    $decoded = is_string($data) ? json_decode($data, true) : $data;
    
    if (!is_array($decoded)) {
        return ['_raw' => true, 'content' => $data];
    }

    $processed = [];
    foreach ($decoded as $key => $value) {
        $processed[$key] = [
            'value' => $value,
            'formatted' => $this->formatFieldValue($value),
            'type' => gettype($value),
        ];
    }
    
    return $processed;
}

protected function formatFieldValue($value)
{
    // \dd($value);
    if (is_null($value)) {
        return '<span class="text-gray-400 italic">NULL</span>';
    }
    
    if (is_bool($value)) {
        $color = $value ? 'text-green-600' : 'text-red-600';
        $icon = $value ? 'fa-check' : 'fa-times';
        $text = $value ? 'Ya' : 'Tidak';
        return "<i class='fas $icon mr-1 $color'></i><span class='$color'>$text</span>";
    }
    
    if (is_array($value)) {
        if (empty($value)) {
            return '<span class="text-gray-400">[]</span>';
        }
        return '<pre class="text-xs">' . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT)) . '</pre>';
    }
    
    if (is_object($value)) {
        return '<pre class="text-xs">' . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT)) . '</pre>';
    }
    
    // Check for email
    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
        return "<a href='mailto:$value' class='text-blue-600 hover:underline'>$value</a>";
    }
    
    // Check for URL
    if (filter_var($value, FILTER_VALIDATE_URL)) {
        $display = parse_url($value, PHP_URL_HOST) ?: $value;
        return "<a href='$value' target='_blank' class='text-blue-600 hover:underline truncate'>$display</a>";
    }
    
    try {
               return "<span class=''>" . $value . "</span>";

    } catch (\Exception $e) {
        // Not a date
    }
    
    return htmlspecialchars($value);
}

protected function formatUserAgent($userAgent)
{
    if (empty($userAgent)) {
        return '-';
    }
    
    // Extract browser and OS info
    $browser = 'Unknown';
    $os = 'Unknown';
    
    // Simple detection (you can expand this)
    if (strpos($userAgent, 'Chrome') !== false) $browser = 'Chrome';
    elseif (strpos($userAgent, 'Firefox') !== false) $browser = 'Firefox';
    elseif (strpos($userAgent, 'Safari') !== false) $browser = 'Safari';
    elseif (strpos($userAgent, 'Edge') !== false) $browser = 'Edge';
    
    if (strpos($userAgent, 'Windows') !== false) $os = 'Windows';
    elseif (strpos($userAgent, 'Mac') !== false) $os = 'macOS';
    elseif (strpos($userAgent, 'Linux') !== false) $os = 'Linux';
    elseif (strpos($userAgent, 'Android') !== false) $os = 'Android';
    elseif (strpos($userAgent, 'iOS') !== false) $os = 'iOS';
    
    return [
        'full' => $userAgent,
        'browser' => $browser,
        'os' => $os,
        'short' => "$browser on $os"
    ];
}

    // METHOD UNTUK TUTUP DETAIL MODAL
    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailData = [];
        $this->detailTitle = '';
    }
    
     
}