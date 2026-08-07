<div class="tab-pane" id="m_download_data" role="tabpanel">
    <div class="callout bg-info disabled color-palette" style="padding-bottom: 15px; cursor: pointer;">
        <table class="table table-borderless table-sm">
            <tr>
                <td><b>Key Product</b></td>
                <td>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><b>Global - Volume</b></td>
                            <td><input id="gw_volume" type="text" value="<?= $datax_13; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>GUMMY - Volume</b></td>
                            <td><input id="gummy_volume" type="text" value="<?= $datax_7; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>BOLI - Volume</b></td>
                            <td><input id="boli_volume" type="text" value="<?= $datax_9; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>EXTRUDER - Volume</b></td>
                            <td><input id="ext_volume" type="text" value="<?= $datax_11; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                        </tr>
                        <tr>
                            <td><b>Global - ASP</b></td>
                            <td><input id="gw_asp" type="text" value="<?= $datax_14; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>GUMMY - ASP</b></td>
                            <td><input id="gummy_asp" type="text" value="<?= $datax_8; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>BOLI - ASP</b></td>
                            <td><input id="boli_asp" type="text" value="<?= $datax_10; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>EXTRUDER - ASP</b></td>
                            <td><input id="ext_asp" type="text" value="<?= $datax_12; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td><b>Channel</b></td>
                <td>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><b>GT - Volume</b></td>
                            <td><input id="gt_volume" type="text" value="<?= $datax_1; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>MT - Volume</b></td>
                            <td><input id="mt_volume" type="text" value="<?= $datax_3; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>OEM - Volume</b></td>
                            <td><input id="oem_volume" type="text" value="<?= $datax_5; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                        </tr>
                        <tr>
                            <td><b>GT - ASP</b></td>
                            <td><input id="gt_asp" type="text" value="<?= $datax_2; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>MT - ASP</b></td>
                            <td><input id="mt_asp" type="text" value="<?= $datax_4; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>OEM - ASP</b></td>
                            <td><input id="oem_asp" type="text" value="<?= $datax_6; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button id="cari_data_domestic" onclick="cari_data_domestic();" type="button" class="btn btn-warning btn-sm"><i class="fa fa-cogs"></i> Process</button>
                </td>
            </tr>					
        </table>
    </div>

    <table id="domestic_report" class="table table-bordered table-hover table-striped compactx" style="width:100%">
        <thead>
            <tr>
                <th rowspan="2" nowrap>No.</th>
                <th rowspan="2" nowrap>CODE INV</th>
                <th rowspan="2" nowrap>KEY PRODUCT</th>
                <th rowspan="2" nowrap>CODE INV</th>
                <th rowspan="2" nowrap>NAME PRODUCT</th>
                <?php 
                $months = ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER', 'TOTAL'];
                foreach($months as $m): 
                ?>
                    <th colspan="3" class="text-center" nowrap><?= $m; ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php foreach($months as $m): ?>
                    <th class="text-center">QTY</th>
                    <th class="text-center">REVENUE</th>
                    <th class="text-center">ASP/kg</th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>