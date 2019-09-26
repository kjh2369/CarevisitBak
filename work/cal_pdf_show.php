<?
	include('../inc/_db_open.php');
	include('../inc/_myFun.php');
	include('../inc/_ed.php');

	//if($debug) print_r($_REQUEST);

	/*********************************************************
		�����Ȱ������ ����
	*********************************************************/
	$dis_yn = 'N';

	//$debug = false;

	$page_pl = $_GET['page_pl'];

	if ($page_pl != 'p' && $page_pl != 'l'){
		$page_pl = 'p';
	}

	require('../pdf/pdf_cal_table.php');
	
	$conn->set_name('euckr');

	$code	 = $_GET['code'];
	$kind	 = $_GET['kind'];
	$year	 = $_GET['year'];
	$month	 = $_GET['month'];
	$type	 = $_GET['type'];
	$useType = $_GET['useType'];
	$detail	 = $_GET['detail'];
	$family	 = $_GET['family'];

	if (Empty($kind)){
		$kind = $_SESSION["userCenterKind"][0];
	}

	$svcParam = explode('/',$_GET['param']);
	$svcDtlYn = $_GET['svcDtlYn'];
	$printDT  = $_GET['printDT'];
	$workGbn  = $_GET['w_gbn'];

	if (empty($printDT) || strlen($printDT) != 10) $printDT = date('Y-m-d', mktime());

	$colDT     = 't01_sugup_date';
	$colFromTM = 't01_sugup_fmtime';
	$colToTM   = 't01_sugup_totime';
	$colSoyoTM = 't01_sugup_soyotime';
	$colSugaCD = 't01_suga_code1';
	$colSugaVL = 't01_suga_tot';
	$colMemCD1 = 't01_mem_cd1';
	$colMemCD2 = 't01_mem_cd2';
	$colMemNM1 = 't01_mem_nm1';
	$colMemNM2 = 't01_mem_nm2';

	if ($family == 'Y'){
		$family_sql = " and t01_toge_umu = 'Y'
						and t01_svc_subcode = '200'";
	}else if ($family == 'W'){
		$family_sql = " and t01_status_gbn = '1'";

		$colDT     = 't01_conf_date';
		$colFromTM = 't01_conf_fmtime';
		$colToTM   = 't01_conf_totime';
		$colSoyoTM = 't01_conf_soyotime';
		$colSugaCD = 't01_conf_suga_code';
		$colSugaVL = 't01_conf_suga_value';
		$colMemCD1 = 't01_yoyangsa_id1';
		$colMemCD2 = 't01_yoyangsa_id2';
		$colMemNM1 = 't01_yname1';
		$colMemNM2 = 't01_yname2';
	}else{
		$family_sql = "";
	}

	//�ѵ��ݾ�
	$sql = 'select m91_code as cd
			,      m91_kupyeo as amt
			  from m91maxkupyeo
			 where left(m91_sdate, 6) <= \''.$year.$month.'\'
			   and left(m91_edate, 6) >= \''.$year.$month.'\'';
	$laLimitPay = $conn->_fetch_array($sql, 'cd');

	//û���ѵ�
	$sql = 'select jumin
			,      amt
			  from client_his_limit
			 where org_no = \''.$code.'\'
			   and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
			   and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'';
	$laClaimPay = $conn->_fetch_array($sql, 'jumin');



	/*********************************************************
		�޷��� �ִ� �ּ� ����
	*********************************************************/
	$minimum_height = 12;


	// ���ϸ���Ʈ
	$sql = "select *
			  from tbl_holiday
			 where mdate like '$year%'";
	$conn->query($sql);
	$conn->fetch();
	$holiday_count = $conn->row_count();

	for($i=0; $i<$holiday_count; $i++){
		$row = $conn->select_row($i);
		$holiday[$row['mdate']] = $row['holiday_name'];
	}

	$conn->row_free();

	if ($_GET['target'] == 'all'){
		if ($type == 's' || $type == 'c'){
			if (Empty($svcParam[0])){
				$sql = "select min(m03_mkind) as kind
						,      m03_jumin as jumin
						  from m03sugupja
						 inner join t01iljung
							on t01_ccode = m03_ccode
						   and t01_mkind = m03_mkind
						   and t01_jumin = m03_jumin
						   and $colDT like '$year$month%' $family_sql
						   and t01_del_yn = 'N'
						 where m03_ccode  = '$code'
						 group by m03_jumin
						 order by m03_name";
			}else{
				$sql = '';

				foreach($svcParam as $i => $svc){
					$svc = Explode('_', $svc);
					$sql .= (!Empty($sql) ? ' UNION ALL ' : '');
					$sql .= 'SELECT MIN(m03_mkind) AS kind
							 ,      m03_jumin AS jumin
							 ,      m03_name AS name
							   FROM m03sugupja
							  INNER JOIN t01iljung
								 ON t01_ccode = m03_ccode
								AND t01_mkind = m03_mkind
								AND t01_jumin = m03_jumin
								AND '.$colDT.' like \''.$year.$month.'%\' '.$family_sql.'
								AND t01_del_yn = \'N\'';

					if (!empty($svc[0]) or $svc[0] != '')
						$sql .= ' and t01_mkind = \''.$svc[0].'\'';

					if (!empty($svc[1]))
						$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';

					$sql .= ' WHERE m03_ccode  = \''.$code.'\'
							  GROUP BY m03_jumin';
				}

				$sql = 'SELECT DISTINCT kind, jumin
						  FROM ('.$sql.') AS t
						 ORDER BY name';

			}
		}else{
			if (Empty($svcParam[0])){
				$sql = 'select distinct kind, jumin, name
						  from (
								select mst.kind, mst.jumin, mst.name
								  from (
									   select min(m02_mkind) as kind
									   ,      m02_yjumin as jumin
									   ,      m02_yname as name
										 from m02yoyangsa
										where m02_ccode = \''.$code.'\'
										group by m02_yjumin
									   ) as mst
								 inner join (
									   select min(t01_mkind) as kind
									   ,      '.$colMemCD1.' as jumin
										 from t01iljung
										where t01_ccode          = \''.$code.'\'
										  and left('.$colDT.',6) = \''.$year.$month.'\' '.$family_sql.'
										  and t01_del_yn         = \'N\'
										group by '.$colMemCD1.'
									   ) as iljung
									on mst.jumin = iljung.jumin
								 union all
								select mst.kind, mst.jumin, mst.name
								  from (
									   select min(m02_mkind) as kind
									   ,      m02_yjumin as jumin
									   ,      m02_yname as name
										 from m02yoyangsa
										where m02_ccode = \''.$code.'\'
										group by m02_yjumin
									   ) as mst
								 inner join (
									   select min(t01_mkind) as kind
									   ,      '.$colMemCD2.' as jumin
										 from t01iljung
										where t01_ccode      = \''.$code.'\'
										  and left('.$colDT.',6) = \''.$year.$month.'\' '.$family_sql.'
										  and t01_del_yn     = \'N\'
										group by '.$colMemCD2.'
									   ) as iljung
									on mst.jumin = iljung.jumin
							   ) as t
						 order by name';
			}else {

				$sql = '';

				foreach($svcParam as $i => $svc){
					$svc = Explode('_', $svc);
					$sql .= (!Empty($sql) ? ' UNION ALL ' : '');
					$sql .= ' SELECT MIN(m02_mkind) AS kind
							 ,      m02_yjumin AS jumin
							 ,      m02_yname AS name
							   FROM m02yoyangsa
							  INNER JOIN t01iljung
								 ON t01_ccode = m02_ccode
								AND t01_mkind = m02_mkind
								AND '.$colMemCD1.' = m02_yjumin
								AND '.$colDT.' like \''.$year.$month.'%\' '.$family_sql.'
								AND t01_del_yn = \'N\'';

					if (!empty($svc[0]) or $svc[0] != '')
						$sql .= ' and t01_mkind = \''.$svc[0].'\'';

					if (!empty($svc[1]))
						$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';

					$sql .= ' WHERE m02_ccode  = \''.$code.'\'
							  GROUP BY m02_yjumin';

					$sql .= ' union all ';
					$sql .= ' SELECT MIN(m02_mkind) AS kind
							 ,      m02_yjumin AS jumin
							 ,      m02_yname AS name
							   FROM m02yoyangsa
							  INNER JOIN t01iljung
								 ON t01_ccode = m02_ccode
								AND t01_mkind = m02_mkind
								AND '.$colMemCD2.' = m02_yjumin
								AND '.$colDT.' like \''.$year.$month.'%\' '.$family_sql.'
								AND t01_del_yn = \'N\'';

					if (!empty($svc[0]) or $svc[0] != '')
						$sql .= ' and t01_mkind = \''.$svc[0].'\'';

					if (!empty($svc[1]))
						$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';

					$sql .= ' WHERE m02_ccode  = \''.$code.'\'
							  GROUP BY m02_yjumin';
				}

				$sql = 'SELECT DISTINCT kind, jumin, name
						  FROM ('.$sql.') AS t
						 ORDER BY name';
			}
		}

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row         = $conn->select_row($i);
			$svcKind[$i] = $row['kind'];
			$list[$i]    = $row['jumin'];
		}

		$conn->row_free();
	}else if (!is_numeric($_GET['target'])){
		$target     = $ed->de($_GET['target']); //iconv("EUC-KR","UTF-8",$_GET['target'])
		$svcKind[0] = $conn->_client_kind_cd($code, $target);
		$list[0]    = $target;
	}else{
		if ($type == 's' || $type == 'c'){
			$sql = "select m03_jumin
					  from m03sugupja
					 where m03_ccode = '".$code."'
					   and m03_mkind = '".$_GET['kind']."'
					   and m03_key   = '".$_GET['target']."'";

		}else{
			$sql = "select m02_yjumin
					  from m02yoyangsa
					 where m02_ccode = '".$code."'
					   and m02_mkind = '".$_GET['kind']."'
					   and m02_key   = '".$_GET['target']."'";
		}
		$target     = $conn->get_data($sql);
		$list[0]    = $target;
		$svcKind[0] = $_GET['kind'];
	}

	//if (strLen($target) == 0) $target = $ed->de($_GET['target']);

	//������ ����
	$sql = 'SELECT	line_cnt, subject
			FROM	signline_set
			WHERE	org_no = \''.$_SESSION['userCode'].'\'';

	$row = $conn->get_array($sql);

	$sginCnt = $row['line_cnt'];
	$sginTxt = Explode('|',$row['subject']);

	Unset($row);

	// ��������
	$sql = "select m00_cname
				 , m00_mname
				 , m00_ctel
				 , m00_bank_no
				 , case m00_bank_name when '001' then '�ѱ�����'
									  when '002' then '�������'
									  when '003' then '�������'
									  when '004' then '��������'
									  when '005' then '��ȯ����'
									  when '007' then '�����߾�ȸ'
									  when '008' then '����������'
									  when '011' then '�����߾�ȸ'
									  when '012' then '����ȸ������'
									  when '020' then '�츮����'
									  when '023' then 'SC��������'
									  when '027' then '�ѱ���Ƽ����'
									  when '031' then '�뱸����'
									  when '032' then '�λ�����'
									  when '034' then '��������'
									  when '035' then '��������'
									  when '037' then '��������'
									  when '039' then '�泲����'
									  when '045' then '�������ݰ�����ȸ'
									  when '048' then '�����߾�ȸ'
									  when '050' then '��ȣ��������'
									  when '071' then '��ü��'
									  when '081' then '�ϳ�����'
									  when '088' then '��������' else m00_bank_name end as bank_name
				 , m00_bank_depos
			  from m00center
			 where m00_mcode = '$code'
			   and m00_mkind = '$kind'";
	$row = $conn->get_array($sql);
	$centerName = $row[0];
	$manager    = $row[1];
	$centerTel	= $myF->phoneStyle($row[2]);
	$bank_No	= $row[3];
	$bank_Name	= $row[4];
	$bank_Depos	= $row[5];

	$pdf = new MYPDF(strtoupper($page_pl));
	$pdf->SetAutoPageBreak(false);
	$pdf->debug = $debug;
	if($code == '24613000160'){
		$pdf->acctBox = false;
	}else {
		$pdf->acctBox = true;
	}
	$pdf->font_name_kor = '����';
	$pdf->font_name_eng = 'Batang';
	$pdf->AddUHCFont('����','Batang');
	$pdf->Open();
	$pdf->SetFillColor(220,220,220);

	// ����� ����
	$pdf->auto_draw_head= $detail == 'y' ? true : false;
	$pdf->year			= $year;		//��
	$pdf->month			= $month;		//��
	$pdf->type			= $type;		//������, ��纸ȣ�� ����
	$pdf->family		= $family;		//ǥ�ñ���
	$pdf->useType		= $useType;		//������, ���� ����
	$pdf->centerCode	= $code;		//�����ڵ�
	$pdf->centerName	= $centerName;	//���͸�
	$pdf->centerTel		= $centerTel;	//������ȭ��ȣ
	$pdf->bankNo        = $bank_No;		//���¹�ȣ
	$pdf->bankName      = $bank_Name;	//�����
	$pdf->bankDepos     = $bank_Depos;	//������
	$pdf->workGbn       = $workGbn;		//Ÿ��Ʋ ��±���
	$pdf->sginCnt	= $sginCnt;
	$pdf->sginTxt	= $sginTxt;



	if($page_pl == 'p'){
		$pdf->left = 7;
		$pdf->width = 196;
	}

	for($l=0; $l<sizeOf($list); $l++){
		$dis_yn = 'N'; //�����Ȱ������ ����
		$pdf->kind = $svcKind[$l];		//���񽺱���

		$target = $list[$l];
		if ($type == 's' || $type == 'c'){
			if ($lbTestMode){
				$sql = 'select min(m03_mkind)
						,      m03_jumin as jumin
						,      m03_name as name
						,      lvl.app_no as app_no
						,      CASE lvl.svc_cd WHEN \'0\' THEN lvl.level
						                       WHEN \'4\' THEN dis.lvl ELSE \'\' END as lvl
						,      kind.kind as kind
						,      kind.rate as rate
						  from m03sugupja
						  left join (
							   select jumin
							   ,      svc_cd
							   ,      seq
							   ,      app_no
							   ,      level
							   ,      from_dt
							   ,      to_dt
								 from client_his_lvl
								where org_no = \''.$code.'\'
								  and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								  and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							   ) as lvl
							on lvl.jumin = m03_jumin
						  left join (
							   select jumin
							   ,      seq
							   ,      kind
							   ,      rate
							   ,      from_dt
							   ,      to_dt
								 from client_his_kind
								where org_no = \''.$code.'\'
								  and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								  and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							   ) as kind
							on kind.jumin = m03_jumin
						  LEFT JOIN (
						       SELECT jumin
							   ,      seq
							   ,      svc_val AS val
							   ,      svc_lvl AS lvl
							     FROM client_his_dis
								WHERE org_no = \''.$code.'\'
								  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								  AND DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
						       ) AS dis
							ON dis.jumin = m03_jumin
						 where m03_ccode = \''.$code.'\'
						   and m03_jumin = \''.$target.'\'
						 group by m03_jumin';

				$row = $conn->get_array($sql);

				$pdf->name	= $row['name'];

				if($useType == 'y' && $svcDtlYn == 'undefined' && $code == '31135000055'){
					/******************************************
						2012.09.26 ���ֿ� ����
						������Ͽ��� ����ǥ���1 ��츸
						�����簡��缾�� �ֹι�ȣ ���̵��� ǥ��
					*******************************************/
					$pdf->jumin	= $myF->issNo($row['jumin']);
				}else {
					$pdf->jumin	= $myF->issStyle($row['jumin']);
				}
				$pdf->no	= $row['app_no'];
				$pdf->lvlCd = $row['lvl'];
				$pdf->level	= $myF->euckr($myF->_lvlNm($row['lvl']));
				$pdf->rate	= $myF->euckr($myF->_kindNm($row['kind'])).(!empty($row['kind'])?' / ':'').$row['rate'];

				if ($pdf->kind == '0' || $pdf->kind == '4'){
				}else{
					if ($pdf->level == '�Ϲ�'){
						$pdf->level = '';
					}
				}

				if ($pdf->kind == '0'){
				}else{
					if ($pdf->rate == '�Ϲ�'){
						$pdf->rate = '';
					}
				}
			}else{
				$sql = "select m03_name
						,      m03_injung_no
						,      case when m03_mkind = '0' then LVL.m81_name
									when m03_mkind = '4' then m03_ylvl else '' end
						,      case when m03_mkind = '0' then STP.m81_name else '' end
						,      case when m03_mkind = '0' then m03_bonin_yul else '' end
						  from m03sugupja
						  left join m81gubun as LVL
							on LVL.m81_gbn  = 'LVL'
						   and LVL.m81_code = m03_ylvl
						  left join m81gubun as STP
							on STP.m81_gbn  = 'STP'
						   and STP.m81_code = m03_skind
						 where m03_ccode  = '$code'
						   and m03_jumin  = '$target'
						   and m03_del_yn = 'N'
						 limit 1";
				$row = $conn->get_array($sql);

				$pdf->name	= $row[0];
				$pdf->jumin	= $myF->issStyle($target);
				$pdf->no	= $row[1];
				$pdf->level	= $row[2];
				$pdf->rate	= $row[3].(!empty($row[4])?' / ':'').$row[4];
			}
		}else{
			$sql = "select m02_yname, m02_ycode, m02_ytel
					  from m02yoyangsa
					 where m02_ccode  = '$code'
					   and m02_yjumin = '$target'
					   and m02_del_yn = 'N'
					 limit 1";
			$row = $conn->get_array($sql);

			$pdf->name	= $row[0];
			$pdf->jumin	= $row[1];
			$pdf->no	= $myF->phoneStyle($row[2]);
		}

		if ($detail == 'y'){
			// ������ �߰�
			$pdf->AddPage(strtoupper($page_pl), 'A4');

			// ���� ���� ����
			$calTime   = mkTime(0, 0, 1, $pdf->month, 1, $pdf->year);
			$today     = date('Ymd', mktime());
			$lastDay   = date('t', $calTime); //���ϼ� ���ϱ�
			$startWeek = date('w', strtotime(date('Y-m', $calTime).'-01')); //���ۿ��� ���ϱ�
			$totalWeek = ceil(($lastDay + $startWeek) / 7); //�� �� ������ ���ϱ�
			$lastWeek  = date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay)); //������ ���� ���ϱ�

			$date = $pdf->year.$pdf->month;

			// �Ϻ� ����Ÿ �ʱ�ȭ
			for($i=1; $i<=$lastDay; $i++){
				$cal[$i][0]['service']	= ''; //���񽺸�
				$cal[$i][0]['time']		= ''; //��ȹ�ð�
				$cal[$i][0]['cost']		= ''; //�ҿ�ð�
				$cal[$i][0]['worker']	= ''; //��纸ȣ��
			}


			// �Ϻ� ����Ÿ
			if ($type == 's'){
				$sql = '';

				foreach($svcParam as $i => $svc){
					$svc = explode('_', $svc);

					$sql .= (!empty($sql) ? ' union all ' : '');
					$sql .= 'select cast(date_format('.$colDT.', \'%d\') as signed)
							 ,      date_format(concat('.$colDT.', '.$colFromTM.',\'00\'), \'%H:%i\')
							 ,      date_format(concat('.$colDT.', '.$colToTM.',\'00\'), \'%H:%i\')
							 ,      '.$colSoyoTM.'
							 ,      case t01_svc_subcode when \'200\' then case when t01_mkind = \'0\' then \'���\' else \'���\' end
														 when \'500\' then \'���\'
														 when \'800\' then \'��ȣ\'
														 when \'21\'  then \'����\'
														 when \'22\'  then \'����\'
														 when \'23\'  then \'���\'
														 when \'24\'  then \'���\'
														 when \'31\'  then \'����\'
														 when \'32\'  then \'����\'
														 when \'33\'  then \'��Ÿ\' else \'-\' end
							 ,      '.$colMemNM1.'
							 ,      concat(case when '.$colMemNM2.' != \'\' then \'/\' else \'\' end, left('.$colMemNM2.', 3))
							 ,	    t01_toge_umu
							 ,      t01_bipay_umu
							   from t01iljung
							  where t01_ccode     = \''.$code.'\'
								and t01_jumin     = \''.$target.'\'
								and '.$colDT.' like \''.$date.'%\' '.$family_sql.'
								and t01_del_yn    = \'N\'';

					if ($code == '31141000043' /* ����� */)
						$sql .= ' and t01_bipay_umu != \'Y\'';

					if (!empty($svc[0]))
						$sql .= ' and t01_mkind = \''.$svc[0].'\'';

					if (!empty($svc[1]))
						$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';

				}

				/*
				$sql = "select cast(date_format($colDT, '%d') as signed)
						,      date_format(concat($colDT, $colFromTM,'00'), '%H:%i')
						,      date_format(concat($colDT, $colToTM,'00'), '%H:%i')
						,      $colSoyoTM
						,      case t01_svc_subcode when '200' then case when t01_mkind = '0' then '���' else '���' end
													when '500' then '���'
													when '800' then '��ȣ'
													when '21'  then '����'
													when '22'  then '����'
													when '23'  then '���'
													when '24'  then '���'
													when '31'  then '����'
													when '32'  then '����'
													when '33'  then '��Ÿ'
													else '-' end
						,      $colMemNM1
						,      concat(case when $colMemNM2 != '' then '/' else '' end, left($colMemNM2, 3))
						,	   t01_toge_umu
						  from t01iljung
						 where t01_ccode  = '$code'
						   and t01_jumin  = '$target'
						   and $colDT like '$date%' $family_sql
						   and t01_del_yn = 'N'";

				if ($code == '31141000043' ){ //�����
					$sql .= " and t01_bipay_umu != 'Y'";
				}

				$sql .= " order by $colDT, $colFromTM, $colToTM";
				*/

			}else if ($type == 'c'){
				$sql = "select cast(date_format($colDT, '%d') as signed)
						,      date_format(concat($colDT, $colFromTM,'00'), '%H:%i')
						,      date_format(concat($colDT, $colToTM,'00'), '%H:%i')
						,      $colSoyoTM
						,      case t01_svc_subcode when '200' then case when t01_mkind = '0' then '���' else '���' end
													when '500' then '���'
													when '800' then '��ȣ'
													when '21'  then '����'
													when '22'  then '����'
													when '23'  then '���'
													when '24'  then '���'
													when '31'  then '����'
													when '32'  then '����'
													when '33'  then '��Ÿ'
													else '-' end
						,      $colMemNM1
						,      concat(case when $colMemNM2 != '' then '/' else '' end, left($colMemNM2, 3))
						,	   t01_toge_umu
						,      t01_bipay_umu
						  from t01iljung
						 where t01_ccode  = '$code'
						   and t01_mkind  = '$kind'
						   and t01_jumin  = '$target'
						   and $colDT like '$date%' $family_sql
						   and t01_del_yn = 'N'
						 order by $colDT, $colFromTM, $colToTM";
			}else{
				$sql = '';

				foreach($svcParam as $i => $svc){
					$svc = explode('_', $svc);

					$sql .= (!empty($sql) ? ' union all ' : '');
					$sql .= 'select cast(date_format('.$colDT.', \'%d\') as signed) as dt
							 ,      date_format(concat('.$colDT.', '.$colFromTM.',\'00\'), \'%H:%i\') as f_time
							 ,      date_format(concat('.$colDT.', '.$colToTM.',\'00\'), \'%H:%i\') as t_time
							 ,      '.$colSoyoTM.' as p_time
							 ,      case t01_svc_subcode when \'200\' then case when t01_mkind = \'0\' then \'���\' else \'���\' end
														 when \'500\' then \'���\'
														 when \'800\' then \'��ȣ\'
														 when \'21\'  then \'����\'
														 when \'22\'  then \'����\'
														 when \'23\'  then \'���\'
														 when \'24\'  then \'���\'
														 when \'31\'  then \'����\'
														 when \'32\'  then \'����\'
														 when \'33\'  then \'��Ÿ\' else \'-\' end as svc_nm
							 ,      case t01_svc_subcode when \'500\' then case '.$colMemCD1.' when \''.$target.'\' then \'(��)\' else \'(��)\' end else \'\' end as ms_gbn
							 ,	    t01_toge_umu as family_yn
							 ,      t01_ccode as code, t01_mkind as kind, t01_jumin as c_cd
							   from t01iljung
							  where t01_ccode      = \''.$code.'\'
								and t01_del_yn     = \'N\'
								and '.$colMemCD1.' = \''.$target.'\'
								and left('.$colDT.', '.strlen($date).') = \''.$date.'\' '.$family_sql;

					if ($code == '31141000043') //�����
						$sql .= ' and t01_bipay_umu != \'Y\'';

					if ($svc[0] != '')
						$sql .= ' and t01_mkind = \''.$svc[0].'\'';

					if ($svc[1] != '')
						$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';

					$sql .= ' union all
							 select cast(date_format('.$colDT.', \'%d\') as signed) as dt
							 ,      date_format(concat('.$colDT.', '.$colFromTM.',\'00\'), \'%H:%i\') as f_time
							 ,      date_format(concat('.$colDT.', '.$colToTM.',\'00\'), \'%H:%i\') as t_time
							 ,      '.$colSoyoTM.' as p_time
							 ,      case t01_svc_subcode when \'200\' then case when t01_mkind = \'0\' then \'���\' else \'���\' end when \'500\' then \'���\' when \'800\' then \'��ȣ\' else \'-\' end as svc_nm
							 ,      case t01_svc_subcode when \'500\' then case '.$colMemCD1.' when \''.$target.'\' then \'(��)\' else \'(��)\' end else \'\' end as ms_gbn
							 ,	    t01_toge_umu as family_yn
							 ,      t01_ccode as code, t01_mkind as kind, t01_jumin as c_cd
							   from t01iljung
							  where t01_ccode      = \''.$code.'\'
								and t01_del_yn     = \'N\'
								and '.$colMemCD2.' = \''.$target.'\'
								and left('.$colDT.', '.strlen($date).') = \''.$date.'\' '.$family_sql;

					if ($code == '31141000043') //�����
						$sql .= ' and t01_bipay_umu != \'Y\'';

					if ($svc[0] != '')
						$sql .= ' and t01_mkind = \''.$svc[0].'\'';

					if ($svc[1] != '')
						$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';
				}

				$sql = 'select dt, f_time, t_time, p_time, svc_nm, m03_name as c_nm, ms_gbn, family_yn
						  from ('.$sql.') as t
						 inner join m03sugupja
							on m03_ccode = t.code
						   and m03_mkind = t.kind
						   and m03_jumin = t.c_cd
						 order by dt, f_time, t_time';

				/*********************************************************
				$sql = "select cast(date_format($colDT, '%d') as signed) as $colDT
						,      date_format(concat($colDT, $colFromTM,'00'), '%H:%i') as $colFromTM
						,      date_format(concat($colDT, $colToTM,'00'), '%H:%i') as $colToTM
						,      $colSoyoTM
						,      case t01_svc_subcode when '200' then case when t01_mkind = '0' then '���' else '���' end
													when '500' then '���'
													when '800' then '��ȣ'
													when '21'  then '����'
													when '22'  then '����'
													when '23'  then '���'
													when '24'  then '���'
													when '31'  then '����'
													when '32'  then '����'
													when '33'  then '��Ÿ'
													else '-' end
						,      m03_name
						,      case t01_svc_subcode when '500' then case $colMemCD1 when '$target' then '(��)' else '(��)' end else '' end
						,	   t01_toge_umu
						  from t01iljung
						 inner join m03sugupja
							on m03_ccode = t01_ccode
						   and m03_mkind = t01_mkind
						   and m03_jumin = t01_jumin
						 where t01_ccode = '$code'
						   and $colMemCD1 = '$target'
						   and $colDT like '$date%' $family_sql
						   and t01_del_yn = 'N'
						 union all
						select cast(date_format($colDT, '%d') as signed) as $colDT
						,      date_format(concat($colDT, $colFromTM,'00'), '%H:%i') as $colFromTM
						,      date_format(concat($colDT, $colToTM,'00'), '%H:%i') as $colToTM
						,      $colSoyoTM
						,      case t01_svc_subcode when '200' then case when t01_mkind = '0' then '���' else '���' end when '500' then '���' when '800' then '��ȣ' else '-' end
						,      m03_name
						,      case t01_svc_subcode when '500' then case $colMemCD1 when '$target' then '(��)' else '(��)' end else '' end
						,	   t01_toge_umu
						  from t01iljung
						 inner join m03sugupja
							on m03_ccode = t01_ccode
						   and m03_mkind = t01_mkind
						   and m03_jumin = t01_jumin
						 where t01_ccode = '$code'
						   and $colMemCD2 = '$target'
						   and $colDT like '$date%' $family_sql
						   and t01_del_yn = 'N'
						 order by $colDT, $colFromTM, $colToTM";
				*********************************************************/
			}

			
			$conn->query($sql);
			$conn->fetch();
			$rowCount = $conn->row_count();

			$day = 0;

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				if ($day != $row[0]){
					$day = $row[0];
					$seq = 0;
				}


				$cal[$day][$seq]['service']	= $row[4];
				$cal[$day][$seq]['cost']	= $row[3];
				$cal[$day][$seq]['toge']	= $row[7];
				$cal[$day][$seq]['bipay']   = $row[8];

				/*********************************************************
					�����Ȱ������
					*****************************************************/

					if ($cal[$day][$seq]['service'] == '���'){
						$dis_yn = 'Y';
					}
				/********************************************************/


				if ($type == 's' || $type == 'c'){
					$cal[$day][$seq]['time']	= $row[1].'~'.$row[2];
					$cal[$day][$seq]['worker']	= $row[5].$row[6];
				}else{
					$cal[$day][$seq]['time']	= $row[1].'~'.$row[2]/*.$row[6]*/;
					$cal[$day][$seq]['worker']	= $row[5];
				}

				$seq ++;
			}
			$conn->row_free();

			$day = 1;
			$height	= $pdf->rowHeight;

			// ������ ���̸� ����Ѵ�.
			for($i=1; $i<=$totalWeek; $i++){
				for($j=0; $j<7; $j++){
					$checkTop[$i] = 0;
					if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
						if (sizeOf($cal[$day]) > 0){
							for($k=0; $k<sizeOf($cal[$day]); $k++){
								if (strLen($cal[$day][$k]['service']) != ''){
									if ($type == 's' || $type == 'c'){
										$checkTop[$i] += ($height * 2);
									}else{
										$checkTop[$i] += ($height * 1.5);
									}
								}
							}
						}
						$day++;
					}
				}
			}

			//print_r($checkTop);

			$col = $pdf->calranderColWidth();

			//$liAddTop = 15;
			//$liAddTop = 25;

			//$liAddTop = 25;
			//$top = $pdf->top+$liAddTop;

			$top = $pdf->GetY();
			$liAddTop = $top - $pdf->top;

			$left	= $pdf->left;
			$height	= $pdf->rowHeight;

			$pdf->SetXY($left, $top);

			$day = 1; //ȭ�鿡 ǥ���� ȭ���� �ʱⰪ�� 1�� ����
			for($i=1; $i<=$totalWeek; $i++){
				// ����
				if ($i == 1){
					$pdf->SetFont('����','B',10);
					for($j=0; $j<7; $j++){
						$pdf->Cell($col['w'][$j], $height, $col['t'][$j], 1, $j < 6 ? 0 : 1, 'C', true);
					}
					$pdf->SetFont('����','',10);
					$top += $height;
					$pdf->SetFillColor(238,238,238);
				}

				$left = $pdf->left;

				// �� ����ĭ �����
				for($j=0; $j<7; $j++){
					$tempTop[$j] = 1;
					if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
						if (sizeOf($cal[$day]) > 0){
							// ������ ���̿� ��ǥ�� �ٽ� ���
							if($page_pl == 'p'){
								$checkHeight = $top + $height + $checkTop[$i] + $height * ($type == 's' || $type == 'c' ? 3.5 : 2.5) + $height + 3;
							}else {
								$checkHeight = $top + $height + $checkTop[$i] + $height * ($type == 's' || $type == 'c' ? 4.8 : 6.7) + $height + 4;
							}

							// ������ ���̰� �������� �Ѿ�� �������� �߰��Ѵ�.
							if ($checkHeight >= $pdf->height - 25){
								// ���Ϻ� ����
								drawLine($pdf, $col, $top, $liAddTop);

								// �׵θ�
								drawBorder($pdf, $top-$height*6-($liAddTop - 15), $liAddTop);

								$pdf->AddPage(strtoupper($page_pl), 'A4');
								$pdf->SetX($pdf->left);
								$top = $pdf->top+$liAddTop;
								$tempTop[$j] = 1;
							}

							if ($holiday[$year.$month.($day<10?'0':'').$day] != ''){
								$str_day = $day;
								$color_yn = 'Y';
							}else{
								$str_day = $day;
								$color_yn = 'N';
							}

							for($k=0; $k<sizeOf($cal[$day]); $k++){
								switch($j){
								case 0:
									switch($code){
										case '34415000061': //���ֻ������
											$pdf->SetTextColor(0,0,0);
											break;
										default:
											$pdf->SetTextColor(255,0,0);
									}
									break;
								case 6:
									switch($code){
										case '34415000061': //���ֻ������
											$pdf->SetTextColor(0,0,0);
											break;
										default:
											$pdf->SetTextColor(0,0,255);
									}
									break;
								default:
									$pdf->SetTextColor(0,0,0);
								}

								switch($code){
									case '34415000061': //���ֻ������
										break;
									default:
										if ($color_yn == 'Y'){
											$pdf->SetTextColor(255,0,0);
										}
								}
								if ($k == 0){
									$pdf->SetXY($left, $top);
									$pdf->SetFont('����','B',10);
									$pdf->Cell($col['w'][$j], $height-0.5, "$str_day", "T", 0, 'L');

									if ($holiday[$year.$month.($day<10?'0':'').$day] != ''){
										$pdf->SetFont('����','',9);
										$pdf->SetX($left+$pdf->GetStringWidth(" $str_day"));
										$pdf->Cell($col['w'][$j], $height-0.5, $holiday[$year.$month.($day<10?'0':'').$day], "T", 1, 'L');
									}

									$pdf->SetFont('����','',9);

									if($page_pl == 'p'){
										$pdf->SetFont('����','B',10);
									}else if($page_pl == 'l'){
										$pdf->SetFont('����','B',11);
									}
								}

								$pdf->SetTextColor(0,0,0);


								if (strLen($cal[$day][$k]['service']) != ''){
									$pdf->SetXY($left, $top + $height + $tempTop[$j]-0.5);

									if($page_pl == 'l'){
										if($cal[$day][$k]['toge'] == 'Y'){
											if ($type == 's' || $type == 'c'){
												$pdf->MultiCell($col['w'][$j], 6, "[���� ".$cal[$day][$k]['service']."]\n".$cal[$day][$k]['time']."\n".$cal[$day][$k]['worker']);
												$tempTop[$j] += ($height * 3);
											}else{
												$pdf->MultiCell($col['w'][$j], 6, "[���� ".$cal[$day][$k]['service']."]".$cal[$day][$k]['worker']."\n".$cal[$day][$k]['time']);
												$tempTop[$j] += ($height * 2.2);
											}
										}else {
											if ($type == 's' || $type == 'c'){
												$pdf->MultiCell($col['w'][$j], 6, "[".$cal[$day][$k]['service']."]".($cal[$day][$k]['bipay'] == 'Y' ? '[��]' : '')."\n".$cal[$day][$k]['time']."\n".$cal[$day][$k]['worker']);
												$tempTop[$j] += ($height * 3);
											}else{
												$pdf->MultiCell($col['w'][$j], 6, "[".$cal[$day][$k]['service']."]".$cal[$day][$k]['worker']."\n".$cal[$day][$k]['time']);
												$tempTop[$j] += ($height * 2.2);
											}
										}
									}else if($page_pl == 'p'){
										if($cal[$day][$k]['toge'] == 'Y'){
											if ($type == 's' || $type == 'c'){
												#$pdf->MultiCell($col['w'][$j], 3.7, "[����]\n".$cal[$day][$k]['time']."\n".$cal[$day][$k]['worker']);
												//$pdf->MultiCell($col['w'][$j], 3.7, "[����]\n".$cal[$day][$k]['time']);
												$pdf->MultiCell($col['w'][$j], 3.7, "[����]");

												$pdf->SetXY($left, $top + $height + $tempTop[$j] + 3.5);
												$pdf->Cell($col['w'][$j], 3, $cal[$day][$k]['time']);

												$pdf->SetXY($left, $top + $height + $tempTop[$j] + 7.4);
												$pdf->Cell($col['w'][$j], 3.7, $cal[$day][$k]['worker'], 0, 1, 'R');

												$tempTop[$j] += ($height * 2);
											}else{
												$pdf->MultiCell($col['w'][$j], 3.7, "[����]".$cal[$day][$k]['worker']."\n".$cal[$day][$k]['time']);
												$tempTop[$j] += ($height * 1.5);
											}
										}else {
											if ($type == 's' || $type == 'c'){
												#$pdf->MultiCell($col['w'][$j], 3.7, "[".$cal[$day][$k]['service']."]\n".$cal[$day][$k]['time']."\n".$cal[$day][$k]['worker']);
												//$pdf->MultiCell($col['w'][$j], 3.7, "[".$cal[$day][$k]['service']."]".($cal[$day][$k]['bipay'] == 'Y' ? '[��]' : '')."\n".$cal[$day][$k]['time']);
												$pdf->MultiCell($col['w'][$j], 3.6, "[".$cal[$day][$k]['service']."]".($cal[$day][$k]['bipay'] == 'Y' ? '[��]' : ''));

												$pdf->SetXY($left, $top + $height + $tempTop[$j] + 3.6);
												$pdf->Cell($col['w'][$j], 3, $cal[$day][$k]['time']);

												$pdf->SetXY($left, $top + $height + $tempTop[$j] + 7.4);
												$pdf->Cell($col['w'][$j], 3, $pdf->_splitTextWidth($myF->utf($cal[$day][$k]['worker']),$col['w'][$j]), 0, 1, 'R');

												$tempTop[$j] += ($height * 2);
											}else{
												$lsTmp = $myF->utf("[".$cal[$day][$k]['service']."]".$cal[$day][$k]['worker']);
												$liTmp = $col['w'][$j];
												$liCnt = $myF->len($lsTmp);
												$lsStr = '';

												for($s=0; $s<$liCnt; $s++){
													$lsStr .= $myF->mid($lsTmp,$s,1);

													if ($pdf->GetStringWidth($lsStr) >= $liTmp){
														break;
													}
												}

												$lsStr = $myF->euckr($lsStr);

												$pdf->MultiCell($col['w'][$j], 3.7, $lsStr."\n".$cal[$day][$k]['time']);
												$tempTop[$j] += ($height * 1.5);
											}
										}
									}

									/*
									switch($cal[$day][$k]['service']){
										case '���':
											$pdf->Image('../image/icon_bath.jpg', $left + $col['w'][$j] - 4 - 1, $top + $tempTop[$j] - $height - 1, 4, 4);
											break;
										case '��ȣ':
											$pdf->Image('../image/icon_nurs.jpg', $left + $col['w'][$j] - 4 - 1, $top + $tempTop[$j] - $height - 1, 4, 4);
											break;
									}
									*/

									if ($cal[$day][$k]['service'] == '���'){
										$lsIconFile = '../image/icon_bath.jpg';
									}else if ($cal[$day][$k]['service'] == '��ȣ'){
										$lsIconFile = '../image/icon_nurs.jpg';
									}else{
										$lsIconFile = '';
									}

									if (!Empty($lsIconFile)){
										$pdf->Image($lsIconFile, $left + $col['w'][$j] - 4 - 0.5, $top + $tempTop[$j] - $height + 2.4, 3.5, 3.5);
									}
								}
							}
						}else{
							$pdf->SetXY($left, $top);
							$pdf->Cell($col['w'][$j], $height, " ", 1, 1);
						}
						$left += $col['w'][$j];
						$day ++;
					}else{
						$pdf->SetXY($left, $top);
						$pdf->Cell($col['w'][$j], $height, " ", "T", 1);
						$left += $col['w'][$j];
					}
				}

				$tempHeight = 0;
				for($j=0; $j<7; $j++){
					if ($tempHeight < $tempTop[$j]) $tempHeight = $tempTop[$j];
				}

				/*********************************************************
					�ּ� ���̸� �����.
				*********************************************************/
				if ($tempHeight < $minimum_height) $tempHeight = $minimum_height;


				if ($tempHeight > 0){
					$top += ($height + $tempHeight);
				}else{
					$top += ($height * 2.5);
				}
			}

			$cal = array();

			// ���Ϻ� ����
			drawLine($pdf, $col, $top, $liAddTop);

			// �׵θ�
			drawBorder($pdf, $top-$height*6-($liAddTop - 15), $liAddTop);

			if ($type == 's' || $type == 'c'){
				if ($pdf->height - $pdf->GetY() > 50){
				}else{
					// ������ �߰�
					$pdf->AddPage(strtoupper($page_pl), 'A4');
					$top = 33;
				}
			}

			// ������ ����
			$pdf->SetFillColor(220,220,220);

			if ($type == 's'){
				// ������
				$sql = "select distinct m02_yname, m02_ytel
						  from (
							   select $colMemCD1 as yoy
								 from t01iljung
								where t01_ccode  = '$code'
								  /*and t01_mkind  = '$kind'*/
								  and t01_jumin  = '$target'
								  and $colDT like '$date%' $family_sql
								  and t01_del_yn = 'N'
								union all
							   select $colMemCD2 as yoy
								 from t01iljung
								where t01_ccode  = '$code'
								  /*and t01_mkind  = '$kind'*/
								  and t01_jumin  = '$target'
								  and $colDT like '$date%' $family_sql
								  and t01_del_yn = 'N'
							   ) as y
						 inner join m02yoyangsa
							on m02_ccode = '$code'
						   /*and m02_mkind = '$kind'*/
						   and m02_yjumin = yoy
						 order by m02_yname";
				$conn->query($sql);
				$conn->fetch();
				$rowCount = $conn->row_count();

				$pdf->SetFont('����','',10);
				$pdf->SetXY($pdf->left, $top+2);

				$yoyInfo = '';

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);
					$yoyInfo .= ($yoyInfo != '' ? '  /  ' : '').$row[0].'('.$myF->phoneStyle($row[1],'.').')';
				}

				/***************************************************************************
					2012.08.24 ���ֿ�  ������½� �̺κ��� ������������ �Ѿ�� ���ļ� ����
				****************************************************************************/
				if ($pdf->GetY() < 50){
					$pdf->SetXY($pdf->left, $pdf->GetY()+27);
					$pdf->Cell($pdf->width, $height+2, " ��� : $yoyInfo", 1, 1, 'L', true);
				}else {
					$pdf->Cell($pdf->width, $height+2, " ��� : $yoyInfo", 1, 1, 'L', true);
				}

				$conn->row_free();
			}else{
				$pdf->SetY($top);
			}
		}else{
			if ($l == 0){
				$pdf->AddPage(strtoupper($page_pl), 'A4');
			}else{
				$pdf->SetY($pdf->GetY() + 3);
				//$pdf->Line($pdf->left, $pdf->GetY(), $pdf->left+$pdf->width*0.45, $pdf->GetY());
				//$pdf->Line($pdf->left+$pdf->width*0.55, $pdf->GetY(), $pdf->left+$pdf->width, $pdf->GetY());

				$w = $pdf->left;

				while(1){
					if ($w >= $pdf->left+$pdf->width) break;

					$pdf->Line($w, $pdf->GetY(), $w+1.5, $pdf->GetY());

					$w = $w + 3;
				}
			}

			$pdf->drawHeader();

			// ������ ����
			$pdf->SetFillColor(220,220,220);
			$height	= $pdf->rowHeight;

			$date = $pdf->year.$pdf->month;
			$lastDay = date('t', $calTime); //���ϼ� ���ϱ�
		}

		// �������񽺳���
		if ($type == 's'){
			$sql = '';

			foreach($svcParam as $i => $svc){
				$svc = explode('_', $svc);

				$sql .= (!empty($sql) ? ' union all ' : '');
				$sql .= 'select cast(date_format('.$colDT.', \'%d\') as signed) as dt
						 ,      date_format(concat('.$colDT.', '.$colFromTM.', \'00\'), \'%H:%i\') as f_time
						 ,      date_format(concat('.$colDT.', '.$colToTM.', \'00\'), \'%H:%i\') as t_time
						 ,      '.$colMemNM1.' as mem_nm1
						 ,      '.$colMemNM2.' as mem_nm2
						 ,      m01_suga_cont as suga_nm
						 ,      t01_svc_subcode as svc_cd
						 ,      '.$colSugaCD.' as suga_cd
						   from t01iljung
						  inner join (
								select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
								  from m01suga
								 where m01_mcode = \'goodeos\'
								 union all
								select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
								  from m11suga
								 where m11_mcode = \'goodeos\'
								 union all
								select service_code, concat(service_gbn, ifnull(service_lvl, \'\')), replace(service_from_dt,\'-\',\'\'), replace(service_to_dt,\'-\',\'\')
								  from suga_service
								 where org_no = \'goodeos\'
								) as suga
							 on '.$colSugaCD.' = m01_mcode2
							and '.$colDT.' between m01_sdate and m01_edate
						  where t01_ccode     = \''.$code.'\'
							and t01_jumin     = \''.$target.'\'
							and '.$colDT.' like \''.$date.'%\' '.$family_sql.'
							and t01_del_yn    = \'N\'';

				if ($code == '31141000043' /* ����� */)
					$sql .= ' and t01_bipay_umu != \'Y\'';

				if ($svc[0] != '')
					$sql .= ' and t01_mkind = \''.$svc[0].'\'';

				if ($svc[1] != '')
					$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';
			}

			$sql .= ' order by mem_nm1, mem_nm2, svc_cd, suga_cd, f_time, t_time, dt';


			/*********************************************************
			$sql = "select cast(date_format($colDT, '%d') as signed)
					,      date_format(concat($colDT, $colFromTM, '00'), '%H:%i')
					,      date_format(concat($colDT, $colToTM, '00'), '%H:%i')
					,      $colMemNM1
					,      $colMemNM2
					,      m01_suga_cont
					,      t01_svc_subcode
					  from t01iljung
					 inner join (
						   select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
							 from m01suga
							where m01_mcode = '$code'
							union all
						   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
							 from m11suga
							where m11_mcode = '$code'
							union all
						   select service_code, concat(service_gbn, ifnull(service_lvl, '')), replace(service_from_dt,'-',''), replace(service_to_dt,'-','')
							 from suga_service
							where org_no = '$code'
						   ) as suga
						on $colSugaCD = m01_mcode2
					   and $colDT between m01_sdate and m01_edate
					 where t01_ccode  = '$code'
					   and t01_jumin  = '$target'
					   and $colDT like '$date%' $family_sql
					   and t01_del_yn = 'N'";

			if ($code == '31141000043'){ //�����
				$sql .= " and t01_bipay_umu != 'Y'";
			}

			$sql .= " order by $colMemNM1, $colMemNM2, t01_svc_subcode, $colSugaCD, $colFromTM, $colToTM, $colDT";
			*********************************************************/





		}else if ($type == 'y'){
			$sql = '';

			foreach($svcParam as $i => $svc){
				$svc = explode('_', $svc);

				$sql .= (!empty($sql) ? ' union all ' : '');
				$sql .= 'select '.$colDT.' as dt
						 ,      date_format(concat('.$colDT.', '.$colFromTM.', \'00\'), \'%H:%i\') as f_time
						 ,      date_format(concat('.$colDT.', '.$colToTM.', \'00\'), \'%H:%i\') as t_time
						 ,      t01_jumin as c_cd
						 ,      case t01_svc_subcode when \'500\' then case '.$colMemCD1.' when \''.$target.'\' then \'(��)\' else \'(��)\' end else \'\' end as ms_gbn
						 ,      t01_svc_subcode as svc_cd
						 ,      '.$colSugaCD.' as suga_cd
						 ,      t01_ccode as code
						 ,      t01_mkind as kind
						   from t01iljung
						  where t01_ccode      = \''.$code.'\'
							and '.$colMemCD1.' = \''.$target.'\'
							and '.$colDT.'  like \''.$date.'%\' '.$family_sql.'
							and t01_del_yn     = \'N\'';

				if ($code == '31141000043' /* ����� */)
					$sql .= ' and t01_bipay_umu != \'Y\'';

				if ($svc[0] != '')
					$sql .= ' and t01_mkind = \''.$svc[0].'\'';

				if ($svc[1] != '')
					$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';


				$sql .= ' union all
						 select '.$colDT.' as dt
						 ,      date_format(concat('.$colDT.', '.$colFromTM.', \'00\'), \'%H:%i\') as f_time
						 ,      date_format(concat('.$colDT.', '.$colToTM.', \'00\'), \'%H:%i\') as t_time
						 ,      t01_jumin as c_cd
						 ,      case t01_svc_subcode when \'500\' then case '.$colMemCD1.' when \''.$target.'\' then \'(��)\' else \'(��)\' end else \'\' end as ms_gbn
						 ,      t01_svc_subcode as svc_cd
						 ,      '.$colSugaCD.' as suga_cd
						 ,      t01_ccode as code
						 ,      t01_mkind as kind
						   from t01iljung
						  where t01_ccode      = \''.$code.'\'
							and '.$colMemCD2.' = \''.$target.'\'
							and '.$colDT.'  like \''.$date.'%\' '.$family_sql.'
							and t01_del_yn     = \'N\'';

				if ($code == '31141000043' /* ����� */)
					$sql .= ' and t01_bipay_umu != \'Y\'';

				if ($svc[0] != '')
					$sql .= ' and t01_mkind = \''.$svc[0].'\'';

				if ($svc[1] != '')
					$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';
			}

			$sql = 'select cast(date_format(t.dt, \'%d\') as signed) dt, f_time, t_time, m03_name as c_nm, ms_gbn, suga_nm, svc_cd
					  from ('.$sql.') as t
					 inner join m03sugupja
						on m03_ccode = t.code
					   and m03_mkind = t.kind
					   and m03_jumin = t.c_cd
					 inner join (
						   select m01_mcode2 as suga_cd, m01_suga_cont as suga_nm, m01_sdate as suga_from, m01_edate as suag_to
							 from m01suga
							where m01_mcode = \'goodeos\'
							union all
						   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
							 from m11suga
							where m11_mcode = \'goodeos\'
							union all
						   select service_code, concat(service_gbn, ifnull(service_lvl, \'\')), replace(service_from_dt,\'-\',\'\'), replace(service_to_dt,\'-\',\'\')
							 from suga_service
							where org_no = \'goodeos\'
						   ) as suga
						on t.suga_cd = suga.suga_cd
					   and t.dt between suga.suga_from and suga.suag_to
					 order by c_nm, suga_nm, svc_cd, f_time, t_time, dt';

			/*********************************************************
			$sql = "select cast(date_format($colDT, '%d') as signed) as $colDT
					,      date_format(concat($colDT, $colFromTM, '00'), '%H:%i') as $colFromTM
					,      date_format(concat($colDT, $colToTM, '00'), '%H:%i') as $colToTM
					,      m03_name
					,      case t01_svc_subcode when '500' then case $colMemCD1 when '$target' then '(��)' else '(��)' end else '' end
					,      m01_suga_cont
					,      t01_svc_subcode
					  from t01iljung
					 inner join m03sugupja
						on m03_ccode = t01_ccode
					   and m03_mkind = t01_mkind
					   and m03_jumin = t01_jumin
					 inner join (
						   select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
							 from m01suga
							where m01_mcode = '$code'
							union all
						   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
							 from m11suga
							where m11_mcode = '$code'
							union all
						   select service_code, concat(service_gbn, ifnull(service_lvl, '')), replace(service_from_dt,'-',''), replace(service_to_dt,'-','')
							 from suga_service
							where org_no = '$code'
						   ) as suga
						on $colSugaCD = m01_mcode2
					   and $colDT between m01_sdate and m01_edate
					 where t01_ccode  = '$code'
					   and t01_mkind  = '$kind'
					   and $colMemCD1 = '$target'
					   and $colDT like '$date%' $family_sql
					   and t01_del_yn = 'N'
					 union all
					select cast(date_format($colDT, '%d') as signed) as $colDT
					,      date_format(concat($colDT, $colFromTM, '00'), '%H:%i') as $colFromTM
					,      date_format(concat($colDT, $colToTM, '00'), '%H:%i') as $colToTM
					,      m03_name
					,      case t01_svc_subcode when '500' then case $colMemCD1 when '$target' then '(��)' else '(��)' end else '' end
					,      m01_suga_cont
					,      t01_svc_subcode
					  from t01iljung
					 inner join m03sugupja
						on m03_ccode = t01_ccode
					   and m03_mkind = t01_mkind
					   and m03_jumin = t01_jumin
					 inner join (
						   select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
							 from m01suga
							where m01_mcode = '$code'
							union all
						   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
							 from m11suga
							where m11_mcode = '$code'
						   ) as suga
						on $colSugaCD = m01_mcode2
					   and $colDT between m01_sdate and m01_edate
					 where t01_ccode  = '$code'
					   and t01_mkind  = '$kind'
					   and $colMemCD2 = '$target'
					   and $colDT like '$date%' $family_sql
					   and t01_del_yn = 'N'
					 order by m03_name, t01_svc_subcode, $colFromTM, $colToTM, $colDT";
			*********************************************************/

		}else if ($type == 'c'){
			//�����ڿ屸���
			$sql = "select desire_status as stat
					,      desire_content as cont
					,      desire_service as svc
					,	   desire_subject1 as subject1
					,	   desire_subject2 as subject2
					,	   desire_subject3 as subject3
					  from counsel_client_desire
					 where org_no      = '$code'
					   and desire_ssn  = '$target'
					   and desire_yymm = '$date'";
		}else{
			$sql = "";
		}

		if ($type == 's' || $type == 'y'){
			$conn->query($sql);
			$conn->fetch();
			$rowCount = $conn->row_count();

			$pdf->SetFont('����','B',9);
			$pdf->SetXY($pdf->left, $pdf->GetY()+2);

			if ($type == 's'){
				if ($pdf->kind == '4'){
					$strType = '�����η¸�';
				}else{
					$strType = '��纸ȣ��';
				}
			}else{
				$strType = '������';
			}

			if ($svcDtlYn == 'Y'){
				$pdf->Cell($pdf->width*0.12, $height, "��������", 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.10, $height, $strType, 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.73, $height, ($svcDtlYn != 'Y' ? '��������/' : '')."������",	1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.05, $height, "Ƚ��", 1, 1, 'C', true);

				$tempDate = '';
				$seq = 0;

				unset($svc);

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					if ($type == 's'){
						$temp_data = $row[3].'_'.$row[4].'_'.$row[5];
					}else{
						$temp_data = $row[3].'_'.$row[5];
					}

					if ($tempData != $temp_data){
						$tempData  = $temp_data;

						$svc[$seq]['order']	= $row[6].'_'.$row[3].'_'.$row[5];
						$svc[$seq]['start']	= $row[1];
						$svc[$seq]['end']	= $row[2];
						$svc[$seq]['yoy1']	= $row[3];
						$svc[$seq]['yoy2']	= $row[4];
						$svc[$seq]['svc']	= $row[5];
						$svc[$seq]['count']	= 0;
						$svc[$seq]['days']	= '/';
						$seq ++;
					}
					$svc[$seq-1]['count'] ++;
					$svc[$seq-1]['days'] .= $row[0].'/';
				}

				$conn->row_free();

				$temp_svc = $myF->sortArray($svc, 'order', 1);
				$svc = $temp_svc;

				$height = 5;

				for($i=0; $i<sizeOf($svc); $i++){
					$pdf->SetTextColor(0,0,0);
					$pdf->SetX($pdf->left);


					if ($pdf->GetY()+$height > $pdf->height){
						$pdf->SetFont('����','B',9);
						$pdf->AddPage(strtoupper($page_pl), 'A4');
						$pdf->SetX($pdf->left);
						$pdf->Cell($pdf->width*0.12, $height, "��������",1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.10, $height, $type == 's' ? "��纸ȣ��" : "������",1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.73, $height, "������",1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.05, $height, "Ƚ��",1, 1, 'C', true);
						$pdf->SetX($pdf->left);
						$top = 33;
					}
					$pdf->SetFont('����','',9);
					$pdf->Cell($pdf->width*0.12, $height, $pdf->_splitTextWidth($myF->utf($svc[$i]['svc']),$pdf->width*0.12), 1, 0, 'L');
					$pdf->Cell($pdf->width*0.10, $height, $pdf->_splitTextWidth($myF->utf($svc[$i]['yoy1'].(!empty($svc[$i]['yoy2'])?'��1':'')),$pdf->width*0.10), 1,	0, 'L');

					for($j=1; $j<=31; $j++){
						if (strVal(strPos($svc[$i]['days'], "/$j/")) == ''){
							$pdf->SetTextColor(220,220,220);
						}else{
							$pdf->SetTextColor(0,0,0);
						}

						if ($j < 10){
							$cellWidth = $pdf->width*0.02;
						}else{
							$cellWidth = $pdf->width*0.025;
						}

						if ($j <= $lastDay){
							if ($j == 1){
								$pdf->Cell($cellWidth, $height, "$j", "LB", 0, 'C');
							}else{
								$pdf->Cell($cellWidth, $height, "$j", "B", 0, 'C');
							}
						}else{
							$pdf->Cell($cellWidth, $height, "", "B", 0, 'C');
						}
					}

					$pdf->SetTextColor(0,0,0);
					$pdf->Cell($pdf->width*0.05, $height, number_format($svc[$i]['count']),	1,	1, 'C');
				}
			}else{
				$pdf->Cell($pdf->width*0.1,	 $height, "�����ð�",			1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.12, $height, $strType,				1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.73, $height, "��������/������",	1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.05, $height, "Ƚ��",				1, 1, 'C', true);

				$tempDate = '';
				$seq = 0;

				unset($svc);

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					if ($type == 's'){
						$temp_data = $row[1].'_'.$row[2].'_'.$row[3].'_'.$row[4].'_'.$row[5];
					}else{
						$temp_data = $row[1].'_'.$row[2].'_'.$row[3];
					}

					if ($tempData != $temp_data){
						$tempData  = $temp_data;

						$svc[$seq]['order']	= $row[6].'_'.$row[1].'_'.$row[2].'_'.$row[3].'_'.$row[5];
						$svc[$seq]['start']	= $row[1];
						$svc[$seq]['end']	= $row[2];
						$svc[$seq]['yoy1']	= $row[3];
						$svc[$seq]['yoy2']	= $row[4];
						$svc[$seq]['svc']	= $row[5];
						$svc[$seq]['count']	= 0;
						$svc[$seq]['days']	= '/';
						$seq ++;
					}
					$svc[$seq-1]['count'] ++;
					$svc[$seq-1]['days'] .= $row[0].'/';
				}

				$conn->row_free();

				$temp_svc = $myF->sortArray($svc, 'order', 1);
				$svc = $temp_svc;

				$height = 5;

				for($i=0; $i<sizeOf($svc); $i++){
					$pdf->SetTextColor(0,0,0);
					$pdf->SetX($pdf->left);

					if ($pdf->GetY()+$height > $pdf->height){
						$pdf->AddPage(strtoupper($page_pl), 'A4');
						$pdf->SetX($pdf->left);
						$pdf->Cell($pdf->width*0.1,	$height, "�����ð�",1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.12,$height, $type == 's' ? "��纸ȣ��" : "������",1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.73,$height, "��������/������",1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.05,$height, "Ƚ��",1, 1, 'C', true);
						$pdf->SetX($pdf->left);
						$top = 33;
					}
					$pdf->SetFont('����','',9);
					$pdf->Cell($pdf->width*0.1,	$height,	$svc[$i]['start'],	"LTR",	0, 'L');

					$Y = $pdf->GetY()+$height;

					if ($type == 's'){
						if ($svc[$i]['yoy2'] == ''){
							$pdf->Cell($pdf->width*0.12,	$height*2,	$svc[$i]['yoy1'],	"LTR",	0, 'L');
						}else{
							$pdf->Cell($pdf->width*0.12,	$height,	$svc[$i]['yoy1'],	"LTR",	0, 'L');
						}
					}else{
						$pdf->Cell($pdf->width*0.12,	$height*2,	$svc[$i]['yoy1'].$svc[$i]['yoy2'],	"LTR",	0, 'L');
					}

					$pdf->Cell($pdf->width*0.73,	$height,	$svc[$i]['svc'],	"LTR",	0, 'L');
					$pdf->Cell($pdf->width*0.05,	$height*2,	number_format($svc[$i]['count']),	1,	1, 'C');
					$pdf->SetXY($pdf->left, $Y);
					$pdf->Cell($pdf->width*0.1,	$height,	'~'.$svc[$i]['end'],	"LBR",	0, 'L');

					if ($type == 's'){
						$pdf->Cell($pdf->width*0.12, $height, ($svc[$i]['yoy2'] != '' ? ', ' : '').$svc[$i]['yoy2'],	"LBR",	0, 'L');
					}else{
						$pdf->Cell($pdf->width*0.12, $height, "", "LBR",	0, 'L');
					}
					//$pdf->Cell(112,	$height,	$svc[$i]['days'],	"LBR",	1, 'L');

					for($j=1; $j<=31; $j++){
						if (strVal(strPos($svc[$i]['days'], "/$j/")) == ''){
							$pdf->SetTextColor(220,220,220);
						}else{
							$pdf->SetTextColor(0,0,0);
						}

						if ($j < 10){
							$cellWidth = $pdf->width*0.02;
						}else{
							$cellWidth = $pdf->width*0.025;
						}

						if ($j <= $lastDay){
							if ($j == 1){
								$pdf->Cell($cellWidth, $height, "$j", "LB", 0, 'C');
							}else if ($j == 31){
								$pdf->Cell($cellWidth, $height, "$j", "B", 1, 'C');
							}else{
								$pdf->Cell($cellWidth, $height, "$j", "B", 0, 'C');
							}
						}else{
							if ($j == 31){
								$pdf->Cell($cellWidth, $height, "", "B", 1, 'C');
							}else{
								$pdf->Cell($cellWidth, $height, "", "B", 0, 'C');
							}
						}
					}
				}
			}
		}else if ($type == 'c'){
			if ($code == '31141000005' || //����� �����ϰ� ������ ���
				$code == '31141000159' ){ //���κ�����������
				//�����ڿ屸���
				$counsel = $conn->get_array($sql);

				$pdf->SetTextColor(0,0,0);
				$pdf->SetXY($pdf->left, $pdf->GetY()+2);

				$pdf->Cell($pdf->width*0.29, $height, 'ǥ��������̿��ȹ', 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.29, $height, '����/�屸��', 1, 0, 'C', true);
				//$pdf->Cell($pdf->width*0.29, $height, '�����/������ �䱸����', 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.29, $height, '�޿�������ȹ', 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.13, $height, '��������', 1, 1, 'C', true);

				$coord_yy = $pdf->GetY();
				$body_ww  = $pdf->width*0.26;
				$tmp_yy = 0;
				$border_yy = 0;


				//$pdf->SetXY($pdf->left, $coord_yy);
				//$pdf->MultiCell($body_w, 5, $counsel['cont'], 1);
				$pdf->SetXY($pdf->left+($pdf->width*0.03), $coord_yy+1.5);
				$pdf->MultiCell($pdf->width * 0.29, 4.6, $counsel['subject1']);
				//$tmp_yy = setDesire($pdf, $pdf->left+($pdf->width*0.03), $coord_yy, $counsel['subject1']);

				if ($border_yy < $tmp_yy) $border_yy = $tmp_yy;

				//$pdf->MultiCell($body_w, 5, $counsel['cont'], 1);
				$pdf->SetXY($pdf->left+$body_ww+($pdf->width*0.03)*2, $coord_yy+1.5);
				$pdf->MultiCell($pdf->width * 0.29, 4.6, $counsel['subject2']);

				//$tmp_yy = setDesire($pdf, $pdf->left+$body_ww+($pdf->width*0.03)*2, $coord_yy, $counsel['subject2']);

				if ($border_yy < $tmp_yy) $border_yy = $tmp_yy;

				$pdf->SetXY($pdf->left+$body_ww*2+($pdf->width*0.03)*3, $coord_yy+1.5);
				$pdf->MultiCell($pdf->width * 0.29, 4.6, $counsel['subject3']);
				//$pdf->MultiCell($body_w, 5, $counsel['svc'], 1);
				//$tmp_yy = setDesire($pdf, $pdf->left+$body_ww*2+($pdf->width*0.03)*3, $coord_yy, $counsel['subject3']);

				$pdf->SetY($coord_yy+12);

				$coord_y = $pdf->GetY();
				$body_w  = $pdf->width*0.29;
				$tmp_y = 0;
				$border_y = 0;

				$pdf->SetXY($pdf->left, $coord_y);
				//$pdf->MultiCell($body_w, 5, $counsel['cont'], 1);
				$tmp_y = setDesire($pdf, $pdf->left, $coord_y, $counsel['cont']);

				if ($border_y < $tmp_y) $border_y = $tmp_y;

				$pdf->SetXY($pdf->left+$body_w, $coord_y);
				//$pdf->MultiCell($body_w, 5, $counsel['cont'], 1);
				$tmp_y = setDesire($pdf, $pdf->left+$body_w, $coord_y, $counsel['stat']);

				if ($border_y < $tmp_y) $border_y = $tmp_y;

				$pdf->SetXY($pdf->left+$body_w*2, $coord_y);
				//$pdf->MultiCell($body_w, 5, $counsel['svc'], 1);
				$tmp_y = setDesire($pdf, $pdf->left+$body_w*2, $coord_y, $counsel['svc']);

				####################################################
				#
				# �����ݾ�
				#
					/*
					$sql = "select $colSugaVL
							,      m03_bonin_yul
							  from t01iljung
							 inner join (
								   select m03_sdate, m03_edate, m03_bonin_yul
									 from m03sugupja
									where m03_ccode = '$code'
									  and m03_mkind = '$kind'
									  and m03_jumin = '$target'
									union all
								   select m31_sdate, m31_edate, m31_bonin_yul
									 from m31sugupja
									where m31_ccode = '$code'
									  and m31_mkind = '$kind'
									  and m31_jumin = '$target'
								   ) as sugupja
								on $colDT between m03_sdate and m03_edate
							 where t01_ccode = '$code'
							   and t01_mkind = '$kind'
							   and t01_jumin = '$target'
							   and $colDT like '$date%' $family_sql
							   and t01_del_yn = 'N'";
					  */
					  $sql = "select $colSugaVL
							  ,      rate
							  from t01iljung
							  left join (
									select jumin
									,      kind
									,      rate
									 from client_his_kind
									where org_no = '$code'
									  and date_format(from_dt,'%Y%m') <= '$year$month'
									  and date_format(to_dt,  '%Y%m') >= '$year$month'
									group by jumin
									) as kind
									on kind.jumin = t01_jumin
							 where t01_ccode = '$code'
							   and t01_mkind = '$kind'
							   and t01_jumin = '$target'
							   and left($colDT,6) = '$date' $family_sql
							   and t01_del_yn = 'N'";

					$conn->query($sql);
					$conn->fetch();

					$row_count = $conn->row_count();

					$tot_amt = 0;
					$my_amt  = 0;

					for($i=0; $i<$row_count; $i++){
						$row = $conn->select_row($i);

						$tot_amt += $row[$colSugaVL];
						$my_amt  += (($row[$colSugaVL] * $row['rate']) / 100);
					}

					$conn->row_free();
				#
				####################################################

				$pdf->SetXY($pdf->left+$body_w*3+1, $coord_yy+1);
				$pdf->MultiCell($body_w, 5, "-�����ݾ�\n   ".number_format($myF->cutOff($tot_amt))."��");

				$pdf->SetTextColor(0,0,255);

				$pdf->SetXY($pdf->left+$body_w*3+1, $pdf->GetY()+1);
				$pdf->MultiCell($body_w, 5, "-���κδ��\n   ".number_format($myF->cutOff($my_amt))."��");

				$pdf->SetTextColor(0,0,0);

				if ($border_yy < $tmp_yy) $border_yy = $tmp_yy;

				if ($border_yy < $coord_yy + 12) $border_yy = $coord_yy + 12;
				if ($border_yy > $pdf->height) $border_yy = $pdf->height;

				$pdf->Text($pdf->left+1.3, $coord_yy+4, '��');
				$pdf->Text($pdf->left+1.3, $coord_yy+10, 'ǥ');
				$pdf->Text($pdf->left+$body_ww+$pdf->width*0.03+1.3, $coord_yy+4, '��');
				$pdf->Text($pdf->left+$body_ww+$pdf->width*0.03+1.3, $coord_yy+10, '��');
				$pdf->Text($pdf->left+$body_ww*2+($pdf->width*0.03*2)+1.3, $coord_yy+4, '��');
				$pdf->Text($pdf->left+$body_ww*2+($pdf->width*0.03*2)+1.3, $coord_yy+10, 'ǥ');
				$pdf->Line($pdf->left, $coord_yy, $pdf->left, $border_yy);
				$pdf->Line($pdf->left+($pdf->width*0.03), $coord_yy, $pdf->left+($pdf->width*0.03), $border_yy);
				$pdf->Line($pdf->left+$body_ww+$pdf->width*0.03, $coord_yy, $pdf->left+$body_ww+($pdf->width*0.03), $border_yy);
				$pdf->Line($pdf->left+$body_ww+($pdf->width*0.03)*2, $coord_yy, $pdf->left+$body_ww+($pdf->width*0.03)*2, $border_yy);
				$pdf->Line($pdf->left+$body_ww*2+($pdf->width*0.03)*2, $coord_yy, $pdf->left+$body_ww*2+($pdf->width*0.03)*2, $border_yy);
				$pdf->Line($pdf->left+$body_ww*2+($pdf->width*0.03)*3, $coord_yy, $pdf->left+$body_ww*2+($pdf->width*0.03)*3, $border_yy);
				$pdf->Line($pdf->left+$body_ww*3+($pdf->width*0.03)*3, $coord_yy, $pdf->left+$body_ww*3+($pdf->width*0.03)*3, $border_yy);
				$pdf->Line($pdf->left+$body_ww*3+($pdf->width*0.03)*3+25.5, $coord_yy, $pdf->left+$body_ww*3+($pdf->width*0.03)*3+25.5, $border_yy);
				//$pdf->Rect($pdf->left, $coord_yy, $pdf->width, $border_yy - $coord_yy);

				if ($border_y < $tmp_y) $border_y = $tmp_y;

				if ($border_y < $coord_y + 25) $border_y = $coord_y + 25;
				if ($border_y > $pdf->height) $border_y = $pdf->height;

				$pdf->Line($pdf->left, $coord_y, $pdf->left, $border_y);
				$pdf->Line($pdf->left+$body_w, $coord_y, $pdf->left+$body_w, $border_y);
				$pdf->Line($pdf->left+$body_w*2, $coord_y, $pdf->left+$body_w*2, $border_y);
				$pdf->Line($pdf->left+$body_w*3, $coord_y, $pdf->left+$body_w*3, $border_y);
				$pdf->Line($pdf->left+$body_w*3+25.5, $coord_y, $pdf->left+$body_w*3+25.5, $border_y);
				$pdf->Line($pdf->left, $coord_y, $pdf->left+$body_w*3, $coord_y);
				$pdf->Line($pdf->left, $border_y, $pdf->width+($pdf->width*0.03), $border_y);
				//$pdf->Rect($pdf->left, $coord_y, $pdf->width, $border_y - $coord_y);
			}else {
				//�����ڿ屸���
				$counsel = $conn->get_array($sql);

				$pdf->SetTextColor(0,0,0);
				$pdf->SetXY($pdf->left, $pdf->GetY()+2);

				$pdf->Cell($pdf->width*0.29, $height, '������ ������/�屸��', 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.29, $height, 'ǥ������� �ʿ䳻��', 1, 0, 'C', true);
				//$pdf->Cell($pdf->width*0.29, $height, '�����/������ �䱸����', 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.29, $height, '��纸ȣ�� ���񽺳���', 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.13, $height, '��������', 1, 1, 'C', true);

				$coord_y = $pdf->GetY();
				$body_w  = $pdf->width*0.29;
				$tmp_y = 0;
				$border_y = 0;

				$pdf->SetXY($pdf->left, $coord_y);
				//$pdf->MultiCell($body_w, 5, $counsel['cont'], 1);
				$tmp_y = setDesire($pdf, $pdf->left, $coord_y, $counsel['stat']);

				if ($border_y < $tmp_y) $border_y = $tmp_y;

				$pdf->SetXY($pdf->left+$body_w, $coord_y);
				//$pdf->MultiCell($body_w, 5, $counsel['cont'], 1);
				$tmp_y = setDesire($pdf, $pdf->left+$body_w, $coord_y, $counsel['cont']);

				if ($border_y < $tmp_y) $border_y = $tmp_y;

				$pdf->SetXY($pdf->left+$body_w*2, $coord_y);
				//$pdf->MultiCell($body_w, 5, $counsel['svc'], 1);
				$tmp_y = setDesire($pdf, $pdf->left+$body_w*2, $coord_y, $counsel['svc']);

				####################################################
				#
				# �����ݾ�
				#
					/*
					$sql = "select $colSugaVL
							,      m03_bonin_yul
							  from t01iljung
							 inner join (
								   select m03_sdate, m03_edate, m03_bonin_yul
									 from m03sugupja
									where m03_ccode = '$code'
									  and m03_mkind = '$kind'
									  and m03_jumin = '$target'
									union all
								   select m31_sdate, m31_edate, m31_bonin_yul
									 from m31sugupja
									where m31_ccode = '$code'
									  and m31_mkind = '$kind'
									  and m31_jumin = '$target'
								   ) as sugupja
								on $colDT between m03_sdate and m03_edate
							 where t01_ccode = '$code'
							   and t01_mkind = '$kind'
							   and t01_jumin = '$target'
							   and $colDT like '$date%' $family_sql
							   and t01_del_yn = 'N'";
					  */
					  $sql = "select $colSugaVL
							,      rate
							  from t01iljung
							 inner join (
									select jumin
									,      kind
									,      rate
									,		replace(from_dt,'-','') as from_dt
									,		replace(to_dt,'-','') as to_dt
									 from client_his_kind
									where org_no = '$code'
									  and date_format(from_dt,'%Y%m') <= '$year$month'
									  and date_format(to_dt,  '%Y%m') >= '$year$month'
									) as kind
									on kind.jumin = t01_jumin
							 where t01_ccode = '$code'
							   and t01_mkind = '$kind'
							   and t01_jumin = '$target'
							   and $colDT like '$date%'
							   and $colDT between kind.from_dt and kind.to_dt $family_sql
							   and t01_del_yn = 'N'";

					$conn->query($sql);
					$conn->fetch();

					$row_count = $conn->row_count();

					$tot_amt = 0;
					$my_amt  = 0;

					for($i=0; $i<$row_count; $i++){
						$row = $conn->select_row($i);

						$tot_amt += $row[$colSugaVL];
						$my_amt  += (($row[$colSugaVL] * $row['rate']) / 100);
					}

					$conn->row_free();
				#
				####################################################

				$pdf->SetXY($pdf->left+$body_w*3+1, $coord_y+1);
				$pdf->MultiCell($body_w, 5, "-�����ݾ�\n   ".number_format($myF->cutOff($tot_amt))."��");

				$pdf->SetTextColor(0,0,255);

				$pdf->SetXY($pdf->left+$body_w*3+1, $pdf->GetY()+1);
				$pdf->MultiCell($body_w, 5, "-���κδ��\n   ".number_format($myF->cutOff($my_amt))."��");

				$pdf->SetTextColor(0,0,0);

				if ($border_y < $tmp_y) $border_y = $tmp_y;

				if ($border_y < $coord_y + 25) $border_y = $coord_y + 25;
				if ($border_y > $pdf->height) $border_y = $pdf->height;

				$pdf->Line($pdf->left+$body_w, $coord_y, $pdf->left+$body_w, $border_y);
				$pdf->Line($pdf->left+$body_w*2, $coord_y, $pdf->left+$body_w*2, $border_y);
				$pdf->Line($pdf->left+$body_w*3, $coord_y, $pdf->left+$body_w*3, $border_y);
				$pdf->Rect($pdf->left, $coord_y, $pdf->width, $border_y - $coord_y);
			}

			###################################################
			#
			# �����ڿ� ��纸ȣ�� ���
				$sql = "select m03_name, m03_yoyangsa1_nm
						  from m03sugupja
						 where m03_ccode = '$code'
						   and m03_mkind = '$kind'
						   and m03_jumin = '$target'";

				$client = $conn->get_array($sql);
			#
			###################################################

			$pdf->set_font(11);
			$pdf->SetXY($pdf->left, $border_y + 1);

			if ($pdf->GetY()+10 > $pdf->height){
				$pdf->AddPage(strtoupper($page_pl), 'A4');
				$pdf->SetX($pdf->left);
			}

			if ($code == '31141000005' || //����� �����ϰ� ������ ���
				$code == '31141000159' ){ //���κ�����������

				$str  = "- ��� ���� �޿�������ȹ�� ������(��ȣ��) ".$pdf->name."�� �䱸�� �ٰŷ� ǥ��������̿��ȹ �� ���(                  )�� ������ ��ȹ�ð� �Ϻθ� ����(����ȹ) �Ͽ����Ƿ� �̿� ���ǽ� ���߱޿��� �����ϰڽ��ϴ�.\n";
				$str .= "- �׸��� �� �ѵ����� �ʰ��Ͽ� ���񽺸� �̿��� ��� �ʰ��� �ݾ��� ���� ������ ������ �δ��Ͽ��� �մϴ�.";
				$pdf->MultiCell($pdf->width, 5, $str);

			}else{
				$pdf->Cell($pdf->width, $pdf->rowHeight, '- ��� ��ȹ�� �����մϴ�.', 0, 1);
			}


			$pdf->SetXY($pdf->left, $pdf->getY()+10);
			$pdf->Cell($pdf->width * 0.5, $pdf->rowHeight, '- ������(��ȣ��) : '.$client[0].'   ��', 0, 0);
			$pdf->Cell($pdf->width * 0.5, $pdf->rowHeight, '- ��纸ȣ�� : '.($client[1] != '' ? $client[1] : '            ').'   ��', 0, 1, 'R');
		}

		$pdf->SetTextColor(0,0,0);

		if ($useType == 'y'){
			// �����ڿ� ��ºκ�
			//if ($pdf->GetY()+$height> $pdf->height){
			//	$pdf->AddPage(strtoupper($page_pl), 'A4');
			//	$top = 35;
			//}else{
			//	$top = $pdf->GetY()+2;
			//}

			if ($type == 's'){
				// ������
				/*********************************************************
				$pdf->SetXY($pdf->left, $top);
				$pdf->Cell($pdf->width*0.12,	$height, "�޿�����",		 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.215,	$height, "����(���񽺸�)", 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.085,	$height, "Ƚ��",			 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.130,	$height, "�ð�",			 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.150,	$height, "����",			 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.150,	$height, "�ѱ޿����",		 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.150,	$height, "���κδ��",		 1, 1, 'C', true);
				 *********************************************************/
				//$pdf->SetXY($pdf->left, $top);
				$pdf->SetX($pdf->left);
				$pdf->Cell($pdf->width*0.08,	$height, "�޿�����", 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.15,	$height, "����(���񽺸�)", 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.05,	$height, "Ƚ��", 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.09,	$height, "�ð�", 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.09,	$height, "����", 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.09,	$height, "�ѱݾ�", 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.09,	$height, "�޿��Ѿ�", 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.09,	$height, "���κδ�", 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.09,	$height, "�ʰ�", 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.09,	$height, "��޿�", 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.09,	$height, "�Ѻδ��", 1, 1, 'C', true);

				if ($lbTestMode){
					//���κδ���
					$sql = 'select kind
							,      rate
							,      date_format(from_dt,\'%Y%m%d\') as from_dt
							,      date_format(to_dt,  \'%Y%m%d\') as to_dt
							  from client_his_kind
							 where org_no = \''.$code.'\'
							   and jumin  = \''.$target.'\'
							   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
							   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';

					$laClientKind = $conn->_fetch_array($sql);

					for($i=1; $i<=31; $i++){
						if (is_array($laClientKind)){
							foreach($laClientKind as $laRow){
								if ($laRow['from_dt'] <= $year.$month.($i<10?'0':'').$i &&
									$laRow['to_dt']   >= $year.$month.($i<10?'0':'').$i){
									$liRate[$i] = $laRow['rate'];
									break;
								}
							}
						}else{
							$liRate[$i] = 100;
						}
					}


					$sql = '';

					foreach($svcParam as $i => $svc){
						$svc = explode('_', $svc);

						$sql .= (!empty($sql) ? ' union all ' : '');
						$sql .= 'select t01_mkind as kind
								 ,      t01_jumin as jumin
								 ,      t01_sugup_date as dt
								 ,      t01_svc_subcode as svc_cd
								 ,      case when t01_svc_subcode = \'200\' then case t01_mkind when \'0\' then \'�湮���\' else \'Ȱ������\' end
											 when t01_svc_subcode = \'500\' then \'�湮���\'
											 when t01_svc_subcode = \'800\' then \'�湮��ȣ\'
											 when t01_svc_subcode > \'20\' and t01_svc_subcode < \'30\' then \'�ٿ�ó\'
											 when t01_svc_subcode > \'30\' and t01_svc_subcode < \'40\' then \'��Ÿ����\' else \'-\' end as svc_nm
								 ,      t01_suga as suga_pay
								 ,      '.$colSugaCD.' as suga_cd
								 ,      '.$colSugaVL.' as suga_val
								 ,	    '.$colSoyoTM.' as soyotime
								 ,      TIMEDIFF(DATE_FORMAT(CONCAT(t01_sugup_date, t01_sugup_totime,\'00\'),\'%Y-%m-%d %H:%i:%s\')
												,DATE_FORMAT(CONCAT(t01_sugup_date, t01_sugup_fmtime,\'00\'),\'%Y-%m-%d %H:%i:%s\')) as proctime
								 ,      t01_bipay_umu as bipay_yn
								 ,      case cast(date_format(t01_sugup_date,\'%d\') as unsigned)';

						for($i=1; $i<=31; $i++){
							if (empty($liRate[$i])) $liRate[$i] = 0;
							$sql .= ' when '.$i.' then '.$liRate[$i];
						}

						$sql .= ' else 0 end as rate';

						$sql .= '  from t01iljung as iljung
								  where t01_ccode  = \''.$code.'\'
									and t01_jumin  = \''.$target.'\'
									and t01_del_yn = \'N\'
									and left('.$colDT.','.strlen($date).') = \''.$date.'\' '.$family_sql;

						if ($code == '31141000043' /* ����� */)
							$sql .= ' and t01_bipay_umu != \'Y\'';

						if ($svc[0] != '')
							$sql .= ' and t01_mkind = \''.$svc[0].'\'';

						if ($svc[1] != '')
							$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';
					}

					/*********************************************************
					$sql = 'select svc_nm
							,      suga_nm
							,      sum(cnt) as cnt
							,      suga_pay
							,      sum(val) as val
							,      sum(expenses) as expenses
							,      soyotime
							  from (
									select t.svc_cd as svc_cd
									,      t.svc_nm as svc_nm
									,      s.suga_nm as suga_nm
									,      count(t.suga_cd) as cnt
									,      t.suga_pay as suga_pay
									,      sum(t.suga_val) as val
									,      case when t.bipay_yn = \'Y\' then sum(t.suga_val) else (t.suga_val * t.rate / 100) * count(t.suga_cd) end as expenses
									,      case when t.kind = \'0\' and t.soyotime > 240 then case when t.soyotime > 510 then 510 else t.soyotime end - 30 else t.soyotime end as soyotime
									  from ('.$sql.') as t

									inner join (
										  select \'0\' as kind, m01_mcode2 as suga_cd, m01_suga_cont as suga_nm, m01_sdate as from_dt, m01_edate as to_dt
											from m01suga
										   where m01_mcode = \''.$code.'\'
										   union all
										  select \'0\' as m01_mkind, m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
											from m11suga
										   where m11_mcode = \''.$code.'\'
										   union all
										  select service_kind, service_code, concat(service_gbn, ifnull(service_lvl, \'\')), replace(service_from_dt,\'-\',\'\'), replace(service_to_dt,\'-\',\'\')
											from suga_service
										   where org_no = \''.$code.'\'
										  ) as s
									   on t.suga_cd = s.suga_cd
									  and t.kind = s.kind
									  and t.dt between s.from_dt and s.to_dt
									group by t.svc_nm, s.suga_nm, t.suga_val, t.rate, t.soyotime
								  ) as t
							group by svc_nm, suga_nm, suga_pay, soyotime
							order by svc_cd';
					 *********************************************************/
					$sql = 'select t.kind as kind
							,      t.dt as dt
							,      t.svc_cd as svc_cd
							,      t.svc_nm as svc_nm
							,      t.suga_cd as suga_cd
							,      s.suga_nm as suga_nm
							,      1 as cnt
							,      t.suga_pay as suga_pay
							,      t.suga_val as val
							,      t.bipay_yn as bipay_yn
							,      t.rate as rate
							,      case when t.kind = \'0\' and t.soyotime > 240 then case when t.soyotime > 510 then 510 else t.soyotime end - 30 else t.soyotime end as soyotime /*t.soyotime*/
							,      case when t.kind = \'0\' and HOUR(proctime) * 60 + MINUTE(proctime) > 240 then case when HOUR(proctime) * 60 + MINUTE(proctime) > 510 then 510 else HOUR(proctime) * 60 + MINUTE(proctime) end - 30 else HOUR(proctime) * 60 + MINUTE(proctime) end as proctime
							  from ('.$sql.') as t

							inner join (
								  select \'0\' as kind, m01_mcode2 as suga_cd, m01_suga_cont as suga_nm, m01_sdate as from_dt, m01_edate as to_dt
									from m01suga
								   where m01_mcode = \'goodeos\'
								   union all
								  select \'0\' as m01_mkind, m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
									from m11suga
								   where m11_mcode = \'goodeos\'
								   union all
								  select service_kind, service_code, concat(service_gbn, ifnull(service_lvl, \'\')), replace(service_from_dt,\'-\',\'\'), replace(service_to_dt,\'-\',\'\')
									from suga_service
								   where org_no = \'goodeos\'
								  ) as s
							   on t.suga_cd = s.suga_cd
							  and t.kind = s.kind
							  and t.dt between s.from_dt and s.to_dt
							order by dt';

					//�ѵ��ݾ�
					$liLimitPay = $laClaimPay[$target]['amt'];

					if (empty($liLimitPay)){
						$liLimitPay = $laLimitPay[$pdf->lvlCd]['amt'];
					}

					//if($debug) echo nl2br($sql);
					$conn->query($sql);
					$conn->fetch();

					$rowCount = $conn->row_count();

					$liTotalPay = 0;

					for($i=0; $i<$rowCount; $i++){
						$row = $conn->select_row($i);

						if ($row['kind'] == '0'){
							$lsKey = $row['suga_cd'];
						}else{
							$lsKey = $row['suga_cd'].'_'.$row['proctime'];
						}

						if (!IsSet($laSvc[$row['svc_cd']][$lsKey])){
							if ($row['svc_cd'] == '500'){
								$row['proctime'] = $row['proctime'] > 60 ? 60 : $row['proctime'];
							}
							$laSvc[$row['svc_cd']][$lsKey]['kind']     = $row['kind'];
							$laSvc[$row['svc_cd']][$lsKey]['svcNm']    = $row['svc_nm'];
							$laSvc[$row['svc_cd']][$lsKey]['sugaNm']   = $row['suga_nm'];
							$laSvc[$row['svc_cd']][$lsKey]['sugaCost'] = $row['suga_pay'];
							$laSvc[$row['svc_cd']][$lsKey]['time']     = $row['proctime'];
							$laSvc[$row['svc_cd']][$lsKey]['expense']  = 0;
							$laSvc[$row['svc_cd']][$lsKey]['cnt']      = 0;
							$laSvc[$row['svc_cd']][$lsKey]['pay']      = 0;
							$laSvc[$row['svc_cd']][$lsKey]['bipay']    = 0;
							$laSvc[$row['svc_cd']][$lsKey]['over']     = 0;
							$laSvc[$row['svc_cd']][$lsKey]['total']    = 0;
						}

						if ($row['bipay_yn'] != 'Y'){
							//�޿�
							if ($liTotalPay + $row['val'] >= $liLimitPay){
								$liVal = $liLimitPay - $liTotalPay;

								if ($liVal > 0){
									$laSvc[$row['svc_cd']][$lsKey]['over']    += $row['val'] - $liVal;
									$laSvc[$row['svc_cd']][$lsKey]['pay']     += $liVal;
									$laSvc[$row['svc_cd']][$lsKey]['expense'] += ($liVal * $row['rate'] / 100);
								}else{
									$laSvc[$row['svc_cd']][$lsKey]['over'] += $row['val'];
								}
							}else{
								$laSvc[$row['svc_cd']][$lsKey]['pay']     += $row['val'];
								$laSvc[$row['svc_cd']][$lsKey]['expense'] += ($row['val'] * $row['rate'] / 100);
							}
							$liTotalPay += $row['val'];
						}else{
							//��޿�
							$laSvc[$row['svc_cd']][$lsKey]['bipay'] += $row['val'];
						}

						//$liTotalPay += $row['val'];
						$laSvc[$row['svc_cd']][$lsKey]['total'] += $row['val'];

						if ($row['kind'] == '0'){
							if ($row['svc_cd'] == '200'){
								//$laSvc[$row['svc_cd']][$lsKey]['timeTot'] += ($row['soyotime'] >= 270 ? $row['soyotime'] - 30 : $row['soyotime']);
								$laSvc[$row['svc_cd']][$lsKey]['timeTot'] += $row['soyotime'];
							}else if ($row['svc_cd'] == '500'){
								$laSvc[$row['svc_cd']][$lsKey]['timeTot'] += ($row['soyotime'] > 60 ? 60 : $row['soyotime']);
							}else{
								$laSvc[$row['svc_cd']][$lsKey]['timeTot'] += $row['soyotime'];
							}
						}else{
							if ($row['svc_cd'] == '500'){
								$laSvc[$row['svc_cd']][$lsKey]['timeTot'] += ($row['soyotime'] > 60 ? 60 : $row['soyotime']);
							}else{
								$laSvc[$row['svc_cd']][$lsKey]['timeTot'] += $row['soyotime'];
							}
						}

						$laSvc[$row['svc_cd']][$lsKey]['cnt'] ++;
					}

					$conn->row_free();
				}else{
					$sql = '';

					foreach($svcParam as $i => $svc){
						$svc = explode('_', $svc);

						$sql .= (!empty($sql) ? ' union all ' : '');
						$sql .= 'select t01_mkind as kind
								 ,      t01_sugup_date as dt
								 ,      t01_svc_subcode as svc_cd
								 ,      case when t01_svc_subcode = \'200\' then case t01_mkind when \'0\' then \'�湮���\' else \'Ȱ������\' end
											 when t01_svc_subcode = \'500\' then \'�湮���\'
											 when t01_svc_subcode = \'800\' then \'�湮��ȣ\'
											 when t01_svc_subcode > \'20\' and t01_svc_subcode < \'30\' then \'�ٿ�ó\'
											 when t01_svc_subcode > \'30\' and t01_svc_subcode < \'40\' then \'��Ÿ����\' else \'-\' end as svc_nm
								 ,      t01_suga as suga_pay
								 ,      '.$colSugaCD.' as suga_cd
								 ,      '.$colSugaVL.' as suga_val
								 ,	    '.$colSoyoTM.' as soyotime
								 ,      TIMEDIFF(DATE_FORMAT(CONCAT(t01_sugup_date, t01_sugup_totime,\'00\'),\'%Y-%m-%d %H:%i:%s\')
												,DATE_FORMAT(CONCAT(t01_sugup_date, t01_sugup_fmtime,\'00\'),\'%Y-%m-%d %H:%i:%s\')) as proctime
								 ,      t01_bipay_umu as bipay_yn
								   from t01iljung
								  where t01_ccode  = \''.$code.'\'
									and t01_jumin  = \''.$target.'\'
									and t01_del_yn = \'N\'
									and left('.$colDT.','.strlen($date).') = \''.$date.'\' '.$family_sql;

						if ($code == '31141000043' /* ����� */)
							$sql .= ' and t01_bipay_umu != \'Y\'';

						if ($svc[0] != '')
							$sql .= ' and t01_mkind = \''.$svc[0].'\'';

						if ($svc[1] != '')
							$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';
					}

					$sql = 'select t.svc_nm
							,      s.suga_nm
							,      count(t.suga_cd) as cnt
							,      t.suga_pay
							,      sum(t.suga_val) as val
							,      case when t.bipay_yn = \'Y\' then sum(t.suga_val) else (t.suga_val * c.rate / 100) * count(t.suga_cd) end as expenses
							,      case when t.kind = \'0\' and t.soyotime > 240 then case when t.soyotime > 510 then 510 else t.soyotime end - 30 else t.soyotime end as soyotime /*t.soyotime*/
							,      HOUR(proctime) * 60 + MINUTE(proctime) as proctime
							  from ('.$sql.') as t


							 inner join (
								   select m03_mkind as kind, m03_sdate as from_dt, m03_edate as to_dt, m03_bonin_yul as rate
									 from m03sugupja
									where m03_ccode = \''.$code.'\'
									  and m03_jumin = \''.$target.'\'
									union all
								   select m31_mkind, m31_sdate, m31_edate, m31_bonin_yul
									 from m31sugupja
									where m31_ccode = \''.$code.'\'
									 and m31_jumin = \''.$target.'\'
								   ) as c
								on t.kind = c.kind
							   and t.dt between c.from_dt and c.to_dt


							inner join (
								  select \'0\' as kind, m01_mcode2 as suga_cd, m01_suga_cont as suga_nm, m01_sdate as from_dt, m01_edate as to_dt
									from m01suga
								   where m01_mcode = \'goodeos\'
								   union all
								  select \'0\' as m01_mkind, m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
									from m11suga
								   where m11_mcode = \'goodeos\'
								   union all
								  select service_kind, service_code, concat(service_gbn, ifnull(service_lvl, \'\')), replace(service_from_dt,\'-\',\'\'), replace(service_to_dt,\'-\',\'\')
									from suga_service
								   where org_no = \'goodeos\'
								  ) as s
							   on t.suga_cd = s.suga_cd
							  and t.kind = s.kind
							  and t.dt between s.from_dt and s.to_dt
							group by t.svc_nm, s.suga_nm, t.suga_val, t.soyotime
							order by t.svc_cd';
				}


				/*********************************************************
				$sql = "select case when t01_svc_subcode = '200' then '�湮���'
							when t01_svc_subcode = '500' then '�湮���'
							when t01_svc_subcode = '800' then '�湮��ȣ'
							when t01_svc_subcode > '20' and t01_svc_subcode < '30' then '�ٿ�ó'
							when t01_svc_subcode > '30' and t01_svc_subcode < '40' then '��Ÿ����'
							else '-' end
				,      m01_suga_cont
				,      count($colSugaCD)
				,      $colSugaVL
				,      sum($colSugaVL)
				,      case when t01_bipay_umu = 'Y' then sum($colSugaVL) else ($colSugaVL * m03_bonin_yul / 100) * count($colSugaCD) end
				,	   $colSoyoTM
				  from t01iljung
				 inner join (
					   select m03_mkind, m03_sdate, m03_edate, m03_bonin_yul
						 from m03sugupja
						where m03_ccode = '$code'
						  and m03_jumin = '$target'
						union all
					   select m31_mkind, m31_sdate, m31_edate, m31_bonin_yul
						 from m31sugupja
						where m31_ccode = '$code'
						  and m31_jumin = '$target'
					   ) as sugupja
					on t01_mkind = m03_mkind
				   and $colDT between m03_sdate and m03_edate
				 inner join (
					   select '0' as m01_mkind, m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
						 from m01suga
						where m01_mcode = '$code'
						union all
					   select '0' as m01_mkind, m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
						 from m11suga
						where m11_mcode = '$code'
						union all
					   select service_kind, service_code, concat(service_gbn, ifnull(service_lvl, '')), replace(service_from_dt,'-',''), replace(service_to_dt,'-','')
						 from suga_service
						where org_no = '$code'
					   ) as suga
					on $colSugaCD = m01_mcode2
				   and t01_mkind = m01_mkind
				   and $colDT between m01_sdate and m01_edate
				 where t01_ccode = '$code'
				   and t01_jumin = '$target'
				   and $colDT like '$date%' $family_sql
				   and t01_del_yn = 'N'";

				if ($code == '31141000043'){ //�����
					$sql .= " and t01_bipay_umu != 'Y'";
				}

				$sql .= " group by t01_svc_subcode, m01_suga_cont, $colSugaCD, $colSugaVL
						  order by t01_svc_subcode";
				*********************************************************/

				/*********************************************************
				$conn->query($sql);
				$conn->fetch();
				$rowCount = $conn->row_count();

				$suga	= 0;
				$total	= 0;
				$bonin	= 0;

				$totSugaCnt  = 0;
				$totSugaTime = 0;

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					if ($pdf->GetY()+$height > $pdf->height){
						$pdf->AddPage(strtoupper($page_pl), 'A4');
						$pdf->SetX($pdf->left);
						$pdf->Cell($pdf->width*0.12,	$height, "�޿�����",		 1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.215,	$height, "����(���񽺸�)", 1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.085,	$height, "Ƚ��",			 1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.130,	$height, "�ð�",			 1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.150,	$height, "����",			 1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.150,	$height, "�ѱ޿����",		 1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.150,	$height, "���κδ��",		 1, 1, 'C', true);
					}

					$sugaTime = $myF->euckr($myF->_min2timeKor($row[6]));

					$pdf->SetX($pdf->left);
					$pdf->Cell($pdf->width*0.12,	$height, $row[0],	1, 0, 'C');
					$pdf->Cell($pdf->width*0.215,	$height, $row[1],	1, 0, 'L');
					$pdf->Cell($pdf->width*0.085,	$height, $row[2],	1, 0, 'R');
					$pdf->Cell($pdf->width*0.130,	$height, $sugaTime,	1, 0, 'R');
					$pdf->Cell($pdf->width*0.150,	$height, number_format($row[3]),	1, 0, 'R');
					$pdf->Cell($pdf->width*0.150,	$height, number_format($row[4]),	1, 0, 'R');
					$pdf->Cell($pdf->width*0.150,	$height, number_format($row[5]),	1, 1, 'R');

					$suga  += $row[3];
					$total += $row[4];
					$bonin += $row[5];

					$totSugaCnt  += $row[2];
					$totSugaTime += ($row[2] * $row[6]);
				}

				$pdf->SetX($pdf->left);
				$pdf->Cell($pdf->width*0.12,	$height, "��      ��",	1, 0, 'C');
				$pdf->Cell($pdf->width*0.215,	$height, "",	1, 0, 'C');
				$pdf->Cell($pdf->width*0.085,	$height, number_format($totSugaCnt),	1, 0, 'R');
				$pdf->Cell($pdf->width*0.130,	$height, $myF->euckr($myF->_min2timeKor($totSugaTime)),	1, 0, 'R');
				$pdf->Cell($pdf->width*0.150,	$height, number_format($suga),	1, 0, 'R');
				$pdf->Cell($pdf->width*0.150,	$height, number_format($total),	1, 0, 'R');
				$pdf->Cell($pdf->width*0.150,	$height, number_format($myF->cutOff($bonin)),	1, 1, 'R');

				$conn->row_free();
				 *********************************************************/
				if (is_array($laSvc)){
					foreach($laSvc as $loSvc){
						if (is_array($loSvc)){
							foreach($loSvc as $row){
								if ($pdf->GetY()+$height > $pdf->height){
									$pdf->AddPage(strtoupper($page_pl), 'A4');
									//$top = 35;
									$pdf->SetX($pdf->left);
									$pdf->Cell($pdf->width*0.08, $height, "�޿�����", 1, 0, 'C', true);
									$pdf->Cell($pdf->width*0.15, $height, "����(���񽺸�)", 1, 0, 'C', true);
									$pdf->Cell($pdf->width*0.05, $height, "Ƚ��", 1, 0, 'C', true);
									$pdf->Cell($pdf->width*0.09, $height, "�ð�", 1, 0, 'C', true);
									$pdf->Cell($pdf->width*0.09, $height, "����", 1, 0, 'C', true);
									$pdf->Cell($pdf->width*0.09, $height, "�ѱݾ�", 1, 0, 'C', true);
									$pdf->Cell($pdf->width*0.09, $height, "�޿��Ѿ�", 1, 0, 'C', true);
									$pdf->Cell($pdf->width*0.09, $height, "���κδ�", 1, 0, 'C', true);
									$pdf->Cell($pdf->width*0.09, $height, "�ʰ�", 1, 0, 'C', true);
									$pdf->Cell($pdf->width*0.09, $height, "��޿�", 1, 0, 'C', true);
									$pdf->Cell($pdf->width*0.09, $height, "�Ѻδ��", 1, 1, 'C', true);
								}

								if ($row['kind'] != '0'){
									$row['expense'] = 0;
									$row['over']    = 0;
								}

								$pdf->SetX($pdf->left);
								$pdf->Cell($pdf->width*0.08, $height, $row['svcNm'], 1, 0, 'C');
								$pdf->Cell($pdf->width*0.15, $height, $row['sugaNm'], 1, 0, 'L');
								$pdf->Cell($pdf->width*0.05, $height, number_format($row['cnt']), 1, 0, 'R');
								$pdf->Cell($pdf->width*0.09, $height, $myF->euckr($myF->_min2timeKor($row['time'])),	1, 0, 'R');
								$pdf->Cell($pdf->width*0.09, $height, number_format($row['sugaCost']), 1, 0, 'R');
								$pdf->Cell($pdf->width*0.09, $height, number_format($row['total']),	1, 0, 'R');
								$pdf->Cell($pdf->width*0.09, $height, number_format($row['pay']) , 1, 0, 'R');
								$pdf->Cell($pdf->width*0.09, $height, number_format($row['expense']), 1, 0, 'R');
								$pdf->Cell($pdf->width*0.09, $height, number_format($row['over']), 1, 0, 'R');
								$pdf->Cell($pdf->width*0.09, $height, number_format($row['bipay']), 1, 0, 'R');
								$pdf->Cell($pdf->width*0.09, $height, number_format($row['expense']+$row['over']+$row['bipay']), 1, 1, 'R');

								$laSvc['total']['total']   += $row['total'];
								$laSvc['total']['time']    += $row['timeTot']; //($row['time'] >= 270 ? $row['time'] - 30 : $row['time']);
								$laSvc['total']['pay']     += $row['pay'];
								$laSvc['total']['expense'] += $row['expense'];
								$laSvc['total']['over']    += $row['over'];
								$laSvc['total']['bipay']   += $row['bipay'];
							}
							unset($loSvc);
						}
					}
				}

				$liExpenseTot = $myF->cutOff($laSvc['total']['expense']+$laSvc['total']['over']+$laSvc['total']['bipay']);
				$liExpensePay = $myF->cutOff($laSvc['total']['expense']);

				$pdf->SetX($pdf->left);
				$pdf->Cell($pdf->width*0.08, $height, '', 'LTB', 0, 'C', true);
				$pdf->Cell($pdf->width*0.15, $height, '', 'TB', 0, 'L', true);
				$pdf->Cell($pdf->width*0.05, $height, '�հ�', 'TB', 0, 'R', true);
				$pdf->Cell($pdf->width*0.18, $height, $myF->euckr($myF->_min2timeKor($laSvc['total']['time'])), 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.09, $height, number_format($laSvc['total']['total']),	1, 0, 'R', true);
				$pdf->Cell($pdf->width*0.09, $height, number_format($laSvc['total']['pay']), 1, 0, 'R', true);
				$pdf->Cell($pdf->width*0.09, $height, number_format($liExpensePay), 1, 0, 'R', true);
				$pdf->Cell($pdf->width*0.09, $height, number_format($laSvc['total']['over']), 1, 0, 'R', true);
				$pdf->Cell($pdf->width*0.09, $height, number_format($laSvc['total']['bipay']), 1, 0, 'R', true);
				$pdf->Cell($pdf->width*0.09, $height, number_format($liExpenseTot), 1, 1, 'R', true);

				unset($laSvc);

				if ($lbTestMode){
					//���� ������ ���� ����� ���� ���
					if (is_array($laClientKind)){
						if (sizeof($laClientKind) > 1){
							$lsLastDt = $myF->dateAdd('month', 1, $year.'-'.$month.'-01', 'Y-m-d');
							$lsLastDt = $myF->dateAdd('day', -1, $lsLastDt, 'Y-m-d');
							$lsStr = '';

							foreach($laClientKind as $laC){
								if ($year.$month.'01' > $laC['from_dt']){
									$lsFromDt = $year.'.'.$month.'.01';
								}else{
									$lsFromDt = $myF->dateStyle($laC['from_dt'],'.');
								}

								if ($lsLastDt < $laC['to_dt']){
									$lsToDt = $myF->dateStyle($lsLastDt,'.');
								}else{
									$lsToDt = $myF->dateStyle($laC['to_dt'],'.');
								}
								$lsStr .= $myF->euckr($myF->_kindNm($laC['kind'])).'('.$laC['rate'].') : '.$lsFromDt.'~'.$lsToDt."\n";
							}

							$pdf->SetXY($pdf->left,$pdf->GetY()+2);
							$pdf->MultiCell($pdf->width, $height, $lsStr);
						}
					}

					unset($laClientKind);
				}
			}else{
				if ($code == '31141000043' /* ����� */){
				}else{
					// ��纸ȣ��
					//$pdf->SetXY($pdf->left, $top);
					$pdf->SetXY($pdf->left, $pdf->GetY() + 2);
					$pdf->Cell($pdf->width*0.15,	$height, "�޿�����",				1, 0, 'C', true);
					//$pdf->Cell(30,	$height, "������",					1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.28,	$height, "����(���񽺸�)", 1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.10,	$height, "Ƚ��", 1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.15,	$height, "�ð�", 1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.16,	$height, "����",					1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.16,	$height, "�ѱ޿����",				1, 1, 'C', true);

					$sql = '';

					foreach($svcParam as $i => $svc){
						$svc = explode('_', $svc);

						$sql .= (!empty($sql) ? ' union all ' : '');
						$sql .= 'select t01_mkind as kind
								 ,      t01_sugup_date as dt
								 ,      t01_svc_subcode as svc_cd
								 ,      case when t01_svc_subcode = \'200\' then \'�湮���\'
											 when t01_svc_subcode = \'500\' then \'�湮���\'
											 when t01_svc_subcode = \'800\' then \'�湮��ȣ\'
											 when t01_svc_subcode > \'20\' and t01_svc_subcode < \'30\' then \'�ٿ�ó\'
											 when t01_svc_subcode > \'30\' and t01_svc_subcode < \'40\' then \'��Ÿ����\' else \'-\' end as svc_nm
								 ,      '.$colSugaCD.' as suga_cd
								 ,      '.$colSugaVL.' as suga_val
								 ,	    '.$colSoyoTM.' as soyotime
								 ,      t01_bipay_umu as bipay_yn
								   from t01iljung
								  where t01_ccode      = \''.$code.'\'
									and t01_del_yn     = \'N\'
									and '.$colMemCD1.' = \''.$target.'\'
									and '.$colDT.'  like \''.$date.'%\' '.$family_sql;

						if ($code == '31141000043' /* ����� */)
							$sql .= ' and t01_bipay_umu != \'Y\'';

						if ($svc[0] != '')
							$sql .= ' and t01_mkind = \''.$svc[0].'\'';

						if ($svc[1] != '')
							$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';


						$sql .= ' union all
								 select t01_mkind as kind
								 ,      t01_sugup_date as dt
								 ,      t01_svc_subcode as svc_cd
								 ,      case when t01_svc_subcode = \'200\' then \'�湮���\'
											 when t01_svc_subcode = \'500\' then \'�湮���\'
											 when t01_svc_subcode = \'800\' then \'�湮��ȣ\'
											 when t01_svc_subcode > \'20\' and t01_svc_subcode < \'30\' then \'�ٿ�ó\'
											 when t01_svc_subcode > \'30\' and t01_svc_subcode < \'40\' then \'��Ÿ����\' else \'-\' end as svc_nm
								 ,      '.$colSugaCD.' as suga_cd
								 ,      '.$colSugaVL.' as suga_val
								 ,	    '.$colSoyoTM.' as soyotime
								 ,      t01_bipay_umu as bipay_yn
								   from t01iljung
								  where t01_ccode      = \''.$code.'\'
									and t01_del_yn     = \'N\'
									and '.$colMemCD2.' = \''.$target.'\'
									and '.$colDT.'  like \''.$date.'%\' '.$family_sql;

						if ($code == '31141000043' /* ����� */)
							$sql .= ' and t01_bipay_umu != \'Y\'';

						if ($svc[0] != '')
							$sql .= ' and t01_mkind = \''.$svc[0].'\'';

						if ($svc[1] != '')
							$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';
					}

					$sql = 'select t.svc_nm
							,      s.suga_nm
							,      count(t.suga_cd) as cnt
							,      t.suga_val
							,      sum(t.suga_val) as val
							,      t.kind
							,      case when t.kind = \'0\' and t.soyotime > 240 then case when t.soyotime > 510 then 510 else t.soyotime end - 30 else t.soyotime end as soyotime /*t.soyotime*/
							,      s.suga_cd
							,      t.svc_cd
							  from ('.$sql.') as t


							 inner join (
								  select \'0\' as kind, m01_mcode2 as suga_cd, m01_suga_cont as suga_nm, m01_sdate as from_dt, m01_edate as to_dt
									from m01suga
								   where m01_mcode = \'goodeos\'
								   union all
								  select \'0\' as m01_mkind, m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
									from m11suga
								   where m11_mcode = \'goodeos\'
								   union all
								  select service_kind, service_code, concat(service_gbn, ifnull(service_lvl, \'\')), replace(service_from_dt,\'-\',\'\'), replace(service_to_dt,\'-\',\'\')
									from suga_service
								   where org_no = \'goodeos\'
								  ) as s
							   on t.suga_cd = s.suga_cd
							  and t.kind = s.kind
							  and t.dt between s.from_dt and s.to_dt
							group by t.svc_nm, s.suga_nm, t.suga_val, t.soyotime
							order by t.svc_cd, s.suga_cd';

					$conn->query($sql);
					$conn->fetch();
					$rowCount = $conn->row_count();

					$suga	= 0;
					$total	= 0;

					$totSugaCnt  = 0;
					$totSugaTime = 0;

					for($i=0; $i<$rowCount; $i++){
						$row = $conn->select_row($i);

						if ($pdf->GetY()+$height > $pdf->height){
							$pdf->AddPage(strtoupper($page_pl), 'A4');
							$pdf->SetX($pdf->left);
							$pdf->Cell($pdf->width*0.15,	$height, "�޿�����",				1, 0, 'C', true);
							//$pdf->Cell(30,	$height, "������",					1, 0, 'C', true);
							$pdf->Cell($pdf->width*0.28,	$height, "����(���񽺸�)", 1, 0, 'C', true);
							$pdf->Cell($pdf->width*0.10,	$height, "Ƚ��", 1, 0, 'C', true);
							$pdf->Cell($pdf->width*0.15,	$height, "�ð�", 1, 0, 'C', true);
							$pdf->Cell($pdf->width*0.16,	$height, "����",					1, 0, 'C', true);
							$pdf->Cell($pdf->width*0.16,	$height, "�ѱ޿����",				1, 1, 'C', true);
						}



						$sugaTime = $myF->euckr($myF->_min2timeKor($row[2] * $row[6]));
						$sugaNm   = $row[1];

						if ($row['suga_cd'] == 'CCWS9' || $row['suga_cd'] == 'CCHS9'){
							$sugaNm .= '('.$myF->euckr($myF->_min2timeKor($row[6])).')';
						}

						$pdf->SetX($pdf->left);
						$pdf->Cell($pdf->width*0.15,	$height, $row[0],	1, 0, 'C');
						//$pdf->Cell(30,	$height, $row[1],	1, 0, 'L');
						$pdf->Cell($pdf->width*0.28,	$height, $sugaNm,	1, 0, 'L');
						$pdf->Cell($pdf->width*0.10,	$height, $row[2],	1, 0, 'R');
						$pdf->Cell($pdf->width*0.15,	$height, $sugaTime,	1, 0, 'R');
						$pdf->Cell($pdf->width*0.16,	$height, number_format($row[3]),	1, 0, 'R');
						$pdf->Cell($pdf->width*0.16,	$height, number_format($row[4]),	1, 1, 'R');

						$suga  += $row[3];
						$total += $row[4];

						$totSugaCnt  += $row[2];
						$totSugaTime += ($row[2] * $row[6]);
					}
					$pdf->SetX($pdf->left);
					$pdf->Cell($pdf->width*0.15,	$height, "��      ��",	1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.28,	$height, "",	1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.10,	$height, number_format($totSugaCnt),	1, 0, 'R', true);
					$pdf->Cell($pdf->width*0.15,	$height, $myF->euckr($myF->_min2timeKor($totSugaTime)),	1, 0, 'R', true);
					$pdf->Cell($pdf->width*0.16,	$height, number_format($suga),	1, 0, 'R', true);
					$pdf->Cell($pdf->width*0.16,	$height, number_format($total),	1, 0, 'R', true);

					$conn->row_free();
				}
			}
		}

		/*********************************************************

			�������

		*********************************************************/
		if ($type == 's' && $dis_yn == 'Y'){
			$print_dt = explode('-', $printDT);

			$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
			$pdf->Cell($pdf->width, $pdf->rowHeight, $print_dt[0].'�� '.$print_dt[1].'�� '.$print_dt[2].'��', 0, 1, 'C');

			if ($code == '31121500010' /* ��Ǫ�� */){
				//���� ���
				$sql = 'select m00_jikin
						  from m00center
						 where m00_mcode = \''.$code.'\'
						 limit 1';

				$iconJikin = $conn->get_data($sql);

				if (!empty($iconJikin)){
					$tmpImg = getImageSize('../mem_picture/'.$iconJikin);
					$pdf->Image('../mem_picture/'.$iconJikin, $pdf->width - 18, $pdf->GetY() - 10, 21);
				}



				$pdf->SetXY($pdf->left + $pdf->width * 0.5, $pdf->GetY() + 2);
				$pdf->Cell($pdf->width * 0.5, $pdf->rowHeight, '����� : '.$manager.'                            ', 0, 1, 'R');
				$pdf->SetXY($pdf->left + $pdf->width * 0.5, $pdf->GetY() + 2);
				$pdf->Cell($pdf->width * 0.5, $pdf->rowHeight, '������ : '.$pdf->name.'       (�� �� �Ǵ� ��)', 0, 1, 'R');
			}


			$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
			$pdf->Cell($pdf->width, $pdf->rowHeight, '�� �ſ� �ۼ��Ͽ� ��� ����.(�����Ⱓ : �ۼ��Ϸκ��� 3��)', 0, 1, 'L');
			$pdf->SetX($pdf->left);
			$pdf->Cell($pdf->width, $pdf->rowHeight, '�� Ȱ��������� �� Ȱ�������ΰ� ������ �� ��ȣ��(����)�� �����Ͽ� �ſ� 5�� �������� �ۼ�', 0, 1, 'L');
			$pdf->SetX($pdf->left);
			$pdf->Cell($pdf->width, $pdf->rowHeight, '�� 2���̻� ���񽺸� ������ ��� Ȱ�������η¸��� �̸��� ���� ������ ���� ǥ���Ѵ�.', 0, 1, 'L');
		}

		usleep(10000);
	}


	$pdf->Output();

	include('../inc/_db_close.php');

	// �޷��� ���Ϻ� ������ �׸���.
	function drawLine($pdf, $col, $top, $aiAddTop){
		$pdf->SetLineWidth(0.2);

		$left = $pdf->left;

		for($i=0; $i<7; $i++){
			$left += $col['w'][$i];
			$pdf->Line($left, $pdf->top+$aiAddTop, $left, $top);
		}
	}

	// �޷��� ��ü �׵θ��� �׸���.
	function drawBorder($pdf, $height,$aiAddTop){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $pdf->top+$aiAddTop, $pdf->width, $height);
		$pdf->SetLineWidth(0.2);
	}

	//������ �屸���
	function setDesire($pdf, $basic_x, $basic_y, $counsel){
		$coord_y = $basic_y;

		//������ ����
		$counsel_str = trim($counsel);
		$counsel_str = str_replace('&nbsp;', '', $counsel_str);
		$counsel_str = str_replace('<P>', '', $counsel_str);
		$counsel_str = str_replace('</P>', '</P>[NEW]</P>', $counsel_str);
		$counsel_str = str_replace('<BR>', '</P>[NEW]</P>', $counsel_str);
		$counsel_str = str_replace('<br>', '</P>[NEW]</P>', $counsel_str);
		$counsel_str = str_replace('<bR>', '</P>[NEW]</P>', $counsel_str);
		$counsel_str = str_replace('<Br>', '</P>[NEW]</P>', $counsel_str);
		$counsel_str = str_replace('">', '</P>', $counsel_str);
		$counsel_str = str_replace('<SPAN style="COLOR: ', '</P>', $counsel_str);
		$counsel_str = str_replace('</SPAN>', '</P>#000000</P>', $counsel_str);

		$counsel_arr = explode('</P>', $counsel_str);

		//��ǥ����
		$pdf->SetXY($basic_x, $coord_y);

		$y = $coord_y + 4.6;

		//�����ۼ�
		for($i=0; $i<sizeof($counsel_arr); $i++){
			$counsel_txt = trim($counsel_arr[$i]);

			if ($new_flag || $i == 0){
				$x = $basic_x + 1;
				$w = 0;
			}

			$color_flag = false;
			$new_flag = false;

			for($j=0; $j<mb_strlen($counsel_txt); $j++){

				if (mb_substr($counsel_txt, $j, 1) == '#'){
					$txt_color = mb_substr($counsel_txt, 1, 6);
					$pdf->SetTextColor(hexdec(substr($txt_color,0,2)),hexdec(substr($txt_color,2,2)),hexdec(substr($txt_color,4,2)));
					$color_flag = true;
					break;
				}else if (mb_substr($counsel_txt, $j, mb_strlen('[NEW]')) == '[NEW]'){
					$y += 4.6;
					$new_flag = true;
					break;
				}else{
					$str = mb_substr($counsel_txt, $j, 1, 'euckr');
					$txt_width = $pdf->GetStringWidth($str);
					$w += $txt_width;

					if ($w >= $pdf->width * 0.29 - 3){
						$y += 4.6;
						$x  = $basic_x + 1;
						$w  = 0;
					}

					$pdf->Text($x, $y, $str);


					$x += $txt_width;
				}

			}
		}

		return $y;
	}
?>
<script>self.focus();</script>