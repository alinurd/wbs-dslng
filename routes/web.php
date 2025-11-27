<?php

use App\Http\Controllers\LanguageController;
use App\Livewire\Auth\LoginFrom;
use App\Livewire\Auth\RegisterForm; 
use App\Livewire\Combo\IndexManual as IndexManual;
use App\Livewire\Menus\Form as MenuForm;
// use App\Livewire\Combo\Index as ComboIndex;

use App\Livewire\Menus\Index as MenuIndex;
use App\Livewire\Modules\AuditTrail;
use App\Livewire\Modules\Compleien;
use App\Livewire\Modules\DashboardIndex; 
use App\Livewire\Modules\FAQ;
use App\Livewire\Modules\News;
use App\Livewire\Modules\Pengaduan\LogApprovalIndex;
use App\Livewire\Modules\Pengaduan\Report as PengaduanIndex;


use App\Livewire\Modules\Pengaduan\Tracking as TrackingIndex;
use App\Livewire\Modules\Users\PermissionManagement;
use App\Livewire\Modules\Users\UserManagement;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

// Route::get('/tes/register', TestRegister::class)->name('register.ts');
Route::get('/login', LoginFrom::class)->name('login');
Route::get('/register', RegisterForm::class)->name('register');
 Route::get('/', LandingIndex::class)->name('landing.index');
Route::get('/news-landing', NewsIndex::class)->name('new.index');
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
    Route::get('/audit', AuditTrail::class)->name('audit');
    
    
    // Route::get('/blog', Blog::class)->name('blog');
    Route::get('/category', IndexManual::class)->name('category');
    
    
    
    Route::get('/news', News::class)->name('news');
    
    //pengaduan
    Route::prefix('/pengaduan')->group(function () {
               Route::get('/p_report', PengaduanIndex::class)->name('p_report');
               Route::get('/p_tracking', TrackingIndex::class)->name('p_tracking');
    Route::get('/log-complien/{code_pengaduan}', LogApprovalIndex::class)->name('log_detail');
        });
        
               Route::get('/complien', Compleien::class)->name('complien');
Route::get('/faq', FAQ::class)->name('faq');
    
});
 
Route::get('/debug-email', function () {
   try {
        $emailService = new \App\Services\PengaduanEmailService();
        
        $pengaduanData = [
            'code_pengaduan' => 'TEST-001',
            'tanggal_pengaduan' => now()->format('d/m/Y'),
            'perihal' => 'Test Pengaduan',
            'email_pelapor' => 'alidevs1405@gmail.com',
            'telepon_pelapor' => '08123456789',
            'waktu_kejadian' => '2024-01-01 10:00',
            'direktorat' => 'IT',
            'uraian' => 'Ini adalah test pengaduan'
        ];
        
        $result = $emailService->sendNewPengaduanNotifications($pengaduanData, 1);
        
        if ($result) {
            echo "Email berhasil dikirim!";
        } else {
            echo "Email gagal dikirim";
        }
        
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    
    return "<br><br>Debug selesai";
});