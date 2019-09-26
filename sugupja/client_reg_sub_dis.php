<?
	#######################################################################
	#
	#

	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	$current_svc_nm = $conn->kind_name($k_list, $__CURRENT_SVC_ID__, 'id');

	$body_w = '100%';
	$today  = date('Y-m-d', mktime());

	#
	#######################################################################

	include('client_reg_sub_reason.php');

	$sql = "select m03_vlvl as gbn
			,      m03_ylvl as lvl
			,      m03_skind as kind
			,      m03_sgbn as gbn2
			,      m03_add_pay_gbn as addPayGbn
			,      m03_overtime as overtime
			,      m03_add_time1 as addTime1
			,      m03_add_time2 as addTime2
			,      m03_kupyeo_max as pay_max
			,      m03_kupyeo_1 as pay_1
			,      m03_kupyeo_2 as tottime
			,      m03_bath_add_yn as bath_add
			  from m03sugupja
			 where m03_ccode  = '$code'
			   and m03_mkind  = '$__CURRENT_SVC_CD__'
			   and m03_jumin  = '$jumin'
			   and m03_del_yn = 'N'";

	$client = $conn->get_array($sql);

	if (!$client){
		$client['gbn']      = 'A';
		$client['gbn2']     = '';
		$client['lvl']      = '1';
		$client['kind']     = '6';
		$client['overtime'] = 0;
		$client['addTime1'] = 0;
		$client['addTime2'] = 0;
	}
