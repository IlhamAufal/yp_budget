<div class="tab-pane" id="m_upload_data" role="tabpanel">
    <div class="callout bg-info disabled color-palette" style="padding-bottom: 15px; cursor: pointer;">
        <table class="table table-borderless table-sm">
            <tr>
                <td><b>Key Product</b></td>
                <td>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><b>Global - Volume</b></td>
                            <td><input id="EX_gw_volume" type="text" value="<?= $EX_datax_13; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>GUMMY - Volume</b></td>
                            <td><input id="EX_gummy_volume" type="text" value="<?= $EX_datax_7; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>BOLI - Volume</b></td>
                            <td><input id="EX_boli_volume" type="text" value="<?= $EX_datax_9; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>EXTRUDER - Volume</b></td>
                            <td><input id="EX_ext_volume" type="text" value="<?= $EX_datax_11; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                        </tr>
                        <tr>
                            <td><b>Global - ASP</b></td>
                            <td><input id="EX_gw_asp" type="text" value="<?= $EX_datax_14; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>GUMMY - ASP</b></td>
                            <td><input id="EX_gummy_asp" type="text" value="<?= $EX_datax_8; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>BOLI - ASP</b></td>
                            <td><input id="EX_boli_asp" type="text" value="<?= $EX_datax_10; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                            <td><b>EXTRUDER - ASP</b></td>
                            <td><input id="EX_ext_asp" type="text" value="<?= $EX_datax_12; ?>" class="form-control input-sm" style="width:60px; display:inline;"> %</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button id="EX_cari_data_domestic" onclick="EX_cari_data_domestic();" type="button" class="btn btn-warning btn-sm"><i class="fa fa-cogs"></i> Process</button>
                </td>
            </tr>					
        </table>
    </div>				

    <table id="EX_master_product" class="table table-bordered table-hover compactx" style="width:100%">
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