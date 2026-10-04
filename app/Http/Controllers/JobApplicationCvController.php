<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves an applicant's CV to staff.
 *
 * A PDF opens inside the browser so it can be read without saving it; anything
 * else (Word files, which browsers cannot show) is sent as a download. Only
 * signed-in admin-panel users get it, and anyone else gets a plain 404 so the
 * route does not advertise that applications exist.
 *
 * The content type is set here, never taken from the upload, and `nosniff`
 * stops a browser treating a disguised file as a web page. A file only counts
 * as a PDF when it also starts with the PDF signature.
 */
class JobApplicationCvController extends Controller
{
    public function show(Request $request, JobApplication $application): Response
    {
        abort_unless($request->user()?->is_admin, 404);

        $contents = $application->cvContents();
        $isPdf = $application->cv_mime === 'application/pdf' && str_starts_with($contents, '%PDF');

        return response($contents, 200, [
            'Content-Type' => $isPdf ? 'application/pdf' : 'application/octet-stream',
            'Content-Disposition' => ($isPdf ? 'inline' : 'attachment').'; filename="'.addslashes($application->cv_original_name).'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
