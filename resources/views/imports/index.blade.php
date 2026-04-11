<x-app-layout title="Import Review">
    <div class="grid gap-6 xl:grid-cols-[400px,1fr]">
        <section class="space-y-5">
            <div class="clt-card p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div
                        class="h-10 w-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-[rgb(var(--text-main))] uppercase tracking-wider">Source
                            Context</h3>
                        <p class="text-[10px] text-[rgb(var(--text-soft))]">Select destination supplier</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="relative">
                        <select id="importSupplierSelect" class="clt-pagination-select w-full">
                            <option value="">Choose supplier for import</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="clt-card p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-10 w-10 rounded-xl bg-orange-500/10 text-orange-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-[rgb(var(--text-main))] uppercase tracking-wider">Data
                            Payload</h3>
                        <p class="text-[10px] text-[rgb(var(--text-soft))]">JSON Format required</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="group relative">
                        <input type="file" id="importPageFileInput" accept="application/json,.json"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div
                            class="border-2 border-dashed border-[rgb(var(--line-color))] rounded-2xl p-8 text-center group-hover:border-emerald-500/50 group-hover:bg-emerald-500/5 transition-all">
                            <div
                                class="h-12 w-12 rounded-xl bg-black/5 mx-auto mb-3 flex items-center justify-center text-[rgb(var(--text-soft))]">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-[rgb(var(--text-main))]" id="fileNameLabel">Click or drop
                                JSON file</p>
                        </div>
                    </div>
                    <button type="button" id="importScanBtn" onclick="scanImportPage()"
                        class="clt-btn-brand w-full py-4 shadow-lg shadow-emerald-500/20">Analyze Import</button>
                </div>
            </div>
        </section>

        <section class="space-y-6">
            <div id="importEmptyState" class="clt-card px-8 py-24 text-center">
                <div class="h-20 w-20 rounded-3xl bg-black/5 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-[rgb(var(--text-soft))] opacity-20" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-xl font-black text-[rgb(var(--text-main))] mb-2">Analysis Pipeline Ready</h3>
                <p class="text-sm text-[rgb(var(--text-soft))] max-w-sm mx-auto leading-relaxed">Once you upload a
                    schema, we'll scan it against existing database records to prevent structural conflicts.</p>
            </div>

            <div id="importResultsPanel" class="hidden space-y-6">
                <div id="importSummaryGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="clt-card p-6 bg-gradient-to-br from-blue-500/[0.03] to-transparent">
                        <p class="text-[10px] font-bold text-[rgb(var(--text-soft))] uppercase tracking-[0.2em] mb-3">
                            Layups Detected</p>
                        <p id="importSummaryLayups"
                            class="text-3xl font-black text-[rgb(var(--text-main))] tracking-tight">0</p>
                    </div>
                    <div class="clt-card p-6 bg-gradient-to-br from-emerald-500/[0.03] to-transparent">
                        <p class="text-[10px] font-bold text-[rgb(var(--text-soft))] uppercase tracking-[0.2em] mb-3">
                            Layers Detected</p>
                        <p id="importSummaryLayers"
                            class="text-3xl font-black text-[rgb(var(--text-main))] tracking-tight">0</p>
                    </div>
                    <div class="clt-card p-6 bg-gradient-to-br from-amber-500/[0.03] to-transparent">
                        <p class="text-[10px] font-bold text-amber-600 uppercase tracking-[0.2em] mb-3">Matches Found
                        </p>
                        <p id="importSummaryMatches"
                            class="text-3xl font-black text-[rgb(var(--text-main))] tracking-tight">0</p>
                    </div>
                    <div class="clt-card p-6 bg-gradient-to-br from-rose-500/[0.03] to-transparent">
                        <p class="text-[10px] font-bold text-rose-500 uppercase tracking-[0.2em] mb-3">Conflicts</p>
                        <p id="importSummaryConflicts" class="text-3xl font-black text-rose-500 tracking-tight">0</p>
                    </div>
                </div>

                <div id="scanStatusCard"
                    class="clt-card p-8 border-2 border-dashed border-emerald-500/20 bg-emerald-500/[0.02]">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-5">
                            <div id="statusIcon"
                                class="h-14 w-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 id="statusTitle" class="text-lg font-black text-[rgb(var(--text-main))]">Data Scan
                                    Complete</h4>
                                <p id="statusDesc" class="text-sm text-[rgb(var(--text-soft))]">Validation successful.
                                    System is ready to commit changes.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <button id="reviewConflictsBtn" onclick="openConflictModalFromPage()"
                                class="hidden px-6 py-3 border border-rose-500/30 text-rose-500 rounded-2xl font-bold text-sm hover:bg-rose-500/5 transition-all">Review
                                Conflicts</button>
                            <button onclick="confirmImportPage('overwrite')"
                                class="clt-btn-brand px-10 py-3.5 shadow-xl shadow-emerald-500/20">Apply
                                Changes</button>
                        </div>
                    </div>
                </div>

                <div id="importExecutionCard" class="hidden clt-card p-1">
                    <div class="p-6 border-b border-[rgb(var(--line-color))]">
                        <h4 class="text-sm font-black text-emerald-500 uppercase tracking-widest">Transaction Log</h4>
                    </div>
                    <div id="importExecutionGrid"
                        class="grid grid-cols-2 md:grid-cols-3 gap-1 bg-[rgb(var(--line-color))]">
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div id="importConflictModal" class="fixed inset-0 z-[100] overflow-hidden hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-md opacity-0 transition-opacity" id="conflictBackdrop"
            onclick="closeConflictModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-[rgb(var(--app-bg))] border border-[rgb(var(--line-color))] rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl scale-95 opacity-0 transition-all duration-300 pointer-events-auto"
                id="conflictPanel">
                <div class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center bg-black/5">
                    <div>
                        <h3 class="text-xl font-bold text-[rgb(var(--text-main))]">Import Conflict Review</h3>
                        <p class="text-[11px] text-[rgb(var(--text-soft))] mt-1">Review differences before merging data
                            into the system.</p>
                    </div>
                    <button onclick="closeConflictModal()"
                        class="text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition-colors"><svg
                            class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <div class="p-6 overflow-y-auto flex-1 custom-scrollbar" id="conflictContent">
                </div>

                <div class="p-6 border-t border-[rgb(var(--line-color))] bg-black/5 flex items-center justify-between">
                    <button onclick="closeConflictModal()"
                        class="px-6 py-2.5 text-sm font-semibold text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition-colors">Return
                        to Summary</button>
                    <div class="flex gap-3">
                        <button id="importSkipBtn" onclick="confirmImportPage('skip')"
                            class="px-6 py-2.5 border border-[rgb(var(--line-color))] rounded-xl text-sm font-semibold text-[rgb(var(--text-main))] hover:border-[rgb(var(--text-soft))] transition-all">Keep
                            Existing (Skip)</button>
                        <button id="importMergeBtn" onclick="confirmImportPage('granular')"
                            class="px-6 py-2.5 border border-[rgb(var(--brand))] rounded-xl text-sm font-semibold text-[rgb(var(--brand))] hover:bg-[rgb(var(--brand))]/5 transition-all">Apply Selected</button>
                        <button id="importDuplicateBtn" onclick="confirmImportPage('duplicate')"
                            class="px-6 py-2.5 border border-blue-500/30 rounded-xl text-sm font-semibold text-blue-500 hover:bg-blue-500/5 transition-all">Duplicate Layup</button>
                        <button id="importOverwriteBtn" onclick="confirmImportPage('overwrite')"
                            class="clt-btn-brand px-8 py-2.5 shadow-lg shadow-emerald-500/20">Overwrite All</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let importSupplierId = '';
            let importToken = null;
            let lastReport = null;

            async function loadImportSuppliers() {
                const response = await fetch('/api/v1/suppliers?per_page=100', { headers: { Accept: 'application/json' } });
                const result = await response.json();
                const items = Array.isArray(result.data) ? result.data : [];
                document.getElementById('importSupplierSelect').innerHTML = '<option value="">Choose supplier for import</option>' + items.map((item) => `<option value="${item.id}">${item.name}</option>`).join('');
            }

            async function scanImportPage() {
                const fileInput = document.getElementById('importPageFileInput');
                const btn = document.getElementById('importScanBtn');
                if (!importSupplierId) return window.showToast('Please select a supplier first', false);
                if (!fileInput.files.length) return window.showToast('Please select a JSON file', false);

                const formData = new FormData();
                formData.append('file', fileInput.files[0]);
                btn.disabled = true;
                btn.innerText = 'Analyzing Data...';

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
                        document.getElementById('scanStatusCard').classList.add('hidden');
                        document.getElementById('importSummaryGrid').classList.add('hidden');
                        renderImportExecution(result.data.execution_summary);
                        window.showToast('Clean scan! Data imported automatically.', true);
                        return;
                    }

                    // Reset visibility for manual confirm
                    document.getElementById('scanStatusCard').classList.remove('hidden');
                    document.getElementById('importSummaryGrid').classList.remove('hidden');
                    document.getElementById('importExecutionCard').classList.add('hidden');

                    lastReport = result.data;
                    renderImportScan(result.data);

                    if (result.data.layers?.conflicts?.length > 0) {
                        openConflictModalFromPage();
                    }

                } catch (error) {
                    window.showToast(error.message, false);
                } finally {
                    btn.disabled = false;
                    btn.innerText = 'Analyze Import';
                }
            }

            function renderImportScan(report) {
                importToken = report.import_token;
                const conflicts = report.layers?.conflicts || [];
                document.getElementById('importSummaryLayups').innerText = report.summary?.total_layups ?? 0;
                document.getElementById('importSummaryLayers').innerText = report.summary?.total_layers ?? 0;
                document.getElementById('importSummaryMatches').innerText = (report.layups?.matches?.length || 0) + (report.layers?.new?.length || 0);
                document.getElementById('importSummaryConflicts').innerText = conflicts.length;

                const reviewBtn = document.getElementById('reviewConflictsBtn');
                const statusIcon = document.getElementById('statusIcon');
                const statusTitle = document.getElementById('statusTitle');
                const statusDesc = document.getElementById('statusDesc');

                if (conflicts.length > 0) {
                    reviewBtn.classList.remove('hidden');
                    statusIcon.className = 'h-14 w-14 rounded-2xl bg-rose-500 text-white flex items-center justify-center shadow-lg shadow-rose-500/20';
                    statusIcon.innerHTML = '<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
                    statusTitle.innerText = 'Conflicts Detected';
                    statusDesc.innerText = `${conflicts.length} technical layers differ from system records. Manual review required.`;
                } else {
                    reviewBtn.classList.add('hidden');
                    statusIcon.className = 'h-14 w-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20';
                    statusIcon.innerHTML = '<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
                    statusTitle.innerText = 'Ready to Merge';
                    statusDesc.innerText = 'Scan successful. All incoming data is valid and safely mappable.';
                }

                const content = document.getElementById('conflictContent');
                if (content) {
                    let html = `
                                    <div class="grid grid-cols-3 gap-4 mb-8">
                                        <div class="bg-blue-500/5 border border-blue-500/10 p-4 rounded-xl">
                                            <span class="block text-[9px] uppercase tracking-widest text-blue-500 font-bold mb-1">New Layups</span>
                                            <span class="text-2xl font-bold text-[rgb(var(--text-main))]">${report.layups.new.length}</span>
                                        </div>
                                        <div class="bg-amber-500/5 border border-amber-500/10 p-4 rounded-xl">
                                            <span class="block text-[9px] uppercase tracking-widest text-amber-500 font-bold mb-1">Layer Conflicts</span>
                                            <span class="text-2xl font-bold text-[rgb(var(--text-main))]">${conflicts.length}</span>
                                        </div>
                                        <div class="bg-emerald-500/5 border border-emerald-500/10 p-4 rounded-xl">
                                            <span class="block text-[9px] uppercase tracking-widest text-emerald-500 font-bold mb-1">Matches Found</span>
                                            <span class="text-2xl font-bold text-[rgb(var(--text-main))]">${report.layups.matches.length + report.layers.new.length}</span>
                                        </div>
                                    </div>
                                `;

                    if (conflicts.length > 0) {
                        html += `<div class="space-y-4">`;
                        conflicts.forEach(c => {
                            const isDiff = (f) => c.incoming[f] != c.existing[f];
                            const key = `${c.layup_name}_${c.layer_order}`;
                            html += `
                                <div class="border border-[rgb(var(--line-color))] rounded-xl overflow-hidden shadow-sm transition-colors group">
                                    <div class="bg-black/5 px-4 py-2 border-b border-[rgb(var(--line-color))] flex justify-between items-center">
                                        <span class="text-[11px] font-bold text-[rgb(var(--text-main))] uppercase tracking-wide">Layup: ${c.layup_name} — Order #${c.layer_order}</span>
                                        <span class="text-[9px] font-black text-[rgb(var(--text-soft))] uppercase tracking-widest">Select Properties to Update</span>
                                    </div>
                                    <div class="grid grid-cols-2">
                                        <div class="p-4 border-r border-[rgb(var(--line-color))] bg-red-500/[0.02]">
                                            <span class="text-[8px] font-bold text-red-500 uppercase tracking-widest block mb-3">System Record</span>
                                            <div class="space-y-4 text-xs text-[rgb(var(--text-main))]">
                                                <div class="flex justify-between items-center h-5"><span>Thickness</span> <span class="font-mono">${c.existing.thickness} mm</span></div>
                                                <div class="flex justify-between items-center h-5"><span>Width</span> <span class="font-mono">${c.existing.width} mm</span></div>
                                                <div class="flex justify-between items-center h-5"><span>Angle</span> <span class="font-mono">${c.existing.angle}°</span></div>
                                            </div>
                                        </div>
                                        <div class="p-4 bg-emerald-500/[0.02]">
                                            <span class="text-[8px] font-bold text-emerald-500 uppercase tracking-widest block mb-3">Incoming File</span>
                                            <div class="space-y-4 text-xs text-[rgb(var(--text-main))]">
                                                ${['thickness', 'width', 'angle'].map(f => `
                                                    <div class="flex justify-between items-center h-5 ${isDiff(f) ? 'text-amber-500' : ''}">
                                                        <span>${f.charAt(0).toUpperCase() + f.slice(1)}</span>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-mono ${isDiff(f) ? 'font-bold underline decoration-amber-500/30' : ''}">${c.incoming[f]} ${f === 'angle' ? '°' : 'mm'}</span>
                                                            ${isDiff(f) ? `
                                                                <input type="checkbox" data-res-key="${key}" data-res-field="${f}" checked 
                                                                    class="w-3.5 h-3.5 rounded border-emerald-500/30 bg-black/20 text-emerald-500 focus:ring-0 cursor-pointer">
                                                            ` : `<div class="w-3.5"></div>`}
                                                        </div>
                                                    </div>
                                                `).join('')}
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                        });
                        html += `</div>`;
                    }

                    content.innerHTML = html;
                }
            }

            async function confirmImportPage(strategy) {
                if (!importSupplierId || !importToken) return;
                const btnIdMap = {
                    'skip': 'importSkipBtn',
                    'overwrite': 'importOverwriteBtn',
                    'granular': 'importMergeBtn',
                    'duplicate': 'importDuplicateBtn'
                };
                const btn = document.getElementById(btnIdMap[strategy]);
                if (!btn) return;
                const originalText = btn.innerText;

                const resolutions = {};
                if (strategy === 'granular') {
                    document.querySelectorAll('input[data-res-key]:checked').forEach(cb => {
                        const key = cb.getAttribute('data-res-key');
                        const field = cb.getAttribute('data-res-field');
                        if (!resolutions[key]) resolutions[key] = [];
                        resolutions[key].push(field);
                    });
                    if (Object.keys(resolutions).length === 0) return window.showToast('Select at least one property to apply', false);
                }

                btn.disabled = true; btn.innerText = 'Syncing...';

                try {
                    const response = await fetch(`/api/v1/suppliers/${importSupplierId}/import/confirm`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            import_token: importToken,
                            strategy: strategy === 'granular' ? 'granular' : strategy,
                            resolutions: resolutions
                        })
                    });
                    const result = await response.json();
                    if (!response.ok || !result.success) throw new Error(result.message || 'Import failed');

                    closeConflictModal();
                    
                    // Hide the scan status and summary grids after success
                    document.getElementById('scanStatusCard').classList.add('hidden');
                    document.getElementById('importSummaryGrid').classList.add('hidden');
                    
                    renderImportExecution(result.data);
                    window.showToast('Import completed successfully', true);

                } catch (error) {
                    window.showToast(error.message, false);
                } finally {
                    btn.disabled = false; btn.innerText = originalText;
                }
            }

            function renderImportExecution(summary) {
                document.getElementById('importExecutionCard').classList.remove('hidden');
                const grid = document.getElementById('importExecutionGrid');
                const data = [
                    ['Layups New', summary.layups_created ?? 0],
                    ['Layups Patch', summary.layups_updated ?? 0],
                    ['Layups Skip', summary.layups_skipped ?? 0],
                    ['Layers New', summary.layers_created ?? 0],
                    ['Layers Patch', summary.layers_updated ?? 0],
                    ['Layers Skip', summary.layers_skipped ?? 0],
                ];
                grid.innerHTML = data.map(([label, value]) => `
                                <div class="bg-[rgb(var(--app-bg))] p-5 border-r border-b border-[rgb(var(--line-color))] last:border-r-0">
                                    <p class="text-[9px] font-bold text-[rgb(var(--text-soft))] uppercase tracking-[0.2em] mb-2">${label}</p>
                                    <p class="text-3xl font-black text-[rgb(var(--text-main))]">${value}</p>
                                </div>
                            `).join('');
            }

            function openConflictModalFromPage() {
                const modal = document.getElementById('importConflictModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        document.getElementById('conflictBackdrop')?.classList.replace('opacity-0', 'opacity-100');
                        document.getElementById('conflictPanel')?.classList.remove('scale-95', 'opacity-0');
                    }, 50);
                }
            }

            function closeConflictModal() {
                document.getElementById('conflictBackdrop')?.classList.replace('opacity-100', 'opacity-0');
                document.getElementById('conflictPanel')?.classList.add('scale-95', 'opacity-0');
                setTimeout(() => { document.getElementById('importConflictModal')?.classList.add('hidden'); }, 300);
            }

            document.getElementById('importSupplierSelect').addEventListener('change', (e) => {
                importSupplierId = e.target.value;
                importToken = null;
                document.getElementById('importResultsPanel').classList.add('hidden');
                document.getElementById('importEmptyState').classList.remove('hidden');
            });

            document.getElementById('importPageFileInput').addEventListener('change', (e) => {
                const label = document.getElementById('fileNameLabel');
                if (e.target.files.length > 0) {
                    label.innerText = e.target.files[0].name;
                    label.classList.add('text-emerald-500');
                } else {
                    label.innerText = 'Click or drop JSON file';
                    label.classList.remove('text-emerald-500');
                }
            });

            window.scanImportPage = scanImportPage;
            window.confirmImportPage = confirmImportPage;
            window.openConflictModalFromPage = openConflictModalFromPage;
            window.closeConflictModal = closeConflictModal;

            document.addEventListener('DOMContentLoaded', loadImportSuppliers);
        </script>
    @endpush
</x-app-layout>