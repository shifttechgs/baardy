{{--
    /privacy -- privacy notice for this website.

    Written to what the site actually does, checked against the code:

      collected   the "Get in touch" form only: name, phone, email (optional),
                  loan of interest, nearest branch, message (optional)
                  -- StoreEnquiryRequest::rules()
      handling    emailed to the team's inbox; the site keeps no database of
                  enquiries -- EnquiryController::store()
      cookies     Laravel's session and XSRF-TOKEN cookies only; no analytics
                  or advertising scripts are loaded -- layouts/marketing

    If any of those change (analytics added, enquiries stored), this page
    must change with them. DRAFT FOR LEGAL REVIEW: see config/company.php ->
    legal.
--}}
@extends('layouts.marketing')

@section('title', 'Privacy notice | '.config('company.name'))

@section('description', 'How '.config('company.legal_name').' handles the personal information you give us through this website.')

@section('content')
    <x-layout.legal-page
        title="Privacy notice"
        summary="What we collect through this website, why, what happens to it, and the choices you have."
        updated="2026-09-27"
    >
        <p>
            This notice is from {{ config('company.legal_name') }} (&ldquo;Baardy&rdquo;, &ldquo;we&rdquo;), a
            microfinance institution licensed by {{ config('company.compliance.regulator') }}. It covers the
            personal information you give us through this website. When you apply for a loan at a branch, you
            will be told there how the information in your application is used.
        </p>

        <h2>What we collect</h2>
        <p>
            We only collect what you type into the &ldquo;Get in touch&rdquo; form:
        </p>
        <ul>
            <li>your name and a phone or WhatsApp number</li>
            <li>your email address, if you choose to give it</li>
            <li>the loan or service you are asking about, and your nearest branch</li>
            <li>anything you write in the message box, if you use it</li>
        </ul>
        <p>
            The website never asks for your ID number, bank details, payslips or other documents. Those are
            handled in person at a branch when you apply.
        </p>

        <h2>Why we use it</h2>
        <p>
            To answer your enquiry: a member of our team calls, WhatsApps or emails you back, and your nearest
            branch helps you with your application. We do not use it for marketing unless you ask us to, and we
            <strong>never sell it</strong>.
        </p>

        <h2>What happens to it</h2>
        <p>
            When you send the form, your details are emailed to our team. The website itself does not keep a
            database of enquiries. We share your information only with the companies that host this website and
            deliver its email, and only so they can do that, or where the law requires us to.
        </p>

        <h2>How long we keep it</h2>
        <p>
            Only as long as we need it to deal with your enquiry. If you go on to become a customer, your
            information becomes part of your loan records, which we keep for as long as the law requires.
        </p>

        <h2>Cookies</h2>
        <p>
            This website uses only the cookies it needs to work: one that keeps your visit together and one that
            protects the enquiry form from misuse. There are no analytics, advertising or tracking cookies.
        </p>

        <h2>Your rights</h2>
        <p>
            Under Zimbabwe&rsquo;s Cyber and Data Protection Act, you can ask us what information we hold about
            you, ask us to correct it, or ask us to delete it where we no longer need it. Contact us using the
            details on this page. If you are not satisfied with how we handle your request, you can complain to
            the Postal and Telecommunications Regulatory Authority of Zimbabwe (POTRAZ), the data protection
            authority.
        </p>

        <h2>Changes to this notice</h2>
        <p>
            If we change how we handle your information, we will update this page and the date at the top.
        </p>
    </x-layout.legal-page>
@endsection
