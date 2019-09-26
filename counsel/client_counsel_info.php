<?
	/**************************************************

		 상담구분

	**************************************************/
	
	if ($is_path == 'counsel'   || 
		$is_path == 'reportNew' ){  ?>
		<table class="my_table my_border_blue" style="width:100%; border-bottom:none;">
			<colgroup>
				<col width="100px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th>상담구분</th>
					<td class="last"><?
						$kind_list = $conn->kind_list($code, true);

						if (is_array($kind_list)){
							foreach($kind_list as $k => $k_list){
								if ($k_list['id'] < 30){?>
									<input id="optSvcKind_<?=$k_list['code'];?>" name="svc_kind" type="radio" class="radio" value="<?=$k_list['code'];?>" onclick="set_counsel_kind('<?=$k_list['code'];?>'); return false;" <?echo ($counsel_kind == $k_list['code'] ? 'checked' : '')?>><label for="optSvcKind_<?=$k_list['code'];?>"><?=$k_list['name'];?></label><?
								}
							}
						}
						unset($kind_list);?>

						<input id="optSvcKind_9" name="svc_kind" type="radio" class="radio" value="9" onclick="set_counsel_kind('9'); return false;" <?echo ($counsel_kind == '9' ? 'checked' : '')?>><label for="optSvcKind_9">기타</label>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="my_table my_border_blue" style="width:100%; border-top:none;"><?
	}else{?>
		<table class="my_table my_border_blue" style="width:100%;"><?
	}



	/**************************************************

		고객등록여부

	**************************************************/
	$sql = 'select count(*)
			  from m03sugupja
			 where m03_ccode = \''.$code.'\'
			   and m03_jumin = \''.$counsel['client_ssn'].'\'';

	$reg_cnt = $conn->get_data($sql);
	
	$talkNm = $normal['talker_nm'] != '' ? $normal['talker_nm'] : $talkNm;


	echo '<input name=\'reg_cnt\' type=\'hidden\' value=\''.$reg_cnt.'\'>';

	if ($reg_cnt == 0){
		$reg_ssn1 = substr($counsel['client_ssn'], 0, 6);
		$reg_ssn2 = substr($counsel['client_ssn'], 6);
	}else{
		$reg_ssn1 = '';
		$reg_ssn2 = '';
	}
