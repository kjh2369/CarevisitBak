<?
	include_once('../../inc/_db_open.php');
	include_once('../../inc/_login.php');
	include_once('../../inc/_myFun.php');

	$orgNo		= $_SESSION['userCenterCode'];
	$orgNm		= $_SESSION['userCenterName'];
	$optArea	= $_POST['optArea'];
	$txtCont	= addslashes($_POST['txtCont']);
	$mode       = $_POST['mode'];
	$seq        = $_POST['seq'];
	
	$sql = 'SELECT	count(*)
			FROM	medical_request
			WHERE	org_no = \''.$orgNo.'\'
			  AND   seq    = \''.$seq.'\'
			  AND   del_flag = \'N\'';
	$liCnt = $conn -> get_data($sql);
	
	if ($liCnt > 0){
		$new = false;
	}else{
		$new = true;
	}

	if($new){

		$sql = 'SELECT	IFNULL(MAX(seq),0)+1
				FROM	medical_request
				WHERE	org_no = \''.$orgNo.'\'';

		$seq = $conn->get_data($sql);
		
		
		$sql = 'INSERT INTO medical_request(
				 org_no
				,seq
				,request_area
				,request_cont
				,insert_dt
				,insert_id) VALUES (
				 \''.$orgNo.'\'
				,\''.$seq.'\'
				,\''.$optArea.'\'
				,\''.$txtCont.'\'
				,NOW()
				,\''.$_SESSION['userCode'].'\'
				)';
		
	}else {
		
		if($mode == 'del'){
		
			$sql = 'UPDATE medical_request
					   SET cancel_yn	= \'Y\'
					 WHERE org_no		= \''.$orgNo.'\'
					   AND seq			= \''.$seq.'\'';
		}else {
			$sql = 'UPDATE medical_request
					   SET request_area		= \''.$optArea.'\'
					,	   request_cont		= \''.$txtCont.'\'
					,	   update_dt		= NOW()
					,	   update_id		= \''.$_SESSION['userCode'].'\'
					 WHERE org_no			= \''.$orgNo.'\'
					   AND seq				= \''.$seq.'\'';
		}

	}

	$conn -> begin();

	if (!$conn->execute($sql)){ 
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn -> commit();

	echo 1; 

?>