<?
	include_once('../inc/_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code	= $_SESSION['userCenterCode'];
	$year	= Date('Y');
	$month	= Date('m');

	//서비스 리스트
	$svcList = $conn->svcKindSort($code, $gHostSvc['voucher']);
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfSearch()',100);
		__init_form(document.f);
	});

	function lfMoveYear(pos){
		var year = __str2num($('#lblYYMM').attr('year'));

		year += pos;

		$('#lblYYMM').attr('year',year).text(year);
	}

	function lfMoveMonth(month){
		setTimeout('lfSearch()',100);
		$('div[id^="btnMonth_"]').removeClass('my_month_y').addClass('my_month_1');
		$('#btnMonth_'+month).removeClass('my_month_1').addClass('my_month_y');
		$('#lblYYMM').attr('month',(month < 10 ? '0' : '')+month);
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./iljung_voucher_notice_search.php'
		,	data :{
				'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				$('input:text[id^="txtNotice_"]').val('');

				var row = data.split(String.fromCharCode(11));

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);

						$('#txtNotice_'+col['svcCd']).val(col['notice']);
					}
				}
			}
		,	complete:function(){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSave(svcCd){
		$.ajax({
			type :'POST'
		,	url  :'./iljung_voucher_notice_save.php'
		,	data :{
				'svcCd'	:svcCd
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			,	'notice':$('#txtNotice_'+svcCd).val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complete:function(){
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">일정표 공지등록(바우처)</div>
<form name="f" method="post">
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
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last"><?=$myF->_btn_month($month,'lfMoveMonth(');?></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="150px">
		<col width="550px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="center">No</th>
			<th class="center">서비스</th>
			<th class="center">공지내용</th>
			<th class="center last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center">1</td>
			<td class="left">요양보호사</td>
			<td class="center"><input id="txtNotice_M" name="txt" type="text" value="" style="width:100%;"></td>
			<td class="left last">
				<span class="btn_pack small"><button type="button" onclick="lfSave('M');">등록</button></span>
				<span class="btn_pack small"><button type="button" onclick="$('#txtNotice_M').val(''); lfSave('M');">삭제</button></span>
			</td>
		</tr><?
		if (is_array($svcList['V'])){
			$no = 2;
			foreach($svcList['V'] as $svc){?>
				<tr>
					<td class="center"><?=$no;?></td>
					<td class="left"><?=$svc['name'];?></td>
					<td class="center"><input id="txtNotice_<?=$svc['code'];?>" name="txt" type="text" value="" style="width:100%;"></td>
					<td class="left last">
						<span class="btn_pack small"><button type="button" onclick="lfSave('<?=$svc['code'];?>');">등록</button></span>
						<span class="btn_pack small"><button type="button" onclick="$('#txtNotice_<?=$svc['code'];?>').val(''); lfSave('<?=$svc['code'];?>');">삭제</button></span>
					</td>
				</tr><?
				$no ++;
			}
		}else{?>
			<tr>
				<td class="center last" colspan="4">::등록 가능한 서비스가 없습니다.::</td>
			</tr><?
		}?>
	</tbody>
	<tfoot>
		<tr>
			<td class="left bottom last" colspan="4">
				※바우처별 등록된 공지내용은 일정표 하단 기관면 아래에 출력됩니다.
			</td>
		</tr>
	</tfoot>
</table>
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>