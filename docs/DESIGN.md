# DESIGN

The visual language of the site, in enough detail to reproduce it.

Everything here is declared in `resources/css/app.css`. That file is the
implementation; this document is the reasoning.

---

## 1. Design inspiration

The reference point is **Ascone Finance** — for its editorial rhythm, generous
whitespace and restraint, not its visuals. The target register is closer to
Stripe and premium private banking than to a typical microfinance site.

It is **inspiration, not a template**. Nothing was copied.

---

## 2. Brand philosophy

The site has one job: convince someone that this company can be trusted with a
decision about their money.

That produces three commitments:

1. **Calm over loud.** No gradient washes, no floating glass cards, no
   decorative motion. A lender that shouts looks like a lender with something
   to hide.
2. **Documents, not dashboards.** The visual vocabulary is the one finance
   already has — ruled tables, term sheets, statements, figures set in columns.
   It is more credible than an abstract fintech illustration because it is what
   the business actually produces.
3. **Say the true thing plainly.** Every unverified number is labelled.
   Typography does the persuading; the copy does not oversell.

### The organising idea: a ruled page

Structure is carried by **hairline rules**, not by shadows or by chopping
content into identical cards:

- Sections are separated by a full-width rule, not by alternating backgrounds.
- Card groups are built as **ruled grids** — a table, not a row of tiles.
- Rules also divide items *within* a block: process steps, ledger rows.
- No shadows. Cards are flat and corners are near-square (2px).

The section rule is on by default and off in three places: the hero (nothing
above it), the closing CTA (an inset dark panel that separates itself) and the
products rail (the pinned track sets its own top edge).

This is the thing that makes the page look like a financial document rather
than a SaaS landing page. Preserve it.

---

## 3. Typography

### Typeface

**Geist** (Vercel), one family, no second face.

Installed via the `geist` npm package and **self-hosted** as a variable font.
It is loaded from our own origin rather than a font CDN: a page that will later
collect financial details should not make a third-party request, and a
self-hosted font removes an external point of failure.

```css
@font-face {
    font-family: 'Geist';
    src: url('../../node_modules/geist/dist/fonts/geist-sans/Geist-Variable.woff2')
         format('woff2-variations');
    font-weight: 100 900;
    font-display: swap;
}
```

Geist Mono ships in the same package but is **not loaded**. If a future screen
needs it (account numbers, references), add the `@font-face` and a `--font-mono`
token then — not before.

### Weights

Only three are used. Geist is a variable font, so any weight is available; the
restraint is deliberate.

| Weight | Used for |
|---|---|
| 400 | Body copy, descriptions, notes |
| 500 | Headings, figures, buttons, labels, navigation |
| 600 | The wordmark only |

Headings are **500, not 700**. At display sizes Geist's medium weight is
authoritative without being heavy, and bold headlines read as promotional.

### Type scale

Fluid, using `clamp()`, so there are no breakpoint jumps in type size.

| Token | Min → Max | Line height | Tracking | Role |
|---|---|---|---|---|
| `text-display` | 38 → 68px | 1.02 | −0.035em | Page `<h1>` |
| `text-h2` | 28 → 44px | 1.12 | −0.025em | Section headings |
| `text-h3` | 19 → 23px | 1.3 | −0.015em | Card and step headings |
| `text-lead` | 17 → 20px | 1.6 | inherit | Section intros, pull quotes |
| `text-body` | 16px | 1.65 | −0.005em | Body copy |
| `text-small` | 14px | 1.55 | −0.005em | Labels, table cells, meta |
| `text-micro` | 13px | 1.4 | −0.005em | Disclosures, legal, notes |

**Tracking tightens as size grows.** This is the single most important
typographic rule here — large type set at default tracking is the clearest
sign of an undesigned page.

`text-wrap: balance` is applied to all headings so they never leave an orphan.

### Measure

Body copy is capped at roughly **65–70 characters** (`max-w-xl` / `max-w-2xl`).
The `<h1>` is capped at `20ch` so it breaks into two or three deliberate lines
rather than one long one.

### Financial figures

Every number uses the `figure-nums` utility:

```css
font-variant-numeric: tabular-nums;   /* columns align; values do not jump width */
letter-spacing: -0.02em;
```

Currency symbols use the `currency` utility — **0.66em and 55% opacity**. The
number is the information; the symbol is the unit, and it should not compete.
Opacity rather than a colour token, so it works unchanged on the dark foot of
the page.

