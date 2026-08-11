<div class="space-y-6">

    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-5 bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div class="flex flex-wrap items-center gap-5 flex-1">
            <div class="flex items-center gap-3 min-w-[220px]">
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-filter text-primary"></i> Channel:
                </label>
                <select x-model="filters.channel" @change="applyFilters()" class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2.5 px-3.5 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    <option value="">- All Channel -</option>
                    <option value="GT">GT - General Trade</option>
                    <option value="MT">MT - Modern Trade</option>
                    <option value="OEM">OEM - Original Equipment Mfg</option>
                    <option value="ECOM">ECOM - E-Commerce</option>
                    <option value="YTI">YTI - Yupi Trading International</option>
                </select>
            </div>
            <div class="relative flex-1 min-w-[300px]">
                <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()" placeholder="Cari SKU Code / Nama Produk / Key Product..." class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-xs text-gray-400"></i>
            </div>
        </div>
        <div class="flex items-center gap-3 shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-gray-100 dark:border-gray-800">
            <button type="button" @click="showChart = !showChart" :class="showChart ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 border-indigo-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border-gray-200'" class="px-4 py-2.5 text-xs font-semibold rounded-xl border shadow-xs transition-all flex items-center gap-2">
                <i class="fa-solid fa-chart-line"></i>
                <span x-text="showChart ? 'Sembunyikan Grafik' : 'Tampilkan Grafik'">Tampilkan Grafik</span>
            </button>
            <button type="button" @click="downloadChannelTemplate()" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-2 active:scale-[0.98]">
                <i class="fa-solid fa-file-excel"></i>
                <span>Template Data Channel</span>
            </button>
        </div>
    </div>

    <div x-show="showChart" x-transition class="bg-white dark:bg-gray-900 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-5">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
            <div class="flex items-center gap-2.5">
                <span class="h-3 w-3 rounded-full bg-teal-500"></span>
                <h3 class="text-xs font-bold text-gray-900 dark:text-white tracking-wider uppercase">SALES DOMESTIC REVENUE TREND (12 MONTHS)</h3>
            </div>
            <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 p-1 rounded-xl">
                <button type="button" @click="chartMetric = 'revenue'; renderChart()" :class="chartMetric === 'revenue' ? 'bg-primary text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'" class="px-3 py-1.5 text-[11px] font-semibold rounded-lg transition-all">Revenue (Rp)</button>
                <button type="button" @click="chartMetric = 'qty'; renderChart()" :class="chartMetric === 'qty' ? 'bg-primary text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'" class="px-3 py-1.5 text-[11px] font-semibold rounded-lg transition-all">Volume (Kg)</button>
            </div>
        </div>
        <div id="domesticTrendChart" class="w-full h-72"></div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xs border border-gray-200/80 dark:border-gray-800 overflow-hidden">
        <div class="px-6 py-4.5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/60 dark:bg-gray-800/40">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-table text-primary text-sm"></i>
                <h3 class="text-xs font-bold text-gray-900 dark:text-white tracking-wider uppercase">Target Sales Domestic 12 Bulan (Pivot Grid)</h3>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-[11px] text-gray-500 dark:text-gray-400 italic hidden lg:inline">Input nilai QTY & Revenue untuk update ASP otomatis</span>
                <button type="button" @click="saveChanges()" class="px-4.5 py-2.5 bg-primary hover:bg-primary/90 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-2 active:scale-[0.98]">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto scrollbar-thin max-h-[680px]">
            <table class="w-full text-left text-[11px] border-collapse min-w-[3400px] whitespace-nowrap">
                <thead class="bg-gray-100/90 dark:bg-gray-800/90 text-gray-700 dark:text-gray-300 font-bold tracking-wider border-b border-gray-300 dark:border-gray-700 sticky top-0 z-20">
                    <tr>
                        <th class="px-3 py-3.5 border-r border-gray-300 dark:border-gray-700 w-12 text-center sticky left-0 z-30 bg-gray-100 dark:bg-gray-800" rowspan="2">No.</th>
                        <th class="px-3 py-3.5 border-r border-gray-300 dark:border-gray-700 min-w-[95px] text-center sticky left-12 z-30 bg-gray-100 dark:bg-gray-800" rowspan="2">CHANNEL</th>
                        <th class="px-3 py-3.5 border-r border-gray-300 dark:border-gray-700 min-w-[160px]" rowspan="2">KEY PRODUCT</th>
                        <th class="px-3 py-3.5 border-r border-gray-300 dark:border-gray-700 min-w-[130px]" rowspan="2">CODE INV</th>
                        <th class="px-3 py-3.5 border-r border-gray-300 dark:border-gray-700 min-w-[280px]" rowspan="2">NAME PRODUCT</th>
                        <template x-for="(month, idx) in monthNames" :key="month">
                            <th class="px-3 py-2.5 border-r border-gray-300 dark:border-gray-700 text-center" :class="idx % 2 === 0 ? 'bg-sky-50/70 dark:bg-gray-800/80' : 'bg-gray-100/70 dark:bg-gray-800/40'" colspan="3" x-text="month"></th>
                        </template>
                        <th class="px-3 py-2.5 border-l-2 border-primary/40 bg-primary/10 dark:bg-primary/20 text-center font-extrabold text-primary dark:text-white" colspan="3">ANNUAL TOTAL</th>
                    </tr>
                    <tr class="bg-gray-50 dark:bg-gray-800 text-[10px]">
                        <?php for ($i = 0; $i < 12; $i++): ?>
                            <th class="px-2 py-2 border-r border-gray-200 dark:border-gray-700 w-[88px] text-center bg-gray-50/80 dark:bg-gray-800">QTY (Kg)</th>
                            <th class="px-2 py-2 border-r border-gray-200 dark:border-gray-700 w-[120px] text-center bg-gray-50/80 dark:bg-gray-800">REVENUE (Rp)</th>
                            <th class="px-2 py-2 border-r border-gray-300 dark:border-gray-700 w-[96px] text-center bg-gray-100/50 dark:bg-gray-800/60">ASP/kg</th>
                        <?php endfor; ?>
                        <th class="px-2 py-2 border-r border-gray-300 dark:border-gray-700 w-[120px] text-center bg-primary/10 text-primary font-bold">TOT QTY</th>
                        <th class="px-2 py-2 border-r border-gray-300 dark:border-gray-700 w-28 text-center bg-primary/10 text-primary font-bold">TOT REV</th>
                        <th class="px-2 py-2 border-r border-gray-300 dark:border-gray-700 w-[120px] text-center bg-primary/10 text-primary font-bold">AVG ASP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono">
                    <template x-if="filteredItems.length === 0">
                        <tr>
                            <td colspan="44" class="p-12 text-center text-gray-400 dark:text-gray-500">
                                <i class="fa-solid fa-inbox text-3xl mb-3"></i>
                                <p class="font-semibold text-gray-600 dark:text-gray-300 text-xs">Belum ada data budget domestic</p>
                                <p class="text-[11px] text-gray-400 mt-1">Upload data via tab <strong>Upload Data</strong> lalu <strong>Process Summary SKU</strong>.</p>
                            </td>
                        </tr>
                    </template>
                    <template x-for="(row, i) in groupedFlatRows.filter(row => row.kind === 'item')" :key="'f-'+i">
                        <tr :class="row.kind === 'subtotal' ? 'bg-amber-50/80 dark:bg-amber-950/30 border-y border-amber-200 dark:border-amber-900/50 font-bold text-amber-900 dark:text-amber-200' : 'hover:bg-sky-50/50 dark:hover:bg-gray-800/60 transition-colors'">
                            <template x-if="row.kind === 'subtotal'">
                                <td class="px-3 py-2.5 text-center sticky left-0 z-10 bg-amber-100 dark:bg-amber-900/40" colspan="2" x-text="row.group.channel"></td>
                            </template>
                            <template x-if="row.kind === 'subtotal'">
                                <td class="p-2" colspan="3">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-layer-group text-xs text-amber-600"></i>
                                        <span x-text="'SUBTOTAL CHANNEL ' + row.group.channel"></span>
                                        <span class="text-[10px] text-amber-700 dark:text-amber-400 font-sans font-normal" x-text="'(' + row.group.items.length + ' items)'"></span>
                                    </span>
                                </td>
                            </template>
                            <template x-if="row.kind === 'item'">
                                <td class="px-3 py-2.5 text-center border-r border-gray-200 dark:border-gray-800 sticky left-0 z-10 bg-white dark:bg-gray-900 text-gray-500" x-text="i + 1"></td>
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
                                <td :class="row.kind === 'subtotal' ? 'px-2 py-2 text-right border-r border-amber-200/60 dark:border-amber-900/40' + (col.k === 'asp' ? ' bg-amber-100/40 dark:bg-amber-950/40' : '') : 'px-2 py-2 border-r border-gray-200 dark:border-gray-800' + (col.k === 'asp' ? ' bg-slate-50/70 dark:bg-gray-800/40 text-right font-medium text-gray-700 dark:text-gray-300' : '')">
                                    <template x-if="row.kind === 'subtotal'">
                                        <span class="block text-right" x-text="col.k === 'qty' ? formatNumber(row.group.subtotal.monthly[col.m].qty) : col.k === 'rev' ? formatNumber(row.group.subtotal.monthly[col.m].revenue) : formatNumber(calculateASP(row.group.subtotal.monthly[col.m].revenue, row.group.subtotal.monthly[col.m].qty, row.group.channel))"></span>
                                    </template>
                                    <template x-if="row.kind === 'item' && col.k === 'qty'">
                                        <input type="number" x-model.number="row.item.monthly[col.m].qty" @input="recalculateRow(row.item)" class="w-full text-right px-2 py-1.5 text-[11px] border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-primary focus:bg-white dark:focus:bg-gray-900 transition-all">
                                    </template>
                                    <template x-if="row.kind === 'item' && col.k === 'rev'">
                                        <input type="number" x-model.number="row.item.monthly[col.m].revenue" @input="recalculateRow(row.item)" class="w-full text-right px-2 py-1.5 text-[11px] border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold focus:ring-1 focus:ring-emerald-500 focus:bg-white dark:focus:bg-gray-900 transition-all">
                                    </template>
                                    <template x-if="row.kind === 'item' && col.k === 'asp'">
                                        <span class="block text-right px-1" x-text="formatNumber(calculateASP(row.item.monthly[col.m].revenue, row.item.monthly[col.m].qty, row.item.id_channel))"></span>
                                    </template>
                                </td>
                            </template>
                            <template x-if="row.kind === 'subtotal'">
                                <td class="px-3 py-2.5 text-right border-r border-amber-300 font-extrabold bg-amber-100 dark:bg-amber-900/50" x-text="formatNumber(row.group.subtotal.total_qty)"></td>
                                <td class="px-3 py-2.5 text-right border-r border-amber-300 font-extrabold text-emerald-700 dark:text-emerald-400 bg-amber-100 dark:bg-amber-900/50" x-text="formatNumber(row.group.subtotal.total_revenue)"></td>
                                <td class="px-3 py-2.5 text-right font-extrabold bg-amber-100 dark:bg-amber-900/50" x-text="formatNumber(calculateASP(row.group.subtotal.total_revenue, row.group.subtotal.total_qty, row.group.channel))"></td>
                            </template>
                            <template x-if="row.kind === 'item'">
                                <td class="px-3 py-2.5 border-l-2 border-primary/30 border-r border-gray-200 dark:border-gray-800 bg-primary/5 dark:bg-gray-800 text-right font-bold text-gray-900 dark:text-white" x-text="formatNumber(row.item.total_qty)"></td>
                                <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800 bg-primary/5 dark:bg-gray-800 text-right font-bold text-emerald-600 dark:text-emerald-400" x-text="formatNumber(row.item.total_revenue)"></td>
                                <td class="px-3 py-2.5 bg-primary/5 dark:bg-gray-800 text-right font-bold text-amber-600 dark:text-amber-400" x-text="formatNumber(calculateASP(row.item.total_revenue, row.item.total_qty, row.item.id_channel))"></td>
                            </template>
                        </tr>
                    </template>
                    <tr class="bg-primary/10 dark:bg-primary/20 font-extrabold text-gray-900 dark:text-white border-t-2 border-primary/30 text-xs">
                        <td class="p-3 text-center sticky left-0 z-10 bg-primary/20 dark:bg-primary/30" colspan="5">GRAND TOTAL ALL CHANNELS</td>
                        <template x-for="col in metricCols" :key="'gt-'+col.m+'-'+col.k">
                            <td class="px-3 py-2.5 text-right border-r border-primary/20" :class="col.k === 'asp' ? 'bg-primary/15 dark:bg-primary/25' : ''" x-text="col.k === 'qty' ? formatNumber(grandTotal.monthly[col.m].qty) : col.k === 'rev' ? formatNumber(grandTotal.monthly[col.m].revenue) : formatNumber(calculateASP(grandTotal.monthly[col.m].revenue, grandTotal.monthly[col.m].qty))"></td>
                        </template>
                        <td class="p-3 text-right border-r border-primary/30 font-mono text-sm font-extrabold" x-text="formatNumber(grandTotal.total_qty)"></td>
                        <td class="p-3 text-right border-r border-primary/30 font-mono text-sm font-extrabold text-emerald-600 dark:text-emerald-400" x-text="formatNumber(grandTotal.total_revenue)"></td>
                        <td class="p-3 text-right font-mono text-sm font-extrabold text-amber-600 dark:text-amber-400" x-text="formatNumber(calculateASP(grandTotal.total_revenue, grandTotal.total_qty))"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>