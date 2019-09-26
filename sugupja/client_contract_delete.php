<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once('../inc/_ed.php');
	
	$code = $_POST['code'];
	$kind = $_POST['kind'];
	$seq  = $_POST['seq'];
	$ssn  = $ed->de($_POST['ssn']);
	$svc_kind = $_POST['svc_kind'];
	

	$conn->begin();
	
	$sql = 'update client_contract
			   set del_flag   = \'Y\'
			 where org_no   = \''.$code.'\'
			   and svc_cd   = \''.$kind.'\'
			   and seq      = \''.$seq.'\'
			   and jumin    = \''.$ssn.'\'';
			   
	if ($conn->execute($sql)){
		$conn->commit();
		echo 'ok';
	}else{
		$conn->rollback();
		echo 'error';
	}
	
	

	include_once("../inc/_db_close.php");
?>