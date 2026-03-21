<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#0ea5e9">
  <link rel="manifest" href="/manifest.json">
  <title>@yield('title', 'E-Barangay Health')</title>

  <!-- 1. Anti-FOUC Critical Style -->
  <style>
    [x-cloak] { display: none !important; }
    /* Hide the body immediately to prevent unstyled flash */
    html { background-color: #f9fafb; }
    body { opacity: 0; }
  </style>

  <!-- 2. TailwindCSS Config + Local (Must load in head) -->
  <script src="{{ asset('js/tailwindcss.js') }}"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 
              400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 
              800: '#075985', 900: '#0c4a6e',
            }
          },
          fontFamily: {
            sans: ['ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif'],
          },
          boxShadow: {
            soft: '0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -2px rgba(0,0,0,0.025)',
            card: '0 6px 12px -2px rgba(0,0,0,0.06), 0 3px 7px -3px rgba(0,0,0,0.04)'
          }
        }
      }
    }
  </script>

  <!-- 3. Local CSS Assets -->
  <link href="{{ asset('css/bootstrap-icons.css') }}" rel="stylesheet">

  <!-- 4. Essential JS (Non-blocking where possible) -->
  <script src="{{ asset('js/axios.js') }}"></script>
  <script>
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    let token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    }
  </script>
  <script src="{{ asset('js/alpine.js') }}" defer></script>

  <!-- 5. Reveal Script -->
  <script>
    (function() {
      const reveal = () => {
        document.body.style.transition = 'opacity 0.4s ease-in-out';
        document.body.style.opacity = '1';
      };
      
      // Reveal on DOM ready, but with a slight delay for Tailwind
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => setTimeout(reveal, 150));
      } else {
        setTimeout(reveal, 150);
      }
      
      // Fallback reveal after 2 seconds in case something blocks DOMContentLoaded
      setTimeout(reveal, 2000);
    })();
  </script>

  {{-- 6. Connectivity Monitoring --}}
  <script>
    window.addEventListener('online', () => {
        document.dispatchEvent(new CustomEvent('connectivity-change', { detail: { online: true } }));
    });
    window.addEventListener('offline', () => {
        document.dispatchEvent(new CustomEvent('connectivity-change', { detail: { online: false } }));
    });
  </script>

  <style>
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #64748b; }

    /* Accessibility / Elderly Friendly */
    body { font-size: 1.125rem; line-height: 1.75; }
    h1, h2, h3, h4, h5, h6 { letter-spacing: -0.01em; color: #111827; }
  </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans text-gray-900 antialiased" x-data="{ sidebarOpen: false }">

    @php
        $user = auth()->user();
        if ($user) {
            $unreadNotifications = $user->unreadNotifications;
            $readNotifications = $user->readNotifications()->latest()->take(5 - $unreadNotifications->count())->get();
            $notifications = $unreadNotifications->merge($readNotifications);
            $unreadCount = $unreadNotifications->count();
            
            // Count unread announcement notifications
            $unreadAnnouncementsCount = $unreadNotifications->filter(function($n) {
                return str_contains($n->type, 'NewAnnouncementNotification');
            })->count();
        } else {
            $notifications = collect();
            $unreadCount = 0;
            $unreadAnnouncementsCount = 0;
        }
    @endphp

  @if (!request()->routeIs('login') && !request()->routeIs('register'))
      
      {{-- Sidebar (Fixed Left) --}}
      @include('layouts.sidebar', [
          'notifications' => $notifications, 
          'unreadCount' => $unreadCount,
          'unreadAnnouncementsCount' => $unreadAnnouncementsCount
      ])

      {{-- Main Wrapper (Pushed right on desktop) --}}
      <div class="md:ml-64 flex flex-col min-h-screen transition-all duration-300 ease-in-out print:ml-0">
          
          {{-- Navbar (Sticky Top) --}}
          @include('layouts.navbar', ['notifications' => $notifications, 'unreadCount' => $unreadCount])

          {{-- Main Content --}}
          <main class="flex-1 overflow-x-hidden">
              <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                  {{-- Flash Messages --}}
                  <div class="mb-6 space-y-4">
                      @if(session('success'))
                      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                           class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-100 rounded-2xl shadow-sm animate-in fade-in slide-in-from-top-4 duration-300">
                          <div class="flex items-center gap-3">
                              <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20">
                                  <i class="bi bi-check2-circle text-xl"></i>
                              </div>
                              <div>
                                  <p class="text-xs font-black text-emerald-800 uppercase tracking-widest">Success</p>
                                  <p class="text-sm font-bold text-emerald-600 mt-0.5">{{ session('success') }}</p>
                              </div>
                          </div>
                          <button @click="show = false" class="p-2 text-emerald-400 hover:text-emerald-600 transition-colors">
                              <i class="bi bi-x-lg"></i>
                          </button>
                      </div>
                      @endif

                      @if(session('error'))
                      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)" 
                           class="flex items-center justify-between p-4 bg-red-50 border border-red-100 rounded-2xl shadow-sm animate-in fade-in slide-in-from-top-4 duration-300">
                          <div class="flex items-center gap-3">
                              <div class="w-10 h-10 rounded-xl bg-red-500 text-white flex items-center justify-center shadow-lg shadow-red-500/20">
                                  <i class="bi bi-exclamation-triangle text-xl"></i>
                              </div>
                              <div>
                                  <p class="text-xs font-black text-red-800 uppercase tracking-widest">Error</p>
                                  <p class="text-sm font-bold text-red-600 mt-0.5">{{ session('error') }}</p>
                              </div>
                          </div>
                          <button @click="show = false" class="p-2 text-red-400 hover:text-red-600 transition-colors">
                              <i class="bi bi-x-lg"></i>
                          </button>
                      </div>
                      @endif

                      @if(session('info'))
                      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                           class="flex items-center justify-between p-4 bg-blue-50 border border-blue-100 rounded-2xl shadow-sm animate-in fade-in slide-in-from-top-4 duration-300">
                          <div class="flex items-center gap-3">
                              <div class="w-10 h-10 rounded-xl bg-blue-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/20">
                                  <i class="bi bi-info-circle text-xl"></i>
                              </div>
                              <div>
                                  <p class="text-xs font-black text-blue-800 uppercase tracking-widest">Notice</p>
                                  <p class="text-sm font-bold text-blue-600 mt-0.5">{{ session('info') }}</p>
                              </div>
                          </div>
                          <button @click="show = false" class="p-2 text-blue-400 hover:text-blue-600 transition-colors">
                              <i class="bi bi-x-lg"></i>
                          </button>
                      </div>
                      @endif
                  </div>

                  @yield('content')
              </div>
          </main>

          <footer class="bg-white border-t border-gray-100 py-4 px-6 text-center text-xs text-gray-500">
              &copy; {{ date('Y') }} E-Barangay Health System. All rights reserved.
          </footer>
      </div>

  @else
      {{-- Login/Register Layout (Full Screen) --}}
      @yield('content')
  @endif

  {{-- FullCalendar JS Local --}}
  <script src="{{ asset('js/fullcalendar.js') }}"></script>

  {{-- Page-specific scripts --}}
  @yield('scripts')

  {{-- Service Worker Registration --}}
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js')
          .then(registration => {
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
          }, err => {
            console.log('ServiceWorker registration failed: ', err);
          });
      });
    }
  </script>

</body>
</html>
