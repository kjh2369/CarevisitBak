<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$year	= Date('Y');
	$month	= Date('m');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'../__management/expense/expense_search_year.php'
		,	data:{
				'fromDt':$('#txtFromDt').val()
			,	'toDt'	:$('#txtToDt').val()
			,	'type'	:'ADMIN'
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				html = html.split('<!--CUT_LINE-->');
				$('#ID_LIST').html(html[0]);
				$('#ID_SUM').html(html[1]);
			}
		,	complete:function(){
			}
		,	error:function(request, status, error){
				return;
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfExcel(orgNo, gbn){
		if (!orgNo || !gbn) return;

		var parm = new Array();
			parm = {
				'orgNo'	:orgNo
			,	'gbn'	:gbn
			,	'fromDt':$('#txtFromDt').val().replace('-','')
			,	'toDt'	:$('#txtToDt').val().replace('-','')
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('method', 'post');
		form.setAttribute('action', '../__management/expense_org_month_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">본인부담금내역(월 청구기준)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="147px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기간</th>
			<td>
				<input id="txtFromDt" type="text" value="<?=$year;?>-<?=$month;?>" class="yymm"> ~
				<input id="txtToDt" type="text" value="<?=$year;?>-<?=$month;?>" class="yymm">
			</td>
			<td class="left last">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="150px">
		<col width="100px" span="4">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">지점명</th>
			<th class="head">청구액</th>
			<th class="head">본인부담금</th>
			<th class="head">결제금액</th>
			<th class="head">매출미수</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="ID_LIST"></tbody>
	<tbody id="ID_SUM"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_db_close.php");
?>