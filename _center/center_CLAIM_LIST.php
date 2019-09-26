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
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split('?');
				var tot = {};

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);
						var m = col['month'];

						$('#ID_CELL_'+m+'_1').text(__str2num(col['cnt']) ? __num2str(col['cnt']) : '');
						$('#ID_CELL_'+m+'_2').text(__str2num(col['acctAmt']) + __str2num(col['unpaid']) ? __num2str(__str2num(col['acctAmt'])+__str2num(col['unpaid'])) : '');
						$('#ID_CELL_'+m+'_3').text(__str2num(col['acctAmt']) ? __num2str(col['acctAmt']) : '');
						$('#ID_CELL_'+m+'_4').text(__str2num(col['unpaid']) ? __num2str(col['unpaid']) : '');
						$('#ID_CELL_'+m+'_5').text(__str2num(col['deposit']) ? __num2str(col['deposit']) : '');
						$('#ID_CELL_'+m+'_6').text(__str2num(col['nonpay']) ? __num2str(col['nonpay']) : '');

						tot['1'] = __str2num(tot['1']) + __str2num(col['cnt']);
						//tot['2'] = __str2num(col['acctAmt'])+__str2num(col['unpaid']);
						tot['3'] = __str2num(tot['3']) + __str2num(col['acctAmt']);
						tot['4'] = __str2num(col['unpaid']);
						tot['5'] = __str2num(tot['5']) + __str2num(col['deposit']);
						tot['6'] = __str2num(tot['6']) + __str2num(col['nonpay']);
					}
				}

				tot['2'] = __str2num(tot['3']) + __str2num(tot['4']);

				$('#ID_CELL_TOT_1').text(__num2str(tot['1']));
				$('#ID_CELL_TOT_2').text(__num2str(tot['2']));
				$('#ID_CELL_TOT_3').text(__num2str(tot['3']));
				$('#ID_CELL_TOT_4').text(__num2str(tot['4']));
				$('#ID_CELL_TOT_5').text(__num2str(tot['5']));
				$('#ID_CELL_TOT_6').text(__num2str(tot['6']));

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
		lfMenu('CLAIM_ORG','&company='+$('#cboCompany').val()+'&year='+$('#yymm').attr('year')+'&month='+month+'&popYn=Y','Y');
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
		<col width="50px">
		<col width="70px">
		<col width="80px" span="5">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">월</th>
			<th class="head" rowspan="2">기관수</th>
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
	<tbody>
		<tr>
			<td class="sum center">합계</td>
			<td class="sum center"><div id="ID_CELL_TOT_1" class="right"></div></td>
			<td class="sum center"><div id="ID_CELL_TOT_2" class="right"></div></td>
			<td class="sum center"><div id="ID_CELL_TOT_3" class="right"></div></td>
			<td class="sum center"><div id="ID_CELL_TOT_4" class="right"></div></td>
			<td class="sum center"><div id="ID_CELL_TOT_5" class="right"></div></td>
			<td class="sum center"><div id="ID_CELL_TOT_6" class="right"></div></td>
			<td class="sum center last"></td>
		</tr><?
		for($i=1; $i<=12; $i++){?>
			<tr>
				<td class="center"><?=$i;?>월</td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_1" class="right"></div></td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_2" class="right"></div></td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_3" class="right"></div></td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_4" class="right"></div></td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_5" class="right"></div></td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_6" class="right"></div></td>
				<td class="center last">
					<div class="left">
						<span class="btn_pack small"><button onclick="lfMoveOrg('<?=$i;?>');">기관별</button></span>
					</div>
				</td>
			</tr><?
		}?>
	</tbody>
</table>