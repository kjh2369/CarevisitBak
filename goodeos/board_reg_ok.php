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
	$notice_yn	= $_POST['notice_yn'] == 'Y' ? 'Y' : 'N';
	$page		= $_POST['page'];
	$mode		= $_POST['mode'];

	if ($mode == 3){
		$reply_yn = 'Y';
	}else{
		$reply_yn = 'N';
	}

	$reg_id		= $_SESSION['userCode'];
	$reg_name	= $_POST['reg_name'] != '' ? $_POST['reg_name'] : $_SESSION['userName'];
	$reg_date	= date('Y-m-d', mktime());
	$reg_time	= date('H:i:s', mktime());

	$subject	= addslashes($_POST['subject']);
	$content	= $_POST['back_content'];

	$conn->begin();

	if ($board_id == 0){
		$type = 1;

		// 다음 아이디
		//$sql = "select ifnull(max(board_id), 0) + 1
		//		  from tbl_board
		//		 where board_center = '$code'
		//		   and board_type   = '$board_type'";
		//$board_id = $conn->get_data($sql);
		$board_id = get_next_id($conn, $code, $board_type);

		$sql = "insert into tbl_board (board_center, board_type, board_id, reg_id, reg_name, reg_date, reg_time, subject, content, reply_id, notice, domain_id) values (
				 '$code'
				,'$board_type'
				,'$board_id'
				,'$reg_id'
				,'$reg_name'
				,'$reg_date'
				,'$reg_time'
				,'$subject'
				,'$content'
				,'0'
				,'$notice_yn'
				,'$gDomainID')";

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
			exit;
		}
	}else{
		if ($mode == 3){
			$type = 2;

			if ($reply_id == 0){
				// 다음 아이디
				$reply_id = get_next_id($conn, $code, $board_type);

				// 답변여부(answer_id 있으면 Y 없으면 N)
				$sql = "update tbl_board
						   set answer_id    = '".$_SESSION['userCode']."'
						 where board_center = '$code'
						   and board_type   = '$board_type'
						   and board_id     = '$board_id'";

				if (!$conn->execute($sql)){
					$conn->rollback();
					echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
					exit;
				}

				$sql = "insert into tbl_board (board_center, board_type, board_id, reg_id, reg_name, reg_date, reg_time, subject, content, reply_id) values (
						 '$code'
						,'$board_type'
						,'$reply_id'
						,'$reg_id'
						,'$reg_name'
						,'$reg_date'
						,'$reg_time'
						,'$subject'
						,'$content'
						,'$board_id')";

				if (!$conn->execute($sql)){
					$conn->rollback();
					echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
					exit;
				}
			}else{
				$sql = "update tbl_board
						   set subject = '$subject'
						,      content = '$content'
						,      notice  = '$notice_yn'
						 where board_center = '$code'
						   and board_type   = '$board_type'
						   and board_id     = '$reply_id'";

				if (!$conn->execute($sql)){
					$conn->rollback();
					echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
					exit;
				}
			}
		}else{
			$type = 2;

			$sql = "update tbl_board
					   set subject = '$subject'
					,      content = '$content'
					,      notice  = '$notice_yn'
					 where board_center = '$code'
					   and board_type   = '$board_type'
					   and board_id     = '$board_id'";

			if (!$conn->execute($sql)){
				$conn->rollback();
				echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
				exit;
			}
		}
	}

	if ($board_type == 'pds'){
		for($i=1; $i<=3; $i++){
			$file = $_FILES['file'.$i];

			if ($file['error'] == '0'){
				$fileName = $code.'_'.$board_type.'_'.$board_id.'_'.$i;
				$movePath = $_SERVER['DOCUMENT_ROOT'].'/files/'.$fileName;

				if (move_uploaded_file($file['tmp_name'],$movePath)){
					$sql = 'REPLACE INTO tbl_board_file (
							 board_center
							,board_type
							,board_id
							,board_seq
							,file_name
							,file_size
							,file_type) VALUES (
							 \''.$code.'\'
							,\''.$board_type.'\'
							,\''.$board_id.'\'
							,\''.$i.'\'
							,\''.$file['name'].'\'
							,\''.$file['size'].'\'
							,\''.$file['type'].'\'
							)';

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
						 exit;
					}
				}
			}
		}
	}

	$conn->commit();

	function get_next_id($conn, $code, $board_type){
		$sql = "select ifnull(max(board_id), 0) + 1
				  from tbl_board
				 where board_center = '$code'
				   and board_type   = '$board_type'";
		$board_id = $conn->get_data($sql);

		return $board_id;
	}
?>
<script>
	alert('<?=$myF->message("ok","N");?>');
	if ('<?=$type;?>' == '1'){
		location.replace('board_list.php?board_type=<?=$board_type?>');
	}else{
		location.replace('board_reg.php?code=<?=$code;?>&board_type=<?=$board_type?>&board_id=<?=$board_id?>&page=<?=$page?>&mode=<?=$type?>');
	}
</script>