<?php

/*
|--------------------------------------------------------------------------
| Marketing content
|--------------------------------------------------------------------------
|
| Content for the public homepage, kept out of the Blade templates so that
| copy and figures can be revised - or moved behind a CMS - without editing
| markup. Each section partial reads the array it needs and renders it.
|
| EVERY NUMBER IN THIS FILE IS AN ILLUSTRATIVE PLACEHOLDER. None of it
| describes real lending activity, real customers or real outcomes. The
| views render a visible "illustrative" disclosure wherever these figures
| appear. Replace them only with verified data.
|
*/

return [

    /*
    | Hero ----------------------------------------------------------------
    |
    | A full-bleed photograph of the people the business actually lends to,
    | with the proposition, the ask and the verifiable credentials laid over
    | it. The whole hero fits one viewport; see sections/home/hero.blade.php.
    */
    'hero' => [
        /*
        | Two short statements, one per line: what the borrower gets (clear
        | terms) and who stands behind it (people). Five words, matching the
        | reference hero (and six at most, per the client).
        | True of every product and every borrower, and it makes no speed
        | claim -- the old "Working capital in days, not weeks" spoke to SME
        | borrowers only and put an unconfirmed turnaround in the largest type
        | on the site. The second line is set a step quieter than the first.
        */
        'heading' => [
            'Loans made clear.',
            'People first.',
        ],

        /*
        | One short line: the two things that make the offer different. The loan
        | types are carried by the chips beside it, so they are not repeated here.
        | Anything longer is not read before the visitor scrolls.
        */
        'lead' => 'Every loan is reviewed by a person, with every cost in writing.',

        /*
        | The three objections a borrower raises first, answered before the
        | call to action rather than after it.
        |
        | WORDING IS LOAD-BEARING. "on complete applications" qualifies the
        | one-day claim; "Settle early" scopes the no-penalty claim. Do not
        | tighten these into unconditional promises — on a lending site a
        | promise is a representation. All three are still unverified: see
        | docs/COMPANY.md, "Value proposition".
        */
        'promises' => [
            'A decision on complete applications in one working day',
            'Every fee and the total repayable, in writing, before you accept',
            'Settle early and pay less interest, with no penalty',
        ],

        /*
        | Review rating shown beneath the hero call to action.
        |
        | NULL, AND IT MUST STAY NULL UNTIL THE REVIEWS EXIST. Checked on
        | 22 September 2026: Baardy has no Google Business Profile at all, so
        | there is no rating, no review count and nothing to show. The hero
        | renders this block only when `score` is set, so leaving it null
        | removes it cleanly rather than showing an empty widget.
        |
        | Do not populate it from memory, from an estimate, or from what the
        | rating "should" be. A star rating is a factual claim about what other
        | customers said; inventing one on a licensed lender's site is a
        | consumer-protection problem, and Google delists profiles for it.
        |
        | When a profile exists and has reviews, fill in exactly what it shows
        | and link to it so the claim is checkable:
        |
        |   'rating' => [
        |       'score'  => 4.8,
        |       'count'  => 19,
        |       'source' => 'Google',
        |       'href'   => 'https://...',   // the public profile
        |   ],
        |
        | For scale: competing Harare microfinance firms on Google Maps carry
        | between 1 and 19 reviews, so a realistic first milestone is small.
        */
        'rating' => null,

        /*
        | A SAMPLE rating, shown only to preview the design while the real one
        | does not exist. Switched on with HERO_RATING_PREVIEW=true in .env
        | AND only ever rendered when APP_ENV=local -- the hero enforces that,
        | so it cannot reach a live server even if the flag is set there. It is
        | unlabelled, so it must never be seen by a real visitor. A real
        | `rating` above always takes precedence.
        */
        'rating_preview' => env('HERO_RATING_PREVIEW', false) ? [
            'score' => 4.8,
            'count' => 19,
            'source' => 'Google',
        ] : null,

        /*
        | The two credential cards on the right of the hero.
        |
        | These exist because the reference designs this hero was measured
        | against carry their trust in invented social proof -- star ratings,
        | customer avatars, "$196,000 saved". None of that exists for this
        | business, and manufacturing it on a lender's site is not a placeholder
        | problem, it is a regulatory one.
        |
        | So the tiles carry the things that ARE true and independently
        | checkable in the RBZ register. For a borrower choosing between
        | licensed and unlicensed lenders, they are worth more than a rating.
        */
        'credentials' => [
            /*
            | `since` is the year the company first appears on the RBZ
            | register. The tile counts forward from it rather than hard-coding
            | a figure, so the claim cannot quietly go stale.
            */
            'years' => [
                'since' => 2015,
                'label' => 'years licensed and lending',
                'note' => 'On the Reserve Bank of Zimbabwe register since 2015',
            ],
            'licence' => [
                'label' => 'Credit-only microfinance licence',
                'note' => 'Harare and Bulawayo, Zimbabwe',
            ],
        ],

        /*
        | The hero shows ONE photograph: the first entry here, the farmer, whose
        | subject sits right of centre so the two-line headline never crosses
        | a face. The others are kept for reuse; the hero no longer rotates
        | through them (a slideshow asks the visitor to wait, and each photo
        | implied a different message).
        |
        | All from Unsplash (free for commercial use, Unsplash License):
        |   vendor      unsplash.com/photos/uk3ey_vhDKA
        |   farmer      unsplash.com/photos/1AoGjqdyDLU
        |   market      unsplash.com/photos/XzgW_vYpm8M
        |
        | `position`  object-position -- keep the subject clear of the copy
        | `flip`      mirror the frame, to move a subject off the left half;
        |             only for photographs with no legible text in them
        | `stretch`   at lg, set the frame this much wider than the hero and
        |             anchored left, which pushes a centred subject right
        */
        'slides' => [
            [
                'label' => 'Farming',
                'alt' => 'A farmer tending a green field of crops',
                'sources' => [
                    'images/hero/farmer-1280.webp' => 1280,
                    'images/hero/farmer-2400.webp' => 2400,
                ],
                'position' => '30% 25%',
                'flip' => true,
                'stretch' => 0,
            ],
            [
                'label' => 'Small business',
                'alt' => 'A food-stall owner in an apron smiling at a phone behind the counter',
                'sources' => [
                    'images/hero/vendor-1280.webp' => 1280,
                    'images/hero/vendor-2400.webp' => 2400,
                ],
                'position' => '40% 30%',
                'flip' => true,
                'stretch' => 0.15,
            ],
            [
                'label' => 'Markets',
                'alt' => 'A trader at a fresh-produce market stall stacked with fruit',
                'sources' => [
                    'images/hero/market-1280.webp' => 1280,
                    'images/hero/market-2400.webp' => 2400,
                ],
                'position' => '60% 40%',
                'flip' => false,
                'stretch' => 0,
            ],
            [
                'label' => 'SME loans',
                'alt' => 'Young traders gathered at a phone-case stall',
                'sources' => [
                    'images/hero/sme-1030.webp' => 1030,
                ],
                'position' => '50% 40%',
                'flip' => false,
                'stretch' => 0,
            ],
        ],
    ],

    /*
    | Trust ---------------------------------------------------------------
    |
    | The section directly under the hero: a photograph with a glass card of
    | register facts, beside a short statement and three large figures. Every
    | figure is true and checkable, so it needs no "illustrative" disclaimer.
    |
    | `key` names a figure the view computes from config -- none is typed in
    | here, so none can drift out of step with the footer or the register:
    |   years     since company.compliance.licensed_since
    |   since     company.compliance.licensed_since
    |   licence   company.compliance.licence
    |   products  count of marketing.products
    |   branches  count of company.branches
    |
    | The old aggregate figures (12,400 borrowers, $310m, 26 hrs, 94%) were
    | placeholders. Bring figures like those back only from the client's real
    | reporting, with the basis of each stated in `note`.
    */
    'trust' => [
        'heading' => 'A licensed lender, built around the way Zimbabwe earns',
        'body' => 'On the Reserve Bank of Zimbabwe’s register since 2015, we lend to workers, farmers, '
            .'traders and small businesses, with every cost in writing, upfront.',
        'link' => 'Check our licence on the RBZ register',

        /*
        | The photograph beside the copy, with the glass card laid over it.
        | Unsplash License: unsplash.com/photos/cU3xxfbB9Es
        */
        'image' => [
            'alt' => 'A tailor smiling at a phone while working at a sewing machine',
            'sources' => [
                'images/about/tailor-720.webp' => 720,
                'images/about/tailor-1280.webp' => 1280,
            ],
        ],

        'card' => [
            'title' => 'On the public register',
            'facts' => [
                ['key' => 'licence', 'prefix' => 'No. ', 'label' => 'RBZ licence'],
                ['key' => 'since', 'label' => 'Listed since'],
            ],
        ],

        'facts' => [
            ['key' => 'years', 'suffix' => '+', 'label' => 'Years licensed and lending'],
            ['key' => 'products', 'label' => 'Loan products'],
            ['key' => 'branches', 'label' => 'Offices you can walk into'],
        ],
    ],

    /*
    | Products ------------------------------------------------------------
    |
    | Names, summaries and `best_for` are drawn from the client-supplied
    | "BMC Website Profile" (received 2026-09-25) and describe the
    | lending products the company actually offers.
    |
    | `min`/`max`/`term` are NOT in that document and remain illustrative
    | placeholders -- the products.blade.php disclosure says so. Replace them
    | with the client's real ranges before launch.
    |
    | `image` is the photograph behind each panel of the products rail; all
    | from Unsplash (free for commercial use, Unsplash License).
    |
    | Youth Empowerment Loans was added later, from the client's final
    | "BMC Website Design" document, with no terms supplied.
    |
    | The profile lists a sixth item, Financial Advisory Services, which is
    | not a loan and has no amount or term -- it is surfaced instead as a
    | benefit in config('marketing.benefits').
    */
    'products' => [
        [
            'name' => 'Salary-Based Loans',
            'slug' => 'salary-based-loans',
            'image' => ['src' => 'images/products/salary-1600.webp', 'alt' => 'A salaried professional in a suit, smiling', 'position' => '65% 30%'],
            'summary' => 'Short-term financing for public-sector employees, government pensioners '
                .'and private-sector employees to meet personal and financial needs.',
            'min' => 2500,
            'max' => 150000,
            'term' => '1 to 12 months',
            'best_for' => 'Salaried public and private-sector employees, and government pensioners',
        ],
        [
            'name' => 'Educational Loans',
            'slug' => 'educational-loans',
            'image' => ['src' => 'images/products/education-1600.webp', 'alt' => 'A schoolchild holding up work in a classroom', 'position' => '50% 35%'],
            'summary' => 'Affordable financing to support education expenses at various levels, '
                .'helping families and students access educational opportunities.',
            'min' => 1000,
            'max' => 50000,
            'term' => '3 to 12 months',
            'best_for' => 'Parents, guardians and students funding school or tuition fees',
        ],
        [
            'name' => 'Agricultural Loans',
            'slug' => 'agricultural-loans',
            'image' => ['src' => 'images/products/agriculture-1600.webp', 'alt' => 'A farmer tending a green field of crops', 'position' => '40% 40%'],
            'summary' => 'Financing to support farmers and agricultural activities, including '
                .'inputs, production and other farming requirements.',
            'min' => 2000,
            'max' => 100000,
            'term' => '3 to 12 months',
            'best_for' => 'Farmers and agricultural producers funding a season\'s inputs or production',
        ],
        [
            'name' => 'Women Empowerment Loans',
            'slug' => 'women-empowerment-loans',
            'image' => ['src' => 'images/products/women-1600.webp', 'alt' => 'A woman running her own shop, serving a customer', 'position' => '30% 35%'],
            'summary' => 'Financial support designed to empower women, promote entrepreneurship and '
                .'strengthen livelihoods, with opportunities for strategic partnerships focused on '
                .'poverty reduction.',
            'min' => 1000,
            'max' => 75000,
            'term' => '3 to 24 months',
            'best_for' => 'Women entrepreneurs and women-led businesses',
        ],
        [
            // Added from the client's final "BMC Website Design" document, which
            // names the product and supplies the photograph but no copy or
            // terms. The summary restates the company profile's aims (young
            // entrepreneurs, livelihoods); `min`/`max`/`term` are PLACEHOLDERS
            // like every range here -- and no age limit is stated, as none
            // has been given. Replace all of it with the client's real terms.
            'name' => 'Youth Empowerment Loans',
            'slug' => 'youth-empowerment-loans',
            'image' => ['src' => 'images/products/youth-1600.webp', 'alt' => 'Young traders gathered at a phone-case stall', 'position' => '50% 40%'],
            'summary' => 'Financial support to help young people start and grow a business, '
                .'build their livelihoods and create opportunity for themselves and others.',
            'min' => 1000,
            'max' => 50000,
            'term' => '3 to 12 months',
            'best_for' => 'Young entrepreneurs starting or growing a business',
        ],
        [
            'name' => 'SME Bridging Finance',
            'slug' => 'sme-bridging-finance',
            'image' => ['src' => 'images/products/sme-1600.webp', 'alt' => 'A butcher standing in the shop doorway', 'position' => '60% 40%'],
            'summary' => 'Short-term working capital for SMEs, sole traders and cross-border traders '
                .'to support business start-up, working capital and recapitalisation.',
            'min' => 5000,
            'max' => 250000,
            'term' => '1 to 12 months',
            'best_for' => 'SMEs, sole traders and cross-border traders needing working capital',
        ],
    ],

    /*
    | How it works --------------------------------------------------------
    |
    | The company's actual four-step application process, taken from the
    | "How to apply" section of the client-supplied "BMC Website Profile"
    | (received 2026-09-25). This is a real sequence, which is why it
    | carries step numbers. The profile lists four steps; "Visit a branch"
    | and "Bring your documents" happen on the same visit, so they are shown
    | as one step here. The wording of each is unchanged in substance.
    |
    |   meta  what the step involves -- NOT a duration. Put real timings here
    |         only once the client confirms them.
    |   icon  x-ui.icon name shown beside `meta`
    */
    'steps' => [
        [
            'title' => 'Visit a branch with your documents',
            'body' => 'Come in to our Harare or Bulawayo branch with a valid national ID and your '
                .'latest payslip, plus company documents and financials for an SME loan.',
            'meta' => 'In person, KYC documents',
            'icon' => 'user',
        ],
        [
            'title' => 'Credit appraisal and approval',
            'body' => 'Our team assesses your application and supporting documents. Once it meets '
                .'the required criteria, it moves forward to approval.',
            'meta' => 'Reviewed by our credit team',
            'icon' => 'shield',
        ],
        [
            'title' => 'Loan disbursement',
            'body' => 'Once your loan is approved, the funds are disbursed to you according to the '
                .'agreed terms.',
            'meta' => 'Funds released on approval',
            'icon' => 'receipt',
        ],
    ],

    /*
    | Why us --------------------------------------------------------------
    |
    | Customer outcomes, not product features.
    |
    | `stat`/`stat_label` follow the same pattern as the trust strip above:
    | a figure that leads, and a label that says what it measures. Most of
    | these are the site's existing policy claims restated as a number
    | (a $0 penalty, a $0 advisory fee, 3 schedule options) rather than
    | invented performance metrics, but they are still ILLUSTRATIVE
    | PLACEHOLDERS pending the client's own figures. The on-page disclosure
    | was removed at the client's request, so confirm every figure before
    | launch -- nothing on the page now flags them as provisional.
    |
    | `image` is optional. Photographs are Unsplash-licensed stock (free for
    | commercial use, attribution not required but recorded here), downloaded
    | and self-hosted under public/images/why-us/ rather than hotlinked, so the
    | page makes no third-party request. They are ATMOSPHERE, not customers:
    | no copy on the page may imply the people shown borrowed from Baardy.
    | Swap in the client's own photography when it exists.
    |
    |   shopkeeper  Ali Mkumbwa    unsplash.com/photos/EOkN2pRjFsg
    |   farmer      Richard Nyoni  unsplash.com/photos/1AoGjqdyDLU
    |   market      Ali Mkumbwa    unsplash.com/photos/XzgW_vYpm8M
    */
    /*
    | The "why borrow from us" bento grid. Every reason is on screen at once --
    | nothing to swipe or click through -- and the grid ends on an ask.
    |
    |   schedule  the large tile: pick a rhythm and see the payment days on a
    |             sample month. An illustration with no amounts; the real
    |             dates are agreed before the borrower accepts.
    |   advisory  the wide tile, with the topics as chips
    |   privacy   the small stat tile
    |   decision  the brand-colour tile that asks for the application
    |
    | `benefit` is an index into `benefits` below and `promise` an index into
    | hero.promises, so each claim's wording lives in one place. Costs in
    | writing, settling early and "a person reads it" are left to the
    | principles list.
    */
    'why_us' => [
        'heading' => 'Made around the way you earn and repay',
        'lead' => 'Loans shaped around real lives, with support that does not stop at the payout.',
        'action' => ['label' => 'Visit a branch', 'href' => '/contact'],
        'tiles' => [
            'schedule' => [
                'benefit' => 2,
                'options' => [
                    // Paydays on a sample four-week month, as day numbers.
                    ['label' => 'Weekly', 'days' => [5, 12, 19, 26]],
                    ['label' => 'Fortnightly', 'days' => [12, 26]],
                    ['label' => 'Monthly', 'days' => [26]],
                ],
            ],
            'advisory' => [
                'benefit' => 4,
                'topics' => ['Planning', 'Budgeting', 'Debt management'],
                'link' => ['label' => 'Read our guides', 'href' => '/insights'],
            ],
            'privacy' => ['benefit' => 5],
            'decision' => ['promise' => 0, 'stat' => '1', 'stat_label' => 'working day'],
        ],
    ],

    'benefits' => [
        [
            'stat' => '1 figure',
            'stat_label' => 'for interest, fees and the total repayable',
            'title' => 'You know what it costs before you commit',
            'body' => 'Every offer puts the interest, the fees and the total you repay in writing, '
                .'before you sign anything.',
            'image' => [
                'name' => 'shopkeeper',
                'sizes' => [720, 1440],
                'ratio' => [6, 5],
                'alt' => 'A shopkeeper smiling behind the counter of a well-stocked general store',
            ],
        ],
        [
            'stat' => '0',
            'stat_label' => 'applications declined by a score alone',
            'title' => 'A person reads your application',
            'body' => 'Thin credit files and irregular income are normal here.',
        ],
        [
            'stat' => '3',
            'stat_label' => 'repayment schedules to choose from',
            'title' => 'Repayments follow your cash, not a calendar',
            'body' => 'Weekly, fortnightly or monthly, built around your trade.',
            'image' => [
                'name' => 'farmer',
                'sizes' => [640, 1040],
                'ratio' => [8, 5],
                'alt' => 'A farmer inspecting his crop in a green field below the hills',
            ],
        ],
        [
            'stat' => '$0',
            'stat_label' => 'early-settlement penalty',
            'title' => 'Settling early costs you less, not more',
            'body' => 'Pay off a facility ahead of schedule, with no penalty.',
            'image' => [
                'name' => 'market',
                'sizes' => [560, 880],
                'ratio' => [11, 9],
                'alt' => 'A trader arranging a stall piled high with fresh fruit at a covered market',
            ],
        ],
        [
            'stat' => '$0',
            'stat_label' => 'cost of financial advisory',
            'title' => 'Free financial advisory, not just a loan',
            'body' => 'Guidance on planning, budgeting and debt management.',
        ],
        [
            'stat' => '0',
            'stat_label' => 'customer records sold to third parties',
            'title' => 'Your information stays yours',
            'body' => 'We collect what the decision needs, and never sell it on.',
        ],
    ],

    /*
    | Principles ----------------------------------------------------------
    |
    | The section after the products rail, laid out after the reference's
    | "Aligned. Flexible." list. It took the slot the placeholder
    | testimonials held: until consented quotes exist, the page states what
    | the lender commits to instead of what invented customers said.
    |
    | Rows with `promise` take their title from hero.promises, so the wording
    | -- and its qualifiers -- lives in one place. WORDING IS LOAD-BEARING:
    | see the note on hero.promises before editing either.
    */
    'principles' => [
        'heading' => ['Clear terms. Real people.', 'Built for borrowers.'],
        'lead' => "We lend the way we'd want to borrow: plainly, and on paper.",
        'action' => ['label' => 'See how it works', 'href' => '/#how-it-works'],
        'items' => [
            [
                'promise' => 0,
                'detail' => 'Bring everything on the checklist and you hear back the next working day.',
                'image' => 'images/hero/vendor-1280.webp',
            ],
            [
                'promise' => 1,
                'detail' => 'Interest, fees and the total you repay, set out before you sign anything.',
                'image' => 'images/about/tailor-720.webp',
            ],
            [
                'promise' => 2,
                'detail' => 'Pay off a loan ahead of schedule without being charged for it.',
                'image' => 'images/hero/market-1280.webp',
            ],
            [
                'title' => 'A person reads your application',
                'detail' => 'Thin credit files and irregular income are normal here, not a reason to decline.',
                'image' => 'images/hero/farmer-1280.webp',
            ],
        ],
    ],

    /*
    | Testimonials --------------------------------------------------------
    |
    | PUBLISHED ONLY WHEN REAL. The section renders an entry only when
    | `consented` is true, and renders nothing at all while no entry is --
    | so the three placeholders below never reach the page. Publishing
    | invented testimonials on a lending site is a misrepresentation.
    |
    | To publish a quote: replace the placeholder with the customer's own
    | words, their real name (or the form they agreed to, e.g. "Tendai M.")
    | and role, keep written consent on file, and set `consented` to true.
    | The first consented entry is the large featured card.
    |
    |   quote      the customer's words, lightly edited only with their OK
    |   name       as they agreed to be named
    |   role       trade and town
    |   initials   shown in place of a portrait
    |   consented  true only with written consent on file
    |
    | DEMO PREVIEW. `testimonials_preview` (TESTIMONIALS_PREVIEW in .env)
    | shows the unconsented placeholders for a client demo or an awards
    | submission, under a visible "sample quotes" note, and with the trade
    | in place of a name so none of them reads as a real, named customer.
    | Off by default; switch it off again before launch. The placeholders
    | also avoid any claim the client has not confirmed (turnaround times,
    | same-day payouts).
    */
    'testimonials_preview' => (bool) env('TESTIMONIALS_PREVIEW', false),

    /*
    | Proof ----------------------------------------------------------------
    |
    | The interim occupant of the social-proof slot, standing in for the
    | testimonials below until consented customer quotes exist.
    |
    | EVERY CLAIM HERE IS CHECKABLE BY THE READER. That is the whole idea: the
    | testimonials it replaces were placeholder quotes the page had to label as
    | not real, which on a lender's site teaches a visitor that no borrower
    | will put their name to it. These three facts are all on the public RBZ
    | register or on the front of a building.
    |
    | Copy only. Each entry's `key` tells the section which figure to put above
    | it -- the licence number, the years count and the branch count are all
    | read from config at render so none of them can drift or go stale.
    |
    | Swap this section back for testimonials in pages/home.blade.php the day
    | real quotes exist; the testimonials partial below is untouched.
    */
    'proof' => [
        [
            'key' => 'licence',
            'title' => 'Licensed and supervised',
            'body' => 'Baardy holds a credit-only microfinance licence and appears on the '
                .'Reserve Bank of Zimbabwe’s public register of licensed institutions.',
            'check' => 'Look us up on the RBZ register',
        ],
        [
            'key' => 'years',
            'title' => 'Years on the register',
            'body' => 'On the register since 2015: the same company under the same licence, '
                .'still listed in the most recent edition.',
            'check' => null,
        ],
        [
            'key' => 'branches',
            'title' => 'Offices you can walk into',
            'body' => 'A head office in Harare and a branch in Bulawayo, each with an address '
                .'and a phone number that a person answers.',
            'check' => null,
        ],
    ],

    /*
    | Customer stories -----------------------------------------------------
    |
    | Short, genuine accounts of a customer's experience, shown under the
    | testimonials. SAME RULE AS TESTIMONIALS: an entry is published only when
    | `consented` is true, and the section renders nothing while none is.
    | Never invent one. With `testimonials_preview` on, the samples below show
    | under a visible "Sample story" label (for a demo only).
    |
    |   title      a short line over the story (optional)
    |   photo      the photograph behind the quote (optional): for a real story,
    |              the customer's own, with their consent; without one the story
    |              is a light card. The samples borrow a loan photograph.
    |   story      what happened, in the customer's own words (2-3 sentences)
    |   name       as they agreed to be named (e.g. "Tendai M.")
    |   role       trade and town
    |   loan       which loan, matching a name in `products` (optional)
    |   consented  true only with written consent on file
    */
    'customer_stories' => [
        [
            'title' => 'Clear from the first visit',
            'photo' => 'images/products/sme-1600.webp',
            'story' => 'I came in not knowing which loan suited a market stall. The officer went through the options and put the total I would repay in writing.',
            'name' => 'Market trader',
            'role' => 'Sample story',
            'loan' => 'SME Bridging Finance',
            'consented' => false,
        ],
        [
            'title' => 'Repayments that follow the harvest',
            'story' => 'Repayments were planned around when my crops are sold, so I was not stretched in the months before harvest.',
            'name' => 'Smallholder farmer',
            'role' => 'Sample story',
            'loan' => 'Agricultural Loans',
            'consented' => false,
        ],
        [
            'story' => 'Someone called me back and explained everything before I signed. I knew what I was agreeing to.',
            'name' => 'Salaried employee',
            'role' => 'Sample story',
            'loan' => 'Salary-Based Loans',
            'consented' => false,
        ],
    ],
    'testimonials' => [
        [
            'quote' => 'I needed stock before the holiday rush. They told me exactly what to bring '
                .'and exactly what it would cost, before I signed anything.',
            'name' => 'Grocery retailer',
            'role' => 'Sample quote',
            'initials' => 'GR',
            'consented' => false,
        ],
        [
            'quote' => 'What I remember is that the officer explained the total I would repay, and '
                .'then wrote it down. I had been turned down twice before without anyone telling me why.',
            'name' => 'Transport operator',
            'role' => 'Sample quote',
            'initials' => 'TO',
            'consented' => false,
        ],
        [
            'quote' => 'My income moves with the season, and they built the schedule around that '
                .'instead of asking me to pretend it does not.',
            'name' => 'Agricultural supplier',
            'role' => 'Sample quote',
            'initials' => 'AS',
            'consented' => false,
        ],
    ],

    /*
    | FAQ -----------------------------------------------------------------
    |
    | Rendered with native <details> elements, so no JavaScript is required.
    |
    | Answers are drawn from the client-supplied "BMC Website Profile"
    | (received 2026-09-25) -- its products, its "How to apply" steps and its
    | KYC list -- plus promises the site already makes elsewhere (the one
    | working day decision on complete applications, the early-settlement
    | policy, the three schedule options in "Why us").
    |
    | Deliberately NOT stated, because nothing supplied confirms them:
    | payment channels (bank, debit order, mobile money), credit-bureau
    | reporting, late-payment fees, same-day disbursement. Add them only
    | when the client confirms how they actually work.
    |
    | Held to five. Questions another section already answers are left out:
    | how to apply ("How it works") and the advisory service ("Why us").
    */
    'faqs' => [
        [
            'question' => 'How much can I borrow?',
            'answer' => 'It depends on the product (salary-based, educational, agricultural, women '
                .'empowerment or SME bridging finance) and on what your income or turnover can '
                .'support. Your limit is set during credit appraisal, and you see it in writing, with '
                .'every fee and the total repayable, before you accept.',
        ],
        [
            'question' => 'What documents do I need?',
            'answer' => 'A valid national ID and your latest payslip. If you are applying for an SME '
                .'loan, also bring your company documents and financials. If your application needs '
                .'anything else, our team will tell you during appraisal.',
        ],
        [
            'question' => 'How quickly can I receive funds?',
            'answer' => 'Complete applications get a decision within one working day. Once your loan '
                .'is approved, funds are disbursed on the terms you agreed. An application that is '
                .'missing documents takes longer, so it is worth bringing everything on the first '
                .'visit.',
        ],
        [
            'question' => 'How does repayment work?',
            'answer' => 'You agree a repayment schedule before you accept: weekly, fortnightly or '
                .'monthly, built around how you earn. If you settle early, you pay less interest, and '
                .'there is no early-settlement penalty.',
        ],
        [
            'question' => 'What happens if I miss a payment?',
            'answer' => 'Talk to us before the due date if you can. It is far easier to work out a plan '
                .'in advance than after a payment has been missed. What a missed payment means for '
                .'you is set out in your loan agreement.',
        ],
    ],

    /*
    | Calculator ----------------------------------------------------------
    |
    | Bounds for the estimate UI. There is deliberately NO repayment formula
    | anywhere in this project - see the note in the calculator partial.
    */
    'calculator' => [
        'amount' => ['min' => 1000, 'max' => 500000, 'step' => 1000, 'default' => 75000],
        'terms' => [3, 6, 12, 24],
        'default_term' => 6,
        'purposes' => [
            'Salary-Based Loan',
            'Educational Loan',
            'Agricultural Loan',
            'Women Empowerment Loan',
            'Youth Empowerment Loan',
            'SME Bridging Finance',
        ],
    ],

    /*
    | Eligibility -----------------------------------------------------------
    |
    | READINESS CHECK, NOT A CREDIT DECISION. The same rule as the calculator
    | applies and for the same reason: no eligibility criteria have been
    | verified for this business, so anything that returned "you qualify" or
    | "you do not qualify" would be an invented lending rule published on a
    | regulated lender's site.
    |
    | What this does instead is genuinely useful and invents nothing: it tells
    | a visitor which documents to bring. The site promises a decision in one
    | working day *on complete applications*, and the FAQ says incomplete
    | applications take longer, so helping someone arrive complete is the
    | honest version of an eligibility check.
    |
    | Both lists come from the client-supplied "BMC Website Profile"
    | (received 2026-09-25): `who` from its customer segments and product
    | list, `documents` from its KYC line -- "Valid National ID, Latest
    | Payslip ... company documents & financials for SMEs loan applications".
    | They match the "What documents do I need?" FAQ answer word for word in
    | substance; change one, change the other.
    |
    | The lists are short because the client's list is short. Earlier drafts
    | also asked for bank statements and proof of address; nothing supplied
    | confirms Baardy asks for them, so they were removed rather than padded
    | back in. Add items only when the client confirms them.
    |
    | NOT ON THE PAGE. Neither list is shown: the checklist section
    | (sections/home/eligibility.blade.php) is no longer included, and the
    | "Who we lend to" list beside the enquiry form was removed at the
    | client's request. Kept for the partial, should it return.
    */
    'eligibility' => [
        'who' => [
            'Public-sector employees and government pensioners',
            'Private-sector employees',
            'SMEs, sole traders and cross-border traders',
            'Farmers and women entrepreneurs',
        ],

        'profiles' => [
            [
                'key' => 'employee',
                'label' => 'Employee or pensioner',
                'documents' => [
                    'A valid national ID',
                    'Your latest payslip',
                ],
            ],
            [
                'key' => 'business',
                'label' => 'SME or trader',
                'documents' => [
                    'A valid national ID',
                    'Company documents',
                    'Company financials',
                ],
            ],
        ],
    ],

];
