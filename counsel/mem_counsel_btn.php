<table class="my_table my_border_blue" style="width:100%;">
	<tbody>
		<tr>
			<th class="right" >
			<?
				if ($is_path == 'counsel'){
					if ($parent_id == 100){
						echo '<span class="btn_pack m icon"><span class="list"></span><button type="button" onFocus="this.blur();" onclick="location.href=\'../yoyangsa/manage.php\'; return false;">리스트</button></span> ';
					}else if ($parent_id == 110){
						echo '<span class="btn_pack m icon"><span class="list"></span><button type="button" onFocus="this.blur();" onclick="location.href=\'../yoyangsa/mem_app.php\'; return false;">리스트</button></span> ';
					}else{
						//echo '<span class="btn_pack m icon"><span class="list"></span><button type="button" onFocus="this.blur();" onclick="list(); return false;">리스트</button></span> ';
					}
					echo '<span class="btn_pack m icon"><span class="save"></span><button type="button" onFocus="this.blur();" onclick="form_save(); return false;">저장</button></span> ';
					echo '<span class="btn_pack m icon" style="margin-right:3px;"><span class="refresh"></span><button type="button" onFocus="this.blur();" onclick="form_reset(); return false;">리셋</button></span>';

					if ($counsel_mode == 2){
						echo '<span class="btn_pack m icon"><span class="delete"></span><button type="button" onFocus="this.blur();" onclick="form_delete(); return false;">삭제</button></span>';
					}
				}else{
					if ($mst[$basic_kind]['m02_key']){
						echo '<div style="float:left;" >
								<span class="btn_pack m"><button onclick="lfSetSign(\'\', \'MEM_'.$mst[$basic_kind]['m02_key'].'\', \'MEM\');">Sign 등록</button></span>
							  </div>';
					}

					echo '<div style="float:right;">';
					echo '<span class="btn_pack m icon"><span class="list"></span><button type="button" onFocus="this.blur();" onclick="go_list('.$page.'); return false;">리스트</button></span> ';
					echo '<span class="btn_pack m icon"><span class="save"></span><button type="button" onFocus="this.blur();" onclick="go_save(); return false;">저장</button></span>';
					echo '</div>';

					if ($menu_mode == 'HUMAN'){
						echo ' <span class=\'btn_pack m icon\'><span class="pdf"></span><button type=\'button\' onFocus=\'this.blur();\' onclick=\'show_pdf("'.$menu_mode.'"); return false;\'>출력</button></span>';
					}
				}
			?>
			</th>
		</tr>
	</tbody>
</table>