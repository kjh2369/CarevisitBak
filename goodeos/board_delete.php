<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$code		= $_POST['code'];
	$board_type	= $_POST['board_type'];
	$board_id	= $_POST['board_id'];
	$reply_id	= $_POST['reply_id'];
	$page		= $_POST['page'];
	$mode		= $_POST['mode'];

	if ($mode == 3){
		$temp_id = $reply_id;

		// 답변여부(answer_id 있으면 Y 없으면 N)
		$sql = "update tbl_board
				   set answer_id    = NULL 
				 where board_center = '$code'
				   and board_type   = '$board_type'
				   and board_id     = '$board_id'";
		
		if (!$conn->execute($sql)){
			$conn->rollback();
			echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
			exit;
		}
	}else{
		$temp_id = $board_id;
	}

	$conn->begin();
	
	// 리플삭제
	$sql = "delete
			  from tbl_board_reply
			 where board_center = '$code'
			   and board_type   = '$board_type'
			   and board_id     = '$temp_id'";
	if (!$conn->execute($sql)){
		$conn->rollback();
		echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
		exit;
	}

	// 첨부파일삭제
	$sql = "delete
			  from tbl_board_file
			 where board_center = '$code'
			   and board_type   = '$board_type'
			   and board_id     = '$temp_id'";
	if (!$conn->execute($sql)){
		$conn->rollback();
		echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
		exit;
	}

	// 본문삭제
	$sql = "delete
			  from tbl_board
			 where board_center = '$code'
			   and board_type   = '$board_type'
			   and board_id     = '$temp_id'";
	if (!$conn->execute($sql)){
		$conn->rollback();
		echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
		exit;
	}

	$conn->commit();
?>
<script>
	if ('<?=$mode;?>' == '3'){
		location.replace('board_reg.php?code=<?=$code;?>&board_type=<?=$board_type?>&board_id=<?=$board_id?>&page=<?=$page?>&mode=2');
	}else{
		location.replace('board_list.php?board_type=<?=$board_type;?>&page=1');
	}
</script>