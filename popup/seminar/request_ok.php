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

	if($BoardSeq == ''){

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
				,\'2\'
				,\''.$BoardGbn.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
		
	}else {
		$sql = 'UPDATE seminar_request
				   SET org_nm		= \''.$orgNm.'\'
				,	   name			= \''.$BoardName.'\'
				,	   rank			= \''.$BoardRank.'\'
				,	   rank2		= \''.$BoardRank2.'\'
				,	   deposit_pay  = \''.$BoardPay.'\'
				,	   pos			= \''.$BoardPos.'\'
				,	   gbn			= \'2\'
				,	   type			= \''.$BoardGbn.'\'
				 WHERE org_no		= \''.$code.'\'
				   AND seq			= \''.$BoardSeq.'\'';

	}
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