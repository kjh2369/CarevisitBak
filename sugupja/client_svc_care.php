<?
	#######################################################################
	#
	# 재가요양
	#

	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	$current_svc_nm = $conn->kind_name($laSvcList, $__CURRENT_SVC_ID__, 'id');
	$body_w = '100%';
	$yymm = Date('Ym');

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

	/*********************************************************
	 * 수급자 옵션
	 *********************************************************/
	$sql = 'SELECT	limit_yn
			,		day_night_yn
			FROM	client_option
			WHERE	org_no = \''.$code.'\'
			AND		jumin  = \''.$jumin.'\'';
	$laOption = $conn->get_array($sql);

	//담당관리자
	$sql = 'SELECT	team_cd
			FROM	client_his_team
			WHERE	org_no	 = \''.$code.'\'
			AND		jumin	 = \''.$jumin.'\'
			AND		svc_cd	 = \''.$__CURRENT_SVC_CD__.'\'
			AND		from_ym <= \''.$yymm.'\'
			AND		to_ym	>= \''.$yymm.'\'
			AND		del_flag = \'N\'';
	$teamCd = $conn->get_data($sql);

	if ($teamCd){
		$sql = 'SELECT	m02_yname
				FROM	m02yoyangsa
				WHERE	m02_ccode = \''.$code.'\'
				AND		m02_mkind = \''.$__CURRENT_SVC_CD__.'\'
				AND		m02_yjumin= \''.$teamCd.'\'';
		$teamNm = $conn->get_data($sql);
		$teamCd = $ed->en($teamCd);
	}
