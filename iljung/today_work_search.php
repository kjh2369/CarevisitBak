<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	//기관
	$orgNo = $_SESSION['userCenterCode'];

	//TODAY
	$today = Date('Ymd');

	//상태
	$stat = Array('1'=>'완료', '5'=>'수행중', '9'=>'대기', '0'=>'미수행');

	//수가
	$sql = 'SELECT	m01_mcode2 AS code
			,		m01_suga_cont AS name
			FROM	m01suga
			WHERE	m01_mcode  = \'goodeos\'
			AND		m01_sdate <= \''.$today.'\'
			AND		m01_edate >= \''.$today.'\'
			UNION	ALL
			SELECT	m11_mcode2 AS code
			,		m11_suga_cont AS name
			FROM	m11suga
			WHERE	m11_mcode  = \'goodeos\'
			AND		m11_sdate <= \''.$today.'\'
			AND		m11_edate >= \''.$today.'\'';

	$suga = $conn->_fetch_array($sql,'code');

	//일정
	$sql = 'SELECT	t01_jumin AS jumin
			,		m03_name AS name
			,		lvl.app_no
			,		t01_sugup_fmtime AS from_time
			,		t01_sugup_totime AS to_time
			,		t01_suga_code1 AS suga_cd
			,		t01_yoyangsa_id1 AS mem_cd1
			,		t01_yoyangsa_id2 AS mem_cd2
			,		t01_yname1 AS mem_nm1
			,		t01_yname2 AS mem_nm2
			,		t01_wrk_fmtime AS work_from
			,		t01_wrk_totime AS work_to
			,		t01_status_gbn AS stat
			,		t01_svc_subcode AS sub_cd
			FROM	t01iljung
			INNER	JOIN	m03sugupja AS mst
					ON		m03_ccode = t01_ccode
					AND		m03_mkind = t01_mkind
					AND		m03_jumin = t01_jumin
			LEFT	JOIN	(
						SELECT	DISTINCT
								jumin
						,		app_no
						FROM	client_his_lvl
						WHERE	org_no	= \''.$orgNo.'\'
						AND		svc_cd	= \'0\'
						AND		from_dt <= \''.$myF->dateStyle($today).'\'
						AND		to_dt	>= \''.$myF->dateStyle($today).'\'
					) AS lvl
					ON		lvl.jumin = t01_jumin
			WHERE	t01_ccode		= \''.$orgNo.'\'
			AND		t01_mkind		= \'0\'
			AND		t01_sugup_date	= \''.$today.'\'
			AND		t01_del_yn		= \'N\'
			ORDER	BY from_time, to_time, name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($i % 2 == 0){
			$bgcolor = 'FFFFFF';
		}else{
			$bgcolor = 'EFEFEF';
		}

		if ($row['sub_cd'] == '200'){
			$subCd = '001';
		}else if ($row['sub_cd'] == '500'){
			$subCd = '002';
		}else{
			$subCd = '003';
		}

		$seq[$row['jumin']][$row['sub_cd']] ++;

		$workTime = $myF->min2time($myF->time2min($row['work_to']) - $myF->time2min($row['work_from']));?>
		<tr style="cursor:default; background-color:#<?=$bgcolor;?>;" onmouseover="this.style.backgroundColor='#D9E5FF';" onmouseout="this.style.backgroundColor='#<?=$bgcolor;?>';" seq="<?=$seq[$row['jumin']][$row['sub_cd']];?>" subCd="<?=$subCd;?>">
			<td class="center"><?=$myF->timeStyle($row['from_time']);?></td>
			<td class="center"><?=$myF->timeStyle($row['to_time']);?></td>
			<td class="center"><?=$row['name'];?></td>
			<td class="center"><div class="left"><?=$row['app_no'];?></div></td>
			<td class="center"><div class="left"><?=$suga[$row['suga_cd']]['name'];?></div></td>
			<td class="center"><div class="left"><?=$row['mem_nm1'].($row['mem_nm2'] ? ' / '.$row['mem_nm2'] : '');?></div></td>
			<td class="center"><?=$myF->timeStyle($row['work_from']);?></td>
			<td class="center"><?=$myF->timeStyle($row['work_to']);?></td>
			<td class="center"><?=($workTime > 0 ? $workTime : '');?></td>
			<td class="center"></td>
			<td class="center"></td>
			<td class="center"></td>
			<td class="center"><?=$stat[$row['stat']];?></td>
			<td class="left last"></td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>