<x-mail::message>
@if ($isRepeat)
# {{ $enquiry['name'] }} enquired again

They already have an open lead ({{ $lead->reference }}, {{ $lead->stage->getLabel() }}). This enquiry has been added to it.
@else
# New lead {{ $lead->reference }}

**{{ $enquiry['name'] }}** would like to talk about **{{ $enquiry['interest'] }}**, nearest branch **{{ $enquiry['branch'] }}**.
@endif

<x-mail::table>
| | |
| :-- | :-- |
| Phone | {{ $enquiry['phone'] }} |
| Email | {{ $enquiry['email'] ?? 'Not given' }} |
| Interested in | {{ $enquiry['interest'] }} |
| Nearest branch | {{ $enquiry['branch'] }} |
| Came from | {{ $lead->sourceLabel() }} |
@if ($promotion)
| Tracking code | {{ $promotion->tracking_code }} |
@endif
</x-mail::table>

@if (filled($enquiry['message'] ?? null))
**Their message**

{{ $enquiry['message'] }}
@endif

<x-mail::button :url="$leadUrl">
Open the lead
</x-mail::button>

Call them back from the lead so the team can see it has been handled.
</x-mail::message>
