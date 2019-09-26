<?
	if (!Is_Array($var)){
		exit;
	}

	$coordX = $pdf->left;
	$ccordY = $pdf->GetY();

	$x = $coordX;
	$y = $ccordY;

	$w1 = $pdf->width / 8;
	$wg = $w1 * 0.1;
	$w  = $w1 - $wg;
	$h1 = $pdf->row_height*0.6;
	$date = $var['fromDt'];

	for($i=0; $i<7; $i++){
		$pdf->SetFont($pdf->font_name_kor,'B',13);
		$day = IntVal(Date('d', StrToTime($date)));
		$weekly = Date('w', StrToTime($date));

		if (Date('Ym',StrToTime($date)) == $var['year'].$var['month']){
			$thisYm = true;
		}else{
			$thisYm = false;
		}

		switch($weekly){
			case '0':
				$weeklyStr = '일';
				if ($thisYm){
					$pdf->SetTextColor(255,0,0);
				}else{
					$pdf->SetTextColor(240,94,100);
				}
				break;

			case '1':
				$weeklyStr = '월';
				if ($thisYm){
					$pdf->SetTextColor(0,0,0);
				}else{
					$pdf->SetTextColor(65,64,73);
				}
				break;

			case '2':
				$weeklyStr = '화';
				if ($thisYm){
					$pdf->SetTextColor(0,0,0);
				}else{
					$pdf->SetTextColor(65,64,73);
				}
				break;

			case '3':
				$weeklyStr = '수';
				if ($thisYm){
					$pdf->SetTextColor(0,0,0);
				}else{
					$pdf->SetTextColor(65,64,73);
				}
				break;

			case '4':
				$weeklyStr = '목';
				if ($thisYm){
					$pdf->SetTextColor(0,0,0);
				}else{
					$pdf->SetTextColor(65,64,73);
				}
				break;

			case '5':
				$weeklyStr = '금';
				if ($thisYm){
					$pdf->SetTextColor(0,0,0);
				}else{
					$pdf->SetTextColor(65,64,73);
				}
				break;

			case '6':
				$weeklyStr = '토';
				if ($thisYm){
					$pdf->SetTextColor(0,0,255);
				}else{
					$pdf->SetTextColor(98,94,240);
				}
				break;
		}

		$pdf->SetDrawColor(0,0,0);
		$pdf->SetLineWidth(0.6);
		$pdf->SetXY($x,$y);
		$pdf->Cell($w*0.2,$pdf->row_height*2,$day.'','B',0,'L');
		$pdf->SetFont($pdf->font_name_kor,'',9);
		$pdf->Cell($w*0.8,$pdf->row_height*2,$weeklyStr,'B',1,'L');
		$pdf->SetLineWidth(0.2);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont($pdf->font_name_kor,'',7);

		for($j=0; $j<24; $j++){
			$t = $j * 60;
			$h = $t / 60;
			$m = $t % 60;

			for($k=0; $k<2; $k++){
				if ($k == 0){
					$str = ($h < 10 ? '0' : '').$h.':'.($m < 10 ? '0' : '').$m;
					$pdf->SetDrawColor(235,235,235);
				}else{
					$str = '';
					$pdf->SetDrawColor(160,160,160);
				}
				$pdf->SetX($x);
				$pos[$i][$j][$k] = Array('x'=>$pdf->GetX(),'y'=>$pdf->GetY());
				//echo $i.'_'.$j.'_'.$k.' : '.$pdf->GetX().'/'.$pdf->GetY().'<br>';
				$pdf->Cell($w,$h1,$str,'B',1,'L');
			}
		}

		$date = $myF->dateAdd('day',1,$date,'Y-m-d');

		$x += $w1;
	}

	$coordX = $x;

	$pdf->SetDrawColor(0,0,0);
	$pdf->SetLineWidth(0.6);
	$pdf->SetXY($coordX,$ccordY);
	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->Cell($w*1,$pdf->row_height*2,'종일일정','B',1,'L');
	$pdf->SetFont($pdf->font_name_kor,'',7);
	$pdf->SetLineWidth(0.2);

	$coordY = $pdf->GetY();

	$var['fromYm']	= SubStr($var['fromDt'],0,6);
	$var['toYm']	= SubStr($var['toDt'],0,6);
	$var['fromDt']	= $myF->dateStyle($var['fromDt']);
	$var['toDt']	= $myF->dateStyle($var['toDt']);

	$fromYm	= SubStr($from,0,6);
	$toYm	= SubStr($to,0,6);
	$fromDt	= $myF->dateStyle($from);
	$toDt	= $myF->dateStyle($to);

	$sql = 'SELECT	cld_seq
			,		cld_yymm
			,		cld_no
			,		cld_dt
			,		cld_from
			,		cld_to
			,		cld_fulltime
			,		cld_subject
			,		cld_contents
			,		cld_reg_nm
			FROM	calendar
			WHERE	org_no	= \''.$var['code'].'\'
			AND		cld_yymm= \''.$var['fromYm'].'\'
			AND		cld_dt BETWEEN \''.$var['fromDt'].'\' AND \''.$var['toDt'].'\'
			AND		del_flag= \'N\'';

	if ($fromYm != $toYm){
		$sql .= '
			UNION	ALL
			SELECT	cld_seq
			,		cld_yymm
			,		cld_no
			,		cld_dt
			,		cld_from
			,		cld_to
			,		cld_fulltime
			,		cld_subject
			,		cld_contents
			,		cld_reg_nm
			FROM	calendar
			WHERE	org_no	= \''.$var['code'].'\'
			AND		cld_yymm= \''.$var['fromYm'].'\'
			AND		cld_dt BETWEEN \''.$var['fromDt'].'\' AND \''.$var['toDt'].'\'
			AND		del_flag= \'N\'';
	}

	$sql .=	'
			ORDER	BY cld_dt,cld_from,cld_seq,cld_no';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$weekly	= Date('w',StrToTime($row['cld_dt']));
		$from	= $myF->time2min($row['cld_from']);
		$to		= $myF->time2min($row['cld_to']);

		if ($tmpDt == $row['cld_dt']){
			if (Is_Array($arr)){
				if (Is_Array($arr[$weekly])){
					foreach($arr[$weekly] as $tmpSeq => $arr2){
						$useYn = 'N';

						foreach($arr2 as $tmp){
							$lbAdd = true;

							if ($tmp['full'] != 'Y'){
								if ($tmp['dt'] == $row['cld_dt']){
									if ($useYn == 'N' && $tmp['from'] <= $from && $tmp['to'] > $from){
										$useYn = 'Y';
									}

									$tmpH1 = SubStr($myF->min2time($tmp['from']),0,2);
									$tmpH2 = SubStr($row['cld_from'],0,2);

									if ($useYn == 'Y' || $tmpH1 == $tmpH2){
										$seq = $tmpSeq;
										$lbAdd = false;
										break;
									}
								}
							}
						}
						if (!$lbAdd) break;
					}
				}
			}

			if ($lbAdd) $seq ++;
		}else{
			$tmpDt	= $row['cld_dt'];
			$seq ++;
		}

		$idx = SizeOf($arr[$weekly][$seq]);

		$arr[$weekly][$seq][$idx]	= Array(
			'id'	=>$row['cld_yymm'].'_'.$row['cld_seq'].'_'.$row['cld_no']
		,	'seq'	=>$row['cld_seq']
		,	'yymm'	=>$row['cld_yymm']
		,	'no'	=>$row['cld_no']
		,	'dt'	=>$row['cld_dt']
		,	'from'	=>$from
		,	'to'	=>$to
		,	'full'	=>$row['cld_fulltime']
		,	'title'	=>$row['cld_subject']
		,	'use'	=>'Y'
		);
	}

	$conn->row_free();

	$pdf->SetFillColor(233,233,233);
	$fullX = $coordX;
	$fullY = $coordY;

	if (Is_Array($arr)){
		foreach($arr as $arr1){
			foreach($arr1 as $arr2){
				$cnt = SizeOf($arr2);
				$x2  = 0;

				foreach($arr2 as $row){
					$weekly = IntVal(Date('w',StrToTime($row['dt'])));

					if ($row['full'] == 'Y'){
						if ($tmpDt != $row['dt']){
							$tmpDt = $row['dt'];

							switch($weekly){
								case '0':
									$weeklyStr = '일';
									if ($thisYm){
										$pdf->SetTextColor(255,0,0);
									}else{
										$pdf->SetTextColor(240,94,100);
									}
									break;

								case '1':
									$weeklyStr = '월';
									if ($thisYm){
										$pdf->SetTextColor(0,0,0);
									}else{
										$pdf->SetTextColor(65,64,73);
									}
									break;

								case '2':
									$weeklyStr = '화';
									if ($thisYm){
										$pdf->SetTextColor(0,0,0);
									}else{
										$pdf->SetTextColor(65,64,73);
									}
									break;

								case '3':
									$weeklyStr = '수';
									if ($thisYm){
										$pdf->SetTextColor(0,0,0);
									}else{
										$pdf->SetTextColor(65,64,73);
									}
									break;

								case '4':
									$weeklyStr = '목';
									if ($thisYm){
										$pdf->SetTextColor(0,0,0);
									}else{
										$pdf->SetTextColor(65,64,73);
									}
									break;

								case '5':
									$weeklyStr = '금';
									if ($thisYm){
										$pdf->SetTextColor(0,0,0);
									}else{
										$pdf->SetTextColor(65,64,73);
									}
									break;

								case '6':
									$weeklyStr = '토';
									if ($thisYm){
										$pdf->SetTextColor(0,0,255);
									}else{
										$pdf->SetTextColor(98,94,240);
									}
									break;
							}

							$pdf->SetXY($fullX,$fullY);
							$pdf->Cell($w*0.15,$h1,IntVal(Date('d',StrToTime($row['dt']))).' ','T',0,'R');
							$pdf->Cell($w*0.85,$h1,$weeklyStr,'T',1,'L');
							$pdf->SetTextColor(0,0,0);

							$fullY = $pdf->GetY();
						}

						$pdf->SetXY($fullX+2,$fullY);
						$pdf->Cell($w,$h1,$row['title'],0,1,'L');

						$fullY = $pdf->GetY();
					}else{
						$hour	= Floor($row['from'] / 60);
						$minute = ($row['from'] % 60) / 30;

						$x  = $pos[$weekly][$hour][$minute]['x']+$x2+0.5;
						$y1 = $pos[$weekly][$hour][$minute]['y']+0.5;

						$hour	= Floor($row['to'] / 60);
						$minute = ($row['to'] % 60) / 30;

						$y2 = $pos[$weekly][$hour][$minute]['y']-$y1-0.5;

						$pdf->SetXY($x,$y1);
						$pdf->Cell($w/$cnt-0.5,$y2,'',1,1,'L',1);
						$pdf->SetXY($x,$y1);
						$pdf->MultiCell($w/$cnt,3,$myF->min2time($row['from']).$row['title'],0,'L');

						$x2 += ($w/$cnt);
					}
				}
			}
		}
	}
?>