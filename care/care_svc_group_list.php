<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./care_svc_group_search.php'
		,	data:{
				'SR':'<?=$SR;?>'
			,	'category':$('#ID_CATEGORY').attr('category')
			,	'IsPopup':'Y'
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#ID_LIST').html(html);
			}
		,	error:function(error){
				alert('err');
			}
		}).responseXML;
	}

	function lfCategoryFind(){
		var objModal = new Object();
		var url = './care_svc_category_find.php';
		var style = 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win = window;
		objModal.SR	 = '<?=$SR;?>'
		objModal.code= '';
		objModal.name= '';

		window.showModalDialog(url, objModal, style);

		if (objModal.code){
			$('#ID_CATEGORY').attr('category',objModal.code).text(objModal.name);
			lfSearch();
		}
	}

	function lfSetGroup(sugaCd,seq,name){
		opener.lfSetGroup(sugaCd,seq,name);
		self.close();
	}
</script>
<div class="title title_border">서비스 묶음 조회</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">카테고리 선택</th>
			<td id="ID_CATEGORY" class="left last" onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='#FFFFFF';" onclick="lfCategoryFind();" category=""></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="150px">
		<col width="150px">
		<col width="150px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">그룹명</th>
			<th class="head">자원명</th>
			<th class="head">서비스</th>
			<th class="head">대상자수</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="ID_LIST"></tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>