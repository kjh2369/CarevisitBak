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
	
	$sql = 'select stress_c_cd as c_cd
			,      stress_m_cd as m_cd
			,      stress_m_nm as m_nm
			,      stress_dt as dt
			,      stress_rct_kind as rct_kind
			,      stress_rct_kind_family as rct_kind_family
			,      stress_rct_kind_other as rct_kind_other
			,      stress_rct_path as rct_path 
			,      stress_rct_path_paper_yn as rct_path_paper_yn
			,      stress_rct_path_other as rct_path_other
			,      stress_cont_kind as cont_kind
			,      stress_cont_text as cont_text
			,      stress_proc_kind as proc_kind
			,      stress_proc_text as proc_text
			,      stress_rst_obj as rst_obj
			,      stress_rst_sub as rst_sub
			,      stress_rst_app as rst_app
			,      stress_rst_otr as rst_otr
			,      stress_after_plan as after_plan
			  from counsel_client_stress
			 where org_no      = \''.$code.'\'
			   and stress_yymm = \''.$yymm.'\'
			   and stress_seq  = \''.$seq.'\'
			   and del_flag    = \'N\'';
			   
	$stress_if = $conn->get_array($sql);
	
	if (empty($stress_if['dt'])) $stress_if['dt'] = date('Y-m-d', mktime());
	if (empty($stress_if['rct_kind'])) $stress_if['rct_kind'] = '1';
	if (empty($stress_if['rct_path'])) $stress_if['rct_path'] = '1';
	if (empty($stress_if['cont_kind'])) $stress_if['cont_kind'] = '1';
	if (empty($stress_if['proc_kind'])) $stress_if['proc_kind'] = '1';
