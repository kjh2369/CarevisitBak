<?
	include_once('../inc/_header.php');
	include_once("../inc/_http_uri.php");
	include_once('../inc/_body_header.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	$mode = $_GET['mode'];
?>
<script type="text/javascript" src="../js/branch.js"></script>

<form name="f" method="post">

<div class="title title_border">지사조회</div>
<div id="myBody"></div>

</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
<script language="javascript">
	_branchList();
</script>