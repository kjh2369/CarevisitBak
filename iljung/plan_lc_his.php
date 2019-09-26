<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$jumin = $_POST['jumin'];
	$yymm  = $_POST['yymm'];
	$svc   = $_POST['svc'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	$sql = 'SELECT COUNT(*)
			  FROM longcare_his
			 WHERE org_no = \''.$code.'\'
			   AND jumin  = \''.$jumin.'\'
			   AND yymm   = \''.$yymm.'\'';
	$liCnt = $conn->get_data($sql);

	$lsCare = 'N';
	$lsBath = 'N';
	$lsNurs = 'N';

	switch($svc){
		case '001':
			$lsCare = 'Y';
			break;

		case '002':
			$lsBath = 'Y';
			break;

		case '003':
			$lsNurs = 'Y';
			break;
	}

	if ($liCnt == 0){
		$sql = 'INSERT INTO longcare_his (
				 org_no
				,jumin
				,yymm
				,care
				,bath
				,nurse) VALUES (
				 \''.$code.'\'
				,\''.$jumin.'\'
				,\''.$yymm.'\'
				,\''.$lsCare.'\'
				,\''.$lsBath.'\'
				,\''.$lsNurs.'\')';
	}else{
		$sql = 'UPDATE longcare_his
				   SET ';

		switch($svc){
			case '001':
				$sql .= ' care = \'Y\'';
				break;

			case '002':
				$sql .= ' bath = \'Y\'';
				break;

			case '003':
				$sql .= ' nurse = \'Y\'';
				break;
		}

		$sql .= ' WHERE org_no = \''.$code.'\'
					AND jumin  = \''.$jumin.'\'
					AND yymm   = \''.$yymm.'\'';
	}

	$conn->execute($sql);

	include_once('../inc/_db_close.php');
?>