<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$sr   = $_POST['sr'];
	$type = $_POST['type'];

	if (Date('Ymd') >= '20180116' && $_SESSION['userArea'] == '05' && $sr == 'S'){
		$IsSugaFixedCd = '05';	//충남
	}else if ($_SESSION['userArea'] == '03' && $sr == 'S'){
		$IsSugaFixedCd = '03';	//강원
	}else if ($_SESSION['userArea'] == '14' && $sr == 'S'){
		$IsSugaFixedCd = '14';	//울산
	}else if ($_SESSION['userArea'] == '08' && $sr == 'S'){
		$IsSugaFixedCd = '08';  //제주
	}else if ($_SESSION['userArea'] == '02' && $sr == 'S'){
		$IsSugaFixedCd = '08';  //경기
	}else if ($_SESSION['userArea'] == '04' && $sr == 'S'){
		$IsSugaFixedCd = '04';  //인천
	}else{
		$IsSugaFixedCd = '';
	}
	//$IsSugaFixedCd = '';

	if ($type == '1'){
		$sql = 'SELECT	DISTINCT
						CONCAT(care.suga_cd,care.suga_sub) AS cd
				,		care.suga_nm AS nm
				,		care.suga_seq AS seq
				,		care.suga_cost AS cost
				,		care.from_dt
				,		care.to_dt
				FROM	care_suga AS care
				INNER	JOIN suga_care AS suga
						ON CONCAT(suga.cd1,suga.cd2,suga.cd3) = care.suga_cd
				WHERE	org_no  = \''.$code.'\'
				AND		suga_sr = \''.$sr.'\'
				ORDER	BY cd, seq DESC';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$data .= 'code='.$row['cd'];
			$data .= '&name='.$row['nm'];
			$data .= '&seq='.$row['seq'];
			$data .= '&cost='.$row['cost'];
			$data .= '&from='.$myF->dateStyle($row['from_dt'],'.');
			$data .= '&to='.$myF->dateStyle($row['to_dt'],'.');
			$data .= chr(13);
		}

		$conn->row_free();

		echo $data;

	}else if ($type == '1_POP'){ //수가 조회
		$gbn = $_POST['gbn'];
		$cd1 = $_POST['cd1'];
		$cd2 = $_POST['cd2'];
		$cd3 = $_POST['cd3'];

		if ($gbn == 'M'){
			$sql = 'SELECT	DISTINCT
							cd1
					,		nm1
					FROM	suga_care';

			if ($sr == 'S'){
				$sql .= '
					WHERE	cd1 >= \'A\'
					AND		cd1 <= \'Z\'';
			}else{
				$sql .= '
					WHERE	cd1 >= \'1\'
					AND		cd1 <= \'9\'';
			}

			$sql .= '
					AND		cd1 < \'Y\'
					ORDER BY cd1';
		}else if ($gbn == 'S'){
			$sql = 'SELECT	DISTINCT
							cd1
					,		cd2
					,		nm2
					FROM	suga_care
					WHERE	nm2 IS NOT NULL
					AND		cd1 = \''.$cd1.'\'
					ORDER BY cd1, cd2';
		}else if ($gbn == 'D'){
			$sql = 'SELECT	DISTINCT
							cd1
					,		cd2
					,		cd3
					,		nm3
					FROM	suga_care
					WHERE	cd1 = \''.$cd1.'\'
					AND		cd2 = \''.$cd2.'\'
					ORDER BY cd1, cd2, cd3';
		}else{
			if ($IsSugaFixedCd){
				$sql = 'SELECT	b.cd1, b.cd2, b.cd3, a.sub_cd AS cd4, a.sub_name AS nm4, a.from_dt, a.to_dt
						FROM	care_fixed_svc AS a
						INNER	JOIN	suga_care AS b
								ON		CONCAT(b.cd1, b.cd2, b.cd3) = a.suga_cd
						WHERE	a.org_no	= \''.$IsSugaFixedCd.'\'
						AND		a.suga_sr	= \''.$sr.'\'
						AND		a.suga_cd	= \''.$cd1.$cd2.$cd3.'\'
						ORDER	BY a.suga_cd, sub_cd';
			}else{
				$sql = 'SELECT	DISTINCT
								suga.cd1
						,		suga.cd2
						,		suga.cd3
						,		care.suga_sub AS cd4
						,		care.suga_nm AS nm4
						FROM	care_suga AS care
						INNER	JOIN	suga_care AS suga
								ON		suga.cd1 = SUBSTR(care.suga_cd,1,1)
								AND		suga.cd2 = SUBSTR(care.suga_cd,2,2)
								AND		suga.cd3 = SUBSTR(care.suga_cd,4,2)
						WHERE	org_no	= \''.$code.'\'
						AND		suga_sr = \''.$sr.'\'
						AND		suga_cd = \''.$cd1.$cd2.$cd3.'\'
						ORDER BY cd1, cd2, cd3, suga_sub';
			}
		}

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($data){
				$data .= '&';
			}

			$data .= 'cd1='.$row['cd1'];
			$data .= '&nm1='.$row['nm1'];
			$data .= '&cd2='.$row['cd2'];
			$data .= '&nm2='.$row['nm2'];
			$data .= '&cd3='.$row['cd3'];
			$data .= '&nm3='.$row['nm3'];
			$data .= '&cd4='.$row['cd4'];
			$data .= '&nm4='.$row['nm4'];
			$data .= '&from_dt='.$row['from_dt'];
			$data .= '&to_dt='.$row['to_dt'];
			$data .= chr(13);
		}

		$conn->row_free();

		echo $data;

	}else if ($type == '1_POP_1'){ //상세서비스
		$sr = $_POST['sr'];
		$cd = $_POST['cd'];

		$sql = 'SELECT	DISTINCT
						suga_sub
				,		suga_seq AS seq
				,		suga_cost AS cost
				,		from_dt
				,		to_dt
				FROM	care_suga
				WHERE	org_no  = \''.$code.'\'
				AND		suga_sr = \''.$sr.'\'
				AND		CONCAT(suga_cd,suga_sub) = \''.$cd.'\'
				ORDER	BY seq DESC';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$data .= 'sub='.$row['suga_sub'];
			$data .= '&seq='.$row['seq'];
			$data .= '&cost='.Number_Format($row['cost']);
			$data .= '&from='.$row['from_dt'];
			$data .= '&to='.$row['to_dt'];
			$data .= chr(13);
		}

		$conn->row_free();

		echo $data;

	}else if ($type == '2'){
		//수가단위
		$year = $_POST['year'];
		$SR = $_POST['SR'];

		$sql = 'SELECT	suga.cd1 AS mst_cd
				,		suga.cd2 AS pro_cd
				,		suga.cd3 AS svc_cd
				,		suga.nm1 AS mst_nm
				,		suga.nm2 AS pro_nm
				,		suga.nm3 AS svc_nm
				,		unit.unit_gbn
				FROM	suga_care AS suga
				LEFT	JOIN care_suga_unit AS unit
						ON unit.year = \''.$year.'\'
						AND unit.suga_cd = CONCAT(suga.cd1,suga.cd2,suga.cd3)';

		if ($SR == 'S'){
			$sql .= '
				WHERE	suga.cd1 >= \'A\'
				AND		suga.cd1 <= \'Z\'';
		}else if ($SR == 'R'){
			$sql .= '
				WHERE	suga.cd1 >= \'1\'
				AND		suga.cd1 <= \'9\'';
		}else{
			$sql .= '
				WHERE	suga.cd1 = \'\'';
		}

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if (!$arr[$row['mst_cd']]){
				$arr[$row['mst_cd']] = Array('cd'=>$row['mst_cd'],'nm'=>$row['mst_nm'],'cnt'=>0);
			}

			if (!$arr[$row['mst_cd']][$row['pro_cd']]){
				$arr[$row['mst_cd']][$row['pro_cd']] = Array('cd'=>$row['pro_cd'],'nm'=>$row['pro_nm'],'cnt'=>0);
			}

			$arr[$row['mst_cd']]['cnt'] ++;
			$arr[$row['mst_cd']][$row['pro_cd']]['cnt'] ++;
			$arr[$row['mst_cd']][$row['pro_cd']][$row['svc_cd']] = Array(
					'cd'=>$row['mst_cd'].$row['pro_cd'].$row['svc_cd']
				,	'nm'=>$row['svc_nm']
				,	'unit'=>$row['unit_gbn']
			);
		}

		$conn->row_free();

		if (Is_Array($arr)){
			foreach($arr as $cd1 => $mst){
				$mstCnt = $mst['cnt'];

				if (Is_Array($mst)){
					foreach($mst as $cd2 => $pro){
						$proCnt = $pro['cnt'];

						if (Is_Array($pro)){
							foreach($pro as $cd3 => $svc){
								if (Is_Array($svc)){
									$data .= 'mstCnt='.$mstCnt;
									$data .= '&proCnt='.$proCnt;
									$data .= '&mstCd='.($mstCnt > 0 ? $mst['cd'] : '');
									$data .= '&proCd='.($proCnt > 0 ? $pro['cd'] : '');
									$data .= '&svcCd='.$svc['cd'];
									$data .= '&mstNm='.($mstCnt > 0 ? $mst['nm'] : '');
									$data .= '&proNm='.($proCnt > 0 ? $pro['nm'] : '');
									$data .= '&svcNm='.$svc['nm'];
									$data .= '&unit='.$svc['unit'];
									$data .= chr(11);

									$mstCnt = 0;
									$proCnt = 0;
								}
							}
						}
					}
				}
			}
		}

		echo $data;

	}else if ($type == '11'){
		/*********************************************************
		 *	자원관리 조회
		 *********************************************************/

		$sr = $_POST['sr'];

		$sql = 'SELECT	DISTINCT
						CONCAT(care.care_svc,care.care_sub) AS svc_cd
				,		suga.suga_nm AS svc_nm
				,		care.care_cust AS cust_cd
				,		cust.cust_nm
				,		cust.cust_gbn
				,		care.care_cd
				,		care.care_cost
				,		cust.per_nm
				,		cust.per_phone
				,		care.from_dt
				,		care.to_dt
				FROM	care_resource AS care';

		if ($IsCareYoyAddon){
			//공통수가
			$sql .= '
				INNER	JOIN (
							SELECT	org_no, suga_sr, suga_cd, suga_sub, suga_nm, from_dt
							FROM	care_suga
							WHERE	org_no	= \''.$code.'\'
							AND		suga_sr	= \''.$sr.'\'
							UNION	ALL
							SELECT	\''.$code.'\' AS org_no, \''.$sr.'\' AS suga_sr, LEFT(code,5) AS suga_cd, MID(code,6) AS suga_sub, name, \'20100101\' as from_dt
							FROM	care_suga_comm
						) AS suga
						ON		suga.org_no	= care.org_no
						AND		suga.suga_sr	= care.care_sr
						AND		suga.suga_cd	= care.care_svc
						AND		suga.suga_sub	= care.care_sub';

			if($_SESSION['userArea'] == '05'){
				$sql .= '	AND CASE when DATE_FORMAT(care.from_dt,\'%Y%m%d\') < \'20180101\' then DATE_FORMAT(suga.from_dt,\'%Y%m%d\') < \'20180101\' else DATE_FORMAT(suga.from_dt,\'%Y%m%d\') >= \'20180101\' end';
			}

		}else{
			$sql .= '
				INNER	JOIN	care_suga AS suga
						ON		suga.org_no		= care.org_no
						AND		suga.suga_sr	= care.care_sr
						AND		suga.suga_cd	= care.care_svc
						AND		suga.suga_sub	= care.care_sub';
		}

		$sql .= '
				INNER	JOIN	care_cust AS cust
						ON		cust.org_no = care.org_no
						AND		cust.cust_cd = care.care_cust
				WHERE	care.org_no	  = \''.$code.'\'
				AND		care.care_sr  = \''.$sr.'\'
				AND		care.del_flag = \'N\'
				ORDER	BY svc_nm,cust_nm,per_nm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$data .= 'svcCd='.$row['svc_cd'];
			$data .= '&svcNm='.$row['svc_nm'];
			$data .= '&custCd='.$row['cust_cd'];
			$data .= '&custNm='.$row['cust_nm'];
			$data .= '&gbn='.$row['cust_gbn'];
			$data .= '&cd='.$row['care_cd'];
			$data .= '&cost='.$row['care_cost'];
			$data .= '&pernm='.$row['per_nm'];
			$data .= '&pertel='.$row['per_phone'];
			$data .= '&from='.Str_Replace('-','',$row['from_dt']);
			$data .= '&to='.Str_Replace('-','',$row['to_dt']);
			$data .= chr(11);
		}

		$conn->row_free();

		echo $data;



	}else if ($type == '11_POP'){
		/*********************************************************
		 *	자원등록 서비스
		 *********************************************************/
		$sr  = $_POST['sr'];
		$svc = $_POST['svc'];

		/*
		$sql = 'SELECT	DISTINCT
						care.suga_cd AS cd
				,		suga.nm3 AS nm
				FROM	care_suga AS care
				INNER	JOIN suga_care AS suga
						ON CONCAT(suga.cd1,suga.cd2,suga.cd3) = care.suga_cd
				WHERE	care.org_no  = \''.$code.'\'
				AND		care.suga_sr = \''.$sr.'\'';
		*/

		$sql = 'SELECT	DISTINCT
						CONCAT(care.suga_cd,care.suga_sub) AS cd
				,		care.suga_nm AS nm
				FROM	care_suga AS care
				INNER	JOIN	suga_care AS suga
						ON		CONCAT(suga.cd1,suga.cd2,suga.cd3) = care.suga_cd
				WHERE	care.org_no	= \''.$code.'\'
				AND		care.suga_sr= \''.$sr.'\'';

		if ($svc){
			$sql .= ' AND	CONCAT(care.suga_cd,care.suga_sub) = \''.$svc.'\'';
		}

		$sql .= ' ORDER	BY nm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		if (!$svc){
			$data = '<option value="">- 서비스를 선택하여 주십시오. -</option>';
		}

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($row['nm']){
				$data .= '<option value="'.$row['cd'].'">'.$row['nm'].'</option>';
			}
		}

		$conn->row_free();

		echo $data;


	}else if ($type == '11_POP_LOAD'){
		/*********************************************************
		 *	자원조회
		 *********************************************************/
		$sub= SubStr($svc,5,2);
		$svc= SubStr($svc,0,5);

		$sql = 'SELECT	care_cost
				,		from_dt
				,		to_dt
				FROM	care_resource
				WHERE	org_no	 = \''.$code.'\'
				AND		care_sr	 = \''.$sr.'\'
				AND		care_svc = \''.$svc.'\'
				AND		care_sub = \''.$sub.'\'
				AND		care_cd	 = \''.$cd.'\'
				AND		del_flag = \'N\'';

		$row = $conn->get_array($sql);

		$data .= 'cost='.$row['care_cost'];
		$data .= '&from='.$row['from_dt'];
		$data .= '&to='.$row['to_dt'];

		echo $data;


	}else if ($type == '11_POP_LIST'){
		/*********************************************************
		 *	자원이력내역
		 *********************************************************/
		$sr	= $_POST['sr'];
		$svc= $_POST['svc'];
		$sub= SubStr($svc,5,2);
		$svc= SubStr($svc,0,5);


		$sql = 'SELECT	care.care_cd
				,		cust.cust_gbn
				,		care.care_cust AS cust_cd
				,		cust.cust_nm
				,		care.care_cost
				,		care.from_dt
				,		care.to_dt
				FROM	care_resource AS care
				INNER	JOIN	care_cust AS cust
						ON		cust.org_no		= care.org_no
						AND		cust.cust_cd	= care.care_cust
				WHERE	care.org_no		= \''.$code.'\'
				AND		care.care_sr	= \''.$sr.'\'
				AND		care.care_svc	= \''.$svc.'\'
				AND		care.care_sub	= \''.$sub.'\'
				AND		care.del_flag	= \'N\'
				ORDER	BY cust_gbn,cust_nm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$data .= 'cd='.$row['care_cd'];
			$data .= '&gbn='.$row['cust_gbn'];
			$data .= '&custCd='.$row['cust_cd'];
			$data .= '&custNm='.$row['cust_nm'];
			$data .= '&cost='.$row['care_cost'];
			$data .= '&from='.$row['from_dt'];
			$data .= '&to='.$row['to_dt'];
			$data .= chr(11);
		}

		$conn->row_free();

		echo $data;


	}else if ($type == 'ILJUNG_REG'){
		$year  = $_POST['year'];
		$month = $_POST['month'];

		$sql = 'SELECT	DISTINCT
						CONCAT(suga_cd,suga_sub) AS cd
				,		suga.nm1 AS mst_nm
				,		suga.nm2 AS pro_nm
				,		suga.nm3 AS svc_nm
				,		care.suga_nm AS sub_nm
				FROM	care_suga AS care
				INNER	JOIN suga_care AS suga
						ON CONCAT(suga.cd1,suga.cd2,suga.cd3) = care.suga_cd
				WHERE	care.org_no  = \''.$code.'\'
				AND		care.suga_sr = \''.$sr.'\'
				AND		DATE_FORMAT(care.from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				AND		DATE_FORMAT(care.to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$data .= 'cd='.$row['cd'];
			$data .= '&mstNm='.Str_Replace('<br>','',$row['mst_nm']);
			$data .= '&proNm='.Str_Replace('<br>','',$row['pro_nm']);
			$data .= '&svcNm='.Str_Replace('<br>','',$row['svc_nm']);
			$data .= '&subNm='.Str_Replace('<br>','',$row['sub_nm']);
			$data .= chr(13);
		}

		$conn->row_free();

		echo $data;

	}else if ($type == 'RESOURCE_REG'){
		$sugaCd = $_POST['sugaCd'];
		$year	= $_POST['year'];
		$month	= $_POST['month'];
		$sugaSub= SubStr($sugaCd,5,2);
		$sugaCd	= SubStr($sugaCd,0,5);

		$sql = 'SELECT	care.care_cd
				,		cust.cust_gbn
				,		care.care_cust
				,		cust.cust_nm
				,		cust.per_nm
				,		cust.per_phone
				,		care.care_cost
				FROM	care_resource AS care
				INNER	JOIN	care_cust AS cust
						ON		cust.org_no	= care.org_no
						AND		cust.cust_cd= care.care_cust
				WHERE	care.org_no		= \''.$code.'\'
				AND		care.care_sr	= \''.$sr.'\'
				AND		care.care_svc	= \''.$sugaCd.'\'
				AND		care.care_sub	= \''.$sugaSub.'\'
				AND		DATE_FORMAT(care.from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				AND		DATE_FORMAT(care.to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
				AND		care.del_flag	= \'N\'
				ORDER	BY cust_gbn,cust_nm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$data .= 'cd='.$row['care_cust'];
			$data .= '&gbn='.$row['cust_gbn'];
			$data .= '&name='.$row['cust_nm'];
			$data .= '&cost='.$row['care_cost'];
			$data .= '&pernm='.$row['per_nm'];
			$data .= '&pertel='.$row['per_phone'];
			$data .= '__TAP__';
		}

		$conn->row_free();

		echo $data;



	}else if ($type == '21'){
		//사업계획
		$year = $_POST['year'];

		//데이타
		$sql = 'SELECT	plan_cd AS cd
				,		plan_target AS target
				,		plan_target_gbn AS gbn
				,		plan_budget AS budget
				,		plan_cnt AS cnt
				,		plan_cont AS cont
				,		plan_effect AS effect
				,		plan_eval AS eval
				FROM	care_year_plan
				WHERE	org_no		= \''.$code.'\'
				AND		plan_year	= \''.$year.'\'
				AND		plan_sr		= \''.$sr.'\'';

		$plan = $conn->_fetch_array($sql,'cd');

		$sql = 'SELECT	DISTINCT
						care.suga_cd
				,		suga.cd1 AS mst_cd
				,		suga.cd2 AS pro_cd
				,		suga.cd3 AS svc_cd
				,		suga.nm1 AS mst_nm
				,		suga.nm2 AS pro_nm
				,		suga.nm3 AS svc_nm
				FROM	care_suga AS care
				INNER	JOIN suga_care AS suga
						ON	CONCAT(suga.cd1,suga.cd2,suga.cd3) = care.suga_cd
				WHERE	care.org_no = \''.$code.'\'
				AND		LEFT(care.from_dt,4) <= \''.$year.'\'
				AND		LEFT(care.to_dt,4) >= \''.$year.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$row['mst_nm'] = Str_Replace(' ','',$row['mst_nm']);
			$row['pro_nm'] = Str_Replace(' ','',$row['pro_nm']);
			$row['mst_nm'] = Explode('<br>',$row['mst_nm']);
			$row['pro_nm'] = Explode('<br>',$row['pro_nm']);
			$row['mst_nm'] = $row['mst_nm'][0];
			$row['pro_nm'] = $row['pro_nm'][0];

			if (!$arr[$row['mst_cd']]){
				$arr[$row['mst_cd']] = Array('cd'=>$row['mst_cd'],'nm'=>$row['mst_nm'],'cnt'=>0);
			}

			if (!$arr[$row['mst_cd']][$row['pro_cd']]){
				$arr[$row['mst_cd']][$row['pro_cd']] = Array('cd'=>$row['pro_cd'],'nm'=>$row['pro_nm'],'cnt'=>0);
			}

			$arr[$row['mst_cd']]['cnt'] ++;
			$arr[$row['mst_cd']][$row['pro_cd']]['cnt'] ++;
			$arr[$row['mst_cd']][$row['pro_cd']][$row['svc_cd']] = Array(
					'suga'=>$row['suga_cd']
				,	'cd'=>$row['svc_cd']
				,	'nm'=>$row['svc_nm']
				,	'target'=>$plan[$row['suga_cd']]['target']
				,	'gbn'=>$plan[$row['suga_cd']]['gbn']
				,	'budget'=>$plan[$row['suga_cd']]['budget']
				,	'cnt'=>$plan[$row['suga_cd']]['cnt']
				,	'cont'=>$plan[$row['suga_cd']]['cont']
				,	'effect'=>$plan[$row['suga_cd']]['effect']
				,	'eval'=>$plan[$row['suga_cd']]['eval']
			);
		}

		$conn->row_free();

		Unset($plan);
		
		if (!is_array($row)) continue;
	

		if ($IsExcelClass){
		
			if (Is_Array($arr)){
				foreach($arr as $cd1 => $mst){
					$mstCnt = $mst['cnt'];

					if (Is_Array($mst)){
						foreach($mst as $cd2 => $pro){
							@$proCnt = $pro['cnt'];

							if (Is_Array($pro)){
								foreach($pro as $cd3 => $svc){
									if (Is_Array($svc)){
										
										$rowNo ++;
										$sheet->getRowDimension($rowNo)->setRowHeight(-1);
										$mstCd = ($mstCnt > 0 ? $mst['cd'] : '');
										$proCd = ($proCnt > 0 ? $pro['cd'] : '');
										$mstNm = ($mstCnt > 0 ? $mst['nm'] : '');
										$proNm = ($proCnt > 0 ? $pro['nm'] : '');
										
										if ($svc['target'] > 0){
											$targetGbn = $svc['target'].($svc['gbn'] == '1' ? '명' : '회');
										}
										
										$budget = $svc['target'];
										
										if($mstCd) $sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'A'.($rowNo+$mstCnt-1), 'val'=>$mstNm, 'H'=>'L','backcolor'=>$bgcolor) );	
										if($proCd) $sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'B'.($rowNo+$proCnt-1), 'val'=>$proNm, 'H'=>'L','backcolor'=>$bgcolor) );
										
									
										$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>$svc['nm'], 'H'=>'L','backcolor'=>$bgcolor) );
										$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>($svc['target'] > 0 ? $svc['target'].($svc['gbn'] == '1' ? '명' : '회') : ''), 'H'=>'L','backcolor'=>$bgcolor) );
										$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>$budget, 'H'=>'L','backcolor'=>$bgcolor) );
										$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>$svc['cnt'], 'H'=>'L','backcolor'=>$bgcolor) );
										$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>$svc['cont'], 'H'=>'L','backcolor'=>$bgcolor) );
										$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>$svc['effect'], 'H'=>'L','backcolor'=>$bgcolor) );
										$sheet->SetData( Array('F'=>'I'.$rowNo, 'val'=>$svc['eval'], 'H'=>'L','backcolor'=>$bgcolor) );

										$mstCnt = 0;
										$proCnt = 0;
									}
								}
							}
						}
					}
				}
			}

		
		}else {
			if (Is_Array($arr)){
				foreach($arr as $cd1 => $mst){
					$mstCnt = $mst['cnt'];

					if (Is_Array($mst)){
						foreach($mst as $cd2 => $pro){
							@$proCnt = $pro['cnt'];

							if (Is_Array($pro)){
								foreach($pro as $cd3 => $svc){
									if (Is_Array($svc)){
										$data .= 'mstCnt='.$mstCnt;
										$data .= '&proCnt='.$proCnt;
										$data .= '&mstCd='.($mstCnt > 0 ? $mst['cd'] : '');
										$data .= '&proCd='.($proCnt > 0 ? $pro['cd'] : '');
										$data .= '&svcCd='.$svc['cd'];
										$data .= '&mstNm='.($mstCnt > 0 ? $mst['nm'] : '');
										$data .= '&proNm='.($proCnt > 0 ? $pro['nm'] : '');
										$data .= '&svcNm='.$svc['nm'];
										$data .= '&suga='.$svc['suga'];
										$data .= '&target='.$svc['target'];
										$data .= '&gbn='.$svc['gbn'];
										$data .= '&budget='.$svc['budget'];
										$data .= '&cnt='.$svc['cnt'];
										$data .= '&cont='.$svc['cont'];
										$data .= '&effect='.$svc['effect'];
										$data .= '&eval='.$svc['eval'];
										$data .= chr(11);

										$mstCnt = 0;
										$proCnt = 0;
									}
								}
							}
						}
					}
				}
			}
		}

		echo $data;

	}else if ($type == '21_POP'){
		//사업계획 수정
		$year = $_POST['year'];
		$suga = $_POST['code'];

		$sql = 'SELECT	nm1
				,		nm2
				,		nm3
				FROM	suga_care
				WHERE	CONCAT(cd1,cd2,cd3) = \''.SubStr($suga,0,5).'\'';

		$arr = $conn->get_array($sql);

		$sql = 'SELECT	unit_gbn
				FROM	care_suga_unit
				WHERE	year = \''.$year.'\'
				AND		suga_cd = \''.$suga.'\'';

		$unit = $conn->get_data($sql);

		if (StrLen($suga) == 7){
			$sql = 'SELECT	suga_nm
					FROM	care_suga
					WHERE	org_no	= \''.$code.'\'
					AND		suga_sr = \''.$sr.'\'
					AND		CONCAT(suga_cd, suga_sub) = \''.$suga.'\'
					AND		LEFT(from_dt,4) <= \''.$year.'\'
					AND		LEFT(to_dt,4)	>= \''.$year.'\'';
			$subNm = $conn->get_data($sql);
		}

		$data .= 'mstNm='.Str_Replace('<br>','',$arr['nm1']);
		$data .= '&proNm='.Str_Replace('<br>','',$arr['nm2']);
		$data .= '&svcNm='.Str_Replace('<br>','',$arr['nm3']);
		$data .= '&subNm='.$subNm;
		$data .= '&unit='.$unit;

		Unset($arr);

		echo $data;

	}else if ($type == '21_POP_1'){
		//사업계획 데이타
		$year = $_POST['year'];
		$suga = $_POST['code'];

		$sql = 'SELECT	plan_target
				,		plan_target_gbn
				,		plan_budget
				,		plan_cnt
				,		plan_cont
				,		plan_effect
				,		plan_eval
				FROM	care_year_plan
				WHERE	org_no		= \''.$code.'\'
				AND		plan_year	= \''.$year.'\'
				AND		plan_cd		= \''.$suga.'\'
				AND		plan_sr		= \''.$sr.'\'';

		$row = $conn->get_array($sql);

		$data .= 'target='.$row['plan_target'];
		$data .= '&targetGbn='.$row['plan_target_gbn'];
		$data .= '&budget='.$row['plan_budget'];
		$data .= '&cnt='.$row['plan_cnt'];
		$data .= '&cont='.StripSlashes($row['plan_cont']);
		$data .= '&effect='.StripSlashes($row['plan_effect']);
		$data .= '&eval='.StripSlashes($row['plan_eval']);

		Unset($row);

		echo $data;

	}else if ($type == '31'){
		//일정표출력(상담지원)
		$sr   = $_POST['sr'];
		$year = $_POST['year'];

		$sql = 'SELECT	DISTINCT
						m02_yjumin AS jumin
				,		m02_yname AS name
				,		DATE_FORMAT(MIN(mem_his.join_dt),\'%Y%m\') AS join_dt
				,		DATE_FORMAT(MAX(IFNULL(mem_his.quit_dt,\'9999-12-31\')),\'%Y%m\') AS quit_dt
				FROM	m02yoyangsa
				INNER	JOIN mem_option
						ON	mem_option.org_no = m02_ccode
						AND	mem_option.mo_jumin = m02_yjumin
						AND	mem_option.counsel_yn = \'Y\'
				INNER	JOIN mem_his
						ON	mem_his.org_no = m02_ccode
						AND	mem_his.jumin = m02_yjumin
						AND	LEFT(mem_his.join_dt,4) <= \''.$year.'\'
						AND	LEFT(IFNULL(mem_his.quit_dt,\'9999-12-31\'),4) >= \''.$year.'\'
				WHERE	m02_ccode = \''.$code.'\'
				GROUP	BY m02_yjumin
				ORDER	BY name';

		$mem = $conn->_fetch_array($sql);

		//일정횟수
		$sql = 'SELECT	jumin
				,		SUM(CASE SUBSTR(iljung_dt,5,2) WHEN \'01\' THEN 1 ELSE 0 END) AS m1
				,		SUM(CASE SUBSTR(iljung_dt,5,2) WHEN \'02\' THEN 1 ELSE 0 END) AS m2
				,		SUM(CASE SUBSTR(iljung_dt,5,2) WHEN \'03\' THEN 1 ELSE 0 END) AS m3
				,		SUM(CASE SUBSTR(iljung_dt,5,2) WHEN \'04\' THEN 1 ELSE 0 END) AS m4
				,		SUM(CASE SUBSTR(iljung_dt,5,2) WHEN \'05\' THEN 1 ELSE 0 END) AS m5
				,		SUM(CASE SUBSTR(iljung_dt,5,2) WHEN \'06\' THEN 1 ELSE 0 END) AS m6
				,		SUM(CASE SUBSTR(iljung_dt,5,2) WHEN \'07\' THEN 1 ELSE 0 END) AS m7
				,		SUM(CASE SUBSTR(iljung_dt,5,2) WHEN \'08\' THEN 1 ELSE 0 END) AS m8
				,		SUM(CASE SUBSTR(iljung_dt,5,2) WHEN \'09\' THEN 1 ELSE 0 END) AS m9
				,		SUM(CASE SUBSTR(iljung_dt,5,2) WHEN \'10\' THEN 1 ELSE 0 END) AS m10
				,		SUM(CASE SUBSTR(iljung_dt,5,2) WHEN \'11\' THEN 1 ELSE 0 END) AS m11
				,		SUM(CASE SUBSTR(iljung_dt,5,2) WHEN \'12\' THEN 1 ELSE 0 END) AS m12
				FROM	care_counsel_iljung
				WHERE	org_no = \''.$code.'\'
				AND		iljung_sr = \''.$sr.'\'
				AND		LEFT(iljung_dt,4) = \''.$year.'\'
				GROUP	BY jumin';

		$iljung = $conn->_fetch_array($sql,'jumin');

		if (Is_Array($mem)){
			foreach($mem as $jumin => $row){
				$data .= 'jumin='.$ed->en($row['jumin']);
				$data .= '&name='.$row['name'];
				$data .= '&from='.$row['join_dt'];
				$data .= '&to='.$row['quit_dt'];

				for($i=1; $i<=12; $i++){
					$data .= '&m'.$i.'='.$iljung[$row['jumin']]['m'.$i];
				}

				$data .= chr(11);
			}
		}

		echo $data;

	}else if ($type == '41'){
		//실적관리(재가지원) 서비스 리스트
		$sr		= $_POST['sr'];
		$year	= $_POST['year'];
		$month	= IntVal($_POST['month']);
		$month	= ($month < 10 ? '0' : '').$month;
		$fromDay= IntVal($_POST['fromDay']);
		$toDay	= IntVal($_POST['toDay']);
		$fromDay= ($fromDay < 10 ? '0' : '').$fromDay;
		$toDay	= ($toDay < 10 ? '0' : '').$toDay;
		$service= $_POST['service'];
		$resource = $_POST['resource'];
		$order	= $_POST['order'];

		if ($fromDay < 1) $fromDay = $toDay;
		if ($toDay < 1) $toDay = $fromDay;

		$fromDt	= str_replace('-','',$_POST['from']);
		$toDt	= str_replace('-','',$_POST['to']);

		if (!$fromDt) $fromDt = $year.$month.$fromDay;
		if (!$toDt) $toDt = $year.$month.$toDay;

		$sql = 'SELECT	DISTINCT
						t01_jumin AS jumin
				,		m03_name AS name
				,		t01_sugup_date AS date
				,		t01_sugup_fmtime AS from_time
				,		t01_sugup_totime AS to_time
				,		t01_sugup_soyotime AS proctime
				,		t01_sugup_seq AS seq
				,		t01_status_gbn AS stat
				,		t01_suga_code1 AS suga_cd
				,		suga.suga_nm AS suga_nm
				,		t01_yoyangsa_id1 AS res_cd
				,		t01_yname1 As res_nm
				,		t01_suga_tot AS suga_cost
				,		IFNULL(mst_jumin.jumin,t01_jumin) AS real_jumin
				FROM	t01iljung
				INNER	JOIN m03sugupja
						ON	m03_ccode = t01_ccode
						AND	m03_jumin = t01_jumin
				INNER	JOIN	mst_jumin
						ON		mst_jumin.org_no = t01_ccode
						AND		mst_jumin.gbn = \'1\'
						AND		mst_jumin.code = t01_jumin';

		if ($IsCareYoyAddon){
			//공통수가
			$sql .= '
				INNER	JOIN (
							SELECT	org_no, suga_sr, suga_cd, suga_sub, suga_nm, from_dt, to_dt
							FROM	care_suga
							WHERE	org_no	= \''.$code.'\'
							AND		suga_sr	= \''.$sr.'\'
							UNION	ALL
							SELECT	\''.$code.'\' AS org_no, \''.$sr.'\' AS suga_sr, LEFT(code,5) AS suga_cd, MID(code,6) AS suga_sub, name, from_dt, to_dt
							FROM	care_suga_comm
						) AS suga
						ON		suga.org_no		= t01_ccode
						AND		suga.suga_sr	= t01_mkind
						AND		CONCAT(suga.suga_cd,suga.suga_sub)= t01_suga_code1
						AND		REPLACE(suga.from_dt,\'-\',\'\') <= t01_sugup_date
						AND		REPLACE(suga.to_dt,\'-\',\'\')	 >= t01_sugup_date';
		}else{
			$sql .= '
				INNER	JOIN	care_suga AS suga
						ON		suga.org_no	= t01_ccode
						AND		suga.suga_sr= \''.$sr.'\'
						AND		CONCAT(suga.suga_cd,suga.suga_sub)= t01_suga_code1
						AND		REPLACE(suga.from_dt,\'-\',\'\') <= t01_sugup_date
						AND		REPLACE(suga.to_dt,\'-\',\'\')	 >= t01_sugup_date';
		}

		$sql .= '
				WHERE	t01_ccode		 = \''.$code.'\'
				AND		t01_mkind		 = \''.$sr.'\'
				AND		t01_sugup_date	>= \''.$fromDt.'\'
				AND		t01_sugup_date	<= \''.$toDt.'\'
				AND		t01_del_yn		 = \'N\'';

		if ($service){
			$sql .= '
				AND		t01_suga_code1 = \''.$service.'\'';
		}

		if ($resource){
			$sql .= '
				AND		t01_yoyangsa_id1 = \''.$resource.'\'';
		}

		if ($order == '1'){
			$sql .= ' ORDER BY date,from_time,to_time';
		}else if ($order == '2'){
			$sql .= ' ORDER BY name,date,from_time,to_time';
		}else if ($order == '3'){
			$sql .= ' ORDER BY suga_nm,date,from_time,to_time';
		}else if ($order == '4'){
			$sql .= ' ORDER BY res_nm,date,from_time,to_time';
		}

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			//성별
			$gender = SubStr($row['real_jumin'],6,1);

			if ($gender){
				$gender = $gender % 2;
			}

			$data .= 'jumin='.$ed->en($row['jumin']);
			$data .= '&name='.$row['name'];
			$data .= '&dt='.$row['date'];
			$data .= '&date='.Date('d',StrToTime($row['date']));
			$data .= '&week='.Date('w',StrToTime($row['date']));
			$data .= '&from='.$row['from_time'];
			$data .= '&seq='.$row['seq'];
			$data .= '&stat='.$row['stat'];
			$data .= '&sugaCd='.$row['suga_cd'];
			$data .= '&sugaNm='.$row['suga_nm'];
			$data .= '&resCd='.$ed->en($row['res_cd']);
			$data .= '&resNm='.$row['res_nm'];
			$data .= '&cost='.$row['suga_cost'];
			$data .= '&birth='.$myF->issToBirthday($row['real_jumin'],'.');
			$data .= '&gender='.$gender;
			$data .= chr(11);
		}

		$conn->row_free();

		echo $data;

	}else if ($type == '42'){
		//실적관ㄹ(상담지원)
		$sr		= $_POST['sr'];
		$year	= $_POST['year'];
		$month	= IntVal($_POST['month']);
		$month	= ($month < 10 ? '0' : '').$month;
		$order	= $_POST['order'];

		$sql = 'SELECT	DISTINCT
						jumin
				,		m02_yname AS name
				,		iljung_dt AS date
				,		iljung_seq AS seq
				,		iljung_jumin AS client_cd
				,		m03_name AS client_nm
				,		iljung_from AS from_time
				,		iljung_to AS to_time
				,		iljung_proc AS proctime
				,		iljung_stat AS stat
				FROM	care_counsel_iljung AS iljung
				INNER	JOIN m02yoyangsa
						ON m02_ccode = iljung.org_no
						AND m02_yjumin = iljung.jumin
				INNER	JOIN m03sugupja
						ON m03_ccode = iljung.org_no
						AND m03_jumin = iljung.iljung_jumin
				WHERE	org_no = \''.$code.'\'
				AND		iljung_sr = \''.$sr.'\'
				AND		LEFT(iljung_dt,6) = \''.$year.$month.'\'
				AND		del_flag = \'N\'';

		if ($order == '1'){
			$sql .= ' ORDER BY date,from_time,to_time';
		}else if ($order == '2'){
			$sql .= ' ORDER BY name,date,from_time,to_time';
		}else if ($order == '3'){
			$sql .= ' ORDER BY suga_nm,date,from_time,to_time';
		}else if ($order == '4'){
			$sql .= ' ORDER BY client_nm,date,from_time,to_time';
		}

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$data .= 'jumin='.$ed->en($row['jumin']);
			$data .= '&name='.$row['name'];
			$data .= '&date='.Date('d',StrToTime($row['date']));
			$data .= '&week='.Date('w',StrToTime($row['date']));
			$data .= '&from='.$row['from_time'];
			$data .= '&to='.$row['to_time'];
			$data .= '&proc='.Round($row['proctime']/60,1);
			$data .= '&seq='.$row['seq'];
			$data .= '&stat='.$row['stat'];
			$data .= '&sugaCd=';
			$data .= '&sugaNm=상담지원';
			$data .= '&resCd=';
			$data .= '&resNm='.$row['client_nm'];
			$data .= '&cost=';
			$data .= chr(11);
		}

		$conn->row_free();

		echo $data;

	}else if (SubStr($type,0,2) == '51'){
		//보고서
		$sr		= $_POST['sr'];
		$year	= $_POST['year'];
		$month	= $myF->monthStr($_POST['month']);
		$lastDay= $myF->lastDay($year,$month);

		//수가별 단위(1:명, 2:횟수)
		$queryType = 1;

		if ($sr == 'S'){
			$kacd = '/KN85C001/KN88C003/KN82C001/KN87C002/KN73C001/KN17C001/KN17C005/KN17C002/KN86C002/KN12C005/KN31C001/KN31C003/KN25C003';

			if (!is_numeric(StrPos($kacd,'/'.$code))){
				$queryType = 2;
			}
		}

		if ($queryType == 2){
			$sql = 'SELECT	LEFT(plan_cd,5) AS cd
					,		SUM(plan_target) AS cnt
					,		plan_target_gbn AS gbn
					FROM	care_year_plan
					WHERE	org_no		= \''.$code.'\'
					AND		plan_year	= \''.$year.'\'
					AND		plan_sr		= \''.$sr.'\'
					AND		LENGTH(plan_cd) = 7
					GROUP	BY LEFT(plan_cd,5)';
		}else{
			$sql = 'SELECT	plan_cd AS cd
					,		plan_target AS cnt
					,		plan_target_gbn AS gbn
					FROM	care_year_plan
					WHERE	org_no		= \''.$code.'\'
					AND		plan_year	= \''.$year.'\'
					AND		plan_sr		= \''.$sr.'\'';
		}

		$target = $conn->_fetch_array($sql,'cd');

		$sql = 'SELECT	suga_cd AS cd
				,		unit_gbn AS gbn
				FROM	care_suga_unit
				WHERE	year = \''.$year.'\'';

		$unit = $conn->_fetch_array($sql,'cd');

		if ($type == '51'){
			//월별소계
			$sql = 'SELECT	close_cd AS cd
					,		close_cnt AS cnt
					,		close_pay AS pay
					,		close_per AS per
					FROM	care_close_month
					WHERE	org_no		= \''.$code.'\'
					AND		close_sr	= \''.$sr.'\'
					AND		close_yymm	= \''.$year.$month.'\'
					AND		close_gbn	= \'11\'';

			$sumData = $conn->_fetch_array($sql,'cd');

		}else if (SubStr($type,0,10) == '51_QUARTER' ||
				SubStr($type,0,9) == '51_MIDDLE'){
			//분기별 상세(중분류) 소계
			$quarter = SubStr($type,StrLen($type)-1,StrLen($type));

			if ($quarter == '1'){
				$from = '01';
				$to = '03';
			}else if ($quarter == '2'){
				$from = '04';
				$to = '06';
			}else if ($quarter == '3'){
				$from = '07';
				$to = '09';
			}else{
				$from = '10';
				$to = '12';
			}

			if ($prtGbn == '1'){
				//기본
				$sql = 'SELECT	close_cd AS cd
						,		SUM(close_cnt) AS cnt
						,		SUM(close_pay) AS pay
						,		SUM(close_per) AS per
						/*,		COUNT(close_cd) AS per*/
						FROM	care_close_month
						WHERE	org_no		= \''.$code.'\'
						AND		close_sr	= \''.$sr.'\'
						AND		close_yymm >= \''.$year.$from.'\'
						AND		close_yymm <= \''.$year.$to.'\'
						AND		close_gbn	= \'11\'
						GROUP	BY close_cd';

				$sumData = $conn->_fetch_array($sql,'cd');

				$sql = 'SELECT	close_cd AS cd
						,		SUM(close_cnt) AS cnt
						,		SUM(close_pay) AS pay
						,		SUM(close_per) AS per
						/*,		COUNT(close_cd) AS per*/
						FROM	care_close_month
						WHERE	org_no		= \''.$code.'\'
						AND		close_sr	= \''.$sr.'\'
						AND		close_yymm >= \''.$year.'01\'
						AND		close_yymm <= \''.$year.$to.'\'
						AND		close_gbn	= \'11\'
						GROUP	BY close_cd';

				$sumTotData = $conn->_fetch_array($sql,'cd');
			}else{
				//상세
				$sql = 'SELECT	CONCAT(close_yymm,\'_\',close_cd) AS idx
						,		close_yymm AS yymm
						,		close_cd AS cd
						,		SUM(close_cnt) AS cnt
						,		SUM(close_pay) AS pay
						,		SUM(close_per) AS per
						/*,		COUNT(close_cd) AS per*/
						FROM	care_close_month
						WHERE	org_no		= \''.$code.'\'
						AND		close_sr	= \''.$sr.'\'
						AND		close_yymm >= \''.$year.$from.'\'
						AND		close_yymm <= \''.$year.$to.'\'
						AND		close_gbn	= \'11\'
						GROUP	BY close_yymm,close_cd';

				$sumData = $conn->_fetch_array($sql,'idx');
			}

			if ($prtGbn == '2' || SubStr($type,0,9) == '51_MIDDLE'){
				$sql = 'SELECT	close_cd AS cd
						,		SUM(close_cnt) AS cnt
						,		SUM(close_pay) AS pay
						,		SUM(close_per) AS per
						/*,		COUNT(close_cd) AS per*/
						FROM	care_close_month
						WHERE	org_no		= \''.$code.'\'
						AND		close_sr	= \''.$sr.'\'
						AND		close_yymm >= \''.$year.'01\'
						AND		close_yymm <= \''.$year.'03\'
						AND		close_gbn	= \'11\'
						GROUP	BY close_cd';

				$sumTotData[1] = $conn->_fetch_array($sql,'cd');

				$sql = 'SELECT	close_cd AS cd
						,		SUM(close_cnt) AS cnt
						,		SUM(close_pay) AS pay
						,		SUM(close_per) AS per
						/*,		COUNT(close_cd) AS per*/
						FROM	care_close_month
						WHERE	org_no		= \''.$code.'\'
						AND		close_sr	= \''.$sr.'\'
						AND		close_yymm >= \''.$year.'04\'
						AND		close_yymm <= \''.$year.'06\'
						AND		close_gbn	= \'11\'
						GROUP	BY close_cd';

				$sumTotData[2] = $conn->_fetch_array($sql,'cd');

				$sql = 'SELECT	close_cd AS cd
						,		SUM(close_cnt) AS cnt
						,		SUM(close_pay) AS pay
						,		SUM(close_per) AS per
						/*,		COUNT(close_cd) AS per*/
						FROM	care_close_month
						WHERE	org_no		= \''.$code.'\'
						AND		close_sr	= \''.$sr.'\'
						AND		close_yymm >= \''.$year.'07\'
						AND		close_yymm <= \''.$year.'09\'
						AND		close_gbn	= \'11\'
						GROUP	BY close_cd';

				$sumTotData[3] = $conn->_fetch_array($sql,'cd');

				$sql = 'SELECT	close_cd AS cd
						,		SUM(close_cnt) AS cnt
						,		SUM(close_pay) AS pay
						,		SUM(close_per) AS per
						/*,		COUNT(close_cd) AS per*/
						FROM	care_close_month
						WHERE	org_no		= \''.$code.'\'
						AND		close_sr	= \''.$sr.'\'
						AND		close_yymm >= \''.$year.'10\'
						AND		close_yymm <= \''.$year.'12\'
						AND		close_gbn	= \'11\'
						GROUP	BY close_cd';

				$sumTotData[4] = $conn->_fetch_array($sql,'cd');
			}

		}else{
			//일별소계
			$sql = 'SELECT	CONCAT(close_date,\'_\',close_cd) AS idx
					,		close_date AS dt
					,		close_cd AS cd
					,		close_cnt AS cnt
					,		close_pay AS pay
					,		close_per AS per
					FROM	care_close_day
					WHERE	org_no		= \''.$code.'\'
					AND		close_sr	= \''.$sr.'\'
					AND		LEFT(close_date,6) = \''.$year.$month.'\'
					AND		close_gbn	= \'11\'';

			$sumData = $conn->_fetch_array($sql,'idx');
		}

		//서비스
		$sql = 'SELECT	DISTINCT
						care.suga_cd AS cd
				,		suga.cd1 AS mst_cd
				,		suga.cd2 AS pro_cd
				,		suga.cd3 AS svc_cd
				,		suga.nm1 AS mst_nm
				,		suga.nm2 AS pro_nm
				,		suga.nm3 AS svc_nm
				FROM	care_suga AS care
				INNER	JOIN suga_care AS suga
						ON CONCAT(suga.cd1,suga.cd2,suga.cd3) = care.suga_cd
				WHERE	org_no = \''.$code.'\'';

		if ($type == '51' || $type == '51_MONTH'){
			$sql .= '	AND	DATE_FORMAT(care.from_dt,\'%Y%m\') <= \''.$year.$month.'\'
						AND	DATE_FORMAT(care.to_dt,\'%Y%m\') >= \''.$year.$month.'\'';
		}else{
			$sql .= '	AND	DATE_FORMAT(care.from_dt,\'%Y%m\') <= \''.$year.$from.'\'';
		}

		$sql .= '
				ORDER	BY cd';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if (!$arr[$row['mst_cd']]){
				 $arr[$row['mst_cd']] = Array('cd'=>$row['mst_cd'],'nm'=>$row['mst_nm'],'cnt'=>0);
			}

			if (SubStr($type,0,9) == '51_MIDDLE'){
				if (!$arr[$row['mst_cd']][$row['pro_cd']]){
					$cntTot = 0;

					for($j=1; $j<=$quarter; $j++){
						if (Is_Array($sumTotData[$j])){
							foreach($sumTotData[$j] as $tmpCd => $tmpArr){
								if (SubStr($tmpCd,0,3) == $row['mst_cd'].$row['pro_cd']){
									if ($unit[$tmpCd]['gbn'] == '1'){
										$cntTot += $tmpArr['per'];
									}else{
										$cntTot += $tmpArr['cnt'];
									}
								}
							}
						}
					}

					$arr[$row['mst_cd']]['cnt'] ++;
					$arr[$row['mst_cd']][$row['pro_cd']] = Array(
							'suga'=>$row['mst_cd'].$row['pro_cd']
						,	'cd'=>$row['pro_cd']
						,	'nm'=>$row['pro_nm']
						,	'target'=>0
						,	'cnt'=>0
						,	'pay'=>0
						,	'cntTot'=>$cntTot
					);
				}

				$arr[$row['mst_cd']][$row['pro_cd']]['target'] += $target[$row['cd']]['cnt'];

				if ($unit[$row['cd']]['gbn'] == '1'){
					//명
					if ($sumData[$row['cd']]['per'] > 0){
						$arr[$row['mst_cd']][$row['pro_cd']]['cnt'] += $sumData[$row['cd']]['per'];
					}
				}else{
					//회
					$arr[$row['mst_cd']][$row['pro_cd']]['cnt'] += $sumData[$row['cd']]['cnt'];
				}
			}else{
				if (!$arr[$row['mst_cd']][$row['pro_cd']]){
					$arr[$row['mst_cd']][$row['pro_cd']] = Array('cd'=>$row['pro_cd'],'nm'=>$row['pro_nm'],'cnt'=>0);
				}

				//$gbn = $target[$row['cd']]['gbn'];
				$gbn = $unit[$row['cd']]['gbn'];
				$targetCnt = $target[$row['cd']]['cnt'];

				if ($gbn == '1'){
					//명수
					if ($type == '51'){
						#if ($sumData[$row['cd']]['cnt'] > 0){
						#	$cnt = 1;
						#}else{
						#	$cnt = 0;
						#}
						$cnt = $sumData[$row['cd']]['per'];
					}else if (SubStr($type,0,10) == '51_QUARTER' ||
							SubStr($type,0,9) == '51_MIDDLE'){
						if ($prtGbn == '1'){
							$cnt = $sumData[$row['cd']]['per'];
							$cntTot = $sumTotData[$row['cd']]['per'];
						}else{
							$cnt = '';
							for($j=IntVal($from); $j<=IntVal($to); $j++){
								$idx = $year.($j < 10 ? '0' : '').$j.'_'.$row['cd'];

								if ($sumData[$idx]['cnt'] > 0){
									$cnt .= '/1';
								}else{
									$cnt .= '/';
								}
							}

							$cntTot = '/'.$sumTotData[1][$row['cd']]['per'].'/'.$sumTotData[2][$row['cd']]['per'].'/'.$sumTotData[3][$row['cd']]['per'].'/'.$sumTotData[4][$row['cd']]['per'];
						}
					}else{
						$cnt = '';
						for($j=1; $j<=$lastDay; $j++){
							$idx = $year.$month.($j < 10 ? '0' : '').$j.'_'.$row['cd'];

							if ($sumData[$idx]['cnt'] > 0){
								$cnt .= '/1';
							}else{
								$cnt .= '/';
							}
						}
					}
				}else{
					//횟수
					if ($type == '51'){
						$cnt = $sumData[$row['cd']]['cnt'];
					}else if (SubStr($type,0,10) == '51_QUARTER' ||
							SubStr($type,0,9) == '51_MIDDLE'){
						if ($prtGbn == '1'){
							$cnt = $sumData[$row['cd']]['cnt'];
							$cntTot = $sumTotData[$row['cd']]['cnt'];
						}else{
							$cnt = '';
							for($j=IntVal($from); $j<=IntVal($to); $j++){
								$idx = $year.($j < 10 ? '0' : '').$j.'_'.$row['cd'];
								$cnt .= '/'.$sumData[$idx]['cnt'];
							}

							$cntTot = '/'.$sumTotData[1][$row['cd']]['cnt'].'/'.$sumTotData[2][$row['cd']]['cnt'].'/'.$sumTotData[3][$row['cd']]['cnt'].'/'.$sumTotData[4][$row['cd']]['cnt'];
						}
					}else{
						$cnt = '';
						for($j=1; $j<=$lastDay; $j++){
							$idx = $year.$month.($j < 10 ? '0' : '').$j.'_'.$row['cd'];
							$cnt .= '/'.$sumData[$idx]['cnt'];
						}
					}
				}

				$pay = $sumData[$row['cd']]['pay'];

				if ($tmpCd != $row['mst_cd'].'_'.$row['pro_cd']){
					$tmpCd = $row['mst_cd'].'_'.$row['pro_cd'];
					$arr[$row['mst_cd']]['cnt'] ++;
				}

				$arr[$row['mst_cd']]['cnt'] ++;
				$arr[$row['mst_cd']][$row['pro_cd']]['cnt'] ++;
				$arr[$row['mst_cd']][$row['pro_cd']][$row['svc_cd']] = Array(
						'suga'=>$row['cd']
					,	'cd'=>$row['svc_cd']
					,	'nm'=>$row['svc_nm']
					,	'target'=>$targetCnt
					,	'gbn'=>$gbn
					,	'cnt'=>$cnt
					,	'pay'=>$pay
					,	'cntTot'=>$cntTot
				);
			}
		}

		$conn->row_free();

		if (Is_Array($arr)){
			foreach($arr as $cd1 => $mst){
				$mstCnt = $mst['cnt'];

				if (Is_Array($mst)){
					foreach($mst as $cd2 => $pro){
						if (SubStr($type,0,9) == '51_MIDDLE'){
							if (Is_Array($pro)){
								$data .= 'mstCnt='.$mstCnt;
								$data .= '&mstCd='.($mstCnt > 0 ? $mst['cd'] : '');
								$data .= '&mstNm='.($mstCnt > 0 ? $mst['nm'] : '');
								$data .= '&proCd='.$pro['cd'];
								$data .= '&proNm='.$pro['nm'];
								$data .= '&suga='.$pro['suga'];
								$data .= '&target='.$pro['target'];
								$data .= '&cnt='.$pro['cnt'];
								$data .= '&pay='.$pro['pay'];
								$data .= '&cntTot='.$pro['cntTot'];
								$data .= chr(11);

								$mstCnt = 0;
							}
						}else{
							@$proCnt = $pro['cnt'];

							if (Is_Array($pro)){
								foreach($pro as $cd3 => $svc){
									if (Is_Array($svc)){
										$data .= 'mstCnt='.$mstCnt;
										$data .= '&proCnt='.$proCnt;
										$data .= '&mstCd='.($mstCnt > 0 ? $mst['cd'] : '');
										$data .= '&proCd='.($proCnt > 0 ? $pro['cd'] : '');
										$data .= '&svcCd='.$svc['cd'];
										$data .= '&mstNm='.($mstCnt > 0 ? $mst['nm'] : '');
										$data .= '&proNm='.($proCnt > 0 ? $pro['nm'] : '');
										$data .= '&svcNm='.$svc['nm'];
										$data .= '&suga='.$svc['suga'];
										$data .= '&target='.$svc['target'];
										$data .= '&gbn='.$svc['gbn'];
										$data .= '&cnt='.$svc['cnt'];
										$data .= '&pay='.$svc['pay'];

										if (SubStr($type,0,10) == '51_QUARTER'){
											//누계
											$data .= '&cntTot='.$svc['cntTot'];
										}

										$data .= chr(11);

										$mstCnt = 0;
										$proCnt = 0;
									}
								}
							}
						}
					}
				}
			}
		}

		if ($type != '51'){
		}else{
			echo $data;
		}

	}else if ($type == '52' || $type == '52_ASS_SVC'){
		//보고서 세부사업(협회)
		$sr = $_POST['sr'];
		//지역
		$area = $_POST['area'];
		//년도
		$year = $_POST['year'];

		if ($area){
			$areaSQL = '	INNER	JOIN	b02center
									ON		b02center.b02_center = org_no
									AND		CASE WHEN IFNULL(b02center.care_area,\'\') != \'\' THEN b02center.care_area ELSE \'\' END = \''.$area.'\'';
		}else{
			$areaSQL = '	INNER	JOIN	b02center
									ON		b02center.b02_center = org_no
									AND		CASE WHEN IFNULL(b02center.care_area,\'\') != \'\' THEN b02center.care_area ELSE \'\' END != \'\'';
		}

		//수가별 단위(1:명, 2:횟수)
		$sql = 'SELECT	plan_cd AS cd
				,		SUM(plan_target) AS cnt
				FROM	care_year_plan';

		$sql .=	$areaSQL;

		$sql .= '
				WHERE	plan_year	= \''.$year.'\'
				AND		plan_sr		= \''.$sr.'\'
				GROUP	BY plan_cd';

		$target = $conn->_fetch_array($sql,'cd');

		$sql = 'SELECT	suga_cd AS cd
				,		unit_gbn AS gbn
				FROM	care_suga_unit
				WHERE	year = \''.$year.'\'';

		$unit = $conn->_fetch_array($sql,'cd');

		//내역
		$sql = 'SELECT	cd
				,		SUM(cnt_1) AS cnt_1
				,		SUM(pay_1) AS pay_1
				,		SUM(per_1) AS per_1
				,		SUM(cnt_2) AS cnt_2
				,		SUM(pay_2) AS pay_2
				,		SUM(per_2) AS per_2
				,		SUM(cnt_3) AS cnt_3
				,		SUM(pay_3) AS pay_3
				,		SUM(per_3) AS per_3
				,		SUM(cnt_4) AS cnt_4
				,		SUM(pay_4) AS pay_4
				,		SUM(per_4) AS per_4
				FROM	(
						SELECT	close_cd AS cd
						,		SUM(close_cnt) AS cnt_1
						,		SUM(close_pay) AS pay_1
						,		SUM(close_per) AS per_1
						/*,		COUNT(close_cd) AS per_1*/
						,		0 AS cnt_2,0 AS pay_2,0 AS per_2
						,		0 AS cnt_3,0 AS pay_3,0 AS per_3
						,		0 AS cnt_4,0 AS pay_4,0 AS per_4
						FROM	care_close_month';

		if (!Empty($area)){
			$sql .=	$areaSQL;
		}

		$sql .= '		WHERE	close_sr	= \''.$sr.'\'
						AND		close_yymm >= \''.$year.'01\'
						AND		close_yymm <= \''.$year.'03\'
						AND		close_gbn = \'11\'
						GROUP	BY close_cd
						UNION	ALL
						SELECT	close_cd AS cd
						,		0 AS cnt_1,0 AS pay_1,0 AS per_1
						,		SUM(close_cnt) AS cnt_2
						,		SUM(close_pay) AS pay_2
						,		SUM(close_per) AS per_2
						/*,		COUNT(close_cd) AS per_2*/
						,		0 AS cnt_3,0 AS pay_3,0 AS per_3
						,		0 AS cnt_4,0 AS pay_4,0 AS per_4
						FROM	care_close_month';

		if (!Empty($area)){
			$sql .=	$areaSQL;
		}

		$sql .= '		WHERE	close_sr	= \''.$sr.'\'
						AND		close_yymm >= \''.$year.'04\'
						AND		close_yymm <= \''.$year.'06\'
						AND		close_gbn = \'11\'
						GROUP	BY close_cd
						UNION	ALL
						SELECT	close_cd AS cd
						,		0 AS cnt_1,0 AS pay_1,0 AS per_1
						,		0 AS cnt_2,0 AS pay_2,0 AS per_2
						,		SUM(close_cnt) AS cnt_3
						,		SUM(close_pay) AS pay_3
						,		SUM(close_per) AS per_3
						/*,		COUNT(close_cd) AS per_3*/
						,		0 AS cnt_4,0 AS pay_4,0 AS per_4
						FROM	care_close_month';

		if (!Empty($area)){
			$sql .=	$areaSQL;
		}

		$sql .= '		WHERE	close_sr	= \''.$sr.'\'
						AND		close_yymm >= \''.$year.'07\'
						AND		close_yymm <= \''.$year.'09\'
						AND		close_gbn = \'11\'
						GROUP	BY close_cd
						UNION	ALL
						SELECT	close_cd AS cd
						,		0 AS cnt_1,0 AS pay_1,0 AS per_1
						,		0 AS cnt_2,0 AS pay_2,0 AS per_2
						,		0 AS cnt_3,0 AS pay_3,0 AS per_3
						,		SUM(close_cnt) AS cnt_4
						,		SUM(close_pay) AS pay_4
						,		SUM(close_per) AS per_4
						/*,		COUNT(close_cd) AS per_4*/
						FROM	care_close_month';

		if (!Empty($area)){
			$sql .=	$areaSQL;
		}

		$sql .= '		WHERE	close_sr	= \''.$sr.'\'
						AND		close_yymm >= \''.$year.'10\'
						AND		close_yymm <= \''.$year.'12\'
						AND		close_gbn = \'11\'
						GROUP	BY close_cd
						) AS t
				GROUP	BY cd';

		$sumData = $conn->_fetch_array($sql,'cd');

		//서비스
		$sql = 'SELECT	DISTINCT
						care.suga_cd AS cd
				,		suga.cd1 AS mst_cd
				,		suga.cd2 AS pro_cd
				,		suga.cd3 AS svc_cd
				,		suga.nm1 AS mst_nm
				,		suga.nm2 AS pro_nm
				,		suga.nm3 AS svc_nm
				FROM	care_suga AS care
				INNER	JOIN suga_care AS suga
						ON CONCAT(suga.cd1,suga.cd2,suga.cd3) = care.suga_cd';

		$sql .=	$areaSQL;

		$sql .= '
				WHERE	LEFT(care.from_dt,4) = \''.$year.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if (!$arr[$row['mst_cd']]){
				$arr[$row['mst_cd']] = Array('cd'=>$row['mst_cd'],'nm'=>$row['mst_nm'],'cnt'=>0);
			}

			if (!$arr[$row['mst_cd']][$row['pro_cd']]){
				$arr[$row['mst_cd']][$row['pro_cd']] = Array('cd'=>$row['pro_cd'],'nm'=>$row['pro_nm'],'cnt'=>0);
			}

			$gbn = $unit[$row['cd']]['gbn'];
			$targetCnt = $target[$row['cd']]['cnt'];

			if ($gbn == '1'){
				//명수
				$cnt = '/'.$sumData[$row['cd']]['per_1'].'/'.$sumData[$row['cd']]['per_2'].'/'.$sumData[$row['cd']]['per_3'].'/'.$sumData[$row['cd']]['per_4'];
			}else{
				$cnt = '/'.$sumData[$row['cd']]['cnt_1'].'/'.$sumData[$row['cd']]['cnt_2'].'/'.$sumData[$row['cd']]['cnt_3'].'/'.$sumData[$row['cd']]['cnt_4'];
			}

			$pay = '/'.$sumData[$row['cd']]['pay_1'].'/'.$sumData[$row['cd']]['pay_2'].'/'.$sumData[$row['cd']]['pay_3'].'/'.$sumData[$row['cd']]['pay_4'];

			if ($tmpCd != $row['mst_cd'].$row['pro_cd']){
				$tmpCd = $row['mst_cd'].$row['pro_cd'];
				$arr[$row['mst_cd']]['cnt'] ++;
			}

			$arr[$row['mst_cd']]['cnt'] ++;
			$arr[$row['mst_cd']][$row['pro_cd']]['cnt'] ++;
			$arr[$row['mst_cd']][$row['pro_cd']][$row['svc_cd']] = Array(
					'suga'=>$row['cd']
				,	'cd'=>$row['svc_cd']
				,	'nm'=>$row['svc_nm']
				,	'target'=>$targetCnt
				,	'gbn'=>$gbn
				,	'cnt'=>$cnt
				,	'pay'=>$pay
				,	'cntTot'=>0
			);
		}

		$conn->row_free();

		if (Is_Array($arr)){
			foreach($arr as $cd1 => $mst){
				$mstCnt = $mst['cnt'];

				if (Is_Array($mst)){
					foreach($mst as $cd2 => $pro){
						$proCnt = $pro['cnt'];

						if (Is_Array($pro)){
							foreach($pro as $cd3 => $svc){
								if (Is_Array($svc)){
									$data .= 'mstCnt='.$mstCnt;
									$data .= '&proCnt='.$proCnt;
									$data .= '&mstCd='.($mstCnt > 0 ? $mst['cd'] : '');
									$data .= '&proCd='.($proCnt > 0 ? $pro['cd'] : '');
									$data .= '&svcCd='.$svc['cd'];
									$data .= '&mstNm='.($mstCnt > 0 ? $mst['nm'] : '');
									$data .= '&proNm='.($proCnt > 0 ? $pro['nm'] : '');
									$data .= '&svcNm='.$svc['nm'];
									$data .= '&suga='.$svc['suga'];
									$data .= '&target='.$svc['target'];
									$data .= '&gbn='.$svc['gbn'];
									$data .= '&cnt='.$svc['cnt'];
									$data .= '&pay='.$svc['pay'];

									if (SubStr($type,0,10) == '51_QUARTER'){
										//누계
										$data .= '&cntTot='.$svc['cntTot'];
									}

									$data .= chr(11);

									$mstCnt = 0;
									$proCnt = 0;
								}
							}
						}
					}
				}
			}
		}

		if ($type == '52'){
			echo $data;
		}

	}else if ($type == '53' || $type == '53_ASS_MIDDLE'){
		//중분류 보고서
		$sr = $_POST['sr'];
		//지역
		$area = $_POST['area'];

		//년도
		$year = $_POST['year'];

		//$areaSQL = '	INNER	JOIN	b02center
		//						ON		b02center.b02_center = org_no
		//						AND		CASE WHEN IFNULL(b02center.care_area,\'\') != \'\' THEN b02center.care_area ELSE \'99\' END = \''.$area.'\'';

		if ($area){
			$areaSQL = '	INNER	JOIN	b02center
									ON		b02center.b02_center = org_no
									AND		CASE WHEN IFNULL(b02center.care_area,\'\') != \'\' THEN b02center.care_area ELSE \'\' END = \''.$area.'\'';
		}else{
			$areaSQL = '	INNER	JOIN	b02center
									ON		b02center.b02_center = org_no
									AND		CASE WHEN IFNULL(b02center.care_area,\'\') != \'\' THEN b02center.care_area ELSE \'\' END != \'\'';
		}


		if($area){
			$areaSQL3 = '	INNER	JOIN	b02center
									ON		b02center.b02_center = a.org_no
									AND		CASE WHEN IFNULL(b02center.care_area,\'\') != \'\' THEN b02center.care_area ELSE \'\' END = \''.$area.'\'';
		}else {
			$areaSQL3 =	'	INNER	JOIN	b02center
									ON		b02center.b02_center = a.org_no
									AND		CASE WHEN IFNULL(b02center.care_area,\'\') != \'\' THEN b02center.care_area ELSE \'\' END != \'\'';
		}

		//수가별 단위(1:명, 2:횟수)
		$sql = 'SELECT	SUBSTR(plan_cd,1,3) AS cd
				,		SUM(plan_target) AS cnt
				FROM	care_year_plan AS a ';
				
		if($_SESSION['userArea'] != '13'){		
			$sql .='INNER   JOIN care_suga AS b
					ON		a.org_no = b.org_no
					AND		a.plan_sr = b.suga_sr
					AND		a.plan_cd = concat(b.suga_cd,b.suga_sub)
					AND		left(b.from_dt, 4) <= \''.$year.'\'
					AND		left(b.to_dt,4) >= \''.$year.'\'';
		}
		
		$sql .=	$areaSQL3;


		$sql .= '
				WHERE	a.plan_year	= \''.$year.'\'
				AND		a.plan_sr		= \''.$sr.'\'';

		if ($code == 'HAN'){
		}else{
			$sql .= '	AND		a.org_no = \''.$code.'\'';
		}

		if ($sr == 'S'){
			$kacd = '/KN85C001/KN88C003/KN82C001/KN87C002/KN73C001/KN17C001/KN17C005/KN17C002/KN86C002/KN12C005/KN31C001/KN31C003/KN25C003';

			if (!is_numeric(StrPos($kacd,'/'.$code))){
				$sql .= ' AND LENGTH(a.plan_cd) = 7';
			}
		}

		$sql .= '
				GROUP	BY SUBSTR(a.plan_cd,1,3)';

		//if($debug) echo nl2br($sql); exit;
		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$sumData[$row['cd']]['target'] += $row['cnt']; //목표
		}

		$conn->row_free();

		$sql = 'SELECT	suga_cd AS cd
				,		unit_gbn AS gbn
				FROM	care_suga_unit
				WHERE	year = \''.$year.'\'';

		$unit = $conn->_fetch_array($sql,'cd');

		//내역
		$sql = 'SELECT	cd
				,		SUM(cnt_1) AS cnt_1
				,		SUM(pay_1) AS pay_1
				,		SUM(per_1) AS per_1
				,		SUM(cnt_2) AS cnt_2
				,		SUM(pay_2) AS pay_2
				,		SUM(per_2) AS per_2
				,		SUM(cnt_3) AS cnt_3
				,		SUM(pay_3) AS pay_3
				,		SUM(per_3) AS per_3
				,		SUM(cnt_4) AS cnt_4
				,		SUM(pay_4) AS pay_4
				,		SUM(per_4) AS per_4
				FROM	(
						SELECT	close_cd AS cd
						,		SUM(close_cnt) AS cnt_1
						,		SUM(close_pay) AS pay_1
						,		SUM(close_per) AS per_1
						/*,		COUNT(close_cd) AS per_1*/
						,		0 AS cnt_2,0 AS pay_2,0 AS per_2
						,		0 AS cnt_3,0 AS pay_3,0 AS per_3
						,		0 AS cnt_4,0 AS pay_4,0 AS per_4
						FROM	care_close_month';

		$sql .=	$areaSQL;

		$sql .= '		WHERE	close_yymm >= \''.$year.'01\'
						AND		close_yymm <= \''.$year.'03\'
						AND		close_sr	= \''.$sr.'\'';

		if ($code == 'HAN'){
		}else{
			$sql .= '	AND		org_no		= \''.$code.'\'';
		}

		$sql .= '		AND		close_gbn = \'11\'
						GROUP	BY close_cd
						UNION	ALL
						SELECT	close_cd AS cd
						,		0 AS cnt_1,0 AS pay_1,0 AS per_1
						,		SUM(close_cnt) AS cnt_2
						,		SUM(close_pay) AS pay_2
						,		SUM(close_per) AS per_2
						/*,		COUNT(close_cd) AS per_2*/
						,		0 AS cnt_3,0 AS pay_3,0 AS per_3
						,		0 AS cnt_4,0 AS pay_4,0 AS per_4
						FROM	care_close_month';

		if (!Empty($area)){
			$sql .=	$areaSQL;
		}

		$sql .= '		WHERE	close_yymm >= \''.$year.'04\'
						AND		close_yymm <= \''.$year.'06\'
						AND		close_sr	= \''.$sr.'\'';

		if ($code == 'HAN'){
		}else{
			$sql .= '	AND		org_no		= \''.$code.'\'';
		}

		$sql .= '		AND		close_gbn = \'11\'
						GROUP	BY close_cd
						UNION	ALL
						SELECT	close_cd AS cd
						,		0 AS cnt_1,0 AS pay_1,0 AS per_1
						,		0 AS cnt_2,0 AS pay_2,0 AS per_2
						,		SUM(close_cnt) AS cnt_3
						,		SUM(close_pay) AS pay_3
						,		SUM(close_per) AS per_3
						/*,		COUNT(close_cd) AS per_3*/
						,		0 AS cnt_4,0 AS pay_4,0 AS per_4
						FROM	care_close_month';

		$sql .=	$areaSQL;

		$sql .= '		WHERE	close_yymm >= \''.$year.'07\'
						AND		close_yymm <= \''.$year.'09\'
						AND		close_sr	= \''.$sr.'\'';

		if ($code == 'HAN'){
		}else{
			$sql .= '	AND		org_no		= \''.$code.'\'';
		}

		$sql .= '		AND		close_gbn = \'11\'
						GROUP	BY close_cd
						UNION	ALL
						SELECT	close_cd AS cd
						,		0 AS cnt_1,0 AS pay_1,0 AS per_1
						,		0 AS cnt_2,0 AS pay_2,0 AS per_2
						,		0 AS cnt_3,0 AS pay_3,0 AS per_3
						,		SUM(close_cnt) AS cnt_4
						,		SUM(close_pay) AS pay_4
						,		SUM(close_per) AS per_4
						/*,		COUNT(close_cd) AS per_4*/
						FROM	care_close_month';

		$sql .=	$areaSQL;

		$sql .= '		WHERE	close_yymm >= \''.$year.'10\'
						AND		close_yymm <= \''.$year.'12\'
						AND		close_sr	= \''.$sr.'\'';

		if ($code == 'HAN'){
		}else{
			$sql .= '	AND		org_no		= \''.$code.'\'';
		}

		$sql .= '		AND		close_gbn = \'11\'
						GROUP	BY close_cd
						) AS t
				GROUP	BY cd
				ORDER	BY cd';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$cd = SubStr($row['cd'],0,3);

			if (!$sumData[$cd]['cd']){
				 $sumData[$cd]['cd'] = $cd;
				 $sumData[$cd]['gbn'] = $unit[$row['cd']]['gbn']; //단위구분
			}

			//$sumData[$cd]['target'] += $target[$row['cd']]['cnt']; //목표

			for($j=1; $j<=4; $j++){
				//분기
				if ($unit[$row['cd']]['gbn'] == '1'){
					$sumData[$cd]['cnt_'.$j] += $row['per_'.$j]; //명수
				}else{
					$sumData[$cd]['cnt_'.$j] += $row['cnt_'.$j]; //횟수
				}
				$sumData[$cd]['pay_'.$j] += $row['pay_'.$j]; //금액
			}
		}

		$conn->row_free();

		//서비스
		$sql = 'SELECT	DISTINCT
						SUBSTR(care.suga_cd,1,3) AS cd
				,		suga.cd1 AS mst_cd
				,		suga.cd2 AS pro_cd
				,		suga.nm1 AS mst_nm
				,		suga.nm2 AS pro_nm
				FROM	care_suga AS care
				INNER	JOIN suga_care AS suga
						ON CONCAT(suga.cd1,suga.cd2) = SUBSTR(care.suga_cd,1,3)';

		if (!Empty($area)){
			$sql .=	$areaSQL;
		}

		$sql .= '
				WHERE	LEFT(care.from_dt,4) <= \''.$year.'\'
				AND		LEFT(care.to_dt,4) >= \''.$year.'\'';

		if ($code == 'HAN'){
		}else{
			$sql .= '
				AND		care.org_no = \''.$code.'\'';
		}

		$sql .= '
				AND		care.suga_sr = \''.$sr.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if (!$arr[$row['mst_cd']]){
				$arr[$row['mst_cd']] = Array('cd'=>$row['mst_cd'],'nm'=>$row['mst_nm'],'cnt'=>0);
			}

			$cnt = '/'.$sumData[$row['cd']]['cnt_1'].'/'.$sumData[$row['cd']]['cnt_2'].'/'.$sumData[$row['cd']]['cnt_3'].'/'.$sumData[$row['cd']]['cnt_4'];
			$pay = '/'.$sumData[$row['cd']]['pay_1'].'/'.$sumData[$row['cd']]['pay_2'].'/'.$sumData[$row['cd']]['pay_3'].'/'.$sumData[$row['cd']]['pay_4'];

			$arr[$row['mst_cd']]['cnt'] ++;
			$arr[$row['mst_cd']][$row['pro_cd']] = Array(
					'suga'=>$row['cd']
				,	'cd'=>$row['pro_cd']
				,	'nm'=>$row['pro_nm']
				,	'target'=>$sumData[$row['cd']]['target']
				,	'cnt'=>$cnt
				,	'pay'=>$pay
			);
		}

		$conn->row_free();

		if (Is_Array($arr)){
			foreach($arr as $cd1 => $mst){
				$mstCnt = $mst['cnt'];

				if (Is_Array($mst)){
					foreach($mst as $cd2 => $pro){
						if (Is_Array($pro)){
							$data .= 'mstCnt='.$mstCnt;
							$data .= '&mstCd='.($mstCnt > 0 ? $mst['cd'] : '');
							$data .= '&mstNm='.($mstCnt > 0 ? $mst['nm'] : '');
							$data .= '&proCd='.$pro['cd'];
							$data .= '&proNm='.$pro['nm'];
							$data .= '&suga='.$pro['suga'];
							$data .= '&target='.$pro['target'];
							$data .= '&gbn='.$pro['gbn'];
							$data .= '&cnt='.$pro['cnt'];
							$data .= '&pay='.$pro['pay'];
							$data .= chr(11);

							$mstCnt = 0;
						}
					}
				}
			}
		}

		if ($type == '53'){
			echo $data;
		}

	}else if ($type == '54'){
		//기관별 서비스
		$sr   = $_POST['sr'];
		$year = $_POST['year'];
		$area = $_POST['area'];

		//실적
		$sql = 'SELECT	care.close_cd AS cd
				,		care.close_yymm AS yymm
				,		COUNT(care.org_no) AS cnt
				FROM	care_close_month AS care';

		if ($area){
			$sql .= '
				INNER	JOIN b02center AS b02
						ON	b02_center		= care.org_no
						AND	b02_kind		= \'0\'
						AND	b02.care_area	= \''.$area.'\'';
		}

		$sql .= '
				WHERE	LEFT(care.close_yymm,4)	= \''.$year.'\'
				AND		care.close_sr			= \''.$sr.'\'
				AND		care.close_gbn			= \'11\'
				GROUP	BY care.close_cd, care.close_yymm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$cd  = $row['cd'];
			$mon = IntVal(SubStr($row['yymm'],4,2));

			$conf[$cd][$mon]['cnt'] = $row['cnt'];
		}

		$conn->row_free();

		if (Is_Array($conf)){
			$sql = 'SELECT	DISTINCT
							cd1 AS mst_cd
					,		cd2 AS pro_cd
					,		cd3 AS svc_cd
					,		nm1 AS mst_nm
					,		nm2 AS pro_nm
					,		nm3 AS svc_nm
					FROM	suga_care
					WHERE	LEFT(from_dt,4)	<= \''.$year.'\'
					AND		LEFT(to_dt,4)	>= \''.$year.'\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);
				$cd  = $row['mst_cd'].$row['pro_cd'].$row['svc_cd'];

				if (Is_Array($conf[$cd])){
					if (!$arr[$row['mst_cd']]){
						 $arr[$row['mst_cd']] = Array('cd'=>$row['mst_cd'],'nm'=>$row['mst_nm'],'cnt'=>0);
					}

					if (!$arr[$row['mst_cd']][$row['pro_cd']]){
						 $arr[$row['mst_cd']][$row['pro_cd']] = Array('cd'=>$row['pro_cd'],'nm'=>$row['pro_nm'],'cnt'=>0);
					}

					if ($tmpCd != $row['mst_cd'].$row['pro_cd']){
						$tmpCd = $row['mst_cd'].$row['pro_cd'];
						$arr[$row['mst_cd']]['cnt'] ++;
					}

					$arr[$row['mst_cd']]['cnt'] ++;
					$arr[$row['mst_cd']][$row['pro_cd']]['cnt'] ++;
					$arr[$row['mst_cd']][$row['pro_cd']][$row['svc_cd']] = Array(
							'suga'=>$row['mst_cd'].$row['pro_cd'].$row['svc_cd']
						,	'cd'=>$row['svc_cd']
						,	'nm'=>$row['svc_nm']
						,	'tot'=>0
						,	'1'=>0
						,	'2'=>0
						,	'3'=>0
						,	'4'=>0
						,	'5'=>0
						,	'6'=>0
						,	'7'=>0
						,	'8'=>0
						,	'9'=>0
						,	'10'=>0
						,	'11'=>0
						,	'12'=>0
					);

					foreach($conf[$cd] as $mon => $tmp){
						$arr[$row['mst_cd']][$row['pro_cd']][$row['svc_cd']][$mon] = $tmp['cnt'];
					}
				}
			}

			$conn->row_free();
		}

		if (Is_Array($arr)){
			foreach($arr as $cd1 => $mst){
				$mstCnt = $mst['cnt'];

				if (Is_Array($mst)){
					foreach($mst as $cd2 => $pro){
						$proCnt = $pro['cnt'];

						if (Is_Array($pro)){
							foreach($pro as $cd3 => $svc){
								if (Is_Array($svc)){
									$data .= 'mstCnt='.$mstCnt;
									$data .= '&proCnt='.$proCnt;
									$data .= '&mstCd='.($mstCnt > 0 ? $mst['cd'] : '');
									$data .= '&proCd='.($proCnt > 0 ? $pro['cd'] : '');
									$data .= '&svcCd='.$svc['cd'];
									$data .= '&mstNm='.($mstCnt > 0 ? $mst['nm'] : '');
									$data .= '&proNm='.($proCnt > 0 ? $pro['nm'] : '');
									$data .= '&svcNm='.$svc['nm'];
									$data .= '&suga='.$svc['suga'];

									$tot = 0;

									for($i=1; $i<=12; $i++){
										$data .= '&'.$i.'='.($svc[$i] > 0 ? $svc[$i] : '');
										$tot += $svc[$i];
									}

									$data .= '&tot='.($tot > 0 ? $tot : '');

									$data .= chr(11);

									$mstCnt = 0;
									$proCnt = 0;
								}
							}
						}
					}
				}
			}
		}

		echo $data;

	}else if ($type == '54_POP'){
		//기관별 보고서 상세
		$sr		= $_POST['sr'];
		$sugaCd	= $_POST['sugaCd'];
		$year	= $_POST['year'];
		$month	= $_POST['month'];
		$area	= $_POST['area'];

		$mstCd	= SubStr($sugaCd,0,1);
		$proCd	= SubStr($sugaCd,1,2);
		$svcCd	= SubStr($sugaCd,3,2);

		$sql = 'SELECT	cd1 AS mst_cd
				,		nm1 AS mst_nm
				,		cd2 AS pro_cd
				,		nm2 AS pro_nm
				,		cd3 AS svc_cd
				,		nm3 AS svc_nm
				FROM	suga_care
				WHERE	cd1 = \''.$mstCd.'\'';

		if ($proCd){
			$sql .= '
				AND		cd2 = \''.$proCd.'\'';
		}

		if ($svcCd){
			$sql .= '
				AND		cd3 = \''.$svcCd.'\'';
		}

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$key = $row['mst_cd'].$row['pro_cd'].$row['svc_cd'];

			$arrSuga[$key] = $row;
		}

		$conn->row_free();

		$sql = 'SELECT	DISTINCT
						care.close_yymm AS yymm
				,		care.org_no AS org_no
				,		mst.m00_store_nm AS org_nm
				,		care.close_cd AS cd
				,		care.close_cnt AS cnt
				,		care.close_pay AS pay
				,		IFNULL(branch.care_area,\'99\') AS area_cd
				,		area.area_nm
				FROM	care_close_month AS care
				INNER	JOIN m00center AS mst
						ON mst.m00_mcode = care.org_no
				INNER	JOIN b02center AS branch
						ON branch.b02_center = care.org_no
				LEFT	JOIN care_area AS area
						ON area.area_cd = CASE WHEN IFNULL(branch.care_area,\'\') != \'\' THEN branch.care_area ELSE \'99\' END
				WHERE	care.close_gbn	= \'11\'
				AND		care.close_sr	= \''.$sr.'\'
				AND		LEFT(care.close_cd,'.StrLen($sugaCd).')	= \''.$sugaCd.'\'';

		if ($month == 'A'){
			$sql .= '
				AND		LEFT(care.close_yymm,4)	= \''.$year.'\'';
		}else{
			$sql .= '
				AND		care.close_yymm	= \''.$year.$month.'\'';
		}

		if ($area){
			$sql .= '
				AND		branch.care_area = \''.$area.'\'';
		}

		if (StrLen($sugaCd) == 1){
			if ($month == 'A'){
				$sql .= '
					ORDER	BY cd,yymm,area_cd,org_nm';
			}else{
				$sql .= '
					ORDER	BY cd,area_cd,org_nm';
			}
		}else if (StrLen($sugaCd) < 5 && $month == 'A'){
			$sql .= '
				ORDER	BY cd,yymm,area_cd,org_nm';
		}else{
			$sql .= '
				ORDER	BY yymm,area_cd,org_nm';
		}

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$mon = IntVal(SubStr($row['yymm'],4));
			$cd  = $row['cd'];

			if (StrLen($sugaCd) < 5 && $month == 'A'){
				$org = $cd.'_'.$row['org_no'];
			}else if (StrLen($sugaCd) < 5){
				$org = $cd.'_'.$row['org_no'];
			}else{
				$org = $row['org_no'];
			}

			$key = $mon.'_'.$org;

			if (StrLen($sugaCd) == 1){
				if ($tmp != $cd.'_'.$mon.'_'.$row['org_no']){
					$tmp  = $cd.'_'.$mon.'_'.$row['org_no'];
					$tmpKey = $key;
				}
			}else if (StrLen($sugaCd) < 5 && $month == 'A'){
				if ($tmp != $cd.'_'.$mon.'_'.$row['org_no']){
					$tmp  = $cd.'_'.$mon.'_'.$row['org_no'];
					$tmpKey = $key;
				}
			}else if (StrLen($sugaCd) < 5){
				if ($tmp != $cd){
					$tmp  = $cd;
					$tmpKey = $key;
				}
			}else{
				if ($tmp != $mon){
					$tmp  = $mon;
					$tmpKey = $key;
				}
			}

			if (!$arr[$key]){
				$arr[$key] = Array(
					'area'	=>$row['area_nm']
				,	'cd'	=>$row['cd']
				,	'nm'	=>$row['org_nm']
				,	'cnt'	=>$row['cnt']
				,	'pay'	=>$row['pay']
				,	'month'	=>$mon
				);
			}

			$arr[$tmpKey]['rows'] ++;
			$arr[$tmpKey][SubStr($cd,0,3)] ++;
			$arr[$tmpKey][$cd] ++;
			$arr[$tmpKey][$cd.'_'.$mon] ++;
		}

		$conn->row_free();

		foreach($arr as $key => $tmp){
			$data .= 'area='.$tmp['area'];
			$data .= '&nm='.$tmp['nm'];
			$data .= '&cnt='.$tmp['cnt'];
			$data .= '&pay='.$tmp['pay'];

			if ($month == 'A'){
				$data .= '&month='.$tmp['month'];
			}

			if (StrLen($sugaCd) < 5){
				$data .= '&sugaCd='.$tmp['cd'];
				$data .= '&svcNm='.$arrSuga[$tmp['cd']]['svc_nm'];
			}

			if (StrLen($sugaCd) == 1){
				$data .= '&proNm='.$arrSuga[$tmp['cd']]['pro_nm'];
			}

			if (StrLen($sugaCd) == 1){
				$data .= '&rowsP='.$tmp[SubStr($tmp['cd'],0,3)];
				$data .= '&rowsS='.$tmp[$tmp['cd']];

				if ($month == 'A'){
					$data .= '&rowsM='.$tmp[$tmp['cd'].'_'.$tmp['month']];
				}
			}else if ($month == 'A' && StrLen($sugaCd) < 5){
				$data .= '&rowsS='.$tmp[$tmp['cd']];
				$data .= '&rowsM='.$tmp[$tmp['cd'].'_'.$tmp['month']];
			}else{
				$data .= '&rows='.$tmp['rows'];
			}

			$data .= chr(11);

			Unset($tmp);
		}

		//Unset($arr);
		//Unset($arrSuga);

		echo $data;

	}else if ($type == '55' || $type == '55_AREA'){
		//보고서 지역별 기관
		$sr		= $_POST['sr'];
		$year	= $_POST['year'];

		//통계
		$sql = 'SELECT	area_cd
				,		area_nm
				,		yymm
				,		COUNT(org_no) AS cnt
				FROM	(
						SELECT	DISTINCT
								IFNULL(b02.care_area,\'99\') AS area_cd
						,		area.area_nm
						,		care.close_yymm AS yymm
						,		care.org_no
						FROM	care_close_month AS care
						INNER	JOIN	b02center AS b02
								ON		b02_center = care.org_no
								AND		DATE_FORMAT(b02.from_dt,\'%Y%m\') <= care.close_yymm
								AND		DATE_FORMAT(b02.to_dt,	\'%Y%m\') >= care.close_yymm
						INNER	JOIN	care_area AS area
								ON		area.area_cd	= IFNULL(b02.care_area,\'\')
								AND		area.show_flag	= \'H\'
						WHERE	LEFT(care.close_yymm,4) = \''.$year.'\'
						AND		care.close_sr			= \''.$sr.'\'
						AND		care.close_gbn			= \'11\'
						) AS t
				GROUP	BY area_cd,area_nm,yymm
				ORDER	BY area_cd';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$mon = IntVal(SubStr($row['yymm'],4));
			$cd  = $row['area_cd'];

			if (!$arr[$cd]){
				 $arr[$cd] = Array(
					 'areaCd'=>$row['area_cd']
					,'areaNm'=>$row['area_nm']
					,'tot'=>0
					,'1'=>0
					,'2'=>0
					,'3'=>0
					,'4'=>0
					,'5'=>0
					,'6'=>0
					,'7'=>0
					,'8'=>0
					,'9'=>0
					,'10'=>0
					,'11'=>0
					,'12'=>0
				 );
			}

			$arr[$cd]['tot'] += $row['cnt'];
			$arr[$cd][$mon]  += $row['cnt'];
		}

		$conn->row_free();

		Unset($row);

		if (Is_Array($arr)){
			foreach($arr as $cd => $row){
				$data .= 'areaCd='.$row['areaCd'];
				$data .= '&areaNm='.$row['areaNm'];
				$data .= '&tot='.$row['tot'];

				for($i=1; $i<=12; $i++){
					$data .= '&'.$i.'='.$row[$i];
				}
				$data .= chr(11);
			}
		}

		Unset($row);
		Unset($arr);

		if ($type == '55'){
			echo $data;
		}



	}else if ($type == '43'){
		//실적마감 결과
		$sr   = $_POST['sr'];
		$year = $_POST['year'];

		//단위
		$sql = 'SELECT	suga_cd AS cd
				,		unit_gbn AS gbn
				FROM	care_suga_unit
				WHERE	year = \''.$year.'\'';

		$unit = $conn->_fetch_array($sql,'cd');

		$sql = 'SELECT	close_yymm AS yymm
				,		close_cd AS cd
				,		close_cnt AS cnt
				,		close_per AS per
				FROM	care_close_month
				WHERE	org_no		= \''.$code.'\'
				AND		close_sr	= \''.$sr.'\'
				AND		close_gbn	= \'11\'
				AND		LEFT(close_yymm,4) = \''.$year.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$m = IntVal(SubStr($row['yymm'],4,2));

			if ($unit[$row['cd']]['gbn'] == '1'){
				$per[$m] += $row['per'];
			}else{
				$cnt[$m] += $row['cnt'];
			}
		}

		$conn->row_free();

		for($i=1; $i<=12; $i++){
			if ($data) $data .= '&';

			$data .= 'Cnt'.$i.'='.$cnt[$i];
			$data .= '&Per'.$i.'='.$per[$i];
		}

		echo $data;

	}else if ($type == '71'){
		//거래처 관리
		$sql = 'SELECT	*
				FROM	care_cust
				WHERE	org_no	 = \''.$code.'\'
				AND		del_flag = \'N\'
				ORDER	BY cust_nm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$data .= 'cd='.$row['cust_cd'];
			$data .= '&nm='.$row['cust_nm'];
			$data .= '&gbn='.$row['cust_gbn'];
			$data .= '&kindS='.$row['supporter_yn'];
			$data .= '&kindW='.$row['worker_yn'];
			$data .= '&manager='.$row['manager'];
			$data .= '&phone='.$myF->phoneStyle($row['phone'],'.');
			$data .= '&fax='.$myF->phoneStyle($row['fax'],'.');
			$data .= '&addr='.$row['addr'];
			$data .= '&per='.$row['per_nm'];
			$data .= '&support='.$row['support_yn'];
			$data .= '&resource='.$row['resource_yn'];
			$data .= chr(11);
		}

		$conn->row_free();

		echo $data;

	}else if ($type == '71_NEW'){
		//신규 거래처
		$sql = 'SELECT	IFNULL(MAX(cust_cd),0)+1
				FROM	care_cust
				WHERE	org_no = \''.$code.'\'';

		$cd = $conn->get_data($sql);
		$cd = '000'.$cd;
		$cd = SubStr($cd,StrLen($cd)-4,StrLen($cd));

		echo $cd;


	}else if ($type == '71_FIND'){
		//거래처 조회
		$cd = $_POST['cd'];

		$sql = 'SELECT	*
				FROM	care_cust
				WHERE	org_no	= \''.$code.'\'
				AND		cust_cd	= \''.$cd.'\'
				AND		del_flag= \'N\'';

		$row = $conn->get_array($sql);

		$data .= 'nm='.$row['cust_nm'];
		$data .= '&gbn='.$row['cust_gbn'];
		$data .= '&kindS='.$row['supporter_yn'];
		$data .= '&kindW='.$row['worker_yn'];
		$data .= '&date='.$row['reg_dt'];
		$data .= '&bizno='.$row['biz_no'];
		$data .= '&manager='.$row['manager'];
		$data .= '&stat='.$row['status'];
		$data .= '&item='.$row['item'];
		$data .= '&phone='.$row['phone'];
		$data .= '&fax='.$row['fax'];
		$data .= '&postno='.$row['postno'];
		$data .= '&addr='.$row['addr'];
		$data .= '&addrDtl='.$row['addr_dtl'];
		$data .= '&pernm='.$row['per_nm'];
		$data .= '&pertel='.$row['per_phone'];
		$data .= '&support='.$row['support_yn'];
		$data .= '&resource='.$row['resource_yn'];

		echo $data;

	}else if ($type == 'FIND_CLIENT'){
		/*********************************************************
		 *	고객정보 조회
		 *********************************************************/
		$jumin = $_POST['jumin'];

		if (!Is_Numeric($jumin)) $jumin = $ed->de($jumin);

		$sql = 'SELECT	code
				FROM	mst_jumin
				WHERE	org_no	= \''.$code.'\'
				AND		gbn		= \'1\'
				AND		jumin	= \''.$jumin.'\'';

		$tmp = $conn->get_data($sql);

		if ($tmp) $jumin = $tmp;

		//기존 존재여부
		$sql = 'SELECT	COUNT(*)
				FROM	m03sugupja
				WHERE	m03_ccode = \''.$code.'\'
				AND		m03_mkind = \'6\'
				AND		m03_jumin = \''.$jumin.'\'';

		$liCnt = $conn->get_data($sql);

		if ($liCnt > 0){
			$data .= 'isYn=Y';
			$data .= '&jumin='.$ed->en($jumin);

			echo $data;
			exit;
		}

		$sql = 'SELECT	DISTINCT
						m03_name AS name
				,		m03_key AS fix_no
				,		m03_tel AS phone
				,		m03_hp AS mobile
				,		m03_post_no AS postno
				,		m03_juso1 AS addr
				,		m03_juso2 AS addr_dtl
				,		m03_yboho_name AS guard_nm
				,		m03_yoyangsa4_nm AS guard_addr
				,		m03_yboho_phone AS guard_tel
				,		m03_yoyangsa1 AS mem_cd
				,		SUBSTR(m03_yoyangsa5_nm,1,1) AS marry_gbn
				,		SUBSTR(m03_yoyangsa5_nm,2,1) AS cohabit_gbn
				,		SUBSTR(m03_yoyangsa5_nm,3,2) AS edu_gbn
				,		SUBSTR(m03_yoyangsa5_nm,5,1) AS rel_gbn
				FROM	m03sugupja
				WHERE	m03_ccode = \''.$code.'\'
				AND		m03_jumin = \''.$jumin.'\'';

		$row = $conn->get_array($sql);

		$row['fix_no'] = '0000000000'.$row['fix_no'];
		$row['fix_no'] = SubStr($row['fix_no'],StrLen($row['fix_no'])-10,StrLen($row['fix_no']));

		$data .= 'isYn=N';
		$data .= '&name='		.$row['name'];
		$data .= '&phone='		.$row['phone'];
		$data .= '&mobile='		.$row['mobile'];
		$data .= '&postno='		.$row['postno'];
		$data .= '&addr='		.$row['addr'];
		$data .= '&addrDtl='	.$row['addr_dtl'];
		$data .= '&guardNm='	.$row['guard_nm'];
		$data .= '&guardAddr='	.$row['guard_addr'];
		$data .= '&guardTel='	.$row['guard_tel'];
		$data .= '&marry='		.$row['marry_gbn'];
		$data .= '&cohabit='	.$row['cohabit_gbn'];
		$data .= '&edu='		.$row['edu_gbn'];
		$data .= '&rel='		.$row['rel_gbn'];
		$data .= '&fixNo='		.$row['fix_no'];

		if ($row['mem_cd']){
			//담당조회
			$sql = 'SELECT	m02_yname AS name
					,		m02_ytel AS phone
					,		m02_ytel2 AS mobile
					FROM	m02yoyangsa
					WHERE	m02_ccode	= \''.$code.'\'
					AND		m02_mkind	= \'0\'
					AND		m02_yjumin	= \''.$row['mem_cd'].'\'';

			$row = $conn->get_array($sql);

			$data .= '&yoyNm='	.$row['name'];
			$data .= '&yoyTel='	.($row['mobile'] ? $row['mobile'] : $row['phone']);
		}

		Unset($row);

		//기관정보
		$sql = 'SELECT	app_no
				,		level
				FROM	client_his_lvl
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		svc_cd	= \'0\'
				ORDER	BY seq DESC
				LIMIT	1';

		$row = $conn->get_array($sql);

		$data .= '&appNo='	.$row['app_no'];
		$data .= '&lvl='	.$row['level'];

		Unset($row);

		$sql = 'SELECT	kind
				FROM	client_his_kind
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				ORDER	BY seq DESC
				LIMIT	1';

		$kind = $conn->get_data($sql);

		$data .= '&kind='.$kind;

		echo $data;

	}else if($type == '56'){
		//소속기관별

		$sr     = $_POST['sr'];
		$year   = $_POST['year'];
		$month	= $myF->monthStr($_POST['month']);


		$sql = 'SELECT left(t01_sugup_date, 6) as yymm
				,      t01_sugup_date
				,      t01_jumin
				,      org_no
				,      org_nm
				,      mst_cd
				,      mst_nm
				,      pro_cd
				,      pro_nm
				,      svc_cd
				,      svc_nm
				,	   suga_sub
				,      suga_nm
				,      count(suga_nm) as cnt
				FROM  (
					SELECT left(t01_sugup_date, 6) as yymm
					,      t01_sugup_date
					,      t01_jumin
					,      care_org_no as org_no
					,      care_org_nm as org_nm
					,      suga.mst_cd
					,      suga.mst_nm
					,      suga.pro_cd
					,      suga.pro_nm
					,      suga.svc_cd
					,      suga.svc_nm
					,	   suga.suga_sub
					,      suga.suga_nm
					FROM   t01iljung as iljung
					INNER  JOIN  client_his_care as care
					ON     care.org_no = iljung.t01_ccode
					AND    care.svc_cd = iljung.t01_mkind
					AND    care.jumin  = iljung.t01_jumin
					INNER  JOIN (
								SELECT	org_no, suga_sr, suga_cd, suga_sub, suga_nm, care.from_dt, care.to_dt, suga.cd1 AS mst_cd, suga.nm1 AS mst_nm, suga.cd2 AS pro_cd, suga.nm2 AS pro_nm, suga.cd3 AS svc_cd, suga.nm3 AS svc_nm
								FROM	care_suga as care
								INNER   JOIN suga_care as suga
								ON cd1 = SUBSTR(care.suga_cd,1,1)
								AND cd2 = SUBSTR(care.suga_cd,2,2)
								AND cd3 = SUBSTR(care.suga_cd,4,2)
								WHERE	org_no	= \''.$code.'\'
								AND		suga_sr	= \''.$sr.'\'
								UNION	ALL
								SELECT	\''.$code.'\' AS org_no, \''.$sr.'\' AS suga_sr, LEFT(code,5) AS suga_cd, MID(code,6) AS suga_sub, name, comm.from_dt, comm.to_dt, suga.cd1 AS mst_cd, suga.nm1 AS mst_nm, suga.cd2 AS pro_cd, suga.nm2 AS pro_nm, suga.cd3 AS svc_cd, suga.nm3 AS svc_nm
								FROM	care_suga_comm as comm
								INNER   JOIN suga_care as suga
								ON cd1 = SUBSTR(left(code,5),1,1)
								AND cd2 = SUBSTR(left(code,5),2,2)
								AND cd3 = SUBSTR(left(code,5),4,2)
								) as suga
					ON		suga.org_no		= t01_ccode
					AND		suga.suga_sr	= t01_mkind
					AND		CONCAT(suga.suga_cd,suga.suga_sub)= t01_suga_code1
					AND		REPLACE(suga.from_dt,\'-\',\'\') <= t01_sugup_date
					AND		REPLACE(suga.to_dt,\'-\',\'\')	 >= t01_sugup_date
					WHERE  t01_ccode = \''.$code.'\'
					AND    t01_mkind = \''.$sr.'\'
					AND LEFT(t01_sugup_date, 6) = \''.$year.$month.'\'
					group  by care_org_nm, mst_cd, pro_cd, svc_cd, suga_nm, t01_jumin
					) as mst
				group  by org_nm, mst_cd, pro_cd, svc_cd, suga_nm';


		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		$no = 0;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);


			if (!$data[$row['org_nm']]){
				 $no++;
				 $data[$row['org_nm']]['no'] = $no;
				 $data[$row['org_nm']]['nm'] = $row['org_nm'];
			}

			$data[$row['org_nm']]['cnt'] ++;

			if (!$data[$row['org_nm']]['sub'][$row['mst_cd']]){
				 $data[$row['org_nm']]['sub'][$row['mst_cd']]['nm'] = $row['mst_nm'];
			}

			$data[$row['org_nm']]['sub'][$row['mst_cd']]['cnt'] ++;


			if (!$data[$row['org_nm']]['sub'][$row['mst_cd']]['sub'][$row['pro_cd']]){
				 $data[$row['org_nm']]['sub'][$row['mst_cd']]['sub'][$row['pro_cd']]['nm'] = $row['pro_nm'];
			}

			$data[$row['org_nm']]['sub'][$row['mst_cd']]['sub'][$row['pro_cd']]['cnt'] ++;


			if (!$data[$row['org_nm']]['sub'][$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]){
				 $data[$row['org_nm']]['sub'][$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['nm'] = $row['svc_nm'];
			}

			$data[$row['org_nm']]['sub'][$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['cnt'] ++;


			if (!$data[$row['org_nm']]['sub'][$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['sub'][$row['suga_sub']]){
				 $data[$row['org_nm']]['sub'][$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['sub'][$row['suga_sub']]['nm'] = $row['suga_nm'];
				 $data[$row['org_nm']]['sub'][$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['sub'][$row['suga_sub']]['cnt'] = $row['cnt'];
			}



		}

		$conn->row_free();


		if ($IsExcelClass){
			if (is_array($data)){
				foreach($data as $orgCd => $org){
					if ($org['no'] % 2 == 1){
						$bgcolor = 'FFFFFF';
					}else{
						$bgcolor = 'EAEAEA';
					}
					if (!is_array($org)) continue;
					foreach($org['sub'] as $mstCd => $mst){
						if (!is_array($mst)) continue;
						foreach($mst['sub'] as $proCd => $pro){
							if (!is_array($pro)) continue;
							foreach($pro['sub'] as $svcCd => $svc){
								if (!is_array($svc)) continue;
								foreach($svc['sub'] as $subCd => $row){
									if (!is_array($row)) continue;

									$rowNo ++;
									$sheet->getRowDimension($rowNo)->setRowHeight(-1);
									$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>$row['nm'], 'H'=>'L','backcolor'=>$bgcolor) );
									$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>$row['cnt'].' 회 ', 'H'=>'R','backcolor'=>$bgcolor) );

									if ($tmpOrgCd != $orgCd){
										$tmpOrgCd  = $orgCd;
										$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'A'.($rowNo + $org['cnt'] - 1), 'val'=>str_replace("<br>", "\n", $org['no']), 'H'=>'L','backcolor'=>$bgcolor) );
										$sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'B'.($rowNo + $org['cnt'] - 1), 'val'=>str_replace("<br>", "\n", $org['nm']), 'H'=>'L','backcolor'=>$bgcolor) );
									}

									if ($tmpMstCd != $orgCd.'_'.$mstCd){
										$tmpMstCd  = $orgCd.'_'.$mstCd;
										$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'C'.($rowNo + $mst['cnt'] - 1), 'val'=>str_replace("<br>", "\n", $mst['nm']), 'H'=>'L','backcolor'=>$bgcolor) );
									}

									if ($tmpProCd != $orgCd.'_'.$mstCd.'_'.$proCd){
										$tmpProCd  = $orgCd.'_'.$mstCd.'_'.$proCd;
										$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'D'.($rowNo + $pro['cnt'] - 1), 'val'=>str_replace("<br>", "\n", $pro['nm']), 'H'=>'L','backcolor'=>$bgcolor) );
									}

									if ($tmpSvcCd != $orgCd.'_'.$mstCd.'_'.$proCd.'_'.$svcCd){
										$tmpSvcCd  = $orgCd.'_'.$mstCd.'_'.$proCd.'_'.$svcCd;
										$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'E'.($rowNo + $svc['cnt'] - 1), 'val'=>str_replace("<br>", "\n", $svc['nm']), 'H'=>'L','backcolor'=>$bgcolor) );
									}
								}
							}
						}
					}
				}
			}
		}else{

			if (!is_array($data)) exit;


			foreach($data as $orgCd => $org){
				if ($org['no'] % 2 == 1){
					$bgcolor = 'FFFFFF';
				}else{
					$bgcolor = 'EAEAEA';
				}

				?>
					<tr>
					<td style="cursor:default;" rowspan="<?=$org['cnt'];?>" style="background-color:#<?=$bgcolor;?>; vertical-align:top;"><div class="left"><?=$org['no'];?></div></td>
					<td style="cursor:default;" rowspan="<?=$org['cnt'];?>" style="background-color:#<?=$bgcolor;?>; vertical-align:top;"><div class="left"><?=$org['nm'];?></div></td><?
				foreach($org['sub'] as $mstCd => $mst){?>
					<td style="cursor:default;" rowspan="<?=$mst['cnt'];?>" style="background-color:#<?=$bgcolor;?>; vertical-align:top;"><div class="left"><?=$mst['nm'];?></div></td><?
					foreach($mst['sub'] as $proCd => $pro){?>
						<td style="cursor:default;" rowspan="<?=$pro['cnt'];?>" style="background-color:#<?=$bgcolor;?>; vertical-align:top;"><div class="left" <?=$nowrap;?>><?=$pro['nm'];?></div></td><?
						foreach($pro['sub'] as $svcCd => $svc){?>
							<td style="cursor:default;" rowspan="<?=$svc['cnt'];?>" style="background-color:#<?=$bgcolor;?>; vertical-align:top;"><div class="left <?=$nowrap;?>"><?=$svc['nm'];?></div></td>

							<?


							foreach($svc['sub'] as $subCd => $sub){ ?>

								<td style="cursor:default;" style="background-color:#<?=$bgcolor;?>;"><div class="left <?=$nowrap;?>"><?=$sub['nm'];?></div></td>
								<td style="cursor:default;" style="background-color:#<?=$bgcolor;?>;"><div class="right <?=$nowrap;?>"><?=$sub['cnt'];?> 회</div></td>
								<td class="last" style="cursor:default;" style="background-color:#<?=$bgcolor;?>;"></td>
								</tr><?
							}


						}
					}
				}
			}
		}


	}else{
		echo $type;
		exit;
	}

	include_once('../inc/_db_close.php');
?>