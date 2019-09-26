<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	/*
SELECT a.*
,      CASE WHEN b.org_no IS NOT NULL THEN 'Y' ELSE 'N' END AS sms_yn
,      CASE WHEN c.org_no IS NOT NULL THEN 'Y' ELSE 'N' END AS smart_yn
FROM   (
       SELECT DISTINCT
              m00_mcode AS org_no
       ,      m00_store_nm AS org_nm
       ,      b02_homecare AS homecare_yn
       ,      MID(b02_voucher,1,1) AS nurse_yn
       ,      MID(b02_voucher,2,1) AS old_yn
       ,      MID(b02_voucher,3,1) AS baby_yn
       ,      MID(b02_voucher,4,1) AS dis_yn
       ,      care_support AS care_s_yn
       ,      care_resource AS care_r_yn
       ,      hold_yn
       ,      basic_cost
       ,      client_cost
       ,      client_cnt
       FROM   m00center
       INNER  JOIN b02center
              ON   b02_center = m00_mcode
              AND  b02_kind = '0'
              AND  LEFT(REPLACE(from_dt,'-',''),6) <= '201502'
              AND  LEFT(REPLACE(to_dt,'-',''),6) >= '201502'
       WHERE  m00_domain = 'carevisit.net'
       ) AS a
LEFT   JOIN sms_acct AS b
       ON   b.org_no = a.org_no
       AND  b.acct_yn = 'Y'
       AND  LEFT(REPLACE(b.from_dt,'-',''),6) <= '201502'
       AND  LEFT(REPLACE(b.to_dt,'-',''),6) >= '201502'
LEFT   JOIN smart_acct AS c
       ON   c.org_no = a.org_no
       AND  c.acct_yn = 'Y'
       AND  LEFT(REPLACE(c.from_dt,'-',''),6) <= '201502'
       AND  LEFT(REPLACE(c.to_dt,'-',''),6) >= '201502'
ORDER  BY org_nm
	*/

	$company= $_POST['company'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;
	$yymm	= $year.$month;

	$unitStr = Array('1'=>'고객', '2'=>'직원', '3'=>'문자', '4'=>'고정');


	//서비스 항목
	$sql = 'SELECT	1 AS svc_gbn
			,		svc_cd
			,		svc_nm
			,		pro_cd
			,		unit_gbn
			FROM	cv_svc_main
			WHERE	parent_cd IS NOT NULL
			UNION	ALL
			SELECT	2
			,		svc_cd
			,		svc_nm
			,		NULL
			,		unit_gbn
			FROM	cv_svc_sub
			WHERE	parent_cd IS NOT NULL';

	//echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$svcList[$row['svc_gbn'].'_'.$row['svc_cd']] = Array(
			'name'=>$row['svc_nm']
		,	'svcGbn'=>$row['svc_gbn']
		,	'svcCd'=>$row['svc_cd']
		,	'proCd'=>$row['pro_cd']
		,	'unitCd'=>$row['unit_gbn'] //초과 단위 1:고객, 2:직원, 3:SMS, 4:고정
		,	'unitStr'=>$unitStr[$row['unit_gbn']]
		,	'acctGbn'=>'1' //과금기준 1:정율제, 2:정액제
		,	'stndCost'=>0 //기본금
		,	'overCost'=>0 //초과단가
		,	'limitCnt'=>0 //제한수
		);
	}

	$conn->row_free();


	$sql = 'SELECT	a.*
			,		CASE WHEN b.org_no IS NOT NULL THEN \'Y\' ELSE \'N\' END AS sms_yn
			,		CASE WHEN c.org_no IS NOT NULL THEN \'Y\' ELSE \'N\' END AS smart_yn
			,		CASE WHEN d.org_no IS NOT NULL THEN \'Y\' ELSE \'N\' END AS dan_yn
			,		CASE WHEN e.org_no IS NOT NULL THEN \'Y\' ELSE \'N\' END AS wmd_yn
			FROM	(
					SELECT	DISTINCT
							m00_mcode AS org_no
					,		m00_store_nm AS org_nm
					,		m00_mname AS manager
					,		b02_homecare AS homecare_yn
					,		MID(b02_voucher,1,1) AS nurse_yn
					,		MID(b02_voucher,2,1) AS old_yn
					,		MID(b02_voucher,3,1) AS baby_yn
					,		MID(b02_voucher,4,1) AS dis_yn
					,		care_support AS care_s_yn
					,		care_resource AS care_r_yn
					,		hold_yn
					,		basic_cost
					,		client_cost
					,		client_cnt
					FROM	m00center
					INNER	JOIN	b02center
							ON		b02_center	= m00_mcode
							AND		b02_kind	= \'0\'
							AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
							AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6)	>= \''.$yymm.'\'
					WHERE	m00_domain = \''.$company.'\'
					) AS a
			LEFT	JOIN	sms_acct AS b
					ON		b.org_no	= a.org_no
					AND		b.acct_yn	= \'Y\'
					AND		LEFT(REPLACE(b.from_dt,\'-\',\'\'),6)	<= \''.$yymm.'\'
					AND		LEFT(REPLACE(b.to_dt,\'-\',\'\'),6)		>= \''.$yymm.'\'
			LEFT	JOIN	smart_acct AS c
					ON		c.org_no	= a.org_no
					AND		c.acct_yn	= \'Y\'
					AND		LEFT(REPLACE(c.from_dt,\'-\',\'\'),6)	<= \''.$yymm.'\'
					AND		LEFT(REPLACE(c.to_dt,\'-\',\'\'),6)		>= \''.$yymm.'\'
			LEFT	JOIN	sub_svc AS d
					ON		d.org_no	= a.org_no
					AND		d.svc_cd	= \'5\'
					AND		d.acct_yn	= \'Y\'
					AND		d.del_flag	= \'N\'
					AND		LEFT(REPLACE(d.from_dt,\'-\',\'\'),6)	<= \''.$yymm.'\'
					AND		LEFT(REPLACE(d.to_dt,\'-\',\'\'),6)		>= \''.$yymm.'\'
			LEFT	JOIN	sub_svc AS e
					ON		e.org_no	= a.org_no
					AND		e.svc_cd	= \'7\'
					AND		e.acct_yn	= \'Y\'
					AND		e.del_flag	= \'N\'
					AND		LEFT(REPLACE(e.from_dt,\'-\',\'\'),6)	<= \''.$yymm.'\'
					AND		LEFT(REPLACE(e.to_dt,\'-\',\'\'),6)		>= \''.$yymm.'\'
			ORDER	BY org_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		/*
		$centerList[$row['org_no']] = Array(
			'name'		=>$row['org_nm']
		,	'homecare'	=>$row['homecare_yn']
		,	'nurse'		=>$row['nurse_yn']
		,	'old'		=>$row['old_yn']
		,	'baby'		=>$row['baby_yn']
		,	'dis'		=>$row['dis_yn']
		,	'careS'		=>$row['care_s_yn']
		,	'careR'		=>$row['care_r_yn']
		,	'hold'		=>$row['hold_yn']
		,	'basicCost'	=>$row['basic_cost']
		,	'clientCost'=>$row['client_cost']
		,	'clientCnt'	=>$row['client_cnt']
		,	'sms'		=>$row['sms_yn']
		,	'smart'		=>$row['smart_yn']
		,	'dan'		=>$row['dan_yn']
		,	'wmd'		=>$row['wmd_yn']
		);
		*/

		/*
		$centerList[$row['org_no']] = Array(
			'HOMECARE'=>Array(
				''=>
			)
		,	'VOUCHRER'=>Array()
		,	'CARESR'=>Array()
		,	'SUBSVC'=>Array()
		);
		*/

		$centerList[$row['org_no']]['name'] = $row['org_nm'];
		$centerList[$row['org_no']]['manager'] = $row['manager'];
		$centerList[$row['org_no']]['cnt'] = SizeOf($svcList);
		$centerList[$row['org_no']]['svc']= $svcList;
	}

	$conn->row_free();


	if (is_array($centerList)){
		foreach($centerList as $orgNo => $R){
			$sql = 'SELECT	svc_gbn
					,		svc_cd
					,		acct_gbn
					,		stnd_cost
					,		over_cost
					,		limit_cnt
					FROM	cv_svc_fee
					WHERE	org_no	= \''.$orgNo.'\'
					AND		acct_yn = \'Y\'
					AND		use_yn	= \'Y\'
					AND		del_flag= \'N\'
					AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
					AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6)	>= \''.$yymm.'\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			if ($rowCnt > 0){
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);

					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['acctGbn']	= $row['acct_gbn']; //과금기준 1:정율제, 2:정액제
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['stndAmt']	= $row['stnd_cost']; //기본금
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['limitCnt']	= $row['limit_cnt']; //제한수
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['overCnt']	= 0; //초과수
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['overCost']	= $row['over_cost']; //초과단가
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['overAmt']	= 0; //초과금액
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['acctAmt']	= 0; //과금금액
				}
			}else{
				Unset($centerList[$orgNo]);
			}

			$conn->row_free();
		}


		//과금금액을 계산한다.
		foreach($centerList as $orgNo => $R){
			foreach($R['svc'] as $svcCD =>$S){
				if ($S['svcGbn'] == '1'){
					if ($S['proCd'] == '7'){
						//복지용구
						$sql = 'SELECT	COUNT(DISTINCT app_no)
								FROM	wmd_longterm_list
								WHERE	org_no	= \''.$orgNo.'\'
								AND		yymm	= \''.$yymm.'\'';
					}else{
						//서비스 인원수
						$sql = 'SELECT	COUNT(DISTINCT t01_jumin)
								FROM	t01iljung
								WHERE	t01_ccode	= \''.$orgNo.'\'
								AND		t01_mkind	= \''.$S['proCd'].'\'
								AND		t01_del_yn	= \'N\'
								AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'';
					}

					//서비스 인원수
					if ($S['unitCd'] == '1'){
						$unitCnt = $conn->get_data($sql);
					}else{
						$unitCnt = 0;
					}

					//초과수
					if ($unitCnt > $S['limitCnt']){
						$centerList[$orgNo]['svc'][$svcCD]['overCnt'] = $unitCnt - $S['limitCnt']; //초과수
						$centerList[$orgNo]['svc'][$svcCD]['overAmt'] = $centerList[$orgNo]['svc'][$svcCD]['overCnt'] * $S['overCost']; //초과금액
					}
				}else{
					//부서비스
					//초과단위
					if ($S['unitCd'] == '1'){
						//고객
						$unitCnt = 0;
					}else if ($S['unitCd'] == '2'){
						//직원
						$unitCnt = 0;
					}else if ($S['unitCd'] == '3'){
						//SMS
						$sql = 'SELECT	COUNT(*)
								FROM	sms_'.$yymm.'
								WHERE	org_no = \''.$orgNo.'\'';
						$unitCnt = $conn->get_data($sql);
					}else if ($S['unitCd'] == '4'){
						//고정
						$unitCnt = 0;
					}

					if ($unitCnt > $S['limitCnt']){
						$centerList[$orgNo]['svc'][$svcCD]['overCnt'] = $unitCnt - $S['limitCnt']; //초과수
						$centerList[$orgNo]['svc'][$svcCD]['overAmt'] = $centerList[$orgNo]['svc'][$svcCD]['overCnt'] * $S['overCost']; //초과금액
					}
				}

				if ($S['acctGbn'] == '1'){
					//정율제
				}else{
					//정액제
					$centerList[$orgNo]['svc'][$svcCD]['overAmt'] = 0;
				}
			}
		}

		//삭제쿼리
		$sql = 'DELETE
				FROM	cv_svc_acct_list
				WHERE	yymm = \''.$yymm.'\'';
		$query[] = $sql;

		//쿼리생성
		foreach($centerList as $orgNo => $R){
			foreach($R['svc'] as $svcCD =>$S){
				if ($S['stndAmt'] + $S['overAmt'] > 0){
					$sql = 'INSERT INTO cv_svc_acct_list VALUES (
							 \''.$orgNo.'\'
							,\''.$yymm.'\'
							,\''.$S['svcGbn'].'\'
							,\''.$S['svcCd'].'\'
							,\''.$S['proCd'].'\'
							,\''.$S['stndAmt'].'\'
							,\''.$S['unitCd'].'\'
							,\''.$S['limitCnt'].'\'
							,\''.$S['overCnt'].'\'
							,\''.$S['overCost'].'\'
							,\''.$S['overAmt'].'\'
							,\''.($S['stndAmt'] + $S['overAmt']).'\'
							,\''.$_SESSION['userCode'].'\'
							,NOW()
							)';
					$query[] = $sql;
				}
			}
		}

		//쿼리
		foreach($query as $sql){
			echo nl2br($sql);
			echo '<br>';
		}
	}


	Unset($svcList);
	Unset($centerList);

	include_once('../inc/_db_close.php');
?>