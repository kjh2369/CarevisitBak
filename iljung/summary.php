<?
	include_once('../inc/_login.php');

	$sql = 'SELECT	t01_jumin AS tgt_cd
			,		t01_mem_cd1 AS plan_mem_cd1, t01_mem_cd2 AS plan_mem_cd2
			,		t01_yoyangsa_id1 AS conf_mem_cd1, t01_yoyangsa_id2 AS conf_mem_cd2
			,		CASE WHEN t01_svc_subcode = \'200\' AND t01_dementia_yn = \'Y\' THEN \'210\' ELSE t01_svc_subcode END AS sub_cd
			,		COUNT(t01_status_gbn) AS plan_cnt
			,		SUM(CASE WHEN t01_status_gbn = \'1\' THEN 1 ELSE 0 END) AS conf_cnt
			FROM	t01iljung
			WHERE	t01_ccode	= \''.$orgNo.'\'
			AND		t01_mkind	= \'0\'
			AND		t01_del_yn	= \'N\'
			AND		LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
			GROUP	BY t01_jumin, t01_mem_cd1, t01_mem_cd2, t01_yoyangsa_id1, t01_yoyangsa_id2, CASE WHEN t01_svc_subcode = \'200\' AND t01_dementia_yn = \'Y\' THEN \'210\' ELSE t01_svc_subcode END';


	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		//서비스 집계
		$sum['SVC'][$row['sub_cd']]['PLAN'] += $row['plan_cnt'];
		$sum['SVC'][$row['sub_cd']]['CONF'] += $row['conf_cnt'];

		//대상자 집계
		$sum['TGT'][$row['tgt_cd']][$row['sub_cd']]['PLAN'] += $row['plan_cnt'];
		$sum['TGT'][$row['tgt_cd']][$row['sub_cd']]['CONF'] += $row['conf_cnt'];

		//직원 집계
		$sum['MEM'][$row['plan_mem_cd1']][$row['sub_cd']]['PLAN'] += $row['plan_cnt'];
		$sum['MEM'][$row['conf_mem_cd1']][$row['sub_cd']]['CONF'] += $row['conf_cnt'];

		if ($row['sub_cd'] == '500'){
			if ($row['plan_mem_cd2']) $sum['MEM'][$row['plan_mem_cd2']][$row['sub_cd']]['PLAN'] += $row['plan_cnt'];
			if ($row['conf_mem_cd2']) $sum['MEM'][$row['conf_mem_cd2']][$row['sub_cd']]['CONF'] += $row['conf_cnt'];
		}
	}

	$conn->row_free();

	Unset($query);

	$sql = 'DELETE
			FROM	sum_iljung_svc
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'';
	$query[] = $sql;

	$sql = 'DELETE
			FROM	sum_iljung_mem
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'';
	$query[] = $sql;

	$sql = 'DELETE
			FROM	sum_iljung_tgt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'';
	$query[] = $sql;

	if (is_array($sum)){
		foreach($sum as $gbn => $R){
			if ($gbn == 'SVC'){
				$sql = 'INSERT INTO sum_iljung_svc VALUES (
						 \''.$orgNo.'\' /* org_no */
						,\''.$yymm.'\' /* yymm */
						,\''.$R['200']['PLAN'].'\' /* plan_care_cnt */
						,\''.$R['500']['PLAN'].'\' /* plan_bath_cnt */
						,\''.$R['800']['PLAN'].'\' /* plan_nurse_cnt */
						,\''.$R['210']['PLAN'].'\' /* plan_dmta_cnt */
						,\''.$R['200']['CONF'].'\' /* conf_care_cnt */
						,\''.$R['500']['CONF'].'\' /* conf_bath_cnt */
						,\''.$R['800']['CONF'].'\' /* conf_nurse_cnt */
						,\''.$R['210']['CONF'].'\' /* conf_dmta_cnt */
						,\''.$_SESSION['userCode'].'\'
						,NOW())';
				$query[] = $sql;
			}else{
				if ($gbn == 'MEM'){
					$tbl = 'sum_iljung_mem';
				}else if ($gbn == 'TGT'){
					$tbl = 'sum_iljung_tgt';
				}else{
					continue;
				}
				foreach($R as $jumin => $V){
					$sql = 'INSERT INTO '.$tbl.' VALUES (
							 \''.$orgNo.'\' /* org_no */
							,\''.$yymm.'\' /* yymm */
							,\''.$jumin.'\'
							,\''.$V['200']['PLAN'].'\' /* plan_care_cnt */
							,\''.$V['500']['PLAN'].'\' /* plan_bath_cnt */
							,\''.$V['800']['PLAN'].'\' /* plan_nurse_cnt */
							,\''.$V['210']['PLAN'].'\' /* plan_dmta_cnt */
							,\''.$V['200']['CONF'].'\' /* conf_care_cnt */
							,\''.$V['500']['CONF'].'\' /* conf_bath_cnt */
							,\''.$V['800']['CONF'].'\' /* conf_nurse_cnt */
							,\''.$V['210']['CONF'].'\' /* conf_dmta_cnt */
							,\''.$_SESSION['userCode'].'\'
							,NOW())';
					$query[] = $sql;
				}
			}
		}

		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 Unset($sum);
				 Unset($query);
				 exit;
			}
		}

		$conn->commit();
	}

	Unset($sum);
	Unset($query);

	include_once('../inc/_db_close.php');
?>