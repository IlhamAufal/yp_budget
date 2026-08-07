<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $site_title; ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" type="image" href="<?php img('favicon.ico'); ?>"/>
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="<?php css('bootstrap.min.css'); ?>">
	<link rel="stylesheet" href="<?php css('daterangepicker.css'); ?>">
    <link rel="stylesheet" href="<?php css('bootstrap-datepicker.css'); ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php fontawesome('css/font-awesome.min.css'); ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php ionicons('css/ionicons.min.css'); ?>">
    <!-- Theme style -->
	<link rel="stylesheet" href="<?php css('dna.css'); ?>">
    <link rel="stylesheet" href="<?php css('AdminLTE.min.css'); ?>">
    <link rel="stylesheet" href="<?php css('skin-red.min.css'); ?>">
    <link rel="stylesheet" href="<?php css('jquery-ui/jquery-ui.structure.min.css'); ?>">
    <link rel="stylesheet" href="<?php css('jquery-ui/jquery-ui.theme.min.css'); ?>">
	<link rel="stylesheet" href="<?php datatables('dataTables.bootstrap.css'); ?>">
    <link rel="stylesheet" href="<?php datatables('buttons.dataTables.min.css'); ?>">
    <link rel="stylesheet" href="<?php datatables('fixedColumns_2.dataTables.min.css'); ?>">
	<link rel="stylesheet" href="<?php css('bootsnipp.css'); ?>">
	<link rel="stylesheet" href="<?php css('jquery.steps.css'); ?>">
	<link rel="stylesheet" href="<?php css('select2.min.css'); ?>">
	<link rel="stylesheet" href="<?php css('sweetalert2.min.css'); ?>">
  </head>
	<style>
	.table-striped tbody tr:nth-child(odd) td {
	  background-color: #EEEEEE;
	}
	
	.table-striped tr .group {
	background-color: #FEF9E7;
	}
	
	.compactx th { white-space: nowrap; }
	.compactx {
		font-size: 0.875em;
	}

	.table-striped tbody tr.highlight td { 
		background-color: red;
	}
	tr.collapse.in {
	  display:table-row;
	}
	
	#first
	{
		position:absolute;
		bottom:0;
		left:0;
		right:0;
		top:0;
		opacity:0.9;
		background-color: black;
		color:#ffffff;
		z-index: 999999;
	}

	40% {
		-webkit-transform: translateY(-10px);
		transform: translateY(-10px);
	}

	80% {
		-webkit-transform: translateY(-5px);
		transform: translateY(-5px);
	}
	
	div.dataTables_length {
        float: left;
    }

    div.dataTables_filter {
        float: right;
    }

    div.dataTables_info {
        float: left;
    }

    div.buttons {
        clear: both;
    }

    div.dt-button-collection {
        width: 180px;
    }
	
    </style>
    <body class="hold-transition skin-red fixed sidebar-mini">
  	<div id="first">
    <h1 style="text-align: center; bottom: 120px; text-shadow: 2px 2px #000000;" >Please Wait </br></br><div class="dna">
		<?php
		$x = 1;
		while($x <= 20) {
		echo "<div class='ele'></div>";
		$x++;
		}
		?>
		</div></h1>
	</div>
    <div class="wrapper">
		<?php $this->load->view('main_header/main_header'); ?>
        <?php $this->load->view('side_bar/side_bar_v3'); ?>
        <div class="content-wrapper">
        <?php $this->load->view('breadcrumbs/breadcrumbs'); ?>
			<section class="content">
			<div class="box box-success" style="text-align: left;">
			<ul id="tabs" class="nav nav-tabs">
				  <li class="active"><a href="#m_product_data" data-toggle="tab"><b>Summary</b></a></li>
				  <li><a href="#m_download_data" data-toggle="tab"><b>Domestic</b></a></li>
				  <li><a href="#m_upload_data" data-toggle="tab"><b>INTL (VALAS)</b></a></li>
				  <li><a href="#m_upload_idr" data-toggle="tab"><b>INTL (IDR)</b></a></li>
				  <li><a href="#delivery_cust" data-toggle="tab"><b>Delivery Exp & Customer Claim</b></a></li>
				  <li><a href="#key_product" data-toggle="tab"><b>Report Key Product</b></a></li>
				  <li><a href="#reclass" data-toggle="tab"><b>Reclass A&P</b></a></li>
			</ul>
			
			<div class="tab-content" id="myTabContent">
			
			<div class="tab-pane" id="reclass" role="tabpanel" aria-labelledby="contact-tab">
				<div class="callout bg-info disabled color-palette" onmouseover="style.cursor = 'pointer';" style="padding-bottom: 15px;">
				<table class="table table-borderless table-sm" style="width:30%;">
				<tr>
					<td><b>Data</b></td>
					<td><select onchange="cari_data_reclass();" id="typex_reclass">
						<option value="">- Pilih -</option>
						<option value="1">Entry Reclass</option>
					</select></td>
				</tr>	
							
				</table>
				</div>
				<table id="table_sales" class="table table-bordered table-hover compacty" style="width:100%">
				<thead>
					<tr>
						<th rowspan="2"  class="all text-left">Header</th>
						<th rowspan="2"  class="all text-left">Category</th>
						<th colspan="12" class="all text-center">Sales Revenue (IDR Mio)</th>
						<th rowspan="2"  class="all text-right">Total</th>
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
					<tbody>
					</tbody>
				<tfoot>
					<tr>
						<td colspan="15" align="center">
						<center>
						<div class="col-sm-6" style="text-align:center">
							<button id="btnSaveReclass" type="button" class="btn btn-block btn-success btn-sm">SAVE DATA</button>
						</div>
						</center>
						</td>
					</tr>
				</tfoot>
				</table>

			</div>
			
			<div class="tab-pane" id="key_product" role="tabpanel" aria-labelledby="contact-tab">
			<div class="callout bg-info disabled color-palette" onmouseover="style.cursor = 'pointer';" style="padding-bottom: 15px;">
				<table class="table table-borderless table-sm">
				<tr>
					<td><b>Channel</b></td>
					<td><select id="chxx" onchange="cari_key_product();" >
						<option value="">- Pilih -</option>
						<option value="DOM">DOMESTIC (YTI)</option>
						<option value="EXP">INTERNATIONAL</option>
					</select></td>
				</tr>
							
				</table>
			</div>
			
				<table id="master_product_key"  class="table table-bordered table-hover compactx" style="width:100%">
                    <thead>
                      <tr>
						<th rowspan="2" nowrap>No.</th>
						<th rowspan="2" nowrap>KEY PRODUCT</th>
						<th colspan="3" style="background-color:#EBF5FB;" class='text-center' nowrap>JANUARY</th>
						<th colspan="3" style="background-color:#D6EAF8;" class='text-center' nowrap>FEBUARY</th>
						<th colspan="3" style="background-color:#EBF5FB;" class='text-center' nowrap>MARCH</th>
						<th colspan="3" style="background-color:#D6EAF8;" class='text-center' nowrap>APRIL</th>
						<th colspan="3" style="background-color:#EBF5FB;" class='text-center' nowrap>MAY</th>
						<th colspan="3" style="background-color:#D6EAF8;" class='text-center' nowrap>JUNE</th>
						<th colspan="3" style="background-color:#EBF5FB;" class='text-center' nowrap>JULY</th>
						<th colspan="3" style="background-color:#D6EAF8;" class='text-center' nowrap>AUGUST</th>
						<th colspan="3" style="background-color:#EBF5FB;" class='text-center' nowrap>SEPTEMBER</th>
						<th colspan="3" style="background-color:#D6EAF8;" class='text-center' nowrap>OCTOBER</th>
						<th colspan="3" style="background-color:#EBF5FB;" class='text-center' nowrap>NOVEMBER</th>
						<th colspan="3" style="background-color:#D6EAF8;" class='text-center' nowrap>DECEMBER</th>
						<th colspan="3" style="background-color:#EBF5FB;" class='text-center' nowrap>TOTAL</th>
					  </tr>
					  <tr>
						<th style="background-color:#EBF5FB;" class='text-center'>QTY</th>
						<th style="background-color:#EBF5FB;" class='text-center'>REVENUE</th>
						<th style="background-color:#EBF5FB;" class='text-center'>ASP/kg</th>
						<th style="background-color:#D6EAF8;" class='text-center'>QTY</th>
						<th style="background-color:#D6EAF8;" class='text-center'>REVENUE</th>
						<th style="background-color:#D6EAF8;" class='text-center'>ASP/kg</th>
						<th style="background-color:#EBF5FB;" class='text-center'>QTY</th>
						<th style="background-color:#EBF5FB;" class='text-center'>REVENUE</th>
						<th style="background-color:#EBF5FB;" class='text-center'>ASP/kg</th>
						<th style="background-color:#D6EAF8;" class='text-center'>QTY</th>
						<th style="background-color:#D6EAF8;" class='text-center'>REVENUE</th>
						<th style="background-color:#D6EAF8;" class='text-center'>ASP/kg</th>
						<th style="background-color:#EBF5FB;" class='text-center'>QTY</th>
						<th style="background-color:#EBF5FB;" class='text-center'>REVENUE</th>
						<th style="background-color:#EBF5FB;" class='text-center'>ASP/kg</th>
						<th style="background-color:#D6EAF8;" class='text-center'>QTY</th>
						<th style="background-color:#D6EAF8;" class='text-center'>REVENUE</th>
						<th style="background-color:#D6EAF8;" class='text-center'>ASP/kg</th>
						<th style="background-color:#EBF5FB;" class='text-center'>QTY</th>
						<th style="background-color:#EBF5FB;" class='text-center'>REVENUE</th>
						<th style="background-color:#EBF5FB;" class='text-center'>ASP/kg</th>
						<th style="background-color:#D6EAF8;" class='text-center'>QTY</th>
						<th style="background-color:#D6EAF8;" class='text-center'>REVENUE</th>
						<th style="background-color:#D6EAF8;" class='text-center'>ASP/kg</th>
						<th style="background-color:#EBF5FB;" class='text-center'>QTY</th>
						<th style="background-color:#EBF5FB;" class='text-center'>REVENUE</th>
						<th style="background-color:#EBF5FB;" class='text-center'>ASP/kg</th>
						<th style="background-color:#D6EAF8;" class='text-center'>QTY</th>
						<th style="background-color:#D6EAF8;" class='text-center'>REVENUE</th>
						<th style="background-color:#D6EAF8;" class='text-center'>ASP/kg</th>
						<th style="background-color:#EBF5FB;" class='text-center'>QTY</th>
						<th style="background-color:#EBF5FB;" class='text-center'>REVENUE</th>
						<th style="background-color:#EBF5FB;" class='text-center'>ASP/kg</th>
						<th style="background-color:#D6EAF8;" class='text-center'>QTY</th>
						<th style="background-color:#D6EAF8;" class='text-center'>REVENUE</th>
						<th style="background-color:#D6EAF8;" class='text-center'>ASP/kg</th>
						<th style="background-color:#EBF5FB;" class='text-center'>QTY</th>
						<th style="background-color:#EBF5FB;" class='text-center'>REVENUE</th>
						<th style="background-color:#EBF5FB;" class='text-center'>ASP/kg</th>
					   </tr>
					</thead>
					<tbody>
					
					</tbody>
				</table>	
				
			</div>
			
			<div class="tab-pane" id="delivery_cust" role="tabpanel" aria-labelledby="contact-tab">
			<div class="callout bg-info disabled color-palette" onmouseover="style.cursor = 'pointer';" style="padding-bottom: 15px;">
				<table class="table table-borderless table-sm">
				<tr>
					<td><b>Channel</b></td>
					<td><select id="ch" onchange="cari_cust_claim();" >
						<option value="">- Pilih -</option>
						<option value="DOM">DOMESTIC (YTI)</option>
						<option value="EXP">INTERNATIONAL</option>
					</select></td>
				</tr>
				<tr>
					<td></td>
					<td>
					<button style="display:none;" id="proses_depres" onclick="proses_depres();" type="button" class="btn btn-xs btn-warning"><i class="fa fa-refresh"></i> <b>PROCCESS TO OPEX SELLING</b></button>
					</td>
				</tr>					
				</table>
			</div>
			
				<table id="cust_claim"  class="table table-bordered table-hover table-striped compactx" style="width:100%">
                    <thead>
                      <tr>
						<th nowrap>Category</th>
						<th nowrap>%</th>
						<th class='text-center' nowrap>JAN</th>
						<th class='text-center' nowrap>FEB</th>
						<th class='text-center' nowrap>MAR</th>
						<th class='text-center' nowrap>APR</th>
						<th class='text-center' nowrap>MAY</th>
						<th class='text-center' nowrap>JUN</th>
						<th class='text-center' nowrap>JUL</th>
						<th class='text-center' nowrap>AUG</th>
						<th class='text-center' nowrap>SEP</th>
						<th class='text-center' nowrap>OCT</th>
						<th class='text-center' nowrap>NOV</th>
						<th class='text-center' nowrap>DEC</th>
						<th class='text-center' nowrap>TOTAL</th>
					  </tr>
					</thead>
					<tbody>
					
					</tbody>
				</table>	
			</div>
			
			<div class="tab-pane active" id="m_product_data" role="tabpanel" aria-labelledby="contact-tab">
			<div class="callout bg-info disabled color-palette" onmouseover="style.cursor = 'pointer';" style="padding-bottom: 15px;">
				<table class="table table-borderless table-sm">
				<tr>
					<td><b>Channel</b></td>
					<td><select id="typex" onchange="cari_data();">
						<option value="">- Pilih -</option>
						<option value="ALL">DOMESTIC & INTERNATIONAL</option>
					</select></td>
				</tr>			
				</table>
			</div>
				<!--
				Table yang sudah di upload actualnya
				-->
				<div id="container"></div>
				<table id="master_product"  class="table table-bordered table-hover compactx" style="width:100%">
                    <thead>
                      <tr>
						<th rowspan="2" nowrap>No.</th>
						<th rowspan="2" nowrap>CODE INV</th>
						<th rowspan="2" nowrap>KEY PRODUCT</th>
						<th rowspan="2" nowrap>CODE INV</th>
						<th rowspan="2" nowrap>NAME PRODUCT	</th>
						<th colspan="3" style="background-color:#EBF5FB;" class='text-center' nowrap>JANUARY</th>
						<th colspan="3" style="background-color:#D6EAF8;" class='text-center' nowrap>FEBUARY</th>
						<th colspan="3" style="background-color:#EBF5FB;" class='text-center' nowrap>MARCH</th>
						<th colspan="3" style="background-color:#D6EAF8;" class='text-center' nowrap>APRIL</th>
						<th colspan="3" style="background-color:#EBF5FB;" class='text-center' nowrap>MAY</th>
						<th colspan="3" style="background-color:#D6EAF8;" class='text-center' nowrap>JUNE</th>
						<th colspan="3" style="background-color:#EBF5FB;" class='text-center' nowrap>JULY</th>
						<th colspan="3" style="background-color:#D6EAF8;" class='text-center' nowrap>AUGUST</th>
						<th colspan="3" style="background-color:#EBF5FB;" class='text-center' nowrap>SEPTEMBER</th>
						<th colspan="3" style="background-color:#D6EAF8;" class='text-center' nowrap>OCTOBER</th>
						<th colspan="3" style="background-color:#EBF5FB;" class='text-center' nowrap>NOVEMBER</th>
						<th colspan="3" style="background-color:#D6EAF8;" class='text-center' nowrap>DECEMBER</th>
						<th colspan="3" style="background-color:#EBF5FB;" class='text-center' nowrap>TOTAL</th>
					  </tr>
					  <tr>
						<th style="background-color:#EBF5FB;" class='text-center'>QTY</th>
						<th style="background-color:#EBF5FB;" class='text-center'>REVENUE</th>
						<th style="background-color:#EBF5FB;" class='text-center'>ASP/kg</th>
						<th style="background-color:#D6EAF8;" class='text-center'>QTY</th>
						<th style="background-color:#D6EAF8;" class='text-center'>REVENUE</th>
						<th style="background-color:#D6EAF8;" class='text-center'>ASP/kg</th>
						<th style="background-color:#EBF5FB;" class='text-center'>QTY</th>
						<th style="background-color:#EBF5FB;" class='text-center'>REVENUE</th>
						<th style="background-color:#EBF5FB;" class='text-center'>ASP/kg</th>
						<th style="background-color:#D6EAF8;" class='text-center'>QTY</th>
						<th style="background-color:#D6EAF8;" class='text-center'>REVENUE</th>
						<th style="background-color:#D6EAF8;" class='text-center'>ASP/kg</th>
						<th style="background-color:#EBF5FB;" class='text-center'>QTY</th>
						<th style="background-color:#EBF5FB;" class='text-center'>REVENUE</th>
						<th style="background-color:#EBF5FB;" class='text-center'>ASP/kg</th>
						<th style="background-color:#D6EAF8;" class='text-center'>QTY</th>
						<th style="background-color:#D6EAF8;" class='text-center'>REVENUE</th>
						<th style="background-color:#D6EAF8;" class='text-center'>ASP/kg</th>
						<th style="background-color:#EBF5FB;" class='text-center'>QTY</th>
						<th style="background-color:#EBF5FB;" class='text-center'>REVENUE</th>
						<th style="background-color:#EBF5FB;" class='text-center'>ASP/kg</th>
						<th style="background-color:#D6EAF8;" class='text-center'>QTY</th>
						<th style="background-color:#D6EAF8;" class='text-center'>REVENUE</th>
						<th style="background-color:#D6EAF8;" class='text-center'>ASP/kg</th>
						<th style="background-color:#EBF5FB;" class='text-center'>QTY</th>
						<th style="background-color:#EBF5FB;" class='text-center'>REVENUE</th>
						<th style="background-color:#EBF5FB;" class='text-center'>ASP/kg</th>
						<th style="background-color:#D6EAF8;" class='text-center'>QTY</th>
						<th style="background-color:#D6EAF8;" class='text-center'>REVENUE</th>
						<th style="background-color:#D6EAF8;" class='text-center'>ASP/kg</th>
						<th style="background-color:#EBF5FB;" class='text-center'>QTY</th>
						<th style="background-color:#EBF5FB;" class='text-center'>REVENUE</th>
						<th style="background-color:#EBF5FB;" class='text-center'>ASP/kg</th>
						<th style="background-color:#D6EAF8;" class='text-center'>QTY</th>
						<th style="background-color:#D6EAF8;" class='text-center'>REVENUE</th>
						<th style="background-color:#D6EAF8;" class='text-center'>ASP/kg</th>
						<th style="background-color:#EBF5FB;" class='text-center'>QTY</th>
						<th style="background-color:#EBF5FB;" class='text-center'>REVENUE</th>
						<th style="background-color:#EBF5FB;" class='text-center'>ASP/kg</th>
					   </tr>
					</thead>
					<tbody>
					
					</tbody>
				</table>	
			</div>
			
			<div class="tab-pane" id="m_upload_data" role="tabpanel" aria-labelledby="contact-tab">
				<div class="callout bg-info disabled color-palette" onmouseover="style.cursor = 'pointer';" style="padding-bottom: 15px;">
					<table class="table table-borderless table-sm">
					<tr>
						<td><b>Key Product</b></td>
						<td>
						<table class="table table-borderless table-sm">
						<tr>
							<td><b>Global - Volume</b> </td>
							<td><input id="EX_gw_volume"    type="text" value="<?=$EX_datax_13;?>" style="width:60px;"> %</td>
							<td><b>GUMMY - Volume</b> </td>              
							<td><input id="EX_gummy_volume" type="text" value="<?=$EX_datax_7;?>" style="width:60px;"> %</td>
							<td><b>BOLI - Volume</b> </td>               
							<td><input id="EX_boli_volume"  type="text" value="<?=$EX_datax_9;?>" style="width:60px;"> %</td>
							<td><b>EXTRUDER - Volume</b> </td>          
							<td><input id="EX_ext_volume"   type="text" value="<?=$EX_datax_11;?>" style="width:60px;"> %</td>
						</tr>
						<tr>
							<td><b>Global - ASP</b></td>
							<td><input id="EX_gw_asp" type="text" value="<?=$EX_datax_14;?>" style="width:60px;"> %</td>
							<td><b>GUMMY - ASP</b></td>                           
							<td><input id="EX_gummy_asp" type="text" value="<?=$EX_datax_8;?>" style="width:60px;"> %</td>
							<td><b>BOLI - ASP</b></td>                            
							<td><input id="EX_boli_asp" type="text" value="<?=$EX_datax_10;?>" style="width:60px;"> %</td>
							<td><b>EXTRUDER - ASP</b></td>                        
							<td><input id="EX_ext_asp" type="text" value="<?=$EX_datax_12;?>" style="width:60px;"> %</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
						<button id="EX_cari_data_domestic" onclick="EX_cari_data_domestic();" type="button" class="btn btn-warning"><i class="fa fa-cogs"></i> Process</button>
						</td>
					</tr>					
					</table>
				</div>				
				<!--
				Table yang sudah di upload actualnya
				-->
				<table id="EX_master_product"  class="table table-bordered table-hover compactx" style="width:100%">
                    <thead>
                      <tr>
						<th rowspan="2" nowrap>No.</th>
						<th rowspan="2" nowrap>CODE INV</th>
						<th rowspan="2" nowrap>KEY PRODUCT</th>
						<th rowspan="2" nowrap>CODE INV</th>
						<th rowspan="2" nowrap>NAME PRODUCT	</th>
						<th colspan="3" class='text-center' nowrap>JANUARY</th>
						<th colspan="3" class='text-center' nowrap>FEBUARY</th>
						<th colspan="3" class='text-center' nowrap>MARCH</th>
						<th colspan="3" class='text-center' nowrap>APRIL</th>
						<th colspan="3" class='text-center' nowrap>MAY</th>
						<th colspan="3" class='text-center' nowrap>JUNE</th>
						<th colspan="3" class='text-center' nowrap>JULY</th>
						<th colspan="3" class='text-center' nowrap>AUGUST</th>
						<th colspan="3" class='text-center' nowrap>SEPTEMBER</th>
						<th colspan="3" class='text-center' nowrap>OCTOBER</th>
						<th colspan="3" class='text-center' nowrap>NOVEMBER</th>
						<th colspan="3" class='text-center' nowrap>DECEMBER</th>
						<th colspan="3" class='text-center' nowrap>TOTAL</th>
					  </tr>
					  <tr>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
					   </tr>
					</thead>
					<tbody>
					
					</tbody>
				</table>	
			</div>
			<?php
			if(empty($dollars)){
				$dollars = "0";
			}
			?>
			<div class="tab-pane" id="m_upload_idr" role="tabpanel" aria-labelledby="contact-tab">
				<div class="callout bg-info disabled color-palette" onmouseover="style.cursor = 'pointer';" style="padding-bottom: 15px;">
					<table class="table table-borderless table-sm">
					<tr>
						<td>
						<table class="table table-borderless table-sm" style="width:50%;">
						<tr>
							<td><b>Rate US$</b> </td>
							<td><input id="usd" type="text" value="<?=number_format($dollars,0);?>" style="width:100px;"> <b>IDR</b></td>
						</tr>
						<tr>
							<td><b>Rate Baht Thailand</b> </td>
							<td><input id="baht" type="text" value="<?=number_format($baht,0);?>" style="width:100px;"> <b>IDR</b></td>
						</tr>
						<tr>
							<td><b>Rate Ringgit Malaysia</b> </td>
							<td><input id="ringgit" type="text" value="<?=number_format($ringgit,0);?>" style="width:100px;"> <b>IDR</b></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<button id="IDR_cari_data_domestic" onclick="IDR_cari_data_domestic();" type="button" class="btn btn-warning"><i class="fa fa-cogs"></i> Process</button>
						</td>
					</tr>					
					</table>
				</div>				
				<!--
				Table yang sudah di upload actualnya
				-->
				<table id="IDR_master_product"  class="table table-bordered table-hover table-striped compactx" style="width:100%">
                    <thead>
                      <tr>
						<th rowspan="2" nowrap>No.</th>
						<th rowspan="2" nowrap>CODE INV</th>
						<th rowspan="2" nowrap>KEY PRODUCT</th>
						<th rowspan="2" nowrap>CODE INV</th>
						<th rowspan="2" nowrap>NAME PRODUCT	</th>
						<th rowspan="2" nowrap>CURRENCY	</th>
						<th colspan="3" class='text-center' nowrap>JANUARY</th>
						<th colspan="3" class='text-center' nowrap>FEBUARY</th>
						<th colspan="3" class='text-center' nowrap>MARCH</th>
						<th colspan="3" class='text-center' nowrap>APRIL</th>
						<th colspan="3" class='text-center' nowrap>MAY</th>
						<th colspan="3" class='text-center' nowrap>JUNE</th>
						<th colspan="3" class='text-center' nowrap>JULY</th>
						<th colspan="3" class='text-center' nowrap>AUGUST</th>
						<th colspan="3" class='text-center' nowrap>SEPTEMBER</th>
						<th colspan="3" class='text-center' nowrap>OCTOBER</th>
						<th colspan="3" class='text-center' nowrap>NOVEMBER</th>
						<th colspan="3" class='text-center' nowrap>DECEMBER</th>
						<th colspan="3" class='text-center' nowrap>TOTAL</th>
					  </tr>
					  <tr>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
					   </tr>
					</thead>
					<tbody>
					
					</tbody>
				</table>		
			</div>
			
			<div class="tab-pane" id="m_download_data" role="tabpanel" aria-labelledby="contact-tab">
				<div class="callout bg-info disabled color-palette" onmouseover="style.cursor = 'pointer';" style="padding-bottom: 15px;">
					<table class="table table-borderless table-sm">
					<tr>
						<td><b>Key Product</b></td>
						<td>
						<table class="table table-borderless table-sm">
						<tr>
							<td><b>Global - Volume</b> </td>
							<td><input id="gw_volume"    type="text" value="<?=$datax_13;?>" style="width:60px;"> %</td>
							<td><b>GUMMY - Volume</b> </td>              
							<td><input id="gummy_volume" type="text" value="<?=$datax_7;?>" style="width:60px;"> %</td>
							<td><b>BOLI - Volume</b> </td>               
							<td><input id="boli_volume"  type="text" value="<?=$datax_9;?>" style="width:60px;"> %</td>
							<td><b>EXTRUDER - Volume</b> </td>          
							<td><input id="ext_volume"   type="text" value="<?=$datax_11;?>" style="width:60px;"> %</td>
						</tr>
						<tr>
							<td><b>Global - ASP</b></td>
							<td><input id="gw_asp" type="text" value="<?=$datax_14;?>" style="width:60px;"> %</td>
							<td><b>GUMMY - ASP</b></td>                           
							<td><input id="gummy_asp" type="text" value="<?=$datax_8;?>" style="width:60px;"> %</td>
							<td><b>BOLI - ASP</b></td>                            
							<td><input id="boli_asp" type="text" value="<?=$datax_10;?>" style="width:60px;"> %</td>
							<td><b>EXTRUDER - ASP</b></td>                        
							<td><input id="ext_asp" type="text" value="<?=$datax_12;?>" style="width:60px;"> %</td>
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
							<td><input id="gt_volume" type="text" value="<?=$datax_1;?>" style="width:60px;"> %</td>
							<td><b>MT - Volume</b></td>                 
							<td><input id="mt_volume" type="text" value="<?=$datax_3;?>" style="width:60px;"> %</td>
							<td><b>OEM - Volume</b></td>                
							<td><input id="oem_volume" type="text" value="<?=$datax_5;?>" style="width:60px;"> %</td>
						</tr>                                           
						<tr>                                            
							<td><b>GT - ASP</b></td>
							<td><input id="gt_asp" type="text" value="<?=$datax_2;?>" style="width:60px;"> %</td>
							<td><b>MT - ASP</b></td>                    
							<td><input id="mt_asp" type="text" value="<?=$datax_4;?>" style="width:60px;"> %</td>
							<td><b>OEM - ASP</b></td>                   
							<td><input id="oem_asp" type="text" value="<?=$datax_6;?>" style="width:60px;"> %</td>
						</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
						<button id="cari_data_domestic" onclick="cari_data_domestic();" type="button" class="btn btn-warning"><i class="fa fa-cogs"></i> Process</button>
						</td>
					</tr>					
					</table>
				</div>
				<!--
				Table yang sudah di upload actualnya
				-->
				<table id="domestic_report"  class="table table-bordered table-hover table-striped compactx" style="width:100%">
                    <thead>
                      <tr>
						<th rowspan="2" nowrap>No.</th>
						<th rowspan="2" nowrap>CODE INV</th>
						<th rowspan="2" nowrap>KEY PRODUCT</th>
						<th rowspan="2" nowrap>CODE INV</th>
						<th rowspan="2" nowrap>NAME PRODUCT	</th>
						<th colspan="3" class='text-center' nowrap>JANUARY</th>
						<th colspan="3" class='text-center' nowrap>FEBUARY</th>
						<th colspan="3" class='text-center' nowrap>MARCH</th>
						<th colspan="3" class='text-center' nowrap>APRIL</th>
						<th colspan="3" class='text-center' nowrap>MAY</th>
						<th colspan="3" class='text-center' nowrap>JUNE</th>
						<th colspan="3" class='text-center' nowrap>JULY</th>
						<th colspan="3" class='text-center' nowrap>AUGUST</th>
						<th colspan="3" class='text-center' nowrap>SEPTEMBER</th>
						<th colspan="3" class='text-center' nowrap>OCTOBER</th>
						<th colspan="3" class='text-center' nowrap>NOVEMBER</th>
						<th colspan="3" class='text-center' nowrap>DECEMBER</th>
						<th colspan="3" class='text-center' nowrap>TOTAL</th>
					  </tr>
					  <tr>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
						<th class='text-center'>QTY</th>
						<th class='text-center'>REVENUE</th>
						<th class='text-center'>ASP/kg</th>
					   </tr>
					</thead>
					<tbody>
					
					</tbody>
				</table>
			</div>
			
			</div>
		    </section>
          </div>
      </div>
	  <div class="modal fade" id="bsModa_unggah_file" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:850px;">
			<div class="modal-content" style="border-radius:18px; padding:10px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="mySmallModalLabel"><b><i class="fa fa-cogs"></i> Upload Budget Sales Domestic</b> <b id="tipes"></b></h4>
			</div>
			<div id="isi_data_unggah" class="modal-body">
			</div>
			<div class="modal-footer">
			</div>
			</div>
		</div>
	  </div>
      <?php $this->load->view('footer/footer'); ?>
      <div class="control-sidebar-bg"></div>
	  <script src="<?php js('jquery-3.5.1.js'); ?>"></script>
	  <script src="<?php js('sweetalert2.all.min.js'); ?>"></script>
      <script src="<?php js('bootstrap.min.js'); ?>"></script>
      <script src="<?php js('jquery.slimscroll.min.js'); ?>"></script>
      <script src="<?php js('fastclick.js'); ?>"></script>
	  <script src="<?php js('jquery.mask.min.js'); ?>"></script>
      <script src="<?php dist('app.min.js'); ?>"></script>
      <script src="<?php dist('demo.js'); ?>"></script>
      <script src="<?php datatables('jquery_2.dataTables.min.js'); ?>"></script>
      <script src="<?php datatables('dataTables.tableTools.nightly.js'); ?>"></script>
      <script src="<?php datatables('dataTables.buttons.min.js'); ?>"></script>
      <script src="<?php datatables('jszip.min.js'); ?>"></script>
      <script src="<?php datatables('pdfmake.min.js'); ?>"></script>
      <script src="<?php datatables('vfs_fonts.js'); ?>"></script>
      <script src="<?php datatables('buttons.html5.min.js'); ?>"></script>
      <script src="<?php dataTables('buttons.colVis.min.js'); ?>"></script>
      <script src="<?php dataTables('dataTables_2.fixedColumns.min.js'); ?>"></script>
      <script src="<?php js('jquery.timepicker.min.js'); ?>"></script>
      <script src="<?php js('jquery-ui.min.js'); ?>"></script>
      <script src="<?php js('moment.min.js'); ?>"></script>
      <script src="<?php js('bootsnipp.js'); ?>"></script>
	  <script src="<?php js('custom.js'); ?>"></script>
	  <script src="<?php js('select2.full.min.js'); ?>"></script>
	  <script src="<?php js('jquery.steps.js'); ?>"></script>
	  <script src="<?php js('jquery.steps.min.js'); ?>"></script>
	  <script src="<?php js('jquery.validate.js'); ?>"></script>
	  <script src="<?php js('jquery.validate.min.js'); ?>"></script>
	  <script src="<?php js('custom.js'); ?>"></script>
	  <script src="<?php js('daterangepicker.js'); ?>"></script>
	  <script src="<?php js('loadingoverlay.min.js'); ?>"></script>
	  <script src="<?php js('sweetalert_new.js'); ?>"></script>
	  <script src="<?php js('highcharts.js'); ?>"></script>
 	  <script src="<?php js('exporting.js'); ?>"></script>
 	  <script src="<?php js('export-data.js'); ?>"></script>
	  <script>
		<?php 
		if($validasi == "NOPE"){
		?>
			// Swal.fire({
				// title: 'PAGE CONFIRMATION',
				// html: "<h4><?=$namax;?> is using this page at this moment.</h4>",
				// type: 'warning',
				// allowOutsideClick: false,
				// confirmButtonColor: '#3085d6',
				// confirmButtonText: 'OK'
			// }).then(function(){
			//window.history.back();
			//});
		<?php 
		}
		?>

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
		
		var defaulturl 		= "<?php echo base_url(); ?>";
	  
	  $(function () {
		$('.money').mask('000,000,000,000,000', {reverse: true});
	  });
	  
	  function hitung(aa){
		var asli  = aa.replace(",", "");
		var hasil = parseInt(asli) * (0.48/1.1);
		$('#hasil').val(number_format(hasil,0,',', ','));
	  }
	  
	  function proses_depres(){
			$.LoadingOverlay("show");
			var default_urls  = "<?php echo base_url(); ?>";
			$.ajax({
				type: 'POST',
				url: default_urls+"sales/sales_to_opex",
				success: function(response) 
				{		
					$.LoadingOverlay("hide");
				}
			});
	  }
	  
	  function cari_data_domestic(){
		var gw_volume		= document.getElementById("gw_volume").value; 
		var gw_asp			= document.getElementById("gw_asp").value; 
		var gummy_volume	= document.getElementById("gummy_volume").value; 
		var gummy_asp		= document.getElementById("gummy_asp").value; 
		var boli_volume		= document.getElementById("boli_volume").value; 
		var boli_asp		= document.getElementById("boli_asp").value; 
		var ext_volume		= document.getElementById("ext_volume").value; 
		var ext_asp			= document.getElementById("ext_asp").value; 
		var gt_volume		= document.getElementById("gt_volume").value; 
		var mt_volume		= document.getElementById("mt_volume").value; 
		var oem_volume		= document.getElementById("oem_volume").value; 
		var gt_asp			= document.getElementById("gt_asp").value; 
		var mt_asp			= document.getElementById("mt_asp").value; 
		var oem_asp			= document.getElementById("oem_asp").value; 
		var i 				= 1;	
		$('#domestic_report tbody').empty();	
		$("#domestic_report").dataTable().fnDestroy();		
		$.LoadingOverlay("show");	
		$.ajax({
			async: false,
			type: 'POST',
			url: defaulturl+"sales/summary_domestic",
			data: 
			{
				gt_volume	:gt_volume		,
				mt_volume	:mt_volume		,
				oem_volume	:oem_volume		,
				gt_asp		:gt_asp			,
				mt_asp		:mt_asp			,
				oem_asp		:oem_asp		,
				gw_volume	:gw_volume		,
				gw_asp		:gw_asp		    ,
				gummy_volume:gummy_volume   ,
				gummy_asp	:gummy_asp	    ,
				boli_volume	:boli_volume	,
				boli_asp	:boli_asp	    ,
				ext_volume	:ext_volume	    ,
				ext_asp		:ext_asp		
			},
			success: function(response) 
			{
				$('#domestic_report tbody').empty();	
				$("#domestic_report").dataTable().fnDestroy();	
				$.each(response,function(index,item){
					if(item.mid_product !== "00"){
					$('#domestic_report tbody').append(
						"<tr>" +
							"<td style='width:3%;'>" + i + "</td>" +
							"<td style='width:15%;'>"+item.id_channel+"</td>" +	
							"<td style='width:15%;'>"+item.key_product+"</td>" +	
							"<td style='width:15%;'>"+item.mid_product+"</td>" +	
							"<td style='width:15%;' nowrap>"+item.product_name+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jan_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jan_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jan_asp,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.feb_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.feb_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.feb_asp,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.mar_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.mar_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.mar_asp,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.apr_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.apr_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.apr_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.may_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.may_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.may_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.jun_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jun_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jun_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.jul_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jul_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jul_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.aug_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.aug_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.aug_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.sep_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.sep_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.sep_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.oct_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.oct_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.oct_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.nov_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.nov_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.nov_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.dec_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.dec_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.dec_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.total_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.total_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.total_asp,2)+"</td>" +
						"</tr>"
					);
					i++;
					}else{
					$('#domestic_report tbody').append(
						"<tr>" +
							"<td style='width:3%;'></td>" +
							"<td style='width:15%;'>"+item.product_name+"</td>" +	
							"<td style='width:15%;'></td>" +	
							"<td style='width:15%;'></td>" +	
							"<td style='width:15%;' nowrap><b>"+item.product_name+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.jan_qty,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.jan_rev,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.jan_asp,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.feb_qty,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.feb_rev,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.feb_asp,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.mar_qty,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.mar_rev,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.mar_asp,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.apr_qty,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.apr_rev,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.apr_asp,2)+"</b></td>" +
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.may_qty,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.may_rev,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.may_asp,2)+"</b></td>" +
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.jun_qty,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.jun_rev,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.jun_asp,2)+"</b></td>" +
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.jul_qty,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.jul_rev,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.jul_asp,2)+"</b></td>" +
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.aug_qty,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.aug_rev,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.aug_asp,2)+"</b></td>" +
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.sep_qty,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.sep_rev,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.sep_asp,2)+"</b></td>" +
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.oct_qty,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.oct_rev,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.oct_asp,2)+"</b></td>" +
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.nov_qty,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.nov_rev,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.nov_asp,2)+"</b></td>" +
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.dec_qty,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.dec_rev,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.dec_asp,2)+"</b></td>" +
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.total_qty,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.total_rev,2)+"</b></td>" +	
							"<td style='width:10%;' class='text-right '><b>"+number_format(item.total_asp,2)+"</b></td>" +
						"</tr>"
					);
					i++;	
					}
				});
			},
			complete: function(){
					var groupColumn = 1;
					var table = $('#domestic_report').DataTable({
						columnDefs: [{ visible: false, targets: groupColumn }],
						scrollY: "470px",
						scrollX:        true,
						scrollCollapse: true,
						paging:         false,
						bSort: 			false,
						searching: 		true,
						fixedColumns:   {
							leftColumns: 5
						},
						dom: 'Bfrtip',
						buttons: [
							{
						extend: 'collection',
							text: 'Export Data',
							buttons: [
								{
									extend: 'copyHtml5',
									text: 'To clipboard'
								},
								{
									extend: 'csvHtml5',
									text: 'CSV'
								},
								{
									extend: 'excelHtml5',
									text: 'Excel'
								},
							]
						}
						],
						drawCallback: function (settings) {
						var api = this.api();
						var rows = api.rows({ page: "all" }).nodes();
						var last = null;
						
						//TOTAL 38 kolom
					
						// Remove the formatting to get integer data for summation
						var intVal = function (i) {
							return typeof i === "string"
							? i.replace(/[\$,]/g, "") * 1
							: typeof i === "number"
							? i
							: 0;
						};
					
						total 	= [];
						total2 	= [];
						total3 	= [];
						total4 	= [];
						total5 	= [];
						total6 	= [];
						total7 	= [];
						total8 	= [];
						total9 	= [];
						total10 = [];
						total11 = [];
						total12 = [];
						total13 = [];
						total14 = [];
						total15 = [];
						total16 = [];
						total17 = [];
						total18 = [];
						total19 = [];
						total20 = [];
						total21 = [];
						total22 = [];
						total23 = [];
						total24 = [];
						total25 = [];
						total26 = [];
						total27 = [];
						total28 = [];
						total29 = [];
						total30 = [];
						total31 = [];
						total32 = [];
						total33 = [];
						total34 = [];
						total35 = [];
						total36 = [];
						total37 = [];
						total38 = [];
						total39 = [];
						
						api
							.column(1, { page: "all" })
							.data()
							.each(function (group, i) {
							group_assoc 	= group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc1 	= "1"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc2 	= "2"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc3 	= "3"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc4 	= "4"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc5 	= "5"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc6 	= "6"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc7 	= "7"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc8 	= "8"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc9 	= "9"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc10 	= "10"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc11 	= "11"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc12 	= "12"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc13 	= "13"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc14 	= "14"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc15 	= "15"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc16 	= "16"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc17 	= "17"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc18 	= "18"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc19 	= "19"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc20	= "20"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc21 	= "21"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc22 	= "22"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc23 	= "23"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc24 	= "24"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc25 	= "25"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc26 	= "26"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc27 	= "27"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc28 	= "28"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc29 	= "29"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc30 	= "30"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc31 	= "31"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc32 	= "32"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc33 	= "33"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc34 	= "34"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc35 	= "35"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc36 	= "36"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc37 	= "37"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc38 	= "38"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc39 	= "39"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
				
							if (typeof total[group_assoc] != "undefined") {
								total[group_assoc] =
									total[group_assoc] + intVal(api.column(5).data()[i]);
							} else {
									total[group_assoc] = intVal(api.column(5).data()[i]);
							}
							
							if (typeof total2[group_assoc1] != "undefined") {
								total2[group_assoc1] =
									total2[group_assoc1] + intVal(api.column(6).data()[i]);
							} else {
									total2[group_assoc1] = intVal(api.column(6).data()[i]);
							}
							
							if (typeof total3[group_assoc2] != "undefined") {
								total3[group_assoc2] =
									total3[group_assoc2] + intVal(api.column(7).data()[i]);
							} else {
									total3[group_assoc2] = intVal(api.column(7).data()[i]);
							}
							
							if (typeof total4[group_assoc3] != "undefined") {
								total4[group_assoc3] =
									total4[group_assoc3] + intVal(api.column(8).data()[i]);
							} else {
									total4[group_assoc3] = intVal(api.column(8).data()[i]);
							}
							
							if (typeof total5[group_assoc4] != "undefined") {
								total5[group_assoc4] =
									total5[group_assoc4] + intVal(api.column(9).data()[i]);
							} else {
									total5[group_assoc4] = intVal(api.column(9).data()[i]);
							}
							
							if (typeof total6[group_assoc5] != "undefined") {
								total6[group_assoc5] =
									total6[group_assoc5] + intVal(api.column(10).data()[i]);
							} else {
									total6[group_assoc5] = intVal(api.column(10).data()[i]);
							}
							
							if (typeof total7[group_assoc6] != "undefined") {
								total7[group_assoc6] =
									total7[group_assoc6] + intVal(api.column(11).data()[i]);
							} else {
									total7[group_assoc6] = intVal(api.column(11).data()[i]);
							}
							
							if (typeof total8[group_assoc7] != "undefined") {
								total8[group_assoc7] =
									total8[group_assoc7] + intVal(api.column(12).data()[i]);
							} else {
									total8[group_assoc7] = intVal(api.column(12).data()[i]);
							}
							
							if (typeof total9[group_assoc8] != "undefined") {
								total9[group_assoc8] =
									total9[group_assoc8] + intVal(api.column(13).data()[i]);
							} else {
									total9[group_assoc8] = intVal(api.column(13).data()[i]);
							}
							
							if (typeof total10[group_assoc9] != "undefined") {
								total10[group_assoc9] =
									total10[group_assoc9] + intVal(api.column(14).data()[i]);
							} else {
									total10[group_assoc9] = intVal(api.column(14).data()[i]);
							}
							
							if (typeof total11[group_assoc10] != "undefined") {
								total11[group_assoc10] =
									total11[group_assoc10] + intVal(api.column(15).data()[i]);
							} else {
									total11[group_assoc10] = intVal(api.column(15).data()[i]);
							}
							
							if (typeof total12[group_assoc11] != "undefined") {
								total12[group_assoc11] =
									total12[group_assoc11] + intVal(api.column(16).data()[i]);
							} else {
									total12[group_assoc11] = intVal(api.column(16).data()[i]);
							}
							
							if (typeof total13[group_assoc12] != "undefined") {
								total13[group_assoc12] =
									total13[group_assoc12] + intVal(api.column(17).data()[i]);
							} else {
									total13[group_assoc12] = intVal(api.column(17).data()[i]);
							}
							
							if (typeof total14[group_assoc13] != "undefined") {
								total14[group_assoc13] =
									total14[group_assoc13] + intVal(api.column(18).data()[i]);
							} else {
									total14[group_assoc13] = intVal(api.column(18).data()[i]);
							}
							
							if (typeof total15[group_assoc14] != "undefined") {
								total15[group_assoc14] =
									total15[group_assoc14] + intVal(api.column(19).data()[i]);
							} else {
									total15[group_assoc14] = intVal(api.column(19).data()[i]);
							}
							
							if (typeof total16[group_assoc15] != "undefined") {
								total16[group_assoc15] =
									total16[group_assoc15] + intVal(api.column(20).data()[i]);
							} else {
									total16[group_assoc15] = intVal(api.column(20).data()[i]);
							}
							
							if (typeof total17[group_assoc16] != "undefined") {
								total17[group_assoc16] =
									total17[group_assoc16] + intVal(api.column(21).data()[i]);
							} else {
									total17[group_assoc16] = intVal(api.column(21).data()[i]);
							}
							
							if (typeof total18[group_assoc17] != "undefined") {
								total18[group_assoc17] =
									total18[group_assoc17] + intVal(api.column(22).data()[i]);
							} else {
									total18[group_assoc17] = intVal(api.column(22).data()[i]);
							}
							
							if (typeof total19[group_assoc18] != "undefined") {
								total19[group_assoc18] =
									total19[group_assoc18] + intVal(api.column(23).data()[i]);
							} else {
									total19[group_assoc18] = intVal(api.column(23).data()[i]);
							}
							
							if (typeof total20[group_assoc19] != "undefined") {
								total20[group_assoc19] =
									total20[group_assoc19] + intVal(api.column(24).data()[i]);
							} else {
									total20[group_assoc19] = intVal(api.column(24).data()[i]);
							}
							
							if (typeof total21[group_assoc20] != "undefined") {
								total21[group_assoc20] =
									total21[group_assoc20] + intVal(api.column(25).data()[i]);
							} else {
									total21[group_assoc20] = intVal(api.column(25).data()[i]);
							}
							
							if (typeof total22[group_assoc21] != "undefined") {
								total22[group_assoc21] =
									total22[group_assoc21] + intVal(api.column(26).data()[i]);
							} else {
									total22[group_assoc21] = intVal(api.column(26).data()[i]);
							}
							
							if (typeof total23[group_assoc22] != "undefined") {
								total23[group_assoc22] =
									total23[group_assoc22] + intVal(api.column(27).data()[i]);
							} else {
									total23[group_assoc22] = intVal(api.column(27).data()[i]);
							}
							
							if (typeof total24[group_assoc23] != "undefined") {
								total24[group_assoc23] =
									total24[group_assoc23] + intVal(api.column(28).data()[i]);
							} else {
									total24[group_assoc23] = intVal(api.column(28).data()[i]);
							}
							
							if (typeof total25[group_assoc24] != "undefined") {
								total25[group_assoc24] =
									total25[group_assoc24] + intVal(api.column(29).data()[i]);
							} else {
									total25[group_assoc24] = intVal(api.column(29).data()[i]);
							}
							
							if (typeof total26[group_assoc25] != "undefined") {
								total26[group_assoc25] =
									total26[group_assoc25] + intVal(api.column(30).data()[i]);
							} else {
									total26[group_assoc25] = intVal(api.column(30).data()[i]);
							}
							
							if (typeof total27[group_assoc26] != "undefined") {
								total27[group_assoc26] =
									total27[group_assoc26] + intVal(api.column(31).data()[i]);
							} else {
									total27[group_assoc26] = intVal(api.column(31).data()[i]);
							}
							
							if (typeof total28[group_assoc27] != "undefined") {
								total28[group_assoc27] =
									total28[group_assoc27] + intVal(api.column(32).data()[i]);
							} else {
									total28[group_assoc27] = intVal(api.column(32).data()[i]);
							}
							
							if (typeof total29[group_assoc28] != "undefined") {
								total29[group_assoc28] =
									total29[group_assoc28] + intVal(api.column(33).data()[i]);
							} else {
									total29[group_assoc28] = intVal(api.column(33).data()[i]);
							}
							
							if (typeof total30[group_assoc29] != "undefined") {
								total30[group_assoc29] =
									total30[group_assoc29] + intVal(api.column(34).data()[i]);
							} else {
									total30[group_assoc29] = intVal(api.column(34).data()[i]);
							}
							
							if (typeof total31[group_assoc30] != "undefined") {
								total31[group_assoc30] =
									total31[group_assoc30] + intVal(api.column(35).data()[i]);
							} else {
									total31[group_assoc30] = intVal(api.column(35).data()[i]);
							}
							
							if (typeof total32[group_assoc31] != "undefined") {
								total32[group_assoc31] =
									total32[group_assoc31] + intVal(api.column(36).data()[i]);
							} else {
									total32[group_assoc31] = intVal(api.column(36).data()[i]);
							}
							
							if (typeof total33[group_assoc32] != "undefined") {
								total33[group_assoc32] =
									total33[group_assoc32] + intVal(api.column(37).data()[i]);
							} else {
									total33[group_assoc32] = intVal(api.column(37).data()[i]);
							}
							
							if (typeof total34[group_assoc33] != "undefined") {
								total34[group_assoc33] =
									total34[group_assoc33] + intVal(api.column(38).data()[i]);
							} else {
									total34[group_assoc33] = intVal(api.column(38).data()[i]);
							}
							
							if (typeof total35[group_assoc34] != "undefined") {
								total35[group_assoc34] =
									total35[group_assoc34] + intVal(api.column(39).data()[i]);
							} else {
									total35[group_assoc34] = intVal(api.column(39).data()[i]);
							}
							
							if (typeof total36[group_assoc35] != "undefined") {
								total36[group_assoc35] =
									total36[group_assoc35] + intVal(api.column(40).data()[i]);
							} else {
									total36[group_assoc35] = intVal(api.column(40).data()[i]);
							}
							
							if (typeof total37[group_assoc36] != "undefined") {
								total37[group_assoc36] =
									total37[group_assoc36] + intVal(api.column(41).data()[i]);
							} else {
									total37[group_assoc36] = intVal(api.column(41).data()[i]);
							}
							
							if (typeof total38[group_assoc37] != "undefined") {
								total38[group_assoc37] =
									total38[group_assoc37] + intVal(api.column(42).data()[i]);
							} else {
									total38[group_assoc37] = intVal(api.column(42).data()[i]);
							}
							
							if (typeof total39[group_assoc38] != "undefined") {
								total39[group_assoc38] =
									total39[group_assoc38] + intVal(api.column(43).data()[i]);
							} else {
									total39[group_assoc38] = intVal(api.column(43).data()[i]);
							}
							
							
							if (group !== "GRAND TOTAL") {
							if (last !== group) {
								$(rows)
								.eq(i)
								.before(
									'<tr style="background-color:#FEF9E7;"><td colspan="4" style="background-color:#FEF9E7;"><b>' +
									group.toUpperCase().replace("GT", "<b style='color:black;'>GT (GENERAL TRADE)</b>").replace("MT", "<b style='color:black;'>MT (MODERN TRADE)</b>").replace("OEM", "<b style='color:black;'>OEM (Original Equipment Manufacture)</b>").replace("YTI", "<b style='color:black;'>YTI (Yupi Trading International)</b>") +
									'</b></td><td class="text-right ' +
									group_assoc +
									'"></td><td class="text-right ' +
									group_assoc1 +      
									'"></td><td class="text-right ' +
									group_assoc2 +      
									'"></td><td class="text-right ' +
									group_assoc3 +      
									'"></td><td class="text-right ' +
									group_assoc4 +      
									'"></td><td class="text-right ' +
									group_assoc5 +      
									'"></td><td class="text-right ' +
									group_assoc6 +      
									'"></td><td class="text-right ' +
									group_assoc7 +      
									'"></td><td class="text-right ' +
									group_assoc8 +      
									'"></td><td class="text-right ' +
									group_assoc9 +      
									'"></td><td class="text-right ' +
									group_assoc10 +     
									'"></td><td class="text-right ' +
									group_assoc11 +     
									'"></td><td class="text-right ' +
									group_assoc12 +     
									'"></td><td class="text-right ' +
									group_assoc13 +     
									'"></td><td class="text-right ' +
									group_assoc14 +     
									'"></td><td class="text-right ' +
									group_assoc15 +     
									'"></td><td class="text-right ' +
									group_assoc16 +     
									'"></td><td class="text-right ' +
									group_assoc17 +     
									'"></td><td class="text-right ' +
									group_assoc18 +     
									'"></td><td class="text-right ' +
									group_assoc19 +     
									'"></td><td class="text-right ' +
									group_assoc20 +     
									'"></td><td class="text-right ' +
									group_assoc21 +     
									'"></td><td class="text-right ' +
									group_assoc22 +     
									'"></td><td class="text-right ' +
									group_assoc23 +     
									'"></td><td class="text-right ' +
									group_assoc24 +     
									'"></td><td class="text-right ' +
									group_assoc25 +     
									'"></td><td class="text-right ' +
									group_assoc26 +     
									'"></td><td class="text-right ' +
									group_assoc27 +     
									'"></td><td class="text-right ' +
									group_assoc28 +     
									'"></td><td class="text-right ' +
									group_assoc29 +     
									'"></td><td class="text-right ' +
									group_assoc30 +     
									'"></td><td class="text-right ' +
									group_assoc31 +     
									'"></td><td class="text-right ' +
									group_assoc32 +     
									'"></td><td class="text-right ' +
									group_assoc33 +     
									'"></td><td class="text-right ' +
									group_assoc34 +     
									'"></td><td class="text-right ' +
									group_assoc35 +     
									'"></td><td class="text-right ' +
									group_assoc36 +     
									'"></td><td class="text-right ' +
									group_assoc37 +     
									'"></td><td class="text-right ' +
									group_assoc38 +
									'"></td></tr>'
								);
					
								last = group;
							}
							}
							});
							
						var REV_VAL			= new Array();
						var QTY_VAL			= new Array();
						
						var REV_VAL2		= new Array();
						var QTY_VAL2		= new Array();
						
						var REV_VAL3		= new Array();
						var QTY_VAL3		= new Array();
						
						var REV_VAL4		= new Array();
						var QTY_VAL4		= new Array();
						
						var REV_VAL5		= new Array();
						var QTY_VAL5		= new Array();
						
						var REV_VAL6		= new Array();
						var QTY_VAL6		= new Array();
						
						var REV_VAL7		= new Array();
						var QTY_VAL7		= new Array();
						
						var REV_VAL8		= new Array();
						var QTY_VAL8		= new Array();
						
						var REV_VAL9		= new Array();
						var QTY_VAL9		= new Array();
						
						var REV_VAL10		= new Array();
						var QTY_VAL10		= new Array();
						
						var REV_VAL11		= new Array();
						var QTY_VAL11		= new Array();
						
						var REV_VAL12		= new Array();
						var QTY_VAL12		= new Array();
						
						var REV_VAL13		= new Array();
						var QTY_VAL13		= new Array();
							
						for (var key in total) {
							$("." + key).html("<b>" + number_format(total[key],2) + "</b>");
							QTY_VAL[key] = intVal($('#domestic_report').find("."+key+"").text());
						}

						for (var key in total2) {
							$("." + key).html("<b>" + number_format(total2[key],2) + "</b>");
							REV_VAL[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total3) {
							if(key.includes("GT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1GT'])/intVal(QTY_VAL['GT'])*1000,2) + "</b>");
							}
							
							if(key.includes("MT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1MT'])/intVal(QTY_VAL['MT'])*1000,2) + "</b>");
							}
							
							if(key.includes("OEM") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1OEM'])/intVal(QTY_VAL['OEM'])*1000,2) + "</b>");
							}
							
							if(key.includes("YTI") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1YTI'])/intVal(QTY_VAL['YTI']),2) + "</b>");
							}
							
							if(key.includes("ECOM") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1ECOM'])/intVal(QTY_VAL['ECOM'])*1000,2) + "</b>");
							}
						}	
		
						for (var key in total4) {
							$("." + key).html("<b>" + number_format(total4[key],2) + "</b>");
							QTY_VAL2[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total5) {
							$("." + key).html("<b>" + number_format(total5[key],2) + "</b>");
							REV_VAL2[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total6) {
							if(key.includes("5GT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4GT'])/intVal(QTY_VAL2['3GT'])*1000,2) + "</b>");
							}
							
							if(key.includes("5MT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4MT'])/intVal(QTY_VAL2['3MT'])*1000,2) + "</b>");
							}
							
							if(key.includes("5OEM") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4OEM'])/intVal(QTY_VAL2['3OEM'])*1000,2) + "</b>");
							}
							
							if(key.includes("5YTI") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4YTI'])/intVal(QTY_VAL2['3YTI']),2) + "</b>");
							}
							
							if(key.includes("5ECOM") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4ECOM'])/intVal(QTY_VAL2['3ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total7) {
							$("." + key).html("<b>" + number_format(total7[key],2) + "</b>");
							QTY_VAL3[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total8) {
							$("." + key).html("<b>" + number_format(total8[key],2) + "</b>");
							REV_VAL3[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total9) {
							if(key.includes("8GT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7GT'])/intVal(QTY_VAL3['6GT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("8MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7MT'])/intVal(QTY_VAL3['6MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("8OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7OEM'])/intVal(QTY_VAL3['6OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("8YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7YTI'])/intVal(QTY_VAL3['6YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("8ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7ECOM'])/intVal(QTY_VAL3['6ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total10) {
							$("." + key).html("<b>" + number_format(total10[key],2) + "</b>");
							QTY_VAL4[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total11) {
							$("." + key).html("<b>" + number_format(total11[key],2) + "</b>");
							REV_VAL4[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total12) {
							if(key.includes("11GT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10GT'])/intVal(QTY_VAL4['9GT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("11MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10MT'])/intVal(QTY_VAL4['9MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("11OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10OEM'])/intVal(QTY_VAL4['9OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("11YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10YTI'])/intVal(QTY_VAL4['9YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("11ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10ECOM'])/intVal(QTY_VAL4['9ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total13) {
							$("." + key).html("<b>" + number_format(total13[key],2) + "</b>");
							QTY_VAL5[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total14) {
							$("." + key).html("<b>" + number_format(total14[key],2) + "</b>");
							REV_VAL5[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total15) {
							if(key.includes("14GT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13GT'])/intVal(QTY_VAL5['12GT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("14MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13MT'])/intVal(QTY_VAL5['12MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("14OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13OEM'])/intVal(QTY_VAL5['12OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("14YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13YTI'])/intVal(QTY_VAL5['12YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("14ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13ECOM'])/intVal(QTY_VAL5['12ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total16) {
							$("." + key).html("<b>" + number_format(total16[key],2) + "</b>");
							QTY_VAL6[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total17) {
							$("." + key).html("<b>" + number_format(total17[key],2) + "</b>");
							REV_VAL6[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total18) {
							if(key.includes("17GT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16GT'])/intVal(QTY_VAL6['15GT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("17MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16MT'])/intVal(QTY_VAL6['15MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("17OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16OEM'])/intVal(QTY_VAL6['15OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("17YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16YTI'])/intVal(QTY_VAL6['15YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("17ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16ECOM'])/intVal(QTY_VAL6['15ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total19) {
							$("." + key).html("<b>" + number_format(total19[key],2) + "</b>");
							QTY_VAL7[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total20) {
							$("." + key).html("<b>" + number_format(total20[key],2) + "</b>");
							REV_VAL7[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total21) {
							if(key.includes("20GT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19GT'])/intVal(QTY_VAL7['18GT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("20MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19MT'])/intVal(QTY_VAL7['18MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("20OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19OEM'])/intVal(QTY_VAL7['18OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("20YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19YTI'])/intVal(QTY_VAL7['18YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("20ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19ECOM'])/intVal(QTY_VAL7['18ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total22) {
							$("." + key).html("<b>" + number_format(total22[key],2) + "</b>");
							QTY_VAL8[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total23) {
							$("." + key).html("<b>" + number_format(total23[key],2) + "</b>");
							REV_VAL8[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total24) {
							if(key.includes("23GT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22GT'])/intVal(QTY_VAL8['21GT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("23MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22MT'])/intVal(QTY_VAL8['21MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("23OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22OEM'])/intVal(QTY_VAL8['21OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("23YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22YTI'])/intVal(QTY_VAL8['21YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("23ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22ECOM'])/intVal(QTY_VAL8['21ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total25) {
							$("." + key).html("<b>" + number_format(total25[key],2) + "</b>");
							QTY_VAL9[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total26) {
							$("." + key).html("<b>" + number_format(total26[key],2) + "</b>");
							REV_VAL9[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total27) {
							if(key.includes("26GT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25GT'])/intVal(QTY_VAL9['24GT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("26MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25MT'])/intVal(QTY_VAL9['24MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("26OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25OEM'])/intVal(QTY_VAL9['24OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("26YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25YTI'])/intVal(QTY_VAL9['24YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("26ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25ECOM'])/intVal(QTY_VAL9['24ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total28) {
							$("." + key).html("<b>" + number_format(total28[key],2) + "</b>");
							QTY_VAL10[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total29) {
							$("." + key).html("<b>" + number_format(total29[key],2) + "</b>");
							REV_VAL10[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total30) {
							if(key.includes("29GT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28GT'])/intVal(QTY_VAL10['27GT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("29MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28MT'])/intVal(QTY_VAL10['27MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("29OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28OEM'])/intVal(QTY_VAL10['27OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("29YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28YTI'])/intVal(QTY_VAL10['27YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("29ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28ECOM'])/intVal(QTY_VAL10['27ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total31) {
							$("." + key).html("<b>" + number_format(total31[key],2) + "</b>");
							QTY_VAL11[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total32) {
							$("." + key).html("<b>" + number_format(total32[key],2) + "</b>");
							REV_VAL11[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total33) {
							if(key.includes("32GT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31GT'])/intVal(QTY_VAL11['30GT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("32MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31MT'])/intVal(QTY_VAL11['30MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("32OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31OEM'])/intVal(QTY_VAL11['30OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("32YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31YTI'])/intVal(QTY_VAL11['30YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("32ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31ECOM'])/intVal(QTY_VAL11['30ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total34) {
							$("." + key).html("<b>" + number_format(total34[key],2) + "</b>");
							QTY_VAL12[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total35) {
							$("." + key).html("<b>" + number_format(total35[key],2) + "</b>");
							REV_VAL12[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total36) {
							if(key.includes("35GT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34GT'])/intVal(QTY_VAL12['33GT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("35MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34MT'])/intVal(QTY_VAL12['33MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("35OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34OEM'])/intVal(QTY_VAL12['33OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("35YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34YTI'])/intVal(QTY_VAL12['33YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("35ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34ECOM'])/intVal(QTY_VAL12['33ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total37) {
							$("." + key).html("<b>" + number_format(total37[key],2) + "</b>");
							QTY_VAL13[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total38) {
							$("." + key).html("<b>" + number_format(total38[key],2) + "</b>");
							REV_VAL13[key] = intVal($('#domestic_report').find("."+key+"").text());
						}
						
						for (var key in total39) {
							if(key.includes("38GT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37GT'])/intVal(QTY_VAL13['36GT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("38MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37MT'])/intVal(QTY_VAL13['36MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("38OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37OEM'])/intVal(QTY_VAL13['36OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("38YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37YTI'])/intVal(QTY_VAL13['36YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("38ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37ECOM'])/intVal(QTY_VAL13['36ECOM'])*1000,2) + "</b>");
							}
						}
						
						}
					});
				$.LoadingOverlay("hide");
				var cnt = $(".dt-buttons").contents();
				
				$(".dt-buttons").replaceWith(cnt);
				var cnta = $("#buttons").contents();
				$("#buttons").replaceWith(cnta);
				
				//BIAR RAPIH
				table.columns.adjust();
			}
		});
	  }
	  
	  function EX_cari_data_domestic(){
		var EX_gw_volume		= document.getElementById("EX_gw_volume").value; 
		var EX_gw_asp			= document.getElementById("EX_gw_asp").value; 
		var EX_gummy_volume		= document.getElementById("EX_gummy_volume").value; 
		var EX_gummy_asp		= document.getElementById("EX_gummy_asp").value; 
		var EX_boli_volume		= document.getElementById("EX_boli_volume").value; 
		var EX_boli_asp			= document.getElementById("EX_boli_asp").value; 
		var EX_ext_volume		= document.getElementById("EX_ext_volume").value; 
		var EX_ext_asp			= document.getElementById("EX_ext_asp").value; 
		var i 					= 1;	
		$('#EX_master_product tbody').empty();	
		$("#EX_master_product").dataTable().fnDestroy();		
		$.LoadingOverlay("show");	
		$.ajax({
			async: false,
			type: 'POST',
			url: defaulturl+"sales/summary_export",
			data: 
			{
				gw_volume	: EX_gw_volume,
				gw_asp		: EX_gw_asp,
				gummy_volume: EX_gummy_volume,
				gummy_asp	: EX_gummy_asp,
				boli_volume	: EX_boli_volume,
				boli_asp	: EX_boli_asp,
				ext_volume	: EX_ext_volume,
				ext_asp		: EX_ext_asp		
			},
		    beforeSend: function() {
				// setting a timeout
				$.LoadingOverlay("show");	
			},
			success: function(response) 
			{
				$.LoadingOverlay("hide");	
				$('#EX_master_product tbody').empty();	
				$("#EX_master_product").dataTable().fnDestroy();	
				$.each(response,function(index,item){		
					$('#EX_master_product tbody').append(
						"<tr>" +
							"<td style='width:3%;'>" + i + "</td>" +
							"<td style='width:15%;'>"+item.id_channel+"</td>" +	
							"<td style='width:15%;'>"+item.key_product+"</td>" +	
							"<td style='width:15%;'>"+item.mid_product+"</td>" +	
							"<td style='width:15%;' nowrap>"+item.product_name+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jan_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jan_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jan_asp,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.feb_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.feb_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.feb_asp,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.mar_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.mar_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.mar_asp,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.apr_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.apr_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.apr_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.may_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.may_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.may_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.jun_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jun_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jun_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.jul_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jul_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jul_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.aug_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.aug_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.aug_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.sep_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.sep_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.sep_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.oct_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.oct_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.oct_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.nov_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.nov_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.nov_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.dec_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.dec_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.dec_asp,2)+"</td>" +
							"<td style='width:10%;' class='text-right '>"+number_format(item.total_qty,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.total_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.total_asp,2)+"</td>" +
						"</tr>"
					);
					i++;
				});
			},
			complete: function(){
					var groupColumn = 1;
					var table = $('#EX_master_product').DataTable({
						columnDefs: [{ visible: false, targets: groupColumn }],
						scrollY: "470px",
						scrollX:        true,
						scrollCollapse: true,
						paging:         false,
						bSort: 			false,
						searching: 		true,
						fixedColumns:   {
							leftColumns: 5
						},
						dom: 'Bfrtip',
						buttons: [
							{
						extend: 'collection',
							text: 'Export Data',
							buttons: [
								{
									extend: 'copyHtml5',
									text: 'To clipboard'
								},
								{
									extend: 'csvHtml5',
									text: 'CSV'
								},
								{
									extend: 'excelHtml5',
									text: 'Excel'
								},
							]
						}
						],
						drawCallback: function (settings) {
						var api = this.api();
						var rows = api.rows({ page: "all" }).nodes();
						var last = null;
						
						//TOTAL 38 kolom
					
						// Remove the formatting to get integer data for summation
						var intVal = function (i) {
							return typeof i === "string"
							? i.replace(/[\$,]/g, "") * 1
							: typeof i === "number"
							? i
							: 0;
						};
					
						total 	= [];
						total2 	= [];
						total3 	= [];
						total4 	= [];
						total5 	= [];
						total6 	= [];
						total7 	= [];
						total8 	= [];
						total9 	= [];
						total10 = [];
						total11 = [];
						total12 = [];
						total13 = [];
						total14 = [];
						total15 = [];
						total16 = [];
						total17 = [];
						total18 = [];
						total19 = [];
						total20 = [];
						total21 = [];
						total22 = [];
						total23 = [];
						total24 = [];
						total25 = [];
						total26 = [];
						total27 = [];
						total28 = [];
						total29 = [];
						total30 = [];
						total31 = [];
						total32 = [];
						total33 = [];
						total34 = [];
						total35 = [];
						total36 = [];
						total37 = [];
						total38 = [];
						total39 = [];
						
						api
							.column(1, { page: "all" })
							.data()
							.each(function (group, i) {
							group_assoc 	= group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc1 	= "1"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc2 	= "2"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc3 	= "3"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc4 	= "4"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc5 	= "5"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc6 	= "6"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc7 	= "7"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc8 	= "8"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc9 	= "9"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc10 	= "10"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc11 	= "11"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc12 	= "12"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc13 	= "13"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc14 	= "14"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc15 	= "15"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc16 	= "16"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc17 	= "17"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc18 	= "18"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc19 	= "19"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc20	= "20"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc21 	= "21"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc22 	= "22"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc23 	= "23"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc24 	= "24"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc25 	= "25"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc26 	= "26"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc27 	= "27"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc28 	= "28"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc29 	= "29"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc30 	= "30"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc31 	= "31"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc32 	= "32"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc33 	= "33"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc34 	= "34"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc35 	= "35"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc36 	= "36"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc37 	= "37"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc38 	= "38"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc39 	= "39"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
				
							if (typeof total[group_assoc] != "undefined") {
								total[group_assoc] =
									total[group_assoc] + intVal(api.column(5).data()[i]);
							} else {
									total[group_assoc] = intVal(api.column(5).data()[i]);
							}
							
							if (typeof total2[group_assoc1] != "undefined") {
								total2[group_assoc1] =
									total2[group_assoc1] + intVal(api.column(6).data()[i]);
							} else {
									total2[group_assoc1] = intVal(api.column(6).data()[i]);
							}
							
							if (typeof total3[group_assoc2] != "undefined") {
								total3[group_assoc2] =
									total3[group_assoc2] + intVal(api.column(7).data()[i]);
							} else {
									total3[group_assoc2] = intVal(api.column(7).data()[i]);
							}
							
							if (typeof total4[group_assoc3] != "undefined") {
								total4[group_assoc3] =
									total4[group_assoc3] + intVal(api.column(8).data()[i]);
							} else {
									total4[group_assoc3] = intVal(api.column(8).data()[i]);
							}
							
							if (typeof total5[group_assoc4] != "undefined") {
								total5[group_assoc4] =
									total5[group_assoc4] + intVal(api.column(9).data()[i]);
							} else {
									total5[group_assoc4] = intVal(api.column(9).data()[i]);
							}
							
							if (typeof total6[group_assoc5] != "undefined") {
								total6[group_assoc5] =
									total6[group_assoc5] + intVal(api.column(10).data()[i]);
							} else {
									total6[group_assoc5] = intVal(api.column(10).data()[i]);
							}
							
							if (typeof total7[group_assoc6] != "undefined") {
								total7[group_assoc6] =
									total7[group_assoc6] + intVal(api.column(11).data()[i]);
							} else {
									total7[group_assoc6] = intVal(api.column(11).data()[i]);
							}
							
							if (typeof total8[group_assoc7] != "undefined") {
								total8[group_assoc7] =
									total8[group_assoc7] + intVal(api.column(12).data()[i]);
							} else {
									total8[group_assoc7] = intVal(api.column(12).data()[i]);
							}
							
							if (typeof total9[group_assoc8] != "undefined") {
								total9[group_assoc8] =
									total9[group_assoc8] + intVal(api.column(13).data()[i]);
							} else {
									total9[group_assoc8] = intVal(api.column(13).data()[i]);
							}
							
							if (typeof total10[group_assoc9] != "undefined") {
								total10[group_assoc9] =
									total10[group_assoc9] + intVal(api.column(14).data()[i]);
							} else {
									total10[group_assoc9] = intVal(api.column(14).data()[i]);
							}
							
							if (typeof total11[group_assoc10] != "undefined") {
								total11[group_assoc10] =
									total11[group_assoc10] + intVal(api.column(15).data()[i]);
							} else {
									total11[group_assoc10] = intVal(api.column(15).data()[i]);
							}
							
							if (typeof total12[group_assoc11] != "undefined") {
								total12[group_assoc11] =
									total12[group_assoc11] + intVal(api.column(16).data()[i]);
							} else {
									total12[group_assoc11] = intVal(api.column(16).data()[i]);
							}
							
							if (typeof total13[group_assoc12] != "undefined") {
								total13[group_assoc12] =
									total13[group_assoc12] + intVal(api.column(17).data()[i]);
							} else {
									total13[group_assoc12] = intVal(api.column(17).data()[i]);
							}
							
							if (typeof total14[group_assoc13] != "undefined") {
								total14[group_assoc13] =
									total14[group_assoc13] + intVal(api.column(18).data()[i]);
							} else {
									total14[group_assoc13] = intVal(api.column(18).data()[i]);
							}
							
							if (typeof total15[group_assoc14] != "undefined") {
								total15[group_assoc14] =
									total15[group_assoc14] + intVal(api.column(19).data()[i]);
							} else {
									total15[group_assoc14] = intVal(api.column(19).data()[i]);
							}
							
							if (typeof total16[group_assoc15] != "undefined") {
								total16[group_assoc15] =
									total16[group_assoc15] + intVal(api.column(20).data()[i]);
							} else {
									total16[group_assoc15] = intVal(api.column(20).data()[i]);
							}
							
							if (typeof total17[group_assoc16] != "undefined") {
								total17[group_assoc16] =
									total17[group_assoc16] + intVal(api.column(21).data()[i]);
							} else {
									total17[group_assoc16] = intVal(api.column(21).data()[i]);
							}
							
							if (typeof total18[group_assoc17] != "undefined") {
								total18[group_assoc17] =
									total18[group_assoc17] + intVal(api.column(22).data()[i]);
							} else {
									total18[group_assoc17] = intVal(api.column(22).data()[i]);
							}
							
							if (typeof total19[group_assoc18] != "undefined") {
								total19[group_assoc18] =
									total19[group_assoc18] + intVal(api.column(23).data()[i]);
							} else {
									total19[group_assoc18] = intVal(api.column(23).data()[i]);
							}
							
							if (typeof total20[group_assoc19] != "undefined") {
								total20[group_assoc19] =
									total20[group_assoc19] + intVal(api.column(24).data()[i]);
							} else {
									total20[group_assoc19] = intVal(api.column(24).data()[i]);
							}
							
							if (typeof total21[group_assoc20] != "undefined") {
								total21[group_assoc20] =
									total21[group_assoc20] + intVal(api.column(25).data()[i]);
							} else {
									total21[group_assoc20] = intVal(api.column(25).data()[i]);
							}
							
							if (typeof total22[group_assoc21] != "undefined") {
								total22[group_assoc21] =
									total22[group_assoc21] + intVal(api.column(26).data()[i]);
							} else {
									total22[group_assoc21] = intVal(api.column(26).data()[i]);
							}
							
							if (typeof total23[group_assoc22] != "undefined") {
								total23[group_assoc22] =
									total23[group_assoc22] + intVal(api.column(27).data()[i]);
							} else {
									total23[group_assoc22] = intVal(api.column(27).data()[i]);
							}
							
							if (typeof total24[group_assoc23] != "undefined") {
								total24[group_assoc23] =
									total24[group_assoc23] + intVal(api.column(28).data()[i]);
							} else {
									total24[group_assoc23] = intVal(api.column(28).data()[i]);
							}
							
							if (typeof total25[group_assoc24] != "undefined") {
								total25[group_assoc24] =
									total25[group_assoc24] + intVal(api.column(29).data()[i]);
							} else {
									total25[group_assoc24] = intVal(api.column(29).data()[i]);
							}
							
							if (typeof total26[group_assoc25] != "undefined") {
								total26[group_assoc25] =
									total26[group_assoc25] + intVal(api.column(30).data()[i]);
							} else {
									total26[group_assoc25] = intVal(api.column(30).data()[i]);
							}
							
							if (typeof total27[group_assoc26] != "undefined") {
								total27[group_assoc26] =
									total27[group_assoc26] + intVal(api.column(31).data()[i]);
							} else {
									total27[group_assoc26] = intVal(api.column(31).data()[i]);
							}
							
							if (typeof total28[group_assoc27] != "undefined") {
								total28[group_assoc27] =
									total28[group_assoc27] + intVal(api.column(32).data()[i]);
							} else {
									total28[group_assoc27] = intVal(api.column(32).data()[i]);
							}
							
							if (typeof total29[group_assoc28] != "undefined") {
								total29[group_assoc28] =
									total29[group_assoc28] + intVal(api.column(33).data()[i]);
							} else {
									total29[group_assoc28] = intVal(api.column(33).data()[i]);
							}
							
							if (typeof total30[group_assoc29] != "undefined") {
								total30[group_assoc29] =
									total30[group_assoc29] + intVal(api.column(34).data()[i]);
							} else {
									total30[group_assoc29] = intVal(api.column(34).data()[i]);
							}
							
							if (typeof total31[group_assoc30] != "undefined") {
								total31[group_assoc30] =
									total31[group_assoc30] + intVal(api.column(35).data()[i]);
							} else {
									total31[group_assoc30] = intVal(api.column(35).data()[i]);
							}
							
							if (typeof total32[group_assoc31] != "undefined") {
								total32[group_assoc31] =
									total32[group_assoc31] + intVal(api.column(36).data()[i]);
							} else {
									total32[group_assoc31] = intVal(api.column(36).data()[i]);
							}
							
							if (typeof total33[group_assoc32] != "undefined") {
								total33[group_assoc32] =
									total33[group_assoc32] + intVal(api.column(37).data()[i]);
							} else {
									total33[group_assoc32] = intVal(api.column(37).data()[i]);
							}
							
							if (typeof total34[group_assoc33] != "undefined") {
								total34[group_assoc33] =
									total34[group_assoc33] + intVal(api.column(38).data()[i]);
							} else {
									total34[group_assoc33] = intVal(api.column(38).data()[i]);
							}
							
							if (typeof total35[group_assoc34] != "undefined") {
								total35[group_assoc34] =
									total35[group_assoc34] + intVal(api.column(39).data()[i]);
							} else {
									total35[group_assoc34] = intVal(api.column(39).data()[i]);
							}
							
							if (typeof total36[group_assoc35] != "undefined") {
								total36[group_assoc35] =
									total36[group_assoc35] + intVal(api.column(40).data()[i]);
							} else {
									total36[group_assoc35] = intVal(api.column(40).data()[i]);
							}
							
							if (typeof total37[group_assoc36] != "undefined") {
								total37[group_assoc36] =
									total37[group_assoc36] + intVal(api.column(41).data()[i]);
							} else {
									total37[group_assoc36] = intVal(api.column(41).data()[i]);
							}
							
							if (typeof total38[group_assoc37] != "undefined") {
								total38[group_assoc37] =
									total38[group_assoc37] + intVal(api.column(42).data()[i]);
							} else {
									total38[group_assoc37] = intVal(api.column(42).data()[i]);
							}
							
							if (typeof total39[group_assoc38] != "undefined") {
								total39[group_assoc38] =
									total39[group_assoc38] + intVal(api.column(43).data()[i]);
							} else {
									total39[group_assoc38] = intVal(api.column(43).data()[i]);
							}
							
							if (last !== group) {
								$(rows)
								.eq(i)
								.before(
									'<tr style="background-color:#FEF9E7;"><td colspan="4" style="background-color: #FEF9E7;"><b>' +
									group.toUpperCase().replace("EXPORT", "<b style='color:black;'>EXPORT</b>").replace("MT", "<b style='color:black;'>MT (MODERN TRADE)</b>").replace("OEM", "<b style='color:red;'>OEM (Original Equipment Manufacture)</b>").replace("YTI", "<b style='color:black;'>YTI (Yupi Trading International)</b>") +
									'</b></td><td style="background-color:#FEF9E7;" class="text-right ' +
									group_assoc +
									'"></td><td style="background-color:#FEF9E7;" class="text-right ' +
									group_assoc1 +      
									'"></td><td style="background-color:#FEF9E7;" class="text-right ' +
									group_assoc2 +      
									'"></td><td class="text-right ' +
									group_assoc3 +      
									'"></td><td class="text-right ' +
									group_assoc4 +      
									'"></td><td class="text-right ' +
									group_assoc5 +      
									'"></td><td class="text-right ' +
									group_assoc6 +      
									'"></td><td class="text-right ' +
									group_assoc7 +      
									'"></td><td class="text-right ' +
									group_assoc8 +      
									'"></td><td class="text-right ' +
									group_assoc9 +      
									'"></td><td class="text-right ' +
									group_assoc10 +     
									'"></td><td class="text-right ' +
									group_assoc11 +     
									'"></td><td class="text-right ' +
									group_assoc12 +     
									'"></td><td class="text-right ' +
									group_assoc13 +     
									'"></td><td class="text-right ' +
									group_assoc14 +     
									'"></td><td class="text-right ' +
									group_assoc15 +     
									'"></td><td class="text-right ' +
									group_assoc16 +     
									'"></td><td class="text-right ' +
									group_assoc17 +     
									'"></td><td class="text-right ' +
									group_assoc18 +     
									'"></td><td class="text-right ' +
									group_assoc19 +     
									'"></td><td class="text-right ' +
									group_assoc20 +     
									'"></td><td class="text-right ' +
									group_assoc21 +     
									'"></td><td class="text-right ' +
									group_assoc22 +     
									'"></td><td class="text-right ' +
									group_assoc23 +     
									'"></td><td class="text-right ' +
									group_assoc24 +     
									'"></td><td class="text-right ' +
									group_assoc25 +     
									'"></td><td class="text-right ' +
									group_assoc26 +     
									'"></td><td class="text-right ' +
									group_assoc27 +     
									'"></td><td class="text-right ' +
									group_assoc28 +     
									'"></td><td class="text-right ' +
									group_assoc29 +     
									'"></td><td class="text-right ' +
									group_assoc30 +     
									'"></td><td class="text-right ' +
									group_assoc31 +     
									'"></td><td class="text-right ' +
									group_assoc32 +     
									'"></td><td class="text-right ' +
									group_assoc33 +     
									'"></td><td class="text-right ' +
									group_assoc34 +     
									'"></td><td class="text-right ' +
									group_assoc35 +     
									'"></td><td class="text-right ' +
									group_assoc36 +     
									'"></td><td class="text-right ' +
									group_assoc37 +     
									'"></td><td class="text-right ' +
									group_assoc38 +
									'"></td></tr>'
								);
					
								last = group;
							}
							});
							
							var REV_VAL			= new Array();
							var QTY_VAL			= new Array();
							
							var REV_VAL2		= new Array();
							var QTY_VAL2		= new Array();
							
							var REV_VAL3		= new Array();
							var QTY_VAL3		= new Array();
							
							var REV_VAL4		= new Array();
							var QTY_VAL4		= new Array();
							
							var REV_VAL5		= new Array();
							var QTY_VAL5		= new Array();
							
							var REV_VAL6		= new Array();
							var QTY_VAL6		= new Array();
							
							var REV_VAL7		= new Array();
							var QTY_VAL7		= new Array();
							
							var REV_VAL8		= new Array();
							var QTY_VAL8		= new Array();
							
							var REV_VAL9		= new Array();
							var QTY_VAL9		= new Array();
							
							var REV_VAL10		= new Array();
							var QTY_VAL10		= new Array();
							
							var REV_VAL11		= new Array();
							var QTY_VAL11		= new Array();
							
							var REV_VAL12		= new Array();
							var QTY_VAL12		= new Array();
							
							var REV_VAL13		= new Array();
							var QTY_VAL13		= new Array();
							
						for (var key in total) {
							$("." + key).html("<b>" + number_format(total[key],2) + "</b>");
							QTY_VAL[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}

						for (var key in total2) {
							$("." + key).html("<b>" + number_format(total2[key],2) + "</b>");
							REV_VAL[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total3) {
							if(key.includes("2EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1EXPORT'])/intVal(QTY_VAL['EXPORT']),2) + "</b>");
							}
							
							if(key.includes("MT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1MT'])/intVal(QTY_VAL['MT'])*1000,2) + "</b>");
							}
							
							if(key.includes("OEM") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1OEM'])/intVal(QTY_VAL['OEM'])*1000,2) + "</b>");
							}
							
							if(key.includes("YTI") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1YTI'])/intVal(QTY_VAL['YTI']),2) + "</b>");
							}
							
							if(key.includes("ECOM") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1ECOM'])/intVal(QTY_VAL['ECOM'])*1000,2) + "</b>");
							}
						}	
		
						for (var key in total4) {
							$("." + key).html("<b>" + number_format(total4[key],2) + "</b>");
							QTY_VAL2[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total5) {
							$("." + key).html("<b>" + number_format(total5[key],2) + "</b>");
							REV_VAL2[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total6) {
							if(key.includes("5EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4EXPORT'])/intVal(QTY_VAL2['3EXPORT']),2) + "</b>");
							}
							
							if(key.includes("5MT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4MT'])/intVal(QTY_VAL2['3MT'])*1000,2) + "</b>");
							}
							
							if(key.includes("5OEM") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4OEM'])/intVal(QTY_VAL2['3OEM'])*1000,2) + "</b>");
							}
							
							if(key.includes("5YTI") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4YTI'])/intVal(QTY_VAL2['3YTI']),2) + "</b>");
							}
							
							if(key.includes("5ECOM") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4ECOM'])/intVal(QTY_VAL2['3ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total7) {
							$("." + key).html("<b>" + number_format(total7[key],2) + "</b>");
							QTY_VAL3[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total8) {
							$("." + key).html("<b>" + number_format(total8[key],2) + "</b>");
							REV_VAL3[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total9) {
							if(key.includes("8EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7EXPORT'])/intVal(QTY_VAL3['6EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("8MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7MT'])/intVal(QTY_VAL3['6MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("8OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7OEM'])/intVal(QTY_VAL3['6OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("8YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7YTI'])/intVal(QTY_VAL3['6YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("8ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7ECOM'])/intVal(QTY_VAL3['6ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total10) {
							$("." + key).html("<b>" + number_format(total10[key],2) + "</b>");
							QTY_VAL4[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total11) {
							$("." + key).html("<b>" + number_format(total11[key],2) + "</b>");
							REV_VAL4[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total12) {
							if(key.includes("11EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10EXPORT'])/intVal(QTY_VAL4['9EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("11MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10MT'])/intVal(QTY_VAL4['9MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("11OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10OEM'])/intVal(QTY_VAL4['9OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("11YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10YTI'])/intVal(QTY_VAL4['9YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("11ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10ECOM'])/intVal(QTY_VAL4['9ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total13) {
							$("." + key).html("<b>" + number_format(total13[key],2) + "</b>");
							QTY_VAL5[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total14) {
							$("." + key).html("<b>" + number_format(total14[key],2) + "</b>");
							REV_VAL5[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total15) {
							if(key.includes("14EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13EXPORT'])/intVal(QTY_VAL5['12EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("14MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13MT'])/intVal(QTY_VAL5['12MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("14OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13OEM'])/intVal(QTY_VAL5['12OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("14YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13YTI'])/intVal(QTY_VAL5['12YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("14ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13ECOM'])/intVal(QTY_VAL5['12ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total16) {
							$("." + key).html("<b>" + number_format(total16[key],2) + "</b>");
							QTY_VAL6[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total17) {
							$("." + key).html("<b>" + number_format(total17[key],2) + "</b>");
							REV_VAL6[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total18) {
							if(key.includes("17EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16EXPORT'])/intVal(QTY_VAL6['15EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("17MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16MT'])/intVal(QTY_VAL6['15MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("17OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16OEM'])/intVal(QTY_VAL6['15OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("17YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16YTI'])/intVal(QTY_VAL6['15YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("17ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16ECOM'])/intVal(QTY_VAL6['15ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total19) {
							$("." + key).html("<b>" + number_format(total19[key],2) + "</b>");
							QTY_VAL7[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total20) {
							$("." + key).html("<b>" + number_format(total20[key],2) + "</b>");
							REV_VAL7[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total21) {
							if(key.includes("20EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19EXPORT'])/intVal(QTY_VAL7['18EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("20MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19MT'])/intVal(QTY_VAL7['18MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("20OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19OEM'])/intVal(QTY_VAL7['18OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("20YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19YTI'])/intVal(QTY_VAL7['18YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("20ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19ECOM'])/intVal(QTY_VAL7['18ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total22) {
							$("." + key).html("<b>" + number_format(total22[key],2) + "</b>");
							QTY_VAL8[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total23) {
							$("." + key).html("<b>" + number_format(total23[key],2) + "</b>");
							REV_VAL8[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total24) {
							if(key.includes("23EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22EXPORT'])/intVal(QTY_VAL8['21EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("23MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22MT'])/intVal(QTY_VAL8['21MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("23OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22OEM'])/intVal(QTY_VAL8['21OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("23YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22YTI'])/intVal(QTY_VAL8['21YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("23ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22ECOM'])/intVal(QTY_VAL8['21ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total25) {
							$("." + key).html("<b>" + number_format(total25[key],2) + "</b>");
							QTY_VAL9[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total26) {
							$("." + key).html("<b>" + number_format(total26[key],2) + "</b>");
							REV_VAL9[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total27) {
							if(key.includes("26EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25EXPORT'])/intVal(QTY_VAL9['24EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("26MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25MT'])/intVal(QTY_VAL9['24MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("26OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25OEM'])/intVal(QTY_VAL9['24OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("26YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25YTI'])/intVal(QTY_VAL9['24YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("26ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25ECOM'])/intVal(QTY_VAL9['24ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total28) {
							$("." + key).html("<b>" + number_format(total28[key],2) + "</b>");
							QTY_VAL10[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total29) {
							$("." + key).html("<b>" + number_format(total29[key],2) + "</b>");
							REV_VAL10[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total30) {
							if(key.includes("29EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28EXPORT'])/intVal(QTY_VAL10['27EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("29MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28MT'])/intVal(QTY_VAL10['27MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("29OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28OEM'])/intVal(QTY_VAL10['27OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("29YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28YTI'])/intVal(QTY_VAL10['27YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("29ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28ECOM'])/intVal(QTY_VAL10['27ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total31) {
							$("." + key).html("<b>" + number_format(total31[key],2) + "</b>");
							QTY_VAL11[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total32) {
							$("." + key).html("<b>" + number_format(total32[key],2) + "</b>");
							REV_VAL11[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total33) {
							if(key.includes("32EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31EXPORT'])/intVal(QTY_VAL11['30EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("32MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31MT'])/intVal(QTY_VAL11['30MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("32OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31OEM'])/intVal(QTY_VAL11['30OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("32YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31YTI'])/intVal(QTY_VAL11['30YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("32ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31ECOM'])/intVal(QTY_VAL11['30ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total34) {
							$("." + key).html("<b>" + number_format(total34[key],2) + "</b>");
							QTY_VAL12[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total35) {
							$("." + key).html("<b>" + number_format(total35[key],2) + "</b>");
							REV_VAL12[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total36) {
							if(key.includes("35EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34EXPORT'])/intVal(QTY_VAL12['33EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("35MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34MT'])/intVal(QTY_VAL12['33MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("35OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34OEM'])/intVal(QTY_VAL12['33OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("35YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34YTI'])/intVal(QTY_VAL12['33YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("35ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34ECOM'])/intVal(QTY_VAL12['33ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total37) {
							$("." + key).html("<b>" + number_format(total37[key],2) + "</b>");
							QTY_VAL13[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total38) {
							$("." + key).html("<b>" + number_format(total38[key],2) + "</b>");
							REV_VAL13[key] = intVal($('#EX_master_product').find("."+key+"").text());
						}
						
						for (var key in total39) {
							if(key.includes("38EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37EXPORT'])/intVal(QTY_VAL13['36EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("38MT") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37MT'])/intVal(QTY_VAL13['36MT'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("38OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37OEM'])/intVal(QTY_VAL13['36OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("38YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37YTI'])/intVal(QTY_VAL13['36YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("38ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37ECOM'])/intVal(QTY_VAL13['36ECOM'])*1000,2) + "</b>");
							}
						}
						
						}
					});
				$.LoadingOverlay("hide");
				var cnt = $(".dt-buttons").contents();
				
				$(".dt-buttons").replaceWith(cnt);
				var cnta = $("#buttons").contents();
				$("#buttons").replaceWith(cnta);
				
				//BIAR RAPIH
				table.columns.adjust();
			}
		});
	  }
	  
	  // 1. Buat helper untuk memilih symbol
	  function getCurrencySymbol(country){
		if(country === 'Thailand') return '฿';       // Thai Baht
		if(country === 'Malaysia') return 'RM';      // Ringgit Malaysia
		return '$';                                  // Default USD
	  }
	  
	  function IDR_cari_data_domestic(){
		// ambil nilai kurs
	    var usd     = document.getElementById("usd").value.replace(",","").replace(".","");
	    var baht    = document.getElementById("baht").value.replace(",","").replace(".","");
	    var ringgit = document.getElementById("ringgit").value.replace(",","").replace(".","");
	    var months  = ["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"];
		var i 					= 1;	
		var $t   			    = $("#IDR_master_product");
		$('#IDR_master_product tbody').empty();	
		$("#IDR_master_product").dataTable().fnDestroy();		
		$.LoadingOverlay("show");	
		
	    // reset tabel
		if ( $.fn.DataTable.isDataTable($t) ) {
			$t.DataTable().destroy();
		}
		$t.find("tbody").empty();
		$.LoadingOverlay("show");
		$.ajax({
			async: false,
			type: 'POST',
			url: defaulturl+"sales/summary_export_idr",
			data: { usd: usd, baht: baht, ringgit: ringgit },
		    beforeSend: function() {
				// setting a timeout
				$.LoadingOverlay("hide");		
			},
			success: function(response) 
			{
				$.LoadingOverlay("hide");	
				$('#IDR_master_product tbody').empty();	
				$("#IDR_master_product").dataTable().fnDestroy();	
				var $b = $t.find("tbody");
				$.each(response, function(_, item){
					var row = "<tr>"
							+ "<td>" + (i++) + "</td>"
							+ "<td>" + item.id_channel  + "</td>"
							+ "<td>" + item.key_product + "</td>"
							+ "<td>" + item.mid_product + "</td>"
							+ "<td nowrap>" + item.product_name + "</td>"
							+ "<td>" + item.currency.toUpperCase() + "</td>";
					// loop QTY / REV / ASP masing-masing bulan
					for (var m = 0; m < months.length; m++){
						var k = months[m];
						row += "<td class='text-right'>" + number_format(item[k + "_qty"], 2) + "</td>"
							+ "<td class='text-right'>" + number_format(item[k + "_rev"], 2) + "</td>"
							+ "<td class='text-right'>" + number_format(item[k + "_asp"], 2) + "</td>";
					}
						// kolom total
						row += "<td class='text-right'>" + number_format(item.total_qty, 2) + "</td>"
							+ "<td class='text-right'>" + number_format(item.total_rev, 2) + "</td>"
							+ "<td class='text-right'>" + number_format(item.total_asp, 2) + "</td>"
							+ "</tr>";
					$b.append(row);
				});
			},
			complete: function(){
					var groupColumn = 1;
					var table = $('#IDR_master_product').DataTable({
						columnDefs: [{ visible: false, targets: groupColumn }],
						scrollY: "470px",
						scrollX:        true,
						scrollCollapse: true,
						paging:         false,
						bSort: 			false,
						searching: 		true,
						fixedColumns:   {
							leftColumns: 5
						},
						dom: 'Bfrtip',
						buttons: [
							{
						extend: 'collection',
							text: 'Export Data',
							buttons: [
								{
									extend: 'copyHtml5',
									text: 'To clipboard'
								},
								{
									extend: 'csvHtml5',
									text: 'CSV'
								},
								{
									extend: 'excelHtml5',
									text: 'Excel'
								},
							]
						}
						],
						drawCallback: function(settings){
							var api      = this.api();
							var rows     = api.rows({ page: "all" }).nodes();
							var groupCol = api.column(1, { page: "all" }).data().toArray();
							var months   = ["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"];
							var startCol = 6;                   // kolom pertama QTY Januari
							var colCount = months.length * 3;   // 12 bulan × (QTY,REV,ASP)
							var idxTQ    = startCol + colCount; // index TOTAL_QTY
							var idxTR    = idxTQ + 1;           // index TOTAL_REV
							
							// hitung subtotal per grup
							var sums       = {};
							var sumsTotal  = {};
							for (var r = 0; r < groupCol.length; r++){
								var g = groupCol[r];
								if (!sums[g])      sums[g]      = Array(colCount).fill(0);
								if (!sumsTotal[g]) sumsTotal[g] = { qty:0, rev:0 };
							
								// bulanan
								for (var c = 0; c < colCount; c++){
								var cell = api.cell(r, startCol + c).data();
								var num  = parseFloat(cell.toString().replace(/,/g,"")) || 0;
								sums[g][c] += num;
								}
							
								// total QTY & REV
								var tq = parseFloat(api.cell(r, idxTQ).data().toString().replace(/,/g,"")) || 0;
								var trv= parseFloat(api.cell(r, idxTR).data().toString().replace(/,/g,"")) || 0;
								sumsTotal[g].qty += tq;
								sumsTotal[g].rev += trv;
							}
							
							// sisipkan baris subtotal untuk tiap grup
							var last = null;
							for (var r = 0; r < groupCol.length; r++){
								var g2 = groupCol[r];
								if (g2 !== last){
								var tr = '<tr class="group"><td colspan="5"><b>' + g2 + '</b></td>';
								// bulan
								for (var m = 0; m < months.length; m++){
									var q = sums[g2][m*3];
									var v = sums[g2][m*3+1];
									var a = q ? v/q : 0;
									tr += '<td class="text-right"><b>' + number_format(q,2) + '</b></td>'
									+  '<td class="text-right"><b>' + number_format(v,2) + '</b></td>'
									+  '<td class="text-right"><b>' + number_format(a,2) + '</b></td>';
								}
								// kolom TOTAL
								var t = sumsTotal[g2];
								var ta = t.qty ? t.rev/t.qty : 0;
								tr += '<td class="text-right"><b>' + number_format(t.qty,2) + '</b></td>'
									+  '<td class="text-right"><b>' + number_format(t.rev,2) + '</b></td>'
									+  '<td class="text-right"><b>' + number_format(ta,2) + '</b></td>'
									+  '</tr>';
								$(rows).eq(r).before(tr);
								last = g2;
								}
							}
						}
					});
				$.LoadingOverlay("hide");
				var cnt = $(".dt-buttons").contents();
				
				$(".dt-buttons").replaceWith(cnt);
				var cnta = $("#buttons").contents();
				$("#buttons").replaceWith(cnta);
				
				//BIAR RAPIH
				table.columns.adjust();
			}
		});
	  }

	  function cari_data(){
		var usd					= document.getElementById("usd").value; 
		var i 					= 1;	
		$('#master_product tbody').empty();	
		$("#master_product").dataTable().fnDestroy();		
		$.LoadingOverlay("show");	
		$.ajax({
			async: false,
			type: 'POST',
			<?php
				if($year == "2025"){
			?>
				url: defaulturl+"sales/summary_all",
			<?php }else{ ?>
				url: defaulturl+"sales/summary_all_new",
			<?php } ?>
			data: 
			{
				usd	: usd	
			},
		    beforeSend: function() {
				// setting a timeout
				$.LoadingOverlay("show");	
			},
			success: function(response) 
			{
				$.LoadingOverlay("hide");	
				$('#master_product tbody').empty();	
				$("#master_product").dataTable().fnDestroy();	
				$.each(response,function(index,item){
					if(item.mid_product !== "0000"){
					$('#master_product tbody').append(
						"<tr>" +
							"<td style='width:3%;'>" + i + "</td>" +
							"<td style='width:15%;'>"+item.id_channel+"</td>" +	
							"<td style='width:15%;'>"+item.key_product+"</td>" +	
							"<td style='width:15%;' nowrap>"+item.mid_product+"</td>" +	
							"<td style='width:15%;' nowrap>"+item.product_name+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.jan_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.jan_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.jan_asp,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.feb_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.feb_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.feb_asp,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.mar_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.mar_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.mar_asp,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.apr_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.apr_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.apr_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.may_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.may_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.may_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.jun_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.jun_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.jun_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.jul_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.jul_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.jul_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.aug_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.aug_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.aug_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.sep_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.sep_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.sep_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.oct_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.oct_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.oct_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.nov_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.nov_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.nov_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.dec_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.dec_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.dec_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.total_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.total_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.total_asp,2)+"</td>" +
						"</tr>"
					);
					i++;
					}else{
						$('#master_product tbody').append(
						"<tr>" +
							"<td style='width:3%;'></td>" +
							"<td style='width:15%;'>"+item.product_name+"</td>" +	
							"<td style='width:15%;'></td>" +	
							"<td style='width:15%;'></td>" +	
							"<td style='width:15%;' nowrap><b>"+item.product_name+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.jan_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.jan_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.jan_asp,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.feb_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.feb_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.feb_asp,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.mar_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.mar_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.mar_asp,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.apr_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.apr_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.apr_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.may_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.may_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.may_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.jun_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.jun_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.jun_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.jul_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.jul_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.jul_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.aug_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.aug_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.aug_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.sep_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.sep_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.sep_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.oct_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.oct_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.oct_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.nov_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.nov_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.nov_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.dec_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.dec_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.dec_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.total_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.total_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.total_asp,2)+"</b></td>" +
						"</tr>"
					);
					}
				});
			},
			complete: function(){
					var groupColumn = 1;
					var table = $('#master_product').DataTable({
						columnDefs: [{ visible: false, targets: groupColumn }],
						scrollY: "470px",
						scrollX:        true,
						scrollCollapse: true,
						paging:         false,
						bSort: 			false,
						searching: 		true,
						fixedColumns:   {
							leftColumns: 5
						},
						dom: 'Bfrtip',
						buttons: [
							{
						extend: 'collection',
							text: 'Export Data',
							buttons: [
								{
									extend: 'copyHtml5',
									text: 'To clipboard'
								},
								{
									extend: 'csvHtml5',
									text: 'CSV'
								},
								{
									extend: 'excelHtml5',
									text: 'Excel'
								},
							]
						}
						],
						drawCallback: function (settings) {
						var api = this.api();
						var rows = api.rows({ page: "all" }).nodes();
						var last = null;
						
						//TOTAL 38 kolom
					
						// Remove the formatting to get integer data for summation
						var intVal = function (i) {
							return typeof i === "string"
							? i.replace(/[\$,]/g, "") * 1
							: typeof i === "number"
							? i
							: 0;
						};
					
						total 	= [];
						total2 	= [];
						total3 	= [];
						total4 	= [];
						total5 	= [];
						total6 	= [];
						total7 	= [];
						total8 	= [];
						total9 	= [];
						total10 = [];
						total11 = [];
						total12 = [];
						total13 = [];
						total14 = [];
						total15 = [];
						total16 = [];
						total17 = [];
						total18 = [];
						total19 = [];
						total20 = [];
						total21 = [];
						total22 = [];
						total23 = [];
						total24 = [];
						total25 = [];
						total26 = [];
						total27 = [];
						total28 = [];
						total29 = [];
						total30 = [];
						total31 = [];
						total32 = [];
						total33 = [];
						total34 = [];
						total35 = [];
						total36 = [];
						total37 = [];
						total38 = [];
						total39 = [];
						
						api
							.column(1, { page: "all" })
							.data()
							.each(function (group, i) {
							group_assoc 	= group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc1 	= "1"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc2 	= "2"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc3 	= "3"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc4 	= "4"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc5 	= "5"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc6 	= "6"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc7 	= "7"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc8 	= "8"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc9 	= "9"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc10 	= "10"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc11 	= "11"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc12 	= "12"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc13 	= "13"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc14 	= "14"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc15 	= "15"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc16 	= "16"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc17 	= "17"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc18 	= "18"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc19 	= "19"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc20	= "20"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc21 	= "21"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc22 	= "22"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc23 	= "23"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc24 	= "24"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc25 	= "25"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc26 	= "26"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc27 	= "27"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc28 	= "28"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc29 	= "29"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc30 	= "30"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc31 	= "31"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc32 	= "32"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc33 	= "33"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc34 	= "34"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc35 	= "35"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc36 	= "36"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc37 	= "37"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc38 	= "38"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
							group_assoc39 	= "39"+group.replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_").replace(" ", "_");
				
							if (typeof total[group_assoc] != "undefined") {
								total[group_assoc] =
									total[group_assoc] + intVal(api.column(5).data()[i]);
							} else {
									total[group_assoc] = intVal(api.column(5).data()[i]);
							}
							
							if (typeof total2[group_assoc1] != "undefined") {
								total2[group_assoc1] =
									total2[group_assoc1] + intVal(api.column(6).data()[i]);
							} else {
									total2[group_assoc1] = intVal(api.column(6).data()[i]);
							}
							
							if (typeof total3[group_assoc2] != "undefined") {
								total3[group_assoc2] =
									total3[group_assoc2] + intVal(api.column(7).data()[i]);
							} else {
									total3[group_assoc2] = intVal(api.column(7).data()[i]);
							}
							
							if (typeof total4[group_assoc3] != "undefined") {
								total4[group_assoc3] =
									total4[group_assoc3] + intVal(api.column(8).data()[i]);
							} else {
									total4[group_assoc3] = intVal(api.column(8).data()[i]);
							}
							
							if (typeof total5[group_assoc4] != "undefined") {
								total5[group_assoc4] =
									total5[group_assoc4] + intVal(api.column(9).data()[i]);
							} else {
									total5[group_assoc4] = intVal(api.column(9).data()[i]);
							}
							
							if (typeof total6[group_assoc5] != "undefined") {
								total6[group_assoc5] =
									total6[group_assoc5] + intVal(api.column(10).data()[i]);
							} else {
									total6[group_assoc5] = intVal(api.column(10).data()[i]);
							}
							
							if (typeof total7[group_assoc6] != "undefined") {
								total7[group_assoc6] =
									total7[group_assoc6] + intVal(api.column(11).data()[i]);
							} else {
									total7[group_assoc6] = intVal(api.column(11).data()[i]);
							}
							
							if (typeof total8[group_assoc7] != "undefined") {
								total8[group_assoc7] =
									total8[group_assoc7] + intVal(api.column(12).data()[i]);
							} else {
									total8[group_assoc7] = intVal(api.column(12).data()[i]);
							}
							
							if (typeof total9[group_assoc8] != "undefined") {
								total9[group_assoc8] =
									total9[group_assoc8] + intVal(api.column(13).data()[i]);
							} else {
									total9[group_assoc8] = intVal(api.column(13).data()[i]);
							}
							
							if (typeof total10[group_assoc9] != "undefined") {
								total10[group_assoc9] =
									total10[group_assoc9] + intVal(api.column(14).data()[i]);
							} else {
									total10[group_assoc9] = intVal(api.column(14).data()[i]);
							}
							
							if (typeof total11[group_assoc10] != "undefined") {
								total11[group_assoc10] =
									total11[group_assoc10] + intVal(api.column(15).data()[i]);
							} else {
									total11[group_assoc10] = intVal(api.column(15).data()[i]);
							}
							
							if (typeof total12[group_assoc11] != "undefined") {
								total12[group_assoc11] =
									total12[group_assoc11] + intVal(api.column(16).data()[i]);
							} else {
									total12[group_assoc11] = intVal(api.column(16).data()[i]);
							}
							
							if (typeof total13[group_assoc12] != "undefined") {
								total13[group_assoc12] =
									total13[group_assoc12] + intVal(api.column(17).data()[i]);
							} else {
									total13[group_assoc12] = intVal(api.column(17).data()[i]);
							}
							
							if (typeof total14[group_assoc13] != "undefined") {
								total14[group_assoc13] =
									total14[group_assoc13] + intVal(api.column(18).data()[i]);
							} else {
									total14[group_assoc13] = intVal(api.column(18).data()[i]);
							}
							
							if (typeof total15[group_assoc14] != "undefined") {
								total15[group_assoc14] =
									total15[group_assoc14] + intVal(api.column(19).data()[i]);
							} else {
									total15[group_assoc14] = intVal(api.column(19).data()[i]);
							}
							
							if (typeof total16[group_assoc15] != "undefined") {
								total16[group_assoc15] =
									total16[group_assoc15] + intVal(api.column(20).data()[i]);
							} else {
									total16[group_assoc15] = intVal(api.column(20).data()[i]);
							}
							
							if (typeof total17[group_assoc16] != "undefined") {
								total17[group_assoc16] =
									total17[group_assoc16] + intVal(api.column(21).data()[i]);
							} else {
									total17[group_assoc16] = intVal(api.column(21).data()[i]);
							}
							
							if (typeof total18[group_assoc17] != "undefined") {
								total18[group_assoc17] =
									total18[group_assoc17] + intVal(api.column(22).data()[i]);
							} else {
									total18[group_assoc17] = intVal(api.column(22).data()[i]);
							}
							
							if (typeof total19[group_assoc18] != "undefined") {
								total19[group_assoc18] =
									total19[group_assoc18] + intVal(api.column(23).data()[i]);
							} else {
									total19[group_assoc18] = intVal(api.column(23).data()[i]);
							}
							
							if (typeof total20[group_assoc19] != "undefined") {
								total20[group_assoc19] =
									total20[group_assoc19] + intVal(api.column(24).data()[i]);
							} else {
									total20[group_assoc19] = intVal(api.column(24).data()[i]);
							}
							
							if (typeof total21[group_assoc20] != "undefined") {
								total21[group_assoc20] =
									total21[group_assoc20] + intVal(api.column(25).data()[i]);
							} else {
									total21[group_assoc20] = intVal(api.column(25).data()[i]);
							}
							
							if (typeof total22[group_assoc21] != "undefined") {
								total22[group_assoc21] =
									total22[group_assoc21] + intVal(api.column(26).data()[i]);
							} else {
									total22[group_assoc21] = intVal(api.column(26).data()[i]);
							}
							
							if (typeof total23[group_assoc22] != "undefined") {
								total23[group_assoc22] =
									total23[group_assoc22] + intVal(api.column(27).data()[i]);
							} else {
									total23[group_assoc22] = intVal(api.column(27).data()[i]);
							}
							
							if (typeof total24[group_assoc23] != "undefined") {
								total24[group_assoc23] =
									total24[group_assoc23] + intVal(api.column(28).data()[i]);
							} else {
									total24[group_assoc23] = intVal(api.column(28).data()[i]);
							}
							
							if (typeof total25[group_assoc24] != "undefined") {
								total25[group_assoc24] =
									total25[group_assoc24] + intVal(api.column(29).data()[i]);
							} else {
									total25[group_assoc24] = intVal(api.column(29).data()[i]);
							}
							
							if (typeof total26[group_assoc25] != "undefined") {
								total26[group_assoc25] =
									total26[group_assoc25] + intVal(api.column(30).data()[i]);
							} else {
									total26[group_assoc25] = intVal(api.column(30).data()[i]);
							}
							
							if (typeof total27[group_assoc26] != "undefined") {
								total27[group_assoc26] =
									total27[group_assoc26] + intVal(api.column(31).data()[i]);
							} else {
									total27[group_assoc26] = intVal(api.column(31).data()[i]);
							}
							
							if (typeof total28[group_assoc27] != "undefined") {
								total28[group_assoc27] =
									total28[group_assoc27] + intVal(api.column(32).data()[i]);
							} else {
									total28[group_assoc27] = intVal(api.column(32).data()[i]);
							}
							
							if (typeof total29[group_assoc28] != "undefined") {
								total29[group_assoc28] =
									total29[group_assoc28] + intVal(api.column(33).data()[i]);
							} else {
									total29[group_assoc28] = intVal(api.column(33).data()[i]);
							}
							
							if (typeof total30[group_assoc29] != "undefined") {
								total30[group_assoc29] =
									total30[group_assoc29] + intVal(api.column(34).data()[i]);
							} else {
									total30[group_assoc29] = intVal(api.column(34).data()[i]);
							}
							
							if (typeof total31[group_assoc30] != "undefined") {
								total31[group_assoc30] =
									total31[group_assoc30] + intVal(api.column(35).data()[i]);
							} else {
									total31[group_assoc30] = intVal(api.column(35).data()[i]);
							}
							
							if (typeof total32[group_assoc31] != "undefined") {
								total32[group_assoc31] =
									total32[group_assoc31] + intVal(api.column(36).data()[i]);
							} else {
									total32[group_assoc31] = intVal(api.column(36).data()[i]);
							}
							
							if (typeof total33[group_assoc32] != "undefined") {
								total33[group_assoc32] =
									total33[group_assoc32] + intVal(api.column(37).data()[i]);
							} else {
									total33[group_assoc32] = intVal(api.column(37).data()[i]);
							}
							
							if (typeof total34[group_assoc33] != "undefined") {
								total34[group_assoc33] =
									total34[group_assoc33] + intVal(api.column(38).data()[i]);
							} else {
									total34[group_assoc33] = intVal(api.column(38).data()[i]);
							}
							
							if (typeof total35[group_assoc34] != "undefined") {
								total35[group_assoc34] =
									total35[group_assoc34] + intVal(api.column(39).data()[i]);
							} else {
									total35[group_assoc34] = intVal(api.column(39).data()[i]);
							}
							
							if (typeof total36[group_assoc35] != "undefined") {
								total36[group_assoc35] =
									total36[group_assoc35] + intVal(api.column(40).data()[i]);
							} else {
									total36[group_assoc35] = intVal(api.column(40).data()[i]);
							}
							
							if (typeof total37[group_assoc36] != "undefined") {
								total37[group_assoc36] =
									total37[group_assoc36] + intVal(api.column(41).data()[i]);
							} else {
									total37[group_assoc36] = intVal(api.column(41).data()[i]);
							}
							
							if (typeof total38[group_assoc37] != "undefined") {
								total38[group_assoc37] =
									total38[group_assoc37] + intVal(api.column(42).data()[i]);
							} else {
									total38[group_assoc37] = intVal(api.column(42).data()[i]);
							}
							
							if (typeof total39[group_assoc38] != "undefined") {
								total39[group_assoc38] =
									total39[group_assoc38] + intVal(api.column(43).data()[i]);
							} else {
									total39[group_assoc38] = intVal(api.column(43).data()[i]);
							}
							
							//console.log(group);
							
							if (group !== "GRAND TOTAL" ){
							if (last !== group) {
								$(rows)
								.eq(i)
								.before(
									'<tr style="background-color: #FEF9E7;"><td colspan="4" style=" background-color: #FEF9E7;"><b>' +
									group.toUpperCase().replace("EXPORT", "<b style='color:black;'>EXPORT</b>").replace("DOMESTIC", "<b style='color:black;'>DOMESTIC</b>").replace("OEM", "<b style='color:red;'>OEM (Original Equipment Manufacture)</b>").replace("YTI", "<b style='color:red;'>YTI (Yupi Trading International)</b>") +
									'</b></td><td style="background-color: #FEF9E7;" class="text-right ' +
									group_assoc +
									'"></td><td style="background-color: #FEF9E7;" class="text-right ' +
									group_assoc1 +      
									'"></td><td style="background-color: #FEF9E7;" class="text-right ' +
									group_assoc2 +      
									'"></td><td class="text-right ' +
									group_assoc3 +      
									'"></td><td class="text-right ' +
									group_assoc4 +      
									'"></td><td class="text-right ' +
									group_assoc5 +      
									'"></td><td class="text-right ' +
									group_assoc6 +      
									'"></td><td class="text-right ' +
									group_assoc7 +      
									'"></td><td class="text-right ' +
									group_assoc8 +      
									'"></td><td class="text-right ' +
									group_assoc9 +      
									'"></td><td class="text-right ' +
									group_assoc10 +     
									'"></td><td class="text-right ' +
									group_assoc11 +     
									'"></td><td class="text-right ' +
									group_assoc12 +     
									'"></td><td class="text-right ' +
									group_assoc13 +     
									'"></td><td class="text-right ' +
									group_assoc14 +     
									'"></td><td class="text-right ' +
									group_assoc15 +     
									'"></td><td class="text-right ' +
									group_assoc16 +     
									'"></td><td class="text-right ' +
									group_assoc17 +     
									'"></td><td class="text-right ' +
									group_assoc18 +     
									'"></td><td class="text-right ' +
									group_assoc19 +     
									'"></td><td class="text-right ' +
									group_assoc20 +     
									'"></td><td class="text-right ' +
									group_assoc21 +     
									'"></td><td class="text-right ' +
									group_assoc22 +     
									'"></td><td class="text-right ' +
									group_assoc23 +     
									'"></td><td class="text-right ' +
									group_assoc24 +     
									'"></td><td class="text-right ' +
									group_assoc25 +     
									'"></td><td class="text-right ' +
									group_assoc26 +     
									'"></td><td class="text-right ' +
									group_assoc27 +     
									'"></td><td class="text-right ' +
									group_assoc28 +     
									'"></td><td class="text-right ' +
									group_assoc29 +     
									'"></td><td class="text-right ' +
									group_assoc30 +     
									'"></td><td class="text-right ' +
									group_assoc31 +     
									'"></td><td class="text-right ' +
									group_assoc32 +     
									'"></td><td class="text-right ' +
									group_assoc33 +     
									'"></td><td class="text-right ' +
									group_assoc34 +     
									'"></td><td class="text-right ' +
									group_assoc35 +     
									'"></td><td class="text-right ' +
									group_assoc36 +     
									'"></td><td class="text-right ' +
									group_assoc37 +     
									'"></td><td class="text-right ' +
									group_assoc38 +
									'"></td></tr>'
								);
					
								last = group;
							}
							}
							});
							
							var REV_VAL			= new Array();
							var QTY_VAL			= new Array();
							
							var REV_VAL2		= new Array();
							var QTY_VAL2		= new Array();
							
							var REV_VAL3		= new Array();
							var QTY_VAL3		= new Array();
							
							var REV_VAL4		= new Array();
							var QTY_VAL4		= new Array();
							
							var REV_VAL5		= new Array();
							var QTY_VAL5		= new Array();
							
							var REV_VAL6		= new Array();
							var QTY_VAL6		= new Array();
							
							var REV_VAL7		= new Array();
							var QTY_VAL7		= new Array();
							
							var REV_VAL8		= new Array();
							var QTY_VAL8		= new Array();
							
							var REV_VAL9		= new Array();
							var QTY_VAL9		= new Array();
							
							var REV_VAL10		= new Array();
							var QTY_VAL10		= new Array();
							
							var REV_VAL11		= new Array();
							var QTY_VAL11		= new Array();
							
							var REV_VAL12		= new Array();
							var QTY_VAL12		= new Array();
							
							var REV_VAL13		= new Array();
							var QTY_VAL13		= new Array();
							
						for (var key in total) {
							$("." + key).html("<b>" + number_format(total[key],2) + "</b>");
							QTY_VAL[key] = intVal($('#master_product').find("."+key+"").text());
						}

						for (var key in total2) {
							$("." + key).html("<b>" + number_format(total2[key],2) + "</b>");
							REV_VAL[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total3) {
							if(key.includes("2EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1EXPORT'])/intVal(QTY_VAL['EXPORT']),2) + "</b>");
							}
							
							if(key.includes("DOMESTIC") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1DOMESTIC'])/intVal(QTY_VAL['DOMESTIC']),2) + "</b>");
							}
							
							if(key.includes("OEM") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1OEM'])/intVal(QTY_VAL['OEM'])*1000,2) + "</b>");
							}
							
							if(key.includes("YTI") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1YTI'])/intVal(QTY_VAL['YTI']),2) + "</b>");
							}
							
							if(key.includes("ECOM") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL['1ECOM'])/intVal(QTY_VAL['ECOM'])*1000,2) + "</b>");
							}
						}	
		
						for (var key in total4) {
							$("." + key).html("<b>" + number_format(total4[key],2) + "</b>");
							QTY_VAL2[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total5) {
							$("." + key).html("<b>" + number_format(total5[key],2) + "</b>");
							REV_VAL2[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total6) {
							if(key.includes("5EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4EXPORT'])/intVal(QTY_VAL2['3EXPORT']),2) + "</b>");
							}
							
							if(key.includes("5DOMESTIC") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4DOMESTIC'])/intVal(QTY_VAL2['3DOMESTIC']),2) + "</b>");
							}
							
							if(key.includes("5OEM") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4OEM'])/intVal(QTY_VAL2['3OEM'])*1000,2) + "</b>");
							}
							
							if(key.includes("5YTI") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4YTI'])/intVal(QTY_VAL2['3YTI']),2) + "</b>");
							}
							
							if(key.includes("5ECOM") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL2['4ECOM'])/intVal(QTY_VAL2['3ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total7) {
							$("." + key).html("<b>" + number_format(total7[key],2) + "</b>");
							QTY_VAL3[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total8) {
							$("." + key).html("<b>" + number_format(total8[key],2) + "</b>");
							REV_VAL3[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total9) {
							if(key.includes("8EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7EXPORT'])/intVal(QTY_VAL3['6EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("8DOMESTIC") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7DOMESTIC'])/intVal(QTY_VAL3['6DOMESTIC']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("8OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7OEM'])/intVal(QTY_VAL3['6OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("8YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7YTI'])/intVal(QTY_VAL3['6YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("8ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL3['7ECOM'])/intVal(QTY_VAL3['6ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total10) {
							$("." + key).html("<b>" + number_format(total10[key],2) + "</b>");
							QTY_VAL4[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total11) {
							$("." + key).html("<b>" + number_format(total11[key],2) + "</b>");
							REV_VAL4[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total12) {
							if(key.includes("11EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10EXPORT'])/intVal(QTY_VAL4['9EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("11DOMESTIC") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10DOMESTIC'])/intVal(QTY_VAL4['9DOMESTIC']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("11OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10OEM'])/intVal(QTY_VAL4['9OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("11YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10YTI'])/intVal(QTY_VAL4['9YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("11ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL4['10ECOM'])/intVal(QTY_VAL4['9ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total13) {
							$("." + key).html("<b>" + number_format(total13[key],2) + "</b>");
							QTY_VAL5[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total14) {
							$("." + key).html("<b>" + number_format(total14[key],2) + "</b>");
							REV_VAL5[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total15) {
							if(key.includes("14EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13EXPORT'])/intVal(QTY_VAL5['12EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("14DOMESTIC") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13DOMESTIC'])/intVal(QTY_VAL5['12DOMESTIC']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("14OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13OEM'])/intVal(QTY_VAL5['12OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("14YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13YTI'])/intVal(QTY_VAL5['12YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("14ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL5['13ECOM'])/intVal(QTY_VAL5['12ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total16) {
							$("." + key).html("<b>" + number_format(total16[key],2) + "</b>");
							QTY_VAL6[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total17) {
							$("." + key).html("<b>" + number_format(total17[key],2) + "</b>");
							REV_VAL6[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total18) {
							if(key.includes("17EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16EXPORT'])/intVal(QTY_VAL6['15EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("17DOMESTIC") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16DOMESTIC'])/intVal(QTY_VAL6['15DOMESTIC']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("17OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16OEM'])/intVal(QTY_VAL6['15OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("17YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16YTI'])/intVal(QTY_VAL6['15YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("17ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL6['16ECOM'])/intVal(QTY_VAL6['15ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total19) {
							$("." + key).html("<b>" + number_format(total19[key],2) + "</b>");
							QTY_VAL7[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total20) {
							$("." + key).html("<b>" + number_format(total20[key],2) + "</b>");
							REV_VAL7[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total21) {
							if(key.includes("20EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19EXPORT'])/intVal(QTY_VAL7['18EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("20DOMESTIC") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19DOMESTIC'])/intVal(QTY_VAL7['18DOMESTIC']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("20OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19OEM'])/intVal(QTY_VAL7['18OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("20YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19YTI'])/intVal(QTY_VAL7['18YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("20ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL7['19ECOM'])/intVal(QTY_VAL7['18ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total22) {
							$("." + key).html("<b>" + number_format(total22[key],2) + "</b>");
							QTY_VAL8[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total23) {
							$("." + key).html("<b>" + number_format(total23[key],2) + "</b>");
							REV_VAL8[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total24) {
							if(key.includes("23EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22EXPORT'])/intVal(QTY_VAL8['21EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("23DOMESTIC") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22DOMESTIC'])/intVal(QTY_VAL8['21DOMESTIC']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("23OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22OEM'])/intVal(QTY_VAL8['21OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("23YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22YTI'])/intVal(QTY_VAL8['21YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("23ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL8['22ECOM'])/intVal(QTY_VAL8['21ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total25) {
							$("." + key).html("<b>" + number_format(total25[key],2) + "</b>");
							QTY_VAL9[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total26) {
							$("." + key).html("<b>" + number_format(total26[key],2) + "</b>");
							REV_VAL9[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total27) {
							if(key.includes("26EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25EXPORT'])/intVal(QTY_VAL9['24EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("26DOMESTIC") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25DOMESTIC'])/intVal(QTY_VAL9['24DOMESTIC']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("26OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25OEM'])/intVal(QTY_VAL9['24OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("26YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25YTI'])/intVal(QTY_VAL9['24YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("26ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL9['25ECOM'])/intVal(QTY_VAL9['24ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total28) {
							$("." + key).html("<b>" + number_format(total28[key],2) + "</b>");
							QTY_VAL10[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total29) {
							$("." + key).html("<b>" + number_format(total29[key],2) + "</b>");
							REV_VAL10[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total30) {
							if(key.includes("29EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28EXPORT'])/intVal(QTY_VAL10['27EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("29DOMESTIC") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28DOMESTIC'])/intVal(QTY_VAL10['27DOMESTIC']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("29OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28OEM'])/intVal(QTY_VAL10['27OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("29YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28YTI'])/intVal(QTY_VAL10['27YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("29ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL10['28ECOM'])/intVal(QTY_VAL10['27ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total31) {
							$("." + key).html("<b>" + number_format(total31[key],2) + "</b>");
							QTY_VAL11[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total32) {
							$("." + key).html("<b>" + number_format(total32[key],2) + "</b>");
							REV_VAL11[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total33) {
							if(key.includes("32EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31EXPORT'])/intVal(QTY_VAL11['30EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("32DOMESTIC") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31DOMESTIC'])/intVal(QTY_VAL11['30DOMESTIC']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("32OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31OEM'])/intVal(QTY_VAL11['30OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("32YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31YTI'])/intVal(QTY_VAL11['30YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("32ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL11['31ECOM'])/intVal(QTY_VAL11['30ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total34) {
							$("." + key).html("<b>" + number_format(total34[key],2) + "</b>");
							QTY_VAL12[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total35) {
							$("." + key).html("<b>" + number_format(total35[key],2) + "</b>");
							REV_VAL12[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total36) {
							if(key.includes("35EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34EXPORT'])/intVal(QTY_VAL12['33EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("35DOMESTIC") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34DOMESTIC'])/intVal(QTY_VAL12['33DOMESTIC']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("35OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34OEM'])/intVal(QTY_VAL12['33OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("35YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34YTI'])/intVal(QTY_VAL12['33YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("35ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL12['34ECOM'])/intVal(QTY_VAL12['33ECOM'])*1000,2) + "</b>");
							}
						}
						
						for (var key in total37) {
							$("." + key).html("<b>" + number_format(total37[key],2) + "</b>");
							QTY_VAL13[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total38) {
							$("." + key).html("<b>" + number_format(total38[key],2) + "</b>");
							REV_VAL13[key] = intVal($('#master_product').find("."+key+"").text());
						}
						
						for (var key in total39) {
							if(key.includes("38EXPORT") == true){
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37EXPORT'])/intVal(QTY_VAL13['36EXPORT']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("38DOMESTIC") == true){                             
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37DOMESTIC'])/intVal(QTY_VAL13['36DOMESTIC']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("38OEM") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37OEM'])/intVal(QTY_VAL13['36OEM'])*1000,2) + "</b>");
							}                                                            
																						 
							if(key.includes("38YTI") == true){                            
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37YTI'])/intVal(QTY_VAL13['36YTI']),2) + "</b>");
							}                                                            
																						 
							if(key.includes("38ECOM") == true){                           
								$("." + key).html("<b>" + number_format(intVal(REV_VAL13['37ECOM'])/intVal(QTY_VAL13['36ECOM'])*1000,2) + "</b>");
							}
						}
						
						}
					});
				$.LoadingOverlay("hide");
				var cnt = $(".dt-buttons").contents();
				
				$(".dt-buttons").replaceWith(cnt);
				var cnta = $("#buttons").contents();
				$("#buttons").replaceWith(cnta);
				
				var intVal = function (i) {
					return typeof i === "string"
					? i.replace(/[\$,]/g, "") * 1
					: typeof i === "number"
					? i
					: 0;
				};
					
				//BIAR RAPIH
				table.columns.adjust();
				
				var dom_1  = intVal($(".1DOMESTIC").text());
				var dom_2  = intVal($(".4DOMESTIC").text());
				var dom_3  = intVal($(".7DOMESTIC").text());
				var dom_4  = intVal($(".10DOMESTIC").text());
				var dom_5  = intVal($(".13DOMESTIC").text());
				var dom_6  = intVal($(".16DOMESTIC").text());
				var dom_7  = intVal($(".19DOMESTIC").text());
				var dom_8  = intVal($(".22DOMESTIC").text());
				var dom_9  = intVal($(".25DOMESTIC").text());
				var dom_10 = intVal($(".28DOMESTIC").text());
				var dom_11 = intVal($(".31DOMESTIC").text());
				var dom_12 = intVal($(".34DOMESTIC").text());
				
				var exp_1  = intVal($(".1EXPORT").text());
				var exp_2  = intVal($(".4EXPORT").text());
				var exp_3  = intVal($(".7EXPORT").text());
				var exp_4  = intVal($(".10EXPORT").text());
				var exp_5  = intVal($(".13EXPORT").text());
				var exp_6  = intVal($(".16EXPORT").text());
				var exp_7  = intVal($(".19EXPORT").text());
				var exp_8  = intVal($(".22EXPORT").text());
				var exp_9  = intVal($(".25EXPORT").text());
				var exp_10 = intVal($(".28EXPORT").text());
				var exp_11 = intVal($(".31EXPORT").text());
				var exp_12 = intVal($(".34EXPORT").text());
				
				Highcharts.setOptions({
					lang: {
						thousandsSep: ','
					}
				});
				
				Highcharts.chart('container', {
				chart: {
					type: 'line'
				},
				title: {
					text: 'SALES SUMMARY REVENUE - <?=$year-1;?>'
				},
				subtitle: {
					text: 'PT YUPI INDO JELLY GUM'
				},
				xAxis: {
					categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
				},
				yAxis: {
					title: {
						text: 'Revenue'
					}
				},
				plotOptions: {
					line: {
						dataLabels: {
							enabled: true
						},
						enableMouseTracking: true
					}
				},
				series: [{
					name: 'DOMESTIC',
					data: [dom_1,
						   dom_2,
						   dom_3,
						   dom_4,
						   dom_5,
						   dom_6,
						   dom_7, 
						   dom_8, 
						   dom_9, 
						   dom_10,
						   dom_11,
						   dom_12
						   ]
				}, {
					name: 'INTERNATIONAL',
					data: [exp_1,
						   exp_2,
						   exp_3,
						   exp_4,
						   exp_5,
						   exp_6,
						   exp_7, 
						   exp_8, 
						   exp_9, 
						   exp_10,
						   exp_11,
						   exp_12
						   ]
				}]
				});
			}
		});
	  }
	  
	   function cari_cust_claim(){
		var ch					= document.getElementById("ch").value; 
		var i 					= 1;	
		//$('#proses_depres').show();	
		$('#cust_claim tbody').empty();	
		$("#cust_claim").dataTable().fnDestroy();		
		$.LoadingOverlay("show");	
		$.ajax({
			async: false,
			type: 'POST',
			url: defaulturl+"sales/summary_cust_claim",
			data: 
			{
				vars	: ch	
			},
		    beforeSend: function() {
				// setting a timeout
				$.LoadingOverlay("show");	
			},
			success: function(response) 
			{
				$.LoadingOverlay("hide");	
				$('#cust_claim tbody').empty();	
				$("#cust_claim").dataTable().fnDestroy();	
				$.each(response,function(index,item){
						$('#cust_claim tbody').append(
						"<tr>" +
							"<td style='width:15%;'><b>"+item.product_name+"</b></td>" +	
							"<td style='width:15%;' class='text-right '><b>"+number_format(item.value_text,2)+"%</b></td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jan_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.feb_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.mar_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.apr_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.may_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jun_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.jul_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.aug_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.sep_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.oct_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.nov_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.dec_rev,2)+"</td>" +	
							"<td style='width:10%;' class='text-right '>"+number_format(item.total_rev,2)+"</td>" +	
						"</tr>"
					);
				});
			},
			complete: function(){
					var table = $('#cust_claim').DataTable({
						scrollY: "470px",
						scrollX:        true,
						scrollCollapse: true,
						paging:         false,
						bSort: 			false,
						searching: 		true,
						fixedColumns:   {
							leftColumns: 1
						},
						dom: 'Bfrtip',
						buttons: [
							{
						extend: 'collection',
							text: 'Export Data',
							buttons: [
								{
									extend: 'copyHtml5',
									text: 'To clipboard'
								},
								{
									extend: 'csvHtml5',
									text: 'CSV'
								},
								{
									extend: 'excelHtml5',
									text: 'Excel'
								},
							]
						}
						]
					});
				$.LoadingOverlay("hide");
				var cnt = $(".dt-buttons").contents();
				
				$(".dt-buttons").replaceWith(cnt);
				var cnta = $("#buttons").contents();
				$("#buttons").replaceWith(cnta);
				
				//BIAR RAPIH
				table.columns.adjust();
			}
		});
	  }
	  
	  function cari_key_product(){
		var dept			= document.getElementById("chxx").value; 
		var i 				= 1;	
		$('#master_product_key tbody').empty();	
		$("#master_product_key").dataTable().fnDestroy();		
		$.LoadingOverlay("show");	
		$.ajax({
			async: false,
			type: 'POST',
			url: defaulturl+"sales/cari_key_baru",
			data: 
			{
				dept:dept
			},
			success: function(response) 
			{
				$('#master_product_key tbody').empty();	
				$("#master_product_key").dataTable().fnDestroy();	
				$.each(response,function(index,item){
					if(item.key_product !== "GRAND TOTAL"){
					$('#master_product_key tbody').append(
						"<tr>" +
							"<td style='width:3%;'>" + i + "</td>" +
							"<td style='width:15%;'>"+item.key_product+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.jan_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.jan_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.jan_asp,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.feb_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.feb_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.feb_asp,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.mar_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.mar_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.mar_asp,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.apr_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.apr_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.apr_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.may_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.may_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.may_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.jun_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.jun_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.jun_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.jul_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.jul_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.jul_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.aug_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.aug_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.aug_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.sep_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.sep_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.sep_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.oct_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.oct_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.oct_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.nov_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.nov_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.nov_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.dec_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.dec_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '>"+number_format(item.dec_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.total_qty,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.total_rev,2)+"</td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '>"+number_format(item.total_asp,2)+"</td>" +
						"</tr>"
					);
					i++;
					}else{
					$('#master_product_key tbody').append(
						"<tr>" +
							"<td style='width:3%;'></td>" +
							"<td style='width:15%;'><b>"+item.key_product+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.jan_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.jan_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.jan_asp,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.feb_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.feb_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.feb_asp,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.mar_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.mar_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.mar_asp,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.apr_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.apr_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.apr_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.may_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.may_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.may_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.jun_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.jun_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.jun_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.jul_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.jul_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.jul_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.aug_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.aug_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.aug_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.sep_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.sep_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.sep_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.oct_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.oct_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.oct_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.nov_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.nov_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.nov_asp,2)+"</b></td>" +
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.dec_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.dec_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#D6EAF8;' class='text-right '><b>"+number_format(item.dec_asp,2)+"</td>" +
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.total_qty,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.total_rev,2)+"</b></td>" +	
							"<td style='width:10%;background-color:#EBF5FB;' class='text-right '><b>"+number_format(item.total_asp,2)+"</b></td>" +
						"</tr>"
					);	
					}
				});
				$.LoadingOverlay("hide");
			},
			complete: function(){
				setTimeout(function(){
					var groupColumn = 0;
					var table = $('#master_product_key').DataTable({
						scrollY: "470px",
						scrollX:        true,
						scrollCollapse: true,
						paging:         false,
						bSort: 			false,
						searching: 		true,
						lengthChange: true,
						heightMatch: 'auto',
						fixedColumns:   {
							leftColumns: 2
						},
						dom: 'Bfrtip',
						buttons: [
							{
						extend: 'collection',
							text: 'Export Data',
							buttons: [
								{
									extend: 'copyHtml5',
									text: 'To clipboard'
								},
								{
									extend: 'csvHtml5',
									text: 'CSV'
								},
								{
									extend: 'excelHtml5',
									text: 'Excel'
								},
							]
						}
						]
					});
				
				var cnt = $(".dt-buttons").contents();
				
				$(".dt-buttons").replaceWith(cnt);
				var cnta = $("#buttons").contents();
				$("#buttons").replaceWith(cnta);
				//BIAR RAPIH
				table.columns.adjust();
				}, 500);
			}
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
	  	dt = $("#table_sales").DataTable({
	  		autoWidth: false,
	  		responsive: false,
	  		ordering: false,
	  		paging: false,
	  		scrollX: true,
	  		scrollY: "55vh",
	  		scrollCollapse: true,
	  		dom: "Blfrtip",
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
	  	$(window).off("resize.sales").on("resize.sales", function(){ if (dt) dt.columns.adjust(); });
	  	if ($.LoadingOverlay) $.LoadingOverlay("hide");
	  	}
	  });
	  }
	  
	  var COL_HEADER = 0;
	  var COL_CATEGORY = 1;
	  var COL_MONTH_START = 2;
	  var COL_TOTAL = COL_MONTH_START + 12;
		    
	  if (typeof number_format !== "function") {
	  window.number_format = function(num, dec) {
	  	if (num === null || num === undefined || num === "") return "0.00";
	  	var n = parseFloat(num); if (isNaN(n)) n = 0;
	  	var fixed = n.toFixed(dec || 2);
	  	var parts = fixed.split(".");
	  	parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	  	return parts.join(".");
	  };
	  }
	  
	  function parse_num(str) {
	  if (str === null || str === undefined) return 0;
	  var s = String(str).replace(/,/g, "");
	  var n = parseFloat(s);
	  return isNaN(n) ? 0 : n;
	  }
	  
	  function isDiscountRowName(name) {
	  if (!name) return false;
	  var s = String(name).toLowerCase();
	  return s.indexOf("discount") > -1;
	  }
		    
	  function buildRow(item){
	  var isDiscount = isDiscountRowName(item.named);
	  var html = "";
	  html += '<tr data-group="' + item.groups + '" data-name="' + item.named + '"' + (isDiscount ? ' class="discount-row"' : '') + '>';
	  html += '<td class="text-left" nowrap>' + item.groups + '</td>';
	  html += '<td class="text-left" nowrap>' + item.named + '</td>';
	  for (var m = 1; m <= 12; m++) {
	  	var val = item['isi_' + m];
	  	if (isDiscount) {
	  	html += '<td>' +
	  				'<input type="text" style="width:80px;" class="discount-input text-right" data-group="' + item.groups + '" data-month="' + m + '" data-row="' + item.named + '" value="' + number_format(val, 2) + '" />' +
	  			'</td>';
	  	} else {
	  	html += '<td class="num"><span class="val">' + number_format(val, 2) + '</span></td>';
	  	}
	  }
	  var rowTot = 0; for (var k = 1; k <= 12; k++) rowTot += parse_num(item['isi_' + k]);
	  html += '<td class="num"><span class="row-total">' + number_format(rowTot, 2) + '</span></td>';
	  html += '</tr>';
	  return html;
	  }
		    
	  function computeRowTotal(tr){
	  var $tr = $(tr);
	  var isDiscount = $tr.hasClass("discount-row");
	  var sum = 0;
	  if (isDiscount) { $tr.find("input.discount-input").each(function(){ sum += parse_num(this.value); }); }
	  else { for (var c = COL_MONTH_START; c < COL_TOTAL; c++) { sum += parse_num($tr.find("td").eq(c).text()); } }
	  $tr.find(".row-total").text(number_format(sum, 2));
	  return sum;
	  }
		    
	  function ensureSingleSubtotalRow(group) {
	  var $sub = $("#table_sales tbody tr.subtotal-row").filter(function(){ return $(this).data("group") === group; });
	  var $lastGroupRow = $("#table_sales tbody tr").filter(function(){ return $(this).data("group") === group && !$(this).hasClass("subtotal-row"); }).last();
	  if ($lastGroupRow.length === 0) return;
		    
	  if ($sub.length === 0) {
	  	var row = '<tr class="subtotal-row" data-group="' + group + '">' +
	  				'<td class="text-left">' + group + '</td>' +
	  				'<td class="text-left">Subtotal</td>';
	  	for (var i = 0; i < 12; i++) row += '<td class="num">0.00</td>';
	  	row += '<td class="num">0.00</td></tr>';
	  	$lastGroupRow.after(row);
	  } else {
	  	if (!$sub.prev().is($lastGroupRow)) { $sub.detach(); $lastGroupRow.after($sub); }
	  }
	  }
		    
	  function computeGroupSums(group) {
	  var sums = [0,0,0,0,0,0,0,0,0,0,0,0,0];
	  var $rows = $("#table_sales tbody tr").filter(function(){ return $(this).data("group") === group && !$(this).hasClass("subtotal-row"); });
	  $rows.each(function(){
	  	var $tr = $(this);
	  	var isDisc = $tr.hasClass("discount-row");
	  	for (var m = 0; m < 12; m++) {
	  	var val = 0;
	  	if (isDisc) val = parse_num($tr.find('input.discount-input[data-month="' + (m+1) + '"]').val());
	  	else val = parse_num($tr.find("td").eq(COL_MONTH_START + m).text());
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
	  $("#table_sales").off("input.discount blur.discount");
	  $("#table_sales").on("input.discount", "input.discount-input", function(){
	  	var $tr = $(this).closest("tr");
	  	computeRowTotal($tr);
	  	var g = $tr.data("group");
	  	renderSubtotals([g]);
	  });
	  $("#table_sales").on("blur.discount", "input.discount-input", function(){ this.value = number_format(parse_num(this.value), 2); });
	  }
		    
	  var dt = null;
	  
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
	  	dt = $("#table_sales").DataTable({
	  		autoWidth: false,
	  		responsive: false,
	  		ordering: false,
	  		paging: false,
	  		scrollX: true,
	  		scrollY: "55vh",
	  		scrollCollapse: true,
	  		dom: "Blfrtip",
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
	  	$(window).off("resize.sales").on("resize.sales", function(){ if (dt) dt.columns.adjust(); });
	  	if ($.LoadingOverlay) $.LoadingOverlay("hide");
	  	}
	  });
	  }
		    
	  window.cari_data = cari_data;
	  
	  function parse_num(str) { if (str === null || str === undefined) return 0; var s = String(str).replace(/,/g, ''); var n = parseFloat(s); return isNaN(n) ? 0 : n; }
		    
	  // ====== Mapping dept berdasar data-group di input ======
	  function mapDeptFromGroupName(groupStr) {
	  var g = (groupStr || '').toString().toLowerCase();
	  if (g.indexOf('domestic') > -1 || g.indexOf('domestik') > -1) return 600;   // DOMESTIC
	  if (g.indexOf('international') > -1 || g.indexOf('internasional') > -1) return 700; // INTERNATIONAL
	  // fallback ke select bila tidak terdeteksi
	  var sel = document.getElementById('typex_reclass');
	  var raw = sel ? (sel.value || (sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].text : '')) : '';
	  var n = parseInt(raw, 10);
	  return isNaN(n) ? raw : n;
	  }
		    
	  // ====== Kumpulkan payload simpan dari baris diskon (per baris) ======
	  function collectDiscountPayload(){
	  var year 	= <?=$year;?>;
	  var notes 	= "";
		    
	  var rows = [];
	  // Tiap baris diskon
	  $('#table_sales tbody tr.discount-row').each(function(){
	  	var $tr = $(this);
	  	// ambil data-group dari input pertama (identik untuk 12 bulan)
	  	var $firstInput = $tr.find('input.discount-input').first();
	  	var id_dept = mapDeptFromGroupName($firstInput.attr('data-group') || $tr.data('group'));
		    
	  	var rec = {
	  	id_coa: $tr.data('coa') || null,
	  	id_dept: id_dept,
	  	year_code: year,
	  	notes: notes,
	  	m1:0,m2:0,m3:0,m4:0,m5:0,m6:0,m7:0,m8:0,m9:0,m10:0,m11:0,m12:0,
	  	total: 0
	  	};
	  	for (var m = 1; m <= 12; m++){
	  	var v = parse_num($tr.find('input.discount-input[data-month="' + m + '"]').val());
	  	rec['m' + m] = v; rec.total += v;
	  	}
	  	rows.push(rec);
	  });
	  return { rows: rows };
	  }
		    
	  // ====== Kirim ke backend ======
	  function saveDiscountReclass(){
	  var payload = collectDiscountPayload();
	  if (!payload.rows.length) { alert('Tidak ada baris Discount untuk disimpan.'); return; }
	  $.ajax({
	  	type: 'POST',
	  	url: defaulturl + 'balance/save_reclass_discount',
	  	data: { rows: JSON.stringify(payload.rows) },
	  	success: function(res){
	  	try { var r = typeof res === 'string' ? JSON.parse(res) : res; } catch(e) { r = { ok:true }; }
	  	alert('Sukses menyimpan ' + (r.saved || payload.rows.length) + ' record.');
	  	},
	  	error: function(){ alert('Gagal menyimpan, coba lagi.'); }
	  });
	  }
		    
	  // Bind tombol
	  $(document).on('click', '#btnSaveReclass', saveDiscountReclass);
	  
	  </script>
    </body>
</html>
