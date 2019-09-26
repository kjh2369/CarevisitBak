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
	_find_body('C');

});

-->
</script>

<div class="title">고객조회</div>

<table class="my_table my_border">
	<colgroup>
		<col width="55px">
		<col width="55px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td>고객명</td>
			<td>
				<input id="findName" name="findName" type="text" value="" style="width:100px;" onkeypress="if(event.keyCode==13){_find_body('C');}">
			</td>
			<td class="last">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_find_body('C');">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>
<div id='infoBody'></div>

<?
	include_once("../inc/_db_close.php");
?>

