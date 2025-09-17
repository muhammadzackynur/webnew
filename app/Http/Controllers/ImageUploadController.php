<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Tambahkan ini untuk logging

class ImageUploadController extends Controller
{
    /**
     * Menyimpan gambar yang diunggah dari bot.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        // 1. Validasi untuk memastikan file yang dikirim adalah gambar
        $validator = validator($request->all(), [
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Maksimal 2MB
            'nama_file' => 'required|string|max:255' // Tambahan validasi untuk nama file
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // 2. Ambil file dari request dan simpan dengan nama yang diberikan
            // 'gambar' adalah key yang akan dikirim oleh bot
            // 'uploads' adalah folder di dalam 'storage/app/public'
            $path = $request->file('gambar')->storeAs(
                'uploads', // Direktori penyimpanan
                $request->input('nama_file'), // Nama file dari bot
                'public' // Menggunakan disk 'public'
            );

            // 3. Dapatkan URL publik dari gambar yang baru diunggah
            $url = Storage::disk('public')->url($path);

            // 4. Kirim respon balik ke bot bahwa upload berhasil
            return response()->json([
                'message' => 'Gambar berhasil diunggah!',
                'path' => $path,
                'url' => $url
            ], 201); // 201 artinya 'Created'

        } catch (\Exception $e) {
            // Catat error ke log untuk debugging
            Log::error('Gagal mengunggah gambar: ' . $e->getMessage());

            // Kirim respon error ke bot
            return response()->json([
                'message' => 'Terjadi kesalahan di server saat mengunggah gambar.'
            ], 500);
        }
    }
}