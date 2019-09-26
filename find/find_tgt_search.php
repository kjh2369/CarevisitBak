<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$name	= $_POST['name'];
	$useGbn	= $_POST['useGbn'];
	$date	= str_replace('-','',$_POST['date']);
	$yymm	= SubStr($date, 0, 6);

	$svcCd = Array('homecare'=>'0','nurse'=>'1', 'old'=>'2', 'baby'=>'3', 'dis'=>'4', 'careSupport'=>'S', 'careResource'=>'R');
	$svcCd = $svcCd[$_POST['svcCd']];

	/*
		$sql = 'SELECT	DISTINCT a.jumin, m03_name AS name
				,		a.svc_cd, m03_tel AS phone, m03_hp AS mobile, m03_post_no AS postno, m03_juso1 AS addr, m03_juso2 AS addr_dtl
				,		CASE a.svc_cd WHEN \'0\' THEN \'재가요양\' WHEN \'1\' THEN \'가사간병\' WHEN \'2\' THEN \'노인돌봄\' WHEN \'3\' THEN \'산모신생아\' WHEN \'4\' THEN \'장애인활동지원\' WHEN \'S\' THEN \'재가지원\' WHEN \'R\' THEN \'자원연계\' ELSE a.svc_cd END AS svc_nm
				,		CASE WHEN DATE_FORMAT(NOW(),\'%Y-%m-%d\') BETWEEN a.from_dt AND a.to_dt THEN 1
							 WHEN CONCAT(a.svc_cd,\'_\',a.seq) = (SELECT CONCAT(svc_cd,\'_\',seq) FROM client_his_svc WHERE org_no = a.org_no AND jumin = a.jumin ORDER BY from_dt DESC LIMIT 1) THEN 2 ELSE 9 END AS gbn
				FROM	client_his_svc AS a
				INNER	JOIN	m03sugupja
						ON		m03_ccode = a.org_no
						AND		m03_jumin = a.jumin
						AND		m03_del_yn= \'N\'
				WHERE	a.org_no = \''.$orgNo.'\'';

		if ($name) $sql .= ' AND m03_name LIKE \'%'.$name.'%\'';
		if ($svcCd) $sql .= ' AND a.svc_cd = \''.$svcCd.'\'';

		$sql .= '
				ORDER	BY name, jumin, gbn';
	*/
	$sql = 'SELECT	*
			FROM	(
					SELECT	a.jumin, a.svc_cd, m03_name AS name, m03_tel AS phone, m03_hp AS mobile, m03_post_no AS postno, m03_juso1 AS addr, m03_juso2 AS addr_dtl
					,		CASE a.svc_cd WHEN \'0\' THEN \'재가요양\'
										  WHEN \'1\' THEN \'가사간병\'
										  WHEN \'2\' THEN \'노인돌봄\'
										  WHEN \'3\' THEN \'산모신생아\'
										  WHEN \'4\' THEN \'장애인활동지원\'
										  WHEN \'S\' THEN \'재가지원\'
										  WHEN \'R\' THEN \'자원연계\' ELSE a.svc_cd END AS svc_nm
					FROM	client_his_svc AS a
					INNER	JOIN	m03sugupja
							ON		m03_ccode = a.org_no
							AND		m03_mkind = CASE WHEN a.svc_cd = \'S\' OR a.svc_cd = \'R\' THEN \'6\' ELSE a.svc_cd END
							AND		m03_jumin = a.jumin
					WHERE	a.org_no = \''.$orgNo.'\'
					AND		a.svc_cd != \'3\'
					AND		DATE_FORMAT(a.from_dt,\'%Y%m\') <= \''.$yymm.'\'
					AND		DATE_FORMAT(a.to_dt,\'%Y%m\') >= \''.$yymm.'\'
					UNION	ALL
					SELECT	DISTINCT jumin, \'3\', name, phone, mobile, postno_s, addr_s, addr_dtl_s, \'산모신생아\'
					FROM	vuc_baby_due
					WHERE	org_no = \''.$orgNo.'\'
					AND		del_flag = \'N\'
					/*AND	DATE_FORMAT(svc_from_dt,\'%Y%m\') <= \''.$yymm.'\'
					AND		DATE_FORMAT(svc_to_dt,\'%Y%m\') >= \''.$yymm.'\'*/
					) AS a
			WHERE	jumin != \'\'';

	if ($name) $sql .= ' AND name LIKE \'%'.$name.'%\'';
	if ($svcCd != '') $sql .= ' AND svc_cd = \''.$svcCd.'\'';

	//if ($name) $sql .= ' AND m03_name LIKE \'%'.$name.'%\'';
	//if ($svcCd != '') $sql .= ' AND a.svc_cd = \''.$svcCd.'\'';

	$sql .= '
			ORDER	BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	//if ($debug) echo '<tr><td colspan="10">'.$svcCd.'<br>'.nl2br($sql).'</td></tr>';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$row['addr'] = Explode(chr(13).chr(10),$row['addr']);?>
		<tr name="<?=$row['name'];?>" jumin="<?=$ed->en($row['jumin']);?>" svcCd="<?=$row['svc_cd'];?>" phone="<?=$row['phone'];?>" mobile="<?=$row['mobile'];?>" postno="<?=$row['postno'];?>" addr1="<?=$row['addr'][0];?>" addr2="<?=$row['addr'][1];?>" addrDtl="<?=$row['addr_dtl'];?>">
			<td class="center"><?=$no;?></td>
			<td class="center"><a href="#" onclick="lfSelTgt(this);"><?=$row['name'];?></a></td>
			<td class="center"><?=is_numeric($row['jumin']) ? $myF->issToBirthday($row['jumin'],'.') : '';?></td>
			<td class="center"><div class="left nowrap" style="width:300px;"><?=$row['svc_nm'];?></div></td>
			<td class="center"></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>