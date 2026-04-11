<x-app-layout title="Activity Logs">
    <div class="space-y-6">
        <section class="clt-card p-6">
            <p class="text-[11px] font-black uppercase tracking-[0.24em] text-[rgb(var(--text-soft))]">Activity Stream
            </p>
            <h2 class="mt-3 text-3xl font-black text-[rgb(var(--text-main))]">Recent Changes</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-[rgb(var(--text-soft))]">All supplier, layup, and layer
                changes are displayed here in the most recent order for easier reading during reviews and demos.</p>
        </section>

        <section class="clt-card overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead
                        class="bg-[rgba(var(--line-color),0.03)] text-left text-[11px] uppercase tracking-[0.22em] text-[rgb(var(--text-soft))]">
                        <tr>
                            <th class="px-5 py-4 font-black">Description</th>
                            <th class="px-5 py-4 font-black">Action</th>
                            <th class="px-5 py-4 font-black">Entity</th>
                            <th class="px-5 py-4 font-black">IP</th>
                            <th class="px-5 py-4 font-black">Time</th>
                        </tr>
                    </thead>
                    <tbody id="activityTableBody" class="divide-y divide-[rgba(var(--line-color),0.06)]">
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center text-[rgb(var(--text-soft))]">Loading activity
                                logs...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <div
            class="clt-pagination-shell flex flex-col gap-4 rounded-[18px] px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="text-sm text-[rgb(var(--text-soft))]">
                <span id="activityPaginationInfo" class="font-medium text-[rgb(var(--text-muted))]">Showing 0 activity
                    logs</span>
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
            let activityPerPage = 10;

            function renderActivityRows(items, meta) {
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
                const perPage = Number(meta.per_page || activityPerPage);
                const from = total ? ((current - 1) * perPage) + 1 : 0;
                const to = total ? Math.min(current * perPage, total) : 0;
                document.getElementById('activityPaginationInfo').innerText = `Showing ${from}-${to} of ${total} activity logs`;
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

            window.fetchActivityLogs = fetchActivityLogs;
            document.addEventListener('DOMContentLoaded', () => fetchActivityLogs(1));
        </script>
    @endpush
</x-app-layout>