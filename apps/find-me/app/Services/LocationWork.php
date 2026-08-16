<?php

namespace App\Services;

use RuntimeException;

final class LocationWork
{
    private static function root(): string
    {
        $root = env('FIND_ME_WORK_DIR', '/app-state/location');
        if (!is_dir($root) && !mkdir($root, 0770, true) && !is_dir($root)) throw new RuntimeException('Cannot create correspondence work ledger');
        return rtrim($root, '/');
    }
    public static function create(array $value): array
    {
        $value = ['work' => 'LW-' . bin2hex(random_bytes(16)), 'created_at_ms' => (int)round(microtime(true) * 1000), ...$value];
        self::save($value);
        return $value;
    }
    public static function save(array $value): void
    {
        $target = self::root() . '/' . $value['work'] . '.json';
        $temporary = $target . '.' . bin2hex(random_bytes(4)) . '.tmp';
        file_put_contents($temporary, json_encode($value, JSON_THROW_ON_ERROR));
        rename($temporary, $target);
    }
    public static function load(string $id): array
    {
        if (!preg_match('/^LW-[a-f0-9]{32}$/', $id)) throw new RuntimeException('Unknown positioning correspondence');
        $value = json_decode((string)@file_get_contents(self::root() . '/' . $id . '.json'), true);
        if (!is_array($value)) throw new RuntimeException('Unknown positioning correspondence');
        return $value;
    }
    public static function all(): array
    {
        $items = [];
        foreach (glob(self::root() . '/LW-*.json') ?: [] as $path) {
            $value = json_decode((string)file_get_contents($path), true);
            if (is_array($value)) $items[] = $value;
        }
        usort($items, fn($a, $b) => $b['created_at_ms'] <=> $a['created_at_ms']);
        return $items;
    }
}
