<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$area = $_POST['area'];

	$sql = 'SELECT	group_cd
			,		group_nm
			FROM	care_group
			WHERE	area_cd = \''.$area.'\'
			ORDER	BY show_seq, group_cd';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<option value="<?=$row['group_cd'];?>"><?=$row['group_nm'];?></option><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>