<div class="tab-pane" id="reclass" role="tabpanel">
    <div class="callout bg-info disabled color-palette" style="padding-bottom: 15px; cursor: pointer;">
        <table class="table table-borderless table-sm" style="width:30%;">
            <tr>
                <td><b>Data</b></td>
                <td>
                    <select onchange="cari_data_reclass();" id="typex_reclass" class="form-control input-sm">
                        <option value="">- Pilih -</option>
                        <option value="1">Entry Reclass</option>
                    </select>
                </td>
            </tr>	
        </table>
    </div>

    <table id="table_sales" class="table table-bordered table-hover compacty" style="width:100%">
        <thead>
            <tr>
                <th rowspan="2" class="all text-left">Header</th>
                <th rowspan="2" class="all text-left">Category</th>
                <th colspan="12" class="all text-center">Sales Revenue (IDR Mio)</th>
                <th rowspan="2" class="all text-right">Total</th>
            </tr>
            <tr>
                <th class="text-right">JAN</th>
                <th class="text-right">FEB</th>
                <th class="text-right">MAR</th>
                <th class="text-right">APR</th>
                <th class="text-right">MAY</th>
                <th class="text-right">JUN</th>
                <th class="text-right">JUL</th>
                <th class="text-right">AUG</th>
                <th class="text-right">SEP</th>
                <th class="text-right">OCT</th>
                <th class="text-right">NOV</th>
                <th class="text-right">DEC</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <td colspan="15" class="text-center">
                    <div class="col-sm-6 col-sm-offset-3">
                        <button id="btnSaveReclass" type="button" class="btn btn-block btn-success btn-sm">SAVE DATA</button>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
</div>