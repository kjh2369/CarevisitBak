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

	$read_cnt = ($row_count-2); //읽은건수
	$reg_cnt = 0;	//업로드등록건수

	for($i=0; $i<=$row_count; $i++){
		$key ++;

		//$no  = $row[$i][0];
		$name  = $row[$i][1];
		$jumin = ereg_replace("[^0-9]", "", $row[$i][2]);

		if($jumin != ''){
			$mobile			= ereg_replace("[^0-9]", "", $row[$i][3]); //핸펀번호
			$phone			= ereg_replace("[^0-9]", "", $row[$i][4]); //자택전화번호
			$postNo         = ereg_replace("[^0-9]", "", $row[$i][5]); //우편번호
			$addr1			= $row[$i][6]; //주소
			$addr2			= $row[$i][7]; //상세주소
			$bohojaName		= $row[$i][8]; //보호자성명
			$bohojaGwange	= $row[$i][9]; //수급자와의관계
			$bohojaPhone	= ereg_replace("[^0-9]", "", $row[$i][10]); //보호자연락처
			$svc_kind		= ereg_replace("[^0-9]", "", $row[$i][11]); //이용서비스
			$svc_stat		= ereg_replace("[^0-9]", "", $row[$i][12]); //이용상태
			$svc_reason		= ereg_replace("[^0-9]", "", $row[$i][13]); //중지사유
			$contractFrom	= ereg_replace("[^0-9]", "", $row[$i][14]); //계약시작일자
			$contractTo		= ereg_replace("[^0-9]", "", $row[$i][15]); //계약종료일자
			$nintyYn		= $row[$i][16];	//90분가능여부
			$main_yoy		= preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $row[$i][17]);   //주담당
			$partner		= $row[$i][18];	//배우자
			$bu_yoy			= preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $row[$i][19]);   //부담당
			$injungNo		= $row[$i][20];   //장기요양인정서정보(인정번호)
			$injungFmDt		= ereg_replace("[^0-9]", "", $row[$i][21]);   //장기요양인증서정보(유효시작일)
			$injungToDt		= ereg_replace("[^0-9]", "", $row[$i][22]);   //장기요양인증서정보(유효종료일)
			$level			= ereg_replace("[^0-9]", "", $row[$i][23]);   //장기요양인증서정보(요양등급)
			$sugupGbn		= ereg_replace("[^0-9]", "", $row[$i][24]);   //본인부담금정보(수급자구분)
			$boninFrom		= ereg_replace("[^0-9]", "", $row[$i][25]);   //본인부담금정보(적용시작일)
			$boninTo		= ereg_replace("[^0-9]", "", $row[$i][26]);   //본인부담금정보(적용종료일)
			$chunguMaxPay	= $row[$i][27];   //청구한도정보(청구한도금액)
			$chunguFrom		= ereg_replace("[^0-9]", "", $row[$i][28]);   //청구한도정보(적용시작일)
			$chunguTo		= ereg_replace("[^0-9]", "", $row[$i][29]);   //청구한도정보(적용종료일)
		
			$contractFrom = ($contractFrom != '' ? $contractFrom : $injungFmDt);	//계약시작일자
			$contractTo   = ($contractTo   != '' ? $contractTo : $injungToDt);		//계약종료일자
			$injungFmDt   = ($injungFmDt   != '' ? $injungFmDt : $contractFrom);	//장기요양인정유효시작일자
			$injungToDt	  = ($injungToDt   != '' ? $injungToDt : $contractTo);		//장기요양인정유효종료일자
			$boninFrom    = ($boninFrom    != '' ? $boninFrom  : $injungFmDt);		//본인부담금적용시작일자
			$boninTo	  = ($boninTo	  != '' ? $boninTo	  : $injungToDt);		//본인부담금적용종료일자
			$chunguFrom   = ($chunguFrom   != '' ? $chunguFrom : $injungFmDt);		//청구한도적용시작일자
			$chunguTo	  = ($chunguTo	  != '' ? $chunguTo   : $injungToDt);		//청구한도적용종료일자
			
			if($contractFrom != ''){
				$contractTo = ($contractTo != '' ? $contractTo : '99991231');	//계약종료일자
				$injungToDt = ($injungToDt != '' ? $injungToDt : '99991231');	//장기요양인정유효종료일자	
				$boninTo    = ($boninTo    != '' ? $boninTo : '99991231');		//본인부담금적용종료일자
				$chunguTo   = ($chunguTo   != '' ? $chunguTo : '99991231');		//청구한도적용종료일자
			}

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
				$mKind = 0;
			}	
			

			switch($svc_reason){
				case 1:
					$svc_reason = '01';
					break;
				case 2:
					$svc_reason = '02';
					break;
				case 3:
					$svc_reason = '03';
					break;
				case 4:
					$svc_reason = '04';
					break;
				case 5:
					$svc_reason = '05';
					break;
				case 6:
					$svc_reason = '06';
					break;
				case 7:
					$svc_reason = '99';
					break;
				default:
					$svc_reason = '';
					break;
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
			
			$chunguMaxPay = $chunguMaxPay != '' ? $chunguMaxPay : $maxKupyeo; 

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
			
			//수급자등록 여부 카운트
			$count = $conn->get_data("select count(*) from m03sugupja where m03_ccode = '$mCode' and m03_jumin = '$jumin'");
			
			//이용상태및서비스계약기간적용 여부 카운트
			$svc_count = $conn->get_data("select max(seq) from client_his_svc where org_no = '$mCode' and jumin  = '$jumin' and svc_cd = '$mKind'");
			
			//장기요양인정정보등록 여부 카운트
			$lvl_count = $conn->get_data("select max(seq) from client_his_lvl where org_no = '$mCode' and jumin  = '$jumin' and svc_cd = '$mKind'");
			
			//장기요양인정정보등록 여부 카운트
			$kind_count = $conn->get_data("select max(seq) from client_his_kind where org_no = '$mCode' and jumin  = '$jumin'");

			//청구한도금액
			$limit_count = $conn->get_data("select max(seq) from client_his_limit where org_no = '$mCode' and jumin  = '$jumin'");

			if ($mDuplicate == 'N'){
				if ($count == 0){
					
					$reg_cnt ++;
					
					/************************************************
			
					#고객정보 등록
					
					*************************************************/

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
							,'$contractFrom'
							,'$contractTo'
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
							,'$contractFrom'
							,'$contractTo'
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
				
				

				/************************************************
			
				#고객상태및서비스계약기간 등록
				
				*************************************************/
			
				if($svc_count == 0){
					$sql = 'select ifnull(max(seq),0)+1
							  from client_his_svc
							 where org_no = \''.$mCode.'\'
							   and jumin  = \''.$jumin.'\'
							   and svc_cd = \''.$mKind.'\'';

					$seq = $conn->get_data($sql);

					$sql = 'insert into client_his_svc (
							 org_no
							,jumin
							,seq
							,from_dt
							,to_dt
							,svc_cd
							,svc_stat
							,svc_reason
							,insert_id
							,insert_dt) values (
							 \''.$mCode.'\'
							,\''.$jumin.'\'
							,\''.$seq.'\'
							,\''.$contractFrom.'\'
							,\''.$contractTo.'\'
							,\''.$mKind.'\'
							,\''.$svc_stat.'\'
							,\''.$svc_reason.'\'
							,\''.$mCode.'\'
							,now())';

					if (!$conn->execute($sql)){
						$conn->rollback();
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}
				
				
				/************************************************
			
				#장기요양인정정보 등록
				
				*************************************************/
			
				if($lvl_count == 0){
					$sql = 'select ifnull(max(seq),0)+1
							  from client_his_lvl
							 where org_no = \''.$mCode.'\'
							   and jumin  = \''.$jumin.'\'
							   and svc_cd = \''.$mKind.'\'';

					$seq = $conn->get_data($sql);

					$sql = 'insert into client_his_lvl (
							 org_no
							,jumin
							,svc_cd
							,seq
							,from_dt
							,to_dt
							,app_no
							,level
							,insert_id
							,insert_dt) values (
							 \''.$mCode.'\'
							,\''.$jumin.'\'
							,\''.$mKind.'\'
							,\''.$seq.'\'
							,\''.$injungFmDt.'\'
							,\''.$injungToDt.'\'
							,\''.$injungNo.'\'
							,\''.$level.'\'
							,\''.$mCode.'\'
							,now())';
					if (!$conn->execute($sql)){
						$conn->rollback();
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}
				
				
				/************************************************
			
				#수급구분이력 등록
				
				*************************************************/
			
				if($kind_count == 0){
					$sql = 'select ifnull(max(seq),0)+1
							  from client_his_kind
							 where org_no = \''.$mCode.'\'
							   and jumin  = \''.$jumin.'\'';

					$seq = $conn->get_data($sql);

					$sql = 'insert into client_his_kind (
							 org_no
							,jumin
							,seq
							,from_dt
							,to_dt
							,kind
							,rate
							,insert_id
							,insert_dt) values (
							 \''.$mCode.'\'
							,\''.$jumin.'\'
							,\''.$seq.'\'
							,\''.$boninFrom.'\'
							,\''.$boninTo.'\'
							,\''.$sugupGbn.'\'
							,\''.$boninYul.'\'
							,\''.$mCode.'\'
							,now())';
					if (!$conn->execute($sql)){
						$conn->rollback();
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}
				
				
				/************************************************
			
				#청구한도이력 등록
				
				*************************************************/
			
				if($limit_count == 0){
					$sql = 'select ifnull(max(seq),0)+1
							  from client_his_limit
							 where org_no = \''.$mCode.'\'
							   and jumin  = \''.$jumin.'\'';

					$seq = $conn->get_data($sql);

					$sql = 'insert into client_his_limit (
							 org_no
							,jumin
							,seq
							,from_dt
							,to_dt
							,amt
							,insert_id
							,insert_dt) values (
							 \''.$mCode.'\'
							,\''.$jumin.'\'
							,\''.$seq.'\'
							,\''.$chunguFrom.'\'
							,\''.$chunguTo.'\'
							,\''.$chunguMaxPay.'\'
							,\''.$mCode.'\'
							,now())';
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
				
				/************************************************
			
				#고객정보 수정
				
				*************************************************/

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
						,      m03_sdate		= '".$contractFrom."'
						,      m03_edate		= '".$contractTo."'
						,      m03_tel			= '".$phone."'
						,      m03_juso1		= '".$addr1."'
						,      m03_juso2		= '".$addr2."'
						,      m03_yboho_name	= '".$bohojaName."'
						,      m03_yboho_gwange	= '".$bohojaGwange."'
						,      m03_yboho_phone	= '".$bohojaPhone."'
						,	   m03_sugup_status = '1'
						,	   m03_gaeyak_fm	= '".$contractFrom."'
						,	   m03_gaeyak_to	= '".$contractTo."'
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
			
			
				/************************************************
				
				#고객상태 및 계약기간 등록 및 수정
				
				*************************************************/
		
				if($svc_count == 0){
					$sql = 'select ifnull(max(seq),0)+1
							  from client_his_svc
							 where org_no = \''.$mCode.'\'
							   and jumin  = \''.$jumin.'\'
							   and svc_cd = \''.$mKind.'\'';
					$seq = $conn->get_data($sql);

					$sql = 'insert into client_his_svc (
							 org_no
							,jumin
							,seq
							,svc_cd
							,insert_id
							,insert_dt
							) values (
							 \''.$mCode.'\'
							,\''.$jumin.'\'
							,\''.$seq.'\'
							,\''.$mKind.'\'
							,\''.$mCode.'\'
							,now())';

					if (!$conn->execute($sql)){
						$conn->rollback();
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}
				
				
				$sql = 'update client_his_svc
						   set from_dt    = \''.$contractFrom.'\'
						,      to_dt      = \''.$contractTo.'\'
						,      svc_stat   = \''.$svc_stat.'\'
						,      svc_reason = \''.$svc_reason.'\'
						,      update_id  = \''.$mCode.'\'
						,      update_dt  = now()
						 where org_no     = \''.$mCode.'\'
						   and jumin      = \''.$jumin.'\'
						   and svc_cd     = \''.$mKind.'\'
						   and seq        = \''.$svc_count.'\'';
				
				if (!$conn->execute($sql)){
					$conn->rollback();
					echo $conn->err_back();
					if ($conn->mode == 1) exit;
				}
				
				/************************************************
				
				#장기요양인정이력 등록 및 수정
				
				*************************************************/
				
				if($lvl_count == 0){
					$sql = 'select ifnull(max(seq),0)+1
							  from client_his_lvl
							 where org_no = \''.$mCode.'\'
							   and jumin  = \''.$jumin.'\'
							   and svc_cd = \''.$mKind.'\'';

					$seq = $conn->get_data($sql);

					$sql = 'insert into client_his_lvl (
							 org_no
							,jumin
							,svc_cd
							,seq
							,insert_id
							,insert_dt) values (
							 \''.$mCode.'\'
							,\''.$jumin.'\'
							,\''.$mKind.'\'
							,\''.$seq.'\'
							,\''.$mCode.'\'
							,now())';
					if (!$conn->execute($sql)){
						$conn->rollback();
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}
				

				$sql = 'update client_his_lvl
						   set from_dt   = \''.$injungFmDt.'\'
						,      to_dt     = \''.$injungToDt.'\'
						,      app_no    = \''.$injungNo.'\'
						,      level     = \''.$level.'\'
						,      update_id = \''.$mCode.'\'
						,      update_dt = now()
						 where org_no    = \''.$mCode.'\'
						   and jumin     = \''.$jumin.'\'
						   and svc_cd    = \''.$mKind.'\'
						   and seq       = \''.$lvl_count.'\'';
				if (!$conn->execute($sql)){
					$conn->rollback();
					echo $conn->err_back();
					if ($conn->mode == 1) exit;
				}
				
				
				/************************************************
				
				#수급구분이력 등록 및 수정
				
				*************************************************/
				
				if($kind_count == 0){
					$sql = 'select ifnull(max(seq),0)+1
							  from client_his_kind
							 where org_no = \''.$mCode.'\'
							   and jumin  = \''.$jumin.'\'';

					$seq = $conn->get_data($sql);

					$sql = 'insert into client_his_kind (
							 org_no
							,jumin
							,seq
							,insert_id
							,insert_dt) values (
							 \''.$mCode.'\'
							,\''.$jumin.'\'
							,\''.$seq.'\'
							,\''.$mCode.'\'
							,now())';
					if (!$conn->execute($sql)){
						$conn->rollback();
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}
				
				$sql = 'update client_his_kind
						   set from_dt   = \''.$boninFrom.'\'
						,      to_dt     = \''.$boninTo.'\'
						,      kind      = \''.$sugupGbn.'\'
						,      rate      = \''.$boninYul.'\'
						,      update_id = \''.$mCode.'\'
						,      update_dt = now()
						 where org_no    = \''.$mCode.'\'
						   and jumin     = \''.$jumin.'\'
						   and seq       = \''.$kind_count.'\'';
				
				
				if (!$conn->execute($sql)){
					$conn->rollback();
					echo $conn->err_back();
					if ($conn->mode == 1) exit;
				}
		
				

				/************************************************
				
				#청구한도이력 등록 및 수정
				
				*************************************************/
				if($limit_count == 0){
					$sql = 'select ifnull(max(seq),0)+1
							  from client_his_limit
							 where org_no = \''.$mCode.'\'
							   and jumin  = \''.$jumin.'\'';

					$seq = $conn->get_data($sql);

					$sql = 'insert into client_his_limit (
							 org_no
							,jumin
							,seq
							,insert_id
							,insert_dt) values (
							 \''.$mCode.'\'
							,\''.$jumin.'\'
							,\''.$seq.'\'
							,\''.$mCode.'\'
							,now())';
					if (!$conn->execute($sql)){
						$conn->rollback();
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}
				
				
				$sql = 'update client_his_limit
						   set from_dt   = \''.$chunguFrom.'\'
						,      to_dt     = \''.($chunguTo != '' ? $chunguTo : '99991231').'\'
						,      amt       = \''.$chunguMaxPay.'\'
						,      update_id = \''.$mCode.'\'
						,      update_dt = now()
						 where org_no    = \''.$mCode.'\'
						   and jumin     = \''.$jumin.'\'
						   and seq       = \''.$limit_count.'\'';
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

	$skip_cnt = ($read_cnt - $reg_cnt); //중복건수
	$modify_cnt = ($modify_cnt - $reg_cnt);	//스킵건수

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