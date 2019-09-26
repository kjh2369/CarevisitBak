<?
	include("../inc/_db_open.php");
	include('../inc/_myFun.php');
	include('../inc/_ed.php');

	header('Content-type: text/html; charset=utf-8'); // 문자셋

	$mCode = $_GET['p_code'];
	//$mKind = $_GET['p_kind'];
	$mDuplicate = $_GET['duplicateYN'];
	$mFile = $_GET['p_file'];

	$file = './excel/'.$mFile;
	$row_no = 1;
	$row_id = 0;

	if (($handle = fopen($file, "r")) !== FALSE) {

	/*
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if ($row_no > 2){
				$cnt = count($data);

				for($i=0; $i < $cnt; $i++) {
					$row[$row_id][$i] = $myF->utf($data[$i]);
				}
				$row_id ++;
			}
			$row_no ++;
		}
	*/
		while(true){
			$str = fgets($handle);

			if ($row_no > 2){
				$dta = explode(',', $str);

				for($i=0; $i<sizeof($dta); $i++){
					$row[$row_id][$i] = $myF->utf($dta[$i]);
				}

				$row_id ++;
			}

			$row_no ++;

			if (feof($handle)) break;
		}

		fclose($handle);
	}else{
		echo $myF->message('업로드하신 파일을 찾을 수 없습니다. 잠시후 다시 시도하여 주십시오.', 'Y', 'Y');
		exit;
	}

	error_reporting(E_ALL ^ E_NOTICE);

	$conn->begin();

	// 키
	$sql = "select ifnull(max(m03_key), 0)
			  from m03sugupja
			 where m03_ccode = '$mCode'";
	$key = $conn->get_data($sql);

	$row_count = sizeof($row);

	$read_cnt = ($row_count-2);
	$reg_cnt = 0;

	for($i=0; $i<=$row_count; $i++){
		$key ++;

		//$no  = $row[$i][0];
		$name  = $row[$i][1];
		$jumin = str_replace('-', '', $row[$i][2]);

		if($jumin != ''){
			$mobile			= str_replace('-', '', $row[$i][3]); //핸펀번호
			$phone			= str_replace('-', '', $row[$i][4]); //자택전화번호
			$addr1			= $row[$i][5]; //주소
			$addr2			= $row[$i][6]; //상세주소
			$bohojaName		= $row[$i][7]; //보호자성명
			$bohojaGwange	= $row[$i][8]; //수급자와의관계
			$bohojaPhone	= preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $row[$i][9]); //보호자연락처
			$svc_kind		= $row[$i][10]; //이용서비스
			$contractDt		= preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $row[$i][11]); //계약일자
			$nintyYn		= $row[$i][12];	//90분가능여부
			$main_yoy		= preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $row[$i][13]);   //주담당
			$partner		= $row[$i][14];	//배우자
			$bu_yoy			= preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $row[$i][15]);   //부담당
			$injungNo		= $row[$i][16];   //장기요양인증서정보(인증번호)
			$injungFmDt		= preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $row[$i][17]);   //장기요양인증서정보(유효시작일)
			$injungToDt		= preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $row[$i][18]);   //장기요양인증서정보(유효종료일)
			$level			= $row[$i][19];   //장기요양인증서정보(요양등급)
			$sugupGbn		= $row[$i][20];   //장기요양인증서정보(수급자구분)

			/*
			if (strlen($contractDt) != 8){
				$contractDt = $row[$i][11];
				$int = 0;

				for($j=0; $j<strlen($contractDt); $j++){
					$txt = substr($contractDt,$j,1);

					if (is_numeric($txt))
						$str[$int] .= $txt;
					else
						$int ++;
				}

				if (intval($str[0]) == 0 || intval($str[1]) == 0 || intval($str[2]) == 0){
					$contractDt = '';
				}else{
					$str[1] = (intval($str[1]) < 10 ? '0' : '').intval($str[1]);
					$str[2] = (intval($str[2]) < 10 ? '0' : '').intval($str[2]);
					$contractDt = $str[0].$str[1].$str[2];
				}
			}
			*/


			if($svc_kind == 1){
				$mKind = 0;
			} else if($svc_kind == 2){
				$mKind = 1;
			} else if($svc_kind == 3){
				$mKind = 2;
			} else if($svc_kind == 4){
				$mKind = 3;
			} else if($svc_kind == 5){
				$mKind = 4;
			} else {
				$mKind = '';
			}



			#주담당직원조회

			$sql = "select m02_yname, m02_yjumin
					  from m02yoyangsa
				     where m02_ccode = '$mCode'
					   and m02_mkind = '$mKind'
					   and m02_yname = '$main_yoy'
					   and m02_ygoyong_stat = '1'
					   and m02_del_yn  = 'N'
					 limit 1";
			$yoy1 = $conn -> get_array($sql);

			if($yoy1[0] != $main_yoy){
				$main_yoy = '';
			}

			#부담당직원조회

			$sql = "select m02_yname, m02_yjumin
					  from m02yoyangsa
				     where m02_ccode = '$mCode'
					   and m02_mkind = '$mKind'
					   and m02_yname = '$bu_yoy'
					   and m02_ygoyong_stat = '1'
					   and m02_del_yn  = 'N'
					 limit 1";
			$yoy2 = $conn -> get_array($sql);

			if($yoy2[0] != $bu_yoy){

				$bu_yoy = '';
				/*
				$err = ($i+3).'라인'.$bu_yoy.'님은 직원이 아닙니다.';
				echo "<script language='javascript'>
						alert('".$err."');
						history.back();
					  </script>";
				exit;
				*/
			}


			if ($level == '1' || $level == '2' || $level == '3'){
			}else{
				$level = '9';	//요양등급(일반)
			}

			#수급자구분
			switch($sugupGbn){
				case 1:
					$sugupGbn = '3';	//기초
					break;
				case 2:
					$sugupGbn = '2';	//의료
					break;
				case 3:
					$sugupGbn = '4';	//경감
					break;
				default:
					$sugupGbn = '1';	//일반
			}

			$date = date('Ymd', mktime());


			#청구한도
			$sql = 'select m91_kupyeo
					  from m91maxkupyeo
					 where m91_code   = \''.$level.'\'
					   and m91_sdate <= \''.$date.'\'
					   and m91_edate >= \''.$date.'\'';

			$maxKupyeo = $conn->get_data($sql);

			#본인부담율
			if ($level == '9'){ //등급외
				$boninYul = '100.0';
				$sugupGbn = '1';
			}else{ //1~3등급
				if ($sugupGbn == '3'){ //기초
					$boninYul = '0.0';
				}else if($sugupGbn == '1'){ //일반
					$boninYul = '15.0';
				}else {
					$boninYul = '7.5';
				}
			}
				
			#본인부담금
			$boninGum = $myF->cutOff($max_kupyeo * $boninYul / 100);


			$count = $conn->get_data("select count(*) from m03sugupja where m03_ccode = '$mCode' and m03_jumin = '$jumin'");

			if ($mDuplicate == 'N'){
				if ($count == 0){

					$reg_cnt ++;

					$sql = "insert into m03sugupja (
							 m03_ccode
							,m03_mkind
							,m03_sdate
							,m03_edate
							,m03_name
							,m03_jumin
							,m03_hp
							,m03_tel
							,m03_juso1
							,m03_juso2
							,m03_yboho_name
							,m03_yboho_phone
							,m03_yboho_gwange
							,m03_sugup_status
							,m03_gaeyak_fm
							,m03_gaeyak_to
							,m03_stat_nogood
							,m03_yoyangsa1_nm
							,m03_yoyangsa1
							,m03_partner
							,m03_yoyangsa2_nm
							,m03_yoyangsa2
							,m03_injung_no
							,m03_injung_from
							,m03_injung_to
							,m03_ylvl
							,m03_skind
							,m03_kupyeo_1
							,m03_kupyeo_2
							,m03_bonin_yul
							,m03_key
							) values (
							 '$mCode'
							,'$mKind'
							,'$contractDt'
							,'99991231'
							,'$name'
							,'$jumin'
							,'$mobile'
							,'$phone'
							,'$addr1'
							,'$addr2'
							,'$bohojaName'
							,'$bohojaPhone'
							,'$bohojaGwange'
							,'1'
							,'$contractDt'
							,'99991231'
							,'$nintyYn'
							,'$main_yoy'
							,'".$yoy1[1]."'
							,'$partner'
							,'$bu_yoy'
							,'".$yoy2[1]."'
							,'$injungNo'
							,'$injungFmDt'
							,'$injungToDt'
							,'$level'
							,'$sugupGbn'
							,'$maxKupyeo'
							,'$boninGum'
							,'$boninYul'
							,'$key')";

					if (!$conn->execute($sql)){
						$conn->rollback();
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}
			}else{

				$modify_cnt ++;

				if ($count == 0){

					$reg_cnt ++;

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
					if (!$conn->execute($sql)){
						$conn->rollback();
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}

				$sql = "update m03sugupja
						   set m03_name			= '".$name."'
						,      m03_jumin		= '".$jumin."'
						,      m03_hp			= '".$mobile."'
						,      m03_sdate		= '".$contractDt."'
						,      m03_edate		= '99991231'
						,      m03_tel			= '".$phone."'
						,      m03_juso1		= '".$addr1."'
						,      m03_juso2		= '".$addr2."'
						,      m03_yboho_name	= '".$bohojaName."'
						,      m03_yboho_gwange	= '".$bohojaGwange."'
						,      m03_yboho_phone	= '".$bohojaPhone."'
						,	   m03_sugup_status = '1'
						,	   m03_gaeyak_fm	= '".$contractDt."'
						,	   m03_gaeyak_to	= '99991231'
						,	   m03_stat_nogood	= '".$nintyYn."'
						,	   m03_yoyangsa1_nm	= '".$main_yoy."'
						,	   m03_yoyangsa1	= '".$yoy1[1]."'
						,	   m03_partner		= '".$partner."'
						,	   m03_yoyangsa2_nm	= '".$bu_yoy."'
						,	   m03_yoyangsa2	= '".$yoy2[1]."'
						,	   m03_injung_no	= '".$injungNo."'
						,	   m03_injung_from	= '".$injungFmDt."'
						,	   m03_injung_to	= '".$injungToDt."'
						,	   m03_ylvl			= '".$level."'
						,	   m03_skind		= '".$sugupGbn."'
						,	   m03_kupyeo_1		= '".$maxKupyeo."'
						,	   m03_kupyeo_2		= '".$boninGum."'
						,	   m03_bonin_yul	= '".$boninYul."'
						 where m03_ccode		= '".$mCode."'
						   and m03_mkind		= '".$mKind."'
						   and m03_jumin		= '".$jumin."'";
				if (!$conn->execute($sql)){
					$conn->rollback();
					echo $conn->err_back();
					if ($conn->mode == 1) exit;
				}
			}
		}
	}

	/*
	if($error != ''){
		$error .= '주민번호를 잘 못 입력하셨습니다. 확인 후 다시 등록해주세요.';
		echo "<script language='javascript'>
				alert('".$error."');
				history.back();
			  </script>";
		exit;
	}
	*/

	$skip_cnt = ($read_cnt - $reg_cnt);
	$modify_cnt = ($modify_cnt - $reg_cnt);

	$confirm = 'read 건수 : '.$read_cnt.'건\nskip(중복) 건수 : '.$skip_cnt.'건\nupload(등록) 건수 : '.$reg_cnt.'건\nmodify(수정) 건수 : '.$modify_cnt.'건\n\n등록이 완료되었습니다.';

	/*
	if (is_file('./Excel/'.$mFile)){
		unlink('./Excel/'.$mFile);
	}
	*/
	$conn->commit();

	include('../inc/_db_close.php');

	echo '
		<script language="javascript">
			alert("'.$confirm.'");
			location.replace("../sugupja/client_list.php");
		</script>
		 ';

?>