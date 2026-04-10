<!DOCTYPE html>
<html lang="en" x-data x-bind:class="$store.ui.dark ? 'dark' : ''">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="CLT Manager — Supplier, Layup & Layer data workspace">
    <title>{{ $title ?? 'Dashboard' }} — CLT Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[rgb(var(--app-bg))] text-[rgb(var(--text-main))] transition-colors duration-300">

@php
    try {
        $notifLogs = \App\Models\ActivityLog::latest('created_at')->limit(6)->get();
    } catch (\Throwable) {
        $notifLogs = collect();
    }
@endphp

{{-- ── Mobile overlay backdrop ─────────────────────────────────────── --}}
<div x-cloak
     x-show="$store.ui.mobileSidebarOpen"
     @click="$store.ui.closeMobile()"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-30 bg-slate-950/60 backdrop-blur-sm lg:hidden">
</div>

{{-- ── Sidebar ───────────────────────────────────────────────────────── --}}
<aside x-bind:class="{
            '-translate-x-full': !$store.ui.mobileSidebarOpen,
            'translate-x-0':      $store.ui.mobileSidebarOpen,
            'lg:w-[72px]':        $store.ui.sidebarCollapsed,
            'lg:w-[260px]':      !$store.ui.sidebarCollapsed
         }"
       class="sidebar-wrap fixed inset-y-0 left-0 z-40 w-[260px] flex flex-col px-3 py-5 transition-all duration-300 ease-out lg:translate-x-0">

    {{-- Logo & collapse button --}}
    <div class="flex items-center justify-between px-1 pb-5">
        <div class="flex items-center gap-3 min-w-0">
            {{-- Icon --}}
            <div class="grid h-9 w-9 flex-shrink-0 place-items-center rounded-xl bg-gradient-to-br from-[rgb(var(--brand))] to-[rgb(var(--brand-alt))] shadow-lg shadow-emerald-500/20">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3"/>
                </svg>
            </div>
            {{-- Brand name --}}
            <div x-cloak x-show="!$store.ui.sidebarCollapsed"
                 x-transition:enter="transition-all duration-300 delay-100"
                 x-transition:enter-start="opacity-0 -translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="overflow-hidden">
                <p class="font-display text-sm font-bold text-[rgb(var(--text-main))] leading-tight tracking-tight">SPECtoolbox</p>
                <p class="text-[10px] uppercase font-bold tracking-widest text-[rgb(var(--text-muted))] leading-tight">Workspace</p>
            </div>
        </div>

        {{-- Desktop collapse toggle --}}
        <button @click="$store.ui.toggleSidebar()"
                id="sidebar-collapse-btn"
                title="Toggle sidebar"
                class="hidden lg:grid h-7 w-7 place-items-center rounded-lg text-[rgb(var(--text-muted))] hover:bg-[rgb(var(--sidebar-item-hover))] hover:text-[rgb(var(--text-main))] transition-all duration-200 flex-shrink-0">
            <svg x-bind:class="$store.ui.sidebarCollapsed ? 'rotate-180' : ''"
                 class="w-4 h-4 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden space-y-0.5 pb-4">

        <div x-show="!$store.ui.sidebarCollapsed" class="sidebar-section-title">Main</div>
        <x-sidebar-link route="dashboard"       icon="home" badge="New">Dashboard</x-sidebar-link>
        <x-sidebar-link route="suppliers.index" icon="building">Suppliers</x-sidebar-link>
        <x-sidebar-link route="layups.index"    icon="layers">Layups</x-sidebar-link>
        <x-sidebar-link route="layers.index"    icon="stack">Layers</x-sidebar-link>

        <div x-show="!$store.ui.sidebarCollapsed" class="sidebar-section-title">Data</div>
        <x-sidebar-link route="import.index" icon="upload">Import</x-sidebar-link>
        <x-sidebar-link route="export.index" icon="download">Export</x-sidebar-link>

        <div x-show="!$store.ui.sidebarCollapsed" class="sidebar-section-title">System</div>
        <x-sidebar-link route="activity-logs.index" icon="log">Activity Logs</x-sidebar-link>
    </nav>

</aside>

