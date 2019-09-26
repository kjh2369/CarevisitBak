<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$jumin	= $ed->de($_POST['jumin']);

	/*
		$sql = 'SELECT	CAST(RIGHT(t01_sugup_date,2) AS unsigned) AS day
				,		CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_fmtime ElSE t01_sugup_fmtime END AS from_time
				,		CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_totime ELSE t01_sugup_totime END AS to_time
				,		t01_yname1 AS mem_nm
				FROM	t01iljung
				WHERE	t01_ccode		= \''.$orgNo.'\'
				AND		t01_mkind		= \'0\'
				AND		t01_jumin		= \''.$jumin.'\'
				AND		t01_svc_subcode = \'200\'
				AND		t01_del_yn		= \'N\'
				AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'';
	 */
	$sql = 'SELECT	CAST(RIGHT(t01_sugup_date,2) AS unsigned) AS day
			,		CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_fmtime ElSE t01_sugup_fmtime END AS from_time
			,		CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_totime ELSE t01_sugup_totime END AS to_time
			,		t01_yname1 AS mem_nm
			,		t01_yname2 AS mem_nm2
			,		t01_mkind AS svc_cd
			FROM	t01iljung
			WHERE	t01_ccode	= \''.$orgNo.'\'
			AND		t01_jumin	= \''.$jumin.'\'
			AND		t01_del_yn	= \'N\'
			AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
			AND		CASE WHEN t01_mkind = \'0\' AND t01_svc_subcode = \'200\' THEN 1
						 WHEN t01_mkind = \'5\' THEN 1 ELSE 0 END = 1
			UNION   ALL
			SELECT	CAST(RIGHT(t01_sugup_date,2) AS unsigned) AS day
			,		CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_fmtime ElSE t01_sugup_fmtime END AS from_time
			,		CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_totime ELSE t01_sugup_totime END AS to_time
			,		t01_yname1 AS mem_nm
			,		t01_yname2 AS mem_nm2
			,		t01_mkind AS svc_cd
			FROM	t01iljung
			WHERE	t01_ccode	= \''.$orgNo.'\'
			AND		t01_jumin	= \''.$jumin.'\'
			AND		t01_del_yn	= \'N\'
			AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
			AND		t01_mkind = \'0\' 
			AND		t01_svc_subcode = \'500\'
			ORDER	BY day, from_time, to_time';

	//if ($debug) echo '<tr><td colspan="4">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['svc_cd'] == '5'){
			$memNm = '<span style="color:BLUE;">주야간</span>';
		}else{
			$memNm = $row['mem_nm'];
			$memNm2 = $row['mem_nm2'] != '' ? '</br>'.$row['mem_nm2'] : '';
		}?>
		<tr>
			<td class="center"><?=$row['day'];?>일</td>
			<td class="center"><?=$myF->timeStyle($row['from_time']);?></td>
			<td class="center"><?=$myF->timeStyle($row['to_time']);?></td>
			<td class="center"><?=$memNm;?><?=$memNm2;?></td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>