<x-app-layout title="Dashboard">
    <div class="grid gap-5 xl:grid-cols-[1.3fr,0.95fr,0.85fr]">
        <section class="grid gap-5 xl:col-span-2">
            <div class="grid gap-5 lg:grid-cols-[1.15fr,0.85fr]">
                <div class="relative overflow-hidden rounded-[8px] border border-emerald-200/60 bg-gradient-to-br from-emerald-500 via-teal-500 to-sky-500 p-6 text-white shadow-[0_30px_60px_-24px_rgba(16,185,129,0.45)] dark:border-emerald-400/10 dark:shadow-[0_32px_65px_-24px_rgba(16,185,129,0.38)]">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.26),transparent_26%),radial-gradient(circle_at_bottom_left,rgba(255,255,255,0.18),transparent_34%)]"></div>
                    <div class="relative flex h-full flex-col justify-between gap-8">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/75">Workspace Overview</p>
                                <h2 class="mt-3 font-display text-4xl font-bold leading-none">{{ number_format($stats['suppliers']) }}</h2>
                                <p class="mt-2 text-sm text-white/82">Total suppliers connected to your CLT workspace.</p>
                            </div>
                            <button class="grid h-10 w-10 place-items-center rounded-full bg-white/18 text-white/90 backdrop-blur">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75h.008v.008H12V6.75Zm0 5.25h.008v.008H12V12Zm0 5.25h.008v.008H12v-.008Z"/>
                                </svg>
                            </button>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <p class="text-[11px] uppercase tracking-[0.2em] text-white/70">Pending</p>
                                <p class="mt-2 text-2xl font-semibold">20k</p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-[0.2em] text-white/70">Safe</p>
                                <p class="mt-2 text-2xl font-semibold">{{ number_format($stats['layups']) }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-[0.2em] text-white/70">Review</p>
                                <p class="mt-2 text-2xl font-semibold">{{ number_format($stats['activities']) }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-[11px] uppercase tracking-[0.18em] text-white/72">
                                <span>Import pulse</span>
                                <span>live</span>
                            </div>
                            <div class="relative h-16 overflow-hidden rounded-[8px] bg-white/12 px-3 py-2 backdrop-blur">
                                <div class="absolute inset-x-0 top-1/2 h-px -translate-y-1/2 bg-white/18"></div>
                                <svg class="h-full w-full" viewBox="0 0 320 80" preserveAspectRatio="none">
                                    <path d="M0 54 C28 38, 54 62, 80 46 S132 28, 158 44 S212 66, 240 42 S292 26, 320 36" fill="none" stroke="rgba(255,255,255,0.48)" stroke-width="2"></path>
                                    <path d="M0 46 C26 62, 58 28, 84 42 S136 58, 162 38 S214 22, 244 50 S292 70, 320 48" fill="none" stroke="rgba(255,214,10,0.78)" stroke-width="2.4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clt-card p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-[rgb(var(--text-main))]">Pending Messages</p>
                            <p class="mt-2 font-display text-4xl font-bold text-[rgb(var(--text-main))]">20.k</p>
                        </div>
                        <button class="grid h-9 w-9 place-items-center rounded-full bg-[rgba(var(--line-color),0.04)] text-[rgb(var(--text-soft))]">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75h.008v.008H12V6.75Zm0 5.25h.008v.008H12V12Zm0 5.25h.008v.008H12v-.008Z"/>
                            </svg>
                        </button>
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-4">
                        <div class="relative h-32 w-32">
                            <svg class="h-full w-full -rotate-90" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="38" fill="none" stroke="rgba(148,163,184,0.18)" stroke-width="10"></circle>
                                <circle cx="60" cy="60" r="38" fill="none" stroke="rgb(16 185 129)" stroke-width="10" stroke-linecap="round" stroke-dasharray="142 239"></circle>
                                <circle cx="60" cy="60" r="27" fill="none" stroke="rgb(14 165 233)" stroke-width="8" stroke-linecap="round" stroke-dasharray="88 169" stroke-dashoffset="-26"></circle>
                            </svg>
                            <div class="absolute inset-0 grid place-items-center">
                                <div class="grid h-12 w-12 place-items-center rounded-full bg-emerald-100 text-emerald-500 dark:bg-emerald-500/12 dark:text-emerald-300">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m15 15-6 6m0-6 6 6M8.25 3.75h7.5A2.25 2.25 0 0 1 18 6v4.5A2.25 2.25 0 0 1 15.75 12.75h-7.5A2.25 2.25 0 0 1 6 10.5V6a2.25 2.25 0 0 1 2.25-2.25Z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span>
                                <span class="text-[rgb(var(--text-soft))]">Conflict review</span>
                                <span class="ml-auto font-semibold text-[rgb(var(--text-main))]">360</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                                <span class="text-[rgb(var(--text-soft))]">Manual queue</span>
                                <span class="ml-auto font-semibold text-[rgb(var(--text-main))]">260</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
                                <span class="text-[rgb(var(--text-soft))]">Resolved</span>
                                <span class="ml-auto font-semibold text-[rgb(var(--text-main))]">820</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clt-card overflow-hidden p-0">
                <div class="flex items-center justify-between border-b border-[rgba(var(--line-color),0.08)] px-5 py-4">
                    <div class="flex items-center gap-5 text-sm">
                        <span class="font-semibold text-[rgb(var(--text-main))]">Post Activity</span>
                        <span class="text-[rgb(var(--text-soft))]">User</span>
                    </div>
                    <button class="grid h-9 w-9 place-items-center rounded-full bg-[rgba(var(--line-color),0.04)] text-[rgb(var(--text-soft))]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75h.008v.008H12V6.75Zm0 5.25h.008v.008H12V12Zm0 5.25h.008v.008H12v-.008Z"/>
                        </svg>
                    </button>
                </div>

                <div class="divide-y divide-[rgba(var(--line-color),0.06)]">
                    @php
                        $activityItems = $recentActivity->take(5);
                    @endphp

                    @forelse($activityItems as $index => $log)
                        <div class="grid grid-cols-[auto,1fr,auto,auto] items-center gap-4 px-5 py-4">
                            <div class="grid h-10 w-10 place-items-center rounded-[8px] bg-emerald-100 text-emerald-500 dark:bg-emerald-500/12 dark:text-emerald-300">
                                <span class="text-xs font-bold">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-[rgb(var(--text-main))]">{{ $log->description }}</p>
                                <p class="mt-1 text-xs text-[rgb(var(--text-soft))]">{{ class_basename($log->subject_type ?? 'Activity') }}</p>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-[rgb(var(--text-soft))]">
                                <span class="grid h-5 w-5 place-items-center rounded-full bg-slate-100 text-slate-700 dark:bg-white/8 dark:text-white/75">@</span>
                                <span>{{ $log->created_at->format('H:i') }}</span>
                            </div>
                            <div class="rounded-full border border-emerald-500/18 bg-emerald-500/10 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 dark:text-emerald-300">
                                + {{ ($index + 1) }}%
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center">
                            <p class="text-sm font-medium text-[rgb(var(--text-main))]">No activity yet.</p>
                            <p class="mt-1 text-sm text-[rgb(var(--text-soft))]">Supplier updates will appear here once the workspace starts moving.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <aside class="grid content-start gap-5">
            <div class="clt-card p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="grid h-14 w-14 place-items-center rounded-[8px] bg-rose-100 text-rose-500 dark:bg-rose-500/12 dark:text-rose-300">
                        <div class="text-center">
                            <p class="text-sm font-bold">3.k</p>
                            <p class="text-[10px] uppercase tracking-[0.16em]">now</p>
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-[rgb(var(--text-main))]">Comments</p>
                        <div class="mt-3 h-2.5 rounded-full bg-[rgba(var(--line-color),0.06)]">
                            <div class="h-2.5 rounded-full bg-gradient-to-r from-emerald-500 to-teal-400" style="width: 64%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clt-card p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-[rgb(var(--text-main))]">Post Stats</p>
                    </div>
                    <button class="grid h-8 w-8 place-items-center rounded-full bg-[rgba(var(--line-color),0.04)] text-[rgb(var(--text-soft))]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75h.008v.008H12V6.75Zm0 5.25h.008v.008H12V12Zm0 5.25h.008v.008H12v-.008Z"/>
                        </svg>
                    </button>
                </div>

                <div class="mt-5 flex h-36 items-end justify-between gap-2">
                    @php($bars = [42, 64, 28, 78, 36, 54, 44])
                    @foreach($bars as $bar)
                        <div class="flex h-full flex-1 flex-col justify-end gap-2">
                            <div class="w-full rounded-full bg-slate-200/70 dark:bg-white/6" style="height: {{ 100 - $bar }}%"></div>
                            <div class="w-full rounded-full {{ $loop->iteration === 4 ? 'bg-gradient-to-b from-emerald-400 to-teal-500' : 'bg-slate-300/80 dark:bg-white/12' }}" style="height: {{ $bar }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="clt-card p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="grid h-14 w-14 place-items-center rounded-[8px] bg-rose-100 text-rose-500 dark:bg-rose-500/12 dark:text-rose-300">
                        <div class="text-center">
                            <p class="text-sm font-bold">10.k</p>
                            <p class="text-[10px] uppercase tracking-[0.16em]">share</p>
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-[rgb(var(--text-main))]">Links Shared</p>
                        <div class="mt-3 h-2.5 rounded-full bg-[rgba(var(--line-color),0.06)]">
                            <div class="h-2.5 rounded-full bg-[linear-gradient(90deg,#111827_0%,#fb7185_100%)]" style="width: 82%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clt-card p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-[rgb(var(--text-main))]">Quick Radar</p>
                        <p class="mt-1 text-xs text-[rgb(var(--text-soft))]">CLT structure health</p>
                    </div>
                    <span class="rounded-full border border-rose-500/18 bg-rose-500/10 px-2.5 py-1 text-[11px] font-semibold text-rose-700 dark:text-rose-300">Active</span>
                </div>

                <div class="mt-5 grid gap-3">
                    <div>
                        <div class="mb-2 flex items-center justify-between text-xs text-[rgb(var(--text-soft))]">
                            <span>Supplier coverage</span>
                            <span>74%</span>
                        </div>
                        <div class="h-2 rounded-full bg-[rgba(var(--line-color),0.06)]">
                            <div class="h-2 rounded-full bg-gradient-to-r from-emerald-500 to-teal-400" style="width: 74%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="mb-2 flex items-center justify-between text-xs text-[rgb(var(--text-soft))]">
                            <span>Layup readiness</span>
                            <span>58%</span>
                        </div>
                        <div class="h-2 rounded-full bg-[rgba(var(--line-color),0.06)]">
                            <div class="h-2 rounded-full bg-gradient-to-r from-teal-500 to-emerald-400" style="width: 58%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="mb-2 flex items-center justify-between text-xs text-[rgb(var(--text-soft))]">
                            <span>Layer completion</span>
                            <span>86%</span>
                        </div>
                        <div class="h-2 rounded-full bg-[rgba(var(--line-color),0.06)]">
                            <div class="h-2 rounded-full bg-[linear-gradient(90deg,#111827_0%,#ef4444_100%)]" style="width: 86%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</x-app-layout>
