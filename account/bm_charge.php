<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= Date('Y');
	$month	= Date('m');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_charge_search.php'
		,	data :{
				'year':$('#lblYYMM').text()
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				$('div[id^="lblIn"]').text('');
				$('div[id^="lblOut"]').text('');
				$('div[id^="lblToT"]').text('');

				$('button').attr('disabled',false);
				$('span[id="lblMsg"]').hide();

				if (!data) return;

				var row = data.split('?');

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);
						var obj = $('#ID_ROW_'+col['month']);

						for(var j=1; j<=6; j++){
							$('#lblInCnt'+j,obj).text(col['inCnt'+j] > 0 ? __num2str(col['inCnt'+j]) : '');
							$('#lblInAmt'+j,obj).text(col['inAmt'+j] > 0 ? __num2str(col['inAmt'+j]) : '');
						}
						//$('#lblInCntX',obj).text(col['inCntX'] > 0 ? __num2str(col['inCntX']) : '');
						//$('#lblInAmtX',obj).text(col['inAmtX'] > 0 ? __num2str(col['inAmtX']) : '');
						$('#lblOutCnt',obj).text(col['outCnt'] > 0 ? __num2str(col['outCnt']) : '');
						$('#lblOutAmt',obj).text(col['outAmt'] > 0 ? __num2str(col['outAmt']) : '');

						if (col['close'] == 'Y'){
							$('#btnIn',obj).attr('disabled',true);
							$('#btnOut',obj).attr('disabled',true);
							$('#lblMsg',obj).show();
						}
					}
				}

				lfSum();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSum(){
		var val = {};

		for(var i=1; i<=6; i++){
			val['inCnt'+i] = 0;
			val['inAmt'+i] = 0;
		}
		//val['inCntX'] = 0;
		//val['inAmtX'] = 0;
		val['outCnt'] = 0;
		val['outAmt'] = 0;

		for(var i=1; i<=12; i++){
			var obj = $('#ID_ROW_'+i);

			for(var j=1; j<=6; j++){
				val['inCnt'+j] += __str2num($('#lblInCnt'+j,obj).text());
				val['inAmt'+j] += __str2num($('#lblInAmt'+j,obj).text());
			}
			//val['inCntX'] += __str2num($('#lblInCntX',obj).text());
			//val['inAmtX'] += __str2num($('#lblInAmtX',obj).text());
			val['outCnt'] += __str2num($('#lblOutCnt',obj).text());
			val['outAmt'] += __str2num($('#lblOutAmt',obj).text());
		}

		for(var i=1; i<=6; i++){
			$('#lblToTInCnt'+i).text(__num2str(val['inCnt'+i]));
			$('#lblToTInAmt'+i).text(__num2str(val['inAmt'+i]));
		}
		//$('#lblToTInCntX').text(__num2str(val['inCntX']));
		//$('#lblToTInAmtX').text(__num2str(val['inAmtX']));
		$('#lblToTOutCnt').text(__num2str(val['outCnt']));
		$('#lblToTOutAmt').text(__num2str(val['outAmt']));
	}

	function lfOut(month){
		var objModal = new Object();
		var url = './bm_charge_reg.php';
		var style = 'dialogWidth:600px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.year	= $('#lblYYMM').text();
		objModal.month	= (month < 10 ? '0' : '')+month;

		window.showModalDialog(url, objModal, style);
	}

	function lfSetOut(month, val){
		var obj = $('#ID_ROW_'+month);

		$('#lblOutCnt',obj).text(__num2str(val['cnt']));
		$('#lblOutAmt',obj).text(__num2str(val['amt']));

		lfSum();
	}

	function lfIn(month){
		var objModal = new Object();
		var url = './bm_salary_other.php';
		var style = 'dialogWidth:600px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.year	= $('#lblYYMM').text();
		objModal.month	= (month < 10 ? '0' : '')+month;

		window.showModalDialog(url, objModal, style);
	}

	function lfSetIn(month, val){
		var obj = $('#ID_ROW_'+month);

		for(var i=1; i<=6; i++){
			$('#lblInCnt'+i,obj).text(__num2str(val['cnt'+i]));
			$('#lblInAmt'+i,obj).text(__num2str(val['amt'+i]));
		}
		//$('#lblInCntX',obj).text(__num2str(val['cntX']));
		//$('#lblInAmtX',obj).text(__num2str(val['amtX']));

		lfSum();
	}
