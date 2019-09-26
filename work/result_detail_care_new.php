<?
	/*********************************************************
	 * 고객등급 이력
	 *********************************************************/
	$sql = 'select level as lvl
			,      from_dt
			,      to_dt
			  from client_his_lvl as lvl
			 where org_no                         = \''.$code.'\'
			   and jumin                          = \''.$jumin.'\'
			   and svc_cd = \'0\'
			   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			   and date_format(to_dt,\'%Y%m\')   >= \''.$year.$month.'\'
			 order by level
			 limit 1';

	$laCltLvl = $conn->get_array($sql);
	$lsDt     = str_replace('-','',$myF->_getDt($laCltLvl['from_dt'],$laCltLvl['to_dt']));

	if (is_array($laCltLvl)){
		/*********************************************************
		 * 한도금액
		 *********************************************************/
		$sql = 'select amt
				  from client_his_limit
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
				 order by seq desc
				 limit 1';

		$liLimitAmt = $conn->get_data($sql);

		if (empty($liLimitAmt)){
			$sql = 'select m91_kupyeo
					  from m91maxkupyeo
					 where m91_code   = \''.$laCltLvl['lvl'].'\'
					   and m91_sdate <= \''.$lsDt.'\'
					   and m91_edate >= \''.$lsDt.'\'';

			$liLimitAmt = $conn->get_data($sql);
		}
	}

	if (!is_numeric($liLimitAmt)) $liLimitAmt = 0;

	if (empty($laCltLvl['lvl'])) $laCltLvl['lvl'] = '9';

	/*********************************************************
	 * 수급자 키
	 *********************************************************/
	$sql = 'select m03_key
			  from m03sugupja
			 where m03_ccode = \''.$code.'\'
			   and m03_jumin = \''.$jumin.'\'
			 order by m03_mkind
			 limit 1';
	$lsClientKey = $conn->get_data($sql);


	/*********************************************************
	 * 고객구분 이력
	 *********************************************************/
	$sql = 'select kind
			,      rate
			,      MIN(from_dt) AS from_dt
			,      MAX(to_dt) AS to_dt
			  from client_his_kind
			 where org_no                         = \''.$code.'\'
			   and jumin                          = \''.$jumin.'\'
			   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			   and date_format(to_dt,\'%Y%m\')   >= \''.$year.$month.'\'
			 group by kind, rate
			 order by seq';


	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if (empty($row['kind'])) $row['kind'] = '1';
		if (empty($row['rate'])){
			if ($row['lvl'] != '9')
				$row['rate'] = 15;
			else
				$row['rate'] = 100;
		}

		$laClient[$i]['jumin'] = $jumin;
		$laClient[$i]['lvlNm'] = $myF->_lvlNm($laCltLvl['lvl']);
		$laClient[$i]['lvlCd'] = $laCltLvl['lvl'];
		$laClient[$i]['kind']  = $row['kind'];
		$laClient[$i]['rate']  = number_format($row['rate'],1);
		$laClient[$i]['max']   = $liLimitAmt;
		$laClient[$i]['key']   = $lsClientKey;
		$laClient[$i]['from']  = $row['from_dt'];
		$laClient[$i]['to']    = $row['to_dt'];
	}

	$conn->row_free();

	if (is_array($laClient)){
		$sql = '';
		$liRowIdx = 0;

		if ($IsExpensePeriod){
			for($i=1; $i<=3; $i++){
				switch($i){
				case 1:
					$lsSvcNm = '방문요양';
					$lsSvcCd = '200';
					break;
				case 2:
					$lsSvcNm = '방문목욕';
					$lsSvcCd = '500';
					break;
				case 3:
					$lsSvcNm = '방문간호';
					$lsSvcCd = '800';
					break;
				}

				if ($i > 1){
					$sql .= ' UNION ALL ';
				}

				$sql .= 'SELECT	\''.$lsSvcNm.'\' AS svc_nm
						,		\''.$lsSvcCd.'\' AS svc_cd
						,		t13_bonin_yul AS rate
						,		t13_max_amt AS max_amt
						,		t13_suga_tot'.$i.' AS suga_tot
						,		t13_over_amt'.$i.' AS over_amt
						,		t13_bipay'.$i.' AS bipay
						,		t13_bonin_amt'.$i.' AS bonin_amt
						,		t13_bonbu_tot'.$i.' AS bonin_tot
						,		t13_chung_amt'.$i.' AS chung_amt
						FROM	t13sugupja
						WHERE	t13_ccode     = \''.$code.'\'
						AND		t13_mkind     = \''.$kind.'\'
						AND		t13_jumin     = \''.$jumin.'\'
						AND		t13_pay_date  = \''.$year.$month.'\'
						AND		t13_type      = \'2\'';

			}
		}else{
			foreach($laClient as $laRow){
				$lsKind = $laRow['kind']; //구분
				$liRate = $laRow['rate']; //본인부담율

				//한도금액 조회
				if ($liRowIdx == 0){
					$liMaxAmt = $conn->_limit_pay($laRow['lvlCd'], $year.$month);
				}else{
					$sql .= ' union all ';
				}

				for($i=1; $i<=3; $i++){
					switch($i){
					case 1:
						$lsSvcNm = '방문요양';
						$lsSvcCd = '200';
						break;
					case 2:
						$lsSvcNm = '방문목욕';
						$lsSvcCd = '500';
						break;
					case 3:
						$lsSvcNm = '방문간호';
						$lsSvcCd = '800';
						break;
					}

					if ($i > 1){
						$sql .= ' union all ';
					}

					$sql .= 'select \''.$lsSvcNm.'\' as svc_nm
							 ,      \''.$lsSvcCd.'\' as svc_cd
							 ,      t13_bonin_yul as rate
							 ,      t13_max_amt as max_amt
							 ,      t13_suga_tot'.$i.' as suga_tot
							 ,      t13_over_amt'.$i.' as over_amt
							 ,      t13_bipay'.$i.' as bipay
							 ,      t13_bonin_amt'.$i.' as bonin_amt
							 ,      t13_bonbu_tot'.$i.' as bonin_tot
							 ,      t13_chung_amt'.$i.' as chung_amt
							   from t13sugupja
							  where  t13_ccode     = \''.$code.'\'
								and  t13_mkind     = \''.$kind.'\'
								and  t13_jumin     = \''.$jumin.'\'
								and  t13_pay_date  = \''.$year.$month.'\'
								and  t13_bonin_yul = \''.$liRate.'\'
								and  t13_type      = \'2\'';

				}

				$liRowIdx ++;
			}
		}

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();
		$liBalance = $laClient[0]['max'];

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if (!is_array($laData[$row['svc_cd']])){
				$laData[$row['svc_cd']] = array(
						'lvlNm'  =>$laClient[0]['lvlNm']
					,	'rate'   =>$laClient[0]['rate']
					,	'maxAmt' =>$laClient[0]['max']
					,	'svcNm'  =>''
					,	'payTot' =>0
					,	'bipay'  =>0
					,	'pay'    =>0
					,	'expense'=>0
					,	'claim'  =>0
				);

				# 한도금액은 위에서 설정한다.
				#if (Empty($liBalance)){
				#	$liBalance = $laClient[0]['max'];
				#}
			}

			$laData[$row['svc_cd']]['svcNm']   = $row['svc_nm']; //서비스명
			//$laData[$row['svc_cd']]['payTot'] += ($row['suga_tot']-$row['bipay']); //급여총액
			$laData[$row['svc_cd']]['payTot'] += ($row['chung_amt']+$row['bonin_amt']); //급여총액
			$laData[$row['svc_cd']]['bipay']  += ($row['over_amt']+$row['bipay']); //비급여
			$laData[$row['svc_cd']]['pay']    += $row['bonin_amt']; //본인부담금
			$laData[$row['svc_cd']]['expense']+= $row['bonin_tot']; //본인부담금계
			$laData[$row['svc_cd']]['claim']  += $row['chung_amt']; //공단청구액

			$liBalance -= ($row['suga_tot']-$row['bipay']); //잔액
		}

		$conn->row_free();
	}




	if (is_array($laData)){
		if($mode == 'excel'){
			//엑셀출력일 경우
			echo '<div class="title">장기요양 확정내역</div>';
			echo '<table class="my_table" border="1">';
			$colspan = '2';
			$css  = 'head';
			$style = 'background-color:#eeeeee;';
		}else {
			echo '<div class="title title_border">장기요양 확정내역</div>';
			echo '<table class="my_table" style="width:100%;">';
			$css  = 'head last';
			$colspan = '1';
		}
		?>

			<thead>
				<tr>
					<th class="head" rowspan="2">등급</th>
					<th class="head" rowspan="2">부담율</th>
					<th class="head" rowspan="2">급여한도액</th>
					<th class="head" rowspan="2">서비스</th>
					<th class="head" rowspan="2">급여총액</th>
					<th class="head" colspan="3">본인부담액</th>
					<th class="head" rowspan="2">공단청구액</th>
					<th class="<?=$css?>" rowspan="2" colspan="<?=$colspan?>">비고</th>
				</tr>
				<tr>
					<th class="head">비급여</th>
					<th class="head">급여</th>
					<th class="head">계</th>
				</tr>
			</thead>
			<tbody><?
				/*********************************************************
				 *
				 *********************************************************/
					if (is_array($laData)){
						$liRowIdx = 0;
						foreach($laData as $laRow){
							if ($liRowIdx == 0){?>
								<tr>
								<td class="left" rowspan="3"><?=$laRow['lvlNm'];?></td>
								<td class="center" rowspan="3"><?=$laRow['rate'];?></td>
								<td class="right"><?=number_format($laRow['maxAmt']);?></td><?
							}else if ($liRowIdx == 1){?>
								<th class="center">한도잔액</th><?
							}else{?>
								<td class="right"><?=number_format($liBalance);?></td><?
							}?>

								<td class="center"><?=$laRow['svcNm'];?></td>
								<td class="right"><?=number_format($laRow['payTot']);?></td>
								<td class="right"><?=number_format($laRow['bipay']);?></td>
								<td class="right"><?=number_format($laRow['pay']);?></td>
								<td class="right"><?=number_format($laRow['expense']);?></td>
								<td class="right"><?=number_format($laRow['claim']);?></td><?
							if ($liRowIdx == 0){?>
								<td class="left top last" rowspan="3" colspan="<?=$colspan?>"><?
									if (is_array($laClient)){
										$lsLastDt = $myF->dateAdd('month', 1, $year.'-'.$month.'-01', 'Y-m-d');
										$lsLastDt = $myF->dateAdd('day', -1, $lsLastDt, 'Y-m-d');

										foreach($laClient as $laC){
											if ($year.'-'.$month.'-01' > $laC['from']){
												$lsFromDt = $year.'.'.$month.'.01';
											}else{
												$lsFromDt = $myF->dateStyle($laC['from'],'.');
											}

											if ($lsLastDt < $laC['to']){
												$lsToDt = $myF->dateStyle($lsLastDt,'.');
											}else{
												$lsToDt = $myF->dateStyle($laC['to'],'.');
											}
											echo $myF->mid($myF->_kindNm($laC['kind']),0,2).'('.$laC['rate'].') : '.$lsFromDt.'~'.$lsToDt.'<br>';
										}
									}?>
								</td><?
							}?>
							</tr><?
							$liRowIdx ++;

							$liPayTot  += $laRow['payTot'];
							$liBipay   += $laRow['bipay'];
							$liPay     += $laRow['pay'];
							$liExpense += $laRow['expense'];
							$liClaim   += $laRow['claim'];
						}
					}

					unset($laData);?>
			</tbody>
			<tbody>
				<tr>
					<th class="right" colspan="4">합계</th>
					<td class="right"><?=number_format($liPayTot);?></td>
					<td class="right"><?=number_format($liBipay);?></td>
					<td class="right"><?=number_format($liPay);?></td>
					<td class="right"><?=number_format($liExpense);?></td>
					<td class="right"><?=number_format($liClaim);?></td>
					<td class="last" colspan="<?=$colspan?>" >&nbsp;</td>
				</tr>
			</tbody>
		</table><?
	}
?>