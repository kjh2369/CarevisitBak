<?php
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*****************************************************
	 바우처 생성내역을 자동등록한다.

	 - 년월이전의 최근 내역을 복사하며 기존의 내역이
	   있을 경우 처리하지 않는다.
	*****************************************************/

	echo $myF->header_script();

	$conn->mode = 1;
	$conn->fetch_type = 'assoc';

	$code    = $_SESSION['userCenterCode'];  //기관코드
	$year    = $_GET['year'];  //년도
	$month   = $_GET['month']; //월
	$gbn = $_GET['gbn']; //1:전월, 2:최근
	$svcList = $_POST['chkSvc'];
	$reg_id  = $_SESSION['userCode'];
	$reg_dt  = date('Y-m-d', mktime());
	$beforeYm= $myF->dateAdd('month', -1, $year.$month.'01', 'Ym');


	foreach($svcList as $svcIdx => $svcCD){
		$sql = "select m03_mkind as kind
				,      m03_jumin as jumin
				  from m03sugupja
				 where m03_ccode  = '$code'
				   and m03_mkind  = '$svcCD'
				   and m03_del_yn = 'N'";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$kind  = $row['kind'];
			$jumin = $row['jumin'];

			if ($_POST['chkReVou'] == 'Y'){
				$mst[sizeof($mst)] = array('kind'=>$kind,'jumin'=>$jumin);
			}else{
				$sql = "select count(*)
						  from voucher_make
						 where org_no        = '$code'
						   and voucher_kind  = '$kind'
						   and voucher_jumin = '$jumin'
						   and voucher_yymm  = '$year$month'
						   and del_flag      = 'N'";

				$cnt = $conn->get_data($sql);

				if ($cnt == 0){
					$mst[sizeof($mst)] = array('kind'=>$kind,'jumin'=>$jumin);
				}
			}
		}

		$conn->row_free();

		unset($row);
	}



	/*
		$sql = "select m03_mkind as kind
				,      m03_jumin as jumin
				  from m03sugupja
				 where m03_ccode       = '$code'
				   and m03_mkind between '1' and '4'
				   and m03_del_yn      = 'N'";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$kind  = $row['kind'];
			$jumin = $row['jumin'];

			$sql = "select count(*)
					  from voucher_make
					 where org_no        = '$code'
					   and voucher_kind  = '$kind'
					   and voucher_jumin = '$jumin'
					   and voucher_yymm  = '$year$month'
					   and del_flag      = 'N'";

			$cnt = $conn->get_data($sql);

			if ($cnt == 0) $mst[sizeof($mst)] = array('kind'=>$kind,'jumin'=>$jumin);
		}

		$conn->row_free();

		unset($row);
	*/

	if (is_array($mst)){
		$conn->begin();

		$sl = "";

		foreach($mst as $i => $m){
			$kind  = $m['kind'];
			$jumin = $m['jumin'];

			if ($_POST['chkReVou'] == 'Y'){
				$sql = 'update voucher_make
						   set del_flag      = \'Y\'
						 where org_no        = \''.$code.'\'
						   and voucher_kind  = \''.$kind.'\'
						   and voucher_jumin = \''.$jumin.'\'
						   and voucher_yymm  = \''.$year.$month.'\'';

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 echo $conn->err_back();
					 if ($conn->mode == 1) exit;
				}
			}


			if ($gbn == '1'){
				$sql = "select *
						  from voucher_make
						 where org_no        = '$code'
						   and voucher_kind  = '$kind'
						   and voucher_jumin = '$jumin'
						   and voucher_yymm  = '$beforeYm'
						   and del_flag      = 'N'";
			}else{
				$sql = "select *
						  from voucher_make
						 where org_no        = '$code'
						   and voucher_kind  = '$kind'
						   and voucher_jumin = '$jumin'
						   and voucher_yymm  < '$year$month'
						   and del_flag      = 'N'
						 order by voucher_yymm desc
						 limit 1";
			}

			$v = $conn->get_array($sql);

			$maketime = $v['voucher_maketime'];   //생성시간
			$makepay  = $v['voucher_makepay'];    //생성시간
			$overtime = $v['voucher_month_time']; //이월시간
			$overpay  = $v['voucher_month_pay'];  //이월금액
			$addtime1 = $v['voucher_addtime1'];   //추가시간1
			$addtime2 = $v['voucher_addtime2'];   //추가시간2
			$addtime  = $v['voucher_addtime'];
			$addpay   = $v['voucher_addpay'];

			if ($overtime < 0 || $voerpay < 0){
				$overtime = 0;
				$overpay = 0;
			}

			if ($_POST['chkReVou'] == 'Y'){
				$overtime = 0;
				$overpay  = 0;
			}

			if (empty($maketime)) $maketime = 0;
			if (empty($overtime)) $overtime = 0;
			if (empty($overpay))  $overpay  = 0;
			if (empty($addtime1)) $addtime1 = 0;
			if (empty($addtime2)) $addtime2 = 0;

			/*********************************************************

				2월은 이월을 하지 않는다.

			*********************************************************/
			if (intval($month) == 2){
				$overtime = 0;
				$overpay  = 0;
			}

			if ($maketime < $overtime){
				$maketime = 0;
				$makepay  = 0;
				$addtime1 = 0;
				$addtime2 = 0;
				$addtime  = 0;
				$addpay   = 0;
			}

			//현재 수가
			$sql = 'SELECT service_cost
					  FROM suga_service
					 WHERE org_no       = \'goodeos\'
					   AND service_kind = \''.$svcCD.'\'
					   AND service_code = \''.$v['voucher_suga_cd'].'\'
					   AND DATE_FORMAT(service_from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					   AND DATE_FORMAT(service_to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';

			$liNowSugaCost = $conn->get_data($sql);

			if (Empty($liNowSugaCost)){
				$liNowSugaCost = $v['voucher_suga_cost'];
			}

			$gbn      = $v['voucher_gbn'];
			$gbn2     = $v['voucher_gbn2'];
			$lvl      = $v['voucher_lvl'];
			$svc_kind = $v['voucher_svc_kind'];

			$suga_cd   = $v['voucher_suga_cd'];

			//$suga_cost = $v['voucher_suga_cost'];
			$suga_cost = $liNowSugaCost;

			$add_gbn = $v['voucher_add_pay_gbn'];


			$addpay1 = $addtime1 * $suga_cost;
			$addpay2 = $addtime2 * $suga_cost;

			if ($kind == '2'){
				$chk_year  = intval($year);
				$chk_month = intval($month) - 1;

				if ($chk_month < 1){
					$chk_month = 12;
					$chk_year -= 1;
				}

				$chk_month = ($chk_month < 10 ? '0' : '').$chk_month;

				$sql = 'select voucher_maketime + voucher_addtime + voucher_overtime
						  from voucher_make
						 where org_no        = \''.$code.'\'
						   and voucher_kind  = \''.$kind.'\'
						   and voucher_jumin = \''.$jumin.'\'
						   and voucher_yymm  = \''.$chk_year.$chk_month.'\'
						   and del_flag      = \'N\'';

				#전월 생성금액 및 시간
				$lastMonthMake = intval($conn->get_data($sql));

				if ($lastMonthMake < 0) $lastMonthMake = 0;

				$sql = 'select sum(t01_conf_soyotime)
						  from t01iljung
						 where t01_ccode      = \''.$code.'\'
						   and t01_mkind      = \''.$kind.'\'
						   and t01_jumin      = \''.$jumin.'\'
						   and t01_status_gbn = \'1\'
						   and t01_del_yn     = \'N\'
						   and left(t01_sugup_date, 6) = \''.$chk_year.$chk_month.'\'';

				#전월 사용금액 및 시간
				$lastMonthUse = intval($conn->get_data($sql));
				$lastMonthUse = floor($lastMonthUse / 60);

				@$overtime = $lastMonthMake - $lastMonthUse;
				@$overpay  = 0;

				if ($overtime < 0 || $voerpay < 0){
					$overtime = 0;
					$overpay = 0;
				}

				$monthtime = $maketime + $overtime;
			}else if ($kind == '4'){
				$chk_year  = intval($year);
				$chk_month = intval($month) - 1;

				if ($chk_month < 1){
					$chk_month = 12;
					$chk_year -= 1;
				}

				$chk_month = ($chk_month < 10 ? '0' : '').$chk_month;

				/*********************************************************
					생성 단가
				*********************************************************/
				$sql = 'select voucher_suga_cost
						  from voucher_make
						 where org_no        = \''.$code.'\'
						   and voucher_kind  = \''.$kind.'\'
						   and voucher_jumin = \''.$jumin.'\'
						   and voucher_yymm  = \''.$chk_year.$chk_month.'\'
						   and del_flag      = \'N\'';

				$lastMonthCost = intval($conn->get_data($sql));


				$sql = 'select voucher_makepay + voucher_addpay + voucher_overpay
						  from voucher_make
						 where org_no        = \''.$code.'\'
						   and voucher_kind  = \''.$kind.'\'
						   and voucher_jumin = \''.$jumin.'\'
						   and voucher_yymm  = \''.$chk_year.$chk_month.'\'
						   and del_flag      = \'N\'';

				#전월 생성금액 및 시간
				$lastMonthMake = intval($conn->get_data($sql));


				/*********************************************************
					사용한 금액 및 시간
				*********************************************************/
				$sql = 'select voucher_maketime + (voucher_overpay / voucher_suga_cost) + voucher_addtime as time0, voucher_makepay + voucher_overpay + voucher_addpay as pay0
						,      voucher_addtime1 + voucher_addtime2 as time1, (voucher_addtime1 + voucher_addtime2) * voucher_suga_cost as pay1
						,      voucher_suga_cost as suga_cost
						,      insert_dt
						,      update_dt
						  from voucher_make
						 where org_no        = \''.$code.'\'
						   and voucher_kind  = \''.$kind.'\'
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
							   and t01_mkind      = \''.$kind.'\'
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
				#@$month_pay  = $lastMonthMake - $lastMonthUse;
				@$overpay  = $lastMonthUse;
				@$overtime = round($month_pay / $lastMonthCost,1);

				$monthtime = $overtime;
				$monthpay  = $overpay;
			}else{
				$monthtime = 0;
			}

			$totaltime = $maketime + $overtime + $addtime1 + $addtime2 + $addtime;
			$totalpay  = $makepay  + $overpay  + $addpay1  + $addpay2  + $addpay;

			if ($maketime > 0 || $overtime > 0){
				if (!empty($sl)) $sl .= ",";

				$sql = 'select ifnull(max(voucher_seq),0)+1
						  from voucher_make
						 where org_no        = \''.$code.'\'
						   and voucher_kind  = \''.$kind.'\'
						   and voucher_jumin = \''.$jumin.'\'
						   and voucher_yymm  = \''.$year.$month.'\'';

				$newSeq = $conn->get_data($sql);

				$sl .= "('$code'
						,'$kind'
						,'$jumin'
						,'$year$month'
						,'$newSeq'
						,'$gbn'
						,'$gbn2'
						,'$lvl'
						,'$svc_kind'
						,'$add_gbn'
						,'$overtime'
						,'$overpay'
						,'$addtime1'
						,'$addtime2'
						,'$maketime'
						,'$makepay'
						,'$addtime'
						,'$addpay'
						,'$totaltime'
						,'$totalpay'
						,'$suga_cd'
						,'$suga_cost'
						,'$monthtime'
						,'$monthpay'
						,'$reg_id'
						,'$reg_dt')";
			}
		}

		if (!empty($sl)){
			$sql = "insert into voucher_make (
					 org_no
					,voucher_kind
					,voucher_jumin
					,voucher_yymm
					,voucher_seq
					,voucher_gbn
					,voucher_gbn2
					,voucher_lvl
					,voucher_svc_kind
					,voucher_add_pay_gbn
					,voucher_overtime
					,voucher_overpay
					,voucher_addtime1
					,voucher_addtime2
					,voucher_maketime
					,voucher_makepay
					,voucher_addtime
					,voucher_addpay
					,voucher_totaltime
					,voucher_totalpay
					,voucher_suga_cd
					,voucher_suga_cost
					,voucher_month_time
					,voucher_month_pay
					,insert_id
					,insert_dt) values $sl";

			if (!$conn->execute($sql)){
				$conn->rollback();
				#echo nl2br($sql);
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
		}

		$conn->commit();
	}

	include_once('../inc/_db_close.php');

	if ($conn->mode == 1){
		echo '<script>';
		echo 'alert(\''.$myF->message('OK', 'N').'\');';
		echo 'location.href=\'../iljung/iljung_list.php?mode=3\';';
		echo '</script>';
	}
?>