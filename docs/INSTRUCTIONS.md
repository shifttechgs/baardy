# INSTRUCTIONS

How this project is built and how to work in it. Read this before making
architectural or major design changes.

> **Rule.** Before making architectural or major design changes, inspect the
> relevant documentation in `/docs`. If a change contradicts something written
> here, update the document in the same commit — or raise it rather than
> quietly diverging.

---

## 1. Project purpose

A public marketing website for a microfinance / SME lending business. The site's
job is to make a visitor confident enough to start a loan application, and to
tell them the truth about cost and process before they commit.

The current build is **Phase 1 — Frontend Foundation**. It is a visual and
architectural base, not a working product. See `MEMORY.md` for status.

---

## 2. Technology stack

| Layer | Choice | Notes |
|---|---|---|
| Framework | Laravel 13 | PHP 8.4 |
| Templating | Blade | Anonymous components, no view models yet |
| CSS | Tailwind CSS v4 | CSS-first config via `@theme`; there is no `tailwind.config.js` |
| Build | Vite 8 | `laravel-vite-plugin` |
| JS | Alpine.js 3 | Two uses only — see §7 |
| Fonts | Geist (`geist` npm package) | Self-hosted variable font |

Session, cache and queue drivers are set to `file` / `sync`. **The application
does not connect to a database.** That is deliberate, not an oversight.

---

## 3. Development principles

1. **The design system is the source of truth.** Colour, spacing, radius,
   shadow and type are declared once in `resources/css/app.css`. Do not
   introduce a raw hex value, a one-off font size, or a new radius in a
   template.
2. **Structure with borders, not shadows.** Two shadows exist in the whole
   system. Reach for a hairline rule first.
3. **Do not fake functionality.** No invented repayment figures, no mock API
   responses, no placeholder that could be mistaken for real behaviour. Where
   something is not built, say so in the interface.
4. **Placeholder data must be visibly labelled.** Every unverified figure on
   the page carries an `<x-ui.disclosure>` saying it is illustrative. Removing
   one is a decision, not a cleanup.
5. **Accessibility is part of "done", not a later pass.** See §10.
6. **Prefer the platform.** Native `<details>` over a JS accordion, a real
   `<table>` for tabular data, a real `<label>` for every control. Reach for
   Alpine only when the platform genuinely has no answer.
7. **Small files.** If a Blade file is getting long, it wants to be a component
   or a section partial.

---

## 4. Folder structure

```
config/
├── company.php          Identity, contact, navigation, compliance placeholders
└── marketing.php        All homepage copy and placeholder figures

resources/
├── css/app.css          Design tokens + the few things utilities cannot do
├── js/app.js            Alpine bootstrap, nothing else
└── views/
    ├── layouts/
    │   └── marketing.blade.php    Document head, SEO tags, header + footer
    ├── components/
    │   ├── ui/                    Reusable interface primitives
    │   └── layout/                Navbar, footer
    ├── sections/home/             One file per homepage section
    └── pages/
        └── home.blade.php         Composes the sections

docs/                    This documentation system
```

### Where things belong

- **Copy or a figure** → `config/marketing.php`. Never hardcode copy in a
  template.
- **Identity, contact, navigation, legal links** → `config/company.php`.
- **A reusable visual primitive** → `resources/views/components/ui/`.
- **A one-off arrangement of primitives for a page** →
  `resources/views/sections/`.
- **A design token** → the `@theme` block in `resources/css/app.css`.

---

## 5. Component conventions

Components are **anonymous Blade components** — a single `.blade.php` file, no
class. Add a class-based component only when it needs real logic.

Every component starts with a comment block stating what it is for and, where
the choice is not obvious, *why it is built that way*. Then `@props`.

```blade
{{--
    What this is, and why it exists in this form.

    Props
      variant  'primary' | 'secondary'
--}}
@props(['variant' => 'primary'])

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
```

Rules:

- **Always use `$attributes->merge()`** so callers can add classes and ARIA
  attributes without editing the component.
- **Give every prop a default** unless it is genuinely required.
- **Render the right element.** `<x-ui.button>` emits an `<a>` when it has an
  `href` and a `<button>` otherwise. Do not make a `<div>` clickable.
- **Do not put colour decisions in a section.** If a section needs a colour a
  component does not offer, add the variant to the component.

### Naming

