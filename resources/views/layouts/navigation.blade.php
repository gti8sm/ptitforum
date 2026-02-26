<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-gray-200 bg-white">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-xl px-2.5 py-1.5 border border-gray-200 bg-white hover:bg-gray-50 transition">
                        <x-application-logo class="block h-8 w-auto fill-current text-gray-900" />
                        <span class="hidden sm:inline text-sm font-semibold tracking-tight text-gray-900">P'tit Forum</span>
                    </a>
                </div>

                @php($currentGroup = request()->route('group'))

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Tableau de bord
                    </x-nav-link>

                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('groups.*') || request()->routeIs('planning.*') || request()->routeIs('threads.*')">
                        Groupes
                    </x-nav-link>

                    @if($currentGroup)
                        <x-nav-link :href="route('groups.show', $currentGroup)" :active="request()->routeIs('groups.show') || request()->routeIs('threads.*')">
                            {{ $currentGroup->name }}
                        </x-nav-link>

                        <x-nav-link :href="route('planning.index', $currentGroup)" :active="request()->routeIs('planning.*')">
                            Planning
                        </x-nav-link>
                    @endif

                    @if(Auth::user()?->role === 'admin')
                        <x-nav-link href="/admin" :active="request()->is('admin*')">
                            Admin
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="me-2">
                    <x-dropdown align="right" width="96">
                        <x-slot name="trigger">
                            <button class="relative inline-flex items-center justify-center h-10 w-10 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition">
                                <svg class="h-5 w-5 text-gray-700" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 17H20L18.5951 15.5951C18.2141 15.2141 18 14.6973 18 14.1585V11C18 7.68629 15.3137 5 12 5C8.68629 5 6 7.68629 6 11V14.1585C6 14.6973 5.78595 15.2141 5.40493 15.5951L4 17H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10 17V18C10 19.1046 10.8954 20 12 20C13.1046 20 14 19.1046 14 18V17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>

                                @php($unreadNotificationsCount = Auth::user()?->unreadNotifications()->count() ?? 0)
                                @if($unreadNotificationsCount > 0)
                                    <span class="absolute -top-1 -right-1 min-w-5 h-5 px-1 inline-flex items-center justify-center rounded-full bg-gray-900 text-white text-[11px] font-semibold">
                                        {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
                                    </span>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-3">
                                <div class="text-sm font-semibold text-gray-900">Notifications</div>
                                <div class="mt-1 text-xs text-gray-600">Dernières activités</div>
                            </div>

                            <div class="border-t border-gray-200"></div>

                            <div class="max-h-80 overflow-auto">
                                @php($notifications = Auth::user()?->notifications()->latest()->limit(10)->get() ?? collect())
                                @forelse($notifications as $notification)
                                    @php($url = $notification->data['url'] ?? null)

                                    @if($url)
                                        <a href="{{ $url }}" class="block px-4 py-3 text-sm text-gray-800 hover:bg-gray-100 transition {{ $notification->read_at ? 'bg-white' : 'bg-gray-50' }}">
                                            <div class="font-semibold text-gray-900">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                            @if(! empty($notification->data['body']))
                                                <div class="mt-0.5 text-sm text-gray-700">{{ $notification->data['body'] }}</div>
                                            @endif
                                            <div class="mt-1 text-xs text-gray-500">{{ optional($notification->created_at)->diffForHumans() }}</div>
                                        </a>
                                    @else
                                        <div class="px-4 py-3 text-sm text-gray-800 {{ $notification->read_at ? 'bg-white' : 'bg-gray-50' }}">
                                            <div class="font-semibold text-gray-900">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                            @if(! empty($notification->data['body']))
                                                <div class="mt-0.5 text-sm text-gray-700">{{ $notification->data['body'] }}</div>
                                            @endif
                                            <div class="mt-1 text-xs text-gray-500">{{ optional($notification->created_at)->diffForHumans() }}</div>
                                        </div>
                                    @endif
                                @empty
                                    <div class="px-4 py-6 text-sm text-gray-700">Aucune notification pour le moment.</div>
                                @endforelse
                            </div>

                            <div class="border-t border-gray-200"></div>

                            <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="px-4 py-3">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 rounded-lg bg-white border border-gray-200 text-sm font-semibold text-gray-900 hover:bg-gray-50 transition">
                                    Tout marquer comme lu
                                </button>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:text-gray-900 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Se déconnecter
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-white/80 focus:outline-none focus:bg-white/80 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Tableau de bord
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('groups.*') || request()->routeIs('planning.*') || request()->routeIs('threads.*')">
                Groupes
            </x-responsive-nav-link>

            @if($currentGroup)
                <x-responsive-nav-link :href="route('groups.show', $currentGroup)" :active="request()->routeIs('groups.show') || request()->routeIs('threads.*')">
                    {{ $currentGroup->name }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('planning.index', $currentGroup)" :active="request()->routeIs('planning.*')">
                    Planning
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()?->role === 'admin')
                <x-responsive-nav-link href="/admin" :active="request()->is('admin*')">
                    Admin
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profil
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        Se déconnecter
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
