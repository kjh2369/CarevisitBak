<?
	$name = $myF->euckr($var['name']);

	$chkSvcCd = str_replace(chr(1),'',$var['chkSvc']);
	
	if ($chkSvcCd == '0_900') $chkSvcCd = '5';
	if ($chkSvcCd == '5'){
		$sql = 'SELECT	memo
				FROM	dan_memo
				WHERE	org_no	= \''.$var['code'].'\'
				AND		key_gbn	= \'ILJUNG\'';
		$danMemo = $conn->get_data($sql);
	}

	if (!$var['code']) $var['code'] = $_SESSION['userCenterCode'];

	//공지내용
	$sql = 'SELECT	svc_cd AS cd
			,		notice AS str
			FROM	voucher_notice
			WHERE	org_no	= \''.$var['code'].'\'
			AND		yymm	= \''.$var['year'].$var['month'].'\'';

	$pdf->tempVal = $conn->_fetch_array($sql,'cd');

	//휴일리스트
	$sql = 'SELECT	mdate AS date
			,		holiday_name AS name
			FROM	tbl_holiday
			WHERE	LEFT(mdate,6)	= \''.$var['year'].$var['month'].'\'';

	$arrHoliday = $conn->_fetch_array($sql,'date');

	if ($var['month'] == '05'){
		$arrHoliday[$var['year'].$var['month'].'01']['name']	= '근로자의날';
	}

	if ($var['mode'] == '101'){
		//직원정보
		$sql = 'SELECT	DISTINCT
						m02_yjumin AS jumin
				,		m02_yname AS name
				,		m02_ytel AS mobile
				,		m02_ytel2 AS phone
				FROM	m02yoyangsa
				WHERE	m02_ccode	= \''.$var['code'].'\'';

		$arrMem = $conn->_fetch_array($sql,'jumin');

		//한도금액
		$sql = 'SELECT	m91_code as cd
				,		m91_kupyeo as amt
				FROM	m91maxkupyeo
				WHERE	LEFT(m91_sdate, 6)	<= \''.$var['year'].$var['month'].'\'
				AND		LEFT(m91_edate, 6)	>= \''.$var['year'].$var['month'].'\'';

		$arrLimitPay = $conn->_fetch_array($sql, 'cd');

		//청구한도
		$sql = 'SELECT	jumin
				,		amt
				FROM	client_his_limit
				WHERE	org_no = \''.$var['code'].'\'
				AND		DATE_FORMAT(from_dt, \'%Y%m\') <= \''.$var['year'].$var['month'].'\'
				AND		DATE_FORMAT(to_dt,   \'%Y%m\') >= \''.$var['year'].$var['month'].'\'';

		if (!Empty($var['jumin'])){
			$sql .= ' AND jumin = \''.$var['jumin'].'\'';
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
			   AND LEFT(m01_sdate,6) <= \''.$var['year'].$var['month'].'\'
			   AND LEFT(m01_edate,6) >= \''.$var['year'].$var['month'].'\'
			 UNION ALL
			SELECT m11_mcode2 AS cd
			,      m11_suga_cont AS nm
			,      m11_suga_value AS cost
			,      m11_sdate AS from_dt
			,      m11_edate AS to_dt
			  FROM m11suga
			 WHERE m11_mcode          = \'goodeos\'
			   AND LEFT(m11_sdate,6) <= \''.$var['year'].$var['month'].'\'
			   AND LEFT(m11_edate,6) >= \''.$var['year'].$var['month'].'\'
			 UNION ALL
			SELECT service_code AS cd
			,      service_gbn AS nm
			,      service_cost AS cost
			,      service_from_dt AS from_dt
			,      service_to_dt AS to_dt
			  FROM suga_service
			 WHERE org_no = \'goodeos\'
			   AND DATE_FORMAT(service_from_dt,\'%Y%m\') <= \''.$var['year'].$var['month'].'\'
			   AND DATE_FORMAT(service_to_dt,  \'%Y%m\') >= \''.$var['year'].$var['month'].'\'
			UNION	ALL
			SELECT	DISTINCT
					CONCAT(suga_cd,suga_sub) AS code
			,		suga_nm AS name
			,		suga_cost AS cost
			,		REPLACE(from_dt,\'-\',\'\') AS from_dt
			,		REPLACE(to_dt,\'-\',\'\') AS to_dt
			FROM	care_suga
			WHERE	org_no	= \''.$var['code'].'\'
			AND		suga_sr	= \''.$var['sr'].'\'
			AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$var['year'].$var['month'].'\'
			AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6) >= \''.$var['year'].$var['month'].'\'';

	//주야가보호 수가
	$sql .= '
			UNION	ALL
			SELECT	CONCAT(code,\'_\',lv_gbn) AS cd
			,		name AS nm
			,		cost
			,		from_dt
			,		to_dt
			FROM	suga_dan
			WHERE	DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$var['year'].$var['month'].'\'
			AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$var['year'].$var['month'].'\'';

	$arrSuga = $conn->_fetch_array($sql);

	//선택서비스
	$svcGbn	= Explode(chr(1),$var['chkSvc']);

	//선택구분
	if ($var['showGbn'] == 'family'){
		$showSql = ' AND t01_toge_umu    = \'Y\'
					 AND t01_svc_subcode = \'200\'';

	}else if ($var['showGbn'] == 'conf'){
		$showSql = ' AND t01_status_gbn = \'1\'';

	}else{
		$showSql = '';
	}

	if ($var['showGbn'] == 'conf'){
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

	if ($var['mode'] == '101'){
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
						,		mst.m03_client_no AS clt_no
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
								AND		DATE_FORMAT(lvl.from_dt,\'%Y%m\') <= \''.$var['year'].$var['month'].'\'
								AND		DATE_FORMAT(lvl.to_dt,  \'%Y%m\') >= \''.$var['year'].$var['month'].'\'
						LEFT	JOIN	client_his_kind AS kind
								ON		kind.org_no = mst.m03_ccode
								AND		kind.jumin  = mst.m03_jumin
								AND		DATE_FORMAT(kind.from_dt,\'%Y%m\') <= \''.$var['year'].$var['month'].'\'
								AND		DATE_FORMAT(kind.to_dt,  \'%Y%m\') >= \''.$var['year'].$var['month'].'\'
						LEFT	JOIN	client_his_dis AS dis
								ON		dis.org_no = mst.m03_ccode
								AND		dis.jumin  = mst.m03_jumin
								AND		DATE_FORMAT(dis.from_dt,\'%Y%m\') <= \''.$var['year'].$var['month'].'\'
								AND		DATE_FORMAT(dis.to_dt,  \'%Y%m\') >= \''.$var['year'].$var['month'].'\'
						WHERE	m03_ccode  = \''.$var['code'].'\'';

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

				if (!Empty($var['jumin'])){
					$sl .= ' AND m03_jumin = \''.$var['jumin'].'\'';
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
				,      clt_no
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
				WHERE	org_no	= \''.$var['code'].'\'
				AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$var['year'].$var['month'].'\'
				AND		DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$var['year'].$var['month'].'\'';

		$arr = $conn->_fetch_array($sql);
		$cnts = SizeOf($arr);

		for($i=0; $i<$cnts; $i++){
			$idx = SizeOf($arrClientRate[$arr[$i]['jumin']]);

			$arrClientRate[$arr[$i]['jumin']][$idx] = Array(
					'rate'	=>$arr[$i]['rate']
				,	'from'	=>str_replace('-', '', $arr[$i]['from_dt'])
				,	'to'	=>str_replace('-', '', $arr[$i]['to_dt'])
			);
		}

		Unset($arr);

	}else if ($var['mode'] == '102'){
		$sql = 'SELECT DISTINCT
					   m02_yjumin AS jumin
				,      m02_yname AS name
				,      m02_ytel AS phone
				  FROM m02yoyangsa
				 WHERE m02_ccode = \''.$var['code'].'\'';

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

			if ($var['mode'] == '101'){
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
						WHERE	t01_ccode = \''.$var['code'].'\'
						AND		t01_mkind = \''.$svcCd.'\'
						AND		LEFT(t01_'.$gbnField.'_date,6) = \''.$var['year'].$var['month'].'\'
						AND		t01_del_yn = \'N\'';

				if (!Empty($var['jumin'])){
					$sl .= ' AND t01_jumin = \''.$var['jumin'].'\'';
				}

				if (!Empty($subCd)){
					$sl .= ' AND t01_svc_subcode = \''.$subCd.'\'';
				}

				if ($var['code'] == '31141000043' /* 예사랑 */){
					$sl.= ' AND t01_bipay_umu != \'Y\'';
				}

				if ($var['bipayYn'] == 'Y'){
					//급여
					$sl.= ' AND t01_bipay_umu != \'Y\'';
				}else if ($var['bipayYn'] == 'N'){
					//비급여
					$sl.= ' AND t01_bipay_umu = \'Y\'';
				}

				if ($svcCd == '6'){
					//재가지원 및 자원연계
					$sl .= ' AND t01_svc_subcd = \''.$var['sr'].'\'';
				}
			}else if ($var['mode'] == '102'){
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
							 WHERE t01_ccode = \''.$var['code'].'\'
							   AND t01_mkind = \''.$svcCd.'\'
							   AND LEFT(t01_'.$gbnField.'_date,6) = \''.$var['year'].$var['month'].'\'
							   AND t01_del_yn = \'N\'';

					if (!Empty($var['jumin'])){
						$sl .= ' AND t01_'.$gbnMemCd.$i.' = \''.$var['jumin'].'\'';
					}

					if (!Empty($subCd)){
						$sl .= ' AND t01_svc_subcode = \''.$subCd.'\'';
					}

					if ($var['code'] == '31141000043' /* 예사랑 */){
						$sl.= ' AND t01_bipay_umu != \'Y\'';
					}
				}
			}

			$sl .= $showSql;
		}
	}

	if ($var['mode'] == '101'){
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
						ON		mst.m03_ccode = \''.$var['code'].'\'
						AND		mst.m03_mkind = '.$svcSL.'
						AND		mst.m03_jumin = iljung.jumin';

		if (!Empty($name)){
			$sql .= ' AND mst.m03_name >= \''.$name.'\'';
		}

		if ($var['order'] == '2'){
			$sql .= ' ORDER BY CASE WHEN mem_nm != \'\' THEN 1 ELSE 2 END,mem_nm,name, jumin, svc_cd, date, from_time';
		}else{
			$sql .= ' ORDER BY name, jumin, svc_cd, date, from_time';
		}

		//if ($debug) echo nl2br($sql);


	}else if ($var['mode'] == '102'){
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
				,      yoy.m02_ytel AS mobile
				  FROM ('.$sl.') AS iljung
				 INNER JOIN m03sugupja AS mst
					ON mst.m03_ccode = \''.$var['code'].'\'
				   AND mst.m03_mkind = iljung.svc_cd
				   AND mst.m03_jumin = iljung.jumin
				 INNER JOIN m02yoyangsa AS yoy
				    ON yoy.m02_ccode  = \''.$var['code'].'\'
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

	if ($var['mode'] == '101'){
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
				$mobile     = $arrMem[$row['mem_cd1']]['mobile'];
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
				if ($var['year'].$var['month'] >= '201402'){
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

			//if ($var['dtlYn'] != 'N'){
				//제공서비스 상세내역
				$key2 = $row['svc_cd'].'_'.$row['sub_cd'].'_'.$row['from_time'].'_'.$row['to_time'].'_'.$worker;
				
				if (!IsSet($arrTme[$key1][$key2])){

					$svcNm  = lfGetSvcFullNm($row['svc_cd'],$row['sub_cd'],$row['family_yn']);
					
					
					//$svcNm .= '/'.lfGetSvcSugaNm($row['sub_cd'],$row['suga_cd'],$procTime);

					
					$arrTme[$key1][$key2] = Array(
							'key'		=>$key2
						,	'svcNm'		=>$svcNm
						,	'procTime'	=>$procTime
						,	'workTime'	=>$workTime
						,	'worker'	=>$worker
						,	'mobile'	=>$mobile
						,	'sugaCost'	=>$row['suga_tot']
						,	'sugaTot'	=>0
						,	'day'		=>''
					);
				}

				if (!Is_Numeric(StrPos($arrTme[$key1][$key2]['day'], '/'.$day))){
					$arrTme[$key1][$key2]['day'] .= '/'.$day;
				}

				$arrTme[$key1][$key2]['sugaTot'] += $row['suga_tot'];
			/*
			}else{

				$key2 = $row['svc_cd'].'_'.$row['sub_cd'].'_'.$time.'_'.$worker;

				if (!IsSet($arrSvc[$key1][$key2])){
					$svcNm  = lfGetSvcFullNm($row['svc_cd'],$row['sub_cd'],$row['family_yn']);
					//$svcNm .= '/'.lfGetSvcSugaNm($row['sub_cd'],$row['suga_cd'],$procTime);


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
			*/

			//제공서비스 금액내역
			$key2 = $row['suga_cd'];
			$key3 = $row['svc_cd'].'_'.$row['sub_cd'].'_'.$time;

			if (!IsSet($arrDtl[$key1][$key2])){
				$svcNm  = lfGetSvcFullNm($row['svc_cd'],$row['sub_cd'],$row['family_yn']);

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
			
			if ($row['svc_cd'] >= '1' && $row['svc_cd'] <= '4'){
				//바우처
				$arrDtl[$key1][$key2]['suga']	+= $row['suga_tot'];
			}
			
			$arrDtl[$key1][$key2]['count'] ++;
			$arrDtl[$key1][$key2]['timeTot']+= $procTime;
			$arrDtl[$key1][$key2]['amt']	+= $row['suga_tot'];

			$liSeq ++;

			
		}

		
	}
	
	$conn->row_free();

	UnSet($tmpJumin);

	if (!Is_Array($arrCaln)){
		exit;
	}

	//고객/직원 정보 넓이
	if ($var['mode'] == '101'){
		$col['infoWidth'][0]	= $pdf->width*0.17;
		$col['infoWidth'][1]	= $pdf->width*0.25;
		$col['infoWidth'][2]	= $pdf->width*0.25;
		$col['infoWidth'][3]	= $pdf->width*0.12;
		$col['infoWidth'][4]	= $pdf->width*0.21;
	}else if ($var['mode'] == '102'){
		$col['infoWidth'][0]	= $pdf->width*0.20;
		$col['infoWidth'][1]	= $pdf->width*0.25;
		$col['infoWidth'][2]	= $pdf->width*0.25;
		$col['infoWidth'][3]	= $pdf->width*0.30;
	}

	//일정표
	$col['calnWidth'][0]	= $pdf->width*0.1428;
	$col['calnWidth'][1]	= $pdf->width*0.1428;
	$col['calnWidth'][2]	= $pdf->width*0.1428;
	$col['calnWidth'][3]	= $pdf->width*0.1428;
	$col['calnWidth'][4]	= $pdf->width*0.1428;
	$col['calnWidth'][5]	= $pdf->width*0.1428;
	$col['calnWidth'][6]	= $pdf->width*0.1428;

	$col['calnWeek'][0]		= '일';
	$col['calnWeek'][1]		= '월';
	$col['calnWeek'][2]		= '화';
	$col['calnWeek'][3]		= '수';
	$col['calnWeek'][4]		= '목';
	$col['calnWeek'][5]		= '금';
	$col['calnWeek'][6]		= '토';

	//제공서비스
	$col['svcWidth'][0]		= $pdf->width * 0.15;
	$col['svcWidth'][1]		= $pdf->width * 0.12;
	$col['svcWidth'][2]		= $pdf->width * 0.08;
	$col['svcWidth'][3]		= $pdf->width * 0.15;
	$col['svcWidth'][4]		= $pdf->width * 0.13;
	$col['svcWidth'][5]		= $pdf->width * 0.37;

	$col['svcTitle'][0]		= '서비스제공자명';
	$col['svcTitle'][1]		= '전 화';
	$col['svcTitle'][2]		= '담 당';
	$col['svcTitle'][3]		= '급여내용';
	$col['svcTitle'][4]		= '주기';
	$col['svcTitle'][5]		= '제공일';


	if ($var['mode'] == '102'){
		$col['svcTitle'][1]	= '고객명';
	}
	
	//제공서비스 상세
	if ($var['mode'] == '101'){
		$col['dtlWidth'][0]	= $pdf->width * 0.08;
		$col['dtlWidth'][1]	= $pdf->width * 0.15;
		$col['dtlWidth'][2]	= $pdf->width * 0.05;
		$col['dtlWidth'][3]	= $pdf->width * 0.09;
		$col['dtlWidth'][4]	= $pdf->width * 0.09;
		$col['dtlWidth'][5]	= $pdf->width * 0.09;
		$col['dtlWidth'][6]	= $pdf->width * 0.09;
		$col['dtlWidth'][7]	= $pdf->width * 0.09;
		$col['dtlWidth'][8]	= $pdf->width * 0.09;
		$col['dtlWidth'][9]	= $pdf->width * 0.09;
		$col['dtlWidth'][10]= $pdf->width * 0.09;

		$col['dtlTitle'][0]	= '급여종류';
		$col['dtlTitle'][1]	= '서비스(서비스명)';
		$col['dtlTitle'][2]	= '횟수';
		$col['dtlTitle'][3]	= '시간';
		$col['dtlTitle'][4]	= '수가';
		$col['dtlTitle'][5]	= '총금액';
		$col['dtlTitle'][6]	= '급여총액';

		if ($var['code'] == '31126000192'){
			//가톨릭노인복지센터
			$col['dtlTitle'][7]	= '공단청구액';
			$col['dtlTitle'][8]	= '본인부담';
			$col['dtlTitle'][9]	= '초과';
			$col['dtlTitle'][10]= '비급여';
		}else{
			$col['dtlTitle'][7]	= '본인부담';
			$col['dtlTitle'][8]	= '초과';
			$col['dtlTitle'][9]	= '비급여';
			$col['dtlTitle'][10]= '총부담액';
		}

	}else if ($var['mode'] == '102'){
		$col['dtlWidth'][0]	= $pdf->width * 0.08;
		$col['dtlWidth'][1]	= $pdf->width * 0.37;
		$col['dtlWidth'][2]	= $pdf->width * 0.10;
		$col['dtlWidth'][3]	= $pdf->width * 0.15;
		$col['dtlWidth'][4]	= $pdf->width * 0.15;
		$col['dtlWidth'][5]	= $pdf->width * 0.15;

		$col['dtlTitle'][0]	= '급여종류';
		$col['dtlTitle'][1]	= '서비스(서비스명)';
		$col['dtlTitle'][2]	= '횟수';
		$col['dtlTitle'][3]	= '시간';
		$col['dtlTitle'][4]	= '수가';
		$col['dtlTitle'][5]	= '총급여비용';
	}

	//일정 변수 설정
	$calTime	= mktime(0, 0, 1, $pdf->month, 1, $pdf->year);
	$today		= date('Ymd', mktime());
	$lastDay	= date('t', $calTime);										//총일수 구하기
	$startWeek	= date('w', strtotime(date('Y-m', $calTime).'-01'));		//시작요일 구하기
	$totalWeek	= ceil(($lastDay + $startWeek) / 7);						//총 몇 주인지 구하기
	$lastWeek	= date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay));	//마지막 요일 구하기

	$height = $pdf->row_height;

	//주별 높이
	if ($var['calnYn'] == 'Y'){
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

	$num = 1;
	
	foreach($arrCaln as $jumin => $caln){
		$pdf->SetFontSize(11);

		foreach($arrCaln[$jumin] as $svcCd => $caln){
			$pdf->svcCd = $svcCd;

			if ($tmpJumin.'_'.$tmpSvcCd != $jumin.'_'.$svcCd){
				#if (!Empty($tmpJumin)){
					$pdf->AddPage(strtoupper($var['dir']), 'A4');
				#}

				$tmpJumin = $jumin;
				$tmpSvcCd = $svcCd;
			}


			
			//정보
			if ($var['mode'] == '101'){
			
				if($svcCd == '2'){
					$col['infoTitle'][0] = "관리번호";
					$col['infoTitle'][1] = "성 명 / 생년월일";
					$col['infoTitle'][2] = "사회복지서비스 제공자";
					$col['infoTitle'][3] = "작성일자";

					$col['clientValue'][0] = $arrClientInfo[$jumin.'_'.$svcCd]['clt_no'];
					$col['clientValue'][1] = $arrClientInfo[$jumin.'_'.$svcCd]['name'].' / '.$myF->issToBirthDay($jumin,'.');


					//담당직원 리스트
					for($day=1; $day<=$lastDay; $day++){
						for($i=0; $i<sizeof($caln); $i++){
							if (!Is_Array($arrMemList[$caln[$day][$i]['memCd1']])){
								$arrMemList[$caln[$day][$i]['memCd1']] = Array(
										'name'	=>$arrMem[$caln[$day][$i]['memCd1']]['name']
								);
							}

							if (!Empty($caln[$day][$i]['memCd2'])){
								if (!Is_Array($arrMemList[$caln[$day][$i]['memCd2']])){
									$arrMemList[$caln[$i]['memCd2']] = Array(
											'name'	=>$arrMem[$caln[$day][$i]['memCd2']]['name']
									);
								}
							}
						}
					}

					//담당직원 리스트
					foreach($arrMemList as $memCd => $arrMemInfo){

						if (!Empty($memStr)){
							$memStrs .= ' / ';
						}

						$memStrs .= $arrMemInfo['name'];

					}


					$col['clientValue'][2] = $memStrs;
					$col['clientValue'][3] = ($var['printDT'] != '' ? $myF->dateStyle($var['printDT'],'.') : '');

				}else {
					$col['infoTitle'][0] = "수급자 성명";
					$col['infoTitle'][1] = "수급자 생년월일";
					$col['infoTitle'][2] = "급여종류";
					$col['infoTitle'][3] = "활동지원인력명";

					$col['clientValue'][0] = $arrClientInfo[$jumin.'_'.$svcCd]['name'];
					$col['clientValue'][1] = $myF->issStyle($arrClientInfo[$jumin.'_'.$svcCd]['jumin']);
					//$col['clientValue'][2] = lfGetSvcFullNm($svcCd);

					//담당직원 리스트
					for($day=1; $day<=$lastDay; $day++){
						for($i=0; $i<sizeof($caln); $i++){
							if (!Is_Array($arrMemList[$caln[$day][$i]['memCd1']])){
								$arrMemList[$caln[$day][$i]['memCd1']] = Array(
										'name'	=>$arrMem[$caln[$day][$i]['memCd1']]['name']
								);
							}

							if (!Empty($caln[$day][$i]['memCd2'])){
								if (!Is_Array($arrMemList[$caln[$day][$i]['memCd2']])){
									$arrMemList[$caln[$i]['memCd2']] = Array(
											'name'	=>$arrMem[$caln[$day][$i]['memCd2']]['name']
									);
								}
							}
						}
					}

					//담당직원 리스트
					foreach($arrMemList as $memCd => $arrMemInfo){
						$lsTel = $myF->phoneStyle($arrMemInfo['mobile'],'.');
						
						if (!Empty($memStrs)){
							$memStrs .= ' / ';
						}

						$memStrs .= $arrMemInfo['name'];

					}

					$col['clientValue'][3] = $memStrs;
					
				}

				unset($memStrs);

			}

			
			$liTop = $pdf->GetY();

			$pdf->SetFont($pdf->font_name_kor,'B',11);
			$pdf->SetFillColor(220,220,220);

			$pdf->SetXY($pdf->left, $pdf->GetY());
			$pdf->Cell($pdf->width*0.22, $pdf->row_height+4, $col['infoTitle'][0], 1, 0, 'C', true);
			$pdf->Cell($pdf->width*0.28, $pdf->row_height+4, $col['clientValue'][0], 1, 0, 'C');
			$pdf->Cell($pdf->width*0.22, $pdf->row_height+4, $col['infoTitle'][1], 1, 0, 'C', true);
			$pdf->Cell($pdf->width*0.28, $pdf->row_height+4, $col['clientValue'][1], 1, 1, 'C');
			
			
			$pdf->SetX($pdf->left);
			$pdf->Cell($pdf->width*0.22, $pdf->row_height+4, $col['infoTitle'][2], 1, 0, 'C', true);
			$pdf->Cell($pdf->width*0.28, $pdf->row_height+4, $col['clientValue'][2], 1, 0, 'C');
			$pdf->Cell($pdf->width*0.22, $pdf->row_height+4, $col['infoTitle'][3], 1, 0, 'C', true);
			$pdf->Cell($pdf->width*0.28, $pdf->row_height+4, $col['clientValue'][3], 1, 1, 'C');

			// 테두리
			$pdf->SetLineWidth(0.6);
			$pdf->Rect($pdf->left, $liTop, $pdf->width, ($pdf->row_height+4) * 2);
			$pdf->SetLineWidth(0.2);

			$pdf->Cell($pdf->width, 3, "", 0, 1);

			$liFirstY = $pdf->GetY();	//테두리 시작 높이
			$liLastY  = 0;				//테두리 종료 높이
			
			$pdf->SetXY($pdf->left,$pdf->GetY());
			//$pdf->SetFont($pdf->font_name_kor,'B',9);
			

			if ($var['calnYn'] == 'Y'){
				$day = 1; //화면에 표시할 화면의 초기값을 1로 설정

				for($i=1; $i<=$totalWeek; $i++){
					if ($i == 1){
						lfGetWeekString($pdf,$col,$height);
					}

					$pdf->SetX($pdf->left);
					$pdf->SetTextColor(0,0,0);

					if ($var['mode'] == '101'){
						$liGbnH = 3;
					}else if ($var['mode'] == '102'){
						$liGbnH = 2;
					}else{
						$liGbnH = 0;
					}

					//행높이
					$liHg = $height * 0.65;
					$liH = (($liCheckCnt[$jumin][$svcCd][$i]) * $liGbnH * $liHg) + $height;

					if ($liH <= $height){
						$liH  = $liHg * 5;
					}

					$liHg = $height * 0.7;

					if (Empty($liLastY)){
						$liLastY = $height;
					}

					if ($pdf->GetY()+$liH > $pdf->height){
						$pdf->SetLineWidth(0.6);
						$pdf->Rect($pdf->left, $liFirstY, $pdf->width, $liLastY);
						$pdf->SetLineWidth(0.2);

						$pdf->AddPage(strtoupper($var['dir']), 'A4');
						lfGetWeekString($pdf,$col,$height);

						$liFirstY = $pdf->GetY() - $height;
						$liLastY  = $height;
					}

					$liLastY += $liH;
					
					//총 가로칸 만들기
					for($j=0; $j<7; $j++){
						if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
							$date = $var['year'].$var['month'].($day < 10 ? '0' : '').$day;

							if ($j == 0){//일요일
								$pdf->SetTextColor(255,0,0); //붉은색
							}else if ($j == 6){//토요일
								$pdf->SetTextColor(0,0,255); //파란색
							}else{//평일
								$pdf->SetTextColor(0,0,0); //검정색
							}

							//기념일
							if (!Empty($arrHoliday[$date]['name'])){
								if ($date == $var['year'].$var['month'].'01'){
									$pdf->SetTextColor(0,0,255); //파란색
								}else{
									$pdf->SetTextColor(255,0,0); //붉은색
								}
							}

							//공간지정
							$pdf->Cell($col['calnWidth'][$j], $liH, "", 1, 0, 'L');

							$liX = $pdf->GetX();
							$liY = $pdf->GetY();

							$pdf->SetX($liX - $col['calnWidth'][$j]);

							//일자라인
							$pdf->line($liX - $col['calnWidth'][$j]+$height, $liY, $liX - $col['calnWidth'][$j]+$height, $liY+$height);
							$pdf->line($liX - $col['calnWidth'][$j], $liY+$height, $liX - $col['calnWidth'][$j]+$height, $liY+$height);

							//일자
							$pdf->Cell($col['calnWidth'][$j]*0.2, $height, Number_Format($day), 0, 0, 'C');

							//기념일
							$pdf->Cell($col['calnWidth'][$j]*0.8, $height, $arrHoliday[$date]['name'], 0, 2, 'R');

							//기본글자색
							$pdf->SetTextColor(0,0,0); //검정색
							
							if (Is_Array($arrCaln[$jumin][$svcCd][$day])){
								foreach($arrCaln[$jumin][$svcCd][$day] as $seq => $caln){
									$pdf->SetX($liX - $col['calnWidth'][$j]);
									
									$svcNm  = lfGetSvcFullNm($caln['svcCd'],$caln['subCd'],$row['familyYn']);
									
									//서비스
									$pdf->Cell($col['calnWidth'][$j], $liHg, $pdf->_splitTextWidth($myF->utf($svcNm),$col['calnWidth'][$j]), 0, 2, 'C');

									//근무시간
									$pdf->Cell($col['calnWidth'][$j], $liHg, $caln['workTime'], 0, 2, 'L');


									if ($var['mode'] == '101'){
										//직원명
										//$pdf->Cell($col['calnWidth'][$j], $liHg, //$pdf->_splitTextWidth($myF->utf($caln['worker']),$col['calnWidth'][$j]), 0, 2, 'R');


										if ($caln['subCd'] == '500'){
											$lsIconFile = '../image/icon_bath.jpg';
										}else if ($caln['subCd'] == '800'){
											$lsIconFile = '../image/icon_nurs.jpg';
										}else{
											$lsIconFile = '';
										}

										if (!Empty($lsIconFile)){
											$pdf->Image($lsIconFile, $pdf->GetX()+$col['calnWidth'][$j]-4, $pdf->GetY()-$liHg*$liGbnH+4, 3.5, 3.5);
										}

										//담당직원 리스트
										/*
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
										*/
									}

									//비급여 표시
									if ($caln['bipayYn'] == 'Y'){
										$lsIconBipay	= '../image/btn/btn_bipay.gif';
									}else{
										$lsIconBipay	= '';
									}

									if (!Empty($lsIconBipay)){
										if ($var['mode'] == '101'){
											$pdf->Image($lsIconBipay, $pdf->GetX()+1, $pdf->GetY()-$liHg-4, 3.5, 3.5);
										}else if ($var['mode'] == '102'){
											$pdf->Image($lsIconBipay, $pdf->GetX()+$col['calnWidth'][$j]-4, $pdf->GetY()-$liHg+0.5, 3.5, 3.5);
										}
									}
								}
							}

							$pdf->SetXY($liX,$liY);
							$pdf->SetTextColor(0,0,0);

							$day ++;
						}else{
							$pdf->Cell($col['calnWidth'][$j], $liH, "", 1, $j < 6 ? 0 : 1, 'C');
						}
					}

					$pdf->SetXY($pdf->left,$pdf->GetY() + $liH);
				}

				$pdf->SetLineWidth(0.6);
				$pdf->Rect($pdf->left, $liFirstY, $pdf->width, $liLastY);
				$pdf->SetLineWidth(0.2);


				$pdf->SetXY($pdf->left,$liFirstY+$liLastY+2);

			}

			//담당직원 리스트 초기화
			UnSet($memStr);
			UnSet($arrMemList);

			$pdf->SetFont($pdf->font_name_kor,'',8);
			$pdf->SetXY($pdf->left,$pdf->GetY()+1);
			
			//제공서비스
			if ($chkSvcCd == '2') lfGetSvcTitle($pdf, $col);

			if ($var['mode'] == '101'){
				$key = $jumin.'_'.$svcCd;
			}else{
				$key = $jumin;
			}

			//if ($var['dtlYn'] != 'N'){
				//제공서비스 타이틀
				
				if ($chkSvcCd == '2'){
					if (Is_Array($arrTme[$key])){
						$arrTme[$key] = $myF->sortArray($arrTme[$key], 'key');

						$liH = $pdf->row_height * 0.8;
						$cnt = 0;
						$num = 0;
						$reStart = true;

						foreach($arrTme[$key] as $key1 => $arrSub){
							$lsSvc	= Explode('_',$key);
							$lsSvc	= $lsSvc[1];
							$days	= $arrSub['day'].'/';
							$lsCnt	= Number_Format(SizeOf(Explode('/',$arrSub['day']))-1);
							//echo $lsCnt.'/';

							$liW1 = $col[5] / $lastDay;
							//echo $rows.'/';
							
							for($i=1; $i<=$lastDay; $i++){
								if ($i < 10){
									$liW2 = $liW1 * 1.2;
								}else{
									$liW2 = $liW1 * 1.7;
								}


								if (Is_Numeric(StrPos($days,'/'.$i.'/'))){

									$dd[$cnt] = $i;
									
									
									if($dd[$cnt-1]==($i-1)){

										$ds = true;

										$coma = '';

										if($num==0){
											$start = true;
										}else {
											$coma = ', ';
											$start = true;
										}

										if(($num+1) == $lsCnt){
											$DDD = $i;
											$ds = false;
										}

									}else {

										if(!$reStart){
											$coma = ', ';
											$ds = false;

										}else {

											$ds = true;

										}



										$start = true;

									}

									if($ds){

										if($start){
											$d = $coma.$dd[$cnt];

										}else {
											$d = '';
										}

									}else {



										if($dd[$cnt] == $DDD){
											$ddd = '';
										}else {
											$ddd = ($dd[$cnt-1]);
										}
										
										if($dd[$cnt]==$i){
											$d = $coma.$i;
										}else {
											$d = '~'.$ddd.$coma.$i;
										}
										
									}

									
									//$pdf->Cell($liW2,$liH,' '.$coma.Number_Format($i),0,0,'C');


									$str .= $d;
									$cnt += 1;
									$num += 1;
									$reStart = false;

								}


							}

							$num = 0;

							$reStart = true;



							$rowHigh = get_row_cnt($pdf, $col['svcWidth'][5], 5, $str);

							if ($pdf->GetY()+$liH > $pdf->height){
								//페이지추가
								$pdf->AddPage(strtoupper($var['dir']), 'A4');

								//타이틀 출력
								lfGetSvcTitle($pdf, $col);
							}


							$workTime	= Explode('~',$arrSub['workTime']);
							$worker		= Explode('/',$arrSub['worker']);

							$pdf->SetX($pdf->left);
							$pdf->Cell($col['svcWidth'][0],$rowHigh,$myF->splits(iconv("UTF-8","EUC-KR", $_SESSION['userCenterKindName'][0]), 9),1,0,'L');
							$pdf->Cell($col['svcWidth'][1],$rowHigh,$myF->phoneStyle($mobile,'.'),1,0,'L');
							$pdf->Cell($col['svcWidth'][2],$rowHigh,$worker[0],1,0,'L');


							//$pdf->Cell($col['svcWidth'][3],$liH,$lsCnt,1,1,'C');

							//$pdf->SetXY($pdf->left,$pdf->GetY()-$liH);


							//$pdf->Cell($col['svcWidth'][0],$liH,"~".$workTime[1],"LBR",0,'L');
							//$pdf->Cell($col['svcWidth'][1],$liH,$worker[1],"LBR",0,'L');

							$pdf->Cell($col['svcWidth'][3],$rowHigh,"","LBR",0,'L');
							$pdf->Cell($col['svcWidth'][4],$rowHigh,"","LBR",0,'L');
							$pdf->Cell($col['svcWidth'][5],$rowHigh,"","LBR",1,'L');

							$liX = $pdf->GetX();
							$liY = $pdf->GetY();

							$pdf->SetXY($pdf->left+$col['svcWidth'][0]+$col['svcWidth'][1]+$col['svcWidth'][2]+$col['svcWidth'][3]+$col['svcWidth'][4],$liY-$rowHigh);

							$pdf->MultiCell($col['svcWidth'][5], 5, $str, 0, "L");

							$pdf->SetXY($liX,$liY);

							unSet($str);

							//lfDrawDays($pdf,$col['svcWidth'],$lastDay,$days,$liH);
						}
					}
				}
			//}
			
			$pdf->SetXY($pdf->left,$pdf->GetY()+1);
			
			$liH = $pdf->row_height;

			//제공서비스 상세
			if ($var['useType'] == 'Y'){
				if ($pdf->GetY()+$pdf->row_height > $pdf->height){
					//페이지추가
					$pdf->AddPage(strtoupper($var['dir']), 'A4');
				}

				if (Is_Array($arrDtl[$key])){
					//타이틀 출력
					lfGetDtlTitle($pdf, $col);

					if ($var['mode'] == '101'){
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
						if ($pdf->GetY()+$liH > $pdf->height){
							//페이지추가
							$pdf->AddPage(strtoupper($var['dir']), 'A4');

							//타이틀 출력
							lfGetDtlTitle($pdf, $col);
						}

						$pdf->SetX($pdf->left);
						$pdf->Cell($col['dtlWidth'][0],$liH,$pdf->_splitTextWidth($myF->utf($arrSub['svcNm']),$col['dtlWidth'][0]),1,0,'L');
						$pdf->Cell($col['dtlWidth'][1],$liH,$pdf->_splitTextWidth($myF->utf($arrSub['sugaNm']),$col['dtlWidth'][1]),1,0,'L');
						$pdf->Cell($col['dtlWidth'][2],$liH,Number_Format($arrSub['count']),1,0,'R');
						$pdf->Cell($col['dtlWidth'][3],$liH,$myF->euckr($myF->_min2timeKor($arrSub['time'])),1,0,'R');
						$pdf->Cell($col['dtlWidth'][4],$liH,Number_Format($arrSub['cost']),1,0,'R');	//수가

						if ($var['mode'] == '101'){
							$pdf->Cell($col['dtlWidth'][5],$liH,Number_Format($arrSub['amt']),1,0,'R');		//총금액
							$pdf->Cell($col['dtlWidth'][6],$liH,Number_Format($arrSub['suga']),1,0,'R');	//급여총액

							if ($var['code'] == '31126000192'){
								//가톨릭노인복지센터
								$pdf->Cell($col['dtlWidth'][7],$liH,Number_Format($arrSub['suga'] - Floor($arrSub['expense'])),1,0,'R');	//공단청구액
								$pdf->Cell($col['dtlWidth'][8],$liH,Number_Format(Floor($arrSub['expense'])),1,0,'R');	//본인부담
								$pdf->Cell($col['dtlWidth'][9],$liH,Number_Format($arrSub['over']),1,0,'R');	//초과
								$pdf->Cell($col['dtlWidth'][10],$liH,Number_Format($arrSub['bipay']),1,1,'R');	//비급여
							}else{
								$pdf->Cell($col['dtlWidth'][7],$liH,Number_Format(Floor($arrSub['expense'])),1,0,'R');	//본인부담
								$pdf->Cell($col['dtlWidth'][8],$liH,Number_Format($arrSub['over']),1,0,'R');	//초과
								$pdf->Cell($col['dtlWidth'][9],$liH,Number_Format($arrSub['bipay']),1,0,'R');	//비급여
								$pdf->Cell($col['dtlWidth'][10],$liH,Number_Format(Floor($arrSub['expense'])+$arrSub['over']+$arrSub['bipay']),1,1,'R');	//총부담액
							}

						}else if ($var['mode'] == '102'){
							$pdf->Cell($col['dtlWidth'][5],$liH,Number_Format($arrSub['amt']),1,1,'R');
						}

						$liTime		+= $arrSub['timeTot'];
						$liAmt		+= $arrSub['amt'];
						$liSuag		+= $arrSub['suga'];
						$liExpense	+= $arrSub['expense'];
						$liOver		+= $arrSub['over'];
						$liBipay	+= $arrSub['bipay'];
					}

					$pdf->SetX($pdf->left);

					if ($var['mode'] == '101'){
						$liExpense	= $myF->cutOff($liExpense);

						$pdf->Cell($col['dtlWidth'][0]+$col['dtlWidth'][1]+$col['dtlWidth'][2],$liH,"합계",1,0,'R',1);
						$pdf->Cell($col['dtlWidth'][3]+$col['dtlWidth'][4],$liH,$myF->euckr($myF->_min2timeKor($liTime)),1,0,'C',1);
						$pdf->Cell($col['dtlWidth'][5],$liH,Number_Format($liAmt),1,0,'R',1);
						$pdf->Cell($col['dtlWidth'][6],$liH,Number_Format($liSuag),1,0,'R',1);

						if ($var['code'] == '31126000192'){
							$pdf->Cell($col['dtlWidth'][7],$liH,Number_Format($liSuag - $liExpense),1,0,'R',1);
							$pdf->Cell($col['dtlWidth'][8],$liH,Number_Format($liExpense),1,0,'R',1);
							$pdf->Cell($col['dtlWidth'][9],$liH,Number_Format($liOver),1,0,'R',1);
							$pdf->Cell($col['dtlWidth'][10],$liH,Number_Format($liBipay),1,1,'R',1);
						}else{
							$pdf->Cell($col['dtlWidth'][7],$liH,Number_Format($liExpense),1,0,'R',1);
							$pdf->Cell($col['dtlWidth'][8],$liH,Number_Format($liOver),1,0,'R',1);
							$pdf->Cell($col['dtlWidth'][9],$liH,Number_Format($liBipay),1,0,'R',1);
							$pdf->Cell($col['dtlWidth'][10],$liH,Number_Format($myF->cutOff($liExpense+$liOver+$liBipay)),1,1,'R',1);
						}

					}else if ($var['mode'] == '102'){
						$pdf->Cell($col['dtlWidth'][0]+$col['dtlWidth'][1],$liH,"합계",1,0,'R',1);
						$pdf->Cell($col['dtlWidth'][2]+$col['dtlWidth'][3],$liH,$myF->euckr($myF->_min2timeKor($liTime)),1,0,'C',1);
						$pdf->Cell($col['dtlWidth'][4],$liH,Number_Format($liSuag),1,0,'R',1);
						$pdf->Cell($col['dtlWidth'][5],$liH,Number_Format($liAmt),1,1,'R',1);
					}

					UnSet($arrDtlTmp);
				}
			}


			//직인출력
			if ($var['mode'] == '101'){
				if ($pdf->svcCd == '4'){
					if (Empty($var['printDT'])){
						$var['printDT'] = Date('Y-m-d');
					}

					$prtDt = Explode('-',$var['printDT']);

					$pdf->SetFont($pdf->font_name_kor,'',12);

					//직인 출력
					$sql = 'SELECT m00_mname AS name
							,      m00_jikin AS jikin
							  FROM m00center
							 WHERE m00_mcode = \''.$var['code'].'\'
							 LIMIT 1';

					$arrMst = $conn->get_array($sql);

					if (!empty($arrMst['jikin'])){
						$tmpImg = getImageSize('../mem_picture/'.$arrMst['jikin']);
						$pdf->Image('../mem_picture/'.$arrMst['jikin'], $pdf->width - 18, $pdf->GetY()+5, 21);
					}

					$pdf->SetXY($pdf->left,$pdf->GetY()+5);
					$pdf->Cell($pdf->width,$pdf->row_height, $prtDt[0].'년 '.$prtDt[1].'월 '.$prtDt[2].'일', 0, 1, 'R');

					$pdf->SetXY($pdf->left + $pdf->width * 0.5, $pdf->GetY() + 2);
					$pdf->Cell($pdf->width * 0.19, $pdf->row_height, '기관장:', 0, 0, 'R');
					$pdf->Cell($pdf->width * 0.11, $pdf->row_height, $arrMst['name'], 0, 0, 'L');
					$pdf->Cell($pdf->width * 0.2, $pdf->row_height, '(서 명 또는 인)', 0, 1, 'R');
					$pdf->SetXY($pdf->left + $pdf->width * 0.5, $pdf->GetY() + 2);
					$pdf->Cell($pdf->width * 0.19, $pdf->row_height, '수급자:', 0, 0, 'R');
					$pdf->Cell($pdf->width * 0.11, $pdf->row_height, $col['clientValue'][0], 0, 0, 'L');
					$pdf->Cell($pdf->width * 0.2, $pdf->row_height, '(서 명 또는 인)', 0, 1, 'R');

					$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
					$pdf->Cell($pdf->width, $pdf->row_height, '※ 매월 작성하여 기관 보관.(보관기간 : 작성일로부터 3년)', 0, 1, 'L');
					$pdf->SetX($pdf->left);
					$pdf->Cell($pdf->width, $pdf->row_height, '※ 활동지원기관 및 활동보조인과 수급자 및 보호자(가족)이 협의하여 매월 5일 이전까지 작성', 0, 1, 'L');
					$pdf->SetX($pdf->left);
					//$pdf->Cell($pdf->width, $pdf->row_height, '※ 2인이상 서비스를 제공할 경우 활동지원인력명에 이름을 쓰고 제공한 날에 표시한다.', 0, 1, 'L');
				}

			}
		}
	}

	function lfGetWeekString($pdf,$col,$height){
		//요일
		$pdf->SetX($pdf->left);
		$pdf->SetFont($pdf->font_name_kor,'B',10);
		for($j=0; $j<7; $j++){
			if ($j == 0){//일요일
				$pdf->SetTextColor(255,0,0); //붉은색
			}else if ($j == 6){//토요일
				$pdf->SetTextColor(0,0,255); //파란색
			}else{//평일
				$pdf->SetTextColor(0,0,0); //검정색
			}
			$pdf->Cell($col['calnWidth'][$j], $height, $col['calnWeek'][$j], 1, $j < 6 ? 0 : 1, 'C', true);
		}
		$pdf->SetFont($pdf->font_name_kor,'',10);
		$pdf->SetFillColor(238,238,238);
		$pdf->SetX($pdf->left);
	}

	function lfGetSvcTitle($pdf, $col){
		$liCnt = SizeOf($col['svcWidth']);

		$pdf->SetX($pdf->left);

		if ($pdf->svcCd == '4'){
			$col['svcTitle'][0] = '활동보조인';
		}

		for($j=0; $j<$liCnt; $j++){
			$pdf->Cell($col['svcWidth'][$j],$pdf->row_height,$col['svcTitle'][$j],1,($j == $liCnt - 1 ? 1 : 0),'C',true);
		}
	}

	function lfGetDtlTitle($pdf, $col){
		$liCnt = SizeOf($col['dtlWidth']);

		$pdf->SetX($pdf->left);

		for($j=0; $j<$liCnt; $j++){
			$pdf->Cell($col['dtlWidth'][$j],$pdf->row_height,$col['dtlTitle'][$j],1,($j == $liCnt - 1 ? 1 : 0),'C',true);
		}
	}

	function lfGetSvcFullNm($svcCd, $subCd, $familyYn){
		if($debug) echo $subCd.'/';
		if ($svcCd == '0'){
			$svcNm = '재가요양';
		}else if ($svcCd == '1'){
			$svcNm = '가사간병';
		}else if ($svcCd == '2'){
			$svcNm = '노인돌봄';
		}else if ($svcCd == '3'){
			$svcNm = '산모신생아';
		}else if ($svcCd == '4'){
			if ($subCd == '200'){
				$svcNm = '활동보조';
			}else if ($subCd == '500'){
				$svcNm = '방문목욕';
			}else if ($subCd == '800'){
				$svcNm = '방문간호';
			}
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

	function lfDrawDays($pdf,$col,$lastDay,$days,$liH){
		$liX = $pdf->GetX();
		$liY = $pdf->GetY();

		$pdf->SetXY($pdf->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4],$liY-$liH);

		$liW1 = $col[5] / $lastDay;
		$cnt = 0;
		for($i=1; $i<=$lastDay; $i++){
			if ($i < 10){
				$liW2 = $liW1 * 1.2;
			}else{
				$liW2 = $liW1 * 1.7;
			}


			if (Is_Numeric(StrPos($days,'/'.$i.'/'))){
				if($cnt != 0){
					$coma = ',';
				}else {
					$coma = '';
				}

				//$pdf->Cell($liW2,$liH,' '.$coma.Number_Format($i),0,0,'C');

				$str .= ' '.$coma.Number_Format($i);

				$cnt ++;
			}

		}

		$pdf->MultiCell($col[5], 5, $str, 0, "L");

		$pdf->SetXY($liX,$liY);
	}
?>