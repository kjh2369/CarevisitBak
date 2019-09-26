<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$name = $_POST['c_name'];
	$phone = $_POST['c_phone'];
	$mail = $_POST['c_mail'];
	$content = $_POST['c_content'];

	// 기존의 자료를 삭제한다.
	/*
	$sql = "delete"
		 . "  from counsel"
		 . " where c_id = '".$c_id
		 . "'";
	$conn->execute($sql);
	*/

	// 자료를 저장한다.
	$sql = "insert into counsel ("
		 . " c_id"
		 . ",c_dt"
		 . ",c_name"
		 . ",c_phone"
		 . ",c_mail"
		 . ",c_content"
		 . ",c_domain_id"
		 . ") values ("
		 . "null"
		 . " ,now()"
		 . " ,'".$name
		 . "','".str_replace('-', '', $phone)
		 . "','".$mail
		 . "','".addSlashes($content)
		 . "','".$gDomainID
		 . "')";

	$conn->execute($sql);

	echo "<script>
			alert('입력이 완료되었습니다. 감사합니다.');
			window.close();
		  </script>";

	include_once('../inc/_db_close.php');

	?>
