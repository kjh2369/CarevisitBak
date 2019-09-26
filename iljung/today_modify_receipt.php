<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_ed.php');

	//기관
	$orgNo = $_SESSION['userCenterCode'];

	//대상자
	$sql = 'SELECT	m03_jumin AS jumin
			,		m03_name AS name
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'0\'';

	$client = $conn->_fetch_array($sql,'jumin');

	//TODAY
	$today = Date('Ymd');

	//현재시간(HH:MM)
	$now = Date('Hi');

	//변경요청만 처리한다.
	$sql = 'SELECT	org_no
			,		svc_cd
			,		jumin
			,		date
			,		time
			,		seq
			,		idx
			,		from_time
			,		to_time
			,		mem_cd1
			,		mem_nm1
			,		mem_cd2
			,		mem_nm2
			FROM	plan_change_request
			WHERE	org_no		= \''.$orgNo.'\'
			AND		svc_cd		= \'0\'
			AND		date		= \''.$today.'\'
			AND		request_type= \'1\'
			AND		result_yn	= \'N\'
			AND		error_yn	= \'N\'
			AND		del_flag	= \'N\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		//에러여부
		$IsError = false;
		$errorMsg = '';

		//일정정보
		$sql = 'SELECT	t01_svc_subcode AS sub_cd
				,		t01_toge_umu AS family_yn
				,		t01_suga_code1 AS suga_cd
				,		t01_mem_cd1 AS mem_cd1
				,		t01_mem_cd2 AS mem_cd2
				,		t01_mem_nm1 AS mem_nm1
				,		t01_mem_nm2 AS mem_nm2
				FROM	t01iljung
				WHERE	t01_ccode		= \''.$row['org_no'].'\'
				AND		t01_mkind		= \''.$row['svc_cd'].'\'
				AND		t01_jumin		= \''.$row['jumin'].'\'
				AND		t01_sugup_date	= \''.$row['date'].'\'
				AND		t01_sugup_fmtime= \''.$row['time'].'\'
				AND		t01_sugup_seq	= \''.$row['seq'].'\'
				AND		t01_del_yn		= \'N\'';

		$iljung = $conn->get_array($sql);


		for($j=1; $j<=2; $j++){
			//요양보호사 일정중복확인
			if ($iljung['mem_cd'.$j]){
				$sql = 'SELECT	t01_jumin AS jumin
						,		t01_sugup_seq AS seq
						,		t01_sugup_fmtime AS from_time
						,		t01_sugup_totime AS to_time
						,		t01_svc_subcode AS sub_cd
						FROM	t01iljung
						WHERE	t01_ccode		= \''.$row['org_no'].'\'
						AND		t01_mkind		= \''.$row['svc_cd'].'\'
						AND		t01_sugup_date	= \''.$row['date'].'\'
						AND		t01_mem_cd1		= \''.$iljung['mem_cd'.$j].'\'
						AND		t01_del_yn		= \'N\'
						UNION	ALL
						SELECT	t01_jumin
						,		t01_sugup_seq
						,		t01_sugup_fmtime
						,		t01_sugup_totime
						,		t01_svc_subcode
						FROM	t01iljung
						WHERE	t01_ccode		= \''.$row['org_no'].'\'
						AND		t01_mkind		= \''.$row['svc_cd'].'\'
						AND		t01_sugup_date	= \''.$row['date'].'\'
						AND		t01_mem_cd2		= \''.$iljung['mem_cd'.$j].'\'
						AND		t01_del_yn		= \'N\'';

				$r = $conn->_fetch_array($sql);

				if (is_array($r)){
					if ($row['from_time'] && $row['to_time']){
						$rowF = $myF->time2min($row['from_time']);
						$rowT = $myF->time2min($row['to_time']);

						if ($rowF > $rowT) $rowT += (24 * 60);
					}


					foreach($r as $tmp){
						if ($row['jumin'] == $tmp['jumin'] && $row['time'] == $tmp['from_time'] && $row['seq'] == $tmp['seq']){
							//비교대상 아님
						}else{
							//요양보호사 당일 일정중복 여부확인
							$tmpF = $myF->time2min($tmp['from_time']);
							$tmpT = $myF->time2min($tmp['to_time']);

							if ($tmpF > $tmpT) $tmpT += (24 * 60);

							if ($rowF <= $tmpF && $rowT >= $tmpF ||
								$rowF <= $tmpT && $rowT >= $tmpT){
								$IsError = true;
								$errorMsg = '요양보호사('.$iljung['mem_nm'.$j].') 수급자('.$client[$tmp['jumin']]['name'].') 일정중복';
								break;
							}

							if ($tmpT + 30 > $now){
								$IsError = true;
								$errorMsg = '당일 일정변경 30분제한';
								break;
							}
						}
					}
				}
			}
		}

		//에러가 발생한 경우 다음로...
		if ($IsError){
			$sql = 'UPDATE	plan_change_request
					SET		error_yn	= \'Y\'
					,		error_msg	= \''.$errorMsg.'\'
					WHERE	org_no		= \''.$row['org_no'].'\'
					AND		svc_cd		= \''.$row['svc_cd'].'\'
					AND		jumin		= \''.$row['jumin'].'\'
					AND		date		= \''.$row['date'].'\'
					AND		time		= \''.$row['time'].'\'
					AND		seq			= \''.$row['seq'].'\'
					AND		idx			= \''.$row['idx'].'\'
					AND		del_flag	= \'N\'';

			$query[SizeOf($query)] = $sql;
			continue;
		}


		$subCd = $iljung['sub_cd'];
		$familyYn = $iljung['family_yn'];

		if ($subCd == '500'){
			if ($iljung['suga_cd'] == 'CBKD2'){
				$bathKind = '1';
			}else if ($iljung['suga_cd'] == 'CBKD1'){
				$bathKind = '2';
			}else{
				$bathKind = '3';
			}
		}else{
			$bathKind = '';
		}

		Unset($iljung);

		if ($row['from_time'] && $row['to_time']){
			//순번배정
			if ($row['time'] != $row['from_time']){
				$sql = 'SELECT	IFNULL(MAX(t01_sugup_seq),0)+1
						FROM	t01iljung
						WHERE	t01_ccode		= \''.$row['org_no'].'\'
						AND		t01_mkind		= \''.$row['svc_cd'].'\'
						AND		t01_jumin		= \''.$row['jumin'].'\'
						AND		t01_sugup_date	= \''.$row['date'].'\'
						AND		t01_sugup_fmtime= \''.$row['from_time'].'\'';

				$row['seq'] = $conn->get_data($sql);
			}

			$suga = $mySuga->findSugaCare($row['org_no'], $subCd, $row['date'], $row['from_time'], $row['to_time'], $familyYn, $bathKind);
		}else{
			$suga = '';
		}

		//요청이력수정
		$sql = 'UPDATE	plan_change_request
				SET		result_yn	= \'Y\'
				,		result_dt	= NOW()
				,		iljung_seq	= \''.$row['seq'].'\'';

		if ($row['from_time'] && $row['to_time']){
			$sql .= '
				,		iljung_time	= \''.$row['from_time'].'\'';
		}

		$sql .= '
				WHERE	org_no	= \''.$row['org_no'].'\'
				AND		svc_cd	= \''.$row['svc_cd'].'\'
				AND		jumin	= \''.$row['jumin'].'\'
				AND		date	= \''.$row['date'].'\'
				AND		time	= \''.$row['time'].'\'
				AND		seq		= \''.$row['seq'].'\'
				AND		idx		= \''.$row['idx'].'\'
				AND		del_flag= \'N\'';

		$query[SizeOf($query)] = $sql;


		$sl = '';

		if ($row['from_time'] && $row['to_time']){
			$sl .= ($sl ? ',' : '');
			$sl .=	't01_sugup_fmtime	= \''.$row['from_time'].'\'	/*시작시간*/
					,t01_sugup_totime	= \''.$row['to_time'].'\'	/*종료시간*/';
		}

		if ($row['mem_cd1'] && $row['mem_nm1']){
			$sl .= ($sl ? ',' : '');
			$sl .=	't01_yoyangsa_id1	= \''.$row['mem_cd1'].'\'	/*실행 주요양보호사*/
					,t01_yname1			= \''.$row['mem_nm1'].'\'	/*요양보호사명*/
					,t01_mem_cd1		= \''.$row['mem_cd1'].'\'	/*계획 주요양보호사*/
					,t01_mem_nm1		= \''.$row['mem_nm1'].'\'	/*요양보호사명*/';
		}

		if ($row['mem_cd2'] && $row['mem_nm2']){
			$sl .= ($sl ? ',' : '');
			$sl .=	't01_yoyangsa_id2	= \''.$row['mem_cd2'].'\'	/*실행 부요양보호사*/
					,t01_yname2			= \''.$row['mem_nm2'].'\'	/*요양보호사명*/
					,t01_mem_cd2		= \''.$row['mem_cd2'].'\'	/*계획 부요양보호사*/
					,t01_mem_nm2		= \''.$row['mem_nm2'].'\'	/*요양보호사명*/';
		}

		if ($suga){
			$sl .= ($sl ? ',' : '');
			$sl .=	't01_suga_code1		= \''.$suga['code'].'\'	/*수가코드*/
					,t01_suga			= \''.$suga['cost'].'\'	/*수가*/
					,t01_suga_over		= \''.$suga['costEvening'].'\'	/*야간할증금액*/
					,t01_suga_night		= \''.$suga['costNight'].'\'	/*심야할증금액*/
					,t01_suga_tot		= \''.$suga['costTotal'].'\'	/*수가총액*/
					,t01_sugup_soyotime	= \''.$suga['procTime'].'\'

					,t01_e_time			= \''.$suga['timeEvening'].'\'	/*야간시간*/
					,t01_n_time			= \''.$suga['timeNight'].'\'	/*심야시간*/

					,t01_conf_suga_code	= \''.$suga['code'].'\'		/*확정수가코드*/
					,t01_conf_suga_value= \''.$suga['costTotal'].'\'/*확정수가*/';
		}

		$sql = 'UPDATE	t01iljung
				SET		'.$sl.'
				WHERE	t01_ccode		= \''.$row['org_no'].'\'
				AND		t01_mkind		= \''.$row['svc_cd'].'\'
				AND		t01_jumin		= \''.$row['jumin'].'\'
				AND		t01_sugup_date	= \''.$row['date'].'\'
				AND		t01_sugup_fmtime= \''.$row['time'].'\'
				AND		t01_sugup_seq	= \''.$row['seq'].'\'
				AND		t01_del_yn		= \'N\'';

		$query[SizeOf($query)] = $sql;

		Unset($suga);
	}

	$conn->row_free();

	/*
	foreach($query as $sql){
		echo $sql.chr(13).chr(13);
	}
	 */

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 echo 9;
			 exit;
		}
	}

	$conn->commit();

	echo 1;

	include_once('../inc/_db_close.php');
?>