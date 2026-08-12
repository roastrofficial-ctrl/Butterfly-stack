<?php

namespace App\Models;

use HarmonicDB\Laravel\Model;

final class PrivateLocationRun extends Model
{
    protected static string $store = 'find_me_private';
    protected static string $domain = 'journey';
    protected static array $waves = ['owner_a', 'owner_b', 'latitude', 'longitude', 'confidence', 'uncertainty_km'];
    protected static bool $maintainSemanticIndexes = false;
    
    public static function owner(string $passport): array
    {
        $h = hash('sha256', $passport, true);
        
        return ['owner_a' => (float)unpack('N', substr($h, 0, 4))[1], 'owner_b' => (float)unpack('N', substr($h, 4, 4))[1]];
    }
    
    public static function forPassport(string $passport): array
    {
        $o = static::owner($passport);

        $matches = fn (float $recovered, float $expected): bool =>
            abs($recovered - $expected) <= max(64.0, abs($expected) * 1.0e-7);

        return array_values(array_filter(static::all(), fn($r) =>
            $matches($r->owner_a, $o['owner_a']) && $matches($r->owner_b, $o['owner_b'])
        ));
    }
}
