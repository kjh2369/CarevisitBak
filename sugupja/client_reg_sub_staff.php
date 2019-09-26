<?
	/******************************

		캡션

	******************************/
	switch($__CURRENT_SVC_ID__){
		case 23:
			$caption = '지정관리사';
			$curr_cd = $__CURRENT_SVC_CD__;
			break;

		case 31:
			$caption = '지정관리사';
			$curr_cd = '';
			break;

		case 24:
			$caption = '활동보조인';
			$curr_cd = $__CURRENT_SVC_CD__;
			break;

		default:
			$caption = '요양보호사';
			$curr_cd = $__CURRENT_SVC_CD__;
	}



	/******************************

		QUERY

	******************************/
	$sql = "select m03_yoyangsa1 as mem_cd1
			,      m03_yoyangsa1_nm as mem_nm1
			,      m03_yoyangsa2 as mem_cd2
			,      m03_yoyangsa2_nm as mem_nm2
			  from m03sugupja
			 where m03_ccode  = '$code'
			   and m03_mkind  = '$__CURRENT_SVC_CD__'
			   and m03_jumin  = '$jumin'";

	$client = $conn->get_array($sql);
?>

<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col width="30px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th rowspan="2"><?=$caption;?></th>
			<th class="head">주</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['mem_nm1'].'</div>';
				}else{
					echo '<input id=\'memCd1_'.$__CURRENT_SVC_CD__.'\' name=\''.$__CURRENT_SVC_ID__.'_mem_cd1\' type=\'hidden\' class=\'clsObjData\' value=\''.$ed->en($client['mem_cd1']).'\' tag=\''.$ed->en($client['mem_cd1']).'\'>';
					echo '<input id=\'memNm1_'.$__CURRENT_SVC_CD__.'\' name=\''.$__CURRENT_SVC_ID__.'_mem_nm1\' type=\'text\'   class=\'clsObjData\' value=\''.$client['mem_nm1'].'\' style=\'background-color:#eeeeee; margin-top:3px;\' readOnly> ';
					echo '<span class=\'btn_pack find\' style=\'margin-top:1px; margin-left:-5px;\' onclick=\'__find_yoyangsa("'.$code.'","'.$curr_cd.'",document.getElementById("'.$__CURRENT_SVC_ID__.'_mem_cd1"),document.getElementById("'.$__CURRENT_SVC_ID__.'_mem_nm1"));';

					if ($__CURRENT_SVC_CD__ == '0'){
						echo 'check_partner("'.$__CURRENT_SVC_ID__.'_mem_cd1","'.$__CURRENT_SVC_ID__.'_partner");';
					}
					echo '\'></span>';
					echo '<span class=\'btn_pack m\' style=\'margin-top:2px;\'><button type=\'button\' onclick=\'clear_mem("'.$__CURRENT_SVC_ID__.'_mem_cd1","'.$__CURRENT_SVC_ID__.'_mem_nm1");';

					if ($__CURRENT_SVC_CD__ == '0'){
						echo 'check_partner("'.$__CURRENT_SVC_ID__.'_mem_cd1","'.$__CURRENT_SVC_ID__.'_partner");';
					}
					echo '\'>삭제</button></span>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th class="head">부</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['mem_nm2'].'</div>';
				}else{
					echo '<input id=\'memCd2_'.$__CURRENT_SVC_CD__.'\' name=\''.$__CURRENT_SVC_ID__.'_mem_cd2\' type=\'hidden\' class=\'clsObjData\' value=\''.$ed->en($client['mem_cd2']).'\' tag=\''.$ed->en($client['mem_cd2']).'\'>';
					echo '<input id=\'memNm2_'.$__CURRENT_SVC_CD__.'\' name=\''.$__CURRENT_SVC_ID__.'_mem_nm2\' type=\'text\'   class=\'clsObjData\' value=\''.$client['mem_nm2'].'\' style=\'background-color:#eeeeee; margin-top:3px;\' readOnly> ';
					echo '<span class=\'btn_pack find\' style=\'margin-top:1px; margin-left:-5px;\' onclick=\'__find_yoyangsa("'.$code.'","'.$curr_cd.'",document.getElementById("'.$__CURRENT_SVC_ID__.'_mem_cd2"),document.getElementById("'.$__CURRENT_SVC_ID__.'_mem_nm2"));\'></span>';
					echo '<span class=\'btn_pack m\' style=\'margin-top:2px;\'><button type=\'button\' onclick=\'clear_mem("'.$__CURRENT_SVC_ID__.'_mem_cd2","'.$__CURRENT_SVC_ID__.'_mem_nm2");\'>삭제</button></span>';
				}
			?>
			</td>
		</tr>
	</tbody>
</table>