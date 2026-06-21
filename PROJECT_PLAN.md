# LPG Point POS — Project Plan & Sprint Roadmap

This is the single source of truth for rebuilding the LPG Point POS prototype (`ui/`) as a real Laravel + Bootstrap 5 + MySQL application. Read this in full before starting or resuming work. It's written so a fresh Claude Code session (after the project folder is moved into Laragon's `www`) can continue without being re-briefed — see `CLAUDE.md` for the short version and pointers.

---

## 1. Progress Tracker

Check these off as each sprint is completed. Always check this section first to know what to do next.

- [x] **Sprint 0** — Docs & Planning (`CLAUDE.md`, `PROJECT_PLAN.md`)
- [x] **Sprint 1** — Project Setup & Environment
- [x] **Sprint 2** — Layout & Design System
- [x] **Sprint 3** — Authentication
- [x] **Sprint 4** — Products & Inventory
- [x] **Sprint 5** — Customers
- [ ] **Sprint 6** — Sales / Returns / Refills engine
- [ ] **Sprint 7** — Transactions list & Customer Ledger
- [ ] **Sprint 8** — Dashboard & Reports
- [ ] **Sprint 9** — Hardening & polish
- [ ] **Sprint 10** — Deployment / Go-live

---

## 2. Business Overview

LPG Point is a single gas-cylinder shop run by two brothers (the "owners"). Both log in and use the same system with identical permissions — there is no staff/cashier role to design for right now.

