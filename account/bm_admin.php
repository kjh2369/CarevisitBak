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
		,	url  :'bm_admin_search2.php'
		,	data :{
				'year':$('#lblYYMM').text()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				//$('#ID_LIST').html(html);
				var html = html.split('<!--CUT_LINE-->');

				$('#ID_LIST').html(html[0]);
				$('#ID_SUM').html(html[1]);

				$('div[id^="ID_CELL_VAL_"]').attr('selYn','N').css('cursor','default').unbind('mouseover').bind('mouseover',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','FAF4C0');
				}).unbind('mouseout').bind('mouseout',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','');
				}).unbind('click').bind('click',function(){
					$(this).attr('selYn','Y').css('background-color','D9E5FF');
					lfSet(this);
				});
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSet(obj){
		var objModal = new Object();
		var url = './bm_admin_salary_set.php';
		var style = 'dialogWidth:750px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.orgNo	= $(obj).attr('orgNo');
		objModal.year	= $('#lblYYMM').text();
		objModal.month	= ($(obj).attr('month') < 10 ? '0' : '')+$(obj).attr('month');

		window.showModalDialog(url, objModal, style);

		//$(obj).attr('selYn','N').mouseout();
	}

	function lfSetSub(orgNo, month, val){
		$('#ID_CELL_VAL_'+orgNo+'_'+month+'_MG').attr('amt',val['mgAmt']).attr('ins',val['mgIns']).attr('retire',val['mgRetire']).text(val['mgCnt']);
		$('#ID_CELL_VAL_'+orgNo+'_'+month+'_MM').attr('amt',val['mmAmt']).attr('ins',val['mmIns']).attr('retire',val['mmRetire']).text(val['mmCnt']);
		$('#ID_CELL_SUM_'+orgNo+'_'+month).text(val['mgCnt'] + val['mmCnt']);

		var val = {};

		val['mgAmt'] = 0;
		val['mgIns'] = 0;
		val['mgRetire'] = 0;
		val['mmAmt'] = 0;
		val['mmIns'] = 0;
		val['mmRetire'] = 0;

		for(var i=1; i<=12; i++){
			val['mgAmt'] += __str2num($('#ID_CELL_VAL_'+orgNo+'_'+i+'_MG').attr('amt'));
			val['mgIns'] += __str2num($('#ID_CELL_VAL_'+orgNo+'_'+i+'_MG').attr('ins'));
			val['mgRetire'] += __str2num($('#ID_CELL_VAL_'+orgNo+'_'+i+'_MG').attr('retire'));
			val['mmAmt'] += __str2num($('#ID_CELL_VAL_'+orgNo+'_'+i+'_MM').attr('amt'));
			val['mmIns'] += __str2num($('#ID_CELL_VAL_'+orgNo+'_'+i+'_MM').attr('ins'));
			val['mmRetire'] += __str2num($('#ID_CELL_VAL_'+orgNo+'_'+i+'_MM').attr('retire'));
		}

		$('#ID_CELL_SUM_'+orgNo+'_MG').text(__num2str(val['mgAmt'] + val['mgIns'] + val['mgRetire']));
		$('#ID_CELL_SUM_'+orgNo+'_MM').text(__num2str(val['mmAmt'] + val['mmIns'] + val['mmRetire']));
		$('#ID_CELL_SUM_'+orgNo).text(__num2str(val['mmAmt'] + val['mmIns'] + val['mmRetire'] + val['mgAmt'] + val['mgIns'] + val['mgRetire']));
		/*
			$('#ID_CELL_SALARY_'+orgNo+'_MG').text(__num2str(val['mgAmt']));
			$('#ID_CELL_4INSU_'+orgNo+'_MG').text(__num2str(val['mgIns']));
			$('#ID_CELL_RETIRE_'+orgNo+'_MG').text(__num2str(val['mgRetire']));
			$('#ID_CELL_SUM_'+orgNo+'_MG').text(__num2str(val['mgAmt'] + val['mgIns'] + val['mgRetire']));

			$('#ID_CELL_SALARY_'+orgNo+'_MM').text(__num2str(val['mmAmt']));
			$('#ID_CELL_4INSU_'+orgNo+'_MM').text(__num2str(val['mmIns']));
			$('#ID_CELL_RETIRE_'+orgNo+'_MM').text(__num2str(val['mmRetire']));
			$('#ID_CELL_SUM_'+orgNo+'_MM').text(__num2str(val['mmAmt'] + val['mmIns'] + val['mmRetire']));

			$('#ID_CELL_SALARY_'+orgNo).text(__num2str(__str2num(val['mgAmt'])+__str2num(val['mmAmt'])));
			$('#ID_CELL_4INSU_'+orgNo).text(__num2str(__str2num(val['mgIns'])+__str2num(val['mmIns'])));
			$('#ID_CELL_RETIRE_'+orgNo).text(__num2str(__str2num(val['mgRetire'])+__str2num(val['mmRetire'])));
			$('#ID_CELL_SUM_'+orgNo).text(__num2str(val['mmAmt'] + val['mmIns'] + val['mmRetire'] + val['mgAmt'] + val['mgIns'] + val['mgRetire']));
		*/

		$('div[id^="ID_CELL_VAL_'+orgNo+'_'+month+'_"][selYn="Y"]').attr('selYn','N').mouseout();
	}

	function lfInDetail(orgNo){
		var objModal = new Object();
		var url = './bm_admin_in_detail.php';
		var style = 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.orgNo	= orgNo;
		objModal.year	= $('#lblYYMM').text();

		window.showModalDialog(url, objModal, style);
	}

	function lfOutDetail(orgNo){
		var objModal = new Object();
		var url = './bm_admin_out_detail.php';
		var style = 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.orgNo	= orgNo;
		objModal.year	= $('#lblYYMM').text();

		window.showModalDialog(url, objModal, style);
	}

	function lfDataCopy(month){
		if (!confirm('전월 데이타를 적용하시면 현재데이타는 삭제됩니다.\n전월 데이타를 적용하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./bm_admin_datacopy.php'
		,	data :{
				'year':$('#lblYYMM').text()
			,	'month':month
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					lfSearch();
				}else if (result == 5){
					alert('전월의 데이타가 존재하지 않습니다. 확인하여 주십시오.');
				}else if (result == 7){
					alert('마감된 년월입니다. 확인하여 주십시오.');
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">정직임금.기타수입지출관리</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="84px">
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
			<td class="right last">
				<span style="font-weight:bold;">※월 인원수의 "월(마우스 오버시 노란색으로 표시)"을 클릭하여 입력창을 활성화할 수 있습니다.</span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="30px">
		<col width="*">
		<col width="40px">
		<col width="30px" span="12">
		<col width="70px" span="5">
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">기관명</th>
			<th class="head" rowspan="2">구분</th>
			<th class="head" colspan="12">월 인원수(<span style="color:BLUE;">아래 월을 클릭하면 전월데이타를 복사할 수 있습니다.</span>)</th>
			<th class="head" rowspan="2">최근입력<br>급여</th>
			<th class="head" rowspan="2">최근입력<br>4대보험<br>(회사분)</th>
			<th class="head" rowspan="2">최근입력<br>퇴직충당금</th>
			<th class="head" rowspan="2">최근입력계</th>
			<th class="head last" rowspan="2">월전체<br>합계</th>
			<!--
			<th class="head" rowspan="2">급여</th>
			<th class="head" rowspan="2">4보험<br>(회사분)</th>
			-->
			<!--
				<th class="head" rowspan="2">수입</th>
				<th class="head last" rowspan="2">지출</th>
			-->
			<!--
			<th class="head" rowspan="2">퇴직<br>충당금</th>
			<th class="head last" rowspan="2">합계</th>
			-->
		</tr>
		<tr><?
			for($i=1; $i<=12; $i++){?>
				<th class="head"><a href="#" onclick="lfDataCopy('<?=$i;?>'); return false;"><?=$i;?>월</a></th><?
			}?>
		</tr>
	</thead>
	<tbody id="ID_SUM"></tbody>
	<tbody id="ID_LIST"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_db_close.php");
?>