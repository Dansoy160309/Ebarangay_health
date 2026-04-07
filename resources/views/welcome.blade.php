<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Barangay Health</title>

    <!-- Bootstrap Icons -->
    <link href="{{ asset('css/bootstrap-icons.css') }}" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="{{ asset('js/tailwindcss.js') }}"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7', // Primary brand color
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    },
                    fontFamily: {
                        sans: ['ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'],
                    },
                    boxShadow: {
                        'card': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                        'soft': '0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025)',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        @media (max-width: 768px) {
            body { padding-bottom: 74px; }
        }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-800" x-data="{ mobileMenuOpen: false }">

    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 h-24 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-brand-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-500/30">
                    <i class="bi bi-heart-pulse-fill text-2xl"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900 tracking-tight">E-Barangay Health</span>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-10 text-base font-medium text-gray-600">
                <a href="#features" class="hover:text-brand-600 transition-colors">Features</a>
                <a href="#about" class="hover:text-brand-600 transition-colors">About Us</a>
                <a href="#contact" class="hover:text-brand-600 transition-colors">Contact</a>
            </div>

            <div class="hidden md:flex items-center gap-4">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="px-6 py-3 text-base font-semibold text-gray-700 hover:text-brand-600 transition-colors">Log in</a>

                        <a href="{{ route('login') }}" class="px-6 py-3 bg-brand-600 text-white text-base font-semibold rounded-xl shadow-lg shadow-brand-500/30 hover:bg-brand-700 hover:shadow-brand-500/40 transition-all transform hover:-translate-y-0.5">
                            Get Started
                        </a>
                    @endif
            </div>

            <!-- Mobile Menu Button with Visible Login Text -->
            <div class="md:hidden flex items-center gap-3">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">Log In</a>
                @endif
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-gray-600 hover:text-brand-600 focus:outline-none">
                    <i class="bi bi-list text-3xl" x-show="!mobileMenuOpen"></i>
                    <i class="bi bi-x-lg text-3xl" x-show="mobileMenuOpen" x-cloak></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Drawer -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden absolute top-20 left-0 w-full bg-white border-b border-gray-100 shadow-xl z-40"
             style="display: none;">
            <div class="px-6 py-4 space-y-4">
                <a href="#features" @click="mobileMenuOpen = false" class="block text-base font-medium text-gray-600 hover:text-brand-600 hover:bg-gray-50 px-4 py-2 rounded-lg transition-colors">Features</a>
                <a href="#about" @click="mobileMenuOpen = false" class="block text-base font-medium text-gray-600 hover:text-brand-600 hover:bg-gray-50 px-4 py-2 rounded-lg transition-colors">About Us</a>
                <a href="#contact" @click="mobileMenuOpen = false" class="block text-base font-medium text-gray-600 hover:text-brand-600 hover:bg-gray-50 px-4 py-2 rounded-lg transition-colors">Contact</a>
                
                <div class="pt-4 border-t border-gray-100 flex flex-col gap-3">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="block text-center w-full px-5 py-3 text-base font-semibold text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors">Log in</a>
                        <a href="{{ route('login') }}" class="block text-center w-full px-5 py-3 bg-brand-600 text-white text-base font-semibold rounded-xl shadow-lg shadow-brand-500/30 hover:bg-brand-700 transition-colors">
                            Get Started
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative overflow-hidden min-h-[88vh] pt-16 pb-24 lg:pt-24 lg:pb-28">
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="flex flex-col lg:flex-row items-center lg:items-start gap-20">
                
                <!-- Hero Content -->
                <div class="lg:w-1/2 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-brand-700 rounded-full text-sm font-semibold mb-8 border border-blue-100">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                        </span>
                        Digital Health Services
                    </div>
                    
                    <h1 class="text-6xl lg:text-7xl font-extrabold text-gray-900 leading-tight mb-6">
                        Better Healthcare for <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600">Our Community</span>
                    </h1>
                    
                    <p class="text-xl lg:text-2xl text-gray-600 mb-10 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Access barangay health services from the comfort of your home. Book appointments, view health records, and stay updated with the latest health advisories.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-5 justify-center lg:justify-start">
                        <a href="{{ route('login') }}" class="px-10 py-5 bg-brand-600 text-white font-black rounded-2xl shadow-lg shadow-brand-500/30 hover:bg-brand-700 hover:shadow-brand-500/40 transition-all transform hover:-translate-y-1 text-center flex items-center justify-center gap-3 group text-base">
                            Get Started
                            <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="#features" class="px-10 py-5 bg-white text-gray-700 font-bold rounded-2xl shadow-md border border-gray-100 hover:bg-gray-50 transition-all text-center text-base">
                            Learn More
                        </a>
                    </div>

                    <div class="mt-14 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-10 text-gray-600">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-check-circle-fill text-green-500 text-2xl"></i>
                            <span class="text-base font-semibold">Easy Scheduling</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="bi bi-check-circle-fill text-green-500 text-2xl"></i>
                            <span class="text-base font-semibold">Secure Records</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="bi bi-check-circle-fill text-green-500 text-2xl"></i>
                            <span class="text-base font-semibold">24/7 Access</span>
                        </div>
                    </div>
                </div>

                <!-- Hero Image/Illustration -->
                <div class="lg:w-1/2 relative">
                    {{-- Blob Background --}}
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120%] h-[120%] bg-gradient-to-tr from-brand-100 to-blue-50 rounded-full blur-3xl opacity-60 z-0"></div>
                    
                    {{-- Main Image Container --}}
                    <div class="relative z-10 rounded-[2.5rem] overflow-hidden shadow-2xl border-4 border-white transform rotate-2 hover:rotate-0 transition-all duration-500 max-h-[520px]">
                        <img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?q=80&w=2070&auto=format&fit=crop" alt="Doctor" class="w-full h-[340px] md:h-[420px] lg:h-[480px] object-cover">
                    </div>

                    {{-- Floating Card 1: Active Patients --}}
                    <div class="absolute -right-6 top-10 z-20 bg-white p-5 rounded-3xl shadow-soft flex items-center gap-4 animate-bounce" style="animation-duration: 3s;">
                        <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center text-brand-600">
                            <i class="bi bi-people-fill text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm text-gray-500 font-semibold uppercase tracking-wider">Active Patients</p>
                            <p class="text-2xl font-bold text-gray-900">500+</p>
                        </div>
                    </div>

                    {{-- Floating Card 2: Appointment Confirmed --}}
                    <div class="absolute -left-10 bottom-16 z-20 bg-white p-5 rounded-3xl shadow-soft flex items-center gap-4 animate-bounce" style="animation-duration: 4s; animation-delay: 1s;">
                        <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                            <i class="bi bi-check-lg text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm text-gray-500 font-semibold uppercase tracking-wider">Appointment</p>
                            <p class="text-2xl font-bold text-gray-900">Confirmed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile-First Home Cards (New) -->
    <section id="mobile-home" class="py-8 bg-gray-50 md:hidden">
        <div class="max-w-md mx-auto px-4">
            <div class="bg-white rounded-3xl shadow-soft border border-gray-100 p-5 mb-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-brand-600 text-white flex items-center justify-center shadow-lg">
                            <i class="bi bi-heart-pulse-fill text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-brand-600">CareHaven</p>
                            <h2 class="text-xl font-black text-gray-900">Better Healthcare for Our Community</h2>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-brand-600 text-white text-xs font-black rounded-xl shadow-lg hover:bg-brand-700 transition-colors">Get Started</a>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">Access barangay health services from home. Book appointments, view records, and stay updated with health advisories.</p>
            </div>

            <div class="bg-white rounded-3xl shadow-soft border border-gray-100 p-4 mb-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Popular Services</h3>
                    <a href="#features" class="text-xs font-bold text-brand-600 hover:text-brand-700">See all</a>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('login') }}" class="p-3 rounded-2xl bg-blue-50 hover:bg-blue-100 transition-colors flex flex-col items-center text-center">
                        <span class="text-xs font-black text-gray-900">Online Booking</span>
                    </a>
                    <a href="{{ route('login') }}" class="p-3 rounded-2xl bg-purple-50 hover:bg-purple-100 transition-colors flex flex-col items-center text-center">
                        <span class="text-xs font-black text-gray-900">Health Records</span>
                    </a>
                    <a href="{{ route('login') }}" class="p-3 rounded-2xl bg-emerald-50 hover:bg-emerald-100 transition-colors flex flex-col items-center text-center">
                        <span class="text-xs font-black text-gray-900">Emergency Hotline</span>
                    </a>
                    <a href="{{ route('login') }}" class="p-3 rounded-2xl bg-orange-50 hover:bg-orange-100 transition-colors flex flex-col items-center text-center">
                        <span class="text-xs font-black text-gray-900">Health Advisories</span>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-soft border border-gray-100 p-4 mb-20">
                <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Barangay Vitality</h3>
                <div class="flex items-center justify-between gap-3">
                    <div class="flex-1 p-4 rounded-2xl bg-brand-50 border border-brand-100">
                        <p class="text-xs font-black uppercase tracking-widest text-gray-500">Health Score</p>
                        <p class="text-3xl font-black text-brand-700">98<span class="text-base">%</span></p>
                        <p class="text-xs text-gray-500">Vaccination & Outreach</p>
                    </div>
                    <div class="flex-1 p-4 rounded-2xl bg-white border border-gray-100">
                        <p class="text-xs font-black uppercase tracking-widest text-gray-500">Active Alerts</p>
                        <p class="text-3xl font-black text-gray-900">12</p>
                        <p class="text-xs text-gray-500">Critical updates this week</p>
                    </div>
                </div>
            </div>
        </div>


    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl opacity-50 -mr-32 -mt-32"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="text-brand-600 font-black text-[10px] uppercase tracking-[0.2em] mb-3 block">What we offer</span>
                <h2 class="text-4xl font-black text-gray-900 mb-6">Comprehensive Health Services</h2>
                <p class="text-gray-500 text-lg leading-relaxed">Everything you need to manage your health and wellness efficiently within your local community.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-10">
                {{-- Feature 1 --}}
                <div class="bg-white p-10 rounded-[2.5rem] shadow-card hover:shadow-soft transition-all duration-500 group border border-gray-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-brand-50 rounded-full -mr-16 -mt-16 group-hover:bg-brand-100 transition-colors"></div>
                    <div class="w-16 h-16 bg-brand-100 rounded-2xl flex items-center justify-center text-brand-600 text-3xl mb-8 group-hover:scale-110 transition-transform relative z-10">
                        <i class="bi bi-calendar2-check-fill"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4 relative z-10">Online Appointment</h3>
                    <p class="text-gray-500 leading-relaxed mb-6 relative z-10">Skip the long lines. Book your check-ups and consultations online with your preferred healthcare provider.</p>
                    <ul class="space-y-3 relative z-10">
                        <li class="flex items-center gap-3 text-sm font-bold text-gray-600">
                            <i class="bi bi-check2 text-brand-500"></i> Easy Scheduling
                        </li>
                        <li class="flex items-center gap-3 text-sm font-bold text-gray-600">
                            <i class="bi bi-check2 text-brand-500"></i> Real-time Availability
                        </li>
                    </ul>
                </div>

                {{-- Feature 2 --}}
                <div class="bg-white p-10 rounded-[2.5rem] shadow-card hover:shadow-soft transition-all duration-500 group border border-gray-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-full -mr-16 -mt-16 group-hover:bg-purple-100 transition-colors"></div>
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 text-3xl mb-8 group-hover:scale-110 transition-transform relative z-10">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4 relative z-10">Digital Records</h3>
                    <p class="text-gray-500 leading-relaxed mb-6 relative z-10">Securely access your medical history, prescriptions, and lab results anytime from any device.</p>
                    <ul class="space-y-3 relative z-10">
                        <li class="flex items-center gap-3 text-sm font-bold text-gray-600">
                            <i class="bi bi-check2 text-purple-500"></i> HIPAA Compliant
                        </li>
                        <li class="flex items-center gap-3 text-sm font-bold text-gray-600">
                            <i class="bi bi-check2 text-purple-500"></i> Easy Data Export
                        </li>
                    </ul>
                </div>

                {{-- Feature 3 --}}
                <div class="bg-white p-10 rounded-[2.5rem] shadow-card hover:shadow-soft transition-all duration-500 group border border-gray-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-amber-50 rounded-full -mr-16 -mt-16 group-hover:bg-amber-100 transition-colors"></div>
                    <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 text-3xl mb-8 group-hover:scale-110 transition-transform relative z-10">
                        <i class="bi bi-megaphone-fill"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4 relative z-10">Health Alerts</h3>
                    <p class="text-gray-500 leading-relaxed mb-6 relative z-10">Stay informed about vaccination drives, medical missions, and health alerts in your local community.</p>
                    <ul class="space-y-3 relative z-10">
                        <li class="flex items-center gap-3 text-sm font-bold text-gray-600">
                            <i class="bi bi-check2 text-amber-500"></i> Real-time Notifications
                        </li>
                        <li class="flex items-center gap-3 text-sm font-bold text-gray-600">
                            <i class="bi bi-check2 text-amber-500"></i> Community Updates
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="py-24 bg-gray-50 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center gap-20">
                <div class="lg:w-1/2 relative">
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-brand-100 rounded-full blur-3xl opacity-60"></div>
                    <div class="relative z-10 rounded-[3rem] overflow-hidden shadow-2xl border-8 border-white">
                        <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=2070&auto=format&fit=crop" alt="Clinic" class="w-full h-[500px] object-cover">
                    </div>
                    <div class="absolute -bottom-10 -right-10 bg-white p-8 rounded-[2.5rem] shadow-soft z-20 border border-gray-100 hidden md:block">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                                <i class="bi bi-award-fill text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-black text-gray-900">10+</p>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Years Service</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">Dedicated to community <br> healthcare since 2014.</p>
                    </div>
                </div>
                
                <div class="lg:w-1/2">
                    <span class="text-brand-600 font-black text-[10px] uppercase tracking-[0.2em] mb-3 block">Our Mission</span>
                    <h2 class="text-4xl font-black text-gray-900 mb-8 leading-tight">Committed to Bridging the <br> <span class="text-brand-600">Healthcare Gap</span></h2>
                    
                    <div class="space-y-8">
                        <div class="flex gap-6">
                            <div class="flex-shrink-0 w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-brand-600 text-xl border border-gray-100">
                                <i class="bi bi-eye-fill"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-gray-900 mb-2">Our Vision</h4>
                                <p class="text-gray-500 leading-relaxed font-medium">To be the model digital health platform that empowers every barangay with accessible and high-quality medical services.</p>
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <div class="flex-shrink-0 w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-brand-600 text-xl border border-gray-100">
                                <i class="bi bi-bullseye"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-gray-900 mb-2">Our Mission</h4>
                                <p class="text-gray-500 leading-relaxed font-medium">To provide a seamless, secure, and user-friendly digital workspace for healthcare providers and patients to manage health efficiently.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 pt-12 border-t border-gray-200 grid grid-cols-2 gap-8">
                        <div>
                            <p class="text-3xl font-black text-gray-900 mb-1">2.5k+</p>
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Total Consultations</p>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-gray-900 mb-1">98%</p>
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Patient Satisfaction</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="bg-gray-900 rounded-[3rem] p-8 md:p-16 overflow-hidden relative">
                {{-- Decorative blobs --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-brand-600 rounded-full blur-[120px] opacity-20 -mr-48 -mt-48"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-600 rounded-full blur-[120px] opacity-20 -ml-48 -mb-48"></div>

                <div class="relative z-10 flex flex-col lg:flex-row gap-16">
                    <div class="lg:w-1/2">
                        <span class="text-brand-400 font-black text-[10px] uppercase tracking-[0.2em] mb-3 block">Get in touch</span>
                        <h2 class="text-4xl font-black text-white mb-8">We're here to help you <br> stay healthy</h2>
                        
                        <div class="space-y-8">
                            <div class="flex items-center gap-6 group">
                                <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-brand-400 text-2xl group-hover:bg-brand-600 group-hover:text-white transition-all">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Visit Us</p>
                                    <p class="text-white font-bold">Barangay Hall, Health Center Section <br> Quezon City, Metro Manila</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-6 group">
                                <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-brand-400 text-2xl group-hover:bg-brand-600 group-hover:text-white transition-all">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Email Us</p>
                                    <p class="text-white font-bold text-lg">support@e-barangayhealth.com</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-6 group">
                                <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-brand-400 text-2xl group-hover:bg-brand-600 group-hover:text-white transition-all">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Call Us</p>
                                    <p class="text-white font-bold text-lg">+63 (02) 8123-4567</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:w-1/2">
                        <div class="bg-white/5 backdrop-blur-md p-8 rounded-[2.5rem] border border-white/10">
                            <h4 class="text-xl font-black text-white mb-8">Send us a message</h4>
                            <form action="#" class="space-y-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <input type="text" placeholder="Your Name" class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-3.5 text-white text-sm font-bold focus:ring-2 focus:ring-brand-500/50 outline-none placeholder-gray-500 transition-all">
                                    </div>
                                    <div>
                                        <input type="email" placeholder="Your Email" class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-3.5 text-white text-sm font-bold focus:ring-2 focus:ring-brand-500/50 outline-none placeholder-gray-500 transition-all">
                                    </div>
                                </div>
                                <div>
                                    <input type="text" placeholder="Subject" class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-3.5 text-white text-sm font-bold focus:ring-2 focus:ring-brand-500/50 outline-none placeholder-gray-500 transition-all">
                                </div>
                                <div>
                                    <textarea placeholder="How can we help?" rows="4" class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-3.5 text-white text-sm font-bold focus:ring-2 focus:ring-brand-500/50 outline-none placeholder-gray-500 transition-all"></textarea>
                                </div>
                                <button type="submit" class="w-full py-4 bg-brand-600 text-white font-black rounded-xl hover:bg-brand-700 transition-all shadow-lg shadow-brand-500/20 uppercase tracking-widest text-xs">
                                    Send Message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-white/5 pt-20 pb-12 overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="grid md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                            <i class="bi bi-heart-pulse-fill text-xl"></i>
                        </div>
                        <span class="text-2xl font-black text-white tracking-tight">E-Barangay Health</span>
                    </div>
                    <p class="text-gray-400 font-medium leading-relaxed max-w-sm mb-8">
                        Providing professional digital health solutions to improve community wellness. Secure, efficient, and accessible healthcare for every resident.
                    </p>
                    <div class="flex items-center gap-4">
                        <a href="#" class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center text-gray-400 hover:bg-brand-600 hover:text-white transition-all"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center text-gray-400 hover:bg-brand-600 hover:text-white transition-all"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center text-gray-400 hover:bg-brand-600 hover:text-white transition-all"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>

                <div>
                    <h5 class="text-white font-black text-sm uppercase tracking-widest mb-6">Quick Links</h5>
                    <ul class="space-y-4">
                        <li><a href="#features" class="text-gray-400 hover:text-brand-400 transition-colors font-bold text-sm">Features</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-brand-400 transition-colors font-bold text-sm">About Us</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-brand-400 transition-colors font-bold text-sm">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-white font-black text-sm uppercase tracking-widest mb-6">Patient Portal</h5>
                    <ul class="space-y-4">
                        <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-brand-400 transition-colors font-bold text-sm">Book Appointment</a></li>
                        <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-brand-400 transition-colors font-bold text-sm">View Health Records</a></li>
                        <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-brand-400 transition-colors font-bold text-sm">Health Advisories</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-12 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6 text-gray-500 text-xs font-black uppercase tracking-widest">
                <p>&copy; {{ date('Y') }} E-Barangay Health System. All rights reserved.</p>
                <div class="flex items-center gap-8">
                    <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
