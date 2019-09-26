<?
	/**************************************************

		기관 및 직원정보

	**************************************************/
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

	$sql = 'select m02_yname as m_nm
			,      m02_ytel as m_mobile
			  from m02yoyangsa
			 where m02_ccode  = \''.$code.'\'
			   and m02_yjumin = \''.$jumin.'\'
			   and m02_del_yn = \'N\'
			 order by m02_mkind
			 limit 1';

	$tmp = $conn->get_array($sql);

	$pdf->m_nm       = $tmp['m_nm'];
	$pdf->m_birthday = $myF->issToBirthDay($jumin,'.');
	$pdf->m_mobile   = $myF->phoneStyle($tmp['m_mobile'],'.');

	unset($tmp);




	/**************************************************

		인적자원관리 PDF 출력

	**************************************************/
	$pdf->SetAutoPageBreak(false);
	$pdf->MY_ADDPAGE();




	/**************************************************
		기본 폰트 설정
	**************************************************/
	$pdf->SetFont($pdf->font_name_kor, '', 10);



	/**************************************************
		교육이수 사항(돌봄관련교육)
	**************************************************/
		$sql = 'select *
				  from counsel_edu
				 where org_no   = \''.$code.'\'
				   and edu_ssn  = \''.$jumin.'\'
				   and edu_type = \'M_HUMAN\'
				   and edu_gbn  = \'1\'
				 union all
				select *
				  from counsel_edu
				 where org_no   = \''.$code.'\'
				   and edu_ssn  = \''.$jumin.'\'
				   and edu_type = \'1\'
				   and edu_gbn  = \'1\'';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$list[$i][0] = init_edu();

			$list[$i][0]['ct'] = $row['edu_center'];
			$list[$i][0]['nm'] = $row['edu_nm'];
			$list[$i][0]['dt'] = $row['edu_time'];
		}

		$conn->row_free();
	/**************************************************
		교육이수 사항(기타교육)
	**************************************************/
		$sql = 'select *
				  from counsel_edu
				 where org_no   = \''.$code.'\'
				   and edu_ssn  = \''.$jumin.'\'
				   and edu_type = \'M_HUMAN\'
				   and edu_gbn  = \'9\'
				 union all
				select *
				  from counsel_edu
				 where org_no   = \''.$code.'\'
				   and edu_ssn  = \''.$jumin.'\'
				   and edu_type = \'1\'
				   and edu_gbn  = \'9\'';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$list[$i][1] = init_edu();

			$list[$i][1]['ct'] = $row['edu_center'];
			$list[$i][1]['nm'] = $row['edu_nm'];
			$list[$i][1]['dt'] = $row['edu_time'];
		}

		$conn->row_free();
	/**************************************************
		리스트 카운트
	**************************************************/
		$list_cnt = sizeof($list);
	/*************************************************/



	/**************************************************
		교육이수 출력
	**************************************************/
		$pdf->SetX($pdf->left);

		/**************************************************
			리스트와 페이지 추가에 대한 높이계산
		**************************************************/
			$tmp   = get_height($pdf, $list_cnt, 2);
			$pos_h = $tmp[0];
			$tmp_h = $tmp[1];
			unset($tmp);
		/*************************************************/

		$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $pos_h / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'교육이수');
		$pdf->draw_header('edu', $pos_h);

		for($i=0; $i<$list_cnt; $i++){
			$pdf->SetX($pdf->left + $pdf->width * 0.130);
			$pdf->Cell($pdf->width * 0.180, $pdf->row_height, $pdf->_splitTextWidth($myF->utf($list[$i][0]['ct']), $pdf->width * 0.180), 1, 0);
			$pdf->Cell($pdf->width * 0.180, $pdf->row_height, $pdf->_splitTextWidth($myF->utf($list[$i][0]['nm']), $pdf->width * 0.180), 1, 0);
			$pdf->Cell($pdf->width * 0.075, $pdf->row_height, $list[$i][0]['dt'], 1, 0);
			$pdf->Cell($pdf->width * 0.180, $pdf->row_height, $pdf->_splitTextWidth($myF->utf($list[$i][1]['ct']), $pdf->width * 0.180), 1, 0);
			$pdf->Cell($pdf->width * 0.180, $pdf->row_height, $pdf->_splitTextWidth($myF->utf($list[$i][1]['nm']), $pdf->width * 0.180), 1, 0);
			$pdf->Cell($pdf->width * 0.075, $pdf->row_height, $list[$i][1]['dt'], 1, 1);

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

		unset($list);
	/*************************************************/


	/**************************************************
		테두리
	**************************************************/
		$pdf->SetLineWidth(0.6);
		$pdf->Line($pdf->left + $pdf->width * 0.565, $pdf->GetY() - $pos_h, $pdf->left + $pdf->width * 0.565, $pdf->GetY());
		$pdf->SetLineWidth(0.2);
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

		$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $pos_h / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'자격');
		$pdf->draw_header('lcs', $pos_h);

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

		$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $pos_h / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.130, 'height'=>1, 'align'=>'C', 'text'=>'상벌');
		$pdf->draw_header('rnp', $pos_h);

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

		$conn->row_free();
	/*************************************************/





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