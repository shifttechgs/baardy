{{--
    Floating WhatsApp button, on every page.

    Opens a chat with the head office's WhatsApp number (the first number in
    config/company.php -> branches labelled WhatsApp), with a greeting already
    typed. Renders nothing if no WhatsApp number is configured. Sits above the
    page but below the header; hidden in print.
--}}
@php
    $whatsapp = collect(config('company.branches'))
        ->pluck('phones')
        ->flatten(1)
        ->first(fn (array $phone): bool => str_contains($phone['label'], 'WhatsApp'));

    $greeting = 'Hello Baardy Micro Capital, I would like to find out more about your loans.';
@endphp

@if ($whatsapp)
    <a
        href="https://wa.me/{{ ltrim($whatsapp['tel'], '+') }}?text={{ rawurlencode($greeting) }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Chat with us on WhatsApp"
        class="group/wa fixed right-3 bottom-3 z-40 inline-flex size-12 sm:size-13 items-center justify-center rounded-full bg-[#25D366] text-white shadow-[0_16px_32px_-12px_rgb(0_0_0/0.45)]
               transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_20px_40px_-12px_rgb(0_0_0/0.5)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#25D366]
               sm:right-4 sm:bottom-4 print:hidden"
    >
        <span aria-hidden="true" class="absolute inset-0 -z-10 animate-ping rounded-full bg-[#25D366]/30 [animation-duration:2.8s] motion-reduce:hidden"></span>
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="size-6.5 shrink-0">
            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91A9.85 9.85 0 0 0 12.04 2Zm0 18.15h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24a8.2 8.2 0 0 1 8.23 8.25c0 4.54-3.7 8.23-8.24 8.23Zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.12-.16.25-.64.81-.78.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.1-.22-.16-.47-.28Z" />
        </svg>
    </a>
@endif
