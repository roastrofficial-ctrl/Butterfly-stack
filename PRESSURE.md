# Integration Pressure

These are evolutionary pressures exposed when the three systems met, not automatically defects.

## v1.0 — PORTER Generation VI: Application Disposition

Generation VI attempted a minimal generic `DS-…` fact and falsified it. Candidate
states `PROCESSED`, `COMPLETED`, `FAILED`, and `IGNORED` each admitted several
incompatible definitions: code starting, parsing ending, effects occurring,
transactions committing, recovery records surviving, or Returns being lodged.
PORTER could not choose among them without learning HDBE or application semantics.

Two contradictory disposition assertions over the same `CL` were equally valid
as data and equally unverifiable as communications facts. A `DS` would therefore
have copied a Host assertion while presenting it as PORTER knowledge. The
experimental feature was removed; PORTER/1 gained no primitive.

The crash matrix used real networkless HarmonicDB and interrupted after reading
the Package, after the HDBE effect, after HarmonicDB's result record, after a
stable Return draft, and after the Return crossed `LG`. The effect-before-record
case was decisive: correspondence history was complete and recoverable, while
HarmonicDB could not honestly determine from PORTER whether retrying the effect
was safe. That ambiguity belongs to the application transaction and recovery
model.

Application-owned records made later stages recoverable. A retained result could
avoid recomputation; a stable Return draft preserved Package identity; an
existing Return Lodgement could be resolved after restart. None changed the
parent Package's PORTER state. They were HarmonicDB recovery decisions followed
by ordinary correspondence.

A Return proves that another Package was lodged and declares an `in_reply_to`
relationship. Success and failure Returns have identical PORTER force. Their
meaning is payload protocol. Absence of a Return proves neither failure nor lack
of processing.

PORTER now ends explicitly at `CL`, where exact correspondence is recoverable in
Host custody. The application exclusively owns semantic validation, computation,
effects, transaction commit, application retry, recovery, completion and whether
to lodge further correspondence. PORTER deliberately refused job status,
workflow state, processing acknowledgement, exactly-once effects and Application
Disposition.

The unexpectedly elegant result was subtraction: `LG → AC → CL` remained enough.
An application can connect two complete correspondence histories with
`in_reply_to` without PORTER interpreting the connection. The difficult result
was accepting that a durable database effect can be real while communications
truth remains unchanged and silent.

The historical lesson is:

> **Correspondence ends where meaning begins.**

No immediate PORTER Generation VII is required. PORTER has discovered the end of
its primitive lifecycle. Butterfly's research horizon retains **No Web Servers**
and **Continuous Correspondence**, but neither has earned this generation and
neither is implemented.

## v0.9 — PORTER Generation V: Responsibility After Acceptance

Custody survived, but only as a statement about recoverability and
responsibility—not possession of bytes or successful computation. Before
Collection, immutable `AC-…` plus the absence of a later Collection fact means
the recipient Porter remains responsible and can reconstruct its inbox. When the
Host voluntarily publishes `CL-…`, the exact Package becomes recoverable in Host
custody and the Porter's current responsibility ends.

Collection required a threshold because the old rename-plus-Ticket-update path
could lose its explanation between writes. `CL` is created by the Host-side
client at its local Porter boundary and retained as PORTER's immutable evidence
of the crossing. The collected Package and Package-to-Collection association are
projections. `AC` is never rewritten. Repeating Collection yields the same `CL`;
it repairs evidence and projections rather than transferring custody again.

The bastard crash occurred immediately after canonical `CL`, before HarmonicDB
could retain application evidence or begin HDBE processing. At that instant the
inbox projection could still coexist with the Collection fact, but canonical
responsibility was not duplicated: `CL` was already decisive. Restart rebuilt
the collected projection, removed the stale inbox projection, and returned the
same Package identity to the Host. No state existed in which neither participant
could recover it.

The immutable history is now `LG` (origin Porter responsibility), `AC`
(recipient Porter acceptance), then `CL` (recoverable Host custody). Current
custody is reconstructed from those ordered facts. A Porter saying it currently
“holds” correspondence means it has canonical acceptance, no later Collection,
and can reproduce the inbox view. It does not mean the sender knows that state.

Collection knowledge remained local. The origin Porter's Receipt stayed a true
historical acceptance statement before and after recipient Collection; the
Sender Host's later Round learned nothing new about collection. Exporting read
receipts did not emerge as necessary.

PORTER responsibility ends precisely when `CL` makes the exact Package
recoverable to the Host. Application responsibility begins after that threshold.
The separate HarmonicDB application record may say an HDBE attempt and Return
lodgement were recorded, but PORTER cannot infer either from `CL`. A crash during
an application effect but before its own commit remains fundamentally ambiguous.

The unexpectedly elegant result was a third immutable fact rather than a custody
state machine: historical facts plus recoverable projections were sufficient.
The unexpectedly difficult distinction was that “Host custody” still does not
mean a particular Host process remembers anything; it means a future Host
execution can recover the Package.

The historical lesson is:

> **Collection transfers custody, not meaning.**

