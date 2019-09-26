<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	// 공통변수
		$code = $_POST['code'];
		$kind = $_POST['kind'];
		$ssn  = $ed->de($_POST['ssn']);
		$year = $_POST['year'];
		$month= $_POST['month'];
		$page = $_POST['page'];
		$yymm = $year.$month;

	// 모드
	// 1 : 등록, 2 : 수정
		$write_mode = $_POST['write_mode'];

	// 입력정보
		$reg_cd = $_SESSION['userCode'];
		$reg_dt = date('Y-m-d', mktime());

	$desire_1 = addslashes($_POST['back_desire_1']); // 현상/욕구평가
	$desire_2 = addslashes($_POST['back_desire_2']); // 표준장기요양이용계획
	$desire_3 = addslashes($_POST['back_desire_3']); // 급여제공계획
	$subject1 = $_POST['desire_subject1'];	//표준장기요양이용계획(목표)
	$subject2 = $_POST['desire_subject2'];	//현상/욕구평가(욕구)
	$subject3 = $_POST['desire_subject3'];	//급여제공계획(목표)

	$conn->begin();

	if ($write_mode == 1){
		$sql = "insert into counsel_client_desire (
				 org_no
				,desire_ssn
				,desire_yymm) values (
				 '$code'
				,'$ssn'
				,'$yymm')";

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $myF->message('error', 'Y', 'Y');
			echo nl2br($sql);
			exit;
		}
	}

	$sql = "update counsel_client_desire
			   set desire_status  = '$desire_1'
			,      desire_content = '$desire_2'
			,      desire_service = '$desire_3'
			,      desire_subject1 = '$subject1'
			,      desire_subject2 = '$subject2'
			,      desire_subject3 = '$subject3'";

	if ($write_mode == 1){
		$sql .= "
			,      insert_id      = '$reg_cd'
			,      insert_dt      = '$reg_dt'";
	}else{
		$sql .= "
			,      update_id      = '$reg_cd'
			,      update_dt      = '$reg_dt'";
	}

	$sql .= "
			 where org_no      = '$code'
			   and desire_ssn  = '$ssn'
			   and desire_yymm = '$yymm'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		//echo nl2br($sql);
		exit;
	}

	$conn->commit();

	include_once("../inc/_db_close.php");
?>
<script>
	alert('<?=$myF->message("ok","N");?>');
	location.replace('client_desire_reg.php?code=<?=$code;?>&kind=<?=$kind;?>&page=<?=$page;?>&ssn=<?=$ed->en($ssn);?>&year=<?=$year;?>&month=<?=$month;?>');
</script>