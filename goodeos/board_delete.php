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

		// �亯����(answer_id ������ Y ������ N)
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
	
	// ���û���
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

	// ÷�����ϻ���
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

	// ��������
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