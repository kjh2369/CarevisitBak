<?
	############################################################################
	#
	# 사용한 시간정보를 저장한다.
	function f_voucher_usetime($conn, $code, $jumin, $year, $month){
		#바우처사용가능한 시간
		$sql = "select voucher_kind as kind
				,      voucher_seq as seq
				,      voucher_overtime as overtime
				,	   voucher_overpay as overpay
				,      voucher_maketime as maketime
				,      voucher_addtime1 as addtime1
				,      voucher_addtime2 as addtime2
				,      0 as overaddtime
				,      voucher_totaltime as totaltime
				  from voucher_make
				 where org_no        = '".$code."'
				   and voucher_jumin = '".$jumin."'
				   and voucher_yymm  = '".$year.$month."'
				   and del_flag      = 'N'";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$voucher[$row['kind']] = $row;

			#바우처사용시간
			$usetime[$row['kind']] = array('kind'=>$row['kind'], 'seq'=>$row['seq'], 'overtime'=>0, 'maketime'=>0, 'addtime1'=>0, 'addtime2'=>0, 'overaddtime'=>0, 'totaltime'=>0);
		}

		$conn->row_free();

		#사용한시간
		$sql = "select t01_mkind as kind
				,      sum(t01_sugup_proctime) as proctime
				,      sum(t01_suga_tot) as sugapay
				  from t01iljung
				 where t01_ccode         = '".$code."'
				   and t01_jumin         = '".$jumin."'
				   and t01_sugup_date like '".$year.$month."%'
				   and t01_bipay_umu    != 'Y'
				   and t01_del_yn        = 'N'
				 group by t01_mkind";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row  = $conn->select_row($i);
			$time = $row['proctime'];
			$pay  = $row['sugapay'];

			#이월된 시간
			if ($voucher[$row['kind']]['overtime'] > 0){
				if ($voucher[$row['kind']]['overtime'] > $time){
					$usetime[$row['kind']]['overtime'] = $time;
					$time = 0;
				}else{
					$usetime[$row['kind']]['overtime'] = $voucher[$row['kind']]['overtime'];
					$time -= $voucher[$row['kind']]['overtime'];
				}
			}

			#이월된 금액
			if ($voucher[$row['kind']]['overpay'] > 0){
				if ($voucher[$row['kind']]['overpay'] > $pay){
					$usetime[$row['kind']]['overpay'] = $pay;
					$pay = 0;
				}else{
					$usetime[$row['kind']]['overpay'] = $voucher[$row['kind']]['overpay'];
					$pay -= $voucher[$row['kind']]['overpay'];
				}
			}


			#생성시간
			if ($voucher[$row['kind']]['maketime'] > 0){
				if ($voucher[$row['kind']]['maketime'] > $time){
					$usetime[$row['kind']]['maketime'] = $time;
					$time = 0;
				}else{
					$usetime[$row['kind']]['maketime'] = $voucher[$row['kind']]['maketime'];
					$time -= $voucher[$row['kind']]['maketime'];
				}
			}

			#시도비시간
			if ($voucher[$row['kind']]['addtime1'] > 0){
				if ($voucher[$row['kind']]['addtime1'] > $time){
					$usetime[$row['kind']]['addtime1'] = $time;
					$time = 0;
				}else{
					$usetime[$row['kind']]['addtime1'] = $voucher[$row['kind']]['addtime1'];
					$time -= $voucher[$row['kind']]['addtime1'];
				}
			}

			#자치비시간
			if ($voucher[$row['kind']]['addtime2'] > 0){
				if ($voucher[$row['kind']]['addtime2'] > $time){
					$usetime[$row['kind']]['addtime2'] = $time;
					$time = 0;
				}else{
					$usetime[$row['kind']]['addtime2'] = $voucher[$row['kind']]['addtime2'];
					$time -= $voucher[$row['kind']]['addtime2'];
				}
			}

			#추가시간
			$usetime[$row['kind']]['overaddtime'] = $time;
			$usetime[$row['kind']]['totaltime']   = $usetime[$row['kind']]['overtime'] + $usetime[$row['kind']]['maketime'] + $usetime[$row['kind']]['addtime1'] + $usetime[$row['kind']]['addtime2'] + $usetime[$row['kind']]['overaddtime'];
		}

		$conn->row_free();

		#사용시간 저장
		if (is_array($usetime)){
			foreach($usetime as $use){
				if (!empty($use['kind'])){
					$sql = "replace into voucher_usetime values (
							 '".($code)."'
							,'".($use['kind'])."'
							,'".($jumin)."'
							,'".($year.$month)."'
							,'".($voucher[$use['kind']]['overtime']+$voucher[$use['kind']]['maketime'])."'
							,'".($use['overtime'])."'
							,'".($use['maketime'])."'
							,'".($use['addtime1'])."'
							,'".($use['addtime2'])."'
							,'".($use['overaddtime'])."'
							,'".($use['totaltime'])."')";

					$conn->execute($sql);

					if ($use['kind'] == '2' || $use['kind'] == '4'){
						#이월가능시간
						$sql = "select voucher_totaltime - (voucher_overtime + voucher_maketime) as mon_time
								  from voucher_usetime
								 where org_no        = '".($code)."'
								   and voucher_kind  = '".($use['kind'])."'
								   and voucher_jumin = '".($jumin)."'
								   and voucher_yymm  = '".($year.$month)."'";

						$mon_time = $conn->get_data($sql);

						#이월가능시간 저장
						$sql = "update voucher_make
								   set voucher_month_time = '".($mon_time)."'
								 where org_no        = '".($code)."'
								   and voucher_kind  = '".($use['kind'])."'
								   and voucher_jumin = '".($jumin)."'
								   and voucher_yymm  = '".($year.$month)."'
								   and del_flag      = 'N'";

						$conn->execute($sql);
					}
				}
			}
		}

		return true;
	}
	#
	############################################################################
?>