</script>
<div class="title title_border">일반경비관리</div>
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
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); lfSearch();" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); lfSearch();" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="30px">
		<col width="33px">
		<col width="65px">
		<col width="33px">
		<col width="65px">
		<col width="33px">
		<col width="65px">
		<col width="33px">
		<col width="65px">
		<col width="33px">
		<col width="65px">
		<col width="33px">
		<col width="65px">
		<col width="33px">
		<col width="65px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="3">월</th>
			<th class="head" colspan="12">기타수입</th>
			<th class="head" rowspan="2" colspan="2">일반경비</th>
			<th class="head last" rowspan="3">비고</th>
		</tr>
		<tr>
			<th class="head" colspan="2">치매수당매출</th>
			<th class="head" colspan="2">치매관리자</th>
			<th class="head" colspan="2">사복가산금매출</th>
			<th class="head" colspan="2">장기근속수당</th>
			<th class="head" colspan="2">장기근속관리자</th>
			<th class="head" colspan="2">기타매출</th>
		</tr>
		<tr>
			<th class="head">건수</th>
			<th class="head">금액</th>
			<th class="head">건수</th>
			<th class="head">금액</th>
			<th class="head">건수</th>
			<th class="head">금액</th>
			<th class="head">건수</th>
			<th class="head">금액</th>
			<th class="head">건수</th>
			<th class="head">금액</th>
			<th class="head">건수</th>
			<th class="head">금액</th>
			<th class="head">건수</th>
			<th class="head">금액</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center sum">합계</td>
			<td class="center sum"><div id="lblToTInCnt2" class="right"></div></td>
			<td class="center sum"><div id="lblToTInAmt2" class="right"></div></td>
			<td class="center sum"><div id="lblToTInCnt5" class="right"></div></td>
			<td class="center sum"><div id="lblToTInAmt5" class="right"></div></td>
			<td class="center sum"><div id="lblToTInCnt3" class="right"></div></td>
			<td class="center sum"><div id="lblToTInAmt3" class="right"></div></td>
			<td class="center sum"><div id="lblToTInCnt4" class="right">0</div></td>
			<td class="center sum"><div id="lblToTInAmt4" class="right">0</div></td>
			<td class="center sum"><div id="lblToTInCnt6" class="right"></div></td>
			<td class="center sum"><div id="lblToTInAmt6" class="right"></div></td>
			<td class="center sum"><div id="lblToTInCnt1" class="right"></div></td>
			<td class="center sum"><div id="lblToTInAmt1" class="right"></div></td>
			<td class="center sum"><div id="lblToTOutCnt" class="right"></div></td>
			<td class="center sum"><div id="lblToTOutAmt" class="right"></div></td>
			<td class="center sum last"></td>
		</tr><?
		for($i=1; $i<=12; $i++){?>
			<tr id="ID_ROW_<?=$i;?>">
				<td class="center"><?=$i;?>월</td>
				<td class="center"><div id="lblInCnt2" class="right"></div></td>
				<td class="center"><div id="lblInAmt2" class="right"></div></td>
				<td class="center"><div id="lblInCnt5" class="right"></div></td>
				<td class="center"><div id="lblInAmt5" class="right"></div></td>
				<td class="center"><div id="lblInCnt3" class="right"></div></td>
				<td class="center"><div id="lblInAmt3" class="right"></div></td>
				<td class="center"><div id="lblInCnt4" class="right"></div></td>
				<td class="center"><div id="lblInAmt4" class="right"></div></td>
				<td class="center"><div id="lblInCnt6" class="right"></div></td>
				<td class="center"><div id="lblInAmt6" class="right"></div></td>
				<td class="center"><div id="lblInCnt1" class="right"></div></td>
				<td class="center"><div id="lblInAmt1" class="right"></div></td>
				<td class="center"><div id="lblOutCnt" class="right"></div></td>
				<td class="center"><div id="lblOutAmt" class="right"></div></td>
				<td class="center last">
					<div class="left">
						<span class="btn_pack small"><button id="btnIn" onclick="lfIn('<?=$i;?>');">기타수입</button></span>
						<span class="btn_pack small"><button id="btnOut" onclick="lfOut('<?=$i;?>');">일반경비</button></span>
						<span class="left" id="lblMsg" style="display:none; color:RED;">*마감되었습니다.</span>
					</div>
				</td>
			</tr><?
		}?>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_db_close.php");
?>