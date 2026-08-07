/**
 * Sales Summary Controller JS
 */

// Helper: Format Angka
function number_format(number, decimals, dec_point, thousands_sep) {
    var n = !isFinite(+number) ? 0 : +number, 
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return Math.round(n * k) / k;
        },
        s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function parse_num(str) {
    if (str === null || str === undefined) return 0;
    var s = String(str).replace(/,/g, "");
    var n = parseFloat(s);
    return isNaN(n) ? 0 : n;
}

$(function () {
    $('.money').mask('000,000,000,000,000', {reverse: true});
    $('#first').fadeOut('slow'); // Auto hide loader
});

function proses_depres(){
    $.LoadingOverlay("show");
    $.ajax({
        type: 'POST',
        url: defaulturl + "sales/sales_to_opex",
        success: function() {		
            $.LoadingOverlay("hide");
        }
    });
}

// ----------------------------------------------------
// Reclass Tab Handlers & Calculation
// ----------------------------------------------------
var COL_HEADER = 0;
var COL_CATEGORY = 1;
var COL_MONTH_START = 2;
var COL_TOTAL = COL_MONTH_START + 12;
var dt_reclass = null;

function isDiscountRowName(name) {
    return name && String(name).toLowerCase().indexOf("discount") > -1;
}

function buildRow(item){
    var isDiscount = isDiscountRowName(item.named);
    var html = '<tr data-group="' + item.groups + '" data-name="' + item.named + '"' + (isDiscount ? ' class="discount-row"' : '') + '>';
    html += '<td class="text-left" nowrap>' + item.groups + '</td>';
    html += '<td class="text-left" nowrap>' + item.named + '</td>';
    
    for (var m = 1; m <= 12; m++) {
        var val = item['isi_' + m];
        if (isDiscount) {
            html += '<td><input type="text" style="width:80px;" class="discount-input text-right" data-group="' + item.groups + '" data-month="' + m + '" data-row="' + item.named + '" value="' + number_format(val, 2) + '" /></td>';
        } else {
            html += '<td class="num"><span class="val">' + number_format(val, 2) + '</span></td>';
        }
    }
    var rowTot = 0; 
    for (var k = 1; k <= 12; k++) rowTot += parse_num(item['isi_' + k]);
    html += '<td class="num"><span class="row-total">' + number_format(rowTot, 2) + '</span></td></tr>';
    return html;
}

function computeRowTotal(tr){
    var $tr = $(tr);
    var isDiscount = $tr.hasClass("discount-row");
    var sum = 0;
    if (isDiscount) { 
        $tr.find("input.discount-input").each(function(){ sum += parse_num(this.value); }); 
    } else { 
        for (var c = COL_MONTH_START; c < COL_TOTAL; c++) { sum += parse_num($tr.find("td").eq(c).text()); } 
    }
    $tr.find(".row-total").text(number_format(sum, 2));
    return sum;
}

function ensureSingleSubtotalRow(group) {
    var $sub = $("#table_sales tbody tr.subtotal-row").filter(function(){ return $(this).data("group") === group; });
    var $lastGroupRow = $("#table_sales tbody tr").filter(function(){ return $(this).data("group") === group && !$(this).hasClass("subtotal-row"); }).last();
    if ($lastGroupRow.length === 0) return;
        
    if ($sub.length === 0) {
        var row = '<tr class="subtotal-row" data-group="' + group + '"><td class="text-left">' + group + '</td><td class="text-left">Subtotal</td>';
        for (var i = 0; i < 12; i++) row += '<td class="num">0.00</td>';
        row += '<td class="num">0.00</td></tr>';
        $lastGroupRow.after(row);
    } else if (!$sub.prev().is($lastGroupRow)) { 
        $sub.detach(); $lastGroupRow.after($sub); 
    }
}

function computeGroupSums(group) {
    var sums = [0,0,0,0,0,0,0,0,0,0,0,0,0];
    var $rows = $("#table_sales tbody tr").filter(function(){ return $(this).data("group") === group && !$(this).hasClass("subtotal-row"); });
    $rows.each(function(){
        var $tr = $(this);
        var isDisc = $tr.hasClass("discount-row");
        for (var m = 0; m < 12; m++) {
            var val = isDisc ? parse_num($tr.find('input.discount-input[data-month="' + (m+1) + '"]').val()) : parse_num($tr.find("td").eq(COL_MONTH_START + m).text());
            sums[m] += isDisc ? -val : val;
        }
        var rowTot = computeRowTotal($tr);
        sums[12] += isDisc ? -rowTot : rowTot;
    });
    return sums;
}

function renderSubtotals(groupsInOrder) {
    for (var i = 0; i < groupsInOrder.length; i++) {
        var g = groupsInOrder[i];
        ensureSingleSubtotalRow(g);
        var sums = computeGroupSums(g);
        var $sub = $("#table_sales tbody tr.subtotal-row").filter(function(){ return $(this).data("group") === g; });
        for (var m = 0; m < 12; m++) { $sub.find("td").eq(COL_MONTH_START + m).text(number_format(sums[m], 2)); }
        $sub.find("td").eq(COL_TOTAL).text(number_format(sums[12], 2));
    }
}

