{{--
    Closing call to action -- an application slip.

    The page ends on its signature: the paperwork. Where "How it works" shows
    the papers the process produces, this is the first one the reader fills
    in -- two answers (which loan, which branch) on a Baardy letterhead slip,
    then Continue. So the last thing on the page starts the application
    rather than pointing somewhere else.

    HOW IT CONTINUES. The slip is a plain GET form to /#contact; the enquiry
    form preselects whatever it is sent (?interest=&branch=, checked against
    its own options). On the homepage, `applicationSlip` in app.js skips the
    reload: it copies both answers into the enquiry form further up the
    page, scrolls there and puts the cursor in the name field. On /insights, which
    has no form, it simply lands on the homepage form, filled in.

    LAYOUT. An inset brand-purple card within the page measure (not a
    full-bleed band), so it reads as an object rather than the top of the
    footer. Kept quiet on purpose: a two-line heading and a two-line
    subheading on the left, the slip on the right, slightly askew, with the
    paper grain used in "How it works". Side by side from xl, where the text
    column spans the slip's height -- label and heading on its top edge,
    subheading on its bottom edge; stacked below xl, where the columns would
    squeeze the heading onto three lines.

    MOTION. The slip lands as the card is revealed: it rises and settles into
    its tilt, like a sheet set down on a desk (`.cta-slip`, app.css). The
    line beside Continue reads the reader's two answers back to them.
--}}
@php
    $interests = \App\Http\Requests\StoreEnquiryRequest::interests();
    $branches = \App\Http\Requests\StoreEnquiryRequest::branches();
@endphp

<x-ui.section id="get-started" :rule="false" aria-labelledby="get-started-heading" class="pt-10! sm:pt-14! lg:pt-20!">
    <x-ui.container wide>
        <div data-reveal class="relative isolate grid gap-12 overflow-hidden rounded-xl bg-accent px-6 py-12 text-paper sm:px-10 sm:py-14 lg:px-14 lg:py-16 xl:grid-cols-12 xl:items-stretch xl:gap-x-12">

            {{-- The letterhead's mark, set very large and cropped by the card. --}}
            <img
                src="{{ asset('images/baardy-mark.png') }}"
                alt=""
                aria-hidden="true"
                class="pointer-events-none absolute -right-40 -bottom-48 -z-10 size-[34rem] opacity-[0.06] mix-blend-luminosity grayscale"
            >

            {{-- Text column: stretched to the slip's height, so the cap height of
                 "Get started" lines up with the slip's top edge and the
                 subheading's baseline with its bottom edge. Optical, not box,
                 alignment: text-box trims each block to cap height and
                 baseline (Chrome, Safari; others fall back within a few px).
                 The slip's -1.2deg tilt drops its left corners ~6px below its
                 box, hence xl:pt-1.5 at the top and xl:-mb-1.5 at the foot. --}}
            <div class="flex flex-col xl:col-span-6 xl:justify-between xl:pt-1.5 xl:-mb-1.5">
                <div>
                    <p class="text-body font-medium text-paper/70 [text-box:trim-both_cap_alphabetic]">Get started</p>

                    <h2 data-word-reveal
                        id="get-started-heading"
                        class="mt-6 text-[length:clamp(1.75rem,1.2rem+1.5vw,2.625rem)] leading-[1.12] font-normal tracking-[-0.03em] text-paper"
                    >
                        Start with two answers.<br class="hidden sm:inline">
                        We&rsquo;ll call you with the rest.
                    </h2>
                </div>

                <p data-word-reveal class="mt-8 text-lead text-paper/75 [text-box:trim-both_cap_alphabetic] xl:mt-0">
                    A person from our team calls you back.<br class="hidden sm:inline">
                    Nothing is committed until you see the full cost.
                </p>
            </div>

            {{-- The slip. --}}
            <form
                method="GET"
                action="{{ route('home') }}#contact"
                x-data="applicationSlip({{ Js::from($interests[0]) }}, {{ Js::from($branches[0]) }})"
                x-on:submit="carryOn($event)"
                class="cta-slip paper-grain relative w-full max-w-xl justify-self-center rounded-[4px] bg-paper p-6 text-ink shadow-[0_40px_60px_-30px_rgb(21_16_25/0.6)] sm:p-8 xl:col-span-6 xl:justify-self-end"
            >
                {{-- Letterhead. --}}
                <div class="flex items-center justify-between border-b border-ink/80 pb-3">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/baardy-mark.png') }}" alt="" class="size-5">
                        <span class="text-[0.75rem] font-medium tracking-[0.12em] uppercase">Baardy Micro Capital</span>
                    </div>
                    <span class="text-[0.75rem] tracking-[0.12em] text-muted uppercase">Application</span>
                </div>

                <fieldset class="mt-6">
                    <legend class="flex items-baseline gap-2 text-small font-medium text-ink">
                        <span class="figure-nums text-accent">1</span> Which loan?
                    </legend>

                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($interests as $interest)
                            <label
                                class="cursor-pointer rounded-full border px-3.5 py-1.5 text-small transition-colors duration-200
                                       has-[:focus-visible]:outline has-[:focus-visible]:outline-2 has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-accent"
                                x-bind:class="interest === {{ Js::from($interest) }} ? 'border-accent bg-accent text-paper' : 'border-line-strong text-ink hover:border-ink'"
                            >
                                <input type="radio" name="interest" value="{{ $interest }}" class="sr-only" x-model="interest" @checked($loop->first)>
                                {{ $interest }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset class="mt-6 border-t border-dashed border-line-strong pt-5">
                    <legend class="sr-only">Nearest branch</legend>
                    <p aria-hidden="true" class="flex items-baseline gap-2 text-small font-medium text-ink">
                        <span class="figure-nums text-accent">2</span> Nearest branch
                    </p>

                    <div class="mt-3 grid grid-cols-2 gap-1 rounded-full bg-mist p-1">
                        @foreach ($branches as $branch)
                            <label
                                class="flex cursor-pointer items-center justify-center rounded-full py-2 text-small font-medium transition-colors duration-200
                                       has-[:focus-visible]:outline has-[:focus-visible]:outline-2 has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-accent"
                                x-bind:class="branch === {{ Js::from($branch) }} ? 'bg-accent text-paper shadow-sm' : 'text-ink-soft hover:text-ink'"
                            >
                                <input type="radio" name="branch" value="{{ $branch }}" class="sr-only" x-model="branch" @checked($loop->first)>
                                {{ $branch }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <div class="mt-7 flex flex-col gap-4 border-t border-dashed border-line-strong pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <p class="flex max-w-[17rem] flex-col gap-0.5 text-small text-muted" aria-live="polite">
                        <span class="text-ink">
                            <span x-text="interest">{{ $interests[0] }}</span>
                            &middot; <span x-text="branch">{{ $branches[0] }}</span> branch
                        </span>
                        <span>Next: your name and number.</span>
                    </p>

                    <x-ui.button type="submit" pill size="lg" class="group w-fit gap-3 pr-1.5 pl-6">
                        Continue
                        <span class="inline-flex size-10 items-center justify-center rounded-full bg-paper/15 transition-transform duration-300 group-hover:translate-x-0.5">
                            <x-ui.icon name="arrow-right" />
                        </span>
                    </x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.container>
</x-ui.section>
