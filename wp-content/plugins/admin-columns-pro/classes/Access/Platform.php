<?php

declare(strict_types=1);

namespace ACP\Access;

final class Platform
{
    public static function is_local(): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        return in_array($ip, ['127.0.0.1', '::1'], true);
    }

}
