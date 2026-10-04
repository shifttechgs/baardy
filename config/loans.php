<?php

/*
|--------------------------------------------------------------------------
| Loan pages (/loans/{slug})
|--------------------------------------------------------------------------
|
| One page per loan, so each can be found, linked and ranked for what it is.
| The loan's name, photograph, amount range and term come from
| config/marketing.php -> products, matched by `slug`; this file holds only
| the words that belong to the page.
|
| EVERY CLAIM HERE IS ONE THE CLIENT HAS ALREADY MADE: the product summaries
| and "best for" lines of the "BMC Website Profile", the two-step document
| list, the one-working-day decision on complete applications, and the
| no-penalty early settlement. Nothing about interest rates, eligibility
| rules, ages, security or timings has been added, because none has been
| supplied. Add such detail here, and only here, when the client confirms it.
|
|   title      the <title>, kept under about 60 characters
|   meta       the search-result description, under about 160
|   headline   the page's <h1>; names the loan and the place
|   intro      two short paragraphs under the headline
|   uses       what the loan is for
|   audience   who it is meant for
|   profile    which document list applies: `employee` (ID, payslip), `business`
|              (ID, company documents and financials) -- both from config
|              marketing.eligibility.profiles -- or `general`, for loans whose
|              own list the client has not given: the national ID every
|              application needs, and a line that the team will say if
|              anything else is required
|   keywords   plain-language questions the page answers, for the FAQ below
|
| Wording is deliberately free of "guaranteed", "instant" and "no credit
| check": see docs/SEO.md §3.
*/

return [

    'salary-based-loans' => [
        'title' => 'Salary-Based Loans in Zimbabwe | Baardy Micro Capital',
        'meta' => 'Short-term salary-based loans for government employees, pensioners and private-sector workers in Zimbabwe, from a licensed lender in Harare and Bulawayo.',
        'headline' => 'Salary-based loans for employees and pensioners in Zimbabwe',
        'intro' => [
            'A salary-based loan is short-term financing for the moments between pay days: a medical bill, a family obligation, a move, an unexpected cost. It is built for people with a regular income, so what you can borrow is matched to what that income can comfortably repay.',
            'We lend to public-sector employees, government pensioners and private-sector employees. A person reviews your application, and you see the full cost in writing before you accept.',
        ],
        'uses' => [
            'Medical and emergency expenses',
            'Family and household obligations',
            'Moving, settling in or setting up a home',
            'Bridging the gap until your next pay day',
        ],
        'audience' => [
            'Public Service Commission employees',
            'Government pensioners',
            'Private-sector employees',
        ],
        'profile' => 'employee',
    ],

    'educational-loans' => [
        'title' => 'Educational Loans and School Fees Loans | Baardy',
        'meta' => 'Affordable educational loans in Zimbabwe to help families and students pay school and tuition fees. Licensed lender with branches in Harare and Bulawayo.',
        'headline' => 'Educational loans for school and tuition fees in Zimbabwe',
        'intro' => [
            'School and tuition fees fall due all at once, usually before the money to cover them has arrived. An educational loan spreads that cost over months you can plan for, so a child or student does not miss a term.',
            'The loan supports education expenses at various levels, for parents, guardians and students. You agree the repayment schedule before you accept, and the total you will repay is written down first.',
        ],
        'uses' => [
            'School fees, from primary school up',
            'College and university tuition',
            'Education costs that fall due before your income arrives',
        ],
        'audience' => [
            'Parents and guardians funding a child\'s schooling',
            'Students funding their own tuition',
            'Employed families who need fees spread across the term',
        ],
        'profile' => 'general',
    ],

    'agricultural-loans' => [
        'title' => 'Agricultural Loans for Farmers in Zimbabwe | Baardy',
        'meta' => 'Agricultural loans for Zimbabwean farmers: financing for inputs, production and other farming needs from a licensed microfinance lender in Harare and Bulawayo.',
        'headline' => 'Agricultural loans for farmers and producers in Zimbabwe',
        'intro' => [
            'A season starts with costs: seed, fertiliser, labour, equipment. The income comes later, at harvest. An agricultural loan covers the gap, so the crop goes in on time and the farm does not stall for want of inputs.',
            'We finance inputs, production and other farming requirements for farmers and agricultural producers. Repayments are agreed before you accept, built around how and when you earn.',
        ],
        'uses' => [
            'Seed, fertiliser and other inputs',
            'Production costs through the season',
            'Other farming requirements, such as tools and labour',
        ],
        'audience' => [
            'Farmers funding a season\'s inputs',
            'Agricultural producers growing their output',
            'Smallholders who need working capital before harvest',
        ],
        'profile' => 'general',
    ],

    'women-empowerment-loans' => [
        'title' => 'Women Empowerment Loans in Zimbabwe | Baardy',
        'meta' => 'Women empowerment loans in Zimbabwe: finance to start or grow a business and strengthen livelihoods. Licensed lender, branches in Harare and Bulawayo.',
        'headline' => 'Women empowerment loans for entrepreneurs in Zimbabwe',
        'intro' => [
            'Women run a large share of the small businesses that keep households and communities going, and are too often the last to be offered capital. This loan is designed to change that: financial support to promote entrepreneurship and strengthen livelihoods.',
            'It is for women entrepreneurs and women-led businesses. Every application is read by a person, and the cost is in writing before you commit.',
        ],
        'uses' => [
            'Starting a business',
            'Stock and working capital for a business you already run',
            'Growing a shop, stall or service',
        ],
        'audience' => [
            'Women entrepreneurs',
            'Women-led businesses',
            'Women starting out on their own',
        ],
        'profile' => 'general',
    ],

    'youth-empowerment-loans' => [
        'title' => 'Youth Empowerment Loans in Zimbabwe | Baardy',
        'meta' => 'Youth empowerment loans in Zimbabwe: finance for young entrepreneurs to start and grow a business. Licensed lender in Harare and Bulawayo.',
        'headline' => 'Youth empowerment loans for young entrepreneurs in Zimbabwe',
        'intro' => [
            'Young people with a good idea often have no one to lend to them: no payslip and no borrowing history. This loan is financial support to help young people start and grow a business, build their livelihoods and create opportunity for others.',
            'A person reads your application and talks it through with you. Thin credit files are normal here, and the full cost is written down before you accept.',
        ],
        'uses' => [
            'Starting a business',
            'Equipment, stock and start-up costs',
            'Growing a business you have already begun',
        ],
        'audience' => [
            'Young entrepreneurs starting a business',
            'Young people growing a trade they already run',
        ],
        'profile' => 'general',
    ],

    'sme-bridging-finance' => [
        'title' => 'SME Bridging Finance and Working Capital | Baardy',
        'meta' => 'Short-term working capital for SMEs, sole traders and cross-border traders in Zimbabwe: start-up, working capital and recapitalisation from a licensed lender.',
        'headline' => 'SME bridging finance and working capital in Zimbabwe',
        'intro' => [
            'A supplier wants payment now, a customer will pay next month, and a good order will not wait. SME bridging finance is short-term working capital that carries a business across that gap.',
            'We lend to SMEs, sole traders and cross-border traders for business start-up, working capital and recapitalisation. For this loan, bring your company documents and financials as well as your ID.',
        ],
        'uses' => [
            'Working capital for stock, wages and suppliers',
            'Business start-up costs',
            'Recapitalising a business after a difficult period',
        ],
        'audience' => [
            'Small and medium-sized businesses',
            'Sole traders',
            'Cross-border traders',
        ],
        'profile' => 'business',
    ],

];
