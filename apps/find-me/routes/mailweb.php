<?php

use Illuminate\Support\Facades\Http;
use App\Models\LocationRun;
use MailWeb\Laravel\Facades\MailWeb;
use MailWeb\Laravel\Http\MailWebRequest;

MailWeb::get('/', fn () => MailWeb::page('Find Me')
    ->heading('FIND ME', variant: 'display')
    ->paragraph('Ask the terrestrial network where this receiver probably is. No browser or device location will be used.')
    ->button('Acquire network fix', '/locate', 'prominent'));

MailWeb::get('/locate', function (MailWebRequest $request) {
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
            ->heading('PROBABLE LOCATION', variant: 'display')
            ->paragraph(number_format($run->latitude, 4).'°, '.number_format($run->longitude, 4).'°')
            ->heading('Confidence')
            ->paragraph(number_format($run->confidence * 100, 1).'%')
            ->heading('Uncertainty')
            ->paragraph('± '.number_format($run->uncertainty_km, 1).' km')
            ->paragraph("Journey {$journey} · {$result['session_id']} · {$hdb['append']} · {$hdb['observation']}")
            ->paragraph('Global Positioning Servers calculated the fix. Find Me stored its LocationRun model in its own HarmonicDB and read it back before posting this reply.')
            ->link('Acquire another fix', '/locate');
    } catch (Throwable $error) {
        return MailWeb::page('Find Me — no fix')->heading('NO FIX')
            ->paragraph('The positioning/database chain did not complete. No substitute location was used.')
            ->paragraph(mb_substr($error->getMessage(), 0, 300))
            ->paragraph($journey)
            ->link('Try again', '/locate');
    }
});
