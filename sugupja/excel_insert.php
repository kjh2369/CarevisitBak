<?
	include("../inc/_db_open.php");
	include('../inc/_myFun.php');
	include('../inc/_ed.php');
	header('Content-type: text/html; charset=utf-8'); // 문자셋

	require_once '../excel/Excel/reader.php';

	$mCode = $_GET['p_code'];
	$mKind = $_GET['p_kind'];
	$mDuplicate = $_GET['duplicateYN'];
	$mFile = $_GET['p_file'];
	
	$excel = new Spreadsheet_Excel_Reader();
	$excel->setOutputEncoding('UTF-8'); // 문자셋
	$excel->read('./Excel/'.$mFile);

	error_reporting(E_ALL ^ E_NOTICE);

	$conn->begin();

	// 급여한도액
	$sql = "select m91_code as code, m91_kupyeo as pay
			  from m91maxkupyeo
			 where '".date('Ymd', mkTime())."' between m91_sdate and m91_edate";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$maxPay[$i] = $conn->select_row($i);
	}
	$conn->row_free();
	$maxPayCount = sizeOf($maxPay);

	// 본인부담율
	$sql = "select m92_code as code, m92_bonin_yul as rate
			  from m92boninyul
			 where '".date('Ymd', mkTime())."' between m92_sdate and m92_edate";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$rate[$i] = $conn->select_row($i);
	}
	$conn->row_free();
	$rateCount = sizeOf($rate);

	// 키
	$sql = "select ifnull(max(m03_key), 0)
			  from m03sugupja
			 where m03_ccode = '$mCode'
			   and m03_mkind = '$mKind'";
	$key = $conn->get_data($sql);

	for($i=3; $i<=$excel->sheets[0]['numRows']; $i++){
		$key ++;
		
		$name = $excel->sheets[0]['cells'][$i][1]; //성명
		$jumin = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][2]); //주민번호
		if($jumin != ''){
			$mobile = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][3]); //핸펀번호
			$phone = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][4]); //자택전화번호
			$postno = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][5]); //우편번호
			$addr1 = $excel->sheets[0]['cells'][$i][6]; //주소
			$addr2 = $excel->sheets[0]['cells'][$i][7]; //상세주소
			$fromDate = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][8]); //계약개시일
			$toDate = '99999999'; //계약종료일
			$status = $excel->sheets[0]['cells'][$i][10] != '' ? $excel->sheets[0]['cells'][$i][10] : '1'; //수급자상태
			$level = $excel->sheets[0]['cells'][$i][11]; //장기요양등급
			$kind = $excel->sheets[0]['cells'][$i][12]; //수급자구분
			$supportAmt = $excel->sheets[0]['cells'][$i][13]; //청구한도금액
			//$boninRate = str_replace(',', '', $excel->sheets[0]['cells'][$i][14]); //본인부담율
			$injungNo = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][15]); //장기요양보험 인증번호
			$noFromDate = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][16]); //장기요양인증서유효기간(FM)
			$noToDate = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][17]); //장기요양인증서유효기간(TO)
			$sickName = $excel->sheets[0]['cells'][$i][18]; //병명
			$bohojaName = $excel->sheets[0]['cells'][$i][19]; //보호자성명
			$bohojaGwange = $excel->sheets[0]['cells'][$i][20]; //수급자와의관계
			$bohojaPhone = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][21]); //보호자연락처
			$yoyangsa1nm    = $excel->sheets[0]['cells'][$i][22]; //담당요양사1 이름
			$yoyangsa1      = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][23]); //담당요양사1 주민
			$sDate = $fromDate;
			$eDate = '99999999';
			
			// 급여한도액
			for($j=0; $j<$maxPayCount; $j++){
				if ($maxPay[$j]['code'] == $level){
					$maxAmt = $maxPay[$j]['pay']; //급여한도액
					break;
				}
			}

			// 본인부담율
			for($j=0; $j<$rateCount; $j++){
				if ($rate[$j]['code'] == $kind){
					$boninRate = str_replace(',', '', $excel->sheets[0]['cells'][$i][14]); //본인부담율
					$boninAmt = $myF->cutOff($supportAmt * $boninRate / 100); //본인부담금
					//$supportAmt = $myF->cutOff($maxAmt - $boninAmt); //정부지원금
					break;
				}
			}
			
			
			$count = $conn->get_data("select count(*) from m03sugupja where m03_ccode = '$mCode' and m03_mkind = '$mKind' and m03_jumin = '$jumin'");
			
			if ($mDuplicate == 'N'){
				if ($count == 0){
					$sql = "insert into m03sugupja (
							 m03_ccode
							,m03_mkind
							,m03_name
							,m03_jumin
							,m03_hp
							,m03_tel
							,m03_post_no
							,m03_juso1
							,m03_juso2
							,m03_gaeyak_fm
							,m03_gaeyak_to
							,m03_sugup_status
							,m03_ylvl
							,m03_skind
							,m03_injung_no
							,m03_byungmung
							,m03_familycare
							,m03_injung_from
							,m03_injung_to
							,m03_yboho_name
							,m03_yboho_juminno
							,m03_yboho_phone
							,m03_yboho_gwange
							,m03_yoyangsa1_nm
							,m03_yoyangsa1
							,m03_yoyangsa2_nm
							,m03_yoyangsa2
							,m03_key
							,m03_sdate
							,m03_edate
							,m03_bonin_yul
							,m03_kupyeo_max
							,m03_kupyeo_1
							,m03_kupyeo_2
							,m03_subcd
							,m03_vlvl
							) values (
							 '$mCode'
							,'$mKind'
							,'$name'
							,'$jumin'
							,'$mobile'
							,'$phone'
							,'$postno'
							,'$addr1'
							,'$addr2'
							,'$fromDate'
							,'$toDate'
							,'$status'
							,'$level'
							,'$kind'
							,'$injungNo'
							,'$sickName'
							,'$familyYN'
							,'$noFromDate'
							,'$noToDate'
							,'$bohojaName'
							,'$bohojaJumin'
							,'$bohojaPhone'
							,'$bohojaGwange'
							,'$yoyangsa1nm'
							,'$yoyangsa1'
							,'$yoyangsa2nm'
							,'$yoyangsa2'
							,'$key'
							,'$sDate'
							,'99999999'
							,'$boninRate'
							,'$maxAmt'
							,'$supportAmt'
							,'$boninAmt'
							,'200'
							,'$mKind')";
					if (!$conn->query($sql)){
						$conn->rollback();
								
						echo '
							<script language="javascript">
								alert("데이타 저장중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.");
								history.back();
							</script>
							 ';
						exit;
					}
					
				
					// 현재 담당요양사를 찾는다.
					$sql = "select m02_yjumin, m02_yname, substring(m02_yjumin, 7, 1) % 2, m02_ytel, m02_yjakuk_kind, m03_sdate
							  from m03sugupja
							 inner join m02yoyangsa
								on m02_ccode = m03_ccode
							   and m02_mkind = m03_mkind
							   and m02_yjumin = m03_yoyangsa1
							 where m03_ccode = '$mCode'
							   and m03_mkind = '$mKind'
							   and m03_jumin = '$jumin'";
					$yoyArray = $conn->get_array($sql);
				
					if ($yoyArray != null){
						$yoy_jumin = $yoyArray[0];
						$beforeDate = $conn->get_data("select ifnull(max(m32_a_date), '')
														 from m32jikwon
														where m32_ccode   = '$mCode'
														  and m32_mkind   = '$mKind'
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
						 . "  '".$mCode
						 . "','".$mKind
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

				}
			}else{
				if ($count == 0){
					$sql = "insert into m03sugupja (
							 m03_ccode
							,m03_mkind
							,m03_jumin
							, m03_key
							) values (
							 '$mCode'
							,'$mKind'
							,'$jumin'
							,'$key')";
					if (!$conn->query($sql)){
						$conn->rollback();
								
						echo '
							<script language="javascript">
								alert("데이타 저장중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.");
								history.back();
							</script>
							 ';
						exit;
					}
					
				}
						// 현재 담당요양사를 찾는다.
				$sql = "select m02_yjumin, m02_yname, substring(m02_yjumin, 7, 1) % 2, m02_ytel, m02_yjakuk_kind, m03_sdate
						  from m03sugupja
						 inner join m02yoyangsa
							on m02_ccode = m03_ccode
						   and m02_mkind = m03_mkind
						   and m02_yjumin = m03_yoyangsa1
						 where m03_ccode = '$mCode'
						   and m03_mkind = '$mKind'
						   and m03_jumin = '$jumin'";
				$yoyArray = $conn->get_array($sql);
				
				if ($yoyArray != null){
					$yoy_jumin = $yoyArray[0];
					$beforeDate = $conn->get_data("select ifnull(max(m32_a_date), '')
													 from m32jikwon
													where m32_ccode   = '$mCode'
													  and m32_mkind   = '$mKind'
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
				$sql = "update m03sugupja
						   set m03_name			= '".$name."'
						,      m03_jumin		= '".$jumin."'
						,      m03_hp			= '".$mobile."'
						,      m03_tel			= '".$phone."'
						,      m03_post_no		= '".$postno."'
						,      m03_juso1		= '".$addr1."'
						,      m03_juso2		= '".$addr2."'
						,      m03_gaeyak_fm	= '".$fromDate."'
						,      m03_gaeyak_to	= '".$toDate."'
						,      m03_yboho_name	= '".$bohojaName."'
						,      m03_yboho_gwange	= '".$bohojaGwange."'
						,      m03_yboho_phone	= '".$bohojaPhone."'
						,      m03_sugup_status	= '".$status."'
						,      m03_ylvl			= '".$level."'
						,      m03_kupyeo_max	= '".$maxAmt."'
						,      m03_skind		= '".$kind."'
						,      m03_bonin_yul	= '".$boninRate."'
						,      m03_kupyeo_1		= '".$supportAmt."'
						,      m03_kupyeo_2		= '".$boninAmt."'
						,      m03_injung_no	= '".$injungNo."'
						,      m03_injung_from	= '".$noFromDate."'
						,      m03_injung_to	= '".$noToDate."'
						,      m03_byungmung	= '".$sickName."'
						,      m03_yoyangsa1	= '".$yoyangsa1."'
						,      m03_yoyangsa2	= '".$yoyangsa2."'
						,      m03_yoyangsa1_nm	= '".$yoyangsa1nm."'
						,      m03_yoyangsa2_nm	= '".$yoyangsa2nm."'
						,      m03_sdate		= '".$sDate."'
						,      m03_edate		= '99999999'
						 where m03_ccode		= '$mCode'
						   and m03_mkind		= '$mKind'
						   and m03_jumin		= '$jumin'";
				if (!$conn->execute($sql)){
					$conn->rollback();
					echo $conn->err_back();
					if ($conn->mode == 1) exit;
				}

				if($beforeJumin != $yoyangsa1){
					$gubun = '1';
					// 변경된 요양사 정보
					$sql = "select m02_yjumin, m02_yname, substring(m02_yjumin, 7, 1) % 2, m02_ytel, m02_yjakuk_kind
							  from m02yoyangsa
							 where m02_ccode  = '$mCode'
							   and m02_mkind  = '$mKind'
							   and m02_yjumin = '$yoyangsa1'";
					
					$yoyArray = $conn->get_array($sql);
					
					if ($yoyArray != null){
						$afterDate		= $sDate;
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
						 . "  '".$mCode
						 . "','".$mKind
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
					
				}
			}			
		}
	}

	if (is_file('./Excel/'.$mFile)){
		unlink('./Excel/'.$mFile);
	}

	$conn->commit();

	include('../inc/_db_close.php');

	echo "
		<script language='javascript'>
			alert('입력이 완료되었습니다.');
		//	location.replace('../main/main.php?gubun=search');
			location.replace('../sugupja/list.php');
		</script>
		 ";

?>