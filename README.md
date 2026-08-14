# Butterfly

> Butterfly is a working computer ecosystem assembled from technologies whose histories never happened.

Each subsystem began as an independent counterfactual experiment. Generation 0.4 makes them survive contact without erasing their boundaries: MailWeb carries application correspondence, Global Positioning Servers estimates location, HarmonicDB stores measurements, Technical Passport supplies holder identity, and Lucida supplies selective visual observation. Find Me is an independent application that consumes those public boundaries.

## Boot the machine

Docker is the only host prerequisite.

```sh
docker compose up --build
```

Open <http://localhost:9847>. Postbox begins at `mailweb://find-me.local/`. Press **Acquire network fix**; the request and its eventual result travel as real MailWeb correspondence through SMTP. Acquisition may take up to two minutes because GPServers performs live network measurements.

## Lucida First Contact

Find Me no longer receives its hero or map as raster assets. Its MailWeb document
names `find-me.hero` and `find-me.map` Sights and asks the configured
`visual.observe` provider for selective SIGHT/1 releases. Lucida owns the source
raster, observer state, protocol interpretation and receiving Draughtsman;
Postbox supplies only a generic capability surface. The final canvas remains an
honestly labelled **HOST DISPLAY SURFACE**.

The hero begins at Pass II and offers an explicit closer look. The map begins at
Pass I; its GPServers estimate and uncertainty are a locally composed overlay.
**Zooming enlarges what you already know. Looking closer asks the source to tell
you more.** Stop only the source with `docker compose stop lucida`: existing
canvases and local zoom remain, while a closer-Sight request fails without a
raster fallback.

## Passport First Contact

The v0.2 import fixture remains under `demo/passport` as an historical protocol
example. The v0.3.1 runtime creates its own persistent Authority and trust state;
the clean demonstration is now to arrive without a wallet and obtain one from
Passport Office. An independent Technical Passport Service owns the wallet,
holder key and PIN interaction. Postbox hosts its green-bordered interaction
surface but only routes the generic `identity.*` capability and its safe result.
The PIN never reaches Postbox, MailWeb, Passport Office or the Authority.

## Immigration

Butterfly v0.3 begins without assuming an installed identity. Open **Private
positioning records** in Find Me, follow the Passport Office link to
`mailweb://passport.local`, submit only public application details, and complete
issuance in the Passport Service's locally hosted UI. The Office, holder service,
Authority, verifier and Find Me remain separate
MailWeb applications. Authenticated fixes are filed in a dedicated HarmonicDB
Domain and private history is selected from the verified passport identity.

Issuance needs `passport-authority`; entry does not. After issuing a passport,
run `docker compose stop passport-authority`, clear or restart Find Me's
short-lived entry state, and present the passport again. The verifier continues
from signed trust and revocation material in the read-only `passport-trust`
volume. Start the Authority again with `docker compose start passport-authority`
before another application.

Mailpit's host inspection UI is at <http://localhost:8025>. The one-shot Stack Inspector prints its report into `docker compose logs inspector`.

To exercise the vertical journey without the graphical renderer, use Postbox from the Compose network:

```sh
docker compose run --rm mailweb --transport smtp --timeout 180s mailweb://find-me.local/locate
```

## Butterfly 0.4

```text
✓ MailWeb
✓ Global Positioning Servers
✓ HarmonicDB
✓ PassportOffice
✓ Lucida visual representation

◐ Browser canvas / physical display
○ Storage
○ Time
○ Memory
○ Computation
○ Software
○ Architecture
```

Unchecked systems are honest host dependencies, not failed services. Their machine-readable register begins at `machine/manifest.yml`.

## Repository assembly

The five source projects remain Git submodules:

```text
systems/mailweb     → What-if-MailWeb
systems/gpservers   → What-if-GPS-ervers
systems/harmonicdb  → What-if-HarmonicDB
systems/passports   → What-if-Auth-Passports
systems/lucida      → What-if-lucida-img
```

Clone recursively, or run `git submodule update --init --recursive`. Butterfly never imports implementation code across these system boundaries. Component changes stay visible as changes within their respective submodule histories.

Read `FIRST-CONTACT.md` for the boundary assessment and `PRESSURE.md` for what broke when the alternate histories met.

## What broke, and what comes next?

MailWeb survived intact. First contact forced GPServers to separate positioning service semantics from its console and forced HarmonicDB to acquire native Domain growth. Both pressures are now resolved public contracts: transport adapters wrap `service.Positioner`, while Find Me uses a Laravel `LocationRun` model to append actual `BF:…` coordinates to its own HDB store.

The machine's next demand is now lower in the substrate: HWS/7 must learn incremental Domain allocation and a growable harmonic catalogue. Today, correct growth atomically rewrites the store, and semantic indexes can either be rebuilt or explicitly left unmaterialized. Storage remains the strongest host dependency and the clearest next Butterfly pressure.
