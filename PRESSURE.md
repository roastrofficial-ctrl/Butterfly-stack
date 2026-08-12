# Integration Pressure

These are evolutionary pressures exposed when the three systems met, not automatically defects.

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
- Symbolic ownership remains awkward. The current model projects 64 bits of a SHA-256 passport fingerprint into two numeric Waves and compares using the measured HWS float recovery tolerance. This yields roughly 46 effective matching bits in the present codec, avoids storing the public passport number verbatim, and is adequate for the experiment—but it is not an exact identity index.
- Private history therefore performs a full owner-Wave sweep and per-coordinate observation. The next HDB contract should support exact symbolic values and indexed filtering/relationships instead of asking an application to turn identity into approximate measurements.

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
