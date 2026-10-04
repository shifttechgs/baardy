{{--
    /responsible-lending -- how we lend, and what to do if repaying is hard.

    Every commitment here restates one the site already makes (hero promises,
    benefits, principles, the FAQ), so this page adds no new claim. Where
    those are still unconfirmed by the client (see docs/COMPANY.md), they are
    unconfirmed here too. The #struggling-to-repay anchor is linked from the
    footer. DRAFT FOR LEGAL REVIEW: see config/company.php -> legal.
--}}
@extends('layouts.marketing')

@section('title', 'Responsible lending | '.config('company.name'))

@section('description', 'How '.config('company.legal_name').' lends responsibly, and what to do if you are finding it hard to repay.')

@section('content')
    <x-layout.legal-page
        title="Responsible lending"
        summary="A loan should help, not hurt. This is how we make sure it does, and what to do if repaying becomes hard."
        updated="2026-10-04"
        image="images/insights/budgeting-1600.webp"
        alt="Planning a household budget on paper"
        position="50% 50%"
        :width="1600"
        :height="1067"
    >
        <p>
            We lend to salaried workers, farmers, traders and small businesses across Zimbabwe. Lending
            responsibly means only lending what you can afford to repay, and making sure you understand exactly
            what you are agreeing to.
        </p>

        <h2>Before you borrow</h2>
        <ul>
            <li><strong>A person reviews your application.</strong> You are not declined by a score alone.</li>
            <li><strong>We check affordability.</strong> What you can borrow depends on what your income or turnover can support.</li>
            <li><strong>You see the full cost first.</strong> The interest, every fee and the total you will repay are in writing before you accept.</li>
            <li><strong>Your schedule fits how you earn.</strong> Weekly, fortnightly or monthly, agreed before you accept.</li>
        </ul>

        <h2>What we will not do</h2>
        <ul>
            <li>Lend to you just because you could be approved. If the repayments would strain you, we will say so.</li>
            <li>Hide a cost. Anything you will be charged is written down before you commit.</li>
            <li>Rush you. You can take the written terms away and think before you accept.</li>
        </ul>

        <h2>While you repay</h2>
        <ul>
            <li>You can settle early, and you will not be charged a penalty for doing so.</li>
            <li>Free financial advisory is available: guidance on planning, budgeting and managing debt.</li>
        </ul>

        <h2 id="struggling-to-repay" class="scroll-mt-28">If you are struggling to repay</h2>
        <p>
            <strong>Talk to us before the due date if you can.</strong> It is far easier to work out a plan in
            advance than after a payment has been missed. Call or WhatsApp us, or visit your branch, and a person
            will go through your options with you. What a missed payment means for you is set out in your loan
            agreement.
        </p>

        <h2>If something goes wrong</h2>
        <p>
            If you believe we did not lend responsibly, or you were treated unfairly, you can complain. See our
            <a href="{{ route('legal.complaints') }}" class="text-accent underline underline-offset-4">complaints procedure</a>.
        </p>

        <h2>Borrowing well</h2>
        <p>
            Borrow for a clear purpose, only what you need, and on a schedule you can keep. Our
            <a href="{{ route('insights.index') }}" class="text-accent underline underline-offset-4">Insights</a>
            guides cover budgeting on an irregular income and what to prepare before your first application.
        </p>
    </x-layout.legal-page>
@endsection
