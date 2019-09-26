<?
	/*********************************************************

		변수설정

		- 2012.03.21
		- 급여조정 화면에서 시급내역 팝업 시 급여 년월에 따른 시급 정보를
		  출력하기 위해 변수를 설정한다.

	*********************************************************/
	if (empty($salarySeq))  $salarySeq = 'auto';   //순번 설정
	if (empty($salaryDate)) $salaryDate = 'now()'; //기준년월 설정


	/*********************************************************

		월급내역

	*********************************************************/
	//$salaryMonIf = $conn->get_array($mySalary->_queryNowSalary($code, $jumin));
	//- 2012.03.21 설정된 변수를 적용시킴.
	$salaryMonIf = $conn->get_array($mySalary->_queryNowSalary($code, $jumin, $salaryDate));



	/*********************************************************

		시급내역

	*********************************************************/
	//$conn->query($mySalary->_queryNowHourly($code, $jumin));
	//- 2012.03.21 설정된 변수를 적용시킴.
	$conn->query($mySalary->_queryNowHourly($code, $jumin, '', $salarySeq, $salaryDate));
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$salaryHourIf[$row['kind']][$row['svc_id']] = $mySalary->_setHourlyData($row);
	}

	$conn->row_free();


	/*********************************************************

		급여 지급 방식

	*********************************************************/
	if (is_array($salaryMonIf)){
		$salaryPayType = '1';
	}else{
		$salaryPayType = '2';
	}


	$html = '';

	/*********************************************************

		월급제

	*********************************************************/
		$html .= '<div id="divSalaryMon" style="clear:both;">
				  <div style="clear:both;'.($salaryYN != 'Y' ? 'margin-left:10px; margin-top:10px;' : '').'">
					<table class="my_table my_border_blue" style="width:100%;">
						<colgroup>
							<col>
						</colgroup>
						<thead>
							<th class="head bold">';
		if ($salaryYN == 'Y'){
			$html .= '<div style=\'float:right; width:auto; margin-right:5px;\'><img src=\'../image/close.gif\' style=\'cursor:pointer;\' onclick=\'__popupHide();\'></div>';
		}

		$html .= '				<div style=\'float:center; width:auto;\'>월급제</div>
							</th>
						</thead>
					</table>
				 </div>';

		$html .= '<div style="clear:both;'.($salaryYN != 'Y' ? 'margin-left:10px;' : '').'">
					<table class="my_table my_border_blue" style="width:100%; border-top:none;">
						<colgroup>
							<col width="40px">
							<col width="50px">
							<col width="50px">
							<col width="100px">
							<col width="110px">
							<col width="110px">
							<col width="135px">
							<col width="135px">
							<col>
						</colgroup>
						<tbody>
							<tr>
								<th class="center">구분</th>
								<td class="center"><div id="salaryApply" class="center bold">'.(is_array($salaryMonIf) ? '기본급' : '무').'</div></td>
								<th class="center">기본급</th>
								<td class="center"><div id="salaryMonAmt" class="right bold">'.number_format($salaryMonIf['pay']).'</div></td>
								<th class="center">케어금액포함여부</th>
								<td class="center"><div id="salaryMonCareYN" class="left bold">'.($salaryMonIf['care_yn'] == 'Y' ? '예' : '아니오').'</div></td>
								<th class="center">목욕,간호수당포함여부</th>
								<td class="center"><div id="salaryMonExtraYN" class="left bold">'.($salaryMonIf['extra_yn'] == 'Y' ? '예' : '아니오').'</div></td>
								<td class="center">
									<div id="salaryMonDay20YN" style="display:none;">'.($salaryMonIf['day20_yn'] == 'Y' ? '예' : '아니오').'</div>
									<div id="salaryMonDealpay" style="display:none;">'.number_format($salaryMonIf['dealpay']).'</div>
									<div class="right"><span class="btn_pack m"><button type="button" onclick="_memSalaryMonSet();">변경</button></span></div>
								</td>
							</tr>
						</tbody>
					</table>

					<input id="salaryMonFrom" name="salaryMonFrom" type="hidden" value="'.$myF->_styleYYMM($salaryMonIf['from_dt']).'">
					<input id="salaryMonTo" name="salaryMonTo" type="hidden" value="'.$myF->_styleYYMM($salaryMonIf['to_dt']).'">';

		$html .= '</div>
				  </div>';


	/*********************************************************

		서비스별 급여제

	*********************************************************/
		$html .= '<div id="divSalarySvc" style="clear:both;">
				  <div style="clear:both;'.($salaryYN != 'Y' ? 'margin-left:10px; margin-top:10px;' : 'margin-top:-2px;').'">
					<table class="my_table my_border_blue" style="width:100%;">
						<colgroup>
							<col>
						</colgroup>
						<thead>
							<tr>
								<th class="head bold">서비스별 급여제</th>
							</tr>
						</thead>
					</table>
				  </div>';



		if ($use_menu[0]){
			$html .= '<div style="clear:both;'.($salaryYN != 'Y' ? 'margin-left:10px;' : '').'">
						<table class="my_table my_border_blue" style="width:100%; border-top:none;">
							<colgroup>
								<col width="30px">
								<col>
							</colgroup>
							<tbody>
								<tr>
									<th class="center bold" style="border-right:2px solid #0e69b0;">장<br>기<br>요<br>양</th>
									<td class="top last">
										<div id="divHourly_11" style="float:left; width:50%; border-right:2px solid #0e69b0; margin-left:-1px;">'.$mySalary->_getSalarySvc(11, $salaryHourIf[0]['11'],true).'</div>
										<div id="divHourly_12" style="float:left; width:auto;">'.$mySalary->_getSalarySvc(12, $salaryHourIf[0]['12'],true).'</div>
									</td>
								</tr>
							</tbody>
						</table>
					  </div>';
		}


		if ($use_menu[1] || $use_menu[2] || $use_menu[3] || $use_menu[4]){
			$html .= '<div style="clear:both;'.($salaryYN != 'Y' ? 'margin-left:10px;' : '').'">
						<table class="my_table my_border_blue" style="width:100%; border-top:none;">
							<colgroup>
								<col width="30px">
								<col>
							</colgroup>
							<tbody>
								<tr>
									<th class="center bold" style="border-right:2px solid #0e69b0;" rowspan="2">바<br>우<br>처</th>
									<td class="center top last">';

									$voucherCnt = 0;

									if ($use_menu[1]) $voucherCnt ++;
									if ($use_menu[2]) $voucherCnt ++;
									if ($use_menu[3]) $voucherCnt ++;
									if ($use_menu[4]) $voucherCnt ++;

									$voucherIndex = 0;
									$voucherStyle[0] = ($voucherCnt > 2 ? 'border-bottom:2px solid #0e69b0;' : '').'border-right:2px solid #0e69b0; margin-left:-1px;';
									$voucherStyle[1] = ($voucherCnt > 2 ? 'border-bottom:2px solid #0e69b0;' : '');
									$voucherStyle[2] = 'border-right:2px solid #0e69b0; margin-left:-1px;';
									$voucherStyle[3] = '';

									if ($use_menu[1]){
										$html .= '<div id="divHourly_21"  style="float:left; width:50%;'.$voucherStyle[$voucherIndex].'">'.$mySalary->_getSalarySvc(21, $salaryHourIf[1]['21'],true,'').'</div>';
										$voucherIndex ++;
									}

									if ($use_menu[2]){
										$html .= '<div id="divHourly_22"  style="float:left; width:50%;'.$voucherStyle[$voucherIndex].'">'.$mySalary->_getSalarySvc(22, $salaryHourIf[2]['22'],true,'').'</div>';
										$voucherIndex ++;
									}

									if ($use_menu[3]){
										$html .= '<div id="divHourly_23"  style="float:left; width:50%;'.$voucherStyle[$voucherIndex].'">'.$mySalary->_getSalarySvc(23, $salaryHourIf[3]['23'],true,'').'</div>';
										$voucherIndex ++;
									}

									if ($use_menu[4]){
										$para['babyShow'] = $use_menu[3];
										$html .= '<div id="divHourly_24"  style="float:left; width:50%;'.$voucherStyle[$voucherIndex].'">'.$mySalary->_getSalarySvc(24, $salaryHourIf[4]['24'],true,'',$para).'</div>';
										$voucherIndex ++;
									}

			$html .= '				</td>
								</tr>
							</tbody>
						</table>
						</div>';
		}


		/*********************************************************

			직원별 선택사항
			- 재수당포함여부

			*****************************************************/
			#$sql = 'select mo_extrapay_yn as extrapay_yn
			#		,      mo_salary_yn as salary_yn
			#		  from mem_option
			#		 where org_no   = \''.$code.'\'
			#		   and mo_jumin = \''.$jumin.'\'';
			#
			#$memOption = $conn->get_array($sql);

			$ls_extraPayYN	= $memOption['extrapay_yn'];
			$ls_salaryCalYN	= $memOption['salary_yn'];
			$ls_extraTimeYN	= $memOption['extratime_yn'];
			$ls_insuDiffYN	= $memOption['insu_yn'];
			$ls_dealInYN	= $memOption['dealin_yn'];
			$ls_dealLimitYN	= $memOption['deal_limit_yn'];
			$prgCostNotYn	= $memOption['prg_cost_not_yn'];
			$retirementNotYn = $memOption['retirement_not_yn'];
			$jobfundsNotYn = $memOption['jobfunds_not_yn'];
			$weeklyPayYn = $memOption['weekly_pay_yn'];
			$annualPayYn = $memOption['annual_pay_yn'];

			if (empty($ls_salaryCalYN)) $ls_salaryCalYN = 'Y';

			$html .= '<div style="clear:both;'.($salaryYN != 'Y' ? 'margin-left:10px;' : '').'">
						<table class="my_table my_border_blue" style="width:100%; border-top:none;">
							<colgroup>
								<col>
							</colgroup>
							<tbody>
								<tr>
									<td class="center top last">';

			//제수당
			//$html .= '<input id="chkExtraPayYN" name="chkExtraPayYN" type="hidden" value="'.($ls_extraPayYN == 'Y' ? 'Y' : 'N').'">';


			//직원정보 수정에서만 기타 옵션을 보여준다.
			if ($salaryYN != 'Y'){
				//수급자 본인부담금 수당지급
				$html .= '<div style="text-align:left; margin-top:2px;">
							<input id="familyPayYN" name="familyPayYN" type="checkbox" value="Y" class="checkbox" '.($ls_familyPayYN == 'Y' ? 'checked' : '').'><label for="familyPayYN"><span style="font-weight:bold;">수급자 본인부담금 수당지급</label>
						  </div>';

				//급여지급여부
				$html .= '<div style="text-align:left; margin-top:2px;">
							<input id="chkSalaryYN" name="chkSalaryYN" type="checkbox" value="Y" class="checkbox" '.($ls_salaryCalYN == 'Y' ? 'checked' : '').'><label for="chkSalaryYN"><span style="font-weight:bold;">급여지급(급여를 지급하지 않을 경우 체크를 제거하여 주십시오.)</label>
						  </div>';

				//재수당(연장, 야간, 휴일, 휴일연장, 휴일야간) 포함여부
				$html .= '<div style="text-align:left; margin-top:2px;">
							<input id="chkExtraPayYN" name="chkExtraPayYN" type="checkbox" value="Y" class="checkbox" '.($ls_extraPayYN == 'Y' ? 'checked' : '').'><label for="chkExtraPayYN"><span style="font-weight:bold;">제수당(<span style="color:#0000ff;">연장, 야간, 휴일, 휴일연장, 휴일야간</span>)포함</span></label>
						  </div>';

				//배상책임보험료 공제적용여부
				$html .= '<div style="text-align:left; margin-top:2px;">
							<input id="chkInsuDiffYN" name="chkInsuDiffYN" type="checkbox" value="Y" class="checkbox" '.($ls_insuDiffYN== 'Y' ? 'checked' : '').'><label for="chkInsuDiffYN"><span style="font-weight:bold;"><span style="color:#0000ff;">배상책임보험료</span>를 <span style="color:#0000ff;">급여에서 공제등록처리</span>를 할 경우 선택하여 주십시오.</span></label>
							<div style="padding-left:26px;">배상책임보험료는 <span style="color:#0000ff;">기관관리 수정화면</span>에서 <span style="color:#0000ff;">보험사명 오른쪽</span>에 "<span style="color:#0000ff;">변경</span>"버튼을 클릭하여 수정하여주십시오.</div>
						  </div>';

				//목욕, 간호 업수시간을 근무시간에 포함여부
				$html .= '<div style="text-align:left; margin-top:2px;">
							<input id="chkExtraTimeYN" name="chkExtraTimeYN" type="checkbox" value="Y" class="checkbox" '.($ls_extraTimeYN == 'Y' ? 'checked' : '').'><label for="chkExtraTimeYN"><span style="font-weight:bold;">목욕, 간호 업무시간을 근무시간으로 처리.</span></label>
						  </div>';

				//처우개서비 급여포함여부
				//$html	.= '<div style="text-align:left; margin-top:2px;">';
				//$html	.= '<input id="chkDealInYN" name="chkDealInYN" type="checkbox" value="Y" class="checkbox" '.($ls_dealInYN == 'Y' ? 'checked' : '').'><label for="chkDealInYN"><span style="font-weight:bold;">처우개선비를 급여에 포함(업무수당에서 처우개선비를 공제처리)</span></label>';
				//$html	.= '</div>';

				//처우개선비 한도설정
				//$html	.= '<div style="text-align:left; margin-top:2px;">';
				//$html	.= '<input id="chkDealLimitYN" name="chkDealLimitYN" type="checkbox" value="Y" class="checkbox" '.($ls_dealLimitYN == 'Y' ? 'checked' : '').'><label for="chkDealLimitYN"><span style="font-weight:bold;">처우개선비 한도(<span style="color:#ff0000;">100,000원</span>)를 설정함.</span></label>';
				//$html	.= '</div>';

				//치매수당 지급여부
				$html	.= '<div style="text-align:left; margin-top:2px;">';
				$html	.= '<input id="chkPrgCostNotYn" name="chkPrgCostNotYn" type="checkbox" value="Y" class="checkbox" '.($prgCostNotYn == 'Y' ? 'checked' : '').'><label for="chkPrgCostNotYn"><span style="font-weight:bold;">치매수당을 지급하지 않음.</span></label>';
				$html	.= '</div>';

				
				//퇴직적립금 계산여부
				$html	.= '<div style="text-align:left; margin-top:2px;">';
				$html	.= '<input id="chkRetirementNotYn" name="chkRetirementNotYn" type="checkbox" value="Y" class="checkbox" '.($retirementNotYn == 'Y' ? 'checked' : '').'><label for="chkRetirementNotYn"><span style="font-weight:bold;">퇴직적립금 계산하지 않음.</span></label>';
				$html	.= '</div>';
				

				//일자리안정자금 적용여부
				$html	.= '<div style="text-align:left; margin-top:2px;">';
				$html	.= '<input id="chkJobfundsNotYn" name="chkJobfundsNotYn" type="checkbox" value="Y" class="checkbox" '.($jobfundsNotYn == 'Y' ? 'checked' : '').'><label for="chkJobfundsNotYn"><span style="font-weight:bold;">일자리안정자금 건강보험 경감 대상 아님.</span></label>';
				$html	.= '</div>';


				if ($gDomain == 'dolvoin.net'){
					//주휴수당 적용여부(돌보인 가족케어만)
					$html	.= '<div style="text-align:left; margin-top:2px;">';
					$html	.= '<input id="chkWeeklyPayYn" name="chkWeeklyPayYn" type="checkbox" value="Y" class="checkbox" '.($weeklyPayYn == 'Y' ? 'checked' : '').'><label for="chkJobfundsNotYn"><span style="font-weight:bold;">주휴수당 지급(가족케어만 해당됨)</span></label>';
					$html	.= '</div>';

					//연차수당 적용여부(돌보인 가족케어만)
					$html	.= '<div style="text-align:left; margin-top:2px;">';
					$html	.= '<input id="chkAnnaulPayYn" name="chkAnnaulPayYn" type="checkbox" value="Y" class="checkbox" '.($annualPayYn == 'Y' ? 'checked' : '').'><label for="chkJobfundsNotYn"><span style="font-weight:bold;">연차수당 지급(가족케어만 해당됨)</span></label>';
					$html	.= '</div>';
				}
			}

			$html .= '				</td>
								</tr>
							</tbody>
						</table>
					  </div>';
		/********************************************************/

		$html .= '</div>';




	unset($salaryIf);

	echo $myF->_gabSplitHtml($html);
?>