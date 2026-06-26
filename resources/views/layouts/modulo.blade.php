<!DOCTYPE html>
<html lang="es" >
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Esterisur')</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="h-full bg-slate-50 font-sans antialiased">

<div id="sidebar-overlay"
     class="fixed inset-0 z-20 bg-black/40 backdrop-blur-sm hidden lg:hidden"
     onclick="toggleSidebar()"></div>

<div class="flex h-full min-h-screen">

    {{-- SIDEBAR --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-30 flex w-60 flex-col bg-white border-r border-slate-200
                  transform -translate-x-full transition-transform duration-300 ease-in-out
                  lg:relative lg:translate-x-0">

        {{-- Logo + módulo actual --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-500">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6
                             1.5 1.5 0 013 7.5v9A1.5 1.5 0 006 18h12a1.5 1.5 0 001.5-1.5v-9
                             a1.5 1.5 0 00-.598-1.5 11.959 11.959 0 01-8.402-2.286z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-800">Esterisur</p>
                <p class="text-xs text-slate-400">@yield('modulo-label', 'Panel')</p>
            </div>
        </div>

        {{-- Nav del módulo --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
            @yield('sidebar-nav')
        </nav>

        {{-- Usuario --}}
        <div class="border-t border-slate-100 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400">{{ auth()->user()->getRoleLabel() }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-slate-600 transition-colors" title="Cerrar sesión">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6
                                     a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0
                                     005.25 21h6a2.25 2.25 0 002.25-2.25V15
                                     m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- CONTENIDO --}}
    <div class="flex flex-1 flex-col min-w-0 overflow-hidden">

        {{-- Topbar --}}
        <header class="flex items-center gap-3 border-b border-slate-200 bg-white px-4 py-3 lg:px-6">
            <button onclick="toggleSidebar()"
                    class="lg:hidden rounded-md p-1.5 text-slate-500 hover:bg-slate-100 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
            <div class="flex-1">
                <h1 class="text-base font-semibold text-slate-800">@yield('page-title')</h1>
                <p class="text-xs text-slate-400 hidden sm:block">@yield('page-subtitle')</p>
            </div>
            <span class="hidden sm:block text-xs text-slate-400">
                {{ now()->locale('es')->isoFormat('ddd D [de] MMMM') }}
            </span>
        </header>

        <main class="flex-1 overflow-y-auto p-4 lg:p-6">

            @if(session('success'))
                <div class="mb-4 flex items-center gap-3 rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-800">
                    <svg class="h-4 w-4 text-teal-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <svg class="h-4 w-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const isOpen  = !sidebar.classList.contains('-translate-x-full');
        sidebar.classList.toggle('-translate-x-full', isOpen);
        overlay.classList.toggle('hidden', isOpen);
    }
</script>

</body>
</html>