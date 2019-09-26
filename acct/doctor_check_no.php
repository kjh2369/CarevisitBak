<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_nhcs_db.php');
	
	

	$licenceNo = $_POST['doctor_licence_no'];
	
	//기관기호 존재여부
	$sql = 'SELECT	COUNT(*)
			FROM	doctor
			WHERE	doctor_licence_no = \''.$licenceNo.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		echo 'N';
	}else{
		echo 'Y';
	}


	include_once('../inc/_db_close.php');
?>