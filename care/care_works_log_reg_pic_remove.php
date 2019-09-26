<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['svcCd'];
	$date	= $_POST['date'];
	$jumin	= $ed->de($_POST['jumin']);
	$suga	= $_POST['suga'];
	$res	= $_POST['resource'];
	$mem	= $ed->de($_POST['mem']);

	$sql = 'SELECT	file_path
			FROM	care_works_log
			WHERE	org_no		= \''.$orgNo.'\'
			AND		org_type	= \''.$SR.'\'
			AND		date		= \''.$date.'\'
			AND		jumin		= \''.$jumin.'\'
			AND		suga_cd		= \''.$suga.'\'
			AND		resource_cd	= \''.$res.'\'
			AND		mem_cd		= \''.$mem.'\'';

	$filePath = $conn->get_data($sql);

	if ($filePath){
		$sql = 'UPDATE	care_works_log
				SET		pic_nm		= \'\'
				,		file_path	= \'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$SR.'\'
				AND		date		= \''.$date.'\'
				AND		jumin		= \''.$jumin.'\'
				AND		suga_cd		= \''.$suga.'\'
				AND		resource_cd	= \''.$res.'\'
				AND		mem_cd		= \''.$mem.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $ocnn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();
		@unlink($filePath);
	}

	echo 1;

	include_once('../inc/_db_close.php');
?>