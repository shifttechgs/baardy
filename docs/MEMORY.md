# MEMORY

The project's long-term memory. Read this first in a new session.

It records what exists, what deliberately does not, and the decisions that a
future developer could reverse by accident. If you make a decision that would
be expensive to rediscover, add it here.

Last updated: **2026-09-22**

---

## Current status

### Phase 1 — Frontend Foundation

A Laravel 13 application serving a single public marketing homepage for a
microfinance / SME lending business.

**What exists is a complete, production-quality front end for one page, and
nothing behind it.** There is no database, no authentication, no application
flow and no business logic. Every figure on the page is a labelled placeholder.

The purpose of this phase was to establish the design system, the component
architecture and the documentation system so that later phases have something
coherent to build on.

---

## What has been implemented

**Application**
- Laravel 13.32 on PHP 8.4, created from the standard skeleton.
- `laravel/boost` (dev) — generates version-accurate framework guidance for AI
  agents in `CLAUDE.md` / `AGENTS.md` and installs skills under
  `.claude/skills/`.
- One route: `GET /` → `pages.home`, via `Route::view`. No controller, because
  the page has no data that does not belong in config.

**Frontend**
- Tailwind CSS v4, CSS-first config. **No `tailwind.config.js`.**
- Vite 8 via `laravel-vite-plugin`.
- Alpine.js 3, used in two places only.
- Geist, self-hosted as a variable font from the `geist` npm package.
- A complete design token system in `resources/css/app.css` — colour, type
  scale, spacing, radius, shadow. No raw hex values anywhere else.

**Components** (`resources/views/components/`)
- `ui/`: container, section, section-header, button, badge, card cell,
  rule-grid, stat, money, icon, logo, field, accordion-item, disclosure.
- `layout/`: navbar, footer.

**Homepage sections** (`resources/views/sections/home/`)
hero · trust · products · how-it-works · calculator · why-us · testimonials ·
faq · cta

**Content**
- `config/company.php` — identity, contact, navigation, footer, compliance.
- `config/marketing.php` — all homepage copy and every placeholder figure.
- No copy is hardcoded in a template.

**Quality**
- Verified at 1440 / 1280 / 1024 / 768 / 430 / 390 / 375px, no horizontal
  scrolling at any width.
- One `<h1>`, correct heading hierarchy, semantic landmarks.
- All text contrast meets WCAG AA; most pairs meet AAA (`DESIGN.md` §4).
- Keyboard-operable throughout, single global focus style, skip link,
  `prefers-reduced-motion` respected.

**Documentation** — the six files in `/docs`.

---

## What has NOT been implemented

Deliberately out of scope for Phase 1:

- No database connection, migrations, models or Eloquent
- No authentication, registration or user accounts
- No loan application form or submission handling
- No eligibility, affordability, interest or repayment calculation
- No payment or disbursement integration
- No API
- No admin dashboard or CRM
- No customer portal
- No email or SMS sending
- No structured data / JSON-LD (see *Decisions*, below)
- No legal pages — the footer links to them but they do not exist
- No tests beyond the two that ship with the skeleton
- No CI

---

## Architectural decisions

**Blade + Alpine, not React/Vue/Inertia.**
A marketing site benefits from server-rendered HTML — indexability, first-paint
speed, and no hydration. Alpine covers the small amount of interactivity
needed. Introducing a SPA framework later would be a significant, deliberate
change, not an incremental one.

**Anonymous Blade components, not class-based.**
None of the components need logic. Add a class only when one genuinely does.

**Content in `config/`, not in templates or a database.**
Copy and figures change often and are edited by people who should not have to
touch markup. Config is the lightest thing that achieves that, and it moves
cleanly behind a CMS later.

**`Route::view` rather than a controller.**
No data-fetching, so a controller would be an empty file. Add one when there is
something to pass.

**File-based session, cache and queue drivers.**
The Laravel default is `database`, which crashes the app without a database.
Changed to `file` / `sync` in `.env` and `.env.example`. **Do not change these
back until a database genuinely exists** — the default skeleton values will
break the site.

**The stock `database/database.sqlite` was deleted** and the default migrations
were left in place, untouched. Nothing connects to them.

---

## Design decisions

Full rationale in `DESIGN.md`. The load-bearing ones:

**Structure is carried by hairline rules, not shadows or cards.**
Sections are divided by full-width rules; card groups are built as *ruled
grids* (a 1px gap over a line-coloured background), not as separated tiles.
Exactly two shadows exist in the system. This is the single thing that makes
the page read as a financial document rather than a SaaS landing page.
**Reversing it would undo the visual identity.**

**One accent: a deep forest green (`#0b5c41`).**
Chosen against the default fintech indigo/violet, and deliberately not a bright
emerald. It is dark enough to act as ink, which is why it can carry buttons,
links and emphasis without making the page colourful.

**Geist at weight 500 for headings, not 700.**
Bold display type reads as promotional. Tracking tightens as size increases.

**The hero's visual is a ledger of recent facilities, not a dashboard mock.**
A record of money moving is the artefact a lender actually produces, and it
makes the headline concrete. It is a real `<table>` because it is real tabular
data.

