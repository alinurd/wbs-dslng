<!DOCTYPE html>
<html>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'PT DONGGI-SENORO LNG' }}</title>
    <link rel="Shortcut Icon" href="{{ asset('assets/images/logo_donggi.ico') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles


 
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}">
</head>

<body class="bg-gray-50 font-sans">
    <livewire:wbs-landing.layout />

 
    @livewireScripts
    
    <script>
     window.AppConfig = {
        routes: {
            languageChange: '{{ route('language.change') }}'
        },
        csrfToken: '{{ csrf_token() }}',
        currentLocale: '{{ $currentLocale }}'
    };
</script>
    <script src="{{ asset('assets/js/landing.js') }}"></script>

</body>

</html>
