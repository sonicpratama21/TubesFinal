<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API reservasi (JSON). Kontrak respons sama dengan versi MVC lama,
 * sehingga public/js/user.js & admin.js tetap bekerja.
 */
class ReservasiController extends Controller
{
    private const REQUIRED = ['nama', 'telp', 'dokter', 'layanan', 'tanggal', 'jam'];

    // GET /reservasi
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->error('Unauthorized');
        }

        $limit = $request->has('limit') ? max(1, (int) $request->query('limit')) : 50;
        $date  = $request->query('date', date('Y-m-d'));

        $query = $user->isAdmin()
            ? Reservasi::tanggal($date)
            : Reservasi::milik($user->id);

        $total = (clone $query)->count();
        $rows  = $query->orderByDesc('created_at')->orderByDesc('id')
                       ->limit($limit)->get(Reservasi::LIST_COLUMNS);

        return response()->json([
            'status' => 'success',
            'date'   => $date,
            'total'  => $total,
            'data'   => $rows,
        ]);
    }

    // POST /reservasi
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->error('Silakan login terlebih dahulu.');
        }

        $data = $this->payload($request, [
            'jaminan' => 'Umum / Mandiri',
        ]);

        if ($this->missingRequired($data)) {
            return $this->error('Field wajib tidak lengkap.');
        }

        $reservasi = Reservasi::create($this->toColumns($data) + ['user_id' => $user->id]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Reservasi berhasil disimpan.',
            'id'      => $reservasi->id,
        ]);
    }

    // PATCH /reservasi  (admin only: ubah status)
    public function updateStatus(Request $request): JsonResponse
    {
        if (! $request->user()?->isAdmin()) {
            return $this->error('Unauthorized');
        }

        $id     = (int) $request->input('id', 0);
        $status = (string) $request->input('status', '');

        if (! $id || ! in_array($status, Reservasi::STATUSES, true)) {
            return $this->error('Data tidak valid.');
        }

        $reservasi = Reservasi::find($id);
        if (! $reservasi) {
            return $this->error('Gagal memperbarui status.');
        }

        $reservasi->update(['status' => $status]);

        return response()->json(['status' => 'success', 'message' => 'Status diperbarui.']);
    }

    // PUT /reservasi  (pemilik atau admin)
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->error('Silakan login terlebih dahulu.');
        }

        $id = (int) $request->input('id', 0);
        if (! $id) {
            return $this->error('ID reservasi tidak valid.');
        }

        $row = Reservasi::find($id);
        if (! $row) {
            return $this->error('Reservasi tidak ditemukan.');
        }
        if (! $this->canManage($user, $row)) {
            return $this->error('Unauthorized');
        }

        $data = $this->payload($request, [
            'nama'    => $row->nama_pasien,
            'telp'    => $row->telpon,
            'dokter'  => $row->dokter,
            'layanan' => $row->layanan,
            'tanggal' => $row->tanggal?->format('Y-m-d') ?? '',
            'jam'     => $row->jam,
            'jaminan' => $row->jaminan,
            'catatan' => $row->catatan,
        ]);

        if ($this->missingRequired($data)) {
            return $this->error('Field wajib tidak lengkap.');
        }

        $row->update($this->toColumns($data));

        return response()->json(['status' => 'success', 'message' => 'Reservasi berhasil diperbarui.']);
    }

    // DELETE /reservasi  (pemilik atau admin)
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->error('Silakan login terlebih dahulu.');
        }

        $id = (int) ($request->input('id') ?? $request->query('id') ?? 0);
        if (! $id) {
            return $this->error('ID reservasi tidak valid.');
        }

        $row = Reservasi::find($id);
        if (! $row) {
            return $this->error('Reservasi tidak ditemukan.');
        }
        if (! $this->canManage($user, $row)) {
            return $this->error('Unauthorized');
        }

        $row->delete(); // file foto ikut terhapus (lihat Reservasi::booted)

        return response()->json(['status' => 'success', 'message' => 'Reservasi berhasil dihapus.']);
    }

    // POST /reservasi/foto  (multipart: id + foto)
    public function foto(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->error('Silakan login terlebih dahulu.');
        }

        $id = (int) $request->input('id', 0);
        if (! $id) {
            return $this->error('ID reservasi tidak valid.');
        }

        $row = Reservasi::find($id);
        if (! $row) {
            return $this->error('Reservasi tidak ditemukan.');
        }
        if (! $this->canManage($user, $row)) {
            return $this->error('Unauthorized');
        }

        $file = $request->file('foto');
        if (! $file || ! $file->isValid()) {
            return $this->error('Tidak ada file foto yang diunggah.');
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return $this->error('Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.');
        }
        if ($file->getSize() > 5 * 1024 * 1024) {
            return $this->error('Ukuran file maksimal 5MB.');
        }

        $filename = 'res_' . $id . '_' . time() . '.' . $ext;
        $file->move(public_path(Reservasi::FOTO_DIR), $filename);

        // Hapus foto lama
        $row->hapusFileFoto();
        $row->update(['foto' => $filename]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Foto berhasil diunggah.',
            'foto'    => $filename,
            'url'     => url(Reservasi::FOTO_DIR . '/' . $filename),
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────
    private function payload(Request $request, array $defaults = []): array
    {
        $out = [];
        foreach (['nama', 'telp', 'dokter', 'layanan', 'tanggal', 'jam', 'jaminan', 'catatan'] as $k) {
            $out[$k] = trim((string) ($request->input($k, $defaults[$k] ?? '') ?? ''));
        }
        return $out;
    }

    private function missingRequired(array $data): bool
    {
        foreach (self::REQUIRED as $k) {
            if ($data[$k] === '') {
                return true;
            }
        }
        return false;
    }

    /** Petakan nama field form -> nama kolom tabel. */
    private function toColumns(array $d): array
    {
        return [
            'nama_pasien' => $d['nama'],
            'telpon'      => $d['telp'],
            'dokter'      => $d['dokter'],
            'layanan'     => $d['layanan'],
            'tanggal'     => $d['tanggal'],
            'jam'         => $d['jam'],
            'jaminan'     => $d['jaminan'],
            'catatan'     => $d['catatan'],
        ];
    }

    private function canManage($user, Reservasi $row): bool
    {
        return $user->isAdmin() || (int) $row->user_id === (int) $user->id;
    }

    private function error(string $message): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => $message]);
    }
}
