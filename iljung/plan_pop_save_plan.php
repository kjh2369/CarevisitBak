<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code    = $_POST['code'];
	$jumin   = $_POST['jumin'];
	$svcCd   = $_POST['svcCd'];
	$date    = $_POST['date'];
	$time    = $_POST['time'];
	$seq     = $_POST['seq'];
	$type    = $_POST['type'];
	
	$svcKind = $_POST['svcKind'];
	$sudangPay  = $_POST['sudangPay'];
	$sudangKind = $_POST['sudangKind'];
	$sudangVal1 = $_POST['sudangVal1'];
	$sudangVal2 = $_POST['sudangVal2'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	if ($svcKind != '200'){
		$ynExtra = 'Y';
	}else{
		$ynExtra = 'N';
	}

	$lsExtraKind  = 'N';

	if ($svcKind == '500'){
		if ($sudangKind == 'RATE'){
			$lsExtraKind = 'RATE'; //수당구분(비율)
			$liExtraRate1   = $sudangVal1;
			$liExtraRate2   = $sudangVal2;
			$liExtraAmt1    = 0;
			$liExtraAmt2    = 0;
		}else if ($sudangKind == 'AMT'){
			$lsExtraKind = 'AMT'; //수당구분(금액)
			$liExtraRate1   = 0;
			$liExtraRate2   = 0;
			$liExtraAmt1    = $sudangVal1;
			$liExtraAmt2    = $sudangVal2;
		}else{
			$lsExtraKind = 'PERSON'; //개별
			$liExtrPay = 0;
			$liExtraRate1   = 0;
			$liExtraRate2   = 0;
			$liExtraAmt1    = $sudangVal1;
			$liExtraAmt2    = $sudangVal2;
		}
	}else if ($svcKind == '800'){
		if ($sudangKind == 'AMT'){
			$lsExtraKind = 'AMT';
		}else{
			$lsExtraKind = 'PERSON';
			$liExtraRate1 = $sudangVal1;
		}
	}

	$memCd1 = $ed->de($_POST['memCd1']);
	$memCd2 = $ed->de($_POST['memCd2']);

	$liF = IntVal(SubStr($_POST['from'],0,2)) * 60 + IntVal(SubStr($_POST['from'],2,2));
	$liT = IntVal(SubStr($_POST['to'],0,2)) * 60 + IntVal(SubStr($_POST['to'],2,2));

	if ($liF  > $liT){
		$liT = $liT + (24 * 60);
	}

	$liTime = $liT - $liF;

	$sql = 'UPDATE t01iljung
			   SET t01_sugup_fmtime		= \''.$_POST['from'].'\'
			,      t01_sugup_totime		= \''.$_POST['to'].'\'
			,      t01_sugup_soyotime	= \''.$liTime.'\'
			,      t01_mem_cd1			= \''.$memCd1.'\'
			,      t01_mem_cd2			= \''.$memCd2.'\'
			,      t01_mem_nm1			= \''.$_POST['memNm1'].'\'
			,      t01_mem_nm2			= \''.$_POST['memNm2'].'\'
			,      t01_yoyangsa_id1		= CASE t01_status_gbn WHEN \'1\' THEN t01_yoyangsa_id1 ELSE \''.$memCd1.'\' END
			,      t01_yoyangsa_id2		= CASE t01_status_gbn WHEN \'1\' THEN t01_yoyangsa_id2 ELSE \''.$memCd2.'\' END
			,      t01_yname1			= CASE t01_status_gbn WHEN \'1\' THEN t01_yname1 ELSE \''.$_POST['memNm1'].'\' END
			,      t01_yname2			= CASE t01_status_gbn WHEN \'1\' THEN t01_yname2 ELSE \''.$_POST['memNm2'].'\' END
			,      t01_suga_code1		= \''.$_POST['sugaCd'].'\'
			,      t01_suga				= \''.$_POST['sugaCost'].'\'
			,      t01_suga_over		= \''.$_POST['sugaECost'].'\'
			,      t01_suga_night		= \''.$_POST['sugaNCost'].'\'
			,      t01_suga_tot			= \''.$_POST['sugaTCost'].'\'
			,      t01_e_time			= \''.$_POST['sugaETime'].'\'
			,      t01_n_time			= \''.$_POST['sugaNTime'].'\'
			,      t01_yname5			=\''.$lsExtraKind.'\'
			,      t01_ysudang_yn		= \''.$ynExtra.'\'
			,      t01_ysudang			= \''.$sudangPay.'\'
			,      t01_ysudang_yul1		= \''.$liExtraRate1.'\'
			,      t01_ysudang_yul2		= \''.$liExtraRate2.'\'
			,      t01_yoyangsa_id3		= \''.$liExtraAmt1.'\'
			,      t01_yoyangsa_id4		= \''.$liExtraAmt2.'\'
			 WHERE t01_ccode			= \''.$code.'\'
			   AND t01_mkind			= \''.$svcCd.'\'
			   AND t01_jumin			= \''.$jumin.'\'
			   AND t01_sugup_date		= \''.$date.'\'
			   AND t01_sugup_fmtime		= \''.$time.'\'
			   AND t01_sugup_seq		= \''.$seq.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();

	echo 1;

	include_once('../inc/_db_close.php');
?>