/**
 * Suppliers Management Logic
 */
(function() {
    let currentPage = 1, searchQuery = '', isEdit = false;
    let searchTimeout;

    const rootEl = document.querySelector('[data-api-suppliers]');
    const config = {
        apiSuppliers: rootEl ? rootEl.dataset.apiSuppliers : '/api/v1/suppliers',
        apiExport: rootEl ? rootEl.dataset.apiExport : '/api/v1/export',
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content
    };

    const fetchSuppliers = async (page = 1) => {
        currentPage = page;
        const grid = document.getElementById('suppliersGrid');
        if (!grid) return;

        const cacheKey = `clt_supp_pg${page}_q${searchQuery}`;
        const cached = sessionStorage.getItem(cacheKey);

        if (cached) {
            try {
                const result = JSON.parse(cached);
                if (!result || !Array.isArray(result.data)) {
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
    };

    const fetchSuppliersData = async (page, grid, cacheKey, updateUI) => {
        try {
            const url = `${config.apiSuppliers}?page=${page}&q=${encodeURIComponent(searchQuery)}`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const result = await res.json(); 
            if (!result.success) throw new Error(result.message);
            const items = Array.isArray(result.data) ? result.data : [];
            const meta = result.metadata || { current_page: 1, per_page: 10, total_page: 1, total_row: 0 };
            sessionStorage.setItem(cacheKey, JSON.stringify({ ...result, data: items, metadata: meta }));
            if (document.getElementById('suppliersGrid')) {
                renderSuppliersDom(grid, items, meta);
            }
        } catch (e) { 
            if (updateUI && document.getElementById('suppliersGrid')) {
                grid.innerHTML = `<div class="col-span-full py-10 text-center text-red-500">${e.message}</div>`; 
            }
        }
    };

    const renderSuppliersDom = (grid, data, meta) => {
        if (!Array.isArray(data) || data.length === 0) {
            grid.innerHTML = `<div class="col-span-full py-20 text-center text-[rgb(var(--text-soft))]">
                                    <svg class="w-10 h-10 opacity-20 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    <span class="block font-medium text-lg">Data Not Found</span>
                                    <span class="block text-sm mt-1">No suppliers found. Create a new one to get started.</span>
                                </div>`;
            updatePagination(meta); return;
        }

        grid.innerHTML = data.map(d => `
            <div class="group relative bg-[rgb(28,28,28)] border border-[rgb(var(--line-color))] rounded-3xl p-6 hover:border-[rgb(var(--brand))] hover:shadow-2xl hover:shadow-[rgb(var(--brand))]/10 transition-all duration-500 overflow-hidden">
                <div class="absolute -bottom-6 -right-6 text-white/[0.03] group-hover:text-white/[0.08] group-hover:-translate-x-4 group-hover:-translate-y-4 transition-all duration-700 pointer-events-none">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
                    </svg>
                </div>
                <div class="absolute top-4 right-4 flex opacity-0 group-hover:opacity-100 transition-all duration-300 gap-1.5 translate-y-2 group-hover:translate-y-0 z-10">
                    <button onclick="suppliers.viewSupplier(${d.id})" title="View Hierarchy" class="p-2.5 bg-black/40 text-[rgb(var(--text-soft))] hover:text-blue-400 backdrop-blur-md rounded-2xl transition-all hover:scale-110"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                    <button onclick="suppliers.exportSupplier(${d.id})" title="Export JSON" class="p-2.5 bg-black/40 text-[rgb(var(--text-soft))] hover:text-emerald-400 backdrop-blur-md rounded-2xl transition-all hover:scale-110"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></button>
                    <button onclick="suppliers.triggerImport(${d.id})" title="Import JSON" class="p-2.5 bg-black/40 text-[rgb(var(--text-soft))] hover:text-amber-400 backdrop-blur-md rounded-2xl transition-all hover:scale-110"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg></button>
                    <button onclick="suppliers.openEditDrawer(${d.id}, '${(d.name || '').replace(/'/g, "\\'")}')" title="Edit" class="p-2.5 bg-black/40 text-[rgb(var(--text-soft))] hover:text-white backdrop-blur-md rounded-2xl transition-all hover:scale-110"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                    <button onclick="suppliers.deleteSupplier(${d.id})" title="Delete" class="p-2.5 bg-black/40 text-[rgb(var(--text-soft))] hover:text-red-400 backdrop-blur-md rounded-2xl transition-all hover:scale-110"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                </div>
                <div class="flex flex-col items-center text-center relative z-0">
                    <div class="h-20 w-20 rounded-[2rem] bg-gradient-to-br from-[rgb(var(--brand))] to-emerald-600 text-white flex items-center justify-center text-3xl font-black mb-6 shadow-2xl shadow-emerald-500/20 group-hover:rotate-6 transition-all duration-500">
                        ${d.name ? d.name.charAt(0).toUpperCase() : '?'}
                    </div>
                    <h3 class="text-lg font-bold text-[rgb(var(--text-main))] w-full truncate px-4 group-hover:text-[rgb(var(--brand))] transition-colors duration-300" title="${d.name}">${d.name}</h3>
                    <div class="mt-4 flex flex-col items-center gap-1">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[rgb(var(--text-muted))]">Authorized Partner</span>
                        <span class="px-3 py-1 bg-white/5 rounded-full text-[10px] font-mono text-[rgb(var(--text-soft))] border border-white/5 backdrop-blur-sm">SUPP-${String(d.id).padStart(4, '0')}</span>
                    </div>
                </div>
                <div class="mt-8 pt-5 border-t border-white/5 flex justify-between items-center relative z-0">
                     <div>
                        <p class="text-[10px] text-[rgb(var(--text-muted))] font-bold uppercase tracking-widest">Active Layups</p>
                        <div class="mt-1 flex items-baseline gap-1">
                            <p class="text-xl font-black text-white">${d.layups_count || 0}</p>
                            <span class="text-[9px] text-[rgb(var(--brand))] font-bold">UNITS</span>
                        </div>
                     </div>
                     <div class="h-8 w-8 rounded-xl bg-white/5 flex items-center justify-center group-hover:bg-[rgb(var(--brand))]/20 transition-colors">
                        <svg class="w-4 h-4 text-[rgb(var(--text-muted))] group-hover:text-[rgb(var(--brand))]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                     </div>
                </div>
            </div>
        `).join('');
        updatePagination(meta);
    };

    const updatePagination = (meta) => {
        const total = Number(meta.total_row || 0);
        const perPage = Number(meta.per_page || 10);
        const current = Number(meta.current_page || 1);
        const from = total ? ((current - 1) * perPage) + 1 : 0;
        const to = total ? Math.min(current * perPage, total) : 0;
        const info = document.getElementById('paginationInfo');
        if (info) info.innerText = `Showing ${from}-${to} of ${total} suppliers`;

        const prev = document.getElementById('prevPageBtn'), next = document.getElementById('nextPageBtn');
        if (prev) {
            prev.disabled = meta.current_page <= 1; 
            prev.onclick = () => fetchSuppliers(meta.current_page - 1);
        }
        if (next) {
            next.disabled = meta.current_page >= meta.total_page; 
            next.onclick = () => fetchSuppliers(meta.current_page + 1);
        }
    };

    const showDrawer = () => { 
        const d = document.getElementById('supplierDrawer'); 
        if (!d) return;
        d.classList.remove('hidden'); 
        setTimeout(() => { 
            document.getElementById('drawerBackdrop')?.classList.replace('opacity-0', 'opacity-100'); 
            document.getElementById('drawerPanel')?.classList.replace('translate-x-full', 'translate-x-0'); 
        }, 10); 
    };

    const closeDrawer = () => { 
        document.getElementById('drawerBackdrop')?.classList.replace('opacity-100', 'opacity-0'); 
        document.getElementById('drawerPanel')?.classList.replace('translate-x-0', 'translate-x-full'); 
        setTimeout(() => { document.getElementById('supplierDrawer')?.classList.add('hidden'); }, 300); 
    };

    const openCreateDrawer = () => {
        isEdit = false;
        const title = document.getElementById('drawerTitle');
        const form = document.getElementById('supplierForm');
        const supplierId = document.getElementById('supplierId');
        const supplierName = document.getElementById('supplierName');

        if (title) title.innerText = 'Add Supplier';
        if (form) form.reset();
        if (supplierId) supplierId.value = '';
        if (supplierName && !form) supplierName.value = '';

        const importSection = document.getElementById('drawerImportSection');
        if (importSection) importSection.classList.add('hidden');

        showDrawer();
    };

    const openEditDrawer = (id, name) => {
        isEdit = true;
        const title = document.getElementById('drawerTitle');
        const supplierId = document.getElementById('supplierId');
        const supplierName = document.getElementById('supplierName');

        if (title) title.innerText = 'Edit Supplier';
        if (supplierId) supplierId.value = id;
        if (supplierName) supplierName.value = name;

        const importSection = document.getElementById('drawerImportSection');
        if (importSection) importSection.classList.remove('hidden');

        showDrawer();
    };

    const saveSupplier = async (e) => {
        e.preventDefault(); 
        const id = document.getElementById('supplierId').value, 
              name = document.getElementById('supplierName').value, 
              btn = document.getElementById('submitBtn'); 
        if (!btn) return;
        btn.disabled = true; 
        btn.innerText = 'Saving...';
        const url = isEdit ? `${config.apiSuppliers}/${id}/update` : config.apiSuppliers; 
        const method = isEdit ? 'PATCH' : 'POST';
        try { 
            const r = await fetch(url, { 
                method, 
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json', 
                    'X-CSRF-TOKEN': config.csrfToken 
                }, 
                body: JSON.stringify({ name }) 
            }); 
            const res = await r.json(); 
            if (!r.ok) throw new Error(res.message); 
            window.showToast(res.message, res.success); 
            closeDrawer(); 
            fetchSuppliers(currentPage); 
        } catch (err) { 
            window.showToast(err.message, false); 
        } finally { 
            btn.disabled = false; 
            btn.innerText = 'Save'; 
        }
    };

    let deleteId = null;
    const deleteSupplier = (id) => {
        deleteId = id;
        const m = document.getElementById('deleteModal'); 
        if (!m) return;
        m.classList.remove('hidden');
        setTimeout(() => { 
            document.getElementById('deleteBackdrop')?.classList.replace('opacity-0', 'opacity-100'); 
            document.getElementById('deletePanel')?.classList.remove('scale-95', 'opacity-0'); 
        }, 50);
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        if (confirmBtn) {
            confirmBtn.onclick = async () => {
                confirmBtn.disabled = true; confirmBtn.innerText = 'Deleting...';
                try {
                    const r = await fetch(`${config.apiSuppliers}/${deleteId}/delete`, { 
                        method: 'DELETE', 
                        headers: { 'X-CSRF-TOKEN': config.csrfToken } 
                    });
                    const res = await r.json();
                    if (!r.ok) throw new Error(res.message);
                    window.showToast(res.message, res.success);
                    window.closeDeleteModal();
                    fetchSuppliers(currentPage);
                } catch (e) { 
                    window.showToast(e.message, false); 
                } finally { 
                    confirmBtn.disabled = false; 
                    confirmBtn.innerText = 'Delete'; 
                }
            };
        }
    };

    window.closeDeleteModal = () => {
        document.getElementById('deleteBackdrop')?.classList.replace('opacity-100', 'opacity-0'); 
        document.getElementById('deletePanel')?.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { document.getElementById('deleteModal')?.classList.add('hidden'); }, 300);
    };

    const viewSupplier = async (id) => {
        const r = await fetch(`${config.apiSuppliers}/${id}/show`);
        const res = await r.json();
        if (!res.success) { window.showToast(res.message, false); return; }

        const data = res.data;
        const content = document.getElementById('detailContent');
        if (!content) return;

        let layupsHtml = (data.layups || []).map(l => `
            <div class="flex items-center justify-between p-2.5 bg-black/5 rounded-lg border border-[rgb(var(--line-color))] hover:border-emerald-500/30 transition-colors">
                <span class="text-xs font-semibold text-[rgb(var(--text-main))]">${l.name}</span>
                <span class="text-[9px] font-mono text-[rgb(var(--text-soft))]">#${l.id}</span>
            </div>
        `).join('');

        if ((data.layups || []).length === 0) layupsHtml = '<p class="text-[11px] text-[rgb(var(--text-soft))] italic">No layups configured yet.</p>';

        content.innerHTML = `
            <div class="flex items-center gap-4 p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-xl mb-6">
                <div class="h-12 w-12 rounded-lg bg-emerald-500 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-emerald-500/20">${data.name ? data.name.charAt(0) : '?'}</div>
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
        window.openDetailModal();
    };

    window.openDetailModal = () => {
        const m = document.getElementById('detailModal');
        if (!m) return;
        m.classList.remove('hidden');
        setTimeout(() => {
            document.getElementById('detailBackdrop')?.classList.replace('opacity-0', 'opacity-100');
            document.getElementById('detailPanel')?.classList.remove('scale-95', 'opacity-0');
        }, 50);
    };

    window.closeDetailModal = () => {
        document.getElementById('detailBackdrop')?.classList.replace('opacity-100', 'opacity-0');
        document.getElementById('detailPanel')?.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { document.getElementById('detailModal')?.classList.add('hidden'); }, 300);
    };

    const exportSupplier = async (id) => {
        window.showToast('Preparing export...', true);
        try {
            const r = await fetch(`${config.apiExport}/${id}`);
            if (!r.ok) throw new Error('Export failed');
            const blob = await r.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            const disposition = r.headers.get('content-disposition');
            let filename = `supplier-${id}.json`;
            if (disposition && disposition.indexOf('attachment') !== -1) {
                const m = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                if (m && m[1]) filename = m[1].replace(/['"]/g, '');
            }
            a.href = url; a.download = filename;
            document.body.appendChild(a); a.click(); a.remove();
            window.URL.revokeObjectURL(url);
            window.showToast('Export downloaded', true);
        } catch (e) { window.showToast(e.message, false); }
    };

    let importSupplierId = null;
    let activeImportToken = null;

    const triggerImport = (id) => { 
        importSupplierId = id; 
        document.getElementById('importFile')?.click(); 
    };

    const importFile = document.getElementById('importFile');
    if (importFile) {
        importFile.addEventListener('change', async function (e) {
            if (!e.target.files[0]) return; 
            const fd = new FormData(); 
            fd.append('file', e.target.files[0]);
            try {
                const r = await fetch(`${config.apiSuppliers}/${importSupplierId}/import/scan`, { 
                    method: 'POST', 
                    body: fd, 
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrfToken } 
                });
                const res = await r.json(); 
                if (!r.ok) throw new Error(res.message);
                if (res.data && res.data.auto_confirmed) { 
                    window.showToast(res.message, true); 
                    fetchSuppliers(currentPage); 
                } else {
                    openConflictModal(res.data);
                }
            } catch (err) { 
                window.showToast('Import failed: ' + err.message, false); 
            }
            e.target.value = '';
        });
    }

    const openConflictModal = (data) => {
        activeImportToken = data.import_token;
        const content = document.getElementById('conflictContent');
        if (!content) return;

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
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('conflictBackdrop')?.classList.replace('opacity-0', 'opacity-100');
                document.getElementById('conflictPanel')?.classList.remove('scale-95', 'opacity-0');
            }, 50);
        }
    };

    window.closeConflictModal = () => {
        document.getElementById('conflictBackdrop')?.classList.replace('opacity-100', 'opacity-0'); 
        document.getElementById('conflictPanel')?.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            document.getElementById('importConflictModal')?.classList.add('hidden');
        }, 300);
    };

    const confirmImport = async (strategy) => {
        const btn = strategy === 'skip' ? document.getElementById('importSkipBtn') : document.getElementById('importOverwriteBtn');
        if (!btn) return;
        const originalText = btn.innerText;
        btn.disabled = true; btn.innerText = 'Processing...';

        try {
            const r = await fetch(`${config.apiSuppliers}/${importSupplierId}/import/confirm`, {
                method: 'POST',
                body: JSON.stringify({ import_token: activeImportToken, strategy: strategy }),
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrfToken }
            });
            const res = await r.json(); if (!r.ok) throw new Error(res.message);
            window.showToast(res.message, res.success);
            window.closeConflictModal();
            fetchSuppliers(currentPage);
        } catch (e) { alert(e.message); }
        finally { btn.disabled = false; btn.innerText = originalText; }
    };

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', e => {
            searchQuery = e.target.value;
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => fetchSuppliers(1), 1000);
        });
    }

    // Export Namespaced Object
    window.suppliers = {
        fetch: fetchSuppliers,
        showDrawer,
        closeDrawer,
        openCreateDrawer,
        openEditDrawer,
        saveSupplier,
        deleteSupplier,
        viewSupplier,
        exportSupplier,
        triggerImport,
        confirmImport,
        openConflictModal,
        openDetailModal,
        closeDetailModal
    };

    // Initial check
    if (document.getElementById('suppliersGrid')) {
        fetchSuppliers(1);
    }
})();
