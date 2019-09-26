<script type="text/javascript">
	if ('<?=$lbLimitSet;?>' == '1'){
		setTimeout('lfSetLimitAmt()',1);
	}

	function lfSetLimitAmt(){
		$('#lblLimitCare').text(__num2str($('#infoClient').attr('claimCare')));
		$('#lblLimitBath').text(__num2str($('#infoClient').attr('claimBath')));
		$('#lblLimitNurse').text(__num2str($('#infoClient').attr('claimNurse')));
		$('#lblLimitTot').text(__num2str($('#infoClient').attr('claimAmt')));
	}
</script>
<div id="tblSvcList0" style="display:none;">
	<table class="my_table" style="width:100%; margin-top:-1px;">
		<colgroup><?
			if ($lbLimitSet){?>
				<col width="80px" span="7"><?
			}else{?>
				<col width="90px" span="6"><?
			}?>
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">구분</th>
				<th class="head">수급(급여)계</th>
				<th class="head">본인부담액</th>
				<th class="head">초과</th>
				<th class="head">비급여</th>
				<th class="head">본인부담계</th><?
				if ($lbLimitSet){?>
					<th class="head">한도금액</th><?
				}?>
				<th class="head last">비고</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center">방문요양</th>
				<td class="center"><div id="lblSvcAmtC_1" value="0" class="right">0</div></td>
				<td class="center"><div id="lblSvcAmtC_2" value="0" class="right">0</div></td>
				<td class="center"><div id="lblSvcAmtC_5" value="0" class="right">0</div></td>
				<td class="center"><div id="lblSvcAmtC_3" value="0" class="right">0</div></td>
				<td class="center"><div id="lblSvcAmtC_4" value="0" class="right">0</div></td><?
				if ($lbLimitSet){?>
					<td class="center"><div id="lblLimitCare" value="0" class="right">0</div></td><?
				}?>
				<td class="center last"></td>
			</tr>
			<tr>
				<th class="center">방문목욕</th>
				<td class="center"><div id="lblSvcAmtB_1" value="0" class="right">0</div></td>
				<td class="center"><div id="lblSvcAmtB_2" value="0" class="right">0</div></td>
				<td class="center"><div id="lblSvcAmtB_5" value="0" class="right">0</div></td>
				<td class="center"><div id="lblSvcAmtB_3" value="0" class="right">0</div></td>
				<td class="center"><div id="lblSvcAmtB_4" value="0" class="right">0</div></td><?
				if ($lbLimitSet){?>
					<td class="center"><div id="lblLimitBath" value="0" class="right">0</div></td><?
				}?>
				<td class="center last"></td>
			</tr>
			<tr>
				<th class="center">방문간호</th>
				<td class="center"><div id="lblSvcAmtN_1" value="0" class="right">0</div></td>
				<td class="center"><div id="lblSvcAmtN_2" value="0" class="right">0</div></td>
				<td class="center"><div id="lblSvcAmtN_5" value="0" class="right">0</div></td>
				<td class="center"><div id="lblSvcAmtN_3" value="0" class="right">0</div></td>
				<td class="center"><div id="lblSvcAmtN_4" value="0" class="right">0</div></td><?
				if ($lbLimitSet){?>
					<td class="center"><div id="lblLimitNurse" value="0" class="right">0</div></td><?
				}?>
				<td class="center last"></td>
			</tr>
			<tr>
				<th class="center bold">계</th>
				<td class="center bold"><div id="lblSvcAmtT_1" value="0" class="right">0</div></td>
				<td class="center bold"><div id="lblSvcAmtT_2" value="0" class="right">0</div></td>
				<td class="center bold"><div id="lblSvcAmtT_5" value="0" class="right">0</div></td>
				<td class="center bold"><div id="lblSvcAmtT_3" value="0" class="right">0</div></td>
				<td class="center bold"><div id="lblSvcAmtT_4" value="0" class="right">0</div></td><?
				if ($lbLimitSet){?>
					<td class="center bold"><div id="lblLimitTot" value="0" class="right">0</div></td><?
				}?>
				<td class="center last"></td>
			</tr>
			<tr>
				<td class="bottom last" colspan="<?=$lbLimitSet ? 8 : 7;?>">
					<div class="left">※ 명세서 및 영수증, 일정표 출력시 본인부담금액 원단위는 절사후 출력됩니다.</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>