<?
	##################################################################
	#
	#
	#
	##################################################################

	include_once("../inc/_http_uri.php");

	if ($is_path == 'counsel' || $is_path == 'report'){
		$class = 'my_table my_border';
		$style = '';
	}else{
		$class = 'my_table my_border_blue';
		$style = 'width:100%;';
	}

?>
<div style="<?=($is_path != 'counsel' && $is_path != 'report' ? 'margin-left:10px; margin-right:10px;' : '');?>">
	<table class="<?=$class;?>" style="<?=$style;?>">
		<colgroup>
			<col width="100px">
			<?
				if ($is_path == 'counsel'){?>
					<col width="130px">
					<col width="100px"><?
				}
			?>
			<col>
			<?
				if ($is_path == 'client_reg'){
					echo '<col width=\'70px\'>';
				}
			?>
		</colgroup>
		<tbody>
			<?
				if ($is_path == 'counsel'){?>
					<tr>
						<th>기관기호</th>
						<td class="left"><?=$_SESSION['userCenterGiho'];?></td>
						<th>기관명</th>
						<td class="left last"><?=$name;?></td>
						<?
							if ($is_path == 'client_reg'){
								echo '<td class=\'center last\'>';
								echo '<a href=\'#\' onclick=\'show_counsel();\'>뒤로</a>';
								echo '</td>';
							}
						?>
					</tr><?
				}
			?>
			<!--tr>
				<th>상담구분</th>
				<td class="last" colspan="3">
				<?
					$kind_list = $conn->kind_list($code, true);

					if ($counsel_seq == 0){
						if (is_array($kind_list)){
							foreach($kind_list as $k => $k_list){
								if ($k_list['id'] < 30){
									echo '<input name=\'counsel_kind\' type=\'radio\' class=\'radio\' value=\''.$k_list['code'].'\' onclick=\'set_counsel_kind('.$k_list['code'].');\''.($counsel_kind == $k_list['code'] ? 'checked' : '').'><a href=\'#\' onclick=\'set_counsel_kind('.$k_list['code'].'); return false;\'>'.$k_list['name'].'</a>';
								}
							}
						}

						#echo '<input name=\'counsel_kind\' type=\'radio\' class=\'radio\' value=\'1\' onclick=\'set_counsel_kind(0);\''.($counsel_kind == 1 ? 'checked' : '').'><a href=\'#\' onclick=\'set_counsel_kind(0); return false;\'>재가/노인돌봄/가사간병/장애활동보조</a>';
						#echo '<input name=\'counsel_kind\' type=\'radio\' class=\'radio\' value=\'2\' onclick=\'set_counsel_kind(3);\''.($counsel_kind == 2 ? 'checked' : '').'><a href=\'#\' onclick=\'set_counsel_kind(3); return false;\'>산모신생아</a>';
					}else{
						/*
						if ($counsel_kind == 1){
							echo '<div class=\'left\'>재가/노인돌봄/가사간병/장애활동보조</div>';
						}else{
							echo '<div class=\'left\'>산모신생아</div>';
						}
						*/
						echo '<div class=\'left\'>'.$conn->kind_name_svc($counsel_kind).'</div>';
						echo '<input name=\'counsel_kind\' type=\'hidden\' value=\''.$counsel_kind.'\'>';
					}

					unset($kind_list);
				?>
				</td>
				<?
					if ($is_path == 'client_reg'){
						echo '<td class=\'last\'>&nbsp;</td>';
					}
				?>
			</tr-->
		</tbody>
	</table>
</div>