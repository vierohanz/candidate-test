/**
 * Layers Management Logic
 */
(function() {
    let currentPage = 1, selectedSupplierId = '', selectedLayupId = '', isEdit = false;
    let layersRequestId = 0;
    let layerSupplierRequestId = 0;
    let layerLayupRequestId = 0;

    const rootEl = document.querySelector('[data-api-layers]');
    const config = {
        apiLayers: rootEl ? rootEl.dataset.apiLayers : '/api/v1/layers',
        apiSuppliers: rootEl ? rootEl.dataset.apiSuppliers : '/api/v1/suppliers',
        apiLayups: rootEl ? rootEl.dataset.apiLayups : '/api/v1/layups',
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content
    };

    const fetchLayers = async (page = 1) => {
        currentPage = page; 
        const tb = document.getElementById('layersTableBody');
        if (!tb) return;
        const requestId = ++layersRequestId;
        
        if (!selectedLayupId) {
            tb.innerHTML = `<tr><td colspan="7" class="text-center py-20 text-[rgb(var(--text-soft))]">Please select a valid layup to view layers.</td></tr>`;
            updatePagination({ current_page: 1, total_page: 1, total_row: 0, per_page: 10 });
            return;
        }
        
        const cacheKey = `clt_layer_s${selectedSupplierId}_l${selectedLayupId}_pg${page}`;
        const cached = sessionStorage.getItem(cacheKey);

        if (cached) {
            try {
                const result = JSON.parse(cached);
                if (!result || !Array.isArray(result.data)) { 
                    sessionStorage.removeItem(cacheKey); 
                    throw new Error('stale_cache'); 
                }
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
    };

    const fetchLayersData = async (page, tb, cacheKey, updateUI) => {
        const requestId = layersRequestId;
        const supplierAtRequest = selectedSupplierId;
        const layupAtRequest = selectedLayupId;
        try {
            const url = `${config.apiLayers}/${supplierAtRequest}/${layupAtRequest}?page=${page}`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const result = await res.json(); 
            if (!result.success) throw new Error(result.message);
            const items = Array.isArray(result.data) ? result.data : [];
            const meta = result.metadata || { current_page: 1, per_page: 10, total_page: 1, total_row: 0 };
            if (requestId !== layersRequestId || supplierAtRequest !== selectedSupplierId || layupAtRequest !== selectedLayupId) return;
            sessionStorage.setItem(cacheKey, JSON.stringify({ ...result, data: items, metadata: meta }));
            if (!document.getElementById('layersTableBody')) return;
            renderLayersDom(tb, items, meta);
        } catch (e) { 
            if (updateUI && requestId === layersRequestId && document.getElementById('layersTableBody')) {
                tb.innerHTML = `<tr><td colspan="7" class="text-center py-10 text-red-500">${e.message}</td></tr>`;
            }
        }
    };

    const renderLayersDom = (tb, data, meta) => {
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
                    <div class="flex justify-center items-center gap-2 opacity-100 transition-opacity">
                        <div class="flex items-center gap-1">
                            <button onclick="layers.viewLayer(${d.id})" title="View" class="action-btn hover:text-blue-500"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                            <button onclick="layers.openEditDrawer(${JSON.stringify(d).replace(/"/g, "&quot;")})" title="Edit" class="action-btn hover:text-emerald-500"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                            <button onclick="layers.deleteLayer(${d.id})" title="Delete" class="action-btn hover:text-red-500"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </div>
                    </div>
                </td>
            </tr>
        `).join('');
        updatePagination(meta);
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
        if (info) info.innerText = `Showing ${from}-${to} of ${total} layers`;

        prev.disabled = meta.current_page <= 1; 
        prev.onclick = () => fetchLayers(meta.current_page - 1);
        next.disabled = meta.current_page >= meta.total_page; 
        next.onclick = () => fetchLayers(meta.current_page + 1);
    };

    const clearLayersCache = () => {
        Object.keys(sessionStorage).filter(k => k.startsWith('clt_layer_')).forEach(k => sessionStorage.removeItem(k));
        sessionStorage.removeItem('clt_layer_suppliers');
    };

    const loadSuppliers = async () => {
        const requestId = ++layerSupplierRequestId;
        const cacheKey = 'clt_layer_suppliers';
        let res;
        const cached = sessionStorage.getItem(cacheKey);
        if (cached) {
            res = JSON.parse(cached);
            fetch(`${config.apiSuppliers}?per_page=100`)
                .then(r => r.json()).then(fresh => sessionStorage.setItem(cacheKey, JSON.stringify(fresh))).catch(() => { });
        } else {
            const r = await fetch(`${config.apiSuppliers}?per_page=100`);
            res = await r.json();
            sessionStorage.setItem(cacheKey, JSON.stringify(res));
        }
        const filter = document.getElementById('supplierFilter');
        if (requestId !== layerSupplierRequestId) return;
        if (!filter) return;
        filter.innerHTML = '';
        (res.data || []).forEach((s, ix) => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.text = s.name;
            filter.appendChild(opt);
            if (ix === 0) selectedSupplierId = s.id;
        });
    };

    const loadLayups = async (sid) => {
        const requestId = ++layerLayupRequestId;
        const cacheKey = `clt_layer_layups_s${sid}`;
        let res;
        const cached = sessionStorage.getItem(cacheKey);
        if (cached) {
            res = JSON.parse(cached);
            fetch(`${config.apiLayups}/${sid}?per_page=100`)
                .then(r => r.json()).then(fresh => sessionStorage.setItem(cacheKey, JSON.stringify(fresh))).catch(() => { });
        } else {
            const r = await fetch(`${config.apiLayups}/${sid}?per_page=100`);
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
            const opt1 = document.createElement('option');
            opt1.value = l.id; opt1.text = l.name;
            filter.appendChild(opt1);
            
            const opt2 = document.createElement('option');
            opt2.value = l.id; opt2.text = l.name;
            formSelect.appendChild(opt2);

            if (ix === 0) selectedLayupId = l.id;
        });
        fetchLayers(1);
    };

    const handleSupplierChange = () => {
        const sFilter = document.getElementById('supplierFilter');
        if (sFilter) {
            selectedSupplierId = sFilter.value;
            loadLayups(selectedSupplierId);
        }
    };

    const handleLayupChange = () => {
        const lFilter = document.getElementById('layupFilter');
        if (lFilter) {
            selectedLayupId = lFilter.value;
            fetchLayers(1);
        }
    };

    const showDrawer = () => { 
        const d = document.getElementById('layerDrawer'); 
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
        setTimeout(() => { document.getElementById('layerDrawer')?.classList.add('hidden'); }, 300); 
    };

    const openCreateDrawer = () => {
        isEdit = false;
        const title = document.getElementById('drawerTitle');
        if (title) title.innerText = 'New Technical Layer';
        
        const fields = ['layerId', 'formOrder', 'formAngle', 'formThickness', 'formWidth'];
        fields.forEach(f => {
            const el = document.getElementById(f);
            if (el) el.value = '';
        });

        const formLayupId = document.getElementById('formLayupId');
        if (formLayupId && selectedLayupId) formLayupId.value = selectedLayupId;

        showDrawer();
    };

    const openEditDrawer = (l) => {
        isEdit = true;
        const title = document.getElementById('drawerTitle');
        if (title) title.innerText = 'Edit Layer';
        
        const mapping = {
            layerId: l.id,
            formLayupId: l.layup_id,
            formOrder: l.layer_order,
            formAngle: l.angle,
            formThickness: l.thickness,
            formWidth: l.width
        };

        Object.keys(mapping).forEach(k => {
            const el = document.getElementById(k);
            if (el) el.value = mapping[k] ?? '';
        });

        showDrawer();
    };

    const saveLayer = async (e) => { 
        e.preventDefault(); 
        const lid = document.getElementById('formLayupId')?.value, 
              id = document.getElementById('layerId')?.value, 
              btn = document.getElementById('submitBtn'); 
        if (!lid || !btn) return;
        
        const data = { 
            layup_id: lid, 
            layer_order: document.getElementById('formOrder')?.value, 
            thickness: document.getElementById('formThickness')?.value, 
            width: document.getElementById('formWidth')?.value, 
            angle: document.getElementById('formAngle')?.value 
        }; 
        btn.disabled = true; 
        btn.innerText = 'Saving...'; 
        const url = isEdit ? `${config.apiLayers}/${id}/update` : config.apiLayers; 
        const method = isEdit ? 'PUT' : 'POST'; 
        try { 
            const r = await fetch(url, { 
                method, 
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json', 
                    'X-CSRF-TOKEN': config.csrfToken 
                }, 
                body: JSON.stringify(data) 
            }); 
            const res = await r.json(); 
            if (!r.ok) throw new Error(res.message); 
            window.showToast(res.message, res.success); 
            closeDrawer(); 
            clearLayersCache(); 
            fetchLayers(currentPage); 
        } catch (err) { 
            window.showToast(err.message, false); 
        } finally { 
            btn.disabled = false; 
            btn.innerText = 'Save'; 
        } 
    };

    const viewLayer = async (id) => {
        const r = await fetch(`${config.apiLayers}/${id}/show`);
        const res = await r.json();
        if (!res.success) { window.showToast(res.message, false); return; }

        const data = res.data;
        const content = document.getElementById('detailContent');
        if (!content) return;
        content.innerHTML = `
            <div class="flex items-center gap-4 p-4 bg-brand/5 border border-brand/10 rounded-xl">
                <div class="h-12 w-12 rounded-lg bg-[rgb(var(--brand))] flex items-center justify-center text-white font-bold text-xl">${data.layer_order}</div>
                <div>
                    <p class="text-[10px] font-bold text-[rgb(var(--brand))] uppercase tracking-widest">Sequence Order</p>
                    <p class="text-xl font-bold text-[rgb(var(--text-main))]">Order Reference #${data.layer_order}</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3 mt-4">
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
            <div class="p-4 bg-black/5 rounded-xl border border-[rgb(var(--line-color))] mt-4">
                <p class="text-[9px] font-bold text-[rgb(var(--text-soft))] uppercase tracking-widest mb-2">Hierarchy Context</p>
                <div class="space-y-1 text-xs">
                    <div class="flex justify-between"><span>Layup Name</span> <span class="font-semibold text-[rgb(var(--text-main))]">${data.layup?.name}</span></div>
                    <div class="flex justify-between"><span>Supplier</span> <span class="font-semibold text-[rgb(var(--text-main))]">${data.layup?.supplier_name}</span></div>
                </div>
            </div>
        `;
        openDetailModal();
    };

    const openDetailModal = () => {
        const m = document.getElementById('detailModal'); if(!m) return;
        m.classList.remove('hidden');
        setTimeout(() => { document.getElementById('detailBackdrop')?.classList.replace('opacity-0', 'opacity-100'); document.getElementById('detailPanel')?.classList.remove('scale-95', 'opacity-0'); }, 50);
    };

    const closeDetailModal = () => {
        document.getElementById('detailBackdrop')?.classList.replace('opacity-100', 'opacity-0'); document.getElementById('detailPanel')?.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { document.getElementById('detailModal')?.classList.add('hidden'); }, 300);
    };

    let deleteLayerId = null;
    const deleteLayer = (id) => {
        deleteLayerId = id;
        const m = document.getElementById('deleteModal'); if(!m) return;
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
                    const r = await fetch(`${config.apiLayers}/${deleteLayerId}/delete`, { 
                        method: 'DELETE', 
                        headers: { 'X-CSRF-TOKEN': config.csrfToken } 
                    });
                    const res = await r.json();
                    if (!r.ok) throw new Error(res.message);
                    window.showToast(res.message, res.success);
                    closeDeleteModal();
                    clearLayersCache(); 
                    fetchLayers(currentPage);
                } catch (e) { 
                    window.showToast(e.message, false); 
                } finally { 
                    confirmBtn.disabled = false; 
                    confirmBtn.innerText = 'Delete'; 
                }
            };
        }
    };

    const closeDeleteModal = () => {
        document.getElementById('deleteBackdrop')?.classList.replace('opacity-100', 'opacity-0'); 
        document.getElementById('deletePanel')?.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { document.getElementById('deleteModal')?.classList.add('hidden'); }, 300);
    };

    const initLayers = async () => {
        await loadSuppliers();
        if (selectedSupplierId) await loadLayups(selectedSupplierId);
    };

    // Export Namespaced Object
    window.layers = {
        fetch: fetchLayers,
        init: initLayers,
        showDrawer,
        closeDrawer,
        openCreateDrawer,
        openEditDrawer,
        saveLayer,
        deleteLayer,
        viewLayer,
        handleSupplierChange,
        handleLayupChange,
        closeDetailModal,
        closeDeleteModal
    };

    if (document.getElementById('layersTableBody')) {
        initLayers();
    }
})();
