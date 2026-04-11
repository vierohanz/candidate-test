@props(['route', 'icon' => 'home', 'badge' => null])

@php
    $active = request()->routeIs($route) || request()->routeIs($route . '.*');
    $label = trim(strip_tags($slot));
@endphp

<div class="relative group/navlink">
    <a href="{{ route($route) }}" class="sidebar-link {{ $active ? 'active' : '' }}"
        x-bind:title="$store.ui.sidebarCollapsed ? '{{ $label }}' : ''">

        <span class="sidebar-link-icon {{ $active ? 'opacity-100' : 'opacity-70 group-hover/navlink:opacity-100' }}">
            @if($icon === 'home')
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
            @elseif($icon === 'building')
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
            @elseif($icon === 'layers')
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                </svg>
            @elseif($icon === 'stack')
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5-3.9 19.5m-2.1-19.5-3.9 19.5" />
                </svg>
            @elseif($icon === 'upload')
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                </svg>
            @elseif($icon === 'download')
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
            @elseif($icon === 'log')
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            @endif
        </span>

        <span x-cloak x-show="!$store.ui.sidebarCollapsed" x-transition:enter="transition-all duration-300 delay-100"
            x-transition:enter-start="opacity-0 -translate-x-1" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition-all duration-100" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="whitespace-nowrap overflow-hidden flex-1">
            {{ $slot }}
        </span>

        @if($badge)
            <span x-cloak x-show="!$store.ui.sidebarCollapsed" class="sidebar-link-badge shadow-sm">
                {{ $badge }}
            </span>
        @endif

        @if($active)
            <div x-cloak x-show="$store.ui.sidebarCollapsed"
                class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-5 rounded-l-full bg-white shadow-[0_0_10px_rgba(255,255,255,0.5)]">
            </div>
        @endif
    </a>

    <div x-cloak x-show="$store.ui.sidebarCollapsed" class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-4 z-50
                 px-2 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-lg
                 bg-slate-900 text-white shadow-2xl whitespace-nowrap
                 opacity-0 group-hover/navlink:opacity-100
                 transition-all duration-200 translate-x-1 group-hover/navlink:translate-x-0 hidden lg:block">
        {{ $label }}
        <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-900"></div>
    </div>
</div>