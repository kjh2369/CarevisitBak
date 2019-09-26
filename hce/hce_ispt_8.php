<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	노인인지기능평가(MMSE-DS)검사
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
			FROM	hce_inspection_mmseds
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$tmpHcptSeq.'\'
			AND		ispt_seq= \''.$isptSeq.'\'';

	$mmseds = $conn->get_array($sql);

	$quest[0]	= Array('id'=>'Q1','no'=>'1.','quest'=>'올해는 몇 년도 입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[1]	= Array('id'=>'Q2','no'=>'2.','quest'=>'지금은 무슨 계절입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[2]	= Array('id'=>'Q3','no'=>'3.','quest'=>'오늘은 며칠입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[3]	= Array('id'=>'Q4','no'=>'4.','quest'=>'오늘은 무슨 요일입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[4]	= Array('id'=>'Q5','no'=>'5.','quest'=>'지금은 몇 월입니까?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[5]	= Array('id'=>'Q6','no'=>'6.','quest'=>'우리가 있는 이곳은 무슨 도/특별시/광역시 입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[6]	= Array('id'=>'Q7','no'=>'7.','quest'=>'여기는 무슨 시/군/구 입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[7]	= Array('id'=>'Q8','no'=>'8.','quest'=>'여기는 무슨 구/동/읍 입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[8]	= Array('id'=>'Q9','no'=>'9.','quest'=>'우리는 지금 이 건물의 몇 층에 있습니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[9]	= Array('id'=>'Q10','no'=>'10.','quest'=>'이 장소의 이름이 무엇입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[10]	= Array('id'=>'T','no'=>'11.','quest'=>'제가 세 가지 물건의 이름을 말씀드리겠습니다. 끝까지 다 들으신 다음에 세 가지 물건의 이름을 모두 말씀해 보십시오. 그리고 몇 분 후에는 그 세가지 물건의 이름들을 다시 물어볼 것이니 들으신 물건의 이름을 잘 기억하고 계십시오.<center>나무&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;자동차&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;모자</center>이제 ○○○님께서 방금 들으신 3가지 물건 이름을 모두 말씀해 보세요.','rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[11]	= Array('id'=>'Q11','no'=>'','quest'=>'나무','rst'=>'Y','border'=>'T','Y'=>'0','N'=>'1');
	$quest[12]	= Array('id'=>'Q12','no'=>'','quest'=>'자동차','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[13]	= Array('id'=>'Q13','no'=>'','quest'=>'모자','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[14]	= Array('id'=>'Q14','no'=>'12.','quest'=>'100에서 7을 빼면 얼마가 됩니까?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[15]	= Array('id'=>'Q15','no'=>'','quest'=>'거기에서 7을 빼면 얼마가 됩니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[16]	= Array('id'=>'Q16','no'=>'','quest'=>'거기에서 7을 빼면 얼마가 됩니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[17]	= Array('id'=>'Q17','no'=>'','quest'=>'거기에서 7을 빼면 얼마가 됩니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[18]	= Array('id'=>'Q18','no'=>'','quest'=>'거기에서 7을 빼면 얼마가 됩니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[19]	= Array('id'=>'T','no'=>'13.','quest'=>'조금 전에 제가 기억하라고 말씀드렸던 세 가지 물건의 이름이 무엇인지 말씀하여 주십시오?','rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[20]	= Array('id'=>'Q19','no'=>'','quest'=>'나무','rst'=>'Y','border'=>'T','Y'=>'0','N'=>'1');
	$quest[21]	= Array('id'=>'Q20','no'=>'','quest'=>'자동차','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[22]	= Array('id'=>'Q21','no'=>'','quest'=>'모자','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[23]	= Array('id'=>'Q22','no'=>'14.','quest'=>'(실제 시계를 보여주며) 이것을 무엇이라고 합니까?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[24]	= Array('id'=>'Q23','no'=>'','quest'=>'(실제 연필을 보여주며) 이것을 무엇이라고 합니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[25]	= Array('id'=>'T','no'=>'15.','quest'=>'제가 하는 말을 끝까지 듣고 따라해 보십시오. 한 번만 말씀드릴 것이니 잘 듣고 따라 하십시오.','rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[26]	= Array('id'=>'Q24','no'=>'','quest'=>'간장공장공장장','rst'=>'Y','border'=>'T','Y'=>'0','N'=>'1');
	$quest[27]	= Array('id'=>'T','no'=>'16.','quest'=>'지금부터 제가 말씀드리는 대로 해 보십시오. 한 번만 말씀드릴 것이니 잘 들으시고 그대로 해 보십시오.<br>제가 종이를 한 장 드릴 것입니다. 그러면 그 종이를 오른손으로 받아, 반으로 접은 다음, 무릎 위에 올려놓으십시오.','rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[28]	= Array('id'=>'Q25','no'=>'','quest'=>'오른손으로 받는다.','rst'=>'Y','border'=>'T','Y'=>'0','N'=>'1');
	$quest[29]	= Array('id'=>'Q26','no'=>'','quest'=>'반으로 접는다.','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[30]	= Array('id'=>'Q27','no'=>'','quest'=>'무릎 위에 놓는다.','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[31]	= Array('id'=>'T','no'=>'17.','quest'=>'(겹친 오각형 그림을 가리키며) 여기에 오각형이 겹쳐져 있는 그림이 있습니다. 이 그림을 아래 빈 곳에 그대로 그려보십시오.','rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[32]	= Array('id'=>'Q28','no'=>'','quest'=>'<img src="./img/test_17.GIF">','rst'=>'Y','border'=>'T','Y'=>'0','N'=>'1');
	$quest[33]	= Array('id'=>'Q29','no'=>'18.','quest'=>'옷은 왜 빨아서 입습니까?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[34]	= Array('id'=>'Q30','no'=>'19.','quest'=>'“티끌 모아 태산”은 무슨 뜻 입니까?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
?>
<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);

		//var currentPosition = parseInt($("#divTitle").css("top"));
		$('#divBody').scroll(function(){
			var pos1 = $('#divBody').scrollTop(); // 현재 스크롤바의 위치값을 반환합니다.
			//$("#divTitle").stop().animate({"top":pos1+currentPosition+"px"},0);
			$("#divTitle").css('top',pos1);

			var pos2 = $('#divBody').height() - $('#divBody').offset().top + $('#divBody').scrollTop()+90;
			$("#divBottom").css('top',pos2);
		});

		$('#divBody').scroll();
	});

	//저장
	function lfSaveSub(){
		var data = {};

		$('input:text').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();
			var dis = $(this).attr('disabled');

			if (!val || dis) val = '';

			data[id] = val;
		});

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
</script>
<div id="divTitle" style="position:relative; left:0; top:0;">
	<table class="my_table" style="width:100%; background-color:#FFFFFF;">
		<colgroup>
			<col width="80px">
			<col width="300px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="left">정규교육연수</th>
				<td class=""><input id="txtEduTraining" name="txt" type="text" value="<?=StripSlashes($mmseds['edu_training']);?>" style="width:100%;"></td>
				<th class="left">한글해독능력</th>
				<td class="last"><input id="txtKorDecode" name="txt" type="text" value="<?=StripSlashes($mmseds['kor_decode']);?>" style="width:100%;"></td>
			</tr>
		<tbody>
	</table>
	<table class="my_table" style="width:100%; background-color:#FFFFFF;">
		<colgroup>
			<col width="80px">
			<col width="168px">
			<col width="45px">
			<col width="70px">
			<col width="55px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="left">주된평생직업</th>
				<td class=""><input id="txtLifeJob" name="txt" type="text" value="<?=StripSlashes($mmseds['life_job']);?>" style="width:100%;"></td>
				<th class="left">검진일</th>
				<td class=""><input id="txtCheckDt" name="txt" type="text" value="<?=$myF->dateStyle($mmseds['check_dt']);?>" class="date" style=""></td>
				<th class="left">평가장소</th>
				<td class="last"><input id="txtEvlPlace" name="txt" type="text" value="<?=StripSlashes($mmseds['evl_place']);?>" style="width:100%;"></td>
			</tr>
		<tbody>
	</table>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="630px">
			<col width="70px" span="2">
		</colgroup>
		<tbody>
			<tr>
				<th class="left bold">- MMSE-DS</th>
				<th class="center bold">0</th>
				<th class="center bold last">1</th>
			</tr>
		<tbody>
	</table>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="30px">
		<col width="600px">
		<col width="70px" span="2">
	</colgroup>
	<tbody><?
		$point = 0;
		foreach($quest as $row){
			if ($row['border']){
				$border = 'border-top:1px solid #cccccc;';
			}else{
				$border = '';
			}

			if ($row['rst'] == 'Y'){?>
				<tr onmouseover="this.style.backgroundColor='#EFEFEF';" onmouseout="this.style.backgroundColor='#FFFFFF';"><?
			}else{?>
				<tr><?
			}?>
				<td class="right top bottom" style="border-right:none; <?=($row['border'] == 'Y' ? $border : '');?>"><?=$row['no'];?></td>
				<td class="bottom <?=($row['rst'] != 'Y' ? 'last' : '');?>" style="border-left:none; <?=($row['border'] == 'Y' || $row['border'] == 'T' ? $border : '');?>" colspan="<?=($row['rst'] == 'Y' ? '1' : '3');?>"><?=$row['quest'];?></td><?
				if ($row['rst'] == 'Y'){?>
					<td class="center bottom" style="<?=$border;?>"><div id="<?=$row['id']?>_Y" class="bold" style="width:100%; color:blue; cursor:default;" value="<?=$row['Y'];?>"><?=($mmseds[$row['id']] == 'Y' ? '0' : '');?></div></td>
					<td class="center bottom last" style="<?=$border;?>"><div id="<?=$row['id']?>_N" class="bold" style="width:100%; color:blue; cursor:default;" value="<?=$row['N'];?>"><?=($mmseds[$row['id']] == 'N' ? '1' : '');?></div></td><?
				}?>
			</tr><?

			if ($mmseds[$row['id']] == 'N'){
				$point += 1;
			}
		}?>
	</tbody>
	<tbody>
		<tr>
			<td class="last" style="padding-left:20px; border-top:1px solid #cccccc;" colspan="4">&nbsp;<br></td>
		</tr>
	</tbody>
</table>
<div id="divBottom" style="position:absolute; height:25px; left:0; top:0; text-align:right; padding:7px 5px 0 0; background-color:#FFFFFF; border-top:1px solid #CCCCCC;">
	총점 : <span id="lblTotPoint" class="bold" style="color:blue;"><?=$point;?></span> / 30점
</div>
<input id="bodyIdx" type="hidden" value="8">
<input id="isptSeq" type="hidden" value="<?=$isptSeq;?>">
<?
	include_once('../inc/_db_close.php');
?>