<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	$code  = $_POST['code'];
	$jumin = $ed->de($_POST['jumin']);


	$conn->begin();
	$sql = 'update mem_salary
			   set del_flag  = \'Y\'
			,      update_id = \''.$_SESSION['userCode'].'\'
			,      update_dt = now()
			 where org_no    = \''.$code.'\'
			   and ms_jumin  = \''.$jumin.'\'
			   and del_flag  = \'N\'
			 order by ms_from_dt desc, ms_to_dt desc
			 limit 1';

	if (!$conn->execute($sql)){
		$conn->rollback();
		$conn->close();

		echo 'error';
		exit;
	}


	$conn->commit();
	echo 'ok';


	include_once('../inc/_db_close.php');
?>