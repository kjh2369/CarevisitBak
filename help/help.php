<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>
<script language='javascript'>
<!--
//-->
</script>

<div style="margin-left:20px; margin-bottom:50px;">
	<img src="../img/img_manual.gif" border="0" usemap="#Map" />
	<map name="Map" id="Map">
		<area shape="rect" coords="547,102,708,124" href="#" onClick="location.href='../file/AdbeRdr810_ko_KR.msi';"/> <!--PDF 다운 -->
		<area shape="rect" coords="152,252,272,273" href="#" onclick="window.open('./M_4.0_2.pdf');"/> <!--방문서비스관리 다운 -->
		<area shape="rect" coords="516,251,635,273" href="#" onClick="window.open('./M_4.0_1.pdf');"/> <!--간편사용자 -->
	</map>
</div>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>