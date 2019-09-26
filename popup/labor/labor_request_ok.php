<?
	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_db_open.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_myFun.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_ed.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/home/var.php');
	
	$code       = $_SESSION['userCenterCode'];
	$center		= $_POST['center'];
	$phone		= Str_Replace('-','',$_POST['phone']);
	$content	= $_POST['content'];
	
	$sql = 'INSERT INTO counsel (
			 c_name
			,c_dt
			,c_phone
			,c_content
			,c_answer_gbn
			,c_domain_id
			,org_no) VALUES (
			 \''.$center.'\'
			,NOW()
			,\''.$phone.'\'
			,\''.$content.'\'
			,\'5\'
			,\'1\'
			,\''.$code.'\'
			)';

	$conn->execute($sql);

	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_db_close.php');
?>