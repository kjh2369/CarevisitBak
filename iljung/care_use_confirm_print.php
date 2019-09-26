<?
	
	
	/*********************************************************
		직인
	*********************************************************/
	$sql = 'select m00_jikin
			  from m00center
			 where m00_mcode = \''.$_SESSION['userCenterCode'].'\'
			 order by m00_mkind
			 limit 1';
	$iconJikin = $conn->get_data($sql);

	
	/**************************************************

		급여내역 조회

	**************************************************/
		
	$sql = "select rate
				from client_his_kind
			   where org_no = '".$code."'
			     and jumin  = '".$jumin."'
				 and date_format(from_dt,'%Y%m') <= '".$year.$month."'
				 and date_format(to_dt,'%Y%m') >= '".$year.$month."'
			   order by from_dt desc
			   limit 1";
	
	$rate = $conn -> get_data($sql); 
	
	
	$sql   = '';
	$kind  = 0; 
	$svcCnt = 0;

	foreach($svcGbn as $svcIdx => $svc){
		if ($svc){
			if (Is_Numeric(StrPos($svc,'_'))){
				$tmp = Explode('_',$svc);
				$svcCd = $tmp[0];
				$subCd = $tmp[1];
			}else{
				$svcCd = $svc;
				$subCd = '';
			}
		}
		
		if ($svc == '200' || $svc == '500' || $svc == '800'){
			$svcCnt ++;
		}
	}
	

	if (is_file('../mem_picture/'.$iconJikin)){
		$exp = explode('.',$iconJikin);
		$exp = strtolower($exp[sizeof($exp)-1]);
		if ($exp != 'bmp'){
			$tmpImg = getImageSize('../mem_picture/'.$iconJikin);
			$pdf->Image('../mem_picture/'.$iconJikin, 165, 175, ($side_show ? 16.5 : 25));
		}
	}

	foreach($svcGbn as $svcIdx => $svc){
		
		if ($svc){
			if (Is_Numeric(StrPos($svc,'_'))){
				$tmp = Explode('_',$svc);
				$svcCd = $tmp[0];
				$subCd = $tmp[1];
			}else{
				$svcCd = $svc;
				$subCd = '';
			}
		}	
		
		$sql .= (!empty($sql) ? ' union all ' : '');
					$sql .= 'select t13_ccode as k_cd
							 ,      t13_mkind as k_kind
							 ,      t13_jumin as c_cd';
		
		//$sql .= ', 0 as svc0_bonin, 0 as svc0_over, 0 as svc0_public, 0 as svc0_suga';
		
		$opt1 = 'Y';
		$bipayYn = 'Y';
		
		

		if($svcCd != 'all'){
			if ($svcCd == '200' || $svcCd == '500' || $svcCd == '800'){
					
				switch($svcCd){
					case '200':
						$lsVal = '1';
						break;

					case '500':
						$lsVal = '2';
						break;

					case '800':
						$lsVal = '3';
						break;

					default:
						$lsVal = '4';
				}
				
				if($svcCnt > 1 ){
					$sql .= ', sum(t13_bonin_amt4) as svc0_bonin
					 , sum(t13_over_amt4'.($bipayYn == 'Y' ? ' + t13_bipay4' : '').') as svc0_over
					 , '.($opt1 == 'Y' ? 'sum(t13_chung_amt4)' : '0').' as svc0_public
					 , '.($opt1 == 'Y' ? 'sum(t13_suga_tot4 - t13_over_amt4 - t13_bipay4)' : 'sum(t13_bonin_amt4)').'  as svc0_suga';
				}else {				
					$sql .= ', sum(t13_bonin_amt'.$lsVal.') as svc0_bonin
							 , sum(t13_over_amt'.$lsVal.($bipayYn == 'Y' ? ' + t13_bipay'.$lsVal : '').') as svc0_over
							 , '.($opt1 == 'Y' ? 'sum(t13_chung_amt'.$lsVal.')' : '0').' as svc0_public
							 , '.($opt1 == 'Y' ? 'sum(t13_suga_tot'.$lsVal.' - t13_over_amt'.$lsVal.' - t13_bipay'.$lsVal.')' : 'sum(t13_bonin_amt'.$lsVal.')').'  as svc0_suga';
				}	

			}
		}else{
			
			$sql .= ', sum(t13_bonin_amt4) as svc0_bonin
					 , sum(t13_over_amt4'.($bipayYn == 'Y' ? ' + t13_bipay4' : '').') as svc0_over
					 , '.($opt1 == 'Y' ? 'sum(t13_chung_amt4)' : '0').' as svc0_public
					 , '.($opt1 == 'Y' ? 'sum(t13_suga_tot4 - t13_over_amt4 - t13_bipay4)' : 'sum(t13_bonin_amt4)').'  as svc0_suga';
			
		}
		
		$sql .= ',      concat(t13_pay_date,\'-\',t13_bill_no) as bill_no
				   from t13sugupja
				  where t13_ccode    = \''.$code.'\'
					and t13_mkind    = \''.$kind.'\'
					and t13_pay_date = \''.$year.$month.'\'
					and t13_jumin    = \''.$jumin.'\'
					and t13_type     = \'2\'
				  group by t13_ccode, t13_mkind, t13_jumin, t13_mkind, t13_pay_date, t13_bill_no';
		
		$kind ++;
		
	}
	
	$sql = 'select c_cd
			,      m03_name as c_nm
			,      lvl.app_no as c_no

			,      sum(svc0_bonin) as svc0_bonin, sum(svc0_over) as svc0_over, sum(svc0_public) as svc0_public, sum(svc0_suga) as svc0_suga

			,     (select ifnull(sum(t13_bonbu_tot4), 0)
					 from t13sugupja
					where t13_ccode = k_cd
					  and t13_jumin = c_cd
					  and t13_type  = \'2\')
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
			  left join	(select distinct
								org_no
						 ,		jumin
						 ,      svc_cd
						 ,      app_no
						   from client_his_lvl
						  where org_no = \''.$code.'\'
							and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
							and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
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

	
	//입금정보
	$sql = 'select case cd when \'카드\' then 1 when \'현금영수증\' then 2 else 3 end as id, cd, sum(pay) as pay, no, max(ent_dt) as ent_dt, bank_nm
			  from (
					select case unpaid_deposit.deposit_type when \'02\' then \'계좌\'
															when \'01\' then \'현금\'
															when \'05\' then \'CMS\'
															when \'04\' then \'카드\'
															when \'03\' then \'지로\' else \'현금\' end as cd
					,      unpaid_deposit_list.deposit_amt as pay
					,      unpaid_deposit.cash_bill_no as no
					,      unpaid_deposit.deposit_reg_dt as ent_dt
					,	   unpaid_deposit.other
					,	   unpaid_deposit.bank_nm
					  from unpaid_deposit
					 inner join unpaid_deposit_list
						on unpaid_deposit_list.org_no         = unpaid_deposit.org_no
					   and unpaid_deposit_list.deposit_ent_dt = unpaid_deposit.deposit_ent_dt
					   and unpaid_deposit_list.deposit_seq    = unpaid_deposit.deposit_seq
					 where unpaid_deposit.org_no              = \''.$code.'\'
					   and unpaid_deposit.deposit_jumin       = \''.$jumin.'\'
					   and unpaid_deposit_list.unpaid_yymm    = \''.$year.$month.'\'
					   and unpaid_deposit.deposit_type       != \'99\'
					   and unpaid_deposit.del_flag            = \'N\'
				   ) as t
			 group by id, cd, no
			 order by id';
	
	$deposit = $conn -> get_array($sql);

	//급여제공기간
	$sql = 'select min(t01_sugup_date)
			,      max(t01_sugup_date)
			  from t01iljung
			 where t01_ccode               = \''.$code.'\'
			   and t01_jumin               = \''.$jumin.'\'
			   and left(t01_sugup_date, 6) = \''.$year.$month.'\'
			   and t01_mkind               = \'0\'
			   and t01_del_yn              = \'N\'';
	
	$tmp = $conn->get_array($sql);
	$svc_dt['min_dt'] = $tmp[0];
	$svc_dt['max_dt'] = $tmp[1];
	
	$t_rate = (100-$rate).'.0';

	unset($tmp);
	
	$amt['tot'] = $svc_dt['svc0_suga']+$svc_dt['svc0_over'];
	$amt['my']  = $svc_dt['svc0_bonin']+$svc_dt['svc0_over'];
		

	$tot = $amt['tot'] != '' ? number_format($amt['tot']) : '';
	$my = $amt['my'] != '' ? number_format($amt['my']) : '';
	$public = !empty($svc_dt['svc0_public']) ? number_format($svc_dt['svc0_public']) : '';	//청구액
	$bonin  = !empty($svc_dt['svc0_bonin']) ? number_format($svc_dt['svc0_bonin']) : '';	//본인부담액
	$over  = !empty($svc_dt['svc0_over']) ? number_format($svc_dt['svc0_over']) : '';		//비급여
	

	if($code == '31126000192'){
		//가톨릭노인복지센터
		$method = $deposit['other'];
	}if($code == '34115000403'){
		//희망짓는요양센터
		$method = $deposit['cd'];
	}else {
		$method = $deposit['cd'];
	}

	
	$pdf->SetXY($pdf->left+30, $pdf->GetY()+10);
	$pdf->Cell($pdf->width*0.9, $pdf->row_height, "1. 납부일자 : ".$myF->dateStyle($deposit['ent_dt'],'.'), 0, 1, "L");
	$pdf->SetX($pdf->left+30);
	$pdf->Cell($pdf->width*0.9, $pdf->row_height, "1. 납부금액 : ".($deposit['pay']!=''? number_format($deposit['pay']).' 원':''), 0, 1, "L");
	$pdf->SetX($pdf->left+30);
	$pdf->Cell($pdf->width*0.9, $pdf->row_height, "1. 납부방법 : ".$method, 0, 1, "L");
	
	
	
	$printDt = explode('-', $_POST['printDT']);	


	$pdf->SetXY($pdf->width*0.43, $pdf->GetY()+15);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, $printDt[0], 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "년", 0, 0, "C");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, ($printDt[1]<10 ?str_replace('0','',$printDt[1]) : $printDt[1]), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "월", 0, 0, "C");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, ($printDt[2]<10 ?str_replace('0','',$printDt[2]) : $printDt[2]), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "일", 0, 1, "C");
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "B", 13);
	$pdf->Cell($pdf->width * 0.65, $pdf->row_height, "장기요양기관장", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, $su['manager'], 0, 0, "C");
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, " (인)", 0, 0, "L");
	
	

	set_array_text($pdf, $pos);
	unset($pos);

?>