# COMPANY

Business context for the project.

> **Status: partially verified.**
>
> The company has been identified. **Identity, regulator, licence number and
> registered address are now verified** against the Reserve Bank of Zimbabwe's
> published registers of licensed microfinance institutions — see
> *Verified facts* below, which carries a source for every line.
>
> **Everything commercial is still unverified.** There is no public website, no
> marketing material, no product sheet, no rate card and no customer record for
> this business anywhere online. Products, amounts, terms, interest rates, fees,
> eligibility, SLAs and customer segments therefore remain `[PLACEHOLDER]`.
>
> For a regulated lender, a plausible-looking invented figure is worse than an
> empty field: it gets copied into the site, into schema markup, and into
> marketing, and it is very hard to trace back out. Nothing below has been
> invented to fill a gap.
>
> **The remaining client conversation is now much shorter.** See
> *Open questions for the client*.

---

## Verified facts

Sourced from the Reserve Bank of Zimbabwe (RBZ) Bank Supervision Division
registers of licensed microfinance institutions. Retrieved 22 September 2026.

| Item | Value | Source |
|---|---|---|
| Registered legal name | **Baardy Micro Capital (Pvt) Ltd** | RBZ register, 31 Mar 2026 |
| Country of operation | **Zimbabwe** | RBZ register |
| Region (head office) | **Harare** | RBZ register, 31 Dec 2015 |
| Second branch | **Bulawayo** | Client-supplied printed material, Sep 2026 |
| Registered / head office address | **Office 400, 4th Floor, Construction House, 108–110 Leopold Takawira Street, Harare** | RBZ registers, 2015 & 2018 |
| Regulator | **Reserve Bank of Zimbabwe**, Bank Supervision Division — Registrar of Microfinance Institutions | Microfinance Act; RBZ licensing requirements |
| Licence number | **658** | RBZ register, 31 Dec 2015 (*Licence No.* column) |
| Licence category | **MLI — moneylending / credit-only microfinance institution** | RBZ registers, 2015 & 2018 |
| Currently licensed | **Yes** — listed as a credit-only MFI as at 31 March 2026 | RBZ register, 31 Mar 2026 |
| First appears on register | Between **30 June 2015** (absent) and **30 September 2015** (present) | RBZ registers, Jun & Sep 2015 |
| CEO / MD | **Admire Madamombe** | RBZ register, 31 Dec 2018 |
| Telephone (head office) | **+263 772 550 189** | Client-confirmed, Sep 2026; matches RBZ registers 2015 & 2018 |
| Telephone (Bulawayo) | **(0)29 2883 657**, **+263 775 399 113**, **+263 715 351 013** | Client-supplied material, Sep 2026 |
| Email | **baardymicrocapital@gmail.com** | RBZ register, 31 Dec 2018 |

### Caveats on the above — read before publishing any of it

- **The head office number is confirmed.** The client gave
  **+263 772 550 189**, which is the same number the RBZ registers printed in
  2015 and 2018 — two independent sources eight years apart. It is published.
- **The CEO and email are still from the 31 December 2018 register**, roughly
  eight years old, and the 2026 register does not republish contact columns.
  **Reconfirm both with the client before they go on the site.**
- **The register's second number, `777254`, is incomplete as printed** — six
  digits with no area or mobile prefix. It is not published anywhere and should
  not be until someone confirms what it is.
- **The email is a Gmail address.** That is normal for a small Zimbabwean MLI,
  but it undercuts the "trustworthy" positioning on a lending site. Recommend a
  domain mailbox before launch.
- RBZ prints the address as "**Conctruction** House" — a typo repeated across
  several registers. The building is **Construction House**, 108–110 Leopold
  Takawira Street, Harare. Publish the corrected spelling.
- **Licence number 658 is sourced from the December 2015 register**, the last
  edition to publish a licence-number column. It is almost certainly still
  correct, but it is the single highest-consequence figure on the site.
  **Have the client confirm it against the physical licence certificate before
  it goes in the footer.**
