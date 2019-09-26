<?
	include_once('../inc/_db_open.php');

	/*********************************************************

		�Ķ��Ÿ

	*********************************************************/
	parse_str($_POST['para'], $var);

	include_once('../inc/_http_uri.php');
	include_once('../inc/_function.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$today = Date('Ymd');


	if (!$var['code']) $var['code'] = $_SESSION['userCenterCode'];


	/**************************************************

		PDF ���

	**************************************************/
	if (strtoupper($var['dir']) == 'L'){
		$paperDir = 'l';
	}else{
		$paperDir = 'p';
	}
	
	require_once('../showPDF/show_header.php');
	
	/**************************************************

		�⺻����

	**************************************************/
	$conn->set_name('euckr'); //�ɸ��ͺ���

	$sql = 'SELECT	m00_ctel
			FROM	m00center
			WHERE	m00_mcode = \''.$var['code'].'\'
			ORDER	BY m00_mkind
			LIMIT	1';
	
	$phone = $conn->get_data($sql);

	if (!Empty($para)){
		$para .= '&';
	}
	$para .= 'phone='.$myF->phoneStyle($phone,'.');
	
	
	//��� �ΰ�
	$sql = 'select m00_icon
			  from m00center
			 where m00_mcode = \''.$_SESSION['userCenterCode'].'\'';
	$icon = $conn -> get_data($sql);


	/**************************************************

		PDF OPEN

	**************************************************/
	$pdf = new MYPDF(strtoupper($paperDir));

	

	//������ ����
	$sql = 'SELECT	line_cnt, subject
			FROM	signline_set
			WHERE	org_no = \''.$_SESSION['userCenterCode'].'\'';

	$row = $conn->get_array($sql);

	$sginCnt = $row['line_cnt'];
	$sginTxt = Explode('|',$row['subject']);

	Unset($row);


	
	$pdf->Open();
	$pdf->SetFillColor(220,220,220);
	$pdf->AddUHCFont($pdf->font_name_kor, $pdf->font_name_eng);
					

	/*********************************************************

		�⺻��Ʈ����

	*********************************************************/
	$fontType1 = array('name'=>$pdf->font_name_kor,'bold'=>'','size'=>10);
	$fontType2 = array('name'=>$pdf->font_name_kor,'bold'=>'B','size'=>11);
	$fontType3 = array('name'=>$pdf->font_name_kor,'bold'=>'B','size'=>12);


	//��ȸ������ ������������
	if ($var['showForm'] == 'SW_WORK_LOG'  ||
		$var['showForm'] == 'SW_WORK_LOG2' ){
		
		$data = explode('?',$var['data']);
		
		if($var['jumin'] != 'sel'){
			$data[0] = 'cltCd='.$var['jumin'];
		}
		
		if (is_array($data)){		
			foreach($data as $tmpIdx => $R){
				parse_str($R,$R);
				
				$var['jumin'] = $ed->de($R['cltCd']);
				
				
				/*********************************************************

					������

				*********************************************************/
				if (!Empty($var['code']) && !Empty($var['jumin'])){
					$sql = 'select min(m03_mkind) as kind
						   ,      m03_jumin as jumin
						   ,      m03_name as name
							 from m03sugupja
							where m03_ccode = \''.$var['code'].'\'
							  and m03_jumin = \''.$var['jumin'].'\'
							group by m03_jumin';
					$row = $conn->get_array($sql);

					$lsName  = $row['name'];
					$lsJumin = $row['jumin'];

					unset($row);
				}

				if (!Empty($lsName) && !Empty($lsName)){
					$para = 'name='.$lsName
						  . '&jumin='.$myF->issStyle($lsJumin);
				}

				if($var['jumin']){ 
					//�����ڸ�
					$sql = 'SELECT	m03_juso1
							,		m03_juso2
							FROM	m03sugupja
							WHERE	m03_ccode = \''.$var['code'].'\'
							AND		m03_mkind = \'0\'
							AND		m03_jumin = \''.$var['jumin'].'\'';
					
					$row = $conn->get_array($sql);

					$addr = $row['m03_juso1'].' '.$row['m03_juso2'];

					Unset($row);

					//��� �� ��ȿ�Ⱓ
					$sql = 'SELECT	level
							,		app_no
							FROM	client_his_lvl
							WHERE	org_no = \''.$var['code'].'\'
							AND		svc_cd = \'0\'
							AND		jumin  = \''.$var['jumin'].'\'
							AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$var['yymm'].'\'
							AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$var['yymm'].'\'';
					
					$row = $conn->get_array($sql);
					$lvl = $row['level'];
					$appNo = $row['app_no'];

					if ($lvl){
						$lvl = $lvl.'���';
					}else{
						$lvl = '';
					}

					Unset($row);
					
					//����
					$sql = 'SELECT	date
							,		time
							,		to_time
							,		reg_jumin
							,		reg_name
							FROM	sw_log
							WHERE	org_no	= \''.$var['code'].'\'
							AND		jumin	= \''.$var['jumin'].'\'
							AND		yymm	= \''.$var['yymm'].'\'
							AND		seq		= \''.$var['seq'].'\'';

					$row = $conn->get_array($sql);

					$sql = 'SELECT	m02_key
							FROM	m02yoyangsa
							WHERE	m02_ccode	= \''.$var['code'].'\'
							AND		m02_yjumin	= \''.$row['reg_jumin'].'\'';

					$key = $conn->get_data($sql);

					$datetime = $myF->dateStyle($row['date'],'.').' '.$myF->timeStyle($row['time']);

					if ($row['to_time']){
						$datetime .= '~'.$myF->timeStyle($row['to_time']);
					}
					
					Unset($row);

					$para .= '&level='.$lvl;
					$para .= '&addr='.$myF->utf($addr);
					$para .= '&datetime='.$datetime;
					$para .= '&regName='.$row['reg_name'];
					$para .= '&regKey='.$key;
					$para .= '&appNo='.$appNo;

					$pdf->cpIcon	= '../ci/ci_'.$gDomainNM.'.jpg';
					$pdf->cpName	= null;
					$pdf->ctIcon	= $conn->center_icon($var['code']);
					$pdf->ctName	= $conn->center_name($var['code']);
					$pdf->showForm	= (!empty($var['showForm']) ? $var['showForm'] : null);
					$pdf->orderBY	= (!empty($var['byGbn']) ? $var['byGbn'] : null);
					$pdf->svcGbn	= $var['svcGbn'];
					$pdf->showGbn   = $var['showGbn'];
					$pdf->year		= $var['year'];
					$pdf->month		= $var['month'];
					$pdf->printDT	= $myF->dateStyle($var['printDT'],'.');
					$pdf->mode		= $var['mode'];
					$pdf->para		= $para;
					$pdf->debug		= $debug;
					$pdf->domain	= $gDomain;
					$pdf->icon      = $icon;
					$pdf->sginCnt	= $sginCnt;
					$pdf->sginTxt	= $sginTxt;
					
					$pdf->MY_ADDPAGE();
					$pdf->SetAutoPageBreak(false);

					$filePath = '../iljung/sw_work_log_pdf.php';
					include($filePath);

					$pdf->AliasNbPages();
					$pdf->SetFont($pdf->font_name_kor, '', $pdf->font_szie);	
					
				}
			}
		
		}
	
	}

	
	/**************************************************

		PDF CLOSE

	**************************************************/
	$pdf->Output();

	include_once('../inc/_db_close.php');
	
	function lfSetMark($q, $r){
		if ($q == $r){
			return '��';
		}else{
			return '��';
		}
	}

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

	function lfDraw($pdf, $data, &$pos){
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.15, $pdf->row_height, "�ۼ�����", 1, 0, 'C', 1);
		$pdf->Cell($pdf->width * 0.20, $pdf->row_height, $data['reg_dt'], 1, 0, 'C');
		$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "�� �� ��", 1, 0, 'C', 1);
		$pdf->Cell($pdf->width * 0.15, $pdf->row_height, $data['reg_nm'], 1, 0, 'L');
		$pdf->Cell($pdf->width * 0.15, $pdf->row_height, "����纸ȣ��", 1, 0, 'C', 1);
		$pdf->Cell($pdf->width * 0.22, $pdf->row_height, $data['yoy_nm'], 1, 1, 'L');

		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.15, 'y'=>$pdf->GetY() + $pdf->row_height * 0.2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.85, 'height'=>5, 'align'=>'L', 'text'=>$data['stat']);

		$pdf->SetX($pdf->left);

		if ($data['org_no'] == '31138000044'){
			$pdf->Cell($pdf->width * 0.15, $pdf->row_height * 5, "��㳻��", 1, 0, 'C', 1);
		}else{
			$pdf->Cell($pdf->width * 0.15, $pdf->row_height * 5, "���º�ȭ", 1, 0, 'C', 1);
		}

		$pdf->Cell($pdf->width * 0.85, $pdf->row_height * 5, "", 1, 1, 'C');

		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.15, 'y'=>$pdf->GetY() + $pdf->row_height * 0.2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.85, 'height'=>5, 'align'=>'L', 'text'=>$data['take']);

		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.15, $pdf->row_height * 5, "��ġ����", 1, 0, 'C', 1);
		$pdf->Cell($pdf->width * 0.85, $pdf->row_height * 5, "", 1, 1, 'C');

		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $pdf->GetY() - $pdf->row_height * 11, $pdf->width, $pdf->row_height * 11);
		$pdf->SetLineWidth(0.2);
	}

	function getPosY($pdf, $row_h, $text){
		$h = $pdf->GetY() + ( $row_h - $pdf->GetStringWidth($text) ) / 2;

		return  $h;
	}



	//ǥ ĭ���̸� ���Ѵ�.
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

	function set_array_text($pdf, $pos){
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