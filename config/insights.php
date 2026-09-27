<?php

/*
|--------------------------------------------------------------------------
| Insights
|--------------------------------------------------------------------------
|
| Short financial-literacy articles, the written side of the Financial
| Advisory Services line in the client's profile. Metadata lives here; each
| body is a Blade partial at resources/views/insights/articles/{slug}.blade.php
| so it can carry real headings and lists without a CMS.
|
| Newest first. The homepage shows the first three.
|
| WRITING RULE: these are general guidance, not claims about Baardy's terms.
| An article may describe good practice for any borrower; it may not state a
| Baardy rate, limit, fee or turnaround the client has not confirmed.
|
| Photographs are self-hosted Unsplash stock (public/images/insights/,
| {slug}-{480,800,1600}.webp). Atmosphere, not customers.
|
|   budgeting        Sincerely Media  unsplash.com/photos/zghx6KQgZyY
|   first-loan       Annie Spratt     unsplash.com/photos/6WYXkhwj6H8
|   seasonal-farm    Ali Mkumbwa      unsplash.com/photos/s8Kzx7C6yqo
|
*/

return [

    'articles' => [
        [
            'slug' => 'budgeting-on-an-irregular-income',
            'image' => 'budgeting',
            'category' => 'Financial literacy',
            'title' => 'Budgeting when your income changes every month',
            'excerpt' => 'Traders, farmers and anyone paid by the job know a fixed monthly budget '
                .'does not fit. Here is a way of planning that does.',
            'alt' => 'A woman writing on a sheet of paper at a wooden table',
            'published' => '2026-09-18',
            'minutes' => 5,
        ],
        [
            'slug' => 'before-your-first-loan-application',
            'image' => 'first-loan',
            'category' => 'Borrowing',
            'title' => 'Five things to sort out before your first loan application',
            'excerpt' => 'A complete application is the fastest one. What to gather, what to work '
                .'out, and what to ask before you sign anything.',
            'alt' => 'Two people signing a document at a table, one pressing a thumbprint',
            'published' => '2026-09-04',
            'minutes' => 4,
        ],
        [
            'slug' => 'planning-seasonal-finance-for-your-farm',
            'image' => 'seasonal-farm',
            'category' => 'Agriculture',
            'title' => 'Planning finance around the farming season',
            'excerpt' => 'Your costs come at planting, your income at harvest. How to borrow for '
                .'inputs without the repayments arriving before the crop does.',
            'alt' => 'Grain pouring from a farmer\'s cupped hands',
            'published' => '2026-08-21',
            'minutes' => 5,
        ],
    ],

];
