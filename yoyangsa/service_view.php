<?
	/*********************************************************

		월급내역

	*********************************************************/
	$salaryMonIf = $conn->get_array($mySalary->_queryNowSalary($code, $jumin));



	/*********************************************************

		시급내역

	*********************************************************/
	$conn->query($mySalary->_queryNowHourly($code, $jumin));
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
		  <div style="clear:both; margin-left:10px; margin-right:10px; margin-top:10px; ">
			<table class="my_table my_border_blue" style="width:100%;">
				<colgroup>
					<col>
				</colgroup>
				<thead>
					<th class="head bold">월급제</th>
				</thead>
			</table>
		 </div>';

		$html .= '<div style="clear:both; margin-left:10px; margin-right:10px;">
					<table class="my_table my_border_blue" style="width:100%; border-top:none;">
						<colgroup>
							<col width="40px">
							<col width="50px">
							<col width="50px">
							<col width="100px">
							<col width="110px">
							<col width="110px">
							<col width="135px">
							<col width="100px">
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
							</tr>
						</tbody>
					</table>';

		$html .= '</div>
				  </div>';


	/*********************************************************

		서비스별 급여제

	*********************************************************/
		$html .= '<div id="divSalarySvc" style="clear:both;">
				  <div style="clear:both; margin-left:10px; margin-right:10px; margin-top:10px;">
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
			$html .= '<div style="clear:both; margin-left:10px; margin-right:10px;">
						<table class="my_table my_border_blue" style="width:100%; border-top:none;">
							<colgroup>
								<col width="30px">
								<col>
							</colgroup>
							<tbody>
								<tr>
									<th class="center bold" style="border-right:2px solid #0e69b0;">장<br>기<br>요<br>양</th>
									<td class="top last">
										<div id="divHourly_11" style="float:left; width:50%; border-right:2px solid #0e69b0; margin-left:-1px;">'._getSalarySvc2(11, $salaryHourIf[0]['11']).'</div>
										<div id="divHourly_12" style="float:left; width:auto;">'._getSalarySvc2(12, $salaryHourIf[0]['12']).'</div>
									</td>
								</tr>
							</tbody>
						</table>
					  </div>';
		}

		
		if ($use_menu[1] || $use_menu[2] || $use_menu[3] || $use_menu[4]){
			$html .= '<div style="clear:both; margin-left:10px; margin-right:10px;">
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
										$html .= '<div id="divHourly_21"  style="float:left; width:50%;'.$voucherStyle[$voucherIndex].'">'._getSalarySvc2(21, $salaryHourIf[1]['21']).'</div>';
										$voucherIndex ++;
									}

									if ($use_menu[2]){
										$html .= '<div id="divHourly_22"  style="float:left; width:50%;'.$voucherStyle[$voucherIndex].'">'._getSalarySvc2(22, $salaryHourIf[2]['22']).'</div>';
										$voucherIndex ++;
									}

									if ($use_menu[3]){
										$html .= '<div id="divHourly_23"  style="float:left; width:50%;'.$voucherStyle[$voucherIndex].'">'._getSalarySvc2(23, $salaryHourIf[3]['23']).'</div>';
										$voucherIndex ++;
									}

									if ($use_menu[4]){
										$html .= '<div id="divHourly_24"  style="float:left; width:50%;'.$voucherStyle[$voucherIndex].'">'._getSalarySvc2(24, $salaryHourIf[4]['24']).'</div>';
										$voucherIndex ++;
									}

			$html .= '				</td>
								</tr>
							</tbody>
						</table>
						</div>';
		}

		$html .= '</div>';

	


	unset($salaryIf);

	echo $myF->_gabSplitHtml($html);


	function _getSalarySvc2($svcID, $salaryIf, $printText = true){
			switch($svcID){
				case 11: $strTitle = '재가요양'; break;
				case 12: $strTitle = '동거가족'; break;
				case 21: $strTitle = '가사간병'; break;
				case 22: $strTitle = '노인돌봄'; break;
				case 23: $strTitle = '산모신생아'; break;
				case 24: $strTitle = '장애인활동보조'; break;
			}

			if (empty($salaryIf['salaryKind'])) $salaryIf['salaryKind'] = '0';
			if (empty($salaryIf['salaryFromDt'])) $salaryIf['salaryFromDt'] = date('Y-m', mktime());
			if (empty($salaryIf['salaryToDt'])) $salaryIf['salaryToDt'] = '9999-12';

			$html = '<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="80px">
							<col>
							<col width="40px">
						</colgroup>
						<thead>
							<tr>
								<th class="head bold last" colspan="3">'.$strTitle.'</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>급여산정방식</th>
								<td class="center last">
									<div class="left">';

									if ($printText){
										$html .= '<div id="salaryKind_'.$svcID.'_0" class="divText">'.($salaryIf['salaryKind'] == '0' ? '<span style="color:#ff0000;">√</span>무' : '<span style="color:#cccccc; font-weight:normal;">무</span>').' <span style="font-weight:normal;">|</span> </div>
												  <div id="salaryKind_'.$svcID.'_1" class="divText">'.($salaryIf['salaryKind'] == '1' ? '<span style="color:#ff0000;">√</span>시급' : '<span style="color:#cccccc; font-weight:normal;">시급</span>').' <span style="font-weight:normal;">|</span> </div>';

										if ($svcID == 11)
											$html .= '<div id="salaryKind_'.$svcID.'_2" class="divText">'.($salaryIf['salaryKind'] == '2' ? '<span style="color:#ff0000;">√</span>변동시급' : '<span style="color:#cccccc; font-weight:normal;">변동시급</span>').' <span style="font-weight:normal;">|</span> </div>';

										$html .= '<div id="salaryKind_'.$svcID.'_4" class="divText">'.($salaryIf['salaryKind'] == '4' ? '<span style="color:#ff0000;">√</span>총액비율' : '<span style="color:#cccccc; font-weight:normal;">총액비율</span>').' <span style="font-weight:normal;">|</span> </div>
												  <div id="salaryKind_'.$svcID.'_3" class="divText">'.($salaryIf['salaryKind'] == '3' ? '<span style="color:#ff0000;">√</span>고정급' : '<span style="color:#cccccc; font-weight:normal;">고정급</span>').'</div>
												  <div id="salaryKind_'.$svcID.'" style="display:none;">'.$salaryIf['salaryKind'].'</div>';
									}else{
										$html .= '<input id="salaryKind_'.$svcID.'_0" name="salaryKind_'.$svcID.'" type="radio" value="0" class="radio" onclick="_memSalarySetSvcSub(\''.$svcID.'\');" '.($salaryIf['salaryKind'] == '0' ? 'checked' : '').'><label for="salaryKind_'.$svcID.'_0">무</label>
												  <input id="salaryKind_'.$svcID.'_1" name="salaryKind_'.$svcID.'" type="radio" value="1" class="radio" onclick="_memSalarySetSvcSub(\''.$svcID.'\');" '.($salaryIf['salaryKind'] == '1' ? 'checked' : '').'><label for="salaryKind_'.$svcID.'_1">시급</label>';

										if ($svcID == 11)
											$html .= '<input id="salaryKind_'.$svcID.'_2" name="salaryKind_'.$svcID.'" type="radio" value="2" class="radio" onclick="_memSalarySetSvcSub(\''.$svcID.'\');" '.($salaryIf['salaryKind'] == '2' ? 'checked' : '').'><label for="salaryKind_'.$svcID.'_2">변동시급</label>';

										$html .= '<input id="salaryKind_'.$svcID.'_4" name="salaryKind_'.$svcID.'" type="radio" value="4" class="radio" onclick="_memSalarySetSvcSub(\''.$svcID.'\');" '.($salaryIf['salaryKind'] == '4' ? 'checked' : '').'><label for="salaryKind_'.$svcID.'_4">총액비율</label>
												  <input id="salaryKind_'.$svcID.'_3" name="salaryKind_'.$svcID.'" type="radio" value="3" class="radio" onclick="_memSalarySetSvcSub(\''.$svcID.'\');" '.($salaryIf['salaryKind'] == '3' ? 'checked' : '').'><label for="salaryKind_'.$svcID.'_3">고정급</label>';
									}

			$html .= '				</div>
								</td>
								<td class="center last">';

			$html .= '			</td>
							</tr>
							<tr>
								<th>시급</th>
								<td class="center last" colspan="2">
									<div class="left">';

									if ($printText){
										$html .= '<div id="salaryAmt_'.$svcID.'_1" class="divText">'.number_format($salaryIf['salaryAmt_1']).'</div>';
									}else{
										$html .= '<input id="salaryAmt_'.$svcID.'_1" name="salaryAmt_'.$svcID.'_1" type="text" value="'.number_format($salaryIf['salaryAmt_1']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_1" style="width:70px; margin:0;">';
									}

			$html .= '				</div>
								</td>
							</tr>';

							if ($svcID == 11){
								$html .= '<tr>
											<th>변동시급</th>
											<td class="last" colspan="2">
												<table class="my_table" style="width:100%;">
													<colgroup>
														<col width="70px">
														<col>
														<col width="70px">
														<col>
													</colgroup>
													<tbody>
														<tr>
															<th>1등급</th>
															<td class="center">
																<div class="left">';

																if ($printText){
																	$html .= '<div id="salaryAmt_'.$svcID.'_2_1" class="divText">'.number_format($salaryIf['salaryAmt_2']['1']).'</div>';
																}else{
																	$html .= '<input id="salaryAmt_'.$svcID.'_2_1" name="salaryAmt_'.$svcID.'_2[]" type="text" value="'.number_format($salaryIf['salaryAmt_2']['1']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_2" style="width:60px; margin:0;">
																			  <input id="salaryAmtCD_'.$svcID.'_2_1" name="salaryAmtCD_'.$svcID.'_2[]" type="hidden" value="1">';
																}

								$html .= '						</div>
															</td>
															<th>2등급</th>
															<td class="center last">
																<div class="left">';

																if ($printText){
																	$html .= '<div id="salaryAmt_'.$svcID.'_2_2" class="divText">'.number_format($salaryIf['salaryAmt_2']['2']).'</div>';
																}else{
																	$html .= '<input id="salaryAmt_'.$svcID.'_2_2" name="salaryAmt_'.$svcID.'_2[]" type="text" value="'.number_format($salaryIf['salaryAmt_2']['2']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_2" style="width:60px; margin:0;">
																			  <input id="salaryAmtCD_'.$svcID.'_2_2" name="salaryAmtCD_'.$svcID.'_2[]" type="hidden" value="2">';
																}

								$html .= '						</div>
															</td>
														</tr>
														<tr>
															<th style="border-bottom:none;">3등급</th>
															<td class="center" style="border-bottom:none;">
																<div class="left">';

																if ($printText){
																	$html .= '<div id="salaryAmt_'.$svcID.'_2_3" class="divText">'.number_format($salaryIf['salaryAmt_2']['3']).'</div>';
																}else{
																	$html .= '<input id="salaryAmt_'.$svcID.'_2_3" name="salaryAmt_'.$svcID.'_2[]" type="text" value="'.number_format($salaryIf['salaryAmt_2']['3']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_2" style="width:60px; margin:0;">
																			  <input id="salaryAmtCD_'.$svcID.'_2_3" name="salaryAmtCD_'.$svcID.'_2[]" type="hidden" value="3">';
																}

								$html .= '						</div>
															</td>
															<th style="border-bottom:none;">일반</th>
															<td class="center last" style="border-bottom:none;">
																<div class="left">';

																if ($printText){
																	$html .= '<div id="salaryAmt_'.$svcID.'_2_9" class="divText">'.number_format($salaryIf['salaryAmt_2']['9']).'</div>';
																}else{
																	$html .= '<input id="salaryAmt_'.$svcID.'_2_9" name="salaryAmt_'.$svcID.'_2[]" type="text" value="'.number_format($salaryIf['salaryAmt_2']['9']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_2" style="width:60px; margin:0;">
																			  <input id="salaryAmtCD_'.$svcID.'_2_9" name="salaryAmtCD_'.$svcID.'_2[]" type="hidden" value="9">';
																}

								$html .= '						</div>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>';
							}
			$html .= '		<tr>
								<th>수가총액비율</th>
								<td class="center last" colspan="2">
									<div class="left">';

									if ($printText){
										$html .= '<div id="salaryAmt_'.$svcID.'_4" class="divText">'.number_format($salaryIf['salaryAmt_4']).'%</div>';
									}else{
										$html .= '<input id="salaryAmt_'.$svcID.'_4" name="salaryAmt_'.$svcID.'_4" type="text" value="'.number_format($salaryIf['salaryAmt_4']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_4" style="width:70px; margin:0;">%';
									}

			$html .= '				</div>
								</td>
							</tr>
							<tr>
								<th class="'.($svcID != 12 && $printText ? 'bottom' : '').'">고정급</th>
								<td class="'.($svcID != 12 && $printText ? 'bottom' : '').' center last" colspan="2">
									<div class="left">';

									if ($printText){
										$html .= '<div id="salaryAmt_'.$svcID.'_3" class="divText">'.number_format($salaryIf['salaryAmt_3']).'</div>';

										if ($svcID == '11' || $svcID == '12' || $svcID == '24'){
											if ($salaryIf['salaryKind'] == '3' && $salaryIf['salaryExtra'] == 'Y')
												$html .= '<div id="salaryExtra_'.$svcID.'_3" class="divText" style="margin-left:10px;">목욕,간호수당포함</div>';
											else
												$html .= '<div style="margin-left:10px; color:#cccccc; font-weight:normal;">목욕,간호수당미포함</div>';
										}
									}else{
										$html .= '<input id="salaryAmt_'.$svcID.'_3" name="salaryAmt_'.$svcID.'_3" type="text" value="'.number_format($salaryIf['salaryAmt_3']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_3" style="width:70px; margin:0;">';

										if ($svcID == 11 || $svcID == 12 || $svcID == 24)
											$html .= '&nbsp;&nbsp;<input id="salaryExtra_'.$svcID.'_3" name="salaryExtra_'.$svcID.'_3" type="checkbox" value="Y" class="checkbox objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_3" style="margin:0;" '.($salaryIf['salaryExtra'] == 'Y' ? 'checked' : '').'><label for="salaryExtra_'.$svcID.'_3">목욕,간호수당포함</label>';
									}


			$html .= '				</div>
								</td>
							</tr>';

							if ($printText){
								$html .= '<input id="salaryFrom_'.$svcID.'" name="salaryFrom_'.$svcID.'" type="hidden" value="'.$salaryIf['salaryFromDt'].'">
										  <input id="salaryTo_'.$svcID.'" name="salaryTo_'.$svcID.'" type="hidden" value="'.$salaryIf['salaryToDt'].'">';
							}else{
								$html .= '<tr>
											<th class="bottom">적용기간</th>
											<td class="bottom center last" colspan="2">
												<div class="left">
													<input id="salaryFrom_'.$svcID.'" name="salaryFrom_'.$svcID.'" type="text" value="'.$salaryIf['salaryFromDt'].'" class="yymm" style="margin:0;">&nbsp;~&nbsp;
													<input id="salaryTo_'.$svcID.'" name="salaryTo_'.$svcID.'" type="text" value="'.$salaryIf['salaryToDt'].'" class="yymm" style="margin:0;" readonly>
												</div>
											</td>
										  </tr>';
							}

			$html .= '	</tbody>
					</table>

					<div id="salarySeq_'.$svcID.'" style="display:none;">'.$salaryIf['salarySeq'].'</div>';

			return $html;
		}
?>