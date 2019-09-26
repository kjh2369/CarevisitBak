<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo		= $_POST['orgNo'];
	$seq        = $_POST['seq'];
	$counselDt	= str_replace('-','', $_POST['counselDt']);
	$counselCont= addslashes($_POST['counselCont']);
	$completeYn = $_POST['completeYn'];
	$cancelYn   = $_POST['cancelYn'] != '' ? $_POST['cancelYn'] : 'N';

	if($_POST['counselCont'] != '') $completeYn = 'Y';
	
	$sql = 'UPDATE medical_request
			   SET counsel_dt		= \''.$counselDt.'\'
			,	   counsel_cont		= \''.$counselCont.'\'
			,	   complete_yn		= \''.$completeYn.'\'
			,	   cancel_yn		= \''.$cancelYn.'\'
			 WHERE org_no			= \''.$orgNo.'\'
			   AND seq				= \''.$seq.'\'';



	$conn -> begin();

	if (!$conn->execute($sql)){ 
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn -> commit();

	echo 1; 

?>