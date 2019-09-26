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
	
	$sql = 'select visit_c_cd as c_cd
			,      visit_m_cd as m_cd
			,      visit_m_nm as m_nm
			,      visit_dt as dt
			,      visit_tm as tm
			,      visit_h_bp as bp
			,      visit_h_nh as nh
			,      visit_h_bf_bs as bf_bs
			,      visit_h_af_time as af_time
			,      visit_h_af_bs as af_bs
			,      visit_h_cf as cf
			,      visit_h_body as body
			,      visit_h_soul as soul
			,      visit_other as other
			  from counsel_client_visit
			 where org_no     = \''.$code.'\'
			   and visit_yymm = \''.$yymm.'\'
			   and visit_seq  = \''.$seq.'\'
			   and del_flag   = \'N\'';

	$visit_if = $conn->get_array($sql);

	if (empty($visit_if['dt'])) $visit_if['dt'] = date('Y-m-d', mktime());

	if (Empty($visit_if['tm'])){
		$lsHour = '00';
		$lsMin  = '00';
	}else{
		$lsHour = SubStr($visit_if['tm'],0,2);
		$lsMin  = SubStr($visit_if['tm'],3,2);
	}
?>
<div style="">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="150px">
			<col width="70px">
			<col width="150px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold" colspan="5">방문상담이력</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center">상담일자</th>
				<td class="">
					<input name="visit_dt" type="text" class="date" value="<?=$visit_if['dt'];?>" style="margin-right:0;">
					<input name="visit_hour" type="text" class="no_string" value="<?=$lsHour;?>" style="width:20px; margin-left:0;" maxlength="2">:
					<input name="visit_min" type="text" class="no_string" value="<?=$lsMin;?>" style="width:20px; margin-left:0;" maxlength="2">
				</td>
				<th class="center">상담자</th>
				<td class="left last" style="padding-top:1px;">
					<span class="btn_pack find" onClick="__find_yoyangsa('<?=$code;?>','','visit_m_cd','visit_m_nm');"></span>
					<span id="visit_m_nm" style="height:100%; margin-left:5px; font-weight:bold;"><?=$visit_if['m_nm'];?></span>
					<input name="visit_m_cd" type="hidden" value="<?=$ed->en($visit_if['m_cd']);?>">
				</td>
				<td class="left">&nbsp;</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="margin-top:10px;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="300px">
			<col width="40px">
			<col width="120px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold" colspan="6">건강상태</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center">혈압</th>
				<td><input name="visit_bp" type="text" value="<?=$visit_if['bp'];?>">mmHg</td>
				<th class="center" rowspan="2">혈당</th>
				<th>식전</th>
				<td><input name="visit_bf_bs" type="text" value="<?=$visit_if['bf_bs'];?>" style="width:100%;"></td>
			</tr>
			<tr>
				<th class="center">&nbsp;</th>
				<td>&nbsp;</td>
				<th>식후<input name="visit_af_time" type="text" value="<?=$visit_if['af_time'];?>" style="width:50px;">시간</th>
				<td><input name="visit_af_bs" type="text" value="<?=$visit_if['af_bs'];?>" style="width:100%;"></td>
			</tr>
			<tr>
				<th class="center">영양</th>
				<td colspan="4" style="height:75px; padding:5px;"><textarea name="visit_nh" style="width:100%; height:100%; margin:0;"><?=stripslashes($visit_if['nh']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">배설</th>
				<td colspan="4" style="height:75px; padding:5px;"><textarea name="visit_cf" style="width:100%; height:100%; margin:0;"><?=stripslashes($visit_if['cf']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">신체적<br>상태</th>
				<td colspan="4" style="height:75px; padding:5px;"><textarea name="visit_body" style="width:100%; height:100%; margin:0;"><?=stripslashes($visit_if['body']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">정신적<br>상태</th>
				<td colspan="4" style="height:75px; padding:5px;"><textarea name="visit_soul" style="width:100%; height:100%; margin:0;"><?=stripslashes($visit_if['soul']);?></textarea></td>
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
				<th class="head bold">기타</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="height:75px; padding:5px;"><textarea name="visit_other" style="width:100%; height:100%; margin:0;"><?=stripslashes($visit_if['other']);?></textarea></td>
			</tr>
		</tbody>
	</table>
</div>
<input name="code" type="hidden" value="<?=$code;?>">
<input name="yymm" type="hidden" value="<?=$yymm;?>">
<input name="seq"  type="hidden" value="<?=$seq;?>">
<input name="visit_seq"  type="hidden" value="<?=$seq;?>">
<input name="visit_ssn"  type="hidden" value="<?=$ed->en($ssn);?>">

<?
	unset($visit_if);

	include_once("../inc/_db_close.php");
?>