The single pressure proposed for Generation VI is **Application Disposition**:
whether a Host needs to record what it chose to do with collected correspondence,
or whether that truth must remain wholly application-owned. It is not implemented,
and it must not become inferred processing or exactly-once execution.

## v0.8 — PORTER Generation IV: Carriage Knowledge

The ugly case was reproduced deliberately. HarmonicDB's Porter durably accepted
one real HDBE Package, but the acceptance evidence was prevented from becoming
durable at Find Me's Porter. After restart, remote acceptance was true while the
originating side could honestly claim only `ACCEPTANCE_UNKNOWN`. Find Me learned
nothing until a later Round inspected its Collection Ticket.

The recipient's canonical `AC-…` acceptance contains the Package, digest,
recipient and acceptance time. It is the remote threshold; the inbox is its
recoverable projection. The sender's carriage journal records attempts, but an
attempt is not acceptance knowledge. Only durable retention of the recipient's
Receipt changes local knowledge to `REMOTE_ACCEPTANCE_KNOWN`.

The Receipt now attests only that the named Porter durably accepted
responsibility for the exact Package identity and digest at a stated time. It
does not mean bytes merely arrived, the Package is still held, the recipient
Host collected it, application processing succeeded, or a Return exists. HTTP
202 has no PORTER meaning until its evidence survives locally.

When evidence disappeared, the sender retained uncertainty and the original
Package for another attempt; it did not invent `REFUSED`. Repeated carriage kept
the same `PKG-…`. The recipient recognised its existing acceptance and returned
the same `AC-…`; it neither created another correspondence fact nor claimed the
application executed only once. A reused Package identity with different bytes
was rejected. Identity proved more important than byte equality because it names
the single lodged correspondence whose evidence is being repeated.

The failure matrix covered absence before carriage, interruption after attempt,
remote acceptance before evidence, loss before local retention, interruption
after retention, delayed and repeated attempts, recipient recovery of a missing
inbox projection, independent Porter restarts, invalid transport success, and
the final isolated-Host collection/Return path. Conventional write-ahead facts,
stable identity, retry and acknowledgement ideas were useful. “Delivery
guarantee”, exactly-once, and transport success were misleading because each
collapsed distinct knowledge boundaries.

The unexpectedly elegant result was that repetition repaired knowledge without
requiring a second correspondence fact. The unexpectedly difficult part was
naming a Receipt narrowly enough: even perfect acceptance evidence becomes a
historical statement as soon as recipient custody changes.

The facts at the boundaries are now explicit:

- the originating Host knows it lodged correspondence and can later observe its
  Porter's carriage knowledge through a Round;
- the originating Porter knows its attempts and only retained acceptance evidence;
- the receiving Porter knows its canonical acceptance and local custody projection;
- the receiving Host knows nothing until it explicitly collects.

The historical lesson is:

> **Fact can outrun knowledge.**

The single pressure that has earned Generation V is **Responsibility After
Acceptance**: what happens to custody knowledge when a recipient Host collects,
without equating collection with processing or creating a Host notification
path. It is proposed here and not implemented.

## v0.7 — PORTER Generation III and ROUNDS

### Lodgement Integrity

"Lodged" ultimately had to mean one thing: the canonical Lodgement fact crossed
the local Porter boundary by atomic publication. The Host creates all identities
while drafting, but they have no public force before that threshold. Once the
`LG-…` fact exists, the Porter owns durable truth and recovery; Ticket JSON,
Package association and outgoing Package are projections rather than competing
accounts of reality.

The crash matrix interrupted immediately after LODGED, after Ticket materialisation,
after association, and after outgoing deposit. A fresh process recovered every
case as `DEFINITELY_LODGED`; absence of the canonical fact remained
`NEVER_LODGED`. Local ambiguity disappeared rather than acquiring a euphemistic
state. Atomic rename and a write-ahead-style canonical record were useful
familiar ideas. Rollback, two-phase commit and application transactions were not:
there is no promise to undo correspondence after it crosses the threshold.

The remaining fundamental ambiguity moved outward. If a recipient Porter accepts
a Package and the sender dies before retaining its Receipt, the local Lodgement
is certain but carriage knowledge is not. That has earned the candidate
Generation IV: **Carriage Knowledge**. It must not be confused with exactly-once
HarmonicDB execution.

The historical lesson is:

> **Lodgement has a threshold.**

### ROUNDS

ROUNDS remains outside the PORTER/1 wire protocol. Find Me's active-journey correspondence asks
Postbox for a bounded revisit cadence. Each revisit creates a distinct MailWeb
request and therefore a new Find Me execution; that execution chooses to inspect
its local Porter through the standard client Round vocabulary, records the observed state, and ends. Only a Round that observes
`RETURN_HELD` explicitly collects and advances the application ledger. No Porter
initiates IPC toward either Host.

The experience became usable without pretending to be synchronous, but attention
was not free. The nominal 650 ms policy is bounded by MailWeb/SMTP execution time,
so a Round can cost seconds. This made application-specific cadence feel more
important, not less. Dormant work makes no ROUNDS; active human work is attentive.
The ledger records Return-held, observed, collected and continued times, making
observation latency a first-class measurement without making "attention" a
PORTER concept.

