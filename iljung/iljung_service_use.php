<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once("iljung_config.php");

	ob_start();

	##########################################################
	#
	# 바우처 생성
	#
	##########################################################

	$code      = $_POST['code'];
	$svc_id    = $_POST['svc_id'];
	$jumin     = $ed->de($_POST['jumin']);
	$year      = $_POST['year'];
	$month     = $_POST['month'];
	$month     = (intval($month) < 10 ? '0' : '').intval($month);
	$kind_list = $conn->kind_list($code, true);
	$svc_cd    = $conn->kind_code($kind_list, $svc_id);
	$seq       = $_POST['seq'];
	$onload	   = $_POST['onload'];

	if (empty($onload)) $onload = 2;

	if (intval($month) != 2)
		$entOverYN = true;
	else
		$entOverYN = false;

	###########################################################
	#
	# 일정등록여부를 확인한다.
		$sql = 'select count(*)
				  from t01iljung
				 where t01_ccode  = \''.$code.'\'
				   and t01_mkind  = \''.$svc_cd.'\'
				   and t01_jumin  = \''.$jumin.'\'
				   and t01_del_yn = \'N\'
				   and left(t01_sugup_date,6) = \''.$year.$month.'\'';

		$cnt = $conn->get_data($sql);

		if (empty($cnt)) $cnt = 0;
	#
	###########################################################

	$sql = 'select voucher_seq
			  from voucher_make
			 where org_no        = \''.$code.'\'
			   and voucher_kind  = \''.$svc_cd.'\'
			   and voucher_jumin = \''.$jumin.'\'
			   and voucher_yymm  = \''.$year.$month.'\'
			   and del_flag      = \'N\'';

	$seq = $conn->get_data($sql);

	if (empty($seq)) $seq = 0;

	if ($seq == 0)
		$mode = 1;
	else
		$mode = 2;

	############################################################
	#
	# 이월시간 조회

		$chk_year  = intval($year);
		$chk_month = intval($month) - 1;

		if ($chk_month < 1){
			$chk_month = 12;
			$chk_year -= 1;
		}

		$chk_month = ($chk_month < 10 ? '0' : '').$chk_month;

		/*
		$sql = 'select t13_save_time
				,      t13_save_pay
				  from t13sugupja
				 where t13_ccode    = \''.$code.'\'
				   and t13_mkind    = \''.$svc_cd.'\'
				   and t13_jumin    = \''.$jumin.'\'
				   and t13_pay_date = \''.$chk_year.$chk_month.'\'
				   and t13_type     = \'2\'';

		$tmp = $conn->get_array($sql);

		if (!is_array($tmp)){
			$sql = 'select voucher_month_time
					,      voucher_month_time * voucher_suga_cost
					,      voucher_month_pay
					  from voucher_make
					 where org_no        = \''.$code.'\'
					   and voucher_kind  = \''.$svc_cd.'\'
					   and voucher_jumin = \''.$jumin.'\'
					   and voucher_yymm  = \''.$chk_year.$chk_month.'\'
					   and del_flag      = \'N\'';

			$tmp = $conn->get_array($sql);
		}

		$month_time = $tmp[0];
		$month_pay  = $tmp[1];

		if (empty($month_time)) $month_time = 0;
		if (empty($month_pay))  $month_pay  = 0;
		*/
		if ($mode == 1){
			/*********************************************************
				생성 단가
			*********************************************************/
			$sql = 'select voucher_suga_cost
					  from voucher_make
					 where org_no        = \''.$code.'\'
					   and voucher_kind  = \''.$svc_cd.'\'
					   and voucher_jumin = \''.$jumin.'\'
					   and voucher_yymm  = \''.$chk_year.$chk_month.'\'
					   and del_flag      = \'N\'';

			$lastMonthCost = intval($conn->get_data($sql));


			/*********************************************************
				전월 생성 금액 및 시간
			*********************************************************/
			if ($svc_id == 24){
				/*********************************************************
					장애인활동지원(금액)
				*********************************************************/
				$sql = 'select voucher_makepay + voucher_addpay + voucher_overpay';
			}else{
				/*********************************************************
					그외(시간)
				*********************************************************/
				$sql = 'select voucher_maketime + voucher_addtime + voucher_overtime';
			}

			$sql .= ' from voucher_make
					 where org_no        = \''.$code.'\'
					   and voucher_kind  = \''.$svc_cd.'\'
					   and voucher_jumin = \''.$jumin.'\'
					   and voucher_yymm  = \''.$chk_year.$chk_month.'\'
					   and del_flag      = \'N\'';

			#전월 생성금액 및 시간
			$lastMonthMake = intval($conn->get_data($sql));


			/*********************************************************
				사용한 금액 및 시간
			*********************************************************/
			if ($svc_id == 24){
				/*********************************************************
					한도정보
				*********************************************************/
				$sql = 'select voucher_maketime + (voucher_overpay / voucher_suga_cost) + voucher_addtime as time0, voucher_makepay + voucher_overpay + voucher_addpay as pay0
						,      voucher_addtime1 + voucher_addtime2 as time1, (voucher_addtime1 + voucher_addtime2) * voucher_suga_cost as pay1
						,      voucher_suga_cost as suga_cost
						,      insert_dt
						,      update_dt
						  from voucher_make
						 where org_no        = \''.$code.'\'
						   and voucher_kind  = \''.$svc_cd.'\'
						   and voucher_jumin = \''.$jumin.'\'
						   and voucher_yymm  = \''.$chk_year.$chk_month.'\'
						   and del_flag      = \'N\'';
				$voucherLimitInfo = $conn->get_array($sql);

				if (!empty($voucherLimitInfo['update_dt'])) $strLastModDT = $myF->dateStyle($voucherLimitInfo['update_dt'],'.'); else $strLastModDT = $myF->dateStyle($voucherLimitInfo['insert_dt'],'.');


				if (is_array($voucherLimitInfo)){
					/*********************************************************
						장애인활동지원(금액)
					*********************************************************/
					$sql = 'select sum(t01_conf_suga_value)
							  from t01iljung
							 where t01_ccode      = \''.$code.'\'
							   and t01_mkind      = \''.$svc_cd.'\'
							   and t01_jumin      = \''.$jumin.'\'
							   and t01_status_gbn = \'1\'
							   and t01_bipay_umu != \'Y\'
							   and t01_del_yn     = \'N\'
							   and left(t01_sugup_date, 6) = \''.$chk_year.$chk_month.'\'';

					$sugaCost   = $voucherLimitInfo['suga_cost'];
					$li_usePay  = intval($conn->get_data($sql));
					$li_useTime = round($li_usePay / $sugaCost, 1);

					$arrTmpPay = array(0, 0);

					for($i=0; $i<2; $i++){
						$leftPay  = $voucherLimitInfo['pay'.$i];
						$leftTime = $leftPay / $sugaCost;

						if ($leftTime - $li_useTime > 0){
							$time = $leftTime - $li_useTime;
							$arrTmpPay[$i] = round($time * $sugaCost);

							break;
						}else{
							$li_useTime   -= floor($leftTime);
							$pay           = floor($leftTime) * $sugaCost;
							$arrTmpPay[$i] = $leftPay - $pay;
						}
					}

					$lastMonthUse = $arrTmpPay[0];
				}

				/*********************************************************
					장애인활동지원(금액)
				*********************************************************/
				/*
				$sql = 'select sum(t01_conf_suga_value)
						  from t01iljung
						 where t01_ccode      = \''.$code.'\'
						   and t01_mkind      = \''.$svc_cd.'\'
						   and t01_jumin      = \''.$jumin.'\'
						   and t01_status_gbn = \'1\'
						   and t01_del_yn     = \'N\'
						   and left(t01_sugup_date, 6) = \''.$chk_year.$chk_month.'\'';

				#전월 사용금액 및 시간
				#$lastMonthUse = intval($conn->get_data($sql));
				*/
			}else{
				/*********************************************************
					그외(시간)
				*********************************************************/
				$sql = 'select sum(t01_conf_soyotime)
						  from t01iljung
						 where t01_ccode      = \''.$code.'\'
						   and t01_mkind      = \''.$svc_cd.'\'
						   and t01_jumin      = \''.$jumin.'\'
						   and t01_status_gbn = \'1\'
						   and t01_del_yn     = \'N\'
						   and left(t01_sugup_date, 6) = \''.$chk_year.$chk_month.'\'';

				#전월 사용금액 및 시간
				$lastMonthUse = intval($conn->get_data($sql));
			}


			if ($svc_id == 24){
				/*********************************************************
					장애인활동지원(금액)
				*********************************************************/
				#@$month_pay  = $lastMonthMake - $lastMonthUse;
				@$month_pay  = $lastMonthUse;
				@$month_time = round($month_pay / $lastMonthCost,1);
			}else{
				/*********************************************************
					그외(시간)
				*********************************************************/
				@$month_time = $lastMonthMake - $lastMonthUse;
				@$month_pay  = $month_time * $lastMonthCost;
			}
		}else{
			$sql = "select voucher_lvl as lvl
					,      voucher_gbn as gbn
					,      voucher_gbn2 as gbn2
					,      voucher_svc_kind as svc_kind
					,      voucher_overtime as overtime
					,      voucher_overpay as overpay
					,      voucher_addtime1 as addtime1
					,      voucher_addtime2 as addtime2
					,      voucher_maketime as maketime
					,      voucher_totaltime as totaltime
					,      voucher_add_pay_gbn as add_pay_gbn
					  from voucher_make
					 where org_no        = '$code'
					   and voucher_kind  = '$svc_cd'
					   and voucher_jumin = '$jumin'
					   and voucher_yymm  = '$year$month'
					   and voucher_seq   = '$seq'
					   and del_flag      = 'N'";

			$voucher_make = $conn->get_array($sql);

			@$month_pay  = $voucher_make['overpay'];
			@$month_time = $voucher_make['overtime'];
		}

		if (empty($month_time) || $month_time < 0) $month_time = 0;
		if (empty($month_pay)  || $month_pay  < 0) $month_pay  = 0;
	#
	############################################################


	/*********************************************************

		매년 2월은 이월급액을 적용하지 않는다.

	*********************************************************/
	if (!$entOverYN){
		$month_time = 0;
		$month_pay  = 0;
	}


	###########################################################
	#
	# 전월의 이월시간과 생성시간을 찾는다.

		#바우처를 생성한다.
		$make_voucher_yn = true;

		/*
		$sql = "select voucher_maketime as maketime
				,      voucher_makepay as makepay
				,      voucher_month_time as montime
				,      voucher_month_pay as monpay
				,      voucher_gbn as gbn
				,      voucher_gbn2 as gbn2
				,      voucher_lvl as lvl
				,      voucher_add_pay_gbn as add_pay_gbn
				  from voucher_make
				 where org_no        = '$code'
				   and voucher_kind  = '$svc_cd'
				   and voucher_jumin = '$jumin'
				   and voucher_yymm  = '$chk_year$chk_month'";

		$tmp_mon = $conn->get_array($sql);

		if ($svc_id == 24){
			if ($li_usePay > 0){
				if ($entOverYN && $tmp_mon['makepay'] > 0){
				}else{
					#바우처를 생성하지 않으며 이월시간만 설정한다.
					$make_voucher_yn = false;
				}
			}
		}else{
			if ($entOverYN && $tmp_mon['montime'] >= $tmp_mon['maketime'] && $tmp_mon['maketime'] > 0){
				#바우처를 생성하지 않으며 이월시간만 설정한다.
				$make_voucher_yn = false;
			}
		}

		if (!$entOverYN) $make_voucher_yn = true;
		*/

	#
	###########################################################

	###########################################################
	#
	# 현재 년월이 아니면 수정 불가능이다.
		/*
		if ($year.$month == date('Ym', mktime()))
			$edit_yn = 'Y';
		else
			$edit_yn = 'N';
		*/
		$edit_yn = 'Y';
	#
	###########################################################

	if ($lbTestMode){
		$sql = 'select his.kind
				,      his.from_dt
				,      his.to_dt
				,      mst.lvl
				,      mst.gbn
				,      mst.gbn2
				,      mst.svc_kind
				,      mst.addtime1
				,      mst.addtime2
				,      mst.add_pay_gbn
				,    '.$month_time.' as overtime
				,    '.$month_pay.' as overpay
				  from (
					   select jumin
					   ,      svc_cd as kind
					   ,      date_format(from_dt,\'%Y%m\') as from_dt
					   ,      date_format(to_dt,\'%Y%m\') as to_dt
						 from client_his_svc
						where org_no  = \''.$code.'\'
						  and jumin   = \''.$jumin.'\'
						  and svc_cd >= \'1\'
						  and svc_cd <= \'4\'
						  and date_format(from_dt,\'%Y\')<= \''.$year.'\'
						  and date_format(to_dt,\'%Y\')  >= \''.$year.'\') as his
				 inner join (
					   select kind, lvl, gbn, gbn2, svc_kind, addtime1, addtime2, add_pay_gbn, max(from_dt) as from_dt
					     from (
						      select m03_mkind as kind
						      ,      m03_ylvl as lvl
						      ,      m03_vlvl as gbn
						      ,      m03_sgbn as gbn2
						      ,      m03_skind as svc_kind
						      ,      m03_add_time1 as addtime1
						      ,      m03_add_time2 as addtime2
						      ,      m03_add_pay_gbn as add_pay_gbn
						      ,      left(m03_sdate,6) as from_dt
							    from m03sugupja
							   where m03_ccode  = \''.$code.'\'
							     and m03_jumin  = \''.$jumin.'\'
							   union all
						      select m31_mkind as kind
						      ,      m31_level as lvl
						      ,      m31_vlvl as gbn
						      ,      m31_sgbn as gbn2
						      ,      m31_kind as svc_kind
						      ,      m31_add_time1 as addtime1
						      ,      m31_add_time2 as addtime2
						      ,      m31_add_pay_gbn as add_pay_gbn
						      ,      left(m31_sdate,6) as from_dt
							    from m31sugupja
							   where m31_ccode  = \''.$code.'\'
							     and m31_jumin  = \''.$jumin.'\'
						      ) as t
					    group by kind) as mst
					on mst.kind     = his.kind
				   and mst.from_dt >= his.from_dt
				   and mst.from_dt <= his.to_dt
				 order by from_dt desc';
	}else{
		$sql = "select kind, from_dt, to_dt, lvl, gbn, gbn2, svc_kind, $month_time as overtime, $month_pay as overpay, addtime1, addtime2, add_pay_gbn
				  from (
					   select m03_mkind as kind
					   ,      m03_sdate as from_dt
					   ,      m03_edate as to_dt
					   ,      m03_ylvl as lvl
					   ,      m03_vlvl as gbn
					   ,      m03_sgbn as gbn2
					   ,      m03_skind as svc_kind
					   ,      m03_add_time1 as addtime1
					   ,      m03_add_time2 as addtime2
					   ,      m03_add_pay_gbn as add_pay_gbn
						 from m03sugupja
						where m03_ccode  = '$code'
						  and m03_jumin  = '$jumin'
						  and m03_del_yn = 'N'
						union all
					   select m31_mkind as kind
					   ,      m31_sdate as from_dt
					   ,      m31_edate as to_dt
					   ,      m31_level as lvl
					   ,      m31_vlvl as gbn
					   ,      m31_sgbn as gbn2
					   ,      m31_kind as svc_kind
					   ,      m31_add_time1 as addtime1
					   ,      m31_add_time2 as addtime2
					   ,      m31_add_pay_gbn as add_pay_gbn
						 from m31sugupja
						where m31_ccode  = '$code'
						  and m31_jumin  = '$jumin'
						  and (select m03_del_yn from m03sugupja where m03_ccode = m31_ccode and m03_mkind = m31_mkind and m03_jumin = m31_jumin) = 'N'
					   ) as t
				 where kind              > '0'
				   and kind              < '5'
				   and left(from_dt, 6) <= '$year$month'
				   and left(to_dt, 6)   >= '$year$month'
				 order by from_dt desc";
	}

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();
	$kind_name = '';

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$kind_id = $conn->kind_code($kind_list, $row['kind'], 'id');

		$kind_name .= '<input name=\'use_kind\' type=\'radio\' class=\'radio\' value=\''.$kind_id.'\' onclick=\'document.getElementById("onload").value=2; document.getElementById("kind").value="'.$row['kind'].'"; _set_service_use("'.$kind_id.'");\' '.($svc_id == $kind_id ? 'checked' : '').'>';
		$kind_name .= $conn->kind_name($kind_list, $row['kind']);

		if ($row['kind'] == $svc_cd){
			$client = $row;
			$cltGbn = $row['gbn'];
		}
	}

	if (!empty($voucher_make) /* && $cnt > 0*/){
		$client['lvl']         = $voucher_make['lvl'];
		$client['gbn']         = $voucher_make['gbn'];
		$client['gbn2']        = $voucher_make['gbn2'];
		$client['svc_kind']    = $voucher_make['svc_kind'];
		#$client['overtime']    = $voucher_make['overtime'];
		#$client['overpay']     = $voucher_make['overpay'];
		$client['addtime1']    = $voucher_make['addtime1'];
		$client['addtime2']    = $voucher_make['addtime2'];
		$client['add_pay_gbn'] = $voucher_make['add_pay_gbn'];

		if (empty($client['overtime'])) $client['overtime'] = $month_time;
		if (empty($client['overpay']))  $client['overpay']  = $month_pay;


		if ($svc_id == 24){
			$strNote = (!empty($strLastModDT) ? '최종수정일 : '.$strLastModDT.' /' : '').' 시간 : '.$voucher_make['overtime'].' / 금액 : '.number_format($voucher_make['overpay']);
		}
	}

	$client['overtime'] = $month_time;
	$client['overpay']  = $month_pay;

	$conn->row_free();

	##########################################################
	#
	# 이용서비스명 및 생성월
	#
	echo '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:'.__GAB__.'\'>
			<colgroup>
				<col width=\'70px\'>
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th>이용서비스</th>
					<td class=\'left\'>'.$kind_name.'</td>
				</tr>
				<tr>
					<th>생성월</th>
					<td class=\'left\'>';

					if ($lbTestMode){
						$sql = 'select date_format(from_dt,\'%Y%m\') as from_dt
								,      date_format(to_dt,\'%Y%m\') as to_dt
								  from client_his_svc
								 where org_no           = \''.$code.'\'
								   and jumin            = \''.$jumin.'\'
								   and svc_cd           = \''.$svc_cd.'\'
								   and left(from_dt,4) <= \''.$year.'\'
								   and left(to_dt,4)   >= \''.$year.'\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCount = $conn->row_count();

						$period = array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0);

						for($i=0; $i<$rowCount; $i++){
							$row = $conn->select_row($i);

							for($j=1; $j<=12; $j++){
								$mon = ($j < 10 ? '0' : '').$j;

								if ($row['from_dt'] <= $year.$mon && $row['to_dt'] >= $year.$mon)
									$period[$j] = 1;
							}
						}

						$conn->row_free();

						for($j=1; $j<=12; $j++){
							$class = 'my_month ';
							$mon   = ($j<10?'0':'').$j;
							$text  = $j.'월';

							if ($j == 12){
								$style = 'float:left;';
							}else{
								$style = 'float:left; margin-right:2px;';
							}

							if ($period[$j] > 0){
								$link  = 'document.getElementById(\'month\').value = \''.$j.'\'; _set_service_use(\''.$svc_id.'\');';
								$color = 'color:#000000; cursor:pointer;';

								if ($mon == $month){
									$class .= 'my_month_y';
								}else{
									$class .= 'my_month_1';
								}
							}else{
								$link   = '';
								$color  = 'color:#cccccc; cursor:default;';
								$class .= 'my_month_1';
							}

							$style .= $color;?>
							<div class="<?=$class;?>" style="<?=$style;?>" onclick="<?=$link;?>"><?=$text;?></div><?
						}
					}else{
						$from_dt = $client['from_dt'];

						if (intval(substr($from_dt, 6, 2)) > 1){
							$tmp_dt1  = intval(substr($from_dt,0,4));
							$tmp_dt2  = intval(substr($from_dt,4,2));
							$tmp_dt2 += 1;
							$tmp_dt2  = ($tmp_dt2 < 10 ? '0' : '').$tmp_dt2;
							$from_dt  = $tmp_dt1.$tmp_dt2;
						}else{
							$from_dt = substr($client[1],0,6);
						}

						$to_dt = substr($client['to_dt'],0,6);

						for($i=1; $i<=12; $i++){
							$class = 'my_month ';
							$curI  = ($i<10?'0':'').$i;
							$link  = '';

							//계약월부터 현재 월까지만 가능하다.
							if ($year.$curI >= $from_dt){
								if ($month == $curI){
									$class .= 'my_month_y ';
									$color  = 'color:#000000;';
								}else{
									$class .= 'my_month_1 ';
									$color  = 'color:#666666;';
								}

								$link  = '<a href=\'#\' onclick=\'';
								$link .= 'document.getElementById("month").value = "'.$i.'";';
								$link .= '_set_service_use("'.$svc_id.'");';
								$link .= ' return false;\'>'.$i.'월</a>';
							}else{
								$class .= 'my_month_1 ';
								$link   = '<span style=\'color:#cccccc; cursor:default;\'>'.$i.'월</span>';
							}

							if ($i == 12){
								$style = 'float:left;';
							}else{
								$style = 'float:left; margin-right:2px;';
							}

							echo '<div class=\''.$class.'\' style=\''.$style.'\'>'.$link.'</div>';
						}
					}

			echo '	</td>
				</tr>
			</tbody>
		  </table>';

	##########################################################
	#
	# 이용서비스명 및 생성월
	#
	echo '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:'.__GAB__.'\'>
			<colgroup>
				<col width=\'70px\'>
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class=\'head bold\' colspan=\'2\'>서비스 내역</th>
				</tr>
			</thead>
			<tbody>';

			$over_time = $client['overtime'];
			$over_pay  = $client['overpay'];

			$make_time = 0;
			$add_time1 = 0;
			$add_time2 = 0;
			$add_times = 0;

			switch($svc_id){
				case 21: //가사간병
					echo '<tr>
							<th>서비스시간</th>
							<td>';
					$sql = "select person_code
							,      person_id
							,      person_conf_time
							,      person_amt1
							,      person_amt2
							  from suga_person
							 where org_no = '$code'
							   and person_code like 'VH0%'
							   and left(person_from_dt, 7) <= '$year-$month'
							   and left(person_to_dt, 7)   >= '$year-$month'";

					$conn->query($sql);
					$conn->fetch();

					$row_count = $conn->row_count();

					for($ii=0; $ii<$row_count; $ii++){
						$row = $conn->select_row($ii);

						if ($row['person_id'] == $client['lvl']) $make_time = $row['person_conf_time'];

						echo '<input name=\'lvl\' type=\'radio\' class=\'radio\' value=\''.$row['person_id'].'\' time=\''.$row['person_conf_time'].'\' onclick=\'_voucher_time("'.$svc_id.'",__object_get_att("lvl","time"));\' '.($row['person_id'] == $client['lvl'] ? 'checked' : '').'>'.$row['person_conf_time'].'시간';
					}

					$conn->row_free();

					echo '	</td>
						  </tr>';

					echo '<input name=\'gbn\'       type=\'hidden\' value=\'0\'>';
					echo '<input name=\'svc_lvl\'   type=\'hidden\' value=\'1\'>';
					echo '<input name=\'gbn2\'      type=\'hidden\' value=\'\'>';
					echo '<input name=\'overTime\'  type=\'hidden\' value=\'0\'>';
					echo '<input name=\'addPayGbn\' type=\'hidden\' value=\'\'>';

					break;
				case 22: //노인돌봄
					echo '<tr>
							<th>서비스구분</th>
							<td>';

							if ($make_voucher_yn){
								echo '<input name=\'gbn\' type=\'radio\' class=\'radio\' value=\'V\' onclick=\'_sel_svc_gbn("V","'.$svc_id.'",__object_get_att("lvlV","time"));\' '.($client['gbn'] == 'V' ? 'checked' : '').'>방문
									  <input name=\'gbn\' type=\'radio\' class=\'radio\' value=\'D\' onclick=\'_sel_svc_gbn("D","'.$svc_id.'",__object_get_att("lvlD","time"));\' '.($client['gbn'] == 'D' ? 'checked' : '').'>주간보호';
							}else{
								switch($tmp_mon['gbn']){
									case 'V':
										$tmp_str = '방문';
										break;
									default:
										$tmp_str = '주간보호';
								}
								echo '<div class=\'left\'>'.$tmp_str.'</div>';
								echo '<input name=\'gbn\' type=\'hidden\' value=\''.$tmp_mon['gbn'].'\'>';
							}

					echo '	</td>
						  </tr>
						  <tr>
							<th>서비스시간</th>
							<td>';

					if ($make_voucher_yn){
						$sql = "select left(person_code, 3), person_conf_time, person_id
								  from suga_person
								 where org_no         = '$code'
								   and person_code like 'VO%'
								   and left(person_from_dt, 7) <= '$year-$month'
								   and left(person_to_dt, 7)   >= '$year-$month'";

						$conn->query($sql);
						$conn->fetch();

						$row_count = $conn->row_count();

						$str['D'] = '';
						$str['V'] = '';

						for($i=0; $i<$row_count; $i++){
							$row = $conn->select_row($i);

							if ('VO'.$client['gbn'] == $row[0] && $client['lvl'] == $row['person_id']) $make_time = $row['person_conf_time']; // * ($row[0] == 'VOD' ? 3 : 1);

							if ($row[0] == 'VOD'){
								$str['D'] .= '<input name=\'lvlD\' type=\'radio\' class=\'radio\' value=\''.$row['person_id'].'\' time=\''.$row['person_conf_time'].'\' onclick=\'_voucher_time("'.$svc_id.'",__object_get_att("lvlD","time"));\' '.($client['lvl'] == $row['person_id'] ? 'checked' : '').'>'.$row['person_conf_time'].'일 ';
							}else{
								$str['V'] .= '<input name=\'lvlV\' type=\'radio\' class=\'radio\' value=\''.$row['person_id'].'\' time=\''.$row['person_conf_time'].'\' onclick=\'_voucher_time("'.$svc_id.'",__object_get_att("lvlV","time"));\' '.($client['lvl'] == $row['person_id'] ? 'checked' : '').'>'.$row['person_conf_time'].'시간 ';
							}
						}

						$conn->row_free();

						echo '	<div id=\'gbnV\' style=\'display:'.($client['gbn'] == 'V' ? '' : 'none').';\'>'.$str['V'].'</div>';
						echo '	<div id=\'gbnD\' style=\'display:'.($client['gbn'] == 'D' ? '' : 'none').';\'>'.$str['D'].'</div>';
					}else{
						$sql = "select person_conf_time
								  from suga_person
								 where org_no                   = '$code'
								   and person_code           like 'VO".$tmp_mon['gbn']."%'
								   and person_id                = '".$tmp_mon['lvl']."'
								   and left(person_from_dt, 7) <= '$year-$month'
								   and left(person_to_dt, 7)   >= '$year-$month'";

						$tmp_str = $conn->get_data($sql);

						echo '<div class=\'left\'>'.$tmp_str.($tmp_mon['gbn'] == 'V' ? '시간' : '일').'</div>';
						echo '<input name=\'lvl'.$tmp_mon['gbn'].'\' type=\'hidden\' value=\''.$tmp_mon['lvl'].'\'>';
					}

					echo '	</td>
						  </tr>';

					echo '<tr>
							<th>이월시간</th>
							<td><input name=\'overTime\' type=\'text\' value=\''.$over_time.'\' class=\'number\' style=\'width:60px;\' onkeyup=\'_voucher_time("'.$svc_id.'",__object_get_att("'.($row[0] == 'VOD' ? 'lvlD' : 'lvlV').'","time"));\' '.(!$entOverYN ? 'readonly' : '').'> 시간(일)</td>
						  </tr>';

					echo '<input name=\'svc_lvl\' type=\'hidden\' value=\'1\'>';
					echo '<input name=\'gbn2\'    type=\'hidden\' value=\'\'>';
					echo '<input name=\'addPayGbn\' type=\'hidden\' value=\'\'>';

					break;
				case 23: //산모신생아
					switch($client['gbn']){
						case '1':
							$make_time = 12;
							break;
						case '2':
							$make_time = 18;
							break;
						case '3':
							$make_time = 24;
							break;
					}
					echo '<tr>
							<th>서비스구분</th>
							<td>
								<input name=\'gbn\' type=\'radio\' class=\'radio\' value=\'1\' time=\'12\' onclick=\'_voucher_time("'.$svc_id.'",__object_get_att("gbn","time"));\' '.($client['gbn'] == '1' ? 'checked' : '').'>단태아(12일)
								<input name=\'gbn\' type=\'radio\' class=\'radio\' value=\'2\' time=\'18\' onclick=\'_voucher_time("'.$svc_id.'",__object_get_att("gbn","time"));\' '.($client['gbn'] == '2' ? 'checked' : '').'>쌍태아(18일)
								<input name=\'gbn\' type=\'radio\' class=\'radio\' value=\'3\' time=\'24\' onclick=\'_voucher_time("'.$svc_id.'",__object_get_att("gbn","time"));\' '.($client['gbn'] == '3' ? 'checked' : '').'>삼태아(24일)
							</td>
						  </tr>';

					echo '<input name=\'svc_lvl\'  type=\'hidden\' value=\'1\'>';
					echo '<input name=\'gbn2\'     type=\'hidden\' value=\'\'>';
					echo '<input name=\'overTime\' type=\'hidden\' value=\'0\'>';
					echo '<input name=\'addPayGbn\' type=\'hidden\' value=\'\'>';

					break;
				case 24: //장애활동보조
					$add_time1 = $client['addtime1'];
					$add_time2 = $client['addtime2'];

					$sql = 'select service_code
							,      service_conf_time
							,      service_conf_amt
							  from suga_service
							 where org_no                    = \'goodeos\'
							   and left(service_code, 2)     = \'VA\'
							   and left(service_from_dt, 7) <= \''.$year.'-'.$month.'\'
							   and left(service_to_dt, 7)   >= \''.$year.'-'.$month.'\'';

					$conn->query($sql);
					$conn->fetch();

					$row_count = $conn->row_count();

					for($i=0; $i<$row_count; $i++){
						$row = $conn->select_row($i);

						if ($row['service_code'] == 'VA'.$cltGbn.$client['lvl'].'0'){
							$make_time = $row['service_conf_time']; #생성시간
							$make_pay  = $row['service_conf_amt'];  #생성금액

							#수가코드
							$suga = $myF->voucher_suga($svc_id, $cltGbn, $client['lvl'], '0');

							#본인부담금
							$stnd_self = $_voucher->getStndSelfPay($code, $svc_cd, $suga, $client['svc_kind'], $year.'-'.$month);

							#단가
							$suga_cost = $_voucher->getBasicCost($code, $suga, $year.'-'.$month);
						}

						echo '<input name=\''.$row['service_code'].'\' type=\'hidden\' value=\''.$row['service_conf_time'].'\'>';
					}

					$conn->row_free();

					if ($client['lvl'] == '1' && $client['gbn'] != 'C'){
						$gbn2_disabled = '';
					}else{
						$gbn2_disabled = 'disabled=true';
					}

					if (!$make_voucher_yn){
						$make_time = 0;
						$make_pay  = 0;
						$stnd_self = 0;
						$add_time1 = 0;
						$add_time2 = 0;
						$add_times = 0;
					}

					#if ($debug){
						echo '<tr>
								<th>기본급여</th>
								<td>';

								if ($make_voucher_yn){
									if ($client['gbn'] == 'A')
										echo '<input id=\'gbnA\' name=\'gbn\' type=\'radio\' class=\'radio\' value=\'A\' onclick=\'_setStndPay(this,"'.$svc_id.'");\' checked><label for=\'gbnA\'>성인(18세이상)</label>';
									else
										echo '<input id=\'gbnC\' name=\'gbn\' type=\'radio\' class=\'radio\' value=\'C\' onclick=\'_setStndPay(this,"'.$svc_id.'");\' checked><label for=\'gbnC\'>아동(6세~18세미만)</label>';

									echo '<input id=\'gbnX\' name=\'gbn\' type=\'radio\' class=\'radio\' value=\'X'.$cltGbn.'\' onclick=\'_setStndPay(this,"'.$svc_id.'");\' '.($client['gbn'] == 'X' ? 'checked' : '').'><label for=\'gbnX\'>해당없음</label>';

									/**************************************************
										2011.11.01부터 "65세도래자"는 삭제한다.
										echo '<input name=\'gbn\' type=\'radio\' class=\'radio\' value=\'O\' onclick=\'_voucher_time_dis("'.$svc_id.'");\' '.($client['gbn'] == 'O' ? 'checked' : '').'>65세도래자';
									**************************************************/
								}else{
									switch($tmp_mon['gbn']){
										case 'A':
											$tmp_str = '성인(18세이상)';
											break;
										case 'C':
											$tmp_str = '아동(6세~18세미만)';
											break;
										default:
											//$tmp_str = '65세도래자';
											$tmp_str = '해당없음';
									}
									echo '<div class=\'left\'>'.$tmp_str.'</div>';
									echo '<input name=\'gbn\' type=\'hidden\' value=\''.$tmp_mon['gbn'].'\'>';
								}

						echo '	</td>
							  </tr>';
					#}else{
					#	echo '<tr>
					#			<th>나이등급</th>
					#			<td>';

					#			if ($make_voucher_yn){
					#				if ($client['gbn'] == 'A')
					#					echo '<input name=\'gbn\' type=\'radio\' class=\'radio\' value=\'A\' onclick=\'_voucher_time_dis("'.$svc_id.'");\' checked>성인(18세이상)';
					#				else
					#					echo '<input name=\'gbn\' type=\'radio\' class=\'radio\' value=\'C\' onclick=\'_voucher_time_dis("'.$svc_id.'");\' checked>아동(6세~18세미만)';

									/**************************************************
										2011.11.01부터 "65세도래자"는 삭제한다.
										echo '<input name=\'gbn\' type=\'radio\' class=\'radio\' value=\'O\' onclick=\'_voucher_time_dis("'.$svc_id.'");\' '.($client['gbn'] == 'O' ? 'checked' : '').'>65세도래자';
									**************************************************/
					#			}else{
					#				switch($tmp_mon['gbn']){
					#					case 'A':
					#						$tmp_str = '성인(18세이상)';
					#						break;
					#					case 'C':
					#						$tmp_str = '아동(6세~18세미만)';
					#						break;
					#					default:
					#						//$tmp_str = '65세도래자';
					#						$tmp_str = '-';
					#				}
					#				echo '<div class=\'left\'>'.$tmp_str.'</div>';
					#				echo '<input name=\'gbn\' type=\'hidden\' value=\''.$tmp_mon['gbn'].'\'>';
					#			}

					#	echo '	</td>
					#		  </tr>';
					#}

					echo '<tr>
							<th class=\'bottom\'>추가급여</th>
							<td class=\'bottom\'>';

							/**************************************************
								2011.11.01부터 "특례구분"을 "추가급여"로 변경된다.
								if ($make_voucher_yn){
									echo '	<input name=\'gbn2\' type=\'radio\' class=\'radio\' value=\'0\' onclick=\'_voucher_time_dis("'.$svc_id.'");\' '.($client['gbn2'] == '0' ? 'checked' : '').'>없음
											<input name=\'gbn2\' type=\'radio\' class=\'radio\' value=\'8\' onclick=\'_voucher_time_dis("'.$svc_id.'");\' '.($client['gbn2'] == '8' ? 'checked' : '').' '.$gbn2_disabled.'>특례180
											<input name=\'gbn2\' type=\'radio\' class=\'radio\' value=\'2\' onclick=\'_voucher_time_dis("'.$svc_id.'");\' '.($client['gbn2'] == '2' ? 'checked' : '').' '.$gbn2_disabled.'>특례120';
								}else{
									switch($tmp_mon['gbn2']){
										case '8':
											$tmp_str = '특례180';
											break;
										case '2':
											$tmp_str = '특례120';
											break;
										default:
											$tmp_str = '없음';
									}
									echo '<div class=\'left\'>'.$tmp_str.'</div>';
									echo '<input name=\'gbn2\' type=\'hidden\' value=\''.$tmp_mon['gbn2'].'\'>';
								}
							**************************************************/


							if ($make_voucher_yn){
								/*
								$sql = 'select svc_gbn_cd as cd
										,      svc_gbn_nm as nm
										,      svc_time as tm
										,      svc_pay as pay
										  from suga_service_add
										 where svc_kind              = \'4\'
										   and svc_gbn_cd            = \''.$client['gbn2'].'\'
										   and left(svc_from_dt, 7) <= \''.$year.'-'.$month.'\'
										   and left(svc_to_dt, 7)   >= \''.$year.'-'.$month.'\'';

								$tmp = $conn->get_array($sql);

								if (empty($tmp['nm'])) $tmp['nm'] = '해당없음';

								echo '<input name=\'gbn2\' type=\'radio\' class=\'radio\' value=\''.$tmp['cd'].'\' checked>'.$tmp['nm'];
								echo '<input name=\'addTimes'.$tmp['cd'].'\' type=\'hidden\' value=\''.$tmp['tm'].'\'>
									  <input name=\'addPays'.$tmp['cd'].'\' type=\'hidden\' value=\''.$tmp['pay'].'\'>';

								#추가시간
								$add_time = $tmp['tm'];

								#추가금액
								$add_pay  = $tmp['pay'];

								#추가본인부담금액
								$add_self = $_voucher->getAddSelfPay($code, $svc_cd, $client['gbn2'], $client['svc_kind'], $year.'-'.$month);

								unset($tmp);
								*/



								$sql = 'select svc_gbn_cd as cd
										,      svc_gbn_nm as nm
										,      svc_time as time
										,      svc_pay as pay
										  from suga_service_add
										 where svc_kind              = \'4\'
										   and svc_group             = \'R\'
										   and left(svc_from_dt, 7) <= \''.$year.'-'.$month.'\'
										   and left(svc_to_dt, 7)   >= \''.$year.'-'.$month.'\'';

								$conn->query($sql);
								$conn->fetch();

								$rowCount = $conn->row_count();

								echo '<div style=\'height:26px; border-bottom:1px solid #cccccc;\'>
										<input id=\'gbn2_0\' name=\'gbn2\' type=\'radio\' class=\'radio\' value=\'\' onclick=\'_makeVoucherUseIfno();\' checked><label for=\'gbn2_0\'>해당없음</label>
										<input id=\'addTimes_0\' name=\'addTimes_0\' type=\'hidden\' value=\'0\'>
										<input id=\'addPays_0\' name=\'addPays_0\' type=\'hidden\' value=\'0\'>';

								for($i=0; $i<$rowCount; $i++){
									$row = $conn->select_row($i);
									echo '<input id=\'gbn2_'.$row['cd'].'\' name=\'gbn2\' type=\'radio\' class=\'radio\' value=\''.$row['cd'].'\' onclick=\'_makeVoucherUseIfno();\' '.($client['gbn2'] == $row['cd'] ? 'checked' : '').'><label for=\'gbn2_'.$row['cd'].'\'>'.$row['nm'].'</label>
										  <input id=\'addTimes_'.$row['cd'].'\' name=\'addTimes_'.$row['cd'].'\' type=\'hidden\' value=\''.$row['time'].'\'>
										  <input id=\'addPays_'.$row['cd'].'\' name=\'addPays_'.$row['cd'].'\' type=\'hidden\' value=\''.$row['pay'].'\'>';
								}

								$conn->row_free();

								echo '</div>
									  <div>';

								$sql = 'select svc_gbn_cd as cd
										,      svc_gbn_nm as nm
										,      svc_time as time
										,      svc_pay as pay
										  from suga_service_add
										 where svc_kind              = \'4\'
										   and svc_group             = \'C\'
										   and left(svc_from_dt, 7) <= \''.$year.'-'.$month.'\'
										   and left(svc_to_dt, 7)   >= \''.$year.'-'.$month.'\'';

								$conn->query($sql);
								$conn->fetch();

								$rowCount = $conn->row_count();

								for($i=0; $i<$rowCount; $i++){
									$row = $conn->select_row($i);
									echo '<div style=\'float:left; width:45%;\'>
											<input id=\'addPayGbn_'.$row['cd'].'\' name=\'addPayGbn[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$row['cd'].'\' onclick=\'_makeVoucherUseIfno();\' '.(is_numeric(strpos($client['add_pay_gbn'], '/'.$row['cd'])) ? 'checked' : '').'><label for=\'addPayGbn_'.$row['cd'].'\'>'.$row['nm'].'</label>
											<input id=\'addTimes_'.$row['cd'].'\' name=\'addTimes_'.$row['cd'].'\' type=\'hidden\' value=\''.$row['time'].'\'>
											<input id=\'addPays_'.$row['cd'].'\' name=\'addPays_'.$row['cd'].'\' type=\'hidden\' value=\''.$row['pay'].'\'>
										  </div>';
								}

								echo '</div>';

								$conn->row_free();

							}else{
								$sql = 'select svc_gbn_nm
										  from suga_service_add
										 where svc_kind              = \'4\'
										   and svc_gbn_cd            = \''.$tmp_mon['gbn2'].'\'
										   and left(svc_from_dt, 7) <= \''.$year.'-'.$month.'\'
										   and left(svc_to_dt, 7)   >= \''.$year.'-'.$month.'\'';

								$tmp_str = $conn->get_data($sql);

								if (empty($tmp_str)) $tmp_str = '해당없음';

								echo '<div class=\'left\'>'.$tmp_str.'</div>';
								echo '<input name=\'gbn2\' type=\'hidden\' value=\''.$tmp_mon['gbn2'].'\'>';
							}
					echo '	</td>
						  </tr>';

					echo '<tr>
							<td colspan=\'2\'>
								<table class=\'my_table\' style=\'width:100%; border-top:2px solid #0e69b0;\'>
									<colgroup>
										<col width=\'75px\'>
										<col width=\'95px\' span=\'4\'>
										<col>
									</colgroup>
									<thead>
										<tr>
											<th class=\'head\'>급여구분</th>
											<th class=\'head\'>합계</th>
											<th class=\'head\'>시간</th>
											<th class=\'head\'>지원금액</th>
											<th class=\'head\'>본인부담금</th>
											<th class=\'head\'>비고</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<th class=\'center\'>기본급여</th>
											<td class=\'center\'><input id=\'payStndTot\'  name=\'pay_stnd_tot\'  type=\'text\' value=\''.($client['gbn'] != 'X' ? number_format($make_pay) : 0).'\' value1=\''.number_format($make_pay).'\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center\'><input id=\'payStndTime\' name=\'pay_stnd_time\' type=\'text\' value=\''.($client['gbn'] != 'X' ? number_format($make_time) : 0).'\' value1=\''.number_format($make_time).'\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center\'><input id=\'payStndUse\'  name=\'pay_stnd_use\'  type=\'text\' value=\''.($client['gbn'] != 'X' ? number_format($make_pay - $stnd_self) : 0).'\' value1=\''.number_format($make_pay - $stnd_self).'\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center\'><input id=\'payStndSelf\' name=\'pay_stnd_self\' type=\'text\' value=\''.($client['gbn'] != 'X' ? number_format($stnd_self) : 0).'\' value1=\''.number_format($stnd_self).'\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'left\'>&nbsp;</td>
										</tr>
										<tr>
											<th class=\'center\'>추가급여</th>
											<td class=\'center\'><input id=\'payAddTot\'  name=\'pay_add_tot\'  type=\'text\' value=\''.number_format($add_pay).'\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center\'><input id=\'payAddTime\' name=\'pay_add_time\' type=\'text\' value=\''.number_format($add_time).'\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center\'><input id=\'payAddUse\'  name=\'pay_add_use\'  type=\'text\' value=\''.number_format($add_pay - $add_self).'\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center\'><input id=\'payAddSelf\' name=\'pay_add_self\' type=\'text\' value=\''.number_format($add_self).'\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'left\'>&nbsp;</td>
										</tr>
										<tr>
											<th class=\'center\'>시도비추가</th>
											<td class=\'center\'><input id=\'paySidoTot\'  name=\'pay_sido_tot\'  type=\'text\' value=\''.number_format($add_time1 * $suga_cost).'\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center\'><input id=\'paySidoTime\' name=\'pay_sido_time\' type=\'text\' value=\''.number_format($add_time1).'\' class=\'number readonly\' style=\'width:100%; background-color:#f6f4d3;\' onchange=\'_voucher_time_dis("'.$svc_id.'");\'></td>
											<td class=\'center\'><input id=\'paySidoUse\'  name=\'pay_sido_use\'  type=\'text\' value=\''.number_format($add_time1 * $suga_cost).'\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center\'><input id=\'paySidoSelf\' name=\'pay_sido_self\' type=\'text\' value=\'0\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'left\'>&nbsp;</td>
										</tr>
										<tr>
											<th class=\'center\'>자치비추가</th>
											<td class=\'center\'><input id=\'payJachTot\'  name=\'pay_jach_tot\'  type=\'text\' value=\''.number_format($add_time2 * $suga_cost).'\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center\'><input id=\'payJachTime\' name=\'pay_jach_time\' type=\'text\' value=\''.number_format($add_time2).'\' class=\'number readonly\' style=\'width:100%; background-color:#f6f4d3;\' onchange=\'_voucher_time_dis("'.$svc_id.'");\'></td>
											<td class=\'center\'><input id=\'payJachUse\'  name=\'pay_jach_use\'  type=\'text\' value=\''.number_format($add_time2 * $suga_cost).'\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center\'><input id=\'payJachSelf\' name=\'pay_jach_self\' type=\'text\' value=\'0\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'left\'>&nbsp;</td>
										</tr>
										<tr>
											<th class=\'center\'>이월급여</th>
											<td class=\'center\'><input id=\'payOverTot\'  name=\'pay_over_tot\'  type=\'text\' value=\''.number_format($over_pay).'\'  class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center\'><input id=\'payOverTime\' name=\'pay_over_time\' type=\'text\' value=\''.$over_time.'\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center\'><input id=\'payOverUse\'  name=\'pay_over_use\'  type=\'text\' value=\''.number_format($over_pay).'\'  class=\'number readonly\' style=\'width:100%; background-color:#f6f4d3;\' onchange=\'_voucher_time_dis("'.$svc_id.'");\' '.(!$entOverYN ? 'readonly' : '').'></td>
											<td class=\'center\'><input id=\'payOverSelf\' name=\'pay_over_self\' type=\'text\' value=\'0\' class=\'number readonly\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'left\'>'.$strNote.'</td>
										</tr>
										<tr>
											<th class=\'center bottom\'>총이용합계</th>
											<td class=\'center bottom\'><input id=\'payTotalTot\'  name=\'pay_total_tot\'  type=\'text\' value=\''.number_format($make_pay + $add_pay + $add_time1 * $suga_cost + $add_time2 * $suga_cost + $month_pay).'\' class=\'number readonly bold\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center bottom\'><input id=\'payTotalTime\' name=\'pay_total_time\' type=\'text\' value=\''.($make_time + $add_time + $add_time1 + $add_time2 + $over_time).'\' class=\'number readonly bold\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center bottom\'><input id=\'payTotalUse\'  name=\'pay_total_use\'  type=\'text\' value=\''.number_format(($make_pay - $stnd_self) + ($add_pay - $add_self) + ($add_time1 * $suga_cost) + ($add_time2 * $suga_cost) + $over_pay).'\' class=\'number readonly bold\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'center bottom\'><input id=\'payTotalSelf\' name=\'pay_total_self\' type=\'text\' value=\''.number_format($stnd_self + $add_self).'\' class=\'number readonly bold\' style=\'width:100%;\' alt=\'not\' readonly></td>
											<td class=\'left bottom\'>&nbsp;</td>
										</tr>
									</tbody>
								</table>
							</td>
						  </tr>';

					echo '<input id=\'lvl\'       name=\'lvl\' type=\'hidden\' value=\''.$client['lvl'].'\'>
						  <input id=\'svc_lvl\'   name=\'svc_lvl\'  type=\'hidden\' value=\''.$client['lvl'].'\'>
						  <input id=\'svc_kind\'  name=\'svc_kind\' type=\'hidden\' value=\''.$client['svc_kind'].'\'>
						  <input id=\'suga_cost\' name=\'suga_cost\' type=\'hidden\' value=\''.$suga_cost.'\'>
						  <input id=\'overTime\'  name=\'overTime\' type=\'hidden\' value=\''.$over_time.'\'>
						  <input id=\'overPay\'   name=\'overPay\' type=\'hidden\' value=\''.$over_pay.'\'>';

					break;
			}
			$total_time = $over_time + $make_time + $add_time1 + $add_time2 + $add_times + $add_time;

	echo '	</tbody>
		  </table>';

	##########################################################
	#
	# 바우처 구매
	#
	echo '<div style=\'width:275px; margin-top:'.__GAB__.'; float:left;\'>
		  <table class=\'my_table my_border_blue\'>
			<colgroup>
				<col width=\'70px\'>
				<col width=\'70px\'>
				<col width=\'50px\'>
				<col width=\'60px\'>
			</colgroup>
			<tbody>
				<tr>
					<th rowspan=\'3\'>바우처구매</th>
					<th>이월시간</th>
					<td class=\'right last\' id=\'strOverTime\'>'.number_format($over_time, 1).'</td>
					<td class=\'left\'>시간(일)</td>
				</tr>
				<tr>
					<th>생성시간</th>
					<td class=\'right last\' id=\'strMakeTime\'>'.number_format($make_time + $add_time1 + $add_time2 + $add_times + $add_time, 1).'</td>
					<td class=\'left\'>시간(일)</td>
				</tr>
				<tr>
					<th>총구매시간</th>
					<td class=\'right last\' id=\'strTotalTime\'>'.number_format($total_time, 1).'</td>
					<td class=\'left\'>시간(일)</td>
				</tr>
			</tbody>
		  </table>
		  </div>';

	##########################################################
	#
	# 버튼그룹
	#
	echo '<div style=\'margin-top:'.__GAB__.'; text-align:right;\'>
		  <table class=\'my_table my_border_blue\' style=\'width:100%;\'>
			<colgroup>
				<col>
			</colgroup>
			<tbody>
				<tr>
					<td class=\'left bottom bold\'>';

					if ($cnt > 0) echo '* 등록된 일정이 있습니다.<br>';//echo '* 등록된 일정이 있어 바우처 생성내역을 수정할 수 없습니다.<br>';
					if (!$entOverYN) echo '* 매년 2월은 이월급여를 등록할 수 없습니다.<br>';
					if ($edit_yn != 'Y') echo '* 현재 년월이 아니면 수정이 불가능합니다.<br>';

			echo '	</td>
				</tr>
				<tr>
					<td class=\'right\'>';

	if ($cnt == 0 && $seq > 0){
		//일정이 없을 경우 삭제가능하도록 한다.
		echo '<div style=\'float:left; width:auto; margin-left:5px;\'><span class=\'btn_pack m\'><button type=\'button\' style=\'color:#ff0000;\' onclick=\'_voucherClear();\'>삭제</button></span></div>';
	}

	echo '				<div style=\'float:right; width:auto;\'>
							<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_voucher_run("iljung_service_use_ok.php");\'>저장</button></span>
							<span class=\'btn_pack m\'><button type=\'button\' onclick=\'self.close();\'>닫기</button></span>
						</div>
					</td>
				</tr>
			</tbody>
		  </table>
		  </div>';

	##########################################################
	#
	#
	#
	//echo '<input name=\'overTime\'  type=\'hidden\' value=\''.$over_time.'\'>';  //이월시간
	echo '<input id=\'makeTime\'  name=\'makeTime\'  type=\'hidden\' value=\''.$make_time.'\'>';  //생성시간
	echo '<input id=\'totalTime\' name=\'totalTime\' type=\'hidden\' value=\''.$total_time.'\'>'; //총구매시간
	echo '<input id=\'seq\'       name=\'seq\'       type=\'hidden\' value=\''.$seq.'\'>';
	echo '<input id=\'mode\'      name=\'mode\'      type=\'hidden\' value=\''.$mode.'\'>';
	echo '<input id=\'onload\'    name=\'onload\'    type=\'hidden\' value=\''.$onload.'\'>';

	echo '<input id="svcID" name="svcID" type="hidden" value="'.$svc_id.'">
		  <input id="svcCD" name="svcCD" type="hidden" value="'.$svc_cd.'">';

	include_once("../inc/_db_close.php");

	$value = ob_get_contents();
	ob_end_clean();

	echo $value;
?>