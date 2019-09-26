<?
	session_start();

	$level = $_SESSION['userLevel'];
	$code  = $_SESSION['userCenterCode'];

	session_unset();
	session_destroy();
?>
<script type="text/javascript">
	if (!parent.opener){
		location.href = '../index.html';
	}else{
		top.close();
	}
</script>