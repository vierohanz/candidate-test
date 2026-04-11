<x-app-layout title="Layups">

    <div data-api-layups="{{ url('/api/v1/layups') }}" data-api-suppliers="{{ url('/api/v1/suppliers') }}">
        <h2 class="text-2xl font-semibold text-[rgb(var(--text-main))] mb-6">Layups</h2>

        <div class="flex items-center gap-3 overflow-x-auto pb-5 mb-1 scrollbar-hide" id="supplierTabs">
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
                <button onclick="layups.openCreateDrawer()" class="clt-btn-brand flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Layup
                </button>
            </div>
        </div>

        <div class="w-full">
            <table class="ref-table">
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
            onclick="layups.closeDrawer()"></div>
        <div class="absolute inset-y-0 right-0 max-w-sm w-full translate-x-full transition-transform" id="drawerPanel">
            <div
                class="h-full bg-[rgb(var(--app-bg))] border-l border-[rgb(var(--line-color))] flex flex-col transition-colors">
                <div class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center">
                    <h3 class="text-[rgb(var(--text-main))] font-semibold" id="drawerTitle">Add Layup</h3>
                    <button onclick="layups.closeDrawer()"
                        class="text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition-colors"><svg
                            class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="p-6 flex-1 overflow-y-auto">
                    <form id="layupsForm" onsubmit="layups.saveLayup(event)" class="space-y-4">
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



    <div id="detailModal" class="fixed inset-0 z-[100] overflow-hidden hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm opacity-0 transition-opacity" id="detailBackdrop"
            onclick="layups.closeDetailModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-[rgb(var(--app-bg))] border border-[rgb(var(--line-color))] rounded-2xl w-full max-w-lg scale-95 opacity-0 transition-all duration-300 shadow-2xl pointer-events-auto"
                id="detailPanel">
                <div class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center bg-black/5">
                    <h3 class="text-lg font-bold text-[rgb(var(--text-main))]" id="detailTitle">Layup Specifications
                    </h3>
                    <button onclick="layups.closeDetailModal()"
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
                    <button onclick="layups.closeDetailModal()" class="clt-btn-brand px-8 py-2.5">Close View</button>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-[120] overflow-hidden hidden">
        <div class="absolute inset-0 bg-red-950/20 backdrop-blur-sm opacity-0 transition-opacity" id="deleteBackdrop"
            onclick="layups.closeDeleteModal()"></div>
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
                        <button onclick="layups.closeDeleteModal()"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-[rgb(var(--line-color))] text-[rgb(var(--text-main))] font-semibold hover:bg-black/5 transition-colors">Cancel</button>
                        <button id="confirmDeleteBtn"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 text-white font-semibold hover:bg-red-600 transition-colors shadow-lg shadow-red-500/20">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/layups.js'])
        <script>
            if (window.layups && window.layups.init) window.layups.init();
        </script>
    @endpush
</x-app-layout>