<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_POST['txtOrgNo'];
	$orgNm = $_POST['txtOrgNm'];

	$logId = $_POST['txtLogId'];
	$lowPw = $_POST['txtLogPw'];

	if (!$orgNo || !$logId) exit;

	$homepage = $_POST['txtHomepage'];
	$email = $_POST['txtEmail'];

	$manager = $_POST['txtManager'];
	$mobile = str_replace('-','',$_POST['txtMobile']);
	$phone = str_replace('-','',$_POST['txtPhone']);
	$fax = str_replace('-','',$_POST['txtFAX']);

	$bizNo = str_replace('-','',$_POST['txtBizNo']);
	$regNo = str_replace('-','',$_POST['txtRegNo']);

	$postno = $_POST['txtPostno'];
	$addr = $_POST['txtAddr'];
	$addrDtl = $_POST['txtAddrDtl'];
	$orgMemo = AddSlashes($_POST['txtOrgMemo']);

	$connDt = str_replace('-','',$_POST['txtConnDt']);
	$contDt = str_replace('-','',$_POST['txtContDt']);

	$fromDt = $_POST['txtFromDt'];
	$toDt = $_POST['txtToDt'];

	$company = $_POST['cboCompany'];
	$branch = $_POST['cboBranch'];
	$person = $_POST['cboPerson'];
	$companyCd = $_POST['companyCd'];

	$svcHomecare = ($_POST['chkSvcHomecare'] == 'Y' ? 'Y' : '');
	$svcGuard = ($_POST['chkSvcGuard'] == 'Y' ? 'Y' : '');

	$svcNurse = ($_POST['chkSvcNurse'] == 'Y' ? 'Y' : '');
	$svcOld = ($_POST['chkSvcOld'] == 'Y' ? 'Y' : '');
	$svcBaby = ($_POST['chkSvcBaby'] == 'Y' ? 'Y' : '');
	$svcDis = ($_POST['chkSvcDis'] == 'Y' ? 'Y' : '');

	$svcResource = ($_POST['chkSvcResource'] == 'Y' ? 'Y' : '');
	$svcSupport = ($_POST['chkSvcSupport'] == 'Y' ? 'Y' : '');
	$area = $_POST['cboArea'];
	$group = $_POST['cboGroup'];

	$svcFacilities = $_POST['chkSvcFacilities'];

	$svcNursePerson = $_POST['chkSvcNursePerson'];
	$svcNursGroup = $_POST['chkSvcNursGroup'];

	$smartAdmin = ($_POST['optSmartAdmin'] == 'Y' ? 'Y' : '');
	$yoyangsa = ($_POST['optYoyangsa'] == 'Y' ? 'Y' : '');
	$socialWorker = ($_POST['optSocialWorker'] == 'Y' ? 'Y' : '');
	$SMS = ($_POST['optSMS'] == 'Y' ? 'Y' : '');

	$atGbn = $_POST['cboAtGbn'];

	$connMemo = AddSlashes($_POST['txtConnMemo']);

	//기존데이타 존재여부
	$sql = 'SELECT	COUNT(*)
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'';

	$cnt = $conn->get_data($sql);

	$conn->begin();

	//신규기관등록
	if (!$cnt){
		$sql = 'INSERT INTO m00center (
				 m00_mcode
				,m00_mkind) VALUES (
				 \''.$orgNo.'\'
				,\'0\'
				)';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo $conn->error_msg.chr(13).$conn->error_query;
			 exit;
		}
	}

	//기관정보 수정
	$sql = 'UPDATE	m00center
			set		m00_store_nm	= \''.$orgNm.'\'
			,		m00_mname		= \''.$manager.'\'
			,		m00_ccode		= \''.$bizNo.'\'
			,		m00_com_no		= \''.$regNo.'\'
			,		m00_ctel		= \''.$phone.'\'
			,		m00_fax_no		= \''.$fax.'\'
			,		m00_cpostno		= \''.$postno.'\'
			,		m00_caddr1		= \''.$addr.'\'
			,		m00_caddr2		= \''.$addrDtl.'\'
			,		m00_homepage	= \''.$homepage.'\'
			,		m00_email		= \''.$email.'\'
			,		m00_domain		= \''.$company.'\'
			,		m00_start_date	= \''.$contDt.'\'
			,		m00_del_yn		= \'N\'
			,		m00_area_cd		= \''.$_POST['area_cd'].'\'
			,		m00_group_cd	= \''.$_POST['group_cd'].'\'
			WHERE	m00_mcode		= \''.$orgNo.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo $conn->error_msg.chr(13).$conn->error_query;
		 exit;
	}

	//로그인 아이디
	$sql = 'SELECT	COUNT(*)
			FROM	han_member
			WHERE	id = \''.$orgNo.'\'
			';
	$cnt = $conn->get_data($sql);

	if ($cnt < 1){
		$sql = 'INSERT INTO han_member (id, pswd, name, sr, pwd, org_no, area_cd, group_cd) VALUES (
				 \''.$logId.'\'
				,\''.$lowPw.'\'
				,\''.$orgNm.'\'
				,\'S\'
				,\'9\'
				,\''.$orgNo.'\'
				,\''.$_POST['area_cd'].'\'
				,\''.$_POST['group_cd'].'\'
				)';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo $conn->error_msg.chr(13).$conn->error_query;
			 exit;
		}
	}

	include_once('../inc/_db_close.php');
?>