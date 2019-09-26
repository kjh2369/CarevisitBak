<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_body_header.php");

	$code	= $_POST['code'];
	$kind	= $_POST['kind'];
	$jumin	= $_POST['jumin'];
	$year	= $_POST['year']  != '' ? $_POST['year']  : date('Y', mktime());
	$month	= $_POST['month'] != '' ? $_POST['month'] : date('m', mktime());

	$init_year = $myF->year();
?>
<script type="text/javascript" src="../js/work.js"></script>
<script language="javascript">
<!--

-->
</script>
<div class="title">방문일정현황</div>
<form name="f" method="post">
<table class="my_table my_border" style="border-bottom:none;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
		</tr>
	</tbody>
</table>
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>