<?
	if (!isset($code)) include_once('../inc/_http_home.php');

	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('iljung_config.php');

	$sql = "select m03_yoyangsa1 as mem_cd1
		 ,     (select m02_yname from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa1 and m02_ygoyong_stat = '1') as mem_nm1
		 ,     (select m02_ygibonkup from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa1 and m02_ygupyeo_kind in ('1','2') and m02_ygoyong_stat = '1') as mem_pay1
		 ,      m03_yoyangsa2 as mem_cd2
		 ,     (select m02_yname from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa2 and m02_ygoyong_stat = '1') as mem_nm2
		 ,     (select m02_ygibonkup from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa2 and m02_ygupyeo_kind in ('1','2') and m02_ygoyong_stat = '1') as mem_pay2
		   from m03sugupja
		  where m03_ccode = '$code'
		    and m03_mkind = '$svc_cd'
		    and m03_jumin = '$jumin'";

	$mem = $conn->get_array($sql);

	$yoy1   = $mem['mem_cd1'];
	$yoyNm1 = $mem['mem_nm1'];
	$yoyTA1 = $mem['mem_pay1'];

	$yoy2   = $mem['mem_cd2'];
	$yoyNm2 = $mem['mem_nm2'];
	$yoyTA2 = $mem['mem_pay2'];





	/**************************************************

		목욕수당 정/부 비율

	**************************************************/
	$sql = 'select m00_muksu_yul1 as rate1
			,      m00_muksu_yul2 as rate2
			  from m00center
			 where m00_mcode = \''.$code.'\'
			 limit 1';

	$bath_sudang_rate = $conn->get_array($sql);




	/**************************************************

		바우처 생성내역 조회

	**************************************************/
	$sql = "select voucher_totaltime as svc_max_time
			,      voucher_suga_cd as suga_cd
			,      voucher_suga_cost as svc_cost
			,      voucher_gbn as gbn
			,      voucher_gbn2 as gbn2
			,      voucher_lvl as lvl
			  from voucher_make
			 where org_no        = '$code'
			   and voucher_kind  = '$svc_cd'
			   and voucher_jumin = '$jumin'
			   and voucher_yymm  = '$year$month'
			   and del_flag      = 'N'";

	$mst = $conn->get_array($sql);

	##################################################################
	#
	# 바우처 생성내역존재 여부
	#
		if (empty($mst)){
			$voucher_make_yn = 'N';
		}else{
			$voucher_make_yn = 'Y';
		}

		/**************************************************

			바우처 생성내역이 없을 경우 마지막 생성내역을 사용

		**************************************************/
		if ($voucher_make_yn != 'Y'){
			$sql = 'select voucher_totaltime as svc_max_time
					,      voucher_suga_cd as suga_cd
					,      voucher_suga_cost as svc_cost
					,      voucher_gbn as gbn
					,      voucher_gbn2 as gbn2
					,      voucher_lvl as lvl
					  from voucher_make
					 where org_no        = \''.$code.'\'
					   and voucher_kind  = \''.$svc_cd.'\'
					   and voucher_jumin = \''.$jumin.'\'
					   and del_flag      = \'N\'
					 order by voucher_yymm desc
					 limit 1';

			$mst = $conn->get_array($sql);

			if (empty($mst)){
				/**************************************************

					바우처 생성기록이 없는 경우

				**************************************************/
				$sql = 'select suga_service.service_code as suga_cd
						,      suga_service.service_gbn as suga_nm
						,      suga_bipay.suga_from_dt as suga_from_dt
						,      suga_bipay.suga_to_dt as suga_to_dt
						  from suga_service
						 inner join suga_bipay
							on suga_bipay.org_no             = suga_service.org_no
						   and suga_bipay.svc_kind           = suga_service.service_kind
						   and suga_bipay.suga_code          = suga_service.service_code
						   and suga_bipay.suga_from_dt      >= suga_service.service_from_dt
						   and suga_bipay.suga_from_dt      <= suga_service.service_to_dt
						 where suga_service.org_no           = \''.$conn->mst_code.'\'
						   and suga_service.service_kind     = \''.$svc_cd.'\'
						   and suga_service.service_from_dt <= \''.$year.$month.'\'
						   and suga_service.service_to_dt   >= \''.$year.$month.'\'
						 order by svc_kind, suga_from_dt';

				$tmp_suga = $conn->get_array($sql);

				$mst['svc_max_time'] = 0;
				$mst['suga_cd']      = $tmp_suga['suga_cd'];
				$mst['svc_cost']     = 0;
				$mst['gbn']          = '';
				$mst['gbn2']         = '';
				$mst['lvl']          = '';

				unset($tmp_suga);
			}
		}

		echo '<input name=\'voucher_make_yn\' type=\'hidden\' value=\''.$voucher_make_yn.'\'>';
	#
	##################################################################



	/*****************************************************************

		바우처가 없으므로 기본 데이타를 가져온다.

	*****************************************************************/
	/****************************************************************/


	$suga_cd = $mst['suga_cd'];

	$wrt_mode = $myF->get_iljung_mode();

	##################################################################
	#
	# 시간입력 방지설정
	$time_readonly  = '';
	$time_style     = '';
	$time_from_hour = '';
	$time_from_min  = '';
	$time_to_hour   = '';
	$time_to_min    = '';
	$time_proc      = '';

	if ($svc_id == 23){
		// 산모신생아

		if (intval($day) > 0)
			$weekday = date('w', mktime(0,0,0,$month,$day,$year));
		else
			$weekday = 1;

		$time_from_readonly = 'readonly';
		$time_from_style    = 'background-color:#efefef;';
		$time_to_readonly   = 'readonly';
		$time_to_style      = 'background-color:#efefef;';
		$time_from_hour     = '09';
		$time_from_min      = '00';
		$time_to_hour       = $weekday != 6 ? '17' : '13';
		$time_to_min        = '00';
		$time_proc          = '1';
	}else if ($svc_id == 22 && $mst['gbn'] == 'D'){
		$time_to_readonly   = 'readonly';
		$time_to_style      = 'background-color:#efefef;';
	}

	if ($wrt_mode == 2 && $mode == 'MODIFY'){
		//수정모드
		$time_from_readonly = 'readonly';
		$time_from_style    = 'background-color:#efefef;';
	}

	#
	##################################################################

	ob_start();

	if ($svc_id == 24){
		/**************************************************

			장애인활동지원

		**************************************************/
		echo '<table class=\'my_table my_border_blue\' style=\'width:100%;'.($wrt_mode == 1 ? '' : 'margin-top:-2px;').'\'>';
		echo '	<colgroup>
					<col width=\'155px\'>
					<col width=\'70px\'>
					<col width=\'95px\'>
					<col width=\'30px\'>
					<col width=\'110px\'>
					<col width=\'90px\'>
					<col width=\'90px\'>
					<col width=\'90px\'>
					<col>
				</colgroup>';
			echo '<thead>';

				if ($wrt_mode == 1){
					echo '<tr><th class=\'head bold\' colspan=\'11\'>'.$kind_nm.'</th></tr>';
				}

				echo '<tr>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>제공서비스</th>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>비용구분</th>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>제공자</th>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\' colspan=\'2\'>방문시간</th>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>소요시간</th>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>입욕선택</th>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>차량선택</th>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>비고</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
				echo '<tr>';

					echo '<td rowspan=\'2\'>';
						/*
						echo '<input name=\'svcSubCode\' type=\'radio\' value=\'200\' class=\'radio\' onclick=\'_set_voucher_svc();\' checked><a id=\'linkSvcCode\' href=\'#\'
									 onclick=\'document.getElementsByName("svcSubCode")[0].checked = true; _set_voucher_svc();\'>활동지원</a><br>';
						echo '<input name=\'svcSubCode\' type=\'radio\' value=\'500\' class=\'radio\' onclick=\'_set_voucher_svc();\'><a id=\'linkSvcCode\' href=\'#\'
									 onclick=\'document.getElementsByName("svcSubCode")[1].checked = true; _set_voucher_svc();\'>방문목욕</a>';
						echo '<input name=\'svcSubCode\' type=\'radio\' value=\'800\' class=\'radio\' onclick=\'_set_voucher_svc();\'><a id=\'linkSvcCode\' href=\'#\'
									 onclick=\'document.getElementsByName("svcSubCode")[2].checked = true; _set_voucher_svc();\'>방문간호</a>';
						*/
						echo '<input id=\'svcSubCode_200\' name=\'svcSubCode\' type=\'radio\' value=\'200\' class=\'radio\' onclick=\'_set_voucher_svc();\' checked><label for=\'svcSubCode_200\'>활동지원</label><br>';
						echo '<input id=\'svcSubCode_500\' name=\'svcSubCode\' type=\'radio\' value=\'500\' class=\'radio\' onclick=\'_set_voucher_svc();\'><label for=\'svcSubCode_500\'>방문목욕</label>';
						echo '<input id=\'svcSubCode_800\' name=\'svcSubCode\' type=\'radio\' value=\'800\' class=\'radio\' onclick=\'_set_voucher_svc();\'><label for=\'svcSubCode_800\'>방문간호</label>';
					echo '</td>';

					if ($voucher_make_yn == 'Y')
						echo '<td rowspan=\'2\'><input id=\'bipayUmu\' name=\'bipayUmu\' type=\'checkbox\' class=\'checkbox\' value=\'Y\' onclick=\'_set_bipay_yn(); _set_bipay_pay(); _get_iljung_suga();\'><label for=\'bipayUmu\'>비급여</label></td>';
					else
						echo '<td rowspan=\'2\'><input id=\'bipayUmu\' name=\'bipayUmu\' type=\'checkbox\' class=\'checkbox\' value=\'Y\' onclick=\'this.checked=true;\' checked>비급여</td>';

					echo '<td>';
						echo '<input name=\'yoyNm1\' type=\'text\'   value=\''.$yoyNm1.'\' style=\'width:70px; background-color:#eeeeee;\' onClick=\'_helpSuYoyPA("'.$code.'","'.$svc_cd.'","'.$key.'",document.f.yoy1,document.f.yoyNm1,document.f.yoyTA1); _iljung_check_time();\' readOnly><a onClick=\'_unset_mem("1");\'><span class=\'bold\'>X</span></a>';
						echo '<input name=\'yoy1\'   type=\'hidden\' value=\''.$ed->en($yoy1).'\'>';
						echo '<input name=\'yoyTA1\' type=\'hidden\' value=\''.$yoyTA1.'\'>';
					echo '</td>';
					echo '<th>시작</th>';
					echo '<td>';
						echo '<input name=\'ftHour\' type=\'text\' value=\''.$time_from_hour.'\' tag=\''.$time_from_hour.'\' maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px;'.$time_from_style.'\' onKeyDown=\'__onlyNumber(this);\' onfocus=\'this.select();\' onkeyup=\'_iljung_check_time(this);\' onchange=\'__object_set_format(this, "number", 2);\' '.$time_from_readonly.'>시';
						echo '<input name=\'ftMin\'  type=\'text\' value=\''.$time_from_min.'\'  tag=\''.$time_from_min.'\'  maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px;'.$time_from_style.'\' onKeyDown=\'__onlyNumber(this);\' onfocus=\'this.select();\' onkeyup=\'_iljung_check_time(this);\' onchange=\'__object_set_format(this, "number", 2);\' '.$time_from_readonly.'>분';
					echo '</td>';
					echo '<td><input name=\'procTime\' type=\'text\' value=\''.$time_proc.'\' tag=\''.$time_proc.'\' class=\'number\' style=\'width:100%; cursor:default; background-color:#eeeeee;\' onfocus=\'this.blur();\' readonly></td>';
					echo '<td>
							<select name=\'svcSubCD\' style=\'width:auto;\' onChange=\'_get_iljung_suga();\' disabled=\'true\'>
								<option value=\'1\'>차량입욕</option>
								<option value=\'2\'>가정내입욕</option>
							</select>
						  </td>';
					echo '<td>
							<select name=\'carNo\' style=\'width:auto; margin-right:3px;\' disabled=\'true\'>';
								$sql = "select m00_car_no1, m00_car_no2
										  from m00center
										 where m00_mcode = '$code'
										   and m00_mkind = '$kind'";

								$car_arr = $conn->get_array($sql);

								echo '<option value=\''.$car_arr[0].'\'>'.$car_arr[0].'</option>';
								echo '<option value=\''.$car_arr[1].'\'>'.$car_arr[1].'</option>';

								unset($car_arr);
								echo '</select>
						  </td>';

					echo '<td>&nbsp;</td>';
				echo '</tr>';

				echo '<tr>';

					echo '<td>';
						echo '<div id=\'mem_if2\' style=\'display:none;\'>';
						echo '<input name=\'yoyNm2\' type=\'text\'   value=\''.$yoyNm2.'\' tag=\''.$yoyNm2.'\' style=\'width:70px; background-color:#eeeeee;\' onClick=\'_helpSuYoyPA("'.$code.'","'.$svc_cd.'","'.$key.'",document.f.yoy2,document.f.yoyNm2,document.f.yoyTA2); _iljung_check_time();\' readOnly><a onClick=\'_unset_mem("2");\'><span id=\'delete_yoy2\' class=\'bold\'>X</span></a>';
						echo '<input name=\'yoy2\'   type=\'hidden\' value=\''.$ed->en($yoy2).'\'   tag=\''.$ed->en($yoy2).'\'>';
						echo '<input name=\'yoyTA2\' type=\'hidden\' value=\''.$yoyTA2.'\' tag=\''.$yoyTA2.'\'>';
						echo '</div>';
					echo '</td>';
					echo '<th>종료</th>';
					echo '<td>';
						echo '<input name=\'ttHour\' type=\'text\' value=\''.$time_to_hour.'\' tag=\''.$time_to_hour.'\' maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px;'.$time_to_style.'\' onKeyDown=\'__onlyNumber(this);\' onfocus=\'this.select();\' onkeyup=\'_iljung_check_time(this);\' onchange=\'__object_set_format(this, "number", 2);\' '.$time_to_readonly.'>시';
						echo '<input name=\'ttMin\'  type=\'text\' value=\''.$time_to_min.'\'  tag=\''.$time_to_min.'\'  maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px;'.$time_to_style.'\' onKeyDown=\'__onlyNumber(this);\' onfocus=\'this.select();\' onkeyup=\'_iljung_check_time(this);\' onchange=\'__object_set_format(this, "number", 2);\' '.$time_to_readonly.'>분';
					echo '</td>';
					echo '<td colspan=\'4\'>
							<input name=\'visitSudangCheck\' type=\'checkbox\' class=\'checkbox\' value=\'Y\' disabled=\''.$visit[0].'\' onClick=\'checkVisitSugang(this.checked);\'>방문건별 수당
							<input name=\'visitSudang\' type=\'text\' value=\'0\' tag=\'0\' disabled=\''.$visit[0].'\' class=\'number\' style=\'width:60px; background-color:'.$visit[1].';\' onKeyDown=\'__onlyNumber(this);\' onFocus=\'__commaUnset(this);\' onBlur=\'__commaSet(this);\'>원&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							수당비율(<input name=\'sudangYul1\' type=\'text\' value=\''.$bath_sudang_rate['rate1'].'\' maxlength=\'5\' tag=\''.$bath_sudang_rate['rate1'].'\' disabled=\''.$visit[0].'\' class=\'number\' style=\'width:40px; background-color:'.$visit[1].';\' onKeyDown=\'__onlyNumber(this, ".");\' onFocus=\'this.select();\' onChange=\'return _setBathRate("1");\'> /
							<input name=\'sudangYul2\' type=\'text\' value=\''.$bath_sudang_rate['rate2'].'\' maxlength=\'5\' tag=\''.$bath_sudang_rate['rate2'].'\' disabled=\''.$visit[0].'\' class=\'number\' style=\'width:40px; background-color:'.$visit[1].';\' onKeyDown=\'__onlyNumber(this, ".");\' onFocus=\'this.select();\' onChange=\'return _setBathRate("2");\'>)
						  </td>';

				echo '</tr>';
			echo '</tbody>';
		echo '</table>';

	}else{
		/**************************************************

			가사간병, 노인돌봄, 산모신생아

		**************************************************/
		echo '<table class=\'my_table my_border_blue\' style=\'width:100%;'.($wrt_mode == 1 ? '' : 'margin-top:-2px;').'\'>';
		echo '	<colgroup>
					<col width=\'100px\'>
					<col width=\'80px\'>
					<col width=\'95px\'>
					<col width=\'30px\'>
					<col width=\'110px\'>
					<col width=\'30px\'>
					<col width=\'110px\'>
					<col width=\'90px\'>
					<col width=\'80px\'>
					<col width=\'90px\'>
					<col>
				</colgroup>';
			echo '<thead>';

				if ($wrt_mode == 1){
					echo '<tr><th class=\'head bold\' colspan=\'11\'>'.$kind_nm.'</th></tr>';
				}

				echo '<tr>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>제공서비스</th>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>비용구분</th>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>제공자</th>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\' colspan=\'4\'>방문시간</th>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>소요시간(일)</th>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>서비스단가</th>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>서비스시간(일)</th>';
					echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>비고</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
				echo '<tr>';
					echo '<td class=\'left\'>'.$kind_nm.'</td>';

					if ($voucher_make_yn == 'Y')
						echo '<td class=\'left\'><input name=\'bipayUmu\' type=\'checkbox\' class=\'checkbox\' value=\'Y\' onclick=\'_set_bipay_yn(); _set_bipay_pay(); _get_iljung_suga();\'>비급여</td>';
					else
						echo '<td class=\'left\'><input name=\'bipayUmu\' type=\'checkbox\' class=\'checkbox\' value=\'Y\' onclick=\'this.checked=true;\' checked>비급여</td>';

					echo '<td>';
						echo '<input name=\'yoyNm1\' type=\'text\'   value=\''.$yoyNm1.'\' style=\'width:70px; background-color:#eeeeee;\' onClick=\'_helpSuYoyPA("'.$code.'","'.$svc_cd.'","'.$key.'",document.f.yoy1,document.f.yoyNm1,document.f.yoyTA1); _iljung_check_time();\' readOnly><a onClick=\'_unset_mem("1");\'><span class=\'bold\'>X</span></a>';
						echo '<input name=\'yoy1\'   type=\'hidden\' value=\''.$ed->en($yoy1).'\'>';
						echo '<input name=\'yoyTA1\' type=\'hidden\' value=\''.$yoyTA1.'\'>';
					echo '</td>';
					echo '<th>시작</th>';
					echo '<td>';
						echo '<input name=\'ftHour\' type=\'text\' value=\''.$time_from_hour.'\' tag=\''.$time_from_hour.'\' maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px;'.$time_from_style.'\' onKeyDown=\'__onlyNumber(this);\' onfocus=\'this.select();\' onkeyup=\'_iljung_check_time(this);\' onchange=\'__object_set_format(this, "number", 2);\' '.$time_from_readonly.'>시';
						echo '<input name=\'ftMin\'  type=\'text\' value=\''.$time_from_min.'\'  tag=\''.$time_from_min.'\'  maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px;'.$time_from_style.'\' onKeyDown=\'__onlyNumber(this);\' onfocus=\'this.select();\' onkeyup=\'_iljung_check_time(this);\' onchange=\'__object_set_format(this, "number", 2);\' '.$time_from_readonly.'>분';
					echo '</td>';
					echo '<th>종료</th>';
					echo '<td>';
						echo '<input name=\'ttHour\' type=\'text\' value=\''.$time_to_hour.'\' tag=\''.$time_to_hour.'\' maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px;'.$time_to_style.'\' onKeyDown=\'__onlyNumber(this);\' onfocus=\'this.select();\' onkeyup=\'_iljung_check_time(this);\' onchange=\'__object_set_format(this, "number", 2);\' '.$time_to_readonly.'>시';
						echo '<input name=\'ttMin\'  type=\'text\' value=\''.$time_to_min.'\'  tag=\''.$time_to_min.'\'  maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px;'.$time_to_style.'\' onKeyDown=\'__onlyNumber(this);\' onfocus=\'this.select();\' onkeyup=\'_iljung_check_time(this);\' onchange=\'__object_set_format(this, "number", 2);\' '.$time_to_readonly.'>분';
					echo '</td>';
					echo '<td><input name=\'procTime\' type=\'text\' value=\''.$time_proc.'\' tag=\''.$time_proc.'\' class=\'number\' style=\'width:100%; cursor:default; background-color:#eeeeee;\' onfocus=\'this.blur();\' readonly></td>';
					echo '<td class=\'right\'>'.number_format($mst['svc_cost']).'</td>';
					echo '<td class=\'right\'>'.number_format($mst['svc_max_time']).'</td>';
					echo '<td></td>';
				echo '</tr>';
			echo '</tbody>';
		echo '</table>';

		echo '<input name=\'svcSubCode\' type=\'hidden\' value=\'Y\'>';

		echo '<input name=\'yoy2\'   type=\'hidden\' value=\'\'>';
		echo '<input name=\'yoyNm2\' type=\'hidden\' value=\'\'>';
		echo '<input name=\'yoyTA2\' type=\'hidden\' value=\'\'>';
	}



	/**************************************************

		비급여 설정

	**************************************************/
	include('iljung_reg_expense.php');
	/*************************************************/



	/**************************************************

		산모 추가 요금 등록

	**************************************************/
	include('iljung_reg_addpay.php');
	/*************************************************/



	########################################################
	#
	# 제공요일 및 일자
	#
	include_once('iljung_svc_date.php');
	########################################################

	########################################################
	#
	# 적용수가
	#
	include_once('iljung_svc_suga.php');
	########################################################



	echo '<input id=\'svcStnd\'    name=\'svcStnd\'    type=\'hidden\' value=\'0\'>';
	echo '<input id=\'svcHoliday\' name=\'svcHoliday\' type=\'hidden\' value=\'0\'>';
	echo '<input id=\'svcCnt\'     name=\'svcCnt\'     type=\'hidden\' value=\'0\'>';
	echo '<input id=\'svcCost\'    name=\'svcCost\'    type=\'hidden\' value=\''.$mst['svc_cost'].'\'>';
	echo '<input id=\'svcMaxTime\' name=\'svcMaxTime\' type=\'hidden\' value=\''.$mst['svc_max_time'].'\'>';
	echo '<input id=\'svcSuga\'    name=\'svcSuga\'    type=\'hidden\' value=\''.$suga_cd.'\'>';
	echo '<input id=\'svcGbn\'     name=\'svcGbn\'     type=\'hidden\' value=\''.$mst['gbn'].'\'>';
	echo '<input id=\'svcGbn2\'    name=\'svcGbn2\'    type=\'hidden\' value=\''.$mst['gbn2'].'\'>';
	echo '<input id=\'svcLvl\'     name=\'svcLvl\'     type=\'hidden\' value=\''.$mst['lvl'].'\'>';



	/**************************************************

		시간입력제한

	**************************************************/
	echo '<input name=\'svcLimitTime\'    type=\'hidden\' value=\'0\'>'; //제한업음
	echo '<input name=\'svcLimitTime200\' type=\'hidden\' value=\'8\'>'; //장애활동보조 일8시간 제한
	echo '<input name=\'svcLimitTime500\' type=\'hidden\' value=\'1\'>'; //방문목욕 주1회 제한
	echo '<input name=\'svcLimitTime800\' type=\'hidden\' value=\'3\'>'; //방문간호 주3회 제한




	$html = ob_get_contents();

	ob_end_clean();

	echo $html;

	unset($mst);

	include_once('../inc/_db_close.php');
?>