<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	parse_str($_POST['arguments'], $val);

	$code  = $val['code'];
	$kind  = $val['kind'];
	$jumin = $ed->de($val['jumin']);
	$date  = $val['date'];
	$svcType   = $val['type'];
	$cnt   = sizeof($val['sugaCode']);
	$today = date('Y-m-d', mktime());
	$regID = $_SESSION['userCode'];

	$sql = 'select ifnull(max(bill_seq), 0) + 1
			  from svc_use_bill
			 where org_no     = \''.$code.'\'
			   and bill_jumin = \''.$jumin.'\'';

	$seq = $conn->get_data($sql);

	$amt['200'] = 0;
	$amt['500'] = 0;
	$amt['800'] = 0;

	for($i=0; $i<$cnt; $i++){
		$svcCode  = $val['svcCD'][$i];
		$sugaCode = $val['sugaCode'][$i];
		$sugaName = $val['sugaName'][$i];
		$sugaCost = str_replace(',', '', $val['sugaCost'][$i]);
		$sugaCnt  = $val['sugaCnt'][$i];
		$sugaMy   = str_replace(',', '', $val['sugaMy'][$i]);
		$myPay    = str_replace(',', '', $val['myPay'][$i]);

		$sl[$i] = 'insert into svc_use_bill_item (
				    org_no
			  	  ,bill_jumin
				  ,bill_seq
				  ,item_seq
				  ,item_svc_cd
				  ,item_suga_cd
				  ,item_suga_nm
				  ,item_suga
				  ,item_bonin_amt
				  ,item_suga_my
				  ,item_suga_cnt
				  ,item_suga_amt) values (
				   \''.$code.'\'
				  ,\''.$jumin.'\'
				  ,\''.$seq.'\'
				  ,\''.($i+1).'\'
				  ,\''.$svcCode.'\'
				  ,\''.$sugaCode.'\'
				  ,\''.$sugaName.'\'
				  ,\''.$sugaCost.'\'
				  ,\''.$myPay.'\'
				  ,\''.$sugaMy.'\'
				  ,\''.$sugaCnt.'\'
				  ,\''.($sugaCost * $sugaCnt).'\')';

		$amt['myPay']  += $sugaMy;
		$amt[$svcCode] += ($sugaCost * $sugaCnt);
	}

	$sql = 'insert into svc_use_bill (
			 org_no
			,bill_jumin
			,bill_seq
			,bill_dt
			,bill_svc_nm
			,bill_suga_dt
			,bill_care_amt
			,bill_bath_amt
			,bill_nurs_amt
			,bill_bonin_amt
			,bill_total_amt
			,insert_id
			,insert_dt) values (
			 \''.$code.'\'
			,\''.$jumin.'\'
			,\''.$seq.'\'
			,\''.$today.'\'
			,\''.$svcType.'\'
			,\''.$date.'\'
			,\''.$amt['200'].'\'
			,\''.$amt['500'].'\'
			,\''.$amt['800'].'\'
			,\''.$amt['myPay'].'\'
			,\''.($amt['200']+$amt['500']+$amt['800']).'\'
			,\''.$regID.'\'
			,\''.$today.'\')';


	$conn->begin();

	if (!$conn->execute($sql)){
		$conn->rollback();
		$conn->close();
		echo 'error_1';
	}


	$cnt = sizeof($sl);

	for($i=0; $i<$cnt; $i++){
		if (!$conn->execute($sl[$i])){
			$conn->rollback();
			$conn->close();
			echo 'error_2';
		}
	}

	$conn->commit();

	echo $seq;

	include_once('../inc/_db_close.php');
?>