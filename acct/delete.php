<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$mode   = $_POST['mode'];
	$code   = $_POST['code'];
	$seq    = $_POST['seq'];

	if ($mode == '1'	||
		$mode == '11'	||
		$mode == '21'	||
		$mode == '51'	||
		$mode == '61'	||
		$mode == '62'	||
		$mode == '63'	||
		$mode == '64'){
		if (Empty($code)){
			echo 9;
			exit;
		}

		if ($mode == '1'){
			$table = 'sms_acct';
		}else if ($mode == '11'){
			$table = 'smart_acct';
		}else if ($mode == '21'){
			$table = 'bank_center';
		}else if ($mode == '51'){
			$table = 'mobile_acct';
		}else if ($mode == '61' || $mode == '63'){
			$table = 'tax_acct';
		}else if ($mode == '62'){
			$table = 'labor_acct';
		}else if ($mode == '64'){
			$table = 'fa_acct';
		}

		$sql = 'DELETE
				  FROM '.$table.'
				 WHERE org_no  = \''.$code.'\'
				   AND seq     = \''.$seq.'\'';

		if (!$conn->execute($sql)){
			 $conn->close();
			 echo 9;
			 exit;
		}

	}else if ($mode == '3_1' || $mode == '13_1'){
		if (Empty($code)){
			echo 9;
			exit;
		}

		if ($mode == '3_1'){
			$table = 'sms_deposit';
		}else if ($mode == '13_1'){
			$table = 'smart_deposit';
		}

		$conn->begin();

		$sql = 'DELETE
				  FROM '.$table.'
				 WHERE org_no  = \''.$code.'\'
				   AND seq     = \''.$seq.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();

	}else if ($mode == '41_1'){
		$date = $myF->dateStyle($_POST['date']);

		if (Empty($code)){
			echo 9;
			exit;
		}

		$conn->begin();

		$sql = 'DELETE
				  FROM center_deposit_'.$gDomainID.'
				 WHERE org_no = \''.$code.'\'
				   AND reg_dt = \''.$date.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();

	}else if ($mode == '2' || $mode == '12' || $mode == '32'){
		$year  = $_POST['year'];
		$month = $_POST['month'];
		$month = (IntVal($month) < 10 ? '0' : '').IntVal($month);

		if ($mode == '2'){
			$table = 'sms_acct_';
		}else if ($mode == '12'){
			$table = 'smart_acct_';
		}else if ($mode == '32'){
			$table = 'center_acct_';
		}else{
			echo 9;
			exit;
		}

		$conn->begin();

		$sql = 'DELETE
				  FROM '.$table.$year.$month;

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();

	}else if ($mode == '33'){
		$date = $_POST['date'];

		$sql = 'DELETE
				  FROM edu_acct
				 WHERE org_no = \''.$code.'\'
				   AND edu_dt = \''.$date.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

	}else if ($mode == '31'){
		//기관/지사연결 삭제
		$sql = 'DELETE
				  FROM b02center
				 WHERE b02_center = \''.$code.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();

	}else{
		echo 9;
		exit;
	}

	echo 1;

	include_once('../inc/_db_close.php');
?>