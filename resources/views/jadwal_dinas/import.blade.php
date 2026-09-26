<?php

namespace App\Imports;

use App\Models\JadwalDinas;
use App\Models\MasterShift;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;

/**
 * Import Jadwal Dinas dari file CSV / XLSX.
 *
 * Format kolom yang diharapkan (header baris pertama, bebas urutan):
 *   nama   | tanggal     | shift
 *   --------------------------------
 *   Budi   | 2026-07-01  | P
 *   Siti   | 2026-07-01  | M
 *
 * Aturan:
 * - 'shift' wajib cocok dengan salah satu kode_shift di Master Shift.
 * - Tanggal wajib bisa diparse (format bebas, Carbon::parse).
 * - Kombinasi (nama, tanggal) yang sudah ada di DB akan DI-SKIP
 *   (tidak ditimpa, tidak dianggap error).
 * - Jika ada baris lain (selain duplikat) yang gagal validasi,
 *   SELURUH import dibatalkan (tidak ada yang disimpan) dan daftar
 *   error dikembalikan ke controller untuk ditampilkan ke user.
 */
class JadwalDinasImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /** @var array<int,string> Daftar pesan error, satu per baris bermasalah */
    public array $errors = [];

    /** @var int Jumlah baris yang berhasil disiapkan untuk disimpan */
    public int $successCount = 0;

    /** @var int Jumlah baris yang di-skip karena duplikat (nama+tanggal sudah ada) */
    public int $skippedCount = 0;

    /** @var array<int,array> Baris valid yang siap di-insert (dikumpulkan dulu, baru disimpan jika TIDAK ada error sama sekali) */
    public array $validRows = [];

    /** @var \Illuminate\Support\Collection Cache master shift, key = kode_shift huruf besar */
    private Collection $masterShifts;

    public function __construct()
    {
        $this->masterShifts = MasterShift::all()->keyBy(function ($shift) {
            return strtoupper(trim($shift->kode_shift));
        });
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            // Baris ke berapa di file (header = baris 1, data mulai baris 2)
            $excelRowNumber = $index + 2;

            $nama    = trim((string) ($row['nama'] ?? ''));
            $tanggal = trim((string) ($row['tanggal'] ?? ''));
            $shiftKode = trim((string) ($row['shift'] ?? ''));

            // --- Validasi kolom wajib ---
            if ($nama === '' || $tanggal === '' || $shiftKode === '') {
                $this->errors[] = "Baris {$excelRowNumber}: kolom 'nama', 'tanggal', dan 'shift' wajib diisi.";
                continue;
            }

            // --- Validasi & parsing tanggal ---
            try {
                $tanggalParsed = $this->parseTanggal($tanggal);
            } catch (\Exception $e) {
                $this->errors[] = "Baris {$excelRowNumber}: format tanggal '{$tanggal}' tidak valid.";
                continue;
            }

            // --- Validasi kode shift terhadap Master Shift ---
            $shiftKodeUpper = strtoupper($shiftKode);

            if (!$this->masterShifts->has($shiftKodeUpper)) {
                $this->errors[] = "Baris {$excelRowNumber}: kode shift '{$shiftKode}' tidak ditemukan di Master Shift.";
                continue;
            }

            $shift = $this->masterShifts->get($shiftKodeUpper);

            // --- Cek duplikat (nama + tanggal) terhadap data yang SUDAH ADA di DB ---
            $sudahAda = JadwalDinas::where('nama', $nama)
                ->where('tanggal', $tanggalParsed->format('Y-m-d'))
                ->exists();

            if ($sudahAda) {
                $this->skippedCount++;
                continue;
            }

            // --- Baris valid, simpan sementara ---
            $this->validRows[] = [
                'nama'       => $nama,
                'tanggal'    => $tanggalParsed->format('Y-m-d'),
                'shift_id'   => $shift->id,
                'shift'      => $shift->kode_shift,
                'jam'        => Carbon::parse($shift->jam_mulai)->format('H:i')
                                . ' - ' .
                                Carbon::parse($shift->jam_selesai)->format('H:i')
                                . ' WIB',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $this->successCount++;
        }
    }

    /**
     * Parse tanggal fleksibel: mendukung format umum (YYYY-MM-DD, DD/MM/YYYY, dst)
     * dan juga numeric Excel date serial (kalau dibaca sebagai angka oleh PhpSpreadsheet).
     */
    private function parseTanggal(string $value): Carbon
    {
        // Kasus: Excel kadang mengirim tanggal sebagai serial number (string angka)
        if (is_numeric($value)) {
            return Carbon::instance(
                \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)
            );
        }

        return Carbon::parse($value);
    }

    /**
     * Simpan seluruh baris valid ke database dalam satu batch insert.
     * Dipanggil oleh controller HANYA jika $this->errors kosong (rollback total jika ada error).
     */
    public function simpanSemua(): void
    {
        if (empty($this->validRows)) {
            return;
        }

        foreach (array_chunk($this->validRows, 500) as $chunk) {
            JadwalDinas::insert($chunk);
        }
    }
}