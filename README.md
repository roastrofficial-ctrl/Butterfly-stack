# Butterfly

The current experiment makes locally chosen Host attention cheap without making
arrival powerful. A single disposable candidate projection lets the unchanged
Host Runtime find relevant work without scanning all Porter custody. See
[INDEXED-ATTENTION-CHECK.md](INDEXED-ATTENTION-CHECK.md) for the causality proof,
measurements, corruption matrix and verdict; [HOST-RUNTIME-PRESSURE.md](HOST-RUNTIME-PRESSURE.md)
records the preceding runtime experiment.

> Butterfly is a working computer ecosystem assembled from technologies whose histories never happened.

| "It's just a website that tells your your approximate location"...but...

Each subsystem began as an independent counterfactual experiment. Generation 0.4 makes them survive contact without erasing their boundaries: MailWeb carries application correspondence, Global Positioning Servers estimates location, HarmonicDB stores measurements, Technical Passport supplies holder identity, and Lucida supplies selective visual observation. Find Me is an independent application that consumes those public boundaries.

## Boot the machine

Docker is the only host prerequisite.

```sh
docker compose up --build
```

Open <http://localhost:9847>. Postbox begins at `mailweb://find-me.local/`. Press **Acquire network fix**; the request and its eventual result travel as real MailWeb correspondence through SMTP. Acquisition may take up to two minutes because GPServers performs live network measurements.

## Lucida First Contact

Find Me keeps its hero and map originals in private application resources. When
preparing correspondence it gives an ephemeral **HOST SIGHT INPUT** to Lucida's
thin server-side tracing service and receives an opaque prepared-Sight handle.
The MailWeb document names Find Me's `find-me.hero` or `find-me.map` Sight and
asks the configured `visual.observe` provider for selective SIGHT/1 releases;
neither the document nor Postbox receives the original. Lucida owns tracing,
protocol interpretation, observer state and the receiving Draughtsman, but owns
no Find Me asset catalogue. Postbox supplies only a generic capability surface.
The final canvas remains an honestly labelled **HOST DISPLAY SURFACE**.

The hero begins with a bounded high-detail release and offers an explicit closer look. The map begins at
Pass I; its GPServers estimate and uncertainty are a locally composed overlay.
**Zooming enlarges what you already know. Looking closer asks the source to tell
you more.** Stop only the source with `docker compose stop lucida`: existing
canvases and local zoom remain, while a closer-Sight request fails without a
raster fallback.

## PORTER Generations I–VI and ROUNDS

PORTER tests the Host Isolation Principle: computational Hosts are not directly
network-addressable. They communicate locally with appointed Porters, which
carry opaque Packages across a separate communications fabric. A recipient
Porter holds an arrival; it never invokes the Host. The Host must explicitly
collect it.

Generation I proves the strong form with two `network_mode: none` fixture Hosts:

```sh
cd systems/porter
python3 -m unittest -v
./tests/docker_generation1.sh
./tests/docker_generation2.sh
./tests/docker_generation3.sh
./tests/docker_generation4.sh
./tests/docker_generation5.sh
./tests/docker_generation6.sh
```

HarmonicDB is the first real Butterfly Host migrated behind a Porter. Its
container has no IP address, route, listener, or membership of either Butterfly
network. Find Me now receives a durable Collection Ticket when it deposits an
opaque HDBE/1 call and ends that execution without waiting. A later MailWeb
request explicitly inspects and collects the Return; append and observation are
two distinct pieces of correspondence across multiple executions. PORTER owns
the carriage and ticket lifecycle, Find Me owns its continuation record, and
HarmonicDB remains unaware of PORTER. See `systems/porter/README.md` for the
observed crash, duplicate, expiry, abandonment and collection semantics.

PORTER 1.2 now treats compromise as succession of recipient-local standing. A
stolen capability was accepted before the atomic change threshold, refused for
new Packages afterward, and preserved for exact historical AC replay. Real
Find Me correspondence continued through replacement standing while HarmonicDB
remained networkless.

PORTER 1.3 makes the knowledge causing that succession travel. A separately
granted, bounded ceremony is addressed to the recipient Porter itself; it never
crosses Host AC or CL. The real delayed experiment preserved attacker ACs made
before recipient knowledge, refused the old capability after SC, and continued
real HDBE through successor authority.

