<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');
	include('../inc/_ed.php');
	include('../inc/_myfun.php');
	require('../pdf/korean.php');

	$conn->set_name('euckr');
	
	$mCode = $_GET['mCode'];
	$mKind = $_GET['mKind'];
	$yKey = $_GET['key'];
	
	

	$sql = "select *"
			 . "  from m02yoyangsa"
			 . " where m02_ccode  = '".$mCode
			 . "'  and m02_mkind  = '".$mKind
			 . "'  and m02_key = '".$yKey
			 . "'";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();
	
	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			
		}
	}
	$conn->row_free();

	$sql = "select m00_mname as manager"
	 . ",      concat(m00_caddr1, ' ', m00_caddr2) as address"
  	 . ",      m00_cname as centerName"
	 . ",      m00_code1 as centerCode"
	 . ",      m00_ctel as centerTel"
	 . "  from m00center"
	 . " where m00_mcode = '".$mCode
	 . "'  and m00_mkind = '".$mKind
	 . "'";
	$center = $conn->get_array($sql);
	

	$pdf=new PDF_Korean('P');
	$pdf->AddUHCFont('바탕','Batang');
	$pdf->Open();
	
	$pdf->SetFillColor(238,238,238);

	$pdf->AddPage();
	$pdf->SetXY(14, 20);
	//$pdf->SetFillColor('255');
	$pdf->Rect(14, 20, 182, 260);
	$pdf->SetLineWidth(0.2);
	$pdf->Line(13.3,19.3,196.7,19.3);
	$pdf->Line(13.3,280.7,196.7,280.7);
	$pdf->Line(13.3,19.3,13.3,280.7);
	$pdf->Line(196.7,19.3,196.7,280.7);
	
	$pdf->SetFont('바탕','BU',20);

	$pdf->text(90,32,'근로계약서');
	
	$pdf->SetFont('바탕','B',10);

	$pdf->text(25,50,'근로계약의 양 당사자는 각자 자유로운 의사로써 이 계약을 체결하고 신의에 따라 성실하게 이를 이행');
	$pdf->text(25,55,'한다.');
	
	
	$pdf->AddUHCFont('굴림','Gulim');

	$pdf->SetFont('굴림','',9);

	$pdf->SetXY(25, 60);
	$pdf->SetLineWidth(0.2);
	$pdf->Rect(25, 60, 160, 25);
	$pdf->SetLineWidth(0.2);

	$pdf->SetX(25);
	$pdf->Cell(18,5,'구분',1,0,'C');
	$pdf->Cell(142,5,'내        용',1,1,'C');
	
	$pdf->SetX(25);
	$pdf->Cell(18,20,'당사자',1,0,'C');
	$pdf->Cell(7,10,'갑',1,0,'C');
	$pdf->Cell(20,5,'사업체명',1,0,'C');
	$pdf->Cell(65,5,$center['centerName'],1,0,'C');
	$pdf->Cell(20,5,'대 표 자',1,0,'C');
	$pdf->Cell(30,5,$center['manager'],1,1,'C');
	
	$pdf->SetX(25);
	$pdf->Cell(18,0,'',0,0,'C');
	$pdf->Cell(7,0,'',0,0,'C');
	$pdf->Cell(20,5,'소재지',1,0,'C');
	$pdf->SetFont('굴림','',6);
	$pdf->Cell(65,5,strlen($center['address']) >= 42 ? substr($center['address'],0,40).'...' : $center['address'],1,0,'C');
	$pdf->SetFont('굴림','',9);
	$pdf->Cell(20,5,'연 락 처',1,0,'C');
	$pdf->Cell(30,5,getPhoneStyle($center['centerTel']),1,1,'C');
	
	$pdf->SetX(25);
	$pdf->Cell(18,0,'',0,0,'C');
	$pdf->Cell(7,10,'을',1,0,'C');
	$pdf->Cell(20,5,'성     명',1,0,'C');
	$pdf->Cell(65,5,$row['m02_yname'],1,0,'C');
	$pdf->Cell(20,5,'주민등록번호',1,0,'C');
	$pdf->Cell(30,5,subStr($row['m02_yjumin'],0,6).'-'.subStr($row['m02_yjumin'],6,1).'******',1,1,'C');
	
	$pdf->SetX(25);
	$pdf->Cell(18,0,'',0,0,'C');
	$pdf->Cell(7,0,'',0,0,'C');
	$pdf->Cell(20,5,'주소(거소지)',1,0,'C');
	$pdf->SetFont('굴림','',6);
	if(strlen($row['m02_yjuso1'].$row['m02_yjuso2']) >= 42){
	$pdf->Cell(65,5,' ('.getPostNoStyle($row['m02_ypostno']).')'.substr($row['m02_yjuso1'].$row['m02_yjuso2'],0,42).'...',1,0,'C');
	}else{
	$pdf->Cell(65,5,$row['m02_ypostno'] == true ? ' ('.getPostNoStyle($row['m02_ypostno']).')'.$row['m02_yjuso1'].$row['m02_yjuso2'] : ' '.$row['m02_yjuso1'].$row['m02_yjuso2'],1,0,'C');
	}
	$pdf->SetFont('굴림','',9);			
	$pdf->Cell(20,5,'연락처',1,0,'C');
	$pdf->Cell(30,5,getPhoneStyle($row["m02_ytel"]),1,1,'C');

	$pdf->SetFont('굴림','B',10);
	$pdf->text(25,95,'제 1 조 (근무 장소 및 담당 업무)');
	$pdf->text(25,125,'제 2 조 (근로계약기간)');
	$pdf->text(25,170,'제 3 조 (근무시간 및 근무형태)');
	$pdf->text(25,230,'제 4 조 (휴가 및 휴일)');


	$pdf->SetFont('굴림','',9);
	$pdf->text(25,100,'① 취업 장소 :    "갑"의 본사 및 "갑"이 지정한 수급자의 거주지');
	$pdf->text(25,105,'② 담당 업무 : 노인장기요양보호법에 따른 요양업무');
	$pdf->text(25,110,'③ 전 ①항은 및 ②항은 "갑" 의 경영사정에 따라 변경될 수 있으며, "을" 이 변경지시에 불응 시에는 사유발생');
	$pdf->text(30,115,'일로부터 근로계약은 자동 종료된다.');
	
	$date = $row["m02_yipsail"];
	$date1 = date("Y-m-d",strtotime("+1 year, $date"));
	$toDate = date("Y-m-d",strtotime("-1 day, $date1"));
	
	$pdf->text(25,130,'① 본 근로 계약의 기간은  '.getDateStyle($row["m02_yipsail"]).'  ~  '.$toDate.'  로 한다.');

	$pdf->text(25,135,'② 본 계약은 상기 ①항의 기간만료 30일전에 별도의 연장이나 갱신에 대한 쌍방의 합의가 없으면 근로계약기');
	$pdf->text(30,140,'간의 만료에 따라 자동적으로 계약이 종료되는 것으로 본다.');
	$pdf->text(25,145,'③ "을"이 계약기간 중 퇴직을 희망할 경우 퇴직희망일 30일전까지 사직원을 직접 작성하여 제출하여 허가를 득');
	$pdf->text(30,150,'하여야 하며, 회사로부터 지급받은 일체의 장비와 비품 및 소모품 등을 갑에게 반납하여야 한다.');
	$pdf->text(25,155,'④ 동조 제③항에 의거 상호합의가 이루어지지 않은 경우에는 무단결근으로 처리하며, 이에 대하여 회사의 손해');
	$pdf->text(30,160,'(인수인계 불성실 등 기타 사유로 인한 손해)가 있을 때에는 "을"은 "갑"에 대하여 민사상 손해배상 책임을 진다.');

	$pdf->text(25,175,'① 근로시간은 휴게시간을 제외하고 1주 40시간을 기본근로시간으로 한다.');
	$pdf->text(25,180,'② "을"의 소정근로시간 및 소정근로일은 다음과 같다.');
	
	$pdf->SetXY(40,185);
	$pdf->SetLineWidth(0.6);
	$pdf->Rect(40, 185, 110, 20);
	$pdf->SetLineWidth(0.2);
	
	$pdf->Cell(28,5,'',1,0,'C');
	$pdf->Cell(82,5,'월요일~금요일',1,1,'C');
	
	$pdf->SetX(40);
	$pdf->Cell(28,5,'시업시각',1,0,'C');
	$pdf->Cell(82,5,'AM9:00',1,1,'C');
	
	$pdf->SetX(40);
	$pdf->Cell(28,5,'종업시각',1,0,'C');
	$pdf->Cell(82,5,'오후 6:00',1,1,'C');
	
	$pdf->SetX(40);
	$pdf->Cell(28,5,'휴게시간',1,0,'C');
	$pdf->Cell(82,5,'12:00~13:00',1,1,'C');

	$pdf->text(25,215,'③ 전 ①항 및 ②항은 "갑" 의 경영상 필요와 계절의 변화, 사업장 사정과 "을"의 근로형태에 따라 개별로 운영');
	$pdf->text(30,220,'할 수 있다.');
	
	$pdf->text(25,235,'① 회사는 법정휴일, 약정휴일, 근로의무가 정지된 날을 제외한 전년도 1년간 소정일수를 8할이상 출근한 직원');
	$pdf->text(30,240,'에게 당해년도 1년사이에 15일의 연차유급휴가를 부여한다. 단, 근속기간이 1년미만인 직원은 전월 개근시');
	$pdf->text(30,245,' 1일의 연차휴가를 사용할 수 있으며 다음년도 15일에서 사용일수를 차감한다.');
	$pdf->text(25,250,'② "갑"의 경영상 필요한 경우 "을" 과 협의하여 법정휴일/휴가일이 변경 또는 수당으로 대체 지급될 수 있다.');
	//$pdf->text(30,255,'있다.');
	$pdf->text(25,255,'③ 회사는 여자직원에게 월 1일의 무급생리휴가를 생리휴가의 청구가 있는 날에 부여한다.');
	$pdf->text(25,260,'④ 연차휴가는 사유발생일에 "을" 의 청구와 "갑" 의 업무에 지장이 없는 범위내에서 자유로이 사용할 수 있다.');
	$pdf->text(30,265,'단, 근로기준법에 의거 서면합의에 따라 취업규칙에 명시한 유급휴일 외에 주중의 국공휴일 및 결근일에 우선');
	$pdf->text(30,270,'적으로 연차유급휴가를 사용토록 한다.');

	$pdf->AddPage('P','A4');
	$pdf->SetXY(14, 20);
	//$pdf->SetFillColor('255');
	$pdf->Rect(14, 20, 182, 260);
	$pdf->SetLineWidth(0.2);
	$pdf->Line(13.3,19.3,196.7,19.3);
	$pdf->Line(13.3,280.7,196.7,280.7);
	$pdf->Line(13.3,19.3,13.3,280.7);
	$pdf->Line(196.7,19.3,196.7,280.7);
	
	$pdf->text(25,40,'⑤ 휴일은 각 호와 같다');
	$pdf->text(25,45,'1. 1주 소정근로일을 개근한 직원에게 1일을 유급의 주휴일로 부여한다.');
	$pdf->text(25,50,'2. 모든 직원에게 근로자의 날을 유급의 휴일로 부여한다.');
	$pdf->text(25,55,'3. 기타 휴일은 취업규칙이 정하는 바에 따른다.');
	$pdf->text(25,60,'⑥ 회사는 직원의 동의를 얻어 유급 휴일을 다른 날로 대체할 수 있다.');

	$pdf->SetFont('굴림','B',10);
	$pdf->text(25,70,'제 5 조 (임금)');
	$pdf->text(25,180,'제 6 조 (해고 및 징계사유)');
	$pdf->text(25,230,'제 7 조 (퇴직금)');

	$pdf->SetFont('굴림','',9);
	$pdf->text(25,76,'① 임금은 월급       '.$row["m02_ygibonkup"].' 원으로 하여, 월급으로 지급하며 그 급여의 구성은 다음과 같다.');
	$pdf->SetXY(25, 78);
	$pdf->SetLineWidth(0.2);
	
	$pdf->SetFont('굴림','B',11);
	$pdf->SetX(25);
	$pdf->Cell(48,5,'1. 항목',1,0,'C');
	$pdf->Cell(35,5,'2. 금 액',1,0,'C');
	$pdf->Cell(79,5,'3. 산출근거',1,1,'C');

	$pdf->SetFont('굴림','',9);
	$pdf->SetX(25);
	$pdf->SetX(25);
	$pdf->Cell(48,5,'시  급',1,0,'C');
	if($row["m02_ygupyeo_kind"]=='1' or $row["m02_ygupyeo_kind"]=='2'){
	$pdf->Cell(35,5,'￦'.$row["m02_ygibonkup"].' ',1,0,'R');
	} else {
	$pdf->Cell(35,5,'',1,0,'C');
	}
	$pdf->Cell(79,5,'',1,1,'C');

	$pdf->SetX(25);
	$pdf->Cell(48,5,'월 기본급[주휴포함]',1,0,'C');
	if($row["m02_ygupyeo_kind"]=='3'){
	$pdf->Cell(35,5,'￦'.$row["m02_ygibonkup"].' ',1,0,'R');
	} else {
	$pdf->Cell(35,5,'',1,0,'C');
	}
	$pdf->Cell(79,5,'(주소정근로시간[주휴포함]X52.14주/12월',1,1,'L');

	$pdf->SetX(25);
	$pdf->Cell(48,5,'식대보조비',1,0,'C');
	$pdf->Cell(35,5,'',1,0,'C');
	$pdf->Cell(79,5,'[대상자에 한함]',1,1,'L');
	
	$pdf->SetX(25);
	$pdf->Cell(48,5,'차량유지비',1,0,'C');
	$pdf->Cell(35,5,'',1,0,'C');
	$pdf->Cell(79,5,'[대상자에 한함]',1,1,'L');

	$pdf->text(25,113,'② 다음과 같이 기타 수당을 지급한다.');
	$pdf->SetXY(25, 116);
	$pdf->SetLineWidth(0.2);
	$pdf->SetFont('굴림','B',11);
	$pdf->SetX(25);
	$pdf->Cell(48,5,'1. 항  목',1,0,'C');
	$pdf->Cell(114,5,'2. 지급규정',1,1,'C');
	

	$pdf->SetFont('굴림','',9);
	$pdf->SetX(25);
	$pdf->Cell(48,15,'',1,0,'C');
	$pdf->Cell(114,15,' 연장, 야간, 휴일 근로에 대해서는 통상시급의 50%를 가산하여 지급한다.',1,1,'L');
	
	$pdf->SetX(25);
	$pdf->Cell(48,10,'특별수당',1,0,'C');
	$pdf->Cell(114,10,'',1,1,'L');
	$pdf->Line(25,126,73,126);
	$pdf->Line(25,131,73,131);
	
	$pdf->text(40,125,'연장근로수당');
	$pdf->text(40,130,'야간근로수당');
	$pdf->text(40,135,'휴일근로수당');

	$pdf->text(75,139.5,'회사의 경영성과 및 개인의 업무평가에 따라 필요하다고 인정할 경우 대');
	$pdf->text(75,144.5,'표자의 결정으로 특별수당을 지급할 수 있다.');


	$pdf->SetFont('굴림','',9);
	$pdf->text(25,155,'③ 통상 임금에 산입하는 임금의 범위는 법정제수당 및 복리후생적, 생활보조적, 근로여부에 따라 지급금액이 변동');
	$pdf->text(30,160,'되는 금품을 제외한 금액으로 한다.');
	$pdf->text(25,165,'④ 월간 급여는 매월 1일에서 매월 말일까지 정산하고, 익월 1일에 을이 지정한 예금계좌에 입금하여 지급한다.');
	//$pdf->text(30,175,'급한다.');
	$pdf->text(25,170,'⑤ 매월 급여정산시에는 사회보험료 등 "을"의 원천징수 부담분을 공제후 지급한다.');

	$pdf->text(25,185,'① "을" 이 안전수칙 불이행, 취업규칙 불이행으로 2회이상 경고처분을 받았을 때.');
	//$pdf->text(30,195,'니된다.');
	$pdf->text(25,190,'② "을" 이 정당한 업무지시 불이행 및 고의, 중대한 과실로 사고나 손실을 야기시킨 경우');
	$pdf->text(25,195,'③ "을" 이 근무성적 또는 능력이 현저하게 불량하여 업무수행이 불가능하다고 인정될 때.');
	$pdf->text(25,200,'④ "을" 이 "갑"의 동의 없이 타 직장에 취업한 때.');
	$pdf->text(30,205,'⑤ "을" 이 퇴직을 원할 때 또는 근로계약기간이 종료 되었을 때.');
	$pdf->text(25,210,'⑥ 도박 또는 풍기문란 행위나 기타 사회통념상 고용관계를 지속시킬 수 없다고 인정되는 경우.');
	$pdf->text(30,215,'⑦ 고객에게 3회 이상 불친절하여 고객으로부터 항의를 받아 영업에 지장을 끼친 경우.');
	$pdf->text(25,220,'⑧ 정기 또는 수시검진 결과 부적당하다고 판단된 경우.');

	$pdf->text(25,235,'회사는 1년 이상 근무한 사원이 퇴직할 경우에는 근속연수 1년에 대하여 평균임금 30일분을 퇴직금으로 지급한다.');
	
	$pdf->SetFont('바탕','B',9);
	$pdf->text(25,245,'"을"은 "갑" 회사의 사규를 열람하고, 본 계약서 및 취업규칙에 없는 사항에 대해서는 노동관계법령 및 제규정과');
	$pdf->text(25,250,'"갑"의 해석에 따르기로 한다.');
	
	$pdf-> text(93,260,substr($row["m02_yipsail"],0,4).'년  '.substr($row["m02_yipsail"],4,2).'월  '.substr($row["m02_yipsail"],6,2).'일');
	$pdf->SetFont('굴림','B',10);
	$pdf->text(25,270.2,'사용자(갑)');
	$pdf->text(112,270.2,'근로자(을)');
	$pdf->SetXY(44,264);
	$pdf->cell(33,10,$center['manager'].' ',0,1,'R');
	$pdf->SetXY(131,264);
	$pdf->cell(32,10,$row['m02_yname'].' ',0,1,'R');

	$pdf->SetFont('굴림','',9);
	
	$pdf->text(79,270.2,'(서명 또는 인)');
	$pdf->text(165,270.2,'(서명 또는 인)');
	
	

	$pdf->Output();
	
	include('../inc/_db_close.php');
?>


