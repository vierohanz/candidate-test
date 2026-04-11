<x-app-layout title="Dashboard">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        function animateValue(id, start, end, duration) {
            if (start === end) return;
            const obj = document.getElementById(id);
            if (!obj) return;
            const range = end - start;
            const minTimer = 50;
            let stepTime = Math.abs(Math.floor(duration / range));
            stepTime = Math.max(stepTime, minTimer);
            const startTime = new Date().getTime();
            const endTime = startTime + duration;
            let timer;
            function run() {
                const now = new Date().getTime();
                const remaining = Math.max((endTime - now) / duration, 0);
                const value = Math.round(end - (remaining * range));
                obj.innerText = value.toLocaleString();
                if (value == end) clearInterval(timer);
            }
            timer = setInterval(run, stepTime);
            run();
        }
    </script>
    <div>
        <h2 class="text-2xl font-semibold text-[rgb(var(--text-main))] mb-6">Dashboard Summary</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2">

            <div
                class="bg-[rgb(var(--card-bg))] border border-[rgb(var(--line-color))] rounded-xl p-6 relative overflow-hidden group transition-colors">
                <div class="absolute right-0 top-0 opacity-5 p-4 scale-150 transform transition group-hover:scale-125">
                </div>
                <h3 class="text-[13px] font-semibold text-[rgb(var(--text-soft))] tracking-wide uppercase">Suppliers
                </h3>
                <div class="mt-4 flex items-baseline gap-3">
                    <p id="stat-suppliers" class="text-4xl font-bold text-[rgb(var(--text-main))]"
                        data-value="{{ $stats['suppliers'] ?? 0 }}">0</p>
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
                    <p id="stat-layups" class="text-4xl font-bold text-[rgb(var(--text-main))]"
                        data-value="{{ $stats['layups'] ?? 0 }}">0</p>
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
                    <p id="stat-layers" class="text-4xl font-bold text-[rgb(var(--text-main))]"
                        data-value="{{ $stats['layers'] ?? 0 }}">0</p>
                    <span
                        class="text-xs font-semibold text-[rgb(var(--text-muted))] bg-[rgb(var(--text-main))/5] px-2 py-0.5 rounded-sm">Data
                        points</span>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <div
                class="bg-[rgb(var(--card-bg))] border border-[rgb(var(--line-color))] rounded-xl p-6 transition-colors">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xs font-bold text-[rgb(var(--text-soft))] uppercase tracking-widest">Supplier
                            Partnership</h3>
                        <p class="text-[10px] text-[rgb(var(--text-muted))] mt-1">Layup distribution across top partners
                        </p>
                    </div>
                    <div class="p-2 bg-[rgb(var(--brand))/10] rounded-lg">
                        <svg class="w-4 h-4 text-[rgb(var(--brand))]" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                    </div>
                </div>
                <div id="supplierChart" class="min-h-[250px]"></div>
            </div>

            <div
                class="bg-[rgb(var(--card-bg))] border border-[rgb(var(--line-color))] rounded-xl p-6 transition-colors">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xs font-bold text-[rgb(var(--text-soft))] uppercase tracking-widest">Layer
                            Complexity</h3>
                        <p class="text-[10px] text-[rgb(var(--text-muted))] mt-1">Number of layers in top layup sets</p>
                    </div>
                    <div class="p-2 bg-blue-500/10 rounded-lg">
                        <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div id="layerChart" class="min-h-[250px]"></div>
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
                        <th class="w-48 pl-10">Timestamp</th>
                        <th class="w-32">Module</th>
                        <th class="w-32 text-center">Action</th>
                        <th class="pr-10">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivity->take(5) as $log)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td
                                class="pl-10 text-[11px] font-mono text-[rgb(var(--text-muted))] group-hover:text-[rgb(var(--text-main))] transition-colors">
                                {{ optional($log->created_at)->format('Y-m-d H:i:s') }}
                            </td>
                            <td
                                class="text-[rgb(var(--text-soft))] text-[13px] font-semibold group-hover:text-[rgb(var(--text-main))]">
                                {{ $log->entity_type }}</td>
                            <td class="text-center">
                                @if(str_contains(strtolower($log->action), 'created'))
                                    <span
                                        class="text-[10px] font-black px-3 py-1.5 rounded-full bg-[rgb(var(--brand))/8%] text-[rgb(var(--brand))] border border-[rgb(var(--brand))/20] tracking-wider">CREATE</span>
                                @elseif(str_contains(strtolower($log->action), 'update'))
                                    <span
                                        class="text-[10px] font-black px-3 py-1.5 rounded-full bg-amber-500/8% text-amber-500 border border-amber-500/20 tracking-wider">UPDATE</span>
                                @elseif(str_contains(strtolower($log->action), 'delete'))
                                    <span
                                        class="text-[10px] font-black px-3 py-1.5 rounded-full bg-red-500/8% text-red-500 border border-red-500/20 tracking-wider uppercase">DELETED</span>
                                @else
                                    <span
                                        class="text-[10px] font-black px-3 py-1.5 rounded-full bg-[rgb(var(--text-main))/8%] text-[rgb(var(--text-main))] border border-[rgb(var(--text-main))/20] tracking-wider uppercase">{{ $log->action }}</span>
                                @endif
                            </td>
                            <td
                                class="pr-6 text-[rgb(var(--text-soft))] text-[12px] leading-relaxed group-hover:text-[rgb(var(--text-main))] transition-colors">
                                {{ $log->description }}</td>
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
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Counter animations
                ['stat-suppliers', 'stat-layups', 'stat-layers'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) animateValue(id, 0, parseInt(el.dataset.value), 1500);
                });

                const chartColors = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4'];

                // Supplier Chart
                const supLabels = {!! json_encode($topSuppliers->pluck('name')) !!};
                const supData = {!! json_encode($topSuppliers->pluck('layups_count')) !!};

                new ApexCharts(document.querySelector("#supplierChart"), {
                    series: supData,
                    chart: { type: 'donut', height: 280, animations: { enabled: true, easing: 'easeinout' }, foreColor: '#aaa' },
                    labels: supLabels,
                    colors: chartColors,
                    stroke: { show: true, width: 2, colors: ['rgb(var(--card-bg))'] },
                    fill: { type: 'gradient', gradient: { shade: 'dark', type: "vertical", opacityFrom: 1, opacityTo: 0.8 } },
                    legend: { position: 'bottom', labels: { colors: '#aaa' }, markers: { radius: 12 } },
                    dataLabels: { enabled: false },
                    plotOptions: { pie: { donut: { size: '75%', background: 'transparent' } } },
                    tooltip: { theme: 'dark', y: { formatter: (v) => `${v} Layups` } },
                    states: { hover: { filter: { type: 'lighten', value: 0.15 } }, active: { filter: { type: 'none' } } }
                }).render();

                // Layer Chart
                const layLabels = {!! json_encode($distribution->pluck('name')) !!};
                const layData = {!! json_encode($distribution->pluck('layers_count')) !!};

                new ApexCharts(document.querySelector("#layerChart"), {
                    series: [{ name: 'Layers', data: layData }],
                    chart: { type: 'bar', height: 280, toolbar: { show: false }, foreColor: '#aaa' },
                    colors: ['#3B82F6'],
                    plotOptions: { bar: { borderRadius: 8, columnWidth: '40%', distributed: false } },
                    fill: { type: 'gradient', gradient: { shade: 'dark', type: "vertical", opacityFrom: 0.95, opacityTo: 0.7, stops: [0, 90, 100] } },
                    dataLabels: { enabled: false },
                    xaxis: { categories: layLabels, labels: { style: { colors: '#aaa', fontSize: '10px' } }, axisBorder: { show: false } },
                    yaxis: { labels: { style: { colors: '#aaa' } } },
                    tooltip: { theme: 'dark' },
                    states: { hover: { filter: { type: 'lighten', value: 0.2 } }, active: { filter: { type: 'none' } }, inactive: { opacity: 0.6 } },
                    grid: { borderColor: 'rgba(255,255,255,0.05)', strokeDashArray: 4 }
                }).render();
            });
        </script>
    @endpush
</x-app-layout>