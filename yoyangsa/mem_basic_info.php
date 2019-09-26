<?
	if ($view_type == 'read'){
	}else{
		echo '<div>';
	}

	//급여내역
	$sql = 'SELECT COUNT(*)
			  FROM salary_basic
			 WHERE org_no       = \''.$code.'\'
			   AND salary_jumin = \''.$jumin.'\'
			   AND salary_yymm >= \''.SubStr(Str_Replace('-','',$memHis['join_dt']),0,6).'\'
			   AND salary_yymm <= \''.SubStr(Str_Replace('-','',(!Empty($memHis['quit_dt']) ? $memHis['quit_dt'] : '9999-12-31')),0,6).'\'';
	$liSalaryCnt = $conn->get_data($sql);

	//배상책임보험등록내역
	$sql = 'SELECT stat
			  FROM insu
			 WHERE org_no  = \''.$code.'\'
			   AND jumin   = \''.$jumin.'\'
			   AND join_dt = \''.$memHis['join_dt'].'\'';
	$lsInsuStat = $conn->get_data($sql);

	if ($mem_mode == 1 && ($liSalaryCnt > 0 /*|| !Empty($lsInsuStat)*/)){
		$lbEditJoinDt = false;
	}else{
		$lbEditJoinDt = true;
	}

	if ($code == '31138000062'){
		$lbEditJoinDt = true;
	}

	//배상책임가입시 퇴사를 막는다.
	if ($lsInsuStat == '1' || $lsInsuStat == '3'){
		$lbEditQuitDt = false;
	}else{
		$lbEditQuitDt = true;
	}
	$lbEditQuitDt = true;

	//배상책임보험사
	/*
	$sql = 'SELECT g02_ins_code AS code
			,      g02_ins_from_date AS from_dt
			,      g02_ins_to_date AS to_dt
			  FROM g02inscenter
			 WHERE g02_ccode = \''.$code.'\'
			   AND g02_mkind = \'0\'';
	 */
	$today = Date('Y-m-d');

	$sql = 'SELECT	insu_cd
			FROM	insu_center
			WHERE	org_no  = \''.$code.'\'
			AND		from_dt <= \''.$today.'\'
			AND		to_dt	>= \''.$today.'\'
			LIMIT 1';
	$lsInsuCode = $conn->get_data($sql);
	
	$sql = 'SELECT	gbn,yymm
			FROM	mem_direct_gbn
			WHERE	org_no  = \''.$code.'\'
			AND     jumin   = \''.$jumin.'\'
			ORDER BY yymm desc
			LIMIT 1';
	
	$direct = $conn->get_array($sql);
	

	$lbInsuMenuShow = false;

	#if ($lsInsuCode == '0' ||
	#	$lsInsuCode == '8' ||
	#	($gDomain == 'kdolbom.net' && $lsInsuCd == '2')){
	#	//배상책임 연계를 우선 막는다.
	#	//$lbInsuMenuShow = true;
	#}

	//동부화재
	if ($lsInsuCode == '4'){
		$lbInsuMenuShow = true;
	}

	if ($view_type == 'read'){?>
		<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
			<table class="my_table my_border_blue" style="width:340px;">
				<colgroup>
					<col width="60px">
					<col width="90px">
					<col width="60px">
					<col width="90px">
				</colgroup>
				<thead>
					<tr>
						<th class="head bold" colspan="5">기본정보</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>사번</th>
						<td class="left"><?=$memHis['com_no']; //$mst[$basic_kind]['m02_mem_no'];?></td>
						<th>사용자ID</th>
						<td class="left last"><?=$member['code'];?></td>
					</tr>
					<tr>
						<th></th>
						<td></td>
						<th>비밀번호</th>
						<td class="left last"><?=$member['pswd'];?></td>
					</tr>
					<tr>
						<th>입사일자</th>
						<td class="left"><?=$myF->dateStyle($memHis['join_dt']/*$mst[$basic_kind]["m02_yipsail"]*/);?></td>
						<th>퇴사일자</th>
						<td class="left last"><?=$myF->dateStyle($memHis['quit_dt']/*$mst[$basic_kind]["m02_ytoisail"]*/);?></td>
					</tr>
					<tr>
						<th>부서</th>
						<td class="left">
						<?
							$sql = "select dept_nm
									  from dept
									 where org_no   = '$code'
									   and dept_cd  = '".$mst[$basic_kind]['m02_dept_cd']."'
									   and del_flag = 'N'";

							echo $conn->get_data($sql);
						?>
						</td>
						<th>직무</th>
						<td class="left last">
						<?
							$sql = 'select job_nm
									  from job_kind
									 where org_no = \''.$code.'\'
									   and job_cd = \''.$mst[$basic_kind]['m02_yjikjong'].'\'';

							echo $conn->get_data($sql);
						?>
						</td>
					</tr>
					<tr>
						<th>고용형태</th>
						<td>
						<?
							switch($memHis['employ_type']/*$mst[$basic_kind]["m02_ygoyong_kind"]*/){
								case '1':
									$tmp = '정규직';
									break;
								case '2':
									$tmp = '계약직';
									break;
								case '3':
									$tmp = '단시간근로자';
									break;
							}
							echo '<div class=\'left\'>'.$tmp.'</div>';
						?>
						</td>
						<th>고용상태</th>
						<td>
						<?
							switch($memHis['employ_stat']/*$mst[$basic_kind]["m02_ygoyong_stat"]*/){
								case '1':
									$tmp = '재직';
									break;
								case '2':
									$tmp = '휴직';
									break;
								case '9':
									$tmp = '퇴사';
									break;
							}
							echo '<div class=\'left\'>'.$tmp.'</div>';
						?>
						</td>
					</tr>
					<tr>
						<th>주휴요일</th>
						<td>
						<?
							$tmp = '<span style=\'font-weight:bold;';

							switch($memHis['weekly']/*$mst[$basic_kind]["m02_weekly_holiday"]*/){
								case '1':
									$tmp .= '\'>월';
									break;
								case '2':
									$tmp .= '\'>화';
									break;
								case '3':
									$tmp .= '\'>수';
									break;
								case '4':
									$tmp .= '\'>목';
									break;
								case '5':
									$tmp .= '\'>금';
									break;
								case '6':
									$tmp .= 'color:#0000ff;\'>토';
									break;
								case '0':
									$tmp .= 'color:#ff0000;\'>일';
									break;
							}

							echo '<div class=\'left\'>'.$tmp.'요일</span></div>';
						?>
						</td>
					</tr>
				</tbody>
			</table>
		</div><?
	}else{?>
		<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
			<table class="my_table my_border_blue">
				<colgroup>
					<col width="60px">
					<col width="90px">
					<col width="60px">
					<col width="90px">
				</colgroup>
				<thead>
					<tr>
						<th class="head bold" colspan="5">기본정보</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>사번</th>
						<td><input name="member_no" type="text" value="<?=$memHis['com_no']; //$mst[$basic_kind]['m02_mem_no'];?>" style="width:100%;" maxlength="15" onchange="return chk_memno(this);" tag="사번을 입력하여 주십시오."></td>
						<th>직원번호</th>
						<td><input name="mem_no" type="text" value="<?=$memHis['mem_no']; //$mst[$basic_kind]['m02_mem_no'];?>" style="width:100%;" maxlength="20" tag="직원번호를 입력하여 주십시오."></td>
					</tr>
					<tr>
						<th>근무시작일</th>
						<td><input id="workDt" name="workDt" type="text" value="<?=$memHis['work_start_dt'];?>" class="date"></td>
						<th>사용자ID</th>
						<td>
							<span class="left" id="lblMemCd"><?=$member['code'];?></span>
							<input id="memId" name="memId" type="hidden" value="<?=$member['code'];?>">
						</td>
					</tr>
					<tr>
						<th>입사일자</th>
						<td>
							<div style="float:left; width:auto;">
								<input id="joinDt" name="join_dt" title="<?=(!$lbEditJoinDt ? '※급여계산 후 변경금지' : '');?>" type="text" value="<?=$myF->dateStyle($memHis['join_dt']/*$mst[$basic_kind]["m02_yipsail"]*/);?>" class="date" onchange="return _memChkJoinDt(this);" tag="입사일자를 입력하여 주십시오." <?=(!$lbEditJoinDt ? 'readonly' : '');?>>
							</div>
							<div style="float:left; width:auto;"><?
								$liSalaryGbn = 0;

								if ($liSalaryCnt > 0){
									$liSalaryGbn = 1;
								}

								if (!Empty($lsInsuStat)){
									$liSalaryGbn += 2;
								}

								if ($liSalaryGbn > 0){
									//echo '※급여계산 후 변경금지';
								}?>
							</div>
						</td>
						<th>비밀번호</th>
						<td><span class="left"><?=$member['pswd'];?></span></td>
					</tr>
				</tbody>
			</table>
		</div>

		<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
			<table class="my_table my_border_blue">
				<colgroup>
					<col width="60px">
					<col width="100px">
				</colgroup>
				<thead>
					<tr>
						<th class="head bold" colspan="3">소속</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>부서</th>
						<td style="padding-left:5px; padding-right:5px;">
							<select name="dept" style="width:100%; margin:0;" onchange="this.title=this.options[this.selectedIndex].text;">
								<option value="">--</option><?
								$sql = "select dept_cd, dept_nm
										  from dept
										 where org_no   = '$code'
										   and del_flag = 'N'
										 order by order_seq";

								$conn->query($sql);
								$conn->fetch();

								$row_count = $conn->row_count();

								for($i=0; $i<$row_count; $i++){
									$row = $conn->select_row($i);?>
									<option value="<?=$row['dept_cd'];?>" title="<?=$row['dept_nm'];?>" <? if($mst[$basic_kind]['m02_dept_cd'] == $row['dept_cd']){?>selected<?} ?>><?=$row['dept_nm'];?></option><?
								}

								$conn->row_free();
							?>
							</select>
						</td>
					</tr>
					<tr>
						<th>직무</th>
						<td style="padding-left:5px; padding-right:5px;">
						<?
							$sql = 'select *
									  from job_kind
									 where org_no = \''.$code.'\'
									   and del_flag = \'N\'
									 order by job_seq';

							$conn->query($sql);
							$conn->fetch();

							$row_count = $conn->row_count();

							echo '<select name="job_kind" style="width:100%; margin:0;">';
							echo '<option value="">--</option>';

							for($i=0; $i<$row_count; $i++){
								$row = $conn->select_row($i);

								echo '<option value="'.$row['job_cd'].'" '.($mst[$basic_kind]['m02_yjikjong'] == $row['job_cd'] ? 'selected' : '').'>'.$row['job_nm'].'</option>';
							}

							echo '</select>';

							$conn->row_free();
						?>
						</td>
					</tr>
					<tr>
						<th>직위</th>
						<td>
							<select id="memPos" name="memPos" style="width:100px;">
								<option value="">--</option><?
								$sql = 'SELECT	pos_cd
										,		pos_nm
										FROM	mem_pos
										WHERE	org_no		= \''.$code.'\'
										AND		del_flag	= \'N\'
										ORDER	BY	pos_seq';

								$conn->query($sql);
								$conn->fetch();

								$rowCnt	= $conn->row_count();

								for($i=0; $i<$rowCnt; $i++){
									$row	= $conn->select_row($i);?>
									<option value="<?=$row['pos_cd'];?>" title="<?=$row['pos_nm'];?>" <? if ($memHis['mem_pos'] == $row['pos_cd']){?>selected<?} ?>><?=$row['pos_nm'];?></option><?
								}

								$conn->row_free();?>
							</select>
							<input name="pay_step" type="hidden" value="<?=$mst[$basic_kind]['m02_pay_step'];?>">
						</td>
					</tr>
					<!--tr>
						<th>호봉</th>
						<td><input name="pay_step" type="text" value="<?=$mst[$basic_kind]['m02_pay_step'];?>" class="number" style="width:50px;"></td>
					</tr-->
				</tbody>
			</table>
		</div><?
	}

	if ($view_type == 'read'){
	}else{ ?>
		<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
			<table class="my_table my_border_blue">
				<colgroup>
					<col width="90px">
					<col width="110px">
				</colgroup>
				<thead>
					<tr>
						<th class="head bold" colspan="2">퇴직금</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>중간정산여부</th>
						<td>
							<input name="ma_yn" type="radio" class="radio" value="Y" onclick="__setEnabled(document.f.ma_dt, true);" <? if($mst[$basic_kind]["m02_ma_yn"] == 'Y'){echo'checked';} ?>>예
							<input name="ma_yn" type="radio" class="radio" value="N" onclick="__setEnabled(document.f.ma_dt, false);" <? if($mst[$basic_kind]["m02_ma_yn"] == 'N'){echo'checked';} ?>>아니오
						</td>
					</tr>
					<tr>
						<th>중간정산일자</th>
						<td><input name="ma_dt" type="text" value="<?=$myF->dateStyle($mst[$basic_kind]["m02_ma_dt"]);?>" maxlength="8" class="date" tag="중간정산일자를 입력하여 주십시오."></td>
					</tr>
				</tbody>
			</table>
		</div><?
	}

	if ($view_type == 'read'){
	}else{?>
		<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
			<table class="my_table my_border_blue">
				<colgroup>
					<col width="100px">
				</colgroup>
				<thead>
					<tr>
						<th class="head bold" colspan="2">스마트폰업무</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<input name="smart_gbn_m" type="checkbox" class="checkbox" value="Y" <? if($smart_gbn['M'] == 'Y'){?>checked<?} ?>>관리자
						</td>
					</tr>
					<tr>
						<td>
							<input name="smart_gbn_y" type="checkbox" class="checkbox" value="Y" <? if($smart_gbn['Y'] == 'Y'){?>checked<?} ?>>요양보호사
						</td>
					</tr>
					<tr>
						<td>
							<input name="smart_gbn_w" type="checkbox" class="checkbox" value="Y" <? if($smart_gbn['W'] == 'Y'){?>checked<?} ?>>사회복지사
						</td>
					</tr>
				</tbody>
			</table>
		</div><?
	}

	if ($view_type == 'read'){
	}else{
		echo '<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
				<table class="my_table my_border_blue">
					<colgroup>';

		if ($IsCare){
			echo '		<col width="80px">
						<col width="60px">
						<col width="80px">
						<col width="80px">
						<col width="160px">';
		}else{
			echo '		<col width="80px">
						<col width="160px">
						<col width="80px">
						<col width="160px">';
		}

		echo '		</colgroup>
					<thead>
						<tr>
							<th class="head bold" colspan="5">고용정보</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>고용형태</th>
							<td colspan="4">
								<input id="idEmployKind_1" name="employ_kind" type="radio" class="radio" value="1" '.($memHis['employ_type']/*$mst[$basic_kind]["m02_ygoyong_kind"]*/ == "1" ? 'checked' : '').'><span style="margin-left:-5px;" title="월급제근로자"><label for="idEmployKind_1">정규직</label></span>
								<input id="idEmployKind_2" name="employ_kind" type="radio" class="radio" value="2" '.($memHis['employ_type']/*$mst[$basic_kind]["m02_ygoyong_kind"]*/ == "2" ? 'checked' : '').'><span style="margin-left:-5px;" title="계약직근로자"><label for="idEmployKind_2">계약직</label></span>
								<input id="idEmployKind_3" name="employ_kind" type="radio" class="radio" value="3" '.($memHis['employ_type']/*$mst[$basic_kind]["m02_ygoyong_kind"]*/ == "3" ? 'checked' : '').'><span style="margin-left:-5px;" title="월60시간이상근로자"><label for="idEmployKind_3">단시간(60시간이상)</label></span>
								<input id="idEmployKind_4" name="employ_kind" type="radio" class="radio" value="4" '.($memHis['employ_type']/*$mst[$basic_kind]["m02_ygoyong_kind"]*/ == "4" ? 'checked' : '').'><span style="margin-left:-5px;" title="월60시간미만근로자"><label for="idEmployKind_4">단시간(60시간미만)</label></span>
							</td>
						</tr>
						<tr>
							<th>고용상태</th>
							<td colspan="2">
								<input id="employStat1" name="employ_stat" type="radio" class="radio" value="1" onclick="set_out_date(this.value);" '.($memHis['employ_stat']/*$mst[$basic_kind]["m02_ygoyong_stat"]*/ == "1" ? 'checked' : '').'><label for="employStat1" style="margin-left:-5px;">재직</label>
								<input id="employStat2" name="employ_stat" type="radio" class="radio" value="2" onclick="set_out_date(this.value);" '.($memHis['employ_stat']/*$mst[$basic_kind]["m02_ygoyong_stat"]*/ == "2" ? 'checked' : '').'><label for="employStat2" style="margin-left:-5px;">휴직</label>
								<input id="employStat9" name="employ_stat" type="radio" class="radio" value="9" onclick="set_out_date(this.value);" '.($memHis['employ_stat']/*$mst[$basic_kind]["m02_ygoyong_stat"]*/ == "9" ? 'checked' : '').'><label for="employStat9" style="margin-left:-5px;">퇴사</label>
								<input id=\'objEmployStat\' name=\'objEmployStat\' type=\'hidden\' value=\''.$memHis['employ_stat']/*$mst[$basic_kind]["m02_ygoyong_stat"]*/.'\'>
							</td>
							<th>퇴사일자</th>
							<td><input id="quitDt" name="out_dt" type="text" value="'.$myF->dateStyle($memHis['quit_dt']/*$mst[$basic_kind]["m02_ytoisail"]*/).'" class="date" tag="'.$myF->dateStyle($memHis['quit_dt']/*$mst[$basic_kind]["m02_ytoisail"]*/).'" '.(!$lbEditQuitDt ? 'readonly' : '').'></td>
						</tr>
						<tr>
							<th>주휴희망요일</th>
							<td colspan="4">
								<input id="weekly1" name="week_holiday" type="radio" value="1" class="radio" onKeyDown="__enterFocus();" '.($memHis['weekly']/*$mst[$basic_kind]["m02_weekly_holiday"]*/ == "1" ? 'checked' : '').'><label for="weekly1" style="font-weight:bold;">월</label>
								<input id="weekly2" name="week_holiday" type="radio" value="2" class="radio" onKeyDown="__enterFocus();" '.($memHis['weekly']/*$mst[$basic_kind]["m02_weekly_holiday"]*/ == "2" ? 'checked' : '').'><label for="weekly2" style="font-weight:bold;">화</label>
								<input id="weekly3" name="week_holiday" type="radio" value="3" class="radio" onKeyDown="__enterFocus();" '.($memHis['weekly']/*$mst[$basic_kind]["m02_weekly_holiday"]*/ == "3" ? 'checked' : '').'><label for="weekly3" style="font-weight:bold;">수</label>
								<input id="weekly4" name="week_holiday" type="radio" value="4" class="radio" onKeyDown="__enterFocus();" '.($memHis['weekly']/*$mst[$basic_kind]["m02_weekly_holiday"]*/ == "4" ? 'checked' : '').'><label for="weekly4" style="font-weight:bold;">목</label>
								<input id="weekly5" name="week_holiday" type="radio" value="5" class="radio" onKeyDown="__enterFocus();" '.($memHis['weekly']/*$mst[$basic_kind]["m02_weekly_holiday"]*/ == "5" ? 'checked' : '').'><label for="weekly5" style="font-weight:bold;">금</label>
								<input id="weekly6" name="week_holiday" type="radio" value="6" class="radio" onKeyDown="__enterFocus();" '.($memHis['weekly']/*$mst[$basic_kind]["m02_weekly_holiday"]*/ == "6" ? 'checked' : '').'><label for="weekly6" style="font-weight:bold; color:#0000ff;">토</label>
								<input id="weekly0" name="week_holiday" type="radio" value="0" class="radio" onKeyDown="__enterFocus();" '.($memHis['weekly']/*$mst[$basic_kind]["m02_weekly_holiday"]*/ == "0" ? 'checked' : '').'><label for="weekly0" style="font-weight:bold; color:#ff0000;">일</label>
							</td>
						</tr>';


		if ($IsCare){
			echo '		<tr>
							<th>기준근로시간</th>
							<td><div id="strFixedHours" class="right">'.$fixedWorks['hours'].'</div></td>
							<th>기준시급</th>
							<td><div id="strFixedHourly" class="right">'.number_format($fixedWorks['hourly']).'</div></td>
							<td class="right"><span class="btn_pack m"><button type="button" onclick="_memFixedWorksChange(\''.$code.'\',\''.$ed->en($jumin).'\');">변경</button></span></td>

							<input id="fixedHours" name="fixedHours" type="hidden" value="'.$fixedWorks['hours'].'">
							<input id="fixedHourly" name="fixedHourly" type="hidden" value="'.number_format($fixedWorks['hourly']).'">
							<input id="fixedFromDt" name="fixedFromDt" type="hidden" value="'.$myF->_styleYYMM($fixedWorks['from_dt']).'">
							<input id="fixedToDt" name="fixedToDt" type="hidden" value="'.$myF->_styleYYMM($fixedWorks['to_dt']).'">
						</tr>';
		}


		echo '			<tr>
							<th>주요업무</th>
							<td colspan="4"><input id="memWork" name="memWork" type="text" style="width:100%;" value="'.$memHis['mem_work'].'"></td>
						</tr>
					</tbody>
				</table>
			  </div>';
	}


	if ($view_type == 'read'){
	}else{?>
		<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
			<table class="my_table my_border_blue">
				<colgroup>
					<col width="80px">
					<col width="110px">
					<col width="50px">
					<col width="70px">
				</colgroup>
				<thead>
					<tr>
						<th class="head bold" colspan="4">급여지급은행정보</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>급여지급은행</th>
						<td><?
							if ($isBankTrans){?>
								<select id="bankCD" name="bank_cd" style="width:auto;">
								<option value="">--</option><?
								$sql = 'SELECT code
										,      name
										  FROM bank
										 ORDER BY name';

								$conn->query($sql);
								$conn->fetch();

								$rowCount = $conn->row_count();

								for($i=0; $i<$rowCount; $i++){
									$row = $conn->select_row($i);?>
									<option value="<?=$row['code'];?>" <? if ($memHis['bank_nm'] == $row['code']){?>selected<?} ?>><?=$row['name'];?></option><?
								}

								$conn->row_free();?>
								</select><?
							}else{?>
								<input id="bankCD" name="bank_cd" type="text" value="<?=$memHis['bank_nm']/*$mst[$basic_kind]['m02_ybank_name']*/;?>" style="width:100%;"><?
							}?>
						</td>
						<th>예금주</th>
						<td><input id="bankAcct" name="acct_holder" type="text" value="<?=$memHis['bank_acct']/*$mst[$basic_kind]['m02_ybank_holder']*/;?>" style="width:100%;" maxlength="30"></td>
					</tr>
					<tr>
						<th>계좌번호</th>
						<td colspan="3"><input id="bankNo" name="acct_no" type="text" value="<?=$memHis['bank_no']/*$mst[$basic_kind]["m02_ygyeoja_no"] __onlyNumber(this, '189 109'); */;?>" class="" style="width:100%;" onKeyDown=""></td>
					</tr>
				</tbody>
			</table>
		</div><?

		if ($IsCare){?>
			<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
				<table class="my_table my_border_blue">
					<colgroup>
						<col width="140px">
						<col width="177px">
					</colgroup>
					<tbody>
						<tr>
							<th>가족케어 요양보호사</th>
							<td>
								<input id="yFamilyCare" name="ynFamilyCare" type="radio" value="Y" class="checkbox" onclick="lfSetSalaryBtn();" <? if($memOption['family_yn'] == 'Y'){?>checked<?} ?>><label for="yFamilyCare">예</label>
								<input id="nFamilyCare" name="ynFamilyCare" type="radio" value="N" class="checkbox" onclick="lfSetSalaryBtn();" <? if($memOption['family_yn'] != 'Y'){?>checked<?} ?>><label for="nFamilyCare">아니오</label>
							</td>
						</tr>
						<tr>
							<th>치매인지수료 여부</th>
							<td>
								<input id="yDementia" name="ynDementia" type="radio" value="Y" class="checkbox" onclick="lfSetSalaryBtn();" <? if($memOption['dementia_yn'] == 'Y'){?>checked<?} ?>><label for="yDementia">예</label>
								<input id="nDementia" name="ynDementia" type="radio" value="N" class="checkbox" onclick="lfSetSalaryBtn();" <? if($memOption['dementia_yn'] != 'Y'){?>checked<?} ?>><label for="nDementia">아니오</label>
							</td>
						</tr>
					</tbody>
				</table>
			</div><?
		}

		//주야간보호
		if ($gDayAndNight){?>
			<div style="width:auto; margin-left:10px; margin-top:7px; float:left;">
				<table class="my_table my_border_blue">
					<colgroup>
						<col width="140px">
						<col width="177px">
					</colgroup>
					<tbody>
						<tr>
							<th>주야간 요양보호사</th>
							<td>
								<input id="optDayNightY" name="optDayNight" type="radio" value="Y" class="checkbox" <? if($memOption['day_night_yn'] == 'Y'){?>checked<?} ?>><label for="optDayNightY">예</label>
								<input id="optDayNightN" name="optDayNight" type="radio" value="N" class="checkbox" <? if($memOption['day_night_yn'] != 'Y'){?>checked<?} ?>><label for="optDayNightN">아니오</label>
							</td>
						</tr>
					</tbody>
				</table>
			</div><?
		}
	}




	if ($view_type == 'read'){
	}else{
		echo '</div>';
		echo '<div>';
	}

	if ($view_type == 'read'){ ?>
		<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
			<table class="my_table my_border_blue">
				<colgroup>
					<col width="80px">
					<col width="95px">
					<col width="50px">
				</colgroup>
				<thead>
					<tr>
						<th class="head bold" colspan="4">재가요양특별수당</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th class="head">야간특별수당</th>
						<td colspan="2" class="right"><?=number_format($memHis['prolong_rate']/*$mst[$basic_kind]["m02_add_payrate"]*/);?>%</td>
					</tr>
					<tr>
						<th class="head">직급수당</th>
						<td colspan="2" class="right"><?=number_format($mst[$basic_kind]["m02_rank_pay"]);?></td>
					</tr>
					<tr>
						<th rowspan="2" class="head">휴일특별수당</th>
						<td class="left">
						<?
							if ($memHis['holiday_rate_gbn']/*$mst[$basic_kind]['m02_holiday_payrate_yn']*/ == 'Y'){
								echo '일요일, 공휴일';
							}else {
								if ($memHis['holiday_rate_gbn']/*$mst[$basic_kind]['m02_holiday_payrate_yn']*/ == 'S'){
									echo '일요일';
								}else if ($memHis['holiday_rate_gbn']/*$mst[$basic_kind]['m02_holiday_payrate_yn']*/ == 'H'){
									echo '공휴일';
								}else {
									echo '';
								}
							}
						?>
						</td>
					</tr>
					<tr>
						<td class="right"><?=number_format($memHis['holiday_rate']/*$mst[$basic_kind]["m02_holiday_payrate"]*/);?>%</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
			<table class="my_table my_border_blue">
				<colgroup>
					<col width="75px">
					<col width="70px">
					<col width="91px">
				</colgroup>
				<thead>
					<tr>
						<th class="head bold" colspan="3">급여공통항목</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>4대보험가입</th>
						<td class="left"><?=$memHis['ins_yn']/*$mst[$basic_kind]['m02_y4bohum_umu']*/ != 'Y' ? '미가입' : '가입'?></td>
						<th class="head">신고월급여액</th>
					</tr>
					<tr>
						<th>국민연금</th>
						<td class="left"><?=$memHis['annuity_yn']/*$mst[$basic_kind]['m02_ykmbohum_umu']*/ != 'Y' ? '미가입' : '가입'?></td>
						<td class="right"><?=number_format($memHis['annuity_amt']/*$mst[$basic_kind]["m02_ykuksin_mpay"]*/);?></td>
					</tr>
					<tr>
						<th>건강보험</th>
						<td class="left"><?=$memHis['health_yn']/*$mst[$basic_kind]['m02_ygnbohum_umu']*/ != 'Y' ? '미가입' : '가입'?></td>
						<td class="right"></td>
					</tr>
					<tr>
						<th>고용보험</th>
						<td class="left"><?=$memHis['employ_yn']/*$mst[$basic_kind]['m02_ygobohum_umu']*/ != 'Y' ? '미가입' : '가입'?></td>
						<td class="right"></td>
					</tr>
					<tr>
						<th>산재보험</th>
						<td class="left"><?=$memHis['sanje_yn']/*$mst[$basic_kind]['m02_ysnbohum_umu']*/ != 'Y' ? '미가입' : '가입'?></td>
						<td class="right"></td>
					</tr>
					<tr>
						<th>원천징수</th>
						<td colspan="2" class="left"><?=$memHis['paye_yn']/*$mst[$basic_kind]['m02_paye_yn']*/ != 'Y' ? '아니오' : '예'; ?></td>
					</tr>
				</tbody>
			</table>
		</div><?
	}else{?>
		<div style="width:auto; margin-left:10px; margin-top:10px; float:left;"><?
			//급여공통항목
			include_once('./mem_his_insu_reg_body.php');?>
		</div><?

		//연장특별수당
		$memHis['prolong_rate'] = Number_format($memHis['prolong_rate'],1);
		$memHis['prolong_rate'] = Str_Replace('.0','',$memHis['prolong_rate']);

		//휴일특별수당
		$memHis['holiday_rate'] = Number_format($memHis['holiday_rate'],1);
		$memHis['holiday_rate'] = Str_Replace('.0','',$memHis['holiday_rate']);

		if ($IsCare){?>
			<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
				<table class="my_table my_border_blue">
					<colgroup>
						<col width="80px">
						<col width="60px">
						<col width="60px">
						<col width="60px">
					</colgroup>
					<thead>
						<tr>
							<th class="head bold" colspan="4">재가요양특별수당</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>야간특별수당</th>
							<td>
								<input name="add_payrate" type="text" value="<?=$memHis['prolong_rate'];?>" maxlength="3" class="number" style="width:30px;">%
							</td>
							<th>직급수당</th>
							<td>
								<input name="rank_pay" type="text" value="<?=number_format($mst[$basic_kind]["m02_rank_pay"]);?>" maxlength="10" class="number" style="width:50px;">
							</td>
						</tr>
						<tr>
							<th>휴일특별수당</th>
							<td colspan="3">
								<input name="sunday_payrate_yn" type="checkbox" class="checkbox" value="Y" onclick="set_add_payrate();" <? if($memHis['holiday_rate_gbn'] == 'Y' || $memHis['holiday_rate_gbn'] == 'S'){echo 'checked';} ?>>일요일
								<input name="holiday_payrate_yn" type="checkbox" class="checkbox" value="Y" onclick="set_add_payrate();" <? if($memHis['holiday_rate_gbn'] == 'Y' || $memHis['holiday_rate_gbn'] == 'H'){echo 'checked';} ?>>공휴일
								<input name="holiday_payrate" type="text" value="<?=$memHis['holiday_rate'];?>" maxlength="3" class="number" style="width:30px;">%
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<script type="text/javascript">
				$('input[name="add_payrate"]').unbind('change').bind('change',function(){
					if ($(this).val().length > 2){
						$(this).val(parseInt($(this).val(),10) * 0.1);
					}
				});
				$('input[name="holiday_payrate"]').unbind('change').bind('change',function(){
					if ($(this).val().length > 2){
						$(this).val(parseInt($(this).val(),10) * 0.1);
					}
				});
			</script><?
		}?>

		<script type="text/javascript">
			function lfFindCompareJobs(obj){
				if (!$('#compareJobs').attr('checked')){
					 $('#compareJobStr').val('');
					 $('#lblCompareJob').text('');

					 return;
				}

				var objModal= new Object();
				var url		= './mem_jobs.php';
				var style	= 'dialogWidth:500px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';

				window.showModalDialog(url, objModal, style);

				if (!objModal.reason){
					$('#compareJobStr').val('');
					$('#lblCompareJob').text('');
					$('#compareJobs').attr('checked',false);

					return;
				}

				$('#compareJobStr').val(objModal.reason);
				$('#lblCompareJob').text(objModal.reason);
			}
		</script><?

		if ($gDayAndNight){?>
			<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
				<table class="my_table my_border_blue">
					<colgroup>
						<col width="140px">
						<col width="177px">
					</colgroup>
					<tbody>
						<tr>
							<th>주야간 프로그램관리사</th>
							<td>
								<label><input name="optDanPrgYn" type="radio" value="Y" class="checkbox" <? if($memOption['dan_prg_yn'] == 'Y'){?>checked<?} ?>>예</label>
								<label><input name="optDanPrgYn" type="radio" value="N" class="checkbox" <? if($memOption['dan_prg_yn'] != 'Y'){?>checked<?} ?>>아니오</label>
							</td>
						</tr>
						<tr>
							<th>간호사 여부</th>
							<td>
								<label><input name="optNurseYn" onclick="__setEnabled(document.f.nurseNo, true);" type="radio" class="radio" value="Y" <?=$memHis['nurse_yn'] == 'Y' ? 'checked' : '';?>>예</label>
								<label><input name="optNurseYn" onclick="__setEnabled(document.f.nurseNo, false);" type="radio" class="radio" value="N" <?=$memHis['nurse_yn'] != 'Y' ? 'checked' : '';?>>아니오</label>
							</td>
						</tr>
						<tr>
							<th>간호사 라이센스</th>
							<td><input id="nurseNo" name="nurseNo" type="text" value="<?=$memHis['nurse_no'];?>" ></td>
						</tr>
					</tbody>
				</table>
			</div><?
		}?>

		<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
			<table class="my_table my_border_blue">
				<colgroup>
					<col width="72px">
					<col width="58px">
				</colgroup>
				<thead>
					<tr>
						<th class="head bold" colspan="2">식대/차량</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>식대보조비</th>
						<td><input id="mealPay" name="mealPay" type="text" value="<?=number_format($mst[$basic_kind]['m02_meal_pay']);?>" class="number" style="width:100%;"></td>
					</tr>
					<tr>
						<th>차량유지비</th>
						<td><input id="carPay" name="carPay" type="text" value="<?=number_format($mst[$basic_kind]['m02_car_pay']);?>" class="number" style="width:100%;"></td>
					</tr>
				</tbody>
			</table>
		</div><?

		if ($IsCare){?>
			<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
				<table class="my_table my_border_blue">
					<colgroup>
						<col width="60px">
						<col width="30px">
						<col width="130px">
					</colgroup>
					<thead>
						<tr>
							<th class="head bold" colspan="3">보수비교</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th class="center"><label for="compareYn">대상</label></th>
							<td class="center"><input id="compareYn" name="compareYn" type="checkbox" class="checkbox" value="Y" <?=($memHis['compare_yn'] != 'N' ? 'checked' : '');?>></td>
							<th class="center">겸직내용</th>
						</tr>
						<tr>
							<th class="center"><label for="compareJobs">겸직</label></th>
							<td class="center"><input id="compareJobs" name="compareJobs" type="checkbox" class="checkbox" value="Y" onclick="lfFindCompareJobs(this);" <?=($memHis['compare_jobs'] == 'Y' ? 'checked' : '');?>></td>
							<td class="center">
								<div id="lblCompareJob" style="cursor:pointer;" onclick="lfFindCompareJobs($('#compareJobs'));"><?=$memHis['compare_jobstr'];?></div>
								<input id="compareJobStr" name="compareJobStr" type="hidden" value="<?=$memHis['compare_jobstr'];?>">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
				<table class="my_table my_border_blue">
					<colgroup>
						<col width="110px">
						<col width="143px">
					</colgroup>
					<tbody>
						<tr>
							<th class="center">사회복지사여부</th>
							<td>
								<label><input name="optSwYn" type="radio" class="radio" value="Y" <?=$memHis['sw_yn'] == 'Y' ? 'checked' : '';?>>예</label>
								<label><input name="optSwYn" type="radio" class="radio" value="N" <?=$memHis['sw_yn'] != 'Y' ? 'checked' : '';?>>아니오</label>
							</td>
						</tr>
						<tr>
							<th class="center">근속수당 대상여부</th>
							<td>
								<label><input name="optLsepYn" type="radio" class="radio" value="Y" <?=$memHis['lsep_yn'] == 'Y' ? 'checked' : '';?>>예</label>
								<label><input name="optLsepYn" type="radio" class="radio" value="N" <?=$memHis['lsep_yn'] != 'Y' ? 'checked' : '';?>>아니오</label>
							</td>
						</tr>
						<script type="text/javascript">
							$('input:radio[name="optSwYn"]').unbind('click').bind('click', function(){
								if ($(this).val() == 'Y'){
									$('input:radio[name="optLsepYn"]').attr('disabled', false);
								}else{
									$('input:radio[name="optLsepYn"]').attr('disabled', true);
								}
							});
							$('input:radio[name="optSwYn"]:checked').click();
						</script>
						<!--tr>
							<td class="left" colspan="2">방문간호 일정등록에서 직원 조회시<br>가장위에 출력됩니다.</td>
						</tr-->
					</tbody>
				</table>
			</div><?
		}

		if ($IsCare){
			if ($lbInsuMenuShow){
				$sql = 'SELECT start_dt
						,      end_dt
						,      stat
						  FROM insu
						 WHERE org_no  = \''.$code.'\'
						   AND jumin   = \''.$jumin.'\'
						   AND join_dt = \''.$memHis['join_dt'].'\'
						 ORDER BY seq DESC
						 LIMIT 1';

				$laInsu = $conn->get_array($sql);

				if ($laInsu['stat'] == '1' || $laInsu['stat'] == '3'){
					$memHis['insu_yn'] = 'Y';
				}else{
					$memHis['insu_yn'] = 'N';
				}?>
				<div style="width:auto; margin-left:10px; margin-top:9px; float:left;">
					<table class="my_table my_border_blue">
						<colgroup>
							<col width="30px">
							<col width="60px">
							<col width="45px">
							<col width="70px">
							<col width="30px">
							<col width="177px">
							<col width="70px">
						</colgroup>
						<thead>
							<tr>
								<th class="head bold" colspan="7">배상책임보험</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th rowspan="2">가입</th>
								<td>
									<input id="insY" name="insYN" type="radio" value="Y" class="radio"><label for="insY">신청</label>
								</td>
								<th>보험사</th>
								<td colspan="3">
									<select id="cboInsu" name="cboInsu" style="width:auto;"><?
										$sql = 'SELECT	mst.svc_cd
												,		mst.seq
												,		mst.cd
												,		sub.nm
												,		mst.from_dt
												,		mst.to_dt
												FROM	(
														SELECT	svc_cd
														,		seq
														,		insu_cd AS cd
														,		from_dt
														,		to_dt
														FROM	insu_center
														WHERE	org_no = \''.$code.'\'
														) AS mst
												INNER	JOIN (
														SELECT	g01_code AS cd
														,		g01_name AS nm
														FROM	g01ins
														) AS sub
														ON	sub.cd = mst.cd
												ORDER	BY seq DESC';

										$conn->query($sql);
										$conn->fetch();

										$rowCount = $conn->row_count();

										for($i=0; $i<$rowCount; $i++){
											$row = $conn->select_row($i);

											if ($memHis['start_dt'] >= $row['from_dt'] && $memHis['start_dt'] <= $row['to_dt']){
												$selected = 'selected';
											}else{
												$selected = '';
											}?>
											<option value="<?=$row['cd'];?>" <?=$selected;?> from="<?=$row['from_dt'];?>" to="<?=$row['to_dt'];?>"><?=$row['nm'];?> [<?=$myF->dateStyle($row['from_dt'],'.');?>~<?=$myF->dateStyle($row['to_dt'],'.');?>]</option><?
										}

										$conn->row_free();?>
									</select>
									<div id="lblInsu" class="left" style="display:none;"></div>
								</td>
								<td class="center" rowspan="2">
									<div><span id="btnInsuRecord" class="btn_pack m"><button type="button" onclick="lfInsuList();">이력</button></span></div>
									<div><span id="btnInsuApply" class="btn_pack m"><button type="button" onclick="lfInsuSave();">적용</button></span></div>
								</td>
							</tr>
							<tr>
								<td>
									<input id="insN" name="insYN" type="radio" value="N" class="radio"><label for="insN">해지</label>
								</td>
								<th>상태</th>
								<td><div id="lblStat" class="left" style="display:none;"></div></td>
								<th>기간</th>
								<td><?
									if ($laInsu['stat'] == '9'){
										$startDt = '';
										$endDt   = '';
									}else{
										$startDt = $laInsu['start_dt'];
										$endDt   = $laInsu['end_dt'];
									}?>
									<input id="txtInsuFrom" name="txtInsuFrom" type="text" value="<?=$startDt;?>" class="date" onchange="return lfChkDate(this);" readonly> ~
									<input id="txtInsuTo" name="txtInsuTo" type="text" value="<?=$endDt;?>" class="date" onchange="return lfChkDate(this);" readonly>
									<input id="txtInsuStat" name="txtInsuStat" type="hidden" value="<?=$laInsu['stat'];?>">
									<input id="txtLastDt" name="txtLastDt" type="hidden" value="<?=(!Empty($laInsu['end_dt']) ? $myF->dateAdd('day', 1, $laInsu['end_dt'], 'Y-m-d') : '');?>">
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<script type="text/javascript">
				$(document).ready(function(){
					if ($('#memMode').val() != '1'){
						$('#btnInsuApply').hide();
						$('#btnInsuRecord').hide();
					}

					if ($('#txtInsuStat').val() == '1'){
						$('#cboInsu').hide();
						$('#lblInsu').text($('#cboInsu option:selected').text()).show();
						$('#lblStat').text('가입신청').show();
						$('#insY').attr('disabled',true);
						$('#insN').attr('disabled',true);
						$('#btnInsuApply').attr('disabled',true);
					}else if ($('#txtInsuStat').val() == '3'){
						$('#cboInsu').hide();
						$('#lblInsu').text($('#cboInsu option:selected').text()).show();
						$('#lblStat').text('가입').show();
						$('#insY').attr('disabled',true);
					}else if ($('#txtInsuStat').val() == '7'){
						$('#cboInsu').hide();
						$('#lblInsu').text($('#cboInsu option:selected').text()).show();
						$('#lblStat').text('해지신청').show();
						$('#insY').attr('disabled',true);
						$('#insN').attr('disabled',true);
						$('#btnInsuApply').attr('disabled',true);
					}else if ($('#txtInsuStat').val() == '9'){
						$('#lblStat').text('해지').show();
						$('#insN').attr('disabled',true);
					}else{
						$('#lblStat').text('미가입').show();
						$('#insN').attr('disabled',true);
					}

					$('input:radio[name="insYN"]').unbind('click').bind('click',function(){
						var today = getToday();

						if ($('#txtInsuStat').val() == '1'){
						}else if ($('#txtInsuStat').val() == '3'){
							if ($(this).val() == 'Y'){
								$('#txtInsuTo').attr('readonly',true);
							}else{
								$('#txtInsuTo').attr('readonly',false).val(today);
							}
							__init_object(document.getElementById('txtInsuTo'));
						}else if ($('#txtInsuStat').val() == '7'){
						}else if ($('#txtInsuStat').val() == '9'){
							var regDt = $('#txtLastDt').val();

							if (today > regDt){
								regDt = today;
							}

							if ($(this).val() == 'Y'){
								$('#txtInsuFrom').attr('readonly',false).val(regDt);
								__init_object(document.getElementById('txtInsuFrom'));
							}
						}else{
							$('#cboInsu').show();
							$('#lblInsu').hide();

							var joinDt = $('#joinDt').val();
							var insuDt = $('#cboInsu option:selected').attr('from');

							if (!joinDt){
								alert('입사일자를 먼저 입력하여 주십시오.');
								$('#joinDt').focus();
								return false;
							}

							if (joinDt > insuDt){
								var regDt = joinDt;
							}else{
								var regDt = insuDt;
							}

							$('#txtInsuFrom').attr('readonly',false).val(regDt);
							__init_object(document.getElementById('txtInsuFrom'));
						}
					});

					$('#cboInsu').unbind('change').bind('change',function(){
						$('#txtInsuFrom').val('');
					});


				});

				function lfChkDate(obj){
					var insuFrom = $('#cboInsu option:selected').attr('from').split('-').join('');
					var insuTo   = $('#cboInsu option:selected').attr('to').split('-').join('');
					var joinDt   = $('#joinDt').val().split('-').join('');
					var quitDt   = $('#quitDt').val().split('-').join('');
					var regDt    = $(obj).val().split('-').join('');
					var lastDt   = $('#txtLastDt').val().split('-').join('');

					if (!regDt){
						alert('가입(해지)일자를 입력하여 주십시오.');
						$(obj).focus();
						return false;
					}

					if (regDt < lastDt){
						alert('가입(해지)입력일자는 '+__getDate(lastDt)+'부터 가능합니다');
						$(obj).val(lastDt).focus();
						return false;
					}

					if ($(obj).attr('id') == 'txtInsuFrom'){
						if (!joinDt){
							alert('입사일자를 입력하여 주십시오.');
							$(obj).val('');
							$('#joinDt').focus();
							return false;
						}

						if (regDt < joinDt){
							alert('배상책임보험 가입일자는 입사일부터 가능합니다.');
							$(obj).val('').focus();
							return false;
						}

						if (regDt < insuFrom){
							alert('배상책임보험 가입일자는 기관의 가입일자부터 가능합니다.');
							$(obj).val('').focus();
							return false;
						}
					}else{
						if (quitDt){
							if (regDt > quitDt){
								alert('해지일자는 퇴자일자까지 가능합니다.');
								$(obj).val('').focus();
								return false;
							}
						}
					}

					if (regDt > insuTo){
						alert('배상책임보험 가입(해지)일자는 기관의 종료일자까지 가능합니다.');
						$(obj).val('').focus();
						return false;
					}

					return true;
				}

				function lfInsuSave(){
					if ($('#btnInsuApply').attr('disabled')){
						return;
					}

					if ($('input:radio[name="insYN"]:checked').val() == 'Y'){
						if (!lfChkDate($('#txtInsuFrom'))) return;
					}else{
						if (!lfChkDate($('#txtInsuTo'))) return;
					}

					if (!confirm('배상책임보험 가입신청을 진행하시곘습니까?')) return;

					$.ajax({
						type: 'POST'
					,	url : './mem_insu_save.php'
					,	data: {
							'jumin'		:$('#memJumin').val()
						,	'joinDt'	:$('#joinDt').val()
						,	'quitDt'	:$('#quitDt').val()
						,	'insuCd'	:$('#cboInsu').val()
						,	'insuFrom'	:$('#txtInsuFrom').val()
						,	'insuTo'	:$('#txtInsuTo').val()
						,	'insuYN'	:$('input:radio[name="insYN"]:checked').val()
						,	'memHisSeq'	:$('#memHisSeq').val()
						}
					,	beforeSend: function (){
						}
					,	success: function (result){
							if (result == '1'){
								$('#txtInsuStat').val(result);
								$('#cboInsu').hide();
								$('#lblInsu').text($('#cboInsu option:selected').text()).show();
								$('#lblStat').text('가입신청').show();
								$('#txtInsuFrom').attr('readonly',true);
								$('#insN').attr('disabled',true);
								__init_object(document.getElementById('txtInsuFrom'));
							}else if (result == '7'){
								$('#txtInsuStat').val(result);
								$('#lblStat').text('해지신청').show();
								$('#txtInsuTo').attr('readonly',true);
								$('#insY').attr('disabled',true);
								__init_object(document.getElementById('txtInsuTo'));
							}else{
								alert('RESULT : '+result);
							}
						}
					,	error: function (){
						}
					}).responseXML;
				}

				function lfInsuList(){
					var w = 800; //screen.availWidth;
					var h = 600; //screen.availHeight;
					var l = (screen.availWidth - w) / 2;
					var t = (screen.availHeight - h) / 2;

					var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
					var url    = './mem_insu_record.php';
					var win    = window.open('', 'MEM_INSU_RECORD', option);
						win.opener = self;
						win.focus();

					var parm = new Array();
						parm = {
							jumin : $('#memJumin').val()
						};

					var form = document.createElement('form');
					var objs;
					for(var key in parm){
						objs = document.createElement('input');
						objs.setAttribute('type', 'hidden');
						objs.setAttribute('name', key);
						objs.setAttribute('value', parm[key]);

						form.appendChild(objs);
					}

					form.setAttribute('target', 'MEM_INSU_RECORD');
					form.setAttribute('method', 'post');
					form.setAttribute('action', url);

					document.body.appendChild(form);

					form.submit();
				}
				</script><?
			}else{?>
				<div style="width:auto; margin-left:10px; margin-top:10px; float:left;">
					<table class="my_table my_border_blue">
						<colgroup>
							<col width="30px">
							<col>
						</colgroup>
						<thead>
							<tr>
								<th class="head bold" colspan="2">배상책임보험</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>가입</th>
								<td>
									<input name="ins_yn" type="radio" class="radio" value="Y" onclick="__setEnabled(document.getElementById('ins_from_dt'),true); __setEnabled(document.getElementById('ins_to_dt'),true);" <?=($mst[$basic_kind]['m02_ins_yn'] == 'Y' ? 'checked' : '');?>>유
									<input name="ins_yn" type="radio" class="radio" value="N" onclick="__setEnabled(document.getElementById('ins_from_dt'),false); __setEnabled(document.getElementById('ins_to_dt'),false);" <?=($mst[$basic_kind]['m02_ins_yn'] != 'Y' ? 'checked' : '');?>>무
								</td>
							</tr>
							<tr>
								<th>기간</th>
								<td>
									<input name="ins_from_dt" type="text" value="<? if($mst[$basic_kind]['m02_ins_yn'] == 'Y'){echo $myF->dateStyle($ins_from_dt);} ?>" tag="<?=$myF->dateStyle($ins_from_dt);?>" class="date">~<input name="ins_to_dt" type="text" value="<? if($mst[$basic_kind]['m02_ins_yn'] == 'Y'){echo $myF->dateStyle($ins_to_dt);}   ?>" tag="<?=$myF->dateStyle($ins_to_dt);?>"   class="date">
								</td>
							</tr>
						</tbody>
					</table>
				</div><?
			}
		}

		if ($gHostSvc['baby']){
			//산모신생아를 하는 기관만 출력?>
			<div class="clean" style="margin-left:10px; margin-top:10px; float:left; width:200px;">
				<table class="my_table my_border_blue">
					<colgroup>
						<col width="350px">
					</colgroup>
					<tbody>
						<tr>
							<th class="head bold">산모신생아 지정관리사 여부</th>
						</tr>
						<tr>
							<td>
								<label><input name="optBabyMgYn" type="radio" class="radio" value="Y" <?=$memOption['baby_mg_yn'] == 'Y' ? 'checked' : '';?>>예</label>
								<label><input name="optBabyMgYn" type="radio" class="radio" value="N" <?=$memOption['baby_mg_yn'] == 'N' ? 'checked' : '';?>>아니오</label>
							</td>
						</tr>
						<tr>
							<th class="head bold">산모신생아 지정관리사 지역</th>
						</tr>
						<tr>
							<td><?
								include('./baby_mg_area.php');?>
							</td>
						</tr>
					</tbody>
				</table>
			</div><?
		} 

		?>
		
		<div class="clean" style="margin-left:10px; margin-top:10px; float:left; width:220px;">
				<table class="my_table my_border_blue">
				<colgroup>
					<col width="60px">
					<col width="100px">
					<col >
				</colgroup>
				<thead>
					<tr>
						<th class="head bold" colspan="3">직,간접인건비 구분</th>						
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>구분</th>
						<td class="left "><div id="strDirectGbn"><?=$direct['gbn'] != '' ? $direct['gbn']=='2'? '간접인건비':'직접인건비' : '';?></div></td>
						<td class="center" rowspan="2"><span class="btn_pack m"><button type="button" onclick="_memDirectChange('<?=$code;?>','<?=$ed->en($jumin);?>');">변경</button></span></td>
					</tr>
					<tr>
						<th>기준년월</th>
						<td >
							<span id="strFromDt" class="left"><?=$myF->_styleYYMM($direct['yymm']);?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
			<div class="clean" style="margin-left:10px; margin-top:10px; float:left; width:370px;">
					<table class="my_table my_border_blue">
					<colgroup>
						<col width="250px">
						<col width="120px">
					</colgroup>
					<thead>
						<tr>
							<th class="head bold" >보험(국민,고용) 신규가입 여부</th>
							<td>
								<label><input name="optInsuNewYn" type="radio" class="radio" value="Y" <?=$memOption['insu_new_yn'] == 'Y' ? 'checked' : '';?>>예</label>
								<label><input name="optInsuNewYn" type="radio" class="radio" value="N" <?=$memOption['insu_new_yn'] == 'N' ? 'checked' : '';?>>아니오</label>
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="2">&nbsp;※ 10인미만 사업장 두루누리 사회보험 신규가입 대상 여부입니다.</td>
						</tr>
					</tbody>
				</table>
			</div>
		<?
		

		if ($debug){
			//배상책임보험 가입여부
			if ($memHis['insu_yn'] == 'Y'){
				if ($laInsu['stat'] == '3'){?>
					<!--div style="width:auto; margin-left:-255px; margin-top:8px; float:left;">
						<table class="my_table my_border_blue">
							<colgroup>
								<col width="70px">
								<col width="165px">
								<col width="60px">
							</colgroup>
							<thead>
								<tr>
									<th class="head bold" colspan="3">배상책임보험 대체인력</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th class="left">요양보호사</th>
									<td style="padding:1px 0 1px 5px;">
										<span class="btn_pack find" onclick="__find_member('<?=$code?>','',['subMemCd','subMemNm','','','','','','']);"></span>
										<span id="subMemNm" style="height:100%; margin-left:5px; font-weight:bold;"></span>
										<input id="subMemCd" name="subMemCd" type="hidden" value="">
									</td>
									<td class="center" rowspan="2">
										<span class="btn_pack m"><button>적용</button></span>
									</td>
								</tr>
								<tr>
									<th class="left">대체기간</th>
									<td>
										<input id="subFromDt" name="subFromDt" type="text" class="date">~<input id="subToDt" name="subToDt" type="text" class="date">
									</td>
								</tr>
							</tbody>
						</table>
					</div--><?
				}
			}
		}
		
		if ($gDomain == 'dolvoin.net'){?>
			<div style="float:left; width:205px; margin-left:10px; margin-top:9px;">
				<table class="my_table my_border_blue">
					<colgroup>
						<col width="55px">
						<col width="150px">
					</colgroup>
					<thead>
						<tr>
							<th class="head bold" colspan="2">퇴직연금 가입정보</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>가입여부</th>
							<td>
								<label><input name="retire_join_flag" type="radio" class="radio" value="Y" <?=$memHis['retire_join_flag'] == 'Y' ? 'checked' : '';?>>가입</label>
								<label><input name="retire_join_flag" type="radio" class="radio" value="N" <?=$memHis['retire_join_flag'] != 'Y' ? 'checked' : '';?>>미가입</label>
							</td>
						</tr>
						<tr>
							<th>가입일자</th>
							<td>
								<input name="retire_join_dt" type="text" value="<?=$myF->dateStyle($memHis['retire_join_dt']);?>" class="date">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<script type="text/javascript">
				$(':radio[name="retire_join_flag"]').unbind('click').bind('click', function(){
					if ($(this).val() == 'Y'){
						$(':text[name="retire_join_dt"]').attr('disabled', false);
					}else{
						$(':text[name="retire_join_dt"]').attr('disabled', true);
					}
				});
				$(':radio[name="retire_join_flag"]:checked').click();
			</script><?
		}
	}

	if ($view_type == 'read'){
	}else{
		echo '</div>';
	}?>
	<script type="text/javascript">
		$(document).ready(function(){
			lfSetSalaryBtn();
		});

		function lfSetSalaryBtn(){
			return;
			var lsFamilyYn = $('input:radio[name="ynFamilyCare"]:checked').val();
			var lsDementiaYn = $('input:radio[name="ynDementia"]:checked').val();

			if (lsFamilyYn == 'Y'){
				//salaryKind_'.$svcID.'
				$('div[id^="salaryKind_11_"]').each(function(){
					if ($(this).text().substring(0,1) == '√'){
					}
				});
				$('img[id^="btnSalarySet_"][id!="btnSalarySet_12"]').hide();
			}else if (lsDementiaYn == 'Y'){
				
			}else{
				$('img[id^="btnSalarySet_"]').show();
			}
		}
	</script>