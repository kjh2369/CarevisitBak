<?
	if ($mode == 1 || $mode == 2){?>
		<table class="my_table my_border" style="margin-top:-1px;"><?
	}else{?>
		<table class="my_table my_border" style="height:100%;"><?
	}
?>	<colgroup>
		<col width="400px">
		<col width="430px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="left top bottom" style="padding:0;">
				<table class="my_table" style="width:100%; border-bottom:none;">
					<colgroup>
						<col width="50px">
						<col width="130px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th colspan="2">주민번호</th>
							<td class="last">
							<?
								if ($mode == 1){?>
									<input name="jumin1" type="text" value="" maxlength="6" class="phone" style="width:50px;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length == 6){document.f.jumin2.focus();}" onChange="_check_ssn('su', document.f.jumin1, document.f.jumin2, document.f.code);" onFocus="this.select();"> -
									<input name="jumin2" type="text" value="" maxlength="7" class="phone" style="width:55px;" onKeyDown="__onlyNumber(this);" onkeyUp="if(this.value.length == 7){document.f.name.focus();}"   onChange="_check_ssn('su', document.f.jumin1, document.f.jumin2, document.f.code);" onFocus="this.select();">
									<input name="jumin" type="hidden" value=""><?
								}else if ($mode == 2){?>
									<div class="left"><?=$myF->issStyle($jumin);?></div>
									<input name="jumin" type="hidden" value="<?=$ed->en($jumin);?>"><?
								}else{?>
									<div class="left"><?=$myF->issStyle($jumin);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">수급자성명</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="name" type="text" value="<?=$mst[$kind]["m03_name"];?>" maxlength="10" onKeyDown="__enterFocus();" onFocus="this.select();" tag="수급자성명을 입력하여 주십시오."><?
								}else{?>
									<div class="left"><?=$mst[$kind]["m03_name"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="2">전화<br>번호</th>
							<th>핸드폰</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="hp" type="text" value="<?=$myF->phoneStyle($mst[$kind]["m03_hp"]);?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);"><?
								}else{?>
									<div class="left"><?=$myF->phoneStyle($mst[$kind]["m03_hp"]);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>자택</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="tel" type="text" value="<?=$myF->phoneStyle($mst[$kind]["m03_tel"]);?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);"><?
								}else{?>
									<div class="left"><?=$myF->phoneStyle($mst[$kind]["m03_tel"]);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="3">소재</th>
							<th>우편번호</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="postno1" type="text" value="<?=substr($mst[$kind]["m03_post_no"],0,3);?>" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onFocus="this.select();"> -
									<input name="postno2" type="text" value="<?=substr($mst[$kind]["m03_post_no"],3,6);?>" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onFocus="this.select();">
									<span class="btn_pack small"><button type="button" onClick="__helpAddress(document.f.postno1, document.f.postno2, document.f.addr1, document.f.addr2);">찾기</button></span><?
								}else{?>
									<div class="left"><?=substr($mst[$kind]["m03_post_no"],0,3);?>-<?=substr($mst[$kind]["m03_post_no"],3,6);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th rowspan="2">주소</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="addr1" type="text" value="<?=$mst[$kind]["m03_juso1"];?>" maxlength="20" style="width:100%;" onKeyDown="__enterFocus();" onFocus="this.select();"><?
								}else{?>
									<div class="left"><?=$mst[$kind]["m03_juso1"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="addr2" type="text" value="<?=$mst[$kind]["m03_juso2"];?>" maxlength="20" style="width:100%;" onKeyDown="__enterFocus();" onFocus="this.select();"><?
								}else{?>
									<div class="left"><?=$mst[$kind]["m03_juso2"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">기관계약일자</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="gaeYakFm" type="text" value="<?=$myF->dateStyle($mst[$kind]["m03_gaeyak_fm"]);?>" tag="<?=$myF->dateStyle($mst[$kind]["m03_gaeyak_fm"]);?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);"> ~
									<input name="gaeYakTo" type="text" value="<?=$myF->dateStyle($mst[$kind]["m03_gaeyak_to"]);?>" tag="<?=$myF->dateStyle($mst[$kind]["m03_gaeyak_to"]);?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);" alt="not"><?
								}else{?>
									<div class="left"><?=$myF->dateStyle($mst[$kind]["m03_gaeyak_fm"]);?>~<?=$myF->dateStyle($mst[$kind]["m03_gaeyak_to"]);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="3">보호자<br>정보</th>
							<th>성명</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yBohoName" type="text" value="<?=$mst[$kind]["m03_yboho_name"];?>" maxlength="10" onKeyDown="__enterFocus();" onFocus="this.select();"><?
								}else{?>
									<div class="left"><?=$mst[$kind]["m03_yboho_name"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>수급자와의 관계</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yBohoGwange" type="text" value="<?=$mst[$kind]["m03_yboho_gwange"];?>" maxlength="10" onKeyDown="__enterFocus();" onFocus="this.select();"><?
								}else{?>
									<div class="left"><?=$mst[$kind]["m03_yboho_gwange"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>연락처</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yBohoPhone" type="text" value="<?=$myF->phoneStyle($mst[$kind]["m03_yboho_phone"]);?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);"><?
								}else{?>
									<div class="left"><?=$myF->phoneStyle($mst[$kind]["m03_yboho_phone"]);?></div><?
								}
							?>
							</td>
						</tr>
					</tbody>
					<tbody>
						<tr>
							<td class="last bottom" colspan="3"></td>
						</tr>
					</tbody>
				</table>
			</td>
			<td class="left top last bottom" style="padding:0;">
				<table class="my_table" style="width:100%; border-bottom:0px;">
					<colgroup>
						<col width="20px">
						<col width="50px">
						<col width="100px">
						<col width="85px">
						<col width="80px">
						<col>
					</colgroup>
					<tbody id="kind_0" style="display:<? if($mst[0]['m03_mkind'] != '0'){echo 'none';} ?>;">
						<tr>
							<th class="center" rowspan="13">재<br>가</th>
							<th colspan="2">수급상태</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){
									$statusList = $definition->SugupjaStatusList();

									for($i=0; $i<sizeOf($statusList); $i++){
										if ($mst[0]['m03_sugup_status'] == $statusList[$i]['code']){
											$checked = 'checked';
										}else{
											$checked = '';
										}

										if ($mode == 1 && $statusList[$i]['code'] != '1'){
											$disabled = true;
										}else{
											$disabled = false;
										}?>
										<input name="sugupStatus" type="radio" class="radio" value="<?=$statusList[$i]['code'];?>" tag="<?=$mst[0]['m03_sugup_status'];?>" <? if($disabled){?>disabled="true"<?} ?> onclick="_client_cont_date(__get_value(document.f.sugupStatus),'gaeYakFm','gaeYakTo');" <?=$checked;?>><?=$statusList[$i]['name'];
									}
								}else{?>
									<div class="left"><?=$definition->SugupjaStatusGbn($mst[0]["m03_sugup_status"]);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="2">장기<br>요양<br>급여</th>
							<th>장기요양등급</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){
									$sql = $conn->get_gubun("LVL");
									$conn->query($sql);
									$row = $conn->fetch();
									$row_count = $conn->row_count();

									for($i=0; $i<$row_count; $i++){
										$row = $conn->select_row($i);

										if ($mst[0]['m03_ylvl'] == $row[0]){
											$checked = 'checked';
										}else{
											$checked = '';
										} ?>
										<input name="yLvl" type="radio" class="radio" value="<?=$row[0];?>" tag="<?=$mst[0]['m03_ylvl'];?>" onclick="_set_max_pay(document.f.kupyeoMax, this.value); _set_my_yul(__get_value(document.getElementsByName('sKind')), __get_tag(document.getElementsByName('sKind')));" <?=$checked;?>><?=$row[1];
									}

									$conn->row_free();
								}else{
									$sql = "select m81_name
											  from m81gubun
											 where m81_gbn  = 'LVL'
											   and m81_code = '".$mst[0]['m03_ylvl']."'";
									$lvl_name = $conn->get_data($sql); ?>
									<div class="left"><?=$lvl_name;?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>급여한도액</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="kupyeoMax" type="text" value="<?=number_format($kupyeoMax);?>" maxlength="10" class="number" style="background-color:#eeeeee;" onFocus="document.f.injungNo.focus();" readOnly><?
								}else{?>
									<div class="left"><?=number_format($mst[0]['m03_kupyeo_max']);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="3">본인<br>부담금</th>
							<th>수급자구분</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){
									$sql = $conn->get_gubun("STP");
									$conn->query($sql);
									$row = $conn->fetch();
									$row_count = $conn->row_count();

									for($i=0; $i<$row_count; $i++){
										$row = $conn->select_row($i);

										if ($mst[0]['m03_skind'] == $row[0]){
											$checked = 'checked';
										}else{
											$checked = '';
										} ?>
										<input name="sKind" type="radio" class="radio" value="<?=$row[0];?>" tag="<?=$mst[0]['m03_skind'];?>" onclick="_set_my_yul(this.value, '<?=$mst[0]['m03_skind'];?>');" <?=$checked;?>><?=$row[1];?><br><?
									}

									$conn->row_free();
								}else{
									$sql = "select m81_name
											  from m81gubun
											 where m81_gbn  = 'STP'
											   and m81_code = '".$mst[0]['m03_skind']."'";
									$stp_name = $conn->get_data($sql);?>
									<div class="left"><?=$stp_name;?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>
								<div class="help_left">청구한도금액</div>
								<div class="help" onmouseover="_show_help(this, '지자체에서 지정한 재가서비스이용한도금액(청구한도금액)을 입력합니다. (표시된 금액이 상이할 경우에 입력)');" onmouseout="_hidden_help();"></div>
							</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="kupyeo1" type="text" value="<?=number_format($mst[0]['m03_kupyeo_1']);?>" tag="<?=number_format($mst[0]['m03_kupyeo_1']);?>" maxlength="10" class="number" style="width:75px; background-color:#eeeeee;" onFocus="document.f.injungNo.focus();" onchange="return _check_min_max_pay();" readOnly><?
								}else{?>
									<div class="left"><?=number_format($mst[0]['m03_kupyeo_1']);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>수급자본인부담율</th>
							<td>
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="boninYul" type="text" value="<?=$mst[0]['m03_bonin_yul'];?>" tag="<?=$mst[0]['m03_bonin_yul'];?>" maxlength="3" style="width:75px; background-color:<?=$mst[0]["m03_skind"] != '3' ? '#ffffff' : '#eeeeee';?>;" class="number" onKeyDown="__onlyNumber(this,'.');" onChange="return _set_pay();" onFocus="if(!this.readOnly){this.select();}else{document.f.injungNo.focus();}" alt="not"><?
								}else{?>
									<div class="left"><?=$mst[0]['m03_bonin_yul'];?></div><?
								}
							?>
							</td>
							<th>본인부담금</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="kupyeo2" type="text" value="<?=number_format($mst[0]['m03_kupyeo_2']);?>" maxlength="10" class="number" style="width:100%; background-color:#eeeeee;" onFocus="document.f.kupyeo1.focus();" readOnly><?
								}else{?>
									<div class="left"><?=number_format($mst[0]['m03_kupyeo_2']);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="2">장기<br>요양<br>보험</th>
							<th>인증번호</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="injungNo" type="text" value="<?=$mst[0]["m03_injung_no"];?>" style="width:150px;" onKeyDown="__enterFocus();" onFocus="__replace(this, '-', '');" onBlur="this.value=__formatString(this.value, '#####-######-###');"><?
								}else{?>
									<div class="left"><?=$mst[0]['m03_injung_no'];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>유효기간</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="injungFrom" type="text" value="<?=$myF->dateStyle($mst[0]["m03_injung_from"]);?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);"> ~
									<input name="injungTo"	 type="text" value="<?=$myF->dateStyle($mst[0]["m03_injung_to"]);?>"   maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);"><?
								}else{?>
									<div class="left"><?=$myF->dateStyle($mst[0]["m03_injung_from"]);?>~<?=$myF->dateStyle($mst[0]["m03_injung_to"]);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">병명</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){
									$sql = $conn->get_gubun("DAS");
									$conn->query($sql);
									$row = $conn->fetch();
									$row_count = $conn->row_count();

									for($i=0; $i<$row_count; $i++){
										$row = $conn->select_row($i);

										if ($mst[0]['m03_byungmung'] == $row[0]){
											$checked = 'checked';
										}else{
											$checked = '';
										} ?>
										<input name="byungMung" type="radio" class="radio" value="<?=$row[0];?>" <?=$checked;?> onclick="set_dis_nm();"><?=$row[1];
									}

									$conn->row_free();
								}else{
									$sql = "select m81_name
											  from m81gubun
											 where m81_gbn  = 'DAS'
											   and m81_code = '".$mst[0]['m03_byungmung']."'";
									$das_name = $conn->get_data($sql);?>
									<div class="left"><?=$das_name;?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">기타병명</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="diseaseNm" type="text" value="<?=$mst[0]["m03_disease_nm"];?>" style="width:100%;">
									<input name="stat_nogood" type="checkbox" class="checkbox" value="Y" <? if($mst[0]['m03_stat_nogood'] == 'Y'){?>checked<?} ?>><span id="lvl_stat_nogood">치매로 인한 수급자 상태여부</span><?
								}else{?>
									<div class="left"><?=$mst[0]["m03_disease_nm"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">목욕초과산정여부</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="bathAddYn" type="radio" class="radio" value="Y" <? if($mst[0]['m03_bath_add_yn'] == 'Y'){echo 'checked';} ?>>예
									<input name="bathAddYn" type="radio" class="radio" value="N" <? if($mst[0]['m03_bath_add_yn'] == 'N'){echo 'checked';} ?>>아니오<?
								}else{?>
									<div class="left"><?=$mst[0]["m03_bath_add_yn"] == 'Y' ? '예' : '아니오';?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="3">요양<br>보호사</th>
							<th>주담당</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yoyangsa1" type="hidden" value="<?=$ed->en($mst[0]["m03_yoyangsa1"]);?>" tag="<?=$ed->en($mst[0]["m03_yoyangsa1"]);?>">
									<input name="yoyangsa1Nm" type="text" value="<?=$mst[0]["m03_yoyangsa1_nm"];?>" style="background-color:#eeeeee; margin-top:3px;" readOnly>
									<span class="btn_pack find" style="margin-top:1px; margin-left:-5px;" onclick="__helpYoy('<?=$code;?>','<?=$kind;?>',document.getElementById('yoyangsa1'),document.getElementById('yoyangsa1Nm'));"></span>
									<span class="btn_pack m" style="margin-top:2px;"><button type="button" onclick="__notYoy(1);">삭제</button></span>
									<input name="partner" type="checkbox" class="checkbox" value="Y" <? if($mst[0]['m03_partner'] == 'Y'){?>checked<?} ?>>배우자<?
								}else{?>
									<div class="left"><?=$mst[0]["m03_yoyangsa1_nm"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>부담당</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yoyangsa2" type="hidden" value="<?=$ed->en($mst[0]["m03_yoyangsa2"]);?>" tag="<?=$ed->en($mst[0]["m03_yoyangsa2"]);?>">
									<input name="yoyangsa2Nm" type="text" value="<?=$mst[0]["m03_yoyangsa2_nm"];?>" style="background-color:#eeeeee; margin-top:3px;" readOnly>
									<span class="btn_pack find" style="margin-top:1px; margin-left:-5px;" onclick="__helpYoy('<?=$code;?>','<?=$kind;?>',document.getElementById('yoyangsa2'),document.getElementById('yoyangsa2Nm'));"></span>
									<span class="btn_pack m" style="margin-top:2px;"><button type="button" onclick="__notYoy(2);">삭제</button></span><?
								}else{?>
									<div class="left"><?=$mst[0]["m03_yoyangsa2_nm"];?></div><?
								}
							?>
							</td>
						</tr>
					</tbody>
					<tbody id="kind_1" style="display:<? if($mst[1]['m03_mkind'] != '1'){echo 'none';} ?>;">
						<tr>
							<th colspan="3">가사간병</th>
							<td class="last" colspan="3">준비중입니다.</td>
						</tr>
					</tbody>

					<tbody id="kind_2" style="display:<? if($mst[2]['m03_mkind'] != '2'){echo 'none';} ?>;">
						<tr>
							<th colspan="3">노인돌봄</th>
							<td class="last" colspan="3">준비중입니다.</td>
						</tr>
					</tbody>

					<tbody id="kind_3" style="display:<? if($mst[3]['m03_mkind'] != '3'){echo 'none';} ?>;">
						<tr>
							<th colspan="3">산모신생아</th>
							<td class="last" colspan="3">준비중입니다.</td>
						</tr>
					</tbody>

					<tbody id="kind_4" style="display:<? if($mst[4]['m03_mkind'] != '4'){echo 'none';} ?>;">
						<tr>
							<th colspan="3">장애인보조</th>
							<td class="last" colspan="3">준비중입니다.</td>
						</tr>
					</tbody>

					<tbody id="kind_5" style="display:<? if($mst[5]['m03_mkind'] != '5'){echo 'none';} ?>;">
						<tr>
							<th colspan="3">시설</th>
							<td class="last" colspan="3">준비중입니다.</td>
						</tr>
					</tbody>
					<tbody>
						<tr>
							<td class="last bottom" colspan="5"></td>
						</tr>
					</tbody>
				</table>
			</td>
			<td class="other"></td>
		</tr>
	</tbody>
</table>