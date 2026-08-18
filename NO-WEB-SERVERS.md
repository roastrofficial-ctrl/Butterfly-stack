# NO WEB SERVERS — Trial Record

Date: 18 August 2026

## Verdict

For the selected Find Me MailWeb path, the conventional application webserver is **OBSOLETE IN THIS ARCHITECTURE**.

Find Me now runs with `network_mode: none`, no exposed port, and one process: `php artisan mailweb:porter`. That process is started and restarted only by local container policy. It polls local Porter custody, explicitly collects a Package, dispatches its MailWeb request through the Laravel MailWeb publisher, and lodges a correlated Return. Neither carriage arrival nor a Porter starts it.

The browser-facing Postbox UI remains a deliberately retained laboratory interface. It is not the Find Me Host and does not invoke Find Me over HTTP.

## The real journey

```text
browser
  -> Postbox UI (retained laboratory HTTP)
  -> Postbox file boundary
  -> locally scheduled Postbox adapter
  -> Postbox Porter
  -> native encrypted carriage
  -> Find Me Porter
  -> locally scheduled, networkless Find Me Host
  -> Laravel MailWeb Publisher
  -> Find Me Porter
  -> native carriage
  -> HarmonicDB Porter
  -> networkless HarmonicDB Host
  -> Return, Round, explicit Collection
  -> MailWeb Return through both Porters
  -> Postbox renderer
```

The demonstrated route is `mailweb://find-me.local/no-web-servers`. Its first document lodged HDBE Package `PKG-0e0124b6674597a3a393ca9a1fa82bbc` with Ticket `CT-85f720a0653d25d2dbab16dd3e1d3138`. A subsequent local execution made Round `RD-c4355fb31f681bb81ac8aa817432e0e1`, collected HDBE Return `CL-a3c53c7dc0b6fa0c1507896350b47724`, and returned **SERVED WITHOUT A WEB SERVER** to Postbox.

No fake `Illuminate\Http\Request` is constructed. The adapter supplies the MailWeb protocol array directly to `MailWeb\Laravel\Protocol\Publisher`. On this path there is no HTTP response object, session, cookie, CSRF token, authentication session, redirect, streaming response, static file, PHP-FPM process, or termination hook tied to a web request. Laravel bootstrap, its service container, MailWeb routing, exception handling, templates, ROUNDS and application continuation remain real.

## Listener inventory

This is a functional inventory of the running Butterfly laboratory, not a prosecution by executable name.

| Surface | Classification | Remote application invocation? | Disposition |
|---|---|---:|---|
| Find Me (formerly `artisan serve :8000`) | application web server | yes | removed |
| former `find-me-worker` Mailpit poller | legacy MailWeb integration | indirectly | removed from Compose; obsolete orphan stopped |
| Porter Find Me `:7410` | Porter native carriage | no | retained |
| Porter Postbox `:7420` | Porter native carriage | no | retained |
| Porter HarmonicDB `:9177` | Porter native carriage | no | retained |
| Postbox `127.0.0.1:9847` | MailWeb laboratory/browser UI | no; it renders and originates correspondence | retained with justification |
| Mailpit `:1025`, `127.0.0.1:8025` | SMTP and development inspector | for other MailWeb/Passport experiments | retained, absent from selected path |
| Passport service `127.0.0.1:8792` and internal Passport ports | external compatibility/admin | yes, for a different experiment | retained, absent from selected path |
| Lucida `127.0.0.1:8077` | development visual provider | capability service | retained, unavailable to networkless Find Me and absent from selected path |
| GPServers `:8090` | legacy HTTP integration | service invocation | retained for the unmigrated positioning route, absent from selected path |

Find Me never had nginx or PHP-FPM in this repository: its historical equivalent was Laravel's PHP development server. The trial therefore convicted the function, not a binary. The image no longer exposes 8000 or starts `artisan serve`.

## What the old listener bundle did

| Historical responsibility | Does the selected path need it? | Present owner |
|---|---:|---|
| accept TCP connection | no | eliminated |
| parse HTTP and frame a response | no | MailWeb parses correspondence; Porter frames carriage |
| map URI/method to application code | yes | MailWeb Publisher inside the application |
| choose and invoke PHP entrypoint | yes | local Host policy / container init |
| supply server variables | no | eliminated |
| body limits | yes | Porter relationship/package limits and MailWeb decoding limits |
| buffering and connection timeouts | not as a web concern | Porter custody, TTL and Postbox wait policy |
| connection concurrency | no | eliminated; local Package concurrency remains |
| process isolation and restart | yes | container/local Host policy |
| TLS | yes, between network principals | native Porter authenticated encryption |
| static files | no, on this route | Postbox owns its UI assets; MailWeb owns stationery/enclosures |
| health endpoint | no | local `php artisan find-me:health` command |
| access logging | useful | MailWeb journeys, Porter evidence and Host visit journal |
| framework error page | no | MailWeb error correspondence / local logs |

