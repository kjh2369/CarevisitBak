<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	$connSeq = $_POST['connSeq'];

	$sql = 'SELECT	conn_orgno
			,		conn_orgnm
			,		per_nm
			,		per_jumin
			,		req_dt
			,		reqor_nm
			,		reqor_rel
			,		req_rsn
			,		req_text
			FROM	hce_svc_connect
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		conn_seq= \''.$connSeq.'\'
			AND		del_flag= \'N\'';

	$row = $conn->get_array($sql);

	if ($row){
		$data .= 'orgNo='	.$row['conn_orgno'];
		$data .= '&orgNm='	.$row['conn_orgnm'];
		$data .= '&perNm='	.$row['per_nm'];
		$data .= '&perJumin='.$ed->en($row['per_jumin']);
		$data .= '&date='	.$row['req_dt'];
		$data .= '&name='	.$row['reqor_nm'];
		$data .= '&rel='	.$row['reqor_rel'];
		$data .= '&rsn='	.StripSlashes($row['req_rsn']);
		$data .= '&text='	.StripSlashes($row['req_text']);
	}

	Unset($row);

	echo $data;

	include_once('../inc/_db_close.php');
?>