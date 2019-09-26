<?
	if ($view_type == 'read'){?>
		<div style="clear:both; width:100%; margin-left:10px; margin-top:10px; margin-right:10px;"><?
	}else{?>
		<div style="clear:both; width:100%; margin-left:10px; margin-top:10px;"><?
	}?>

	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold">재가지원 기타사항</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">
					<label><input id="chkSupportYn" name="chkSupportYn" type="checkbox" class="checkbox" value="Y" <?=($memOption['support_yn'] == 'Y' ? 'checked' : '');?>>재가지원</label>
					<label><input id="chkResponseYn" name="chkResponseYn" type="checkbox" class="checkbox" value="Y" <?=($memOption['response_yn'] == 'Y' ? 'checked' : '');?>>자원연계</label>
					<label><input id="chkWithoutpayYn" name="chkWithoutpayYn" type="checkbox" class="checkbox" value="Y" <?=($memOption['withoutpay_yn'] == 'Y' ? 'checked' : '');?>>무급직원</label>
				</td>
			</tr>
		</tbody>
	</table>
</div>