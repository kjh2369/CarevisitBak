<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$useYn	= $_POST['useYn'];

	/*
		$year	= $_POST['year'];
		$month	= $_POST['month'];
		$month	= ($month < 10 ? '0' : '').$month;
	*/
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);
	
	$sql = 'SELECT	suga_cd AS cd, DATE_FORMAT(reg_dt, \'%Y%m%d\') AS date, SUM(att_cnt) AS cnt_per, COUNT(suga_cd) AS cnt_svc
			FROM	care_rpt
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		org_sr	 = \''.$SR.'\'
			AND		reg_dt	>= \''.$fromDt.'\'
			AND		reg_dt	<= \''.$toDt.'\'
			AND		del_flag = \'N\'
			GROUP BY suga_cd, reg_dt';
	//if ($debug) echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>';
	$rpt = $conn->_fetch_array($sql, 'cd');

			

	$sql = 'SELECT	*
			FROM	(
					SELECT	b.cd1 AS mst_cd
					,		b.cd2 AS pro_cd
					,		b.cd3 AS svc_cd
					,		a.suga_cd AS suga_cd
					,		a.suga_sub AS sub_cd
					,		b.nm1 AS mst_nm
					,		b.nm2 AS pro_nm
					,		b.nm3 AS svc_nm
					,		a.suga_nm AS sub_nm
					,		COUNT(DISTINCT c.jumin) AS cnt_per
					,		COUNT(c.jumin) AS cnt_svc
					FROM	(
							SELECT	DISTINCT org_no, suga_sr, suga_cd, suga_sub, suga_nm, DATE_FORMAT(from_dt, \'%Y%m%d\') AS from_dt, DATE_FORMAT(to_dt, \'%Y%m%d\') AS to_dt
							FROM	care_suga
							WHERE	org_no	= \''.$orgNo.'\'
							AND		suga_sr = \''.$SR.'\'
							AND		CASE WHEN DATE_FORMAT(from_dt, \'%Y%m%d\') BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' THEN 1
										 WHEN DATE_FORMAT(to_dt, \'%Y%m%d\') BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' THEN 1
										 WHEN \''.$fromDt.'\' BETWEEN DATE_FORMAT(from_dt, \'%Y%m%d\') AND DATE_FORMAT(to_dt, \'%Y%m%d\') THEN 1
										 WHEN \''.$toDt.'\' BETWEEN DATE_FORMAT(from_dt, \'%Y%m%d\') AND DATE_FORMAT(to_dt, \'%Y%m%d\') THEN 1 ELSE 0 END = 1
							UNION	ALL
							SELECT	\''.$orgNo.'\', \''.$SR.'\', LEFT(code,5), MID(code,6), name, DATE_FORMAT(from_dt, \'%Y%m%d\'), DATE_FORMAT(to_dt, \'%Y%m%d\')
							FROM	care_suga_comm
							WHERE	CASE WHEN DATE_FORMAT(from_dt, \'%Y%m%\') BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' THEN 1
										 WHEN DATE_FORMAT(to_dt, \'%Y%m%d\') BETWEEN\''.$fromDt.'\' AND \''.$toDt.'\' THEN 1
										 WHEN \''.$fromDt.'\' BETWEEN DATE_FORMAT(from_dt, \'%Y%m%d\') AND DATE_FORMAT(to_dt, \'%Y%m%d\') THEN 1
										 WHEN \''.$toDt.'\' BETWEEN DATE_FORMAT(from_dt, \'%Y%m%d\') AND DATE_FORMAT(to_dt, \'%Y%m%d\') THEN 1 ELSE 0 END = 1
							) AS a
					INNER	JOIN	suga_care AS b
							ON		CONCAT(b.cd1, b.cd2, b.cd3) = a.suga_cd
					LEFT	JOIN	(
							SELECT	t01_suga_code1 AS cd, t01_sugup_date AS date, t01_jumin AS jumin
							FROM	t01iljung
							WHERE	t01_ccode		= \''.$orgNo.'\'
							AND		t01_mkind		= \''.$SR.'\'
							AND		t01_sugup_date >= \''.$fromDt.'\'
							AND		t01_sugup_date <= \''.$toDt.'\'
							AND		t01_status_gbn	= \'1\'
							AND		t01_del_yn		= \'N\'
							) AS c
							ON		c.cd	= CONCAT(a.suga_cd, a.suga_sub)
							AND		c.date	BETWEEN a.from_dt AND a.to_dt
					WHERE	a.org_no	= \''.$orgNo.'\'
					AND		a.suga_sr	= \''.$SR.'\'
					GROUP	BY mst_cd, pro_cd, svc_cd, sub_cd
			) AS a';

	if ($useYn == 'Y'){
		$sql .= ' WHERE cnt_per > 0';
	}

	$sql .=' ORDER	BY mst_cd, pro_cd, svc_cd, sub_nm';

	//if ($debug) echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$mstCd = $row['mst_cd'];
		$proCd = $row['pro_cd'];
		$svcCd = $row['svc_cd'];
		
		
		//$row['cnt_per'] += $rpt[$row['suga_cd'].$row['sub_cd']]['cnt_per'];
		//$row['cnt_svc'] += $rpt[$row['suga_cd'].$row['sub_cd']]['cnt_svc'];

		if (!$data[$mstCd]){
			 $data[$mstCd]['name'] = $row['mst_nm'];
		}

		if (!$data[$mstCd][$proCd]){
			 $data[$mstCd][$proCd]['name'] = $row['pro_nm'];
			 $data[$mstCd]['cnt'] ++;
		}

		if (!$data[$mstCd][$proCd][$svcCd]){
			 $data[$mstCd][$proCd][$svcCd]['name'] = $row['svc_nm'];
			 $data[$mstCd]['cnt'] ++;
			 $data[$mstCd][$proCd]['cnt'] ++;
		}

		$data[$mstCd][$proCd][$svcCd]['list'][] = Array(
			'code'	=>$row['sub_cd']
		,	'name'	=>$row['sub_nm']
		,	'cntPer'=>$row['cnt_per']
		,	'cntSvc'=>$row['cnt_svc']
		);

		$data[$mstCd]['cnt'] ++;
		$data[$mstCd][$proCd]['cnt'] ++;
		$data[$mstCd][$proCd][$svcCd]['cnt'] ++;
	}

	$conn->row_free();

	$style = '';


	if ($IsExcelClass){
		if (is_array($data)){
			$cntPer[3] = 0;
			$cntSvc[3] = 0;

			foreach($data as $mstCd => $mstArr){
				if (!is_array($mstArr)) continue;

				$cntPer[2] = 0;
				$cntSvc[2] = 0;

				foreach($mstArr as $proCd => $proArr){
					if (!is_array($proArr)) continue;

					$cntPer[1] = 0;
					$cntSvc[1] = 0;

					foreach($proArr as $svcCd => $svcArr){
						if (!is_array($svcArr)) continue;

						$cntPer[0] = 0;
						$cntSvc[0] = 0;

						foreach($svcArr['list'] as $idx => $row){
							if (!is_array($row)) continue;

							$rowNo ++;
							$sheet->getRowDimension($rowNo)->setRowHeight(-1);
							$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>$row['name'], 'H'=>'L') );
							$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>$row['cntPer'], 'H'=>'R') );
							$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>$row['cntSvc'], 'H'=>'R') );

							$cntPer[0] += $row['cntPer'];
							$cntSvc[0] += $row['cntSvc'];
							$cntPer[1] += $row['cntPer'];
							$cntSvc[1] += $row['cntSvc'];
							$cntPer[2] += $row['cntPer'];
							$cntSvc[2] += $row['cntSvc'];
							$cntPer[3] += $row['cntPer'];
							$cntSvc[3] += $row['cntSvc'];

							if ($tmpMstCd != $mstCd){
								$tmpMstCd  = $mstCd;
								$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'A'.($rowNo + $mstArr['cnt'] - 1), 'val'=>str_replace("<br>", "\n", $mstArr['name']), 'H'=>'L') );
							}

							if ($tmpProCd != $mstCd.'_'.$proCd){
								$tmpProCd  = $mstCd.'_'.$proCd;
								$sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'B'.($rowNo + $proArr['cnt'] - 1), 'val'=>str_replace("<br>", "\n", $proArr['name']), 'H'=>'L') );
							}

							if ($tmpSvcCd != $mstCd.'_'.$proCd.'_'.$svcCd){
								$tmpSvcCd  = $mstCd.'_'.$proCd.'_'.$svcCd;
								$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'C'.($rowNo + $svcArr['cnt'] - 1), 'val'=>str_replace("<br>", "\n", $svcArr['name']), 'H'=>'L') );
							}
						}

						$rowNo ++;
						$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);
						$sheet->getRowDimension($rowNo)->setRowHeight($rH * 1.3);
						$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>str_replace('<br>', ' ', $svcArr['name']).' 소계', 'H'=>'R', 'backcolor'=>'E4F7BA') );
						$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>$cntPer[0], 'H'=>'R', 'backcolor'=>'E4F7BA') );
						$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>$cntSvc[0], 'H'=>'R', 'backcolor'=>'E4F7BA') );
						$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
					}

					$rowNo ++;
					$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);
					$sheet->getRowDimension($rowNo)->setRowHeight($rH * 1.3);
					$sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>str_replace('<br>', ' ', $proArr['name']).' 소계', 'H'=>'R', 'backcolor'=>'FAF4C0') );
					$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>$cntPer[1], 'H'=>'R', 'backcolor'=>'FAF4C0') );
					$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>$cntSvc[1], 'H'=>'R', 'backcolor'=>'FAF4C0') );
					$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
				}

				$rowNo ++;
				$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);
				$sheet->getRowDimension($rowNo)->setRowHeight($rH * 1.3);
				$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>str_replace('<br>', ' ', $mstArr['name']).' 소계', 'H'=>'R', 'backcolor'=>'FAE0D4') );
				$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>$cntPer[2], 'H'=>'R', 'backcolor'=>'FAE0D4') );
				$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>$cntSvc[2], 'H'=>'R', 'backcolor'=>'FAE0D4') );
				$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
			}

			$rowNo ++;
			$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);
			$sheet->getRowDimension($rowNo)->setRowHeight($rH * 1.3);
			$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>'합계', 'H'=>'R', 'backcolor'=>'D4F4FA') );
			$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>$cntPer[3], 'H'=>'R', 'backcolor'=>'D4F4FA') );
			$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>$cntSvc[3], 'H'=>'R', 'backcolor'=>'D4F4FA') );
			$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
		}
	}else{
		if ($IsExcel){
			$style = 'border:0.5pt solid BLACK;';
		}

		if (is_array($data)){
			$cntPer[3] = 0;
			$cntSvc[3] = 0;

			foreach($data as $mstCd => $mstArr){
				if (!is_array($mstArr)) continue;?>
				<tr>
				<td class="top" rowspan="<?=$mstArr['cnt'];?>" style="<?=$style;?>"><div class="left nowrap" style="padding-right:5px;"><?=!$IsExcel ? $mstArr['name'] : str_replace('<br>','<br style="mso-data-placement:same-cell;">',$mstArr['name']);?></div></td><?

				$cntPer[2] = 0;
				$cntSvc[2] = 0;

				foreach($mstArr as $proCd => $proArr){
					if (!is_array($proArr)) continue;
					if ($mstArr['cnt'] < 1){?>
						<tr><?
					}?>
					<td class="top" rowspan="<?=$proArr['cnt'];?>" style="<?=$style;?>"><div class="left" style="padding-right:5px;"><?=!$IsExcel ? $proArr['name'] : str_replace('<br>','<br style="mso-data-placement:same-cell;">',$proArr['name']);?></div></td><?

					$cntPer[1] = 0;
					$cntSvc[1] = 0;

					foreach($proArr as $svcCd => $svcArr){
						if (!is_array($svcArr)) continue;
						if ($proArr['cnt'] < 1){?>
							<tr><?
						}?>
						<td class="top" rowspan="<?=$svcArr['cnt'];?>" style="<?=$style;?>"><div class="left" style="padding-right:5px;"><?=$svcArr['name'];?></div></td><?

						$cntPer[0] = 0;
						$cntSvc[0] = 0;

						foreach($svcArr['list'] as $idx => $row){
							if (!is_array($row)) continue;
							if ($svcArr['cnt'] < 1){?>
								<tr><?
							}?>
							<td style="<?=$style;?>"><div class="left" style="padding-right:5px;"><?=$row['name'];?></div></td>
							<td style="<?=$style;?>"><div class="right"><?=($row['cntPer'] ? number_format($row['cntPer']) : '&nbsp;');?></div></td>
							<td style="<?=$style;?>"><div class="right"><?=($row['cntSvc'] ? number_format($row['cntSvc']) : '&nbsp;');?></div></td><?
							if (!$IsExcel){?>
								<td class="last" style="<?=$style;?>">
								<div class="left"><a href="#" onclick="lfNameTable('<?=$mstCd;?>','<?=$proCd;?>','<?=$svcCd;?>','<?=$row['code'];?>'); return false;">명단보기</a></div>
								</td><?
							}?>
							</tr><?

							$cntPer[0] += $row['cntPer'];
							$cntSvc[0] += $row['cntSvc'];
							$cntPer[1] += $row['cntPer'];
							$cntSvc[1] += $row['cntSvc'];
							$cntPer[2] += $row['cntPer'];
							$cntSvc[2] += $row['cntSvc'];
							$cntPer[3] += $row['cntPer'];
							$cntSvc[3] += $row['cntSvc'];
						}?>
						<tr style="background-color:#E4F7BA; font-weight:bold;">
						<td class="" colspan="2"><div class="right"><?=$svcArr['name'];?> 소계</div></td>
						<td class=""><div class="right"><?=$cntPer[0] ? number_format($cntPer[0]) : '&nbsp;';?></div></td>
						<td class=""><div class="right"><?=$cntSvc[0] ? number_format($cntSvc[0]) : '&nbsp;';?></div></td>
						<td class="last"></td>
						</tr><?
					}?>
					<tr style="background-color:#FAF4C0; font-weight:bold;">
					<td class="" colspan="3"><div class="right"><?=str_replace('<br>', ' ', $proArr['name']);?> 소계</div></td>
					<td class=""><div class="right"><?=$cntPer[1] ? number_format($cntPer[1]) : '&nbsp;';?></div></td>
					<td class=""><div class="right"><?=$cntSvc[1] ? number_format($cntSvc[1]) : '&nbsp;';?></div></td>
					<td class="last"></td>
					</tr><?

					$proArr['cnt'] = 0;
				}?>
				<tr style="background-color:#FAE0D4; font-weight:bold;">
				<td class="" colspan="4"><div class="right"><?=str_replace('<br>', ' ', $mstArr['name']);?> 소계</div></td>
				<td class=""><div class="right"><?=$cntPer[2] ? number_format($cntPer[2]) : '&nbsp;';?></div></td>
				<td class=""><div class="right"><?=$cntSvc[2] ? number_format($cntSvc[2]) : '&nbsp;';?></div></td>
				<td class="last"></td>
				</tr><?

				$mstArr['cnt'] = 0;
			}?>
			<tr style="background-color:#D4F4FA; font-weight:bold;">
			<td class="" colspan="4"><div class="right">합계</div></td>
			<td class=""><div class="right"><?=$cntPer[3] ? number_format($cntPer[3]) : '&nbsp;';?></div></td>
			<td class=""><div class="right"><?=$cntSvc[3] ? number_format($cntSvc[3]) : '&nbsp;';?></div></td>
			<td class="last"></td>
			</tr><?
		}else{?>
			<tr>
				<td class="center last" style="<?=$style;?>" colspan="7">::검색된 데이타가 없습니다.::</td>
			</tr><?
		}

		if ($IsExcel){?>
			<td colspan="6" style="text-align:center; font-size:17px; font-weight:bold; border:none;"><?=$_SESSION['userCenterName'];?></td><?
		}
	}

	include_once('../inc/_db_close.php');
?>