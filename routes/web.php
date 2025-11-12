<?php

use App\Http\Controllers\LanguageController;
use App\Livewire\Combo\IndexManual as IndexManual;
// use App\Livewire\Combo\Index as ComboIndex;

use App\Livewire\Menus\Form as MenuForm;

use App\Livewire\Menus\Index as MenuIndex;
use App\Livewire\News\Detail as NewsDetail;
use App\Livewire\News\Index as NewsIndex;
use App\Livewire\PermissionManagement;
use App\Livewire\Roles\Editor as RoleEditor;


use App\Livewire\Roles\Form as RoleForm;
use App\Livewire\Roles\Index as RoleIndex;
use App\Livewire\UserManagement;
use App\Livewire\WbsLanding\Index as LandingIndex;

use Illuminate\Support\Facades\Route;

Route::get('/', LandingIndex::class)->name('landing.index');
Route::get('/news', NewsIndex::class)->name('new.index');
Route::get('/news-detail/{slug}', NewsDetail::class)->name('new-detail.index');
Route::post('/change-language', [LanguageController::class, 'change'])->name('language.change');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/users', UserManagement::class)->name('users');
     Route::get('/permissions', PermissionManagement::class)->name('permissions');

    Route::get('/roles', RoleIndex::class)->name('roles.index');
    Route::get('/roles/create', RoleForm::class)->name('roles.create');
    Route::get('/roles/{id}/edit', RoleForm::class)->name('roles.edit');
    Route::get('/roles/{id}/permissions', RoleEditor::class)->name('roles.permissions');


    Route::get('/menus', MenuIndex::class)->name('menus.index');
    Route::get('/menus/create', MenuForm::class)->name('menus.create');
    Route::get('/menus/{id}/edit', MenuForm::class)->name('menus.edit');

 

    Route::get('/combo', IndexManual::class)->name('combo');
    // Route::get('/combo', ComboIndex::class)->name('combo');
});


// routes/web.php (sementara)

Route::get('/test-helpers', function() {
    return [
        'can_combo_view' => can('combo.view', false),
        'module_perms' => module_permissions('combo'),
        'crud_access' => can_crud('combo')
    ];
});
