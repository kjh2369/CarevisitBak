<?
	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	######################################################
	# 변경레이어
		if ($view_type == 'read'){
		}else{
			include('client_reg_sub_layer.php');
		}
	#
	######################################################

	if (isset($client)) unset($client);

	$use_yn = 'Y';
?>
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head bold" colspan="2"><?=$current_svc_nm;?></th>
		</tr>
		<?
			if ($__CURRENT_SVC_ID__ == 11){
				$sql = "select m03_sugup_status as stat
						,      m03_gaeyak_fm as from_dt
						,      m03_gaeyak_to as to_dt
						  from m03sugupja
						 where m03_ccode  = '$code'
						   and m03_mkind  = '$__CURRENT_SVC_CD__'
						   and m03_jumin  = '$jumin'
						   and m03_del_yn = 'N'";

				$client = $conn->get_array($sql);

				if (!$client){
					$client['stat']    = '1';
					$client['from_dt'] = date('Ymd', mktime());
					$client['to_dt']   = '99991231';

					$use_yn = 'N';
				}

				echo '<tr>';
				echo '<th>수급상태</th>';
				echo '<td>';

				$status_list = $definition->SugupjaStatusList();

				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$definition->SugupjaStatusGbn($client['stat']).'</div>';
				}else{
					echo '<table style=\'width:100%;\'>';

					$obj_id = $__CURRENT_SVC_ID__.'_sugupStatus';

					$tr = false;

					for($ii=0; $ii<sizeOf($status_list); $ii++){
						if ($ii % 4 == 0){
							if ($tr) echo '</tr>';

							$tr = true;
							echo '<tr>';
						}
						echo '<td class=\'bottom last\' style=\'height:22px; line-height:1em;\'>';

						$obj_write = '';
						$obj_link  = '<a href=\'#\' onclick=\'document.getElementsByName("'.$obj_id.'")['.$ii.'].checked = true; check_status("'.$__CURRENT_SVC_ID__.'","'.$status_list[$ii]['end'].'"); return false;\'>'.$status_list[$ii]['name'].'</a>';

						if ($use_yn == 'N'){
							if ($ii != 0){
								$obj_write = 'disabled=\'true\'';
								$obj_link  = '<a style=\'cursor:default;\'>'.$status_list[$ii]['name'].'</a>';
							}
						}

						echo '<input name=\''.$obj_id.'\' type=\'radio\' class=\'radio\' value=\''.$status_list[$ii]['code'].'\' tag=\''.$client['stat'].'\' onclick=\'check_status("'.$__CURRENT_SVC_ID__.'","'.$status_list[$ii]['end'].'");\' '.($status_list[$ii]['code'] == $client['stat'] ? 'checked' : '').' '.$obj_write.'>';
						echo $obj_link;
						echo '</td>';
					}

					echo '</tr>';
					echo '</table>';
				}

				echo '</td>';
				echo '</tr>';
			}else if ($__CURRENT_SVC_ID__ > 20 && $__CURRENT_SVC_ID__ < 30){
				$sql = "select m03_sugup_status as stat
						,      m03_gaeyak_fm as from_dt
						,      m03_gaeyak_to as to_dt
						,      m03_stop_reason as reason
						  from m03sugupja
						 where m03_ccode  = '$code'
						   and m03_mkind  = '$__CURRENT_SVC_CD__'
						   and m03_jumin  = '$jumin'
						   and m03_del_yn = 'N'";

				$client = $conn->get_array($sql);

				if (!$client){
					$client['stat']    = '1';
					$client['from_dt'] = date('Ymd', mktime());
					$client['to_dt']   = '99991231';

					$use_yn = 'N';
				}

				if (empty($client['reason'])) $client['reason'] = '01';

				$obj_id = $__CURRENT_SVC_ID__.'_sugupStatus';

				echo '<tr>';
				echo '<th>이용상태</th>';
				echo '<td>';

				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.($client['stat'] == '1' ? '이용' : '중지').'</div>';
				}else{
					echo '<input name=\''.$obj_id.'\' type=\'radio\' class=\'radio\' value=\'1\' tag=\''.$client['stat'].'\' onclick=\'check_status("'.$__CURRENT_SVC_ID__.'","2");\' '.($client['stat'] == '1' ? 'checked' : '').'><a href=\'#\' onclick=\'check_status("'.$__CURRENT_SVC_ID__.'","2"); return false;\'>이용</a>';

					$obj_write = '';
					$obj_click = 'check_status("'.$__CURRENT_SVC_ID__.'","1");';
					$obj_link  = '<a href=\'#\' onclick=\''.$obj_click.' return false;\'>중지</a>';

					if ($use_yn == 'N'){
						$obj_write = 'disabled=\'true\'';
						$obj_click = '';
						$obj_link  = '<a style=\'cursor:default;\'>중지</a>';
					}

					echo '<input name=\''.$obj_id.'\' type=\'radio\' class=\'radio\' value=\'2\' tag=\''.$client['stat'].'\' onclick=\''.$obj_click.'\' '.($client['stat'] == '2' ? 'checked' : '').' '.$obj_write.'>'.$obj_link;
				}

				echo '</td>';
				echo '</tr>';

				if ($view_type == 'read'){
				}else{
					echo '<tr>';
					echo '<th>중지사유</th>';
					echo '<td>';

					$stop_list = $definition->GetStopReason();

					echo '<table style=\'width:100%;\'>';

					$obj_id = $__CURRENT_SVC_ID__.'_stopReason';

					$tr = false;

					for($ii=0; $ii<sizeof($stop_list); $ii++){
						if ($ii % 3 == 0){
							if ($tr) echo '</tr>';

							$tr = true;
							echo '<tr>';
						}

						$obj_write = '';
						$obj_link  = '<a href=\'#\' onclick=\'document.getElementsByName("'.$obj_id.'")['.$ii.'].checked = true; return false;\'>'.$stop_list[$ii]['nm'].'</a>';

						if ($use_yn == 'N' || $client['stat'] == '1'){
							$obj_write = 'disabled=\'true\'';

							if ($use_yn == 'N')
								$obj_link  = '<a style=\'cursor:default;\'>'.$stop_list[$ii]['nm'].'</a>';
						}

						echo '<td class=\'bottom last\' style=\'height:22px; line-height:1em;\'>';
						echo '<input name=\''.$obj_id.'\' type=\'radio\' class=\'radio\' value=\''.$stop_list[$ii]['cd'].'\' tag=\''.$client['reason'].'\' onclick=\'\' '.($stop_list[$ii]['cd'] == $client['reason'] ? 'checked' : '').' '.$obj_write.'>';
						echo $obj_link;
						echo '</td>';
					}

					echo '</tr>';
					echo '</table>';

					echo '</td>';
					echo '</tr>';
				}
			}else if ($__CURRENT_SVC_ID__ > 30 && $__CURRENT_SVC_ID__ < 40){
				$sql = "select m03_sugup_status as stat
						,      m03_gaeyak_fm as from_dt
						,      m03_gaeyak_to as to_dt
						  from m03sugupja
						 where m03_ccode  = '$code'
						   and m03_mkind  = '$__CURRENT_SVC_CD__'
						   and m03_jumin  = '$jumin'
						   and m03_del_yn = 'N'";

				$client = $conn->get_array($sql);

				if (!$client){
					$client['stat']    = '1';
					$client['from_dt'] = date('Ymd', mktime());
					$client['to_dt']   = '99991231';

					$use_yn = 'N';
				}

				$obj_id = $__CURRENT_SVC_ID__.'_sugupStatus';

				echo '<tr>';
				echo '<th>이용상태</th>';
				echo '<td>';

				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.($client['stat'] == '1' ? '이용' : '중지').'</div>';
				}else{
					echo '<input name=\''.$obj_id.'\' type=\'radio\' class=\'radio\' value=\'1\' tag=\''.$client['stat'].'\' onclick=\'check_status("'.$__CURRENT_SVC_ID__.'","2");\' '.($client['stat'] == '1' ? 'checked' : '').'><a href=\'#\' onclick=\'check_status("'.$__CURRENT_SVC_ID__.'","2"); return false;\'>이용</a>';

					$obj_write = '';
					$obj_event = 'check_status("'.$__CURRENT_SVC_ID__.'","1");';
					$obj_link  = '<a href=\'#\' onclick=\''.$obj_event.' return false;\'>중지</a>';

					if ($use_yn == 'N'){
						$obj_write = 'disabled=\'true\'';
						$obj_event = '';
						$obj_link  = '<a style=\'cursor:default;\'>중지</a>';
					}

					echo '<input name=\''.$obj_id.'\' type=\'radio\' class=\'radio\' value=\'2\' tag=\''.$client['stat'].'\' onclick=\''.$obj_event.'\' '.($client['stat'] == '2' ? 'checked' : '').' '.$obj_write.'>'.$obj_link;
				}

				echo '</td>';
				echo '</tr>';
			}

			echo '<tr>';
			echo '<th>계약기간</th>';
			echo '<td>';

			if ($view_type == 'read'){
				echo '<div class=\'left\'>'.$myF->dateStyle($client['from_dt']).' ~ '.$myF->dateStyle($client['to_dt']).'</div>';
			}else{
				if ($__CURRENT_SVC_ID__ == '11'){
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_gaeYakFm\' type=\'text\' value=\''.$myF->dateStyle($client['from_dt']).'\' tag=\''.$client['from_dt'].'\' class=\'date\' onchange=\'set_max_pay_svc("'.$__CURRENT_SVC_ID__.'");\'> ~ ';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_gaeYakFm\' type=\'text\' value=\''.$myF->dateStyle($client['from_dt']).'\' tag=\''.$client['from_dt'].'\' class=\'date\' onchange=\'check_from_to_dt("'.$__CURRENT_SVC_ID__.'",this);\' alt=\'check_from_to_dt("'.$__CURRENT_SVC_ID__.'",document.getElementById("'.$__CURRENT_SVC_ID__.'_gaeYakFm"));\'> ~ ';
				}

				echo '<input name=\''.$__CURRENT_SVC_ID__.'_gaeYakTo\' type=\'text\' value=\''.$myF->dateStyle($client['to_dt']).'\' tag=\''.$client['to_dt'].'\' class=\'date\' alt=\'not\'>';
			}

			echo '</td>';
			echo '</tr>';

			unset($client);
		?>
	</tbody>
</table>
<input name="<?=$__CURRENT_SVC_ID__?>_svcNm" type="hidden" value="<?=$__CURRENT_SVC_NM__;?>">
<input name="<?=$__CURRENT_SVC_ID__?>_writeMode" type="hidden" value="<?=$use_yn == 'N' ? 1 : 2;?>">

<?
	if ($__CURRENT_SVC_ID__ > 20 && $__CURRENT_SVC_ID__ < 30){
		include('client_reg_sub_staff.php');
	}
?>