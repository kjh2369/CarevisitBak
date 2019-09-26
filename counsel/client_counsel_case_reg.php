<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code = $_POST['code'];
	$yymm = $_POST['yymm'];
	$seq  = $_POST['seq'];
	$ssn  = $ed->de($_POST['ssn']);

	if (empty($yymm)){
		$yymm = date('Ym', mktime());
		$seq  = 0;
	}

	$mode = 1;

	if ($_SERVER['PHP_SELF'] == '/yoyangsa/mem_counsel.php'){
		$mode = 2;
	}

	$sql = 'select *
			  from counsel_client_case
			 where org_no    = \''.$code.'\'
			   and case_yymm = \''.$yymm.'\'
			   and case_seq  = \''.$seq.'\'';

	$case_if = $conn->get_array($sql);

	if (empty($case_if['case_dt'])) $case_if['case_dt'] = date('Y-m-d', mktime());

?>
<div style="">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="70px">
			<col width="70px">
			<col width="150px">
			<col width="70px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold" colspan="6">회의이력</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center">회의일자</th>
				<td class="center"><input name="case_dt" type="text" class="date" value="<?=$case_if['case_dt'];?>"></td>
				<th class="center">주관자</th>
				<td class="left" style="padding-top:1px;">
					<span class="btn_pack find" onClick="__find_yoyangsa('<?=$code;?>','','case_run_cd','case_run_nm');"></span>
					<span id="case_run_nm" style="height:100%; margin-left:5px; font-weight:bold;"><?=$case_if['case_run_nm'];?></span>
					<input name="case_run_cd" type="hidden" value="<?=$ed->en($case_if['case_run_cd']);?>">
				</td>
				<th class="center">참석자</th>
				<td><input name="case_present_nm" type="text" value="<?=$case_if['case_present_nm'];?>" style="width:100%;"></td>
			</tr>
			<tr>
				<th class="center">서비스명</th>
				<td colspan="5">
				<?
					// 기관분류 리스트
					$k_list = $conn->kind_list($code, true);
					$k_cnt  = sizeof($k_list);
					$c_list = $conn->client_kind_list($code, $ssn);

					$compare_case_list = '';

					if (Is_Array($c_list)){
						foreach($c_list as $i => $c){
							$compare_case_list .= $c['code'].'_'.$c['name'].'/';
						}
					}

					$current_case_list = $case_if['case_svc_kind'];

					include('../sugupja/client_reg_service_menu.php');

					unset($k_list);
					unset($k_cnt);
					unset($c_list);
					unset($compare_list);
				?>
				</td>
			</tr>
			<tr>
				<th class="center">이용기간</th>
				<td colspan="5">
					<input name="case_use_from" type="text" value="<?=$case_if['case_use_from'];?>" class="date"> ~
					<input name="case_use_to" type="text" value="<?=$case_if['case_use_to'];?>" class="date">
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="margin-top:10px;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="150px">
			<col width="70px" span="5">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold" colspan="8">서비스 제공자</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center">성명</th>
				<td class="left" style="padding-top:1px;">
					<span class="btn_pack find" onClick="__find_member_if('<?=$code;?>',{'name':'case_m_nm','jumin':'case_m_cd','age':'case_m_age','gender':'case_m_gender'});"></span>
					<span id="case_m_nm" style="height:100%; margin-left:5px; font-weight:bold;"><?=$case_if['case_m_nm'];?></span>
					<input name="case_m_cd" type="hidden" value="<?=$ed->en($case_if['case_m_cd']);?>">
				</td>
				<th class="center">나이</th>
				<td><span id="case_m_age" style="height:100%; margin-left:5px; font-weight:bold;"><?=$case_if['case_m_age'];?></span></td>
				<th class="center">성별</th>
				<td><span id="case_m_gender" style="height:100%; margin-left:5px; font-weight:bold;"><?=$case_if['case_m_gender'];?></span></td>
				<th class="center">재직경력</th>
				<td><input name="case_m_career" type="text" value="<?=$case_if['case_m_career'];?>" style="width:100%;"></td>
			</tr>
		</tbody>
	</table>
</div>

