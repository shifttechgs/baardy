<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Something went wrong | {{ config('company.name') }}</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f6f4f8; color: #151019; font: 16px/1.5 system-ui, sans-serif; }
        main { max-width: 32rem; padding: 2rem 1.25rem; }
        h1 { font-size: 2rem; font-weight: 400; letter-spacing: -0.02em; margin: 0 0 .75rem; }
        p { color: #413b4a; margin: 0 0 1.5rem; }
        a { display: inline-block; background: #5e2681; color: #fff; padding: .75rem 1.5rem; border-radius: 999px; text-decoration: none; }
    </style>
</head>
<body>
    <main>
        <h1>Something went wrong on our side</h1>
        <p>We have been told and will fix it. Please try again in a few minutes, or call us on {{ config('company.contact.phone') }}.</p>
        <a href="/">Back to the home page</a>
    </main>
</body>
</html>
