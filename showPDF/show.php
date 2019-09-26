<?
	include_once('../inc/_db_open.php');

	/*********************************************************

		�Ķ��Ÿ

	*********************************************************/
	parse_str($_POST['para'], $var);

	
	if($var['hompageYn'] == 'Y'){
		//����Ȩ���������� ��� ��
	}else {
		include_once('../inc/_http_uri.php');
	}

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
		$var['realSsn'] = $ed->de($var['realSsn']); 
	}



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

	if ($var['showForm'] == 'Iljung'){
		$sql = 'select app_no
			    ,      level
				  from client_his_lvl
				 where org_no = \''.$var['code'].'\'
				   and jumin  = \''.$var['jumin'].'\'
				   and svc_cd = \''.$var['svcCd'].'\'
				   and date_format(from_dt,\'%Y%m\') <= \''.$var['year'].$var['month'].'\'
				   and date_format(to_dt,  \'%Y%m\') >= \''.$var['year'].$var['month'].'\'
				 order by seq desc
				 limit 1';
		$row = $conn->get_array($sql);

		$lsAppno = $row['app_no'];
		$lsLevel = $row['level'];

		unset($row);

		$sql = 'select kind
				,      rate
				 from client_his_kind
				where org_no = \''.$var['code'].'\'
				  and jumin  = \''.$var['jumin'].'\'
				  and date_format(from_dt,\'%Y%m\') <= \''.$var['year'].$var['month'].'\'
				  and date_format(to_dt,  \'%Y%m\') >= \''.$var['year'].$var['month'].'\'
				order by seq desc
				limit 1';
		$row = $conn->get_array($sql);

		$lsKind = $myF->euckr($myF->_kindNm($row['kind']));
		$lsRate = $row['rate'];

		unset($row);
	}

	if (!Empty($lsName) && !Empty($lsName)){
		$para = 'name='.$lsName
			  . '&jumin='.$myF->issStyle($lsJumin);
	}

	if ($var['showForm'] == 'Iljung'){
		$para .= '&appno='.($lsAppno ? substr($lsAppno,0,6).'*****' : '')
			  .  '&level='.$myF->euckr($myF->_lvlNm($lsLevel, $var['svcCd']))
			  .  '&kind='.$lsKind
			  .  '&rate='.$lsRate;
	}else if ($var['showForm'] == 'DEAL_REPORT'){
		if ($var['type'] == '101'){
			//������ ó�찳���� �հ�
			$sql = 'SELECT SUM(times) AS times
					,      SUM(pay) AS pay
					  FROM salary_deal
					 WHERE org_no = \''.$var['code'].'\'
					   AND yymm   = \''.$var['year'].$var['month'].'\'';
		}else{
			$sql = 'SELECT SUM(dtl.salary_work_hours) AS times
					,      SUM(basic.deal_pay) AS pay
					  FROM salary_basic_dtl AS dtl
					 INNER JOIN salary_basic AS basic
						ON basic.org_no = dtl.org_no
					   AND basic.salary_yymm = dtl.salary_yymm
					   AND basic.salary_jumin = dtl.salary_jumin
					 WHERE dtl.org_no      = \''.$var['code'].'\'
					   AND dtl.salary_yymm = \''.$var['year'].$var['month'].'\'
					   AND dtl.salary_kind = \'11\'';
		}

		$arrDealPay = $conn->get_array($sql);

		$para .= '&time='.Number_Format($arrDealPay['times'],1)
			  .  '&pay='.Number_Format($arrDealPay['pay']);

		UnSet($arrPlanDealPay);

	}else if ($var['showForm'] == 'ILJUNG_CALN'){
		//$para .= '&mode='.$var['mode']
		//	  .  '&showGbn='.$var['showGbn'];
		$sql = 'SELECT m00_ctel AS phone
				,      m00_mkind AS svc_cd
				,      m00_bank_no AS bank_no
				,      CASE m00_bank_name WHEN \'001\' then \'�ѱ�����\'
										  WHEN \'002\' then \'�������\'
										  WHEN \'003\' then \'�������\'
										  WHEN \'004\' then \'��������\'
										  WHEN \'005\' then \'��ȯ����\'
										  WHEN \'007\' then \'�����߾�ȸ\'
										  WHEN \'008\' then \'����������\'
										  WHEN \'011\' then \'�����߾�ȸ\'
										  WHEN \'012\' then \'����ȸ������\'
										  WHEN \'020\' then \'�츮����\'
										  WHEN \'023\' then \'SC��������\'
										  WHEN \'027\' then \'�ѱ���Ƽ����\'
										  WHEN \'031\' then \'�뱸����\'
										  WHEN \'032\' then \'�λ�����\'
										  WHEN \'034\' then \'��������\'
										  WHEN \'035\' then \'��������\'
										  WHEN \'037\' then \'��������\'
										  WHEN \'039\' then \'�泲����\'
										  WHEN \'045\' then \'�������ݰ���ȸ\'
										  WHEN \'048\' then \'�����߾�ȸ\'
										  WHEN \'050\' then \'��ȣ��������\'
										  WHEN \'071\' then \'��ü��\'
										  WHEN \'081\' then \'�ϳ�����\'
										  WHEN \'088\' then \'��������\' else m00_bank_name end as bank_nm
				,      m00_bank_depos AS bank_depos
				  FROM m00center
				 WHERE m00_mcode  = \''.$var['code'].'\'
				   AND m00_del_yn = \'N\'
				 ORDER BY svc_cd';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if ($i == 0){
				if (!Empty($para)){
					$para .= '&';
				}
				$para .= 'phone='.$myF->phoneStyle($row['phone'],'.');
			}

			if (!Empty($para)){
				$para .= '&';
			}

			if ($row['svc_cd'] == '0'){
				$para .= $row['svc_cd'].'=�Աݰ���:'.$row['bank_nm'].'('.$row['bank_no'].') ������:'.$row['bank_depos'];
			}
		}

		$conn->row_free();

	}else if ($var['showForm'] == 'CALN_WEEKLY'){
		if ($para) $para .= '&';
		$para .= 'weekly='.$var['weekly'];
	}else{
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
	}

	//��ȸ������ ������������
	if ($var['showForm'] == 'SW_WORK_LOG'  ||
		$var['showForm'] == 'SW_WORK_LOG2' ){


		//�����ڸ�
		$sql = 'SELECT	m03_juso1
				,		m03_juso2
				,		m03_key
				FROM	m03sugupja
				WHERE	m03_ccode = \''.$var['code'].'\'
				AND		m03_mkind = \'0\'
				AND		m03_jumin = \''.$var['jumin'].'\'';

		$row = $conn->get_array($sql);

		$addr = $row['m03_juso1'].' '.$row['m03_juso2'];
		$regKey = $row['m03_key'];

		Unset($row);

		$sql = 'SELECT	date
				FROM	sw_log
				WHERE	org_no	= \''.$var['code'].'\'
				AND		jumin	= \''.$var['jumin'].'\'
				AND		yymm	= \''.$var['yymm'].'\'
				AND		seq		= \''.$var['seq'].'\'';

		$tmpDt = $conn->get_data($sql);
		
		if(!$tmpDt) $tmpDt = date('Ymd');

		//��� �� ��ȿ�Ⱓ
		$sql = 'SELECT	level
				,		app_no
				FROM	client_his_lvl
				WHERE	org_no = \''.$var['code'].'\'
				AND		svc_cd = \'0\'
				AND		jumin  = \''.$var['jumin'].'\'
				AND		DATE_FORMAT(from_dt,\'%Y%m%d\') <= \''.$tmpDt.'\'
				AND		DATE_FORMAT(to_dt,	\'%Y%m%d\') >= \''.$tmpDt.'\'';

		$row = $conn->get_array($sql);
		$lvl = $row['level'];
		$appNo = $row['app_no'];
		
		/*
		if ($lvl){
			$lvl = $lvl.'���';
		}else{
			$lvl = '';
		}
		*/

		$lvl = $myF->_lvlNm($lvl);

		Unset($row);

		//����
		$sql = 'SELECT	date
				,		time
				,		to_time
				,		reg_jumin
				,		reg_name
				,		sign_manager
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

		$para .= '&level='.$myF->euckr($lvl);
		$para .= '&addr='.$myF->utf($addr);
		$para .= '&datetime='.$datetime;
		$para .= '&regName='.$row['reg_name'];
		$para .= '&regKey='.$regKey;
		$para .= '&regYymm='.$var['yymm'];
		$para .= '&regSeq='.$var['seq'];
		$para .= '&signManager='.$row['sign_manager'];
		$para .= '&appNo='.$appNo;

		Unset($row);
	}

	//��ȸ������ ������������ ����(��ȸ�Ⱓ)
	if ($var['showForm'] == 'SW_WORK_LOG_SIGN'){
		$para .= '&fromDt='.$var['fromDt'];
		$para .= '&toDt='.$var['toDt'];
	}

	if ($var['showForm'] == 'REPORT2014'){
		if ($var['mode'] == '3'){
			if ($var['type'] == '8_1'){

				$sql = 'SELECT r_b_dt
						  FROM r_cltplanchn_sub
						 WHERE org_no = \''.$var['code'].'\'
						   AND r_yymm = \''.str_replace('-','',$var['yymm']).'\'
						   AND r_seq  = \''.$var['seq'].'\'';
				$ym = $conn -> get_data($sql);

				$sql = 'SELECT *
						  FROM r_cltplanchn
						 WHERE org_no = \''.$var['code'].'\'
						   AND r_yymm = \''.str_replace('-','',$var['yymm']).'\'
						   AND r_seq  = \''.$var['seq'].'\'';

				$data = $conn->get_array($sql);

				if ($data['r_c_id']){
					if (SubStr($data['r_c_id'],0,7) % 2 == 1){
						$gender = '��';
					}else{
						$gender = '��';
					}
				}else{
					$gender = '';
				}
			}
		}
	}


	//��� �ΰ�
	$sql = 'select m00_icon
			  from m00center
			 where m00_mcode = \''.$_SESSION['userCenterCode'].'\'';
	$icon = $conn -> get_data($sql);


	$sql = 'select m00_jikin
			  from m00center
			 where m00_mcode = \''.$_SESSION['userCenterCode'].'\'';
	$JikIn = $conn -> get_data($sql);

	/**************************************************

		PDF OPEN

	**************************************************/
	$pdf = new MYPDF(strtoupper($paperDir));

	if ($var['showForm'] == 'Iljung'){
		$pdf->left  = 5;
		$pdf->width = 200;
	}else if ($var['showForm'] == 'ILJUNG_CALN'){
		if ($var['dir'] == 'l'){
			$pdf->left	= 14;
			$pdf->top	= 21;
			$pdf->width	= 270;
			$pdf->height= 180;
		}else{
			$pdf->top	= 31;
			$pdf->left	= 7;
			$pdf->width	= 196;
			$pdf->height= 270;
		}
	}else if ($var['showForm'] == 'CALN_WEEKLY'){
		$pdf->left	= 14;
		$pdf->top	= 6;
		$pdf->width	= 270;
		$pdf->height= 195;
	}else if ($var['showForm'] == 'REPORT2014'){
		$pdf->type			= $var['type'];
		$pdf->c_nm          = iconv("utf-8", "euckr", $_SESSION['userCenterName']);	//�����
		$pdf->name			= $data['r_c_nm'];										//�����ڸ�
		$pdf->gender		= $gender;												//����
		$pdf->birthday		= $myF->issToBirthday($data['r_c_id'],'.');				//�ֹι�ȣ

		$var['year']		= $ym != '' ? substr($ym,0,4) : date('Y');

	}else if ($var['showForm'] == 'BUDGET'){
		$pdf->re_gbn = $var['re_gbn'];
		$pdf->year   = $var['year'];
	}else if ($var['showForm'] == 'SPEC'){
		$pdf->re_gbn = $var['re_gbn'];
		$pdf->year   = $var['year'];
	}else if ($var['showForm'] == 'BUDGET_R'){
		$pdf->year   = $var['year'];
	}else if ($var['showForm'] == 'ACCTBK'){
		$pdf->year = $var['year'];
		$pdf->month = $var['month'];
	}else if ($var['showForm'] == 'AR'){ 
		$orgNo = $_SESSION['userCenterCode'];
		$ent_dt = $var['ent_dt'];
		$ent_seq = $var['ent_seq'];
		
		//����Ÿ
		$sql = 'SELECT	a.gwan_cd, a.hang_cd, a.mog_cd, a.wrt_dt, a.mov_dt, a.app_dt, a.rct_dt, a.reg_dt, a.per_dt, a.cause, a.exp_name
				,		c.gbn_name AS ar_type, a.ar_amt, d.gbn_name AS sof_type
				FROM	fa_apprq AS a
				INNER	JOIN	fa_apprq_gbn AS c
						ON		c.gbn_type	= \'T2\'
						AND		c.gbn_cd	= a.ar_type
						AND		c.del_flag	= \'N\'
				INNER	JOIN	fa_apprq_gbn AS d
						ON		d.gbn_type	= \'T3\'
						AND		d.gbn_cd	= a.sof_type
						AND		d.del_flag	= \'N\'
				WHERE	a.org_no	= \''.$orgNo.'\'
				AND		a.ent_dt	= \''.$ent_dt.'\'
				AND		a.ent_seq	= \''.$ent_seq.'\'
				AND		a.del_flag	= \'N\'
				';
		$apprq = $conn->get_array($sql);
		
		$sql = 'SELECT  gwan_name, hang_name, mog_name
				FROM	fa_item
				WHERE	gwan_cd	= \''.$apprq['gwan_cd'].'\'
				AND		hang_cd	= \''.$apprq['hang_cd'].'\'
				AND		mog_cd	= \''.$apprq['mog_cd'].'\'
				AND		re_gbn	= \'E\'';
		
		$item = $conn->get_array($sql); 
		
		$pdf->sign_cd= $apprq['sign_cd'];			//�������
		$pdf->gwan_name= $item['gwan_name'];		//��
		$pdf->hang_name= $item['hang_name'];		//��
		$pdf->mog_name= $item['mog_name'];			//��
		$pdf->exp_name= $apprq['exp_name'];		//�����
		$pdf->mov_dt= $myF->euckr($myF->dateStyle($apprq['mov_dt'],'KOR'));	//�������� 
		$pdf->app_dt= $myF->euckr($myF->dateStyle($apprq['app_dt'],'KOR'));	//��������
		$pdf->rct_dt= $myF->euckr($myF->dateStyle($apprq['rct_dt'],'KOR'));	//�ⳳ����
		$pdf->reg_dt= $myF->euckr($myF->dateStyle($apprq['reg_dt'],'KOR'));	//�������
		$pdf->per_dt= $myF->euckr($myF->dateStyle($apprq['per_dt'],'KOR'));	//��������
		$pdf->sof_type= $apprq['sof_type'];		//�ڱݿ�õ
		$pdf->ar_type= $apprq['ar_type'];			//ǰ������
		$pdf->ar_amt= number_format($apprq['ar_amt']);			//ǰ�Ǳݾ�
		$pdf->cause= $apprq['cause'];				//���ι׿뵵
		
	}else if ($var['showForm'] == 'BS'){
		$pdf->year   = $var['year'];
	}else if ($var['showForm'] == 'SALARY_1'){
		$pdf->year   = $var['year'];
		$pdf->month = $var['month'];
		$pdf->subCd = $var['subCd'];
	}else if ($var['showForm'] == 'SALARY_2'){
		$pdf->year   = $var['year'];
		$pdf->month = $var['month'];
		$pdf->subCd = $var['subCd'];
	}
	
	if ($var['mode'] == '81'){
		//��ʰ��� - ������ ����� ���
		$sql = 'SELECT m03_name
				  from m03sugupja
				 where m03_ccode = \''.$_SESSION['userCenterCode'].'\'
				   and m03_key = \''.$_SESSION['HCE_IPIN'].'\'
				 limit 1';

		$client = $conn -> get_data($sql);

		$pdf->client = $client;
	}

	if($var['fileName'] == 'care_client_find_log'){
		$sql = 'SELECT	*
				FROM	apprline_set
				WHERE	org_no	= \''.$_SESSION['userCenterCode'].'\'
				AND		gbn		= \'01\'';
		$apprline = $conn->get_array($sql);
		
		$sginCnt = $apprline['line_cnt'];
		$sginTxt = Explode('|',$apprline['line_name']);
		$sginPrt = $apprline['prt_yn'];
		
	}else {
		//������ ����
		$sql = 'SELECT	line_cnt, subject
				FROM	signline_set
				WHERE	org_no = \''.$_SESSION['userCenterCode'].'\'';

		$row = $conn->get_array($sql);
	
		$sginCnt = $row['line_cnt'];
		$sginTxt = Explode('|',$row['subject']);

	}
	
	Unset($row);

	$pdf->AddUHCFont($pdf->font_name_kor, $pdf->font_name_eng);
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
	$pdf->jikin     = $JikIn;
	$pdf->icon      = $icon;
	$pdf->sginCnt	= $sginCnt;
	$pdf->sginTxt	= $sginTxt;
	$pdf->subCd     = $var['subCd'];

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

		if ($pdf->showForm == 'ILJUNG_CALN'){
		}else{
			$pdf->MY_ADDPAGE();
		}

		if ($pdf->showForm == 'IssueList' ||
			$pdf->showForm == 'Iljung' ||
			$pdf->showForm == 'DEAL_REPORT' ||
			$pdf->showForm == 'CALN_LIST' ){
			$pdf->SetAutoPageBreak(true);

		}else if ($pdf->showForm == 'HCE'){
			if ($pdf->mode == '131'){
				//$pdf->SetAutoPageBreak(true, 30);
				$pdf->SetAutoPageBreak(false);
			}else{
				$pdf->SetAutoPageBreak(false);
			}

		}else if ($pdf->showForm == 'SALARY_1' ||
				  $pdf->showForm == 'SALARY_2' ||
				  $pdf->showForm == 'ACCTBK'   ||
				  $pdf->showForm == 'BUDGET_R' ){
			
			$pdf->SetAutoPageBreak(true, 25);

		}else if ($pdf->showForm == 'BS' ){
			
			$pdf->SetAutoPageBreak(true, 15);

		}else if ($pdf->showForm == 'GL' ){
			
			$pdf->SetAutoPageBreak(true, 20);

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

	include_once('../inc/_db_close.php');

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

		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.15, 'y'=>$pdf->GetY() + $pdf->row_height * 0.1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.85, 'height'=>5, 'align'=>'L', 'text'=>$data['stat']);

		$pdf->SetX($pdf->left);
		
		if(!$data){ 
			$rowHeight = ($pdf->row_height * 5);
		}else {
			$rowHeight = ($pdf->row_height * 2);
		}
		
		$statHigh =  get_row_cnt($pdf, ($pdf->width * 0.84), $rowHeight, $data['stat'], 5); 
		$takeHigh =  get_row_cnt($pdf, ($pdf->width * 0.84), $rowHeight, $data['take'], 5); 

		if ($data['org_no'] == '31138000044'){
			$pdf->Cell($pdf->width * 0.15, $statHigh, "��㳻��", 1, 0, 'C', 1);
		}else{
			$pdf->Cell($pdf->width * 0.15, $statHigh, "���º�ȭ", 1, 0, 'C', 1);
		}

		$pdf->Cell($pdf->width * 0.85, $statHigh, "", 1, 1, 'C');
		
		
		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.15, 'y'=>$pdf->GetY() + $pdf->row_height * 0.1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.85, 'height'=>5, 'align'=>'L', 'text'=>$data['take']);
		
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.15, $takeHigh, "��ġ����", 1, 0, 'C', 1);
		$pdf->Cell($pdf->width * 0.85, $takeHigh, "", 1, 1, 'C');

		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $pdf->GetY() - ($pdf->row_height + $statHigh + $takeHigh), $pdf->width, ($pdf->row_height + $statHigh + $takeHigh));
		$pdf->SetLineWidth(0.2);
	}

	function getPosY($pdf, $row_h, $text){
		$h = $pdf->GetY() + ( $row_h - $pdf->GetStringWidth($text) ) / 2;

		return  $h;
	}



	//ǥ ĭ���̸� ���Ѵ�.
	function get_row_cnt($pdf, $col_w, $row_h, $text, $rH = false){
	
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
		
		$rH = ($rH != '' ? $rH : 4.7);

		$row_high = $row_cnt*$rH;

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

	function lfSetMark($q, $r){
		if ($q == $r){
			return '��';
		}else{
			return '��';
		}
	}
?>