?>

	<colgroup>
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody id="counsel_normal" style="display:<?=$counsel_kind != '3' ? '' : 'none';?>">
		<tr>
			<th>상담자</th>
			<td class="left" colspan="2">
				<span class="btn_pack find" onClick="__find_yoyangsa('<?=$code;?>','<?=$kind;?>','normal_talker_cd','normal_talker_str');"></span>
				<span id="normal_talker_str" style="height:100%; margin-left:5px; font-weight:bold;"><?=$talkNm;?></span>
				<input name="normal_talker_cd" type="hidden" value="<?=$ed->en($normal['talker_ssn']);?>">
				<input name="normal_talker_nm" type="hidden" value="<?=$talkNm;?>">
			</td>
			<th>상담유형</th>
			<td colspan="2">
				<input name="normal_counsel_type" type="radio" class="radio" value="1" <? if($normal['talker_type'] == '1'){?>checked<?} ?>>내방
				<input name="normal_counsel_type" type="radio" class="radio" value="2" <? if($normal['talker_type'] == '2'){?>checked<?} ?>>방문
				<input name="normal_counsel_type" type="radio" class="radio" value="3" <? if($normal['talker_type'] == '3'){?>checked<?} ?>>전화
			</td>
			<th>상담일자</th>
			<td class="last">
				<input name="normal_counsel_date" type="text" value="<?=$normal['talker_dt'];?>" class="date" onkeydown="__onlyNumber(this);" onclick="_carlendar(this);">
			</td>
		</tr>
	</tbody>
	<tbody id="counsel_baby" style="display:<?=$counsel_kind != '3' ? 'none' : '';?>">
		<tr>
			<th>상담자</th>
			<td class="left" colspan="2">
				<span class="btn_pack find" onClick="__find_yoyangsa('<?=$code;?>','<?=$kind;?>','baby_talker_cd','baby_talker_str');"></span>
				<span id="baby_talker_str" style="height:100%; margin-left:5px; font-weight:bold;"><?=$baby['talker_nm'];?></span>
				<input name="baby_talker_cd" type="hidden" value="<?=$ed->en($baby['talker_ssn']);?>">
				<input name="baby_talker_nm" type="hidden" value="<?=$baby['talker_nm'];?>">
			</td>
			<th>상담유형</th>
			<td colspan="2">
				<input name="baby_counsel_type" type="radio" class="radio" value="1" <? if($baby['talker_type'] == '1'){?>checked<?} ?>>내방
				<input name="baby_counsel_type" type="radio" class="radio" value="2" <? if($baby['talker_type'] == '2'){?>checked<?} ?>>방문
				<input name="baby_counsel_type" type="radio" class="radio" value="3" <? if($baby['talker_type'] == '3'){?>checked<?} ?>>전화
			</td>
			<th>상담일자</th>
			<td class="last">
				<input name="baby_counsel_date" type="text" value="<?=$baby['talker_dt'];?>" class="date" onkeydown="__onlyNumber(this);" onclick="_carlendar(this);">
			</td>
		</tr>
	</tbody>
	<tbody>
	<?
		if ($is_path == 'counsel'   || 
			$is_path == 'reportNew' ){?>
			<tr>
				<th rowspan="7">개인정보</th>
				<th>성명</th>
				<td class="last"><input name="counsel_name" type="text" tabindex="1" value="<?=$counsel['client_nm'];?>" maxlength="4"></td>
				<td></td>
				<th rowspan="2">연락처</th>
				<th>유선</th>
				<td class="last" colspan="2"><input name="counsel_phone" type="text" tabindex="11" value="<?=$counsel['client_phone'];?>" maxlength="11" class="phone"></td>
			</tr>
			<tr>
				<th>주민번호</th>
				<td colspan="2">
				<?
					if ($reg_cnt == 0){?>
						<input name="counsel_ssn1" type="text" tabindex="1" value="<?=$reg_ssn1;?>" maxlength="6" class="no_string" style="width:50px;" onkeydown="__onlyNumber(this);"> -
						<input name="counsel_ssn2" type="text" tabindex="1" value="<?=$reg_ssn2;?>" maxlength="7" class="no_string" style="width:55px;" onkeydown="__onlyNumber(this);"><?
					}else{?>
						<div class="left"><?=$myF->issStyle($counsel['client_ssn']);?></div>
						<input name="counsel_ssn" type="hidden" value="<?=$ed->en($counsel['client_ssn']);?>"><?
					}
				?>
				</td>
				<th>무선</th>
				<td class="last" colspan="2"><input name="counsel_mobile" type="text" tabindex="11" value="<?=$counsel['client_mobile'];?>" maxlength="11" class="phone"></td>
			</tr>
			<tr>
				<th rowspan="3">소재</th>
				<td colspan="2">
					<input id="counsel_postno" name="counsel_postno" type="text" value="<?=$counsel['client_postno'];?>" tabindex="21" maxlength="6" class="no_string" style="width:50px; margin-right:0;">
					<span class="btn_pack m"><button onclick="lfPostno();">찾기</button></span>
					<!--span class="btn_pack small"><button type="button" onClick="__helpAddress(__getObject('counsel_postno1'),__getObject('counsel_postno2'),__getObject('counsel_addr'),__getObject('counsel_addr_dtl'));">찾기</button></span-->
				</td>
				<th rowspan="3">보호자</th>
				<th>성명</th>
				<td class="last" colspan="2"><input name="counsel_protect_nm" type="text" tabindex="1" value="<?=$counsel['client_protect_nm'];?>" maxlength="4"></td>
			</tr>
			<tr>
				<td colspan="2"><input id="counsel_addr" name="counsel_addr" type="text" value="<?=$counsel['client_addr'];?>" tabindex="21" maxlength="20" style="width:100%;"></td>
				<th>관계</th>
				<td class="last" colspan="2"><input name="counsel_protect_rel" type="text" tabindex="1" value="<?=$counsel['client_protect_rel'];?>"></td>
			</tr>
			<tr>
				<td colspan="2"><input id="counsel_addr_dtl" name="counsel_addr_dtl" type="text" value="<?=$counsel['client_addr_dtl'];?>" tabindex="21" maxlength="20" style="width:100%;"></td>
				<th>연락처</th>
				<td class="last" colspan="2"><input name="counsel_protect_tel" type="text" tabindex="11" value="<?=$counsel['client_protect_tel'];?>" maxlength="11" class="phone"></td>
			</tr><?
		}?>
		<tr>
			<td class="last" colspan="8">
				<table class="my_table" style="width:100%; border-bottom:none;">
					<colgroup>
						<col width="100px">
						<col>
					</colgroup>
					<tbody>
						<tr id="normal_protect" style="display:<?=$counsel_kind != '3' ? '' : 'none';?>">
							<th class="bottom">보호구분</th>
							<td class="last bottom">
								<input name="normal_protect_gbn" type="radio" class="radio" value="1" onclick="__setEnabled('normal_protect_other', false);" <? if($normal['protect_gbn'] == '1'){echo 'checked';} ?>>수급자1종
								<input name="normal_protect_gbn" type="radio" class="radio" value="2" onclick="__setEnabled('normal_protect_other', false);" <? if($normal['protect_gbn'] == '2'){echo 'checked';} ?>>수급자2종
								<input name="normal_protect_gbn" type="radio" class="radio" value="N" onclick="__setEnabled('normal_protect_other', false);" <? if($normal['protect_gbn'] == 'N'){echo 'checked';} ?>>일반
								<input name="normal_protect_gbn" type="radio" class="radio" value="9" onclick="__setEnabled('normal_protect_other', true);"	 <? if($normal['protect_gbn'] == '9'){echo 'checked';} ?>>기타
								<input name="normal_protect_other" type="text" value="">
							</td>
						</tr>
						<tr id="baby_protect" style="display:<?=$counsel_kind != '3' ? 'none' : '';?>">
							<th class="bottom">보호구분</th>
							<td class="last bottom">
								<input name="baby_protect_gbn" type="radio" class="radio" value="1" <? if($baby['protect_gbn'] == '1'){echo 'checked';} ?>>일반
								<input name="baby_protect_gbn" type="radio" class="radio" value="2" <? if($baby['protect_gbn'] == '2'){echo 'checked';} ?>>차상위
								<input name="baby_protect_gbn" type="radio" class="radio" value="9" <? if($baby['protect_gbn'] == '3'){echo 'checked';} ?>>기초생활수급자
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr><?
		
			if($counsel_kind == '0'){ ?>
			<tr>
				<th>요양등급</th>
				<td colspan="6">
					<input name="normal_level_gbn" type="radio" class="radio" value="1" <? if($normal['level_gbn'] == '1'){echo 'checked';} ?>>1등급
					<input name="normal_level_gbn" type="radio" class="radio" value="2" <? if($normal['level_gbn'] == '2'){echo 'checked';} ?>>2등급
					<input name="normal_level_gbn" type="radio" class="radio" value="3" <? if($normal['level_gbn'] == '3'){echo 'checked';} ?>>3등급
					<input name="normal_level_gbn" type="radio" class="radio" value="4" <? if($normal['level_gbn'] == '4'){echo 'checked';} ?>>4등급
					<input name="normal_level_gbn" type="radio" class="radio" value="5" <? if($normal['level_gbn'] == '5'){echo 'checked';} ?>>5등급
					<input name="normal_level_gbn" type="radio" class="radio" value="9" <? if($normal['level_gbn'] == '9'){echo 'checked';} ?>>일반
				</td>
			</tr><?
			}?>
		<tr>
			<th>가족형태</th>
			<td class="last" colspan="6">
				<input name="family_gbn" type="radio" class="radio" value="1" onclick="__setEnabled('family_other', false);" <? if($counsel['client_family_gbn'] == '1'){echo 'checked';} ?>>독거
				<input name="family_gbn" type="radio" class="radio" value="2" onclick="__setEnabled('family_other', false);" <? if($counsel['client_family_gbn'] == '2'){echo 'checked';} ?>>노인부부
				<input name="family_gbn" type="radio" class="radio" value="3" onclick="__setEnabled('family_other', false);" <? if($counsel['client_family_gbn'] == '3'){echo 'checked';} ?>>아들가족
				<input name="family_gbn" type="radio" class="radio" value="4" onclick="__setEnabled('family_other', false);" <? if($counsel['client_family_gbn'] == '4'){echo 'checked';} ?>>딸가족
				<input name="family_gbn" type="radio" class="radio" value="9" onclick="__setEnabled('family_other', true);"  <? if($counsel['client_family_gbn'] == '9'){echo 'checked';} ?>>기타
				<input name="family_other" type="text" value="<?=$counsel['client_family_other'];?>">
			</td>
		</tr>
	</tbody>
