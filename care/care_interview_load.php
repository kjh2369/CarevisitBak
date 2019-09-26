<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$SR = $_POST['SR'];
	$key = $_POST['key'];

	$sql = 'SELECT	*
			FROM	hce_interview
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		IPIN	= \''.$key.'\'
			AND		rcpt_seq= \'-1\'';

	$row = $conn->get_array($sql);

	//사업대상
	if ($row['income_gbn'] == '1' || $row['income_gbn'] == '2'){
		$val = $row['income_gbn'];
	}else{
		$val = '1';
	}
	$data .= 'optBizTarget='.$val;

	//동거실태
	if ($row['generation_gbn'] == '1' || $row['generation_gbn'] == '2'){
		$val = $row['generation_gbn'];
	}else{
		$val = '9';
	}
	$data .= '&optCohabit='.$val;

	//주거형태
	if ($row['dwelling_gbn'] == '1' || $row['dwelling_gbn'] == '2' || $row['dwelling_gbn'] == '3'){
		$val = $row['dwelling_gbn'];
	}else{
		$val = '9';
	}
	$data .= '&optDwelling='.$val;

	//만성질환
	$data .= '&chkDisease='.$row['disease_gbn'];

	//장기요양등급
	if ($row['longlvl_gbn'] == '1' || $row['longlvl_gbn'] == '2' || $row['longlvl_gbn'] == '3'){
		$val = '1';
	}else if ($row['longlvl_gbn'] == '4'){
		$val = 'A';
	}else{
		$val = '9';
	}
	$data .= '&optLongtermLvl='.$val;

	//신청서비스
	$data .= '&chkSvcReq='.$row['req_svc_gbn'];

	//적격여부
	$data .= '&optSvcOffer='.$row['offer_gbn'];

	//서비스사유
	$data .= '&optSvcRsnGbn='.$row['svc_rsn_gbn'];
	$data .= '&txtSvcRsnOther='.$row['svc_rsn_other'];

	//제공서비스
	$data .= '&chkSvcOff='.$row['offer_svc_gbn'];

	//의뢰인
	$data .= '&txtReqName='.$row['req_nm'];
	$data .= '&txtReqRel='.$row['req_rel'];
	$data .= '&txtReqTelno='.$row['req_telno'];

	//비고
	$data .= '&txtOther='.$row['remark'];

	Unset($row);

	echo $data;

	include_once('../inc/_db_close.php');
?>