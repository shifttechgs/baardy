{{--
    /complaints -- how to make a complaint.

    Deliberately states no response times: none are confirmed by the client.
    Add them here (and nowhere else) once they are. DRAFT FOR LEGAL REVIEW:
    see config/company.php -> legal.
--}}
@extends('layouts.marketing')

@section('title', 'Complaints procedure | '.config('company.name'))

@section('description', 'How to make a complaint to '.config('company.legal_name').', and what happens next.')

@section('content')
    <x-layout.legal-page
        title="Complaints procedure"
        summary="If we have got something wrong, we want to hear about it. This is how to tell us, and what happens next."
        updated="2026-10-04"
        image="images/insights/first-loan-1600.webp"
        alt="A customer and a loan officer talking through an application"
        position="50% 40%"
        :width="1600"
        :height="1067"
    >
        <p>
            You can complain about anything to do with our service: an application, a loan, a repayment, or how
            you were treated. Making a complaint does not affect your application or your loan.
        </p>

        <h2>How to complain</h2>
        <ul>
            <li>call or WhatsApp us, using the details on this page</li>
            <li>visit your branch and ask to speak to the branch manager</li>
            <li>send us a message through the enquiry form and choose &ldquo;Something else&rdquo;</li>
        </ul>

        <h2>What to tell us</h2>
        <ul>
            <li>your name and the best number to reach you on</li>
            <li>your loan or application details, if you have them</li>
            <li>what happened, and what you would like us to do about it</li>
        </ul>

        <h2>Your complaint is safe with us</h2>
        <p>
            You will not be treated worse for complaining. Only the people who need to look into it will see it,
            and we will handle your details as set out in our
            <a href="{{ route('legal.privacy') }}" class="text-accent underline underline-offset-4">privacy notice</a>.
        </p>

        <h2>What happens next</h2>
        <p>
            We will confirm we have your complaint and tell you who is looking into it. A person reviews it, not
            a system, and we will explain our answer to you in plain terms. If we got something wrong, we will
            tell you how we will put it right.
        </p>

        <h2>If you are not satisfied</h2>
        <p>
            Ask for your complaint to be reviewed by our management. If you are still not satisfied with our final
            answer, you can refer it to {{ config('company.compliance.regulator') }}, which licenses and supervises
            microfinance institutions in Zimbabwe.
        </p>
    </x-layout.legal-page>
@endsection
