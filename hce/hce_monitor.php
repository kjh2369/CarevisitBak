<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	대상자 선정기준표
	 *********************************************************/

	$orgNo = $_SESSION['userCenterCode'];
	$orgType = '40';

	$consentDt = Date('Y-m-d');
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
		,	url:'./hce_monitor_search.php'
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
				<span class="btn_pack m"><span class="add"></span><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=102" target="frmBody">추가</a></span>
				<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('<?=$type;?>');">전체출력</button></span>
			</td>
		</tr>
	</tbody>
</table><?
$colgroup = '	<col width="40px">
				<col width="90px">
				<col width="50px">
				<col width="50px">
				<col width="70px">
				<col width="70px">
				<col>';?>
<div class="my_border_blue" style="border-bottom:none;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<thead>
			<tr>
				<th class="head">No</th>
				<th class="head">작성일자</th>
				<th class="head">방법</th>
				<th class="head">구분</th>
				<th class="head">담당자</th>
				<th class="head">조사자</th>
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