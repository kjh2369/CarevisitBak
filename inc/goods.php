<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	include_once('cust_var.php');
?>
<style>
.view_type1 thead th{
	padding:0;
	margin:0;
	text-align:center;
}
</style>
<script type="text/javascript" src="../js/mall.js"></script>
<form name="f" method="post">
<table style="width:100%;">
<colgroup>
	<col>
</colgroup>
<tr>
	<td class="title">상품정보</td>
</tr>
<tr>
	<td class="noborder" style="text-align:left; margin:0; padding:0;">
		<div id="terms" style="width:auto; float:left;">

		</div>
		<div id="button" style="width:auto; float:right;">

		</div>
	</td>
</tr>
<tr>
	<td style="border:none; vertical-align:top; margin:0; padding:0;">
		<div id="body"></div>
	</td>
</tr>
</table>
<input name="page" type="hidden" value="<?=$page;?>">
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
<script language='javascript'>
	document.body.onload = function(){

	}
</script>