<?
	include_once('../inc/_header.php');
?>
<script type="text/javascript">
<!--

var opener = null;

function setItem(para){
	opener.para = para;
	self.close();
}

$(document).ready(function(){
	opener = window.dialogArguments;
	_find_body('T');

});

-->
</script>

<div class="title">전화번호조회</div>

<table class="my_table my_border" style="botder-bottom:0;">
	<colgroup>
		<col width="100px">
		<col width="55px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td>전화번호뒷자리</td>
			<td>
				<input id="findTel" name="findTel" type="text" value="" style="width:100px;" maxlength="4" class="phone" onkeypress="if(event.keyCode==13){_find_body('T');}">
			</td>
			<td class="last">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_find_body('T');">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>
<div id='infoBody'></div>

<?
	include_once("../inc/_db_close.php");
?>

