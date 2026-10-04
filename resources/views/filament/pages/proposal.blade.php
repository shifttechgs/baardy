{{--
    The full proposal, readable inside the panel. Same content as the shareable
    proposal page; the lists are data at the top so the wording is easy to edit.
    The calculator runs in the browser on the reader's own numbers (Alpine).
--}}
@php
    $contents = [
        'shift' => 'What changes',
        'paper' => 'Paper',
        'built' => 'Already built',
        'modules' => 'What we would build',
        'whowhen' => 'Who and when',
        'automation' => 'What runs by itself',
        'pays' => 'Where it pays',
        'price' => 'What it costs',
        'phases' => 'Phases',
        'needs' => 'What we need',
    ];

    $shift = [
        ['Client files', 'A folder per client at one branch, copies in a spreadsheet.', 'One client record with ID, employer or business, guarantors and scanned documents, visible to the right staff at any branch.'],
        ['The loan book', 'A sheet that someone updates by hand and that is only as current as its last edit.', 'Every loan with its amount, term, rate, fees and balance, always current, never retyped.'],
        ['Repayments', 'Payments matched to a schedule by eye.', 'Each payment matched to its instalment. Part payments, early settlement and arrears worked out for you.'],
        ['Who to call', 'Whoever remembers, or whoever checks the sheet.', 'A worklist each morning: due today, late, and promised payments, per officer.'],
        ['Approvals', 'A signature on a form, found later in a drawer.', 'The application, who appraised it, who approved it and when, kept with the loan.'],
        ['Reports', 'Built at month end from several sheets.', 'The loan book, collections and loans at risk by branch, product and officer, whenever you open it.'],
        ['Accountability', 'Hard to say who changed a figure.', 'Every change recorded with the person and the time. Nothing disappears.'],
    ];

    $paper = [
        ['Printed from the system', 'Loan agreements, repayment schedules, statements and receipts come out ready to print, already filled in from the record. Nobody types the same details twice.'],
        ['Signed on paper, kept in the system', 'When a client signs, the signed copy is scanned and filed against their loan. The paper goes in the cabinet. The record stays on screen, searchable and backed up.'],
        ['One live record', 'Staff work from the screen at any branch. They are never hunting for a folder, comparing two versions or wondering which sheet is current.'],
    ];

    $built = [
        ['Leads and funnel', 'Every website enquiry is captured, assigned and tracked from first call to approved or lost, with a timeline of each contact.'],
        ['Owner dashboard', 'Enquiries, conversion and speed to first call against the previous 30 days, with branch, product and source charts.'],
        ['Promotions', 'Offers on the website with sign-off, scheduling, and tagged links that show which channel brought each enquiry.'],
        ['Careers', 'Open roles, online applications with CVs, and a reader to review them without downloading.'],
        ['Staff access', 'Sign-in for admin users only, with a pulse in the menu where something needs attention.'],
    ];

    $modules = [
        ['Clients', 'Build first', 'Who is this person, and what have we agreed with them?', ['Identity, contacts, employer or business, and next of kin.', 'Guarantors and their documents, scanned and stored with the record.', 'A lead becomes a client in one step when a loan is approved.', 'Search by name, phone or ID across all branches.'], 'client folders and the contact spreadsheet.'],
        ['Loans', 'Build first', 'What do we lend, to whom, and who approved it?', ['Application, appraisal and approval with the officer on every file.', 'Products set up once: rates, fees, terms and limits.', 'Repayment schedule generated at disbursement, weekly, fortnightly or monthly.', 'Approval steps that match your own process and limits.'], 'the loan register and paper approval forms.'],
        ['Repayments', 'Phase 2', 'What is due, when, and what has been paid?', ['Record each payment against its instalment, with a receipt.', 'Part payments, early settlement and penalties worked out by rule.', 'Balance and arrears per loan, updated as payments arrive.', 'Import payroll deduction schedules for salary-based loans.'], 'manual payment tracking and balance sheets.'],
        ['Collections', 'Phase 2', 'Who do we need to speak to today?', ['A daily worklist per officer: due today, late, promised.', 'Call, WhatsApp and note on the same screen, logged as you go.', 'Promises to pay with a date, so a broken promise is flagged.', 'Loans at risk grouped by how late they are.'], 'phoning from a spreadsheet and remembering who said what.'],
        ['Team and branches', 'Phase 3', 'Who did this, and were they allowed to?', ['An account per person with a role: officer, branch manager, owner.', 'Limits by role, such as the largest loan a person can approve.', 'Branch targets and results next to each other.', 'A complete activity log that nobody can edit.'], 'shared passwords, shared files and trust.'],
        ['Reports', 'Phase 3', 'How is the business doing, and where is it slipping?', ['The loan book, disbursements and collections by branch, product and officer.', 'Loans at risk and how the book is ageing.', 'Print or export to Excel in layouts we agree with you.', 'The owner dashboard fed by real loan figures, not only enquiries.'], 'month-end spreadsheet building.'],
    ];

    $principles = [
        ['Each person has their own sign-in', 'No shared passwords, so every line has a name against it.'],
        ['Roles decide what a person can do', 'An officer works their own clients. A manager approves within a limit. The owner sees everything.'],
        ['Nothing is deleted quietly', 'Changes are recorded with the old and new value. Corrections add a line and never erase one.'],
        ['Dates drive the work', 'Due dates, promised payments and follow-ups all appear on the right person\'s list on the right day.'],
    ];

    $timeline = [
        ['3 Mar, 09:12', 'Application received', 'Entered by a branch officer', false],
        ['4 Mar, 11:40', 'Appraised', 'Credit officer, payslips verified', false],
        ['5 Mar, 15:05', 'Approved', 'Branch manager, within approval limit', false],
        ['6 Mar, 10:30', 'Disbursed, schedule created', 'Twelve monthly instalments', false],
        ['30 Apr, 08:00', 'Reminder sent', 'Automatic, two days before due', false],
        ['2 May, 16:20', 'Instalment missed', 'Added to the officer\'s list for 3 May', true],
        ['3 May, 09:45', 'Promise to pay on 10 May', 'Collections officer, after a call', false],
    ];

    $automation = [
        ['Repayment schedules', 'Created the moment a loan is disbursed, from the product\'s own rules.'],
        ['Payment reminders', 'SMS or WhatsApp to the client before an instalment is due, and again if it is missed.'],
        ['Daily worklists', 'Each officer opens the system to a list of who to call, already sorted by urgency.'],
        ['Arrears and ageing', 'Every loan is aged daily, so loans at risk are known before they become a loss.'],
        ['Penalties and interest', 'Worked out by the rules you set, the same way every time.'],
        ['Alerts to the owner', 'An approval waiting, a branch behind target, a large loan approved. Sent to you, not searched for.'],
        ['Statements and receipts', 'Produced for the client on request, with the balance and what was paid.'],
        ['Import from Excel', 'Existing clients and loans loaded from your current sheets, so nobody retypes the book.'],
    ];

    $levers = [
        ['Money comes in sooner', 'Reminders before a due date and a worklist after it get more instalments paid on time, and late ones chased the same day.'],
        ['Loans move faster', 'An application goes from entry to appraisal to approval without paper changing hands, so good clients are served before they go elsewhere.'],
        ['Losses show early', 'Every loan is aged daily. You see a slipping loan at one week late, not at three months.'],
        ['No leakage', 'Penalties and balances are worked out by rule every time. A payment cannot be missed because a sheet was not updated.'],
        ['Lend where it works', 'See which branch, product and officer perform, and put more money where it comes back.'],
        ['Staff time back', 'Hours spent retyping, filing and chasing go to speaking with clients and winning new ones.'],
    ];

    $phases = [
        ['Clients and loans', 'Client records, loan products, applications, approvals and repayment schedules. Your existing clients and loans are imported from Excel.', 'open any client or loan on screen and see its full history.'],
        ['Repayments and collections', 'Payments, arrears, daily worklists and automatic reminders. Payroll deduction schedules for government employees.', 'stop chasing from spreadsheets and see the arrears position every morning.'],
        ['Team, branches and reports', 'Roles and limits, branch targets, the full activity log, and the report layouts agreed with you.', 'run the organisation from the owner dashboard with real loan figures.'],
    ];

    $needs = [
        ['Loan product terms', 'Rates, fees, penalties, terms and limits for each product.'],
        ['Your current spreadsheets', 'Client lists and the loan register, so we can plan the import.'],
        ['The approval process', 'Who appraises, who approves, and the limits at each level.'],
        ['Staff and branches', 'Who works where, and what each person should be able to do.'],
        ['Payroll deduction process', 'How salary-based loans are collected from government employers today.'],
        ['Reports you must produce', 'The layouts you give your regulator and your board. We will check them against the regulator\'s requirements with you.'],
        ['Messaging preferences', 'SMS, WhatsApp or both, and the wording clients should receive.'],
        ['Hosting and data decisions', 'Where the data should be kept, who may access it, and how often it is backed up.'],
    ];
