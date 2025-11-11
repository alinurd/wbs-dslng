<?php 
namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    public $roles;

    public function mount()
    {
        $this->roles = Role::all();
    }

    public function delete($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        session()->flash('message', 'Role berhasil dihapus!');
        $this->roles = Role::all();
    }

    public function render()
    {
        return view('livewire.roles.index');
    }
}
