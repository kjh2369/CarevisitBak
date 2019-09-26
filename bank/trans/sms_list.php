<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if (!IsSet($_SESSION['USER_CODE'])){
		exit;
	}

	$bankCD = '003';

	$sql = 'SELECT admin_tel
			  FROM bank_config
			 WHERE bank_cd = \''.$bankCD.'\'';
	$tel = $conn->get_data($sql);

	$sql = 'SELECT org_no AS cd
			,      m00_store_nm AS nm
			,      COUNT(org_no) AS cnt
			,      m00_ctel AS tel
			  FROM trans
			 INNER JOIN m00center AS mst
				ON m00_mcode = org_no
			   AND m00_mkind = \'0\'
			 WHERE stat   = \'1\'
			   AND sms_yn = \'N\'
			 GROUP BY org_no, m00_store_nm, m00_ctel
			 ORDER BY nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	$conn->begin();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$msg = $row['nm'].'에서 '.$row['cnt'].'건의 이체요청이 있습니다.';

		$sql = 'UPDATE trans
				   SET sms_yn = \'Y\'
				 WHERE org_no = \''.$row['cd'].'\'
				   AND stat   = \'1\'
				   AND sms_yn = \'N\'';
		$conn->execute($sql);

		$sql = 'INSERT INTO sms_'.date('Ym').' (
				 org_no
				,gbn
				,call_from
				,call_to
				,call_msg
				,insert_dt) VALUES (
				 \'BANK_'.$bankCD.'\'
				,\'N\'
				,\''.$row['tel'].'\'
				,\''.$tel.'\'
				,\''.$msg.'\'
				,now()
				)';
		$conn->execute($sql);
	}

	$conn->commit();

	$sms = new connection('58.229.121.69', 'npro', 'npro1234', 'npro');

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$msg = $row['nm'].'에서 '.$row['cnt'].'건의 이체요청이 있습니다.';

		$sql = 'INSERT INTO msg_data (
				 CUR_STATE
				,CALL_TO
				,CALL_FROM
				,SMS_TXT
				,MSG_TYPE) VALUES (
				 0
				,\''.$tel.'\'
				,\''.$row['tel'].'\'
				,\''.$msg.'\'
				,4);';
		$sms->execute($sql);
	}

	$sms->close();

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>