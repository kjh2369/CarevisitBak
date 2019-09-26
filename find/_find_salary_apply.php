<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	$code    = $_POST['code'];
	$jumin   = $ed->de($_POST['jumin']);
	$pay     = str_replace(',','',$_POST['pay']);
	$careYN  = $_POST['careYN'];
	$extraYN = $_POST['extraYN'];
	$day20YN = $_POST['day20YN'];
	$dealPay = str_replace(',','',$_POST['dealPay']);
	$from    = str_replace('-','',$_POST['fromDT']);
	$to      = str_replace('-','',$_POST['toDT']);

	if ($day20YN != 'Y') $day20YN = 'N';


	if (empty($code)){
		$conn->close();
		echo 'ok';
		return;
	}


	/*********************************************************
		org_no,ms_jumin,ms_seq,ms_salary,ms_extra_yn,ms_care_yn,ms_from_dt,ms_to_dt,del_flag,insert_id,insert_dt,update_id,update_dt
	*********************************************************/

	/*********************************************************
		일자 중복 체크
	*********************************************************/
	$sql = 'select ms_from_dt
			  from mem_salary
			 where org_no      = \''.$code.'\'
			   and ms_jumin    = \''.$jumin.'\'
			   and ms_from_dt <= \''.$from.'\'
			   and ms_to_dt   >= \''.$from.'\'
			   and del_flag    = \'N\'
			 order by ms_from_dt desc, ms_to_dt desc
			 limit 1';

	$chkFromDT = $conn->get_data($sql);

	$sql = 'select ms_to_dt
			  from mem_salary
			 where org_no      = \''.$code.'\'
			   and ms_jumin    = \''.$jumin.'\'
			   and ms_from_dt <= \''.$to.'\'
			   and ms_to_dt   >= \''.$to.'\'
			   and del_flag    = \'N\'
			 order by ms_from_dt desc, ms_to_dt desc
			 limit 1';

	$chkToDT = $conn->get_data($sql);

	if (empty($chkFromDT) && empty($chkToDT)){
		$conn->begin();

		$sql = 'select ifnull(max(ms_seq),0) + 1
				  from mem_salary
				 where org_no   = \''.$code.'\'
				   and ms_jumin = \''.$jumin.'\'';

		$seq = $conn->get_data($sql);

		$sql = 'insert into mem_salary (
				 org_no
				,ms_jumin
				,ms_seq
				,ms_salary
				,ms_extra_yn
				,ms_care_yn
				,ms_20day_yn
				,ms_dealpay
				,ms_from_dt
				,ms_to_dt
				,insert_id
				,insert_dt) values (
				 \''.$code.'\'
				,\''.$jumin.'\'
				,\''.$seq.'\'
				,\''.$pay.'\'
				,\''.$extraYN.'\'
				,\''.$careYN.'\'
				,\''.$day20YN.'\'
				,\''.$dealPay.'\'
				,\''.$from.'\'
				,\''.$to.'\'
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
		if ($chkFromDT == $from){
			$conn->begin();

			$sql = 'select ifnull(max(ms_seq),0)
					  from mem_salary
					 where org_no     = \''.$code.'\'
					   and ms_jumin   = \''.$jumin.'\'
					   and ms_from_dt = \''.$from.'\'
					   and del_flag   = \'N\'';

			$seq = $conn->get_data($sql);



			$sql = 'update mem_salary
					   set ms_salary   = \''.$pay.'\'
					,      ms_extra_yn = \''.$extraYN.'\'
					,      ms_care_yn  = \''.$careYN.'\'
					,      ms_20day_yn = \''.$day20YN.'\'
					,		ms_dealpay	= \''.$dealPay.'\'
					,      ms_to_dt    = \''.$to.'\'
					,      update_id   = \''.$_SESSION['userCode'].'\'
					,      update_dt   = now()
					 where org_no      = \''.$code.'\'
					   and ms_jumin    = \''.$jumin.'\'
					   and ms_seq      = \''.$seq.'\'
					   and del_flag    = \'N\'';

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