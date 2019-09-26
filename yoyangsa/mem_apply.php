<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$mode  = $_POST['mode'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	if ($mode == 1){
		$sql = 'select m02_yname as nm
				,      m02_ypostno as postno
				,      m02_yjuso1 as addr
				,      m02_yjuso2 as addr_dtl
				,      m02_mobile_kind as m_kind
				,      m02_ytel as mobile
				,      m02_ytel2 as phone
				,      m02_email as email
				,      m02_rfid_yn as rfid
				,      m02_ygyeoja_no AS bank_no
				,      m02_ybank_name AS bank_cd
				,      m02_ybank_holder AS bank_acct
				  from m02yoyangsa
				 where m02_ccode  = \''.$code.'\'
				   and m02_yjumin = \''.$jumin.'\'';

		$data = $conn->get_array($sql);
	}

	if ($data){
		echo 'memNm='.$data['nm']
			.'&postNo='.$data['postno']
			.'&addr='.$data['addr']
			.'&addrDtl='.$data['addr_dtl']
			.'&mobileKind='.$data['m_kind']
			.'&mobile='.$data['mobile']
			.'&phone='.$data['phone']
			.'&email='.$data['email']
			.'&rfid='.$data['rfid']
			.'&bankNo='.$data['bank_no']
			.'&bankCd='.$data['bank_cd']
			.'&bankAcct='.$data['bank_acct'];

		unset($data);
	}

	include_once('../inc/_db_close.php');
?>