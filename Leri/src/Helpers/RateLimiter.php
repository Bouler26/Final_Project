<?php

namespace Src\Helpers;
class RateLimiter {
    public static function check($key, $max = 5, $window = 5) {
        $file = __DIR__ . '/../../../logs/ratelimit_' . md5($key) . '.txt';
        $now = time();
        $hits = [];

        if (file_exists($file)) {
            $hits = array_filter(
                array_map('intval', explode("\n", trim(file_get_contents($file)))),
                fn($t) => $t > $now - $window
            );
        }

        if (count($hits) >= $max) return false;

        $hits[] = $now;
        file_put_contents($file, implode("\n", $hits));
        return true;
    }
}