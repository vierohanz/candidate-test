<x-app-layout title="Suppliers">

    <div>
        <h2 class="text-2xl font-semibold text-[rgb(var(--text-main))] mb-6">Suppliers</h2>

        <!-- Controls row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-[rgb(var(--text-soft))]">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" id="searchInput" placeholder="Search by name..." class="ref-input w-full pl-9">
            </div>

            <div class="flex items-center gap-3">
                <button onclick="openCreateDrawer()" class="clt-btn-brand flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Supplier
                </button>
            </div>
        </div>

        <!-- Cards Grid -->
        <div id="suppliersGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
            <!-- Data -->
        </div>

        <!-- Pagination -->
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between mt-8 border-t border-[rgb(var(--line-color))] pt-6 transition-colors">
            <p class="text-[11px] text-[rgb(var(--text-soft))]" id="paginationInfo">Showing 0 suppliers</p>
            <div class="flex gap-2">
                <button id="prevPageBtn" disabled
                    class="clt-page-btn text-[rgb(var(--text-soft))] px-4 border border-[rgb(var(--line-color))] rounded-lg hover:border-[rgb(var(--text-soft))] transition-colors">Previous</button>
                <button id="nextPageBtn" disabled
                    class="clt-page-btn text-[rgb(var(--text-soft))] px-4 border border-[rgb(var(--line-color))] rounded-lg hover:border-[rgb(var(--text-soft))] transition-colors">Next</button>
            </div>
        </div>
    </div>

    <!-- Drawer Modal -->
    <div id="supplierDrawer" class="fixed inset-0 z-50 overflow-hidden hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm opacity-0 transition-opacity" id="drawerBackdrop"
            onclick="closeDrawer()"></div>
        <div class="absolute inset-y-0 right-0 max-w-sm w-full translate-x-full transition-transform" id="drawerPanel">
            <div class="h-full bg-[rgb(var(--app-bg))] border-l border-[rgb(var(--line-color))] flex flex-col">
                <div
                    class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center transition-colors">
                    <h3 class="text-[rgb(var(--text-main))] font-semibold" id="drawerTitle">Add Supplier</h3>
                    <button onclick="closeDrawer()"
                        class="text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition-colors"><svg
                            class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="p-6 flex-1">
                    <form id="supplierForm" onsubmit="saveSupplier(event)" class="space-y-4">
                        <input type="hidden" id="supplierId">
                        <div>
                            <label class="block text-xs font-semibold text-[rgb(var(--text-main))] mb-2">Supplier
                                Name</label>
                            <input type="text" id="supplierName" required class="ref-input w-full"
                                placeholder="PT. Company...">
                        </div>
                        <button type="submit" id="submitBtn" class="clt-btn-brand w-full mt-4">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="toast"
        class="fixed bottom-8 right-8 z-[60] bg-[rgb(var(--brand))] text-white px-5 py-3 rounded-md shadow-xl text-sm font-semibold opacity-0 translate-y-4 transition-all">
        <span id="toastMsg">Success</span>
    </div>

    <!-- Detail Viewer Modal -->
    <div id="detailModal" class="fixed inset-0 z-[100] overflow-hidden hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-md opacity-0 transition-opacity" id="detailBackdrop"
            onclick="closeDetailModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-[rgb(var(--app-bg))] border border-[rgb(var(--line-color))] rounded-2xl w-full max-w-lg scale-95 opacity-0 transition-all duration-300 shadow-2xl pointer-events-auto"
                id="detailPanel">
                <div class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center bg-black/5">
                    <h3 class="text-lg font-bold text-[rgb(var(--text-main))]" id="detailTitle">Data Integrity Check
                    </h3>
                    <button onclick="closeDetailModal()"
                        class="text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition-colors"><svg
                            class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="p-8">
                    <div id="detailContent" class="space-y-6">
                        <!-- Data here -->
                    </div>
                </div>
                <div class="p-6 border-t border-[rgb(var(--line-color))] flex justify-end">
                    <button onclick="closeDetailModal()" class="clt-btn-brand px-8 py-2.5">Close View</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden file input for import -->
    <input type="file" id="importFile" class="hidden" accept=".json">

    <!-- Import Conflict Modal -->
    <div id="importConflictModal" class="fixed inset-0 z-[100] overflow-hidden hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-md opacity-0 transition-opacity" id="conflictBackdrop"
            onclick="closeConflictModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-[rgb(var(--app-bg))] border border-[rgb(var(--line-color))] rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl scale-95 opacity-0 transition-all duration-300 pointer-events-auto"
                id="conflictPanel">
                <div class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center">
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
                    <!-- Content injected here -->
                </div>

                <div class="p-6 border-t border-[rgb(var(--line-color))] bg-black/5 flex items-center justify-between">
                    <button onclick="closeConflictModal()"
                        class="px-6 py-2.5 text-sm font-semibold text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition-colors">Cancel
                        Import</button>
                    <div class="flex gap-3">
                        <button id="importSkipBtn" onclick="confirmImport('skip')"
                            class="px-6 py-2.5 border border-[rgb(var(--line-color))] rounded-xl text-sm font-semibold text-[rgb(var(--text-main))] hover:border-[rgb(var(--text-soft))] transition-all">Keep
                            Existing (Skip)</button>
                        <button id="importOverwriteBtn" onclick="confirmImport('overwrite')"
                            class="clt-btn-brand px-8 py-2.5 shadow-lg shadow-emerald-500/20">Overwrite & Merge</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-[120] overflow-hidden hidden">
        <div class="absolute inset-0 bg-red-950/20 backdrop-blur-sm opacity-0 transition-opacity" id="deleteBackdrop"
            onclick="closeDeleteModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-[rgb(var(--app-bg))] border border-red-500/20 rounded-2xl w-full max-w-sm scale-95 opacity-0 transition-all duration-300 shadow-2xl pointer-events-auto"
                id="deletePanel">
                <div class="p-8 text-center">
                    <div
                        class="mx-auto w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center text-red-500 mb-6">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-[rgb(var(--text-main))] mb-2">Confirm Delete?</h3>
                    <p class="text-sm text-[rgb(var(--text-soft))] mb-8">This action is permanent and cannot be undone.
                        All associated data will be removed.</p>
                    <div class="flex gap-3">
                        <button onclick="closeDeleteModal()"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-[rgb(var(--line-color))] text-[rgb(var(--text-main))] font-semibold hover:bg-black/5 transition-colors">Cancel</button>
                        <button id="confirmDeleteBtn"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 text-white font-semibold hover:bg-red-600 transition-colors shadow-lg shadow-red-500/20">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentPage = 1, searchQuery = '', isEdit = false;
            async function fetchSuppliers(page = 1) {
                currentPage = page;
                const grid = document.getElementById('suppliersGrid');
                const cacheKey = `clt_supp_pg${page}_q${searchQuery}`;
                const cached = sessionStorage.getItem(cacheKey);

                if (cached) {
                    try {
                        const result = JSON.parse(cached);
                        if (!Array.isArray(result.data)) {
                            sessionStorage.removeItem(cacheKey);
                            throw new Error('stale_cache');
                        }
                        renderSuppliersDom(grid, result.data, result.metadata);
                        fetchSuppliersData(page, grid, cacheKey, false);
                    } catch (e) {
                        fetchSuppliersData(page, grid, cacheKey, true);
                    }
                } else {
                    grid.innerHTML = Array(8).fill(`
                        <div class="bg-[rgb(var(--card-bg))] border border-[rgb(var(--line-color))] rounded-xl p-5 w-full transition-colors">
                            <div class="flex flex-col items-center mt-3 mb-2">
                                <div class="h-14 w-14 rounded-full skeleton-loader mb-4"></div>
                                <div class="h-4 w-32 skeleton-loader rounded mb-3"></div>
                                <div class="h-3 w-16 skeleton-loader rounded"></div>
                            </div>
                        </div>
                    `).join('');
                    fetchSuppliersData(page, grid, cacheKey, true);
                }
            }

            async function fetchSuppliersData(page, grid, cacheKey, updateUI) {
                try {
                    const url = `{{ url('/api/v1/suppliers') }}?page=${page}&q=${searchQuery}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const result = await res.json(); if (!result.success) throw new Error(result.message);
                    sessionStorage.setItem(cacheKey, JSON.stringify(result));
                    renderSuppliersDom(grid, result.data, result.metadata);
                } catch (e) { if (updateUI) grid.innerHTML = `<div class="col-span-full py-10 text-center text-red-500">${e.message}</div>`; }
            }

            function renderSuppliersDom(grid, data, meta) {
                if (!data || data.length === 0) {
                    grid.innerHTML = `<div class="col-span-full py-20 text-center text-[rgb(var(--text-soft))]">
                        <svg class="w-10 h-10 opacity-20 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span class="block font-medium text-lg">Data Not Found</span>
                        <span class="block text-sm mt-1">No suppliers found. Create a new one to get started.</span>
                    </div>`;
                    updatePagination(meta); return;
                }

                grid.innerHTML = data.map(d => `
                    <div class="relative bg-[rgb(var(--app-bg))] border border-[rgb(var(--line-color))] rounded-xl p-5 hover:border-[rgb(var(--text-soft))] transition-colors group">
                        <!-- Actions Grid (Visible on hover) -->
                        <div class="absolute top-4 right-4 flex opacity-0 group-hover:opacity-100 transition-opacity gap-1">
                            <button onclick="viewSupplier(${d.id})" title="View Hierarchy" class="p-1.5 text-[rgb(var(--text-soft))] hover:text-blue-500 transition rounded"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                            <button onclick="exportSupplier(${d.id})" title="Export JSON" class="p-1.5 text-[rgb(var(--text-soft))] hover:text-green-500 transition rounded"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></button>
                            <button onclick="triggerImport(${d.id})" title="Import JSON" class="p-1.5 text-[rgb(var(--text-soft))] hover:text-yellow-500 transition rounded"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg></button>
                            <button onclick="openEditDrawer(${d.id}, '${(d.name || '').replace(/'/g, "\\'")}')" title="Edit" class="p-1.5 text-[rgb(var(--text-soft))] hover:text-[rgb(var(--brand))] transition rounded"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                            <button onclick="deleteSupplier(${d.id})" title="Delete" class="p-1.5 text-[rgb(var(--text-soft))] hover:text-red-500 transition rounded"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </div>

                        <!-- Card Body -->
                        <div class="flex flex-col items-center text-center mt-3 mb-2">
                            <div class="h-14 w-14 rounded-full bg-[rgb(var(--brand))/10] text-[rgb(var(--brand))] border border-[rgb(var(--brand))/20] flex items-center justify-center text-xl font-bold mb-4 shadow-sm group-hover:scale-105 transition-transform duration-300">
                                ${d.name ? d.name.charAt(0).toUpperCase() : '?'}
                            </div>
                            <h3 class="text-[14px] font-semibold text-[rgb(var(--text-main))] w-full truncate px-2" title="${d.name}">${d.name}</h3>
                            <div class="mt-2 text-[10px] uppercase font-mono tracking-widest text-[rgb(var(--text-soft))]">
                                ID / ${d.id}
                            </div>
                        </div>
                    </div>
                `).join('');
                updatePagination(meta);
            }
            function updatePagination(meta) {
                document.getElementById('paginationInfo').innerText = `Copyright © 2026 CLT Management System`;
                const prev = document.getElementById('prevPageBtn'), next = document.getElementById('nextPageBtn');
                prev.disabled = meta.current_page <= 1; prev.onclick = () => fetchSuppliers(meta.current_page - 1);
                next.disabled = meta.current_page >= meta.total_page; next.onclick = () => fetchSuppliers(meta.current_page + 1);
            }
            function showDrawer() { const d = document.getElementById('supplierDrawer'); d.classList.remove('hidden'); setTimeout(() => { document.getElementById('drawerBackdrop').classList.replace('opacity-0', 'opacity-100'); document.getElementById('drawerPanel').classList.replace('translate-x-full', 'translate-x-0'); }, 10); }
            function closeDrawer() { document.getElementById('drawerBackdrop').classList.replace('opacity-100', 'opacity-0'); document.getElementById('drawerPanel').classList.replace('translate-x-0', 'translate-x-full'); setTimeout(() => { document.getElementById('supplierDrawer').classList.add('hidden'); }, 300); }
            function openCreateDrawer() { isEdit = false; document.getElementById('drawerTitle').innerText = 'Add Supplier'; document.getElementById('supplierForm').reset(); showDrawer(); }
            function openEditDrawer(id, name) { isEdit = true; document.getElementById('drawerTitle').innerText = 'Edit Supplier'; document.getElementById('supplierId').value = id; document.getElementById('supplierName').value = name; showDrawer(); }
            async function saveSupplier(e) {
                e.preventDefault(); const id = document.getElementById('supplierId').value, name = document.getElementById('supplierName').value, btn = document.getElementById('submitBtn'); btn.disabled = true; btn.innerText = 'Saving...';
                const url = isEdit ? `{{ url('/api/v1/suppliers') }}/${id}/update` : `{{ url('/api/v1/suppliers') }}`; const method = isEdit ? 'PATCH' : 'POST';
                try { const r = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ name }) }); const res = await r.json(); if (!r.ok) throw new Error(res.message); showToast(isEdit ? 'Updated' : 'Created'); closeDrawer(); fetchSuppliers(currentPage); } catch (err) { showToast(err.message); } finally { btn.disabled = false; btn.innerText = 'Save'; }
            }
            let deleteId = null;
            function deleteSupplier(id) {
                deleteId = id;
                const m = document.getElementById('deleteModal'); m.classList.remove('hidden');
                setTimeout(() => { document.getElementById('deleteBackdrop').classList.replace('opacity-0', 'opacity-100'); document.getElementById('deletePanel').classList.remove('scale-95', 'opacity-0'); }, 50);
                document.getElementById('confirmDeleteBtn').onclick = async () => {
                    const btn = document.getElementById('confirmDeleteBtn');
                    btn.disabled = true; btn.innerText = 'Deleting...';
                    try {
                        const r = await fetch(`{{url('/api/v1/suppliers')}}/${deleteId}/delete`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' } });
                        const res = await r.json();
                        if (!r.ok) throw new Error(res.message);
                        showToast('Supplier Removed');
                        closeDeleteModal();
                        fetchSuppliers(currentPage);
                    } catch (e) { showToast(e.message); } finally { btn.disabled = false; btn.innerText = 'Delete'; }
                };
            }
            function closeDeleteModal() {
                document.getElementById('deleteBackdrop').classList.replace('opacity-100', 'opacity-0'); document.getElementById('deletePanel').classList.add('scale-95', 'opacity-0');
                setTimeout(() => { document.getElementById('deleteModal').classList.add('hidden'); }, 300);
            }
            function showToast(m) { const t = document.getElementById('toast'); document.getElementById('toastMsg').innerText = m; t.classList.replace('opacity-0', 'opacity-100'); t.classList.replace('translate-y-4', 'translate-y-0'); setTimeout(() => { t.classList.replace('opacity-100', 'opacity-0'); t.classList.replace('translate-y-0', 'translate-y-4'); }, 3000); }

            async function viewSupplier(id) {
                const r = await fetch(`{{url('/api/v1/suppliers')}}/${id}/show`);
                const res = await r.json();
                if (!res.success) { showToast(res.message); return; }

                const data = res.data;
                const content = document.getElementById('detailContent');

                let layupsHtml = (data.layups || []).map(l => `
                    <div class="flex items-center justify-between p-2.5 bg-black/5 rounded-lg border border-[rgb(var(--line-color))] hover:border-emerald-500/30 transition-colors">
                        <span class="text-xs font-semibold text-[rgb(var(--text-main))]">${l.name}</span>
                        <span class="text-[9px] font-mono text-[rgb(var(--text-soft))]">#${l.id}</span>
                    </div>
                `).join('');

                if ((data.layups || []).length === 0) layupsHtml = '<p class="text-[11px] text-[rgb(var(--text-soft))] italic">No layups configured yet.</p>';

                content.innerHTML = `
                    <div class="flex items-center gap-4 p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-xl mb-6">
                        <div class="h-12 w-12 rounded-lg bg-emerald-500 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-emerald-500/20">${data.name.charAt(0)}</div>
                        <div>
                            <p class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest mb-0.5">Supplier Identity</p>
                            <p class="text-xl font-bold text-[rgb(var(--text-main))]">${data.name}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[10px] font-bold text-[rgb(var(--text-soft))] uppercase tracking-widest">Linked CLT Layups</h4>
                            <span class="text-[10px] bg-emerald-500/10 text-emerald-600 px-2 py-0.5 rounded-full font-bold">${(data.layups || []).length} Sets</span>
                        </div>
                        <div class="max-h-[200px] overflow-y-auto pr-2 custom-scrollbar space-y-2">
                            ${layupsHtml}
                        </div>
                    </div>
                `;
                openDetailModal();
            }

            function openDetailModal() {
                const m = document.getElementById('detailModal');
                m.classList.remove('hidden');
                setTimeout(() => {
                    document.getElementById('detailBackdrop').classList.replace('opacity-0', 'opacity-100');
                    document.getElementById('detailPanel').classList.remove('scale-95', 'opacity-0');
                }, 50);
            }

            function closeDetailModal() {
                document.getElementById('detailBackdrop').classList.replace('opacity-100', 'opacity-0');
                document.getElementById('detailPanel').classList.add('scale-95', 'opacity-0');
                setTimeout(() => { document.getElementById('detailModal').classList.add('hidden'); }, 300);
            }

            function exportSupplier(id) { window.open(`{{url('/api/v1/export')}}/${id}`, '_blank'); }

            let importSupplierId = null;
            let activeImportToken = null;

            function triggerImport(id) { importSupplierId = id; document.getElementById('importFile').click(); }

            document.getElementById('importFile').addEventListener('change', async function (e) {
                if (!e.target.files[0]) return; const fd = new FormData(); fd.append('file', e.target.files[0]);
                try {
                    const r = await fetch(`{{url('/api/v1/suppliers')}}/${importSupplierId}/import/scan`, { method: 'POST', body: fd, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{csrf_token()}}' } });
                    const res = await r.json(); if (!r.ok) throw new Error(res.message);
                    if (res.data && res.data.auto_confirmed) { showToast('Clean Data! Imported automatically.'); fetchSuppliers(currentPage); }
                    else {
                        openConflictModal(res.data);
                    }
                } catch (err) { showToast('Import failed: ' + err.message); }
                e.target.value = '';
            });

            function openConflictModal(data) {
                activeImportToken = data.import_token;
                const content = document.getElementById('conflictContent');

                let html = `
                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="bg-blue-500/5 border border-blue-500/10 p-4 rounded-xl">
                            <span class="block text-[9px] uppercase tracking-widest text-blue-500 font-bold mb-1">New Layups</span>
                            <span class="text-2xl font-bold text-[rgb(var(--text-main))]">${data.layups.new.length}</span>
                        </div>
                        <div class="bg-amber-500/5 border border-amber-500/10 p-4 rounded-xl">
                            <span class="block text-[9px] uppercase tracking-widest text-amber-500 font-bold mb-1">Layer Conflicts</span>
                            <span class="text-2xl font-bold text-[rgb(var(--text-main))]">${data.layers.conflicts.length}</span>
                        </div>
                        <div class="bg-emerald-500/5 border border-emerald-500/10 p-4 rounded-xl">
                            <span class="block text-[9px] uppercase tracking-widest text-emerald-500 font-bold mb-1">Matched Items</span>
                            <span class="text-2xl font-bold text-[rgb(var(--text-main))]">${data.layups.matches.length + data.layers.new.length}</span>
                        </div>
                    </div>
                `;

                if (data.layers.conflicts.length > 0) {
                    html += `<h4 class="text-xs font-bold text-amber-500 mb-4 flex items-center gap-2 uppercase tracking-tight">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Parameter Differences Detected
                    </h4>`;

                    html += `<div class="space-y-4">`;
                    data.layers.conflicts.forEach(c => {
                        const isDiff = (a, b) => a != b ? 'text-amber-500 font-bold underline decoration-amber-500/50 underline-offset-4' : '';
                        html += `
                            <div class="border border-[rgb(var(--line-color))] rounded-xl overflow-hidden shadow-sm">
                                <div class="bg-black/5 px-4 py-2 border-b border-[rgb(var(--line-color))] flex justify-between items-center transition-colors">
                                    <span class="text-[11px] font-bold text-[rgb(var(--text-main))] uppercase tracking-wide">Layup: ${c.layup_name} — Order #${c.layer_order}</span>
                                </div>
                                <div class="grid grid-cols-2">
                                    <div class="p-4 border-r border-[rgb(var(--line-color))] bg-red-500/[0.02]">
                                        <span class="text-[8px] font-bold text-red-500 uppercase tracking-widest block mb-3">System (Current)</span>
                                        <div class="space-y-1.5 text-xs text-[rgb(var(--text-main))]">
                                            <div class="flex justify-between"><span>Thickness</span> <span class="font-mono">${c.existing.thickness} mm</span></div>
                                            <div class="flex justify-between"><span>Width</span> <span class="font-mono">${c.existing.width} mm</span></div>
                                            <div class="flex justify-between"><span>Angle</span> <span class="font-mono">${c.existing.angle}°</span></div>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-emerald-500/[0.02]">
                                        <span class="text-[8px] font-bold text-emerald-500 uppercase tracking-widest block mb-3">Incoming (File)</span>
                                        <div class="space-y-1.5 text-xs text-[rgb(var(--text-main))]">
                                            <div class="flex justify-between"><span>Thickness</span> <span class="font-mono ${isDiff(c.incoming.thickness, c.existing.thickness)}">${c.incoming.thickness} mm</span></div>
                                            <div class="flex justify-between"><span>Width</span> <span class="font-mono ${isDiff(c.incoming.width, c.existing.width)}">${c.incoming.width} mm</span></div>
                                            <div class="flex justify-between"><span>Angle</span> <span class="font-mono ${isDiff(c.incoming.angle, c.existing.angle)}">${c.incoming.angle}°</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += `</div>`;
                } else {
                    html += `<div class="py-10 text-center text-[rgb(var(--text-soft))] border-2 border-dashed border-[rgb(var(--line-color))] rounded-2xl">
                        <p class="font-medium text-sm">No direct conflicts found.</p>
                        <p class="text-[10px] mt-1">Found ${data.layups.matches.length} matching entities that will be updated or reused.</p>
                    </div>`;
                }

                content.innerHTML = html;

                const modal = document.getElementById('importConflictModal');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    document.getElementById('conflictBackdrop').classList.replace('opacity-0', 'opacity-100');
                    document.getElementById('conflictPanel').classList.remove('scale-95', 'opacity-0');
                }, 50);
            }

            function closeConflictModal() {
                document.getElementById('conflictBackdrop').classList.replace('opacity-100', 'opacity-0');
                document.getElementById('conflictPanel').classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    document.getElementById('importConflictModal').classList.add('hidden');
                }, 300);
            }

            async function confirmImport(strategy) {
                const btn = strategy === 'skip' ? document.getElementById('importSkipBtn') : document.getElementById('importOverwriteBtn');
                const originalText = btn.innerText;
                btn.disabled = true; btn.innerText = 'Processing...';

                try {
                    const r = await fetch(`{{url('/api/v1/suppliers')}}/${importSupplierId}/import/confirm`, {
                        method: 'POST',
                        body: JSON.stringify({ import_token: activeImportToken, strategy: strategy }),
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{csrf_token()}}' }
                    });
                    const res = await r.json(); if (!r.ok) throw new Error(res.message);
                    showToast(`Success: ${strategy === 'overwrite' ? 'Merged & Overwritten' : 'Conflicts Skipped'}`);
                    closeConflictModal();
                    fetchSuppliers(currentPage);
                } catch (e) { alert(e.message); }
                finally { btn.disabled = false; btn.innerText = originalText; }
            }

            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', e => {
                searchQuery = e.target.value;
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => fetchSuppliers(1), 1000);
            });
            document.addEventListener('DOMContentLoaded', () => fetchSuppliers(1));
        </script>
    @endpush
</x-app-layout>