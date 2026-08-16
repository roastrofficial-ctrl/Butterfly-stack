<?php

use Illuminate\Support\Facades\Http;
use App\Models\LocationRun;
use App\Models\PrivateLocationRun;
use App\Services\HarmonicCorrespondence;
use App\Services\LocationWork;
use MailWeb\Laravel\Facades\MailWeb;
use MailWeb\Laravel\Http\MailWebRequest;

$entries = [];

// Find Me owns Sight identity, source custody and disclosure policy. Lucida is a
// thin, generic server-side Draughtsman reached over HOST INTEGRATION TRANSPORT.
$prepareSight = function (string $privateAsset, int $maxPass = 4, string $detail = 'medium'): string {
    try {
        $response = Http::timeout(45)->post(rtrim(env('LUCIDA_URL', 'http://lucida:8077'), '/') . '/api/prepare', [
            'source_base64' => base64_encode(file_get_contents($privateAsset)),
            'detail' => $detail,
            'max_pass' => $maxPass,
        ]);
        $response->throw();
        return (string) $response->json('handle');
    } catch (Throwable) {
        return 'UNAVAILABLE';
    }
};

$site = MailWeb::template('find-me/site', fn() => MailWeb::page('Find Me')
    ->presentation('#20B8CD', '#07141D', '#F4E9CE', '#102733', 'mono', 'spacious', 'soft')
    ->heading('FIND ME', variant: 'display')
    ->nav('Find Me navigation', [
        ['Find me', '/'],
        ['The stack', '/stack'],
        ['Correspondence', '/correspondence'],
        ['Private records', '/private'],
        ['Present passport', '/passport'],
    ])
    ->slotPlaceholder('content')
    ->paragraph('No satellites. No GeoIP. No browser location. Just the Internet being asked a deeply unreasonable question.'));

MailWeb::get('/', function () use ($site, $prepareSight) {
    $hero = $prepareSight(resource_path('private/find-me-network.jpg'), 4, 'high');
    return MailWeb::page('Find Me')
        ->template($site)
        ->slot('content', MailWeb::page('Find Me')
            ->capabilitySurface('visual.observe', ['sight' => 'find-me.hero', 'prepared_sight' => $hero, 'initial_pass' => '4', 'initial_budget' => '280', 'refine_pass' => '4'], 'hero')
            ->heading('WHERE DOES THE INTERNET THINK YOU ARE?', variant: 'display')
            ->paragraph('A constellation of terrestrial servers will time the shape of the network, argue statistically about the evidence, and return a gloriously provisional position.')
            ->paragraph('Your device will not reveal its location. The network has to earn its answer.')
            ->paragraph('Oh, and be patient - GPServers aren\'t quick.')
            ->button('Acquire network fix', '/locate', 'prominent')
            ->button('Private positioning records', '/private')
            ->button('Present Technical Passport', '/passport'));
});

MailWeb::get('/passport', function () use ($site) {
    try {
        $response = Http::timeout(5)->post(rtrim(env('PASSPORT_VERIFIER_URL', 'http://passport-verifier:8790'), '/') . '/challenge');
        $response->throw();
        $challenge = $response->json('challenge');

        return MailWeb::page('Find Me — passport entry')
            ->template($site)
            ->slot('content', MailWeb::page('Passport entry')
                ->heading('FIND ME REQUESTS YOUR TECHNICAL PASSPORT', variant: 'display')
                ->paragraph('Anonymous positioning remains available. Presenting a passport proves who you are for this entry only; it does not create an account or positioning history.')
                ->heading('Service', level: 2)
                ->paragraph('find-me.local')
                ->heading('Identity disclosed', level: 2)
                ->paragraph('Passport number · mail address · holder identity · holder public key')
                ->clientAction('Present Passport', 'identity.present', '/passport/verify', [
                    'service' => 'find-me.local',
                    'challenge' => json_encode($challenge, JSON_THROW_ON_ERROR),
                ], ['passport_number', 'mail_address', 'holder_name', 'holder_public_key'])
                ->link('Continue anonymously', '/'));
    } catch (Throwable $error) {
        return MailWeb::page('Find Me — passport unavailable', 503)->template($site)->slot('content', MailWeb::page('Passport unavailable')->heading('PASSPORT ENTRY UNAVAILABLE')->paragraph('Find Me could not create a local verification challenge. Anonymous positioning is still available.')->link('Continue anonymously', '/'));
    }
});

