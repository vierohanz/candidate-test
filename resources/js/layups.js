/**
 * Layups Management Logic
 */
(function() {
    let currentPage = 1, searchQuery = '', selectedSupplierId = '', isEdit = false;
    let searchTimeout;
    let layupsRequestId = 0;
    let layupsSuppliersRequestId = 0;

    const rootEl = document.querySelector('[data-api-layups]');
    const config = {
        apiLayups: rootEl ? rootEl.dataset.apiLayups : '/api/v1/layups',
        apiSuppliers: rootEl ? rootEl.dataset.apiSuppliers : '/api/v1/suppliers',
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content
    };

    const getSupplierChipClass = (active = false) => [
        'group',
        'inline-flex',
        'min-w-[160px]',
        'h-12',
        'rounded-xl',
        'border',
        'px-4',
        'text-center',
        'transition-all',
        'duration-200',
        'items-center',
        'justify-center',
        'text-[12px]',
        'font-semibold',
        'tracking-[0.02em]',
        active
            ? 'border-emerald-400 bg-emerald-500 text-white shadow-[0_10px_22px_rgba(16,185,129,0.18)]'
            : 'border-[rgb(var(--line-color))] bg-[rgb(var(--card-bg))] text-[rgb(var(--text-soft))] hover:-translate-y-0.5 hover:border-emerald-300/70 hover:text-white'
    ].join(' ');

    const fetchLayups = async (page = 1) => {
        currentPage = page;
        const tb = document.getElementById('layupsTableBody');
        if (!tb) return;

        const cacheKey = `clt_layup_s${selectedSupplierId}_pg${page}_q${searchQuery}`;
        const cached = sessionStorage.getItem(cacheKey);
        const requestId = ++layupsRequestId;

        if (cached) {
            try {
                const result = JSON.parse(cached);
                if (!result || !Array.isArray(result.data)) {
                    sessionStorage.removeItem(cacheKey);
                    throw new Error('stale_cache');
                }
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
    };

    const fetchLayupsData = async (page, tb, cacheKey, updateUI) => {
        const requestId = layupsRequestId;
        const supplierAtRequest = selectedSupplierId;
        const queryAtRequest = searchQuery;
        try {
            const sId = supplierAtRequest || 0;
            const url = `${config.apiLayups}/${sId}?page=${page}&q=${encodeURIComponent(queryAtRequest)}`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const result = await res.json(); 
            if (!result.success) throw new Error(result.message);
            const items = Array.isArray(result.data) ? result.data : [];
            const meta = result.metadata || { current_page: 1, per_page: 10, total_page: 1, total_row: 0 };
            if (requestId !== layupsRequestId || supplierAtRequest !== selectedSupplierId || queryAtRequest !== searchQuery) return;
            sessionStorage.setItem(cacheKey, JSON.stringify({ ...result, data: items, metadata: meta }));
            if (!document.getElementById('layupsTableBody')) return;
            renderLayupsDom(tb, items, meta);
        } catch (e) { 
            if (updateUI && requestId === layupsRequestId && document.getElementById('layupsTableBody')) {
                tb.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-red-500">${e.message}</td></tr>`;
            }
        }
    };

    const renderLayupsDom = (tb, data, meta) => {
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
                    <div class="flex justify-center items-center gap-2 opacity-100 transition-opacity">
                        <div class="flex items-center gap-1">
                            <button onclick="layups.viewLayup(${d.id})" title="View" class="action-btn hover:text-blue-500"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                            <button onclick="layups.openEditDrawer(${d.id}, '${(d.name || '').replace(/'/g, "\\'")}', ${d.supplier_id || "''"})" title="Edit" class="action-btn hover:text-emerald-500"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                            <button onclick="layups.deleteLayup(${d.id})" title="Delete" class="action-btn hover:text-red-500"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </div>
                    </div>
                </td>
            </tr>
        `).join('');
        updatePagination(meta);
    };

    const clearLayupsCache = () => {
        Object.keys(sessionStorage).filter(k => k.startsWith('clt_layup_')).forEach(k => sessionStorage.removeItem(k));
    };

    const setSupplierFilter = (id, btnRef) => {
        selectedSupplierId = id;
        document.querySelectorAll('#supplierTabs button[data-supplier-chip]').forEach(el => {
            el.className = getSupplierChipClass(false);
        });
        if (btnRef) btnRef.className = getSupplierChipClass(true);
        fetchLayups(1);
    };

    const updatePagination = (meta) => {
        const prev = document.getElementById('prevPageBtn'), next = document.getElementById('nextPageBtn');
        if (!prev || !next) return;
        const total = Number(meta.total_row || 0);
        const perPage = Number(meta.per_page || 10);
        const current = Number(meta.current_page || 1);
        const from = total ? ((current - 1) * perPage) + 1 : 0;
        const to = total ? Math.min(current * perPage, total) : 0;
        const info = document.getElementById('paginationInfo');
        if (info) info.innerText = `Showing ${from}-${to} of ${total} layups`;

        prev.disabled = meta.current_page <= 1; 
        prev.onclick = () => fetchLayups(meta.current_page - 1);
        next.disabled = meta.current_page >= meta.total_page; 
        next.onclick = () => fetchLayups(meta.current_page + 1);
    };

    const init = async () => {
        const tabsContainer = document.getElementById('supplierTabs');
        if (!tabsContainer) return;

        const requestId = ++layupsSuppliersRequestId;
        const r = await fetch(`${config.apiSuppliers}?per_page=100`);
        const res = await r.json();
        if (requestId !== layupsSuppliersRequestId) return;

        const formSelect = document.getElementById('formSupplierId');
        
        tabsContainer.innerHTML = '';
        if (formSelect) formSelect.innerHTML = '';

        (res.data || []).forEach((s, ix) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.dataset.supplierChip = 'true';
            btn.className = getSupplierChipClass(ix === 0);
            btn.innerText = s.name;
            btn.onclick = function () { layups.setSupplierFilter(s.id, this); };
            tabsContainer.appendChild(btn);

            if (ix === 0) selectedSupplierId = s.id;

            if (formSelect) {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.text = s.name;
                formSelect.appendChild(opt);
            }
        });

        fetchLayups(1);
    };

    const showDrawer = () => { 
        const d = document.getElementById('layupDrawer'); 
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
        setTimeout(() => { document.getElementById('layupDrawer')?.classList.add('hidden'); }, 300); 
    };

    const openCreateDrawer = () => {
        isEdit = false;
        const title = document.getElementById('drawerTitle');
        if (title) title.innerText = 'Add Layup';
        const form = document.getElementById('layupsForm');
        if (form && typeof form.reset === 'function') {
            form.reset();
        } else {
            const lId = document.getElementById('layupId');
            if (lId) lId.value = '';
            const lName = document.getElementById('layupName');
            if (lName) lName.value = '';
        }
        if (selectedSupplierId) {
            const fsId = document.getElementById('formSupplierId');
            if (fsId) fsId.value = selectedSupplierId;
        }
        showDrawer();
    };

    const openEditDrawer = (id, name, sid) => { 
        isEdit = true; 
        const title = document.getElementById('drawerTitle');
        if (title) title.innerText = 'Edit Layup'; 
        const lId = document.getElementById('layupId');
        if (lId) lId.value = id; 
        const lName = document.getElementById('layupName');
        if (lName) lName.value = name; 
        
        const targetSid = String(sid || selectedSupplierId);
        const selectEl = document.getElementById('formSupplierId');
        if (selectEl) selectEl.value = targetSid;
        
        showDrawer(); 
    };

    const saveLayup = async (e) => {
        e.preventDefault(); 
        const id = document.getElementById('layupId').value, 
              sid = document.getElementById('formSupplierId').value, 
              name = document.getElementById('layupName').value, 
              btn = document.getElementById('submitBtn'); 
        if (!btn) return;
        btn.disabled = true; 
        btn.innerText = 'Saving...';
        const url = isEdit ? `${config.apiLayups}/${id}/update` : config.apiLayups; 
        const method = isEdit ? 'PATCH' : 'POST';
        try { 
            const r = await fetch(url, { 
                method, 
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json', 
                    'X-CSRF-TOKEN': config.csrfToken 
                }, 
                body: JSON.stringify({ name: name, supplier_id: sid }) 
            }); 
            const res = await r.json(); 
            if (!r.ok) throw new Error(res.message); 
            window.showToast(res.message, res.success); 
            closeDrawer(); 
            clearLayupsCache(); 
            fetchLayups(currentPage); 
        } catch (err) { 
            window.showToast(err.message, false); 
        } finally { 
            btn.disabled = false; 
            btn.innerText = 'Save'; 
        }
    };

    const viewLayup = async (id) => {
        const r = await fetch(`${config.apiLayups}/${id}/show`);
        const res = await r.json();
        if (!res.success) { window.showToast(res.message, false); return; }

        const data = res.data;
        const content = document.getElementById('detailContent');
        if (!content) return;

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
    };

    const openDetailModal = () => {
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

    let deleteLayupId = null;
    const deleteLayup = (id) => {
        deleteLayupId = id;
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
                    const r = await fetch(`${config.apiLayups}/${deleteLayupId}/delete`, { 
                        method: 'DELETE', 
                        headers: { 'X-CSRF-TOKEN': config.csrfToken } 
                    });
                    const res = await r.json();
                    if (!r.ok) throw new Error(res.message);
                    window.showToast(res.message, res.success);
                    closeDeleteModal();
                    clearLayupsCache(); 
                    fetchLayups(currentPage);
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

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', e => {
            searchQuery = e.target.value;
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => fetchLayups(1), 1000);
        });
    }

    // Export Namespaced Object
    window.layups = {
        fetch: fetchLayups,
        init: init,
        showDrawer,
        closeDrawer,
        closeDetailModal: window.closeDetailModal,
        closeDeleteModal: window.closeDeleteModal,
        openCreateDrawer,
        openEditDrawer,
        saveLayup,
        deleteLayup,
        viewLayup,
        setSupplierFilter
    };

    if (document.getElementById('layupsTableBody')) {
        init();
    }
})();
