<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=OPEX_SELLING_REPORT_" . strtoupper($type ?? 'ALL') . "_" . date('Ymd_His') . ".xls");
?>
<table border="1">
    <thead>
        <tr style="background-color: #004085; color: #ffffff;">
            <th>MAIN ACCOUNT</th>
            <th>COST CENTER</th>
            <th>DESCRIPTION</th>
            <th>JAN</th><th>FEB</th><th>MAR</th><th>APR</th><th>MAY</th><th>JUN</th>
            <th>JUL</th><th>AUG</th><th>SEP</th><th>OCT</th><th>NOV</th><th>DEC</th>
            <th>TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($rows)) : foreach ($rows as $r) : ?>
            <tr>
                <td><?= esc($r['main_account'] ?? ''); ?></td>
                <td><?= esc($r['cost_center'] ?? ''); ?></td>
                <td><?= esc($r['cost_center_desc'] ?? ''); ?></td>
                <?php for ($i = 1; $i <= 12; $i++) : ?>
                    <td align="right"><?= number_format((float)($r['isi_' . $i] ?? 0), 2); ?></td>
                <?php endfor; ?>
                <td align="right"><b><?= number_format((float)($r['isi_tot'] ?? 0), 2); ?></b></td>
            </tr>
        <?php endforeach; endif; ?>
    </tbody>
</table>