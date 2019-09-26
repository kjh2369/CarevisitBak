<?
	class mySalary{
		var $corpFlag = false;
		var $babyDayPay = false;

		/*********************************************************

			현재 월급내역 정보의 쿼리

		*********************************************************/
		function _queryNowSalary($code, $jumin, $date = 'now()'){
			$sql = 'select ms_salary as pay
					,      ms_care_yn as care_yn
					,      ms_extra_yn as extra_yn
					,      ms_20day_yn AS day20_yn
					,		ms_dealpay AS dealpay
					,      ms_from_dt as from_dt
					,      ms_to_dt as to_dt
					  from mem_salary
					 where org_no      = \''.$code.'\'
					   and ms_jumin    = \''.$jumin.'\'
					   and ms_from_dt <= date_format('.$date.', \'%Y%m\')
					   and ms_to_dt   >= date_format('.$date.', \'%Y%m\')
					   and del_flag    = \'N\'';

			return $sql;
		}



		/*********************************************************

			현재 시급내역 정보의 쿼리

		*********************************************************/
		function _queryNowHourly($code, $jumin, $svcID = '', $seq = 'auto', $date = 'now()'){
			$sql = 'select mh_svc as svc_id
					,      mh_seq as seq
					,      mh_kind as kind
					,      mh_type as type
					,      mh_hourly as hourly
					,      mh_vary_hourly_1 as vary_hourly_1
					,      mh_vary_hourly_2 as vary_hourly_2
					,      mh_vary_hourly_3 as vary_hourly_3
					,      mh_vary_hourly_4 as vary_hourly_4
					,      mh_vary_hourly_5 as vary_hourly_5
					,      mh_vary_hourly_6 as vary_hourly_6
					,      mh_vary_hourly_7 as vary_hourly_7
					,      mh_vary_hourly_8 as vary_hourly_8
					,      mh_vary_hourly_9 as vary_hourly_9
					,      mh_hourly_rate as hourly_rate
					,      mh_fixed_pay as fixed_pay
					,		mh_daily_pay1 AS daily_pay1
					,		mh_daily_pay2 AS daily_pay2
					,		mh_daily_pay3 AS daily_pay3
					,      mh_extra_yn as extra_yn
					,      mh_from_dt as from_dt
					,      mh_to_dt as to_dt
					  from mem_hourly
					 where org_no      = \''.$code.'\'
					   and mh_jumin    = \''.$jumin.'\'';

			if ($svcID != '' && $seq != ''){
				$sql .= ' and mh_svc = \''.$svcID.'\'';

				if ($seq != 'auto')
					$sql .= ' and mh_seq = \''.$seq.'\'';
			}

			if ($seq == 'auto'){
				$sql .= '  and mh_from_dt <= date_format('.$date.', \'%Y%m\')
						   and mh_to_dt   >= date_format('.$date.', \'%Y%m\')';
			}

			$sql .= '  and del_flag = \'N\'';

			return $sql;
		}

		function _setHourlyData($dbData){
			$salaryHourIf = Array(
				 'salarySeq'=>$dbData['seq']
				,'salarySvcID'=>$dbData['svc_id']
				,'salaryKind'=>$dbData['type']
				,'salaryAmt_1'=>$dbData['hourly']
				,'salaryAmt_2'=>Array('1'=>$dbData['vary_hourly_1']
									, '2'=>$dbData['vary_hourly_2']
									, '3'=>$dbData['vary_hourly_3']
									, '4'=>$dbData['vary_hourly_4']
									, '5'=>$dbData['vary_hourly_5']
									, '6'=>$dbData['vary_hourly_6']
									, '7'=>$dbData['vary_hourly_7']
									, '8'=>$dbData['vary_hourly_8']
									, '9'=>$dbData['vary_hourly_9'])
				,'salaryAmt_3'=>$dbData['fixed_pay']
				,'salaryAmt_4'=>$dbData['hourly_rate']
				,'salaryAmt_6'=>Array(
									'1'=>$dbData['daily_pay1']
								,	'2'=>$dbData['daily_pay2']
								,	'3'=>$dbData['daily_pay3']
								)
				,'salaryExtra'=>$dbData['extra_yn']
				,'salaryFromDt'=>substr($dbData['from_dt'],0,4).'-'.substr($dbData['from_dt'],4,2)
				,'salaryToDt'=>substr($dbData['to_dt'],0,4).'-'.substr($dbData['to_dt'],4,2));

			return $salaryHourIf;
		}



		/*********************************************************

			서비스별 급여 내역 테이블

		*********************************************************/
		function _getSalarySvc($svcID, $salaryIf, $printText = true, $joinYM = '', $para = ''){
			switch($svcID){
				case 11: $strTitle = '재가요양'; break;
				case 12: $strTitle = '동거가족'; break;
				case 21: $strTitle = '가사간병'; break;
				case 22: $strTitle = '노인돌봄'; break;
				case 23: $strTitle = '산모신생아'; break;
				case 24: $strTitle = '장애인활동보조'; break;
			}

			if (empty($salaryIf['salaryKind'])) $salaryIf['salaryKind'] = '0';
			if (empty($salaryIf['salaryFromDt'])) $salaryIf['salaryFromDt'] = $joinYM;
			if (empty($salaryIf['salaryFromDt'])) $salaryIf['salaryFromDt'] = date('Y-m', mktime());
			if (empty($salaryIf['salaryToDt'])) $salaryIf['salaryToDt'] = '9999-12';

			$html = '<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="80px">
							<col>';

			if ($printText){
				$html .= '<col width="40px">';
			}else{
				$html .= '<col width="5px">';
			}

			$html .= '	</colgroup>
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
										if ($this->corpFlag){
											switch($salaryIf['salaryKind']){
												case '0':
													$html .= '<div id="salaryKind_'.$svcID.'_0" class="divText"><span style="color:#ff0000;">√</span>무</div>';
													break;

												case '1':
													$html .= '<div id="salaryKind_'.$svcID.'_1" class="divText"><span style="color:#ff0000;">√</span>시급</div>';
													break;

												case '2':
													$html .= '<div id="salaryKind_'.$svcID.'_2" class="divText"><span style="color:#ff0000;">√</span>수가별</div>';
													break;

												case '3':
													$html .= '<div id="salaryKind_'.$svcID.'_3" class="divText"><span style="color:#ff0000;">√</span>고정급</div>';
													break;

												case '4':
													$html .= '<div id="salaryKind_'.$svcID.'_4" class="divText"><span style="color:#ff0000;">√</span>총액비율</div>';
													break;

												case '6':
													$html .= '<div id="salaryKind_'.$svcID.'_6" class="divText"><span style="color:#ff0000;">√</span>일당</div>';
													break;

												case '7':
													$html .= '<div id="salaryKind_'.$svcID.'_7" class="divText"><span style="color:#ff0000;">√</span>공단비율</div>';
													break;
											}
										}else{
											$html .= '<div id="salaryKind_'.$svcID.'_0" class="divText">'.($salaryIf['salaryKind'] == '0' ? '<span style="color:#ff0000;">√</span>무' : '<span style="color:#cccccc; font-weight:normal;">무</span>').' <span style="font-weight:normal;">|</span> </div>';

											$html .= '<div id="salaryKind_'.$svcID.'_1" class="divText">'.($salaryIf['salaryKind'] == '1' ? '<span style="color:#ff0000;">√</span>시급' : '<span style="color:#cccccc; font-weight:normal;">시급</span>').' <span style="font-weight:normal;">|</span> </div>';

											if ($svcID == 11){
												$html .= '<div id="salaryKind_'.$svcID.'_2" class="divText">'.($salaryIf['salaryKind'] == '2' ? '<span style="color:#ff0000;">√</span>수가별' : '<span style="color:#cccccc; font-weight:normal;">수가별</span>').' <span style="font-weight:normal;">|</span> </div>';
											}

											$html .= '<div id="salaryKind_'.$svcID.'_4" class="divText">'.($salaryIf['salaryKind'] == '4' ? '<span style="color:#ff0000;">√</span>총액비율' : '<span style="color:#cccccc; font-weight:normal;">총액비율</span>').' <span style="font-weight:normal;">|</span> </div>';

											if ($this->babyDayPay){
												if ($svcID == 23){
													$html .= '<div id="salaryKind_'.$svcID.'_6" class="divText">'.($salaryIf['salaryKind'] == '6' ? '<span style="color:#ff0000;">√</span>일당' : '<span style="color:#cccccc; font-weight:normal;">일당</span>').' <span style="font-weight:normal;">|</span> </div>';
												}
											}

											$html .= '<div id="salaryKind_'.$svcID.'_3" class="divText">'.($salaryIf['salaryKind'] == '3' ? '<span style="color:#ff0000;">√</span>고정급' : '<span style="color:#cccccc; font-weight:normal;">고정급</span>').'</div>';
										}

										$html .= '<div id="salaryKind_'.$svcID.'" style="display:none;">'.$salaryIf['salaryKind'].'</div>';
									}else{
										$html .= '<input id="salaryKind_'.$svcID.'_0" name="salaryKind_'.$svcID.'" type="radio" value="0" class="radio" onclick="_memSalarySetSvcSub(\''.$svcID.'\');" '.($salaryIf['salaryKind'] == '0' ? 'checked' : '').'><label for="salaryKind_'.$svcID.'_0">무</label>
												  <input id="salaryKind_'.$svcID.'_1" name="salaryKind_'.$svcID.'" type="radio" value="1" class="radio" onclick="_memSalarySetSvcSub(\''.$svcID.'\');" '.($salaryIf['salaryKind'] == '1' ? 'checked' : '').'><label for="salaryKind_'.$svcID.'_1">시급</label>';

										if ($svcID == 11){
											//$html .= '<input id="salaryKind_'.$svcID.'_2" name="salaryKind_'.$svcID.'" type="radio" value="2" class="radio" onclick="_memSalarySetSvcSub(\''.$svcID.'\');" '.($salaryIf['salaryKind'] == '2' ? 'checked' : '').'><label for="salaryKind_'.$svcID.'_2">변동시급</label>';
											$html .= '<input id="salaryKind_'.$svcID.'_2" name="salaryKind_'.$svcID.'" type="radio" value="2" class="radio" onclick="_memSalarySetSvcSub(\''.$svcID.'\');" '.($salaryIf['salaryKind'] == '2' ? 'checked' : '').'><label for="salaryKind_'.$svcID.'_2">수가별수당</label>';
										}

										$html .= '<input id="salaryKind_'.$svcID.'_4" name="salaryKind_'.$svcID.'" type="radio" value="4" class="radio" onclick="_memSalarySetSvcSub(\''.$svcID.'\');" '.($salaryIf['salaryKind'] == '4' ? 'checked' : '').'><label for="salaryKind_'.$svcID.'_4">총액비율</label>';

										if ($this->babyDayPay){
											if ($svcID == 23){
												$html .= '<input id="salaryKind_'.$svcID.'_6" name="salaryKind_'.$svcID.'" type="radio" value="6" class="radio" onclick="_memSalarySetSvcSub(\''.$svcID.'\');" '.($salaryIf['salaryKind'] == '6' ? 'checked' : '').'><label for="salaryKind_'.$svcID.'_6">일당</label>';
											}
										}

										$html .= '<input id="salaryKind_'.$svcID.'_3" name="salaryKind_'.$svcID.'" type="radio" value="3" class="radio" onclick="_memSalarySetSvcSub(\''.$svcID.'\');" '.($salaryIf['salaryKind'] == '3' ? 'checked' : '').'><label for="salaryKind_'.$svcID.'_3">고정급</label>';

										if ($this->corpFlag){
											if ($svcID == 11 || $svcID == 12){
												$html .= '<input id="salaryKind_'.$svcID.'_7" name="salaryKind_'.$svcID.'" type="radio" value="7" class="radio" onclick="_memSalarySetSvcSub(\''.$svcID.'\');" '.($salaryIf['salaryKind'] == '7' ? 'checked' : '').'><label for="salaryKind_'.$svcID.'_3">공단비율</label>';
											}
										}
									}

			$html .= '				</div>
								</td>
								<td class="center last">';

									if ($printText){
										//변경 버튼
										$html .= '<div class="right"><img id="btnSalarySet_'.$svcID.'" src="../image/btn_change.gif" onclick="_memSalaryHourSet(\''.$svcID.'\');" style="cursor:pointer;"></div>';
									}else{
										$html .= '&nbsp;';
									}


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
											<th>수가별수당</th>
											<td class="last" colspan="2">
												<table class="my_table" style="width:100%;">
													<colgroup>
														<col width="35px">
														<col>
														<col width="35px">
														<col>
														<col width="35px">
														<col>
														<col width="35px">
														<col>';


								$html .= '			</colgroup>
													<tbody>';

								if ($printText){
									$html .= '	<tr>
													<th>30분</th>
													<td class="center">
														<div id="salaryAmt_'.$svcID.'_2_1" class="left divText">'.number_format($salaryIf['salaryAmt_2']['1']).'</div>
													</td>
													<th>60분</th>
													<td class="center">
														<div id="salaryAmt_'.$svcID.'_2_2" class="left divText">'.number_format($salaryIf['salaryAmt_2']['2']).'</div>
													</td>
													<th>90분</th>
													<td class="center last">
														<div id="salaryAmt_'.$svcID.'_2_3" class="left divText">'.number_format($salaryIf['salaryAmt_2']['3']).'</div>
													</td>
												</tr>
												<tr>
													<th>120분</th>
													<td class="center">
														<div id="salaryAmt_'.$svcID.'_2_4" class="left divText">'.number_format($salaryIf['salaryAmt_2']['4']).'</div>
													</td>
													<th>150분</th>
													<td class="center">
														<div id="salaryAmt_'.$svcID.'_2_5" class="left divText">'.number_format($salaryIf['salaryAmt_2']['5']).'</div>
													</td>
													<th>180분</th>
													<td class="center last">
														<div id="salaryAmt_'.$svcID.'_2_6" class="left divText">'.number_format($salaryIf['salaryAmt_2']['6']).'</div>
													</td>
												</tr>
												<tr>
													<th style="border-bottom:none;">210분</th>
													<td class="center" style="border-bottom:none;">
														<div id="salaryAmt_'.$svcID.'_2_7" class="left divText">'.number_format($salaryIf['salaryAmt_2']['7']).'</div>
													</td>
													<th style="border-bottom:none;">240분</th>
													<td class="center" style="border-bottom:none;">
														<div id="salaryAmt_'.$svcID.'_2_8" class="left divText">'.number_format($salaryIf['salaryAmt_2']['8']).'</div>
													</td>
													<th style="border-bottom:none;">&nbsp;</th>
													<td class="center last" style="border-bottom:none;">&nbsp;</td>
												</tr>';
								}else{
									$html .= '	<tr>
													<th>30분</th>
													<td class="center">
														<input id="salaryAmt_'.$svcID.'_2_1" name="salaryAmt_'.$svcID.'_2[]" type="text" value="'.number_format($salaryIf['salaryAmt_2']['1']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_2" style="width:50px; margin:0;"><input id="salaryAmtCD_'.$svcID.'_2_1" name="salaryAmtCD_'.$svcID.'_2[]" type="hidden" value="1">
													</td>
													<th>60분</th>
													<td class="center">
														<input id="salaryAmt_'.$svcID.'_2_2" name="salaryAmt_'.$svcID.'_2[]" type="text" value="'.number_format($salaryIf['salaryAmt_2']['2']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_2" style="width:50px; margin:0;"><input id="salaryAmtCD_'.$svcID.'_2_2" name="salaryAmtCD_'.$svcID.'_2[]" type="hidden" value="2">
													</td>
													<th>90분</th>
													<td class="center">
														<input id="salaryAmt_'.$svcID.'_2_3" name="salaryAmt_'.$svcID.'_2[]" type="text" value="'.number_format($salaryIf['salaryAmt_2']['3']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_2" style="width:50px; margin:0;"><input id="salaryAmtCD_'.$svcID.'_2_3" name="salaryAmtCD_'.$svcID.'_2[]" type="hidden" value="3">
													</td>
													<th>120분</th>
													<td class="center last">
														<input id="salaryAmt_'.$svcID.'_2_4" name="salaryAmt_'.$svcID.'_2[]" type="text" value="'.number_format($salaryIf['salaryAmt_2']['4']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_2" style="width:50px; margin:0;"><input id="salaryAmtCD_'.$svcID.'_2_4" name="salaryAmtCD_'.$svcID.'_2[]" type="hidden" value="4">
													</td>
												</tr>
												<tr>
													<th style="border-bottom:none;">150분</th>
													<td class="center" style="border-bottom:none;">
														<input id="salaryAmt_'.$svcID.'_2_5" name="salaryAmt_'.$svcID.'_2[]" type="text" value="'.number_format($salaryIf['salaryAmt_2']['5']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_2" style="width:50px; margin:0;"><input id="salaryAmtCD_'.$svcID.'_2_5" name="salaryAmtCD_'.$svcID.'_2[]" type="hidden" value="5">
													</td>
													<th style="border-bottom:none;">180분</th>
													<td class="center" style="border-bottom:none;">
														<input id="salaryAmt_'.$svcID.'_2_6" name="salaryAmt_'.$svcID.'_2[]" type="text" value="'.number_format($salaryIf['salaryAmt_2']['6']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_2" style="width:50px; margin:0;"><input id="salaryAmtCD_'.$svcID.'_2_6" name="salaryAmtCD_'.$svcID.'_2[]" type="hidden" value="6">
													</td>
													<th style="border-bottom:none;">210분</th>
													<td class="center" style="border-bottom:none;">
														<input id="salaryAmt_'.$svcID.'_2_7" name="salaryAmt_'.$svcID.'_2[]" type="text" value="'.number_format($salaryIf['salaryAmt_2']['7']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_2" style="width:50px; margin:0;"><input id="salaryAmtCD_'.$svcID.'_2_7" name="salaryAmtCD_'.$svcID.'_2[]" type="hidden" value="7">
													</td>
													<th style="border-bottom:none;">240분</th>
													<td class="center last" style="border-bottom:none;">
														<input id="salaryAmt_'.$svcID.'_2_8" name="salaryAmt_'.$svcID.'_2[]" type="text" value="'.number_format($salaryIf['salaryAmt_2']['8']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_2" style="width:50px; margin:0;"><input id="salaryAmtCD_'.$svcID.'_2_8" name="salaryAmtCD_'.$svcID.'_2[]" type="hidden" value="8">
													</td>
												</tr>';
								}

								$html .= '			</tbody>
												</table>
											</td>
										</tr>';
							}
			$html .= '		<tr>
								<th>수가총액비율</th>
								<td class="center last" colspan="2">
									<div class="left">';

									/*
										if ($printText){
											$html .= '<div id="salaryAmt_'.$svcID.'_4" class="divText">'.number_format($salaryIf['salaryAmt_4']).'%</div>';
										}else{
											$html .= '<input id="salaryAmt_'.$svcID.'_4" name="salaryAmt_'.$svcID.'_4" type="text" value="'.number_format($salaryIf['salaryAmt_4']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_4" style="width:70px; margin:0;">%';
											$html .= '<input id="salaryAmt_'.$svcID.'_4_Sub" name="salaryAmt_'.$svcID.'_4_Sub" type="hidden" value="00">';
										}
									 */

									//소수점 입력가능하게 수정
									if ($printText){
										$html .= '<div id="salaryAmt_'.$svcID.'_4" class="divText">'.number_format($salaryIf['salaryAmt_4'],2).'%</div>';
									}else{
										$val	= Explode('.',$salaryIf['salaryAmt_4']);

										$html .= '<input id="salaryAmt_'.$svcID.'_4" name="salaryAmt_'.$svcID.'_4" type="text" value="'.$val[0].'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_4" style="width:30px; margin:0;" maxlength="3">.';
										$html .= '<input id="salaryAmt_'.$svcID.'_4_Sub" name="salaryAmt_'.$svcID.'_4_Sub" type="text" value="'.$val[1].'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_4" style="width:30px; margin:0;" maxlength="2">%';
									}

			$html .= '				</div>
								</td>
							</tr>';

			if ($this->babyDayPay){
				if ($svcID == 23){
					$html .= '	<tr>
									<th>일당</th>
									<td class="center last" colspan="2">
										<table class="my_table" style="width:100%;">
											<colgroup>
												<col width="40px">
												<col width="55px">
												<col width="40px">
												<col width="55px">
												<col width="40px">
												<col>
											</colgroup>
											<tbody>
												<tr>';
					if ($printText){
						$html .= '					<th class="bottom">단태아</th>
													<td class="left bottom">'.number_format($salaryIf['salaryAmt_6']['1']).'</td>
													<th class="bottom">쌍태아</th>
													<td class="left bottom">'.number_format($salaryIf['salaryAmt_6']['2']).'</td>
													<th class="bottom">삼태아</th>
													<td class="left bottom last">'.number_format($salaryIf['salaryAmt_6']['3']).'</td>';
					}else{
						$html .= '					<th class="bottom">단태아</th>
													<td class="bottom"><input id="salaryAmt_'.$svcID.'_6_1" name="salaryAmt_'.$svcID.'_6[]" type="text" value="'.number_format($salaryIf['salaryAmt_6']['1']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_6" style="width:60px;"></td>
													<th class="bottom">쌍태아</th>
													<td class="bottom"><input id="salaryAmt_'.$svcID.'_6_2" name="salaryAmt_'.$svcID.'_6[]" type="text" value="'.number_format($salaryIf['salaryAmt_6']['2']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_6" style="width:60px;"></td>
													<th class="bottom">삼태아</th>
													<td class="bottom last"><input id="salaryAmt_'.$svcID.'_6_3" name="salaryAmt_'.$svcID.'_6[]" type="text" value="'.number_format($salaryIf['salaryAmt_6']['3']).'" class="number objSlyClsSvc_'.$svcID.' objSlyClsSvc_'.$svcID.'_6" style="width:60px;"></td>';
					}
					$html .= '					</tr>
											</tbody>
										</table>
									</td>
								</tr>';
				}
			}

			$html .= '		<tr>
								<th class="'.($svcID != 12 && $printText && !isset($para['babyShow']) ? 'bottom' : '').'">고정급</th>
								<td class="'.($svcID != 12 && $printText && !isset($para['babyShow']) ? 'bottom' : '').' center last" colspan="2">
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
				$html .= '	<input id="salaryFrom_'.$svcID.'" name="salaryFrom_'.$svcID.'" type="hidden" value="'.$salaryIf['salaryFromDt'].'">
							<input id="salaryTo_'.$svcID.'" name="salaryTo_'.$svcID.'" type="hidden" value="'.$salaryIf['salaryToDt'].'">';
			}else{
				$html .= '	<tr>
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
	}

	$mySalary = new mySalary();
	$mySalary->corpFlag = true;
	$mySalary->babyDayPay = true; //산모신생아 일당 적용여부
?>