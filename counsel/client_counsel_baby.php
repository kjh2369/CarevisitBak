<?
	$baby_hope_svc[0][0] = '산모영양관리(식사)';
	$baby_hope_svc[0][1] = '유방관리';
	$baby_hope_svc[0][2] = '산후체조';
	$baby_hope_svc[0][3] = '좌욕';
	$baby_hope_svc[0][4] = '정시적 지지 및 안정';

	$baby_hope_svc[1][0] = '수유보조';
	$baby_hope_svc[1][1] = '아기목욕';
	$baby_hope_svc[1][2] = '건강관리 및 예방접종';
	$baby_hope_svc[1][3] = '신생아 마사지';
	$baby_hope_svc[1][4] = '';

	$baby_hope_svc[2][0] = '세탁물관리';
	$baby_hope_svc[2][1] = '식사상차림';
	$baby_hope_svc[2][2] = '요리';
	$baby_hope_svc[2][3] = '방청소';
	$baby_hope_svc[2][4] = '';

	$baby_hope_svc[3][0] = '큰아이돌보기';
	$baby_hope_svc[3][1] = '';
	$baby_hope_svc[3][2] = '';
	$baby_hope_svc[3][3] = '';
	$baby_hope_svc[3][4] = '';

	$baby_hope_cnt = sizeof($baby_hope_svc);
	$baby_hope_list = sizeof($baby_hope_svc[0]);
?>

