<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnquiryRequest;
use App\Mail\EnquiryReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class EnquiryController extends Controller
{
    /**
     * Deliver a "Get in touch" enquiry to the client's inbox.
     *
     * Works both as a plain form post (redirect back to the section with a
     * flash message) and as the Alpine-enhanced fetch the section uses
     * (JSON), so the form still works with JavaScript off.
     */
    public function store(StoreEnquiryRequest $request): JsonResponse|RedirectResponse
    {
        // Honeypot: a real visitor never sees the `website` field. A bot that
        // fills it gets the same success response, so it learns nothing.
        if (! $request->filled('website')) {
            Mail::to(config('company.enquiries.to'))->send(
                new EnquiryReceived($request->safe()->except('website'))
            );
        }

        $message = 'Thank you. We have your details and will be in touch shortly.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()->to(route('home').'#contact')->with('enquiry_sent', $message);
    }
}
