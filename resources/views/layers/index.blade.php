<x-app-layout title="Layers">

    <div class="space-y-6" data-api-layers="{{ url('/api/v1/layers') }}"
        data-api-suppliers="{{ url('/api/v1/suppliers') }}" data-api-layups="{{ url('/api/v1/layups') }}">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="space-y-2">
                <p class="text-[11px] font-black uppercase tracking-[0.24em] text-[rgb(var(--text-soft))]">Layer
                    Workspace</p>
                <h2 class="text-3xl font-black text-[rgb(var(--text-main))]">Technical Layers</h2>
                <p class="max-w-2xl text-sm leading-6 text-[rgb(var(--text-soft))]">Pilih supplier dan layup aktif, lalu
                    kelola urutan layer, thickness, width, dan angle dari satu tampilan yang lebih rapi.</p>
            </div>
            <button onclick="layers.openCreateDrawer()"
                class="clt-btn-brand inline-flex items-center justify-center gap-2 px-6 py-3 shadow-lg shadow-emerald-500/10">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                New Layer
            </button>
        </div>

        <section class="grid gap-4 lg:grid-cols-2">
            <div class="clt-card p-5">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[rgb(var(--text-soft))]">Supplier</p>
                <div class="relative mt-4">
                    <select id="supplierFilter" onchange="layers.handleSupplierChange()"
                        class="clt-pagination-select w-full"></select>
                </div>
            </div>
            <div class="clt-card p-5">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[rgb(var(--text-soft))]">Layup</p>
                <div class="relative mt-4">
                    <select id="layupFilter" onchange="layers.handleLayupChange()"
                        class="clt-pagination-select w-full"></select>
                </div>
            </div>
        </section>

        <section class="overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="ref-table">
                    <thead>
                        <tr>
                            <th class="w-16 pl-4">No</th>
                            <th class="w-20">Order</th>
                            <th>Layup Name</th>
                            <th class="text-center">Thickness (mm)</th>
                            <th class="text-center">Width (mm)</th>
                            <th class="text-center">Angle (°)</th>
                            <th class="w-24 text-center pr-4">Action</th>
                        </tr>
                    </thead>
                    <tbody id="layersTableBody">
                    </tbody>
                </table>
            </div>
        </section>

        <div
            class="clt-pagination-shell flex flex-col gap-4 rounded-[18px] px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <p class="text-sm text-[rgb(var(--text-soft))]" id="paginationInfo">Showing 0 layers</p>
            <div class="flex gap-2">
                <button id="prevPageBtn" disabled class="clt-page-btn px-4">Prev</button>
                <button id="nextPageBtn" disabled class="clt-page-btn px-4">Next</button>
            </div>
        </div>
    </div>

    <div id="layerDrawer" class="fixed inset-0 z-50 overflow-hidden hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm opacity-0 transition-opacity" id="drawerBackdrop"
            onclick="layers.closeDrawer()"></div>
        <div class="absolute inset-y-0 right-0 max-w-sm w-full translate-x-full transition-transform" id="drawerPanel">
            <div
                class="h-full bg-[rgb(var(--app-bg))] border-l border-[rgb(var(--line-color))] flex flex-col transition-colors">
                <div class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center">
                    <h3 class="text-[rgb(var(--text-main))] font-semibold" id="drawerTitle">Add Layer</h3>
                    <button onclick="layers.closeDrawer()"
                        class="text-[rgb(var(--text-soft))] hover:text-[rgb(var(--text-main))] transition-colors"><svg
                            class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="p-6 flex-1 overflow-y-auto">
                    <form onsubmit="layers.saveLayer(event)" class="space-y-4">
                        <input type="hidden" id="layerId">
                        <div>
                            <label class="block text-xs font-semibold text-[rgb(var(--text-main))] mb-2">Layup</label>
                            <select id="formLayupId" required class="ref-input w-full"></select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label
                                    class="block text-xs font-semibold text-[rgb(var(--text-main))] mb-2">Order</label><input
                                    type="number" id="formOrder" required class="ref-input w-full"></div>
                            <div><label class="block text-xs font-semibold text-[rgb(var(--text-main))] mb-2">Angle
                                    °</label><input type="number" step="0.1" id="formAngle" required
                                    class="ref-input w-full"></div>
                            <div><label
                                    class="block text-xs font-semibold text-[rgb(var(--text-main))] mb-2">Thickness</label><input
                                    type="number" step="0.01" id="formThickness" required class="ref-input w-full">
                            </div>
                            <div><label
                                    class="block text-xs font-semibold text-[rgb(var(--text-main))] mb-2">Width</label><input
                                    type="number" step="0.1" id="formWidth" required class="ref-input w-full"></div>
                        </div>
                        <button type="submit" id="submitBtn" class="clt-btn-brand w-full mt-4">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div id="detailModal" class="fixed inset-0 z-[100] overflow-hidden hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm opacity-0 transition-opacity" id="detailBackdrop"
            onclick="layers.closeDetailModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-[rgb(var(--app-bg))] border border-[rgb(var(--line-color))] rounded-2xl w-full max-w-lg scale-95 opacity-0 transition-all duration-300 shadow-2xl pointer-events-auto"
                id="detailPanel">
                <div class="p-6 border-b border-[rgb(var(--line-color))] flex justify-between items-center bg-black/5">
                    <h3 class="text-lg font-bold text-[rgb(var(--text-main))]" id="detailTitle">Technical Specifications
                    </h3>
                    <button onclick="layers.closeDetailModal()"
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
                    <button onclick="layers.closeDetailModal()" class="clt-btn-brand px-8 py-2.5">Close View</button>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-[120] overflow-hidden hidden">
        <div class="absolute inset-0 bg-red-950/20 backdrop-blur-sm opacity-0 transition-opacity" id="deleteBackdrop"
            onclick="layers.closeDeleteModal()"></div>
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
                    <h3 class="text-xl font-bold text-[rgb(var(--text-main))] mb-2">Remove Layer?</h3>
                    <p class="text-sm text-[rgb(var(--text-soft))] mb-8">This technical layer will be deleted. This
                        action cannot be reversed.</p>
                    <div class="flex gap-3">
                        <button onclick="layers.closeDeleteModal()"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-[rgb(var(--line-color))] text-[rgb(var(--text-main))] font-semibold hover:bg-black/5 transition-colors">Cancel</button>
                        <button id="confirmDeleteBtn"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 text-white font-semibold hover:bg-red-600 transition-colors shadow-lg shadow-red-500/20">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/layers.js'])
        <script>
            if (window.layers && window.layers.init) window.layers.init();
        </script>
    @endpush
</x-app-layout>