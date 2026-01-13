<?php
namespace App\Services;

use App\Helpers\NotificationHelper;
use App\Models\Pengaduan;
use App\Models\User;
use App\Services\EmailService;
use Spatie\Permission\Models\Role;

class PengaduanEmailService 
{
    private $emailService;

    // Constants untuk role ID
    const ROLE_WBS_EKSTERNAL = 2;
    const ROLE_WBS_INTERNAL = 4;
    const ROLE_WBS_CC = 5;
    const ROLE_WBS_FORWARD = 6;
    const ROLE_WBS_CCO = 7;

    // Status mapping berdasarkan penjelasan Anda
    const STATUS_REJECT_EKS = [10];                    // Reject oleh WBS Eksternal
    const STATUS_APPROVE_EKS = [6];                    // Approve oleh WBS Eksternal → Notif WBS internal dan ke pelapor
    const STATUS_REJECT_INT = [9, 11];                 // Reject oleh WBS Internal → Notif ke pelapor dan WBS EKS
    const STATUS_REJECT_CC = [9];                 // Reject oleh WBS Internal → Notif ke pelapor dan WBS EKS
    const STATUS_APPROVE_INT_CC = [7];                 // APPROVE oleh WBS Internal → Notif ke pelapor dan CC
    const STATUS_APPROVE_INT_FORWARD = [5];            // APPROVE oleh WBS Internal → Notif ke pelapor dan Forward  
    const STATUS_APPROVE_INT_CCO = [1];                // APPROVE oleh WBS Internal → Notif ke pelapor dan CCO
    const STATUS_COMPLETE_FWD = [2];                   // Complete oleh Forward → Notif ke Pelapor dan WBS internal
    const STATUS_APPROVE_CCO = [3];                    // Final approve → Notif ke pelapor
    const STATUS_REJECT_CCO = [8];                     // Final reject → Notif ke pelapor

    public function __construct()
    {
        $this->emailService = new EmailService();
    }

    /**
     * MAIN METHOD: Handle semua email flow berdasarkan status change
     */
    public function handleStatusChange($pengaduan, $statusAction, $roleId, $catatan = '', $forwardDestination = null, $userId=null)
    {
        \Log::info('PengaduanEmailService: Handling status change', [
            'pengaduan_id' => $pengaduan->id,
            'status_action' => $statusAction,
            'role_id' => $roleId,
            'forward_destination' => $forwardDestination
        ]);

        // Format data untuk email
        $pengaduanData = $this->formatPengaduanData($pengaduan, $userId);

        // REJECT BY WBS EKSTERNAL
        if (in_array($statusAction, self::STATUS_REJECT_EKS) && $roleId == self::ROLE_WBS_EKSTERNAL) {
            $this->sendRejectByWbsEks($pengaduanData, $this->getRejectReason($statusAction), $catatan);
        }
        
        // APPROVE BY WBS EKSTERNAL (Submit to Internal)
        elseif (in_array($statusAction, self::STATUS_APPROVE_EKS) && $roleId == self::ROLE_WBS_EKSTERNAL) {
            $this->sendSubmitToWbsInternal($pengaduanData, $catatan ?: 'Mohon ditindaklanjuti');
        }
        
        // REJECT BY WBS INTERNAL  
        elseif (in_array($statusAction, self::STATUS_REJECT_INT) && $roleId == self::ROLE_WBS_INTERNAL) {
            $this->sendRejectByWbsInternal($pengaduanData, $this->getRejectReason($statusAction), $catatan);
        }
        
      
        // APPROVE BY WBS INTERNAL - KE CC
        elseif (in_array($statusAction, self::STATUS_APPROVE_INT_CC) && $roleId == self::ROLE_WBS_INTERNAL) {
            $this->sendSubmitToCc($pengaduanData, $catatan ?: 'Mohon ditindaklanjuti');
        }
        
          // REJECT BY WBS CC  
        elseif (in_array($statusAction, self::STATUS_REJECT_CC) && $roleId == self::ROLE_WBS_CC) {
            $this->sendRejectByWbsCC($pengaduanData, $this->getRejectReason($statusAction), $catatan);
        }

        // APPROVE BY WBS CC - KE FORWARD
        elseif (in_array($statusAction, self::STATUS_APPROVE_INT_FORWARD) && $roleId == self::ROLE_WBS_CC) {
            $this->sendSubmitToForward($pengaduanData, $catatan ?: 'Mohon ditindaklanjuti');
        }
        
        // APPROVE BY WBS INTERNAL - KE CCO
        elseif (in_array($statusAction, self::STATUS_APPROVE_INT_CCO) && $roleId == self::ROLE_WBS_INTERNAL) {
            $this->sendSubmitToCco($pengaduanData, $catatan ?: 'Mohon ditindaklanjuti');
        }

        // APPROVE BY  CC - KE CCO
        elseif (in_array($statusAction, self::STATUS_APPROVE_INT_CCO) && $roleId == self::ROLE_WBS_CC) {
            $this->sendSubmitToCco($pengaduanData, $catatan ?: 'Mohon ditindaklanjuti');
        }
        
        // COMPLETE BY FORWARD
        elseif (in_array($statusAction, self::STATUS_COMPLETE_FWD) && $roleId == self::ROLE_WBS_FORWARD) {
            $this->sendForwardCompleted($pengaduanData, $catatan);
        }
        
        // FINAL APPROVE BY CCO
        elseif (in_array($statusAction, self::STATUS_APPROVE_CCO) && $roleId == self::ROLE_WBS_CCO) {
            $this->sendFinalApproval($pengaduanData, $catatan);
        }
        
        // FINAL REJECT BY CCO
        elseif (in_array($statusAction, self::STATUS_REJECT_CCO) && $roleId == self::ROLE_WBS_CCO) {
            $this->sendRejectByRole($pengaduanData, $roleId, $this->getRejectReason($statusAction), $catatan);
        }

        else {
            \Log::warning('No email flow matched', [
                'status_action' => $statusAction,
                'role_id' => $roleId
            ]);
        }
    }

