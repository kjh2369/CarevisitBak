<?
	include_once('../../inc/_db_open.php');
	include_once('../../inc/_function.php');
	include_once('../../inc/_myFun.php');
	include_once('../../inc/_ed.php');
	include_once('../../inc/_definition.php');

	/*********************************************************

		파라메타

	*********************************************************/
	
	$var['root'] = $_GET['root'];
	$var['dir'] = $_GET['dir'];
	$var['code'] = $_GET['code'];
	$var['mode'] = $_GET['mode'];
	$var['type'] = $_GET['type'];
	$var['fileType'] = $_GET['fileType'];
	$var['year'] = $_GET['year'];
	$var['month'] = $_GET['month'];
	$var['key'] = $_GET['key'];
	$var['mobileYn'] = $_GET['mobileYn'];
	$var['opt1'] = $_GET['opt1'];
	
	

	/**************************************************

		PDF 헤더

	**************************************************/
	if (strtoupper($var['dir']) == 'L'){
		$paperDir = 'l';
	}else{
		$paperDir = 'p';
	}

	//require_once('./show_header.php');


	//if($debug) print_r($_POST['para']);


	/**************************************************

		기본설정

	**************************************************/
	#케릭터변경
	$conn->set_name('euckr');
	

	#기관명
	$center_nm = $conn->center_name($var['code']);


	/**************************************************

		기관정보 조회

	**************************************************/
	$sql = 'select m00_code1 as cd
			,      m00_cname as nm
			,      m00_mname as mm
			,      m00_ccode as no
			/*,      concat(substring(m00_cpostno,1,3),\'-\',substring(m00_cpostno,4,3)) as postno*/
			,	   m00_cpostno as postno
			,      concat(m00_caddr1,\' \',m00_caddr2) as addr
			,      m00_ctel as tel
			,      m00_bank_name as bank_nm
			,      m00_bank_no as bank_no
			,      m00_bank_depos as bank_owner
			  from m00center
			 where m00_mcode = \''.$var['code'].'\'';

		if($var['find_type']){
			$sql .= ' and m00_mkind =   \''.$var['find_type'].'\'';
		}else if($var['kind']){
			$sql .= ' and m00_mkind =   \''.$var['kind'].'\'';
		}else{
			$sql .= ' order by m00_mkind
					  limit 1';
		}
	
	$ct_if = $conn->get_array($sql);
	
	$ct_if['bank_nm'] = $myF->euckr($definition->GetBankName($ct_if['bank_nm']));

	/*********************************************************

		출력 리스트

	*********************************************************/
	if ($var['jumin'] == 'all'){
		$sql = 'select distinct t13_jumin as cd, m03_name as nm
				  from t13sugupja
				 inner join m03sugupja
					on m03_ccode = t13_ccode
				   and m03_mkind = t13_mkind
				   and m03_jumin = t13_jumin
				 where t13_ccode    = \''.$var['code'].'\'';

		if($var['find_type'] != ''){
			$sql .=	'  and t13_mkind    = \''.$var['find_type'].'\'';
		}

		$sql .= '  and t13_pay_date = \''.$var['year'].$var['month'].'\'
				   and t13_type     = \'2\'
				 order by nm';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$printList[$i] = $row['cd'];
		}

		$conn->row_free();
	}else if (is_numeric($var['key'])){
		$sql = 'select m03_jumin
				  from m03sugupja
				 where m03_ccode = \''.$var['code'].'\'
				   and m03_key   = \''.$var['key'].'\'
				 limit 1';
		$printList[0] = $conn->get_data($sql);
	}else if ($var['jumin'] == 'null'){
		$printList[0] = '';
	}else{
		$printList[0] = $ed->de($var['jumin']);
	}


	/**************************************************

		PDF OPEN

	**************************************************/
	$pdf->SetFillColor(220,220,220);




	/*********************************************************

		기본폰트설정

	*********************************************************/
	$fontType1 = array('name'=>$pdf->font_name_kor,'bold'=>'','size'=>10);
	$fontType2 = array('name'=>$pdf->font_name_kor,'bold'=>'B','size'=>11);
	$fontType3 = array('name'=>$pdf->font_name_kor,'bold'=>'B','size'=>12);




	/**************************************************

		고객정보 및 급여내역 조회

	**************************************************/
	$kind_list = $conn->kind_list($var['code']);
	
	foreach($printList as $printIndex => $jumin){
		$sql   = '';

		foreach($kind_list as $i => $k){
			if ($k['code'] == '6'){
				//재가지원
			}else{
				$sql .= (!empty($sql) ? ' union all ' : '');
				$sql .= 'select t13_ccode as k_cd
						 ,      t13_mkind as k_kind
						 ,      t13_jumin as c_cd';

				if ($k['code'] > '0') $sql .= ', 0 as svc0_bonin, 0 as svc0_over, 0 as svc0_public, 0 as svc0_suga';
				if ($k['code'] > '1') $sql .= ', 0 as svc1_bonin, 0 as svc1_over, 0 as svc1_public, 0 as svc1_suga';
				if ($k['code'] > '2') $sql .= ', 0 as svc2_bonin, 0 as svc2_over, 0 as svc2_public, 0 as svc2_suga';
				if ($k['code'] > '3') $sql .= ', 0 as svc3_bonin, 0 as svc3_over, 0 as svc3_public, 0 as svc3_suga';
				if ($k['code'] > '4') $sql .= ', 0 as svc4_bonin, 0 as svc4_over, 0 as svc4_public, 0 as svc4_suga';
				if ($k['code'] > 'A') $sql .= ', 0 as svcA_bonin, 0 as svcA_over, 0 as svcA_public, 0 as svcA_suga';
				if ($k['code'] > 'B') $sql .= ', 0 as svcB_bonin, 0 as svcB_over, 0 as svcB_public, 0 as svcB_suga';

				if ($var['code'] == '31141000043' /* 예사랑 */){
					$sql .= ', sum(t13_bonin_amt4) as svc'.$k['code'].'_bonin
							 , 0 as svc'.$k['code'].'_over
							 , '.($var['opt1'] == 'Y' ? 'sum(t13_chung_amt4)' : '0').' as svc'.$k['code'].'_public
							 , '.($var['opt1'] == 'Y' ? 'sum(t13_suga_tot4 - t13_bipay4)' : 'sum(t13_bonin_amt4)').' as svc'.$k['code'].'_suga';
				}else{
					$sql .= ', sum(t13_bonin_amt4) as svc'.$k['code'].'_bonin
							 , sum(t13_over_amt4'.($var['bipayYn'] == 'Y' ? ' + t13_bipay4' : '').') as svc'.$k['code'].'_over
							 , '.($var['opt1'] == 'Y' ? 'sum(t13_chung_amt4)' : '0').' as svc'.$k['code'].'_public
							 , '.($var['opt1'] == 'Y' ? 'sum(t13_suga_tot4 - t13_over_amt4 - t13_bipay4)' : 'sum(t13_bonin_amt4)').'  as svc'.$k['code'].'_suga';
				}

				if ($k['code'] < '1') $sql .= ', 0 as svc1_bonin, 0 as svc1_over, 0 as svc1_public, 0 as svc1_suga';
				if ($k['code'] < '2') $sql .= ', 0 as svc2_bonin, 0 as svc2_over, 0 as svc2_public, 0 as svc2_suga';
				if ($k['code'] < '3') $sql .= ', 0 as svc3_bonin, 0 as svc3_over, 0 as svc3_public, 0 as svc3_suga';
				if ($k['code'] < '4') $sql .= ', 0 as svc4_bonin, 0 as svc4_over, 0 as svc4_public, 0 as svc4_suga';
				if ($k['code'] < 'A') $sql .= ', 0 as svcA_bonin, 0 as svcA_over, 0 as svcA_public, 0 as svcA_suga';
				if ($k['code'] < 'B') $sql .= ', 0 as svcB_bonin, 0 as svcB_over, 0 as svcB_public, 0 as svcB_suga';
				if ($k['code'] < 'C') $sql .= ', 0 as svcC_bonin, 0 as svcC_over, 0 as svcC_public, 0 as svcC_suga';

				$sql .= ',      concat(t13_pay_date,\'-\',t13_bill_no) as bill_no
						   from t13sugupja
						  where t13_ccode    = \''.$var['code'].'\'
							and t13_mkind    = \''.$k['code'].'\'
							and t13_pay_date = \''.$var['year'].$var['month'].'\'
							and t13_jumin    = \''.$jumin.'\'
							and t13_type     = \'2\'
						  group by t13_ccode, t13_mkind, t13_jumin, t13_mkind, t13_pay_date, t13_bill_no';
			}
		}

		$sql = 'select c_cd
				,      m03_name as c_nm
				,      replace(lvl.app_no, \'-\', \'\') as c_no

				,      sum(svc0_bonin) as svc0_bonin, sum(svc0_over) as svc0_over, sum(svc0_public) as svc0_public, sum(svc0_suga) as svc0_suga
				,      sum(svc1_bonin) as svc1_bonin, sum(svc1_over) as svc1_over, sum(svc1_public) as svc1_public, sum(svc1_suga) as svc1_suga
				,      sum(svc2_bonin) as svc2_bonin, sum(svc2_over) as svc2_over, sum(svc2_public) as svc2_public, sum(svc2_suga) as svc2_suga
				,      sum(svc3_bonin) as svc3_bonin, sum(svc3_over) as svc3_over, sum(svc3_public) as svc3_public, sum(svc3_suga) as svc3_suga
				,      sum(svc4_bonin) as svc4_bonin, sum(svc4_over) as svc4_over, sum(svc4_public) as svc4_public, sum(svc4_suga) as svc4_suga
				,      sum(svcA_bonin) as svcA_bonin, sum(svcA_over) as svcA_over, sum(svcA_public) as svcA_public, sum(svcA_suga) as svcA_suga
				,      sum(svcB_bonin) as svcB_bonin, sum(svcB_over) as svcB_over, sum(svcB_public) as svcB_public, sum(svcB_suga) as svcB_suga
				,      sum(svcC_bonin) as svcC_bonin, sum(svcC_over) as svcC_over, sum(svcC_public) as svcC_public, sum(svcC_suga) as svcC_suga

				,     (select ifnull(sum(t13_bonbu_tot4), 0)
						 from t13sugupja
						where t13_ccode = k_cd
						  and t13_jumin = c_cd
						  and t13_type  = \'2\'
						  and t13_pay_date <= \''.$var['year'].$var['month'].'\')
				-     (select ifnull(sum(deposit_amt), 0)
						 from unpaid_deposit
						where org_no        = k_cd
						  and deposit_jumin = c_cd
						  and del_flag      = \'N\') as unpaid
				,      bill_no
				  from ('.$sql.') as t
				 inner join m03sugupja
					on m03_ccode = k_cd
				   and m03_mkind = k_kind
				   and m03_jumin = c_cd
				  left join	(select DISTINCT org_no
							 ,		jumin
							 ,      svc_cd
							 ,      app_no
							   from client_his_lvl
							  where org_no = \''.$var['code'].'\'
								and date_format(from_dt,\'%Y%m\') <= \''.$var['year'].$var['month'].'\'
								and date_format(to_dt,  \'%Y%m\') >= \''.$var['year'].$var['month'].'\'
							 ) as lvl
					on lvl.org_no = k_cd
				   and lvl.svc_cd = k_kind
				   and lvl.jumin  = c_cd
				 group by c_cd
				 order by c_cd';

		$svc_dt = $conn->get_array($sql);

		if (StrLen($svc_dt['c_no']) == 11){
			$svc_dt['c_no'] = SubStr($svc_dt['c_no'],0,6).'*****';
		}


		//급여제공기간
		$sql = 'select min(t01_sugup_date)
				,      max(t01_sugup_date)
				  from t01iljung
				 where t01_ccode               = \''.$var['code'].'\'
				/*AND		t01_mkind	= \''.$k['code'].'\'*/
				   and t01_jumin               = \''.$jumin.'\'
				   and left(t01_sugup_date, 6) = \''.$var['year'].$var['month'].'\'
				   and t01_del_yn              = \'N\'';
		//if($debug) echo nl2br($sql); exit;
		$tmp = $conn->get_array($sql);
		$svc_dt['min_dt'] = $tmp[0];
		$svc_dt['max_dt'] = $tmp[1];

		unset($tmp);


		//입금정보
		$sql = 'select case cd when \'카드\' then 1 when \'현금영수증\' then 2 else 3 end as id, cd, sum(pay) as pay, no, max(ent_dt) as ent_dt
				  from (
						select case unpaid_deposit.deposit_type when \'01\' then \'현금\'
																when \'02\' then \'현금\'
																when \'03\' then \'현금\'
																when \'04\' then \'카드\'
																when \'05\' then \'현금\'
																when \'06\' then \'현금영수증\' else \'현금\' end as cd
						,      unpaid_deposit.deposit_amt as pay
						,      unpaid_deposit.cash_bill_no as no
						,      unpaid_deposit.deposit_reg_dt as ent_dt
						  from unpaid_deposit
						 inner join unpaid_deposit_list
							on unpaid_deposit_list.org_no         = unpaid_deposit.org_no
						   and unpaid_deposit_list.deposit_ent_dt = unpaid_deposit.deposit_ent_dt
						   and unpaid_deposit_list.deposit_seq    = unpaid_deposit.deposit_seq
						 where unpaid_deposit.org_no              = \''.$var['code'].'\'
						   and unpaid_deposit.deposit_jumin       = \''.$jumin.'\'
						   and unpaid_deposit.del_flag            = \'N\'
						   and unpaid_deposit_list.unpaid_yymm    = \''.$var['year'].$var['month'].'\'
					   ) as t
				 group by id, cd, no
				 order by id';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$in_pay[$row['id']] = array('cd'=>$row['cd'], 'pay'=>$row['pay'], 'no'=>$row['no'], 'dt'=>$row['ent_dt']);
		}

		$conn->row_free();


	}

	
?>