{{--
    /careers -- work at Baardy.

    Vacancies come from the admin panel (Vacancy::open()). The application
    form is a plain multipart POST (no JavaScript needed) to careers.apply: the
    CV is stored in the database and emailed to the team. A role's "Apply"
    button preselects it in the form; with no open roles the form still takes
    a general application. Every claim restates something the homepage says.
--}}
@extends('layouts.marketing')

@section('title', 'Careers | '.config('company.name'))

@section('description', 'Open roles at '.config('company.legal_name').', a licensed Zimbabwean microfinance institution. Apply online and attach your CV.')

@php
    $input = 'h-13 w-full rounded-xl border border-line bg-mist px-4 text-body text-ink transition duration-200 '
        .'placeholder:text-muted/70 hover:border-line-strong focus:border-accent focus:bg-paper focus:ring-4 focus:ring-accent/10 focus:outline-none focus-visible:outline-none';
    $chosenVacancy = old('vacancy_id', request('vacancy'));
    $roleOptions = $vacancies
        ->map(fn ($vacancy): array => ['id' => (string) $vacancy->id, 'label' => $vacancy->title.' — '.$vacancy->location])
        ->prepend(['id' => '', 'label' => 'General application (keep me in mind)'])
        ->values();
@endphp

@section('content')
    <x-layout.page-header
        eyebrow="Careers"
        title="Build a career where people come first"
        lead="Join a licensed Zimbabwean lender that has served government employees, pensioners and small businesses since {{ config('company.compliance.licensed_since') }}."
    >
        @if ($vacancies->isNotEmpty())
            <x-ui.button href="#vacancies" arrow class="rise mt-8 [animation-delay:340ms]">See open roles</x-ui.button>
        @endif
    </x-layout.page-header>

    {{-- Why work here: restates the company profile, no invented perks. --}}
    <x-ui.section :rule="false" aria-label="Working at Baardy" class="pt-4! pb-16! sm:pb-20! lg:pb-24!">
        <x-ui.container wide>
            <div data-reveal-cards class="grid gap-4 lg:grid-cols-12 lg:gap-5">
                <article class="group relative isolate flex min-h-[22rem] flex-col justify-end overflow-hidden rounded-xl bg-ink p-3 sm:p-4 lg:col-span-6">
                    <img src="{{ asset('images/why-us/advisory-1400.webp') }}" alt="" width="1400" height="933" loading="lazy" decoding="async"
                        class="absolute inset-0 -z-10 size-full object-cover object-[50%_35%] transition-transform duration-[1400ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-[1.04]">
                    <div class="flex flex-col gap-2 rounded-[0.625rem] bg-ink/45 p-5 text-paper ring-1 ring-paper/15 ring-inset backdrop-blur-xl sm:p-6">
                        <h2 class="text-[length:clamp(1.25rem,1rem+0.8vw,1.75rem)] leading-snug font-normal tracking-[-0.015em]">Work that widens access</h2>
                        <p class="text-body text-paper/80">We promote financial inclusion and help customers start and grow businesses, meet essential obligations and build their livelihoods.</p>
                    </div>
                </article>

                <article class="flex flex-col justify-between gap-10 rounded-xl bg-mist p-6 sm:p-8 lg:col-span-3">
                    <p class="figure-nums text-[length:clamp(3rem,2.2rem+2.2vw,4.25rem)] leading-none tracking-[-0.045em] text-accent">{{ config('company.compliance.licensed_since') }}</p>
                    <div class="flex flex-col gap-2">
                        <h3 class="text-body font-medium text-ink">Established and registered</h3>
                        <p class="text-small text-ink-soft">Proudly Zimbabwean-owned, and licensed as a microfinance institution.</p>
                    </div>
                </article>

                <article class="relative isolate flex flex-col justify-between gap-10 overflow-hidden rounded-xl bg-accent p-6 text-paper sm:p-8 lg:col-span-3">
                    <span aria-hidden="true" class="pointer-events-none absolute -right-3 -bottom-10 -z-10 text-[12rem] leading-none tracking-[-0.06em] text-paper/[0.07]">1</span>
                    <p class="text-[length:clamp(3rem,2.2rem+2.2vw,4.25rem)] leading-none tracking-[-0.045em]">Every CV, read</p>
                    <p class="text-body text-paper/80">A person reads every application. Send yours, even when no role is open.</p>
                </article>
            </div>
        </x-ui.container>
    </x-ui.section>

    @php
        $locations = $vacancies->pluck('location')->unique()->values();
        $counts = $locations->mapWithKeys(fn (string $location): array => [$location => $vacancies->where('location', $location)->count()])
            ->prepend($vacancies->count(), '');
    @endphp

    <div x-data="{ vacancy: @js((string) $chosenVacancy), place: '', counts: @js($counts) }">
        {{-- Open positions: a filter row and a grid of white cards on the lavender panel. --}}
        <x-ui.section id="vacancies" :rule="false" tone="mist" aria-label="Open positions" class="scroll-mt-24 py-16! sm:py-20! lg:py-24!">
            <x-ui.container wide>
                <div data-reveal class="mb-10 flex flex-col gap-3 lg:mb-12">
                    <p class="text-body font-medium text-accent">Open positions</p>
                    <h2 class="max-w-[22ch] text-[length:clamp(1.75rem,1.2rem+1.6vw,2.5rem)] leading-[1.2] font-normal tracking-[-0.025em] text-ink">Find the role that fits you</h2>
                </div>

                @if ($vacancies->isNotEmpty())
                    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between lg:mb-10">
                        @if ($locations->count() > 1)
                            <div role="group" aria-label="Filter by location" class="flex w-fit gap-1 rounded-full bg-paper p-1">
                                <button type="button" x-on:click="place = ''" x-bind:class="place === '' ? 'bg-accent text-paper' : 'text-ink-soft hover:text-ink'" class="rounded-full px-5 py-2 text-small font-medium transition-colors duration-300">All</button>
                                @foreach ($locations as $location)
                                    <button type="button" x-on:click="place = @js($location)" x-bind:class="place === @js($location) ? 'bg-accent text-paper' : 'text-ink-soft hover:text-ink'" class="rounded-full px-5 py-2 text-small font-medium transition-colors duration-300">{{ $location }}</button>
                                @endforeach
                            </div>
                        @endif
                        <p class="figure-nums text-small text-ink-soft" aria-live="polite"><span x-text="counts[place]">{{ $vacancies->count() }}</span> {{ $vacancies->count() === 1 ? 'role' : 'roles' }} available</p>
                    </div>
                @endif

                <div class="grid items-stretch gap-3 md:grid-cols-2 lg:gap-4 xl:grid-cols-3">
                    @forelse ($vacancies as $vacancy)
                        <article
                            x-data="{ more: false }"
                            x-show="place === '' || place === @js($vacancy->location)"
                            x-transition.opacity.duration.300ms
                            class="flex flex-col gap-6 rounded-xl bg-paper p-6 shadow-[0_1px_0_0_rgb(21_16_25/0.04)] transition-shadow duration-500 hover:shadow-[0_24px_48px_-28px_rgb(21_16_25/0.35)] sm:p-7"
                        >
                            <div class="flex items-center gap-3">
                                <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-xl bg-mist">
                                    <img src="{{ asset('images/baardy-mark.png') }}" alt="" width="28" height="28" class="size-7 object-contain">
                                </span>
                                <span class="flex flex-col leading-tight">
                                    <span class="text-small text-ink-soft">{{ config('company.name') }}</span>
                                    <span class="text-small font-medium text-accent">{{ $vacancy->location }}</span>
                                </span>
                                @if ($vacancy->closes_on)
                                    <span class="ml-auto rounded-full bg-accent-tint px-3 py-1 text-small whitespace-nowrap text-accent">Closes {{ $vacancy->closes_on->format('j M') }}</span>
                                @endif
                            </div>

                            <div class="flex flex-col gap-3">
                                <h3 class="text-[length:clamp(1.75rem,1.3rem+1.4vw,2.25rem)] leading-[1.1] tracking-[-0.02em] text-ink">{{ $vacancy->title }}</h3>
                                <p class="text-body leading-[1.4] text-ink-soft">{{ $vacancy->summary }}</p>
                            </div>

                            @if (filled($vacancy->requirements))
                                <ul class="flex flex-wrap gap-2 border-t border-line pt-5">
                                    @foreach (array_slice(array_filter(array_map('trim', explode("\n", $vacancy->requirements))), 0, 3) as $requirement)
                                        <li class="max-w-full truncate rounded-full bg-mist px-3 py-1 text-small text-ink-soft">{{ $requirement }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            <div x-show="more" x-transition.opacity x-cloak class="text-body leading-[1.5] whitespace-pre-line text-ink-soft">{{ $vacancy->description }}</div>

                            <div class="mt-auto flex items-center justify-between gap-4 pt-1">
                                <button type="button" x-on:click="more = ! more" x-bind:aria-expanded="more" class="inline-flex items-center gap-1.5 text-small font-medium text-ink transition-colors hover:text-accent">
                                    <span x-text="more ? 'Less' : '{{ $vacancy->employment_type }} · Details'">{{ $vacancy->employment_type }} &middot; Details</span>
                                    <x-ui.icon name="chevron-down" class="size-4 transition-transform duration-300" x-bind:class="more && 'rotate-180'" />
                                </button>
                                <x-ui.button
                                    :href="route('careers', ['vacancy' => $vacancy->id]).'#apply'"
                                    x-on:click.prevent="vacancy = '{{ $vacancy->id }}'; document.getElementById('apply').scrollIntoView({ behavior: 'smooth' })"
                                    arrow
                                >
                                    Apply
                                </x-ui.button>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-xl bg-paper p-6 sm:p-8 md:col-span-2 xl:col-span-3">
                            <p class="text-[length:clamp(1.5rem,1.1rem+1.2vw,2rem)] leading-[1.15] tracking-[-0.02em] text-ink">No roles are open right now</p>
                            <p class="mt-2 text-body text-ink-soft">Send us your CV below and we will keep you in mind.</p>
                        </div>
                    @endforelse
                </div>
            </x-ui.container>
        </x-ui.section>
        {{-- Application: a purple card with a white form panel, like the closing CTA. --}}
        <x-ui.section :rule="false" aria-label="Apply" class="py-16! sm:py-20! lg:py-24!">
            <x-ui.container wide>
                <div id="apply" data-reveal class="grid scroll-mt-24 gap-3 rounded-3xl bg-accent p-3 text-paper sm:p-4 lg:grid-cols-12 lg:gap-4">
                    <div class="flex flex-col justify-between gap-10 p-4 sm:p-6 lg:col-span-5 lg:p-8">
                        <h2 class="text-[length:clamp(2rem,3.34vw,3rem)] leading-[1.1] font-normal tracking-[-0.03em]">Apply with your CV</h2>
                        <div class="flex flex-col gap-6">
                            <ol class="flex flex-col gap-4">
                                @foreach (['Choose a role, or a general application', 'Attach your CV as PDF or Word, up to 5 MB', 'We reply if there is a fit'] as $step)
                                    <li class="flex items-center gap-3 text-body text-paper/85">
                                        <span class="figure-nums inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-paper/15 text-small">{{ $loop->iteration }}</span>
                                        {{ $step }}
                                    </li>
                                @endforeach
                            </ol>
                            <p class="max-w-xs text-small text-paper/70">A person reads every application.</p>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-paper p-6 text-ink sm:p-8 lg:col-span-7">
                @if (session('application_sent'))
                    <div class="flex flex-col gap-3" role="status">
                        <p class="text-lead text-ink">Application received</p>
                        <p class="max-w-xl text-body text-ink-soft">{{ session('application_sent') }}</p>
                        <a href="{{ route('careers') }}" class="w-fit text-body text-accent hover:text-accent-strong">Send another application</a>
                    </div>
                @else
                    <form method="POST" action="{{ route('careers.apply') }}" enctype="multipart/form-data" class="flex flex-col gap-5" x-data="{ file: '' }">
                        @csrf

                        @if ($errors->any())
                            <div role="alert" class="rounded-lg border border-danger/30 bg-danger/5 px-4 py-3 text-small text-danger">
                                Please fix the highlighted fields and try again.
                            </div>
                        @endif

                        <div class="flex flex-col gap-2">
                            <label for="apply-vacancy" class="text-small font-medium text-ink">Role</label>
                            <div
                                class="relative"
                                x-data="{
                                    open: false,
                                    options: @js($roleOptions),
                                    get label() { return this.options.find((option) => option.id === vacancy)?.label ?? this.options[0].label },
                                    pick(id) { vacancy = id; this.open = false; this.$refs.trigger.focus() },
                                    move(step) {
                                        const index = this.options.findIndex((option) => option.id === vacancy);
                                        vacancy = this.options[(index + step + this.options.length) % this.options.length].id;
                                    },
                                }"
                                x-on:click.outside="open = false"
                                x-on:keydown.escape="open = false"
                            >
                                <input type="hidden" name="vacancy_id" x-bind:value="vacancy">
                                <button
                                    type="button"
                                    id="apply-vacancy"
                                    x-ref="trigger"
                                    aria-haspopup="listbox"
                                    x-bind:aria-expanded="open"
                                    x-on:click="open = ! open"
                                    x-on:keydown.arrow-down.prevent="open ? move(1) : open = true"
                                    x-on:keydown.arrow-up.prevent="open ? move(-1) : open = true"
                                    class="{{ $input }} flex items-center justify-between gap-3 pr-4 text-left"
                                    x-bind:class="open && 'border-accent! bg-paper! ring-4 ring-accent/10'"
                                >
                                    <span class="truncate" x-text="label">General application (keep me in mind)</span>
                                    <x-ui.icon name="chevron-down" class="size-4 shrink-0 text-muted transition-transform duration-300" x-bind:class="open && 'rotate-180'" />
                                </button>

                                <ul
                                    x-show="open"
                                    x-cloak
                                    x-transition.origin.top.duration.200ms
                                    role="listbox"
                                    aria-labelledby="apply-vacancy"
                                    class="absolute z-20 mt-2 max-h-72 w-full overflow-auto rounded-xl border border-line bg-paper p-1.5 shadow-[0_24px_48px_-20px_rgb(21_16_25/0.35)]"
                                >
                                    <template x-for="option in options" x-bind:key="option.id">
                                        <li
                                            role="option"
                                            x-bind:aria-selected="vacancy === option.id"
                                            x-on:click="pick(option.id)"
                                            x-on:keydown.enter.prevent="pick(option.id)"
                                            class="flex cursor-pointer items-center justify-between gap-3 rounded-lg px-3.5 py-3 text-body text-ink transition-colors duration-150 hover:bg-mist"
                                            x-bind:class="vacancy === option.id && 'bg-accent-tint text-accent'"
                                        >
                                            <span x-text="option.label"></span>
                                            <x-ui.icon name="check" class="size-4 shrink-0 text-accent" x-show="vacancy === option.id" />
                                        </li>
                                    </template>
                                </ul>
                            </div>
                            @error('vacancy_id')<p class="text-small text-danger">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="flex flex-col gap-2">
                                <label for="apply-name" class="text-small font-medium text-ink">Full name<span class="text-accent"> *</span></label>
                                <input id="apply-name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}" class="{{ $input }} @error('name') border-danger! @enderror">
                                @error('name')<p class="text-small text-danger">{{ $message }}</p>@enderror
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="apply-phone" class="text-small font-medium text-ink">Phone or WhatsApp<span class="text-accent"> *</span></label>
                                <input id="apply-phone" name="phone" type="tel" autocomplete="tel" required value="{{ old('phone') }}" class="{{ $input }} @error('phone') border-danger! @enderror">
                                @error('phone')<p class="text-small text-danger">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="apply-email" class="text-small font-medium text-ink">Email <span class="font-normal text-muted">(optional)</span></label>
                            <input id="apply-email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" class="{{ $input }} @error('email') border-danger! @enderror">
                            @error('email')<p class="text-small text-danger">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="apply-cv" class="text-small font-medium text-ink">Your CV<span class="text-accent"> *</span></label>
                            <label for="apply-cv" class="flex cursor-pointer flex-col items-start gap-1 rounded-xl border border-dashed border-line-strong bg-mist px-4 py-5 transition duration-200 hover:border-accent hover:bg-accent-tint has-[:focus-visible]:outline-2 has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-accent @error('cv') border-danger! @enderror">
                                <span class="text-body text-ink" x-text="file || 'Choose a file to attach'">Choose a file to attach</span>
                                <span class="text-small text-muted">PDF, DOC or DOCX, up to 5 MB</span>
                                <input id="apply-cv" name="cv" type="file" required accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="sr-only" x-on:change="file = $event.target.files[0]?.name ?? ''">
                            </label>
                            @error('cv')<p class="text-small text-danger">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="apply-message" class="text-small font-medium text-ink">A short note <span class="font-normal text-muted">(optional)</span></label>
                            <textarea id="apply-message" name="message" rows="4" maxlength="2000" class="w-full rounded-xl border border-line bg-mist px-4 py-3 text-body text-ink transition duration-200 hover:border-line-strong focus:border-accent focus:bg-paper focus:ring-4 focus:ring-accent/10 focus:outline-none focus-visible:outline-none">{{ old('message') }}</textarea>
                            @error('message')<p class="text-small text-danger">{{ $message }}</p>@enderror
                        </div>

                        {{-- Honeypot: invisible to people. --}}
                        <div class="absolute -left-[9999px]" aria-hidden="true">
                            <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>

                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <p class="max-w-md text-small text-muted">
                                Your CV is used only to consider your application. See our
                                <a href="{{ route('legal.privacy') }}" class="underline hover:text-accent">privacy notice</a>.
                            </p>
                            <x-ui.button type="submit" size="lg" arrow class="w-full sm:w-fit">
                                Send application
                            </x-ui.button>
                        </div>
                    </form>
                @endif
                    </div>
                </div>
            </x-ui.container>
        </x-ui.section>
    </div>

@endsection
