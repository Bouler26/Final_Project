<?php

namespace Src\Helpers;

class Jwt
{
    /**
     * Mengkodekan data ke format Base64URL.
     * @param string $data
     * @return string
     */
    private static function base64url_encode(string $data): string
    {
        // rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Mendekodekan data dari format Base64URL.
     * @param string $data
     * @return string|false
     */
    private static function base64url_decode(string $data): string|false
    {
        // Tambahkan padding '=' yang hilang jika ada
        $padding = strlen($data) % 4;
        if ($padding) {
            $data .= str_repeat('=', 4 - $padding);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Membuat tanda tangan HMAC-SHA256.
     * @param string $header_encoded
     * @param string $payload_encoded
     * @param string $secret
     * @return string
     */
    private static function hmac_sha256(string $header_encoded, string $payload_encoded, string $secret): string
    {
        $data = $header_encoded . '.' . $payload_encoded;
        $signature = hash_hmac('sha256', $data, $secret, true);
        return self::base64url_encode($signature);
    }

    /**
     * Membuat JSON Web Token (JWT).
     * @param array $payload Data yang akan dimuat (payload).
     * @param string $secret Kunci rahasia untuk tanda tangan.
     * @param int $exp Waktu kedaluwarsa dalam detik.
     * @return string JWT yang sudah ditandatangani.
     */
    public static function sign(array $payload, string $secret, int $exp): string
    {
        // Header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        $slg = self::base64url_encode(json_encode($header));

        // Payload (dengan iat dan exp)
        $payload['iat'] = time();
        $payload['exp'] = time() + $exp;
        $spl = self::base64url_encode(json_encode($payload));

        // Tanda Tangan
        $sseg = self::hmac_sha256($slg, $spl, $secret);

        return implode('.', [$slg, $spl, $sseg]);
    }

    /**
     * Memverifikasi JWT.
     * @param string $jwt Token yang akan diverifikasi.
     * @param string $secret Kunci rahasia.
     * @return array|null Payload jika token valid dan belum kedaluwarsa, null jika sebaliknya.
     */
    public static function verify(string $jwt, string $secret): ?array
    {
        $sp = explode('.', $jwt);

        if (count($sp) !== 3) {
            return null;
        }

        [$sh, $sb, $ss] = $sp;

        // Hitung ulang tanda tangan dan bandingkan
        $ss_hash = self::hmac_sha256($sh, $sb, $secret);

        if (!hash_equals($ss, $ss_hash)) {
            return null;
        }

        // Dekode payload
        $pl = json_decode(self::base64url_decode($sb), true);

        // Cek kedaluwarsa
        if (isset($pl['exp']) && ($pl['exp'] <= time())) {
            return null;
        }

        return $pl;
    }
} 