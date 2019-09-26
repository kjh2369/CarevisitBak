<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	$code = $_POST['code'];
	$yymm = $_POST['yymm'];
	$seq  = $_POST['seq'];
	$no   = $_POST['no'];


	$conn->begin();

	$sql = 'update calendar
			   set del_flag = \'Y\'
			 where org_no   = \''.$code.'\'
			   and cld_yymm = \''.$yymm.'\'
			   and cld_seq  = \''.$seq.'\'
			   and cld_no   = \''.$no.'\'';

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo 'error';
	}else{
		$conn->commit();
		echo 'ok';
	}


	include_once('../inc/_db_close.php');
?>