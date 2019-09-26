<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$mode = $_POST['mode'];

	if ($mode == '1' ||
		$mode == '11' ||
		$mode == '21' ||
		$mode == '51' ||
		$mode == '61' ||
		$mode == '62' ||
		$mode == '63' ||
		$mode == '64'){
		$page = IntVal($_POST['page']);
		$max  = IntVal($_POST['max']);

		if ($mode == '1'){
			$table = 'sms_acct';
		}else if ($mode == '11'){
			$table = 'smart_acct';
		}else if ($mode == '21'){
			$table = 'bank_center';
		}else if ($mode == '51'){
			$table = 'mobile_acct';
		}else if ($mode == '61' || $mode == '63'){
			$table = 'tax_acct';
		}else if ($mode == '62'){
			$table = 'labor_acct';
		}else if ($mode == '64'){
			$table = 'fa_acct';
		}

		$itemCount = 20;
		$pageCount = 10;

		$pageCnt = $page;

		if (Empty($pageCount)){
			$pageCount = 1;
		}

		$pageCnt = (intVal($pageCnt) - 1) * $itemCount;

		if ($page == 0){
			$sql = 'SELECT COUNT(*)
					  FROM '.$table.'
					 INNER JOIN (
						   SELECT MIN(m00_mkind)
						   ,      m00_mcode AS code
						   ,      m00_store_nm AS name
							 FROM m00center
							WHERE m00_domain = \''.$gDomain.'\'
							  AND m00_del_yn = \'N\'
							GROUP BY m00_mcode
						   ) AS mst
						ON mst.code = '.$table.'.org_no';

			if ($mode == '61'){
				$sql .= ' AND '.$table.'.acct_type = \'9\'';
			}else if ($mode == '63'){
				$sql .= ' AND '.$table.'.acct_type = \'1\'';
			}

			$maxCount = $conn->get_data($sql);

			echo $maxCount;

			$conn->close();
			exit;
		}

		$sql = 'SELECT acct.org_no AS code
				,      mst.name
				,      acct.from_dt
				,      acct.to_dt';

		if ($mode == '21'){
			$sql .= ', acct.bank_cd
					 , bank.name AS bank_nm';
		}else{
			$sql .= ', acct.acct_yn';
		}

		$sql .= ',     acct.seq
				,		m97_id AS id
				,		m97_pass AS pw
				  FROM '.$table.' AS acct';

		if ($mode == '21'){
			$sql .= '  LEFT JOIN bank
					     ON bank.code = acct.bank_cd';
		}

		$sql .= ' INNER JOIN (
					    SELECT MIN(m00_mkind)
					    ,      m00_mcode AS code
					    ,      m00_store_nm AS name
						  FROM m00center
						 WHERE m00_domain = \''.$gDomain.'\'
						   AND m00_del_yn = \'N\'
						 GROUP BY m00_mcode
					    ) AS mst
					 ON mst.code = acct.org_no
				INNER	JOIN	m97user
						ON		m97_user = mst.code';

		if ($mode == '61'){
			$sql .= ' WHERE acct.acct_type = \'9\'';
		}else if ($mode == '63'){
			$sql .= ' WHERE acct.acct_type = \'1\'';
		}

		$sql .= '
				  ORDER BY name, code, seq DESC
				  LIMIT '.$pageCnt.','.$itemCount.'';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if ($tmpCD != $row['code']){
				$tmpCD  = $row['code'];
				$editYn = 'Y';
			}else{
				$editYn = 'N';
			}

			$data .= ($pageCnt + ($i + 1)).chr(2)
				  .  $row['code'].chr(2)
				  .  $row['name'].chr(2)
				  .  $myF->dateStyle($row['from_dt'],'.').chr(2)
				  .  $row['to_dt'].chr(2);

			if ($mode == '21'){
				$data .= $row['bank_cd'].chr(2)
					  .  $row['bank_nm'].chr(2);
			}else{
				$data .= $row['acct_yn'].chr(2);
			}

			$data .= $row['seq'].chr(2)
				  .  $editYn.chr(2)
				  .  $ed->en($row['id']).chr(2)
				  .  $ed->en($row['pw']).chr(1);
		}

		$conn->row_free();

		echo $data;

	}else if ($mode == '2'){
		$year  = $_POST['year'];
		$month = $_POST['month'];
		$month = (IntVal($month) < 10 ? '0' : '').IntVal($month);

		$sql = 'SELECT sms.code
				,      mst.name
				,      sms.acct_yn
				,      sms.cnt
				,      sms.basic
				,      sms.over
				,      sms.tot
				  FROM (
				   SELECT MIN(m00_mkind) AS kind
					   ,      m00_mcode AS code
					   ,      m00_store_nm AS name
						 FROM m00center
						WHERE m00_domain = \''.$gDomain.'\'
						  AND m00_del_yn = \'N\'
						GROUP BY m00_mcode
					   ) AS mst
				 INNER JOIN (
					   SELECT org_no AS code
					   ,      acct_yn
					   ,      cnt
					   ,      basic
					   ,      over
					   ,      tot
					     FROM sms_acct_'.$year.$month.'
					   ) AS sms
					ON sms.code = mst.code
				 ORDER BY name';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $row['code'].chr(2)
				  .  $row['name'].chr(2)
				  .  $row['acct_yn'].chr(2)
				  .  $row['cnt'].chr(2)
				  .  $row['basic'].chr(2)
				  .  $row['over'].chr(2)
				  .  $row['tot'].chr(1);
		}

		$conn->row_free();

		if ($rowCount > 0){
			$conn->close();
			echo $data.chr(3).'Y';
			exit;
		}

		$cost      = 20; //문자 건당 단가
		$limitCnt  = 300; //기본문자 갯수
		$basicCost = 5000; //기본단가

		$sql = 'SELECT sms.code
				,      mst.name
				,      sms.acct_yn
				,      IFNULL(acct.cnt,0) AS cnt
				  FROM (
					   SELECT MIN(m00_mkind) AS kind
					   ,      m00_mcode AS code
					   ,      m00_store_nm AS name
						 FROM m00center
						WHERE m00_domain = \''.$gDomain.'\'
						  AND m00_del_yn = \'N\'
						GROUP BY m00_mcode
					   ) AS mst
				 INNER JOIN (
					   SELECT org_no AS code
					   ,      acct_yn
						 FROM sms_acct
						WHERE DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
						  AND DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
					   ) AS sms
					ON sms.code = mst.code
				  LEFT JOIN (
					   SELECT org_no AS code
					   ,      COUNT(org_no) AS cnt
						 FROM sms_'.$year.$month.'
						WHERE call_seq > 0
						GROUP BY org_no
					   ) AS acct
					ON acct.code = mst.code
				 ORDER BY name';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$liCnt = $row['cnt'];

			if ($row['acct_yn'] == 'Y'){
				if ($liCnt > 0){
					$liBasicPay = $basicCost;
				}else{
					$liBasicPay = 0;
				}
			}else{
				$liBasicPay = 0;
			}

			if ($liCnt > 300){
				$liOverCnt = $liCnt - $limitCnt;
				$liOverPay = $liOverCnt * $cost;
			}else{
				$liOverCnt = 0;
				$liOverPay = 0;
			}

			$liTotPay = $liBasicPay + $liOverPay;

			$data .= $row['code'].chr(2)
				  .  $row['name'].chr(2)
				  .  $row['acct_yn'].chr(2)
				  .  $liCnt.chr(2)
				  .  $liBasicPay.chr(2)
				  .  $liOverPay.chr(2)
				  .  $liTotPay.chr(1);
		}

		$conn->row_free();

		echo $data.chr(3).'N';

	}else if ($mode == '3' || $mode == '13'){
		if ($mode == '3'){
			$table = 'sms';
		}else if ($mode == '13'){
			$table = 'smart';
		}

		$conn->query('SHOW TABLES LIKE \''.$table.'_acct_%\'');
		$conn->fetch();

		$rowCount = $conn->row_count();

		$sql = '';

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			$sql .= (!Empty($sql) ? ' UNION ALL ' : '');

			$sql .= 'SELECT org_no';

			if ($mode == '3'){
				$sql .= ', tot';
			}else if ($mode == '13'){
				$sql .= ', total_amt AS tot';
			}

			$sql .= '  FROM '.$row[0];

		}

		$conn->row_free();

		$sql = 'SELECT mst.code
				,      mst.name
				,      acct.amt AS charge
				,      IFNULL(dep.amt,0) AS deposit
				,      acct.amt - IFNULL(dep.amt,0) AS unpaid
				  FROM (
					   SELECT MIN(m00_mkind) AS kind
					   ,      m00_mcode AS code
					   ,      m00_store_nm AS name
						 FROM m00center
						WHERE m00_domain = \''.$gDomain.'\'
						  AND m00_del_yn = \'N\'
						GROUP BY m00_mcode
					   ) AS mst
				 INNER JOIN (
					   SELECT org_no AS code
					   ,      SUM(tot) AS amt
						 FROM ('.$sql.') AS acct
						GROUP BY org_no
					   ) AS acct
					ON acct.code = mst.code
				  LEFT JOIN (
					   SELECT org_no AS code
					   ,      SUM(amt) AS amt
						 FROM '.$table.'_deposit
						GROUP BY org_no
					   ) AS dep
					ON dep.code = mst.code
				 WHERE acct.amt > 0
				 ORDER BY name';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $row['code'].chr(2)
				  .  $row['name'].chr(2)
				  .  $row['charge'].chr(2)
				  .  $row['deposit'].chr(2)
				  .  $row['unpaid'].chr(1);
		}

		$conn->row_free();

		echo $data;

	}else if ($mode == '3_1' || $mode == '13_1'){
		$code = $_POST['code'];

		if ($mode == '3_1'){
			$table = 'sms_deposit';
		}else if ($mode == '13_1'){
			$table = 'smart_deposit';
		}

		$sql = 'SELECT seq
				,      DATE_FORMAT(reg_dt,\'%Y-%m-%d\') AS dt
				,      amt
				,      type
				,      other
				  FROM '.$table.'
				 WHERE org_no = \''.$code.'\'
				 ORDER BY reg_dt DESC';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $myF->dateStyle($row['dt'],'.').chr(2)
				  .  $row['amt'].chr(2)
				  .  $row['type'].chr(2)
				  .  $row['other'].chr(2)
				  .  $row['seq'].chr(1);
		}

		$conn->row_free();

		echo $data;

	}else if ($mode == '12'){
		$year  = $_POST['year'];
		$month = $_POST['month'];
		$month = (IntVal($month) < 10 ? '0' : '').IntVal($month);

		$sql = 'SELECT mst.code
				,      mst.name
				,      acct.acct_yn
				,      acct.admin_cnt
				,      acct.admin_amt
				,      acct.mem_cnt
				,      acct.mem_amt
				,      acct.total_amt
				  FROM (
					   SELECT MIN(m00_mkind) AS kind
					   ,      m00_mcode AS code
					   ,      m00_store_nm AS name
						 FROM m00center
						WHERE m00_domain = \''.$gDomain.'\'
						  AND m00_del_yn = \'N\'
						GROUP BY m00_mcode
					   ) AS mst
				 INNER JOIN (
					   SELECT org_no AS code
					   ,      acct_yn
					   ,      admin_cnt
					   ,      admin_amt
					   ,      mem_cnt
					   ,      mem_amt
					   ,      total_amt
						 FROM smart_acct_'.$year.$month.'
					   ) AS acct
					ON acct.code = mst.code
				 ORDER BY name';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $row['code'].chr(2)
				  .  $row['name'].chr(2)
				  .  $row['acct_yn'].chr(2)
				  .  $row['admin_cnt'].chr(2)
				  .  $row['admin_amt'].chr(2)
				  .  $row['mem_cnt'].chr(2)
				  .  $row['mem_amt'].chr(1);
		}

		$conn->row_free();

		if ($rowCount > 0){
			$conn->close();
			echo $data.chr(3).'Y';
			exit;
		}

		$sql = 'SELECT code
				,      name
				,      acct_yn
				,      SUM(CASE WHEN gbn = \'A\' OR gbn = \'M\' THEN 1 ELSE 0 END) AS admin_cnt
				,      SUM(CASE WHEN gbn = \'A\' OR gbn = \'Y\' THEN 1 ELSE 0 END) AS mem_cnt
				  FROM (
					   SELECT mst.code
					   ,      mst.name
					   ,      acct.acct_yn
					   ,      mem.m02_jikwon_gbn AS gbn
						 FROM (
							  SELECT MIN(m00_mkind) AS kind
							  ,      m00_mcode AS code
							  ,      m00_store_nm AS name
								FROM m00center
							   WHERE m00_domain = \''.$gDomain.'\'
								 AND m00_del_yn = \'N\'
							   GROUP BY m00_mcode
							  ) AS mst
						INNER JOIN (
							  SELECT org_no AS code
							  ,      acct_yn
								FROM smart_acct
							   WHERE DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								 AND DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							  ) AS acct
						   ON acct.code = mst.code
						INNER JOIN m02yoyangsa AS mem
						   ON mem.m02_ccode       = mst.code
						  AND mem.m02_mkind       = \'0\'
						  AND mem.m02_del_yn      = \'N\'
						  AND mem.m02_jikwon_gbn != \'\'
					   ) AS t
				 GROUP BY code
				 ORDER BY name';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if ($row['acct_yn'] == 'Y'){
				$liAdminAmt = ($row['admin_cnt'] > 0 ? 20000 : 0);
				$liMemAmt   = ($row['mem_cnt'] * 2000);
			}else{
				$liAdminAmt = 0;
				$liMemAmt   = 0;
			}

			$data .= $row['code'].chr(2)
				  .  $row['name'].chr(2)
				  .  $row['acct_yn'].chr(2)
				  .  $row['admin_cnt'].chr(2)
				  .  $liAdminAmt.chr(2)
				  .  $row['mem_cnt'].chr(2)
				  .  $liMemAmt.chr(1);
		}

		$conn->row_free();

		echo $data.chr(3).'N';

	}else if ($mode == '31'){
		$page			= IntVal($_POST['page']);
		$max			= IntVal($_POST['max']);
		$findCd			= $_POST['code'];
		$findNm			= $_POST['name'];
		$findManager	= $_POST['manager'];
		$findCMS		= $_POST['cms'];
		$findBranch		= $_POST['branch'];
		$findContFrom	= $_POST['contFrom'];
		$findContTo		= $_POST['contTo'];
		$findContYn		= $_POST['contYn'];
		$findCMSYn		= $_POST['cmsYn'];
		$applyDt		= $_POST['applyDt'];

		//기관 아이디/비번
		$sql = 'SELECT m97_user AS cd
				,      m97_id AS id
				,      m97_pass AS pw
				  FROM m97user';
		$centerId = $conn->_fetch_array($sql, 'cd');

		if (!Empty($findCMS)){
			if (StrLen($findCMS) < 8){
				$liCnt = 8 - StrLen($findCMS);

				$findCMS = '';

				for($i=1; $i<=$liCnt; $i++){
					$findCMS .= '0';
				}
				$findCMS .= IntVal($_POST['cms']);
			}
		}

		$itemCount = 20;
		$pageCount = 10;

		$pageCnt = $page;

		if (Empty($pageCount)){
			$pageCount = 1;
		}

		$pageCnt = (intVal($pageCnt) - 1) * $itemCount;

		if ($page == 0){
			$sql = 'SELECT	DISTINCT
							mst.m00_mcode					AS code
					,		mst.m00_store_nm				AS name
					,		IFNULL(mst.m00_cont_date,\'\')	AS cont_dt
					,		IFNULL(mst.m00_mname,\'\')		AS rep_nm
					,		IFNULL(center.from_dt,\'\')		AS from_dt
					,		IFNULL(center.to_dt,\'\')		AS to_dt
					,		center.b02_homecare				AS homecare
					,		center.b02_voucher				AS voucher
					,		center.b02_caresvc				AS caresvc
					,		center.b02_date					AS start_dt
					,		center.care_area				AS care_area
					,		center.care_group				AS care_group
					,		IFNULL(center.cms_cd,\'\')		AS cms_cd
					,		center.hold_yn
					,		center.basic_cost
					,		center.client_cost
					,		center.client_cnt
					,		branch.b00_code		AS branch_cd
					,		branch.b00_name		AS branch_nm
					,		manager.b01_code	AS manager_cd
					,		manager.b01_name	AS manager_nm
					FROM	m00center AS mst
					INNER	JOIN	b02center AS center
							ON		center.b02_center	= mst.m00_mcode
					INNER	JOIN	b00branch AS branch
							ON		branch.b00_code		= center.b02_branch
					INNER	JOIN	b01person AS manager
							ON		manager.b01_branch	= center.b02_branch
							AND		manager.b01_code	= center.b02_person
					WHERE	m00_domain	= \''.$gDomain.'\'
					AND		m00_del_yn	= \'N\'';

			if (!Empty($findCd)){
				$sql .= '	AND	m00_mcode LIKE \''.$findCd.'%\'';
			}

			if (!Empty($findNm)){
				$sql .= '	AND	m00_store_nm LIKE \'%'.$findNm.'%\'';
			}

			if (!Empty($findManager)){
				$sql .= '	AND	m00_mname LIKE \'%'.$findManager.'%\'';
			}

			if (!Empty($findBranch)){
				$sql .= '	AND	b02_branch = \''.$findBranch.'\'';
			}

			if (!Empty($findCMS)){
				$sql .= '	AND	cms_cd >= \''.$findCMS.'\'';
			}

			if (!Empty($findContFrom) && !Empty($findContTo)){
				$sql .= '	AND	mst.m00_cont_date >= \''.$findContFrom.'\'
							AND	mst.m00_cont_date <= \''.$findContTo.'\'';
			}

			if ($findContYn == 'N'){
				$sql .= '	AND	IFNULL(mst.m00_cont_date,\'\') = \'\'';
			}

			if ($findCMSYn == 'N'){
				$sql .= '	AND	IFNULL(center.cms_cd,\'\') = \'\'';
			}

			if ($applyDt){
				$sql .= '	AND	center.to_dt <= \''.$applyDt.'\'';
			}

			$sql = 'SELECT COUNT(*)
					  FROM ('.$sql.') AS t';

			$maxCount = $conn->get_data($sql);

			echo $maxCount;

			$conn->close();
			exit;
		}

		$sql = 'SELECT DISTINCT
					   mst.m00_mcode AS code
				,      mst.m00_store_nm AS name
				,      IFNULL(mst.m00_cont_date,\'\') AS cont_dt
				,      IFNULL(mst.m00_mname,\'\') AS rep_nm
				,      IFNULL(center.from_dt,\'\') AS from_dt
				,      IFNULL(center.to_dt,\'\') AS to_dt
				,      center.b02_homecare AS homecare
				,      center.b02_voucher AS voucher
				,      center.b02_caresvc AS caresvc
				,      center.b02_date AS start_dt
				,		center.care_area
				,		IFNULL(center.care_group,\'99\') AS care_group
				,		center.care_support
				,		center.care_resource
				,      IFNULL(center.cms_cd,\'\') AS cms_cd
				,      center.hold_yn
				,      center.basic_cost
				,      center.client_cost
				,      center.client_cnt
				,      branch.b00_code AS branch_cd
				,      branch.b00_name AS branch_nm
				,      manager.b01_code AS manager_cd
				,      manager.b01_name AS manager_nm
				,      center.b02_other AS other
				  FROM m00center AS mst
				 INNER JOIN b02center AS center
					ON center.b02_center = mst.m00_mcode
				 INNER JOIN b00branch AS branch
					ON branch.b00_code = center.b02_branch
				 INNER JOIN b01person AS manager
					ON manager.b01_branch = center.b02_branch
				   AND manager.b01_code   = center.b02_person
				 WHERE m00_domain = \''.$gDomain.'\'
				   AND m00_del_yn = \'N\'';

		if (!Empty($findCd)){
			$sql .= ' AND m00_mcode LIKE \''.$findCd.'%\'';
		}

		if (!Empty($findNm)){
			$sql .= ' AND m00_store_nm LIKE \'%'.$findNm.'%\'';
		}

		if (!Empty($findManager)){
			$sql .= ' AND m00_mname LIKE \'%'.$findManager.'%\'';
		}

		if (!Empty($findBranch)){
			$sql .= ' AND b02_branch = \''.$findBranch.'\'';
		}

		if (!Empty($findCMS)){
			$sql .= ' AND cms_cd >= \''.$findCMS.'\'';
		}

		if (!Empty($findContFrom) && !Empty($findContTo)){
			$sql .= ' AND m00_cont_date >= \''.$findContFrom.'\'
					  AND m00_cont_date <= \''.$findContTo.'\'';
		}

		if ($findContYn == 'N'){
			$sql .= ' AND IFNULL(mst.m00_cont_date,\'\') = \'\'';
		}

		if ($findCMSYn == 'N'){
			$sql .= ' AND IFNULL(center.cms_cd,\'\') = \'\'';
		}

		if ($applyDt){
			$sql .= '	AND	center.to_dt <= \''.$applyDt.'\'';
		}

		if (!Empty($findCMS)){
			$sql .= ' ORDER BY cms_cd';
		}else{
			$sql .= ' ORDER BY name';
		}

		$sql .= ' LIMIT '.$pageCnt.','.$itemCount.'';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= ($pageCnt + ($i + 1)).chr(2)	//0
				  .  $row['code'].chr(2)			//1
				  .  $row['name'].chr(2)			//2
				  .  $row['from_dt'].chr(2)			//3
				  .  $row['to_dt'].chr(2)			//4
				  .  $row['cms_cd'].chr(2)			//5
				  .  $row['hold_yn'].chr(2)			//6
				  .  $row['basic_cost'].chr(2)		//7
				  .  $row['client_cost'].chr(2)		//8
				  .  $row['client_cnt'].chr(2)		//9
				  .  $row['branch_cd'].chr(2)		//10
				  .  $row['branch_nm'].chr(2)		//11
				  .  $row['manager_cd'].chr(2)		//12
				  .  $row['manager_nm'].chr(2)		//13
				  .  $row['homecare'].$row['voucher'].chr(2)	//14
				  .  $myF->dateStyle($row['start_dt']).chr(2)	//15
				  .  $myF->dateStyle($row['cont_dt']).chr(2)	//16
				  .  $row['other'].chr(2)	//17
				  .  $row['rep_nm'].chr(2)	//18
				  .  $ed->en($centerId[$row['code']]['id']).chr(2)	//19
				  .  $ed->en($centerId[$row['code']]['pw']).chr(2);	//20
			$data .= $row['caresvc'].chr(2);	//21
			$data .= $row['care_area'].chr(2);	//22
			$data .= $row['care_support'].chr(2);	//23
			$data .= $row['care_resource'].chr(2);	//24
			$data .= $row['care_group'].chr(2);	//25
			$data .= chr(1);
		}

		$conn->row_free();

		echo $data;

	}else if ($mode == '31_SVCCNT'){
		//연별 일정 카운트
		$year	= $_POST['year'];
		$code	= $_POST['code'];
		$arrCnt	= Array(
				1	=>0
			,	2	=>0
			,	3	=>0
			,	4	=>0
			,	5	=>0
			,	6	=>0
			,	7	=>0
			,	8	=>0
			,	9	=>0
			,	10	=>0
			,	11	=>0
			,	12	=>0
		);

		$sql = 'SELECT	LEFT(t01_sugup_date,6) AS yymm
				,		COUNT(DISTINCT t01_jumin) AS cnt
				FROM	t01iljung
				WHERE	t01_ccode				= \''.$code.'\'
				AND		t01_mkind				= \'0\'
				AND		t01_del_yn				= \'N\'
				AND		LEFT(t01_sugup_date,4)	= \''.$year.'\'
				GROUP	BY	LEFT(t01_sugup_date,6)';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt	= $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row	= $conn->select_row($i);
			$month	= IntVal(SubStr($row['yymm'],4,2));

			$arrCnt[$month]	= $row['cnt'];
		}

		$conn->row_free();

		for($i=0; $i<=12; $i++){
			$data	.= $arrCnt[$i].chr(2);
		}

		echo $data;

	}else if ($mode == '32'){
		$year  = $_POST['year'];
		$month = $_POST['month'];
		$month = (IntVal($month) < 10 ? '0' : '').IntVal($month);

		$sql = 'SELECT COUNT(*)
					  FROM (
					       SELECT MIN(m00_mkind) AS kind
						   ,      m00_mcode AS code
						   ,      m00_store_nm AS name
							 FROM m00center
							WHERE m00_domain = \''.$gDomain.'\'
							  AND m00_del_yn = \'N\'
							GROUP BY m00_mcode
					       ) AS mst
					 INNER JOIN (
					       SELECT org_no AS code
					         FROM center_acct_'.$year.$month.'
						   ) AS acct
						ON acct.code = mst.code';

		@$liCnt = IntVal($conn->get_data($sql));

		if ($liCnt > 0){
			$sql = 'SELECT mst.code
					,      mst.name
					,      acct.hold_yn
					,      acct.basic_amt
					,      acct.tot_amt
					,      acct.limit_cnt
					,      acct.client_cnt
					,      acct.client_cost
					,      acct.cont_yn
					  FROM (
						   SELECT MIN(m00_mkind) AS kind
						   ,      m00_mcode AS code
						   ,      m00_store_nm AS name
							 FROM m00center
							WHERE m00_domain = \''.$gDomain.'\'
							  AND m00_del_yn = \'N\'
							GROUP BY m00_mcode
						   ) AS mst
					 INNER JOIN (
						   SELECT b02_center AS code
						   ,      hold_yn
						   ,      basic_cost
						   ,      client_cnt
						   ,      client_cost
							 FROM b02center
							WHERE DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
							  AND DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
						   ) AS center
						ON center.code = mst.code
					  LEFT JOIN (
						   SELECT org_no AS code
						   ,      hold_yn
						   ,      basic_amt
						   ,      over_amt
						   ,      tot_amt
						   ,      client_cnt
						   ,      limit_cnt
						   ,      client_cost
						   ,      cont_yn
							 FROM center_acct_'.$year.$month.'
						   ) AS acct
						ON acct.code = mst.code
					 ORDER BY CASE WHEN cont_yn = \'Y\' THEN 1 ELSE 2 END, name';

			$conn->query($sql);
			$conn->fetch();

			$rowCount = $conn->row_count();

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				if ($row['over_amt'] > 0){
					$row['over_amt'] = $row['over_amt'] ($row['client_cnt'] - $row['limit_cnt']);
				}

				if ($row['tot_amt'] == 0){
					$row['client_cost'] = 0;
				}

				$data .= $row['code'].chr(2)
					  .  $row['name'].chr(2)
					  .  $row['hold_yn'].chr(2)
					  .  $row['basic_amt'].chr(2)
					  .  $row['limit_cnt'].chr(2)
					  .  $row['client_cnt'].chr(2)
					  .  $row['client_cost'].chr(2)
					  .  $row['cont_yn'].chr(1);
			}

			$conn->row_free();
			$conn->close();
			echo $data.chr(3).'Y';
			exit;
		}

		$sql = 'SELECT mst.code
				,      mst.name
				,      mst.cont_dt
				,      center.hold_yn
				,      center.basic_cost
				,      center.client_cnt
				,      center.client_cost
				,      client.cnt
				  FROM (
					   SELECT MIN(m00_mkind) AS kind
					   ,      m00_mcode AS code
					   ,      m00_store_nm AS name
					   ,      LEFT(m00_cont_date,6) AS cont_dt
						 FROM m00center
						WHERE m00_domain = \''.$gDomain.'\'
						  AND m00_del_yn = \'N\'
						GROUP BY m00_mcode
					   ) AS mst
				 INNER JOIN (
					   SELECT b02_center AS code
					   ,      hold_yn
					   ,      basic_cost
					   ,      client_cnt
					   ,      client_cost
						 FROM b02center
						WHERE DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
						  AND DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
					   ) AS center
					ON center.code = mst.code
				  LEFT JOIN (
					   SELECT code
					   ,      IFNULL(COUNT(jumin),0) AS cnt
						 FROM (
							  SELECT DISTINCT
									 org_no AS code
							  ,      jumin
								FROM client_his_svc
							   WHERE DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								 AND DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							  ) AS client
						GROUP BY code
					   ) AS client
					ON client.code = mst.code
				 ORDER BY CASE WHEN cont_dt != \'\' THEN 1 ELSE 2 END, name';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if (Empty($row['cont_dt'])){
				$row['cont_dt'] = '99991231';
				$contYn = 'N';
			}else{
				$contYn = 'Y';
			}

			if ($row['cont_dt'] > $year.$month){
				$row['basic_cost']  = 0;
				$row['client_cost'] = 0;
			}

			$data .= $row['code'].chr(2)
				  .  $row['name'].chr(2)
				  .  $row['hold_yn'].chr(2)
				  .  $row['basic_cost'].chr(2)
				  .  $row['client_cnt'].chr(2)
				  .  $row['cnt'].chr(2)
				  .  $row['client_cost'].chr(2)
				  .  $contYn.chr(1);
		}

		$conn->row_free();

		echo $data.chr(3).'N';

	}else if ($mode == '33'){
		$page = IntVal($_POST['page']);
		$max  = IntVal($_POST['max']);

		$itemCount = 20;
		$pageCount = 10;

		$pageCnt = $page;

		if (Empty($pageCount)){
			$pageCount = 1;
		}

		$pageCnt = (intVal($pageCnt) - 1) * $itemCount;

		if ($page == 0){
			$sql = 'SELECT COUNT(*)
					  FROM (
						   SELECT DISTINCT
								  MIN(m00_mkind) AS kind
						   ,      m00_mcode AS code
						   ,      m00_store_nm AS name
						   ,      m00_cont_date AS cont_dt
						   ,      m00_mname AS rep_nm
							 FROM m00center
							WHERE m00_domain = \''.$gDomain.'\'
							  AND m00_del_yn = \'N\'
							GROUP BY m00_mcode
						   ) AS mst
					 INNER JOIN (
						   SELECT org_no AS code
						   ,      edu_dt
						   ,      amt
						   ,      other
							 FROM edu_acct
						   ) AS acct
						ON acct.code = mst.code
					 ORDER BY name';

			$maxCount = $conn->get_data($sql);

			echo $maxCount;

			$conn->close();
			exit;
		}

		$sql = 'SELECT mst.code
				,      mst.name
				,      acct.edu_dt
				,      acct.amt
				,      acct.other
				  FROM (
					   SELECT DISTINCT
							  MIN(m00_mkind) AS kind
					   ,      m00_mcode AS code
					   ,      m00_store_nm AS name
					   ,      m00_cont_date AS cont_dt
					   ,      m00_mname AS rep_nm
						 FROM m00center
						WHERE m00_domain = \''.$gDomain.'\'
						  AND m00_del_yn = \'N\'
						GROUP BY m00_mcode
					   ) AS mst
				 INNER JOIN (
					   SELECT org_no AS code
					   ,      edu_dt
					   ,      amt
					   ,      other
						 FROM edu_acct
					   ) AS acct
					ON acct.code = mst.code
				 ORDER BY name
				 LIMIT '.$pageCnt.','.$itemCount.'';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= ($pageCnt + ($i + 1)).chr(2)
				  .  $row['code'].chr(2)
				  .  $row['name'].chr(2)
				  .  $myF->dateStyle($row['edu_dt'],'.').chr(2)
				  .  $row['amt'].chr(2)
				  .  $row['other'].chr(1);
		}

		$conn->row_free();

		echo $data;

	}else if ($mode == '41'){
		//입금관리
		$sql = 'SELECT code, name, center, edu, sms, smart, tot, deposit, unpaid
				  FROM (
					   SELECT mst.code
					   ,      mst.name
					   ,      amt.center
					   ,      amt.edu
					   ,      amt.sms
					   ,      amt.smart
					   ,      amt.center + amt.sms + amt.smart + amt.edu AS tot
					   ,      amt.deposit
					   ,      (amt.center + amt.sms + amt.smart + amt.edu) - amt.deposit AS unpaid
						 FROM (
							  SELECT MIN(m00_mkind) AS kind
							  ,      m00_mcode AS code
							  ,      m00_store_nm AS name
								FROM m00center
							   WHERE m00_domain = \''.$gDomain.'\'
								 AND m00_del_yn = \'N\'
							   GROUP BY m00_mcode
							  ) AS mst
						INNER JOIN (
							  SELECT code
							  ,      SUM(center_amt) AS center
							  ,      SUM(sms_amt) AS sms
							  ,      SUM(smart_amt) AS smart
							  ,      SUM(deposit_amt) AS deposit
							  ,      SUM(edu_amt) AS edu
								FROM (
									 SELECT code
									 ,      SUM(amt) AS center_amt
									 ,      0 AS sms_amt
									 ,      0 AS smart_amt
									 ,      0 AS deposit_amt
									 ,      0 AS edu_amt
									   FROM (';

		$conn->query('SHOW TABLES LIKE \'center_acct_%\'');
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			$sql .= ($i > 0 ? ' UNION ALL ' : '');
			$sql .= 'SELECT org_no AS code
					,       tot_amt AS amt
					   FROM '.$row[0];
		}

		$conn->row_free();

		$sql .= '							) AS center
									  GROUP BY code
									  UNION ALL
									 SELECT code
									 ,      0 AS center_amt
									 ,      SUM(amt) AS sms_amt
									 ,      0 AS smart_amt
									 ,      0 AS deposit_amt
									 ,      0 AS edu_amt
									   FROM (';

		$conn->query('SHOW TABLES LIKE \'sms_acct_%\'');
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			$sql .= ($i > 0 ? ' UNION ALL ' : '');
			$sql .= 'SELECT org_no AS code
					,       tot AS amt
					   FROM '.$row[0];
		}

		$conn->row_free();

		$sql .= '							) AS smt
									  GROUP BY code
									  UNION ALL
									 SELECT code
									 ,      0 AS center_amt
									 ,      0 AS sms_amt
									 ,      SUM(amt) AS smart_amt
									 ,      0 AS deposit_amt
									 ,      0 AS edu_amt
									   FROM (';

		$conn->query('SHOW TABLES LIKE \'smart_acct_%\'');
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			$sql .= ($i > 0 ? ' UNION ALL ' : '');
			$sql .= 'SELECT org_no AS code
					,       total_amt AS amt
					   FROM '.$row[0];
		}

		$conn->row_free();

		$sql .= '							) AS smart
									  GROUP BY code
									  UNION ALL
									 SELECT org_no
									 ,      0 AS center_amt
									 ,      0 AS sms_amt
									 ,      0 AS smart_amt
									 ,      SUM(amt) AS deposit_amt
									 ,      0 AS edu_amt
									   FROM center_deposit_'.$gDomainID.'
									  GROUP BY org_no
									  UNION ALL
									 SELECT org_no
									 ,      0 AS center_amt
									 ,      0 AS sms_amt
									 ,      0 AS smart_amt
									 ,      0 AS deposit_amt
									 ,      SUM(amt) AS edu_amt
									   FROM edu_acct
									  GROUP BY org_no
									 ) AS amt
							   GROUP BY code
							  ) AS amt
						   ON amt.code = mst.code
					   ) AS t
				 ORDER BY CASE WHEN unpaid > 0 THEN 1
				               WHEN unpaid < 0 THEN 2
							   WHEN tot > 0 THEN 3 ELSE 4 END, name';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $row['code'].chr(2)
				  .  $row['name'].chr(2)
				  .  $row['tot'].chr(2)
				  .  $row['center'].chr(2)
				  .  $row['sms'].chr(2)
				  .  $row['smart'].chr(2)
				  .  $row['unpaid'].chr(2)
				  .  $row['deposit'].chr(2)
				  .  $row['edu'].chr(1);
		}

		$conn->row_free();

		echo $data;

	}else if ($mode == '42'){
		//월별 입금내역
		$year = $_POST['year'];

		$sql = 'SELECT CAST(DATE_FORMAT(reg_dt,\'%m\') AS unsigned) AS dt
				,      SUM(amt) AS amt
				  FROM center_deposit_'.$gDomainID.'
				 WHERE DATE_FORMAT(reg_dt,\'%Y\') = \''.$year.'\'
				 GROUP BY DATE_FORMAT(reg_dt,\'%m\')';

		$mon = $conn->_fetch_array($sql,'dt');

		for($i=1; $i<=12; $i++){
			$data .= IntVal($mon[$i]['amt']);

			if ($i < 12){
				$data .= chr(2);
			}
		}

		echo $data;

	}else if ($mode == '42_1'){
		//월별 입금상세내역
		$year  = $_POST['year'];
		$month = $_POST['month'];

		$sql = 'SELECT mst.code
				,      mst.name
				,      acct.cms_cd
				,      acct.reg_dt
				,      acct.amt
				,      acct.type
				,      acct.other
				  FROM (
					   SELECT branch.code
					   ,      cms.cms_cd
					   ,      cms.reg_dt
					   ,      cms.amt
					   ,      cms.type
					   ,      cms.other
					     FROM (
						      SELECT cms_cd
						      ,      reg_dt
						      ,      amt
						      ,      type
						      ,      other
							    FROM center_deposit_'.$gDomainID.'
							   WHERE DATE_FORMAT(reg_dt,\'%Y%m\') = \''.$year.$month.'\'
						      ) AS cms
					     LEFT JOIN (
						      SELECT b02_center AS code
						      ,      cms_cd
							    FROM b02center
						      ) AS branch
						   ON branch.cms_cd = cms.cms_cd
					   ) AS acct
				  LEFT JOIN (
					   SELECT MIN(m00_mkind) AS kind
					   ,      m00_mcode AS code
					   ,      m00_store_nm AS name
						 FROM m00center
						WHERE m00_domain = \''.$gDomain.'\'
						  AND m00_del_yn = \'N\'
						GROUP BY m00_mcode
					   ) AS mst
					ON mst.code = acct.code
				 ORDER BY CASE WHEN mst.code IS NULL THEN 1 ELSE 2 END, name';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if (Empty($row['code'])){
				$row['code'] = $row['cms_cd'];
				$row['name'] = 'CMS 미등록기관';
				$lsYn = 'N';
			}else{
				$lsYn = 'Y';
			}

			if ($row['type'] == '1'){
				$row['type'] = 'CMS';
			}else if ($row['type'] == '3'){
				$row['type'] = '현금';
			}else if ($row['type'] == '5'){
				$row['type'] = '카드';
			}else{
				$row['type'] = '기타';
			}

			$data .= $myF->dateStyle($row['reg_dt'],'.').chr(2)
				  .  $row['code'].chr(2)
				  .  $row['name'].chr(2)
				  .  $row['amt'].chr(2)
				  .  $row['type'].chr(2)
				  .  $row['other'].chr(2)
				  .  $lsYn.chr(1);
		}

		$conn->row_free();

		echo $data;

	}else if ($mode == '2_1' || $mode == '12_1' || $mode == '32_1'){
		$year = $_POST['year'];

		if ($mode == '2_1'){
			$table = 'sms_acct_'.$year;
		}else if ($mode == '12_1'){
			$table = 'smart_acct_'.$year;
		}else if ($mode == '32_1'){
			$table = 'center_acct_'.$year;
		}

		$laCnt = Array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0);

		for($i=1; $i<=12; $i++){
			$sql = 'SELECT COUNT(*)
					  FROM (
					       SELECT MIN(m00_mkind) AS kind
						   ,      m00_mcode AS code
						   ,      m00_store_nm AS name
							 FROM m00center
							WHERE m00_domain = \''.$gDomain.'\'
							  AND m00_del_yn = \'N\'
							GROUP BY m00_mcode
					       ) AS mst
					 INNER JOIN (
					       SELECT org_no AS code
					         FROM '.$table.($i < 10 ? '0' : '').$i.'
							LIMIT 1
						   ) AS acct
						ON acct.code = mst.code';
			@$laCnt[$i] = IntVal($conn->get_data($sql));
		}

		echo $laCnt[1].chr(2)
			.$laCnt[2].chr(2)
			.$laCnt[3].chr(2)
			.$laCnt[4].chr(2)
			.$laCnt[5].chr(2)
			.$laCnt[6].chr(2)
			.$laCnt[7].chr(2)
			.$laCnt[8].chr(2)
			.$laCnt[9].chr(2)
			.$laCnt[10].chr(2)
			.$laCnt[11].chr(2)
			.$laCnt[12];

	}else if ($mode == '41_1'){
		$code = $_POST['code'];

		$sql = 'SELECT DATE_FORMAT(reg_dt,\'%Y.%m.%d\') AS reg_dt
				,      amt
				,      type
				,      other
				  FROM center_deposit_'.$gDomainID.'
				 WHERE org_no = \''.$code.'\'
				 ORDER BY reg_dt DESC';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $row['reg_dt'].chr(2)
				  .  $row['amt'].chr(2)
				  .  $row['type'].chr(2)
				  .  $row['other'].chr(1);
		}

		$conn->row_free();

		echo $data;

	}else if ($mode == '71'){
		$SR = $_POST['SR'];

		$sql = 'SELECT	*
				FROM	suga_care';

		if ($SR == 'S'){
			$sql .= '
				WHERE	cd1 >= \'A\'
				AND		cd1 <= \'Z\'';
		}else if ($SR == 'R'){
			$sql .= '
				WHERE	cd1 >= \'1\'
				AND		cd1 <= \'9\'';
		}else{
			$sql .= '
				WHERE	cd1 >= \'\'';
		}

		$sql .= '
				ORDER	BY cd1, cd2, cd3, seq DESC';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if (!$arr[$row['cd1']]){
				$arr[$row['cd1']]['name'] = $row['nm1'];
				$arr[$row['cd1']]['cnt'] = 0;
			}

			if (!$arr[$row['cd1']][$row['cd2']]){
				$arr[$row['cd1']][$row['cd2']]['name'] = $row['nm2'];
				$arr[$row['cd1']][$row['cd2']]['cnt'] = 0;
			}

			$arr[$row['cd1']]['cnt'] ++;
			$arr[$row['cd1']][$row['cd2']]['cnt'] ++;
			$arr[$row['cd1']][$row['cd2']][$row['cd3']][$row['seq']]['name']	= $row['nm3'];
			$arr[$row['cd1']][$row['cd2']][$row['cd3']][$row['seq']]['seq']		= $row['seq'];
			$arr[$row['cd1']][$row['cd2']][$row['cd3']][$row['seq']]['cost']	= $row['cost'];
			$arr[$row['cd1']][$row['cd2']][$row['cd3']][$row['seq']]['from']	= $row['from_dt'];
			$arr[$row['cd1']][$row['cd2']][$row['cd3']][$row['seq']]['to']		= $row['to_dt'];
		}

		$conn->row_free();

		foreach($arr as $cd1 => $sub){
			if (Is_Array($sub)){
				$cnt1 = $sub['cnt'];

				foreach($sub as $cd2 => $dtl){
					if (Is_Array($dtl)){
						$cnt2 = $dtl['cnt'];

						foreach($dtl as $cd3 => $arr){
							if (Is_Array($arr)){
								foreach($arr as $seq => $suga){
									if ($cnt1 > 0){
										$data .= 'cnt1='.$cnt1;
										$data .= '&nm1='.$sub['name'];
									}

									if ($cnt2 > 0){
										$data .= '&cnt2='.$cnt2;
										$data .= '&nm2='.$dtl['name'];
									}

									$data .= '&cd1='.$cd1;
									$data .= '&cd2='.$cd2;
									$data .= '&cd3='.$cd3;
									$data .= '&nm3='.$suga['name'];
									$data .= '&seq='.$suga['seq'];
									$data .= '&cost='.$suga['cost'];
									$data .= '&from='.$suga['from'];
									$data .= '&to='.$suga['to'];
									$data .= chr(13);

									$cnt1 = 0;
									$cnt2 = 0;
								}
							}
						}
					}
				}
			}
		}

		echo $data;

	}else if ($mode == 'CARE_SUGA_FIND'){
		//재가지원 대분류 코드 조회
		$type = $_POST['type'];
		$mstCd = $_POST['mstCd'];
		$proCd = $_POST['proCd'];

		$SR = $_POST['SR'];

		if ($type == 'MST'){
			$sql = 'SELECT	DISTINCT cd1 AS cd,nm1 AS nm
					FROM	suga_care';

			if ($SR == 'S'){
				$sql .= '
					WHERE	cd1 >= \'A\'
					AND		cd1 <= \'Z\'';
			}else if ($SR == 'R'){
				$sql .= '
					WHERE	cd1 >= \'1\'
					AND		cd1 <= \'9\'';
			}else{
				$sql .= '
					WHERE	cd1 = \'\'';
			}
		}else if ($type == 'PRO'){
			$sql = 'SELECT	DISTINCT cd2 AS cd,nm2 AS nm
					FROM	suga_care
					WHERE	cd1 = \''.$mstCd.'\'';
		}else if ($type == 'SVC'){
			$sql = 'SELECT	DISTINCT cd3 AS cd,nm3 AS nm
					FROM	suga_care
					WHERE	cd1 = \''.$mstCd.'\'
					AND		cd2 = \''.$proCd.'\'';
		}

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$data .= 'code='.$row['cd'];
			$data .= '&name='.Str_Replace('<br>','/',$row['nm']);
			$data .= chr(11);
		}

		$conn->row_free();

		echo $data;

	}else if ($mode == 'CARE_SUGA_HIS'){
		//재가지원 이력내역
		$mstCd = $_POST['mstCd'];
		$proCd = $_POST['proCd'];
		$svcCd = $_POST['svcCd'];

		$sql = 'SELECT	seq
				,		cd1 AS mst_cd,nm1 AS mst_nm
				,		cd2 AS pro_cd,nm2 AS pro_nm
				,		cd3 AS svc_cd,nm3 AS svc_nm
				,		cost
				,		from_dt
				,		to_dt
				FROM	suga_care
				WHERE	cd1 = \''.$mstCd.'\'
				AND		cd2 = \''.$proCd.'\'
				AND		cd3 = \''.$svcCd.'\'
				ORDER	BY seq DESC';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$data .= 'seq='.$row['seq'];
			$data .= '&code='.$row['mst_cd'].$row['pro_cd'].$row['svc_cd'];
			$data .= '&mst='.Str_Replace('<br>','',$row['mst_nm']);
			$data .= '&pro='.Str_Replace('<br>','',$row['pro_nm']);
			$data .= '&svc='.Str_Replace('<br>','',$row['svc_nm']);
			$data .= '&cost='.$row['cost'];
			$data .= '&from='.Str_Replace('-','',$row['from_dt']);
			$data .= '&to='.Str_Replace('-','',$row['to_dt']);
			$data .= chr(11);
		}

		$conn->row_free();

		echo $data;

	}else{
		$conn->close();
		echo 9;
		exit;
	}

	include_once('../inc/_db_close.php');
?>