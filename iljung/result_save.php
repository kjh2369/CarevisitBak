<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$today	= Date('Ymd');

	parse_str($_POST['para'], $val);

	//일자
	$date = str_replace('.','',$val['fromDate']);

	//전일자 삭제
	#if ($val['first'] == 'Y'){
	#	$sql = 'DELETE
	#			FROM	plan_longterm_conf
	#			WHERE	org_no	= \''.$orgNo.'\'
	#			AND		date	= \''.$date.'\'';
	#}else{
		$sql = 'DELETE
				FROM	plan_longterm_conf
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		date	!= \''.$today.'\'
				AND		date	!= \''.$date.'\'';
	#}

	//$conn->execute($sql);
	$query[] = $sql;


	//청구여부 Y/N
	$claimYn = $val['claimYN'];

	//서비스
	if ($val['svcKind'] == '방문요양'){
		$subCd = '200';
	}else if ($val['svcKind'] == '방문목욕'){
		$subCd = '500';
	}else if ($val['svcKind'] == '방문간호'){
		$subCd = '800';
	}

	//고객정보
	$name = $val['cNm'];
	$appNo = $val['cNo'];

	//직원정보
	$memNm = $val['mNm'];
	$memCd = $val['mCd'];
	$memCd = str_replace('-','',$memCd);
	$memCd = str_replace('*','',$memCd);

	//자동여부
	$autoYn = $val['autoYN'];

	//시간
	$from = str_replace(':','',$val['fromTime']);
	$to = str_replace(':','',$val['toTime']);
	$time = $val['procTime'];

	//90분여부
	$min90Yn = $val['min90YN'];

	//목욕구분 1:미차량, 2:차량입욕, 3:차량미입욕, 4:?
	$bathGbn = $val['bathGbn'];


	//고객 주민번호
	$sql = 'SELECT	jumin
			FROM	client_his_lvl
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		app_no	 = \''.$appNo.'\'
			AND		from_dt <= \''.$date.'\'
			AND		to_dt	>= \''.$date.'\'';

	$jumin = $conn->get_data($sql);

	//직원주민번호
	$sql = 'SELECT	m02_yjumin
			FROM	m02yoyangsa
			WHERE	m02_ccode = \''.$orgNo.'\'
			AND		m02_mkind = \'0\'
			AND		LEFT(m02_yjumin,'.StrLen($memCd).') = \''.$memCd.'\'
			AND		LEFT(m02_yname, '.StrLen($memNm).') = \''.$memNm.'\'';

	$memCd = $conn->get_data($sql);


	//등록된 계획 찾기
	$sql = 'SELECT	t01_sugup_fmtime AS from_time
			,		t01_sugup_totime AS to_time
			,		t01_sugup_soyotime AS proc_time
			,		t01_sugup_seq AS seq
			,		t01_svc_subcode AS sub_cd
			,		t01_mem_cd1 AS mem_cd1
			,		t01_mem_cd2 AS mem_cd2
			,		t01_mem_nm1 AS mem_nm1
			,		t01_mem_nm2 AS mem_nm2';

	if ($subCd == '500'){
		$sql .= '
			,		\'N\' AS link_yn';
	}else{
		$sql .= '
			,		CASE WHEN lt.seq IS NULL THEN \'N\' ELSE \'Y\' END AS link_yn';
	}

	$sql .= '
			FROM	t01iljung
			LEFT	JOIN	plan_longterm_conf AS lt
					ON		lt.org_no	= t01_ccode
					AND		lt.date		= t01_sugup_date
					AND		lt.jumin	= t01_jumin
					AND		lt.plan_from= t01_sugup_fmtime
					AND		lt.plan_seq	= t01_sugup_seq
			WHERE	t01_ccode		= \''.$orgNo.'\'
			AND		t01_mkind		= \'0\'
			AND		t01_jumin		= \''.$jumin.'\'
			AND		t01_sugup_date	= \''.$date.'\'
			AND		t01_svc_subcode	= \''.$subCd.'\'
			AND		t01_del_yn		= \'N\'';

	if ($subCd != '500'){
		#$sql .= '
		#	AND		lt.seq IS NULL';
	}

	$sql .= '
			ORDER	BY CASE WHEN lt.seq IS NULL THEN 1 ELSE 2 END, from_time, to_time
			LIMIT	1';

	//if ($orgNo == '34715000136'){
	//	echo $sql;
	//}

	$plan = $conn->_fetch_array($sql);
	$planCnt = SizeOf($plan);

	$seq = -1;
	$memCd1 = '';
	$memCd2 = '';
	$memNm1 = '';
	$memNm2 = '';

	for($i=0; $i<$planCnt; $i++){
		$planSeq = $plan[$i]['seq'];
		$planFrom = $plan[$i]['from_time'];
		$planTo = $plan[$i]['to_time'];
		$planTime = $myF->time2min($planTo) - $myF->time2min($planFrom);

		if ($plan[$i]['link_yn'] == 'N'){
			//일자별 순번
			$sql = 'SELECT	seq
					,		mem_cd1
					,		mem_cd2
					,		mem_nm1
					,		mem_nm2
					,		conf_from
					,		conf_to
					FROM	plan_longterm_conf
					WHERE	org_no		= \''.$orgNo.'\'
					AND		date		= \''.$date.'\'
					AND		jumin		= \''.$jumin.'\'
					AND		sub_cd		= \''.$plan[$i]['sub_cd'].'\'
					AND		plan_from	= \''.$plan[$i]['from_time'].'\'
					AND		plan_seq	= \''.$plan[$i]['seq'].'\'';

			$row = $conn->get_array($sql);

			if (is_array($row)){
				if ($subCd == '500'){
					$tmpCd1 = $plan[$i]['mem_cd1'].'_'.$plan[$i]['mem_cd2'];
					$tmpCd2 = $plan[$i]['mem_cd2'].'_'.$plan[$i]['mem_cd1'];;

					if ($memCd == $plan[$i]['mem_cd1']){
						$seq = $row['seq'];
						$memCd1 = $memCd;
						$memNm1 = $memNm;
						$memCd2 = $plan[$i]['mem_cd2'];
						$memNm2 = $plan[$i]['mem_nm2'];
					}else if ($memCd == $plan[$i]['mem_cd2']){
						$seq = $row['seq'];
						$memCd1 = $plan[$i]['mem_cd1'];
						$memNm1 = $plan[$i]['mem_nm1'];
						$memCd2 = $memCd;
						$memNm2 = $memNm;
					}else{
						if ($memCd == $row['mem_cd1']){
							$seq = $row['seq'];
							$memCd1 = $memCd;
							$memNm1 = $memNm;
							$memCd2 = $row['mem_cd2'];
							$memNm2 = $row['mem_nm2'];
						}else if ($memCd == $row['mem_cd2']){
							$seq = $row['seq'];
							$memCd1 = $row['mem_cd1'];
							$memNm1 = $row['mem_nm1'];
							$memCd2 = $memCd;
							$memNm2 = $memNm;
						}else{
							if (!$row['mem_cd1']){
								$seq = $row['seq'];
								$memCd1 = $memCd;
								$memNm1 = $memNm;
								$memCd2 = $row['mem_cd2'];
								$memNm2 = $row['mem_nm2'];
							}else if (!$row['mem_cd2']){
								$seq = $row['seq'];
								$memCd1 = $row['mem_cd1'];
								$memNm1 = $row['mem_nm1'];
								$memCd2 = $memCd;
								$memNm2 = $memNm;
							}
						}
					}

					if ($from > $row['conf_from']) $from = $row['conf_from'];
					if ($to < $row['conf_to']) $to = $row['conf_to'];
				}else{
					if ($memCd == $row['mem_cd1']){
						$seq = $row['seq'];
						$memCd1 = $memCd;
						$memNm1 = $memNm;
					}
				}
			}

			if ($seq >= 0){
				$plan[$i]['link_yn'] == 'Y';
				break;
			}
		}
	}

	if ($seq < 0){
		if ($subCd == '500'){
			$sql = 'SELECT	seq
					FROM	plan_longterm_conf
					WHERE	org_no		= \''.$orgNo.'\'
					AND		date		= \''.$date.'\'
					AND		sub_cd		= \''.$subCd.'\'
					AND		jumin		= \''.$jumin.'\'';
		}else{
			$sql = 'SELECT	seq
					FROM	plan_longterm_conf
					WHERE	org_no		= \''.$orgNo.'\'
					AND		date		= \''.$date.'\'
					AND		sub_cd		= \''.$subCd.'\'
					AND		conf_from	= \''.$from.'\'
					AND		jumin		= \''.$jumin.'\'
					AND		mem_cd1		= \''.$memCd.'\'';
		}
		$seq = 0;
		$seq = $conn->get_data($sql);

		if (Empty($seq)){
			$sql = 'SELECT	IFNULL(MAX(seq),0)+1
					FROM	plan_longterm_conf
					WHERE	org_no	= \''.$orgNo.'\'
					AND		date	= \''.$date.'\'';

			$seq = $conn->get_data($sql);
			$lbNew = true;
		}else{
			$lbNew = false;
		}

		$memCd1 = $memCd;
		$memNm1 = $memNm;
	}else{
		$lbNew = false;
	}

	if ($lbNew){
		$sql = 'INSERT INTO plan_longterm_conf (
				 org_no
				,date
				,seq
				,sub_cd
				,plan_seq
				,plan_from
				,plan_to
				,plan_time
				,conf_from
				,conf_to
				,conf_time
				,jumin
				,name
				,mem_cd1
				,mem_cd2
				,mem_nm1
				,mem_nm2) VALUES (
				 \''.$orgNo.'\'
				,\''.$date.'\'
				,\''.$seq.'\'
				,\''.$subCd.'\'
				,\''.$planSeq.'\'
				,\''.$planFrom.'\'
				,\''.$planTo.'\'
				,\''.$planTime.'\'
				,\''.$from.'\'
				,\''.$to.'\'
				,\''.$time.'\'
				,\''.$jumin.'\'
				,\''.$name.'\'
				,\''.$memCd1.'\'
				,\''.$memCd2.'\'
				,\''.$memNm1.'\'
				,\''.$memNm2.'\'
				)';
	}else{
		$sql = 'UPDATE	plan_longterm_conf
				SET		sub_cd		= \''.$subCd.'\'
				,		plan_seq	= \''.$planSeq.'\'
				,		plan_from	= \''.$planFrom.'\'
				,		plan_to		= \''.$planTo.'\'
				,		plan_time	= \''.$planTime.'\'
				,		conf_from	= \''.$from.'\'
				,		conf_to		= \''.$to.'\'
				,		conf_time	= \''.$time.'\'
				,		jumin		= \''.$jumin.'\'
				,		name		= \''.$name.'\'
				,		mem_cd1		= \''.$memCd1.'\'
				,		mem_cd2		= \''.$memCd2.'\'
				,		mem_nm1		= \''.$memNm1.'\'
				,		mem_nm2		= \''.$memNm2.'\'
				WHERE	org_no	= \''.$orgNo.'\'
				AND		date	= \''.$date.'\'
				AND		seq		= \''.$seq.'\'';
	}

	//$conn->begin();
	//$conn->execute($sql);
	//$conn->commit();
	$query[] = $sql;

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
		}
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>