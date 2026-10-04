{{--
    /privacy -- privacy notice for this website.

    Written to what the site actually does, checked against the code:

      collected   the "Get in touch" form (name, phone, email optional, loan,
                  branch, message optional) and the careers form (name, phone,
                  email optional, role, message, CV file)
                  -- StoreEnquiryRequest, StoreJobApplicationRequest
      source      the first page a visit landed on, the site it came from and
                  any campaign tags on the link, kept with the enquiry
                  -- RememberLeadSource, Lead::capture()
      handling    stored in the staff back office AND emailed to the team's
                  inbox -- EnquiryController, CareerController
      cookies     Laravel's session and XSRF-TOKEN cookies only; no analytics
                  or advertising scripts are loaded -- layouts/marketing

    If any of those change (analytics added, new fields, retention period
    set), this page must change with them. No retention period or response
    time is stated because none is confirmed by the client. DRAFT FOR LEGAL
    REVIEW: see config/company.php -> legal.
--}}
@extends('layouts.marketing')

@section('title', 'Privacy notice | '.config('company.name'))

@section('description', 'What '.config('company.legal_name').' collects through this website, why, who sees it, how long it is kept, and your rights.')

@section('content')
    <x-layout.legal-page
        title="Privacy notice"
        summary="What we collect through this website, why, who sees it, and the choices you have."
        updated="2026-10-04"
        image="images/hero/vendor-2400.webp"
        alt="A market vendor, one of the people we lend to"
        position="50% 40%"
        :width="2400"
        :height="1600"
    >
        <p>
            This notice is from {{ config('company.legal_name') }} (&ldquo;Baardy&rdquo;, &ldquo;we&rdquo;), a
            microfinance institution licensed by {{ config('company.compliance.regulator') }}. It covers the
            personal information you give us through this website. When you apply for a loan at a branch, you
            will be told there how the information in your application is used.
        </p>

        <h2>The short version</h2>
        <ul>
            <li>We collect only what you type into our forms, plus a note of how you found us.</li>
            <li>Our staff use it to reply to you. We <strong>never sell it</strong>.</li>
            <li>The website never asks for your ID number, bank details or payslips.</li>
            <li>No advertising or tracking cookies. No analytics.</li>
            <li>You can ask to see, correct or delete what we hold.</li>
        </ul>

        <h2>What we collect</h2>
        <p><strong>When you send an enquiry</strong> (the &ldquo;Get in touch&rdquo; form):</p>
        <ul>
            <li>your name and a phone or WhatsApp number</li>
            <li>your email address, if you choose to give it</li>
            <li>the loan or service you are asking about, and your nearest branch</li>
            <li>anything you write in the message box</li>
        </ul>
        <p><strong>When you apply for a job</strong> (the careers form):</p>
        <ul>
            <li>your name, phone number, and email address if you give one</li>
            <li>the role you are applying for, and any message you write</li>
            <li>your CV, as a PDF or Word file</li>
        </ul>
        <p>
            <strong>How you found us.</strong> With an enquiry we also keep the first page of our site you
            visited, the website you came from, and any campaign tags on the link you followed (for example, that
            you clicked an offer we shared on Facebook). This tells us which of our offers and channels are
            worth running. It is stored with your enquiry, not as a profile of you.
        </p>
        <p>
            The website never asks for your ID number, bank details, payslips or other documents. Those are
            handled in person at a branch when you apply for a loan.
        </p>

        <h2>Why we use it</h2>
        <p>
            To answer your enquiry: a member of our team calls, WhatsApps or emails you back, and your nearest
            branch helps you with your application. To consider your job application. And to understand, in
            general terms, which offers bring people to us. We do not use your details for marketing unless you
            ask us to.
        </p>

        <h2>Who sees it</h2>
        <p>
            Enquiries and job applications are emailed to our team and kept in our staff back office, which only
            signed-in staff can open. A CV can be read only by staff who handle applications. We share your
            information only with the companies that host this website and deliver its email, and only so they
            can do that, or where the law requires us to.
        </p>

        <h2>How long we keep it</h2>
        <p>
            Only as long as we need it for the purpose you gave it for. An enquiry that does not become a loan,
            and a job application that does not lead to a job, are not kept indefinitely. If you go on to become
            a customer, your information becomes part of your loan records, which we keep for as long as the law
            requires.
        </p>

        <h2>Cookies</h2>
        <p>
            This website uses only the cookies it needs to work: one that keeps your visit together (it also
            remembers where you came from until you send an enquiry), and one that protects our forms from
            misuse. There are no analytics, advertising or tracking cookies, and we do not load scripts from
            advertising or social networks. The WhatsApp button opens WhatsApp, which has its own privacy terms.
        </p>

        <h2>Your rights</h2>
        <p>
            Under Zimbabwe&rsquo;s Cyber and Data Protection Act, you can ask us what information we hold about
            you, ask us to correct it, or ask us to delete it where we no longer need it. Contact us using the
            details beside this page and tell us what you would like. If you are not satisfied with how we handle
            your request, you can complain to the Postal and Telecommunications Regulatory Authority of Zimbabwe
            (POTRAZ), the data protection authority.
        </p>

        <h2>Changes to this notice</h2>
        <p>
            If we change how we handle your information, we will update this page and the date at the foot of it.
        </p>
    </x-layout.legal-page>
@endsection
