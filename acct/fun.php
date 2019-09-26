<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$mode	= $_POST['mode'];
	$code	= $_POST['code'];
	$seq	= $_POST['seq'];

	if (Empty($code)){
		echo 9;
		exit;
	}

	if ($mode == '1'){
		$sql = 'DELETE
				  FROM sms_acct
				 WHERE org_no  = \''.$code.'\'
				   AND seq     = \''.$seq.'\'';

		if (!$conn->execute($sql)){
			 $conn->close();
			 echo 9;
			 exit;
		}

	}else{
		echo 9;
		exit;
	}

	echo 1;

	include_once('../inc/_db_close.php');
?>