<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$board_center	= $_POST['board_center'];
	$board_type		= $_POST['board_type'];
	$board_id		= $_POST['board_id'];
	$page			= $_POST['page'];
	$reg_code		= $_SESSION['userCode'];
	$reg_name		= $_SESSION['userCenterName'];
	$reg_date		= date('Y-m-d', mktime());
	$reg_time		= date('H:i:s', mktime());
	$subject		= $_POST['subject'];
	$content		= $_POST['content'];

	if ($board_center == '') $board_center = 'goodeos';

	if ($board_id == 0){
		$sql = "select ifnull(max(board_id), 0) + 1
				  from tbl_board
				 where board_center = '$board_center'
				   and board_type   = '$board_type'
				   and board_id     = '$board_id'";
		$board_id = $conn->get_data($sql);

		$sql = "insert into tbl_board (
				 board_center
				,board_type
				,board_id
				,reg_id
				,reg_name
				,reg_date
				,reg_time
				,subject
				,content
				) values (
				 '$board_center'
				,'$board_type'
				,'$board_id'
				,'$reg_code'
				,'$reg_name'
				,'$reg_date'
				,'$reg_time'
				,'$subject'
				,'$content')";
	}else{
		$sql = "update tbl_board
				   set subject = '$subject'
				,      content = '$content'
				 where board_center = '$board_center'
				   and board_type   = '$board_type'
				   and board_id     = '$board_id'";
	}

	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();
	}else{
		$conn->rollback();
		echo $sql;
		echo '
			<script>
				alert("저장중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.");
				history.back();
			</script>
			 ';
		exit;
	}

	include_once("../inc/_db_close.php");
?>
<script>
	location.replace('../goodeos/comm_list.php?board_center=<?=$board_center;?>&board_type=<?=$board_type;?>&page=<?=$page;?>');
</script>