?>
<div style="">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="70px">
			<col width="70px">
			<col width="150px">
			<col width="90px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold" colspan="6">불만 및 고충처리 상담이력</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center">접수일자</th>
				<td class="center"><input name="stress_dt" type="text" class="date" value="<?=$stress_if['dt'];?>"></td>
				<th class="center">접수자</th>
				<td class="left" style="padding-top:1px;">
					<span class="btn_pack find" onClick="__find_yoyangsa('<?=$code;?>','','stress_m_cd','stress_m_nm');"></span>
					<span id="stress_m_nm" style="height:100%; margin-left:5px; font-weight:bold;"><?=$stress_if['m_nm'];?></span>
					<input name="stress_m_cd" type="hidden" value="<?=$ed->en($stress_if['m_cd']);?>">
				</td>
				<th class="center">접수인 구분</th>
				<td class="">
					<input name="stress_kind" type="radio" value="1" class="radio" <? if($stress_if['rct_kind'] == '1'){echo 'checked';} ?>>본인
					<input name="stress_kind" type="radio" value="2" class="radio" <? if($stress_if['rct_kind'] == '2'){echo 'checked';} ?> onclick="init_stress_reg();">가족(<input name="stress_kind_family" type="text" value="<?=$stress_if['rct_kind_family'];?>" style="width:50px;">)
					<input name="stress_kind" type="radio" value="3" class="radio" <? if($stress_if['rct_kind'] == '3'){echo 'checked';} ?>>직원
					<input name="stress_kind" type="radio" value="9" class="radio" <? if($stress_if['rct_kind'] == '9'){echo 'checked';} ?> onclick="init_stress_reg();">기타(<input name="stress_kind_other" type="text" value="<?=$stress_if['rct_kind_other'];?>" style="width:50px;">)
				</td>
			</tr>
			<tr>
				<th class="center">접수경로</th>
				<td class="" colspan="5">
					<input name="stress_path" type="radio" value="1" class="radio" <? if($stress_if['rct_path'] == '1'){echo 'checked';} ?>>내방
					<input name="stress_path" type="radio" value="2" class="radio" <? if($stress_if['rct_path'] == '2'){echo 'checked';} ?>>방문
					<input name="stress_path" type="radio" value="3" class="radio" <? if($stress_if['rct_path'] == '3'){echo 'checked';} ?>>전화
					<input name="stress_path" type="radio" value="4" class="radio" <? if($stress_if['rct_path'] == '4'){echo 'checked';} ?>>홈페이지
					<input name="stress_path" type="radio" value="5" class="radio" <? if($stress_if['rct_path'] == '5'){echo 'checked';} ?> onclick="init_stress_reg();">서면( 내용증명여부 <input name="stress_path_paper_yn" type="radio" value="Y" class="radio" <? if($stress_if['rct_path_paper_yn'] == 'Y'){echo 'checked';} ?>>예 <input name="stress_path_paper_yn" type="radio" value="N" class="radio" <? if($stress_if['rct_path_paper_yn'] != 'Y'){echo 'checked';} ?>>아니오)
					<input name="stress_path" type="radio" value="9" class="radio" <? if($stress_if['rct_path'] == '9'){echo 'checked';} ?> onclick="init_stress_reg();">기타(<input name="stress_path_other" type="text" value="">)
				</td>
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
				<th class="head bold" colspan="2">내용</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center" rowspan="2">불만 및<br>고충 내용</th>
				<td class="">
					<input name="stress_cont_kind" type="radio" value="1" class="radio" <? if ($stress_if['cont_kind'] == '1'){echo 'checked';} ?>>직원불친절
					<input name="stress_cont_kind" type="radio" value="2" class="radio" <? if ($stress_if['cont_kind'] == '2'){echo 'checked';} ?>>서비스내용
					<input name="stress_cont_kind" type="radio" value="3" class="radio" <? if ($stress_if['cont_kind'] == '3'){echo 'checked';} ?>>서비스비용
					<input name="stress_cont_kind" type="radio" value="9" class="radio" <? if ($stress_if['cont_kind'] == '9'){echo 'checked';} ?>>기타
				</td>
			</tr>
			<tr>
				<td style="height:75px; padding:5px;"><textarea name="stress_cont_text" style="width:100%; height:100%; margin:0;"><?=stripslashes($stress_if['cont_text']);?></textarea></td>
			</tr>
			<tr>
				<th class="center" rowspan="2">처리<br>방법</th>
				<td class="">
					<input name="stress_proc_kind" type="radio" value="1" class="radio" <? if ($stress_if['proc_kind'] == '1'){echo 'checked';} ?>>경청 등 효과적 대화
					<input name="stress_proc_kind" type="radio" value="2" class="radio" <? if ($stress_if['proc_kind'] == '2'){echo 'checked';} ?>>이용자 방문
					<input name="stress_proc_kind" type="radio" value="3" class="radio" <? if ($stress_if['proc_kind'] == '3'){echo 'checked';} ?>>서면
					<input name="stress_proc_kind" type="radio" value="9" class="radio" <? if ($stress_if['proc_kind'] == '9'){echo 'checked';} ?>>기타
				</td>
			</tr>
			<tr>
				<td style="height:75px; padding:5px;"><textarea name="stress_proc_text" style="width:100%; height:100%; margin:0;"><?=stripslashes($stress_if['proc_text']);?></textarea></td>
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
				<th class="head bold" colspan="2">처리결과</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center">객관적<br>결과</th>
				<td style="height:75px; padding:5px;"><textarea name="stress_rst_obj" style="width:100%; height:100%; margin:0;"><?=stripslashes($stress_if['rst_obj']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">접수인의<br>주관적<br>반응</th>
				<td style="height:75px; padding:5px;"><textarea name="stress_rst_sub" style="width:100%; height:100%; margin:0;"><?=stripslashes($stress_if['rst_sub']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">직원의<br>평가</th>
				<td style="height:75px; padding:5px;"><textarea name="stress_rst_app" style="width:100%; height:100%; margin:0;"><?=stripslashes($stress_if['rst_app']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">기타</th>
				<td style="height:75px; padding:5px;"><textarea name="stress_rst_otr" style="width:100%; height:100%; margin:0;"><?=stripslashes($stress_if['rst_otr']);?></textarea></td>
			</tr>
		</tbody>
	</table>
</div>

<div style="margin-top:10px;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold">이후 계획</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="height:75px; padding:5px;"><textarea name="stress_after_plan" style="width:100%; height:100%; margin:0;"><?=stripslashes($stress_if['after_plan']);?></textarea></td>
			</tr>
		</tbody>
	</table>
</div>

<input name="code" type="hidden" value="<?=$code;?>">
<input name="yymm" type="hidden" value="<?=$yymm;?>">
<input name="seq"  type="hidden" value="<?=$seq;?>">
<input name="stress_seq"  type="hidden" value="<?=$seq;?>">
<input name="stress_ssn"  type="hidden" value="<?=$ed->en($ssn);?>">

<?
	unset($stress_if);
	
	include_once("../inc/_db_close.php");
?>