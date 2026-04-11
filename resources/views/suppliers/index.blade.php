<x-app-layout title="Suppliers">

    <div data-api-suppliers="{{ url('/api/v1/suppliers') }}" data-api-export="{{ url('/api/v1/export') }}">
        <h2 class="text-2xl font-semibold text-[rgb(var(--text-main))] mb-6">Suppliers</h2>

        <!-- Controls row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-[rgb(var(--text-soft))]">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" id="searchInput" placeholder="Search by name..." class="ref-input w-full pl-9">
            </div>

            <div class="flex items-center gap-3">
                <button onclick="suppliers.openCreateDrawer()" class="clt-btn-brand flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Supplier
                </button>
            </div>
        </div>

        <!-- Cards Grid -->
        <div id="suppliersGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
            <!-- Data -->
        </div>

        <!-- Pagination -->
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between mt-8 border-t border-[rgb(var(--line-color))] pt-6 transition-colors">
            <p class="text-[11px] text-[rgb(var(--text-soft))]" id="paginationInfo">Showing 0 suppliers</p>
            <div class="flex gap-2">
                <button id="prevPageBtn" disabled
                    class="clt-page-btn text-[rgb(var(--text-soft))] px-4 border border-[rgb(var(--line-color))] rounded-lg hover:border-[rgb(var(--text-soft))] transition-colors">Previous</button>
                <button id="nextPageBtn" disabled
                    class="clt-page-btn text-[rgb(var(--text-soft))] px-4 border border-[rgb(var(--line-color))] rounded-lg hover:border-[rgb(var(--text-soft))] transition-colors">Next</button>
            </div>
        </div>
    </div>

    <!-- Drawer Modal -->
    <div id="supplierDrawer" class="fixed inset-0 z-50 overflow-hidden hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm opacity-0 transition-opacity" id="drawerBackdrop"
            onclick="suppliers.closeDrawer()"></div>
        <div class="absolute inset-y-0 right-0 max-w-sm w-full translate-x-full transition-transform" id="drawerPanel">
            <div class="h-full bg-[rgb(var(--app-bg))] border-l border-[rgb(var(--line-color))] flex flex-col">
                <div
                    class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center transition-colors">
                    <h3 class="text-[rgb(var(--text-main))] font-semibold" id="drawerTitle">Add Supplier</h3>
                    <button onclick="suppliers.closeDrawer()"
                        class="text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition-colors"><svg
                            class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="p-6 flex-1">
                    <form id="supplierForm" onsubmit="suppliers.saveSupplier(event)" class="space-y-4">
                        <input type="hidden" id="supplierId">
                        <div>
                            <label class="block text-xs font-semibold text-[rgb(var(--text-main))] mb-2">Supplier
                                Name</label>
                            <input type="text" id="supplierName" required class="ref-input w-full"
                                placeholder="PT. Company...">
                        </div>
                        <button type="submit" id="submitBtn" class="clt-btn-brand w-full mt-4">Save Changes</button>
                    </form>

                    <div id="drawerImportSection" class="mt-10 pt-6 border-t border-[rgb(var(--line-color))] hidden">
                        <p class="text-[10px] font-bold text-[rgb(var(--text-soft))] uppercase tracking-widest mb-4">Advanced Actions</p>
                        <button onclick="suppliers.triggerImport(document.getElementById('supplierId').value)" 
                                class="w-full flex items-center justify-between p-4 bg-orange-500/5 border border-orange-500/10 rounded-2xl hover:bg-orange-500/10 transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-orange-500/20 text-orange-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-bold text-[rgb(var(--text-main))]">Import JSON</p>
                                    <p class="text-[10px] text-[rgb(var(--text-soft))]">Merge external layup data</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-[rgb(var(--text-soft))] group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div id="detailModal" class="fixed inset-0 z-[100] overflow-hidden hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-md opacity-0 transition-opacity" id="detailBackdrop"
            onclick="suppliers.closeDetailModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-[rgb(var(--app-bg))] border border-[rgb(var(--line-color))] rounded-2xl w-full max-w-lg scale-95 opacity-0 transition-all duration-300 shadow-2xl pointer-events-auto"
                id="detailPanel">
                <div class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center bg-black/5">
                    <h3 class="text-lg font-bold text-[rgb(var(--text-main))]" id="detailTitle">Data Integrity Check
                    </h3>
                    <button onclick="suppliers.closeDetailModal()"
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
                    <button onclick="suppliers.closeDetailModal()" class="clt-btn-brand px-8 py-2.5">Close View</button>
                </div>
            </div>
        </div>
    </div>

    <input type="file" id="importFile" class="hidden" accept=".json">

    <div id="importConflictModal" class="fixed inset-0 z-[100] overflow-hidden hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-md opacity-0 transition-opacity" id="conflictBackdrop"
            onclick="suppliers.closeConflictModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-[rgb(var(--app-bg))] border border-[rgb(var(--line-color))] rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl scale-95 opacity-0 transition-all duration-300 pointer-events-auto"
                id="conflictPanel">
                <div class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-[rgb(var(--text-main))]">Import Conflict Review</h3>
                        <p class="text-[11px] text-[rgb(var(--text-soft))] mt-1">Review differences before merging data
                            into the system.</p>
                    </div>
                    <button onclick="suppliers.closeConflictModal()"
                        class="text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition-colors"><svg
                            class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <div class="p-6 overflow-y-auto flex-1 custom-scrollbar" id="conflictContent">
                    <!-- Content injected here -->
                </div>

                <div class="p-6 border-t border-[rgb(var(--line-color))] bg-black/5 flex items-center justify-between">
                    <button onclick="suppliers.closeConflictModal()"
                        class="px-6 py-2.5 text-sm font-semibold text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition-colors">Cancel
                        Import</button>
                    <div class="flex gap-3">
                        <button id="importSkipBtn" onclick="suppliers.confirmImport('skip')"
                            class="px-6 py-2.5 border border-[rgb(var(--line-color))] rounded-xl text-sm font-semibold text-[rgb(var(--text-main))] hover:border-[rgb(var(--text-soft))] transition-all">Keep
                            Existing (Skip)</button>
                        <button id="importOverwriteBtn" onclick="suppliers.confirmImport('overwrite')"
                            class="clt-btn-brand px-8 py-2.5 shadow-lg shadow-emerald-500/20">Overwrite & Merge</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-[120] overflow-hidden hidden">
        <div class="absolute inset-0 bg-red-950/20 backdrop-blur-sm opacity-0 transition-opacity" id="deleteBackdrop"
            onclick="suppliers.closeDeleteModal()"></div>
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
                    <h3 class="text-xl font-bold text-[rgb(var(--text-main))] mb-2">Confirm Delete?</h3>
                    <p class="text-sm text-[rgb(var(--text-soft))] mb-8">This action is permanent and cannot be undone.
                        All associated data will be removed.</p>
                    <div class="flex gap-3">
                        <button onclick="suppliers.closeDeleteModal()"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-[rgb(var(--line-color))] text-[rgb(var(--text-main))] font-semibold hover:bg-black/5 transition-colors">Cancel</button>
                        <button id="confirmDeleteBtn"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 text-white font-semibold hover:bg-red-600 transition-colors shadow-lg shadow-red-500/20">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/suppliers.js'])
        <script>
            if (window.suppliers && window.suppliers.fetch) window.suppliers.fetch(1);
        </script>
    @endpush
</x-app-layout>