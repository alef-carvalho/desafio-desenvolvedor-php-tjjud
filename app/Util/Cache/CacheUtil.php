<?php

namespace App\Util\Cache;

class CacheUtil
{
    public const SHORT = 30;         // 30s
    public const DEFAULT = 600;      // 10m
    public const LONG = 3600;        // 01h

    public static function key(string $prefix, array $args): string
    {
        $hash = sha1(json_encode(ksort($args)));
        return "{$prefix}:{$hash}";
    }
}
