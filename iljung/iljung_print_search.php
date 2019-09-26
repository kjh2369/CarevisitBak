<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$mode = $_POST['mode'];

	if ($mode == '101' ||
		$mode == '102' ||
		$mode == '103' ||
		$mode == '104'){
	}else{
		$conn->close();
		exit;
	}

	$code		= $_SESSION['userCenterCode'];
	$year		= $_POST['year'];
	$showGbn	= $_POST['showGbn'];
	$name		= $_POST['name'];
	$svcGbn		= Explode(chr(1),$_POST['chkSvc']);
	$order		= $_POST['optOrder'];
	$teamCd     = $ed->de($_POST['teamCd']);

	if ($showGbn == 'family'){
		$showSql = ' AND t01_toge_umu    = \'Y\'
					 AND t01_svc_subcode = \'200\'';

	}else if ($showGbn == 'conf'){
		$showSql = ' AND t01_status_gbn = \'1\'';

	}else{
		$showSql = '';
	}

	if ($mode == '101'){
		//고객등급
		$sql = 'SELECT CONCAT(jumin, \'_\', svc_cd) AS id
				,      level AS lvl
				  FROM client_his_lvl
				 WHERE org_no           = \''.$code.'\'
				   AND LEFT(from_dt,4) <= \''.$year.'\'
				   AND LEFT(to_dt,4)   >= \''.$year.'\'
				 ORDER BY jumin, svc_cd, from_dt, to_dt';

		$arrLvl = $conn->_fetch_array($sql,'id');

		//고객구분
		$sql = 'SELECT jumin AS id
				,      kind
				,      rate
				  FROM client_his_kind
				 WHERE org_no           = \''.$code.'\'
				   AND LEFT(from_dt,4) <= \''.$year.'\'
				   AND LEFT(to_dt,4)   >= \''.$year.'\'
				 ORDER BY jumin, from_dt, to_dt';

		$arrKind = $conn->_fetch_array($sql,'id');

	}else if ($mode == '102'){
		$sql = 'SELECT dept_cd AS cd
				,      dept_nm AS nm
				  FROM dept
				 WHERE org_no   = \''.$code.'\'
				   AND del_flag = \'N\'';

		$arrDept = $conn->_fetch_array($sql,'cd');
	}

	//서비스별 조회
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

			$sl .= 'SELECT jumin
					,      CASE WHEN svc_cd = \'5\' THEN \'0\' ELSE svc_cd END AS svc_cd
					,      SUM(CASE yymm WHEN \''.$year.'01\' THEN 1 ELSE 0 END) AS m01
					,      SUM(CASE yymm WHEN \''.$year.'02\' THEN 1 ELSE 0 END) AS m02
					,      SUM(CASE yymm WHEN \''.$year.'03\' THEN 1 ELSE 0 END) AS m03
					,      SUM(CASE yymm WHEN \''.$year.'04\' THEN 1 ELSE 0 END) AS m04
					,      SUM(CASE yymm WHEN \''.$year.'05\' THEN 1 ELSE 0 END) AS m05
					,      SUM(CASE yymm WHEN \''.$year.'06\' THEN 1 ELSE 0 END) AS m06
					,      SUM(CASE yymm WHEN \''.$year.'07\' THEN 1 ELSE 0 END) AS m07
					,      SUM(CASE yymm WHEN \''.$year.'08\' THEN 1 ELSE 0 END) AS m08
					,      SUM(CASE yymm WHEN \''.$year.'09\' THEN 1 ELSE 0 END) AS m09
					,      SUM(CASE yymm WHEN \''.$year.'10\' THEN 1 ELSE 0 END) AS m10
					,      SUM(CASE yymm WHEN \''.$year.'11\' THEN 1 ELSE 0 END) AS m11
					,      SUM(CASE yymm WHEN \''.$year.'12\' THEN 1 ELSE 0 END) AS m12
					  FROM (';

			if ($mode == '101'){
				$sl .= 'SELECT t01_jumin AS jumin
						,      t01_mkind AS svc_cd
						,      LEFT(t01_sugup_date,6) AS yymm
						  FROM t01iljung
						 WHERE t01_ccode = \''.$code.'\'
						   AND t01_mkind = \''.$svcCd.'\'
						   AND LEFT(t01_sugup_date,4) = \''.$year.'\'
						   AND t01_del_yn = \'N\''.$showSql;

				if (!Empty($subCd)){
					$sl .= ' AND t01_svc_subcode = \''.$subCd.'\'';
				}

			}else if ($mode == '102'){
				$loopCnt = 1;

				if ($svcCd == '0' && $subCd == '500'){
					$loopCnt = 2;
				}else if ($svcCd == '4' && ($subCd == '200' || $subCd == '500')){
					$loopCnt = 2;
				}

				for($i=1; $i<=$loopCnt; $i++){
					if ($i > 1){
						$sl .= ' UNION ALL ';
					}

					if ($showGbn == 'conf'){
						$sl .= 'SELECT t01_yoyangsa_id'.$i.' AS jumin';
					}else{
						$sl .= 'SELECT t01_mem_cd'.$i.' AS jumin';
					}

					$sl .= ',      t01_mkind AS svc_cd
							,      LEFT(t01_sugup_date,6) AS yymm
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \''.$svcCd.'\'
							   AND LEFT(t01_sugup_date,4) = \''.$year.'\'
							   AND t01_del_yn = \'N\''.$showSql;

					if (!Empty($subCd)){
						$sl .= ' AND t01_svc_subcode = \''.$subCd.'\'';
					}
				}
			}

			$sl .= '       ) AS caln
					 GROUP BY jumin, svc_cd';
		}
	}

	$sql = 'SELECT caln.jumin';

	if ($mode == '101'){
		$sql .= ',      mst.m03_name AS name
				 ,		mst.m03_yoyangsa1_nm AS mem_nm
				 ,      caln.svc_cd';
	}else if ($mode == '102'){
		$sql .= ',      mst.m02_yname AS name
				 ,      mst.m02_dept_cd AS dept_cd';
	}

	$sql .=',      SUM(caln.m01) AS m01
			,      SUM(caln.m02) AS m02
			,      SUM(caln.m03) AS m03
			,      SUM(caln.m04) AS m04
			,      SUM(caln.m05) AS m05
			,      SUM(caln.m06) AS m06
			,      SUM(caln.m07) AS m07
			,      SUM(caln.m08) AS m08
			,      SUM(caln.m09) AS m09
			,      SUM(caln.m10) AS m10
			,      SUM(caln.m11) AS m11
			,      SUM(caln.m12) AS m12
			  FROM ('.$sl.') AS caln';

	if ($mode == '101'){
		$sql .= ' INNER JOIN m03sugupja AS mst
					 ON mst.m03_ccode = \''.$code.'\'
					AND mst.m03_mkind = CASE WHEN caln.svc_cd = \'S\' OR caln.svc_cd = \'R\' THEN \'6\'
											 WHEN caln.svc_cd = \'5\' THEN \'0\'
											 ELSE caln.svc_cd END
					AND mst.m03_jumin = caln.jumin';

		
		if (!Empty($teamCd)){
			
			$sql .= ' INNER JOIN client_his_team as team
					     ON team.org_no = mst.m03_ccode
						AND team.jumin  = mst.m03_jumin
						AND del_flag    = \'N\'
						AND left(from_ym, 4) <= \''.$year.'\'
						AND left(to_ym, 4) >= \''.$year.'\'
						AND team.team_cd = \''.$teamCd.'\'';
			
		}
		

		if (!Empty($name)){
			$sql .= ' WHERE mst.m03_name >= \''.$name.'\'';
		}
		
		$sql .= ' GROUP BY caln.jumin, mst.m03_name, caln.svc_cd';

	}else if ($mode == '102'){
		$sql .= ' INNER JOIN m02yoyangsa AS mst
					 ON mst.m02_ccode  = \''.$code.'\'
					AND mst.m02_mkind  = \''.$_SESSION['userCenterKind'][0].'\'
					AND mst.m02_yjumin = caln.jumin';

		if (!Empty($name)){
			$sql .= ' WHERE mst.m02_yname >= \''.$name.'\'';
		}

		$sql .= ' GROUP BY caln.jumin, mst.m02_yname';
	}

	if ($mode == '101'){
		if ($order == '2'){
			$sql .= ' ORDER BY CASE WHEN mem_nm != \'\' THEN 1 ELSE 2 END,mem_nm,name';
		}else{
			$sql .= ' ORDER BY name';
		}
	}else{
		$sql .= ' ORDER BY name';
	}
	
	
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data .= $ed->en($row['jumin']).chr(2)
			  .  $row['m01'].chr(2)
			  .  $row['m02'].chr(2)
			  .  $row['m03'].chr(2)
			  .  $row['m04'].chr(2)
			  .  $row['m05'].chr(2)
			  .  $row['m06'].chr(2)
			  .  $row['m07'].chr(2)
			  .  $row['m08'].chr(2)
			  .  $row['m09'].chr(2)
			  .  $row['m10'].chr(2)
			  .  $row['m11'].chr(2)
			  .  $row['m12'].chr(2)
			  .  ($row['name'] ? $row['name'] : ($debug ? $row['jumin'] : '')).chr(2);

		if ($mode == '101'){
			$data .= $row['svc_cd'].chr(2)
				  .  $arrLvl[$row['jumin'].'_'.$row['svc_cd']]['lvl'].chr(2)
				  .  $arrKind[$row['jumin']]['kind'].chr(2)
				  .  $row['mem_nm'].chr(1);

		}else if ($mode == '102'){
			$data .= $arrDept[$row['dept_cd']]['nm'].chr(1);
		}
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>