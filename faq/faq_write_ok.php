<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$id		= $_POST['id'];
	$gbn	= $_POST['gbn'];
	$subject= $_POST['subject'];
	$content= $_POST['content'];
	$write	= $_POST['write'];

	if (Empty($id)){
		$sql = 'INSERT INTO faq (
				 faq_gbn
				,quest
				,result
				,reg_dt
				,reg_id
				,reg_name
				,domain
				,insert_id
				,insert_dt) VALUES (
				 \''.$gbn.'\'
				,\''.$subject.'\'
				,\''.$content.'\'
				,NOW()
				,\''.$_SESSION['userCode'].'\'
				,\''.$write.'\'
				,\''.$gDomainID.'\'
				,NOW()
				,\''.$_SESSION['userCode'].'\')';
	}else{
		$sql = 'UPDATE faq
				   SET faq_gbn  = \''.$gbn.'\'
				,      quest    = \''.$subject.'\'
				,      result   = \''.$content.'\'
				,      reg_name = \''.$write.'\'
				 WHERE id = \''.$id.'\'';
	}

	if ($conn->execute($sql)){
		echo 1;
	}else{
		echo 9;
	}

	include_once('../inc/_db_close.php');
?>