<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
?>
<base target='_self'>

<script language='javascript'>
<!--

var opener = null;

function search(){
	try{
		$.ajax({
			type: 'POST',
			url : './_find_suga_list.php',
			data: {
				'code':opener.code
			,	'svcCD':opener.svcCD
			,	'date':opener.date
			,	'name':$('#findName').attr('value')
			,	'over270YN':opener.over270YN != 'N' ? 'Y' : 'N'
			},
			beforeSend: function (){
			},
			success: function (xmlHttp){
				$('#listBody').html(xmlHttp);
				$('#tblList tr:even').css('background-color', '#ffffff');
				$('#tblList tr:odd').css('background-color', '#f9f9f9');
				$('#tblList tr').mouseover(function(){
					$(this).css('background-color', '#f2f5ff');
				});
				$('#tblList tr').mouseout(function(){
					if ($('#tblList tr').index($(this)) % 2 == 1){
						$(this).css('background-color', '#f9f9f9');
					}else{
						$(this).css('background-color', '#ffffff');
					}
				});
			},
			error: function (){
			}
		}).responseXML;
	}catch(e){
	}
}

function setItem(para){
	opener.para = para;
	self.close();
}

$(document).ready(function(){
	var height = $(document).height();
	var top    = __getObjectTop(listBody);

	$('#listBody').height(height - top - 2);

	opener = window.dialogArguments;

	search();
});

-->
</script>

<div class='title title_border'>수가찾기</div>

<table class='my_table' style='width:100%;'>
	<colgroup>
		<col width='60px'>
		<col width='100px'>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class='center'>수가명</th>
			<td class='center'><input id='findName' name='findName' type='text' value=''></td>
			<td class='left last'>
				<span class='btn_pack m'><button type='button' onclick='search();'>조회</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table id='tblList' class='my_table' style='width:100%;'>
	<colgroup>
		<col width='40px'>
		<col width='70px'>
		<col width='130px'>
		<col width='90px'>
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class='head'>No</th>
			<th class='head'>수가코드</th>
			<th class='head'>수가명</th>
			<th class='head'>단가</th>
			<th class='head last'>비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class='center last' colspan='5'>
				<div id='listBody' style='overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;'>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>