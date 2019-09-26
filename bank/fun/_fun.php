<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if (!IsSet($_SESSION['USER_CODE'])){
		exit;
	}

	$insuCode = '0';
	$type = $_POST['type'];
	$height = $_POST['height'];

	$code = $_POST['code'];
	$year = $_POST['year'] != '' ? $_POST['year'] : '';
	$month = $_POST['month'] != '' ? $_POST['month'] : '';
	$day   = $_POST['day'] != '' ? $_POST['day'] : '';

	if ($type == '1'){
		$sql = 'SELECT DISTINCT org_no AS cd
				  FROM insu_center
				 WHERE insu_cd = \''.$insuCode.'\'';
		$laInsu = $conn->_fetch_array($sql);

		//기관별 신청 소계리스트
		$sql = 'SELECT mst.code
				,      mst.cd
				,      mst.nm
				,      insu.join_cnt
				,      insu.quit_cnt
				,      insu.cnt
				,      insu.tot
				  FROM (';

		foreach($laInsu as $insu){
			$sl .= (!Empty($sl) ? ' UNION ALL ' : '');
			$sl .= 'SELECT m00_mcode AS code
				    ,      m00_code1 AS cd
				    ,      m00_cname AS nm
					  FROM m00center
					 WHERE m00_mcode = \''.$insu['cd'].'\'
					   AND m00_mkind = \'0\'';
		}

		$sql .= $sl.'
					   ) AS mst
				  LEFT JOIN (
					   SELECT cd
					   ,      SUM(CASE stat WHEN \'1\' THEN 1 ELSE 0 END) AS join_cnt
					   ,      SUM(CASE stat WHEN \'7\' THEN 1 ELSE 0 END) AS quit_cnt
					   ,      SUM(CASE WHEN stat = \'1\' OR stat = \'7\' THEN 1 ELSE 0 END) AS cnt
					   ,      SUM(tot) AS tot
						 FROM (
							  SELECT org_no AS cd
						      ,      stat
							  ,      0 AS tot
						        FROM insu
						       WHERE stat = \'1\'
							     /*AND start_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')*/
						       UNION ALL
							  SELECT org_no AS cd
						      ,      stat
							  ,      0 AS tot
						        FROM insu
						       WHERE stat = \'7\'
							     /*AND end_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')*/
							   UNION ALL
							  SELECT org_no AS cd
						      ,      \'\'
							  ,      COUNT(*) AS tot
						        FROM insu
							   WHERE stat  > \'1\'
							     AND stat != \'9\'
						       GROUP BY org_no
						     ) AS insu
					    GROUP BY cd
					   ) AS insu
					ON insu.cd = mst.code
				 ORDER BY CASE WHEN cnt > 0 THEN 1 ELSE 2 END, CASE WHEN tot > 0 THEN 1 ELSE 2 END, nm';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $row['code'].chr(2)
				  .  $row['cd'].chr(2)
				  .  $row['nm'].chr(2)
				  .  $row['join_cnt'].chr(2)
				  .  $row['quit_cnt'].chr(2)
				  .  $row['cnt'].chr(2)
				  .  $row['tot'].chr(1);
		}

		$conn->row_free();

		echo $data;

	}else if ($type == '3_1' || $type == '3_2'){
		$code = $_POST['code'];

		if ($type == '3_1'){
			$stat[0] = '1';
			$stat[1] = '7';
		}else if ($type == '3_2'){
			$stat[0] = '3';
			$stat[1] = '9';
		}else{
			exit;
		}

		$sql = 'SELECT mst.jumin
				,      mst.name
				,      insu.seq
				,      insu.join_dt
				,      insu.quit_dt
				,      insu.start_dt
				,      insu.end_dt
				,      insu.stat
				  FROM (
					   SELECT jumin
					   ,      seq
					   ,      join_dt
					   ,      quit_dt
					   ,      start_dt
					   ,      end_dt
					   ,      stat
						 FROM insu
						WHERE org_no = \''.$code.'\'
						  AND stat   = \''.$stat[0].'\'
						UNION ALL
					   SELECT jumin
					   ,      seq
					   ,      join_dt
					   ,      quit_dt
					   ,      start_dt
					   ,      end_dt
					   ,      stat
						 FROM insu
						WHERE org_no = \''.$code.'\'
						  AND stat   = \''.$stat[1].'\'
					   ) AS insu
				 INNER JOIN (
					   SELECT DISTINCT
							  m02_yjumin AS jumin
					   ,      m02_yname AS name
						 FROM m02yoyangsa
						WHERE m02_ccode = \''.$code.'\'
					   ) AS mst
					ON mst.jumin = insu.jumin
				 ORDER BY name';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $myF->issStyle($row['jumin']).chr(2)
				  .  $row['name'].chr(2)
				  .  $myF->dateStyle($row['join_dt'],'.').chr(2)
				  .  $myF->dateStyle($row['quit_dt'],'.').chr(2)
				  .  $myF->dateStyle($row['start_dt'],'.').chr(2)
				  .  $myF->dateStyle($row['end_dt'],'.').chr(2)
				  .  $row['stat'].chr(2)
				  .  $ed->en($row['jumin']).chr(2)
				  .  $row['seq'].chr(1);
		}

		$conn->row_free();

		echo $data;

	}else if ($type == '3_REG'){
		$code  = $_POST['code'];
		$jumin = $_POST['jumin'];
		$seq   = $_POST['seq'];
		$stat  = $_POST['stat'];
		$join  = $_POST['join'];
		$date  = $myF->dateStyle($_POST['date']);

		if (!Is_Numeric($jumin)) $jumin = $ed->de($jumin);

		if ($stat == '1'){
			//가입
			$sql = 'UPDATE insu
					   SET stat      = \'3\'
					,      start_dt  = \''.$date.'\'
					,      insert_dt = NOW()
					 WHERE org_no = \''.$code.'\'
					   AND jumin  = \''.$jumin.'\'
					   AND seq    = \''.$seq.'\'';
		}else{
			//해지
			$sql = 'UPDATE insu
					   SET stat      = \'9\'
					,      insert_dt = NOW()
					 WHERE org_no = \''.$code.'\'
					   AND jumin  = \''.$jumin.'\'
					   AND seq    = \''.$seq.'\'';
		}

		if ($conn->execute($sql)){
			echo 1;
		}else{
			echo 9;
		}

	}else if ($type == '3_CANCEL'){
		$code  = $_POST['code'];
		$jumin = $_POST['jumin'];
		$seq   = $_POST['seq'];
		$stat  = $_POST['stat'];

		if (!Is_Numeric($jumin)) $jumin = $ed->de($jumin);

		if ($stat == '9')
			$stat = '7';
		else if ($stat == '3')
			$stat = '1';

		//취소
		$sql = 'UPDATE insu
				   SET stat      = \''.$stat.'\'
				,      insert_dt = NOW()
				 WHERE org_no = \''.$code.'\'
				   AND jumin  = \''.$jumin.'\'
				   AND seq    = \''.$seq.'\'';

		if ($conn->execute($sql)){
			echo 1;
		}else{
			echo 9;
		}

	}else if ($type == 'SEARCH'){
		$fromDt = $myF->dateStyle($_POST['from']);
		$toDt   = $myF->dateStyle($_POST['to']);

		$sql = 'SELECT DISTINCT org_no AS cd
				  FROM insu_center
				 WHERE insu_cd = \''.$insuCode.'\'';
		$laInsu = $conn->_fetch_array($sql);

		$sql = 'SELECT insu.code
				,      mst.cd
				,      mst.nm
				,      insu.jumin
				,      insu.name
				,      insu.start_dt
				,      insu.end_dt
				  FROM (';

		foreach($laInsu as $insu){
			$sl .= (!Empty($sl) ? ' UNION ALL ' : '');
			$sl .= 'SELECT insu.code
					,      mem.jumin
					,      mem.name
					,      insu.start_dt
					,      insu.end_dt
					  FROM (
						   SELECT org_no AS code
						   ,      jumin
						   ,      seq
						   ,      start_dt
						   ,      end_dt
							 FROM insu
							WHERE org_no    = \''.$insu['cd'].'\'
							  AND start_dt >= \''.$fromDt.'\'
							  AND start_dt <= \''.$toDt.'\'
							  AND stat     >  \'1\'
						   ) AS insu
					 INNER JOIN (
						   SELECT DISTINCT
								  m02_yjumin AS jumin
						   ,      m02_yname AS name
							 FROM m02yoyangsa
							WHERE m02_ccode = \''.$insu['cd'].'\'
						   ) AS mem
						ON mem.jumin = insu.jumin';
		}

		$sql .= $sl.'  ) AS insu
				 INNER JOIN (
					   SElECT m00_mcode AS code
					   ,      m00_code1 AS cd
					   ,      m00_cname AS nm
						 FROM m00center
						WHERE m00_mkind = \'0\'
					   ) AS mst
					ON mst.code = insu.code
				 ORDER BY mst.nm, insu.name';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $row['code'].chr(2)
				  .  $row['cd'].chr(2)
				  .  $row['nm'].chr(2)
				  .  $myF->issStyle($row['jumin']).chr(2)
				  .  $row['name'].chr(2)
				  .  $row['start_dt'].chr(2)
				  .  $row['end_dt'].chr(1);
		}

		$conn->row_free();

		echo $data;

	}else if ($type == 'INCHARGE_SAVE'){
		$insu = '0';
		$nm1  = $_POST['nm1'];
		$nm2  = $_POST['nm2'];
		$tel2 = $_POST['tel2'];
		$fax2 = $_POST['fax2'];
		$nm3  = $_POST['nm3'];
		$tel3 = $_POST['tel3'];
		$fax3 = $_POST['fax3'];

		$sql = 'REPLACE INTO insu_incharge (
				 insu_cd
				,name1
				,name2
				,tel2
				,fax2
				,name3
				,tel3
				,fax3) values (
				 \''.$insu.'\'
				,\''.$nm1.'\'
				,\''.$nm2.'\'
				,\''.$tel2.'\'
				,\''.$fax2.'\'
				,\''.$nm3.'\'
				,\''.$tel3.'\'
				,\''.$fax3.'\'
				)';

		if ($conn->execute($sql)){
			echo 1;
		}else{
			echo 9;
		}

	}else if ($type == 'REQUEST_CENTER'){
		$lsCenterNm = $_POST['center'];
		$lsFindYn   = $_POST['find'];

		$sql = 'SELECT DISTINCT org_no AS cd
				  FROM insu_center
				 WHERE insu_cd = \''.$insuCode.'\'';

		if ($lsFindYn != 'ALL'){
			$sql .= ' AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					  AND DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';
		}

		$laInsu = $conn->_fetch_array($sql);

		if (Is_Array($laInsu)){
			foreach($laInsu as $insu){
				$sl .= (!Empty($sl) ? ' UNION ALL ' : '');
				$sl .= 'SELECT m00_mcode AS code
						,      m00_code1 AS cd
						,      m00_cname AS nm
						,     (SELECT COUNT(*)
								 FROM insu
								WHERE org_no = \''.$insu['cd'].'\'
								  AND stat   > \'1\'
								  AND join_prt_yn = \'N\'
								  AND DATE_FORMAT(start_dt,\'%Y%m\') = \''.$year.$month.'\') +
							  (SELECT COUNT(*)
								 FROM insu
								WHERE org_no = \''.$insu['cd'].'\'
								  AND stat   = \'9\'
								  AND quit_prt_yn = \'N\'
								  AND DATE_FORMAT(end_dt,\'%Y%m\') = \''.$year.$month.'\') AS cnt
						  FROM m00center
						 WHERE m00_mcode = \''.$insu['cd'].'\'
						   AND m00_mkind = \'0\'';

				if (!Empty($lsCenterNm)){
					$sl .= ' AND m00_cname LIKE \'%'.$lsCenterNm.'%\'';
				}
			}

			$sql = $sl.' ORDER BY cnt DESC, nm';

			$conn->query($sql);
			$conn->fetch();

			$rowCount = $conn->row_count();

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				$data .= $row['code'].chr(2)
					  .  $row['cd'].chr(2)
					  .  $row['nm'].chr(2)
					  .  $row['cnt'].chr(1);
			}

			$conn->row_free();
		}

		echo $data;

	}else if ($type == 'CENTER_INSU_MON_CNT'){
		$sql = 'SELECT SUM(m01) AS m01
				,      SUM(m02) AS m02
				,      SUM(m03) AS m03
				,      SUM(m04) AS m04
				,      SUM(m05) AS m05
				,      SUM(m06) AS m06
				,      SUM(m07) AS m07
				,      SUM(m08) AS m08
				,      SUM(m09) AS m09
				,      SUM(m10) AS m10
				,      SUM(m11) AS m11
				,      SUM(m12) AS m12
				  FROM (
					   SELECT SUM(CASE DATE_FORMAT(start_dt,\'%m\') WHEN \'01\' THEN 1 ELSE 0 END) AS m01
					   ,      SUM(CASE DATE_FORMAT(start_dt,\'%m\') WHEN \'02\' THEN 1 ELSE 0 END) AS m02
					   ,      SUM(CASE DATE_FORMAT(start_dt,\'%m\') WHEN \'03\' THEN 1 ELSE 0 END) AS m03
					   ,      SUM(CASE DATE_FORMAT(start_dt,\'%m\') WHEN \'04\' THEN 1 ELSE 0 END) AS m04
					   ,      SUM(CASE DATE_FORMAT(start_dt,\'%m\') WHEN \'05\' THEN 1 ELSE 0 END) AS m05
					   ,      SUM(CASE DATE_FORMAT(start_dt,\'%m\') WHEN \'06\' THEN 1 ELSE 0 END) AS m06
					   ,      SUM(CASE DATE_FORMAT(start_dt,\'%m\') WHEN \'07\' THEN 1 ELSE 0 END) AS m07
					   ,      SUM(CASE DATE_FORMAT(start_dt,\'%m\') WHEN \'08\' THEN 1 ELSE 0 END) AS m08
					   ,      SUM(CASE DATE_FORMAT(start_dt,\'%m\') WHEN \'09\' THEN 1 ELSE 0 END) AS m09
					   ,      SUM(CASE DATE_FORMAT(start_dt,\'%m\') WHEN \'10\' THEN 1 ELSE 0 END) AS m10
					   ,      SUM(CASE DATE_FORMAT(start_dt,\'%m\') WHEN \'11\' THEN 1 ELSE 0 END) AS m11
					   ,      SUM(CASE DATE_FORMAT(start_dt,\'%m\') WHEN \'12\' THEN 1 ELSE 0 END) AS m12
						 FROM insu
						WHERE org_no = \''.$code.'\'
						  AND stat   > \'1\'
						  AND DATE_FORMAT(start_dt,\'%Y\') = \''.$year.'\'
						UNION ALL
					   SELECT SUM(CASE DATE_FORMAT(end_dt,\'%m\') WHEN \'01\' THEN 1 ELSE 0 END)
					   ,      SUM(CASE DATE_FORMAT(end_dt,\'%m\') WHEN \'02\' THEN 1 ELSE 0 END)
					   ,      SUM(CASE DATE_FORMAT(end_dt,\'%m\') WHEN \'03\' THEN 1 ELSE 0 END)
					   ,      SUM(CASE DATE_FORMAT(end_dt,\'%m\') WHEN \'04\' THEN 1 ELSE 0 END)
					   ,      SUM(CASE DATE_FORMAT(end_dt,\'%m\') WHEN \'05\' THEN 1 ELSE 0 END)
					   ,      SUM(CASE DATE_FORMAT(end_dt,\'%m\') WHEN \'06\' THEN 1 ELSE 0 END)
					   ,      SUM(CASE DATE_FORMAT(end_dt,\'%m\') WHEN \'07\' THEN 1 ELSE 0 END)
					   ,      SUM(CASE DATE_FORMAT(end_dt,\'%m\') WHEN \'08\' THEN 1 ELSE 0 END)
					   ,      SUM(CASE DATE_FORMAT(end_dt,\'%m\') WHEN \'08\' THEN 1 ELSE 0 END)
					   ,      SUM(CASE DATE_FORMAT(end_dt,\'%m\') WHEN \'10\' THEN 1 ELSE 0 END)
					   ,      SUM(CASE DATE_FORMAT(end_dt,\'%m\') WHEN \'11\' THEN 1 ELSE 0 END)
					   ,      SUM(CASE DATE_FORMAT(end_dt,\'%m\') WHEN \'12\' THEN 1 ELSE 0 END)
						 FROM insu
						WHERE org_no = \''.$code.'\'
						  AND stat   = \'9\'
						  AND DATE_FORMAT(end_dt,\'%Y\') = \''.$year.'\'
					   ) AS t';

		$tmp = $conn->get_array($sql);
		$data = $tmp['m01'].chr(2)
			  . $tmp['m02'].chr(2)
			  . $tmp['m03'].chr(2)
			  . $tmp['m04'].chr(2)
			  . $tmp['m05'].chr(2)
			  . $tmp['m06'].chr(2)
			  . $tmp['m07'].chr(2)
			  . $tmp['m08'].chr(2)
			  . $tmp['m09'].chr(2)
			  . $tmp['m10'].chr(2)
			  . $tmp['m11'].chr(2)
			  . $tmp['m12'].chr(1);

		echo $data;

	}else if ($type == 'REQUEST_MEMBER'){
		$seq = $_POST['seq'];

		$sql = 'SELECT from_dt
				,      to_dt
				  FROM insu_center
				 WHERE org_no = \''.$code.'\'
				   AND svc_cd = \'0\'';

		if (Is_Numeric($seq)){
			$sql .= ' AND seq = \''.$seq.'\'';
		}else{
			$sql .= ' AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				      AND DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';
		}

		$sql .= ' ORDER BY seq DESC
				  LIMIT 1';

		$insu = $conn->get_array($sql);

		$sql = 'SELECT mem.jumin
				,      mem.name
				,      insu.start_dt
				,      insu.end_dt
				,      insu.stat
				,      insu.prt_yn
				,      insu.lotte_flag
				  FROM (
					   SELECT jumin
					   ,      start_dt
					   ,      end_dt
					   ,      stat
					   ,      join_prt_yn AS prt_yn
					   ,      lotte_flag
						 FROM insu
						WHERE org_no = \''.$code.'\'
						  AND stat   > \'1\'';

		if (Is_Numeric($seq)){
			$sql .= ' AND start_dt >= \''.$insu['from_dt'].'\'
					  AND start_dt <  \''.$insu['to_dt'].'\'
					  AND IFNULL(end_dt,\''.$insu['to_dt'].'\') <= \''.$insu['to_dt'].'\'';
		}else{
			$sql .= '	  AND DATE_FORMAT(start_dt,\'%Y%m\') = \''.$year.$month.'\'
						UNION ALL
					   SELECT jumin
					   ,      start_dt
					   ,      end_dt
					   ,      stat
					   ,      quit_prt_yn
					   ,      lotte_flag
						 FROM insu
						WHERE org_no = \''.$code.'\'
						  AND stat   = \'9\'
						  AND DATE_FORMAT(end_dt,\'%Y%m\') = \''.$year.$month.'\'';
		}

		$sql .= '	   ) AS insu
				 INNER JOIN (
					   SELECT MIN(m02_mkind)
					   ,      m02_yjumin AS jumin
					   ,      m02_yname AS name
						 FROM m02yoyangsa
						WHERE m02_ccode = \''.$code.'\'
						GROUP BY m02_yjumin
					   ) AS mem
					ON mem.jumin = insu.jumin
				 ORDER BY name';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if (Is_Numeric($seq)){
				$lsEndDt = $row['end_dt'];
			}else{
				if ($row['stat'] == '9'){
					$lsEndDt = $row['end_dt'];
				}else{
					$lsEndDt = $insu['to_dt'];
				}
			}

			$data .= $row['name'].chr(2)
				  .  $myF->issStyle($row['jumin']).chr(2)
				  .  $myF->dateStyle($row['start_dt'],'.').chr(2)
				  .  $myF->dateStyle($lsEndDt,'.').chr(2)
				  .  $row['stat'].chr(2)
				  .  $row['prt_yn'].chr(1);
		}

		$conn->row_free();

		echo $data;

	}else if ($type == 'CENTER_LIST'){
		$name = $_POST['center'];

		$sql = 'SELECT insu.code
				,      mst.cd
				,      mst.nm
				,      insu.from_dt
				,      insu.to_dt
				,      insu.secu_no
				,      insu.svc_cd
				,      insu.seq
				  FROM (
					   SELECT org_no AS code
					   ,      svc_cd
					   ,      secu_no
					   ,      from_dt
					   ,      to_dt
					   ,      seq
						 FROM insu_center
						WHERE svc_cd  = \'0\'
						  AND insu_cd = \'0\'
					   ) AS insu
				 INNER JOIN (
					   SELECT m00_mcode AS code
					   ,      m00_code1 AS cd
					   ,      m00_cname AS nm
						 FROM m00center
						WHERE m00_mkind = \'0\'';

		if (!Empty($name)){
			$sql .= ' AND m00_cname LIKE \'%'.$name.'%\'';
		}

		$sql .= '	   ) AS mst
					ON mst.code = insu.code
				 ORDER BY nm, seq DESC';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $row['code'].chr(2)
				  .  $row['cd'].chr(2)
				  .  $row['nm'].chr(2)
				  .  $myF->dateStyle($row['from_dt'],'.').chr(2)
				  .  $myF->dateStyle($row['to_dt'],'.').chr(2)
				  .  $row['secu_no'].chr(2)
				  .  $row['svc_cd'].chr(2)
				  .  $row['seq'].chr(1);
		}

		$conn->row_free();

		echo $data;

	}else if ($type == 'REG_PERIOD'){
		$sql = 'SELECT seq
				,      from_dt
				,      to_dt
				  FROM insu_center
				 WHERE org_no = \''.$code.'\'
				   AND svc_cd = \'0\'
				 ORDER BY seq DESC';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $row['seq'].chr(2)
				  .  $myF->dateStyle($row['from_dt'],'.').chr(2)
				  .  $myF->dateStyle($row['to_dt'],'.').chr(1);
		}

		$conn->row_free();

		echo $data;

	}else {

	}

	include_once('../inc/_db_close.php');
?>