**Almost everything is on white.** Two departures only: `mist` on a few bands
and panels, and the dark foot (closing CTA + footer as one ink block).
Alternating light/dark bands section by section is what makes a page look
generic.

**One motion moment.** A staggered hero entrance on load. No scroll reveals, no
hover lifts on cards.

**The page is left-aligned throughout.** Nothing is centred.

---

## Decisions that must not be accidentally reversed

1. **There is no repayment calculation anywhere, and that is intentional.**
   The calculator echoes the amount and term the visitor selected; the
   repayment field shows a pending state with an explanation. Any formula would
   need an interest rate and fee schedule that have not been set, and
   publishing an invented repayment figure on a lending site is a regulatory
   problem, not a placeholder. The extension point is documented in
   `sections/home/calculator.blade.php`.

2. **No JSON-LD is emitted, deliberately.** Structured data built from
   placeholder company details would assert unverified facts about a regulated
   business to search engines. The reason is recorded in the layout. See
   `SEO.md` §7 for the implementation order once real details exist.

3. **Compliance fields in `config/company.php` are unfilled on purpose.**
   Licence number, regulator and registration number must come from the client.
   Do not guess, and do not remove the placeholders to "tidy up" — the footer
   renders them, which is how they stay visible.

4. **Every placeholder figure carries a visible `<x-ui.disclosure>`.**
   Removing one is a decision about honesty, not a cleanup task. They come out
   when the data beside them becomes real.

5. **The testimonials are invented and must not be published.** They are
   labelled on the page. Replace with consented, attributable quotes.

6. **The FAQ uses native `<details name="faq">`, not a JS accordion.**
   It is keyboard- and screen-reader-correct for free, works before JavaScript
   loads, and is findable by in-page search. Do not "upgrade" it to a custom
   widget.

7. **Do not add `tailwind.config.js`.** v4 configuration is CSS-first via
   `@theme`. Adding one would split the token system in two.

---

## Important conventions

- Design tokens live only in `resources/css/app.css`. No raw hex in a template.
- Copy lives only in `config/marketing.php`.
- Components use `$attributes->merge()` so callers can extend them.
- `<x-ui.button>` renders `<a>` with `href`, `<button>` without.
- Money renders through `<x-ui.money>` so currency formatting is defined once.
- Section files are named to match their anchor id.
- Every component file opens with a comment explaining *why* it is built that
  way, not just what it is.

---

## Known limitations

- **The currency symbol is `¤`** (the generic currency sign) because the
  operating country is unknown. It is a single value in
  `config/company.php` and should be the **first thing changed** once geography
  is confirmed.
- **The company name "Baardy" is a working title** taken from the project
  directory. Not a verified trading name.
- **The logo is a placeholder mark**, designed to fit the system but not a
  commissioned identity.
- **No `og:image`** — needs 1200×630 brand artwork. Noted as a TODO in the
  layout.
- **Footer legal links point at `#`.** The pages do not exist.
- **Nav links are in-page anchors**, not routes. They become real URLs as pages
  are built.
- `.claude/skills/` and the Boost guidance files were generated by
  `boost:install` and are not hand-maintained.

---

## Lessons learned

- **Alpine's `x-transition` breaks against this project's reduced-motion
  rules.** The global `transition-duration: 0.01ms !important` under
  `prefers-reduced-motion` leaves an `x-show` element stuck at
  `display: block` — the mobile menu never closed. Fixed by using plain
  `x-show`. If a panel will not hide, this is why.

- **Laravel's default `SESSION_DRIVER=database` breaks a DB-free build** with a
  500 on the first request, and the error surfaces as a session query failure
  rather than anything that points at configuration.

- **Generic currency signs need typographic help.** Rendered at full size,
  `¤` reads as a broken glyph. Setting currency symbols at `0.66em` and 55%
  opacity (the `currency` utility) fixes it — and is better typesetting for any
  symbol, so it should stay after the real currency is set.

- **Right-aligned phrases need `whitespace-nowrap` on narrow screens.**
  "Funded in 3 days" broke mid-phrase at 375px and stranded a word on its own
  line.

---

## Future planned modules

Roughly in dependency order. None are started.

1. **Phase 2 — Content pages.** A page per product, About, Contact, and the
   legal pages the footer already links to. Pure front end; no backend needed.
2. **Phase 3 — Application intake.** The first real backend work: database,
   migrations, a multi-step application form, validation, file upload for
   documents. `SECURITY.md` §3, §5 and §11 become mandatory here.
3. **Phase 4 — Authentication and customer portal.** Accounts, application
   status, repayment schedule.
4. **Phase 5 — Staff back office.** Review queue, credit decisioning, audit
   trail, role separation.
5. **Phase 6 — Payments and disbursement.** Provider integration, reconciliation.
6. **Phase 7 — Loan pricing.** Interest and fee engine; only then does the
   calculator get real numbers.

---

## For the next session

Start here:

1. Read this file, then `INSTRUCTIONS.md`.
2. If the work is visual, read `DESIGN.md` before writing markup.
3. If the work touches data, input or auth, read `SECURITY.md` first.
4. **`COMPANY.md` is the bottleneck.** Currency, geography, legal name,
   licensing and real product terms all block work in `SEO.md` and block
   replacing the placeholders. Getting it filled in is the highest-value next
   action.
