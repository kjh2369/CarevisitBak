<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$board_type	= $_POST['board_type'];
	$board_seq	= $_POST['board_seq'];
	$page		= $_POST['page'];
	$mode		= $_POST['mode'];

	$conn->begin();
	
	// 리플삭제
	$sql = "delete
			  from news_reply
			 where news_gbn    = '$board_type'
			   and news_seq    = '$board_seq'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
		exit;
	}

	// 본문삭제
	$sql = "delete
			  from news_list
			 where news_gbn   = '$board_type'
			   and news_seq     = '$board_seq'";
	if (!$conn->execute($sql)){
		$conn->rollback();
		echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
		exit;
	}

	$conn->commit();
?>
<script>
	if ('<?=$mode;?>' == '3'){
		location.replace('board_reg.php?board_type=<?=$board_type?>&board_seq=<?=$board_seq?>&page=<?=$page?>&mode=2');
	}else{
		location.replace('board_list.php?board_type=<?=$board_type;?>&page=1');
	}
</script>