A crash after observation is harmless to correspondence: the Return remains
held. A crash after collection but before continuation is an application recovery
problem; PORTER retains which Package was collected but cannot declare the
journey complete. Tests found no accidental push path.

ROUNDS has earned standard PORTER client vocabulary: an `RD-…` identity, an
explicit boundary visit over one or many Tickets, and a durable
`PORTER-ROUNDS/1` observation journal. It has not earned a PORTER wire verb.
Cadence, collection, and continuation remain Host policy, allowing different
Hosts to express genuinely different attention needs through the same ceremony.

## v0.6 — PORTER Generation II

Generation I removed direct addressing but left Find Me synchronously staring at
its mail slot. Generation II replaces that hidden wait with a durable Collection
Ticket. The deposit execution ends immediately; inspection and collection occur
only in later, explicit Find Me executions. Find Me keeps a separate continuation
ledger because deciding what an HDBE Return means is application work, not a
communications concern. HarmonicDB's Laravel package has consequently lost all
PORTER configuration and code.

The real machine demonstrated the uncomfortable case, not merely the happy path:
a Find Me execution lodged an HDBE `info` Package in about 1.2 ms and exited;
HarmonicDB later processed it while the requesting Host was absent; the Find Me
Porter was stopped and restarted; then a fresh Host execution inspected
`RETURN_HELD` and collected the surviving HDBE/1 Return. HarmonicDB remained
`network_mode: none` throughout.

Tests also forced the lifecycle to answer questions that Generation I postponed:

- duplicate Returns remain evidence and do not imply duplicate computation;
- one competing collector wins deterministically, while others observe a
  contest or the already-recorded collection;
- expiry is an observed condition, not retroactive cancellation;
- abandonment ends application intention but does not destroy a late Return;
- Ticket state and event history survive process restart.

The unexpected pressure was filesystem identity. Porter and Host containers can
share a volume without sharing a user, so lock-file permissions are part of the
Host IPC ABI. This is now explicit rather than an accidental Docker property.

What became unexpectedly elegant was inspection: because arrival is silent,
expiry, a held Return, and abandonment all become facts a later Host execution
observes through the same boundary. What remained unexpectedly difficult was the
old call-shaped application journey. Laravel could end cleanly after lodgement,
but Find Me needed an application-owned ledger and explicit continuation pages;
PORTER could not honestly manufacture either of those responsibilities.

The implemented Ticket became durable local evidence connecting one deposited
Package to any Returns that cite it. It is not a transferable promise, a remote
execution handle, or proof of recipient collection. Multiple Returns can attach
to it, inspection does not consume them, and collection records which one won.
Exactly-once processing, retry policy, application completion, and cancellation
of work already in carriage therefore do not belong in PORTER. The fundamental
failures now visible are ambiguity after a partial lodgement, duplicate
computation outside the carriage boundary, and an application crash after
collection but before it records its own continuation.

### Most interesting Generation III

Investigate **Lodgement Integrity**, but do not implement it in this generation.
Ticket creation, Package association and outgoing deposit are currently separate
durable writes. A crash between them can orphan one fact. The next experiment is
a recoverable local Porter lodgement ceremony that preserves Host silence and
does not make PORTER responsible for application transactions.

## v0.5 — PORTER Generation I

### Before implementation

Every Butterfly Host occupied the same Docker IP network and most exposed an
HTTP listener. Removing published host ports did not isolate computational Hosts;
any peer container could still address `harmonicdb:8787`, `gpservers:8090`, or a
Passport service directly. The architecture assumed that locating and connecting
to a process was the natural prerequisite to useful computation.

The smallest independent proof therefore used two new Hosts with
`network_mode: none`, each sharing a private filesystem mail slot only with its
Porter. Both Porters alone joined a communications network. The test started no
recipient Host, deposited a Package, observed it held at the destination Porter,
and proved no recipient process or execution marker appeared. Only after the
recipient Host was explicitly started did it COLLECT, process, deposit a Return,
and allow the sender Host to COLLECT that Return.

### PORTER/1 boundary

PORTER/1 defines Package, Deposit, Collection, Return, Receipt and Refusal. Its
envelope exposes sender/recipient identities, Kind, creation/expiry, reply
relationships and an opaque application payload. It deliberately does not yet
define Introduction, authority claims, retries, duplicate suppression,
withdrawal, discovery or delivery guarantees.

Host–Porter IPC uses atomic filesystem rename. Porter–Porter carriage currently
uses HTTP/JSON over a dedicated Docker network. Both are **HOST INTEGRATION
TRANSPORT** scaffolding. Crucially, the HTTP listener belongs to communications
apparatus, never the computational Host.

### First real collision: HarmonicDB

HarmonicDB was chosen because useful database computation does not inherently
require IP connectivity. It now runs with `network_mode: none`, no listener, no
IP address and no gateway. Its host loop polls its local Porter slot, explicitly
collects `hdbe.call` Packages, evaluates the unchanged HDBE/1 application payload,
and deposits `porter.return` Packages. PORTER knows no Waves, Domains, queries or
database errors.

