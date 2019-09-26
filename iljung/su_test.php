<?
	include("../inc/_db_open.php");
	include("../inc/_function.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
?>
	<script src="../js/prototype.js" type="text/javascript"></script>
	<script src="../js/xmlHTTP.js" type="text/javascript"></script>
	<script src="../js/script.js" type="text/javascript"></script>
	<script src="../js/center.js" type="text/javascript"></script>
	<script src="../js/iljung.js" type="text/javascript"></script>
<?
	getMonthSugup($_POST);

	echo '<br><br><br>END';

	include("../inc/_db_close.php");
?>