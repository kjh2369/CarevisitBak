<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$IPIN	= $_POST['IPIN'];
	$rcpt	= $_POST['rcpt'];
	$ispt	= $_POST['ispt'];
	$seq	= $_POST['seq'];
?>
<form name="f" method="post" enctype="multipart/form-data">
</form>
<?
	include_once('../inc/_footer.php');
?>