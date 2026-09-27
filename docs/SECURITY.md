# SECURITY

The security baseline for this project.

**Nothing in this document is implemented yet.** The current build is a static
marketing site with no database, no authentication and no user input handling
(see `MEMORY.md`). This is the standard to meet *before* any of that is added —
written now, while it is cheap, rather than after a breach.

A lending business holds identity documents, income records, bank details and
credit decisions. A compromise here is not an outage; it is a disclosure of the
most sensitive information customers have.

---

## 1. Secrets

- **Never commit a secret.** No API keys, passwords, tokens, certificates or
  connection strings in the repository — including in comments, tests,
  seeders, fixtures or documentation.
- `.env` is git-ignored and must stay that way. `.env.example` carries keys
  with empty or obviously-fake values only.
- `APP_KEY` is generated per environment and never shared between them.
- Secrets in production come from the platform's secret store or environment,
  not from a file in the repo.
- If a secret is ever committed: **rotate it first**, then remove it from
  history. Rewriting history alone is not remediation — assume it is public.
- Never log a secret, a full card number, a national ID, or a password.

## 2. Environment configuration

Production must have:

```
APP_ENV=production
APP_DEBUG=false          # a debug page leaks env vars, paths and stack traces
APP_URL=https://…        # real scheme and host, for correct absolute URLs
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true     # once sessions carry anything about a person
```

- **`APP_DEBUG=true` in production is a critical incident.** Laravel's error
  page renders environment variables.
- Run `php artisan config:cache` in production, and never commit the cache.
- Keep per-environment credentials separate. Staging must not hold production
  data — if real customer data is ever needed for testing, anonymise it first.

## 3. Authentication (when it is built)

- Use Laravel's first-party authentication. Do not hand-roll it, and do not
  invent a password hashing scheme — `bcrypt`/`argon2` via Laravel's `Hash`.
- Enforce a minimum password length (12+) and check against a breached-password
  list. Do not impose composition rules that push people towards `P@ssw0rd1`.
- Rate-limit and throttle login, password reset, OTP entry and any application
  submission endpoint.
- Always `session()->regenerate()` on login and `invalidate()` on logout, to
  close session fixation.
- Offer MFA for staff accounts before customer accounts, and require it for
  anyone who can view customer files or approve a facility.
- Password reset tokens: single-use, short expiry, and the response must not
  reveal whether an address is registered.
- Sessions should be short for staff, and a re-authentication step should guard
  sensitive actions (viewing full bank details, approving a disbursement).

## 4. Authorization

- **Deny by default.** Every route that is not deliberately public requires
  authentication.
- Use Policies and Gates. Authorize on the *object*, not just the route — a
  logged-in borrower must not be able to read another borrower's application by
  changing an id.
- Insecure direct object reference is the most likely bug in a lending
  portal. Scope every query to the authenticated user (`$user->applications()`),
  never `Application::find($id)` on a request parameter.
- Prefer UUIDs or ULIDs over sequential integers in customer-facing URLs, so
  record counts and identifiers are not enumerable.
- Staff roles need real separation: an agent who can *view* an application
  should not be able to *approve* one.
- Log every privileged action with actor, target, timestamp and outcome.

## 5. Input validation

- Validate **every** request, in a Form Request class. Validation is a
  whitelist of what is allowed, not a blacklist of what is not.
- Validate type, format, range and business rules — a loan amount is a positive
  integer within the product's published bounds, not just "numeric".
- Never trust a hidden field, a client-side check, or a value that arrived in
  JavaScript. Re-derive prices, limits and eligibility on the server.
- Guard mass assignment: keep `$fillable` explicit; never `$guarded = []` on a
  model that holds financial or identity fields.
- Normalise before validating (trim, canonicalise phone numbers), and store in
  one canonical format.

## 6. Output escaping and XSS

- Use `{{ }}`, which escapes. **`{!! !!}` is the exception, not a convenience** —
  every use needs a comment justifying it and a guarantee the content is not
  user-supplied.
- Never interpolate user data into a `<script>` block. Pass data as JSON via
  `@json()` / `Js::from()`.
- Never build an `href` from unvalidated user input — `javascript:` URLs.
- Sanitise any rich text server-side with a well-maintained HTML purifier and a
  strict allow-list. Do not write your own sanitiser.
- Add a Content-Security-Policy (§10). Self-hosting the font and keeping
  Alpine bundled rather than CDN-loaded means a strict `self` policy is already
  achievable — **do not add a CDN script tag and give that up.**

## 7. CSRF

- Laravel's CSRF middleware is on by default. Every state-changing form needs
  `@csrf`.
- **Do not add routes to the CSRF exception list** to make something work. If a
  webhook needs to be excluded, authenticate it by signature instead.
- Use `SameSite=Lax` (the default) or `Strict` for session cookies.
- State-changing actions are `POST`/`PATCH`/`DELETE`, never `GET`.

## 8. SQL injection

- Use Eloquent and the query builder with bindings. They parameterise.
- `DB::raw()`, `whereRaw()`, `orderByRaw()` and friends are where injection
  happens. If raw SQL is genuinely needed, **bind every parameter** — never
  concatenate a request value into the string.
- A user-supplied sort column or direction must be checked against an
  allow-list, not passed through.
- The application's database user should have only the privileges it needs —
  not `DROP`, not `GRANT`, and ideally not schema modification in production.

## 9. Rate limiting

- Throttle by route *and* by identity. Apply to: login, registration, password
  reset, OTP, loan application submission, document upload, search, and any
  endpoint that sends an SMS or email (each one costs money).
- Rate-limit the quote/eligibility endpoint when it exists — it is the obvious
  target for scraping the lending model.
