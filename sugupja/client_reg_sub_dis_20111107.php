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
			,      m03_sgbn as gbn2
			,      m03_overtime as overtime
			,      m03_add_time1 as addTime1
			,      m03_add_time2 as addTime2
			,      m03_kupyeo_max as pay_max
			,      m03_kupyeo_1 as pay_1
			,      m03_kupyeo_2 as tottime
			  from m03sugupja
			 where m03_ccode  = '$code'
			   and m03_mkind  = '$__CURRENT_SVC_CD__'
			   and m03_jumin  = '$jumin'
			   and m03_del_yn = 'N'";

	$client = $conn->get_array($sql);

	if (!$client){
		$client['gbn']      = 'A';
		$client['gbn2']     = '0';
		$client['lvl']      = '1';
		$client['kind']     = '9';
		$client['overtime'] = 0;
	}
?>
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
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
				}else{?>
					<input name="<?=$__CURRENT_SVC_ID__;?>_gbn" type="radio" class="radio" value="A" tag="<?=$client['gbn'];?>" onclick="check_time('<?=$__CURRENT_SVC_ID__;?>','A',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_lvl'),__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn2'));" <? if($client['gbn'] == 'A'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_gbn')[0].checked = true; check_time('<?=$__CURRENT_SVC_ID__;?>','A',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_lvl'),__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn2')); return false;">성인</a>
					<input name="<?=$__CURRENT_SVC_ID__;?>_gbn" type="radio" class="radio" value="C" tag="<?=$client['gbn'];?>" onclick="check_time('<?=$__CURRENT_SVC_ID__;?>','C',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_lvl'),__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn2'));" <? if($client['gbn'] == 'C'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_gbn')[1].checked = true; check_time('<?=$__CURRENT_SVC_ID__;?>','C',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_lvl'),__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn2')); return false;">아동</a>
					<input name="<?=$__CURRENT_SVC_ID__;?>_gbn" type="radio" class="radio" value="O" tag="<?=$client['gbn'];?>" onclick="check_time('<?=$__CURRENT_SVC_ID__;?>','O',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_lvl'),__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn2'));" <? if($client['gbn'] == 'O'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_gbn')[2].checked = true; check_time('<?=$__CURRENT_SVC_ID__;?>','O',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_lvl'),__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn2')); return false;">65세도래자</a><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th>장애인정등급</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['lvl'].'등급</div>';
				}else{?>
					<input name="<?=$__CURRENT_SVC_ID__;?>_lvl" type="radio" class="radio" value="1" tag="<?=$client['lvl'];?>" onclick="check_time('<?=$__CURRENT_SVC_ID__;?>',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn'),'1',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn2'));" <? if($client['lvl'] == '1'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_lvl')[0].checked = true; check_time('<?=$__CURRENT_SVC_ID__;?>',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn'),'1',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn2')); return false;">1등급</a>
					<input name="<?=$__CURRENT_SVC_ID__;?>_lvl" type="radio" class="radio" value="2" tag="<?=$client['lvl'];?>" onclick="check_time('<?=$__CURRENT_SVC_ID__;?>',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn'),'2',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn2'));" <? if($client['lvl'] == '2'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_lvl')[1].checked = true; check_time('<?=$__CURRENT_SVC_ID__;?>',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn'),'2',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn2')); return false;">2등급</a>
					<input name="<?=$__CURRENT_SVC_ID__;?>_lvl" type="radio" class="radio" value="3" tag="<?=$client['lvl'];?>" onclick="check_time('<?=$__CURRENT_SVC_ID__;?>',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn'),'3',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn2'));" <? if($client['lvl'] == '3'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_lvl')[2].checked = true; check_time('<?=$__CURRENT_SVC_ID__;?>',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn'),'3',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn2')); return false;">3등급</a>
					<input name="<?=$__CURRENT_SVC_ID__;?>_lvl" type="radio" class="radio" value="4" tag="<?=$client['lvl'];?>" onclick="check_time('<?=$__CURRENT_SVC_ID__;?>',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn'),'4',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn2'));" <? if($client['lvl'] == '4'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_lvl')[3].checked = true; check_time('<?=$__CURRENT_SVC_ID__;?>',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn'),'4',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn2')); return false;">4등급</a><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th>특례구분</th>
			<td>
			<?
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
				}else{?>
					<input name="<?=$__CURRENT_SVC_ID__;?>_gbn2" type="radio" class="radio" value="0" tag="<?=$client['gbn2'];?>" onclick="check_time('<?=$__CURRENT_SVC_ID__;?>',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn'),__object_get_value('<?=$__CURRENT_SVC_ID__;?>_lvl'),'0');" <? if($client['gbn2'] == '0'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_gbn2')[0].checked = true; check_time('<?=$__CURRENT_SVC_ID__;?>',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn'),__object_get_value('<?=$__CURRENT_SVC_ID__;?>_lvl'),'0'); return false;">없음</a>
					<input name="<?=$__CURRENT_SVC_ID__;?>_gbn2" type="radio" class="radio" value="8" tag="<?=$client['gbn2'];?>" onclick="check_time('<?=$__CURRENT_SVC_ID__;?>',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn'),__object_get_value('<?=$__CURRENT_SVC_ID__;?>_lvl'),'8');" <? if($client['gbn2'] == '8'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_gbn2')[1].checked = true; check_time('<?=$__CURRENT_SVC_ID__;?>',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn'),__object_get_value('<?=$__CURRENT_SVC_ID__;?>_lvl'),'8'); return false;">특례180</a>
					<input name="<?=$__CURRENT_SVC_ID__;?>_gbn2" type="radio" class="radio" value="2" tag="<?=$client['gbn2'];?>" onclick="check_time('<?=$__CURRENT_SVC_ID__;?>',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn'),__object_get_value('<?=$__CURRENT_SVC_ID__;?>_lvl'),'2');" <? if($client['gbn2'] == '2'){echo 'checked';} ?>><a href="#" onclick="document.getElementsByName('<?=$__CURRENT_SVC_ID__;?>_gbn2')[2].checked = true; check_time('<?=$__CURRENT_SVC_ID__;?>',__object_get_value('<?=$__CURRENT_SVC_ID__;?>_gbn'),__object_get_value('<?=$__CURRENT_SVC_ID__;?>_lvl'),'2'); return false;">특례120</a><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th>소득등급</th>
			<td style="height:98px;">
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
</table>

<input name="<?=$__CURRENT_SVC_ID__;?>_sugaTime" type="hidden" value="0">
<input name="<?=$__CURRENT_SVC_ID__;?>_overTime" type="hidden" value="0">