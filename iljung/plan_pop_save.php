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
	$from    = $_POST['from'];
	$seq     = $_POST['seq'];
	$svcKind = $_POST['svcKind'];

	$confDt       = trim($_POST['confDt']);
	$confFrom     = trim($_POST['confFrom']);
	$confTo       = trim($_POST['confTo']);
	$confProctime = trim($_POST['confProctime']);
	$confMemCd1   = trim($_POST['confMemCd1']);
	$confMemNm1   = trim($_POST['confMemNm1']);
	$confMemCd2   = trim($_POST['confMemCd2']);
	$confMemNm2   = trim($_POST['confMemNm2']);
	$confSugaCd   = trim($_POST['confSugaCd']);
	$confSugaCost = trim($_POST['confSugaCost']);

	$sudangPay  = $_POST['sudangPay'];
	$sudangKind = $_POST['sudangKind'];
	$sudangVal1 = $_POST['sudangVal1'];
	$sudangVal2 = $_POST['sudangVal2'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);
	if (!is_numeric($confMemCd1)) $confMemCd1 = $ed->de($confMemCd1);
	if (!is_numeric($confMemCd2)) $confMemCd2 = $ed->de($confMemCd2);

	if ($svcKind != '200'){
		$ynExtra = 'Y';
	}else{
		$ynExtra = 'N';
	}

	$lsExtraKind  = 'N';
	#$liExtraRate1 = 0;
	#$liExtraRate2 = 0;
	#$liExtraAmt1  = 0;
	#$liExtraAmt2  = 0;

	/*
	switch($sudangKind){
		case 'rate':
			$lsExtraKind  = 'R';
			$liExtraRate1 = $sudangVal1;
			$liExtraRate2 = $sudangVal2;
			$liExtraAmt1  = $sudangPay*0.5;
			$liExtraAmt2  = $sudangPay*0.5;
			break;

		case 'amt':
			$lsExtraKind  = 'P';
			$liExtraRate1 = 50;
			$liExtraRate2 = 50;
			$liExtraAmt1  = $sudangVal1;
			$liExtraAmt2  = $sudangVal2;
			break;
	}
	*/
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

	$conn->begin();

	$sql = 'update t01iljung
			   set t01_conf_date       = \''.$confDt.'\'
			,      t01_conf_fmtime     = \''.$confFrom.'\'
			,      t01_conf_totime     = \''.$confTo.'\'
			,      t01_conf_soyotime   = \''.$confProctime.'\'
			,      t01_yoyangsa_id1    = \''.$confMemCd1.'\'
			,      t01_yoyangsa_id2    = \''.$confMemCd2.'\'
			,      t01_yname1          = \''.$confMemNm1.'\'
			,      t01_yname2          = \''.$confMemNm2.'\'
			,      t01_conf_suga_code  = \''.$confSugaCd.'\'
			,      t01_conf_suga_value = \''.$confSugaCost.'\'
			,      t01_yname5          = \''.$lsExtraKind.'\'
			,      t01_ysudang_yn      = \''.$ynExtra.'\'
			,      t01_ysudang         = \''.$sudangPay.'\'
			,      t01_ysudang_yul1    = \''.$liExtraRate1.'\'
			,      t01_ysudang_yul2    = \''.$liExtraRate2.'\'
			,      t01_yoyangsa_id3    = \''.$liExtraAmt1.'\'
			,      t01_yoyangsa_id4    = \''.$liExtraAmt2.'\'
			,      t01_status_gbn      = \'1\'
			 where t01_ccode           = \''.$code.'\'
			   and t01_mkind           = \''.$svcCd.'\'
			   and t01_jumin           = \''.$jumin.'\'
			   and t01_sugup_date      = \''.$date.'\'
			   and t01_sugup_fmtime    = \''.$from.'\'
			   and t01_sugup_seq       = \''.$seq.'\'';

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