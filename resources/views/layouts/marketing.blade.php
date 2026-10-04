{{--
    Public marketing layout.

    Carries the document head -- title, description, canonical and Open Graph
    structure -- plus the skip link, header and footer. Pages supply their own
    title and description via @section; everything else falls back to the
    company profile.

    Structured data (Organization / FinancialService / FAQPage) is deliberately
    NOT emitted yet. Publishing schema built from placeholder company details
    would assert unverified facts to search engines. See docs/SEO.md for the
    planned implementation.
--}}
@php
    $metaTitle = trim($__env->yieldContent('title')) ?: config('company.name');
    $metaDescription = trim($__env->yieldContent('description')) ?: config('company.description');
    $canonical = url()->current();

    // A page opts into the header floating over its first section by
    // setting its 'navbar' section to 'overlay'. Only the homepage does: its hero is a
    // full-bleed photograph the header is designed to sit on.
    $navbarOverlay = trim($__env->yieldContent('navbar')) === 'overlay';
@endphp

<!DOCTYPE html>
<html lang="en" class="scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ $canonical }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('company.name') }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:locale" content="en">
    {{-- TODO: add og:image once brand artwork exists (1200x630). --}}

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">

    <meta name="theme-color" content="#ffffff">

    {{-- The one typeface, fetched alongside the stylesheet rather than after
         it: text paints in Geist sooner and does not reflow when it lands. --}}
    <link rel="preload" href="{{ Vite::asset('node_modules/geist/dist/fonts/geist-sans/Geist-Variable.woff2') }}" as="font" type="font/woff2" crossorigin>

    {{-- Page-specific preloads (the hero photograph on the homepage). --}}
    @stack('head')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen antialiased">
    <a
        href="#main"
        class="sr-only rounded-sm bg-accent px-4 py-2 text-small font-medium text-paper
               focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-100"
    >
        Skip to content
    </a>

    <x-layout.navbar :overlay="$navbarOverlay" />

    <main id="main">
        @yield('content')
    </main>

    <x-layout.footer />

    <x-layout.whatsapp-button />
</body>
</html>
