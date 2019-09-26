<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	사정기록_일상생활 동작정도(ADL)
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
	$sql = 'SELECT	base_door,base_shoes,base_shoes_put,base_chair
			,		per_bath,per_wash,per_groom,per_in_dress,per_out_dress
			,		wc_bedpan,wc_after,wc_feces,wc_urine
			,		eat_spoon,eat_stick,eat_poke,eat_cup,eat_grip_cup
			,		walk_100m,walk_hand,walk_stair
			,		bed_sitdown,bed_standup,bed_lie,bed_turn,bed_tidy
			FROM	hce_inspection_adl
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$tmpHcptSeq.'\'
			AND		ispt_seq= \''.$isptSeq.'\'';

	$row = $conn->get_array($sql);

	$idx = 0;

	for($i=1; $i<=26; $i++){
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
		<col width="80px">
		<col width="160px">
		<col width="135px" span="3">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="20">- 일상생활 동작정도(ADL)</th>
		</tr>
		<tr style="cursor:default;">
			<th class="head" colspan="2">일상생활 동작</th>
			<th class="head">자립가능</th>
			<th class="head">약간불편</th>
			<th class="head">도와주면 가능</th>
			<th class="head last">완전도움필요</th>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<td class="center" rowspan="4">기본동작</td>
			<th class="">문 열고 닫기</th>
			<td class="center"><div id="1_1" class="divVal"><?=$col['1_1'];?></div></td>
			<td class="center"><div id="1_2" class="divVal"><?=$col['1_2'];?></div></td>
			<td class="center"><div id="1_3" class="divVal"><?=$col['1_3'];?></div></td>
			<td class="center last"><div id="1_4" class="divVal"><?=$col['1_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">혼자서 신발 벗기</th>
			<td class="center"><div id="2_1" class="divVal"><?=$col['2_1'];?></div></td>
			<td class="center"><div id="2_2" class="divVal"><?=$col['2_2'];?></div></td>
			<td class="center"><div id="2_3" class="divVal"><?=$col['2_3'];?></div></td>
			<td class="center last"><div id="2_4" class="divVal"><?=$col['2_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">신발을 신장에 넣기</th>
			<td class="center"><div id="3_1" class="divVal"><?=$col['3_1'];?></div></td>
			<td class="center"><div id="3_2" class="divVal"><?=$col['3_2'];?></div></td>
			<td class="center"><div id="3_3" class="divVal"><?=$col['3_3'];?></div></td>
			<td class="center last"><div id="3_4" class="divVal"><?=$col['3_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">의자를 책상에 넣고 빼기</th>
			<td class="center"><div id="4_1" class="divVal"><?=$col['4_1'];?></div></td>
			<td class="center"><div id="4_2" class="divVal"><?=$col['4_2'];?></div></td>
			<td class="center"><div id="4_3" class="divVal"><?=$col['4_3'];?></div></td>
			<td class="center last"><div id="4_4" class="divVal"><?=$col['4_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<td class="center" rowspan="5">신변처리</td>
			<th class="">욕조에 들어가 목욕하기</th>
			<td class="center"><div id="5_1" class="divVal"><?=$col['5_1'];?></div></td>
			<td class="center"><div id="5_2" class="divVal"><?=$col['5_2'];?></div></td>
			<td class="center"><div id="5_3" class="divVal"><?=$col['5_3'];?></div></td>
			<td class="center last"><div id="5_4" class="divVal"><?=$col['5_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">세수하고 양치질하기</th>
			<td class="center"><div id="6_1" class="divVal"><?=$col['6_1'];?></div></td>
			<td class="center"><div id="6_2" class="divVal"><?=$col['6_2'];?></div></td>
			<td class="center"><div id="6_3" class="divVal"><?=$col['6_3'];?></div></td>
			<td class="center last"><div id="6_4" class="divVal"><?=$col['6_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">기본적인 몸단장 하기</th>
			<td class="center"><div id="7_1" class="divVal"><?=$col['7_1'];?></div></td>
			<td class="center"><div id="7_2" class="divVal"><?=$col['7_2'];?></div></td>
			<td class="center"><div id="7_3" class="divVal"><?=$col['7_3'];?></div></td>
			<td class="center last"><div id="7_4" class="divVal"><?=$col['7_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">혼자서 옷 입기</th>
			<td class="center"><div id="8_1" class="divVal"><?=$col['8_1'];?></div></td>
			<td class="center"><div id="8_2" class="divVal"><?=$col['8_2'];?></div></td>
			<td class="center"><div id="8_3" class="divVal"><?=$col['8_3'];?></div></td>
			<td class="center last"><div id="8_4" class="divVal"><?=$col['8_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">혼자서 옷 벗기</th>
			<td class="center"><div id="9_1" class="divVal"><?=$col['9_1'];?></div></td>
			<td class="center"><div id="9_2" class="divVal"><?=$col['9_2'];?></div></td>
			<td class="center"><div id="9_3" class="divVal"><?=$col['9_3'];?></div></td>
			<td class="center last"><div id="9_4" class="divVal"><?=$col['9_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<td class="center" rowspan="4">용변처리</td>
			<th class="">혼자서 변기에 앉기</th>
			<td class="center"><div id="10_1" class="divVal"><?=$col['10_1'];?></div></td>
			<td class="center"><div id="10_2" class="divVal"><?=$col['10_2'];?></div></td>
			<td class="center"><div id="10_3" class="divVal"><?=$col['10_3'];?></div></td>
			<td class="center last"><div id="10_4" class="divVal"><?=$col['10_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">용변 후 뒷처리 하기</th>
			<td class="center"><div id="11_1" class="divVal"><?=$col['11_1'];?></div></td>
			<td class="center"><div id="11_2" class="divVal"><?=$col['11_2'];?></div></td>
			<td class="center"><div id="11_3" class="divVal"><?=$col['11_3'];?></div></td>
			<td class="center last"><div id="11_4" class="divVal"><?=$col['11_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">대변 조절하기</th>
			<td class="center"><div id="12_1" class="divVal"><?=$col['12_1'];?></div></td>
			<td class="center"><div id="12_2" class="divVal"><?=$col['12_2'];?></div></td>
			<td class="center"><div id="12_3" class="divVal"><?=$col['12_3'];?></div></td>
			<td class="center last"><div id="12_4" class="divVal"><?=$col['12_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">소변 조절하기</th>
			<td class="center"><div id="13_1" class="divVal"><?=$col['13_1'];?></div></td>
			<td class="center"><div id="13_2" class="divVal"><?=$col['13_2'];?></div></td>
			<td class="center"><div id="13_3" class="divVal"><?=$col['13_3'];?></div></td>
			<td class="center last"><div id="13_4" class="divVal"><?=$col['13_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<td class="center" rowspan="5">식사</td>
			<th class="">수저로 먹기</th>
			<td class="center"><div id="14_1" class="divVal"><?=$col['14_1'];?></div></td>
			<td class="center"><div id="14_2" class="divVal"><?=$col['14_2'];?></div></td>
			<td class="center"><div id="14_3" class="divVal"><?=$col['14_3'];?></div></td>
			<td class="center last"><div id="14_4" class="divVal"><?=$col['14_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">젓가락 사용하기</th>
			<td class="center"><div id="15_1" class="divVal"><?=$col['15_1'];?></div></td>
			<td class="center"><div id="15_2" class="divVal"><?=$col['15_2'];?></div></td>
			<td class="center"><div id="15_3" class="divVal"><?=$col['15_3'];?></div></td>
			<td class="center last"><div id="15_4" class="divVal"><?=$col['15_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">포크 사용하기</th>
			<td class="center"><div id="16_1" class="divVal"><?=$col['16_1'];?></div></td>
			<td class="center"><div id="16_2" class="divVal"><?=$col['16_2'];?></div></td>
			<td class="center"><div id="16_3" class="divVal"><?=$col['16_3'];?></div></td>
			<td class="center last"><div id="16_4" class="divVal"><?=$col['16_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">컵으로 물 마시기</th>
			<td class="center"><div id="17_1" class="divVal"><?=$col['17_1'];?></div></td>
			<td class="center"><div id="17_2" class="divVal"><?=$col['17_2'];?></div></td>
			<td class="center"><div id="17_3" class="divVal"><?=$col['17_3'];?></div></td>
			<td class="center last"><div id="17_4" class="divVal"><?=$col['17_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">손잡이 있는 컵 사용하기</th>
			<td class="center"><div id="18_1" class="divVal"><?=$col['18_1'];?></div></td>
			<td class="center"><div id="18_2" class="divVal"><?=$col['18_2'];?></div></td>
			<td class="center"><div id="18_3" class="divVal"><?=$col['18_3'];?></div></td>
			<td class="center last"><div id="18_4" class="divVal"><?=$col['18_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<td class="center" rowspan="3">보행</td>
			<th class="">혼자서 100m이상 걷기</th>
			<td class="center"><div id="19_1" class="divVal"><?=$col['19_1'];?></div></td>
			<td class="center"><div id="19_2" class="divVal"><?=$col['19_2'];?></div></td>
			<td class="center"><div id="19_3" class="divVal"><?=$col['19_3'];?></div></td>
			<td class="center last"><div id="19_4" class="divVal"><?=$col['19_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">난간잡고 계단 오르내리기</th>
			<td class="center"><div id="20_1" class="divVal"><?=$col['20_1'];?></div></td>
			<td class="center"><div id="20_2" class="divVal"><?=$col['20_2'];?></div></td>
			<td class="center"><div id="20_3" class="divVal"><?=$col['20_3'];?></div></td>
			<td class="center last"><div id="20_4" class="divVal"><?=$col['20_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">난간없이 계단 오르내리기</th>
			<td class="center"><div id="21_1" class="divVal"><?=$col['21_1'];?></div></td>
			<td class="center"><div id="21_2" class="divVal"><?=$col['21_2'];?></div></td>
			<td class="center"><div id="21_3" class="divVal"><?=$col['21_3'];?></div></td>
			<td class="center last"><div id="21_4" class="divVal"><?=$col['21_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<td class="center bottom" rowspan="5">침상활동</td>
			<th class="">선 상태에서 앉기</th>
			<td class="center"><div id="22_1" class="divVal"><?=$col['22_1'];?></div></td>
			<td class="center"><div id="22_2" class="divVal"><?=$col['22_2'];?></div></td>
			<td class="center"><div id="22_3" class="divVal"><?=$col['22_3'];?></div></td>
			<td class="center last"><div id="22_4" class="divVal"><?=$col['22_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">앉은 상태에서 일어나기</th>
			<td class="center"><div id="23_1" class="divVal"><?=$col['23_1'];?></div></td>
			<td class="center"><div id="23_2" class="divVal"><?=$col['23_2'];?></div></td>
			<td class="center"><div id="23_3" class="divVal"><?=$col['23_3'];?></div></td>
			<td class="center last"><div id="23_4" class="divVal"><?=$col['23_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">혼자서 눕고 일어나기</th>
			<td class="center"><div id="24_1" class="divVal"><?=$col['24_1'];?></div></td>
			<td class="center"><div id="24_2" class="divVal"><?=$col['24_2'];?></div></td>
			<td class="center"><div id="24_3" class="divVal"><?=$col['24_3'];?></div></td>
			<td class="center last"><div id="24_4" class="divVal"><?=$col['24_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="">혼자서 뒤척이기</th>
			<td class="center"><div id="25_1" class="divVal"><?=$col['25_1'];?></div></td>
			<td class="center"><div id="25_2" class="divVal"><?=$col['25_2'];?></div></td>
			<td class="center"><div id="25_3" class="divVal"><?=$col['25_3'];?></div></td>
			<td class="center last"><div id="25_4" class="divVal"><?=$col['25_4'];?></div></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class=" bottom">기상 후 침구류 정리하기</th>
			<td class="center bottom"><div id="26_1" class="divVal"><?=$col['26_1'];?></div></td>
			<td class="center bottom"><div id="26_2" class="divVal"><?=$col['26_2'];?></div></td>
			<td class="center bottom"><div id="26_3" class="divVal"><?=$col['26_3'];?></div></td>
			<td class="center bottom last"><div id="26_4" class="divVal"><?=$col['26_3'];?></div></td>
		</tr>
	</tbody>
</table>
<input id="bodyIdx" type="hidden" value="3">
<input id="isptSeq" type="hidden" value="<?=$isptSeq;?>">
<?
	include_once('../inc/_db_close.php');
?>