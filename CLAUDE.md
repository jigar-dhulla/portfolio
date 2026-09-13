# Portfolio

Personal portfolio site. Laravel 13 on FrankenPHP (PHP 8.4), SQLite, deployed as a
container image to a shared VPS.

## Deployment architecture — read this before touching Docker or routing

This app does **not** own its own ingress. It shares a VPS (`5.223.52.5`) with an
unrelated app (`yaarpool`, at `github.com/jigar-dhulla/yaarpool-whatsapp-agent`),
and a single host-wide Traefik terminates TLS for both.

```
internet :443
     |
  infra-traefik-1          /opt/infra          <- owns :80/:443, Let's Encrypt
     |  (proxy network)
     +-- portfolio-web-1   /opt/portfolio      <- this app
     +-- yaarpool-web-1    /opt/yaarpool
```

Three rules follow from that, and breaking any of them breaks the *other* app too:

1. **Never add a `ports:` mapping.** Traefik reaches the container over the shared
   `proxy` network. Publishing a port would expose the app on the host bypassing
   TLS, and can collide with whatever else is on the box.
2. **`proxy` must stay `external: true`.** It is created once on the host
   (`docker network create proxy`) and is deliberately owned by no compose
   project, so that no single app's `docker compose down` can delete it.
3. **Traefik router and service names must be globally unique**, not just unique
   within this file. Traefik reads labels for every project off the Docker socket
   at once. Ours are all named `portfolio`; yaarpool's are `yaarpool`. A
   collision silently hijacks the other app's routing.

`WEB_HOST` has no default. An unset value makes Traefik reject the router, which
is intentional — a default would request a Let's Encrypt certificate for the
wrong domain and burn the failed-challenge rate limit.

## How a deploy happens

Deployment is **pull-based** — nothing pushes to the server.

```
push to main -> CI builds -> ghcr.io/jigar-dhulla/portfolio:latest
                                      |
        portfolio-deploy.timer (every 2 min, on the server) polls the digest
                                      |
                  digest changed? -> migrate -> compose up -d
```

So **merging to `main` ships to production within ~2 minutes.** There is no
separate deploy step to forget, and no staging environment.

The trigger is outbound by design: the box runs CrowdSec, which has previously
dropped inbound SSH from GitHub runners. `deploy/deploy.sh` re-syncs *itself* and
`docker-compose.prod.yml` from `main` before rolling out, so changes to either
take effect on the next deploy — but note that means **the server always runs the
compose file from `main`**, not whatever is checked out locally.

The GHCR package must stay **public**: `deploy.sh` reads the manifest digest with
an anonymous registry token. Making the package private silently stops deploys
(the digest read returns empty and the script skips the cycle).

## Layout

| Path | What |
|---|---|
| `Dockerfile` | Multi-stage: node builds assets, composer installs `--no-dev`, FrankenPHP runtime |
| `docker-compose.prod.yml` | Production topology. `web`, `queue`, `scheduler`, plus a `cli`-profile `app` for one-off artisan |
| `deploy/deploy.sh` | Digest-polling rollout, runs on the server at `/opt/portfolio/deploy.sh` |
| `deploy/portfolio-deploy.{service,timer}` | systemd units, installed to `/etc/systemd/system/` |
| `.github/workflows/publish-image.yml` | Builds and pushes to GHCR on push to `main` |

On the server, `/opt/portfolio/.env` is the real environment file — it is **not**
in git and is bind-mounted into every container. `COMPOSE_FILE` is set there so a
bare `docker compose` in that directory finds the prod file.

The database is SQLite on the `app-db` docker volume, at
`/app/database/data/database.sqlite`. `DB_CONNECTION` and `DB_DATABASE` are pinned
in `docker-compose.prod.yml`; setting them in `.env` has no effect.

## Local development

```sh
composer install && npm install
php artisan serve      # or use Herd
npm run dev
```

The local SQLite file (`database/database.sqlite`) is gitignored and unrelated to
production data.

## Useful server commands

```sh
ssh root@5.223.52.5
cd /opt/portfolio

docker compose ps
docker compose logs -f web
docker compose run --rm app php artisan tinker   # one-off, does not touch web
./deploy.sh                                       # force a rollout now
journalctl -u portfolio-deploy.service -n 50      # why didn't it deploy?
```

<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>
