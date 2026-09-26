<?php

namespace App\Http\Controllers;

use App\Models\MasterShift;
use Illuminate\Http\Request;

class MasterShiftController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'kode_shift' => 'required|unique:master_shift,kode_shift',
            'nama_shift' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        MasterShift::create([
            'kode_shift' => strtoupper($request->kode_shift),
            'nama_shift' => $request->nama_shift,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Shift berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_shift' => 'required|unique:master_shift,kode_shift,' . $id,
            'nama_shift' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        $shift = MasterShift::findOrFail($id);

        $shift->update([
            'kode_shift' => strtoupper($request->kode_shift),
            'nama_shift' => $request->nama_shift,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Shift berhasil diperbarui');
    }

    public function destroy($id)
    {
        $dipakai = JadwalDinas::where('shift_id', $id)->exists();

        if ($dipakai) {
            return back()->with(
                'error',
                'Shift masih digunakan pada jadwal dinas.'
            );
        }

        MasterShift::findOrFail($id)->delete();

        return back()->with(
            'success',
            'Shift berhasil dihapus'
        );
    }
}