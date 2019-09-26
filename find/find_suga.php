<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_ed.php');

	$code		= $_SESSION['userCenterCode'];//$_POST['code'];
	$svcCd		= $_POST['svcCd'];
	$svcKind	= $_POST['svcKind'];
	$date		= $_POST['date'];
	$fromTime	= $_POST['fromTime'];
	$toTime		= $_POST['toTime'];
	$ynFamily	= $_POST['ynFamily'];
	$bathKind	= $_POST['bathKind'];
	$svcVal		= $_POST['svcVal'];
	$svcLvl		= $_POST['svcLvl'];
	$memCnt		= $_POST['memCnt'];

	if ($svcCd == '0'){
		$loSuga = $mySuga->findSugaCare($code, $svcKind, $date, $fromTime, $toTime, $ynFamily, $bathKind, true, $svcLvl);
	}else if ($svcCd == '1' || $svcCd == '2' || $svcCd == '3'){
		$loSuga = $mySuga->findSugaVoucher($code, $svcCd, $date, $fromTime, $toTime, $svcVal, $svcLvl);
	}else if ($svcCd == '4'){
		$loSuga = $mySuga->findSugaDis($code, $svcKind, $date, $fromTime, $toTime, $svcVal, $svcLvl, $bathKind, $memCnt);
	}else if ($svcCd == '5'){
		//주야간보호
		$loSuga = $mySuga->findSugaDayNight($date, $fromTime, $toTime, $svcLvl);
	}else{
		$loSuga = $mySuga->findSugaOther($code, $svcCd, $date, $fromTime, $toTime);
	}

	if (is_array($loSuga)){
		foreach($loSuga as $i => $val){
			$str .= (!empty($str) ? '&' : '').$i.'='.$val;
		}
	}

	echo $str;

	include_once('../inc/_db_close.php');
?>