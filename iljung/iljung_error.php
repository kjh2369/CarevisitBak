<?
	include("../inc/_header.php");

	$code  = $_REQUEST["mCode"];
	$kind  = $_REQUEST["mKind"];
	$key   = $_REQUEST["mKey"];
	$year  = $_REQUEST["calYear"];
	$month = $_REQUEST["calMonth"];
?>
<style>
body{
	margin-top:10px;
	margin-left:10px;
	margin-right:10px;
	overflow-x:hidden;
}
</style>

<script type='text/javascript' src='../js/change_info_guide.js'></script>
<script type="text/javascript" src="../js/iljung.reg.js"></script>
<script type="text/javascript" src="../js/iljung.add.js"></script>
<script type="text/javascript" src="../js/work.js"	></script>

<form name="f" method="post">

<div id="body_err">

<div id="center_info"></div>

<div id="error_message"></div>

</div>

</form>
<?
	include("../inc/_footer.php");
?>
<script language="javascript">
	self.focus();

	window.onload = function(){
		_set_center_info('<?=$code;?>','<?=$key;?>','<?=$year;?>','<?=$month;?>','<?=$svc_id;?>','ERROR');
	}
</script>