- Company registration number, tax number and data-protection registration are
  **not** in any RBZ register and remain unknown.

### What this does *not* tell us

The RBZ register is a licence list. It carries no products, no rates, no fees,
no loan sizes, no staff numbers, no branches and no customers. None of that is
publicly available for this business. It has to come from the client.

---

## Company

**Trading name:** `[PLACEHOLDER]` — see note below

**Registered legal entity name:** Baardy Micro Capital (Pvt) Ltd *(verified)*

The site currently renders the name as **"Baardy"**, which was a working title
taken from the project directory. It now turns out to match the registered
name's distinctive element, so it is defensible as a short form — but **whether
the business actually trades as "Baardy", "Baardy Micro Capital", or something
else entirely is still unconfirmed.** Set in `config/company.php` → `name`, with
the full entity in → `legal_name`.

**Founded:** `[PLACEHOLDER]` — licensed by RBZ during Q3 2015, so the business
has been operating for **at least ~11 years**. The incorporation date may be
earlier than the licence date; only the client can confirm it.

**Size:** `[PLACEHOLDER]` — staff count, branch count. The register lists a
single Harare head office; the client has since confirmed a **second branch in
Bulawayo**. The register does not reliably enumerate branches, so there may be
others -- ask.

**Website:** **None found.** Extensive searching turned up no website, no social
media presence and no business-directory listing. This project appears to be the
company's first web presence — which makes the SEO opportunity larger and the
accuracy obligation higher, since there is no existing public record to check
the copy against.

---

## Market

Microfinance, lending and consumer/SME fintech, in Zimbabwe.

**The lending model is now confirmed by the licence category.** Baardy holds a
**credit-only / moneylending (MLI)** licence. That means:

- It **lends its own capital.** The site's existing assumption is correct and
  the copy does not need rewriting for a brokerage or peer-to-peer model.
- It **may not take deposits from the public.** This is a hard statutory
  prohibition, not a business choice. **The site must never imply savings,
  deposit, wallet or account-balance features**, and should not use the word
  "bank" about itself. Deposit-taking in Zimbabwe requires a separate
  microfinance-bank licence — there were only 7 such institutions on the
  March 2026 register, against 332 credit-only MFIs.

**Competitive position:** `[PLACEHOLDER]`

**Market structure (verified):** Baardy is one of **332 registered credit-only
microfinance institutions** in Zimbabwe as at 31 March 2026. This is a crowded,
low-differentiation market. At least two direct competitors — Ashleen
Investments and Bayce Microfinance — operate from *the same building* on
Leopold Takawira Street.

**Main competitors:** `[PLACEHOLDER]` — the client should name who they actually
lose deals to. The register gives the universe, not the competitive set.

---

## Geography

**Country of operation:** **Zimbabwe** *(verified)*

**Cities / regions served:** **Harare and Bulawayo** — the two cities with
offices. Whether lending extends beyond them (by phone, agent or referral) is
still `[PLACEHOLDER]`.

**Branch locations:** two, both set in `config/company.php` → `branches`.

| Branch | Address | Contact |
|---|---|---|
| **Harare** — head office | Office 400, 4th Floor, Construction House, 108–110 Leopold Takawira Street | Cell +263 772 550 189 |
| **Bulawayo** — branch | 3rd Floor, Mership House, Cnr 9th Avenue and J. Nkomo Street | Tel (0)29 2883 657 · Cell +263 775 399 113 · +263 715 351 013 |

Still to confirm:

- **The Bulawayo building name was read from a soft photograph.** "Mership
  House" is the most likely reading but the print is not sharp enough to be
  certain. Confirm the spelling before it is used in schema markup or print.
- **Opening hours.** The site shows "Mon–Fri, 08:00–17:00" beside the phone
  number in the header and the closing call to action. That is still a
  placeholder and is now the only unverified thing next to a real number.

