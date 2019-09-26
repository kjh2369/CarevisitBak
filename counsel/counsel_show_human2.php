<?
	
	$jumin = $_POST['m_cd'] != '' ? $ed->de($_POST['m_cd']) : $ed->de($_POST['jumin']);
	$row_num_1 = 6; //입사전기록 데이터없을 시 행 카운트
	$row_num_2 = 5;	//교육이수,자격,상벌 데이터읎을 시 행 카운트
	

	/**************************************************

		직원정보

	**************************************************/
	/*
	$sql = 'select m00_mcode as cd
			,      m00_store_nm as nm
			  from m00center
			 where m00_mcode  = \''.$code.'\'
			   and m00_del_yn = \'N\'
			 order by m00_mkind
			 limit 1';

	$tmp = $conn->get_array($sql);

	$pdf->k_cd = $tmp['cd'];
	$pdf->k_nm = $tmp['nm'];
	
	unset($tmp);
	*/
	
	#최종학력
	$sql = "select case mem_edu_lvl when '1' then '중졸이하' when '3' then '고졸' when '5' then '대학중퇴' when '7' then '대졸이상' else ' ' end as eduLvl
			  from counsel_mem
			 where org_no  = '$code'
			   and mem_ssn = '$jumin'";
	$eduLvl = $conn->get_data($sql);
	
	$pdf->eduLvl = $eduLvl;
	
	$sql = 'select m02_yname as m_nm
			,      m02_ytel as m_mobile
			,	   m02_ytel2 as m_tel
			,	   m02_ypostno as m_postno
			,	   m02_yjuso1 as m_addr
			,	   m02_yjuso2 as m_add_dtl
			,      m02_yipsail as m_from_dt
			,      m02_ytoisail as m_retire
			,	   m02_picture as m_pic
			,      m02_memo as memo
			  from m02yoyangsa
			 where m02_ccode  = \''.$code.'\'
			   and m02_yjumin = \''.$jumin.'\'
			   and m02_del_yn = \'N\'
			 order by m02_mkind
			 limit 1';
	
	$tmp = $conn->get_array($sql);
	
	$sql = 'select *
			  from mem_his
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			 order by seq desc
			 limit 1';

	$memHis = $conn->get_array($sql);


	$tmp_dt = date("Y-m-d",strtotime("+1 year, $memHis[join_dt]"));
	$toDate = date("Y.m.d",strtotime("-1 day, $tmp_dt"));
	
	$memo = $tmp['memo']; //특이사항

	$pdf->m_nm       = $tmp['m_nm'];
	$pdf->m_jumin = $myF->issStyle($jumin,'.');
	$pdf->m_mobile   = $myF->phoneStyle($tmp['m_mobile'],'.');
	$pdf->m_tel   = $myF->phoneStyle($tmp['m_tel'],'.');
	$pdf->m_addr  = '('.substr($tmp['m_postno'],0,3).'-'.substr($tmp['m_postno'],3,3).') '.$tmp['m_addr'].' '.$tmp['m_addr_dtl'];
	$pdf->m_from_dt  = $myF->dateStyle($memHis['join_dt'],'.');		//고용시작일
	$pdf->m_to_dt = $toDate;										//고용종료일
	$pdf->m_retire_dt  = $myF->dateStyle($memHis['quit_dt'],'.');	//퇴사일
	$pdf->m_picture = $tmp['m_pic'];
	unset($tmp);

	/**************************************************

		인사기록카드 PDF 출력

	**************************************************/
	$pdf->SetAutoPageBreak(false);
	$pdf->MY_ADDPAGE();

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 1, $pdf->row_height, '입 사 면 담 기 록', 1, 1, 'C', 1);
	

	/**************************************************
		기본 폰트 설정
	**************************************************/
	$pdf->SetFont($pdf->font_name_kor, '', 10);


	
	/**************************************************
		입사전기록
	**************************************************/
		$sql = 'select *
				  from counsel_record
				 where org_no   = \''.$code.'\'
				   and record_ssn  = \''.$jumin.'\'
				   and record_type = \'M_HUMAN\'';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		$pdf->SetX($pdf->left);

		/**************************************************
			리스트와 페이지 추가에 대한 높이계산
		**************************************************/
			$tmp   = get_height($pdf, $row_count, 1);
			$pos_h = $tmp[0];
			$tmp_h = $tmp[1];
			unset($tmp);
		/*************************************************/

		
		if($row_count > 0){

			//공백라인카운트
			$blank_cnt = abs($row_num_1 - $row_count);
			
			//라인5까지 높이계산
			$row_n =  $pos_h+ (($row_num_1 - $row_count) > 0 ? ($pdf->row_height*$blank_cnt) : 0);
			
			$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $row_n / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'입사전기록');
			$pdf->draw_header('rec', $pos_h+($pdf->row_height*$blank_cnt));
		
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				
				$pdf->SetX($pdf->left + $pdf->width * 0.130);
				$pdf->Cell($pdf->width * 0.260, $pdf->row_height, $myF->dateStyle($row['record_fm_dt'],'.').'~'.$myF->dateStyle($row['record_to_dt'],'.'), 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.200, $pdf->row_height, $row['record_job_nm'], 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.120, $pdf->row_height, $row['record_position'], 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.170, $pdf->row_height, $row['record_task'], 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.120, $pdf->row_height, $row['record_salary'], 1, 1, 'L');
				
				if ($pdf->GetY() + $pdf->row_height > $pdf->height){
					set_array_text($pdf, $pos);
					unset($pos);

					$pdf->MY_ADDPAGE();
					$pdf->SetX($pdf->left);
					$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $pos_h*$blank_cnt / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'입사전기록');
					$pdf->draw_header('rec', $pos_h*$blank_cnt / 2);
				}
			}
			
			if(($row_num_1-$row_count) > 0){
				for($i=0; $i<$blank_cnt; $i++){
					
					$pdf->SetX($pdf->left + $pdf->width * 0.130);
					$pdf->Cell($pdf->width * 0.260, $pdf->row_height, '', 1, 0, 'C');
					$pdf->Cell($pdf->width * 0.200, $pdf->row_height, '', 1, 0, 'C');
					$pdf->Cell($pdf->width * 0.120, $pdf->row_height, '', 1, 0, 'C');
					$pdf->Cell($pdf->width * 0.170, $pdf->row_height, '', 1, 0, 'C');
					$pdf->Cell($pdf->width * 0.120, $pdf->row_height, '', 1, 1, 'C');
					
				}	
			}			
		}else {
			
			$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + ($pos_h+$pdf->row_height*$row_num_1) / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'입사전기록');
			$pdf->draw_header('rec', ($pos_h+$pdf->row_height*$row_num_1));
			
			for($i=0; $i<$row_num_1; $i++){
				$pdf->SetX($pdf->left + $pdf->width * 0.130);
				$pdf->Cell($pdf->width * 0.260, $pdf->row_height, '~', 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.200, $pdf->row_height, '', 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.120, $pdf->row_height, '', 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.170, $pdf->row_height, '', 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.120, $pdf->row_height, '', 1, 1, 'C');
			}
		}
		
		/*
		if($row_count < 5){
			$blank_cnt = 5 - $row_count;
			
			$tmp_h = ($pdf->row_height*($blank_cnt+1));

			$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $tmp_h / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'입사전기록');
			$pdf->draw_header('rec', $tmp_h);
		
			for($i=0; $i<$blank_cnt; $i++){
				$pdf->SetX($pdf->left + $pdf->width * 0.130);
				$pdf->Cell($pdf->width * 0.260, $pdf->row_height, $myF->dateStyle($row['record_fm_dt'],'.').'~'.$myF->dateStyle($row['record_to_dt'],'.'), 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.200, $pdf->row_height, $row['record_job_nm'], 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.120, $pdf->row_height, $row['record_position'], 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.170, $pdf->row_height, $row['record_task'], 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.120, $pdf->row_height, $row['record_salary'], 1, 1, 'C');
				
						
				if ($pdf->GetY() + $pdf->row_height > $pdf->height){
					set_array_text($pdf, $pos);
					unset($pos);
					
					$pdf->MY_ADDPAGE();
					$pdf->SetX($pdf->left);
					$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $tmp_h / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'입사전기록');
					$pdf->draw_header('rec', $tmp_h);
				}
			}	
		}
		*/

		$conn->row_free();
	/*************************************************/

	/**************************************************
		교육이수 사항(돌봄관련교육)
	**************************************************/
		$sql = 'select *
				  from counsel_edu
				 where org_no   = \''.$code.'\'
				   and edu_ssn  = \''.$jumin.'\'
				   and edu_type = \'M_HUMAN\'
				 union all
				select *
				  from counsel_edu
				 where org_no   = \''.$code.'\'
				   and edu_ssn  = \''.$jumin.'\'
				   and edu_type = \'1\'';
		
		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();
		
		/**************************************************
		교육이수 출력
	**************************************************/
		$pdf->SetX($pdf->left);

		/**************************************************
			리스트와 페이지 추가에 대한 높이계산
		**************************************************/
			$tmp   = get_height($pdf, $row_count, 1);
			$pos_h = $tmp[0];
			$tmp_h = $tmp[1];
			unset($tmp);
		/*************************************************/

		if($row_count > 0){
			
			
			//공백라인카운트
			$blank_cnt = abs($row_num_2 - $row_count);
			
			//라인5까지 높이계산
			$row_n =  $pos_h+ (($row_num_2 - $row_count) > 0 ? ($pdf->row_height*$blank_cnt) : 0);
			
			$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $row_n / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'교육이수');
			$pdf->draw_header('edu', $pos_h+($pdf->row_height*$blank_cnt));

			/*
			$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $pos_h / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'교육이수');
			$pdf->draw_header('edu', $pos_h);
			*/

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				
				$edu_gbn = $row['edu_gbn'] == '1' ? '돌봄관련교육' : '기타교육';

				$pdf->SetX($pdf->left + $pdf->width * 0.130);
				$pdf->Cell($pdf->width * 0.120, $pdf->row_height, $edu_gbn, 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.180, $pdf->row_height, $pdf->_splitTextWidth($myF->utf($row['edu_center']), $pdf->width * 0.180), 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.200, $pdf->row_height, $pdf->_splitTextWidth($myF->utf($row['edu_nm']), $pdf->width * 0.200), 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.260, $pdf->row_height, $myF->dateStyle($row['edu_from_dt'],'.').'~'.$myF->dateStyle($row['edu_to_dt'],'.'), 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.110, $pdf->row_height, $pdf->_splitTextWidth($myF->utf($row['edu_time']), $pdf->width * 0.110), 1, 1, 'L');

				if ($pdf->GetY() + $pdf->row_height > $pdf->height){
					/**************************************************
						테두리
					**************************************************/
						$pdf->SetLineWidth(0.6);
						$pdf->Line($pdf->left + $pdf->width * 0.565, $pdf->GetY() - $pos_h, $pdf->left + $pdf->width * 0.565, $pdf->GetY());
						$pdf->SetLineWidth(0.2);
					/*************************************************/

					set_array_text($pdf, $pos);
					unset($pos);

					$pdf->MY_ADDPAGE();
					$pdf->SetX($pdf->left);
					$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $tmp_h / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'교육이수');
					$pdf->draw_header('edu', $tmp_h);

					$pos_h = $tmp_h;
				}
			}
			
			if(($row_num_2-$row_count) > 0){
				for($i=0; $i<$blank_cnt; $i++){
					$pdf->SetX($pdf->left + $pdf->width * 0.130);
					$pdf->Cell($pdf->width * 0.120, $pdf->row_height, '', 1, 0, 'C');
					$pdf->Cell($pdf->width * 0.180, $pdf->row_height, '', 1, 0, 'L');
					$pdf->Cell($pdf->width * 0.200, $pdf->row_height, '', 1, 0, 'L');
					$pdf->Cell($pdf->width * 0.260, $pdf->row_height, '~', 1, 0, 'C');
					$pdf->Cell($pdf->width * 0.110, $pdf->row_height, '', 1, 1, 'L');

				}
			}

			$conn->row_free();
		}else {
			
			$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + ($pos_h+$pdf->row_height*$row_num_2) / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'교육이수');
			$pdf->draw_header('edu', ($pos_h+$pdf->row_height*$row_num_2));
			
			for($i=0; $i<$row_num_2; $i++){
				$pdf->SetX($pdf->left + $pdf->width * 0.130);
				$pdf->Cell($pdf->width * 0.120, $pdf->row_height, '', 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.180, $pdf->row_height, '', 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.200, $pdf->row_height, '', 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.260, $pdf->row_height, '~', 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.110, $pdf->row_height, '', 1, 1, 'C');
			}
			
		}

		unset($list);
	/*************************************************/


	/**************************************************
		테두리
	**************************************************/
		/*
		$pdf->SetLineWidth(0.6);
		$pdf->Line($pdf->left + $pdf->width * 0.565, $pdf->GetY() - $pos_h, $pdf->left + $pdf->width * 0.565, $pdf->GetY());
		$pdf->SetLineWidth(0.2);
		*/
	/*************************************************/



	/**************************************************
		자격사항
	**************************************************/
		$sql = 'select *
				  from counsel_license
				 where org_no       = \''.$code.'\'
				   and license_ssn  = \''.$jumin.'\'
				   and license_type = \'M_HUMAN\'
				 union all
				select *
				  from counsel_license
				 where org_no       = \''.$code.'\'
				   and license_ssn  = \''.$jumin.'\'
				   and license_type = \'1\'';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		$pdf->SetX($pdf->left);

		/**************************************************
			리스트와 페이지 추가에 대한 높이계산
		**************************************************/
			$tmp   = get_height($pdf, $row_count, 1);
			$pos_h = $tmp[0];
			$tmp_h = $tmp[1];
			unset($tmp);
		/*************************************************/

		if($row_count > 0){
			
			//공백라인카운트
			$blank_cnt = abs($row_num_2 - $row_count);
			
			//라인5까지 높이계산
			$row_n =  $pos_h+ (($row_num_2 - $row_count) > 0 ? ($pdf->row_height*$blank_cnt) : 0);
			
			$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $row_n / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'자격');
			$pdf->draw_header('lcs', $pos_h+($pdf->row_height*$blank_cnt));

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				$pdf->SetX($pdf->left + $pdf->width * 0.130);
				$pdf->Cell($pdf->width * 0.220, $pdf->row_height, $pdf->_splitTextWidth($myF->utf($row['license_gbn']), $pdf->width * 0.225), 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.215, $pdf->row_height, $pdf->_splitTextWidth($myF->utf($row['license_no']), $pdf->width * 0.220), 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.290, $pdf->row_height, $pdf->_splitTextWidth($myF->utf($row['license_center']), $pdf->width * 0.295), 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.145, $pdf->row_height, $myF->dateStyle($row['license_dt'],'.'), 1, 1, 'C');

				if ($pdf->GetY() + $pdf->row_height > $pdf->height){
					set_array_text($pdf, $pos);
					unset($pos);

					$pdf->MY_ADDPAGE();
					$pdf->SetX($pdf->left);
					$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $tmp_h / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'자격');
					$pdf->draw_header('lcs', $tmp_h);
				}
			}
			
			if((5-$row_count) > 0){
				for($i=0; $i<$blank_cnt; $i++){
					$pdf->SetX($pdf->left + $pdf->width * 0.130);
					$pdf->Cell($pdf->width * 0.220, $pdf->row_height, '', 1, 0, 'L');
					$pdf->Cell($pdf->width * 0.215, $pdf->row_height, '', 1, 0, 'L');
					$pdf->Cell($pdf->width * 0.290, $pdf->row_height, '', 1, 0, 'L');
					$pdf->Cell($pdf->width * 0.145, $pdf->row_height, '', 1, 1, 'C');
				}
			}

		}else {
			
			$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + ($pos_h+$pdf->row_height*$row_num_2) / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'자격');
			$pdf->draw_header('lcs', ($pos_h+$pdf->row_height*$row_num_2));

			for($i=0; $i<$row_num_2; $i++){
				$pdf->SetX($pdf->left + $pdf->width * 0.130);
				$pdf->Cell($pdf->width * 0.220, $pdf->row_height, '', 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.215, $pdf->row_height, '', 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.290, $pdf->row_height, '', 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.145, $pdf->row_height, '', 1, 1, 'C');
			}
		}
		$conn->row_free();
	/*************************************************/



	/**************************************************
		상벌 사항
	**************************************************/
		$sql = 'select *
				  from counsel_rnp
				 where org_no   = \''.$code.'\'
				   and rnp_ssn  = \''.$jumin.'\'
				   and rnp_type = \'M_HUMAN\'';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		$pdf->SetX($pdf->left);

		/**************************************************
			리스트와 페이지 추가에 대한 높이계산
		**************************************************/
			$tmp   = get_height($pdf, $row_count, 1);
			$pos_h = $tmp[0];
			$tmp_h = $tmp[1];
			unset($tmp);
		/*************************************************/

		

		if($row_count > 0){
			
			//공백라인카운트
			$blank_cnt = abs(5 - $row_count);
			
			//라인5까지 높이계산
			$row_n =  $pos_h+ (($row_num_2 - $row_count) > 0 ? ($pdf->row_height*$blank_cnt) : 0);
			
			$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $row_n / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'상벌');
			$pdf->draw_header('rnp', $pos_h+($pdf->row_height*$blank_cnt));

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				$pdf->SetX($pdf->left + $pdf->width * 0.130);
				$pdf->Cell($pdf->width * 0.145, $pdf->row_height, $myF->dateStyle($row['rnp_date'],'.'), 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.100, $pdf->row_height, $row['rnp_gbn'] == 'R' ? '포상' : '징계', 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.625, $pdf->row_height, $pdf->_splitTextWidth($myF->utf($row['rnp_comment']), $pdf->width * 0.630), 1, 1, 'L');

				if ($pdf->GetY() + $pdf->row_height > $pdf->height){
					set_array_text($pdf, $pos);
					unset($pos);

					$pdf->MY_ADDPAGE();
					$pdf->SetX($pdf->left);
					$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $tmp_h / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'상벌');
					$pdf->draw_header('rnp', $tmp_h);
				}
			}

			if((5-$row_count) > 0){
				for($i=0; $i<$blank_cnt; $i++){
					$pdf->SetX($pdf->left + $pdf->width * 0.130);
					$pdf->Cell($pdf->width * 0.145, $pdf->row_height, '', 1, 0, 'C');
					$pdf->Cell($pdf->width * 0.100, $pdf->row_height, '', 1, 0, 'C');
					$pdf->Cell($pdf->width * 0.625, $pdf->row_height, '', 1, 1, 'L');
				}
			}
		}else {
			
			$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + ($pos_h+$pdf->row_height*5) / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'상벌');
			$pdf->draw_header('rnp', ($pos_h+$pdf->row_height*5));
			
			for($i=0; $i<5; $i++){
				$pdf->SetX($pdf->left + $pdf->width * 0.130);
				$pdf->Cell($pdf->width * 0.145, $pdf->row_height, '', 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.100, $pdf->row_height, '', 1, 0, 'C');
				$pdf->Cell($pdf->width * 0.625, $pdf->row_height, '', 1, 1, 'L');
			}
		}

		$conn->row_free();
	/*************************************************/

		if($pdf->getY() > $pdf->height-20){
			set_array_text($pdf, $pos);
			unset($pos);
			
			$pdf->MY_ADDPAGE();
		}
		
		
		$pos[sizeof($pos)] = array('x'=>$pdf->GetX()+$pdf->width*0.150, 'y'=>$pdf->GetY(), 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.870, 'height'=>4, 'align'=>'L', 'text'=>$memo);
		
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.130, $pdf->row_height*4, '특이사항', 1, 0, 'C', 1);
		$pdf->Cell($pdf->width * 0.870, $pdf->row_height*4, '', 1, 0, 'C');
		

	/**************************************************

		기타 텍스트 출력 부분

		x         : X좌표
		y         : Y좌표
		type      : 출력형식
		width     :
		height    :
		font_size :
		align     :
		border	  :
		text      : 출력텍스트

	**************************************************/
	set_array_text($pdf, $pos);
	unset($pos);



	/**************************************************
		메모리 해제
	**************************************************/
	unset($list);



	/**************************************************

		교육이수 배열 초기화

	**************************************************/
	function init_edu(){
		$tmp['ct'] = '';
		$tmp['nm'] = '';
		$tmp['dt'] = '';

		return $tmp;
	}

	/**************************************************

		리스트와 페이지 추가에 대한 높이계산

	**************************************************/
	function get_height($pdf, $row_cnt, $head_cnt){
		//높이를 구한다.
		if ($pdf->GetY() + $pdf->row_height * ($row_cnt + $head_cnt) > $pdf->height){
			$pos_h     = $pdf->height - $pdf->GetY();
			$pos_tmp_h = $pdf->row_height * ($row_cnt + $head_cnt) - $pos_h + ($pdf->row_height * $head_cnt);
		}else{
			$pos_h     = $pdf->row_height * ($row_cnt + $head_cnt);
			$pos_tmp_h = 0;
		}

		return array($pos_h, $pos_tmp_h);
	}
?>