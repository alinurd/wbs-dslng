<?php
namespace App\Services;

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
    public function handleStatusChange($pengaduan, $statusAction, $roleId, $catatan = '', $forwardDestination = null)
    {
        \Log::info('PengaduanEmailService: Handling status change', [
            'pengaduan_id' => $pengaduan->id,
            'status_action' => $statusAction,
            'role_id' => $roleId,
            'forward_destination' => $forwardDestination
        ]);

        // Format data untuk email
        $pengaduanData = $this->formatPengaduanData($pengaduan);

        // REJECT BY WBS EKSTERNAL
        if (in_array($statusAction, self::STATUS_REJECT_EKS) && $roleId == self::ROLE_WBS_EKSTERNAL) {
            $this->sendRejectByWbsEks($pengaduanData, $this->getRejectReason($statusAction), $catatan);
        }
        
        // APPROVE BY WBS EKSTERNAL (Submit to Internal)
        elseif (in_array($statusAction, self::STATUS_APPROVE_EKS) && $roleId == self::ROLE_WBS_EKSTERNAL) {
            $this->sendSubmitToWbsInternal($pengaduanData);
        }
        
        // REJECT BY WBS INTERNAL  
        elseif (in_array($statusAction, self::STATUS_REJECT_INT) && $roleId == self::ROLE_WBS_INTERNAL) {
            $this->sendRejectByWbsInternal($pengaduanData, $this->getRejectReason($statusAction), $catatan);
        }
        
        // APPROVE BY WBS INTERNAL - KE CC
        elseif (in_array($statusAction, self::STATUS_APPROVE_INT_CC) && $roleId == self::ROLE_WBS_INTERNAL) {
            $this->sendSubmitToCc($pengaduanData, $catatan ?: 'Mohon ditindaklanjuti');
        }
        
        // APPROVE BY WBS INTERNAL - KE FORWARD
        elseif (in_array($statusAction, self::STATUS_APPROVE_INT_FORWARD) && $roleId == self::ROLE_WBS_INTERNAL) {
            $this->sendSubmitToForward($pengaduanData, $catatan ?: 'Mohon ditindaklanjuti');
        }
        
        // APPROVE BY WBS INTERNAL - KE CCO
        elseif (in_array($statusAction, self::STATUS_APPROVE_INT_CCO) && $roleId == self::ROLE_WBS_INTERNAL) {
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
    private function formatPengaduanData($pengaduan)
    {
        return [
            'code_pengaduan' => $pengaduan->code_pengaduan,
            'tanggal_pengaduan' => $pengaduan->tanggal_pengaduan,
            // 'perihal' => $pengaduan->perihal,
            'email_pelapor' => $pengaduan->pelapor->email ?? $pengaduan->email_pelapor,
            'telepon_pelapor' => $pengaduan->telepon_pelapor,
            'waktu_kejadian' => $pengaduan->waktu_kejadian,
            'direktorat' => $pengaduan->direktorat,
            'uraian' => $pengaduan->uraian,
            'catatan' => $pengaduan->catatan,
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
     * TAHAP 1: PENGADUAN BARU DIBUAT
     * Email ke: Pelapor + Semua WBS Eksternal
     */
    public function sendNewPengaduanNotifications($pengaduanData, $userId = null)
    {
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
        $wbsEksEmails = $this->getEmailsByRoleId(self::ROLE_WBS_EKSTERNAL);
        $contentToWbsEks = "
            <h3>TUGAS BARU - Pengaduan Masuk</h3>
            <p>Ada pengaduan baru yang membutuhkan review segera:</p>
            
            <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Kode Pengaduan:</strong> {$pengaduanData['code_pengaduan']}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br>
                <strong>Telepon:</strong> {$pengaduanData['telepon_pelapor']}<br> 
                <strong>Waktu Kejadian:</strong> {$pengaduanData['waktu_kejadian']}<br>
                <strong>Direktorat:</strong> {$pengaduanData['direktorat']}
            </div>

            <p><strong>Uraian Pengaduan:</strong><br>{$pengaduanData['uraian']}</p>
            
            <div style='margin-top: 20px; padding: 10px; background: #dc3545; color: white; border-radius: 5px;'>
                <strong>Segera tinjau pengaduan ini!</strong>
            </div>
        ";

        foreach ($wbsEksEmails as $email) {
            $this->sendNotification(
                $email,
                "Tugas Baru: Pengaduan {$pengaduanData['code_pengaduan']}",
                $contentToWbsEks,
                'urgent'
            );
        }
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

            <p><em>Pengaduan ini telah ditutup dan tidak dapat diproses lebih lanjut.</em></p>
        ";

        $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Pengaduan Ditolak - ' . $pengaduanData['code_pengaduan'],
            $contentToPelapor, 
            'reject'
        );

        // 2. EMAIL KE SEMUA WBS EKSTERNAL (Notifikasi tugas selesai)
        $wbsEksEmails = $this->getEmailsByRoleId(self::ROLE_WBS_EKSTERNAL);
        $contentToWbsEksTeam = "
            <h3>Tugas Selesai - Pengaduan Ditolak</h3>
            <p>Pengaduan <strong>{$pengaduanData['code_pengaduan']}</strong> telah ditolak oleh kolega Anda.</p>
            
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Alasan Penolakan:</strong> {$alasanReject}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br> 
            </div>

            <p><em>Pengaduan ini telah ditutup.</em></p>
        ";

        foreach ($wbsEksEmails as $email) {
            $this->sendNotification(
                $email,
                "Tugas Selesai: Pengaduan {$pengaduanData['code_pengaduan']} Ditolak",
                $contentToWbsEksTeam,
                'info'
            );
        }
    }

    /**
     * TAHAP 3: WBS EKSTERNAL SUBMIT KE WBS INTERNAL
     * Email ke: Pelapor + Semua WBS Internal
     */
    public function sendSubmitToWbsInternal($pengaduanData)
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
        $wbsIntEmails = $this->getEmailsByRoleId(self::ROLE_WBS_INTERNAL);
        $contentToWbsInt = "
            <h3>🎯 TUGAS BARU - Pengaduan dari WBS Eksternal</h3>
            <p>WBS Eksternal telah mengirimkan pengaduan untuk ditindaklanjuti:</p>
            
            <div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Kode Pengaduan:</strong> {$pengaduanData['code_pengaduan']}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br>
                <strong>Telepon:</strong> {$pengaduanData['telepon_pelapor']}<br>
                
                <strong>Direktorat:</strong> {$pengaduanData['direktorat']}
            </div>

            <p><strong>Uraian Pengaduan:</strong><br>{$pengaduanData['uraian']}</p>
            
            <div style='margin-top: 20px; padding: 10px; background: #17a2b8; color: white; border-radius: 5px;'>
                📋 <strong>Tentukan tindakan selanjutnya: CC, CCO, atau Forward</strong>
            </div>
        ";

        foreach ($wbsIntEmails as $email) {
            $this->sendNotification(
                $email,
                "Tugas Baru: Pengaduan {$pengaduanData['code_pengaduan']}",
                $contentToWbsInt,
                'urgent'
            );
        }
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

            <p><em>Pengaduan ini telah ditutup dan tidak dapat diproses lebih lanjut.</em></p>
        ";

        $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Pengaduan Ditolak - ' . $pengaduanData['code_pengaduan'],
            $contentToPelapor, 
            'reject'
        );

        // 2. EMAIL KE SEMUA WBS INTERNAL (Notifikasi tugas selesai)
        $wbsIntEmails = $this->getEmailsByRoleId(self::ROLE_WBS_INTERNAL);
        $contentToWbsIntTeam = "
            <h3>Tugas Selesai - Pengaduan Ditolak</h3>
            <p>Pengaduan <strong>{$pengaduanData['code_pengaduan']}</strong> telah ditolak oleh kolega Anda.</p>
            
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Alasan Penolakan:</strong> {$alasanReject}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br> 
            </div>

            <p><em>Pengaduan ini telah ditutup.</em></p>
        ";

        foreach ($wbsIntEmails as $email) {
            $this->sendNotification(
                $email,
                "Tugas Selesai: Pengaduan {$pengaduanData['code_pengaduan']} Ditolak",
                $contentToWbsIntTeam,
                'info'
            );
        }
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
                <strong>Penanggung Jawab:</strong> Tim Carbon Copy (CC)
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
        $wbsCcEmails = $this->getEmailsByRoleId(self::ROLE_WBS_CC);
        $contentToWbsCc = "
            <h3>📎 TUGAS BARU - Pengaduan dari WBS Internal</h3>
            <p>Anda ditugaskan sebagai CC untuk menangani pengaduan berikut:</p>
            
            <div style='background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Kode Pengaduan:</strong> {$pengaduanData['code_pengaduan']}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br>
                <strong>Telepon:</strong> {$pengaduanData['telepon_pelapor']}<br>
                
                <strong>Direktorat:</strong> {$pengaduanData['direktorat']}
            </div>

            " . (!empty($instruksiKhusus) ? "
            <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Instruksi Khusus dari WBS Internal:</strong><br>{$instruksiKhusus}
            </div>
            " : "") . "

            <p><strong>Uraian Pengaduan:</strong><br>{$pengaduanData['uraian']}</p>
        ";

        foreach ($wbsCcEmails as $email) {
            $this->sendNotification(
                $email,
                "Tugas CC: Pengaduan {$pengaduanData['code_pengaduan']}",
                $contentToWbsCc,
                'info'
            );
        }
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
        $wbsCcoEmails = $this->getEmailsByRoleId(self::ROLE_WBS_CCO);
        $contentToWbsCco = "
            <h3>🔍 TUGAS BARU - Pengaduan dari WBS Internal</h3>
            <p>Anda ditugaskan sebagai CCO untuk menangani pengaduan berikut:</p>
            
            <div style='background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Kode Pengaduan:</strong> {$pengaduanData['code_pengaduan']}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br>
                <strong>Telepon:</strong> {$pengaduanData['telepon_pelapor']}<br> 
                <strong>Direktorat:</strong> {$pengaduanData['direktorat']}
            </div>

            " . (!empty($instruksiKhusus) ? "
            <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Instruksi Khusus dari WBS Internal:</strong><br>{$instruksiKhusus}
            </div>
            " : "") . "

            <p><strong>Uraian Pengaduan:</strong><br>{$pengaduanData['uraian']}</p>
        ";

        foreach ($wbsCcoEmails as $email) {
            $this->sendNotification(
                $email,
                "Tugas CCO: Pengaduan {$pengaduanData['code_pengaduan']}",
                $contentToWbsCco,
                'info'
            );
        }
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
        $wbsFwdEmails = $this->getEmailsByRoleId(self::ROLE_WBS_FORWARD);
        $contentToWbsFwd = "
            <h3>↪️ TUGAS BARU - Pengaduan Diteruskan dari WBS Internal</h3>
            <p>Anda ditugaskan untuk menangani pengaduan yang diteruskan berikut:</p>
            
            <div style='background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Kode Pengaduan:</strong> {$pengaduanData['code_pengaduan']}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br>
                <strong>Telepon:</strong> {$pengaduanData['telepon_pelapor']}<br> 
                <strong>Direktorat:</strong> {$pengaduanData['direktorat']}
            </div>

            " . (!empty($instruksiKhusus) ? "
            <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Instruksi Khusus dari WBS Internal:</strong><br>{$instruksiKhusus}
            </div>
            " : "") . "

            <p><strong>Uraian Pengaduan:</strong><br>{$pengaduanData['uraian']}</p>
            
            <div style='margin-top: 20px; padding: 10px; background: #6c757d; color: white; border-radius: 5px;'>
                ℹ️ <strong>Anda dapat mengupdate status menjadi READ, kemudian data akan kembali ke WBS Internal</strong>
            </div>
        ";

        foreach ($wbsFwdEmails as $email) {
            $this->sendNotification(
                $email,
                "Tugas Forward: Pengaduan {$pengaduanData['code_pengaduan']}",
                $contentToWbsFwd,
                'info'
            );
        }
    }

    /**
     * TAHAP 8: FORWARD UPDATE STATUS KE READ
     * Email ke: Semua WBS Internal (notifikasi tugas selesai)
     */
    public function sendForwardCompleted($pengaduanData, $catatanDariForward = '')
    {
        $wbsIntEmails = $this->getEmailsByRoleId(self::ROLE_WBS_INTERNAL);
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

            <p><strong>Data telah kembali ke WBS Internal untuk tindakan selanjutnya.</strong></p>
        ";

        foreach ($wbsIntEmails as $email) {
            $this->sendNotification(
                $email,
                "Forward Selesai: Pengaduan {$pengaduanData['code_pengaduan']}",
                $contentToWbsInt,
                'info'
            );
        }
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

            <p><em>Pengaduan ini telah ditutup dan tidak dapat diproses lebih lanjut.</em></p>
        ";

        $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Pengaduan Ditolak - ' . $pengaduanData['code_pengaduan'],
            $contentToPelapor, 
            'reject'
        );

        // 2. EMAIL KE SEMUA USER ROLE TERSEBUT (Notifikasi tugas selesai)
        $roleEmails = $this->getEmailsByRoleId($roleId);
        $contentToRoleTeam = "
            <h3>Tugas Selesai - Pengaduan Ditolak</h3>
            <p>Pengaduan <strong>{$pengaduanData['code_pengaduan']}</strong> telah ditolak oleh kolega Anda di tim {$roleName}.</p>
            
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <strong>Alasan Penolakan:</strong> {$alasanReject}<br>
                <strong>Pelapor:</strong> {$pengaduanData['email_pelapor']}<br> 
            </div>

            <p><em>Pengaduan ini telah ditutup.</em></p>
        ";

        foreach ($roleEmails as $email) {
            $this->sendNotification(
                $email,
                "Tugas Selesai: Pengaduan {$pengaduanData['code_pengaduan']} Ditolak",
                $contentToRoleTeam,
                'info'
            );
        }
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

        return $this->sendNotification(
            $pengaduanData['email_pelapor'], 
            'Pengaduan Selesai - ' . $pengaduanData['code_pengaduan'],
            $contentToPelapor, 
            'success'
        );
    }
}