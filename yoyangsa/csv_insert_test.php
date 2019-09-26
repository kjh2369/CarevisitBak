<?
	include("../inc/_db_open.php");
	include("../inc/_myFun.php");

	//$conn->set_name('euckr');

	header('Content-type: text/html; charset=utf-8'); // 문자셋
	//header('Content-type: text/html; charset=euc-kr');

	$mCode = $_GET['p_code'];
	$mKind = $_GET['p_kind'];
	$mDuplicate = $_GET['duplicateYN'];
	$mFile = $_GET['p_file'];

	$file = $mFile;
	$row_no = 1;
	$row_id = 0;

	if (($handle = fopen($file, "r")) != FALSE) {	
		while(true){
			$str = fgets($handle);

			if ($row_no > 1){
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
	

	$row_count = sizeof($row);
	
	$read_cnt = ($row_count-1);
	$reg_cnt = 0;
	
	
	for($i=0; $i<=$row_count; $i++){
		
		$name = $row[$i][0]; //성명
		$jumin = ereg_replace("[^0-9]", "", $row[$i][1]); //주민번호	
	
		if($jumin != ''){
			$addr = $row[$i][2]; //주소
			
			/*
			$sql = "update m02yoyangsa
					   set m02_yjuso1			 = '강원도 ".$addr."'
					 where m02_ccode             = '34211000101'
					   and m02_yjumin			 = '".$jumin."'";
			//echo $sql; 
			//$conn->execute($sql);
			*/
			/*
			$sql = "update m03sugupja
					   set m03_juso1 = '강원도 ".$addr."'
					 where m03_ccode = '34211000101'
					   and m03_jumin = '".$jumin."'";
			
			$conn->execute($sql);
			*/
			//$count = $conn->get_data("select count(*) from m02yoyangsa where m02_ccode = '$mCode' and m02_mkind = '$mKind' and m02_yjumin = '$jumin'");
			/*
			if($mDuplicate == 'N'){
				if ($count == 0){

					$reg_cnt ++;

					$sql = "insert into m02yoyangsa (
							 m02_ccode
							,m02_mkind
							,m02_yjumin
							,m02_yname
							,m02_ytel
							,m02_yjuso1
							,m02_yjuso2
							,m02_ygoyong_kind
							,m02_ygoyong_stat
							,m02_yipsail
							,m02_yfamcare_umu
							,m02_y4bohum_umu
							,m02_ygobohum_umu
							,m02_ysnbohum_umu
							,m02_ygnbohum_umu
							,m02_ykmbohum_umu
							,m02_yjakuk_kind
							,m02_yjikjong
							,m02_ygunmu_mon
							,m02_ygunmu_tue
							,m02_ygunmu_wed
							,m02_ygunmu_thu
							,m02_ygunmu_fri
							,m02_ygunmu_sat
							,m02_ygunmu_sun
							,m02_stnd_work_time
							,m02_stnd_work_pay
							,m02_key
							) values (
							 '$mCode'
							,'$mKind'
							,'$jumin'
							,'$name'
							,'$mobile'
							,'$addr1'
							,'$addr2'
							,'$form'
							,'1'
							,'$inDate'
							,'$yFamCareUmu'
							,'$bohumYN'
							,'$bohumYN'
							,'$bohumYN'
							,'$bohumYN'
							,'$bohumYN'
							,'12'
							,'11'
							,'Y'
							,'Y'
							,'Y'
							,'Y'
							,'Y'
							,'Y'
							,'Y'
							,'$day_work_hour'
							,'$day_hourly'
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
			}else {
				
				$modify_cnt ++;

				if ($count == 0){
					
					$reg_cnt ++;

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
						,      m02_yjuso1			 = '".$addr1."'
						,      m02_yjuso2			 = '".$addr2."'
						,      m02_ygoyong_kind      = '".$form."'
						,      m02_ygoyong_stat      = '1'
						,      m02_yipsail			 = '".$inDate."'
						,      m02_yfamcare_umu		 = '".$yFamCareUmu."'
						,      m02_y4bohum_umu       = '".$bohumYN."'
						,      m02_ygobohum_umu		= '".$bohumYN."'
						,      m02_ysnbohum_umu		= '".$bohumYN."'
						,      m02_ygnbohum_umu		= '".$bohumYN."'
						,      m02_ykmbohum_umu		= '".$bohumYN."'
						,      m02_yjakuk_kind		 = '12'
						,      m02_yjikjong          = '11'
						,      m02_ygunmu_mon		 = 'Y'
						,      m02_ygunmu_tue        = 'Y'
						,      m02_ygunmu_wed        = 'Y'
						,      m02_ygunmu_thu        = 'Y'
						,      m02_ygunmu_fri        = 'Y'
						,      m02_ygunmu_sat        = 'Y'
						,      m02_ygunmu_sun        = 'Y'
						,	   m02_stnd_work_time    = '".$day_work_hour."'
						,	   m02_stnd_work_pay	 = '".$day_hourly."'
						where  m02_ccode			 = '".$mCode."'
						  and  m02_mkind			 = '".$mKind."'
						  and  m02_yjumin			 = '".$jumin."'";
				$conn->execute($sql);
				
			}
			
			
			$sql = "select seq
					  from mem_his
					 where jumin = '".$jumin."'
					   and join_dt = '".$inDate."'";
			$Seq = $conn -> get_data($sql);

			$sql = 'replace into mem_his (
								 org_no
								,jumin
								,seq
								,join_dt
								,quit_dt
								,leave_from
								,leave_to
								,com_no
								,mem_id
								,employ_type
								,employ_stat
								,ins_yn
								,annuity_yn
								,health_yn
								,sanje_yn
								,employ_yn) values (
								 \''.$mCode.'\'
								,\''.$jumin.'\'
								,\''.($Seq != '' ? $Seq : '1').'\'
								,\''.$inDate.'\'
								,null
								,null
								,null
								,\''.$comNo.'\'
								,\''.$memId.'\'
								,\''.$form.'\'
								,\'1\'
								,\''.$bohumYN.'\'
								,\''.$bohumYN.'\'
								,\''.$bohumYN.'\'
								,\''.$bohumYN.'\'
								,\''.$bohumYN.'\'
								)';

			$conn->execute($sql);
			
			*/
		}		
	}
	


	$skip_cnt = ($read_cnt - $reg_cnt);
	$modify_cnt = ($modify_cnt - $reg_cnt);
	
	$confirm = 'read 건수 : '.$read_cnt.'건\nskip(중복) 건수 : '.$skip_cnt.'건\nupload(등록) 건수 : '.$reg_cnt.'건\nmodify(수정) 건수 : '.$modify_cnt.'건\n\n등록이 완료되었습니다.'; 
	
	/*
	if (is_file('./Excel/'.$mFile)){
		unlink('./Excel/'.$mFile);
	}
	*/

	$conn->commit();

	include("../inc/_db_close.php");

	echo '
		<script language="javascript">
			alert("'.$confirm.'");
			location.replace("../main/main.php?gubun=search");
			location.replace("../yoyangsa/mem_list.php");
		</script>
		 ';
?>