<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$code		= $_POST['code'];
	$board_type	= $_POST['board_type'];
	$board_id	= $_POST['board_id'];
	$board_seq	= $_POST['board_seq'];
	$page		= $_POST['page'];
	$mode		= $_POST['mode'];

	$conn->begin();
	
	//리플 카운트
	$sql = "select ifnull(max(reply_count), 0) - 1
			  from tbl_board
			 where board_center = '$code'
			   and board_type   = '$board_type'
			   and board_id     = '$board_id'";
	$reply_cnt = $conn->get_data($sql);

	//리플카운트수정
	$sql = "update tbl_board
			   set reply_count  = '$reply_cnt'
			 where board_center = '$code'
			   and board_type   = '$board_type'
			   and board_id     = '$board_id'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
		exit;
	}
	
	// 리플삭제
	$sql = "delete
			  from tbl_board_reply
			 where board_center = '$code'
			   and board_type   = '$board_type'
			   and board_id     = '$board_id'
			   and board_seq    = '$board_seq'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
		exit;
	}

	$conn->commit();
?>
<script>
	location.replace('board_reg.php?code=<?=$code?>&board_type=<?=$board_type;?>&board_id=<?=$board_id?>&mode=2&page=1');
</script>