MailWeb::post('/passport/verify', function (MailWebRequest $request) use ($site, &$entries) {
    try {
        $proof = $request->input('passport_proof');
        if (! is_array($proof)) {
            throw new RuntimeException('No presentable passport proof was received.');
        }
        $response = Http::timeout(5)->post(rtrim(env('PASSPORT_VERIFIER_URL', 'http://passport-verifier:8790'), '/') . '/verify', ['proof' => $proof]);
        $result = $response->json();
        if (! $response->successful() || ! ($result['ok'] ?? false)) {
            return MailWeb::page('Find Me — entry denied', 403)->template($site)->slot('content', MailWeb::page('Entry denied')
                ->heading('ENTRY DENIED', variant: 'display')
                ->paragraph('Passport verification failed: ' . ($result['status'] ?? 'INVALID_PROOF') . '.')
                ->link('Request a fresh challenge', '/passport')
                ->link('Continue anonymously', '/'));
        }
        $credential = $proof['credential'];
        $entry = bin2hex(random_bytes(24));
        $entries[$entry] = ['passport' => $credential['passport_number'], 'expires' => time() + 900];
        return MailWeb::page('Find Me — entry granted')->template($site)->slot('content', MailWeb::page('Entry granted')
            ->heading('ENTRY GRANTED', variant: 'display')
            ->paragraph('Passport ' . $credential['passport_number'])
            ->paragraph('Mail identity ' . $credential['mail_address'])
            ->paragraph('Authority seal VALID · holder proof VALID · audience VALID · challenge CONSUMED')
            ->paragraph('Revocation ' . $result['status'] . ' · knowledge issued ' . $result['revocation_knowledge'])
            ->button('Clear immigration', '/private?entry=' . $entry, 'prominent'));
    } catch (Throwable $error) {
        return MailWeb::page('Find Me — entry denied', 403)->template($site)->slot('content', MailWeb::page('Entry denied')->heading('ENTRY DENIED', variant: 'display')->paragraph('The passport proof could not be verified.')->link('Request a fresh challenge', '/passport'));
    }
});

MailWeb::get('/private', function (MailWebRequest $request) use ($site, &$entries) {
    $entry = (string)$request->query('entry');
    $session = $entries[$entry] ?? null;
    if (!$session || $session['expires'] < time()) return MailWeb::page('Find Me — passport required', 401)->template($site)->slot('content', MailWeb::page('Passport required')->heading('PRIVATE POSITIONING RECORDS', variant: 'display')->paragraph('Technical Passport required.')->button('Present Passport', '/passport', 'prominent')->button('Apply at Passport Office ↗', 'mailweb://passport.local/'));
    $runs = PrivateLocationRun::forPassport($session['passport']);
    $content = MailWeb::page('Private records')->heading('IMMIGRATION CLEARED', variant: 'display')->paragraph('Passport ' . $session['passport'])->button('Acquire authenticated network fix', '/private/locate?entry=' . $entry, 'prominent')->heading('YOUR POSITIONING RUNS', level: 2);
    if ($runs === []) $content->paragraph('No authenticated positioning runs are on file.');
    foreach (array_reverse($runs) as $run) {
        if ($run->coordinate() === 'BF:BOOTSTRAP') continue;
        $content->heading((string)$run->coordinate(), level: 3)->paragraph(number_format($run->latitude, 4) . '°, ' . number_format($run->longitude, 4) . '° · confidence ' . number_format($run->confidence * 100, 1) . '% · uncertainty ±' . number_format($run->uncertainty_km, 1) . ' km');
    }
    return MailWeb::page('Find Me — private records')->template($site)->slot('content', $content);
});

