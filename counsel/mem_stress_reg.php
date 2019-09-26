<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$self	= $myF->_self();
	
	if ($self == 'mem_stress_reg')
		$stress_mode = 1;
	else
		$stress_mode = 2;

	$code	= $_POST['code'];
	$kind	= $conn->center_kind($code);
	$ssn	= $ed->de($_POST['ssn']);
	$seq	= $_POST['seq'];
	$m_nm	= $conn->member_name($code, $ssn);

	if (!is_numeric($seq)) $seq = 0;

	$sql = "select *
			  from counsel_stress
			 where org_no     = '$code'
			   and stress_ssn = '$ssn'
			   and stress_seq = '$seq'";

	$stress = $conn->get_array($sql);

	if (!$stress['stress_type'])$stress['stress_type'] = '1';
	if (!$stress['stress_dt'])	$stress['stress_dt']   = date('Y-m-d', mktime());
?>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
	<?
		if ($stress_mode == 1){
			echo '<col width=\'70px\'>
				  <col width=\'190px\'>';
		}else{
			echo '<col width=\'70px\'>
				  <col width=\'150px\'>
				  <col width=\'70px\'>
				  <col width=\'150px\'>';
		}?>
		<col width="70px">
		<col width="200px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head bold last" colspan="8">상담이력</th>
		</tr>
	</thead>
	<tbody>
		<!-- 상담자 -->
		<tr>
		<?
			if ($stress_mode == 1){
			}else{
				echo '<th>직원명</th>';
				echo '<td class=\'left\'>
						<span class=\'btn_pack find\' onClick=\'__find_yoyangsa("'.$code.'","'.$kind.'","stress_ssn","m_nm");\'></span>
						<span id=\'m_nm\' style=\'height:100%; margin-left:5px; font-weight:bold;\'>'.$m_nm.'</span>
						<input name="stress_ssn" type="hidden" value="'.$ed->en($ssn).'" />
					  </td>';
			}
		?>
			<th>상담자</th>
			<td class="left" style="padding-top:1px;">
				<span class="btn_pack find" onClick="__find_yoyangsa('<?=$code;?>','<?=$kind;?>','stress_talker_cd','stress_talker_str');"></span>
				<span id="stress_talker_str" style="height:100%; margin-left:5px; font-weight:bold;"><?=$stress['stress_talker_nm'];?></span>
				<input name="stress_talker_cd" type="hidden" value="<?=$ed->en($stress['stress_talker_ssn']);?>">
				<input name="stress_talker_nm" type="hidden" value="<?=$stress['stress_talker_nm'];?>">
			</td>
			<th>상담유형</th>
			<td>
				<input name="stress_type" type="radio" class="radio" value="1" <? if($stress['stress_type'] == '1'){?>checked<?} ?>>내방
				<input name="stress_type" type="radio" class="radio" value="2" <? if($stress['stress_type'] == '2'){?>checked<?} ?>>방문
				<input name="stress_type" type="radio" class="radio" value="3" <? if($stress['stress_type'] == '3'){?>checked<?} ?>>전화
			</td>
			<th>상담일</th>
			<td>
				<input name="stress_date" type="text" value="<?=$stress['stress_dt'];?>" class="date" onkeydown="__onlyNumber(this);">
				<input name="temp_date" type="hidden" value="<?=$stress['stress_dt'];?>">
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="150px">
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head bold last" colspan="8">업무내용</th>
		</tr>
	</thead>
	<tbody>
		<!-- 업무내용 -->
		<tr>
			<th>현재 가장 어려운점</th>
			<td class="last" colspan="6">
				<input name="stress_work_hard" type="text" value="<?=stripslashes($stress['stress_work_hard']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th>업무 적성 문제</th>
			<td class="last" colspan="6">
				<input name="stress_work_aptitude" type="text" value="<?=stripslashes($stress['stress_work_aptitude']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th>이용자(보호자)관계</th>
			<td class="last" colspan="6">
				<input name="stress_work_client" type="text" value="<?=stripslashes($stress['stress_work_client']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th rowspan="2">업무량</th>
			<th>일 근로시간</th>
			<td>
				<input name="stress_work_day" type="text" value="<?=$stress['stress_work_day'];?>" class="number">
			</td>
			<th>주 근로시간</th>
			<td>
				<input name="stress_work_week" type="text" value="<?=$stress['stress_work_week'];?>" class="number">
			</td>
			<th>월 근로시간</th>
			<td class="last">
				<input name="stress_work_month" type="text" value="<?=$stress['stress_work_month'];?>" class="number">
			</td>
		</tr>
		<tr>
			<th>희망근로시간</th>
			<td>
				<input name="stress_work_hope_time" type="text" value="<?=$stress['stress_work_hope_time'];?>">
			</td>
			<th>희망급여</th>
			<td class="last" colspan="3">
				<input name="stress_work_hope_pay" type="text" value="<?=$stress['stress_work_hope_pay'];?>">
			</td>
		</tr>
		<tr>
			<th>업무변동(이직)계획</th>
			<td class="last" colspan="6">
				<input name="stress_work_change" type="text" value="<?=stripslashes($stress['stress_work_change']);?>" style="width:100%;">
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="150px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head bold last" colspan="8">개인사항</th>
		</tr>
	</thead>
	<tbody>
		<!-- 개인사항 -->
		<tr>
			<th>어려운 가족사항</th>
			<td class="last" colspan="6">
				<textarea name="stress_person_family" style="width:100%; height:50px;"><?=stripslashes($stress['stress_person_family']);?></textarea>
			</td>
		</tr>
		<tr>
			<th>경제적문제</th>
			<td class="last" colspan="6">
				<textarea name="stress_person_economy" style="width:100%; height:50px;"><?=stripslashes($stress['stress_person_economy']);?></textarea>
			</td>
		</tr>
		<tr>
			<th>건강문제</th>
			<td class="last" colspan="6">
				<textarea name="stress_person_health" style="width:100%; height:50px;"><?=stripslashes($stress['stress_person_health']);?></textarea>
			</td>
		</tr>
		<tr>
			<th>기타</th>
			<td class="last" colspan="6">
				<textarea name="stress_person_other" style="width:100%; height:50px;"><?=stripslashes($stress['stress_person_other']);?></textarea>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="150px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head bold last" colspan="8">기관관계</th>
		</tr>
	</thead>
	<tbody>
		<!-- 기관관계 -->
		<tr>
			<th>월례회의 및 직원전체모임</th>
			<td class="last" colspan="6">
				<input name="stress_center_meet" type="text" value="<?=stripslashes($stress['stress_center_meet']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th>기관교육</th>
			<td class="last" colspan="6">
				<input name="stress_center_edu" type="text" value="<?=stripslashes($stress['stress_center_edu']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th>실무자관계</th>
			<td class="last" colspan="6">
				<input name="stress_center_worker" type="text" value="<?=stripslashes($stress['stress_center_worker']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th>주민연대</th>
			<td class="last" colspan="6">
				<input name="stress_center_person" type="text" value="<?=stripslashes($stress['stress_center_person']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th>기타건의사항</th>
			<td class="last" colspan="6">
				<input name="stress_center_other" type="text" value="<?=stripslashes($stress['stress_center_other']);?>" style="width:100%;">
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="150px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head bold last" colspan="8">자기개발계획</th>
		</tr>
	</thead>
	<tbody>
		<!-- 자기개발계획 -->
		<tr>
			<th>교육계획(참여가능)</th>
			<td class="last" colspan="6">
				<input name="stress_self_edu" type="text" value="<?=stripslashes($stress['stress_self_edu']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th>소모임 참여계획</th>
			<td class="last" colspan="6">
				<input name="stress_self_meet" type="text" value="<?=stripslashes($stress['stress_self_meet']);?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th>기타 개인 계획</th>
			<td class="last" colspan="6">
				<input name="stress_self_other" type="text" value="<?=stripslashes($stress['stress_self_other']);?>" style="width:100%;">
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="150px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head bold last" colspan="8">상담결과 및 조치사항</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>기타</th>
			<td class="last" colspan="7">
				<textarea name="stress_other" style="width:100%; height:50px;"><?=stripslashes($stress['stress_other']);?></textarea>
			</td>
		</tr>

		<tr>
			<th>상담자의견</th>
			<td class="last" colspan="7">
				<textarea name="stress_talker_cont" style="width:100%; height:50px;"><?=stripslashes($stress['stress_talker_cont']);?></textarea>
			</td>
		</tr>

		<tr>
			<th>조치및결과</th>
			<td class="last" colspan="7">
				<textarea name="stress_result" style="width:100%; height:50px;"><?=stripslashes($stress['stress_result']);?></textarea>
			</td>
		</tr>
	</tbody>
</table>

<?
	unset($stress);

	###########################################################
	# 환경변수

	echo '<input name=\'process_code\' type=\'hidden\' value=\''.$code.'\'>';
	echo '<input name=\'process_ssn\'  type=\'hidden\' value=\''.$ed->en($ssn).'\'>';
	echo '<input name=\'process_seq\'  type=\'hidden\' value=\''.$seq.'\'>';

	###########################################################

	include_once("../inc/_db_close.php");
?>