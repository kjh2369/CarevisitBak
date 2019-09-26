<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_search.php'
		,	data:{
				'company':$('#cboCompany').val()
			,	'year':$('#yymm').attr('year')
			,	'month':$('#yymm').attr('month')
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

	function lfMoveOrg(month){
		lfMenu('CLAIM_ORG','&year='+$('#yymm').attr('year')+'&month='+month);
	}

	function lfExcel(){
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
		<col width="40px">
		<col width="140px">
		<col width="60px" span="10">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">기관명</th>
			<th class="head" colspan="3">청구</th>
			<th class="head" colspan="3">입금</th>
			<th class="head" colspan="4">미납</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">합계</th>
			<th class="head">CMS</th>
			<th class="head">무통장</th>
			<th class="head">합계</th>
			<th class="head">CMS</th>
			<th class="head">무통장</th>
			<th class="head">합계</th>
			<th class="head">미납분</th>
			<th class="head">CMS</th>
			<th class="head">무통장</th>
		</tr>
	</thead>
	<tbody id="ID_TOTAL">
		<tr>
			<td class="sum center" colspan="2"><div class="right">합계</div></td>
			<td class="sum center" colspan="3"><div class="right">0</div></td>
			<td class="sum center" colspan="3"><div class="right">0</div></td>
			<td class="sum center" colspan="4"><div class="right">0</div></td>
			<td class="sum center last"></td>
		</tr>
	</tbody>
</table>
<div id="ID_BODY_LIST" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="140px">
			<col width="60px" span="10">
			<col>
		</colgroup>
		<tbody id="ID_LIST"></tbody>
	</table>
</div>