Find Me's Laravel HarmonicDB adapter deposits to its own local slot and scans for
the correlated Return. A live test deliberately configured an unreachable HTTP
URL and still collected HDBE/1 `info` successfully through PORTER. Observed timing
was approximately 42 ms waiting for collection versus 0.027 ms of Host work.

This is not yet an asynchronous application architecture. The correspondence is
asynchronous and store-and-collect, but Laravel waits inside a synchronous model
call. That discomfort is the result: remote-function-call control flow survived
after its transport primitive disappeared.

Find Me itself remains IP-networked because its MailWeb, GPServers, Passport and
Lucida boundaries have not migrated. Claiming whole-machine Host Isolation would
therefore be false. Generation I proves two fully isolated fixture Hosts and one
fully isolated production service Host, not all of Butterfly.

### Most interesting Generation II

Add durable local **Collection Tickets** rather than migrating another service.
A Host should deposit, continue useful work, and later collect available Returns.
That experiment will force crash recovery, duplicate Returns, idempotent
collection, expiry and abandoned work to become native concepts. Introductions
and Technical Passport claims should wait until the lifecycle they would protect
has earned a stable shape.

## v0.4 — Lucida First Contact

### Initial assessment

- Find Me's hero was an ordinary JPEG MailWeb enclosure. Its positioning result had no map at all, so ordinary Postbox enclosure handling would have delivered and cached source bytes.
- MailWeb 0.6 could declare a one-shot `client_action`, but that shape assumes a provider popup followed by an application POST. A long-lived observation with local interaction did not fit honestly.
- Postbox already routed capability names to provider declarations, but its registry contained only Technical Passport and its renderer offered only ordinary `<img>` output.
- Lucida Stage III already had the important boundary: SIGHT/1 request/release JSON, independent `SightSource` and `Observer`, private prepared Tracings, selective projection, accumulated observer knowledge, and offline replay. Its localhost laboratory server was the right small seam to harden into a service; no new Lucida protocol was needed.

### Boundary chosen

MailWeb gained one generic declarative node, `capability_surface`. It carries a
capability name and bounded opaque string parameters. Postbox resolves a provider
declaration, loads that provider's declared client module, and supplies a DOM
surface plus a generic evidence callback. It does not interpret SIGHT/1,
LUCIDA/2, Forms, Passes, regions, private Sights, or Draughtsman operations.

Lucida exposes `service-info`, health, ephemeral preparation, observer state,
open-observation and request/refine operations over **HOST INTEGRATION
TRANSPORT**. Find Me owns the private source and its public Sight name; it submits
an unnamed HOST SIGHT INPUT to the generic preparation operation and places only
the returned opaque handle in correspondence. Lucida owns no application asset
catalogue. Its own browser module is the receiving Draughtsman. HTTP, base64 host
input envelopes, dynamic browser modules and CORS are scaffolding from our
timeline, not Lucida networking.

### Outcome and pressure

- SIGHT/1 survived application use unchanged. The source raster stays in Find Me's private application resources and crosses only the server-side Lucida tool boundary as HOST SIGHT INPUT; MailWeb correspondence contains a Sight ID, opaque prepared handle and observation intent. No image fallback exists.
- Prepared observation remained appropriate, but ownership moved upward. Find Me requests an ephemeral private Tracing when composing the relevant page; Lucida deduplicates it by content/policy without learning application names. Releases contain expanded observer-safe operations with zero original pixels and no private Form catalogue.
- Initial hero and map observations are selective. Refinement is explicit. Canvas scaling emits journey evidence but performs no fetch. Released construction stays in the Lucida-owned browser module and remains drawable after the source stops; reload/restart state is deliberately ephemeral.
- Observer sessions are isolated in the service. There is no cross-observer cache. Once a browser reload forgets knowledge, that is software state loss—not undisclosure.
- Find Me owns the position and uncertainty values. Today its declared overlay is drawn by the Lucida client package on the local surface, but those values are never sent to the Sight source. A future generic host-overlay contract may separate even this rendering custody if another application earns it.
- The Journey Inspector records capability delegation, SIGHT/1 release facts/bytes/pass/region, Draughtsman rendition, and explicitly network-free local zoom. These browser-observed events are session-only and are not server-authenticated evidence.
- The Local Capability Service pattern now works across a secret-bearing one-shot Passport ceremony and a stateful, repeated visual surface. It is **PROVEN ACROSS TWO CAPABILITY CLASSES**, while discovery remains a static host registry.
- Visual representation and transfer are now counterfactual. Physical canvas rasterisation, browser layout, colour output and the display window remain **HOST DISPLAY SURFACE** dependencies. Graphics is partially replaced, not checked off without qualification.
- Performance and bandwidth are exposed per release in the surface and journey. No superiority claim is made; tracing at service startup and JSON geometry are visibly more expensive than a cached JPEG in some cases.
- MailWeb page caching does not contain observer releases, but a cached document can create a fresh observation surface. Privacy-bearing capability freshness and non-prefetch metadata remain open pressure.

