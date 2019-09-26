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
	_find_body('M');

});

-->
</script>

<div class="title">직원조회</div>

<table class="my_table my_border">
	<colgroup>
		<col width="55px">
		<col width="55px">
		<col width="100px">
		<col width="55px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td>직원명</td>
			<td>
				<input id="findName" name="findName" type="text" value="" style="width:100px;" onkeypress="if(event.keyCode==13){_find_body('M');}">
			</td>
			<td>전화번호뒷자리</td>
			<td>
				<input id="findTel" name="findTel" type="text" value="" style="width:100px;" maxlength="4" class="phone" onkeypress="if(event.keyCode==13){_find_body('T');}">
			</td>
			<td class="last">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_find_body('M');">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style='width:100%;'>
	<colgroup>
		<col width="70px">
		<col width="65px">
		<col width="65px">
		<col width="85px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">성명</th>
			<th class="head">생년월일</th>
			<th class="head">입사일</th>
			<th class="head">전화</th>
			<th class="head last">주소</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class='center last' colspan='5'>
				<div id='infoBody' style='overflow-x:hidden; overflow-y:scroll; width:100%; height:255px;'></div>
			</td>
		</tr>
	</tbody>

<?
	include_once("../inc/_db_close.php");
?>

