<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Esterisur') — Panel de Control</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="h-full bg-slate-50 font-sans antialiased">

{{-- Overlay mobile --}}
<div
    id="sidebar-overlay"
    class="fixed inset-0 z-20 bg-black/40 backdrop-blur-sm hidden lg:hidden"
    onclick="toggleSidebar()"
></div>

<div class="flex h-full min-h-screen">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside
        id="sidebar"
        class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-slate-900 text-white
               transform -translate-x-full transition-transform duration-300 ease-in-out
               lg:relative lg:translate-x-0 lg:flex"
    >
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700/60">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-teal-500">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6
                             1.5 1.5 0 013 7.5v9A1.5 1.5 0 006 18h12a1.5 1.5 0 001.5-1.5v-9
                             a1.5 1.5 0 00-.598-1.5 11.959 11.959 0 01-8.402-2.286z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold tracking-wide text-white">Esterisur</p>
                <p class="text-xs text-slate-400">Trazabilidad</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">

            <p class="px-3 pt-2 pb-1 text-xs font-semibold uppercase tracking-widest text-slate-500">General</p>

            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75
                             C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z
                             M9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25
                             c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625z
                             M16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75
                             c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
                Dashboard
            </a>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest text-slate-500">Operaciones</p>

            <a href="#" class="nav-link {{ request()->routeIs('admin.lotes*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622
                             a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5
                             h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125
                             H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
                Lotes
            </a>

            <a href="#" class="nav-link">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5
                             M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625
                             a1.875 1.875 0 010-3.75z" />
                </svg>
                Recepción
            </a>

            <a href="#" class="nav-link">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5
                             M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082
                             a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591
                             L19.8 15.3M14.25 3.104c.251.023.501.05.75.082
                             M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15
                             a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8
                             l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611
                             A48.309 48.309 0 0112 21a48.309 48.309 0 01-8.135-.687
                             c-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                </svg>
                Esterilización
            </a>

            <a href="#" class="nav-link">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0
                             m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25
                             m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0
                             h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0
                             00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25
                             M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106
                             a48.554 48.554 0 00-10.026 0
                             1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677
                             m0 4.5v-4.5m0 0h-12" />
                </svg>
                Entregas
            </a>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest text-slate-500">Administración</p>

            <a href="{{ route('admin.usuarios.index') }}"
               class="nav-link {{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0
                             004.121-.952 4.125 4.125 0 00-7.533-2.493
                             M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07
                             M15 19.128v.106A12.318 12.318 0 018.624 21
                             c-2.331 0-4.512-.645-6.374-1.766l-.001-.109
                             a6.375 6.375 0 0111.964-3.07M12 6.375
                             a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />
                </svg>
                Usuarios
            </a>

            <a href="{{ route('admin.instituciones.index') }}"
               class="nav-link {{ request()->routeIs('admin.instituciones*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18
                             M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15
                             m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125
                             h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
                Instituciones
            </a>


            <a href="{{ route('admin.protocolos.index') }}"
               class="nav-link {{ request()->routeIs('admin.protocolos*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 0-.231-.035-.454-.1-.664M6.75 3.75A2.25 2.25 0 004.5 6v14.25A2.25 2.25 0 006.75 22.5h10.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6.75z" /></svg>
                Protocolos
            </a>

            <a href="{{ route('admin.equipos.index') }}"
               class="nav-link {{ request()->routeIs('admin.equipos*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                Equipos
            </a>
            <a href="#" class="nav-link">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621
                             A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25
                             H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3
                             H5.25A2.25 2.25 0 003 5.25m18 0H3" />
                </svg>
                Reportes
            </a>
        </nav>

        {{-- User footer --}}
        <div class="border-t border-slate-700/60 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-slate-400 truncate">Administrador</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-white transition-colors" title="Cerrar sesión">
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

    {{-- ===================== CONTENIDO PRINCIPAL ===================== --}}
    <div class="flex flex-1 flex-col min-w-0 overflow-hidden">

        {{-- Topbar --}}
        <header class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3 lg:px-6">
            <div class="flex items-center gap-3">
                {{-- Hamburger mobile --}}
                <button
                    onclick="toggleSidebar()"
                    class="lg:hidden rounded-md p-1.5 text-slate-500 hover:bg-slate-100 transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <div>
                    <h1 class="text-base font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-xs text-slate-400 hidden sm:block">@yield('page-subtitle', 'Panel de administración')</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                {{-- Fecha --}}
                <span class="hidden sm:block text-xs text-slate-400">
                    {{ now()->locale('es')->isoFormat('dddd D [de] MMMM, YYYY') }}
                </span>
            </div>
        </header>

        {{-- Main content --}}
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

<style>
    .nav-link {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: rgb(148 163 184);
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .nav-link:hover {
        background-color: rgb(30 41 59);
        color: white;
    }
    .nav-link.active {
        background-color: rgb(20 184 166 / 0.15);
        color: rgb(45 212 191);
    }
    .nav-link.active svg {
        color: rgb(45 212 191);
    }
</style>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const isOpen = !sidebar.classList.contains('-translate-x-full');
        if (isOpen) {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        } else {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        }
    }
</script>

</body>
</html>