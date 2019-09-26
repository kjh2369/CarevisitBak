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
		,	url:'./center_ACCT_MONTH_search.php'
		,	data:{
				'company':$('#cboCompany').val()
			,	'year':$('#yymm').attr('year')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				if (!data) return;

				var row = data.split('?');

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);

						$('#ID_CELL_MONTH_'+col['month']).text(__num2str(col['acctAmt']));
					}
				}

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
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="100px" span="6">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center" rowspan="3">월별</th>
			<th class="center" colspan="3">청구내역</th>
			<th class="center" colspan="2">입금내역</th>
			<th class="center" rowspan="2">미납합계</th>
			<th class="center last" rowspan="3">비고</th>
		</tr>
		<tr>
			<th class="center">합계</th>
			<th class="center">당월분</th>
			<th class="center">미납분</th>
			<th class="center">입금액</th>
			<th class="center">미납금</th>
		</tr>
		<tr>
			<td class="center"><div class="right"></div></td>
			<td class="center"><div class="right"></div></td>
			<td class="center"><div class="right"></div></td>
			<td class="center"><div class="right"></div></td>
			<td class="center"><div class="right"></div></td>
			<td class="center"><div class="right"></div></td>
		</tr><?
		for($i=1; $i<=12; $i++){?>
			<tr>
				<th class="center"><?=$i;?>월</th>
				<td class="center"><div id="ID_CELL_TOTAL_<?=$i;?>" class="right"></div></td>
				<td class="center"><div id="ID_CELL_MONTH_<?=$i;?>" class="right"></div></td>
				<td class="center"><div class="right"></div></td>
				<td class="center"><div class="right"></div></td>
				<td class="center"><div class="right"></div></td>
				<td class="center"><div class="right"></div></td>
				<td class="center last"><div class="left"></div></td>
			</tr><?
		}?>
	</tbody>
</table>