<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');

	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $var['jumin'];
	$fromDt = str_replace('-','', $var['fromDt']);
	$toDt = str_replace('-','', $var['toDt']);

	$sql = 'SELECT	date
			,		jumin
			,	    comment
			,		command
			FROM	sw_log
			WHERE	org_no		= \''.$orgNo.'\'
			AND		reg_jumin	= \''.$jumin.'\'
			AND		date		>= \''.$fromDt.'\'
			AND		date		<= \''.$toDt.'\'
			AND		del_flag= \'N\'
			ORDER   BY date desc, time desc';
	//echo nl2br($sql); exit;
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	$colW = $pdf->width * 0.34;	
	$rowY = $pdf->row_height;


	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		

		//고객정보(주민,이름,연락처);
		$sql = 'select m03_name as name
				  from m03sugupja
				 where m03_ccode = \''.$orgNo.'\'
				   and m03_jumin   = \''.$row['jumin'].'\'';
		$clt_name = $conn -> get_data($sql);

		$high1 = get_row_cnt($pdf, $colW, $rowY, $row['comment']);
		$high2 = get_row_cnt($pdf, $colW, $rowY, $row['command']);
		
		if($high1 > $high2){
			$high = $high1;
		}else {
			$high = $high2;
		}

		if($pdf->GetY()+$high > '270'){
			
			set_array_text($pdf, $pos);
			unset($pos);

			$pdf->MY_ADDPAGE();
		}	

		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.31, 'y'=>$pdf->GetY(), 'font_size'=>9, 'type'=>'multi_text', 'width'=>$pdf->width * 0.34, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $row['comment'])));
		
		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.66, 'y'=>$pdf->GetY(), 'font_size'=>9, 'type'=>'multi_text', 'width'=>$pdf->width * 0.34, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $row['command'])));
		
		$pdf->SetXY($pdf->left, $pdf->GetY());
		$pdf->Cell($pdf->width*0.15, $high, $myF->dateStyle($row['date'],'.'), 1, 0, 'C');
		$pdf->Cell($pdf->width*0.15, $high, ' '.$clt_name, 1, 0, 'L');
		$pdf->Cell($pdf->width*0.35, $high, '', 1, 0, 'C');
		$pdf->Cell($pdf->width*0.35, $high, '', 1, 1, 'C');

			
	}

	$conn->row_free();

	set_array_text($pdf, $pos);
	unset($pos);

	include_once('../inc/_db_close.php');