{{--
    Public homepage.

    Composed entirely from section partials. Each section is a separate file in
    resources/views/sections/home/ -- reordering the page is reordering this
    list, and no section knows about any other.
--}}
@extends('layouts.marketing')

@section('title', config('company.name').' | Loans you can read, from people you can reach')

@section('description', config('company.description'))

{{-- The header floats over the hero photograph until the page scrolls. --}}
@section('navbar', 'overlay')

@section('content')
    @include('sections.home.hero')
    @include('sections.home.trust')
    @include('sections.home.products')

    {{-- Social proof straight after the offer, while the reader is still
         deciding whether this lender is for them -- not buried after the
         process. Shows consented quotes, or the samples while
         marketing.testimonials_preview is on; otherwise renders nothing. --}}
    @include('sections.home.testimonials')

    {{-- `proof` (licence, years, offices) is no longer included: the
         standing section under the hero now carries those facts. Restore it
         by adding include('sections.home.proof') back here. --}}
    @include('sections.home.principles')

    {{-- Differentiation sits next to the trust/proof/testimonials cluster,
         before the process and application steps, so it lands earlier in
         the decision than the eligibility checklist. --}}
    @include('sections.home.why-us')
    @include('sections.home.how-it-works')

    {{-- Removed: the calculator can't return a real number until interest
         rates and a fee schedule exist -- see the note at the top of
         sections/home/calculator.blade.php. The partial is left intact to
         reconnect once real pricing is set. --}}

    {{-- Removed: `eligibility` ("Before you apply") repeated "How it works"
         step 2 and the FAQ's documents answer, and its "who can apply" list
         was taken off the page at the client's request, along with the
         Eligibility nav link. The partial is left intact. --}}
    @include('sections.home.faq')

    {{-- A question the FAQ did not answer lands straight on a way to ask it.
         This section owns #contact, so every "Start an application" button
         arrives at the form. --}}
    @include('sections.home.contact')
    @include('sections.home.insights')
    @include('sections.home.cta')
@endsection
