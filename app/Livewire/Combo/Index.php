<?php

namespace App\Livewire\Combo;

use App\Models\Combo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $title = "Combo";
    public $showModal = false;
    public $updateMode = false;
    public $highlightId = null;
    public $search = '';
    public $comboId, $kelompok, $data, $param_int, $param_str, $is_active = true;
    public $currentLocale = 'en';

    public $locale;

    protected $rules = [
        'kelompok' => 'required|string',
        'data' => 'required|string',
        'param_int' => 'nullable|numeric',
        'param_str' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'kelompok.required' => 'Kelompok wajib diisi!',
        'data.required' => 'Data wajib diisi!',
        'param_int.numeric' => 'Param Int harus berupa angka!',
    ];

    public function mount()
    {
        can_any(['combo.view']);
        $this->locale = Session::get('locale', config('app.locale'));
        App::setLocale($this->locale);
        
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
     public function changeLanguage($lang)
    {
        $this->locale = $lang;
        Session::put('locale', $lang);
        App::setLocale($lang);

        $this->dispatch('reload-page');
    }
    

    public function render()
    {
        $query = Combo::query();

        if ($this->search) {
            $query->where('kelompok', 'like', "%{$this->search}%")
                ->orWhere('data', 'like', "%{$this->search}%")
                ->orWhere('param_str', 'like', "%{$this->search}%");
        }

        $dataList = $query->orderBy('id', 'desc')->paginate(10);

        $permissions = module_permissions('combo');

        return view('livewire.combo.index', compact('dataList'))->with([
            'title' => $this->title,
            'currentLocale' => $this->currentLocale,
            'CanCreate' => $permissions['can']['create'],
            'CanView' => $permissions['can']['view'],
            'CanEdit' => $permissions['can']['edit'],
            'CanDelete' => $permissions['can']['delete'],
            'CanManage' => $permissions['can']['manage'],
        ]);
    }

    public function openModal($id = null)
    {
        // SINGLE LINE CHECK!
        $id ? can('combo.edit') : can('combo.create');

        $this->resetInput();

        if ($id) {
            $combo = Combo::findOrFail($id);
            $this->comboId = $combo->id;
            $this->kelompok = $combo->kelompok;
            $this->data = $combo->data;
            $this->param_int = $combo->param_int;
            $this->param_str = $combo->param_str;
            $this->is_active = $combo->is_active;
            $this->updateMode = true;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->resetInput();
        $this->showModal = false;
    }

    private function resetInput()
    {
        $this->comboId = null;
        $this->kelompok = '';
        $this->data = '';
        $this->param_int = '';
        $this->param_str = '';
        $this->is_active = true;
        $this->updateMode = false;
    }

    public function store()
    {
        // SINGLE LINE CHECK!
        can('combo.create');

        $this->validate();

        $combo = Combo::create([
            'kelompok' => $this->kelompok,
            'data' => $this->data,
            'param_int' => $this->param_int ?: null,
            'param_str' => $this->param_str ?: null,
            'is_active' => $this->is_active,
        ]);

        $this->highlightId = $combo->id;
        session()->flash('message', "{$this->title} berhasil dibuat!");
        $this->closeModal();
    }

    public function update()
    {
        // SINGLE LINE CHECK!
        can('combo.edit');

        $this->validate();

        $combo = Combo::findOrFail($this->comboId);
        $combo->update([
            'kelompok' => $this->kelompok,
            'data' => $this->data,
            'param_int' => $this->param_int ?: null,
            'param_str' => $this->param_str ?: null,
            'is_active' => $this->is_active,
        ]);

        $this->highlightId = $combo->id;
        session()->flash('message', "{$this->title} berhasil diperbarui!");
        $this->closeModal();
    }

    public function delete($id)
    {
        // SINGLE LINE CHECK!
        can('combo.delete');

        Combo::findOrFail($id)->delete();
        session()->flash('message', "{$this->title} berhasil dihapus!");
    }

    public function clearSearch()
    {
        $this->search = '';
    }
}
