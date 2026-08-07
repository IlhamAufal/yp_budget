<div class="tab-pane active" id="m_product_data" role="tabpanel">
    <div class="callout bg-info disabled color-palette" style="padding-bottom: 15px; cursor: pointer;">
        <table class="table table-borderless table-sm">
            <tr>
                <td><b>Channel</b></td>
                <td>
                    <select id="typex" class="form-control input-sm" style="width: 200px;" onchange="cari_data();">
                        <option value="">- Pilih -</option>
                        <option value="ALL">DOMESTIC & INTERNATIONAL</option>
                    </select>
                </td>
            </tr>			
        </table>
    </div>

    <div id="container" style="margin-bottom: 20px;"></div>

    <table id="master_product" class="table table-bordered table-hover compactx" style="width:100%">
        <thead>
            <tr>
                <th rowspan="2" nowrap>No.</th>
                <th rowspan="2" nowrap>CODE INV</th>
                <th rowspan="2" nowrap>KEY PRODUCT</th>
                <th rowspan="2" nowrap>CODE INV</th>
                <th rowspan="2" nowrap>NAME PRODUCT</th>
                <?php 
                $months = ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER', 'TOTAL'];
                foreach($months as $index => $m): 
                    $bg = ($index % 2 == 0) ? 'bg-jan' : 'bg-feb';
                ?>
                    <th colspan="3" class="text-center <?= $bg; ?>" nowrap><?= $m; ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php foreach($months as $index => $m): 
                    $bg = ($index % 2 == 0) ? 'bg-jan' : 'bg-feb';
                ?>
                    <th class="text-center <?= $bg; ?>">QTY</th>
                    <th class="text-center <?= $bg; ?>">REVENUE</th>
                    <th class="text-center <?= $bg; ?>">ASP/kg</th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody></tbody>
    </table>	
</div>