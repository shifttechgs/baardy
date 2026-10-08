{{--
    Spinner: a small ring that turns, for a button or a form that is working.
    Takes the surrounding text colour (a faint ring with a solid arc), so it
    suits any button. Decorative; say what is happening in text beside it.
--}}
<span {{ $attributes->merge(['class' => 'inline-block size-4 shrink-0 animate-spin rounded-full border-2 border-current/30 border-t-current']) }} aria-hidden="true"></span>
