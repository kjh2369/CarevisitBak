<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$power	= $_SESSION['userLevel'];
	$yymm	= Date('Y-m');
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
			type :'POST'
		,	url  :'./bm_expense_search.php'
		,	data :{
				'fromDt':$('#txtFromDt').val()
			,	'toDt'	:$('#txtToDt').val()
			}
		,	beforeSend:function(){
				$('#txtFromDt').attr('orgDt',$('#txtFromDt').val());
				$('#txtToDt').attr('orgDt',$('#txtToDt').val());
			}
		,	success:function(html){
				html = html.split('<!--CUT_LINE-->');
				$('#ID_LIST').html(html[0]);
				$('#ID_SUM').html(html[1]);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfExcel(){
		if (!$('#txtFromDt').attr('orgDt') || !$('#txtToDt').attr('orgDt')) return;

		var parm = new Array();
			parm = {
				'orgNo'	:'<?=$orgNo;?>'
			,	'fromDt':$('#txtFromDt').attr('orgDt').replace('-','')
			,	'toDt'	:$('#txtToDt').attr('orgDt').replace('-','')
			,	'gbn'	:'1'
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
<div class="title title_border">본인부담내역(월 청구기준)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기간</th>
			<td class="last">
				<div style="float:left; width:auto; padding-top:2px;">
					<input id="txtFromDt" type="text" value="<?=$yymm;?>" orgDt="" class="yymm"> ~
					<input id="txtToDt" type="text" value="<?=$yymm;?>" orgDt="" class="yymm">
				</div>
				<div style="float:left; width:auto; padding-top:2px;">
					<span class="btn_pack m"><button onclick="lfSearch()">조회</button></span>
				</div>
				<div class="right" style="float:right; width:auto; padding-top:2px;">
					<span class="btn_pack m"><button onclick="lfExcel()">본인부담금내역 출력</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="100px" span="4">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">년월</th>
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