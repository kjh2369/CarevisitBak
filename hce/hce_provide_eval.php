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

<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="right bottom last">
				<span class="btn_pack m"><span class="add"></span><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=142" target="frmBody">추가</a></span>
			</td>
		</tr>
	</tbody>
</table>

<div class="my_border_blue" style="border-bottom:none;"><?
	$colgroup = '
		<col width="40px">
		<col width="70px">
		<col width="80px">
		<col>';?>
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<thead>
			<tr>
				<th class="head">No</th>
				<th class="head">평가일자</th>
				<th class="head">사례관리자</th>
				<th class="head last">비고</th>
			</tr>
		</thead>
	</table>
</div>

<div id="divBody" class="my_border_blue" style="height:200px; overflow-x:hidden; overflow-y:auto; border-top:none;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody id="tbodyList"></tbody>
	</table>
</div>
<?
	include_once('../inc/_db_close.php');
?>