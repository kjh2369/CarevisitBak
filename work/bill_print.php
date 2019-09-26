<?
	include("../inc/_header.php");
	include('../inc/_myFun.php');
	include('../inc/_ed.php');
	
	$mCode = $_GET['mCode'];
	$mKind = $_GET['mKind'];
	$mDate = $_GET['mDate'];
	$mSugupja = $ed->de($_GET['mSugupja']);
	$mBoninYul = $_GET['mBoninYul'];
?>
<style>
	body{
		margin:10px;
	}
</style>
<table style="width:100%;">
<tr>
	<td style="border:none; font-weight:bold;"><?=subStr($mDate, 0, 4);?>년 <?=subStr($mDate, 4, 2);?>월 장기요양급여 본인부담금 청구서(시설용)</td>
</tr>
</table>
<?
	include('../inc/_footer.php');
?>
<script>self.focus();</script>