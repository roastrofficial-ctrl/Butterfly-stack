<?php

use Illuminate\Support\Facades\Artisan;
use MailWeb\Laravel\Protocol\Publisher;
use Porter\Client\CollectionTickets;

Artisan::command('find-me:health', function () {
    $this->info('ok');
})->purpose('Verify that the standalone Find Me application can boot');

Artisan::command('mailweb:porter {--once : Make one local boundary visit and exit}', function () {
    $ipc = env('PORTER_IPC', '/porter');
    $client = new CollectionTickets($ipc, 'find-me');
    $publisher = app(Publisher::class);
    $workRoot = rtrim(env('FIND_ME_WORK_DIR', '/app-state/location'), '/');
    $metrics = $workRoot . '/host-visits.jsonl';
    $dispositions = $workRoot . '/mailweb-dispositions';
    @mkdir(dirname($metrics), 0770, true);
    @mkdir($dispositions, 0770, true);
    $writeDisposition = static function (string $path, array $value): void {
        $temporary = $path . '.tmp.' . bin2hex(random_bytes(6));
        file_put_contents($temporary, json_encode($value, JSON_THROW_ON_ERROR), LOCK_EX);
        rename($temporary, $path);
    };
    $bootstrapMs = round((microtime(true) - LARAVEL_START) * 1000, 3);
    $this->info('Find Me Host policy is visiting its local Porter boundary.');
    do {
        $visitStarted = hrtime(true);
        $handled = 0;
        $paths = array_unique(array_merge(
            glob(rtrim($ipc, '/') . '/inbox/PKG-*.json') ?: [],
            glob(rtrim($ipc, '/') . '/collected/PKG-*.json') ?: [],
        ));
        foreach ($paths as $path) {
            $package = json_decode((string)file_get_contents($path), true);
            if (($package['kind'] ?? null) !== 'mailweb.request') continue;
            $dispositionPath = $dispositions . '/' . $package['package'] . '.json';
            $disposition = is_file($dispositionPath)
                ? json_decode((string)file_get_contents($dispositionPath), true)
                : null;
            if (($disposition['state'] ?? null) === 'RETURN_LODGED') continue;
            $collectionStarted = hrtime(true);
            $collection = $client->collectPackage($package['package']);
            $collectionMs = round((hrtime(true) - $collectionStarted) / 1e6, 3);
            if (!in_array($collection['state'], ['COLLECTED', 'ALREADY_COLLECTED'], true)) continue;
            if (!is_array($disposition)) {
                $disposition = [
                    'package' => $package['package'],
                    'collection' => $collection['collection'],
                    'state' => 'COLLECTED',
                ];
                $writeDisposition($dispositionPath, $disposition);
            }
            $handlingStarted = hrtime(true);
            if (($disposition['state'] ?? null) === 'HANDLED') {
                $response = $disposition['response'];
            } else {
                try {
                    $response = $publisher->handle($package['payload']['request'] ?? []);
                } catch (Throwable $error) {
                    $this->error("MailWeb Package {$package['package']}: {$error->getMessage()}");
                    continue;
                }
                $disposition['state'] = 'HANDLED';
                $disposition['response'] = $response;
                $writeDisposition($dispositionPath, $disposition);
            }
            $handlingMs = round((hrtime(true) - $handlingStarted) / 1e6, 3);
            $lodgementStarted = hrtime(true);
            $ticket = $client->deposit('postbox', 'mailweb.return', ['response' => $response], 300, $package['package']);
            $returnLodgementMs = round((hrtime(true) - $lodgementStarted) / 1e6, 3);
            $disposition['state'] = 'RETURN_LODGED';
            $disposition['return_package'] = $ticket['package'];
            $disposition['return_ticket'] = $ticket['ticket'];
            unset($disposition['response']);
            $writeDisposition($dispositionPath, $disposition);
            $handled++;
            file_put_contents($metrics, json_encode([
                'at_ms' => (int)round(microtime(true) * 1000),
                'package' => $package['package'],
                'collection' => $collection['collection'],
                'return_package' => $ticket['package'],
                'bootstrap_ms' => $bootstrapMs,
                'collection_ms' => $collectionMs,
                'handling_ms' => $handlingMs,
                'return_lodgement_ms' => $returnLodgementMs,
                'visit_ms' => round((hrtime(true) - $visitStarted) / 1e6, 3),
            ], JSON_THROW_ON_ERROR) . "\n", FILE_APPEND | LOCK_EX);
        }
        if ($this->option('once')) break;
        usleep($handled > 0 ? 10000 : 100000);
    } while (true);
    return 0;
})->purpose('Locally collect and dispatch Porter-carried MailWeb correspondence');
