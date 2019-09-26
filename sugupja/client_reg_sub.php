<?
	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	switch($__CURRENT_SVC_ID__){
		case 11: //재가요양
			if ($lbTestMode){
				include_once('./client_svc_care.php');
			}else{
				include_once('./client_reg_sub_care.php');
			}
			break;
		case 21: //가사간병
			if ($lbTestMode){
				include_once('./client_svc_nurse.php');
			}else{
				include_once('./client_reg_sub_nurse.php');
			}
			break;
		case 22: //노인돌봄
			if ($lbTestMode){
				include_once('./client_svc_old.php');
			}else{
				include_once('./client_reg_sub_oldman.php');
			}
			break;
		case 23: //산모신생아
			if ($lbTestMode){
				include_once('./client_svc_baby.php');
			}else{
				include_once('./client_reg_sub_baby.php');
			}
			break;
		case 24: //장애인보조
			if ($lbTestMode){
				include_once('./client_svc_dis.php');
			}else{
				include_once('./client_reg_sub_dis.php');
			}
			break;
		case 26: //재가관리
			include_once('./client_svc_support.php');
			break;

		default: //기타유료
			if ($lbTestMode){
				include('./client_svc_other.php');
			}else{
				include('./client_reg_sub_other.php');
			}
	}

	// 변경내역
	if ($__CURRENT_SVC_ID__ != 26){
		include('./client_reg_history.php');
	}
?>