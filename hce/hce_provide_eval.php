<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/******************************************
	 *	제공평가서
	 ******************************************/
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfResize(){
		var top = $('#divBody').offset().top;
		var height = $(document).height();

		var h = height - top - 10;

		$('#divBody').height(h);
	}

	//조회
	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./hce_provide_eval_search.php'
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
	<div style="float:left; width:auto;">제공평가서</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m"><button type="button" class="bold" onclick="location.href='../hce/hce_body.php?sr=<?=$sr;?>&type=142'">추가</button></span>
	</div>
</div>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">평가일자</th>
			<th class="head">사례관리자</th>
			<th class="head last">비고</th>
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