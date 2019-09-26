<table class="my_table my_border_blue" style="width:100%;">
	<tbody>
		<tr>
			<th class="right">
			<?
				echo '<span class="btn_pack m icon"><span class="list"></span><button type="button" onFocus="this.blur();" onclick="go_list(); return false;">리스트</button></span> ';

				$human_mode = $_GET['human_mode'];

				if (!isset($human_mode)) $human_mode = 1;

				switch($human_mode){
					case 1:
						echo '<span class="btn_pack m icon"><span class="download"></span><button type="button" onFocus="this.blur();" onclick="go_human_reg(); return false;">작성</button></span>';
						break;
					default:
						echo '<span class="btn_pack m icon"><span class="save"></span><button type="button" onFocus="this.blur();" onclick="go_save(); return false;">저장</button></span> ';
						echo '<span class="btn_pack m"><button type="button" onFocus="this.blur();" onclick="go_human_list(); return false;">취소</button></span>';
				}
			?>
			</th>
		</tr>
	</tbody>
</table>