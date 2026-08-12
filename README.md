# Butterfly

> Butterfly is a working computer ecosystem assembled from technologies whose histories never happened.

Each subsystem began as an independent counterfactual experiment. Generation 0.1 makes them survive contact without erasing their boundaries: MailWeb carries application correspondence, Global Positioning Servers estimates location from network observations, and HarmonicDB stores the resulting measurements as Waves over a Domain. Find Me is an independent application that consumes those public boundaries; it is not part of any system repository.

## Boot the machine

Docker is the only host prerequisite.

```sh
docker compose up --build
```

Open <http://localhost:9847>. Postbox begins at `mailweb://find-me.local/`. Press **Acquire network fix**; the request and its eventual result travel as real MailWeb correspondence through SMTP. Acquisition may take up to two minutes because GPServers performs live network measurements.

Mailpit's host inspection UI is at <http://localhost:8025>. The one-shot Stack Inspector prints its report into `docker compose logs inspector`.

To exercise the vertical journey without the graphical renderer, use Postbox from the Compose network:

```sh
docker compose run --rm mailweb --transport smtp --timeout 180s mailweb://find-me.local/locate
```

## Butterfly 0.1

```text
✓ MailWeb
✓ Global Positioning Servers
✓ HarmonicDB

○ Graphics
○ Storage
○ Time
○ Memory
○ Computation
○ Software
○ Architecture
```

Unchecked systems are honest host dependencies, not failed services. Their machine-readable register begins at `machine/manifest.yml`.

## Repository assembly

The three source projects remain Git submodules:

```text
systems/mailweb     → What-if-MailWeb
systems/gpservers   → What-if-GPS-ervers
systems/harmonicdb  → What-if-HarmonicDB
```

Clone recursively, or run `git submodule update --init --recursive`. Butterfly never imports implementation code across these system boundaries. Component changes stay visible as changes within their respective submodule histories.

Read `FIRST-CONTACT.md` for the boundary assessment and `PRESSURE.md` for what broke when the alternate histories met.

## What broke, and what comes next?

MailWeb survived intact. First contact forced GPServers to separate positioning service semantics from its console and forced HarmonicDB to acquire native Domain growth. Both pressures are now resolved public contracts: transport adapters wrap `service.Positioner`, while Find Me uses a Laravel `LocationRun` model to append actual `BF:…` coordinates to its own HDB store.

The machine's next demand is now lower in the substrate: HWS/7 must learn incremental Domain allocation and a growable harmonic catalogue. Today, correct growth atomically rewrites the store, and semantic indexes can either be rebuilt or explicitly left unmaterialized. Storage remains the strongest host dependency and the clearest next Butterfly pressure.
