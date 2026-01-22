<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="min-h-screen font-sans antialiased bg-base-200">
        {{-- Mobile Nav (hidden on desktop) --}}
        <x-nav sticky class="lg:hidden">
            <x-slot:brand>
                <div class="ml-5 pt-5">
                    <div class="text-xl font-bold">{{ config('app.name') }}</div>
                </div>
            </x-slot:brand>
            <x-slot:actions>
                <label for="main-drawer" class="lg:hidden mr-3">
                    <x-icon name="o-bars-3" class="cursor-pointer" />
                </label>
            </x-slot:actions>
        </x-nav>

        {{-- Main Container --}}
        <x-main>
            {{-- Sidebar --}}
            <x-slot:sidebar drawer="main-drawer" collapsible collapse-text="Recolher" class="bg-base-200 lg:bg-transparent">
                <div class="flex h-full flex-col">
                    {{-- Brand --}}
                    <div class="mary-hideable mb-5 ml-5 pt-5">
                        <div class="text-xl font-bold">{{ config('app.name') }}</div>
                    </div>

                    <x-menu-separator />

                    {{-- User Profile Section --}}
                    <div class="mary-sidebar-section px-5 py-4">
                        <x-dropdown>
                            <x-slot:trigger>
                                <div class="flex items-center justify-center gap-3 cursor-pointer hover:bg-base-300 rounded-lg p-2 transition-colors">
                                    <x-user-avatar :user="auth()->user()" class="!w-10" />

                                    <div class="mary-hideable min-w-0 flex-1">
                                        <div class="truncate text-sm font-semibold">{{ auth()->user()->name }}</div>
                                        <div class="truncate text-xs text-base-content/70">{{ auth()->user()->email }}</div>
                                    </div>

                                    <x-icon name="o-chevron-down" class="mary-hideable w-4 h-4" />
                                </div>
                            </x-slot:trigger>

                            <x-menu-item title="Minha Conta" icon="o-user-circle" link="/settings" />

                            <x-menu-separator />

                            <form action="/logout" method="POST">
                                @csrf
                                <x-menu-item title="Sair" icon="o-arrow-right-on-rectangle" onclick="this.closest('form').submit()" />
                            </form>
                        </x-dropdown>
                    </div>

                    <x-menu-separator />

                    {{-- Navigation Menu --}}
                    <x-menu activate-by-route>
                        {{-- Dashboard --}}
                        <x-menu-item title="Dashboard" icon="o-home" link="/dashboard" />

                        {{-- Users Submenu (Admin only) --}}
                        @if(auth()->user()->is_admin ?? false)
                            <x-menu-item title="Usuários" icon="o-users" link="/users" />
                        @endif

                        <x-menu-separator />

                        {{-- Help --}}
                        <x-menu-item title="Ajuda" icon="o-question-mark-circle" link="/help" />
                    </x-menu>

                </div>
            </x-slot:sidebar>

            {{-- Content --}}
            <x-slot:content>
                {{ $slot }}
            </x-slot:content>
        </x-main>

        {{-- Toast Notifications --}}
        <x-toast />

        @livewireScripts
    </body>
</html>
