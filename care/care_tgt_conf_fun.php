<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$type	= $_POST['type'];

	if ($type == '1'){
		$fromDt	= str_replace('-','',$_POST['fromDt']);
		$toDt	= str_replace('-','',$_POST['toDt']);

		$sql = 'SELECT	a.jumin AS cd, m03_name AS name, m03_key AS IPIN, b.jumin
				,		GROUP_CONCAT(CONCAT(DATE_FORMAT(a.from_dt,\'%Y.%m.%d\'),\' ~ \',DATE_FORMAT(a.to_dt,\'%Y.%m.%d\')) SEPARATOR \'<br>\') AS period
				FROM	client_his_svc AS a
				INNER	JOIN	m03sugupja
						ON		m03_ccode = a.org_no
						AND		m03_mkind = \'6\'
						AND		m03_jumin = a.jumin
				INNER	JOIN	mst_jumin AS b
						ON		b.org_no= a.org_no
						AND		b.gbn	= \'1\'
						AND		b.code	= a.jumin
				WHERE	a.org_no = \''.$orgNo.'\'
				AND		a.svc_cd = \''.$SR.'\'
				AND		CASE WHEN DATE_FORMAT(a.from_dt,\'%Y%m%d\') BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' THEN 1
							 WHEN DATE_FORMAT(a.to_dt,\'%Y%m%d\') BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' THEN 1
							 WHEN \''.$fromDt.'\' BETWEEN DATE_FORMAT(a.from_dt,\'%Y%m%d\') AND DATE_FORMAT(a.to_dt,\'%Y%m%d\') THEN 1
							 WHEN \''.$toDt.'\' BETWEEN DATE_FORMAT(a.from_dt,\'%Y%m%d\') AND DATE_FORMAT(a.to_dt,\'%Y%m%d\') THEN 1 ELSE 0 END = 1
				GROUP BY a.jumin
				ORDER BY name';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$no = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$gender = SubStr($row['jumin'],6,1);

			if ($gender != 0){
				if ($gender % 2 == 1){
					$gender = '<span style="color:BLUE;">남</span>';
				}else{
					$gender = '<span style="color:RED;">여</span>';
				}
			}else{
				$gender = '';
			}

			?>
			<tr jumin="<?=$ed->en($row['cd']);?>" IPIN="<?=$ed->en($row['IPIN']);?>">
				<td class="center"><?=$no;?></td>
				<td class="center"><div class="left"><?=$row['name'].($gender ? '('.$gender.')' : '');?></div></td>
				<td class="center"><?=$myF->issToBirthday($row['jumin'],'.');?></td>
				<td class="center last"><div class="left"><?=$row['period'];?></div></td>
			</tr><?

			$no ++;
		}

		$conn->row_free();

	}else if ($type == '2'){
		$jumin	= $ed->de($_POST['jumin']);
		$fromDt	= str_replace('-','',$_POST['fromDt']);
		$toDt	= str_replace('-','',$_POST['toDt']);

		$sql = 'SELECT	t01_suga_code1 AS suga_cd, a.suga_nm, t01_sugup_date AS date
				FROM	t01iljung
				INNER	JOIN	care_suga AS a
						ON		a.org_no = t01_ccode
						AND		a.suga_sr = t01_mkind
						AND		CONCAT(a.suga_cd,a.suga_sub) = t01_suga_code1
						AND		DATE_FORMAT(a.from_dt,\'%Y%m%d\') <= t01_sugup_date
						AND		DATE_FORMAT(a.to_dt,\'%Y%m%d\') >= t01_sugup_date
				WHERE	t01_ccode	= \''.$orgNo.'\'
				AND		t01_mkind	= \''.$SR.'\'
				AND		t01_jumin	= \''.$jumin.'\'
				AND		t01_del_yn	= \'N\'
				AND		t01_status_gbn = \'1\'
				AND		t01_sugup_date BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'
				ORDER	BY suga_cd, date';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$c = $row['suga_cd'];
			$y = IntVal(SubStr($row['date'],0,4));
			$m = IntVal(SubStr($row['date'],4,2));
			$d = IntVal(SubStr($row['date'],6,2));

			if (!$data[$c]) $data[$c]['name'] = $row['suga_nm'];

			$data[$c]['LIST'][$y]['LIST'][$m]['LIST'][$d]['value'] ++;
			$data[$c]['LIST'][$y]['LIST'][$m]['value'] ++;
			$data[$c]['LIST'][$y]['value'] ++;
			$data[$c]['value'] ++;

			if ($tmpCYMD != $c.$y.$m.$d){
				$tmpCYMD = $c.$y.$m.$d;
				$data[$c]['LIST'][$y]['LIST'][$m]['rows'] ++;
				$data[$c]['LIST'][$y]['rows'] ++;
				$data[$c]['rows'] ++;
			}
			if ($tmpCYM != $c.$y.$m){
				$tmpCYM = $c.$y.$m;
				$data[$c]['LIST'][$y]['rows'] ++;
				$data[$c]['rows'] ++;
			}
			if ($tmpCY != $c.$y){
				$tmpCY = $c.$y;
				$data[$c]['rows'] ++;
			}
		}

		$conn->row_free();

		if (is_array($data)){
			$weekClr = Array(6=>'0000FF', 0=>'FF0000');
			$weekStr = Array(0=>'일', 1=>'월', 2=>'화', 3=>'수', 4=>'목', 5=>'금', 6=>'토');

			foreach($data as $sugaCd => $R1){?>
				<tr>
				<td class="center top" rowspan="<?=$R1['rows'];?>"><div class="left"><?=$R1['name'];?>(<?=$sugaCd;?>)</div></td><?

				$IsFirst[0] = true;

				foreach($R1['LIST'] as $year => $R2){
					if ($IsFirst[0]){
						$IsFirst[0] = false;
					}else{?>
						<tr><?
					}?>
					<td class="center top" rowspan="<?=$R2['rows'];?>"><div class="right"><?=$year;?>년</div></td><?

					$IsFirst[1] = true;

					foreach($R2['LIST'] as $month => $R3){
						if ($IsFirst[1]){
							$IsFirst[1] = false;
						}else{?>
							<tr><?
						}?>
						<td class="center top" rowspan="<?=$R3['rows'];?>"><div class="right"><?=$month;?>월</div></td><?

						$IsFirst[2] = true;

						foreach($R3['LIST'] as $day => $R4){
							$w = Date('w',StrToTime($year.($month < 10 ? '0' : '').$month.($day < 10 ? '0' : '').$day));

							if ($IsFirst[2]){
								$IsFirst[2] = false;
							}else{?>
								<tr><?
							}?>
							<td class="center"><div class="right"><?=$day;?>일(<span style="color:<?=$weekClr[$w];?>;"><?=$weekStr[$w];?></span>)</div></td>
							<td class="center"><div class="right"><?=$R4['value'];?></div></td>
							</tr><?
						}?>
						<tr>
							<td class="center bold" style="background-color:#E4F7BA;" colspan="2"><div class="right"><?=$year;?>년 <?=$month;?>월 계</div></td>
							<td class="center bold" style="background-color:#E4F7BA;"><div class="right"><?=$R3['value'];?></div></td>
						</tr><?
					}?>
					<tr>
						<td class="center bold" style="background-color:#FFFF00;" colspan="3"><div class="right"><?=$year;?>년 <?=count($R2['LIST']);?>개월 계</div></td>
						<td class="center bold" style="background-color:#FFFF00;"><div class="right"><?=$R2['value'];?></div></td>
					</tr><?
				}?>
				<tr>
					<td class="center bold" style="background-color:#FDE9D9;" colspan="4"><div class="right"><?=$R1['name'];?> 계</div></td>
					<td class="center bold" style="background-color:#FDE9D9;"><div class="right"><?=$R1['value'];?></div></td>
				</tr><?
			}
		}else{?>
			<tr>
				<td class="center last" colspan="5">::검색된 데이타가 없습니다.::</td>
			</tr><?
		}

	}else{
	}

	include_once('../inc/_db_close.php');
?>