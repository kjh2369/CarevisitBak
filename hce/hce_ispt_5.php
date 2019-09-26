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
			AND		rcpt_seq= \''.$rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$isptSeq = $conn->get_data($sql);
	*/
	$isptSeq = '1';

	if ($_POST['hcptSeq']) $tmpHcptSeq = $_POST['hcptSeq'];
	if (!$tmpHcptSeq) $tmpHcptSeq = $hce->rcpt;

	$sql = 'SELECT	feel1_yn
			,		feel2_yn,feel2_rsn
			,		feel3_yn
			,		feel4_yn,feel4_rsn
			,		feel5_yn
			,		feel6_yn,feel6_eft
			,		feel7_yn,feel7_cnt,feel7_whn,feel7_rsn
			FROM	hce_inspection_feel
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$tmpHcptSeq.'\'
			AND		ispt_seq= \''.$isptSeq.'\'';

	$row = $conn->get_array($sql);

	$feel1Yn	= $row['feel1_yn'];
	$feel2Yn	= $row['feel2_yn'];
	$feel2Rsn	= StripSlashes($row['feel2_rsn']);
	$feel3Yn	= $row['feel3_yn'];
	$feel4Yn	= $row['feel4_yn'];
	$feel4Rsn	= StripSlashes($row['feel4_rsn']);
	$feel5Yn	= $row['feel5_yn'];
	$feel6Yn	= $row['feel6_yn'];
	$feel6Eft	= StripSlashes($row['feel6_eft']);
	$feel7Yn	= $row['feel7_yn'];
	$feel7Cnt	= StripSlashes($row['feel7_cnt']);
	$feel7Whn	= StripSlashes($row['feel7_whn']);
	$feel7Rsn	= StripSlashes($row['feel7_rsn']);

	Unset($row);
