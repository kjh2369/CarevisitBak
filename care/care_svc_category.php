<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_svc_category_search.php'
		,	data :{
				'SR':$('#sr').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_CATEGORY').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfCategoryReg(parent, code, name, seq){
		var objModal = new Object();
		var url = './care_svc_category_reg.php';
		var style = 'dialogWidth:400px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.SR		= $('#sr').val();
		objModal.parent = (parent ? parent : '');
		objModal.code	= (code ? code : '');
		objModal.name	= (name ? name : '');
		objModal.seq	= (seq ? seq : '');

		window.showModalDialog(url, objModal, style);

		if (objModal.result) lfSearch();
	}

	function lfCategoryRemove(code){
		if (!code) return;
		if (!confirm('삭제 후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_svc_category_remove.php'
		,	data :{
				'SR'	:$('#sr').val()
			,	'code'	:code
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result == 1){
					lfSearch();
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else if (result == 7){
					alert('하위 카테고리가 존재합니다. 하위 카테고리를 먼저 삭제하여 주십시오.');
				}else if (result == 5){
					alert('카테고리에 묶음이 등록되어 있습니다. 묶음 등록 취소 후 삭제하여 주십시오.');
				}else{
					alert(result);
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfCategorySet(obj, code){
		var objModal = new Object();
		var url = './care_svc_category_set.php';
		var style = 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.SR		= $('#sr').val();
		objModal.code	= (code ? code : '');

		window.showModalDialog(url, objModal, style);

		$(obj).text('묶음('+objModal.count+'건)');
	}
</script>
<div class="title title_border">카테고리관리(<?=$title;?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">명칭</th>
			<th class="head">우선순위</th>
			<th class="head last">
				<div style="float:left; width:auto; padding-left:5px;"><span class="btn_pack small"><button onclick="lfCategoryReg();">추가</button></span></div>
				<div style="float:center; width:auto;">비고</div>
			</th>
		</tr>
	</thead>
	<tbody id="ID_CATEGORY"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>