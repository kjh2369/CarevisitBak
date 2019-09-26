<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$conn->begin();

	$mode		= $_POST['mode'];		//작업구분 1:등록, 2:수정
	$code		= $_POST['code'];		//기관코드
	$kind		= $_POST['kind'];		//기관분류코드
	$kind_temp	= $_POST['kind_temp'];
	$kind_list	= $_POST['kind_list'];	//기관리스트
	$kind_count = sizeof($kind_temp);	//기관리스트갯수

	// 주민번호
	if ($mode == 1){
		$jumin	= $_POST['yJumin1'].$_POST['yJumin2'];
	}else{
		$jumin	= $ed->de($_POST['jumin']);
	}

	if ($mode == 1){ //등록
		// 다음 키
		$sql = "select ifnull(max(m02_key), 0) + 1
				  from m02yoyangsa
				 where m02_ccode = '$code'
				   and m02_mkind = '$kind'";
		$key = $conn->get_data($sql);
	}else{
		$sql = "select m02_key
				  from m02yoyangsa
				 where m02_ccode  = '$code'
				   and m02_mkind  = '$kind'
				   and m02_yjumin = '$jumin'";
		$key = $conn->get_data($sql);
	}

	for($i=0; $i<$kind_count; $i++){
		$kind_code  = $kind_temp[$i];
		$kind_exist = false;

		for($j=0; $j<sizeof($kind_list); $j++){
			if ($kind_code == $kind_list[$j]){
				$kind_exist = true;
				break;
			}
		}

		if ($kind_exist){
			$sql = "select count(*)
					  from m02yoyangsa
					 where m02_ccode  = '$code'
					   and m02_mkind  = '$kind_code'
					   and m02_yjumin = '$jumin'";
			if ($conn->get_data($sql) == 0){
				// 저장
				$sql = "insert into m02yoyangsa (m02_ccode, m02_mkind, m02_yjumin, m02_key) values ('$code', '$kind_code', '$jumin', '$key')";
				$conn->execute($sql);
			}

			// 스마트업구구분
			if ($_POST['jikwonGbnM'] == 'Y' && $_POST['jikwonGbnY'] == 'Y'){
				$smart_gbn  = 'A';	//관리자+요양보호사
			}else if ($_POST['jikwonGbnM'] == 'Y'){
				$smart_gbn  = 'M';	//관리자
			}else if ($_POST['jikwonGbnY'] == 'Y'){
				$smart_gbn  = 'Y';	//요양보호사
			}else{
				$smart_gbn  = ' ';	//미사용
			}

			// 시급고정급여부
			if ($_POST['yGupyeoKind'] == '1Y'){
				$pay_kind = '1';
				$pay_type = 'Y';
				$pay_basic = $_POST['yGibonKup1'];
				$pay_rate  = 0;
			}else if ($_POST['yGupyeoKind'] == '1N'){
				$pay_kind = '1';
				$pay_type = 'N';
				$pay_basic = $_POST['yGibonKup'][0];
				$pay_rate  = 0;
			}else if ($_POST['yGupyeoKind'] == '3'){
				$pay_kind = '3';

				// 포괄임금제 적용여부
				if ($_POST['yGibonKupCom'] == 'Y'){
					$pay_type = 'Y';
				}else{
					$pay_type = ' ';
				}
				$pay_basic = $_POST['yGibonKup3'];
				$pay_rate  = 0;
			}else if ($_POST['yGupyeoKind'] == '4'){
				$pay_kind = '4';
				$pay_type = ' ';
				$pay_basic = 0;
				$pay_rate  = $_POST['ySugaYoyul'];
			}else{
				$pay_kind = '0';
				$pay_type = ' ';
				$pay_basic = 0;
				$pay_rate  = 0;
			}

			// 동거케어 시급고정급여부
			if ($_POST['yFamCareType'] == 'Y'){
				$famcare_type = '1';
				$famcare_umu = 'Y';
				$famcare_pay = $_POST['yFamCarePay1'];
			}else if ($_POST['yFamCareType'] == '2Y'){
				$famcare_type = '2';
				$famcare_umu = 'Y';
				$famcare_pay = $_POST['yFamCarePay2'];

			}else if ($_POST['yFamCareType'] == '3Y'){
				$famcare_type = '3';
				$famcare_umu = 'Y';
				$famcare_pay = $_POST['yFamCarePay3'];
			}else{
				$famcare_type = '1';
				$famcare_umu = 'N';
				$famcare_pay = 0;
			}

			$sql = "update m02yoyangsa
					   set m02_yname			= '".$_POST['yName']."'
					,      m02_ytel				= '".str_replace('-', '', $_POST['yTel'])."'
					,      m02_ytel2			= '".str_replace('-', '', $_POST['yTel2'])."'
					,      m02_ypostno			= '".$_POST['yPostNo1'].$_POST['yPostNo2']."'
					,      m02_yjuso1			= '".$_POST['yJuso1']."'
					,      m02_yjuso2			= '".$_POST['yJuso2']."'
					,      m02_yjakuk_kind		= '".$_POST['yJakukKind']."'
					,      m02_yjagyuk_no		= '".$_POST['yJagyukNo']."'
					,      m02_yjakuk_date		= '".str_replace('-', '', $_POST['yJakukDate'])."'
					,      m02_yjikjong			= '".$_POST['yJikJong']."'
					,      m02_ybank_name		= '".$_POST['yBankName']."'
					,      m02_ygyeoja_no		= '".$_POST['yGyeojaNo']."'
					,      m02_y4bohum_umu		= '".$_POST['y4BohumUmu']."'
					,      m02_ygobohum_umu		= '".$_POST['y4BohumUmu']."'
					,      m02_ysnbohum_umu		= '".$_POST['y4BohumUmu']."'
					,      m02_ygnbohum_umu		= '".$_POST['y4BohumUmu']."'
					,      m02_ykmbohum_umu		= '".$_POST['y4BohumUmu']."'
					,      m02_ygongjeja_no		= '".$_POST['yGongJeJaNo']."'
					,      m02_ygongjejaye_no	= '".$_POST['yGongJeJayeNo']."'
					,      m02_ykuksin_mpay		= '".str_replace(',', '', $_POST['yKuksinMpay'])."'
					,      m02_health_mpay		= '".str_replace(',', '', $_POST['yHealthMpay'])."'
					,      m02_employ_mpay		= '".str_replace(',', '', $_POST['yEmployMpay'])."'
					,      m02_sanje_mpay		= '".str_replace(',', '', $_POST['ySanjeMpay'])."'
					,      m02_jikwon_gbn		= '".$smart_gbn."'
					,      m02_ygoyong_kind		= '".$_POST['yGoyongKind']."'
					,      m02_ygoyong_stat		= '".$_POST['yGoyongStat']."'
					,      m02_yipsail			= '".str_replace('-', '', $_POST['yIpsail'])."'
					,      m02_ytoisail			= '".str_replace('-', '', $_POST['yToisail'])."'
					,      m02_ygunmu_mon		= '".$_POST['yGunmuMon']."'
					,      m02_ygunmu_tue		= '".$_POST['yGunmuTue']."'
					,      m02_ygunmu_wed		= '".$_POST['yGunmuWed']."'
					,      m02_ygunmu_thu		= '".$_POST['yGunmuThu']."'
					,      m02_ygunmu_fri		= '".$_POST['yGunmuFri']."'
					,      m02_ygunmu_sat		= '".$_POST['yGunmuSat']."'
					,      m02_ygunmu_sun		= '".$_POST['yGunmuSun']."'
					,      m02_ygupyeo_kind		= '".$pay_kind."'
					,      m02_pay_type			= '".$pay_type."'
					,      m02_ygibonkup		= '".str_replace(',', '', $pay_basic)."'
					,      m02_ysuga_yoyul		= '".$pay_rate."'
					,      m02_yfamcare_umu		= '".$famcare_umu."'
					,      m02_yfamcare_pay		= '".str_replace(',', '', $famcare_pay)."'
					,      m02_yfamcare_type    = '".$famcare_type."'
					,      m02_ins_yn			= '".$_POST['insYN']."'
					,      m02_ins_code			= '".$_POST['ins_code']."'
					,      m02_ins_from_date	= '".str_replace('-', '', $_POST['insFromDate'])."'
					,      m02_ins_to_date		= '".str_replace('-', '', $_POST['insToDate'])."'
					,      m02_rank_pay         = '".str_replace(',', '', $_POST['rank_pay'])."'
					,      m02_add_payrate      = '".str_replace(',', '', $_POST['addPayRate'])."'
					,      m02_bnpay_yn         = '".$_POST['ybnpay']."'
					,      m02_del_yn           = 'N'
					 where m02_ccode			= '".$code."'
					   and m02_mkind			= '".$kind_code."'
					   and m02_yjumin			= '".$jumin."'";
			$conn->execute($sql);
			//echo $sql.'<br><br><br>';
		}else{
			$sql = "update m02yoyangsa
					   set m02_del_yn = 'Y'
					 where m02_ccode  = '$code'
					   and m02_mkind  = '$i'
					   and m02_yjumin = '$jumin'";
			$conn->execute($sql);
		}
	}

	// 변동 시급제 저장
	if ($pay_type == 'N'){
		// 변동 시급 삭제
		$sql = "delete
				  from m02pay
				 where m02_ccode = '$code'
				   and m02_mkind = '$kind'
				   and m02_jumin = '$jumin'";
		$conn->execute($sql);
		//echo $sql.'<br>';

		// 변동 시급 저장
		$sql = "insert into m02pay values
				 ('$code', '$kind', '$jumin', '".$_POST["yGibonKupCode"][0]."', '".str_replace(',', '', $_POST["yGibonKup"][0])."')
				,('$code', '$kind', '$jumin', '".$_POST["yGibonKupCode"][1]."', '".str_replace(',', '', $_POST["yGibonKup"][1])."')
				,('$code', '$kind', '$jumin', '".$_POST["yGibonKupCode"][2]."', '".str_replace(',', '', $_POST["yGibonKup"][2])."')
				,('$code', '$kind', '$jumin', '".$_POST["yGibonKupCode"][3]."', '".str_replace(',', '', $_POST["yGibonKup"][3])."')";
		$conn->execute($sql);
		//echo $sql.'<br>';
	}

	// 고정급여 저장
	$sql = "delete
			  from t25payfix
			 where t25_ccode    = '$code'
			   and t25_mkind    = '$kind'
			   and t25_yoy_code = '$jumin'";
	$conn->execute($sql);
	//echo $sql.'<br>';

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
						 '$code'
						,'$kind'
						,'$jumin'
						,'$id1'
						,'$id2'
						,'$id3'
						,'".str_replace(",", "", $_POST['pay_'.$id1.'_'.$id2.'_'.$id3])."')";
				$conn->execute($sql);
				//echo $sql.'<br>';
			}
		}
	}

	// 배상책임보험 처리
	if ($_POST['insYN'] == 'Y' && $_POST['ins_no'] == ''){
		// 배상책임보험 가입신청
		$sql = "select count(*)
				  from g03insapply
				 where g03_ins_code      = '".$_POST['insCode']."'
				   and g03_ins_from_date = '".str_replace('-', '', $_POST['insFromDate'])."'
				   and g03_jumin         = '".$jumin."'";
		if ($conn->get_data($sql) == 0){
			$sql = "insert into g03insapply values (
					 '".$_POST['ins_code']."'
					,'".str_replace('-', '', $_POST['insFromDate'])."'
					,''
					,'".$code."'
					,'".$kind."'
					,'".$jumin."'
					,'".$_POST["yName"]."'
					,'1'
					,'')";
			$conn->execute($sql);
			//echo $sql.'<br>';
		}
	}else if ($_POST['insYN'] == 'N'){
		// 배상책임보험 해지
		$sql = "update g03insapply
				   set g03_ins_to_date   = '".str_replace('-', '', $_POST['insToDate'])."'
				,      g03_ins_stat      = '7'
				 where g03_ins_code      = '".$_POST['ins_code']."'
				   and g03_ins_from_date = '".str_replace('-', '', $_POST['insFromDate'])."'
				   and g03_jumin         = '".$jumin."'
				   and (g03_ins_stat = '1' or g03_ins_stat = '2')";
		$conn->execute($sql);
		//echo $sql.'<br>';
	}

	$conn->commit();

	include_once("../inc/_db_close.php");
?>
<script>
	alert('<?=$myF->message("ok","N");?>');
	location.replace('reg.php?code=<?=$code;?>&kind=<?=$kind;?>&jumin=<?=$ed->en($jumin);?>&page=<?=$page;?>');
</script>