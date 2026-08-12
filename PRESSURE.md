# Integration Pressure

These are evolutionary pressures exposed when the three systems met, not automatically defects.

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
