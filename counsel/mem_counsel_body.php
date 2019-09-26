<?
	if (empty($is_path)) $is_path = $_POST['path'];

	include_once('mem_counsel_head.php');
?>
<table class="my_table my_border_blue" style="width:100%; margin-top:10px; border-bottom:none;">
	<colgroup>
		<col width="100px">
		<col width="362px">
		<col width="70px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>종교</th>
			<td>
				<input name="counsel_religion" type="radio" value="N" tabindex="31" class="radio" onclick="_setDisabled(this, document.getElementById('counsel_rel_other'));" <? if($mem['mem_religion'] == 'N'){?>checked<?} ?>>무
				<input name="counsel_religion" type="radio" value="1" tabindex="31" class="radio" onclick="_setDisabled(this, document.getElementById('counsel_rel_other'));" <? if($mem['mem_religion'] == '1'){?>checked<?} ?>>기독교
				<input name="counsel_religion" type="radio" value="2" tabindex="31" class="radio" onclick="_setDisabled(this, document.getElementById('counsel_rel_other'));" <? if($mem['mem_religion'] == '2'){?>checked<?} ?>>천주교
				<input name="counsel_religion" type="radio" value="3" tabindex="31" class="radio" onclick="_setDisabled(this, document.getElementById('counsel_rel_other'));" <? if($mem['mem_religion'] == '3'){?>checked<?} ?>>불교
				<input name="counsel_religion" type="radio" value="9" tabindex="31" class="radio" onclick="_setDisabled(this, document.getElementById('counsel_rel_other'));" <? if($mem['mem_religion'] == '9'){?>checked<?} ?>>기타
				<input name="counsel_rel_other" type="text" value="<?=$mem['mem_rel_other'];?>" tabindex="31" style="width:70px; margin-right:0;">
			</td>
			<th>취미/특기</th>
			<td class="last" colspan="2"><input name="counsel_hobby" type="text" tabindex="31" value="<?=$mem['mem_hobby'];?>" style="width:100%;"></td>
		</tr>
		<tr>
			<th>본인장애</th>
			<td colspan="2">
				<input name="counsel_dis_level" type="radio" value="N" tabindex="31" class="radio" <? if($mem['mem_dis_lvl'] == 'N'){?>checked<?} ?>>없음
				<input name="counsel_dis_level" type="radio" value="1" tabindex="31" class="radio" <? if($mem['mem_dis_lvl'] == '1'){?>checked<?} ?>>1등급
				<input name="counsel_dis_level" type="radio" value="2" tabindex="31" class="radio" <? if($mem['mem_dis_lvl'] == '2'){?>checked<?} ?>>2등급
				<input name="counsel_dis_level" type="radio" value="3" tabindex="31" class="radio" <? if($mem['mem_dis_lvl'] == '3'){?>checked<?} ?>>3등급
				<input name="counsel_dis_level" type="radio" value="4" tabindex="31" class="radio" <? if($mem['mem_dis_lvl'] == '4'){?>checked<?} ?>>4등급
				<input name="counsel_dis_level" type="radio" value="5" tabindex="31" class="radio" <? if($mem['mem_dis_lvl'] == '5'){?>checked<?} ?>>5등급
				<input name="counsel_dis_level" type="radio" value="6" tabindex="31" class="radio" <? if($mem['mem_dis_lvl'] == '6'){?>checked<?} ?>>6등급
			</td>
			<th>치료중인질병</th>
			<td class="last"><input name="dis_text" type="text" value="<?=$mem['mem_dis_nm'];?>" tabindex="31" style="width:100%;"></td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%; border-top:none;">
	<colgroup>
		<col width="100px">
		<col width="200px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>신청경로</th>
			<td class="last" colspan="3">
				<input name="counsel_app_path" type="radio" value="1" tabindex="31" class="radio" onclick="_setDisabled(this, document.getElementById('counsel_app_other'));" <? if($mem['mem_app_path'] == '1'){?>checked<?} ?>>지역신문
				<input name="counsel_app_path" type="radio" value="2" tabindex="31" class="radio" onclick="_setDisabled(this, document.getElementById('counsel_app_other'));" <? if($mem['mem_app_path'] == '2'){?>checked<?} ?>>인터넷취업사이트
				<input name="counsel_app_path" type="radio" value="3" tabindex="31" class="radio" onclick="_setDisabled(this, document.getElementById('counsel_app_other'));" <? if($mem['mem_app_path'] == '3'){?>checked<?} ?>>홍보물
				<input name="counsel_app_path" type="radio" value="4" tabindex="31" class="radio" onclick="_setDisabled(this, document.getElementById('counsel_app_other'));" <? if($mem['mem_app_path'] == '4'){?>checked<?} ?>>소개
				<input name="counsel_app_path" type="radio" value="5" tabindex="31" class="radio" onclick="_setDisabled(this, document.getElementById('counsel_app_other'));" <? if($mem['mem_app_path'] == '5'){?>checked<?} ?>>타기관의뢰
				<input name="counsel_app_path" type="radio" value="9" tabindex="31" class="radio" onclick="_setDisabled(this, document.getElementById('counsel_app_other'));" <? if($mem['mem_app_path'] == '9'){?>checked<?} ?>>기타
				<input name="counsel_app_other" type="text" value="<?=$mem['mem_app_other'];?>" tabindex="31" style="width:200px">
			</td>
		</tr>
		<tr>
			<th>자원봉사경험</th>
			<td class="last" colspan="3">
				<input name="counsel_service_work" type="radio" value="N" tabindex="31" class="radio" onclick="__setEnabled('counsel_service_other', false);" <? if($mem['mem_svc_work'] == 'N'){?>checked<?} ?>>무
				<input name="counsel_service_work" type="radio" value="Y" tabindex="31" class="radio" onclick="__setEnabled('counsel_service_other', true);" <? if($mem['mem_svc_work'] == 'Y'){?>checked<?} ?>>유
				<input name="counsel_service_other" type="text" value="<?=$mem['mem_svc_other'];?>" tabindex="31" style="width:200px">
			</td>
		</tr>
		<tr>
			<th>활동희망영역</th>
			<td class="last" colspan="3">
				<input name="counsel_hope_work1" type="checkbox" value="Y" tabindex="31" class="checkbox" <? if($mem['mem_hope_work'][0] == 'Y'){?>checked<?} ?>>장기요양
				<input name="counsel_hope_work2" type="checkbox" value="Y" tabindex="31" class="checkbox" <? if($mem['mem_hope_work'][1] == 'Y'){?>checked<?} ?>>노인돌봄
				<input name="counsel_hope_work3" type="checkbox" value="Y" tabindex="31" class="checkbox" <? if($mem['mem_hope_work'][2] == 'Y'){?>checked<?} ?>>가사간병
				<input name="counsel_hope_work4" type="checkbox" value="Y" tabindex="31" class="checkbox" <? if($mem['mem_hope_work'][3] == 'Y'){?>checked<?} ?>>산모신생아
				<input name="counsel_hope_work5" type="checkbox" value="Y" tabindex="31" class="checkbox" <? if($mem['mem_hope_work'][4] == 'Y'){?>checked<?} ?>>장애인활동보조
				<input name="counsel_hope_work6" type="checkbox" value="Y" tabindex="31" class="checkbox" onclick="__setEnabled('counsel_hope_other', this.checked);" <? if($mem['mem_hope_work'][5] == 'Y'){?>checked<?} ?>>기타
				<input name="counsel_hope_other" type="text" value="<?=$mem['mem_hope_other'];?>" tabindex="31" style="width:200px">
			</td>
		</tr>
		<tr>
			<th>근무가능시간</th>
			<td>
				<input name="counsel_work_time" type="radio" value="1" tabindex="31" class="radio" <? if($mem['mem_work_time'] == '1'){?>checked<?} ?>>종일
				<input name="counsel_work_time" type="radio" value="2" tabindex="31" class="radio" <? if($mem['mem_work_time'] == '2'){?>checked<?} ?>>오전
				<input name="counsel_work_time" type="radio" value="3" tabindex="31" class="radio" <? if($mem['mem_work_time'] == '3'){?>checked<?} ?>>오후
			</td>
			<th>희망소득</th>
			<td class="left last">
				월<input name="counsel_hope_salary" type="text" value="<?=number_format($mem['mem_salary']);?>" tabindex="31" class="number" onkeydown="__onlyNumber(this);">원
				(시급:<input name="counsel_hope_hourly" type="text" value="<?=number_format($mem['mem_hourly']);?>" tabindex="31" class="number" onkeydown="__onlyNumber(this);">원)
			</td>
		</tr>
	</tbody>
