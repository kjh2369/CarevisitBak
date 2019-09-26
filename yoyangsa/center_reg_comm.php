<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$mode = $_POST['mode'];

	if ($mode == '1' || $mode == '11' || $mode == '21'){
		$page = IntVal($_POST['page']);
		$max  = IntVal($_POST['max']);

		if ($mode == '1'){
			$table = 'sms_acct';
		}else if ($mode == '11'){
			$table = 'smart_acct';
		}else if ($mode == '21'){
			$table = 'bank_center';
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
				  .  $editYn.chr(1);
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
		$page = IntVal($_POST['page']);
		$max  = IntVal($_POST['max']);
		$findCd       = $_POST['code'];
		$findNm       = $_POST['name'];
		$findManager  = $_POST['manager'];
		$findCMS      = $_POST['cms'];
		$findBranch   = $_POST['branch'];
		$findContFrom = $_POST['contFrom'];
		$findContTo   = $_POST['contTo'];
		$findContYn   = $_POST['contYn'];
		$findCMSYn    = $_POST['cmsYn'];

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
			$sql = 'SELECT DISTINCT
						   mst.m00_mcode AS code
					,      mst.m00_store_nm AS name
					,      IFNULL(mst.m00_cont_date,\'\') AS cont_dt
					,      IFNULL(mst.m00_mname,\'\') AS rep_nm
					,      IFNULL(center.from_dt,\'\') AS from_dt
					,      IFNULL(center.to_dt,\'\') AS to_dt
					,      center.b02_homecare AS homecare
					,      center.b02_voucher AS voucher
					,      center.b02_date AS start_dt
					,      IFNULL(center.cms_cd,\'\') AS cms_cd
					,      center.hold_yn
					,      center.basic_cost
					,      center.client_cost
					,      center.client_cnt
					,      branch.b00_code AS branch_cd
					,      branch.b00_name AS branch_nm
					,      manager.b01_code AS manager_cd
					,      manager.b01_name AS manager_nm
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
				$sql .= ' AND mst.m00_cont_date >= \''.$findContFrom.'\'
						  AND mst.m00_cont_date <= \''.$findContTo.'\'';
			}

			if ($findContYn == 'N'){
				$sql .= ' AND IFNULL(mst.m00_cont_date,\'\') = \'\'';
			}

			if ($findCMSYn == 'N'){
				$sql .= ' AND IFNULL(center.cms_cd,\'\') = \'\'';
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
				,      center.b02_date AS start_dt
				,      IFNULL(center.cms_cd,\'\') AS cms_cd
				,      center.hold_yn
				,      center.basic_cost
				,      center.client_cost
				,      center.client_cnt
				,      branch.b00_code AS branch_cd
				,      branch.b00_name AS branch_nm
				,      manager.b01_code AS manager_cd
				,      manager.b01_name AS manager_nm
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

			$data .= ($pageCnt + ($i + 1)).chr(2)
				  .  $row['code'].chr(2)
				  .  $row['name'].chr(2)
				  .  $row['from_dt'].chr(2)
				  .  $row['to_dt'].chr(2)
				  .  $row['cms_cd'].chr(2)
				  .  $row['hold_yn'].chr(2)
				  .  $row['basic_cost'].chr(2)
				  .  $row['client_cost'].chr(2)
				  .  $row['client_cnt'].chr(2)
				  .  $row['branch_cd'].chr(2)
				  .  $row['branch_nm'].chr(2)
				  .  $row['manager_cd'].chr(2)
				  .  $row['manager_nm'].chr(2)
				  .  $row['homecare'].$row['voucher'].chr(2)
				  .  $myF->dateStyle($row['start_dt']).chr(2)
				  .  $myF->dateStyle($row['cont_dt']).chr(2)
				  .  $row['other'].chr(2)
				  .  $row['rep_nm'].chr(1);
		}

		$conn->row_free();

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
									   FROM center_deposit
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
				  FROM center_deposit
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
							    FROM center_deposit
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
				  FROM center_deposit
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

	}else{
		$conn->close();
		echo 9;
		exit;
	}

	include_once('../inc/_db_close.php');
?>