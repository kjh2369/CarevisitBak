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
	var SR = '';

	try{
		SR = opener.paras['SR'];
	}catch(e){}

	try{
		$.ajax({
			type: 'POST',
			url : './_find_client_list.php',
			data: {
				code :opener.code
			,	kind :opener.kind
			,	year :opener.year
			,	month:opener.month
			,	name :$('#findName').val()
			,	SR:SR
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

<div class='title title_border'>수급자찾기</div>

<table class='my_table' style='width:100%;'>
	<colgroup>
		<col width='60px'>
		<col width='100px'>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class='center'>수급자명</th>
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
		<col width='90px'>
		<col width='90px'>
		<col width='90px'>
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class='head'>No</th>
			<th class='head'>수급자명</th>
			<th class='head'>인정번호</th>
			<th class='head'>생년월일</th>
			<th class='head'>연락처</th>
			<th class='head last'>주소</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class='center last' colspan='6'>
				<div id='listBody' style='overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;'></div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>