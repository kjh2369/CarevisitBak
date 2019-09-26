<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$power	= $_SESSION['userLevel'];
	$year	= Date('Y');
	$month	= Date('m');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_salary_search.php'
		,	data :{
				'year':$('#lblYYMM').text()
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				$('input:text[id^="txt"]').val('0');

				if (!data) return;

				var row = data.split('?');

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);
						var obj = $('#ID_ROW_'+col['month']);

						$('#txtManagerCnt',obj).val(__num2str(col['mgCnt']));
						$('#txtManagerPay',obj).val(__num2str(col['mgPay']));
						$('#txtManager4Insu',obj).val(__num2str(col['mgIns']));
						$('#txtMemberCnt',obj).val(__num2str(col['mmCnt']));
						$('#txtMemberPay',obj).val(__num2str(col['mmPay']));
						$('#txtmember4Insu',obj).val(__num2str(col['mmIns']));

						$('#lblOtherCnt1',obj).text(__num2str(col['otCnt1']));
						$('#lblOtherAmt1',obj).text(__num2str(col['otAmt1']));
						$('#lblOtherCnt2',obj).text(__num2str(col['otCnt2']));
						$('#lblOtherAmt2',obj).text(__num2str(col['otAmt2']));
					}
				}

				lfSum();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSet(obj){
		$.ajax({
			type :'POST'
		,	url  :'./bm_salary_set.php'
		,	data :{
				'year'	:$('#lblYYMM').text()
			,	'month'	:$(obj).attr('month')
			,	'id'	:$(obj).attr('id')
			,	'val'	:$(obj).val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					lfSum();
				}else if (result == 9){
					alert('데이타 전송중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSum(){
		var mgPay = 0, mg4Insu = 0, mmPay = 0, mm4Insu = 0, otCnt1 = 0, otAmt1 = 0, otCnt2 = 0, otAmt2 = 0;

		for(var i=1; i<=12; i++){
			var obj = $('#ID_ROW_'+i);

			mgPay	+= __str2num($('#txtManagerPay',obj).val());
			mg4Insu += __str2num($('#txtManager4Insu',obj).val());
			mmPay	+= __str2num($('#txtMemberPay',obj).val());
			mm4Insu += __str2num($('#txtmember4Insu',obj).val());

			otCnt1	+= __str2num($('#lblOtherCnt1',obj).text());
			otAmt1	+= __str2num($('#lblOtherAmt1',obj).text());
			otCnt2	+= __str2num($('#lblOtherCnt2',obj).text());
			otAmt2	+= __str2num($('#lblOtherAmt2',obj).text());
		}

		$('#lblManagerPay').text(__num2str(mgPay));
		$('#lblManager4Insu').text(__num2str(mg4Insu));
		$('#lblMemberPay').text(__num2str(mmPay));
		$('#lblMember4Insu').text(__num2str(mm4Insu));

		var obj = $('#ID_ROW_SUM');

		$('#lblOtherCnt1',obj).text(__num2str(otCnt1));
		$('#lblOtherAmt1',obj).text(__num2str(otAmt1));
		$('#lblOtherCnt2',obj).text(__num2str(otCnt2));
		$('#lblOtherAmt2',obj).text(__num2str(otAmt2));
	}

	function lfOther(month){
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

		$('#lblOtherCnt1',obj).text(__num2str(val['cnt1']));
		$('#lblOtherAmt1',obj).text(__num2str(val['amt1']));
		$('#lblOtherCnt2',obj).text(__num2str(val['cnt2']));
		$('#lblOtherAmt2',obj).text(__num2str(val['amt2']));

		lfSum();
	}
</script>
<div class="title title_border">정직임금.기타수입관리</div>
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
		<col width="40px">
		<col width="50px">
		<col width="90px">
		<col width="90px">
		<col width="50px">
		<col width="90px">
		<col width="90px">
		<col width="60px">
		<col width="80px">
		<col width="60px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">월</th>
			<th class="head" colspan="3">센터장</th>
			<th class="head" colspan="3">정직원</th>
			<th class="head" colspan="2">기타수입</th>
			<th class="head" colspan="2">기타(매출미수)</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">인원</th>
			<th class="head">급여</th>
			<th class="head">4보험(회사분)</th>
			<th class="head">인원</th>
			<th class="head">급여</th>
			<th class="head">4보험(회사분)</th>
			<th class="head">건수</th>
			<th class="head">금액</th>
			<th class="head">건수</th>
			<th class="head">금액</th>
		</tr>
	</thead>
	<tbody>
		<tr id="ID_ROW_SUM">
			<td class="center sum">합계</td>
			<td class="center sum">-</td>
			<td class="center sum"><div id="lblManagerPay" class="right">0</div></td>
			<td class="center sum"><div id="lblManager4Insu" class="right">0</div></td>
			<td class="center sum">-</td>
			<td class="center sum"><div id="lblMemberPay" class="right">0</div></td>
			<td class="center sum"><div id="lblMember4Insu" class="right">0</div></td>
			<td class="center sum"><div id="lblOtherCnt1" class="right">0</div></td>
			<td class="center sum"><div id="lblOtherAmt1" class="right">0</div></td>
			<td class="center sum"><div id="lblOtherCnt2" class="right">0</div></td>
			<td class="center sum"><div id="lblOtherAmt2" class="right">0</div></td>
			<td class="center sum last"></td>
		</tr><?
		for($i=1; $i<=12; $i++){?>
			<tr id="ID_ROW_<?=$i;?>">
				<td class="center"><?=$i;?>월</td>
				<td class="center" style="<?=($power == 'A' ? 'background-color:#EAEAEA;' : '');?>">
					<input id="txtManagerCnt" type="text" value="0" class="number" style="width:100%;" month="<?=$i;?>" onchange="lfSet(this);" <?=($power == 'A' ? 'readonly' : '');?>>
				</td>
				<td class="center" style="<?=($power == 'A' ? 'background-color:#EAEAEA;' : '');?>">
					<input id="txtManagerPay" type="text" value="0" class="number" style="width:100%;" month="<?=$i;?>" onchange="lfSet(this);" <?=($power == 'A' ? 'readonly' : '');?>>
				</td>
				<td class="center" style="<?=($power != 'A' ? 'background-color:#EAEAEA;' : '');?>">
					<input id="txtManager4Insu" type="text" value="0" class="number" style="width:100%;" month="<?=$i;?>" onchange="lfSet(this);" <?=($power != 'A' ? 'readonly' : '');?>>
				</td>
				<td class="center" style="<?=($power == 'A' ? 'background-color:#EAEAEA;' : '');?>">
					<input id="txtMemberCnt" type="text" value="0" class="number" style="width:100%;" month="<?=$i;?>" onchange="lfSet(this);" <?=($power == 'A' ? 'readonly' : '');?>>
				</td>
				<td class="center" style="<?=($power == 'A' ? 'background-color:#EAEAEA;' : '');?>">
					<input id="txtMemberPay" type="text" value="0" class="number" style="width:100%;" month="<?=$i;?>" onchange="lfSet(this);" <?=($power == 'A' ? 'readonly' : '');?>>
				</td>
				<td class="center" style="<?=($power != 'A' ? 'background-color:#EAEAEA;' : '');?>">
					<input id="txtMember4Insu" type="text" value="0" class="number" style="width:100%;" month="<?=$i;?>" onchange="lfSet(this);" <?=($power != 'A' ? 'readonly' : '');?>>
				</td>
				<td class="center"><div id="lblOtherCnt1" class="right"></div></td>
				<td class="center"><div id="lblOtherAmt1" class="right"></div></td>
				<td class="center"><div id="lblOtherCnt2" class="right"></div></td>
				<td class="center"><div id="lblOtherAmt2" class="right"></div></td>
				<td class="center last">
					<div class="left">
						<span class="btn_pack small"><button onclick="lfOther('<?=$i;?>');">기타수입</button></span>
					</div>
				</td>
			</tr><?
		}?>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_db_close.php");
?>