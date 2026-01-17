<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="min-h-screen font-sans antialiased">
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
            <x-slot:sidebar drawer="main-drawer" collapsible collapse-text="Recolher"  class="bg-base-200">
                <div class="flex h-full flex-col">
                    {{-- Brand --}}
                    <div class="mb-5 ml-5 pt-5">
                        <div class="text-xl font-bold">{{ config('app.name') }}</div>
                    </div>

                    <x-menu-separator />

                    {{-- User Profile Section --}}
                    <div class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <x-user-avatar :user="auth()->user()" class="!w-10" />

                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-semibold">{{ auth()->user()->name }}</div>
                                <div class="truncate text-xs text-base-content/70">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                    </div>

                    <x-menu-separator />

                    {{-- Navigation Menu --}}
                    <x-menu activate-by-route>
                        {{-- Dashboard --}}
                        <x-menu-item title="Dashboard" icon="o-home" link="/dashboard" />

                        {{-- Users Submenu (Admin only) --}}
                        @if(auth()->user()->is_admin ?? false)
                            <x-menu-sub title="Usuários" icon="o-users">
                                <x-menu-item title="Todos os Usuários" icon="o-users" link="/users" />
                                <x-menu-item title="Adicionar Usuário" icon="o-plus-circle" link="/users/create" />
                            </x-menu-sub>
                        @endif

                        <x-menu-separator />

                        {{-- Settings --}}
                        <x-menu-item title="Configurações" icon="o-cog-6-tooth" link="/settings" />

                        {{-- Help --}}
                        <x-menu-item title="Ajuda" icon="o-question-mark-circle" link="/help" />
                    </x-menu>

                    {{-- Spacer to push content below to bottom --}}
                    <div class="flex-1"></div>

                    {{-- Theme Toggle & Logout (bottom of sidebar) --}}
                    <x-menu-separator />

                    <div class="px-5 py-4 space-y-3">
                        {{-- Theme Toggle --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Tema</span>
                            <x-theme-toggle class="!w-auto" />
                        </div>

                        {{-- Logout Button --}}
                        <form action="/logout" method="POST">
                            @csrf
                            <x-button
                                label="Sair"
                                icon="o-arrow-right-on-rectangle"
                                type="submit"
                                class="btn-error btn-sm w-full"
                                no-wire-navigate
                            />
                        </form>
                    </div>
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