MailWeb::get('/private/locate', function (MailWebRequest $request) use ($site, &$entries) {
    $entry = (string)$request->query('entry');
    $session = $entries[$entry] ?? null;
    if (!$session || $session['expires'] < time()) return MailWeb::page('Entry expired', 401)->heading('PASSPORT REQUIRED')->link('Return to immigration', '/passport');
    try {
        $journey = 'BF:' . substr($request->id(), -12);
        $response = Http::timeout(150)->post(rtrim(env('GPSERVERS_URL', 'http://gpservers:8090'), '/') . '/api/position', ['correlation_id' => $journey]);
        $response->throw();
        $result = $response->json();
        $fix = $result['fix'];
        PrivateLocationRun::create($journey, ['owner' => $session['passport'], 'latitude' => (float)$fix['latitude'], 'longitude' => (float)$fix['longitude'], 'confidence' => (float)$fix['confidence'], 'uncertainty_km' => (float)$fix['uncertainty_km']]);
        return MailWeb::page('Authenticated fix filed')->template($site)->slot('content', MailWeb::page('Filed')->heading('POSITIONING RUN FILED', variant: 'display')->paragraph($journey . ' is associated with the verified passport identity in HarmonicDB.')->button('Return to private records', '/private?entry=' . $entry, 'prominent'));
    } catch (Throwable $e) {
        return MailWeb::page('No fix', 503)->heading('NO FIX')->paragraph('No authenticated run was filed.')->link('Return', '/private?entry=' . $entry);
    }
});

MailWeb::get('/stack', function () use ($site) {
    return MailWeb::page('Find Me — the stack')
        ->template($site)
        ->slot('content', MailWeb::page('The stack')
            ->heading('SIX IMPROBABLE MACHINES', variant: 'display')
            ->paragraph('Find Me is a small, conventional Laravel application. The unconventional part is everything it chooses to depend on.')
            ->heading('1 · MailWeb', level: 2)
            ->paragraph('The interface reaches you as correspondence. Requests and replies travel through SMTP as structured MailWeb documents; this page is not HTML pretending to be mail.')
            ->heading('2 · Global Positioning Servers', level: 2)
            ->paragraph('GPServers measures terrestrial network signals and calculates a probable fix. It knows nothing about this application, MailWeb, or where results should be stored.')
            ->heading('3 · HarmonicDB', level: 2)
            ->paragraph('Find Me stores each LocationRun in its own HarmonicDB through a Laravel package, then reads the observation back before replying. The database stores those values as measurable waves inside a real audio file.')
            ->heading('4 · Technical Passport', level: 2)
            ->paragraph('Postbox carries the holder’s protected wallet and can prove possession to Find Me. A local verifier trusts independently exported Authority material; the issuing Authority remains offline during entry.')
            ->heading('5 · Lucida', level: 2)
            ->paragraph('Find Me names remote Sights rather than image files. Lucida releases only permitted visual construction; its local Draughtsman builds a Rendition on the browser’s host display surface.')
            ->heading('6 · Porters', level: 2)
            ->paragraph('Services communicate through a network of Porters, which facilitate the exchange of information between different systems without Host systems being reachable on any network.')
            ->heading('THE IMPORTANT BIT', level: 2)
            ->paragraph('The systems meet only at their public service boundaries. Find Me orchestrates them exactly as an ordinary client application would—no shared runtime, hidden filesystem shortcut, or crossed repository boundary.')
            ->button('Put the stack to work', '/locate', 'prominent'));
});

