<?
	include("../inc/_db_open.php");
	include("../inc/_myFun.php");

	//$conn->set_name('euckr');

	header('Content-type: text/html; charset=utf-8'); // 문자셋
	//header('Content-type: text/html; charset=euc-kr');

	require_once '../excel/Excel/reader.php';

	$excel = new Spreadsheet_Excel_Reader();
	$excel->setOutputEncoding('UTF-8'); // 문자셋
	//$excel->setOutputEncoding('EUC-KR'); // 문자셋
	$excel->read('./Excel/2011-1.xls');

	error_reporting(E_ALL ^ E_NOTICE);
	
	
	/*
	echo 'file is ';

	if (is_file('./Excel/'.$mFile)){
		echo 'true';
	}else{
		echo 'false';
	}
	echo '<br>';

	echo 'file name : '.$mFile.'<br>';

	echo 'cnt : '.sizeof($excel->sheets);

	echo '<br>';

	echo 'rows : '.$excel->sheets[0]['numRows'];

	echo '<br>';

	print_r($excel);

	exit;
	*/

	$conn->begin();
	
	echo $excel->sheets[0]['numRows']; exit;
		
	for($i=5; $i<=$excel->sheets[0]['numRows']; $i++){
		$name = $excel->sheets[0]['cells'][$i][2]; //성명
		
		$sql = 'select m03_jumin
				  from m03sugupja
				 where m03_name = \''.$name.'\'';
		$jumin = $conn -> get_data($sql);
	
		
		if($jumin != ''){
			/*
			$level = $excel->sheets[0]['cells'][$i][3];
			$svc   = $excel->sheets[0]['cells'][$i][4];
			$from_time = $excel->sheets[0]['cells'][$i][5];
			$to_time = $excel->sheets[0]['cells'][$i][5];
			$count = $excel->sheets[0]['cells'][$i][6];
			$suga  = $excel->sheets[0]['cells'][$i][7];
			$tot_suga = $excel->sheets[0]['cells'][$i][8];
			$yoyangsa = $excel->sheets[0]['cells'][$i][9];
			$sudang = $excel->sheets[0]['cells'][$i][10];
			$tot_sudang = $excel->sheets[0]['cells'][$i][11];
			$date = $excel->sheets[0]['cells'][$i][12];
			*/
			

			/*
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
			*/	
		}

	}


	if (is_file('./Excel/'.$mFile)){
		unlink('./Excel/'.$mFile);
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