<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$code	= $_POST['code'];
	$kind	= $_POST['kind'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];

	$in_type	= $_POST['in_type'];					//입금구분
	$in_cal		= $_POST['in_type_cal_'.$in_type];		//수입계산코드
	$in_income	= $_POST['in_type_income_'.$in_type];	//수입추가구분
	$in_dt		= $_POST['in_dt'];						//입금일자
	$in_jumin	= $ed->de($_POST['jumin']);				//주민번호(수급자)
	$in_name	= $conn->client_name($code, $in_jumin);	//수급자명
	$in_amt		= intval(str_replace(',','',$_POST['in_amt'])) * intval($in_cal);//입금금액

	$today		= date('Y-m-d', mktime());

	$ent_dt		= $_POST['ent_dt'];
	$ent_seq	= $_POST['ent_seq'];

	if (strlen($ent_dt) == 10 && intval($ent_seq) > 0){
		$is_update = true;
	}else{
		$is_update = false;
		$ent_dt	= $today;
		$ent_seq= $conn->get_data("select ifnull(max(deposit_seq), 0) + 1 from unpaid_deposit where org_no = '$code' and deposit_ent_dt = '$ent_dt'");
	}

	// 회계사코드
	$sql = "select m00_account_firm_cd
			  from m00center as mst
			 where m00_mcode = '$code'
			   and m00_mkind = '0'";
	$account_firm_code = $conn->get_data($sql);

	// 배치시작시간기록
	include('../inc/_batch_var.php');

	$conn->begin();

	// 입금
	if (!$is_update){
		$sql = "insert into unpaid_deposit (
				 org_no
				,deposit_ent_dt
				,deposit_seq
				,create_dt
				,create_id
				,deposit_reg_dt
				,deposit_jumin
				,deposit_type
				,deposit_amt) value (
				 '$code'
				,'$ent_dt'
				,'$ent_seq'
				,'$today'
				,'$code'
				,'$in_dt'
				,'$in_jumin'
				,'$in_type'
				,'$in_amt')";
	}else{
		$sql = "update unpaid_deposit
				   set update_dt      = '$today'
				,      update_id      = '$code'
				,      deposit_reg_dt = '$in_dt'
				,      deposit_type   = '$in_type'
				,      deposit_amt    = '$in_amt'
				 where org_no         = '$code'
				   and deposit_ent_dt = '$ent_dt'
				   and deposit_seq    = '$ent_seq'";
	}

	if (!$conn->execute($sql)){
		$conn->rollback();
		$conn->batch_log('99', date('Ym', mktime()), $batch_start_sec, $start_dt, $start_tm, '미수금입금 처리중 오류발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$income_seq= $conn->get_data("select ifnull(max(income_seq), 0) + 1 from center_income where org_no = '$code' and income_ent_dt = '$ent_dt'");

	// 수입
	if ($in_income > 0){
		if (!$is_update){
			$proofNo = $conn->_proofNo($code, $ent_dt, '0111111','income');

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
					 '$code'
					,'$ent_dt'
					,'$income_seq'
					,'$ent_seq'
					,'$code'
					,'$ent_dt'
					,'$account_firm_code'
					,'$in_dt'
					,'$in_amt'
					,'미수입금($in_name)'
					,'0111111'
					,'".SubStr($in_dt,0,4)."'
					,'$proofNo')";
		}else{
			$sql = "update center_income
					   set update_dt      = '$today'
					,      update_id      = '$code'
					,      income_acct_dt = '$in_dt'
					,      income_amt     = '$in_amt'
					 where org_no         = '$code'
					   and income_ent_dt  = '$ent_dt'
					   and deposit_seq    = '$ent_seq'";
		}

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->batch_log('99', date('Ym', mktime()), $batch_start_sec, $start_dt, $start_tm, '미수금입금 처리중 오류발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}
	}

	// 미납과 입금 연결
	if ($in_income > 0){
		$sql = "delete
				  from unpaid_deposit_list
				 where org_no         = '$code'
				   and deposit_ent_dt = '$ent_dt'
				   and deposit_seq    = '$ent_seq'";

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->batch_log('99', date('Ym', mktime()), $batch_start_sec, $start_dt, $start_tm, '미수금입금 처리중 오류발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}

		$unpaid_cnt  = sizeof($_POST['unpaid_yymm']);

		$sql = "insert into unpaid_deposit_list values ";
		$seq = 0;

		for($i=0; $i<$unpaid_cnt; $i++){
			$unpaid_yymm = str_replace('.', '', $_POST['unpaid_yymm'][$i]); //미납년월
			$unpaid_amt  = str_replace(',', '', $_POST['unpaid_amt'][$i]);  //미납금액
			$deposit_amt = str_replace(',', '', $_POST['deposit_amt'][$i]); //입금금액

			if ($deposit_amt > 0){
				$seq ++;

				if ($sl) $sl .= ",";

				$sl .= "('$code'
						,'$ent_dt'
						,'$ent_seq'
						,'$seq'
						,'$unpaid_yymm'
						,'$in_jumin'
						,'$deposit_amt')";
			}
		}

		if ($seq > 0){
			$sql .= $sl;

			if (!$conn->execute($sql)){
				$conn->rollback();
				$conn->batch_log('99', date('Ym', mktime()), $batch_start_sec, $start_dt, $start_tm, '미수금입금 처리중 오류발생');
				echo $myF->message('error', 'Y', 'Y');
				exit;
			}
		}
	}

	$conn->commit();

	include_once("../inc/_db_close.php");
?>
<script>
	alert('<?=$myF->message("ok","N");?>');
	location.replace('unpaid_reg.php?mode=<?=$is_update;?>&jumin=<?=$ed->en($in_jumin);?>&year=<?=$year;?>&month=<?=$month;?>');
</script>