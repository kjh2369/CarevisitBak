<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$acctYm	= $year.($month < 10 ? '0' : '').$month;
	$yymm	= $myF->dateAdd('month', -1, $acctYm.'01', 'Ym');
	$data	= Explode('?',$_POST['data']);

	$sql = 'SELECT	CONCAT(svc_gbn, \'_\', svc_cd) AS svc_cd, \'Y\' AS yn
			FROM	cv_svc_acct_list
			WHERE	org_no	= \''.$orgNo.'\'
			AND		acct_ym = \''.$acctYm.'\'';

	$acctList = $conn->_fetch_array($sql, 'svc_cd');

	//사용기간
	$lastday= $myF->dateAdd('day', -1, $acctYm.'01', 'd');
	$useFrom = $yymm.'01';
	$useTo	 = $yymm.$lastday;

	for($i=0; $i<count($data); $i++){
		parse_str($data[$i],$R);

		$svcCd = $R['svcGbn'].'_'.$R['svcCd'];

		$stndAmt = str_replace(',','',$R['stndAmt']);
		$overCnt = str_replace(',','',$R['overCnt']);
		$overCost = str_replace(',','',$R['overCost']);
		$overAmt = $overCnt * $overCost;

		if ($yymm > '201508'){
			$acctAmt = $stndAmt + $overAmt;
		}else{
			//현청구금액
			$sql = 'SELECT	amt
					FROM	cv_svc_acct_amt
					WHERE	org_no = \''.$orgNo.'\'
					AND		yymm <= \''.$yymm.'\'
					ORDER	BY yymm DESC
					LIMIT	1';
			$tmpAmt = $stndAmt + $overAmt;
			$acctAmt = $conn->get_data($sql);
			$stndAmt = $acctAmt;

			if (!$tmpOrgCd['1_01']) $tmpOrgCd['1_01'] = $svcCd;
		}

		if ($R['useFrom'] && $R['useFrom'] <= $useFrom) $R['useFrom'] = $useFrom;
		if ($R['useTo'] && $R['useTo'] >= $useTo) $R['useTo'] = $useTo;

		if ($acctList[$svcCd]['yn'] == 'Y'){
			if ($yymm > '201508' || $tmpOrgCd['1_01'] == $svcCd){
				$sql = 'UPDATE	cv_svc_acct_list
						SET		stnd_amt	= \''.$stndAmt.'\'
						,		over_cnt	= \''.$overCnt.'\'
						,		over_cost	= \''.$overCost.'\'
						,		over_amt	= \''.$overAmt.'\'
						,		acct_amt	= \''.$acctAmt.'\'
						,		tmp_amt		= \''.$tmpAmt.'\'
						,		pro_cd		= \''.$R['proCd'].'\'
						,		acct_gbn	= \''.$R['acctGbn'].'\'
						,		unit_cd		= \''.$R['unitCd'].'\'
						,		use_from	= \''.$R['useFrom'].'\'
						,		use_to		= \''.$R['useTo'].'\'
						WHERE	org_no	= \''.$orgNo.'\'
						AND		yymm	= \''.$yymm.'\'
						AND		svc_gbn	= \''.$R['svcGbn'].'\'
						AND		svc_cd	= \''.$R['svcCd'].'\'';
			}else{
				$sql = 'UPDATE	cv_svc_acct_list
						SET		tmp_amt		= tmp_amt + \''.$tmpAmt.'\'
						WHERE	org_no	= \''.$orgNo.'\'
						AND		yymm	= \''.$yymm.'\'
						AND		CONCAT(svc_gbn, \'_\', svc_cd) = \''.$tmpOrgCd['1_01'].'\'';
			}
		}else{
			$sql = 'INSERT INTO cv_svc_acct_list (org_no,yymm,svc_gbn,svc_cd,pro_cd,acct_gbn,unit_cd,use_from,use_to,stnd_amt,acct_ym,acct_amt,over_cnt,over_cost,over_amt,insert_id,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$yymm.'\'
					,\''.$R['svcGbn'].'\'
					,\''.$R['svcCd'].'\'
					,\''.$R['proCd'].'\'
					,\''.$R['acctGbn'].'\'
					,\''.$R['unitCd'].'\'
					,\''.$R['useFrom'].'\'
					,\''.$R['useTo'].'\'
					,\''.$stndAmt.'\'
					,\''.$acctYm.'\'
					,\''.$acctAmt.'\'
					,\''.$overCnt.'\'
					,\''.$overCost.'\'
					,\''.$overAmt.'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';
		}

		$query[] = $sql;
	}

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo $conn->error_msg.'/'.$conn->error_query;
			 exit;
		}
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>