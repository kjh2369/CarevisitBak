<?
	include("../inc/_db_open.php");
	include('../inc/_ed.php');
?>
	<script src="../js/prototype.js" type="text/javascript"></script>
	<script src="../js/xmlHTTP.js" type="text/javascript"></script>
	<script src="../js/script.js" type="text/javascript"></script>
	<script src="../js/center.js" type="text/javascript"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?
	$conn->begin();

	// 현재 담당요양사를 찾는다.
	$sql = "select m02_yjumin, m02_yname, substring(m02_yjumin, 7, 1) % 2, m02_ytel, m02_yjakuk_kind, m03_sdate"
		 . "  from m03sugupja"
		 . " inner join m02yoyangsa"
		 . "    on m02_ccode = m03_ccode"
		 . "   and m02_mkind = m03_mkind"
		 . "   and m02_yjumin = m03_yoyangsa1"
		 . " where m03_ccode = '".$_POST["curMcode"]
		 . "'  and m03_mkind = '".$_POST["curMkind"]
		 . "'  and m03_jumin = '".$_POST["jumin1"].$_POST["jumin2"]
		 . "'";
	$yoyArray = $conn->get_array($sql);

	if ($yoyArray != null){
		$beforeDate = $conn->get_data("select ifnull(max(m32_a_date), '') from m32jikwon where m32_ccode = '".$_POST["curMcode"]."' and m32_mkind = '".$_POST["curMkind"]."' and m32_jumin ='".$_POST["jumin1"].$_POST["jumin2"]."' and m32_a_jumin = '".$$yoyArray[0]."'");

		if ($beforeDate == ''){
			$beforeDate = $yoyArray[5];
		}
		$beforeJumin = $yoyArray[0];
		$beforeName = $yoyArray[1];
		$beforeGenger = $yoyArray[2];
		$beforeTel = $yoyArray[3];
		$beforeLicense = (strLen($yoyArray[4]) == 1 ? '0' : '').$yoyArray[4];
	}

	if ($_POST["editMode"]){
		//등록
		$sql = "select ifnull(max(m03_key), 0)+1"
			 . "  from m03sugupja"
			 . " where m03_ccode = '".$_POST["curMcode"]
			 . "'  and m03_mkind = '".$_POST["curMkind"]
			 . "'";
		$conn->query($sql);
		$row = $conn->fetch();
		$key = $row[0];
		$conn->row_free();

		$sql = "insert into m03sugupja ("
			 . "  m03_ccode"
			 . ", m03_mkind"
			 . ", m03_jumin"
			 . ", m03_key"
			 . ") values ("
			 . "  '".$_POST["curMcode"]
			 . "','".$_POST["curMkind"]
			 . "','".$_POST["jumin1"].$_POST["jumin2"]
			 . "','".$key
			 . "')";
		if (!$conn->query($sql)){
			echo '<script>alert("'.mysql_error().'"); history.back();</script>';
			$conn->rollback();
			exit;
		}
	}else{
		$key = $_POST['mKey'];

		// 수급자 히스토리 관리
		if ($_POST['historys'] == 'Y'){
			$endDate = str_replace('.', '-', $_POST["sDate"]);

			$sql = 'insert into m31sugupja (m31_ccode, m31_mkind, m31_jumin, m31_sdate, m31_edate, m31_level, m31_kind, m31_bonin_yul, m31_kupyeo_max, m31_kupyeo_1, m31_kupyeo_2, m31_status, m31_gaeyak_fm, m31_gaeyak_to) '
				 . 'select m03_ccode, m03_mkind, m03_jumin, m03_sdate, replace(date_add(date_format(\''.$endDate.'\', \'%Y%m%d\'), interval -1 day), \'-\', \'\'), m03_ylvl, m03_skind, m03_bonin_yul, m03_kupyeo_max, m03_kupyeo_1, m03_kupyeo_2, m03_sugup_status, m03_gaeyak_fm, m03_gaeyak_to'
				 . '  from m03sugupja'
				 . ' where m03_ccode = \''.$_POST["curMcode"]
				 . '\' and m03_mkind = \''.$_POST["curMkind"]
				 . '\' and m03_jumin = \''.$_POST["jumin1"].$_POST["jumin2"]
				 . '\'';
			if (!$conn->query($sql)){
				echo '<script>alert("'.mysql_error().'"); history.back();</script>';
				$conn->rollback();
				exit;
			}
		}
	}

	$sql = "update m03sugupja"
		 . "   set m03_subcd         = '".$_POST["subCD"]
		 . "',     m03_vlvl          = '".$_POST["curMkind"]
		 . "',     m03_tel           = '".str_replace("-", "", $_POST["tel"])
		 . "',     m03_hp            = '".str_replace("-", "", $_POST["hp"])
		 . "',     m03_name          = '".$_POST["name"]
		 . "',     m03_juso1         = '".$_POST["juso1"]
		 . "',     m03_juso2         = '".$_POST["juso2"]
		 . "',     m03_post_no       = '".$_POST["postNo1"].$_POST["postNo2"]
		 . "',     m03_gaeyak_fm     = '".str_replace("-", "", $_POST["gaeYakFm"])
		 . "',     m03_gaeyak_to     = '".str_replace("-", "", $_POST["gaeYakTo"])
		 . "',     m03_yccode        = '".$_POST["curMcode"]
		 . "',     m03_ylvl          = '".$_POST["yLvl"]
		 . "',     m03_byungmung     = '".$_POST["byungMung"]
		 . "',     m03_injung_no     = '".$_POST["injungNo"]
		 . "',     m03_familycare    = '".$_POST["familyCare"]
		 . "',     m03_skind         = '".$_POST["sKind"]
		 . "',     m03_bonin_yul     = '".$_POST["boninYul"]
		 . "',     m03_kupyeo_max    = '".str_replace(",", "", $_POST["kupyeoMax"])
		 . "',     m03_kupyeo_1      = '".str_replace(",", "", $_POST["kupyeo1"])
		 . "',     m03_kupyeo_2      = '".str_replace(",", "", $_POST["kupyeo2"])
		 . "',     m03_injung_from   = '".str_replace("-", "", $_POST["injungFrom"])
		 . "',     m03_injung_to     = '".str_replace("-", "", $_POST["injungTo"])
		 . "',     m03_skigwan_name  = '".$_POST["skigwanName"]
		 . "',     m03_skigwan_code  = '".$_POST["skigwanCode"]
		 . "',     m03_yoyangsa1     = '".$_POST["yoyangsa1"]
		 . "',     m03_yoyangsa2     = '".$_POST["yoyangsa2"]
		 . "',     m03_yoyangsa3     = '".$_POST["yoyangsa3"]
		 . "',     m03_yoyangsa4     = '".$_POST["yoyangsa4"]
		 . "',     m03_yoyangsa5     = '".$_POST["yoyangsa5"]
		 . "',     m03_yoyangsa1_nm  = '".$_POST["yoyangsa1Nm"]
		 . "',     m03_yoyangsa2_nm  = '".$_POST["yoyangsa2Nm"]
		 . "',     m03_yoyangsa3_nm  = '".$_POST["yoyangsa3Nm"]
		 . "',     m03_yoyangsa4_nm  = '".$_POST["yoyangsa4Nm"]
		 . "',     m03_yoyangsa5_nm  = '".$_POST["yoyangsa5Nm"]
		 . "',     m03_yboho_name    = '".$_POST["yBohoName"]
		 . "',     m03_yboho_juminno = '".$_POST["yBohoJuminNo1"].$_POST["yBohoJuminNo2"]
		 . "',     m03_yboho_gwange  = '".$_POST["yBohoGwange"]
		 . "',     m03_yboho_phone   = '".str_replace("-", "", $_POST["yBohoPhone"])
		 . "',     m03_sugup_status  = '".$_POST["sugupStatus"]
		 . "',     m03_sdate         = '".str_replace('.', '', $_POST["sDate"])
		 . "',     m03_edate         = '99999999'"
		 . " where m03_ccode = '".$_POST["curMcode"]
		 . "'  and m03_mkind = '".$_POST["curMkind"]
		 . "'  and m03_jumin = '".$_POST["jumin1"].$_POST["jumin2"]
		 . "'";

	if (!$conn->query($sql)){
		echo '<script>alert("'.mysql_error().'"); history.back();</script>';
		$conn->rollback();
		exit;
	}

	// 담당요양사 변경여부
	if ($beforeJumin != $_POST["yoyangsa1"]){
		$gubun = '1';

		// 변경된 요양사 정보
		$sql = "select m02_yjumin, m02_yname, substring(m02_yjumin, 7, 1) % 2, m02_ytel, m02_yjakuk_kind"
			 . "  from m03sugupja"
			 . " inner join m02yoyangsa"
			 . "    on m02_ccode = m03_ccode"
			 . "   and m02_mkind = m03_mkind"
			 . "   and m02_yjumin = '".$_POST["yoyangsa1"]
			 . "'"
			 . " where m03_ccode = '".$_POST["curMcode"]
			 . "'  and m03_mkind = '".$_POST["curMkind"]
			 . "'  and m03_jumin = '".$_POST["jumin1"].$_POST["jumin2"]
			 . "'";
		$yoyArray = $conn->get_array($sql);

		if ($yoyArray != null){
			$afterDate		= str_replace('.', '', $_POST["sDate"]);
			$afterJumin		= $yoyArray[0];
			$afterName		= $yoyArray[1];
			$afterGenger	= $yoyArray[2];
			$afterTel		= $yoyArray[3];
			$afterLicense	= (strLen($yoyArray[4]) == 1 ? '0' : '').$yoyArray[4];
		}

		// 센터정보
		$sql = "select m00_mname, m00_ctel"
			 . "  from m00center"
			 . " where m00_mcode = '".$_POST["curMcode"]
			 . "'  and m00_mkind = '".$_POST["curMkind"]
			 . "'";
		$centerArray = $conn->get_array($sql);
		$centerMname = $centerArray[0];
		$centerTel = $centerArray[1];

		$sql = "replace into m32jikwon values ("
			 . "  '".$_POST["curMcode"]
			 . "','".$_POST["curMkind"]
			 . "','".$_POST["jumin1"].$_POST["jumin2"]
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
		if (!$conn->query($sql)){
			echo '<script>alert("'.mysql_error().'"); history.back();</script>';
			$conn->rollback();
			exit;
		}
	}

	$conn->commit();
	$conn->close();

	echo "<script>location.replace('sugupja.php?gubun=sugupjaReg&mCode=".$_POST["curMcode"]."&mKind=".$_POST["curMkind"]."&mJumin=".$ed->en($_POST["jumin1"].$_POST["jumin2"])."');</script>";
?>