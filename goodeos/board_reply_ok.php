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

	$reg_id		= $_SESSION['userCode'];
	$reg_name	= $_SESSION['userName'];
	$reg_date	= date('Y-m-d', mktime());
	$reg_time	= date('H:i:s', mktime());

	$reply   = addslashes($_POST['reply']);
	$reply_s = addslashes($_POST['reply_'.$board_seq]);

	$conn->begin();

	if ($board_seq == 0){
		// 다음 아이디
		$sql = "select ifnull(max(board_seq), 0) + 1
				  from tbl_board_reply
				 where board_center = '$code'
				   and board_type   = '$board_type'
				   and board_id     = '$board_id'";
		$board_seq = $conn->get_data($sql);

		$sql = "insert into tbl_board_reply (board_center, board_type, board_id, board_seq, reg_id, reg_name, reg_date, reg_time, content) values (
				 '$code'
				,'$board_type'
				,'$board_id'
				,'$board_seq'
				,'$reg_id'
				,'$reg_name'
				,'$reg_date'
				,'$reg_time'
				,'$reply')";

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
			exit;
		}

		$sql = "update tbl_board
				   set reply_count = (select count(*)
										from tbl_board_reply
									   where tbl_board_reply.board_center = tbl_board.board_center
									     and tbl_board_reply.board_type   = tbl_board.board_type
										 and tbl_board_reply.board_id     = tbl_board.board_id)
				 where board_center = '$code'
				   and board_type   = '$board_type'
				   and board_id     = '$board_id'";

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
			exit;
		}
	}else{
		$sql = "update tbl_board_reply
				   set content = '$reply_s'
				 where board_center = '$code'
				   and board_type   = '$board_type'
				   and board_id     = '$board_id'
				   and board_seq    = '$board_seq'";

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
			exit;
		}
	}

	$conn->commit();
?>
<script>
	alert('<?=$myF->message("ok","N");?>');
	location.replace('board_reg.php?code=<?=$code;?>&board_type=<?=$board_type?>&board_id=<?=$board_id?>&page=<?=$page?>&mode=2');
</script>