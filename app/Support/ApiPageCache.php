<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

class ApiPageCache
{
    protected const STORE = 'file';

    public static function remember(string $group, array $payload, int $ttlSeconds, Closure $callback): mixed
    {
        $version = self::version($group);
        $key = self::key($group, $version, $payload);

        return Cache::store(self::STORE)->remember($key, $ttlSeconds, $callback);
    }

    public static function bump(array|string $groups): void
    {
        foreach ((array) $groups as $group) {
            $versionKey = self::versionKey($group);
            $current = (int) Cache::store(self::STORE)->get($versionKey, 1);
            Cache::store(self::STORE)->forever($versionKey, $current + 1);
        }
    }

    protected static function version(string $group): int
    {
        return (int) Cache::store(self::STORE)->get(self::versionKey($group), 1);
    }

    protected static function versionKey(string $group): string
    {
        return "api-page-cache:version:{$group}";
    }

    protected static function key(string $group, int $version, array $payload): string
    {
        return sprintf(
            'api-page-cache:%s:v%d:%s',
            $group,
            $version,
            md5(json_encode($payload))
        );
    }
}
