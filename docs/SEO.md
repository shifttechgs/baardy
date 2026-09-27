# SEO

Strategy foundation. **This is not an implementation plan for now** — the
current build establishes correct technical groundwork only (see §6). Campaign
work, content production and schema come later.

---

## 1. Target audience

Two distinct groups with different search behaviour:

**Small business owners and traders.** Need working capital for stock, wages,
equipment or a supplier invoice. Often searching on a phone, often urgently,
often after a bank has said no or taken too long. Low tolerance for forms.

**Salaried individuals.** Need credit for school fees, medical costs, a move or
an emergency. More likely to compare a few lenders and to search for specific
amounts and repayment terms.

Both groups are **high-anxiety, high-scepticism**. They are looking for
evidence of legitimacy as much as for a product. Pages that answer "is this
real, what will it cost, and how fast" outrank pages that describe a brand.

---

## 2. Primary search intent

Ranked by commercial value:

1. **Transactional** — "apply for a business loan", "quick loan online",
   "[product] application". Highest intent, lowest volume.
2. **Commercial investigation** — "best SME loans in [location]", "[competitor]
   alternative", "loan requirements", "how much can I borrow".
3. **Informational, high-relevance** — "what documents do I need for a business
   loan", "how does loan repayment work", "what happens if I miss a payment".
   These build the topical authority that makes the transactional pages rank,
   and they are exactly what AI assistants cite.
4. **Navigational** — brand name, branch locations, contact.

The FAQ content already written for the homepage maps directly onto group 3 and
should be expanded into standalone pages.

---

## 3. Core service keywords

Seed clusters to research properly with real volume data before committing.
**Do not treat this list as validated.**

- **Business:** business loan, SME loan, working capital loan, business
  financing, invoice financing, stock financing, equipment loan
- **Personal:** personal loan, salary loan, emergency loan, quick loan, short
  term loan, school fees loan
- **Qualifier modifiers:** fast, same day, online, no collateral, bad credit,
  requirements, calculator, interest rate
- **Comparison:** vs bank loan, alternatives to, best, reviews

**Regulated-sector caution.** Terms like "no credit check", "guaranteed
approval" and "instant cash" attract volume and the wrong customers, breach
responsible-lending rules in most jurisdictions, and invite regulatory
attention. **Do not target them.** Compliance review before any keyword
targeting goes live.

---

## 4. Local SEO

Microfinance is a local business even when the application is online. Trust is
geographic — people want to know there is an office.

- **Google Business Profile** per physical branch: correct category, hours,
  photos, and an active review flow.
- **NAP consistency** — name, address, phone identical everywhere: site,
  GBP, directories, social profiles. Inconsistent NAP is the most common local
  ranking problem.
- Local directory and chamber-of-commerce citations.
- Reviews are the strongest local signal available. Build a request step into
  the post-disbursement flow.
- Embed a map and full address on the contact page; mark up with
  `LocalBusiness`.

**Blocked on `COMPANY.md`:** geography is `[PLACEHOLDER]`. No location strategy
can be finalised until the operating country, cities and branch list are known.

## 5. Location strategy

Once geography is confirmed:

- `/{product}` for the main product pages.
- `/locations/{city}` for each place with a real branch or genuine service
  presence.
- **Only build a location page where there is something real to say** —
  a branch, staff, local requirements, local case studies. Thin templated
  city pages ("business loans in X" × 40 with the town name swapped) are
  doorway pages: they get filtered, and at scale they invite a manual action.
- One page per location, linked from a locations index and from the footer.

---

## 6. On-page rules

Already true of the homepage, and the standard for every page added:

- **One `<h1>`**, describing the page, not the brand.
- No skipped heading levels. Headings describe content; they are not styling.
- Semantic landmarks: `<header>`, `<main>`, `<nav>`, `<footer>`, `<section>`.
- Tabular data in a real `<table>` with a `<caption>` and scoped `<th>`.
- Descriptive link text. Never "click here" or "read more" alone.
- Every meaningful image has real `alt` text; decorative SVG is `aria-hidden`.
- Content in the HTML, not injected by JavaScript. The site is server-rendered
  Blade; **keep it that way** — it is a large part of why it will index well.
- Fast by construction: self-hosted variable font with `font-display: swap`,
  one small CSS file, one small JS file, no CDN round trips, no layout shift
  from late-loading fonts or images.

### Metadata rules

Handled in `resources/views/layouts/marketing.blade.php`; each page sets
`@section('title')` and `@section('description')`.

- **Title:** 50–60 characters. Primary term first, brand last. Unique per page.
- **Description:** 140–160 characters. Written to earn a click — state the
  offer and the differentiator. Unique per page. Not keyword stuffing.
- **Canonical:** self-referencing on every page; already emitted.
- **Open Graph / Twitter:** already emitted. `og:image` is the one gap — needs
  1200×630 brand artwork.
- Never `noindex` a page that should rank; never index thin utility pages.

### Internal linking

- Link from informational content to the relevant product page with descriptive
  anchor text.
- Hub-and-spoke: a product page is the hub; the FAQs and guides around it are
  spokes that link back.
- Every page should be reachable within three clicks of the homepage.
- The footer carries the durable structural links; it is not a keyword dump.
- Keep anchor text varied and natural.

### URL structure

- Lowercase, hyphenated, no file extensions, no query strings for navigation.
- Short and readable: `/business-working-capital`, not `/products?id=3`.
- Stable. If a URL must change, `301` it — never leave a `404`.
- No dates in evergreen URLs.

---