### What broke when an application stopped receiving images and started asking permission to look?

The one-shot client-action assumption broke. Rendering could no longer mean
resolving a URL: it needed an independently owned, stateful provider surface whose
knowledge grows only after explicit requests. The collision also split “graphics”
in two. Lucida could replace remote visual representation and transfer, but not
the host canvas that turns released construction into emitted light.

## v0.3.1 — Departmental Separation (before implementation)

Postbox currently contains `passport.go`, embeds the Passport Node holder agent in its image, persists `/postbox-data/holder.passport.json`, validates `WALLET-1`, exposes passport inspect/import/remove endpoints, knows public credential fields, collects PINs, invokes Authority issuance, invokes AUTH-1 proof generation, and renders Passport-specific enrollment/presentation language. Compose gives it the wallet volume, Authority URL and Passport implementation files.

| Current knowledge | Classification |
| --- | --- |
| Render a declared `client_action`, authorize it against the current document, and return its safe result | **SHOULD BELONG TO POSTBOX** / generic client-capability concern |
| Map `identity.*` capabilities to one configured local provider over host IPC | **GENERIC CLIENT-CAPABILITY CONCERN** |
| `WALLET-1` validation and storage path | **SHOULD BELONG TO PASSPORT SERVICE** |
| Passport number, credential shape, Authority seal, wallet inspection/removal/import | **SHOULD BELONG TO PASSPORT SERVICE** |
| PIN collection, confirmation, derivation and unlock | **SHOULD BELONG TO PASSPORT SERVICE** |
| Holder key generation, private-key protection and enrollment state | **SHOULD BELONG TO PASSPORT SERVICE** |
| AUTH-1 challenge signing and proof assembly | **SHOULD BELONG TO PASSPORT SERVICE** |
| Consent/disclosure copy for identity enrollment/presentation | **SHOULD BELONG TO PASSPORT SERVICE**; Postbox may temporarily host the returned UI surface |
| Authority issuance and revocation concepts | Authority certification belongs to **PASSPORT AUTHORITY**; holder-facing interpretation belongs to **PASSPORT SERVICE** |

The correction target is therefore not merely another container. Postbox must lose wallet custody and Passport implementation imports. Its remaining seam should be: receive a generic capability request, ask a configured local provider for a trusted interaction description, relay opaque user input directly to that provider, and return only the provider's safe result to MailWeb.

## Passport First Contact observations (before implementation)

### Passport: the domain boundary survived its CLI

- Finding: the Passport Authority already exports transport-independent `prove`, `verifyCredential`, and `ChallengeStore` APIs. The public credential is embedded in `WALLET-1`, while `protected_holder_key` remains private and is unlocked with scrypt/AES-256-GCM only for proof generation.
- Pressure: Postbox is Go and Find Me is PHP, while the authoritative implementation is Node.js. Integration needs thin host adapters; copying Ed25519, canonicalization, wallet protection, or status logic into either consumer would create competing protocols.
- Authority state: offline verification needs only a host-configured signed Authority Document, pinned public key, and the latest signed revocation document. The issuing Authority is not an authentication dependency.

### MailWeb: native client behaviour was not expressible

- Finding: MailWeb/0.5 is deliberately closed and declarative. Forms submit application-visible values; buttons only navigate. Neither can safely request local holder action.
- Pressure: authentication requires the smallest generic primitive that says “invoke an explicitly named client capability, with public parameters, then POST its public result to this MailWeb action.” This is a generic client action, not a Passport-specific transport and not an extension framework.
- Security boundary: a client action is rendered as Postbox-owned security UI. Ordinary MailWeb documents cannot create password/PIN inputs or receive the locally entered secret.
- First collision: putting the authentication entry point in ordinary navigation allowed prEmail to request it speculatively, starting a short-lived challenge before holder intent. Find Me therefore exposes passport entry as a button, which the existing pre-mail policy deliberately does not follow. Future capability metadata may need explicit freshness/cache semantics.

### Postbox: custody makes it a holder environment

- Finding: Postbox currently owns only session navigation, stationery, enclosures and journey evidence. It has no extension mechanism or persistent user state.
- Pressure: a passport wallet introduces explicitly labelled **HOST STORAGE** and local security UI. This is evidence that Postbox may be becoming a personal computing shell, but First Contact is too early to declare that architecture.
- Constraint: decrypted holder material and the PIN must remain within the Postbox process boundary and must be excluded from correspondence, retained state, journeys and logs.

### Find Me: authentication requires short-lived state

- Finding: Find Me is otherwise stateless between MailWeb requests; its only durable application data is a `LocationRun` in HarmonicDB.
- Pressure: one-time challenges require ephemeral pending/consumed state. Passport profiles, login history and authenticated positioning history are explicitly out of scope.
- Service identity: `find-me.local` is selected because it is already the authority component of the real `mailweb://find-me.local/...` application identity. This is a local, minimal choice—not a Butterfly-wide naming system.

### Butterfly trust and host dependencies

