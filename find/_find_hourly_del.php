<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	$code  = $_POST['code'];
	$jumin = $ed->de($_POST['jumin']);
	$svcID = $_POST['svcID'];
	$seq   = $_POST['seq'];


	$conn->begin();


	//삭제
	$sql = 'update mem_hourly
			   set del_flag = \'Y\'
			,      update_id = \''.$_SESSION['userCode'].'\'
			,      update_dt = now()
			 where org_no    = \''.$code.'\'
			   and mh_jumin  = \''.$jumin.'\'
			   and mh_svc    = \''.$svcID.'\'
			   and mh_seq    = \''.$seq.'\'
			   and del_flag  = \'N\'';

	if (!$conn->execute($sql)){
		$conn->rollback();
		$conn->close();
		echo 'error';
		exit;
	}


	//최근데이타
	$sql = 'select mh_seq
			  from mem_hourly
			 where org_no   = \''.$code.'\'
			   and mh_jumin = \''.$jumin.'\'
			   and mh_svc   = \''.$svcID.'\'
			   and del_flag = \'N\'
			 order by mh_from_dt desc, mh_to_dt desc';

	$seq = $conn->get_data($sql);

	if (!empty($seq)){
		$sql = 'update mem_hourly
				   set mh_to_dt = \'999912\'
				 where org_no   = \''.$code.'\'
				   and mh_jumin = \''.$jumin.'\'
				   and mh_svc   = \''.$svcID.'\'
				   and mh_seq   = \''.$seq.'\'';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->close();

			echo 'error';
			exit;
		}
	}

	$conn->commit();
	echo 'ok';


	include_once('../inc/_db_close.php');
?>