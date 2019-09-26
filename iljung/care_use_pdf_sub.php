<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_function.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_report.php');


	/**************************************************

		페이지 세로, 가로 구분

	**************************************************/
	//$paper_dir = ($_GET['paper_dir'] == 1 ? 'P' : 'L');
	$paper_dir = 'p';


	/**************************************************

		빈양식 출력여부

	**************************************************/
	//$blank = $_GET['blank'];
	$blank = $_POST['blank'];




	/**************************************************

		파라메타

	**************************************************/
	$code         = $_POST['code'];
	$mode         = $_POST['mode'];
	$year         = $_POST['year'];
	$month        = ($_POST['month']<10? '0'.$_POST['month'] : $_POST['month']);
	$svcGbn       = Explode(chr(1),$_POST['svcGbn']);
	$printDT      = $_POST['printDT'];
	$data         = explode('?', $_POST['data']);
	
	
	/**************************************************

		기본설정

	**************************************************/
	#케릭터변경
	$conn->set_name('euckr');

	#기관명
	$center_nm   = $conn->center_name($code);
	$center_icon = $conn->center_icon($code);
	
	
	//기관 로고
	$sql = 'select m00_icon
			  from m00center
			 where m00_mcode = \''.$_SESSION['userCenterCode'].'\'';
	$icon = $conn -> get_data($sql);
	
	
	@require_once('./care_use_pdf_header.php');
	

	/**************************************************

		PDF OPEN

	**************************************************/
	$pdf = new MYPDF(strtoupper($paper_dir));
	$pdf->ctIcon = $conn->center_icon($code);
	$pdf->ctName = $conn->center_name($code);
	$pdf->AddUHCFont($pdf->font_name_kor, $pdf->font_name_eng);
	$pdf->Open();
	$pdf->SetFillColor(220,220,220);
	$pdf->year = $year;
	$pdf->month = $month;
	$pdf->svcGbn = $svcGbn;
	$pdf->mode   = $mode;
	$pdf->printDT = $printDT;
	$pdf->domain	= $gDomain;
	$pdf->icon      = $icon;
	
	if($_POST['jumin'] != 'sel'){
		$data[0] = 'cltCd='.$_POST['jumin'];
	}
	if (is_array($data)){
		
		foreach($data as $tmpIdx => $R){
			parse_str($R,$R);
		
			$jumin = $ed->de($R['cltCd']);
			
			if($jumin){ 
				$sql = 'select m03_name as name
						,	   center.manager
						,	   concat(m03_juso1, \' \', m03_juso2) as address
						  from m03sugupja
						 inner join (select m00_mname as manager
									 ,		m00_mcode
									   from m00center) as center
							on center.m00_mcode = \''.$code.'\'					
						 where m03_ccode = \''.$code.'\'
						   and m03_jumin = \''.$jumin.'\'';
				
				$su  =  $conn -> get_array($sql);
				
				
				$sql = "select from_dt, to_dt
							from client_his_svc
						   where org_no = '".$code."'
							 and jumin  = '".$jumin."'
							 and date_format(from_dt,'%Y%m') <= '".$year.$month."'
							 and date_format(to_dt,'%Y%m') >= '".$year.$month."'
						   order by from_dt desc
						   limit 1";
				$svc = $conn -> get_array($sql); 
				
				/**************************************************

					PDF 헤더

				**************************************************/
				
				
				$lastday = date("t",mktime(0,0,0,$month,1,$year));		 //이달의 마지막날 구한다.

				$adress =  explode('<br />',nl2br($su['address']));
				
				$pdf->name   = $su['name'];
				$pdf->address = $adress[0];
				$pdf->contDt = $myF->dateStyle($svc['from_dt'], '.').' ~ '.$myF->dateStyle($svc['to_dt'], '.');
				$pdf->useDt = $year.'.'.$month.'.01 ~ '.$year.'.'.$month.'.'.$lastday;
				
				
				/**************************************************

					PDF START

				**************************************************/
				$pdf->MY_ADDPAGE();
	
							
				include('./care_use_'.$mode.'_print.php');
				
				
				/**************************************************

					PDF END

				**************************************************/
			}

		}
	}



	/**************************************************

		PDF CLOSE

	**************************************************/
	$pdf->Output();

	include_once('../inc/_db_close.php');


	function set_array_text($pdf, $pos){
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
		if (is_array($pos)){
			foreach($pos as $i => $p){
				$tmp_x = $pdf->GetX();
				$tmp_y = $pdf->GetY();

				if ($p['type'] == 'multi_text' ||
					$p['type'] == 'text'){
					if (!empty($p['font_size']))
						$pdf->SetFont($pdf->font_name_kor, $p['font_bold'].$p['font_style'], $p['font_size']);
					else
						$pdf->SetFont($pdf->font_name_kor, '', 10);

					$pdf->SetTextColor($p['text_color']['r'], $p['text_color']['g'], $p['text_color']['b']);
				}

				if ($p['type'] == 'multi_text'){
					$pdf->SetXY($p['x'], $p['y']);
					$pdf->MultiCell($p['width'], $p['height'], $p['text'], $p['border'], $p['align']);
				}else if ($p['type'] == 'text'){
					$pdf->Text($p['x'], $p['y'], $p['text']);
				}else if ($p['type'] == 'image'){
					$pdf->Image($p['text'], $p['x'], $p['y'], $p['width'], $p['height']);
				}
			}
		}
	}
?>