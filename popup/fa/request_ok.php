<?
	include_once('../../inc/_db_open.php');
	include_once('../../inc/_login.php');
	include_once('../../inc/_myFun.php');

	$code		= $_SESSION['userCenterCode'];
	$orgNm		= $_SESSION['userCenterName'];
	$BoardRank	= $_POST['BoardRank'];
	$BoardRank2	= $_POST['BoardRank2'];
	$BoardName	= $_POST['BoardName'];
	$BoardSeq	= $_POST['txtBoardSeq'];
	$BoardPay	= str_replace(',','',$_POST['txtBoardPay']);
	$BoardPos	= $_POST['BoardPos'];
	$BoardGbn	= $_POST['BoardGbn'];
	
	$sql = 'select m00_mname, m00_ctel, m00_caddr1
			from   m00center
			where  m00_mcode = \''.$code.'\'';
	$org = $conn -> get_array($sql);
	
	$orgMName = $org['m00_mname'];
	$orgTel = $org['m00_ctel'];
	$orgAddr = $org['m00_caddr1'];

	$sql = 'SELECT	IFNULL(MAX(seq),0)+1
			FROM	seminar_request
			WHERE	org_no = \''.$code.'\'';

	$Seq = $conn->get_data($sql);
	
	$sql = 'INSERT INTO seminar_request(
			 org_no
			,seq
			,org_nm
			,name
			,rank
			,rank2
			,deposit_pay
			,pos
			,gbn
			,type
			,insert_id
			,insert_dt
			) VALUES (
			 \''.$code.'\'
			,\''.$Seq.'\'
			,\''.$orgNm.'\'
			,\''.$BoardName.'\'
			,\''.$BoardRank.'\'
			,\''.$BoardRank2.'\'
			,\''.$BoardPay.'\'
			,\''.$BoardPos.'\'
			,\'9\'
			,\''.$BoardGbn.'\'
			,\''.$_SESSION['userCode'].'\'
			,NOW()
			)';

	$conn -> begin();

	if (!$conn->execute($sql)){
		 $conn->close();
		 echo 9;
		 exit;
	}
	
	$conn -> commit();
	$conn->close();
	
	$conn = new connection('115.68.110.24', 'npro', 'npro', 'npro');

	$conn->begin();
	
	$toTal = '01037575043';
	$fromTel = '0269529253';

	$text = '재무회계 신청기관\n'.$orgNm.'/'.$orgMName.'/'.$myF->phoneStyle($orgTel).'/'.$orgAddr.'';

	$sql = 'INSERT INTO msg_data (CUR_STATE,CALL_TO,CALL_FROM,SMS_TXT,MSG_TYPE) VALUES (
			 0
			,\''.$toTal.'\'
			,\''.$fromTel.'\'
			,\''.$text.'\'
			,4)';

	if (!$conn->execute($sql)){		 
		 echo 9;
		 exit;
	}

	$conn->commit();
	$conn->close();
	

	echo '1';

?>