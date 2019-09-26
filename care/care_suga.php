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
		setTimeout('lfSearch()',200);
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
			setTimeout('lfSearch()',200);
		}
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_suga_search.php'
		,	data :{
				'sr':'<?=$sr;?>'
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

	function lfLoad(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'sr':'<?=$sr;?>'
			,	'type':'<?=$type;?>'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(13));
				var html = '';
				var no = 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						html += '<tr>';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="center">'+col['code']+'</td>';
						html += '<td class="center"><div class="left">'+col['name']+'</div></td>';
						//html += '<td class="center"><div class="right">'+__num2str(col['cost'])+'</div></td>';
						html += '<td class="center">'+col['from']+' ~ '+col['to']+'</td>';
						html += '<td class="center last"></td>';
						html += '</tr>';

						no ++;
					}
				}

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">
	<div style="float:left;width:auto;">수가관리(<?=$title;?>)</div>
	<div style="float:right; width:auto; margin-top:10px;"><span class="btn_pack m"><button type="button" onclick="lfFindSuga();">등록 및 변경</button></span></div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">검색</th>
			<td><input id="txtCategory" type="text" value=""></td>
			<td class="left last"><span class="btn_pack m"><button onclick="lfSearch();">조회</button></span></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<!--col width="70px"-->
		<col width="130px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">대분류</th>
			<th class="head">중분류</th>
			<th class="head">소분류</th>
			<th class="head">상세서비스</th>
			<!--th class="head">단가</th-->
			<th class="head">기간</th>
			<th class="head last">
				<div style="float:left; width:auto; height:24px; margin-left:5px;"></div>
				<div style="float:center; width:auto;">비고</div>
			</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr class="removeCls">
			<td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<!--
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="200px">
		<col width="70px">
		<col width="140px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">수가코드</th>
			<th class="head">수가명</th>
			<th class="head">수가</th>
			<th class="head">기간</th>
			<th class="head last">
				<div style="float:left; width:auto; height:24px; margin-left:5px;"></div>
				<div style="float:center; width:auto;">비고</div>
			</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr class="removeCls">
			<td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
-->
<?
	include_once('../inc/_db_close.php');
?>