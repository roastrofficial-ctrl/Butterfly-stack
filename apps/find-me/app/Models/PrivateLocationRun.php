<?php

namespace App\Models;

use HarmonicDB\Laravel\Model;

final class PrivateLocationRun extends Model
{
    protected static string $store = 'find_me_private';
    protected static string $domain = 'journey';
    protected static array $waves = ['owner', 'latitude', 'longitude', 'confidence', 'uncertainty_km'];
    protected static bool $maintainSemanticIndexes = false;
    
    public static function forPassport(string $passport): array
    {
        return static::whereSymbolExact('owner', $passport);
    }
}
