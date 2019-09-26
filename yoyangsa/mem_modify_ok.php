<?php
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo $myF->header_script();

	$code  = $_POST['code'];  #기관기호
	$index = $_POST['index']; #인덱스

	$index_cnt = sizeof($index);

	$conn->begin();

	for($i=0; $i<$index_cnt; $i++){
		$id = $index[$i];

		$jumin   = $ed->de($_POST['jumin'][$id]); //주민번호
		$join_dt = str_replace('-', '', $_POST['join_dt'][$id]); #입사일
		$mobile  = str_replace('-', '', $_POST['mobile'][$id]);  #핸드폰

		$annuity_yn = $_POST['annuity_yn'][$id]; //국민연금
		$health_yn  = $_POST['health_yn'][$id];  //건강보험
		$employ_yn  = $_POST['employ_yn'][$id];  //고용보험
		$sanje_yn   = $_POST['sanje_yn'][$id];   //산재보험

		if ($annuity_yn == 'N' && $health_yn == 'N' && $employ_yn == 'N' && $sanje_yn == 'N')
			$ins_yn4 = 'N';
		else
			$ins_yn4 = 'Y';


		$annuity_pay = ($annuity_yn == 'Y' ? $_POST['annuity_pay'][$id] : 0); //국민연금
		$health_pay  = ($health_yn  == 'Y' ? $_POST['health_pay'][$id]  : 0); //건강보험
		$employ_pay  = ($employ_yn  == 'Y' ? $_POST['employ_pay'][$id]  : 0); //고용보험
		$sanje_pay   = ($sanje_yn   == 'Y' ? $_POST['sanje_pay'][$id]   : 0); //산재보험

		$stnd_time = $_POST['stnd_time'][$id]; //근로기준시간
		$stnd_pay  = str_replace(',','',$_POST['stnd_pay'][$id]);  //근로기준시급

		$rank_pay    = str_replace(',','',$_POST['rank_pay'][$id]);    //직급수당
		$add_payrate = str_replace(',','',$_POST['add_payrate'][$id]); //특별수당

		$ins_yn      = ($_POST['ins_yn'][$id] == 'Y' ? 'Y' : 'N');      //배상책입가입여부
		$ins_code    = '0';
		$ins_from_dt = ($ins_yn == 'Y' ? $_POST['ins_from_dt'][$id] : ''); //배상책임기간
		$ins_to_dt   = ($ins_yn == 'Y' ? $_POST['ins_to_dt'][$id]   : ''); //배상책임기간

		$ybnpay = ($_POST['ybnpay_0_'.$id] == 'Y' ? 'Y' : 'N'); //목욕,간호수당포함여부



		/****************************************

			일반수급자케어 급여산정

		****************************************/
			$tmp_pay_kind = $_POST['pay_kind_0_'.$id]; #일반수급자케어 급여산정방식

			if ($tmp_pay_kind == '1'){
				$pay_kind[0]  = '1';
				$pay_type[0]  = 'Y';
				$pay_basic[0] = str_replace(',','',$_POST['hourly_pay_0_'.$id]);
				$pay_rate[0]  = 0;
			}else if ($tmp_pay_kind == '2'){
				$pay_kind[0]  = '1';
				$pay_type[0]  = 'N';
				$pay_basic[0] = 0;
				$pay_rate[0]  = 0;

				// 변동 시급 저장
				$sql = 'replace into m02pay values
						 (\''.$code.'\', \'0\', \''.$jumin.'\', \''.$_POST['change_hourly_cd_0_1_'.$id].'\', \''.str_replace(',', '', $_POST['change_hourly_pay_0_1_'.$id]).'\')
						,(\''.$code.'\', \'0\', \''.$jumin.'\', \''.$_POST['change_hourly_cd_0_2_'.$id].'\', \''.str_replace(',', '', $_POST['change_hourly_pay_0_2_'.$id]).'\')
						,(\''.$code.'\', \'0\', \''.$jumin.'\', \''.$_POST['change_hourly_cd_0_3_'.$id].'\', \''.str_replace(',', '', $_POST['change_hourly_pay_0_3_'.$id]).'\')
						,(\''.$code.'\', \'0\', \''.$jumin.'\', \''.$_POST['change_hourly_cd_0_9_'.$id].'\', \''.str_replace(',', '', $_POST['change_hourly_pay_0_9_'.$id]).'\')';
				$conn->execute($sql);
			}else if ($tmp_pay_kind == '3'){
				$pay_kind[0]  = '3';
				$pay_type[0]  = ' ';
				$pay_basic[0] = str_replace(',','',$_POST['base_pay_0_'.$id]);
				$pay_rate[0]  = 0;
			}else if ($tmp_pay_kind == '4'){
				$pay_kind[0]  = '4';
				$pay_type[0]  = ' ';
				$pay_basic[0] = 0;
				$pay_rate[0]  = str_replace(',','',$_POST['suga_rate_pay_0_'.$id]);
			}else{
				$pay_kind[0]  = '0';
				$pay_type[0]  = ' ';
				$pay_basic[0] = 0;
				$pay_rate[0]  = 0;
			}
		/***************************************

			동거가족케어 급여산정

		***************************************/
			$tmp_pay_kind = $_POST['family_pay_kind_'.$id];

			if ($tmp_pay_kind == '1'){
				$famcare_type = '1';
				$famcare_umu  = 'Y';
				$famcare_pay  = str_replace(',','',$_POST['family_hourly_pay_'.$id]);
			}else if ($tmp_pay_kind == '2'){
				$famcare_type = '2';
				$famcare_umu  = 'Y';
				$famcare_pay  = str_replace(',','',$_POST['family_suga_rate_pay_'.$id]);
			}else if ($tmp_pay_kind == '3'){
				$famcare_type = '3';
				$famcare_umu  = 'Y';
				$famcare_pay  = str_replace(',','',$_POST['family_base_pay_'.$id]);
			}else{
				$famcare_type = '1';
				$famcare_umu  = 'N';
				$famcare_pay  = 0;
			}
		/***************************************

			바우처 급여산정

		***************************************/
			for($j=1; $j<=4; $j++){
				$tmp_pay_kind = $_POST['voucher_pay_kind_'.$j.'_'.$id]; #바우처 급여산정방식

				if ($tmp_pay_kind == '1'){
					$pay_kind[$j]  = '1';
					$pay_type[$j]  = 'Y';
					$pay_basic[$j] = str_replace(',','',$_POST['hourly_pay_'.$j.'_'.$id]);
					$pay_rate[$j]  = 0;
				}else if ($tmp_pay_kind == '3'){
					$pay_kind[$j]  = '3';
					$pay_type[$j]  = ' ';
					$pay_basic[$j] = str_replace(',','',$_POST['base_pay_'.$j.'_'.$id]);
					$pay_rate[$j]  = 0;
				}else if ($tmp_pay_kind == '4'){
					$pay_kind[$j]  = '4';
					$pay_type[$j]  = ' ';
					$pay_basic[$j] = 0;
					$pay_rate[$j]  = str_replace(',','',$_POST['suga_rate_pay_'.$j.'_'.$id]);
				}else{
					$pay_kind[$j]  = '0';
					$pay_type[$j]  = ' ';
					$pay_basic[$j] = 0;
					$pay_rate[$j]  = 0;
				}
			}
		/***************************************

			비급여수가급여

		***************************************/
			$bipay_yn   = $_POST['bipay_yn_'.$id];
			$bipay_rate = ($bipay_yn == 'Y' ? $_POST['bipay_rate_'.$id] : 0);
		/**************************************/


		for($j=0; $j<=4; $j++){
			$sql = 'update m02yoyangsa
					   set m02_yipsail = \''.$join_dt.'\'
					,      m02_ytel    = \''.$mobile.'\'

					,      m02_y4bohum_umu  = \''.$ins_yn4.'\'
					,      m02_ygobohum_umu = \''.$annuity_yn.'\'
					,      m02_ysnbohum_umu = \''.$health_yn.'\'
					,      m02_ygnbohum_umu = \''.$employ_yn.'\'
					,      m02_ykmbohum_umu = \''.$sanje_yn.'\'

					,      m02_ykuksin_mpay	= \''.$annuity_pay.'\'
					,      m02_health_mpay	= \''.$health_pay.'\'
					,      m02_employ_mpay	= \''.$employ_pay.'\'
					,      m02_sanje_mpay	= \''.$sanje_pay.'\'

					,      m02_stnd_work_time = \''.$stnd_time.'\'
					,      m02_stnd_work_pay  = \''.$stnd_pay.'\'

					,      m02_rank_pay    = \''.$rank_pay.'\'
					,      m02_add_payrate = \''.$add_payrate.'\'

					,      m02_ins_yn		 = \''.$ins_yn.'\'
					,      m02_ins_code		 = \''.$ins_code.'\'
					,      m02_ins_from_date = \''.$ins_from_dt.'\'
					,      m02_ins_to_date	 = \''.$ins_to_dt.'\'

					,      m02_ygupyeo_kind  = \''.$pay_kind[$j].'\'
					,      m02_pay_type		 = \''.$pay_type[$j].'\'
					,      m02_ygibonkup	 = \''.$pay_basic[$j].'\'
					,      m02_ysuga_yoyul	 = \''.$pay_rate[$j].'\'';

			if ($j == 0){
				$sql .= ', m02_bnpay_yn      = \''.$ybnpay.'\'
					,      m02_yfamcare_umu  = \''.$famcare_umu.'\'
					,      m02_yfamcare_pay  = \''.$famcare_pay.'\'
					,      m02_yfamcare_type = \''.$famcare_type.'\'';
			}else{
				$sql .= ', m02_bnpay_yn      = \'N\'';
			}

			$sql .= ',     m02_del_yn = \'N\'

					 where m02_ccode  = \''.$code.'\'
					   and m02_mkind  = \''.$j.'\'
					   and m02_yjumin = \''.$jumin.'\'';

			if (!$conn->execute($sql)){
				echo $conn->err_back();
			}
		}
	}

	$conn->commit();

	include_once('../inc/_db_close.php');

	echo '<script>';
	echo 'alert(\''.$myF->message('OK','N').'\');';
	echo 'location.replace(\'mem_modify.php\');';
	echo '</script>';
?>