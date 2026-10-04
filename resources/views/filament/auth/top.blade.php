{{-- Sign-in page, above the card: the lockup left, a way back to the site right. --}}
<div class="absolute inset-x-0 top-0 flex items-center justify-between px-6 py-5 sm:px-10 sm:py-7">
    <a href="{{ url('/') }}" class="flex items-center gap-2.5">
        <img src="{{ asset('images/baardy-mark.png') }}" alt="" class="size-8">
        <span class="text-[1.0625rem] font-semibold tracking-[-0.03em] text-gray-950">Baardy <span class="font-normal text-gray-500">Micro Capital</span></span>
    </a>

    <a href="{{ url('/') }}" class="text-sm text-gray-500 transition-colors hover:text-gray-900">
        Back to the website &rarr;
    </a>
</div>
