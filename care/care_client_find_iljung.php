<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if (!$IsExcel){
		$orgNo	= $_SESSION['userCenterCode'];
		$SR		= $_POST['SR'];
		$jumin	= $ed->de($_POST['jumin']);
		$today	= Date('Y-m-d');
		$year	= $_POST['year'];
		$month	= IntVal($_POST['month']);
		$month	= ($month < 10 ? '0' : '').$month;
	}else{
		$jumin = $code;
	}
	

	$sql = 'SELECT jumin, org_type as mkind, date, suga_cd, resource_cd as mem_cd1, mem_cd as mem_cd2, contents
			FROM   care_works_log
			WHERE  org_no	= \''.$orgNo.'\'
			AND	   org_type	= \''.$SR.'\'';
	
	if ($year && $month){
		$sql .= '
			AND		LEFT(date,6) = \''.$year.$month.'\'';
	}
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		
		$log[$row['jumin']][$row['mkind']][$row['date']][$row['suga_cd']][$row['mem_cd1']][$row['mem_cd2']]['contents'] = $row['contents'];


	}

	$conn -> row_free();


	$sql = 'SELECT	t01_sugup_date AS date
			,		t01_mkind AS mkind
			,		t01_suga_code1 AS suga_cd
			,		a.suga_nm
			,	    t01_jumin AS jumin
			,		t01_yoyangsa_id1 AS res_cd
			,		t01_yname1 AS res_nm
			,		t01_yoyangsa_id2 AS mem_cd
			,		t01_yname2 AS mem_nm
			,		t01_sugup_fmtime
			,		t01_sugup_seq
			,		content AS contents';

	#$sql .= '
	#		,		IFNULL(b.contents,c.content) AS content';
	#$sql .= '
	#		,		b.content';

	$sql .= '
			FROM	t01iljung';

	/*
		$sql .= '
			INNER	JOIN	care_suga AS a
					ON		a.org_no	= t01_ccode
					AND		a.suga_sr	= t01_mkind
					AND		CONCAT(a.suga_cd,a.suga_sub) = t01_suga_code1';
	 */
	$sql .= '
			INNER	JOIN (
						SELECT	org_no, suga_sr, suga_cd, suga_sub, suga_nm, from_dt, to_dt
						FROM	care_suga
						WHERE	org_no	= \''.$orgNo.'\'
						AND		suga_sr = \''.$SR.'\'
						UNION	ALL
						SELECT	\''.$orgNo.'\', \''.$SR.'\', LEFT(code,5), MID(code,6), name, from_dt, to_dt
						FROM	care_suga_comm
					) AS a
					ON		a.org_no	= t01_ccode
					AND		a.suga_sr	= t01_mkind
					AND		CONCAT(a.suga_cd,a.suga_sub)	 = t01_suga_code1
					AND		REPLACE(a.from_dt,	\'-\',\'\') <= t01_sugup_date
					AND		REPLACE(a.to_dt,	\'-\',\'\') >= t01_sugup_date';

	$sql .= '
			
			LEFT	JOIN	care_result AS c
					ON		c.org_no	= t01_ccode
					AND		c.org_type	= t01_mkind
					AND		c.jumin		= t01_jumin
					AND		c.date		= t01_sugup_date
					AND		c.time		= t01_sugup_fmtime
					AND		c.seq		= t01_sugup_seq
					AND		c.no		= \'1\'';

	#$sql .= '
	#		LEFT	JOIN	care_result AS b
	#				ON		b.org_no	= t01_ccode
	#				AND		b.org_type	= t01_mkind
	#				AND		b.jumin		= t01_jumin
	#				AND		b.date		= t01_sugup_date
	#				AND		b.time		= t01_sugup_fmtime
	#				AND		b.seq		= t01_sugup_seq
	#				AND		b.del_flag	= \'N\'';

	$sql .= '
			WHERE	t01_ccode	= \''.$orgNo.'\'
			AND		t01_mkind	= \''.$SR.'\'
			AND		t01_jumin	= \''.$jumin.'\'
			AND		t01_del_yn	= \'N\'';

	if ($year && $month){
		$sql .= '
			AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'';
	}
	
	if($_POST['align'] == '1'){
		$sql .= '
				ORDER	BY date DESC';
	}else {
		$sql .= '
				ORDER	BY date ASC';
	}
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$y = SubStr($row['date'],0,4);
		$m = SubStr($row['date'],4,2);
		$d = SubStr($row['date'],6,2);

		if (!$data[$y]) $data[$y]['year'] = $y;
		if (!$data[$y][$m]) $data[$y][$m]['month'] = $m;
		if (!$data[$y][$m][$d]) $data[$y][$m][$d]['day'] = $d;

		$data[$y][$m][$d][] = Array(
			'suga'=>$row['suga_nm']
		,	'res'=>$row['res_nm']
		,	'mem'=>$row['mem_nm']
		,	'cont'=>($log[$row['jumin']][$row['mkind']][$row['date']][$row['suga_cd']][$row['res_cd']][$row['mem_cd']]['contents'] != '' ? $log[$row['jumin']][$row['mkind']][$row['date']][$row['suga_cd']][$row['res_cd']][$row['mem_cd']]['contents'] : $row['contents'])
		);

		$data[$y]['rows'] ++;
		$data[$y][$m]['rows'] ++;
		$data[$y][$m][$d]['rows'] ++;

			
	}

	$conn->row_free();

	if (is_array($data)){
		$weekday = array(0=>'<span style="color:RED;">일</span>', 1=>'월', 2=>'화', 3=>'수', 4=>'목', 5=>'금', 6=>'<span style="color:BLUE;">토</span>');

		foreach($data as $y => $ry){
			if (!is_array($ry)) continue;
			foreach($ry as $m => $rm){
				if (!is_array($rm)) continue;
				foreach($rm as $d => $rd){
					if (!is_array($rd)) continue;
					foreach($rd as $i => $r){
						if (!is_array($r)) continue;?>
						<tr><?
							$weekly = $weekday[Date('w',StrToTime($y.'-'.$m.'-'.$d))];
							if ($ry['rows'] > 0){?>
								<td class="center" style="<?=$IsExcel ? 'text-align:center;' : '';?> vertical-align:top; font-weight:bold; padding-top:3px;" rowspan="<?=$ry['rows'];?>"><?=$y;?>년</td><?
								$ry['rows'] = 0;
							}
							if ($rm['rows'] > 0){?>
								<td class="center" style="<?=$IsExcel ? 'text-align:center;' : '';?> vertical-align:top; font-weight:bold; padding-top:3px;" rowspan="<?=$rm['rows'];?>"><?=IntVal($m);?>월</td><?
								$rm['rows'] = 0;
							}
							if ($rd['rows'] > 0){?>
								<td class="center" style="text-align:right; vertical-align:top; font-weight:bold; padding-top:3px; padding-right:5px;" rowspan="<?=$rd['rows'];?>"><?=IntVal($d).'('.$weekly.')';?></td><?
								$rd['rows'] = 0;
							}?>
							<td class="center"><div class="left"><?=$r['suga'];?></div></td>
							<td class="center"><div class="left"><?=$r['res'];?></div></td>
							<td class="center"><div class="left"><?=$r['mem'];?></div></td>
							<td class="center last"><div class="left"><?=$r['cont'];?></div></td>
						</tr><?
					}
				}
			}
		}
	}else{?>
		<tr>
			<td class="center last" colspan="7">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	if (!$IsExcel){
		include_once('../inc/_db_close.php');
	}
?>