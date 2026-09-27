{{--
    /terms -- terms of use for this website.

    Scope is the website, not the loans: every loan is governed by its own
    written agreement, and this page says so. DRAFT FOR LEGAL REVIEW: see
    config/company.php -> legal.
--}}
@extends('layouts.marketing')

@section('title', 'Terms of use | '.config('company.name'))

@section('description', 'The terms for using the '.config('company.legal_name').' website.')

@section('content')
    <x-layout.legal-page
        title="Terms of use"
        summary="The terms for using this website. Your loan itself is governed by your written loan agreement."
        updated="2026-09-27"
    >
        <p>
            This website is run by {{ config('company.legal_name') }}, a microfinance institution licensed by
            {{ config('company.compliance.regulator') }}. By using it, you agree to these terms.
        </p>

        <h2>Nothing here is an offer of credit</h2>
        <p>
            The website describes our loans and how we lend. It is not an offer of credit. Every loan is subject
            to an application, an affordability assessment and approval. What you can borrow, and on what terms,
            is decided during that review and set out in your written loan agreement. If anything on this website
            differs from your loan agreement, <strong>your loan agreement applies</strong>.
        </p>

        <h2>Guidance is general</h2>
        <p>
            Our Insights articles are general guidance on budgeting, borrowing and running a business. They are
            not advice about your own circumstances. For that, speak to our team.
        </p>

        <h2>Keeping it accurate</h2>
        <p>
            We work to keep this website accurate and up to date, but information can change. If something
            matters to your decision, confirm it with us before you rely on it.
        </p>

        <h2>Using the website</h2>
        <p>
            Please use the enquiry form only for genuine enquiries, and do not try to disrupt or misuse the
            website. Some links, such as WhatsApp, take you to services run by other companies, whose own terms
            apply there.
        </p>

        <h2>Our content</h2>
        <p>
            The text, design and Baardy name and logo on this website belong to us. Photographs are used under
            licence. Please do not copy them for commercial use without our permission.
        </p>

        <h2>The law that applies</h2>
        <p>
            These terms are governed by the laws of Zimbabwe.
        </p>

        <h2>Changes to these terms</h2>
        <p>
            We may update these terms. The date at the top shows when they last changed.
        </p>
    </x-layout.legal-page>
@endsection
