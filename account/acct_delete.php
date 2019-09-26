<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$code   = $_SESSION['userCenterCode'];
	$type   = $_POST['type'];
	$today  = Date('Y-m-d');
	$userId = $_SESSION['userCode'];

	$conn->begin();

	if ($type == '2' || $type == '12'){
		//입금내역삭제
		$entDt = $_POST['entDt'];
		$seq   = $_POST['seq'];

		if ($type == '2'){
			$field = 'income';
		}else if ($type == '12'){
			$field = 'outgo';
		}else{
			echo 9;
			exit;
		}

		$sql = 'DELETE
				  FROM center_'.$field.'
				 WHERE org_no        = \''.$code.'\'
				   AND '.$field.'_ent_dt = \''.$entDt.'\'
				   AND '.$field.'_seq    = \''.$seq.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

	}else{
		echo 9;
		exit;
	}

	$conn->commit();

	echo 1;

	include_once('../inc/_db_close.php');
?>