<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	parse_str($_POST['para'], $_val);

	$orgNo = $_SESSION['userCenterCode'];
	$SR = $_val['SR'];
	$suga_cd = $_val['suga_cd'];
	$seq = $_val['seq'];
	$gbn = SubStr($suga_cd, 0, 5);

	$str = Array(
		'B9901'=>Array('title'=>'', 'str1'=>'참석자수', 'str2'=>'참석자', 'str3'=>'명')
	,	'B9902'=>Array('title'=>'', 'str1'=>'교육생수', 'str2'=>'교육생', 'str3'=>'명')
	,	'B9903'=>Array('title'=>'', 'str1'=>'실습생수', 'str2'=>'실습생', 'str3'=>'명')
	,	'B9904'=>Array('title'=>'', 'str1'=>'홍보건수', 'str2'=>'홍보방법', 'str3'=>'건')
	,	'A0602'=>Array('title'=>'', 'str1'=>'후원자수', 'str2'=>'후원자', 'str3'=>'명')
	,	'A0603'=>Array('title'=>'', 'str1'=>'봉사자수', 'str2'=>'봉사자', 'str3'=>'명')
	,	'B0301'=>Array('title'=>'', 'str1'=>'참석자수', 'str2'=>'참석자', 'str3'=>'명')
	);

	$sql = 'SELECT	a.reg_dt, a.att_cnt, a.attendee, a.contents, b.suga_nm, a.pic1, a.pic2
			FROM	care_rpt AS a
			INNER	JOIN	care_suga AS b
					ON		b.org_no = a.org_no
					AND		b.suga_sr = a.org_sr
					AND		CONCAT(b.suga_cd, b.suga_sub) = a.suga_cd
					AND		DATE_FORMAT(b.from_dt, \'%Y%m%d\') <= a.reg_dt
					AND		DATE_FORMAT(b.to_dt, \'%Y%m%d\') >= a.reg_dt
			WHERE	a.org_no	= \''.$orgNo.'\'
			AND		a.org_sr	= \''.$SR.'\'
			AND		a.suga_cd	= \''.$suga_cd.'\'
			AND		a.seq		= \''.$seq.'\'
			AND		a.del_flag	= \'N\'';

	$R = $conn->get_array($sql);
	
	if(count(explode('/', $R['attendee'])) < 2){
		$attendee = $R['attendee'];
	}else {
		if ($gbn == 'A0602' || $gbn == 'A0603'){
			$attendee = $R['attendee'];
			$R['attendee'] = '';

			$sql = 'SELECT	cust_cd, cust_nm
					FROM	care_cust
					WHERE	org_no	 = \''.$orgNo.'\'
					AND		del_flag = \'N\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if (is_numeric(StrPos($attendee, '/'.$row['cust_cd']))) $R['attendee'] .= ($R['attendee'] ? ', ' : '').$row['cust_nm'];
			}

			$conn->row_free();

		}
	}

	$str[$gbn]['title'] = $R['suga_nm'];

	$rowH = 10;

	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetFont($pdf->font_name_kor, 'B', 17);
	$pdf->Cell($pdf->width, $rowH, $str[$gbn]['title'], false, 1, 'C');
	$pdf->SetFont($pdf->font_name_kor, '', 9);

	$rowH = 7;

	$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
	$pdf->Cell($pdf->width * .12, $rowH, '작성일자', true, 0);
	$pdf->Cell($pdf->width * .88, $rowH, $myF->dateStyle($R['reg_dt'], '.'), true, 1);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * .12, $rowH, $str[$gbn]['str1'], true, 0);
	$pdf->Cell($pdf->width * .88, $rowH, $R['att_cnt'].$str[$gbn]['str3'], true, 1);

	$y1 = $pdf->GetY();

	$pdf->SetXY($pdf->left + $pdf->width * .12, $y1 + 1);
	$pdf->MultiCell($pdf->width * .88, 4, stripslashes($R['attendee']));

	$y2 = $pdf->GetY() - $y1 + 1;

	$pdf->SetXY($pdf->left, $y1);
	$pdf->Cell($pdf->width * .12, $y2, $str[$gbn]['str2'], true, 0);
	$pdf->Cell($pdf->width * .88, $y2, '', true, 1);

	$y1 = $pdf->GetY();

	$pdf->SetXY($pdf->left, $y1 + 1);
	$pdf->MultiCell($pdf->width, 4, stripslashes($R['contents']));

	$y2 = $pdf->GetY() - $y1 + 1;

	if ($y2 < 50) $y2 = 50;

	$pdf->SetXY($pdf->left, $y1);
	$pdf->Cell($pdf->width, $y2, '', true, 1);

	if ($R['pic1']){
		$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
		$pdf->Cell($pdf->width, $rowH, '첨부사진', 0, 1);
		$pdf->Image($R['pic1'], $pdf->left, $pdf->GetY());
	}


	include_once('../inc/_db_close.php');
?>