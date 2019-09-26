<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;

	/* 02:자동전송, 01:시작만전송, 03:오류수정, 04:직접입력, 99:기타 */
	/*
SELECT mem_nm
,      name
,      app_no
,      svc_gbn
,      send_gbn
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '01' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft01
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '02' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft02
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '03' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft03
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '04' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft04
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '05' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft05
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '06' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft06
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '07' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft07
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '08' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft08
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '09' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft09
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '10' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft10
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '11' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft11
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '12' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft12
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '13' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft13
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '14' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft14
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '15' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft15
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '16' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft16
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '17' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft17
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '18' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft18
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '19' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft19
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '20' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft20
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '21' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft21
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '22' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft22
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '23' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft23
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '24' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft24
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '25' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft25
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '26' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft26
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '27' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft27
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '28' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft28
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '29' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft29
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '30' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft30
,      GROUP_CONCAT(DISTINCT CASE WHEN RIGHT(reg_dt,2) = '31' THEN CONCAT(LEFT(from_tm,4),'~',LEFT(to_tm,4)) ELSE '' END) AS ft31
FROM   lg2cv
WHERE  org_no = '34273000017'
AND    yymm = '201411'
GROUP  BY mem_nm, name, app_no, svc_gbn, send_gbn
ORDER  BY mem_nm, name
	 */

	//대상자 정보
	$sql = 'SELECT	DISTINCT jumin, app_no, level
			FROM	client_his_lvl
			WHERE	org_no = \''.$orgNo.'\'
			AND		svc_cd = \'0\'
			AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$year.$month.'\'
			AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6) >= \''.$year.$month.'\'
			ORDER	BY jumin, from_dt, to_dt';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$appNo = $row['app_no'];

		$tgif[$appNo] = Array(
			'age'=>$myF->issToAge($row['jumin']).'세'
		,	'lvl'=>$row['level'].($row['level'] ? '급' : '')
		);
	}

	$conn->row_free();

	//작성정보
	$sql = 'SELECT	app_no
			,		reg_dt
			,		svc_cd
			,		from_tm
			,		write_yn
			FROM	lg2cv_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		LEFT(reg_dt,6) = \''.$year.$month.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$appNo = $row['app_no'];

		$tgif[$appNo]['LOG_'.$row['reg_dt'].'_'.$row['svc_cd'].'_'.$row['from_tm']] = ($row['write_yn'] == 'Y' ? 'Y' : 'N');
	}

	$conn->row_free();

	/* 02:자동전송, 01:시작만전송, 03:오류수정, 04:직접입력, 99:기타 */
	$sql = 'SELECT	mem_nm
			,		name
			,		app_no
			,		svc_gbn
			,		send_gbn
			,		reg_dt
			,		CAST(RIGHT(reg_dt,2) AS unsigned) AS day
			,		from_tm
			,		to_tm
			FROM	lg2cv
			WHERE	org_no	  = \''.$orgNo.'\'
			AND		yymm	  = \''.$year.$month.'\'
			AND		use_yn	  = \'Y\'';

	$sql .= '
			AND		send_gbn != \'02\'
			AND		send_gbn != \'01\'
			AND		send_gbn != \'99\'';

	$sql .= '
			ORDER	BY mem_nm, name, app_no, svc_gbn
					,	CASE WHEN send_gbn = \'02\' THEN 1
							 WHEN send_gbn = \'04\' THEN 2
							 WHEN send_gbn = \'03\' THEN 3
							 WHEN send_gbn = \'01\' THEN 4 ElSE 5 END
					,	reg_dt, from_tm, to_tm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$idx = -1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($tmpMemNm != $row['mem_nm']){
			$idx ++;

			$data[$idx] = Array(
				'name'	=>$row['mem_nm']
			,	'cnt'	=>0
			,	'add'	=>0
			,	'iljung'=>0
			);

			$tmpMemNm = $row['mem_nm'];
		}

		$appNo = $row['app_no'];

		if (!$data[$idx]['list'][$appNo]){
			 $data[$idx]['list'][$appNo] = Array(
				'name'	=>$row['name']
			,	'cnt'	=>0
			,	'add'	=>0
			,	'iljung'=>0
			);
		}

		$svcGbn = $row['svc_gbn'];

		if ($row['svc_gbn'] == '200'){
			$row['svc_gbn'] = '요양';
		}else if ($row['svc_gbn'] == '500'){
			$row['svc_gbn'] = '목욕';
		}else if ($row['svc_gbn'] == '800'){
			$row['svc_gbn'] = '간호';
		}

		if (!$data[$idx]['list'][$appNo]['list'][$svcGbn]){
			 $data[$idx]['list'][$appNo]['list'][$svcGbn] = Array(
				'name'	=>$row['svc_gbn']
			,	'cnt'	=>0
			,	'add'	=>0
			,	'iljung'=>0
			);
		}

		$sendGbn = $row['send_gbn'];

		/* 02:자동전송, 01:시작만전송, 03:오류수정, 04:직접입력, 99:기타 */

		if ($row['send_gbn'] == '02'){
			$row['send_gbn'] = '자동';
		}else if ($row['send_gbn'] == '01'){
			$row['send_gbn'] = '시작';
		}else if ($row['send_gbn'] == '03'){
			$row['send_gbn'] = '오류';
		}else if ($row['send_gbn'] == '04'){
			$row['send_gbn'] = '직접';
		}else{
			$row['send_gbn'] = '기타';
		}

		if (!$data[$idx]['list'][$appNo]['list'][$svcGbn]['list'][$sendGbn]){
			 $data[$idx]['list'][$appNo]['list'][$svcGbn]['list'][$sendGbn] = Array(
				'name'	=>$row['send_gbn']
			,	'cnt'	=>0
			,	'iljung'=>0
			);
		}

		$data[$idx]['list'][$appNo]['list'][$svcGbn]['list'][$sendGbn]['list'][$row['day']][] = Array(
			'date'	=>$row['reg_dt']
		,	'from'	=>$row['from_tm']
		,	'to'	=>$row['to_tm']
		,	'val'	=>$myF->timeStyle(SubStr($row['from_tm'],0,4)).'<br>~'.$myF->timeStyle(SubStr($row['to_tm'],0,4))
		);

		$dayCnt = SizeOf($data[$idx]['list'][$appNo]['list'][$svcGbn]['list'][$sendGbn]['list'][$row['day']]);

		if ($tmpKey != $row['mem_nm'].'_'.$row['app_no'].'_'.$row['svc_gbn'].'_'.$row['send_gbn']){
			$data[$idx]['cnt'] ++;
			$data[$idx]['list'][$appNo]['cnt'] ++;
			$data[$idx]['list'][$appNo]['list'][$svcGbn]['cnt'] ++;

			$tmpKey = $row['mem_nm'].'_'.$row['app_no'].'_'.$row['svc_gbn'].'_'.$row['send_gbn'];
		}

		if ($dayCnt > 1){
			$data[$idx]['add'] = $dayCnt - 1;
			$data[$idx]['list'][$appNo]['add'] = $dayCnt - 1;
			$data[$idx]['list'][$appNo]['list'][$svcGbn]['add'] = $dayCnt - 1;
		}

		$data[$idx]['iljung'] ++;
		$data[$idx]['list'][$appNo]['iljung'] ++;
		$data[$idx]['list'][$appNo]['list'][$svcGbn]['iljung'] ++;
		$data[$idx]['list'][$appNo]['list'][$svcGbn]['list'][$sendGbn]['iljung'] ++;

		$data[$idx]['list'][$appNo]['list'][$svcGbn]['list'][$sendGbn]['cnt'] = $dayCnt;
	}

	$conn->row_free();

	if (is_array($data)){
		$no = 1;

		foreach($data as $idx => $R1){
			if ($no % 2 == 0){
				$bgclr = '#EAEAEA';
			}else{
				$bgclr = '';
			}?>
			<tr style="background-color:<?=$bgclr;?>;">
			<td class="center" style="vertical-align:top;" rowspan="<?=$R1['cnt']+$R1['add'];?>"><?=$R1['name'];?><br><span style="color:BLUE;"><?=$R1['iljung'];?></span>건</td><?
			$IsFirst[0] = true;

			foreach($R1['list'] as $appNo => $R2){
				if ($IsFirst[0]){
					$IsFirst[0] = false;
				}else{?>
					<tr style="background-color:<?=$bgclr;?>;"><?
				}?>
				<td class="center" style="vertical-align:top;" rowspan="<?=$R2['cnt']+$R2['add'];?>"><?=$R2['name'];?><br><?=$tgif[$appNo]['age'].($tgif[$appNo]['lvl'] ? ',' : '').$tgif[$appNo]['lvl'];?></td><?

				$IsFirst[1] = true;

				foreach($R2['list'] as $svcGbn => $R3){
					if ($IsFirst[1]){
						$IsFirst[1] = false;
					}else{?>
						<tr style="background-color:<?=$bgclr;?>;"><?
					}?>
					<td class="center" style="vertical-align:top;" rowspan="<?=$R3['cnt']+$R3['add'];?>"><?=$R3['name'];?><br><span style="color:BLUE;"><?=$R3['iljung'];?></span>건</td><?

					$IsFirst[2] = true;

					foreach($R3['list'] as $sendGbn => $R4){
						if ($IsFirst[2]){
							$IsFirst[2] = false;
						}else{?>
							<tr style="background-color:<?=$bgclr;?>;"><?
						}?>
						<td class="center" style="vertical-align:top;" rowspan="<?=$R4['cnt'];?>"><?=$R4['name'];?><br><span style="color:BLUE;"><?=$R4['iljung'];?></span>건</td><?

						for($i=0; $i<$R4['cnt']; $i++){
							if ($i > 0){?>
								<tr style="background-color:<?=$bgclr;?>;"><?
							}

							for($j=1; $j<=31; $j++){
								if ($tgif[$appNo]['LOG_'.$R4['list'][$j][$i]['date'].'_'.$svcGbn.'_'.$R4['list'][$j][$i]['from']] == 'Y'){
									$clr = '#FFD8D8';
								}else{
									$clr = '';
								}?>
								<td class="center" style="line-height:1.3em; background-color:<?=$clr;?>;">
									<div class="left" onclick="lfSetLog(this,'<?=$appNo;?>','<?=$R4['list'][$j][$i]['date'];?>','<?=$svcGbn;?>','<?=$R4['list'][$j][$i]['from'];?>','<?=$R4['list'][$j][$i]['to'];?>');">
										<a href="#" onclick="return false;"><?=$R4['list'][$j][$i]['val'];?></a>
									</div>
								</td><?
							}?>
							<td></td>
							</tr><?
						}
					}
				}
			}

			$no ++;
		}
	}else{?>
		<tr>
			<td class="center last" colspan="35">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	Unset($data);

	include_once('../inc/_db_close.php');
?>