**Languages required:** `[PLACEHOLDER]` — the site is currently English only.
English is an official language of Zimbabwe and is standard for financial
documents, so English-only is defensible for launch. **Shona and Ndebele should
be considered**, particularly for the target segment of traders and informal
earners, but this is a client decision, not an assumption to bake in.

**Currency: US dollars (USD).** *Confirmed by the client, September 2026.*

Rendered as `$` throughout. Set once in `config/company.php` →
`currency.symbol` / `currency.code`, and flowing through every figure on the
site via `<x-ui.money>`.

**One open point on presentation.** Zimbabwe operates a **multi-currency
regime**: both the **ZiG** (Zimbabwe Gold, introduced 5 April 2024) and the US
dollar are legal tender, and RBZ has stated an intention to move to ZiG as sole
legal tender with de-dollarisation targeted by 2030. Because of that, a bare
`$` is genuinely ambiguous to a Zimbabwean reader, and the local convention for
disambiguating is **`US$`**.

The site uses `$` as instructed. **Recommend raising `US$` with the client** —
on a lending site, the currency a borrower will repay in should not be
inferable only from context. It is a one-character change to `currency.symbol`
and every amount follows.

That USD is the lending currency is also the commercially sensible answer here:
it is what holds value in this market, and it is what the RBZ expresses the
sector's minimum capital requirement in (USD 25,000).

Geography *is* now unblocked for: local SEO strategy, `LocalBusiness` schema,
and the footer address. Those can proceed on the verified Harare address.

---

## Target customers

**Still assumed, not verified.** No customer information for this business is
public. The segments below are inferred from the brief and from what a
Harare-based credit-only MLI typically serves. **Confirm before treating as
settled.**

- **Small business owners and traders** needing working capital — stock, wages,
  a supplier invoice, equipment.
- **SMEs** needing larger, longer facilities against a specific plan.
- **Salaried individuals** with planned personal costs — school fees, medical
  bills, a move.
- **Entrepreneurs and sole traders** with irregular or seasonal income and thin
  credit files.

The Harare CBD location — an office building on Leopold Takawira Street, walking
distance from the main trading areas — is *consistent with* a walk-in SME and
trader customer base rather than a purely digital one. That is an inference from
the address, not a fact. **It matters for the site**: if most customers walk in,
the site's job is to pre-qualify and build trust before a branch visit, not to
close an application online.

**Who is explicitly *not* a customer:** `[PLACEHOLDER]` — e.g. minimum trading
period, excluded sectors, excluded regions.

---

## Products

**No product information for this business is publicly available.** The site
currently presents four facilities. **The names are plausible; every figure
attached to them is a placeholder.** They live in `config/marketing.php` →
`products`.

| Product | Amount range | Term | Status |
|---|---|---|---|
| Business working capital | `[PLACEHOLDER]` | `[PLACEHOLDER]` | Illustrative |
| SME term loans | `[PLACEHOLDER]` | `[PLACEHOLDER]` | Illustrative |
| Personal loans | `[PLACEHOLDER]` | `[PLACEHOLDER]` | Illustrative |
| Emergency credit | `[PLACEHOLDER]` | `[PLACEHOLDER]` | Illustrative |

Still needed for each product:

- Real minimum and maximum amounts (in USD, now confirmed)
- Real term ranges
- Interest rate or pricing basis — **and whether it is quoted monthly or
  annually**, which is the norm-sensitive part in this market
- Fee schedule — origination, late payment, early settlement
- Eligibility criteria
- Required documents
- Security or collateral requirements
- Disbursement method and realistic timing — **cash, bank transfer, EcoCash or
  other mobile money?** This materially affects the "how it works" page.

**Good news:** the client already holds this information in a form RBZ requires.
Licensed credit-only MFIs must file, and keep current, minimum and maximum loan
sizes per borrower and maximum maturity (RBZ *Minimum Licensing Requirements for
Credit-Only Microfinance Institutions*, Jan 2023, §8.8) and an itemised
breakdown of interest rates and all charges (§8.9, §8.17). **Asking the client
for their RBZ submission is the fastest way to fill this whole table
accurately.**

