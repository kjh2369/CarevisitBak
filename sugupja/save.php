<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	//$conn->mode = 2;

	$mode = $_POST['mode'];

	$code		= $_POST['code'];		//기관코드
	$kind		= $_POST['kind'];		//기관분류코드
	$kind_temp	= $_POST['kind_temp'];
	$kind_list	= $_POST['kind_list'];	//기관리스트
	$kind_count = sizeof($kind_temp);	//기관리스트갯수

	$history_yn	= $_POST['history_yn'];	//히스토리 괸리 여부

	// 주민번호
	if ($mode == 1){
		$jumin		= $_POST['jumin1'].$_POST['jumin2'];
		$yoy_jumin1 = $_POST['yoyangsa1'];
		$yoy_jumin2 = $_POST['yoyangsa2'];
	}else{
		$jumin		= $ed->de($_POST['jumin']);

		if (is_numeric($_POST['yoyangsa1'])){
			$yoy_jumin1 = $_POST['yoyangsa1'];
		}else{
			$yoy_jumin1 = $ed->de($_POST['yoyangsa1']);
		}

		if (is_numeric($_POST['yoyangsa2'])){
			$yoy_jumin2 = $_POST['yoyangsa2'];
		}else{
			$yoy_jumin2 = $ed->de($_POST['yoyangsa2']);
		}
	}

	if ($mode == 1){ //등록
		// 다음 키
		$sql = "select ifnull(max(m03_key), 0) + 1
				  from m03sugupja
				 where m03_ccode = '$code'
				   and m03_mkind = '$kind'";
		$key = $conn->get_data($sql);
	}else{
		$sql = "select m03_key
				  from m03sugupja
				 where m03_ccode  = '$code'
				   and m03_mkind  = '$kind'
				   and m03_jumin = '$jumin'";
		$key = $conn->get_data($sql);

		// 수급자 히스토리 관리
		if ($history_yn == 'Y'){
			$endDate = str_replace('.', '', str_replace('-', '', $_POST['startDate']));
			$endDate = $myF->dateStyle($endDate);

			$sql = "insert into m31sugupja (m31_ccode, m31_mkind, m31_jumin, m31_sdate, m31_edate, m31_level, m31_kind, m31_bonin_yul, m31_kupyeo_max, m31_kupyeo_1, m31_kupyeo_2, m31_status, m31_gaeyak_fm, m31_gaeyak_to)
					select m03_ccode, m03_mkind, m03_jumin, m03_sdate, replace(date_add(date_format('$endDate', '%Y%m%d'), interval -1 day), '-', ''), m03_ylvl, m03_skind, m03_bonin_yul, m03_kupyeo_max, m03_kupyeo_1, m03_kupyeo_2, m03_sugup_status, m03_gaeyak_fm, m03_gaeyak_to
					  from m03sugupja
					 where m03_ccode = '$code'
					   and m03_mkind = '$kind'
					   and m03_jumin = '$jumin'";
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
			//echo $sql.'<br><br>';
		}
	}

	// 현재 담당요양사를 찾는다.
	$sql = "select m02_yjumin, m02_yname, substring(m02_yjumin, 7, 1) % 2, m02_ytel, m02_yjakuk_kind, m03_sdate
			  from m03sugupja
			 inner join m02yoyangsa
				on m02_ccode = m03_ccode
			   and m02_mkind = m03_mkind
		       and m02_yjumin = m03_yoyangsa1
		     where m03_ccode = '$code'
		       and m03_mkind = '$kind'
		       and m03_jumin = '$jumin'";
	$yoyArray = $conn->get_array($sql);

	if ($yoyArray != null){
		$yoy_jumin = $yoyArray[0];
		$beforeDate = $conn->get_data("select ifnull(max(m32_a_date), '')
										 from m32jikwon
										where m32_ccode   = '$code'
										  and m32_mkind   = '$kind'
										  and m32_jumin   = '$jumin'
										  and m32_a_jumin = '$yoy_jumin'");

		if ($beforeDate == ''){
			$beforeDate = $yoyArray[5];
		}
		$beforeJumin	= $yoyArray[0];
		$beforeName		= $yoyArray[1];
		$beforeGenger	= $yoyArray[2];
		$beforeTel		= $yoyArray[3];
		$beforeLicense  = (strLen($yoyArray[4]) == 1 ? '0' : '').$yoyArray[4];
	}

	$conn->begin();

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
					  from m03sugupja
					 where m03_ccode  = '$code'
					   and m03_mkind  = '$kind_code'
					   and m03_jumin = '$jumin'";
			if ($conn->get_data($sql) == 0){
				// 저장
				$sql = "insert into m03sugupja (m03_ccode, m03_mkind, m03_jumin, m03_key) values ('$code', '$kind_code', '$jumin', '$key')";
				if (!$conn->execute($sql)){
					$conn->rollback();
					echo $conn->err_back();
					if ($conn->mode == 1) exit;
				}
				//echo $sql.'<br><br>';
			}

			$sql = "update m03sugupja
					   set m03_name			= '".$_POST['name']."'
					,      m03_hp			= '".str_replace('-', '', $_POST['hp'])."'
					,      m03_tel			= '".str_replace('-', '', $_POST['tel'])."'
					,      m03_post_no		= '".$_POST['postno1'].$_POST['postno2']."'
					,      m03_juso1		= '".$_POST['addr1']."'
					,      m03_juso2		= '".$_POST['addr2']."'
					,      m03_gaeyak_fm	= '".str_replace('-', '', $_POST['gaeYakFm'])."'
					,      m03_gaeyak_to	= '".str_replace('-', '', $_POST['gaeYakTo'])."'
					,      m03_yboho_name	= '".$_POST['yBohoName']."'
					,      m03_yboho_gwange	= '".$_POST['yBohoGwange']."'
					,      m03_yboho_phone	= '".str_replace('-', '', $_POST['yBohoPhone'])."'
					,      m03_sugup_status	= '".$_POST['sugupStatus']."'
					,      m03_ylvl			= '".$_POST['yLvl']."'
					,      m03_kupyeo_max	= '".str_replace(',', '', $_POST['kupyeoMax'])."'
					,      m03_skind		= '".$_POST['sKind']."'
					,      m03_bonin_yul	= '".$_POST['boninYul']."'
					,      m03_kupyeo_1		= '".str_replace(',', '', $_POST['kupyeo1'])."'
					,      m03_kupyeo_2		= '".str_replace(',', '', $_POST['kupyeo2'])."'
					,      m03_injung_no	= '".$_POST['injungNo']."'
					,      m03_injung_from	= '".str_replace('-', '', $_POST['injungFrom'])."'
					,      m03_injung_to	= '".str_replace('-', '', $_POST['injungTo'])."'
					,      m03_byungmung	= '".$_POST['byungMung']."'
					,      m03_disease_nm   = '".$_POST['diseaseNm']."'
					,      m03_stat_nogood  = '".$_POST['stat_nogood']."'
					,      m03_yoyangsa1	= '".$yoy_jumin1."'
					,      m03_yoyangsa2	= '".$yoy_jumin2."'
					,      m03_yoyangsa1_nm	= '".$_POST['yoyangsa1Nm']."'
					,      m03_yoyangsa2_nm	= '".$_POST['yoyangsa2Nm']."'
					,      m03_partner      = '".$_POST['partner']."'
					,      m03_bath_add_yn  = '".$_POST['bathAddYn']."'
					,      m03_sdate		= '".str_replace('.', '', str_replace('-', '', $_POST['startDate']))."'
					,      m03_edate		= '99999999'
					 where m03_ccode		= '$code'
					   and m03_mkind		= '$kind'
					   and m03_jumin		= '$jumin'";
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
			//echo $sql.'<br><br>';

			if (str_replace('-', '', $_POST['gaeYakFm']) != str_replace('.', '', str_replace('-', '', $_POST['startDate']))){
				$sql = "select min(m31_sdate)
						  from m31sugupja
						 where m31_ccode = '$code'
						   and m31_mkind = '$kind'
						   and m31_jumin = '$jumin'";
				$his_start_dt = $conn->get_data($sql);

				if ($his_start_dt == ''){
					$sql = "update m03sugupja
							   set m03_sdate = m03_gaeyak_fm
							 where m03_ccode = '$code'
							   and m03_mkind = '$kind'
							   and m03_jumin = '$jumin'";

					if (!$conn->execute($sql)){
						$conn->rollback();
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}else{
					if (str_replace('-', '', $_POST['gaeYakFm']) != $his_start_dt){
						$new_date = str_replace('-', '', $_POST['gaeYakFm']);
						$sql = "update m31sugupja
								   set m31_sdate = '$new_date'
								 where m31_ccode = '$code'
								   and m31_mkind = '$kind'
								   and m31_jumin = '$jumin'
								   and m31_sdate = '$his_start_dt'";

						if (!$conn->execute($sql)){
							$conn->rollback();
							echo $conn->err_back();
							if ($conn->mode == 1) exit;
						}
					}
				}
			}
		}else{
			$sql = "update m03sugupja
					   set m03_del_yn = 'Y'
					 where m03_ccode  = '$code'
					   and m03_mkind  = '$i'
					   and m03_jumin = '$jumin'";
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
			//echo $sql.'<br><br>';
		}
	}

	// 담당요양사 변경여부
	if ($beforeJumin != $yoy_jumin1){
		$gubun = '1';

		// 변경된 요양사 정보
		$sql = "select m02_yjumin, m02_yname, substring(m02_yjumin, 7, 1) % 2, m02_ytel, m02_yjakuk_kind
				  from m02yoyangsa
				 where m02_ccode  = '$code'
				   and m02_mkind  = '$kind'
				   and m02_yjumin = '$yoy_jumin1'";
		//echo $sql.'<br>';
		$yoyArray = $conn->get_array($sql);

		if ($yoyArray != null){
			//$afterDate	= str_replace('.', '', str_replace('-', '', $_POST['sDate']));
			$afterDate		= str_replace('.', '', str_replace('-', '', $_POST['mem_change_dt']));
			$afterJumin		= $yoyArray[0];
			$afterName		= $yoyArray[1];
			$afterGenger	= $yoyArray[2];
			$afterTel		= $yoyArray[3];
			$afterLicense	= (strLen($yoyArray[4]) == 1 ? '0' : '').$yoyArray[4];
		}

		// 센터정보
		$sql = "select m00_mname, m00_ctel"
			 . "  from m00center"
			 . " where m00_mcode = '".$code
			 . "'  and m00_mkind = '".$kind
			 . "'";
		$centerArray = $conn->get_array($sql);
		$centerMname = $centerArray[0];
		$centerTel = $centerArray[1];

		$sql = "replace into m32jikwon values ("
			 . "  '".$code
			 . "','".$kind
			 . "','".$jumin
			 . "','".$gubun
			 . "','".$beforeDate
			 . "','".$beforeJumin
			 . "','".$beforeName
			 . "','".$beforeGenger
			 . "','".$beforeTel
			 . "','".$beforeLicense
			 . "','".$afterDate
			 . "','".$afterJumin
			 . "','".$afterName
			 . "','".$afterGenger
			 . "','".$afterTel
			 . "','".$afterLicense
			 . "','".$centerMname
			 . "','"
			 . "','".$centerTel
			 . "')";
		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}
		//echo $sql.'<br><br>';
	}

	$conn->commit();

	include_once("../inc/_db_close.php");
?>
<script>
	alert('<?=$myF->message("ok","N");?>');
	location.replace('reg.php?code=<?=$code;?>&kind=<?=$kind;?>&jumin=<?=$ed->en($jumin);?>&page=<?=$page;?>');
</script>