    /**
     * Get alasan reject berdasarkan status
     */
    private function getRejectReason($statusAction)
    {
        $reasons = [
            10 => 'Data tidak lengkap',
            9 => 'Data tidak cukup',
            11 => 'Data tidak lengkap',
            8 => 'Tidak dapat diproses'
        ];

        return $reasons[$statusAction] ?? 'Tidak dapat diproses';
    }

    /**
     * Format pengaduan data for email
     */
    private function formatPengaduanData($pengaduan, $userId)
    { 
        return [
            'id' => $pengaduan->id,
            'code_pengaduan' => $pengaduan->code_pengaduan,
            'pelapor_id' => $pengaduan->user_id,
            'tanggal_pengaduan' => $pengaduan->tanggal_pengaduan,
            // 'perihal' => $pengaduan->perihal,
            'email_pelapor' => $pengaduan->pelapor->email ?? $pengaduan->email_pelapor,
            'telepon_pelapor' => $pengaduan->telepon_pelapor,
            'waktu_kejadian' => $pengaduan->waktu_kejadian,
            'direktorat' => $pengaduan->direktorat,
            'uraian' => $pengaduan->uraian,
            'catatan' => $pengaduan->catatan,
            'userId' => $userId,
        ];
    }

    /**
     * Get all active user emails by role ID
     */
    private function getEmailsByRoleId($roleId)
    {
        return User::whereHas('roles', function($query) use ($roleId) {
            $query->where('id', $roleId)->where('is_active', 1);
        })->pluck('email')->toArray();
    }

    /**
     * Get all active users by role ID
     */
    private function getUsersByRoleId($roleId)
    {
        return User::whereHas('roles', function($query) use ($roleId) {
            $query->where('id', $roleId)->where('is_active', 1);
        })->select('id', 'email', 'name')->get();
    }

    /**
     * Helper untuk mendapatkan nama role berdasarkan ID - AMBIL DARI DATABASE
     */
    private function getRoleNameById($roleId)
    {
        $role = Role::where('id', $roleId)->where('is_active', 1)->first();
        return $role ? $role->name : 'Unknown Role';
    }

    /**
     * Simplified send notification
     */
    private function sendNotification($to, $title, $content, $type = 'info')
    {
        return $this->emailService->sendNotificationEmail($to, $title, $content, $type);
    }

    /**
     * TAHAP 1: Revisi BARU DIBUAT
     * Email ke: Pelapor + Semua WBS Eksternal
     */
    public function sendRevisiPengaduanNotifications($pengaduanData, $kode='',$tanggal_pengaduan='', $userId = null)
    {
 
        // dd($pengaduanData);
        // Set user ID untuk audit log jika ada
        if ($userId) {
            $this->emailService->setUserId($userId);
        }

        // 1. EMAIL KE PELAPOR
        $contentToPelapor = "
            <h3>📋 Pengaduan berhasil diperbaiki</h3>
            <p>Terima kasih telah menyampaikan pengaduan. Berikut detail pengaduan Anda:</p>
            
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Kode Pengaduan:</strong> {$kode}<br>
                <strong>Tanggal Pengaduan:</strong> {$tanggal_pengaduan}<br>
                 <strong>Status:</strong> Menunggu review WBS Eksternal
            </div>

            <p>Silakan cek secara berkala untuk melihat pembaruan status pengaduan Anda.</p>
        ";

        $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Konfirmasi perbaikan Pengaduan',
            $contentToPelapor, 
            'info'
        );

        // 2. EMAIL KE SEMUA WBS EKSTERNAL
        $wbsEksUsers = $this->getUsersByRoleId(self::ROLE_WBS_EKSTERNAL);
        $contentToWbsEks = "
            <h3>TUGAS BARU - Pengaduan Masuk</h3>
            <p> pengaduan dengan Kode {$kode} telah diperbaiki dan membutuhkan review segera:</p>
            
            <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Kode Pengaduan:</strong> {$kode}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br>
                <strong>Telepon:</strong> {$pengaduanData['telepon_pelapor']}<br> 
                <strong>Waktu Kejadian:</strong> {$pengaduanData['waktu_kejadian']}<br> 
            </div>

            <p><strong>Uraian Pengaduan:</strong><br>{$pengaduanData['uraian']}</p>
            
