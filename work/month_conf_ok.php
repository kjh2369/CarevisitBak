<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');
	include('../inc/_myFun.php');
	include('../inc/_ed.php');

	$con2 = new connection();

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$mCode  = $_POST['mCode'];  //기관코드
	$mKind  = $_POST['mKind'];  //기관분류코드
	$mPayDate = $_POST['confYear'].$_POST['confMonth'];
	$mSugupja = $ed->de($_POST['mSugupja']);

	$startDate = $_POST['confYear'].$_POST['confMonth'].'01';
	$endDate = $_POST['confYear'].$_POST['confMonth'].date("t", mkTime(0, 0, 1, $_POST['confMonth'], 1, $_POST['confYear']));
	$billNo = $conn->get_data("select ifnull(max(t13_bill_no), '') from t13sugupja where t13_ccode = '".$mCode."' and t13_mkind = '".$mKind."' and t13_pay_date = '".$mPayDate."' and t13_type = '2'");

	$sql = "select m03_ccode"
		 . ",      m03_mkind"
		 . ",      m03_jumin"
		 . ",      m03_name"
		 . ",      m03_ylvl"
		 . ",      m03_skind"
		 . ",      m03_bonin_yul"
		 . ",      m03_kupyeo_max"
		 . ",      m03_sdate"
		 . ",      m03_edate"
		 . ",      m03_key"
		 . "  from ("
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
		 . "'      ) as t"
		 . " order by m03_skind, m03_sdate, m03_edate";
	//echo $sql;

	$y = 0;

	$conn->query($sql);
	$conn->fetch();
	$r1_count = $conn->row_count();

	for($i=0; $i<$r1_count; $i++){
		$r1 = $conn->select_row($i);

		$sql = "select *"
			 . "  from t01iljung"
			 . " where t01_ccode = '".$r1['m03_ccode']
			 . "'  and t01_mkind = '".$r1['m03_mkind']
			 . "'  and t01_jumin = '".$r1['m03_jumin']
			 . "'  and left(t01_sugup_date, 6) = '".$mPayDate
			 . "'  and t01_sugup_date between '".$r1['m03_sdate']
			 . "'                         and '".$r1['m03_edate']
			 . "'  and t01_del_yn = 'N'"
			 . " order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";
		$con2->query($sql);
		$con2->fetch();
		$r2_count = $con2->row_count();

		for($j=0; $j<$r2_count; $j++){
			$r2 = $con2->select_row($j);

			// 수급자의 의료등급
			if ($sugupjaKind != $r1['m03_bonin_yul']){
				$sugupjaKind  = $r1['m03_bonin_yul'];

				// 계획 데이타 초기화
				$p[$y] = setInit($conn, $r2['t01_ccode'], $r2['t01_mkind'], $r2['t01_jumin'], $mPayDate, $r1['m03_bonin_yul'], '1', $r1['m03_kupyeo_max']);

				// 실적 데이타 초기화
				$c[$y] = setInit($conn, $r2['t01_ccode'], $r2['t01_mkind'], $r2['t01_jumin'], $mPayDate, $r1['m03_bonin_yul'], '2', $r1['m03_kupyeo_max']);

				$y ++;
				$k = $y - 1;

				// 계획총금액
				$p[$k]['realTotal'] = getTotalSuga($conn, 't01_suga', $mCode, $mKind, $mSugupja, $r1['m03_sdate'], $r1['m03_edate']);

				// 확정총금액
				$c[$k]['realTotal'] = getTotalSuga($conn, 't01_conf_suga_value', $mCode, $mKind, $mSugupja, $r1['m03_sdate'], $r1['m03_edate']);
			}

			switch($r2['t01_svc_subcode']){
			case '200':
				$svcIndex = '1';
				break;
			case '500':
				$svcIndex = '2';
				break;
			case '800':
				$svcIndex = '3';
				break;
			}

			//계획 총 수가
			$p[$k]['sugaTot'.$svcIndex] = $p[$k]['sugaTot'.$svcIndex] + $r2['t01_suga'];

			//추가금
			//case when sum(case t01_bipay_umu when 'Y' then 0 else t01_suga end) * (m03_bonin_yul / 100) - sum(t01_suga) > 0 then sum(case t01_bipay_umu when 'Y' then 0 else t01_suga end) * (m03_bonin_yul / 100) - sum(t01_suga) else 0 end as overPay
			if ($p[$k]['maxAmt'] < $p[$k]['sugaTotal'] + (($r2['t01_bipay_umu'] != 'Y') ? $r2['t01_suga'] : 0)){
				//총급여액보다 계획총금액이 넘어갔다.
				if ($p[$k]['overAmt'] == 0){
					$tempSuga = $p[$k]['maxAmt'] - $p[$k]['sugaTotal'];
					$p[$k]['boninAmt'.$svcIndex] = $p[$k]['boninAmt'.$svcIndex] + $tempSuga * ($r1['m03_bonin_yul'] / 100);
					$p[$k]['overAmt'.$svcIndex] = $r2['t01_suga'] - $tempSuga;
				}else{
					$p[$k]['overAmt'.$svcIndex] = $p[$k]['overAmt'.$svcIndex] + (($r2['t01_bipay_umu'] != 'Y') ? $r2['t01_suga'] : 0);
				}
				$p[$k]['overAmt'] = $p[$k]['overAmt'] + (($r2['t01_bipay_umu'] != 'Y') ? $r2['t01_suga'] : 0);
			}else{
				//계획 본인부담금
				$p[$k]['boninAmt'.$svcIndex] = $p[$k]['boninAmt'.$svcIndex] + ((($r2['t01_bipay_umu'] != 'Y') ? $r2['t01_suga'] : 0) * ($r1['m03_bonin_yul'] / 100));
			}

			//총 수가금액
			$p[$k]['sugaTotal'] = $p[$k]['sugaTotal'] + (($r2['t01_bipay_umu'] != 'Y') ? $r2['t01_suga'] : 0);

			//비급여
			$p[$k]['biPay'.$svcIndex] = $p[$k]['biPay'.$svcIndex] + (($r2['t01_bipay_umu'] == 'Y') ? $r2['t01_suga'] : 0);




			if ($r2['t01_conf_soyotime'] > (($r2['t01_svc_subcode'] == '200') ? 29 : 0)){
				//확정 총 수가
				$c[$k]['sugaTot'.$svcIndex] = $c[$k]['sugaTot'.$svcIndex] + (($r2['t01_bipay_umu'] != 'Y') ? $r2['t01_conf_suga_value'] : 0);

				//추가금
				if ($c[$k]['maxAmt'] < $c[$k]['sugaTotal'] + (($r2['t01_bipay_umu'] != 'Y') ? $r2['t01_conf_suga_value'] : 0)){
					//총급여액보다 계획총금액이 넘어갔다.
					if ($c[$k]['overAmt'] == 0){
						$tempSuga = $c[$k]['maxAmt'] - $c[$k]['sugaTotal'];
						$c[$k]['boninAmt'.$svcIndex] = $c[$k]['boninAmt'.$svcIndex] + ($tempSuga * ($r1['m03_bonin_yul'] / 100));
						$c[$k]['overAmt'.$svcIndex] = (($r2['t01_bipay_umu'] != 'Y') ? $r2['t01_conf_suga_value'] : 0) - $tempSuga;
					}else{
						$c[$k]['overAmt'.$svcIndex] = $c[$k]['overAmt'.$svcIndex] + (($r2['t01_bipay_umu'] != 'Y') ? $r2['t01_conf_suga_value'] : 0);
					}
					$c[$k]['overAmt'] = $c[$k]['overAmt'] + (($r2['t01_bipay_umu'] != 'Y') ? $r2['t01_conf_suga_value'] : 0);
				}else{
					//확정 본인부담금
					$c[$k]['boninAmt'.$svcIndex] = $c[$k]['boninAmt'.$svcIndex] + ((($r2['t01_bipay_umu'] != 'Y') ? $r2['t01_conf_suga_value'] : 0) * ($r1['m03_bonin_yul'] / 100));
				}

				//총 수가금액
				$c[$k]['sugaTotal'] = $c[$k]['sugaTotal'] + (($r2['t01_bipay_umu'] != 'Y') ? $r2['t01_conf_suga_value'] : 0);

				//비급여
				$c[$k]['biPay'.$svcIndex] = $c[$k]['biPay'.$svcIndex] + (($r2['t01_bipay_umu'] == 'Y') ? $r2['t01_conf_suga_value'] : 0);
			}
		}
		$con2->row_free();
	}
	$conn->row_free();

	$pCount = sizeOf($p);

	for($k=0; $k<$pCount; $k++){
		//총본인부담금액
		/*
		$p[$k]['overAmt'] = $p[$k]['overAmt1'] + $p[$k]['overAmt2'] + $p[$k]['overAmt3'];

		$p[$k]['bonbuTot1'] = $p[$k]['boninAmt1'] + $p[$k]['overAmt1'] + $p[$k]['biPay1'];
		$p[$k]['bonbuTot2'] = $p[$k]['boninAmt2'] + $p[$k]['overAmt2'] + $p[$k]['biPay2'];
		$p[$k]['bonbuTot3'] = $p[$k]['boninAmt3'] + $p[$k]['overAmt3'] + $p[$k]['biPay3'];
		$p[$k]['chungAmt1'] = $p[$k]['sugaTot1'] - $p[$k]['bonbuTot1'];
		$p[$k]['chungAmt2'] = $p[$k]['sugaTot2'] - $p[$k]['bonbuTot2'];
		$p[$k]['chungAmt3'] = $p[$k]['sugaTot3'] - $p[$k]['bonbuTot3'];

		$p[$k]['boninAmt1'] += ($p[$k]['chungAmt1'] - cutOff($p[$k]['chungAmt1']));
		$p[$k]['boninAmt2'] += ($p[$k]['chungAmt2'] - cutOff($p[$k]['chungAmt2']));
		$p[$k]['boninAmt3'] += ($p[$k]['chungAmt3'] - cutOff($p[$k]['chungAmt3']));

		$p[$k]['bonbuTot1'] = $p[$k]['boninAmt1'] + $p[$k]['overAmt1'] + $p[$k]['biPay1'];
		$p[$k]['bonbuTot2'] = $p[$k]['boninAmt2'] + $p[$k]['overAmt2'] + $p[$k]['biPay2'];
		$p[$k]['bonbuTot3'] = $p[$k]['boninAmt3'] + $p[$k]['overAmt3'] + $p[$k]['biPay3'];

		$p[$k]['chungAmt1'] = cutOff($p[$k]['chungAmt1']);
		$p[$k]['chungAmt2'] = cutOff($p[$k]['chungAmt2']);
		$p[$k]['chungAmt3'] = cutOff($p[$k]['chungAmt3']);
		*/

		/*
		 * 2011.04.06
		 * 공단청구액 및 본인 부담금 계산방식을
		 * 본인 부담금 계산 후 총금액에서 빼서 공단청구액을 구하는 방식으로 변경.
		 */
			$p[$k]['overAmt'] = $p[$k]['overAmt1'] + $p[$k]['overAmt2'] + $p[$k]['overAmt3'];

			$p[$k]['boninAmt1'] = $myF->cutOff($p[$k]['boninAmt1']);
			$p[$k]['boninAmt2'] = $myF->cutOff($p[$k]['boninAmt2']);
			$p[$k]['boninAmt3'] = $myF->cutOff($p[$k]['boninAmt3']);

			$p[$k]['bonbuTot1'] = $myF->cutOff($p[$k]['boninAmt1'] + $p[$k]['overAmt1'] + $p[$k]['biPay1']);
			$p[$k]['bonbuTot2'] = $myF->cutOff($p[$k]['boninAmt2'] + $p[$k]['overAmt2'] + $p[$k]['biPay2']);
			$p[$k]['bonbuTot3'] = $myF->cutOff($p[$k]['boninAmt3'] + $p[$k]['overAmt3'] + $p[$k]['biPay3']);

			$p[$k]['chungAmt1'] = $p[$k]['sugaTot1'] - $p[$k]['bonbuTot1'];
			$p[$k]['chungAmt2'] = $p[$k]['sugaTot2'] - $p[$k]['bonbuTot2'];
			$p[$k]['chungAmt3'] = $p[$k]['sugaTot3'] - $p[$k]['bonbuTot3'];
		/*
		 * 여기까지...
		 */

		$p[$k]['sugaTot4']  = $p[$k]['sugaTot1']  + $p[$k]['sugaTot2']  + $p[$k]['sugaTot3'];
		$p[$k]['boninAmt4'] = $p[$k]['boninAmt1'] + $p[$k]['boninAmt2'] + $p[$k]['boninAmt3'];
		$p[$k]['overAmt4']  = $p[$k]['overAmt1']  + $p[$k]['overAmt2']  + $p[$k]['overAmt3'];
		$p[$k]['biPay4']    = $p[$k]['biPay1']    + $p[$k]['biPay2']    + $p[$k]['biPay3'];
		$p[$k]['bonbuTot4'] = $p[$k]['bonbuTot1'] + $p[$k]['bonbuTot2'] + $p[$k]['bonbuTot3'];
		$p[$k]['chungAmt4'] = $p[$k]['chungAmt1'] + $p[$k]['chungAmt2'] +$p[$k]['chungAmt3'];

		$p[$k]['resultAmt'] = $p[$k]['maxAmt'] - $p[$k]['sugaTot4'];
		$p[$k]['billNo']    = getBillNo($billNo);
	}

	$cCount = sizeOf($c);

	for($k=0; $k<$cCount; $k++){
		/*
		$c[$k]['overAmt'] = $c[$k]['overAmt1'] + $c[$k]['overAmt2'] + $c[$k]['overAmt3'];

		$c[$k]['bonbuTot1'] = $c[$k]['boninAmt1'] + $c[$k]['overAmt1'] + $c[$k]['biPay1'];
		$c[$k]['bonbuTot2'] = $c[$k]['boninAmt2'] + $c[$k]['overAmt2'] + $c[$k]['biPay2'];
		$c[$k]['bonbuTot3'] = $c[$k]['boninAmt3'] + $c[$k]['overAmt3'] + $c[$k]['biPay3'];

		$c[$k]['chungAmt1'] = $c[$k]['sugaTot1'] - $c[$k]['bonbuTot1'];
		$c[$k]['chungAmt2'] = $c[$k]['sugaTot2'] - $c[$k]['bonbuTot2'];
		$c[$k]['chungAmt3'] = $c[$k]['sugaTot3'] - $c[$k]['bonbuTot3'];

		$c[$k]['boninAmt1'] += ($c[$k]['chungAmt1'] - cutOff($c[$k]['chungAmt1']));
		$c[$k]['boninAmt2'] += ($c[$k]['chungAmt2'] - cutOff($c[$k]['chungAmt2']));
		$c[$k]['boninAmt3'] += ($c[$k]['chungAmt3'] - cutOff($c[$k]['chungAmt3']));

		$c[$k]['bonbuTot1'] = $c[$k]['boninAmt1'] + $c[$k]['overAmt1'] + $c[$k]['biPay1'];
		$c[$k]['bonbuTot2'] = $c[$k]['boninAmt2'] + $c[$k]['overAmt2'] + $c[$k]['biPay2'];
		$c[$k]['bonbuTot3'] = $c[$k]['boninAmt3'] + $c[$k]['overAmt3'] + $c[$k]['biPay3'];

		$c[$k]['chungAmt1'] = cutOff($c[$k]['chungAmt1']);
		$c[$k]['chungAmt2'] = cutOff($c[$k]['chungAmt2']);
		$c[$k]['chungAmt3'] = cutOff($c[$k]['chungAmt3']);
		*/

		/*
		 * 2011.04.06
		 * 공단청구액 및 본인 부담금 계산방식을
		 * 본인 부담금 계산 후 총금액에서 빼서 공단청구액을 구하는 방식으로 변경.
		 */
			$c[$k]['overAmt'] = $c[$k]['overAmt1'] + $c[$k]['overAmt2'] + $c[$k]['overAmt3'];

			$c[$k]['boninAmt1'] = $myF->cutOff($c[$k]['boninAmt1']);
			$c[$k]['boninAmt2'] = $myF->cutOff($c[$k]['boninAmt2']);
			$c[$k]['boninAmt3'] = $myF->cutOff($c[$k]['boninAmt3']);

			$c[$k]['bonbuTot1'] = $myF->cutOff($c[$k]['boninAmt1'] + $c[$k]['overAmt1'] + $c[$k]['biPay1']);
			$c[$k]['bonbuTot2'] = $myF->cutOff($c[$k]['boninAmt2'] + $c[$k]['overAmt2'] + $c[$k]['biPay2']);
			$c[$k]['bonbuTot3'] = $myF->cutOff($c[$k]['boninAmt3'] + $c[$k]['overAmt3'] + $c[$k]['biPay3']);

			$c[$k]['chungAmt1'] = $c[$k]['sugaTot1'] - $c[$k]['bonbuTot1'];
			$c[$k]['chungAmt2'] = $c[$k]['sugaTot2'] - $c[$k]['bonbuTot2'];
			$c[$k]['chungAmt3'] = $c[$k]['sugaTot3'] - $c[$k]['bonbuTot3'];
		/*
		 * 여기까지...
		 */

		$c[$k]['sugaTot4']  = $c[$k]['sugaTot1']  + $c[$k]['sugaTot2']  + $c[$k]['sugaTot3'];
		$c[$k]['boninAmt4'] = $c[$k]['boninAmt1'] + $c[$k]['boninAmt2'] + $c[$k]['boninAmt3'];
		$c[$k]['overAmt4']  = $c[$k]['overAmt1']  + $c[$k]['overAmt2']  + $c[$k]['overAmt3'];
		$c[$k]['biPay4']    = $c[$k]['biPay1']    + $c[$k]['biPay2']    + $c[$k]['biPay3'];
		$c[$k]['bonbuTot4'] = $c[$k]['bonbuTot1'] + $c[$k]['bonbuTot2'] + $c[$k]['bonbuTot3'];
		$c[$k]['chungAmt4'] = $c[$k]['chungAmt1'] + $c[$k]['chungAmt2'] + $c[$k]['chungAmt3'];

		$c[$k]['resultAmt'] = $c[$k]['maxAmt'] - $c[$k]['sugaTot4'];
		$c[$k]['misuAmt']   = $c[$k]['sugaTot4'];
		$c[$k]['billNo']    = getBillNo($billNo);
	}

	$conn->begin();

	for($k=0; $k<$pCount;$k++){
		$result = setData($conn, $p[$k]);

		if ($result != true){
			$conn->rollback();
			echo '<script>alert("확정 처리중 오류가 발생하였습니다."); history.back();</script>';
			exit;
		}
	}

	for($k=0; $k<$pCount;$k++){
		$result = setData($conn, $c[$k]);

		if ($result != true){
			$conn->rollback();
			echo '<script>alert("확정 처리중 오류가 발생하였습니다."); history.back();</script>';
			exit;
		}
	}

	$conn->commit();

	include('../inc/_db_close.php');

	// 총사용금액
	function getTotalSuga($p_conn, $p_filed, $p_ccode, $p_mkind, $p_jumin, $p_sdate, $p_edate){
		$sql = "select sum(".$p_filed.")"
			 . "  from t01iljung"
			 . " where t01_ccode = '".$p_ccode
			 . "'  and t01_mkind = '".$p_mkind
			 . "'  and t01_jumin = '".$p_jumin
			 . "'  and t01_sugup_date between '".$p_sdate
			 . "'                         and '".$p_edate
			 . "'  and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end"
			 . "   and t01_del_yn = 'N'";
		return $p_conn->get_data($sql);
	}

	// 영수증번호
	function getBillNo($p_billNo){
		$billNo = $p_billNo;

		if ($billNo == ''){
			$billNo = '1';
			$newBillNo = '000001';
		}else{
			$billNo = intVal($billNo) + 1;
			$newBillNo = '';
			for($j=strLen($billNo)+1; $j<=6; $j++){
				$newBillNo .= '0';
			}
			$newBillNo .= $billNo;
		}

		return $newBillNo;
	}

	// 배열초기화
	function setInit($p_conn, $p_ccode, $p_mkind, $p_jumin, $p_payDate, $p_boninYul, $p_type, $p_maxAmt){
		$d['ccode'] = $p_ccode; //기관코드
		$d['mkind'] = $p_mkind; //기관분류코드
		$d['jumin'] = $p_jumin; //수급자
		$d['payDate'] = $p_payDate; //확정년월
		$d['boninYul'] = $p_boninYul; //본인부담율
		$d['type'] = $p_type; //계획구분자
		$d['maxAmt'] = $p_maxAmt; //급여한도
		$d['resultAmt'] = 0;
		$d['overAmt'] = 0;
		$d['realTotal'] = 0;
		$d['sugaTotal'] = 0;

		for($i=1; $i<=4; $i++){
			$d['sugaTot'.$i] = 0;
			$d['boninAmt'.$i] = 0;
			$d['overAmt'.$i] = 0;
			$d['biPay'.$i] = 0;
			$d['bonbuTot'.$i] = 0;
			$d['chungAmt'.$i] = 0;
		}

		$d['misuAmt'] = 0;
		$d['misuInAmt'] = 0;
		$d['billNo'] = '000000';

		return $d;
	}

	// 데이타 저장
	function setData($conn, $a){
		# 기존 데이타삭제
		$sql = "delete"
			 . "  from t13sugupja"
			 . " where t13_ccode = '".$a['ccode']
			 . "'  and t13_mkind = '".$a['mkind']
			 . "'  and t13_jumin = '".$a['jumin']
			 . "'  and t13_pay_date = '".$a['pay_date']
			 . "'  and t13_type = '".$a['type']
			 . "'";
		if (!$conn->query($sql)){
			return false;
		}

		$sql = "insert into t13sugupja values ("
			 . "  '".$a['ccode']
			 . "','".$a['mkind']
			 . "','".$a['jumin']
			 . "','".$a['payDate']
			 . "','".$a['boninYul']
			 . "','".$a['type']
			 . "','".$a['maxAmt']
			 . "','".$a['resultAmt']
			 . "','".$a['overAmt']
			 . "','".$a['sugaTot1']
			 . "','".$a['boninAmt1']
			 . "','".$a['overAmt1']
			 . "','".$a['biPay1']
			 . "','".$a['bonbuTot1']
			 . "','".$a['chungAmt1']
			 . "','".$a['sugaTot2']
			 . "','".$a['boninAmt2']
			 . "','".$a['overAmt2']
			 . "','".$a['biPay2']
			 . "','".$a['bonbuTot2']
			 . "','".$a['chungAmt2']
			 . "','".$a['sugaTot3']
			 . "','".$a['boninAmt3']
			 . "','".$a['overAmt3']
			 . "','".$a['biPay3']
			 . "','".$a['bonbuTot3']
			 . "','".$a['chungAmt3']
			 . "','".$a['sugaTot4']
			 . "','".$a['boninAmt4']
			 . "','".$a['overAmt4']
			 . "','".$a['biPay4']
			 . "','".$a['bonbuTot4']
			 . "','".$a['chungAmt4']
			 . "','".$a['bonbuTot4']
			 . "','0"
			 . "','".$a['billNo']
			 . "')";

		if (!$conn->query($sql)){
			return false;
		}

		return true;
	}

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