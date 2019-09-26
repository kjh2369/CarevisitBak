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

	$sql = 'SELECT	a.jumin
			,		m03_name AS name
			,		a.suga_cnt
			,		a.iljung_cnt
			,		a.jumin AS real_jumin
			,		a.mp_gbn
			,		a.stat
			,		m03_juso1 AS addr
			,		m03_juso2 AS addr_dtl
			,		m03_tel AS phone
			,		m03_hp AS mobile
			FROM	(
					SELECT	a.org_no
					,		a.svc_cd
					,		a.jumin
					,		COUNT(DISTINCT a.suga_cd) AS suga_cnt
					,		SUM(a.iljung_cnt) AS iljung_cnt
					,		c.mp_gbn
					,		IFNULL(c.svc_stat, \'7\') AS stat
					FROM	(
							SELECT	t01_ccode AS org_no
							,		t01_mkind AS svc_cd
							,		t01_jumin AS jumin
							,		t01_sugup_date AS date
							,		t01_suga_code1 AS suga_cd
							,		COUNT(t01_suga_code1) AS iljung_cnt
							FROM	t01iljung
							WHERE	t01_ccode		= \''.$orgNo.'\'
							AND		t01_mkind		= \''.$SR.'\'
							AND		t01_sugup_date >= \''.$year.$month.'01\'
							AND		t01_sugup_date <= \''.$year.$month.'31\'
							AND		t01_status_gbn	= \'1\'
							AND		t01_del_yn		= \'N\'
							GROUP	BY t01_ccode, t01_mkind, t01_jumin, t01_sugup_date, t01_suga_code1
							) AS a
					LEFT	JOIN	client_his_svc AS c
							ON		c.org_no	= a.org_no
							AND		c.svc_cd	= a.svc_cd
							AND		c.jumin		= a.jumin
							AND		DATE_FORMAT(c.from_dt,	\'%Y%m%d\') <= a.date
							AND		DATE_FORMAT(c.to_dt,	\'%Y%m%d\') >= a.date
					GROUP	BY a.org_no, a.svc_cd, a.jumin
					) AS a
			INNER	JOIN	m03sugupja
					ON		m03_ccode	= a.org_no
					AND		m03_mkind	= \'6\'
					AND		m03_jumin	= a.jumin
			LEFT	JOIN	mst_jumin AS b
					ON		b.org_no	= a.org_no
					AND		b.code		= a.jumin
			ORDER	BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$cls = 'center';
		$jumin = SubStr($row['real_jumin'].'0000000',0,13);
		$gender = $myF->issToGender($jumin);

		if ($gender == '남'){
			$gender = '<span style="color:BLUE;">남</span>';
		}else if ($gender == '여'){
			$gender = '<span style="color:RED;">여</span>';
		}

		if ($row['stat'] == '1'){
			$stat = '<span style="color:BLACK;">이용</span>';
		}else if ($row['stat'] == '7'){
			$stat = '<span style="color:BLUE;">미등록</span>';
		}else{
			$stat = '<span style="color:RED;">중지</span>';
		}

		if ($rowCnt > 8 && $i == $rowCnt - 1){
			$cls .= ' bottom';
		}?>
		<tr code="<?=$ed->en($row['jumin']);?>">
			<td class="<?=$cls;?>"><?=$no;?></td>
			<td class="<?=$cls;?>"><div class="left nowrap" style="width:70px;"><?=$row['name'];?></div></td>
			<td class="<?=$cls;?>"><?=$myF->issToBirthDay($jumin,'.');?></td>
			<td class="<?=$cls;?>"><?=$gender;?></td>
			<td class="<?=$cls;?>"><?=$stat;?></td>
			<td class="<?=$cls;?>"><?=$row['mp_gbn'] == 'Y' ? 'Y' : '';?></td>
			<td class="<?=$cls;?>"><div class="left nowrap" style="width:150px;"><?=$row['addr'];?><?=$row['addr_dtl'];?></div></td>
			<td class="<?=$cls;?>"><div class="left nowrap" style="width:150px;"><?=$myF->phoneStyle($row['phone'],'.');?><?=$row['phone'] && $row['moblie'] ? '/' : '';?><?=$myF->phoneStyle($row['moblie'],'.');?></div></td>
			<td class="<?=$cls;?>"><div class="right"><?=$row['suga_cnt'];?></div></td>
			<td class="<?=$cls;?>"><div class="right"><?=$row['iljung_cnt'];?></div></td>
			<td class="<?=$cls;?>"></div></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>