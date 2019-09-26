<?
	include_once('../inc/_db_open.php');
	//include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$conn->close();

	//$conn = new connection('58.229.121.69', 'npro', 'npro1234', 'npro');
	$conn = new connection('115.68.110.24', 'npro', 'npro', 'npro');
	
	//$company= $_POST['company'];
	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	
	$month	= ($month < 10 ? '0' : '').$month;
	$yymm	= '201901';
	
	$sql = 'SELECT 	MSG_SEQ, CALL_FROM, CALL_TO, RSLT_CODE, RSLT_CODE2
			FROM    msg_log_'.$yymm.'
			WHERE   RSLT_CODE = \'410\'';
	
	$msgLog = $conn -> _fetch_array($sql);
	
	$conn->close();
	$conn = new connection();
		
	foreach($msgLog as $m => $R){
		
		$sql = 'SELECT count(*)
				FROM sms_send_fail_log
				WHERE call_seq = \''.$R['MSG_SEQ'].'\'';
		
		$cnt = $conn -> get_data($sql);		
		
		if($cnt == 0){	
			$sql = 'INSERT INTO sms_send_fail_log (call_seq, call_from, call_to, call_rst_cd, call_rst_cd_dtl, insert_dt) VALUES (\''.$R['MSG_SEQ'].'\', \''.$R['CALL_FROM'].'\', \''.$R['CALL_TO'].'\', \''.$R['RSLT_CODE'].'\', \''.$R['RSLT_CODE2'].'\',now());';
			
			$query[] = $sql;
		}

		/*
		$sql = 'UPDATE sms_send_log
				SET    call_from = \''.$R['CALL_FROM'].'\'
				,	   call_to = \''.$R['CALL_TO'].'\'
				,	   call_rst_cd = \''.$R['RSLT_CODE'].'\'	
				,	   call_rst_cd_dtl = \''.$R['RSLT_CODE2'].'\'
				,	   update_dt = now()
				WHERE  call_seq = \''.$R['MSG_SEQ'].'\'';
		
		$query[] = $sql;
		*/
	}

	if (is_array($query)){
		
		$conn->begin();

		foreach($query as $sql){
	
			if(!$conn->execute($sql)){
				echo nl2br($sql).'</br>'; 
				exit;
			}
		}

		$conn->commit();

		
		$lsPara = 1;
	}
			

	include_once('../inc/_db_close.php');
?>