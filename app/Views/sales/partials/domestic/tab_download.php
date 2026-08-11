<?php
$channels = [
    [
        'code'      => 'GT',
        'name'      => 'General Trade',
        'desc'      => 'Template target sales untuk channel General Trade (Pasar Tradisional & Grosir).',
        'icon'      => 'fa-solid fa-store',
        'bg_icon'   => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
        'badge_cls' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
        'btn_cls'   => 'bg-emerald-600 hover:bg-emerald-700 text-white',
    ],
    [
        'code'      => 'MT',
        'name'      => 'Modern Trade',
        'desc'      => 'Template target sales untuk Modern Trade (Supermarket, Hypermarket & Minimarket).',
        'icon'      => 'fa-solid fa-cart-shopping',
        'bg_icon'   => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
        'badge_cls' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800',
        'btn_cls'   => 'bg-blue-600 hover:bg-blue-700 text-white',
    ],
    [
        'code'      => 'OEM',
        'name'      => 'Original Equipment Mfg',
        'desc'      => 'Template target sales untuk kemitraan B2B & Original Equipment Manufacturing.',
        'icon'      => 'fa-solid fa-industry',
        'bg_icon'   => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400',
        'badge_cls' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800',
        'btn_cls'   => 'bg-amber-600 hover:bg-amber-700 text-white',
    ],
    [
        'code'      => 'ECOM',
        'name'      => 'E-Commerce',
        'desc'      => 'Template target sales untuk kanal digital, official store, dan marketplace online.',
        'icon'      => 'fa-solid fa-globe',
        'bg_icon'   => 'bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400',
        'badge_cls' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-800',
        'btn_cls'   => 'bg-purple-600 hover:bg-purple-700 text-white',
    ],
    [
        'code'      => 'YTI',
        'name'      => 'Yupi Trading International',
        'desc'      => 'Template target sales untuk entitas trading dan distribusi ekspor afiliasi.',
        'icon'      => 'fa-solid fa-plane-departure',
        'bg_icon'   => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
        'badge_cls' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800',
        'btn_cls'   => 'bg-rose-600 hover:bg-rose-700 text-white',
    ],
];
?>
<div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-6">
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fa-solid fa-file-excel text-emerald-600 dark:text-emerald-400"></i>
            Download Template Excel Sales Domestic
        </h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unduh template dinamis berdasarkan 5 channel target untuk pengisian data target Volume dan Revenue.</p>
    </div>

    <!-- Grid 2 Cols Channel Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-5">
        <?php foreach ($channels as $ch): ?>
            <div class="p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 hover:bg-white dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-700 hover:shadow-md transition-all flex flex-col justify-between gap-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl <?= $ch['bg_icon'] ?> shadow-xs">
                            <i class="<?= $ch['icon'] ?> text-lg"></i>
                        </span>
                        <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full border <?= $ch['badge_cls'] ?>">
                            <?= esc($ch['code']) ?>
                        </span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                            <?= esc($ch['code']) ?> - <?= esc($ch['name']) ?>
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                            <?= esc($ch['desc']) ?>
                        </p>
                    </div>
                </div>

                <div class="space-y-2.5 pt-2">
                    <button type="button" @click="downloadChannelTemplate('<?= $ch['code'] ?>')" class="w-full px-4 py-2.5 <?= $ch['btn_cls'] ?> text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                        <i class="fa-solid fa-download"></i>
                        <span>Download Bundle (VOL + REV)</span>
                    </button>
                    <div class="flex items-center justify-between px-1 text-[11px] text-gray-500 dark:text-gray-400">
                        <span class="text-[10px] text-gray-400">Unduh terpisah:</span>
                        <div class="flex items-center gap-2.5">
                            <button type="button" @click="downloadSingleTemplate('VOL', '<?= $ch['code'] ?>')" class="font-semibold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                                <i class="fa-solid fa-file-excel text-[10px]"></i> Volume
                            </button>
                            <span class="text-gray-300 dark:text-gray-600">•</span>
                            <button type="button" @click="downloadSingleTemplate('REV', '<?= $ch['code'] ?>')" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                <i class="fa-solid fa-file-excel text-[10px]"></i> Revenue
                            </button>
                        </div>
                    </div>
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
