<?php

namespace Src\Controllers;

use Src\Config\Database;
use Src\Helpers\Response;
use Src\Helpers\Jwt;
use PDO;

class AuthController extends BaseController
{
    /**
     * Menangani permintaan login.
     * @return mixed
     */
    public function login()
    {
        $contents = file_get_contents('php://input');
        $input = json_decode($contents, true) ?? [];

        // Validasi input
        if (empty($input['email']) || empty($input['password'])) {
            return Response::jsonError(422, 'Email and password required');
        }

        // Ambil user dari database
        $db = Database::connect(); // Asumsi: Database::connect() mengembalikan objek PDO
        $stmt = $db->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ?');
        $stmt->execute([$input['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifikasi kredensial
        if (!$user || !password_verify($input['password'], $user['password_hash'])) {
            return Response::jsonError(401, 'Invalid Credentials');
        }

        // Buat payload JWT
        $payload = [
            'uid' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role'],
        ];

        // Ambil secret dan exp dari konfigurasi
        $secret = $this->cfg['app']['jwt_secret']; // Asumsi: $this->cfg tersedia
        $exp = 3600; // Waktu kedaluwarsa 1 jam (3600 detik)

        // Buat token
        $token = Jwt::sign($payload, $secret, $exp);

        // Respon
        return Response::json(['token' => $token]);
    }
}