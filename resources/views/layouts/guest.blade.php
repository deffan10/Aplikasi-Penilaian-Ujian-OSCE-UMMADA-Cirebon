<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    html, body { height: 100%; overflow: hidden; }
    @media (max-height: 600px) {
        html, body { overflow: auto; }
    }
    @media (max-width: 640px) {
        .login-wrapper {
            flex-direction: column-reverse !important;
            gap: 0.75rem !important;
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }
    }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased h-full">
<div class="min-h-full flex flex-col sm:flex-row justify-center items-center px-3 sm:px-4 py-4 sm:py-6 bg-gray-100 gap-3 sm:gap-6 login-wrapper">
    <div class="shrink-0 order-2 sm:order-1">
        <a href="/">
            <x-application-logo class="w-16 h-16 sm:w-20 sm:h-20 fill-current text-gray-500" />
        </a>
    </div>

    <div class="w-full sm:max-w-md order-1 sm:order-2 mt-0 sm:mt-6 px-4 sm:px-6 py-4 sm:py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </div>
</div>
</body>
</html>