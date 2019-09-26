<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$code		= $_POST['code'];
	$year		= $_POST['year'];
	$mode		= $_POST['mode'];
	$month		= $_POST['month'];
	$month      = (intval($month) < 10 ? '0' : '').intval($month);
	$cancel_dt	= $_POST['cancel_dt'];

	// 배치시작시간기록
	include('../inc/_batch_var.php');

	$conn->begin();

	if ($mode == 1){
		$sql = "delete
				  from t13sugupja
				 where t13_ccode    = '$code'
				   and t13_pay_date = '$year$month'";

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->batch_log('3', $year.$month, $batch_start_sec, $start_dt, $start_tm, '실적일괄확정 취소중 오류발생');
			 echo $myF->message('error', 'Y', 'Y');
			 exit;
		}

		$sql = "delete
				  from t15paymentissu
				 where t15_ccode    = '$code'
				   and t15_pay_date = '$year$month'";

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->batch_log('3', $year.$month, $batch_start_sec, $start_dt, $start_tm, '실적일괄확정 취소중 오류발생');
			 echo $myF->message('error', 'Y', 'Y');
			 exit;
		}

		$sql = "update closing_progress
				   set act_bat_conf_flag = 'N'
				,      act_bat_can_flag  = 'Y'
				,      act_bat_can_dt    = '$cancel_dt'
				,      act_cls_flag      = 'N'
				 where org_no            = '$code'
				   and closing_yymm      = '$year$month'";

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->batch_log('3', $year.$month, $batch_start_sec, $start_dt, $start_tm, '실적일괄확정 취소중 오류발생');
			 echo $myF->message('error', 'Y', 'Y');
			 exit;
		}

		//로그내역 복귀
		$sql = 'SELECT *
				  FROM plan_conf_his
				 WHERE org_no       = \''.$code.'\'
				   AND LEFT(date,6) = \''.$year.$month.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			//일정확정 데이타 수정
			$sql = 'UPDATE t01iljung
					   SET t01_conf_fmtime     = \''.$row['conf_from'].'\'
					,      t01_conf_totime     = \''.$row['conf_to'].'\'
					,      t01_conf_soyotime   = \''.$row['conf_time'].'\'
					,      t01_conf_suga_code  = \''.$row['conf_suga'].'\'
					,      t01_conf_suga_value = \''.$row['conf_value'].'\'
					 WHERE t01_ccode = \''.$row['org_no'].'\'
					   AND t01_mkind = \''.$row['svc_kind'].'\'
					   AND t01_jumin = \''.$row['jumin'].'\'
					   AND t01_sugup_date   = \''.$row['date'].'\'
					   AND t01_sugup_fmtime = \''.$row['time'].'\'
					   AND t01_sugup_seq    = \''.$row['seq'].'\'';

			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->batch_log('3', $year.$month, $batch_start_sec, $start_dt, $start_tm, '실적일괄확정 취소중 오류발생');
				 echo $myF->message('error', 'Y', 'Y');
				 exit;
			}
		}

		$conn->row_free();

		$conn->batch_log('3', $year.$month, $batch_start_sec, $start_dt, $start_tm, '실적일괄확정 취소 성공');
	}else if ($mode == 2){
		// 기존데이타 삭제
		if (!$conn->execute("delete from salary_basic where org_no = '$code' and salary_yymm = '$year$month'")){
			$conn->rollback();
			$conn->batch_log('4', $year.$month, $batch_start_sec, $start_dt, $start_tm, '급여자동계산 취소중 오류발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}
		if (!$conn->execute("delete from salary_bn where org_no = '$code' and salary_yymm = '$year$month'")){
			$conn->rollback();
			$conn->batch_log('4', $year.$month, $batch_start_sec, $start_dt, $start_tm, '급여자동계산 취소중 오류발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}
		if (!$conn->execute("delete from salary_amt where org_no = '$code' and salary_yymm = '$year$month'")){
			$conn->rollback();
			$conn->batch_log('4', $year.$month, $batch_start_sec, $start_dt, $start_tm, '급여자동계산 취소중 오류발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}
		if (!$conn->execute("delete from salary_hourly where org_no = '$code' and salary_yymm = '$year$month'")){
			$conn->rollback();
			$conn->batch_log('4', $year.$month, $batch_start_sec, $start_dt, $start_tm, '급여자동계산 취소중 오류발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}
		if (!$conn->execute("delete from salary_monthly where org_no = '$code' and salary_yymm = '$year$month'")){
			$conn->rollback();
			$conn->batch_log('4', $year.$month, $batch_start_sec, $start_dt, $start_tm, '급여자동계산 취소중 오류발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}
		if (!$conn->execute("delete from salary_rate where org_no = '$code' and salary_yymm = '$year$month'")){
			$conn->rollback();
			$conn->batch_log('4', $year.$month, $batch_start_sec, $start_dt, $start_tm, '급여자동계산 취소중 오류발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}
		if (!$conn->execute("delete from salary_addon_pay where org_no = '$code' and salary_yymm = '$year$month'")){
			$conn->rollback();
			$conn->batch_log('4', $year.$month, $batch_start_sec, $start_dt, $start_tm, '급여자동계산 취소중 오류발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}
		if (!$conn->execute("delete from salary_detail where org_no = '$code' and salary_yymm = '$year$month'")){
			$conn->rollback();
			$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_center_amt 입력위한 삭제중 오류발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}

		$sql = "update closing_progress
				   set salary_bat_calc_flag = 'N'
				,      salary_bat_can_flag  = 'Y'
				,      salary_bat_can_dt    = '$cancel_dt'
				 where org_no            = '$code'
				   and closing_yymm      = '$year$month'";

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->batch_log('4', $year.$month, $batch_start_sec, $start_dt, $start_tm, '급여자동계산 취소중 오류발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}

		$conn->batch_log('4', $year.$month, $batch_start_sec, $start_dt, $start_tm, '급여자동계산 취소 성공');
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
<script language='javascript'>
	alert("<?=$myF->message('ok','N');?>");
	location.replace("result_finish_confirm_cancel.php?mode=<?=$mode;?>&year=<?=$year;?>&month=<?=intval($month);?>");
</script>