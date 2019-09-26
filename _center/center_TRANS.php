<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfResizeSub(){
		var obj = __GetTagObject($('#ID_LIST'),'DIV');
		var h = document.body.offsetHeight - $(obj).offset().top - $('#copyright').height();
		$(obj).height(h);
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_search.php'
		,	data:{
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LIST').html(html);
				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
</script><?
$colgroup = '
	<col width="40px">
	<col width="90px">
	<col width="150px">
	<col width="80px">
	<col width="90px">
	<col width="150px">
	<col width="80px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" colspan="3">현 기관</th>
			<th class="head" colspan="3">전 기관</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">기관기호</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th>
			<th class="head">기관기호</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th>
		</tr>
	</thead>
</table>
<div style="width:100%; height:100px; overflow-x:hidden; overflow-y:auto;">
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<tbody id="ID_LIST"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
</div>