<?php
/* =====================================================================
 * foh/partials/detail_entry_modal_content.php — KONTEN form detail item
 * sub-account FOH untuk GLOBAL MODAL (vanilla JS, tanpa Alpine).
 *
 * Dimuat via endpoint: GET foh/detail-entry-modal
 *   ?entry_data_id=&id_coa=&dept=
 *
 * Fitur: dynamic rows, paste-from-Excel (13 kolom: nama + 12 bulan),
 * simpan via foh/saveDetailItems. Setelah sukses:
 *   Modal.close() + dispatch event 'foh-detail-saved' (halaman me-reload matriks).
 * ===================================================================== */
$entryDataId = (int) ($entryDataId ?? 0);
$idCoa       = (string) ($idCoa ?? '');
$dept        = (string) ($dept ?? '');
$itemsJson   = (string) ($itemsJson ?? '[]');
?>

<div id="fohDetailEntryScope">
    <div class="rounded-2xl border border-blue-200 bg-blue-50/70 p-3.5 text-xs text-blue-800 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-300">
        <i class="fa-solid fa-lightbulb mr-1 text-blue-600"></i>
        <strong>Tip:</strong> Copy 13 kolom (Detail Item + 12 bulan) dari Excel lalu paste di salah satu kolom untuk mengisi otomatis satu baris atau lebih.
    </div>

    <div class="overflow-x-auto max-h-96 rounded-2xl border border-gray-200/80 dark:border-gray-800 overflow-hidden shadow-xs mt-4">
        <table class="w-full text-left text-xs border-collapse min-w-[1100px]">
            <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                <tr class="bg-[#2F3185] text-white font-semibold">
                    <th class="px-3.5 py-3 border-r border-white/20 min-w-[200px] text-white font-semibold">Detail Item</th>
                    <?php foreach (['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'] as $mLabel): ?>
                        <th class="px-2 py-3 border-r border-white/20 text-center min-w-[75px] text-white font-semibold"><?= $mLabel ?></th>
                    <?php endforeach; ?>
                    <th class="px-2 py-3 text-center w-12 text-white font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody id="fohDetailRows" class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs"></tbody>
        </table>
    </div>

    <div class="flex items-center justify-between pt-4">
        <button type="button" id="fohDetailAddRow" class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition-all dark:bg-emerald-950/40 dark:text-emerald-400 active:scale-[0.98]">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Item</span>
        </button>
        <div class="flex gap-2">
            <button type="button" id="fohDetailCancel" class="rounded-xl border border-gray-300 dark:border-gray-700 px-5 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">Batal</button>
            <button type="button" id="fohDetailSave" class="rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-5 py-2.5 text-xs font-semibold text-white shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98]">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Detail</span>
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    var scope = document.getElementById('fohDetailEntryScope');
    if (!scope) return;

    var ENTRY_DATA_ID = <?= json_encode($entryDataId) ?>;
    var ID_COA        = <?= json_encode($idCoa) ?>;
    var DEPT          = <?= json_encode($dept) ?>;
    var EXISTING      = <?= $itemsJson ?>;
    var MONTH_KEYS    = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

    var tbody   = document.getElementById('fohDetailRows');
    var addBtn  = document.getElementById('fohDetailAddRow');
    var cancelB = document.getElementById('fohDetailCancel');
    var saveB   = document.getElementById('fohDetailSave');

    function newRowData() {
        var row = { nama_barang: '' };
        MONTH_KEYS.forEach(function (k) { row[k] = 0; });
        return row;
    }

    function buildRow(data) {
        var tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/60 dark:hover:bg-gray-800/40';

        // Kolom nama item
        var tdName = document.createElement('td');
        tdName.className = 'p-2 border-r border-gray-200 dark:border-gray-800 font-sans';
        var nameInput = document.createElement('input');
        nameInput.type = 'text';
        nameInput.placeholder = 'Nama Detail Item...';
        nameInput.value = data.nama_barang || '';
        nameInput.className = 'w-full rounded-xl border border-gray-300 dark:border-gray-700 px-3 py-1.5 text-xs focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:outline-none dark:bg-gray-800 dark:text-white';
        tdName.appendChild(nameInput);
        tr.appendChild(tdName);

        // 12 kolom bulanan
        MONTH_KEYS.forEach(function (mk) {
            var td = document.createElement('td');
            td.className = 'p-2 border-r border-gray-200 dark:border-gray-800';
            var input = document.createElement('input');
            input.type = 'number';
            input.step = '0.01';
            input.value = Number(data[mk] || 0);
            input.dataset.month = mk;
            input.className = 'w-full text-right rounded-xl border border-gray-300 dark:border-gray-700 px-2 py-1.5 text-xs focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:outline-none dark:bg-gray-800 dark:text-white font-mono';
            input.addEventListener('paste', handlePaste);
            td.appendChild(input);
            tr.appendChild(td);
        });

        // Kolom aksi (hapus)
        var tdAct = document.createElement('td');
        tdAct.className = 'p-2 text-center';
        var delBtn = document.createElement('button');
        delBtn.type = 'button';
        delBtn.title = 'Hapus Baris';
        delBtn.className = 'text-rose-500 hover:text-rose-700 p-1 transition-colors';
        delBtn.innerHTML = '<i class="fa-solid fa-trash-can text-xs"></i>';
        delBtn.addEventListener('click', function () {
            if (tbody.rows.length > 1) tr.remove();
        });
        tdAct.appendChild(delBtn);
        tr.appendChild(tdAct);

        return tr;
    }

    function addRow(data) {
        var tr = buildRow(data || newRowData());
        tbody.appendChild(tr);
        return tr;
    }

    /**
     * Paste dari Excel: 13 kolom (Detail Item + 12 bulan).
     * Satu kolom saja -> isi cell biasa. Multi kolom/baris -> isi dari
     * kolom nama + jan..dec, menambah baris otomatis bila perlu.
     */
    function handlePaste(event) {
        event.preventDefault();
        var text = (event.clipboardData || window.clipboardData).getData('text');
        if (!text) return;

        var lines = text.split('\n').filter(function (l) { return l.trim() !== ''; });
        if (lines.length === 0) return;

        var input = event.target;
        var tr = input.closest('tr');
        var rowIdx = Array.prototype.indexOf.call(tbody.rows, tr);
        var firstCols = lines[0].split('\t');

        // Paste satu cell (tanpa tab)
        if (firstCols.length <= 1) {
            input.value = parseFloat(firstCols[0]) || 0;
            return;
        }

        for (var i = 0; i < lines.length; i++) {
            var cols = lines[i].split('\t');
            if (cols.length < 2) continue;

            var targetIdx = rowIdx + i;
            while (targetIdx >= tbody.rows.length) {
                addRow();
            }

            var targetRow = tbody.rows[targetIdx];
            var nameInput = targetRow.querySelector('input[type="text"]');
            var monthInputs = targetRow.querySelectorAll('input[type="number"]');

            if (cols[0] && cols[0].trim()) {
                nameInput.value = cols[0].trim();
            }
            for (var c = 1; c <= 12 && c < cols.length; c++) {
                monthInputs[c - 1].value = parseFloat(cols[c].replace(/,/g, '')) || 0;
            }
        }
    }

    function collectItems() {
        var items = [];
        Array.prototype.forEach.call(tbody.rows, function (tr) {
            var name = (tr.querySelector('input[type="text"]').value || '').trim();
            if (!name) return;
            var payload = { nama_barang: name };
            tr.querySelectorAll('input[type="number"]').forEach(function (inp) {
                payload[inp.dataset.month] = Number(inp.value) || 0;
            });
            items.push(payload);
        });
        return items;
    }

    function save() {
        var items = collectItems();
        if (items.length === 0) {
            window.showToast('error', 'Tidak ada item untuk disimpan.');
            return;
        }

        saveB.disabled = true;
        window.ypFetch('<?= base_url('foh/saveDetailItems') ?>', {
            entry_data_id: ENTRY_DATA_ID,
            id_coa: ID_COA,
            dept: DEPT,
            items: JSON.stringify(items)
        }).then(function (res) {
            saveB.disabled = false;
            if (res.status === 'success') {
                window.showToast('success', res.message || 'Detail item berhasil disimpan!');
                window.dispatchEvent(new CustomEvent('foh-detail-saved'));
                if (window.Modal) window.Modal.close();
            } else {
                window.showToast('error', res.message || 'Gagal menyimpan detail item.');
            }
        }).catch(function (e) {
            saveB.disabled = false;
            console.error('Error saving:', e);
            window.showToast('error', 'Gagal menyimpan detail item.');
        });
    }

    // Init rows
    if (Array.isArray(EXISTING) && EXISTING.length > 0) {
        EXISTING.forEach(function (it) { addRow(it); });
    } else {
        addRow();
    }

    addBtn.addEventListener('click', function () { addRow(); });
    cancelB.addEventListener('click', function () { if (window.Modal) window.Modal.close(); });
    saveB.addEventListener('click', save);
})();
</script>