### Things this design does not do

Deliberately avoided, because they are the common tells of a generic page:

- All-caps tracked-out eyebrow labels. Section labels are **sentence case**.
- Accenting one word of a headline in a different colour or weight.
- Appending `→` to link and button text.
- A monospace face for small labels.

---

## 4. Colour

Six core tokens. One accent. No secondary brand colour.

| Token | Hex | Role |
|---|---|---|
| `--color-paper` | `#ffffff` | Primary surface |
| `--color-mist` | `#f6f4f8` | The one tinted band; summary panels |
| `--color-ink` | `#151019` | Headings, primary text |
| `--color-ink-soft` | `#413b4a` | Secondary text, nav links |
| `--color-muted` | `#676070` | Body copy, labels, notes |
| `--color-line` | `#e7e4ec` | Hairline rules, card borders |
| `--color-line-strong` | `#d3cedb` | Emphasised rules, control borders |
| `--color-accent` | `#5e2681` | The single brand colour |
| `--color-accent-strong` | `#4a1d68` | Accent hover |
| `--color-accent-tint` | `#f5f0f9` | Accent background wash |
| `--color-highlight` | `#f16a26` | The logo's orange. See the warning below. |
| `--color-highlight-tint` | `#fef2ea` | Highlight wash |
| `--color-ink-line` | `#2e2637` | Rules on the dark foot |
| `--color-ink-muted` | `#a79fb3` | Secondary text on the dark foot |

### Why purple

The accent is **taken from the client's logo**, sampled from the artwork rather
than chosen: the wordmark is `#5E2681` across 28,005 pixels, so there is nothing
to interpret. The mark's gradient also runs through `#652482` and `#253F93`, and
its centre disc is `#F16A26`.

The site was originally built on a deep forest green, chosen to avoid the
default fintech indigo. The logo settled the question — a green site under a
purple mark reads as two brands.

The near-black `--color-ink` carries a faint violet cast, and the greys with it,
so text and accent belong to the same family rather than sitting as
black-plus-a-colour.

**The orange is a highlight, not a second accent.** At `#f16a26` it measures
only **3.07:1** on paper: usable for large type, an icon or a fill, and **never
for body copy, labels or small text**. It is currently defined but unused. Spend
it on the single figure the eye should land on first, and nowhere else.

### Rebranding

Change the `--color-accent*` trio and, if needed, the violet cast in `ink`,
`mist` and the `line` pair. Nothing else should need touching — no component
contains a raw hex value.

### Contrast (measured, WCAG 2.1)

| Foreground | Background | Ratio | |
|---|---|---|---|
| `ink` | `paper` | **18.75:1** | AAA |
| `ink-soft` | `paper` | **10.77:1** | AAA |
| `muted` | `paper` | **6.03:1** | AA |
| `muted` | `mist` | **5.52:1** | AA |
| `accent` | `paper` | **10.14:1** | AAA |
| `accent` | `accent-tint` | **9.04:1** | AAA |
| `paper` | `accent` | **10.14:1** | AAA |
| `paper` | `ink` | **18.75:1** | AAA |
| `ink-muted` | `ink` | **7.37:1** | AAA |
| `highlight` | `paper` | **3.07:1** | Large text only |

`muted` is the lightest text colour in the system. **Do not introduce anything
lighter for text.** `line` and `line-strong` are for rules and borders only —
they are far below text contrast and must never carry type.

---

## 5. Spacing and rhythm

Tailwind's 4px base scale, used through utilities.

### Section padding

| Breakpoint | Default | Tight |
|---|---|---|
| Mobile | `py-20` (80px) | `py-14` (56px) |
| `sm` (≥640) | `py-28` (112px) | `py-16` (64px) |
| `lg` (≥1024) | `py-40` (160px) | `py-20` (80px) |

Generous section padding is what makes the page feel expensive. Resist
compressing it.

### Internal spacing

- Section header → content: `mt-14`, `lg:mt-20`
- Card padding: `p-8`, `lg:p-12`
- Stat cell padding: `px-6 py-8`, `lg:py-10`
- Between grid items: `gap-x-10 gap-y-12`

Use `gap` for sibling spacing, not margins.

---

## 6. Container and grid

```css
--container-content: 1200px;
```

`<x-ui.container>` is the single horizontal measure for the whole site:

```
mx-auto w-full max-w-content px-6 sm:px-10 lg:px-16
```

