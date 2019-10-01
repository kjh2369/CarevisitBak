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
		<div style="width:auto;  margin-top:10px; float:left;">
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
						<th></th>
						<td></td>
						<th>비밀번호</th>
						<td class="left last"><?=$member['pswd'];?></td>
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
				</tbody>
			</table>
		</div><?
	}else{?>
		<div style="width:550px;  margin-top:10px; float:left;">
			<table class="my_table my_border_blue" width="100%">
				<colgroup>
					<col width="80px">
					<col width="100px">
					<col width="100px">
					<col width="100px">
					<col width="80px">
					<col width="90px">
				</colgroup>
				<thead>
					<tr>
						<th class="head bold" colspan="6">기본정보</th>
					</tr>
				</thead>
				<tbody>
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
						<th>근무시작일</th>
						<td ><input id="workDt" name="workDt" type="text" value="<?=$memHis['work_start_dt'];?>" class="date"></td>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<th>사번</th>
						<td><input name="member_no" type="text" value="<?=$memHis['com_no']; //$mst[$basic_kind]['m02_mem_no'];?>" style="width:100%;" maxlength="15" onchange="return chk_memno(this);" tag="사번을 입력하여 주십시오."></td>
						<th>사용자ID</th>
						<td>
							<span class="left" id="lblMemCd"><?=$member['code'];?></span>
							<input id="memId" name="memId" type="hidden" value="<?=$member['code'];?>">
						</td>
						<th>비밀번호</th>
						<td><span class="left"><?=$member['pswd'];?></span></td>
					</tr>
		
				</tbody>
			</table>
		</div>
		<div style="width:250px; margin-left:10px; margin-top:10px; float:left;">
			<table class="my_table my_border_blue">
				<colgroup>
					<col width="100px">
					<col width="150px">
				</colgroup>
				<thead>
					<tr>
						<th class="head bold" colspan="2">급여지급은행정보</th>
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
								<input id="bankCD" name="bank_cd" type="text" value="<?=$memHis['bank_nm']/*$mst[$basic_kind]['m02_ybank_name']*/;?>" ><?
							}?>
						</td>
					</tr>
					<tr>
						<th>예금주</th>
						<td><input id="bankAcct" name="acct_holder" type="text" value="<?=$memHis['bank_acct']/*$mst[$basic_kind]['m02_ybank_holder']*/;?>" maxlength="30"></td>
					</tr>
					<tr>
						<th>계좌번호</th>
						<td ><input id="bankNo" name="acct_no" type="text" value="<?=$memHis['bank_no']/*$mst[$basic_kind]["m02_ygyeoja_no"] __onlyNumber(this, '189 109'); */;?>" class="" onKeyDown=""></td>
					</tr>
				</tbody>
			</table>
		</div>
		<?
	}
	
	if ($view_type == 'read'){
	}else{?>
		<div style="width:200px; margin-left:10px;  margin-top:10px; float:left;">
			<table class="my_table my_border_blue" style="width:100%;">
				<colgroup>
					<col width="60px">
					<col width="120px">
				</colgroup>
				<thead>
					<tr>
						<th class="head bold" colspan="2">소속</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>부서</th>
						<td>
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
						<td >
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
							<select id="memPos" name="memPos" style="width:100%; margin:0;">
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
		</div>
		

		<div style="width:180px; margin-left:10px;  margin-top:10px; float:left;">
			<table class="my_table my_border_blue" >
				<colgroup>
					<col width="60px">
					<col width="120px">
				</colgroup>
				<thead>
					<tr>
						<th class="head bold" colspan="2">담당</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>직책</th>
						<td>
							<select id="memWorkGbn" name="memWorkGbn" style="width:100%; margin:0;">
								<option value="">--</option>
								<option value="1" <?=$mst[$basic_kind]['m02_work_gbn']=='1'? 'selected' : '';?> >관리자</option>
								<option value="2" <?=$mst[$basic_kind]['m02_work_gbn']=='2'? 'selected' : '';?> >사회복지사</option>
								<option value="3" <?=$mst[$basic_kind]['m02_work_gbn']=='3'? 'selected' : '';?> >생활관리자</option>
								<option value="9" <?=$mst[$basic_kind]['m02_work_gbn']=='9'? 'selected' : '';?> >기타</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>팀장</th>
						<td>
							<span class="btn_pack m"><button onclick="__find_yoyangsa('<?=$code?>','S','teamCd','teamNm', 'team');">변경</button></span>
							<span id="teamNm" style="height:100%; margin-left:5px; font-weight:bold;"><?=$mst[$basic_kind]['m02_yname'];?></span>
							<input id="teamCd" name="teamCd" type="hidden" value="<?=$ed->en($mst[$basic_kind]['m02_team_manager']);?>">
						</td>
					</tr>	
				</tbody>
			</table>
		</div>

		<?
		
	}

	
	if ($view_type == 'read'){
	}else{
		echo '<div style="width:460px;  margin-top:10px; float:left;">
				<table class="my_table my_border_blue" >
					<colgroup>';

		
			echo '		<col width="80px">
						<col width="60px">
						<col width="80px">
						<col width="80px">
						<col width="160px">';
		

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
						';



		echo '			<tr>
							<th>주요업무</th>
							<td colspan="4"><input id="memWork" name="memWork" type="text" style="width:100%;" value="'.$memHis['mem_work'].'"></td>
						</tr>
					</tbody>
				</table>
			  </div>';
	}


	

	if ($view_type == 'read'){ ?>
		<div style="width:236px;  margin-top:10px; float:left;">
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
		<?
			//급여공통항목
			include_once('./mem_his_insu_reg_body.php');?>
		

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
		</script>

		<?

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