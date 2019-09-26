<?
	include("../inc/_db_open.php");
?>
	<script src="../js/prototype.js" type="text/javascript"></script>
	<script src="../js/xmlHTTP.js" type="text/javascript"></script>
	<script src="../js/script.js" type="text/javascript"></script>
	<script src="../js/center.js" type="text/javascript"></script>
<?
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$conn->begin();

	if ($_SESSION["userLevel"] == "A"){
		$mCode = $_POST["curMcode"];
		$mKind = $_POST["curMkind"];
	}else{
		$mCode = $_POST["curMcode"];
		$mKind = $_POST["currentMkind"];
	}

	if ($_POST["editMode"]){
		//등록
		$sql = "select ifnull(max(m02_key), 0)+1"
			 . "  from m02yoyangsa"
			 . " where m02_ccode  = '".$mCode
			 . "'  and m02_mkind  = '".$mKind
			 . "'";
		$conn->query($sql);
		$row = $conn->fetch();
		$key = $row[0];

		$sql = "insert into m02yoyangsa ("
			 . "  m02_key"
			 . ", m02_ccode"
			 . ", m02_mkind"
			 . ", m02_yjumin"
			 . ") values ("
			 . "  '".$key
			 . "','".$mCode
			 . "','".$mKind
			 . "','".$_POST["yJumin1"].$_POST["yJumin2"]
			 . "')";
		$conn->execute($sql);
	}else{
		//수정
		$key = $_POST["key"];
	}

	// 시급 및 서비스건별일경우 고정급여부
	$gupyeoType = $_POST['gupyeoType'] == 'Y' ? 'Y' : 'N';

	// 급여산정방식
	if ($_POST["yGupyeoKind"] == '1' || $_POST["yGupyeoKind"] == '2'){
		if ($gupyeoType != 'Y'){
			$payType = '1';
		}else{
			$payType = '2';
		}
	}else if ($_POST["yGupyeoKind"] == '3'){
		$payType = '3';
	}else if ($_POST["yGupyeoKind"] == '4'){
		$payType = '4';
	}else{
		$payType = '1';
	}

	switch($payType){
		case '1':
			$gibonKup = str_replace(",", "", $_POST["yGibonKup"][0]);
			$gibonYul = '0';
			break;
		case '2':
			$gibonKup = str_replace(",", "", $_POST["yGibonKup2"]);
			$gibonYul = '0';
			break;
		case '3':
			$gibonKup = str_replace(",", "", $_POST["yGibonKup3"]);
			$gibonYul = '0';
			break;
		case '4':
			$gibonKup = '0';
			$gibonYul = $_POST["ySugaYoyul"];
			break;
	}

	/*
	if ($gupyeoType == 'Y'){
		$gibonKup = str_replace(",", "", $_POST['yGibonKupM']);
	}else{
		$gibonKup = str_replace(",", "", $_POST["yGibonKup"][0]);
	}
	*/

	if ($_POST["y4BohumUmu"] == 'Y'){
		$bohum1 = 'Y';
		$bohum2 = 'Y';
		$bohum3 = 'Y';
		$bohum4 = 'Y';
	}else{
		$bohum1 = $_POST["yGoBohumUmu"];
		$bohum2 = $_POST["ySnBohumUmu"];
		$bohum3 = $_POST["yGnBohumUmu"];
		$bohum4 = $_POST["yKmBohumUmu"];
	}

	if ($_POST["insCode"] > 0 && $_POST["insItemCode"] > 0){
		$insYN = $_POST["insYN"];
		$insCode = $_POST["insCode"];
		$insItemCode = $_POST["insItemCode"];
		$insPrice = $_POST["insPrice"];
		$insFromDate = $_POST["insFromDate"];
		$insToDate = $_POST["insToDate"];
	}else{
		$insYN = "N";
		$insCode = 0;
		$insItemCode = 0;
		$insPrice = 0;
		$insFromDate = "";
		$insToDate = "";
	}

	// 기관의 인원수
	$sql = "select count(*)
			  from m02yoyangsa
			 where m02_ccode = '$mCode'
			   and m02_mkind = '$mKind'
			   and m02_ygoyong_stat = '1'";
	$memberCount = $conn->get_data($sql);

	$sql = "update m00center
			   set m00_inwonsu = '$memberCount'
			 where m00_mcode = '$mCode'
			   and m00_mkind = '$mKind'";
	$conn->execute($sql);

	$sql = "update m02yoyangsa"
		 . "   set m02_ytel           = '".str_replace("-", "", $_POST["yTel"])
		 . "',     m02_yname          = '".$_POST["yName"]
		 . "',     m02_ycode          = '".str_replace("-", "", $_POST["yCode"])
		 . "',     m02_yjimun         = '".$_POST["yJimun"]
		 . "',     m02_sign1          = '".$_POST["sign1"]
		 . "',     m02_sign2          = '".$_POST["sign2"]
		 . "',     m02_yinsign1       = '".$_POST["yInSign1"]
		 . "',     m02_yinsign2       = '".$_POST["yInSign2"]
		 . "',     m02_yindate1       = '".str_replace("-", "", $_POST["yInDate1"])
		 . "',     m02_yindate2       = '".str_replace("-", "", $_POST["yInDate2"])
		 . "',     m02_ydwndate       = '".str_replace("-", "", $_POST["yDwnDate"])
		 . "',     m02_ygunmu_kind    = '".$_POST["yGunmuKind"]
		 . "',     m02_yjikjong       = '".$_POST["yJikJong"]
		 . "',     m02_yjakuk_kind    = '".$_POST["yJakukKind"]
		 . "',     m02_yjagyuk_no     = '".$_POST["yJagyukNo"]
		 . "',     m02_yjakuk_date    = '".str_replace("-", "", $_POST["yJakukDate"])
		 . "',     m02_ygyeongyuk     = '".$_POST["yGyeongyuk"]
		 . "',     m02_ytel2          = '".str_replace("-", "", $_POST["yTel2"])
		 . "',     m02_ypostno        = '".$_POST["yPostNo1"].$_POST["yPostNo2"]
		 . "',     m02_yjuso1         = '".$_POST["yJuso1"]
		 . "',     m02_yjuso2         = '".$_POST["yJuso2"]
		 . "',     m02_ygoyong_kind   = '".$_POST["yGoyongKind"]
		 . "',     m02_ygoyong_stat   = '".$_POST["yGoyongStat"]
		 . "',     m02_yipsail        = '".str_replace("-", "", $_POST["yIpsail"])
		 . "',     m02_ytoisail       = '".str_replace("-", "", $_POST["yToisail"])
		 . "',     m02_ygunmu_mon     = '".$_POST["yGunmuMon"]
		 . "',     m02_ygunmu_tue     = '".$_POST["yGunmuTue"]
		 . "',     m02_ygunmu_wed     = '".$_POST["yGunmuWed"]
		 . "',     m02_ygunmu_thu     = '".$_POST["yGunmuThu"]
		 . "',     m02_ygunmu_fri     = '".$_POST["yGunmuFri"]
		 . "',     m02_ygunmu_sat     = '".$_POST["yGunmuSat"]
		 . "',     m02_ygunmu_sun     = '".$_POST["yGunmuSun"]
		 . "',     m02_ygunmu_cond    = '".$_POST["yGunmuCond"]
		 . "',     m02_ygupyeo_kind   = '".$_POST["yGupyeoKind"]
		 . "',     m02_ygibonkup      = '".$gibonKup //str_replace(",", "", $_POST["yGibonKup"][0])
		 . "',     m02_ysugang        = '".str_replace(",", "", $_POST["ySudang"])
		 . "',     m02_ygyeoja_no     = '".$_POST["yGyeojaNo"]
		 . "',     m02_ybank_name     = '".$_POST["yBankName"]
		 . "',     m02_y4bohum_umu    = '".$_POST["y4BohumUmu"]
		 . "',     m02_ygobohum_umu   = '".$bohum1 //$_POST["yGoBohumUmu"]
		 . "',     m02_ysnbohum_umu   = '".$bohum2 //$_POST["ySnBohumUmu"]
		 . "',     m02_ygnbohum_umu   = '".$bohum3 //$_POST["yGnBohumUmu"]
		 . "',     m02_ykmbohum_umu   = '".$bohum4 //$_POST["yKmBohumUmu"]
		 . "',     m02_ygongjeja_no   = '".$_POST["yGongJeJaNo"]
		 . "',     m02_ygongjejaye_no = '".$_POST["yGongJeJayeNo"]
		 . "',     m02_ysuga_yoyul    = '".$gibonYul //$_POST["ySugaYoyul"]
		 . "',     m02_yjikgup_sudang = '".$_POST["yJikgupSudand"]
		 . "',     m02_yfamcare_umu   = '".$_POST["yFamCareUmu"]
		 . "',     m02_yfamcare_pay   = '".str_replace(",", "", $_POST["yFamCarePay"])
		 . "',     m02_ybsbohum_name  = '".$_POST["yBsBohumName"]
		 . "',     m02_ybsbohum_fm    = '".str_replace("-", "", $_POST["yBsBohumFm"])
		 . "',     m02_ybsbohum_to    = '".str_replace("-", "", $_POST["yBsBohumTo"])
		 . "',     m02_ybsbohum_amt   = '".$_POST["yBsBohumAmt"]
		 . "',     m02_ybsbohum_umu   = '".$_POST["yBsBohumUmu"]
		 . "',     m02_ykuksin_mpay   = '".str_replace(",", "", $_POST["yKuksinMpay"])
		 . "',     m02_ysowon_gbn     = '".$_POST["ySowonGbn"]
		 . "',     m02_jikwon_gbn     = '".$_POST["jikwonGbn"]
		 . "',     m02_pay_type       = '".$gupyeoType
		 . "',     m02_ins_yn         = '".$_POST['insYN']."'
			 ,     m02_ins_code       = '".($_POST['insYN'] == 'Y' ? $_POST['insCode'] : '0')."'
			 ,     m02_ins_from_date  = '".($_POST['insYN'] == 'Y' ? str_replace('-', '', $_POST['insFromDate']) : '')."'
			 ,     m02_ins_to_date    = '".($_POST['insYN'] == 'Y' ? str_replace('-', '', $_POST['insToDate']) : '')."'"
		 . " where m02_ccode  = '".$mCode
		 . "'  and m02_mkind  = '".$mKind
		 . "'  and m02_yjumin = '".$_POST["yJumin1"].$_POST["yJumin2"]
		 . "'";
	$conn->execute($sql);

	// 시급저장
	$sql = "delete"
		 . "  from m02pay"
		 . " where m02_ccode = '".$mCode
		 . "'  and m02_mkind = '".$mKind
		 . "'  and m02_jumin = '".$_POST["yJumin1"].$_POST["yJumin2"]
		 . "'";
	$conn->execute($sql);

	$payCount = sizeOf($_POST["yGibonKup"]);

	for($i=0; $i<$payCount; $i++){
		switch($payType){
		case '1':
			$gibonKup = str_replace(",", "", $_POST["yGibonKup"][$i]);
			break;
		case '2':
			$gibonKup = str_replace(",", "", $_POST["yGibonKup2"]);
			break;
		case '3':
			$gibonKup = '0';
			break;
		case '4':
			$gibonKup = '0';
			break;
		}

		$sql = "insert into m02pay ("
			 . " m02_ccode"
			 . ",m02_mkind"
			 . ",m02_jumin"
			 . ",m02_gubun"
			 . ",m02_pay"
			 . ") values ("
			 . "  '".$mCode
			 . "','".$mKind
			 . "','".$_POST["yJumin1"].$_POST["yJumin2"]
			 . "','".$_POST["yGibonKupCode"][$i]
			 . "','".$gibonKup
			 . "')";
		$conn->execute($sql);
	}

	$yoyCode = $_POST["yJumin1"].$_POST["yJumin2"];

	// 급여 고정급
	$sql = "delete
			  from t25payfix
			 where t25_ccode = '$mCode'
			   and t25_mkind = '$mKind'
			   and t25_yoy_code = '$yoyCode'";
	$conn->execute($sql);

	for($i=1; $i<=5; $i++){
		if ($i == 1 || $i == 2){
			$id1 = 1;
			if ($i == 1) $id2 = 0;
		}else{
			$id1 = 2;
			if ($i == 3) $id2 = 0;
		}
		$id2 ++;
		for($j=1; $j<=10; $j++){
			$id3 = ($j<10?'0':'').$j;
			if ($_POST['pay_'.$id1.'_'.$id2.'_'.$id3] != ''){
				$sql = "insert into t25payfix values (
						 '$mCode'
						,'$mKind'
						,'$yoyCode'
						,'$id1'
						,'$id2'
						,'$id3'
						,'".str_replace(",", "", $_POST['pay_'.$id1.'_'.$id2.'_'.$id3])."')";
				$conn->execute($sql);
			}
		}
	}

	if ($_POST['insYN'] == 'Y' && $_POST['insNo'] == ''){
		// 배상책임보험 가입신청
		$sql = "select count(*)
				  from g03insapply
				 where g03_ins_code      = '".$_POST['insCode']."'
				   and g03_ins_from_date = '".str_replace('-', '', $_POST['insFromDate'])."'
				   and g03_jumin         = '".$_POST["yJumin1"].$_POST["yJumin2"]."'";
		//echo $sql.'<br>';
		if ($conn->get_data($sql) == 0){
			$sql = "insert into g03insapply values (
					 '".$_POST['insCode']."'
					,'".str_replace('-', '', $_POST['insFromDate'])."'
					,''
					,'".$mCode."'
					,'".$mKind."'
					,'".$_POST["yJumin1"].$_POST["yJumin2"]."'
					,'".$_POST["yName"]."'
					,'1'
					,'')";
			$conn->execute($sql);
			//echo $sql.'<br>';
		}
	}else if ($_POST['insYN'] == 'N'){
		// 배상책임보험 해지
		$sql = "update g03insapply
				   set g03_ins_to_date = '".str_replace('-', '', $_POST['insToDate'])."'
				,      g03_ins_stat    = '7'
				 where g03_ins_code      = '".$_POST['insCode']."'
				   and g03_ins_from_date = '".str_replace('-', '', $_POST['insFromDate'])."'
				   and g03_jumin         = '".$_POST["yJumin1"].$_POST["yJumin2"]."'
				   and (g03_ins_stat = '1' or g03_ins_stat = '2')";
		$conn->execute($sql);
		//echo $sql.'<br>';
	}

	$conn->commit();
	$conn->close();

	//echo "<script>location.replace('../main/main.php?gubun=yoyangsaReg&mCode=".$mCode."&mKind=".$mKind."&mKey=".$key."');</script>";
	echo "<script>location.replace('../yoyangsa/yoyangsa.php?gubun=reg&mCode=$mCode&mKind=$mKind&mKey=$key');</script>";
?>