# Integration Pressure

These are evolutionary pressures exposed when the three systems met, not automatically defects.

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
