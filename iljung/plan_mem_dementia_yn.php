<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$memCd = $_POST['memCd'];
	
	if (!is_numeric($memCd)) $memCd = $ed->de($memCd);

	

	//치매인지수료여부
	$sql = 'select dementia_yn
			from   mem_option
			where  org_no = \''.$code.'\'
			and    mo_jumin = \''.$memCd.'\'';

	$dementiaYn = $conn->get_data($sql); 
	
	$conn->row_free();

	

	echo $dementiaYn;

	include_once('../inc/_db_close.php');
?>