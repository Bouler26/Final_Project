<?php

namespace Src\Middlewares;

use Src\Helpers\Response;
use Src\Helpers\Jwt;

class AuthMiddleware
{
    /**
     * Middleware untuk memverifikasi token dan menyimpan data user.
     * @param array $cfg Konfigurasi (termasuk secret dan tempat penyimpanan user).
     * @return callable Fungsi yang akan dipanggil oleh router.
     */
    public static function user(array $cfg): callable
    {
        return function () use ($cfg) {
            // Ambil header Authorization
            $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

            // Cek format Bearer token: /^Bearer\s+([\w-]+\.[\w-]+\.[\w-]+)$/i
            if (!preg_match('/^Bearer\s+([\w-]+\.[\w-]+\.[\w-]+)$/i', $auth_header, $m)) {
                return Response::jsonError(401, 'Missing Token');
            }

            $token = $m[1];
            $pl = Jwt::verify($token, $cfg['app']['jwt_secret']);

            if (!$pl) {
                return Response::jsonError(401, 'Invalid/Expired Token');
            }

            // Simpan payload user ke konfigurasi/container
            $cfg['app']['user'] = $pl;

            return true; // Lanjut ke controller
        };
    }

    /**
     * Middleware untuk memverifikasi token dan memastikan peran user adalah 'admin'.
     * @param array $cfg Konfigurasi.
     * @return callable
     */
    public static function admin(array $cfg): callable
    {
        return function () use ($cfg) {
            // Asumsi middleware user() sudah dijalankan atau dijalankan di sini jika perlu
            $user_check = self::user($cfg)();
            if ($user_check !== true) {
                return $user_check; // Kembalikan error dari user()
            }
            
            $pl = $cfg['app']['user'];

            // Cek peran (role)
            if (!isset($pl['role']) || $pl['role'] !== 'admin') {
                return Response::jsonError(403, 'Forbidden');
            }

            return true; // Lanjut ke controller
        };
    }
}