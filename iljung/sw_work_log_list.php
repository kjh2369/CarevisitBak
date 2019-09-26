<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $ed->de($_POST['jumin']);
	$year = $_POST['year'];
	$month = $_POST['month'];

	$sql = 'SELECT	date
			,		time
			,		reg_name
			,		yymm
			,		seq
			,		dan_yn
			FROM	sw_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$year.$month.'\'
			AND		del_flag= \'N\'
			ORDER	BY date, time';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$seq = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$seq;?>회</td>
			<td class="center"><?=$myF->dateStyle($row['date'],'.');?></td>
			<td class="center"><?=$myF->timeStyle($row['time']);?></td>
			<td class="left last" onclick="lfLogSel('<?=$row['yymm'];?>','<?=$row['seq'];?>','<?=$seq;?>');"><?
				if ($row['dan_yn'] == 'Y'){?>
					<div style="float:right; width:auto; color:BLUE; text-align:right; padding-top:4px; padding-right:2px; font-size:9px; line-height:1em;">주야간<br>보호</div><?
				}?>
				<div style="float:left; width:auto;"><a href="#" onclick="return false;"><?=$row['reg_name'];?></a></div>
			</td>
		</tr><?
		$seq ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>