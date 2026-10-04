{{--
    WhatsApp call to action -- the site's one WhatsApp button, as first set in
    the FAQ card: a pill with the chat icon, the label, and a purple circle
    carrying an arrow. Opens the chat in a new tab.

    Props
      number  international number, with or without the +
      label   the button text
      tone    'paper' (default): a white pill, for dark and tinted surfaces
              'mist': a lavender pill, for white surfaces
      text    a message to start the chat with (optional)
--}}
@props(['number', 'label', 'tone' => 'paper', 'text' => null])

<a
    href="https://wa.me/{{ ltrim($number, '+') }}{{ filled($text) ? '?text='.rawurlencode($text) : '' }}"
    target="_blank"
    rel="noopener noreferrer"
    {{ $attributes->class([
        'group inline-flex h-11 w-fit items-center gap-2.5 rounded-full pr-1.5 pl-4 text-small font-medium whitespace-nowrap select-none transition-colors duration-150',
        'bg-paper text-ink hover:bg-mist' => $tone === 'paper',
        'bg-mist text-ink hover:bg-line' => $tone === 'mist',
    ]) }}
>
    <x-ui.icon name="chat" />
    {{ $label }}
    <span class="inline-flex size-8 items-center justify-center rounded-full bg-accent text-paper transition-transform duration-300 group-hover:translate-x-0.5">
        <x-ui.icon name="arrow-right" />
    </span>
</a>
