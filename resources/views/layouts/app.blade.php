<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Система управления заданиями')</title>
    @vite(['resources/css/app.css'])
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}

    @vite('resources/css/app.css')

    <!-- CodeMirror CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @yield('styles')
</head>

<body>
    @unless(View::hasSection('noHeaderFooter'))
    @include('inc.header')
    @endunless

    <div class="container">
        @yield('content')
    </div>

    @unless(View::hasSection('noHeaderFooter'))
    @include('inc.footer')
    @endunless

    <!-- Scripts -->
    @vite(['resources/js/app.js', 'resources/js/admin-sidebar.js'])
    @stack('scripts')
</body>

</html>