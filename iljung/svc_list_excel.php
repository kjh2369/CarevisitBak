<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_login.php');
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$type	= $_POST['type'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);
	$appNo	= $_POST['appNo'];
	$svcGbn	= $_POST['svcGbn'];
	$year	= Date('Y',StrToTime($fromDt));
	$month	= Date('m',StrToTime($fromDt));

	if ($svcGbn){
		switch($svcGbn){
			case '200':
				$svcNm = '(방문요양)';
				break;

			case '500':
				$svcNm = '(방문목욕)';
				break;

			case '800':
				$svcNm = '(방문간호)';
				break;
		}
	}

	$IsExcel = true;

	if ($type == 'LIST'){
		$title = '서비스내역';
	}else if ($type == 'LGC'){
		$title = $year.'년 '.IntVal($month).'월 공단자료 확인(복지사업무 포함)';
	}else if ($type == 'PROVIDE'){
		$title = '장기요양급여 제공기록 내역'.$svcNm;
	}else{
		exit;
	}

	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr($title).".xls" );
?>
<div style="text-align:center; font-size:21px; font-weight:bold;"><?=$title;?></div>
<?
	if ($type == 'LIST'){
		include_once('./svc_list_excel_1.php');
	}else if ($type == 'LGC'){
		include_once('./svc_list_excel_2.php');
	}else if ($type == 'PROVIDE'){
		include_once('./svc_list_excel_3.php');
	}

	include_once("../inc/_db_close.php");
?>