<?
	if (!Is_Array($var)){
		exit;
	}

	$lastDay= $myF->lastDay($var['year'], $var['month']);
	$today	= Date('Ymd');

	$sql = 'SELECT	cld_yymm
			,		cld_seq
			,		cld_no
			,		cld_dt
			,		cld_from
			,		cld_to
			,		TIMEDIFF(cld_to, cld_from) AS proctime
			,		cld_fulltime
			,		cld_subject
			,		cld_contents
			,		cld_reg_nm
			FROM	calendar
			WHERE	org_no	= \''.$var['code'].'\'
			AND		cld_yymm= \''.$var['year'].$var['month'].'\'
			AND		del_flag= \'N\'
			ORDER	BY cld_from';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$day = intval(substr($row['cld_dt'], 8, 2));
		$id  = sizeof($data[$day]);

		$data[$day][$id] = array('code'		=>$code
								,'yymm'		=>$row['cld_yymm']
								,'seq'		=>$row['cld_seq']
								,'no'		=>$row['cld_no']
								,'date'		=>$row['cld_dt']
								,'from'		=>substr($row['cld_from'], 0, 5)
								,'to'		=>substr($row['cld_to'], 0, 5)
								,'proctime'	=>$row['proctime']
								,'fulltime'	=>$row['cld_fulltime']
								,'subject'	=>stripslashes($row['cld_subject'])
								,'contents'	=>stripslashes($row['cld_contents'])
								,'writer'	=>$row['cld_reg_nm']);

		$id ++;
	}

	$conn->row_free();

	$pdf->SetFont($pdf->font_name_kor,'',9);

	for($i=1; $i<=$lastDay; $i++){
		$day	= $i.' ';
		$weekly = Date('w',StrToTime($var['year'].'-'.$var['month'].'-'.$i));
		$cnt	= SizeOf($data[$i]);

		if (Empty($cnt)) $cnt = 1;

		$y = $pdf->GetY();

		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width*0.06,$pdf->row_height * $cnt,$day,'LTB',0,'R');

		switch($weekly){
			case '0':
				$weeklyStr = '일';
				$pdf->SetTextColor(255,0,0);
				break;

			case '1':
				$weeklyStr = '월';
				$pdf->SetTextColor(0,0,0);
				break;

			case '2':
				$weeklyStr = '화';
				$pdf->SetTextColor(0,0,0);
				break;

			case '3':
				$weeklyStr = '수';
				$pdf->SetTextColor(0,0,0);
				break;

			case '4':
				$weeklyStr = '목';
				$pdf->SetTextColor(0,0,0);
				break;

			case '5':
				$weeklyStr = '금';
				$pdf->SetTextColor(0,0,0);
				break;

			case '6':
				$weeklyStr = '토';
				$pdf->SetTextColor(0,0,255);
				break;
		}

		$pdf->Cell($pdf->width*0.04,$pdf->row_height * $cnt,$weeklyStr,'RTB',0,'L');
		$pdf->SetTextColor(0,0,0);

		$x = $pdf->left + $pdf->width * 0.1;

		if (Is_Array($data[$i])){
			//$pdf->SetY($y);

			foreach($data[$i] as $arr){
				if ($arr['fulltime'] == 'Y'){
					$time = '종일일정';
				}else{
					$temptime = explode(':', $arr['proctime']);
					$time = $arr['from'].'('.(intval($temptime[0]) > 0 ? intval($temptime[0]).'시간 ' : '').(intval($temptime[1]) > 0 ? intval($temptime[1]).'분' : '').')';
				}

				$pdf->SetX($x);
				$pdf->Cell($pdf->width*0.2,$pdf->row_height,$time,1,0,'L');
				$pdf->Cell($pdf->width*0.15,$pdf->row_height,$arr['writer'],1,0,'L');
				$pdf->Cell($pdf->width*0.55,$pdf->row_height,StripSlashes($arr['subject']),1,1,'L');
			}
		}else{
			$pdf->Cell($pdf->width*0.9,$pdf->row_height,'',1,1,'C');
		}
	}
?>