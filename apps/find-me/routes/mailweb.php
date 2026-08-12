<?php

use Illuminate\Support\Facades\Http;
use App\Models\LocationRun;
use App\Models\PrivateLocationRun;
use MailWeb\Laravel\Facades\MailWeb;
use MailWeb\Laravel\Http\MailWebRequest;

$entries = [];

$site = MailWeb::template('find-me/site', fn () => MailWeb::page('Find Me')
    ->presentation('#20B8CD', '#07141D', '#F4E9CE', '#102733', 'mono', 'spacious', 'soft')
    ->heading('FIND ME', variant: 'display')
    ->nav('Find Me navigation', [
        ['Find me', '/'],
        ['The stack', '/stack'],
        ['Private records', '/private'],
        ['Present passport', '/passport'],
    ])
    ->slotPlaceholder('content')
    ->paragraph('No satellites. No GeoIP. No browser location. Just the Internet being asked a deeply unreasonable question.'));

MailWeb::get('/', function () use ($site) {
    $hero = MailWeb::enclosure(resource_path('mailweb/find-me-network.jpg'), 'find-me-network');

    return MailWeb::page('Find Me')
        ->template($site)
        ->slot('content', MailWeb::page('Find Me')
            ->image($hero, 'Terrestrial servers triangulating a receiver through luminous network paths', 'hero')
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
        $response = Http::timeout(5)->post(rtrim(env('PASSPORT_VERIFIER_URL', 'http://passport-verifier:8790'), '/').'/challenge');
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
        $response = Http::timeout(5)->post(rtrim(env('PASSPORT_VERIFIER_URL', 'http://passport-verifier:8790'), '/').'/verify', ['proof' => $proof]);
        $result = $response->json();
        if (! $response->successful() || ! ($result['ok'] ?? false)) {
            return MailWeb::page('Find Me — entry denied', 403)->template($site)->slot('content', MailWeb::page('Entry denied')
                ->heading('ENTRY DENIED', variant: 'display')
                ->paragraph('Passport verification failed: '.($result['status'] ?? 'INVALID_PROOF').'.')
                ->link('Request a fresh challenge', '/passport')
                ->link('Continue anonymously', '/'));
        }
        $credential = $proof['credential'];
        $entry = bin2hex(random_bytes(24)); $entries[$entry] = ['passport'=>$credential['passport_number'],'expires'=>time()+900];
        return MailWeb::page('Find Me — entry granted')->template($site)->slot('content', MailWeb::page('Entry granted')
            ->heading('ENTRY GRANTED', variant: 'display')
            ->paragraph('Passport '.$credential['passport_number'])
            ->paragraph('Mail identity '.$credential['mail_address'])
            ->paragraph('Authority seal VALID · holder proof VALID · audience VALID · challenge CONSUMED')
            ->paragraph('Revocation '.$result['status'].' · knowledge issued '.$result['revocation_knowledge'])
            ->button('Clear immigration', '/private?entry='.$entry, 'prominent'));
    } catch (Throwable $error) {
        return MailWeb::page('Find Me — entry denied', 403)->template($site)->slot('content', MailWeb::page('Entry denied')->heading('ENTRY DENIED', variant: 'display')->paragraph('The passport proof could not be verified.')->link('Request a fresh challenge', '/passport'));
    }
});

MailWeb::get('/private', function (MailWebRequest $request) use ($site, &$entries) {
    $entry=(string)$request->query('entry'); $session=$entries[$entry]??null;
    if(!$session || $session['expires']<time()) return MailWeb::page('Find Me — passport required',401)->template($site)->slot('content',MailWeb::page('Passport required')->heading('PRIVATE POSITIONING RECORDS',variant:'display')->paragraph('Technical Passport required.')->button('Present Passport','/passport','prominent')->button('Apply at Passport Office ↗','mailweb://passport.local/'));
    $runs=PrivateLocationRun::forPassport($session['passport']);
    $content=MailWeb::page('Private records')->heading('IMMIGRATION CLEARED',variant:'display')->paragraph('Passport '.$session['passport'])->button('Acquire authenticated network fix','/private/locate?entry='.$entry,'prominent')->heading('YOUR POSITIONING RUNS',level:2);
    if($runs===[]) $content->paragraph('No authenticated positioning runs are on file.');
    foreach(array_reverse($runs) as $run) { if($run->coordinate()==='BF:BOOTSTRAP') continue; $content->heading((string)$run->coordinate(),level:3)->paragraph(number_format($run->latitude,4).'°, '.number_format($run->longitude,4).'° · confidence '.number_format($run->confidence*100,1).'% · uncertainty ±'.number_format($run->uncertainty_km,1).' km'); }
    return MailWeb::page('Find Me — private records')->template($site)->slot('content',$content);
});

