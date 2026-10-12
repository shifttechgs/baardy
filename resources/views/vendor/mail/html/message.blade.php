<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')" />
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
{{ config('company.legal_name') }}  
Licensed by {{ config('company.compliance.regulator') }}  
{{ implode(', ', config('company.branches.0.address')) }}

This is an internal notification from the {{ config('company.name') }} website.
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
