<div class="title title_border">장기요양 확정내역</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="40px">
		<col width="70px">
		<col width="60px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">등급</th>
			<th class="head" rowspan="2">부담율</th>
			<th class="head" rowspan="2">급여한도액</th>
			<th class="head" rowspan="2">서비스</th>
			<th class="head" rowspan="2">급여총액</th>
			<th class="head" colspan="3">본인부담액</th>
			<th class="head" rowspan="2">공단청구액</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<!--th class="head">초과+비급여</th>
			<th class="head">순수</th-->
			<th class="head">비급여</th>
			<th class="head">급여</th>
			<th class="head">계</th>
		</tr>
	</thead>
	<tbody>
	<?

		if ($lbTestMode){
			/*********************************************************
				고객등급 이력
			*********************************************************/
			$sql = 'select level as lvl
					,      m03_key as c_key
					,      from_dt
					,      to_dt
					  from client_his_lvl as lvl
					 inner join m03sugupja as mst
						on mst.m03_ccode = lvl.org_no
					   and mst.m03_jumin = lvl.jumin
					 where org_no                         = \''.$code.'\'
					   and jumin                          = \''.$jumin.'\'
					   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					   and date_format(to_dt,\'%Y%m\')   >= \''.$year.$month.'\'
					 order by level
					 limit 1';

			$laCltLvl = $conn->get_array($sql);
			$lsDt     = str_replace('-','',$myF->_getDt($laCltLvl['from_dt'],$laCltLvl['to_dt']));

			if (empty($laCltLvl['lvl'])) $laCltLvl['lvl'] = '9';

			/*********************************************************
				한도금액
			*********************************************************/
			$sql = 'select m91_kupyeo
					  from m91maxkupyeo
					 where m91_code   = \''.$laCltLvl['lvl'].'\'
					   and m91_sdate <= \''.$lsDt.'\'
					   and m91_edate >= \''.$lsDt.'\'';

			$liLimitAmt = $conn->get_data($sql);

			if (!is_numeric($liLimitAmt)) $liLimitAmt = 0;


			/*********************************************************
				고객구분 이력
			*********************************************************/
			$sql = 'select kind
					,      rate
					  from client_his_kind
					 where org_no                         = \''.$code.'\'
					   and jumin                          = \''.$jumin.'\'
					   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					   and date_format(to_dt,\'%Y%m\')   >= \''.$year.$month.'\'
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

				$client[$i]['jumin']      = $jumin;
				$client[$i]['lvl']        = $myF->_lvlNm($laCltLvl['lvl']);
				$client[$i]['ylvl']       = $laCltLvl['lvl'];
				$client[$i]['skind']      = $row['kind'];
				$client[$i]['bonin_yul']  = number_format($row['rate'],1);
				$client[$i]['max_pay']    = $liLimitAmt;
				$client[$i]['client_key'] = $laCltLvl['c_key'];
			}

			$conn->row_free();
			$client_count = $rowCount;

			/*
			$laCltKind = $conn->get_array($sql);

			if (empty($laCltKind['kind'])) $laCltKind['kind'] = '1';
			if (empty($laCltKind['rate'])){
				if ($laCltLvl['lvl'] != '9')
					$laCltKind['rate'] = 15;
				else
					$laCltKind['rate'] = 100;
			}

			$client[0]['jumin']      = $jumin;
			$client[0]['lvl']        = $myF->_lvlNm($laCltLvl['lvl']);
			$client[0]['ylvl']       = $laCltLvl['lvl'];
			$client[0]['skind']      = $laCltKind['kind'];
			$client[0]['bonin_yul']  = number_format($laCltKind['rate'],1);
			$client[0]['max_pay']    = $liLimitAmt;
			$client[0]['client_key'] = $laCltLvl['c_key'];
			$client_count = 1;
			*/
		}else{
			$sql = "select distinct jumin, LVL.m81_name as lvl, client_key, skind, max_pay, bonin_yul, ylvl
					  from (
						   select m03_jumin as jumin
						   ,      m03_name as name
						   ,      m03_key as client_key
						   ,      m03_ylvl as ylvl
						   ,      m03_skind as skind
						   ,      m03_kupyeo_max as max_pay
						   ,      m03_bonin_yul as bonin_yul
						   ,      m03_sdate as sdate
						   ,      m03_edate as edate
							 from m03sugupja
							where m03_ccode = '$code'
							  and m03_mkind = '$kind'
							  and m03_jumin = '$jumin'
							union all
						   select m31_jumin
						   ,      m03_name
						   ,      m03_key
						   ,      m31_level
						   ,      m31_kind
						   ,      m31_kupyeo_max
						   ,      m31_bonin_yul
						   ,      m31_sdate as sdate
						   ,      m31_edate as edate
							 from m31sugupja
							inner join m03sugupja
							   on m03_ccode = m31_ccode
							  and m03_mkind = m31_mkind
							  and m03_jumin = m31_jumin
							where m31_ccode = '$code'
							  and m31_mkind = '$kind'
							  and m31_jumin = '$jumin'
						   ) as t
					 inner join m81gubun as LVL
						on LVL.m81_gbn  = 'LVL'
					   and LVL.m81_code = ylvl
					 where '$year$month' between left(sdate, 6) and left(edate, 6)
					 order by sdate, edate";

			$conn->query($sql);
			$conn->fetch();

			$rowCount = $conn->row_count();

			for($i=0; $i<$rowCount; $i++){
				$client[$i] = $conn->select_row($i);
			}

			$conn->row_free();

			$client_count = $rowCount;
		}

		if ($client_count > 0){
			$tot_amt     = 0; //급여총액
			$over_amt    = 0; //초과, 비급여
			$bonin_amt   = 0; //순수
			$tot_sub_amt = 0; //계
			$gongdan_amt = 0; //공단청구액

			for($client_i=0; $client_i<$client_count; $client_i++){
				$sql = "";
				$skind     = $client[$client_i]['skind'];
				$bonin_yul = $client[$client_i]['bonin_yul'];



				/*********************************************************
					한도금액 조회
				*********************************************************/
					$max_amount = $conn->_limit_pay($client[$client_i]['ylvl'], $year.$month);
					if (empty($max_group)) $max_group = $max_amount;
					if ($max_group > $max_amount) $max_group = $max_amount;



				for($i=1; $i<=3; $i++){
					switch($i){
					case 1:
						$gubun = '방문요양';
						$serviceCode = '200';
						break;
					case 2:
						$gubun = '방문목욕';
						$serviceCode = '500';
						break;
					case 3:
						$gubun = '방문간호';
						$serviceCode = '800';
						break;
					}

					if ($i > 1) $sql .= " union all ";

					if ($lbTestMode){
						$sql .="select '$gubun' as gubun
								,      '$serviceCode' as service_code
								,      t13_bonin_yul as bonin_yul
								,      t13_max_amt as max_amt
								,      t13_suga_tot$i as suga_tot
								,      t13_over_amt$i as over_amt
								,      t13_bipay$i as bipay
								,      t13_bonin_amt$i as bonin_amt
								,      t13_bonbu_tot$i as bonin_tot
								,      t13_chung_amt$i as chung_amt
								  from t13sugupja
								 where  t13_ccode     = '$code'
								   and  t13_mkind     = '$kind'
								   and  t13_jumin     = '$jumin'
								   and  t13_pay_date  = '$year$month'
								   and  t13_bonin_yul = '$bonin_yul'
								   and  t13_type      = '2'";
					}else{
						$sql .="select '$gubun' as gubun
								,      '$serviceCode' as service_code
								,      t13_bonin_yul as bonin_yul
								,      t13_max_amt as max_amt
								,      t13_suga_tot$i as suga_tot
								,      t13_over_amt$i as over_amt
								,      t13_bipay$i as bipay
								,      t13_bonin_amt$i as bonin_amt
								,      t13_bonbu_tot$i as bonin_tot
								,      t13_chung_amt$i as chung_amt
								  from t13sugupja
								 where  t13_ccode     = '$code'
								   and  t13_mkind     = '$kind'
								   and  t13_jumin     = '$jumin'
								   and  t13_pay_date  = '$year$month'
								   /*and (t13_bonin_yul = '$skind' or t13_bonin_yul = '$bonin_yul')*/
								   and  t13_type      = '2'";
					}
				}
				$sql .= "order by bonin_yul, service_code";

				$conn->query($sql);
				$conn->fetch();
				$rowCount = $conn->row_count();

				$html = '';

				if ($rowCount > 0){
					for($i=0; $i<$rowCount; $i++){
						$row = $conn->select_row($i);

						$html .= '<tr>';

						switch($i){
							case 0:
								$html .= '<td class=\'center\' rowspan=\'3\'>'.$client[$client_i]['lvl'].'</td>';
								$html .= '<td class=\'right\'  rowspan=\'3\'>'.$client[$client_i]['bonin_yul'].'</td>';
								$html .= '<td class=\'right\'>'.number_format($max_group).'</td>';

								$max_pay = $client[$client_i]['max_pay'];
								break;
							case 1:
								$html .= '<th class=\'head\'>한도잔액</th>';
								break;
							case 2:
								$html .= '<td class=\'right\'> _TEMP_AMT_ </td>';
								break;
						}

						$html .= '<td class=\'center\'>'.$row['gubun'].'</td>';
						$html .= '<td class=\'right\'>'.number_format($row['suga_tot']-$row['bipay']).'</td>';
						$html .= '<td class=\'right\'>'.number_format($row['over_amt']+$row['bipay']).'</td>';
						$html .= '<td class=\'right\'>'.number_format($row['bonin_amt']).'</td>';
						$html .= '<td class=\'right\'>'.number_format($row['bonin_tot']).'</td>';
						$html .= '<td class=\'right\'>'.number_format($row['chung_amt']).'</td>';
						$html .= '<td class=\'left last\'>&nbsp;</td>';

						$html .= '</tr>';

						$tot_amt	 += ($row['suga_tot']-$row['bipay']);
						$over_amt    += ($row['over_amt']+$row['bipay']);
						$bonin_amt   += ($row['bonin_amt']);
						$tot_sub_amt += ($row['bonin_tot']);
						$gongdan_amt += ($row['chung_amt']);
					}
				}

				$conn->row_free();
			}
			unset($client);
		}

		$html = str_replace(' _TEMP_AMT_ ', number_format($max_group - $tot_amt), $html);

		echo $html;
	?>
	</tbody>
	<tbody>
		<tr>
			<th class="right" colspan="4">합계</th>
			<td class="right"><?=number_format($tot_amt);?></td>
			<td class="right"><?=number_format($over_amt);?></td>
			<td class="right"><?=number_format($bonin_amt);?></td>
			<td class="right"><?=number_format($tot_sub_amt);?></td>
			<td class="right"><?=number_format($gongdan_amt);?></td>
			<td class="last">&nbsp;</td>
		</tr>
	</tbody>
</table>