</table>

<!-- 가족사항 -->
<table id="tbl_family" class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="100px">
		<col width="100px">
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

<!-- 교육이수 -->
<table id="tbl_edu" class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="100px">
		<col width="100px">
		<col width="150px">
		<col width="150px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody id="my_edu">
		<tr>
			<th id="edu_span" rowspan="<?=$edu_cnt + 1;?>">교육수료</th>
			<th class="head">교육구분</th>
			<th class="head">교육기관</th>
			<th class="head">교육명</th>
			<th class="head">교육시간</th>
			<th class="head last">비고</th>
		</tr>
		<?
			for($i=0; $i<$edu_cnt; $i++){
				$id = $i + 1;?>
				<tr id="edu_row_<?=$id;?>">
					<td class="center">
						<select name="edu_gbn[]" style="width:auto;">
							<option value="1" <? if($edu[$i]['edu_gbn'] == '1'){?>selected<?} ?>>돌봄관련교육</option>
							<option value="9" <? if($edu[$i]['edu_gbn'] == '9'){?>selected<?} ?>>기타교육</option>
						</select>
					</td>
					<td class="center"><input name="edu_center[]" type="text" value="<?=$edu[$i]['edu_center'];?>" style="width:100%;"></td>
					<td class="center"><input name="edu_name[]" type="text" value="<?=$edu[$i]['edu_nm'];?>" style="width:100%;"></td>
					<td class="center"><input name="edu_date[]" type="text" value="<?=$edu[$i]['edu_time'];?>" style="width:100%;"></td><?
					if ($i == 0){?>
						<td class="left last"><span class="btn_pack m"><button type="button" onclick="edu_tbl.t_add_row();">추가</button></span></td><?
					}else{?>
						<td class="left last"><span class="btn_pack m"><button type="button" onclick="edu_tbl.t_delete_row('edu_row_<?=$id;?>', 0);">삭제</button></span></td><?
					}?>
				</tr><?
			}
		?>
	</tbody>
