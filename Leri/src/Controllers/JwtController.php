<?php

namespace Src\Controllers;

use Src\Helpers\Response;
use Src\Helpers\Jwt;

class JwtController extends BaseController
{
    /**
     * Generate new JWT token
     * @return mixed
     */
    public function generate()
    {
        $contents = file_get_contents('php://input');
        $input = json_decode($contents, true) ?? [];

        // Default payload if not provided
        $payload = $input['payload'] ?? [
            'sub' => 3,
            'name' => 'Leri',
            'role' => 'admin'
        ];

        $secret = $this->cfg['app']['jwt_secret'] ?? 'password_hash_leri_99_numerik_api';
        $exp = $input['exp'] ?? 60; // Default 1 minute

        $token = Jwt::sign($payload, $secret, $exp);

        return Response::json([
            'status' => 'token_generated',
            'token' => $token,
            'payload' => $payload,
            'expired_at' => date('Y-m-d H:i:s', time() + $exp)
        ]);
    }

    /**
     * Verify JWT token
     * @return mixed
     */
    public function verify()
    {
        $contents = file_get_contents('php://input');
        $input = json_decode($contents, true) ?? [];

        $token = $input['token'] ?? '';

        if (empty($token)) {
            return Response::jsonError(400, 'Token is required');
        }

        $secret = $this->cfg['app']['jwt_secret'] ?? 'password_hash_leri_99_numerik_api';
        $payload = Jwt::verify($token, $secret);

        if (!$payload) {
            return Response::jsonError(401, 'Invalid or expired token');
        }

        return Response::json([
            'status' => 'valid',
            'message' => 'Token is valid',
            'payload' => $payload,
            'expires_in_seconds' => $payload['exp'] - time(),
            'expired_at' => date('Y-m-d H:i:s', $payload['exp']),
            'now' => date('Y-m-d H:i:s')
        ]);
    }
}