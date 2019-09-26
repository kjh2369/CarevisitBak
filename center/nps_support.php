<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$type = $_POST['type'];
	$year = $_POST['year'];

	if ($type == 'FIND'){
		$sql = 'SELECT	yymm
				,		support_yn AS nps_yn
				,		emp_yn, health_yn, per5_flag
				FROM	center_ins_support
				WHERE	org_no = \''.$code.'\'
				AND		LEFT(yymm,4) = \''.$year.'\'
				AND		ins_type = \'A\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$mon = IntVal(SubStr($row['yymm'],4,2));

			if ($data) $data .= '&';

			$data .= 'nps'.$mon.'='.$row['nps_yn'].'&emp'.$mon.'='.$row['emp_yn'].'&health'.$mon.'='.$row['health_yn'].'&per'.$mon.'='.$row['per5_flag'];
		}

		$conn->row_free();

		echo $data;

	}else if ($type == 'APPLY'){
		for($i=1; $i<=12; $i++){
			$sptYn = $_POST['nps'.$i];
			$empYn = $_POST['emp'.$i];
			$healthYn = $_POST['health'.$i];
			$per5Yn = $_POST['under'.$i];
			$yymm  = $year.($i < 10 ? '0' : '').$i;

			$sql = 'REPLACE INTO center_ins_support (
					 org_no
					,yymm
					,ins_type
					,support_yn
					,emp_yn
					,health_yn
					,new_flag
					,per5_flag) VALUES (
					 \''.$code.'\'
					,\''.$yymm.'\'
					,\'A\'
					,\''.$sptYn.'\'
					,\''.$empYn.'\'
					,\''.$healthYn.'\'
					,\''.($year >= '2018' ? $_POST['newYn'] : 'N').'\'
					,\''.$per5Yn.'\'
					)';

			$conn->execute($sql);
		}

		$sql = 'SELECT	COUNT(*)
				FROM	center_ins_support
				WHERE	org_no	= \''.$code.'\'
				AND		yymm	< \'201601\'
				AND		CASE WHEN support_yn = \'Y\' THEN 1
							 WHEN emp_yn = \'Y\' THEN 1 ELSE 0 END = 1';

		$newYn = $conn->get_data($sql);

		if ($newYn > 0){
			$newYn = 'N';
		}else{
			$newYn = 'Y';
		}

		if ($newYn == 'Y' && $year >= 2016){
			$newYn = $_POST['newYn'];
		}
		
		
		$sql = 'REPLACE INTO center_ins_spt_option (org_no, new_yn) VALUES (\''.$code.'\', \''.$newYn.'\')';
		$conn->execute($sql);
	}

	include_once('../inc/_db_close.php');
?>