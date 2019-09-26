<?
	session_start();
	include_once('../inc/_http_uri.php');
	include_once('../inc/_hce.php');

	$hce->init();

	$type = $_GET['type'];
	$sr   = $_GET['sr'];

	if (!$type) $type = '1';
?>
<html>
<head>
	<title>::방문서비스시스템::</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<frameset rows="182px,*" frameborder="0">
	<frame src="./hce_top.php?sr=<?=$sr;?>&type=<?=$type;?>" name="frmTop" scrolling="no" noresize>
	<frameset cols="205px,*" frameborder="0">
		<frame src="./hce_left.php?sr=<?=$sr;?>&type=<?=$type;?>" name="frmLeft" scrolling="no" noresize>
		<frame src="./hce_body.php?sr=<?=$sr;?>&type=<?=$type;?>" name="frmBody" scrolling="no" noresize>
	</frameset>
</frameset>
</html>