?>
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>목욕초과산정</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.($client['bath_add'] == 'Y' ? '예' : '아니오').'</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_bathAddYn\' type=\'radio\' class=\'radio\' value=\'Y\' '.($client['bath_add'] == 'Y' ? 'checked' : '').'>예';
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_bathAddYn\' type=\'radio\' class=\'radio\' value=\'N\' '.($client['bath_add'] != 'Y' ? 'checked' : '').'>아니오';
				}
			?>
			</td>
		</tr>
		<tr>
			<th>나이등급</th>
			<td>
			<?
				if ($view_type == 'read'){
					switch($client['gbn']){
						case 'A':
							$tmp = '성인';
							break;
						case 'C':
							$tmp = '아동';
							break;
						case 'O':
							$tmp = '65세도래자';
							break;
					}
					echo '<div class=\'left\'>'.$tmp.'</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_gbn\'
								 type=\'radio\' class=\'radio\' value=\'A\'
								 tag=\''.$client['gbn'].'\'
								 onclick=\'check_time("'.$__CURRENT_SVC_ID__.'","A",__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2"));\' '.($client['gbn'] == 'A' ? 'checked' : '').'><a href=\'#\'
								 onclick=\'document.getElementsByName("'.$__CURRENT_SVC_ID__.'_gbn")[0].checked = true;
										   check_time("'.$__CURRENT_SVC_ID__.'","A",__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2")); return false;\'>성인(18세이상)</a>';

					echo '<input name=\''.$__CURRENT_SVC_ID__.'_gbn\'
								 type=\'radio\' class=\'radio\' value=\'C\'
								 tag=\''.$client['gbn'].'\'
								 onclick=\'check_time("'.$__CURRENT_SVC_ID__.'","C",__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2"));\' '.($client['gbn'] == 'C' ? 'checked' : '').'><a href=\'#\' onclick=\'document.getElementsByName("'.$__CURRENT_SVC_ID__.'_gbn")[1].checked = true;
										   check_time("'.$__CURRENT_SVC_ID__.'","C",__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2")); return false;\'>아동(6세~18세미만)</a>';


					/**************************************************

						2011.11.01부터 "65세 도래자" 삭제한다.


					echo '<input name=\''.$__CURRENT_SVC_ID__.'_gbn\'
								 type=\'radio\' class=\'radio\' value=\'O\'
								 tag=\''.$client['gbn'].'\'
								 onclick=\'check_time("'.$__CURRENT_SVC_ID__.'","O",__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2"));\' '.($client['gbn'] == 'O' ? 'checked' : '').'><a href=\'#\' onclick=\'document.getElementsByName("'.$__CURRENT_SVC_ID__.'_gbn")[2].checked = true;
										   check_time("'.$__CURRENT_SVC_ID__.'","O",__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2")); return false;\'>65세도래자</a>';
					**************************************************/
				}
			?>
			</td>
		</tr>
		<tr>
			<th>활동지원등급</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['lvl'].'등급</div>';
				}else{
					echo '<div id=\'disLvl1\' style=\'float:left; width:auto;\'>
						  <input name=\''.$__CURRENT_SVC_ID__.'_lvl\'
								 type=\'radio\' class=\'radio\' value=\'1\'
								 tag=\''.$client['lvl'].'\'
								 onclick=\'check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),"1",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2"));\' '.($client['lvl'] == '1' ? 'checked' : '').'><a href=\'#\' onclick=\'document.getElementsByName("'.$__CURRENT_SVC_ID__.'_lvl")[0].checked = true;
										   check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),"1",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2")); return false;\'><span id=\'strLvl1\'>1등급</span></a>
						  </div>';


					echo '<div id=\'disLvl2\' style=\'float:left; width:auto;\'>
						  <input name=\''.$__CURRENT_SVC_ID__.'_lvl\'
								 type=\'radio\' class=\'radio\' value=\'2\'
								 tag=\''.$client['lvl'].'\'
								 onclick=\'check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),"2",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2"));\' '.($client['lvl'] == '2' ? 'checked' : '').'><a href=\'#\' onclick=\'document.getElementsByName("'.$__CURRENT_SVC_ID__.'_lvl")[1].checked = true;
										   check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),"2",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2")); return false;\'><span id=\'strLvl2\'>2등급</span></a>
						  </div>';


					echo '<div id=\'disLvl3\' style=\'float:left; width:auto;\'>
						  <input name=\''.$__CURRENT_SVC_ID__.'_lvl\'
								 type=\'radio\' class=\'radio\' value=\'3\'
								 tag=\''.$client['lvl'].'\'
								 onclick=\'check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),"3",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2"));\' '.($client['lvl'] == '3' ? 'checked' : '').'><a href=\'#\' onclick=\'document.getElementsByName("'.$__CURRENT_SVC_ID__.'_lvl")[2].checked = true;
										   check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),"3",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2")); return false;\'><span id=\'strLvl3\'>3등급</span></a>
						  </div>';


					echo '<div id=\'disLvl4\' style=\'float:left; width:auto;\'>
						  <input name=\''.$__CURRENT_SVC_ID__.'_lvl\'
								 type=\'radio\' class=\'radio\' value=\'4\'
								 tag=\''.$client['lvl'].'\'
								 onclick=\'check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),"4",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2"));\' '.($client['lvl'] == '4' ? 'checked' : '').'><a href=\'#\' onclick=\'document.getElementsByName("'.$__CURRENT_SVC_ID__.'_lvl")[3].checked = true;
										   check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),"4",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2")); return false;\'><span id=\'strLvl4\'>4등급</span></a>
						  </div>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th>추가급여분류</th>
			<td style="padding-top:3px; padding-bottom:3px;">
			<?
				/**************************************************

					2011.11.01부터 "특례등급"을 "추가급여"로 변경한다.

				if ($view_type == 'read'){
					switch($client['gbn']){
						case '8':
							$tmp = '특례180';
							break;
						case '2':
							$tmp = '특례120';
							break;
						default:
							$tmp = '해당없음';
							break;
					}
					echo '<div class=\'left\'>'.$tmp.'</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_gbn2\'
								 type=\'radio\' class=\'radio\' value=\'0\'
								 tag=\''.$client['gbn2'].'\'
								 onclick=\'check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),"0");\' '.($client['gbn2'] == '0' ? 'checked' : '').'><a href=\'#\' onclick=\'document.getElementsByName("'.$__CURRENT_SVC_ID__.'_gbn2")[0].checked = true;
										   check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),"0"); return false;\'>없음</a>';


					echo '<input name=\''.$__CURRENT_SVC_ID__.'_gbn2\'
								 type=\'radio\' class=\'radio\' value=\'8\'
								 tag=\''.$client['gbn2'].'\'
								 onclick=\'check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),"8");\' '.($client['gbn2'] == '8' ? 'checked' : '').'><a href=\'#\' onclick=\'document.getElementsByName("'.$__CURRENT_SVC_ID__.'_gbn2")[1].checked = true;
										   check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),"8"); return false;\'>특례180</a>';


					echo '<input name=\''.$__CURRENT_SVC_ID__.'_gbn2\'
								 type=\'radio\' class=\'radio\' value=\'2\'
								 tag=\''.$client['gbn2'].'\'
								 onclick=\'check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),"2");\' '.($client['gbn2'] == '2' ? 'checked' : '').'><a href=\'#\' onclick=\'document.getElementsByName("'.$__CURRENT_SVC_ID__.'_gbn2")[2].checked = true;
										   check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),"2"); return false;\'>특례120</a>';
				}
				**************************************************/

				if ($view_type == 'read'){
					$sql = 'select svc_gbn_nm
							  from suga_service_add
							 where svc_kind     = \''.$__CURRENT_SVC_CD__.'\'
							   and svc_gbn_cd   = \''.$client['gbn'].'\'
							   and svc_from_dt <= \''.$today.'\'
							   and svc_to_dt   >= \''.$today.'\'';

					$tmp_str = $conn->get_data($sql);

					echo '<div class=\'left\'>'.$tmp_str.'</div>';
				}else{
					/*********************************************************
						그룹
					*********************************************************/
					$sql = 'select svc_gbn_cd as cd
							,      svc_gbn_nm as nm
							,      svc_pay as pay
							  from suga_service_add
							 where svc_kind     = \''.$__CURRENT_SVC_CD__.'\'
							   and svc_from_dt <= \''.$today.'\'
							   and svc_to_dt   >= \''.$today.'\'
							   and svc_group    = \'R\'';

					$conn->query($sql);
					$conn->fetch();

					$row_count = $conn->row_count();

					echo '<div style=\'margin-bottom:5px; padding-bottom:5px; border-bottom:1px solid #cccccc;\'>';
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_gbn2\'
								 type=\'radio\' class=\'radio\' value=\'\'
								 tag=\''.($client['gbn2'] != '0' ? $client['gbn2'] : '').'\'
								 onclick=\'check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),"");\' checked><a href=\'#\'
								 onclick=\'document.getElementsByName("'.$__CURRENT_SVC_ID__.'_gbn2")[0].checked = true;
										   check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),""); return false;\'>해당없음</a><br>';

					for($ii=0; $ii<$row_count; $ii++){
						$row = $conn->select_row($ii);

						echo '<input name=\''.$__CURRENT_SVC_ID__.'_gbn2\'
									 type=\'radio\' class=\'radio\' value=\''.$row['cd'].'\'
									 tag=\''.$client['gbn2'].'\'
									 onclick=\'check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),"'.$row['cd'].'");\' '.($client['gbn2'] == $row['cd'] ? 'checked' : '').'><a href=\'#\'
									 onclick=\'document.getElementsByName("'.$__CURRENT_SVC_ID__.'_gbn2")['.($ii+1).'].checked = true;
											   check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),"'.$row['cd'].'"); return false;\'>'.$row['nm'].'</a><br>';
					}

					echo '</div>';

					$conn->row_free();


					/*********************************************************
						개별
					*********************************************************/
					$sql = 'select svc_gbn_cd as cd
							,      svc_gbn_nm as nm
							,      svc_pay as pay
							  from suga_service_add
							 where svc_kind     = \''.$__CURRENT_SVC_CD__.'\'
							   and svc_from_dt <= \''.$today.'\'
							   and svc_to_dt   >= \''.$today.'\'
							   and svc_group    = \'C\'';

					$conn->query($sql);
					$conn->fetch();

					$row_count = $conn->row_count();

					for($ii=0; $ii<$row_count; $ii++){
						$row = $conn->select_row($ii);

						$id = 'addPayGbn_'.$__CURRENT_SVC_ID__.'_'.$row['cd'];
						$nm = 'addPayGbn_'.$__CURRENT_SVC_ID__.'[]';

						if (is_numeric(strpos($client['addPayGbn'], '/'.$row['cd']))){
							$tmpVal = $row['cd'];
						}else{
							$tmpVal = '';
						}

						echo '<input id=\''.$id.'\' name=\''.$nm.'\' type=\'checkbox\' class=\'checkbox\' value=\''.$row['cd'].'\' tag=\''.$tmpVal.'\'
									 onclick=\'check_time("'.$__CURRENT_SVC_ID__.'",__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn"),__object_get_value("'.$__CURRENT_SVC_ID__.'_lvl"),__object_get_value("'.$__CURRENT_SVC_ID__.'_gbn2"));\' '.($tmpVal == $row['cd'] ? 'checked' : '').'>
									 <label for=\''.$id.'\'>'.$row['nm'].'</label><br>';
					}

					$conn->row_free();
				}
			?>
			</td>
		</tr>
		<tr>
			<th class="bottom">소득등급</th>
			<td class="bottom" style="height:98px;">
			<?
				if ($view_type == 'read'){
					$sql = "select lvl_nm
							  from income_lvl
							 where lvl_cd in ('21', '22', '26', '27', '28', '29', '99')
							   and lvl_id = '".$client['kind']."'";

					echo '<div class=\'left\'>'.$conn->get_data($sql).'</div>';
				}else{
					echo $conn->income_lvl($__CURRENT_SVC_ID__, $client['kind'], "'21', '22', '26', '27', '28', '29', '99'");
				}
			?>
			</td>
		</tr>
	</tbody>
