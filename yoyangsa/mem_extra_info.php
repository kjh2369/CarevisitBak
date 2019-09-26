<?
	$sql = 'select extra500_1
			,      extra500_2
			,      extra500_3
			,      extra800_1
			,      extra800_2
			,      extra800_3
			  from mem_extra
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';
	$laExtra = $conn->get_array($sql);
?>
<div style="clear:both; margin-top:10px; margin-left:10px;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="60px">
			<col width="100px" span="6">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold" rowspan="2">구분</th>
				<th class="head bold" colspan="3">방문목욕 수당관리</th>
				<th class="head bold" colspan="3">방문간호 수당관리</th>
				<th class="head bold" rowspan="2">비고</th>
			</tr>
			<tr>
				<th class="head">미차량</th>
				<th class="head">차량(입욕)</th>
				<th class="head">차량(가정내입욕)</th>
				<th class="head">30분미만</th>
				<th class="head">30~60분미만</th>
				<th class="head">60분이상</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center">공단수가</th>
				<td class="center bold"><div class="right"><?=number_format($laSuga['CBFD1']['val']);?></div></td>
				<td class="center bold"><div class="right"><?=number_format($laSuga['CBKD1']['val']);?></div></td>
				<td class="center bold"><div class="right"><?=number_format($laSuga['CBKD2']['val']);?></div></td>
				<td class="center bold"><div class="right"><?=number_format($laSuga['CNWS1']['val']);?></div></td>
				<td class="center bold"><div class="right"><?=number_format($laSuga['CNWS2']['val']);?></div></td>
				<td class="center bold"><div class="right"><?=number_format($laSuga['CNWS3']['val']);?></div></td>
				<td class="center">&nbsp;</td>
			</tr>
			<tr>
				<th class="center">수당금액</th>
				<td class="center"><input id="txtExtra500_1" name="txtExtra500_1" type="text" value="<?=number_format($laExtra['extra500_1']);?>" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtExtra500_2" name="txtExtra500_2" type="text" value="<?=number_format($laExtra['extra500_2']);?>" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtExtra500_3" name="txtExtra500_3" type="text" value="<?=number_format($laExtra['extra500_3']);?>" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtExtra800_1" name="txtExtra800_1" type="text" value="<?=number_format($laExtra['extra800_1']);?>" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtExtra800_2" name="txtExtra800_2" type="text" value="<?=number_format($laExtra['extra800_2']);?>" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtExtra800_3" name="txtExtra800_3" type="text" value="<?=number_format($laExtra['extra800_3']);?>" class="number" style="width:100%;"></td>
				<td class="center">&nbsp;</td>
			</tr>
		</tbody>
	</table>
</div>