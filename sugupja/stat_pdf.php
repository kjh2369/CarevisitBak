<?

	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');



	$code  = $var['code'] != '' ? $var['code'] : $code;;
	$jumin = $var['jumin'] != '' ? $var['jumin'] : $jumin;
	$regDt = $var['regDt'] != '' ? $var['regDt'] : $yymm;


	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetFont($pdf->font_name_kor, 'B', 15);

	if ($code == '31138000044'){
		$pdf->Cell($pdf->width, $pdf->row_height * 2, "수급자(보호자)상담일지", 0, 1, "C");
	}else{
		$pdf->Cell($pdf->width, $pdf->row_height * 2, "상태변화관리", 0, 1, "C");
	}

	//기관정보
	$sql = 'SELECT m00_code1 AS code
			,      m00_cname AS name
			  FROM m00center
			 WHERE m00_mcode = \''.$code.'\'
			 ORDER BY m00_mkind
			 LIMIT 1';
	$laData = $conn->get_array($sql);

	$pdf->SetFont($pdf->font_name_kor, '', $pdf->font_size-1);
	$pdf->SetXY($pdf->left, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, "기관기호", 1, 0, "C", 1);
	$pdf->Cell($pdf->width * 0.25, $pdf->row_height, $laData['code'], 1, 0, "L");
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, "기 관 명", 1, 0, "C", 1);
	$pdf->Cell($pdf->width * 0.45, $pdf->row_height, $laData['name'], 1, 1, "L");

	UnSet($laData);

	//고객정보
	$sql = 'SELECT m03_name AS c_nm
			,      m03_tel AS c_phone
			,      m03_hp AS c_mobile
			,      m03_post_no AS c_postno
			,      m03_juso1 AS c_addr
			,      m03_juso2 AS c_addr_dtl
			,      m03_yboho_name AS p_nm
			,      m03_yboho_gwange AS p_reg
			,      m03_yboho_phone AS p_phone
			  FROM m03sugupja
			 WHERE m03_ccode = \''.$code.'\'
			   AND m03_jumin = \''.$jumin.'\'
			 ORDER BY m03_mkind
			 LIMIT 1';
	$laData = $conn->get_array($sql);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, "고 객 명", 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.40, $pdf->row_height, $laData['c_nm'], 1, 0, 'L');
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height * 2, "연 락 처", 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height, "유     선", 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.20, $pdf->row_height, $myF->phoneStyle($laData['c_phone'],'.'), 1, 1, 'L');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, "주민번호", 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.40, $pdf->row_height, $myF->issStyle($jumin), 1, 0, 'L');
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, "", 0, 0, 'C');
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height, "무     선", 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.20, $pdf->row_height, $myF->phoneStyle($laData['c_mobile'],'.'), 1, 1, 'L');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.15, 'y'=>$pdf->GetY() + $pdf->row_height * 0.2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.40, 'height'=>5, 'align'=>'L', 'text'=>SubStr($laData['c_postno'],0,3).'-'.SubStr($laData['c_postno'],3,3)."\n".$laData['c_addr']."\n".$laData['c_addr_dtl']);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height * 3, "주     소", 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.40, $pdf->row_height * 3, "", 1, 0, 'C');
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height * 3, "보 호 자", 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height, "성     명", 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.20, $pdf->row_height, $laData['p_nm'], 1, 1, 'L');

	$pdf->SetX($pdf->left + $pdf->width * 0.70);
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height, "관     계", 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.20, $pdf->row_height, $laData['p_reg'], 1, 1, 'L');

	$pdf->SetX($pdf->left + $pdf->width * 0.70);
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height, "연 락 처", 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.20, $pdf->row_height, $myF->phoneStyle($laData['p_phone'],'.'), 1, 1, 'L');

	UnSet($laData);

	$pdf->SetLineWidth(0.6);
	$pdf->Rect($pdf->left, $pdf->GetY() - $pdf->row_height * 6, $pdf->width, $pdf->row_height * 6);
	$pdf->SetLineWidth(0.2);

	$sql = 'SELECT	org_no
			,		reg_dt
			,		reg_tm
			,		reg_nm
			,		yoy_nm
			,		stat
			,		take
			FROM	counsel_client_state
			WHERE	org_no = \''.$code.'\'
			AND		jumin  = \''.$jumin.'\'';

	$query[0] = $sql.' AND reg_dt > \''.$regDt.'\'
					 ORDER BY reg_dt
					 LIMIT 1';

	$query[1] = $sql.' AND reg_dt = \''.$regDt.'\'';

	$query[2] = $sql.' AND reg_dt < \''.$regDt.'\'
					 ORDER BY reg_dt DESC
					 LIMIT 1';

	$i = 0;
	$idx = 0;

	while(true){
		if ($i > 2){
			break;
		}

		$laData = $conn->get_array($query[$i]);

		if (Is_Array($laData)){
			$laData['reg_dt'] = $myF->dateStyle($laData['reg_dt'],'.').' '.$myF->timeStyle($laData['reg_tm']);
			lfDraw($pdf, $laData, $pos);

			$idx ++;
		}

		$i ++;

		UnSet($laData);
	}

	for($i=$idx; $i<3; $i++){
		lfDraw($pdf, null, $pos);
	}


	setArrayText($pdf, $pos);

	//include_once('../inc/_db_close.php');

?>