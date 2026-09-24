<div class="space-y-6">

    <div class="flex flex-col lg:flex-row items-stretch lg:items-end justify-between gap-5 bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Channel:
                </label>
                <select x-model="filters.channel" @change="applyFilters()" class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2.5 px-3.5 focus:ring-2 focus:ring-[#2F3185]/20 focus:border-[#2F3185] transition-all">
                    <option value="">- All Channel -</option>
                    <?php foreach ($channels ?? [] as $ch): ?>
                        <option value="<?= esc($ch['channel_code']) ?>"><?= esc($ch['channel_code']) ?> - <?= esc($ch['channel_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Pencarian Produk:
                </label>
                <input type="text" 
                       x-model="filters.search" 
                       @input.debounce.300ms="applyFilters()" 
                       placeholder="Ketik untuk mencari SKU, nama produk, atau kategori..." 
                       class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white placeholder:text-gray-400 focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-[#2F3185]/20 focus:border-[#2F3185] transition-all">
            </div>
        </div>
        <div class="flex items-center gap-3 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-gray-100 dark:border-gray-800">
            <button type="button" @click="showChart = !showChart" :class="showChart ? 'bg-indigo-50 text-[#2F3185] dark:bg-indigo-900/40 dark:text-indigo-300 border-indigo-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border-gray-200'" class="px-4.5 py-2.5 text-xs font-semibold rounded-xl border shadow-xs transition-all flex items-center gap-2">
                <i class="fa-solid fa-chart-line"></i>
                <span x-text="showChart ? 'Sembunyikan Grafik' : 'Tampilkan Grafik'">Tampilkan Grafik</span>
            </button>
            <button type="button" @click="downloadChannelTemplate()" class="px-4.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-2 active:scale-[0.98]">
                <i class="fa-solid fa-file-excel"></i>
                <span>Template Data Channel</span>
            </button>
        </div>
    </div>

    <div x-show="showChart" x-transition class="bg-white dark:bg-gray-900 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-5">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
            <div class="flex items-center gap-2.5">
                <span class="h-3 w-3 rounded-full bg-teal-500"></span>
                <h3 class="text-xs font-bold text-gray-900 dark:text-white tracking-wider">Trend Revenue Sales Domestic (12 Bulan)</h3>
            </div>
            <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 p-1 rounded-xl">
                <button type="button" @click="chartMetric = 'revenue'; renderChart()" :class="chartMetric === 'revenue' ? 'bg-[#2F3185] text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">Revenue</button>
                <button type="button" @click="chartMetric = 'qty'; renderChart()" :class="chartMetric === 'qty' ? 'bg-[#2F3185] text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">Volume</button>
            </div>
        </div>
        <div id="domesticTrendChart" class="w-full h-72"></div>
    </div>

    <!-- Table Section: Label & Action Controls (Separated from table container) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Target Sales Domestic 12 Bulan (Pivot Grid)</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Input nilai Qty & Revenue untuk update ASP otomatis.</p>
        </div>
        <button type="button" @click="saveChanges()" class="px-5 py-2.5 bg-[#2F3185] hover:bg-[#25276d] text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 active:scale-[0.98] shrink-0">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden bg-white dark:bg-gray-900">
        <div id="domesticBudgetTableWrap" class="overflow-x-auto scrollbar-thin max-h-[680px]">
            <table class="w-full text-left text-xs border-collapse min-w-[3400px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 sticky top-0 z-20 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold border-b border-white/20">
                        <th class="px-3 py-3 border-r border-white/20 w-12 text-center sticky left-0 z-30 bg-[#2F3185] text-white font-semibold text-xs" rowspan="2">No.</th>
                        <th class="px-3 py-3 border-r border-white/20 min-w-[95px] text-center sticky left-12 z-30 bg-[#2F3185] text-white font-semibold text-xs" rowspan="2">Channel</th>
                        <th class="px-3 py-3 border-r border-white/20 min-w-[160px] text-white font-semibold text-xs" rowspan="2">Key Product</th>
                        <th class="px-3 py-3 border-r border-white/20 min-w-[130px] text-white font-semibold text-xs" rowspan="2">Code Inv</th>
                        <th class="px-3 py-3 border-r border-white/20 min-w-[280px] text-white font-semibold text-xs" rowspan="2">Name Product</th>
                        <template x-for="(month, idx) in monthNames" :key="month">
                            <th class="px-3 py-2 border-r border-white/20 text-center text-white font-semibold text-xs bg-[#2F3185]" colspan="3" x-text="month"></th>
                        </template>
                        <th class="px-3 py-2 border-l border-white/20 bg-[#2F3185] text-center font-bold text-white text-xs" colspan="3">Annual Total</th>
                    </tr>
                    <tr class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
                        <?php for ($i = 0; $i < 12; $i++): ?>
                            <th class="px-2 py-2 border-r border-white/20 w-[88px] text-center text-white font-semibold text-xs bg-[#2F3185]">Qty</th>
                            <th class="px-2 py-2 border-r border-white/20 w-[120px] text-center text-white font-semibold text-xs bg-[#2F3185]">Revenue</th>
                            <th class="px-2 py-2 border-r border-white/20 w-[96px] text-center text-white font-semibold text-xs bg-[#2F3185]">ASP/kg</th>
                        <?php endfor; ?>
                        <th class="px-2 py-2 border-r border-white/20 w-[120px] text-center text-white bg-[#2F3185] font-semibold text-xs">Tot Qty</th>
                        <th class="px-2 py-2 border-r border-white/20 w-28 text-center text-white bg-[#2F3185] font-semibold text-xs">Tot Rev</th>
                        <th class="px-2 py-2 border-r border-white/20 w-[120px] text-center text-white bg-[#2F3185] font-semibold text-xs">Avg ASP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-if="filteredItems.length === 0">
                        <tr>
                            <td colspan="44" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-boxes-packing text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Data Budget Domestic</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Upload data via tab <strong>Upload Data</strong> lalu <strong>Process Summary SKU</strong>.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-for="(row, i) in pageFlatRows" :key="'f-'+i">
                        <tr :class="row.kind === 'subtotal' ? 'bg-amber-50/80 dark:bg-amber-950/30 border-y border-amber-200 dark:border-amber-900/50 font-bold text-amber-900 dark:text-amber-200' : 'hover:bg-sky-50/50 dark:hover:bg-gray-800/60 transition-colors'">
                            <template x-if="row.kind === 'subtotal'">
                                <td class="px-3 py-2.5 text-center sticky left-0 z-10 bg-amber-100 dark:bg-amber-900/40 text-xs" colspan="2" x-text="row.group.channel"></td>
                            </template>
                            <template x-if="row.kind === 'subtotal'">
                                <td class="p-2 text-xs" colspan="3">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-layer-group text-xs text-amber-600"></i>
                                        <span x-text="'SUBTOTAL CHANNEL ' + row.group.channel"></span>
                                        <span class="text-xs text-amber-700 dark:text-amber-400 font-sans font-normal" x-text="'(' + row.group.items.length + ' items)'"></span>
                                    </span>
                                </td>
                            </template>
                            <template x-if="row.kind === 'item'">
                                <td class="px-3 py-2.5 text-center border-r border-gray-200 dark:border-gray-800 sticky left-0 z-10 bg-white dark:bg-gray-900 text-gray-500 text-xs" x-text="(currentPage - 1) * perPage + i + 1"></td>
                            </template>
                            <template x-if="row.kind === 'item'">
                                <td class="px-3 py-2.5 text-center border-r border-gray-200 dark:border-gray-800 font-sans font-bold text-xs sticky left-12 z-10 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300" x-text="row.item.id_channel"></td>
                            </template>
                            <template x-if="row.kind === 'item'">
                                <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800 font-sans text-xs text-gray-600 dark:text-gray-400" x-text="row.item.key_product || '-'"></td>
                            </template>
                            <template x-if="row.kind === 'item'">
                                <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="row.item.code_inv_1 || row.item.mid_product"></td>
                            </template>
                            <template x-if="row.kind === 'item'">
                                <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800 font-sans font-medium text-xs text-gray-900 dark:text-white" x-text="row.item.name_product || row.item.product_name"></td>
                            </template>
                            <template x-for="col in metricCols" :key="'fm-'+i+'-'+col.m+'-'+col.k">
                                <td :class="row.kind === 'subtotal' ? 'px-2 py-2 text-right border-r border-amber-200/60 dark:border-amber-900/40 text-xs' + (col.k === 'asp' ? ' bg-amber-100/40 dark:bg-amber-950/40' : '') : 'px-2 py-2 border-r border-gray-200 dark:border-gray-800 text-xs' + (col.k === 'asp' ? ' bg-slate-50/70 dark:bg-gray-800/40 text-right font-medium text-gray-700 dark:text-gray-300' : '')">
                                    <template x-if="row.kind === 'subtotal'">
                                        <span class="block text-right text-xs" x-text="col.k === 'qty' ? formatNumber(row.group.subtotal.monthly[col.m].qty) : col.k === 'rev' ? formatNumber(row.group.subtotal.monthly[col.m].revenue) : formatNumber(calculateASP(row.group.subtotal.monthly[col.m].revenue, row.group.subtotal.monthly[col.m].qty, row.group.channel))"></span>
                                    </template>
                                    <template x-if="row.kind === 'item' && col.k === 'qty'">
                                        <input type="number" x-model.number="row.item.monthly[col.m].qty" @input="recalculateRow(row.item)" class="w-full text-right px-2 py-1.5 text-xs font-mono border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-[#2F3185] focus:bg-white dark:focus:bg-gray-900 transition-all">
                                    </template>
                                    <template x-if="row.kind === 'item' && col.k === 'rev'">
                                        <input type="number" x-model.number="row.item.monthly[col.m].revenue" @input="recalculateRow(row.item)" class="w-full text-right px-2 py-1.5 text-xs font-mono border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold focus:ring-1 focus:ring-emerald-500 focus:bg-white dark:focus:bg-gray-900 transition-all">
                                    </template>
                                    <template x-if="row.kind === 'item' && col.k === 'asp'">
                                        <span class="block text-right px-1 text-xs font-mono" x-text="formatNumber(calculateASP(row.item.monthly[col.m].revenue, row.item.monthly[col.m].qty, row.item.id_channel))"></span>
                                    </template>
                                </td>
                            </template>
                            <template x-if="row.kind === 'subtotal'">
                                <td class="px-3 py-2.5 text-right border-r border-amber-300 font-extrabold text-xs bg-amber-100 dark:bg-amber-900/50" x-text="formatNumber(row.group.subtotal.total_qty)"></td>
                            </template>
                            <template x-if="row.kind === 'subtotal'">
                                <td class="px-3 py-2.5 text-right border-r border-amber-300 font-extrabold text-xs text-emerald-700 dark:text-emerald-400 bg-amber-100 dark:bg-amber-900/50" x-text="formatNumber(row.group.subtotal.total_revenue)"></td>
                            </template>
                            <template x-if="row.kind === 'subtotal'">
                                <td class="px-3 py-2.5 text-right font-extrabold text-xs bg-amber-100 dark:bg-amber-900/50" x-text="formatNumber(calculateASP(row.group.subtotal.total_revenue, row.group.subtotal.total_qty, row.group.channel))"></td>
                            </template>
                            <template x-if="row.kind === 'item'">
                                <td class="px-3 py-2.5 border-l-2 border-[#2F3185]/30 border-r border-gray-200 dark:border-gray-800 bg-[#2F3185]/5 dark:bg-gray-800 text-right font-bold text-xs text-gray-900 dark:text-white" x-text="formatNumber(row.item.total_qty)"></td>
                            </template>
                            <template x-if="row.kind === 'item'">
                                <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800 bg-[#2F3185]/5 dark:bg-gray-800 text-right font-bold text-xs text-emerald-600 dark:text-emerald-400" x-text="formatNumber(row.item.total_revenue)"></td>
                            </template>
                            <template x-if="row.kind === 'item'">
                                <td class="px-3 py-2.5 bg-[#2F3185]/5 dark:bg-gray-800 text-right font-bold text-xs text-amber-600 dark:text-amber-400" x-text="formatNumber(calculateASP(row.item.total_revenue, row.item.total_qty, row.item.id_channel))"></td>
                            </template>
                        </tr>
                    </template>
                    <tr class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-extrabold text-gray-900 dark:text-white border-t-2 border-[#2F3185]/30 text-xs">
                        <td class="p-3 text-center sticky left-0 z-10 bg-[#2F3185]/20 dark:bg-[#2F3185]/30 text-xs" colspan="5">Grand Total All Channels</td>
                        <template x-for="col in metricCols" :key="'gt-'+col.m+'-'+col.k">
                            <td class="px-3 py-2.5 text-right border-r border-[#2F3185]/20 text-xs" :class="col.k === 'asp' ? 'bg-[#2F3185]/15 dark:bg-[#2F3185]/25' : ''" x-text="col.k === 'qty' ? formatNumber(grandTotal.monthly[col.m].qty) : col.k === 'rev' ? formatNumber(grandTotal.monthly[col.m].revenue) : formatNumber(calculateASP(grandTotal.monthly[col.m].revenue, grandTotal.monthly[col.m].qty))"></td>
                        </template>
                        <td class="p-3 text-right border-r border-[#2F3185]/30 font-mono text-xs font-extrabold" x-text="formatNumber(grandTotal.total_qty)"></td>
                        <td class="p-3 text-right border-r border-[#2F3185]/30 font-mono text-xs font-extrabold text-emerald-600 dark:text-emerald-400" x-text="formatNumber(grandTotal.total_revenue)"></td>
                        <td class="p-3 text-right font-mono text-xs font-extrabold text-amber-600 dark:text-amber-400" x-text="formatNumber(calculateASP(grandTotal.total_revenue, grandTotal.total_qty))"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-3.5 border-t border-gray-200/80 dark:border-gray-800 bg-gray-50/60 dark:bg-gray-800/40">
            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                <span>
                    Menampilkan
                    <span class="font-semibold text-gray-800 dark:text-gray-200" x-text="pageInfo().from">0</span>
                    &ndash;
                    <span class="font-semibold text-gray-800 dark:text-gray-200" x-text="pageInfo().to">0</span>
                    dari
                    <span class="font-semibold text-gray-800 dark:text-gray-200" x-text="pageInfo().total">0</span>
                    data
                </span>
                <select x-model.number="perPage" @change="setPerPage(perPage)" class="rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 py-1.5 px-2.5 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    <option value="10">10 / halaman</option>
                    <option value="25">25 / halaman</option>
                    <option value="50">50 / halaman</option>
                    <option value="100">100 / halaman</option>
                </select>
            </div>
            <nav class="flex items-center gap-1" aria-label="Pagination Tabel Budget">
                <button type="button" @click="setPage(currentPage - 1)" :disabled="currentPage <= 1"
                    class="px-3 py-1.5 rounded-lg border text-xs font-semibold transition-all bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed">
                    &laquo; Prev
                </button>
                <template x-for="(p, pi) in getPageList()" :key="'pg-' + pi + '-' + p">
                    <span x-show="p === '...'" class="px-2 text-xs text-gray-400 select-none">&hellip;</span>
                </template>
                <template x-for="(p, pi) in getPageList()" :key="'pgn-' + pi + '-' + p">
                    <button x-show="p !== '...'" type="button" @click="setPage(p)"
                        :class="p === currentPage ? 'bg-[#2F3185] border-[#2F3185] text-white shadow-xs' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="min-w-[32px] px-2.5 py-1.5 rounded-lg border text-xs font-semibold transition-all"
                        x-text="p"></button>
                </template>
                <button type="button" @click="setPage(currentPage + 1)" :disabled="currentPage >= totalPages()"
                    class="px-3 py-1.5 rounded-lg border text-xs font-semibold transition-all bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed">
                    Next &raquo;
                </button>
            </nav>
        </div>
    </div>

</div>