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
		,	url  :'./care_rec_provide_q.php'
		,	data :{
				'type'	:'PROVIDE_ITEM'
			,	'SR'	:$('#sr').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();

				$('td[id^="itemId_"]',$('#tbodyList')).unbind('mouseover').bind('mouseover',function(){
					//var idx = $('td',$(this).parent()).index(this);
					var obj = $(this).parent();
					$('.clsTd',obj).css('background-color','#EFEFEF');
				}).unbind('mouseout').bind('mouseout',function(){
					var obj = $(this).parent();
					$('.clsTd',obj).css('background-color','#FFFFFF');
				});
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSetUse(cd, seq){
		$.ajax({
			type :'POST'
		,	url  :'./care_rec_provide_q.php'
		,	data :{
				'type'	:'PROVIDE_ITEM_SET'
			,	'SR'	:$('#sr').val()
			,	'cd'	:cd
			,	'seq'	:seq
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				$('#itemId_'+cd+'_'+seq).css('color',result == 'Y' ? 'BLUE' : 'RED').text(result);
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">서비스 제공기록지 항목관리(<?=$title;?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col width="150px" span="3">
		<col width="80px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">대분류</th>
			<th class="head">중분류</th>
			<th class="head">소분류</th>
			<th class="head">상세서비스</th>
			<th class="head">사용기간</th>
			<th class="head">출력여부</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>