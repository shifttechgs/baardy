<x-mail::message>
# New website enquiry

**{{ $enquiry['name'] }}** would like to talk about **{{ $enquiry['interest'] }}**, nearest branch **{{ $enquiry['branch'] }}**.

<x-mail::table>
| | |
| :-- | :-- |
| Phone | {{ $enquiry['phone'] }} |
| Email | {{ $enquiry['email'] ?? 'Not given' }} |
| Interested in | {{ $enquiry['interest'] }} |
| Nearest branch | {{ $enquiry['branch'] }} |
</x-mail::table>

@if (filled($enquiry['message'] ?? null))
**Their message**

{{ $enquiry['message'] }}
@endif

Sent from the "Get in touch" form on {{ config('app.url') }}.
</x-mail::message>
