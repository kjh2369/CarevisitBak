<?
	#######################################################################
	#
	# 재가요양
	#

	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	$current_svc_nm = $conn->kind_name($k_list, $__CURRENT_SVC_ID__, 'id');
	$body_w = '100%';

	#
	#######################################################################

	include('./client_reg_sub_reason.php');

	$sql = "select m03_byungmung as sick_gbn
			,      m03_disease_nm as sick_nm
			,      m03_stat_nogood as nogood
			,      m03_yoyangsa1 as mem_cd1
			,      m03_yoyangsa1_nm as mem_nm1
			,      m03_yoyangsa2 as mem_cd2
			,      m03_yoyangsa2_nm as mem_nm2
			,      m03_partner as partner
			,      m03_bath_add_yn as bath_add
			,      m03_injung_no as conf_no
			,      m03_injung_from as conf_from_dt
			,      m03_injung_to as conf_to_dt
			,      m03_ylvl as lvl
			,      m03_skind as kind
			,      m03_kupyeo_max as pay_max
			,      m03_kupyeo_1 as pay1
			,      m03_kupyeo_2 as pay2
			,      m03_bonin_yul as pay_rate
			  from m03sugupja
			 where m03_ccode  = '$code'
			   and m03_mkind  = '$__CURRENT_SVC_CD__'
			   and m03_jumin  = '$jumin'
			   and m03_del_yn = 'N'";

	$client = $conn->get_array($sql);

	if (!$client){
		$client['sick_gbn'] = '9';
		$client['nogood']   = 'N';
		$client['partner']  = 'N';
		$client['bath_add'] = 'N';
		$client['lvl']      = '9';
		$client['kind']		= '1';
		$client['pay1']     = 0;
		$client['pay2']     = 0;
		$client['pay_rate'] = 0;
	}

	if (empty($client['pay1'])){
		$client['pay1'] = $max_amount = $conn->_limit_pay($client['lvl'], date('Ym', mktime()));
	}
