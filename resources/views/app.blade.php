<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Light mode favicon -->
        <link rel="icon" type="image/png" href="/favicon-light.png" media="(prefers-color-scheme: light)">
        <!-- Dark mode favicon -->
        <link rel="icon" type="image/png" href="/favicon-dark.png" media="(prefers-color-scheme: dark)">

        <!-- ✅ AdSense -->
        <script async
            src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4474320596321568"
            crossorigin="anonymous">
        </script>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div id="app" data-page="{{ json_encode($page) }}"></div>
    </body>
</html>