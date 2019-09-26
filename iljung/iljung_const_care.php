<?
	// 수급자의 급여한도를 조회한다.
	if ($lbTestMode){
		/*********************************************************
			장기요양 등급
		*********************************************************/
		$sql = 'select min(level) as lvl
				  from client_his_lvl
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and date_format(from_dt,\'%Y%m\') <= \''.$ym.'\'
				   and date_format(to_dt,  \'%Y%m\') >= \''.$ym.'\'';

		$liLvlCd = $conn->get_data($sql);

		/*********************************************************
			수급자 구분
		*********************************************************/
		$sql = 'select rate
				  from client_his_kind
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and date_format(from_dt,\'%Y%m\') <= \''.$ym.'\'
				   and date_format(to_dt,  \'%Y%m\') >= \''.$ym.'\'
				 order by seq desc
				 limit 1';

		$liRate = $conn->get_data($sql);

		/*********************************************************
			청구한도
		*********************************************************/
		$sql = 'select amt
				  from client_his_limit
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and date_format(from_dt,\'%Y%m\') <= \''.$ym.'\'
				   and date_format(to_dt,  \'%Y%m\') >= \''.$ym.'\'
				 order by seq desc
				 limit 1';

		$liLimitAmt = $conn->get_data($sql);

		$bonin_yul  = $liRate;	   //본인부담율
		$client_lvl = $liLvlCd;    //수급자등급
		$max_group  = $liLimitAmt; //청구한도금액
	}else{
		$sql = "select m03_kupyeo_max, m03_kupyeo_1, m03_bonin_yul, m03_ylvl, m03_skind
				  from (
					   select m03_kupyeo_max, m03_kupyeo_1, m03_bonin_yul, m03_ylvl, m03_skind
					   ,      m03_sdate
					   ,      m03_edate
						 from m03sugupja
						where m03_ccode = '$code'
						  and m03_mkind = '$kind'
						  and m03_jumin = '$jumin'
						union all
					   select m31_kupyeo_max, m31_kupyeo_1, m31_bonin_yul, m31_level, m31_kind
					   ,      m31_sdate
					   ,      m31_edate
						 from m31sugupja
						where m31_ccode = '$code'
						  and m31_mkind = '$kind'
						  and m31_jumin = '$jumin'
					   ) as t
				 where '$ym' between left(m03_sdate, 6) and left(m03_edate, 6)
				 order by m03_sdate desc, m03_edate desc
				 limit 1";

		$client_array = $conn->get_array($sql);
		$max_amount   = $client_array[0];	//한도금액
		$max_group    = $client_array[1];	//정부지원금한도액
		$bonin_yul    = $client_array[2];	//본인부담율
		$client_lvl   = $client_array[3];   //수급자등급
		$client_kind  = $client_array[4];	//수급자구분
	}


	/*********************************************************

		한도금액을 가져온다.

	*********************************************************/
	$max_amount = $conn->_limit_pay($client_lvl, $ym);
	#if ($ym >= '201201') $max_group = $max_amount;
	if (empty($max_group)) $max_group = $max_amount;
	if ($max_group > $max_amount) $max_group = $max_amount;

	$limit_amt = $max_group;


	if ($type == 'search'){
		$title = '수급자 월수급 현황(실적기준)';
	}else{
		$title = '수급자 월수급 현황';
	}

	$sql = "select t01_svc_subcode as code
			,      case t01_bipay_umu when 'Y' then 'Y' else 'N' end as bipay_yn
			,      t01_suga_tot as pay
			  from t01iljung
			 where t01_ccode = '$code'
			   and t01_mkind = '$kind'
			   and t01_jumin = '$jumin'
			   and t01_sugup_date like '$ym%'
			   and t01_del_yn ='N'";

	if ($type == 'search'){
		$sql .= " and t01_status_gbn = '1'";
	}

	$sql .= "
			 order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	$temp_max_amount = 0;

	$amt = array('200' => array('total' => 0, 'bonin' => 0, 'over' => 0, 'bipay' => 0),
				 '500' => array('total' => 0, 'bonin' => 0, 'over' => 0, 'bipay' => 0),
				 '800' => array('total' => 0, 'bonin' => 0, 'over' => 0, 'bipay' => 0),
				 'tot' => array('total' => 0, 'bonin' => 0, 'over' => 0, 'bipay' => 0));

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		if ($row['bipay_yn'] == 'Y'){
			$amt[$row['code']]['bipay'] += $row['pay'];
		}else{
			$temp_max_amount += $row['pay'];

			if ($limit_amt > $temp_max_amount){
				$amt[$row['code']]['total'] += $row['pay'];
				$amt[$row['code']]['bonin'] += ($row['pay'] * $bonin_yul / 100);
			}else{
				if ($limit_amt >= $amt['200']['total'] + $amt['500']['total'] + $amt['800']['total']){
					$temp_max_prc = $limit_amt - ($amt['200']['total'] + $amt['500']['total'] + $amt['800']['total']);
					$amt[$row['code']]['total'] += $temp_max_prc;
					$amt[$row['code']]['bonin'] += ($temp_max_prc * $bonin_yul / 100);
				}
				$amt[$row['code']]['over'] += $row['pay'] - $temp_max_prc;
			}
		}
	}

	$amt['200']['bonin'] = floor($amt['200']['bonin']);
	$amt['500']['bonin'] = floor($amt['500']['bonin']);
	$amt['800']['bonin'] = floor($amt['800']['bonin']);

	$amt['tot']['total'] = $amt['200']['total'] + $amt['500']['total'] + $amt['800']['total'];
	$amt['tot']['bonin'] = $amt['200']['bonin'] + $amt['500']['bonin'] + $amt['800']['bonin'];
	$amt['tot']['over']  = $amt['200']['over']  + $amt['500']['over']  + $amt['800']['over'];
	$amt['tot']['bipay'] = $amt['200']['bipay'] + $amt['500']['bipay'] + $amt['800']['bipay'];

	$sur_amount = $limit_amt - ($amt['tot']['total'] + $amt['tot']['over']);

	$conn->row_free();

	ob_start();

	echo '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:'.__GAB__.'px;\'>';
	echo '	<colgroup>
				<col width=\'100px\'>
				<col width=\'130px\'>
				<col width=\'130px\'>
				<col width=\'130px\'>
				<col width=\'130px\'>
				<col width=\'130px\'>
				<col>
			</colgroup>';
	echo '	<tbody>';

	echo '		<tr>';
	echo '			<th class=\'head bold\' colspan=\'6\'>'.$kind_nm.' 서비스 내역</th>';
	echo '			<th class=\'head bold\'>급여한도금액</th>';
	echo '		</tr>';

	echo '		<tr>';
	echo '			<th class=\'head bold\'>구분</th>';
	echo '			<th class=\'head bold\'>수급(급여)계</th>';
	echo '			<th class=\'head bold\'>본인부담액</th>';
	echo '			<th class=\'head bold\'>초과</th>';
	echo '			<th class=\'head bold\'>비급여</th>';
	echo '			<th class=\'head bold\'>본인부담계</th>';
	echo '			<td class=\'right bold\'>'.number_format($max_amount).'원</td>';
	echo '		</tr>';

	echo '		<tr>';
	echo '			<th class=\'head bold\'>요양</th>';
	echo '			<td class=\'right\' id=\'txt_200_total\' tag=\''.($amt['200']['total']).'\'>'.number_format($amt['200']['total']).'</td>';
	echo '			<td class=\'right\' id=\'txt_200_bonin\' tag=\''.($amt['200']['bonin']).'\'>'.number_format($amt['200']['bonin']).'</td>';
	echo '			<td class=\'right\' id=\'txt_200_over\'	 tag=\''.($amt['200']['over']).'\'>'.number_format($amt['200']['over']).'</td>';
	echo '			<td class=\'right\' id=\'txt_200_bipay\' tag=\''.($amt['200']['bipay']).'\'>'.number_format($amt['200']['bipay']).'</td>';
	echo '			<td class=\'right\' id=\'txt_200_sum\'	 tag=\''.($amt['200']['bonin']+$amt['200']['over']+$amt['200']['bipay']).'\'>'.number_format($amt['200']['bonin']+$amt['200']['over']+$amt['200']['bipay']).'</td>';
	echo '			<th class=\'head bold\'>청구한도금액</th>';
	echo '		</tr>';

	echo '		<tr>';
	echo '			<th class=\'head bold\'>목욕</th>';
	echo '			<td class=\'right\' id=\'txt_500_total\' tag=\''.($amt['500']['total']).'\'>'.number_format($amt['500']['total']).'</td>';
	echo '			<td class=\'right\' id=\'txt_500_bonin\' tag=\''.($amt['500']['bonin']).'\'>'.number_format($amt['500']['bonin']).'</td>';
	echo '			<td class=\'right\' id=\'txt_500_over\'  tag=\''.($amt['500']['over']).'\'>'.number_format($amt['500']['over']).'</td>';
	echo '			<td class=\'right\' id=\'txt_500_bipay\' tag=\''.($amt['500']['bipay']).'\'>'.number_format($amt['500']['bipay']).'</td>';
	echo '			<td class=\'right\' id=\'txt_500_sum\'   tag=\''.($amt['500']['bonin']+$amt['500']['over']+$amt['500']['bipay']).'\'>'.number_format($amt['500']['bonin']+$amt['500']['over']+$amt['500']['bipay']).'</td>';
	echo '			<td class=\'right bold\'>'.number_format($max_group).'원</td>';
	echo '		</tr>';

	echo '		<tr>';
	echo '			<th class=\'head bold\'>간호</th>';
	echo '			<td class=\'right\' id=\'txt_800_total\' tag=\''.($amt['800']['total']).'\'>'.number_format($amt['800']['total']).'</td>';
	echo '			<td class=\'right\' id=\'txt_800_bonin\' tag=\''.($amt['800']['bonin']).'\'>'.number_format($amt['800']['bonin']).'</td>';
	echo '			<td class=\'right\' id=\'txt_800_over\'  tag=\''.($amt['800']['over']).'\'>'.number_format($amt['800']['over']).'</td>';
	echo '			<td class=\'right\' id=\'txt_800_bipay\' tag=\''.($amt['800']['bipay']).'\'>'.number_format($amt['800']['bipay']).'</td>';
	echo '			<td class=\'right\' id=\'txt_800_sum\'   tag=\''.($amt['800']['bonin']+$amt['800']['over']+$amt['800']['bipay']).'\'>'.number_format($amt['800']['bonin']+$amt['800']['over']+$amt['800']['bipay']).'</td>';
	echo '			<th class=\'head bold\'>청구한도잔액</th>';
	echo '		</tr>';

	echo '		<tr>';
	echo '			<th class=\'head bold\'>계</th>';
	echo '			<td class=\'right bold\' id=\'txt_tot_total\' tag=\''.($amt['tot']['total']).'\'>'.number_format($amt['tot']['total']).'</td>';
	echo '			<td class=\'right bold\' id=\'txt_tot_bonin\' tag=\''.($amt['tot']['bonin']).'\'>'.number_format($amt['tot']['bonin']).'</td>';
	echo '			<td class=\'right bold\' id=\'txt_tot_over\'  tag=\''.($amt['tot']['over']).'\'>'.number_format($amt['tot']['over']).'</td>';
	echo '			<td class=\'right bold\' id=\'txt_tot_bipay\' tag=\''.($amt['tot']['bipay']).'\'>'.number_format($amt['tot']['bipay']).'</td>';
	echo '			<td class=\'right bold\' id=\'txt_tot_sum\'   tag=\''.($amt['tot']['bonin']+$amt['tot']['over']+$amt['tot']['bipay']).'\'>'.number_format($amt['tot']['bonin']+$amt['tot']['over']+$amt['tot']['bipay']).'</td>';
	echo '			<td class=\'right bold\' id=\'txt_sur_amt\' tag=\''.($sur_amount).'\' style=\'color:'.($sur_amount > 0 ? '#0000ff' : '#ff0000').';\'>'.number_format($sur_amount).'원</td>';
	echo '		</tr>';

	echo '	</tbody>';
	echo '</table>';

	########################################################
	#
	# 변수설정
	#
	########################################################

	echo '<input name=\'maxAmount\'  type=\'hidden\' value=\''.$max_group.'\'>';
	echo '<input name=\'max_amount\' type=\'hidden\' value=\''.$max_group.'\' tag=\''.$max_group.'\'>';
	echo '<input name=\'bonin_yul\'  type=\'hidden\' value=\''.$bonin_yul.'\' tag=\''.$bonin_yul.'\'>';

	$html = ob_get_contents();

	ob_end_clean();

	echo $html;
?>