</table>


<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-bottom:2px solid #0e69b0;">
	<colgroup>
		<col width='85px'>
		<col width='80px'>
		<col width='80px'>
		<col width='80px'>
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class='head'>급여구분</th>
			<th class='head'>합계</th>
			<th class='head'>시간</th>
			<th class='head'>지원금액</th>
			<th class='head'>본인부담금</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class='center'>기본급여</th>
			<td class='center'><input name='pay_stnd_tot'  type='text' value='0' class='number readonly' style='width:100%;' alt='not' readonly></td>
			<td class='center'><input name='pay_stnd_time' type='text' value='0' class='number readonly' style='width:100%;' alt='not' readonly></td>
			<td class='center'><input name='pay_stnd_use'  type='text' value='0' class='number readonly' style='width:100%;' alt='not' readonly></td>
			<td class='center'><input name='pay_stnd_self' type='text' value='0' class='number readonly' style='width:100%;' alt='not' readonly></td>
		</tr>
		<tr>
			<th class='center'>추가급여</th>
			<td class='center'><input name='pay_add_tot'  type='text' value='0' class='number readonly' style='width:100%;' alt='not' readonly></td>
			<td class='center'><input name='pay_add_time' type='text' value='0' class='number readonly' style='width:100%;' alt='not' readonly></td>
			<td class='center'><input name='pay_add_use'  type='text' value='0' class='number readonly' style='width:100%;' alt='not' readonly></td>
			<td class='center'><input name='pay_add_self' type='text' value='0' class='number readonly' style='width:100%;' alt='not' readonly></td>
		</tr>
		<tr>
			<th class='center'>시도비추가</th>
			<td class='center'><input name='pay_sido_tot'  type='text' value='0' class='number readonly' style='width:100%;' alt='not' readonly></td>
			<td class='center'><input name='pay_sido_time' type='text' value='<?=$client['addTime1'];?>' class='number readonly' style='width:100%; background-color:#f6f4d3;' onchange='check_time("<?=$__CURRENT_SVC_ID__;?>",__object_get_value("<?=$__CURRENT_SVC_ID__;?>_gbn"),__object_get_value("<?=$__CURRENT_SVC_ID__;?>_lvl"),__object_get_value("<?=$__CURRENT_SVC_ID__;?>_gbn2"));'></td>
			<td class='center'><input name='pay_sido_use'  type='text' value='0' class='number readonly' style='width:100%;' alt='not' readonly></td>
			<td class='center'><input name='pay_sido_self' type='text' value='0' class='number readonly' style='width:100%;' alt='not' readonly></td>
		</tr>
		<tr>
			<th class='center'>자치비추가</th>
			<td class='center'><input name='pay_jach_tot'  type='text' value='0' class='number readonly' style='width:100%;' alt='not' readonly></td>
			<td class='center'><input name='pay_jach_time' type='text' value='<?=$client['addTime2'];?>' class='number readonly' style='width:100%; background-color:#f6f4d3;' onchange='check_time("<?=$__CURRENT_SVC_ID__;?>",__object_get_value("<?=$__CURRENT_SVC_ID__;?>_gbn"),__object_get_value("<?=$__CURRENT_SVC_ID__;?>_lvl"),__object_get_value("<?=$__CURRENT_SVC_ID__;?>_gbn2"));'></td>
			<td class='center'><input name='pay_jach_use'  type='text' value='0' class='number readonly' style='width:100%;' alt='not' readonly></td>
			<td class='center'><input name='pay_jach_self' type='text' value='0' class='number readonly' style='width:100%;' alt='not' readonly></td>
		</tr>
		<tr>
			<th class='center'>총이용합계</th>
			<td class='center'><input name='pay_total_tot'  type='text' value='0' class='number readonly' style='width:100%; font-weight:bold;' alt='not' readonly></td>
			<td class='center'><input name='pay_total_time' type='text' value='0' class='number readonly' style='width:100%; font-weight:bold;' alt='not' readonly></td>
			<td class='center'><input name='pay_total_use'  type='text' value='0' class='number readonly' style='width:100%; font-weight:bold;' alt='not' readonly></td>
			<td class='center'><input name='pay_total_self' type='text' value='0' class='number readonly' style='width:100%; font-weight:bold;' alt='not' readonly></td>
		</tr>
	</tbody>
