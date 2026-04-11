<x-app-layout title="Export Center">
    <div class="flex min-h-[60vh] flex-col items-center justify-center text-center">
        <div class="grid h-20 w-20 place-items-center rounded-3xl bg-emerald-500/10 text-emerald-500 mb-6">
            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
        </div>
        <h2 class="text-2xl font-black text-[rgb(var(--text-main))]">Export Center</h2>
        <p class="mt-2 max-w-sm text-[rgb(var(--text-soft))]">The bulk export utility is currently being optimized. You can still export individual suppliers directly from the <a href="{{ route('suppliers.index') }}" class="text-emerald-500 font-bold hover:underline">Suppliers page</a>.</p>
        <div class="mt-8">
            <a href="{{ route('dashboard') }}" class="clt-btn-brand px-8">Back to Summary</a>
        </div>
    </div>
</x-app-layout>
