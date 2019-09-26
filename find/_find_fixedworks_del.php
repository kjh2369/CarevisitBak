<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	$code  = $_POST['code'];
	$jumin = $ed->de($_POST['jumin']);


	$conn->begin();
	$sql = 'update fixed_works
			   set del_flag  = \'Y\'
			,      update_id = \''.$_SESSION['userCode'].'\'
			,      update_dt = now()
			 where org_no    = \''.$code.'\'
			   and fw_jumin  = \''.$jumin.'\'
			   and del_flag  = \'N\'
			 order by fw_from_dt desc, fw_to_dt desc
			 limit 1';

	if (!$conn->execute($sql)){
		$conn->rollback();
		$conn->close();

		echo 'error';
		exit;
	}


	$sql = 'select ifnull(max(fw_seq),0)
			  from fixed_works
			 where org_no      = \''.$code.'\'
			   and fw_jumin    = \''.$jumin.'\'
			   and del_flag    = \'N\'';

	$seq = $conn->get_data($sql);


	$sql = 'update fixed_works
			   set fw_to_dt = \'999912\'
			 where org_no   = \''.$code.'\'
			   and fw_jumin = \''.$jumin.'\'
			   and fw_seq   = \''.$seq.'\'';

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