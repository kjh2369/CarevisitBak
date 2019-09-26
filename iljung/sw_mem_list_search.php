<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$year = $_POST['year'];
	$month = $_POST['month'];

	$sql = 'SELECT	m02_yjumin AS jumin
			,		m02_yname AS name
			,		m02_jikwon_gbn AS gbn
			,		his.join_dt
			,		his.quit_dt
			FROM	m02yoyangsa
			INNER	JOIN	mem_his AS his
					ON		his.org_no = m02_ccode
					AND		his.jumin = m02_yjumin
					AND		DATE_FORMAT(his.join_dt,\'%Y%m\') <= \''.$year.$month.'\'
					AND		DATE_FORMAT(IFNULL(his.quit_dt,\'9999-12-31\'),\'%Y%m\') >= \''.$year.$month.'\'
			WHERE	m02_ccode = \''.$orgNo.'\'
			AND		m02_mkind = \'0\'
			AND		m02_jikwon_gbn IN (\'B\',\'C\',\'D\',\'W\')
			ORDER	BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><div class="left"><a href="#" onclick="lfSetMem('<?=$ed->en($row['jumin']);?>','<?=$row['name'];?>','<?=str_replace('-','',$row['join_dt']);?>','<?=str_replace('-','',$row['quit_dt']);?>'); return false;"><?=$row['name'];?></a></div></td>
			<td class="center"><?=$myF->dateStyle($row['join_dt'],'.');?></td>
			<td class="center"><?=$myF->dateStyle($row['quit_dt'],'.');?></td>
			<td class="last"></td>
		</tr><?
		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>