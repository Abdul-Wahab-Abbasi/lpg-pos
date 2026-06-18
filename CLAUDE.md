# LPG Point POS

A Point-of-Sale system for a single LPG (gas cylinder) shop, run by two owners (brothers). Cylinder sales, returns, refills, customer udhaar (credit) ledger, inventory, and reports.

**Full roadmap, schema, and conventions live in [`PROJECT_PLAN.md`](./PROJECT_PLAN.md) — read it before doing any work here.** It has a progress checklist; check the next unchecked sprint to know what to do next.

## Tech stack

- Laravel (latest stable) + Blade views — no SPA framework.
- Bootstrap 5 (native JS: Modal/Toast/Dropdown) + Bootstrap Icons, installed via npm/Vite — **not CDN**. This is a local shop POS; it must keep working if the shop's internet is down.
- MySQL via Laragon.
- Inter + JetBrains Mono fonts, self-hosted via npm (`@fontsource/*`), same offline-reliability reason.
- Pest for tests.
- `barryvdh/laravel-dompdf` for Reports PDF export.

## Running locally (Laragon)

- Project folder lives under Laragon's `www` directory (e.g. `C:\laragon\www\lpg-pos`). Laragon auto-creates a vhost — visit `http://lpg-pos.test`.
- DB: create a MySQL database via Laragon's "Database" button or HeidiSQL, default creds `root` / *(empty password)*, host `127.0.0.1`. Set in `.env`.
- `composer install`, `npm install`, `npm run dev` (or `npm run build` for production assets), `php artisan migrate --seed`.
- Two owner accounts are created by `UserSeeder` — see `PROJECT_PLAN.md` for the seeded usernames/passwords.

## Conventions

- One controller per resource/screen (e.g. `ProductController`, `InventoryController`, `SaleController`) — see "Conventions" section in `PROJECT_PLAN.md` for the full controller/route map before adding new ones.
- All multi-step money/stock writes (sale, return, refill, payment) must be wrapped in `DB::transaction()`.
- Validation via Form Request classes, not inline `$request->validate()` in controllers, once past Sprint 1 scaffolding.
- The `ui/` folder is the original static HTML/CSS/JS design prototype — kept as a visual/behavioral reference during the Blade rebuild. Don't treat it as the app; it's reference-only and can be archived/deleted once the rebuild is feature-complete.
- `lpg-pos-design-doc.pdf` is the original designer brief — useful for screen-by-screen field lists if `PROJECT_PLAN.md` is ever ambiguous.

## Working agreement

- Update the progress checklist in `PROJECT_PLAN.md` as sprints complete.
- This project has exactly 2 end users, both non-technical, both with identical full access — don't introduce roles/permissions infrastructure unless asked.
- Keep it boring and maintainable over clever: plain Blade + Bootstrap, no premature abstractions, no multi-tenancy, no API layer — none of that is needed for a single shop with 2 users.
