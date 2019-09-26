<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$mode   = $_POST['mode'];
	$code   = $_POST['code'];
	$from   = $_POST['from'];
	$to     = $_POST['to'];
	$seq    = IntVal($_POST['seq']);
	$acctYn = $_POST['acctYn'];

	if (Empty($code)){
		echo 9;
		exit;
	}

	if ($mode == '1'	||
		$mode == '11'	||
		$mode == '21'	||
		$mode == '51'	||
		$mode == '61'	||
		$mode == '62'	||
		$mode == '63'	||
		$mode == '64'){
		if ($mode == '1'){
			$table = 'sms_acct';
		}else if ($mode == '11'){
			$table = 'smart_acct';
		}else if ($mode == '21'){
			$table = 'bank_center';
		}else if ($mode == '51'){
			$table = 'mobile_acct';
		}else if ($mode == '61' || $mode == '63'){
			$table = 'tax_acct';
		}else if ($mode == '62'){
			$table = 'labor_acct';
		}else if ($mode == '64'){
			$table = 'fa_acct';
		}

		if (Empty($seq)){
			$type = 1;
			$sql = 'SELECT IFNULL(MAX(seq),0) + 1
					  FROM '.$table.'
					 WHERE org_no = \''.$code.'\'';
			$seq = $conn->get_data($sql);
		}else{
			$type = 2;
		}
	}

	if ($mode == '1'	||
		$mode == '11'	||
		$mode == '51'	||
		$mode == '61'	||
		$mode == '62'	||
		$mode == '63'	||
		$mode == '64'){
		if ($mode == '1'){
			$table = 'sms_acct';
		}else if ($mode == '11'){
			$table = 'smart_acct';
		}else if ($mode == '51'){
			$table = 'mobile_acct';
		}else if ($mode == '61' || $mode == '63'){
			$table = 'tax_acct';
		}else if ($mode == '62'){
			$table = 'labor_acct';
		}else if ($mode == '64'){
			$table = 'fa_acct';
		}

		if ($type == '1'){
			$sql = 'SELECT COUNT(*)
					  FROM '.$table.'
					 WHERE org_no   = \''.$code.'\'
					   AND from_dt <= \''.$from.'\'
					   AND to_dt   >= \''.$from.'\'';
			$liCnt1 = $conn->get_data($sql);

			$sql = 'SELECT COUNT(*)
					  FROM '.$table.'
					 WHERE org_no   = \''.$code.'\'
					   AND from_dt >= \''.$from.'\'';
			$liCnt2 = $conn->get_data($sql);

			if ($liCnt1 + $liCnt2 > 0){
				$conn->close();
				echo '기존의 내역과 적용기간이 중복됩니다. 확인 후 다시 등록하여 주십시오.';
				exit;
			}

			$sql = 'INSERT INTO '.$table.' (
					 org_no
					,seq
					,acct_yn';

			if ($mode == '61' || $mode == '63'){
				$sql .= '
					,acct_type';
			}

			$sql .= '
					,from_dt
					,to_dt) VALUES (
					 \''.$code.'\'
					,\''.$seq.'\'
					,\''.$acctYn.'\'';

			if ($mode == '61'){
				$sql .= '
					,\'9\'';
			}else if ($mode == '63'){
				$sql .= '
					,\'1\'';
			}

			$sql .= '
					,\''.$from.'\'
					,\''.$to.'\'
					)';
		}else{
			$sql = 'UPDATE '.$table.'
					   SET acct_yn = \''.$acctYn.'\'
					,      to_dt   = \''.$to.'\'
					 WHERE org_no  = \''.$code.'\'
					   AND seq     = \''.$seq.'\'';
		}

		if (!$conn->execute($sql)){
			 $conn->close();
			 echo 9;
			 exit;
		}

	}else if ($mode == '21'){
		if ($type == '1'){
			$sql = 'SELECT COUNT(*)
					  FROM bank_center
					 WHERE org_no   = \''.$code.'\'
					   AND from_dt <= \''.$from.'\'
					   AND to_dt   >= \''.$from.'\'';
			$liCnt1 = $conn->get_data($sql);

			$sql = 'SELECT COUNT(*)
					  FROM bank_center
					 WHERE org_no   = \''.$code.'\'
					   AND from_dt >= \''.$from.'\'';
			$liCnt2 = $conn->get_data($sql);

			if ($liCnt1 + $liCnt2 > 0){
				$conn->close();
				echo '기존의 내역과 적용기간이 중복됩니다. 확인 후 다시 등록하여 주십시오.';
				exit;
			}

			$sql = 'INSERT INTO bank_center (
					 org_no
					,seq
					,bank_cd
					,from_dt
					,to_dt
					,insert_dt) VALUES (
					 \''.$code.'\'
					,\''.$seq.'\'
					,\''.$_POST['bank'].'\'
					,\''.$from.'\'
					,\''.$to.'\'
					,NOW())';
		}else{
			$sql = 'UPDATE bank_center
					   SET to_dt  = \''.$to.'\'
					 WHERE org_no = \''.$code.'\'
					   AND seq    = \''.$seq.'\'';
		}

		if (!$conn->execute($sql)){
			 $conn->close();
			 echo 9;
			 exit;
		}

	}else if ($mode == '31'){
		if (!Empty($_POST['cmsCd'])){
			$cmsCd = $_POST['cmsCd'];

			if (StrLen($cmsCd) < 8){
				$liCnt = 8 - StrLen($cmsCd);

				$cmsCd = '';

				for($i=1; $i<=$liCnt; $i++){
					$cmsCd .= '0';
				}
				$cmsCd .= IntVal($_POST['cmsCd']);
				$_POST['cmsCd'] = $cmsCd;
			}

			$sql = 'SELECT b02_center AS cd
					  FROM b02center
					 WHERE cms_cd = \''.$_POST['cmsCd'].'\'';
			$tmpArr = $conn->_fetch_array($sql);

			$lbDuplicate = false;

			if (Is_Array($tmpArr)){
				foreach($tmpArr as $tmp){
					if ($tmp['cd'] != $code){
						$lbDuplicate = true;
						break;
					}
				}
			}

			if ($lbDuplicate){
				$conn->close();
				echo $_POST['cmsCd'].'/CMS코드가 중복됩니다. 확인 후 다시 입력하여 주십시오.';
				exit;
			}
		}

		$sql = 'REPLACE INTO b02center (
				 b02_center
				,b02_kind
				,b02_branch
				,b02_person
				,b02_date
				,b02_other
				,b02_homecare
				,b02_voucher
				,b02_caresvc
				,cms_cd
				,from_dt
				,to_dt
				,hold_yn
				,basic_cost
				,client_cost
				,client_cnt
				,care_area
				,care_group
				,care_support
				,care_resource) VALUES (
				 \''.$code.'\'
				,\'0\'
				,\''.$_POST['branch'].'\'
				,\''.Str_Replace($_POST['branch'], '', $_POST['manager']).'\'
				,\''.$_POST['startDt'].'\'
				,\''.$_POST['other'].'\'
				,\''.$_POST['homeCare'].'\'
				,\''.$_POST['vouNurse'].$_POST['vouOld'].$_POST['vouBaby'].$_POST['vouDis'].'\'
				,\''.$_POST['careSvc'].'\'
				,\''.$_POST['cmsCd'].'\'
				,\''.$_POST['fromDt'].'\'
				,\''.$_POST['toDt'].'\'
				,\''.$_POST['holdYn'].'\'
				,\''.$_POST['basicCost'].'\'
				,\''.$_POST['clientCost'].'\'
				,\''.$_POST['clientCnt'].'\'
				,\''.$_POST['careArea'].'\'
				,\''.$_POST['careGroup'].'\'
				,\''.$_POST['careSp'].'\'
				,\''.$_POST['careRs'].'\'
				)';

		$conn->execute($sql);

		$sql = 'UPDATE m00center
				   SET m00_start_date = \''.$_POST['startDt'].'\'
				,      m00_cont_date  = \''.$_POST['contDt'].'\'
				 WHERE m00_mcode = \''.$code.'\'';

		$conn->execute($sql);

	}else if ($mode == '33'){
		$code  = $_POST['code'];
		$date  = $_POST['date'];
		$amt   = $_POST['amt'];
		$other = $_POST['other'];

		$sql = 'SELECT COUNT(*)
				  FROM edu_acct
				 WHERE org_no = \''.$code.'\'
				   AND edu_dt = \''.$date.'\'';
		$liCnt = IntVal($conn->get_data($sql));

		if ($liCnt > 0){
			$sql = 'UPDATE edu_acct
					   SET amt    = \''.$amt.'\'
					,      other  = \''.$other.'\'
					 WHERE org_no = \''.$code.'\'
					   AND edu_dt = \''.$date.'\'';
		}else{
			$sql = 'INSERT INTO edu_acct (
					 org_no
					,edu_dt
					,amt
					,other) VALUES (
					 \''.$code.'\'
					,\''.$date.'\'
					,\''.$amt.'\'
					,\''.$other.'\'
					)';
		}

		if (!$conn->execute($sql)){
			 $conn->close();
			 echo 9;
			 exit;
		}


	}else{
		echo 9;
		exit;
	}

	echo 1;

	include_once('../inc/_db_close.php');
?>