<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$host = $myF->host();

	$edit_mode = $_POST['edit_mode']; //등록, 수정구분

	$page = $_POST['page'];

	$code = $_POST['mCode'];	//기관기호
	$name = $_POST['cName'];	//기관명

	// 기관분류코드 및 승인번호
	if ($_POST['kind_1'] == 'Y'){
		$kind = 0;
		$code1 = $_POST['code0'];
	}else if ($_POST['kind_2'] == 'Y'){
		if ($_POST['kind_2_1'] == 'Y'){
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
		}
	}else if ($_POST['kind_3'] == 'Y'){
		$kind = 5;
		$code1 = $_POST['code5'];
	}

	$code_2[0] = '';
	$code_2[1] = '';
	$code_2[2] = '';
	$code_2[3] = '';
	$code_2[4] = '';

	if ($_POST['kind_1'] == 'Y') $code_2[0] = $_POST['code0']; //$code;
	if ($_POST['kind_2'] == 'Y'){
		if ($_POST['kind_2_1'] == 'Y') $code_2[1] = $_POST['code1'];
		if ($_POST['kind_2_2'] == 'Y') $code_2[2] = $_POST['code2'];
		if ($_POST['kind_2_3'] == 'Y') $code_2[3] = $_POST['code3'];
		if ($_POST['kind_2_4'] == 'Y') $code_2[4] = $_POST['code4'];
	}
	if ($_POST['kind_3'] == 'Y') $code_2[5] = $_POST['code5'];

	$cond_date = str_replace('-', '', $_POST['contDate']);
	if ($cond_date == ''){
		$cond_date = date('Ym', mkTime()).'01';
	}

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

			$sql = "update m00center
					   set m00_code1			= '".$code_value."'
					,      m00_cname			= '".$name_value."'
					,      m00_mname			= '".$_POST['mName']."'
					,      m00_ccode            = '".str_replace('-', '', $_POST['cCode'])."'
					,      m00_ctel				= '".str_replace('-', '', $_POST['cTel'])."'
					,      m00_cpostno			= '".$_POST['cPostNo1'].$_POST['cPostNo2']."'
					,      m00_caddr1			= '".$_POST['cAddr1']."'
					,      m00_caddr2			= '".$_POST['cAddr2']."'
					,      m00_cdate			= '".str_replace('-', '', $_POST['cDate'])."'
					,      m00_jdate			= '".$j_date."'
					,      m00_kupyeo_1			= '".$_POST['kupyeo1']."'
					,      m00_kupyeo_2			= '".$_POST['kupyeo2']."'
					,      m00_kupyeo_3			= '".$_POST['kupyeo3']."'
					,      m00_inwonsu			= '".$_POST['inwonsu']."'
					,      m00_homepage			= '".$_POST['homepage']."'
					,      m00_car_no1			= '".$_POST['carNo1']."'
					,      m00_car_no2			= '".$_POST['carNo2']."'
					,      m00_car_no3			= '".$_POST['carNo3']."'
					,      m00_muksu_yul1		= '".$_POST['sudangYul1']."'
					,      m00_muksu_yul2		= '".$_POST['sudangYul2']."'
					,      m00_bank_no			= '".$_POST['bankNo']."'
					,      m00_bank_name		= '".$_POST['bankName']."'
					,      m00_bank_depos		= '".$_POST['bankDepos']."'
					,      m00_sudang_renew		= '".$_POST['sudang_renew']."'
					,      m00_sudang_night		= '".$_POST['sudang_night']."'
					,      m00_sudang_holiday	= '".$_POST['sudang_holiday']."'
					,      m00_sudang_month		= '".$_POST['sudang_month']."'
					,	   m00_cont_date        = '".$cond_date."'
					,      m00_bath_add_yn      = '".$_POST['bath_add_yn']."'
					,      m00_nursing_add_yn   = '".$_POST['nursing_add_yn']."'
					,      m00_day_work_hour			= '".$_POST['day_work_hour']."'
					,      m00_day_hourly               = '".str_replace(',', '', $_POST['day_hourly'])."'
					,      m00_law_holiday_yn			= '".$_POST['law_holiday_yn']."'
					,      m00_law_holiday_pay_yn		= '".$_POST['law_holiday_pay_yn']."'
					,      m00_del_yn           = 'N'
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
					alert("기관저장중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.");
					history.back();
				</script>
				 ';
			exit;
		}
	}

	// 배상책임 보험사 등록
	if ($_POST["insName"] != ''){
		$sql = "replace into g02inscenter values (
				 '$code'
				,'0'
				,'".$_POST["insName"]."'
				,'".str_replace("-", "", $_POST["insFromDate"])."'
				,'".str_replace("-", "", $_POST["insToDate"])."')";
		$conn->execute($sql);
	}else{
		$sql = "update g02inscenter
				   set g02_ins_code = '0'
				,      g02_ins_from_date = ''
				,      g02_ins_to_date   = ''
				 where g02_ccode = '$code'
				   and g02_mkind = '0'";
		$conn->execute($sql);
	}

	$sql = "select count(*)
			  from m97user
			 where m97_user = '$code'";
	if ($conn->get_data($sql) == 0){
		$sql = "insert into m97user (
				m97_user
			   ,m97_pass) values (
			    '$code'
			   ,'1111')";
		$conn->execute($sql);
	}

	// 수가확인
	$sql = "select count(*)
			  from m01suga
			 where m01_mcode = '$code'";
	if ($conn->get_data($sql) == 0){
		$conn->begin();
		$sql = 'insert into m01suga (m01_mcode, m01_mcode2, m01_scode, m01_suga_cont, m01_suga_value, m01_suga_value15, m01_suga_value75, m01_suga_cvalue1, m01_suga_cvalue2, m01_suga_cvalue3, m01_calc_time, m01_sdate, m01_edate, m01_rate) '
			 //. 'select \''.$code.'\', m01_mcode2, m01_scode, m01_suga_cont, m01_suga_value, m01_suga_value15, m01_suga_value75, m01_suga_cvalue1, m01_suga_cvalue2, m01_suga_cvalue3, m01_calc_time, date_format(now(), \'%Y%m%d\'), \'99999999\', m01_rate'
			 . 'select \''.$code.'\', m01_mcode2, m01_scode, m01_suga_cont, m01_suga_value, m01_suga_value15, m01_suga_value75, m01_suga_cvalue1, m01_suga_cvalue2, m01_suga_cvalue3, m01_calc_time, \'20100101\', \'99999999\', m01_rate'
			 . '  from m01suga'
			 . ' where m01_mcode = \'goodeos\'';
		$conn->execute($sql);
		$conn->commit();
	}
	$conn->close();
?>
<script>
	alert('<?=$myF->message("ok","N");?>');
	location.replace('reg.php?mCode=<?=$code;?>&page=<?=$page;?>');
</script>