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
	$liExtraRate1 = 0;
	$liExtraRate2 = 0;
	$liExtraAmt1  = 0;
	$liExtraAmt2  = 0;

	/*
	if ($svcKind == '500'){
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
			   set t01_yname5          = \''.$lsExtraKind.'\'
			,      t01_ysudang_yn      = \''.$ynExtra.'\'
			,      t01_ysudang         = \''.$sudangPay.'\'
			,      t01_ysudang_yul1    = \''.$liExtraRate1.'\'
			,      t01_ysudang_yul2    = \''.$liExtraRate2.'\'
			,      t01_yoyangsa_id3    = \''.$liExtraAmt1.'\'
			,      t01_yoyangsa_id4    = \''.$liExtraAmt2.'\'
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