MailWeb::get('/private/locate', function (MailWebRequest $request) use ($site, &$entries) {
    $entry=(string)$request->query('entry'); $session=$entries[$entry]??null;
    if(!$session || $session['expires']<time()) return MailWeb::page('Entry expired',401)->heading('PASSPORT REQUIRED')->link('Return to immigration','/passport');
    try { $journey='BF:'.substr($request->id(),-12); $response=Http::timeout(150)->post(rtrim(env('GPSERVERS_URL','http://gpservers:8090'),'/').'/api/position',['correlation_id'=>$journey]); $response->throw(); $result=$response->json(); $fix=$result['fix']; $owner=PrivateLocationRun::owner($session['passport']); PrivateLocationRun::create($journey,[...$owner,'latitude'=>(float)$fix['latitude'],'longitude'=>(float)$fix['longitude'],'confidence'=>(float)$fix['confidence'],'uncertainty_km'=>(float)$fix['uncertainty_km']]); return MailWeb::page('Authenticated fix filed')->template($site)->slot('content',MailWeb::page('Filed')->heading('POSITIONING RUN FILED',variant:'display')->paragraph($journey.' is associated with the verified passport identity in HarmonicDB.')->button('Return to private records','/private?entry='.$entry,'prominent')); } catch(Throwable $e) { return MailWeb::page('No fix',503)->heading('NO FIX')->paragraph('No authenticated run was filed.')->link('Return','/private?entry='.$entry); }
});

MailWeb::get('/stack', function () use ($site) {
    return MailWeb::page('Find Me — the stack')
        ->template($site)
        ->slot('content', MailWeb::page('The stack')
            ->heading('FOUR IMPROBABLE MACHINES', variant: 'display')
            ->paragraph('Find Me is a small, conventional Laravel application. The unconventional part is everything it chooses to depend on.')
            ->heading('1 · MailWeb', level: 2)
            ->paragraph('The interface reaches you as correspondence. Requests and replies travel through SMTP as structured MailWeb documents; this page is not HTML pretending to be mail.')
            ->heading('2 · Global Positioning Servers', level: 2)
            ->paragraph('GPServers measures terrestrial network signals and calculates a probable fix. It knows nothing about this application, MailWeb, or where results should be stored.')
            ->heading('3 · HarmonicDB', level: 2)
            ->paragraph('Find Me stores each LocationRun in its own HarmonicDB through a Laravel package, then reads the observation back before replying. The database stores those values as measurable waves inside a real audio file.')
            ->heading('4 · Technical Passport', level: 2)
            ->paragraph('Postbox carries the holder’s protected wallet and can prove possession to Find Me. A local verifier trusts independently exported Authority material; the issuing Authority remains offline during entry.')
            ->heading('THE IMPORTANT BIT', level: 2)
            ->paragraph('The systems meet only at their public service boundaries. Find Me orchestrates them exactly as an ordinary client application would—no shared runtime, hidden filesystem shortcut, or crossed repository boundary.')
            ->button('Put the stack to work', '/locate', 'prominent'));
});

MailWeb::get('/locate', function (MailWebRequest $request) use ($site) {
    $journey = 'BF:'.substr($request->id(), -12);
    try {
        $response = Http::timeout(150)->post(rtrim(env('GPSERVERS_URL', 'http://gpservers:8090'), '/').'/api/position', [
            'correlation_id' => $journey,
        ]);
        $response->throw();
        $result = $response->json();
        $run = LocationRun::fromPositioningResult($journey, $result);
        $hdb = $run->evidence();
        return MailWeb::page('Find Me — fix acquired')
            ->template($site)
            ->slot('content', MailWeb::page('Probable location')
                ->heading('PROBABLE LOCATION', variant: 'display')
                ->paragraph(number_format($run->latitude, 4).'°, '.number_format($run->longitude, 4).'°')
                ->heading('Confidence')
                ->paragraph(number_format($run->confidence * 100, 1).'%')
                ->heading('Uncertainty')
                ->paragraph('± '.number_format($run->uncertainty_km, 1).' km')
                ->paragraph("Journey {$journey} · {$result['session_id']} · {$hdb['append']} · {$hdb['observation']}")
                ->paragraph('GPServers calculated the fix. Find Me stored its LocationRun in HarmonicDB and read it back before posting this reply.')
                ->button('Acquire another fix', '/locate', 'prominent'));
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