<table class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="100px">
		<col width="100px">
		<col width="150px">
		<col width="100px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th rowspan="6">건강상태</th>
			<th>출산일</th>
			<td><input name="baby_delivery_dt" type="text" class="date" value="<?=$baby['health_delivery_dt'];?>"></td>
			<th>출산형태</th>
			<td class="last" colspan="2"><input name="baby_delivery_kind" type="text" value="<?=$baby['health_delivery_kind'];?>" style="width:100%;"></td>
		</tr>
		<tr>
			<th>장애등급</th>
			<td class="last" colspan="4">
				<input name="baby_dis_lvl" type="radio" class="radio" value="N" <? if($baby['health_dis_lvl'] == 'N'){echo 'checked';} ?> onclick="set_baby_dis_kind();">없음
				<input name="baby_dis_lvl" type="radio" class="radio" value="1" <? if($baby['health_dis_lvl'] == '1'){echo 'checked';} ?> onclick="set_baby_dis_kind();">1등급
				<input name="baby_dis_lvl" type="radio" class="radio" value="2" <? if($baby['health_dis_lvl'] == '2'){echo 'checked';} ?> onclick="set_baby_dis_kind();">2등급
				<input name="baby_dis_lvl" type="radio" class="radio" value="3" <? if($baby['health_dis_lvl'] == '3'){echo 'checked';} ?> onclick="set_baby_dis_kind();">3등급
				<input name="baby_dis_lvl" type="radio" class="radio" value="4" <? if($baby['health_dis_lvl'] == '4'){echo 'checked';} ?> onclick="set_baby_dis_kind();">4등급
				<input name="baby_dis_lvl" type="radio" class="radio" value="5" <? if($baby['health_dis_lvl'] == '5'){echo 'checked';} ?> onclick="set_baby_dis_kind();">5등급
				<input name="baby_dis_lvl" type="radio" class="radio" value="6" <? if($baby['health_dis_lvl'] == '6'){echo 'checked';} ?> onclick="set_baby_dis_kind();">6등급
			</td>
		</tr>
		<tr>
			<th>장애유형</th>
			<td class="last" colspan="4">
				<input name="baby_dis_kind" type="radio" class="radio" value="1" <? if($baby['health_dis_kind'] == '1'){echo 'checked';} ?>>정신질환
				<input name="baby_dis_kind" type="radio" class="radio" value="2" <? if($baby['health_dis_kind'] == '2'){echo 'checked';} ?>>호흡기장애
				<input name="baby_dis_kind" type="radio" class="radio" value="3" <? if($baby['health_dis_kind'] == '3'){echo 'checked';} ?>>지체장애
				<input name="baby_dis_kind" type="radio" class="radio" value="4" <? if($baby['health_dis_kind'] == '4'){echo 'checked';} ?>>시각장애
				<input name="baby_dis_kind" type="radio" class="radio" value="5" <? if($baby['health_dis_kind'] == '5'){echo 'checked';} ?>>청각장애
				<input name="baby_dis_kind" type="radio" class="radio" value="6" <? if($baby['health_dis_kind'] == '6'){echo 'checked';} ?>>언어장애
				<input name="baby_dis_kind" type="radio" class="radio" value="9" <? if($baby['health_dis_kind'] == '9'){echo 'checked';} ?>>기타
			</td>
		</tr>
		<tr>
			<th>수유상태</th>
			<td colspan="2"><input name="baby_nurse" type="text" value="<?=$baby['health_nurse'];?>" style="width:100%;"></td>
			<th rowspan="2">약물복용</th>
			<td class="last" rowspan="2"><textarea name="baby_drug" style="width:100%; height:50px; margin-top:1px; margin-bottom:1px;"><?=$baby['health_drug'];?></textarea></td>
		</tr>
		<tr>
			<th>심리상태</th>
			<td colspan="2"><input name="baby_mind" type="text" value="<?=$baby['health_mind'];?>" style="width:100%;"></td>
		</tr>
		<tr>
			<th>신체적건강상태</th>
			<td class="last" colspan="4"><input name="baby_body" type="text" value="<?=$baby['health_body'];?>" style="width:100%;"></td>
		</tr>
		<tr>
			<th rowspan="3">가족 및<br>환경족 욕구</th>
			<th>가족상황</th>
			<td class="last" colspan="4"><input name="baby_family" type="text" value="<?=$baby['family_status'];?>" style="width:100%;"></td>
		</tr>
		<tr>
			<th>주거상황</th>
			<td class="last" colspan="4"><input name="baby_abode" type="text" value="<?=$baby['family_abode'];?>" style="width:100%;"></td>
		</tr>
		<tr>
			<th>기타</th>
			<td class="last" colspan="4"><input name="baby_abode_other" type="text" value="<?=$baby['family_other'];?>" style="width:100%;"></td>
		</tr>
		<tr>
			<th rowspan="2">희망서비스</th>
			<td class="last" colspan="5">
				<table class="my_table" style="width:100%; border-bottom:none;">
					<colgroup>
						<col width="25%" span="4">
					</colgroup>
					<thead>
						<tr>
							<th class="head">산모</th>
							<th class="head">신생아</th>
							<th class="head">가사</th>
							<th class="head last">기타</th>
						</tr>
					</thead>
					<tbody>
					<?
						for($i=0; $i<$baby_hope_list; $i++){
							echo '<tr>';

							if ($i == $baby_hope_list - 1){
								$class = 'bottom ';
							}else{
								$class = '';
							}

							for($j=0; $j<$baby_hope_cnt; $j++){
								$id = $j * $baby_hope_list + $i;

								if ($baby['hope_service'][$id] == 'Y')
									$hope_checked = 'checked';
								else
									$hope_checked = '';

								if ($j == $baby_hope_cnt - 1)
									$class .= 'last';

								echo '<td class=\''.$class.'\'>';

								if ($baby_hope_svc[$j][$i] != '')
									echo '<input name="baby_hope_svc_'.$j.'_'.$i.'" type="checkbox" class="checkbox" value="Y" '.$hope_checked.'><a href=\'#\' onclick=\'document.f.baby_hope_svc_'.$j.'_'.$i.'.checked=(document.f.baby_hope_svc_'.$j.'_'.$i.'.checked ? false : true); return false;\'>'.$baby_hope_svc[$j][$i].'</a>';
								else
									echo '<input name="baby_hope_svc_'.$j.'_'.$i.'" type="hidden" value="N">';

								echo '</td>';
							}

							echo '</tr>';
						}

						echo '<input name=\'baby_hope_row\' type=\'hidden\' value=\''.$baby_hope_cnt.'\'>';
						echo '<input name=\'baby_hope_col\' type=\'hidden\' value=\''.$baby_hope_list.'\'>';
					?>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td class="last" colspan="5">
				<table class="my_table" style="width:100%; border-bottom:none;">
					<colgroup>
						<col width="80px">
						<col width="100px">
						<col width="80px">
						<col width="100px">
						<col width="80px">
						<col width="80px">
						<col width="80px">
						<col width="80px">
					</colgroup>
					<tbody>
						<tr>
							<th class="bottom">서비스일자</th>
							<td class="bottom"><input name="baby_hope_dt" type="text" class="date" value="<?=$baby['svc_dt'];?>" style="width:100%;"></td>
							<th class="bottom">서비스기간</th>
							<td class="bottom"><input name="baby_hope_period" type="text" value="<?=$baby['svc_period'];?>" style="width:100%;"></td>
							<th class="bottom">서비스시간</th>
							<td class="bottom"><input name="baby_hope_time" type="text" value="<?=$baby['svc_time'];?>" style="width:100%;"></td>
							<th class="bottom">이용금액</th>
							<td class="bottom last"><input name="baby_hope_amt" type="text" value="<?=$baby['svc_use_amt'];?>" style="width:100%; text-align:right;"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th>기타</th>
			<td class="last" colspan="5"><input name="baby_other" type="text" value="<?=$baby['other'];?>" style="width:100%;"></td>
		</tr>
	</tbody>
</table>