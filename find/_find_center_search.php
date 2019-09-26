<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$mode = $_POST['mode'];
	$page = IntVal($_POST['page']);
	$max  = IntVal($_POST['max']);
	$code = $_POST['code'];
	$name = $_POST['name'];
	$pCnt = $_POST['pCnt'];

	if (!$pCnt) $pCnt = 10;

	if ($mode == '1'){
		$table = 'sms_acct';
	}else if ($mode == '11'){
		$table = 'smart_acct';
	}else if ($mode == '21'){
		$table = 'bank_center';
	}else if ($mode == '61'){
		$table = 'tax_acct';
	}else if ($mode == '62'){
		$table = 'labor_acct';
	}else{
		$table = '';
	}

	$itemCount = 20;
	$pageCount = $pCnt;

	$pageCnt = $page;

	if (Empty($pageCount)){
		$pageCount = 1;
	}

	$pageCnt = (intVal($pageCnt) - 1) * $itemCount;

	if ($page == 0){
		if ($mode == '31'){
			$sql = 'SELECT COUNT(*)
					  FROM (
						   SELECT DISTINCT
								  m00_mcode AS code
							 FROM m00center
							WHERE m00_domain = \''.$gDomain.'\'
							  AND m00_del_yn = \'N\'';

			if (!Empty($code)){
				$sql .= ' AND m00_mcode LIKE \''.$code.'%\'';
			}

			if (!Empty($name)){
				$sql .= ' AND m00_store_nm LIKE \'%'.$name.'%\'';
			}

			$sql .= '	   ) AS mst
					  LEFT JOIN (
						   SELECT DISTINCT
								  b02_center AS code
							 FROM b02center
							/*WHERE from_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
							  AND to_dt   >= DATE_FORMAT(NOW(),\'%Y-%m-%d\')*/
						   ) AS branch
						ON branch.code = mst.code
					 WHERE branch.code IS NULL';
		}else{
			$sql = 'SELECT COUNT(*)
					  FROM (
						   SELECT DISTINCT
								  m00_mcode AS code
							 FROM m00center
							INNER JOIN b02center
							   ON b02_center = m00_mcode';

			if (!Empty($table)){
				$sql .= ' LEFT JOIN '.$table.'
							ON '.$table.'.org_no = m00_mcode
						   AND '.$table.'.from_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
						   AND '.$table.'.to_dt   >= DATE_FORMAT(NOW(),\'%Y-%m-%d\')';
			}

			if ($mode == '97'){
				//사용중인 기관
				$sql .= ' INNER JOIN cv_reg_info AS a
								ON a.org_no = m00_mcode
								AND a.from_dt <= DATE_FORMAT(NOW(),\'%Y%m%d\')
								AND a.to_dt >= DATE_FORMAT(NOW(),\'%Y%m%d\')';
			}

			if ($gDomain == 'carevisit.net' && $mode == '99'){
				$sql .= ' WHERE m00_del_yn = \'N\'';
			}else{
				if ($mode == '97'){
					$sql .= ' WHERE m00_del_yn = \'N\'';
				}else { 
					$sql .= ' WHERE m00_domain = \''.$gDomain.'\'
								AND m00_del_yn = \'N\'';
				}
			}

			if (!Empty($table)){
				$sql .= ' AND '.$table.'.org_no IS NULL';
			}

			if (!Empty($code)){
				$sql .= ' AND m00_mcode LIKE \''.$code.'%\'';
			}

			if (!Empty($name)){
				$sql .= ' AND m00_store_nm LIKE \'%'.$name.'%\'';
			}

			$sql .= ') AS t';
		}

		$maxCount = $conn->get_data($sql);

		echo $maxCount;

		$conn->close();
		exit;
	}

	if ($mode == '31'){
		$sql = 'SELECT DISTINCT
					   mst.code
				,      mst.name
				,      mst.manager
				,      mst.addr
				  FROM (
					   SELECT DISTINCT
							  m00_mcode AS code
					   ,      m00_store_nm AS name
					   ,      m00_mname AS manager
					   ,      m00_caddr1 AS addr
						 FROM m00center
						WHERE m00_domain = \''.$gDomain.'\'
						  AND m00_del_yn = \'N\'';

		if (!Empty($code)){
			$sql .= ' AND m00_mcode LIKE \''.$code.'%\'';
		}

		if (!Empty($name)){
			$sql .= ' AND m00_store_nm LIKE \'%'.$name.'%\'';
		}

		$sql .= '	   ) AS mst
				  LEFT JOIN (
					   SELECT DISTINCT
							  b02_center AS code
						 FROM b02center
						/*WHERE from_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
						  AND to_dt   >= DATE_FORMAT(NOW(),\'%Y-%m-%d\')*/
					   ) AS branch
					ON branch.code = mst.code
				 WHERE branch.code IS NULL';
	}else{
		$sql = 'SELECT DISTINCT
					   m00_mcode AS code
				,      m00_store_nm AS name
				,      m00_mname AS manager
				,      m00_caddr1 AS addr
				  FROM m00center
				 INNER JOIN b02center
					ON b02_center = m00_mcode';

		if (!Empty($table)){
			$sql .= ' LEFT JOIN '.$table.'
						ON '.$table.'.org_no = m00_mcode
					   AND '.$table.'.from_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
					   AND '.$table.'.to_dt   >= DATE_FORMAT(NOW(),\'%Y-%m-%d\')';
		}

		if ($mode == '97'){
			//사용중인 기관
			$sql .= ' INNER JOIN cv_reg_info AS a
							ON a.org_no = m00_mcode
							AND a.from_dt <= DATE_FORMAT(NOW(),\'%Y%m%d\')
							AND a.to_dt >= DATE_FORMAT(NOW(),\'%Y%m%d\')';
		}

		#$sql .= ' WHERE m00_domain = \''.$gDomain.'\'
		#			AND m00_del_yn = \'N\'';
		if ($gDomain == 'carevisit.net' && $mode == '99'){
			$sql .= ' WHERE m00_del_yn = \'N\'';
		}else{
			if ($mode == '97'){
				$sql .= ' WHERE m00_del_yn = \'N\'';
			}else { 
				$sql .= ' WHERE m00_domain = \''.$gDomain.'\'
							AND m00_del_yn = \'N\'';
			}
		}

		if (!Empty($table)){
			$sql .= ' AND '.$table.'.org_no IS NULL';
		}

		if (!Empty($code)){
			$sql .= ' AND m00_mcode LIKE \''.$code.'%\'';
		}

		if (!Empty($name)){
			$sql .= ' AND m00_store_nm LIKE \'%'.$name.'%\'';
		}
	}

	$sql .= ' ORDER BY name
			  LIMIT '.$pageCnt.','.$itemCount.'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data .= ($pageCnt + ($i + 1)).chr(2)
			  .  $row['code'].chr(2)
			  .  $row['name'].chr(2)
			  .  $row['manager'].chr(2)
			  .  $row['addr'].chr(1);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>