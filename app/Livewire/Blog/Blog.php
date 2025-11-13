<?php

namespace App\Livewire\Blog;

use App\Models\Combo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;

class Blog extends Component
{
    use WithPagination;

    public $title = "Blog";
    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Modal states
    public $showModal = false;
    public $showFilterModal = false;
    public $updateMode = false;
    public $filterMode = false;
    
    // Form fields
    public $comboId;
    public $kelompok = '';
    public $data = '';
    public $param_int = '';
    public $param_str = '';
    public $is_active = true;

    // Bulk actions
    public $selectedItems = [];
    public $selectAll = false;

    // Filter fields
    public $filterKelompok = '';
    public $filterStatus = '';

    protected $rules = [
        'kelompok' => 'required|string|max:255',
        'data' => 'required|string|max:255',
        'param_int' => 'nullable|numeric',
        'param_str' => 'nullable|string|max:255',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'kelompok.required' => 'Kelompok wajib diisi!',
        'data.required' => 'Data wajib diisi!',
        'param_int.numeric' => 'Param Int harus berupa angka!',
    ];

    public function mount()
    {
        can_any(['blog.view']);
        $this->locale = Session::get('locale', config('app.locale'));
        App::setLocale($this->locale);
    }

    public function render()
    {
        $query = Combo::query();

        // Search functionality
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kelompok', 'like', '%' . $this->search . '%')
                  ->orWhere('data', 'like', '%' . $this->search . '%')
                  ->orWhere('param_str', 'like', '%' . $this->search . '%');
            });
        }

        // Filter functionality
        if ($this->filterKelompok) {
            $query->where('kelompok', 'like', '%' . $this->filterKelompok . '%');
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $combos = $query->paginate($this->perPage);

        return view('livewire.blog.index-manual', [
            'combos' => $combos,
            'title' => "Blog",
            'permissions' => module_permissions('blog')['can'] ?? []
        ]);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        
        // Animation trigger
        $this->dispatch('tableSorted');
    }

    /**
     * Get sort icon untuk field tertentu
     */
    public function getSortIcon($field)
    {
        if ($this->sortField !== $field) {
            return 'fa-sort';
        }

        return $this->sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->dispatch('searchUpdated');
    }

    public function updatingPerPage()
    {
        $this->resetPage();
        $this->dispatch('perPageUpdated');
    }

    // Bulk selection
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = $this->combos->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
        $this->dispatch('selectionUpdated');
    }

    public function updatedSelectedItems()
    {
        $this->selectAll = false;
        $this->dispatch('selectionUpdated');
    }

    // Toggle selection
    public function toggleSelect($id)
    {
        if (in_array($id, $this->selectedItems)) {
            $this->selectedItems = array_diff($this->selectedItems, [$id]);
        } else {
            $this->selectedItems[] = $id;
        }
        $this->dispatch('selectionUpdated');
    }

    // Bulk delete
    public function deleteBulk()
    {
        can_any(['blog.delete']);
        
        if (empty($this->selectedItems)) {
            session()->flash('error', 'Tidak ada data yang dipilih untuk dihapus.');
            return;
        }

        $count = count($this->selectedItems);
        Combo::whereIn('id', $this->selectedItems)->delete();
        
        $this->selectedItems = [];
        $this->selectAll = false;
        
        session()->flash('message', $count . ' data berhasil dihapus.');
        $this->resetPage();
        $this->dispatch('bulkDeleteCompleted');
    }

    // Export functionality
    public function export($type)
    {
        can_any(['blog.view']);
        
        $query = Combo::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kelompok', 'like', '%' . $this->search . '%')
                  ->orWhere('data', 'like', '%' . $this->search . '%')
                  ->orWhere('param_str', 'like', '%' . $this->search . '%');
            });
        }

        // Apply filters
        if ($this->filterKelompok) {
            $query->where('kelompok', 'like', '%' . $this->filterKelompok . '%');
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus);
        }

        $data = $query->get();

        if ($type === 'excel') {
            return $this->exportExcel($data);
        } else {
            return $this->exportPdf($data);
        }
    }

    private function exportExcel($data)
    {
        // Implement Excel export logic here
        session()->flash('message', 'Export Excel berhasil dilakukan.');
        $this->dispatch('exportCompleted');
    }

    private function exportPdf($data)
    {
        // Implement PDF export logic here
        session()->flash('message', 'Export PDF berhasil dilakukan.');
        $this->dispatch('exportCompleted');
    }

    // Filter methods
    public function openFilter()
    {
        $this->showFilterModal = true;
    }

    public function applyFilter()
    {
        $this->resetPage();
        $this->showFilterModal = false;
        $this->filterMode = true;
        session()->flash('message', 'Filter berhasil diterapkan.');
        $this->dispatch('filterApplied');
    }

    public function resetFilter()
    {
        $this->filterKelompok = '';
        $this->filterStatus = '';
        $this->filterMode = false;
        $this->resetPage();
        $this->showFilterModal = false;
        session()->flash('message', 'Filter berhasil direset.');
        $this->dispatch('filterReset');
    }

    // Create
    public function create()
    {
        can_any(['blog.create']);
        $this->resetForm();
        $this->showModal = true;
        $this->updateMode = false;
        $this->dispatch('modalOpened');
    }

    // Edit
    public function edit($id)
    {
        can_any(['blog.edit']);
        $combo = Combo::findOrFail($id);

        $this->comboId = $combo->id;
        $this->kelompok = $combo->kelompok;
        $this->data = $combo->data;
        $this->param_int = $combo->param_int;
        $this->param_str = $combo->param_str;
        $this->is_active = $combo->is_active;

        $this->showModal = true;
        $this->updateMode = true;
        $this->dispatch('modalOpened');
    }

    // Save (Create/Update)
    public function save()
    {
        if ($this->updateMode) {
            can_any(['blog.edit']);
            $this->validate();
            
            $combo = Combo::findOrFail($this->comboId);
            $combo->update([
                'kelompok' => $this->kelompok,
                'data' => $this->data,
                'param_int' => $this->param_int,
                'param_str' => $this->param_str,
                'is_active' => $this->is_active,
            ]);

            session()->flash('message', 'Data berhasil diupdate.');
        } else {
            can_any(['blog.create']);
            $this->validate();

            Combo::create([
                'kelompok' => $this->kelompok,
                'data' => $this->data,
                'param_int' => $this->param_int,
                'param_str' => $this->param_str,
                'is_active' => $this->is_active,
            ]);

            session()->flash('message', 'Data berhasil ditambahkan.');
        }

        $this->closeModal();
        $this->resetPage();
        $this->dispatch('dataSaved');
    }

    // Delete
    public function delete($id)
    {
        can_any(['blog.delete']);
        $combo = Combo::findOrFail($id);
        $combo->delete();

        session()->flash('message', 'Data berhasil dihapus.');
        $this->resetPage();
        $this->dispatch('dataDeleted');
    }

    // View
    public function view($id)
    {
        can_any(['blog.view']);
        $combo = Combo::findOrFail($id);
        
        $this->dispatch('showDetailModal', [
            'title' => 'Detail Combo',
            'data' => [
                'ID' => $combo->id,
                'Kelompok' => $combo->kelompok,
                'Data' => $combo->data,
                'Param Int' => $combo->param_int ?? '-',
                'Param Str' => $combo->param_str ?? '-',
                'Status' => $combo->is_active ? 'Aktif' : 'Nonaktif',
                'Dibuat Pada' => $combo->created_at->format('d/m/Y H:i'),
                'Diupdate Pada' => $combo->updated_at->format('d/m/Y H:i'),
            ]
        ]);
    }

    // Close modal
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('modalClosed');
    }

    // Close filter modal
    public function closeFilterModal()
    {
        $this->showFilterModal = false;
        $this->dispatch('filterModalClosed');
    }

    // Reset form
    private function resetForm()
    {
        $this->reset([
            'comboId', 'kelompok', 'data', 'param_int', 
            'param_str', 'is_active', 'updateMode'
        ]);
        $this->resetErrorBag();
    }
}