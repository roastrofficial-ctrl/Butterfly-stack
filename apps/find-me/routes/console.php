<?php

use Illuminate\Support\Facades\Artisan;
use MailWeb\Laravel\Protocol\Publisher;
use Porter\Client\CollectionTickets;

Artisan::command('find-me:health', function () {
    $this->info('ok');
})->purpose('Verify that the standalone Find Me application can boot');

/* The application side of the language-neutral PORTER-HOST-ADAPTER/1 seam. */
Artisan::command('mailweb:adapter', function () {
    $ipc = env('PORTER_IPC', '/porter');
    $client = new CollectionTickets($ipc, 'find-me');
    $publisher = app(Publisher::class);
    $workRoot = rtrim(env('FIND_ME_WORK_DIR', '/app-state/location'), '/');
    $dispositions = $workRoot . '/mailweb-dispositions';
    @mkdir($dispositions, 0770, true);
    $writeDisposition = static function (string $path, array $value): void {
        $temporary = $path . '.tmp.' . bin2hex(random_bytes(6));
        file_put_contents($temporary, json_encode($value, JSON_THROW_ON_ERROR), LOCK_EX);
        rename($temporary, $path);
    };
    $containsRevisit = static function (mixed $value) use (&$containsRevisit): bool {
        if (!is_array($value)) return false;
        if (($value['type'] ?? null) === 'revisit') return true;
        foreach ($value as $nested) if ($containsRevisit($nested)) return true;
        return false;
    };

    fwrite(STDOUT, json_encode([
        'contract' => 'PORTER-HOST-ADAPTER/1',
        'runtime_observation' => 'ADAPTER_READY',
    ], JSON_THROW_ON_ERROR) . "\n");
    fflush(STDOUT);
    $dispatchCount = 0;

    while (($line = fgets(STDIN)) !== false) {
        $dispatchCount++;
        $dispatch = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
        if (($dispatch['contract'] ?? null) !== 'PORTER-HOST-ADAPTER/1') {
            throw new RuntimeException('Unsupported Host Runtime adapter contract');
        }
        $fact = $dispatch['collection'] ?? null;
        $package = $fact['package'] ?? null;
        if (!is_array($package) || ($package['kind'] ?? null) !== 'mailweb.request') {
            throw new RuntimeException('Find Me adapter received non-MailWeb correspondence');
        }
        $dispositionPath = $dispositions . '/' . $package['package'] . '.json';
        $disposition = is_file($dispositionPath)
            ? json_decode((string) file_get_contents($dispositionPath), true)
            : ['package' => $package['package'], 'collection' => $fact['collection'], 'state' => 'COLLECTED'];
        $nextVisitMs = 250;
        if (($disposition['state'] ?? null) !== 'RETURN_LODGED') {
            if (($disposition['state'] ?? null) === 'HANDLED') {
                $response = $disposition['response'];
            } else {
                $response = $publisher->handle($package['payload']['request'] ?? []);
                $disposition['state'] = 'HANDLED';
                $disposition['response'] = $response;
                $writeDisposition($dispositionPath, $disposition);
            }
            $nextVisitMs = $containsRevisit($response) ? 10 : 250;
            $ticket = $client->deposit('postbox', 'mailweb.return', ['response' => $response], 300, $package['package']);
            $disposition['state'] = 'RETURN_LODGED';
            $disposition['return_package'] = $ticket['package'];
            $disposition['return_ticket'] = $ticket['ticket'];
            unset($disposition['response']);
            $writeDisposition($dispositionPath, $disposition);
        }
        $crashAfter = (int) env('PORTER_EXPERIMENT_CRASH_AFTER_DISPATCHES', 0);
        $crashMarker = $workRoot . '/runtime-adapter-crash-once';
        if ($crashAfter === $dispatchCount && !is_file($crashMarker)) {
            file_put_contents($crashMarker, $fact['collection'] . "\n", LOCK_EX);
            throw new RuntimeException('Host Runtime experiment adapter death before control return');
        }
        fwrite(STDOUT, json_encode([
            'contract' => 'PORTER-HOST-ADAPTER/1',
            'dispatch' => $dispatch['dispatch'],
            'runtime_observation' => 'ADAPTER_RETURNED_CONTROL',
            'next_visit_ms' => $nextVisitMs,
        ], JSON_THROW_ON_ERROR) . "\n");
        fflush(STDOUT);
    }
    return 0;
})->purpose('Serve the local application side of the Host Runtime contract');

/* Compatibility entrypoint for the old proof and for episodic measurements. */
Artisan::command('mailweb:porter {--once : Make one local boundary visit and exit}', function () {
    $arguments = [
        'porter-host-runtime', '--host', 'find-me', '--kind', 'mailweb.request',
        '--batch-size', (string) env('PORTER_HOST_BATCH_SIZE', 10),
        '--idle-ms', (string) env('PORTER_HOST_IDLE_MS', 100),
        '--adapter', 'php artisan mailweb:adapter',
    ];
    if ($this->option('once')) $arguments[] = '--once';
    passthru(implode(' ', array_map('escapeshellarg', $arguments)), $status);
    return $status;
})->purpose('Run the shared local Host Runtime for Find Me');
