<?
	include("../inc/_header.php");
?>
<script src="../js/work.js" type="text/javascript"></script>
<div id="myBody"></div>
<?
	include("../inc/_footer.php");
?>
<script language="javascript">
	popupSugupTimeLoad('Y', '<?=$_GET["mCode"];?>', '<?=$_GET["mKind"];?>', '<?=$_GET["mYear"];?>', '<?=$_GET["mMonth"];?>', '<?=$_GET["mSvcCode"];?>', '<?=$_GET["mKey"];?>');
</script>
<script>self.focus();</script>