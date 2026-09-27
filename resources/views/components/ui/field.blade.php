{{--
    Form field wrapper.

    Pairs a real <label> with its control and an optional hint, wiring the
    `for`/`id` and `aria-describedby` relationships so the control is
    announced correctly. The control itself is passed in the slot.

    Props
      id     the control's id -- required for the label association
      label  visible label text
      hint   help text, linked via aria-describedby
--}}
@props([
    'id',
    'label',
    'hint' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-2']) }}>
    <label for="{{ $id }}" class="text-small font-medium text-ink">
        {{ $label }}
    </label>

    {{ $slot }}

    @if ($hint)
        <p id="{{ $id }}-hint" class="text-micro text-muted">
            {{ $hint }}
        </p>
    @endif
</div>
