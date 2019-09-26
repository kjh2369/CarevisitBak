<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$conn->mode = 1;
	$host = $myF->host();

	$edit_mode = $_POST['edit_mode']; //등록, 수정구분

	$page = $_POST['page'];

	$code   = $_POST['mCode']; //기관기호
	$id     = $_POST['logID']; //로그인ID
	$name   = $_POST['cName']; //기관명
	$domain = $myF->_domain();

	if (empty($id)) $id = $code;

	$id = strtolower($id);

	// 기관분류코드 및 승인번호
	if ($_POST['kind_1'] == 'Y'){
		$kind = 0;
		$code1 = $_POST['code0'];
	}else if ($_POST['kind_2_1'] == 'Y'){
		$kind = 1;
		$code1 = $_POST['code1'];
	}else if ($_POST['kind_2_2'] == 'Y'){
		$kind = 2;
		$code1 = $_POST['code2'];
	}else if ($_POST['kind_2_3'] == 'Y'){
		$kind = 3;
		$code1 = $_POST['code3'];
	}else if ($_POST['kind_2_4'] == 'Y'){
		$kind = 4;
		$code1 = $_POST['code4'];
	}else if ($_POST['kind_3'] == 'Y'){
		$kind = 5;
		$code1 = $_POST['code5'];
	}

	$code_2[0] = '';
	$code_2[1] = '';
	$code_2[2] = '';
	$code_2[3] = '';
	$code_2[4] = '';

	if ($_POST['kind_1'] == 'Y')   $code_2[0] = $_POST['code0']; //$code;
	if ($_POST['kind_2_1'] == 'Y') $code_2[1] = $_POST['code1'];
	if ($_POST['kind_2_2'] == 'Y') $code_2[2] = $_POST['code2'];
	if ($_POST['kind_2_3'] == 'Y') $code_2[3] = $_POST['code3'];
	if ($_POST['kind_2_4'] == 'Y') $code_2[4] = $_POST['code4'];
	if ($_POST['kind_3'] == 'Y')   $code_2[5] = $_POST['code5'];

	if ($host == 'admin' || $host == 'fr'){
		if ($code_2[0] == '' &&
			$code_2[1] == '' &&
			$code_2[2] == '' &&
			$code_2[3] == '' &&
			$code_2[4] == ''){
			$code_2[0] = $code;
		}
	}else if ($gDomain == 'kacold.net'){
		if (!$code_2[0]) $code_2[0] = $code;
	}else{
		if (!$code1) $code_2[0] = $code;
	}

	$cond_date = str_replace('-', '', $_POST['contDate']);
	if ($cond_date == ''){
		$cond_date = date('Ym', mkTime()).'01';
	}

	/*************************************

		이미지 저장

	*************************************/
		if (is_array($_FILES)){
			foreach($_FILES as $f => $file){
				$pic    = $file;
				$upload = false;

				if ($pic['tmp_name'] != ''){
					$tmp_info = pathinfo($pic['name']);
					$exp_nm = strtolower($tmp_info['extension']);

					if ($exp_nm == 'jpg' ||
						$exp_nm == 'png' ||
						$exp_nm == 'gif' ||
						$exp_nm == 'bmp'){
						$pic_nm = mktime().'.'.$exp_nm;
					}else{
						$pic_nm = '';
					}

					if (!empty($pic_nm)){
						if (move_uploaded_file($pic['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/mem_picture/'.$pic_nm)){
							// 업로드 성공
							$upload = true;
						}
					}
				}

				#######################################
				#
				# 이미지 축소
				/*
				if ($upload && $exp_nm != 'bmp'){
					$original_path = '../mem_picture/'.$pic_nm;
					$img_m = $f == 'icon' ? 32 : 142;
					$img_s = getimagesize($original_path);

					if ($img_s[0] > $img_m || $img_s[1] > $img_m){
						if ($img_s[0] > $img_s[1]){
							$img_r = $img_s[1] / $img_s[0];
							$img_w = $img_m;
							$img_h = $img_m * $img_r;
						}else{
							$img_r = $img_s[0] / $img_s[1];
							$img_h = $img_m;
							$img_w = $img_m * $img_r;
						}
					}else{
						$img_w = $img_s[0];
						$img_h = $img_s[1];
					}

					switch($exp_nm){
						case 'jpg':
							$original_img = imageCreateFromJpeg($original_path);
							break;
						case 'png':
							$original_img = imageCreateFromPng($original_path);
							break;
						case 'gif':
							$original_img = imageCreateFromGif($original_path);
							break;
						case 'bmp':
							$original_img = imageCreateFromBmp($original_path);
							break;
					}

					// 새 이미트 틀작성
					$new_img = imageCreateTrueColor($img_w, $img_h);

					// 배경을 하얀색으로 설정
					$trans_colour = imageColorAllocate($new_img, 255,255,255);
					imageFill($new_img, 0, 0, $trans_colour);

					// 투명
					//imageSaveAlpha($new_img, true);
					//$trans_colour = imageColorAllocateAlpha($new_img, 0, 0, 0, 127);
					//imageFill($new_img, 0, 0, $trans_colour);


					// 이미지 복사
					imageCopyReSampled($new_img, $original_img, 0, 0, 0, 0, $img_w, $img_h, $img_s[0], $img_s[1]);

					// 이미지 저장
					switch($exp_nm){
						case 'jpg':
							imageJpeg($new_img, $original_path);
							break;
						case 'png':
							imagePng($new_img, $original_path);
							break;
						case 'gif':
							imageGif($new_img, $original_path);
							break;
						case 'bmp':
							imageBmp($new_img, $original_path);
							break;
					}

					// 종료
					imageDestroy($new_img);
				}
				*/
				#
				#######################################

				// 업로드 실패시 파일명 삭제
				if ($upload)
					$picNm[$f] = $pic_nm;
				else
					$picNm[$f] = $_POST[$f.'_back'];
			}
		}
	/************************************/

	$conn->begin();

	// 재가요양 수정
	for($i=0; $i<=sizeOf($code_2); $i++){
		$code_value = $code_2[$i];
		$name_value = $_POST['cName'.$i];
		$j_date     = str_replace('-', '', $_POST['jDate'.$i]);

		if ($code_value != ''){
			$sql = "select count(*)
					  from m00center
					 where m00_mcode = '$code'
					   and m00_mkind = '$i'";
			if ($conn->get_data($sql) == 0){
				$sql = "insert into m00center (m00_mcode, m00_mkind, m00_code1, m00_cname, m00_jdate) values ('$code', '$i', '$code_value', '$name_value', '$j_date')";

				if (!$conn->execute($sql)){
					echo '
						<script>
							alert("기관저장중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.");
							history.back();
						</script>
						 ';
					exit;
				}
			}

			$salaryType = $_POST['salaryType'.$i].'/'.$_POST['salaryType'.$i.'_500'].'/'.$_POST['salaryType'.$i.'_800'];


			$sql = "update m00center
					   set m00_code1			  = '".$code_value."'
					,      m00_store_nm			  = '".$_POST['storeNm']."'
					,      m00_open_dt			  = '".$_POST['openDt']."'
					,      m00_cname			  = '".$name_value."'
					,      m00_mname			  = '".$_POST['mName']."'
					,      m00_ccode              = '".str_replace('-', '', $_POST['cCode'])."'
					,      m00_ctel				  = '".str_replace('-', '', $_POST['cTel'])."'
					,      m00_fax_no			  = '".str_replace('-', '', $_POST['faxNo'])."'
					,      m00_cpostno			  = '".$_POST['cPostNo']/*$_POST['cPostNo1'].$_POST['cPostNo2']*/."'
					,      m00_caddr1			  = '".$_POST['cAddr1']."'
					,      m00_caddr2			  = '".$_POST['cAddr2']."'
					,      m00_jdate			  = '".$j_date."'
					,      m00_kupyeo_1			  = '".$_POST['kupyeo1']."'
					,      m00_kupyeo_2			  = '".$_POST['kupyeo2']."'
					,      m00_kupyeo_3			  = '".$_POST['kupyeo3']."'
					,      m00_inwonsu			  = '".$_POST['inwonsu']."'
					,      m00_homepage			  = '".$_POST['homepage']."'
					,      m00_email			  = '".$_POST['email']."'
					,      m00_car_no1			  = '".$_POST['carNo1']."'
					,      m00_car_no2			  = '".$_POST['carNo2']."'
					,      m00_car_no3			  = '".$_POST['carNo3']."'
					,      m00_muksu_yul1		  = '".$_POST['sudangYul1']."'
					,      m00_muksu_yul2		  = '".$_POST['sudangYul2']."'
					,      m00_bank_no			  = '".$_POST['bankNo'.$i]."'
					,      m00_bank_name		  = '".$_POST['bankName'.$i]."'
					,      m00_bank_depos		  = '".$_POST['bankDepos'.$i]."'
					,      m00_bank_no_bath		  = '".$_POST['bankNo'.$i.'_500']."'
					,      m00_bank_name_bath	  = '".$_POST['bankName'.$i.'_500']."'
					,      m00_bank_depos_bath	  = '".$_POST['bankDepos'.$i.'_500']."'
					,      m00_bank_no_nurse	  = '".$_POST['bankNo'.$i.'_800']."'
					,      m00_bank_name_nurse	  = '".$_POST['bankName'.$i.'_800']."'
					,      m00_bank_depos_nurse	  = '".$_POST['bankDepos'.$i.'_800']."'
					,      m00_sudang_renew		  = '".$_POST['sudang_renew']."'
					,      m00_sudang_night		  = '".$_POST['sudang_night']."'
					,      m00_sudang_holiday	  = '".$_POST['sudang_holiday']."'
					,      m00_sudang_month		  = '".$_POST['sudang_month']."'
					,      m00_bath_add_yn        = '".$_POST['bath_add_yn']."'
					,      m00_nursing_add_yn     = '".$_POST['nursing_add_yn']."'
					,      m00_day_work_hour	  = '".$_POST['day_work_hour']."'
					,      m00_day_hourly         = '".str_replace(',', '', $_POST['day_hourly'])."'
					,      m00_law_holiday_yn	  = '".$_POST['law_holiday_yn']."'
					,      m00_law_holiday_pay_yn = '".$_POST['law_holiday_pay_yn']."'
					,	   m00_com_no             = '".str_replace('-', '', $_POST['comNo'])."'
					,      m00_salary_day         = '".$_POST['salaryDay']."'
					,      m00_weeklyin_yn        = '".$_POST['weeklyInYN']."'
					,      m00_annualin_yn        = '".($_POST['annualInYN'] == 'Y' ? 'Y' : 'N')."'
					,      m00_annual_yn          = '".(/*$_POST['annualInYN'] == 'Y' || */ $_POST['annualYN'] == 'Y' ? 'Y' : 'N')."'
					,      m00_fixed_days         = '".$_POST['fixedDays']."'
					";

			if ($_SESSION['userLevel'] == 'A' || $_SESSION['userLevel'] == 'B' || $_SESSION['userLevel'] == 'C'){
				$sql .= ", m00_domain = '".$domain."'";
			}

			if (!empty($picNm['icon'])){
				$sql .= ", m00_icon = '".$picNm['icon']."'";
			}

			if (!empty($picNm['jikin'])){
				$sql .= ", m00_jikin = '".$picNm['jikin']."'";
			}

			$sql .= ", m00_salary_type = '".$salaryType."'";

			$sql .=",      m00_del_yn             = 'N'
					 where m00_mcode = '$code'
					   and m00_mkind = '$i'";
		}else{
			$sql = "update m00center
					   set m00_del_yn = 'Y'
					 where m00_mcode  = '$code'
					   and m00_mkind  = '$i'";
		}

		if (!$conn->execute($sql)){
			echo '
				<script>
					alert(\'기관저장중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.\');
					//history.back();
				</script>
				 ';
			exit;
		}
	}

	//결제란 설정
	$signCnt = $_POST['cboSignCnt'];
	if ($signCnt != ''){
		$signNm	 = $_POST['txtSign1'].'|'.$_POST['txtSign2'].'|'.$_POST['txtSign3'].'|'.$_POST['txtSign4'].'|'.$_POST['txtSign5'];

		$sql = 'REPLACE INTO signline_set VALUES (
				 \''.$code.'\'
				,\''.$signCnt.'\'
				,\''.$signNm.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}else{
		$sql = 'DELETE
				FROM	signline_set
				WHERE	org_no = \''.$code.'\'';
	}

	$conn->execute($sql);

	$conn->commit();
	$conn->_logW();

	include_once("../inc/_db_close.php");
?>
<script>
	if ('<?=$conn->mode;?>' == '1'){
		alert('<?=$myF->message("ok","N");?>');
		location.replace('center_reg.php?mCode=<?=$code;?>&page=<?=$page;?>');
	}
</script>