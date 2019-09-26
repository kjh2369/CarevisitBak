<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$year	= $_POST['year'];

	if ($IsExcel){
		$style = 'border:0.5pt solid BLACK;';
	}

	$sql = 'SELECT	b.cd1 AS mst_cd
			,		b.cd2 AS pro_cd
			,		b.cd3 AS svc_cd
			,		a.suga_sub AS sub_cd
			,		b.nm1 AS mst_nm
			,		b.nm2 AS pro_nm
			,		b.nm3 AS svc_nm
			,		a.suga_nm AS sub_nm
			FROM	care_suga AS a
			INNER	JOIN	suga_care AS b
					ON		b.cd1 = SUBSTR(a.suga_cd,1,1)
					AND		b.cd2 = SUBSTR(a.suga_cd,2,2)
					AND		b.cd3 = SUBSTR(a.suga_cd,4,2)
			WHERE	a.org_no			 = \''.$orgNo.'\'
			AND		a.suga_sr			 = \''.$SR.'\'
			AND		LEFT(a.from_dt,4)	<= \''.$year.'\'
			AND		LEFT(a.to_dt,4)		>= \''.$year.'\'
			ORDER	BY mst_cd, pro_cd, svc_cd, sub_nm
			';
	$rows = $conn->_fetch_array($sql);

	for($i=0; $i<count($rows); $i++){
		$row = $rows[$i];

		if (!$data[$row['mst_cd']]){
			 $data[$row['mst_cd']] = Array(
				'name'	=>$row['mst_nm']
			,	'cnt'	=>0
			);
		}

		if (!$data[$row['mst_cd']]['DATA'][$row['pro_cd']]){
			 $data[$row['mst_cd']]['DATA'][$row['pro_cd']] = Array(
				'name'	=>$row['pro_nm']
			,	'cnt'	=>0
			);
		}

		if (!$data[$row['mst_cd']]['DATA'][$row['pro_cd']]['DATA'][$row['svc_cd']]){
			 $data[$row['mst_cd']]['DATA'][$row['pro_cd']]['DATA'][$row['svc_cd']] = Array(
				'name'	=>$row['svc_nm']
			,	'cnt'	=>0
			);
		}

		if (!$data[$row['mst_cd']]['DATA'][$row['pro_cd']]['DATA'][$row['svc_cd']]['DATA'][$row['sub_cd']]){
			 $data[$row['mst_cd']]['DATA'][$row['pro_cd']]['DATA'][$row['svc_cd']]['DATA'][$row['sub_cd']] = Array(
				'name'	=>$row['sub_nm']
			,	'DATA'	=>Array(1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 7=>0, 8=>0, 9=>0, 10=>0, 11=>0, 12=>0)
			);
		}

		$sql = 'SELECT	DATE_FORMAT(t01_sugup_date, \'%c\') AS month, COUNT(t01_jumin) AS cnt
				FROM	t01iljung
				WHERE	t01_ccode		= \''.$orgNo.'\'
				AND		t01_mkind		= \''.$SR.'\'
				AND		t01_suga_code1	= \''.$row['mst_cd'].$row['pro_cd'].$row['svc_cd'].$row['sub_cd'].'\'
				AND		t01_status_gbn	= \'1\'
				AND		t01_del_yn		= \'N\'
				AND		LEFT(t01_sugup_date,4) = \''.$year.'\'
				GROUP BY LEFT(t01_sugup_date,6)
				';
		$r = $conn->_fetch_array($sql);

		for($j=0; $j<count($r); $j++){
			$data[$row['mst_cd']]['DATA'][$row['pro_cd']]['DATA'][$row['svc_cd']]['DATA'][$row['sub_cd']]['DATA']['SUM'] += $r[$j]['cnt'];
			$data[$row['mst_cd']]['DATA'][$row['pro_cd']]['DATA'][$row['svc_cd']]['DATA'][$row['sub_cd']]['DATA'][$r[$j]['month']] = $r[$j]['cnt'];
		}

		unset($r);

		if ($tmpKey != $row['mst_cd'].'_'.$row['pro_cd'].'_'.$row['svc_cd'].'_'.$row['sub_cd']){
			$data[$row['mst_cd']]['cnt'] ++;
			$data[$row['mst_cd']]['DATA'][$row['pro_cd']]['cnt'] ++;
			$data[$row['mst_cd']]['DATA'][$row['pro_cd']]['DATA'][$row['svc_cd']]['cnt'] ++;
			$tmpKey = $row['mst_cd'].'_'.$row['pro_cd'].'_'.$row['svc_cd'].'_'.$row['sub_cd'];
		}
	}

	unset($rows);

	if ($IsExcelClass){
		if (is_array($data)){
			foreach($data as $mstCd => $mstR){
				foreach($mstR['DATA'] as $proCd => $proR){
					foreach($proR['DATA'] as $svcCd => $svcR){
						foreach($svcR['DATA'] as $subCd => $subR){
							$rowNo ++;
							$sheet->getRowDimension($rowNo)->setRowHeight(-1);
							$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>$subR['name'], 'H'=>'L') );
							$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>$subR['DATA']['SUM'], 'H'=>'R') );

							$cellF = 'E';
							for($i=1; $i<=12; $i++){
								$cellF = GetNextCellId($cellF);
								$sheet->SetData( Array('F'=>$cellF.$rowNo, 'val'=>$subR['DATA'][$i], 'H'=>'R') );
							}

							if ($tmpMstCd != $mstCd){
								$tmpMstCd  = $mstCd;
								$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'A'.($rowNo + $mstR['cnt'] - 1), 'val'=>str_replace("<br>", "\n", $mstR['name']), 'H'=>'L') );
							}

							if ($tmpProCd != $mstCd.'_'.$proCd){
								$tmpProCd  = $mstCd.'_'.$proCd;
								$sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'B'.($rowNo + $proR['cnt'] - 1), 'val'=>str_replace("<br>", "\n", $proR['name']), 'H'=>'L') );
							}

							if ($tmpSvcCd != $mstCd.'_'.$proCd.'_'.$svcCd){
								$tmpSvcCd  = $mstCd.'_'.$proCd.'_'.$svcCd;
								$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'C'.($rowNo + $svcR['cnt'] - 1), 'val'=>str_replace("<br>", "\n", $svcR['name']), 'H'=>'L') );
							}
						}
					}
				}
			}
		}
	}else{
		if (is_array($data)){
			foreach($data as $mstCd => $mstR){?>
				<tr>
				<td class="center" style="<?=$style;?>" rowspan="<?=$mstR['cnt'];?>"><?=$mstR['name'];?></td><?

				$IsFirst[0] = true;
				foreach($mstR['DATA'] as $proCd => $proR){
					if ($IsFirst[0]){
						$IsFirst[0] = false;
					}else{?>
						<tr><?
					}?>
					<td class="center" style="<?=$style;?>" rowspan="<?=$proR['cnt'];?>"><?=$proR['name'];?></td><?

					$IsFirst[1] = true;
					foreach($proR['DATA'] as $svcCd => $svcR){
						if ($IsFirst[1]){
							$IsFirst[1] = false;
						}else{?>
							<tr><?
						}?>
						<td class="center" style="<?=$style;?>" rowspan="<?=$svcR['cnt'];?>"><?=$svcR['name'];?></td><?

						$IsFirst[2] = true;
						foreach($svcR['DATA'] as $subCd => $subR){
							if ($IsFirst[2]){
								$IsFirst[2] = false;
							}else{?>
								<tr><?
							}?>
							<td class="center" style="<?=$style;?>"><?=$subR['name'];?></td>
							<td class="center" style="<?=$style;?>"><div class="right"><?=$subR['DATA']['SUM'] > 0 ? number_format($subR['DATA']['SUM']) : '';?></div></td><?
							for($i=1; $i<=12; $i++){?>
								<td class="center <?=$i == 12 ? 'last' : '';?>" style="<?=$style;?>"><div class="right"><?=$subR['DATA'][$i] > 0 ? number_format($subR['DATA'][$i]) : '';?></div></td><?
							}?>
							</tr><?
						}
					}
				}
			}
		}
	}

	include_once('../inc/_db_close.php');
?>