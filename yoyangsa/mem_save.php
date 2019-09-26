<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_login.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$conn->mode = 1;

	$conn->debug = false;

	$mem_mode	= $_POST['mem_mode'];	//작업구분 0:등록, 1:수정
	$code		= $_POST['code'];		//기관코드
	$kind_list	= $_POST['kind_temp'];	//기관리스트
	$kind_temp	= $_POST['kind_temp'];
	$kind		= $kind_list[0];		//기관분류코드
	$kind_count = sizeof($kind_list);	//기관리스트갯수

	$menu_select = $_POST['menu_select']; //선택된 메뉴
	$stress_mode = $_POST['stress_mode']; //고충상담 모드
	$counselMode = $_POST['txtCounselMode'];

	// 주민번호
	if ($mem_mode == 0){
		$jumin = $_POST['ssn1'].$_POST['ssn2'];

		/*
			//주민번호 자리수 맞추기
			//주민번호 미등록 사용시 입력한 자리 뒤부터 순번으로 사용하기 위해서 작성됨.
			if (StrLen($jumin) != 13){
				$sql = 'SELECT	DISTINCT m02_yjumin AS jumin
						FROM	m02yoyangsa
						WHERE	m02_ccode = \''.$code.'\'
						AND		LEFT(m02_yjumin,'.StrLen($jumin).') = \''.$jumin.'\'
						ORDER	BY jumin';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();
				$no = 1;

				if ($rowCnt > 0){
					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);
						$tmp = '000000000000'.$no;
						$tmp = SubStr($tmp, StrLen($tmp) - 13, StrLen($tmp));
						$tmp = $jumin.SubStr($tmp,StrLen($jumin),StrLen($tmp));

						if ($tmp < $row['jumin']){
							$jumin = $tmp;
							echo $jumin;
							echo 'OK<br>';
							break;
						}

						$no ++;
					}
				}else{
					$tmp = '000000000000'.$no;
					$tmp = SubStr($tmp, StrLen($tmp) - 13, StrLen($tmp));
					$tmp = $jumin.SubStr($tmp,StrLen($jumin),StrLen($tmp));
					$jumin = $tmp;
				}

				$conn->row_free();
			}
		*/
	}else{
		$jumin = $ed->de($_POST['ssn']);
	}

	if (Empty($code) || Empty($jumin)){
		include('../inc/_http_home.php');
		exit;
	}

	$conn->begin();

	if ($mem_mode == 0){
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

	/*************************************

		이미지 저장

	*************************************/
		$pic = $_FILES['counsel_mem_picture'];
		$pic_back = $_POST['mem_picture_back'];

		$upload = false;

		if ($pic['tmp_name'] != ''){
			$tmp_info = pathinfo($pic['name']);
			$exp_nm = strtolower($tmp_info['extension']);
			$pic_nm = mktime().'.'.$exp_nm;

			if (move_uploaded_file($pic['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/mem_picture/'.$pic_nm)){
				// 업로드 성공
				$upload = true;
			}
		}

		#######################################
		#
		# 이미지 축소
		if ($upload){
			$original_path = '../mem_picture/'.$pic_nm;
			$img_w = 90;
			$img_h = 120;
			$img_s = getimagesize($original_path);

			/**************************************************

				가로, 세로 비율에 맞게 축소한다.

			**************************************************/
			if ($img_w < $img_s[0] || $img_h < $img_s[1]){
				if ($img_s[0] > $img_s[1]){
					$img_r = $img_s[1] / $img_s[0];
					$img_h = $img_h * $img_r;
				}else{
					$img_r = $img_s[0] / $img_s[1];
					$img_w = $img_w * $img_r;
				}
			}else{
				$img_w = $img_s[0];
				$img_h = $img_s[1];
			}

			switch($exp_nm){
				case 'jpg':
					$original_img = imageCreateFromJpeg($original_path);
					break;
				case 'png':
					$original_img = imageCreateFromPng($original_path);
					break;
				case 'gif':
					$original_img = imageCreateFromGif($original_path);
					break;
				case 'bmp':
					$original_img = imageCreateFromBmp($original_path);
					break;
			}

			// 새 이미트 틀작성
			$new_img = imageCreateTrueColor($img_w, $img_h);

			// 배경을 하얀색으로 설정
			$trans_colour = imageColorAllocate($new_img, 255,255,255);
			imageFill($new_img, 0, 0, $trans_colour);

			// 이미지 복사
			imageCopyReSampled($new_img, $original_img, 0, 0, 0, 0, $img_w, $img_h, $img_s[0], $img_s[1]);

			// 이미지 저장
			switch($exp_nm){
				case 'jpg':
					imageJpeg($new_img, $original_path);
					break;
				case 'png':
					imagePng($new_img, $original_path);
					break;
				case 'gif':
					imageGif($new_img, $original_path);
					break;
				case 'bmp':
					imageBmp($new_img, $original_path);
					break;
			}

			// 종료
			imageDestroy($new_img);
		}
		#
		#######################################

		// 업로드 실패시 파일명 삭제
		if (!$upload) $pic_nm = $pic_back;
	/************************************/

	for($i=0; $i<$kind_count; $i++){
		$kind_code = $kind_temp[$i]; //기관분류코드
		$pay_gbn   = $kind_code;     //급여분류코드

		$kind_exist = true;

		$sql = "select count(*)
				  from m02yoyangsa
				 where m02_ccode  = '$code'
				   and m02_mkind  = '$kind_code'
				   and m02_yjumin = '$jumin'";
		if ($conn->get_data($sql) == 0){
			$sql    = "insert into m02yoyangsa (m02_ccode, m02_mkind, m02_yjumin, m02_key) values ('$code', '$kind_code', '$jumin', '$key')";
			$conn->execute($sql);
		}

		/*
		// 스마트업구구분
		if ($_POST['smart_gbn_m'] == 'Y' && $_POST['smart_gbn_y'] == 'Y' && $_POST['smart_gbn_w'] == 'Y'){
			$smart_gbn = 'B'; //관리자 + 요양보호사 + 사회복지사
		}else if ($_POST['smart_gbn_m'] == 'Y' && $_POST['smart_gbn_y'] == 'Y'){
			$smart_gbn = 'A';	//관리자+요양보호사
		}else if ($_POST['smart_gbn_m'] == 'Y' && $_POST['smart_gbn_w'] == 'Y'){
			$smart_gbn = 'C';	//관리자+사회복지사
		}else if ($_POST['smart_gbn_y'] == 'Y' && $_POST['smart_gbn_w'] == 'Y'){
			$smart_gbn = 'D';	//요양보호사+사회복지사
		}else if ($_POST['smart_gbn_m'] == 'Y'){
			$smart_gbn = 'M';	//관리자
		}else if ($_POST['smart_gbn_y'] == 'Y'){
			$smart_gbn = 'Y';	//요양보호사
		}else if ($_POST['smart_gbn_w'] == 'Y'){
			$smart_gbn = 'W';	//사회복지사
		}else{
			$smart_gbn = ' ';	//미사용
		}


		$join_dt = str_replace('-', '', $_POST['join_dt']); //입사일
		$out_dt  = str_replace('-', '', $_POST['out_dt']);  //퇴사일

		if ($_POST['holiday_payrate_yn'] == 'Y' && $_POST['sunday_payrate_yn'] == 'Y'){
			$holiday_payrate_yn = 'Y';
		}else if ($_POST['holiday_payrate_yn'] == 'Y'){
			$holiday_payrate_yn = 'H';
		}else if ($_POST['sunday_payrate_yn'] == 'Y'){
			$holiday_payrate_yn = 'S';
		}else{
			$holiday_payrate_yn = 'N';
		}

		$sql = "update m02yoyangsa
				   set m02_yname			= '".$_POST['counsel_name']."'
				,      m02_mem_no           = '".$_POST['member_no']."'
				,      m02_ytel				= '".str_replace('-', '', $_POST['mem_mobile'])."'
				,      m02_ytel2			= '".str_replace('-', '', $_POST['mem_phone'])."'
				,      m02_email            = '".$_POST['mem_email']."'
				,      m02_ypostno			= '".$_POST['mem_postno1'].$_POST['mem_postno2']."'
				,      m02_yjuso1			= '".$_POST['mem_addr']."'
				,      m02_yjuso2			= '".$_POST['mem_addr_dtl']."'
				,      m02_yjikjong			= '".$_POST['job_kind']."'
				,      m02_ybank_name		= '".$_POST['bank_cd']."'
				,      m02_ybank_holder		= '".$_POST['acct_holder']."'
				,      m02_ygyeoja_no		= '".$_POST['acct_no']."'
				,      m02_y4bohum_umu		= '".$_POST['4ins_yn']."'
				,      m02_ygobohum_umu		= '".$_POST['employ_yn']."'
				,      m02_ysnbohum_umu		= '".$_POST['sanje_yn']."'
				,      m02_ygnbohum_umu		= '".$_POST['health_yn']."'
				,      m02_ykmbohum_umu		= '".$_POST['annuity_yn']."'
				,      m02_ygongjeja_no		= '".$_POST['gongjejano']."'
				,      m02_ygongjejaye_no	= '".$_POST['gonjejayeno']."'
				,      m02_ykuksin_mpay		= '".str_replace(',', '', $_POST['annuity_pay'])."'
				,      m02_health_mpay		= '".str_replace(',', '', $_POST['health_pay'])."'
				,      m02_employ_mpay		= '".str_replace(',', '', $_POST['employ_pay'])."'
				,      m02_sanje_mpay		= '".str_replace(',', '', $_POST['sanje_pay'])."'
				,      m02_jikwon_gbn		= '".$smart_gbn."'
				,      m02_ygoyong_kind		= '".$_POST['employ_kind']."'
				,      m02_ygoyong_stat		= '".$_POST['employ_stat']."'
				,      m02_yipsail			= '".$join_dt."'
				,      m02_ytoisail			= '".$out_dt."'
				,      m02_ygunmu_mon		= 'Y'
				,      m02_ygunmu_tue		= 'Y'
				,      m02_ygunmu_wed		= 'Y'
				,      m02_ygunmu_thu		= 'Y'
				,      m02_ygunmu_fri		= 'Y'
				,      m02_ygunmu_sat		= 'Y'
				,      m02_weekly_holiday   = '".$_POST['week_holiday']."'
				,      m02_bipay_yn         = '".$_POST['bipay_yn']."'
				,      m02_bipay_rate       = '".$_POST['bipay_rate']."'
				,      m02_ygunmu_sun		= 'Y'";

		if ($_SESSION['userLevel'] == 'C'){
			$sql .= ",      m02_ygupyeo_kind  = '".$pay_kind."'
					 ,      m02_pay_type	  = '".$pay_type."'
					 ,      m02_ygibonkup	  = '".str_replace(',', '', $pay_basic)."'
					 ,      m02_ysuga_yoyul	  = '".$pay_rate."'
					 ,      m02_yfamcare_umu  = '".$famcare_umu."'
					 ,      m02_yfamcare_pay  = '".str_replace(',', '', $famcare_pay)."'
					 ,      m02_yfamcare_type = '".$famcare_type."'
					 ,      m02_pay_step      = '".$_POST['pay_step']."'
					 ,      m02_bnpay_yn      = '".$_POST['ybnpay'.$i]."'
					 ,      m02_family_pay_yn = '".$_POST['familyPayYN']."'";
		}

		$sql .=",      m02_ins_yn			  = '".($_POST['ins_yn'] == 'Y' ? 'Y' : 'N')."'
				,      m02_ins_code			  = '".$_POST['ins_code']."'
				,      m02_ins_from_date	  = '".str_replace('-', '', $_POST['ins_from_dt'])."'
				,      m02_ins_to_date		  = '".str_replace('-', '', $_POST['ins_to_dt'])."'
				,      m02_rank_pay           = '".str_replace(',', '', $_POST['rank_pay'])."'
				,      m02_add_payrate        = '".str_replace(',', '', $_POST['add_payrate'])."'
				,      m02_holiday_payrate_yn = '".$holiday_payrate_yn."'
				,      m02_holiday_payrate    = '".str_replace(',', '', $_POST['holiday_payrate'])."'
				,      m02_ma_yn              = '".$_POST['ma_yn']."'
				,      m02_ma_dt              = '".str_replace('-', '', $_POST['ma_dt'])."'
				,      m02_stnd_work_time     = '".$_POST['fixedHours']."'
				,      m02_stnd_work_pay      = '".str_replace(',', '', $_POST['fixedHourly'])."'
				,      m02_dept_cd            = '".$_POST['dept']."'
				,      m02_memo               = '".addslashes($_POST['mem_memo'])."'
				,      m02_mobile_kind        = '".$_POST['mobile_kind']."'
				,      m02_model_no           = '".$_POST['modelNo']."'
				,      m02_rfid_yn            = '".$_POST['rfid_yn']."'
				,      m02_paye_yn            = '".$_POST['payeYN']."'
				,      m02_picture            = '".$pic_nm."'
				,      m02_meal_pay           = '".str_replace(',', '', $_POST['mealPay'])."'
				,      m02_car_pay            = '".str_replace(',', '', $_POST['carPay'])."'
				,      m02_del_yn             = '".($kind_exist ? 'N' : 'Y')."'
				 where m02_ccode			  = '".$code."'
				   and m02_mkind			  = '".$kind_code."'
				   and m02_yjumin			  = '".$jumin."'";

		$conn->execute($sql);
		*/
	}

	// 스마트업구구분
	if ($_POST['smart_gbn_m'] == 'Y' && $_POST['smart_gbn_y'] == 'Y' && $_POST['smart_gbn_w'] == 'Y'){
		$smart_gbn = 'B'; //관리자 + 요양보호사 + 사회복지사
	}else if ($_POST['smart_gbn_m'] == 'Y' && $_POST['smart_gbn_y'] == 'Y'){
		$smart_gbn = 'A';	//관리자+요양보호사
	}else if ($_POST['smart_gbn_m'] == 'Y' && $_POST['smart_gbn_w'] == 'Y'){
		$smart_gbn = 'C';	//관리자+사회복지사
	}else if ($_POST['smart_gbn_y'] == 'Y' && $_POST['smart_gbn_w'] == 'Y'){
		$smart_gbn = 'D';	//요양보호사+사회복지사
	}else if ($_POST['smart_gbn_m'] == 'Y'){
		$smart_gbn = 'M';	//관리자
	}else if ($_POST['smart_gbn_y'] == 'Y'){
		$smart_gbn = 'Y';	//요양보호사
	}else if ($_POST['smart_gbn_w'] == 'Y'){
		$smart_gbn = 'W';	//사회복지사
	}else{
		$smart_gbn = ' ';	//미사용
	}


	$join_dt = str_replace('-', '', $_POST['join_dt']); //입사일
	$out_dt  = str_replace('-', '', $_POST['out_dt']);  //퇴사일

	if ($_POST['holiday_payrate_yn'] == 'Y' && $_POST['sunday_payrate_yn'] == 'Y'){
		$holiday_payrate_yn = 'Y';
	}else if ($_POST['holiday_payrate_yn'] == 'Y'){
		$holiday_payrate_yn = 'H';
	}else if ($_POST['sunday_payrate_yn'] == 'Y'){
		$holiday_payrate_yn = 'S';
	}else{
		$holiday_payrate_yn = 'N';
	}

	/*
		$postno = $_POST['mem_postno1'].$_POST['mem_postno2'];
		$addr	= $_POST['mem_addr'];
		$addrDtl= $_POST['mem_addr_dtl'];
	*/
	$postno = $_POST['txtPostNo'];
	$addr	= $_POST['txtAddr'];
	$addrDtl= $_POST['txtAddrDtl'];

	$sql = "update m02yoyangsa
			   set m02_yname			= '".$_POST['counsel_name']."'
			,      m02_mem_no           = '".$_POST['member_no']."'
			,      m02_ytel				= '".str_replace('-', '', $_POST['mem_mobile'])."'
			,      m02_ytel2			= '".str_replace('-', '', $_POST['mem_phone'])."'
			,      m02_email            = '".$_POST['mem_email']."'
			,      m02_ypostno			= '".$postno."'
			,      m02_yjuso1			= '".$addr."'
			,      m02_yjuso2			= '".$addrDtl."'
			,      m02_yjikjong			= '".$_POST['job_kind']."'
			,      m02_ybank_name		= '".$_POST['bank_cd']."'
			,      m02_ybank_holder		= '".$_POST['acct_holder']."'
			,      m02_ygyeoja_no		= '".$_POST['acct_no']."'
			,      m02_y4bohum_umu		= '".$_POST['4ins_yn']."'
			,      m02_ygobohum_umu		= '".$_POST['employ_yn']."'
			,      m02_ysnbohum_umu		= '".$_POST['sanje_yn']."'
			,      m02_ygnbohum_umu		= '".$_POST['health_yn']."'
			,      m02_ykmbohum_umu		= '".$_POST['annuity_yn']."'
			,      m02_ygongjeja_no		= '".$_POST['gongjejano']."'
			,      m02_ygongjejaye_no	= '".$_POST['gonjejayeno']."'
			,      m02_ykuksin_mpay		= '".str_replace(',', '', $_POST['annuity_pay'])."'
			,      m02_health_mpay		= '".str_replace(',', '', $_POST['health_pay'])."'
			,      m02_employ_mpay		= '".str_replace(',', '', $_POST['employ_pay'])."'
			,      m02_sanje_mpay		= '".str_replace(',', '', $_POST['sanje_pay'])."'
			,      m02_jikwon_gbn		= '".$smart_gbn."'
			,      m02_ygoyong_kind		= '".$_POST['employ_kind']."'
			,      m02_ygoyong_stat		= '".$_POST['employ_stat']."'
			,      m02_yipsail			= '".$join_dt."'
			,      m02_ytoisail			= '".$out_dt."'
			,      m02_ygunmu_mon		= 'Y'
			,      m02_ygunmu_tue		= 'Y'
			,      m02_ygunmu_wed		= 'Y'
			,      m02_ygunmu_thu		= 'Y'
			,      m02_ygunmu_fri		= 'Y'
			,      m02_ygunmu_sat		= 'Y'
			,      m02_weekly_holiday   = '".$_POST['week_holiday']."'
			,      m02_bipay_yn         = '".$_POST['bipay_yn']."'
			,      m02_bipay_rate       = '".$_POST['bipay_rate']."'
			,      m02_ygunmu_sun		= 'Y'";

	if ($_SESSION['userLevel'] == 'C'){
		$sql .= ",      m02_ygupyeo_kind  = '".$pay_kind."'
				 ,      m02_pay_type	  = '".$pay_type."'
				 ,      m02_ygibonkup	  = '".str_replace(',', '', $pay_basic)."'
				 ,      m02_ysuga_yoyul	  = '".$pay_rate."'
				 ,      m02_yfamcare_umu  = '".$famcare_umu."'
				 ,      m02_yfamcare_pay  = '".str_replace(',', '', $famcare_pay)."'
				 ,      m02_yfamcare_type = '".$famcare_type."'
				 ,      m02_pay_step      = '".$_POST['pay_step']."'
				 ,      m02_bnpay_yn      = '".$_POST['ybnpay'.$i]."'
				 ,      m02_family_pay_yn = '".$_POST['familyPayYN']."'";
	}

	$sql .=",      m02_ins_yn			  = '".($_POST['ins_yn'] == 'Y' ? 'Y' : 'N')."'
			,      m02_ins_code			  = '".$_POST['ins_code']."'
			,      m02_ins_from_date	  = '".str_replace('-', '', $_POST['ins_from_dt'])."'
			,      m02_ins_to_date		  = '".str_replace('-', '', $_POST['ins_to_dt'])."'
			,      m02_rank_pay           = '".str_replace(',', '', $_POST['rank_pay'])."'
			,      m02_add_payrate        = '".str_replace(',', '', $_POST['add_payrate'])."'
			,      m02_holiday_payrate_yn = '".$holiday_payrate_yn."'
			,      m02_holiday_payrate    = '".str_replace(',', '', $_POST['holiday_payrate'])."'
			,      m02_ma_yn              = '".$_POST['ma_yn']."'
			,      m02_ma_dt              = '".str_replace('-', '', $_POST['ma_dt'])."'
			,      m02_stnd_work_time     = '".$_POST['fixedHours']."'
			,      m02_stnd_work_pay      = '".str_replace(',', '', $_POST['fixedHourly'])."'
			,      m02_dept_cd            = '".$_POST['dept']."'
			,      m02_memo               = '".addslashes($_POST['mem_memo'])."'
			,      m02_mobile_kind        = '".$_POST['mobile_kind']."'
			,      m02_model_no           = '".$_POST['modelNo']."'
			,      m02_rfid_yn            = '".$_POST['rfid_yn']."'
			,      m02_paye_yn            = '".$_POST['payeYN']."'
			,      m02_picture            = '".$pic_nm."'
			,      m02_meal_pay           = '".str_replace(',', '', $_POST['mealPay'])."'
			,      m02_car_pay            = '".str_replace(',', '', $_POST['carPay'])."'
			,      m02_del_yn             = 'N'
			 where m02_ccode			  = '".$code."'
			   and m02_yjumin			  = '".$jumin."'";

	$conn->execute($sql);



	/*********************************************************

		직원 히스토리 작성

	*********************************************************/
	if (!Empty($code) && !Empty($jumin)){
		$employNew	= $_POST['employ_stat'];   //현재 고용상태
		$employNow	= $_POST['objEmployStat']; //새 고용상태
		$hisSeq		= $_POST['memHisSeq'];     //과겨이력 순번
		$comNo		= $_POST['member_no'];     //사번
		$memNo      = $_POST['mem_no'];		   //직원번호
		$memId		= $_POST['memId'];         //아이디
		$workDt		= $_POST['workDt'];        //근무시작일자

		if ($memId == '미등록') $memId = '';

		if ($_POST['retire_join_flag'] != 'Y') $_POST['retire_join_dt'] = '';

		$sql = 'replace into mem_his (
				 org_no
				,jumin
				,seq
				,join_dt
				,quit_dt
				,leave_from
				,leave_to
				,com_no
				,mem_no
				,mem_id
				,employ_type
				,employ_stat
				,weekly
				,bank_acct
				,bank_no
				,bank_nm
				,prolong_rate
				,holiday_rate_gbn
				,holiday_rate
				,ins_yn
				,annuity_yn
				,health_yn
				,sanje_yn
				,employ_yn
				,paye_yn
				,annuity_amt
				,insu_yn
				,work_start_dt
				,compare_yn
				,compare_jobs
				,compare_jobstr
				,mem_pos
				,mem_work
				,nurse_yn
				,nurse_no
				,sw_yn
				,lsep_yn
				,retire_join_flag
				,retire_join_dt
				) values (
				 \''.$code.'\'
				,\''.$jumin.'\'
				,\''.$hisSeq.'\'
				,\''.$join_dt.'\'
				,'.(!empty($out_dt) ? '\''.$out_dt.'\'' : 'NULL').'
				,NULL
				,NULL
				,\''.$comNo.'\'
				,\''.$memNo.'\'
				,\''.$memId.'\'
				,\''.$_POST['employ_kind'].'\'
				,\''.$employNew.'\'
				,\''.$_POST['week_holiday'].'\'
				,\''.$_POST['acct_holder'].'\'
				,\''.$_POST['acct_no'].'\'
				,\''.$_POST['bank_cd'].'\'
				,\''.$_POST['add_payrate'].'\'
				,\''.$holiday_payrate_yn.'\'
				,\''.$_POST['holiday_payrate'].'\'
				,\''.$_POST['4ins_yn'].'\'
				,\''.$_POST['annuity_yn'].'\'
				,\''.$_POST['health_yn'].'\'
				,\''.$_POST['sanje_yn'].'\'
				,\''.$_POST['employ_yn'].'\'
				,\''.$_POST['payeYN'].'\'
				,\''.str_replace(',', '', $_POST['annuity_pay']).'\'
				,\''.($_POST['insYN'] == 'Y' ? 'Y' : 'N').'\'
				,'.(!Empty($workDt) ? '\''.$workDt.'\'' : 'NULL').'
				,\''.($_POST['compareYn'] == 'Y' ? 'Y' : 'N').'\'
				,\''.($_POST['compareJobs'] == 'Y' ? 'Y' : 'N').'\'
				,\''.$_POST['compareJobStr'].'\'
				,\''.$_POST['memPos'].'\'
				,\''.$_POST['memWork'].'\'
				,\''.($_POST['optNurseYn'] == 'Y' ? 'Y' : 'N').'\'
				,\''.$_POST['nurseNo'].'\'
				,\''.($_POST['optSwYn'] == 'Y' ? 'Y' : 'N').'\'
				,\''.($_POST['optLsepYn'] == 'Y' ? 'Y' : 'N').'\'
				,\''.($_POST['retire_join_flag'] == 'Y' ? 'Y' : 'N').'\'
				,\''.str_replace('-', '', $_POST['retire_join_dt']).'\'
				)';

		$conn->execute($sql);

		if ($debug){
			if ($mem_mode == '0'){
				if ($_POST['insYN'] == 'Y'){
					$sql = 'INSERT INTO insu (
							 org_no
							,jumin
							,seq
							,join_dt
							,start_dt
							,stat
							,insert_dt) VALUES (
							 \''.$code.'\'
							,\''.$jumin.'\'
							,\'1\'
							,\''.$myF->dateStyle($join_dt).'\'
							,\''.$myF->dateStyle($_POST['txtInsuFrom']).'\'
							,\'1\'
							,NOW())';
					$conn->execute($sql);
				}
			}
		}else{
			if ($mem_mode == '1'){
				//퇴사자 배상책임보험 해지신청
				if ($_POST['employ_stat'] == '9'){
					//퇴사
					$sql = 'UPDATE insu
							   SET quit_dt   = \''.$myF->dateStyle($out_dt).'\'
							,      end_dt    = \''.$myF->dateStyle($out_dt).'\'
							,      stat      = \'7\'
							,      insert_dt = NOW()
							 WHERE org_no    = \''.$code.'\'
							   AND jumin     = \''.$jumin.'\'
							   AND join_dt   = \''.$myF->dateStyle($join_dt).'\'
							   AND stat     != \'9\'';

					$conn->execute($sql);
				}
			}
		}
	}


	/*********************************************************
	 * 직원별 수당 금액
	 *********************************************************/
		if ($_SESSION['userLevel'] == 'C'){
			$sql = 'replace into mem_extra (
					 org_no
					,jumin
					,extra500_1
					,extra500_2
					,extra500_3
					,extra800_1
					,extra800_2
					,extra800_3) values (
					 \''.$code.'\'
					,\''.$jumin.'\'
					,\''.str_replace(',','',$_POST['txtExtra500_1']).'\'
					,\''.str_replace(',','',$_POST['txtExtra500_2']).'\'
					,\''.str_replace(',','',$_POST['txtExtra500_3']).'\'
					,\''.str_replace(',','',$_POST['txtExtra800_1']).'\'
					,\''.str_replace(',','',$_POST['txtExtra800_2']).'\'
					,\''.str_replace(',','',$_POST['txtExtra800_3']).'\')';
			$conn->execute($sql);
		}



	// 초기상담기록지
	include_once('../counsel/mem_counsel_save_sub.php');

	// 고충상담메뉴가 선택된어진 후 입력 모드면 고충상담을 저장한다.
		//if ($menu_select == 5 && $stress_mode == 0){
		//	include_once('../counsel/mem_stress_save.php');
		//}

		if ($menu_select == 5){
			if ($counselMode == '1'){
				include_once('../counsel/mem_stress_save.php');
			}else if ($counselMode == '2'){
				include_once('../counsel/client_counsel_stress_save.php');
			}else if ($counselMode == '3'){
				include_once('../counsel/client_counsel_case_save.php');
			}
		}


	/**************************************************

		인적사항 저장

	**************************************************/
	if ($menu_select == 6)
		include_once('../counsel/mem_human_save.php');



	/*********************************************************

		직원별 선택사항

		*****************************************************/
		if ($gHostNm == 'pr'){
			if (!empty($code) && !empty($jumin)){
				$lsSupportYn	= ($_POST['chkSupportYn'] == 'Y' ? 'Y' : 'N'); //재가지원
				$lsResponseYn	= ($_POST['chkResponseYn'] == 'Y' ? 'Y' : 'N'); //자원연계
				$lsWithoutpayYn	= ($_POST['chkWithoutpayYn'] == 'Y' ? 'Y' : 'N'); //무급직원
				$prgCostNotYn	= ($_POST['chkPrgCostNotYn'] == 'Y' ? 'Y' : 'N'); //치매수당 지급여부
				$retirementNotYn	= ($_POST['chkRetirementNotYn'] == 'Y' ? 'Y' : 'N'); //퇴직적립금 계산여부
				$jobfundsNotYn	= ($_POST['chkJobfundsNotYn'] == 'Y' ? 'Y' : 'N'); //일자리안정자금 적용여부
				$weeklyPayYn    = ($_POST['chkWeeklyPayYn'] == 'Y' ? 'Y' : 'N'); //주휴수당지급여부(돌보인가족케어만)
				$annaulPayYn    = ($_POST['chkAnnaulPayYn'] == 'Y' ? 'Y' : 'N'); //연차수당지급여부(돌보인가족케어만)

				$sql = 'update	mem_option
						set		support_yn		= \''.$lsSupportYn.'\'
						,		response_yn		= \''.$lsResponseYn.'\'
						,		withoutpay_yn	= \''.$lsWithoutpayYn.'\'
						,		prg_cost_not_yn	= \''.$prgCostNotYn.'\'
						,		retirement_not_yn = \''.$retirementNotYn.'\'
						,		jobfunds_not_yn = \''.$jobfundsNotYn.'\'
						,		weekly_pay_yn   = \''.$weeklyPayYn.'\'
						,		annual_pay_yn   = \''.$annaulPayYn.'\'
						where	org_no			= \''.$code.'\'
						and		mo_jumin		= \''.$jumin.'\'';

				$conn->execute($sql);
			}
		}else{
			if (!empty($code) && !empty($jumin)){
				$sql = 'select count(*)
						  from mem_option
						 where org_no   = \''.$code.'\'
						   and mo_jumin = \''.$jumin.'\'';

				if ($conn->get_data($sql) == 0){
					$sql = 'insert into mem_option (
							 org_no
							,mo_jumin
							,insert_id
							,insert_dt) values (
							 \''.$code.'\'
							,\''.$jumin.'\'
							,\''.$_SESSION['userCode'].'\'
							,now())';

					$conn->execute($sql);
				}


				//재수당 포함여부
				$lsExtraPayYN	= ($_POST['chkExtraPayYN'] == 'Y' ? 'Y' : 'N');
				$lsSalaryCalYN	= ($_POST['chkSalaryYN'] == 'Y' ? 'Y' : 'N');
				$lsFamilyYN		= ($_POST['ynFamilyCare'] == 'Y' ? 'Y' : 'N');
				$lsExtraTimeYn	= ($_POST['chkExtraTimeYN'] == 'Y' ? 'Y' : 'N');
				$lsInsuDiffYN	= ($_POST['chkInsuDiffYN'] == 'Y' ? 'Y' : 'N');
				$lsDealInYn		= ($_POST['chkDealInYN'] == 'Y' ? 'Y' : 'N');
				$lsDealLimitYn	= ($_POST['chkDealLimitYN'] == 'Y' ? 'Y' : 'N');
				$lsCounselYn	= ($_POST['chkCounselYn'] == 'Y' ? 'Y' : 'N');

				$lsSupportYn	= ($_POST['chkSupportYn'] == 'Y' ? 'Y' : 'N');
				$lsResponseYn	= ($_POST['chkResponseYn'] == 'Y' ? 'Y' : 'N');
				$lsWithoutpayYn	= ($_POST['chkWithoutpayYn'] == 'Y' ? 'Y' : 'N');

				//주야간보호사 여부
				$dayNightYn	= $_POST['optDayNight'] == 'Y' ? 'Y' : 'N';

				//산모신생아 지정관리사여부
				$babyMgYn = $_POST['optBabyMgYn'] == 'Y' ? 'Y' : 'N';
				$babyMgArea = $_POST['cboBabyMgArea'];

				//주야간보호 프로그램관리사 여부
				$danPrgYn = $_POST['optDanPrgYn'] == 'Y' ? 'Y' : 'N';

				//치매수당 지급여부
				$prgCostNotYn = ($_POST['chkPrgCostNotYn'] == 'Y' ? 'Y' : 'N');

				//치매인지수료여부
				$lsDementiaYN	= ($_POST['ynDementia'] == 'Y' ? 'Y' : 'N');

				//두루누리보험(국민,건강) 신규가입 여부
				$lsInsuNewYn	= ($_POST['optInsuNewYn'] == 'Y' ? 'Y' : 'N');

				//퇴직적립금 계산여부
				$retirementNotYn = ($_POST['chkRetirementNotYn'] == 'Y' ? 'Y' : 'N');
				
				//일자리안정자금 적용여부
				$jobfundsNotYn = ($_POST['chkJobfundsNotYn'] == 'Y' ? 'Y' : 'N');
				
				$weeklyPayYn    = ($_POST['chkWeeklyPayYn'] == 'Y' ? 'Y' : 'N'); //주휴수당지급여부(돌보인가족케어만)
				$annaulPayYn    = ($_POST['chkAnnaulPayYn'] == 'Y' ? 'Y' : 'N'); //연차수당지급여부(돌보인가족케어만)

				$sql = 'update	mem_option
						set		mo_extrapay_yn	= \''.$lsExtraPayYN.'\'
						,		mo_salary_yn	= \''.$lsSalaryCalYN.'\'
						,		family_yn		= \''.$lsFamilyYN.'\'
						,		extratime_yn	= \''.$lsExtraTimeYn.'\'
						,		insu_yn			= \''.$lsInsuDiffYN.'\'
						,		dealin_yn		= \''.$lsDealInYn.'\'
						,		deal_limit_yn	= \''.$lsDealLimitYn.'\'
						,		counsel_yn		= \''.$lsCounselYn.'\'
						,		support_yn		= \''.$lsSupportYn.'\'
						,		response_yn		= \''.$lsResponseYn.'\'
						,		withoutpay_yn	= \''.$lsWithoutpayYn.'\'
						,		day_night_yn	= \''.$dayNightYn.'\'
						,		baby_mg_yn		= \''.$babyMgYn.'\'
						,		baby_mg_area	= \''.$babyMgArea.'\'
						,		dan_prg_yn		= \''.$danPrgYn.'\'
						,		prg_cost_not_yn	= \''.$prgCostNotYn.'\'
						,		insu_new_yn     = \''.$lsInsuNewYn.'\'
						,		dementia_yn	    = \''.$lsDementiaYN.'\'  /*치매인지수료여부*/
						,		retirement_not_yn = \''.$retirementNotYn.'\'
						,		jobfunds_not_yn = \''.$jobfundsNotYn.'\'
						,		weekly_pay_yn   = \''.$weeklyPayYn.'\'
						,		annual_pay_yn   = \''.$annaulPayYn.'\'
						where	org_no			= \''.$code.'\'
						and		mo_jumin		= \''.$jumin.'\'';

				$conn->execute($sql);
			}
		}
	/********************************************************/


	//재무회계에 사용할 사용자 키를 생성.
	$sql = 'SELECT	COUNT(*)
			FROM	fa_org
			WHERE	org_no	= \''.$code.'\'
			AND		user_key= \''.$key.'\'
			';
	$cnt = $conn->get_data($sql);

	if ($cnt == 0){
		$sql = 'INSERT INTO fa_org (org_no, user_key) VALUES (
				 \''.$code.'\'
				,\''.$key.'\'
				)';
		$conn->execute($sql);
	}



	$conn->commit();

	include_once("../inc/_db_close.php");
?>
<script>
	alert('<?=$myF->message("ok","N");?>');

	if ('<?=$conn->mode;?>' == '1')
		location.replace('mem_reg.php?code=<?=$code;?>&kind=<?=$kind;?>&jumin=<?=$ed->en($jumin);?>&page=<?=$page;?>&menu_select=<?=$menu_select;?>&counsel_menu=<?=$counselMode;?>');
</script>