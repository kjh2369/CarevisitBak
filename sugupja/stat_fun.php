<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$jumin = $_POST['jumin'];
	$regDt = $_POST['regDt'];
	$type  = $_POST['type'];

	if (!Is_Numeric($jumin)) $jumin = $ed->de($jumin);

	switch($type){
		case 'DELETE':
			$sql = 'DELETE
					  FROM counsel_client_state
					 WHERE org_no = \''.$code.'\'
					   AND jumin  = \''.$jumin.'\'
					   AND reg_dt = \''.$regDt.'\'';
			if ($conn->execute($sql)){
				echo 1;
			}else{
				echo 9;
			}

			break;
	}

	include_once('../inc/_db_close.php');
?>