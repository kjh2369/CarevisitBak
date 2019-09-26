<?
	include_once('../../inc/_db_open.php');
	#include_once('../../inc/_http_uri.php');
	include_once('../../inc/_function.php');
	include_once('../../inc/_myFun.php');
	include_once('../../inc/_ed.php');


	/*********************************************************

		�Ķ��Ÿ

	*********************************************************/
	parse_str($_POST['para'], $var);




	/**************************************************

		PDF ���

	**************************************************/
	if (strtoupper($var['dir']) == 'L'){
		$paperDir = 'l';
	}else{
		$paperDir = 'p';
	}



	require_once('./show_header.php');





	/**************************************************

		�⺻����

	**************************************************/
	$conn->set_name('euckr'); //�ɸ��ͺ���



	/*********************************************************

		�ֹι�ȣ

	*********************************************************/
	if (!is_numeric($var['jumin'])){
		$var['jumin'] = $ed->de($var['jumin']);
	}


	/**************************************************

		PDF OPEN

	**************************************************/
	$pdf = new MYPDF(strtoupper($paperDir));
	$pdf->font_name_kor = '-�����230';
	$pdf->font_name_eng = '-�����230';
	$pdf->AddUHCFont($pdf->font_name_kor, $pdf->font_name_eng);
	$pdf->Open();
	$pdf->SetFillColor(220,220,220);


	/*********************************************************

		�⺻��Ʈ����

	*********************************************************/
	$fontType1 = array('name'=>$pdf->font_name_kor,'bold'=>'','size'=>10);
	$fontType2 = array('name'=>$pdf->font_name_kor,'bold'=>'B','size'=>11);
	$fontType3 = array('name'=>$pdf->font_name_kor,'bold'=>'B','size'=>12);



	/**************************************************

		PDF START

		**********************************************/

		$pdf->MY_ADDPAGE();

		if ($pdf->showForm == 'IssueList' ||
			$pdf->showForm == 'Iljung'){
		}else{
			$pdf->SetAutoPageBreak(false);
		}

		$pdf->AliasNbPages();
		$pdf->SetFont($pdf->font_name_kor, '', $pdf->font_szie);

		if ($var['root'] == 'showPDF'){
			$filePath = './';
		}else{
			$filePath = '../'.$var['root'].'/'.$var['fileName'].'_'.$var['fileType'].'.php';
		}

		include_once($filePath);

		/*********************************************

		PDF END

	**************************************************/



	/**************************************************

		PDF CLOSE

	**************************************************/
	$pdf->Output();

	include_once('../../inc/_db_close.php');

	function setArrayText($pdf, $pos){
		/**************************************************

			��Ÿ �ؽ�Ʈ ��� �κ�

			x         : X��ǥ
			y         : Y��ǥ
			type      : �������
			width     :
			height    :
			font_size :
			align     :
			border	  :
			text      : ����ؽ�Ʈ

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

	function getPosY($pdf, $row_h, $text){
		$h = $pdf->GetY() + ( $row_h - $pdf->GetStringWidth($text) ) / 2;

		return  $h;
	}
?>