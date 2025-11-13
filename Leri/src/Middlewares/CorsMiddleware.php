<?php

namespace Src\Middlewares;

class CorsMiddleware
{
    /**
     * Menangani permintaan preflight OPTIONS dan mengatur header CORS.
     *
     * @param array
     * @return void
     */
    public static function handle(array $cfg)
    {
        // Mendapatkan origin dari request saat ini
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
        
        // Mendapatkan daftar origin yang diizinkan dari konfigurasi
        $allowed = $cfg['app']['allowed_origins'] ?? [];

        // 1. Cek jika origin saat ini ada dalam daftar yang diizinkan
        if (in_array($origin, $allowed, true)) {
            header("Access-Control-Allow-Origin: $origin");
            header('Vary: Origin');
        } 
        // 2. Jika daftar allowed kosong (berarti mengizinkan semua)
        else if (empty($allowed)) {
            header('Access-Control-Allow-Origin: *');
        }
        // Catatan: Jika origin tidak diizinkan dan $allowed tidak kosong, header ACAO tidak akan dikirim.
        
        // Header CORS tambahan yang diperlukan
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        // Set max-age untuk caching preflight (opsional, 1 jam)
        // header('Access-Control-Max-Age: 3600'); 
    }
}