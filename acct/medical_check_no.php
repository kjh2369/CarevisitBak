<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	
	$conn -> close();	//케어비지트 닫기
	###############################################


	//방문간호 디비 접속
	$conn -> user = "nhcs";
	$conn -> pass = "nhcs9482";
	$conn -> db_name = "nhcs";
	$conn -> connection();

	$type = $_POST['type'];
	$code = $_POST['medical_org_no'];
	
	//기관기호 존재여부
	$sql = 'SELECT	COUNT(*)
			FROM	medical_org
			WHERE	medical_org_no = \''.$code.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		echo 'N';
	}else{
		echo 'Y';
	}


	include_once('../inc/_db_close.php');
?>