**Until pricing exists there can be no repayment calculation.** See `MEMORY.md`,
*Decisions that must not be accidentally reversed*, item 1.

---

## Value proposition

**Confirmed:** `[PLACEHOLDER]`

The current copy is written around three claims, chosen because they are what
this customer base actually complains about elsewhere. **Each needs to be
verified as true of this business before launch** — they are product promises,
not decoration:

1. **A decision in days rather than weeks.** Currently stated as "usually one
   working day" for complete applications. Needs a real SLA.
2. **The full cost in writing before you commit** — interest, every fee, and
   the total repayable as one figure. *This one is well supported: RBZ requires
   licensed MFIs to operate under the microfinance **Core Client Protection
   Principles** (§8.7, §8.16) and to keep a complaints procedure visible to
   clients (§8.10). Stating the cost clearly is an obligation the client already
   carries — but confirm they meet it in practice before claiming it as a
   differentiator.*
3. **Repayment that matches how the customer earns** — weekly, fortnightly or
   monthly, adjustable for seasonal income, with no early-settlement penalty.

**A fourth claim is now available and is genuinely strong in this market:**

4. **Licensed and regulated by the Reserve Bank of Zimbabwe since 2015.** In a
   market with 332 credit-only lenders and widespread unlicensed moneylending, a
   verifiable licence number and an eleven-year track record are the single most
   credible trust signals this business has. **Use them prominently** — footer,
   about page, and `LocalBusiness` schema — once the licence number is confirmed
   against the certificate.

If any of these is not true, remove it. A promise on a lending site is a
representation.

---

## Brand personality

- Trustworthy
- Modern
- Transparent
- Human
- Fast
- Professional

### What this means in practice

The design and copy already express this deliberately, so it is worth stating
how, to keep future work consistent:

- **Trustworthy** → figures are labelled, costs are stated before the ask, and
  nothing is overclaimed. **Visible RBZ licensing — now available.**
- **Transparent** → the cost is the headline of the offer, not a footnote. The
  "what happens if I miss a payment" question is answered directly rather than
  avoided.
- **Human** → a phone number in the header. Copy says "a person reads your
  application" because that is the differentiator against automated scoring.
- **Modern** → restraint, whitespace and typography, not gradients and
  animation. Also a genuine differentiator here: most Zimbabwean MLIs of this
  size have no website at all.
- **Fast** → speed is expressed as a concrete timeframe, never as "instant".

### Tone of voice

Plain, direct, second person. Short sentences. Active voice. No jargon, no
corporate abstraction, and specifically **not** the register of "empowering your
financial future" — that language is what the design is reacting against.

---

## Customer problems

The financial problems the business exists to solve:

- **Timing.** The opportunity, the stock, or the emergency is now; bank credit
  takes weeks. Slow money is often the same as no money.
- **Exclusion.** Thin or no credit file, informal income, or no collateral — so
  automated scoring declines them regardless of whether they can repay. *Acute
  in Zimbabwe, where a large share of economic activity is informal and outside
  formal credit-bureau coverage.*
- **Opacity.** Fees that appear after signing, and a total cost the borrower
  cannot work out in advance.
- **Rigidity.** Fixed monthly schedules that ignore seasonal or irregular
  earnings, which turns a solvent borrower into a defaulting one.
- **No recourse.** Nobody to call when circumstances change, so a missed payment
  escalates instead of being restructured.
- **Being declined without explanation**, and so being unable to fix the problem
  for next time.
- **Not knowing whether a lender is legitimate.** In a market with hundreds of
  licensed lenders and many unlicensed ones, borrowers cannot easily tell them
  apart. A published licence number answers this directly.

---

## Customer outcomes

What a customer should be able to say afterwards:

- "I knew where I stood within a day."
- "I knew exactly what it would cost before I agreed to anything."
- "The repayments fit how my business actually earns."
- "I could reach a person who could help."
- "I took the opportunity instead of watching it pass."
- "Paying it off early actually saved me money."
- "I would use them again, and I would tell someone else to."

