<div class="tab-pane" id="delivery_cust" role="tabpanel">
    <div class="callout bg-info disabled color-palette" style="padding-bottom: 15px; cursor: pointer;">
        <table class="table table-borderless table-sm">
            <tr>
                <td><b>Channel</b></td>
                <td>
                    <select id="ch" class="form-control input-sm" style="width: 200px;" onchange="cari_cust_claim();">
                        <option value="">- Pilih -</option>
                        <option value="DOM">DOMESTIC (YTI)</option>
                        <option value="EXP">INTERNATIONAL</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button style="display:none;" id="proses_depres" onclick="proses_depres();" type="button" class="btn btn-xs btn-warning">
                        <i class="fa fa-refresh"></i> <b>PROCESS TO OPEX SELLING</b>
                    </button>
                </td>
            </tr>					
        </table>
    </div>

    <table id="cust_claim" class="table table-bordered table-hover table-striped compactx" style="width:100%">
        <thead>
            <tr>
                <th nowrap>Category</th>
                <th nowrap>%</th>
                <th class="text-center" nowrap>JAN</th>
                <th class="text-center" nowrap>FEB</th>
                <th class="text-center" nowrap>MAR</th>
                <th class="text-center" nowrap>APR</th>
                <th class="text-center" nowrap>MAY</th>
                <th class="text-center" nowrap>JUN</th>
                <th class="text-center" nowrap>JUL</th>
                <th class="text-center" nowrap>AUG</th>
                <th class="text-center" nowrap>SEP</th>
                <th class="text-center" nowrap>OCT</th>
                <th class="text-center" nowrap>NOV</th>
                <th class="text-center" nowrap>DEC</th>
                <th class="text-center" nowrap>TOTAL</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>	
</div>