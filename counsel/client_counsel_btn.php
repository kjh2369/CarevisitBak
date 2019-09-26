<?
	if ($is_path != 'counsel') return;

	if (empty($tmp_btn_grp)){
		$style = 'margin-bottom:10px;';
	}else{
		$style = 'margin-bottom:10px; margin-top:10px;';
	}


?>
<div style="<?=$style;?>">
<table class="my_table my_border_blue" style="width:100%;">
	<tbody>
		<tr>
			<th class="right">
			<?
				if ($is_path == 'counsel'){
					if ($parent_id == 110){
						echo '<span class="btn_pack m icon"><span class="list"></span><button type="button" onFocus="this.blur();" onclick="location.href=\'../sugupja/client_app.php\';">리스트</button></span> ';
					}else{
						if($_POST['counsel_seq']){ 
							echo '<span class="btn_pack m"><button type="button" onFocus="this.blur();" onclick="form_copy();">복사</button></span> ';
						}
						echo '<span class="btn_pack m icon"><span class="list"></span><button type="button" onFocus="this.blur();" onclick="list();">리스트</button></span> ';
					}
					echo '<span class="btn_pack m icon"><span class="save"></span><button type="button" onFocus="this.blur();" onclick="form_save();">저장</button></span> ';
					echo '<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onFocus="this.blur();" onclick="form_reset();">리셋</button></span>';

					if ($counsel_mode == 2){
						echo '<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onFocus="this.blur();" onclick="form_delete();">삭제</button></span>';
					}
				}
			?>
			</th>
		</tr>
	</tbody>
</table>
</div>
<?
	$tmp_btn_grp = true;
?>