            <div style='margin-top: 20px; padding: 10px; background: #dc3545; color: white; border-radius: 5px;'>
                <strong>Segera tinjau pengaduan ini!</strong>
            </div>
        ";

                $pelaporUser = User::where('id', $userId)->first();
//                 $pId = Pengaduan::where('code_pengaduan', $kode)->first();

// dd($pId);
        foreach ($wbsEksUsers as $user) {
            // Send email
            $this->sendNotification(
                $user->email,
                "Tugas Baru: Pengaduan {$kode}",
                $contentToWbsEks,
                'urgent'
            );

            // Send push notification
            NotificationHelper::sendToUser(
                $user->id,
                "Tugas Baru ID: {$kode}",
                "{$pelaporUser->name} telah memperbaiki pengaduan yang perlu ditinjau",
                $pelaporUser->id,
                'complien',
                2,
                $kode
            );
        }

        // Send notification to pelapor if they have user account
        // if ($pelaporUser) {
            NotificationHelper::sendToUser(
                $pelaporUser->id,
                "Pengaduan Diterima",
                "Pengaduan {$kode} telah diterima dan sedang ditinjau",
                $pelaporUser->id,
                'complien',
                2,
                $kode            
            );
        // }
    }

    public function sendNewPengaduanNotifications($pengaduanData, $userId = null)
    {
        // dd($pengaduanData);
        // Set user ID untuk audit log jika ada
        if ($userId) {
            $this->emailService->setUserId($userId);
        }

        // 1. EMAIL KE PELAPOR
        $contentToPelapor = "
            <h3>📋 Pengaduan Anda Telah Diterima</h3>
            <p>Terima kasih telah menyampaikan pengaduan. Berikut detail pengaduan Anda:</p>
            
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Kode Pengaduan:</strong> {$pengaduanData['code_pengaduan']}<br>
                <strong>Tanggal Pengaduan:</strong> {$pengaduanData['tanggal_pengaduan']}<br>
                 <strong>Status:</strong> Menunggu review WBS Eksternal
            </div>

            <p>Silakan cek secara berkala untuk melihat pembaruan status pengaduan Anda.</p>
        ";

        $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Konfirmasi Penerimaan Pengaduan',
            $contentToPelapor, 
            'info'
        );

        // 2. EMAIL KE SEMUA WBS EKSTERNAL
        $wbsEksUsers = $this->getUsersByRoleId(self::ROLE_WBS_EKSTERNAL);
        $contentToWbsEks = "
            <h3>TUGAS BARU - Pengaduan Masuk</h3>
            <p>Ada pengaduan baru yang membutuhkan review segera:</p>
            
            <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Kode Pengaduan:</strong> {$pengaduanData['code_pengaduan']}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br>
                <strong>Telepon:</strong> {$pengaduanData['telepon_pelapor']}<br> 
                <strong>Waktu Kejadian:</strong> {$pengaduanData['waktu_kejadian']}<br> 
            </div>

            <p><strong>Uraian Pengaduan:</strong><br>{$pengaduanData['uraian']}</p>
            
            <div style='margin-top: 20px; padding: 10px; background: #dc3545; color: white; border-radius: 5px;'>
                <strong>Segera tinjau pengaduan ini!</strong>
            </div>
        ";

                $pelaporUser = User::where('id', $userId)->first();
//                 $pId = Pengaduan::where('code_pengaduan', $pengaduanData['code_pengaduan'])->first();

