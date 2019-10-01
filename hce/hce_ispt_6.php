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

	$sql = 'SELECT	social1
			,		social2,social2_rsn
			,		social3
			,		social4,social4_rsn
			,		social5
			,		social6,social6_rsn
			,		social7,social7_nm,social7_tel
			,		social8,social8_other
			,		social9,social9_other
			FROM	hce_inspection_social
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$tmpHcptSeq.'\'
			AND		ispt_seq= \''.$isptSeq.'\'';

	$row = $conn->get_array($sql);

	$social1	= $row['social1'];
	$social2	= $row['social2'];
	$social2Rsn	= StripSlashes($row['social2_rsn']);
	$social3	= $row['social3'];
	$social4	= $row['social4'];
	$social4Rsn	= StripSlashes($row['social4_rsn']);
	$social5	= $row['social5'];
	$social6	= $row['social6'];
	$social6Rsn	= StripSlashes($row['social6_rsn']);
	$social7	= $row['social7'];
	$social7Nm	= StripSlashes($row['social7_nm']);
	$social7Tel	= $myF->phoneStyle($row['social7_tel']);
	$social8	= $row['social8'];
	$social8Str	= StripSlashes($row['social8_other']);
	$social9	= $row['social9'];
	$social9Str	= StripSlashes($row['social9_other']);

	Unset($row);
