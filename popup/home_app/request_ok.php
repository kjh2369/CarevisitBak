<?
	include_once('../../inc/_db_open.php');
	include_once('../../inc/_login.php');
	include_once('../../inc/_myFun.php');

	$code		= $_SESSION['userCenterCode'];
	$BoardPay	= str_replace(',','',$_POST['txtBoardPay']);

	$sql = 'SELECT	IFNULL(MAX(seq),0)+1
			FROM	homepage_request
			WHERE	org_no = \''.$code.'\'';

	$Seq = $conn->get_data($sql);
	
	$sql = 'INSERT INTO homepage_request(
			 org_no
			,seq
			,pay
			,hp_gbn
			,insert_id
			,insert_dt) VALUES (
			 \''.$code.'\'
			,\''.$Seq.'\'
			,\''.$BoardPay.'\'
			,\'new\'
			,\''.$_SESSION['userCode'].'\'
			,NOW()
			)';
	
	$conn -> begin();

	if (!$conn->execute($sql)){
		 $conn->close();
		 lfErr();
		 exit;
	}
	
	$conn -> commit();

	include_once('../../inc/_db_close.php');

	echo '
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script type="text/javascript">
			alert("신청을 완료하엿습니다.\n 입금 완료하시면 확인 후 연락드리겠습니다.");
			self.close();
		</script>';

	function lfErr(){
		echo '
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<script type="text/javascript">
				alert("데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.");
				location.href = "./labor_contract.php";
			</script>';
	}
?>