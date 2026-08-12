<?php

use Illuminate\Support\Facades\Http;
use App\Models\LocationRun;
use MailWeb\Laravel\Facades\MailWeb;
use MailWeb\Laravel\Http\MailWebRequest;

$site = MailWeb::template('find-me/site', fn () => MailWeb::page('Find Me')
    ->presentation('#20B8CD', '#07141D', '#F4E9CE', '#102733', 'mono', 'spacious', 'soft')
    ->heading('FIND ME', variant: 'display')
    ->nav('Find Me navigation', [
        ['Find me', '/'],
        ['The stack', '/stack'],
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
            ->button('Acquire network fix', '/locate', 'prominent'));
});

MailWeb::get('/stack', function () use ($site) {
    return MailWeb::page('Find Me — the stack')
        ->template($site)
        ->slot('content', MailWeb::page('The stack')
            ->heading('THREE IMPROBABLE MACHINES', variant: 'display')
            ->paragraph('Find Me is a small, conventional Laravel application. The unconventional part is everything it chooses to depend on.')
            ->heading('1 · MailWeb', level: 2)
            ->paragraph('The interface reaches you as correspondence. Requests and replies travel through SMTP as structured MailWeb documents; this page is not HTML pretending to be mail.')
            ->heading('2 · Global Positioning Servers', level: 2)
            ->paragraph('GPServers measures terrestrial network signals and calculates a probable fix. It knows nothing about this application, MailWeb, or where results should be stored.')
            ->heading('3 · HarmonicDB', level: 2)
            ->paragraph('Find Me stores each LocationRun in its own HarmonicDB through a Laravel package, then reads the observation back before replying. The database stores those values as measurable waves inside a real audio file.')
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