MailWeb::get('/locate', function (MailWebRequest $request) use ($site) {
    $journey = 'BF:' . substr($request->id(), -12);
    try {
        $response = Http::timeout(150)->post(rtrim(env('GPSERVERS_URL', 'http://gpservers:8090'), '/') . '/api/position', [
            'correlation_id' => $journey,
        ]);
        $response->throw();
        $result = $response->json();
        $attributes = LocationRun::attributesFromPositioningResult($result);
        $ticket = (new HarmonicCorrespondence)->lodge('append', ['store' => 'find_me', 'domain' => 'journey', 'coordinate' => $journey, 'values' => $attributes, 'maintain_semantic_indexes' => false]);
        $work = LocationWork::create(['journey' => $journey, 'stage' => 'append_outstanding', 'ticket' => $ticket['ticket'], 'positioning' => $result]);
        return MailWeb::page('Find Me — correspondence lodged', 202)
            ->template($site)
            ->slot('content', MailWeb::page('Correspondence lodged')->heading('POSITIONING WORK LODGED', variant: 'display')->paragraph("Find Me deposited HarmonicDB work and ended this execution without waiting for an answer.")->paragraph("Package {$ticket['package']} · Collection Ticket {$ticket['ticket']} · Journey {$journey}")->button('Inspect outstanding correspondence', '/locate/correspondence?work=' . $work['work'], 'prominent')->button('View all correspondence', '/correspondence'));
    } catch (Throwable $error) {
        return MailWeb::page('Find Me — no fix')
            ->template($site)
            ->slot('content', MailWeb::page('No fix')
                ->heading('NO FIX', variant: 'display')
                ->paragraph('The positioning/database chain did not complete. No substitute location was used.')
                ->paragraph(mb_substr($error->getMessage(), 0, 300))
                ->paragraph($journey)
                ->button('Try again', '/locate', 'prominent'));
    }
});