- Trust bootstrap: Find Me's verifier receives a pinned Authority public key and signed Authority Document as **HOST TRUST CONFIGURATION**.
- Revocation: verification uses the latest independently exported signed revocation document and reports its freshness; authentication never contacts the Authority.
- **HOST TIME** determines credential validity, challenge lifetime and revocation freshness.
- **HOST STORAGE** holds Postbox's protected wallet and the verifier's trust/revocation material.

## Passport First Contact outcome

- The existing Passport library boundary worked outside its CLI. Only thin stdin/HTTP host adapters were required; no cryptography was duplicated.
- AUTH-1 challenge semantics survived asynchronous MailWeb correspondence, provided authentication entry is not speculatively pre-mailed.
- `WALLET-1` maps coherently to Postbox custody: inspectable public credential plus protected holder material in local **HOST STORAGE**.
- Verification required no live Authority state. It required pinned public trust material, a signed revocation snapshot, ephemeral one-time challenge state and **HOST TIME**.
- MailWeb needed one generic `client_action` document primitive. The proof still returns as an ordinary correlated MailWeb POST; no Passport REST bypass was introduced.
- Postbox is no longer merely a renderer. It now holds user state and owns trusted consent/unlock UI distinct from MailWeb content. This supports—but does not yet settle—the “personal computing shell” hypothesis.
- Find Me learns a verified identity for one response only. It creates no account, profile, login history, position history, Visa or Entry Stamp.
- The machine now demands explicit capability freshness/cache semantics, durable-but-recoverable holder storage design, trust/revocation distribution, and eventually non-host time. Those are v0.3+ pressures, not additions to v0.2.

## Passport Office observations (before implementation)

### Issuance: the Stage I ceremony gave the Authority the PIN

- Finding: `PassportAuthority.issuePassport()` creates the holder key and immediately protects it with a caller-supplied PIN. Its CLI therefore receives the holder secret during issuance.
- Pressure: an Office form cannot safely preserve Postbox's trusted-input boundary if it invokes that API unchanged.
- Required split: Postbox must generate and retain holder private material locally, send only the holder public key and public application details through MailWeb, receive an Authority-sealed credential, and finish `WALLET-1` protection locally. Authority signing remains independent from the user-facing Office.

### MailWeb: issuance is a stateful native ceremony

- Finding: v0.2 `client_action` assumes a native result can be produced immediately and POSTed once.
- Pressure: enrollment needs a public-key application round trip followed by local installation. The smallest extension is another named client capability whose result remains an ordinary MailWeb POST; Postbox retains only a short-lived local enrollment key until the credential returns.

### Postbox: multi-site state is not tab state

- Finding: one `BrowserSession` owns one history cursor, although its archive can retain correspondence from many sites.
- Pressure: real Find Me ↔ Passport Office movement now justifies multiple independent history cursors sharing one holder wallet, transport and correspondence archives. Treating archive entries as cosmetic tabs would not preserve application state honestly.

### HarmonicDB: passport identity is symbolic while Waves are numeric

- Finding: `LocationRun` values are numeric Waves and journey IDs are Domain coordinates. Passport numbers are symbolic identifiers.
- Pressure: private history needs an exact identity association and filtered multi-coordinate observation. Encoding a passport number as an unverified floating-point hash would create collision and privacy ambiguity. HDB needs a native symbolic/indexable association or an equally explicit relationship construct before the Laravel model can offer an honest `forPassport()` query.

## Passport Office & Immigration outcome

### Passport Office and Authority

- The Authority's issuance logic survived behind a small HTTP host adapter. Passport Office is a separate Laravel/MailWeb application and never imports Authority implementation or private state.
- The public application—holder name and correspondence identity—travels through MailWeb. PIN entry occurs only in trusted Postbox chrome. Because the current `issuePassport()` contract still creates and encrypts the holder key, Postbox sends the PIN directly to the Authority adapter over the host integration network; it never enters Passport Office content, MailWeb correspondence, journey evidence or application logs.
- This is the smallest safe adaptation, not the desired final ceremony. The machine still demands split issuance: holder key generation and wallet protection in Postbox, public-key credential signing in the Authority.
- The protected `WALLET-1` response is consumed and installed directly by Postbox in **HOST STORAGE**. Only its public credential is posted back to the Office. A generic `technical-passport.apply` client action was enough; MailWeb did not gain a wallet-shaped enclosure.
- Issuance is online-only. Authentication uses the verifier's mounted, signed Authority Document, pinned public key and revocation snapshot and remains independent of the live Authority process.

### Postbox and multi-site MailWeb

- SMTP routing is now selected by the `mailweb://` host, so `find-me.local` and `passport.local` remain distinct correspondents and applications while sharing one Postbox transport and holder environment.
- Lightweight site tabs preserve each site's last MailWeb URI/document through the correspondence archive and allow switching and closing. They do not yet provide truly independent `BrowserSession` history cursors. The earlier pressure was correct: honest full tab state requires a deeper application-shell session model.
- Local passport custody is now fundamental to the demo. Trusted apply/present actions collect secrets outside semantic content; ordinary documents can request the capability but cannot receive or render the PIN.
- The journey inspector retains the public issuance credential and later public proof because those are the actual MailWeb POST bodies. It never retains the protected wallet or PIN. A future evidence policy may want selective redaction of public-but-identifying credentials.

