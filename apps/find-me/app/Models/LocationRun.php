<?php

namespace App\Models;

use HarmonicDB\Laravel\Model;

final class LocationRun extends Model
{
    protected static string $store = 'find_me';
    protected static string $domain = 'journey';
    protected static array $waves = [
        'latitude',
        'longitude',
        'confidence',
        'uncertainty_km',
        'median_latency_ms',
        'mean_jitter_ms',
        'network_quality',
        'signals_acquired',
    ];
    protected static bool $maintainSemanticIndexes = false;

    public static function fromPositioningResult(string $journey, array $result): self
    {
        return self::create($journey,self::attributesFromPositioningResult($result));
    }

    public static function attributesFromPositioningResult(array $result): array
    {
        $successful = array_values(array_filter(
            $result['measurements'] ?? [],
            fn (array $measurement) => ($measurement['success_rate'] ?? 0) > 0,
        ));
        $mean = fn (string $key): float => $successful === [] ? 0.0 : array_sum(array_column($successful, $key)) / count($successful);
        $fix = $result['fix'];

        return [
            'latitude' => (float) $fix['latitude'],
            'longitude' => (float) $fix['longitude'],
            'confidence' => (float) $fix['confidence'],
            'uncertainty_km' => (float) ($fix['credible_90_km'] ?? $fix['uncertainty_km']),
            'median_latency_ms' => $mean('median_ms'),
            'mean_jitter_ms' => $mean('jitter_ms'),
            'network_quality' => (float) ($result['network_weather']['nqi'] ?? 0),
            'signals_acquired' => (float) count($successful),
        ];
    }
}
