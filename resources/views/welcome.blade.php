<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SPECtoolbox start page for CLT supplier, layup, and layer management.">
    <title>SPECtoolbox Start</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --brand: 16, 185, 129;
            --brand-alt: 20, 184, 166;
            --accent-teal: 45, 212, 191;
        }
    </style>
</head>

<body class="min-h-screen overflow-hidden bg-[#091015] text-white antialiased">
    <main class="relative min-h-screen overflow-hidden">
        <img src="https://images.pexels.com/photos/30990853/pexels-photo-30990853.jpeg?cs=srgb&dl=pexels-jhonny-mages-2149929716-30990853.jpg&fm=jpg"
            alt="CLT production floor"
            class="absolute inset-0 h-full w-full scale-[1.04] object-cover object-center brightness-[0.42] contrast-[1.06] saturate-[0.82]">
        <div
            class="absolute inset-0 bg-[linear-gradient(90deg,rgba(4,12,16,0.92)_0%,rgba(4,12,16,0.78)_28%,rgba(4,12,16,0.44)_52%,rgba(4,12,16,0.72)_100%)]">
        </div>
        <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_72%_18%,rgba(14,165,233,0.16)_0%,transparent_24%),radial-gradient(circle_at_34%_74%,rgba(22,163,74,0.15)_0%,transparent_28%)]">
        </div>
        <div
            class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.03)_1px,transparent_1px)] bg-[size:96px_96px] opacity-20">
        </div>

        <div class="relative z-10 flex min-h-screen flex-col px-4 py-6 sm:px-6 lg:px-10">
            <header class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4">
                <div
                    class="inline-flex items-center rounded-full border border-white/20 bg-white/6 px-5 py-3 backdrop-blur-md">
                    <span class="font-display text-[1.9rem] font-extrabold leading-none sm:text-[2.2rem]">
                        <span class="text-[rgb(var(--brand))]">SPEC</span><span class="text-white">toolbox</span>
                    </span>
                </div>

                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center rounded-full bg-white px-7 py-3 text-sm font-semibold text-slate-950 shadow-[0_20px_36px_-22px_rgba(15,23,42,0.45)] transition hover:bg-emerald-50">
                    Dashboard
                </a>
            </header>

            <section class="mx-auto flex w-full max-w-7xl flex-1 items-center">
                <div class="grid w-full gap-10 lg:grid-cols-[1.1fr,0.9fr] lg:items-end">
                    <div class="max-w-4xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.34em] text-emerald-200/90">CLT Data
                            Workspace</p>

                        <h1
                            class="mt-7 font-display text-[clamp(3.3rem,8vw,7rem)] font-extrabold leading-[0.92] text-white">
                            Keep every
                            <span class="block text-transparent"
                                style="background: linear-gradient(90deg, rgb(var(--brand)) 0%, rgb(var(--accent-teal)) 100%); -webkit-background-clip: text; background-clip: text;">
                                supplier tree
                            </span>
                            in sync.
                        </h1>

                        <p class="mt-8 max-w-2xl text-[clamp(1rem,1.75vw,1.22rem)] leading-[1.8] text-slate-200/82">
                            Manage suppliers, shape layups, track layers, and review imports before anything commits.
                            One surface, clear decisions, no drift.
                        </p>

                        <div
                            class="mt-10 flex flex-wrap items-center gap-x-5 gap-y-3 text-sm font-medium text-white/72">
                            <span>Supplier management</span>
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400/80"></span>
                            <span>Conflict-safe import</span>
                            <span class="h-1.5 w-1.5 rounded-full bg-sky-400/80"></span>
                            <span>Export-ready structure</span>
                        </div>
                    </div>

                    <div class="flex justify-start lg:justify-end">
                        <div class="w-full max-w-sm border border-white/14 bg-white/6 p-5 backdrop-blur-md">
                            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-white/45">Import
                                        Status</p>
                                    <p class="mt-2 font-display text-2xl font-bold text-white">2-Phase Resolve</p>
                                </div>
                                <span
                                    class="rounded-full bg-emerald-400/14 px-3 py-1 text-[11px] font-bold text-emerald-300">Ready</span>
                            </div>

                            <div class="space-y-4 pt-5">
                                <div>
                                    <div class="mb-1.5 flex items-center justify-between text-xs text-white/58">
                                        <span>Suppliers</span>
                                        <span>{{ \App\Models\Supplier::count() }}</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-white/10">
                                        <div class="h-2 rounded-full bg-gradient-to-r from-[rgb(var(--brand))] to-[rgb(var(--accent-teal))]"
                                            style="width: 72%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="mb-1.5 flex items-center justify-between text-xs text-white/58">
                                        <span>Layups</span>
                                        <span>{{ \App\Models\CltLayup::count() }}</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-white/10">
                                        <div class="h-2 rounded-full bg-gradient-to-r from-[rgb(var(--accent-teal))] to-sky-300"
                                            style="width: 58%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="mb-1.5 flex items-center justify-between text-xs text-white/58">
                                        <span>Layers</span>
                                        <span>{{ \App\Models\CltLayer::count() }}</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-white/10">
                                        <div class="h-2 rounded-full bg-gradient-to-r from-emerald-400 to-[rgb(var(--brand))]"
                                            style="width: 84%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer
                class="mx-auto flex w-full max-w-7xl items-end justify-between gap-4 pb-2 text-[11px] uppercase tracking-[0.24em] text-white/34">
                <span>SPECtoolbox</span>
                <span>Supplier / Layup / Layer</span>
            </footer>
        </div>
    </main>
</body>

</html>