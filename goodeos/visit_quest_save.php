<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');

	//echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$id	= $_POST['id'];
	$phone = $_POST['phone'];
	$mail = $_POST['mail'];

	if($phone == 'Y' and $mail == 'Y'){
		$gbn = 3;
	}else if($phone == 'Y'){
		$gbn = 1;
	}else if($mail == 'Y'){
		$gbn = 2;
	}else {
		$gbn = '';
	}

	$sql = "update counsel
			   set c_answer_gbn = '$gbn'
			 where c_id	= '$id'";
	if($conn->execute($sql)){
		$rst = 'Y';
	}else{
		$rst = 'N';
	}

	include_once('../inc/_db_close.php');
?>