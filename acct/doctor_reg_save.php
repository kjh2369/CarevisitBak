<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_nhcs_db.php');
	
	$userCode = $_SESSION['userCode'];


	$doctorNm = $_POST['txtDoctorNm'];					//의사명
	$licenceNo = $_POST['txtLicenceNo'];				//의사면허번호
	$mobile = str_replace('-','',$_POST['txtMobile']);	//의사 무선번호
	$phone = str_replace('-','',$_POST['txtPhone']);	//의사 유선번호
	$spc = $_POST['cboSpc'];							//전문과목
	$postno = $_POST['txtPostno'];						//우편번호
	$addr = $_POST['txtAddr'];							//주소
	$addrDtl = $_POST['txtAddrDtl'];					//상세주소
	

	//기존데이타 존재여부
	$sql = 'SELECT	COUNT(*)
			FROM	doctor
			WHERE	doctor_licence_no = \''.$licenceNo.'\'';

	$cnt = $conn->get_data($sql);

	$conn->begin();

	//신규기관등록
	if (!$cnt){
		$sql = 'INSERT INTO doctor (
				 doctor_licence_no,
				 create_id,
				 create_dt) VALUES (
				 \''.$licenceNo.'\',
				\''.$userCode.'\',
				now()
				)';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo $conn->error_msg.chr(13).$conn->error_query;
			 exit;
		}
	}

	//기관정보 수정
	$sql = 'UPDATE	doctor
			set		doctor_name			= \''.$doctorNm.'\'
			,		spc_subject			= \''.$spc.'\'
			,		telno_loc			= \''.$phone.'\'
			,		telno_mob			= \''.$mobile.'\'
			,		zipcd				= \''.$postno.'\'
			,		addr1				= \''.$addr.'\'
			,		addr2				= \''.$addrDtl.'\'
			,		del_flag			= \'N\'
			WHERE	doctor_licence_no	= \''.$licenceNo.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo $conn->error_msg.chr(13).$conn->error_query;
		 exit;
	}

	
	$conn->commit();
	

	include_once('../inc/_db_close.php');
?>