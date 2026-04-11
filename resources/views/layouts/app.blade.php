<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — CLT Manager</title>

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-[rgb(var(--text-main))] bg-[rgb(var(--app-bg))] selection:bg-emerald-500/30">

    @php
        $recentActivity = \App\Models\ActivityLog::latest()->take(5)->get() ?? collect();
    @endphp

    <div class="flex h-screen overflow-hidden">

        <aside class="sidebar-wrap w-64 flex-shrink-0 flex flex-col h-full shadow-[2px_0_10px_rgba(0,0,0,0.02)]">
            <div class="h-20 flex items-center px-6 pt-2">
                <div class="flex items-center gap-3">
                    <div
                        class="h-8 w-8 rounded bg-[rgb(var(--brand))] flex items-center justify-center text-white font-bold shadow-md">
                        C
                    </div>
                    <div>
                        <h1 class="text-[15px] font-semibold tracking-wide">CLT SYSTEM</h1>
                        <p class="text-[10px] text-[rgb(var(--text-soft))] -mt-0.5">Management Platform</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ Route::is('dashboard') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                    </svg>
                    Dashboard
                </a>

                <div class="mt-6 mb-2 px-3">
                    <p class="text-[11px] font-semibold text-[rgb(var(--text-muted))] uppercase tracking-wider">Master
                        Data</p>
                </div>

                <a href="{{ route('suppliers.index') }}"
                    class="sidebar-link {{ Route::is('suppliers.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
                    </svg>
                    Suppliers
                </a>
                <a href="{{ route('layups.index') }}" class="sidebar-link {{ Route::is('layups.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z" />
                    </svg>
                    Layups
                </a>
                <a href="{{ route('layers.index') }}" class="sidebar-link {{ Route::is('layers.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M11.99 18.54l-7.37-5.73L3 14.07l9 7 9-7-1.63-1.27-7.38 5.74zM12 16l7.36-5.73L21 9l-9-7-9 7 1.63 1.27L12 16z" />
                    </svg>
                    Layers
                </a>
            </nav>

            <div class="p-4 mt-auto">
                <p class="text-[10px] text-[rgb(var(--text-muted))] text-center">&copy; 2026 CLT Management System</p>
            </div>
        </aside>

        <main class="flex-1 flex flex-col h-full overflow-hidden relative">

            <header
                class="h-16 px-8 flex items-center justify-between border-b border-[rgb(var(--line-color))] bg-[rgb(var(--app-bg))] z-40">
                @php
                    $breadcrumbParent = match (true) {
                        Route::is('suppliers.*'), Route::is('layups.*'), Route::is('layers.*') => 'Master',
                        Route::is('exports.*'), Route::is('imports.*'), Route::is('activity.*') => 'Tools',
                        default => null,
                    };
                @endphp
                <div class="flex items-center text-[12px] text-[rgb(var(--text-soft))] gap-2" id="breadcrumb">
                    @if($breadcrumbParent)
                        <span>{{ $breadcrumbParent }}</span>
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    @endif
                    <span class="text-[rgb(var(--text-main))] font-medium">{{ $title ?? 'Dashboard' }}</span>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-5">

                    <button onclick="toggleTheme()"
                        class="text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition"
                        id="themeToggleBtn">
                        <svg class="w-5 h-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg class="w-5 h-5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div class="relative">
                        <button onclick="toggleNotifications()"
                            class="text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition relative mt-1">
                            @if($recentActivity->count() > 0)
                                <span
                                    class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 border border-[rgb(var(--app-bg))] rounded-full text-[8px] flex items-center justify-center text-white font-bold">!</span>
                            @endif
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>
                        <!-- Dropdown Panel -->
                        <div id="notificationDropdown"
                            class="absolute right-0 mt-3 w-80 bg-[rgb(var(--card-bg))] border border-[rgb(var(--line-color))] rounded-lg shadow-xl shadow-black/10 hidden">
                            <div
                                class="p-3 border-b border-[rgb(var(--line-color))] flex justify-between items-center bg-[rgb(var(--sidebar-hover))]">
                                <h3 class="text-xs font-semibold uppercase tracking-wider">Recent Activity</h3>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                @forelse($recentActivity as $log)
                                    <div
                                        class="px-4 py-3 border-b border-[rgb(var(--line-color))] last:border-b-0 hover:bg-[rgba(16,185,129,0.03)] transition-colors">
                                        <p class="text-[12px] text-[rgb(var(--text-main))] font-medium">
                                            {{ ucwords($log->action) }}
                                            {{ ucwords(str_replace('_', ' ', $log->entity_type)) }}</p>
                                        <p class="text-[11px] text-[rgb(var(--text-soft))] mt-0.5">{{ $log->description }}
                                        </p>
                                        <p class="text-[10px] text-[rgb(var(--text-muted))] mt-1">
                                            {{ $log->created_at->diffForHumans() }}</p>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-xs text-[rgb(var(--text-soft))]">No recent
                                        notifications.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Profile -->
                    <div class="flex items-center gap-2">
                        <div
                            class="w-8 h-8 rounded-full bg-[rgb(var(--brand))] text-[11px] border-2 border-white/10 font-bold text-white flex items-center justify-center">
                            A</div>
                        <span class="text-[13px] font-medium hidden sm:block">Admin</span>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto w-full" onclick="closeNotificationsIfClickOutside(event)">
                <div class="p-8 pb-12 w-full mx-auto" id="pjax-container">
                    {{ $slot }}
                    @stack('scripts')
                </div>
            </div>
        </main>

    </div>

    <script>
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        function toggleNotifications() {
            document.getElementById('notificationDropdown').classList.toggle('hidden');
        }

        function closeNotificationsIfClickOutside(e) {
            if (!e.target.closest('#notificationDropdown') && !e.target.closest('button[onclick="toggleNotifications()"]')) {
                document.getElementById('notificationDropdown').classList.add('hidden');
            }
        }

        let _navController = null;

        document.addEventListener('click', e => {
            const link = e.target.closest('a.sidebar-link');
            if (!link || link.target === '_blank' || link.hasAttribute('download')) return;

            e.preventDefault();
            const url = link.href;
            if (url === window.location.href) return;

            // Abort any in-flight navigation
            if (_navController) _navController.abort();
            _navController = new AbortController();
            const signal = _navController.signal;

            document.querySelectorAll('a.sidebar-link').forEach(a => a.classList.remove('active'));
            link.classList.add('active');

            const main = document.getElementById('pjax-container');
            main.style.transition = 'opacity 0.2s ease-out';
            main.style.opacity = '0.3';

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal })
                .then(r => r.text())
                .then(html => {
                    if (signal.aborted) return;

                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    document.title = doc.title;

                    const targetMain = document.getElementById('pjax-container');
                    const sourceMain = doc.getElementById('pjax-container');
                    if (sourceMain && targetMain) targetMain.innerHTML = sourceMain.innerHTML;

                    const sourceBreadcrumb = doc.getElementById('breadcrumb');
                    const targetBreadcrumb = document.getElementById('breadcrumb');
                    if (sourceBreadcrumb && targetBreadcrumb) targetBreadcrumb.innerHTML = sourceBreadcrumb.innerHTML;

                    Array.from(targetMain.querySelectorAll('script')).forEach(oldScript => {
                        const newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));

                        const prefix = "(() => { const _origAEL = document.addEventListener; document.addEventListener = function(t, l, o) { if (t === 'DOMContentLoaded') { setTimeout(l, 1); } else { _origAEL.call(document, t, l, o); } }; try { \n";
                        const suffix = "\n } finally { document.addEventListener = _origAEL; } })();";
                        newScript.text = prefix + oldScript.text + suffix;

                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });

                    document.querySelectorAll('.fixed.inset-0.z-50').forEach(el => el.classList.add('hidden'));
                    history.pushState({}, '', url);
                    main.style.opacity = '1';
                    targetMain.parentElement.scrollTop = 0;
                    _navController = null;
                })
                .catch(err => { if (err.name !== 'AbortError') window.location = url; });
        });
        window.addEventListener('popstate', () => window.location.reload());
    </script>
</body>

</html>