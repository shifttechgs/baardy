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
        updated="2026-10-04"
        image="images/hero/farmer-2400.webp"
        alt="A farmer in a field, one of the people we lend to"
        position="50% 40%"
        :width="2400"
        :height="1600"
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
            Please use our forms only for genuine enquiries and genuine job applications, give details that are
            yours and true, and do not try to disrupt, probe or misuse the website or its back office. We may
            block access that does. Some links, such as WhatsApp, take you to services run by other companies,
            whose own terms apply there.
        </p>

        <h2>Sending us information</h2>
        <p>
            Sending an enquiry or a job application does not create a loan, a job offer or any other contract. It
            asks us to get in touch. How we handle what you send is in our
            <a href="{{ route('legal.privacy') }}" class="text-accent underline underline-offset-4">privacy notice</a>.
            Please do not send ID numbers, bank details or passwords through the website; we will ask for what we
            need in person.
        </p>

        <h2>Offers and promotions</h2>
        <p>
            Offers shown on this website run for the period stated, are subject to the terms written on the
            offer&rsquo;s page, and to application, affordability assessment and approval. We may end or change an
            offer. An offer ending does not affect a loan already agreed in writing.
        </p>

        <h2>Our content</h2>
        <p>
            The text, design and Baardy name and logo on this website belong to us. Photographs are used under
            licence. Please do not copy them for commercial use without our permission.
        </p>

        <h2>Our responsibility</h2>
        <p>
            We take care over this website but provide it as it is. We are not responsible for losses that come
            from relying on it in place of your loan agreement or our advice, or from the website being
            unavailable for a time. Nothing here limits any right you have under the law that cannot be limited.
        </p>

        <h2>The law that applies</h2>
        <p>
            These terms are governed by the laws of Zimbabwe.
        </p>

        <h2>Changes to these terms</h2>
        <p>
            We may update these terms. The date at the top and foot of the page shows when they last changed, and
            using the website after that means you accept the update.
        </p>
    </x-layout.legal-page>
@endsection
