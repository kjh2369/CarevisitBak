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
		,	url  :'./bm_disps_search2.php'
		,	data :{
				'year':$('#lblYYMM').text()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				html = html.split('<!--CUT_LINE-->');
				$('#ID_LIST_SUM').html(html[1]);
				$('#tbodyList').html(html[0]);
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
		var url = './bm_disps_set.php';
		var style = 'dialogWidth:400px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.orgNo	= $(obj).attr('orgNo');
		objModal.year	= $('#lblYYMM').text();
		objModal.month	= ($(obj).attr('month') < 10 ? '0' : '')+$(obj).attr('month');

		window.showModalDialog(url, objModal, style);
	}

	function lfSetSub(orgNo, month, val){
		var v = {};

		v['CNT1'] = __str2num($('#ID_CELL_TOT_VAL_'+month+'_1').text()) - __str2num($('#ID_CELL_VAL_'+orgNo+'_'+month+'_1').text()) + val['allotCnt'];
		v['CNT2'] = __str2num($('#ID_CELL_TOT_VAL_'+month+'_2').text()) - __str2num($('#ID_CELL_VAL_'+orgNo+'_'+month+'_2').text()) + val['employCnt'];
		v['AMT1'] = __str2num($('#ID_CELL_TOT_VAL_'+month+'_1').attr('amt')) - __str2num($('#ID_CELL_VAL_'+orgNo+'_'+month+'_1').attr('amt')) + val['allotAmt'];
		v['AMT2'] = __str2num($('#ID_CELL_TOT_VAL_'+month+'_2').attr('amt')) - __str2num($('#ID_CELL_VAL_'+orgNo+'_'+month+'_2').attr('amt')) + val['deductAmt'];

		$('#ID_CELL_TOT_VAL_'+month+'_1').attr('amt', v['AMT1']).text(v['CNT1']);
		$('#ID_CELL_TOT_VAL_'+month+'_2').attr('amt', v['AMT2']).text(v['CNT2']);

		v['AMT1'] = 0;
		v['AMT2'] = 0;

		for(var i=1; i<=12; i++){
			v['AMT1'] += __str2num($('#ID_CELL_TOT_VAL_'+i+'_1').attr('amt'));
			v['AMT2'] += __str2num($('#ID_CELL_TOT_VAL_'+i+'_2').attr('amt'));
		}

		$('#ID_CELL_TOT_AMT_1').text(__num2str(v['AMT1']));
		$('#ID_CELL_TOT_AMT_2').text(__num2str(v['AMT2']));


		$('#ID_CELL_VAL_'+orgNo+'_'+month+'_1').attr('amt',val['allotAmt']).text(val['allotCnt']);
		$('#ID_CELL_VAL_'+orgNo+'_'+month+'_2').attr('amt',val['deductAmt']).text(val['employCnt']);

		var val = {};

		val['allotAmt']	= 0;
		val['deductAmt']= 0;

		for(var i=1; i<=12; i++){
			val['allotAmt'] += __str2num($('#ID_CELL_VAL_'+orgNo+'_'+i+'_1').attr('amt'));
			val['deductAmt']+= __str2num($('#ID_CELL_VAL_'+orgNo+'_'+i+'_2').attr('amt'));
		}

		$('#ID_CELL_AMT_'+orgNo+'_1').text(__num2str(val['allotAmt']));
		$('#ID_CELL_AMT_'+orgNo+'_2').text(__num2str(val['deductAmt']));

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
		,	url  :'./bm_disps_set_datacopy.php'
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
<div class="title title_border">장애인채용관리</div>
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
		<col width="40px">
		<col width="100px">
		<col width="60px">
		<col width="30px" span="12">
		<col width="80px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">기관명</th>
			<th class="head" rowspan="2">구분</th>
			<th class="head" colspan="12">월 인원수(<span style="color:BLUE;">아래 월을 클릭하면 전월데이타를 복사할 수 있습니다.</span>)</th>
			<th class="head" rowspan="2">최근분담금액<br>최근공제금액</th>
			<th class="head" rowspan="2">월 분담합계<br>월 공제합계</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr><?
			for($i=1; $i<=12; $i++){?>
				<th class="head"><a href="#" onclick="lfDataCopy('<?=$i;?>'); return false;"><?=$i;?>월</a></th><?
			}?>
		</tr>
	</thead>
	<tbody id="ID_LIST_SUM"></tbody>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_db_close.php");
?>