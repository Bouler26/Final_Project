<?php
namespace Src\Controllers;

use Src\Helpers\Response;

class UploadController extends BaseController
{
    public function store()
    {
        // Pastikan content-type bukan JSON
        if (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json')) {
            return $this->error(415, 'Use multipart/form-data for upload');
        }

        // Pastikan file dikirim
        if (empty($_FILES['file'])) {
            return $this->error(422, 'File is required');
        }

        $f = $_FILES['file'];

        // Cek error upload
        if ($f['error'] !== UPLOAD_ERR_OK) {
            return $this->error(400, 'Upload error');
        }

        // Cek ukuran maksimum 2MB
        if ($f['size'] > 2 * 1024 * 1024) {
            return $this->error(422, 'Max file size is 2MB');
        }

        // Deteksi MIME type file
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($f['tmp_name']);

        // Daftar tipe file yang diizinkan
        $allowed = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'application/pdf' => 'pdf'
        ];

        if (!isset($allowed[$mime])) {
            return $this->error(422, 'Invalid file type');
        }

        // Buat nama unik untuk file
        $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];

        // Tentukan lokasi folder uploads
        $destDir = __DIR__ . '/../../../uploads';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true); // buat folder kalau belum ada
        }

        $dest = $destDir . '/' . $name;

        // Simpan file
        if (!move_uploaded_file($f['tmp_name'], $dest)) {
            return $this->error(500, 'Save failed');
        }

        // Berhasil
        return $this->ok([
            'path' => "/uploads/$name"
        ], 201);
    }
}
