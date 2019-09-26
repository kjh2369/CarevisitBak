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

	$conn->fetch_type = 'assoc';

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
			AND		CASE WHEN svc_gbn = \'200\' AND send_gbn != \'02\' AND send_gbn != \'01\' AND send_gbn != \'99\' THEN 1 ELSE 0 END
					+ CASE WHEN svc_gbn = \'500\' THEN 1 ELSE 0 END
					+ CASE WHEN svc_gbn = \'800\' AND send_gbn != \'02\' AND send_gbn != \'01\' AND send_gbn != \'99\' THEN 1 ELSE 0 END > 0';

	$sql .= '
			ORDER	BY svc_gbn, mem_nm, name, app_no, svc_gbn
					,	CASE WHEN send_gbn = \'02\' THEN 1
							 WHEN send_gbn = \'04\' THEN 2
							 WHEN send_gbn = \'03\' THEN 3
							 WHEN send_gbn = \'01\' THEN 4 ElSE 5 END
					,	reg_dt, from_tm, to_tm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	//if ($debug) echo '<tr><td class="left last" colspan="35">'.nl2br($sql).'</td></tr>';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$svcGbn = $row['svc_gbn'];
		$memNm	= $row['mem_nm'];
		$appNo	= $row['app_no'];
		$sendGbn= $row['send_gbn'];

		if ($svcGbn == '500'){
			$idx = -1;

			if (is_array($rowData[$svcGbn])){
				foreach($rowData[$svcGbn] as $tmpI => $tmpR){
					if ($rowData[$svcGbn][$tmpI]['send_gbn'] != ''){
						$sendIdx = 0;
					}else{
						$sendIdx = 1;
					}

					if ($tmpR['send_gbn'] == '03' || $tmpR['send_gbn'] == '04') $sendIdx ++;
					if ($row['send_gbn'] == '03' || $row['send_gbn'] == '04') $sendIdx ++;

					if ($sendIdx > 0){
						if ($tmpR['mem_nm'] != $row['mem_nm'] && $tmpR['name'] == $row['name'] && $tmpR['reg_dt'] == $row['reg_dt']){
							$idx = $tmpI;
							break;
						}
					}
				}
			}

			if ($idx < 0){
				$rowData[$svcGbn][] = $row;
			}else{
				$rowData[$svcGbn][$idx]['mem_nm']	.= ('/'.$row['mem_nm']);
				$rowData[$svcGbn][$idx]['send_gbn'] .= ('/'.$row['send_gbn']);
				$rowData[$svcGbn][$idx]['from_tm']	.= ('/'.$row['from_tm']);
				$rowData[$svcGbn][$idx]['to_tm']	.= ('/'.$row['to_tm']);
			}
		}else{
			$rowData[$svcGbn][] = $row;
		}
	}

	$conn->row_free();


	if (is_array($rowData['500'])){
		foreach($rowData['500'] as $tmpI => $tmpR){
			if (is_numeric(StrPos($tmpR['send_gbn'],'/'))){
				$tmpR500[] = $tmpR;
			}
		}

		if (is_array($tmpR500)){
			$rowData['500'] = $tmpR500;
		}else{
			$rowData['500'] = null;
		}
	}

	if (is_array($rowData)){
		$idx = -1;

		foreach($rowData as $svcGbn => $svcR){
			if (!is_array($svcR)) continue;
			if (!$data[$svcGbn]){
				switch($svcGbn){
					case '200':
						$svcNm = '요양';
						break;

					case '500':
						$svcNm = '목욕';
						break;

					case '800':
						$svcNm = '간호';
						break;

					default:
						$svcNm = $svcGbn;
				}

				$data[$svcGbn] = Array(
					'name'	=>$svcNm
				,	'cnt'	=>0
				,	'iljung'=>0
				);
			}

			foreach($svcR as $tmpI => $tmpR){
				$memNm = $tmpR['mem_nm'];

				if ($key1 != $svcGbn.'_'.$memNm){
					$key1  = $svcGbn.'_'.$memNm;
					$idx ++;
				}

				if (!$data[$svcGbn]['list'][$idx]){
					 $data[$svcGbn]['list'][$idx] = Array(
						'name'	=>$memNm
					,	'cnt'	=>0
					,	'addCnt'=>0
					,	'iljung'=>0
					);
				}

				$appNo = $tmpR['app_no'];

				if (!$data[$svcGbn]['list'][$idx]['list'][$appNo]){
					 $data[$svcGbn]['list'][$idx]['list'][$appNo] = Array(
						'name'	=>$tmpR['name']
					,	'cnt'	=>0
					,	'addCnt'=>0
					,	'iljung'=>0
					);
				}

				$sendGbn = $tmpR['send_gbn'];

				if (!$data[$svcGbn]['list'][$idx]['list'][$appNo]['list'][$sendGbn]){
					switch($sendGbn){
						case '02':
							$sendNm = '자동';
							break;

						case '01':
							$sendNm = '시작';
							break;

						case '03':
							$sendNm = '오류';
							break;

						case '04':
							$sendNm = '직접';
							break;

						case '99':
							$sendNm = '기타';
							break;

						default:
							$sendNm = $sendGbn;
					}
					$data[$svcGbn]['list'][$idx]['list'][$appNo]['list'][$sendGbn] = Array(
						'name'	=>$sendNm
					,	'cnt'	=>0
					,	'addCnt'=>0
					,	'iljung'=>0
					);
				}

				$date	= $tmpR['reg_dt'];
				$day	= $tmpR['day'];
				$from	= $tmpR['from_tm'];
				$to		= $tmpR['to_tm'];

				$data[$svcGbn]['list'][$idx]['list'][$appNo]['list'][$sendGbn]['list'][$day][] = Array(
					'date'	=>$date
				,	'from'	=>$from
				,	'to'	=>$to
				,	'val'	=>$myF->timeStyle(SubStr($from,0,4)).'<br>~'.$myF->timeStyle(SubStr($to,0,4))
				);

				if ($tmpKey != $memNm.'_'.$appNo.'_'.$svcGbn.'_'.$sendGbn){
					$data[$svcGbn]['cnt'] ++;
					$data[$svcGbn]['list'][$idx]['cnt'] ++;
					$data[$svcGbn]['list'][$idx]['list'][$appNo]['cnt'] ++;
					$data[$svcGbn]['list'][$idx]['list'][$appNo]['list'][$sendGbn]['cnt'] ++;

					$tmpKey = $memNm.'_'.$appNo.'_'.$svcGbn.'_'.$sendGbn;
				}

				if ($dayCnt <= SizeOf($data[$svcGbn]['list'][$idx]['list'][$appNo]['list'][$sendGbn]['list'][$day])){
					$dayCnt = SizeOf($data[$svcGbn]['list'][$idx]['list'][$appNo]['list'][$sendGbn]['list'][$day]);

					if ($dayCnt - 1 > $data[$svcGbn]['list'][$idx]['list'][$appNo]['list'][$sendGbn]['addCnt']){
						$data[$svcGbn]['addCnt'] = $data[$svcGbn]['addCnt'] - $data[$svcGbn]['list'][$idx]['list'][$appNo]['list'][$sendGbn]['addCnt'] + $dayCnt - 1;
						$data[$svcGbn]['list'][$idx]['addCnt'] = $data[$svcGbn]['list'][$idx]['addCnt'] - $data[$svcGbn]['list'][$idx]['list'][$appNo]['list'][$sendGbn]['addCnt'] + $dayCnt - 1;
						$data[$svcGbn]['list'][$idx]['list'][$appNo]['addCnt'] = $data[$svcGbn]['list'][$idx]['list'][$appNo]['addCnt'] - $data[$svcGbn]['list'][$idx]['list'][$appNo]['list'][$sendGbn]['addCnt'] + $dayCnt - 1;

						$data[$svcGbn]['list'][$idx]['list'][$appNo]['list'][$sendGbn]['addCnt'] = $dayCnt - 1;
					}
				}

				$data[$svcGbn]['iljung'] ++;
				$data[$svcGbn]['list'][$idx]['iljung'] ++;
				$data[$svcGbn]['list'][$idx]['list'][$appNo]['iljung'] ++;
				$data[$svcGbn]['list'][$idx]['list'][$appNo]['list'][$sendGbn]['iljung'] ++;
			}
		}

		Unset($rowData);
	}


	//echo '</td></tr>';

	/* 02:자동전송, 01:시작만전송, 03:오류수정, 04:직접입력, 99:기타 */
	$strSendGbn = Array('02'=>'자','01'=>'시','03'=>'오','04'=>'직','99'=>'기');

	if ($IsExcel){
		$style = 'border:0.5pt solid BLACK;';
	}else{
		$style = '';
	}

	if (is_array($data)){
		foreach($data as $svcGbn => $svcR){
			if ($svcGbn == '200'){
				$bgclr = '#E0FFDB';
			}else if ($svcGbn == '500'){
				$bgclr = '#ECEBFF';
			}else if ($svcGbn == '800'){
				$bgclr = '#FFEBFE';
			}else{
				$bgclr = '';
			}?>
			<tr style="background-color:<?=$bgclr;?>;">
				<td class="center" colspan="35" style="<?=$style;?>"><div class="left bold">-<?=$svcR['name'];?>(<?=number_format($svcR['iljung']);?>건)</div></td>
			</tr><?

			$no1 = 1;

			foreach($svcR['list'] as $memI => $memR){
				if ($no1 % 2 == 1){
					$bgclr = 'F0FBFD';
				}else{
					$bgclr = 'FDEDEE';
				}?>
				<tr style="background-color:#<?=$bgclr;?>;">
				<td class="center" style="vertical-align:top; padding:3px 0 3px 0; line-height:1.3em; <?=$style;?>" rowspan="<?=$memR['cnt'] + $memR['addCnt'];?>"><?=str_replace('/','<br>/',$memR['name']);?><br><span style="color:BLUE;"><?=$memR['iljung'];?></span>건</td><?

				$IsFirst[0] = true;

				$no2 = 1;

				foreach($memR['list'] as $appNo => $appR){
					if ($no1 % 2 == 1){
						if ($no2 % 2 == 1){
							$bgclr = 'f0fbfd';
						}else{
							$bgclr = 'e7f9fb';
						}
					}else{
						if ($no2 % 2 == 1){
							$bgclr = 'fdedee';
						}else{
							$bgclr = 'fce2e3';
						}
					}

					if ($IsFirst[0]){
						$IsFirst[0] = false;
					}else{?>
						<tr style="background-color:#<?=$bgclr;?>;"><?
					}?>
					<td class="center" style="vertical-align:top; padding:3px 0 3px 0; line-height:1.3em; <?=$style;?>" rowspan="<?=$appR['cnt'] + $appR['addCnt'];?>"><?=$appR['name'];?><br><?=$tgif[$appNo]['age'].($tgif[$appNo]['lvl'] ? ',' : '').$tgif[$appNo]['lvl'];?></td><?

					$IsFirst[1] = true;

					foreach($appR['list'] as $sendGbn => $sendR){
						if (is_numeric(StrPos($sendGbn,'/'))){
							$tmpSend = Explode('/',$sendGbn);
							$sendR['name'] = $strSendGbn[$tmpSend[0]].'/'.$strSendGbn[$tmpSend[1]];
						}

						if ($IsFirst[1]){
							$IsFirst[1] = false;
						}else{?>
							<tr style="background-color:#<?=$bgclr;?>;"><?
						}?>
						<td class="center" style="vertical-align:top; padding:3px 0 3px 0; line-height:1.3em; <?=$style;?>" rowspan="<?=$sendR['cnt'] + $sendR['addCnt'];?>"><?=$sendR['name'];?><br><span style="color:BLUE;"><?=$sendR['iljung'];?></span>건</td><?

						for($i=0; $i<$sendR['cnt']+$sendR['addCnt']; $i++){
							if ($i > 0){?>
								<tr style="background-color:#<?=$bgclr;?>;"><?
							}
							for($j=1; $j<=31; $j++){
								if ($svcGbn == '500'){
									$tmpCd = 'LOG_'.$sendR['list'][$j][$i]['date'].'_'.$svcGbn.'_';

									if (is_numeric(StrPos($sendR['list'][$j][$i]['from'],'/'))){
										$tmpCd .= SubStr($sendR['list'][$j][$i]['from'], 0, StrPos($sendR['list'][$j][$i]['from'],'/'));
									}else{
										$tmpCd .= $sendR['list'][$j][$i]['from'];
									}
								}else{
									$tmpCd = 'LOG_'.$sendR['list'][$j][$i]['date'].'_'.$svcGbn.'_'.$sendR['list'][$j][$i]['from'];
								}

								if ($tgif[$appNo][$tmpCd] == 'Y'){
									$clr = 'fdfab3';
								}else{
									$clr = '';
								}?>
								<td class="center" style="background-color:#<?=$clr;?>; <?=$style;?>">
									<div class="left" style="line-height:1.3em;" onclick="lfSetLog(this,'<?=$appNo;?>','<?=$sendR['list'][$j][$i]['date'];?>','<?=$svcGbn;?>','<?=$sendR['list'][$j][$i]['from'];?>','<?=$sendR['list'][$j][$i]['to'];?>');"><?
										if ($IsExcel){?>
											<span><?=$sendR['list'][$j][$i]['val'];?></span><?
										}else{?>
											<a href="#" onclick="return false;"><?=$sendR['list'][$j][$i]['val'];?></a><?
										}?>
									</div>
								</td><?
							}?>
							<td style="<?=$style;?>"></td><?
						}?>
						</tr><?
					}

					$no2 ++;
				}

				$no1 ++;
			}
		}
		#echo '<tr><td class="left last" colspan="34">';
		#echo '</td></tr>';
	}else{?>
		<tr>
			<td class="center last" colspan="34">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	Unset($data);
	Unset($IsFirst);

	include_once('../inc/_db_close.php');
?>