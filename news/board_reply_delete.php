<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$board_type	= $_POST['board_type'];
	$board_seq	= $_POST['board_seq'];
	$reply_no	= $_POST['reply_no'];
	$page		= $_POST['page'];
	$mode		= $_POST['mode'];

	$conn->begin();
	
	// 리플삭제
	$sql = "delete
			  from news_reply
			 where news_gbn    = '$board_type'
			   and news_seq    = '$board_seq'
			   and reply_no    = '$reply_no'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
		exit;
	}

	$conn->commit();
?>
<script>
	location.replace('board_reg.php?code=<?=$code?>&board_type=<?=$board_type;?>&board_seq=<?=$board_seq?>&mode=2&page=1');
</script>