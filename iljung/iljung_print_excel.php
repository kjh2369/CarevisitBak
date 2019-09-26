<?
	include_once('../inc/_login.php');

	/*
	Array (
		[root] => iljung
		[dir] => p
		[fileName] => iljung_print
		[fileType] => excel
		[target] => show.php
		[showForm] => ILJUNG_CALN
		[code] => 1234
		[year] => 2015
		[month] => 03
		[jumin] => %A3%40%E6%82%D2%E7%9At%AE%0D%DD0%CD
		[showGbn] => all
		[mode] => 101
		[name] =>
		[chkSvc] => 0_2000_5000_800
		[cboSvcYn] => undefined
		[printDT] => 2015-04-10
		[useType] => Y
		[calnYn] => Y
		[dtlYn] => N
		[order] => 1
		[bipayYn] => A
		[sr] =>
		[param] => )
	 */

	$orgNo	= $_SESSION['userCenterCode'];
	$mode	= $_POST['mode'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$jumin	= $ed->de($_POST['jumin']);
	$SR		= $_POST['sr'];
	$showGbn= $_POST['showGbn'];


	//결제란설정
	$sql = 'SELECT	line_cnt, subject
			FROM	signline_set
			WHERE	org_no = \''.$orgNo.'\'';
	$row = $conn->get_array($sql);

	$signCnt = $row['line_cnt'];
	$signTxt = Explode('|',$row['subject']);

	Unset($row);


	/** 데이타 ****************************************/
	//공지내용
	$sql = 'SELECT	svc_cd AS cd
			,		notice AS str
			FROM	voucher_notice
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'';
	//$pdf->tempVal = $conn->_fetch_array($sql,'cd');
	$noticeRow = $conn->_fetch_array($sql,'cd');

	//휴일리스트
	$sql = 'SELECT	mdate AS date
			,		holiday_name AS name
			FROM	tbl_holiday
			WHERE	LEFT(mdate,6)	= \''.$yymm.'\'';
	$arrHoliday = $conn->_fetch_array($sql,'date');

	if ($month == '5'){
		$arrHoliday[$yymm.'01']['name']	= '근로자의날';
	}

	if ($mode == '101'){
		//직원정보
		$sql = 'SELECT	DISTINCT
						m02_yjumin AS jumin
				,		m02_yname AS name
				,		m02_ytel AS mobile
				,		m02_ytel2 AS phone
				FROM	m02yoyangsa
				WHERE	m02_ccode	= \''.$orgNo.'\'';

		$arrMem = $conn->_fetch_array($sql,'jumin');

		//한도금액
		$sql = 'SELECT	m91_code as cd
				,		m91_kupyeo as amt
				FROM	m91maxkupyeo
				WHERE	LEFT(m91_sdate, 6)	<= \''.$yymm.'\'
				AND		LEFT(m91_edate, 6)	>= \''.$yymm.'\'';

		$arrLimitPay = $conn->_fetch_array($sql, 'cd');

		//청구한도
		$sql = 'SELECT	jumin
				,		amt
				FROM	client_his_limit
				WHERE	org_no = \''.$orgNo.'\'
				AND		DATE_FORMAT(from_dt, \'%Y%m\') <= \''.$yymm.'\'
				AND		DATE_FORMAT(to_dt,   \'%Y%m\') >= \''.$yymm.'\'';

		if (!Empty($jumin)){
			$sql .= ' AND jumin = \''.$jumin.'\'';
		}

		$arrClaimPay = $conn->_fetch_array($sql, 'jumin');
	}

	//수가정보
	$sql = 'SELECT m01_mcode2 AS cd
			,      m01_suga_cont AS nm
			,      m01_suga_value AS cost
			,      m01_sdate AS from_dt
			,      m01_edate AS to_dt
			  FROM m01suga
			 WHERE m01_mcode          = \'goodeos\'
			   AND LEFT(m01_sdate,6) <= \''.$yymm.'\'
			   AND LEFT(m01_edate,6) >= \''.$yymm.'\'
			 UNION ALL
			SELECT m11_mcode2 AS cd
			,      m11_suga_cont AS nm
			,      m11_suga_value AS cost
			,      m11_sdate AS from_dt
			,      m11_edate AS to_dt
			  FROM m11suga
			 WHERE m11_mcode          = \'goodeos\'
			   AND LEFT(m11_sdate,6) <= \''.$yymm.'\'
			   AND LEFT(m11_edate,6) >= \''.$yymm.'\'
			 UNION ALL
			SELECT service_code AS cd
			,      service_gbn AS nm
			,      service_cost AS cost
			,      service_from_dt AS from_dt
			,      service_to_dt AS to_dt
			  FROM suga_service
			 WHERE org_no = \'goodeos\'
			   AND DATE_FORMAT(service_from_dt,\'%Y%m\') <= \''.$yymm.'\'
			   AND DATE_FORMAT(service_to_dt,  \'%Y%m\') >= \''.$yymm.'\'
			UNION	ALL
			SELECT	DISTINCT
					CONCAT(suga_cd,suga_sub) AS code
			,		suga_nm AS name
			,		suga_cost AS cost
			,		REPLACE(from_dt,\'-\',\'\') AS from_dt
			,		REPLACE(to_dt,\'-\',\'\') AS to_dt
			FROM	care_suga
			WHERE	org_no	= \''.$orgNo.'\'
			AND		suga_sr	= \''.$SR.'\'
			AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
			AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6) >= \''.$yymm.'\'';

	//주야가보호 수가
	$sql .= '
			UNION	ALL
			SELECT	CONCAT(code,\'_\',lv_gbn) AS cd
			,		name AS nm
			,		cost
			,		from_dt
			,		to_dt
			FROM	suga_dan
			WHERE	DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
			AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$yymm.'\'';

	$arrSuga = $conn->_fetch_array($sql);

	//선택서비스
	$svcGbn	= Explode(chr(1),$_POST['chkSvc']);

	//선택구분
	if ($showGbn == 'family'){
		$showSql = ' AND t01_toge_umu    = \'Y\'
					 AND t01_svc_subcode = \'200\'';

	}else if ($showGbn == 'conf'){
		$showSql = ' AND t01_status_gbn = \'1\'';

	}else{
		$showSql = '';
	}

	if ($showGbn == 'conf'){
		$gbnField	= 'conf';
		$gbnMemCd	= 'yoyangsa_id';
		$gbnMemNm	= 'yname';
		$gbnSugaCd	= 'conf_suga_code';
		$gbnSugaTot	= 'conf_suga_value';
	}else{
		$gbnField	= 'sugup';
		$gbnMemCd	= 'mem_cd';
		$gbnMemNm	= 'mem_nm';
		$gbnSugaCd	= 'suga_code1';
		$gbnSugaTot	= 'suga_tot';
	}

	if ($mode == '101'){
		//고객정보 조회
		foreach($svcGbn as $svcIdx => $svc){
			if ($svc){
				if (Is_Numeric(StrPos($svc,'_'))){
					$tmp = Explode('_',$svc);
					$svcCd = $tmp[0];
					$subCd = $tmp[1];

					if ($subCd == '900'){
						$svcCd = '5';
						$subCd = '';
					}
				}else{
					$svcCd = $svc;
					$subCd = '';
				}

				if (!Empty($sl)){
					$sl .= ' UNION ALL ';
				}

				$sl .= 'SELECT	mst.m03_jumin AS jumin
						,		mst.m03_name AS name
						,		mst.m03_mkind AS svc_cd
						,		lvl.app_no
						,		CASE mst.m03_mkind	WHEN \'0\'	THEN lvl.level
													WHEN \'4\'	THEN dis.svc_lvl ELSE \'\' END AS lvl_cd
						,		CASE mst.m03_mkind	WHEN \'0\'	THEN
													CASE kind.kind	WHEN \'3\'	THEN \'기초\'
																	WHEN \'2\'	THEN \'의료\'
																	WHEN \'4\'	THEN \'경감\' ELSE \'일반\' END
													ELSE \'\' END AS kind
						,		CASE mst.m03_mkind WHEN \'0\' THEN kind.rate ELSE \'\' END AS rate
						FROM	m03sugupja AS mst
						LEFT	JOIN	client_his_lvl AS lvl
								ON		lvl.org_no = mst.m03_ccode
								AND		lvl.svc_cd = mst.m03_mkind
								AND		lvl.jumin  = mst.m03_jumin
								AND		DATE_FORMAT(lvl.from_dt,\'%Y%m\') <= \''.$yymm.'\'
								AND		DATE_FORMAT(lvl.to_dt,  \'%Y%m\') >= \''.$yymm.'\'
						LEFT	JOIN	client_his_kind AS kind
								ON		kind.org_no = mst.m03_ccode
								AND		kind.jumin  = mst.m03_jumin
								AND		DATE_FORMAT(kind.from_dt,\'%Y%m\') <= \''.$yymm.'\'
								AND		DATE_FORMAT(kind.to_dt,  \'%Y%m\') >= \''.$yymm.'\'
						LEFT	JOIN	client_his_dis AS dis
								ON		dis.org_no = mst.m03_ccode
								AND		dis.jumin  = mst.m03_jumin
								AND		DATE_FORMAT(dis.from_dt,\'%Y%m\') <= \''.$yymm.'\'
								AND		DATE_FORMAT(dis.to_dt,  \'%Y%m\') >= \''.$yymm.'\'
						WHERE	m03_ccode  = \''.$orgNo.'\'';

				if ($svcCd == 'S' || $svcCd == 'R'){
					$sl .= '
							AND		m03_mkind  = \'6\'';
				}else if ($svcCd == '5'){
					$sl .= '
							AND		m03_mkind  = \'0\'';
				}else{
					$sl .= '
							AND		m03_mkind  = \''.$svcCd.'\'';
				}

				$sl .= '
						AND		m03_del_yn = \'N\'';

				if (!Empty($jumin)){
					$sl .= ' AND m03_jumin = \''.$jumin.'\'';
				}

				if (!Empty($name)){
					$sl .= 'AND m03_name >= \''.$name.'\'';
				}
			}
		}

		/*
			$sql = 'SELECT DISTINCT
						   CONCAT(jumin,\'_\',svc_cd) AS id
					,      jumin
					,      name
					,      svc_cd
					,      app_no
					,      lvl_cd AS lvl_cd
					,      CASE lvl_cd WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl_cd,\'등급\') END AS lvl
					,      kind
					,      rate
					  FROM ('.$sl.') AS t
					 ORDER BY name, jumin, svc_cd';
		*/

		$sql = 'SELECT CONCAT(jumin,\'_\',svc_cd) AS id
				,      jumin
				,      name
				,      svc_cd
				,      app_no
				,      MIN(lvl_cd) AS lvl_cd
				,      CASE MIN(lvl_cd) WHEN \'9\' THEN \'일반\' ELSE CONCAT(MIN(lvl_cd),\'등급\') END AS lvl
				,      kind
				,      rate
				  FROM ('.$sl.') AS t
				 GROUP BY jumin, svc_cd
				 ORDER BY name, jumin, svc_cd';

		UnSet($sl);

		//고객정보 저정
		$arrClientInfo = $conn->_fetch_array($sql,'id');

		$sql = 'SELECT	jumin
				,		rate
				,		from_dt
				,		to_dt
				FROM	client_his_kind
				WHERE	org_no	= \''.$orgNo.'\'
				AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
				AND		DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$yymm.'\'';

		$arr = $conn->_fetch_array($sql);
		$cnt = SizeOf($arr);

		for($i=0; $i<$cnt; $i++){
			$idx = SizeOf($arrClientRate[$arr[$i]['jumin']]);

			$arrClientRate[$arr[$i]['jumin']][$idx] = Array(
					'rate'	=>$arr[$i]['rate']
				,	'from'	=>str_replace('-', '', $arr[$i]['from_dt'])
				,	'to'	=>str_replace('-', '', $arr[$i]['to_dt'])
			);
		}

		Unset($arr);

	}else if ($mode == '102'){
		$sql = 'SELECT DISTINCT
					   m02_yjumin AS jumin
				,      m02_yname AS name
				,      m02_ytel AS phone
				  FROM m02yoyangsa
				 WHERE m02_ccode = \''.$orgNo.'\'';

		if (!Empty($name)){
			$sql .= ' AND m02_yname >= \''.$name.'\'';
		}

		$arrMemberInfo = $conn->_fetch_array($sql,'jumin');
	}

	//일정정보
	foreach($svcGbn as $svcIdx => $svc){
		if ($svc){
			if (Is_Numeric(StrPos($svc,'_'))){
				$tmp = Explode('_',$svc);
				$svcCd = $tmp[0];
				$subCd = $tmp[1];

				if ($subCd == '900'){
					$svcCd = '5';
					$subCd = '';
				}
			}else{
				$svcCd = $svc;
				$subCd = '';
			}

			if (!Empty($sl)){
				$sl .= ' UNION ALL ';
			}

			if ($mode == '101'){
				$sl .= 'SELECT	t01_jumin AS jumin
						,		t01_mkind AS svc_cd
						,		t01_'.$gbnField.'_date AS date
						,		t01_'.$gbnField.'_fmtime AS from_time
						,		t01_'.$gbnField.'_totime AS to_time
						,		t01_'.$gbnField.'_soyotime AS proc_time
						,		t01_svc_subcode AS sub_cd
						,		t01_'.$gbnMemCd.'1 AS mem_cd1
						,		t01_'.$gbnMemCd.'2 AS mem_cd2
						,		t01_'.$gbnMemNm.'1 AS mem_nm1
						,		t01_'.$gbnMemNm.'2 AS mem_nm2
						,		t01_toge_umu AS family_yn
						,		t01_bipay_umu AS bipay_yn
						,		t01_'.$gbnSugaCd.' AS suga_cd
						,		t01_'.$gbnSugaTot.' AS suga_tot
						FROM	t01iljung
						WHERE	t01_ccode = \''.$orgNo.'\'
						AND		t01_mkind = \''.$svcCd.'\'
						AND		LEFT(t01_'.$gbnField.'_date,6) = \''.$yymm.'\'
						AND		t01_del_yn = \'N\'';

				if (!Empty($jumin)){
					$sl .= ' AND t01_jumin = \''.$jumin.'\'';
				}

				if (!Empty($subCd)){
					$sl .= ' AND t01_svc_subcode = \''.$subCd.'\'';
				}

				if ($orgNo == '31141000043' /* 예사랑 */){
					$sl.= ' AND t01_bipay_umu != \'Y\'';
				}

				if ($_POST['bipayYn'] == 'Y'){
					//급여
					$sl.= ' AND t01_bipay_umu != \'Y\'';
				}else if ($_POST['bipayYn'] == 'N'){
					//비급여
					$sl.= ' AND t01_bipay_umu = \'Y\'';
				}

				if ($svcCd == '6'){
					//재가지원 및 자원연계
					$sl .= ' AND t01_svc_subcd = \''.$SR.'\'';
				}
			}else if ($mode == '102'){
				$liLoopCnt = 1;

				if ($svcCd == '0'){
					if ($subCd == '500'){
						$liLoopCnt = 2;
					}
				}else if ($svcCd == '4'){
					if ($subCd == '200' || $subCd == '500'){
						$liLoopCnt = 2;
					}
				}

				for($i=1; $i<=$liLoopCnt; $i++){
					if ($i > 1){
						$sl .= ' UNION ALL ';
					}

					$sl .= 'SELECT t01_jumin AS jumin
							,      t01_mkind AS svc_cd
							,      t01_'.$gbnField.'_date AS date
							,      t01_'.$gbnField.'_fmtime AS from_time
							,      t01_'.$gbnField.'_totime AS to_time
							,      t01_'.$gbnField.'_soyotime AS proc_time
							,      t01_svc_subcode AS sub_cd
							,      t01_toge_umu AS family_yn
							,      t01_bipay_umu AS bipay_yn
							,      t01_'.$gbnSugaCd.' AS suga_cd
							,      t01_'.$gbnSugaTot.' AS suga_tot
							,      t01_'.$gbnMemCd.$i.' AS mem_cd
							  FROM t01iljung
							 WHERE t01_ccode = \''.$orgNo.'\'
							   AND t01_mkind = \''.$svcCd.'\'
							   AND LEFT(t01_'.$gbnField.'_date,6) = \''.$yymm.'\'
							   AND t01_del_yn = \'N\'';

					if (!Empty($jumin)){
						$sl .= ' AND t01_'.$gbnMemCd.$i.' = \''.$jumin.'\'';
					}

					if (!Empty($subCd)){
						$sl .= ' AND t01_svc_subcode = \''.$subCd.'\'';
					}

					if ($orgNo == '31141000043' /* 예사랑 */){
						$sl.= ' AND t01_bipay_umu != \'Y\'';
					}
				}
			}

			$sl .= $showSql;
		}
	}

	if ($mode == '101'){
		if ($svcCd == 'S' || $svcCd == 'R'){
			$svcSL = '6';
		}else if ($svcCd == '5'){
			$svcSL = '\'0\'';
		}else{
			$svcSL = 'iljung.svc_cd';
		}

		$sql = 'SELECT	iljung.jumin
				,		mst.m03_name AS name
				,		mst.m03_yoyangsa1_nm AS mem_nm
				,		iljung.svc_cd
				,		iljung.date
				,		iljung.from_time
				,		iljung.to_time
				,		iljung.proc_time
				,		iljung.sub_cd
				,		iljung.family_yn
				,		iljung.bipay_yn
				,		iljung.suga_cd
				,		iljung.suga_tot
				,		iljung.mem_cd1
				,		iljung.mem_cd2
				,		iljung.mem_nm1
				,		iljung.mem_nm2
				FROM	('.$sl.') AS iljung
				INNER	JOIN	m03sugupja AS mst
						ON		mst.m03_ccode = \''.$orgNo.'\'
						AND		mst.m03_mkind = '.$svcSL.'
						AND		mst.m03_jumin = iljung.jumin';

		if (!Empty($name)){
			$sql .= ' AND mst.m03_name >= \''.$name.'\'';
		}

		if ($_POST['order'] == '2'){
			$sql .= ' ORDER BY CASE WHEN mem_nm != \'\' THEN 1 ELSE 2 END,mem_nm,name, jumin, svc_cd, date, from_time';
		}else{
			$sql .= ' ORDER BY name, jumin, svc_cd, date, from_time';
		}

		//if ($debug) echo nl2br($sql);


	}else if ($mode == '102'){
		$sql = 'SELECT iljung.jumin
				,      mst.m03_name AS name
				,      iljung.svc_cd
				,      iljung.date
				,      iljung.from_time
				,      iljung.to_time
				,      iljung.proc_time
				,      iljung.sub_cd
				,      iljung.family_yn
				,      iljung.bipay_yn
				,      iljung.suga_cd
				,      iljung.suga_tot
				,      iljung.mem_cd
				,      yoy.m02_yname AS mem_nm
				  FROM ('.$sl.') AS iljung
				 INNER JOIN m03sugupja AS mst
					ON mst.m03_ccode = \''.$orgNo.'\'
				   AND mst.m03_mkind = \''.$_SESSION['userCenterKind'][0].'\'
				   AND mst.m03_jumin = iljung.jumin
				 INNER JOIN m02yoyangsa AS yoy
				    ON yoy.m02_ccode  = \''.$orgNo.'\'
				   AND yoy.m02_mkind  = \''.$_SESSION['userCenterKind'][0].'\'
				   AND yoy.m02_yjumin = iljung.mem_cd';

		if (!Empty($name)){
			$sql .= ' AND yoy.m02_yname >= \''.$name.'\'';
		}

		$sql .= ' ORDER BY mem_nm, mem_cd, date, from_time, svc_cd';

	}

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	if ($mode == '101'){
		//일정표(고객)
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if ($tmpJumin != $row['jumin']){
				$tmpJumin  = $row['jumin'];

				//한도금액
				$liLimitPay[$tmpJumin] = $arrClaimPay[$tmpJumin]['amt'];

				if (Empty($liLimitPay[$tmpJumin])){
					$liLimitPay[$tmpJumin] = $arrLimitPay[$arrClientInfo[$tmpJumin.'_'.$row['svc_cd']]['lvl_cd']]['amt'];
				}
			}

			$day = IntVal(Date('d',StrToTime($row['date'])));

			if ($tmpDay != $day){
				$tmpDay  = $day;
				$liSeq   = 0;
			}

			if ($row['svc_cd'] == '6' || $row['svc_cd'] == 'S' || $row['svc_cd'] == 'R'){
				$workTime	= $myF->timeStyle($row['from_time']);
				$resource	= $row['mem_nm1'];

				if ($row['mem_cd2']){
					$worker	= $row['mem_nm2'];
				}else{
					$worker	= '';
				}
			}else{
				$workTime	= $myF->timeStyle($row['from_time']).'~'.$myF->timeStyle($row['to_time']);
				$worker		= $arrMem[$row['mem_cd1']]['name'].(!Empty($row['mem_cd2']) ? '/'.$arrMem[$row['mem_cd2']]['name'] : '');
				$resource	= '';
			}

			$sugaNm	= '';

			foreach($arrSuga as $idx => $suga){
				if ($suga['cd'] == $row['suga_cd']){
					$sugaNm = $suga['nm'];
					break;
				}
			}

			if ($row['svc_cd'] == '5'){
				$liSvcCd = '0';
			}else{
				$liSvcCd = $row['svc_cd'];
			}

			//고객변경 확인(주민번호/서비스/일자/순번)
			$arrCaln[$row['jumin']][$liSvcCd][$day][$liSeq] = Array(
					'svcCd'		=>$row['svc_cd']
				,	'subCd'		=>$row['sub_cd']
				,	'name'		=>$row['name']
				,	'procTime'	=>$row['proc_time']
				,	'familyYn'	=>$row['family_yn']
				,	'bipayYn'	=>$row['bipay_yn']
				,	'workTime'	=>$workTime
				,	'worker'	=>$worker
				,	'resource'	=>$resource
				,	'memCd1'	=>$row['mem_cd1']
				,	'memCd2'	=>$row['mem_cd2']
				,	'sugaNm'	=>$sugaNm
			);

			//제공서비스 정보
			//$procTime = $myF->cutOff($row['proc_time'],30);
			if ($row['svc_cd'] == '0'){
				if ($row['sub_cd'] == '200'){
					$procTime = $myF->cutOff($row['proc_time'],30);
				}else if ($row['sub_cd'] == '500'){
					if ($row['proc_time'] >= 60){
						$procTime	= 60;
					}else if ($row['proc_time'] >= 40){
						$procTime	= 40;
					}else{
						$procTime	= 0;
					}
				}else{
					$procTime = $row['proc_time'];
				}
			}else if ($row['svc_cd'] == '1'){
				if ($yymm >= '201402'){
					$tmpProcTime = $row['proc_time'] % 60;
					$procTime = $myF->cutOff($row['proc_time'],60);

					if ($tmpProcTime >= 15 && $tmpProcTime <= 44){
						$procTime += 30;
					}else if ($tmpProcTime >= 45){
						$procTime += 60;
					}else{
					}
				}else{
					$procTime = $row['proc_time'];
				}
			}else{
				$procTime = $row['proc_time'];
			}

			if ($row['sub_cd'] == '500'){
				if ($procTime > 60){
					$procTime = 60;
				}
			}

			if (StrLen($procTime) < 3){
				$time = '0'.$procTime;
			}else{
				$time = $procTime;
			}

			$key1 = $row['jumin'].'_'.$row['svc_cd'];

			if ($_POST['dtlYn'] != 'N'){
				//제공서비스 상세내역
				$key2 = $row['svc_cd'].'_'.$row['sub_cd'].'_'.$row['from_time'].'_'.$row['to_time'].'_'.$worker;

				if (!IsSet($arrTme[$key1][$key2])){
					if ($row['svc_cd'] == '6' || $row['svc_cd'] == 'S' || $row['svc_cd'] == 'R'){
						$svcNm = $sugaNm;
					}else{
						$svcNm  = lfGetSvcNm($row['svc_cd'],$row['sub_cd'],$row['family_yn']);
						$svcNm .= '/'.lfGetSvcSugaNm($row['sub_cd'],$row['suga_cd'],$procTime);
					}

					$arrTme[$key1][$key2] = Array(
							'key'		=>$key2
						,	'svcNm'		=>$svcNm
						,	'procTime'	=>$procTime
						,	'workTime'	=>$workTime
						,	'worker'	=>$worker
						,	'sugaCost'	=>$row['suga_tot']
						,	'sugaTot'	=>0
						,	'day'		=>''
					);
				}

				if (!Is_Numeric(StrPos($arrTme[$key1][$key2]['day'], '/'.$day))){
					$arrTme[$key1][$key2]['day'] .= '/'.$day;
				}

				$arrTme[$key1][$key2]['sugaTot'] += $row['suga_tot'];
			}else{
				if ($row['svc_cd'] == 'S' || $row['svc_cd'] == 'R'){
					$key2 = $row['svc_cd'].'_'.$worker;
				}else{
					$key2 = $row['svc_cd'].'_'.$row['sub_cd'].'_'.$time.'_'.$worker;
				}

				if (!IsSet($arrSvc[$key1][$key2])){
					if ($row['svc_cd'] == '6' || $row['svc_cd'] == 'S' || $row['svc_cd'] == 'R'){
						$svcNm = $sugaNm;
					}else{
						$svcNm  = lfGetSvcNm($row['svc_cd'],$row['sub_cd'],$row['family_yn']);
						$svcNm .= '/'.lfGetSvcSugaNm($row['sub_cd'],$row['suga_cd'],$procTime);
					}

					$arrSvc[$key1][$key2] = Array(
							'key'		=>$key2
						,	'svcNm'		=>$svcNm
						,	'procTime'	=>$procTime
						,	'memNm'		=>$worker
						,	'day'		=>''
					);
				}

				if (!Is_Numeric(StrPos($arrSvc[$key1][$key2]['day'], '/'.$day))){
					$arrSvc[$key1][$key2]['day'] .= '/'.$day;
				}
			}


			//제공서비스 금액내역
			$key2 = $row['suga_cd'];
			$key3 = $row['svc_cd'].'_'.$row['sub_cd'].'_'.$time;

			if (!IsSet($arrDtl[$key1][$key2])){
				$svcNm	= lfGetSvcFullNm($row['svc_cd']);
				$sugaNm	= '';

				foreach($arrSuga as $idx => $suga){
					if ($suga['cd'] == $row['suga_cd']){
						$sugaNm = $suga['nm'];
						break;
					}
				}

				$arrDtl[$key1][$key2] = Array(
						'key'		=>$key3
					,	'svcNm'		=>$svcNm
					,	'sugaNm'	=>$sugaNm
					,	'count'		=>0
					,	'time'		=>$procTime
					,	'timeTot'	=>0
					,	'cost'		=>$suga['cost']
					,	'amt'		=>0
					,	'suga'		=>0
					,	'expense'	=>0
					,	'over'		=>0
					,	'bipay'		=>0
				);
			}

			if ($row['svc_cd'] == '0'){
				if ($row['bipay_yn'] != 'Y'){
					//본인부담율
					if (is_array($arrClientRate[$tmpJumin])){
						foreach($arrClientRate[$tmpJumin] as $idx => $arr){
							if ($row['date'] >= $arr['from'] && $row['date'] <= $arr['to']){
								$rate = $arr['rate'];
								break;
							}
						}
					}else{
						$rate = $arrClientInfo[$tmpJumin.'_'.$row['svc_cd']]['rate'];
					}

					//급여
					if ($liTotalPay[$key1] + $row['suga_tot'] >= $liLimitPay[$row['jumin']]){
						$liVal = $liLimitPay[$row['jumin']] - $liTotalPay[$key1];

						if ($liVal > 0){
							$arrDtl[$key1][$key2]['over']		+= $row['suga_tot'] - $liVal;
							$arrDtl[$key1][$key2]['suga']		+= $liVal;
							$arrDtl[$key1][$key2]['expense']	+= ($liVal * $rate / 100);
						}else{
							$arrDtl[$key1][$key2]['over']	+= $row['suga_tot'];
						}
					}else{
						$arrDtl[$key1][$key2]['suga']		+= $row['suga_tot'];
						$arrDtl[$key1][$key2]['expense']	+= ($row['suga_tot'] * $rate / 100);
					}
					$liTotalPay[$key1]	+= $row['suga_tot'];
				}else{
					//비급여
					$arrDtl[$key1][$key2]['bipay']	+= $row['suga_tot'];
				}
			}else if ($row['svc_cd'] >= '1' && $row['svc_cd'] <= '4'){
				//바우처
				$arrDtl[$key1][$key2]['suga']	+= $row['suga_tot'];
			}else if ($row['svc_cd'] == '6' || $row['svc_cd'] == 'S' || $row['svc_cd'] == 'R'){
				//재가지원
				$arrDtl[$key1][$key2]['suga']	+= $row['suga_tot'];
			}else{
				//기타비급여
				$arrDtl[$key1][$key2]['bipay']	+= $row['suga_tot'];
			}

			$arrDtl[$key1][$key2]['count'] ++;
			$arrDtl[$key1][$key2]['timeTot']+= $procTime;
			$arrDtl[$key1][$key2]['amt']	+= $row['suga_tot'];

			$liSeq ++;
		}


	}else if ($mode == '102'){
		//일정표(직원)
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if ($tmpJumin != $row['mem_cd']){
				$tmpJumin  = $row['mem_cd'];
			}

			$day = IntVal(Date('d',StrToTime($row['date'])));

			if ($tmpDay != $day){
				$tmpDay  = $day;
				$liSeq   = 0;
			}

			$workTime	= $myF->timeStyle($row['from_time']).'~'.$myF->timeStyle($row['to_time']);

			//직원일정 내역
			$arrCaln[$row['mem_cd']][$_POST['cboSvcYn'] == 'A' ? '0' : $row['svc_cd']][$day][$liSeq] = Array(
					'svcCd'		=>$row['svc_cd']
				,	'subCd'		=>$row['sub_cd']
				,	'name'		=>$row['name']
				,	'procTime'	=>$row['proc_time']
				,	'familyYn'	=>$row['family_yn']
				,	'bipayYn'	=>$row['bipay_yn']
				,	'workTime'	=>$workTime
			);

			//제공서비스 정보
			if ($row['svc_cd'] == '0'){
				if ($row['sub_cd'] == '200'){
					$procTime = $myF->cutOff($row['proc_time'],30);
				}else if ($row['sub_cd'] == '500'){
					if ($row['proc_time'] >= 60){
						$procTime	= 60;
					}else if ($row['proc_time'] >= 40){
						$procTime	= 40;
					}else{
						$procTime	= 0;
					}
				}else{
					$procTime = $row['proc_time'];
				}
			}else{
				$procTime = $row['proc_time'];
			}

			if ($row['sub_cd'] == '500'){
				if ($procTime > 60){
					$procTime = 60;
				}
			}

			if (StrLen($procTime) < 3){
				$time = '0'.$procTime;
			}else{
				$time = $procTime;
			}

			$key1 = $row['mem_cd'];
			$key2 = $row['svc_cd'].'_'.$row['sub_cd'].'_'.$time.'_'.$row['name'];

			if (!IsSet($arrSvc[$key1][$key2])){
				$svcNm  = lfGetSvcNm($row['svc_cd'],$row['sub_cd'],$row['family_yn']);
				$svcNm .= '/'.lfGetSvcSugaNm($row['sub_cd'],$row['suga_cd'],$procTime);

				$arrSvc[$key1][$key2] = Array(
						'key'		=>$key2
					,	'svcNm'		=>$svcNm
					,	'procTime'	=>$procTime
					,	'memNm'		=>$row['name']
					,	'day'		=>''
				);
			}

			if (!Is_Numeric(StrPos($arrSvc[$key1][$key2]['day'], '/'.$day))){
				$arrSvc[$key1][$key2]['day'] .= '/'.$day;
			}

			//제공서비스 금액내역
			$key2 = $row['suga_cd'];
			$key3 = $row['svc_cd'].'_'.$row['sub_cd'].'_'.$time;

			if (!IsSet($arrDtl[$key1][$key2])){
				$svcNm	= lfGetSvcFullNm($row['svc_cd']);
				$sugaNm	= '';

				foreach($arrSuga as $idx => $suga){
					if ($suga['cd'] == $row['suga_cd']){
						$sugaNm = $suga['nm'];
						break;
					}
				}

				$arrDtl[$key1][$key2] = Array(
						'key'		=>$key3
					,	'svcNm'		=>$svcNm
					,	'sugaNm'	=>$sugaNm
					,	'cost'		=>$suga['cost']
					,	'suga'		=>$suga['cost']
					,	'count'		=>0
					,	'time'		=>$procTime
					,	'timeTot'	=>0
					,	'amt'		=>0
				);
			}

			$arrDtl[$key1][$key2]['count']	++;
			$arrDtl[$key1][$key2]['timeTot']+= $procTime;
			$arrDtl[$key1][$key2]['amt']	+= $row['suga_tot'];

			$liSeq ++;
		}

	}

	$conn->row_free();

	UnSet($tmpJumin);


	/******************************************/



	if ($mode == '101'){
		$title = $year."년 ".$month."월 서비스 일정표(수급자)";
	}else if ($mode == '102'){
		$title = $year."년 ".$month."월 근무현황 일정표(요양보호사)";
	}


	$objPHPExcel = new PHPExcel();
	/*
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle($title);
	//$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$sheet->getPageMargins()->setTop(1.5);
	$sheet->getPageMargins()->setRight(0.0);
	$sheet->getPageMargins()->setLeft(0.0);
	$sheet->getPageMargins()->setBottom(0.5);
	$sheet->getPageSetup()->setHorizontalCentered(true);
	*/


	//바닥글
	$footer = "&C&14&\"Bold\"".$_SESSION['userCenterName'];
	$sql = 'SELECT m00_ctel AS phone
			,      m00_mkind AS svc_cd
			,      m00_bank_no AS bank_no
			,      CASE m00_bank_name WHEN \'001\' then \'한국은행\'
									  WHEN \'002\' then \'산업은행\'
									  WHEN \'003\' then \'기업은행\'
									  WHEN \'004\' then \'국민은행\'
									  WHEN \'005\' then \'외환은행\'
									  WHEN \'007\' then \'수협중앙회\'
									  WHEN \'008\' then \'수출입은행\'
									  WHEN \'011\' then \'농협중앙회\'
									  WHEN \'012\' then \'농협회원조합\'
									  WHEN \'020\' then \'우리은행\'
									  WHEN \'023\' then \'SC제일은행\'
									  WHEN \'027\' then \'한국씨티은행\'
									  WHEN \'031\' then \'대구은행\'
									  WHEN \'032\' then \'부산은행\'
									  WHEN \'034\' then \'광주은행\'
									  WHEN \'035\' then \'제주은행\'
									  WHEN \'037\' then \'전북은행\'
									  WHEN \'039\' then \'경남은행\'
									  WHEN \'045\' then \'새마을금고연합회\'
									  WHEN \'048\' then \'신협중앙회\'
									  WHEN \'050\' then \'상호저축은행\'
									  WHEN \'071\' then \'우체국\'
									  WHEN \'081\' then \'하나은행\'
									  WHEN \'088\' then \'신한은행\' else m00_bank_name end as bank_nm
			,      m00_bank_depos AS bank_depos
			  FROM m00center
			 WHERE m00_mcode  = \''.$orgNo.'\'
			   AND m00_del_yn = \'N\'
			 ORDER BY svc_cd
			 LIMIT	1';

	$row = $conn->get_array($sql);
	$footer .= "(".$myF->phoneStyle($row['phone'],'.').")";

	if ($mode == '101') $footer .= "\n&C&9입금계좌 : ".$row['bank_nm']."(".$row['bank_no'].")    예금주 : ".$row['bank_depos'];


	//일정 변수 설정
	$calTime	= mktime(0, 0, 1, $month, 1, $year);
	$today		= date('Ymd', mktime());
	$lastDay	= date('t', $calTime);										//총일수 구하기
	$startWeek	= date('w', strtotime(date('Y-m', $calTime).'-01'));		//시작요일 구하기
	$totalWeek	= ceil(($lastDay + $startWeek) / 7);						//총 몇 주인지 구하기
	$lastWeek	= date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay));	//마지막 요일 구하기

	if ($_POST['calnYn'] == 'Y'){
		//주별 높이
		foreach($arrCaln as $jumin => $caln1){
			foreach($arrCaln[$jumin] as $svcCd => $caln2){
				$day = 1;

				for($i=1; $i<=$totalWeek; $i++){
					for($j=0; $j<7; $j++){
						if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
							$liCnt = SizeOf($arrCaln[$jumin][$svcCd][$day]);

							if ($liCheckCnt[$jumin][$svcCd][$i] < $liCnt){
								$liCheckCnt[$jumin][$svcCd][$i] = $liCnt;
							}

							$day++;
						}
					}
				}
			}
		}
	}


	//일정
	$sheetIndex = 0;
	foreach($arrCaln as $jumin => $caln){
		foreach($arrCaln[$jumin] as $svcCd => $caln){
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			$sheet = $objPHPExcel->getActiveSheet();
			$sheetIndex ++;


			if ($svcCd == '0'){
				$id = $jumin.'_'.$svcCd;
			}else if ($svcCd == '6' || $svcCd == 'S' || $svcCd == 'R'){
				$id = $jumin.'_6';
			}else{
				$id = $jumin.'_'.$svcCd;
			}

			if ($mode == '101'){
				$sheetName = $arrClientInfo[$id]['name'];
			}else if ($mode == '102'){
				$sheetName = $arrMemberInfo[$jumin]['name'];
			}


			$sheet->setTitle($sheetName);
			//$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
			$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$sheet->getPageMargins()->setTop(1.5);
			$sheet->getPageMargins()->setRight(0.0);
			$sheet->getPageMargins()->setLeft(0.0);
			$sheet->getPageMargins()->setBottom(0.5);
			$sheet->getPageSetup()->setHorizontalCentered(true);
			//$sheet->getHeaderFooter()->setOddFooter("&C&14&\"Bold\"(주)케어비지트(070.4044.1312)\n&C&9입금계좌:기업은행(803-215-00151-2) 예금주:(주)굿이오스1");
			$sheet->getHeaderFooter()->setOddFooter($footer);


			//스타일
			$lastCol = 40;
			$widthCol= 2.5;
			include("../excel/init.php");
			include_once("../excel/style.php");
			$sheet->getColumnDimension("AO")->setWidth(4.5);


			//기본설정
			$objPHPExcel->getDefaultStyle()->getFont()->setName("Batang"); //Gulim


			//초기화
			$defFontSize = 9;
			$rowH = 15;
			$rowNo = 0;


			$fontSize = 13;
			$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
			$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

			$fontSize = $defFontSize;
			$rH = $rowH * $fontSize / $defFontSize;


			//타이틀 및 결재란
				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight($rH);
				$sheet->getRowDimension($rowNo+1)->setRowHeight($rH);
				$sheet->getRowDimension($rowNo+2)->setRowHeight($rH);
				$sheet->getRowDimension($rowNo+3)->setRowHeight($rH);

				if ($signCnt == 0){
					$cellT = 'AO';
				}else{
					$cellT = 'X';
				}
				$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>$cellT.($rowNo+3), 'val'=>$title, 'border'=>'TNRNBNLN' ) );

				$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
				$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

				if ($signCnt != ''){
					if ($signCnt == 1){
						$firstF = 'AI';
					}else if ($signCnt == 2){
						$firstF = 'AD';
					}else if ($signCnt == 3 || $signCnt == 4){
						$firstF = 'AB';
					}else{
						$firstF = 'Y';
					}

					if ($signCnt == 3){
						$cellCnt = 3;
					}else if ($signCnt == 4 || $signCnt == 5){
						$cellCnt = 2;
					}else{
						$cellCnt = 4;
					}

					$cellF = $firstF;
					$cellT = GetNextCellId($cellF, 1);
					$sheet->SetData( Array( 'F'=>$cellF.$rowNo, 'T'=>$cellT.($rowNo+3), 'val'=>"결\n재" ) );

					for($i=0; $i<$signCnt; $i++){
						$cellF = GetNextCellId($cellT);
						$cellT = GetNextCellId($cellF, $cellCnt);
						$sheet->SetData( Array( 'F'=>$cellF.$rowNo, 'T'=>$cellT.$rowNo, 'val'=>$signTxt[$i] ) );
					}

					$rowNo ++;
					$cellT = GetNextCellId($firstF, 1);
					for($i=0; $i<$signCnt; $i++){
						$cellF = GetNextCellId($cellT);
						$cellT = GetNextCellId($cellF, $cellCnt);
						$sheet->SetData( Array( 'F'=>$cellF.$rowNo, 'T'=>$cellT.($rowNo+2) ) );
					}
				}else{
					$sheet->SetData( Array('F'=>'Z'.$rowNo, 'T'=>'AC'.($rowNo+3), 'val'=>"결\n\n재") );
					$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AG'.$rowNo, 'val'=>"담  당") );
					$sheet->SetData( Array('F'=>'AH'.$rowNo, 'T'=>'AK'.$rowNo, 'val'=>"") );
					$sheet->SetData( Array('F'=>'AL'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>"기관장") );

					$rowNo ++;
					$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AG'.($rowNo+2), 'val'=>"") );
					$sheet->SetData( Array('F'=>'AH'.$rowNo, 'T'=>'AK'.($rowNo+2), 'val'=>"") );
					$sheet->SetData( Array('F'=>'AL'.$rowNo, 'T'=>'AO'.($rowNo+2), 'val'=>"") );
				}


			//구분공란
				$rowNo ++;
				$rowNo ++;
				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight($rH * 0.3);


			//고객정보
			if ($mode == '101'){
				//수급자정보
				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight($rH);
				$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>"수급자명", 'border'=>'LT', 'backcolor'=>'EAEAEA') );
				$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'P'.$rowNo, 'val'=>"주민등록번호", 'border'=>'T', 'backcolor'=>'EAEAEA') );
				$sheet->SetData( Array('F'=>'Q'.$rowNo, 'T'=>'Z'.$rowNo, 'val'=>"장기요양인정번호", 'border'=>'T', 'backcolor'=>'EAEAEA') );
				$sheet->SetData( Array('F'=>'AA'.$rowNo, 'T'=>'AF'.$rowNo, 'val'=>"등급", 'border'=>'T', 'backcolor'=>'EAEAEA') );
				$sheet->SetData( Array('F'=>'AG'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>"본인부담율", 'border'=>'TR', 'backcolor'=>'EAEAEA') );

				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight($rH);
				$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>$arrClientInfo[$id]['name'], 'border'=>'LB') ); //수급자명
				$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'P'.$rowNo, 'val'=>$myF->issStyle($arrClientInfo[$id]['jumin']), 'border'=>'B') );
				$sheet->SetData( Array('F'=>'Q'.$rowNo, 'T'=>'Z'.$rowNo, 'val'=>SubStr($arrClientInfo[$id]['app_no'],0,StrLen($arrClientInfo[$id]['app_no'])-4).'****', 'border'=>'B') );
				$sheet->SetData( Array('F'=>'AA'.$rowNo, 'T'=>'AF'.$rowNo, 'val'=>$arrClientInfo[$id]['lvl'], 'border'=>'B') );
				$sheet->SetData( Array('F'=>'AG'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>$arrClientInfo[$id]['kind'].'/'.$arrClientInfo[$id]['rate'], 'border'=>'BR') );

			}else if ($mode == '102'){
				//요양보호사정보
				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight($rH);
				$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>"직원명", 'border'=>'LT', 'backcolor'=>'EAEAEA') );
				$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'P'.$rowNo, 'val'=>"연락처", 'border'=>'T', 'backcolor'=>'EAEAEA') );
				$sheet->SetData( Array('F'=>'Q'.$rowNo, 'T'=>'Z'.$rowNo, 'val'=>"", 'border'=>'T', 'backcolor'=>'EAEAEA') );
				$sheet->SetData( Array('F'=>'AA'.$rowNo, 'T'=>'AF'.$rowNo, 'val'=>"", 'border'=>'T', 'backcolor'=>'EAEAEA') );
				$sheet->SetData( Array('F'=>'AG'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>"", 'border'=>'TR', 'backcolor'=>'EAEAEA') );

				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight($rH);
				$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>$arrMemberInfo[$jumin]['name'], 'border'=>'LB') ); //수급자명
				$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'P'.$rowNo, 'val'=>$myF->phoneStyle($arrMemberInfo[$jumin]['phone'],'.'), 'border'=>'B') );
				$sheet->SetData( Array('F'=>'Q'.$rowNo, 'T'=>'Z'.$rowNo, 'border'=>'B') );
				$sheet->SetData( Array('F'=>'AA'.$rowNo, 'T'=>'AF'.$rowNo, 'border'=>'B') );
				$sheet->SetData( Array('F'=>'AG'.$rowNo, 'T'=>'AO'.$rowNo, 'border'=>'BR') );
			}

			//구분공란
				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight($rH * 0.3);


			if ($_POST['calnYn'] == 'Y'){
				$day = 1; //화면에 표시할 화면의 초기값을 1로 설정

				for($i=1; $i<=$totalWeek; $i++){
					if ($i == 1){
						//요일
						$rowNo ++;
						$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);
						$sheet->getRowDimension($rowNo)->setRowHeight($rH);
						$sheet->SetData( Array( 'F'=>'A'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>"일", 'color'=>'FF0000', 'border'=>'LT', 'backcolor'=>'EAEAEA' ) );
						$sheet->SetData( Array( 'F'=>'G'.$rowNo, 'T'=>'L'.$rowNo, 'val'=>"월", 'border'=>'T', 'backcolor'=>'EAEAEA' ) );
						$sheet->SetData( Array( 'F'=>'M'.$rowNo, 'T'=>'R'.$rowNo, 'val'=>"화", 'border'=>'T', 'backcolor'=>'EAEAEA' ) );
						$sheet->SetData( Array( 'F'=>'S'.$rowNo, 'T'=>'X'.$rowNo, 'val'=>"수", 'border'=>'T', 'backcolor'=>'EAEAEA' ) );
						$sheet->SetData( Array( 'F'=>'Y'.$rowNo, 'T'=>'AD'.$rowNo, 'val'=>"목", 'border'=>'T', 'backcolor'=>'EAEAEA' ) );
						$sheet->SetData( Array( 'F'=>'AE'.$rowNo, 'T'=>'AJ'.$rowNo, 'val'=>"금", 'border'=>'T', 'backcolor'=>'EAEAEA' ) );
						$sheet->SetData( Array( 'F'=>'AK'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>"토", 'color'=>'0000FF', 'border'=>'TR', 'backcolor'=>'EAEAEA' ) );
						$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
						$rH = $rowH * $fontSize / $defFontSize * 0.8;
					}

					$rowNo ++;
					$sheet->getRowDimension($rowNo)->setRowHeight($rH);
					$rowId = $rowNo;


					//총 가로칸 만들기
					for($j=0; $j<7; $j++){
						if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
							$date = $yymm.($day < 10 ? '0' : '').$day;
							$rowNo = $rowId;

							if ($j == 0){//일요일
								$fontColor = 'FF0000'; //붉은색
							}else if ($j == 6){//토요일
								$fontColor = '0000FF'; //파란색
							}else{//평일
								$fontColor = '000000'; //검정색
							}

							//기념일
							if (!Empty($arrHoliday[$date]['name'])){
								if ($date == $year.'0501'){
									$fontColor = '0000FF'; //파란색
								}else{
									$fontColor = 'FF0000'; //붉은색
								}
							}

							$cellId = $j * 6;
							$cellIdF = GetNextCellId('A',$cellId);
							$cellIdT = GetNextCellId($cellIdF);
							$sheet->SetData( Array( 'F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'val'=>$day, 'H'=>'L', 'color'=>$fontColor, 'border'=>($j == 0 ? 'L' : '').'RNBN' ) ); //일자

							$tmpF = GetNextCellId($cellIdT);
							$cellIdT = GetNextCellId($tmpF,$j == 6 ? 2 : 3);
							$sheet->SetData( Array( 'F'=>$tmpF.$rowNo, 'T'=>$cellIdT.$rowNo, 'val'=>$arrHoliday[$date]['name'], 'H'=>'R', 'color'=>$fontColor, 'border'=>($j == 6 ? 'R' : '').'LNBN' ) ); //기념일


							if (Is_Array($arrCaln[$jumin][$svcCd][$day])){
								foreach($arrCaln[$jumin][$svcCd][$day] as $seq => $caln){
									//서비스명
									if ($caln['svcCd'] == '6' || $caln['svcCd'] == 'S' || $caln['svcCd'] == 'R'){
										$svcNm = $caln['sugaNm'];
									}else{
										$svcNm = '['.lfGetSvcNm($caln['svcCd'],$caln['subCd'],$caln['familyYn']);

										if ($mode == '101'){
											if ($caln['subCd'] == '200' || $caln['subCd'] == '500'){
												//요양, 목욕 서비스 시간 표시
												$svcNm	.= '/'.$caln['procTime'].'분]';
											}else{
												//간호는 서비스명만 표시
												$svcNm	.= ']';
											}
										}else if ($mode == '102'){
											$svcNm	.= ']'.$caln['name'];
										}else{
											$svcNm	.= ']';
										}
									}

									//서비스
									$rowNo ++;
									$sheet->getRowDimension($rowNo)->setRowHeight($rH);
									$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'val'=>$svcNm, 'H'=>'L', 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').($seq > 0 ? 'TH' : 'TN').'BN') );

									if ($caln['subCd'] == '500'){
										$lsIconFile = '../image/icon_bath.jpg';
									}else if ($caln['subCd'] == '800'){
										$lsIconFile = '../image/icon_nurs.jpg';
									}else{
										$lsIconFile = '';
									}

									if ($lsIconFile){
										$objDrawing = new PHPExcel_Worksheet_Drawing();
										$objDrawing->setName('IMAGE');
										$objDrawing->setDescription('IMAGE');
										$objDrawing->setPath($lsIconFile);	// filesystem reference for the image file //'./images/phpexcel_logo.gif'
										$objDrawing->setHeight(13);
										$objDrawing->setCoordinates($cellIdT.$rowNo);	// pins the top-left corner of the image to cell D24
										$objDrawing->setOffsetX(11);	// pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
										$objDrawing->setOffsetY(2);
										$objDrawing->setWorksheet($sheet);
									}


									$rowNo ++;
									$sheet->getRowDimension($rowNo)->setRowHeight($rH);

									//근무시간
									if ($caln['svcCd'] == 'S' || $caln['svcCd'] == 'R'){
										$tmpF = $cellIdF;
										$tmpT = GetNextCellId($tmpF, 2);
										$sheet->SetData( Array('F'=>$tmpF.$rowNo, 'T'=>$tmpT.$rowNo, 'val'=>$caln['workTime'], 'H'=>'L', 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'RNTNBN') );

										$tmpF = GetNextCellId($tmpT);
										$tmpT = GetNextCellId($tmpT, $j == 6 ? 2 : 3);
										$sheet->SetData( Array('F'=>$tmpF.$rowNo, 'T'=>$tmpT.$rowNo, 'val'=>$caln['worker'], 'H'=>'R', 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'LNTNBN') );
									}else{
										$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'val'=>$seq.'/'.$caln['workTime'], 'H'=>'L', 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'TNBN') );
									}


									if ($mode == '101'){
										$rowNo ++;
										$sheet->getRowDimension($rowNo)->setRowHeight($rH);

										//고객명
										if ($caln['svcCd'] == 'S' || $caln['svcCd'] == 'R'){
											$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'val'=>$caln['resource'], 'H'=>'R', 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'TNBN') );
										}else{
											$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'val'=>$caln['worker'], 'H'=>'R', 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'TNBN') );
										}

										//담당직원 리스트
										if (!Is_Array($arrMemList[$caln['memCd1']])){
											$arrMemList[$caln['memCd1']] = Array(
													'name'	=>$arrMem[$caln['memCd1']]['name']
												,	'mobile'=>$arrMem[$caln['memCd1']]['mobile']
											);
										}

										if (!Empty($caln['memCd2'])){
											if (!Is_Array($arrMemList[$caln['memCd2']])){
												$arrMemList[$caln['memCd2']] = Array(
														'name'	=>$arrMem[$caln['memCd2']]['name']
													,	'mobile'=>$arrMem[$caln['memCd2']]['mobile']
												);
											}
										}
									}

									//비급여 표시
									if ($caln['bipayYn'] == 'Y'){
										$lsIconBipay	= '../image/btn/btn_bipay.gif';
									}else{
										$lsIconBipay	= '';
									}

									if ($lsIconBipay){
										$objDrawing = new PHPExcel_Worksheet_Drawing();
										$objDrawing->setName('IMAGE');
										$objDrawing->setDescription('IMAGE');
										$objDrawing->setPath($lsIconBipay);	// filesystem reference for the image file //'./images/phpexcel_logo.gif'
										$objDrawing->setHeight(13);
										$objDrawing->setCoordinates($cellIdF.$rowNo);	// pins the top-left corner of the image to cell D24
										$objDrawing->setOffsetX(2);	// pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
										$objDrawing->setOffsetY(2);
										$objDrawing->setWorksheet($sheet);
									}
								}

								if ($seq + 1 < $liCheckCnt[$jumin][$svcCd][$i]){
									for($k=$seq+1; $k<$liCheckCnt[$jumin][$svcCd][$i]; $k++){
										$rowNo ++;
										$sheet->getRowDimension($rowNo)->setRowHeight($rH);
										$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'TNBN') );

										$rowNo ++;
										$sheet->getRowDimension($rowNo)->setRowHeight($rH);
										$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'TNBN') );

										if ($mode == '101'){
											$rowNo ++;
											$sheet->getRowDimension($rowNo)->setRowHeight($rH);
											$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'TNBN') );
										}
									}
								}
							}else{
								//서비스 공란
								if (!$liCheckCnt[$jumin][$svcCd][$i]) $liCheckCnt[$jumin][$svcCd][$i] = 1;
								for($k=0; $k<$liCheckCnt[$jumin][$svcCd][$i]; $k++){
									$rowNo ++;
									$sheet->getRowDimension($rowNo)->setRowHeight($rH);
									$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'TNBN') );

									$rowNo ++;
									$sheet->getRowDimension($rowNo)->setRowHeight($rH);
									$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'TNBN') );

									if ($mode == '101'){
										$rowNo ++;
										$sheet->getRowDimension($rowNo)->setRowHeight($rH);
										$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'TNBN') );
									}
								}
							}

							$day ++;
						}else{
							$rowNo = $rowId;

							//일자 공란
							$cellId = $j * 6;
							$cellIdF = GetNextCellId('A',$cellId);
							$cellIdT = GetNextCellId($cellIdF,$j == 6 ? 4 : 5);
							$sheet->SetData( Array( 'F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'BN' ) ); //공란

							$rowNo ++;
							$sheet->getRowDimension($rowNo)->setRowHeight($rH);
							$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'TNBN') );

							$rowNo ++;
							$sheet->getRowDimension($rowNo)->setRowHeight($rH);
							$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'TNBN') );

							if ($mode == '101'){
								$rowNo ++;
								$sheet->getRowDimension($rowNo)->setRowHeight($rH);
								$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'border'=>($j == 0 ? 'L' : '').($j == 6 ? 'R' : '').'TN') );
							}
						}
					}
				}

				$rH = $rowH * $fontSize / $defFontSize;

				//구분공란
				$rowNo ++;
				$cellIdF = GetNextCellId();
				$cellIdT = GetNextCellId($cellIdF,$lastCol);
				$sheet->getRowDimension($rowNo)->setRowHeight($rH * 0.3);
				$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'border'=>'TRNBNLN') );


				if ($mode == '101'){
					//담당직원 리스트
					foreach($arrMemList as $memCd => $arrMemInfo){
						$lsTel = $myF->phoneStyle($arrMemInfo['mobile'],'.');

						if (!Empty($memStr)){
							$memStr .= ' / ';
						}

						$memStr .= $arrMemInfo['name'];

						if (!Empty($lsTel)){
							$memStr .= '('.$lsTel.')';
						}
					}

					$rowNo ++;
					$sheet->getRowDimension($rowNo)->setRowHeight($rH);
					$sheet->SetData( Array('F'=>$cellIdF.$rowNo, 'T'=>$cellIdT.$rowNo, 'val'=>'담당 : '.$memStr, 'H'=>'L') );
				}
			}

			//담당직원 리스트 초기화
			UnSet($memStr);
			UnSet($arrMemList);


			//공란
			$rowNo ++;
			$sheet->getRowDimension($rowNo)->setRowHeight($rH * 0.3);


			//제공서비스
			$rowNo ++;
			$sheet->getRowDimension($rowNo)->setRowHeight($rH);
			$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'E'.$rowNo, 'val'=>'제공서비스', 'backcolor'=>'EAEAEA') );
			$sheet->SetData( Array('F'=>'F'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>'담당자명', 'backcolor'=>'EAEAEA') );
			$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'AM'.$rowNo, 'val'=>'제공서비스 / 제공일', 'backcolor'=>'EAEAEA') );
			$sheet->SetData( Array('F'=>'AN'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>'횟수', 'backcolor'=>'EAEAEA') );

			if ($mode == '101'){
				$key = $jumin.'_'.$svcCd;
			}else{
				$key = $jumin;
			}

			if ($_POST['dtlYn'] != 'N'){
				//제공서비스 타이틀
				if (Is_Array($arrTme[$key])){
					$arrTme[$key] = $myF->sortArray($arrTme[$key], 'key');

					foreach($arrTme[$key] as $key1 => $arrSub){
						$lsSvc	= Explode('_',$key);
						$lsSvc	= $lsSvc[1];
						$days	= $arrSub['day'].'/';
						$lsCnt	= Number_Format(SizeOf(Explode('/',$arrSub['day']))-1);

						if ($pdf->GetY()+$liH > $pdf->height){
							//페이지추가
							$pdf->AddPage(strtoupper($_POST['dir']), 'A4');

							//타이틀 출력
							lfGetSvcTitle($pdf, $col);
						}

						$workTime	= Explode('~',$arrSub['workTime']);
						$worker		= Explode('/',$arrSub['worker']);

						$pdf->SetX($pdf->left);
						$pdf->Cell($col['svcWidth'][0],$liH,$workTime[0],"LTR",0,'L');
						$pdf->Cell($col['svcWidth'][1],$liH,$worker[0],"LTR",0,'L');

						if ($_POST['dtlYn'] == 'A'){
							$pdf->Cell($col['svcWidth'][2],$liH,$arrSub['svcNm'].'  |  단가: '.Number_Format($arrSub['sugaCost']).'  |  소계: '.Number_Format($arrSub['sugaTot']),"LTR",0,'L');
						}else{
							$pdf->Cell($col['svcWidth'][2],$liH,$arrSub['svcNm'],"LTR",0,'L');
						}

						$pdf->Cell($col['svcWidth'][3],$liH*2,$lsCnt,1,1,'C');

						$pdf->SetXY($pdf->left,$pdf->GetY()-$liH);

						if ($lsSvc == 'S' || $lsSvc == 'R'){
							$pdf->Cell($col['svcWidth'][0],$liH,"","LBR",0,'L');
							$pdf->Cell($col['svcWidth'][1],$liH,"","LBR",0,'L');
						}else{
							$pdf->Cell($col['svcWidth'][0],$liH,"~".$workTime[1],"LBR",0,'L');
							$pdf->Cell($col['svcWidth'][1],$liH,$worker[1],"LBR",0,'L');
						}
						$pdf->Cell($col['svcWidth'][2],$liH,"","LBR",1,'L');

						lfDrawDays($pdf,$col['svcWidth'],$lastDay,$days,$liH);
					}
				}
			}else{
				//제공서비스 타이틀
				if (Is_Array($arrSvc[$key])){
					$arrSvc[$key] = $myF->sortArray($arrSvc[$key], 'key');

					foreach($arrSvc[$key] as $key1 => $arrSub){
						$days	= $arrSub['day'].'/';
						$lsCnt	= Number_Format(SizeOf(Explode('/',$arrSub['day']))-1);
						$lsDay	= '';

						for($k=1; $k<=$lastDay; $k++){
							if (Is_Numeric(StrPos($days,'/'.$k.'/'))){
								$lsDay .= ($lsDay ? ', ' : '').$k;
							}
						}

						$rowNo ++;
						$sheet->getRowDimension($rowNo)->setRowHeight($rH);
						$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'E'.$rowNo, 'val'=>$arrSub['svcNm'], 'H'=>'L') );
						$sheet->SetData( Array('F'=>'F'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>$arrSub['memNm'], 'H'=>'L') );
						$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'AM'.$rowNo, 'val'=>$lsDay, 'H'=>'L') );
						$sheet->SetData( Array('F'=>'AN'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>$lsCnt) );
					}
				}
			}


			//공란
			$rowNo ++;
			$sheet->getRowDimension($rowNo)->setRowHeight($rH * 0.3);


			//제공서비스 상세
			if ($_POST['useType'] == 'Y'){
				if (Is_Array($arrDtl[$key])){
					//타이틀 출력
					$rowNo ++;
					$sheet->getRowDimension($rowNo)->setRowHeight($rH);
					$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>'급여종류', 'backcolor'=>'EAEAEA') );
					$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>'서비스명', 'backcolor'=>'EAEAEA') );
					$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'M'.$rowNo, 'val'=>'횟수', 'backcolor'=>'EAEAEA') );
					$sheet->SetData( Array('F'=>'N'.$rowNo, 'T'=>'P'.$rowNo, 'val'=>'시간', 'backcolor'=>'EAEAEA') );
					$sheet->SetData( Array('F'=>'Q'.$rowNo, 'T'=>'S'.$rowNo, 'val'=>'수가', 'backcolor'=>'EAEAEA') );
					$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'W'.$rowNo, 'val'=>'총금액', 'backcolor'=>'EAEAEA') );

					if ($mode == '101'){
						$sheet->SetData( Array('F'=>'X'.$rowNo, 'T'=>'AA'.$rowNo, 'val'=>'급여총액', 'backcolor'=>'EAEAEA') );
						$sheet->SetData( Array('F'=>'AB'.$rowNo, 'T'=>'AE'.$rowNo, 'val'=>'본인부담', 'backcolor'=>'EAEAEA') );
						$sheet->SetData( Array('F'=>'AF'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>'초과', 'backcolor'=>'EAEAEA') );
						$sheet->SetData( Array('F'=>'AI'.$rowNo, 'T'=>'AK'.$rowNo, 'val'=>'비급여', 'backcolor'=>'EAEAEA') );
						$sheet->SetData( Array('F'=>'AL'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>'총부담액', 'backcolor'=>'EAEAEA') );
					}else{
						$sheet->SetData( Array('F'=>'X'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>'비고', 'backcolor'=>'EAEAEA') );
					}

					if ($mode == '101'){
						$key = $jumin.'_'.$svcCd;
					}else{
						$key = $jumin;
					}

					$arrDtlTmp = $myF->sortArray($arrDtl[$key], 'key');

					$liTime		= 0;
					$liAmt		= 0;
					$liSuag		= 0;
					$liExpense	= 0;
					$liOver		= 0;
					$liBipay	= 0;

					foreach($arrDtlTmp as $key1 => $arrSub){
						$rowNo ++;
						$sheet->getRowDimension($rowNo)->setRowHeight($rH);
						$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>$arrSub['svcNm'], 'H'=>'L') );
						$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>$arrSub['sugaNm'], 'H'=>'L') );
						$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'M'.$rowNo, 'val'=>$arrSub['count'], 'H'=>'R') );
						$sheet->SetData( Array('F'=>'N'.$rowNo, 'T'=>'P'.$rowNo, 'val'=>$myF->_min2timeKor($arrSub['time']), 'H'=>'R') );
						$sheet->SetData( Array('F'=>'Q'.$rowNo, 'T'=>'S'.$rowNo, 'val'=>$arrSub['cost'], 'H'=>'R', 'format'=>'#,##0') );


						if ($mode == '101'){
							$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'W'.$rowNo, 'val'=>$arrSub['amt'], 'H'=>'R', 'format'=>'#,##0') );
							$sheet->SetData( Array('F'=>'X'.$rowNo, 'T'=>'AA'.$rowNo, 'val'=>$arrSub['suga'], 'H'=>'R', 'format'=>'#,##0') );

							if ($orgNo == '31126000192'){
								//가톨릭노인복지센터
								$sheet->SetData( Array('F'=>'AB'.$rowNo, 'T'=>'AE'.$rowNo, 'val'=>($arrSub['suga'] - $arrSub['expense']), 'format'=>'#,##0') );
								$sheet->SetData( Array('F'=>'AF'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>$arrSub['expense'], 'H'=>'R', 'format'=>'#,##0') );
								$sheet->SetData( Array('F'=>'AI'.$rowNo, 'T'=>'AK'.$rowNo, 'val'=>$arrSub['over'], 'H'=>'R', 'format'=>'#,##0') );
								$sheet->SetData( Array('F'=>'AL'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>$arrSub['bipay'], 'H'=>'R', 'format'=>'#,##0') );
							}else{
								$sheet->SetData( Array('F'=>'AB'.$rowNo, 'T'=>'AE'.$rowNo, 'val'=>$arrSub['expense'], 'H'=>'R', 'format'=>'#,##0') );
								$sheet->SetData( Array('F'=>'AF'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>$arrSub['over'], 'H'=>'R', 'format'=>'#,##0') );
								$sheet->SetData( Array('F'=>'AI'.$rowNo, 'T'=>'AK'.$rowNo, 'val'=>$arrSub['bipay'], 'H'=>'R', 'format'=>'#,##0') );
								$sheet->SetData( Array('F'=>'AL'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>($arrSub['expense']+$arrSub['over']+$arrSub['bipay']), 'H'=>'R', 'format'=>'#,##0') );
							}

						}else if ($mode == '102'){
							$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'W'.$rowNo, 'val'=>$arrSub['amt'], 'H'=>'R', 'format'=>'#,##0') );
							$sheet->SetData( Array('F'=>'X'.$rowNo, 'T'=>'AO'.$rowNo) );
						}

						$liTime		+= $arrSub['timeTot'];
						$liAmt		+= $arrSub['amt'];
						$liSuag		+= $arrSub['suga'];
						$liExpense	+= $arrSub['expense'];
						$liOver		+= $arrSub['over'];
						$liBipay	+= $arrSub['bipay'];
					}

					if ($mode == '101'){
						$liExpense	= $myF->cutOff($liExpense);

						$rowNo ++;
						$sheet->getRowDimension($rowNo)->setRowHeight($rH);
						$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'M'.$rowNo, 'val'=>'합계', 'backcolor'=>'EAEAEA', 'H'=>'R') );
						$sheet->SetData( Array('F'=>'N'.$rowNo, 'T'=>'P'.$rowNo, 'val'=>$myF->_min2timeKor($liTime), 'backcolor'=>'EAEAEA', 'H'=>'R') );
						$sheet->SetData( Array('F'=>'Q'.$rowNo, 'T'=>'W'.$rowNo, 'val'=>$liAmt, 'backcolor'=>'EAEAEA', 'H'=>'R', 'format'=>'#,##0') );
						$sheet->SetData( Array('F'=>'X'.$rowNo, 'T'=>'AA'.$rowNo, 'val'=>$liSuag, 'backcolor'=>'EAEAEA', 'H'=>'R', 'format'=>'#,##0') );

						if ($orgNo == '31126000192'){
							$sheet->SetData( Array('F'=>'AB'.$rowNo, 'T'=>'AE'.$rowNo, 'val'=>($liSuag - $liExpense), 'backcolor'=>'EAEAEA', 'H'=>'R', 'format'=>'#,##0') );
							$sheet->SetData( Array('F'=>'AF'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>$liExpense, 'backcolor'=>'EAEAEA', 'H'=>'R', 'format'=>'#,##0') );
							$sheet->SetData( Array('F'=>'AI'.$rowNo, 'T'=>'AK'.$rowNo, 'val'=>$liOver, 'backcolor'=>'EAEAEA', 'H'=>'R', 'format'=>'#,##0') );
							$sheet->SetData( Array('F'=>'AL'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>$liBipay, 'backcolor'=>'EAEAEA', 'H'=>'R', 'format'=>'#,##0') );
						}else{
							$sheet->SetData( Array('F'=>'AB'.$rowNo, 'T'=>'AE'.$rowNo, 'val'=>$liExpense, 'backcolor'=>'EAEAEA', 'H'=>'R', 'format'=>'#,##0') );
							$sheet->SetData( Array('F'=>'AF'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>$liOver, 'backcolor'=>'EAEAEA', 'H'=>'R', 'format'=>'#,##0') );
							$sheet->SetData( Array('F'=>'AI'.$rowNo, 'T'=>'AK'.$rowNo, 'val'=>$liBipay, 'backcolor'=>'EAEAEA', 'H'=>'R', 'format'=>'#,##0') );
							$sheet->SetData( Array('F'=>'AL'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>($liExpense+$liOver+$liBipay), 'backcolor'=>'EAEAEA', 'H'=>'R', 'format'=>'#,##0') );
						}

					}else if ($mode == '102'){
						$rowNo ++;
						$sheet->getRowDimension($rowNo)->setRowHeight($rH);
						$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'M'.$rowNo, 'val'=>'합계', 'backcolor'=>'EAEAEA', 'H'=>'R') );
						$sheet->SetData( Array('F'=>'N'.$rowNo, 'T'=>'P'.$rowNo, 'val'=>$myF->_min2timeKor($liTime), 'backcolor'=>'EAEAEA', 'H'=>'R') );
						$sheet->SetData( Array('F'=>'Q'.$rowNo, 'T'=>'W'.$rowNo, 'val'=>$liAmt, 'backcolor'=>'EAEAEA', 'H'=>'R', 'format'=>'#,##0') );
						$sheet->SetData( Array('F'=>'X'.$rowNo, 'T'=>'AO'.$rowNo) );
					}

					UnSet($arrDtlTmp);
				}
			}

			/*
			//직인출력
			if ($mode == '101'){
				if ($pdf->svcCd == '4'){
					if (Empty($_POST['printDT'])){
						$_POST['printDT'] = Date('Y-m-d');
					}

					$prtDt = Explode('-',$_POST['printDT']);

					$pdf->SetXY($pdf->left,$pdf->GetY()+5);
					$pdf->Cell($pdf->width,$pdf->row_height, $prtDt[0].'년 '.$prtDt[1].'월 '.$prtDt[2].'일', 0, 1, 'C');

					//직인 출력
					$sql = 'SELECT m00_mname AS name
							,      m00_jikin AS jikin
							  FROM m00center
							 WHERE m00_mcode = \''.$orgNo.'\'
							 LIMIT 1';

					$arrMst = $conn->get_array($sql);

					if (!empty($arrMst['jikin'])){
						$tmpImg = getImageSize('../mem_picture/'.$arrMst['jikin']);
						$pdf->Image('../mem_picture/'.$arrMst['jikin'], $pdf->width - 18, $pdf->GetY() - 10, 21);
					}

					$pdf->SetXY($pdf->left + $pdf->width * 0.5, $pdf->GetY() + 2);
					$pdf->Cell($pdf->width * 0.5, $pdf->row_height, '기관장 : '.$arrMst['name'].'                            ', 0, 1, 'R');
					$pdf->SetXY($pdf->left + $pdf->width * 0.5, $pdf->GetY() + 2);
					$pdf->Cell($pdf->width * 0.5, $pdf->row_height, '수급자 : '.$col['clientValue'][0].'       (서 명 또는 인)', 0, 1, 'R');

					$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
					$pdf->Cell($pdf->width, $pdf->row_height, '※ 매월 작성하여 기관 보관.(보관기간 : 작성일로부터 3년)', 0, 1, 'L');
					$pdf->SetX($pdf->left);
					$pdf->Cell($pdf->width, $pdf->row_height, '※ 활동지원기관 및 활동보조인과 수급자 및 보호자(가족)이 협의하여 매월 5일 이전까지 작성', 0, 1, 'L');
					$pdf->SetX($pdf->left);
					$pdf->Cell($pdf->width, $pdf->row_height, '※ 2인이상 서비스를 제공할 경우 활동지원인력명에 이름을 쓰고 제공한 날에 표시한다.', 0, 1, 'L');
				}
			}
			*/
		}
	}


	$objPHPExcel->getDefaultStyle()->getFont()->setSize($defFontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	//$objPHPExcel->setActiveSheetIndex(0);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->save("php://output");


	function lfGetSvcNm($svcCd,$subCd,$familyYn){
		if ($svcCd == '0'){
			if ($subCd == '200'){
				if ($familyYn == 'Y'){
					$svcNm = '가족';
				}else{
					$svcNm = '요양';
				}
			}else if ($subCd == '500'){
				$svcNm = '목욕';
			}else if ($subCd == '800'){
				$svcNm = '간호';
			}
		}else if ($svcCd == '1'){
			$svcNm = '간병';
		}else if ($svcCd == '2'){
			$svcNm = '돌봄';
		}else if ($svcCd == '3'){
			$svcNm = '산모';
		}else if ($svcCd == '4'){
			if ($subCd == '200'){
				$svcNm = '장애';
			}else if ($subCd == '500'){
				$svcNm = '목욕';
			}else if ($subCd == '800'){
				$svcNm = '간호';
			}
		}else if ($svcCd == '5'){
			$svcNm = '주야간보호';
		}else if ($svcCd == '6' || $svcCd == 'S' || $svcCd == 'R'){
			$svcNm = '재가';
		}else if ($svcCd == 'A'){
			$svcNm = '산유';
		}else if ($svcCd == 'B'){
			$svcNm = '병원';
		}else if ($svcCd == 'C'){
			$svcNm = '기타';
		}else{
			$svcNm = $svcCd;
		}

		return $svcNm;
	}

	function lfGetSvcFullNm($svcCd){
		if ($svcCd == '0'){
			$svcNm = '재가요양';
		}else if ($svcCd == '1'){
			$svcNm = '가사간병';
		}else if ($svcCd == '2'){
			$svcNm = '노인돌봄';
		}else if ($svcCd == '3'){
			$svcNm = '산모신생아';
		}else if ($svcCd == '4'){
			$svcNm = '장애인활동지원';
		}else if ($svcCd == '5'){
			$svcNm = '주야간보호';
		}else if ($svcCd == '6'){
			$svcNm = '재가지원';
		}else if ($svcCd == 'S'){
			$svcNm = '재가지원';
		}else if ($svcCd == 'R'){
			$svcNm = '자원연계';
		}else if ($svcCd == 'A'){
			$svcNm = '산모유료';
		}else if ($svcCd == 'B'){
			$svcNm = '병원간병';
		}else if ($svcCd == 'C'){
			$svcNm = '기타유료';
		}else{
			$svcNm = $svcCd;
		}

		return $svcNm;
	}

	function lfGetSvcSugaNm($subCd,$sugaCd,$procTime){
		if ($subCd == '500'){
			if ($sugaCd == 'CBFD1'){
				$svcNm = '미차량';
			}else if ($sugaCd == 'CBFD2'){
				$svcNm = '미차량';
			}else if ($sugaCd == 'CBKD1'){
				$svcNm = '차량 입욕';
			}else if ($sugaCd == 'CBKD2'){
				$svcNm = '차량 가정';
			}
		}else if ($subCd == '800'){
			if ($procTime < 30){
				$svcNm = '30분미만';
			}else if ($procTime < 60){
				$svcNm = '60분미만';
			}else{
				$svcNm = '60분이상';
			}
		}else{
			$svcNm = $procTime.'분';
		}

		return $svcNm;
	}
?>