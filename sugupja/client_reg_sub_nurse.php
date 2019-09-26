<?
	#######################################################################
	#
	#

	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	$current_svc_nm = $conn->kind_name($k_list, $__CURRENT_SVC_ID__, 'id');

	$body_w = '100%';

	#
	#######################################################################

	include('client_reg_sub_reason.php');

	$__SUGA_CD = 'VH'; //가사간병 수가코드

	$sql = "select m03_ylvl as lvl
			,      m03_skind as kind
			,      m03_kupyeo_max as pay_max
			,      m03_kupyeo_1 as pay_1
			  from m03sugupja
			 where m03_ccode  = '$code'
			   and m03_mkind  = '$__CURRENT_SVC_CD__'
			   and m03_jumin  = '$jumin'
			   and m03_del_yn = 'N'";

	$client = $conn->get_array($sql);

	if (!$client){
		$client['lvl']  = '1';
		$client['kind'] = '2';
	}
?>
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>서비스시간</th>
			<td>
			<?
				if ($view_type == 'read'){
					$sql = "select person_conf_time
							  from suga_person
							 where org_no         = '$code'
							   and person_code like 'VH0%'
							   and person_id      = '".$client['lvl']."'";

					echo '<div class=\'left\'>'.$conn->get_data($sql).'시간</div>';
				}else{
					$sql = "select person_code
							,      person_id
							,      person_conf_time
							,      person_amt1
							,      person_amt2
							  from suga_person
							 where org_no         = '$code'
							   and person_code like 'VH0%'";

					$conn->query($sql);
					$conn->fetch();

					$row_count = $conn->row_count();

					for($ii=0; $ii<$row_count; $ii++){
						$row = $conn->select_row($ii);

						echo '<input name=\''.$__CURRENT_SVC_ID__.'_lvl\' type=\'radio\' class=\'radio\' value=\''.$row['person_id'].'\' tag=\''.$client['lvl'].'\' time=\''.$row['person_conf_time'].'\' onclick=\'check_time("'.$__CURRENT_SVC_ID__.'","0","1","");\' '.($row['person_id'] == $client['lvl'] ? 'checked' : '').'>';
						echo '<a href=\'#\' onclick=\'check_obj("'.$__CURRENT_SVC_ID__.'_lvl", '.$ii.', "'.$__CURRENT_SVC_ID__.'","0","1",""); return false;\'>'.$row['person_conf_time'].'시간</a>';
					}

					$conn->row_free();
				}
			?>
			</td>
		</tr>
		<tr>
			<th>소득등급</th>
			<td>
			<?
				if ($view_type == 'read'){
					$sql = "select lvl_nm
							  from income_lvl
							 where lvl_cd in ('21', '22', '99')
							   and lvl_id = '".$client['kind']."'";

					echo '<div class=\'left\'>'.$conn->get_data($sql).'</div>';
				}else{
					echo $conn->income_lvl($__CURRENT_SVC_ID__, $client['kind'], "'21', '22', '99'");
				}
			?>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; <?=$view_type == 'read' ? '' : 'border-bottom:none;';?>">
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
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_kupyeoMax\' type=\'text\' value=\'0\' maxlength=\'10\' class=\'number\' readOnly>';
				}
			?>
			</td>
			<th>본인부담금</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.number_format($client['pay_1']).'</div>';
				}else{
					echo '<input name=\''.$__CURRENT_SVC_ID__.'_kupyeo1\' type=\'text\' value=\'0\' maxlength=\'10\' class=\'number\' readOnly>';
				}
			?>
			</td>
		</tr>
	</tbody>
</table>