</table>

<!-- 자격 -->
<table id="tbl_license" class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="100px">
		<col width="150px">
		<col width="150px">
		<col width="150px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody id="my_license">
		<tr>
			<th id="license_span" rowspan="<?=$li_cnt + 1;?>">자격증 및 수료</th>
			<th class="head">자격증종류</th>
			<th class="head">자격증번호</th>
			<th class="head">발급기관</th>
			<th class="head">발급일자</th>
			<th class="head last">비고</th>
		</tr>
		<?
			for($i=0; $i<$li_cnt; $i++){
				$id = $i + 1;?>
				<tr id="license_row_<?=$id;?>">
					<td class="center"><input name="license_type[]" type="text" value="<?=$li[$i]['license_gbn'];?>" style="width:100%;" onKeyDown="__enterFocus();"></td>
					<td class="center"><input name="license_no[]" type="text" value="<?=$li[$i]['license_no'];?>" style="width:100%;" onKeyDown="__enterFocus();"></td>
					<td class="center"><input name="license_center[]" type="text" value="<?=$li[$i]['license_center'];?>" style="width:100%;" onKeyDown="__enterFocus();"></td>
					<td class="center"><input name="license_date[]" type="text" value="<?=$li[$i]['license_dt'];?>" class="date" onclick="_carlendar(this);" onKeyDown="if(event.keyCode == 13){li_tbl.t_add_row();}"></td><?
					if ($i == 0){?>
						<td class="left last"><span class="btn_pack m"><button type="button" onclick="li_tbl.t_add_row();">추가</button></span></td><?
					}else{?>
						<td class="left last"><span class="btn_pack m"><button type="button" onclick="li_tbl.t_delete_row('license_row_<?=$id;?>', 0);">삭제</button></span></td><?
					}?>
				</tr><?
			}
		?>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%; margin-top:10px; margin-bottom:10px;">
	<colgroup>
		<col width="100px">
		<col width="150px">
		<col width="100px">
		<col width="200px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>상담자</th>
			<td class="left">
				<span class="btn_pack find" onClick="__find_yoyangsa('<?=$code;?>','<?=$kind;?>','talker_cd','talker_str');"></span>
				<span id="talker_str" style="height:100%; margin-left:5px; font-weight:bold;"><?=$mem['mem_talker_nm'];?></span>
				<input name="talker_cd" type="hidden" value="<?=$ed->en($mem['mem_talker_id']);?>">
				<input name="talker_nm" type="hidden" value="<?=$mem['mem_talker_nm'];?>">
			</td>
			<th>상담유형</th>
			<td>
				<input name="counsel_type" type="radio" class="radio" value="1" <? if($mem['mem_counsel_gbn'] == '1'){?>checked<?} ?>>내방
				<input name="counsel_type" type="radio" class="radio" value="2" <? if($mem['mem_counsel_gbn'] == '2'){?>checked<?} ?>>방문
				<input name="counsel_type" type="radio" class="radio" value="3" <? if($mem['mem_counsel_gbn'] == '3'){?>checked<?} ?>>전화
			</td>
			<th>상담일자</th>
			<td class="last">
				<input name="counsel_date" type="text" value="<?=$mem['mem_counsel_dt'];?>" class="date" onkeydown="__onlyNumber(this);" onclick="_carlendar(this);">
			</td>
		</tr>
		<tr>
			<th>상담내용</th>
			<td class="last" colspan="5">
				<textarea name="counsel_cont" style="width:100%; height:50px;"><?=stripslashes($mem['mem_counsel_content']);?></textarea>
			</td>
		</tr>
		<tr>
			<th>조치사항</th>
			<td class="last" colspan="5">
				<textarea name="counsel_action" style="width:100%; height:50px;"><?=stripslashes($mem['mem_counsel_action']);?></textarea>
			</td>
		</tr>
		<tr>
			<th>처리결과</th>
			<td class="last" colspan="5">
				<textarea name="counsel_result" style="width:100%; height:50px;"><?=stripslashes($mem['mem_counsel_result']);?></textarea>
			</td>
		</tr>
		<tr>
			<th>기타</th>
			<td class="last" colspan="5">
				<textarea name="counsel_other" style="width:100%; height:50px;"><?=stripslashes($mem['mem_counsel_other']);?></textarea>
			</td>
		</tr>
	</tbody>
</table>