These are the outcomes the site's copy promises. They are also the right basis
for testimonials once real ones are collected.

---

## Compliance

**Partially resolved.** These appear in the site footer, and they are the fields
a regulator and a cautious customer both check. Set in `config/company.php` →
`compliance`.

| Item | Value | Status |
|---|---|---|
| Registered company name | Baardy Micro Capital (Pvt) Ltd | **Verified** |
| Company registration number | `[PLACEHOLDER]` | Not public — ask client |
| Registered address | Office 400, 4th Floor, Construction House, 108–110 Leopold Takawira Street, Harare | **Verified** |
| Lending licence number | **658** | **Verified (2015 register) — confirm against certificate** |
| Regulatory body | Reserve Bank of Zimbabwe — Registrar of Microfinance Institutions | **Verified** |
| Licence category / scope | Credit-only microfinance (moneylending); **deposit-taking not permitted** | **Verified** |
| Data protection registration | `[PLACEHOLDER]` | Not public — ask client |
| Tax identification number | `[PLACEHOLDER]` | Not public — ask client |

### Governing legislation

The **Microfinance Act** (Act 3 of 2013) governs credit-only microfinance
institutions and the Registrar of Microfinance Institutions.

**Note a citation discrepancy to resolve with the client's lawyer:** ZimLII
publishes the Act as **[Chapter 24:29]**, while RBZ's own January 2023 licensing
requirements cite it as **[Chapter 24:30]**. Both citations are in current
circulation. **Do not print a chapter number on the site until this is settled**
— an incorrect statutory citation on a lender's website is exactly the kind of
detail a regulator notices.

### Responsible lending requirements

Partially sourced from RBZ's *Minimum Licensing Requirements for Credit-Only
Microfinance Institutions* (January 2023). That document governs licensing
rather than ongoing conduct, so **this list is indicative and must be checked
against the Act and the licence conditions** before any of it drives copy:

- **Core Client Protection Principles** compliance, on an ongoing basis, with
  documented strategies (§8.7, §8.16)
- **Itemised disclosure of interest rates and all charges**, with justification;
  the effective rate (monthly interest plus all other charges) must be "in line
  with market trends and reflective of responsible lending" (§8.9, §8.17)
- **Loan agreements must comply with section 16 of the Microfinance Act**
  (§8.15) — `[PLACEHOLDER: obtain s.16 text]`. The RBZ-hosted copy of the Act is
  a scanned image with no text layer, so s.16's specific requirements could not
  be read. **This is the clause that governs what must appear in the loan
  agreement, so it directly constrains the site's disclosure copy.** Get it from
  the client's lawyer or a text copy of the Act.
- **A comprehensive complaints procedure manual, kept visible to clients**, with
  all complaints logged in a complaints register and resolutions documented
  (§8.10, §8.11) — **this maps directly to a required page on the site**
- **Minimum paid-up share capital of the ZWL equivalent of USD 25,000**,
  maintained on an ongoing basis (§3.1)
- **Formal, non-residential business premises** required
- Licensed **debt collectors** only, whose licence must be filed with RBZ (§8.14)

Still to confirm with the client: affordability assessment requirements, any
cooling-off period, rate or total-cost caps, prescribed arrears and collections
procedure, credit-bureau reporting obligations, and any external dispute
resolution scheme.

**Advertising restrictions still apply — see `SEO.md` §3.** Terms like
"guaranteed approval" and "no credit check" are prohibited or hazardous in most
lending jurisdictions and must not be used pending confirmation.

### Privacy requirements

Zimbabwe's applicable regime is the **Data Protection Act** (Act 5 of 2021),
administered by **POTRAZ** as the Data Protection Authority.
`[PLACEHOLDER — confirm with client's lawyer, including whether Baardy is
registered as a data controller]`. Determines:

