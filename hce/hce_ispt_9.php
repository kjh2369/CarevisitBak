<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	노인우울척도
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

	//노인인지능력평가
	$sql = 'SELECT	*
			FROM	hce_inspection_sgds
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$tmpHcptSeq.'\'
			AND		ispt_seq= \''.$isptSeq.'\'';

	$sgds = $conn->get_array($sql);

	$quest[0]	= Array('id'=>'Q1','no'=>'1.','quest'=>'현재의 생활에 대체적으로 만족하십니까?','Y'=>'0','N'=>'1');
	$quest[1]	= Array('id'=>'Q2','no'=>'2.','quest'=>'요즈음 들어 활동량이나 의욕이 많이 떨어지셨습니까?','Y'=>'1','N'=>'0');
	$quest[2]	= Array('id'=>'Q3','no'=>'3.','quest'=>'자신이 헛되이 살고 있다고 느끼십니까?','Y'=>'1','N'=>'0');
	$quest[3]	= Array('id'=>'Q4','no'=>'4.','quest'=>'생활이 지루하게 느껴질 때가 많습니까?','Y'=>'1','N'=>'0');
	$quest[4]	= Array('id'=>'Q5','no'=>'5.','quest'=>'평소에 기분은 상쾌한 편이십니까?','Y'=>'0','N'=>'1');
	$quest[5]	= Array('id'=>'Q6','no'=>'6.','quest'=>'자신에게 불길한 일이 닥칠 것 같아 불안하십니까?','Y'=>'1','N'=>'0');
	$quest[6]	= Array('id'=>'Q7','no'=>'7.','quest'=>'대체로 마음이 즐거운 편이십니까?','Y'=>'0','N'=>'1');
	$quest[7]	= Array('id'=>'Q8','no'=>'8.','quest'=>'절망적이라는 느낌이 자주 드십니까?','Y'=>'1','N'=>'0');
	$quest[8]	= Array('id'=>'Q9','no'=>'9.','quest'=>'바깥에 나가기가 싫고 집에만 있고 싶습니까?','Y'=>'1','N'=>'0');
	$quest[9]	= Array('id'=>'Q10','no'=>'10.','quest'=>'비슷한 나이의 다른 노인들보다 기억력이 더 나쁘다고 느끼십니까?','Y'=>'1','N'=>'0');
	$quest[10]	= Array('id'=>'Q11','no'=>'11.','quest'=>'현재 살아 있다는 것이 즐겁게 생각되십니까?','Y'=>'0','N'=>'1');
	$quest[11]	= Array('id'=>'Q12','no'=>'12.','quest'=>'지금의 내 자신이 아무 쓸모 없는 사람이라고 느끼십니까?','Y'=>'1','N'=>'0');
	$quest[12]	= Array('id'=>'Q13','no'=>'13.','quest'=>'기력이 좋으신 편입니까?','Y'=>'0','N'=>'1');
	$quest[13]	= Array('id'=>'Q14','no'=>'14.','quest'=>'지금 자신의 처지가 아무런 희망도 없다고 느끼십니까?','Y'=>'1','N'=>'0');
	$quest[14]	= Array('id'=>'Q15','no'=>'15.','quest'=>'자신이 다른 사람들의 처지보다 더 못하다고 느끼십니까?','Y'=>'1','N'=>'0');?>

<script type="text/javascript">
	$(document).ready(function(){
		//var currentPosition = parseInt($("#divTitle").css("top"));
		$('#divBody').scroll(function(){
			var pos1 = $('#divBody').scrollTop(); // 현재 스크롤바의 위치값을 반환합니다.
			//$("#divTitle").stop().animate({"top":pos1+currentPosition+"px"},0);
			$("#divTitle").css('top',pos1);

			var pos2 = $('#divBody').height() - $('#divBody').offset().top + $('#divBody').scrollTop()+63;
			$("#divBottom").css('top',pos2);
		});

		$('div[id^="Q"]').unbind('click').bind('click',function(){
			var tmp = $(this).attr('id').split('_');
			var id  = tmp[0];
			var pnt = 0;

			$('div[id^="'+id+'"]').text('');

			$(this).text($(this).attr('value'));

			$('div[id^="Q"]').each(function(){
				var i = __str2num($(this).text());

				pnt += i;
			});

			$('#lblTotPoint').text(pnt);
		});

		$('#ID_DIV_LIST').height(__GetHeight($('#ID_DIV_LIST')) - 63).css('border-bottom','1px solid red');
		$('#divBody').scroll();
	});

	//저장
	function lfSaveSub(){
		var data = {};

		$('input:hidden').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

		$('div[id^="Q"]').each(function(){
			var tmp = $(this).attr('id').split('_');
			var id  = tmp[0];
			var val = '';

			if ($('#'+id+'_N').text()) val = 'N';
			if ($('#'+id+'_Y').text()) val = 'Y';

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
<div id="divTitle" style="position:relative; left:0; top:0;">
<div style="overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="630px">
			<col width="70px" span="2">
		</colgroup>
		<tr>
			<th class="bold last" colspan="3">- 노인우울척도(Short Form of Geriatric Depression Scale : SGDS)</th>
		</tr>
		<tr>
			<th class="center">문항</th>
			<th class="center">예</th>
			<th class="center last">아니오</th>
		</tr>
	</table>
</div>
</div>
<div id="ID_DIV_LIST" style="height:200px; overflow-x:hidden; overflow-y:scroll;">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="30px">
		<col width="600px">
		<col width="70px" span="2">
	</colgroup>
	<tbody><?
		$point = 0;
		foreach($quest as $row){?>
			<tr>
				<td class="center" style="border-right:none;"><div style="text-align:right; margin-right:5px;"><?=$row['no'];?></div></td>
				<td style="border-left:none;"><?=$row['quest'];?></td>
				<td class="center"><div id="<?=$row['id']?>_Y" class="bold" style="width:100%; color:blue; cursor:default;" value="<?=$row['Y'];?>"><?=($sgds[$row['id']] == 'Y' ? $row['Y'] : '');?></div></td>
				<td class="center last"><div id="<?=$row['id']?>_N" class="bold" style="width:100%; color:blue; cursor:default;" value="<?=$row['N'];?>"><?=($sgds[$row['id']] == 'N' ? $row['N'] : '');?></div></td>
			</tr><?

			if ($sgds[$row['id']] == 'Y') $point += $row['Y'];
			if ($sgds[$row['id']] == 'N') $point += $row['N'];
		}?>
	</tbody>
</table>
</div>
<div id="divBottom" style="position:absolute; height:25px; left:0; top:0; text-align:right; padding:7px 5px 0 0; background-color:#FFFFFF; border-top:1px solid #CCCCCC;">
	총점 : <span id="lblTotPoint" class="bold" style="color:blue;"><?=$point;?></span>
</div>
<input id="bodyIdx" type="hidden" value="9">
<input id="isptSeq" type="hidden" value="<?=$isptSeq;?>">
<?
	include_once('../inc/_db_close.php');
?>