<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];

	$sql = 'SELECT	dept_cd, dept_nm
			FROM	dept
			WHERE	org_no	= \''.$orgNo.'\'
			AND		del_flag= \'N\'
			ORDER	BY order_seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr deptCd="<?=$row['dept_cd'];?>">
			<td class="left"><?=$row['dept_nm'];?></td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>