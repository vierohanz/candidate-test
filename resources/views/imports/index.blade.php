<x-app-layout title="Import Review">
    <div class="flex min-h-[60vh] flex-col items-center justify-center text-center">
        <div class="grid h-20 w-20 place-items-center rounded-3xl bg-emerald-500/10 text-emerald-500 mb-6">
            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
        </div>
        <h2 class="text-2xl font-black text-[rgb(var(--text-main))]">Import Review</h2>
        <p class="mt-2 max-w-sm text-[rgb(var(--text-soft))]">The 2-phase import engine is ready for deployment. Please use the CLI tools for large datasets or contact support for the UI branch.</p>
        <div class="mt-8">
            <a href="{{ route('dashboard') }}" class="clt-btn-brand px-8">Return Home</a>
        </div>
    </div>
</x-app-layout>
