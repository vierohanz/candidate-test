<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — CLT Manager</title>

    <style>
        html {
            background: #141414;
            color-scheme: dark;
        }
    </style>

    <script>
        document.documentElement.classList.add('dark');
        document.documentElement.style.backgroundColor = '#141414';
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-[rgb(var(--text-main))] bg-[rgb(var(--app-bg))] selection:bg-emerald-500/30">

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

                <a href="{{ route('suppliers.page') }}"
                    class="sidebar-link {{ Route::is('suppliers.page') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
                    </svg>
                    Suppliers
                </a>
                <a href="{{ route('layups.page') }}" class="sidebar-link {{ Route::is('layups.page') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z" />
                    </svg>
                    Layups
                </a>
                <a href="{{ route('layers.page') }}" class="sidebar-link {{ Route::is('layers.page') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M11.99 18.54l-7.37-5.73L3 14.07l9 7 9-7-1.63-1.27-7.38 5.74zM12 16l7.36-5.73L21 9l-9-7-9 7 1.63 1.27L12 16z" />
                    </svg>
                    Layers
                </a>
                <a href="{{ route('imports.index') }}" class="sidebar-link {{ Route::is('imports.index') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 16l4-4h-3V4h-2v8H8l4 4zm-7 2h14v2H5z" />
                    </svg>
                    Import
                </a>
                <a href="{{ route('exports.index') }}" class="sidebar-link {{ Route::is('exports.index') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8 8 12h3v8h2v-8h3l-4-4zM5 4h14v2H5z" />
                    </svg>
                    Export
                </a>
                <a href="{{ route('activity.index') }}" class="sidebar-link {{ Route::is('activity.index') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 3h9l5 5v13a1 1 0 0 1-1 1H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm8 1.5V9h4.5L13 4.5zM7 12h10v1.5H7V12zm0 4h10v1.5H7V16z" />
                    </svg>
                    Activity Logs
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
                        Route::is('suppliers.page'), Route::is('layups.page'), Route::is('layers.page') => 'Master',
                        Route::is('exports.index'), Route::is('imports.index'), Route::is('activity.index') => 'Tools',
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

                    <!-- Notifications Dropdown -->
                    <div class="relative">
                        <button onclick="toggleNotifications()"
                            class="text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition relative mt-1">
                            <span id="notificationBadge"
                                class="hidden absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 border border-[rgb(var(--app-bg))] rounded-full text-[8px] items-center justify-center text-white font-bold">!</span>
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
                            <div id="notificationDropdownBody" class="max-h-64 overflow-y-auto">
                                <div class="p-4 text-center text-xs text-[rgb(var(--text-soft))]">Loading notifications...</div>
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
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            if (!dropdown) return;

            const willOpen = dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden');

            if (willOpen) {
                refreshNotifications();
            }
        }

        function closeNotificationsIfClickOutside(e) {
            if (!e.target.closest('#notificationDropdown') && !e.target.closest('button[onclick="toggleNotifications()"]')) {
                document.getElementById('notificationDropdown').classList.add('hidden');
            }
        }

        async function refreshNotifications() {
            const badge = document.getElementById('notificationBadge');
            const body = document.getElementById('notificationDropdownBody');
            if (!badge || !body) return;

            try {
                const response = await fetch('/api/v1/activity-logs?per_page=5', {
                    headers: { 'Accept': 'application/json' }
                });
                const result = await response.json();
                if (!response.ok || !result.success) return;

                const items = Array.isArray(result.data) ? result.data : [];
                if (items.length > 0) {
                    badge.classList.remove('hidden');
                    badge.classList.add('flex');
                } else {
                    badge.classList.add('hidden');
                    badge.classList.remove('flex');
                }

                body.innerHTML = items.length
                    ? items.map((log) => `
                        <div class="px-4 py-3 border-b border-[rgb(var(--line-color))] last:border-b-0 hover:bg-[rgba(16,185,129,0.03)] transition-colors">
                            <p class="text-[12px] text-[rgb(var(--text-main))] font-medium">${(log.action || '').toString().replace(/^./, c => c.toUpperCase())} ${(log.entity_type || '').toString()}</p>
                            <p class="text-[11px] text-[rgb(var(--text-soft))] mt-0.5">${log.description || ''}</p>
                            <p class="text-[10px] text-[rgb(var(--text-muted))] mt-1">${log.created_at ? new Date(log.created_at).toLocaleString() : ''}</p>
                        </div>
                    `).join('')
                    : `<div class="p-4 text-center text-xs text-[rgb(var(--text-soft))]">No recent notifications.</div>`;
            } catch (error) {
                console.warn('Notification refresh failed:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            refreshNotifications();
        });

        let _navController = null;
        let _navRequestId = 0;
        const _pageCache = new Map();
        const _pageCacheTtl = 30000;

        function setPageLoading(isLoading) {
            document.body.classList.toggle('page-shell-loading', isLoading);
        }

        function getCachedPage(url) {
            const cached = _pageCache.get(url);
            if (!cached) return null;
            if ((Date.now() - cached.ts) > _pageCacheTtl) {
                _pageCache.delete(url);
                return null;
            }
            return cached.html;
        }

        function cachePage(url, html) {
            _pageCache.set(url, { html, ts: Date.now() });
        }

        function patchPageScripts(container) {
            Array.from(container.querySelectorAll('script')).forEach((oldScript) => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach((attr) => newScript.setAttribute(attr.name, attr.value));

                if (oldScript.src) {
                    newScript.src = oldScript.src;
                } else {
                    const prefix = "(() => { const _origAEL = document.addEventListener; document.addEventListener = function(t, l, o) { if (t === 'DOMContentLoaded') { setTimeout(() => { try { l.call(document, new Event('DOMContentLoaded')); } catch (_) {} }, 1); } else { return _origAEL.call(document, t, l, o); } }; try {\n";
                    const suffix = "\n} finally { document.addEventListener = _origAEL; } })();";
                    newScript.text = prefix + oldScript.text + suffix;
                }

                oldScript.parentNode.replaceChild(newScript, oldScript);
            });
        }

        function applyPageHtml(url, html, options = {}) {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const targetMain = document.getElementById('pjax-container');
            const sourceMain = doc.getElementById('pjax-container');
            const sourceBreadcrumb = doc.getElementById('breadcrumb');
            const targetBreadcrumb = document.getElementById('breadcrumb');

            if (!targetMain || !sourceMain) {
                window.location.href = url;
                return false;
            }

            document.title = doc.title;
            targetMain.innerHTML = sourceMain.innerHTML;
            if (sourceBreadcrumb && targetBreadcrumb) targetBreadcrumb.innerHTML = sourceBreadcrumb.innerHTML;

            patchPageScripts(targetMain);

            document.querySelectorAll('.fixed.inset-0.z-50, .fixed.inset-0.z-\\[100\\], .fixed.inset-0.z-\\[120\\]').forEach((el) => el.classList.add('hidden'));
            document.getElementById('notificationDropdown')?.classList.add('hidden');
            targetMain.parentElement.scrollTop = 0;

            if (options.pushState !== false) {
                history.pushState({}, '', url);
            }

            return true;
        }

        async function navigateTo(url, options = {}) {
            if (!url || url === window.location.href) return;

            const requestId = ++_navRequestId;
            if (_navController) _navController.abort();
            _navController = new AbortController();

            const cachedHtml = getCachedPage(url);
            if (cachedHtml) {
                applyPageHtml(url, cachedHtml, options);
                setPageLoading(false);
                refreshNotifications();
                return;
            }

            setPageLoading(true);

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: _navController.signal,
                });

                if (!response.ok) throw new Error(`Navigation failed: ${response.status}`);
                const html = await response.text();
                if (requestId !== _navRequestId) return;

                cachePage(url, html);
                const applied = applyPageHtml(url, html, options);
                if (applied) refreshNotifications();
            } catch (error) {
                if (error.name !== 'AbortError') {
                    window.location.href = url;
                }
            } finally {
                if (requestId === _navRequestId) {
                    setPageLoading(false);
                    _navController = null;
                }
            }
        }

        document.addEventListener('click', (e) => {
            const link = e.target.closest('a.sidebar-link');
            if (!link || link.target === '_blank' || link.hasAttribute('download')) return;

            const href = link.getAttribute('href');
            if (!href) return;

            const url = new URL(href, window.location.origin);
            if (url.origin !== window.location.origin) return;

            e.preventDefault();

            document.querySelectorAll('a.sidebar-link').forEach((item) => item.classList.remove('active'));
            link.classList.add('active');

            navigateTo(url.toString());
        });

        window.addEventListener('popstate', () => {
            navigateTo(window.location.href, { pushState: false });
        });

        let _toastTimer = null;
        function showToast(message, isSuccess = true) {
            const toast = document.getElementById('global-toast');
            const toastMsg = document.getElementById('global-toast-msg');
            const toastIcon = document.getElementById('global-toast-icon');
            if (!toast || !toastMsg) return;

            toastMsg.innerText = message || (isSuccess ? 'Success' : 'Error');
            
            if (isSuccess) {
                toast.classList.remove('bg-red-500/90', 'border-red-500/20');
                toast.classList.add('bg-[rgb(var(--brand))]/90', 'border-emerald-500/20');
                if (toastIcon) toastIcon.innerHTML = `<svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>`;
            } else {
                toast.classList.remove('bg-[rgb(var(--brand))]/90', 'border-emerald-500/20');
                toast.classList.add('bg-red-500/90', 'border-red-500/20');
                if (toastIcon) toastIcon.innerHTML = `<svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>`;
            }

            toast.classList.remove('hidden');
            toast.classList.add('flex');
            setTimeout(() => {
                toast.classList.replace('opacity-0', 'opacity-100');
                toast.classList.replace('-translate-y-4', 'translate-y-0');
            }, 10);

            if (_toastTimer) clearTimeout(_toastTimer);
            _toastTimer = setTimeout(() => {
                toast.classList.replace('opacity-100', 'opacity-0');
                toast.classList.replace('translate-y-0', '-translate-y-4');
                setTimeout(() => {
                    toast.classList.add('hidden');
                    toast.classList.remove('flex');
                }, 300);
            }, 4000);
        }
        window.showToast = showToast;
    </script>
    
    <div id="global-toast"
        class="fixed top-8 left-1/2 -translate-x-1/2 z-[100] bg-[rgb(var(--brand))]/90 backdrop-blur-md border border-white/10 text-white px-6 py-4 rounded-2xl shadow-xl text-sm font-bold opacity-0 -translate-y-4 transition-all duration-300 pointer-events-none items-center gap-3 hidden min-w-[300px] justify-center text-center">
        <div id="global-toast-icon" class="flex-shrink-0 w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <span id="global-toast-msg">Success</span>
        </div>
    </div>
</body>

</html>
