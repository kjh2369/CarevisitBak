<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	사정기록_도구적 일상생활 동작정도(IADL)
	 *********************************************************/

	//사정기록
	/*
	$sql = 'SELECT	ispt_seq
			FROM	hce_inspection
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$isptSeq = $conn->get_data($sql);
	*/
	$isptSeq = '1';

	if ($_POST['hcptSeq']) $tmpHcptSeq = $_POST['hcptSeq'];
	if (!$tmpHcptSeq) $tmpHcptSeq = $hce->rcpt;

	//일상생활 동작정도
	$sql = 'SELECT	phone,outdoor,buying,eating,homework,cleaning,medicine,money,repair
			FROM	hce_inspection_iadl
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$tmpHcptSeq.'\'
			AND		ispt_seq= \''.$isptSeq.'\'';

	$row = $conn->get_array($sql);

	$idx = 0;

	for($i=1; $i<=9; $i++){
		$val = false;

		for($j=1; $j<=4; $j++){
			$id = $i.'_'.$j;

			if ($row[$idx] == $j){
				$col[$id] = 'V';
				$val = true;
			}else{
				$col[$id] = '';
			}
		}

		$idx ++;
	}

	Unset($row);
?>
<style>
	.divVal{
		font-weight:bold;
		color:blue;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$('.divVal').unbind('click').bind('click',function(){
			var id = $(this).attr('id').split('_');

			$('div[id^="'+id[0]+'_"]').text('');
			$('#'+id[0]+'_'+id[1]).text('V');
		});
	});

	//마우스 이벤트
	function lfMouseEvt(obj,evt){
		var cnt = $('td',obj).length;

		if (evt == 'OVER'){
			$('td',obj).eq(cnt-1).css('background-color','#efefef');
			$('td',obj).eq(cnt-2).css('background-color','#efefef');
			$('td',obj).eq(cnt-3).css('background-color','#efefef');
			$('td',obj).eq(cnt-4).css('background-color','#efefef');
		}else{
			$('td',obj).eq(cnt-1).css('background-color','#ffffff');
			$('td',obj).eq(cnt-2).css('background-color','#ffffff');
			$('td',obj).eq(cnt-3).css('background-color','#ffffff');
			$('td',obj).eq(cnt-4).css('background-color','#ffffff');
		}
	}

	//저장
	function lfSaveSub(){
		var data = {};

		$('.divVal').each(function(){
			if ($(this).text() == 'V'){
				var id = $(this).attr('id').split('_');

				data[id[0]] = id[1];
			}
		});

		$('input:hidden').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

		$.ajax({
			type:'POST'
		,	url:'./hce_apply.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					top.frames['frmTop'].lfTarget();
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
		<col width="135px" span="4">
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="20">- 도구적 일상생활 동작(IADL)</th>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head">항목</th>
			<th class="head">자립가능</th>
			<th class="head">약간불편</th>
			<th class="head">도와주면 가능</th>
			<th class="head last">완전도움필요</th>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">전화사용</th>
			<td class="center"><div id="1_1" class="divVal"><?=$col['1_1'];?></div></td>
			<td class="center"><div id="1_2" class="divVal"><?=$col['1_2'];?></div></td>
			<td class="center"><div id="1_3" class="divVal"><?=$col['1_3'];?></div></td>
			<td class="center last"><div id="1_4" class="divVal"><?=$col['1_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">외출 또는 여행</th>
			<td class="center"><div id="2_1" class="divVal"><?=$col['2_1'];?></div></td>
			<td class="center"><div id="2_2" class="divVal"><?=$col['2_2'];?></div></td>
			<td class="center"><div id="2_3" class="divVal"><?=$col['2_3'];?></div></td>
			<td class="center last"><div id="2_4" class="divVal"><?=$col['2_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">물건구입</th>
			<td class="center"><div id="3_1" class="divVal"><?=$col['3_1'];?></div></td>
			<td class="center"><div id="3_2" class="divVal"><?=$col['3_2'];?></div></td>
			<td class="center"><div id="3_3" class="divVal"><?=$col['3_3'];?></div></td>
			<td class="center last"><div id="3_4" class="divVal"><?=$col['3_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">식사준비</th>
			<td class="center"><div id="4_1" class="divVal"><?=$col['4_1'];?></div></td>
			<td class="center"><div id="4_2" class="divVal"><?=$col['4_2'];?></div></td>
			<td class="center"><div id="4_3" class="divVal"><?=$col['4_3'];?></div></td>
			<td class="center last"><div id="4_4" class="divVal"><?=$col['4_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">집안일(청소나 정리정돈)</th>
			<td class="center"><div id="5_1" class="divVal"><?=$col['5_1'];?></div></td>
			<td class="center"><div id="5_2" class="divVal"><?=$col['5_2'];?></div></td>
			<td class="center"><div id="5_3" class="divVal"><?=$col['5_3'];?></div></td>
			<td class="center last"><div id="5_4" class="divVal"><?=$col['5_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">빨래</th>
			<td class="center"><div id="6_1" class="divVal"><?=$col['6_1'];?></div></td>
			<td class="center"><div id="6_2" class="divVal"><?=$col['6_2'];?></div></td>
			<td class="center"><div id="6_3" class="divVal"><?=$col['6_3'];?></div></td>
			<td class="center last"><div id="6_4" class="divVal"><?=$col['6_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">제시간에 정확한 용량의 약 복용</th>
			<td class="center"><div id="7_1" class="divVal"><?=$col['7_1'];?></div></td>
			<td class="center"><div id="7_2" class="divVal"><?=$col['7_2'];?></div></td>
			<td class="center"><div id="7_3" class="divVal"><?=$col['7_3'];?></div></td>
			<td class="center last"><div id="7_4" class="divVal"><?=$col['7_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">금전관리</th>
			<td class="center"><div id="8_1" class="divVal"><?=$col['8_1'];?></div></td>
			<td class="center"><div id="8_2" class="divVal"><?=$col['8_2'];?></div></td>
			<td class="center"><div id="8_3" class="divVal"><?=$col['8_3'];?></div></td>
			<td class="center last"><div id="8_4" class="divVal"><?=$col['8_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">집 수공일(바느질이나 못질)</th>
			<td class="center"><div id="9_1" class="divVal"><?=$col['9_1'];?></div></td>
			<td class="center"><div id="9_2" class="divVal"><?=$col['9_2'];?></div></td>
			<td class="center"><div id="9_3" class="divVal"><?=$col['9_3'];?></div></td>
			<td class="center last"><div id="9_4" class="divVal"><?=$col['9_4'];?></div></td>
		</tr>
	</tbody>
</table>
<input id="bodyIdx" type="hidden" value="4">
<input id="isptSeq" type="hidden" value="<?=$isptSeq;?>">
<?
	include_once('../inc/_db_close.php');
?>