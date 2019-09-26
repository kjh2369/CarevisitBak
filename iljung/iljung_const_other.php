<?
	$start_id  = $svc_id - ($svc_id % 10);
	$end_id    = $start_id + 10;

	foreach($kind_list as $num => $k_list){
		if ($k_list['id'] > $start_id && $k_list['id'] < $end_id){
			$const_k[sizeof($const_k)] = $k_list;
		}
	}

	$k_cnt = sizeof($const_k);

	if ($svc_id > 20 && $svc_id < 30){
		$svc_mode = 1;
	}else{
		$svc_mode = 2;
	}

	############################################################
	#
	# 서비스별 내역
	#
	############################################################

	if ($svc_mode == 1){
		$sql = "select voucher_kind as svc_cd
				,      voucher_suga_cost as svc_amt
				,      voucher_totaltime as svc_time
				,      voucher_gbn as gbn
				,      voucher_gbn2 as gbn2
				,      voucher_lvl as lvl
				  from voucher_make
				 where org_no        = '$code'
				   and voucher_jumin = '$jumin'
				   and voucher_yymm  = '$year$month'
				   and del_flag      = 'N'";
	}else{
		$sql = "select svc_cd
				,      svc_amt
				,      svc_time
				  from (
					   select m03_mkind as svc_cd
					   ,      m03_kupyeo_1 as svc_amt
					   ,      m03_kupyeo_2 as svc_time
					   ,      m03_sdate as from_dt
					   ,      m03_edate as to_dt
						 from m03sugupja
						where m03_ccode = '$code'
						  and m03_jumin = '$jumin'
						union all
					   select m31_mkind as svc_cd
					   ,      m31_kupyeo_1
					   ,      m31_kupyeo_2
					   ,      m31_sdate
					   ,      m31_edate
						 from m31sugupja
						where m31_ccode = '$code'
						  and m31_jumin = '$jumin'
					   ) as t
				 where svc_cd           >  '0'
				   and left(from_dt, 6) <= '$year$month'
				   and left(to_dt, 6)   >= '$year$month'";
	}

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		$mst[$row['svc_cd']] = $conn->select_row($i);
		$mst[$row['svc_cd']]['limit_pay'] = $row['svc_amt'] * $row['svc_time'];

		for($k=0; $k<$k_cnt; $k++){
			if ($const_k[$k]['code'] == $row['svc_cd']){
				$const_k[$k]['use'] = 'Y';
				break;
			}
		}
	}

	$conn->row_free();

	unset($row);

	############################################################
	#
	# 서비스별 일정 내역
	#
	############################################################
	foreach($const_k as $i => $k_list){
		$svc[$k_list['id']] = array('tot_pay'=>0, 'over_pay'=>0, 'bipay'=>0, 'use_time'=>0, 're_time'=>(is_numeric($mst[$k_list['code']]['svc_time']) ? $mst[$k_list['code']]['svc_time'] : 0), 'tot_time'=>(is_numeric($mst[$k_list['code']]['svc_time']) ? $mst[$k_list['code']]['svc_time'] : 0), 'limit_pay'=>(is_numeric($mst[$k_list['code']]['limit_pay']) ? $mst[$k_list['code']]['limit_pay'] : 0));
	}

	$svc['tot'] = array('tot_pay'=>0, 'over_pay'=>0, 'bipay'=>0, 'use_time'=>0, 're_time'=>0, 'tot_time'=>0, 'limit_pay'=>0);

	$sql = "select t01_mkind as svc_cd
			,      t01_svc_subcode as svc_id
			,      case t01_bipay_umu when 'Y' then 'Y' else 'N' end as bipay_yn
			,      t01_sugup_proctime as proctime
			,      t01_suga_tot as pay
			  from t01iljung
			 where t01_ccode = '$code'
			   and t01_jumin = '$jumin'
			   and t01_sugup_date like '$year$month%'
			   and t01_mkind  > '0'
			   and t01_del_yn = 'N'
			 order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$id = $row['svc_id'];
		$cd = $row['svc_cd'];

		/*
		switch($id){
			case 22:
				#####################################
				#
				# 노인볼봄
				# - 방문시 기본2시간이며
				#   30분미만은 0시간 30분이상은 1시간으로
				#   산정함.
				# - 주간보호는 1일 9시간으로 산정함
				#
				#####################################
				if ($mst[$cd]['gbn'] == 'D'){
					$time = $row['proctime'] / 60 / 9;
				}else{
					$time = $row['proctime'] / 60;
				}
				break;
			case 24:
				#####################################
				#
				# 장애인보조는 기본 1시간이며
				# 30분 미만은 0시간 30분이상은 1시간으로
				# 산정함.
				#
				#####################################
				$time = $myF->com_time($row['proctime']);
				break;
			default:
				$time = $row['proctime'] / 60;
		}
		*/

		if ($row['bipay_yn'] == 'Y'){
			$svc[$id]['bipay'] += $row['pay']; //비급여
		}else{
			$time = $row['proctime'];

			$svc[$id]['use_time'] += $time ; //사용시간
			$svc[$id]['re_time']  -= $time ; //잔여시간

			if ($svc[$id]['re_time'] < 0){
				//잔여시간이 없으면 초과금으로 계산
				$svc[$id]['over_pay'] += $row['pay'];
			}
			//총이용금액
			$svc[$id]['tot_pay'] += $row['pay'];
		}
	}

	$conn->row_free();

	unset($row);

	############################################################
	#
	# 화면 그리기
	#
	############################################################
	echo '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:'.__GAB__.'px;\'>';
		echo '<colgroup>';

		if ($svc_mode == 1){
			echo '<col width=\'100px\'>';
			echo '<col width=\'130px\'>';
			echo '<col width=\'130px\'>';
			echo '<col width=\'130px\'>';
			echo '<col width=\'130px\'>';
			echo '<col width=\'130px\'>';
		}else{
			echo '<col width=\'100px\'>';
			echo '<col width=\'130px\'>';
			echo '<col width=\'130px\'>';
		}

		echo '<col>';
		echo '</colgroup>';
		echo '<tbody>';

			echo '<tr>';
				echo '<th class=\'head bold\' colspan=\'7\'>'.$kind_nm.' 서비스 내역</th>';
			echo '</tr>';

			echo '<tr>';

			if ($svc_mode == 1){
				echo '<th class=\'head bold\'>구분</th>';
				echo '<th class=\'head bold\'>수급(급여)계</th>';
				echo '<th class=\'head bold\'>초과</th>';
				echo '<th class=\'head bold\'>비급여</th>';
				echo '<th class=\'head bold\'>이용시간(일)</th>';
				echo '<th class=\'head bold\'>잔여시간(일)</th>';
			}else{
				echo '<th class=\'head bold\'>구분</th>';
				echo '<th class=\'head bold\'>수급(급여)계</th>';
				echo '<th class=\'head bold\'>이용시간(일)</th>';
			}

			echo '<th class=\'head bold\'>비고</th>';
			echo '</tr>';

			foreach($const_k as $i => $k_list){
				$id = $k_list['id'];

				if ($k_list['use'] == 'Y'){
					echo '<tr>';

					if ($svc_mode == 1){
						echo '<th class=\'head bold\'>'.$k_list['name'].'</th>';
						echo '<td class=\'right\' id=\'tot_pay_'.$id.'\'  tag=\''.$svc[$id]['tot_pay'].'\'>'.number_format($svc[$id]['tot_pay']).'</td>';
						echo '<td class=\'right\' id=\'over_pay_'.$id.'\' tag=\''.$svc[$id]['over_pay'].'\'>'.number_format($svc[$id]['over_pay']).'</td>';
						echo '<td class=\'right\' id=\'bipay_'.$id.'\'    tag=\''.$svc[$id]['bipay'].'\'>'.number_format($svc[$id]['bipay']).'</td>';
						echo '<td class=\'right\' id=\'use_time_'.$id.'\' tag=\''.$svc[$id]['use_time'].'\'>'.$svc[$id]['use_time'].'</td>';
						echo '<td class=\'right\' id=\'re_time_'.$id.'\'  tag=\''.$svc[$id]['re_time'].'\'>'.$svc[$id]['re_time'].'</td>';
					}else{
						echo '<th class=\'head bold\'>'.$k_list['name'].'</th>';
						echo '<td class=\'right\' id=\'tot_pay_'.$id.'\'  tag=\''.$svc[$id]['tot_pay'].'\'>'.number_format($svc[$id]['tot_pay']).'</td>';
						echo '<td class=\'right\' id=\'use_time_'.$id.'\' tag=\''.$svc[$id]['use_time'].'\'>'.$svc[$id]['use_time'].'</td>';

						echo '<div id=\'over_pay_'.$id.'\' tag=\''.$svc[$id]['over_pay'].'\' style=\'display:none;\'>0</div>';
						echo '<div id=\'bipay_'.$id.'\'    tag=\''.$svc[$id]['bipay'].'\'    style=\'display:none;\'>0</div>';
						echo '<div id=\'re_time_'.$id.'\'  tag=\''.$svc[$id]['re_time'].'\'  style=\'display:none;\'>0</div>';
					}

					echo '<td class=\'left\'></td>';
					echo '</tr>';

					echo '<input name=\'tot_time_'.$id.'\'  type=\'hidden\' value=\''.$svc[$id]['tot_time'].'\'>';
					echo '<input name=\'limit_pay_'.$id.'\' type=\'hidden\' value=\''.$svc[$id]['limit_pay'].'\'>';

					$svc['tot']['tot_pay']   += $svc[$id]['tot_pay'];
					$svc['tot']['over_pay']  += $svc[$id]['over_pay'];
					$svc['tot']['bipay']     += $svc[$id]['bipay'];
					$svc['tot']['use_time']  += $svc[$id]['use_time'];
					$svc['tot']['re_time']   += $svc[$id]['re_time'];
					$svc['tot']['tot_time']  += $svc[$id]['tot_time'];
					$svc['tot']['limit_pay'] += $svc[$id]['limit_pay'];
				}
			}

			echo '<tr>';

			if ($svc_mode == 1){
				echo '<th class=\'head bold\'>계</th>';
				echo '<td class=\'right bold\' id=\'tot_pay_tot\'  tag=\''.$svc['tot']['tot_pay'].'\'>'.number_format($svc['tot']['tot_pay']).'</td>';
				echo '<td class=\'right bold\' id=\'over_pay_tot\' tag=\''.$svc['tot']['over_pay'].'\'>'.number_format($svc['tot']['over_pay']).'</td>';
				echo '<td class=\'right bold\' id=\'bipay_tot\'    tag=\''.$svc['tot']['bipay'].'\'>'.number_format($svc['tot']['bipay']).'</td>';
				echo '<td class=\'right bold\' id=\'use_time_tot\' tag=\''.$svc['tot']['use_time'].'\'>'.$svc['tot']['use_time'].'</td>';
				echo '<td class=\'right bold\' id=\'re_time_tot\'  tag=\''.$svc['tot']['re_time'].'\'>'.$svc['tot']['re_time'].'</td>';
			}else{
				echo '<th class=\'head bold\'>계</th>';
				echo '<td class=\'right bold\' id=\'tot_pay_tot\'  tag=\''.$svc['tot']['tot_pay'].'\'>'.number_format($svc['tot']['tot_pay']).'</td>';
				echo '<td class=\'right bold\' id=\'use_time_tot\' tag=\''.$svc['tot']['use_time'].'\'>'.$svc['tot']['use_time'].'</td>';

				echo '<div id=\'over_pay_tot\' tag=\''.$svc['tot']['over_pay'].'\' style=\'display:none;\'>'.number_format($svc['tot']['over_pay']).'</td>';
				echo '<div id=\'bipay_tot\'    tag=\''.$svc['tot']['bipay'].'\'    style=\'display:none;\'>'.number_format($svc['tot']['bipay']).'</td>';
				echo '<div id=\'re_time_tot\'  tag=\''.$svc['tot']['re_time'].'\'  style=\'display:none;\'>'.$svc['tot']['re_time'].'</td>';
			}

			echo '<td class=\'left bold\'></td>';
			echo '</tr>';

			echo '<input name=\'tot_time_tot\'  type=\'hidden\' value=\''.$svc['tot']['tot_time'].'\'>';
			echo '<input name=\'limit_pay_tot\' type=\'hidden\' value=\''.$svc['tot']['limit_pay'].'\'>';

		echo '</tbody>';
	echo '</table>';

	if ($svc_id == 23 || $svc_id == 31){
		echo '<table class=\'my_table my_border_blue\' style=\'width:100%; border-top:none;\'>
				<colgroup>
					<col width=\'50px\'>
					<col width=\'70px\'>
					<col width=\'90px\'>
					<col width=\'70px\'>
					<col width=\'90px\'>
					<col width=\'70px\'>
					<col width=\'90px\'>
					<col width=\'90px\'>
					<col width=\'90px\'>
					<col width=\'90px\'>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th class=\'center bold\' rowspan=\'3\'>추가<br>요금<br>합계</th>
						<th class=\'center bold\' colspan=\'2\'>미취학아동</th>
						<th class=\'center bold\' colspan=\'2\'>취학아동</th>
						<th class=\'center bold\' colspan=\'2\'>동거가족</th>
						<th class=\'center bold\' rowspan=\'2\'>입주<br>추가요금</th>
						<th class=\'center bold\' rowspan=\'2\'>공/휴일<br>추가요금</th>
						<th class=\'center bold\' rowspan=\'2\'>합계</th>
						<th class=\'center bold\' rowspan=\'2\'>비고</th>
					</tr>
					<tr>
						<th class=\'center bold\'>아동수</th>
						<th class=\'center bold\'>추가요금</th>
						<th class=\'center bold\'>아동수</th>
						<th class=\'center bold\'>추가요금</th>
						<th class=\'center bold\'>가족수</th>
						<th class=\'center bold\'>추가요금</th>
					</tr>
					<tr>
						<td class=\'center\'><div class=\'right\' id=\'addpayNotChildCnt\'>0</div></td>
						<td class=\'center\'><div class=\'right\' id=\'addpayNotChildAmt\'>0</div></td>
						<td class=\'center\'><div class=\'right\' id=\'addpayChildCnt\'>0</div></td>
						<td class=\'center\'><div class=\'right\' id=\'addpayChildAmt\'>0</div></td>
						<td class=\'center\'><div class=\'right\' id=\'addpayFamilyCnt\'>0</div></td>
						<td class=\'center\'><div class=\'right\' id=\'addpayFamilyAmt\'>0</div></td>
						<td class=\'center\'><div class=\'right\' id=\'addpayHouseAmt\'>0</div></td>
						<td class=\'center\'><div class=\'right\' id=\'addpayHolidayAmt\'>0</div></td>
						<td class=\'center\'><div class=\'right\' id=\'addpayTotalAmt\'>0</div></td>
						<td class=\'center\'><div class=\'left\'>&nbsp;</div></td>
					</tr>
				</tbody>
			  </table>';
	}

	unset($mst);
?>