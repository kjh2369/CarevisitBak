<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$fromDt	= $_POST['fromDt'];


	//사유코드 및 시작일자
	$sql = 'SELECT	from_dt, rs_cd
			FROM	cv_reg_info
			WHERE	org_no	= \''.$orgNo.'\'
			AND		from_dt = \''.$fromDt.'\'';

	$row = $conn->get_array($sql);

	$tmpFromDt = $row['from_dt'];
	$tmpRsCd = $row['rs_cd'];

	Unset($row);


	if ($tmpRsCd == '2' || $tmpRsCd == '4'){ //일시정지, 해지
		$sql = 'SELECT	from_dt, to_dt, rs_cd, org_to_dt
				FROM	cv_reg_info
				WHERE	org_no	= \''.$orgNo.'\'
				AND		from_dt < \''.$fromDt.'\'
				ORDER	BY from_dt DESC
				LIMIT	1';

		$row = $conn->get_array($sql);

		$endFromDt = $row['from_dt'];
		$endToDt = $row['to_dt'];
		$endRsCd = $row['rs_cd'];
		$toDt = $row['org_to_dt'];

		Unset($row);

		if ($endRsCd != '2' && $endRsCd != '4' && $myF->dateAdd('day', 1, $endToDt, 'Ymd') == $tmpFromDt){
			$sql = 'UPDATE	cv_reg_info
					SET		to_dt	= org_to_dt
					,		org_to_dt	= NULL
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no	= \''.$orgNo.'\'
					AND		from_dt	= \''.$endFromDt.'\'';

			$query[] = $sql;
		}

		$sql = 'UPDATE	cv_svc_fee
				SET		to_dt	 = \''.$toDt.'\'
				,		mod_gbn	 = \'1\'
				,		update_id= \''.$_SESSION['userCode'].'\'
				,		update_dt= NOW()
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		to_dt	<= \''.$toDt.'\'
				AND		mod_gbn	 = \'9\'
				AND		del_flag = \'N\'';

		$query[] = $sql;
	}else{
		/*
		$sql = 'SELECT	rs_cd, rs_dtl_cd, from_dt, to_dt, IFNULL(org_to_dt,to_dt) AS org_to_dt
				FROM	cv_reg_info
				WHERE	org_no	= \''.$orgNo.'\'
				AND		from_dt < \''.$fromDt.'\'
				ORDER	BY from_dt DESC
				LIMIT	1';

		$row = $conn->get_array($sql);

		if ($row['rs_cd'] == '3' && $row['rs_dtl_cd'] == '01' && $row['to_dt'] != $row['org_to_dt']){
			$sql = 'UPDATE	cv_reg_info
					SET		to_dt	= org_to_dt
					,		org_to_dt	= NULL
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no	= \''.$orgNo.'\'
					AND		from_dt	= \''.$row['from_dt'].'\'';

			$query[] = $sql;
		}
		*/
	}

	Unset($row);

	$sql = 'DELETE
			FROM	cv_reg_info
			WHERE	org_no	= \''.$orgNo.'\'
			AND		from_dt	= \''.$fromDt.'\'';

	$query[] = $sql;

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 echo $fromDt;
			 exit;
		}
	}

	$conn->commit();

	$sql = 'SELECT	from_dt
			FROM	cv_reg_info
			WHERE	org_no = \''.$orgNo.'\'
			ORDER	BY from_dt DESC
			LIMIT	1';
	echo $conn->get_data($sql);

	include_once('../inc/_db_close.php');
?>