<table class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="100px">
		<col width="100px">
		<col width="260px">
		<col width="100px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th rowspan="3">건강상태</th>
			<th>질병명</th>
			<td><input name="normal_sick_nm" type="text" value="<?=$normal['health_sick_nm'];?>" style="width:100%;"></td>
			<th>약복용</th>
			<td colspan="2" class="last"><input name="normal_drug_nm" type="text" value="<?=$normal['health_drug_nm'];?>" style="width:100%;"></td>
		</tr>
		<tr>
			<th>진단명</th>
			<td><input name="normal_diag_nm" type="text" value="<?=$normal['health_diag_nm'];?>" style="width:100%;"></td>
			<th>장애명</th>
			<td colspan="2" class="last"><input name="normal_dis_nm" type="text" value="<?=$normal['health_dis_nm'];?>" style="width:100%;"></td>
		</tr>
		<tr>
			<th>시력</th>
			<td>
				<input name="normal_eye" type="radio" class="radio" value="1" <? if($normal['health_eye_kind'] == '1'){echo 'checked';} ?>>양호
				<input name="normal_eye" type="radio" class="radio" value="2" <? if($normal['health_eye_kind'] == '2'){echo 'checked';} ?>>보통
				<input name="normal_eye" type="radio" class="radio" value="3" <? if($normal['health_eye_kind'] == '3'){echo 'checked';} ?>>나쁨
			</td>
			<th>청력</th>
			<td colspan="2" class="last">
				<input name="normal_ear" type="radio" class="radio" value="1" <? if($normal['health_ear_kind'] == '1'){echo 'checked';} ?>>양호
				<input name="normal_ear" type="radio" class="radio" value="2" <? if($normal['health_ear_kind'] == '2'){echo 'checked';} ?>>보통
				<input name="normal_ear" type="radio" class="radio" value="3" <? if($normal['health_ear_kind'] == '3'){echo 'checked';} ?>>나쁨
			</td>
		</tr>
		<tr>
			<th>신체상태</th>
			<th>운동 및 활동</th>
			<td class="last" colspan="4">
				<input name="normal_activate" type="radio" class="radio" value="1" <? if($normal['body_activate_kind'] == '1'){echo 'checked';} ?>>완전자립
				<input name="normal_activate" type="radio" class="radio" value="2" <? if($normal['body_activate_kind'] == '2'){echo 'checked';} ?>>부분자립
				<input name="normal_activate" type="radio" class="radio" value="3" <? if($normal['body_activate_kind'] == '3'){echo 'checked';} ?>>전적인도움
			</td>
		</tr>
		<tr>
			<th rowspan="2">영양상태 및<br>위생상태</th>
			<th>식사 및 영양</th>
			<td>
				<input name="normal_eat" type="radio" class="radio" value="1" <? if($normal['nutr_eat_kind'] == '1'){echo 'checked';} ?>>완전자립
				<input name="normal_eat" type="radio" class="radio" value="2" <? if($normal['nutr_eat_kind'] == '2'){echo 'checked';} ?>>부분자립
				<input name="normal_eat" type="radio" class="radio" value="3" <? if($normal['nutr_eat_kind'] == '3'){echo 'checked';} ?>>전적인도움
			</td>
			<th>배설</th>
			<td colspan="2" class="last">
				<input name="normal_excreata" type="radio" class="radio" value="1" <? if($normal['nutr_excreta_kind'] == '1'){echo 'checked';} ?>>완전자립
				<input name="normal_excreata" type="radio" class="radio" value="2" <? if($normal['nutr_excreta_kind'] == '2'){echo 'checked';} ?>>부분자립
				<input name="normal_excreata" type="radio" class="radio" value="3" <? if($normal['nutr_excreta_kind'] == '3'){echo 'checked';} ?>>전적인도움
			</td>
		</tr>
		<tr>
			<th colspan="2">위생관리(구강/세면/세발/손/발톱관리/목욕)</th>
			<td class="last" colspan="3">
				<input name="normal_hygiene" type="radio" class="radio" value="1" <? if($normal['nutr_hygiene_kind'] == '1'){echo 'checked';} ?>>완전자립
				<input name="normal_hygiene" type="radio" class="radio" value="2" <? if($normal['nutr_hygiene_kind'] == '2'){echo 'checked';} ?>>부분자립
				<input name="normal_hygiene" type="radio" class="radio" value="3" <? if($normal['nutr_hygiene_kind'] == '3'){echo 'checked';} ?>>전적인도움
				<input name="normal_hygiene" type="radio" class="radio" value="4" <? if($normal['nutr_hygiene_kind'] == '4'){echo 'checked';} ?>>기타시설
			</td>
		</tr>
		<!--
			<tr>
				<th rowspan="2">의사소통 상태</th>
				<th>정서적상태</th>
				<td class="last" colspan="4">
					<input name="normal_mind" type="radio" class="radio" value="1" <? if($normal['talk_mind_kind'] == '1'){echo 'checked';} ?> onclick="__setEnabled('normal_mind_other', false);">활발/적극
					<input name="normal_mind" type="radio" class="radio" value="2" <? if($normal['talk_mind_kind'] == '2'){echo 'checked';} ?> onclick="__setEnabled('normal_mind_other', false);">조용/내성적
					<input name="normal_mind" type="radio" class="radio" value="3" <? if($normal['talk_mind_kind'] == '3'){echo 'checked';} ?> onclick="__setEnabled('normal_mind_other', false);">흥분/우울
					<input name="normal_mind" type="radio" class="radio" value="9" <? if($normal['talk_mind_kind'] == '9'){echo 'checked';} ?> onclick="__setEnabled('normal_mind_other', true);">기타
					<input name="normal_mind_other" type="text" value="<?=$normal['talk_mind_other'];?>">
				</td>
			</tr>
			<tr>
				<th>의사소통 및<br>의식상태</th>
				<td class="last" colspan="4"><textarea name="normal_talk_stat" style="width:100%; height:90px;"><?=$normal['talk_status'];?></textarea></td>
			</tr>
		-->
		<tr>
			<th>의사소통 상태</th>
			<th>정서적상태</th>
			<td class="last" colspan="4">
				<input name="normal_mind" type="radio" class="radio" value="1" <? if($normal['talk_mind_kind'] == '1'){echo 'checked';} ?> onclick="__setEnabled('normal_mind_other', false);">활발/적극
				<input name="normal_mind" type="radio" class="radio" value="2" <? if($normal['talk_mind_kind'] == '2'){echo 'checked';} ?> onclick="__setEnabled('normal_mind_other', false);">조용/내성적
				<input name="normal_mind" type="radio" class="radio" value="3" <? if($normal['talk_mind_kind'] == '3'){echo 'checked';} ?> onclick="__setEnabled('normal_mind_other', false);">흥분/우울
				<input name="normal_mind" type="radio" class="radio" value="9" <? if($normal['talk_mind_kind'] == '9'){echo 'checked';} ?> onclick="__setEnabled('normal_mind_other', true);">기타
				<input name="normal_mind_other" type="text" value="<?=$normal['talk_mind_other'];?>">
			</td>
		</tr>
		<tr>
			<th>
				신체,배설상태<br>
				영양,위생상태<br>
				의사소통,의식상태
			</th>
			<td class="last" colspan="5"><textarea name="normal_talk_stat" style="width:100%; height:90px;"><?=stripslashes($normal['talk_status']);?></textarea></td>
		</tr>
		<tr>
			<th>인지상태</th>
			<th>인지력 및 기억력</th>
			<td>
				<input name="normal_remember" type="radio" class="radio" value="1" <? if($normal['rec_remember_kind'] == '1'){echo 'checked';} ?>>명확
				<input name="normal_remember" type="radio" class="radio" value="2" <? if($normal['rec_remember_kind'] == '2'){echo 'checked';} ?>>부분도움
				<input name="normal_remember" type="radio" class="radio" value="3" <? if($normal['rec_remember_kind'] == '3'){echo 'checked';} ?>>불가능
			</td>
			<th>표현력</th>
			<td colspan="2" class="last">
				<input name="normal_express" type="radio" class="radio" value="1" <? if($normal['rec_express_kind'] == '1'){echo 'checked';} ?>>명확
				<input name="normal_express" type="radio" class="radio" value="2" <? if($normal['rec_express_kind'] == '2'){echo 'checked';} ?>>부분도움
				<input name="normal_express" type="radio" class="radio" value="3" <? if($normal['rec_express_kind'] == '3'){echo 'checked';} ?>>불가능
			</td>
		</tr>
		<tr>
			<th>자원이용</th>
			<th>자원이용현황</th>
			<td colspan="2">
				<input name="normal_use_center" type="radio" class="radio" value="1" <? if($normal['center_use_kind'] == '1'){echo 'checked';} ?> onclick="__setEnabled('normal_use_other', false);">없음
				<input name="normal_use_center" type="radio" class="radio" value="2" <? if($normal['center_use_kind'] == '2'){echo 'checked';} ?> onclick="__setEnabled('normal_use_other', true);">의료기관
				<input name="normal_use_center" type="radio" class="radio" value="3" <? if($normal['center_use_kind'] == '3'){echo 'checked';} ?> onclick="__setEnabled('normal_use_other', true);">사회복지기관
				<input name="normal_use_center" type="radio" class="radio" value="4" <? if($normal['center_use_kind'] == '4'){echo 'checked';} ?> onclick="__setEnabled('normal_use_other', true);">기타
			</td>
			<th>기관명</th>
			<td class="last"><input name="normal_use_other" type="text" value="<?=$normal['center_use_other'];?>" style="width:100%;"></td>
		</tr>
	</tbody>
</table>