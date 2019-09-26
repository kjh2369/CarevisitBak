<?
	$pdf->SetFont($pdf->font_name_kor, '', 7);
	
	$sql = 'select t01_jumin as c_cd
			,      m03_name as c_nm
			,      t01_yoyangsa_id1 as m_cd1
			,      t01_yoyangsa_id2 as m_cd2
			,      left(t01_yname1, 3) as m_nm1
			,      left(t01_yname2, 3) as m_nm2
			,      cast(right(t01_sugup_date, 2) as unsigned) as svc_dt
			,      t01_sugup_fmtime as from_time
			,      t01_sugup_totime as to_time
			,      t01_svc_subcode as svc_cd
			,      t01_sugup_soyotime as plan_time
			,      t01_conf_soyotime as conf_time
			,      t01_conf_suga_value as conf_suga
			  from t01iljung
			 inner join m03sugupja
			    on m03_ccode = t01_ccode
			   and m03_mkind = t01_mkind
			   and m03_jumin = t01_jumin
			 where t01_ccode               = \''.$code.'\'
			   and left(t01_sugup_date, 6) = \''.$year.$month.'\'
			   and t01_status_gbn          = \'1\'
			   and t01_del_yn              = \'N\'   
			 order by c_nm, svc_cd, m_nm1, m_nm2, from_time, to_time';
			 
	$conn->query($sql);
	$conn->fetch();
	
	$row_count = $conn->row_count();
	
	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		
		if ($c_cd != $row['c_cd']){
			$c_cd  = $row['c_cd'];
			$c_id  = sizeof($c_if);
			$c_if[$c_id] = array('no'=>number_format($c_id+1), 'nm'=>$row['c_nm'], 'cnt'=>1);
			
			unset($m_cd);
		}
		
		
		if ($m_cd != $row['m_cd1'].'_'.$row['m_cd2']){
			$m_cd  = $row['m_cd1'].'_'.$row['m_cd2'];
			$m_id  = sizeof($m_if[$c_id]);
			$m_if[$c_id][$m_id] = array('nm'=>$row['m_nm1'].(!empty($row['m_nm2']) ? '/' : '').$row['m_nm2'], 'cnt'=>0);
			
			unset($s_cd);
		}
		
		if ($s_cd != $row['svc_cd'].'_'.$row['from_time'].'_'.$row['to_time']){
			$s_cd  = $row['svc_cd'].'_'.$row['from_time'].'_'.$row['to_time'];
			$s_id  = sizeof($s_if[$c_id][$m_id]);
			$s_if[$c_id][$m_id][$s_id] = array('from_time'=>$myF->timeStyle($row['from_time']), 'to_time'=>' ~ '.$myF->timeStyle($row['to_time']), 'svc'=>$myF->euckr($conn->kind_name_svc($row['svc_cd'])), 'hour'=>0, 'days'=>0, 'suga'=>0);
			$c_if[$c_id]['cnt'] ++;
			$m_if[$c_id][$m_id]['cnt'] ++; 
		}
		
		$s_if[$c_id][$m_id][$s_id]['suga'] += $row['conf_suga'];
		$s_if[$c_id][$m_id][$s_id]['hour'] += round($row['conf_time'] / 60,1);
		$s_if[$c_id][$m_id][$s_id]['days'] ++;
		
		$i_if[$c_id][$m_id][$s_id][$row['svc_dt']] = $row['conf_time'];
	}
	
	$conn->row_free();
	
	/**************************************************
	
		수급자
	
	**************************************************/
	$c_cnt = sizeof($c_if);
	
	for($c=0; $c<$c_cnt; $c++){
		$c_height = $pdf->row_height * $c_if[$c]['cnt'];
		
		if (empty($c_height)) $c_height = $pdf->row_height;
		
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.03, $c_height, $c_if[$c]['no'], 1, 0, 'C');
		$pdf->Cell($pdf->width * 0.07, $c_height, $c_if[$c]['nm'], 1, 0, 'C');
	
		
		
		/**************************************************
		
			요양보호사
		
		**************************************************/
		$m_cnt = sizeof($m_if[$c]);
		
		$hour = 0;
		$days = 0;
		$suga = 0;
		
		for($m=0; $m<$m_cnt; $m++){
			$m_height = $pdf->row_height * $m_if[$c][$m]['cnt'];
		
			if (empty($m_height)) $m_height = $pdf->row_height;
			
			$pdf->SetX($pdf->left + $pdf->width * 0.10);
			$pdf->Cell($pdf->width * 0.07, $m_height, $m_if[$c][$m]['nm'], 1, 0, 'C');
		
			
			
			/**************************************************
			
				수가
			
			**************************************************/
			$s_cnt = sizeof($s_if[$c][$m]);
			
			for($s=0; $s<$s_cnt; $s++){
				$pos_y = $pdf->GetY();
				
				$pdf->SetFont($pdf->font_name_kor, '', 6);
				$pdf->SetX($pdf->left + $pdf->width * 0.17);
				$pdf->Cell($pdf->width * (0.08 + $pdf->val_w), $pdf->row_height / 2, $s_if[$c][$m][$s]['from_time'], 'LTR', 2, 'L'); //서비스
				
				$pdf->SetX($pdf->left + $pdf->width * 0.17);
				$pdf->Cell($pdf->width * (0.08 + $pdf->val_w), $pdf->row_height / 2, $s_if[$c][$m][$s]['to_time'], 'LBR', 2, 'L'); //서비스
				
				$pdf->SetFont($pdf->font_name_kor, '', 7);
				$pdf->SetXY($pdf->left + $pdf->width * 0.17 + $pdf->GetStringWidth('~ 00:00'), $pos_y);
				$pdf->Cell($pdf->width * 0.02, $pdf->row_height, $s_if[$c][$m][$s]['svc'], 0, 0, 'L');
				
				$pdf->SetXY($pdf->left + $pdf->width * 0.17 + $pdf->width * (0.08 + $pdf->val_w), $pos_y);
				$pdf->Cell($pdf->width * 0.03, $pdf->row_height, str_replace('.0','',number_format($s_if[$c][$m][$s]['hour'],1)), 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.03, $pdf->row_height, number_format($s_if[$c][$m][$s]['days']), 1, 0, 'C');
			
			
				/**************************************************
				
					일정
				
				**************************************************/
				for($i=1; $i<=$pdf->lastday; $i++){
					$str = number_format($i_if[$c][$m][$s][$i]/60,1);
					$str = str_replace('.0', '', $str);
					
					if (empty($str)) $str = '';
					
					$pdf->Cell($pdf->width * 0.02, $pdf->row_height, $str, 1, 0, 'C');
				}
				
				/**************************************************
				
					급여합계
				
				**************************************************/
				$pdf->Cell($pdf->width * 0.07, $pdf->row_height, number_format($s_if[$c][$m][$s]['suga']), 1, 1, 'R');
				
				$hour += $s_if[$c][$m][$s]['hour'];
				$days += $s_if[$c][$m][$s]['days'];
				$suga += $s_if[$c][$m][$s]['suga'];
			}
		}
		
		
		
		/**************************************************
		
			소계
		
		**************************************************/
		$pdf->SetX($pdf->left + $pdf->width * 0.10);
		$pdf->Cell($pdf->width * (0.07 + (0.08 + $pdf->val_w)), $pdf->row_height, '소계', 1, 0, 'C');
		$pdf->Cell($pdf->width * 0.03, $pdf->row_height, str_replace('.0','',number_format($hour,1)), 1, 0, 'C');
		$pdf->Cell($pdf->width * 0.03, $pdf->row_height, number_format($days), 1, 0, 'C');
		$pdf->Cell($pdf->width * (0.02 * $pdf->lastday), $pdf->row_height, '', 1, 0, 'C');
		
		
		/**************************************************
		
			금액
		
		**************************************************/
		$pdf->Cell($pdf->width * 0.07, $pdf->row_height, number_format($suga), 1, 1, 'R');
	}
?>