<?php

namespace App\Services;

use App\Models\LocationRun;
use RuntimeException;

final class LocationRounds
{
    public function __construct(private readonly HarmonicCorrespondence $porter = new HarmonicCorrespondence) {}

    public function make(string $workId, ?string $interruptAfter = null): array
    {
        $work = LocationWork::load($workId);
        if (in_array($work['stage'], ['complete', 'abandoned'], true)) return $work;

        $round = $this->porter->makeRound([$work['ticket']]);
        $round['sequence'] = count($work['rounds'] ?? []) + 1;
        $status = $round['observations'][0];
        $work['rounds'] ??= [];
        $work['rounds'][] = $round;
        $work['last_round'] = $round;
        LocationWork::save($work);

        if ($interruptAfter === 'observation') throw new RuntimeException('Simulated Host interruption after observation');
        if ($status['state'] !== 'RETURN_HELD' && $status['state'] !== 'COLLECTED') return $work;

        $collection = $this->porter->collect($work['ticket']);
        if (!isset($collection['envelope'])) throw new RuntimeException('The held Return was contested during collection.');
        if ($interruptAfter === 'collection') throw new RuntimeException('Simulated Host interruption after collection');

        $work = LocationWork::load($workId);
        $work['last_round']['collected_at_ms'] = $this->now();
        $work['last_round']['return'] = $collection['return'];
        $last = array_key_last($work['rounds']);
        $work['rounds'][$last] = $work['last_round'];

        if ($work['stage'] === 'append_outstanding') {
            $work['append'] = $collection['envelope']['result']['evidence']['execution_id'];
            $ticket = $this->porter->lodge('observe', [
                'store' => 'find_me',
                'waves' => array_keys(LocationRun::attributesFromPositioningResult($work['positioning'])),
                'coordinate' => $work['journey'],
                'trace' => true,
            ]);
            $work['stage'] = 'observe_outstanding';
            $work['ticket'] = $ticket['ticket'];
            $work['last_round']['continued_at_ms'] = $this->now();
            $work['last_round']['continuation'] = 'observation_lodged';
        } else {
            $work['stage'] = 'complete';
            $work['observation'] = $collection;
            $work['last_round']['continued_at_ms'] = $this->now();
            $work['last_round']['continuation'] = 'journey_complete';
        }
        $work['rounds'][$last] = $work['last_round'];
        LocationWork::save($work);
        return $work;
    }

    private function now(): int
    {
        return (int)round(microtime(true) * 1000);
    }
}
