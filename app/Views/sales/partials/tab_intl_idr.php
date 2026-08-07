<?php $dollars = empty($dollars) ? "0" : $dollars; ?>
<div class="tab-pane" id="m_upload_idr" role="tabpanel">
    <div class="callout bg-info disabled color-palette" style="padding-bottom: 15px; cursor: pointer;">
        <table class="table table-borderless table-sm" style="width:50%;">
            <tr>
                <td><b>Rate US$</b></td>
                <td><input id="usd" type="text" value="<?= number_format((float)$dollars, 0); ?>" class="form-control input-sm" style="width:100px; display:inline;"> <b>IDR</b></td>
            </tr>
            <tr>
                <td><b>Rate Baht Thailand</b></td>
                <td><input id="baht" type="text" value="<?= number_format((float)$baht, 0); ?>" class="form-control input-sm" style="width:100px; display:inline;"> <b>IDR</b></td>
            </tr>
            <tr>
                <td><b>Rate Ringgit Malaysia</b></td>
                <td><input id="ringgit" type="text" value="<?= number_format((float)$ringgit, 0); ?>" class="form-control input-sm" style="width:100px; display:inline;"> <b>IDR</b></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button id="IDR_cari_data_domestic" onclick="IDR_cari_data_domestic();" type="button" class="btn btn-warning btn-sm"><i class="fa fa-cogs"></i> Process</button>
                </td>
            </tr>					
        </table>
    </div>				

    <table id="IDR_master_product" class="table table-bordered table-hover table-striped compactx" style="width:100%">
        <thead>
            <tr>
                <th rowspan="2" nowrap>No.</th>
                <th rowspan="2" nowrap>CODE INV</th>
                <th rowspan="2" nowrap>KEY PRODUCT</th>
                <th rowspan="2" nowrap>CODE INV</th>
                <th rowspan="2" nowrap>NAME PRODUCT</th>
                <th rowspan="2" nowrap>CURRENCY</th>
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