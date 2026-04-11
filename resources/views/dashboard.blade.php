<x-app-layout title="Dashboard">
    <div>
        <h2 class="text-2xl font-semibold text-[rgb(var(--text-main))] mb-6">Dashboard Summary</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2">

            <div
                class="bg-[rgb(var(--card-bg))] border border-[rgb(var(--line-color))] rounded-xl p-6 relative overflow-hidden group transition-colors">
                <div class="absolute right-0 top-0 opacity-5 p-4 scale-150 transform transition group-hover:scale-125">
                    <svg class="w-24 h-24 text-[rgb(var(--brand))]" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
                    </svg>
                </div>
                <h3 class="text-[13px] font-semibold text-[rgb(var(--text-soft))] tracking-wide uppercase">Suppliers
                </h3>
                <div class="mt-4 flex items-baseline gap-3">
                    <p class="text-4xl font-bold text-[rgb(var(--text-main))]">
                        {{ number_format($stats['suppliers'] ?? 0) }}</p>
                    <span
                        class="text-xs font-semibold text-[rgb(var(--brand))] bg-[rgb(var(--brand))/10] px-2 py-0.5 rounded-sm">Registered</span>
                </div>
            </div>

            <div
                class="bg-[rgb(var(--card-bg))] border border-[rgb(var(--line-color))] rounded-xl p-6 relative overflow-hidden group transition-colors">
                <div class="absolute right-0 top-0 opacity-5 p-4 scale-150 transform transition group-hover:scale-125">
                    <svg class="w-24 h-24 text-[rgb(var(--brand))]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z" />
                    </svg>
                </div>
                <h3 class="text-[13px] font-semibold text-[rgb(var(--text-soft))] tracking-wide uppercase">Layups</h3>
                <div class="mt-4 flex items-baseline gap-3">
                    <p class="text-4xl font-bold text-[rgb(var(--text-main))]">
                        {{ number_format($stats['layups'] ?? 0) }}</p>
                    <span
                        class="text-xs font-semibold text-[rgb(var(--brand))] bg-[rgb(var(--brand))/10] px-2 py-0.5 rounded-sm">Active
                        setups</span>
                </div>
            </div>

            <div
                class="bg-[rgb(var(--card-bg))] border border-[rgb(var(--line-color))] rounded-xl p-6 relative overflow-hidden group transition-colors">
                <div class="absolute right-0 top-0 opacity-5 p-4 scale-150 transform transition group-hover:scale-125">
                    <svg class="w-24 h-24 text-[rgb(var(--brand))]" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M11.99 18.54l-7.37-5.73L3 14.07l9 7 9-7-1.63-1.27-7.38 5.74zM12 16l7.36-5.73L21 9l-9-7-9 7 1.63 1.27L12 16z" />
                    </svg>
                </div>
                <h3 class="text-[13px] font-semibold text-[rgb(var(--text-soft))] tracking-wide uppercase">Technical
                    Layers</h3>
                <div class="mt-4 flex items-baseline gap-3">
                    <p class="text-4xl font-bold text-[rgb(var(--text-main))]">
                        {{ number_format($stats['layers'] ?? 0) }}</p>
                    <span
                        class="text-xs font-semibold text-[rgb(var(--text-muted))] bg-[rgb(var(--text-main))/5] px-2 py-0.5 rounded-sm">Data
                        points</span>
                </div>
            </div>
        </div>

        <div
            class="mt-8 border border-[rgb(var(--line-color))] rounded-xl bg-[rgb(var(--card-bg))] overflow-hidden transition-colors">
            <div class="px-6 py-4 border-b border-[rgb(var(--line-color))]">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-[rgb(var(--text-soft))]">System Activity
                    Log</h3>
            </div>
            <table class="w-full text-left border-collapse ref-table">
                <thead>
                    <tr>
                        <th class="w-48 pl-6">Timestamp</th>
                        <th class="w-32">Module</th>
                        <th class="w-32 text-center">Action</th>
                        <th class="pr-6">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivity->take(5) as $log)
                        <tr class="hover:bg-[rgb(var(--brand))/5] transition-colors">
                            <td class="pl-6 text-[12px] font-mono text-[rgb(var(--text-muted))]">
                                {{ optional($log->created_at)->format('Y-m-d H:i:s') }}</td>
                            <td class="text-[rgb(var(--text-main))] text-[13px]">{{ $log->entity_type }}</td>
                            <td class="text-center">
                                @if(str_contains(strtolower($log->action), 'created'))
                                    <span
                                        class="text-[10px] font-bold px-2 py-1 rounded bg-[rgb(var(--brand))/10] text-[rgb(var(--brand))] border border-[rgb(var(--brand))/20]">CREATE</span>
                                @elseif(str_contains(strtolower($log->action), 'update'))
                                    <span
                                        class="text-[10px] font-bold px-2 py-1 rounded bg-yellow-500/10 text-yellow-500 border border-yellow-500/20">UPDATE</span>
                                @else
                                    <span
                                        class="text-[10px] font-bold px-2 py-1 rounded bg-[rgb(var(--text-main))/10] text-[rgb(var(--text-main))] border border-[rgb(var(--text-main))/20]">{{ strtoupper($log->action) }}</span>
                                @endif
                            </td>
                            <td class="pr-6 text-[rgb(var(--text-soft))] text-[13px]">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center text-[rgb(var(--text-soft))] text-sm">
                                <svg class="w-8 h-8 opacity-20 mx-auto mb-3" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="block font-medium">Data Not Found</span>
                                <span class="block text-xs mt-1">No recent activity detected on the system.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>