PORTER 1.4 removes HTTP carriage from the real Find Me ↔ HarmonicDB path. Porters
exchange mutually authenticated encrypted native Units; acceptance evidence and
ceremony results return as separate carriage. HarmonicDB Porter moved from one
rendezvous port to another while identity, standing, Hosts and application code
remained unchanged.

PORTER 1.5 lets authenticated rendezvous and carriage-key knowledge move too.
Signed `RV-…` chains preserve Porter identity across overlap and no-overlap
movement without static-map edits, central discovery, or DNS-as-identity.

Generation III gives lodgement one atomic threshold. A canonical `LG-…` fact
binds Package and Ticket identities before any recoverable projection is made;
a restarted Porter can reconstruct missing Ticket, association and outgoing
facts. PORTER's Host-side clients now share `PORTER-ROUNDS/1`: a durable `RD-…`
observation over one or many Collection Tickets. During an active positioning
journey, Find Me chooses when to make those ROUNDS through distinct MailWeb
executions, then separately chooses collection and continuation. The page exposes
when a Return was held, when a later Round observed it, and how much latency
belonged to Host attention. PORTER remains silent and knows nothing of the
journey.

Generation IV separates remote fact from local knowledge. The receiving Porter
atomically publishes one canonical acceptance fact containing the original
Package identity. Its inbox is a recoverable projection. The originating Porter
records every carriage attempt as `ACCEPTANCE_UNKNOWN` and may claim
`REMOTE_ACCEPTANCE_KNOWN` only after durably retaining a precise Receipt from the
recipient. Repeated carriage of the same Package recovers the same acceptance;
it does not invent a second correspondence. ROUNDS expose this knowledge without
waking the Host or changing its attention policy.

Generation V gives Host-initiated Collection its own threshold. The immutable
`CL-…` fact contains the exact accepted Package and records its recoverable
transfer from recipient Porter custody to recipient Host custody. Inbox,
collected Package and Package-to-Collection association are projections repaired
from that fact. `LG`, `AC`, and `CL` remain historical; none is mutated into a
global status. Collection transfers custody, not application meaning, and is not
propagated back to the sender.

Generation VI deliberately adds no PORTER primitive. A five-stage crash matrix
moved from application read through HDBE effect, application record, Return draft
and Return lodgement. No generic `DS-…` disposition could distinguish success,
failure, completion or abandonment without interpreting application semantics.
A Return proves only that related correspondence was lodged. PORTER now
explicitly terminates at `CL`; applications own interpretation, effects,
transactions, recovery and the decision to lodge further correspondence.

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

## Butterfly 1.0

```text
✓ MailWeb
✓ Global Positioning Servers
✓ HarmonicDB
✓ PassportOffice
✓ Lucida visual representation
✓ PORTER mediated communication

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

The six source projects remain Git submodules:

```text
systems/mailweb     → What-if-MailWeb
systems/gpservers   → What-if-GPS-ervers
systems/harmonicdb  → What-if-HarmonicDB
systems/passports   → What-if-Auth-Passports
systems/lucida      → What-if-lucida-img
systems/porter      → What-if-Porter
```

Clone recursively, or run `git submodule update --init --recursive`. Butterfly never imports implementation code across these system boundaries. Component changes stay visible as changes within their respective submodule histories.

Read `FIRST-CONTACT.md` for the boundary assessment and `PRESSURE.md` for what broke when the alternate histories met.

## What broke, and what comes next?

MailWeb survived intact. First contact forced GPServers to separate positioning service semantics from its console and forced HarmonicDB to acquire native Domain growth. Both pressures are now resolved public contracts: transport adapters wrap `service.Positioner`, while Find Me uses a Laravel `LocationRun` model to append actual `BF:…` coordinates to its own HDB store.

The machine's next demand is now lower in the substrate: HWS/7 must learn incremental Domain allocation and a growable harmonic catalogue. Today, correct growth atomically rewrites the store, and semantic indexes can either be rebuilt or explicitly left unmaterialized. Storage remains the strongest host dependency and the clearest next Butterfly pressure.
