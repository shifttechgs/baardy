{{--
    /contact -- where to find us, then the way to ask.

    The offices and their map (sections/home/visit), flowing straight into the
    enquiry form (sections/home/contact). Both used to sit on the homepage;
    they live here so the landing page stays light. Every "Start an
    application" button on the site lands on this page, and the form reads
    ?interest= and ?branch= from the query, so whoever arrives with a loan or a
    branch in mind finds it already chosen.
--}}
@extends('layouts.marketing')

@section('title', 'Contact us | '.config('company.name'))

@section('description', 'Visit '.config('company.legal_name').' in Harare or Bulawayo, call or WhatsApp us, or send an enquiry and a person will call you back.')

@section('content')
    <x-layout.page-header
        eyebrow="Contact us"
        title="Talk to a person"
        lead="Visit an office, call or WhatsApp us, or send a message and we will call you back."
    />

    @include('sections.home.visit', ['showHeading' => false])
    @include('sections.home.contact')
@endsection
