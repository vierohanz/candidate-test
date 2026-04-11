<x-app-layout title="Layups">

    <div>
        <h2 class="text-2xl font-semibold text-[rgb(var(--text-main))] mb-2">Layups</h2>

        <div class="flex items-center gap-3 overflow-x-auto pb-4 mb-4 scrollbar-hide" id="supplierTabs">
        </div>

        <div class="flex items-center justify-between mb-6">
            <div class="relative w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-[rgb(var(--text-soft))]">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" id="searchInput" placeholder="Search layups..." class="ref-input w-full pl-9">
            </div>

            <div class="flex items-center gap-3">
                <button onclick="openCreateDrawer()" class="clt-btn-brand flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Layup
                </button>
            </div>
        </div>

        <div class="w-full">
            <table class="w-full text-left border-collapse ref-table transition-colors">
                <thead>
                    <tr>
                        <th class="w-16 pl-4">No</th>
                        <th class="w-24">ID</th>
                        <th>Layup Name</th>
                        <th>Supplier</th>
                        <th class="w-24 text-center pr-4">Action</th>
                    </tr>
                </thead>
                <tbody id="layupsTableBody">
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between mt-6 px-4">
            <p class="text-[11px] text-[rgb(var(--text-soft))]" id="paginationInfo">Showing 0 layups</p>
            <div class="flex gap-1">
                <button id="prevPageBtn" disabled
                    class="clt-page-btn text-[rgb(var(--text-soft))] px-4 border border-[rgb(var(--line-color))] rounded-lg hover:border-[rgb(var(--text-soft))] transition-colors">Prev</button>
                <button id="nextPageBtn" disabled
                    class="clt-page-btn text-[rgb(var(--text-soft))] px-4 border border-[rgb(var(--line-color))] rounded-lg hover:border-[rgb(var(--text-soft))] transition-colors">Next</button>
            </div>
        </div>
    </div>

    <div id="layupDrawer" class="fixed inset-0 z-50 overflow-hidden hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm opacity-0 transition-opacity" id="drawerBackdrop"
            onclick="closeDrawer()"></div>
        <div class="absolute inset-y-0 right-0 max-w-sm w-full translate-x-full transition-transform" id="drawerPanel">
            <div
                class="h-full bg-[rgb(var(--app-bg))] border-l border-[rgb(var(--line-color))] flex flex-col transition-colors">
                <div class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center">
                    <h3 class="text-[rgb(var(--text-main))] font-semibold" id="drawerTitle">Add Layup</h3>
                    <button onclick="closeDrawer()"
                        class="text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition-colors"><svg
                            class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="p-6 flex-1 overflow-y-auto">
                    <form id="layupsForm" onsubmit="saveLayup(event)" class="space-y-4">
                        <input type="hidden" id="layupId">
                        <div>
                            <label
                                class="block text-xs font-semibold text-[rgb(var(--text-main))] mb-2">Supplier</label>
                            <select id="formSupplierId" required class="ref-input w-full"></select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[rgb(var(--text-main))] mb-2">Layup
                                Name</label>
                            <input type="text" id="layupName" required class="ref-input w-full"
                                placeholder="e.g. 5-Layer Layout">
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

    <div id="detailModal" class="fixed inset-0 z-[100] overflow-hidden hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm opacity-0 transition-opacity" id="detailBackdrop"
            onclick="closeDetailModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-[rgb(var(--app-bg))] border border-[rgb(var(--line-color))] rounded-2xl w-full max-w-lg scale-95 opacity-0 transition-all duration-300 shadow-2xl pointer-events-auto"
                id="detailPanel">
                <div class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center bg-black/5">
                    <h3 class="text-lg font-bold text-[rgb(var(--text-main))]" id="detailTitle">Layup Specifications
                    </h3>
                    <button onclick="closeDetailModal()"
                        class="text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition-colors"><svg
                            class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="p-8">
                    <div id="detailContent" class="space-y-6">
                    </div>
                </div>
                <div class="p-6 border-t border-[rgb(var(--line-color))] flex justify-end">
                    <button onclick="closeDetailModal()" class="clt-btn-brand px-8 py-2.5">Close View</button>
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
                    <h3 class="text-xl font-bold text-[rgb(var(--text-main))] mb-2">Confirm Delete Layup?</h3>
                    <p class="text-sm text-[rgb(var(--text-soft))] mb-8">This action is permanent and cannot be undone.
                        All associated technical layers will be removed.</p>
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
            let currentPage = 1, searchQuery = '', selectedSupplierId = '', isEdit = false;
            let layupsRequestId = 0;
            let layupsSuppliersRequestId = 0;
            async function fetchLayups(page = 1) {
                currentPage = page; const tb = document.getElementById('layupsTableBody');
                const cacheKey = `clt_layup_s${selectedSupplierId}_pg${page}_q${searchQuery}`;
                const cached = sessionStorage.getItem(cacheKey);
                const requestId = ++layupsRequestId;

                if (cached) {
                    try {
                        const result = JSON.parse(cached);
                        if (!Array.isArray(result.data)) { sessionStorage.removeItem(cacheKey); throw new Error('stale_cache'); }
                        if (requestId !== layupsRequestId) return;
                        renderLayupsDom(tb, result.data, result.metadata);
                        fetchLayupsData(page, tb, cacheKey, false);
                    } catch (e) {
                        fetchLayupsData(page, tb, cacheKey, true);
                    }
                } else {
                    tb.innerHTML = Array(5).fill(`
                                <tr>
                                    <td colspan="5" class="py-4 px-4">
                                        <div class="flex items-center gap-4 w-full">
                                            <div class="h-6 w-1/4 skeleton-loader rounded"></div>
                                            <div class="h-6 w-1/2 skeleton-loader rounded"></div>
                                            <div class="h-6 w-1/4 skeleton-loader rounded"></div>
                                        </div>
                                    </td>
                                </tr>
                            `).join('');
                    fetchLayupsData(page, tb, cacheKey, true);
                }
            }

            async function fetchLayupsData(page, tb, cacheKey, updateUI) {
                const requestId = layupsRequestId;
                const supplierAtRequest = selectedSupplierId;
                const queryAtRequest = searchQuery;
                try {
                    const sId = supplierAtRequest || 0;
                    const url = `{{ url('/api/v1/layups') }}/${sId}?page=${page}&q=${encodeURIComponent(queryAtRequest)}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const result = await res.json(); if (!result.success) throw new Error(result.message);
                    const items = Array.isArray(result.data) ? result.data : [];
                    const meta = result.metadata || { current_page: 1, per_page: 10, total_page: 1, total_row: 0 };
                    if (requestId !== layupsRequestId || supplierAtRequest !== selectedSupplierId || queryAtRequest !== searchQuery) return;
                    sessionStorage.setItem(cacheKey, JSON.stringify({ ...result, data: items, metadata: meta }));
                    if (!document.getElementById('layupsTableBody')) return;
                    renderLayupsDom(tb, items, meta);
                } catch (e) { if (updateUI && requestId === layupsRequestId && document.getElementById('layupsTableBody')) tb.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-red-500">${e.message}</td></tr>`; }
            }

            function renderLayupsDom(tb, data, meta) {
                if (!Array.isArray(data) || data.length === 0) {
                    tb.innerHTML = `<tr><td colspan="5" class="text-center py-20 text-[rgb(var(--text-soft))]">
                                <svg class="w-10 h-10 opacity-20 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <span class="block font-medium text-lg">Data Not Found</span>
                                <span class="block text-sm mt-1">No layups found. Try creating a new setup.</span>
                            </td></tr>`;
                    updatePagination(meta); return;
                }
                let startNo = ((meta.current_page - 1) * meta.per_page) + 1;
                tb.innerHTML = data.map((d, i) => `
                            <tr class="hover:bg-[rgba(16,185,129,0.03)] transition-colors group">
                                <td class="font-bold text-[rgb(var(--text-main))] pl-4">${startNo + i}</td>
                                <td class="font-mono text-xs text-[rgb(var(--text-soft))]">#${d.id}</td>
                                <td class="text-[rgb(var(--text-main))] font-medium">${d.name}</td>
                                <td class="text-[rgb(var(--text-soft))]">${d.supplier?.name || 'Unassigned'}</td>
                                <td class="text-center pr-4">
                                    <div class="flex justify-center items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick="viewLayup(${d.id})" title="View Detail" class="p-1 text-[rgb(var(--text-soft))] hover:text-blue-500 transition rounded"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                        <button onclick="openEditDrawer(${d.id}, '${d.name.replace(/'/g, "\\'")}', ${d.supplier?.id})" title="Edit" class="p-1 text-[rgb(var(--text-soft))] hover:text-[rgb(var(--brand))] transition rounded"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                                        <button onclick="deleteLayup(${d.id})" title="Delete" class="p-1 text-[rgb(var(--text-soft))] hover:text-red-500 transition rounded"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                updatePagination(meta);
            }

            function clearLayupsCache() {
                Object.keys(sessionStorage).filter(k => k.startsWith('clt_layup_')).forEach(k => sessionStorage.removeItem(k));
            }

            function setSupplierFilter(id, btnRef) {
                selectedSupplierId = id;
                document.querySelectorAll('#supplierTabs .top-pill').forEach(el => el.classList.remove('active'));
                if (btnRef) btnRef.classList.add('active');
                fetchLayups(1);
            }

            function updatePagination(meta) {
                const prev = document.getElementById('prevPageBtn'), next = document.getElementById('nextPageBtn');
                prev.disabled = meta.current_page <= 1; prev.onclick = () => fetchLayups(meta.current_page - 1);
                next.disabled = meta.current_page >= meta.total_page; next.onclick = () => fetchLayups(meta.current_page + 1);
            }

            async function init() {
                const requestId = ++layupsSuppliersRequestId;
                const r = await fetch('{{ url('/api/v1/suppliers') }}?per_page=10');
                const res = await r.json();
                if (requestId !== layupsSuppliersRequestId) return;

                const tabsContainer = document.getElementById('supplierTabs');
                const formSelect = document.getElementById('formSupplierId');

                (res.data || []).forEach((s, ix) => {
                    const btn = document.createElement('button');
                    btn.className = 'top-pill' + (ix === 0 ? ' active' : '');
                    btn.innerText = s.name;
                    btn.onclick = function () { setSupplierFilter(s.id, this); };
                    tabsContainer.appendChild(btn);

                    if (ix === 0) selectedSupplierId = s.id;


                    formSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
                });

                fetchLayups(1);
            }

            function showDrawer() { const d = document.getElementById('layupDrawer'); d.classList.remove('hidden'); setTimeout(() => { document.getElementById('drawerBackdrop').classList.replace('opacity-0', 'opacity-100'); document.getElementById('drawerPanel').classList.replace('translate-x-full', 'translate-x-0'); }, 10); }
            function closeDrawer() { document.getElementById('drawerBackdrop').classList.replace('opacity-100', 'opacity-0'); document.getElementById('drawerPanel').classList.replace('translate-x-0', 'translate-x-full'); setTimeout(() => { document.getElementById('layupDrawer').classList.add('hidden'); }, 300); }
            function openCreateDrawer() {
                isEdit = false;
                document.getElementById('drawerTitle').innerText = 'Add Layup';
                const form = document.getElementById('layupsForm');
                if (form && typeof form.reset === 'function') {
                    form.reset();
                } else {
                    document.getElementById('layupId').value = '';
                    document.getElementById('layupName').value = '';
                }
                if (selectedSupplierId && document.getElementById('formSupplierId')) {
                    document.getElementById('formSupplierId').value = selectedSupplierId;
                }
                showDrawer();
            }
            function openEditDrawer(id, name, sid) { isEdit = true; document.getElementById('drawerTitle').innerText = 'Edit Layup'; document.getElementById('layupId').value = id; document.getElementById('layupName').value = name; document.getElementById('formSupplierId').value = sid; showDrawer(); }
            async function saveLayup(e) {
                e.preventDefault(); const id = document.getElementById('layupId').value, sid = document.getElementById('formSupplierId').value, name = document.getElementById('layupName').value, btn = document.getElementById('submitBtn'); btn.disabled = true; btn.innerText = 'Saving...';
                const url = isEdit ? `{{ url('/api/v1/layups') }}/${id}/update` : `{{ url('/api/v1/layups') }}`; const method = isEdit ? 'PATCH' : 'POST';
                try { const r = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ name: name, supplier_id: sid }) }); const res = await r.json(); if (!r.ok) throw new Error(res.message); showToast(isEdit ? 'Updated' : 'Created'); closeDrawer(); clearLayupsCache(); fetchLayups(currentPage); } catch (err) { showToast(err.message); } finally { btn.disabled = false; btn.innerText = 'Save'; }
            }
            async function viewLayup(id) {
                const r = await fetch(`{{url('/api/v1/layups')}}/${id}/show`);
                const res = await r.json();
                if (!res.success) { showToast(res.message); return; }

                const data = res.data;
                const content = document.getElementById('detailContent');

                let layersHtml = (data.layers || []).sort((a, b) => a.order - b.order).map(l => `
                            <div class="flex items-center gap-4 p-4 bg-black/5 rounded-xl border border-[rgb(var(--line-color))] hover:border-blue-500/30 transition-all group/item">
                                <div class="h-10 w-10 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold text-sm border border-blue-500/20 shadow-sm">${l.order}</div>
                                <div class="flex-1 grid grid-cols-3 gap-4">
                                     <div>
                                        <p class="text-[10px] font-bold text-[rgb(var(--text-soft))] uppercase tracking-wider mb-0.5">Thickness</p>
                                        <p class="text-sm font-mono font-bold text-[rgb(var(--text-main))]">${l.thickness} mm</p>
                                     </div>
                                     <div>
                                        <p class="text-[10px] font-bold text-[rgb(var(--text-soft))] uppercase tracking-wider mb-0.5">Width</p>
                                        <p class="text-sm font-mono font-bold text-[rgb(var(--text-main))]">${l.width} mm</p>
                                     </div>
                                     <div>
                                        <p class="text-[10px] font-bold text-[rgb(var(--text-soft))] uppercase tracking-wider mb-0.5">Angle</p>
                                        <p class="text-sm font-mono font-bold text-[rgb(var(--text-main))]">${l.angle}°</p>
                                     </div>
                                </div>
                            </div>
                        `).join('');

                if ((data.layers || []).length === 0) layersHtml = '<div class="py-4 text-center border-2 border-dashed border-[rgb(var(--line-color))] rounded-xl text-[rgb(var(--text-soft))] text-xs italic">No layers defined for this layup</div>';

                content.innerHTML = `
                            <div class="flex items-center gap-4 p-4 bg-blue-500/5 border border-blue-500/10 rounded-xl mb-6 shadow-sm">
                                <div class="h-12 w-12 rounded-lg bg-blue-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/20"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg></div>
                                <div>
                                    <p class="text-[9px] font-bold text-blue-500 uppercase tracking-widest mb-0.5">${data.supplier?.name || 'Unassigned'}</p>
                                    <p class="text-xl font-bold text-[rgb(var(--text-main))]">${data.name}</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-[10px] font-bold text-[rgb(var(--text-soft))] uppercase tracking-widest">Configured Layers Hierarchy</h4>
                                    <span class="text-[10px] bg-blue-500/10 text-blue-600 px-2 py-0.5 rounded-full font-bold">${(data.layers || []).length} Layers</span>
                                </div>
                                <div class="max-h-[250px] overflow-y-auto pr-2 custom-scrollbar space-y-2">
                                    ${layersHtml}
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
            function deleteLayup(id) {
                deleteId = id;
                const m = document.getElementById('deleteModal'); m.classList.remove('hidden');
                setTimeout(() => { document.getElementById('deleteBackdrop').classList.replace('opacity-0', 'opacity-100'); document.getElementById('deletePanel').classList.remove('scale-95', 'opacity-0'); }, 50);
                document.getElementById('confirmDeleteBtn').onclick = async () => {
                    const btn = document.getElementById('confirmDeleteBtn');
                    btn.disabled = true; btn.innerText = 'Deleting...';
                    try {
                        const r = await fetch(`{{url('/api/v1/layups')}}/${deleteId}/delete`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' } });
                        const res = await r.json();
                        if (!r.ok) throw new Error(res.message);
                        showToast('Layup Removed');
                        closeDeleteModal();
                        clearLayupsCache(); fetchLayups(currentPage);
                    } catch (e) { showToast(e.message); } finally { btn.disabled = false; btn.innerText = 'Delete'; }
                };
            }
            function closeDeleteModal() {
                document.getElementById('deleteBackdrop').classList.replace('opacity-100', 'opacity-0'); document.getElementById('deletePanel').classList.add('scale-95', 'opacity-0');
                setTimeout(() => { document.getElementById('deleteModal').classList.add('hidden'); }, 300);
            }
            function showToast(m) { const t = document.getElementById('toast'); document.getElementById('toastMsg').innerText = m; t.classList.replace('opacity-0', 'opacity-100'); t.classList.replace('translate-y-4', 'translate-y-0'); setTimeout(() => { t.classList.replace('opacity-100', 'opacity-0'); t.classList.replace('translate-y-0', 'translate-y-4'); }, 3000); }
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', e => {
                searchQuery = e.target.value;
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => fetchLayups(1), 1000);
            })
            document.addEventListener('DOMContentLoaded', init);
        </script>
    @endpush
</x-app-layout>
