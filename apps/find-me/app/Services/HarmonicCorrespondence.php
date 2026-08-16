<?php

namespace App\Services;

use HarmonicDB\Laravel\HDBEException;
use Porter\Client\CollectionTickets;

final class HarmonicCorrespondence
{
    private CollectionTickets $tickets;

    public function __construct()
    {
        $this->tickets = new CollectionTickets(env('PORTER_IPC', '/porter'), 'find-me');
    }

    public function lodge(string $operation, array $parameters, int $ttl = 300): array
    {
        return $this->tickets->deposit('harmonicdb', 'hdbe.call', ['operation' => $operation, 'parameters' => $parameters, 'deposited_at_ms' => (int)round(microtime(true) * 1000)], $ttl);
    }

    public function inspect(string $ticket): array
    {
        return $this->tickets->inspect($ticket);
    }

    public function makeRound(array $tickets): array
    {
        return $this->tickets->makeRound($tickets);
    }

    public function abandon(string $ticket): array
    {
        return $this->tickets->abandon($ticket);
    }
    
    public function collect(string $ticket): array
    {
        $collection = $this->tickets->collect($ticket);
        if (!in_array($collection['state'], ['COLLECTED', 'ALREADY_COLLECTED'], true)) return $collection;
        $envelope = $collection['package']['payload']['envelope'] ?? null;
        if (!is_array($envelope) || ($envelope['protocol'] ?? null) !== 'HDBE/1') throw new HDBEException(['code' => 'ProtocolMismatch', 'message' => 'PORTER Return did not contain HDBE/1']);
        if (!($envelope['ok'] ?? false)) throw new HDBEException($envelope['error'] ?? ['code' => 'HostFailure', 'message' => 'HarmonicDB declined the correspondence']);
        $collection['envelope'] = $envelope;
        return $collection;
    }
}
