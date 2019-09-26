<?
	include_once('../inc/_header.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	$parent = $_GET['parent'];

	if ($parent == 'ROOT'){
		$sql = "select concat('R', right(concat('00', cast(cast(ifnull(max(right(parent, 3)), 0) as signed) + 1 as char)), 3))
				  from tbl_category";
		$parent		= $conn->get_data($sql);
		$parentName = 'ROOT';
	}else{
		$parentName = '';
	}
?>
<style>
body{
	margin-left:10px;
	margin-right:10px;
}
.view_type1 thead th{
	padding:0;
	margin:0;
	text-align:center;
}
</style>
<base target="_self">
<script type="text/javascript" src="../js/mall.js"></script>
<form name="f" method="post">
<table style="width:100%;">
<colgroup>
	<col>
</colgroup>
<tr>
	<td class="title">카테고리 추가</td>
</tr>
<tr>
	<td style="border:none; text-align:left; vertical-align:top; margin:0; padding:0;">
		<div>현재위치 : <?=$parentName;?></div>
		<div>명칭 : <input name="name" type="text" value=""></div>
	</td>
</tr>
<tr>
	<td style="border:none; text-align:right; margin:0; padding:0;">
		<span id="new" class="btn_pack m icon"><span class="save"></span><button type="button" onClick="">저장</button></span>
	</td>
</tr>
</table>
</form>
<?
	include_once('../inc/_footer.php');
?>