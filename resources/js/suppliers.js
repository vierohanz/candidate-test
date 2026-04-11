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
                                    <span class="block font-medium text-lg">Data Not Found</span>
                                    <span class="block text-sm mt-1">No suppliers found. Create a new one to get started.</span>
                                </div>`;
            updatePagination(meta); return;
        }

        grid.innerHTML = data.map(d => `
            <div class="group relative bg-[#1E1E1E] border border-white/5 rounded-2xl p-8 transition-all duration-300 hover:border-emerald-500/30 hover:translate-y-[-2px] flex flex-col h-full shadow-lg overflow-hidden">
                
                <div class="flex flex-col items-center text-center relative z-10">
                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-emerald-800/20 text-emerald-500 border border-emerald-500/20 flex items-center justify-center text-3xl font-black mb-6 shadow-inner">
                        ${d.name ? d.name.charAt(0).toUpperCase() : '?'}
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 truncate w-full" title="${d.name}">${d.name}</h3>
                    <p class="text-[10px] font-mono text-[rgb(var(--text-muted))] uppercase tracking-[0.2em] mb-8 px-4 py-1.5 bg-white/5 rounded-full inline-block">SUPP-${String(d.id).padStart(4, '0')}</p>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex items-center justify-between">
                         <div class="flex items-center gap-2">
                             <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                             <span class="text-[11px] font-bold text-[rgb(var(--text-soft))] uppercase tracking-tight">Active Layups</span>
                         </div>
                         <span class="text-sm font-black text-white">${d.layups_count || 0} UNITS</span>
                    </div>
                    <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500/80 shadow-[0_0_10px_rgba(16,185,129,0.3)] transition-all duration-1000" style="width: ${Math.min(100, (d.layups_count || 0) * 10)}%"></div>
                    </div>
                </div>

                <div class="mt-auto pt-4 border-t border-white/5 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <button data-bounce-click onclick="suppliers.viewSupplier(${d.id})" title="View" class="h-8 w-8 rounded-lg text-emerald-400 hover:text-emerald-300 flex items-center justify-center transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="3" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="3" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        <button data-bounce-click onclick="suppliers.triggerImport(${d.id})" title="Import" class="h-8 w-8 rounded-lg text-[rgb(var(--text-soft))] hover:text-amber-400 flex items-center justify-center transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </button>
                        <button data-bounce-click onclick="suppliers.exportSupplier(${d.id})" title="Export" class="h-8 w-8 rounded-lg text-[rgb(var(--text-soft))] hover:text-emerald-400 flex items-center justify-center transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </button>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button data-bounce-click onclick="suppliers.openEditDrawer(${d.id}, '${(d.name || '').replace(/'/g, "\\'")}')" title="Edit" class="h-8 w-8 rounded-lg text-[rgb(var(--text-muted))] hover:text-sky-400 flex items-center justify-center transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button data-bounce-click onclick="suppliers.deleteSupplier(${d.id})" title="Delete" class="h-8 w-8 rounded-lg text-[rgb(var(--text-muted))] hover:text-red-400 flex items-center justify-center transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
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
            <div class="flex items-center justify-between px-3 py-2.5 bg-black/5 rounded-lg border border-[rgb(var(--line-color))] hover:border-emerald-500/30 transition-colors">
                <span class="text-sm font-semibold text-[rgb(var(--text-main))]">${l.name}</span>
                <span class="text-[11px] font-mono text-[rgb(var(--text-soft))]">#${l.id}</span>
            </div>
        `).join('');

        if ((data.layups || []).length === 0) layupsHtml = '<p class="text-sm text-[rgb(var(--text-soft))] italic">No layups configured yet.</p>';

        const layupsScrollStyle = (data.layups || []).length > 5
            ? 'max-height: 286px; overflow-y: auto;'
            : '';

        content.innerHTML = `
            <div class="flex items-center gap-4 p-5 bg-emerald-500/5 border border-emerald-500/10 rounded-xl mb-6">
                <div class="h-14 w-14 rounded-xl bg-emerald-500 flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-emerald-500/20">${data.name ? data.name.charAt(0) : '?'}</div>
                <div>
                    <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-[0.22em] mb-1">Supplier Identity</p>
                    <p class="text-[1.75rem] leading-none font-bold text-[rgb(var(--text-main))]">${data.name}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-[rgb(var(--text-soft))] uppercase tracking-[0.18em]">Linked CLT Layups</h4>
                    <span class="text-[11px] bg-emerald-500/10 text-emerald-400 px-2.5 py-1 rounded-full font-bold">${(data.layups || []).length} Sets</span>
                </div>
                <div class="pr-2 custom-scrollbar space-y-2" style="${layupsScrollStyle}">
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

            data.layers.conflicts.forEach(c => {
                const isDiff = (f) => c.incoming[f] != c.existing[f];
                const key = `${c.layup_name}_${c.layer_order}`;
                
                html += `
                    <div class="border border-[rgb(var(--line-color))] rounded-xl overflow-hidden shadow-sm transition-colors group">
                        <div class="bg-black/5 px-4 py-2 border-b border-[rgb(var(--line-color))] flex justify-between items-center">
                            <span class="text-[11px] font-bold text-[rgb(var(--text-main))] uppercase tracking-wide">Layup: ${c.layup_name} — Order #${c.layer_order}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-[9px] font-black text-[rgb(var(--text-soft))] uppercase tracking-widest">Select Fields to Update</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2">
                            <div class="p-4 border-r border-[rgb(var(--line-color))] bg-red-500/[0.02]">
                                <span class="text-[8px] font-bold text-red-500 uppercase tracking-widest block mb-3">System (Current Record)</span>
                                <div class="space-y-4 text-xs text-[rgb(var(--text-main))]">
                                    <div class="flex justify-between items-center h-5"><span>Thickness</span> <span class="font-mono">${c.existing.thickness} mm</span></div>
                                    <div class="flex justify-between items-center h-5"><span>Width</span> <span class="font-mono">${c.existing.width} mm</span></div>
                                    <div class="flex justify-between items-center h-5"><span>Angle</span> <span class="font-mono">${c.existing.angle}°</span></div>
                                </div>
                            </div>
                            <div class="p-4 bg-emerald-500/[0.02]">
                                <span class="text-[8px] font-bold text-emerald-500 uppercase tracking-widest block mb-3">Incoming (New Values)</span>
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
        const btnIdMap = {
            'skip': 'importSkipBtn',
            'overwrite': 'importOverwriteBtn',
            'granular': 'importMergeBtn',
            'duplicate': 'importDuplicateBtn'
        };
        const btn = document.getElementById(btnIdMap[strategy]);
        if (!btn) return;

        const resolutions = {};
        if (strategy === 'granular') {
            document.querySelectorAll('input[data-res-key]:checked').forEach(cb => {
                const key = cb.getAttribute('data-res-key');
                const field = cb.getAttribute('data-res-field');
                if (!resolutions[key]) resolutions[key] = [];
                resolutions[key].push(field);
            });
            if (Object.keys(resolutions).length === 0) return window.showToast('Please select at least one numeric change to apply', false);
        }

        const originalText = btn.innerText;
        btn.disabled = true; btn.innerText = 'Syncing...';

        try {
            const r = await fetch(`${config.apiSuppliers}/${importSupplierId}/import/confirm`, {
                method: 'POST',
                body: JSON.stringify({
                    import_token: activeImportToken,
                    strategy: strategy === 'granular' ? 'merge' : strategy,
                    resolutions: resolutions
                }),
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrfToken }
            });
            const res = await r.json(); if (!r.ok) throw new Error(res.message);
            window.showToast(res.message, res.success);
            window.closeConflictModal();
            fetchSuppliers(currentPage);
        } catch (e) { window.showToast(e.message, false); }
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
        closeDeleteModal: window.closeDeleteModal,
        closeConflictModal: window.closeConflictModal,
        openCreateDrawer,
        openEditDrawer,
        saveSupplier,
        deleteSupplier,
        viewSupplier,
        exportSupplier,
        triggerImport,
        confirmImport,
        openConflictModal,
        openDetailModal: window.openDetailModal,
        closeDetailModal: window.closeDetailModal
    };

    // Initial check
    if (document.getElementById('suppliersGrid')) {
        fetchSuppliers(1);
    }
})();
