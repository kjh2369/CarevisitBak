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

	if ($mode == 3){
		$reply_yn = 'Y';
	}else{
		$reply_yn = 'N';
	}

	$subject	= addslashes($_POST['subject']);
	$content	= $_POST['back_content'];

	$conn->begin();

	if ($board_seq == 0){
		$type = 1;
		
		$sql = 'SELECT IFNULL(MAX(news_seq),0)+1
				  FROM news_list
				 WHERE news_gbn     = \''.$board_type.'\'';
		$board_seq = $conn->get_data($sql);
		
		$sql = 'INSERT INTO news_list (
				 news_gbn
				,news_seq
				,news_reg_dt
				,news_subject
				,news_contents
				,news_reg_id
				,news_count) VALUES (
				\''.$board_type.'\'
				,\''.$board_seq.'\'
				,now()
				,\''.$subject.'\'
				,\''.$content.'\'
				,\''.$_SESSION['userCode'].'\'
				,\'0\'
				)';

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo '<script>alert("'.$myF->message('error','N').'"); history.back();</script>';
			exit;
		}
	}else{

		$type = 2;

		$sql = 'UPDATE news_list
				   SET news_subject = \''.$subject.'\'
				,      news_contents = \''.$content.'\'
				 WHERE news_gbn   = \''.$board_type.'\'
				   AND news_seq     = \''.$board_seq.'\'';

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
	if ('<?=$type;?>' == '1'){
		location.replace('board_list.php?board_type=<?=$board_type?>');
	}else{
		location.replace('board_reg.php?board_type=<?=$board_type?>&board_seq=<?=$board_seq?>&page=<?=$page?>&mode=<?=$type?>');
	}
</script>