<div style="margin-top:10px;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold" colspan="2">요구내용</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center">경제력</th>
				<td style="height:75px; padding:5px;"><textarea name="case_economy" style="width:100%; height:100%; margin:0;"><?=stripslashes($case_if['case_economy']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">가정환경</th>
				<td style="height:75px; padding:5px;"><textarea name="case_family" style="width:100%; height:100%; margin:0;"><?=stripslashes($case_if['case_family']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">정서적</th>
				<td style="height:75px; padding:5px;"><textarea name="case_soul" style="width:100%; height:100%; margin:0;"><?=stripslashes($case_if['case_soul']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">육체적</th>
				<td style="height:75px; padding:5px;"><textarea name="case_body" style="width:100%; height:100%; margin:0;"><?=stripslashes($case_if['case_body']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">기타</th>
				<td style="height:75px; padding:5px;"><textarea name="case_other" style="width:100%; height:100%; margin:0;"><?=stripslashes($case_if['case_other']);?></textarea></td>
			</tr>
		</tbody>
	</table>
</div>

<div style="margin-top:10px;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center">주요<br>문제점</th>
				<td style="height:75px; padding:5px;"><textarea name="case_main_quest" style="width:100%; height:100%; margin:0;"><?=stripslashes($case_if['case_main_quest']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">침석자<br>의견</th>
				<td style="height:75px; padding:5px;"><textarea name="case_present_talk" style="width:100%; height:100%; margin:0;"><?=stripslashes($case_if['case_present_talk']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">추후<br>계획</th>
				<td style="height:75px; padding:5px;"><textarea name="case_later_plan" style="width:100%; height:100%; margin:0;"><?=stripslashes($case_if['case_later_plan']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">진행<br>일정</th>
				<td style="height:75px; padding:5px;"><textarea name="case_proc_period" style="width:100%; height:100%; margin:0;"><?=stripslashes($case_if['case_proc_period']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">이후<br>계획</th>
				<td style="height:75px; padding:5px;"><textarea name="case_after_plan" style="width:100%; height:100%; margin:0;"><?=stripslashes($case_if['case_after_plan']);?></textarea></td>
			</tr>
		</tbody>
	</table>
</div><?

$counselId	= '0040';?>
<div style="margin-top:10px;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="250px">
			<col width="70px">
			<col width="250px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold" colspan="5">
					<div style="float:right; width:auto; font-weight:normal; margin-right:3px;"><span class="btn_pack m"><button type="button" onclick="_ccAttachAdd();">추가</button></span></div>
					<div style="float:center; width:auto;">첨부파일</div>
				</th>
			</tr>
		</thead>
		<tbody id="bodyAttachFile"><?
		$sql = 'SELECT	no
				,		subject
				,		file_name
				,		file_size
				,		file_type
				FROM	counsel_file
				WHERE	org_no		= \''.$code.'\'
				AND		counsel_id	= \''.$counselId.'\'
				AND		yymm		= \''.$yymm.'\'
				AND		seq			= \''.$seq.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt	= $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row	= $conn->select_row($i);?>
			<tr id="<?=$counselId;?>" yymm="<?=$yymm;?>" seq="<?=$seq;?>" no="<?=$row['no'];?>">
				<th class="center">제목</th>
				<td class="center"><div class="left nowrap" style="width:250px;"><?=$row['subject'];?></div></td>
				<th class="center">첨부파일</th>
				<td class="center"><div class="left nowrap" style="width:250px;"><?=$row['file_name'];?></div></td>
				<td class="left"><span class="btn_pack m"><button type="button" onclick="_ccAttachDel($(this).parent().parent().parent(),true);">삭제</button></span></td>
			</tr><?
		}

		$conn->row_free();?>
		</tbody>
	</table>
</div>

<input name="code" type="hidden" value="<?=$code;?>">
<input name="yymm" type="hidden" value="<?=$yymm;?>">
<input name="seq"  type="hidden" value="<?=$seq;?>">
<input name="case_seq"  type="hidden" value="<?=$seq;?>">
<input name="case_ssn"  type="hidden" value="<?=$ed->en($ssn);?>">

<?
	unset($case_if);

	include_once("../inc/_db_close.php");
?>