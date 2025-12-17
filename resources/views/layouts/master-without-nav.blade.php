<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>@yield('title') | Taurus CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Meta Tags -->
    <meta content="Taurus Technologies CRM System" name="description" />
    <meta content="Taurus Technologies" name="author" />
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('images/favicon.ico') }}">
    
    <!-- Include head CSS -->
    @include('layouts.head-css')
    
    <!-- Page specific CSS -->
    @yield('css')
</head>

<body class="@yield('body-class')">
    
    <!-- Main Content -->
    @yield('content')
    
    <!-- Vendor Scripts -->
    @include('layouts.vendor-scripts')
    
    <!-- Page specific scripts -->
    @yield('script')
    
</body>
</html>