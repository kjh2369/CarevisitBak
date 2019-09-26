<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$SR = $_POST['SR'];
	$date = $_POST['date'];
	$suga_cd = $_POST['suga_cd'];

	$sql = 'SELECT	CONCAT(suga_cd, suga_sub) AS suga_cd, suga_nm
			FROM	care_suga
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		suga_sr	 = \''.$SR.'\'
			AND		from_dt <= \''.$date.'\'
			AND		to_dt	>= \''.$date.'\'
			AND		LEFT(CONCAT(suga_cd, suga_sub), '.StrLen($suga_cd).') = \''.$suga_cd.'\'
			ORDER	BY suga_cd, suga_sub';
	
	//if($debug) echo nl2br($sql); exit;

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	//echo '{';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		echo $i > 0 ? "," : "";
		echo '{code:"'.$row['suga_cd'].'", name:"'.$row['suga_nm'].'"}';
	}

	//echo '}';

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>