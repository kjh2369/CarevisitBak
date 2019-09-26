<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		//lfSearch();
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
				'company':$('#cboCompany').val()
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				var html = html.split('<!--CUT_LINE-->');
				$('#ID_TOTAL').html(html[1]);
				$('#ID_LIST').html(html[0]);
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

	function lfPopDtl(orgNo){
		$.ajax({
			type:'POST',
			url:'../center/bill_rec_dtl.php',
			data:{
				'orgNo'	:orgNo
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(html){
				$('#ID_POP_DTL').html(html).show();
				$('#tempLodingBar').remove();
			},
			error: function (request, status, error){
				$('#tempLodingBar').remove();

				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function lfBillClose(){
		$('#ID_POP_DTL').hide();
	}

	function lfExcel(){
		return;
		var parm = new Array();
			parm = {
				'company':$('#cboCompany').val()
			,	'year':$('#yymm').attr('year')
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
		form.setAttribute('action', './center_<?=$menuId?>_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="150px">
		<col width="90px">
		<col width="70px">
		<col width="80px" span="5">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">기관명</th>
			<th class="head" rowspan="2">기관기호</th>
			<th class="head" rowspan="2">대표자</th>
			<th class="head" colspan="3">청구내역</th>
			<th class="head" colspan="2">입금내역</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">합계</th>
			<th class="head">당월분</th>
			<th class="head">미납분</th>
			<th class="head">입금</th>
			<th class="head">미납</th>
		</tr>
	</thead>
	<tbody id="ID_TOTAL">
		<tr>
			<td class="sum last" colspan="10"></td>
		</tr>
	</tbody>
</table>
<div style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="50px">
			<col width="150px">
			<col width="90px">
			<col width="70px">
			<col width="80px" span="5">
			<col>
		</colgroup>
		<tbody id="ID_LIST"></tbody>
		<tfoot>
			<tr>
				<td class="bottom last"></td>
			</tr>
		</tfoot>
	</table>
</div>