## 7. Schema.org strategy

**Nothing is implemented yet, deliberately.** Emitting structured data built
from placeholder company details would assert unverified facts about a
regulated business directly to search engines. The layout carries a comment
recording this.

Implement in this order, once `COMPANY.md` is filled in:

1. **`Organization` / `FinancialService`** — sitewide, in the layout. Legal
   name, logo, URL, `sameAs` social profiles, contact point, address, area
   served. `FinancialService` is the correct specific type; add
   `LocalBusiness` properties for branches.
2. **`FAQPage`** — on the FAQ page. The questions already written are genuine
   user questions, which is the requirement. Mark up **only** questions that
   are visible on the page.
3. **`BreadcrumbList`** — once there is a hierarchy deeper than one level.
4. **`Product` / `Offer`** — for loan products, **only once real terms exist**.
   Publishing an invented rate or amount in structured data is worse than
   publishing it in prose: it is machine-readable and it propagates.
5. **`Review` / `AggregateRating`** — **only** from verified, consented
   customer reviews. Self-serving review markup is a manual-action risk and,
   for a lender, a consumer-protection issue.

Validate with Google's Rich Results Test before shipping any of it.

---

## 8. Sitemap

- `sitemap.xml` at the root, generated rather than hand-written, once there is
  more than one page.
- Include only canonical, indexable `200` URLs. Exclude utility pages.
- Accurate `lastmod`. Omit `priority` and `changefreq` — they are ignored.
- Split by type (pages, locations, articles) when it exceeds a few hundred URLs.
- Reference it from `robots.txt` and submit via Search Console.

## 9. robots.txt

- Allow crawling of everything public.
- Disallow application flows, account areas, and any URL carrying a token or
  personal parameter — these must never be indexed.
- Reference the sitemap.
- **Do not use `robots.txt` to hide a page from search.** Disallow prevents
  crawling, not indexing; use `noindex` for that.
- Staging must be fully disallowed *and* HTTP-authenticated. A crawlable
  staging site competing with production is a common and avoidable problem.

---

## 10. AI search and answer engines (AEO)

A growing share of "how do loans work" questions are answered by an assistant
rather than a results page. Optimising for citation is now part of the job.

- **Answer the question in the first two sentences**, then elaborate. Assistants
  extract passages, not pages.
- Write **self-contained passages**. A paragraph that only makes sense after
  reading the three above it will not be quoted.
- Use real question-shaped headings (`How long does approval take?`), matching
  how people actually ask.
- Be specific and factual. Assistants cite sources that state concrete things;
  marketing abstraction is not quotable.
- Keep content in server-rendered HTML — many crawlers do not execute
  JavaScript.
- Clean semantic structure and correct `FAQPage` markup both help extraction.
- Decide a position on `llms.txt` and on which AI crawlers are allowed. This is
  a business decision, not a technical default.
- **Brand mentions off-site matter more than links here.** Assistants
  synthesise from what the wider web says about the company — directory
  listings, press, review platforms, forum answers.

## 11. Generative engine optimisation (GEO)

- Keep facts consistent everywhere. Contradictory figures across the site,
  GBP and directories reduce the confidence a model has in citing you.
- Publish the things a model needs to answer well: eligibility criteria,
  required documents, the process, the fee structure, worked repayment
  examples — once they are real.
- Structured data and plain factual prose reinforce each other.
- Monitor what assistants currently say about the brand and the category, and
  treat wrong answers as a content gap.
- Expect less referral traffic per impression. Measure branded search volume
  and direct traffic, not only clicks.

---

## 12. Content strategy

Sequence, once geography and products are confirmed:

1. **Homepage** — done, conversion-focused.
2. **A page per product.** The four facilities each deserve a full page:
   eligibility, documents, worked example, FAQs, application CTA. These are the
   primary commercial landing pages.
3. **The trust pages.** About, team, branches, licensing, responsible lending,
   complaints procedure, privacy. These exist for conversion and E-E-A-T as
   much as for ranking — in a regulated sector, visible credentials are a
   ranking factor in practice.
4. **The document and process guides.** "What documents do I need", "how
   repayment works", "what happens if you miss a payment". Highest AEO value.
5. **Comparison and decision content.** Loan vs overdraft, choosing a term.
6. **Local pages**, where there is a real presence.
7. **Customer stories**, once consented.

### Standards

- **E-E-A-T is load-bearing here.** Lending is YMYL ("your money or your
  life") — Google holds it to a higher standard. Named authors with real
  credentials, a review date, visible licensing, a real address and a real
  phone number all count.
- Write for the borrower, not the algorithm.
- Never publish an unverified figure. Everything currently on the site is
  labelled illustrative for exactly this reason.
- Review dated content on a schedule; stale rates are a compliance risk, not
  just an SEO one.
- Compliance review before publishing anything describing cost or eligibility.

---

## 13. Measurement

Set up before content work begins, so there is a baseline:

- Google Search Console — coverage, queries, Core Web Vitals.
- Analytics, configured **without** sending PII, and never passing an
  application id or personal parameter into an event.
- Track the real conversion: applications started and completed, not sessions.
- Monitor branded vs non-branded search separately.

---

## 14. Blocked on COMPANY.md

None of the following can be completed until the placeholders are resolved:

- Geography and therefore all local strategy and location pages
- Real product terms, and therefore `Product`/`Offer` schema
- Licence and regulator details, and therefore `FinancialService` schema and
  the trust pages
- The legal entity name used in `Organization` schema
- Currency, which appears in every figure on the site
