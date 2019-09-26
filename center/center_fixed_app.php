<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	$code = $_POST['code'];
	$year = date('Y');
	$time = $_POST['time'];
	$pay  = str_replace(',','',$_POST['pay']);

	$conn->begin();

	$sql = 'select fw_jumin as jumin
			,      fw_seq as seq
			,      fw_from_dt as f_dt
			,      fw_to_dt as t_dt
			,      fw_hours as hours
			,      fw_hourly as hourly
			  from fixed_works
			 where org_no             = \''.$code.'\'
			   and left(fw_to_dt, 4) >= \''.$year.'\'
			   and del_flag           = \'N\'
			 order by jumin, f_dt, t_dt';


	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		#if ($row['hourly'] < $pay || $row['hours'] < $time){
			$jumin  = $row['jumin'];
			$seq    = $row['seq'];
			$fromDT = $row['f_dt'];
			$toDT   = $row['t_dt'];

			#if ($row['hourly'] < $pay) $newPay = $pay; else $newPay = $row['hourly'];
			#if ($row['hours'] < $time) $newTime = $time; else $newTime = $row['hours'];

			$newPay = $pay;
			$newTime = $time;


			if (substr($fromDT,0,4) < $year){
				/*********************************************************
					변경 후 새 내역을 작성
				*********************************************************/
				$oldToDT   = $myF->dateAdd('month',-1,$year.'0101','Ym');
				$newFromDT = $year.'01';

				$sql = 'update fixed_works
						   set fw_to_dt  = \''.$oldToDT.'\'
						,      update_id = \''.$_SESSION['userCode'].'\'
						,      update_dt = now()
						 where org_no    = \''.$code.'\'
						   and fw_jumin  = \''.$jumin.'\'
						   and fw_seq    = \''.$seq.'\'';

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 echo 'error';
					 exit;
				}


				/*********************************************************
					순번찾기
				*********************************************************/
				$sql = 'select ifnull(max(fw_seq), 0) + 1
						  from fixed_works
						 where org_no    = \''.$code.'\'
						   and fw_jumin  = \''.$jumin.'\'';

				$newSeq = $conn->get_data($sql);


				/*********************************************************
					새로운 내역 저장
				*********************************************************/
				$sql = 'insert into fixed_works (
						 org_no
						,fw_jumin
						,fw_seq
						,fw_from_dt
						,fw_to_dt
						,fw_hours
						,fw_hourly
						,insert_id
						,insert_dt) values (
						 \''.$code.'\'
						,\''.$jumin.'\'
						,\''.$newSeq.'\'
						,\''.$newFromDT.'\'
						,\''.$toDT.'\'
						,\''.$newTime.'\'
						,\''.$newPay.'\'
						,\''.$_SESSION['userCode'].'\'
						,now())';

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 echo 'error';
					 exit;
				}
			}else{
				$sql = 'select count(*)
						  from fixed_works
						 where org_no   = \''.$code.'\'
						   and fw_jumin = \''.$jumin.'\'';

				if ($conn->get_data($sql) == 0){
					/*********************************************************
						새로운 내역 저장
					*********************************************************/
					$sql = 'insert into fixed_works (
							 org_no
							,fw_jumin
							,fw_seq
							,fw_from_dt
							,fw_to_dt
							,fw_hours
							,fw_hourly
							,insert_id
							,insert_dt) values (
							 \''.$code.'\'
							,\''.$jumin.'\'
							,\'1\'
							,\''.$year.'01\'
							,\'999912\'
							,\''.$newTime.'\'
							,\''.$newPay.'\'
							,\''.$_SESSION['userCode'].'\'
							,now())';

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->close();
						 echo 'error';
						 exit;
					}
				}else{
					/*********************************************************
						내역만 변경
					*********************************************************/
					$sql = 'update fixed_works
							   set fw_hours  = \''.$newTime.'\'
							,      fw_hourly = \''.$newPay.'\'
							,      update_id = \''.$_SESSION['userCode'].'\'
							,      update_dt = now()
							 where org_no    = \''.$code.'\'
							   and fw_jumin  = \''.$jumin.'\'
							   and fw_seq    = \''.$seq.'\'';

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->close();
						 echo 'error';
						 exit;
					}
				}
			}
		#}
	}

	$conn->row_free();
	$conn->commit();

	echo 'ok';

	include_once('../inc/_db_close.php');
?>