{{-- ── Main content area ────────────────────────────────────────────── --}}
<div x-bind:class="$store.ui.sidebarCollapsed ? 'lg:pl-[72px]' : 'lg:pl-[260px]'"
     class="min-h-screen transition-all duration-300 ease-out">

    {{-- ── Topbar ──────────────────────────────────────────────────── --}}
    <header class="sticky top-0 z-20 clt-topbar px-4 sm:px-6">
        <div class="flex h-16 items-center gap-3">

            {{-- Mobile hamburger --}}
            <button @click="$store.ui.toggleMobile()"
                    id="mobile-menu-btn"
                    class="grid h-10 w-10 place-items-center rounded-xl border border-[rgba(var(--text-main),0.08)] bg-[rgb(var(--card-bg))] text-[rgb(var(--text-soft))] hover:border-[rgb(var(--brand))] hover:text-[rgb(var(--brand))] transition-all duration-200 lg:hidden">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>

            {{-- Page title --}}
            <div class="min-w-0 flex-1">
                <h1 class="font-display text-xl font-bold leading-tight text-[rgb(var(--text-main))]">{{ $title ?? 'Dashboard' }}</h1>
                <p class="text-xs text-[rgb(var(--text-soft))]">{{ now()->format('l, F jS Y') }}</p>
            </div>

            {{-- Right actions --}}
            <div class="flex items-center gap-2">

                {{-- Dark mode toggle --}}
                <button @click="$store.ui.toggleDark()"
                        id="dark-mode-btn"
                        title="Toggle dark mode"
                        class="grid h-9 w-9 place-items-center rounded-xl text-[rgb(var(--text-soft))] hover:bg-[rgba(var(--text-main),0.06)] hover:text-[rgb(var(--text-main))] transition-all duration-200">
                    <svg x-show="!$store.ui.dark" class="w-4.5 h-4.5" style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>
                    </svg>
                    <svg x-cloak x-show="$store.ui.dark" class="w-4.5 h-4.5" style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
                    </svg>
                </button>

                {{-- Notification bell --}}
                <div x-data="{ open: false }" class="relative hidden sm:block">
                    <button @click="open = !open"
                            id="notif-btn"
                            class="relative grid h-9 w-9 place-items-center rounded-xl text-[rgb(var(--text-soft))] hover:bg-[rgba(var(--text-main),0.06)] hover:text-[rgb(var(--text-main))] transition-all duration-200">
                        <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                        </svg>
                        @if($notifLogs->isNotEmpty())
                            <span class="notif-dot"></span>
                        @endif
                    </button>

                    {{-- Notifications dropdown --}}
                    <div x-cloak x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                         class="absolute right-0 mt-2 w-80 clt-card p-1 z-50">
                        <div class="px-3 py-2.5 border-b border-[rgba(var(--text-main),0.06)]">
                            <p class="text-sm font-bold text-[rgb(var(--text-main))]">Notifications</p>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            @forelse($notifLogs as $log)
                                <div class="flex gap-3 px-3 py-2.5 rounded-xl hover:bg-[rgba(var(--text-main),0.04)] transition-colors">
                                    <div class="mt-0.5 grid h-6 w-6 flex-shrink-0 place-items-center rounded-full bg-indigo-100 dark:bg-indigo-500/15">
                                        <div class="w-1.5 h-1.5 rounded-full bg-[rgb(var(--brand))]"></div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm text-[rgb(var(--text-main))] truncate">{{ $log->description }}</p>
                                        <p class="text-xs text-[rgb(var(--text-soft))] mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 text-center">
                                    <p class="text-sm text-[rgb(var(--text-soft))]">No notifications yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                </div>
        </div>
    </header>

    {{-- ── Page content ────────────────────────────────────────────── --}}
    <main class="px-4 pb-8 pt-5 sm:px-6 lg:px-7">
        @if(session('success'))
            <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-500/20 dark:bg-emerald-500/10 px-4 py-3">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 dark:border-red-500/20 dark:bg-red-500/10 px-4 py-3">
                <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                </svg>
                <p class="text-sm font-medium text-red-700 dark:text-red-400">{{ session('error') }}</p>
            </div>
        @endif

        <div class="animate-rise">
            {{ $slot }}
        </div>
    </main>
</div>

</body>
</html>