?>
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; border-bottom:none;">
	<colgroup>
		<col width="80px" span="2">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th rowspan="2">질병</th>
			<th>병명</th>
			<td>
			<?
				if ($view_type == 'read'){
					$sql = "select m81_name as nm
							  from m81gubun
							 where m81_gbn  = 'DAS'
							   and m81_code = '".$client['sick_gbn']."'";

					$tmp = $conn->get_data($sql);

					echo '<div class=\'left\'>'.$tmp.'</div>';
				}else{
					$sql = "select m81_code as cd
							,      m81_name as nm
							  from m81gubun
							 where m81_gbn = 'DAS'";

					$conn->query($sql);
					$conn->fetch();

					$row_count = $conn->row_count();

					for($ii=0; $ii<$row_count; $ii++){
						$row =$conn->select_row($ii);

						echo '<input name=\''.$__CURRENT_SVC_ID__.'_byungMung\' type=\'radio\' class=\'radio\' value=\''.$row['cd'].'\' '.($row['cd'] == $client['sick_gbn'] ? 'checked' : '').' onclick=\'check_sick("'.$__CURRENT_SVC_ID__.'_byungMung","'.$row['cd'].'");\'><a href=\'#\' onclick=\'check_sick("'.$__CURRENT_SVC_ID__.'_byungMung","'.$row['cd'].'"); return false;\'>'.$row['nm'].'</a>';
					}

					$conn->row_free();
				}
			?>
			</td>
		</tr>
		<tr>
			<th>기타병명</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['sick_nm'].'</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_diseaseNm\' type=\'text\' value=\''.$client['sick_nm'].'\' style=\'width:100%;\'>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th colspan="2">20일초과, 90분가능여부</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.($client['nogood'] == 'Y' ? '예' : '아니오').'</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_statNogood\' type=\'radio\' class=\'radio\' value=\'Y\' '.($client['nogood'] == 'Y' ? 'checked' : '').'>예';
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_statNogood\' type=\'radio\' class=\'radio\' value=\'N\' '.($client['nogood'] != 'Y' ? 'checked' : '').'>아니오';
				}
			?>
			</td>
		</tr>
		<tr>
			<th rowspan="2">요양보호사</th>
			<th>주요양보호사</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['mem_nm1'].'</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_mem_cd1\' type=\'hidden\' value=\''.$ed->en($client['mem_cd1']).'\' tag=\''.$ed->en($client['mem_cd1']).'\'>';
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_mem_nm1\' type=\'text\'   value=\''.$client['mem_nm1'].'\' style=\'background-color:#eeeeee; margin-top:3px;\' readOnly> ';
					echo '<span class=\'btn_pack find\' style=\'margin-top:1px; margin-left:-5px;\' onclick=\'__find_yoyangsa("'.$code.'","0",document.getElementById("'.$__CURRENT_SVC_ID__.'_mem_cd1"),document.getElementById("'.$__CURRENT_SVC_ID__.'_mem_nm1")); check_partner("'.$__CURRENT_SVC_ID__.'_mem_cd1","'.$__CURRENT_SVC_ID__.'_partner");\'></span>';
					echo '<span class=\'btn_pack m\' style=\'margin-top:2px;\'><button type=\'button\' onclick=\'clear_mem("'.$__CURRENT_SVC_ID__.'_mem_cd1","'.$__CURRENT_SVC_ID__.'_mem_nm1"); check_partner("'.$__CURRENT_SVC_ID__.'_mem_cd1","'.$__CURRENT_SVC_ID__.'_partner");\'>삭제</button></span>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th>부요양보호사</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['mem_nm2'].'</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_mem_cd2\' type=\'hidden\' value=\''.$ed->en($client['mem_cd2']).'\' tag=\''.$ed->en($client['mem_cd2']).'\'>';
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_mem_nm2\' type=\'text\'   value=\''.$client['mem_nm2'].'\' style=\'background-color:#eeeeee; margin-top:3px;\' readOnly> ';
					echo '<span class=\'btn_pack find\' style=\'margin-top:1px; margin-left:-5px;\' onclick=\'__find_yoyangsa("'.$code.'","0",document.getElementById("'.$__CURRENT_SVC_ID__.'_mem_cd2"),document.getElementById("'.$__CURRENT_SVC_ID__.'_mem_nm2"));\'></span>';
					echo '<span class=\'btn_pack m\' style=\'margin-top:2px;\'><button type=\'button\' onclick=\'clear_mem("'.$__CURRENT_SVC_ID__.'_mem_cd2","'.$__CURRENT_SVC_ID__.'_mem_nm2");\'>삭제</button></span>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th colspan="2">주 요양보호사 배우자여부</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.($client['partner'] == 'Y' ? '예' : '아니오').'</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_partner\' type=\'radio\' class=\'radio\' value=\'Y\' '.($client['partner'] == 'Y' ? 'checked' : '').'>예';
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_partner\' type=\'radio\' class=\'radio\' value=\'N\' '.($client['partner'] != 'Y' ? 'checked' : '').'>아니오';
				}
			?>
			</td>
		</tr>
		<tr>
			<th>가족보호사</th>
			<td colspan="2">
				<table id="tblFamily" class="my_table" style="width:100%;">
					<colgroup>
						<col width="40px">
						<col width="110px">
						<col width="110px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="head bottom">No</th>
							<th class="head bottom">요양보호사</th>
							<th class="head bottom">관계</th>
							<th class="head bottom last"><span class="btn_pack m"><button type="button" onclick="_clientFamilyAddRow();">추가</button></span></th>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th colspan="2">목욕초과산정여부</th>
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
			<th rowspan="2">장기요양보험</th>
			<th>인정번호</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['conf_no'].'</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_injungNo\' type=\'text\' value=\''.$client['conf_no'].'\' style=\'width:150px;\' onKeyDown=\'__enterFocus();\' onFocus=\'__replace(this, "-", "");\' onBlur=\'this.value=__formatString(this.value, "#####-######-###");\'>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th>유효기간</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$myF->dateStyle($client['conf_from_dt']),' ~ '.$myF->dateStyle($client['conf_to_dt']).'</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_injungFrom\' type=\'text\' value=\''.$myF->dateStyle($client['conf_from_dt']).'\' tag=\''.$client['conf_from_dt'].'\' class=\'date\'> ~ ';
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_injungTo\'   type=\'text\' value=\''.$myF->dateStyle($client['conf_to_dt']).'\'   tag=\''.$client['conf_to_dt'],'\'   class=\'date\'>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th>장기요양등급</th>
			<td colspan="2">
			<?
				if ($view_type == 'read'){
					$sql = "select m81_name as nm
							  from m81gubun
							 where m81_gbn  = 'LVL'
							   and m81_code = '".$client['lvl']."'";

					$tmp = $conn->get_data($sql);

					echo '<div class=\'left\'>'.$tmp.'</div>';
				}else{
					$sql = "select m81_code as cd
							,      m81_name as nm
							  from m81gubun
							 where m81_gbn = 'LVL'";

					$conn->query($sql);
					$conn->fetch();

					$row_count = $conn->row_count();

					for($ii=0; $ii<$row_count; $ii++){
						$row =$conn->select_row($ii);

						echo '<input name=\''.$__CURRENT_SVC_ID__.'_lvl\' type=\'radio\' class=\'radio\' value=\''.$row['cd'].'\' tag=\''.$client['lvl'].'\' onclick=\'set_max_pay("'.$__CURRENT_SVC_ID__.'",__getObject("'.$__CURRENT_SVC_ID__.'_kupyeoMax"), this.value); set_my_yul("'.$__CURRENT_SVC_ID__.'",__get_value(document.getElementsByName("'.$__CURRENT_SVC_ID__.'_kind")), __get_tag(document.getElementsByName("'.$__CURRENT_SVC_ID__.'_kind")));\' '.($row['cd'] == $client['lvl'] ? 'checked' : '').'>'.$row['nm'];
					}

					$conn->row_free();
				}
			?>
			</td>
		</tr>
		<tr>
			<th>급여한도액</th>
			<td colspan="2">
			<?
				if ($view_type == 'read'){
					$sql = "select m91_kupyeo
							  from m91maxkupyeo
							 where m91_code = '".$client['lvl']."'
							   and replace(left(now(), 10), '-', '') between m91_sdate and m91_edate";

					$tmp = $conn->get_data($sql);
					$tmp = number_format($tmp);

					echo '<div class=\'left\'>'.$tmp.'</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_kupyeoMax\' type=\'text\' value=\'0\' maxlength=\'10\' class=\'number\' readOnly>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th>수급자구분</th>
			<td colspan="2">
			<?
				if ($view_type == 'read'){
					$sql = "select m81_name as nm
							  from m81gubun
							 where m81_gbn  = 'STP'
							   and m81_code = '".$client['kind']."'";

					$tmp = $conn->get_data($sql);

					echo '<div class=\'left\'>'.$tmp.'</div>';
				}else{
					$sql = "select m81_code as cd
							,      m81_name as nm
							  from m81gubun
							 where m81_gbn = 'STP'
							 order by m81_seq";

					$conn->query($sql);
					$conn->fetch();

					$row_count = $conn->row_count();

					echo '<table id=\'tmp_tbl\' style=\'width:100%;\'>';

					$tr = false;

					for($ii=0; $ii<$row_count; $ii++){
						$row =$conn->select_row($ii);

						if ($ii % 2 == 0){
							if ($tr) echo '</tr>';

							$tr = true;
							echo '<tr>';
						}

						echo '<td class=\'bottom last\' style=\'height:22px; line-height:1em;\'>';
						echo '<input name=\''.$__CURRENT_SVC_ID__.'_kind\' type=\'radio\' class=\'radio\' value=\''.$row['cd'].'\' tag=\''.$client['kind'].'\' onclick=\'set_my_yul("'.$__CURRENT_SVC_ID__.'",__get_value(document.getElementsByName("'.$__CURRENT_SVC_ID__.'_kind")), __get_tag(document.getElementsByName("'.$__CURRENT_SVC_ID__.'_kind")));\' '.($row['cd'] == $client['kind'] ? 'checked' : '').'>'.$row['nm'];
						echo '</td>';
					}

					echo '</tr>';
					echo '</table>';

					$conn->row_free();
				}
			?>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col width="120px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>청구한도금액</th>
			<td colspan="3">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.number_format($client['pay1']).'</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_kupyeo1\' type=\'text\' value=\''.number_format($client['pay1']).'\' tag=\''.$client['pay1'].'\' maxlength=\'10\' class=\'number\' style=\'background-color:#eeeeee;\' onchange=\'set_pay("'.$__CURRENT_SVC_ID__.'");\'; readOnly>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th>본인부담율</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['pay_rate'].'%</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_boninYul\' type=\'text\' value=\''.$client['pay_rate'].'\' tag=\''.$client['pay_rate'].'\' maxlength=\'3\' class=\'number\' onKeyDown=\'__onlyNumber(this,".");\' onchange=\'set_pay("'.$__CURRENT_SVC_ID__.'");\' alt=\'not\'>';
				}
			?>
			</td>
			<th>본인부담금</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.number_format($client['pay2']).'</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_kupyeo2\' type=\'text\' value=\''.number_format($client['pay2']).'\' maxlength=\'10\' class=\'number\' style=\'background-color:#eeeeee;\' readOnly>';
				}
			?>
			</td>
		</tr>
	</tbody>
</table>
<?
	unset($client);
?>