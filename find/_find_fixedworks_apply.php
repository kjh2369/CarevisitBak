<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	$code   = $_POST['code'];
	$jumin  = $ed->de($_POST['jumin']);
	$hours  = $_POST['hours'];
	$hourly = str_replace(',','',$_POST['hourly']);
	$from   = str_replace('-','',$_POST['fromDT']);
	$to     = str_replace('-','',$_POST['toDT']);
	$last   = $myF->dateAdd('month', -1, $from.'01', 'Ym');

	if (empty($to)) $to = '999912';

	/*********************************************************
		贸澜 积己老
	*********************************************************/
	$sql = 'select min(fw_from_dt)
			  from fixed_works
			 where org_no      = \''.$code.'\'
			   and fw_jumin    = \''.$jumin.'\'
			   and del_flag    = \'N\'';

	$minDT = $conn->get_data($sql);


	/*********************************************************
		老磊 吝汗 眉农
	*********************************************************/
	$sql = 'select fw_from_dt
			  from fixed_works
			 where org_no      = \''.$code.'\'
			   and fw_jumin    = \''.$jumin.'\'
			   and fw_from_dt >= \''.$from.'\'
			   and del_flag    = \'N\'
			 order by fw_from_dt, fw_to_dt
			 limit 1';

	$chkDT = $conn->get_data($sql);

	if (empty($chkDT)){
		if ($from > $to) $to = '999912';

		$conn->begin();

		$sql = 'select ifnull(max(fw_seq),0)
				  from fixed_works
				 where org_no      = \''.$code.'\'
				   and fw_jumin    = \''.$jumin.'\'
				   and del_flag    = \'N\'';

		$seq = $conn->get_data($sql);


		$sql = 'update fixed_works
				   set fw_to_dt  = \''.$last.'\'
				,      update_id = \''.$_SESSION['userCode'].'\'
				,      update_dt = now()
				 where org_no    = \''.$code.'\'
				   and fw_jumin  = \''.$jumin.'\'
				   and fw_seq    = \''.$seq.'\'
				   and del_flag    = \'N\'';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->close();
			echo 'error';
			exit;
		}

		$sql = 'select ifnull(max(fw_seq),0) + 1
				  from fixed_works
				 where org_no      = \''.$code.'\'
				   and fw_jumin    = \''.$jumin.'\'';

		$seq = $conn->get_data($sql);

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
				,\''.$seq.'\'
				,\''.$from.'\'
				,\''.$to.'\'
				,\''.$hours.'\'
				,\''.$hourly.'\'
				,\''.$_SESSION['userCode'].'\'
				,now())';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->close();
			echo 'error';
			exit;
		}

		$conn->commit();
		echo 'ok';
	}else{
		if ($chkDT == $from){
			$conn->begin();

			$sql = 'select fw_seq
					  from fixed_works
					 where org_no     = \''.$code.'\'
					   and fw_jumin   = \''.$jumin.'\'
					   and fw_from_dt = \''.$from.'\'
					   and del_flag   = \'N\'';

			$seq = $conn->get_data($sql);


			$sql = 'update fixed_works
					   set fw_hours  = \''.$hours.'\'
					,      fw_hourly = \''.$hourly.'\'
					,      update_id = \''.$_SESSION['userCode'].'\'
					,      update_dt = now()
					 where org_no    = \''.$code.'\'
					   and fw_jumin  = \''.$jumin.'\'
					   and fw_seq    = \''.$seq.'\'
					   and del_flag    = \'N\'';

			if (!$conn->execute($sql)){
				$conn->rollback();
				$conn->close();
				echo 'error';
				exit;
			}

			$conn->commit();
			echo 'ok';
		}else if ($from < $minDT){
			$to = $myF->dateAdd('month', -1, $minDT.'01', 'Ym');

			$conn->begin();

			$sql = 'select ifnull(max(fw_seq),0) + 1
					  from fixed_works
					 where org_no      = \''.$code.'\'
					   and fw_jumin    = \''.$jumin.'\'';

			$seq = $conn->get_data($sql);

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
					,\''.$seq.'\'
					,\''.$from.'\'
					,\''.$to.'\'
					,\''.$hours.'\'
					,\''.$hourly.'\'
					,\''.$_SESSION['userCode'].'\'
					,now())';

			if (!$conn->execute($sql)){
				$conn->rollback();
				$conn->close();
				echo 'error';
				exit;
			}

			$conn->commit();
			echo 'ok';
		}else{
			echo 'date';
		}
	}


	include_once('../inc/_db_close.php');
?>