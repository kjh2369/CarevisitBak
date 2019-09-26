<?
	include_once('../../inc/_db_open.php');
	include_once('../../inc/_login.php');
	include_once('../../inc/_myFun.php');
	include_once('../../inc/_ed.php');

	$mode = $_POST['mode'];
	$code = $_SESSION['userCenterCode'];

	if ($mode == '101'){
		$list = Explode(Chr(1),$_POST['data']);

		$conn->begin();

		foreach($list as $row){
			if (Empty($row)){
				break;
			}

			$col = Explode(Chr(2),$row);

			$sql = 'UPDATE mem_his
					   SET work_start_dt = \''.$myF->dateStyle($col[2]).'\'
					 WHERE org_no = \''.$code.'\'
					   AND jumin  = \''.Str_Replace('-','',$col[1]).'\'
					   AND seq    = \''.$col[0].'\'';

			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}
		}

		$conn->commit();
		echo 1;

	}else{
		$conn->close();
		echo 9;
		exit;
	}

	include_once('../../inc/_db_close.php');
?>