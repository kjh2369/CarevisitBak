<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	define(__SPLIT__, '|');

	$__ADD_KEY__ = -100;

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	// 배치시작시간기록
	include('../inc/_batch_var.php');

	$code	= $_POST['code'];
	$kind	= $_POST['kind'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$recal	= $_POST['chkReCal'];

	$check		= $_POST['check'];
	$check_cnt	= sizeof($check);

	// 회계사코드
	$sql = "select m00_account_firm_cd
			  from m00center as mst
			 where m00_mcode = '$code'
			   and m00_mkind = '0'";
	$account_firm_code = $conn->get_data($sql);

	$mem = array();
	$mem_index = -1;

	for($i=0; $i<$check_cnt; $i++){
		$mem_cd = $ed->de($_POST['mem_cd'][$check[$i]]);
		$per_cd = $ed->de($_POST['per_cd'][$check[$i]]);
		$per_nm = $ed->de($_POST['per_nm'][$check[$i]]);

		$deposit_amt = $_POST['deposit_amt'][$check[$i]];
		$deposit_ahead = $_POST['in_amt'][$check[$i]];

		$is_update = $_POST['is_update'][$check[$i]];

		if ($temp_cd != $mem_cd){
			$temp_cd  = $mem_cd;
			$mem_index++;

			$mem[$mem_index]['mem']        = $mem_cd;
			$mem[$mem_index]['update']     = 'N';
			$mem[$mem_index]['cnt']        = 0;
			$mem[$mem_index]['fam_pay_yn'] = $_POST['fam_pay_yn'][$check[$i]];
		}

		$mem[$mem_index]['per']   .= ($mem[$mem_index]['per']   != '' ? __SPLIT__ : '').$per_cd;
		$mem[$mem_index]['nm']    .= ($mem[$mem_index]['nm']    != '' ? __SPLIT__ : '').$per_nm;
		$mem[$mem_index]['pay']   .= ($mem[$mem_index]['pay']   != '' ? __SPLIT__ : '').$deposit_amt;
		$mem[$mem_index]['ahead'] .= ($mem[$mem_index]['ahead'] != '' ? __SPLIT__ : '').$deposit_ahead;
		$mem[$mem_index]['yn']    .= ($mem[$mem_index]['yn']    != '' ? __SPLIT__ : '').$is_update;
		$mem[$mem_index]['amt']   += $deposit_amt;

		if ($mem[$mem_index]['update'] == 'N'){
			$mem[$mem_index]['update'] = $is_update;
		}

		$mem[$mem_index]['cnt'] ++;
	}

	$mem_cnt = sizeof($mem);

	$conn->begin();

	// 공제 항목 추가
	$sql = "select count(*)
			  from salary_addon
			 where org_no       = '$code'
			   and salary_type  = '2'
			   and salary_index = '$__ADD_KEY__'";

	if ($conn->get_data($sql) == 0){
		$sql = "insert into salary_addon (
				 org_no
				,salary_type
				,salary_index
				,salary_subject
				,salary_pay
				,salary_seq) values (
				 '$code'
				,'2'
				,'$__ADD_KEY__'
				,'본인부담금공제'
				,'0'
				,'1')";

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->batch_log('99', $year.$month, $batch_start_sec, $start_dt, $start_tm, '본인부담금 자동공제 항목 등록 중 에러발생');
			 echo $myF->message('error', 'Y', 'Y');
			 exit;
		}
	}

	$sql = "select count(*)
			  from salary_addon
			 where org_no       = '$code'
			   and salary_type  = '1'
			   and salary_index = '$__ADD_KEY__'";

	if ($conn->get_data($sql) == 0){
		$sql = "insert into salary_addon (
				 org_no
				,salary_type
				,salary_index
				,salary_subject
				,salary_pay
				,salary_seq) values (
				 '$code'
				,'1'
				,'$__ADD_KEY__'
				,'본인부담금수당'
				,'0'
				,'1')";

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->batch_log('99', $year.$month, $batch_start_sec, $start_dt, $start_tm, '본인부담금 자동공제 항목 등록 중 에러발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}
	}

	/************************************
	2012.08.27 김주완 수정
	기관테이블 급여지급일 조회
	************************************/
	$sql = "select m00_salary_day
			  from m00center as mst
			 where m00_mcode = '$code'
			   and m00_mkind = '0'";
	$salary_day = $conn->get_data($sql);

	//기관 급여지급일
	$salary_day = ($salary_day < 10 ? '0'.$salary_day : $salary_day);

	$today	= date('Y-m-d', mktime());
	$ent_dt	= $myF->dateAdd('month', 1, $year.'-'.$month.'-'.$salary_day, 'Y-m-d');

	//입금일 조회

	$sql = "select deposit_ent_dt
	          from unpaid_deposit
			 where org_no = '$code'
			   and deposit_ent_dt = '".substr($ent_dt, 0, 8)."05'
			   and deposit_auto   = 'Y'";
	$deposit_dt = $conn->get_data($sql);

	//if($debug) echo $deposit_dt; exit;

	//입금일
	$ent_dt = $deposit_dt == '' ? $ent_dt : substr($ent_dt, 0, 8).'05';


	$sql = "delete
			  from unpaid_auto_list
			 where org_no      = '$code'
			   and unpaid_yymm = '$year$month'";

	//echo $sql.'<br><br>';

	if (!$conn->execute($sql)){
		$conn->rollback();
		$conn->batch_log('99', $year.$month, $batch_start_sec, $start_dt, $start_tm, '본인부담금 자동공제 등록 중 에러발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$sql = "delete
			  from salary_addon_pay
			 where org_no       = '$code'
			   and salary_yymm  = '$year$month'
			   and salary_index = '$__ADD_KEY__'";

	//echo $sql.'<br><br>';

	if (!$conn->execute($sql)){
		$conn->rollback();
		$conn->batch_log('99', $year.$month, $batch_start_sec, $start_dt, $start_tm, '본인부담금 자동공제 등록 중 에러발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	// 수입/지출테이블 삭제
	$sql = "select deposit_seq
			  from unpaid_deposit
			 where org_no         = '$code'
			   and deposit_ent_dt = '$ent_dt'
			   and deposit_auto   = 'Y'";

	//echo $sql.'<br><br>';

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	//echo $row_count.'<br><br>';

	for($j=0; $j<$row_count; $j++){
		$row = $conn->select_row($j);

		$tmp_seq = $row['deposit_seq'];

		// 수입테이블 삭제
		$sql = "delete
				  from center_income
				 where org_no         = '$code'
				   and income_ent_dt  = '$ent_dt'
				   and deposit_seq    = '$tmp_seq'";

		//echo $sql.'<br><br>';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->batch_log('99', $year.$month, $batch_start_sec, $start_dt, $start_tm, '본인부담금 자동공제 등록 중 에러발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}

		// 미납연결 삭제
		$sql = "delete
				  from unpaid_deposit_list
				 where org_no         = '$code'
				   and deposit_ent_dt = '$ent_dt'
				   and deposit_seq    = '$tmp_seq'";

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->batch_log('99', $year.$month, $batch_start_sec, $start_dt, $start_tm, '본인부담금 자동공제 등록 중 에러발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}
	}

	$conn->row_free();

	// 입금테이블 삭제
	$sql = "delete
			  from unpaid_deposit
			 where org_no         = '$code'
			   and deposit_ent_dt = '$ent_dt'
			   and deposit_auto   = 'Y'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		$conn->batch_log('99', $year.$month, $batch_start_sec, $start_dt, $start_tm, '본인부담금 자동공제 등록 중 에러발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$ent_seq	= $conn->get_data("select ifnull(max(deposit_seq), 0) + 1 from unpaid_deposit where org_no = '$code' and deposit_ent_dt = '$ent_dt'");
	$income_seq = $conn->get_data("select ifnull(max(income_seq), 0) + 1 from center_income where org_no = '$code' and income_ent_dt = '$ent_dt'");

	for($i=0; $i<$mem_cnt; $i++){
		// 공제 내역 저장
		$sql = "insert into unpaid_auto_list (
				 org_no
				,unpaid_yymm
				,unpaid_jumin
				,unpaid_per_cnt
				,unpaid_per_cd
				,unpaid_amt) values (
				 '".($code)."'
				,'".($year.$month)."'
				,'".($mem[$i]['mem'])."'
				,'".($mem[$i]['cnt'])."'
				,'".($mem[$i]['per'])."'
				,'".($mem[$i]['amt'])."')";

		//echo $sql.'<br><br>';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->batch_log('99', $year.$month, $batch_start_sec, $start_dt, $start_tm, '본인부담금 자동공제 등록 중 에러발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}

		// 급여에 공제 적용
		$sql = "insert into salary_addon_pay (
				 org_no
				,salary_yymm
				,salary_jumin
				,salary_type
				,salary_index
				,salary_subject
				,salary_pay) values (
				 '".($code)."'
				,'".($year.$month)."'
				,'".($mem[$i]['mem'])."'
				,'2'
				,'$__ADD_KEY__'
				,'본인부담금공제'
				,'".($mem[$i]['amt'])."')";

		//echo $sql.'<br><br>';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->batch_log('99', $year.$month, $batch_start_sec, $start_dt, $start_tm, '본인부담금 자동공제 등록 중 에러발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}

		// 동거가족 본인부담금 수당지급
		if ($mem[$i]['fam_pay_yn'] == 'Y'){
			$sql = "insert into salary_addon_pay (
					 org_no
					,salary_yymm
					,salary_jumin
					,salary_type
					,salary_index
					,salary_subject
					,salary_pay) values (
					 '".($code)."'
					,'".($year.$month)."'
					,'".($mem[$i]['mem'])."'
					,'1'
					,'$__ADD_KEY__'
					,'본인부담금수당'
					,'".($mem[$i]['amt'])."')";

			//echo $sql.'<br><br>';

			if (!$conn->execute($sql)){
				$conn->rollback();
				$conn->batch_log('99', $year.$month, $batch_start_sec, $start_dt, $start_tm, '본인부담금 자동공제 등록 중 에러발생');
				echo $myF->message('error', 'Y', 'Y');
				exit;
			}
		}

		// 자동공제 입금잡기
		unset($sub_per);
		unset($sub_pay);
		unset($sub_ahead);
		unset($sub_yn);

		$sub_per   = explode(__SPLIT__, $mem[$i]['per']);
		$sub_nm    = explode(__SPLIT__, $mem[$i]['nm']);
		$sub_pay   = explode(__SPLIT__, $mem[$i]['pay']);
		$sub_ahead = explode(__SPLIT__, $mem[$i]['ahead']);
		$sub_yn    = explode(__SPLIT__, $mem[$i]['yn']);

		for($j=0; $j<sizeof($sub_per); $j++){
			// 입금테이블
			$sql = "insert into unpaid_deposit (
					 org_no
					,deposit_ent_dt
					,deposit_seq
					,create_dt
					,create_id
					,deposit_reg_dt
					,deposit_jumin
					,deposit_yymm
					,deposit_type
					,deposit_amt
					,deposit_ahead
					,deposit_auto
					,deposit_mem) values (
					 '".($code)."'
					,'".($ent_dt)."'
					,'".($ent_seq)."'
					,'".($today)."'
					,'".($code)."'
					,'".($ent_dt)."'
					,'".($sub_per[$j])."'
					,'".($year.$month)."'
					,'01'
					,'".($sub_pay[$j])."'
					,'".($sub_ahead[$j])."'
					,'Y'
					,'".($mem[$i]['mem'])."')";

			if (!$conn->execute($sql)){
				$conn->rollback();
				$conn->batch_log('99', $year.$month, $batch_start_sec, $start_dt, $start_tm, '본인부담금 자동공제 등록 중 에러발생');
				echo $myF->message('error', 'Y', 'Y');
				exit;
			}

			// 미납연결
			$sql = "insert into unpaid_deposit_list (org_no,deposit_ent_dt,deposit_seq,list_seq,unpaid_yymm,unpaid_jumin,deposit_amt) values (
					 '".($code)."'
				    ,'".($ent_dt)."'
				    ,'".($ent_seq)."'
				    ,'1'
				    ,'".($year.$month)."'
				    ,'".($sub_per[$j])."'
				    ,'".($sub_pay[$j])."')";

			if (!$conn->execute($sql)){
				$conn->rollback();
				$conn->batch_log('99', $year.$month, $batch_start_sec, $start_dt, $start_tm, '본인부담금 자동공제 등록 중 에러발생');
				echo $myF->message('error', 'Y', 'Y');
				exit;
			}

			//echo $sql.'<br><br>';

			$proofNo = $conn->_proofNo($code, $ent_dt, '0111111','income');

			// 수입테이블
			$sql = "insert into center_income (
					 org_no
					,income_ent_dt
					,income_seq
					,deposit_seq
					,create_id
					,create_dt
					,account_firm_cd
					,income_acct_dt
					,income_amt
					,income_item
					,income_item_cd
					,proof_year
					,proof_no) values (
					 '".($code)."'
					,'".($ent_dt)."'
					,'".($income_seq)."'
					,'".($ent_seq)."'
					,'".($code)."'
					,'".($today)."'
					,'".($account_firm_code)."'
					,'".($ent_dt)."'
					,'".($sub_pay[$j])."'
					,'자동미수입금(".($sub_nm[$j]).")'
					,'0111111'
					,'".SubStr($ent_dt,0,4)."'
					,'".$proofNo."')";

			if (!$conn->execute($sql)){
				$conn->rollback();
				$conn->batch_log('99', $year.$month, $batch_start_sec, $start_dt, $start_tm, '본인부담금 자동공제 등록 중 에러발생');
				echo $myF->message('error', 'Y', 'Y');
				exit;
			}

			//echo $sql.'<br><br><br><br><br><br>';

			$ent_seq ++;
			$income_seq ++;
		}
	}

	$conn->commit();

	if ($recal == 'Y'){
		$result = '1';
	}else{
		$result = '';
	}

	include_once("../inc/_db_close.php");
?>
<script>
	alert('<?=$myF->message("ok","N");?>');

	if ('<?=$conn->mode;?>' == '1')
		location.replace('unpaid_auto_detail.php?code=<?=$code;?>&kind=<?=$kind;?>&year=<?=$year;?>&month=<?=$month;?>&result=<?=$result;?>');
</script>