<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	
	$orgNo	= $_SESSION["userCenterCode"];
	$orgNm	= $_SESSION["userCenterName"];
	$SR		= $var['SR'];
	$year	= $var['year'];
	$month	= $var['month'];
	//$jumin	= $var['jumin'];
	$svcStatPrtYn = $var['svcStatPrtYn'];
	$data   = explode('?', $var['data']);
	$align  = $var['align'];
	
	
	$sql = 'SELECT jumin, org_type as mkind, date, suga_cd, resource_cd as mem_cd1, mem_cd as mem_cd2, contents
			FROM   care_works_log
			WHERE  org_no	= \''.$orgNo.'\'
			AND	   org_type	= \''.$SR.'\'';
	if($month){
		$sql .= ' AND		LEFT(date,6) = \''.$year.($month < 10 ? '0' : '').$month.'\'';
	}else {
		$sql .= ' AND		LEFT(date,4) = \''.$year.'\'';
	}
	
	
	//$log = $conn->_fetch_array($sql, 'jumin');

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		
		$log[$row['jumin']][$row['mkind']][$row['date']][$row['suga_cd']][$row['mem_cd1']][$row['mem_cd2']]['contents'] = $row['contents'];


	}

	$conn -> row_free();

	/*
	$sql = 'SELECT	*
			FROM	apprline_set
			WHERE	org_no	= \''.$orgNo.'\'
			AND		gbn		= \'01\'';
	$apprline = $conn->get_array($sql);

	if ($apprline){
		$apprline['name'] = Explode('|',$apprline['line_name']);
	}else{
		$apprline['prt_yn'] = 'Y';
		$apprline['line_cnt'] = 3;
		$apprline['name'][] = '담당';
		$apprline['name'][] = '팀장';
		$apprline['name'][] = '센터장';
	}
	*/

	
	
	if ($month){
		$fromM	= $month;
		$toM	= $month;
	}else{
		$fromM	= 1;
		$toM	= 12;
	}


	function lfSetArr($arr, $cd){
		$cnt = SizeOf($arr);
		$sel = -1;

		for($i=0; $i<$cnt; $i++){
			if ($arr[$i]['V']['cd'] == $cd){
				$sel = $i;
				break;
			}
		}

		return $sel;
	}


	$weekly = Array(0=>'일',1=>'월',2=>'화',3=>'수',4=>'목',5=>'금',6=>'토');
	
	/*
	if (!is_array($data)){
		$data[0] = 'jumin='.$_POST['jumin'];
	}
	*/
	$loopIdx = 0;
	$cnt = 0;
	if (is_array($data)){
	
		foreach($data as $tmpIdx => $R){
			parse_str($R,$R);
			
			$jumin = $ed->de($R['jumin']);
			
			if($jumin != ''){

				$sql = 'SELECT	DISTINCT m03_name
						FROM	m03sugupja
						WHERE	m03_ccode = \''.$orgNo.'\'
						AND		m03_jumin = \''.$jumin.'\'';
				$name = $conn->get_data($sql);
				
				if($month){
					$subject = $name.' 대상자 '.$year.'년 '.$month.'월 서비스내역';
				}else {
					$subject = $name.' 대상자 '.$year.'년 서비스내역';
				}
				
				$sql = 'SELECT	t01_sugup_date AS date
						,		t01_mkind as mkind
						,		LEFT(t01_sugup_date,4) AS year
						,		MID(t01_sugup_date,5,2) AS month
						,		MID(t01_sugup_date,7) AS day
						,		t01_sugup_yoil AS week
						,		t01_suga_code1 AS suga_cd
						,		a.suga_nm
						,	    t01_jumin AS jumin
						,		t01_yoyangsa_id1 AS res_cd
						,		t01_yoyangsa_id2 AS mem_cd
						,		t01_yname1 AS res_nm
						,		t01_yname2 AS mem_nm
						/*,		IFNULL(b.contents,c.content) AS contents*/
						,		content AS contents
						FROM	t01iljung
						INNER	JOIN (
								SELECT	org_no, suga_sr, suga_cd, suga_sub, suga_nm, from_dt, to_dt
								FROM	care_suga
								WHERE	org_no	= \''.$orgNo.'\'
								AND		suga_sr = \''.$SR.'\'
								UNION	ALL
								SELECT	\''.$orgNo.'\', \''.$SR.'\', LEFT(code,5), MID(code,6), name, from_dt, to_dt
								FROM	care_suga_comm
								) AS a
								ON		a.org_no = t01_ccode
								AND		a.suga_sr = t01_mkind
								AND		CONCAT(a.suga_cd,a.suga_sub)	 = t01_suga_code1
								AND		REPLACE(a.from_dt,\'-\',\'\')	<= t01_sugup_date
								AND		REPLACE(a.to_dt,\'-\',\'\')		>= t01_sugup_date
						/*
						LEFT	JOIN	care_works_log AS b
								ON		b.org_no	= t01_ccode
								AND		b.org_type	= t01_mkind
								AND		b.date		= t01_sugup_date
								AND		b.jumin		= t01_jumin
								AND		b.suga_cd	= t01_suga_code1
								AND		b.resource_cd = t01_yoyangsa_id1
								AND		b.mem_cd	= t01_yoyangsa_id2
						*/
						LEFT	JOIN	care_result AS c
								ON		c.org_no	= t01_ccode
								AND		c.org_type	= t01_mkind
								AND		c.jumin		= t01_jumin
								AND		c.date		= t01_sugup_date
								AND		c.time		= t01_sugup_fmtime
								AND		c.seq		= t01_sugup_seq
								AND		c.no		= \'1\'
						WHERE	t01_ccode		= \''.$orgNo.'\'
						AND		t01_mkind		= \''.$SR.'\'
						AND		t01_jumin		= \''.$jumin.'\'
						/*AND		t01_status_gbn	= \'1\'*/
						AND		t01_del_yn		= \'N\'';
						
						
						if($month){
							$sql .= ' AND		LEFT(t01_sugup_date,6) = \''.$year.($month < 10 ? '0' : '').$month.'\'';
						}else {
							$sql .= ' AND		LEFT(t01_sugup_date,4) = \''.$year.'\'';
						}
						
						if($align == 1){
							$sql .= ' ORDER	BY date DESC, suga_nm, res_nm, mem_nm';
						}else {
							$sql .= ' ORDER	BY date ASC, suga_nm, res_nm, mem_nm';
						}
				
				//if($debug) echo nl2br($sql); 
				
				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();
				
				
				if($rowCnt > 0){
				
					if($loopIdx > 0){
						$pdf->MY_ADDPAGE();
					}

					$pdf->SetXY($pdf->left, $pdf->top);
					
					if($var['dir'] == 'P'){
						$pdf->SetFont($pdf->font_name_kor,'B',15);
					}else {
						$pdf->SetFont($pdf->font_name_kor,'B',20);
					}
					

					$rowHeight = $pdf->row_height;

					
					/*
					$pdf->SetLineWidth(0.2);
					if ($pdf->sginCnt == ''){
						$pdf->sginCnt = 3;
						$pdf->sginTxt = Array(0=>'담당자', 1=>'', 2=>$tmpStr);
					}
					*/

					if($sginPrt == 'Y'){
						$tmpL = $pdf->_SinglineWidth();
						$pdf->Cell($pdf->width - $tmpL, $rowHeight * 4, $subject, 1, 1, 'C');
						$pdf->SetFont($pdf->font_name_kor, "", 11);
						$pdf->_SignlineSet();
					}else {
						$pdf->Cell($pdf->width, $rowHeight * 4, $subject, 1, 1, 'C');
						$pdf->SetFont($pdf->font_name_kor, "", 11);
					}
					
					$pdf->SetFont($pdf->font_name_kor, "", 10);

					if($var['dir'] == 'P'){
						$totH = 265;
					}else {
						$totH = 175;
					}

					if($month){
						if($var['dir'] == 'P'){
							$col[] = $pdf->width*0.08;
							$col[] = $pdf->width*0.22;
							$col[] = $pdf->width*0.22;
							$col[] = $pdf->width*0.1;
							$col[] = $pdf->width*0.38;
							
							$leng = 10;
						}else {
							$col[] = $pdf->width*0.06;
							$col[] = $pdf->width*0.18;
							$col[] = $pdf->width*0.18;
							$col[] = $pdf->width*0.1;
							$col[] = $pdf->width*0.48;
							$leng = 15;
						
						}	
						
						$pdf->SetXY($pdf->left, $pdf->GetY()+5);
						$pdf->Cell($col[0], $pdf->row_height, '일', 1, 0, 'C',true); 
						$pdf->Cell($col[1], $pdf->row_height, '서비스', 1, 0, 'C',true); 
						$pdf->Cell($col[2], $pdf->row_height, '자원', 1, 0, 'C',true); 
						$pdf->Cell($col[3], $pdf->row_height, '담당자', 1, 0, 'C',true); 
						$pdf->Cell($col[4], $pdf->row_height, '비고', 1, 1, 'C',true); 
					}else {
						if($var['dir'] == 'P'){
							$col[] = $pdf->width*0.06;
							$col[] = $pdf->width*0.08;
							$col[] = $pdf->width*0.21;
							$col[] = $pdf->width*0.21;
							$col[] = $pdf->width*0.08;
							$col[] = $pdf->width*0.36;
							
							$leng = 11;
						}else {
							$col[] = $pdf->width*0.06;
							$col[] = $pdf->width*0.06;
							$col[] = $pdf->width*0.16;
							$col[] = $pdf->width*0.16;
							$col[] = $pdf->width*0.08;
							$col[] = $pdf->width*0.48;

							$leng = 15;
						}

						
						$pdf->SetXY($pdf->left, $pdf->GetY()+5);
						$pdf->Cell($col[0], $pdf->row_height, '월', 1, 0, 'C',true); 
						$pdf->Cell($col[1], $pdf->row_height, '일', 1, 0, 'C',true); 
						$pdf->Cell($col[2], $pdf->row_height, '서비스', 1, 0, 'C',true); 
						$pdf->Cell($col[3], $pdf->row_height, '자원', 1, 0, 'C',true); 
						$pdf->Cell($col[4], $pdf->row_height, '담당자', 1, 0, 'C',true); 
						$pdf->Cell($col[5], $pdf->row_height, '비고', 1, 1, 'C',true); 
					}
				}
				
				
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);
					
					$content = ($log[$row['jumin']][$row['mkind']][$row['date']][$row['suga_cd']][$row['res_cd']][$row['mem_cd']]['contents'] != '' ? $log[$row['jumin']][$row['mkind']][$row['date']][$row['suga_cd']][$row['res_cd']][$row['mem_cd']]['contents'] : $row['contents']);	

					$mon[$i] = $row['month'];

					$y = $pdf->GetY();
					
					
					if($mon[$i-1] != $mon[$i]){
						$mons = $mon[$i];
						$border = 'LTR';
					}else {
						$mons = "";
						$border = 'LR';
					}
					
					
					if($pdf->GetY() > $totH){
						
						$pdf->line($pdf->left, $pdf->GetY(),$pdf->left+$col[0] , $pdf->GetY());
						
						$pdf->MY_ADDPAGE();
						
						$y = $pdf->GetY();
						
						$pdf->line($pdf->left, $pdf->GetY(),$pdf->left+$col[0] , $pdf->GetY());

						$mons = $mon[$i];
					}
					

					
					
					if(!$month){
						
						$high = get_row_cnt($pdf, $col[5]-2, $pdf->row_height, $content);	
					
						$pdf->SetXY($pdf->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4],$y+1);
						$pdf->MultiCell($col[5]+2, 4, $content);
						
						
						$pdf->SetXY($pdf->left,$y+1);
						$pdf->MultiCell($col[0], 4, ($mons != '' ? intval($mons).'월' : ''),"C","C");
						
						
						$pdf->SetXY($pdf->left, $y);	
						$pdf->Cell($col[0], $high, '', $border, 0, 'C'); 
						$pdf->Cell($col[1], $high, intval($row['day']).'('.$weekly[$row['week']].')', 1, 0, 'R'); 
						$pdf->Cell($col[2], $high, $myF->splits($row['suga_nm'],$leng), 1, 0); 
						$pdf->Cell($col[3], $high, $myF->splits($row['res_nm'],$leng) , 1, 0); 
						$pdf->Cell($col[4], $high, $row['mem_nm'], 1, 0); 
						$pdf->Cell($col[5], $high, '', 1, 1); 
					}else {
						
						$high = get_row_cnt($pdf, $col[4]-2, $pdf->row_height, $content);	
					
						$pdf->SetXY($pdf->left+$col[0]+$col[1]+$col[2]+$col[3],$y+1);
						$pdf->MultiCell($col[4]+2, 4, $content);

						$pdf->SetXY($pdf->left, $y);	
						$pdf->Cell($col[0], $high, intval($row['day']).'('.$weekly[$row['week']].')', 1, 0, 'R'); 
						$pdf->Cell($col[1], $high, $myF->splits($row['suga_nm'],$leng), 1, 0); 
						$pdf->Cell($col[2], $high, $myF->splits($row['res_nm'],$leng), 1, 0); 
						$pdf->Cell($col[3], $high, $row['mem_nm'], 1, 0); 
						$pdf->Cell($col[4], $high, '', 1, 1); 
					}
				
					#마지막 라인을 그린다.
					if($rowCnt == ($i+1)){
						$pdf->line($pdf->left, ($y+$high),$pdf->left+$col[0] , ($y+$high));
					}


					//서비스 현황 집계
					$cur1 = lfSetArr($SUM, $row['suga_cd']);

					if ($cur1 < 0){
						$SUM[]['V'] = Array('cd'=>$row['suga_cd'], 'nm'=>$row['suga_nm'], 'cnt'=>0);
						$cur1 = SizeOf($SUM) - 1;
					}
					
					$cur2 = lfSetArr($SUM[$cur1]['L'], $row['res_cd']);
					if ($cur2 < 0){
						$SUM[$cur1]['L'][]['V'] = Array('cd'=>$row['res_cd'], 'nm'=>$row['res_nm'], 'cnt'=>0);
						$cur2 = SizeOf($SUM[$cur1]['L']) - 1;
					}

					$cur3 = lfSetArr($SUM[$cur1]['L'][$cur2]['L'], $row['mem_cd']);
					if ($cur3 < 0){
						$SUM[$cur1]['L'][$cur2]['L'][]['V'] = Array('cd'=>$row['mem_cd'], 'nm'=>$row['mem_nm'], 'cnt'=>0);
						$cur3 = SizeOf($SUM[$cur1]['L'][$cur2]['L']) - 1;
					}

					$SUM[$cur1]['L'][$cur2]['L'][$cur3]['V']['cnt'] ++;
					$loopIdx ++;
					
				}
				

				$conn->row_free();
				
				Unset($col);
				

				if ($svcStatPrtYn == 'Y'){
					$col[] = $pdf->width*0.06;
					$col[] = $pdf->width*0.33;
					$col[] = $pdf->width*0.28;
					$col[] = $pdf->width*0.1;
					$col[] = $pdf->width*0.08;
					$col[] = $pdf->width*0.15;
					
					$pdf->SetXY($pdf->left, $pdf->GetY()+5);
					$pdf->Cell($col[0], $pdf->row_height, 'No', 1, 0, 'C',true); 
					$pdf->Cell($col[1], $pdf->row_height, '서비스', 1, 0, 'C',true); 
					$pdf->Cell($col[2], $pdf->row_height, '자원', 1, 0, 'C',true); 
					$pdf->Cell($col[3], $pdf->row_height, '담당자', 1, 0, 'C',true); 
					$pdf->Cell($col[4], $pdf->row_height, '횟수', 1, 0, 'C',true); 
					$pdf->Cell($col[5], $pdf->row_height, '비고', 1, 1, 'C',true); 
					

					if (is_array($SUM)){
						$sumCnt = SizeOf($SUM);

						for($i=0; $i<$sumCnt-1; $i++){
							for($j=$i+1; $j<$sumCnt; $j++){
								if ($SUM[$i]['V']['nm'] > $SUM[$j]['V']['nm']){
									$tmpSUM = $SUM[$i];
									$SUM[$i] = $SUM[$j];
									$SUM[$j] = $tmpSUM;
								}
							}
						}

						$no = 1;

						foreach($SUM as $tmp1 => $R1){
							foreach($R1['L'] as $tmp2 => $R2){
								foreach($R2['L'] as $tmp3 => $R3){
									$rowNo ++;
									

									if($pdf->GetY() > $totH){
						
										$pdf->MY_ADDPAGE();
										
									}

									$pdf->SetX($pdf->left);
									$pdf->Cell($col[0], $pdf->row_height, number_format($no), 1, 0, 'C'); 
									$pdf->Cell($col[1], $pdf->row_height, $myF->splits($R1['V']['nm'], 26), 1, 0, 'L'); 
									$pdf->Cell($col[2], $pdf->row_height, $myF->splits($R2['V']['nm'], 26), 1, 0, 'L'); 
									$pdf->Cell($col[3], $pdf->row_height, $R3['V']['nm'], 1, 0, 'L'); 
									$pdf->Cell($col[4], $pdf->row_height, $R3['V']['cnt'].'회', 1, 0, 'R'); 
									$pdf->Cell($col[5], $pdf->row_height, '', 1, 1, 'C'); 
									
									
									$no ++;
								}
							}
							
							Unset($SUM);

						}
					}		
				}
				
				Unset($col);

				$cnt++;	
			}
				
		}
	}
	
	

	if($cnt == 1 && $rowCnt == 0){
		
		$pdf->SetXY($pdf->left, $pdf->top);
		
		if($var['dir'] == 'P'){
			$pdf->SetFont($pdf->font_name_kor,'B',15);
		}else {
			$pdf->SetFont($pdf->font_name_kor,'B',20);
		}
		

		$rowHeight = $pdf->row_height;

		if($sginPrt == 'Y'){
			$tmpL = $pdf->_SinglineWidth();
			$pdf->Cell($pdf->width - $tmpL, $rowHeight * 4, $subject, 1, 1, 'C');
			$pdf->SetFont($pdf->font_name_kor, "", 11);
			$pdf->_SignlineSet();
		}else {
			$pdf->Cell($pdf->width, $rowHeight * 4, $subject, 1, 1, 'C');
			$pdf->SetFont($pdf->font_name_kor, "", 11);
		}
		
		$pdf->SetFont($pdf->font_name_kor, "", 10);

		if($var['dir'] == 'P'){
			$totH = 265;
		}else {
			$totH = 175;
		}

		if($month){
			if($var['dir'] == 'P'){
				$col[] = $pdf->width*0.08;
				$col[] = $pdf->width*0.22;
				$col[] = $pdf->width*0.22;
				$col[] = $pdf->width*0.1;
				$col[] = $pdf->width*0.38;
				
				$leng = 10;
			}else {
				$col[] = $pdf->width*0.06;
				$col[] = $pdf->width*0.18;
				$col[] = $pdf->width*0.18;
				$col[] = $pdf->width*0.1;
				$col[] = $pdf->width*0.48;
				$leng = 15;
			
			}	
			
			$pdf->SetXY($pdf->left, $pdf->GetY()+5);
			$pdf->Cell($col[0], $pdf->row_height, '일', 1, 0, 'C',true); 
			$pdf->Cell($col[1], $pdf->row_height, '서비스', 1, 0, 'C',true); 
			$pdf->Cell($col[2], $pdf->row_height, '자원', 1, 0, 'C',true); 
			$pdf->Cell($col[3], $pdf->row_height, '담당자', 1, 0, 'C',true); 
			$pdf->Cell($col[4], $pdf->row_height, '비고', 1, 1, 'C',true); 
		}else {
			if($var['dir'] == 'P'){
				$col[] = $pdf->width*0.06;
				$col[] = $pdf->width*0.08;
				$col[] = $pdf->width*0.21;
				$col[] = $pdf->width*0.21;
				$col[] = $pdf->width*0.08;
				$col[] = $pdf->width*0.36;
				
				$leng = 11;
			}else {
				$col[] = $pdf->width*0.06;
				$col[] = $pdf->width*0.06;
				$col[] = $pdf->width*0.16;
				$col[] = $pdf->width*0.16;
				$col[] = $pdf->width*0.08;
				$col[] = $pdf->width*0.48;

				$leng = 15;
			}

			
			$pdf->SetXY($pdf->left, $pdf->GetY()+5);
			$pdf->Cell($col[0], $pdf->row_height, '월', 1, 0, 'C',true); 
			$pdf->Cell($col[1], $pdf->row_height, '일', 1, 0, 'C',true); 
			$pdf->Cell($col[2], $pdf->row_height, '서비스', 1, 0, 'C',true); 
			$pdf->Cell($col[3], $pdf->row_height, '자원', 1, 0, 'C',true); 
			$pdf->Cell($col[4], $pdf->row_height, '담당자', 1, 0, 'C',true); 
			$pdf->Cell($col[5], $pdf->row_height, '비고', 1, 1, 'C',true); 
		}
		
	}

	include_once("../inc/_db_close.php");
?>