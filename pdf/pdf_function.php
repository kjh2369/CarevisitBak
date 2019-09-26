<?php
include_once('../pdf/korean.php');

class PDF_FUNCTION extends PDF_Korean{
	var $font_name_kor = '바탕';
	var $font_name_eng = 'Batang';
	var $font_size     = 10;
	var $font_size_bck = 10;

	function _setDefaultFont(){
		if ($_SESSION['userCenterCode'] == 'KN17C005'){
			$pdf->AddUHCFont('돋움', 'Dotum');
			$pdf->AddUHCFont('바탕', 'Batang');
			$pdf->AddUHCFont('궁서', 'Gungsuh');
			$pdf->AddUHCFont('굴림', 'Gulim');
		}else{
			$this->AddUHCFont($this->font_name_kor, $this->font_name_eng);
		}
	}


	/*********************************************************

		폰트만들기

	*********************************************************/
	function _makeNewFont($name = '', $size = 10, $bold = ''){
		$font['name'] = (!empty($name) ? $name : $this->font_name_kor);
		$font['size'] = $size;
		$font['bold'] = $bold;

		return $font;
	}


	/*********************************************************

		사이즈

	*********************************************************/
	function _makeSize(){
		$size['w'] = 0;
		$size['h'] = 0;
		$size['x'] = 0;
		$size['y'] = 0;

		return $size;
	}


	function _showTop2($p_title, $p_fontSize = 30, $p_border = 1){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',$p_fontSize);
		$this->Cell($this->width, $this->GetStringWidth('▒')+4, $p_title, $p_border, 1, 'C');

		$Y = $this->GetY();

		return $Y;
	}

	function _showTop1($p_title, $p_fontSize = 30){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',$p_fontSize);
		$this->Cell(114,18,	$p_title,	1,	0,	'C');
		$this->Cell(8,	18,	'',	1,	0,	'C');
		$this->Cell(20,	6,	'',	1,	0,	'C');
		$this->Cell(20,	6,	'',	1,	0,	'C');
		$this->Cell(20,	6,	'',	1,	1,	'C');

		$this->SetX($this->left);
		$this->Cell(114,0,	'',	0,	0,	'C');
		$this->Cell(8,	0,	'',	0,	0,	'C');
		$this->Cell(20,	12,	'',	1,	0,	'C');
		$this->Cell(20,	12,	'',	1,	0,	'C');
		$this->Cell(20,	12,	'',	1,	1,	'C');

		$Y = $this->GetY();

		$this->SetXY($this->left+115, $this->top+3);
		$this->SetFont($this->font_name_kor,'B',11);
		$this->MultiCell(8,	6,	"결\n재");

		return $Y;
	}

	function _showTop($p_title, $p_fontSize = 30, $p_border = true, $p_printDate = false){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',$p_fontSize);
		$this->Cell($this->width, $p_fontSize / 2, $p_title, $p_border ? 1 : 0, !$p_printDate ? 1 : 0, 'C');

		/*********************************
			월별급여대장
			출력일자변수 : $this->printDt
		**********************************/
		$printDt = $this->printDt != '' ? $this->printDt : date('Y.m.d', mktime());

		if ($p_printDate){
			$this->SetXY($this->left, $this->top + $p_fontSize / 2 - 6);
			$this->set_text_font();
			$this->Cell($this->width, 6, "출력일 : ".$printDt, 0, 1, 'R');
		}

		$Y = $this->GetY();

		if ($this->salaryDt){
			$this->SetXY($this->left, $this->top + $p_fontSize / 2 - 2);
			$this->set_text_font();
			$this->Cell($this->width, 6, "지급일 : ".$this->salaryDt, 0, 1, 'R');
		}

		$this->SetY($Y);

		return $Y;
	}

	function _drawBorder($p_left = 0, $p_top = 0, $p_width = 0, $p_height = 0){
		if ($p_top		== 0) $p_top	= $this->top;
		if ($p_left		== 0) $p_left	= $this->left;
		if ($p_width	== 0) $p_width	= $this->width;
		if ($p_height	== 0) $p_height = $this->height-$this->top;

		$this->SetLineWidth(0.6);
		$this->Rect($p_left, $p_top, $p_width, $p_height);
		$this->SetLineWidth(0.2);
	}