?>
<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);

		$('input:radio[name="opt2"]:checked').click();
		$('input:radio[name="opt4"]:checked').click();
		$('input:radio[name="opt6"]:checked').click();
		$('input:radio[name="opt7"]:checked').click();
		$('input:radio[name="opt8"]:checked').click();
		$('input:radio[name="opt9"]:checked').click();
	});

	//마우스 이벤트
	function lfMouseEvt(obj,evt){
		var cnt = $('td',obj).length;

		if (evt == 'OVER'){
			$('td',obj).eq(cnt-1).css('background-color','#efefef');
			$('td',obj).eq(cnt-2).css('background-color','#efefef');
			$('td',obj).eq(cnt-3).css('background-color','#efefef');
		}else{
			$('td',obj).eq(cnt-1).css('background-color','#ffffff');
			$('td',obj).eq(cnt-2).css('background-color','#ffffff');
			$('td',obj).eq(cnt-3).css('background-color','#ffffff');
		}
	}

	function lfDisabled(obj,enabled){
		if (enabled){
			$(obj).css('background-color','#ffffff').attr('disabled',false).focus();
		}else{
			$(obj).css('background-color','#efefef').attr('disabled',true);
		}
	}

	//저장
	function lfSaveSub(){
		var data = {};

		$('input:text').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

		$('input:hidden').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

		$('input:radio').each(function(){
			var name= $(this).attr('name');
			var val	= $('input:radio[name="'+name+'"]:checked').val();

			if (!val) val = '';

			data[name] = val;
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
		<col width="50px" span="2">
		<col width="600px">
		<col width="70px" span="2">
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="5">- 사회적 측면</th>
		</tr>
		<tr>
			<th class="head">No</th>
			<th class="head" colspan="2">질문</th>
			<th class="head">예</th>
			<th class="head last">아니오</th>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head">1</th>
			<td class="left" colspan="2">
				<div>최근 1년 사이에 귀하는 출가 또는 분가한 자녀들과 교류는 어떻습니까?</div>
				<div style="float:left; width:45%;"><label><input id="opt1_1" name="opt1" type="radio" class="radio" value="1" <?=($social1 == '1' ? 'checked' : '');?>>거의 연락없이 지낸다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt1_2" name="opt1" type="radio" class="radio" value="2" <?=($social1 == '2' ? 'checked' : '');?>>명절, 생일 등에 가끔씩 만나거나 연락한다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt1_3" name="opt1" type="radio" class="radio" value="3" <?=($social1 == '3' ? 'checked' : '');?>>2달~3달에 한 두 번 정도 연락한다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt1_4" name="opt1" type="radio" class="radio" value="4" <?=($social1 == '4' ? 'checked' : '');?>>1달에 한 두 번 정도 연락한다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt1_5" name="opt1" type="radio" class="radio" value="5" <?=($social1 == '5' ? 'checked' : '');?>>1주 일회 이상 연락한다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt1_6" name="opt1" type="radio" class="radio" value="6" <?=($social1 == '6' ? 'checked' : '');?>>출가한 자녀가 없다.</label></div>
			</td>
			<td class="last" colspan="2"></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head" rowspan="2">2</th>
			<td class="left" colspan="2">
				가족과의 관계에 문제가 있습니까?(예라면 이유를 작성하여 주십시오.)
			</td>
			<td class="center" rowspan="2"><input id="opt2Y" name="opt2" type="radio" value="Y" style="width:60px; border:none;" otherVal="Y" otherObj="txt2Rsn" <?=($social2 == 'Y' ? 'checked' : '');?>></td>
			<td class="center last" rowspan="2"><input id="opt2N" name="opt2" type="radio" value="N" style="width:60px; border:none;" otherVal="Y" otherObj="txt2Rsn" <?=($social2 == 'N' ? 'checked' : '');?>></td>
		</tr>
		<tr>
			<th class="head">이유</th>
			<td class="center"><input id="txt2Rsn" name="txt" type="text" value="<?=$social2Rsn;?>" style="width:100%; background-color:#efefef;" disabled="true"></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head">3</th>
			<td class="left" colspan="2">
				<div>귀하는 대체로 이웃과 어느 정도 친하게 지내고 계십니까?</div>
				<div style="float:left; width:45%;"><label><input id="opt3_1" name="opt3" type="radio" class="radio" value="1" <?=($social3 == '1' ? 'checked' : '');?>>거의 모르고 지낸다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt3_2" name="opt3" type="radio" class="radio" value="2" <?=($social3 == '2' ? 'checked' : '');?>>인사하는 정도이다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt3_3" name="opt3" type="radio" class="radio" value="3" <?=($social3 == '3' ? 'checked' : '');?>>말벗하는 정도이다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt3_4" name="opt3" type="radio" class="radio" value="4" <?=($social3 == '4' ? 'checked' : '');?>>말벗과 여러 도움을 주고받는다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt3_5" name="opt3" type="radio" class="radio" value="5" <?=($social3 == '5' ? 'checked' : '');?>>절대적으로 믿고 가족처럼 지낸다.</label></div>
			</td>
			<td class="last" colspan="2"></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head" rowspan="2">4</th>
			<td class="left" colspan="2">
				이웃과 어울리는데 어려움이 있으십니까?(예라면 이유를 작성하여 주십시오.)
			</td>
			<td class="center" rowspan="2"><input id="opt4Y" name="opt4" type="radio" value="Y" style="width:60px; border:none;" otherVal="Y" otherObj="txt4Rsn" <?=($social4 == 'Y' ? 'checked' : '');?>></td>
			<td class="center last" rowspan="2"><input id="opt4N" name="opt4" type="radio" value="N" style="width:60px; border:none;" otherVal="Y" otherObj="txt4Rsn" <?=($social4 == 'N' ? 'checked' : '');?>></td>
		</tr>
		<tr>
			<th class="head">이유</th>
			<td class="center"><input id="txt4Rsn" name="txt" type="text" value="<?=$social4Rsn;?>" style="width:100%; background-color:#efefef;" disabled="true"></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head">5</th>
			<td class="left" colspan="2">
				친구들이 있어서 정기적으로 만나십니까?
			</td>
			<td class="center"><input id="opt5Y" name="opt5" type="radio" value="Y" style="width:60px; border:none;" <?=($social5 == 'Y' ? 'checked' : '');?>></td>
			<td class="center last"><input id="opt5N" name="opt5" type="radio" value="N" style="width:60px; border:none;" <?=($social5 == 'N' ? 'checked' : '');?>></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head" rowspan="2">6</th>
			<td class="left" colspan="2">
				교회, 지역사회활동 그 밖의 사회활동에 참여하십니까?
			</td>
			<td class="center" rowspan="2"><input id="opt6Y" name="opt6" type="radio" value="Y" style="width:60px; border:none;" otherVal="Y" otherObj="txt6Rsn" <?=($social6 == 'Y' ? 'checked' : '');?>></td>
			<td class="center last" rowspan="2"><input id="opt6N" name="opt6" type="radio" value="N" style="width:60px; border:none;" otherVal="Y" otherObj="txt6Rsn" <?=($social6 == 'N' ? 'checked' : '');?>></td>
		</tr>
		<tr>
			<th class="head">이유</th>
			<td class="center"><input id="txt6Rsn" name="txt" type="text" value="<?=$social6Rsn;?>" style="width:100%; background-color:#efefef;" disabled="true"></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head" rowspan="2">7</th>
			<td class="left" colspan="2">
				<div>종교생활을 하신다면 어떻게 하십니까?</div>
				<div style="float:left; width:45%;"><label><input id="opt7_1" name="opt7" type="radio" class="radio" value="1" <?=($social7 == '1' ? 'checked' : '');?>>전혀 하지 않는다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt7_2" name="opt7" type="radio" class="radio" value="2" <?=($social7 == '2' ? 'checked' : '');?>>가끔씩 한다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt7_3" name="opt7" type="radio" class="radio" value="3" <?=($social7 == '3' ? 'checked' : '');?>>자주 하는 편이다(월1~2회)</label></div>
				<div style="float:left; width:45%;"><label><input id="opt7_4" name="opt7" type="radio" class="radio" value="4" <?=($social7 == '4' ? 'checked' : '');?>>항상 참여한다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt7_5" name="opt7" type="radio" class="radio" value="5" <?=($social7 == '5' ? 'checked' : '');?>>종교가 없다.</label></div>
			</td>
			<td class="last" colspan="2" rowspan="2"></td>
		</tr>
		<tr>
			<td class="" colspan="2">
				<table class="my_table" border="1">
					<colgroup>
						<col width="70px">
						<col width="150px">
						<col width="70px">
						<col width="50px">
					</colgroup>
					<tbody>
						<tr>
							<th class="center bottom">종교기관명</th>
							<td class="bottom"><input id="txt7Nm" name="txt" type="text" value="<?=$social7Nm;?>" style="width:100%; background-color:#efefef;" disabled="true"></td>
							<th class="center bottom">전화번호</th>
							<td class="bottom"><input id="txt7Tel" name="txt" type="text" value="<?=$social7Tel;?>" class="phone" style="background-color:#efefef;" disabled="true"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head">8</th>
			<td class="left" colspan="2">
				<div>좋아하는 소일거리(여가시간)는?</div>
				<div style="float:left; width:45%;"><label><input id="opt8_1" name="opt8" type="radio" class="radio" value="1" otherVal="8" otherObj="txt8Other" <?=($social8 == '1' ? 'checked' : '');?>>노인정, 양로원에서 시간을 보낸다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt8_2" name="opt8" type="radio" class="radio" value="2" otherVal="8" otherObj="txt8Other" <?=($social8 == '2' ? 'checked' : '');?>>놀이터, 공원, 산책로에서 보낸다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt8_3" name="opt8" type="radio" class="radio" value="3" otherVal="8" otherObj="txt8Other" <?=($social8 == '3' ? 'checked' : '');?>>집에서 TV를 보거나 그냥 지낸다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt8_4" name="opt8" type="radio" class="radio" value="4" otherVal="8" otherObj="txt8Other" <?=($social8 == '4' ? 'checked' : '');?>>친구의 집이나 내 집에서 친구와 지낸다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt8_5" name="opt8" type="radio" class="radio" value="5" otherVal="8" otherObj="txt8Other" <?=($social8 == '5' ? 'checked' : '');?>>노인대학에 가거나 취미활동을 한다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt8_6" name="opt8" type="radio" class="radio" value="6" otherVal="8" otherObj="txt8Other" <?=($social8 == '6' ? 'checked' : '');?>>취로사업이나 일, 부업을 한다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt8_7" name="opt8" type="radio" class="radio" value="7" otherVal="8" otherObj="txt8Other" <?=($social8 == '7' ? 'checked' : '');?>>종교활동이나 봉사활동을 한다.</label></div>
				<div style="float:left; width:45%;"><label><input id="opt8_8" name="opt8" type="radio" class="radio" value="8" otherVal="8" otherObj="txt8Other" <?=($social8 == '8' ? 'checked' : '');?>>기타(<input id="txt8Other" name="txt" type="text" value="<?=$social8Str;?>" style="width:100px; background-color:#efefef;" disabled="true">)</label></div>
			</td>
			<td class="last" colspan="2"></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head bottom" rowspan="2">9</th>
			<td class="left bottom" colspan="2">
				<div>귀하는 어려움이 생기거나 아플 때 누구로부터 도움을 가장 많이 받습니까?</div>
				<div style="float:left; width:45%;"><label><input id="opt9_1" name="opt9" type="radio" class="radio" value="1" otherVal="9" otherObj="txt9Other" <?=($social9 == '1' ? 'checked' : '');?>>배우자</label></div>
				<div style="float:left; width:45%;"><label><input id="opt9_2" name="opt9" type="radio" class="radio" value="2" otherVal="9" otherObj="txt9Other" <?=($social9 == '2' ? 'checked' : '');?>>자녀</label></div>
				<div style="float:left; width:45%;"><label><input id="opt9_3" name="opt9" type="radio" class="radio" value="3" otherVal="9" otherObj="txt9Other" <?=($social9 == '3' ? 'checked' : '');?>>형제자매, 부모</label></div>
				<div style="float:left; width:45%;"><label><input id="opt9_4" name="opt9" type="radio" class="radio" value="4" otherVal="9" otherObj="txt9Other" <?=($social9 == '4' ? 'checked' : '');?>>이웃</label></div>
				<div style="float:left; width:45%;"><label><input id="opt9_5" name="opt9" type="radio" class="radio" value="5" otherVal="9" otherObj="txt9Other" <?=($social9 == '5' ? 'checked' : '');?>>사회복지관</label></div>
				<div style="float:left; width:45%;"><label><input id="opt9_6" name="opt9" type="radio" class="radio" value="6" otherVal="9" otherObj="txt9Other" <?=($social9 == '6' ? 'checked' : '');?>>동사무소 전문요원</label></div>
				<div style="float:left; width:45%;"><label><input id="opt9_7" name="opt9" type="radio" class="radio" value="7" otherVal="9" otherObj="txt9Other" <?=($social9 == '7' ? 'checked' : '');?>>종교단체</label></div>
				<div style="float:left; width:45%;"><label><input id="opt9_8" name="opt9" type="radio" class="radio" value="8" otherVal="9" otherObj="txt9Other" <?=($social9 == '8' ? 'checked' : '');?>>없다</label></div>
				<div style="float:left; width:45%;"><label><input id="opt9_9" name="opt9" type="radio" class="radio" value="9" otherVal="9" otherObj="txt9Other" <?=($social9 == '9' ? 'checked' : '');?>>기타(<input id="txt9Other" name="txt" type="text" value="<?=$social9Str;?>" style="width:100px; background-color:#efefef;" disabled="true">)</label></div>
			</td>
			<td class="bottom last" colspan="2" rowspan="2"></td>
		</tr>
	</tbody>
</table>
<input id="bodyIdx" type="hidden" value="6">
<input id="isptSeq" type="hidden" value="<?=$isptSeq;?>">
<?
	include_once('../inc/_db_close.php');
?>