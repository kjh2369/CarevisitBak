<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$year = $var['year'];
	$re_gbn = $var['re_gbn'];


	for($i=$year-1; $i<=$year; $i++){
		$sql = 'SELECT	gwan_cd, hang_cd, mog_cd, amt
				FROM	fa_budget
				WHERE	org_no	= \''.$orgNo.'\'
				AND		year	= \''.$i.'\'
				AND		re_gbn	= \''.$re_gbn.'\'
				';
		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($j=0; $j<$rowCnt; $j++){
			$row = $conn->select_row($j);

			$budget[$i][$row['gwan_cd']][$row['hang_cd']][$row['mog_cd']] = $row['amt'];
		}

		$conn->row_free();
	}

	$sql = 'SELECT	gwan_cd, hang_cd, mog_cd, gwan_name, hang_name, mog_name
			FROM	fa_item AS a
			WHERE	re_gbn	 = \''.$re_gbn.'\'
			AND		use_flag = \'Y\'
			ORDER	BY gwan_cd, hang_cd, mog_cd
			';
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$gwan_cd = $row['gwan_cd'];
		$hang_cd = $row['hang_cd'];
		$mog_cd = $row['mog_cd'];

		if (!$data[$gwan_cd])
			 $data[$gwan_cd] = Array('name'=>$row['gwan_name'], 'rows'=>0, 'LIST'=>Array());
		if (!$data[$gwan_cd]['LIST'][$hang_cd])
			 $data[$gwan_cd]['LIST'][$hang_cd] = Array('name'=>$row['hang_name'], 'rows'=>0, 'LIST'=>Array());

		$data[$gwan_cd]['LIST'][$hang_cd]['LIST'][$mog_cd] = Array('name'=>$row['mog_name'], 'last_year'=>$budget[$year-1][$gwan_cd][$hang_cd][$mog_cd], 'this_year'=>$budget[$year][$gwan_cd][$hang_cd][$mog_cd]);
		$data[$gwan_cd]['rows'] ++;
		$data[$gwan_cd]['LIST'][$hang_cd]['rows'] ++;
	}

	$conn->row_free();

	unset($budget);

	$pdf->SetFont($pdf->font_name_kor, "", 9);

	$rowH = $pdf->row_height;
	$cnt = 0;

	if (is_array($data)){
		foreach($data as $gwan_cd => $gwan){
			foreach($gwan['LIST'] as $hang_cd => $hang){
				foreach($hang['LIST'] as $mog_cd => $mog){
					
					$cnt ++;
					$orgY = $pdf->GetY();

					if ($gwan['rows'] > 0){
						$border[0] = 'RLT';
						$pdf->SetXY($pdf->left,$orgY);
						$pdf->MultiCell($pdf->width * .16, 5, $gwan['name'], 0, 'L');
					}else{
						$border[0] = 'RL';
					}

					if ($hang['rows'] > 0){
						$border[1] = 'RLT';
						$pdf->SetXY($pdf->left+$pdf->width * .16,$orgY);
						$pdf->MultiCell($pdf->width * .16, 5, $hang['name'], 0, 'L');
					}else{
						$border[1] = 'RL';
					}
					
					if($rowCnt==$cnt){
						$border[0] = 'RLB';
						$border[1] = 'RLB';
					}
					
					
					
					$pdf->SetXY($pdf->left, $orgY);
					$pdf->Cell($pdf->width * .16, $rowH, '', $border[0], 0);
					$pdf->Cell($pdf->width * .16, $rowH, '', $border[1], 0);
					$pdf->Cell($pdf->width * .20, $rowH, $mog['name'], 1, 0);
					$pdf->Cell($pdf->width * .12, $rowH, number_format($mog['last_year']), 1, 0, 'R');
					$pdf->Cell($pdf->width * .12, $rowH, number_format($mog['this_year']), 1, 0, 'R');
					$pdf->Cell($pdf->width * .12, $rowH, number_format($mog['this_year'] - $mog['last_year']), 1, 0, 'R');
					$pdf->Cell($pdf->width * .12, $rowH, '', 1, 1);

					$gwan['rows'] = 0;
					$hang['rows'] = 0;

					
				}
			}	
		}
	}


?>