?>
<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);
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
		<col width="510px">
		<col span="2">
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="20">- 정서적 측면</th>
		</tr>
		<tr>
			<th class="head">No</th>
			<th class="head" colspan="2">질문</th>
			<th class="head">예</th>
			<th class="head last">아니오</th>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head">1</th>
			<td class="left" colspan="2">이전에 비해 요즘 더 잘 잊어버리십니까?[기억력]</td>
			<td class="center"><input id="opt1Y" name="opt1" type="radio" value="Y" style="width:60px; border:none;" <?=($feel1Yn == 'Y' ? 'checked' : '');?>></td>
			<td class="center last"><input id="opt1N" name="opt1" type="radio" value="N" style="width:60px; border:none;" <?=($feel1Yn == 'N' ? 'checked' : '');?>></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head" rowspan="2">2</th>
			<td class="left" colspan="2">안정되지 않거나 화가난 적이 자주 있습니까?[정서상태](예라면 이유를 작성하여 주십시오.)</td>
			<td class="center" rowspan="2"><input id="opt2Y" name="opt2" type="radio" value="Y" style="width:60px; border:none;" onclick="lfDisabled($('#txt2Rsn'),true);" <?=($feel2Yn == 'Y' ? 'checked' : '');?>></td>
			<td class="center last" rowspan="2"><input id="opt2N" name="opt2" type="radio" value="N" style="width:60px; border:none;" onclick="lfDisabled($('#txt2Rsn'),false);" <?=($feel2Yn == 'N' ? 'checked' : '');?>></td>
		</tr>
		<tr>
			<th class="head">이유</th>
			<td class="center"><input id="txt2Rsn" name="txt" type="text" value="<?=$feel2Rsn;?>" style="width:100%; <?=($feel2Yn != 'Y' ? 'background-color:#efefef;"' : '');?>" <?=($feel2Yn != 'Y' ? 'disabled="false"' : '');?>></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head">3</th>
			<td class="left" colspan="2">소지품 등을 자주 잃어버리십니까?</td>
			<td class="center"><input id="opt3Y" name="opt3" type="radio" value="Y" style="width:60px; border:none;" <?=($feel3Yn == 'Y' ? 'checked' : '');?>></td>
			<td class="center last"><input id="opt3N" name="opt3" type="radio" value="N" style="width:60px; border:none;" <?=($feel3Yn == 'N' ? 'checked' : '');?>></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head" rowspan="2">4</th>
			<td class="left" colspan="2">슬픔을 느끼는 적이 종종 있으십니까?[정서상태](예라면 이유를 작성하여 주십시오.)</td>
			<td class="center" rowspan="2"><input id="opt4Y" name="opt4" type="radio" value="Y" style="width:60px; border:none;" onclick="lfDisabled($('#txt4Rsn'),true);" <?=($feel4Yn == 'Y' ? 'checked' : '');?>></td>
			<td class="center last" rowspan="2"><input id="opt4N" name="opt4" type="radio" value="N" style="width:60px; border:none;" onclick="lfDisabled($('#txt4Rsn'),false);" <?=($feel4Yn == 'N' ? 'checked' : '');?>></td>
		</tr>
		<tr>
			<th class="head">이유</th>
			<td class="center"><input id="txt4Rsn" name="txt" type="text" value="<?=$feel4Rsn;?>" style="width:100%; <?=($feel4Yn != 'Y' ? 'background-color:#efefef;"' : '');?>" <?=($feel4Yn != 'Y' ? 'disabled="false"' : '');?>></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head">5</th>
			<td class="left" colspan="2">잠은 편안히 잘 주무십니까?</td>
			<td class="center"><input id="opt5Y" name="opt5" type="radio" value="Y" style="width:60px; border:none;" <?=($feel5Yn == 'Y' ? 'checked' : '');?>></td>
			<td class="center last"><input id="opt5N" name="opt5" type="radio" value="N" style="width:60px; border:none;" <?=($feel5Yn == 'N' ? 'checked' : '');?>></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head" rowspan="2">6</th>
			<td class="left" colspan="2">
				작년 한 해 동안 혹은 근래에 친구나 친척을 잃은 일이 있습니까? 있다면, 어떤 영향을 끼쳤습니까?
			</td>
			<td class="center" rowspan="2"><input id="opt6Y" name="opt6" type="radio" value="Y" style="width:60px; border:none;" onclick="lfDisabled($('#txt6Eft'),true);" <?=($feel6Yn == 'Y' ? 'checked' : '');?>></td>
			<td class="center last" rowspan="2"><input id="opt6N" name="opt6" type="radio" value="N" style="width:60px; border:none;" onclick="lfDisabled($('#txt6Eft'),false);" <?=($feel6Yn == 'N' ? 'checked' : '');?>></td>
		</tr>
		<tr>
			<th class="head">영향</th>
			<td class="center"><input id="txt6Eft" name="txt" type="text" value="<?=$feel6Eft;?>" style="width:100%; <?=($feel6Yn != 'Y' ? 'background-color:#efefef;"' : '');?>" <?=($feel6Yn != 'Y' ? 'disabled="false"' : '');?>></td>
		</tr>
		<tr onmouseover="lfMouseEvt(this,'OVER');" onmouseout="lfMouseEvt(this,'OUT');" style="cursor:default;">
			<th class="head" rowspan="2">7</th>
			<td class="left" colspan="2">정신 질환으로 입원하신 적이 있습니까?(예라면 이유를 작성하여 주십시오.)</td>
			<td class="center" rowspan="2"><input id="opt7Y" name="opt7" type="radio" value="Y" style="width:60px; border:none;" onclick="lfDisabled($('#txt7Whn'),true);lfDisabled($('#txt7Rsn'),true);lfDisabled($('#txt7Cnt'),true);" <?=($feel7Yn == 'Y' ? 'checked' : '');?>></td>
			<td class="center last" rowspan="2"><input id="opt7N" name="opt7" type="radio" value="N" style="width:60px; border:none;" onclick="lfDisabled($('#txt7Whn'),false);lfDisabled($('#txt7Rsn'),false);lfDisabled($('#txt7Cnt'),false);" <?=($feel7Yn == 'N' ? 'checked' : '');?>></td>
		</tr>
		<tr>
			<td class="left" colspan="2">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="45px">
						<col width="70px">
						<col width="45px">
						<col width="150px">
						<col width="45px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="head bottom">횟수</th>
							<td class="bottom"><input id="txt7Cnt" name="txt" type="text" value="<?=$feel7Cnt;?>" style="width:100%; <?=($feel7Yn != 'Y' ? 'background-color:#efefef;"' : '');?>" <?=($feel7Yn != 'Y' ? 'disabled="false"' : '');?>></td>
							<th class="head bottom">언제</th>
							<td class="bottom"><input id="txt7Whn" name="txt" type="text" value="<?=$feel7Whn;?>" style="width:100%; <?=($feel7Yn != 'Y' ? 'background-color:#efefef;"' : '');?>" <?=($feel7Yn != 'Y' ? 'disabled="false"' : '');?>></td>
							<th class="head bottom">이유</th>
							<td class="bottom last"><input id="txt7Rsn" name="txt" type="text" value="<?=$feel7Rsn;?>" style="width:100%; <?=($feel7Yn != 'Y' ? 'background-color:#efefef;"' : '');?>" <?=($feel7Yn != 'Y' ? 'disabled="false"' : '');?>></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<input id="bodyIdx" type="hidden" value="5">
<input id="isptSeq" type="hidden" value="<?=$isptSeq;?>">
<?
	include_once('../inc/_db_close.php');
?>