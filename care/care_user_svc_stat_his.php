<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$month	= ($month < 10 ? '0' : '').$month;
	$code	= $ed->de($_POST['code']);

	$sql = 'SELECT	suga_cd
			,		res_cd
			,		mem_cd
			,		suga_nm
			,		res_nm
			,		mem_nm
			,		COUNT(*) AS cnt
			,		GROUP_CONCAT(date) AS date
			FROM	(
					SElECT	suga_cd
					,		res_cd
					,		mem_cd
					,		suga_nm
					,		res_nm
					,		mem_nm
					,		CONCAT(date, \'(\', CASE weekday	WHEN \'0\' THEN \'월\'
																WHEN \'1\' THEN \'화\'
																WHEN \'2\' THEN \'수\'
																WHEN \'3\' THEN \'목\'
																WHEN \'4\' THEN \'금\'
																WHEN \'5\' THEN \'<span style="color:BLUE;">토</span>\'
																WHEN \'6\' THEN \'<span style="color:RED;">일</span>\' ELSE weekday END, \')\') AS date
					FROM	(
							SELECT	CAST(RIGHT(t01_sugup_date, 2) AS unsigned) AS date
							,		WEEKDAY(DATE_FORMAT(t01_sugup_date, \'%Y-%m-%d\')) AS weekday
							,		t01_suga_code1 AS suga_cd
							,		TRIM(a.suga_nm) AS suga_nm
							,		t01_yoyangsa_id1 AS res_cd
							,		t01_yname1 AS res_nm
							,		t01_yoyangsa_id2 AS mem_cd
							,		t01_yname2 AS mem_nm
							FROM	t01iljung';

		/*
			$sql .= '	INNER	JOIN	care_suga AS a
								ON		a.org_no = t01_ccode
								AND		a.suga_sr = t01_mkind
								AND		CONCAT(a.suga_cd, a.suga_sub) = t01_suga_code1
								AND		DATE_FORMAT(a.from_dt, \'%Y%m%d\') <= t01_sugup_date
								AND		DATE_FORMAT(a.to_dt, \'%Y%m%d\') >= t01_sugup_date';
		 */
		$sql .= '		INNER	JOIN (
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

		$sql .= '			WHERE	t01_ccode		= \''.$orgNo.'\'
							AND		t01_mkind		= \''.$SR.'\'
							AND		t01_jumin		= \''.$code.'\'
							AND		t01_sugup_date >= \''.$year.$month.'01\'
							AND		t01_sugup_date <= \''.$year.$month.'31\'
							AND		t01_status_gbn	= \'1\'
							AND		t01_del_yn		= \'N\'
							ORDER	BY t01_sugup_date
							) AS a
					) AS a
			GROUP	BY suga_cd, res_cd, mem_cd
			ORDER	BY suga_nm, res_nm, mem_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$key1 = $row['suga_cd'];
		$key2 = $row['res_cd'];

		if (!$data[$key1]){
			 $data[$key1]['name'] = $row['suga_nm'];
		}

		if (!$data[$key1][$key2]){
			 $data[$key1][$key2]['name'] = $row['res_nm'];
		}

		$data[$key1][$key2]['list'][] = Array(
			'name'	=>$row['mem_nm']
		,	'cnt'	=>$row['cnt']
		,	'date'	=>$row['date']
		);

		$data[$key1]['cnt'] ++;
		$data[$key1][$key2]['cnt'] ++;
	}

	$conn->row_free();

	if (is_array($data)){
		$no = 1;
		foreach($data as $suagCd => $suga){
			if (!is_array($suga)) continue;
			if ($no % 2 == 1){
				$bgclr = 'FFFFFF';
			}else{
				$bgclr = 'EAEAEA';
			}?>
			<tr>
			<td class="center" style="background-color:#<?=$bgclr;?>;" rowspan="<?=$suga['cnt'];?>"><?=$no;?></td>
			<td class="center" style="background-color:#<?=$bgclr;?>;" rowspan="<?=$suga['cnt'];?>"><div class="left" style="padding-right:5px;"><?=$suga['name'];?></div></td><?

			$IsFirst[1] = true;

			foreach($suga as $resCd => $res){
				if (!is_array($res)) continue;
				if (!$IsFirst[1]){?>
					<tr><?
				}?>

				<td class="center" style="background-color:#<?=$bgclr;?>;" rowspan="<?=$res['cnt'];?>"><div class="left" style="padding-right:5px;"><?=$res['name'];?></div></td><?

				$IsFirst[2] = true;

				foreach($res['list'] as $idx => $row){
					if (!is_array($row)) continue;
					if (!$IsFirst[2]){?>
						<tr><?
					}?>

					<td class="center" style="background-color:#<?=$bgclr;?>;"><div class="left" style="padding-right:5px;"><?=$row['name'];?></div></td>
					<td class="center" style="background-color:#<?=$bgclr;?>;"><div class="right"><?=$row['cnt'];?></div></td>
					<td class="center last" style="background-color:#<?=$bgclr;?>;"><div class="left" style="padding-right:5px;"><?=$row['date'];?></div></td>
					</tr><?

					$IsFirst[2] = false;
				}

				$IsFirst[1] = false;
			}

			$no ++;
		}
	}

	include_once('../inc/_db_close.php');
?>