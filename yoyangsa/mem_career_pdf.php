<?
	include('../inc/_login.php');

	if (!Is_Array($var)){
		exit;
	}

	$code		= $_SESSION['userCenterCode'];
	$reportId	= '201090CAREER';	//리포트 ID
	$year		= Date('Y');

	//발급번호
	$sql = 'SELECT	issue_no
			FROM	report_issue
			WHERE	org_no			= \''.$code.'\'
			AND		issue_report	= \''.$reportId.'\'
			AND		issue_year		= \''.$year.'\'
			AND     issue_target    = \''.$var['jumin'].'\'';
	
	$issueNo	= $conn->get_data($sql);
	
	
	if($issueNo == ''){
		$sql = 'SELECT	IFNULL(MAX(issue_no),0)+1
				FROM	report_issue
				WHERE	org_no			= \''.$code.'\'
				AND		issue_report	= \''.$reportId.'\'
				AND		issue_year		= \''.$year.'\'';

		$issueNo	= $conn->get_data($sql);

		$sql = 'INSERT INTO report_issue (
				 org_no
				,issue_report
				,issue_year
				,issue_no
				,issue_dt
				,issue_id
				,issue_target) VALUES (
				 \''.$code.'\'
				,\''.$reportId.'\'
				,\''.$year.'\'
				,\''.$issueNo.'\'
				,NOW()
				,\''.$_SESSION['userCode'].'\'
				,\''.$var['jumin'].'\'
				)';

		$conn->execute($sql);
	}

	$issueNo	= '00000'.$issueNo;
	$issueNo	= $year.'-'.SubStr($issueNo,StrLen($issueNo)-4,StrLen($issueNo));

	//기관정뵤
	$sql = 'SELECT	m00_store_nm	AS nm
			,		m00_mname		AS manager
			,		m00_ctel		AS phone
			,		m00_cpostno		AS postno
			,		m00_caddr1		AS addr
			,		m00_caddr2		AS addr_dtl
			,		m00_jikin		AS jikin
			FROM	m00center
			WHERE	m00_mcode = \''.$code.'\'
			  AND   m00_del_yn = \'N\'
			LIMIT	1';

	$row	= $conn->get_array($sql);

	$center		= $row['nm'];
	$manager	= $row['manager'];
	$phone		= $myF->phoneStyle($row['phone'],'.');
	$jikin		= $row['jikin'];

	$row['addr'] = str_replace(chr(10), '', $row['addr']);
	$row['addr'] = Explode(chr(13), $row['addr']);
	$row['addr'] = $row['addr'][0];

	$centerAddr	.= $row['addr'].' ';
	$centerAddr	.= $row['addr_dtl'];

	Unset($row);

	//직원 입사이력
	$sql = 'SELECT	mem_his.join_dt
			,		mem_his.quit_dt
			,		mem_his.employ_stat
			,		mem_pos.pos_nm
			,		mem_his.mem_work
			FROM	mem_his
			LEFT	JOIN	mem_pos
					ON		mem_pos.org_no	= mem_his.org_no
					AND		mem_pos.pos_cd	= mem_his.mem_pos
			WHERE	mem_his.org_no	= \''.$code.'\'
			AND		mem_his.jumin	= \''.$var['jumin'].'\'
			AND		mem_his.seq		= \''.$var['seq'].'\'';

	$his	= $conn->get_array($sql);

	$sql = 'SELECT	employ_stat
			,		join_dt
			,		quit_dt
			FROM	mem_his
			WHERE	org_no	 = \''.$code.'\'
			AND		jumin	 = \''.$var['jumin'].'\'
			AND		seq		<= \''.$var['seq'].'\'
			ORDER	BY	seq DESC';

	$conn->query($sql);
	$conn->fetch();

	$hisCnt	= $conn->row_count();

	for($i=0; $i<$hisCnt; $i++){
		$row	= $conn->select_row($i);

		$period	= $myF->cutOff((StrToTime($row['quit_dt']) - StrToTime($row['join_dt'])) / 86400, 30);
		$year	= Floor($period / 30 / 12);
		$month	= ($period / 30) % 12;

		$period	= (!Empty($year) ? $year.'년 ' : '').$month.'개월';

		if ($row['employ_stat'] == '1'){
			$jobHis	.= $myF->dateStyle($row['join_dt'],'.')." ~ 현재\n";
		}else if ($row['employ_stat'] == '9'){
			$jobHis	.= $myF->dateStyle($row['join_dt'],'.')." ~ ".$myF->dateStyle($row['quit_dt'],'.')."  근속기간 : ".$period."\n";
		}
	}

	$conn->row_free();

	Unset($row);

	//직원 개인정보
	$sql = 'SELECT	m02_yname						AS name
			,		IFNULL(dept.dept_nm,\'\')		AS dept_nm
			,		IFNULL(job_kind.job_nm,\'\')	AS job_nm
			,		m02_ypostno						AS postno
			,		m02_yjuso1						AS addr
			,		m02_yjuso2						AS addr_dtl
			FROM	m02yoyangsa
			LEFT	JOIN	dept
					ON		dept.org_no		= m02_ccode
					AND		dept.dept_cd	= m02_dept_cd
					AND		dept.del_flag	= \'N\'
			LEFT	JOIN	job_kind
					ON		job_kind.org_no		= m02_ccode
					AND		job_kind.job_cd		= m02_yjikjong
					AND		job_kind.del_flag	= \'N\'
			WHERE	m02_ccode	= \''.$code.'\'
			AND		m02_yjumin	= \''.$var['jumin'].'\'
			ORDER	BY m02_mkind
			LIMIT	1';

	$row	= $conn->get_array($sql);

	//if (StrLen($row['postno']) == 6){
	//	$addr	= '['.SubStr($row['postno'],0,3).'-'.SubStr($row['postno'],3,3).'] ';
	//}else{
	//	$addr	= '';
	//}

	$addr	.= $row['addr'].' ';
	$addr	.= $row['addr_dtl'];

	if ($his['employ_stat'] == '1'){
		$lsTitle	= "재  직  증  명  서";
		$lsMsg		= "상기자는 당사에서 위와 같이 재직중임을 확인합니다.";
	}else{
		$lsTitle	= "경  력  증  명  서";
		$lsMsg		= "상기자는 당사에서 위와 같이 근무하였음을 확인합니다.";
	}

	$pdf->SetFont($pdf->font_name_kor,'BU',30);
	$pdf->SetXY($pdf->left,$pdf->top);
	$pdf->Cell($pdf->width,$pdf->row_height,$lsTitle,0,1,'C');

	$pdf->SetXY($pdf->left,$pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor,'',$pdf->font_size);
	$pdf->Cell($pdf->width,$pdf->row_height,"발급번호 : ".$issueNo,0,1,'L');

	$pdf->SetFont($pdf->font_name_kor,'',15);

	$rowH	= $pdf->row_height / $pdf->font_size * 15 * 1.5;

	$liY	= $pdf->GetY();
	
	/*
	if($code == '34717000001' || //어르신이행복한세상
	   $code == '34121000062' ){ //광명나누미 

	}else {
		$jumin = $myF->issStyle($var['jumin']);	
	}
	*/

	$jumin = $myF->issNo($var['jumin']);

	$pdf->SetXY($pdf->left,$liY);
	$pdf->Cell($pdf->width * 0.20, $rowH, "성   명", 1, 0, 'C');
	$pdf->Cell($pdf->width * 0.30, $rowH, $row['name'], 1, 0, 'C');
	$pdf->Cell($pdf->width * 0.20, $rowH, "주민등록번호", 1, 0, 'C');
	$pdf->Cell($pdf->width * 0.30, $rowH, $jumin, 1, 1, 'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.20, $rowH, "소   속", 1, 0, 'C');
	$pdf->Cell($pdf->width * 0.30, $rowH, $row['dept_nm'], 1, 0, 'C');
	$pdf->Cell($pdf->width * 0.20, $rowH, "직   위", 1, 0, 'C');
	$pdf->Cell($pdf->width * 0.30, $rowH, $his['pos_nm'], 1, 1, 'C');

	if ($his['employ_stat'] == '1'){
		$quitDt	= '재직중';
	}else if ($his['employ_stat'] == '9'){
		$quitDt	= '퇴사';
	}else{
		$quitDt	= '휴직중';
	}

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.20, $rowH, "최근입사일", 1, 0, 'C');
	$pdf->Cell($pdf->width * 0.30, $rowH, $myF->dateStyle($his['join_dt'],'.'), 1, 0, 'C');
	$pdf->Cell($pdf->width * 0.20, $rowH, "재 직 상 태", 1, 0, 'C');
	$pdf->Cell($pdf->width * 0.30, $rowH, $quitDt, 1, 1, 'C');

	if (!Empty($jobHis)){
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.20, $pdf->row_height*$hisCnt+3, "근 무 기 간", 1, 0, 'C');
		$pdf->Cell($pdf->width * 0.80, $pdf->row_height*$hisCnt+3, "", 1, 1, 'C');

		$y	= $pdf->GetY();

		$pdf->SetFontSize(13);
		$pdf->SetXY($pdf->left+$pdf->width * 0.20,$y-($pdf->row_height*$hisCnt+3)+1.5);
		$pdf->MultiCell($pdf->width * 0.80, $pdf->row_height, $jobHis);
		$pdf->SetFontSize(15);

		$pdf->SetY($y);
	}

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.20, $rowH, "주   소", 1, 0, 'C');
	$pdf->SetFontSize(11);
	$pdf->Cell($pdf->width * 0.80, $rowH, $pdf->_splitTextWidth($myF->utf($addr), $pdf->width * 0.80), 1, 1, 'L');
	$pdf->SetFontSize(15);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.20, $rowH, "주 요 업 무", 1, 0, 'C');
	$pdf->SetFontSize(13);
	$pdf->Cell($pdf->width * 0.80, $rowH, $pdf->_splitTextWidth($myF->utf($his['mem_work']), $pdf->width * 0.80), 1, 1, 'L');
	$pdf->SetFontSize(15);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width, $pdf->height - $pdf->GetY(), "", 1, 0, 'C');

	$liC	= $hisCnt;
	$liT	= 20 - $pdf->row_height;

	$pdf->SetFontSize(13);
	$pdf->SetXY($pdf->left, $pdf->GetY()+$liT);
	$pdf->Cell($pdf->width, $rowH, $lsMsg, 0, 1, 'C');

	$liT	= 30 - $pdf->row_height * ($liC / 2);

	$pdf->SetXY($pdf->left+$pdf->width*0.4, $pdf->GetY()+$liT);
	$pdf->Cell($pdf->width*0.6, $rowH, Date('Y').'년 '.IntVal(Date('m')).'월 '.IntVal(Date('d')).'일', 0, 1, 'L');

	$pdf->SetXY($pdf->left+$pdf->width*0.4, $pdf->GetY()+$liT);

	//직인 출력
	if (!Empty($jikin)){
		$tmpImg = getImageSize('../mem_picture/'.$jikin);
		$pdf->Image('../mem_picture/'.$jikin, $pdf->width*0.4+$pdf->GetStringWidth("대표자 : ".$manager."       (인)     "), $pdf->GetY() + 7, 21);
	}

	$pdf->Cell($pdf->width*0.6, $rowH, "업체명 : ".$center, 0, 1, 'L');
	$pdf->SetX($pdf->left+$pdf->width*0.4);
	$pdf->Cell($pdf->width*0.6, $rowH, "대표자 : ".$manager."       (인)", 0, 1, 'L');
	$pdf->SetX($pdf->left+$pdf->width*0.4);
	$pdf->Cell($pdf->width*0.6, $rowH, "연락처 : ".$phone, 0, 1, 'L');
	$pdf->SetX($pdf->left+$pdf->width*0.4);
	$pdf->Cell($pdf->width*0.6, $rowH, "주   소 : ", 0, 1, 'L');
	$pdf->SetXY($pdf->left+$pdf->width*0.5, $pdf->GetY()-9);
	$pdf->MultiCell($pdf->width * 0.48, $pdf->row_height, $centerAddr);

	$pdf->SetLineWidth(0.6);
	$pdf->Rect($pdf->left, $liY, $pdf->width, $pdf->height-$liY);
	$pdf->SetLineWidth(0.2);

	Unset($his);
	Unset($row);
?>