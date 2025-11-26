<!DOCTYPE html>
<html lang="{{ $currentLocale }}">

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
    @include('components.partials.header')

    <main>
        {{ $slot }}
    </main>

    @include('components.partials.footer')

 @livewireScripts

                    <script>
                       // File: resources/js/captcha-handler.js
document.addEventListener('livewire:init', () => {
    disableLoginButton();

    Livewire.on('enable-login-button', () => {
        enableLoginButton();
    });

    Livewire.on('disable-login-button', () => {
        disableLoginButton();
    });
});

function enableLoginButton() {
    const loginBtns = document.querySelectorAll('.verif-btn');
    if (loginBtns.length > 0) {
        loginBtns.forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            btn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
        });
    }
}

function disableLoginButton() {
    const loginBtns = document.querySelectorAll('.verif-btn');
    if (loginBtns.length > 0) {
        loginBtns.forEach(btn => {
            btn.disabled = true;
            btn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
            btn.classList.add('bg-gray-400', 'cursor-not-allowed');
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[wire\\:submit="login"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.verif-btn');
            
            if (submitBtn && submitBtn.disabled) {
                e.preventDefault();
                e.stopPropagation();
                alert('{{ __("captcha.complete_verification_first") }}');
                return false;
            }
        });
    });
    
    disableLoginButton();
});

window.captchaVerified = false;
                    </script>

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
