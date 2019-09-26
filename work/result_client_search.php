<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;


	//수가정보
	$sql = 'SELECT	*
			FROM	(
					SELECT	m01_mcode2 AS code
					,		m01_suga_cont AS name
					,		m01_sdate AS from_dt
					,		m01_edate AS to_dt
					FROM	m01suga
					WHERE	m01_mcode = \'goodeos\'
					UNION	ALL
					SELECT	m11_mcode2
					,		m11_suga_cont
					,		m11_sdate
					,		m11_edate
					FROM	m11suga
					WHERE	m11_mcode = \'goodeos\'
					) AS a
			WHERE	LEFT(from_dt,6) <= \''.$year.$month.'\'
			AND		LEFT(to_dt,6)	>= \''.$year.$month.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$suga[$row['code']][] = Array(
			'fromDt'=>$row['from_dt']
		,	'toDt'	=>$row['to_dt']
		,	'name'	=>$row['name']
		);
	}

	$conn->row_free();


	//최저시급
	$sql = 'SELECT	g07_pay_time
			FROM	g07minpay
			WHERE	g07_year = \''.$year.'\'';

	$minPay = $conn->get_data($sql);


	//요양보호사 기준시급
	$sql = 'SELECT	fw_jumin AS jumin
			,		fw_hourly AS pay
			FROM	fixed_works
			WHERE	org_no		 = \''.$orgNo.'\'
			AND		fw_from_dt	<= \''.$year.$month.'\'
			AND		fw_to_dt	>= \''.$year.$month.'\'
			AND		del_flag	 = \'N\'';

	$fixedPay = $conn->_fetch_array($sql,'jumin');


	//요양보호사 시급
	$sql = 'SELECT	mh_jumin AS jumin
			,		mh_svc AS svc_cd
			,		mh_type AS type
			,		mh_hourly AS pay
			FROM	mem_hourly
			WHERE	org_no		 = \''.$orgNo.'\'
			AND		mh_from_dt	<= \''.$year.$month.'\'
			AND		mh_to_dt	>= \''.$year.$month.'\'
			AND		del_flag	 = \'N\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		if ($row['type'] == '1'){
			$salary[$row['jumin']][$row['svc_cd']] = $row['pay'];
		}else{
			$salary[$row['jumin']][$row['svc_cd']] = 'X';
		}
	}

	$conn->row_free();


	//목욕 및 간호 수당
	$sql = 'SELECT	jumin
			,		extra500_1 as B3
			,		extra500_2 as B1
			,		extra500_3 as B2
			,		extra800_1 as N1
			,		extra800_2 as N2
			,		extra800_3 as N3
			FROM	mem_extra
			WHERE	org_no = \''.$orgNo.'\'';

	$extraPay = $conn->_fetch_array($sql, 'jumin');


	//인정번호
	$sql = 'SELECT	DISTINCT jumin, app_no
			FROM	client_his_lvl
			WHERE	org_no = \''.$orgNo.'\'
			AND		svc_cd = \'0\'
			AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$year.$month.'\'
			AND		LEFT(REPLACE(to_dt,	 \'-\',\'\'),6) >= \''.$year.$month.'\'';

	$appNo = $conn->_fetch_array($sql,'jumin');


	//수급자 등급 정보
	$sql = 'SELECT	jumin
			,		from_dt
			,		to_dt
			,		kind
			,		rate
			FROM	client_his_kind
			WHERE	org_no = \''.$orgNo.'\'
			AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$year.$month.'\'
			AND		LEFT(REPLACE(to_dt,	 \'-\',\'\'),6) >= \''.$year.$month.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$rate[$row['jumin']][] = Array(
			'fromDt'=>str_replace('-','',$row['from_dt'])
		,	'toDt'	=>str_replace('-','',$row['to_dt'])
		,	'rate'	=>$row['rate']
		);
	}

	$conn->row_free();


	$sql = 'SELECT	t01_jumin AS jumin
			,		m03_name AS name
			,		t01_sugup_date AS date
			,		t01_yoyangsa_id1 AS mem_cd1
			,		t01_yname1 AS mem_nm1
			,		t01_yoyangsa_id2 AS mem_cd2
			,		t01_yname2 AS mem_nm2
			,		t01_conf_suga_code AS code
			,		t01_conf_suga_value AS value
			,		t01_conf_soyotime AS proc_time
			,		t01_toge_umu AS family_yn
			,		t01_svc_subcode AS sub_cd
			FROM	t01iljung
			INNER	JOIN	m03sugupja
					ON		m03_ccode = t01_ccode
					AND		m03_mkind = t01_mkind
					AND		m03_jumin = t01_jumin
			WHERE	t01_ccode		= \''.$orgNo.'\'
			AND		t01_mkind		= \'0\'
			AND		t01_del_yn		= \'N\'
			AND		t01_status_gbn	= \'1\'
			AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
			ORDER	BY name, sub_cd, code, mem_nm1, mem_nm2, date';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	if ($IsExcel){
		$style = 'border:0.5pt solid BLACK;';
	}else{
		$style = '';
	}

	for($i=0; $i<$rowCnt; $i++){
		$row	= $conn->select_row($i);
		$jumin	= $row['jumin'];
		$code	= $row['code'];
		$tmpCd[1] = $row['mem_cd1'];
		$tmpCd[2] = $row['mem_cd2'];

		if ($gDomain == 'dolvoin.net' && $row['sub_cd'] == '500') $row['value'] = $row['value'] * 0.5;

		if (!is_array($data[$jumin])){
			$tmpRate = '<span style="color:RED;">100.0</span>';

			if (is_array($rate[$jumin])){
				foreach($rate[$jumin] as $tmpII => $arrRate){
					if ($arrRate['fromDt'] <= $row['date'] && $arrRate['toDt'] >= $row['date']){
						$tmpRate = '<span style="color:BLUE;">'.$arrRate['rate'].'</span>';
					}
				}
			}

			$data[$jumin] = Array(
				'name'	=>$row['name']
			,	'appNo'	=>$appNo[$jumin]['app_no']
			,	'rate'	=>$tmpRate
			,	'cnt'	=>0
			);
		}

		foreach($tmpCd as $tmpI => $memCd){
			if (!$memCd) continue;

			if (!is_array($data[$jumin]['mem'][$memCd])){
				$data[$jumin]['mem'][$memCd] = Array(
					'name'	=>$row['mem_nm'.$tmpI]
				,	'birth'	=>$myF->issToBirthDay($memCd,'.')
				,	'cnt'	=>0
				);
			}

			if (!is_array($data[$jumin]['mem'][$memCd]['suga'][$code])){
				$sugaNm = '<span style="color:RED;">미등록</span>';

				if (is_array($suga[$code])){
					foreach($suga[$code] as $tmpII => $arrSuga){
						if ($arrSuga['fromDt'] <= $row['date'] && $arrSuga['toDt'] >= $row['date']){
							$sugaNm = $arrSuga['name'];
						}
					}
				}

				$data[$jumin]['mem'][$memCd]['suga'][$code]['name'] = $sugaNm;

				$data[$jumin]['cnt'] ++;
				$data[$jumin]['mem'][$memCd]['cnt'] ++;
			}

			$tmpRate = 100;

			if (is_array($rate[$jumin])){
				foreach($rate[$jumin] as $tmpII => $arrRate){
					if ($arrRate['fromDt'] <= $row['date'] && $arrRate['toDt'] >= $row['date']){
						$tmpRate = $arrRate['rate'];
					}
				}
			}

			if ($row['sub_cd'] == '200'){
				if ($row['proc_time'] >= 510){
					$row['proc_time'] = 480;
				}else if ($row['proc_time'] >= 270){
					if ($year.$month >= '201603'){
						if ($row['proc_time'] < 480){
							$row['proc_time'] -= 30;
						}
					}else{
						$row['proc_time'] -= 30;
					}
				}
				$procTime = $myF->cutOff($row['proc_time'], 30) / 60;

				if ($salary[$memCd][$row['family_yn'] == 'Y' ? '12' : '11'] == 'X'){
					$cost = '시급제아님';
					$pay = 0;
				}else{
					if ($fixedPay[$memCd]['pay'] < $minPay) $fixedPay[$memCd]['pay'] = $minPay;
					if ($salary[$memCd][$row['family_yn'] == 'Y' ? '12' : '11'] < $fixedPay[$memCd]['pay']) $salary[$memCd][$row['family_yn'] == 'Y' ? '12' : '11'] = $fixedPay[$memCd]['pay'];

					$cost = $salary[$memCd][$row['family_yn'] == 'Y' ? '12' : '11'];
					$pay = $cost * $procTime;
				}

				if ($row['family_yn'] == 'Y'){
					$data[$jumin]['mem'][$memCd]['time']['12'] += $procTime;
					$data[$jumin]['mem'][$memCd]['pay']['12'] += $pay;
					$data[$jumin]['mem'][$memCd]['amt']['12'] += $row['value'];
					$data[$jumin]['mem'][$memCd]['deal']['12'] += ($procTime * 625);
					if (!$data[$jumin]['mem'][$memCd]['cost']['12']) $data[$jumin]['mem'][$memCd]['cost']['12'] = $cost;
				}else{
					$data[$jumin]['mem'][$memCd]['time']['11'] += $procTime;
					$data[$jumin]['mem'][$memCd]['pay']['11'] += $pay;
					$data[$jumin]['mem'][$memCd]['amt']['11'] += $row['value'];
					$data[$jumin]['mem'][$memCd]['deal']['11'] += ($procTime * 625);
					if (!$data[$jumin]['mem'][$memCd]['cost']['11']) $data[$jumin]['mem'][$memCd]['cost']['11'] = $cost;
				}
			}else if ($row['sub_cd'] == '500'){
				if ($code == 'CBFD1'){
					$idx = '3';
				}else if ($code == 'CBKD1' || $code == 'VAB10'){
					$idx = '1';
				}else if ($code == 'CBKD2' || $code == 'VAB20'){
					$idx = '2';
				}else{
					$idx = '';
				}

				$cost = $extraPay[$memCd]['B'.$idx];

				if ($row['proc_time'] < 40){
					$procTime = 0;
					$pay = 0;
					$deal = 0;
				}else if ($row['proc_time'] >= 40 && $row['proc_time'] < 60){
					$procTime = 60 * 0.8;
					$pay = $cost * 0.8;
					$deal = 625 * 0.8;
				}else{
					$procTime = 1;
					$pay = $cost;
					$deal = 625;
				}

				$data[$jumin]['mem'][$memCd]['time']['13'] += $procTime;
				$data[$jumin]['mem'][$memCd]['pay']['13'] += $pay;
				$data[$jumin]['mem'][$memCd]['amt']['13'] += $row['value'];
				$data[$jumin]['mem'][$memCd]['deal']['13'] += $deal;
				if (!$data[$jumin]['mem'][$memCd]['cost']['13']) $data[$jumin]['mem'][$memCd]['cost']['13'] = $cost;
			}else{
				if ($row['proc_time'] > 60) $procTime = 60;
				$procTime = Floor($procTime / 60 * 10) / 10;

				if ($code == 'CNWS1' || $code == 'CNHS1' || $code == 'VAN10'){
					$idx = '1';
				}else if ($code == 'CNWS2' || $code == 'CNHS2' || $code == 'VAN20'){
					$idx = '2';
				}else if ($code == 'CNWS3' || $code == 'CNHS3' || $code == 'VAN30'){
					$idx = '3';
				}else{
					$idx = '';
				}

				$cost = $extraPay[$memCd]['N'.$idx];

				$data[$jumin]['mem'][$memCd]['time']['14'] += $procTime;
				$data[$jumin]['mem'][$memCd]['pay']['14'] += $cost;
				$data[$jumin]['mem'][$memCd]['amt']['14'] += $row['value'];
				$data[$jumin]['mem'][$memCd]['deal']['14'] = 0;
				if (!$data[$jumin]['mem'][$memCd]['cost']['14']) $data[$jumin]['mem'][$memCd]['cost']['14'] = $cost;
			}

			$expense = Floor($row['value'] * $tmpRate / 100);

			$data[$jumin]['mem'][$memCd]['suga'][$code]['subCd']	 = $row['sub_cd'];
			$data[$jumin]['mem'][$memCd]['suga'][$code]['amt']		+= $row['value'];
			$data[$jumin]['mem'][$memCd]['suga'][$code]['expense']	+= $expense;
			//$data[$jumin]['mem'][$memCd]['suga'][$code]['longterm']	 = $data[$jumin]['mem'][$memCd]['suga'][$code]['amt'] - $data[$jumin]['mem'][$memCd]['suga'][$code]['expense'];
			//$data[$jumin]['mem'][$memCd]['suga'][$code]['longterm'] += ($row['value'] - $expense);
			$data[$jumin]['mem'][$memCd]['suga'][$code]['cnt'] ++;
		}
	}

	$conn->row_free();

	if (is_array($data)){
		$no = 1;

		foreach($data as $jumin => $client){
			if ($no % 2 == 1){
				$bgcolor = 'FFFFFF';
			}else{
				$bgcolor = 'EAEAEA';
			}?>
			<tr>
			<td class="center" style="background-color:#<?=$bgcolor;?>; <?=$style;?> <?=$IsExcel ? 'text-align:center;' : '';?>" rowspan="<?=$client['cnt'];?>"><?=$no;?></td>
			<td class="center" style="background-color:#<?=$bgcolor;?>; <?=$style;?>" rowspan="<?=$client['cnt'];?>"><div class="left"><?=$client['name'];?>(<?=$client['rate'];?>%)<br><?=$client['appNo'];?></div></td><?

			foreach($client['mem'] as $memCd => $mem){
				if ($client['cnt'] < 1){?>
					<tr><?
				}?>
				<td class="center" style="background-color:#<?=$bgcolor;?>; <?=$style;?>" rowspan="<?=$mem['cnt'];?>"><div class="left"><?=$mem['name'];?><br><?=$mem['birth'];?></div></td><?

				foreach($mem['suga'] as $code => $row){
					if ($mem['cnt'] < 1){?>
						<tr><?
					}

					$expense = $row['expense'];

					$longterm = $row['amt'] - $expense;?>
					<td class="center" style="background-color:#<?=$bgcolor;?>; <?=$style;?>"><div class="left"><?=$row['name'];?></div></td>
					<td class="center" style="background-color:#<?=$bgcolor;?>; <?=$style;?>"><div class="right"><?=number_format($row['cnt']);?></div></td>
					<td class="center" style="background-color:#<?=$bgcolor;?>; <?=$style;?>"><div class="right"><?=number_format($row['amt']);?></div></td>
					<td class="center" style="background-color:#<?=$bgcolor;?>; <?=$style;?>"><div class="right"><?=number_format($longterm);?></div></td>
					<td class="center" style="background-color:#<?=$bgcolor;?>; <?=$style;?>"><div class="right"><?=number_format($expense);?></div></td><?

					$total['amt'] += $row['amt'];
					$total['longterm'] += $longterm;
					$total['expense'] += $expense;

					if ($mem['cnt'] > 0){
						$strTime = '';
						$strCost = '';
						$strPay = '';
						$strDeal = '';
						$strAmt = '';
						$strProfit = ''; //이익
						$strPFRate = ''; //이익율

						//급여
						$total['salary'] += $mem['pay']['11'];
						$total['salary'] += $mem['pay']['12'];
						$total['salary'] += $mem['pay']['13'];
						$total['salary'] += $mem['pay']['14'];

						//처우개선비
						$total['deal'] += $mem['deal']['11'];
						$total['deal'] += $mem['deal']['12'];
						$total['deal'] += $mem['deal']['13'];
						$total['deal'] += $mem['deal']['14'];

						//이익
						$total['profit'] += ($mem['amt']['11'] - ($mem['pay']['11']+$mem['deal']['11']));
						$total['profit'] += ($mem['amt']['12'] - ($mem['pay']['12']+$mem['deal']['12']));
						$total['profit'] += ($mem['amt']['13'] - ($mem['pay']['13']+$mem['deal']['13']));
						$total['profit'] += ($mem['amt']['14'] - ($mem['pay']['14']+$mem['deal']['14']));

						if ($mem['time']['11'] > 0){
							$mem['time']['11'] = Floor($mem['time']['11'] * 10) / 10;
							$strTime .= '요양 : '.$mem['time']['11'].'H<br>';
							$strCost .= (is_numeric($mem['cost']['11']) ? number_format($mem['cost']['11']) : $mem['cost']['11']).'<br>';
							$strPay .= ($mem['pay']['11'] > 0 ? number_format($mem['pay']['11']) : '').'<br>';
							$strDeal .= ($mem['deal']['11'] > 0 ? number_format($mem['deal']['11']) : '').'<br>';

							if ($IsExcel){
								$strAmt .= ($mem['pay']['11']+$mem['deal']['11'] > 0 ? number_format($mem['pay']['11']+$mem['deal']['11']) : '').'<br>';

								$profit = $mem['amt']['11'] - ($mem['pay']['11']+$mem['deal']['11']);
								$yul = @Round($profit / $mem['amt']['11'] * 100,2);
								$strProfit .= ($profit > 0 ? number_format($profit) : '').'<br>';
								$strPFRate .= ($yul > 0 ? $yul.'%' : '').'<br>';
							}
						}

						if ($mem['time']['12'] > 0){
							$mem['time']['12'] = Floor($mem['time']['12'] * 10) / 10;
							$strTime .= '가족 : '.$mem['time']['12'].'H<br>';
							$strCost .= (is_numeric($mem['cost']['12']) ? number_format($mem['cost']['12']) : $mem['cost']['12']).'<br>';
							$strPay .= ($mem['pay']['12'] > 0 ? number_format($mem['pay']['12']) : '').'<br>';
							$strDeal .= ($mem['deal']['12'] > 0 ? number_format($mem['deal']['12']) : '').'<br>';

							if ($IsExcel){
								$strAmt .= ($mem['pay']['12']+$mem['deal']['12'] > 0 ? number_format($mem['pay']['12']+$mem['deal']['12']) : '').'<br>';

								$profit = $mem['amt']['12'] - ($mem['pay']['12']+$mem['deal']['12']);
								$yul = @Round($profit / $mem['amt']['12'] * 100,2);
								$strProfit .= ($profit > 0 ? number_format($profit) : '').'<br>';
								$strPFRate .= ($yul > 0 ? $yul.'%' : '').'<br>';
							}
						}

						if ($mem['time']['13'] > 0){
							$mem['time']['13'] = Floor($mem['time']['13'] * 10) / 10;
							$strTime .= '목욕 : '.$mem['time']['13'].'H<br>';
							$strCost .= number_format($mem['cost']['13']).'<br>';
							$strPay .= number_format($mem['pay']['13']).'<br>';
							$strDeal .= ($mem['deal']['13'] > 0 ? number_format($mem['deal']['13']) : '').'<br>';

							if ($IsExcel){
								$strAmt .= ($mem['pay']['13']+$mem['deal']['13'] > 0 ? number_format($mem['pay']['13']+$mem['deal']['13']) : '').'<br>';

								$profit = $mem['amt']['13'] - ($mem['pay']['13']+$mem['deal']['13']);
								$yul = @Round($profit / $mem['amt']['13'] * 100,2);
								$strProfit .= ($profit > 0 ? number_format($profit) : '').'<br>';
								$strPFRate .= ($yul > 0 ? $yul.'%' : '').'<br>';
							}
						}

						if ($mem['time']['14'] > 0){
							$mem['time']['14'] = Floor($mem['time']['14'] * 10) / 10;
							$strCost .= number_format($mem['cost']['14']).'<br>';
							$strPay .= number_format($mem['pay']['14']).'<br>';
							$strDeal .= ($mem['deal']['14'] > 0 ? number_format($mem['deal']['14']) : '').'<br>';

							if ($IsExcel){
								$strAmt .= ($mem['pay']['14']+$mem['deal']['14'] > 0 ? number_format($mem['pay']['14']+$mem['deal']['14']) : '').'<br>';

								$profit = $mem['amt']['14'] - ($mem['pay']['14']+$mem['deal']['14']);
								$yul = @Round($profit / $mem['amt']['14'] * 100,2);
								$strProfit .= ($profit > 0 ? number_format($profit) : '').'<br>';
								$strPFRate .= ($yul > 0 ? $yul.'%' : '').'<br>';
							}
						}?>
						<td class="center" style="background-color:#<?=$bgcolor;?>; <?=$style;?>" rowspan="<?=$mem['cnt'];?>"><div class="left"><?=$strTime;?></div></td>
						<td class="center" style="background-color:#<?=$bgcolor;?>; <?=$style;?>" rowspan="<?=$mem['cnt'];?>"><div class="right"><?=$strCost;?></div></td>
						<td class="center" style="background-color:#<?=$bgcolor;?>; <?=$style;?>" rowspan="<?=$mem['cnt'];?>"><div class="right"><?=$strPay;?></div></td>
						<td class="center last" style="background-color:#<?=$bgcolor;?>; <?=$style;?>" rowspan="<?=$mem['cnt'];?>"><div class="right"><?=$strDeal;?></div></td><?

						if ($IsExcel){?>
							<td class="center" style="background-color:#<?=$bgcolor;?>; <?=$style;?>" rowspan="<?=$mem['cnt'];?>"><div class="right"><?=$strAmt;?></div></td><?

							if ($gDomain == 'dolvoin.net'){?>
								<td class="center" style="background-color:#<?=$bgcolor;?>; <?=$style;?>" rowspan="<?=$mem['cnt'];?>"><div class="right"><?=$strProfit;?></div></td>
								<td class="center" style="background-color:#<?=$bgcolor;?>; <?=$style;?>" rowspan="<?=$mem['cnt'];?>"><div class="right"><?=$strPFRate;?></div></td><?
							}
						}
					}

					$mem['cnt'] = 0;
				}?>
				</tr><?
			}

			$client['cnt'] = 0;
			$no ++;
		}


		if ($IsExcel){
			$style = 'text-align:right; font-weight:bold; background-color:#E7E7E7; border:0.5pt solid BLACK;';

			$yul = Round($total['profit'] / $total['amt'] * 100,2);?>
			<tr>
				<td style="<?=$style;?>" colspan="5">합계</td>
				<td style="<?=$style;?>"><?=$total['amt'] > 0 ? number_format($total['amt']) : '';?></td>
				<td style="<?=$style;?>"><?=$total['longterm'] > 0 ? number_format($total['longterm']) : '';?></td>
				<td style="<?=$style;?>"><?=$total['expense'] > 0 ? number_format($total['expense']) : '';?></td>
				<td style="<?=$style;?>"></td>
				<td style="<?=$style;?>"></td>
				<td style="<?=$style;?>"><?=$total['salary'] > 0 ? number_format($total['salary']) : '';?></td>
				<td style="<?=$style;?>" class="last"><?=$total['deal'] > 0 ? number_format($total['deal']) : '';?></td>
				<td style="<?=$style;?>"><?=$total['salary']+$total['deal'] > 0 ? number_format($total['salary']+$total['deal']) : '';?></td><?

				if ($gDomain == 'dolvoin.net'){?>
					<td style="<?=$style;?>"><?=$total['profit'] > 0 ? number_format($total['profit']) : '';?></td>
					<td style="<?=$style;?>"><?=$yul > 0 ? $yul.'%' : '';?></td><?
				}?>
			</tr><?
		}
	}else{?>
		<tr>
			<td class="center last" colspan="12">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	Unset($suga);
	Unset($rate);

	include_once('../inc/_db_close.php');
?>