<?php
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$sql = "select *
			  from m02yoyangsa
			 where m02_ccode        = '$code'
			   and m02_ygoyong_stat = '1'
			   and m02_del_yn       = 'N'
			 order by m02_yname, m02_yjumin, m02_mkind";

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$mst[$i] = $conn->select_row($i);
	}

	$conn->row_free();

	$mst_cnt = $row_count;

	for($i=0; $i<$mst_cnt; $i++){
		$kind  = $mst[$i]['m02_mkind'];
		$jumin = $mst[$i]['m02_yjumin'];

		/********************************************************************

			급여산정방식

		********************************************************************/
			if ($mst[$i]["m02_ygupyeo_kind"] == '1' || $mst[$i]["m02_ygupyeo_kind"] == '2'){
				if ($mst[$i]['m02_pay_type'] == 'Y'){
					$pay_type[$jumin][$kind] = 1; //시급(고정급)
				}else{
					$pay_type[$jumin][$kind] = 2; //시급(변동급)
				}
			}else if ($mst[$i]["m02_ygupyeo_kind"] == '3'){
				$pay_type[$jumin][$kind] = 3; //월급

				if ($mst[$i]['m02_pay_type'] == 'Y'){
					$pay_com_type[$jumin][$kind] = 'Y';
				}
			}else if ($mst[$i]["m02_ygupyeo_kind"] == '4'){
				$pay_type[$jumin][$kind] = 4; //총액비율
			}else{
				$pay_type[$jumin][$kind] = 0;
			}

			switch($pay_type[$jumin][$kind]){
				case 1:
					$hourly_1[$jumin][$kind] = $mst[$i]["m02_ygibonkup"];
					break;
				case 2:
					$sql = "select m02_gubun
							,      m02_pay
							  from m02pay
							 where m02_ccode = '$code'
							   and m02_mkind = '$kind'
							   and m02_jumin = '$jumin'";
					$conn->query($sql);
					$conn->fetch();
					$row_count = $conn->row_count();

					for($j=0; $j<$row_count; $j++){
						$row = $conn->select_row($j);
						$hourly_2[$jumin][$kind][$row['m02_gubun']] = $row['m02_pay'];
					}

					$conn->row_free();
					break;
				case 3:
					$hourly_3[$jumin][$kind] = $mst[$i]["m02_ygibonkup"];
					break;
				case 4:
					$hourly_4[$jumin][$kind] = $mst[$i]["m02_ysuga_yoyul"];
					break;
			}
		/********************************************************************/


		/********************************************************************

			동거가족급여

		********************************************************************/
			if ($kind == '0'){
				if($mst[$i]['m02_yfamcare_type'] == '1'){
					$famcare_type[$jumin] = 1; //고정급

					if($mst[$i]['m02_yfamcare_umu'] == 'N'){
						$famcare_type[$jumin] = 0;  //무
					}
				}else if($mst[$i]['m02_yfamcare_type'] == '2'){
					$famcare_type[$jumin] = 2; //수가총액
				}else if($mst[$i]['m02_yfamcare_type'] == '3'){
					$famcare_type[$jumin] = 3; //고정급
				}else {
					$famcare_type[$jumin] = 0;
				}

				// 동거가족 본인부담금 수당지급 여부
				$family_pay_yn[$jumin] = $mst[$i]['m02_family_pay_yn'];;

				switch($famcare_type[$jumin]){
					case '1':
						$famcare_pay1[$jumin] = $mst[$i]['m02_yfamcare_pay'];
						break;
					case '2':
						$famcare_pay2[$jumin] = $mst[$i]['m02_yfamcare_pay'];
						break;
					case '3':
						$famcare_pay3[$jumin] = $mst[$i]['m02_yfamcare_pay'];
						break;
					default:
						$family_pay_yn[$jumin] = 'N';
				}
			}
		/********************************************************************/
	}

	unset($tmp_jumin);

	for($i=0; $i<$mst_cnt; $i++){
		$kind  = $mst[$i]['m02_mkind'];
		$jumin = $mst[$i]['m02_yjumin'];

		if ($tmp_jumin != $jumin){
			$tmp_jumin  = $jumin;
			$r          = $i;

			$no ++;
			$ii = $no - 1;

			/************************************************

				스타일설정

			************************************************/
				if ($no % 2 == 1){
					$style = 'background-color:#ffffff;';
				}else{
					$style = 'background-color:#f9f9f9;';
				}
			/***********************************************/

			if ($no == 1){
				$html[0][0] = '<div id=\'list_div\' style=\'width:'.$col_widths[0].'px; float:left; overflow-x:hidden; overflow-y:hidden\'>';
				$html[0][1] = '</div>';

				$html[1][0] = '<div id=\'cont_div\' style=\'width:'.($col_max - $col_widths[0]).'px; float:left; overflow:scroll;\'>';
				$html[1][1] = '</div>';
			}


			/********************************************************************************************************************************************************************************************

				직원명과 주민번호

			********************************************************************************************************************************************************************************************/
				$html[2][0] .= '<div style=\'position:relative; top:-'.($ii * 26).'px;\'>';
					$html[2][0] .= '<div class=\'text center\' style=\''.$style.' width:'.($col_width[0]).'px; height:53px; line-height:53px;\'>'.$no.'</div>';
					$html[2][0] .= '<div class=\'text left\' style=\''.$style.' width:'.($col_width[1]).'px; height:28px;\'>'.$mst[$r]['m02_yname'].'</div>';
				$html[2][0] .= '</div>';

				$html[2][0] .= '<div style=\'position:relative; left:'.($col_width[0]).'px; top:-'.($ii * 26 + 26).'px;\'>';
					$html[2][0] .= '<div class=\'text left\' style=\''.$style.' width:'.($col_width[1]).'px;\'>'.$myF->issStyle($mst[$r]['m02_yjumin']).'</div>';
				$html[2][0] .= '</div>';
			/*******************************************************************************************************************************************************************************************/

			/********************************************************************************************************************************************************************************************

				리스트

			********************************************************************************************************************************************************************************************/
				$html[2][1] .= '<div style=\'position:relative; width:'.$col_widths[1].'px;\'>';

					#입사일
					$html[2][1] .= '<div id=\'list_menu_0[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[2]).'px; height:28px;\'><input name=\'join_dt[]\' type=\'text\' class=\'date\' style=\'width:100%;\' value=\''.$myF->dateStyle($mst[$r]['m02_yipsail']).'\'></div>';

					#4대보험 가입여부
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px; height:28px;\'><span onclick=\'set_4ins_yn(this, "annuity", '.$ii.');\' style=\'cursor:pointer; font-weight:'.($mst[$r]['m02_ykmbohum_umu'] == 'Y' ? 'bold' : 'normal').';\'>'.($mst[$r]['m02_ykmbohum_umu'] == 'Y' ? '가입' : '미가입').'</span></div>';
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px; height:28px;\'><span onclick=\'set_4ins_yn(this, "health", '.$ii.');\'  style=\'cursor:pointer; font-weight:'.($mst[$r]['m02_ygnbohum_umu'] == 'Y' ? 'bold' : 'normal').';\'>'.($mst[$r]['m02_ygnbohum_umu'] == 'Y' ? '가입' : '미가입').'</span></div>';
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px; height:28px;\'><span onclick=\'set_4ins_yn(this, "employ", '.$ii.');\'  style=\'cursor:pointer; font-weight:'.($mst[$r]['m02_ygobohum_umu'] == 'Y' ? 'bold' : 'normal').';\'>'.($mst[$r]['m02_ygobohum_umu'] == 'Y' ? '가입' : '미가입').'</span></div>';
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px; height:28px;\'><span onclick=\'set_4ins_yn(this, "sanje", '.$ii.');\'   style=\'cursor:pointer; font-weight:'.($mst[$r]['m02_ysnbohum_umu'] == 'Y' ? 'bold' : 'normal').';\'>'.($mst[$r]['m02_ysnbohum_umu'] == 'Y' ? '가입' : '미가입').'</span></div>';

					#근로기준 시간
					$html[2][1] .= '<div id=\'list_menu_2[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[4]).'px; height:28px;\'><input name=\'stnd_time[]\' type=\'text\' class=\'number\' style=\'width:100%;\' value=\''.$mst[$r]['m02_stnd_work_time'].'\' onkeydown=\'__onlyNumber(this, ".");\'></div>';

					#직급수당
					$html[2][1] .= '<div id=\'list_menu_3[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[5]).'px; height:28px;\'><input name=\'stnd_time[]\' type=\'text\' class=\'number\' style=\'width:100%;\' value=\''.$mst[$r]['m02_rank_pay'].'\'></div>';

					#배상책임보험 가입여부
					$html[2][1] .= '<div id=\'list_menu_4[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[6]).'px; height:28px;\'><span onclick=\'set_ins_yn(this, '.$ii.');\' style=\'cursor:pointer; font-weight:'.($mst[$r]['m02_ins_yn'] == 'Y' ? 'bold' : 'normal').';\'>'.($mst[$r]['m02_ins_yn'] == 'Y' ? '가입' : '미가입').'</span></div>';

					#일반수급자케어 급여산정방식
					$html[2][1] .= '<div id=\'list_menu_5[]\' class=\'text left\' style=\''.$style.' width:'.($col_width[7]).'px; height:28px;\'>
										<input name=\'pay_kind_0_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'0\' onclick=\'set_normal_care('.$ii.');\' '.($pay_type[$jumin][0] == 0 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>무</span>
										<input name=\'pay_kind_0_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'1\' onclick=\'set_normal_care('.$ii.');\' '.($pay_type[$jumin][0] == 1 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>고정시급</span>
										<input name=\'pay_kind_0_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'2\' onclick=\'set_normal_care('.$ii.');\' '.($pay_type[$jumin][0] == 2 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>변동시급</span>
										<input name=\'pay_kind_0_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'4\' onclick=\'set_normal_care('.$ii.');\' '.($pay_type[$jumin][0] == 4 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>총액비율</span>
										<input name=\'pay_kind_0_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'3\' onclick=\'set_normal_care('.$ii.');\' '.($pay_type[$jumin][0] == 3 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>월급</span>
									</div>';

					#동거가족케어 급여산정방식
					$html[2][1] .= '<div id=\'list_menu_6[]\' class=\'text left\' style=\''.$style.' width:'.($col_width[8]).'px; height:28px;\'>
										<input name=\'family_pay_kind_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'0\' onclick=\'set_family_care('.$ii.');\' '.($famcare_type[$jumin] == 0 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>무</span>
										<input name=\'family_pay_kind_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'1\' onclick=\'set_family_care('.$ii.');\' '.($famcare_type[$jumin] == 1 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>고정시급</span>
										<input name=\'family_pay_kind_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'2\' onclick=\'set_family_care('.$ii.');\' '.($famcare_type[$jumin] == 2 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>수가총액비율</span>
										<input name=\'family_pay_kind_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'3\' onclick=\'set_family_care('.$ii.');\' '.($famcare_type[$jumin] == 3 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>고정급</span>
									</div>';

					#바우처 급여산정방식
					for($j=1; $j<=4; $j++){
						$html[2][1] .= '<div id=\'list_menu_'.($j+6).'[]\' class=\'text left\' style=\''.$style.' width:'.($col_width[$j+9-1]).'px; height:28px;\'>
											<input name=\'voucher_pay_kind_'.$j.'_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'0\' onclick=\'set_voucher_care('.$j.','.$ii.');\' '.($pay_type[$jumin][$j] == 0 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>무</span>
											<input name=\'voucher_pay_kind_'.$j.'_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'1\' onclick=\'set_voucher_care('.$j.','.$ii.');\' '.($pay_type[$jumin][$j] == 1 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>고정시급</span>
											<input name=\'voucher_pay_kind_'.$j.'_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'4\' onclick=\'set_voucher_care('.$j.','.$ii.');\' '.($pay_type[$jumin][$j] == 4 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>총액비율</span>
											<input name=\'voucher_pay_kind_'.$j.'_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'3\' onclick=\'set_voucher_care('.$j.','.$ii.');\' '.($pay_type[$jumin][$j] == 3 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>월급</span>
										</div>';
					}

					#비급여수가급여
					$html[2][1] .= '<div id=\'list_menu_11[]\' class=\'text left\' style=\''.$style.' width:'.($col_width[13]).'px; height:28px;\'>
										<input name=\'bipay_yn_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'Y\' onclick=\'set_bipay_care('.$ii.');\' '.($mst[$r]['m02_bipay_yn'] == 'Y' ? 'checked' : '').'><span style=\'margin-left:-5px;\'>지급</span>
										<input name=\'bipay_yn_'.$ii.'\' type=\'radio\' class=\'radio\' value=\'N\' onclick=\'set_bipay_care('.$ii.');\''.($mst[$r]['m02_bipay_yn'] != 'Y' ? 'checked' : '').'><span style=\'margin-left:-5px;\'>미지급</span>
									</div>';

				$html[2][1] .= '</div>';

				$html[2][1] .= '<div style=\'position:relative; width:'.$col_widths[1].'px;\'>';

					#모바일
					$html[2][1] .= '<div id=\'list_menu_0[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[2]).'px;\'><input name=\'mobile[]\' type=\'text\' class=\'phone\' style=\'width:100%;\' value=\''.$myF->phoneStyle($mst[$r]['m02_ytel']).'\'></div>';

					#4대보험 가입금액
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px;\'><input name=\'annuity_pay[]\' type=\'text\' class=\'number\' style=\'width:100%;\' value=\''.$mst[$r]['m02_ykuksin_mpay'].'\'></div>';
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px;\'><input name=\'health_pay[]\'  type=\'text\' class=\'number\' style=\'width:100%;\' value=\''.$mst[$r]['m02_health_mpay'].'\'></div>';
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px;\'><input name=\'employ_pay[]\'  type=\'text\' class=\'number\' style=\'width:100%;\' value=\''.$mst[$r]['m02_employ_mpay'].'\'></div>';
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px;\'><input name=\'sanje_pay[]\'   type=\'text\' class=\'number\' style=\'width:100%;\' value=\''.$mst[$r]['m02_sanje_mpay'].'\'></div>';

					#근로기준 시급
					$html[2][1] .= '<div id=\'list_menu_2[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[4]).'px;\'><input name=\'stnd_pay[]\' type=\'text\' class=\'number\' style=\'width:100%;\' value=\''.$mst[$r]['m02_stnd_work_pay'].'\'></div>';

					#연장특별수당
					$html[2][1] .= '<div id=\'list_menu_3[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[5]).'px;\'><input name=\'stnd_pay[]\' type=\'text\' class=\'number\' style=\'width:80%;\' value=\''.$mst[$r]['m02_add_payrate'].'\' onkeydown=\'__onlyNumber(this, ".");\'>%</div>';

					#배상책임보험 가입기간
					$html[2][1] .= '<div id=\'list_menu_4[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[6]).'px;\'>
										<input name=\'ins_from_dt[]\' type=\'text\' class=\'date\' value=\''.$mst[$r]['m02_ins_from_date'].'\'> ~
										<input name=\'ins_to_dt[]\'   type=\'text\' class=\'date\' value=\''.$mst[$r]['m02_ins_to_date'].'\'>
									</div>';

					#일반수급자케어 급여금액
					$html[2][1] .= '<div id=\'list_menu_5[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[7]).'px;\'>
										<div id=\'pay_type_1_'.$ii.'\' class=\'left\' style=\'display:'.($pay_type[$jumin][0] == 1 ? '' : 'none').';\'>
											시급 <input name=\'hourly_pay_0_'.$ii.'\' type=\'text\' value=\''.$hourly_1[$jumin][0].'\' maxlength=\'8\' class=\'number\' style=\'width:50px;\'>
										</div>
										<div id=\'pay_type_2_'.$ii.'\' class=\'left\' style=\'display:'.($pay_type[$jumin][0] == 2 ? '' : 'none').';\'>
											1등급 <input name=\'change_hourly_pay_0_1_'.$ii.'\' type=\'text\' value=\''.$hourly_2[$jumin][0][1].'\' maxlength=\'8\' class=\'number\' style=\'width:50px;\'>
											2등급 <input name=\'change_hourly_pay_0_2_'.$ii.'\' type=\'text\' value=\''.$hourly_2[$jumin][0][2].'\' maxlength=\'8\' class=\'number\' style=\'width:50px;\'>
											3등급 <input name=\'change_hourly_pay_0_3_'.$ii.'\' type=\'text\' value=\''.$hourly_2[$jumin][0][3].'\' maxlength=\'8\' class=\'number\' style=\'width:50px;\'>
											일반  <input name=\'change_hourly_pay_0_9_'.$ii.'\' type=\'text\' value=\''.$hourly_2[$jumin][0][9].'\' maxlength=\'8\' class=\'number\' style=\'width:50px;\'>
										</div>
										<div id=\'pay_type_4_'.$ii.'\' class=\'left\' style=\'display:'.($pay_type[$jumin][0] == 4 ? '' : 'none').';\'>
											총액비율 <input name=\'suga_rate_pay_0_'.$ii.'\' type=\'text\' value=\''.$hourly_4[$jumin][0].'\' maxlength=\'4\' class=\'number\' onKeyDown=\'__onlyNumber(this,".");\' style=\'width:50px;\'>%
										</div>
										<div id=\'pay_type_3_'.$ii.'\' class=\'left\' style=\'display:'.($pay_type[$jumin][0] == 3 ? '' : 'none').';\'>
											월급 <input name=\'base_pay_0_'.$ii.'\' type=\'text\' value=\''.$hourly_3[$jumin][0].'\' maxlength=\'8\' class=\'number\'>
												 <input name=\'ybnpay_0_'.$ii.'\' type=\'checkbox\' class=\'checkbox\' value=\'Y\' '.($mst[$r]['m02_bnpay_yn'] == 'Y' ? '' : '').'>목욕,간호수당포함
										</div>
									</div>';

					#동거가족케어 급여금액
					$html[2][1] .= '<div id=\'list_menu_6[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[8]).'px;\'>
										<div id=\'family_pay_type_1_'.$ii.'\' class=\'left\' style=\'display:'.($famcare_type[$jumin] == 1 ? '' : 'none').';\'>
											고정시급 <input name=\'family_hourly_pay[]\' type=\'text\' value=\''.$famcare_pay1[$jumin].'\' maxlength=\'8\' class=\'number\' style=\'width:50px;\'>
										</div>
										<div id=\'family_pay_type_2_'.$ii.'\' class=\'left\' style=\'display:'.($famcare_type[$jumin] == 2 ? '' : 'none').';\'>
											수가총액비율 <input name=\'family_suga_rate_pay[]\' type=\'text\' value=\''.$famcare_pay2[$jumin].'\' maxlength=\'4\' class=\'number\' onKeyDown=\'__onlyNumber(this,".");\' style=\'width:50px;\'>%
										</div>
										<div id=\'family_pay_type_3_'.$ii.'\' class=\'left\' style=\'display:'.($famcare_type[$jumin] == 3 ? '' : 'none').';\'>
											고정급 <input name=\'family_base_pay[]\' type=\'text\' value=\''.$famcare_pay3[$jumin].'\' maxlength=\'8\' class=\'number\'>
										</div>
									</div>';

					#바우처 급여금액
					for($j=1; $j<=4; $j++){
						$html[2][1] .= '<div id=\'list_menu_'.($j + 6).'[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[$j+9-1]).'px;\'>
											<div id=\'voucher_pay_type_1_'.$j.'_'.$ii.'\' class=\'left\' style=\'display:'.($pay_type[$jumin][$j] == 1 ? '' : 'none').';\'>
												시급 <input name=\'hourly_pay_'.$j.'_'.$ii.'\' type=\'text\' value=\''.$hourly_1[$jumin][$j].'\' maxlength=\'8\' class=\'number\' style=\'width:50px;\'>
											</div>
											<div id=\'voucher_pay_type_4_'.$j.'_'.$ii.'\' class=\'left\' style=\'display:'.($pay_type[$jumin][$j] == 4 ? '' : 'none').';\'>
												총액비율 <input name=\'suga_rate_pay_'.$j.'_'.$ii.'\' type=\'text\' value=\''.$hourly_4[$jumin][$j].'\' maxlength=\'4\' class=\'number\' onKeyDown=\'__onlyNumber(this,".");\' style=\'width:50px;\'>%
											</div>
											<div id=\'voucher_pay_type_3_'.$j.'_'.$ii.'\' class=\'left\' style=\'display:'.($pay_type[$jumin][$j] == 3 ? '' : 'none').';\'>
												월급 <input name=\'base_pay_'.$j.'_'.$ii.'\' type=\'text\' value=\''.$hourly_3[$jumin][$j].'\' maxlength=\'8\' class=\'number\'>
											</div>
										</div>';
					}

					#동거가족케어 급여금액
					$html[2][1] .= '<div id=\'list_menu_11[]\' class=\'text left\' style=\''.$style.' width:'.($col_width[13]).'px;\'>
										지급율 <input name=\'bipay_rate[]\' type=\'text\' value=\''.$mst[$r]['m02_bipay_rate'].'\' class=\'number\' maxlength=\'3\' style=\'width:50px;\'> %
									</div>';

				$html[2][1] .= '</div>';
			/*******************************************************************************************************************************************************************************************/


			$html[2][1] .= '<input name=\'index[]\' type=\'hidden\' value=\''.$ii.'\'>'; //국민연금 가입여부

			$html[2][1] .= '<input name=\'annuity_yn[]\' type=\'hidden\' value=\''.($mst[$r]['m02_ykmbohum_umu'] == 'Y' ? 'Y' : 'N').'\'>'; //국민연금 가입여부
			$html[2][1] .= '<input name=\'health_yn[]\'  type=\'hidden\' value=\''.($mst[$r]['m02_ygnbohum_umu'] == 'Y' ? 'Y' : 'N').'\'>'; //건강보험 가입여부
			$html[2][1] .= '<input name=\'employ_yn[]\'  type=\'hidden\' value=\''.($mst[$r]['m02_ygobohum_umu'] == 'Y' ? 'Y' : 'N').'\'>'; //고용보험 가입여부
			$html[2][1] .= '<input name=\'sanje_yn[]\'   type=\'hidden\' value=\''.($mst[$r]['m02_ysnbohum_umu'] == 'Y' ? 'Y' : 'N').'\'>'; //산재보험 가입여부

			$html[2][1] .= '<input name=\'ins_yn[]\'   type=\'hidden\' value=\''.($mst[$r]['m02_ins_yn'] == 'Y' ? 'Y' : 'N').'\'>'; //배상책임 가입여부

			$html[2][1] .= '<input name=\'change_hourly_cd_0_1[]\' type=\'hidden\' value=\'1\'>';
			$html[2][1] .= '<input name=\'change_hourly_cd_0_2[]\' type=\'hidden\' value=\'2\'>';
			$html[2][1] .= '<input name=\'change_hourly_cd_0_3[]\' type=\'hidden\' value=\'3\'>';
			$html[2][1] .= '<input name=\'change_hourly_cd_0_9[]\' type=\'hidden\' value=\'9\'>';
		}
	}

	echo $html[0][0];
		echo $html[2][0];
	echo $html[0][1];

	echo $html[1][0];
		echo $html[2][1];
	echo $html[1][1];

	unset($pay_type);
	unset($hourly_1);
	unset($hourly_2);
	unset($hourly_3);
	unset($hourly_4);
	unset($html);

	include_once('../inc/_db_close.php');
?>