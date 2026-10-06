{{--
    Structured data: pushes one JSON-LD block into the page head.

    Pass either a full document (with @context) or a list of nodes, which is
    wrapped in a @graph. Built with App\Support\StructuredData.
--}}
@props(['data'])

@php
    $document = isset($data['@context']) ? $data : \App\Support\StructuredData::graph($data);
@endphp

@push('head')
    <script type="application/ld+json">{!! json_encode($document, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
