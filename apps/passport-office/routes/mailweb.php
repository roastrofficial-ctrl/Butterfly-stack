<?php

use MailWeb\Laravel\Facades\MailWeb;
use MailWeb\Laravel\Http\MailWebRequest;
use Illuminate\Support\Facades\Http;

$office = MailWeb::template('passport-office/site', fn() => MailWeb::page('Butterfly Passport Office')
    ->presentation('#9B202F', '#F4E9D3', '#251A18', '#FFF9EA', 'editorial', 'spacious', 'square')
    ->heading('BUTTERFLY PASSPORT OFFICE', variant: 'display')->nav('Office navigation', [['Applications', '/'], ['Find Me', 'mailweb://find-me.local/']])->slotPlaceholder('content')->paragraph('Technical Passport Directorate · Issuance requires the Butterfly Passport Authority.'));

MailWeb::get('/', fn() => MailWeb::page('Passport Office')->template($office)->slot('content', MailWeb::page('Apply')
    ->heading('APPLICATIONS', variant: 'display')->paragraph('Apply for a portable Technical Passport. First submit your public details here. Technical Passport Service will then provide a protected local panel where you create a private Passport PIN of at least 8 characters.')
    ->paragraph('You will use the same PIN whenever you present the passport. It travels directly from that local panel to Technical Passport Service: Postbox, Passport Office and the Authority never receive it. Butterfly does not currently offer PIN recovery.')
    ->form('POST', '/apply', [MailWeb::text('holder_name', 'Holder name', 'Ada Butterfly', true), MailWeb::text('mail_address', 'MailWeb correspondence identity', 'ada@butterfly.local', true)], 'Submit public application')));

MailWeb::post('/apply', function (MailWebRequest $request) use ($office) {
    $name = trim((string)$request->input('holder_name'));
    $mail = trim((string)$request->input('mail_address'));
    if ($name === '' || !filter_var($mail, FILTER_VALIDATE_EMAIL)) return MailWeb::page('Application refused', 422)->template($office)->slot('content', MailWeb::page('Invalid')->heading('APPLICATION INCOMPLETE')->link('Correct application', '/'));
    return MailWeb::page('Application accepted')->template($office)->slot('content', MailWeb::page('Accepted')->heading('PUBLIC APPLICATION ACCEPTED', variant: 'display')->paragraph('Technical Passport Service will now create your protected holder key locally. Passport Office will receive only the public key.')->clientAction('Create PIN and holder key', 'identity.enroll.prepare', '/certify', ['holder_name' => $name, 'mail_address' => $mail], ['holder_public_key', 'holder_name', 'mail_address']));
});

MailWeb::post('/certify', function (MailWebRequest $request) use ($office) {
    try {
        $response=Http::timeout(10)->post(rtrim(env('PASSPORT_AUTHORITY_URL','http://passport-authority:8791'),'/').'/certify-holder',[
            'holder_name'=>$request->input('holder_name'), 'mail_address'=>$request->input('mail_address'), 'holder_public_key'=>$request->input('holder_public_key'),
        ]); $response->throw(); $credential=$response->json('credential'); $token=(string)$request->input('enrollment_token');
        return MailWeb::page('Credential certified')->template($office)->slot('content',MailWeb::page('Certified')->heading('PUBLIC KEY CERTIFIED',variant:'display')->paragraph('The Authority certified the holder public key. Technical Passport Service must now install the credential beside its protected private key.')->clientAction('Install certified passport','identity.enroll.complete','/issued',['enrollment_token'=>$token,'credential'=>json_encode($credential,JSON_UNESCAPED_SLASHES)],['passport_number','holder_name','mail_address','issuing_authority','expires_at']));
    } catch(Throwable) { return MailWeb::page('Authority unavailable',503)->template($office)->slot('content',MailWeb::page('Unavailable')->heading('ISSUING AUTHORITY UNAVAILABLE')->paragraph('Your local holder key was not disclosed. Submit a new application when the Authority is online.')->link('Return to applications','/')); }
});

MailWeb::post('/issued', function (MailWebRequest $request) use ($office) {
    $c = $request->input('credential');
    if (!is_array($c)) return MailWeb::page('Issuance failed', 400)->heading('NO CREDENTIAL');
    return MailWeb::page('Passport issued')->template($office)->slot('content', MailWeb::page('Issued')->heading('TECHNICAL PASSPORT ISSUED', variant: 'display')->heading('Passport No.', 2)->paragraph($c['passport_number'])->heading('Holder', 2)->paragraph($c['holder_name'])->heading('Correspondence', 2)->paragraph($c['mail_address'])->heading('Issuer', 2)->paragraph('Butterfly Passport Authority')->heading('Authority Seal', 2)->paragraph('PRESENT')->heading('Expiry', 2)->paragraph($c['expires_at'])->paragraph('The protected WALLET-1 has been installed in Technical Passport Service HOST STORAGE.')->button('Return to Find Me', 'mailweb://find-me.local/', 'prominent'));
});
