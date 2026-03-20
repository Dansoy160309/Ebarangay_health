<nav class="bg-white border-b border-gray-100 h-16 sticky top-0 z-30 print:hidden shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex justify-between items-center h-full">

            {{-- Left Side: Page Title --}}
            <div class="flex items-center gap-2 sm:gap-4 overflow-hidden flex-1">
                <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-50 transition-colors shrink-0">
                    <i class="bi bi-list text-2xl"></i>
                </button>
                <h2 class="text-sm sm:text-xl font-black text-gray-900 tracking-tight truncate leading-tight">
                    @yield('title', 'Dashboard')
                </h2>
            </div>

            {{-- Right Side: Actions --}}
            <div class="flex items-center gap-1.5 sm:gap-4">

                {{-- Notifications --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 sm:p-2.5 rounded-xl text-gray-400 hover:text-brand-600 hover:bg-brand-50 transition-all relative">
                        <i class="bi bi-bell text-xl sm:text-xl"></i>
                        @if($unreadCount > 0)
                            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                        @endif
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="fixed inset-x-2 top-16 md:absolute md:inset-x-auto md:right-0 md:top-full md:mt-3 md:w-96 bg-white border border-gray-100 text-gray-800 shadow-2xl md:shadow-xl rounded-2xl md:rounded-xl overflow-hidden z-50 ring-1 ring-black/5"
                         style="display: none;"
                         x-cloak>
                        
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 backdrop-blur-sm">
                            <span class="font-bold text-lg text-gray-800">Notifications</span>
                            <a href="{{ route('notifications.markAll') }}" class="text-sm text-brand-600 hover:text-brand-700 font-semibold">Mark all read</a>
                        </div>

                        <div class="max-h-[70vh] md:max-h-96 overflow-y-auto custom-scrollbar">
                            @forelse($notifications as $notification)
                                <a href="{{ route('notifications.read', $notification->id) }}"
                                   class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 transition border-b border-gray-50 last:border-0 {{ is_null($notification->read_at) ? 'bg-brand-50/50' : '' }}">
                                    <div class="flex-shrink-0 mt-1">
                                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full {{ is_null($notification->read_at) ? 'bg-brand-100 text-brand-600' : 'bg-gray-100 text-gray-500' }}">
                                            <i class="bi bi-info-circle-fill text-lg"></i>
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm sm:text-base font-medium text-gray-900 truncate">
                                            {{ \Illuminate\Support\Str::limit($notification->data['message'] ?? 'New notification', 60) }}
                                        </p>
                                        <p class="text-[10px] sm:text-sm text-gray-500 mt-1">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if(is_null($notification->read_at))
                                        <span class="inline-block w-2.5 h-2.5 bg-brand-600 rounded-full mt-2"></span>
                                    @endif
                                </a>
                            @empty
                                <div class="px-5 py-10 text-center">
                                    <div class="mx-auto h-16 w-16 text-gray-300 mb-4 flex items-center justify-center bg-gray-50 rounded-full">
                                        <i class="bi bi-bell-slash text-3xl"></i>
                                    </div>
                                    <p class="text-base text-gray-500">No new notifications</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 text-center">
                            <a href="{{ route('notifications.index') }}" class="text-base text-brand-600 hover:text-brand-800 font-medium hover:underline">View all notifications</a>
                        </div>
                    </div>
                </div>

                {{-- User Profile --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 sm:gap-3 p-1 rounded-2xl hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center font-black text-sm border border-brand-200 shadow-sm">
                            {{ substr(auth()->user()->first_name, 0, 1) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-xs font-black text-gray-900 leading-none">{{ auth()->user()->full_name }}</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                            </p>
                        </div>
                        <i class="bi bi-chevron-down text-[10px] text-gray-400"></i>
                    </button>

                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute right-0 top-full mt-3 w-56 bg-white border border-gray-100 text-gray-800 shadow-2xl rounded-2xl overflow-hidden z-50 ring-1 ring-black/5"
                         style="display: none;"
                         x-cloak>
                        
                        <div class="p-4 border-b border-gray-50 bg-gray-50/50">
                            <p class="text-xs font-black text-gray-900 leading-none truncate">{{ auth()->user()->full_name }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1.5">{{ auth()->user()->email }}</p>
                        </div>

                        <div class="p-2">
                            @if(auth()->user()->role === 'patient')
                                <a href="{{ route('patient.profile.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-gray-600 hover:text-brand-600 hover:bg-brand-50 rounded-xl transition-all group">
                                    <i class="bi bi-person text-lg opacity-50 group-hover:opacity-100 transition-opacity"></i>
                                    My Profile
                                </a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-red-500 hover:bg-red-50 rounded-xl transition-all group text-left">
                                    <i class="bi bi-box-arrow-right text-lg opacity-50 group-hover:opacity-100 transition-opacity"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</nav>