?>
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col width="80px">
		<col width="70px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th rowspan="2">질병</th>
			<th>병명</th>
			<td colspan="3">
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

						echo '<input id="sickNm_'.$row['cd'].'" name=\''.$__CURRENT_SVC_ID__.'_byungMung\' type=\'radio\' class=\'radio clsObjData\' value=\''.$row['cd'].'\' '.($row['cd'] == $client['sick_gbn'] ? 'checked' : '').' onclick=\'check_sick("'.$__CURRENT_SVC_ID__.'_byungMung","'.$row['cd'].'");\'><label for="sickNm_'.$row['cd'].'">'.$row['nm'].'</label>';
					}

					$conn->row_free();
				}
			?>
			</td>
		</tr>
		<tr>
			<th>기타병명</th>
			<td colspan="3">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['sick_nm'].'</div>';
				}else{
					echo '<input id=\''.$__CURRENT_SVC_ID__.'_diseaseNm\' name=\''.$__CURRENT_SVC_ID__.'_diseaseNm\' type=\'text\' value=\''.$client['sick_nm'].'\' class=\'clsObjData\' style=\'width:100%;\'>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th colspan="2">20일초과, 90분가능여부</th>
			<td colspan="3">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.($client['nogood'] == 'Y' ? '예' : '아니오').'</div>';
				}else{
					echo '<input id="statNogoodY'.$__CURRENT_SVC_ID__.'" name=\''.$__CURRENT_SVC_ID__.'_statNogood\' type=\'radio\' class=\'radio clsObjData\' value=\'Y\' '.($client['nogood'] == 'Y' ? 'checked' : '').'><label for="statNogoodY'.$__CURRENT_SVC_ID__.'">예</label>';
					echo '<input id="statNogoodN'.$__CURRENT_SVC_ID__.'" name=\''.$__CURRENT_SVC_ID__.'_statNogood\' type=\'radio\' class=\'radio clsObjData\' value=\'N\' '.($client['nogood'] != 'Y' ? 'checked' : '').'><label for="statNogoodN'.$__CURRENT_SVC_ID__.'">아니오</label>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th rowspan="2">요양보호사</th>
			<th>주요양보호사</th>
			<td colspan="3">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['mem_nm1'].'</div>';
				}else{
					echo '<input id=\'memCd1_'.$__CURRENT_SVC_CD__.'\' name=\''.$__CURRENT_SVC_ID__.'_mem_cd1\' type=\'hidden\' class=\'clsData\' value=\''.$ed->en($client['mem_cd1']).'\' tag=\''.$ed->en($client['mem_cd1']).'\'>';
					echo '<input id=\'memNm1_'.$__CURRENT_SVC_CD__.'\' name=\''.$__CURRENT_SVC_ID__.'_mem_nm1\' type=\'text\'   class=\'clsData\' value=\''.$client['mem_nm1'].'\' style=\'background-color:#eeeeee; margin-top:3px;\' readOnly> ';
					echo '<span class=\'btn_pack find\' style=\'margin-top:1px; margin-left:-5px;\' onclick=\'__find_yoyangsa("'.$code.'","0",document.getElementById("'.$__CURRENT_SVC_ID__.'_mem_cd1"),document.getElementById("'.$__CURRENT_SVC_ID__.'_mem_nm1")); check_partner("'.$__CURRENT_SVC_ID__.'_mem_cd1","'.$__CURRENT_SVC_ID__.'_partner");\'></span>';
					echo '<span class=\'btn_pack m\' style=\'margin-top:2px;\'><button type=\'button\' onclick=\'clear_mem("'.$__CURRENT_SVC_ID__.'_mem_cd1","'.$__CURRENT_SVC_ID__.'_mem_nm1"); check_partner("'.$__CURRENT_SVC_ID__.'_mem_cd1","'.$__CURRENT_SVC_ID__.'_partner");\'>삭제</button></span>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th>부요양보호사</th>
			<td colspan="3">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['mem_nm2'].'</div>';
				}else{
					echo '<input id=\'memCd2_'.$__CURRENT_SVC_CD__.'\' name=\''.$__CURRENT_SVC_ID__.'_mem_cd2\' type=\'hidden\' class=\'clsData\' value=\''.$ed->en($client['mem_cd2']).'\' tag=\''.$ed->en($client['mem_cd2']).'\'>';
					echo '<input id=\'memNm2_'.$__CURRENT_SVC_CD__.'\' name=\''.$__CURRENT_SVC_ID__.'_mem_nm2\' type=\'text\'   class=\'clsData\' value=\''.$client['mem_nm2'].'\' style=\'background-color:#eeeeee; margin-top:3px;\' readOnly> ';
					echo '<span class=\'btn_pack find\' style=\'margin-top:1px; margin-left:-5px;\' onclick=\'__find_yoyangsa("'.$code.'","0",document.getElementById("'.$__CURRENT_SVC_ID__.'_mem_cd2"),document.getElementById("'.$__CURRENT_SVC_ID__.'_mem_nm2"));\'></span>';
					echo '<span class=\'btn_pack m\' style=\'margin-top:2px;\'><button type=\'button\' onclick=\'clear_mem("'.$__CURRENT_SVC_ID__.'_mem_cd2","'.$__CURRENT_SVC_ID__.'_mem_nm2");\'>삭제</button></span>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th style="" colspan="2">주 요양보호사 배우자여부</th>
			<td style="" colspan="3">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.($client['partner'] == 'Y' ? '예' : '아니오').'</div>';
				}else{
					echo '<input id="partnerY'.$__CURRENT_SVC_ID__.'" name=\''.$__CURRENT_SVC_ID__.'_partner\' type=\'radio\' class=\'radio clsObjData\' value=\'Y\' '.($client['partner'] == 'Y' ? 'checked' : '').'><label for="partnerY'.$__CURRENT_SVC_ID__.'">예</label>';
					echo '<input id="partnerN'.$__CURRENT_SVC_ID__.'" name=\''.$__CURRENT_SVC_ID__.'_partner\' type=\'radio\' class=\'radio clsObjData\' value=\'N\' '.($client['partner'] != 'Y' ? 'checked' : '').'><label for="partnerN'.$__CURRENT_SVC_ID__.'">아니오</label>';
				}
			?>
			</td>
		</tr><?
		if ($jumin){?>
			<tr>
				<th style="border-bottom:1px solid #0e69b0;" colspan="2">담당팀장</th>
				<td style="border-bottom:1px solid #0e69b0;" colspan="3"><?
					if ($view_type == 'read'){?>
						<div class="left"><?=$teamNm;?></div><?
					}else{?>
						<div id="ID_TEAM" class="left nowrap" style="float:left; width:auto;"><?=$teamNm;?></div>
						<div style="float:left; width:auto; margin-left:5px; margin-top:3px;">
							<span class="btn_pack m"><button onclick="lfTeamPop('<?=$__CURRENT_SVC_CD__;?>');">변경</button></span>
						</div><?
					}?>
				</td>
			</tr><?
		}?>
		<tr>
			<th style="border-bottom:1px solid #0e69b0;">가족보호사</th>
			<td style="border-bottom:1px solid #0e69b0;" colspan="4">
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
							<th class="head bottom last"><?
								if ($view_type != 'read'){?>
									<span class="btn_pack m"><button type="button" onclick="_clientFamilyAddRow();">추가</button></span><?
								}?>
							</th>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th style="border-bottom:1px solid #0e69b0;" colspan="2">목욕초과산정여부</th>
			<td style="border-bottom:1px solid #0e69b0;" colspan="3">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.($client['bath_add'] == 'Y' ? '예' : '아니오').'</div>';
				}else{
					echo '<input id="bathAddY'.$__CURRENT_SVC_ID__.'" name=\''.$__CURRENT_SVC_ID__.'_bathAddYn\' type=\'radio\' class=\'radio clsObjData\' value=\'Y\' '.($client['bath_add'] == 'Y' ? 'checked' : '').'><label for="bathAddY'.$__CURRENT_SVC_ID__.'">예</label>';
					echo '<input id="bathAddN'.$__CURRENT_SVC_ID__.'" name=\''.$__CURRENT_SVC_ID__.'_bathAddYn\' type=\'radio\' class=\'radio clsObjData\' value=\'N\' '.($client['bath_add'] != 'Y' ? 'checked' : '').'><label for="bathAddN'.$__CURRENT_SVC_ID__.'">아니오</label>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th style="border-bottom:1px solid #0e69b0;" rowspan="4">장기요양보험</th>
			<th>인정번호</th>
			<td class="left" colspan="3">
				<span id="mgmtNo" value="" class="clsData"></span>
				<span id="mgmtSeq" value="" class="clsData" style="display:none;"></span><?
				if ($view_type != 'read'){?>
					<span class="btn_pack m" style="margin-left:40px; vertical-align:middle;"><button type="button" onclick="_clientCareShow('<?=$__CURRENT_SVC_ID__;?>','<?=$__CURRENT_SVC_CD__;?>')">변경</button></span><?
				}?>
			</td>
		</tr>
		<tr>
			<th>유효기간</th>
			<td class="left" colspan="3">
				<div style="float:left; width:auto; margin-right:5px;"><span id="mgmtFrom" value="" class="clsData"></span><span id="mgmtTo" value="" class="clsData"></span></div>
			</td>
		</tr>
		<tr>
			<th>등급</th>
			<td class="left">
				<span id="mgmtLvl" value="" class="clsData"></span>
			</td>
			<th>급여한도금액</th>
			<td class="left">
				<span id="mgmtPay" value="" class="clsData"></span>
			</td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid #0e69b0;" colspan="4">
				<input id="chkOverApp" name="chkOverApp" type="checkbox" class="checkbox clsObjData" value="Y" <? if($laOption['limit_yn'] != 'N'){?>checked<?} ?>><label for="chkOverApp">한도초과시 비급여 처리</label>
				<p style="line-height:1.3em;">
					- 체크시 RFID 확정된 금액중 한도가 넘는 부분은<br>&nbsp;&nbsp;"초과(비급여 본인부담금)" 처리함.<br>
					- 체크하지 않을 시 한도까지만 본인부담금액을 계산함.
				</p>
			</td>
		</tr>


		<tr>
			<th style="border-bottom:1px solid #0e69b0;" rowspan="3">본인부담금</th>
			<th>수급자구분</th>
			<td class="left" colspan="3">
				<span id="expenseKind_<?=$__CURRENT_SVC_ID__;?>" value="" class="clsData"></span>
				<span id="expenseSeq_<?=$__CURRENT_SVC_ID__;?>" value="" class="clsData" style="display:none;"></span><?
				if ($view_type != 'read'){?>
					<span class="btn_pack m" style="margin-left:40px; vertical-align:middle;"><button type="button" onclick="_clientKindShow('<?=$__CURRENT_SVC_ID__;?>','<?=$__CURRENT_SVC_CD__;?>')">변경</button></span><?
				}?>
			</td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td class="left" colspan="3">
				<div style="float:left; width:auto; margin-right:5px;"><span id="expenseFrom_<?=$__CURRENT_SVC_ID__;?>" value="" class="clsData"></span><span id="expenseTo_<?=$__CURRENT_SVC_ID__;?>" value="" class="clsData"></span></div>
			</td>
		</tr>
		<tr>
			<th style="border-bottom:1px solid #0e69b0;">본인부담율</th>
			<td style="border-bottom:1px solid #0e69b0;" class="left">
				<span id="expenseRate_<?=$__CURRENT_SVC_ID__;?>" value="" class="clsData"></span>
			</td>
			<th style="border-bottom:1px solid #0e69b0;">본인부담금</th>
			<td style="border-bottom:1px solid #0e69b0;" class="left">
				<span id="expenseAmt_<?=$__CURRENT_SVC_ID__;?>" value="" class="clsData"></span>
			</td>
		</tr>

		<tr>
			<th style="border-bottom:1px solid #0e69b0;" rowspan="2">청구한도</th>
			<th>청구한도금액</th>
			<td class="left" colspan="3">
				<span id="claimAmt_<?=$__CURRENT_SVC_ID__;?>" value="" class="clsData"></span>
				<span id="claimSeq_<?=$__CURRENT_SVC_ID__;?>" value="" class="clsData"></span><?
				if ($view_type != 'read'){?>
					<span class="btn_pack m" style="margin-left:40px; vertical-align:middle;"><button type="button" onclick="_clientClaimShow('<?=$__CURRENT_SVC_ID__;?>','<?=$__CURRENT_SVC_CD__;?>')">변경</button></span><?
				}?>
			</td>
		</tr>
		<tr>
			<th style="border-bottom:1px solid #0e69b0;">적용기간</th>
			<td style="border-bottom:1px solid #0e69b0;" class="left" colspan="3">
				<div style="float:left; width:auto; margin-right:5px;"><span id="claimFrom_<?=$__CURRENT_SVC_ID__;?>" value="" class="clsData"></span><span id="claimTo_<?=$__CURRENT_SVC_ID__;?>" value="" class="clsData"></span></div>
			</td>
		</tr><?
		if ($jumin && $gDayAndNight){
			$sql = 'SELECT	COUNT(*) AS cnt
					,		SUM(cost) AS amt
					FROM	dan_nonpayment_client
					WHERE	org_no	= \''.$code.'\'
					AND		jumin	= \''.$jumin.'\'
					AND		del_flag= \'N\'';
			$r = $conn->get_array($sql);
			$npmtCnt = number_format($r['cnt']);
			$npmtAmt = number_format($r['amt']);
			Unset($r);?>
			<tr>
				<th style="">주야간보호</th>
				<td class="left" style="" colspan="4">
					<label><input id="optDANY" name="optDANYN" type="radio" class="radio clsObjData" value="Y" <?=$laOption['day_night_yn'] == 'Y' ? 'checked' : '';?>>예</label>
					<label><input id="optDANN" name="optDANYN" type="radio" class="radio clsObjData" value="N" <?=$laOption['day_night_yn'] != 'Y' ? 'checked' : '';?>>아니오</label>
				</td>
			</tr>
			<tr>
				<th style="border-bottom:1px solid #0e69b0;">비급여관리</th>
				<td class="left" style="border-bottom:1px solid #0e69b0;" colspan="4">
					<div style="float:left; width:auto;">
						<span id="lblDanNpmtCnt"><?=$npmtCnt;?></span>건 / <span id="lblDanNpmtAmt"><?=$npmtAmt;?></span>원
					</div>
					<div style="float:right; width:auto; padding-top:1px; padding-right:5px;"><span id="btnDanNpmt" class="btn_pack m"><button onclick="_clientDanNpmt();">비급여관리</button></span></div>
				</td>
			</tr><?
		}?>
	</tbody>
</table>
<?
	unset($client);

	/*********************************************************

	가족요양보호사 조회

	*****************************************************/
	$sql = 'select cf_mem_cd as cd
			,      cf_mem_nm as nm
			,      cf_kind as kind
			  from client_family
			 where org_no   = \''.$code.'\'
			   and cf_jumin = \''.$jumin.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);?>
		<script type="text/javascript">
			_clientFamilyAddRow('<?=$row['nm'];?>','<?=$ed->en($row['cd']);?>','<?=$row['kind'];?>','<?=$view_type;?>');
		</script><?
	}

	$conn->row_free();?>

	<script type="text/javascript">
	$(document).ready(function(){
		try{
			check_sick("11_byungMung",$('input:radio[name="11_byungMung"]:checked').val());
		}catch(e){
		}
	});

	function check_sick(object, value){
		__object_checked(object, value);

		var val    = value;
		var obj_id = __get_svc_code(object);
		var obj    = __object_check(object);
		var obj_nm = __getObject(obj_id+'_diseaseNm');
		var stat   = document.getElementsByName(obj_id+'_statNogood');

		if (val == 1){
			__setEnabled(obj_nm, false);
			__setEnabled(stat[0], true);
			__setEnabled(stat[1], true);
		}else if (val == 9){
			__setEnabled(obj_nm, true);
			__setEnabled(stat[0], false);
			__setEnabled(stat[1], false);
		}else{
			__setEnabled(obj_nm, false);
			__setEnabled(stat[0], false);
			__setEnabled(stat[1], false);
		}
	}
	</script>