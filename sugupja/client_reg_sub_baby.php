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
			,      m03_kupyeo_max as pay_max
			,      m03_kupyeo_1 as pay_1
			  from m03sugupja
			 where m03_ccode  = '$code'
			   and m03_mkind  = '$__CURRENT_SVC_CD__'
			   and m03_jumin  = '$jumin'
			   and m03_del_yn = 'N'";

	$client = $conn->get_array($sql);

	if (!$client){
		$client['gbn']  = '1';
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
			<th>서비스 구분</th>
			<td>
			<?
				if ($view_type == 'read'){
					switch($client['gbn']){
						case '1':
							$tmp = '단태아';
							break;
						case '2':
							$tmp = '쌍태아';
							break;
						case '3':
							$tmp = '삼태아';
							break;
					}
					echo '<div class=\'left\'>'.$tmp.'</div>';
				}else{?>
					<input name="<?=$__CURRENT_SVC_ID__;?>_gbn" type="radio" class="radio" value="1" tag="<?=$client['gbn'];?>" onclick="set_svc_time('<?=$__CURRENT_SVC_ID__;?>_gbn','1');" <? if($client['gbn'] == '1'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_gbn')[0].checked = true; set_svc_time('<?=$__CURRENT_SVC_ID__;?>_gbn','1'); return false;">단태아</a>
					<input name="<?=$__CURRENT_SVC_ID__;?>_gbn" type="radio" class="radio" value="2" tag="<?=$client['gbn'];?>" onclick="set_svc_time('<?=$__CURRENT_SVC_ID__;?>_gbn','2');" <? if($client['gbn'] == '2'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_gbn')[1].checked = true; set_svc_time('<?=$__CURRENT_SVC_ID__;?>_gbn','2'); return false;">쌍태아</a>
					<input name="<?=$__CURRENT_SVC_ID__;?>_gbn" type="radio" class="radio" value="3" tag="<?=$client['gbn'];?>" onclick="set_svc_time('<?=$__CURRENT_SVC_ID__;?>_gbn','3');" <? if($client['gbn'] == '3'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_gbn')[2].checked = true; set_svc_time('<?=$__CURRENT_SVC_ID__;?>_gbn','3'); return false;">삼태아</a><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th>서비스시간</th>
			<td id="<?=$__CURRENT_SVC_ID__;?>_svc_time">
			<?
				if ($view_type == 'read'){
					$date = date('Ymd', mktime());
					$suga = $myF->voucher_suga($__CURRENT_SVC_ID__, '0');

					$sql = "select person_conf_time
							  from suga_person
							 where org_no          = '$code'
							   and person_from_dt <= '$date'
							   and person_to_dt   >= '$date'
							   and person_code  like '$suga%'
							   and person_id       = '".$client['gbn']."'
							 order by person_from_dt desc, person_to_dt desc
							 limit 1";

					$tmp = $conn->get_data($sql);

					echo '<div class=\'left\'>'.$tmp.' 시간</div>';
				}else{
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
							 where lvl_cd in ('24', '25', '99')
							   and lvl_id = '".$client['kind']."'";

					echo '<div class=\'left\'>'.$conn->get_data($sql).'</div>';
				}else{
					echo $conn->income_lvl($__CURRENT_SVC_ID__, $client['kind'], "'24', '25', '99'");
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
	</tbody>
</table>


<?
	/*************************

		산모신생아 비급여 항목

	*************************/
	include('client_reg_sub_baby_addpay.php');
?>