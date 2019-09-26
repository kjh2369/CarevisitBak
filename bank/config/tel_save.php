<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if (!IsSet($_SESSION['USER_CODE'])){
		exit;
	}

	$cd  = $_POST['cd'];
	$tel = $_POST['tel'];
	$yymm  = $_POST['yymm'];
	$jumin = $ed->de($_POST['jumin']);
	$seq   = $_POST['seq'];
	$stat  = $_POST['stat'];
	$other = $_POST['other'];

	$sql = 'REPLACE INTO bank_config (
			 bank_cd
			,admin_tel) VALUES (
			 \''.$cd.'\'
			,\''.$tel.'\')';

	if (!$conn->execute($sql)){
		 $conn->close();
		 echo 9;
		 exit;
	}

	echo 1;

	include_once('../inc/_db_close.php');
?>