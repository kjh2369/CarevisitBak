<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	서비스 연계 및 의뢰서
	 *********************************************************/
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfSearch()',200);
		lfResize();
	});

	//조회
	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./hce_svc_connect_search.php'
		,	data:{
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#tbodyList').html(html);
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>

<div class="title title_border">
	<div style="float:left; width:auto;">연계 및 의뢰서</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m"><button type="button" class="bold" onclick="location.href='../hce/hce_body.php?sr=<?=$sr;?>&type=92'">추가</button></span>
		<span class="btn_pack m"><button type="button" class="bold" onclick="lfPDF('<?=$type;?>');">전체출력</button></span>
	</div>
</div>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="90px">
		<col width="70px">
		<col width="70px">
		<col width="200px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">의뢰일자</th>
			<th class="head">의뢰인</th>
			<th class="head">관계</th>
			<th class="head">의뢰기관</th>
			<th class="head last">의뢰사유</th>
		</tr>
	</thead>
</table>

<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<tbody id="tbodyList"></tbody>
</table>

<?
	include_once('../inc/_db_close.php');
?>