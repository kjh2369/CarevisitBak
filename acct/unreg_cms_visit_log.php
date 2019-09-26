<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfSearch()', 200);
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./unreg_cms_visit_log_search.php'
		,	data :{
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
<div class="title title_border">CMS 미등록 기관 접속로그</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="150px">
		<col width="70px">
		<col width="80px">
		<col width="150px">
		<col width="50px" span="2">
		<col width="60px" span="2">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관코드</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th>
			<th class="head">연락처</th>
			<th class="head">주소</th>
			<th class="head">일수</th>
			<th class="head">횟수</th>
			<th class="head">오늘처음</th>
			<th class="head">마지막</th>
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