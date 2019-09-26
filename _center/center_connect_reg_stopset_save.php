<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$seq	= $_POST['seq'];
	$stopGbn= $_POST['gbn'];
	$stopDt	= str_replace('-','',$_POST['stopDt']);
	$defTxt	= $_POST['defTxt'];
	$defAmt	= str_replace(',','',$_POST['defAmt']);
	$clsDt	= str_replace('-','',$_POST['clsDt']);
	$clsYn	= $_POST['clsYn'];
	$memo	= $_POST['memo'];
	$other	= str_replace(chr(13), '', str_replace(chr(10), '\N', (addslashes($_POST['other']))));
	$today	= Date('Ymd');

	if ($clsYn == 'D'){
		//삭제
		$sql = 'DELETE
				FROM	stop_set
				WHERE	org_no	= \''.$orgNo.'\'
				AND		seq		= \''.$seq.'\'';

		$query[] = $sql;
	}else{
		//기관 종료일
		$sql = 'SELECT	to_dt, rs_cd, rs_dtl_cd
				FROM	cv_reg_info
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		from_dt	<= \''.$today.'\'
				AND		to_dt	>= \''.$today.'\'';
		$row = $conn->get_array($sql);

		$toDt = $row['to_dt'];
		$rsCd = $row['rs_cd'];
		$rsDtlCd = $row['rs_dtl_cd'];

		Unset($row);

		if ($seq){
			if ($clsYn == 'M'){
				$sql = 'UPDATE	stop_set
						SET		cls_yn	= \'N\'
						,		stop_gbn= \''.$stopGbn.'\'
						,		stop_dt	= \''.$stopDt.'\'
						,		close_dt= \''.$clsDt.'\'
						,		def_txt	= \''.$defTxt.'\'
						,		def_amt	= \''.$defAmt.'\'
						,		memo	= \''.$memo.'\'
						,		other	= \''.$other.'\'
						WHERE	org_no	= \''.$orgNo.'\'
						AND		seq		= \''.$seq.'\'';
			}else{
				$sql = 'UPDATE	stop_set
						SET		cls_yn	= \''.$clsYn.'\'';

				if ($memo) $sql .= ', memo = \''.$memo.'\'';
				if ($other) $sql .= ', other = \''.$other.'\'';

				$sql .= ',		update_id = \''.$_SESSION['userCode'].'\'
						,		update_dt = NOW()
						WHERE	org_no	= \''.$orgNo.'\'
						AND		seq		= \''.$seq.'\'';
			}
		}else{
			$sql = 'SELECT	IFNULL(MAX(seq),0)+1
					FROM	stop_set
					WHERE	org_no	= \''.$orgNo.'\'';
			$seq = $conn->get_data($sql);

			$sql = 'INSERT INTO stop_set (org_no,seq,stop_gbn,stop_dt,close_dt,def_txt,def_amt,org_to_dt,org_rs_cd,org_rs_dtl_cd,memo,other,insert_id,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$seq.'\'
					,\''.$stopGbn.'\'
					,\''.$stopDt.'\'
					,\''.$clsDt.'\'
					,\''.$defTxt.'\'
					,\''.$defAmt.'\'
					,\''.$toDt.'\'
					,\''.$rsCd.'\'
					,\''.$rsDtlCd.'\'
					,\''.$memo.'\'
					,\''.$other.'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';
		}
		$query[] = $sql;

		if ($clsYn == 'Y'){
			//복원
			$sql = 'SELECT	org_to_dt, org_rs_cd, org_rs_dtl_cd, close_dt
					FROM	stop_set
					WHERE	org_no	= \''.$orgNo.'\'
					AND		seq		= \''.$seq.'\'';
			$row = $conn->get_array($sql);

			$toDt = $row['org_to_dt'];
			$rsCd = $row['org_rs_cd'];
			$rsDtlCd = $row['org_rs_dtl_cd'];
			$closeDt = $row['close_dt'];

			Unset($row);

			$sql = 'UPDATE	b02center
					SET		to_dt = \''.$myF->dateStyle($toDt).'\'
					WHERE	b02_center = \''.$orgNo.'\'';
			$query[] = $sql;

			$sql = 'UPDATE	cv_reg_info
					SET		to_dt	 = \''.$toDt.'\'
					,		rs_cd	 = \''.$rsCd.'\'
					,		rs_dtl_cd= \''.$rsDtlCd.'\'
					WHERE	org_no	= \''.$orgNo.'\'
					AND		to_dt	= \''.$myF->dateAdd('day', -1, $closeDt, 'Ymd').'\'
					/*
					AND		from_dt <= \''.$today.'\'
					AND		to_dt	>= \''.$today.'\'
					*/';
			$query[] = $sql;
		}else{
			if ($stopGbn == '1'){
				$clsDt = $myF->dateAdd('day', -1, $clsDt, 'Ymd');

				//중지설정
				$sql = 'UPDATE	b02center
						SET		to_dt = \''.$myF->dateStyle($clsDt).'\'
						WHERE	b02_center = \''.$orgNo.'\'';
				$query[] = $sql;

				$sql = 'UPDATE	cv_reg_info
						SET		to_dt	 = \''.$clsDt.'\'
						,		rs_cd	 = \'2\'
						,		rs_dtl_cd= \'01\'
						WHERE	org_no	 = \''.$orgNo.'\'
						AND		from_dt <= \''.$today.'\'
						AND		to_dt	>= \''.$today.'\'';
				$query[] = $sql;
			}
		}
	}

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 if ($debug) echo $conn->error_msg.'\n'.$conn->error_query.'\n';
			 echo '데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
			 exit;
		}
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>