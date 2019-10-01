<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfFindSuga(){
		var objModal = new Object();
		var url = './care_suga_reg.php?sr=<?=$sr;?>';
		var style = 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win = window;
		objModal.type = '<?=$type;?>_POP';
		objModal.sr = '<?=$sr;?>';

		window.showModalDialog(url, objModal, style);

		if (objModal.result == 1){
			lfSearch();
		}
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_suga_search.php'
		,	data :{
				'SR':'<?=$sr;?>'
			,	'str':$('#txtCategory').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title">
	<div style="float:left;width:auto;">수가관리(<?=$title;?>)</div>
	<div style="float:right; width:auto; margin-top:10px;"><span class="btn_pack m"><button type="button" onclick="lfFindSuga();">등록 및 변경</button></span></div>
</div>
<table class="my_table my_border" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="90px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">검색</th>
			<td><input id="txtCategory" type="text" value="" style="width:70px;"></td>
			<td class="left last"><span class="btn_pack m"><button onclick="lfSearch(); return false;">조회</button></span></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="200px">
		<col width="200px">
		<col width="300px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">관</th>
			<th class="head">항</th>
			<th class="head">목</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr class="removeCls">
			<td class="center last" colspan="4">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last" style="background-color:white;"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>