### Find Me immigration

- Anonymous acquisition remains unchanged. Private routes require a short-lived entry token created only from a verified AUTH-1 result; a passport number from ordinary content or form input cannot select history.
- Find Me creates no user record, password or login history. Its only durable private application state is the authenticated positioning observation in its own `find_me_private` HarmonicDB store.
- Entry tokens currently live in the long-running MailWeb worker's memory. Restarting that application process clears immigration without affecting the portable passport or HDB history. Durable sessions would be a new host/application policy, not an identity requirement.

### HarmonicDB private history

- Dynamic Domain append was sufficient to file arbitrary authenticated run coordinates. The Laravel model needed a generic `all()` path implemented as a real HDB sweep followed by observations; `describe` exposes coordinate counts, not coordinate identities.
- Resolved by HarmonicDB Stage IX: private ownership is now one exact `owner`
  Symbol Wave. HDBE `match_symbol` uses an embedded candidate index followed by
  mandatory byte-exact verification; hashes and reconstructed amplitudes are
  never authoritative equality. Find Me no longer sweeps or projects identity
  into the former `owner_a`/`owner_b` approximate numeric pair.

### New and clarified host dependencies

- **HOST TRUST** is a named read-only volume populated by the Authority and consumed by the verifier.
- **HOST CRYPTOGRAPHY** remains Node's Ed25519, scrypt and AES-GCM implementation behind the Passport library; no consumer reimplemented it.
- **HOST STORAGE** holds Authority private state, exported trust, Postbox's encrypted wallet and both application-owned HWS stores.
- **HOST TIME** governs issuance, expiry, challenge lifetime, entry-token lifetime and revocation freshness. Butterfly still does not solve time.

### What Butterfly now demands next

Butterfly now demands two precise substrate improvements: a split Passport enrollment ceremony that never reveals the holder secret outside Postbox, and exact indexed symbolic association in HarmonicDB. Postbox's honest independent tab/session model is the emerging shell pressure behind them—not permission to build an operating system yet.

## v0.3.1 — Departmental Separation (after implementation)

- Postbox is again a correspondence client. It has no Passport implementation,
  Authority address, wallet mount, credential parser, key operation or Passport
  endpoint. It authorizes an exact capability/action pair from the current
  document, asks the configured local provider for an interaction surface, and
  returns only that provider's safe result.
- Technical Passport Service is the holder environment. It alone generates and
  protects the private key, receives the PIN, stores `WALLET-1`, and creates
  `AUTH-1` proofs. The browser submits the secret directly to that service; it is
  absent from MailWeb bodies, Postbox APIs and journey evidence.
- Passport Office carries public application data and holder public material.
  Passport Authority certifies the public key and never receives a secret.
  Find Me requests generic `identity.present` and continues to verify through
  its independent, offline-capable verifier.
- Removing or stopping Passport Service removes identity capability but leaves
  anonymous MailWeb navigation intact. Stopping Authority prevents new issuance
  but does not prevent presentation and verification of an existing passport.
- The residual host coupling is explicit: Postbox currently knows the configured
  local capability-provider URL and hosts the provider-described form surface.
  A future provider discovery/IPC contract could remove that deployment detail;
  it does not give Postbox Passport custody or protocol knowledge.

The earlier v0.3 observations below are retained as the history that produced
this correction. References there to Postbox wallet custody describe the old
design, not the v0.3.1 runtime.

## v0.3.2 — The Independent Holder

The adversarial coupling audit found four classes of dependency:

| Dependency | Classification | v0.3.2 disposition |
| --- | --- | --- |
| authorize `client_action`, correlate action/result, report unavailable/cancelled | **GENERIC CAPABILITY BROKER RESPONSIBILITY** | remains in Postbox |
| `identity.*` ceremony, provider interaction wording, PIN, key, wallet, credential inspection/removal, proof | **PASSPORT SERVICE RESPONSIBILITY** | TPS/1 and provider-owned browser-to-service interaction |
| capability name plus opaque request/result in a document | **MAILWEB PROTOCOL RESPONSIBILITY** | unchanged and identity-ignorant |
| hard-coded provider health at Postbox startup, undeclared endpoint semantics | **ACCIDENTAL COUPLING** | startup dependency removed; provider declares ID/name/capabilities/contract |

**Is Passport Service genuinely independent?** Yes. TPS/1 exposes declaration,
status, enrollment, presentation, public inspection and removal over today's
localhost HTTP Host Integration Transport. The independent holder client imports
no Postbox code. A lifecycle test enrolls, kills/restarts only the service,
inspects the persisted identity, and produces a proof.

