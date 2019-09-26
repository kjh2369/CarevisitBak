<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	$code   = $_POST['code'];
	$kind   = $_POST['code'];
	$from_y = $_POST['year_from'];
	$from_m = ($_POST['month_from'] < 10 ? '0' : '').$_POST['month_from'];
	$to_y   = $_POST['year_to'];
	$to_m   = ($_POST['month_to'] < 10 ? '0' : '').$_POST['month_to'];
	$writer = $_SESSION['userCode'];
	$today  = date('Y-m-d', mktime());

	$conn->begin();

	$sql = 'select desire_ssn as ssn
			,      desire_status as stat
			,      desire_content as cont
			,      desire_service as svc
			,	   desire_subject1 as subject1
			,	   desire_subject2 as subject2
			,	   desire_subject3 as subject3
			  from counsel_client_desire
			 where org_no      = \''.$code.'\'
			   and desire_yymm = \''.$from_y.$from_m.'\'';

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	$sql = "insert into counsel_client_desire (org_no, desire_ssn, desire_yymm, desire_status, desire_content, desire_service, desire_subject1, desire_subject2,  desire_subject3, insert_id, insert_dt) values ";
	$sl  = "";

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$sl = 'SELECT	COUNT(*)
				FROM	counsel_client_desire
				WHERE	org_no		= \''.$code.'\'
				AND		desire_ssn	= \''.$row['ssn'].'\'
				AND		desire_yymm	= \''.$to_y.$to_m.'\'';

		$cnt = $conn->get_data($sl);

		if ($cnt < 1){
			//$sl .= ($sl != "" ? "," : "");
			$sl  = $sql;
			$sl .= "('".$code."'
					,'".$row['ssn']."'
					,'".$to_y.$to_m."'
					,'".$row['stat']."'
					,'".$row['cont']."'
					,'".$row['svc']."'
					,'".$row['subject1']."'
					,'".$row['subject2']."'
					,'".$row['subject3']."'
					,'".$writer."'
					,'".$today."')";

			if (!$conn->execute($sl)){
				 $conn->rollback();
				 echo 'N';
				 exit;
			}
		}
	}

	//$sql .= $sl;

	$conn->row_free();
	$conn->commit();
	echo 'Y';

	/*
	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();
		$rst = 'Y';
	}else{
		$conn->rollback();
		$rst = 'N';
	}

	echo $rst;
	*/

	include_once("../inc/_db_close.php");
?>