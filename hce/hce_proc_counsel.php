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
		var order = __getCookie('HCE_81_ORDER');

		if (!order) order = 'DESC';

		$('input:radio[name="optOrder"][value="'+order+'"]').attr('checked',true);

		setTimeout('lfSearch()',200);
		lfResize();
	});

	function lfResize(){
		var top = $('#divBody').offset().top;
		var height = $(document).height();

		var h = height - top - 10;

		$('#divBody').height(h);
	}

	//조회
	function lfSearch(){
		var order = $('input:radio[name="optOrder"]:checked').val();

		if (!order) order = 'DESC';

		__setCookie('HCE_81_ORDER', order, 31);

		$.ajax({
			type:'POST'
		,	url:'./hce_proc_counsel_search.php'
		,	data:{
				'order':order
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#tbodyList').html(html);

				var fromDt	= '';
				var toDt	= '';

				if (order == 'DESC'){
					fromDt	= $('td',$('tr:last',$('#tbodyList'))).eq(1).text();
					toDt	= $('td',$('tr:first',$('#tbodyList'))).eq(1).text();
				}else{
					toDt	= $('td',$('tr:last',$('#tbodyList'))).eq(1).text();
					fromDt	= $('td',$('tr:first',$('#tbodyList'))).eq(1).text();
				}

				$('#txtPrtFrom').val(__getDate(fromDt));
				$('#txtPrtTo').val(__getDate(toDt));
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script><?
$colgroup = '	<col width="40px">
				<col width="90px">
				<col width="60px">
				<col width="400px">
				<col width="70px">
				<col>';?>

<div class="title title_border">
	<div style="float:left; width:auto;">과정상담</div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="bottom last">
				<div class="bold" style="float:left; width:auto;">
					<label><input id="optOrderD" name="optOrder" type="radio" class="radio" value="DESC" onclick="lfSearch();" checked>최근일자<span style="color:BLUE;">순</span></label>
					<label><input id="optOrderA" name="optOrder" type="radio" class="radio" value="ASC" onclick="lfSearch();">최근일자<span style="color:BLUE;">역순</span></label>
				</div>
				<div style="float:right; width:auto;">
					<span class="btn_pack m"><span class="add"></span><button onclick="location.href='../hce/hce_body.php?sr=<?=$sr;?>&type=82'" target="frmBody">추가</button></span>
					<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('<?=$type;?>','','',$('#txtPrtFrom').val()+'/'+$('#txtPrtTo').val());">출력</button></span>
				</div>
				<div style="float:right; width:auto;">
					출력기간 : <input id="txtPrtFrom" type="text" value="" class="date"> ~ <input id="txtPrtTo" type="text" value="" class="date">
				</div>
			</td>
		</tr>
	</tbody>
</table>
<!-- <div class="my_border_blue" style="border-bottom:none;"> -->
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<thead>
			<tr>
				<th class="head">No</th>
				<th class="head">일자</th>
				<th class="head">상담방법</th>
				<th class="head">내용</th>
				<th class="head">상담자</th>
				<th class="head last">비고</th>
			</tr>
		</thead>
	</table>
<!-- </div> -->
<!-- <div id="divBody" class="my_border_blue" style="height:200px; overflow-x:hidden; overflow-y:auto; border-top:none;"> -->
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody id="tbodyList"></tbody>
	</table>
<!-- </div> -->
<?
	include_once('../inc/_db_close.php');
?>