Gutters: **24px mobile → 40px tablet → 64px desktop.**

### Grid

A 12-column grid on `lg` and up. The recurring arrangement is **asymmetric**,
which is what gives the page its editorial feel:

| Pattern | Columns |
|---|---|
| Section header | label `1–3`, heading + intro `4–12` |
| Hero | lead + CTAs `1–5`, ledger `7–12` |
| Process step | number `1–2`, content `3–9`, timing `10–12` |
| FAQ | heading `1–4`, questions `6–12` |
| Closing CTA | copy `1–7`, actions `9–12` |

Below `lg`, everything stacks to a single column. Card grids go 4 → 2 → 1 or
3 → 1 depending on content density.

Content is **left-aligned throughout**. There is no centred text anywhere on
the page — centred blocks read as a template.

---

## 7. Border radius

Four steps, each with a fixed job. Do not mix arbitrarily.

| Token | Value | Used on |
|---|---|---|
| `rounded-xs` | 4px | Badges, dots, focus rings, inline links |
| `rounded-sm` | 8px | Buttons, inputs, selects, segmented controls |
| `rounded-md` | 14px | Cards and panels |
| `rounded-lg` | 22px | Feature panels only — the hero ledger, the calculator |

Radius encodes scale: the larger the surface, the larger the radius.

---

## 8. Borders

The primary structural device.

- **Hairline, always 1px.** No 2px borders anywhere.
- `--color-line` for standard rules; `--color-line-strong` where a rule needs
  to read as a boundary (control borders, the process list).

### The ruled grid

Card groups use a 1px `gap` over a line-coloured background, so the parent
shows through as interior rules:

```blade
<x-ui.rule-grid cols="sm:grid-cols-2">
    <x-ui.rule-cell>…</x-ui.rule-cell>
</x-ui.rule-grid>
```

```
grid gap-px border-y border-line bg-line   ← parent
bg-paper p-8 lg:p-10                        ← each cell
```

This guarantees every interior rule is exactly one pixel and no border is ever
doubled. Prefer it over bordered cards.

---

## 9. Shadows

Two, and they are the only two.

| Token | Value | Used on |
|---|---|---|
| `--shadow-bar` | `0 1px 0 0 rgb(14 21 18 / .06)` | Sticky header, scrolled |
| `--shadow-panel` | `0 1px 2px rgb(14 21 18 / .04), 0 12px 32px -12px rgb(14 21 18 / .10)` | The hero ledger only |

**Do not add a third.** If something needs separating, it needs a border.

---

## 10. Buttons

`<x-ui.button>` renders an `<a>` when given `href`, a `<button>` otherwise.

| Variant | Appearance | Use |
|---|---|---|
| `primary` | Accent fill, paper text | The one main action |
| `secondary` | Paper, `line-strong` border | The alternative action |
| `ghost` | No fill, mist on hover | Tertiary, in-chrome |
| `inverse` | Paper fill, ink text | On the dark foot |

| Size | Height | Padding | Type |
|---|---|---|---|
| `md` | 44px | `px-5` | 14px |
| `lg` | 52px | `px-7` | 16px |

Only `md` and `lg` exist — both clear the 44px touch target.

**Label rules:** say what happens ("Start an application", not "Submit"); keep
the same wording through a flow; sentence case; no trailing arrow.

---

## 11. Cards and panels

Cards are `rounded-md`, `border-line`, `bg-paper`, `p-8 lg:p-12`, **no shadow**.

Where cards form a group, use a ruled grid (§8) rather than separated cards.

The hero ledger and the calculator are **feature panels**: `rounded-lg`, one
border, and — for the ledger only — `shadow-panel`. Nothing else gets this
treatment; it is what marks them as the page's focal objects.

---

## 12. Forms

- Controls are 44–48px tall, `rounded-sm`, `border-line-strong`.
- Every control has a real `<label>` via `<x-ui.field>`, which wires `for`/`id`
  and links hints with `aria-describedby`.
- Grouped choices use `<fieldset>` + `<legend>`. The term selector is a radio
  group styled as segmented buttons — it looks like buttons but keeps arrow-key
  navigation and correct announcement.
- The range input is the one control needing real CSS, since its track and
  thumb are shadow-DOM pseudo-elements. It still uses tokens.
- Focus comes from the global rule. Do not style focus per control.

---

## 13. Navigation

