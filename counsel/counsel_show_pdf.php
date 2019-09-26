<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_function.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/**************************************************

		파라메타

	**************************************************/
	$code  = $_POST['code'];
	$jumins[0] = $ed->de($_POST['jumin']);
	$types[0] = $_POST['type'];
	$root  = $_POST['root'];
	$yymm  = $_POST['yymm'];
	$seq = $_POST['seq'];
	$jumin = $_POST['jumin'];

	$row_cnt = $_POST['row_cnt'];

	//전체출력인 경우
	if($types[0] == ''){
		for($i=0; $i<$row_cnt; $i++){
			/*************************************
			전체출력에 파라메타 값
			**************************************/
			$types[$i] = $_POST['type_'.$i];
			$yymms[$i] = $_POST['yymm_'.$i];
			$seqs[$i] = $_POST['seq_'.$i];
			$jumins[$i] = $ed->de($_POST['jumin_'.$i]);
			$dts[$i] = $_POST['regDt_'.$i];
		}
	}
	
	
	$k = 0;	//배열 인덱스

	if($_POST['seq'] == ''){
		foreach($types as $type_i => $type_cd){
			$type = $type_cd;

			/**************************************************

				PDF 헤더

			**************************************************/
			@require_once('./counsel_show_header.php');


			/**************************************************

				기본설정

			**************************************************/
			#케릭터변경
			$conn->set_name('euckr');



			/**************************************************

				PDF OPEN

			**************************************************/

				$pdf = new MYPDF(strtoupper('P'));	
				$pdf->root = $root;
				$pdf->cpIcon   = '../ci/ci_'.$gDomainNM.'.jpg';
				$pdf->cpName   = null;
				$pdf->ctIcon   = $conn->center_icon($code);
				$pdf->ctName   = $conn->center_name($code);
				$pdf->AddUHCFont($pdf->font_name_kor, $pdf->font_name_eng);
				$pdf->Open();
				$pdf->SetFillColor(220,220,220);

		}
	}
	
	
	foreach($types as $type_i => $type_cd){
		$type = $type_cd;

		//$pdf->type = $type;

		if($_POST['seq'] == ''){
			/**************************************************

				전체출력

			**************************************************/
			$yymm   = $yymms[$k];
			$seq    = $seqs[$k];
			$jumin  = $jumins[$k];
			$dt		= $dts[$k];


		}else{
			/**************************************************

				개별출력

			**************************************************/



			/**************************************************

				PDF 헤더

			**************************************************/

			@include_once('./counsel_show_header.php');

			//결제란 설정
			$sql = 'SELECT	line_cnt, subject
					FROM	signline_set
					WHERE	org_no = \''.$_SESSION['userCenterCode'].'\'';

			$row = $conn->get_array($sql);

			$sginCnt = $row['line_cnt'];
			$sginTxt = Explode('|',$myF->euckr($row['subject']));

			/**************************************************

				기본설정

			**************************************************/
			#케릭터변경
			$conn->set_name('euckr');


			/**************************************************

				PDF OPEN

			**************************************************/
			$pdf = new MYPDF();
			$pdf->debug     = $debug;
			$pdf->sginCnt	= $sginCnt;
			$pdf->sginTxt	= $sginTxt;
			$pdf->type = $type;
			$pdf->root = $root;
			$pdf->cpIcon   = '../ci/ci_'.$gDomainNM.'.jpg';
			$pdf->cpName   = null;
			$pdf->ctIcon   = $conn->center_icon($code);
			$pdf->ctName   = $conn->center_name($code);
			$pdf->AddUHCFont($pdf->font_name_kor, $pdf->font_name_eng);
			$pdf->Open();
			$pdf->SetFillColor(220,220,220);
		}

		/**************************************************

			PDF START

		**************************************************/
		if ($type == 'HUMAN' or $type == 'HUMAN2' or $type == 'AGREE'){
			include('./counsel_show_'.strtolower($type).'.php');
		}else if($type == 'STAT'){
			$pdf->MY_ADDPAGE();
			include('../sugupja/stat_pdf.php');
			unset($pos);
		}else if($type == 'PROCESS'){
			include('../report/r_show_MEMTR3.php');
		}else if($type == '200' or $type == '500' or $type == '800' or $type == '900' or $type == '200_test' or $type == '500_test' or $type == '800_test'){ //이용계약서
			include('../sugupja/client_contract_show_'.$type.'.php');
		}else{
			include('./counsel_show_info.php');
			include('./counsel_show_'.strtolower($type).'.php');
		}

		$k ++ ;

	}

	/**************************************************

		PDF END

	**************************************************/




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

	function setArrayText($pdf, $pos){
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

					if (is_array($p['text_color'])){
						$pdf->SetTextColor($p['text_color']['r'], $p['text_color']['g'], $p['text_color']['b']);
					}
				}

				if ($p['type'] == 'multi_text'){
					$pdf->SetXY($p['x'], $p['y']);
					$pdf->MultiCell($p['width'], $p['height'], $p['text'], $p['border'], $p['align']);
				}else if ($p['type'] == 'text'){
					$pdf->Text($p['x'], $p['y'], $p['text']);
				}
			}
		}
	}

	function lfDraw($pdf, $data, &$pos){
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.15, $pdf->row_height, "작성일자", 1, 0, 'C', 1);
		$pdf->Cell($pdf->width * 0.15, $pdf->row_height, $data['reg_dt'], 1, 0, 'C');
		$pdf->Cell($pdf->width * 0.15, $pdf->row_height, "기 록 자", 1, 0, 'C', 1);
		$pdf->Cell($pdf->width * 0.15, $pdf->row_height, $data['reg_nm'], 1, 0, 'L');
		$pdf->Cell($pdf->width * 0.15, $pdf->row_height, "담당요양보호사", 1, 0, 'C', 1);
		$pdf->Cell($pdf->width * 0.25, $pdf->row_height, $data['yoy_nm'], 1, 1, 'L');

		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.15, 'y'=>$pdf->GetY() + $pdf->row_height * 0.2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.85, 'height'=>5, 'align'=>'L', 'text'=>$data['stat']);

		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.15, $pdf->row_height * 5, "상태변화", 1, 0, 'C', 1);
		$pdf->Cell($pdf->width * 0.85, $pdf->row_height * 5, "", 1, 1, 'C');

		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.15, 'y'=>$pdf->GetY() + $pdf->row_height * 0.2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.85, 'height'=>5, 'align'=>'L', 'text'=>$data['take']);

		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.15, $pdf->row_height * 5, "조치사항", 1, 0, 'C', 1);
		$pdf->Cell($pdf->width * 0.85, $pdf->row_height * 5, "", 1, 1, 'C');

		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $pdf->GetY() - $pdf->row_height * 11, $pdf->width, $pdf->row_height * 11);
		$pdf->SetLineWidth(0.2);
	}

	function get_pos_y($pdf, $row_h, $text){
		$h = $pdf->GetY() + ( $row_h - $pdf->GetStringWidth($text) ) / 2;

		return  $h;
	}

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

		$row_high = $row_cnt*4.7;

		if($row_h > $row_high){
			$high = $row_h;
		}else {
			$high = $row_high;
		}

		return $high;
	}


	function _splitTexts($text, $width, $height = 0){
		if ($height > 0){
			$arrTxt = explode("\n", $text);
			$height = $height - ($height % floor($this->_rowH()));
		}else{
			$arrTxt[0] = $text;
		}

		$idx = 0;
		$h = 0;
		$isEnd = false;

		foreach($arrTxt as $arrI => $txt){
			$txt = iconv("EUC-KR","UTF-8",$txt);
			$len = mb_strlen($txt,"UTF-8");

			for($i=0; $i<$len; $i++){
				$str = mb_substr($txt, $i, 1, "UTF-8");
				$str = iconv("UTF-8", "EUC-KR", $str);

				if ($height > 0){
					if ($h > $height && $height > 0){
						$tmpTxt = iconv("EUC-KR","UTF-8",$arr[$idx-1]);
						$tmpLen = mb_strlen($tmpTxt,"UTF-8");
						$arr[$idx-1] = '';

						for($j=0; $j<$tmpLen; $j++){
							$tmpStr = mb_substr($tmpTxt, $j, 1, "UTF-8");
							$tmpStr = iconv("UTF-8", "EUC-KR", $tmpStr);

							if ($this->GetStringWidth($arr[$idx-1].$str.' ... ') > $width){
								$arr[$idx-1] .= '...';
								break;
							}else{
								$arr[$idx-1] .= $tmpStr;
							}
						}

						$isEnd = true;
						break;
					}else{
						if ($this->GetStringWidth($arr[$idx].$str) > $width){
							$h += floor($this->_rowH());
							$idx ++;
						}
						if (!$isEnd) $arr[$idx] .= $str;
					}
				}else{
					if ($this->GetStringWidth($arr[$idx].$str.' ... ') > $width){
						$arr[$idx] .= '...';
						break;
					}else{
						$arr[$idx] .= $str;
					}
				}
			}

			if ($isEnd) break;

			$h += floor($this->_rowH());
			$idx ++;
		}

		$txt = '';

		unset($arr[$idx]);

		foreach($arr as $i => $str){
			#echo $str.'<br>';
			#echo '<br>---------------------------------------------------------------------------------------<br>';
			$txt .= $str."\n";
		}

		return $txt;
	}
?>