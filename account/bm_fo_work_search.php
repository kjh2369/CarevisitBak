<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	if ($_SESSION['userLevel'] == 'A'){
		$orgNo	= $_POST['orgNo'];
	}else{
		$orgNo	= $_SESSION['userCenterCode'];
	}

	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	$sql = 'SELECT	a.*, GROUP_CONCAT(DISTINCT m03_name) AS name
			FROM	(
					SELECT	a.cf_mem_cd AS mem_cd, a.cf_mem_nm AS mem_nm, a.cf_jumin AS jumin
					,		COUNT(DISTINCT b.jumin) AS tg_cnt
					,		COUNT(DISTINCT b.conf_jumin) AS conf_cnt
					,		ROUND(SUM(CASE WHEN b.sub_cd = \'200\' AND b.family_yn = \'Y\' THEN b.proc_time ELSE 0 END) / 60, 1) AS family_time
					,		ROUND(SUM(CASE WHEN b.sub_cd = \'200\' AND b.family_yn = \'Y\' THEN 0 ELSE b.proc_time END) / 60, 1) AS other_time
					,		ROUND(SUM(CASE WHEN b.stat_gbn = \'1\' AND b.sub_cd = \'200\' AND b.family_yn = \'Y\' THEN b.conf_time ELSE 0 END) / 60, 1) AS conf_f_time
					,		ROUND(SUM(CASE WHEN b.stat_gbn = \'1\' AND b.sub_cd = \'200\' AND b.family_yn = \'Y\' THEN 0 ELSE b.conf_time END) / 60, 1) AS conf_o_time
					FROM	client_family AS a
					INNER	JOIN (
							SELECT	t01_jumin AS jumin, CONCAT(\'/\',t01_yoyangsa_id1, \'/\',t01_yoyangsa_id2) AS mem_cd
							,		t01_svc_subcode AS sub_cd, t01_toge_umu AS family_yn, t01_sugup_soyotime AS proc_time
							,		t01_status_gbn AS stat_gbn, t01_conf_soyotime AS conf_time
							,		CASE WHEN t01_status_gbn = \'1\' THEN t01_jumin ELSE NULL END AS conf_jumin
							FROM	t01iljung';

	if ($gDomain == 'dolvoin.net'){
		$sql .= '			WHERE	t01_ccode LIKE \'dolvoin%\'';
	}else{
		$sql .= '			WHERE	t01_ccode = \''.$orgNo.'\'';
	}

	$sql .= '				/*AND		t01_mkind = \'0\'*/
							AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'
							AND		t01_del_yn = \'N\'
							AND		IFNULL(t01_bipay_umu, \'N\') != \'Y\'
							) AS b
							ON		INSTR(b.mem_cd,CONCAT(\'/\',a.cf_mem_cd)) > 0
					INNER	JOIN	mem_his AS c
							ON		c.org_no = a.org_no
							AND		c.jumin	 = a.cf_mem_cd
							AND		DATE_FORMAT(c.join_dt, \'%Y%m\') <= \''.$yymm.'\'
							AND		DATE_FORMAT(CASE WHEN IFNULL(c.quit_dt, \'\') != \'\' THEN c.quit_dt ELSE \'9999-12-31\' END, \'%Y%m\') >= \''.$yymm.'\'
					WHERE	a.org_no = \''.$orgNo.'\'
					GROUP	BY a.cf_mem_cd, a.cf_jumin
					) AS a
			INNER	JOIN	m03sugupja
					ON		m03_ccode = \''.$orgNo.'\'
					/*AND		m03_mkind = \'0\'*/
					AND		m03_jumin = a.jumin';

	if ($gDomain == 'dolvoin.net'){
	}else{
		$sql .= '
			WHERE	a.family_time + a.conf_f_time > 0';
	}

	$sql .='
			GROUP	BY a.mem_cd
			ORDER	BY mem_nm';

	//if ($debug) echo '<tr><td colspan="10">'.$gDomain.'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="">&nbsp;<?=$row['mem_nm'];?></td>
			<td class="">&nbsp;<?=$row['name'];?></td>
			<td style="text-align:right;"><?=$row['family_time'];?>&nbsp;</td>
			<td style="text-align:right; <?=$row['other_time'] >= 160 ? 'color:BLUE; font-weight:bold;' : '';?>"><?=$row['other_time'];?>&nbsp;</td>
			<td style="text-align:right;"><?=$row['tg_cnt'];?>&nbsp;</td>
			<td style="text-align:right;"><?=$row['conf_f_time'];?>&nbsp;</td>
			<td style="text-align:right; <?=$row['conf_o_time'] >= 160 ? 'color:RED; font-weight:bold;' : '';?>"><?=$row['conf_o_time'];?>&nbsp;</td>
			<td style="text-align:right;"><?=$row['conf_cnt'];?>&nbsp;</td>
			<td class="last"></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>