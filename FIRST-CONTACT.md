# First Contact

Assessment date: 2026-08-12. The three systems are Git submodules under `systems/`; their histories and standalone workflows remain independent. The supplied SSH URLs and repository names, including `What-if-GPS-ervers`, are recorded unchanged. First assembly used GitHub's equivalent HTTPS transport because this host did not yet trust GitHub's SSH host key.

## MailWeb — READY

MailWeb is a Go Postbox client plus a reusable PHP/Laravel publisher package. Its Compose environment already separates Postbox, a Laravel demo publisher, its SMTP listener, and Mailpit. Postbox supports terminal and browser entry points. Application requests are closed-schema MailWeb/0.5 messages carrying GET/POST semantics; the reference SMTP carrier sends them as `application/mailweb+json`, and replies are correlated by request ULID.

Arbitrary applications can already be served: the demo is an ordinary consumer of `laravel-mailweb`, with application routes in `routes/mailweb.php`. Butterfly's Find Me is likewise a standalone Laravel application with its own bootstrap, dependency manifest and image. It consumes only the reusable `laravel-mailweb` package from the MailWeb repository; no demo application files enter its image. The user-facing operation genuinely travels Postbox → SMTP → publisher and returns the same way. HTTP is used only by Postbox to inspect Mailpit below that correspondence boundary.

Minimum change: none to MailWeb. Find Me depends on the public publisher package exactly as an independently housed consumer would.

## Global Positioning Servers — READY

GPServers is Go 1.23 with a multi-stage Docker image. Its `gps` binary has CLI experiments and a receiver HTTP service; the supplied Nginx console calls `POST /api/acquire` and follows an SSE event stream. A run discovers RIPE Atlas anchors, makes genuine TCP observations, and produces measurements, network quality, probability fields, fixes, confidence, and credible uncertainty. It does not use browser/device geolocation.

First contact revealed that the existing receiver owned the UI lifecycle: acquisition acknowledgement and results were separated by an event stream. GPServers now exposes a transport-independent Go service contract in `service`: `Positioner`, `Request`, and `Result`. The receiver's `POST /api/position` route is only a **HOST INTEGRATION TRANSPORT** adapter around that contract. Consoles or future transports can invoke the same service without owning its lifecycle. GPServers calculates positioning evidence and has no application-persistence dependency.

## HarmonicDB — READY

HarmonicDB is Python 3.10+ and exposes Stage VIII through HDBE/1. `python -m harmonic.hdbe` registers named stores and exposes info, stores, open/describe, capabilities, health, physical structure, HQL query/explain/TRACE, measure, observe, sweep, update, and coherent structured transactions. Responses preserve `HDB:…` execution evidence; remote diagnostics can consume structure and trace data. HTTP/JSON is explicitly a **HOST INTEGRATION TRANSPORT**.

HDBE/1 exposes native `domain.append`: one coordinate plus its Wave values is incorporated by atomically re-establishing and replacing the HWS from its logical model. The safe default rebuilds semantic indexes; observation-ledger consumers may explicitly leave them unmaterialized. HarmonicDB also supplies a reusable `laravel-harmonicdb` package whose model abstraction maps declared application Waves to append and traced Observation operations without exposing HWS internals.

Find Me owns store `find_me` and an application model named `LocationRun`. Its `journey` Domain uses the real `BF:…` identity; latitude, longitude, confidence, uncertainty, network timing, quality and signal count are Waves. After GPServers returns a calculation, Find Me creates and reads back this model before constructing its MailWeb response. No local application database or fallback store exists.

The fixture's original eight coordinates (`session_40`–`session_47`) remain sample history. New journey coordinates grow the same authoritative Domain and survive engine restarts. HWS/7 growth currently rewrites the store, so performance and catalogue capacity remain explicit format pressures rather than missing application semantics.

## Boundary map

```text
Postbox --MailWeb/0.5 over SMTP--> Find Me publisher
Find Me --host HTTP/JSON--> GPServers position boundary
Find Me --Laravel HarmonicDB model / HDBE/1--> HarmonicDB
```

Docker DNS names provide service identity. `localhost` is used only inside a container's own health check or for host-exposed user interfaces.
