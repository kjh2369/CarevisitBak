<?
/*
* 아이디중복체크
*/
	include_once('../inc/_db_open.php');
	
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$user_id = $_POST['user_id'];
	$code = $_POST['mCode'];
	
	$sql = "select count(*)
			  from member
			 where org_no = '".$code."'
			   and code = '".$user_id."'";
	$idCount = $conn -> get_data($sql);

	if($idCount[0] > 0){ ?>
		<script language='javascript'>  
			alert("이미 사용중인 아이디입니다.");
			parent.document.f.user_id.select();
		</script>
	<?
		return true;
	}

	include_once('../inc/_db_close.php');
?>