function attachDiscountHandlers() {
    $("#table_sales").off("input.discount blur.discount")
        .on("input.discount", "input.discount-input", function(){
            var $tr = $(this).closest("tr");
            computeRowTotal($tr);
            renderSubtotals([$tr.data("group")]);
        })
        .on("blur.discount", "input.discount-input", function(){ 
            this.value = number_format(parse_num(this.value), 2); 
        });
}

function cari_data_reclass(){
    var dept = document.getElementById("typex_reclass").value;
    if (!dept) {
        if ($.fn.DataTable.isDataTable("#table_sales")) $("#table_sales").DataTable().clear().draw();
        return;
    }
        
    if ($.fn.DataTable.isDataTable("#table_sales")) $("#table_sales").DataTable().clear().destroy();
    $("#table_sales tbody").empty();
    if ($.LoadingOverlay) $.LoadingOverlay("show");
        
    $.ajax({
        type: "POST",
        url: typeof defaulturl !== "undefined" ? defaulturl + "balance/sales_value_reclass" : "about:blank",
        data: { dept: dept },
        success: function(response){
            var groupsOrder = []; var seen = {};
            $.each(response, function(index, item){
                if (!seen[item.groups]) { seen[item.groups] = true; groupsOrder.push(item.groups); }
                $("#table_sales tbody").append(buildRow(item));
            });
            for (var i = 0; i < groupsOrder.length; i++) ensureSingleSubtotalRow(groupsOrder[i]);
            renderSubtotals(groupsOrder);
            attachDiscountHandlers();
        },
        complete: function(){
            dt_reclass = $("#table_sales").DataTable({
                autoWidth: false, responsive: false, ordering: false, paging: false,
                scrollX: true, scrollY: "55vh", scrollCollapse: true, dom: "Blfrtip",
                buttons: [
                    { extend: "copyHtml5", text: "Copy" },
                    { extend: "csvHtml5",  text: "CSV" },
                    { extend: "excelHtml5", text: "Excel" }
                ],
                columnDefs: [
                    { targets: COL_HEADER, width: 220 },
                    { targets: COL_CATEGORY, width: 240 },
                    { targets: [2,3,4,5,6,7,8,9,10,11,12,13], width: 110 },
                    { targets: COL_TOTAL, width: 140 }
                ],
                initComplete: function(){ this.api().columns.adjust(); },
                drawCallback: function(){ this.api().columns.adjust(); }
            });
            $(window).off("resize.sales").on("resize.sales", function(){ if (dt_reclass) dt_reclass.columns.adjust(); });
            if ($.LoadingOverlay) $.LoadingOverlay("hide");
        }
    });
}

function mapDeptFromGroupName(groupStr) {
    var g = (groupStr || '').toString().toLowerCase();
    if (g.indexOf('domestic') > -1 || g.indexOf('domestik') > -1) return 600;
    if (g.indexOf('international') > -1 || g.indexOf('internasional') > -1) return 700;
    var sel = document.getElementById('typex_reclass');
    var raw = sel ? (sel.value || (sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].text : '')) : '';
    var n = parseInt(raw, 10);
    return isNaN(n) ? raw : n;
}

function saveDiscountReclass(){
    var rows = [];
    $('#table_sales tbody tr.discount-row').each(function(){
        var $tr = $(this);
        var $firstInput = $tr.find('input.discount-input').first();
        var id_dept = mapDeptFromGroupName($firstInput.attr('data-group') || $tr.data('group'));
        
        var rec = {
            id_coa: $tr.data('coa') || null,
            id_dept: id_dept,
            year_code: globalYear,
            notes: "",
            m1:0,m2:0,m3:0,m4:0,m5:0,m6:0,m7:0,m8:0,m9:0,m10:0,m11:0,m12:0,
            total: 0
        };
        for (var m = 1; m <= 12; m++){
            var v = parse_num($tr.find('input.discount-input[data-month="' + m + '"]').val());
            rec['m' + m] = v; rec.total += v;
        }
        rows.push(rec);
    });

    if (!rows.length) { alert('Tidak ada baris Discount untuk disimpan.'); return; }

    $.ajax({
        type: 'POST',
        url: defaulturl + 'balance/save_reclass_discount',
        data: { rows: JSON.stringify(rows) },
        success: function(res){
            var r = typeof res === 'string' ? JSON.parse(res) : res;
            alert('Sukses menyimpan ' + (r.saved || rows.length) + ' record.');
        },
        error: function(){ alert('Gagal menyimpan, coba lagi.'); }
    });
}

$(document).on('click', '#btnSaveReclass', saveDiscountReclass);