<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');
	include('../inc/_ed.php');
	
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$conn->begin();
	
	$mCode  = $_POST['mCode'];  //기관코드
	$mKind  = $_POST['mKind'];  //기관분류코드
	$mPayDate = $_POST['confYear'].$_POST['confMonth'];
	$mSugupja = $ed->de($_POST['mSugupja']);

	#계획자료를 저정한다.
	$sql = "select t01_jumin"
		 . ",      t01_svc_subcode"
		 . ",      m03_kupyeo_max"
		 . ",      m03_skind"
		 . ",      m03_bonin_yul"
		 . ",    ((select sum(t01_suga)"
		 . "         from t01iljung"
		 . "        where t01_ccode = '".$mCode
		 . "'         and t01_mkind = '".$mKind
		 . "'         and t01_jumin = '".$mSugupja
		 . "'         and t01_sugup_date between m03_sdate and m03_edate"
		 . "          and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end"
		 . "          and t01_del_yn = 'N') - m03_kupyeo_max) as sugaOver"
		 . ",    ((select sum(t01_conf_suga_value)"
		 . "         from t01iljung"
		 . "        where t01_ccode = '".$mCode
		 . "'         and t01_mkind = '".$mKind
		 . "'         and t01_jumin = '".$mSugupja
		 . "'         and t01_sugup_date between m03_sdate and m03_edate"
		 . "          and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end"
		 . "          and t01_del_yn = 'N') - m03_kupyeo_max) as confSugaOver"
		 . ",      sum(t01_suga) as sugaPay"
		 . ",      sum(case t01_bipay_umu when 'Y' then 0 else t01_suga end) * (m03_bonin_yul / 100) as boninPay"
		 . ",      case when sum(case t01_bipay_umu when 'Y' then 0 else t01_suga end) * (m03_bonin_yul / 100) - sum(t01_suga) > 0 then sum(case t01_bipay_umu when 'Y' then 0 else t01_suga end) * (m03_bonin_yul / 100) - sum(t01_suga) else 0 end as overPay"
		 . ",      sum(case t01_bipay_umu when 'Y' then t01_suga else 0 end) as biPay"
		 . ",      sum(case when t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value else 0 end) as confSugaPay"
		 . ",      sum(case t01_bipay_umu when 'Y' then 0 else case when t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value else 0 end end) * (m03_bonin_yul / 100) as confBoninPay"
		 . ",      case when sum(case t01_bipay_umu when 'Y' then 0 else case when t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value else 0 end end) * (m03_bonin_yul / 100) - sum(case when t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value else 0 end) > 0 then sum(case t01_bipay_umu when 'Y' then 0 else case when t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value else 0 end end) * (m03_bonin_yul / 100) - sum(case when t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value else 0 end) else 0 end as confOverPay"
		 . ",      sum(case t01_bipay_umu when 'Y' then case when t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value else 0 end else 0 end) as confBiPay"
		 . "  from t01iljung as iljung"
		 . " inner join ("
		 . "       select m03_ccode as m03_ccode"
		 . "       ,      m03_mkind as m03_mkind"
		 . "       ,      m03_jumin as m03_jumin"
		 . "       ,      m03_name as m03_name"
		 . "       ,      m03_ylvl as m03_ylvl"
		 . "       ,      m03_skind as m03_skind"
		 . "       ,      m03_bonin_yul as m03_bonin_yul"
		 . "       ,      m03_kupyeo_max as m03_kupyeo_max"
		 . "       ,      m03_sdate as m03_sdate"
		 . "       ,      m03_edate as m03_edate"
		 . "       ,      m03_key   as m03_key"
		 . "         from m03sugupja"
		 . "        where m03_ccode = '".$mCode
		 . "'         and m03_mkind = '".$mKind
		 . "'         and m03_jumin = '".$mSugupja
		 . "'       union all"
		 . "       select m31_ccode as m03_ccode"
		 . "       ,      m31_mkind as m03_mkind"
		 . "       ,      m31_jumin as m03_jumin"
		 . "       ,      m03_name as m03_name"
		 . "       ,      m31_level as m03_ylvl"
		 . "       ,      m31_kind as m03_skind"
		 . "       ,      m31_bonin_yul as m03_bonin_yul"
		 . "       ,      m31_kupyeo_max as m03_kupyeo_max"
		 . "       ,      m31_sdate as m03_sdate"
		 . "       ,      m31_edate as m03_edate"
		 . "       ,      m03_key   as m03_key"
		 . "         from m31sugupja"
		 . "        inner join m03sugupja"
		 . "           on m03_ccode = m31_ccode"
		 . "          and m03_mkind = m31_mkind"
		 . "          and m03_jumin = m31_jumin"
		 . "        where m31_ccode = '".$mCode
		 . "'         and m31_mkind = '".$mKind
		 . "'         and m31_jumin = '".$mSugupja
		 . "'      ) as sugupja"
		 . "    on t01_ccode = m03_ccode"
		 . "   and t01_mkind = m03_mkind"
		 . "   and t01_jumin = m03_jumin"
		 . "   and t01_sugup_date between m03_sdate and m03_edate"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and t01_jumin = '".$mSugupja
		 . "'  and left(t01_sugup_date, 6) = '".$mPayDate
		 . "'  and t01_del_yn = 'N'"
		 . " group by t01_jumin, t01_svc_subcode, m03_kupyeo_max, m03_bonin_yul, m03_skind"
		 . " order by t01_jumin, t01_svc_subcode, m03_skind";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	$tempJumin = '';
	$seq = 0;

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		if ($tempJumin != $row['t01_jumin'].'_'.$row['m03_skind']){
			$tempJumin  = $row['t01_jumin'].'_'.$row['m03_skind'];
			$seq ++;
			$rowI = $seq - 1;
			$rowData[$rowI]['ccode'] = $mCode;
			$rowData[$rowI]['mkind'] = $mKind;
			$rowData[$rowI]['jumin'] = $row['t01_jumin'];
			$rowData[$rowI]['pay_date']   = $mPayDate;
			$rowData[$rowI]['bonin_yul']  = $row['m03_skind'];
			$rowData[$rowI]['max_amt']    = $row['m03_kupyeo_max'];
			$rowData[$rowI]['result_amt'] = 0;
			$rowData[$rowI]['over_amt'] = 0;
			$rowData[$rowI]['totalSuga'] = 0;

			for($j=1; $j<=4; $j++){
				$rowData[$rowI]['suga_tot'.$j]  = 0;
				$rowData[$rowI]['bonin_amt'.$j] = 0;
				$rowData[$rowI]['over_amt'.$j]  = 0;
				$rowData[$rowI]['bipay'.$j]     = 0;
				$rowData[$rowI]['bonbu_tot'.$j] = 0;
				$rowData[$rowI]['chung_amt'.$j] = 0;
			}

			$newI = $seq - 1;
			$newData[$newI]['ccode'] = $mCode;
			$newData[$newI]['mkind'] = $mKind;
			$newData[$newI]['jumin'] = $row['t01_jumin'];
			$newData[$newI]['pay_date']   = $mPayDate;
			$newData[$newI]['bonin_yul']  = $row['m03_skind'];
			$newData[$newI]['max_amt']    = $row['m03_kupyeo_max'];
			$newData[$newI]['result_amt'] = 0;
			$newData[$newI]['over_amt'] = 0;
			$newData[$newI]['totalSuga'] = 0;

			for($j=1; $j<=4; $j++){
				$newData[$newI]['suga_tot'.$j]  = 0;
				$newData[$newI]['bonin_amt'.$j] = 0;
				$newData[$newI]['over_amt'.$j]  = 0;
				$newData[$newI]['bipay'.$j]     = 0;
				$newData[$newI]['bonbu_tot'.$j] = 0;
				$newData[$newI]['chung_amt'.$j] = 0;
			}
		}

		//switch($subCode[$i]){
		switch($row['t01_svc_subcode']){
			case '200': $index = '1'; break;
			case '500': $index = '2'; break;
			case '800': $index = '3'; break;
		}


		if ($rowData[$rowI]['max_amt'] < $rowData[$rowI]['totalSuga'] + $row['sugaPay']){
			if ($rowData[$rowI]['over_amt'] == 0){
				$tempSuga = $newData[$rowI]['max_amt'] - $rowData[$rowI]['totalSuga'];
				$rowData[$rowI]['bonin_amt'.$index] = $tempSuga * ($row['m03_bonin_yul'] / 100);
				$rowData[$rowI]['over_amt'] = $row['sugaPay'] - $tempSuga;
			}else{
				$rowData[$rowI]['over_amt'] = $rowData[$rowI]['over_amt'] + $row['sugaPay'];
			}
		}else{
			$rowData[$rowI]['bonin_amt'.$index] = $row['boninPay'];
		}
		$rowData[$rowI]['totalSuga'] = $rowData[$rowI]['totalSuga'] + $row['sugaPay'];


		$rowData[$rowI]['suga_tot'. $index] = $row['sugaPay'];
		$rowData[$rowI]['bonin_amt'.$index] = $row['boninPay'];
		$rowData[$rowI]['over_amt'. $index] = $row['overPay'];
		$rowData[$rowI]['bipay'.    $index] = $row['biPay'];
		//$rowData[$rowI]['bonbu_tot'.$index] = $row['boninPay'] + $row['overPay'] + $row['biPay'];
		$rowData[$rowI]['bonbu_tot'.$index] = $rowData[$rowI]['bonin_amt'.$index] + $rowData[$rowI]['over_amt'.$index] + $rowData[$rowI]['bipay'.$index];
		$rowData[$rowI]['chung_amt'.$index] = $row['sugaPay'] - $rowData[$rowI]['bonbu_tot'.$index];

		$rowData[$rowI]['bonin_amt'.$index] += ($rowData[$rowI]['chung_amt'.$index] - cutOff($rowData[$rowI]['chung_amt'.$index]));
		$rowData[$rowI]['bonbu_tot'.$index]  = $rowData[$rowI]['over_amt'.$index] + $rowData[$rowI]['bipay'.$index] + $rowData[$rowI]['bonin_amt'.$index];
		$rowData[$rowI]['chung_amt'.$index]  = cutOff($rowData[$rowI]['chung_amt'.$index]);

		$rowData[$rowI]['suga_tot4']  += $rowData[$rowI]['suga_tot'. $index];
		$rowData[$rowI]['bonin_amt4'] += $rowData[$rowI]['bonin_amt'.$index];
		$rowData[$rowI]['over_amt4']  += $rowData[$rowI]['over_amt'. $index];
		$rowData[$rowI]['bipay4']     += $rowData[$rowI]['bipay'.    $index];
		$rowData[$rowI]['bonbu_tot4'] += $rowData[$rowI]['bonbu_tot'.$index];
		$rowData[$rowI]['chung_amt4'] += $rowData[$rowI]['chung_amt'.$index];



		if ($newData[$newI]['max_amt'] < $newData[$newI]['totalSuga'] + $row['confSugaPay']){
			if ($newData[$newI]['over_amt'] == 0){
				$tempSuga = $newData[$newI]['max_amt'] - $newData[$newI]['totalSuga'];
				$newData[$newI]['bonin_amt'.$index] = $tempSuga * ($row['m03_bonin_yul'] / 100);
				$newData[$newI]['over_amt'] = $row['confSugaPay'] - $tempSuga;
			}else{
				$newData[$newI]['over_amt'] = $newData[$newI]['over_amt'] + $row['confSugaPay'];
			}
		}else{
			$newData[$newI]['bonin_amt'.$index] = $row['confBoninPay'];
		}
		$newData[$newI]['totalSuga'] = $newData[$newI]['totalSuga'] + $row['confSugaPay'];

		
		$newData[$newI]['suga_tot'.$index]  = $row['confSugaPay'];
		$newData[$newI]['bonin_amt'.$index] = $row['confBoninPay'];
		$newData[$newI]['over_amt'.$index]  = $row['confOverPay'];
		$newData[$newI]['bipay'.$index]     = $row['confBiPay'];
		//$newData[$newI]['bonbu_tot'.$index] = $row['confBoninPay'] + $row['confOverPay'] + $row['confBiPay'];
		$newData[$newI]['bonbu_tot'.$index] = $newData[$newI]['bonin_amt'.$index] + $newData[$newI]['over_amt'.$index] + $newData[$newI]['bipay'.$index];
		$newData[$newI]['chung_amt'.$index] = $row['confSugaPay'] - $newData[$newI]['bonbu_tot'.$index];

		$newData[$newI]['suga_tot4']  += $newData[$newI]['suga_tot'.$index];
		$newData[$newI]['bonin_amt4'] += $newData[$newI]['bonin_amt'.$index];
		$newData[$newI]['over_amt4']  += $newData[$newI]['over_amt'.$index];
		$newData[$newI]['bipay4']     += $newData[$newI]['bipay'.$index];
		$newData[$newI]['bonbu_tot4'] += $newData[$newI]['bonbu_tot'.$index];
		$newData[$newI]['chung_amt4'] += $newData[$newI]['chung_amt'.$index];
	}

	$conn->row_free();
		
	//print_r($newData);
	//exit;

	$rowCount = sizeOf($rowData);

	for($i=0; $i<$rowCount; $i++){
		# 기존 데이타삭제
		$sql = "delete"
			 . "  from t13sugupja"
			 . " where t13_ccode    = '".$rowData[$i]['ccode']
			 . "'  and t13_mkind    = '".$rowData[$i]['mkind']
			 . "'  and t13_jumin    = '".$rowData[$i]['jumin']
			 . "'  and t13_pay_date = '".$rowData[$i]['pay_date']
			 . "'  and t13_type = '1'";
		#echo $sql.'<br>';
		if (!$conn->query($sql)){
			$conn->rollback();
			echo '<script>alert("확정 처리중 오류가 발생하였습니다."); history.back();</script>';
			exit;
		}
		
		$rowData[$i]['result_amt'] = $rowData[$i]['max_amt'] - $rowData[$i]['suga_tot4'];

		# 데이타 저장
		$sql = "insert into t13sugupja ("
			 . " t13_ccode"
			 . ",t13_mkind"
			 . ",t13_jumin"
			 . ",t13_pay_date"
			 . ",t13_bonin_yul"
			 . ",t13_type"
			 . ",t13_max_amt"
			 . ",t13_result_amt"
			 . ",t13_over_amt"
			 . ",t13_suga_tot1"
			 . ",t13_bonin_amt1"
			 . ",t13_over_amt1"
			 . ",t13_bipay1"
			 . ",t13_bonbu_tot1"
			 . ",t13_chung_amt1"
			 . ",t13_suga_tot2"
			 . ",t13_bonin_amt2"
			 . ",t13_over_amt2"
			 . ",t13_bipay2"
			 . ",t13_bonbu_tot2"
			 . ",t13_chung_amt2"
			 . ",t13_suga_tot3"
			 . ",t13_bonin_amt3"
			 . ",t13_over_amt3"
			 . ",t13_bipay3"
			 . ",t13_bonbu_tot3"
			 . ",t13_chung_amt3"
			 . ",t13_suga_tot4"
			 . ",t13_bonin_amt4"
			 . ",t13_over_amt4"
			 . ",t13_bipay4"
			 . ",t13_bonbu_tot4"
			 . ",t13_chung_amt4"
			 . ",t13_misu_amt"
			 . ",t13_misu_inamt"
			 . ",t13_bill_no"
			 . ") values ("
			 . "  '".$rowData[$i]['ccode']
			 . "','".$rowData[$i]['mkind']
			 . "','".$rowData[$i]['jumin']
			 . "','".$rowData[$i]['pay_date']
			 . "','".$rowData[$i]['bonin_yul']
			 . "','1"
			 . "','".$rowData[$i]['max_amt']
			 . "','".$rowData[$i]['result_amt']
			 //. "','".($rowData[$i]['result_amt'] + $rowData[$i]['bonin_amt1'] + $rowData[$i]['bonin_amt2'] + $rowData[$i]['bonin_amt3'])
			 //. "','".(($rowData[$i]['result_amt'] < 0) ? abs($rowData[$i]['result_amt']) : 0)
			 . "','".$rowData[$i]['over_amt']
			 . "','".$rowData[$i]['suga_tot1']
			 . "','".$rowData[$i]['bonin_amt1']
			 . "','".$rowData[$i]['over_amt1']
			 . "','".$rowData[$i]['bipay1']
			 . "','".$rowData[$i]['bonbu_tot1']
			 . "','".$rowData[$i]['chung_amt1']
			 . "','".$rowData[$i]['suga_tot2']
			 . "','".$rowData[$i]['bonin_amt2']
			 . "','".$rowData[$i]['over_amt2']
			 . "','".$rowData[$i]['bipay2']
			 . "','".$rowData[$i]['bonbu_tot2']
			 . "','".$rowData[$i]['chung_amt2']
			 . "','".$rowData[$i]['suga_tot3']
			 . "','".$rowData[$i]['bonin_amt3']
			 . "','".$rowData[$i]['over_amt3']
			 . "','".$rowData[$i]['bipay3']
			 . "','".$rowData[$i]['bonbu_tot3']
			 . "','".$rowData[$i]['chung_amt3']
			 . "','".$rowData[$i]['suga_tot4']
			 . "','".$rowData[$i]['bonin_amt4']
			 . "','".$rowData[$i]['over_amt4']
			 . "','".$rowData[$i]['bipay4']
			 . "','".$rowData[$i]['bonbu_tot4']
			 . "','".$rowData[$i]['chung_amt4']
			 . "','0"
			 . "','0"
			 . "','000000"
			 . "')";
		#echo $sql.'<br>';
		if (!$conn->query($sql)){
			$conn->rollback();
			echo '<script>alert("확정 처리중 오류가 발생하였습니다."); history.back();</script>';
			exit;
		}
	}
	
	
	
	
	$dataCount = sizeOf($newData);

	for($i=0; $i<$dataCount; $i++){
		# 기존 데이타삭제
		$sql = "delete"
			 . "  from t13sugupja"
			 . " where t13_ccode    = '".$newData[$i]['ccode']
			 . "'  and t13_mkind    = '".$newData[$i]['mkind']
			 . "'  and t13_jumin    = '".$newData[$i]['jumin']
			 . "'  and t13_pay_date = '".$newData[$i]['pay_date']
			 . "'  and t13_type = '2'";
		#echo $sql.'<br>';
		if (!$conn->query($sql)){
			$conn->rollback();
			echo '<script>alert("확정 처리중 오류가 발생하였습니다."); history.back();</script>';
			exit;
		}
	}

	$sql = "select ifnull(max(t13_bill_no),0)"
		 . "  from t13sugupja"
		 . " where t13_ccode    = '".$mCode
		 . "'  and t13_mkind    = '".$mKind
		 . "'  and t13_pay_date = '".$mPayDate
		 . "'  and t13_type = '2'";
	$mBillNo = $conn->get_data($sql);
	$tempJumin = '';
	for($i=0; $i<$dataCount; $i++){
		$newData[$i]['result_amt'] = $newData[$i]['max_amt'] - $newData[$i]['suga_tot4'];

		// 수급자가 바뀌었을 경우 새로운 영수증번호를 발급한다.
		if ($tempJumin != $newData[$i]['jumin']){
			$tempJumin  = $newData[$i]['jumin'];
			$mBillNo = ceil($mBillNo) + 1;
			$newBillNo = '';
			for($j=strLen($mBillNo)+1; $j<=6; $j++){
				$newBillNo .= '0';
			}
			$newBillNo .= $mBillNo;
		}

		# 데이타 저장
		$sql = "insert into t13sugupja ("
			 . " t13_ccode"
			 . ",t13_mkind"
			 . ",t13_jumin"
			 . ",t13_pay_date"
			 . ",t13_bonin_yul"
			 . ",t13_type"
			 . ",t13_max_amt"
			 . ",t13_result_amt"
			 . ",t13_over_amt"
			 . ",t13_suga_tot1"
			 . ",t13_bonin_amt1"
			 . ",t13_over_amt1"
			 . ",t13_bipay1"
			 . ",t13_bonbu_tot1"
			 . ",t13_chung_amt1"
			 . ",t13_suga_tot2"
			 . ",t13_bonin_amt2"
			 . ",t13_over_amt2"
			 . ",t13_bipay2"
			 . ",t13_bonbu_tot2"
			 . ",t13_chung_amt2"
			 . ",t13_suga_tot3"
			 . ",t13_bonin_amt3"
			 . ",t13_over_amt3"
			 . ",t13_bipay3"
			 . ",t13_bonbu_tot3"
			 . ",t13_chung_amt3"
			 . ",t13_suga_tot4"
			 . ",t13_bonin_amt4"
			 . ",t13_over_amt4"
			 . ",t13_bipay4"
			 . ",t13_bonbu_tot4"
			 . ",t13_chung_amt4"
			 . ",t13_misu_amt"
			 . ",t13_misu_inamt"
			 . ",t13_bill_no"
			 . ") values ("
			 . "  '".$newData[$i]['ccode']
			 . "','".$newData[$i]['mkind']
			 . "','".$newData[$i]['jumin']
			 . "','".$newData[$i]['pay_date']
			 . "','".$newData[$i]['bonin_yul']
			 . "','2"
			 . "','".$newData[$i]['max_amt']
			 . "','".$newData[$i]['result_amt']
			 //. "','".($newData[$i]['result_amt'] + $newData[$i]['bonin_amt1'] + $newData[$i]['bonin_amt2'] + $newData[$i]['bonin_amt3'])
			 //. "','".(($newData[$i]['result_amt'] < 0) ? abs($newData[$i]['result_amt']) : 0)
			 . "','".$newData[$i]['over_amt']
			 . "','".$newData[$i]['suga_tot1']
			 . "','".$newData[$i]['bonin_amt1']
			 . "','".$newData[$i]['over_amt1']
			 . "','".$newData[$i]['bipay1']
			 . "','".$newData[$i]['bonbu_tot1']
			 . "','".$newData[$i]['chung_amt1']
			 . "','".$newData[$i]['suga_tot2']
			 . "','".$newData[$i]['bonin_amt2']
			 . "','".$newData[$i]['over_amt2']
			 . "','".$newData[$i]['bipay2']
			 . "','".$newData[$i]['bonbu_tot2']
			 . "','".$newData[$i]['chung_amt2']
			 . "','".$newData[$i]['suga_tot3']
			 . "','".$newData[$i]['bonin_amt3']
			 . "','".$newData[$i]['over_amt3']
			 . "','".$newData[$i]['bipay3']
			 . "','".$newData[$i]['bonbu_tot3']
			 . "','".$newData[$i]['chung_amt3']
			 . "','".$newData[$i]['suga_tot4']
			 . "','".$newData[$i]['bonin_amt4']
			 . "','".$newData[$i]['over_amt4']
			 . "','".$newData[$i]['bipay4']
			 . "','".$newData[$i]['bonbu_tot4']
			 . "','".$newData[$i]['chung_amt4']
			 . "','".$newData[$i]['bonbu_tot4']
			 . "','0"
			 . "','".$newBillNo
			 . "')";
		#echo $sql.'<br>';
		if (!$conn->query($sql)){
			$conn->rollback();
			echo '<script>alert("확정 처리중 오류가 발생하였습니다."); history.back();</script>';
			exit;
		}
	}
	
	$conn->commit();

	include('../inc/_db_close.php');

	$mCode  = $_POST['mCode'];  //기관코드
	$mKind  = $_POST['mKind'];  //기관분류코드
	$mPayDate = $_POST['confYear'].$_POST['confMonth'];
	$mSugupja = $ed->de($_POST['mSugupja']);
?>
<form name="f" method="post">
<input name="curYear" type="hidden" value="">
<input name="curMonth" type="hidden" value="">
<input name="curMcode" type="hidden" value="">
<input name="curMkind" type="hidden" value="">
<input name="curSugupja" type="hidden" value="">
</form>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
	goMonthConfSugupja("<?=$_POST['confYear'];?>", "<?=$_POST['confMonth'];?>", "<?=$_POST['mCode'];?>", "<?=$_POST['mKind'];?>", "<?=$_POST['mSugupja'];?>")
</script>