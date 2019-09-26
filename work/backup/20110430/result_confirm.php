<?
	include_once("../inc/_db_open.php");
	//include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");

	/*
	 * pos
	 * 1 : Ajax로 실행
	 * 2 : window.open으로 실행
	 */

	$pos	= $_REQUEST['pos'];
	$code	= $_REQUEST['code'];
	$year	= $_REQUEST['year'];
	$month	= ($_REQUEST['month'] < 10 ? '0' : '').intval($_REQUEST['month']);
	$gubun	= $_REQUEST['gubun'];

	if ($pos == 2){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	}

	// 배치시작시간기록
	include('../inc/_batch_var.php');

	$sql = "select act_bat_conf_flag
			,      act_bat_conf_dt
			  from closing_progress
			 where org_no       = '$code'
			   and closing_yymm = '$year$month'";
	$temp_data = $conn->get_array($sql);

	if ($temp_data[0] == 'Y'){
		// 일괄확정이 진행되었다.
		if ($pos == 1){
			echo '이미 일괄확정이 진행된 년월입니다.';
		}else{
			echo $myF->message('이미 일괄확정이 진행된 년월입니다.', 'Y', 'N', 'Y');
		}
		exit;
	}

	if ($temp_data[1] > date('Y-m-d', mktime())){
		// 일괄확정 진행일이 아직 되지 앟았다.
		if ($pos == 1){
			echo '아직 일괄확정 진행일이 되지 않았습니다.\n'.$year.'년 '.$month.'월의 일괄확정진행일은 '.$temp_data[1].'입니다.';
		}else{
			echo $myF->message('아직 일괄확정 진행일이 되지 않았습니다.\n'.$year.'년 '.$month.'월의 일괄확정진행일은 '.$temp_data[1].'입니다.', 'Y', 'N', 'Y');
		}
		exit;
	}

	unset($temp_data);

	$bill_no = $conn->get_data("select ifnull(max(t13_bill_no), '') from t13sugupja where t13_ccode = '".$mCode."' and t13_mkind = '".$mKind."' and t13_pay_date = '".$year.$month."' and t13_type = '2'");

	// 수급자
	$sql = "select m03_ccode as code
			,      m03_mkind as kind
			,      m03_jumin as jumin
			,      m03_name as name
			,      m03_ylvl as ylvl
			,      m03_skind as skind
			,      m03_bonin_yul as bonin_yul
			,      m03_kupyeo_max as kupyeo_max
			,      m03_sdate as date_from
			,      m03_edate as date_to
			,      m03_key as client_key
			from (
				  select m03_ccode as m03_ccode
				  ,      m03_mkind as m03_mkind
				  ,      m03_jumin as m03_jumin
				  ,      m03_name as m03_name
				  ,      m03_ylvl as m03_ylvl
				  ,      m03_skind as m03_skind
				  ,      m03_bonin_yul as m03_bonin_yul
				  ,      m03_kupyeo_max as m03_kupyeo_max
				  ,      m03_sdate as m03_sdate
				  ,      m03_edate as m03_edate
				  ,      m03_key   as m03_key
				    from m03sugupja
				   where m03_ccode  = '$code'
				     and m03_del_yn = 'N'
				   union all
				  select m31_ccode as m03_ccode
				  ,      m31_mkind as m03_mkind
				  ,      m31_jumin as m03_jumin
				  ,      m03_name as m03_name
				  ,      m31_level as m03_ylvl
				  ,      m31_kind as m03_skind
				  ,      m31_bonin_yul as m03_bonin_yul
				  ,      m31_kupyeo_max as m03_kupyeo_max
				  ,      m31_sdate as m03_sdate
				  ,      m31_edate as m03_edate
				  ,      m03_key   as m03_key
				    from m31sugupja
				   inner join m03sugupja
					  on m03_ccode  = m31_ccode
				     and m03_mkind  = m31_mkind
				     and m03_jumin  = m31_jumin
					 and m03_del_yn = 'N'
				   where m31_ccode = '$code'
				  ) as t
			where '$year$month' between left(m03_sdate, 6) and left(m03_edate, 6)
			order by m03_skind, m03_sdate, m03_edate";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$client[$i] = $conn->select_row($i);
	}

	$conn->row_free();

	$conf_index   = 0;
	$client_count = sizeof($client);

	for($i=0; $i<$client_count; $i++){
		$sql = "select *
				  from t01iljung
				 where t01_ccode               = '".$client[$i]['code']."'
				   and t01_mkind               = '".$client[$i]['kind']."'
				   and t01_jumin               = '".$client[$i]['jumin']."'
				   and left(t01_sugup_date, 6) = '".$year.$month."'
				   and t01_sugup_date    between '".$client[$i]['date_from']."' and '".$client[$i]['date_to']."'
				   and t01_del_yn              = 'N'
				 order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		$y = 0;
		$client_kind = '';

		// 수급자 일정 확정
		for($j=0; $j<$row_count; $j++){
			$row = $conn->select_row($j);

			// 수급자의 의료등급
			if ($client_kind != $client[$i]['bonin_yul']){
				$client_kind  = $client[$i]['bonin_yul'];

				// 계획 데이타 초기화
				$p[$y] = init_array($row['t01_ccode'], $row['t01_mkind'], $row['t01_jumin'], $year.$month, $client[$i]['bonin_yul'], '1', $client[$i]['kupyeo_max']);

				// 실적 데이타 초기화
				$c[$y] = init_array($row['t01_ccode'], $row['t01_mkind'], $row['t01_jumin'], $year.$month, $client[$i]['bonin_yul'], '2', $client[$i]['kupyeo_max']);

				$y ++;
				$k = $y - 1;

				// 계획총금액
				$p[$k]['realTotal'] = get_total_amt($conn, 't01_suga', $client[$i]['code'], $client[$i]['kind'], $client[$i]['jumin'], $client[$i]['date_from'], $client[$i]['date_to']);

				// 확정총금액
				$c[$k]['realTotal'] = get_total_amt($conn, 't01_conf_suga_value', $client[$i]['code'], $client[$i]['kind'], $client[$i]['jumin'], $client[$i]['date_from'], $client[$i]['date_to']);
			}

			switch($row['t01_svc_subcode']){
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
			$p[$k]['sugaTot'.$svcIndex] = $p[$k]['sugaTot'.$svcIndex] + $row['t01_suga'];

			//추가금
			if ($p[$k]['maxAmt'] < $p[$k]['sugaTotal'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_suga'] : 0)){
				//총급여액보다 계획총금액이 넘어갔다.
				if ($p[$k]['overAmt'] == 0){
					$tempSuga = $p[$k]['maxAmt'] - $p[$k]['sugaTotal'];
					$p[$k]['boninAmt'.$svcIndex] = $p[$k]['boninAmt'.$svcIndex] + $tempSuga * ($client[$i]['bonin_yul'] / 100);
					$p[$k]['overAmt'.$svcIndex] = $row['t01_suga'] - $tempSuga;
				}else{
					$p[$k]['overAmt'.$svcIndex] = $p[$k]['overAmt'.$svcIndex] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_suga'] : 0);
				}
				$p[$k]['overAmt'] = $p[$k]['overAmt'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_suga'] : 0);
			}else{
				//계획 본인부담금
				$p[$k]['boninAmt'.$svcIndex] = $p[$k]['boninAmt'.$svcIndex] + ((($row['t01_bipay_umu'] != 'Y') ? $row['t01_suga'] : 0) * ($client[$i]['bonin_yul'] / 100));
			}

			//총 수가금액
			$p[$k]['sugaTotal'] = $p[$k]['sugaTotal'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_suga'] : 0);

			//비급여
			$p[$k]['biPay'.$svcIndex] = $p[$k]['biPay'.$svcIndex] + (($row['t01_bipay_umu'] == 'Y') ? $row['t01_suga'] : 0);




			if ($row['t01_conf_soyotime'] > (($row['t01_svc_subcode'] == '200') ? 29 : 0)){
				//확정 총 수가
				$c[$k]['sugaTot'.$svcIndex] = $c[$k]['sugaTot'.$svcIndex] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);

				//추가금
				if ($c[$k]['maxAmt'] < $c[$k]['sugaTotal'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0)){
					//총급여액보다 계획총금액이 넘어갔다.
					if ($c[$k]['overAmt'] == 0){
						$tempSuga = $c[$k]['maxAmt'] - $c[$k]['sugaTotal'];
						$c[$k]['boninAmt'.$svcIndex] = $c[$k]['boninAmt'.$svcIndex] + ($tempSuga * ($client[$i]['bonin_yul'] / 100));
						$c[$k]['overAmt'.$svcIndex] = (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0) - $tempSuga;
					}else{
						$c[$k]['overAmt'.$svcIndex] = $c[$k]['overAmt'.$svcIndex] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);
					}
					$c[$k]['overAmt'] = $c[$k]['overAmt'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);
				}else{
					//확정 본인부담금
					$c[$k]['boninAmt'.$svcIndex] = $c[$k]['boninAmt'.$svcIndex] + ((($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0) * ($client[$i]['bonin_yul'] / 100));
				}

				//총 수가금액
				$c[$k]['sugaTotal'] = $c[$k]['sugaTotal'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);

				//비급여
				$c[$k]['biPay'.$svcIndex] = $c[$k]['biPay'.$svcIndex] + (($row['t01_bipay_umu'] == 'Y') ? $row['t01_conf_suga_value'] : 0);
			}
		}

		$conn->row_free();

		if ($row_count > 0){
			$bill_no = get_billno($bill_no);

			$p_count = sizeOf($p);

			for($k=0; $k<$p_count; $k++){
				//총본인부담금액
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

				$p[$k]['sugaTot4']  = $p[$k]['sugaTot1']  + $p[$k]['sugaTot2']  + $p[$k]['sugaTot3'];
				$p[$k]['boninAmt4'] = $p[$k]['boninAmt1'] + $p[$k]['boninAmt2'] + $p[$k]['boninAmt3'];
				$p[$k]['overAmt4']  = $p[$k]['overAmt1']  + $p[$k]['overAmt2']  + $p[$k]['overAmt3'];
				$p[$k]['biPay4']    = $p[$k]['biPay1']    + $p[$k]['biPay2']    + $p[$k]['biPay3'];
				$p[$k]['bonbuTot4'] = $p[$k]['bonbuTot1'] + $p[$k]['bonbuTot2'] + $p[$k]['bonbuTot3'];
				$p[$k]['chungAmt4'] = $p[$k]['chungAmt1'] + $p[$k]['chungAmt2'] + $p[$k]['chungAmt3'];

				$p[$k]['resultAmt'] = $p[$k]['maxAmt'] - $p[$k]['sugaTot4'];
				$p[$k]['billNo']    = $bill_no;
			}

			$c_count = sizeOf($c);

			for($k=0; $k<$c_count; $k++){
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

				$c[$k]['sugaTot4']  = $c[$k]['sugaTot1']  + $c[$k]['sugaTot2']  + $c[$k]['sugaTot3'];
				$c[$k]['boninAmt4'] = $c[$k]['boninAmt1'] + $c[$k]['boninAmt2'] + $c[$k]['boninAmt3'];
				$c[$k]['overAmt4']  = $c[$k]['overAmt1']  + $c[$k]['overAmt2']  + $c[$k]['overAmt3'];
				$c[$k]['biPay4']    = $c[$k]['biPay1']    + $c[$k]['biPay2']    + $c[$k]['biPay3'];
				$c[$k]['bonbuTot4'] = $c[$k]['bonbuTot1'] + $c[$k]['bonbuTot2'] + $c[$k]['bonbuTot3'];
				$c[$k]['chungAmt4'] = $c[$k]['chungAmt1'] + $c[$k]['chungAmt2'] + $c[$k]['chungAmt3'];

				$c[$k]['resultAmt'] = $c[$k]['maxAmt'] - $c[$k]['sugaTot4'];
				$c[$k]['misuAmt']   = $c[$k]['sugaTot4'];
				$c[$k]['billNo']    = $bill_no;
			}

			$conf_data[$conf_index]['p'] = $p;
			$conf_data[$conf_index]['c'] = $c;

			$conf_index ++;
		}

		if (is_array($p)) unset($p);
		if (is_array($c)) unset($c);
	}

	unset($client);

	$conf_count = sizeof($conf_data);

	$conn->begin();

	//$conn->execute("delete from closing_result where org_no = '$code' and closing_yymm = '$year$month' and closing_gbn = '$gubun'");

	/*
	$sql = "insert into closing_result (id, org_no, closing_yymm, closing_gbn, closing_dt_f) values (null, '$code', '$year$month', '$gubun', now())";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$new_id = $conn->get_data("select max(id) from closing_result where org_no = '$code' and closing_yymm = '$year$month'");
	*/

	// 기존데이타 삭제
	$sql = "delete
			  from t13sugupja
			 where t13_ccode    = '$code'
			   and t13_pay_date = '$year$month'";

	if (!$conn->query($sql)){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		echo $conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, 't13sugupja 입력위한 삭제중 오류발생');
		exit;
	}

	for($i=0; $i<$conf_count; $i++){
		$p_count = sizeof($conf_data[$i]['p']);
		$c_count = sizeof($conf_data[$i]['c']);

		for($j=0; $j<$p_count; $j++){
			$result = set_data($conn, $conf_data[$i]['p'][$j]);

			if (!$result){
				$conn->rollback();
				echo $myF->message('error', 'Y', 'Y');

				// 로그기록
				$conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, '수급자 일괄확정중오류 발생(계획)');
				exit;
			}
		}

		for($j=0; $j<$c_count; $j++){
			$result = set_data($conn, $conf_data[$i]['c'][$j]);

			if (!$result){
				$conn->rollback();
				echo $myF->message('error', 'Y', 'Y');

				// 로그기록
				$conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, '수급자 일괄확정중오류 발생(실적)');
				exit;
			}
		}
	}

	/*
	if (!$conn->execute(set_message($new_id, $gubun, 'Y','수급자 일괄확정을 완료하였습니다.'))){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}
	*/

	$sql = "update closing_progress
			   set act_bat_conf_flag = 'Y'
			 where org_no       = '$code'
			   and closing_yymm = '$year$month'
			   and del_flag     = 'N'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		echo $conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'closing_progress 플래그 변경중 오류발생');
		exit;
	}

	// 로그기록
	$conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, '수급자 일괄확정 완료');
	$conn->commit();

	unset($conf_data);

	// 배열초기화
	function init_array($p_ccode, $p_mkind, $p_jumin, $p_payDate, $p_boninYul, $p_type, $p_maxAmt){
		$d['ccode']		= $p_ccode;		//기관코드
		$d['mkind']		= $p_mkind;		//기관분류코드
		$d['jumin']		= $p_jumin;		//수급자
		$d['payDate']	= $p_payDate;	//확정년월
		$d['boninYul']	= $p_boninYul;	//본인부담율
		$d['type']		= $p_type;		//계획구분자
		$d['maxAmt']	= $p_maxAmt;	//급여한도
		$d['resultAmt']	= 0;
		$d['overAmt']	= 0;
		$d['realTotal']	= 0;
		$d['sugaTotal']	= 0;

		for($i=1; $i<=4; $i++){
			$d['sugaTot'.$i]	= 0;
			$d['boninAmt'.$i]	= 0;
			$d['overAmt'.$i]	= 0;
			$d['biPay'.$i]		= 0;
			$d['bonbuTot'.$i]	= 0;
			$d['chungAmt'.$i]	= 0;
		}

		$d['misuAmt']	= 0;
		$d['misuInAmt']	= 0;
		$d['billNo']	= '000000';

		return $d;
	}

	// 총사용금액
	function get_total_amt($p_conn, $p_filed, $p_ccode, $p_mkind, $p_jumin, $p_sdate, $p_edate){
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
	function get_billno($p_billNo){
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

	// 데이타 저장
	function set_data($conn, $a){
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

	function set_message($id, $gbn, $rst, $msg){
		$sql = "update closing_result
				   set closing_dt_t = now()
				,      closing_rst  = '$rst'
				,      closing_msg  = '$msg'
				 where id           = '$id'";
		return $sql;
	}

	include_once("../inc/_db_close.php");
?>