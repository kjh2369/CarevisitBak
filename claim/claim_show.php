<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_function.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');



	/**************************************************

		파라메타

	**************************************************/
	$type  = $_POST['type'];
	$mode  = $_POST['mode'];
	$code  = $_POST['code'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$month = (intval($month) < 10 ? '0' : '').intval($month);
	$dir   = $_POST['dir'];

	if ($dir == 1){
		$paper_dir = 'p';
	}else{
		$paper_dir = 'l';
	}




	/**************************************************

		PDF 헤더

	**************************************************/
	@require_once('./claim_show_header.php');






	/**************************************************

		기본설정

	**************************************************/
	#케릭터변경
	$conn->set_name('euckr');





	/**************************************************

		PDF OPEN

	**************************************************/
	$pdf = new MYPDF($paper_dir);
	$pdf->AddUHCFont($pdf->font_name_kor, $pdf->font_name_eng);
	$pdf->Open();
	$pdf->SetFillColor(220,220,220);




	/**************************************************

		기본설정

	**************************************************/
	$pdf->mode = $mode;

	$pdf->cp_nm = '';
	$pdf->cp_ci = '../ci/ci_dwcare.jpg';

	$pdf->ct_nm = $conn->center_name($code);
	$pdf->ct_ci = $conn->center_icon($code);

	switch($mode){
		case 100:
			$pdf->title = intval($year).'년 '.intval($month).'월 수급대상자현황표';
			break;
	}

	switch($paper_dir){
		case 'l':
			$pdf->width  = 297 - $pdf->left * 2;
			$pdf->height = 210 - $pdf->top * 2;
			break;
		default:
			$pdf->width  = 210 - $pdf->left * 2;
			$pdf->height = 297 - $pdf->top * 2;
	}

	$pdf->year    = $year;
	$pdf->month   = $month;
	$pdf->lastday = $myF->lastDay($year, $month);
	$pdf->domain  = $gDomain;



	/**************************************************

		PDF START

	**************************************************/

	$pdf->MY_ADDPAGE();
	require_once('./claim_show_'.$mode.'.php');


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
				}
			}
		}
	}

	function get_pos_y($pdf, $row_h, $text){
		$h = $pdf->GetY() + ( $row_h - $pdf->GetStringWidth($text) ) / 2;

		return  $h;
	}
?>