MailWeb::get('/locate/correspondence', function (MailWebRequest $request) use ($site, $prepareSight) {
    try {
        $work = LocationWork::load((string)$request->query('work'));
        $porter = new HarmonicCorrespondence;
        if ($work['stage'] === 'complete') {
            $collection = $work['observation'];
        } else {
            $status = $porter->inspect($work['ticket']);
            if (in_array($status['state'], ['OUTSTANDING', 'EXPIRED_OBSERVED', 'ABANDONED', 'ABANDONED_WITH_RETURN'], true)) {
                $page = MailWeb::page('Outstanding correspondence')->heading('CORRESPONDENCE ' . $status['state'], variant: 'display')->paragraph("Ticket {$status['ticket']} · Package {$status['package']}")->paragraph('Inspecting is not collecting. This MailWeb execution observed the Porter and can now end again.');
                foreach (array_slice($status['events'], -6) as $event) $page->paragraph(($event['event'] ?? 'EVENT') . ' · ' . ($event['at_ms'] ?? 0));
                if ($status['state'] === 'OUTSTANDING') $page->button('Inspect again', '/locate/correspondence?work=' . $work['work'], 'prominent')->button('Abandon this work', '/locate/abandon?work=' . $work['work']);
                elseif ($status['state'] === 'ABANDONED_WITH_RETURN') $page->paragraph('A late Return remains held as evidence. PORTER did not discard it or revive the application journey.');
                return MailWeb::page('Find Me — outstanding', 202)->template($site)->slot('content', $page);
            }
            $collection = $porter->collect($work['ticket']);
            if (!isset($collection['envelope'])) throw new RuntimeException('The held Return was contested during collection.');
            if ($work['stage'] === 'append_outstanding') {
                $work['append'] = $collection['envelope']['result']['evidence']['execution_id'];
                $ticket = $porter->lodge('observe', ['store' => 'find_me', 'waves' => array_keys(LocationRun::attributesFromPositioningResult($work['positioning'])), 'coordinate' => $work['journey'], 'trace' => true]);
                $work['stage'] = 'observe_outstanding';
                $work['ticket'] = $ticket['ticket'];
                LocationWork::save($work);
                return MailWeb::page('Find Me — next correspondence lodged', 202)->template($site)->slot('content', MailWeb::page('Observation lodged')->heading('APPEND RETURN COLLECTED', variant: 'display')->paragraph("Find Me collected {$collection['return']}, recorded the append, deposited a separate observation Package, and can end again.")->paragraph("New Ticket {$ticket['ticket']} · Package {$ticket['package']}")->button('Inspect observation correspondence', '/locate/correspondence?work=' . $work['work'], 'prominent'));
            }
            $work['stage'] = 'complete';
            $work['observation'] = $collection;
            LocationWork::save($work);
        }
        $result = $work['positioning'];
        $fix = $result['fix'];
        $measures = $collection['envelope']['result']['measurements'];
        $evidence = $collection['envelope']['result']['evidence']['execution_id'];
        $map = $prepareSight(resource_path('private/find-me-map.png'), 3);
        return MailWeb::page('Find Me — fix acquired')->template($site)->slot('content', MailWeb::page('Probable location')->heading('PROBABLE LOCATION', variant: 'display')->paragraph(number_format((float)$fix['latitude'], 4) . '°, ' . number_format((float)$fix['longitude'], 4) . '°')->heading('Confidence')->paragraph(number_format((float)$fix['confidence'] * 100, 1) . '%')->heading('Uncertainty')->paragraph('± ' . number_format((float)($fix['credible_90_km'] ?? $fix['uncertainty_km']), 1) . ' km')->capabilitySurface('visual.observe', ['sight' => 'find-me.map', 'prepared_sight' => $map, 'initial_pass' => '1', 'refine_pass' => '3', 'knowledge_map' => 'true', 'overlay_position' => 'true', 'latitude' => (string)$fix['latitude'], 'longitude' => (string)$fix['longitude'], 'uncertainty_km' => (string)($fix['credible_90_km'] ?? $fix['uncertainty_km'])], 'map')->paragraph("Journey {$work['journey']} · {$result['session_id']} · {$work['append']} · {$evidence}")->paragraph('Two separate pieces of HarmonicDB correspondence were lodged and collected across multiple Find Me executions.')->button('Acquire another fix', '/locate', 'prominent'));
    } catch (Throwable $error) {
        return MailWeb::page('Correspondence failure', 503)->template($site)->slot('content', MailWeb::page('Failure')->heading('CORRESPONDENCE COULD NOT CONTINUE')->paragraph(mb_substr($error->getMessage(), 0, 300))->link('View correspondence', '/correspondence'));
    }
});

MailWeb::get('/locate/abandon', function (MailWebRequest $request) use ($site) {
    try {
        $work = LocationWork::load((string)$request->query('work'));
        $status = (new HarmonicCorrespondence)->abandon($work['ticket']);
        $work['stage'] = 'abandoned';
        LocationWork::save($work);
        return MailWeb::page('Find Me — abandoned')->template($site)->slot('content', MailWeb::page('Abandoned')->heading('CORRESPONDENCE ABANDONED', variant: 'display')->paragraph("Ticket {$status['ticket']} remains durable. A late Return may still be held, but Find Me will not resume this journey."));
    } catch (Throwable $error) {
        return MailWeb::page('Unknown correspondence', 404)->heading('UNKNOWN CORRESPONDENCE');
    }
});

MailWeb::get('/correspondence', function () use ($site) {
    $page = MailWeb::page('Correspondence')->heading('OUTSTANDING CORRESPONDENCE', variant: 'display')->paragraph('Durable Find Me continuation records. Inspecting one is an explicit new Host execution.');
    foreach (LocationWork::all() as $work) $page->heading($work['journey'] . ' · ' . $work['stage'], level: 2)->paragraph($work['work'] . ' · Ticket ' . ($work['ticket'] ?? 'collected'))->button('Inspect', '/locate/correspondence?work=' . $work['work']);
    return MailWeb::page('Find Me — correspondence')->template($site)->slot('content', $page);
});
