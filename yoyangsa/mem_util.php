<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	//include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo $myF->header_script();

	/*
	$sql = 'select org_no as code
			,      mh_jumin as jumin
			  from mem_hourly
			 where mh_svc = \'12\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$code  = $row['code'];
		$jumin = $row['jumin'];

		$sql = 'select count(*)
				  from mem_salary
				 where org_no   = \''.$code.'\'
				   and ms_jumin = \''.$jumin.'\'';
		$liSalary = intval($conn->get_data($sql));

		if ($liSalary == 0){
			$sql = 'select count(*)
					  from mem_hourly
					 where org_no   = \''.$code.'\'
					   and mh_jumin = \''.$jumin.'\'
					   and mh_svc  != \'12\'';
			$liHourly = intval($conn->get_data($sql));

			if ($liHourly == 0){
				$sql = 'select family_yn
						  from mem_option
						 where org_no     = \''.$code.'\'
						   and mo_jumin   = \''.$jumin.'\'';
				$lsFamily = $conn->get_data($sql);

				if ($lsFamily != 'Y'){
					if (empty($lsFamily)){
						$sql = 'insert into mem_option (
								 org_no
								,mo_jumin
								,mo_extrapay_yn
								,mo_salary_yn
								,family_yn
								,insert_id
								,insert_dt) values (
								 \''.$code.'\'
								,\''.$jumin.'\'
								,\'N\'
								,\'Y\'
								,\'Y\'
								,\''.$_SESSION['userCode'].'\'
								,now())';
						$conn->execute($sql);
					}else{
						$sql = 'update mem_option
								   set family_yn      = \'Y\'
								 where org_no         = \''.$code.'\'
								   and mo_jumin       = \''.$jumin.'\'';

						$conn->execute($sql);
					}
				}
			}
		}
	}

	$conn->row_free();

	echo 'END';
	*/
	echo 'ERROR';

	include_once('../inc/_db_close.php');
?>