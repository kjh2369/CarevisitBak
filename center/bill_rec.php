<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$year	= Date('Y');
	$month	= IntVal(Date('m'));
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST',
			url:'./bill_rec_search.php',
			data:{
				'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(data){
				var row = data.split('?');
				var tot = {};

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);

						col['ACCT_TOT'] = __str2num(col['MON_PAY'])+__str2num(col['NON_PAY']);
						col['NON_TOT'] = __str2num(col['NON_PAY'])+__str2num(col['NON_AMT']);

						$('#ID_CELL_'+col['month']+'_ACCT_TOT').text(col['ACCT_TOT'] > 0 ? __num2str(col['ACCT_TOT']) : '');
						$('#ID_CELL_'+col['month']+'_MON_PAY').text(col['ACCT_TOT'] > 0 ? __num2str(col['MON_PAY']) : '');
						$('#ID_CELL_'+col['month']+'_NON_PAY').text(col['ACCT_TOT'] > 0 ? __num2str(col['NON_PAY']) : '');
						$('#ID_CELL_'+col['month']+'_IN_AMT').text(col['ACCT_TOT'] > 0 ? __num2str(col['IN_AMT']) : '');
						$('#ID_CELL_'+col['month']+'_CUT_AMT').text(col['ACCT_TOT'] > 0 ? __num2str(col['CUT_AMT']) : '');
						$('#ID_CELL_'+col['month']+'_NON_AMT').text(col['ACCT_TOT'] > 0 ? __num2str(col['NON_AMT']) : '');
						$('#ID_CELL_'+col['month']+'_NON_TOT').text(col['ACCT_TOT'] > 0 ? __num2str(col['NON_TOT']) : '');

						tot['ACCT_TOT'] = __str2num(tot['ACCT_TOT']) + __str2num(col['ACCT_TOT']);
						tot['MON_PAY'] = __str2num(tot['MON_PAY']) + __str2num(col['MON_PAY']);
						tot['NON_PAY'] = __str2num(tot['NON_PAY']) + __str2num(col['NON_PAY']);
						tot['IN_AMT'] = __str2num(tot['IN_AMT']) + __str2num(col['IN_AMT']);
						tot['CUT_AMT'] = __str2num(tot['CUT_AMT']) + __str2num(col['CUT_AMT']);
						tot['NON_AMT'] = __str2num(tot['NON_AMT']) + __str2num(col['NON_AMT']);
						tot['NON_TOT'] = __str2num(tot['NON_TOT']) + __str2num(col['NON_TOT']);

						if (col['ACCT_TOT'] > 0){
							$('#ID_CELL_'+col['month']+'_BTN').show();
						}else{
							$('#ID_CELL_'+col['month']+'_BTN').hide();
						}
					}
				}

				$('#ID_CELL_ACCT_TOT').text(tot['ACCT_TOT'] > 0 ? __num2str(tot['ACCT_TOT']) : '');
				$('#ID_CELL_MON_PAY').text(tot['ACCT_TOT'] > 0 ? __num2str(tot['MON_PAY']) : '');
				$('#ID_CELL_NON_PAY').text(tot['ACCT_TOT'] > 0 ? __num2str(tot['NON_PAY']) : '');
				$('#ID_CELL_IN_AMT').text(tot['ACCT_TOT'] > 0 ? __num2str(tot['IN_AMT']) : '');
				$('#ID_CELL_CUT_AMT').text(tot['ACCT_TOT'] > 0 ? __num2str(tot['CUT_AMT']) : '');
				$('#ID_CELL_NON_AMT').text(tot['ACCT_TOT'] > 0 ? __num2str(tot['NON_AMT']) : '');
				$('#ID_CELL_NON_TOT').text(tot['ACCT_TOT'] > 0 ? __num2str(tot['NON_TOT']) : '');

				$('#tempLodingBar').remove();
			},
			error: function (request, status, error){
				$('#tempLodingBar').remove();

				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function lfSetMonth(month){
		$('div[id^="btnMonth_"]').each(function(){
			if ($(this).hasClass('my_month_y')){
				$(this).removeClass('my_month_y').addClass('my_month_1');
			}
		});

		$('#btnMonth_'+month).removeClass('my_month_1').addClass('my_month_y');
		$('#lblYYMM').attr('month',month);

		lfSearch();
	}

	function lfBillDtl(month){
		$.ajax({
			type:'POST',
			url:'./bill_rec_dtl.php',
			data:{
				'year'	:$('#lblYYMM').attr('year')
			,	'month'	:month
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(html){
				$('#ID_BILL_DTL').html(html).show();
				$('#tempLodingBar').remove();
			},
			error: function (request, status, error){
				$('#tempLodingBar').remove();

				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function lfBillClose(){
		$('#ID_BILL_DTL').hide();
	}
</script>
<div class="title title_border">청구내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td class="last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1,'lfSearch()');" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1,'lfSearch()');" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last"><?//$myF->_btn_month($month,'lfSetMonth(');?></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="80px" span="6">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">월</th>
			<th class="head" colspan="3">청구내역</th>
			<th class="head" colspan="3">입금내역</th>
			<th class="head" rowspan="2">미납합계</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">합계</th>
			<th class="head">당월분</th>
			<th class="head">미납분</th>
			<th class="head">입금액</th>
			<th class="head">감면액</th>
			<th class="head">미납금</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center sum">합계</td>
			<td class="center sum"><div id="ID_CELL_ACCT_TOT" class="right"></div></td>
			<td class="center sum"><div id="ID_CELL_MON_PAY" class="right"></div></td>
			<td class="center sum"><div id="ID_CELL_NON_PAY" class="right"></div></td>
			<td class="center sum"><div id="ID_CELL_IN_AMT" class="right"></div></td>
			<td class="center sum"><div id="ID_CELL_CUT_AMT" class="right"></div></td>
			<td class="center sum"><div id="ID_CELL_NON_AMT" class="right"></div></td>
			<td class="center sum"><div id="ID_CELL_NON_TOT" class="right"></div></td>
			<td class="center last sum"></td>
		</tr><?
		for($i=1; $i<=12; $i++){?>
			<tr>
				<td class="center"><?=$i;?>월</td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_ACCT_TOT" class="right"></div></td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_MON_PAY" class="right"></div></td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_NON_PAY" class="right"></div></td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_IN_AMT" class="right"></div></td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_CUT_AMT" class="right"></div></td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_NON_AMT" class="right"></div></td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_NON_TOT" class="right"></div></td>
				<td class="center last">
					<div id="ID_CELL_<?=$i;?>_BTN" class="left" style="display:none;">
						<span class="btn_pack small"><button onclick="lfBillDtl(<?=$i;?>);">청구상세내역</button></span>
					</div>
				</td>
			</tr><?
		}?>
	</tbody>
</table>
<div id="ID_BILL_DTL" style="position:absolute; left:200px; top:200px; width:800px; height:600px; border:3px solid #4374D9; background-color:WHITE; display:none;"></div>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>