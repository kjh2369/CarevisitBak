<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사례접수일지
	 *********************************************************/

	$orgNo	= $_SESSION['userCenterCode'];
	$strName = $var['strName'];
	$strFrom = str_replace('-', '', $var['from']);
	$strTo = str_replace('-', '', $var['to']);
	$strEndYn	= $var['endYn'];

	$pdf->SetFont($pdf->font_name_kor,'',9);
	$col = $pdf->_colWidth();

	$rowHeight = $pdf->row_height * 2.3;

	//접수방법
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'CT\'';

	$gbnRct = $conn->_fetch_array($sql,'code');

	//사례접수리스트
	$sql = 'SELECT	DISTINCT
					rcpt.counsel_type
			,		rcpt.rcpt_dt
			,		m03_jumin AS jumin
			,		m03_name AS name
			,		rcpt.addr
			,		rcpt.addr_dtl
			,		rcpt.phone
			,		rcpt.mobile
			,		rcpt.counsel_text
			,		reqor_nm
			,		reqor_telno
			,		rcver_nm
			FROM	hce_receipt AS rcpt
			INNER	JOIN	m03sugupja AS mst
					ON		mst.m03_ccode = rcpt.org_no
					AND		mst.m03_mkind = \'6\'
					AND		mst.m03_key = rcpt.IPIN';
	
	/*
	if ($strName){
		$sql .= '	AND		mst.m03_name >= \''.$strName.'\'';
	}
	*/

	$sql .= '	WHERE	rcpt.org_no		= \''.$orgNo.'\'
				AND		rcpt.org_type	= \''.$var['sr'].'\'
				AND		rcpt.rcpt_seq	= (	SELECT	MAX(rcpt_seq)
											FROM	hce_receipt
											WHERE	org_no	= rcpt.org_no
											AND		org_type= rcpt.org_type
											AND		IPIN	= rcpt.IPIN
											AND		del_flag= \'N\')
				AND		rcpt.del_flag	= \'N\'';
	
	if ($strFrom && $strTo){
		$sql .= '
			AND		rcpt.rcpt_dt >= \''.$strFrom.'\'
			AND		rcpt.rcpt_dt <= \''.$strTo.'\'';
	}
	
	if ($strEndYn){
		$sql .= '
			AND		end_flag = \''.$strEndYn.'\'';
	}


	$sql .= '	ORDER	BY rcpt_dt';
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	if ($rowCnt % 10 > 0) $rowCnt = (Floor($rowCnt / 10) + 1) * 10;

	for($i=0; $i<$rowCnt; $i++){
		@$row = $conn->select_row($i);

		if ($i > 0 && $i % 10 == 0){
			$pdf->MY_ADDPAGE();
			$pdf->SetFont($pdf->font_name_kor,'',9);
		}

		if ($row)
			$no = Number_Format($i+1);
		else
			$no = '';

		$posY = $pdf->GetY();

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$rowHeight,$no,1,0,'C');
		$pdf->Cell($col[1],$rowHeight,$gbnRct[$row['counsel_type']]['name'],1,0,'C');
		$pdf->Cell($col[2],$rowHeight,$myF->dateStyle($row['rcpt_dt'],'.'),1,0,'C');
		$pdf->Cell($col[3],$rowHeight,'',1,0,'C');
		$pdf->Cell($col[4],$rowHeight,'',1,0,'C');
		$pdf->Cell($col[5],$rowHeight,'',1,0,'C');
		$pdf->Cell($col[6],$rowHeight,'',1,0,'C');
		$pdf->Cell($col[7],$rowHeight,$row['rcver_nm'],1,0,'C');
		$pdf->Cell($col[8],$rowHeight,'',1,1,'C');

		if ($row){
			//대상자명,성별,나이
			$str = $row['name']."\n(".$myF->euckr($myF->issToGender($row['jumin']))."/".$myF->issToAge($row['jumin'])."세)";
			$pos[3] = Array('X'=>$pdf->left+$col[0]+$col[1]+$col[2],'Y'=>$posY+2,'align'=>'C','str'=>$str);

			//주소
			$telno = $myF->phoneStyle($row['phone'] ? $row['phone'] : $row['mobile'],'.');

			if ($telno){
				$str = $pdf->_splitTextWidth($myF->utf($row['addr']." ".$row['addr_dtl']),$col[4])."\n".$telno;
				$align = 'C';
			}else{
				$str = $pdf->_splitTextWidth($myF->utf($row['addr']." ".$row['addr_dtl']),$col[4]*2);
				$align = 'L';
			}

			$pos[4] = Array('X'=>$pdf->left+$col[0]+$col[1]+$col[2]+$col[3],'Y'=>$posY+2,'align'=>$align,'str'=>$str);

			//상담내용
			$str = $pdf->_splitTextWidth($myF->utf(Str_Replace("\n"," ",$row['counsel_text'])),$col[5]*2-8);
			$pos[5] = Array('X'=>$pdf->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4],'Y'=>$posY+2,'align'=>'','str'=>$str);

			//의뢰인
			$str = $pdf->_splitTextWidth($myF->utf($row['reqor_nm']),$col[6])."\n".$myF->phoneStyle($row['reqor_telno'],'.');
			$pos[6] = Array('X'=>$pdf->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]+$col[5],'Y'=>$posY+2,'align'=>'C','str'=>$str);
		}

		//현재 좌표
		$X = $pdf->GetX();
		$Y = $pdf->GetY();

		if (Is_Array($pos)){
			foreach($pos as $idx => $arr){
				$pdf->SetXY($arr['X'],$arr['Y']);
				$pdf->MultiCell($col[$idx],5,$arr['str'],0,$arr['align']);
			}
		}

		Unset($pos);

		$pdf->SetXY($X,$Y);
	}

	$conn->row_free();
?>