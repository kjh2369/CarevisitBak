<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$conn->begin();

	$code = $_SESSION["userCenterCode"];		//기관코드
	$chk_box1 = $_POST['check_1'];
	$chk_box2 = $_POST['check_2'];
	$chk_box3 = $_POST['check_3'];
	$chk_box4 = $_POST['check_4'];
	$chk_box5 = $_POST['check_5'];
	$chk_box6 = $_POST['check_6'];
	$chk_box7 = $_POST['check_7'];
	$chk_box8 = $_POST['check_8'];
	$chk_box9 = $_POST['check_9'];
	$chk_box10 = $_POST['check_10'];
	$chk_box11 = $_POST['check_11'];
	$chk_box12 = $_POST['check_12'];
	$chk_box13 = $_POST['check_13'];
	$chk_box14 = $_POST['check_14'];
	$chk_box15 = $_POST['check_15'];


	$sql = "select count(*)
				  from m02yoyangsa
				 where m02_ccode = '".$code."'
				";
		$cnt = $conn -> get_data($sql);
		
		for($i=0; $i<$cnt; $i++){

			$kind	= $_POST['kind'.$i];		//기관분류코드
			$jumin	= $ed->de($_POST['jumin'.$i]);
			

			// 시급고정급여부
			if ($_POST['yGupyeoKind'.$i] == '1Y'){
				$pay_kind = '1';
				$pay_type = 'Y';
				$pay_basic = $_POST['yGibonKup1'.$i];
				$pay_rate  = 0;
			}else if ($_POST['yGupyeoKind'.$i] == '1N'){
				$pay_kind = '1';
				$pay_type = 'N';
				$pay_basic = $_POST['yGibonKup'.$i][0];
				$pay_rate  = 0;
			}else if ($_POST['yGupyeoKind'.$i] == '3'){
				$pay_kind = '3';

				// 포괄임금제 적용여부
				if ($_POST['yGibonKupCom'.$i] == 'Y'){
					$pay_type = 'Y';
				}else{
					$pay_type = ' ';
				}
				$pay_basic = $_POST['yGibonKup3'.$i];
				$pay_rate  = 0;
			}else if ($_POST['yGupyeoKind'.$i] == '4'){
				$pay_kind = '4';
				$pay_type = ' ';
				$pay_basic = 0;
				$pay_rate  = $_POST['ySugaYoyul'.$i];
			}else{
				$pay_kind = '0';
				$pay_type = ' ';
				$pay_basic = 0;
				$pay_rate  = 0;
			}
			
			
			// 동거케어 시급고정급여부
			if ($_POST['yFamCareType'.$i] == 'Y'){
				$famcare_type = '1';
				$famcare_umu = 'Y';
				$famcare_pay = $_POST['yFamCarePay1_'.$i];
			}else if ($_POST['yFamCareType'.$i] == '2Y'){
				$famcare_type = '2';
				$famcare_umu = 'Y';
				$famcare_pay = $_POST['yFamCarePay2_'.$i];
			}else if ($_POST['yFamCareType'.$i] == '3Y'){
				$famcare_type = '3';
				$famcare_umu = 'Y';
				$famcare_pay = $_POST['yFamCarePay3_'.$i];
			}else{
				$famcare_type = '1';
				$famcare_umu = 'N';
				$famcare_pay = 0;
			}
			
			//echo $famcare_type.'/'.$famcare_umu.'/'.$famcare_pay.'<br>';
		
			$sql = "update m02yoyangsa
					   set m02_ytel				= '".str_replace('-', '', $_POST['yTel'.$i])."'
					,      m02_yjuso1			= '".$_POST['yJuso1'.$i]."'
					,      m02_yjakuk_kind		= '".$_POST['yJakukKind'.$i]."'
					,      m02_yjagyuk_no		= '".$_POST['yJagyukNo'.$i]."'
					,      m02_yjakuk_date		= '".str_replace('-', '', $_POST['yJakukDate'.$i])."'
					,      m02_yjikjong			= '".$_POST['yJikJong'.$i]."'
					,      m02_ybank_name		= '".$_POST['yBankName'.$i]."'
					,      m02_ygyeoja_no		= '".$_POST['yGyeojaNo'.$i]."'
					,      m02_y4bohum_umu		= '".$_POST['y4BohumUmu'.$i]."'
					,      m02_ygobohum_umu		= '".$_POST['y4BohumUmu'.$i]."'
					,      m02_ysnbohum_umu		= '".$_POST['y4BohumUmu'.$i]."'
					,      m02_ygnbohum_umu		= '".$_POST['y4BohumUmu'.$i]."'
					,      m02_ykmbohum_umu		= '".$_POST['y4BohumUmu'.$i]."'
					,      m02_ykuksin_mpay		= '".str_replace(',', '', $_POST['yKuksinMpay'.$i])."'
					,      m02_health_mpay		= '".str_replace(',', '', $_POST['yHealthMpay'.$i])."'
					,      m02_employ_mpay		= '".str_replace(',', '', $_POST['yEmployMpay'.$i])."'
					,      m02_sanje_mpay		= '".str_replace(',', '', $_POST['ySanjeMpay'.$i])."'
					,      m02_ygoyong_kind		= '".$_POST['yGoyongKind'.$i]."'
					,      m02_ygoyong_stat		= '".$_POST['yGoyongStat'.$i]."'
					,      m02_yipsail			= '".str_replace('-', '', $_POST['yIpsail'.$i])."'
					,      m02_ytoisail			= '".str_replace('-', '', $_POST['yToisail'.$i])."'
					,      m02_ygunmu_mon		= '".$_POST['yGunmuMon'.$i]."'
					,      m02_ygunmu_tue		= '".$_POST['yGunmuTue'.$i]."'
					,      m02_ygunmu_wed		= '".$_POST['yGunmuWed'.$i]."'
					,      m02_ygunmu_thu		= '".$_POST['yGunmuThu'.$i]."'
					,      m02_ygunmu_fri		= '".$_POST['yGunmuFri'.$i]."'
					,      m02_ygunmu_sat		= '".$_POST['yGunmuSat'.$i]."'
					,      m02_ygunmu_sun		= '".$_POST['yGunmuSun'.$i]."'
					,      m02_ygupyeo_kind		= '".$pay_kind."'
					,      m02_pay_type			= '".$pay_type."'
					,      m02_ygibonkup		= '".str_replace(',', '', $pay_basic)."'
					,      m02_ysuga_yoyul		= '".$pay_rate."'
					,      m02_yfamcare_umu		= '".$famcare_umu."'
					,      m02_yfamcare_pay		= '".str_replace(',', '', $famcare_pay)."'
					,      m02_yfamcare_type    = '".$famcare_type."'
					,      m02_ins_yn			= '".$_POST['insYN'.$i]."'
					,      m02_ins_code			= '".$_POST['ins_code'.$i]."'
					,      m02_ins_from_date	= '".str_replace('-', '', $_POST['insFromDate'.$i])."'
					,      m02_ins_to_date		= '".str_replace('-', '', $_POST['insToDate'.$i])."'
					,      m02_rank_pay         = '".str_replace(',', '', $_POST['rank_pay'.$i])."'
					,      m02_add_payrate      = '".str_replace(',', '', $_POST['addPayRate'.$i])."'
					,      m02_del_yn           = 'N'
					 where m02_ccode			= '".$code."'
					   and m02_mkind			= '".$kind."'
					   and m02_yjumin			= '".$jumin."'";
			$conn->execute($sql);
			
			//echo $sql;


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
						 ('$code', '$kind', '$jumin', '".$_POST["yGibonKupCode".$i][0]."', '".str_replace(',', '', $_POST["yGibonKup".$i][0])."')
						,('$code', '$kind', '$jumin', '".$_POST["yGibonKupCode".$i][1]."', '".str_replace(',', '', $_POST["yGibonKup".$i][1])."')
						,('$code', '$kind', '$jumin', '".$_POST["yGibonKupCode".$i][2]."', '".str_replace(',', '', $_POST["yGibonKup".$i][2])."')
						,('$code', '$kind', '$jumin', '".$_POST["yGibonKupCode".$i][3]."', '".str_replace(',', '', $_POST["yGibonKup".$i][3])."')";
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

			for($j=1; $j<=5; $j++){
				if ($j == 1 || $j == 2){
					$id1 = 1;
					if ($j == 1) $id2 = 0;
				}else{
					$id1 = 2;
					if ($j == 3) $id2 = 0;
				}
				$id2 ++;
				for($k=1; $k<=10; $k++){
					$id3 = ($j<10?'0':'').$k;
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
						 where g03_ins_code      = '".$_POST['insCode'.$i]."'
						   and g03_ins_from_date = '".str_replace('-', '', $_POST['insFromDate'.$i])."'
						   and g03_jumin         = '".$jumin."'";
				if ($conn->get_data($sql) == 0){
					$sql = "insert into g03insapply values (
							 '".$_POST['ins_code'.$i]."'
							,'".str_replace('-', '', $_POST['insFromDate'.$i])."'
							,''
							,'".$code."'
							,'".$kind."'
							,'".$jumin."'
							,'".$_POST["yName".$i]."'
							,'1'
							,'')";
					$conn->execute($sql);
					//echo $sql.'<br>';
				}
			}else if ($_POST['insYN'] == 'N'){
				// 배상책임보험 해지
				$sql = "update g03insapply
						   set g03_ins_to_date   = '".str_replace('-', '', $_POST['insToDate'.$i])."'
						,      g03_ins_stat      = '7'
						 where g03_ins_code      = '".$_POST['ins_code'.$i]."'
						   and g03_ins_from_date = '".str_replace('-', '', $_POST['insFromDate'.$i])."'
						   and g03_jumin         = '".$jumin."'
						   and (g03_ins_stat = '1' or g03_ins_stat = '2')";
				$conn->execute($sql);
				//echo $sql.'<br>';
			}
		}
	$conn->commit();

	include_once("../inc/_db_close.php");
	
?>
<script>
	alert('<?=$myF->message("ok","N");?>');
	location.replace('yoy_reg.php?code=<?=$code;?>&chk_box1=<?=$chk_box1;?>&chk_box2=<?=$chk_box2;?>&chk_box3=<?=$chk_box3;?>&chk_box4=<?=$chk_box4;?>&chk_box5=<?=$chk_box5;?>&chk_box6=<?=$chk_box6;?>&chk_box7=<?=$chk_box7;?>&chk_box8=<?=$chk_box8;?>&chk_box9=<?=$chk_box9;?>&chk_box10=<?=$chk_box10;?>&chk_box11=<?=$chk_box11;?>&chk_box12=<?=$chk_box12;?>&chk_box13=<?=$chk_box13;?>&chk_box14=<?=$chk_box14;?>&chk_box15=<?=$chk_box15;?>');
</script>