## Host absence and restart proof

Find Me was stopped while its Porter remained running. Postbox lodged request `06G192K6KJRAKQ1GT2XAPNFDFR`; Find Me Porter accepted Package `PKG-3c7cb79ac9884a949675865ccc76eb6d` into `/ipc/inbox`. During that interval:

- the Find Me container state was `exited`;
- its network mode remained `none`;
- no Find Me application process existed;
- the Porter process and native listener remained alive;
- the Postbox request remained waiting.

Starting only Find Me's local policy collected the held Package as `CL-9019ddb38017da3b440be997c0e8cc30`, executed Laravel, lodged Return `PKG-d921733c50f1b46466e6962a24bdf804`, and released the waiting Postbox navigation. Arrival was therefore causally insufficient to execute application code.

The Host disposition journal has durable `COLLECTED`, `HANDLED`, and `RETURN_LODGED` phases. A restart after Collection can resume from Porter's collected custody; a restart after handling can reuse the persisted MailWeb response. A crash in application handling remains application-owned ambiguity. A crash in the narrow interval after Return LG but before recording it can create a duplicate Return; PORTER retains and exposes duplicates rather than pretending exactly-once application effects.

## Performance baseline

Measurements are local Docker Desktop observations, useful for shape rather than universal benchmarks.

| Measure | Historical listener | Networkless persistent Host |
|---|---:|---:|
| idle RSS | 36.59 MiB | 37.9 MiB |
| idle CPU | 0.03% | approximately 0% once settled (100 ms idle cadence) |
| warmed trivial dispatch | 100 `/up` requests in 226.62 ms (2.27 ms each) | simple MailWeb handling commonly 2.5–13 ms |
| genuine HDBE-start document handling | n/a | 17–26 ms |
| isolated Laravel bootstrap | 43.45 ms | 43.45 ms |
| observed Postbox round trip | n/a | 225–302 ms, including two local adapters and two native carriage legs |
| multiple held Packages | webserver request concurrency | sequential batch in one visit; four were dispatched in about 37 ms of visit work after warm bootstrap |

Persistent PHP does not materially reduce RSS compared with the old development server; it removes remote invocation and amortises the roughly 43 ms Laravel bootstrap. Episodic one-Package execution therefore pays more bootstrap than typical application handling. Batching is immediately attractive. The current proof processes held Packages sequentially; Collection makes competing local workers deterministic, but the application has not earned a worker pool yet.

The 25 ms Postbox adapter cadence, 100 ms idle Find Me cadence and carriage scheduling dominate trivial route latency. That is slower than direct loopback HTTP. It is also honest observation latency, not disguised network latency. The old direct listener was quicker at remotely invoking computation; the new path is simpler in custody, restart semantics and causal ownership.

## Corpse inspection

Inside the live Find Me container:

```text
PID 1  www-data  php artisan mailweb:porter
/proc/net/tcp:  header only
/proc/net/tcp6: header only
interfaces with traffic: lo only, 0 bytes
Docker network mode: none
exposed ports: none
published ports: none
```

The image contains no nginx or PHP-FPM configuration. Legacy HTTP URL environment variables were removed from Find Me. Laravel still contains its unused `public/index.php` and HTTP framework facilities: these are compatibility fossils in the source tree, not participants in the selected execution path.

## Webserver Fossil Ledger

| Fossil/function | Classification |
|---|---|
| Find Me TCP listener and remote invocation | ELIMINATED |
| HTTP parsing, server variables and response framing | ELIMINATED |
| native ingress, identity, limits, encryption and custody | MOVED TO PORTER |
| PHP start/restart, observation cadence and batching | MOVED TO HOST POLICY |
| explicit Collection, MailWeb dispatch and disposition journal | MOVED TO APPLICATION |
| URI semantics, documents, stationery and renderer delivery | MOVED TO MAILWEB |
| Postbox browser UI/static assets | RETAINED WITH JUSTIFICATION |
| Laravel HTTP kernel, `public/index.php`, sessions/cookies support | TEMPORARY COMPATIBILITY FOSSIL |
| Find Me `/locate`, Lucida and Passport routes needing unmigrated network services | UNRESOLVED outside the selected path |
| local concurrency, shutdown and active-work policy | UNRESOLVED Host-runtime pressure |

## Pressure and exactly one next experiment

A shared runtime has now earned investigation, but not yet a grand abstraction. The repeated responsibilities are local scheduling, episodic versus warm lifecycle, Round cadence, batching, graceful shutdown, crash injection, active-work tracking, resource limits and local concurrency.

The next experiment is **Host Runtime**: extract only those measured local lifecycle responsibilities from Find Me and HarmonicDB into a small application-neutral contract, while preserving the rule that no arrival can wake a Host. Do not add network listeners and do not generalise application dispatch.

