{{--
    The CV, as a reading surface. A PDF is drawn by PDF.js (resources/js/cv-viewer.js)
    onto white sheets inside the card, with a page count and zoom, instead of the
    browser's own viewer. Only a PDF can be shown; a Word file gets a short note
    and the Download button. The file comes from the staff-only route, so the
    reader works only while signed in.
--}}
@php
    /** @var \App\Models\JobApplication $application */
    $application = $getRecord();
    $isPdf = $application->cv_mime === 'application/pdf';
@endphp

@if ($isPdf)
    @vite('resources/js/cv-viewer.js')

    <div
        data-cv-viewer
        data-src="{{ route('applications.cv', $application) }}"
        class="overflow-hidden rounded-xl bg-gray-100 ring-1 ring-gray-200"
    >
        <div class="flex items-center justify-between gap-3 border-b border-gray-200 bg-white px-4 py-2 text-sm text-gray-600">
            <span data-cv-page class="tabular-nums">Loading</span>

            <span class="flex items-center gap-1">
                <button type="button" data-cv-zoom-out aria-label="Zoom out" class="flex size-8 items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 disabled:opacity-40">
                    <x-filament::icon icon="heroicon-m-minus" class="size-4" />
                </button>
                <span data-cv-zoom-label class="w-12 text-center tabular-nums">100%</span>
                <button type="button" data-cv-zoom-in aria-label="Zoom in" class="flex size-8 items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 disabled:opacity-40">
                    <x-filament::icon icon="heroicon-m-plus" class="size-4" />
                </button>
            </span>
        </div>

        <div data-cv-scroll class="relative h-[calc(100vh-17rem)] min-h-[34rem] overflow-auto [scrollbar-color:var(--gray-300)_transparent] [scrollbar-width:thin]">
            <p data-cv-status class="absolute inset-0 flex items-center justify-center text-sm text-gray-500">Loading the CV…</p>
            <div data-cv-pages class="flex min-w-min flex-col items-center gap-4 p-4"></div>
        </div>
    </div>
@else
    <div class="flex flex-col items-center gap-3 rounded-xl bg-gray-50 px-6 py-16 text-center ring-1 ring-gray-200">
        <span class="flex size-12 items-center justify-center rounded-full bg-white text-gray-400 ring-1 ring-gray-200">
            <x-filament::icon icon="heroicon-o-document-text" class="size-6" />
        </span>
        <p class="text-sm font-medium text-gray-950">Word documents can't be previewed here</p>
        <p class="max-w-sm text-sm text-gray-500">Browsers can only show PDFs. Download the file to read it.</p>
    </div>
@endif
