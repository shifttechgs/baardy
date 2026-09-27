<?php

/*
|--------------------------------------------------------------------------
| Company profile
|--------------------------------------------------------------------------
|
| Identity, contact details and navigation for the public marketing site.
|
| Values marked VERIFIED are sourced from the Reserve Bank of Zimbabwe's
| published registers of licensed microfinance institutions, retrieved
| 2026-09-22. Every value marked PLACEHOLDER is provisional and must be
| replaced with client-supplied information before the site is published.
|
| See docs/COMPANY.md for the source of every verified value, and for the
| caveats attached to each one.
|
*/

return [

    /*
    | VERIFIED — registered legal entity. 'name' is the short form used in
    | headings and meta tags; confirm with the client that the business
    | actually trades as "Baardy" rather than the full entity name.
    */
    'name' => 'Baardy',
    'legal_name' => 'Baardy Micro Capital (Pvt) Ltd',

    // One sentence describing what the company does. Used in meta tags.
    'description' => 'Salary-based, educational, agricultural, SME and women-empowerment '
        .'loans from a licensed Zimbabwean microfinance institution, with a decision made '
        .'by a person and the full cost written out before you commit.',

    /*
    | Currency used to render every monetary figure. Amounts are stored as
    | plain integers throughout so the symbol and formatting can change
    | without touching a single view.
    |
    | CONFIRMED BY THE CLIENT: the US dollar.
    |
    | This matters more here than in most markets. Zimbabwe runs a
    | multi-currency regime — both the ZiG (ZWG) and the US dollar are legal
    | tender — so a bare "$" is genuinely ambiguous to a Zimbabwean reader, and
    | the local convention for disambiguating is "US$".
    |
    | The symbol is "$" as instructed. If figures should read US$10,000 rather
    | than $10,000, change `symbol` here and every amount on the site follows.
    */
    'currency' => [
        'symbol' => '$',    // CONFIRMED — US dollar
        'code' => 'USD',    // CONFIRMED
    ],

    /*
    | Address is VERIFIED. Phone and email come from the RBZ register of
    | 31 December 2018 and are roughly eight years old, so both are held back
    | until the client reconfirms them:
    |
    |   phone  0772 550 189  (a second listed number, 777254, is printed
    |                         incomplete by RBZ and must not be published)
    |   email  baardymicrocapital@gmail.com  (recommend a domain mailbox
    |                         before launch — a Gmail address undercuts the
    |                         trust positioning on a lending site)
    */
    /*
    | The single number and address shown in the header, the closing call to
    | action and the top of the footer. This is the head office line; the
    | per-branch numbers live in `branches` below.
    |
    | Views strip the spaces to build the tel: href, so the number is stored in
    | its readable form here.
    */
    'contact' => [
        'phone' => '+263 772 550 189',             // CONFIRMED by the client
        'phone_label' => 'Mon–Fri, 08:00–17:00',   // PLACEHOLDER — real opening hours
        'email' => 'hello@example.com',            // PLACEHOLDER — reconfirm with client
    ],

    /*
    | Where the "Get in touch" form delivers enquiries. Set ENQUIRIES_TO in
    | the environment to the client's real inbox before launch -- until then
    | the fallback is the placeholder contact address, and with the default
    | `log` mailer nothing leaves the machine.
    */
    'enquiries' => [
        'to' => env('ENQUIRIES_TO', 'hello@example.com'),   // PLACEHOLDER — client inbox
    ],

    /*
    |--------------------------------------------------------------------------
    | Branches
    |--------------------------------------------------------------------------
    |
    | Two offices. The first is the head office and is the one the RBZ register
    | carries; the second was supplied by the client.
    |
    | `tel` is the dialable form used in the href and must stay in full
    | international format. `display` is what a reader sees, and is only set
    | where the client's own material prints the number differently.
    |
    | This array drives the footer, and it is what `LocalBusiness` schema should
    | be generated from when that is added -- a multi-branch lender needs one
    | entry per location, not one for the company.
    */
    'branches' => [
        [
            'name' => 'Harare',
            'role' => 'Head office',
            // VERIFIED against the RBZ register (2015 and 2018 editions).
            'address' => [
                'Office 400, 4th Floor, Construction House',
                '108–110 Leopold Takawira Street',
                'Harare, Zimbabwe',
            ],
            /*
            | The first number is CONFIRMED BY THE CLIENT, and it matches the
            | RBZ 2018 register (0772 550 189) -- an independent corroboration
            | eight years apart.
            |
            | The remaining three numbers come from the client-supplied "BMC
            | Website Profile" (received 2026-09-25), which prints them in
            | full. The RBZ register's own second number, 777254, was
            | previously withheld here because that register printed it
            | incomplete; the client's own profile now gives the complete
            | landline (0242-777254) and two mobile numbers, so all three are
            | published. This document does not mention +263 772 550 189 at
            | all -- confirm with the client which number(s) should be
            | primary before launch.
            */
            'phones' => [
                ['label' => 'Cell', 'tel' => '+263772550189', 'display' => '+263 772 550 189'],
                ['label' => 'Tel', 'tel' => '+263242777254', 'display' => '0242-777254'],
                ['label' => 'Call', 'tel' => '+263787417155', 'display' => '+263 787 417 155'],
                ['label' => 'Call/WhatsApp', 'tel' => '+263715351004', 'display' => '+263 715 351 004'],
            ],
        ],
        [
            'name' => 'Bulawayo',
            'role' => 'Branch',
            /*
            | Supplied by the client as a photograph of printed material. The
            | building name was read from a soft image and should be confirmed:
            | "Mership House" is the most likely reading, but the print is not
            | sharp enough to rule out a variant spelling.
            */
            'address' => [
                '3rd Floor, Mership House',
                'Cnr 9th Avenue and J. Nkomo Street',
                'Bulawayo, Zimbabwe',
            ],
            'phones' => [
                ['label' => 'Tel', 'tel' => '+263292883657', 'display' => '(0)29 2883 657'],
                ['label' => 'Cell', 'tel' => '+263775399113', 'display' => '+263 775 399 113'],
                ['label' => 'Cell', 'tel' => '+263715351013', 'display' => '+263 715 351 013'],
            ],
        ],
    ],

    /*
    | Regulatory disclosure.
    |
    | Regulator and licence category are VERIFIED against the RBZ register as
    | at 31 March 2026, where the company is listed as a credit-only
    | microfinance institution.
    |
    | The licence number is taken from the RBZ register of 31 December 2015,
    | the last edition to publish a licence-number column. It is the single
    | highest-consequence figure on the site: CONFIRM IT AGAINST THE PHYSICAL
    | LICENCE CERTIFICATE before the site goes live.
    |
    | The company registration number appears in no public register and must
    | come from the client.
    |
    | Note: 'credit-only' means deposit-taking is statutorily prohibited. No
    | copy anywhere on this site may imply savings, deposits, wallets or
    | account balances, or describe the business as a bank.
    */
    'compliance' => [
        'regulator' => 'the Reserve Bank of Zimbabwe',       // VERIFIED
        'licence' => '658',                                  // VERIFIED (2015) — confirm vs certificate
        'licence_category' => 'credit-only microfinance',    // VERIFIED
        'licensed_since' => '2015',                          // VERIFIED — absent Jun 2015, listed Sep 2015
        'registration' => '[PLACEHOLDER — company registration number]',

        /*
        | The public register a visitor can check the licence against. This
        | exact document was retrieved and read on 22 September 2026; Baardy is
        | entry 22 in the credit-only section.
        |
        | IT IS A DATED FILE. RBZ publishes a new register periodically and old
        | URLs are not guaranteed to survive. Check this link when reviewing the
        | site, and update it when a newer register is published — a verify
        | link that 404s is worse than no verify link.
        */
        'register_url' => 'https://www.rbz.co.zw/documents/bank_sup/Registered_Microfinance_/LIST_OF_REGISTERED_MICROFINANCE_INSTIUTIIONS_AS_AT_31_MARCH_2026.pdf',
    ],

    /*
    | Primary navigation.
    |
    | An item with `menu` opens a panel that grows out of the header (see
    | components/layout/navbar): `loans` lists marketing.products, `insights`
    | the newest articles, `about` (labelled Company) the cards in nav_about
    | below. `href` is
    | where the item goes without JavaScript, and on the mobile panel's
    | section heading.
    */
    'nav' => [
        ['label' => 'Loans', 'href' => '/#products', 'menu' => 'loans'],
        ['label' => 'Company', 'href' => '/#trust', 'menu' => 'about'],
        ['label' => 'Insights', 'href' => '/insights', 'menu' => 'insights'],
        ['label' => 'FAQs', 'href' => '/#faq'],
    ],

    /*
    | The Company menu. Each card restates what its section or page already
    | says; nothing here makes a new claim. "Who we are" repeats the verified
    | facts of the section under the hero (#trust) -- the licence, the year
    | of first listing and the two offices. A fuller /about page belongs here
    | once the client supplies its history, mission and leadership.
    */
    'nav_about' => [
        ['label' => 'Who we are', 'description' => 'A licensed microfinance institution on the Reserve Bank of Zimbabwe register since 2015, in Harare and Bulawayo.', 'href' => '/#trust'],
        ['label' => 'Why borrow from us', 'description' => 'Repayments that follow your cash, free advisory, and your information kept private.', 'href' => '/#why-us'],
        ['label' => 'How it works', 'description' => 'Three steps from a branch visit to funds, on paper at every step.', 'href' => '/#how-it-works'],
        ['label' => 'Responsible lending', 'description' => 'How we lend, and what to do if repaying becomes hard.', 'href' => '/responsible-lending'],
        ['label' => 'Get in touch', 'description' => 'Talk to a person, by phone or WhatsApp, or send us a message.', 'href' => '/#contact'],
    ],

    'cta' => [
        'primary' => ['label' => 'Start an application', 'href' => '/#contact'],
        'secondary' => ['label' => 'See how it works', 'href' => '/#how-it-works'],
    ],

    /*
    | Footer columns. Every link goes somewhere real: a section of the
    | homepage, /insights, or one of the legal pages below. A link whose
    | page does not exist yet does not belong here -- add it with its page.
    */
    'footer' => [
        'Borrow' => [
            ['label' => 'Salary-based loans', 'href' => '/#products'],
            ['label' => 'Educational loans', 'href' => '/#products'],
            ['label' => 'Agricultural loans', 'href' => '/#products'],
            ['label' => 'Women empowerment loans', 'href' => '/#products'],
            ['label' => 'SME bridging finance', 'href' => '/#products'],
        ],
        'Company' => [
            ['label' => 'Why borrow from us', 'href' => '/#why-us'],
            ['label' => 'How it works', 'href' => '/#how-it-works'],
            ['label' => 'Insights', 'href' => '/insights'],
        ],
        'Support' => [
            ['label' => 'FAQs', 'href' => '/#faq'],
            ['label' => 'Contact us', 'href' => '/#contact'],
            ['label' => 'Struggling to repay?', 'href' => '/responsible-lending#struggling-to-repay'],
            ['label' => 'Make a complaint', 'href' => '/complaints'],
        ],
        'Legal' => [
            ['label' => 'Privacy notice', 'href' => '/privacy'],
            ['label' => 'Terms of use', 'href' => '/terms'],
            ['label' => 'Responsible lending', 'href' => '/responsible-lending'],
            ['label' => 'Complaints procedure', 'href' => '/complaints'],
        ],
    ],

    /*
    | Legal pages, in the order the legal pages' sidebar lists them. Each is
    | a view at pages/legal/{view} served by its named route (routes/web.php).
    |
    | DRAFTED FOR THE CLIENT'S REVIEW. The text describes only what this site
    | actually does (see each view's header note) and restates promises the
    | site already makes. It has not been reviewed by the client's legal
    | adviser -- that review is a go-live requirement.
    */
    'legal' => [
        ['label' => 'Privacy notice', 'route' => 'legal.privacy'],
        ['label' => 'Terms of use', 'route' => 'legal.terms'],
        ['label' => 'Responsible lending', 'route' => 'legal.responsible-lending'],
        ['label' => 'Complaints procedure', 'route' => 'legal.complaints'],
    ],

    /*
    | Social profiles. `href` is null until the client confirms the account;
    | the footer shows only profiles with a real address.
    */
    'social' => [
        ['label' => 'LinkedIn', 'href' => null, 'icon' => 'linkedin'],
        ['label' => 'X', 'href' => null, 'icon' => 'x'],
        ['label' => 'Facebook', 'href' => null, 'icon' => 'facebook'],
    ],

];
