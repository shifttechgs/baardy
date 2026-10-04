<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobApplicationRequest;
use App\Mail\JobApplicationReceived;
use App\Models\JobApplication;
use App\Models\Vacancy;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CareerController extends Controller
{
    public function index(): View
    {
        return view('pages.careers', [
            'vacancies' => Vacancy::query()->open()->orderBy('closes_on')->latest()->get(),
        ]);
    }

    /**
     * Store an application with its CV in the database, then email the team the
     * details with the CV attached. The email is best-effort: the application
     * is already safe in the admin panel.
     */
    public function store(StoreJobApplicationRequest $request): RedirectResponse
    {
        $done = redirect()->to(route('careers').'#apply')
            ->with('application_sent', 'Thank you. We have your application and CV, and will be in touch if there is a fit.');

        // Honeypot: bots get the same success response.
        if ($request->filled('website')) {
            return $done;
        }

        $cv = $request->file('cv');

        $application = JobApplication::create([
            ...$request->safe()->only(['vacancy_id', 'name', 'phone', 'email', 'message']),
            'cv_original_name' => $cv->getClientOriginalName(),
            'cv_mime' => $cv->getMimeType() ?? 'application/octet-stream',
            'cv_size' => $cv->getSize(),
            'cv_data' => base64_encode($cv->get()),
        ]);

        try {
            Mail::to(config('company.careers.to'))->send(new JobApplicationReceived($application));
        } catch (Throwable $exception) {
            report($exception);
        }

        return $done;
    }
}
