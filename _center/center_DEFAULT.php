<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		//$('#cboCompany option:eq(0)').text('전체').attr('selected','selected');
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
				//$('#ID_LIST').html('<tr><td colspan="11">'+html+'</td></tr>');
				$('#ID_LIST').html(html);
				$('#tempLodingBar').remove();

				var amt3 = 0, amt4 = 0, amt5 = 0, amt6 = 0, amt7 = 0, amt8 = 0;

				$('tr',$('#ID_LIST')).each(function(){
					amt3 += __str2num($('td',this).eq(3).text());
					amt4 += __str2num($('td',this).eq(4).text());
					amt5 += __str2num($('td',this).eq(5).text());
					amt6 += __str2num($('td',this).eq(6).text());
					amt7 += __str2num($('td',this).eq(7).text());
					amt8 += __str2num($('td',this).eq(8).text());
				});

				$('#ID_CELL_SUM_3').text(__num2str(amt3));
				$('#ID_CELL_SUM_4').text(__num2str(amt4));
				$('#ID_CELL_SUM_5').text(__num2str(amt5));
				$('#ID_CELL_SUM_6').text(__num2str(amt6));
				$('#ID_CELL_SUM_7').text(__num2str(amt7));
				$('#ID_CELL_SUM_8').text(__num2str(amt8));
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
		lfMenu('CLAIM_ACCT_ORG','&year='+$('#yymm').attr('year')+'&month='+month);
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
		<col width="150px">
		<col width="90px">
		<col width="80px" span="6">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">기관명</th>
			<th class="head" rowspan="2">기관기호</th>
			<th class="head" rowspan="2">청구액</th>
			<th class="head" rowspan="2">입금액</th>
			<th class="head" rowspan="2">미납액</th>
			<th class="head" colspan="3">미연결(현시점)</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">합계</th>
			<th class="head">CMS</th>
			<th class="head">무통장</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="sum center" colspan="3"><div class="right">합계</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_3" class="right">0</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_4" class="right">0</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_5" class="right">0</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_6" class="right">0</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_7" class="right">0</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_8" class="right">0</div></td>
			<td class="sum center last"></td>
		</tr>
	</tbody>
</table>
<div id="ID_BODY_LIST" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="150px">
			<col width="90px">
			<col width="80px" span="6">
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