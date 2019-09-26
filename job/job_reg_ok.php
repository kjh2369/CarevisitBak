<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$check   = $_POST["check"];
	$code    = $_POST['code'];
	$job_cd  = $_POST['job_cd'];
	$job_nm  = $_POST['job_nm'];
	$job_seq = $_POST['job_seq'];

	$sql = "select ifnull(max(job_cd), 0) + 1
			  from job_kind
			 where org_no = '$code'";
	$next_code = $conn -> get_data($sql);

	$conn->begin();

	for($i=0; $i<sizeof($check); $i++){
		if(!empty($job_cd[$check[$i]])){
			$sql = 'update job_kind
					   set job_nm   = \''.$job_nm[$check[$i]].'\'
						 , job_seq  = \''.$job_seq[$check[$i]].'\'
					 where org_no   = \''.$code.'\'
					   and del_flag = \'N\'
					   and job_cd   = \''.$job_cd[$check[$i]].'\'';
			$conn -> execute($sql);
		}else{
			$sql = 'insert into job_kind values (
					 \''.$code.'\'
					,\''.$next_code.'\'
					,\''.$job_nm[$check[$i]].'\'
					,\''.$job_seq[$check[$i]].'\'
					,\'N\')';

			$conn -> execute($sql);
			$next_code++;
		}
	}

	$conn->commit();

	include_once("../inc/_db_close.php");

	echo '<script>';
	echo 'alert(\''.$myF->message('ok', 'N').'\');';
	echo 'location.replace(\'job_list.php\');';
	echo '</script>';
?>