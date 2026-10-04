<x-mail::message>
# New job application

**{{ $application->name }}** applied for **{{ $application->vacancy?->title ?? 'a general application' }}**. Their CV is attached.

<x-mail::table>
| | |
| :-- | :-- |
| Phone | {{ $application->phone }} |
| Email | {{ $application->email ?? 'Not given' }} |
@if ($application->vacancy)
| Location | {{ $application->vacancy->location }} |
@endif
</x-mail::table>

@if (filled($application->message))
**Their note**

{{ $application->message }}
@endif
</x-mail::message>
