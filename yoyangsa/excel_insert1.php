<?
	include("../inc/_db_open.php");

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

	/**/
	$sql = "select ifnull(max(m02_key), 0)
			  from m02yoyangsa
			 where m02_ccode = '$mCode'
			   and m02_mkind = '$mKind'";
	$key = $conn->get_data($sql);
	
	for($i=4; $i<=$excel->sheets[0]['numRows']; $i++){
		$key ++;
		
		
		$name = $excel->sheets[0]['cells'][$i][1]; //성명
		$jumin = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][2]); //주민번호
		if($jumin != ''){
			$mobile = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][3]); //핸드폰
			$yjagyukno = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][4]); //자격증번호
			$postno = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][5]); //우편번호
			$addr1 = $excel->sheets[0]['cells'][$i][6]; //주소
			$addr2 = $excel->sheets[0]['cells'][$i][7]; //상세주소
			$form	= $excel->sheets[0]['cells'][$i][8]; //고용형태
			$status = $excel->sheets[0]['cells'][$i][9] != '' ? $excel->sheets[0]['cells'][$i][9] : '1'; //고용상태
			$inDate =  preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][10]); //입사일
			if($status == '9'){
				$outDate = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][11]); //퇴사일
			}else {
				$outDate = '99999999';
			}
			$weekDay = ' '.$excel->sheets[0]['cells'][$i][12]; //근무가능요일
			$payKind = $excel->sheets[0]['cells'][$i][13]; // 급여산정방식종류
			$yFamCareUmu = ($excel->sheets[0]['cells'][$i][15] == '유' ? 'Y' : 'N'); // 동거케어유무
			$yFamCareType = $excel->sheets[0]['cells'][$i][16]; // 동거케어유형
			$yFamCarePay = $excel->sheets[0]['cells'][$i][17]; // 동거케어급여액
			$bankNo = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][18]); //급여지급계좌번호
		
			if($excel->sheets[0]['cells'][$i][19] == '한국은행'){$bankName = '001';}
			else if($excel->sheets[0]['cells'][$i][19] == '산업은행'){$bankName = '002';}
			else if($excel->sheets[0]['cells'][$i][19] == '기업은행'){$bankName = '003';}
			else if($excel->sheets[0]['cells'][$i][19] == '국민은행'){$bankName = '004';}
			else if($excel->sheets[0]['cells'][$i][19] == '외환은행'){$bankName = '005';}
			else if($excel->sheets[0]['cells'][$i][19] == '수협중앙회'){$bankName = '007';}
			else if($excel->sheets[0]['cells'][$i][19] == '수출입은행'){$bankName = '008';}
			else if($excel->sheets[0]['cells'][$i][19] == '농협중앙회'){$bankName = '011';}
			else if($excel->sheets[0]['cells'][$i][19] == '농협회원조합'){$bankName = '012';}
			else if($excel->sheets[0]['cells'][$i][19] == '우리은행'){$bankName = '020';}
			else if($excel->sheets[0]['cells'][$i][19] == 'SC제일은행'){$bankName = '023';}
			else if($excel->sheets[0]['cells'][$i][19] == '한국씨티은행'){$bankName = '027';}
			else if($excel->sheets[0]['cells'][$i][19] == '대구은행'){$bankName = '031';}
			else if($excel->sheets[0]['cells'][$i][19] == '부산은행'){$bankName = '032';}
			else if($excel->sheets[0]['cells'][$i][19] == '광주은행'){$bankName = '034';}
			else if($excel->sheets[0]['cells'][$i][19] == '제주은행'){$bankName = '035';}
			else if($excel->sheets[0]['cells'][$i][19] == '전북은행'){$bankName = '037';}
			else if($excel->sheets[0]['cells'][$i][19] == '경남은행'){$bankName = '039';}
			else if($excel->sheets[0]['cells'][$i][19] == '새마을금고연합회'){$bankName = '045';}
			else if($excel->sheets[0]['cells'][$i][19] == '신협중앙회'){$bankName = '048';}
			else if($excel->sheets[0]['cells'][$i][19] == '상호저축은행'){$bankName = '050';}
			else if($excel->sheets[0]['cells'][$i][19] == '우체국'){$bankName = '071';}
			else if($excel->sheets[0]['cells'][$i][19] == '하나은행'){$bankName = '081';}
			else if($excel->sheets[0]['cells'][$i][19] == '신한은행'){$bankName = '088';}
			
			$npAmt = str_replace(',', '', $excel->sheets[0]['cells'][$i][20]); //국민연금신고월급여액
			$bohumYN = ($excel->sheets[0]['cells'][$i][21] == '유' ? 'Y' : 'N'); //4대보험가입여부
			$insYn = ($excel->sheets[0]['cells'][$i][22] == '유' ? 'Y' : 'N'); //배상책임보험유무
			$insFromDate = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $excel->sheets[0]['cells'][$i][23]); //보험시작일
			$insToDate = '99999999'; //보험만료일
			$jikwonGbn = $excel->sheets[0]['cells'][$i][25]; //직원여부(모바일용)
			

			// 시급고정급여부
			if ($payKind == '1'){
				$payType = 'Y';
				$payAmt = str_replace(',', '', $excel->sheets[0]['cells'][$i][14]); //시급액/월급액/총액비율
				$payRate  = 0;
			}else if ($payKind == '2'){
				$payKind = '1';
				$payType = 'N';
				// 변동 시급 삭제
				$sql = "delete
						  from m02pay
						 where m02_ccode = '$mCode'
						   and m02_mkind = '$mKind'
						   and m02_jumin = '$jumin'";
				$conn->execute($sql);
				//echo $sql.'<br>';

				$pay = str_replace(',', '', $excel->sheets[0]['cells'][$i][14]);
				$payArr = explode('/', $pay);

				// 변동 시급 저장
				$sql = "insert into m02pay values
						 ('$mCode', '$mKind', '$jumin', '1', '".$payArr[0]."')
						,('$mCode', '$mKind', '$jumin', '2', '".$payArr[1]."')
						,('$mCode', '$mKind', '$jumin', '3', '".$payArr[2]."')
						,('$mCode', '$mKind', '$jumin', '9', '".$payArr[3]."')";
				$conn->execute($sql);

				$payAmt = $payArr[0];
				$payRate  = 0;
			}else if ($payKind == '4'){
				$payKind = '3';
				$payType = ' ';
				$payAmt = str_replace(',', '', $excel->sheets[0]['cells'][$i][14]); //시급액/월급액/총액비율
				$payRate  = 0;
			}else if ($payKind == '3'){
				$payKind = '4';
				$payType = ' ';
				$payRate  = str_replace(',', '', $excel->sheets[0]['cells'][$i][14]); //시급액/월급액/총액비율
				$payAmt = 0;
			}else{
				$payKind = '0';
				$payType = ' ';
				$payAmt = 0;
				$payRate  = 0;
			}	
			
			$mon = (strPos($weekDay, '월') != null ? 'Y' : 'N');
			$tue = (strPos($weekDay, '화') != null ? 'Y' : 'N');
			$wed = (strPos($weekDay, '수') != null ? 'Y' : 'N');
			$thu = (strPos($weekDay, '목') != null ? 'Y' : 'N');
			$fri = (strPos($weekDay, '금') != null ? 'Y' : 'N');
			$sat = (strPos($weekDay, '토') != null ? 'Y' : 'N');
			$sun = (strPos($weekDay, '일') != null ? 'Y' : 'N');		

			$count = $conn->get_data("select count(*) from m02yoyangsa where m02_ccode = '$mCode' and m02_mkind = '$mKind' and m02_yjumin = '$jumin'");
			
			if($mDuplicate == 'N'){
				if ($count == 0){
					$sql = "insert into m02yoyangsa (
							 m02_ccode
							,m02_mkind
							,m02_yjumin
							,m02_yname
							,m02_ytel
							,m02_yjagyuk_no
							,m02_ypostno
							,m02_yjuso1
							,m02_yjuso2
							,m02_ygoyong_kind
							,m02_ygoyong_stat
							,m02_yipsail
							,m02_ytoisail
							,m02_ygunmu_mon
							,m02_ygunmu_tue
							,m02_ygunmu_wed
							,m02_ygunmu_thu
							,m02_ygunmu_fri
							,m02_ygunmu_sat
							,m02_ygunmu_sun
							,m02_ygupyeo_kind
							,m02_pay_type
							,m02_yfamcare_umu
							,m02_yfamcare_pay
							,m02_yfamcare_type
							,m02_ygibonkup
							,m02_ysuga_yoyul
							,m02_ygyeoja_no
							,m02_ybank_name
							,m02_ykuksin_mpay
							,m02_ins_yn
							,m02_ins_from_date
							,m02_ins_to_date
							,m02_y4bohum_umu
							,m02_ygobohum_umu
							,m02_ysnbohum_umu
							,m02_ygnbohum_umu
							,m02_ykmbohum_umu
							,m02_jikwon_gbn
							,m02_key
							) values (
							 '$mCode'
							,'$mKind'
							,'$jumin'
							,'$name'
							,'$mobile'
							,'$yjagyukno'
							,'$postno'
							,'$addr1'
							,'$addr2'
							,'$form'
							,'$status'
							,'$inDate'
							,'$outDate'
							,'$mon'
							,'$tue'
							,'$wed'
							,'$thu'
							,'$fri'
							,'$sat'
							,'$sun'
							,'$payKind'
							,'$payType'
							,'$yFamCareUmu'
							,'$yFamCarePay'
							,'$yFamCareType'
							,'$payAmt'
							,'$payRate'
							,'$bankNo'
							,'$bankName'
							,'$npAmt'
							,'$insYn'
							,'$insFromDate'
							,'$insToDate'
							,'$bohumYN'
							,'$bohumYN'
							,'$bohumYN'
							,'$bohumYN'
							,'$bohumYN'
							,'$jikwonGbn'
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
					echo $sql.'<br>';

					$sql = "insert into g03insapply values (
								'".$insFromDate."'
								,''
								,'".$mCode."'
								,'".$mKind."'
								,'".$jumin."'
								,'".$name."'
								,'1'
								,'')";
						$conn->execute($sql);
						//echo $sql.'<br>';

				}
			}else {
				if ($count == 0){
					$sql = "insert into m02yoyangsa (
							 m02_ccode
							,m02_mkind
							,m02_yjumin
							,m02_key
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

					$sql = "insert into g03insapply values (
								'".$insFromDate."'
								,''
								,'".$mCode."'
								,'".$mKind."'
								,'".$jumin."'
								,'".$name."'
								,'1'
								,'')";
						$conn->execute($sql);
						//echo $sql.'<br>';

				}
				$sql = "update m02yoyangsa
						   set m02_yname			 = '".$name."'
						,      m02_ytel				 = '".$mobile."'
						,      m02_yjagyuk_no		 = '".$yjagyukno."'
						,      m02_ypostno			 = '".$postno."'
						,      m02_yjuso1			 = '".$addr1."'
						,      m02_yjuso2			 = '".$addr2."'
						,      m02_ygoyong_kind      = '".$form."'
						,      m02_ygoyong_stat      = '".$status."'
						,      m02_yipsail			 = '".$inDate."'
						,      m02_ytoisail			 = '".$outDate."'
						,      m02_ygunmu_mon		 = '".$mon."'
						,      m02_ygunmu_tue        = '".$tue."'
						,      m02_ygunmu_wed        = '".$wed."'
						,      m02_ygunmu_thu        = '".$thu."'
						,      m02_ygunmu_fri        = '".$fri."'
						,      m02_ygunmu_sat        = '".$sat."'
						,      m02_ygunmu_sun        = '".$sun."'
						,      m02_ygupyeo_kind		 = '".$payKind."'
						,      m02_pay_type			 = '".$payType."'
						,      m02_yfamcare_umu		 = '".$yFamCareUmu."'
						,      m02_yfamcare_pay		 = '".$yFamCarePay."'
						,      m02_yfamcare_type     = '".$yFamCareType."'
						,      m02_ygibonkup		 = '".$payAmt."'
						,      m02_ysuga_yoyul       = '".$payRate."'
						,      m02_ygyeoja_no        = '".$bankNo."'
						,      m02_ybank_name        = '".$bankName."'
						,      m02_ykuksin_mpay      = '".$npAmt."'
						,      m02_ins_yn			 = '".$insYn."'
						,      m02_ins_from_date	 = '".$insFromDate."'
						,      m02_ins_to_date		 = '".$insToDate."'
						,      m02_y4bohum_umu       = '".$bohumYN."'
						,      m02_ygobohum_umu		= '".$bohumYN."'
						,      m02_ysnbohum_umu		= '".$bohumYN."'
						,      m02_ygnbohum_umu		= '".$bohumYN."'
						,      m02_ykmbohum_umu		= '".$bohumYN."'
						,      m02_jikwon_gbn		 = '".$jikwonGbn."'
						where  m02_ccode			 = '".$mCode."'
						  and  m02_mkind			 = '".$mKind."'
						  and  m02_yjumin			 = '".$jumin."'";
				$conn->execute($sql);
			}
		
		}
		
	}
	/**/
	
	$conn->commit();

	include("../inc/_db_close.php");

	echo '
		<script language="javascript">
			alert("입력이 완료되었습니다.");
		//	location.replace("../main/main.php?gubun=search");
		//	location.replace("../yoyangsa/list.php");
		</script>
		 ';
?>