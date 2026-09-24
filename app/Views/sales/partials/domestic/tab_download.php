<?php
$channelCount = count($channels ?? []);
?>
<div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-6">
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white">Download Template Excel Sales Domestic</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unduh template dinamis berdasarkan <?= $channelCount ?> channel target untuk pengisian data target Volume dan Revenue.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <?php foreach ($channels ?? [] as $ch):
            $code = (string) ($ch['channel_code'] ?? '');
            $name = (string) ($ch['channel_name'] ?? $code);
        ?>
            <div class="p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 hover:bg-white dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-700 hover:shadow-md transition-all flex flex-col justify-between gap-5">
                <div class="space-y-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 shadow-xs">
                        <i class="fa-solid fa-file-excel text-base"></i>
                    </span>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                            <?= esc($code) ?> - <?= esc($name) ?>
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                            Template target sales channel <?= esc($name) ?>.
                        </p>
                    </div>
                </div>

                <div>
                    <button type="button" @click="downloadChannelTemplate('<?= esc($code) ?>')" class="px-4.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                        <i class="fa-solid fa-download"></i>
                        <span>Download Bundle</span>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="p-4 rounded-xl bg-sky-50 dark:bg-gray-800/80 border border-sky-100 dark:border-gray-700 space-y-2 text-xs text-sky-900 dark:text-sky-200">
        <h5 class="font-bold flex items-center gap-1.5 text-sky-800 dark:text-sky-300">
            <i class="fa-solid fa-circle-info"></i> Petunjuk Pengisian Template Excel:
        </h5>
        <ul class="list-disc list-inside space-y-1 text-gray-600 dark:text-gray-300">
            <li>Pilih channel target yang sesuai untuk mengunduh template Excel khusus channel tersebut.</li>
            <li>Jangan mengubah format atau urutan kolom Kode Produk (<code class="font-mono bg-white dark:bg-gray-900 px-1 py-0.5 rounded text-gray-800 dark:text-gray-200">CODE INV</code>).</li>
            <li>Pastikan angka yang dimasukkan berupa nilai numerik tanpa simbol mata uang atau pemisah ribuan titik.</li>
            <li>Setelah pengisian selesai, gunakan menu <span class="font-bold">Upload Data</span> untuk mengunggah berkas.</li>
        </ul>
    </div>
</div>