@endphp

<x-filament-panels::page>
    {{--
        The section links light up for the section you are in. Clicking one marks
        it at once and holds that for a moment while the page scrolls there; after
        that the active link follows the reader down the page.
    --}}
    <div
        class="flex flex-col gap-14"
        x-data="{
            active: @js(array_key_first($contents)),
            holding: false,
            choose(id) {
                this.active = id;
                this.holding = true;
                setTimeout(() => this.holding = false, 900);
            },
            follow() {
                if (this.holding) {
                    return;
                }

                const line = window.innerHeight * 0.3;
                let current = null;

                this.$root.querySelectorAll('section[id]').forEach((section) => {
                    if (section.getBoundingClientRect().top <= line) {
                        current = section.id;
                    }
                });

                if (current) {
                    this.active = current;
                }
            },
            init() {
                const start = window.location.hash.slice(1);

                if (start && this.$root.querySelector('section#' + CSS.escape(start))) {
                    this.active = start;
                }
            },
        }"
        x-on:scroll.window.passive.throttle.60ms="follow()"
    >

        <nav aria-label="Sections" class="sticky top-16 z-10 -mt-2 flex flex-wrap gap-2 bg-[var(--baardy-page)] py-3">
            @foreach ($contents as $anchor => $label)
                <a
                    href="#{{ $anchor }}"
                    x-on:click="choose('{{ $anchor }}')"
                    x-bind:aria-current="active === '{{ $anchor }}' ? 'true' : null"
                    x-bind:class="active === '{{ $anchor }}'
                        ? 'bg-primary-700 text-white ring-primary-700'
                        : 'bg-white text-gray-700 ring-gray-950/10 hover:text-primary-700 hover:ring-primary-600'"
                    class="rounded-full px-3 py-1.5 text-sm ring-1 transition"
                >{{ $label }}</a>
            @endforeach
        </nav>

        <p class="max-w-3xl text-lg leading-relaxed text-gray-600">
            Loans, clients, repayments and staff in one place. The system reminds, calculates and records, so your people spend their time lending and collecting, and you can see the money moving.
        </p>

        {{-- What changes --}}
        <section id="shift" class="flex scroll-mt-24 flex-col gap-5">
            <header class="flex max-w-3xl flex-col gap-1">
                <h2 class="text-2xl font-medium tracking-tight text-gray-950">What changes day to day</h2>
                <p class="text-sm text-gray-600">The work stays the same. What changes is where it lives, and who can see it.</p>
            </header>

            <div class="overflow-x-auto rounded-xl bg-white ring-1 ring-gray-950/5">
                <table class="w-full min-w-[40rem] text-left text-sm">
                    <thead class="bg-gray-50 text-xs tracking-wide text-gray-500 uppercase">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-medium">Area</th>
                            <th scope="col" class="px-5 py-3 font-medium">Paper and Excel today</th>
                            <th scope="col" class="px-5 py-3 font-medium">In the system</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($shift as [$area, $today, $system])
                            <tr class="align-top">
                                <td class="w-1/5 px-5 py-4 font-medium text-gray-950">{{ $area }}</td>
                                <td class="w-2/5 px-5 py-4 text-gray-500">{{ $today }}</td>
                                <td class="px-5 py-4 text-gray-950">{{ $system }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Paper --}}
        <section id="paper" class="flex scroll-mt-24 flex-col gap-5">
            <header class="flex max-w-3xl flex-col gap-1">
                <h2 class="text-2xl font-medium tracking-tight text-gray-950">If the government wants paper, print it. Do not run the business on it.</h2>
                <p class="text-sm text-gray-600">Regulators, employers and courts may still ask for a physical copy. That is fine. The paper is something the system produces, not where your business lives.</p>
            </header>

            <div class="grid gap-4 md:grid-cols-3">
                @foreach ($paper as [$title, $body])
                    <article class="flex flex-col gap-1.5 rounded-xl bg-white p-5 ring-1 ring-gray-950/5">
                        <h3 class="text-base font-medium text-gray-950">{{ $title }}</h3>
                        <p class="text-sm leading-relaxed text-gray-600">{{ $body }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Built --}}
        <section id="built" class="flex scroll-mt-24 flex-col gap-5">
            <header class="flex max-w-3xl flex-col gap-1">
                <h2 class="text-2xl font-medium tracking-tight text-gray-950">Already built and working</h2>
                <p class="text-sm text-gray-600">This is not starting from nothing. The foundation below is live in this panel today, and the loan modules build on it.</p>
            </header>

            <ul class="divide-y divide-gray-100 rounded-xl bg-white ring-1 ring-gray-950/5">
                @foreach ($built as [$title, $body])
                    <li class="grid gap-1 px-5 py-4 md:grid-cols-[12rem_1fr_auto] md:items-baseline md:gap-5">
                        <span class="font-medium text-gray-950">{{ $title }}</span>
                        <span class="text-sm text-gray-600">{{ $body }}</span>
                        <span class="w-fit rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-700">Live</span>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Modules --}}
        <section id="modules" class="flex scroll-mt-24 flex-col gap-5">
            <header class="flex max-w-3xl flex-col gap-1">
                <h2 class="text-2xl font-medium tracking-tight text-gray-950">What we would build next</h2>
                <p class="text-sm text-gray-600">Six modules, in the order they would be built. Each one answers a question the owner or an officer asks every day.</p>
            </header>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($modules as [$title, $status, $answers, $points, $replaces])
                    <article class="flex flex-col gap-4 rounded-xl bg-white p-6 ring-1 ring-gray-950/5">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-lg font-medium tracking-tight text-gray-950">{{ $title }}</h3>
                            <span @class([
                                'rounded-full px-2.5 py-1 text-xs font-medium',
                                'bg-primary-50 text-primary-700' => $status === 'Build first',
                                'bg-gray-100 text-gray-600' => $status !== 'Build first',
                            ])>{{ $status }}</span>
                        </div>

                        <p class="text-sm text-gray-500">{{ $answers }}</p>

                        <ul class="list-disc space-y-1.5 pl-5 text-sm leading-relaxed text-gray-700 marker:text-primary-600">
                            @foreach ($points as $point)
                                <li>{{ $point }}</li>
                            @endforeach
                        </ul>

                        <p class="mt-auto border-t border-gray-100 pt-4 text-sm text-gray-500"><span class="font-medium text-gray-700">Replaces</span> {{ $replaces }}</p>

                        <p class="-mx-6 -mb-6 flex items-center gap-2 rounded-b-xl border-t border-gray-100 bg-primary-50 px-6 py-3.5 text-sm">
                            <span class="shrink-0 font-semibold whitespace-nowrap text-primary-700">30-day free trial</span>
                            <span class="text-gray-600">Try it with your own clients and loans.</span>
                        </p>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Who and when --}}
        <section id="whowhen" class="flex scroll-mt-24 flex-col gap-5">
            <header class="flex max-w-3xl flex-col gap-1">
                <h2 class="text-2xl font-medium tracking-tight text-gray-950">Always knowing who and when</h2>
                <p class="text-sm text-gray-600">Every action in the system leaves a line: who did it, what changed and when. That is the part paper and Excel cannot give you.</p>
            </header>

            <div class="grid items-start gap-6 lg:grid-cols-2">
                <div class="flex flex-col gap-4">
                    @foreach ($principles as [$title, $body])
                        <div class="border-l-2 border-primary-600 pl-4">
                            <p class="font-medium text-gray-950">{{ $title }}</p>
                            <p class="text-sm text-gray-600">{{ $body }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="rounded-xl bg-white p-5 ring-1 ring-gray-950/5" aria-label="Example loan timeline">
                    <p class="mb-4 text-xs tracking-wide text-gray-500 uppercase">Example loan timeline. Illustration only.</p>
                    <ol class="flex flex-col gap-3.5">
                        @foreach ($timeline as [$when, $what, $who, $flag])
                            <li class="grid gap-0.5 text-sm sm:grid-cols-[7.5rem_1fr] sm:gap-4">
                                <time class="pt-0.5 font-mono text-xs text-gray-500">{{ $when }}</time>
                                <div>
                                    <p @class(['font-medium', 'text-gray-950' => ! $flag, 'text-warning-700' => $flag])>{{ $what }}</p>
                                    <p class="text-gray-500">{{ $who }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </section>

        {{-- Automation --}}
        <section id="automation" class="flex scroll-mt-24 flex-col gap-5">
            <header class="flex max-w-3xl flex-col gap-1">
                <h2 class="text-2xl font-medium tracking-tight text-gray-950">What runs by itself</h2>
                <p class="text-sm text-gray-600">The point of replacing the sheets is that the system does the repetitive parts, so staff spend their time with clients.</p>
            </header>

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($automation as [$title, $body])
                    <article class="flex flex-col gap-1 rounded-xl bg-white p-4 ring-1 ring-gray-950/5">
                        <h3 class="text-sm font-medium text-gray-950">{{ $title }}</h3>
                        <p class="text-sm text-gray-600">{{ $body }}</p>
                    </article>
                @endforeach
            </div>

            <p class="max-w-3xl rounded-xl bg-warning-50 px-4 py-3 text-sm text-gray-800">
                <span class="font-medium text-warning-700">Messaging costs.</span>
                SMS and WhatsApp reminders go through a messaging provider, which charges per message. We will agree the provider with you and show the expected cost before switching them on.
            </p>
        </section>

        {{-- Where it pays --}}
        <section id="pays" class="flex scroll-mt-24 flex-col gap-5">
            <header class="flex max-w-3xl flex-col gap-1">
                <h2 class="text-2xl font-medium tracking-tight text-gray-950">Where it pays for itself</h2>
                <p class="text-sm text-gray-600">A lender makes money when loans are made quickly, paid on time and watched closely. Doing the admin by hand slows every one of those. Here is where the gain comes from.</p>
            </header>

            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($levers as [$title, $body])
                    <article class="flex flex-col gap-1 rounded-xl border-t-[3px] border-primary-600 bg-white p-4 ring-1 ring-gray-950/5">
                        <h3 class="text-sm font-medium text-gray-950">{{ $title }}</h3>
                        <p class="text-sm text-gray-600">{{ $body }}</p>
                    </article>
                @endforeach
            </div>

            <form
                class="grid overflow-hidden rounded-2xl bg-white ring-1 ring-gray-950/5 lg:grid-cols-[1.2fr_1fr]"
                x-data="{
                    fee: @js($this->priceFrom()),
                    hours: 40, rate: 4, cut: 50, due: 50000, late: 15, sooner: 25,
                    get coverage() { return this.fee && this.timeValue > 0 ? (this.timeValue / this.fee).toFixed(1) + ' times' : '—' },
                    pct(value) { return Math.min(Math.max(Number(value) || 0, 0), 100) / 100 },
                    num(value) { return Math.max(Number(value) || 0, 0) },
                    money(value) { return 'US$' + Math.round(value).toLocaleString('en-US') },
                    get hoursFreed() { return this.num(this.hours) * this.pct(this.cut) * 52 / 12 },
                    get timeValue() { return this.hoursFreed * this.num(this.rate) },
                    get cashSooner() { return this.num(this.due) * this.pct(this.late) * this.pct(this.sooner) },
                }"
                x-on:submit.prevent
            >
                <div class="flex flex-col gap-3 p-6">
                    <h3 class="text-lg font-medium text-gray-950">Work it out with your own numbers</h3>
                    <p class="text-sm text-gray-500">The values below are examples. Replace them with yours. The result is an estimate from what you enter, not a promise.</p>

                    @foreach ([
                        ['hours', 'Staff hours a week spent on manual admin', 1],
                        ['rate', 'Cost of one staff hour (US$)', 0.5],
                        ['cut', 'Share of that admin the system takes over (%)', 5],
                        ['due', 'Instalments due each month (US$)', 1000],
                        ['late', 'Share of those paid late (%)', 1],
                        ['sooner', 'Share of late payments reminders bring in sooner (%)', 5],
                    ] as [$model, $label, $step])
                        <label class="flex flex-col gap-1 text-sm text-gray-600" for="calc-{{ $model }}">
                            {{ $label }}
                            <input
                                id="calc-{{ $model }}"
                                type="number"
                                min="0"
                                step="{{ $step }}"
                                inputmode="decimal"
                                x-model.number="{{ $model }}"
                                class="w-full rounded-lg border-0 bg-gray-50 px-3 py-2 text-base text-gray-950 tabular-nums ring-1 ring-gray-200 focus:bg-white focus:ring-2 focus:ring-primary-600"
                            >
                        </label>
                    @endforeach
                </div>

                <div class="flex flex-col justify-center gap-5 bg-primary-50 p-6" aria-live="polite">
                    <div>
                        <p class="text-sm text-gray-600">Staff hours freed each month</p>
                        <p class="font-mono text-3xl font-medium tracking-tight text-primary-700 tabular-nums" x-text="Math.round(hoursFreed).toLocaleString('en-US')">0</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Value of that time each month</p>
                        <p class="font-mono text-3xl font-medium tracking-tight text-primary-700 tabular-nums" x-text="money(timeValue)">US$0</p>
                    </div>
                    @if ($this->priceFrom())
                        <div>
                            <p class="text-sm text-gray-600">Staff time covers the starting price (US${{ $this->priceFrom() }} a month)</p>
                            <p class="font-mono text-3xl font-medium tracking-tight text-primary-700 tabular-nums" x-text="coverage">—</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-600">Cash arriving sooner each month</p>
                        <p class="font-mono text-3xl font-medium tracking-tight text-primary-700 tabular-nums" x-text="money(cashSooner)">US$0</p>
                    </div>
                    <p class="text-xs text-gray-500">Cash arriving sooner is money already owed to you, collected earlier. It is not extra profit. Staff time is the saving you can count on directly.</p>
                </div>
            </form>
        </section>

        {{-- Price --}}
        <section id="price" class="flex scroll-mt-24 flex-col gap-5">
            <header class="flex max-w-3xl flex-col gap-1">
                <h2 class="text-2xl font-medium tracking-tight text-gray-950">What it costs</h2>
                <p class="text-sm text-gray-600">A simple monthly price, and a free first month to prove it works for you.</p>
            </header>

            <div class="grid max-w-3xl overflow-hidden rounded-2xl bg-white ring-1 ring-gray-950/5 sm:grid-cols-[14rem_1fr]">
                <div class="flex flex-col justify-center gap-1 bg-primary-50 p-6">
                    @if ($this->priceFrom())
                        <p class="text-sm text-gray-600">From</p>
                        <p class="font-mono text-5xl font-medium tracking-tight text-primary-700">US${{ $this->priceFrom() }}</p>
                        <p class="text-sm text-gray-600">a month</p>
                    @else
                        <p class="text-lg font-medium tracking-tight text-primary-700">Priced after the working session</p>
                    @endif
                </div>
                <ul class="flex flex-col justify-center gap-3 p-6 text-sm text-gray-700">
                    <li><span class="font-medium text-gray-950">Free for the first 30 days.</span> Nothing is charged until the trial ends.</li>
                    <li><span class="font-medium text-gray-950">Confirmed after the working session.</span> Your exact price depends on the size of your loan book, branches and team, and we agree it with you before the trial ends.</li>
                    <li><span class="font-medium text-gray-950">Messaging is separate.</span> SMS and WhatsApp are charged per message by the provider. We show you the cost before switching them on.</li>
                </ul>
            </div>
        </section>

        {{-- Phases --}}
        <section id="phases" class="flex scroll-mt-24 flex-col gap-5">
            <header class="flex max-w-3xl flex-col gap-1">
                <h2 class="text-2xl font-medium tracking-tight text-gray-950">How we would roll it out</h2>
                <p class="text-sm text-gray-600">In phases, so the first useful piece is in staff's hands early and each phase builds on the last. Dates are set after the discovery session.</p>
            </header>

            <ol class="flex flex-col gap-3">
                @foreach ($phases as $index => [$title, $body, $outcome])
                    <li class="grid gap-2 rounded-xl bg-white p-5 ring-1 ring-gray-950/5 sm:grid-cols-[3.5rem_1fr] sm:gap-4">
                        <span class="font-mono text-3xl leading-none text-primary-600">{{ $index + 1 }}</span>
                        <div class="flex flex-col gap-1">
                            <h3 class="text-lg font-medium text-gray-950">{{ $title }}</h3>
                            <p class="text-sm text-gray-600">{{ $body }}</p>
                            <p class="mt-1 text-sm text-gray-950"><span class="font-medium">You can then</span> {{ $outcome }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </section>

        {{-- Needs --}}
        <section id="needs" class="flex scroll-mt-24 flex-col gap-5">
            <header class="flex max-w-3xl flex-col gap-1">
                <h2 class="text-2xl font-medium tracking-tight text-gray-950">What we need from Baardy</h2>
                <p class="text-sm text-gray-600">Most of this already exists on paper or in a spreadsheet. We need to see it to set the system up correctly.</p>
            </header>

            <ul class="grid gap-x-10 md:grid-cols-2">
                @foreach ($needs as [$title, $body])
                    <li class="border-b border-gray-200 py-3">
                        <p class="font-medium text-gray-950">{{ $title }}</p>
                        <p class="text-sm text-gray-600">{{ $body }}</p>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Trial --}}
        <section class="flex flex-col gap-2 rounded-2xl bg-primary-50 p-7">
            <h2 class="text-2xl font-medium tracking-tight text-gray-950">Try it free for 30 days</h2>
            <p class="max-w-3xl text-gray-800">
                Start with a working session: the owner and a branch manager walk us through a real loan from application to final payment. We then set the system up with your own clients and loans, and you use it for 30 days before you decide anything. @if ($this->priceFrom())After the trial, pricing starts from US${{ $this->priceFrom() }} a month and is confirmed with you after the working session.@else Pricing is confirmed with you after the working session.@endif The exact scope and phase dates are written down once we have seen your process.
            </p>
        </section>

        <footer class="text-sm text-gray-500">
            <p class="max-w-3xl">Figures in the example timeline and the calculator's starting values are illustrations. Scope, timing and the final price are confirmed after the working session.</p>
        </footer>
    </div>
</x-filament-panels::page>
