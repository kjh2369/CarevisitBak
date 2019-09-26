<?
	include('../inc/_db_open.php');
	include('../inc/_myFun.php');
	include('../inc/_ed.php');
	require('../pdf/pdf_gunbo_table.php');

	define('__ROW_LIMIT__',15);

	$conn->set_name('euckr');

	$code		= $_GET['code'];
	$kind		= $_GET['kind'];
	$year		= $_GET['year'];
	$month		= $_GET['month'];
	$SvcCd     = $_GET['svccd'];		//케어구분
	

	$sql = "select distinct m03_jumin
				  from m03sugupja
				 inner join t01iljung
					on t01_ccode = m03_ccode
				   and t01_mkind = m03_mkind
				   and t01_jumin = m03_jumin
				   and t01_sugup_date like '$year$month%'";
	
	if(!empty($SvcCd)) $sql .= " and t01_svc_subcode = '".$SvcCd."'";

	$sql .= "	   and t01_del_yn = 'N'
				 where m03_ccode = '$code'
				   and m03_mkind = '$kind'
				 order by m03_name";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row      = $conn->select_row($i);
		$list[$i] = $row[0];
	}

	$conn->row_free();

	//if (strLen($target) == 0) $target = $ed->de($_GET['target']);

	// 센터정보
	$sql = "select m00_cname, m00_ctel
			  from m00center
			 where m00_mcode = '$code'
			   and m00_mkind = '$kind'";
	$row = $conn->get_array($sql);
	$centerName = $row[0];
	$centerTel	= $myF->phoneStyle($row[1]);

	$pdf = new MYPDF('P');
	$pdf->AliasNbPages();
	$pdf->AddUHCFont('굴림','Gulim');
	$pdf->Open();
	$pdf->SetFillColor(220,220,220);

	// 헤더값 설정
	$pdf->year			= $year;		//년
	$pdf->month			= $month;		//월
	$pdf->centerName	= $centerName;	//센터명
	$pdf->centerTel		= $centerTel;	//선터전화번호

	$pdf->AddPage('L', 'A4');

	$row_no = 0;

	for($l=0; $l<sizeOf($list); $l++){

		$target = $list[$l];
		
		//수급자 및 인정번호 조회

		$sql = "select m03_name
				,      lvl.app_no
				,	   m03_jumin
				  from m03sugupja
				  left join (
					   select jumin
					   ,	  svc_cd
					   ,      app_no
						 from client_his_lvl
						where org_no = '$code'
						  and date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')
						  and date_format(now(),'%Y%m%d') <= date_format(to_dt,  '%Y%m%d')
					   ) as lvl
					on m03_jumin = lvl.jumin
				   and m03_mkind = lvl.svc_cd
				 where m03_ccode = '$code'
				   and m03_mkind = '$kind'
				   and m03_jumin = '$target'";
		$row2 = $conn->get_array($sql);

		// 배경색상 설정
		//$pdf->SetFillColor(200,200,200);
		$height	= $pdf->rowHeight;

		$date = $pdf->year.$pdf->month;
		$lastDay = date('t', $calTime); //총일수 구하기
		
		
		$sql = "select cast(date_format(t01_sugup_date, '%d') as signed)
				,      date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%H:%i')
				,      date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%H:%i')
				,      t01_mem_nm1
				,      t01_mem_nm2
				,      m01_suga_cont
				,      t01_svc_subcode
				,	   t01_mem_cd1
				,	   t01_mem_cd2
				  from t01iljung
				 inner join (
					   select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
						 from m01suga
						where m01_mcode = '$code'
						union all
					   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
						 from m11suga
						where m11_mcode = '$code'
					   ) as suga
					on t01_suga_code1 = m01_mcode2
				   and t01_sugup_date between m01_sdate and m01_edate
				 where t01_ccode  = '$code'
				   and t01_mkind  = '$kind'
				   and t01_jumin  = '$target'
				   and t01_sugup_date like '$date%' $family_sql
				   and t01_del_yn = 'N'";
		
		if(!empty($SvcCd)) $sql .= " and t01_svc_subcode = '$SvcCd'";

		$sql .= " order by t01_mem_nm1, t01_mem_nm2, t01_svc_subcode, t01_sugup_fmtime, t01_sugup_totime, t01_sugup_date";

		
		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();

		$pdf->SetFont('굴림','B',9);
		$pdf->SetXY($pdf->left, $pdf->GetY());

		$pdf->SetFont('굴림','',9);
		
		$tempDate = '';
		$seq = 0;

		unset($svc);

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$temp_data = $row2[0].'_'.$row2[1].'_'.$row[7].'_'.$row[3].'_'.$row[4].'_'.$row[1].'_'.$row[2];

			if ($tempData != $temp_data){
				$tempData  = $temp_data;

				$svc[$seq]['order']	= $row[6].'_'.$row[1].'_'.$row[2].'_'.$row[3].'_'.$row[3];	
				$svc[$seq]['start']	= $row[1];													//제공시작시간
				$svc[$seq]['end']	= $row[2];													//제공종료시간
				$svc[$seq]['yoy1']	= $row[3];													//주요양사
				$svc[$seq]['yoy2']	= $row[4];													//부요양사
				$svc[$seq]['svc']	= $row[5];													//서비스명
				$svc[$seq]['su']	= $row2[0];													//수급자명
				$svc[$seq]['jumin'] = $row2[2];													//수급자주민번호
				$svc[$seq]['yjumin'] = $row[7];													//주요양사주민
				$svc[$seq]['yjumin2'] = $row[8];												//부요양사주민
				$svc[$seq]['injung_no']	= $row2[1];												//인정번호
				$svc[$seq]['birth']	= substr($row[7], 0, 6);									//주요양사생년월일
				$svc[$seq]['birth2'] = substr($row[8], 0, 6);									//부요양사생년월일
				$svc[$seq]['count']	= 0;														//횟수
				$svc[$seq]['days']	= '/';
				$seq ++;
			}
			$svc[$seq-1]['count'] ++;															//횟수 증가
			$svc[$seq-1]['days'] .= $row[0].'/';												
		}

		$conn->row_free();

		$temp_svc = $myF->sortArray($svc, 'order', 1);
		$svc = $temp_svc;

		$height = 5.1;

		for($i=0; $i<sizeOf($svc); $i++){

			$pdf->SetTextColor(0,0,0);
			$pdf->SetX($pdf->left);
			$su = '';
			$injung_no = '';
			$yoy = '';
			$birth = '';
			$yoy2 = '';
			$birth2 = '';

			if($temp != $svc[$i]['jumin']){
				$temp = $svc[$i]['jumin'];
				if($temp != $svc[$i+1]['jumin']){
					$mode = 'LRB';
				}else {
					$mode = 'LR';
				}
				$su = $svc[$i]['su'];
				$injung_no = $svc[$i]['injung_no'];

			}else if($temp == $svc[$i+1]['jumin']){
				$mode = 'LR';

			}else {
				$mode = 'LRB';
			}

			if($temp2 != $svc[$i]['jumin'].'_'.$svc[$i]['yjumin'].'_'.$svc[$i]['yjumin2']){
				$temp2 = $svc[$i]['jumin'].'_'.$svc[$i]['yjumin'].'_'.$svc[$i]['yjumin2'];
				if($temp2 != $svc[$i+1]['jumin'].'_'.$svc[$i+1]['yjumin'].'_'.$svc[$i+1]['yjumin2']){
					$mode2 = 'LRB';
				}else {
					$mode2 = 'LR';
				}

				$yoy = $svc[$i]['yoy1'];
				$birth = $svc[$i]['birth'];
				$yoy2 = $svc[$i]['yoy2'];
				$birth2 = $svc[$i]['birth2'];

			}else if($temp2 == $svc[$i+1]['jumin'].'_'.$svc[$i+1]['yjumin'].'_'.$svc[$i+1]['yjumin2']){
				$mode2 = 'LR';
			}else {
				$mode2 = 'LRB';
			}
			//$yoy = $svc[$i]['yoy1'];
			//$birth = $svc[$i]['birth'];

			$row_no ++;

			if ($row_no % __ROW_LIMIT__ == 0){
				$mode = 'LRB';
				$mode2 = 'LRB';
			}


			if($row_no % __ROW_LIMIT__ == 1 && $su == '') $su =  $svc[$i]['su'];
			if($row_no % __ROW_LIMIT__ == 1 && $injung_no == '') $injung_no =  $svc[$i]['injung_no'];
			if($row_no % __ROW_LIMIT__ == 1 && $yoy == '') $yoy =  $svc[$i]['yoy1'];
			if($row_no % __ROW_LIMIT__ == 1 && $yoy2 == '') $yoy2 =  $svc[$i]['yoy2'];
			if($row_no % __ROW_LIMIT__ == 1 && $birth == '') $birth =  $svc[$i]['birth'];
			if($row_no % __ROW_LIMIT__ == 1 && $birth2 == '') $birth2 =  $svc[$i]['birth2'];


			$pdf->Cell(30,	$height,	$su,	'LR',	0, 'L');

			/*
			if ($svc[$i]['yoy2'] == ''){
				if($svc[$i+2]['yoy1'] != $svc[$i]['yoy1']){
					$pdf->Cell(40,	$height*2,	$yoy.'  '.$birth,	'LR',	0, 'L');
				}else {
					$pdf->Cell(40,	$height,	$yoy.'  '.$birth,	'LR',	0, 'L');
				}
			}else{
				$pdf->Cell(40,	$height,	$yoy.'  '.$birth,	'LR',	0, 'L');
			}
			*/


			$pdf->Cell(40,	$height,	$yoy.'  '.$birth,	'LR',	0, 'L');
			$pdf->Cell(30,	$height*2, $svc[$i]['start'].'~'.$svc[$i]['end'],	"LTR",	0, 'C');

			$Y = $pdf->GetY()+$height;

			$pdf->Cell(150,	$height,	$svc[$i]['svc'],	"LTR",	0, 'L');
			$pdf->Cell(20,	$height, '',	"LR",	0, 'C');
			$pdf->SetXY($pdf->left, $Y);
			$pdf->Cell(30,	$height,	$injung_no,	$mode,	0, 'L');
			$pdf->Cell(40,	$height,	$yoy2.'  '.$birth2,	$mode2,	0, 'L');
			$pdf->Cell(30,	$height,	'',	"LBR",	0, 'L');
			//$pdf->Cell(112,	$height,	$svc[$i]['days'],	"LBR",	1, 'L');

			for($j=1; $j<=31; $j++){
				if (strVal(strPos($svc[$i]['days'], "/$j/")) == ''){
					$pdf->SetTextColor(220,220,220);
				}else{
					$pdf->SetTextColor(0,0,0);
				}

				if ($j < 10){
					$cellWidth = 4.1;
				}else if ($j == $lastDay){
					$cellWidth = 6;
				}else{
					$cellWidth = 5.1;
				}

				if ($j <= $lastDay){
					if ($j == 1){
						$pdf->Cell($cellWidth, $height, "$j", "LB", 0, 'C');
					}else if ($j == 31){
						$pdf->Cell($cellWidth, $height, "$j", "RB", 0, 'C');
					}else{
						$pdf->Cell($cellWidth, $height, "$j", "B", 0, 'C');
					}
				}else{
					if ($j == 31){
						$pdf->Cell($cellWidth, $height, "", "RB", 0, 'C');
					}else{
						$pdf->Cell($cellWidth, $height, "", "B", 0, 'C');
					}
				}
			}

			$pdf->SetTextColor(0,0,0);

			$pdf->Cell(20,	$height,	number_format($svc[$i]['count']),	"LBR",	1, 'C');

		}
	}

	$pdf->Output();

	include('../inc/_db_close.php');
?>
<script>self.focus();</script>