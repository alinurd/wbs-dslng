<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'PT DONGGI-SENORO LNG' }}</title>
    <link rel="Shortcut Icon" href="{{ asset('assets/images/logo_donggi.ico') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <style>
        :root {
            --color-dslng-blue: #0033A0;
            --color-dslng-light-blue: #0072CE;
            --color-dslng-red: #E4002B;
            --color-dslng-gray: #53565A;
        }

        .hero-bg {
            background: linear-gradient(135deg, var(--color-dslng-blue), var(--color-dslng-light-blue));
        }

        .news-card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .required-info-item {
            border-left: 4px solid var(--color-dslng-red);
            transition: all 0.3s ease;
        }

        .required-info-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }

        /* Sticky Header */
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            transform: translateY(0);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sticky-header.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        /* Desktop Navigation */
        .nav-link {
            position: relative;
            padding: 0.5rem 0;
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--color-dslng-blue);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Language Dropdown */
        .language-dropdown {
            position: relative;
        }



        .dropdown-menu {
            transform: translateY(-10px);
            transition: all 0.3s ease;
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 50;
        }

        /* Mobile Menu */
        @media (max-width: 767px) {
            .sticky-header {
                z-index: 1001;
            }
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <livewire:wbs-landing.layout />

 
    @livewireScripts
    <script>
        // Mobile Menu Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.getElementById('mainHeader');
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const mobileMenu = document.getElementById('mobileMenu');

            // Sticky header on scroll
            window.addEventListener('scroll', () => {
                if (window.scrollY > 100) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });

            // Mobile menu toggle
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isHidden = mobileMenu.classList.contains('hidden');

                    if (isHidden) {
                        mobileMenu.classList.remove('hidden');
                        this.innerHTML = '<i class="fas fa-times text-xl"></i>';
                        this.classList.add('text-blue-600');
                    } else {
                        mobileMenu.classList.add('hidden');
                        this.innerHTML = '<i class="fas fa-bars text-xl"></i>';
                        this.classList.remove('text-blue-600');
                    }
                });

                // Close mobile menu when clicking on links
                mobileMenu.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A') {
                        mobileMenu.classList.add('hidden');
                        mobileMenuButton.innerHTML = '<i class="fas fa-bars text-xl"></i>';
                        mobileMenuButton.classList.remove('text-blue-600');
                    }
                });

                // Close mobile menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!header.contains(e.target)) {
                        mobileMenu.classList.add('hidden');
                        if (mobileMenuButton) {
                            mobileMenuButton.innerHTML = '<i class="fas fa-bars text-xl"></i>';
                            mobileMenuButton.classList.remove('text-blue-600');
                        }
                    }
                });
            }

            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            }
        });


        window.addEventListener('reload-page', () => {
            Livewire.navigate(window.location.href);
        });
    </script>


</body>

</html>
