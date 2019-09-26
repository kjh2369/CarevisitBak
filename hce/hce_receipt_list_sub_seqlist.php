<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$sr		= $_POST['SR'];
	$IPIN	= $_POST['IPIN'];

	//접수방법
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'CT\'';

	$rctGbn = $conn->_fetch_array($sql,'code');

	//카운트
	$sql = 'SELECT	COUNT(*)
			FROM	hce_receipt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$sr.'\'
			AND		IPIN	= \''.$IPIN.'\'';

	$cnt = $conn->get_data($sql);

	$sql = 'SELECT	rcpt_seq
			,		rcpt_dt
			,		counsel_type
			,		hce_seq
			,		reqor_nm
			,		reqor_telno
			,		rcver_nm
			,		end_flag
			FROM	hce_receipt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$sr.'\'
			AND		IPIN	= \''.$IPIN.'\'
			AND		del_flag= \'N\'
			ORDER	BY rcpt_seq DESC
			LIMIT	1,'.($cnt-1);

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= 'seq='			.$row['rcpt_seq'];
		$data .= '&no='			.$row['hce_seq'];
		$data .= '&type='		.$rctGbn[$row['counsel_type']]['name'];
		$data .= '&date='		.$row['rcpt_dt'];
		$data .= '&reqorNm='	.$row['reqor_nm'];
		$data .= '&reqorTel='	.$row['reqor_telno'];
		$data .= '&rcverNm='	.$row['rcver_nm'];
		$data .= '&endYn='		.$row['end_flag'];
		$data .= chr(11);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>