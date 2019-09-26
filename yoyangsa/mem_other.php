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
					<input id="chkCounselYn" name="chkCounselYn" type="checkbox" class="checkbox" value="Y" <?=($memOption['counsel_yn'] == 'Y' ? 'checked' : '');?>><label for="chkCounselYn">상담지원 겸직여부</label>
				</td>
			</tr>
		</tbody>
	</table>
</div>