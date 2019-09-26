<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	
	$sql = 'SELECT	cont_seq
			,		svc_nm
			,		content
			,		remark
			FROM	hce_consent_svc
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		
		$data .= 'svcCd='.StripSlashes($row['svc_nm']);
		$data .= '&cont='.StripSlashes($row['content']);
		$data .= '&other='.StripSlashes($row['remark']);
		$data .= chr(11);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>