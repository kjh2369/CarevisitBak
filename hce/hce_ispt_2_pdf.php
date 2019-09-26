<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사정기록지-가계도
	 *********************************************************/
	$conn->fetch_type = 'assoc';
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$orgNo = $_SESSION['userCenterCode'];

	$sql = 'SELECT	family_remark
			,		ecomap_remark
			,		remark
			,		family_path
			,		eco_path
			FROM	hce_map
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$row = $conn->get_array($sql);

	//가계도
	$mapFile= $hce->IPIN.'_'.$hce->rcpt.'.jpg';
	$img1	= '../hce/map/'.$orgNo.'/'.$hce->SR.'/'.$mapFile;
	$img2	= '../hce/eco/'.$orgNo.'/'.$hce->SR.'/'.$mapFile;

	if ($debug){
		if ($row['family_path']) $img1 = $row['family_path'];
		if ($row['eco_path']) $img2 = $row['eco_path'];

		if (!is_file($img1)) $img1 = '';
		if (!is_file($img2)) $img2 = '';
	}

	#$tmpImg = getImageSize('../mem_picture/'.$iconJikin);
	#$pdf->Image('../mem_picture/'.$iconJikin, $left + $draw_w + ($side_show ? -1 : -9) - $pdf->left, $pdf->GetY() + ($side_show ? 5 : 7), ($side_show ? 16.5 : 25));

	$col[] = $pdf->width * 0.1;
	$col[] = $pdf->width * 0.9;
	
	$top = $pdf->top+$totrH;
	$width	= $col[1];
	$height	= $pdf->row_height*12;
	$X = $pdf->left+$col[0]+$col[1] * 0.03 / 2;
	$Y = $top + $col[1] / 2 * 0.03 / 2;
	$W = $col[1] * 0.97;
	$H = $col[1] / 2 * 0.97;

	$pos[] = Array('X'=>$pdf->left+$col[0],'Y'=>$top + $col[1] / 2,'width'=>$col[1],'align'=>'L','text'=>$row['family_remark']);

	$pdf->SetXY($pdf->left,$top);
	$pdf->Cell($col[0],$pdf->row_height*3+$col[1]/2,"가계도",1,0,'C',1);
	$pdf->Cell($col[1],$col[1]/2,"",1,1,'C');

	if (is_file($img1)){
		//$tmpImg = getImageSize($img1);
		$pdf->Image($img1, $X, $Y, $W, $H);
	}

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height*3,"",1,1,'C');

	$X = $pdf->left+$col[0]+$col[1] * 0.03 / 2;
	$Y = $pdf->GetY() + $col[1] / 2 * 0.03 / 2;

	$pos[] = Array('X'=>$pdf->left+$col[0],'Y'=>$pdf->GetY() + $col[1] / 2,'width'=>$col[1],'align'=>'L','text'=>$row['ecomap_remark']);

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height*3+$col[1]/2,"생태도",1,0,'C',1);
	$pdf->Cell($col[1],$col[1]/2,"",1,1,'C');

	if (is_file($img2)){
		//$tmpImg = getImageSize($img2);
		$pdf->Image($img2, $X, $Y, $W, $H);
	}

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height*3,"",1,1,'C');

	$pos[] = Array('X'=>$pdf->left+$col[0],'Y'=>$pdf->GetY(),'width'=>$col[1],'align'=>'L','text'=>$row['remark']);

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],($pdf->row_height*9)-$totrH,"비   고",1,0,'C',1);
	$pdf->Cell($col[1],($pdf->row_height*9)-$totrH,"",1,1,'C');
	
	

	Unset($row);


	foreach($pos as $row){
		$pdf->SetXY($row['X'],$row['Y']);
		$pdf->MultiCell($row['width'], 3.5, $row['text'], 0, ($row['align'] ? $row['align'] : 'C'));
	}

	Unset($col);
	Unset($pos);
?>