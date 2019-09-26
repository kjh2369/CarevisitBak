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

	$sql = 'UPDATE t01iljung
			   SET t01_conf_date		= \'\'
			,      t01_conf_fmtime		= \'\'
			,      t01_conf_totime		= \'\'
			,      t01_conf_soyotime	= \'\'
			,      t01_yoyangsa_id1		= \''.$memCd1.'\'
			,      t01_yoyangsa_id2		= \''.$memCd2.'\'
			,      t01_yname1			= \''.$_POST['memNm1'].'\'
			,      t01_yname2			= \''.$_POST['memNm2'].'\'
			,      t01_conf_suga_code	= \'\'
			,      t01_conf_suga_value	= \'\'
			,      t01_yname5			= \''.$lsExtraKind.'\'
			,      t01_ysudang_yn		= \''.$ynExtra.'\'
			,      t01_ysudang			= \''.$sudangPay.'\'
			,      t01_ysudang_yul1		= \''.$liExtraRate1.'\'
			,      t01_ysudang_yul2		= \''.$liExtraRate2.'\'
			,      t01_yoyangsa_id3		= \''.$liExtraAmt1.'\'
			,      t01_yoyangsa_id4		= \''.$liExtraAmt2.'\'
			,      t01_status_gbn		= \'9\'
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