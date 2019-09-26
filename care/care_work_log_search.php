<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo		= $_SESSION['userCenterCode'];
	$SR			= $_POST['SR'];
	$sugaCd		= $_POST['sugaCd'];
	$memName	= $_POST['memName'];
	$tgName		= $_POST['tgName'];
	$fromDt		= str_replace('-','',$_POST['fromDt']);
	$toDt		= str_replace('-','',$_POST['toDt']);
	$orderName	= $_POST['orderName'];
	$orderDate	= $_POST['orderDate'];


	//대상자 중점여부
	$sql = 'SELECT	jumin
			,		seq
			,		from_dt
			,		to_dt
			,		mp_gbn
			FROM	client_his_svc
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		svc_cd	 = \''.$SR.'\'
			AND		from_dt <= \''.$fromDt.'\'
			AND		to_dt	>= \''.$toDt.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$mp[$row['jumin']][] = Array(
			'fromDt'=>str_replace('-','',$row['from_dt'])
		,	'toDt'	=>str_replace('-','',$row['to_dt'])
		,	'mpGbn'	=>$row['mp_gbn']
		);
	}

	$conn->row_free();


	//서비스
	$sql = 'SElECT	a.jumin AS mem_cd
			,		b.name AS mem_nm
			,		a.target AS tg_cd
			,		c.name AS tg_nm
			,		e.jumin AS tg_jumin
			,		a.date
			,		d.sub_key
			FROM	(
						SELECT	DISTINCT
								t01_yoyangsa_id2 AS jumin
						,		t01_jumin AS target
						,		t01_sugup_date AS date
						FROM	t01iljung
						WHERE	t01_ccode		= \''.$orgNo.'\'
						AND		t01_mkind		= \''.$SR.'\'
						AND		t01_suga_code1	= \''.$sugaCd.'\'
						AND		t01_del_yn		= \'N\'
						AND		t01_sugup_date >= \''.$fromDt.'\'
						AND		t01_sugup_date <= \''.$toDt.'\'
					) AS a
			INNER	JOIN (
						SELECT	DISTINCT
								m02_yjumin AS jumin
						,		m02_yname AS name
						FROM	m02yoyangsa
						WHERE	m02_ccode = \''.$orgNo.'\'
					) AS b
					ON	b.jumin = a.jumin
			INNER	JOIN (
						SELECT	m03_jumin AS jumin
						,		m03_name AS name
						FROM	m03sugupja
						WHERE	m03_ccode = \''.$orgNo.'\'
						AND		m03_mkind = \'6\'
					) AS c
					ON	c.jumin = a.target
			INNER	JOIN	mst_jumin AS e
					ON		e.org_no= \''.$orgNo.'\'
					AND		e.gbn	= \'1\'
					AND		e.code	= a.target
			LEFT	JOIN (
						SELECT	mem_cd
						,		jumin
						,		date
						,		sub_key
						FROM	care_work_log
						WHERE	org_no	 = \''.$orgNo.'\'
						AND		org_type = \''.$SR.'\'
						AND		suga_cd	 = \''.$sugaCd.'\'
						AND		date	>= \''.$fromDt.'\'
						AND		date	<= \''.$toDt.'\'
						AND		del_flag = \'N\'
					) AS d
					ON		d.mem_cd= a.jumin
					AND		d.jumin	= a.target
					AND		d.date	= a.date
			ORDER	BY ';

	if ($orderName == '1'){
		$sql .= 'mem_nm, tg_nm';
	}else{
		$sql .= 'tg_nm, mem_nm';
	}

	$sql .= ', date';

	if ($orderDate == '1'){
		$sql .= ' ASC';
	}else{
		$sql .= ' DESC';
	}

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row	= $conn->select_row($i);
		$memCd	= $row['mem_cd'];
		$tgCd	= $row['tg_cd'];

		if (!$data[$memCd]){
			 $data[$memCd] = Array(
				'name'		=>$row['mem_nm']
			,	'birthday'	=>$myF->issToBirthday($row['mem_cd'],'.')
			,	'gender'	=>$myF->issToGender($row['mem_cd'])
			,	'cnt'		=>0
			);
		}

		if (!$data[$memCd]['target'][$tgCd]){
			if (is_array($mp[$tgCd])){
				foreach($mp[$tgCd] as $idx => $mpRow){
					if ($mpRow['fromDt'] <= $row['date'] && $mpRow['toDt'] >= $row['date']){
						$mpGbn = $mpRow['mpGbn'];
					}
				}
			}else{
				$mpGbn = '';
			}

			$data[$memCd]['target'][$tgCd] = Array(
				'name'		=>$row['tg_nm']
			,	'birthday'	=>$myF->issToBirthday($row['tg_jumin'],'.')
			,	'gender'	=>$myF->issToGender($row['tg_jumin'])
			,	'mpGbn'		=>$mpGbn == 'Y' ? 'Y' : ''
			,	'cnt'		=>0
			);
		}

		$data[$memCd]['cnt'] ++;
		$data[$memCd]['target'][$tgCd]['cnt'] ++;

		$data[$memCd]['target'][$tgCd]['iljung'][] = Array(
			'date'	=>$row['date']
		,	'key'	=>$row['sub_key']
		);
	}

	$conn->row_free();

	Unset($mp);

	if (!is_array($data)){?>
		<tr>
			<td class="center last" colspan="11">::검색된 데이타가 없습니다.::</td>
		</tr><?
		$conn->close();
		exit;
	}

	$no = 1;

	foreach($data as $memCd => $memRow){
		if (!is_array($memRow)) continue;

		if ($no % 2 == 1){
			$bgcolor = 'FFFFFF';
		}else{
			$bgcolor = 'EAEAEA';
		}

		$style = 'background-color:#'.$bgcolor.';';
		$seq = 1;

		foreach($memRow['target'] as $tgCd => $tgRow){
			if (!is_array($tgRow)) continue;

			foreach($tgRow['iljung'] as $idx => $row){
				if (!is_array($row)) continue;?>
				<tr><?
				if ($memRow['cnt'] > 0){?>
					<td class="center top" style="<?=$style;?>" rowspan="<?=$memRow['cnt']?>"><?=$no;?></td>
					<td class="center top" style="<?=$style;?>" rowspan="<?=$memRow['cnt']?>"><?=$memRow['name'];?></td>
					<td class="center top" style="<?=$style;?>" rowspan="<?=$memRow['cnt']?>"><?=$memRow['birthday'];?></td>
					<td class="center top" style="<?=$style;?>" rowspan="<?=$memRow['cnt']?>"><?=$memRow['gender'];?></td><?
					$memRow['cnt'] = 0;
				}

				if ($tgRow['cnt'] > 0){?>
					<td class="center top" style="<?=$style;?>" rowspan="<?=$tgRow['cnt']?>"><?=$tgRow['name'];?></td>
					<td class="center top" style="<?=$style;?>" rowspan="<?=$tgRow['cnt']?>"><?=$tgRow['birthday'];?></td>
					<td class="center top" style="<?=$style;?>" rowspan="<?=$tgRow['cnt']?>"><?=$tgRow['gender'];?></td>
					<td class="center top" style="<?=$style;?>" rowspan="<?=$tgRow['cnt']?>"><?=$tgRow['mgGbn'];?></td><?
					$tgRow['cnt'] = 0;
				}?>
				<td class="center" style="<?=$style;?>"><?=$myF->dateStyle($row['date'],'.');?></td>
				<td class="center" style="<?=$style;?>" id="lblRegYn"><?=$row['key'] > 0 ? '<span style="color:BLUE;">Y</span>' : '<span style="color:RED;">N</span>';?></td>
				<td class="center last" style="<?=$style;?>">
					<div class="left">
						<span class="btn_pack m"><span class="add"></span><button id="ID_<?=$no;?>_<?=$seq;?>_<?=$row['date'];?>" onclick="lfLogReg(this);" jumin="<?=$ed->en($memCd);?>" target="<?=$ed->en($tgCd);?>" date="<?=$row['date'];?>" key="<?=$row['key'];?>"><?=$row['key'] > 0 ? '수정' : '작성';?></button></span><?
						if ($row['key'] > 0){?>
							<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel('<?=$ed->en($memCd);?>','<?=$ed->en($tgCd);?>','<?=$row['date'];?>');">엑셀</button></span><?
						}?>
					</div>
				</td>
				</tr><?
			}

			$seq ++;
		}

		$no ++;
	}

	include_once('../inc/_db_close.php');
?>