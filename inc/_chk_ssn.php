<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_login.php');

	$id   = $_REQUEST['id'];
	$code = $_REQUEST['code'];
	$ssn  = $myF->utf($_REQUEST['ssn']);

	switch($id){
		case 10: //기관 로그인 아이디
			$tbl = 'm97user';
			$col = 'm97_id';
			$org = '';
			$del = '';
			$ret = '';
			$oth = 'or m97_id = \''.$ssn.'\'';
			break;
		case 100: //직원 초기상담기록지
			$tbl = 'counsel_mem';
			$col = 'mem_ssn';
			$org = 'org_no';
			$del = 'del_flag';
			$ret = '';
			$oth = '';
			break;
		case 110: //직원 주민확인
			$tbl = 'm02yoyangsa';
			$col = 'm02_yjumin';
			$org = 'm02_ccode';
			$del = '';
			$ret = '';
			$oth = '';
			break;
		case 130: //직원 사번확인
			$tbl = 'm02yoyangsa';
			$col = 'm02_mem_no';
			$org = 'm02_ccode';
			$del = '';
			$ret = '';
			$oth = '';
			break;
		case 140: //고객 관리번호
			$tbl = 'm03sugupja';
			$col = 'm03_client_no';
			$org = 'm03_ccode';
			$del = '';
			$ret = '';
			$oth = '';
			break;
		case 210: //고객정보등록
			$tbl = 'counsel_client';
			$col = 'client_ssn';
			$org = 'org_no';
			$del = '';
			$ret = '';
			$oth = '';
			break;
		case 220: //고객정보등록
			$tbl = 'm03sugupja';
			$col = 'm03_jumin';
			$org = 'm03_ccode';
			$del = '';
			$ret = '';
			$oth = '';
			break;
		case 510: //본사 담당자 아이디 중복 여부
			$tbl = 'b01person';
			$col = 'b01_id';
			$org = 'b01_branch';
			$del = '';
			$ret = '';
			$oth = '';
			break;
		case 610: //본사 담당자 아이디 중복 여부
			$tbl = 'b01person';
			$col = 'b01_id';
			$org = '';
			$del = '';
			$ret = '';
			$oth = '';
			break;
		default:
			exit;
	}

	$sql = "select count(*)
			  from $tbl
			 where $col = '$ssn'";

	if ($org != ''){
		$sql .= " and $org = '$code'";
	}

	if ($del != ''){
		$sql .= " and $del = 'N'";
	}

	if ($oth != ''){
		$sql .= " $oth ";
	}

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$rst = 'Y';
	}else{
		$rst = 'N';
	}

	if ($ret == ''){
		echo $rst;
	}else{
		return $rst;
	}

	include_once('../inc/_db_close.php');
?>