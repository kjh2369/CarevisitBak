<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['svcCd'];
	$date	= $_POST['date'];
	$time	= $_POST['time'];
	$seq	= $_POST['seq'];
	$jumin	= $ed->de($_POST['jumin']);
	$suga	= $_POST['suga'];
	$res	= $_POST['res'];
	$mem	= $ed->de($_POST['mem']);
	$origin	= $_POST['origin'];

	if ($origin == 'NEW'){
		$sql = 'DELETE
				FROM	care_works_log
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$SR.'\'
				AND		date		= \''.$date.'\'
				AND		jumin		= \''.$jumin.'\'
				AND		suga_cd		= \''.$suga.'\'
				AND		resource_cd	= \''.$res.'\'
				AND		mem_cd		= \''.$mem.'\'';
	}else if ($origin == 'OLD'){
		$sql = 'UPDATE	care_result
				SET		del_flag= \'Y\'
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		date	= \''.$date.'\'
				AND		time	= \''.$time.'\'
				AND		seq		= \''.$seq.'\'
				AND		del_flag= \'N\'';
	}else{
		$conn->close();
		echo 1;
		exit;
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>