<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	//기관
	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$lastday= $myF->lastday($year,$month);

	//가족관계
	$sql = 'SELECT	cf_jumin AS jumin
			,		cf_seq AS seq
			,		cf_mem_cd AS mem_cd
			,		cf_kind AS gbn
			FROM	client_family
			WHERE	org_no = \''.$orgNo.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$familyRel[$row['jumin']][$row['mem_cd']] = $row['gbn'];
	}

	$conn->row_free();

	//등급
	$sql = 'SELECT	jumin
			,		level AS lvl
			,		app_no
			FROM	client_his_lvl
			WHERE	org_no = \''.$orgNo.'\'
			AND		svc_cd = \'0\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$year.$month.'\'';

	$lvl = $conn->_fetch_array($sql,'jumin');

	//구분
	//3:기초 2"의료 4:경감 1:일반
	$sql = 'SELECT	jumin
			,		kind
			FROM	client_his_kind
			WHERE	org_no = \''.$orgNo.'\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$year.$month.'\'';

	$kind = $conn->_fetch_array($sql,'jumin');


	/*
		$sql = 'SELECT	m03_jumin AS jumin
				,		m03_name AS name
				,		t01_sugup_date AS date
				,		t01_sugup_fmtime AS from_time
				,		t01_sugup_totime AS to_time
				,		t01_sugup_seq AS seq
				,		t01_svc_subcode AS sub_cd
				,		t01_mem_cd1 AS mem_cd1
				,		t01_mem_cd2 AS mem_cd2
				,		t01_mem_nm1 AS mem_nm1
				,		t01_mem_nm2 AS mem_nm2
				,		t01_suga_code1 AS suga_cd
				FROM	m03sugupja
				INNER	JOIN	client_his_svc AS svc
						ON		svc.org_no	= m03_ccode
						AND		svc.svc_cd	= m03_mkind
						AND		svc.jumin	= m03_jumin
						AND		DATE_FORMAT(svc.from_dt,\'%Y%m\') <= \''.$year.$month.'\'
						AND		DATE_FORMAT(svc.to_dt,	\'%Y%m\') >= \''.$year.$month.'\'
				INNER	JOIN	t01iljung
						ON		t01_ccode = m03_ccode
						AND		t01_mkind = m03_mkind
						AND		t01_jumin = m03_jumin
						AND		t01_sugup_date >= \''.$year.$month.'01\'
						AND		t01_sugup_date <= \''.$year.$month.'31\'
						AND		t01_bipay_umu != \'Y\'
						AND		t01_del_yn = \'N\'
				WHERE	m03_ccode = \''.$orgNo.'\'
				AND		m03_mkind = \'0\'
				AND		m03_del_yn= \'N\'
				ORDER	BY name';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$jumin = $row['jumin'];

			if (!$data[$jumin]){
				if ($lvl[$jumin]['lvl']){
					$lvlnm = $lvl[$jumin]['lvl'].'등급';
				}else{
					$lvlnm = '-';
				}

				//인정번호
				$appNo = $lvl[$jumin]['app_no'];

				if ($kind[$jumin]['kind']){
					switch($kind[$jumin]['kind']){
						case '3': $kindnm = '기초'; break;
						case '2': $kindnm = '의료'; break;
						case '4': $kindnm = '경감'; break;
						case '1': $kindnm = '일반'; break;
						default: $kindnm = '-';
					};
				}else{
					$kindnm = '';
				}

				$data[$jumin]['name'] = $row['name'];
				$data[$jumin]['lvl'] = $lvlnm;
				$data[$jumin]['kind'] = $kindnm;
				$data[$jumin]['appNo'] = $appNo;
			}

			$day = IntVal(Date('d', StrToTime($row['date'])));
			$idx = SizeOf($data[$jumin]['cv'][$day]);

			if ($row['from_time'] && $row['to_time']){
				$para	= '?memCd1='.$ed->en($row['mem_cd1'])
						. '&memCd2='.$ed->en($row['mem_cd2'])
						. '&memNm1='.$row['mem_nm1']
						. '&memNm2='.$row['mem_nm2']
						. '&familyRel1='.$familyRel[$jumin][$row['mem_cd1']]
						. '&familyRel2='.$familyRel[$jumin][$row['mem_cd2']]
						. '&sugaCd='.$row['suga_cd']
						. '&from='.$row['from_time']
						. '&to='.$row['to_time']
						. '&seq='.$row['seq']
						. '&subCd='.$row['sub_cd'];
				$data[$jumin]['cv'][$day][$idx] = $para;
			}
		}

		$conn->row_free();
	 */


	//리스트
	$sql = 'SELECT	DISTINCT m03_jumin AS jumin, m03_name AS name
			FROM	m03sugupja
			INNER	JOIN	client_his_svc AS svc
					ON		svc.org_no	= m03_ccode
					AND		svc.svc_cd	= m03_mkind
					AND		svc.jumin	= m03_jumin
					AND		DATE_FORMAT(svc.from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					AND		DATE_FORMAT(svc.to_dt,	\'%Y%m\') >= \''.$year.$month.'\'
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \'0\'
			AND		m03_del_yn= \'N\'
			ORDER	BY name';

	$r = $conn->_fetch_array($sql);
	$rCnt = SizeOf($r);

	for($i=0; $i<$rCnt; $i++){
		$jumin = $r[$i]['jumin'];

		$sql = 'SELECT	t01_sugup_date AS date
				,		t01_sugup_fmtime AS from_time
				,		t01_sugup_totime AS to_time
				,		t01_sugup_seq AS seq
				,		t01_svc_subcode AS sub_cd
				,		t01_mem_cd1 AS mem_cd1
				,		t01_mem_cd2 AS mem_cd2
				,		t01_mem_nm1 AS mem_nm1
				,		t01_mem_nm2 AS mem_nm2
				,		t01_suga_code1 AS suga_cd
				FROM	t01iljung
				WHERE	t01_ccode = \''.$orgNo.'\'
				AND		t01_mkind = \'0\'
				AND		t01_jumin = \''.$jumin.'\'
				AND		t01_sugup_date >= \''.$year.$month.'01\'
				AND		t01_sugup_date <= \''.$year.$month.'31\'
				AND		IFNULL(t01_bipay_umu, \'N\') != \'Y\'
				AND		t01_del_yn = \'N\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($j=0; $j<$rowCnt; $j++){
			$row = $conn->select_row($j);

			if (!$data[$jumin]){
				if ($lvl[$jumin]['lvl']){
					$lvlnm = $lvl[$jumin]['lvl'].'등급';
				}else{
					$lvlnm = '-';
				}

				//인정번호
				$appNo = $lvl[$jumin]['app_no'];

				if ($kind[$jumin]['kind']){
					switch($kind[$jumin]['kind']){
						case '3': $kindnm = '기초'; break;
						case '2': $kindnm = '의료'; break;
						case '4': $kindnm = '경감'; break;
						case '1': $kindnm = '일반'; break;
						default: $kindnm = '-';
					};
				}else{
					$kindnm = '';
				}

				$data[$jumin]['name'] = $r[$i]['name'];
				$data[$jumin]['lvl'] = $lvlnm;
				$data[$jumin]['kind'] = $kindnm;
				$data[$jumin]['appNo'] = $appNo;
			}

			$day = IntVal(Date('d', StrToTime($row['date'])));
			$idx = SizeOf($data[$jumin]['cv'][$day]);

			if ($row['from_time'] && $row['to_time']){
				$para	= '?memCd1='.$ed->en($row['mem_cd1'])
						. '&memCd2='.$ed->en($row['mem_cd2'])
						. '&memNm1='.$row['mem_nm1']
						. '&memNm2='.$row['mem_nm2']
						. '&familyRel1='.$familyRel[$jumin][$row['mem_cd1']]
						. '&familyRel2='.$familyRel[$jumin][$row['mem_cd2']]
						. '&sugaCd='.$row['suga_cd']
						. '&from='.$row['from_time']
						. '&to='.$row['to_time']
						. '&seq='.$row['seq']
						. '&subCd='.$row['sub_cd'];
				$data[$jumin]['cv'][$day][$idx] = $para;
			}
		}

		$conn->row_free();
	}

	if (!is_array($data)){
		exit;
	}

	$no = 1;

	foreach($data as $jumin => $row){
		if ($no % 2 == 1){
			$bgcolor = 'FFFFFF';
			$rowbg1 = 'EBF7FF';
			$rowbg2 = 'FFF2E6';
		}else{
			$bgcolor = 'EFEFEF';
			$rowbg1 = 'D9E5FF';
			$rowbg2 = 'FAE0D4';
		}

		if ($no > 1){?>
			<tr id="LIN_<?=$no;?>"><td class="last" style="height:3px; border-top:2px solid #CCCCCC; border-bottom:2px solid #CCCCCC;" colspan="50"></td></tr><?
		}?>
		<tr id="ROW_<?=$no;?>" style="cursor:default; background-color:#<?=$bgcolor;?>;" backClr="<?=$bgcolor;?>" jumin="<?=$ed->en($jumin);?>" appNo="<?=$row['appNo'];?>" compareYn="N" errorYn="N">
			<td class="center" style="" rowspan="2"><?=$no;?></td>
			<td class="center" style="" rowspan="2">
				<div class="left nowrap" style="width:75px;"><a href="#" onclick="_planReg('','<?=$year;?>','<?=$month;?>','<?=$ed->en($jumin);?>','0','<?=$orgNo;?>',''); return false;"><?=$row['name'];?></a></div>
				<div class="left nowrap" style="width:75px;"><?=$row['appNo'];?></div>
			</td>
			<td class="center" style="" rowspan="2"><?=$row['lvl'];?><br><?=$row['kind'];?></td>
			<td class="center" style="" title="케어비지트"><span style="font-size:7px; line-height:1em;">케어<br>비지트</span></td><?
			for($j=1; $j<=31; $j++){
				if ($j == 31){
					$cls = 'last';
				}else{
					$cls = '';
				}

				$para = '';

				if ($row['cv'][$j]){
					$c = SizeOf($row['cv'][$j]);

					for($i=0; $i<$c; $i++){
						$para .= $row['cv'][$j][$i];
					}

					$cnt[$j] += $c;
				}?>
				<td id="ROW_<?=$no;?>_CV_<?=$j;?>" class="center <?=$cls;?>" style="background-color:#<?=$rowbg1;?>;" para="<?=$para;?>"><?=$cnt[$j];?></td><?
			}?>
		</tr>
		<tr id="ROW_<?=$no;?>" style="cursor:default; background-color:#<?=$bgcolor;?>;" backClr="<?=$bgcolor;?>" errorYn="N">
			<td class="center" style="" title="건보공단"><span style="font-size:7px; line-height:1em;">건보<br>공단</span></td><?
			for($j=1; $j<=31; $j++){
				if ($j == 31){
					$cls = 'last';
				}else{
					$cls = '';
				}?>
				<td id="ROW_<?=$no;?>_LT_<?=$j;?>" class="center <?=$cls;?>" style="background-color:#<?=$rowbg2;?>;" para=""></td><?
			}?>
		</tr><?

		$no ++;

		Unset($cnt);
	}

	include_once('../inc/_db_close.php');
?>