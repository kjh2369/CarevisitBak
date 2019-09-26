<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$conn->fetch_type = 'assoc';

	$orgNo = $_SESSION['userCenterCode'];

	if ($var['mode'] == 'INTERVIEW_REG'){
		$var['mode'] = '21_0';
	}else if ($var['mode'] == 'INTERVIEW_REG_N'){
		$var['mode'] = '21_N';
	}

	if ($var['def'] == 'Y'){
		$tmpIPIN = $hce->IPIN;
		$hce->IPIN = 'DUMMY';
	}

	switch($var['mode']){
		case '1':
			//�����������
			include_once('../hce/hce_recepit_list_pdf.php');
			break;

		case '21':
			//�ʱ���������
			include_once('../hce/hce_interview_pdf.php');
			break;

		case '21_0':
			//�ʱ���������
			include_once('../hce/hce_interview_pdf.php');
			break;

		case '21_N':
			//�ʱ���������
			include_once('../hce/hce_interview_pdf.php');
			break;

		case '31':
			//���������
			if ($var['subId'] == 'ALL'){
				include_once('../hce/hce_inspection_pdf.php');
			}else{
				include_once('../hce/hce_ispt_'.$var['subId'].'_pdf.php');
			}
			break;

		case '41':
			//����� ��������ǥ

			$pdf->SetXY($pdf->left+$pdf->width*0.03, $pdf->top);
			$pdf->SetFont($pdf->font_name_kor,'B',18);
			
			$subject = '����� ��������ǥ';
			$disH = 0;
			
			$pdf->_SignHcelineSet($subject,$disH);
			
			$pdf->SetFont($pdf->font_name_kor,'B',9);

			include_once('../hce/hce_choice_pdf.php');
			break;

		case '51':
			//���ȸ�Ƿ�
			if ($var['subId'] == 'ALL'){
				$sql = 'SELECT	meet_seq
						FROM	hce_meeting
						WHERE	org_no	= \''.$orgNo.'\'
						AND		org_type= \''.$hce->SR.'\'
						AND		IPIN	= \''.$hce->IPIN.'\'
						AND		rcpt_seq= \''.$hce->rcpt.'\'
						AND     del_flag= \'N\'
						ORDER	BY meet_seq DESC';
				
				$arrMeet = $conn->_fetch_array($sql);
				$first = true;

				if (is_array($arrMeet)){
					foreach($arrMeet as $arrSeq){
						if (!$first) $pdf->MY_ADDPAGE();

						$var['idx'] = $arrSeq['meet_seq'];
						
						$pdf->SetXY($pdf->left+$pdf->width*0.03, $pdf->top);
						$pdf->SetFont($pdf->font_name_kor,'B',18);
						
						$subject = '�� �� ȸ �� ��';
						$disH = 0;
						
						$pdf->_SignHcelineSet($subject,$disH);
						
						$pdf->SetFont($pdf->font_name_kor,'B',9);

						include('../hce/hce_case_meeting_pdf.php');
						

						$first = false;
					}
				}

				Unset($arrMeet);
			}else{

				$pdf->SetXY($pdf->left, $pdf->top);
				$pdf->SetFont($pdf->font_name_kor,'B',18);

				$subject = '�� �� ȸ �� ��';
				$disH = 0;
				
				$pdf->_SignHcelineSet($subject,$disH);
				
				$pdf->SetFont($pdf->font_name_kor,'B',9);

				$col = $pdf->_colWidth();
				$rowH = $pdf->row_height * 1.5;

				$pdf->SetX($pdf->left);
				$pdf->Cell($col[0], $rowH, "ȸ��", 1, 0, "C", 1);
				$pdf->Cell($col[1], $rowH, "��������", 1, 0, "C", 1);
				$pdf->Cell($col[2], $rowH, "ȸ������", 1, 0, "C", 1);
				$pdf->Cell($col[3], $rowH, "������", 1, 0, "C", 1);
				$pdf->Cell($col[4], $rowH, "������", 1, 0, "C", 1);
				$pdf->Cell($col[5], $rowH, "��������", 1, 0, "C", 1);
				$pdf->Cell($col[6], $rowH, "��������", 1, 0, "C", 1);
				$pdf->Cell($col[7], $rowH, "���", 1, 1, "C", 1);

				include_once('hce_case_meeting_list_pdf.php');
			}
			break;

		case '52':
			//���ȸ�Ƿ�

			$pdf->SetXY($pdf->left+$pdf->width*0.03, $pdf->top);
			$pdf->SetFont($pdf->font_name_kor,'B',18);
			
			$subject = '�� �� ȸ �� ��';
			$disH = 0;
			
			$pdf->_SignHcelineSet($subject,$disH);
			
			$pdf->SetFont($pdf->font_name_kor,'B',9);

			include_once('../hce/hce_case_meeting_pdf.php');
			break;

		case '61':
			//���񽺰�ȹ��
			if ($var['subId'] == 'ALL'){
				$sql = 'SELECT	plan_seq
						FROM	hce_plan_sheet
						WHERE	org_no	= \''.$orgNo.'\'
						AND		org_type= \''.$hce->SR.'\'
						AND		IPIN	= \''.$hce->IPIN.'\'
						AND		rcpt_seq= \''.$hce->rcpt.'\'
						AND		del_flag= \'N\'
						ORDER	BY plan_dt DESC';

				$arrMeet = $conn->_fetch_array($sql);
				$first = true;
				
				if (is_array($arrMeet)){
					foreach($arrMeet as $arrSeq){
						if (!$first) $pdf->MY_ADDPAGE();

						$var['idx'] = $arrSeq['plan_seq'];
						
						$pdf->SetXY($pdf->left+$pdf->width*0.03, $pdf->top);
						$pdf->SetFont($pdf->font_name_kor,'B',18);

						$subject = '�� �� �� �� ȹ ��';
						$disH = 0;
						
						$pdf->_SignHcelineSet($subject,$disH);

						$col = $pdf->_colWidth();
						$rowH = $pdf->row_height * 1.5;

						$pdf->SetX($pdf->left);
						$pdf->Cell($col[0], $rowH, "ȸ��", 1, 0, "C", 1);
						$pdf->Cell($col[1], $rowH, "�ۼ�����", 1, 0, "C", 1);
						$pdf->Cell($col[2], $rowH, "�ۼ���", 1, 0, "C", 1);
						$pdf->Cell($col[3], $rowH, "���", 1, 1, "C", 1);

						if ($hce->SR == 'S'){
							include('../hce/hce_svc_plan2_pdf.php');
						}else{
							include('../hce/hce_svc_plan_pdf.php');
						}

						$first = false;
					}
				}

				Unset($arrMeet);
			}else{

				$pdf->SetXY($pdf->left+$pdf->width*0.03, $pdf->top);
				$pdf->SetFont($pdf->font_name_kor,'B',18);

				$subject = '�� �� �� �� ȹ ��';
				$disH = 0;
				
				$pdf->_SignHcelineSet($subject,$disH);

				$col = $pdf->_colWidth();
				$rowH = $pdf->row_height * 1.5;

				$pdf->SetX($pdf->left);
				$pdf->Cell($col[0], $rowH, "ȸ��", 1, 0, "C", 1);
				$pdf->Cell($col[1], $rowH, "�ۼ�����", 1, 0, "C", 1);
				$pdf->Cell($col[2], $rowH, "�ۼ���", 1, 0, "C", 1);
				$pdf->Cell($col[3], $rowH, "���", 1, 1, "C", 1);

				include_once('hce_svc_plan_list_pdf.php');
			}
			break;

		case '62':
			
			$pdf->SetXY($pdf->left+$pdf->width*0.03, $pdf->top);
			$pdf->SetFont($pdf->font_name_kor,'B',18);

			$subject = '�� �� �� �� ȹ ��';
			$disH = 0;

			$pdf->_SignHcelineSet($subject,$disH);	
			
			//���񽺰�ȹ��
			if ($hce->SR == 'S'){
				include_once('../hce/hce_svc_plan2_pdf.php');
			}else{
				include_once('../hce/hce_svc_plan_pdf.php');
			}
			break;

		case '71':
			//���� �̿� �ȳ� �� ���Ǽ�

			$pdf->SetXY($pdf->left+$pdf->width*0.03, $pdf->top);
			$pdf->SetFont($pdf->font_name_kor,'B',18);
			
			$subject = '���� �̿� �ȳ� �� ���Ǽ�';
			$disH = 0;
			
			$pdf->_SignHcelineSet($subject,$disH);
			
			$pdf->SetFont($pdf->font_name_kor,'B',9);

			include_once('../hce/hce_consent_form_php.php');
			break;

		case '81':
			//�������
			include_once('../hce/hce_proc_counsel_pdf.php');
			break;

		case '91':
			//���� ���� �� �Ƿڼ�
			$sql = 'SELECT	conn_seq
					FROM	hce_svc_connect
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$hce->IPIN.'\'
					AND		rcpt_seq= \''.$hce->rcpt.'\'
					ORDER	BY conn_seq DESC';

			$arr = $conn->_fetch_array($sql);
			$first = true;

			if (is_array($arr)){
				foreach($arr as $arrSeq){
					if (!$first) $pdf->MY_ADDPAGE();

					$var['idx'] = $arrSeq['conn_seq'];
					include('../hce/hce_svc_connection_pdf.php');

					$first = false;
				}
			}

			Unset($arr);
			break;

		case '92':
			//���� ���� �� �Ƿڼ�
			include_once('../hce/hce_svc_connection_pdf.php');
			break;

		case '101':
			//����͸� �����
			$sql = 'SELECT	mntr_seq
					FROM	hce_monitor
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$hce->IPIN.'\'
					AND		rcpt_seq= \''.$hce->rcpt.'\'
					AND		del_flag= \'N\'
					ORDER	BY mntr_seq DESC';

			$arr = $conn->_fetch_array($sql);
			$first = true;

			if (is_array($arr)){
				foreach($arr as $arrSeq){
					if (!$first) $pdf->MY_ADDPAGE();

					$var['idx'] = $arrSeq['mntr_seq'];


					$pdf->SetXY($pdf->left+$pdf->width*0.03, $pdf->top);
					$pdf->SetFont($pdf->font_name_kor,'B',18);
					
					$subject = '����͸� �����';
					$disH = 0;
					
					$pdf->_SignHcelineSet($subject,$disH);
					
					$pdf->SetFont($pdf->font_name_kor,'B',9);

					include('../hce/hce_monitor_pdf.php');

					$first = false;
				}
			}

			Unset($arr);
			break;

		case '102':
			//����͸� �����

			$pdf->SetXY($pdf->left+$pdf->width*0.03, $pdf->top);
			$pdf->SetFont($pdf->font_name_kor,'B',18);
			
			$subject = '����͸� �����';
			$disH = 0;
			
			$pdf->_SignHcelineSet($subject,$disH);
			
			$pdf->SetFont($pdf->font_name_kor,'B',9);

			include_once('../hce/hce_monitor_pdf.php');
			break;

		case '111':
			//����������
			$sql = 'SELECT	ispt_seq
					FROM	hce_re_ispt
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$hce->IPIN.'\'
					AND		rcpt_seq= \''.$hce->rcpt.'\'
					AND		del_flag= \'N\'
					ORDER	BY ispt_seq DESC';

			$arr = $conn->_fetch_array($sql);
			$first = true;

			if (is_array($arr)){
				foreach($arr as $arrSeq){
					if (!$first) $pdf->MY_ADDPAGE();

					$var['idx'] = $arrSeq['ispt_seq'];
					include('../hce/hce_re_ispt_pdf.php');

					$first = false;
				}
			}

			Unset($arr);
			break;

		case '112':
			//����������
			include_once('../hce/hce_re_ispt_pdf.php');
			break;

		case '121':
			//���� ���� �ȳ���
			include_once('../hce/hce_end_pdf.php');
			break;

		case '131':
			//����򰡼�
			
			$pdf->SetXY($pdf->left+$pdf->width*0.03, $pdf->top);
			$pdf->SetFont($pdf->font_name_kor,'B',18);
			
			$subject = '�� �� �� �� ��';
			$disH = 0;
			
			$pdf->_SignHcelineSet($subject,$disH);
			
			//$pdf->SetFont($pdf->font_name_kor,'B',9);
			
			include_once('../hce/hce_evaluation_pdf.php');
			break;

		case '142':
			//�����򰡼�
		include_once('../hce/hce_provide_eval_pdf.php');
			break;

		default:
			echo 'MODE : '.$var['mode'];
			exit;
	}

	if ($var['def'] == 'Y'){
		$hce->IPIN = $tmpIPIN;
	}

	include_once('../inc/_db_close.php');

	//���й��ڿ�
	function lfGetGbnStr($pdf,$myF,$conn,$sql,$code,$otherCode='',$otherStr='',$otherLen=30){
		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($row['code'] == $code){
				$str .= "��";
			}else{
				$str .= "��";
			}

			$str .= $row['name'];

			if ($row['code'] == $otherCode){
				$str .= "(";

				if ($code == $otherCode){
					$str .= $pdf->_splitTextWidth($myF->utf($otherStr),$otherLen);
				}else{
					$str .= "                         ";
				}

				$str .= ")";
			}

			$str .= "   ";
		}

		$conn->row_free();

		$str = Trim($str);

		return $str;
	}

	function lfGetGbnText($conn,$sql,$code,$gbn){
		$gbn = str_replace('/','&',$gbn);
		$gbn = str_replace(':','=',$gbn);

		parse_str($gbn,$arr);

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($arr[$row['code']] == 'Y'){
				$str .= ($str ? ',  ' : '').$row['name'];
			}
		}

		$conn->row_free();

		$str = Trim($str);

		return $str;
	}

	function lfGetGbnList($conn,$sql,$code,$gbn,$otherCode='',$otherStr='',$otherLen=30,$newLine = false){
		$gbn = str_replace('/','&',$gbn);
		$gbn = str_replace(':','=',$gbn);

		parse_str($gbn,$arr);

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($row['code'] == $otherCode && $newLine) $str .= "\n";
			if ($arr[$row['code']] == 'Y'){
				$cd = $row['code'];
				$str .= "��";
			}else{
				$str .= "��";
			}

			$str .= $row['name'];

			if ($row['code'] == $otherCode){
				$str .= "(";

				if ($cd == $otherCode){
					//$str .= $pdf->_splitTextWidth($myF->utf($otherStr),$otherLen);
					$str .= $otherStr;
				}else{
					$str .= "                         ";
				}

				$str .= ")";
			}else{
				$str .= "   ";
			}
		}

		$conn->row_free();

		$str = Trim($str);

		return $str;
	}

	function lfGetStringHeight($pdf,$width,$str,$height = 4){
		$X = 1000;
		$Y = $pdf->GetY();

		$pdf->SetXY($X, $Y);
		$pdf->MultiCell($width, $height, $str);

		$H = $pdf->GetY() - $Y;

		$pdf->SetFillColor(255,255,255);
		$pdf->SetXY($X, $Y);
		$pdf->Cell($width, $H, "", 0, 0, "C", 1);
		$pdf->SetFillColor(213,213,213);

		return $H;
	}

	function lfGetCol($col,$start,$end){
		$w = 0;

		for($i=$start; $i<=$end; $i++){
			$w += $col[$i];
		}

		return $w;
	}
?>