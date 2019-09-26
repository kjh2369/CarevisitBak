<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$conn->fetch_type = 'assoc';

	$orgNo = $_SESSION['userCenterCode'];
	$year = $var['year'];
	$re_gbn = $var['re_gbn'];
	
	//echo $var['year'];
	
	//결제라인
	/*
	$sql = 'SELECT	org_name
			FROM	ltcf_center
			WHERE	org_no = \''.$orgNo.'\'
			';
	$org_name = $myF->euckr($conn->get_data($sql));

	$sql = 'SELECT	line_name,sign_cd
			FROM	ltcf_appline
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		use_flag = \'Y\'
			AND		del_flag = \'N\'
			ORDER	BY seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$line_name[] = $myF->euckr($row['line_name']);
		$sign_cd[] = $row['sign_cd'];
	}
	*/

	
	//$pdf->MY_ADDPAGE();
	

	$sql = 'SELECT	a.gwan_cd, a.hang_cd, a.mog_cd, a.gwan_name, a.hang_name, a.mog_name, b.amt
			FROM	fa_item AS a
			LEFT	JOIN	fa_budget AS b
					ON		b.org_no	= \''.$orgNo.'\'
					AND		b.year		= \''.$year.'\'
					AND		b.gwan_cd	= a.gwan_cd
					AND		b.hang_cd	= a.hang_cd
					AND		b.mog_cd	= a.mog_cd
					AND		b.re_gbn	= a.re_gbn
			WHERE	a.re_gbn	= \''.$re_gbn.'\'
			AND		a.use_flag	= \'Y\'
			ORDER	BY gwan_cd, hang_cd, mog_cd
			';
	//echo nl2br($sql); 
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

		$data[$gwan_cd]['LIST'][$hang_cd]['LIST'][$mog_cd] = Array('name'=>$row['mog_name'], 'amt'=>$row['amt']);
		$data[$gwan_cd]['rows'] ++;
		$data[$gwan_cd]['LIST'][$hang_cd]['rows'] ++;
	}

	$conn->row_free();

	//$pdf->font['size'] = $pdf->fontsize;
	//$pdf->FontStyle();
	
	$pdf->SetFont($pdf->font_name_kor, "", 9);

	$rowH = $pdf->row_height;

	if (is_array($data)){
		foreach($data as $gwan_cd => $gwan){
			foreach($gwan['LIST'] as $hang_cd => $hang){
				foreach($hang['LIST'] as $mog_cd => $mog){
					
					$orgY = $pdf->GetY();

					if ($gwan['rows'] > 0){
						$border[0] = 'RLT';
						$pdf->SetXY($pdf->left,$orgY);
						$pdf->MultiCell($pdf->width * .25, 5, $gwan['name'], 0, 'L');
					}else{
						$border[0] = 'RL';
					}

					if ($hang['rows'] > 0){
						$border[1] = 'RLT';
						$pdf->SetXY($pdf->left+$pdf->width * .25,$orgY);
						$pdf->MultiCell($pdf->width * .25, 5, $hang['name'], 0, 'L');
					}else{
						$border[1] = 'RL';
					}

					$pdf->SetXY($pdf->left, $orgY);
					$pdf->Cell($pdf->width * .25, $rowH, '', $border[0], 0);
					$pdf->Cell($pdf->width * .25, $rowH, '', $border[1], 0);
					$pdf->Cell($pdf->width * .25, $rowH, $mog['name'], 1, 0);
					$pdf->Cell($pdf->width * .25, $rowH, number_format($mog['amt']), 1, 1, 'R');

					$gwan['rows'] = 0;
					$hang['rows'] = 0;
				}
			}
		}
	}

?>