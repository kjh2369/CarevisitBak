<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_ed.php');

	$orgNo		= $_SESSION['userCenterCode'];
	$jumin		= $ed->de($_POST['jumin']);
	$memNm1		= $_POST['memNm1'];
	$memCd1		= $ed->de($_POST['memCd1']);
	$familyYn1	= $_POST['familyYn1'];
	$familyRel1	= $_POST['familyRel1'];
	$memNm2		= $_POST['memNm2'];
	$memCd2		= $ed->de($_POST['memCd2']);
	$familyYn2	= $_POST['familyYn2'];
	$familyRel2	= $_POST['familyRel2'];
	$from		= $_POST['from'];
	$to			= $_POST['to'];
	$subCd		= $_POST['subCd'];
	$sugaCd		= $_POST['sugaCd'];
	$planDate	= $_POST['planDate'];
	$planTime	= $_POST['planTime'];
	$planSeq	= $_POST['planSeq'];

	/*
	$tmpSuga = $mySuga->findSugaCare($code
									,$svc_list[$s]['svc_cd']
									,$svc_list[$s]['date']
									,$svc_list[$s]['conf_from']
									,$svc_list[$s]['conf_to']
									,$svc_list[$s]['family_yn']
									,$svc_list[$s]['bath_kind']);

	$svc_list[$s]['suga_if'] = array('code'			=>$tmpSuga['code']
									,'name'			=>$tmpSuga['name']
									,'cost'			=>$tmpSuga['cost']
									,'evening_cost'	=>$tmpSuga['costEvening']
									,'night_cost'	=>$tmpSuga['costNight']
									,'total_cost'	=>$tmpSuga['costTotal']
									,'sudang_pay'	=>$tmpSuga['sudangPay']
									,'evening_time'	=>$tmpSuga['timeEvening']
									,'night_time'	=>$tmpSuga['timeNight']
									,'evening_yn'	=>$tmpSuga['ynEvening']
									,'night_yn'		=>$tmpSuga['ynNight']
									,'holiday_yn'	=>$tmpSuga['ynHoliday']);
	 */

	//B2300000 차량 60분 - CBKD1
	//B2380000 차랑 40분~60분미만 - CBKD1
	//B2400000 가정내 60분 - CBKD2
	//B2480000 가정내 40분~60분미만 - CBKD2
	//B2500000 미차랑 60분 - CBFD1
	//B2580000 미차랑 40분~60분미만 - CBFD1

	if ($subCd == '500'){
		if (SubStr($sugaCd,0,3) == 'B23'){
			$bathkind = '1';
		}else if (SubStr($sugaCd,0,3) == 'B24'){
			$bathkind = '2';
		}else{
			$bathkind = '3';
		}
	}else{
		$bathkind = '';
	}

	//수가
	$suga = $mySuga->findSugaCare(
				$orgNo,
				$subCd,
				$planDate,
				$from,
				$to,
				$familyYn1,
				$bathkind);

	if ($planTime && $planSeq){
		//수정
	}else{
		//신규
	}

	include_once('../inc/_db_close.php');
?>