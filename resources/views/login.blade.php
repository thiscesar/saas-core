<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Login - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center justify-center min-h-screen">
        <div class="w-full max-w-md">
            <div class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg p-8 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)]">
                <h1 class="text-2xl font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-6">Login</h1>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-[#fff2f2] dark:bg-[#1D0002] border border-[#F53003] dark:border-[#F61500] rounded-sm">
                        <ul class="text-sm text-[#F53003] dark:text-[#FF4433]">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:border-black dark:focus:border-white"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            Password
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:border-black dark:focus:border-white"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full mt-4 px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec] rounded-sm text-sm font-medium hover:bg-black dark:hover:bg-white transition-all"
                    >
                        Login
                    </button>
                </form>
            </div>
        </div>
    </body>
</html>