| Thing | Convention | Example |
|---|---|---|
| Component file | kebab-case | `section-header.blade.php` |
| Component usage | dot namespace | `<x-ui.section-header>` |
| Section partial | kebab-case, matches its anchor | `how-it-works.blade.php` |
| Section anchor id | kebab-case | `id="how-it-works"` |
| Config key | snake_case | `best_for` |
| CSS token | semantic, not literal | `--color-ink`, not `--color-dark-green` |

Tokens are named for **role**, not appearance, so rebranding does not leave
`--color-green` holding a blue.

---

## 6. Adding a new page

1. Add the route to `routes/web.php`. Use `Route::view` for a static page; add
   a controller when it needs data that does not belong in config.
2. Create `resources/views/pages/<name>.blade.php` extending
   `layouts.marketing`.
3. Set `@section('title')` and `@section('description')`. Both have sensible
   fallbacks, but a page-specific description is worth writing.
4. Build the page from `@include('sections.<page>.<section>')` partials.
5. Put the copy in `config/marketing.php`.

---

## 7. JavaScript

Alpine is used in exactly two places:

- **`components/layout/navbar.blade.php`** — mobile panel open state and the
  scrolled state of the header.
- **`sections/home/calculator.blade.php`** — input state only. It echoes the
  selected amount and term. **It does not calculate anything.**

Do not add a third use without a reason that the platform cannot meet.

**Known trap:** `x-transition` on `x-show` breaks when the project's
`prefers-reduced-motion` rules override `transition-duration` — the element
stays at `display: block` and never hides. Use plain `x-show`.

---

## 8. What must NOT be changed without approval

1. **No database connection.** No migrations, models, Eloquent, or DB-backed
   session/cache drivers.
2. **No authentication or user accounts.**
3. **No repayment or interest calculation, anywhere, client or server.**
   Publishing an invented figure on a lending site is a regulatory problem.
4. **No payment integration.**
5. **Do not remove a `<x-ui.disclosure>`** or any "illustrative" label unless
   the figure beside it has been replaced with verified data.
6. **Do not fill in the compliance placeholders** in `config/company.php` with
   guessed values. Licence and registration numbers come from the client.
7. **Do not publish the placeholder testimonials** as real customer quotes.
8. **Do not add `tailwind.config.js`.** v4 config is CSS-first.
9. **Do not introduce React, Vue or Inertia.**

---

## 9. How to approach future tasks

1. Read the `/docs` file that covers the area you are touching.
2. Check whether a component already exists before writing markup.
3. Check whether a token already exists before writing a value.
4. Make the change in the smallest correct place — token, component, section,
   in that order of preference.
5. Run the checks in §11.
6. Update `MEMORY.md` if you made a decision a future developer could
   accidentally reverse.

---

## 10. Accessibility baseline

Non-negotiable, and already true of the current build:

- Semantic HTML: one `<h1>` per page, no skipped heading levels, real `<table>`
  for tabular data, `<ol>` for sequences, `<fieldset>`/`<legend>` for grouped
  controls.
- Every form control has an associated `<label>`; hints link via
  `aria-describedby`.
- Visible focus on everything interactive — a single global `:focus-visible`
  rule, so it cannot drift between components.
- Disclosure widgets set `aria-expanded` and `aria-controls`, close on Escape,
  and return focus to their trigger.
- A skip link to `#main`.
- `prefers-reduced-motion` is respected globally.
- Body text meets WCAG AA against its background — see `DESIGN.md` for the
  measured ratios.
- Decorative SVG is `aria-hidden`; meaningful icons get text alternatives.

---

## 11. Definition of done

A change is done when all of the following are true.

- [ ] It uses existing tokens and components; anything new is documented.
- [ ] `npm run build` succeeds.
- [ ] The page renders without error: `php artisan serve`, load the route.
- [ ] `vendor/bin/pint --test` passes (PHP formatting).
- [ ] `php artisan test` passes.
- [ ] Checked at 1440, 1280, 1024, 768, 430, 390 and 375px. No horizontal
      scrolling at any width.
- [ ] Keyboard-only pass: every interactive element reachable, focus visible,
      nothing trapped.
- [ ] Heading hierarchy still correct.
- [ ] No new placeholder figure without a visible disclosure.
- [ ] No hardcoded copy in a template.
- [ ] `/docs` updated if a documented decision changed.
