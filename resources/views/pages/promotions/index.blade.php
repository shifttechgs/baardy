{{--
    /promotions -- every live promotion, soonest-ending first.

    Same editorial language as /insights: a quiet hero, then cards with their
    text below the photograph (x-ui.promotion-card). With nothing running,
    the page says so and points to the loans rather than showing an empty
    grid.
--}}
@extends('layouts.marketing')

@section('title', 'Promotions | '.config('company.name'))

@section('description', 'Current offers from '.config('company.legal_name').'. Every offer is subject to application, affordability assessment and approval.')

@section('content')
    <x-layout.page-header
        eyebrow="Promotions"
        title="Current offers"
        lead="Every offer runs for a set time, with its terms in full on its page. Each is subject to application, affordability assessment and approval."
    />

    <x-ui.section :rule="false" class="pt-0! pb-16! sm:pb-20! lg:pb-28!">
        <x-ui.container wide>
            @if ($promotions->isEmpty())
                <div class="flex flex-col items-start gap-5 rounded-2xl bg-mist p-8">
                    <p class="text-lead text-ink">There are no promotions running right now.</p>
                    <p class="text-body text-ink-soft">Our loans are available all year. Tell us what you need and a person will call you back.</p>
                    <x-ui.button :href="config('company.cta.primary.href')" arrow>
                        {{ config('company.cta.primary.label') }}
                    </x-ui.button>
                </div>
            @else
                <div class="grid gap-x-5 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($promotions as $promotion)
                        <x-ui.promotion-card :promotion="$promotion" />
                    @endforeach
                </div>
            @endif
        </x-ui.container>
    </x-ui.section>

    @include('sections.home.cta')
@endsection
