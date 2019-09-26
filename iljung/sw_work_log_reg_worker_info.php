<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$memCd	= $ed->de($_POST['memCd']);
	$date	= str_replace('-','',$_POST['date']);

	$sql = 'SELECT	a.jumin
			,		m03_name AS name
			,		a.time
			,		a.to_time
			FROM	sw_log AS a
			INNER	JOIN	m03sugupja
					ON		m03_ccode = a.org_no
					AND		m03_mkind = \'0\'
					AND		m03_jumin = a.jumin
			WHERE	a.org_no	 = \''.$orgNo.'\'
			AND		a.jumin		!= \''.$jumin.'\'
			AND		a.reg_jumin  = \''.$memCd.'\'
			AND		a.date		 = \''.$date.'\'
			AND		a.del_flag	 = \'N\'
			ORDER	BY time,to_time';
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if (!$row['to_time']) $row['to_time'] = str_replace(':','',$myF->min2time($myF->time2min($row['time']) + 15));?>

		<div id="ID_DIV_WK_<?=$i;?>" from="<?=$row['time'];?>" jumin="<?=$ed->en($row['jumin']);?>" to="<?=$row['to_time'];?>" gbn="1" onclick="lfTimerNothing();" style="clear:both; cursor:default; border:1px solid #CCCCCC; <?=$i > 0 ? 'border-top:none;' : '';?>">
			<div style="text-align:left; padding:3px 0 2px 5px;"><?=$row['name'];?></div>
			<div style="text-align:right; padding:2px 5px 3px 0;"><?=$myF->timeStyle($row['time']);?>~<?=$myF->timeStyle($row['to_time']);?></div>
		</div><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>