<x-app-layout title="Layers">

    <div class="space-y-6">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="space-y-2">
                <p class="text-[11px] font-black uppercase tracking-[0.24em] text-[rgb(var(--text-soft))]">Layer Workspace</p>
                <h2 class="text-3xl font-black text-[rgb(var(--text-main))]">Technical Layers</h2>
                <p class="max-w-2xl text-sm leading-6 text-[rgb(var(--text-soft))]">Pilih supplier dan layup aktif, lalu kelola urutan layer, thickness, width, dan angle dari satu tampilan yang lebih rapi.</p>
            </div>
            <button onclick="openCreateDrawer()"
                class="clt-btn-brand inline-flex items-center justify-center gap-2 px-6 py-3 shadow-lg shadow-emerald-500/10">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                New Layer
            </button>
        </div>

        <section class="grid gap-4 lg:grid-cols-2">
            <div class="clt-card p-5">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[rgb(var(--text-soft))]">Supplier</p>
                <div class="relative mt-4">
                    <select id="supplierFilter" onchange="handleSupplierChange()"
                        class="clt-pagination-select w-full rounded-[16px] px-4 py-3.5 pr-10 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[rgba(var(--brand),0.22)]"></select>
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[rgb(var(--text-soft))]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </span>
                </div>
            </div>
            <div class="clt-card p-5">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[rgb(var(--text-soft))]">Layup</p>
                <div class="relative mt-4">
                    <select id="layupFilter" onchange="handleLayupChange()"
                        class="clt-pagination-select w-full rounded-[16px] px-4 py-3.5 pr-10 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[rgba(var(--brand),0.22)]"></select>
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[rgb(var(--text-soft))]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </span>
                </div>
            </div>
        </section>

        <section class="clt-card overflow-hidden p-0">
            <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse ref-table transition-colors">
                <thead>
                    <tr>
                        <th class="w-16 pl-4">No</th>
                        <th class="w-20">Order</th>
                        <th>Layup Name</th>
                        <th class="text-center">Thickness (mm)</th>
                        <th class="text-center">Width (mm)</th>
                        <th class="text-center">Angle (°)</th>
                        <th class="w-24 text-center pr-4">Action</th>
                    </tr>
                </thead>
                <tbody id="layersTableBody">
                </tbody>
            </table>
            </div>
        </section>

        <div class="clt-pagination-shell flex flex-col gap-4 rounded-[18px] px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <p class="text-sm text-[rgb(var(--text-soft))]" id="paginationInfo">Showing 0 layers</p>
            <div class="flex gap-2">
                <button id="prevPageBtn" disabled
                    class="clt-page-btn px-4">Prev</button>
                <button id="nextPageBtn" disabled
                    class="clt-page-btn px-4">Next</button>
            </div>
        </div>
    </div>

    <!-- Drawer Modal -->
    <div id="layerDrawer" class="fixed inset-0 z-50 overflow-hidden hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm opacity-0 transition-opacity" id="drawerBackdrop"
            onclick="closeDrawer()"></div>
        <div class="absolute inset-y-0 right-0 max-w-sm w-full translate-x-full transition-transform" id="drawerPanel">
            <div
                class="h-full bg-[rgb(var(--app-bg))] border-l border-[rgb(var(--line-color))] flex flex-col transition-colors">
                <div class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center">
                    <h3 class="text-[rgb(var(--text-main))] font-semibold" id="drawerTitle">Add Layer</h3>
                    <button onclick="closeDrawer()"
                        class="text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition-colors"><svg
                            class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="p-6 flex-1 overflow-y-auto">
                    <form onsubmit="saveLayer(event)" class="space-y-4">
                        <input type="hidden" id="layerId">
                        <div>
                            <label class="block text-xs font-semibold text-[rgb(var(--text-main))] mb-2">Layup</label>
                            <select id="formLayupId" required class="ref-input w-full"></select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label
                                    class="block text-xs font-semibold text-[rgb(var(--text-main))] mb-2">Order</label><input
                                    type="number" id="formOrder" required class="ref-input w-full"></div>
                            <div><label class="block text-xs font-semibold text-[rgb(var(--text-main))] mb-2">Angle
                                    °</label><input type="number" step="0.1" id="formAngle" required
                                    class="ref-input w-full"></div>
                            <div><label
                                    class="block text-xs font-semibold text-[rgb(var(--text-main))] mb-2">Thickness</label><input
                                    type="number" step="0.01" id="formThickness" required class="ref-input w-full">
                            </div>
                            <div><label
                                    class="block text-xs font-semibold text-[rgb(var(--text-main))] mb-2">Width</label><input
                                    type="number" step="0.1" id="formWidth" required class="ref-input w-full"></div>
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
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm opacity-0 transition-opacity" id="detailBackdrop"
            onclick="closeDetailModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-[rgb(var(--app-bg))] border border-[rgb(var(--line-color))] rounded-2xl w-full max-w-lg scale-95 opacity-0 transition-all duration-300 shadow-2xl pointer-events-auto"
                id="detailPanel">
                <div class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center bg-black/5">
                    <h3 class="text-lg font-bold text-[rgb(var(--text-main))]" id="detailTitle">Technical Specifications
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

    <!-- Delete Confirmation Modal -->
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
                    <h3 class="text-xl font-bold text-[rgb(var(--text-main))] mb-2">Remove Layer?</h3>
                    <p class="text-sm text-[rgb(var(--text-soft))] mb-8">This technical layer will be deleted. This
                        action cannot be reversed.</p>
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
            let currentPage = 1, selectedSupplierId = '', selectedLayupId = '', isEdit = false;
            let layersRequestId = 0;
            let layerSupplierRequestId = 0;
            let layerLayupRequestId = 0;
            async function fetchLayers(page = 1) {
                currentPage = page; const tb = document.getElementById('layersTableBody');
                if (!tb) return;
                const requestId = ++layersRequestId;
                if (!selectedLayupId) {
                    tb.innerHTML = `<tr><td colspan="7" class="text-center py-20 text-[rgb(var(--text-soft))]">Please select a valid layup to view layers.</td></tr>`;
                    updatePagination({ current_page: 1, total_page: 1, total: 0 });
                    return;
                }
                const cacheKey = `clt_layer_s${selectedSupplierId}_l${selectedLayupId}_pg${page}`;
                const cached = sessionStorage.getItem(cacheKey);

                if (cached) {
                    try {
                        const result = JSON.parse(cached);
                        if (!Array.isArray(result.data)) { sessionStorage.removeItem(cacheKey); throw new Error('stale_cache'); }
                        if (requestId !== layersRequestId) return;
                        renderLayersDom(tb, result.data, result.metadata);
                        fetchLayersData(page, tb, cacheKey, false);
                    } catch (e) {
                        fetchLayersData(page, tb, cacheKey, true);
                    }
                } else {
                    tb.innerHTML = Array(5).fill(`
                                        <tr>
                                            <td colspan="7" class="py-4 px-4">
                                                <div class="flex items-center gap-4 w-full">
                                                    <div class="h-6 w-12 skeleton-loader rounded"></div>
                                                    <div class="h-6 w-1/3 skeleton-loader rounded"></div>
                                                    <div class="h-6 w-1/6 skeleton-loader rounded"></div>
                                                    <div class="h-6 w-1/6 skeleton-loader rounded"></div>
                                                    <div class="h-6 w-1/6 skeleton-loader rounded"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    `).join('');
                    fetchLayersData(page, tb, cacheKey, true);
                }
            }

            async function fetchLayersData(page, tb, cacheKey, updateUI) {
                const requestId = layersRequestId;
                const supplierAtRequest = selectedSupplierId;
                const layupAtRequest = selectedLayupId;
                try {
                    const url = `{{ url('/api/v1/layers') }}/${supplierAtRequest}/${layupAtRequest}?page=${page}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const result = await res.json(); if (!result.success) throw new Error(result.message);
                    const items = Array.isArray(result.data) ? result.data : [];
                    const meta = result.metadata || { current_page: 1, per_page: 10, total_page: 1, total_row: 0 };
                    if (requestId !== layersRequestId || supplierAtRequest !== selectedSupplierId || layupAtRequest !== selectedLayupId) return;
                    sessionStorage.setItem(cacheKey, JSON.stringify({ ...result, data: items, metadata: meta }));
                    if (!document.getElementById('layersTableBody')) return;
                    renderLayersDom(tb, items, meta);
                } catch (e) { if (updateUI && requestId === layersRequestId && document.getElementById('layersTableBody')) tb.innerHTML = `<tr><td colspan="7" class="text-center py-10 text-red-500">${e.message}</td></tr>`; }
            }

            function clearLayersCache() {
                Object.keys(sessionStorage).filter(k => k.startsWith('clt_layer_')).forEach(k => sessionStorage.removeItem(k));
                sessionStorage.removeItem('clt_layer_suppliers');
            }

            function renderLayersDom(tb, data, meta) {
                if (!Array.isArray(data) || data.length === 0) {
                    tb.innerHTML = `<tr><td colspan="7" class="text-center py-20 text-[rgb(var(--text-soft))]">
                                        <svg class="w-10 h-10 opacity-20 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        <span class="block font-medium text-lg">Data Not Found</span>
                                        <span class="block text-sm mt-1">No layers configured. Please add one.</span>
                                    </td></tr>`;
                    updatePagination(meta); return;
                }
                let startNo = ((meta.current_page - 1) * meta.per_page) + 1;
                tb.innerHTML = data.map((d, i) => `
                                    <tr class="hover:bg-[rgba(16,185,129,0.03)] transition-colors group">
                                        <td class="font-bold text-[rgb(var(--text-main))] pl-4">${startNo + i}</td>
                                        <td class="font-mono text-xs text-[rgb(var(--text-soft))]">#${d.layer_order}</td>
                                        <td class="text-[rgb(var(--text-main))] font-medium">${d.layup?.name || 'N/A'}<br><span class="text-[10px] text-[rgb(var(--text-soft))]">${d.layup?.supplier?.name || ''}</span></td>
                                        <td class="text-center text-[rgb(var(--text-main))]">${d.thickness}</td>
                                        <td class="text-center text-[rgb(var(--text-main))]">${d.width}</td>
                                        <td class="text-center text-[rgb(var(--text-main))] font-mono">${d.angle}&deg;</td>
                                        <td class="text-center pr-4">
                                            <div class="flex justify-center items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button onclick="viewLayer(${d.id})" title="View Detail" class="p-1 text-[rgb(var(--text-soft))] hover:text-blue-500 transition rounded"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                                <button onclick="openEditDrawer(${JSON.stringify(d).replace(/"/g, "&quot;")})" title="Edit" class="p-1 text-[rgb(var(--text-soft))] hover:text-[rgb(var(--brand))] transition rounded"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                                                <button onclick="deleteLayer(${d.id})" title="Delete" class="p-1 text-[rgb(var(--text-soft))] hover:text-red-500 transition rounded"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                            </div>
                                        </td>
                                    </tr>
                                `).join('');
                updatePagination(meta);
            }
            function updatePagination(meta) {
                const prev = document.getElementById('prevPageBtn'), next = document.getElementById('nextPageBtn');
                const total = Number(meta.total_row || 0);
                const perPage = Number(meta.per_page || 10);
                const current = Number(meta.current_page || 1);
                const from = total ? ((current - 1) * perPage) + 1 : 0;
                const to = total ? Math.min(current * perPage, total) : 0;
                document.getElementById('paginationInfo').innerText = `Showing ${from}-${to} of ${total} layers`;
                prev.disabled = meta.current_page <= 1; prev.onclick = () => fetchLayers(meta.current_page - 1);
                next.disabled = meta.current_page >= meta.total_page; next.onclick = () => fetchLayers(meta.current_page + 1);
            }

            async function init() {
                await loadSuppliers();
                if (selectedSupplierId) await loadLayups(selectedSupplierId);
            }

            async function loadSuppliers() {
                const requestId = ++layerSupplierRequestId;
                const cacheKey = 'clt_layer_suppliers';
                let res;
                const cached = sessionStorage.getItem(cacheKey);
                if (cached) {
                    res = JSON.parse(cached);
                    fetch('{{ url('/api/v1/suppliers') }}?per_page=100')
                        .then(r => r.json()).then(fresh => sessionStorage.setItem(cacheKey, JSON.stringify(fresh))).catch(() => { });
                } else {
                    const r = await fetch('{{ url('/api/v1/suppliers') }}?per_page=100');
                    res = await r.json();
                    sessionStorage.setItem(cacheKey, JSON.stringify(res));
                }
                const filter = document.getElementById('supplierFilter');
                if (requestId !== layerSupplierRequestId) return;
                if (!filter) return;
                filter.innerHTML = '';
                (res.data || []).forEach((s, ix) => {
                    filter.innerHTML += `<option value="${s.id}">${s.name}</option>`;
                    if (ix === 0) selectedSupplierId = s.id;
                });
            }

            async function loadLayups(sid) {
                const requestId = ++layerLayupRequestId;
                const cacheKey = `clt_layer_layups_s${sid}`;
                let res;
                const cached = sessionStorage.getItem(cacheKey);
                if (cached) {
                    res = JSON.parse(cached);
                    fetch(`{{ url('/api/v1/layups') }}/${sid}?per_page=100`)
                        .then(r => r.json()).then(fresh => sessionStorage.setItem(cacheKey, JSON.stringify(fresh))).catch(() => { });
                } else {
                    const r = await fetch(`{{ url('/api/v1/layups') }}/${sid}?per_page=100`);
                    res = await r.json();
                    sessionStorage.setItem(cacheKey, JSON.stringify(res));
                }
                const filter = document.getElementById('layupFilter');
                const formSelect = document.getElementById('formLayupId');
                if (requestId !== layerLayupRequestId || sid != selectedSupplierId) return;
                if (!filter || !formSelect) return;

                filter.innerHTML = '';
                formSelect.innerHTML = '';

                if (!res.data || res.data.length === 0) {
                    filter.innerHTML = '<option value="">No Layups Found</option>';
                    formSelect.innerHTML = '<option value="">No Layups Found</option>';
                    selectedLayupId = '';
                    fetchLayers(1);
                    return;
                }

                res.data.forEach((l, ix) => {
                    filter.innerHTML += `<option value="${l.id}">${l.name}</option>`;
                    formSelect.innerHTML += `<option value="${l.id}">${l.name}</option>`;
                    if (ix === 0) selectedLayupId = l.id;
                });
                fetchLayers(1);
            }

            function handleSupplierChange() {
                selectedSupplierId = document.getElementById('supplierFilter').value;
                loadLayups(selectedSupplierId);
            }

            function handleLayupChange() {
                selectedLayupId = document.getElementById('layupFilter').value;
                fetchLayers(1);
            }
            function showDrawer() { const d = document.getElementById('layerDrawer'); d.classList.remove('hidden'); setTimeout(() => { document.getElementById('drawerBackdrop').classList.replace('opacity-0', 'opacity-100'); document.getElementById('drawerPanel').classList.replace('translate-x-full', 'translate-x-0'); }, 10); }
            function closeDrawer() { document.getElementById('drawerBackdrop').classList.replace('opacity-100', 'opacity-0'); document.getElementById('drawerPanel').classList.replace('translate-x-0', 'translate-x-full'); setTimeout(() => { document.getElementById('layerDrawer').classList.add('hidden'); }, 300); }
            function openCreateDrawer() {
                isEdit = false;

                const title = document.getElementById('drawerTitle');
                const layerId = document.getElementById('layerId');
                const formLayupId = document.getElementById('formLayupId');
                const formOrder = document.getElementById('formOrder');
                const formAngle = document.getElementById('formAngle');
                const formThickness = document.getElementById('formThickness');
                const formWidth = document.getElementById('formWidth');

                if (title) title.innerText = 'New Technical Layer';
                if (layerId) layerId.value = '';
                if (formOrder) formOrder.value = '';
                if (formAngle) formAngle.value = '';
                if (formThickness) formThickness.value = '';
                if (formWidth) formWidth.value = '';
                if (formLayupId && selectedLayupId) formLayupId.value = selectedLayupId;

                showDrawer();
            }
            function openEditDrawer(l) {
                isEdit = true;

                const title = document.getElementById('drawerTitle');
                const layerId = document.getElementById('layerId');
                const formLayupId = document.getElementById('formLayupId');
                const formOrder = document.getElementById('formOrder');
                const formAngle = document.getElementById('formAngle');
                const formThickness = document.getElementById('formThickness');
                const formWidth = document.getElementById('formWidth');

                if (title) title.innerText = 'Edit Layer';
                if (layerId) layerId.value = l.id ?? '';
                if (formLayupId) formLayupId.value = l.layup_id ?? '';
                if (formOrder) formOrder.value = l.layer_order ?? '';
                if (formAngle) formAngle.value = l.angle ?? '';
                if (formThickness) formThickness.value = l.thickness ?? '';
                if (formWidth) formWidth.value = l.width ?? '';

                showDrawer();
            }
            async function saveLayer(e) { e.preventDefault(); const lid = document.getElementById('formLayupId').value, id = document.getElementById('layerId').value, btn = document.getElementById('submitBtn'); const data = { layup_id: lid, layer_order: document.getElementById('formOrder').value, thickness: document.getElementById('formThickness').value, width: document.getElementById('formWidth').value, angle: document.getElementById('formAngle').value }; btn.disabled = true; btn.innerText = 'Saving...'; const url = isEdit ? `{{url('/api/v1/layers')}}/${id}/update` : `{{url('/api/v1/layers')}}`; const method = isEdit ? 'PUT' : 'POST'; try { const r = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{csrf_token()}}' }, body: JSON.stringify(data) }); const res = await r.json(); if (!r.ok) throw new Error(res.message); showToast(isEdit ? 'Updated' : 'Created'); closeDrawer(); clearLayersCache(); fetchLayers(currentPage); } catch (err) { showToast(err.message); } finally { btn.disabled = false; btn.innerText = 'Save'; } }
            async function viewLayer(id) {
                const r = await fetch(`{{url('/api/v1/layers')}}/${id}/show`);
                const res = await r.json();
                if (!res.success) { showToast(res.message); return; }

                const data = res.data;
                const content = document.getElementById('detailContent');
                content.innerHTML = `
                                    <div class="flex items-center gap-4 p-4 bg-brand/5 border border-brand/10 rounded-xl">
                                        <div class="h-12 w-12 rounded-lg bg-[rgb(var(--brand))] flex items-center justify-center text-white font-bold text-xl">${data.layer_order}</div>
                                        <div>
                                            <p class="text-[10px] font-bold text-[rgb(var(--brand))] uppercase tracking-widest">Sequence Order</p>
                                            <p class="text-xl font-bold text-[rgb(var(--text-main))]">Order Reference #${data.layer_order}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div class="p-4 border border-[rgb(var(--line-color))] rounded-xl text-center bg-black/5 shadow-sm">
                                            <p class="text-[11px] font-bold text-[rgb(var(--text-soft))] uppercase mb-1.5 tracking-wide">Thickness</p>
                                            <p class="text-base font-mono text-[rgb(var(--text-main))] font-bold">${data.thickness}mm</p>
                                        </div>
                                        <div class="p-4 border border-[rgb(var(--line-color))] rounded-xl text-center bg-black/5 shadow-sm">
                                            <p class="text-[11px] font-bold text-[rgb(var(--text-soft))] uppercase mb-1.5 tracking-wide">Width</p>
                                            <p class="text-base font-mono text-[rgb(var(--text-main))] font-bold">${data.width}mm</p>
                                        </div>
                                        <div class="p-4 border border-[rgb(var(--line-color))] rounded-xl text-center bg-black/5 shadow-sm">
                                            <p class="text-[11px] font-bold text-[rgb(var(--text-soft))] uppercase mb-1.5 tracking-wide">Angle</p>
                                            <p class="text-base font-mono text-[rgb(var(--text-main))] font-bold">${data.angle}°</p>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-black/5 rounded-xl border border-[rgb(var(--line-color))]">
                                        <p class="text-[9px] font-bold text-[rgb(var(--text-soft))] uppercase tracking-widest mb-2">Hierarchy Context</p>
                                        <div class="space-y-1 text-xs">
                                            <div class="flex justify-between"><span>Layup Name</span> <span class="font-semibold text-[rgb(var(--text-main))]">${data.layup?.name}</span></div>
                                            <div class="flex justify-between"><span>Supplier</span> <span class="font-semibold text-[rgb(var(--text-main))]">${data.layup?.supplier_name}</span></div>
                                        </div>
                                    </div>
                                `;
                openDetailModal();
            }

            function openDetailModal() {
                const m = document.getElementById('detailModal'); m.classList.remove('hidden');
                setTimeout(() => { document.getElementById('detailBackdrop').classList.replace('opacity-0', 'opacity-100'); document.getElementById('detailPanel').classList.remove('scale-95', 'opacity-0'); }, 50);
            }
            function closeDetailModal() {
                document.getElementById('detailBackdrop').classList.replace('opacity-100', 'opacity-0'); document.getElementById('detailPanel').classList.add('scale-95', 'opacity-0');
                setTimeout(() => { document.getElementById('detailModal').classList.add('hidden'); }, 300);
            }
            let deleteId = null;
            function deleteLayer(id) {
                deleteId = id;
                const m = document.getElementById('deleteModal'); m.classList.remove('hidden');
                setTimeout(() => { document.getElementById('deleteBackdrop').classList.replace('opacity-0', 'opacity-100'); document.getElementById('deletePanel').classList.remove('scale-95', 'opacity-0'); }, 50);
                document.getElementById('confirmDeleteBtn').onclick = async () => {
                    const btn = document.getElementById('confirmDeleteBtn');
                    btn.disabled = true; btn.innerText = 'Deleting...';
                    try {
                        const r = await fetch(`{{url('/api/v1/layers')}}/${deleteId}/delete`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' } });
                        const res = await r.json();
                        if (!r.ok) throw new Error(res.message);
                        showToast('Layer removed');
                        closeDeleteModal();
                        clearLayersCache(); fetchLayers(currentPage);
                    } catch (e) { showToast(e.message); } finally { btn.disabled = false; btn.innerText = 'Delete'; }
                };
            }
            function closeDeleteModal() {
                document.getElementById('deleteBackdrop').classList.replace('opacity-100', 'opacity-0'); document.getElementById('deletePanel').classList.add('scale-95', 'opacity-0');
                setTimeout(() => { document.getElementById('deleteModal').classList.add('hidden'); }, 300);
            }
            function showToast(m) { const t = document.getElementById('toast'); document.getElementById('toastMsg').innerText = m; t.classList.replace('opacity-0', 'opacity-100'); t.classList.replace('translate-y-4', 'translate-y-0'); setTimeout(() => { t.classList.replace('opacity-100', 'opacity-0'); t.classList.replace('translate-y-0', 'translate-y-4'); }, 3000); }
            window.openCreateDrawer = openCreateDrawer;
            window.openEditDrawer = openEditDrawer;
            window.viewLayer = viewLayer;
            window.deleteLayer = deleteLayer;
            window.closeDrawer = closeDrawer;
            window.closeDetailModal = closeDetailModal;
            window.closeDeleteModal = closeDeleteModal;
            window.handleSupplierChange = handleSupplierChange;
            window.handleLayupChange = handleLayupChange;

            document.addEventListener('DOMContentLoaded', init);
        </script>
    @endpush
</x-app-layout>