Sticky, `h-18` (72px), `bg-paper/85` with a backdrop blur. The bottom rule
appears only once the page has scrolled, so the header is borderless at rest.

Desktop: wordmark left, links centre-left, phone number and one primary action
right. **One button, not two** — a second button here only repeats a link
already in the nav, and for a first-time borrower the phone number converts
better.

Mobile: a full-width panel below the header. It sets `aria-expanded` and
`aria-controls`, closes on Escape with focus returned to the trigger, and
closes when a link is chosen.

---

## 14. The hero

The page's one bold moment, spent on two things:

1. **The headline** — display size, tight tracking, capped at `20ch`.
2. **The ledger** — a real `<table>` of recent facilities.

The ledger is the deliberate alternative to a product screenshot or an abstract
illustration. A record of money moving is the artefact a lender produces, and
it makes the headline's promise concrete. It is marked up as a table because it
*is* tabular data.

Everything around these two elements stays quiet.

---

## 15. Section layout

Standard shape:

```blade
<x-ui.section id="…">
    <x-ui.container>
        <x-ui.section-header label="…" heading="…">
            Optional intro.
        </x-ui.section-header>

        {{-- content, mt-14 lg:mt-20 --}}
    </x-ui.container>
</x-ui.section>
```

`<x-ui.section-header>` is the asymmetric two-column header — section name in a
narrow left column, heading and intro in a wider right column. The offset *is*
the editorial device. Do not centre it.

### Tonal structure

Almost the entire page is on paper, divided by rules. There are exactly two
departures:

- `mist` on the How-it-works band and the calculator summary. Testimonials
  used to carry it too; it moved to sit directly above How-it-works, and two
  tinted bands back to back merge into one.
- `mist` on the Get-in-touch band, which holds the enquiry form.
- **Brand purple** on the closing CTA, and **ink** on the footer.

The closing CTA is an **inset panel**, not a full-bleed band: `rounded-xl`,
split with a flat, unfiltered photograph (no glows or gradients),
held inside the container, with paper visible around it. It was originally
full-bleed and continuous with the footer; that was changed at the client's
request so the closing ask reads as an object on the page rather than as the
top of the footer. The footer itself remains full-bleed.

Alternating light/dark bands section by section is still what makes a page look
generic — these two remain the only tonal departures.

---

## 16. Responsive rules

Mobile-first. Verified at **1440, 1280, 1024, 768, 430, 390 and 375px**, with
no horizontal scrolling at any width.

| Breakpoint | Behaviour |
|---|---|
| `< 640` | Single column. 24px gutters. Buttons full width, stacked. Mobile nav. |
| `sm ≥ 640` | Card grids to 2 columns. 40px gutters. Buttons inline. |
| `lg ≥ 1024` | 12-column asymmetric grid. 64px gutters. Desktop nav. Full section padding. |

Guards in place:

- `overflow-x: clip` on `<body>` — `clip` rather than `hidden`, which would
  break the sticky header.
- Long right-aligned phrases in the ledger are `whitespace-nowrap`, so they
  cannot break mid-phrase and strand a word.
- Fluid type means no size jumps at breakpoints.

---

## 17. Motion

**One orchestrated moment: the hero settles on load.** Three elements rise and
fade in with a 90ms stagger, using `cubic-bezier(0.16, 1, 0.3, 1)`.

Nothing else on the page animates on scroll. Per-section scroll reveals and
hover lifts on every card are the clearest sign of a generated page.

Beyond that, motion only ever answers a user action — a hover colour change, a
menu opening, an accordion expanding — and stays at 150ms.

`prefers-reduced-motion: reduce` disables all of it globally, including smooth
scrolling.

---

## 18. Accessibility principles

Built in, not retrofitted. See `INSTRUCTIONS.md` §10 for the working checklist.

Design-level commitments:

- **One focus style, globally.** A 2px accent outline with 3px offset, defined
  once on `:focus-visible`, so it cannot drift between components.
- **Prefer native elements.** The FAQ is native `<details>` with a shared
  `name` for exclusivity — keyboard-correct, screen-reader-correct, works
  before JavaScript loads, and findable by in-page search. No ARIA needed at
  all.
- **Never encode meaning in colour alone.** The selected loan term has a
  border change and a real checked radio, not just a tint.
- **Touch targets ≥ 44px.**
- **Decorative SVG is `aria-hidden`;** icon-only controls carry `sr-only` text.
- **Contrast is a design constraint,** not a review finding — see §4.
