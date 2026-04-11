<x-app-layout title="Export Center">
    <div class="space-y-6">
        <section class="clt-card p-6">
            <p class="text-[11px] font-black uppercase tracking-[0.24em] text-[rgb(var(--text-soft))]">Export Center</p>
            <h2 class="mt-3 text-3xl font-black text-[rgb(var(--text-main))]">Supplier Snapshots</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-[rgb(var(--text-soft))]">Download one supplier at a time
                with all the layouts and connected layers in JSON format ready to use for demo or backup.</p>
        </section>

        <section class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3" id="exportSupplierGrid">
            <div class="clt-card p-8 text-center text-[rgb(var(--text-soft))]">Loading suppliers...</div>
        </section>

        <div
            class="clt-pagination-shell flex flex-col gap-4 rounded-[18px] px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="text-sm text-[rgb(var(--text-soft))]">
                <span id="exportPaginationInfo" class="font-medium text-[rgb(var(--text-muted))]">Showing 0
                    suppliers</span>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <button id="exportPrevBtn" class="clt-page-btn px-3" disabled>PREV</button>
                <div id="exportPageNumbers" class="flex flex-wrap gap-1.5"></div>
                <button id="exportNextBtn" class="clt-page-btn px-3" disabled>NEXT</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let exportPage = 1;
            let exportPerPage = 10;
            const exportCache = new Map();

            function getExportKey(page = 1) {
                return JSON.stringify({ page: Number(page), perPage: Number(exportPerPage) });
            }

            function renderExportSuppliers(items, meta) {
                const grid = document.getElementById('exportSupplierGrid');
                if (!items.length) {
                    grid.innerHTML = '<div class="clt-card col-span-full p-10 text-center text-[rgb(var(--text-soft))]">No suppliers found for export.</div>';
                } else {
                    grid.innerHTML = items.map((item) => `
                        <div class="clt-card p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[rgb(var(--text-soft))]">Supplier</p>
                                    <h3 class="mt-3 text-2xl font-black text-[rgb(var(--text-main))]">${item.name}</h3>
                                </div>
                                <div class="grid h-12 w-12 place-items-center rounded-full bg-[linear-gradient(135deg,rgb(var(--brand))_0%,rgb(var(--brand-alt))_100%)] text-lg font-black text-white">${item.name?.charAt(0)?.toUpperCase() || '?'}</div>
                            </div>
                            <p class="mt-4 text-sm leading-6 text-[rgb(var(--text-soft))]">Download a clean supplier snapshot with all layups and layers included.</p>
                            <div class="mt-6 flex items-center justify-between border-t border-[rgba(var(--line-color),0.06)] pt-5">
                                <span class="text-xs font-semibold uppercase tracking-[0.18em] text-[rgb(var(--text-soft))]">JSON export</span>
                                <button onclick="silentExport(${item.id})" class="clt-btn-brand">Export</button>
                            </div>
                        </div>
                    `).join('');
                }
                updateExportPagination(meta || {});
            }

            async function silentExport(id) {
                window.showToast('Preparing snapshot...', true);
                try {
                    const r = await fetch(`/api/v1/suppliers/${id}/export`);
                    if (!r.ok) throw new Error('Download failed');
                    const blob = await r.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a'); 
                    a.href = url;
                    const disposition = r.headers.get('content-disposition');
                    let filename = `supplier-${id}-export.json`;
                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        const m = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                        if (m && m[1]) filename = m[1].replace(/['"]/g, '');
                    }
                    a.download = filename; document.body.appendChild(a); a.click(); a.remove();
                    window.URL.revokeObjectURL(url);
                    window.showToast('Snapshot downloaded', true);
                } catch (e) { window.showToast(e.message, false); }
            }

            async function fetchExportSuppliers(page = 1, options = {}) {
                exportPage = page;
                const key = getExportKey(page);
                const cached = exportCache.get(key);
                if (!options.force && cached) {
                    renderExportSuppliers(cached.items, cached.meta);
                    return;
                }
                document.getElementById('exportSupplierGrid').innerHTML = '<div class="clt-card col-span-full p-10 text-center text-[rgb(var(--text-soft))]">Loading suppliers...</div>';
                try {
                    const response = await fetch(`/api/v1/suppliers?page=${page}&per_page=${exportPerPage}`, { headers: { Accept: 'application/json' } });
                    const result = await response.json();
                    if (!response.ok || !result.success) throw new Error(result.message || 'Failed to fetch suppliers');
                    const items = Array.isArray(result.data) ? result.data : [];
                    const meta = result.metadata || {};
                    exportCache.set(key, { items, meta });
                    renderExportSuppliers(items, meta);
                } catch (error) {
                    document.getElementById('exportSupplierGrid').innerHTML = `<div class="clt-card col-span-full p-10 text-center text-rose-500">${error.message}</div>`;
                }
            }

            function updateExportPagination(meta) {
                const current = Number(meta.current_page || 1);
                const totalPages = Number(meta.total_page || 1);
                const total = Number(meta.total_row || 0);
                const perPage = Number(meta.per_page || exportPerPage);
                const from = total ? ((current - 1) * perPage) + 1 : 0;
                const to = total ? Math.min(current * perPage, total) : 0;
                document.getElementById('exportPaginationInfo').innerText = `Showing ${from}-${to} of ${total} suppliers`;
                document.getElementById('exportPrevBtn').disabled = current <= 1;
                document.getElementById('exportNextBtn').disabled = current >= totalPages;
                const pageNumbers = document.getElementById('exportPageNumbers');
                pageNumbers.innerHTML = '';
                for (let i = 1; i <= totalPages; i++) {
                    pageNumbers.innerHTML += `<button onclick="fetchExportSuppliers(${i})" class="clt-page-btn ${i === current ? 'active' : ''}">${i}</button>`;
                }
                document.getElementById('exportPrevBtn').onclick = () => { if (current > 1) fetchExportSuppliers(current - 1); };
                document.getElementById('exportNextBtn').onclick = () => { if (current < totalPages) fetchExportSuppliers(current + 1); };
            }

            window.fetchExportSuppliers = fetchExportSuppliers;
            window.silentExport = silentExport;
            document.addEventListener('DOMContentLoaded', () => fetchExportSuppliers(1));
        </script>
    @endpush
</x-app-layout>