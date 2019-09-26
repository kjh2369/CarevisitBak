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
	
	$sql = 'select phone_c_cd as c_cd
			,      phone_m_cd as m_cd
			,      phone_m_nm as m_nm
			,      phone_dt as dt
			,      phone_kind as kind
			,      phone_start as start
			,      phone_end as end
			,      phone_contents as cont
			,      phone_result as result
			,      phone_other as other
			  from counsel_client_phone
			 where org_no     = \''.$code.'\'
			   and phone_yymm = \''.$yymm.'\'
			   and phone_seq  = \''.$seq.'\'
			   and del_flag   = \'N\'';
			   
	$phone_if = $conn->get_array($sql);
	
	if (empty($phone_if['dt'])) $phone_if['dt'] = date('Y-m-d', mktime());
	if (empty($phone_if['kind'])) $phone_if['kind'] = '1';
	if (!empty($phone_if['start'])) $phone_if['start'] = substr($phone_if['start'],0,5);
	if (!empty($phone_if['end'])) $phone_if['end'] = substr($phone_if['end'],0,5);
?>
<div style="">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="70px">
			<col width="70px">
			<col width="145px">
			<col width="70px">
			<col width="190px">
			<col width="70px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold" colspan="8">전화상담이력</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center">상담일자</th>
				<td class="center"><input name="phone_dt" type="text" class="date" value="<?=$phone_if['dt'];?>"></td>
				<th class="center">상담자</th>
				<td class="left" style="padding-top:1px;">
					<span class="btn_pack find" onClick="__find_yoyangsa('<?=$code;?>','','phone_m_cd','phone_m_nm');"></span>
					<span id="phone_m_nm" style="height:100%; margin-left:5px; font-weight:bold;"><?=$phone_if['m_nm'];?></span>
					<input name="phone_m_cd" type="hidden" value="<?=$ed->en($phone_if['m_cd']);?>">
				</td>
				<th class="center">상담유형</th>
				<td class="left">
					<input name="phone_kind" type="radio" class="radio" value="1" <? if($phone_if['kind'] == '1'){echo 'checked';} ?>>고객
					<input name="phone_kind" type="radio" class="radio" value="2" <? if($phone_if['kind'] == '2'){echo 'checked';} ?>>직원
					<input name="phone_kind" type="radio" class="radio" value="3" <? if($phone_if['kind'] == '3'){echo 'checked';} ?>>관리자
				</td>
				<th class="center">통화시간</th>
				<td class="center">
					<input name="phone_start" type="text" value="<?=$phone_if['start'];?>" class="no_string" style="text-align:center;" alt="time"> ~
					<input name="phone_end" type="text" value="<?=$phone_if['end'];?>" class="no_string" style="text-align:center;" alt="time">
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
				<th class="head bold" colspan="2">상담내용</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center">내용</th>
				<td style="height:75px; padding:5px;"><textarea name="phone_cont" style="width:100%; height:100%; margin:0;"><?=stripslashes($phone_if['cont']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">결과</th>
				<td style="height:75px; padding:5px;"><textarea name="phone_result" style="width:100%; height:100%; margin:0;"><?=stripslashes($phone_if['result']);?></textarea></td>
			</tr>
			<tr>
				<th class="center">기타</th>
				<td style="height:75px; padding:5px;"><textarea name="phone_other" style="width:100%; height:100%; margin:0;"><?=stripslashes($phone_if['other']);?></textarea></td>
			</tr>
		</tbody>
	</table>
</div>

<input name="code" type="hidden" value="<?=$code;?>">
<input name="yymm" type="hidden" value="<?=$yymm;?>">
<input name="seq"  type="hidden" value="<?=$seq;?>">
<input name="phone_seq"  type="hidden" value="<?=$seq;?>">
<input name="phone_ssn"  type="hidden" value="<?=$ed->en($ssn);?>">

<?
	unset($phone_if);
	
	include_once("../inc/_db_close.php");
?>