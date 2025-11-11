<?php

namespace App\Livewire\Menus;

use App\Models\Menu;
use Livewire\Component;

class Index extends Component
{
    public $menus;
    public $confirmingDeleteId = null;

    protected $listeners = ['menuSaved' => '$refresh'];

    public function mount()
    {
        $this->loadMenus();
    }

    public function loadMenus()
    {
        $this->menus = Menu::with('children')->whereNull('parent_id')->orderBy('order')->get();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
    }

    public function delete($id)
    {
        Menu::find($id)?->delete();
        $this->loadMenus();
        $this->dispatch('alert', type: 'success', message: 'Menu berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.menus.index');
    }
}
