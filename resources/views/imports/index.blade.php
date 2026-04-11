<x-app-layout title="Import Review">
    <div class="grid gap-6 xl:grid-cols-[0.92fr,1.08fr]">
        <section class="space-y-5">
            <div class="clt-card p-5">
                <p class="text-[11px] font-black uppercase tracking-[0.24em] text-[rgb(var(--text-soft))]">Supplier Context</p>
                <div class="relative mt-4">
                    <select id="importSupplierSelect" class="clt-pagination-select w-full rounded-[16px] px-4 py-3.5 pr-10 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[rgba(var(--brand),0.22)]">
                        <option value="">Choose supplier for import</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[rgb(var(--text-soft))]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </span>
                </div>
                <p id="importSupplierHint" class="mt-3 text-sm text-[rgb(var(--text-soft))]">Pick one supplier. Scan runs first, commit happens only after review.</p>
            </div>

            <div class="clt-card p-5">
                <label class="block text-[11px] font-black uppercase tracking-[0.24em] text-[rgb(var(--text-soft))]">Upload JSON</label>
                <input type="file" id="importPageFileInput" accept="application/json,.json" class="mt-4 block w-full rounded-2xl border border-dashed border-[rgba(var(--line-color),0.14)] bg-transparent px-4 py-6 text-sm text-[rgb(var(--text-main))] file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-500 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-emerald-600">
                <button type="button" id="importScanBtn" onclick="scanImportPage()" class="mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-emerald-500 px-5 py-3 text-sm font-black text-white shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-600 disabled:cursor-not-allowed disabled:opacity-50">Scan Import</button>
            </div>

            <div id="importExecutionCard" class="hidden rounded-2xl border border-emerald-500/20 bg-emerald-50 p-5 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-emerald-600 dark:text-emerald-400">Execution Summary</p>
                <div id="importExecutionGrid" class="mt-4 grid gap-3 sm:grid-cols-2"></div>
            </div>
        </section>

        <section class="space-y-5">
            <div id="importEmptyState" class="clt-card px-6 py-16 text-center">
                <p class="text-lg font-black text-[rgb(var(--text-main))]">Ready to review</p>
                <p class="mt-2 text-sm text-[rgb(var(--text-soft))]">Choose a supplier, upload JSON, then run scan to detect conflicts before commit.</p>
            </div>

            <div id="importResultsPanel" class="hidden space-y-5">
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="clt-card p-4">
                        <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[rgb(var(--text-soft))]">Layups</p>
                        <p id="importSummaryLayups" class="mt-2 text-3xl font-black text-[rgb(var(--text-main))]">0</p>
                    </div>
                    <div class="clt-card p-4">
                        <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[rgb(var(--text-soft))]">Layers</p>
                        <p id="importSummaryLayers" class="mt-2 text-3xl font-black text-[rgb(var(--text-main))]">0</p>
                    </div>
                    <div class="clt-card p-4">
                        <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[rgb(var(--text-soft))]">Conflicts</p>
                        <p id="importSummaryConflicts" class="mt-2 text-3xl font-black text-rose-500">0</p>
                    </div>
                </div>

                <div class="clt-card p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-black text-[rgb(var(--text-main))]">Detected conflicts</p>
                            <p class="mt-1 text-sm text-[rgb(var(--text-soft))]">Layer order sama tapi data struktural beda akan muncul di sini.</p>
                        </div>
                        <span id="importConflictBadge" class="rounded-full bg-rose-50 px-3 py-1 text-[11px] font-black uppercase tracking-[0.18em] text-rose-600 dark:bg-rose-500/10 dark:text-rose-300">0 items</span>
                    </div>
                    <div id="importConflictListPage" class="mt-4 max-h-[28rem] space-y-3 overflow-y-auto"></div>
                </div>

                <div id="importResolvePanel" class="hidden clt-card p-5">
                    <p class="text-sm font-black text-[rgb(var(--text-main))]">Choose conflict strategy</p>
                    <p class="mt-1 text-sm text-[rgb(var(--text-soft))]">Use overwrite to replace conflict layers, or skip to keep existing rows.</p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <button type="button" id="importOverwriteBtn" onclick="confirmImportPage('overwrite')" class="rounded-2xl bg-rose-500 px-5 py-3 text-sm font-black text-white transition hover:bg-rose-600 disabled:cursor-not-allowed disabled:opacity-50">Overwrite Conflicts</button>
                        <button type="button" id="importSkipBtn" onclick="confirmImportPage('skip')" class="rounded-2xl border border-[rgba(var(--line-color),0.12)] bg-[rgb(var(--card-bg))] px-5 py-3 text-sm font-black text-[rgb(var(--text-main))] transition hover:bg-[rgba(var(--line-color),0.03)] disabled:cursor-not-allowed disabled:opacity-50">Skip Conflicts</button>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
    <script>
        let importSupplierId = '';
        let importToken = null;

        async function loadImportSuppliers() {
            const response = await fetch('/api/v1/suppliers?per_page=100', { headers: { Accept: 'application/json' } });
            const result = await response.json();
            const items = Array.isArray(result.data) ? result.data : [];
            document.getElementById('importSupplierSelect').innerHTML = '<option value="">Choose supplier for import</option>' + items.map((item) => `<option value="${item.id}">${item.name}</option>`).join('');
        }

        async function scanImportPage() {
            const fileInput = document.getElementById('importPageFileInput');
            const btn = document.getElementById('importScanBtn');
            if (!importSupplierId) return alert('Choose a supplier first.');
            if (!fileInput.files.length) return alert('Choose a JSON file first.');

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            btn.disabled = true;
            btn.innerText = 'SCANNING...';

            try {
                const response = await fetch(`/api/v1/suppliers/${importSupplierId}/import/scan`, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || 'Scan failed');

                document.getElementById('importEmptyState').classList.add('hidden');
                document.getElementById('importResultsPanel').classList.remove('hidden');

                if (result.data.auto_confirmed) {
                    renderImportExecution(result.data.execution_summary);
                    document.getElementById('importResolvePanel').classList.add('hidden');
                    document.getElementById('importConflictListPage').innerHTML = '<div class="rounded-2xl border border-emerald-500/20 bg-emerald-50 px-4 py-5 text-sm text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">Clean scan. Import executed automatically.</div>';
                    return;
                }

                renderImportScan(result.data);
            } catch (error) {
                alert(error.message);
            } finally {
                btn.disabled = false;
                btn.innerText = 'Scan Import';
            }
        }

        function renderImportScan(report) {
            importToken = report.import_token;
            const conflicts = report.layers?.conflicts || [];
            document.getElementById('importSummaryLayups').innerText = report.summary?.total_layups ?? 0;
            document.getElementById('importSummaryLayers').innerText = report.summary?.total_layers ?? 0;
            document.getElementById('importSummaryConflicts').innerText = conflicts.length;
            document.getElementById('importConflictBadge').innerText = `${conflicts.length} items`;
            const list = document.getElementById('importConflictListPage');

            if (!conflicts.length) {
                list.innerHTML = '<div class="rounded-2xl border border-emerald-500/20 bg-emerald-50 px-4 py-5 text-sm text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">Clean scan. No conflicts detected. Confirm is ready.</div>';
                document.getElementById('importResolvePanel').classList.remove('hidden');
                return;
            }

            list.innerHTML = conflicts.map((conflict) => `
                <div class="rounded-2xl border border-rose-500/14 bg-rose-50/70 p-4 dark:bg-rose-500/8 dark:border-rose-500/20">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-black text-[rgb(var(--text-main))]">${conflict.layup_name} · Layer ${conflict.layer_order}</p>
                        <span class="rounded-full bg-rose-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.16em] text-rose-600 dark:bg-rose-500/10 dark:text-rose-300">Conflict</span>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl bg-white/70 p-3 ring-1 ring-rose-200/60 dark:bg-white/5 dark:ring-white/8">
                            <p class="text-[11px] font-black uppercase tracking-[0.18em] text-[rgb(var(--text-soft))]">Existing</p>
                            <div class="mt-2 space-y-1 text-sm text-[rgb(var(--text-main))]">
                                <p>Thickness: ${conflict.existing.thickness}</p>
                                <p>Width: ${conflict.existing.width}</p>
                                <p>Angle: ${conflict.existing.angle}</p>
                            </div>
                        </div>
                        <div class="rounded-xl bg-white/70 p-3 ring-1 ring-emerald-200/60 dark:bg-white/5 dark:ring-white/8">
                            <p class="text-[11px] font-black uppercase tracking-[0.18em] text-[rgb(var(--text-soft))]">Incoming</p>
                            <div class="mt-2 space-y-1 text-sm text-[rgb(var(--text-main))]">
                                <p>Thickness: ${conflict.incoming.thickness}</p>
                                <p>Width: ${conflict.incoming.width}</p>
                                <p>Angle: ${conflict.incoming.angle}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            document.getElementById('importResolvePanel').classList.remove('hidden');
        }

        async function confirmImportPage(strategy) {
            if (!importSupplierId || !importToken) return alert('Scan first.');
            document.getElementById('importOverwriteBtn').disabled = true;
            document.getElementById('importSkipBtn').disabled = true;

            try {
                const response = await fetch(`/api/v1/suppliers/${importSupplierId}/import/confirm`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ import_token: importToken, strategy })
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || 'Import failed');
                renderImportExecution(result.data);
                document.getElementById('importResolvePanel').classList.add('hidden');
            } catch (error) {
                alert(error.message);
            } finally {
                document.getElementById('importOverwriteBtn').disabled = false;
                document.getElementById('importSkipBtn').disabled = false;
            }
        }

        function renderImportExecution(summary) {
            document.getElementById('importExecutionCard').classList.remove('hidden');
            document.getElementById('importExecutionGrid').innerHTML = [
                ['Layups Created', summary.layups_created ?? 0],
                ['Layups Updated', summary.layups_updated ?? 0],
                ['Layups Skipped', summary.layups_skipped ?? 0],
                ['Layers Created', summary.layers_created ?? 0],
                ['Layers Updated', summary.layers_updated ?? 0],
                ['Layers Skipped', summary.layers_skipped ?? 0],
            ].map(([label, value]) => `<div class="rounded-xl bg-white/70 px-4 py-3 ring-1 ring-emerald-200/70 dark:bg-white/5 dark:ring-white/8"><p class="text-[11px] font-black uppercase tracking-[0.18em] text-[rgb(var(--text-soft))]">${label}</p><p class="mt-2 text-2xl font-black text-[rgb(var(--text-main))]">${value}</p></div>`).join('');
        }

        document.getElementById('importSupplierSelect').addEventListener('change', (e) => {
            importSupplierId = e.target.value;
            importToken = null;
            const label = importSupplierId ? e.target.options[e.target.selectedIndex]?.text : 'Choose one supplier. Scan runs first, commit happens only after review.';
            document.getElementById('importSupplierHint').innerText = importSupplierId ? `Import target: ${label}` : label;
            document.getElementById('importExecutionCard').classList.add('hidden');
            document.getElementById('importResultsPanel').classList.add('hidden');
            document.getElementById('importEmptyState').classList.remove('hidden');
            document.getElementById('importConflictListPage').innerHTML = '';
        });

        window.scanImportPage = scanImportPage;
        window.confirmImportPage = confirmImportPage;

        document.addEventListener('DOMContentLoaded', loadImportSuppliers);
    </script>
    @endpush
</x-app-layout>