	/*
	 *
	 */
	function _splitTextWidth($text, $width, $height = 0){
		$txt = '';
		$len = mb_strlen($text,"UTF-8");
		$idx = 0;
		$h   = 0;

		for($j=0; $j<$len; $j++){
			$str = mb_substr($text, $j, 1, "UTF-8");
			$str = iconv("UTF-8", "EUC-KR", $str);

			if ($height > 0){
				$tmp = mb_substr($text, $j, 2, "UTF-8");

				if ($tmp == "\n"){
					$idx ++;
				}else{
					if ($h + $this->_rowH() > $height){
						$arr[$idx] .= '...';
						break;
					}else{
						if ($this->GetStringWidth($arr[$idx].$str) > $width){
							$idx ++;
						}
						$arr[$idx] .= $str;
					}
				}
			}else{
				if ($this->GetStringWidth($txt.$str.' ... ') > $width){
					$txt .= '...';
					break;
				}else{
					$txt .= $str;
				}
			}
		}

		if (is_array($arr)){
			foreach($arr as $i => $str){
				if (!empty($txt)) $txt .= "\n";

				$txt .= $str;
			}
		}

		return $txt;
	}

	function _splitText($text, $width, $height = 0){
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

	function set_default_xy($coord_x = 0, $coord_y = 0){
		$this->x = $this->left + $coord_x;
		$this->y = $this->GetY() + $coord_y;

		$this->SetXY($this->x, $this->y);
	}

	function set_caption_font($p_bold = ''){
		$this->SetFont($this->font_name_kor,$p_bold,10);
	}

	function set_text_font($p_bold = ''){
		$this->SetFont($this->font_name_kor,$p_bold,9);
	}

	function set_font($p_size = 9, $p_bold = ''){
		$this->SetFont($this->font_name_kor,$p_bold,$p_size);
	}



	/*********************************************************

		자고

	*********************************************************/
	function _getStringHeight($txt){
		$txt = explode("\n", $txt);
		$h = 0;

		foreach($txt as $i => $t){
			$h += $this->GetStringWidth(substr($t,0,1));
		}

		return $h;
	}


	function _getStringCnt($txt){
		$txt = explode("\n", $txt);
		$h = sizeof($txt);

		return $h;
	}


	function _getCellHeight($h, $txt, $rate = 1){
		$x = $h / $this->_getStringCnt($txt) * $rate;

		return $x;
	}



	/*********************************************************

		출력

	*********************************************************/
	function _Cell($w, $h, $txt, $font, $border = 0, $ln = 0, $align = 'L', $fill = 0){
		$this->SetFont($font['name'], $font['bold'], $font['size']);
		$this->Cell($w, $h, $txt, $border, $ln, $align, $fill);
	}



	/**************************************************



	**************************************************/
	function _drawCi(){
		$this->SetFont($this->font_name_kor, '', 12);
		$this->SetXY($this->left, -7);

		$pos_y = $this->GetY();


		if (!empty($this->ct_ci)){
			$centerIcon = $this->ct_ci;
		}else if (!empty($this->ctIcon)){
			$centerIcon = $this->ctIcon;
		}


		if (!empty($this->ct_nm)){
			$centerName = $this->ct_nm;
		}else if (!empty($this->ctName)){
			$centerName = $this->ctName;
		}


		if (!empty($this->cp_ci)){
			$companyIcon = $this->cp_ci;
		}else if (!empty($this->cpIcon)){
			$companyIcon = $this->cpIcon;
		}


		if (!empty($this->cp_nm)){
			$companyName = $this->cp_nm;
		}else if (!empty($this->cpName)){
			$companyName = $this->cpName;
		}




		if (!empty($centerIcon)){
			$ci_img = getimagesize('../mem_picture/'.$centerIcon);
			$this->Image('../mem_picture/'.$centerIcon, $this->left, $this->GetY() - (10 - $ci_img[1] / 3.8) / 2);
			$this->SetXY($this->left + $ci_img[0] / 3.8 + 2, $pos_y - 5);
		}

		$this->Cell($this->width, 10, $this->centerName, 0, 0, 'L');

		if (!empty($companyIcon)){
			$ci_img = getimagesize($companyIcon);

			$host = explode('.', $_SERVER['HTTP_HOST']);
			#청구관리(대장사현황표(도우누리로고출력여부))
			#바우처홈에서만 이미지출력
			if($host[0] == 'dwcare' || $host[1] == 'dwcare'){
				$this->Image($companyIcon, $this->width - $this->GetStringWidth($companyName) - $ci_img[0] / 3.8 + 3, $pos_y - $ci_img[1] / 3.8);
			}
		}

		$this->SetX($this->left);
		$this->Cell($this->width, 10, $companyName, 0, 0, 'R');
	}


	function _drawIcon(){

		$this->SetTextColor(0,0,0);
		$this->SetFont($this->font_name_kor, '', 8);
		$this->SetXY($this->left, -20);

		$pos_y = $this->GetY();


		if (!empty($this->ct_ci)){
			$centerIcon = $this->ct_ci;
		}else if (!empty($this->ctIcon)){
			$centerIcon = $this->ctIcon;
		}

		/*
		if (!empty($this->ct_nm)){
			$centerName = $this->ct_nm;
		}else if (!empty($this->ctName)){
			$centerName = $this->ctName;
		}

		if (!empty($this->cp_ci)){
			$companyIcon = $this->cp_ci;
		}else if (!empty($this->cpIcon)){
			$companyIcon = $this->cpIcon;
		}


		if (!empty($this->cp_nm)){
			$companyName = $this->cp_nm;
		}else if (!empty($this->cpName)){
			$companyName = $this->cpName;
		}
		*/

		$exp = explode('.',$centerIcon);
		$exp = strtolower($exp[sizeof($exp)-1]);

		if($this->direction == 'P'){ //세로
			$left = 180;
		}else if($this->direction == 'L'){ //가로
			$left = 270;
		}else {
			$left = 180;
		}

		if ($exp != 'bmp'){
			if (is_file('../mem_picture/'.$centerIcon)){
				$top = $this->GetY(); // - (10 - $ci_img[1] / 3.8) / 2;
				$ci_img = getImageSize('../mem_picture/'.$centerIcon);
				$this->Image('../mem_picture/'.$centerIcon, $left, $top, 20);

				$liM = Round(1.3 / 2.54 * 96);
				$liLeft = 20;

				if ($ci_img[1] > $liM){
					$liTop = 20;
				}else{
					$liTop = Round($ci_img[1] * 2.54 / 96 * 10);
				}

				if ($top + $liTop > 290){
					$liTop = $top - $liTop;
				}else{
					$liTop = $top + $liTop;
				}

				$this->SetXY($this->left + $liLeft, $liTop);

				//$this->SetXY($this->left + $ci_img[0] / 3.8 + 2, $top + $ci_img[1] / 3.8 - $this->GetStringWidth('▦') * 1.5);
				//$this->SetXY(15,-15);
				$height = $ci_img[1] / 3.8 + 1.5;
			}else{
				$height = 10;
			}
		}else{
			$height = 10;
		}

		/*
		$this->Cell($this->width, $height, $centerName, 0, 0, 'L');

		$exp = explode('.',$companyIcon);
		$exp = strtolower($exp[sizeof($exp)-1]);

		if ($exp != 'bmp'){
			if (is_file($companyIcon)){
				$ci_img = getImageSize($companyIcon);
				if($this->type == 'DEGREE'){
					//급여공제동의서
				}else{
					$this->Image($companyIcon, $this->width - $this->GetStringWidth($companyName) - $ci_img[0] / 3.8 + 3 - (14-$this->left), $pos_y);
				}
				$height = $ci_img[1] / 3.8 + 1.5;
			}else{
				$height = 10;
			}
		}else{
			$height = 10;
		}

		$this->SetX($this->left);
		$this->Cell($this->width, $height, $companyName, 0, 0, 'R');
		*/

	}


	function _drawJikin(){

		$this->SetTextColor(0,0,0);
		$this->SetFont($this->font_name_kor, '', 8);

		$pos_y = $this->GetY()+10;

		if (!empty($this->jikin)){
			$jikin = $this->jikin;
		}

		$exp = explode('.',$jikin);
		$exp = strtolower($exp[sizeof($exp)-1]);

		if($this->direction == 'P'){ //세로
			$left = 40;
		}else if($this->direction == 'L'){ //가로
			$left = 40;
		}else {
			$left = 40;
		}

		if ($exp != 'bmp'){
			$top = $this->GetY()+15; // - (10 - $ci_img[1] / 3.8) / 2;

			if (is_file('../iljung/img/original.jpg')){
				$this->Image('../iljung/img/original.jpg', $left, $top, 50);
			}

			if (is_file('../mem_picture/'.$jikin)){

				//$ci_img = getImageSize('../mem_picture/'.$jikin);
				$this->Image('../mem_picture/'.$jikin, $left+38, $top+1, 11);
				/*
				$liM = Round(1.3 / 2.54 * 96);
				$liLeft = 20;
				$liTop = 20;

				$this->SetXY($this->left + $liLeft, $liTop);
				*/
			}

		}
	}


	function _fontSize(){
		return $this->FontSizePt;
	}
	function _rowH(){
		return $this->row_height * ($this->FontSizePt / $this->font_size);
	}
	function _colW($rate = 100){
		$rate /= 100;
		return $this->width * $rate;
	}


	//문장높이
	function _getStrY($pdf,$width,$str){
		$X = 1000;
		$Y = $pdf->GetY();

		$pdf->SetXY($X, $Y);
		$pdf->MultiCell($width, 4, $str);

		$H = $pdf->GetY() - $Y;

		$pdf->SetFillColor(255,255,255);
		$pdf->SetXY($X, $Y);
		$pdf->Cell($width, $H, "", 0, 0, "C", 1);
		$pdf->SetFillColor(213,213,213);

		return $H;
	}

	function _SignlineSet(){
		if ($this->sginCnt < 1) return;

		if ($this->sginCnt <= 3){
			$sginBoxW = 21;
		}else if ($this->sginCnt == 4){
			$sginBoxW = 17;
		}else{
			$sginBoxW = 15;
		}

		$this->SetFont($this->font_name_kor, '', 11);
		$sginBoxL = $this->width - $sginBoxW + $this->left;

		for($i=0; $i<$this->sginCnt; $i++){
			$txt = $this->sginTxt[$this->sginCnt-$i-1];
			$this->Rect($sginBoxL, $this->top, $sginBoxW, $this->row_height * 4);
			$this->Text($sginBoxL+($sginBoxW-$this->GetStringWidth($txt))/2, $this->top+5, $txt);
			$sginBoxL -= $sginBoxW;
		}

		$this->Rect($sginBoxL+$sginBoxW*0.5, $this->top, $sginBoxW*0.5, $this->row_height * 4);
		$this->Line($sginBoxL+$sginBoxW, $this->top+$this->row_height*1.3, $sginBoxL+$sginBoxW*($this->sginCnt+1), $this->top+$this->row_height*1.3);
		$this->Text($sginBoxL+$sginBoxW*0.5+($sginBoxW*0.5-$this->GetStringWidth("결"))/2, $this->top+9, "결");
		$this->Text($sginBoxL+$sginBoxW*0.5+($sginBoxW*0.5-$this->GetStringWidth("재"))/2, $this->top+18, "재");
	}


	function _SignHcelineSet($subject,$disH){
		$top = $this->top-$disH;
		
		if ($this->sginCnt < 1){
			$this->Cell($this->width, $this->row_height * 2, $subject, 0, 1, 'C');
		}else if ($this->sginCnt == 1){
			$this->Cell($this->width * 0.8, $this->row_height * 3.7, $subject, 0, 1, 'C');
		}else if ($this->sginCnt == 2){
			$this->Cell($this->width * 0.7, $this->row_height * 3.7, $subject, 0, 1, 'C');
		}else if ($this->sginCnt == 3){
			$this->Cell($this->width * 0.6, $this->row_height * 3.7, $subject, 0, 1, 'C');
		}else if ($this->sginCnt == 4){
			$this->Cell($this->width * 0.5, $this->row_height * 3.7, $subject, 0, 1, 'C');
		}else if ($this->sginCnt == 5){
			$this->Cell($this->width * 0.4, $this->row_height * 3.7, $subject, 0, 1, 'C');
		}

		if ($this->sginCnt < 1) return;
		
		if ($this->sginCnt <= 3){
			$sginBoxW = 19;
		}else if ($this->sginCnt == 4){
			$sginBoxW = 19;
		}else{
			$sginBoxW = 19;
		}
		
		$this->SetFont($this->font_name_kor, 'B', 9);
		$sginBoxL = $this->width - $sginBoxW + $this->left;

		for($i=0; $i<$this->sginCnt; $i++){
			$txt = $this->sginTxt[$this->sginCnt-$i-1];
			$this->Rect($sginBoxL, $top, $sginBoxW, $this->row_height * 3.7);
			$this->Text($sginBoxL+($sginBoxW-$this->GetStringWidth($txt))/2, $top+4.5, $txt);
			$sginBoxL -= $sginBoxW;
		}
		
		$this->Rect($sginBoxL+$sginBoxW*0.5, $top, $sginBoxW*0.5, $this->row_height * 3.7);
		$this->Line($sginBoxL+$sginBoxW, $top+$this->row_height*1.1, $sginBoxL+$sginBoxW*($this->sginCnt+1), $top+$this->row_height*1.1);
		$this->Text($sginBoxL+$sginBoxW*0.5+($sginBoxW*0.5-$this->GetStringWidth("결"))/2, $top+10, "결");
		$this->Text($sginBoxL+$sginBoxW*0.5+($sginBoxW*0.5-$this->GetStringWidth("재"))/2, $top+16, "재");
	}

	function _SinglineWidth(){
		if ($this->sginCnt > 0){
			if ($this->sginCnt <= 3){
				$sginBoxW = 21;
			}else if ($this->sginCnt == 4){
				$sginBoxW = 17;
			}else{
				$sginBoxW = 15;
			}

			$width = $sginBoxW * $this->sginCnt + $sginBoxW * 0.5;
		}else{
			$width = 0;
		}

		return $width;
	}

	function TestSize($width, $txt){
		$fontsize = $this->font_size;
		$stndW = $width;
		$loopsize = $fontsize;

		while(true){
			$textW = $this->GetStringWidth($txt);

			if ($stndW >= $textW) break;

			$loopsize = $loopsize - 0.5;
			$this->SetFontSize($loopsize);
		}

		$newsize = $loopsize;
		$this->font_size = $fontsize;
		$this->SetFontSize($this->font_size);

		return $newsize;
	}

	#이미지 처리 함수

	const DPI = 96;
	const MM_IN_INCH = 25.4;

	function pixelToMM($val) {
		return $val * self::MM_IN_INCH / self::DPI;
	}

	function resizeToFit($imgFilename) {
		list($width, $height) = getimagesize($imgFilename);

		return array(
			round($this->pixelToMM($width)),
			round($this->pixelToMM($height))
		);
	}

	function baseImg($base64_string, $x, $y, $w=0, $h=0, $type='', $link='', $isMask=false, $maskImg=0) {
		$filename = $_SESSION['USER_IPIN'].'_'.uniqid().'.png';
		$file = '../temp/tmp_img/'.$filename;

		$data = explode(',', $base64_string);
		$data = base64_decode($data[1]);
		$success = file_put_contents($file, $data);

		if (is_file($file)){
			$this->ImageA($file, $x, $y, $w, $h, $type, $link, $isMask, $maskImg);
			unlink($file);
		}
	}

	/*******************************************************************************
	* 알파채널 이미지 처리                                                         *
	*                               Public methods                                 *
	*                                                                              *
	*******************************************************************************/
	function ImageA($file,$x,$y,$w=0,$h=0,$type='',$link='', $isMask=false, $maskImg=0){
		//Put an image on the page
		if(!isset($this->images[$file])){
			//First use of image, get info
			if($type==''){
				$pos=strrpos($file,'.');
				if(!$pos)
					$this->Error('Image file has no extension and no type was specified: '.$file);
				$type=substr($file,$pos+1);
			}
			$type=strtolower($type);
			$mqr=get_magic_quotes_runtime();
			set_magic_quotes_runtime(0);
			if($type=='jpg' || $type=='jpeg')
				$info=$this->_parsejpg($file);
			elseif($type=='png'){
				$info=$this->_parsepng($file);
				if ($info=='alpha') return $this->ImagePngWithAlpha($file,$x,$y,$w,$h,$link);
			}else{
				//Allow for additional formats
				$mtd='_parse'.$type;
				if(!method_exists($this,$mtd))
					$this->Error('Unsupported image type: '.$type);
				$info=$this->$mtd($file);
			}
			set_magic_quotes_runtime($mqr);

			if ($isMask){
				$info['cs']="DeviceGray"; // try to force grayscale (instead of indexed)
			}
			$info['i']=count($this->images)+1;
			if ($maskImg>0) $info['masked'] = $maskImg;###
			$this->images[$file]=$info;
		}else{
			$info=$this->images[$file];
		}

		//Automatic width and height calculation if needed
		if($w==0 && $h==0){
			//Put image at 72 dpi
			$w=$info['w']/$this->k;
			$h=$info['h']/$this->k;
		}
		if ($w==0) $w=$h*$info['w']/$info['h'];
		if ($h==0) $h=$w*$info['h']/$info['w'];

		// embed hidden, ouside the canvas
		if ((float)FPDF_VERSION>=1.7){
			if ($isMask) $x = ($this->CurOrientation=='P'?$this->CurPageSize[0]:$this->CurPageSize[1]) + 10;
		}else{
			if ($isMask) $x = ($this->CurOrientation=='P'?$this->CurPageFormat[0]:$this->CurPageFormat[1]) + 10;
		}

		$this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
		if($link) $this->Link($x,$y,$w,$h,$link);

		return $info['i'];
	}

	// needs GD 2.x extension
	// pixel-wise operation, not very fast
	function ImagePngWithAlpha($file,$x,$y,$w=0,$h=0,$link=''){
		$tmp_alpha = tempnam('.', 'mska');
		$this->tmpFiles[] = $tmp_alpha;
		#$tmp_plain = tempnam('.', 'mskp');
		#$this->tmpFiles[] = $tmp_plain;

		list($wpx, $hpx) = getimagesize($file);
		$img = imagecreatefrompng($file);
		$alpha_img = imagecreate( $wpx, $hpx );

		// generate gray scale pallete
		for($c=0;$c<256;$c++) ImageColorAllocate($alpha_img, 18, 18, 18);

		$black = imagecolorallocate($alpha_img, 0, 0, 0);
		imagecolortransparent($alpha_img, $black);

		// extract alpha channel
		$xpx=0;
		while ($xpx<$wpx){
			$ypx = 0;
			while ($ypx<$hpx){
				$color_index = imagecolorat($img, $xpx, $ypx);
				$alpha = 255-($color_index>>24)*255/127; // GD alpha component: 7 bit only, 0..127!
				imagesetpixel($alpha_img, $xpx, $ypx, $alpha);
			++$ypx;
			}
			++$xpx;
		}

		imagepng($alpha_img, $tmp_alpha);
		imagedestroy($alpha_img);

		// extract image without alpha channel
		#$plain_img = imagecreatetruecolor ( $wpx, $hpx );
		#imagecopy ($plain_img, $img, 0, 0, 0, 0, $wpx, $hpx );
		#imagepng($plain_img, $tmp_plain);
		#imagedestroy($plain_img);

		//first embed mask image (w, h, x, will be ignored)
		//$maskImg = $this->Image($tmp_alpha, 0,0,0,0, 'PNG', '', true);
		$maskImg = $this->Image($tmp_alpha, $x,$y,$w,$h, 'PNG', '', true);

		//embed image, masked with previously embedded mask
		#$this->Image($tmp_plain,$x,$y,$w,$h,'PNG',$link, false, $maskImg);
	}

	function Close(){
		parent::Close();

		// clean up tmp files
		if ($this->tmpFiles!=''){
			foreach($this->tmpFiles as $tmp){
				@unlink($tmp);
			}
		}
	}

	/*******************************************************************************
	*                                                                              *
	*                               Private methods                                *
	*                                                                              *
	*******************************************************************************/
	function _putimages(){
		$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
		reset($this->images);
		while(list($file,$info)=each($this->images)){
			$this->_newobj();
			$this->images[$file]['n']=$this->n;
			$this->_out('<</Type /XObject');
			$this->_out('/Subtype /Image');
			$this->_out('/Width '.$info['w']);
			$this->_out('/Height '.$info['h']);

			if (isset($info["masked"])) $this->_out('/SMask '.($this->n-1).' 0 R'); ###

			if($info['cs']=='Indexed')
				$this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
			else{
				$this->_out('/ColorSpace /'.$info['cs']);
				if ($info['cs']=='DeviceCMYK') $this->_out('/Decode [1 0 1 0 1 0 1 0]');
			}
			$this->_out('/BitsPerComponent '.$info['bpc']);
			if (isset($info['f'])) $this->_out('/Filter /'.$info['f']);
			if (isset($info['parms'])) $this->_out($info['parms']);
			if (isset($info['trns']) && is_array($info['trns'])){
				$trns='';
				for($i=0;$i<count($info['trns']);$i++)
					$trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
				$this->_out('/Mask ['.$trns.']');
			}
			$this->_out('/Length '.strlen($info['data']).'>>');
			$this->_putstream($info['data']);
			unset($this->images[$file]['data']);
			$this->_out('endobj');
			//Palette
			if($info['cs']=='Indexed'){
				$this->_newobj();
				$pal=($this->compress) ? gzcompress($info['pal']) : $info['pal'];
				$this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
				$this->_putstream($pal);
				$this->_out('endobj');
			}
		}
	}

	// this method overwriing the original version is only needed to make the Image method support PNGs with alpha channels.
	// if you only use the ImagePngWithAlpha method for such PNGs, you can remove it from this script.
	function _parsepng($file){
		//Extract info from a PNG file
		$f=fopen($file,'rb');
		if (!$f) $this->Error('Can\'t open image file: '.$file);
		//Check signature
		if (fread($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10)) $this->Error('Not a PNG file: '.$file);
		//Read header chunk
		fread($f,4);
		if (fread($f,4)!='IHDR') $this->Error('Incorrect PNG file: '.$file);
		$w=$this->_readint($f);
		$h=$this->_readint($f);
		$bpc=ord(fread($f,1));
		if($bpc>8) $this->Error('16-bit depth not supported: '.$file);
		$ct=ord(fread($f,1));
		if($ct==0) $colspace='DeviceGray';
		elseif($ct==2) $colspace='DeviceRGB';
		elseif($ct==3) $colspace='Indexed';
		else{
			fclose($f);      // the only changes are
			return 'alpha';  // made in those 2 lines
		}
		if(ord(fread($f,1))!=0) $this->Error('Unknown compression method: '.$file);
		if(ord(fread($f,1))!=0) $this->Error('Unknown filter method: '.$file);
		if(ord(fread($f,1))!=0) $this->Error('Interlacing not supported: '.$file);
		fread($f,4);
		$parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
		//Scan chunks looking for palette, transparency and image data
		$pal='';
		$trns='';
		$data='';
		do{
			$n=$this->_readint($f);
			$type=fread($f,4);
			if($type=='PLTE'){
				//Read palette
				$pal=fread($f,$n);
				fread($f,4);
			}elseif($type=='tRNS'){
				//Read transparency info
				$t=fread($f,$n);
				if($ct==0)
					$trns=array(ord(substr($t,1,1)));
				elseif($ct==2)
					$trns=array(ord(substr($t,1,1)),ord(substr($t,3,1)),ord(substr($t,5,1)));
				else
				{
					$pos=strpos($t,chr(0));
					if($pos!==false)
						$trns=array($pos);
				}
				fread($f,4);
			}elseif($type=='IDAT'){
				//Read image data block
				$data.=fread($f,$n);
				fread($f,4);
			}elseif($type=='IEND')
				break;
			else
				fread($f,$n+4);
		}

		while($n);
		if($colspace=='Indexed' && empty($pal)) $this->Error('Missing palette in '.$file);
		fclose($f);
		return array('w'=>$w,'h'=>$h,'cs'=>$colspace,'bpc'=>$bpc,'f'=>'FlateDecode','parms'=>$parms,'pal'=>$pal,'trns'=>$trns,'data'=>$data);
	}

}
?>