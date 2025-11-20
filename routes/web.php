<?php

use App\Http\Controllers\LanguageController;
use App\Livewire\Auth\LoginFrom;
use App\Livewire\Auth\RegisterForm;
use App\Livewire\Blog\Blog;
use App\Livewire\Blog\BlogForm;
use App\Livewire\Combo\IndexManual as IndexManual;
use App\Livewire\Menus\Form as MenuForm;
// use App\Livewire\Combo\Index as ComboIndex;

use App\Livewire\Menus\Index as MenuIndex;
use App\Livewire\Modules\Compleien;
use App\Livewire\Modules\DashboardIndex; 
use App\Livewire\Modules\Pengaduan\LogApprovalIndex;
use App\Livewire\Modules\Pengaduan\Report as PengaduanIndex;


use App\Livewire\Modules\Pengaduan\Tracking as TrackingIndex;
use App\Livewire\Modules\users\PermissionManagement;
use App\Livewire\Modules\users\UserManagement;
use App\Livewire\News\Detail as NewsDetail;



use App\Livewire\News\Index as NewsIndex;
use App\Livewire\Param\ParamAduan;
use App\Livewire\Param\ParamDirektorat;
use App\Livewire\Param\ParamEmailNotif;
use App\Livewire\Param\ParamFAQ;
use App\Livewire\Param\ParamForward;
use App\Livewire\Param\ParamJenis;
use App\Livewire\Param\ParamNotif;
use App\Livewire\Param\ParamPertanyaan;









use App\Livewire\Param\ParamStsAduan;
use App\Livewire\Roles\Editor as RoleEditor;


use App\Livewire\Roles\Form as RoleForm;
 use App\Livewire\WbsLanding\Index as LandingIndex;
use App\Livewire\Roles\Index as RoleIndex;
use App\Livewire\TestRegister;

use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/tes/register', TestRegister::class)->name('register.ts');
Route::get('/auth/register', RegisterForm::class)->name('register.form');
Route::get('/auth/login', LoginFrom::class)->name('login.form');
Route::get('/', LandingIndex::class)->name('landing.index');
Route::get('/news', NewsIndex::class)->name('new.index');
Route::get('/news-detail/{slug}', NewsDetail::class)->name('new-detail.index');
Route::post('/change-language', [LanguageController::class, 'change'])->name('language.change');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
        Route::get('/dashboard', DashboardIndex::class)->name('dashboard');
        

});


Route::middleware(['auth'])->group(function (): void {
    // Route::get('/users', UserManagementRoot::class)->name('users');
 
    Route::get('/users', UserManagement::class)->name('users.index');
    Route::get('/users/create', UserManagement::class)->name('users.create');
    Route::get('/users/{id}/edit', UserManagement::class)->name('users.edit');
 
    Route::get('/permissions', PermissionManagement::class)->name('permissions.index');
    Route::get('/permissions/create', PermissionManagement::class)->name('permissions.create');
    Route::get('/permissions/{id}/edit', PermissionManagement::class)->name('permissions.edit');
 
    Route::get('/roles', RoleIndex::class)->name('roles.index');
    Route::get('/roles/create', RoleForm::class)->name('roles.create');
    Route::get('/roles/{id}/edit', RoleForm::class)->name('roles.edit');
    Route::get('/roles/{id}/permissions', RoleEditor::class)->name('roles.permissions');


    Route::get('/menus', MenuIndex::class)->name('menus.index');
    Route::get('/menus/create', MenuForm::class)->name('menus.create');
    Route::get('/menus/{id}/edit', MenuForm::class)->name('menus.edit');

 

    Route::get('/combo', IndexManual::class)->name('combo');
    Route::get('/jenis', ParamJenis::class)->name('jenis');
    // Route::get('/jenis', ParamAduan::class)->name('jenis');
    Route::get('/p_faq', ParamFAQ::class)->name('p_faq');
    Route::get('/aduan', ParamAduan::class)->name('aduan');
    Route::get('/statusaduan', ParamStsAduan::class)->name('statusaduan');
    Route::get('/pertanyaan', ParamPertanyaan::class)->name('pertanyaan');
    Route::get('/forward', ParamForward::class)->name('forward');
    Route::get('/direktorat', ParamDirektorat::class)->name('direktorat');
    Route::get('/emailnotif', ParamEmailNotif::class)->name('emailnotif');
    Route::get('/paramnotif', ParamNotif::class)->name('paramnotif');
    
    
    // Route::get('/blog', Blog::class)->name('blog');
    Route::get('/category', IndexManual::class)->name('category');
    
    
    
    Route::get('/blog', Blog::class)->name('blog.index');
    Route::get('/blog/create', BlogForm::class)->name('blog.create');
    Route::get('/blog/{id}/edit', BlogForm::class)->name('blog.edit');
    
    //pengaduan
    Route::prefix('/pengaduan')->group(function () {
               Route::get('/p_report', PengaduanIndex::class)->name('p_report');
               Route::get('/p_tracking', TrackingIndex::class)->name('p_tracking');
    Route::get('/log-complien/{code_pengaduan}', LogApprovalIndex::class)->name('log_detail');
        });
        
               Route::get('/complien', Compleien::class)->name('complien');
Route::get('/faq', DashboardIndex::class)->name('faq');
    
});


// routes/web.php (sementara)

Route::get('/test-helpers', function() {
    return [
        'can_combo_view' => can('combo.view', false),
        'module_perms' => module_permissions('combo'),
        'crud_access' => can_crud('combo')
    ];
});
