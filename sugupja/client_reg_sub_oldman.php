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

	$sql = "select m03_vlvl as gbn
			,      m03_ylvl as lvl
			,      m03_skind as kind
			,      m03_overtime as overtime
			,      m03_kupyeo_2 as tottime
			,      m03_kupyeo_max as pay_max
			,      m03_kupyeo_1 as pay_1
			  from m03sugupja
			 where m03_ccode  = '$code'
			   and m03_mkind  = '$__CURRENT_SVC_CD__'
			   and m03_jumin  = '$jumin'
			   and m03_del_yn = 'N'";

	$client = $conn->get_array($sql);

	if (!$client){
		$client['gbn']      = 'V';
		$client['lvl']      = '1';
		$client['kind']     = '3';
		$client['overtime'] = 0;
		$client['tottime']  = 0;
	}
?>
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>서비스구분</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.($client['gbn'] == 'V' ? '방문' : '주간보호').'</div>';
				}else{?>
					<input name="<?=$__CURRENT_SVC_ID__;?>_gbn" type="radio" class="radio" value="V" tag="<?=$client['gbn'];?>" onclick="set_svc_time('<?=$__CURRENT_SVC_ID__;?>_gbn','<?=$client['lvl'];?>');" <? if($client['gbn'] == 'V'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_gbn')[0].checked = true; set_svc_time('<?=$__CURRENT_SVC_ID__;?>_gbn','<?=$client['lvl'];?>'); return false;">방문</a>
					<input name="<?=$__CURRENT_SVC_ID__;?>_gbn" type="radio" class="radio" value="D" tag="<?=$client['gbn'];?>" onclick="set_svc_time('<?=$__CURRENT_SVC_ID__;?>_gbn','<?=$client['lvl'];?>');" <? if($client['gbn'] == 'D'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_gbn')[1].checked = true; set_svc_time('<?=$__CURRENT_SVC_ID__;?>_gbn','<?=$client['lvl'];?>'); return false;">주간보호</a>
					<input name="<?=$__CURRENT_SVC_ID__;?>_tmp_lvl" type="hidden" value="<?=$client['lvl'];?>"><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th>서비스시간</th>
			<td id="<?=$__CURRENT_SVC_ID__;?>_svc_time">
			<?
				if ($view_type == 'read'){
					if ($client['gbn'] == 'V'){
						$tmp = ' 시간';
					}else{
						$tmp = ' 일';
					}

					$date = date('Ymd', mktime());
					$suga = $myF->voucher_suga($__CURRENT_SVC_ID__, $client['gbn']);

					$sql = "select person_conf_time
							  from suga_person
							 where org_no          = '$code'
							   and person_from_dt <= '$date'
							   and person_to_dt   >= '$date'
							   and person_code  like '$suga%'
							   and person_id       = '".$client['lvl']."'
							 order by person_from_dt desc, person_to_dt desc
							 limit 1";

					$tmp = $conn->get_data($sql).$tmp;

					echo '<div class=\'left\'>'.$tmp.'</div>';
				}else{
				}
			?>
			</td>
		<tr>
		</tr>
			<th>소득등급</th>
			<td>
			<?
				if ($view_type == 'read'){
					$sql = "select lvl_nm
							  from income_lvl
							 where lvl_cd in ('21', '22', '23', '99')
							   and lvl_id = '".$client['kind']."'";

					echo '<div class=\'left\'>'.$conn->get_data($sql).'</div>';
				}else{
					echo $conn->income_lvl($__CURRENT_SVC_ID__, $client['kind'], "'21', '22', '23', '99'");
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
			<th>총서비스시간</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['tottime'].' 시간</div>';
				}else{?>
					<input name="<?=$__CURRENT_SVC_ID__;?>_kupyeo2" type="text" value="<?=$client['tottime'];?>" maxlength="10" class="number" readOnly style="width:70px;">시간<?
				}
			?>
			</td>
			<th></th>
			<td></td>
		</tr>
	</tbody>
</table>
<input name="<?=$__CURRENT_SVC_ID__;?>_overTime" type="hidden" value="0">