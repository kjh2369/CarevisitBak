<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_SESSION['userCenterCode'];
	$year = $_POST['year'];

	$sql = 'SELECT	DISTINCT
					his.jumin
			,		mst.m02_yname AS name
			FROM	mem_his AS his
			INNER	JOIN	m02yoyangsa AS mst
					ON		mst.m02_ccode	= his.org_no
					AND		mst.m02_mkind	= \'0\'
					AND		mst.m02_yjumin	= his.jumin
			WHERE org_no = \''.$orgNo.'\'
			AND LEFT(join_dt,4) <= \''.$year.'\'
			AND LEFT(IFNULL(quit_dt,\'9999\'),4) >= \''.$year.'\'
			ORDER BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="left"><?=$row['name'];?></td>
			<td class="left last">
				<a href="#" onclick="lfWorkSearch('1','<?=$ed->en($row['jumin']);?>'); return false;">계획</a>
				<a href="#" onclick="lfWorkSearch('2','<?=$ed->en($row['jumin']);?>'); return false;">실적</a>
			</td>
		</tr><?
		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>