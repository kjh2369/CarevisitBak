<?
	include("../inc/_header.php");
?>
<script src="../js/work.js" type="text/javascript"></script>
<div id="myBody"></div>
<?
	include("../inc/_footer.php");
?>
<script language="javascript">
	showWorkDetail('<?=$_GET["mPopup"];?>','<?=$_GET["mCode"];?>','<?=$_GET["mKind"];?>','<?=$_GET["mkey"];?>','<?=$_GET["mDate"];?>','<?=$_GET["mFmTime"];?>','<?=$_GET["mSeq"];?>');
</script>
<script>self.focus();</script>