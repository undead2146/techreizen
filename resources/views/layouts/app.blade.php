<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Techreizen') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS must be first -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Pure Tailwind CSS for styling enhancements -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            important: true,
            corePlugins: {
                preflight: false,
            },
        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

    <!-- Custom CSS -->
    <style>
        [x-cloak] { display: none !important; }
        
        .navbar-nav {
            display: flex !important;
            align-items: center;
        }
        
        .nav-item {
            display: block !important;
            margin: 0 6px;
        }
        
        .nav-btn {
            @apply px-4 py-2 font-bold rounded transition duration-200 ease-in-out inline-flex items-center justify-center;
        }
        
        .nav-btn-blue {
            @apply bg-blue-500 hover:bg-blue-700 text-white;
        }
        
        .nav-btn-green {
            @apply bg-green-500 hover:bg-green-600 text-white;
        }
        
        .nav-btn-gray {
            @apply bg-gray-100 hover:bg-gray-200 text-gray-800;
        }
        
        .nav-btn-red {
            @apply bg-red-500 hover:bg-red-600 text-white;
        }
        
        button, .btn {
            display: inline-block !important;
            font-weight: 600 !important;
        }

        /* Fix dropdown menu positioning and appearance */
        .dropdown-menu {
            margin-top: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 0.375rem;
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
            font-weight: 500;
        }
        
        .dropdown-item:hover {
            background-color: #f3f4f6;
        }
        
        /* Fix dropdown alignment issues */
        .nav-item.dropdown .nav-link.dropdown-toggle::after {
            vertical-align: middle;
            margin-left: 0.5em;
        }
        
        .nav-btn-dropdown {
            padding-right: 0.5rem;  /* Reduced right padding to accommodate dropdown arrow */
        }

        /* Custom responsive fixes - removing collapse behavior */
        @media (max-width: 767.98px) {
            .navbar-collapse {
                display: flex !important;
                flex-basis: auto;
            }
            
            .navbar-nav {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .nav-item {
                margin: 0.25rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header with UCLL logo -->
    <div style="z-index: 100; position: relative;" class="border-b border-gray-200">
        <div class="flex items-center bg-light py-2 px-4">
            <div class="flex-shrink-0 mr-4">
                <img src="{{ asset('images/ucll_logo.png') }}" class="rounded h-16" alt="logo ucll">
            </div>
            <div class="flex flex-col">
                <h1 class="text-red-600 text-2xl font-bold mb-0 mt-2">TECHNOLOGIE</h1>
                <h3 class="text-green-600 text-lg">internationalisering - studiereizen</h3>
            </div>
        </div>
    </div>
    
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            @if (Auth::check() && Auth::user()->role === 'traveller')
                <a class="navbar-brand" href="{{ url('/traveller/home') }}">Home</a>
            @elseif (Auth::check() && Auth::user()->role === 'guest')
                <a class="navbar-brand" href="{{ url('/guest/home') }}">Home</a>
            @elseif (Auth::check() && Auth::user()->role === 'guide')
                <a class="navbar-brand" href="{{ url('/guide/home') }}">Home</a>
            @else
                <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Home') }}</a>
            @endif
            
            <!-- Always visible navigation -->
            <div class="navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <div class="nav-btn nav-btn-blue">
                                        <i class="fas fa-sign-in-alt mr-2"></i>{{ __('Login') }}
                                    </div>
                                </a>
                            </li>
                        @endif
                    @else
                        @if (Auth::user()->role === 'traveller')
                            <!-- Mijn Reis Dropdown - Restored and fixed alignment -->
                            <li class="nav-item dropdown">
                                <a id="myTripDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <span class="nav-btn nav-btn-dropdown ">
                                        <i class="fas fa-plane-departure mr-2"></i>{{ __('Mijn Reis') }}
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="myTripDropdown">
                                    <a class="dropdown-item" href="{{ route('groups.index') }}">
                                        <i class="fas fa-users mr-2"></i>{{ __('Mijn Groepen') }}
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-clipboard-list mr-2"></i>{{ __('Reisschema') }}
                                    </a>
                                </div>
                            </li>
                        @elseif(Auth::user()->role === 'guide' || Auth::user()->role === 'admin')
                            <!-- Fix dropdown for guides/admins -->
                            <li class="nav-item dropdown">
                                <a id="guideDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <span class="nav-btn nav-btn-dropdown ">
                                        <i class="fas fa-user-tie mr-2"></i>{{ __('Guide Menu') }}
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="guideDropdown">
                                    <a class="dropdown-item" href="{{ route('guide.home') }}">
                                        <i class="fas fa-home mr-2"></i>{{ __('Dashboard') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('guide.groups.index') }}">
                                        <i class="fas fa-users-cog mr-2"></i>{{ __('Groepen Beheren') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('groups.create') }}">
                                        <i class="fas fa-plus-circle mr-2"></i>{{ __('Nieuwe Groep') }}
                                    </a>
                                </div>
                            </li>
                        @endif
                        
                        @if (Auth::user()->role === 'guest' && Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <div class="nav-btn nav-btn-green">
                                    <i class="fas fa-user-plus mr-2"></i>{{ __('Register') }}
                                </div>
                            </a>
                        </li>
                    @endif
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="nav-btn nav-btn-gray nav-btn-dropdown">
                                    <i class="fas fa-id-card mr-2 text-gray-600"></i>{{ Auth::user()->name ?? Auth::user()->login }}
                                </span>
                            </a>



                            
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt mr-2"></i>{{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>



                        


                        
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>
