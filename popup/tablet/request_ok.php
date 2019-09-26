<?
	include_once('../../inc/_db_open.php');
	include_once('../../inc/_login.php');
	include_once('../../inc/_myFun.php');

	$code		= $_SESSION['userCenterCode'];
	$BoardRank	= $_POST['BoardRank'];
	$BoardPay	= str_replace(',','',$_POST['txtBoardPay']);

	$sql = 'SELECT	IFNULL(MAX(seq),0)+1
			FROM	tablet_request
			WHERE	org_no = \''.$code.'\'';

	$Seq = $conn->get_data($sql);
	
	$sql = 'INSERT INTO tablet_request(
			 org_no
			,seq
			,rank
			,pay
			,cnt
			,insert_id
			,insert_dt) VALUES (
			 \''.$code.'\'
			,\''.$Seq.'\'
			,\''.$BoardRank.'\'
			,\''.$BoardPay.'\'
			,\'2\'
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
			alert("정상적으로 처리되었습니다.");
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