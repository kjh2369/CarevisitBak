<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo $myF->header_script();

	$check  = $_POST['check'];
	$job_cd = $_POST['job_cd'];
	$code   = $_POST['code'];

	$conn->begin();

	for($i=0; $i<sizeof($check); $i++){
		$sql = 'update job_kind
				   set del_flag = \'Y\'
				 where org_no   = \''.$code.'\'
				   and job_cd   = \''.$job_cd[$check[$i]].'\'';

		$conn->execute($sql);
	}

	$conn->commit();

	include_once("../inc/_db_close.php");

	echo '<script>';
	echo 'alert(\''.$myF->message('ok', 'N').'\');';
	echo 'location.replace(\'job_list.php\');';
	echo '</script>';
?>
