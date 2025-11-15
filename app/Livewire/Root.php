<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;

abstract class Root extends Component
{
    use WithPagination;

    // ================= PROPERTIES ==================
    public $title = 'Title';
    public $modul = 'combo';
    public $views = 'index';
    public $model;
    
    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public $showModal = false;
    public $showFilterModal = false;
    public $updateMode = false;
    public $filterMode = false;

    public $form = [];
    public $formDefault = [];
    public $filters = [];
    public $selectedItems = [];
    public $selectAll = false;

    public $rules = []; // child dapat override dengan property
    public $locale;


    // ================== MOUNT =====================
    public function mount()
    {
        // Simpan default form
        $this->formDefault = is_array($this->form) ? $this->form : [];

        // Title otomatis jika tidak didefinisikan
        $this->title = $this->title ?: class_basename($this->model);

        // Hak akses
        can_any([strtolower($this->modul).'.view']);

        // Locale
        $this->locale = Session::get('locale', config('app.locale'));
        App::setLocale($this->locale);
    }


    // ================ QUERY BUILDER =================
    public function query()
    {
        $query = ($this->model)::query();

        // Filter default jika ada
        if (method_exists($this, 'filterDefault')) {
            $filterDefault = $this->filterDefault();
            if (is_array($filterDefault) && count($filterDefault)) {
                $query->where(function ($q) use ($filterDefault) {
                    foreach ($filterDefault as $col) {
                        if (!empty($col['f'])) {
                            $q->Where($col['f'], $col['v']);
                        }
                    }
                });
            }
        }
        
        // Search
        if ($this->search && method_exists($this, 'columns')) {
            
            $columns = $this->columns();
            dd($columns);
            if (is_array($columns) && count($columns)) {
                $query->where(function ($q) use ($columns) {
                    foreach ($columns as $col) {
                        $q->orWhere($col, 'like', "%{$this->search}%");
                    }
                });
            }
        }

        // Additional filters
        if (is_array($this->filters)) {
            // dd($this->filters);
            foreach ($this->filters as $key => $val) {
                if ($val !== '' && $val !== null) {
                    $query->where($key, 'like', "%$val%");
                }
            }
        }

        return $query;
    }


    // ===================== RENDER ======================
    public function render()
    {
        $items = $this->query()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view($this->viewPath(), [
            '_records'    => $items,
            'title'       => $this->title,
            'permissions' => module_permissions(strtolower($this->modul))['can'] ?? []
        ]);
    }


    protected function viewPath()
    {
        return 'livewire.' . strtolower($this->views);
    }


    // ======================= CRUD =======================

    public function create()
    {
        can_any([strtolower($this->modul).'.create']);
        $this->resetForm();
        $this->updateMode = false;
        $this->showModal = true;

        $this->dispatch('modalOpened');
    }


    public function edit($id)
    {
        can_any([strtolower($this->modul).'.edit']);

        $record = ($this->model)::findOrFail($id);

        foreach ($this->formDefault as $key => $default) {
            $this->form[$key] = $record->$key ?? $default;
        }

        $this->form['id'] = $id;

        $this->updateMode = true;
        $this->showModal = true;

        $this->dispatch('modalOpened');
    }


    public function save()
    {
        // Validasi - prioritaskan method rules(), lalu property $rules
        $validationRules = [];
        
        if (method_exists($this, 'rules')) {
            $validationRules = $this->rules();
        } elseif (!empty($this->rules)) {
            $validationRules = $this->rules;
        }

        if (!empty($validationRules)) {
            $this->validate($validationRules);
        }

        $modelClass = $this->model;

        // Payload hanya akan mengambil field yang ada di formDefault
        $payload = collect($this->form)
            ->only(array_keys($this->formDefault))
            ->toArray();

        if ($this->updateMode) {
            can_any([strtolower($this->modul).'.edit']);
            $record = $modelClass::findOrFail($this->form['id']);
            $record->update($payload);
            session()->flash('message', 'Data berhasil diperbarui.');
        } else {
            can_any([strtolower($this->modul).'.create']);
            $modelClass::create($payload);
            session()->flash('message', 'Data berhasil ditambahkan.');
        }

        $this->closeModal();
        $this->resetPage();
        $this->dispatch('dataSaved');
    }


    public function delete($id)
    {
        can_any([strtolower($this->modul).'.delete']);
        ($this->model)::findOrFail($id)->delete();
        session()->flash('message', 'Data berhasil dihapus!');
        $this->resetPage();
        $this->dispatch('dataDeleted');
    }


    // =================== BULK DELETE ===================
    public function deleteBulk()
    {
        can_any([strtolower($this->modul).'.delete']);

        if (count($this->selectedItems)) {
            ($this->model)::whereIn('id', $this->selectedItems)->delete();
        }

        $this->selectedItems = [];
        session()->flash('message', 'Beberapa data berhasil dihapus!');
        $this->dispatch('bulkDeleteCompleted');
    }


    // ====================== FILTER ========================
    public function openFilter()
    {
        $this->showFilterModal = true;
    }


    public function applyFilter()
    {
        $this->filterMode = true;
        $this->showFilterModal = false;
        $this->resetPage();
        session()->flash('message', 'Filter diterapkan.');
    }


    public function resetFilter()
    {
        foreach ($this->filters as $key => $val) {
            $this->filters[$key] = '';
        }

        $this->filterMode = false;
        $this->showFilterModal = false;
        $this->resetPage();
        session()->flash('message', 'Filter direset.');
    }


    // ================= VIEW MODAL ======================
    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        $record = ($this->model)::findOrFail($id);

        $this->dispatch('showDetailModal', [
            'title' => "Detail " . $this->title,
            'data'  => $record->toArray()
        ]);
    }


    // =================== SUPPORT =======================
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('modalClosed');
    }


    protected function resetForm()
    {
        $this->form = $this->formDefault;
        $this->resetErrorBag();
    }


    // =================== SORTING =======================
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->dispatch('tableSorted');
    }


    public function getSortIcon($field)
    {
        if ($this->sortField !== $field) return 'fa-sort';
        return $this->sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
    }


    // =================== SELECT ALL =====================
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = $this->query()->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }


    // =================== PAGINATION =====================
    public function previousPage()
    {
        $this->setPage(max($this->page - 1, 1));
    }


    public function nextPage()
    {
        $this->setPage($this->page + 1);
    }


    // =================== EXPORT =========================
    public function export($type = 'excel')
    {
        can_any([strtolower($this->modul).'.export']);

        $data = $this->query()->get();

        if ($type === 'excel') {
            // Logic export Excel
            session()->flash('message', 'Data berhasil diexport ke Excel.');
        } elseif ($type === 'pdf') {
            // Logic export PDF
            session()->flash('message', 'Data berhasil diexport ke PDF.');
        }

        $this->dispatch('exportCompleted', ['type' => $type]);
    }
}