<x-app-layout title="Activity Logs">
    <div class="space-y-6">
        <section class="clt-card p-4 sm:p-5">
            <div class="grid gap-3 xl:grid-cols-[1.2fr,0.6fr,0.7fr,auto] xl:items-center">
                <div class="relative text-[rgb(var(--text-main))]">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[rgb(var(--text-soft))]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input id="activitySearchInput" type="text" placeholder="Search activity description..." class="w-full rounded-full border-0 bg-[rgba(var(--line-color),0.04)] py-3.5 pl-11 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-[rgba(var(--brand),0.35)]">
                </div>
                <div class="relative">
                    <select id="activityActionFilter" class="clt-pagination-select w-full rounded-[16px] px-4 py-3.5 pr-10 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[rgba(var(--brand),0.22)]">
                        <option value="">All actions</option>
                        <option value="created">Created</option>
                        <option value="updated">Updated</option>
                        <option value="deleted">Deleted</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[rgb(var(--text-soft))]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </span>
                </div>
                <div class="relative">
                    <select id="activityEntityFilter" class="clt-pagination-select w-full rounded-[16px] px-4 py-3.5 pr-10 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[rgba(var(--brand),0.22)]">
                        <option value="">All entities</option>
                        <option value="supplier">Supplier</option>
                        <option value="clt_layup">Layup</option>
                        <option value="clt_layer">Layer</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[rgb(var(--text-soft))]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </span>
                </div>
                <button onclick="fetchActivityLogs(1, { force: true })" class="clt-btn-brand whitespace-nowrap">Refresh</button>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-3">
            <div class="clt-card p-5">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[rgb(var(--text-soft))]">Visible Logs</p>
                <p id="activityVisibleCount" class="mt-3 text-xl font-black text-[rgb(var(--text-main))]">0</p>
            </div>
            <div class="clt-card p-5">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[rgb(var(--text-soft))]">Filter Action</p>
                <p id="activityActionLabel" class="mt-3 text-xl font-black text-[rgb(var(--text-main))]">All</p>
            </div>
            <div class="clt-card p-5">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[rgb(var(--text-soft))]">Filter Entity</p>
                <p id="activityEntityLabel" class="mt-3 text-xl font-black text-[rgb(var(--text-main))]">All</p>
            </div>
        </section>

        <section class="clt-card overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[rgba(var(--line-color),0.03)] text-left text-[11px] uppercase tracking-[0.22em] text-[rgb(var(--text-soft))]">
                        <tr>
                            <th class="px-5 py-4 font-black">Description</th>
                            <th class="px-5 py-4 font-black">Action</th>
                            <th class="px-5 py-4 font-black">Entity</th>
                            <th class="px-5 py-4 font-black">IP</th>
                            <th class="px-5 py-4 font-black">Time</th>
                        </tr>
                    </thead>
                    <tbody id="activityTableBody" class="divide-y divide-[rgba(var(--line-color),0.06)]">
                        <tr><td colspan="5" class="px-5 py-16 text-center text-[rgb(var(--text-soft))]">Loading activity logs...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <div class="clt-pagination-shell flex flex-col gap-4 rounded-[18px] px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="flex flex-wrap items-center gap-3 text-sm text-[rgb(var(--text-soft))]">
                <span>Show</span>
                <div class="relative">
                    <select id="activityPerPage" class="clt-pagination-select rounded-[14px] px-4 py-2.5 pr-10 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[rgba(var(--brand),0.22)]">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[rgb(var(--text-soft))]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </span>
                </div>
                <span id="activityPaginationInfo" class="font-medium text-[rgb(var(--text-muted))]">from 0</span>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <button id="activityPrevBtn" class="clt-page-btn px-3" disabled>PREV</button>
                <div id="activityPageNumbers" class="flex flex-wrap gap-1.5"></div>
                <button id="activityNextBtn" class="clt-page-btn px-3" disabled>NEXT</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let activityPage = 1;
        let activitySearch = '';
        let activityAction = '';
        let activityEntity = '';
        let activityPerPage = 10;

        function renderActivityRows(items, meta) {
            document.getElementById('activityVisibleCount').innerText = items.length;
            document.getElementById('activityActionLabel').innerText = activityAction || 'All';
            document.getElementById('activityEntityLabel').innerText = activityEntity || 'All';
            const body = document.getElementById('activityTableBody');
            if (!items.length) {
                body.innerHTML = '<tr><td colspan="5" class="px-5 py-16 text-center text-[rgb(var(--text-soft))]">No activity logs found.</td></tr>';
            } else {
                body.innerHTML = items.map((item) => `
                    <tr>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-[rgb(var(--text-main))]">${item.description || '-'}</p>
                            <p class="mt-1 text-xs text-[rgb(var(--text-soft))]">ID ${item.entity_id || '-'}</p>
                        </td>
                        <td class="px-5 py-4"><span class="rounded-full bg-[rgba(var(--brand),0.12)] px-3 py-1 text-xs font-semibold capitalize text-[rgb(var(--brand))]">${item.action || '-'}</span></td>
                        <td class="px-5 py-4 text-[rgb(var(--text-main))]">${item.entity_type || '-'}</td>
                        <td class="px-5 py-4 text-[rgb(var(--text-soft))]">${item.ip_address || '-'}</td>
                        <td class="px-5 py-4 text-[rgb(var(--text-soft))]">${item.created_at ? new Date(item.created_at).toLocaleString() : '-'}</td>
                    </tr>
                `).join('');
            }
            updateActivityPagination(meta || {});
        }

        async function fetchActivityLogs(page = 1) {
            activityPage = page;
            document.getElementById('activityTableBody').innerHTML = '<tr><td colspan="5" class="px-5 py-16 text-center text-[rgb(var(--text-soft))]">Loading activity logs...</td></tr>';
            try {
                const params = new URLSearchParams({
                    page,
                    q: activitySearch,
                    action: activityAction,
                    entity_type: activityEntity,
                    per_page: activityPerPage,
                });
                const response = await fetch(`/api/v1/activity-logs?${params.toString()}`, { headers: { Accept: 'application/json' } });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || 'Failed to fetch activity logs');
                renderActivityRows(Array.isArray(result.data) ? result.data : [], result.metadata || {});
            } catch (error) {
                document.getElementById('activityTableBody').innerHTML = `<tr><td colspan="5" class="px-5 py-16 text-center text-rose-500">${error.message}</td></tr>`;
            }
        }

        function updateActivityPagination(meta) {
            const current = Number(meta.current_page || 1);
            const totalPages = Number(meta.total_page || 1);
            const total = Number(meta.total_row || 0);
            document.getElementById('activityPaginationInfo').innerText = `from ${total}`;
            document.getElementById('activityPrevBtn').disabled = current <= 1;
            document.getElementById('activityNextBtn').disabled = current >= totalPages;
            const pageNumbers = document.getElementById('activityPageNumbers');
            pageNumbers.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                pageNumbers.innerHTML += `<button onclick="fetchActivityLogs(${i})" class="clt-page-btn ${i === current ? 'active' : ''}">${i}</button>`;
            }
            document.getElementById('activityPrevBtn').onclick = () => { if (current > 1) fetchActivityLogs(current - 1); };
            document.getElementById('activityNextBtn').onclick = () => { if (current < totalPages) fetchActivityLogs(current + 1); };
        }

        document.getElementById('activitySearchInput').addEventListener('input', (e) => { activitySearch = e.target.value; fetchActivityLogs(1); });
        document.getElementById('activityActionFilter').addEventListener('change', (e) => { activityAction = e.target.value; fetchActivityLogs(1); });
        document.getElementById('activityEntityFilter').addEventListener('change', (e) => { activityEntity = e.target.value; fetchActivityLogs(1); });
        document.getElementById('activityPerPage').addEventListener('change', (e) => { activityPerPage = Number(e.target.value); fetchActivityLogs(1); });
        document.addEventListener('DOMContentLoaded', () => fetchActivityLogs(1));
    </script>
    @endpush
</x-app-layout>