- Lawful basis for processing, and consent records
- Retention periods per data category
- Data subject rights, and where record-keeping obligations override erasure
- Cross-border transfer rules — **directly relevant to hosting location. If the
  site is hosted outside Zimbabwe, confirm the transfer basis before launch.**
- Breach notification window
- Whether a Data Protection Officer is required

See `SECURITY.md` §14.

### Required legal pages

Linked from the footer, none written:

- Privacy policy
- Terms of service
- Responsible lending statement
- **Complaints procedure** — note this is a regulatory obligation (§8.10), not
  just good practice
- Cookie policy, if analytics are added

---

## Open questions for the client

Reordered — the identity and regulatory questions are now answered, so the
commercial questions are what block the most work.

**Confirm (quick — we have answers, we need sign-off):**

1. **Licence number 658** — confirm against the physical licence certificate.
2. **Is Admire Madamombe still CEO/MD?** Our source is the 2018 register.
3. **Email, and opening hours.** The head office number is confirmed; the email
   is still the 2018 register's Gmail address — is there a domain mailbox? And
   what are the real branch opening hours?
4. **Trading name** — "Baardy", "Baardy Micro Capital", or something else?
5. **Company registration number, tax number, data-protection registration.**
6. **Should amounts read `US$10,000` rather than `$10,000`?** Zimbabwe is a
   dual-currency market, so a bare `$` is ambiguous and `US$` is the local
   convention. One-character change; see *Geography → Currency*.

**Answer (blocking — nothing public exists):**

7. **Real product terms** — amounts, periods, rates, fees, eligibility,
   required documents. *Ask for the RBZ submission; it contains all of this.*
8. **Is interest quoted monthly or annually?**
9. **What is the real decision SLA?** The site currently says one working day.
10. **How are funds disbursed** — cash, bank transfer, EcoCash/mobile money?
11. **Is early settlement genuinely free of penalty?**
12. **Are flexible/seasonal repayment schedules genuinely offered?**
13. **Does lending extend beyond Harare and Bulawayo?** Blocks location pages.
13. **Confirm the Bulawayo building name** — read as "Mership House" from a photo.
14. **Are there real customers who would give a consented testimonial?**
15. **Who owns compliance sign-off** for marketing copy?
17. **Should the site be English-only**, or are Shona and Ndebele needed?

---

## Sources

Retrieved 22 September 2026.

- RBZ, *List of Registered Microfinance Institutions as at 31 March 2026* —
  current licence status; Baardy listed at no. 22 of 332 credit-only MFIs.
  <https://www.rbz.co.zw/documents/bank_sup/Registered_Microfinance_/LIST_OF_REGISTERED_MICROFINANCE_INSTIUTIIONS_AS_AT_31_MARCH_2026.pdf>
- RBZ, *Register of Licensed Microfinanciers as at 31 December 2018* — CEO/MD,
  telephone, email.
  <https://www.rbz.co.zw/documents/bank_sup/MFI_registered_31Dec2018.pdf>
- RBZ, *Register of Licensed Microfinanciers as at 31 December 2015* — licence
  number 658, MLI classification, Harare region.
  <https://www.rbz.co.zw/documents/BLSS/BANK%20SUPERVISION/List%20of%20Registered%20Financial%20Institutions/Registered%20MicroFinance%20Institutions%20(MFIs)/MFI%20Register%20as%20at%2031%20December%202015.pdf>
- RBZ, *Register as at 30 September 2015* (present) and *30 June 2015* (absent)
  — brackets first licensing to Q3 2015.
- RBZ Bank Supervision Division, *Minimum Licensing Requirements for Credit-Only
  Microfinance Institutions*, January 2023 — capital, disclosure and client
  protection obligations.
  <https://www.rbz.co.zw/documents/BLSS/2023/COMFIs_-_Minimum_Licensing_Requirements_2023.pdf>
- Microfinance Act (Act 3 of 2013), Zimbabwe — cited as Chapter 24:29 (ZimLII)
  and Chapter 24:30 (RBZ 2023). Discrepancy unresolved.
