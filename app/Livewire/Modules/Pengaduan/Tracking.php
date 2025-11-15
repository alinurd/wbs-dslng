<?php
namespace App\Livewire\Modules\Pengaduan;

use App\Livewire\Root;
use App\Models\Combo;
use App\Models\Comment;
use App\Models\Pengaduan;

class Tracking extends Root
{
    public $modul = 'p_tracking';
    public $model = Pengaduan::class;
    public $views = 'modules.pengaduan.tracking';
    public $title = "Lacak Aduan";
    
    // Properties untuk chat
    public $trackingId = null;
    public $newMessage = '';
    public $messages = [];
    public $showComment = false; // Tambahkan ini
    public $detailData = []; // Tambahkan ini
    public $detailTitle = ''; // Tambahkan ini

    public function columns()
    {
        return ['code_pengaduan', 'perihal', 'tanggal_pengaduan', 'status'];
    }

    public function filterDefault()
    {
        return [
            ['f' => 'user_id', 'v' => auth()->id()],
        ];
    }

    public function mount()
    {
        parent::mount();
        $this->loadDropdownData();
    }

    public function query()
    {
        $q = ($this->model)::query();
        
        if (is_array($this->filters)) {
            foreach ($this->filters as $key => $val) {
                if ($key == 'tahun' && !empty($val)) {
                    $q->whereRaw('YEAR(tanggal_pengaduan) = ?', [$val]);
                }
                if ($key == 'jenis_pengaduan_id' && !empty($val)) {
                    $q->where('jenis_pengaduan_id', $val); // Hapus bracket array
                }
            }
            $q->where('user_id', auth()->id());
        }

        return $q;
    }

    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::findOrFail($id);

        $this->detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $record->jenis_pengaduan_id,
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $record->status ? 'Aktif' : 'Nonaktif',
        ];
        
        $this->detailTitle = "Detail " . $this->title;
        $this->showDetailModal = true;
    }

   public function comment($id)
{
    can_any([strtolower($this->modul).'.view']);
    
    // PERBAIKAN: Gunakan App\Models\Comment dalam relationship
    $record = $this->model::with(['comments.user'])->findOrFail($id);

    $this->detailData = [
        'Kode Tracking' => $record->code_pengaduan,
        'Perihal' => $record->perihal,
        'Jenis Pelanggaran' => $record->jenisPengaduan->name ?? 'Tidak diketahui',
        'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
        'Status' => $record->status ? 'Aktif' : 'Nonaktif',
        'Lokasi' => $record->alamat_kejadian ?? 'Tidak diketahui',
        'Deskripsi' => $record->uraian ?? 'Tidak ada deskripsi',
    ];
    
    $this->detailTitle = "Detail Pengaduan - " . $record->code_pengaduan;
    $this->showComment = true;
    $this->trackingId = $record->id;
    $this->loadMessages();
}

// Send message
public function sendMessage()
{
    $this->validate([
        'newMessage' => 'required|string|max:1000',
    ]);

    if (!$this->trackingId) return;

    // PERBAIKAN: Gunakan App\Models\Comment
    \App\Models\Comment::create([
        'pengaduan_id' => $this->trackingId,
        'user_id' => auth()->id(),
        'message' => $this->newMessage,
    ]);

    $this->newMessage = '';
    $this->loadMessages();
}

// Load messages untuk chat
public function loadMessages()
{
    if (!$this->trackingId) return;

    // PERBAIKAN: Gunakan App\Models\Comment
    $chatMessages = \App\Models\Comment::where('pengaduan_id', $this->trackingId)
        ->with('user')
        ->orderBy('created_at', 'asc')
        ->get();

    $this->messages = $chatMessages->map(function ($message) {
        return [
            'id' => $message->id,
            'message' => $message->message,
            'sender' => $message->user->name,
            'is_own' => $message->user_id === auth()->id(),
            'time' => $message->created_at->format('H:i'),
            'date' => $message->created_at->format('d M Y'),
            'avatar' => $message->user->profile_photo_url ?? null,
        ];
    })->toArray();
}
   
}