**Is Postbox genuinely Passport-agnostic?** Its application/API path knows only
capability, opaque parameters/result, action correlation and failure. The current
browser host configuration still contains one local endpoint and an optional
identity-management shortcut. The trusted interaction description and service
name come from the provider; Passport ceremony semantics do not enter Postbox
handlers. Another provider can use the same invocation seam by declaring a
capability, though the tiny static browser registry must be configured.

**Who owns trusted UI and secrets?** TPS owns the wording and operation. The
Postbox browser supplies a host surface, but submits the secret directly from
browser JavaScript to TPS; no Postbox Go handler, MailWeb body, journey record or
filesystem receives it. This is real process/data-flow isolation, not complete
visual-origin isolation: browser CSP/CORS and the host window remain shared host
machinery.

**Local Capability Service pattern: PROVEN (narrowly).** Two consumers—Postbox
and `examples/independent_holder_client`—consume the same declared contract, and
Postbox no longer gates startup on TPS health. What remains bound is explicit:
localhost HTTP, a configured endpoint registry, CORS origins, the browser window,
TPS host storage and host time. There is no discovery, negotiation, plugin system
or universal bus. Approximate HarmonicDB owner fingerprints remain deliberately
unchanged; Visas and Entry Stamps remain unimplemented.

## Resolved in 0.1: GPServers receiver assumed UI ownership

- Assumption: a positioning run is started asynchronously by the bundled console and completed through an SSE broadcast.
- Consequence: an external application cannot correlate one call with one final structured result.
- Resolution: the public `service.Positioner` contract now owns request correlation, acquisition and structured results. HTTP is one replaceable host adapter. Application persistence belongs to the calling application, not GPServers.
- Remaining pressure: asynchronous progress and evidence retrieval may eventually become service concepts, but neither is required for correctness.

## Resolved in 0.1: HarmonicDB categorical coordinates were fixed at creation

- Assumption: early positioning fixtures knew their probe-session coordinates in advance.
- Consequence: arbitrary `BF:…` identities cannot become new Domain coordinates through HDBE/1.
- Resolution: `domain.append` is a public in-process and HDBE/1 operation. It atomically re-establishes the HWS with the new coordinate and supplied Wave values; no ring or shadow ledger remains.
- Remaining pressure: HWS/7 growth is whole-store rewriting and the embedded catalogue is bounded. A future HWS format needs incremental coordinate allocation and catalogue growth.

## HarmonicDB: semantic index maintenance magnified append cost

- Assumption: a re-established numeric Wave should receive a fresh persisted Semantic Spectrum.
- Consequence: an observation-ledger append exceeded GPServers' service deadline while rebuilding indexes the application never queries.
- Resolution: append exposes an explicit semantic-index policy. Rebuilding is the safe default; GPServers deliberately requests `NOT MATERIALISED` for its positioning ledger.
- Future pressure: grow or update Semantic Spectra incrementally when Domain length changes.

## Resolved in 0.1: database ownership followed the positioning service

- Assumption: because GPServers produced measurements, its Butterfly adapter should persist them.
- Consequence: a positioning service acquired application-specific database policy and a runtime dependency on HarmonicDB.
- Resolution: GPServers now returns evidence only. Find Me owns a `LocationRun` Laravel model and its dedicated `find_me` HDB store through the reusable `laravel-harmonicdb` package.
- Remaining pressure: package distribution should move from workspace path repositories to ordinary tagged Composer releases.

## HarmonicDB: registration is startup configuration

- Assumption: HDBE stores are path-backed and registered on service startup.
- Consequence: Butterfly must initialise the positioning HWS before the engine starts and keep a host volume mounted.
- Immediate change: the container creates the fixture only when the volume is empty and registers it as `positioning`.
- Future pressure: a storage-provider lifecycle beyond POSIX paths, while retaining HDBE semantics.

## MailWeb: publisher packaging is Laravel-shaped

- Assumption: the reusable application boundary is a Laravel package consumed by a conventional Laravel process.
- Consequence: a tiny Butterfly application still carries the host PHP/Laravel software stack.
- Immediate change: Find Me is a standalone Laravel application and image. It consumes only MailWeb's reusable publisher package; it neither extends nor copies the demo application.
- Future pressure: a language-neutral publisher boundary or alternate software substrate, if another application genuinely demands it.

## Cross-system identity is correlation, not causality

- Assumption: MailWeb and HarmonicDB generate their own identities; GPServers previously generated none for service calls.
- Consequence: no universal trace exists.
- Immediate change: derive a host-level `BF:…` correlation from the MailWeb request ID, generate `GPS:…`, and preserve the returned `HDB:…` execution ID in the MailWeb result.
- Future pressure: identity propagation and inspection semantics. Do not mistake this for causal time.

## Host dependency register

| Dependency | Current host mechanism |
| --- | --- |
| Graphics | Browser raster/window rendering |
| Storage | Docker volumes, POSIX paths and files |
| Time | Wall and monotonic host clocks |
| Memory | Per-process RAM |
| Computation | Conventional processes, CPU and host scheduling |
| Software | Source trees, container images, language runtimes and packages |
| Architecture | Binary host CPU |
| Integration transport | Docker networking, TCP, HTTP/JSON, SMTP and Mailpit |
