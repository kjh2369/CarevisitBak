<?
	include_once("../inc/_header.php");
	include_once("../inc/_body_header.php");
?>
<div id="center_body" style="text-align:left;"></div>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");

	if ($_GET["gubun"] == "reg"){
	?>
		<script>
			_centerReg('reg','<?=$_GET["mCode"];?>','<?=$_GET["mKind"];?>','','');
		</script>
	<?
	}else if($_GET["gubun"] == "search"){
	?>
		<script>
			_centerList();
		</script>
	<?
	}
?>