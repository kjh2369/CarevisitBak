<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_function.php');
	require_once('../pdf/korean.php');
	require_once('../pdf/pdf_counsel_table.php');

	$conn->set_name('euckr');

	$code = $_GET['code'];
	$ssn = $ed->de($_GET['ssn']);
	
	$rows = 6.5;
	
	$row_rel = $rows+80;
	$row_adobe = $rows+86.5;
	$row_lvl = $rows+93.5;
	$row_gbn = $rows+100.5;
	$row_path = $rows+106.5;
	$row_svc_work = $rows+113;
	$row_hope_work = $rows+121;
	$row_time = $rows+126;
	
	
	$sql = "select mem_ssn
			,      mem_counsel_dt
			,      mem_counsel_gbn
			,	   mem_edu_lvl
			,	   mem_gbn
			,	   mem_abode
			,	   mem_religion
			,	   mem_rel_other
			,      mem_app_path
			,	   mem_app_other
			,	   mem_svc_work
			,	   mem_svc_other
			,	   mem_hope_work
			,      mem_hope_other
			,	   mem_work_time
			,	   mem_salary
			,	   mem_hourly
			,      mem_talker_id
			,      mem_talker_nm
			,	   mem_counsel_dt
			,	   mem_counsel_content
			,	   mem_counsel_action
			,	   mem_counsel_result
			,	   mem_counsel_other
			,      mem_nm
			,      mem_phone
			,	   mem_mobile
			,      mem_email
			,	   mem_postno
			,      mem_addr
			,      mem_addr_dtl
			,      mem_marry
			,      mem_picture
			,      m00_cname
			  from counsel_mem
		inner join m00center
		        on m00_mcode = org_no
			 where org_no   = '$code'
			   and mem_ssn  = '$ssn'
			   and del_flag = 'N'";
	
	
	$mem = $conn->get_array($sql);

	
	$pdf = new MYPDF(strtoupper('P'));
	$pdf->font_name_kor = '굴림';
	$pdf->font_name_eng = 'Gulim';
	$pdf->AddUHCFont('굴림','Gulim');
	$pdf->Open();
	
	$pdf -> left = 14;
	$pdf -> row_height = $rows;
	$pdf -> code = $_SESSION['userCenterGiho'];						//기관코드
	$pdf -> cname = $mem['m00_cname'];			//기관명
	$pdf -> picture = $mem['mem_picture'];		//증명사진
	$pdf -> mname = $mem['mem_nm'];				//성명
	$pdf -> mjumin = $myF->issStyle($mem['mem_ssn']);			//주민
	$pdf -> marry_yn = $mem['mem_marry'];		//결혼여부
	$pdf -> tel = $myF->phoneStyle($mem['mem_phone'],'.');			//유선
	$pdf -> mobile = $myF->phoneStyle($mem['mem_mobile'],'.');		//무선(휴대폰)
	$pdf -> email = $mem['mem_email'];			//이메일
	$pdf -> postno = getPostNoStyle($mem['mem_postno']);		//우편번호
	$pdf -> addr = $mem['mem_addr'];			//주소
	$pdf -> addr_dtl = $mem['mem_addr_dtl'];	//상세주소


	$pdf->SetFillColor(222,222,222);

	$pdf->AddPage(strtoupper('p'), 'A4');

	$pdf->SetXY($pdf->left,$pdf->top+62);
		

	//종교
	switch($mem['mem_religion']){
		case N:
			$pdf->Text(37.7, $row_rel, 'v');
			break;
		case 1:
			$pdf->Text(46.2, $row_rel, 'v');
			break;
		case 2:
			$pdf->Text(61, $row_rel, 'v');
			break;
		case 3:
			$pdf->Text(75.7, $row_rel, 'v');
			break;
		case 9:
			$pdf->Text(87.5, $row_rel, 'v');
			$pdf->Text(99, $row_rel + 0.5, $mem['mem_rel_other']);
			break;
	}

	//구분
	switch($mem['mem_gbn']){
		case '1':
			$pdf->Text(112.5, $row_gbn, 'v');
			break;
		case '3':
			$pdf->Text(124.3, $row_gbn, 'v');
			break;
		case 'A':
			$pdf->Text(136, $row_gbn, 'v');
			break;
	}

	//주거
	switch($mem['mem_abode']){
		case 1:
			$pdf->Text(112.5, $row_adobe, 'v');
			break;
		case 3:
			$pdf->Text(124.3, $row_adobe, 'v');
			break;
		case 5:
			$pdf->Text(136, $row_adobe, 'v');
			break;
	}
	//학력
	switch($mem['mem_edu_lvl']){
		case 1:
			$pdf->Text(112.5, $row_lvl, 'v');
			break;
		case 3:
			$pdf->Text(130.5, $row_lvl, 'v');
			break;
		case 5:
			$pdf->Text(142.3, $row_lvl, 'v');
			break;
		case 7:
			$pdf->Text(160.3, $row_lvl, 'v');
			break;
	}


	//신청경로
	switch($mem['mem_app_path']){
		case 1:
			$pdf->Text(37.7, $row_path, 'v');
			break;
		case 2:
			$pdf->Text(55.7, $row_path, 'v');
			break;
		case 3:
			$pdf->Text(86.3, $row_path, 'v');
			break;
		case 4:
			$pdf->Text(101, $row_path, 'v');
			break;
		case 5:
			$pdf->Text(112.8, $row_path, 'v');
			break;
		case 9:
			$pdf->Text(134, 106, 'v');
			$pdf->Text(145, $row_path + 0.5, $mem['mem_app_other']);
			break;
	}


	//자원봉사경험
	switch($mem['mem_svc_work']){
		case N:
			$pdf->Text(37.7, $row_svc_work, 'v');
			break;
		case Y:
			$pdf->Text(46, $row_svc_work, 'v');
			$pdf->Text(63, $row_svc_work + 0.5, $mem['mem_svc_other']);
			break;
	}

	//활동희망영역
	if($mem['mem_hope_work'][0] == 'Y'){
		$pdf->Text(37.7, $row_hope_work, 'v');
	}

	if($mem['mem_hope_work'][1] == 'Y'){
		$pdf->Text(55.7, $row_hope_work, 'v');
	}

	if($mem['mem_hope_work'][2] == 'Y'){
		$pdf->Text(73.7, $row_hope_work, 'v');
	}

	if($mem['mem_hope_work'][3] == 'Y'){
		$pdf->Text(91.7, $row_hope_work, 'v');
	}

	if($mem['mem_hope_work'][4] == 'Y'){
		$pdf->Text(112.9, $row_hope_work, 'v');
	}

	if($mem['mem_hope_work'][$rows] == 'Y'){
		$pdf->Text(137.2, $row_hope_work, 'v');
		$pdf->Text(148, $row_hope_work + 0.5, $mem['mem_hope_other']);
	}

	//근무가능시간
	switch($mem['mem_work_time']){
		case 1:
			$pdf->Text(37.7, $row_time, 'v');
			break;
		case 2:
			$pdf->Text(49.3, $row_time, 'v');
			break;
		case 3:
			$pdf->Text(61, $row_time, 'v');
			break;
	}

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'종교',1,0,'C',true);
	$pdf->Cell(160,$rows,'□무  □기독교  □천주교  □불교  □기타(                    )',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'취미/특기',1,0,'C',true);
	$pdf->Cell(50,$rows,'',1,0,'L');
	$pdf->Cell(25,$rows,'주거',1,0,'L',true);
	$pdf->Cell(85,$rows,'□전세  □월세  □자가',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'본인장애',1,0,'C',true);
	$pdf->Cell(50,$rows,'',1,0,'L');
	$pdf->Cell(25,$rows,'학력',1,0,'L',true);
	$pdf->Cell(85,$rows,'□중졸이하  □고졸  □대학중퇴  □대졸이상',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'치료중인질병',1,0,'C',true);
	$pdf->Cell(50,$rows,'',1,0,'L');
	$pdf->Cell(25,$rows,'구분',1,0,'L',true);
	$pdf->Cell(85,$rows,'□일반  □차상위(모자,부자 가정)',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'신청경로',1,0,'C',true);
	$pdf->Cell(160,$rows,'□지역신문  □인터넷취업사이트  □홍보물  □소개  □타기관의뢰  □기타(                             ) ',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'자원봉사경험',1,0,'C',true);
	$pdf->Cell(160,$rows,'□무  □유 (내용:                                                                                )',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'활동희망영역',1,0,'C',true);
	$pdf->Cell(160,$rows,'□장기요양  □노인돌봄  □가사간병  □산모신생아  □장애인활동보  □기타(                           )',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'근무가능시간',1,0,'C',true);
	$pdf->Cell(50,$rows,'□종일  □오전  □오후',1,0,'L');
	$pdf->Cell(25,$rows,'희망소득',1,0,'L',true);
	$pdf->Cell(85,$rows,'월 '.number_format($mem['mem_salary']).'원(시급: '.number_format($mem['mem_hourly']).'원)',1,1,'L');

	$high = $rows;

	$sql = "select count(*)
			  from counsel_family
			 where org_no = '$code'
			   and family_type = '1'
			   and family_ssn = '$ssn'";
	$family = $conn->get_data($sql);

    $high = $family != 0 ? $family * $rows + $high : $rows*2;

	$pdf->SetX(14);
	$pdf->Cell(22,$high,'가족사항',1,0,'C',true);
	$pdf->Cell(25,$rows,'성명',1,0,'C',true);
	$pdf->Cell(25,$rows,'관계',1,0,'C',true);
	$pdf->Cell(25,$rows,'연령',1,0,'C',true);
	$pdf->Cell(25,$rows,'직업',1,0,'C',true);
	$pdf->Cell(20,$rows,'동거여부',1,0,'C',true);
	$pdf->Cell(40,$rows,'월수입',1,1,'C',true);

	$sql = "select family_nm
			,	   family_rel
			,	   family_age
			,	   family_job
			,	   case family_with when 'Y' then '예' else '아니오' end as fm_with
			,	   family_monthly
			  from counsel_family
			 where org_no = '$code'
			   and family_type = '1'
			   and family_ssn = '$ssn'";
	$conn->query($sql);

	$conn->fetch();
	$rowCount = $conn->row_count();
	if($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$pdf->SetX(14);
			$pdf->Cell(22,0,'',0,0,'C');
			$pdf->Cell(25,$rows,$row['family_nm'],1,0,'L');
			$pdf->Cell(25,$rows,$row['family_rel'],1,0,'L');
			$pdf->Cell(25,$rows,$row['family_age'],1,0,'L');
			$pdf->Cell(25,$rows,$row['family_job'],1,0,'L');
			$pdf->Cell(20,$rows,$row['fm_with'],1,0,'C');
			$pdf->Cell(40,$rows,$row['family_month'],1,1,'L');

			$high += $rows;
		}
	}else {
		$pdf->SetX(14);
		$pdf->Cell(22,0,'',0,0,'C');
		$pdf->Cell(25,$rows,'',1,0,'L');
		$pdf->Cell(25,$rows,'',1,0,'L');
		$pdf->Cell(25,$rows,'',1,0,'L');
		$pdf->Cell(25,$rows,'',1,0,'L');
		$pdf->Cell(20,$rows,'',1,0,'L');
		$pdf->Cell(40,$rows,'',1,1,'L');
	}


	$sql = "select count(*)
			  from counsel_edu
			 where org_no = '$code'
			   and edu_ssn = '$ssn'
			   and edu_gbn = '1'";
	$edu_dol = $conn->get_data($sql);

	$sql = "select count(*)
			  from counsel_edu
			 where org_no = '$code'
			   and edu_ssn = '$ssn'
			   and edu_gbn = '9'";
	$edu_order = $conn->get_data($sql);

	$high = $rows*2;

	if($edu_dol != 0 and $edu_order != 0){
		$high = ($edu_dol+$edu_order) * 5 + $rows*4;
	}else if($edu_dol == 0 and $edu_order == 0){
		$high = (($edu_dol+$edu_order) * 5 + $high)+$rows*4;
	}else if($edu_dol == 0 or $edu_order == 0){
		$high = (($edu_dol+$edu_order) * 5 + $high)+$rows*3;
	}


	$pdf->SetX(14);
	$pdf->Cell(22,$high,'교육이수',1,0,'C',true);
	$pdf->Cell(160,$rows,'돌봄관련 교육',1,1,'C',true);

	$pdf->SetX(14);
	$pdf->Cell(22,0,'',0,0,'C',true);
	$pdf->Cell(50,$rows,'교육기관',1,0,'C',true);
	$pdf->Cell(70,$rows,'교육명',1,0,'C',true);
	$pdf->Cell(40,$rows,'교육시간',1,1,'C',true);

	$sql = "select *
			  from counsel_edu
			 where org_no = '$code'
			   and edu_ssn = '$ssn'
			   and edu_gbn = '1'";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();
	if($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$pdf->SetX(14);
			$pdf->Cell(22,0,'',0,0,'C');
			$pdf->Cell(50,$rows,$row['edu_center'],1,0,'L');
			$pdf->Cell(70,$rows,$row['edu_nm'],1,0,'L');
			$pdf->Cell(40,$rows,$row['edu_time'],1,1,'L');
		}
	}else {
		$pdf->SetX(14);
		$pdf->Cell(22,0,'',0,0,'C');
		$pdf->Cell(50,$rows,'',1,0,'L');
		$pdf->Cell(70,$rows,'',1,0,'L');
		$pdf->Cell(40,$rows,'',1,1,'L');
	}

	$pdf->SetX(14);
	$pdf->Cell(22,0,'',0,0,'C',true);
	$pdf->Cell(160,$rows,'기타교육',1,1,'C',true);

	$pdf->SetX(14);
	$pdf->Cell(22,0,'',0,0,'C',true);
	$pdf->Cell(50,$rows,'교육기관',1,0,'C',true);
	$pdf->Cell(70,$rows,'교육명',1,0,'C',true);
	$pdf->Cell(40,$rows,'교육시간',1,1,'C',true);

	$sql = "select *
			  from counsel_edu
			 where org_no = '$code'
			   and edu_ssn = '$ssn'
			   and edu_gbn = '9'";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();
	if($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$pdf->SetX(14);
			$pdf->Cell(22,0,'',0,0,'C');
			$pdf->Cell(50,$rows,$row['edu_center'],1,0,'L');
			$pdf->Cell(70,$rows,$row['edu_nm'],1,0,'L');
			$pdf->Cell(40,$rows,$row['edu_time'],1,1,'L');
		}
	}else {
		$pdf->SetX(14);
		$pdf->Cell(22,0,'',0,0,'C');
		$pdf->Cell(50,$rows,'',1,0,'L');
		$pdf->Cell(70,$rows,'',1,0,'L');
		$pdf->Cell(40,$rows,'',1,1,'L');
	}
	/*
	$pdf->SetX(14);
	$pdf->Cell(182,$rows,'',1,1,'L');
	*/
	$sql = "select count(*)
			  from counsel_license
			 where org_no = '$code'
			   and license_ssn = '$ssn'";
	$license = $conn->get_data($sql);

	$high = $rows;

	$high = $license != 0 ? $license * $rows + $high : $rows*2;

	//자격증
	$pdf->SetX(14);
	$pdf->Cell(22,$high,'자격',1,0,'C',true);

	$pdf->SetX(14);
	$pdf->Cell(22,0,'',1,0,'C',true);
	$pdf->Cell(50,$rows,'자격증종류',1,0,'C',true);
	$pdf->Cell(25,$rows,'자격증번호',1,0,'C',true);
	$pdf->Cell(45,$rows,'발급기간',1,0,'C',true);
	$pdf->Cell(40,$rows,'발급일자',1,1,'C',true);

	$sql = "select *
			  from counsel_license
			 where org_no = '$code'
			   and license_ssn = '$ssn'";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();
	if($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$pdf->SetX(14);
			$pdf->Cell(22,0,'',0,0,'C');
			$pdf->Cell(50,$rows,$row['license_gbn'],1,0,'L');
			$pdf->Cell(25,$rows,$row['license_no'],1,0,'L');
			$pdf->Cell(45,$rows,$row['license_center'],1,0,'L');
			if($row['license_dt'] != '0000-00-00'){
				$pdf->Cell(40,$rows,$row['license_dt'],1,1,'C');
			}else {
				$pdf->Cell(40,$rows,'',1,1,'C');
			}

		}
	}else {
		$pdf->SetX(14);
		$pdf->Cell(22,0,'',0,0,'C');
		$pdf->Cell(50,$rows,'',1,0,'L');
		$pdf->Cell(25,$rows,'',1,0,'L');
		$pdf->Cell(45,$rows,'',1,0,'L');
		$pdf->Cell(40,$rows,'',1,1,'L');
	}

	/*
	$pdf->SetX(14);
	$pdf->Cell(182,$rows,'',1,1,'L');
	*/
	

	$col_width_1 = 158; 
	$row_height_1 = $rows * 2.5;
	
	//문자열높이변수
	$high_0 = get_row_cnt($pdf, $col_width_1, $row_height_1, $mem['mem_counsel_content']);
	$high_1 = get_row_cnt($pdf, $col_width_1, $row_height_1, $mem['mem_counsel_action']);
	$high_2 = get_row_cnt($pdf, $col_width_1, $row_height_1, $mem['mem_counsel_result']);
	$high_3 = get_row_cnt($pdf, $col_width_1, $row_height_1, $mem['mem_counsel_other']);
	
	$height = $pdf->GetY();
	
	//높이비교변수
	$gY1 = $height + $high_0;
	$gY2 = $gY1 + $high_1;
	$gY3 = $gY2 + $high_2;
	$gY4 = $gY3 + $high_3;

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'상담자',1,0,'C',true);
	$pdf->Cell(35,$rows,$mem['mem_talker_nm'],1,0,'C');
	$pdf->Cell(22,$rows,'상담일자',1,0,'C',true);
	$pdf->Cell(35,$rows,str_replace('-','.', $mem['mem_counsel_dt']),1,0,'C');
	$pdf->Cell(22,$rows,'상담유형',1,0,'C',true);
	$pdf->Cell(46,$rows,'□내방 □방문 □전화',1,1,'C');
	
	//상담유형
	switch($mem['mem_counsel_gbn']){
		case 1:
			$pdf->SetXY(157.5,$height);
			$pdf->MultiCell($rows, $rows, 'v');
			break;
		case 2:
			$pdf->SetXY(168,$height);
			$pdf->MultiCell($rows, $rows, 'v');
			break;
		case 3:
			$pdf->SetXY(178.5,$height);
			$pdf->MultiCell($rows, $rows, 'v');
			break;
	}

	$height0 = $pdf->GetY();
	
	if($gY1 > $pdf->height){
		$pdf->AddPage(strtoupper('p'), 'A4');
	}

	$pdf->SetX(14);
	$pdf->Cell(22,$high_0,'상담내용',1,0,'C',true);
	$pdf->Cell(160,$high_0,'',1,1,'L');

	$height1 = $pdf->GetY();
	
	$pdf->SetXY(36,$height0);
	$pdf->MultiCell (158,4,$mem['mem_counsel_content']);

	if($gY1 < $pdf->height){
		if($gY2 > $pdf->height){
			$pdf->AddPage(strtoupper('p'), 'A4');
			$height1 = $pdf->GetY();
		}
	}
	
	$pdf->SetXY(14, $height1);
	$pdf->Cell(22,$high_1,'조치사항',1,0,'C',true);
	$pdf->Cell(160,$high_1,'',1,1,'L');
	
	$height2 = $pdf->GetY();

	$pdf->SetXY(36,$height1);
	$pdf->MultiCell (158,4,$mem['mem_counsel_action']);
	
	if($gY2 < $pdf->height){
		if($gY3 > $pdf->height){
			$pdf->AddPage(strtoupper('p'), 'A4');
			$height2 = $pdf->GetY();
		}
	}
	
	$pdf->SetXY(14, $height2);
	$pdf->Cell(22,$high_2,'처리결과',1,0,'C',true);
	$pdf->Cell(160,$high_2,'',1,1,'L');
	
	$height3 = $pdf->GetY();

	$pdf->SetXY(36, $height2);
	$pdf->MultiCell (158,4,$mem['mem_counsel_result']);
	
	if($gY3 < $pdf->height){
		if($gY4 > $pdf->height){
			$pdf->AddPage(strtoupper('p'), 'A4');

			$height3 = $pdf->GetY();
		}	
	}
	
	$pdf->SetXY(14, $height3);
	$pdf->Cell(22,$high_3,'기타',1,0,'C',true);
	$pdf->Cell(160,$high_3,'',1,1,'L');
		
	$pdf->SetXY(36,$height3);
	$pdf->MultiCell (158,4,$mem['mem_counsel_other']);
	
	
	$pdf->Output();

	include_once('../inc/_db_close.php');
	

	//표 칸높이를 구한다.
	function get_row_cnt($pdf, $col_w, $row_h, $text){
		
		$row_high = $pdf->row_height;
		$str_text =  explode("\n", stripslashes(str_replace(chr(13).chr(10), "\n", $text)));
		$str_cnt = sizeof($str_text);
		

		for($i=0; $i<$str_cnt; $i++){
			$str_wid = $pdf->GetStringWidth($str_text[$i]);
		
			if($str_wid > $col_w){
				$row_cnt += ceil($str_wid/$col_w);	
			}else {
				$row_cnt += 1;
			}
		}
		
		$row_high = $row_cnt*4;
		
		if($row_h > $row_high){
			$high = $row_h;
		}else {
			$high = $row_high;
		}

		return $high;
	}
?>