// dd($pId);
        foreach ($wbsEksUsers as $user) {
            // Send email
            $this->sendNotification(
                $user->email,
                "Tugas Baru: Pengaduan {$pengaduanData['code_pengaduan']}",
                $contentToWbsEks,
                'urgent'
            );

            // Send push notification
            NotificationHelper::sendToUser(
                $user->id,
                "Tugas Baru ID: {$pengaduanData['code_pengaduan']}",
                "{$pelaporUser->name} telah membuat pengaduan pengaduan baru yang perlu ditinjau",
                $pelaporUser->id,
                'complien',
                2,
                $pengaduanData['code_pengaduan']
            );
        }

        // Send notification to pelapor if they have user account
        // if ($pelaporUser) {
            NotificationHelper::sendToUser(
                $pelaporUser->id,
                "Pengaduan Diterima",
                "Pengaduan {$pengaduanData['code_pengaduan']} telah diterima dan sedang ditinjau",
                $pelaporUser->id,
                'complien',
                2,
                $pengaduanData['code_pengaduan']            
            );
        // }
    }

    /**
     * TAHAP 2: REJECT OLEH WBS EKSTERNAL
     * Email ke: Pelapor + Semua WBS Eksternal (notifikasi tugas selesai)
     */
    public function sendRejectByWbsEks($pengaduanData, $alasanReject, $catatanKhusus = '')
    {
        // 1. EMAIL KE PELAPOR (FINAL - STOP)
        $contentToPelapor = "
            <h3>❌ Status Pengaduan Ditolak</h3>
            <p>Pengaduan Anda dengan kode <strong>{$pengaduanData['code_pengaduan']}</strong> telah ditinjau oleh tim WBS Eksternal.</p>
            
            <div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Status:</strong> <span style='color: #dc3545;'>Tidak Dapat Diproses</span><br>
                <strong>Alasan Penolakan:</strong> {$alasanReject}
            </div>

            " . (!empty($catatanKhusus) ? "
            <div style='background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Catatan Khusus:</strong><br>{$catatanKhusus}
            </div>
            " : "") . "

            <p><em>silahkan lakukan perbaikan dan lakukan submit kembali.</em></p>
        ";

        $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Pengaduan Ditolak - ' . $pengaduanData['code_pengaduan'],
            $contentToPelapor, 
            'reject'
        );

        // 2. EMAIL KE SEMUA WBS EKSTERNAL (Notifikasi tugas selesai)
        $wbsEksUsers = $this->getUsersByRoleId(self::ROLE_WBS_EKSTERNAL);
        $contentToWbsEksTeam = "
            <h3>Tugas Selesai - Pengaduan Ditolak</h3>
            <p>Pengaduan <strong>{$pengaduanData['code_pengaduan']}</strong> telah ditolak oleh kolega Anda.</p>
            
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Alasan Penolakan:</strong> {$alasanReject}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br> 
            </div>

            <p><em>Pengaduan ini telah ditutup.</em></p>
        ";
                        $pelaporUser = User::where('id', $pengaduanData['pelapor_id'])->first();

                        

        foreach ($wbsEksUsers as $user) {
            // Send email
            $this->sendNotification(
                $user->email,
                "Tugas Selesai: Pengaduan {$pengaduanData['code_pengaduan']} Ditolak",
                $contentToWbsEksTeam,
                'info'
            );

            // Send push notification
            NotificationHelper::sendToUser(
                $user->id,
                'Tugas Selesai',
                "Pengaduan {$pengaduanData['code_pengaduan']} telah ditolak oleh WBS Eksternal",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            );
        }

        // Send notification to pelapor if they have user account
  
            NotificationHelper::sendToUser(
                $pengaduanData['pelapor_id'],
                'Pengaduan Ditolak',
                "Pengaduan {$pengaduanData['code_pengaduan']} ditolak oleh WBS Eksternal. Alasan: {$alasanReject}",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            ); 
    }

    /**
     * TAHAP 3: WBS EKSTERNAL SUBMIT KE WBS INTERNAL
     * Email ke: Pelapor + Semua WBS Internal
     */
    public function sendSubmitToWbsInternal($pengaduanData, $instruksiKhusus)
    {
        // 1. EMAIL KE PELAPOR
        $contentToPelapor = "
            <h3>📤 Pengaduan Diproses Lebih Lanjut</h3>
            <p>Pengaduan Anda dengan kode <strong>{$pengaduanData['code_pengaduan']}</strong> telah disetujui oleh WBS Eksternal dan sedang diproses lebih lanjut.</p>
            
            <div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Status Saat Ini:</strong> Dalam penanganan WBS Internal<br>
                <strong>Update Terakhir:</strong> WBS Eksternal telah menyetujui pengaduan Anda
            </div>

            <p>Tim WBS Internal akan meninjau dan menentukan langkah selanjutnya.</p>
        ";

        $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Pengaduan Diproses - ' . $pengaduanData['code_pengaduan'],
            $contentToPelapor, 
            'info'
        );

        // 2. EMAIL KE SEMUA WBS INTERNAL (Tugas Baru)
        $wbsIntUsers = $this->getUsersByRoleId(self::ROLE_WBS_INTERNAL);
        $contentToWbsInt = "
            <h3>🎯 TUGAS BARU - Pengaduan dari WBS Eksternal</h3>
            <p>WBS Eksternal telah mengirimkan pengaduan untuk ditindaklanjuti:</p>
            
            <div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Kode Pengaduan:</strong> {$pengaduanData['code_pengaduan']}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br>
                <strong>Telepon:</strong> {$pengaduanData['telepon_pelapor']}<br>
                 
            </div>

             " . (!empty($instruksiKhusus) ? "
            <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Instruksi Khusus dari WBS Eksternal:</strong><br>{$instruksiKhusus}
            </div>
            " : "") . "

            <p><strong>Uraian Pengaduan:</strong><br>{$pengaduanData['uraian']}</p>
            
        ";

        foreach ($wbsIntUsers as $user) {
            // Send email
            $this->sendNotification(
                $user->email,
                "Tugas Baru: Pengaduan {$pengaduanData['code_pengaduan']}",
                $contentToWbsInt,
                'urgent'
            );

            // Send push notification
            NotificationHelper::sendToUser(
                $user->id,
                "Tugas Baru: Pengaduan {$pengaduanData['code_pengaduan']}",
                "Ada pengaduan baru yang perlu ditindaklanjuti dari WBS Eksternal",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            );
        } 
            NotificationHelper::sendToUser(
                $pengaduanData['pelapor_id'],
                "Pengaduan Diproses",
                "Pengaduan {$pengaduanData['code_pengaduan']} telah disetujui WBS Eksternal dan diproses ke WBS Internal",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            ); 
    }

    /**
     * TAHAP 4: WBS CC REJECT
     * Email ke: Pelapor + Semua WBS CC (notifikasi tugas selesai)
     */
    public function sendRejectByWbsCC($pengaduanData, $alasanReject, $catatanKhusus = '')
    {
        // 1. EMAIL KE PELAPOR (FINAL - STOP)
        $contentToPelapor = "
            <h3>❌ Pengaduan Ditolak oleh WBS CC</h3>
            <p>Pengaduan Anda dengan kode <strong>{$pengaduanData['code_pengaduan']}</strong> telah ditinjau oleh tim WBS CC.</p>
            
            <div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Status:</strong> <span style='color: #dc3545;'>Tidak Dapat Diproses Lebih Lanjut</span><br>
                <strong>Alasan Penolakan:</strong> {$alasanReject}
            </div>

            " . (!empty($catatanKhusus) ? "
            <div style='background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Catatan Khusus:</strong><br>{$catatanKhusus}
            </div>
            " : "") . "

            <p><em>silahkan lakukan perbaikan dan lakukan submit kembali.</em></p>
        ";

        $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Pengaduan Ditolak - ' . $pengaduanData['code_pengaduan'],
            $contentToPelapor, 
            'reject'
        );

        // 2. EMAIL KE SEMUA WBS INTERNAL (Notifikasi tugas selesai)
        $wbsIntUsers = $this->getUsersByRoleId(self::ROLE_WBS_INTERNAL);
        $contentToWbsIntTeam = "
            <h3>Tugas Selesai - Pengaduan Ditolak</h3>
            <p>Pengaduan <strong>{$pengaduanData['code_pengaduan']}</strong> telah ditolak oleh kolega Anda.</p>
            
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Alasan Penolakan:</strong> {$alasanReject}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br> 
            </div>

            <p><em>Pengaduan ini telah ditutup.</em></p>
        ";

        foreach ($wbsIntUsers as $user) {
            // Send email
            $this->sendNotification(
                $user->email,
                "Tugas Selesai: Pengaduan {$pengaduanData['code_pengaduan']} Ditolak",
                $contentToWbsIntTeam,
                'info'
            );

            // Send push notification
            NotificationHelper::sendToUser(
                $user->id,
                "Tugas Selesai: Pengaduan Ditolak",
                "Pengaduan {$pengaduanData['code_pengaduan']} telah ditolak oleh WBS CC",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            );
        }

        // Send notification to pelapor if they have user account
       
            NotificationHelper::sendToUser(
                $pengaduanData['pelapor_id'],
                "Pengaduan Ditolak",
                "Pengaduan {$pengaduanData['code_pengaduan']} ditolak oleh WBS CC. Alasan: {$alasanReject}",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            ); 
    }

    /**
     * TAHAP 4: WBS INTERNAL REJECT
     * Email ke: Pelapor + Semua WBS Internal (notifikasi tugas selesai)
     */
    public function sendRejectByWbsInternal($pengaduanData, $alasanReject, $catatanKhusus = '')
    {
        // 1. EMAIL KE PELAPOR (FINAL - STOP)
        $contentToPelapor = "
            <h3>❌ Pengaduan Ditolak oleh WBS Internal</h3>
            <p>Pengaduan Anda dengan kode <strong>{$pengaduanData['code_pengaduan']}</strong> telah ditinjau oleh tim WBS Internal.</p>
            
            <div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Status:</strong> <span style='color: #dc3545;'>Tidak Dapat Diproses Lebih Lanjut</span><br>
                <strong>Alasan Penolakan:</strong> {$alasanReject}
            </div>

            " . (!empty($catatanKhusus) ? "
            <div style='background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Catatan Khusus:</strong><br>{$catatanKhusus}
            </div>
            " : "") . "

            <p><em>silahkan lakukan perbaikan dan lakukan submit kembali.</em></p>
        ";

        $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Pengaduan Ditolak - ' . $pengaduanData['code_pengaduan'],
            $contentToPelapor, 
            'reject'
        );

        // 2. EMAIL KE SEMUA WBS INTERNAL (Notifikasi tugas selesai)
        $wbsIntUsers = $this->getUsersByRoleId(self::ROLE_WBS_INTERNAL);
        $contentToWbsIntTeam = "
            <h3>Tugas Selesai - Pengaduan Ditolak</h3>
            <p>Pengaduan <strong>{$pengaduanData['code_pengaduan']}</strong> telah ditolak oleh kolega Anda.</p>
            
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Alasan Penolakan:</strong> {$alasanReject}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br> 
            </div>

            <p><em>Pengaduan ini telah ditutup.</em></p>
        ";

        foreach ($wbsIntUsers as $user) {
            // Send email
            $this->sendNotification(
                $user->email,
                "Tugas Selesai: Pengaduan {$pengaduanData['code_pengaduan']} Ditolak",
                $contentToWbsIntTeam,
                'info'
            );

            // Send push notification
            NotificationHelper::sendToUser(
                $user->id,
                "Tugas Selesai: Pengaduan Ditolak",
                "Pengaduan {$pengaduanData['code_pengaduan']} telah ditolak oleh WBS Internal",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            );
        }

        // Send notification to pelapor if they have user account
       
            NotificationHelper::sendToUser(
                $pengaduanData['pelapor_id'],
                "Pengaduan Ditolak",
                "Pengaduan {$pengaduanData['code_pengaduan']} ditolak oleh WBS Internal. Alasan: {$alasanReject}",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            ); 
    }

    /**
     * TAHAP 5: WBS INTERNAL SUBMIT KE CC
     * Email ke: Pelapor + Semua WBS CC
     */
    public function sendSubmitToCc($pengaduanData, $instruksiKhusus = '')
    {
        // 1. EMAIL KE PELAPOR
        $contentToPelapor = "
            <h3>👥 Pengaduan Ditugaskan ke Tim CC</h3>
            <p>Pengaduan Anda dengan kode <strong>{$pengaduanData['code_pengaduan']}</strong> sedang ditangani oleh tim CC.</p>
            
            <div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Status Saat Ini:</strong> Dalam penanganan WBS CC<br>
                <strong>Penanggung Jawab:</strong> Tim Compliance Committee (CC)
            </div>

            <p>Tim CC akan menindaklanjuti pengaduan Anda sesuai dengan instruksi dari WBS Internal.</p>
        ";

        $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Pengaduan Ditugaskan ke Tim CC - ' . $pengaduanData['code_pengaduan'],
            $contentToPelapor, 
            'info'
        );

        // 2. EMAIL KE SEMUA WBS CC (Tugas Baru)
        $wbsCcUsers = $this->getUsersByRoleId(self::ROLE_WBS_CC);
        $contentToWbsCc = "
            <h3>📎 TUGAS BARU - Pengaduan dari WBS Internal</h3>
            <p>Anda ditugaskan sebagai CC untuk menangani pengaduan berikut:</p>
            
            <div style='background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Kode Pengaduan:</strong> {$pengaduanData['code_pengaduan']}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br>
                <strong>Telepon:</strong> {$pengaduanData['telepon_pelapor']}<br>
                 
            </div>

            " . (!empty($instruksiKhusus) ? "
            <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Instruksi Khusus dari WBS Internal:</strong><br>{$instruksiKhusus}
            </div>
            " : "") . "

            <p><strong>Uraian Pengaduan:</strong><br>{$pengaduanData['uraian']}</p>
        ";

        foreach ($wbsCcUsers as $user) {
            // Send email
            $this->sendNotification(
                $user->email,
                "Tugas CC: Pengaduan {$pengaduanData['code_pengaduan']}",
                $contentToWbsCc,
                'info'
            );

            // Send push notification
            NotificationHelper::sendToUser(
                $user->id,
                "Tugas Baru: {$pengaduanData['code_pengaduan']}",
                "Anda ditugaskan sebagai CC untuk pengaduan {$pengaduanData['code_pengaduan']}",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            );
        }

        // Send notification to pelapor if they have user account
      
            NotificationHelper::sendToUser(
                $pengaduanData['pelapor_id'],
                "Pengaduan Ditugaskan ke CC",
                "Pengaduan {$pengaduanData['code_pengaduan']} telah ditugaskan ke tim CC",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            ); 
    }

    /**
     * TAHAP 6: WBS INTERNAL SUBMIT KE CCO
     * Email ke: Pelapor + Semua WBS CCO
     */
    public function sendSubmitToCco($pengaduanData, $instruksiKhusus = '')
    {
        // 1. EMAIL KE PELAPOR
        $contentToPelapor = "
            <h3>🔍 Pengaduan Ditugaskan ke Tim CCO</h3>
            <p>Pengaduan Anda dengan kode <strong>{$pengaduanData['code_pengaduan']}</strong> sedang ditangani oleh tim CCO.</p>
            
            <div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Status Saat Ini:</strong> Dalam penanganan WBS CCO<br>
                <strong>Penanggung Jawab:</strong> Tim CCO
            </div>

            <p>Tim CCO akan menindaklanjuti pengaduan Anda sesuai dengan instruksi dari WBS Internal.</p>
        ";

        $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Pengaduan Ditugaskan ke Tim CCO - ' . $pengaduanData['code_pengaduan'],
            $contentToPelapor, 
            'info'
        );

        // 2. EMAIL KE SEMUA WBS CCO (Tugas Baru)
        $wbsCcoUsers = $this->getUsersByRoleId(self::ROLE_WBS_CCO);
        $contentToWbsCco = "
            <h3>🔍 TUGAS BARU - Pengaduan dari WBS Internal</h3>
            <p>Anda ditugaskan sebagai CCO untuk menangani pengaduan berikut:</p>
            
            <div style='background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Kode Pengaduan:</strong> {$pengaduanData['code_pengaduan']}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br>
                <strong>Telepon:</strong> {$pengaduanData['telepon_pelapor']}<br>  
            </div>

            " . (!empty($instruksiKhusus) ? "
            <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Instruksi Khusus dari WBS Internal:</strong><br>{$instruksiKhusus}
            </div>
            " : "") . "

            <p><strong>Uraian Pengaduan:</strong><br>{$pengaduanData['uraian']}</p>
        ";

        foreach ($wbsCcoUsers as $user) {
            // Send email
            $this->sendNotification(
                $user->email,
                "Tugas CCO: Pengaduan {$pengaduanData['code_pengaduan']}",
                $contentToWbsCco,
                'info'
            );

            // Send push notification
            NotificationHelper::sendToUser(
                $user->id,
                "Tugas Baru: {$pengaduanData['code_pengaduan']}",
                "Anda ditugaskan sebagai CCO untuk pengaduan {$pengaduanData['code_pengaduan']}",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            );
        }

        
            NotificationHelper::sendToUser(
                $pengaduanData['pelapor_id'],
                "Pengaduan Ditugaskan ke CCO",
                "Pengaduan {$pengaduanData['code_pengaduan']} telah ditugaskan ke tim CCO",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            ); 
    }

    /**
     * TAHAP 7: WBS INTERNAL SUBMIT KE FORWARD
     * Email ke: Pelapor + Semua WBS Forward
     */
    public function sendSubmitToForward($pengaduanData, $instruksiKhusus = '')
    {
        // 1. EMAIL KE PELAPOR
        $contentToPelapor = "
            <h3>↪️ Pengaduan Diteruskan ke Tim Forward</h3>
            <p>Pengaduan Anda dengan kode <strong>{$pengaduanData['code_pengaduan']}</strong> telah diteruskan ke tim Forward untuk tindakan lebih lanjut.</p>
            
            <div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Status Saat Ini:</strong> Dalam penanganan WBS Forward<br>
                <strong>Penanggung Jawab:</strong> Tim Forward
            </div>

            <p>Tim Forward akan menindaklanjuti pengaduan Anda dan memberikan update sesuai kebutuhan.</p>
        ";

        $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Pengaduan Diteruskan ke Tim Forward - ' . $pengaduanData['code_pengaduan'],
            $contentToPelapor, 
            'info'
        );

        // 2. EMAIL KE SEMUA WBS FORWARD (Tugas Baru)
        $wbsFwdUsers = $this->getUsersByRoleId(self::ROLE_WBS_FORWARD);
        $contentToWbsFwd = "
            <h3>↪️ TUGAS BARU - Pengaduan Diteruskan dari WBS CC</h3>
            <p>Anda ditugaskan untuk menangani pengaduan yang diteruskan berikut:</p>
            
            <div style='background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Kode Pengaduan:</strong> {$pengaduanData['code_pengaduan']}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br>
                <strong>Telepon:</strong> {$pengaduanData['telepon_pelapor']}<br>  
            </div>

            " . (!empty($instruksiKhusus) ? "
            <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Instruksi Khusus dari WBS CC:</strong><br>{$instruksiKhusus}
            </div>
            " : "") . "

            <p><strong>Uraian Pengaduan:</strong><br>{$pengaduanData['uraian']}</p>
            
            <div style='margin-top: 20px; padding: 10px; background: #6c757d; color: white; border-radius: 5px;'>
                ℹ️ <strong>Anda dapat menyelesaikan pengaduan ini dengan mengupdate status menjadi READ,</strong>
            </div>
        ";

        foreach ($wbsFwdUsers as $user) {
            // Send email
            $this->sendNotification(
                $user->email,
                "Tugas Forward: Pengaduan {$pengaduanData['code_pengaduan']}",
                $contentToWbsFwd,
                'info'
            );

            // Send push notification
            NotificationHelper::sendToUser(
                $user->id,
                "Tugas Baru: {$pengaduanData['code_pengaduan']}",
                "Anda ditugaskan untuk menangani pengaduan {$pengaduanData['code_pengaduan']}",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            );
        }

        // Send notification to pelapor if they have user account
        $pelaporUser = User::where('email', $pengaduanData['email_pelapor'])->first();
        if ($pelaporUser) {
            NotificationHelper::sendToUser(
                $pelaporUser->id,
                "Pengaduan Diteruskan ke Forward",
                "Pengaduan {$pengaduanData['code_pengaduan']} telah diteruskan ke tim Forward",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            );
        }
    }

    /**
     * TAHAP 8: FORWARD UPDATE STATUS KE READ
     * Email ke: Semua WBS Internal (notifikasi tugas selesai)
     */
    public function sendForwardCompleted($pengaduanData, $catatanDariForward = '')
    {
        $wbsIntUsers = $this->getUsersByRoleId(self::ROLE_WBS_CC);
        $contentToWbsInt = "
            <h3>Tugas Forward Selesai</h3>
            <p>Tim Forward telah menyelesaikan tugas untuk pengaduan <strong>{$pengaduanData['code_pengaduan']}</strong>.</p>
            
            <div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Kode Pengaduan:</strong> {$pengaduanData['code_pengaduan']}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br> 
                <strong>Status:</strong> READ oleh Forward
            </div>

            " . (!empty($catatanDariForward) ? "
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Catatan dari Forward:</strong><br>{$catatanDariForward}
            </div>
            " : "") . "

            <p><strong>pengaduan telah diselesaikan</strong></p>
        ";

        foreach ($wbsIntUsers as $user) {
            // Send email
            $this->sendNotification(
                $user->email,
                "Forward Selesai: Pengaduan {$pengaduanData['code_pengaduan']}",
                $contentToWbsInt,
                'info'
            );

            // Send push notification
            NotificationHelper::sendToUser(
                $user->id,
                "Forward Selesai",
                "Pengaduan {$pengaduanData['code_pengaduan']} telah selesai diproses oleh Forward",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            );
        }

        
         $contentToPelapor = "
            <h3>Pengaduan Telah Selesai</h3>
            <p>Pengaduan Anda dengan kode <strong>{$pengaduanData['code_pengaduan']}</strong> telah diselesaikan Oleh WBS Forward.</p>
            
            <div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Status:</strong> <span style='color: #28a745;'>Selesai dan Disetujui Oleh WBS Forward</span><br> 
            </div>

            " . (!empty($catatan) ? "
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Catatan Penutupan:</strong><br>{$catatan}
            </div>
            " : "") . "

            <p><em>Terima kasih telah menggunakan layanan pengaduan kami.</em></p>
        ";

        // Send email
        $emailResult = $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Pengaduan Selesai - ' . $pengaduanData['code_pengaduan'],
            $contentToPelapor, 
            'success'
        );

        // Send push notification to pelapor if they have user account
      
            NotificationHelper::sendToUser(
                $pengaduanData['pelapor_id'],
                "Pengaduan Selesai",
                "Pengaduan {$pengaduanData['code_pengaduan']} telah diselesaikan oleh wbs forward",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            ); 

    }

    /**
     * REJECT DARI CC/CCO/FORWARD
     * Email ke: Pelapor + Semua user role tersebut (notifikasi tugas selesai)
     */
    public function sendRejectByRole($pengaduanData, $roleId, $alasanReject, $catatanKhusus = '')
    {
        $roleName = $this->getRoleNameById($roleId);

        // 1. EMAIL KE PELAPOR (FINAL - STOP)
        $contentToPelapor = "
            <h3>❌ Pengaduan Ditolak oleh Tim {$roleName}</h3>
            <p>Pengaduan Anda dengan kode <strong>{$pengaduanData['code_pengaduan']}</strong> telah ditinjau oleh tim {$roleName}.</p>
            
            <div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Status:</strong> <span style='color: #dc3545;'>Tidak Dapat Diproses</span><br>
                <strong>Alasan Penolakan:</strong> {$alasanReject}
            </div>

            " . (!empty($catatanKhusus) ? "
            <div style='background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Catatan Khusus:</strong><br>{$catatanKhusus}
            </div>
            " : "") . "

            <p><em>silahkan lakukan perbaikan dan lakukan submit kembali.</em></p>
        ";

        $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Pengaduan Ditolak - ' . $pengaduanData['code_pengaduan'],
            $contentToPelapor, 
            'reject'
        );

        // 2. EMAIL KE SEMUA USER ROLE TERSEBUT (Notifikasi tugas selesai)
        $roleUsers = $this->getUsersByRoleId($roleId);
        $contentToRoleTeam = "
            <h3>Tugas Selesai - Pengaduan Ditolak</h3>
            <p>Pengaduan <strong>{$pengaduanData['code_pengaduan']}</strong> telah ditolak oleh kolega Anda di tim {$roleName}.</p>
            
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Alasan Penolakan:</strong> {$alasanReject}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br> 
            </div>

            <p><em>Pengaduan ini telah ditutup.</em></p>
        ";

        foreach ($roleUsers as $user) {
            // Send email
            $this->sendNotification(
                $user->email,
                "Tugas Selesai: Pengaduan {$pengaduanData['code_pengaduan']} Ditolak",
                $contentToRoleTeam,
                'info'
            );

            // Send push notification
            NotificationHelper::sendToUser(
                $user->id,
                "Tugas Selesai: Pengaduan Ditolak",
                "Pengaduan {$pengaduanData['code_pengaduan']} telah ditolak oleh tim {$roleName}",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            );
        }

        // Send notification to pelapor if they have user account
     
            NotificationHelper::sendToUser(
                $pengaduanData['pelapor_id'],
                "Pengaduan Ditolak",
                "Pengaduan {$pengaduanData['code_pengaduan']} ditolak oleh tim {$roleName}. Alasan: {$alasanReject}",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            ); 
    }

    /**
     * TAHAP 9: FINAL APPROVAL - Pengaduan Selesai
     * Email ke: Pelapor
     */
    public function sendFinalApproval($pengaduanData, $catatan = '')
    {
        $contentToPelapor = "
            <h3>Pengaduan Telah Selesai</h3>
            <p>Pengaduan Anda dengan kode <strong>{$pengaduanData['code_pengaduan']}</strong> telah diselesaikan dan disetujui.</p>
            
            <div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Status:</strong> <span style='color: #28a745;'>Selesai dan Disetujui</span><br> 
            </div>

            " . (!empty($catatan) ? "
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Catatan Penutupan:</strong><br>{$catatan}
            </div>
            " : "") . "

            <p><em>Terima kasih telah menggunakan layanan pengaduan kami.</em></p>
        ";

        // Send email
        $emailResult = $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Pengaduan Selesai - ' . $pengaduanData['code_pengaduan'],
            $contentToPelapor, 
            'success'
        );

        // Send push notification to pelapor if they have user account
      
            NotificationHelper::sendToUser(
                $pengaduanData['pelapor_id'],
                "Pengaduan Selesai",
                "Pengaduan {$pengaduanData['code_pengaduan']} telah diselesaikan dan disetujui",
                $pengaduanData['userId'],
                'complien',
                2,
                $pengaduanData['id']
            ); 

        return $emailResult;
    }
}