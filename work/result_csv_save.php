<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_referer.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_function.php');

	$code = $_POST['code'];
	$kind = '0';
	$file = $_POST['file'];
	$list = $_POST['check'];
	$cnt  = sizeof($list);

	$conn->mode = 1;

	$conn->begin();

	for($i=0; $i<$cnt; $i++){
		$id     = $_POST['index_'.$list[$i]];
		$client = $ed->de($_POST['client'][$id]);
		$member = $ed->de($_POST['member'][$id]);

		$svc = $_POST['svc_code'][$id];

		$plan_dt   = ereg_replace('[^0-9]', '', $_POST['plan_date'][$id]);
		$plan_ft   = $myF->timeStyle($_POST['plan_from'][$id]);
		$plan_tt   = $myF->timeStyle($_POST['plan_to'][$id]);
		$plan_seq  = $_POST['plan_seq'][$id];
		$plan_proc = $_POST['plan_time'][$id];

		$csv_dt   = ereg_replace('[^0-9]', '', $_POST['work_date'][$id]);
		$csv_ft   = $myF->timeStyle($_POST['work_from'][$id]);
		$csv_tt   = $myF->timeStyle($_POST['work_to'][$id]);
		$csv_proc = $_POST['work_time'][$id];

		$work_dt   = ereg_replace('[^0-9]', '', $_POST['conf_date'][$id]);
		$work_ft   = $_POST['conf_from'][$id];
		$work_tt   = $_POST['conf_to'][$id];
		$work_proc = $_POST['conf_time'][$id];

		$suga_code  = $_POST['suga_code'][$id];
		$suga_price = $_POST['suga_price'][$id];

		$sql = "insert into csv_plan (
				 org_no
				,plan_client
				,plan_member
				,plan_svc_cd
				,plan_dt
				,plan_from
				,plan_to
				,plan_seq
				,plan_proc_time
				,insert_dt) values (
				 '$code'
				,'$client'
				,'$member'
				,'$svc'
				,'$plan_dt'
				,'$plan_ft:00'
				,'$plan_tt:00'
				,'$plan_seq'
				,'$plan_proc'
				,now())";
		$conn->execute($sql);

		$sql = "insert into csv_work (
				 org_no
				,plan_client
				,plan_dt
				,plan_from
				,plan_seq
				,work_member
				,work_svc_cd
				,work_dt
				,work_from
				,wrok_to
				,work_proc_time
				,insert_dt) values (
				 '$code'
				,'$client'
				,'$plan_dt'
				,'$plan_ft:00'
				,'$plan_seq'
				,'$member'
				,'$svc'
				,'$csv_dt'
				,'$csv_ft:00'
				,'$csv_tt:00'
				,'$csv_proc'
				,now())";
		$conn->execute($sql);

		$conf_dt   = $work_dt;
		$conf_ft   = $myF->time2min($work_ft);
		$conf_tt   = $myF->time2min($work_tt);
		$conf_proc = $conf_tt - $conf_ft;
		$conf_proc = $myF->cutOff($conf_proc, 30);
		$tmp_proc  = ($conf_tt - $conf_ft) - $conf_proc;

		if ($tmp_proc < 0) $tmp_proc = 0;

		$conf_tt = $conf_tt - $tmp_proc;
		$conf_ft = ereg_replace('[^0-9]', '', $myF->min2time($conf_ft));
		$conf_tt = ereg_replace('[^0-9]', '', $myF->min2time($conf_tt));

		$csv_ft = ereg_replace('[^0-9]', '', $csv_ft);
		$csv_tt = ereg_replace('[^0-9]', '', $csv_tt);

		$plan_ft = ereg_replace('[^0-9]', '', $plan_ft);
		$plan_tt = ereg_replace('[^0-9]', '', $plan_tt);

		if ($suga_price > 0){
			$sql = "update t01iljung
					   set t01_wrk_date   = '$csv_dt'
					,      t01_wrk_fmtime = '$csv_ft'
					,      t01_wrk_totime = '$csv_tt'

					,      t01_conf_date       = '$conf_dt'
					,      t01_conf_fmtime     = '$conf_ft'
					,      t01_conf_totime     = '$conf_tt'
					,      t01_conf_soyotime   = '$conf_proc'
					,      t01_conf_suga_code  = '$suga_code'
					,      t01_conf_suga_value = '$suga_price'

					,      t01_status_gbn = '1'
					,      t01_modify_yn  = 'F'

					 where t01_ccode        = '$code'
					   and t01_mkind        = '$kind'
					   and t01_jumin        = '$client'
					   and t01_yoyangsa_id1 = '$member'
					   and t01_sugup_date   = '$plan_dt'
					   and t01_sugup_fmtime = '$plan_ft'
					   and t01_sugup_seq    = '$plan_seq'";

			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
		}
	}

	$conn->commit();

	include_once('../inc/_db_close.php');

	if (is_file($file)){
		unlink($file);
	}

	echo $myF->header_script();
	echo '<script>';
	echo 'alert(\''.$myF->message('ok', 'N').'\');';

	if ($conn->mode == 1)
		echo 'self.close();';

	echo '</script>';
?>