<?php

namespace App\Http\Controllers;

use App\Models\Kalibrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CetakKalibrasiController extends Controller
{
    /**
     * Preview atau streaming file PDF sertifikat asli yang diunggah secara Inline
     */
  public function viewSertifikat($id)
{
    $kalibrasi = Kalibrasi::findOrFail($id);

    if (!$kalibrasi->sertifikat_pdf) {
        abort(404, 'File sertifikat belum diunggah.');
    }

    // Pastikan fisik filenya benar-benar ada di folder storage/public
    if (!Storage::disk('public')->exists($kalibrasi->sertifikat_pdf)) {
        abort(404, 'Berkas fisik sertifikat tidak ditemukan di server.');
    }

    // Dapatkan URL publik untuk file tersebut (contoh: http://127.0.0.1:8000/storage/sertifikat_kalibrasi/xyz.pdf)
    $fileUrl = asset('storage/' . $kalibrasi->sertifikat_pdf);

    // Render halaman HTML yang memaksa browser menampilkan file di dalam bingkai (iframe)
    return response("
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <title>Pratinjau Sertifikat Kalibrasi</title>
            <style>
                body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; background-color: #f4f5f7; }
                iframe { width: 100%; height: 100%; border: none; }
            </style>
        </head>
        <body>
            <iframe src='{$fileUrl}'></iframe>
        </body>
        </html>
    ");
}
}