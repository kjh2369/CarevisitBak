<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$jumin = $_POST['jumin'] != '' ? $_POST['jumin'] : $_POST['ssn'];
	$regDt = $_POST['regDt'];

	if (!Is_Numeric($jumin)) $jumin = $ed->de($jumin);

	#if($debug) print_r($_POST);

	$sql = 'SELECT	reg_dt
			,		reg_tm
			,		reg_cd
			,		reg_nm
			,		yoy_cd
			,		yoy_nm
			,		stat
			,		take
			FROM	counsel_client_state
			WHERE	org_no = \''.$code.'\'
			AND		jumin  = \''.$jumin.'\'
			AND		reg_dt = \''.$regDt.'\'';

	$stat = $conn->get_array($sql);
	$stat['reg_tm'] = $myF->timeStyle($stat['reg_tm']);
?>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="150px">
		<col width="70px">
		<col width="150px">
		<col width="90px">
		<col width="150px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">작성일자</th>
			<td class="center">
				<div style="text-align:left;">
					<input id="statDt" name="statDt" type="text" value="<?=$stat['reg_dt'];?>" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this,'-','');" onBlur="__getDate(this);" onClick="_carlendar(this);" style="margin-right:0;">
					<input id="statTm" name="statTm" type="text" value="<?=$stat['reg_tm'];?>" class="no_string" alt="time" style="margin-left:0;">
					<input id="statBackDt" name="statBackDt" type="hidden" value="<?=$stat['reg_dt'];?>">
				</div>
			</td>
			<th class="center">기록자</th>
			<td class="left" style="padding-top:1px;">
				<span class="btn_pack find" onClick="__find_yoyangsa('<?=$code;?>','','statRegCd','statRegNm');"></span>
				<span id="statRegNm" style="height:100%; margin-left:5px; font-weight:bold;"><?=$stat['reg_nm'];?></span>
				<input name="statRegCd" type="hidden" value="<?=$ed->en($stat['reg_cd']);?>">
			</td>
			<th class="center">담당요양보호사</th>
			<td class="left last" style="padding-top:1px;">
				<span class="btn_pack find" onClick="__find_yoyangsa('<?=$code;?>','','statYoyCd','statYoyNm');"></span>
				<span id="statYoyNm" style="height:100%; margin-left:5px; font-weight:bold;"><?=$stat['yoy_nm'];?></span>
				<input name="statYoyCd" type="hidden" value="<?=$ed->en($stat['yoy_cd']);?>">
			</td>
			<td class="left last"></td>
		</tr>
		<tr>
			<th class="center"><?
				if ($code == ''){
					echo '상담내용';
				}else{
					echo '상태변화';
				}?>
			</th>
			<td class="center last" colspan="6"><textarea id="statText" name="statText" style="width:100%; height:70px;"><?=StripSlashes($stat['stat']);?></textarea></td>
		</tr>
		<tr>
			<th class="center">조치사항</th>
			<td class="center last" colspan="6"><textarea id="statTake" name="statTake" style="width:100%; height:70px;"><?=StripSlashes($stat['take']);?></textarea></td>
		</tr>
	</tbody>
</table>

<!--input name="code"  type="hidden" value="<?=$code;?>"-->
<input name="statSsn"  type="hidden" value="<?=$ed->en($jumin);?>">


<?
	include_once('../inc/_db_close.php');
?>