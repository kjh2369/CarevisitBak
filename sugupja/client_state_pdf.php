<?
	
	if (!Is_Array($var)){
		exit;
	}

	
	$code	= $_SESSION['userCenterCode'];
	$year	= Str_Replace('.','',$var['year']);
	$month	= Str_Replace('.','',$var['month']);
	
	
	$month	= ($month < 10 ? '0' : '').$month;
	

	$sql = 'SELECT	DISTINCT
					m03_jumin		AS jumin
			,		m03_name		AS name
			,		m03_juso1		AS addr
			,		m03_juso2		AS addr_dtl
			,		m03_tel			AS phone
			,		m03_hp			AS mobile
			,		m03_yboho_name	AS grd_nm
			,		svc.from_dt		AS from_dt
			,		svc.to_dt		AS to_dt
			,		lvl.app_no		AS app_no
			,		lvl.level		AS lvl
			,		kind.kind		AS kind
			FROM	m03sugupja
			INNER	JOIN	client_his_svc AS svc
					ON		svc.org_no	= m03_ccode
					AND		svc.svc_cd	= m03_mkind
					AND		svc.jumin	= m03_jumin
					/*AND		svc.svc_stat= \'1\'*/
					AND		DATE_FORMAT(svc.from_dt,\'%Y%m\')	<= \''.$year.$month.'\'
					AND		DATE_FORMAT(svc.to_dt,\'%Y%m\')		>= \''.$year.$month.'\'
			INNER	JOIN	client_his_lvl AS lvl
					ON		lvl.org_no	= m03_ccode
					AND		lvl.svc_cd	= m03_mkind
					AND		lvl.jumin	= m03_jumin
			INNER	JOIN	client_his_kind AS kind
					ON		kind.org_no	= m03_ccode
					AND		kind.jumin	= m03_jumin
			WHERE	m03_ccode = \''.$code.'\'
			AND		m03_mkind = \'0\'
			ORDER	BY name';

	$arr = $conn->_fetch_array($sql,'jumin');
	
	
	$height = $pdf->row_height;


	$pdf->SetFont($pdf->font_name_kor,'',10);
	$pdf->SetFillColor(238,238,238);
	$pdf->SetXY($pdf->left, $pdf->GetY());

	$no = 0;

	if (Is_Array($arr)){
		foreach($arr as $row){
			$coordY = $pdf->GetY();
			$coordX = $pdf->left;

			if ($coordY > $pdf->height){
				//$pdf->Line($pdf->left,$coordY2,$pdf->left+$pdf->width,$coordY2);
				$pdf->addPage();
			}
			
			if ($row['kind'] == '3'){
				$row['kind'] = '기초';
			}else if ($row['kind'] == '2'){
				$row['kind'] = '의료';
			}else if ($row['kind'] == '4'){
				$row['kind'] = '경감';
			}else if ($row['kind'] == '1'){
				$row['kind'] = '일반';
			}else{
				$row['kind'] = '';
			}

			if ((SubStr($row['jumin'],6,1) % 2) == '1'){
				$gender = '남';
			}else{
				$gender = '여';
			}
			
			
			$pdf->SetX($pdf->left);
			$pdf->Cell($pdf->width*0.04,$height,($no+1).' ',1,0,'R');
			$pdf->Cell($pdf->width*0.07,$height,$row['name'],1,0,'L');
			$pdf->Cell($pdf->width*0.125,$height,$myF->issStyle($row['jumin']),1,0,'C');
			$pdf->Cell($pdf->width*0.105,$height,$row['app_no'],1,0,'C');
			$pdf->Cell($pdf->width*0.04,$height,$row['kind'],1,0,'C');
			$pdf->Cell($pdf->width*0.04,$height,$gender,1,0,'C');
			$pdf->Cell($pdf->width*0.17,$height,$myF->dateStyle(Str_Replace('-','',$row['from_dt']),'.').'~'.$myF->dateStyle(Str_Replace('-','',$row['to_dt']),'.'),1,0,'C');
			$pdf->Cell($pdf->width*0.18,$height,$pdf->_splitTextWidth($myF->utf($row['addr'].' '.$row['addr_dtl']),50),1,0,'L');
			$pdf->Cell($pdf->width*0.12,$height,($row['phone'] ? $myF->phoneStyle($row['phone']) : $myF->phoneStyle($row['mobile'])),1,0,'L');
			$pdf->Cell($pdf->width*0.07,$height,$row['grd_nm'],1,0,'L');
			$pdf->Cell($pdf->width*0.04,$height,'',1,1,'L');
			
			$no ++;
		}
	}
	
?>