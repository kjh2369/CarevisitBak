<?
	include("../inc/_db_open.php");
	include("../inc/_ed.php");
	include("../inc/_myFun.php");
?>
	<script src="../js/prototype.js" type="text/javascript"></script>
	<script src="../js/xmlHTTP.js" type="text/javascript"></script>
	<script src="../js/script.js" type="text/javascript"></script>
	<script src="../js/center.js" type="text/javascript"></script>
<?
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$host = $myF->host();

	if ($_POST["editMode"]){
		//등록
		$sql = "insert into m00center ("
			 . "  m00_mcode"
			 . ", m00_mkind"
			 . ", m00_code1"
			 . ", m00_cname"
			 . ", m00_mname"
			 . ", m00_mjumin"
			 . ", m00_ctel"
			 . ", m00_cpostno"
			 . ", m00_caddr1"
			 . ", m00_caddr2"
			 . ", m00_cdate"
			 . ", m00_jdate"
			 . ", m00_ccode"
			 . ", m00_kupyeo_gbn"
			 . ", m00_kupyeo_1"
			 . ", m00_kupyeo_2"
			 . ", m00_kupyeo_3"
			 . ", m00_sisel_gbn"
			 . ", m00_inwonsu"
			 . ", m00_homepage"
			 . ", m00_close_cond"
			 . ", m00_car_no1"
			 . ", m00_car_no2"
			 . ", m00_car_no3"
			 . ", m00_car_no4"
			 . ", m00_car_no5"
			 . ", m00_muksu_yul1"
			 . ", m00_muksu_yul2"
			 . ", m00_cont_date"
			 . ", m00_bank_no"
			 . ", m00_bank_name"
			 . ", m00_bank_depos"
			 . ", m00_sudang_renew"
			 . ", m00_sudang_night"
			 . ", m00_sudang_holiday"
			 . ", m00_sudang_month"
			 . ", m00_writer"
			 . ") values ("
			 . "  '".$_POST["mCode"]
			 . "','".$_POST["mKind"]
			 . "','".$_POST["code1"]
			 . "','".addslashes($_POST["cName"])
			 . "','".addslashes($_POST["mName"])
			 . "','".$_POST['mJumin1'].$_POST['mJumin2']
			 . "','".str_replace("-","",$_POST["cTel"])
			 . "','".$_POST["cPostNo1"].$_POST["cPostNo2"]
			 . "','".addslashes($_POST["cAddr1"])
			 . "','".addslashes($_POST["cAddr2"])
			 . "','".str_replace("-","",$_POST["cDate"])
			 . "','".str_replace("-","",$_POST["jDate"])
			 . "','".str_replace("-","",$_POST["cCode"])
			 . "','".$_POST["kupyeoGbn"]
			 . "','".$_POST["kupyeo1"]
			 . "','".$_POST["kupyeo2"]
			 . "','".$_POST["kupyeo3"]
			 . "','".$_POST["siselGbn"]
			 . "','".$_POST["inwonsu"]
			 . "','".addslashes($_POST["homepage"])
			 . "','".$_POST["closeCond"]
			 . "','".$_POST["carNo1"]
			 . "','".$_POST["carNo2"]
			 . "','".$_POST["carNo3"]
			 . "','".$_POST["carNo4"]
			 . "','".$_POST["carNo5"]
			 . "','".$_POST["sudangYul1"]
			 . "','".$_POST["sudangYul2"]
			 . "','".str_replace("-","",$_POST["contDate"])
			 . "','".$_POST["bankNo"]
			 . "','".$_POST["bankName"]
			 . "','".$_POST["bankDepos"]
			 . "','".$_POST["sudangRenew"]
			 . "','".$_POST["sudangNight"]
			 . "','".$_POST["sudangHoliday"]
			 . "','".$_POST["sudangMonth"]
			 . "','".$host
			 . "')";
	}else{
		//수정
		$sql = "update m00center"
			 . "   set m00_code1      = '".$_POST["code1"]
			 . "',     m00_cname      = '".addslashes($_POST["cName"])
			 . "',     m00_mname      = '".addslashes($_POST["mName"])
			 . "',     m00_mjumin     = '".$_POST['mJumin1'].$_POST['mJumin2']
			 . "',     m00_ctel       = '".str_replace("-","",$_POST["cTel"])
			 . "',     m00_cpostno    = '".$_POST["cPostNo1"].$_POST["cPostNo2"]
			 . "',     m00_caddr1     = '".addslashes($_POST["cAddr1"])
			 . "',     m00_caddr2     = '".addslashes($_POST["cAddr2"])
			 . "',     m00_cdate      = '".str_replace("-","",$_POST["cDate"])
			 . "',     m00_jdate      = '".str_replace("-","",$_POST["jDate"])
			 . "',     m00_ccode      = '".str_replace("-","",$_POST["cCode"])
			 . "',     m00_kupyeo_gbn = '".$_POST["kupyeoGbn"]
			 . "',     m00_kupyeo_1   = '".$_POST["kupyeo1"]
			 . "',     m00_kupyeo_2   = '".$_POST["kupyeo2"]
			 . "',     m00_kupyeo_3   = '".$_POST["kupyeo3"]
			 . "',     m00_sisel_gbn  = '".$_POST["siselGbn"]
			 . "',     m00_inwonsu    = '".$_POST["inwonsu"]
			 . "',     m00_homepage   = '".addslashes($_POST["homepage"])
			 . "',     m00_close_cond = '".$_POST["closeCond"]
			 . "',     m00_car_no1    = '".$_POST["carNo1"]
			 . "',     m00_car_no2    = '".$_POST["carNo2"]
			 . "',     m00_car_no3    = '".$_POST["carNo3"]
			 . "',     m00_car_no4    = '".$_POST["carNo4"]
			 . "',     m00_car_no5    = '".$_POST["carNo5"]
			 . "',     m00_muksu_yul1 = '".$_POST["sudangYul1"]
			 . "',     m00_muksu_yul2 = '".$_POST["sudangYul2"]
			 . "',     m00_cont_date  = '".str_replace("-","",$_POST["contDate"])
			 . "',     m00_bank_no    = '".$_POST["bankNo"]
			 . "',     m00_bank_name  = '".$_POST["bankName"]
			 . "',     m00_bank_depos = '".$_POST["bankDepos"]
			 . "',     m00_sudang_renew = '".$_POST["sudangRenew"]
			 . "',     m00_sudang_night = '".$_POST["sudangNight"]
			 . "',     m00_sudang_holiday = '".$_POST["sudangHoliday"]
			 . "',     m00_sudang_month = '".$_POST["sudangMonth"]
			 . "'"
			 . " where m00_mcode = '".$_POST["mCode"]
			 . "'  and m00_mkind = '".$_POST["mKind"]
			 . "'";
	}

	$conn->query($sql);

	// 배상책임 보험사 등록
	if ($_POST["insName"] != ''){
		/*
		$sql = "select count(*)
				  from g02inscenter
				 where g02_ccode = '".$_POST["mCode"]."'
				   and g02_mkind = '".$_POST["mKind"]."'
				   and g02_year  = '".subStr($_POST["insFromDate"], 0, 4)."'";
		if ($conn->get_data($sql) == 0){
		*/
			$sql = "replace into g02inscenter values (
					 '".$_POST["mCode"]."'
					,'".$_POST["mKind"]."'
					,'".$_POST["insName"]."'
					,'".str_replace("-", "", $_POST["insFromDate"])."'
					,'".str_replace("-", "", $_POST["insToDate"])."')";
			$conn->execute($sql);
		//}
	}

	/*
	$insCheck = $_POST["insCheck"];
	$insCount = sizeOf($insCheck);
	$seq = 0;

	if ($insCount > 0){
		// 보험 가입신청서 저장
		$conn->begin();
		for($i=0; $i<$insCount; $i++){
			$id = $insCheck[$i];
			$sql = "select m02_yname, m02_yipsail
					  from m02yoyangsa
					 where m02_ccode = '".$_POST["mCode"]."'
					   and m02_mkind = '".$_POST["mKind"]."'
					   and m02_yjumin = '".$ed->de($_POST["jumin"][$id])."'";
			$yoyArray = $conn->get_array($sql);

			$date = date("Ymd", mkTime());
			$jumin = $ed->de($_POST["jumin"][$id]);
			$price = str_replace(",", "", $_POST["insMemberPrice"]);
			$fromDate = str_replace("-", "", $_POST["startDate"][$id]);
			$toDate = str_replace("-", "", $_POST["insToDate"]);
			$dateDiff = $myF->dateDiff("d", $myF->dateStyle($fromDate), $myF->dateStyle($toDate));

			$price = $price * (365 - $dateDiff) / 365;

			$sql = "insert into g05insrequest values (
					 '".$_POST["mCode"]."'
					,'".$_POST["mKind"]."'
					,'".$date."'
					,'".$jumin."'
					,'".$yoyArray[0]."'
					,'".$yoyArray[1]."'
					,'".$_POST["insName"]."'
					,'".$_POST["insItem"]."'
					,'".$price."'
					,'".$fromDate."'
					,'".$toDate."'
					,'1')";
			if ($conn->execute($sql)){
				// 입출력 히스토리
				if ($seq == 0){
					$month = intVal(date("m", mkTime()));

					if ($month >= 1 && $month <=3){
						$quarter = 1;
					}else if ($month >= 4 && $month <=6){
						$quarter = 2;
					}else if ($month >= 7 && $month <=9){
						$quarter = 3;
					}else{
						$quarter = 4;
					}

					$sql = "select ifnull(max(g06_seq), 0)
							  from g06insaccount
							 where g06_ccode = '".$_POST["mCode"]."'
							   and g06_mkind = '".$_POST["mKind"]."'
							   and g06_year = '".$date."'
							   and g06_quarter = '".$quarter."'";
					$seq = $conn->get_data($sql);
				}

				$seq ++;

				$sql = "insert into g06insaccount values (
						 '".$_POST["mCode"]."'
						,'".$_POST["mKind"]."'
						,'".date("Y", mkTime())."'
						,'".$quarter."'
						,'".$seq."'
						,'".$date."'
						,'".$jumin."'
						,'1'
						,'".$price."'
						,'N'
						,'')";
				if (!$conn->execute($sql)){
					$conn->rollback();
					break;
				}
			}
		}
		$conn->commit();
	}
	*/

	$sql = "select count(*)"
		 . "  from m97user"
		 . " where m97_user = '".$_POST["mCode"]
		 . "'";
	$conn->query($sql);
	$row = $conn->fetch();

	if ($row[0] == 0){
		$sql = "insert into m97user ("
			 . "  m97_user"
			 . ", m97_pass"
			 . ") values ("
			 . "  '".$_POST["mCode"]
			 . "','1111')";
		$conn->query($sql);
	}
	$conn->row_free();

	// 수가확인
	$sql = "select count(*)
			  from m01suga
			 where m01_mcode = '".$_POST["mCode"]."'";
	if ($conn->get_data($sql) == 0){
		$conn->begin();
		$sql = 'insert into m01suga (m01_mcode, m01_mcode2, m01_scode, m01_suga_cont, m01_suga_value, m01_suga_value15, m01_suga_value75, m01_suga_cvalue1, m01_suga_cvalue2, m01_suga_cvalue3, m01_calc_time, m01_sdate, m01_edate, m01_rate) '
			 . 'select \''.$_POST['mCode'].'\', m01_mcode2, m01_scode, m01_suga_cont, m01_suga_value, m01_suga_value15, m01_suga_value75, m01_suga_cvalue1, m01_suga_cvalue2, m01_suga_cvalue3, m01_calc_time, date_format(now(), \'%Y%m%d\'), \'99999999\', m01_rate'
			 . '  from m01suga'
			 . ' where m01_mcode = \'goodeos\'';
		$conn->execute($sql);
		$conn->commit();
	}

	$conn->close();

	echo "<script>location.replace('../main/main.php?gubun=centerReg&mCode=".$_POST["mCode"]."&mKind=".$_POST["mKind"]."');</script>";
?>