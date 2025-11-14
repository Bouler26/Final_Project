<?php

namespace Src\Controllers;

use Src\Helpers\Response;

class ApiContractController extends BaseController
{
    /**
     * Get API contract/documentation
     * @return mixed
     */
    public function index()
    {
        $api_contract = [
            [
                "endpoint" => "/api/v1/health",
                "method" => "GET",
                "description" => "Cek status kesehatan API",
                "request_body" => null,
                "response" => [
                    "status" => "success",
                    "message" => "API is healthy"
                ],
                "status_code" => 200,
                "version" => "v1"
            ],

            [
                "endpoint" => "/api/v1/auth/login",
                "method" => "POST",
                "description" => "Autentikasi user menggunakan email dan password",
                "request_body" => [
                    "email" => "string",
                    "password" => "string"
                ],
                "response" => [
                    "status" => "success",
                    "token" => "string"
                ],
                "status_code" => 200,
                "version" => "v1"
            ],

            [
                "endpoint" => "/api/v1/users",
                "method" => "GET",
                "description" => "Menampilkan daftar semua user",
                "request_body" => null,
                "response" => [
                    "status" => "success",
                    "data" => "array of users"
                ],
                "status_code" => 200,
                "version" => "v1"
            ],

            [
                "endpoint" => "/api/v1/users/{id}",
                "method" => "GET",
                "description" => "Menampilkan detail user berdasarkan ID",
                "request_body" => null,
                "response" => [
                    "status" => "success",
                    "data" => "user object"
                ],
                "status_code" => 200,
                "version" => "v1"
            ],

            [
                "endpoint" => "/api/v1/users",
                "method" => "POST",
                "description" => "Membuat user baru",
                "request_body" => [
                    "name" => "string",
                    "email" => "string",
                    "password" => "string"
                ],
                "response" => [
                    "status" => "success",
                    "message" => "User created successfully"
                ],
                "status_code" => 201,
                "version" => "v1"
            ],

            [
                "endpoint" => "/api/v1/users/{id}",
                "method" => "PUT",
                "description" => "Mengupdate data user berdasarkan ID",
                "request_body" => [
                    "name" => "string (optional)",
                    "email" => "string (optional)",
                    "password" => "string (optional)"
                ],
                "response" => [
                    "status" => "success",
                    "message" => "User updated successfully"
                ],
                "status_code" => 200,
                "version" => "v1"
            ],

            [
                "endpoint" => "/api/v1/users/{id}",
                "method" => "DELETE",
                "description" => "Menghapus user berdasarkan ID",
                "request_body" => null,
                "response" => [
                    "status" => "success",
                    "message" => "User deleted successfully"
                ],
                "status_code" => 200,
                "version" => "v1"
            ],

            [
                "endpoint" => "/api/v1/upload",
                "method" => "POST",
                "description" => "Mengupload file ke server",
                "request_body" => [
                    "file" => "binary"
                ],
                "response" => [
                    "status" => "success",
                    "file_url" => "string"
                ],
                "status_code" => 201,
                "version" => "v1"
            ],

            [
                "endpoint" => "/api/v1/version",
                "method" => "GET",
                "description" => "Menampilkan versi API saat ini",
                "request_body" => null,
                "response" => [
                    "status" => "success",
                    "version" => "v1"
                ],
                "status_code" => 200,
                "version" => "v1"
            ],

            [
                "endpoint" => "/api/v1/jwt/generate",
                "method" => "POST",
                "description" => "Generate JWT token baru",
                "request_body" => [
                    "payload" => "object (optional)",
                    "exp" => "integer (optional)"
                ],
                "response" => [
                    "status" => "token_generated",
                    "token" => "string",
                    "payload" => "object",
                    "expired_at" => "string"
                ],
                "status_code" => 200,
                "version" => "v1"
            ],

            [
                "endpoint" => "/api/v1/jwt/verify",
                "method" => "POST",
                "description" => "Verifikasi JWT token",
                "request_body" => [
                    "token" => "string"
                ],
                "response" => [
                    "status" => "valid",
                    "message" => "Token is valid",
                    "payload" => "object",
                    "expires_in_seconds" => "integer",
                    "expired_at" => "string",
                    "now" => "string"
                ],
                "status_code" => 200,
                "version" => "v1"
            ]
        ];

        return Response::json($api_contract);
    }
}