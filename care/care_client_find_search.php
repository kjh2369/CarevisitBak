<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$name	= $_POST['name'];
	$fromDt	= $_POST['fromDt'];
	$toDt	= $_POST['toDt'];
	$sugaCd	= $_POST['sugaCd'];

	/*
		$sql = 'SELECT	m03_jumin AS code
				,		m03_name AS name
				,		a.mp_gbn
				,		m03_juso1 AS addr
				,		m03_juso2 AS addr_dtl
				,		m03_tel AS phone
				,		m03_hp AS mobile
				,		b.jumin AS jumin
				,		a.from_dt
				,		a.to_dt
				FROM	m03sugupja
				LEFT	JOIN	client_his_svc AS a
						ON		a.org_no	 = m03_ccode
						AND		a.jumin	 = m03_jumin
						AND		a.svc_cd	 = \''.$SR.'\'
						AND		a.from_dt	<= NOW()
						AND		a.to_dt	>= NOW()
				INNER	JOIN mst_jumin AS b
						ON		b.org_no	= m03_ccode
						AND		b.gbn		= \'1\'
						AND		b.code		= m03_jumin
				WHERE	m03_ccode	= \''.$orgNo.'\'
				AND		m03_mkind	= \'6\'
				AND		m03_del_yn	= \'N\'';

		if ($name){
			$sql .= ' AND m03_name = \''.$name.'\'';
		}

		$sql .= '
				ORDER	BY name';
	 */

	$sql = 'SELECT	DISTINCT
					a.jumin AS code
			,		a.name
			,		a.addr
			,		a.addr_dtl
			,		a.phone
			,		a.mobile
			,		b.mp_gbn
			,		b.from_dt
			,		b.to_dt
			,		c.jumin
			FROM	(
					SELECT	DISTINCT
							a.org_no
					,		a.svc_cd
					,		a.jumin
					,		m03_name AS name
					,		m03_juso1 AS addr
					,		m03_juso2 AS addr_dtl
					,		m03_tel AS phone
					,		m03_hp AS mobile
					FROM	client_his_svc AS a
					INNER	JOIN	m03sugupja
							ON		m03_ccode = a.org_no
							AND		m03_mkind = \'6\'
							AND		m03_jumin = a.jumin';

	if ($sugaCd){
		$sql .= '	INNER	JOIN	t01iljung
							ON		t01_ccode = a.org_no
							AND		t01_mkind = a.svc_cd
							AND		t01_jumin = a.jumin
							AND		t01_suga_code1 = \''.$sugaCd.'\'
							AND		t01_del_yn = \'N\'';
		if($fromDt && $toDt){
			$sql .= ' AND		t01_sugup_date BETWEEN REPLACE(\''.$fromDt.'\',\'-\',\'\') AND REPLACE(\''.$toDt.'\',\'-\',\'\')';
		}else {
			$sql .= ' AND		t01_sugup_date BETWEEN REPLACE(a.from_dt,\'-\',\'\') AND REPLACE(a.to_dt,\'-\',\'\')';
		}
	}

	$sql .= '		WHERE	a.org_no = \''.$orgNo.'\'
					AND		a.svc_cd = \''.$SR.'\'
					) AS a
			'.($fromDt && $toDt ? 'INNER' : 'LEFT').'	JOIN	client_his_svc AS b
					ON		b.org_no	 = a.org_no
					AND		b.svc_cd	 = a.svc_cd
					AND		b.jumin		 = a.jumin';

	if ($fromDt && $toDt){
		$sql .= '	AND		CASE WHEN b.from_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' THEN 1 ELSE 0 END + CASE WHEN b.to_dt BETWEEN \''.$fromDt.'\' AND \'9999-12-31\' THEN 1 ELSE 0 END > 0';
		$sql .= '   AND     b.from_dt <= \''.$toDt.'\'';

	}else{
		$sql .= '	AND		b.from_dt	<= NOW()
					AND		b.to_dt		>= NOW()';
	}

	$sql .= '
			INNER	JOIN	mst_jumin AS c
					ON		c.org_no= a.org_no
					AND		c.gbn	= \'1\'
					AND		c.code	= a.jumin';
	if ($name){
		$sql .= ' WHERE a.name = \''.$name.'\'';
	}

	$sql .= '
			ORDER	BY name';

	//if ($debug){
	//	echo '<tr><td colspan="10">'.nl2br($sql).'</td></tr>';
	//}

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$jumin	= SubStr($row['jumin'].'0000000',0,13);
		$birth	= $myF->issToBirthDay($jumin,'.');
		$gender	= $myF->issToGender($jumin);
		$telno	= Trim($myF->phoneStyle($row['phone'],'.').' / '.$myF->phoneStyle($row['mobile'],'.'));

		if (SubStr($telno,0,1) == '/'){
			$telno = SubStr($telno,1);
		}else if (SubStr($telno,StrLen($telno) - 1, 1) == '/'){
			$telno = SubStr($telno,0,StrLen($telno) - 1);
		}

		if ($gender == '남'){
			$gender = '<span style="color:BLUE;">남</span>';
		}else if ($gender == '여'){
			$gender = '<span style="color:RED;">여</span>';
		}

		if ($row['from_dt'] <= Date('Y-m-d') && $row['to_dt'] >= Date('Y-m-d')){
			$gbn = '진행중';
		}else{
			$gbn = '중지';
		}?>
		<tr jumin="<?=$ed->en($row['code']);?>" no="<?=$i?>">
			<td class="center"><input id="chkIn<?=$i;?>" name="chkIn" type="checkbox" class="checkbox" jumin="<?=$ed->en($row['code']);?>"></td>
			<td class="center"><?=$row['name'];?></td>
			<td class="center"><?=$birth;?></td>
			<td class="center"><?=$gender;?></td>
			<td class="center"><?=$row['mp_gbn'];?></td>
			<td class="center"><div class="left nowrap" style="width:200px;"><?=$row['addr'].' '.$row['addr_dtl'];?></div></td>
			<td class="center"><?=$telno;?></td>
			<td class="center last"></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>