Core operations:
- **Sale** — sell one or more cylinders to a customer (new or existing). Decreases stock, increases what that customer "has out".
- **Return** — customer brings back an empty cylinder they previously got from the shop. No charge. Increases stock, decreases what that customer "has out". Capped at how many of that type they actually have out.
- **Refill** — customer brings their *own* cylinder (doesn't have to be one the shop tracks as "out") to be refilled with gas, for a fee. Does **not** change shop stock count (cylinder leaves full, nothing was added to or removed from inventory). May be paid cash or on credit (udhaar).
- **Udhaar (credit)** — any Sale or Refill can be paid "on credit" instead of cash, which adds to the customer's outstanding `balance`. Owners can later record a "Payment Received" against a customer to reduce that balance.
- **Inventory** — per cylinder-type stock levels, with restock and price-editing actions. Low stock = current qty is under 25% of max capacity.
- **Reports** — daily/monthly/inventory views, plain tables, with PDF export.

This business logic is already fully worked out in the prototype (`ui/assets/js/main.js` + the 10 `ui/*.html` screens) and the design doc (`lpg-pos-design-doc.pdf`). Treat both as the functional spec; this plan's job is to map that spec onto a real normalized database and Laravel app structure — fixing a few things that only worked because the prototype faked its DB in `localStorage`.

### Deliberate corrections vs. the prototype

1. **`cylindersOut: {}` JSON blob → `customer_cylinder_balances` table.** The prototype stored "what a customer has out" as a JSON object keyed by product id, mutated in JS. We use a real `(customer_id, product_id) → qty_out` table so it's queryable and consistent under concurrent writes.
2. **Ledger only records balance-affecting events.** The prototype wrote a ledger row for every transaction (including cash ones, with a separate `cashAmt` field that didn't actually move the running balance), which made the ledger confusing. In this rebuild, `ledger_entries` only gets a row for: credit sales/refills (debit, increases balance) and payments received (credit, decreases balance). Cash transactions still show up in `transactions` and in the Reports payment breakdown — they just don't need a ledger row, since they never touched `balance`.
3. **No separate "invoice counter".** The prototype kept a separate incrementing counter per prefix (`DB.nextId('INV')`) in `localStorage`. We just use the transaction's auto-increment `id` and format an `invoice_no` from it after insert — no extra counter table to keep in sync, no race condition.
4. **Inventory fields live on `products`, not a separate `inventories` table.** It's a strict 1:1 relationship in this domain (no multi-warehouse), so a join would buy nothing. If multi-location is ever needed, extracting it later is a small, well-contained migration.
5. **`stock_movements` audit table is new** (the prototype had no audit trail — a restock just silently changed a number). Cheap to add now, valuable later for "why is stock wrong" debugging.

---

## 3. Tech Stack & Architecture Decisions

| Concern | Decision | Why |
|---|---|---|
| Framework | Laravel, latest stable | Standard, well-documented, matches user's ask |
| Views | Blade only, no SPA framework | 2 non-technical users, no need for Vue/React/Livewire complexity |
| CSS framework | Bootstrap 5 (npm, via Vite) | User explicitly asked for Bootstrap; gives real Modal/Toast/Dropdown/Collapse JS components |
| Icons | Bootstrap Icons (npm, via Vite) | Already used throughout the prototype's markup |
| Fonts | Inter + JetBrains Mono via `@fontsource/*` (npm) | Matches prototype's design tokens, but self-hosted |
| **No CDN at runtime** | All of the above bundled via Vite | This is a local shop POS — it must work if the internet is down |
| Database | MySQL (via Laragon) | User's requirement |
| Auth | Hand-rolled controller + `Auth::attempt()` + sessions | Only 2 users, login/logout only, no registration/password-reset/email-verification needed — pulling in Breeze/Fortify would mean ripping out unwanted scaffolding |
| PDF export | `barryvdh/laravel-dompdf` | For the Reports "Download PDF" buttons |
| Printable receipts | Blade partial + `@media print` CSS + `window.print()` | Matches prototype behavior, no extra dependency needed |
| Tests | Pest | Laravel's current default; feature tests for the money-math flows (sale/return/refill/ledger) matter most here |
| Roles/permissions | None — both users have full identical access | Explicit user decision; don't build roles infrastructure pre-emptively |

---

## 4. Local Dev Environment (Laragon)

1. Project folder lives under Laragon's `www`, e.g. `C:\laragon\www\lpg-pos`. Laragon auto-creates a vhost at `http://lpg-pos.test` (or use `http://localhost/lpg-pos/public`).
2. Create a MySQL database (Laragon's "Database" button, or HeidiSQL/phpMyAdmin) — suggested name `lpg_pos`.
3. `.env`: `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=lpg_pos`, `DB_USERNAME=root`, `DB_PASSWORD=` (Laragon's MySQL default has no root password — confirm in Laragon's database UI).
4. Requires PHP 8.2+ and Composer 2 (Laragon lets you switch PHP version per-project if needed) and Node/npm for Vite.
5. Standard cycle: `composer install`, `npm install`, `npm run dev` (or `npm run build`), `php artisan migrate --seed`, `php artisan serve` is *not* needed under Laragon (it serves via the vhost automatically).

### Pre-flight check (do this before any Sprint 1 command)

Laragon bundles PHP/Composer/MySQL/Node but doesn't always put them on the system `PATH` for a terminal opened outside of Laragon itself. Before running anything, verify in the terminal Claude Code is using:

```
php -v
composer -V
npm -v
mysql --version
```

If any of these fail to resolve, either (a) enable Laragon's "Add to PATH" — right-click the Laragon tray icon → *Tools* → *Quick add PATH (Top priority)*, then restart the terminal — or (b) launch the terminal via Laragon's own *Terminal* button (it pre-configures the PATH for that session). Don't proceed with Sprint 1 until all four commands resolve.

---

## 5. Database Schema

All tables use Laravel's default `id` (unsigned bigint) + `timestamps()` unless noted otherwise. Money columns are `decimal(10,2)`.

### `users`
- `name` string
- `username` string, unique
- `password` string (hashed)

### `products`
*(catalog + inventory merged — see correction #4 above)*
- `name` string
- `category` string, default `'Cylinder'`
- `sale_price` decimal
- `refill_charge` decimal
- `return_deposit` decimal
- `unit` enum: `pcs`, `kg`, `ltr`
- `qty` unsigned int — current stock
- `min_qty` unsigned int — alert threshold
- `max_qty` unsigned int — capacity used for the % stocked bar

### `stock_movements`
- `product_id` FK → products
- `type` enum: `RESTOCK`, `SALE`, `RETURN`, `ADJUSTMENT`
- `qty_change` int (signed)
- `qty_after` unsigned int
- `note` string, nullable
- `created_by` FK → users

### `customers`
- `name` string
- `phone` string, unique
- `address` string, nullable
- `note` string, nullable
- `balance` decimal, default 0 — current outstanding udhaar
- `total_sales` decimal, default 0 — lifetime debit total (sales + refills, cash + credit)
- `total_paid` decimal, default 0 — lifetime credit payments received
- `last_visit_at` timestamp, nullable

### `customer_cylinder_balances`
- `customer_id` FK → customers
- `product_id` FK → products
- `qty_out` unsigned int, default 0
- unique on (`customer_id`, `product_id`)

### `transactions`
- `invoice_no` string, unique — formatted from `id` after insert, e.g. `INV-00001`
- `customer_id` FK → customers
- `type` enum: `SALE`, `RETURN`, `REFILL`
- `payment_method` enum: `cash`, `easypaisa`, `jazzcash`, `credit` — nullable (RETURN has none)
- `amount` decimal — total
- `qty` unsigned int — total qty across items (denormalized for fast list display)
- `condition` enum: `ok`, `damaged`, `leaking` — nullable, RETURN only
- `note` string, nullable
- `created_by` FK → users

### `transaction_items`
- `transaction_id` FK → transactions
- `product_id` FK → products
- `qty` unsigned int
- `unit_price` decimal
- `subtotal` decimal

### `ledger_entries`
*(only balance-affecting events — see correction #2 above)*
- `customer_id` FK → customers
- `transaction_id` FK → transactions, nullable (manual adjustments have none)
- `type` enum: `SALE`, `REFILL`, `PAYMENT`, `ADJUSTMENT`
- `description` string
- `debit` decimal, default 0
- `credit` decimal, default 0
- `balance_after` decimal
- `payment_method` string, nullable
- `created_by` FK → users

### Business rules these tables must enforce (in controllers/services, not the DB)

- Low stock: `products.qty / products.max_qty < 0.25`.
- **Sale**: for each item — decrement `products.qty`, upsert `customer_cylinder_balances.qty_out += qty`, insert `stock_movements` (type `SALE`, negative `qty_change`). Update `customers.total_sales`, `last_visit_at`. If `payment_method = credit`: `customers.balance += amount`, insert `ledger_entries` (type `SALE`, debit). All inside one `DB::transaction()`.
- **Return**: validate `qty <= customer_cylinder_balances.qty_out` for that product (error if not). Increment `products.qty`, decrement `qty_out`, insert `stock_movements` (type `RETURN`, positive). No charge, no ledger row.
- **Refill**: does **not** touch `products.qty` or `customer_cylinder_balances` at all — it's independent of the shop's own stock/out-tracking. Update `customers.total_sales`, `last_visit_at`. If `payment_method = credit`: same as Sale's credit handling.
- **Payment Receive**: `customers.balance = max(0, balance - amount)`, `customers.total_paid += amount`, insert `ledger_entries` (type `PAYMENT`, credit).

---

## 6. Conventions

### Controllers / routes (one per screen, matching the prototype's screen list)

| Screen (prototype file) | Controller | Notes |
|---|---|---|
| `index.html` (Dashboard) | `DashboardController@index` | aggregate queries only, no model of its own |
| `Login.html` | `AuthController@show/login/logout` | no registration |
| `sale.html` | `SaleController@create/store` | multi-item |
| `Return.html` | `ReturnController@create/store` | single item |
| `Refill.html` | `RefillController@create/store` | single item |
| `transactions.html` | `TransactionController@index/show` | `show` = receipt view |
| `Customers.html` + ledger modal | `CustomerController@index/store/ledger/paymentReceive/balanceReceipt` | add-customer endpoint reused by Sale/Return/Refill via a shared Blade/JS partial |
| `Products.html` | `ProductController` (full resource) | catalog CRUD |
| `inventory.html` | `InventoryController@index/restock/updatePrice/updateLevels` | reads/writes the same `Product` model as `ProductController`, different screen/concerns |
| `Reports.html` | `ReportController@daily/monthly/inventory/pdf` | |

### Other conventions

- Validation: Form Request classes per write action (`StoreSaleRequest`, etc.) once past initial scaffolding — don't leave validation inline in controllers long-term.
- All multi-step money/stock writes wrapped in `DB::transaction()`.
- Blade components for repeated UI: stat card, badge, modal shell, receipt partial — built in Sprint 2, reused everywhere after.
- Keep `ui/` around as a visual/behavioral reference until the rebuild is feature-complete; it is not part of the shipped app.

---

## 7. Sprint Roadmap

### Sprint 0 — Docs & Planning ✅
`CLAUDE.md` + this file. Done.

### Sprint 1 — Project Setup & Environment
- `composer create-project laravel/laravel .` in the project root (keep `ui/` and the design PDF alongside — no naming conflicts with Laravel's own folders).
- Configure `.env` for the Laragon MySQL DB.
- `git init` + a Laravel-appropriate `.gitignore` (keep `ui/` tracked).
- `npm install` Bootstrap 5, Bootstrap Icons, `@fontsource/inter`, `@fontsource/jetbrains-mono`; wire into `resources/css`/`resources/js` + `vite.config.js`.
- Install `barryvdh/laravel-dompdf`.
- Set up Pest (`php artisan pest:install` or via the Laravel installer's testing prompt).
- **Definition of done**: `npm run dev` + visiting the Laravel welcome-replacement page works on `http://lpg-pos.test` with Bootstrap CSS/JS and the custom fonts loading with no console errors and no external network requests.

### Sprint 2 — Layout & Design System
- Port the color tokens, spacing, and typography from `ui/assets/css/style.css` into `resources/css/app.css` as CSS variables, layered on top of Bootstrap 5's defaults (override Bootstrap's CSS vars where they overlap, e.g. `--bs-body-bg`, `--bs-primary`).
- Build the master layout (`resources/views/layouts/app.blade.php`): fixed sidebar (230px, matches prototype), topbar, content `@yield`/`@section`, user/logout footer.
- Build reusable Blade components: `<x-stat-card>`, `<x-badge>`, `<x-modal>` (wrapping Bootstrap's Modal JS), toast container (wrapping Bootstrap's Toast JS).
- Build a `/style-guide` route/view exercising every component for visual sanity-checking — delete or gate it behind local-only once done.
- **Definition of done**: the style-guide page visually matches the prototype's dark theme, and a Bootstrap modal + toast both work using Bootstrap's own JS (no hand-rolled `openModal()`).

### Sprint 3 — Authentication
- `users` migration + `UserSeeder` (seed both owners — pick real names/usernames/passwords at this point, not placeholders).
- `AuthController` (`show` login form, `login`, `logout`), session-based, no registration/reset routes.
- Login Blade view matching `ui/Login.html` (centered card, no sidebar, eye-icon password toggle, inline error on bad credentials).
- `auth` middleware applied to every route except login.
- **Definition of done**: can log in as either owner, get redirected to a (still-empty) dashboard, can't reach any route unauthenticated, can log out.

### Sprint 4 — Products & Inventory
- `products` + `stock_movements` migrations + a seeder with the shop's real cylinder types (11kg, 5kg, 45kg commercial, HOB mini — confirm exact names/prices with the user, prototype values are placeholders).
- `ProductController` resource (catalog CRUD — matches `Products.html`).
- `InventoryController` (matches `inventory.html`): card grid, restock modal (writes a `stock_movements` row + bumps `qty`), edit-price modal, set-levels modal (min/max).
- Low-stock badge/alert wired to the `qty/max_qty < 0.25` rule.
- **Definition of done**: can add/edit/delete products, restock from the Inventory screen and see both `products.qty` and a new `stock_movements` row update, low-stock badge appears under 25%.

### Sprint 5 — Customers
- `customers` + `customer_cylinder_balances` migrations.
- `CustomerController@index` (matches `Customers.html`: search, filter by out/balance, summary cards).
- Add-customer modal as a shared partial (used standalone here, and reused by Sale/Return/Refill in Sprint 6).
- **Definition of done**: can add/search/filter customers; list shows correct (currently zero) cylinders-out and balance columns ready for Sprint 6 to populate.

### Sprint 6 — Sales / Returns / Refills engine
- `transactions`, `transaction_items`, `ledger_entries` migrations.
- `SaleController` (matches `sale.html`: multi-item, autocomplete-or-add customer, stock check before submit, all side effects per the business rules in §5, receipt partial shown after).
- `ReturnController` (matches `Return.html`: only show product types the selected customer has out, cap qty, condition dropdown, receipt).
- `RefillController` (matches `Refill.html`: charge field, payment method, receipt).
- Receipt Blade partials (Sale/Return/Refill) + print stylesheet.
- **Definition of done**: a full Sale → stock decremented, customer's qty_out increased, ledger row only if credit; a Return → capped correctly, stock incremented, qty_out decremented; a Refill → stock unchanged; all three produce a printable receipt. Pest feature tests cover these three flows' DB side effects.

### Sprint 7 — Transactions list & Customer Ledger
- `TransactionController@index` (matches `transactions.html`: search/type/date filters, receipt view button reusing the Sprint 6 partials).
- `CustomerController@ledger` (matches the ledger modal in `Customers.html`/design doc screen 08: Dr/Cr table, running balance, 4 mini stat cards).
- `CustomerController@paymentReceive` (decrements balance, inserts `ledger_entries` PAYMENT row).
- `CustomerController@balanceReceipt` (printable udhaar receipt).
- **Definition of done**: every transaction from Sprint 6 is visible/filterable here; recording a payment against a customer reduces their balance and shows correctly in their ledger.

### Sprint 8 — Dashboard & Reports
- `DashboardController@index` (matches `index.html`: today's revenue, cylinders out, refills today, low-stock count, recent transactions, stock overview panel).
- `ReportController@daily/monthly/inventory` (matches `Reports.html`'s three tabs).
- `ReportController@pdf` using `barryvdh/laravel-dompdf` for the "Download PDF" buttons.
- **Definition of done**: dashboard numbers match what you'd compute by hand from the transactions created in Sprint 6/7; each report tab renders and its PDF downloads with the same data.

### Sprint 9 — Hardening & polish
- Convert any inline `$request->validate()` left over from earlier sprints into Form Request classes.
- Audit N+1 queries (eager-load relations on every index/list view).
- Pest feature tests for anything not already covered (auth, validation edge cases, low-stock thresholds, return-qty cap).
- Friendly 403/404/500 error pages matching the dark theme.
- Re-read this whole plan against the shipped app and fix any drift.
- **Definition of done**: `php artisan test` passes; no N+1 warnings from Laravel Debugbar/Telescope if installed; manual click-through of all 10 screens with no console errors.

### Sprint 10 — Deployment / Go-live
- Production `.env` for the Laragon machine (or wherever it'll actually run day-to-day).
- `npm run build`, `php artisan migrate --force` against real data (or a clean seed if starting fresh).
- Short README/runbook for the two owners: how to start it, how to back up the MySQL DB (a simple scheduled `mysqldump` is enough at this scale), who to call if something breaks.
- **Definition of done**: the owners can open the app on the shop PC and use it for real transactions; a backup of the DB exists and is documented.

---

## 8. Appendix — Prototype Screen → Route Map

For quick cross-reference while building, the prototype files map to these intended routes:

| Prototype file | Route |
|---|---|
| `index.html` | `/` (dashboard) |
| `Login.html` | `/login` |
| `sale.html` | `/sales/create` |
| `Return.html` | `/returns/create` |
| `Refill.html` | `/refills/create` |
| `transactions.html` | `/transactions` |
| `Customers.html` | `/customers` |
| Customer ledger modal | `/customers/{customer}/ledger` |
| `Products.html` | `/products` |
| `inventory.html` | `/inventory` |
| `Reports.html` | `/reports` |