- Return `429` with `Retry-After`. Log repeated hits; sustained throttling is a
  signal worth alerting on.
- Put an edge/WAF layer in front in production. Application-level throttling
  does not stop a volumetric attack.

## 10. Secure headers

Set at the edge or in middleware:

| Header | Value |
|---|---|
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains; preload` |
| `Content-Security-Policy` | Start `default-src 'self'`; avoid `unsafe-inline` |
| `X-Content-Type-Options` | `nosniff` |
| `Referrer-Policy` | `strict-origin-when-cross-origin` |
| `X-Frame-Options` | `DENY` (or CSP `frame-ancestors 'none'`) |
| `Permissions-Policy` | Disable camera, microphone, geolocation unless needed |

- HTTPS everywhere; redirect HTTP; no mixed content.
- Cookies: `Secure`, `HttpOnly`, `SameSite`.
- **Clickjacking matters here specifically** — a framed "approve this facility"
  button is a real attack, hence `DENY`.

## 11. File uploads

Loan applications mean ID documents, payslips and bank statements — the highest
-value data in the system.

- Validate MIME type by **inspecting content**, not by trusting the extension or
  the `Content-Type` header. Keep a strict allow-list (PDF, JPEG, PNG).
- Enforce a maximum size, and a maximum count per application.
- **Never** store uploads in `public/`. Use private storage and serve through
  an authorized controller or a short-lived signed URL.
- Generate your own filename. Never use the client's — path traversal, null
  bytes, overlong names.
- Store outside the web root; ensure the storage path cannot execute PHP.
- Scan for malware before a staff member opens a file.
- Strip EXIF/location metadata from images.
- Set a retention period and delete on schedule.

## 12. Logging

- Log authentication events, authorization failures, privileged actions, and
  application state changes — with actor, timestamp, and outcome.
- **Never log:** passwords, tokens, session ids, full bank account or card
  numbers, national ID numbers, OTP codes, or full request bodies from
  application forms. Redact at the logger, not at the call site.
- Do not put PII in log messages used for debugging. Log the record id.
- Protect logs as sensitive data: access-controlled, retention-limited,
  shipped off-host so they survive a compromise.
- Monitor for: repeated authorization failures, throttle trips, and unusual
  volumes of document access by a single staff account.

## 13. Sensitive financial data

- **Do not store card numbers.** Use a payment provider's tokenisation. Being
  out of PCI-DSS scope is worth more than any convenience.
- Encrypt bank account numbers and national identifiers at rest — Laravel's
  `encrypted` cast, with a documented key rotation plan.
- Never place an account number, an identity number, or an application id in a
  URL, a query string, a redirect, or an analytics event.
- Money is stored as **integer minor units**, never a float. Round-off in
  interest or repayment arithmetic is a customer dispute.
- Every credit decision needs an immutable audit trail: inputs, rule version,
  outcome, who approved it. Regulators ask; so do customers.
- Segregate duties in code: the person who creates a disbursement should not be
  the person who approves it.

## 14. PII and privacy

- **Collect only what the credit decision needs.** Every extra field is
  liability.
- Define a retention period per data category and enforce deletion
  automatically. "We keep everything forever" is not a policy.
- Support access, correction and erasure requests — subject to the record-
  keeping the lending licence requires, which usually overrides erasure.
- Track consent explicitly, with a timestamp and what was consented to.
- Restrict staff access by role and log every view of a customer file.
- Do not send PII to third parties — analytics, session replay, error trackers,
  chat widgets — without a legal basis and a data processing agreement. Error
  trackers in particular will capture request bodies by default; configure
  scrubbing before enabling one.
- Never email a document or an identity number as an attachment.

## 15. API security

- Authenticate with Sanctum (first-party) or OAuth via Passport. Not a static
  API key in a query string.
- Version the API. Scope tokens narrowly and expire them.
- Rate-limit per token, and return generic errors — do not let error text
  confirm whether an account or an application exists.
- Validate and authorize on **every** endpoint; an internal API is not a
  trusted one.
- Verify inbound webhooks by signature with a constant-time comparison, and
  make handlers idempotent. Payment callbacks will be replayed.
- Never expose an internal model straight out of an endpoint. Use explicit API
  Resources so a new column cannot silently leak.
- Lock CORS to known origins. Not `*`.

## 16. Dependencies

- `composer audit` and `npm audit` in CI; fail the build on a high severity.
- Keep `composer.lock` and `package-lock.json` committed, and deploy with
  `composer install --no-dev` in production.
- Review what a new dependency actually does before adding it. Every package is
  code running with the application's privileges.
- Track Laravel and PHP release support; do not run an unsupported PHP.
- Pin versions; do not deploy from a floating tag.

## 17. Production deployment

- Only `public/` is web-accessible. The document root must not be the project
  root — `.env`, `storage/` and `vendor/` must never be reachable over HTTP.
- `php artisan config:cache`, `route:cache`, `view:cache` on deploy.
- `APP_DEBUG=false`. Custom `404` and `500` pages that reveal nothing.
- Disable directory listing. Block access to dotfiles.
- Restrict database access to the application host; no public database port.
- Automated, **encrypted, tested** backups. An untested backup is not a backup.
- Use a non-root user for the application process; least-privilege filesystem
  permissions.
- Keep an incident response plan: who is called, how customers are notified,
  and the regulator's breach notification window.

---

## Before any of this becomes relevant

The moment this project stops being a static site — the first form that
submits, the first record stored — this document stops being aspirational.
Re-read it then, and implement §3, §4, §5, §7 and §11 in the same change that
introduces the feature, not afterwards.
