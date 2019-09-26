<?
	include('../inc/_db_open.php');
	include('../inc/_myFun.php');
	include('../inc/_ed.php');



	/*********************************************************
		장애인활동지원 여부
	*********************************************************/
	$dis_yn = 'N';



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

	$svcParam = explode('/',$_GET['param']);
	$printDT  = $_GET['printDT'];

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



	/*********************************************************
		달력의 주당 최소 높이
	*********************************************************/
	$minimum_height = 12;


	// 휴일리스트
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
			$sql = "select kind, jumin, name
					  from (
						   select min(m02_mkind) as kind
						   ,      m02_yjumin as jumin
						   ,      m02_yname as name
						     from m02yoyangsa
						    inner join t01iljung
							   on m02_ccode = t01_ccode
						      and m02_mkind = t01_mkind
						      and $colMemCD1 = m02_yjumin
						      and $colDT like '$year$month%' $family_sql
						      and t01_del_yn = 'N'
						    where m02_ccode  = '$code'
							group by m02_yjumin
						    union all
						   select min(m02_mkind) as kind
						   ,      m02_yjumin as jumin
						   ,      m02_yname as name
						     from m02yoyangsa
						    inner join t01iljung
							   on m02_ccode         = t01_ccode
						      and m02_mkind         = t01_mkind
						      and $colMemCD2       = m02_yjumin
						      and $colDT like '$year$month%' $family_sql
						      and t01_del_yn        = 'N'
						    where m02_ccode         = '$code'
							group by m02_yjumin
						   ) as t
					 order by name";


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

	// 센터정보
	$sql = "select m00_cname
				 , m00_mname
				 , m00_ctel
				 , m00_bank_no
				 , case m00_bank_name when '002' then '산업은행'
									  when '003' then '기업은행'
									  when '004' then '국민은행'
									  when '005' then '외환은행'
									  when '007' then '수협중앙회'
									  when '008' then '수출입은행'
									  when '011' then '농협중앙회'
									  when '012' then '농협회원조합'
									  when '020' then '우리은행'
									  when '023' then 'SC제일은행'
									  when '027' then '한국씨티은행'
									  when '031' then '대구은행'
									  when '032' then '부산은행'
									  when '034' then '광주은행'
									  when '035' then '제주은행'
									  when '037' then '전북은행'
									  when '039' then '경남은행'
									  when '045' then '새마을금고연합회'
									  when '048' then '신협중앙회'
									  when '050' then '상호저축은행'
									  when '071' then '우체국'
									  when '081' then '하나은행'
									  when '088' then '신한은행' else m00_bank_name end as bank_name
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
	$pdf->font_name_kor = '굴림';
	$pdf->font_name_eng = 'Gulim';
	$pdf->AddUHCFont('굴림','Gulim');
	$pdf->Open();
	$pdf->SetFillColor(220,220,220);

	// 헤더값 설정
	$pdf->auto_draw_head= $detail == 'y' ? true : false;
	$pdf->year			= $year;		//년
	$pdf->month			= $month;		//월
	$pdf->type			= $type;		//수급자, 요양보호사 구분
	$pdf->useType		= $useType;		//관리자, 직원 구분
	$pdf->centerName	= $centerName;	//센터명
	$pdf->centerTel		= $centerTel;	//선터전화번호
	$pdf->bankNo        = $bank_No;
	$pdf->bankName      = $bank_Name;
	$pdf->bankDepos     = $bank_Depos;


	$pdf->left = 7;
	$pdf->width = 196;


	for($l=0; $l<sizeOf($list); $l++){
		$dis_yn = 'N'; //장애인활동지원 여부
		$pdf->kind = $svcKind[$l];		//서비스구분
		$target = $list[$l];
		if ($type == 's' || $type == 'c'){
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
			// 페이지 추가
			$pdf->AddPage(strtoupper($page_pl), 'A4');

			// 일정 변수 설정
			$calTime   = mkTime(0, 0, 1, $pdf->month, 1, $pdf->year);
			$today     = date('Ymd', mktime());
			$lastDay   = date('t', $calTime); //총일수 구하기
			$startWeek = date('w', strtotime(date('Y-m', $calTime).'-01')); //시작요일 구하기
			$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
			$lastWeek  = date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay)); //마지막 요일 구하기

			$date = $pdf->year.$pdf->month;

			// 일별 데이타 초기화
			for($i=1; $i<=$lastDay; $i++){
				$cal[$i][0]['service']	= ''; //서비스명
				$cal[$i][0]['time']		= ''; //계획시간
				$cal[$i][0]['cost']		= ''; //소요시간
				$cal[$i][0]['worker']	= ''; //요양보호사
			}


			// 일별 데이타
			if ($type == 's'){
				$sql = '';

				foreach($svcParam as $i => $svc){
					$svc = explode('_', $svc);

					$sql .= (!empty($sql) ? ' union all ' : '');
					$sql .= 'select cast(date_format('.$colDT.', \'%d\') as signed)
							 ,      date_format(concat('.$colDT.', '.$colFromTM.',\'00\'), \'%H:%i\')
							 ,      date_format(concat('.$colDT.', '.$colToTM.',\'00\'), \'%H:%i\')
							 ,      '.$colSoyoTM.'
							 ,      case t01_svc_subcode when \'200\' then case when t01_mkind = \'0\' then \'요양\' else \'장애\' end
														 when \'500\' then \'목욕\'
														 when \'800\' then \'간호\'
														 when \'21\'  then \'간병\'
														 when \'22\'  then \'노인\'
														 when \'23\'  then \'산모\'
														 when \'24\'  then \'장애\'
														 when \'31\'  then \'산유\'
														 when \'32\'  then \'병원\'
														 when \'33\'  then \'기타\' else \'-\' end
							 ,      '.$colMemNM1.'
							 ,      concat(case when '.$colMemNM2.' != \'\' then \'/\' else \'\' end, left('.$colMemNM2.', 3))
							 ,	    t01_toge_umu
							 ,      t01_bipay_umu
							   from t01iljung
							  where t01_ccode     = \''.$code.'\'
								and t01_jumin     = \''.$target.'\'
								and '.$colDT.' like \''.$date.'%\' '.$family_sql.'
								and t01_del_yn    = \'N\'';

					if ($code == '31141000043' /* 예사랑 */)
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
						,      case t01_svc_subcode when '200' then case when t01_mkind = '0' then '요양' else '장애' end
													when '500' then '목욕'
													when '800' then '간호'
													when '21'  then '간병'
													when '22'  then '노인'
													when '23'  then '산모'
													when '24'  then '장애'
													when '31'  then '산유'
													when '32'  then '병원'
													when '33'  then '기타'
													else '-' end
						,      $colMemNM1
						,      concat(case when $colMemNM2 != '' then '/' else '' end, left($colMemNM2, 3))
						,	   t01_toge_umu
						  from t01iljung
						 where t01_ccode  = '$code'
						   and t01_jumin  = '$target'
						   and $colDT like '$date%' $family_sql
						   and t01_del_yn = 'N'";

				if ($code == '31141000043' ){ //예사랑
					$sql .= " and t01_bipay_umu != 'Y'";
				}

				$sql .= " order by $colDT, $colFromTM, $colToTM";
				*/

			}else if ($type == 'c'){
				$sql = "select cast(date_format($colDT, '%d') as signed)
						,      date_format(concat($colDT, $colFromTM,'00'), '%H:%i')
						,      date_format(concat($colDT, $colToTM,'00'), '%H:%i')
						,      $colSoyoTM
						,      case t01_svc_subcode when '200' then case when t01_mkind = '0' then '요양' else '장애' end
													when '500' then '목욕'
													when '800' then '간호'
													when '21'  then '간병'
													when '22'  then '노인'
													when '23'  then '산모'
													when '24'  then '장애'
													when '31'  then '산유'
													when '32'  then '병원'
													when '33'  then '기타'
													else '-' end
						,      $colMemNM1
						,      concat(case when $colMemNM2 != '' then '/' else '' end, left($colMemNM2, 3))
						,	   t01_toge_umu
						,      t01_bipay_umu
						  from t01iljung
						 where t01_ccode  = '$code'
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
							 ,      case t01_svc_subcode when \'200\' then case when t01_mkind = \'0\' then \'요양\' else \'장애\' end
														 when \'500\' then \'목욕\'
														 when \'800\' then \'간호\'
														 when \'21\'  then \'간병\'
														 when \'22\'  then \'노인\'
														 when \'23\'  then \'산모\'
														 when \'24\'  then \'장애\'
														 when \'31\'  then \'산유\'
														 when \'32\'  then \'병원\'
														 when \'33\'  then \'기타\' else \'-\' end as svc_nm
							 ,      case t01_svc_subcode when \'500\' then case '.$colMemCD1.' when \''.$target.'\' then \'(정)\' else \'(부)\' end else \'\' end as ms_gbn
							 ,	    t01_toge_umu as family_yn
							 ,      t01_ccode as code, t01_mkind as kind, t01_jumin as c_cd
							   from t01iljung
							  where t01_ccode      = \''.$code.'\'
								and t01_del_yn     = \'N\'
								and '.$colMemCD1.' = \''.$target.'\'
								and left('.$colDT.', '.strlen($date).') = \''.$date.'\' '.$family_sql;

					if ($code == '31141000043') //예사랑
						$sql .= ' and t01_bipay_umu != \'Y\'';

					if (!empty($svc[0]))
						$sql .= ' and t01_mkind = \''.$svc[0].'\'';

					if (!empty($svc[1]))
						$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';

					$sql .= ' union all
							 select cast(date_format('.$colDT.', \'%d\') as signed) as dt
							 ,      date_format(concat('.$colDT.', '.$colFromTM.',\'00\'), \'%H:%i\') as f_time
							 ,      date_format(concat('.$colDT.', '.$colToTM.',\'00\'), \'%H:%i\') as t_time
							 ,      '.$colSoyoTM.' as p_time
							 ,      case t01_svc_subcode when \'200\' then case when t01_mkind = \'0\' then \'요양\' else \'장애\' end when \'500\' then \'목욕\' when \'800\' then \'간호\' else \'-\' end as svc_nm
							 ,      case t01_svc_subcode when \'500\' then case '.$colMemCD1.' when \''.$target.'\' then \'(정)\' else \'(부)\' end else \'\' end as ms_gbn
							 ,	    t01_toge_umu as family_yn
							 ,      t01_ccode as code, t01_mkind as kind, t01_jumin as c_cd
							   from t01iljung
							  where t01_ccode      = \''.$code.'\'
								and t01_del_yn     = \'N\'
								and '.$colMemCD2.' = \''.$target.'\'
								and left('.$colDT.', '.strlen($date).') = \''.$date.'\' '.$family_sql;

					if ($code == '31141000043') //예사랑
						$sql .= ' and t01_bipay_umu != \'Y\'';

					if (!empty($svc[0]))
						$sql .= ' and t01_mkind = \''.$svc[0].'\'';

					if (!empty($svc[1]))
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
						,      case t01_svc_subcode when '200' then case when t01_mkind = '0' then '요양' else '장애' end
													when '500' then '목욕'
													when '800' then '간호'
													when '21'  then '간병'
													when '22'  then '노인'
													when '23'  then '산모'
													when '24'  then '장애'
													when '31'  then '산유'
													when '32'  then '병원'
													when '33'  then '기타'
													else '-' end
						,      m03_name
						,      case t01_svc_subcode when '500' then case $colMemCD1 when '$target' then '(정)' else '(부)' end else '' end
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
						,      case t01_svc_subcode when '200' then case when t01_mkind = '0' then '요양' else '장애' end when '500' then '목욕' when '800' then '간호' else '-' end
						,      m03_name
						,      case t01_svc_subcode when '500' then case $colMemCD1 when '$target' then '(정)' else '(부)' end else '' end
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
					장애인활동지원
					*****************************************************/

					if ($cal[$day][$seq]['service'] == '장애'){
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

			// 주차별 높이를 계산한다.
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

			$top	= $pdf->top+15;
			$left	= $pdf->left;
			$height	= $pdf->rowHeight;

			$pdf->SetXY($left, $top);

			$day = 1; //화면에 표시할 화면의 초기값을 1로 설정
			for($i=1; $i<=$totalWeek; $i++){
				// 요일
				if ($i == 1){
					$pdf->SetFont('굴림','B',9);
					for($j=0; $j<7; $j++){
						$pdf->Cell($col['w'][$j], $height, $col['t'][$j], 1, $j < 6 ? 0 : 1, 'C', true);
					}
					$pdf->SetFont('굴림','',9);
					$top += $height;
					$pdf->SetFillColor(238,238,238);
				}

				$left = $pdf->left;

				// 총 가로칸 만들기
				for($j=0; $j<7; $j++){
					$tempTop[$j] = 1;
					if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
						if (sizeOf($cal[$day]) > 0){
							// 주차별 높이와 좌표를 다시 계산
							if($page_pl == 'p'){
								$checkHeight = $top + $height + $checkTop[$i] + $height * ($type == 's' || $type == 'c' ? 3.5 : 1.5) + $height + 3;
							}else {
								$checkHeight = $top + $height + $checkTop[$i] + $height * ($type == 's' || $type == 'c' ? 4.8 : 2) + $height + 4;
							}

							// 주차별 높이가 페이지를 넘어가면 페이지를 추가한다.
							if ($checkHeight >= $pdf->height){
								// 요일별 라인
								drawLine($pdf, $col, $top);

								// 테두리
								drawBorder($pdf, $top-$height*6);

								$pdf->AddPage(strtoupper($page_pl), 'A4');
								$pdf->SetX($pdf->left);
								$top = $pdf->top+15;
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
										case '34415000061': //공주사랑나눔
											$pdf->SetTextColor(0,0,0);
											break;
										default:
											$pdf->SetTextColor(255,0,0);
									}
									break;
								case 6:
									switch($code){
										case '34415000061': //공주사랑나눔
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
									case '34415000061': //공주사랑나눔
										break;
									default:
										if ($color_yn == 'Y'){
											$pdf->SetTextColor(255,0,0);
										}
								}
								if ($k == 0){
									$pdf->SetXY($left, $top);
									$pdf->SetFont('굴림','B',10);
									$pdf->Cell($col['w'][$j], $height-0.5, "$str_day", "T", 0, 'L');

									if ($holiday[$year.$month.($day<10?'0':'').$day] != ''){
										$pdf->SetFont('굴림','',9);
										$pdf->SetX($left+$pdf->GetStringWidth(" $str_day"));
										$pdf->Cell($col['w'][$j], $height-0.5, $holiday[$year.$month.($day<10?'0':'').$day], "T", 1, 'L');
									}

									$pdf->SetFont('굴림','',9);

									if($page_pl == 'p'){
										$pdf->SetFont('굴림','B',9);
									}else if($page_pl == 'l'){
										$pdf->SetFont('굴림','B',11);
									}
								}

								$pdf->SetTextColor(0,0,0);


								if (strLen($cal[$day][$k]['service']) != ''){
									$pdf->SetXY($left, $top + $height + $tempTop[$j]);

									if($page_pl == 'l'){
										if($cal[$day][$k]['toge'] == 'Y'){
											if ($type == 's' || $type == 'c'){
												$pdf->MultiCell($col['w'][$j], 6, "[동거 ".$cal[$day][$k]['service']."]\n".$cal[$day][$k]['time']."\n".$cal[$day][$k]['worker']);
												$tempTop[$j] += ($height * 3);
											}else{
												$pdf->MultiCell($col['w'][$j], 6, "[동거 ".$cal[$day][$k]['service']."]".$cal[$day][$k]['worker']."\n".$cal[$day][$k]['time']);
												$tempTop[$j] += ($height * 2.2);
											}
										}else {
											if ($type == 's' || $type == 'c'){
												$pdf->MultiCell($col['w'][$j], 6, "[".$cal[$day][$k]['service']."]".($cal[$day][$k]['bipay'] == 'Y' ? '[비]' : '')."\n".$cal[$day][$k]['time']."\n".$cal[$day][$k]['worker']);
												$tempTop[$j] += ($height * 3);
											}else{
												$pdf->MultiCell($col['w'][$j], 6, "[".$cal[$day][$k]['service']."]".$cal[$day][$k]['worker']."\n".$cal[$day][$k]['time']);
												$tempTop[$j] += ($height * 2.2);
											}
										}
									}else if($page_pl == 'p'){
										if($cal[$day][$k]['toge'] == 'Y'){
											if ($type == 's' || $type == 'c'){
												#$pdf->MultiCell($col['w'][$j], 3.7, "[동거]\n".$cal[$day][$k]['time']."\n".$cal[$day][$k]['worker']);
												$pdf->MultiCell($col['w'][$j], 3.7, "[동거]\n".$cal[$day][$k]['time']);

												$pdf->SetXY($left, $top + $height + $tempTop[$j] + 7.4);
												$pdf->Cell($col['w'][$j], 3.7, $cal[$day][$k]['worker'], 0, 1, 'R');

												$tempTop[$j] += ($height * 2);
											}else{
												$pdf->MultiCell($col['w'][$j], 3.7, "[동거]".$cal[$day][$k]['worker']."\n".$cal[$day][$k]['time']);
												$tempTop[$j] += ($height * 1.5);
											}
										}else {
											if ($type == 's' || $type == 'c'){
												#$pdf->MultiCell($col['w'][$j], 3.7, "[".$cal[$day][$k]['service']."]\n".$cal[$day][$k]['time']."\n".$cal[$day][$k]['worker']);
												$pdf->MultiCell($col['w'][$j], 3.7, "[".$cal[$day][$k]['service']."]".($cal[$day][$k]['bipay'] == 'Y' ? '[비]' : '')."\n".$cal[$day][$k]['time']);

												$pdf->SetXY($left, $top + $height + $tempTop[$j] + 7.4);
												$pdf->Cell($col['w'][$j], 3.7, $pdf->_splitTextWidth($myF->utf($cal[$day][$k]['worker']),$col['w'][$j]), 0, 1, 'R');

												$tempTop[$j] += ($height * 2);
											}else{
												$pdf->MultiCell($col['w'][$j], 3.7, "[".$cal[$day][$k]['service']."]".$cal[$day][$k]['worker']."\n".$cal[$day][$k]['time']);
												$tempTop[$j] += ($height * 1.5);
											}
										}
									}

									switch($cal[$day][$k]['service']){
										case '목욕':
											$pdf->Image('../image/icon_bath.jpg', $left + $col['w'][$j] - 4 - 1, $top + $tempTop[$j] - $height, 4, 4);
											break;
										case '간호':
											$pdf->Image('../image/icon_nurs.jpg', $left + $col['w'][$j] - 4 - 1, $top + $tempTop[$j] - $height, 4, 4);
											break;
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
					최소 높이를 맞춘다.
				*********************************************************/
				if ($tempHeight < $minimum_height) $tempHeight = $minimum_height;


				if ($tempHeight > 0){
					$top += ($height + $tempHeight);
				}else{
					$top += ($height * 2.5);
				}
			}

			$cal = array();

			// 요일별 라인
			drawLine($pdf, $col, $top);

			// 테두리
			drawBorder($pdf, $top-$height*6);

			if ($type == 's' || $type == 'c'){
				if ($pdf->height - $pdf->GetY() > 50){
				}else{
					// 페이지 추가
					$pdf->AddPage(strtoupper($page_pl), 'A4');
					$top = 33;
				}
			}

			// 배경색상 설정
			$pdf->SetFillColor(220,220,220);

			if ($type == 's'){
				// 요양보호사
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

				$pdf->SetFont('굴림','',10);
				$pdf->SetXY($pdf->left, $top+2);

				$yoyInfo = '';

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);
					$yoyInfo .= ($yoyInfo != '' ? '  /  ' : '').$row[0].'('.$myF->phoneStyle($row[1],'.').')';
				}

				$pdf->Cell($pdf->width, $height+2, " 담당 요양보호사 : $yoyInfo", 1, 1, 'L', true);

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

			// 배경색상 설정
			$pdf->SetFillColor(220,220,220);
			$height	= $pdf->rowHeight;

			$date = $pdf->year.$pdf->month;
			$lastDay = date('t', $calTime); //총일수 구하기
		}

		// 제공서비스내역
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
								 where m01_mcode = \''.$code.'\'
								 union all
								select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
								  from m11suga
								 where m11_mcode = \''.$code.'\'
								 union all
								select service_code, concat(service_gbn, ifnull(service_lvl, \'\')), replace(service_from_dt,\'-\',\'\'), replace(service_to_dt,\'-\',\'\')
								  from suga_service
								 where org_no = \''.$code.'\'
								) as suga
							 on '.$colSugaCD.' = m01_mcode2
							and '.$colDT.' between m01_sdate and m01_edate
						  where t01_ccode     = \''.$code.'\'
							and t01_jumin     = \''.$target.'\'
							and '.$colDT.' like \''.$date.'%\' '.$family_sql.'
							and t01_del_yn    = \'N\'';

				if ($code == '31141000043' /* 예사랑 */)
					$sql .= ' and t01_bipay_umu != \'Y\'';

				if (!empty($svc[0]))
					$sql .= ' and t01_mkind = \''.$svc[0].'\'';

				if (!empty($svc[1]))
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

			if ($code == '31141000043'){ //예사랑
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
						 ,      case t01_svc_subcode when \'500\' then case '.$colMemCD1.' when \''.$target.'\' then \'(정)\' else \'(부)\' end else \'\' end as ms_gbn
						 ,      t01_svc_subcode as svc_cd
						 ,      '.$colSugaCD.' as suga_cd
						 ,      t01_ccode as code
						 ,      t01_mkind as kind
						   from t01iljung
						  where t01_ccode      = \''.$code.'\'
							and '.$colMemCD1.' = \''.$target.'\'
							and '.$colDT.'  like \''.$date.'%\' '.$family_sql.'
							and t01_del_yn     = \'N\'';

				if ($code == '31141000043' /* 예사랑 */)
					$sql .= ' and t01_bipay_umu != \'Y\'';

				if (!empty($svc[0]))
					$sql .= ' and t01_mkind = \''.$svc[0].'\'';

				if (!empty($svc[1]))
					$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';


				$sql .= ' union all
						 select '.$colDT.' as dt
						 ,      date_format(concat('.$colDT.', '.$colFromTM.', \'00\'), \'%H:%i\') as f_time
						 ,      date_format(concat('.$colDT.', '.$colToTM.', \'00\'), \'%H:%i\') as t_time
						 ,      t01_jumin as c_cd
						 ,      case t01_svc_subcode when \'500\' then case '.$colMemCD1.' when \''.$target.'\' then \'(정)\' else \'(부)\' end else \'\' end as ms_gbn
						 ,      t01_svc_subcode as svc_cd
						 ,      '.$colSugaCD.' as suga_cd
						 ,      t01_ccode as code
						 ,      t01_mkind as kind
						   from t01iljung
						  where t01_ccode      = \''.$code.'\'
							and '.$colMemCD2.' = \''.$target.'\'
							and '.$colDT.'  like \''.$date.'%\' '.$family_sql.'
							and t01_del_yn     = \'N\'';

				if ($code == '31141000043' /* 예사랑 */)
					$sql .= ' and t01_bipay_umu != \'Y\'';

				if (!empty($svc[0]))
					$sql .= ' and t01_mkind = \''.$svc[0].'\'';

				if (!empty($svc[1]))
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
							where m01_mcode = \''.$code.'\'
							union all
						   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
							 from m11suga
							where m11_mcode = \''.$code.'\'
							union all
						   select service_code, concat(service_gbn, ifnull(service_lvl, \'\')), replace(service_from_dt,\'-\',\'\'), replace(service_to_dt,\'-\',\'\')
							 from suga_service
							where org_no = \''.$code.'\'
						   ) as suga
						on t.suga_cd = suga.suga_cd
					   and t.dt between suga.suga_from and suga.suag_to
					 order by c_nm, svc_cd, f_time, t_time, dt';

			/*********************************************************
			$sql = "select cast(date_format($colDT, '%d') as signed) as $colDT
					,      date_format(concat($colDT, $colFromTM, '00'), '%H:%i') as $colFromTM
					,      date_format(concat($colDT, $colToTM, '00'), '%H:%i') as $colToTM
					,      m03_name
					,      case t01_svc_subcode when '500' then case $colMemCD1 when '$target' then '(정)' else '(부)' end else '' end
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
					,      case t01_svc_subcode when '500' then case $colMemCD1 when '$target' then '(정)' else '(부)' end else '' end
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
			//수급자욕구상담
			$sql = "select desire_status as stat
					,      desire_content as cont
					,      desire_service as svc
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

			$pdf->SetFont('굴림','B',9);
			$pdf->SetXY($pdf->left, $pdf->GetY()+2);

			if ($type == 's'){
				if ($pdf->kind == '4'){
					$strType = '지원인력명';
				}else{
					$strType = '요양보호사';
				}
			}else{
				$strType = '수급자';
			}

			$pdf->Cell($pdf->width*0.1,	 $height, "제공시간",			1, 0, 'C', true);
			$pdf->Cell($pdf->width*0.12, $height, $strType,				1, 0, 'C', true);
			$pdf->Cell($pdf->width*0.73, $height, "제공서비스/제공일",	1, 0, 'C', true);
			$pdf->Cell($pdf->width*0.05, $height, "횟수",				1, 1, 'C', true);

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
					$pdf->Cell($pdf->width*0.1,	$height, "제공시간",1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.12,$height, $type == 's' ? "요양보호사" : "수급자",1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.73,$height, "제공서비스/제공일",1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.05,$height, "횟수",1, 1, 'C', true);
					$pdf->SetX($pdf->left);
					$top = 33;
				}
				$pdf->SetFont('굴림','',9);
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
		}else if ($type == 'c'){
			//수급자욕구상담
			$counsel = $conn->get_array($sql);

			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY($pdf->left, $pdf->GetY()+2);

			$pdf->Cell($pdf->width*0.27, $height, '수급자 현상태/욕구평가', 1, 0, 'C', true);
			$pdf->Cell($pdf->width*0.27, $height, '장기요양/수급자 요구내용', 1, 0, 'C', true);
			$pdf->Cell($pdf->width*0.27, $height, '요양보호사 서비스내용', 1, 0, 'C', true);
			$pdf->Cell($pdf->width*0.19, $height, '수가적용', 1, 1, 'C', true);

			$coord_y = $pdf->GetY();
			$body_w  = $pdf->width*0.27;
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
			# 수가금액
			#
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

				$conn->query($sql);
				$conn->fetch();

				$row_count = $conn->row_count();

				$tot_amt = 0;
				$my_amt  = 0;

				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i);

					$tot_amt += $row[$colSugaVL];
					$my_amt  += (($row[$colSugaVL] * $row['m03_bonin_yul']) / 100);
				}

				$conn->row_free();
			#
			####################################################

			$pdf->SetXY($pdf->left+$body_w*3+3, $coord_y+1);
			$pdf->MultiCell($body_w, 5, "-월사용금액\n   ".number_format($myF->cutOff($tot_amt))."원");

			$pdf->SetTextColor(0,0,255);

			$pdf->SetXY($pdf->left+$body_w*3+3, $pdf->GetY()+1);
			$pdf->MultiCell($body_w, 5, "-본인부담금\n   ".number_format($myF->cutOff($my_amt))."원");

			$pdf->SetTextColor(0,0,0);

			if ($border_y < $tmp_y) $border_y = $tmp_y;

			if ($border_y < $coord_y + 25) $border_y = $coord_y + 25;
			if ($border_y > $pdf->height) $border_y = $pdf->height;

			$pdf->Line($pdf->left+$body_w, $coord_y, $pdf->left+$body_w, $border_y);
			$pdf->Line($pdf->left+$body_w*2, $coord_y, $pdf->left+$body_w*2, $border_y);
			$pdf->Line($pdf->left+$body_w*3, $coord_y, $pdf->left+$body_w*3, $border_y);
			$pdf->Rect($pdf->left, $coord_y, $pdf->width, $border_y - $coord_y);

			###################################################
			#
			# 수급자와 요양보호사 출력
				$sql = "select m03_name, m03_yoyangsa1_nm
						  from m03sugupja
						 where m03_ccode = '$code'
						   and m03_mkind = '$kind'
						   and m03_jumin = '$target'";

				$client = $conn->get_array($sql);
			#
			###################################################

			$pdf->set_font(11);
			$pdf->SetXY($pdf->left, $border_y+3);
			$pdf->Cell($pdf->width, $pdf->rowHeight, '- 상기 계획에 동의합니다.', 0, 1);

			$pdf->SetX($pdf->left);
			$pdf->Cell($pdf->width * 0.5, $pdf->rowHeight, '- 수급자(보호자) : '.$client[0].'   인', 0, 0);
			$pdf->Cell($pdf->width * 0.5, $pdf->rowHeight, '- 요양보호사 : '.($client[1] != '' ? $client[1] : '            ').'   인', 0, 1, 'R');
		}

		$pdf->SetTextColor(0,0,0);

		if ($useType == 'y'){
			// 관리자용 출력부분
			if ($pdf->GetY()+$height> $pdf->height){
				$pdf->AddPage(strtoupper($page_pl), 'A4');
				$top = 35;
			}else{
				$top = $pdf->GetY()+2;
			}

			if ($type == 's'){
				// 수급자
				$pdf->SetXY($pdf->left, $top);
				$pdf->Cell($pdf->width*0.12,	$height, "급여종류",		 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.215,	$height, "서비스(서비스명)", 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.085,	$height, "횟수",			 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.130,	$height, "시간",			 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.150,	$height, "수가",			 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.150,	$height, "총급여비용",		 1, 0, 'C', true);
				$pdf->Cell($pdf->width*0.150,	$height, "본인부담액",		 1, 1, 'C', true);

				$sql = '';

				foreach($svcParam as $i => $svc){
					$svc = explode('_', $svc);

					$sql .= (!empty($sql) ? ' union all ' : '');
					$sql .= 'select t01_mkind as kind
							 ,      t01_sugup_date as dt
							 ,      t01_svc_subcode as svc_cd
							 ,      case when t01_svc_subcode = \'200\' then \'방문요양\'
										 when t01_svc_subcode = \'500\' then \'방문목욕\'
										 when t01_svc_subcode = \'800\' then \'방문간호\'
										 when t01_svc_subcode > \'20\' and t01_svc_subcode < \'30\' then \'바우처\'
										 when t01_svc_subcode > \'30\' and t01_svc_subcode < \'40\' then \'기타유료\' else \'-\' end as svc_nm
							 ,      t01_suga as suga_pay
							 ,      '.$colSugaCD.' as suga_cd
							 ,      '.$colSugaVL.' as suga_val
							 ,	    '.$colSoyoTM.' as soyotime
							 ,      t01_bipay_umu as bipay_yn
							   from t01iljung
							  where t01_ccode     = \''.$code.'\'
								and t01_jumin     = \''.$target.'\'
								and t01_del_yn    = \'N\'
								and '.$colDT.' like \''.$date.'%\' '.$family_sql;

					if ($code == '31141000043' /* 예사랑 */)
						$sql .= ' and t01_bipay_umu != \'Y\'';

					if (!empty($svc[0]))
						$sql .= ' and t01_mkind = \''.$svc[0].'\'';

					if (!empty($svc[1]))
						$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';
				}


				$sql = 'select t.svc_nm
						,      s.suga_nm
						,      count(t.suga_cd) as cnt
						,      t.suga_pay
						,      sum(t.suga_val) as val
						,      case when t.bipay_yn = \'Y\' then sum(t.suga_val) else (t.suga_val * c.rate / 100) * count(t.suga_cd) end as expenses
						,      t.soyotime
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
						group by t.svc_nm, s.suga_nm, t.suga_val, t.soyotime
						order by t.svc_cd';


				/*********************************************************
				$sql = "select case when t01_svc_subcode = '200' then '방문요양'
							when t01_svc_subcode = '500' then '방문목욕'
							when t01_svc_subcode = '800' then '방문간호'
							when t01_svc_subcode > '20' and t01_svc_subcode < '30' then '바우처'
							when t01_svc_subcode > '30' and t01_svc_subcode < '40' then '기타유료'
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

				if ($code == '31141000043'){ //예사랑
					$sql .= " and t01_bipay_umu != 'Y'";
				}

				$sql .= " group by t01_svc_subcode, m01_suga_cont, $colSugaCD, $colSugaVL
						  order by t01_svc_subcode";
				*********************************************************/


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
						$pdf->Cell($pdf->width*0.12,	$height, "급여종류",		 1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.215,	$height, "서비스(서비스명)", 1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.085,	$height, "횟수",			 1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.130,	$height, "시간",			 1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.150,	$height, "수가",			 1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.150,	$height, "총급여비용",		 1, 0, 'C', true);
						$pdf->Cell($pdf->width*0.150,	$height, "본인부담액",		 1, 1, 'C', true);
					}

					$sugaTime = $myF->euckr($myF->_min2timeKor($row[2] * $row[6]));

					$pdf->SetX($pdf->left);
					$pdf->Cell($pdf->width*0.12,	$height, $row[0],	1, 0, 'C');
					$pdf->Cell($pdf->width*0.215,	$height, $row[1],	1, 0, 'L');
					$pdf->Cell($pdf->width*0.085,	$height, $row[2],	1, 0, 'R');
					$pdf->Cell($pdf->width*0.130,	$height, $sugaTime,	1, 0, 'R');
					$pdf->Cell($pdf->width*0.150,	$height, number_format($row[3]),	1, 0, 'R');
					$pdf->Cell($pdf->width*0.150,	$height, number_format($row[4]),	1, 0, 'R');
					$pdf->Cell($pdf->width*0.150,	$height, number_format($myF->cutOff($row[5])),	1, 1, 'R');

					$suga  += $row[3];
					$total += $row[4];
					$bonin += $myF->cutOff($row[5]);

					$totSugaCnt  += $row[2];
					$totSugaTime += ($row[2] * $row[6]);
				}

				$pdf->SetX($pdf->left);
				$pdf->Cell($pdf->width*0.12,	$height, "합      계",	1, 0, 'C');
				$pdf->Cell($pdf->width*0.215,	$height, "",	1, 0, 'C');
				$pdf->Cell($pdf->width*0.085,	$height, number_format($totSugaCnt),	1, 0, 'R');
				$pdf->Cell($pdf->width*0.130,	$height, $myF->euckr($myF->_min2timeKor($totSugaTime)),	1, 0, 'R');
				$pdf->Cell($pdf->width*0.150,	$height, number_format($suga),	1, 0, 'R');
				$pdf->Cell($pdf->width*0.150,	$height, number_format($total),	1, 0, 'R');
				$pdf->Cell($pdf->width*0.150,	$height, number_format($bonin),	1, 1, 'R');

				$conn->row_free();
			}else{
				if ($code == '31141000043' /* 예사랑 */){
				}else{
					// 요양보호사
					$pdf->SetXY($pdf->left, $top);
					$pdf->Cell($pdf->width*0.15,	$height, "급여종류",				1, 0, 'C', true);
					//$pdf->Cell(30,	$height, "수급자",					1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.28,	$height, "서비스(서비스명)", 1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.10,	$height, "횟수", 1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.15,	$height, "시간", 1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.16,	$height, "수가",					1, 0, 'C', true);
					$pdf->Cell($pdf->width*0.16,	$height, "총급여비용",				1, 1, 'C', true);

					$sql = '';

					foreach($svcParam as $i => $svc){
						$svc = explode('_', $svc);

						$sql .= (!empty($sql) ? ' union all ' : '');
						$sql .= 'select t01_mkind as kind
								 ,      t01_sugup_date as dt
								 ,      t01_svc_subcode as svc_cd
								 ,      case when t01_svc_subcode = \'200\' then \'방문요양\'
											 when t01_svc_subcode = \'500\' then \'방문목욕\'
											 when t01_svc_subcode = \'800\' then \'방문간호\'
											 when t01_svc_subcode > \'20\' and t01_svc_subcode < \'30\' then \'바우처\'
											 when t01_svc_subcode > \'30\' and t01_svc_subcode < \'40\' then \'기타유료\' else \'-\' end as svc_nm
								 ,      '.$colSugaCD.' as suga_cd
								 ,      '.$colSugaVL.' as suga_val
								 ,	    '.$colSoyoTM.' as soyotime
								 ,      t01_bipay_umu as bipay_yn
								   from t01iljung
								  where t01_ccode      = \''.$code.'\'
									and t01_del_yn     = \'N\'
									and '.$colMemCD1.' = \''.$target.'\'
									and '.$colDT.'  like \''.$date.'%\' '.$family_sql;

						if ($code == '31141000043' /* 예사랑 */)
							$sql .= ' and t01_bipay_umu != \'Y\'';

						if (!empty($svc[0]))
							$sql .= ' and t01_mkind = \''.$svc[0].'\'';

						if (!empty($svc[1]))
							$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';


						$sql .= ' union all
								 select t01_mkind as kind
								 ,      t01_sugup_date as dt
								 ,      t01_svc_subcode as svc_cd
								 ,      case when t01_svc_subcode = \'200\' then \'방문요양\'
											 when t01_svc_subcode = \'500\' then \'방문목욕\'
											 when t01_svc_subcode = \'800\' then \'방문간호\'
											 when t01_svc_subcode > \'20\' and t01_svc_subcode < \'30\' then \'바우처\'
											 when t01_svc_subcode > \'30\' and t01_svc_subcode < \'40\' then \'기타유료\' else \'-\' end as svc_nm
								 ,      '.$colSugaCD.' as suga_cd
								 ,      '.$colSugaVL.' as suga_val
								 ,	    '.$colSoyoTM.' as soyotime
								 ,      t01_bipay_umu as bipay_yn
								   from t01iljung
								  where t01_ccode      = \''.$code.'\'
									and t01_del_yn     = \'N\'
									and '.$colMemCD2.' = \''.$target.'\'
									and '.$colDT.'  like \''.$date.'%\' '.$family_sql;

						if ($code == '31141000043' /* 예사랑 */)
							$sql .= ' and t01_bipay_umu != \'Y\'';

						if (!empty($svc[0]))
							$sql .= ' and t01_mkind = \''.$svc[0].'\'';

						if (!empty($svc[1]))
							$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';
					}

					$sql = 'select t.svc_nm
							,      s.suga_nm
							,      count(t.suga_cd) as cnt
							,      t.suga_val
							,      sum(t.suga_val) as val
							,      t.kind
							,      t.soyotime
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
							group by t.svc_nm, s.suga_nm, t.suga_val, t.soyotime
							order by t.svc_cd';

					/*********************************************************
					$sql = "select t01_svc_subcode
							,      m01_suga_cont
							,      sum(cnt) as cnt
							,      $colSugaVL
							,      sum(tot_amt)
							,      t01_mkind
							,      $colSoyoTM
							  from (
								   select case when t01_svc_subcode = '200' then '방문요양'
											   when t01_svc_subcode = '500' then '방문목욕'
											   when t01_svc_subcode = '800' then '방문간호'
											   when t01_svc_subcode > '20' and t01_svc_subcode < '30' then '바우처'
											   when t01_svc_subcode > '30' and t01_svc_subcode < '40' then '기타유료' else '-' end as t01_svc_subcode
								   ,      m01_suga_cont
								   ,      count($colSugaCD) as cnt
								   ,      $colSugaVL
								   ,      sum($colSugaVL) as tot_amt
								   ,      t01_mkind
								   ,      $colSoyoTM
									 from t01iljung
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
									   on t01_mkind = m01_mkind
									  and $colSugaCD = m01_mcode2
									  and $colDT between m01_sdate and m01_edate
									where t01_ccode = '$code'
									  and $colDT like '$date%' $family_sql
									  and $colMemCD1 = '$target'
									  and t01_del_yn = 'N'
									group by t01_svc_subcode, $colSugaCD, $colSoyoTM
									union all
								   select case when t01_svc_subcode = '200' then '방문요양'
											   when t01_svc_subcode = '500' then '방문목욕'
											   when t01_svc_subcode = '800' then '방문간호'
											   when t01_svc_subcode > '20' and t01_svc_subcode < '30' then '바우처'
											   when t01_svc_subcode > '30' and t01_svc_subcode < '40' then '기타유료' else '-' end as t01_svc_subcode
								   ,      m01_suga_cont
								   ,      count($colSugaCD) as cnt
								   ,      $colSugaVL
								   ,      sum($colSugaVL) as tot_amt
								   ,      t01_mkind
								   ,      $colSoyoTM
									 from t01iljung
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
									   on t01_mkind = m01_mkind
									  and $colSugaCD = m01_mcode2
									  and $colDT between m01_sdate and m01_edate
									where t01_ccode = '$code'
									  and $colDT like '$date%' $family_sql
									  and $colMemCD2 = '$target'
									  and t01_del_yn = 'N'
									group by t01_svc_subcode, $colSugaCD, $colSoyoTM
								   ) as t
							 group by t01_svc_subcode, m01_suga_cont, $colSugaVL, $colSoyoTM
							 order by t01_mkind, t01_svc_subcode, m01_suga_cont";
					*********************************************************/

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
							$pdf->Cell($pdf->width*0.15,	$height, "급여종류",				1, 0, 'C', true);
							//$pdf->Cell(30,	$height, "수급자",					1, 0, 'C', true);
							$pdf->Cell($pdf->width*0.28,	$height, "서비스(서비스명)", 1, 0, 'C', true);
							$pdf->Cell($pdf->width*0.10,	$height, "횟수", 1, 0, 'C', true);
							$pdf->Cell($pdf->width*0.15,	$height, "시간", 1, 0, 'C', true);
							$pdf->Cell($pdf->width*0.16,	$height, "수가",					1, 0, 'C', true);
							$pdf->Cell($pdf->width*0.16,	$height, "총급여비용",				1, 1, 'C', true);
						}

						$sugaTime = $myF->euckr($myF->_min2timeKor($row[2] * $row[6]));

						$pdf->SetX($pdf->left);
						$pdf->Cell($pdf->width*0.15,	$height, $row[0],	1, 0, 'C');
						//$pdf->Cell(30,	$height, $row[1],	1, 0, 'L');
						$pdf->Cell($pdf->width*0.28,	$height, $row[1],	1, 0, 'L');
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
					$pdf->Cell($pdf->width*0.15,	$height, "합      계",	1, 0, 'C');
					$pdf->Cell($pdf->width*0.28,	$height, "",	1, 0, 'C');
					$pdf->Cell($pdf->width*0.10,	$height, number_format($totSugaCnt),	1, 0, 'R');
					$pdf->Cell($pdf->width*0.15,	$height, $myF->euckr($myF->_min2timeKor($totSugaTime)),	1, 0, 'R');
					$pdf->Cell($pdf->width*0.16,	$height, number_format($suga),	1, 0, 'R');
					$pdf->Cell($pdf->width*0.16,	$height, number_format($total),	1, 0, 'R');

					$conn->row_free();
				}
			}
		}

		/*********************************************************

			직인출력

		*********************************************************/
		if ($type == 's' && $dis_yn == 'Y'){
			$print_dt = explode('-', $printDT);

			$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
			$pdf->Cell($pdf->width, $pdf->rowHeight, $print_dt[0].'년 '.$print_dt[1].'월 '.$print_dt[2].'일', 0, 1, 'C');

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
			$pdf->Cell($pdf->width * 0.5, $pdf->rowHeight, '기관장 : '.$manager.'                            ', 0, 1, 'R');
			$pdf->SetXY($pdf->left + $pdf->width * 0.5, $pdf->GetY() + 2);
			$pdf->Cell($pdf->width * 0.5, $pdf->rowHeight, '수급자 : '.$pdf->name.'       (서 명 또는 인)', 0, 1, 'R');



			$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
			$pdf->Cell($pdf->width, $pdf->rowHeight, '※ 매월 작성하여 기관 보관.(보관기간 : 작성일로부터 3년)', 0, 1, 'L');
			$pdf->SetX($pdf->left);
			$pdf->Cell($pdf->width, $pdf->rowHeight, '※ 활동지원기관 및 활동보조인과 수급자 및 보호자(가족)이 협의하여 매월 5일 이전까지 작성', 0, 1, 'L');
			$pdf->SetX($pdf->left);
			$pdf->Cell($pdf->width, $pdf->rowHeight, '※ 2인이상 서비스를 제공할 경우 활동지원인력명에 이름을 쓰고 제공한 날에 표시한다.', 0, 1, 'L');
		}

		usleep(10000);
	}


	$pdf->Output();

	include('../inc/_db_close.php');

	// 달력의 요일별 라인을 그린다.
	function drawLine($pdf, $col, $top){
		$pdf->SetLineWidth(0.2);

		$left = $pdf->left;

		for($i=0; $i<7; $i++){
			$left += $col['w'][$i];
			$pdf->Line($left, $pdf->top+15, $left, $top);
		}
	}

	// 달력의 전체 테두리를 그린다.
	function drawBorder($pdf, $height){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $pdf->top+15, $pdf->width, $height);
		$pdf->SetLineWidth(0.2);
	}

	//수급자 욕구상담
	function setDesire($pdf, $basic_x, $basic_y, $counsel){
		$coord_y = $basic_y;

		//echo htmlspecialchars($counsel['stat']).'<br><br>';

		//내용을 가공
		$counsel_str = trim($counsel);
		$counsel_str = str_replace('&nbsp;', '', $counsel_str);
		$counsel_str = str_replace('<P>', '', $counsel_str);
		$counsel_str = str_replace('</P>', '</P>[NEW]</P>', $counsel_str);
		$counsel_str = str_replace('">', '</P>', $counsel_str);
		$counsel_str = str_replace('<SPAN style="COLOR: ', '</P>', $counsel_str);
		$counsel_str = str_replace('</SPAN>', '</P>#000000</P>', $counsel_str);

		//echo htmlspecialchars($counsel_str).'<br><br>';

		$counsel_arr = explode('</P>', $counsel_str);

		//좌표설정
		$pdf->SetXY($basic_x, $coord_y);

		$y = $coord_y + 5;

		//내용작성
		for($i=0; $i<sizeof($counsel_arr); $i++){
			$counsel_txt = trim($counsel_arr[$i]);

			//echo $counsel_txt.'<br>';

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
					$y += 5;
					$new_flag = true;
					break;
				}else{
					$str = mb_substr($counsel_txt, $j, 1, 'euckr');
					$txt_width = $pdf->GetStringWidth($str);
					$w += $txt_width;

					if ($w >= $pdf->width * 0.27 - 1){
						$y += 5;
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