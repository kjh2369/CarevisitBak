<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_nhcs_db.php');
	
	$userCode = $_SESSION['userCode'];
	$orgNo = $_POST['txtOrgNo'];
	$orgNm = $_POST['txtOrgNm'];

	$homepage = $_POST['txtHomepage'];
	$email = $_POST['txtEmail'];

	$manager = $_POST['txtManager'];
	$mobile = str_replace('-','',$_POST['txtMobile']);
	$phone = str_replace('-','',$_POST['txtPhone']);
	$fax = str_replace('-','',$_POST['txtFAX']);
	$ceoMobile = str_replace('-','',$_POST['txtCeoMobile']);
	$ceoPhone = str_replace('-','',$_POST['txtCeoPhone']);
	$smsMobile = str_replace('-','',$_POST['txtSmsMobile']);
	$smsPhone = str_replace('-','',$_POST['txtSmsPhone']);

	$bizNo = str_replace('-','',$_POST['txtBizNo']);
	
	$postno = $_POST['txtPostno'];
	$addr = $_POST['txtAddr'];
	$addrDtl = $_POST['txtAddrDtl'];
	

	//기존데이타 존재여부
	$sql = 'SELECT	COUNT(*)
			FROM	medical_org
			WHERE	medical_org_no = \''.$orgNo.'\'';

	$cnt = $conn->get_data($sql);

	$conn->begin();

	//신규기관등록
	if (!$cnt){
		$sql = 'INSERT INTO medical_org (
				 medical_org_no,
				 create_id,
				 create_dt) VALUES (
				 \''.$orgNo.'\',
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
	$sql = 'UPDATE	medical_org
			set		medical_org_name= \''.$orgNm.'\'
			,		ceo_name		= \''.$manager.'\'
			,		taxid			= \''.$bizNo.'\'
			,		telno_loc_org	= \''.$phone.'\'
			,		faxno_org		= \''.$fax.'\'
			,		telno_loc_ceo	= \''.$ceoPhone.'\'
			,		telno_mob_ceo	= \''.$ceoMobile.'\'
			,		telno_mob_sms	= \''.$smsMobile.'\'
			,		telno_loc_sms	= \''.$smsPhone.'\'
			,		addr1			= \''.$addr.'\'
			,		addr2			= \''.$addrDtl.'\'
			,		homepage		= \''.$homepage.'\'
			,		e_mail			= \''.$email.'\'
			,		del_flag		= \'N\'
			WHERE	medical_org_no	= \''.$orgNo.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo $conn->error_msg.chr(13).$conn->error_query;
		 exit;
	}

	
	$conn->commit();
	

	include_once('../inc/_db_close.php');
?>