</table>


<!--table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; <?=$view_type == 'read' ? '' : 'border-bottom:none;';?>">
	<colgroup>
		<col width="80px">
		<col width="120px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>총지원금액</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.number_format($client['pay_max']).'</div>';
				}else{?>
					<input name="<?=$__CURRENT_SVC_ID__;?>_kupyeoMax" type="text" value="0" maxlength="10" class="number" readOnly><?
				}
			?>
			</td>
			<th>본인부담금</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.number_format($client['pay_1']).'</div>';
				}else{?>
					<input name="<?=$__CURRENT_SVC_ID__;?>_kupyeo1" type="text" value="0" maxlength="10" class="number" readOnly><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th>시도비추가</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['addTime1'].' 시간</div>';
				}else{?>
					<input name="<?=$__CURRENT_SVC_ID__;?>_addTime1" type="text" value="<?=$client['addTime1'];?>" tag="<?=$client['addTime1'];?>" maxlength="10" class="number" style="width:70px;">시간<?
				}
			?>
			</td>
			<th>자치비추가</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['addTime2'].' 시간</div>';
				}else{?>
					<input name="<?=$__CURRENT_SVC_ID__;?>_addTime2" type="text" value="<?=$client['addTime2'];?>" tag="<?=$client['addTime2'];?>" maxlength="10" class="number" style="width:70px;">시간<?
				}
			?>
			</td>
		</tr>
		<tr>
			<th>총서비스시간</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.($client['tottime']+$client['overtime']+$client['addTime1']+$client['addTime2']).' 시간</div>';
				}else{?>
					<input name="<?=$__CURRENT_SVC_ID__;?>_kupyeo2" type="text" value="0" maxlength="10" class="number"style="width:70px;" readOnly>시간<?
				}
			?>
			</td>
			<th></th>
			<td></td>
		</tr>
	</tbody>
</table-->

<input name="<?=$__CURRENT_SVC_ID__;?>_sugaTime" type="hidden" value="0">
<input name="<?=$__CURRENT_SVC_ID__;?>_overTime" type="hidden" value="0">
<input name="<?=$__CURRENT_SVC_ID__;?>_kupyeoMax" type="hidden" value="0">
<input name="<?=$__CURRENT_SVC_ID__;?>_kupyeo1" type="hidden" value="0">
<input name="<?=$__CURRENT_SVC_ID__;?>_kupyeo2" type="hidden" value="0">
<input name="<?=$__CURRENT_SVC_ID__;?>_addTime1" type="hidden" value="0">
<input name="<?=$__CURRENT_SVC_ID__;?>_addTime2" type="hidden" value="0">