{{--
    One page for every error a visitor can meet, in the site's own layout:
    what happened in plain words, and a way back. Never a stack trace, never
    the framework's default screen.

    Variables: $code, $heading, $lead
--}}
@extends('layouts.marketing')

@section('title', $heading.' | '.config('company.name'))

@section('content')
    <x-ui.section :rule="false" class="pt-28! pb-24! sm:pt-36! lg:pb-32!">
        <x-ui.container>
            <div class="flex max-w-xl flex-col items-start gap-6">
                <p class="figure-nums text-small font-medium text-accent">{{ $code }}</p>
                <h1 class="text-[length:clamp(2rem,1.4rem+2.4vw,3.25rem)] leading-[1.1] font-normal tracking-[-0.03em] text-balance text-ink">{{ $heading }}</h1>
                <p class="text-lead text-ink-soft">{{ $lead }}</p>
                <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                    <x-ui.button :href="route('home')" arrow>Back to the home page</x-ui.button>
                    <a href="{{ route('contact') }}" class="text-body text-accent hover:text-accent-strong">Talk to a person</a>
                </div>
            </div>
        </x-ui.container>
    </x-ui.section>
@endsection
