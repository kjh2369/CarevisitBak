<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$code		= $_SESSION['userCode'];
	$board_type	= $_POST['board_type'];
	$board_seq	= $_POST['board_seq'];
	$reply_no   = $_POST['reply_no'];
	$page		= $_POST['page'];
	$mode		= $_POST['mode'];

	$reg_id		= $_SESSION['userCode'];
	$reg_name	= $_SESSION['userName'];
	$reply_reg_dt	= date('Y-m-d', mktime());
	
	$reply   = addslashes($_POST['reply']);
	$reply_s = addslashes($_POST['reply_'.$reply_no]);
	

	$conn->begin();

	if ($reply_no == 0){
		// 다음 아이디
		$sql = "select ifnull(max(reply_no), 0) + 1
				  from news_reply
				 where news_gbn     = '$board_type'
				   and news_seq     = '$board_seq'";
		$reply_no = $conn->get_data($sql);

		$sql = "insert into news_reply (news_gbn, news_seq, reply_no, reply_contents, reply_reg_dt, reply_reg_id) values (
				 '$board_type'
				,'$board_seq'
				,'$reply_no'
				,'$reply'
				,now()
				,'$code'
				)";

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
			exit;
		}

	}else{
		$sql = "update news_reply
				   set reply_contents	= '$reply_s'
				 where news_gbn			= '$board_type'
				   and news_seq			= '$board_seq'";

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
	location.replace('board_reg.php?board_type=<?=$board_type?>&board_seq=<?=$board_seq?>&page=<?=$page?>&mode=2');
</script>