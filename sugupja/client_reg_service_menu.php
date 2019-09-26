<?
	if ($myF->_self() == 'client_counsel_case_reg'){
		$obj_nm_mode = 2;
		$obj_nm = 'case_use_svc[]';
		$obj_cd = 'case_kind_list[]';
		$this_class = '';
		$this_style = 'border-bottom:none;';
		$draw_header = false;
		$size_header = '75px';
		$draw_div = false;
	}else if ($myF->_self() == 'mem_counsel'){
		$obj_nm_mode = 1;
		$obj_nm = 'case_use_svc[]';
		$obj_cd = 'kind_list[]';
		$this_class = '';
		$this_style = 'border-bottom:none;';
		$draw_header = false;
		$size_header = '75px';
		$draw_div = false;
	}else{
		$obj_nm_mode = 1;
		$obj_nm = 'use_svc_';
		$obj_cd = 'kind_list[]';
		$this_class = 'my_border_blue';
		$this_style = '';
		$draw_header = true;
		$size_header = '80px';
		$draw_div = true;
	}

	if ($draw_div)
		echo '<div style=\'width:100%; padding:10px 10px 10px 10px;\'>';
?>

<table class="my_table <?=$this_class;?>" style="width:100%; <?=$this_style;?>">
	<colgroup>
		<col width="<?=$size_header;?>">
		<col>
	</colgroup>
	<tbody>
	<?
		$tr = false;

		for($i=0; $i<$k_cnt; $i++){
			$id = $k_list[$i]['id'];
			$cd = $k_list[$i]['code'];
			$no = $id + (10 - ($id % 10)) - 10;

			if ($i == 0 && $draw_header) echo '<tr><th class=\'head bold\' colspan=\'2\'>이용서비스</th></tr>';

			if ($tmp_no != $no){
				$tmp_no  = $no;

				if ($tr) echo '</td></tr>';

				$tr = true;

				echo '<tr>';

				if ($cd == '0'){
					echo '<th>장기요양</th>';
					echo '<td>';
				}else if ($cd >= '1' && $cd <= '4'){
					echo '<th>바우처</th>';
					echo '<td>';
				}else if ($cd == '5'){
					echo '<th>시설</th>';
					echo '<td>';
				}else{
					echo '<th class=\'bottom\'>기타유료</th>';
					echo '<td class=\'bottom\'>';
				}
			}

			if ($obj_nm_mode == 1){
				$svc_id = $obj_nm.$id;
			}else{
				$svc_id = $obj_nm;
			}

			if (!empty($compare_case_list)){
				$compare_pos = strpos($compare_case_list, $k_list[$i]['code'].'_'.$k_list[$i]['name']);

				if (is_numeric($compare_pos))
					$compare_style = 'font-weight:bold;';
				else
					$compare_style = '';

				$current_pos = strpos($current_case_list, $k_list[$i]['code'].'/');

				if (is_numeric($current_pos))
					$checked = 'checked';
				else
					$checked = '';

				echo '<input name=\''.$svc_id.'\' type=\'checkbox\' class=\'checkbox\' value=\''.$cd.'\' onclick=\'\' '.$checked.'><a href=\'#\' onclick=\'document.getElementsByName("'.$svc_id.'")['.$i.'].checked = !document.getElementsByName("'.$svc_id.'")['.$i.'].checked; return false;\' style=\''.$compare_style.'\'>'.$k_list[$i]['name'].'</a>';
			}else{
				if ($myF->_self() == 'mem_counsel'){
					if (StrLen(Str_Replace($cd.'/','',$case_if['case_svc_kind'])) != StrLen($case_if['case_svc_kind'])){
						$checked = 'checked';
					}else{
						$checked = '';
					}
					echo '<input id=\''.$svc_id.'\' name=\''.$svc_id.'\' type=\'checkbox\' class=\'checkbox\' value=\''.$cd.'\' '.$checked.'><label for=\''.$svc_id.'\'>'.$k_list[$i]['name'].'</label>';
				}else{
					if ($client_kind_list[$cd]['cd'] == $cd && $client_kind_list[$cd]['del'] == 'N'){
						$client_kind_list[$cd]['id']  = $id;
						$checked = 'checked';
					}else{
						$checked = '';
					}

					echo '<input id=\''.$svc_id.'\' name=\''.$svc_id.'\' type=\'checkbox\' class=\'checkbox\' value=\''.$cd.'\' onclick=\'check_svc(this,"'.$svc_id.'","click");\' '.$checked.'><label for=\''.$svc_id.'\'>'.$k_list[$i]['name'].'</label>';
				}
			}

			echo '<input name=\''.$obj_cd.'\' type=\'hidden\' value=\''.$cd.'_'.$id.'\'>';
		}

		echo '</td></tr>';
	?>
	</tbody>
</table>

<?
	if ($draw_div)
		echo '</div>';
?>