</table>

<?
	$family_cnt = sizeof($family);

	if (empty($family_cnt)) $family_cnt = 1;
?>
<!-- 가족사항 -->
<table id="tbl_family" class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="100px">
		<col width="105px">
		<col width="80px">
		<col width="90px">
		<col width="150px">
		<col width="60px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody id="my_family">
		<tr>
			<th id="family_span" rowspan="<?=$family_cnt + 1;?>">가족사항</th>
			<th class="head">성명</th>
			<th class="head">관계</th>
			<th class="head">생년월일</th>
			<th class="head">직업</th>
			<th class="head">동거여부</th>
			<th class="head">월수입</th>
			<th class="head last">비고</th>
		</tr>
		<?
			for($i=0; $i<$family_cnt; $i++){
				$id = $i+1;?>
				<tr id="family_row_<?=$id;?>">
					<td class="center"><input name="family_name[]" type="text" value="<?=$family[$i]['family_nm'];?>" tabindex="41" style="width:100%;"></td>
					<td class="center"><input name="family_relation[]" type="text" value="<?=$family[$i]['family_rel'];?>" tabindex="41" style="width:100%;"></td>
					<td class="center"><input name="family_age[]" type="text" value="<?=$family[$i]['family_age'];?>" tabindex="41" style="width:100%;"></td>
					<td class="center"><input name="family_job[]" type="text" value="<?=$family[$i]['family_job'];?>" tabindex="41" style="width:100%;"></td>
					<td class="center">
						<select name="family_together[]" style="width:auto;" tabindex="41">
							<option value="Y" <? if($family[$i]['family_with'] == 'Y'){?>selected<?} ?>>예</option>
							<option value="N" <? if($family[$i]['family_with'] == 'N'){?>selected<?} ?>>아니오</option>
						</select>
					</td>
					<td class="center"><input name="family_salary[]" type="text" value="<?=number_format($family[$i]['family_monthly']);?>" tabindex="41" class="number" style="width:100%;" onkeydown="if(event.keyCode == 13 || event.keyCode == 9){family_tbl.t_add_row();}else{__onlyNumber(this);}"></td><?
					if ($i == 0){?>
						<td class="left last"><span class="btn_pack m"><button type="button" onclick="family_tbl.t_add_row();">추가</button></span></td><?
					}else{?>
						<td class="left last"><span class="btn_pack m"><button type="button" onclick="family_tbl.t_delete_row('family_row_<?=$id;?>', 0);">삭제</button></span></td><?
					}?>
				</tr><?
			}
		?>
	</tbody>
</table>
<input name="family_cnt" type="hidden" value="<?=$family_cnt;?>">