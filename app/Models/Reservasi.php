<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pengganti ReservasiModel (tabel `reservasi`).
 */
class Reservasi extends Model
{
    protected $table = 'reservasi';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'nama_pasien', 'telpon', 'dokter', 'layanan',
        'tanggal', 'jam', 'jaminan', 'catatan', 'foto', 'status',
    ];

    /** Kolom yang dikirim ke frontend (user_id tidak diekspos). */
    public const LIST_COLUMNS = [
        'id', 'nama_pasien', 'telpon', 'dokter', 'layanan', 'tanggal',
        'jam', 'jaminan', 'catatan', 'foto', 'status', 'created_at',
    ];

    public const STATUSES = ['menunggu', 'terkonfirmasi', 'dibatalkan', 'selesai'];

    public const FOTO_DIR = 'uploads/reservasi';

    protected function casts(): array
    {
        return ['tanggal' => 'date:Y-m-d'];
    }

    /** Format tanggal JSON sama seperti versi MVC lama: Y-m-d H:i:s */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected static function booted(): void
    {
        // Hapus file foto fisik saat record dihapus
        static::deleting(fn (Reservasi $r) => $r->hapusFileFoto());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hapusFileFoto(): void
    {
        if ($this->foto) {
            $path = public_path(self::FOTO_DIR.'/'.$this->foto);
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    // ── Query scopes (pengganti getByDate / getByUser) ───────────────────
    public function scopeTanggal($q, string $date)
    {
        return $q->where('tanggal', $date);
    }

    public function scopeMilik($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }
}
