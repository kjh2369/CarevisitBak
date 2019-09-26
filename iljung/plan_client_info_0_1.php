<?
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
?>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="60px">
			<col width="80px">
			<col width="40px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center">고객명</th>
				<td class="center"></td>
				<th class="center">등급</th>
				<td class="center last"></td>
			</tr>
			<tr>
				<th class="center <?=($type == 'PLAN' ? 'bottom' : '');?>">인정번호</th>
				<td class="center <?=($type == 'PLAN' ? 'bottom' : '');?>"><div class="left" id="clientAppNo"><?=$laClientHis['app_no'];?></div></td>
				<th class="center <?=($type == 'PLAN' ? 'bottom' : '');?>">구분</th>
				<td class="center <?=($type == 'PLAN' ? 'bottom' : '');?> last"></td>
			</tr>
		</tbody>
	</table>

	<?
	unset($laClientHis);
	unset($laClientKind);
?>