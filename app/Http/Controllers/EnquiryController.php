<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RememberLeadSource;
use App\Http\Requests\StoreEnquiryRequest;
use App\Mail\EnquiryReceived;
use App\Models\Lead;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EnquiryController extends Controller
{
    /**
     * Take a "Get in touch" enquiry: store it as a lead, then email the team.
     *
     * The lead is stored first and the email is best-effort: if the mailer is
     * down the failure is reported and the visitor still gets their
     * reference, because the lead is already waiting in the admin panel.
     *
     * Works both as a plain form post (redirect back to the section with a
     * flash message) and as the Alpine-enhanced fetch the section uses
     * (JSON), so the form still works with JavaScript off.
     */
    public function store(StoreEnquiryRequest $request): JsonResponse|RedirectResponse
    {
        // Honeypot: a real visitor never sees the `website` field. A bot that
        // fills it gets the same success response, so it learns nothing.
        if ($request->filled('website')) {
            return $this->respond($request, null);
        }

        $enquiry = $request->safe()->only(['name', 'phone', 'email', 'interest', 'branch', 'message']);
        $enquiry['promotion_id'] = filled($request->validated('promo'))
            ? Promotion::where('tracking_code', $request->validated('promo'))->value('id')
            : null;

        $lead = Lead::capture(
            $enquiry,
            Arr::only($request->session()->get(RememberLeadSource::SESSION_KEY, []), ['utm_source', 'utm_medium', 'utm_campaign', 'referrer', 'landing_page']),
        );

        try {
            Mail::to(config('company.enquiries.to'))->send(new EnquiryReceived($lead, $enquiry));
        } catch (Throwable $exception) {
            report($exception);
        }

        return $this->respond($request, $lead->reference);
    }

    private function respond(StoreEnquiryRequest $request, ?string $reference): JsonResponse|RedirectResponse
    {
        // A bot gets a reference-shaped string too, so the two responses look alike.
        $reference ??= 'BMC-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $message = 'Thank you. We have your details and will call or WhatsApp you shortly.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'reference' => $reference]);
        }

        return redirect()->to(route('contact').'#contact')
            ->with('enquiry_sent', $message)
            ->with('enquiry_reference', $reference);
    }
}
