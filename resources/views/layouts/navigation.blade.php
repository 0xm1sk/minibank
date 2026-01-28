<nav x-data="{ open: false }" class="bg-gray-800 border-b border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @auth
                        @if(in_array(Auth::user()->role_id, [1, 2, 3]))
                            <a href="{{ route('client.dashboard') }}">
                        @elseif(in_array(Auth::user()->role_id, [4, 5, 6, 7]))
                            <a href="{{ route('employee.dashboard') }}">
                        @elseif(Auth::user()->role_id == 8)
                            <a href="{{ route('admin.dashboard') }}">
                        @else
                            <a href="/">
                        @endif
                    @else
                        <a href="/">
                    @endauth
                        <div class="flex items-center space-x-3">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-200" />
                            <span class="text-xl font-bold text-gray-200">MiniBank</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if(in_array(Auth::user()->role_id, [1, 2, 3]))
                            <x-nav-link :href="route('client.dashboard')" :active="request()->routeIs('client.*')">
                                {{ __('My Dashboard') }}
                            </x-nav-link>
                        @elseif(in_array(Auth::user()->role_id, [4, 5, 6, 7]))
                            <x-nav-link :href="route('employee.dashboard')" :active="request()->routeIs('employee.*')">
                                {{ __('Employee Dashboard') }}
                            </x-nav-link>
                        @elseif(in_array(Auth::user()->role_id, [5, 6, 7, 8]))
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                                {{ __('Admin Dashboard') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Theme Toggle -->
                <div id="theme-toggle" class="theme-toggle me-4">
                    <div class="theme-toggle-slider">
                        <svg class="w-4 h-4 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                        </svg>
                    </div>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-400 bg-gray-800 hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-300 hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-gray-300 transition duration-150 ease-in-out">
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
            @auth
                @if(in_array(Auth::user()->role_id, [1, 2, 3]))
                    <x-responsive-nav-link :href="route('client.dashboard')" :active="request()->routeIs('client.*')">
                        {{ __('My Dashboard') }}
                    </x-responsive-nav-link>
                @elseif(in_array(Auth::user()->role_id, [4, 5, 6, 7]))
                    <x-responsive-nav-link :href="route('employee.dashboard')" :active="request()->routeIs('employee.*')">
                        {{ __('Employee Dashboard') }}
                    </x-responsive-nav-link>
                @elseif(in_array(Auth::user()->role_id, [5, 6, 7, 8]))
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                        {{ __('Admin Dashboard') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
